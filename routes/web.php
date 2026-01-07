<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaceController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    // الصفحة الرئيسية
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    // صفحات الوجه
    Route::get('/face/browser', function () {
        return view('face.browser');
    })->name('face.browser');
    
    Route::post('/face/save-encoding', [FaceController::class, 'saveEncoding'])->name('face.save.encoding');
    Route::post('/face/verify', [FaceController::class, 'verify'])->name('face.verify');
    
    // الحضور
    Route::post('/attendance/browser', [AttendanceController::class, 'browserAttendance'])->name('attendance.browser');
    Route::post('/attendance/mark', [AttendanceController::class, 'markAttendance'])->name('attendance.mark');
    
    // تاريخ الحضور
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');

    // مسارات المستخدم
    Route::prefix('user')->group(function () {
        Route::get('/settings', [UserController::class, 'settings'])->name('user.settings');
        Route::post('/update-profile', [UserController::class, 'updateProfile'])->name('user.updateProfile');
        Route::post('/change-password', [UserController::class, 'changePassword'])->name('user.changePassword');
        Route::post('/delete-face', [UserController::class, 'deleteFace'])->name('user.deleteFace');
    });
    
    // إحصائيات الحضور
    Route::get('/attendance/stats', [HomeController::class, 'attendanceStats'])->name('attendance.stats');
    
});
