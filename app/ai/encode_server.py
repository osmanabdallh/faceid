from http.server import HTTPServer, BaseHTTPRequestHandler
import json
import traceback
import face_recognition
import numpy as np
import os
import base64
from PIL import Image
import io
import pickle

class FaceHandler(BaseHTTPRequestHandler):
    
    def _set_headers(self, status=200):
        self.send_response(status)
        self.send_header('Content-Type', 'application/json')
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type')
        self.end_headers()
    
    def do_OPTIONS(self):
        self._set_headers()
    
    def do_GET(self):
        if self.path == '/':
            self._set_headers()
            self.wfile.write(json.dumps({
                "status": "running",
                "message": "Face Recognition Server is running"
            }).encode())
        else:
            self._set_headers(404)
    
    def do_POST(self):
        if self.path == '/encode':
            self.handle_encode()
        elif self.path == '/compare':
            self.handle_compare()
        elif self.path == '/verify':
            self.handle_verify()
        else:
            self._set_headers(404)
    
    def handle_encode(self):
        """استخراج بصمة الوجه من الصورة"""
        try:
            content_length = int(self.headers.get('Content-Length', 0))
            if content_length == 0:
                self._set_headers(400)
                self.wfile.write(json.dumps({"error": "No data received"}).encode())
                return
            
            post_data = self.rfile.read(content_length).decode('utf-8')
            data = json.loads(post_data)
            
            image_data = data.get('image')
            user_id = data.get('user_id')
            
            if not image_data:
                self._set_headers(400)
                self.wfile.write(json.dumps({"error": "Image data required"}).encode())
                return
            
            # معالجة base64
            if 'base64,' in image_data:
                image_data = image_data.split('base64,')[1]
            
            # تحويل base64 إلى صورة
            image_bytes = base64.b64decode(image_data)
            image = Image.open(io.BytesIO(image_bytes)).convert('RGB')
            
            # حفظ مؤقت للصورة
            temp_path = f"temp_{user_id}.jpg"
            image.save(temp_path)
            
            # استخراج الوجه
            loaded_image = face_recognition.load_image_file(temp_path)
            face_encodings = face_recognition.face_encodings(loaded_image)
            
            if len(face_encodings) == 0:
                self._set_headers(400)
                self.wfile.write(json.dumps({
                    "error": "No face detected in image",
                    "success": False
                }).encode())
                os.remove(temp_path)
                return
            
            if len(face_encodings) > 1:
                self._set_headers(400)
                self.wfile.write(json.dumps({
                    "error": "Multiple faces detected",
                    "success": False
                }).encode())
                os.remove(temp_path)
                return
            
            # الحصول على البصمة
            encoding = face_encodings[0].tolist()
            
            # تنظيف الملف المؤقت
            os.remove(temp_path)
            
            response_data = {
                "success": True,
                "encoding": encoding,
                "user_id": user_id,
                "message": "Face encoding generated successfully"
            }
            
            self._set_headers(200)
            self.wfile.write(json.dumps(response_data).encode())
            
        except Exception as e:
            print(f"❌ Encoding Error: {str(e)}")
            self._set_headers(500)
            self.wfile.write(json.dumps({
                "error": str(e),
                "success": False
            }).encode())
    
    def handle_verify(self):
        """مقارنة وجه مع وجه مخزن"""
        try:
            content_length = int(self.headers.get('Content-Length', 0))
            post_data = self.rfile.read(content_length).decode('utf-8')
            data = json.loads(post_data)
            
            current_image = data.get('image')
            stored_encoding = data.get('stored_encoding')
            
            if not current_image or not stored_encoding:
                self._set_headers(400)
                self.wfile.write(json.dumps({"error": "Missing data"}).encode())
                return
            
            # تحويل base64 إلى صورة
            if 'base64,' in current_image:
                current_image = current_image.split('base64,')[1]
            
            image_bytes = base64.b64decode(current_image)
            image = Image.open(io.BytesIO(image_bytes)).convert('RGB')
            
            # حفظ مؤقت
            temp_path = "temp_verify.jpg"
            image.save(temp_path)
            
            # استخراج الوجه من الصورة الحالية
            loaded_image = face_recognition.load_image_file(temp_path)
            face_encodings = face_recognition.face_encodings(loaded_image)
            
            if len(face_encodings) == 0:
                os.remove(temp_path)
                self._set_headers(400)
                self.wfile.write(json.dumps({
                    "match": False,
                    "error": "No face detected",
                    "similarity": 0
                }).encode())
                return
            
            current_encoding = face_encodings[0]
            stored_encoding_array = np.array(stored_encoding)
            
            # حساب التشابه
            face_distances = face_recognition.face_distance([stored_encoding_array], current_encoding)
            similarity = 1 - face_distances[0]
            
            # عتبة المطابقة (يمكن تعديلها)
            match = similarity > 0.6
            
            os.remove(temp_path)
            
            response_data = {
                "match": bool(match),
                "similarity": float(similarity),
                "threshold": 0.6,
                "distance": float(face_distances[0])
            }
            
            self._set_headers(200)
            self.wfile.write(json.dumps(response_data).encode())
            
        except Exception as e:
            print(f"❌ Verification Error: {str(e)}")
            self._set_headers(500)
            self.wfile.write(json.dumps({
                "error": str(e),
                "match": False
            }).encode())

def run_server():
    port = 8000
    server_address = ('0.0.0.0', port)
    
    print("🔧 Installing required libraries if needed...")
    print("📚 Make sure you have: face_recognition, numpy, Pillow")
    print("\n" + "="*50)
    
    httpd = HTTPServer(server_address, FaceHandler)
    print(f"🚀 Face Recognition Server started!")
    print(f"📍 Local: http://127.0.0.1:{port}")
    print(f"🌐 Network: http://localhost:{port}")
    print(f"🔌 Endpoints: /encode, /verify, /compare")
    print("="*50)
    print("\n📝 Make sure to install required packages:")
    print("pip install face_recognition numpy pillow")
    print("\nPress Ctrl+C to stop the server\n")
    
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        print("\n👋 Server stopped by user")
    finally:
        httpd.server_close()

if __name__ == '__main__':
    run_server()