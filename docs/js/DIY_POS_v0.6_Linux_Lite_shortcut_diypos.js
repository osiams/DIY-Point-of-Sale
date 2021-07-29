let head="ทำ Shortcut เปิด DIY POS Web Application"
let msg=""
let img=[
	"pos_linux_lite_v0.6_shortcut_diypos1.png",
		"ทำตามรูปเลย",
	"pos_linux_lite_v0.6_shortcut_diypos2.png",
		"กรอกเลือกตามรูปเลย<br>ตัวเลือก Command<br>เปิดด้วย firefox"+M.tm_s("firefox https://localhost/diypos-0.6")+"<br>เปิดด้วย opera"+M.tm_s("opera https://localhost/diypos-0.6")+"<br>เปิดด้วย Google Chrome"+M.tm_s("google-chrome https://localhost/diypos-0.6")+" <br>ถ้าไม่ต้องการเข้ทาง https:// ก็เข้าทาง http:// ธรรมดาได้เหมือนกัน",
	"pos_linux_lite_v0.6_shortcut_diypos3.png",
		"สำเร็จ",
]


M.blockHead(head)
M.blockStep(
	head,
	`ทำตามรูปได้เลย`,
	[img[0],img[1],img[2],img[3],img[4],img[5]]
)
M.sticky()
