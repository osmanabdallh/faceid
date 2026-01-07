<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'نظام الحضور بالوجه') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Tajawal:400,500,700&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- مكتبة FaceAPI.js -->
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }
        
        body {
            font-family: 'Tajawal', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #F3F8F2 0%, #F5F4F4 100%);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
        }
        
        .dropdown-menu {
            text-align: right;
        }
        
        .btn-face {
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 50px;
            margin: 10px;
            transition: all 0.3s;
            font-family: 'Tajawal', sans-serif;
        }
        
        .btn-face:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        /* تخصيصات إضافية لصفحة الكاميرا */
        .card-header-camera {
            background: linear-gradient(to right, #4f46e5, #7c3aed);
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .video-container, .preview-container {
            text-align: center;
        }
        
        #video, #canvas {
            width: 100%;
            max-width: 500px;
            border-radius: 15px;
            border: 4px solid #4f46e5;
            background: #000;
        }
        
        .similarity-meter {
            width: 100%;
            height: 30px;
            background: #e5e7eb;
            border-radius: 15px;
            overflow: hidden;
            margin: 20px 0;
            position: relative;
        }
        
        .similarity-fill {
            height: 100%;
            background: linear-gradient(to right, #ef4444, #f59e0b, #10b981);
            transition: width 0.5s ease;
        }
        
        .similarity-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-weight: bold;
            color: #1f2937;
            text-shadow: 0 0 3px white;
        }
        
        .face-count {
            display: inline-block;
            background: #F3F8F2;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-right: 10px;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <!-- القائمة الرئيسية -->
    <ul class="navbar-nav me-auto">
        @auth
            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="fas fa-home me-1"></i>الرئيسية
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('attendance.history') }}">
                    <i class="fas fa-history me-1"></i>سجل الحضور
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('face.browser') }}">
                    <i class="fas fa-camera me-1"></i>الكاميرا
                </a>
            </li>
        @endauth
    </ul>

    <!-- القائمة الجانبية -->
    <ul class="navbar-nav ms-auto">
        <!-- روابط المصادقة -->
        @guest
            @if (Route::has('login'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt me-1"></i>{{ __('تسجيل الدخول') }}
                    </a>
                </li>
            @endif

            @if (Route::has('register'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">
                        <i class="fas fa-user-plus me-1"></i>{{ __('تسجيل حساب جديد') }}
                    </a>
                </li>
            @endif
        @else
            <li class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" 
                   data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    <i class="fas fa-user-circle me-1"></i>{{ Auth::user()->name }}
                </a>

                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="{{ route('home') }}">
                        <i class="fas fa-home me-2"></i>الرئيسية
                    </a>
                    <a class="dropdown-item" href="{{ route('user.settings') }}">
                        <i class="fas fa-user-cog me-2"></i>إعدادات الحساب
                    </a>
                    <a class="dropdown-item" href="{{ route('attendance.history') }}">
                        <i class="fas fa-history me-2"></i>سجل الحضور
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" id="logout-link">
                        <i class="fas fa-sign-out-alt me-2"></i>{{ __('تسجيل الخروج') }}
                    </a>
                </div>
            </li>
        @endguest
    </ul>
</div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
        
        <!-- Footer -->
        <footer class="bg-dark text-white py-4 mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start">
                        <h5><i class="fas fa-fingerprint me-2"></i>نظام الحضور الذكي</h5>
                        <p class="mb-0">نظام متقدم للحضور باستخدام التعرف على الوجه</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <p class="mb-0">
                            <i class="fas fa-clock me-1"></i>توقيت الخرطوم
                            <span id="khartoum-time" class="fw-bold">{{ now()->timezone('Africa/Khartoum')->format('H:i:s') }}</span>
                        </p>
                        <p class="mb-0 text-muted">
                            © {{ date('Y') }} جميع الحقوق محفوظة
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Font Awesome JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    
    <script>
        // تحديث وقت الخرطوم كل ثانية
        function updateKhartoumTime() {
            const now = new Date();
            const khartoumTime = new Date(now.getTime() + (2 * 60 * 60 * 1000)); // UTC+2
            
            const hours = khartoumTime.getUTCHours().toString().padStart(2, '0');
            const minutes = khartoumTime.getUTCMinutes().toString().padStart(2, '0');
            const seconds = khartoumTime.getUTCSeconds().toString().padStart(2, '0');
            
            document.getElementById('khartoum-time').textContent = `${hours}:${minutes}:${seconds}`;
        }
        
        setInterval(updateKhartoumTime, 1000);
        updateKhartoumTime(); // التهيئة الأولية
        
        // إصلاح تسجيل الخروج
        document.addEventListener('DOMContentLoaded', function() {
            const logoutLink = document.getElementById('logout-link');
            
            if (logoutLink) {
                logoutLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    if (confirm('هل أنت متأكد من تسجيل الخروج؟')) {
                        // إنشاء form جديد لتسجيل الخروج
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("logout") }}';
                        form.style.display = 'none';
                        
                        // إضافة CSRF token
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
                        form.appendChild(csrfToken);
                        
                        // إضافة إلى body وإرساله
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>