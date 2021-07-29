let head="ตั้งรหัสผ่าน ให้ root ในฐานข้อมูล"
let msg=""
let img=[
	"pos_linux_lite_v0.6_mrdb01.png",
		`เข้าไปที่ ${M.link_s(null,"http://localhost/phpmyadmin/")}`,
	"pos_linux_lite_v0.6_mrdb02.png",
		"กดเลือก User accounts",
	"pos_linux_lite_v0.6_mrdb03.png",
		"กดเลือก Change password",
	"pos_linux_lite_v0.6_mrdb04.png",
		"ใส่รหัสผ่านตามต้องการ และต้องจำให้ได้ ถ้าวันหน้าจำไมาได้ ก็ไม่สามารถเข้าใช์ในฐานะ root ได้ แล้วกด go",
	"pos_linux_lite_v0.6_mrdb05.png",
		"รีเฟรชหน้าเว็บใหม่จะได้หน้าตาประมาณนี้",
	"pos_windows_v0.6_mrdb06.png",
		"เปิดไฟล์ C:\\xampp\\phpMyAdmin\\config.inc.php แก้ไข <br>$cfg['Servers'][$i]['auth_type'] = 'config';<br>เป็น<code class=\"block\">$cfg['Servers'][$i]['auth_type'] = 'cookie';</code> แล้วบันทึก",
	"pos_windows_v0.6_mrdb06.1.png",
		"Restart MySql Database",
	"pos_linux_lite_v0.6_mrdb07.png",
		"ลอง รีโหลด หน้าใหม่จะเข้า โดยต้องกรอกรหัส จบแล้ว",
]


M.blockHead(head)
M.blockStep(
	"ตั้งรหัสผ่าน ให้ root ในฐานข้อมูล",
	`เปิดเว็บ บราวเซอร์ เข้าไปที่ ${M.link_s(null,"https://localhost/phpmyadmin/")} จะเข้าได้อัตโนมัติ ซึ่งไม่ปลอดภัย วิธีทำตามภาพได้เลย`,
	[img[0],img[1],img[2],img[3],img[4],
		img[5],img[6],img[7],img[8],img[9],
		img[10],img[11],img[12],img[13],img[14],img[15]]
)
M.sticky()
