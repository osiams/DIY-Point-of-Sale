<?php
define("CF", [
	"a"=>["product","unit","fetch","me","user","setting","bills","sell","barcode","bill58","ret","it","day","qrc"],
//==========คำเตือน!! โปรดแก้ไขด้วยความระมัดระวัง
//==========ป้อมูลการเชื่อมต่อกับ ฐานข้อมูล MariaDB
	"server"=>"127.0.0.1",					//--ค่านี้เท่านั้น
	"database"=>"diypos_0.0",		//--ชื่อฐานข้อมูลตามรุ่น หรือ ชื่ออื่น
	"user"=>"ชื่อผู้ใช้",									//--ชื่อผู้ใช
	"password"=>"รหัสผ่าน",			//--รหัสผ่านที่ได้ตั้งไว้ใน User accounts ของ   phpmyadmin
//==http port ต้องตรงกับในไฟล์ httpd.conf
		"http_port"=>80,
		"https_port"=>443,
//==========ตำแหน่งผู้ใช้ ลำดับตามบรรทัดมี 10 ระดับเท่านั้น
"userceo"=>[
	["name"=>"พักหรือออก","a"=>[]],
	["name"=>"พนักงานขาย","a"=>["sell","me","fetch","product","bills","bill58","barcode","qrc"]],
	null,
	null,
	null,
	null,
	null,
	null,
	["name"=>"ผู้จัดการรร้าน","a"=>["product","unit","fetch","me","user","bills","sell","bill58","ret","it","day","barcode","qrc"]],
	["name"=>"เจ้าของร้าน","a"=>["product","unit","fetch","me","user","setting","bills","sell","bill58","ret","it","day","barcode","qrc"]]
]
]);

?>
