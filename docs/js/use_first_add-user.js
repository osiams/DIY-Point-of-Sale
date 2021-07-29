let head="เพิ่มผู้ใช้งาน DIY POS"
let msg=""
let img=[
	"use_first_add-user_01.png",
		"ประมาณนี้เลย",
	"use_first_add-user_03.png",
		"ประมาณนี้เลย",
	"use_first_add-user_02.png",
		"ประมาณนี้เลย",
]


M.blockHead(head)
M.blockStep(
	head,
	``,
	[img[0],img[1],img[2],img[3],img[4],img[5]]
)
M.sticky()
