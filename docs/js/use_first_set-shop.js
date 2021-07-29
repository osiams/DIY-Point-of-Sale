let head=M.ob_page[M.s_page].nt
let msg=""
let img=[
	"use_first_set-shop_01.png",
		"ประมาณนี้เลย",
	"use_first_set-shop_02.png",
		"ประมาณนี้เลย",
]


M.blockHead(head)
M.blockStep(
	head,
	`แฟ้ม source code ของ DIY POS จะมีแฟ้ม ชื่อ set ในแฟ้มจะมีไฟล์ shop.json ให้แก้ไขที่ไฟล์นี้ จะเป็นข้อมูลที่แสดงหน้า Log in <br />ห้ามกด Enter ขึ้นบรรทดใหม่<br />ถ้าข้อความมี \\ และหรือ \" <br />ให้เพิ่ม \\  <br />\\ เป็น \\\\<br />\" เป็น \\\" `,
	[img[0],img[1],img[2],img[3]]
)
M.sticky()
