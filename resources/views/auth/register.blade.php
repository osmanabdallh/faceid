@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 register-card">
                <div class="card-header-camera text-center py-4">
                    <h2 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>تسجيل حساب جديد
                    </h2>
                    <p class="mb-0 mt-2 opacity-75">انضم إلى نظام الحضور الذكي</p>
                </div>

                <div class="card-body p-5">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>يوجد أخطاء في المدخلات
                            </h5>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" class="register-form" id="registerForm">
                        @csrf

                        <!-- الاسم الكامل -->
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold">
                                <i class="fas fa-user me-2 text-primary"></i>الاسم الكامل
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-id-card text-primary"></i>
                                </span>
                                <input id="name" type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required 
                                       autocomplete="name" 
                                       autofocus
                                       placeholder="ادخل اسمك الكامل">
                            </div>
                            <small class="text-muted mt-1 d-block">
                                <i class="fas fa-info-circle me-1"></i>سيظهر هذا الاسم في سجلات الحضور
                            </small>
                        </div>

                        <!-- البريد الإلكتروني -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">
                                <i class="fas fa-envelope me-2 text-primary"></i>البريد الإلكتروني
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-at text-primary"></i>
                                </span>
                                <input id="email" type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="email"
                                       placeholder="example@domain.com">
                            </div>
                            <small class="text-muted mt-1 d-block">
                                <i class="fas fa-info-circle me-1"></i>ستستخدم هذا البريد لتسجيل الدخول
                            </small>
                        </div>

                        <!-- كلمة المرور -->
                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">
                                <i class="fas fa-lock me-2 text-primary"></i>كلمة المرور
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-key text-primary"></i>
                                </span>
                                <input id="password" type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="اختر كلمة مرور قوية">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength mt-2">
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" id="passwordStrength" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-shield-alt me-1"></i>قوة كلمة المرور: <span id="strengthText">ضعيفة</span>
                                </small>
                            </div>
                        </div>

                        <!-- تأكيد كلمة المرور -->
                        <div class="mb-4">
                            <label for="password-confirm" class="form-label fw-bold">
                                <i class="fas fa-lock me-2 text-primary"></i>تأكيد كلمة المرور
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-key text-primary"></i>
                                </span>
                                <input id="password-confirm" type="password" 
                                       class="form-control" 
                                       name="password_confirmation" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="أعد إدخال كلمة المرور">
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted mt-1 d-block">
                                <i class="fas fa-check-circle me-1"></i>تأكد من تطابق كلمتي المرور
                            </small>
                        </div>

                        <!-- شروط الاستخدام -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    أوافق على 
                                    <a href="#" class="text-primary fw-bold text-decoration-none" data-bs-toggle="modal" data-bs-target="#termsModal">
                                        شروط الاستخدام وسياسة الخصوصية
                                    </a>
                                    للمنظومة
                                </label>
                            </div>
                        </div>

                        <!-- زر التسجيل -->
                        <div class="d-grid gap-2 mb-4">
                            <button type="submit" class="btn btn-primary btn-lg btn-face">
                                <i class="fas fa-user-plus me-2"></i>إنشاء الحساب
                            </button>
                        </div>

                        <!-- رابط تسجيل الدخول -->
                        <div class="text-center">
                            <p class="text-muted">
                                لديك حساب بالفعل؟
                                <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">
                                    <i class="fas fa-sign-in-alt me-1"></i>سجل الدخول
                                </a>
                            </p>
                        </div>
                    </form>

                    <!-- معلومات النظام -->
                    <div class="alert alert-info mt-4">
                        <h5 class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>تنبيه هام:
                        </h5>
                        <ul class="mb-0 mt-2">
                            <li>بعد إنشاء الحساب، يجب تسجيل بصمة الوجه لاستخدام النظام</li>
                            <li>النظام يستخدم تقنية التعرف على الوجه داخل المتصفح</li>
                            <li>يجب أن يكون جهازك مزوداً بكاميرا ويب</li>
                            <li>جميع البيانات مشفرة وآمنة</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal لشروط الاستخدام -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header card-header-camera">
                <h5 class="modal-title" id="termsModalLabel">
                    <i class="fas fa-file-contract me-2"></i>شروط الاستخدام وسياسة الخصوصية
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. جمع واستخدام البيانات:</h6>
                <ul>
                    <li>نظام التعرف على الوجه لا يخزن أي صور شخصية</li>
                    <li>يتم تخزين بصمة الوجه الرقمية فقط (مصفوفة أرقام)</li>
                    <li>البصمة الرقمية لا يمكن استعادتها لصورة</li>
                </ul>
                
                <h6>2. الخصوصية والأمان:</h6>
                <ul>
                    <li>معالجة الوجه تتم في متصفحك فقط</li>
                    <li>لا يتم إرسال أي صور إلى السيرفر</li>
                    <li>التشفير المستخدم: AES-256</li>
                </ul>
                
                <h6>3. مسؤوليات المستخدم:</h6>
                <ul>
                    <li>يجب تسجيل بصمة الوجه بعد التسجيل مباشرة</li>
                    <li>المسؤولية الكاملة عن الجهاز المستخدم</li>
                    <li>عدم مشاركة بيانات الحساب مع الآخرين</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="fas fa-check me-2"></i>فهمت الشروط
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .register-card {
        border-radius: 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 2px solid #e0e0e0;
    }
    
    .card-header-camera {
        background: linear-gradient(to right, #4f46e5, #7c3aed);
        color: white;
        padding: 25px;
        text-align: center;
        border-radius: 18px 18px 0 0 !important;
    }
    
    .register-form .form-control {
        padding: 12px 15px;
        border-radius: 10px;
        border: 2px solid #e0e0e0;
        transition: all 0.3s;
        font-family: 'Tajawal', sans-serif;
    }
    
    .register-form .form-control:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
    }
    
    .btn-face {
        padding: 15px 30px;
        font-size: 18px;
        border-radius: 50px;
        margin: 10px;
        transition: all 0.3s;
        font-family: 'Tajawal', sans-serif;
        background: linear-gradient(to right, #4f46e5, #7c3aed);
        border: none;
    }
    
    .btn-face:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.4);
    }
    
    .password-strength .progress-bar {
        transition: width 0.5s ease;
    }
    
    .form-check-input:checked {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
    
    .alert-info {
        border-radius: 15px;
        border: 2px solid #4f46e5;
        background: rgba(79, 70, 229, 0.1);
    }
    
    .modal-content {
        border-radius: 15px;
        border: none;
    }
    
    .text-primary {
        color: #4f46e5 !important;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // إظهار/إخفاء كلمة المرور
        const togglePassword = document.getElementById('togglePassword');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password-confirm');
        
        function togglePasswordVisibility(input, button) {
            button.addEventListener('click', function() {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                const icon = this.querySelector('i');
                if (type === 'password') {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            });
        }
        
        if (togglePassword && passwordInput) {
            togglePasswordVisibility(passwordInput, togglePassword);
        }
        
        if (toggleConfirmPassword && confirmPasswordInput) {
            togglePasswordVisibility(confirmPasswordInput, toggleConfirmPassword);
        }
        
        // حساب قوة كلمة المرور
        const passwordStrength = document.getElementById('passwordStrength');
        const strengthText = document.getElementById('strengthText');
        
        if (passwordInput && passwordStrength && strengthText) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                let color = 'bg-danger';
                let text = 'ضعيفة';
                
                // قواعد قوة كلمة المرور
                if (password.length >= 8) strength += 25;
                if (/[A-Z]/.test(password)) strength += 25;
                if (/[0-9]/.test(password)) strength += 25;
                if (/[^A-Za-z0-9]/.test(password)) strength += 25;
                
                // تحديد اللون والنص
                if (strength >= 75) {
                    color = 'bg-success';
                    text = 'قوية جداً';
                } else if (strength >= 50) {
                    color = 'bg-warning';
                    text = 'جيدة';
                } else if (strength >= 25) {
                    color = 'bg-info';
                    text = 'متوسطة';
                }
                
                // تحديث شريط التقدم
                passwordStrength.style.width = strength + '%';
                passwordStrength.className = 'progress-bar ' + color;
                strengthText.textContent = text;
                strengthText.className = '';
                
                // لون النص
                if (color === 'bg-success') strengthText.classList.add('text-success');
                else if (color === 'bg-warning') strengthText.classList.add('text-warning');
                else if (color === 'bg-info') strengthText.classList.add('text-info');
                else strengthText.classList.add('text-danger');
            });
        }
        
        // التحقق من تطابق كلمتي المرور
        function checkPasswordMatch() {
            if (passwordInput && confirmPasswordInput) {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                
                if (confirmPassword && password !== confirmPassword) {
                    confirmPasswordInput.classList.add('is-invalid');
                    confirmPasswordInput.classList.remove('is-valid');
                    return false;
                } else if (confirmPassword) {
                    confirmPasswordInput.classList.remove('is-invalid');
                    confirmPasswordInput.classList.add('is-valid');
                    return true;
                }
            }
            return false;
        }
        
        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', checkPasswordMatch);
        }
        
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                if (confirmPasswordInput.value) {
                    checkPasswordMatch();
                }
            });
        }
        
        // التحقق من صحة النموذج قبل الإرسال
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                const name = document.getElementById('name').value;
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('password-confirm').value;
                const terms = document.getElementById('terms').checked;
                
                let errors = [];
                
                // التحقق من الاسم
                if (name.length < 3) {
                    errors.push('الاسم يجب أن يكون 3 أحرف على الأقل');
                }
                
                // التحقق من البريد الإلكتروني
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    errors.push('صيغة البريد الإلكتروني غير صحيحة');
                }
                
                // التحقق من كلمة المرور
                if (password.length < 8) {
                    errors.push('كلمة المرور يجب أن تكون 8 أحرف على الأقل');
                }
                
                // التحقق من تطابق كلمتي المرور
                if (password !== confirmPassword) {
                    errors.push('كلمتا المرور غير متطابقتين');
                }
                
                // التحقق من الشروط
                if (!terms) {
                    errors.push('يجب الموافقة على شروط الاستخدام');
                }
                
                // إذا كان هناك أخطاء، منع الإرسال
                if (errors.length > 0) {
                    e.preventDefault();
                    
                    // عرض الأخطاء
                    let errorHTML = '<ul class="mb-0">';
                    errors.forEach(error => {
                        errorHTML += `<li>${error}</li>`;
                    });
                    errorHTML += '</ul>';
                    
                    // إظهار رسالة الخطأ
                    if (!document.querySelector('.form-errors')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'alert alert-danger form-errors';
                        errorDiv.innerHTML = `
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>يوجد أخطاء
                            </h5>
                            ${errorHTML}
                        `;
                        registerForm.prepend(errorDiv);
                    }
                    
                    return false;
                }
                
                // إظهار رسالة تحميل
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري إنشاء الحساب...';
                    submitBtn.disabled = true;
                }
            });
        }
    });
</script>
@endsection