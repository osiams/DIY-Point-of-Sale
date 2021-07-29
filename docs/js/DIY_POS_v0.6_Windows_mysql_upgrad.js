let us=M.v_s("diypos")
let head="ทำ Mysql Upgrade"
let msg=""
let img=[
	"pos_windows_v0.6_mysqlupgrad1.png",
		"เข้า  cmd ตามรูป",
	"pos_windows_v0.6_mysqlupgrad2.png",
		"พิมพิ์"+M.tm_s("c:\\xampp\\mysql\\bin\\mysql_upgrade -u root -p")+"กด Enter ตามด้วย กรอกรหัสผ่านผู้ใช้ root ในฐานข้อมูล",
	"pos_windows_v0.6_mysqlupgrad3.png",
		"ผลสำเร็จ restart MySQL ที่ XAMPP Control Panel ใหม่ด้วย ถึงจะใช้ได้",
]

M.blockHead(head)
M.blockStep(
	head,
	`ทำตามภาพได้เลย ถ้าไม่ทำสิ่งนี้ จะไม่สามารถติดตั้ง DIY POS ได้`,
	[img[0],img[1],img[2],img[3],img[4],
		img[5]]
)
M.sticky()
