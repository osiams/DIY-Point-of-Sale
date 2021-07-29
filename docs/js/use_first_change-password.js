let head="เปลี่ยนรหัสผ่านใหม่สำหรับผู้ดูแลระบบ"
let msg=""
let img=[
	"use_first_change-password_01.png",
		"ตามรูปเลย",
	"use_first_change-password_02.png",
		"ตามรูปเลย",
	"use_first_change-password_03.png",
		"ตามรูปเลย",
]
//let ipr=M.v_s("192.168.1.1")

M.blockHead(head)
M.blockStep(
	head,
	`ทำตามรูปได้เลย `,
	[img[0],img[1],img[2],img[3],img[4],img[5]]
)
M.sticky()
