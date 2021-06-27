<?php
class main{
	private $re;
	protected int $user_ceo;
	public function __construct(){
		date_default_timezone_set ("Asia/Bangkok" );
		$this->cf=["server"=>CF["server"],"database"=>CF["database"],"user"=>CF["user"],"password"=>CF["password"],"userceo"=>CF["userceo"]];
		$this->pem=PEM;
		$this->system=null;
		$this->user_ceo=isset($_SESSION["userceo"])?$_SESSION["userceo"]:-1;
		$this->gallery_dir=dirname(__DIR__)."/img/gallery";
		$this->main_ip=$this->userIPv4();
		$this->re=[
			"connect"=>false,
			"connect_error"=>"",
			"result"=>false,
			"count"=>[],
			"data"=>[],
			"message_error"=>""
		];
		$this->mb_type = ["s"=>"🏠 ผู้ประกอบการ","p"=>"🧑 ผู้บริโภค"];
		$this->s_type=[
			"p"=>["icon"=>"⚃","desc"=>"ขายเป็น อัน","opg"=>"อัน"],
			"w"=>["icon"=>"⚖️","desc"=>"ชั่งน้ำหนักขาย","opg"=>"น้ำหนัก"],
			"l"=>["icon"=>"📏","desc"=>"วัดความยาวขาย","opg"=>"ความยาว"],
			"v"=>["icon"=>"🧊","desc"=>"ขายเป็นปริมาตร","opg"=>"ปริมาตร"]
		];
		$this->money_type=[
			"ca"=>["icon"=>"💰","name"=>"เงินสด"],
			"tr"=>["icon"=>"🏦","name"=>"เงินโอน"],
			"ck"=>["icon"=>"💸","name"=>"เช็ค"],
			"cd"=>["icon"=>"👎","name"=>"เงินเชื่อ"]
		];
		$this->tb=[
			"bill_in"=>[
				"name"=>"bill_in",
				"column"=>["id","time_id","in_type","sku","lot_from","lot_root","bill","n",
				"bill_po_sku","pn_key","pn_root","bill_no","bill_date","bill_type","icon_arr","icon_gl","vat_n",
				"sum","changto","user","user_edit","note","stkey_","stroot_",
				"r_","_r","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],				
				"primary"=>"sku",
				"index"=>["time_id","in_type","changto","user","note","stkey_","stroot_","r_","_r"]
			],
			"bill_in_list"=>[
				"name"=>"bill_in_list",
				"column"=>["id","stkey","stroot","bill_in_sku","lot","product_sku_key","product_sku_root","name","s_type",
				"n","balance","n_wlv","balance_wlv","sum","sq","unit_sku_key","unit_sku_root","note","idkey"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP"],
				"primary"=>"id",
				"index"=>["lot","stkey","stroot","bill_in_sku","product_sku_key","product_sku_root","balance"]
			],
			"bill_sell"=>[
				"name"=>"bill_sell",
				"column"=>["id","time_id","sku","n","cost","costr","price","pricer","user","user_edit","member_sku_key","member_sku_root","stat","stath","note","w","r_","_r",
					"min","mout","credit","payu_json","payu_ref_json","payu_key_json","rca_key_json","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL","pricer"=>0,"costr"=>0,"member_sku_key"=>"NULL","member_sku_root"=>"NULL"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],	
				"primary"=>"sku",
				"index"=>["time_id","member_sku_key","member_sku_root","user","stat","stath","w","date_reg"]
			],
			"bill_sell_list"=>[
				"name"=>"bill_sell_list",
				"column"=>["id","sku","bill_in_list_id","lot","product_sku_key","product_sku_root",
					"n","n_wlv","c","u","r","h","sq","unit_sku_key","unit_sku_root","note","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL","r"=>0,"h"=>0,"n_wlv"=>1,"sq"=>1],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],	
				"primary"=>"id",
				"index"=>["sku","bill_in_list_id","lot","product_sku_key","product_sku_root","n_wlv"]
			],
			"bill_rca"=>[
				"name"=>"bill_rca",
				"column"=>["id","time_id","sku","user_id","member_id","member_sku_key","user","user_edit",
				"note","r_","_r","pos_id","drawers_id","onoff",
					"pay","min","credit","payu_json","payu_key_json","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL","min"=>0,"mout"=>0,"credit"=>0,"onoff"=>"1"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],	
				"primary"=>"id",
				"index"=>["time_id","sku","member_id","date_reg","member_sku_key","user","onoff",]
			],
			"bill_rca_partner"=>[
				"name"=>"bill_rca_partner",
				"column"=>["id","time_id","sku","user_id","member_id","note","r_","_r","pos_id","drawers_id",
					"pay","min","credit","payu_json","payu_key_json","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL","min"=>0,"mout"=>0,"credit"=>0],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],	
				"primary"=>"id",
				"index"=>["time_id","sku","member_id","date_reg"]
			],
			"bill_rca_list"=>[
				"name"=>"bill_rca_list",
				"column"=>["id","bill_rca_id","bill_sell_id","credit","money_balance","min","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP"],
				"primary"=>"id",
				"index"=>["bill_rca_id","bill_sell_id"]
			],
			"device_pos"=>[
				"name"=>"device_pos",
				"column"=>["id"			,"sku"			,"name"		,"ip"		,"no",
									"onoff"		,"time_id"		,"drawers_id",
									"money_start"	,"money_balance",			"user",
									"disc"		,"icon_arr"		,"icon_gl"			,"modi_date"	,
									"date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],					
				"primary"=>"ip",
				"unique"=>["sku","name"],
				"index"=>["onoff","drawers_id"]
			],
			"device_drawers"=>[
				"name"=>"device_drawers",
				"column"=>["id"			,"sku"				,"name"				,"no",		
									"money_balance"				,"disc"		,"icon_arr"		,"icon_gl"			,"modi_date"	,
									"date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],					
				"primary"=>"sku",
				"not_null"=>["sku","name"],
				"unique"=>["name","no"]
			],
			"gallery"=>[
				"name"=>"gallery",
				"column"=>["id","sku_key","gl_key","name","a_type","mime_type","md5","user","size","width","height","date_reg"],
				"primary"=>"sku_key",
				"index"=>["gl_key","a_type","mime_type","size","width","height"]
			],
			"group"=>[
				"name"=>"group",
				"column"=>["id","sku","sku_key","sku_root","d1","d2","d3","d4","name","prop","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],
				"primary"=>"sku_root",
				"not_null"=>["name"],
				"unique"=>["sku"],
				"check"=>" prop IS NULL OR JSON_VALID(prop)"
			],
			"group_ref"=>[
				"name"=>"group_ref",
				"column"=>["id","sku","sku_key","sku_root","d1","d2","d3","d4","name","prop","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],
				"primary"=>"sku_key",
				"index"=>["sku_root"],
				"check"=>" prop IS NULL OR JSON_VALID(prop)"
			],
			"it"=>[
				"name"=>"it",
				"column"=>["id","sku","sku_key","sku_root","name","note","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],
				"primary"=>"sku_root",
				"not_null"=>["name"],
				"unique"=>["name","sku"]
			],
			"it_ref"=>[
				"name"=>"it_ref",
				"column"=>["id","sku","sku_key","sku_root","name","note","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],
				"primary"=>"sku_key",
				"index"=>["sku_root"]
			],
			"member"=>[
				"name"=>"member",
				"column"=>["id","sku","sku_key","sku_root",
					"name","lastname","mb_type","credit","icon",
					"sex","birthday",
					"password","memberceo",
					"no","alley","road","distric","country",
					"province","post_no",
					"tel","idc","disc","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL","birthday"=>"NULL"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],
				"unsigned"=>["day_nv"],
				"primary"=>"sku_root",
				"not_null"=>["name"],
				"index"=>["mb_type","sex","birthday"],
				"unique"=>["sku","tel","idc"]
			],
			"member_ref"=>[
				"name"=>"member_ref",
				"column"=>["id","sku","sku_key","sku_root",
					"name","lastname","mb_type","credit","icon",
					"sex","birthday",
					"password","memberceo",
					"no","alley","road","distric","country",
					"province","post_no",
					"tel","idc","disc","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"unsigned"=>["day_nv"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],
				"primary"=>"sku_key",
				"index"=>["sku_root","birthday"]
			],
			"product"=>[
				"name"=>"product",
				"column"=>["id","sku","barcode","sku_key","sku_root","name","cost","price","group_key","group_root","props","s_type","partner",
					"vat","vat_p","unit","skuroot1","skuroot1_n","skuroot2","skuroot2_n","pdstat","disc","statnote","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL","pdstat"=>"c","s_type"=>"p"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],
				"not_null"=>["name","sku_root"],
				"primary"=>"sku_root",
				"unique"=>["sku","name","barcode"],
				"index"=>["pdstat","skuroot1","skuroot2","group_root","s_type"]
			],
			"product_ref"=>[
				"name"=>"product_ref",
				"column"=>["id","sku","barcode","sku_key","sku_root","name","cost","price","group_key","group_root","props","s_type","partner",
					"vat","vat_p","unit","skuroot1","skuroot1_n","skuroot2","skuroot2_n","pdstat","disc","statnote","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"not_null"=>["name","sku_root"],
				"primary"=>"sku_key",
				"index"=>["sku_root"]
			],
			"partner"=>[
				"name"=>"partner",
				"column"=>["id","sku","sku_key","sku_root",
					"brand_name","name","pn_type","icon",
					"no","alley","road","distric","country",
					"province","post_no",
					"tel","fax","tax","web","tp_type",
					"od_type","note","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],
				"unsigned"=>["day_nv"],
				"primary"=>"sku_root",
				"not_null"=>["name"],
				"index"=>["tp_type","pn_type"],
				"unique"=>["name","sku"]
			],
			"partner_ref"=>[
				"name"=>"partner_ref",
				"column"=>["id","sku","sku_key","sku_root",
				"brand_name","name","pn_type","icon",
				"no","alley","road","distric","country",
				"province","post_no",
				"tel","fax","tax","web","tp_type",
				"od_type","note","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"unsigned"=>["day_nv"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],
				"primary"=>"sku_key",
				"index"=>["sku_root"]
			],
			"payu"=>[
				"name"=>"payu",
				"column"=>["id","sku","sku_key","sku_root",
					"name","money_type","icon",
					"note","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],
				"primary"=>"sku_root",
				"not_null"=>["name"],
				"index"=>["money_type"],
				"unique"=>["name","sku","sku_key"]
			],
			"payu_ref"=>[
				"name"=>"payu_ref",
				"column"=>["id","sku","sku_key","sku_root",
					"name","money_type","icon",
					"note","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],
				"primary"=>"sku_key",
				"not_null"=>["name"],
				"index"=>["money_type","sku"]
			],
			"prop"=>[
				"name"=>"prop",
				"column"=>["id","sku","sku_key","sku_root","name","data_type","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL","data_type"=>"u"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],
				"primary"=>"sku_root",
				"not_null"=>["name"],
				"unique"=>["name","sku"]
			],
			"prop_ref"=>[
				"name"=>"prop_ref",
				"column"=>["id","sku","sku_key","sku_root","name","data_type","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL","data_type"=>"u"],
				"primary"=>"sku_key",
				"index"=>["sku_root"]
			],
			"ref"=>[
				"name"=>"ref",
				"column"=>["id"	,"ref_stat"			,"ref_table_"	,"ref__table"	,"ref_table_id_",
					"sku_key"			,"ref__table_id"	,"ref_ip_"		,"ref__ip"		,"user"				,"date_exp"	,"date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL","data_type"=>"u"],
				"primary"=>"id",
				"unique"=>["sku_key"],
				"index"=>["ref_stat","ref_table_","ref__table"]
			],
			"rca"=>[
				"name"=>"rca",
				"column"=>["id"		,"time_id"	,"bill_sell_id"			,"user_id"		,"member_id"		,"credit", "date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP"],
				"primary"=>"id",
				"index"=>["time_id","bill_sell_id","member_id","credit"]
			],
			"rca_partner"=>[
				"name"=>"rca_partner",
				"column"=>["id"		,"time_id"	,"bill_sell_id"			,"user_id"		,"member_id"		,"credit", "date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP"],
				"primary"=>"id",
				"index"=>["time_id","bill_sell_id","member_id","credit"]
			],
			"user"=>[
				"name"=>"user",
				"column"=>["id","sku","sku_key","sku_root","name","lastname","email","password","userceo","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],
				"primary"=>"sku_root",
				"not_null"=>["name","email","password"],
				"unique"=>["email","sku","sku_key"]
			],
			"user_ref"=>[
				"name"=>"user_ref",
				"column"=>["id","sku","sku_key","sku_root","name","lastname","email","password","userceo","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"primary"=>"sku_key",
				"index"=>["sku_root"]
			],
			"mmm"=>[
				"name"=>"mmm",
				"column"=>["id","bill_in_id",
				"skukey","skuroot","skukey1","skuroot1","skukey2","skuroot2",
				"skuroot_n","skuroot1_n","skuroot2_n"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","skuroot_n"=>0,"skuroot1_n"=>0,"skuroot2_n"=>0],
				"primary"=>"bill_in_id",
				"index"=>["skuroot"]
			],
			"s"=>[
				"name"=>"s",
				"column"=>["id","tr",
					"bi_c","bil_c","bs_c","bsl_c","bp_c","bpl_c",
					"bir_","bi_r","bsr_","bs_r","bpr_","bp_r",
					"bilr_","bil_r","bslr_","bsl_r","bplr_","bpl_r","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],
				"primary"=>"tr"
			],
			"test"=>[
				"name"=>"test",
				"column"=>["id","tms","note","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","tms"=>00000.000000],
				"primary"=>"id"
			],
			"time"=>[
				"name"=>"time",
				"column"=>[	"id"			,"ip"			,"drawers_id"		,"money_start"			,"min"				,"mout",		"money_balance",
									"r_"			,"_r"			,"user"		,"note"		,"date_reg"	,"date_exp"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","date_exp"=>"NULL"],					
				"primary"=>"id",
				"index"=>["drawers_id","ip","user"]
			],
			"tran"=>[
				"name"=>"tran",
				"column"=>[	"id"			,"time_id"			,"tran_type"		,"ref"						,"ip"			,"drawers_id",
									"min"		,"mout"				,"money_balance"	,"user"		,"note",
									"date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","min"=>0,"mout"=>0,"money_balance"=>0],					
				"primary"=>"id",
				"index"=>["time_id","drawers_id","ip","user","tran_type","ref"]
			],
			"tran_rca"=>[
				"name"=>"tran_rca",
				"column"=>[	"id"			,"time_id"			,"tran_rca_type"			,"bill_rca_id"		,"ip"					,"drawers_id",
									"min"		,"mout"				,"money_balance"		,"member_id"		,"user_id",			
									"date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","min"=>0,"mout"=>0,"money_balance"=>0],					
				"primary"=>"id",
				"index"=>["time_id","drawers_id","ip","user_id","bill_rca_id","tran_rca_type","member_id"]
			],
			"tran_rca_partner"=>[
				"name"=>"tran_rca_partner",
				"column"=>[	"id"			,"time_id"			,"tran_rca_type"		,"ref"						,"ip"					,"drawers_id",
									"min"		,"mout"				,"money_balance"		,"member_id"		,"user_id",
									"note",		"date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","min"=>0,"mout"=>0,"money_balance"=>0],					
				"primary"=>"id",
				"unique"=>["ref"],
				"index"=>["time_id","drawers_id","ip","user_id","tran_rca_type","member_id"]
			],
			"unit"=>[
				"name"=>"unit",
				"column"=>["id","sku","sku_key","sku_root","name","s_type","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL","s_type"=>"p"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],
				"primary"=>"sku_root",
				"not_null"=>["name"],
				"unique"=>["name","sku"]
			],
			"unit_ref"=>[
				"name"=>"unit_ref",
				"column"=>["id","sku","sku_key","sku_root","name","s_type","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL","s_type"=>"p"],
				"primary"=>"sku_key",
				"index"=>["sku_root"]
			]
		];
		$this->fills=[
			"a_type"=>["name"=>"สำหรับแอป","type"=>"ENUM","length_value"=>["partner","bill_in","payu","member","device_pos","device_drawers"]],
			"amount"=>["name"=>"จำนวน","type"=>"INT","length_value"=>10],
			"alley"=>["name"=>"ซอย","type"=>"CHAR","length_value"=>80,"charset"=>"thai"],
			"barcode"=>["name"=>"รหัสแท่ง","type"=>"CHAR","length_value"=>80],
			"balance"=>["name"=>"คงเหลือ","type"=>"INT","length_value"=>10],
			"balance_wlv"=>["name"=>"คงเหลือชั่งตวงวัด","type"=>"FLOAT","length_value"=>[10,4]],
			"bill"=>["name"=>"ใบ","type"=>"CHAR","length_value"=>25],
			"bill_date"=>["name"=>"วันที่ในใบเสร็จ","type"=>"TIMESTAMP",],
			"bill_in_id"=>["name"=>"ที่นำเขา","type"=>"INT","length_value"=>10],
			"bill_in_list_id"=>["name"=>"ที่นำเขา","type"=>"INT","length_value"=>10],
			"bill_no"=>["name"=>"เลขที่ในใบเส็จ","type"=>"CHAR","length_value"=>80,"charset"=>"thai"],
			"bill_po_sku"=>["name"=>"เลขที่ใบสั่งซื้อ","type"=>"CHAR","length_value"=>25],
			"bill_in_sku"=>["name"=>"รหัสภายในใบนำเข้าสินค้า","type"=>"CHAR","length_value"=>25],
			"bill_sell_id"=>["name"=>"ที่ใบขาย","type"=>"INT","length_value"=>10],
			"bill_rca_id"=>["name"=>"ที่ใบชำระค้างจ่าย","type"=>"INT","length_value"=>10],
			"bill_type"=>["name"=>"ประเภทใบเสร็จ","type"=>"ENUM","length_value"=>["c","v0","v1"]],
			"birthday"=>["name"=>"วันเกิด","type"=>"TIMESTAMP",],
			"brand_name"=>["name"=>"ชื่อการค้า","type"=>"CHAR","length_value"=>255,"charset"=>"thai"],
			//--0=เงินม1=สินค้าตัวเดิม
			"changto"=>["name"=>"เปลี่ยนเป็น","type"=>"ENUM","length_value"=>["0","1"]],
			//"barcode1"=>["name"=>"รหัสแท่งย่อยสุด1","type"=>"CHAR","length_value"=>80],
			//"barcode2"=>["name"=>"รหัสแท่งย่อยสุด2","type"=>"CHAR","length_value"=>80],
			//"barcode1_n"=>["name"=>"จำนวนที่แบ่ง1","type"=>"INT","length_value"=>10],
			//"barcode2_n"=>["name"=>"จำนวนที่แบ่ง_2","type"=>"INT","length_value"=>10],
			"cost"=>["name"=>"ต้นทุน","type"=>"FLOAT","length_value"=>[15,4]],
			"costr"=>["name"=>"ต้นทุนคืน","type"=>"FLOAT","length_value"=>[15,4]],
			"costa"=>["name"=>"ต้นทุนเฉลี่ย","type"=>"FLOAT","length_value"=>[15,4]],
			"country"=>["name"=>"เขต/อำเภอ","type"=>"CHAR","length_value"=>80,"charset"=>"thai"],
			"credit"=>["name"=>"เชื่อ","type"=>"FLOAT","length_value"=>[15,2]],
			"d1"=>["name"=>"ระดับ1","type"=>"CHAR","length_value"=>25],
			"d2"=>["name"=>"ระดับ2","type"=>"CHAR","length_value"=>25],
			"d3"=>["name"=>"ระดับ3","type"=>"CHAR","length_value"=>25],
			"d4"=>["name"=>"ระดับ4","type"=>"CHAR","length_value"=>25],
			"distric"=>["name"=>"แขวง/ตำบล","type"=>"CHAR","length_value"=>80,"charset"=>"thai"],
			"data_type"=>["name"=>"ชนิดข้อมูล","type"=>"ENUM","length_value"=>["s","n","b","u"]],
			"disc"=>["name"=>"รายละเอียด","type"=>"VARCHAR","length_value"=>1000,"charset"=>"thai"],
			"date_reg"=>["name"=>"วันที่สร้าง","type"=>"TIMESTAMP"],
			"date_exp"=>["name"=>"วันทสิ้นสุด","type"=>"TIMESTAMP"],
			"drawers_id"=>["name"=>"ลิ้นชักที่","type"=>"INT","length_value"=>10],
			"email"=>["name"=>"อีเมล","type"=>"CHAR","length_value"=>30],
			"fax"=>["name"=>"แฟ็กซ์","type"=>"CHAR","length_value"=>15],
			"float"=>["name"=>"จำนวน","type"=>"FLOAT","length_value"=>[10,4]],
			"gl_key"=>["name"=>"รหัสห้องภาพ","type"=>"CHAR","length_value"=>25],
			"group_key"=>["name"=>"กลุ่มอ้างอิง","type"=>"CHAR","length_value"=>25],
			"group_root"=>["name"=>"กลุ่มราก","type"=>"CHAR","length_value"=>25],
			"h"=>["name"=>"เปลี่ยน","type"=>"INT","length_value"=>10],
			"height"=>["name"=>"กว้าง","type"=>"INT","length_value"=>6],
			"icon"=>["name"=>"รูป","type"=>"CHAR","length_value"=>255],
			"icon_arr"=>["name"=>"รูปหลาย","type"=>"TEXT","length_value"=>65535],
			"icon_gl"=>["name"=>"ห้องรูป","type"=>"TEXT","length_value"=>65535],
			"id"=>["name"=>"ที่","type"=>"INT","length_value"=>10],
			"idc"=>["name"=>"เลขที่บัตรประชาชน์","type"=>"CHAR","length_value"=>15],
			"idkey"=>["name"=>"ที่อ้างอิง","type"=>"INT","length_value"=>10],
			//--"buy","cancel","return",move,x,delete
			"in_type"=>["name"=>"ประเภทการเข้า","type"=>"ENUM","length_value"=>["b","c","r","m","x","d","mm"]],
			"ip"=>["name"=>"เลข IP","type"=>"CHAR","length_value"=>25],
			"lastname"=>["name"=>"นามสกุล","type"=>"CHAR","length_value"=>255,"charset"=>"thai"],
			"lot"=>["name"=>"งวด","type"=>"CHAR","length_value"=>25],
			"lot_from"=>["name"=>"งวดอ้างอิง","type"=>"CHAR","length_value"=>25],
			"lot_root"=>["name"=>"งวดราก","type"=>"CHAR","length_value"=>25],
			"m"=>["name"=>"สินค้ารากที่แตก","type"=>"CHAR","length_value"=>25],
			"m_n"=>["name"=>"จำนวนสินค้ารากที่แตก","type"=>"INT","length_value"=>10],
			"mb_type"=>["name"=>"ประเภทสมาชิก","type"=>"ENUM","length_value"=>["s","p"]],
			"md5"=>["name"=>"md5","type"=>"CHAR","length_value"=>32],
			"memberceo"=>["name"=>"ระดับสมาชิก","type"=>"ENUM","length_value"=>["0","1","2","3","4","5","6","7","8","9"]],
			"member_id"=>["name"=>"เลขที่สมาชิก","type"=>"INT","length_value"=>10],
			"member_sku_key"=>["name"=>"รหัสอ้างอิงสมาชิก","type"=>"CHAR","length_value"=>25],
			"member_sku_root"=>["name"=>"รหัสรากสมาชิก","type"=>"CHAR","length_value"=>25],
			"min"=>["name"=>"เงินเข้า","type"=>"FLOAT","length_value"=>[15,2]],
			"mime_type"=>["name"=>"ประเภทไฟล์","type"=>"ENUM","length_value"=>["image/png","image/gif","image/jpeg"]],
			"mout"=>["name"=>"เงินออก","type"=>"FLOAT","length_value"=>[15,2]],
			"modi_date"=>["name"=>"วันปรับปรุง","type"=>"TIMESTAMP",],
			"money_start"=>["name"=>"เงินเริ่มต้น","type"=>"FLOAT","length_value"=>[15,2]],
			"money_balance"=>["name"=>"เงินคงเหลือ","type"=>"FLOAT","length_value"=>[15,2]],
			"money_type"=>["name"=>"รูปแบบเงิน","type"=>"ENUM","length_value"=>["ca","tr","ck","cd"]],
			"n"=>["name"=>"จำนวน","type"=>"INT","length_value"=>10],
			"n_wlv"=>["name"=>"จำนวนชั่งตวงวัด","type"=>"FLOAT","length_value"=>[10,4]],
			"no"=>["name"=>"เลขที่","type"=>"CHAR","length_value"=>25],
			"c"=>["name"=>"จำนวนที่ได้ตัด","type"=>"INT","length_value"=>10],
			"u"=>["name"=>"จำนวนไมได้ตัด","type"=>"INT","length_value"=>10],
			"name"=>["name"=>"ชื่อ","type"=>"CHAR","length_value"=>255,"charset"=>"thai"],
			"note"=>["name"=>"บันทึกย่อ","type"=>"CHAR","length_value"=>255,"charset"=>"thai"],
			"od_type"=>["name"=>"รูปแบบการสั่งซื้อ","type"=>"ENUM","length_value"=>["s","a","o","t"]],
			"onoff"=>["name"=>"เปิดปิด","type"=>"ENUM","length_value"=>["0","1"]],
			//"partner1"=>["name"=>"คู่ค้า1","type"=>"CHAR","length_value"=>255],
			//"partner2"=>["name"=>"คู่ค้า2","type"=>"CHAR","length_value"=>255],
			//"partner3"=>["name"=>"คู่ค้า3","type"=>"CHAR","length_value"=>255],
			"pay"=>["name"=>"ชำระ","type"=>"FLOAT","length_value"=>[15,2]],
			"payu_json"=>["name"=>"รูปแบบการชำระ","type"=>"VARCHAR","length_value"=>1024],
			"payu_ref_json"=>["name"=>"รูปแบบการชำระอ้างอิง","type"=>"VARCHAR","length_value"=>1024],
			"payu_key_json"=>["name"=>"รูปแบบการชำระอ้างอิง","type"=>"VARCHAR","length_value"=>1024],
			"partner"=>["name"=>"คู่ค้า","type"=>"TEXT","length_value"=>65535],
			"password"=>["name"=>"รหัสผ่าน","type"=>"CHAR","length_value"=>64],
			//--"b"=>"ใบดำ บัญชีดำ","r"=>หยุดขาย ,"y"=>นำเข้ามาขายแต่ต้องระวังและตรวจสอบเป็นพิเศษ,"c"=>ขายปกติ
			"pdstat"=>["name"=>"สถานะ","type"=>"ENUM","length_value"=>["b","r","y","c"]],
			"pn_root"=>["name"=>"คู่ค้า1ราก","type"=>"CHAR","length_value"=>25],
			"pn_key"=>["name"=>"คู่ค้า1อ้างอิง","type"=>"CHAR","length_value"=>25],
			"pn_type"=>["name"=>"ประเภทคู่ค้า","type"=>"ENUM","length_value"=>["s","n"]],
			"post_no"=>["name"=>"รหัสไปรษณี","type"=>"CHAR","length_value"=>25],
			"pos_id"=>["name"=>"เครื่องที่","type"=>"INT","length_value"=>10],
			"price"=>["name"=>"ราคา","type"=>"FLOAT","length_value"=>[15,2]],
			"pricer"=>["name"=>"ราคาคืน","type"=>"FLOAT","length_value"=>[15,2]],
			"product_sku_key"=>["name"=>"รหัสอ้างอิงสินค้า","type"=>"CHAR","length_value"=>25],
			"product_sku_root"=>["name"=>"รหัสรากสินค้า","type"=>"CHAR","length_value"=>25],
			"prop"=>["name"=>"คุณสมบัติ","type"=>"VARCHAR","length_value"=>1024],
			"props"=>["name"=>"คุณสมบัติ.","type"=>"TEXT","length_value"=>65535],
			"province"=>["name"=>"จังหวัด","type"=>"CHAR","length_value"=>80,"charset"=>"thai"],
			"r"=>["name"=>"คืน","type"=>"INT","length_value"=>10],
			"r_"=>["name"=>"เริ่ม","type"=>"INT","length_value"=>10],
			"_r"=>["name"=>"สิ้นสุด","type"=>"INT","length_value"=>10],
			"rca_key_json"=>["name"=>"การชำระอ้างอิง","type"=>"VARCHAR","length_value"=>1024],
			"ref"=>["name"=>"อ้างอิง","type"=>"CHAR","length_value"=>25],
			"ref_ip_"=>["name"=>"อ้างไอพี_","type"=>"CHAR","length_value"=>25],
			"ref__ip"=>["name"=>"อ้าง_ไอพี","type"=>"CHAR","length_value"=>25],
			"ref_stat"=>["name"=>"สถานะอ้างอิง","type"=>"ENUM","length_value"=>["w","s","e","c"]],
			"ref__table"=>["name"=>"อ้างอิง_ตาราง","type"=>"CHAR","length_value"=>25],
			"ref_table_"=>["name"=>"อ้างอิงตาราง_","type"=>"CHAR","length_value"=>25],
			"ref__table_id"=>["name"=>"อ้างอิง_ตารางที่","type"=>"INT","length_value"=>10],
			"ref_table_id_"=>["name"=>"อ้างอิงตารางที่_","type"=>"INT","length_value"=>10],
			"road"=>["name"=>"ถนน","type"=>"CHAR","length_value"=>80,"charset"=>"thai"],
			"s_type"=>["name"=>"รูปแบบการขาย","type"=>"ENUM","length_value"=>["p","w","l","v"]],
			"sex"=>["name"=>"เพศ","type"=>"ENUM","length_value"=>["m","f"]],
			"sq"=>["name"=>"ลำดับ","type"=>"INT","length_value"=>10],
			"sku"=>["name"=>"รหัสภายใน","type"=>"CHAR","length_value"=>25],
			"sku_key"=>["name"=>"รหัสอ้างอิง","type"=>"CHAR","length_value"=>25],
			"skukey"=>["name"=>"รหัสอ้างอิง","type"=>"CHAR","length_value"=>25],
			"skukey1"=>["name"=>"รหัสอ้างอิง","type"=>"CHAR","length_value"=>25],
			"skukey2"=>["name"=>"รหัสอ้างอิง","type"=>"CHAR","length_value"=>25],
			"sku_root"=>["name"=>"รหัสราก","type"=>"CHAR","length_value"=>25],
			"skuroot"=>["name"=>"รหัสราก","type"=>"CHAR","length_value"=>25],
			"skuroot1"=>["name"=>"รหัสราก1","type"=>"CHAR","length_value"=>25],
			"skuroot2"=>["name"=>"รหัสราก2","type"=>"CHAR","length_value"=>25],
			"skuroot_n"=>["name"=>"รหัสราก","type"=>"CHAR","length_value"=>25],
			"skuroot1_n"=>["name"=>"จำนวน1","type"=>"INT","length_value"=>10],
			"skuroot2_n"=>["name"=>"จำนวน2","type"=>"INT","length_value"=>10],
			"size"=>["name"=>"ขนาด","type"=>"INT","length_value"=>10],
			"stkey"=>["name"=>"คลังสินค้าอ้างอิง","type"=>"CHAR","length_value"=>25],
			"stroot"=>["name"=>"คลังสินค้าราก","type"=>"CHAR","length_value"=>25],
			"stkey_"=>["name"=>"คลังอ้างอิง_","type"=>"CHAR","length_value"=>25],
			"stroot_"=>["name"=>"คลังราก_","type"=>"CHAR","length_value"=>25],
			"sum"=>["name"=>"รวม","type"=>"FLOAT","length_value"=>[15,4]],
			//--cancel,wait,success,return
			"stat"=>["name"=>"สถานะ","type"=>"ENUM","length_value"=>["c","w","s","r"]],
			"stath"=>["name"=>"สถานะเปลียน","type"=>"ENUM","length_value"=>["h"]],
			"statnote"=>["name"=>"บันทึกย่อ","type"=>"CHAR","length_value"=>255,"charset"=>"thai"],
			"tax"=>["name"=>"เลขที่ภาษี","type"=>"CHAR","length_value"=>15],
			"tel"=>["name"=>"เบอร์โทีศัพท์","type"=>"CHAR","length_value"=>15],
			"time_id"=>["name"=>"เลขที่กะ","type"=>"INT","length_value"=>10],
			"tms"=>["name"=>"เวลา","type"=>"FLOAT","length_value"=>[12,6]],
			"tp_type"=>["name"=>"การส่งสินค้า","type"=>"ENUM","length_value"=>["0","1"]],
			"tran_ref"=>["name"=>"เลขอ้างอิง","type"=>"CHAR","length_value"=>25],
			"tran_type"=>["name"=>"รูปแบบเข้าออกเงิน","type"=>"ENUM","length_value"=>["sell","min","mout","ret","pay","canc"]],
			"tran_rca_type"=>["name"=>"รูปแบหนี้สินเพิ่มลด","type"=>"ENUM","length_value"=>["sell","pay","ret","canc"]],
			"unit"=>["name"=>"หน่วย","type"=>"CHAR","length_value"=>25],
			"unit_sku_key"=>["name"=>"รหัสอิงอิงหน่วย","type"=>"CHAR","length_value"=>25],
			"unit_sku_root"=>["name"=>"รหัสรากหน่วย","type"=>"CHAR","length_value"=>25],
			"user"=>["name"=>"ผู้ใช้","type"=>"CHAR","length_value"=>25],
			"user_edit"=>["name"=>"ผู้แกไข","type"=>"CHAR","length_value"=>25],
			"user_id"=>["name"=>"เลขที่ผู้ใช้","type"=>"INT","length_value"=>10],
			"userceo"=>["name"=>"ระดับผู้ใช้","type"=>"ENUM","length_value"=>["0","1","2","3","4","5","6","7","8","9"]],
			"w"=>["name"=>"เตือน","type"=>"ENUM","length_value"=>["0","1"]],
			"w1_n"=>["name"=>"จำนวนสินค้าหลักที่รวม","type"=>"INT","length_value"=>10],
			"w2_n"=>["name"=>"จำนวนสินค้าแถมที่รวม","type"=>"INT","length_value"=>10],
			"w1"=>["name"=>"สินค้าหลักที่รวม","type"=>"CHAR","length_value"=>25],
			"w2"=>["name"=>"สินค้าแถมที่รวม","type"=>"CHAR","length_value"=>25],
			"web"=>["name"=>"เว็บไซต์","type"=>"CHAR","length_value"=>255],
			"width"=>["name"=>"กว้าง","type"=>"INT","length_value"=>6],
			"vat"=>["name"=>"มีภาษี","type"=>"ENUM","length_value"=>["0","1"]],
			"vat_p"=>["name"=>"อัตราภาษีมูลค่าเพิ่ม","type"=>"FLOAT","length_value"=>[3,2]],
			"vat_n"=>["name"=>"ภาษี","type"=>"FLOAT","length_value"=>[10,4]],
			
			"tr"=>["name"=>"ช่วงเวลา","type"=>"INT","length_value"=>10],
			"bi_c"=>["name"=>"จำนวนแถว bill_in","type"=>"INT","length_value"=>10],
			"bil_c"=>["name"=>"จำนวนแถว bill_in_list","type"=>"INT","length_value"=>10],
			"bs_c"=>["name"=>"จำนวนแถว bill_sell","type"=>"INT","length_value"=>10],
			"bsl_c"=>["name"=>"จำนวนแถว bill_sell_list","type"=>"INT","length_value"=>10],
			"bp_c"=>["name"=>"จำนวนแถว bill_sell","type"=>"INT","length_value"=>10],
			"bpl_c"=>["name"=>"จำนวนแถว bill_sell_list","type"=>"INT","length_value"=>10],
			"bir_"=>["name"=>"เริ่ม bill_in","type"=>"INT","length_value"=>10],
			"bi_r"=>["name"=>"สิ้นสุด bill_in","type"=>"INT","length_value"=>10],
			"bilr_"=>["name"=>"เริ่ม bill_in_list","type"=>"INT","length_value"=>10],
			"bil_r"=>["name"=>"สิ้นสุด bill_in_list","type"=>"INT","length_value"=>10],
			"bpr_"=>["name"=>"เริ่ม bill_rca","type"=>"INT","length_value"=>10],
			"bp_r"=>["name"=>"สิ้นสุด bill_rca","type"=>"INT","length_value"=>10],
			"bplr_"=>["name"=>"เริ่ม bill_rca_list","type"=>"INT","length_value"=>10],
			"bpl_r"=>["name"=>"สิ้นสุด bill_rca_list","type"=>"INT","length_value"=>10],
			
			"bsr_"=>["name"=>"เริ่ม bill_sell","type"=>"INT","length_value"=>10],
			"bs_r"=>["name"=>"สิ้นสุด bill_sell","type"=>"INT","length_value"=>10],
			"bslr_"=>["name"=>"เริ่ม bill_sell_list","type"=>"INT","length_value"=>10],
			"bsl_r"=>["name"=>"สิ้นสุด bill_sell_list","type"=>"INT","length_value"=>10]
			
		];
		$this->home=0;
		$this->dir=[];
	}
	protected function key(string $type="key",int $rid_length=7):string{
		if($type=="key"){
			return time()."".$this->rid($rid_length);
		}
		return time()."".$this->rid(7);
	}
	protected function rid(int $length=15):string{
		$t="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$re="";
		for($i=0;$i<$length;$i++){
			$re.=$t[rand(0,61)];
		}
		return $re;
	}
	protected function dbConnect(): ?object{
		$conn=null;
		try{
				$conn = new PDO("mysql:host=".$this->cf["server"].";dbname=".$this->cf["database"].";charset=utf8", $this->cf["user"],$this->cf["password"]);
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
		}catch(PDOException $e){
			return $e;
		}
		return $conn;
	}
	public static  function getStringSqlSet(string $value_set):string{
		if(strlen(trim($value_set))==0){
			$value_set="NULL";
		}else{			
			$value_set=str_replace("\\","\\\\",$value_set);	
			$value_set=str_replace("$","\$",$value_set);	
			$value_set="\"".str_replace("\"","\\\"",$value_set)."\"";
		}
		return $value_set;
	}
	public function metMnSql(array $sql,array $se):array{
		/* $sql ต้องระวัง SQL  Injection  เนื่องจาก function นี้ ใช้ PDO->query()
		 * $se=["ชื่อลำดับใน sql ที่จะให้ return ค่า กลับ",,] */
		$stmt=[];
		$re=$this->re;
		$conn=$this->dbConnect();
		
		if(get_class($conn)=="PDO"){
			$conn->beginTransaction();
			try{
				foreach($sql as $k=>$v){
					$stmt[$k] = $conn->query($v);
					$re["data"][$k]=[];
				}
				
				foreach($se as $k=>$v){
					$i=1;
					while ($row = $stmt[$v]->fetch(PDO::FETCH_ASSOC)) {
						array_push($re["data"][$v],$row);
						$re["count"][$v]=$i;
						$i+=1;
					}
				}
				$re["result"]=true;
				try{
					$conn->commit();
				}catch(PDOException $e){

				}
			}catch(PDOException $e){
				$mr="";
				if(strlen(trim($e))!=0){
					$mr.=$e->getMessage().";";
				}	
				try{
					$conn->rollBack();
				}catch(PDOException $e){
					//print_r($e);
					$mr.=$e->getMessage().";";
				}

				$re["message_error"]=$mr;

				
				//print_r($e->getMessage());
				//print_r($sql);
				
				//$conn->beginTransaction();
				
			}
			$re["connect"]=true;
		}else if(get_class($conn)=="PDOException"){
			//echo get_class($conn)."****";
			$re["connect_error"]="ไม่สามารถติดต่อฐานข้อมูลได้";		
			$re["message_error"]="ไม่สามารถติดต่อฐานข้อมูลได้";
		}
		//var_dump($re);
		return $re;	
	}
	protected  function ref(string $table,string $sku_key,string $value):string{
		$tx="INSERT IGNORE INTO `".$table."_ref`  
			SELECT * FROM `".$table."` WHERE  `".$table."`.`".$sku_key."` ='".$value."' ;";
		return $tx;
	}
	protected  function coppyTo(string $table_,string $_table,string $sku_key,?string $value_type,string $value):string{
		//--ระวัง $ ' " \
		$v="'".$value."'";
		if($value_type=="int"){
			$v=(int) $value;
		}
		$tx="INSERT IGNORE INTO `".$_table."`  
			SELECT * FROM `".$table_."` WHERE  `".$table_."`.`".$sku_key."` =".$v." LIMIT 1 ;";
		return $tx;
	}
	protected function checkMe(string $passverf):bool{
		$r=false;
		$sql=[];
		$sql["result"]="SELECT `password` FROM `user` WHERE `sku_root`='".$_SESSION["sku_root"]."' LIMIT 1";
		$re=$this->metMnSql($sql,["result"]);
		if($re["result"]){
			if(is_array($re["data"])){
				if(count($re["data"]["result"])==1
					&&password_verify($passverf,$re["data"]["result"][0]["password"])){
					$r=true;
				}
			}
		}
		return $r;
	}
	protected function pageHead(array $data){
		$title=(isset($data["title"]))?$data["title"]:"DIYPOS";
		$r_more=(isset($data["r_more"]))?$data["r_more"]:[];
		echo '<!DOCTYPE html>
					<html xmlns="http://www.w3.org/1999/xhtml" lang="th">
					<head>
					<title>'.$title.'</title>
					<meta charset="utf-8">
					<meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=yes">
					<meta name="description" content="'.$title.'">
					<link rel="manifest" href="'.(isset($data["manifest"])?$data["manifest"]:"set/manifest.json").'">
<link rel="apple-touch-icon" href="img/pwa/diypos_128.png">   
<meta name="theme-color" content="black"/>  
<meta name="apple-mobile-web-app-capable" content="yes">  
<meta name="apple-mobile-web-app-status-bar-style" content="black"> 
<meta name="apple-mobile-web-app-title" content="D I Y P O S"> 
					<meta name="msapplication-TileImage" content="'.(isset($data["titleimg"])?$data["titleimg"]:"img/pwa/diypos_128.png").'">  
					<link rel="icon"   type="image/png" href="'.(isset($data["icon"])?$data["icon"]:"img/favicon.png").'" />
					<link rel="stylesheet" type="text/css" href="css/css.css">'.$this->pageHeadCss($data).'
					<script src="js/main.js" type="text/javascript"></script>'.$this->pageHeadJs($data).'
			</head><body>
			<div id="film"></div>
			<script type="text/javascript">let M=new main();let G=new gpu();'.$this->pageHeadOb($data).'</script>
			';
		if(!isset($data["dir"])){
			$this->topBar($r_more);
		}
	}
	private function pageHeadJs(array $data):string{
		$re="";
		if(isset($data["js"])){
			for($i=0;$i<count($data["js"]);$i+=2){
				$re.="\n					<script src=\"js/".$data["js"][$i].".js\" type=\"text/javascript\"></script>";
			}
		}
		return $re;
	}
	private function pageHeadCss(array $data):string{
		$re="";
		if(isset($data["css"])){
			for($i=0;$i<count($data["css"]);$i++){
				$re.="\n					<link rel=\"stylesheet\" type=\"text/css\" href=\"css/".$data["css"][$i].".css\">";
			}
		}
		return $re;
	}
	private function pageHeadOb(array $data):string{
		$re="";
		if(isset($data["js"])){
			for($i=0;$i<count($data["js"]);$i+=2){
				$job=$data["js"][$i];
				$a=explode("/",$data["js"][$i]);
				if(count($a)>1){
					$job=$a[count($a)-1];
				}
				$re.="let ".$data["js"][$i+1]."=new ".$job."(M);";
			}
		}
		$re.="M.run();";
		if(isset($data["run"])){
			for($i=0;$i<count($data["run"]);$i++){
				$re.=$data["run"][$i].".run();";
			}
		}
		return $re;
	}
	private function topBar(array $r_more):void{
		if($this->home==0){
			echo '<div class="topbar">';
			$this->avatarWrite();
			echo '<a onclick="window.history.back()" title="กลับ"><span class="back">&nbsp;🔙&nbsp;</span></a>  
			 <a href="index.php" title="ไปหน้าหลัก"> 🏠 หน้าหลัก</a>';
			 foreach($this->dir as $v){
				 echo ' » '.$v;
			 }
			 echo '	</div>';
			 if(count($r_more)>0){
				 $this->rMore($r_more);
			 }
		}
	}
	protected function rMore(array $data):void{
		//print_r($data);
		//--tap ,spacebar เป็น childNodes ของ .menu_more อยู่ ห้ามเอาอะไรมาใส่ หรือลบ 
		echo '<div class="menu_more menu_more_min" >	
		
			<div class="click0" onclick="G.rMore(this)"></div>	
			<!--<xdiv class="menu_more_min_r">-->
			<div class="menu_more_auto">
				<div>';
		for($i=0;$i<count($data["menu"]);$i++){
			$a=($data["menu"][$i]["b"]==$data["active"])?" class=\"menu_more_active\"":"";
			$b=($a!="")?"👀 ":"";
			echo '<div'.$a.'>';
				if($data["menu"][$i]["link"]!=""){
					echo '<a href="'.$data["menu"][$i]["link"].'">'.$b.''.$data["menu"][$i]["name"].'</a>';
				}else if(isset($data["menu"][$i]["html"])){
					echo $data["menu"][$i]["html"];
				}
			echo '</div>';
		}
		echo '	</div>
			</div>
		</div>';
	}
	protected function avatarWrite(string $page=null){
		if(isset($_SESSION["sku_root"])){
			$cs=($page=="home")?" style=\"float:none;display:block;text-align:right;\"":"";
			echo '<div class="avatar"'.$cs.'><a href="?a=me&amp;b=time">👤 '.$_SESSION["name"].' '.$_SESSION["lastname"].'</a></div>';
		}
	}
	protected function addDir(string $href,string $text){
		if(strlen($href)>0){
			$t='<a href="'.$href.'">'. $text.'</a>';
		}else{
			$t=$text;
		}
		array_push($this->dir,$t);
	}
	protected function btMore(array $data):void{
		echo '<div class="bt_more">';
		foreach($data as $k=>$v){
			echo '<span><a href="'.$v["link"].'">'.$v["name"].'</a></span>';
		}
		echo '</div>';
	}
	protected function pageFoot(){
		//print_r($_SESSION);
		echo '</body></html>';
	}
	protected function checkSet(string $table,array $dt,string $type="post",array $not_null=[],array $alias=[]):array{
		$re=["result"=>true,"message_error"=>""];
		//$dt=["get"=>[],"post"=>[]];
		if(count($alias)>0){
			foreach($alias as $k=>$v){
				if(isset($_POST[$k])){
					$_POST[$v]=$_POST[$k];
					array_push($dt[$type] ,$v);
				}else{
					$_POST[$v]=null;
				}
			}
		}
		//print_r($_POST);
		foreach($dt[$type] as $v){
			$ry="";
			if($type=="post"){
				if(isset($_POST[$v])){
					$ry=$_POST[$v];
				}
			}else{
				if(isset($_GET[$v])){
					$ry=$_GET[$v];
				}
			}
			if(!isset($ry)){
				$re["result"]=false;
				$re["message_error"]="ข้อมูล \"".$v."\"  ไม่มีส่งมา" ;
				break;
			}else{
				if(isset($this->tb[$table])){
					$tb=$this->tb[$table];
					if(strlen(trim($ry))==0&&isset($tb["not_null"])&&in_array($v,$tb["not_null"])){
						$nm=$this->fills[$v]["name"];
						$re["result"]=false;
						$re["message_error"]="ข้อมูล \"".$nm."\"  ต้องไม่ว่าง" ;
						break;
					}else if(strlen(trim($ry))>0){
						$name=["name","brand_name","no","alley","road","distric","country","province","note","lastname"];
						$sku=["sku","unit","ref","sku_root"];
						$tax=["tax","tel","fax","post_no"];
						$url=["web"];
						$idc=["idc"];
						$barcode=["barcode"];
						$password=["password"];
						$money=["price","cost","min"];
						$int=["drawers_id"];
						$float=["vat_p"];
						$enum = ["data_type","s_type","pn_type","od_type","tp_type","bill_type","sex","mb_type"];
						$json_arr = ["prop","partner"];
						$json= ["payu_json"];
						$province=["bill_no"];
						$date=["birthday"];
						$disc=["disc"];
						$ip=["ip"];
						if(in_array($v,$sku)){
							$pt="/^[0-9a-zA-Z-+\.&\/]{1,25}$/";
							if(!preg_match($pt,$ry)) {
								$re["result"]=false;
								$re["message_error"]=$this->fills[$v]["name"]." ไม่อยู่ในรูปแบบ 0-9a-zA-Z-+.&/ 1-25 ตัว";
								break;
							}
						}else if(in_array($v,$barcode)){
							$pt="/^[0-9]{2,24}$/";
							if(!preg_match($pt,$ry)) {
								$re["result"]=false;
								$re["message_error"]=$this->fills[$v]["name"]." ไม่อยู่ในรูปแบบ 0-9 จำนวน 2-24 ตัว";
								break;
							}else{
								$l=strlen(trim($ry));
								if($l!=13&&$l%2==1){
									$re["result"]=false;
									$re["message_error"]=$this->fills[$v]["name"]."  ระบบ ใช้ ITF (Interleaved 2 of 5) จำนวนหลักต้องเป็นหลักคู่ เช่น 01,0020,112036";
								}
							}
						}else if(in_array($v,$password)){
							$pt="/^[0-9a-zA-Z]{8,32}$/";
							if(!preg_match($pt,$ry)) {
								$re["result"]=false;
								$re["message_error"]=$this->fills[$v]["name"]." ไม่อยู่ในรูปแบบ 0-9a-zA-Z  จำนวน 8-32 ตัว";
								break;
							}
						}else if(in_array($v,$money)){
							$pt="/^(([0-9])*|([0-9]*\.[0-9]{1,4}))$/";
							if(!preg_match($pt,$ry)) {
								$re["result"]=false;
								$re["message_error"]=$this->fills[$v]["name"]." ไม่อยู่ในรูปแบบ จำนวนเงิน xxxx.xx";
								break;
							}
						}else if(in_array($v,$tax)){
							$pt="/^[0-9]{1,15}$/";
							if(!preg_match($pt,$ry)) {
								$re["result"]=false;
								$re["message_error"]=$this->fills[$v]["name"]." ไม่อยู่ในรูปแบบ ".$this->fills[$v]["name"];
								break;
							}
						}else if(in_array($v,$name)){
							$max=$this->fills[$v]["length_value"]-4;
							if(strlen($ry)>$max) {
								$re["result"]=false;
								$re["message_error"]=$this->fills[$v]["name"]." ยาวเกิน ".$max." แต่ข้อความคุณยาว ".strlen($ry);
								break;
							}
						}else if(in_array($v,$url)){
							$pt="/^http(s?):\/\/.*(.){1}.*$/";
							if(!preg_match($pt,$ry)) {
								$re["result"]=false;
								$re["message_error"]=$this->fills[$v]["name"]." ไม่อยู่ในรูปแบบ ".$this->fills[$v]["name"];
								break;
							}
						}else if(in_array($v,$json_arr)){
							if(strlen(trim($ry))>2){
								$prop =trim($ry);
								$d = explode(",,",substr($prop,1,-1));
								$len = 25;
								//$check = true;
								$pt="/^[0-9a-zA-Z-+\.&\/]{1,25}$/";
								for($i = 0;$i<count($d);$i++){
									if(!preg_match($pt,$d[$i])){
										//$check = false;
										$re["result"]=false;
										echo "---".$d[$i]."--";
										$re["message_error"]="ค่า key_root ของ ".$this->fills[$v]["name"]." บางตัว ไม่อยู่ในรูปแบบ 0-9a-zA-Z-+.&/ 1-25 ตัว";
										break;
									}									
								}
							}
						}else if(in_array($v,$json)){
							if(!$this->isJSON($ry)){
								$re["result"]=false;
								$re["message_error"]=$this->fills[$v]["name"]." ไม่อยู่ในรูปแบบ";
								break;
							}
						}else if($v=="email"&&!filter_var($ry, FILTER_VALIDATE_EMAIL)){
							$re["result"]=false;
							$re["message_error"]=$this->fills[$v]["name"]." ไม่อยู่ในรูปแบบ";
							break;
						}else if($v=="userceo"&&!in_array($ry,$this->fills[$v]["length_value"])){
							$re["result"]=false;
							$re["message_error"]=$this->fills[$v]["name"]." ไม่อยู่ในรูปแบบ";
							break;
						}else if(in_array($v,$enum)){
							if(!in_array($ry,$this->fills[$v]["length_value"])){
								$re["result"]=false;
								$re["message_error"]=$this->fills[$v]["name"]." ไม่อยู่ในรูปแบบ";
							}
						}else if(in_array($v,$int)){
							$pt="/^[0-9]{1,11}$/";
							if(!preg_match($pt,$ry)) {
								$re["result"]=false;
								$re["message_error"]=$this->fills[$v]["name"]." ไม่อยู่ในรูปแบบ จำนวนเต็ม";
								break;
							}
						}else if(in_array($v,$float)){
							$pt="/^(([0-9])*|([0-9]*\.[0-9]{1,2}))$/";
							if(!preg_match($pt,$ry)) {
								$re["result"]=false;
								$re["message_error"]=$this->fills[$v]["name"]." ไม่อยู่ในรูปแบบ จำนวน xx.xx";
								break;
							}
						}else if(in_array($v,$province)){
							$max=$this->fills[$v]["length_value"]-4;
							if(strlen($ry)>$max) {
								$re["result"]=false;
								$re["message_error"]=$this->fills[$v]["name"]." ยาวเกิน ".$max." แต่ข้อความคุณยาว ".strlen($ry);
								break;
							}
						}else if(in_array($v,$date)){
							$pt="/^([1-9])[0-9]{3}-(0|1)[0-9]-(0|1|2|3)[0-9]$/";
							if(!preg_match($pt,$ry)) {
								$re["result"]=false;
								$re["message_error"]=$this->fills[$v]["name"]." วันที่ไม่อยู่ในรูปแบบ yyyy-mm-dd";
								break;
							}
						}else if(in_array($v,$idc)){
							$pt="/^[1-8]{1}[0-9]{12}$/";
							if(!preg_match($pt,$ry)) {
								$re["result"]=false;
								$re["message_error"]=$this->fills[$v]["name"]." ไม่อยู่ในรูปแบบ ^[1-8]{1}[0-9]{12}\$";
								break;
							}else{
								$s=0;
								for($i=0;$i<12;$i++){
									$s+=((int) $ry[$i])*(13-$i);
								}
								$u=11-($s%11);
								if((int) $ry[12]!=$u){
									$re["result"]=false;
									$re["message_error"]="เลขบัตรประชาชนไม่ถูกต้อง เลขหลักสุดท้าย ควรเป็นเลข ".$u." ไม่ใช่เลข ".$ry[12];
									break;
								}
							}
						}else if(in_array($v,$disc)){
							$max=$this->fills[$v]["length_value"]-4;
							if(strlen($ry)>$max) {
								$re["result"]=false;
								$re["message_error"]=$this->fills[$v]["name"]." ยาวเกิน ".$max." แต่ข้อความคุณยาว ".strlen($ry);
								break;
							}
						}
					}else if(in_array($v,$not_null)){
						$nm=$this->fills[$v]["name"];
						$re["result"]=false;
						$re["message_error"]="ข้อมูล \"".$nm."\"  ต้องไม่ว่าง" ;
						break;
					}
				}
			}
		}
		$this->nullSet($dt,$type);
		return $re;
	}
	protected function isMoney(string $money):bool{
		$re=false;
		$pt="/^([0-9]+.)?[0-9]{1,2}$/";
		if(preg_match($pt,$money)) {
			$re=true;
		}
		return $re;
	}
	protected function isJSON(string $json):bool{
		$re=false;
		$pt="/^{.{5,}}$/";
		$a=json_decode($json);
		if (json_last_error() === JSON_ERROR_NONE && preg_match($pt,trim($json))) {
			$re=true;
		}
		return $re;
	}
	private function nullSet(array $dt,string $type="post"){
		foreach($dt[$type] as $v){
			if($type=="post"){
				if(!isset($_POST[$v])){
					$_POST[$v]="";
				}else if(strlen(trim($_POST[$v]))==0){
					$_POST[$v]="";
				}
			}else if($type=="get"){
				if(!isset($_GET[$v])){
					$_GET[$v]="";
				}
			}
		}
	}
	protected function isSKU(string $sku):bool{
		if(preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$sku)){
			return true;
		}else{
			return false;
		}
	}
	protected function page(?int $count,?int $per,int $page,string $qury):void{
		$count=($count===0)?1:$count;
		$per=($per===0)?20:$per;
		$pages=ceil($count/$per);
		$size=strlen("".$page);
		echo '<div class="page c">
		<label for="idpageview">หน้าที่</label> <input id="idpageview" class="c" type="number" min="1" value="'.$page.'" size="'.$size.'" style="width:50px;" onkeyup="F.go(event,\''.$qury.'\')"> / '.$pages.' 
		<input  onclick="F.go(null,\''.$qury.'\')" type="button" value="ไป" />
		</div>';
	}
	protected function setPageR():int{
		if(!isset($_GET["page"])){
			return 1;
		}else if(!preg_match("/^[0-9]{1,10}$/",$_GET["page"])){
			return 1;
		}else{
			return $_GET["page"];
		}
	}
	protected function ago(int $s):string{
		$re="";
		if($s<60){
			$re=$s." วินาที";
		}else if($s<60*60){
			$re=floor($s/60)." นาที";
		}else if($s<60*60*24){
			$re=floor($s/(60*60))." ชั่วโมง";
		}else if($s<60*60*24*30.5){
			$re=floor($s/(60*60*24))." วัน";
		}else{
			$re=floor(($s/(60*60*24))/7)." สัปดาห์";
		}
		return $re;
	}
	protected function ago2(int $ss):string{
		$re="";
		$h="00";
		$m="00";
		$s="00";
		$ah=floor($ss/3600);
		$am=$ss%3600;
		$as=$ss%60;
		if($as<10){
			$s="0".$as;
		}else{
			$s="".$as;
		}
		if($am<600){
			$m="0".(($am-$as)/60);
		}else{
			$m=(($am-$as)/60);
		}
		if($ah<10){
			$h="0".$ah;
		}else{
			$h=$ah;
		}
		return "".$h.":".$m.":".$s."";
	}
	protected function billNote(string $type,string $note,string $nt2=""):string{
		$not=$note;
		$note=htmlspecialchars($note);
		$t="";
		$nt2_old=$nt2;
		if($nt2!=""){
			$nt2="<span class=\"pin\"> 📌  ".htmlspecialchars($nt2)."</span>";
		}
		if($type=="b"){
			$a=explode("/",$not);
			if(count($a)==3){
				//$t.="💰 ซื้อเข้า 🏭".$a[0]." 📅".$a[1]." 🧾".$a[2];
			}
			$t.="💵 ".$note.'🧾'.$nt2_old;
		}else if($type=="c"){
			$t.="❌ ยกเลิก ".$note."".$nt2;
		}else if($type=="r"){
			$t.="↩️ คืนสินค้า ".$note."".$nt2;
		}else if($type=="m"){
			$t.='📥 ย้ายเข้า '.$note.''.$nt2;
		}else if($type=="mm"){
			$t.='💦 แตกสินค้า '.$note.''.$nt2;
		}else if($type=="x"){
			$t.="🗑 ลบคลังสินค้า ".$note."".$nt2;
		}
		if(empty($t)){
			$t=$note;
		}
		return $t;
	}
	static function oa(string $tx):bool{
		if(in_array($tx,$_SESSION["oa"])){
			return true;
		}
		return false;
	}
	protected function os(string $tx,string $doc):string{
		if(in_array($tx,$_SESSION["oa"])){
			return $doc;
		}
		return "";
	}
	protected function findIPv4():string{
		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		@socket_connect($sock, "8.8.8.8", 80);
		socket_getsockname($sock, $name);
		return $name;
	}
	protected function userIPv4():string{
		$re=$_SERVER['REMOTE_ADDR'];
		if($re==$_SERVER["SERVER_ADDR"]){
			$re=$this->findIPv4();
		}
		return $re;
	}
	protected function createBcWLV(string $bc,float $n_wlv):string{
		$re="";
		$bc_len=strlen($bc);
		if(strlen(trim($bc))>0){
			$bc_len_tx=strlen($bc)<10?"0".strlen($bc):strlen($bc);
			$a=(string) $n_wlv*1;
			$b=explode(".",$a);
			if(count($b)==1){
				$b[1]="";
			}
			$n_tx=$b[0];
			$float_tx=$b[1];	
			$n_len_tx=strlen($n_tx)<10?"0".strlen($n_tx):strlen($n_tx);
			if(($bc_len+strlen($n_tx)+strlen($float_tx))%2!=0){
				$n_tx="0".$b[0];
				$n_len_tx=strlen($n_tx)<10?"0".strlen($n_tx):strlen($n_tx);
			}
			$re=$bc."".$n_tx."".$float_tx."".$n_len_tx."".$bc_len_tx;
		}
		return $re;
	}
	protected function img2Base64(string $file):string{
		$re="";
		if(!file_exists($file)){
			$file="img/pos/64x64_null.png";
		}
		$imagedata = file_get_contents($file);
		if(strlen(trim($file))>0){
			$re = "data:".mime_content_type($file).";base64,".base64_encode($imagedata);
		}
		return $re;
	}
	protected function setDateR(string $date,string $time=""):string{
		$re="";
		$tm="";
		if(strlen($time)>0){
			$tm=" ".$time;
		}
		if(strlen($date)>0){
			$re= $date."".$tm;
		}
		return $re;
	}
	protected function delImgs(array $files):void{
		$sq=[16,32,64,128,256,512,1024];
		for($g=0;$g<count($files);$g++){
			$file=$this->gallery_dir."/".$files[$g];
			echo $file."*";
			if(file_exists($file)){
				unlink($file);
			}
			for($i=0;$i<count($sq);$i++){
				$file=$this->gallery_dir."/".$sq[$i]."x".$sq[$i]."_".$files[$g];
				if(file_exists($file)){
					unlink($file);
				}
			}
		}
	}
	protected function jsonDocCut0ToArrayKeyDoc(string $json_doc):string{
		$h=[];
		$a=json_decode($json_doc,true);
		foreach($a as $k=>$v){
			if($v > 0){
				$h[$k]=$v;
			}
		}
		$c=json_encode($h);
		return $c;
	}
	protected function jsonDocToArrayKeyDoc(string $json_doc):string{
		$a=json_decode($json_doc,true);
		$b=array_keys($a);
		$c=json_encode($b);
		return $c;
	}
}
?>
