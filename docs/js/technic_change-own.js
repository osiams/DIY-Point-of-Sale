let head=M.ob_page[M.s_page].nt
let msg=""
let img=[
]
//let ipr=M.v_s("192.168.1.1")

M.blockHead(head)
M.blockStep(
	head,
	`เปลี่ยนเจ้าของไฟล์ ใน Linux \${user} 
		<br />\${user}  คือชื่อผู้ใช้
${M.tm_s("su ")}
กด Enter กรอกรหัส แล้วตามด้วย
${M.tm_s("chown  -R ${user} /opt/lampp/htdocs")}
	`,
	[]
)
M.sticky()
