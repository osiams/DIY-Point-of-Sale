let head=M.ob_page[M.s_page].nt
let msg=""
let img=[
	"use_first_set-bill_03.webp",
		"ประมาณนี้เลย",
]


M.blockHead(head)
M.blockStep(
	head,
	`แฟ้ม source code ของ DIY POS จะมีแฟ้ม ชื่อ set ในแฟ้มจะมีไฟล์ receipt.json ให้แก้ไขที่ไฟล์นี้ 
	จะเป็นข้อมูลที่แสดงหน้า ใบเสร็จ <br />ห้ามกด Enter ขึ้นบรรทดใหม่<br />ถ้าข้อความมี \\ และหรือ \" <br />ให้เพิ่ม \\  <br />\\ เป็น \\\\<br />\" เป็น \\\" 
	<img src="img/use_first_set-bill_d01.png" onclick="M.viewImg(this)"/>
	<code class="block">
	<p>${M.v_s("logo")} คือ แสดงโลโก้ที่หัวใบเสร็จหรือไม่ 0=ไม่แสดง ,1=แสดง รูปโลโก้ใบเสร็จอยู่ที่ img/logo.png ต้องใช้ชื่อนี้เท่านันเป็นสีขาว ดำ กว้างไม่เกิน 384px</p>
	<p>${M.v_s("head")} คือ ข้อความส่วนหัวใบเสร็จ</p>
	<p>${M.v_s("name")} คือ ชื่อใบเสร็จ</p>
	<p>${M.v_s("foot")} คือ  ข้อความท้ายใบเสร็จ</p>
	</code>
	<p>*${M.v_s("head")},${M.v_s("name")},${M.v_s("foot")} จะไม่ตัดคำให้ ถ้าต้องการขึ้นบรรทัดใหม่ให้ใช้ ${M.v_s("\\n")}</p>`,
	[img[0],img[1]]
)
M.sticky()
