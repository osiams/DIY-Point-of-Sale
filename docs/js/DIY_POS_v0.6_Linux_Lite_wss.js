let head="ตั้งค่า ws:// และ wss:// ให้ Server"
let msg=""
let pic=[]


M.blockHead(head)
M.blockStep(
	"ตั้งค่า ไฟล์ httpd.conf",
	`เปิดไฟล์ /opt/lampp/etc/httpd.conf เพิ่ม 
	<code class="block">LoadModule proxy_wstunnel_module modules/mod_proxy_wstunnel.so
	<br>ProxyPass \"/websocket/\" \"ws://localhost:9000/\"</code>
	ไว้ที่บรรทัดสุดท้าย แล้วบันทึก
	<br><br><p class="desc">การตั้งค่านี้ ใช้ในการเชื่อมต่อ จอแสดงผลสำหรับลูกค้า ด้วย WebSocket ที่ใช้งานด้วย ws:// และ wss://</a>`,
	[]
)
M.sticky()
