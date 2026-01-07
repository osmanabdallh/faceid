@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 login-card">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="mb-0">
                        <i class="fas fa-sign-in-alt me-2"></i>تسجيل الدخول للنظام
                    </h2>
                    <p class="mb-0 mt-2 opacity-75">نظام الحضور الذكي بالتعرف على الوجه</p>
                </div>

                <div class="card-body p-5">
                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="login-form">
                        @csrf

                        <!-- البريد الإلكتروني -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">
                                <i class="fas fa-envelope me-2 text-primary"></i>البريد الإلكتروني
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-user text-primary"></i>
                                </span>
                                <input id="email" type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="email" 
                                       autofocus
                                       placeholder="ادخل بريدك الإلكتروني">
                                @error('email')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
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
                                       autocomplete="current-password"
                                       placeholder="ادخل كلمة المرور">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- تذكرني -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    <i class="fas fa-check-circle me-1 text-primary"></i>تذكرني على هذا الجهاز
                                </label>
                            </div>
                        </div>

                        <!-- زر تسجيل الدخول -->
                        <div class="d-grid gap-2 mb-4">
                            <button type="submit" class="btn btn-primary btn-lg btn-face">
                                <i class="fas fa-sign-in-alt me-2"></i>تسجيل الدخول
                            </button>
                        </div>

                        <!-- روابط مساعدة -->
                        <div class="text-center">
                            @if (Route::has('password.request'))
                                <a class="btn btn-link text-decoration-none" href="{{ route('password.request') }}">
                                    <i class="fas fa-question-circle me-1"></i>نسيت كلمة المرور؟
                                </a>
                            @endif
                            
                            @if (Route::has('register'))
                                <div class="mt-3">
                                    <p class="text-muted">
                                        ليس لديك حساب؟
                                        <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">
                                            <i class="fas fa-user-plus me-1"></i>سجل الآن
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </form>

                    <!-- معلومات النظام -->
                    <div class="alert alert-info mt-4">
                        <h5 class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>معلومات هامة:
                        </h5>
                        <ul class="mb-0 mt-2">
                            <li>يجب تسجيل بصمة الوجه بعد تسجيل الدخول لأول مرة</li>
                            <li>البيانات مشفرة وآمنة</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .login-card {
        border-radius: 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 2px solid #e0e0e0;
    }
    
    .login-form .form-control {
        padding: 12px 15px;
        border-radius: 10px;
        border: 2px solid #e0e0e0;
        transition: all 0.3s;
        font-family: 'Tajawal', sans-serif;
    }
    
    .login-form .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }
    
    .input-group-text {
        border-radius: 10px 0 0 10px !important;
    }
    
    .btn-face {
        padding: 15px 30px;
        font-size: 18px;
        border-radius: 50px;
        margin: 10px;
        transition: all 0.3s;
        font-family: 'Tajawal', sans-serif;
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        border: none;
    }
    
    .btn-face:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    
    .alert-info {
        border-radius: 15px;
        border: 2px solid #0dcaf0;
        background: rgba(13, 202, 240, 0.1);
    }
    
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // إظهار/إخفاء كلمة المرور
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // تغيير الأيقونة
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
        
        // التحقق من صحة البريد الإلكتروني أثناء الكتابة
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('blur', function() {
                const email = this.value;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (email && !emailRegex.test(email)) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        }
        
        // إضافة تأثير عند التركيز على الحقول
        const formInputs = document.querySelectorAll('.form-control');
        formInputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
        
        // التحقق من صحة النموذج قبل الإرسال
        const loginForm = document.querySelector('.login-form');
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                
                if (!email || !password) {
                    e.preventDefault();
                    alert('⚠️ يرجى ملء جميع الحقول المطلوبة');
                    return false;
                }
                
                // إظهار رسالة تحميل
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري تسجيل الدخول...';
                    submitBtn.disabled = true;
                }
            });
        }
    });
</script>
@endsection