<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class FaceController extends Controller
{
    /**
     * عرض صفحة تسجيل الوجه
     */
    public function showRegisterForm()
    {
        return view('face-register');
    }

    /**
     * عرض صفحة الكاميرا
     */
    public function showCamera()
    {
        return view('face-camera');
    }

    /**
     * تسجيل بصمة الوجه
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|string'
        ]);

        $user = Auth::user();
        $imageData = $request->image;

        try {
            // 1. حفظ الصورة
            $imageName = $this->saveImage($user->id, $imageData);
            
            // 2. إرسال إلى سيرفر البايثون
            $response = Http::timeout(10)->post('http://127.0.0.1:8000/encode', [
                'user_id' => $user->id,
                'image' => $imageData
            ]);

            if (!$response->successful()) {
                return back()->with('error', '❌ فشل في الاتصال بسيرفر التعرف على الوجه');
            }

            $result = $response->json();

            if (!isset($result['encoding'])) {
                return back()->with('error', '❌ فشل في توليد البصمة');
            }

            // 3. حفظ البصمة في قاعدة البيانات
            $user->face_encoding = json_encode($result['encoding']);
            $user->save();

            // 4. تسجيل في السجلات
            \Log::info("تم تسجيل بصمة وجه للمستخدم #{$user->id}");

            return redirect('/home')->with('success', '✅ تم تسجيل بصمة الوجه بنجاح! (نظام تجريبي)');

        } catch (\Exception $e) {
            \Log::error('Face registration error: ' . $e->getMessage());
            return back()->with('error', '❌ حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * التحقق من الوجه
     */
    public function verify(Request $request)
    {
        $request->validate([
            'image' => 'required|string'
        ]);

        $user = Auth::user();

        try {
            $response = Http::timeout(10)->post('http://127.0.0.1:8000/verify', [
                'user_id' => $user->id,
                'image' => $request->image,
                'stored_encoding' => $user->face_encoding ? json_decode($user->face_encoding, true) : []
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل في التحقق'
                ], 500);
            }

            $result = $response->json();

            return response()->json([
                'success' => true,
                'match' => $result['match'] ?? false,
                'similarity' => $result['similarity'] ?? 0,
                'message' => $result['message'] ?? 'تم التحقق'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في الاتصال'
            ], 500);
        }
    }

    /**
     * حفظ الصورة
     */
    private function saveImage($userId, $imageData)
    {
        // تحويل base64
        $image = str_replace('data:image/png;base64,', '', $imageData);
        $image = str_replace(' ', '+', $image);
        
        $imageName = 'faces/user_' . $userId . '_' . time() . '.png';
        
        Storage::disk('public')->put($imageName, base64_decode($image));
        
        return $imageName;
    }

    /**
     * رفع الصورة من الكاميرا
     */
    public function upload(Request $request)
    {
        $user = Auth::user();

        if (!$request->image) {
            return back()->with('error', '❌ لم يتم استلام الصورة');
        }

        // حفظ الصورة
        $this->saveImage($user->id, $request->image);

        // تحديث حالة المستخدم
        $user->update([
            'face_encoding' => 'IMAGE_SAVED_' . time()
        ]);

        return redirect('/face-register')
            ->with('success', '✅ تم حفظ صورة الوجه بنجاح');
    }
}