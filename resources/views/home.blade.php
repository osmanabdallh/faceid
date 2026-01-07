@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 attendance-card">
                <div class="card-body p-4">
                    <h1 class="text-center mb-4 text-primary">
                        <i class="fas fa-user-check me-2"></i>نظام الحضور بالتعرف على الوجه
                    </h1>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <div class="text-center mb-5">
                        <h3 class="fw-bold">مرحباً {{ Auth::user()->name }} 👋</h3>
                        <p class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>نظام التعرف على الوجه يعمل في متصفحك مباشرة
                        </p>
                        
                        <!-- وقت الخرطوم الحالي -->
                        <div class="alert alert-info d-inline-block">
                            <i class="fas fa-clock me-2"></i>
                            <strong>توقيت الخرطوم:</strong> 
                            <span id="current-khartoum-time" class="fw-bold">
                                {{ \Carbon\Carbon::now('Africa/Khartoum')->format('H:i:s') }}
                            </span>
                        </div>
                        
                        <!-- وقت جهاز المستخدم -->
                        <div class="alert alert-warning d-inline-block mt-2">
                            <i class="fas fa-desktop me-2"></i>
                            <strong>توقيت جهازك:</strong> 
                            <span id="client-time" class="fw-bold"></span>
                        </div>
                    </div>
                    
                    <!-- بطاقات الحالة -->
                    <div class="row text-center mb-5">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-fingerprint fa-3x text-primary mb-3"></i>
                                        <h4 class="card-title">حالة البصمة</h4>
                                    </div>
                                    
                                    <div class="mb-4">
                                        @if($hasFace)
                                            <span class="badge bg-success fs-6 p-3">
                                                <i class="fas fa-check-circle me-2"></i>البصمة مسجلة
                                            </span>
                                        @else
                                            <span class="badge bg-danger fs-6 p-3">
                                                <i class="fas fa-exclamation-triangle me-2"></i>البصمة غير مسجلة
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <a href="{{ route('face.browser') }}" class="btn btn-lg btn-primary btn-face w-100">
                                        @if($hasFace)
                                            <i class="fas fa-redo me-2"></i>تحديث البصمة
                                        @else
                                            <i class="fas fa-fingerprint me-2"></i>تسجيل البصمة
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-calendar-check fa-3x text-success mb-3"></i>
                                        <h4 class="card-title">حضور اليوم</h4>
                                    </div>
                                    
                                    @if($todayAttendance)
                                        <div class="mb-4">
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle me-2"></i>تم تسجيل حضور اليوم
                                            </div>
                                            <p class="fw-bold">
                                                <i class="fas fa-clock me-2"></i>
                                                الوقت: {{ \Carbon\Carbon::parse($todayAttendance->check_in)->timezone('Africa/Khartoum')->format('H:i:s') }}
                                            </p>
                                            <p class="text-muted">
                                                التاريخ: {{ \Carbon\Carbon::parse($todayAttendance->date)->format('Y-m-d') }}
                                            </p>
                                        </div>
                                    @else
                                        <div class="mb-4">
                                            <p class="text-muted">لم يتم تسجيل حضور اليوم</p>
                                        </div>
                                        
                                        <!-- زر يوجه إلى صفحة التحقق من الوجه -->
                                        <a href="{{ route('face.browser') }}?action=attendance" 
                                           class="btn btn-lg btn-success btn-face w-100" 
                                           id="mark-attendance-btn">
                                            <i class="fas fa-sign-in-alt me-2"></i>تسجيل الحضور
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- الإحصائيات -->
                    <div class="row text-center mb-5">
                        <h3 class="text-center mb-4">
                            <i class="fas fa-chart-bar me-2 text-info"></i>إحصائيات الشهر
                        </h3>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <i class="fas fa-calendar-day fa-2x text-primary mb-3"></i>
                                    <h2 class="display-5 fw-bold">{{ $monthAttendances->count() }}</h2>
                                    <p class="text-muted">أيام الحضور</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <i class="fas fa-user-check fa-2x text-success mb-3"></i>
                                    <h2 class="display-5 fw-bold">{{ $monthAttendances->where('status', 'present')->count() }}</h2>
                                    <p class="text-muted">حضور ناجح</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <i class="fas fa-bullseye fa-2x text-warning mb-3"></i>
                                    <h2 class="display-5 fw-bold">🎯</h2>
                                    <p class="text-muted">دقة عالية</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- أزرار التنقل السريع -->
                    <div class="text-center mb-4">
                        <h3 class="text-center mb-4">
                            <i class="fas fa-rocket me-2 text-primary"></i>روابط سريعة
                        </h3>
                        
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <a href="{{ route('attendance.history') }}" class="btn btn-info btn-face">
                                <i class="fas fa-history me-2"></i>سجل الحضور الكامل
                            </a>
                            <a href="{{ route('face.browser') }}" class="btn btn-warning btn-face">
                                <i class="fas fa-camera me-2"></i>فتح الكاميرا للتحقق
                            </a>
                            @if($hasFace)
                                <button onclick="verifyFace()" class="btn btn-success btn-face">
                                    <i class="fas fa-user-check me-2"></i>التحقق من الوجه
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- معلومات النظام -->
                    <div class="alert alert-info mt-4">
                        <h5 class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>معلومات النظام:
                        </h5>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <ul class="mb-0">
                                    <li><i class="fas fa-check-circle text-success me-2"></i>النظام يعمل في المتصفح فقط</li>
                                    <li><i class="fas fa-check-circle text-success me-2"></i>يستخدم توقيت جهاز المستخدم</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="mb-0">
                                    <li><i class="fas fa-check-circle text-success me-2"></i>حضور مرة واحدة يومياً</li>
                                    <li><i class="fas fa-check-circle text-success me-2"></i>سرية البيانات مضمونة</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .attendance-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 20px;
    }
    
    .card {
        border-radius: 15px;
        transition: transform 0.3s;
    }
    
    .card:hover {
        transform: translateY(-5px);
    }
    
    #client-time {
        direction: ltr;
        display: inline-block;
    }
</style>
@endsection

@section('scripts')
<script>
    // تحديث وقت الخرطوم في الصفحة
    function updateCurrentTime() {
        const now = new Date();
        const khartoumTime = new Date(now.getTime() + (2 * 60 * 60 * 1000));
        
        const hours = khartoumTime.getUTCHours().toString().padStart(2, '0');
        const minutes = khartoumTime.getUTCMinutes().toString().padStart(2, '0');
        const seconds = khartoumTime.getUTCSeconds().toString().padStart(2, '0');
        
        document.getElementById('current-khartoum-time').textContent = `${hours}:${minutes}:${seconds}`;
    }
    
    // عرض وقت جهاز المستخدم
    function updateClientTime() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        
        document.getElementById('client-time').textContent = `${hours}:${minutes}:${seconds}`;
    }
    
    // تحديث الوقت كل ثانية
    setInterval(updateCurrentTime, 1000);
    setInterval(updateClientTime, 1000);
    updateCurrentTime();
    updateClientTime();
    
    // دالة التحقق من الوجه
    function verifyFace() {
        const hasFace = {{ $hasFace ? 'true' : 'false' }};
        
        if (!hasFace) {
            alert('❌ يرجى تسجيل بصمة الوجه أولاً');
            window.location.href = '{{ route("face.browser") }}';
            return;
        }
        
        window.location.href = '{{ route("face.browser") }}?action=verify';
    }
    
    // تفعيل زر تسجيل الحضور
    document.getElementById('mark-attendance-btn')?.addEventListener('click', function(e) {
        const hasFace = {{ $hasFace ? 'true' : 'false' }};
        
        if (!hasFace) {
            e.preventDefault();
            alert('❌ يرجى تسجيل بصمة الوجه أولاً');
        }
    });
</script>
@endsection