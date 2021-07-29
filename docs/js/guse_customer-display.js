let head=M.ob_page[M.s_page].nt
let msg=""
let img=[
	"guse_customer-display_01.png",
		"ประมาณนี้เลย",
	"guse_customer-display_02.png",
		"กดปุ่มคัดลอก",
	"guse_customer-display_03.png",
		`สำหรับ Windows เปิด cmd แล้วนำมาวางแล้กด Enter
		<br><br>สำหรับ Linux เปิด Terminal แล้วนำมาวางแลวกด Enter
		<br><br>*ตลอดการใช้งานต้องเปิดตลอดเวลา
		`,
	"guse_customer-display_07.webp",
		"ประมาณนี้เลย",
	"guse_customer-display_08.webp",
		"ประมาณนี้เลย",
	"guse_customer-display_09.webp",
		"ประมาณนี้เลย",
	"guse_customer-display_10.webp",
		"ประมาณนี้เลย",
	"guse_customer-display_11.webp",
		"ประมาณนี้เลย",
	"guse_customer-display_12.webp",
		"ประมาณนี้เลย",
	"guse_customer-display_13.webp",
		"ประมาณนี้เลย",
	"guse_customer-display_14.webp",
		"ประมาณนี้เลย",
]


M.blockHead(head)
M.blockStep(
	"เตรียมอุปกรณ์",
	`เตรียมอุปกรณ์ ใน DIY POS จะใช้ โทรศัพท์มือถือ หรือ แท็บเลต มาทำหน้าจ้อสำหรับฝั่งลูกค้า
	`,
	[]
)
M.blockStep(
	"เปิด WebSockets",
	`ทำกับเครื่องที่ลง XAMPP (เครื่อง Server)  ตามภาพเลย
	`,
		[img[0],img[1],img[2],img[3],img[4],img[5]]
)
M.blockStep(
	"เปิด หน้าเว็บจอแสดงผล",
	`เข้าด้วยทาง https:// (จะมีหน้าเตือนให้กดยอมรับ) เปิด หน้าเว็บจอแสดงผล โดย Log in ในชื่อ system@diy.pos password 12345678 ด้วยอุปกร์ที่จะนำมาทำจอแสดงผลสำหรับลูกค้า แนะนำใช้ Opera หรือ Firefox จะสามาถทำให้เต็มหน้าจอได้
	`,
		[img[6],img[7],img[8],img[9],img[10],img[11],img[12],img[13],img[14],img[15]]
)
M.blockStep(
	"ทำ Icon ไว้ใช้งานเต็มจอ",
	`
	`,
		[img[16],img[17],img[18],img[19],img[20],img[21]]
)
M.sticky()
