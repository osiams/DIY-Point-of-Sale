let head="ทำ port ให้เป็นชื่อ คงที่"
let msg=""
let pic=[
	"DIY_POS_v0.6_Linux_Lite_sttport1.png",
		"ช่องเสียบ",
	"DIY_POS_v0.6_Linux_Lite_sttport2.png",
		"สังเกตกรอบสีเหลือง และสีแดง จะนำมาใช้",
	"DIY_POS_v0.6_Linux_Lite_sttport3.png",
		"ชื่อที่ได้ตั้งไว้จะแสดงเมื่อเสียบเครื่องพิมพิ์ "

]
let fm=M.v_s("/etc/udev/rules.d/usb_.rules")

M.blockHead(head)
M.blockStep(
	`สร้างไฟล์ กำหนดค่า`,
	`เป็นการกำหนด หรือตั้งชื่อ พอร์ต ให่กับเครื่องคอมพิวเตอร์ 
	<br>เปิดไฟล์  ${M.v_s(fm)} ถ้าไม่มีก็สร้าง ของผมตั้งชื่อไว้ว่า usb_.rules หรือชื่ออื่น ก็ได้`,
	[pic[0],pic[1]]
)
M.blockStep(
	`เตรียม สำรวจช่องเสียบ`,
	`นำสายเครื่องพิม มาเสียบกับ พอร์ต ตรวจสอบว่ามันเชื่อมต่อได้จริงหรือไม่ ให้เปิด เทอร์มินัล พิมพ์
	<code class="block">ls /dev/ttyUSB*</code>
	หรือ
	<code class="block">ls /dev/usb/lp*</code>
	คำสั่งทั้ง 2 อัน มันจะมี คำสั่งหนึ่งที่แสดงเป็น /dev/ttyUSB0 หรือ /dev/usb/lp0
	`,
	[]
)
M.blockStep(
	`ตรวจสอบรายละเอียดของพอร์ต`,
	`พิมพ์คำสั่งที่ เทอร์มินัล 
	<code class="block">udevadm info --name=/dev/ttyUSB0 --attribute-walk</code>
	ถ้าขึ้นว่า device node not found ให้พิมพ์
	<code class="block">udevadm info --name=/dev/usb/lp0 --attribute-walk</code>
	จะได้ตามภาพข้างล่าง
	`,
	[pic[2],pic[3]]
)
M.blockStep(
	`นำข้อมูลมากรอก`,
	`เปิดไฟล? ${M.v_s(fm)} 
	<br>ให้กรอกตามรูปแบบนี้
	<code class="block">KERNEL=="ttyUSB*", KERNELS=="1-11:1.0", SYMLINK+="usb_1"</code>
	KERNEL=="ttyUSB*" คือ กรอบสีเหลือง ให้เปลี่ยนเลข 0 เป็น *
	<br>KERNELS=="1-11:1.0" คือ กรอบสีแดง แต่ละช่องเสียบค่านี้จะไม่เหมือนกัน
	<br>SYMLINK+="usb_1" กำหนดชื่อพอร์ต เป็น usb_1
	<br>ทำแบบนี้ให้ครบทุกช่อง หรือตามที่ต้องการ
	<br>เสร็จแล้วก็จะได้ประมาณนี้ แต่ละท่านขะได้ไม่เหมือนกันนะครับ แล้วแต่คอมของใคร
	<code class="block">KERNEL=="ttyUSB*", KERNELS=="3-4:1.0", SYMLINK+="usb_1"
	KERNEL=="ttyUSB*", KERNELS=="3-2:1.0", SYMLINK+="usb_2"
	KERNEL=="ttyUSB*", KERNELS=="1-11:1.0", SYMLINK+="usb_3"
	KERNEL=="ttyUSB*", KERNELS=="1-14:1.0", SYMLINK+="usb_4"
	KERNEL=="ttyUSB*", KERNELS=="1-4:1.0", SYMLINK+="usb_5"
	KERNEL=="ttyUSB*", KERNELS=="1-3:1.0", SYMLINK+="usb_6"
	KERNEL=="ttyUSB*", KERNELS=="1-1:1.0", SYMLINK+="usb_7"
	KERNEL=="ttyUSB*", KERNELS=="1-2:1.0", SYMLINK+="usb_8"
	KERNEL=="lp*", KERNELS=="3-4:1.0", SYMLINK+="usb_1"
	KERNEL=="lp*", KERNELS=="3-2:1.0", SYMLINK+="usb_2"
	KERNEL=="lp*", KERNELS=="1-11:1.0", SYMLINK+="usb_3"
	KERNEL=="lp*", KERNELS=="1-14:1.0", SYMLINK+="usb_4"
	KERNEL=="lp*", KERNELS=="1-4:1.0", SYMLINK+="usb_5"
	KERNEL=="lp*", KERNELS=="1-3:1.0", SYMLINK+="usb_6"
	KERNEL=="lp*", KERNELS=="1-1:1.0", SYMLINK+="usb_7"
	KERNEL=="lp*", KERNELS=="1-2:1.0", SYMLINK+="usb_8"</code>
	เสร็จแล้วให้ รีสตาร์ทเครื่อง นำเครื่องพิมพิ์มาเสียบ แล้วเปิด เทอมินัล พิมพิ์
	<code class="block">ls /dev</code>
	`,
	[pic[4],pic[5]]
)
M.sticky()
M.blockRef([
	M.link_s("Assign a static USB port on Linux","https://msadowski.github.io/linux-static-port/")
])
