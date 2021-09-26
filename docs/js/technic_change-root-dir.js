let head=M.ob_page[M.s_page].nt
let msg=""
let img=[
]
//let ipr=M.v_s("192.168.1.1")

M.blockHead(head)
M.blockStep(
	head,
	`ปกติ 
	<br>(Windows) root directory ของ XAMPP จะอยู่ที่ <br>C:\\xampp\\htdocs 
	<br>ให้แก้ที่ไฟล์ <br>C:\\xampp\\apache\\conf\httpd.conf <br>และ <br>C:\\xampp\\apache\\conf\\extra\\httpd-ssl.conf
	<br>
	<br><br>(Linux Lite) root directory ของ XAMPP จะอยู่ที่ <br>/opt/lampp/htdocs 
	<br>ให้แก้ที่ไฟล์ <br>/opt/lampp/etc/httpd.conf <br>และ <br>/opt/lampp/etc/extra/httpd-ssl.conf
	
	<br><br>ให้หาคำว่า DocumentRoot แล้วก็เปลี่ยนค่า
	`,
	[]
)
M.sticky()
