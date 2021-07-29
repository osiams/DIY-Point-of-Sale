let head="ทำ Shortcut เปิด XAMPP Control Panel"
let msg=""
let img=[
	"pos_linux_lite_v0.6_shortcut_xampp1.png",
		"ทำตามรูปเลย",
	"pos_linux_lite_v0.6_shortcut_xampp2.png",
		"กรอกเลือกตามรูปเลย"+M.tm_s("sudo /opt/lampp/manager-linux-x64.run")+"",
]


M.blockHead(head)
M.blockStep(
	head,
	`ทำตามรูปได้เลย`,
	[img[0],img[1],img[2],img[3]]
)
M.sticky()
