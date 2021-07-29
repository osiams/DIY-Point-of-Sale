let mv=M.v_s("v1.4.3")
let xd=M.v_s("/home/sh/download")
let dc=M.v_s("/home/sh/เอกสาร/")
let img=[
	"pos_win_v0.6_https_01.png",
		"เลือก สำหรับ Linux mkcert-v1.4.3-linux-amd64",
	"pos_win_v0.6_https_02.png",
		"ไฟล์ที่ ดาวโหลด์มาได้",
	"pos_win_v0.6_https_03.png",
		"เปิด  ตามภาพเลย",
	"pos_win_v0.6_https_04.png",
		"พิมพิ์ "+M.tm_s("choco install mkcert")+" แล้วกด Enter",
	"pos_win_v0.6_https_05.png",
		"ผล",
	"pos_win_v0.6_https_06.png",
		"ลองทดสอบพิมพิ์ "+M.tm_s("choco")+" แล้วกด Enter",
	"pos_win_v0.6_https_07.png",
		"ผล",
	"pos_win_v0.6_https_08.png",
		"ผล",
	"pos_win_v0.6_https_08.5.png",
		"พิมพิ์ "+M.tm_s("mkcert -install")+" แล้วกด Enter จะมีกล่องเตือนให้กด Yes",	
	"pos_win_v0.6_https_09.png",
		"server.crt เปลี่ยนเป็น server.crt.bak หรือชื่ออื่นๆ ",
	"pos_win_v0.6_https_10.png",
		"server.key เปลี่ยนเป็น server.key.bak หรือชื่ออื่นๆ",
	"pos_win_v0.6_https_11.png",
		"เปิด Windows PowerShell พิมพิ์ "+M.tm_s("mkcert -key-file C:\\xampp\\apache\\conf\\ssl.key\\server.key -cert-file C:\\xampp\\apache\\conf\\ssl.crt\\server.crt  localhost 127.0.0.1 192.168.1.100  diy.pos pos mypos ::1")+" แล้วกด Enter <br>ถ้าใคร สร้างโดเมนที่ไม่เหมือนตัวอย่างด้านบน ก็เทียบเคียงโค๊ดดูครับ",
	"pos_win_v0.6_https_12.png",
		"เปิดไฟล์ C:\\Windows\\System32\\drivers\\etc\\hosts ด้วยโปรแกรม Notepad ในฐานะ Administrator",
	"pos_win_v0.6_https_13.png",
		"แก้ไขตามโดเมนที่ได้สร้างไว้ แล้วกดบันทึก (ถ้าบันทึกไม่ได้ให้ลองปิดโปรแกรมป้องกันไวรัส)",
	"pos_win_v0.6_https_14.png",
		"ลองเข้า ชื่อ ตามโดเมนที่ได้สร้างไว้",
	"pos_win_v0.6_https_15.png",
		"เข้าผ่านทางอุปกรณ์อื่น ๆ ในวง LAN เดียวกัน ",
]
let head="ทำ https:// และ ชื่อ Domain"
let msg=""
let pic=[]


M.blockHead(head)
M.blockNote(`ขั้นตอนนี้สารมารถข้ามไปได้ หรือไม่ทำก็ได้`)
M.blockStep(
	"ศึกษาข้อมูล และวิธีการติดตั้ง",
	`ดูได้ที่  ${M.link_s("mkcert","https://github.com/FiloSottile/mkcert#windows")} `,
	[]
)
M.blockStep(
	"ดาวน์โหลด mkcert",
	`ดาวโหลด ${M.link_s("mkcert","https://github.com/FiloSottile/mkcert/releases")} สำหรับการเข้าทาง https:// เว็บบราวเซอร์ จะเตือนเรื่องความปลอดภัย
	เลือก mkcert-vxxx สำหรับ Windows
		วิธีการ ทำ SSL ให้กับ localhost  windows"`,
	[img[0],img[1],img[2],img[3],]
)
M.blockStep(
	"ติดตั้ง Chocolatey ตัวจัดการแพคเกจ",
	`ทำตาม ${M.link_s("การติดตั้ง Chocolatey","https://chocolatey.org/install")} หรือ ทำตามภาพด้านล่างได้เลย`,
	[img[4],img[5],img[6],img[7],img[8],img[9],img[10],img[11]]
)
M.blockStep(
	"ติดตั้ง mkcert ",
	`ทำตามภาพได้เลย`,
	[img[12],img[13],img[14],img[15],img[16],img[17]]
)
M.blockStep(
	"สร้างไฟล์ server.crt และ server.key",
	`<p>ตัวอย่างจะสร้างโดเมนไว้ใช้เองดังนี้
	<code class="block">localhost
	<br/>127.0.0.1
	<br/>192.168.1.100 
	<br/>diy.pos
	<br/>pos
	<br/>mypos
	<br/>::1
	</code>
	*192.168.1.100 &lt;--IP ในวง LAN ของเครื่อง Server (เครื่องที่ติดคั้ง XAMPP) เป็น IP สำหรับเรียกใช้งานผ่านอุปกรณ์อื่นๆ เช่น โทรศัพท์ แท็บเลต แต่ละท่านอาจได้เลขที่ค่างกันไป</p>
	<br><p>1.ไปที่ C:\\xampp\\apache\\conf\\ssl.crt\\server.crt เปลี่ยนชื่อ หรือสกุลไฟล์เป็นอย่างอื่น ในตัวอย่างเปลี่ยนเป็น server.crt.bak (จะไม่ใช้ไฟล์นี้) </p>
	<br><p>2.ไปที่ C:\\xampp\\apache\conf\\ssl.key\\server.key เปลี่ยนชื่อ หรือสกุลไฟล์เป็นอย่างอื่น ในตัวอย่างเปลี่ยนเป็น server.key.bak (จะไม่ใช้ไฟล์นี้)</p>
	`,
	[img[18],img[19],img[20],img[21],img[22],img[23]]
)
M.blockStep(
	"แก้ไขไฟล์ hosts",
	`ทำตามรูปได้เลย`,
	[img[24],img[25],img[26],img[27]]
)
M.blockStep(
	"ทดสอบเข้าเว็บ",
	`restart Apache  ใหม่ แล้วลองเข้าเว็บตามโดเมนที่ได้สร้างไว้ ใช้ได้กับเครื่องที่ติดตั้ง XAMPP ถ้าเรียกใช้ผ่านอุปกรณ์อื่นศึกษาเพิ่มเติม (ไม่ทำก็ได้ แต่ในกรณีเข้าทาง https:// เข้าใช้งานจากเครื่องอื่น จะมีหน้าเตือนเรื่องความไม่ปลอดภัย  ถ้าเข้าทาง http:// จะไม่มีหน้าเตือน อันนี้ยิงไม่ปลอดภัยเลย )
	<p>${M.link_s("ssl - How to install trusted CA certificate on Android device? - Stack Overflow","https://stackoverflow.com/questions/4461360/how-to-install-trusted-ca-certificate-on-android-device/22040887#57925784")}</p>
	<p>${M.link_s("How to Make a Computer Trust a Certificate Authority","https://smallbusiness.chron.com/make-computer-trust-certificate-authority-57649.html")}</p>
	`,
	[img[28],img[29],img[30],img[31]]
)
M.sticky()
