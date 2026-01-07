<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>نظام التعرف على الوجه في المتصفح</title>
    
    <!-- مكتبة FaceAPI.js -->
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
            padding: 30px;
            border-radius: 20px;
            background: rgba(0, 0, 0, 0.3);
        }
        
        .header h1 {
            font-size: 2.8rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .card-header {
            background: linear-gradient(to right, #4f46e5, #7c3aed);
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .card-header h2 {
            font-size: 1.8rem;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .card-body {
            padding: 40px;
        }
        
        .camera-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        @media (max-width: 768px) {
            .camera-section {
                grid-template-columns: 1fr;
            }
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
        
        .controls {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 25px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 15px 35px;
            border: none;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-primary {
            background: linear-gradient(to right, #4f46e5, #7c3aed);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(to right, #10b981, #34d399);
            color: white;
        }
        
        .btn-success:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
        }
        
        .btn-warning {
            background: linear-gradient(to right, #f59e0b, #fbbf24);
            color: white;
        }
        
        .btn-warning:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.4);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }
        
        .status-container {
            margin-top: 30px;
            padding: 25px;
            border-radius: 15px;
            background: #f8fafc;
            border-left: 5px solid #4f46e5;
        }
        
        .status {
            font-size: 1.1rem;
            font-weight: 600;
            padding: 12px 25px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .status-loading {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-error {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .status-info {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .face-count {
            display: inline-block;
            background: #4f46e5;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-left: 10px;
        }
        
        .progress-container {
            margin: 25px 0;
        }
        
        .progress-bar {
            height: 10px;
            background: #e5e7eb;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(to right, #4f46e5, #7c3aed);
            border-radius: 5px;
            transition: width 0.5s ease;
        }
        
        .instructions {
            background: #f0f9ff;
            border-radius: 15px;
            padding: 25px;
            margin-top: 30px;
            border: 2px dashed #0ea5e9;
        }
        
        .instructions h3 {
            color: #0369a1;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .instructions ul {
            list-style: none;
            padding-right: 20px;
        }
        
        .instructions li {
            margin-bottom: 12px;
            padding-right: 10px;
            position: relative;
        }
        
        .instructions li:before {
            content: "✓";
            position: absolute;
            right: -20px;
            color: #10b981;
            font-weight: bold;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: 2px solid #e5e7eb;
        }
        
        .stat-card h3 {
            font-size: 2rem;
            color: #4f46e5;
            margin-bottom: 10px;
        }
        
        .stat-card p {
            color: #6b7280;
            font-weight: 500;
        }
        
        .face-data {
            background: #1f2937;
            color: white;
            padding: 20px;
            border-radius: 10px;
            font-family: monospace;
            margin-top: 20px;
            overflow-x: auto;
            max-height: 200px;
            overflow-y: auto;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 30px;
        }
        
        .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #4f46e5;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .alert {
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            display: none;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #a7f3d0;
        }
        
        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #fecaca;
        }
        
        .nav-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        
        .face-points {
            position: absolute;
            pointer-events: none;
        }
        
        .face-point {
            position: absolute;
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            border: 2px solid white;
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
    </style>
</head>
<body>
    <div class="container">
        <!-- العنوان الرئيسي -->
        <div class="header">
            <h1>🧬 نظام التعرف على الوجه في المتصفح</h1>
            <p>لا يحتاج تثبيت أي مكتبات على السيرفر - كل شيء يعمل في متصفحك</p>
        </div>

        <!-- البطاقة الرئيسية -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-camera"></i> تسجيل بصمة الوجه</h2>
            </div>
            
            <div class="card-body">
                <!-- قسم الكاميرا والمعاينة -->
                <div class="camera-section">
                    <div class="video-container">
                        <h3>كاميرا الويب <span id="faceCount" class="face-count">0 وجوه</span></h3>
                        <video id="video" width="500" height="375" autoplay muted playsinline></video>
                        <div class="face-points" id="facePoints"></div>
                    </div>
                    
                    <div class="preview-container">
                        <h3>معاينة الوجه</h3>
                        <canvas id="canvas" width="500" height="375"></canvas>
                        <div class="similarity-meter" id="similarityMeter" style="display:none;">
                            <div class="similarity-fill" id="similarityFill" style="width:0%"></div>
                            <div class="similarity-text" id="similarityText">0%</div>
                        </div>
                    </div>
                </div>

                <!-- عناصر التحكم -->
                <div class="controls">
                    <button id="startCamera" class="btn btn-primary">
                        <i class="fas fa-play"></i> تشغيل الكاميرا
                    </button>
                    
                    <button id="detectFaces" class="btn btn-warning" disabled>
                        <i class="fas fa-search"></i> اكتشاف الوجوه
                    </button>
                    
                    <button id="registerFace" class="btn btn-success" disabled>
                        <i class="fas fa-save"></i> تسجيل البصمة
                    </button>
                    
                    <button id="verifyFace" class="btn btn-primary" disabled>
                        <i class="fas fa-check-circle"></i> التحقق من الوجه
                    </button>
                </div>

                <!-- حالة التحميل -->
                <div class="loading" id="loading">
                    <div class="spinner"></div>
                    <p>جاري تحميل نماذج التعرف على الوجه...</p>
                </div>

                <!-- حاوية الحالة -->
                <div id="statusContainer" class="status-container">
                    <div class="status status-info" id="statusMessage">
                        <i class="fas fa-info-circle"></i>
                        اضغط على "تشغيل الكاميرا" للبدء
                    </div>
                    <div class="progress-container">
                        <div class="progress-bar">
                            <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <!-- الإشعارات -->
                <div class="alert alert-success" id="successAlert" style="display:none;"></div>
                <div class="alert alert-danger" id="errorAlert" style="display:none;"></div>

                <!-- التعليمات -->
                <div class="instructions">
                    <h3><i class="fas fa-graduation-cap"></i> تعليمات الاستخدام:</h3>
                    <ul>
                        <li>تأكد من إضاءة جيدة على وجهك</li>
                        <li>اجعل وجهك في منتصف الشاشة</li>
                        <li>انظر مباشرة إلى الكاميرا</li>
                        <li>ابقَ ثابتاً أثناء تسجيل البصمة</li>
                        <li>النظام لا يرسل أي صور إلى السيرفر</li>
                        <li>بيانات البصمة مشفرة ومحمولة</li>
                    </ul>
                </div>

                <!-- الإحصائيات -->
                <div class="stats">
                    <div class="stat-card">
                        <h3 id="modelStatus">❌</h3>
                        <p>نماذج التعرف</p>
                    </div>
                    
                    <div class="stat-card">
                        <h3 id="faceStatus">0</h3>
                        <p>وجوه مكتشفة</p>
                    </div>
                    
                    <div class="stat-card">
                        <h3 id="encodingStatus">❌</h3>
                        <p>بصمة مسجلة</p>
                    </div>
                    
                    <div class="stat-card">
                        <h3 id="performanceStatus">0ms</h3>
                        <p>سرعة المعالجة</p>
                    </div>
                </div>

                <!-- بيانات البصمة (اختياري) -->
                <div style="display:none;" id="encodingData">
                    <h3>بيانات البصمة:</h3>
                    <div class="face-data" id="encodingOutput"></div>
                </div>

                <!-- أزرار التنقل -->
                <div class="nav-buttons">
                    <a href="/home" class="btn btn-primary">
                        <i class="fas fa-home"></i> العودة للرئيسية
                    </a>
                    
                    <a href="/attendance/history" class="btn btn-warning">
                        <i class="fas fa-history"></i> سجل الحضور
                    </a>
                    
                    <button id="testAttendance" class="btn btn-success">
                        <i class="fas fa-clock"></i> اختبار تسجيل الحضور
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- مكتبة Font Awesome للأيقونات -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    
    <!-- الكود الرئيسي -->
    <script>
        // ================ تعريف المتغيرات ================
        let video = document.getElementById('video');
        let canvas = document.getElementById('canvas');
        let ctx = canvas.getContext('2d');
        let facePoints = document.getElementById('facePoints');
        let currentFaceDescriptor = null;
        let storedFaceDescriptor = null;
        let isCameraOn = false;
        let faceDetectionInterval = null;
        let modelsLoaded = false;

        // ================ عناصر DOM ================
        const startCameraBtn = document.getElementById('startCamera');
        const detectFacesBtn = document.getElementById('detectFaces');
        const registerFaceBtn = document.getElementById('registerFace');
        const verifyFaceBtn = document.getElementById('verifyFace');
        const testAttendanceBtn = document.getElementById('testAttendance');
        const loadingDiv = document.getElementById('loading');
        const statusMessage = document.getElementById('statusMessage');
        const progressFill = document.getElementById('progressFill');
        const successAlert = document.getElementById('successAlert');
        const errorAlert = document.getElementById('errorAlert');
        const faceCount = document.getElementById('faceCount');
        const modelStatus = document.getElementById('modelStatus');
        const faceStatus = document.getElementById('faceStatus');
        const encodingStatus = document.getElementById('encodingStatus');
        const performanceStatus = document.getElementById('performanceStatus');
        const similarityMeter = document.getElementById('similarityMeter');
        const similarityFill = document.getElementById('similarityFill');
        const similarityText = document.getElementById('similarityText');

        // ================ تحميل نماذج FaceAPI ================
        async function loadModels() {
            try {
                showLoading('جاري تحميل نماذج التعرف على الوجه...');
                updateProgress(10);

                // استخدام نماذج FaceAPI من CDN
                await faceapi.nets.tinyFaceDetector.loadFromUri('https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights');
                updateProgress(40);
                
                await faceapi.nets.faceLandmark68Net.loadFromUri('https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights');
                updateProgress(70);
                
                await faceapi.nets.faceRecognitionNet.loadFromUri('https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights');
                updateProgress(100);

                modelsLoaded = true;
                modelStatus.innerHTML = '✅';
                updateStatus('✅ نماذج التعرف على الوجه جاهزة', 'success');
                hideLoading();
                
                detectFacesBtn.disabled = false;
                startCameraBtn.disabled = true;
                
                // بدء الكاميرا تلقائياً
                startCamera();
                
            } catch (error) {
                console.error('Error loading models:', error);
                updateStatus('❌ فشل تحميل النماذج: ' + error.message, 'error');
                modelStatus.innerHTML = '❌';
                hideLoading();
            }
        }

        // ================ تشغيل الكاميرا ================
        async function startCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: { ideal: 500 },
                        height: { ideal: 375 },
                        facingMode: "user" 
                    },
                    audio: false 
                });
                
                video.srcObject = stream;
                isCameraOn = true;
                updateStatus('✅ الكاميرا تعمل - ابدأ باكتشاف الوجوه', 'success');
                
                // تفعيل زر اكتشاف الوجوه
                detectFacesBtn.disabled = false;
                startCameraBtn.disabled = true;
                
            } catch (error) {
                console.error('Camera error:', error);
                updateStatus('❌ خطأ في الكاميرا: ' + error.message, 'error');
            }
        }

        // ================ اكتشاف الوجوه ================
        async function detectFaces() {
            if (!isCameraOn || !modelsLoaded) {
                updateStatus('❌ يرجى تشغيل الكاميرا أولاً', 'error');
                return;
            }

            if (faceDetectionInterval) {
                clearInterval(faceDetectionInterval);
                faceDetectionInterval = null;
                detectFacesBtn.innerHTML = '<i class="fas fa-search"></i> اكتشاف الوجوه';
                updateStatus('⏸️ توقف اكتشاف الوجوه', 'info');
                return;
            }

            detectFacesBtn.innerHTML = '<i class="fas fa-stop"></i> إيقاف الاكتشاف';
            updateStatus('🔍 جاري اكتشاف الوجوه...', 'info');

            faceDetectionInterval = setInterval(async () => {
                const startTime = performance.now();
                
                try {
                    // اكتشاف الوجوه
                    const detections = await faceapi.detectAllFaces(
                        video, 
                        new faceapi.TinyFaceDetectorOptions()
                    ).withFaceLandmarks().withFaceDescriptors();
                    
                    const endTime = performance.now();
                    const processingTime = endTime - startTime;
                    
                    // تحديث الإحصائيات
                    faceStatus.innerHTML = detections.length;
                    faceCount.innerHTML = `${detections.length} وجوه`;
                    performanceStatus.innerHTML = `${processingTime.toFixed(0)}ms`;
                    
                    // تنظيف النقاط السابقة
                    facePoints.innerHTML = '';
                    
                    // رسم النقاط على الوجه
                    if (detections.length > 0) {
                        const detection = detections[0];
                        
                        // رسم نقاط معالم الوجه
                        if (detection.landmarks) {
                            detection.landmarks.positions.forEach(point => {
                                const pointElement = document.createElement('div');
                                pointElement.className = 'face-point';
                                pointElement.style.left = `${point.x}px`;
                                pointElement.style.top = `${point.y}px`;
                                facePoints.appendChild(pointElement);
                            });
                        }
                        
                        // حفظ واصف الوجه الحالي
                        currentFaceDescriptor = detection.descriptor;
                        
                        // رسم على Canvas
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                        
                        // رسم مربع حول الوجه
                        const box = detection.detection.box;
                        ctx.strokeStyle = '#10b981';
                        ctx.lineWidth = 3;
                        ctx.strokeRect(box.x, box.y, box.width, box.height);
                        
                        // تفعيل أزرار التسجيل والتحقق
                        registerFaceBtn.disabled = false;
                        verifyFaceBtn.disabled = false;
                        
                        updateStatus(`✅ تم اكتشاف ${detections.length} وجه`, 'success');
                    } else {
                        registerFaceBtn.disabled = true;
                        verifyFaceBtn.disabled = true;
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        updateStatus('❌ لم يتم اكتشاف أي وجه', 'error');
                    }
                    
                } catch (error) {
                    console.error('Face detection error:', error);
                }
            }, 500); // فحص كل 500ms
        }

      // ================ تسجيل بصمة الوجه ================
async function registerFace() {
    if (!currentFaceDescriptor) {
        updateStatus('❌ لم يتم اكتشاف وجه لتسجيله', 'error');
        showAlert('error', '❌ يرجى اكتشاف وجه أولاً');
        return;
    }

    try {
        showLoading('جاري تسجيل بصمة الوجه...');
        
        // تحويل FaceDescriptor إلى array
        const encodingArray = Array.from(currentFaceDescriptor);
        console.log('Encoding array:', encodingArray.slice(0, 5)); // لأغراض التصحيح
        
        // إعداد بيانات الطلب
        const requestData = {
            encoding: encodingArray,
            timestamp: new Date().toISOString()
        };
        
        console.log('Sending request data:', requestData);
        
        // إرسال البيانات إلى السيرفر
        const response = await fetch('/face/save-encoding', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(requestData)
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        // التحقق من نوع الرد
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const textResponse = await response.text();
            console.error('Non-JSON response:', textResponse.substring(0, 500));
            throw new Error(`الخادم رد بـ HTML بدلاً من JSON. تأكد من صحة الـ Route.`);
        }
        
        // محاولة تحليل JSON
        let data;
        try {
            data = await response.json();
        } catch (jsonError) {
            console.error('JSON parse error:', jsonError);
            throw new Error('فشل تحليل الاستجابة من الخادم');
        }
        
        console.log('Response data:', data);
        
        if (data.success) {
            // حفظ محلياً
            storedFaceDescriptor = currentFaceDescriptor;
            localStorage.setItem('face_encoding', JSON.stringify(encodingArray));
            localStorage.setItem('face_registered_at', new Date().toISOString());
            
            encodingStatus.innerHTML = '✅';
            
            showAlert('success', '✅ تم تسجيل بصمة الوجه بنجاح!');
            updateStatus('✅ البصمة مسجلة وجاهزة', 'success');
            
        } else {
            throw new Error(data.message || 'فشل التسجيل');
        }
        
    } catch (error) {
        console.error('Registration error:', error);
        
        let errorMessage = error.message;
        if (errorMessage.includes('Unexpected token')) {
            errorMessage = '❌ الخادم يرد بـ HTML بدلاً من JSON. تأكد من:';
            errorMessage += '\n1. صحة الـ Route: /face/save-encoding';
            errorMessage += '\n2. أن Controller يعيد response()->json()';
            errorMessage += '\n3. عدم وجود أخطاء في PHP';
        }
        
        showAlert('error', errorMessage);
        updateStatus('❌ فشل تسجيل البصمة', 'error');
    } finally {
        hideLoading();
    }
}

        // ================ التحقق من الوجه ================
        async function verifyFace() {
            if (!currentFaceDescriptor) {
                updateStatus('❌ لم يتم اكتشاف وجه للتحقق منه', 'error');
                return;
            }

            // تحميل البصمة المخزنة من localStorage أو من السيرفر
            let storedDescriptor = storedFaceDescriptor;
            
            if (!storedDescriptor) {
                const savedEncoding = localStorage.getItem('face_encoding');
                if (savedEncoding) {
                    storedDescriptor = new Float32Array(JSON.parse(savedEncoding));
                }
            }

            if (!storedDescriptor) {
                updateStatus('❌ لا يوجد بصمة مسجلة للتحقق منها', 'error');
                return;
            }

            try {
                // حساب المسافة بين البصمتين
                const distance = faceapi.euclideanDistance(currentFaceDescriptor, storedDescriptor);
                const similarity = Math.max(0, 100 - (distance * 100));
                
                // عرض مؤشر التشابه
                similarityMeter.style.display = 'block';
                similarityFill.style.width = `${similarity}%`;
                similarityText.textContent = `${similarity.toFixed(1)}%`;
                
                if (similarity > 70) {
                    similarityFill.style.background = 'linear-gradient(to right, #10b981, #34d399)';
                    updateStatus(`✅ التحقق ناجح - التشابه: ${similarity.toFixed(1)}%`, 'success');
                    showAlert('success', `✅ الوجه مطابق بنسبة ${similarity.toFixed(1)}%`);
                    
                    // تفعيل زر تسجيل الحضور
                    testAttendanceBtn.disabled = false;
                    
                } else {
                    similarityFill.style.background = 'linear-gradient(to right, #ef4444, #f59e0b)';
                    updateStatus(`❌ الوجه غير مطابق - التشابه: ${similarity.toFixed(1)}%`, 'error');
                    showAlert('error', `❌ الوجه غير مطابق - التشابه: ${similarity.toFixed(1)}%`);
                }
                
            } catch (error) {
                console.error('Verification error:', error);
                updateStatus('❌ خطأ في التحقق', 'error');
            }
        }

        // ================ تسجيل الحضور ================
       async function markAttendance() {
    try {
        showLoading('جاري تسجيل الحضور...');
        
        // التحقق من البصمة المحفوظة
        const hasFace = localStorage.getItem('face_registered') === 'true';
        if (!hasFace) {
            throw new Error('❌ يرجى تسجيل بصمة الوجه أولاً');
        }
        
        // التحقق من الوجه الحالي
        if (!currentFaceDescriptor) {
            throw new Error('❌ لم يتم اكتشاف وجه');
        }
        
        // حساب التشابه مع البصمة المخزنة
        const savedEncoding = JSON.parse(localStorage.getItem('face_encoding'));
        const storedDesc = new Float32Array(savedEncoding);
        
        const distance = faceapi.euclideanDistance(currentFaceDescriptor, storedDesc);
        const similarity = Math.max(0, 100 - (distance * 100));
        
        if (similarity < 70) {
            throw new Error(`❌ الوجه غير مطابق (${similarity.toFixed(1)}%)`);
        }
        
        // إرسال طلب الحضور (بدون بيانات الوجه)
        const response = await fetch('/attendance/browser', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                verified: true,
                similarity: similarity
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('success', `✅ ${data.message} - ${data.time}`);
            updateStatus(`✅ ${data.type === 'check_in' ? 'حضور' : 'انصراف'} مسجل`, 'success');
            
            // إعادة التوجيه بعد 2 ثانية
            setTimeout(() => {
                window.location.href = '/home';
            }, 2000);
            
        } else {
            throw new Error(data.message);
        }
        
    } catch (error) {
        console.error('Attendance error:', error);
        showAlert('error', error.message);
        updateStatus('❌ فشل تسجيل الحضور', 'error');
    } finally {
        hideLoading();
    }
}

        // ================ دوال مساعدة ================
        function showLoading(message) {
            loadingDiv.style.display = 'block';
            loadingDiv.innerHTML = `
                <div class="spinner"></div>
                <p>${message}</p>
            `;
        }

        function hideLoading() {
            loadingDiv.style.display = 'none';
        }

        function updateStatus(message, type = 'info') {
            statusMessage.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'times-circle' : 'info-circle'}"></i>
                ${message}
            `;
            statusMessage.className = `status status-${type}`;
        }

        function updateProgress(percent) {
            progressFill.style.width = `${percent}%`;
        }

        function showAlert(type, message) {
            const alert = type === 'success' ? successAlert : errorAlert;
            alert.innerHTML = message;
            alert.style.display = 'block';
            
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }

        // ================ إضافة Event Listeners ================
        startCameraBtn.addEventListener('click', startCamera);
        detectFacesBtn.addEventListener('click', detectFaces);
        registerFaceBtn.addEventListener('click', registerFace);
        verifyFaceBtn.addEventListener('click', verifyFace);
        testAttendanceBtn.addEventListener('click', markAttendance);

        // ================ بدء التحميل عند فتح الصفحة ================
        window.addEventListener('DOMContentLoaded', () => {
            // تحميل النماذج تلقائياً
            loadModels();
            
            // التحقق من البصمات المحفوظة
            const savedEncoding = localStorage.getItem('face_encoding');
            if (savedEncoding) {
                encodingStatus.innerHTML = '✅';
                storedFaceDescriptor = new Float32Array(JSON.parse(savedEncoding));
                updateStatus('✅ تم تحميل بصمة وجه مسجلة مسبقاً', 'success');
            }
        });

        // ================ تنظيف عند مغادرة الصفحة ================
        window.addEventListener('beforeunload', () => {
            if (faceDetectionInterval) {
                clearInterval(faceDetectionInterval);
            }
            if (video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
            }
        });
    </script>
</body>
</html>