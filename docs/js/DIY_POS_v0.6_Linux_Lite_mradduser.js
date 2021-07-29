let us=M.v_s("diypos")
let head="เพิ่มผู้ใช้ ในฐานข้อมูล"
let msg=""
let img=[
	"pos_linux_lite_v0.6_mrdbuser01.png",
		`เข้าไปที่ ${M.link_s(null,"http://localhost/phpmyadmin/")} เข้าสู่ระบบด้วย root`,
	"pos_linux_lite_v0.6_mrdbuser02.png",
		"กดเลือก Add user accounts ตามรูป",
	"pos_linux_lite_v0.6_mrdbuser03.png",
		"กำหนด user name เป็นภาษาอังกฤษ ตัวอย่างใช้ชื่อ "+M.v_s(us)+" หรือชื่ออื่นก็ได้ สวนค่าอืนๆกำหนดตามตัวอย่างได้เลย",
	"pos_linux_lite_v0.6_mrdbuser04.png",
		"เครื่องหมายตามรูปเลย แล้วกด go",
	"pos_linux_lite_v0.6_mrdbuser05.png",
		"เพิ่มผู้ใช้ "+M.v_s(us)+" สำเร็จ",
	"pos_linux_lite_v0.6_mrdbuser06.png",
		"ออกจาก phpMyadmin กดตามรูปเลย",
	"pos_linux_lite_v0.6_mrdb06.1.png",
		"Restart MySql Database",
	"pos_linux_lite_v0.6_mrdbuser07.png",
		"ลอง รีโหลด หน้าใหม่จะเข้า โดยต้องผู้ใช้ที่ไลงทะเบียนไว้ และกรอกรหัส จบแล้ว",
]


M.blockHead(head)
M.blockStep(
	head,
	`เปิดเว็บ บราวเซอร์ เข้าไปที่ ${M.link_s(null,"https://localhost/phpmyadmin/")} ให้เข้าระบบด้วย ${M.v_s("root")} 
	ตัวอย่างต่อไปนี้จะเพิ่มผู้ใช้ ${us} ไว้สำหรับติดต่อฐานข้อมูล MariaDB`,
	[img[0],img[1],img[2],img[3],img[4],
		img[5],img[6],img[7],img[8],img[9],
		img[10],img[11],img[12],img[13],img[14],img[15]]
)
M.sticky()
