"""
🎯 سيرفر بايثون وهمي للتعرف على الوجه - لا يحتاج تثبيت مكتبات
تشغيل: python mock_python_server.py
"""
from http.server import HTTPServer, BaseHTTPRequestHandler
import json
import random
import time

class MockFaceHandler(BaseHTTPRequestHandler):
    
    def _set_headers(self, status=200):
        """تعيين رؤوس الرد"""
        self.send_response(status)
        self.send_header('Content-Type', 'application/json')
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type')
        self.end_headers()
    
    def do_OPTIONS(self):
        """معالجة طلبات CORS"""
        self._set_headers()
    
    def do_GET(self):
        """اختبار إذا كان السيرفر يعمل"""
        if self.path == '/':
            self._set_headers()
            self.wfile.write(json.dumps({
                "status": "running",
                "message": "Mock Face Recognition Server",
                "version": "1.0",
                "note": "This is a mock server for testing"
            }).encode())
    
    def do_POST(self):
        """معالجة طلبات POST"""
        if self.path == '/encode':
            self.handle_encode()
        elif self.path == '/verify':
            self.handle_verify()
        elif self.path == '/compare':
            self.handle_compare()
        else:
            self._set_headers(404)
            self.wfile.write(json.dumps({"error": "Endpoint not found"}).encode())
    
    def handle_encode(self):
        """استخراج بصمة وجه وهمية"""
        try:
            # قراءة البيانات
            content_length = int(self.headers.get('Content-Length', 0))
            post_data = self.rfile.read(content_length)
            
            # محاكاة وقت المعالجة
            time.sleep(0.5)
            
            # توليد encoding وهمي (128 رقم)
            encoding = [round(random.uniform(-1.0, 1.0), 6) for _ in range(128)]
            
            # سجل الاستلام
            print(f"✅ [ENCODE] تم استلام طلب استخراج بصمة")
            
            response = {
                "success": True,
                "encoding": encoding,
                "message": "تم توليد بصمة الوجه بنجاح (نظام تجريبي)",
                "warning": "This is mock data for testing only"
            }
            
            self._set_headers(200)
            self.wfile.write(json.dumps(response).encode())
            
        except Exception as e:
            print(f"❌ [ENCODE] Error: {e}")
            self._set_headers(500)
            self.wfile.write(json.dumps({"error": str(e)}).encode())
    
    def handle_verify(self):
        """مقارنة وجه مع وجه مخزن"""
        try:
            content_length = int(self.headers.get('Content-Length', 0))
            post_data = self.rfile.read(content_length)
            
            # محاكاة وقت المعالجة
            time.sleep(0.5)
            
            # نسبة نجاح 90% (لتجربة النظام)
            is_match = random.random() > 0.1  # 90% نجاح
            similarity = round(random.uniform(0.85, 0.99), 4) if is_match else round(random.uniform(0.1, 0.6), 4)
            
            print(f"✅ [VERIFY] تمت المقارنة - النتيجة: {'مطابق' if is_match else 'غير مطابق'}")
            
            response = {
                "match": is_match,
                "similarity": similarity,
                "message": "الوجه مطابق" if is_match else "الوجه غير مطابق",
                "note": "هذه نتائج تجريبية للنظام"
            }
            
            self._set_headers(200)
            self.wfile.write(json.dumps(response).encode())
            
        except Exception as e:
            print(f"❌ [VERIFY] Error: {e}")
            self._set_headers(500)
            self.wfile.write(json.dumps({"error": str(e)}).encode())
    
    def handle_compare(self):
        """مقارنة وجهين (للحضور)"""
        try:
            content_length = int(self.headers.get('Content-Length', 0))
            post_data = self.rfile.read(content_length)
            
            time.sleep(0.5)
            
            # دائمًا نجاح للاختبار
            is_match = True
            similarity = round(random.uniform(0.92, 0.98), 4)
            
            print(f"✅ [COMPARE] تم التحقق للمستخدم - النتيجة: مطابق")
            
            response = {
                "match": is_match,
                "similarity": similarity,
                "user_id": 1,  # إرجاع user_id افتراضي
                "message": "تم التحقق من الهوية بنجاح",
                "timestamp": time.strftime("%Y-%m-%d %H:%M:%S")
            }
            
            self._set_headers(200)
            self.wfile.write(json.dumps(response).encode())
            
        except Exception as e:
            print(f"❌ [COMPARE] Error: {e}")
            self._set_headers(500)
            self.wfile.write(json.dumps({"error": str(e)}).encode())

def run_server():
    """تشغيل السيرفر"""
    port = 8000
    host = '0.0.0.0'
    
    print("=" * 60)
    print("🚀 **Mock Face Recognition Server**")
    print("=" * 60)
    print(f"📍 العنوان: http://127.0.0.1:{port}")
    print(f"🌐 الشبكة: http://localhost:{port}")
    print("=" * 60)
    print("\n📡 **Endpoints المتاحة:**")
    print("  POST /encode  - استخراج بصمة وجه (إرجاع 128 رقم)")
    print("  POST /verify  - مقارنة وجهين")
    print("  POST /compare - التحقق للمستخدم (للحضور)")
    print("  GET  /        - اختبار السيرفر")
    print("\n⚠️  **ملاحظة:** هذا سيرفر تجريبي يعيد بيانات وهمية")
    print("=" * 60)
    print("\n✅ السيرفر يعمل... اضغط Ctrl+C لإيقافه\n")
    
    server = HTTPServer((host, port), MockFaceHandler)
    
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        print("\n👋 تم إيقاف السيرفر")
    finally:
        server.server_close()

if __name__ == '__main__':
    run_server()