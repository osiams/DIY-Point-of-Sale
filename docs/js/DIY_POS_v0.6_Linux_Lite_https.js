let mv=M.v_s("v1.4.3")
let xd=M.v_s("/home/sh/download")
let dc=M.v_s("/home/sh/เอกสาร/")
let img=[
	"pos_linux_lite_v0.6_https1.png",
		"เลือก สำหรับ Linux mkcert-v1.4.3-linux-amd64",
	"pos_linux_lite_v0.6_https2.png",
		"ไฟล์ที่ ดาวโหลด์มาได้",
	
	"pos_linux_lite_v0.6_https3.png",
		"ทำการอัปเดต ด้วย "+M.tm_s("sudo apt-get update")+"",
	"pos_linux_lite_v0.6_https4.png",
		"ต่อด้วย "+M.tm_s("sudo apt install wget libnss3-tools")+"",
	"pos_linux_lite_v0.6_https5.png",
		"ต่อด้วย "+M.tm_s("export VER=\""+M.v_s(mv)+"\"")+"",	
	"pos_linux_lite_v0.6_https6.png",
		"ต่อด้วย "+M.tm_s("wget -O mkcert https://github.com/FiloSottile/mkcert/releases/download/${VER}/mkcert-${VER}-linux-amd64"),		
	"pos_linux_lite_v0.6_https7.png",
		"ได้ผลลับดังรูป",
	"pos_linux_lite_v0.6_https8.png",
		"เปลี่ยนโหมดไฟล์ ด้วย"+M.tm_s("chmod +x mkcert"),
	"pos_linux_lite_v0.6_https10.png",
		"ต่อด้วย"+M.tm_s("sudo update-ca-certificates"),	
	"pos_linux_lite_v0.6_https9.png",
		"ไฟล์ mkcert  อยู่ที่ /usr/local/bin",		
	"pos_linux_lite_v0.6_https11.png",
		"ดูว่า Ca Root อยู่ที่ใหน"+M.tm_s("mkcert -CAROOT"),	
	"pos_linux_lite_v0.6_https12.png",
		"ต่อดวย"+M.tm_s("mkcert -install")+" สำเร็จ ตัวสร้างไฟล์ ",

	"pos_linux_lite_v0.6_https13.png",
		"สรางไฟล์ server.key และ server.crt ไว้ที่ "+dc+"  หรือที่อื่นๆ ตามสะดวก พร้อมกำหนดชื่อ โดเมนตามต้องการ ด้วนคำสั่ง "+M.tm_s("mkcert -key-file "+dc+"server.key  -cert-file "+dc+"server.crt localhost 127.0.0.1 192.168.1.100 diy.pos mypos pos ::1"),
	
	
	//home/sh/เอกสาร/	
	"pos_linux_lite_v0.6_https14.png",
		"ได้ไฟล์ certificate แล้ว ",		
	"pos_linux_lite_v0.6_https15.png",
		"ที่อยู่ปลายทาง สร้างไฟล์สำเร็จ",		
	"pos_linux_lite_v0.6_https16.png",
		"แก้ไขๆฟล์ /etc/hosts เพิ่ม โดเมนให้ตรงกับไฟล์ที่ได้าร้งไว้ขั้นตอนที่แล้ว",	
	"pos_linux_lite_v0.6_https17.png",
		"(เปิดไฟล์ /opt/lampp/etc/ssl.crt/ ด้วย Open as Administrator) ให้ไปที่ /opt/lampp/etc/ssl.crt/ เปลี่ยนขื่อไฟล์ server.crt เป็นชื่ออื่นๆ เช่น xxx_server.crt แล้ว คัดลอกไฟล์  server.crt ที่ได้สร้างไว้ในขั้นตอนที่แล้วนำมาวางแทน",		
	"pos_linux_lite_v0.6_https18.png",
		"(เปิดไฟล์ /opt/lampp/etc/ssl.key/ ด้วย Open as Administrator) ให้ไปที่ /opt/lampp/etc/ssl.key/ เปลี่ยนขื่อไฟล์ server.key เป็นชื่ออื่นๆ เช่น xxx_server.key แล้ว คัดลอกไฟล์  server.key ที่ได้สร้างไว้ในขั้นตอนที่แล้วนำมาวางแทน",		
	"pos_linux_lite_v0.6_https19.png",
		"ลองเปิด url ด้วย โดเมน ที่ได้สร้างไว้ จากขั้นตอนที่ 2  ด้วย เว็บบราวเซอร์ ด้วย https:// เช่น https://diy.pos",	
]
let head="ทำ https:// และ ชื่อ Domain"
let msg=""
let pic=[]


M.blockHead(head)
M.blockNote(`ขั้นตอนนี้สารมารถข้ามไปได้ หรือไม่ทำก็ได้`)
M.blockStep(
	"ดาวน์โหลด mkcert",
	`ดาวโหลด ${M.link_s("mkcert","https://github.com/FiloSottile/mkcert/releases")} สำหรับการเข้าทาง https:// เว็บบราวเซอร์ จะเตือนเรื่องความปลอดภัย
	เลือก mkcert-v1.4.3-linux-amd64
		วิธีการ ทำ SSL ให้กับ localhost Linux Lite`,
	[img[0],img[1],img[2],img[3],]
)
M.blockStep(
	"ติดตั้ง mkcert",
	`ไฟล์ที่ดาวน์โหลดมาได้ mkcert-v1.4.3-linux-amd64 สังเกต รุ่นด้วยครับ อันนี้ คือ ${M.v_s("v1.4.3")} ใหม่ที่สุด ณ วันที่ 2021-07
		${M.li_s([mv+" รุ่นของ mkcert "])}ทำตามภาพได้เลย`,
	[img[4],img[5],img[6],img[7],img[8],img[9],img[10],img[11],img[12],img[13],img[14],img[15],
	img[16],img[17],img[18],img[19],img[20],img[21],img[22],img[23]]
)
M.blockStep(
	"สร้างไฟล์ server.crt และ server.key",
	`ไฟล์ที่ดาวน์โหลดมาได้ mkcert-v1.4.3-linux-amd64 สังเกต รุ่นด้วยครับ อันนี้ คือ ${M.v_s("v1.4.3")} ใหม่ที่สุด ณ วันที่ 2021-07
		${M.li_s([mv+" รุ่นของ mkcert "])}ทำตามภาพได้เลย`,
	[img[24],img[25],img[26],img[27],img[28],img[29]]
)
M.blockStep(
	"นำไฟล์ server.crt และ server.key ไปใช้",
	``,
	[img[30],img[31],img[32],img[33],img[34],img[35],img[36],img[37]]
)
M.sticky()
M.blockRef([
	M.link_s("How to create locally trusted SSL Certificates on Linux and macOS with mkcert","https://computingforgeeks.com/how-to-create-locally-trusted-ssl-certificates-on-linux-and-macos-with-mkcert/")
])
