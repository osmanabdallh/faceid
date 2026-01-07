<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * عرض صفحة إعدادات المستخدم
     */
    public function settings()
    {
        $user = Auth::user();
        return view('user.settings', compact('user'));
    }

    /**
     * تحديث بيانات المستخدم
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('user.settings')->with('success', '✅ تم تحديث البيانات الشخصية بنجاح');
    }

    /**
     * تغيير كلمة المرور
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // التحقق من كلمة المرور الحالية
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->with('error', '❌ كلمة المرور الحالية غير صحيحة');
        }

        // تحديث كلمة المرور
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('user.settings')->with('success', '✅ تم تغيير كلمة المرور بنجاح');
    }

    /**
     * حذف البصمة
     */
    public function deleteFace()
    {
        $user = Auth::user();
        
        $user->face_encoding = null;
        $user->save();
        
        // حذف من localStorage
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => '✅ تم حذف بصمة الوجه'
            ]);
        }
        
        return redirect()->route('user.settings')->with('success', '✅ تم حذف بصمة الوجه');
    }

    /**
     * الحصول على إحصائيات المستخدم
     */
    public function getStats()
    {
        $user = Auth::user();
        
        $stats = [
            'name' => $user->name,
            'email' => $user->email,
            'has_face' => !empty($user->face_encoding),
            'registered_at' => $user->created_at->format('Y-m-d'),
            'face_registered_at' => !empty($user->face_encoding) ? $user->updated_at->format('Y-m-d') : null,
        ];

        return response()->json($stats);
    }
}