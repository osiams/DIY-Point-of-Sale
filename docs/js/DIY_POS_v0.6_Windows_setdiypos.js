let head="ติดตั้ง โปรแกรม DIY POS (Web Application)"
let msg=""
let img=[
	"pos_windows_v0.6_setdiypos1.png",
		"ไฟล์ที่ ดาวโหลดได้มา ให้คลิกขวาที่ไฟล์ เลือก Extract All... แล้วให้เปิดปฟ้มที่ไดมาจะมีแฟ้มข้างในอีกที",
	"pos_windows_v0.6_setdiypos2.png",
		"เปลี่ยนชื่อแฟ้มให้ตรงตาม version  ตัวอย่างเปลี่ยนเป็น diypos-0.6 แล้ว Coppy (ใช้ชื่ออื่นก็ได้ แต่วิธีทำด้านล่างต้องเทียบเคียงกับตัวอย่าง)",
	"pos_windows_v0.6_setdiypos3.png",
		"นำไปไว้ที่  C:\\xampp\\htdocs จะได้ดังรูป",
	"pos_linux_lite_v0.6_setdiypos4.png",
		"เข้าไปที่ /img จะมีแฟ้มชื่อ gallery ให้คลิ๊กขวาที่แฟ้มเลือก Properties แล้วเลือก Permissions ตรง Others ให้เปลี่ยนเป็น Read & Write ถ้าไม่ตั้งค่านี้ จะไม่สามารถนำรูปมาเก็บที่แฟ้ม gallery ได้",
	"pos_linux_lite_v0.6_setdiypos5.png",
		`อันดับแรกไปที่ไฟล์ config.php แก้<br>	"server"=>"127.0.0.1",
		<br>"database"=>"diypos_0.6", //(ชื่ออื่นก็ได้)",
		<br>"user\"=>"diypos",	//ผู้ใช้ในฐานข้อมูลจากขั้นตอน ${M.link_s("เพิ่มผู้ใช้ ในฐานข้อมูล","?page=pos__0.6__lnl__adusr_____")}
		<br>*โปรดแก้ดวยความระมัดระวัง`,
	"pos_linux_lite_v0.6_setdiypos6.png",
		"เปิดเว็บบราวเซอร์ เข้าไปที่ <code class=\"block\">localhost/diypos-0.6</code>จากตัวอย่าง ผมตั้งชื่อแฟ้มใน htdocs คือ  \"diypos-0.6\"  ถ้าใครตั้งชื่ออื่นก็ใช้ชื่อนั้น",
	"pos_linux_lite_v0.6_setdiypos7.png",
		"จะพบ หน้าอ่านฉัน และเมื่อ ติ๊กถูก ก็จะเห็นดังภาพ ข้อมูลที่แสดงมาคือ ค่าที่มีอยู่ในไฟล์ config.php ให้กด ติดตั้ง",
	"pos_linux_lite_v0.6_setdiypos8.png",
		"จะเป็นการ Table ,Routines ,Triggers ให้กับฐานข้อมูล",
	"pos_linux_lite_v0.6_setdiypos9.png",
		"ติดตั้งข้อมูลตัวอย่าง (ไม่มีใน v0.6)",
	"pos_linux_lite_v0.6_setdiypos10.png",
		"เกือบสำเร็จ ให้แก้ไข้ไฟล์ตามลำดับดังนี้",
	"pos_windows_v0.6_setdiypos11.png",
		"เปลียนชื่อไฟล์ ตามลำดับดังนี้<code class=\"block\">1. ไฟล์ index.php แก้เป็น index.php.back หรือชื่ออื่น<br>2. ไฟล์ index แก้เป็น index.php เท่านั้น</code>",
	"pos_linux_lite_v0.6_setdiypos12.png",
		"โหลดหน้าใหม่ หรือกดที่ หน้าหลัก หรือเข้าไปที่ localhost/diypos-0.6 จะได้หน้าเข้าสู่ระบบ",
	"pos_linux_lite_v0.6_setdiypos13.png",
		"เมื่อเข้ามาจะได้หน้าดังนี้ ก็ทำตามโดย ลงทะเบียนลิ้นชัก และเครื่อง POS ",
	"pos_linux_lite_v0.6_setdiypos14.png",
		"หน้า DIY POS Web Application",
]



M.blockHead(head)
M.blockStep(
	"ดาวโหลด source code โปรแกรม DIY POS (Web Application)",
	`ไปที่ ${M.link_s("https://github.com/osiams/DIY-Point-of-Sale/releases","https://github.com/osiams/DIY-Point-of-Sale/releases")}`,
	[]
)
M.blockStep(
	"ย้ายไฟล์ไปไว้ใน htdocs",
	`ทำตามรูปตัวอย่างได้เลย`,
	[img[0],img[1],img[2],img[3],img[4],
		img[5]
	]
)
M.blockStep(
	"ติดตั้ง DIY POS",
	`ทำตามรูปตัวอย่างได้เลย`,
	[img[8],img[9],
		img[10],img[11],img[12],img[13],img[14],img[15],
		img[16],img[17],img[18],img[19],img[20],img[21],img[22],img[23],img[24],img[25],img[26],img[27]
	]
)
M.sticky()
