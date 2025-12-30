<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>اختبار نظام الحضور</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2>🧪 اختبار نظام الحضور</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>اختبار 1: سيرفر البايثون</h4>
                        <button id="testServerBtn" class="btn btn-info mb-2">
                            اختبار اتصال السيرفر
                        </button>
                        <div id="serverResult"></div>
                    </div>
                    <div class="col-md-6">
                        <h4>اختبار 2: تسجيل حضور</h4>
                        <button id="testAttendanceBtn" class="btn btn-success mb-2">
                            اختبار تسجيل حضور
                        </button>
                        <div id="attendanceResult"></div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h4>📊 معلومات النظام</h4>
                        <div class="alert alert-info">
                            <p><strong>السيرفر البايثون:</strong> http://127.0.0.1:8000</p>
                            <p><strong>نظام:</strong> تجريبي (Mock Server)</p>
                            <p><strong>الحالة:</strong> جاهز للاختبار</p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="/home" class="btn btn-primary">العودة للرئيسية</a>
                    <a href="/face-camera" class="btn btn-warning">تسجيل وجه جديد</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const testServerBtn = document.getElementById('testServerBtn');
        const testAttendanceBtn = document.getElementById('testAttendanceBtn');
        const serverResult = document.getElementById('serverResult');
        const attendanceResult = document.getElementById('attendanceResult');

        // اختبار السيرفر
        testServerBtn.addEventListener('click', async () => {
            serverResult.innerHTML = '<div class="alert alert-info">⏳ جاري اختبار الاتصال...</div>';
            
            try {
                const response = await fetch('http://127.0.0.1:8000/', {
                    method: 'GET'
                });
                
                if (response.ok) {
                    const data = await response.json();
                    serverResult.innerHTML = `
                        <div class="alert alert-success">
                            ✅ السيرفر يعمل بنجاح!<br>
                            <strong>الحالة:</strong> ${data.status}<br>
                            <strong>الرسالة:</strong> ${data.message}<br>
                            <strong>النسخة:</strong> ${data.version}
                        </div>
                    `;
                } else {
                    serverResult.innerHTML = `
                        <div class="alert alert-danger">
                            ❌ السيرفر لا يستجيب<br>
                            تأكد من تشغيل: python mock_python_server.py
                        </div>
                    `;
                }
            } catch (error) {
                serverResult.innerHTML = `
                    <div class="alert alert-danger">
                        ❌ خطأ في الاتصال: ${error.message}<br>
                        تأكد من تشغيل السيرفر البايثون أولاً
                    </div>
                `;
            }
        });

        // اختبار الحضور
        testAttendanceBtn.addEventListener('click', async () => {
            attendanceResult.innerHTML = '<div class="alert alert-info">⏳ جاري اختبار الحضور...</div>';
            
            try {
                const response = await fetch('/attendance', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ test: true })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    attendanceResult.innerHTML = `
                        <div class="alert alert-success">
                            ✅ ${data.message}<br>
                            <strong>النوع:</strong> ${data.type}<br>
                            <strong>الوقت:</strong> ${data.time}<br>
                            <strong>التاريخ:</strong> ${data.date}
                        </div>
                    `;
                } else {
                    attendanceResult.innerHTML = `
                        <div class="alert alert-danger">
                            ❌ ${data.message}
                        </div>
                    `;
                }
            } catch (error) {
                attendanceResult.innerHTML = `
                    <div class="alert alert-danger">
                        ❌ خطأ في الاتصال: ${error.message}
                    </div>
                `;
            }
        });
    </script>
</body>
</html>