<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        // التحقق من وجود بصمة وجه
        $hasFace = !empty($user->face_encoding);
        
        // استخدام توقيت الخرطوم
        $today = Carbon::now('Africa/Khartoum')->toDateString();
        
        // جلب حضور اليوم
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
        
        // جلب إحصائيات الشهر
        $monthAttendances = Attendance::where('user_id', $user->id)
            ->whereMonth('date', Carbon::now('Africa/Khartoum')->month)
            ->whereYear('date', Carbon::now('Africa/Khartoum')->year)
            ->get();

        return view('home', compact('hasFace', 'todayAttendance', 'monthAttendances'));
    }

    /**
     * إحصائيات الحضور لـ API
     */
    public function attendanceStats()
    {
        $user = Auth::user();
        
        $total = Attendance::where('user_id', $user->id)->count();
        
        $currentMonth = Attendance::where('user_id', $user->id)
            ->whereMonth('date', Carbon::now('Africa/Khartoum')->month)
            ->whereYear('date', Carbon::now('Africa/Khartoum')->year)
            ->count();
        
        return response()->json([
            'success' => true,
            'total' => $total,
            'current_month' => $currentMonth
        ]);
    }
}