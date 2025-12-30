from http.server import BaseHTTPRequestHandler, HTTPServer
import json

class FaceHandler(BaseHTTPRequestHandler):

    def do_POST(self):
        if self.path == "/compare":
            self.send_response(200)
            self.send_header("Content-Type", "application/json")
            self.end_headers()

            response = {
                "match": True,
                "distance": 0.3
            }

            self.wfile.write(json.dumps(response).encode())

server = HTTPServer(("127.0.0.1", 8000), FaceHandler)
print("Python AI Server running on http://127.0.0.1:8000")
server.serve_forever()
