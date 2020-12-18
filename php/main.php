<?php
class main{
	private $re;
	public function __construct(){
		date_default_timezone_set ("Asia/Bangkok" );
		$this->cf=["server"=>CF["server"],"database"=>CF["database"],"user"=>CF["user"],"password"=>CF["password"]];
		$this->re=[
			"connect"=>false,
			"connect_error"=>"",
			"result"=>false,
			"count"=>[],
			"data"=>[],
			"message_error"=>""
		];
		$this->tb=[
			"product"=>[
				"name"=>"product",
				"column"=>["id","sku","barcode","sku_key","sku_root","name","cost","price",
					"unit","skuroot1","skuroot1_n","skuroot2","skuroot2_n","pdstat","disc","statnote","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL","pdstat"=>"c"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],
				"not_null"=>["name","sku_root"],
				"primary"=>"sku_root",
				"unique"=>["sku","name","barcode"],
				"index"=>["pdstat","skuroot1","skuroot2"]
			],
			"product_ref"=>[
				"name"=>"product_ref",
				"column"=>["id","sku","barcode","sku_key","sku_root","name","cost","price",
					"unit","skuroot1","skuroot1_n","skuroot2","skuroot2_n","pdstat","disc","statnote","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"not_null"=>["name","sku_root"],
				"primary"=>"sku_key",
				"index"=>["sku_root"]
			],
			"unit"=>[
				"name"=>"unit",
				"column"=>["id","sku","sku_key","sku_root","name","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],
				"primary"=>"sku_root",
				"not_null"=>["name"],
				"unique"=>["name","sku"]
			],
			"unit_ref"=>[
				"name"=>"unit_ref",
				"column"=>["id","sku","sku_key","sku_root","name","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"primary"=>"sku_key",
				"index"=>["sku_root"]
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
			"bill_sell"=>[
				"name"=>"bill_sell",
				"column"=>["id","sku","n","cost","costr","price","pricer","user","user_edit","stat","stath","note","w","r_","_r","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL","pricer"=>0,"costr"=>0],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],	
				"primary"=>"sku",
				"index"=>["user","stat","stath","w","date_reg"]
			],
			"bill_sell_list"=>[
				"name"=>"bill_sell_list",
				"column"=>["id","sku","bill_in_list_id","lot","product_sku_key","product_sku_root",
					"n","c","u","r","h","unit_sku_key","unit_sku_root","note","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL","r"=>0,"h"=>0],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],	
				"primary"=>"id",
				"index"=>["sku","bill_in_list_id","lot","product_sku_key","product_sku_root"]
			],
			"bill_in"=>[
				"name"=>"bill_in",
				"column"=>["id","in_type","sku","lot_from","lot_root","bill","n","sum","changto","user","user_edit","note","stkey_","stroot_",
				"r_","_r","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],				
				"primary"=>"sku",
				"index"=>["in_type","changto","user","note","stkey_","stroot_","r_","_r"]
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
			"bill_in_list"=>[
				"name"=>"bill_in_list",
				"column"=>["id","stkey","stroot","bill_in_sku","lot","product_sku_key","product_sku_root","name","n","balance","sum","sq","unit_sku_key","unit_sku_root","note","idkey"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP"],
				"primary"=>"id",
				"index"=>["lot","stkey","stroot","bill_in_sku","product_sku_key","product_sku_root","balance"]
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
			"s"=>[
				"name"=>"s",
				"column"=>["id","tr",
					"bi_c","bil_c","bs_c","bsl_c",
					"bir_","bi_r","bsr_","bs_r",
					"bilr_","bil_r","bslr_","bsl_r","modi_date","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","modi_date"=>"NULL"],
				"on"=>["modi_date"=>"ON UPDATE CURRENT_TIMESTAMP"],
				"primary"=>"tr"
			],
			"test"=>[
				"name"=>"test",
				"column"=>["id","tms","note","date_reg"],
				"default"=>["date_reg"=>"CURRENT_TIMESTAMP","tms"=>00000.000000],
				"primary"=>"id"
			]
		];
		$this->fills=[
			"amount"=>["name"=>"จำนวน","type"=>"INT","length_value"=>10],
			"barcode"=>["name"=>"รหัสแท่ง","type"=>"CHAR","length_value"=>80],
			"balance"=>["name"=>"คงเหลือ","type"=>"INT","length_value"=>10],
			"bill"=>["name"=>"ใบ","type"=>"CHAR","length_value"=>25],
			"bill_in_id"=>["name"=>"ที่นำเขา","type"=>"INT","length_value"=>10],
			"bill_in_list_id"=>["name"=>"ที่นำเขา","type"=>"INT","length_value"=>10],
			"bill_in_sku"=>["name"=>"รหัสภายในใบนำเข้าสินค้า","type"=>"CHAR","length_value"=>25],
			//--0=เงินม1=สินค้าตัวเดิม
			"changto"=>["name"=>"เปลี่ยนเป็น","type"=>"ENUM","length_value"=>["0","1"]],
			//"barcode1"=>["name"=>"รหัสแท่งย่อยสุด1","type"=>"CHAR","length_value"=>80],
			//"barcode2"=>["name"=>"รหัสแท่งย่อยสุด2","type"=>"CHAR","length_value"=>80],
			//"barcode1_n"=>["name"=>"จำนวนที่แบ่ง1","type"=>"INT","length_value"=>10],
			//"barcode2_n"=>["name"=>"จำนวนที่แบ่ง_2","type"=>"INT","length_value"=>10],
			"cost"=>["name"=>"ต้นทุน","type"=>"FLOAT","length_value"=>[15,4]],
			"costr"=>["name"=>"ต้นทุนคืน","type"=>"FLOAT","length_value"=>[15,4]],
			"costa"=>["name"=>"ต้นทุนเฉลี่ย","type"=>"FLOAT","length_value"=>[15,4]],
			"disc"=>["name"=>"รายละเอียด","type"=>"VARCHAR","length_value"=>1000,"charset"=>"thai"],
			"date_reg"=>["name"=>"วันที่สร้าง","type"=>"TIMESTAMP"],
			"email"=>["name"=>"อีเมล","type"=>"CHAR","length_value"=>30],
			"h"=>["name"=>"เปลี่ยน","type"=>"INT","length_value"=>10],
			"id"=>["name"=>"ที่","type"=>"INT","length_value"=>10],
			"idkey"=>["name"=>"ที่อ้างอิง","type"=>"INT","length_value"=>10],
			//--"buy","cancel","return",move,x,delete
			"in_type"=>["name"=>"ประเภทการเข้า","type"=>"ENUM","length_value"=>["b","c","r","m","x","d","mm"]],
			"lastname"=>["name"=>"นามสกุล","type"=>"CHAR","length_value"=>255,"charset"=>"thai"],
			"lot"=>["name"=>"งวด","type"=>"CHAR","length_value"=>25],
			"lot_from"=>["name"=>"งวดอ้างอิง","type"=>"CHAR","length_value"=>25],
			"lot_root"=>["name"=>"งวดราก","type"=>"CHAR","length_value"=>25],
			"m"=>["name"=>"สินค้ารากที่แตก","type"=>"CHAR","length_value"=>25],
			"m_n"=>["name"=>"จำนวนสินค้ารากที่แตก","type"=>"INT","length_value"=>10],
			"modi_date"=>["name"=>"วันปรับปรุง","type"=>"TIMESTAMP",],
			"n"=>["name"=>"จำนวน","type"=>"INT","length_value"=>10],
			"c"=>["name"=>"จำนวนที่ได้ตัด","type"=>"INT","length_value"=>10],
			"u"=>["name"=>"จำนวนไมได้ตัด","type"=>"INT","length_value"=>10],
			"name"=>["name"=>"ชื่อ","type"=>"CHAR","length_value"=>255,"charset"=>"thai"],
			"note"=>["name"=>"บันทึกย่อ","type"=>"CHAR","length_value"=>255,"charset"=>"thai"],
			//"partner1"=>["name"=>"คู่ค้า1","type"=>"CHAR","length_value"=>255],
			//"partner2"=>["name"=>"คู่ค้า2","type"=>"CHAR","length_value"=>255],
			//"partner3"=>["name"=>"คู่ค้า3","type"=>"CHAR","length_value"=>255],
			"password"=>["name"=>"รหัสผ่าน","type"=>"CHAR","length_value"=>64],
			//--"b"=>"ใบดำ บัญชีดำ","r"=>หยุดขาย ,"y"=>นำเข้ามาขายแต่ต้องระวังและตรวจสอบเป็นพิเศษ,"c"=>ขายปกติ
			"pdstat"=>["name"=>"สถานะ","type"=>"ENUM","length_value"=>["b","r","y","c"]],
			"price"=>["name"=>"ราคา","type"=>"FLOAT","length_value"=>[15,2]],
			"pricer"=>["name"=>"ราคาคืน","type"=>"FLOAT","length_value"=>[15,2]],
			"product_sku_key"=>["name"=>"รหัสอ้างอิงสินค้า","type"=>"CHAR","length_value"=>25],
			"product_sku_root"=>["name"=>"รหัสรากสินค้า","type"=>"CHAR","length_value"=>25],
			"r"=>["name"=>"คืน","type"=>"INT","length_value"=>10],
			"r_"=>["name"=>"เริ่ม","type"=>"INT","length_value"=>10],
			"_r"=>["name"=>"สิ้นสุด","type"=>"INT","length_value"=>10],
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
			"stkey"=>["name"=>"คลังสินค้าอ้างอิง","type"=>"CHAR","length_value"=>25],
			"stroot"=>["name"=>"คลังสินค้าราก","type"=>"CHAR","length_value"=>25],
			"stkey_"=>["name"=>"คลังอ้างอิง_","type"=>"CHAR","length_value"=>25],
			"stroot_"=>["name"=>"คลังราก_","type"=>"CHAR","length_value"=>25],
			"sum"=>["name"=>"รวม","type"=>"FLOAT","length_value"=>[15,4]],
			//--cancel,wait,success,return
			"stat"=>["name"=>"สถานะ","type"=>"ENUM","length_value"=>["c","w","s","r"]],
			"stath"=>["name"=>"สถานะเปลียน","type"=>"ENUM","length_value"=>["h"]],
			"statnote"=>["name"=>"บันทึกย่อ","type"=>"CHAR","length_value"=>255,"charset"=>"thai"],
			"tms"=>["name"=>"เวลา","type"=>"FLOAT","length_value"=>[12,6]],
			"unit"=>["name"=>"หน่วย","type"=>"CHAR","length_value"=>25],
			"unit_sku_key"=>["name"=>"รหัสอิงอิงหน่วย","type"=>"CHAR","length_value"=>25],
			"unit_sku_root"=>["name"=>"รหัสรากหน่วย","type"=>"CHAR","length_value"=>25],
			"user"=>["name"=>"ผู้ใช้","type"=>"CHAR","length_value"=>25],
			"user_edit"=>["name"=>"ผู้แกไข","type"=>"CHAR","length_value"=>25],
			"userceo"=>["name"=>"ระดับผู้ใช้","type"=>"ENUM","length_value"=>["0","1","2","3","4","5","6","7","8","9"]],
			"w"=>["name"=>"เตือน","type"=>"ENUM","length_value"=>["0","1"]],
			"w1_n"=>["name"=>"จำนวนสินค้าหลักที่รวม","type"=>"INT","length_value"=>10],
			"w2_n"=>["name"=>"จำนวนสินค้าแถมที่รวม","type"=>"INT","length_value"=>10],
			"w1"=>["name"=>"สินค้าหลักที่รวม","type"=>"CHAR","length_value"=>25],
			"w2"=>["name"=>"สินค้าแถมที่รวม","type"=>"CHAR","length_value"=>25],
			
			"tr"=>["name"=>"ช่วงเวลา","type"=>"INT","length_value"=>10],
			"bi_c"=>["name"=>"จำนวนแถว bill_in","type"=>"INT","length_value"=>10],
			"bil_c"=>["name"=>"จำนวนแถว bill_in_list","type"=>"INT","length_value"=>10],
			"bs_c"=>["name"=>"จำนวนแถว bill_sell","type"=>"INT","length_value"=>10],
			"bsl_c"=>["name"=>"จำนวนแถว bill_sell_list","type"=>"INT","length_value"=>10],
			"bir_"=>["name"=>"เริ่ม bill_in","type"=>"INT","length_value"=>10],
			"bi_r"=>["name"=>"สิ้นสุด bill_in","type"=>"INT","length_value"=>10],
			"bilr_"=>["name"=>"เริ่ม bill_in_list","type"=>"INT","length_value"=>10],
			"bil_r"=>["name"=>"สิ้นสุด bill_in_list","type"=>"INT","length_value"=>10],
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
				$conn->commit();
			}catch(PDOException $e){
				//$conn->rollBack();
				//print_r($e->getMessage());
				//print_r($sql);
				$re["message_error"]=$e->getMessage();
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
		echo '<!DOCTYPE html>
					<html xmlns="http://www.w3.org/1999/xhtml" lang="th">
					<head>
					<title>'.$title.'</title>
					<meta charset="utf-8">
					<meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=yes">
					<meta name="description" content="'.$title.'">
					<link rel="manifest" href="set/manifest.json">
<link rel="apple-touch-icon" href="img/pwa/diypos_128.png">   
<meta name="theme-color" content="black"/>  
<meta name="apple-mobile-web-app-capable" content="yes">  
<meta name="apple-mobile-web-app-status-bar-style" content="black"> 
<meta name="apple-mobile-web-app-title" content="D I Y P O S"> 
<meta name="msapplication-TileImage" content="img/pwa/diypos_128.png">  
					<link rel="icon"   type="image/png" href="img/favicon.png" />
					<link rel="stylesheet" type="text/css" href="css/css.css">'.$this->pageHeadCss($data).'
					<script src="js/main.js" type="text/javascript"></script>'.$this->pageHeadJs($data).'
			</head><body>
			<div id="film"></div>
			<script type="text/javascript">let M=new main();let G=new gpu();'.$this->pageHeadOb($data).'</script>
			';
		if(!isset($data["dir"])){
			$this->topBar();
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
				$re.="let ".$data["js"][$i+1]."=new ".$data["js"][$i]."();";
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
	private function topBar(){
		if($this->home==0){
			echo '<div class="topbar">';
			$this->avatarWrite();
			echo '<a onclick="window.history.back()" title="กลับ"><span class="back">&nbsp;🔙&nbsp;</span></a>  
			 <a href="index.php" title="ไปหน้าหลัก"> 🏠 หน้าหลัก</a>';
			 foreach($this->dir as $v){
				 echo ' » '.$v;
			 }
			 echo '	</div>';
		}
	}
	protected function avatarWrite(string $page=null){
		if(isset($_SESSION["sku_root"])){
			$cs=($page=="home")?" style=\"float:none;display:block;text-align:right;\"":"";
			echo '<div class="avatar"'.$cs.'><a href="?a=me&amp;b=edit">👤 '.$_SESSION["name"].' '.$_SESSION["lastname"].'</a></div>';
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
	protected function pageFoot(){
		echo '</body></html>';
	}
	protected function checkSet(string $table,array $dt,string $type="post"):array{
		$re=["result"=>true,"message_error"=>""];
		//$dt=["get"=>[],"post"=>[]];
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
					if(strlen(trim($ry))==0&&in_array($v,$tb["not_null"])){
						$nm=$this->fills[$v]["name"];
						$re["result"]=false;
						$re["message_error"]="ข้อมูล \"".$nm."\"  ต้องไม่ว่าง" ;
						break;
					}else if(strlen(trim($ry))>0){
						$name=["name"];
						$sku=["sku","unit"];
						$barcode=["barcode"];
						$password=["password"];
						$money=["price","cost"];
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
						}else if(in_array($v,$name)){
							$max=$this->fills[$v]["length_value"]-4;
							if(strlen($ry)>$max) {
								$re["result"]=false;
								$re["message_error"]=$this->fills[$v]["name"]." ยาวเกิน ".$max." แต่ข้อความคุณยาว ".strlen($ry);
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
						}
					}
				}
			}
		}
		$this->nullSet($dt,$type);
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
	protected function billNote(string $type,string $note,string $nt2=""):string{
		$not=$note;
		$note=htmlspecialchars($note);
		$t="";
		if($nt2!=""){
			$nt2="<span class=\"pin\"> 📌  ".htmlspecialchars($nt2)."</span>";
		}
		if($type=="b"){
			$a=explode("/",$not);
			if(count($a)==3){
				//$t.="💰 ซื้อเข้า 🏭".$a[0]." 📅".$a[1]." 🧾".$a[2];
			}
			$t.="💵 ".$note;
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
}
?>