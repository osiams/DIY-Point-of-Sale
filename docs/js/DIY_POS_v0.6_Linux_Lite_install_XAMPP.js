let xv=M.v_s("xampp-linux-x64-8.0.7-0-installer.run")
let xd=M.v_s("/home/sh/download")
let img=[
	"pos_linux_lite_v0.6_xampp_web.png",
		"เลือก ส่วนของ Linux เลือก รุ่นที่ใหม่ที่สุด เลือก ส่วนของ Linux เลือก รุ่นที่ใหม่ที่สุด เลือก ส่วนของ Linux เลือก รุ่นที่ใหม่ที่สุด",
	"pos_linux_lite_v0.6_xampp_at_com.png",
		"ไฟล์ที่ ดาวโหลดลงเครื่องแล้ว",
	"pos_linux_lite_v0.6_xampp_tm_cd.png",
		"เปิด เทอร์มินัล ให้ชี้ไปที่ "+M.tm_s("cd "+xd)+" (ชื่อผู้ใช้ของผมคือ sh) ",
	"pos_linux_lite_v0.6_xampp_tm_cd_ed.png",
		"จะชี้มาตำแหน่งแฟ้มที่เก็บไฟล",
	"pos_linux_lite_v0.6_xampp_tm_cd_ex.png",
		"เปลี่ยนโหมดไฟล์ "+M.tm_s("chmod +x  "+xv)+"",
	"pos_linux_lite_v0.6_xampp_tm_cd_start_set.png",
		"เริ่มต้นติดต้ง ด้วยคำสั่ง"+M.tm_s("sudo ./"+xv)+"",
	"pos_linux_lite_v0.6_xampp_tm_cd_start_setdir.png",
		"จะติดตั้งที่ /opt/lampp",
	"pos_linux_lite_v0.6_xampp_tm_cd_start_fins.png",
		"จบการติดตั้ง",
	"pos_linux_lite_v0.6_xampp_tm_cd_start_pen.png",
		"หน้าต่างควบคุม ของ XAMPP",
	"pos_linux_lite_v0.6_xampp_tm_cd_start_suc.png",
		"เปิด Firefox เข้า http://localhost จะได้ดังภาพ"
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
	`ไฟล์ที่ได้จากการดาวน์โหลดของผมคือ ${xv}
	เก็บไว้ที่ ${xd}
	${M.li_s([xv+" ชื่อไฟล์ที่ ดาวน์โหลดมา",xd+" แฟ้มของ ไฟล์ที่ดาวน์โหลดมา"])}
	`,
	[img[4],img[5],img[6],img[7],img[8],img[9],img[10],
	img[11],img[12],img[13],img[14],img[15],img[16],img[17],img[18],img[19]]
)
M.sticky()
