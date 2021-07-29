let head="ทำ Shortcut เปิด แฟ้ม source code DIY POS Web Application"
let msg=""
let img=[
	"pos_linux_lite_v0.6_shortcut_filediypos2.png",
		"ทำตามรูปเลย",
	"pos_linux_lite_v0.6_shortcut_filediypos3.png",
		"กรอกเลือกตามรูปเลย<br>ตัวเลือก Command"+M.tm_s("xdg-open /opt/lampp/htdocs/diypos-0.6")+"",
	"pos_linux_lite_v0.6_shortcut_filediypos4.png",
		"สำเร็จ",
]


M.blockHead(head)
M.blockStep(
	head,
	`ทำตามรูปได้เลย`,
	[img[0],img[1],img[2],img[3],img[4],img[5]]
)
M.sticky()
