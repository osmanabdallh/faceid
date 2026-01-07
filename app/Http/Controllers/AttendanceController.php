<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    /**
     * تسجيل الحضور فقط (بدون انصراف)
     */
    public function markAttendance(Request $request)
    {
        try {
            $user = Auth::user();

            // التحقق من وجود بصمة وجه
            if (empty($user->face_encoding)) {
                return back()->with('error', '❌ يرجى تسجيل بصمة الوجه أولاً');
            }

            // الحصول على الوقت الحالي من جهاز المستخدم (JavaScript سيرسله)
            $clientTime = $request->input('client_time', now());
            
            // استخدام توقيت المستخدم أو توقيت الخرطوم
            $khartoumTime = Carbon::parse($clientTime)->timezone('Africa/Khartoum');
            $today = $khartoumTime->toDateString();
            
            // التحقق إذا تم تسجيل الحضور اليوم
            $existingAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first();

            if ($existingAttendance) {
                return back()->with('error', '❌ تم تسجيل حضورك اليوم مسبقاً');
            }

            // تسجيل حضور جديد
            Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'check_in' => $khartoumTime->toTimeString(),
                'method' => 'face_recognition',
                'status' => 'present',
                'attendance_date' => $khartoumTime->format('Y-m-d H:i:s'), // تاريخ كامل مع الوقت
                'client_timezone' => $request->input('timezone', 'Africa/Khartoum')
            ]);

            return back()->with('success', '✅ تم تسجيل الحضور بنجاح - ' . $khartoumTime->format('H:i:s'));

        } catch (\Exception $e) {
            return back()->with('error', '❌ خطأ: ' . $e->getMessage());
        }
    }

    /**
     * سجل الحضور
     */
    public function history()
    {
        $user = Auth::user();
        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('attendance.history', compact('attendances'));
    }

    /**
     * تسجيل الحضور من صفحة الكاميرا
     */
    public function browserAttendance(Request $request)
    {
        try {
            $user = Auth::user();
            
            // التحقق من وجود بصمة وجه
            if (empty($user->face_encoding)) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ يرجى تسجيل بصمة الوجه أولاً'
                ], 400);
            }

            // الحصول على وقت المستخدم
            $clientTime = $request->input('client_time', now());
            $khartoumTime = Carbon::parse($clientTime)->timezone('Africa/Khartoum');
            $today = $khartoumTime->toDateString();
            
            // التحقق من وجود حضور اليوم
            $existingAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ تم تسجيل حضورك اليوم مسبقاً'
                ], 400);
            }

            // تسجيل الحضور
            Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'check_in' => $khartoumTime->toTimeString(),
                'method' => 'face_recognition',
                'status' => 'present',
                'attendance_date' => $khartoumTime->format('Y-m-d H:i:s')
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ تم تسجيل الحضور بنجاح',
                'time' => $khartoumTime->format('H:i:s'),
                'date' => $today
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف الحضور (إذا لزم الأمر)
     */
    public function deleteAttendance($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);
            
            // التحقق من أن الحضور يخص المستخدم الحالي
            if ($attendance->user_id !== Auth::id()) {
                return back()->with('error', '❌ غير مصرح لك بحذف هذا الحضور');
            }
            
            $attendance->delete();
            
            return back()->with('success', '✅ تم حذف الحضور بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', '❌ خطأ: ' . $e->getMessage());
        }
    }
}