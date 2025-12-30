<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>التقاط صورة الوجه</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #667eea;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .camera-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
        }
        .camera-header {
            background: #4f46e5;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .camera-preview {
            text-align: center;
            padding: 20px;
        }
        #video {
            width: 100%;
            max-width: 600px;
            border-radius: 10px;
            border: 3px solid #4f46e5;
        }
        .controls {
            margin: 20px 0;
        }
        .btn-camera {
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 50px;
            margin: 10px;
        }
    </style>
</head>
<body>
    <div class="camera-container">
        <div class="camera-header">
            <h1><i class="fas fa-camera"></i> التقاط صورة الوجه</h1>
            <p>التقط صورة واضحة لوجهك لتسجيل بصمة الوجه</p>
        </div>
        
        <div class="camera-preview">
            <video id="video" width="640" height="480" autoplay></video>
            
            <div class="controls">
                <button id="captureBtn" class="btn btn-primary btn-camera">
                    <i class="fas fa-camera"></i> التقاط صورة
                </button>
                <button id="retryBtn" class="btn btn-secondary btn-camera" style="display:none;">
                    <i class="fas fa-redo"></i> إعادة المحاولة
                </button>
                <a href="/home" class="btn btn-warning btn-camera">
                    <i class="fas fa-home"></i> العودة للرئيسية
                </a>
            </div>
            
            <canvas id="canvas" width="640" height="480" style="display:none;"></canvas>
            <div id="preview" class="mt-3"></div>
            
            <div id="result" class="mt-3"></div>
            
            <form id="uploadForm" method="POST" action="/face-register" style="display:none;">
                @csrf
                <input type="hidden" name="image" id="imageInput">
                <button type="submit" class="btn btn-success btn-camera">
                    <i class="fas fa-save"></i> حفظ البصمة
                </button>
            </form>
        </div>
        
        <div class="alert alert-info m-3">
            <h5><i class="fas fa-lightbulb"></i> نصائح للتصوير:</h5>
            <ul>
                <li>✅ تأكد من إضاءة جيدة</li>
                <li>✅ انظر مباشرة للكاميرا</li>
                <li>❌ تجنب الظلال على الوجه</li>
                <li>❌ لا تضع نظارات شمسية</li>
            </ul>
            <p class="mb-0"><strong>ملاحظة:</strong> هذا نظام تجريبي للاختبار</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const captureBtn = document.getElementById('captureBtn');
        const retryBtn = document.getElementById('retryBtn');
        const previewDiv = document.getElementById('preview');
        const resultDiv = document.getElementById('result');
        const uploadForm = document.getElementById('uploadForm');
        const imageInput = document.getElementById('imageInput');
        
        let capturedImage = null;

        // تشغيل الكاميرا
        async function startCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: { ideal: 640 },
                        height: { ideal: 480 },
                        facingMode: "user"
                    } 
                });
                video.srcObject = stream;
                captureBtn.disabled = false;
            } catch (err) {
                showResult('❌ خطأ في الكاميرا: ' + err.message, 'danger');
            }
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

        // التقاط صورة
        captureBtn.addEventListener('click', () => {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            capturedImage = canvas.toDataURL('image/png');
            
            // عرض المعاينة
            previewDiv.innerHTML = `
                <h4>معاينة الصورة:</h4>
                <img src="${capturedImage}" style="max-width:300px; border-radius:10px; border:2px solid #4f46e5;">
            `;
            
            // إخفاء زر التقاط وإظهار زر الإعادة والحفظ
            captureBtn.style.display = 'none';
            retryBtn.style.display = 'inline-block';
            uploadForm.style.display = 'block';
            
            // إيقاف الكاميرا
            const stream = video.srcObject;
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                video.style.display = 'none';
            }
            
            showResult('✅ تم التقاط الصورة بنجاح!', 'success');
        });

        // إعادة المحاولة
        retryBtn.addEventListener('click', () => {
            capturedImage = null;
            previewDiv.innerHTML = '';
            resultDiv.innerHTML = '';
            captureBtn.style.display = 'inline-block';
            retryBtn.style.display = 'none';
            uploadForm.style.display = 'none';
            video.style.display = 'block';
            startCamera();
        });

        // إرسال النموذج
        uploadForm.addEventListener('submit', function(e) {
            if (!capturedImage) {
                e.preventDefault();
                showResult('❌ لم يتم التقاط صورة', 'danger');
                return;
            }
            imageInput.value = capturedImage;
            showResult('⏳ جاري حفظ البصمة...', 'info');
        });

        // بدء الكاميرا عند تحميل الصفحة
        window.onload = startCamera;
    </script>
</body>
</html>