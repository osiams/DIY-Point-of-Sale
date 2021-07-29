let head="กำหนดกลุ่มผู้ใช้ ให้เครื่องพิมพิ์"
let msg=""
let pic=[]


M.blockHead(head)
M.blockStep(
	head,
	`ใช้สำหรับเครื่องพิมพิ์ที่เชื่อมต่อด้วย USB
	<code class="block">sudo usermod -a -G dialout daemon</code>
	และหรือ</br>
	<code class="block">sudo usermod -a -G lp daemon</code>
	*<b>daemon</b> มาจาก /opt/lampp/etc/httpd.conf ประมาณบรรทัดที่ 174
	<br>แล้วรีสตาร์ทเครื่องใหม่`,
	[]
)
M.sticky()
M.blockRef([
	M.link_s("Getting a USB receipt printer working on Linux","https://mike42.me/blog/2015-03-getting-a-usb-receipt-printer-working-on-linux")
])
