let us=M.v_s("diypos")
let head="ตั้งค่า ให้ติดต่อกับอุปกรณ์ในเครือข่ายได้ Local Area Network (LAN) "
let msg=""
let img=[
	"pos_windows_v0.6_alowc1.png",
		"ทำตามรูป",
	"pos_windows_v0.6_alowc2.png",
		"เลือกตามรูป",
]

M.blockHead(head)
M.blockStep(
	head,
	`ทำตามภาพได้เลย ถ้าไม่ทำสิ่งนี้ จะไม่สามารถใช้งานผ่านอุปกรณ์อื่นได้ อุปกรณ์อื่นๆ ในวง LAN ไม่สามารถเข้าเว็บได้ โปรแกรม DIY POS ได้ `,
	[img[0],img[1],img[2],img[3]]
)
M.sticky()
