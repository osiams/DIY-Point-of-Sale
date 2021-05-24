<?php
#คำเตือน!! โปรดแก้ไขด้วยความระมัดระวัง
define("CF", [
#หน้า index?a=value ทั้งหมดที่มีและใช้งาน
	"a"=>["product","unit","fetch","me","user","setting","bills","sell","barcode","bill58","ret","it",
				"day","qrc","cd","group","partner","prop","payu","tool","fileupload","gallery","factory",
				"member","device","time","tran","account","account_rca"],
	"require" => ["group"=>["prop"],"product"=>["group","prop"]],
#้ข้อมูลการเชื่อมต่อกับ ฐานข้อมูล MariaDB
	"server"=>"127.0.0.1",
	"database"=>"diypos_0.5",
	"user"=>"diypos",		
	"password"=>"mr12345678",	
#http port ต้องตรงกับในไฟล์ httpd.conf
	"http_port"=>80,
	"https_port"=>443,
#websocket host ,port 
	"ws_host" => "0.0.0.0",
	"ws_port" => 9000,	
#List of Supported Timezones @https://www.php.net/manual/en/timezones.php	
#เวลาที่เครื่องเซิฟเวอร์	
	"timezone"=>"Asia/Bangkok",
#ตำแหน่งผู้ใช้ ลำดับตามบรรทัดมี 10 ระดับเท่านั้น
	"userceo"=>[
		["name"=>"[[SYSTEM]]","a"=>["cd","me","fetch"]],
		["name"=>"พักหรือออก","a"=>[]],
		["name"=>"พนักงานขาย","a"=>["sell","me","fetch","product","bills","setting","bill58","barcode","qrc","group","partner","prop","payu","fileupload","gallery","member","device","time","tran","account","account_rca"]],
		["name"=>"-","a"=>["me"]],
		["name"=>"-","a"=>["me"]],
		["name"=>"-","a"=>["me"]],
		["name"=>"-","a"=>["me"]],
		["name"=>"-","a"=>["me"]],
		["name"=>"ผู้จัดการรร้าน","a"=>["product","unit","fetch","me","user","setting","bills","sell","bill58","ret","it","day","barcode","qrc","group","partner","prop","payu","fileupload","gallery","member","device","time","tran","account","account_rca"]],
		["name"=>"เจ้าของร้าน","a"=>["product","unit","fetch","me","user","setting","bills","sell","bill58","ret","it","day","barcode","qrc","group","partner","prop","payu","tool","fileupload","gallery","factory","member","device","time","tran","account","account_rca"]]
	]
]);
#ค่าการอณุญาตการใช้งาน
#"user_regto" คือ สามารถลงทะเบียนผู้ใช้ใหม่ได้หรือไม่ [true=ได้,false=ไม่ได้]
#"user_regceoto" คือ ระดับผู้ใช้ที่สามารถลงทะเบียนให้ได้ 
#"user_editto" คือ ระดับผู้ใช้ที่สามารถแก้ไขได้
#"user_delto" คือ ระดับผู้ใช้ที่ลบออกจากฐานข้อมูลได้
#"time_closeto" ปิดกะของผู้ใช้คนอื่นได้
define("PEM",[
	0=>["user_regto"=>false,"user_regceoto"=>[],"user_editto"=>[],"user_delto"=>[],
			"time_closeto"=>false
	],
	1=>["user_regto"=>false,"user_regceoto"=>[],"user_editto"=>[],"user_delto"=>[],
			"time_closeto"=>false
	],
	2=>["user_regto"=>false,"user_regceoto"=>[],"user_editto"=>[],"user_delto"=>[],
			"time_closeto"=>false
	],
	3=>["user_regto"=>false,"user_regceoto"=>[],"user_editto"=>[],"user_delto"=>[],
			"time_closeto"=>false
	],
	4=>["user_regto"=>false,"user_regceoto"=>[],"user_editto"=>[],"user_delto"=>[],
			"time_closeto"=>false
	],
	5=>["user_regto"=>false,"user_regceoto"=>[],"user_editto"=>[],"user_delto"=>[],
			"time_closeto"=>false
	],
	6=>["user_regto"=>false,"user_regceoto"=>[],"user_editto"=>[],"user_delto"=>[],
			"time_closeto"=>false
	],
	7=>["user_regto"=>false,"user_regceoto"=>[],"user_editto"=>[],"user_delto"=>[],
			"time_closeto"=>false
	],
	8=>["user_regto"=>true,"user_regceoto"=>[1,2,3,4,5,6,7],"user_editto"=>[1,2,3,4,5,6,7],"user_delto"=>[1,2,3,4,5,6,7],
			"time_closeto"=>true
	],
	9=>["user_regto"=>true,"user_regceoto"=>[1,2,3,4,5,6,7,8,9],"user_editto"=>[0,1,2,3,4,5,6,7,8,9],"user_delto"=>[0,1,2,3,4,5,6,7,8,9],
			"time_closeto"=>true
	]
]);

?>
