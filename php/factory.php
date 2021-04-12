<?php
class factory extends main{
	public function __construct(){
		parent::__construct();
	}
	public function fetch(){
		if(isset($_POST["b"])&&$_POST["b"]=="reset"){
			$this->fetchReset();
		}else{
			header('Content-type: application/json');
			print_r("{}");
		}
	}
	private function fetchReset(){
		$re=["result"=>false,"message_error"=>""];
		$sql=[];
		$sql["truncate"]="
			TRUNCATE `bill_in`;
			TRUNCATE `bill_in_list`;
			TRUNCATE `bill_sell`;
			TRUNCATE `bill_sell_list`;
			TRUNCATE `gallery`;
			TRUNCATE `group`;
			TRUNCATE `group_ref`;
			TRUNCATE `it`;
			TRUNCATE `it_ref`;
			TRUNCATE `mmm`;
			TRUNCATE `partner`;
			TRUNCATE `partner_ref`;
			TRUNCATE `payu`;
			TRUNCATE `payu_ref`;
			TRUNCATE `product`;
			TRUNCATE `product_ref`;
			TRUNCATE `prop`;
			TRUNCATE `prop_ref`;
			TRUNCATE `s`;
			TRUNCATE `test`;
			TRUNCATE `unit`;
			TRUNCATE `unit_ref`;
			TRUNCATE `user`;
			TRUNCATE `user_ref`;
		";
		$se=$this->metMnSql($sql,[]);
		$sql=[];
		$df=$this->getSQLinsertDefault();
		foreach($df as $k=>$v){
			$sql[$k]=$v;
		}
		$se=$this->metMnSql($sql,[]);
		if($se["result"]&&$se["message_error"]==""){
			$re["result"]=true;
		}else{
			$re["message_error"]=$se["message_error"];
		}
		$this->deleteAllImg();
		$this->addImgDefault();
		session_unset();
		header('Content-type: application/json');
		echo json_encode($re,true);
	}
	private function deleteAllImg(){
		$files = glob($this->gallery_dir."/*");
		foreach($files as $file){
			if(is_file($file)){
				unlink($file); 
			}
		}
	}
	private function addImgDefault(){
		$dir=$this->gallery_dir;
		$dir_=$dir.".default";
		$files = glob($dir_."/*");
		foreach($files as $file){
			$file_to=str_replace($dir_,$dir,$file);
			copy($file,$file_to);
		}
	}
	public function getSQLinsertDefault():array{
			$user=(isset($_SESSION["sku_root"]))?$_SESSION["sku_root"]:"administratorroot";
			$sql=[];
			$sql["default_unit"]="BEGIN NOT ATOMIC
				INSERT INTO `unit` (`id`,`sku`,`sku_key`,`sku_root`,`name`,`s_type`) VALUES (1,'default','defaultroot','defaultroot','_ไม่ระบุ','p') 
				ON DUPLICATE KEY UPDATE `sku`='default',`sku_key`='defaultroot',`sku_root`='defaultroot',`name`='_ไม่ระบุ',`s_type`='p';
				INSERT INTO `unit` (`id`,`sku`,`sku_key`,`sku_root`,`name`,`s_type`) VALUES (2,'kilogram','kilogramroot','kilogramroot','กิโลกรัม','w') 
				ON DUPLICATE KEY UPDATE `sku`='kilogram',`sku_key`='kilogramroot',`sku_root`='kilogramroot',`name`= 'กิโลกรัม',`s_type`='w';
				INSERT INTO `unit` (`id`,`sku`,`sku_key`,`sku_root`,`name`,`s_type`) VALUES (3,'meter','meterroot','meterroot','เมตร','l') 
				ON DUPLICATE KEY UPDATE `sku`='meter',`sku_key`='meterroot',`sku_root`='meterroot',`name`= 'เมตร',`s_type`='l';
				INSERT INTO `unit` (`id`,`sku`,`sku_key`,`sku_root`,`name`,`s_type`) VALUES (4,'litre','litreroot','litreroot','ลิตร','v') 
				ON DUPLICATE KEY UPDATE `sku`='litre',`sku_key`='litreroot',`sku_root`='litreroot',`name`= 'ลิตร',`s_type`='v';
			END;";
			$sql["default_group"]="
				INSERT INTO `group` (`id`,`sku`,`sku_key`,`sku_root`,`d1`,`name`) VALUES (1,'default','defaultroot','defaultroot','defaultroot','_ไม่ระบุ') 
				ON DUPLICATE KEY UPDATE `sku`='default',`sku_key`='defaultroot',`sku_root`='defaultroot',`d1`='defaultroot',`name`='_ไม่ระบุ';
			";
			$sql["default_payu"]="
				INSERT INTO `payu` (`id`,`sku`,`sku_key`,`sku_root`,`name`,`money_type`,`icon`) VALUES (1,'default','defaultroot','defaultroot','เงินสด','ca','defaultpayudefaultroot.png') 
				ON DUPLICATE KEY UPDATE `sku`='default',`sku_key`='defaultroot',`sku_root`='defaultroot',`name`='เงินสด',`money_type`='ca',`icon`='defaultpayudefaultroot.png';
			";
			$sql["default_gallery"]="
				INSERT INTO `gallery` (`id`,`sku_key`,`name`,`a_type`,`mime_type`,`md5`,`user`,`size`,`width`,`height`) 
					VALUES (1,'defaultpayudefaultroot','เงินสด','payu','image/png','ba5a9696072f8b83516e2dac2f75ef5c','".$user."',34102,480,480) 
				ON DUPLICATE KEY UPDATE `sku_key`='defaultpayudefaultroot',`name`='เงินสด',`a_type`='payu',`mime_type`='image/png',`md5`='ba5a9696072f8b83516e2dac2f75ef5c',`user`='".$user."',`size`=34102,`width`=480,`height`=480;
			";
			$pw=password_hash("12345678", PASSWORD_DEFAULT);
			$sql["default_user"]="BEGIN NOT ATOMIC
				INSERT INTO `user` (`id`,`sku`,`sku_key`,`sku_root`,`name`,`lastname`,`email`,`password`,`userceo`) 
				VALUES (1,'0000001','administratorroot','administratorroot','ดู อิท','ยัวร์เซล์ฟ','admin@diy.pos','".$pw."','9') 
				ON DUPLICATE KEY UPDATE `sku`='0000001',`sku_key`='administratorroot',`sku_root`='administratorroot',`name`='ดู อิท',
					`lastname`='ยัวร์เซล์ฟ',`email`='admin@diy.pos',`password`='".$pw."',`userceo`='9';
				INSERT INTO `user` (`id`,`sku`,`sku_key`,`sku_root`,`name`,`lastname`,`email`,`password`,`userceo`) 
				VALUES (2,'0000002','systemroot','systemroot','[[SYSTEM]]','','system@diy.pos','".$pw."','0') 
				ON DUPLICATE KEY UPDATE `sku`='0000002',`sku_key`='systemroot',`sku_root`='systemroot',`name`='[[SYSTEM]]',
					`lastname`='',`email`='system@diy.pos',`password`='".$pw."',`userceo`='0';
			END;";
			$sql["default_it"]="BEGIN NOT ATOMIC 
				INSERT INTO `it` (`id`,`sku`,`sku_key`,`sku_root`,`name`,`note`) VALUES (1,'default','defaultroot','defaultroot','_ไม่ระบุ','ไม่ระบุ') 
				ON DUPLICATE KEY UPDATE `sku`='default',`sku_key`='defaultroot',`sku_root`='defaultroot',`name`='_ไม่ระบุ',`note`='ไม่ระบุ';
				INSERT INTO `it` (`id`,`sku`,`sku_key`,`sku_root`,`name`,`note`) VALUES (2,'p','proot','proot','พร้อมขาย','พร้อมขาย') 
				ON DUPLICATE KEY UPDATE `sku`='p',`sku_key`='proot',`sku_root`='proot',`name`='พร้อมขาย',`note`='พร้อมขาย';
				INSERT INTO `it` (`id`,`sku`,`sku_key`,`sku_root`,`name`,`note`) VALUES (3,'x','xroot','xroot','ไม่พร้อมขาย','ไม่พร้อมขาย') 
				ON DUPLICATE KEY UPDATE `sku`='x',`sku_key`='xroot',`sku_root`='xroot',`name`='ไม่พร้อมขาย',`note`='ไม่พร้อมขาย';
				INSERT INTO `it` (`id`,`sku`,`sku_key`,`sku_root`,`name`,`note`) VALUES (4,'d','droot','droot','เสีย ชำรุด','เสีย ชำรุด') 
				ON DUPLICATE KEY UPDATE `sku`='d',`sku_key`='droot',`sku_root`='droot',`name`='เสีย ชำรุด',`note`='เสีย ชำรุด';
				INSERT INTO `it` (`id`,`sku`,`sku_key`,`sku_root`,`name`,`note`) VALUES (5,'e','eroot','eroot','หมดอายุ','หมดอายุ') 
				ON DUPLICATE KEY UPDATE `sku`='e',`sku_key`='eroot',`sku_root`='eroot',`name`='หมดอายุ',`note`='หมดอายุ ตกรุ่น เก่า';
			END;";
			$sql["default_prop"]="BEGIN NOT ATOMIC 
				INSERT INTO `prop` (`id`,`sku`,`sku_key`,`sku_root`,`name`,`data_type`) VALUES (1,'width-mm','width-mmroot','width-mmroot','กว้าง (มม.)','n') 
				ON DUPLICATE KEY UPDATE `sku`='width-mm',`sku_key`='width-mmroot',`sku_root`='width-mmroot',`name`='กว้าง (มม.)',`data_type`='n';
				INSERT INTO `prop` (`id`,`sku`,`sku_key`,`sku_root`,`name`,`data_type`) VALUES (2,'height-mm','height-mmroot','height-mmroot','สูง (มม.)','n') 
				ON DUPLICATE KEY UPDATE `sku`='height-mm',`sku_key`='height-mmroot',`sku_root`='height-mmroot',`name`='สูง (มม.)',`data_type`='n';
				INSERT INTO `prop` (`id`,`sku`,`sku_key`,`sku_root`,`name`,`data_type`) VALUES (3,'color','colorroot','colorroot','สี','s') 
				ON DUPLICATE KEY UPDATE `sku`='color',`sku_key`='colorroot',`sku_root`='colorroot',`name`='สี',`data_type`='s';
				INSERT INTO `prop` (`id`,`sku`,`sku_key`,`sku_root`,`name`,`data_type`) VALUES (4,'flavor','flavorroot','flavorroot','รส','s') 
				ON DUPLICATE KEY UPDATE `sku`='flavor',`sku_key`='flavorroot',`sku_root`='flavorroot',`name`='รส',`data_type`='s';
				INSERT INTO `prop` (`id`,`sku`,`sku_key`,`sku_root`,`name`,`data_type`) VALUES (5,'smell','smellroot','smellroot','กลิ่น','s') 
				ON DUPLICATE KEY UPDATE `sku`='smell',`sku_key`='smellroot',`sku_root`='smellroot',`name`='กลิ่น',`data_type`='s';
			END;";
			$sql["default_s"]="
				INSERT INTO `s` (`tr`,`bi_c`,`bil_c`,`bs_c`,`bsl_c`) VALUES(1,0,0,0,0)
				ON DUPLICATE KEY UPDATE `bi_c`=0,`bil_c`=0,`bs_c`=0,`bsl_c`=0;
			";
			$sql["ref_unit1"]=$this->ref("unit","sku_key","defaultroot");
			$sql["ref_unit2"]=$this->ref("unit","sku_key","kilogramroot");
			$sql["ref_unit3"]=$this->ref("unit","sku_key","meterroot");
			$sql["ref_unit4"]=$this->ref("unit","sku_key","litreroot");
			$sql["ref_group"]=$this->ref("group","sku_key","defaultroot");
			$sql["ref_user1"]=$this->ref("user","sku_key","administratorroot");
			$sql["ref_user2"]=$this->ref("user","sku_key","systemroot");
			$sql["ref_payu1"]=$this->ref("payu","sku_key","defaultroot");
			$sql["ref_it1"]=$this->ref("it","sku_key","defaultroot");
			$sql["ref_it2"]=$this->ref("it","sku_key","proot");
			$sql["ref_it3"]=$this->ref("it","sku_key","xroot");
			$sql["ref_it4"]=$this->ref("it","sku_key","droot");
			$sql["ref_it5"]=$this->ref("it","sku_key","eroot");
			$sql["ref_prop1"]=$this->ref("prop","sku_key","width-mmroot");
			$sql["ref_prop2"]=$this->ref("prop","sku_key","height-mmroot");
			$sql["ref_prop3"]=$this->ref("prop","sku_key","colorroot");
			$sql["ref_prop4"]=$this->ref("prop","sku_key","flavorroot");
			$sql["ref_prop5"]=$this->ref("prop","sku_key","smellroot");
		return $sql;
	}
}
