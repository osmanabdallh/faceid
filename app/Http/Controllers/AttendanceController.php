<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * تسجيل الحضور/الانصراف
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // 1. التحقق من وجود بصمة
        if (!$user->face_encoding) {
            return response()->json([
                'success' => false,
                'message' => '❌ يرجى تسجيل بصمة الوجه أولاً'
            ], 400);
        }

        // 2. إرسال إلى سيرفر البايثون للتحقق
        try {
            $response = Http::timeout(10)->post('http://127.0.0.1:8000/compare', [
                'user_id' => $user->id
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ فشل الاتصال بسيرفر التعرف'
                ], 500);
            }

            $result = $response->json();

            if (!$result['match']) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ الوجه غير مطابق - حاول مرة أخرى'
                ], 403);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ خطأ في التحقق: ' . $e->getMessage()
            ], 500);
        }

        // 3. التحقق من الحضور المفتوح
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->whereNull('check_out')
            ->first();

        if ($attendance) {
            // تسجيل الانصراف
            $attendance->update([
                'check_out' => Carbon::now(),
                'status' => 'completed'
            ]);

            return response()->json([
                'success' => true,
                'type' => 'check_out',
                'message' => '✅ تم تسجيل الانصراف بنجاح',
                'time' => now()->format('H:i:s'),
                'date' => now()->format('Y-m-d')
            ]);
        }

        // 4. تسجيل حضور جديد
        Attendance::create([
            'user_id' => $user->id,
            'check_in' => Carbon::now(),
            'date' => $today,
            'status' => 'present',
            'method' => 'face_recognition'
        ]);

        return response()->json([
            'success' => true,
            'type' => 'check_in',
            'message' => '✅ تم تسجيل الحضور بنجاح',
            'time' => now()->format('H:i:s'),
            'date' => now()->format('Y-m-d')
        ]);
    }

    /**
     * عرض سجل الحضور
     */
    public function index()
    {
        $user = Auth::user();
        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('attendance.history', compact('attendances'));
    }

    /**
     * لوحة التحكم الرئيسية
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        $today = Carbon::today();
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->first();

        $totalDays = Attendance::where('user_id', $user->id)
            ->distinct('date')
            ->count();

        return view('home', [
            'user' => $user,
            'todayAttendance' => $todayAttendance,
            'totalDays' => $totalDays,
            'hasFace' => !empty($user->face_encoding)
        ]);
    }
}