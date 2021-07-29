let head=M.ob_page[M.s_page].nt
let msg=""
let img=[
	"technic_find-font-path-window_01.png",
		"เข้าไปที่ C:\\Windows\\Fonts",
	"technic_find-font-path-window_02.png",
		"จากรูปจะได้ที่อยู่ฟ้อนต์คือ<code class=\"block\">C:\\Windows\\Fonts\\tahomabd.ttf </code>",
]
//let ipr=M.v_s("192.168.1.1")

M.blockHead(head)
M.blockStep(
	head,
	`การหาที่อยู่เต็มของฟ้อนต์ในระบบ Windows 10 เพื่อนำมาอ้างอิง ใน ${M.link_s("ตั้งค่าเครื่องพิมพ์","?page=user_frist_set-printer")} 
	ต้องเป็นไฟล์ ${M.v_s(".ttf")} เท่านั้น
	<br />ทำตามภาพได้เลย
	`,
	[img[0],img[1],img[2],img[3]]
)
M.sticky()
