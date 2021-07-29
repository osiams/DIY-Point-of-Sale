let head="ทำ Shortcut เปิด DIY POS Web Application"
let msg=""
let img=[
	"pos_windows_v0.6_shortcut_diypos1.png",
		"ทำตามรูปเลย สามารถเปลี่ยน Icon หรือข้อความ โดยการคลิ๊กขวาที่ shortcut ที่ได้ลากวางไว้",
]


M.blockHead(head)
M.blockStep(
	head,
	`ทำตามรูปได้เลย`,
	[img[0],img[1]]
)
M.sticky()
