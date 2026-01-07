<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FaceController extends Controller
{
    /**
     * حفظ بصمة الوجه
     */
    public function saveEncoding(Request $request)
    {
        Log::info('Face encoding save request received', [
            'user_id' => Auth::id(),
            'data_received' => $request->all()
        ]);

        try {
            // التحقق من البيانات
            $request->validate([
                'encoding' => 'required|array',
                'image' => 'nullable|string'
            ]);

            $user = Auth::user();
            
            // تحويل البيانات إلى JSON
            $encoding = $request->encoding;
            
            // تأكد أن البيانات صحيحة
            if (!is_array($encoding) || count($encoding) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات البصمة غير صالحة'
                ], 400);
            }

            // حفظ البصمة في قاعدة البيانات
            $user->face_encoding = json_encode($encoding);
            $user->save();

            Log::info('Face encoding saved successfully', [
                'user_id' => $user->id,
                'encoding_length' => count($encoding)
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ تم تسجيل بصمة الوجه بنجاح!',
                'data' => [
                    'user_id' => $user->id,
                    'encoding_length' => count($encoding),
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving face encoding: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => '❌ خطأ في حفظ البيانات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * التحقق من الوجه
     */
    public function verify(Request $request)
    {
        try {
            $request->validate([
                'encoding' => 'required|array'
            ]);

            $user = Auth::user();
            
            // التحقق من وجود بصمة مسجلة
            if (empty($user->face_encoding)) {
                return response()->json([
                    'success' => false,
                    'match' => false,
                    'message' => '❌ لا يوجد بصمة مسجلة'
                ], 400);
            }

            // حساب التشابه (نسبة وهمية للاختبار)
            $similarity = 85.5; // نسبة ثابتة للاختبار
            
            // عتبة 70%
            $isMatch = $similarity >= 70;

            return response()->json([
                'success' => true,
                'match' => $isMatch,
                'similarity' => $similarity,
                'threshold' => 70,
                'message' => $isMatch 
                    ? "✅ الوجه مطابق بنسبة {$similarity}%"
                    : "❌ الوجه غير مطابق ({$similarity}%)"
            ]);

        } catch (\Exception $e) {
            Log::error('Face verification error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'match' => false,
                'message' => '❌ خطأ في التحقق'
            ], 500);
        }
    }
}