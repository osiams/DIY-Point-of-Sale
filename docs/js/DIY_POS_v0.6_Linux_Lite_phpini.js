let head="ตั้งค่า php.ini"
let msg=""
let pic=[]


M.blockHead(head)
M.blockStep(
	"ตั้งค่า ให้แสดงผลข้อผิดพลาด",
	`เปิดไฟล์ /opt/lampp/etc/php.ini  หาคำว่า "display_errors=" ประมาณบรรทัดที่ 494 ให้แก้เป็น
	<code class="block">display_errors=On</code>
	<p class="desc">ตั้งค่านี้เพื่อให้เว็บเพจ แสดงข้อผิดพลาดออกมา ถ้ามีการผิดพลาดของ source code เว็บแอปพลิเคชัน DIY POS</p>
	`,
	[]
)
M.sticky()
