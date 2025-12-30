<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaceController;
use App\Http\Controllers\AttendanceController;

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    // الصفحة الرئيسية
    Route::get('/home', [AttendanceController::class, 'dashboard'])->name('home');
    
    // تسجيل الوجه
    Route::get('/face-register', [FaceController::class, 'showRegisterForm'])->name('face.register');
    Route::post('/face-register', [FaceController::class, 'store'])->name('face.store');
    Route::post('/face-verify', [FaceController::class, 'verify'])->name('face.verify');
    
    // الكاميرا
    Route::get('/face-camera', [FaceController::class, 'showCamera'])->name('face.camera');
    Route::post('/face-upload', [FaceController::class, 'upload'])->name('face.upload');
    
    // الحضور
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/history', [AttendanceController::class, 'index'])->name('attendance.history');
    
    // صفحات الاختبار
    Route::get('/test-attendance', function () {
        return view('attendance-test');
    });
    
    Route::get('/test-camera', function () {
        return view('face-camera');
    });
});