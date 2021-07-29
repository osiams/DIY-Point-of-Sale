let us=M.v_s("diypos")
let head="ทำ Mysql Upgrade"
let msg=""
let img=[
	"pos_linux_lite_v0.6_mysqlupgrad1.png",
		"เข้าโดย root พิมพิ์"+M.tm_s("sudo -s")+"",
	"pos_linux_lite_v0.6_mysqlupgrad2.png",
		"เข้าใช้งาน ฐานข้อมูลด้วย  root พิมพิ์"+M.tm_s("/opt/lampp/bin/mysql_upgrade -u root -p")+"",
	"pos_linux_lite_v0.6_mysqlupgrad3.png",
		"กรอก รหัสผ่านของ root ในฐานข้อมูล",
	"pos_linux_lite_v0.6_mysqlupgrad4.png",
		"เสร็จแล้ว Restart MySQL Database ",
]

M.blockHead(head)
M.blockStep(
	head,
	`ทำตามภาพได้เลย ถ้าไม่ทำสิ่งนี้ จะไม่สามารถติดตั้ง DIY POS ได้`,
	[img[0],img[1],img[2],img[3],img[4],
		img[5],img[6],img[7]]
)
M.sticky()
