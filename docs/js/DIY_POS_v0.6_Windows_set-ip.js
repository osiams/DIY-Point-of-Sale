let head="ตั้งค่า IP เครื่อง ตามต้องการ Local Area Network (LAN)"
let msg=""
let img=[
	"pos_windows_v0.6_set-ip1.png",
		"ทำตามรูปเลย",
	"pos_windows_v0.6_set-ip2.png",
		"ทำตามรูปเลย",
	"pos_windows_v0.6_set-ip3.png",
		"ทำตามรูปเลย",
	"pos_windows_v0.6_set-ip4.png",
		"ทำตามรูปเลย",
	"pos_windows_v0.6_set-ip5.png",
		"ทำตามรูปเลย",
	"pos_windows_v0.6_set-ip6.png",
		"กด OK แล้วกด OK",
]
let ipr=M.v_s("192.168.1.1")

M.blockHead(head)
M.blockStep(
	head,
	`ทำตามรูปได้เลย ผมจะยกตัวอย่างคือ IP ของ เราเตอร์ (อังกฤษ: router) คือ ${ipr} โดยส่วนมากจะเป็น IP นี้นะครับ ผมจะต้องการเปลี่ยน IP ตามต้องการ ในที่นี้จะเปลี่ยนเป็น 192.168.1.100 หรือจะเปลี่ยนเป็น เลขอื่นก็ได้โดยเปลี่ยนแค่ .xxx สามตัวท้าย 0-255 เช่น 
	<br>192.168.1.150<br>192.168.1.10  <br><br>*ที่ไม่ใช่ <br>192.168.1.1<br>192.168.1.0 <br>192.168.1.255 )`,
	[img[0],img[1],img[2],img[3],img[4],img[5],img[6],img[7],img[8],img[9],img[10],img[11]]
)
M.sticky()
