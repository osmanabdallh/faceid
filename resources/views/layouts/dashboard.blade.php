<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - نظام الحضور</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .dashboard-card {
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .face-status {
            font-size: 1.2rem;
            padding: 10px;
            border-radius: 10px;
        }
        .face-registered {
            background-color: #d4edda;
            color: #155724;
        }
        .face-not-registered {
            background-color: #f8d7da;
            color: #721c24;
        }
        .attendance-btn {
            padding: 20px;
            font-size: 1.5rem;
            border-radius: 15px;
            width: 100%;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">نظام الحضور بالتعرف على الوجه</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('attendance.history') }}">سجل الحضور</a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link">تسجيل الخروج</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-primary text-white">
                        <h4>مرحباً {{ Auth::user()->name }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="face-status {{ $hasFace ? 'face-registered' : 'face-not-registered' }}">
                                    حالة بصمة الوجه: 
                                    @if($hasFace)
                                        ✅ مسجلة
                                    @else
                                        ❌ غير مسجلة
                                    @endif
                                </div>
                                
                                @if(!$hasFace)
                                    <div class="alert alert-warning mt-3">
                                        <h5>تحذير!</h5>
                                        <p>لم تقم بتسجيل بصمة وجهك بعد. الرجاء تسجيل البصمة لاستخدام نظام الحضور.</p>
                                        <a href="{{ route('face.camera') }}" class="btn btn-warning">تسجيل بصمة الوجه</a>
                                    </div>
                                @endif
                                
                                @if($todayAttendance)
                                    <div class="alert alert-info mt-3">
                                        <h5>حالة حضور اليوم:</h5>
                                        <p>الحضور: {{ $todayAttendance->check_in ? date('H:i', strtotime($todayAttendance->check_in)) : 'لم يسجل' }}</p>
                                        <p>الانصراف: {{ $todayAttendance->check_out ? date('H:i', strtotime($todayAttendance->check_out)) : 'لم يسجل' }}</p>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h5>تسجيل الحضور/الانصراف</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($hasFace)
                                            <div id="attendanceSection">
                                                <video id="video" width="100%" height="auto" autoplay></video>
                                                <canvas id="canvas" style="display:none;"></canvas>
                                                
                                                <button id="captureBtn" class="btn btn-primary attendance-btn mt-3">
                                                    📸 التقاط صورة وتسجيل الحضور
                                                </button>
                                                
                                                <div id="result" class="mt-3"></div>
                                            </div>
                                        @else
                                            <p class="text-danger">يجب تسجيل بصمة الوجه أولاً</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if($monthStats)
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card dashboard-card">
                    <div class="card-header bg-info text-white">
                        <h5>إحصائيات الشهر</h5>
                    </div>
                    <div class="card-body">
                        <p>إجمالي أيام الحضور: {{ $monthStats->total }}</p>
                        <p>أيام مكتملة (حضور وانصراف): {{ $monthStats->completed }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // كود الكاميرا وتسجيل الحضور
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const captureBtn = document.getElementById('captureBtn');
        const resultDiv = document.getElementById('result');
        
        // تشغيل الكاميرا
        async function startCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: "user" } 
                });
                video.srcObject = stream;
                captureBtn.disabled = false;
            } catch (err) {
                resultDiv.innerHTML = `<div class="alert alert-danger">خطأ في الكاميرا: ${err.message}</div>`;
            }
        }
        
        // التقاط صورة
        captureBtn.addEventListener('click', function() {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const imageData = canvas.toDataURL('image/png');
            
            // إرسال الصورة لتسجيل الحضور
            markAttendance(imageData);
        });
        
        // تسجيل الحضور
        async function markAttendance(imageData) {
            resultDiv.innerHTML = '<div class="alert alert-info">جاري التحقق من الوجه وتسجيل الحضور...</div>';
            captureBtn.disabled = true;
            
            try {
                const response = await fetch('{{ route("attendance.mark") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ image: imageData })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    resultDiv.innerHTML = `<div class="alert alert-success">
                        ${data.message}<br>
                        الوقت: ${data.time}<br>
                        نسبة المطابقة: ${(data.similarity * 100).toFixed(2)}%
                    </div>`;
                    
                    // تحديث الصفحة بعد 3 ثوان
                    setTimeout(() => location.reload(), 3000);
                } else {
                    resultDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    captureBtn.disabled = false;
                }
            } catch (error) {
                resultDiv.innerHTML = `<div class="alert alert-danger">خطأ في الاتصال: ${error.message}</div>`;
                captureBtn.disabled = false;
            }
        }
        
        // بدء الكاميرا عند تحميل الصفحة
        window.onload = startCamera;
    </script>
</body>
</html>