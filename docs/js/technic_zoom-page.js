let head=M.ob_page[M.s_page].nt
let msg=""
let img=[
]
//let ipr=M.v_s("192.168.1.1")

M.blockHead(head)
M.blockStep(
	head,
	`ใช้กับเครื่อง PC โดยกด ปุ่ม ctrl + จะขยาย ,ctrl - จะย่อ
	<img src="img/technic_zoom-page.png" onclick="M.viewImg(this)"/>
	`,
	[]
)
M.sticky()
