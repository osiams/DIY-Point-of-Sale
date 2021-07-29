let us=M.v_s("diypos")
let head="ตั้งค่า ให้ติดต่อกับอุปกรณ์ในเครือข่ายได้ Local Area Network (LAN) "
let msg=""
let img=[
	"pos_linux_lite_v0.6_alowc1.png",
		"เลือกตามรูป",
	"pos_linux_lite_v0.6_alowc2.png",
		"เลือกตามรูป",
	"pos_linux_lite_v0.6_alowc3.png",
		"เลือกตามรูป",
	"pos_linux_lite_v0.6_alowc4.png",
		"เลือกตามรูป",
	"pos_linux_lite_v0.6_alowc5.png",
		"เลือกตามรูป",
	"pos_linux_lite_v0.6_alowc6.png",
		"เลือกตามรูป",
	"pos_linux_lite_v0.6_alowc7.png",
		"เลือกตามรูป",
	"pos_linux_lite_v0.6_alowc8.png",
		"เลือกตามรูป",
	"pos_linux_lite_v0.6_alowc9.png",
		"เลือกตามรูป",
	"pos_linux_lite_v0.6_alowc10.png",
		"",
	"pos_linux_lite_v0.6_alowc11.png",
		"เลือกตามรูป จบ",
]

M.blockHead(head)
M.blockStep(
	head,
	`ทำตามภาพได้เลย ถ้าไม่ทำสิ่งนี้ จะไม่สามารถใช้งานผ่านอุปกรณ์อื่นได้`,
	[img[0],img[1],img[2],img[3],img[4],
		img[5],img[6],img[7],img[8],img[9],img[10],img[11],img[12],
		img[13],img[14],img[15],img[16],img[17],img[18],img[19],img[20],img[21]]
)
M.sticky()
