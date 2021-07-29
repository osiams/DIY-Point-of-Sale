let head="ตั้งค่า php.ini"
let msg=""
let pic=[]


M.blockHead(head)
M.blockStep(
	"ตั้งค่า ให้แสดงผลข้อผิดพลาด",
	`เปิดไฟล์ C:\\xampp\\php\\php.ini  หาคำว่า "display_errors=" ประมาณบรรทัดที่ 494 ให้แก้เป็น 
	<code class="block">
	;extension=gd แก้เป็น extension=gd
	<br />;extension=intl  แก้เป็น extension=intl
	<br />;extension=sockets  แก้เป็น extension=sockets
	<br />display_errors=On</code>
	<p class="desc"></p>
	`,
	[]
)
M.sticky()
