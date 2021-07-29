let xv=M.v_s("xampp-linux-x64-8.0.7-0-installer.run")
let xd=M.v_s("/home/sh/download")
let img=[
	"pos_win_v0.6_xampp_01.png",
		"เลือก ส่วนของ Windows เลือก รุ่นที่ใหม่ที่สุด เลือก ส่วนของ Windows เลือก รุ่นที่ใหม่ที่สุด เลือก ส่วนของ Windows เลือก รุ่นที่ใหม่ที่สุด",
	"pos_win_v0.6_xampp_02.png",
		"ไฟล์ที่ ดาวโหลดลงเครื่องแล้ว",
	"pos_win_v0.6_xampp_03.png",
		"กดดับเบิ้ลคลิดไฟล์ที่ดาวโหลดมา จะขึ้นหน้าต่างแบบนี้ ให้กด Yes",
	"pos_win_v0.6_xampp_04.png",
		"กด Yes ได้เลย แต่ถ้าต้องการติดตั้งให้เร็วขึ้น ให้ปิดโปรแกรมป้องกันไวรัส",
	"pos_win_v0.6_xampp_05.png",
		"แนะนำติดตั้งที่ C:\Program Files",
	"pos_win_v0.6_xampp_06.png",
		"เริ่ม",
	"pos_win_v0.6_xampp_07.png",
		"ค่าที่จำเป็นจะเป็นดังรูป แต่จะติ๊กเอาหมดเลยก็ได้",
	"pos_win_v0.6_xampp_08.png",
		"ที่อยู่ xampp อยู่ที่ C:\\xampp สามารถเปลี่ยนเป็นที่อยู่อื่นได้",		
	"pos_win_v0.6_xampp_09.png",
		"เลือกภาษาต้องการ",
	"pos_win_v0.6_xampp_10.png",
		"ต่อ",
	"pos_win_v0.6_xampp_11.png",
		"ต่อ",
	"pos_win_v0.6_xampp_12.png",
		"เริ่มติดตั้งแล้ว",
	"pos_win_v0.6_xampp_13.png",
		"เสร็จแล้ว กด Finish",
	"pos_win_v0.6_xampp_14.png",
		"จะเด้ง XAMPP Control panel มา",	
	"pos_win_v0.6_xampp_15.png",
		"กด start ที่ Apache และ MySQL ต่อด้วยกดที่ admin แถว Apache โปรแกรมจะเปิดหน้าต้อนรับของ XAMPP (localhost)",	
]
let head="ติดตั้ง XAMPP บน Linux Lite"
let msg=""
let pic=[]


M.blockHead(head)
M.blockStep(
	"ดาวน์โหลด XAMPP ",
	`ให้ไปดาวน์โหลด ${M.link_s("XAMPP","https://www.apachefriends.org/")} เลือกในส่วนของ Linux `,
	[img[0],img[1],img[2],img[3]]
)
M.blockStep(
	"ทำการติดต้ง XAMPP ",
	`ทำตามรูปได้เลย`,
	[img[4],img[5],img[6],img[7],img[8],img[9],img[10],
	img[11],img[12],img[13],img[14],img[15],img[16],img[17],img[18],img[19],
	img[20],img[21],img[22],img[23],img[24],img[25],img[26],img[27],img[28],img[29]]
)
M.sticky()
