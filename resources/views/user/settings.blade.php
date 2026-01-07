@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-user-cog me-2"></i>إعدادات الحساب
                    </h3>
                </div>
                
                <div class="card-body p-4">
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
                    
                    <!-- معلومات الحساب -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h5 class="d-flex align-items-center">
                                    <i class="fas fa-info-circle me-2"></i>معلومات الحساب
                                </h5>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <p><strong>الاسم:</strong> {{ $user->name }}</p>
                                        <p><strong>البريد الإلكتروني:</strong> {{ $user->email }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>تاريخ التسجيل:</strong> {{ $user->created_at->format('Y-m-d') }}</p>
                                        <p><strong>حالة البصمة:</strong> 
                                            @if(!empty($user->face_encoding))
                                                <span class="badge bg-success">✅ مسجلة</span>
                                            @else
                                                <span class="badge bg-danger">❌ غير مسجلة</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- تعديل البيانات الشخصية -->
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>تعديل البيانات الشخصية</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('user.updateProfile') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label">الاسم الكامل</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>حفظ التغييرات
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- تغيير كلمة المرور -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-key me-2"></i>تغيير كلمة المرور</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('user.changePassword') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">كلمة المرور الحالية</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" name="current_password" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">كلمة المرور الجديدة</label>
                                    <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                           id="new_password" name="new_password" required>
                                    <small class="form-text text-muted">يجب أن تكون كلمة المرور 8 أحرف على الأقل</small>
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">تأكيد كلمة المرور الجديدة</label>
                                    <input type="password" class="form-control" 
                                           id="new_password_confirmation" name="new_password_confirmation" required>
                                </div>
                                
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-key me-2"></i>تغيير كلمة المرور
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- إدارة بصمة الوجه -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-fingerprint me-2"></i>إدارة بصمة الوجه</h5>
                        </div>
                        <div class="card-body">
                            @if(!empty($user->face_encoding))
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>البصمة مسجلة:</strong> يمكنك تحديث أو حذف بصمة وجهك
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <a href="{{ route('face.browser') }}" class="btn btn-primary">
                                        <i class="fas fa-redo me-2"></i>تحديث البصمة
                                    </a>
                                    
                                    <form method="POST" action="{{ route('user.deleteFace') }}" 
                                          class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف بصمة الوجه؟')">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash me-2"></i>حذف البصمة
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>البصمة غير مسجلة:</strong> يرجى تسجيل بصمة وجهك لاستخدام نظام الحضور
                                </div>
                                
                                <a href="{{ route('face.browser') }}" class="btn btn-success">
                                    <i class="fas fa-fingerprint me-2"></i>تسجيل بصمة جديدة
                                </a>
                            @endif
                        </div>
                    </div>
                    
                    <!-- إحصائيات الحساب -->
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>إحصائيات الحساب</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-6 mb-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <i class="fas fa-calendar-check fa-2x text-primary mb-2"></i>
                                            <h4 class="fw-bold" id="attendanceCount">0</h4>
                                            <p class="text-muted mb-0">أيام الحضور</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <i class="fas fa-calendar-alt fa-2x text-success mb-2"></i>
                                            <h4 class="fw-bold" id="currentMonthCount">0</h4>
                                            <p class="text-muted mb-0">حضور الشهر الحالي</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- أزرار التنقل -->
                    <div class="text-center mt-4">
                        <a href="{{ route('home') }}" class="btn btn-outline-primary">
                            <i class="fas fa-home me-2"></i>العودة للرئيسية
                        </a>
                        
                        <a href="{{ route('attendance.history') }}" class="btn btn-outline-info">
                            <i class="fas fa-history me-2"></i>سجل الحضور
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // جلب إحصائيات الحضور
    document.addEventListener('DOMContentLoaded', function() {
        fetch('{{ route("attendance.stats") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('attendanceCount').textContent = data.total;
                    document.getElementById('currentMonthCount').textContent = data.current_month;
                }
            })
            .catch(error => console.error('Error:', error));
    });
</script>
@endsection