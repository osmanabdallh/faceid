<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>نظام الحضور بالوجه</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: none;
            margin-bottom: 20px;
        }
        .status-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
        }
        .btn-attendance {
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 50px;
            margin: 10px;
            transition: all 0.3s;
        }
        .btn-attendance:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .face-status {
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: bold;
        }
        .face-registered {
            background: #d4edda;
            color: #155724;
        }
        .face-not-registered {
            background: #f8d7da;
            color: #721c24;
        }
        .nav-tabs .nav-link.active {
            background: #4f46e5;
            color: white;
            border-radius: 10px;
        }
        .attendance-log {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/home">
                <i class="fas fa-fingerprint"></i> نظام الحضور الذكي
            </a>
            <div class="navbar-nav">
                <a class="nav-link" href="/attendance/history">
                    <i class="fas fa-history"></i> سجل الحضور
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link">
                        <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- البطاقة الرئيسية -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-user-circle"></i> مرحباً {{ Auth::user()->name }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="status-card">
                                    <h5>حالة الوجه</h5>
                                    <div class="face-status {{ $hasFace ? 'face-registered' : 'face-not-registered' }}">
                                        @if($hasFace)
                                            <i class="fas fa-check-circle"></i> ✅ مسجل
                                        @else
                                            <i class="fas fa-times-circle"></i> ❌ غير مسجل
                                        @endif
                                    </div>
                                    @if(!$hasFace)
                                        <a href="/face-camera" class="btn btn-warning mt-3">
                                            <i class="fas fa-camera"></i> تسجيل بصمة الوجه
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="status-card">
                                    <h5>حضور اليوم</h5>
                                    @if($todayAttendance)
                                        <p><strong>الحضور:</strong> {{ $todayAttendance->check_in ? date('H:i', strtotime($todayAttendance->check_in)) : 'لم يسجل' }}</p>
                                        <p><strong>الانصراف:</strong> {{ $todayAttendance->check_out ? date('H:i', strtotime($todayAttendance->check_out)) : 'لم يسجل' }}</p>
                                        @if(!$todayAttendance->check_out)
                                            <button id="checkOutBtn" class="btn btn-danger btn-attendance">
                                                <i class="fas fa-sign-out-alt"></i> تسجيل انصراف
                                            </button>
                                        @endif
                                    @else
                                        <p>لم يسجل حضور اليوم</p>
                                        <button id="checkInBtn" class="btn btn-success btn-attendance">
                                            <i class="fas fa-sign-in-alt"></i> تسجيل حضور
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div id="cameraSection" style="display: none;">
                                <h5><i class="fas fa-camera"></i> التقط صورة للتحقق</h5>
                                <div class="text-center">
                                    <video id="video" width="100%" height="auto" autoplay style="max-width: 500px; border-radius: 10px;"></video>
                                    <br>
                                    <button id="captureBtn" class="btn btn-primary mt-2">
                                        <i class="fas fa-camera"></i> التقاط صورة
                                    </button>
                                    <canvas id="canvas" style="display: none;"></canvas>
                                </div>
                            </div>
                            <div id="result" class="mt-3"></div>
                        </div>
                    </div>
                </div>

                <!-- إحصائيات -->
                <div class="card mt-3">
                    <div class="card-header bg-info text-white">
                        <h5><i class="fas fa-chart-bar"></i> إحصائيات</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h2>{{ $totalDays }}</h2>
                                <p>أيام الحضور</p>
                            </div>
                            <div class="col-md-4">
                                <h2>✅</h2>
                                <p>النظام يعمل</p>
                            </div>
                            <div class="col-md-4">
                                <h2>🎯</h2>
                                <p>دقة عالية</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الجانب الأيمن -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-bolt"></i> إجراءات سريعة</h5>
                    </div>
                    <div class="card-body">
                        <a href="/face-camera" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-camera"></i> تسجيل وجه جديد
                        </a>
                        <a href="/attendance/history" class="btn btn-outline-info w-100 mb-2">
                            <i class="fas fa-history"></i> سجل الحضور
                        </a>
                        <a href="/test-attendance" class="btn btn-outline-warning w-100 mb-2">
                            <i class="fas fa-test"></i> صفحة الاختبار
                        </a>
                    </div>
                </div>

                <!-- تعليمات -->
                <div class="card mt-3">
                    <div class="card-header bg-warning text-dark">
                        <h5><i class="fas fa-info-circle"></i> تعليمات الاستخدام</h5>
                    </div>
                    <div class="card-body">
                        <ol>
                            <li>تأكد من إضاءة جيدة</li>
                            <li>انظر مباشرة للكاميرا</li>
                            <li>حاول إبقاء الوجه في المنتصف</li>
                            <li>النظام يعمل تجريبياً الآن</li>
                        </ol>
                        <div class="alert alert-info">
                            <i class="fas fa-lightbulb"></i> 
                            <strong>ملاحظة:</strong> هذا نظام تجريبي للاختبار
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // عناصر DOM
        const checkInBtn = document.getElementById('checkInBtn');
        const checkOutBtn = document.getElementById('checkOutBtn');
        const cameraSection = document.getElementById('cameraSection');
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const captureBtn = document.getElementById('captureBtn');
        const resultDiv = document.getElementById('result');
        
        let currentAction = ''; // 'check_in' أو 'check_out'
        let stream = null;

        // تشغيل الكاميرا
        async function startCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: "user" } 
                });
                video.srcObject = stream;
                cameraSection.style.display = 'block';
            } catch (err) {
                showResult('❌ خطأ في الكاميرا: ' + err.message, 'danger');
            }
        }

        // إيقاف الكاميرا
        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            cameraSection.style.display = 'none';
        }

        // عرض نتيجة
        function showResult(message, type = 'success') {
            resultDiv.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }

        // تسجيل الحضور
        if (checkInBtn) {
            checkInBtn.addEventListener('click', () => {
                currentAction = 'check_in';
                startCamera();
            });
        }

        // تسجيل الانصراف
        if (checkOutBtn) {
            checkOutBtn.addEventListener('click', () => {
                currentAction = 'check_out';
                startCamera();
            });
        }

        // التقاط الصورة
        captureBtn.addEventListener('click', async () => {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const imageData = canvas.toDataURL('image/png');
            
            // إرسال الطلب
            showResult('⏳ جاري التحقق من الوجه...', 'info');
            
            try {
                const response = await fetch('/attendance', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ image: imageData })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showResult('✅ ' + data.message, 'success');
                    stopCamera();
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showResult('❌ ' + data.message, 'danger');
                }
            } catch (error) {
                showResult('❌ خطأ في الاتصال: ' + error.message, 'danger');
            }
        });
    </script>
</body>
</html>