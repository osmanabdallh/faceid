<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل بصمة الوجه</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
        }
        
        .header {
            background: #4f46e5;
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px;
        }
        
        .camera-section {
            text-align: center;
            margin-bottom: 30px;
        }
        
        #video {
            width: 100%;
            max-width: 500px;
            border-radius: 10px;
            border: 3px solid #4f46e5;
        }
        
        .controls {
            margin-top: 20px;
        }
        
        button {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 16px;
            cursor: pointer;
            margin: 0 10px;
            transition: all 0.3s;
        }
        
        button:hover {
            background: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        #capture {
            background: #10b981;
        }
        
        #capture:hover {
            background: #0da271;
        }
        
        #register {
            background: #f59e0b;
        }
        
        #register:hover {
            background: #d97706;
        }
        
        .preview {
            display: none;
            margin-top: 20px;
        }
        
        #canvas {
            width: 100%;
            max-width: 500px;
            border-radius: 10px;
            border: 3px solid #10b981;
        }
        
        .status {
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            display: none;
        }
        
        .success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .loading {
            display: none;
            text-align: center;
            margin: 20px 0;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4f46e5;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .instructions {
            background: #f8fafc;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .instructions ul {
            text-align: right;
            padding-right: 20px;
        }
        
        .instructions li {
            margin-bottom: 10px;
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👤 تسجيل بصمة الوجه</h1>
            <p>التقط صورة واضحة لوجهك لتسجيل بصمة الوجه</p>
        </div>
        
        <div class="content">
            <div class="camera-section">
                <video id="video" width="640" height="480" autoplay></video>
                
                <div class="controls">
                    <button id="startCamera">تشغيل الكاميرا</button>
                    <button id="capture" disabled>التقاط صورة</button>
                    <button id="register" disabled>تسجيل البصمة</button>
                </div>
                
                <div class="preview">
                    <h3>معاينة الصورة:</h3>
                    <canvas id="canvas" width="640" height="480"></canvas>
                </div>
                
                <div class="loading" id="loading">
                    <div class="spinner"></div>
                    <p>جاري معالجة الصورة...</p>
                </div>
                
                <div class="status" id="status"></div>
            </div>
            
            <div class="instructions">
                <h3>📋 تعليمات التسجيل:</h3>
                <ul>
                    <li>✅ تأكد من إضاءة جيدة</li>
                    <li>✅ اجعل وجهك في منتصف الشاشة</li>
                    <li>✅ انظر مباشرة إلى الكاميرا</li>
                    <li>❌ لا تضع نظارات شمسية</li>
                    <li>❌ تجنب الظلال على الوجه</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const context = canvas.getContext('2d');
        const startCameraBtn = document.getElementById('startCamera');
        const captureBtn = document.getElementById('capture');
        const registerBtn = document.getElementById('register');
        const loading = document.getElementById('loading');
        const statusDiv = document.getElementById('status');
        const previewDiv = document.querySelector('.preview');
        
        let stream = null;
        let capturedImage = null;
        
        startCameraBtn.addEventListener('click', async () => {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: { ideal: 640 },
                        height: { ideal: 480 },
                        facingMode: 'user'
                    } 
                });
                video.srcObject = stream;
                captureBtn.disabled = false;
                startCameraBtn.disabled = true;
                showStatus('تم تشغيل الكاميرا بنجاح', 'success');
            } catch (err) {
                console.error('Camera error:', err);
                showStatus('فشل في تشغيل الكاميرا: ' + err.message, 'error');
            }
        });
        
        captureBtn.addEventListener('click', () => {
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            capturedImage = canvas.toDataURL('image/png');
            previewDiv.style.display = 'block';
            registerBtn.disabled = false;
            showStatus('تم التقاط الصورة بنجاح', 'success');
        });
        
        registerBtn.addEventListener('click', async () => {
            if (!capturedImage) {
                showStatus('لم يتم التقاط صورة', 'error');
                return;
            }
            
            loading.style.display = 'block';
            registerBtn.disabled = true;
            
            try {
                const response = await fetch('/face/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ image: capturedImage })
                });
                
                const data = await response.json();
                
                if (data.success || response.ok) {
                    showStatus('✅ تم تسجيل بصمة الوجه بنجاح!', 'success');
                    setTimeout(() => {
                        window.location.href = '/home';
                    }, 2000);
                } else {
                    showStatus('❌ ' + (data.message || 'فشل التسجيل'), 'error');
                    registerBtn.disabled = false;
                }
            } catch (error) {
                console.error('Registration error:', error);
                showStatus('❌ خطأ في الاتصال: ' + error.message, 'error');
                registerBtn.disabled = false;
            } finally {
                loading.style.display = 'none';
            }
        });
        
        function showStatus(message, type) {
            statusDiv.textContent = message;
            statusDiv.className = `status ${type}`;
            statusDiv.style.display = 'block';
            
            setTimeout(() => {
                if (type === 'success') {
                    statusDiv.style.display = 'none';
                }
            }, 5000);
        }
    </script>
</body>
</html>