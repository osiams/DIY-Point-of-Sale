<?php 
require_once("config.php");
require_once("php/main.php");
class install extends main{
	public function __construct(){
		parent::__construct();
	}
	public  function pageInstall(){
		$ck=$this->isInstalled();
		$this->home=1;
		if($ck){																													//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			if($_SESSION["step"]==2){
				//print_r($_POST);
				if(isset($_POST["ok"])&&($_POST["ok"]=="1"||$_POST["ok"]=="0")){
					if($_POST["ok"]=="1"){
						$this->simple();
					}
					$this->setFinish();
					$this->home=0;
					$this->pageHead(["title"=>"ติดตั้ง DIYPOS"]);
					$this->contentFinishPage();
					$this->pageFoot();
				}else{
					$this->pageHead(["title"=>"ติดตั้ง DIYPOS"]);
					$this->contentSimplePage();
					$this->pageFoot();
				}
			}else{
				$this->pageHead(["title"=>"ติดตั้ง DIYPOS"]);
				$this->contentInstall();
				$this->pageFoot();
			}
		}else{
				$this->home=0;
				$this->pageHead(["title"=>"ติดตั้ง DIYPOS"]);
				echo '<div class="error">คุณติดตั้งแล้ว</div>';
				$this->pageFoot();
		}
	}
	private function isInstalled():bool{
		$re=file_exists("index");
		return $re;
	}
	private function setFinish(){
		unlink("index.php");
		rename ("index", "index.php");
	}
	private function contentFinishPage():void{

		echo '<div class="content"><h1>การติดตั้งเรียบร้อย</h1>
			<p>สวัสดี คุณ <b>ดู อิท ยัวร์เซล์ฟ </b>(Do it yourself.  ทำด้วยตัวคุณเอง) <br />
			คุณคือผู้ควบคุมระบบ โปรแกรมขายหน้าร้าน <br /><br />
			โปรดเข้าสู่ระบบด้วย <br />
			ผู้ใช้ <b>admin@diy.pos</b> <br />
			หัสผ่านปริยายคือ <b>12345678</b> <br /><br />
			<span class="red">โปรดเปลี่ยนรหัสผ่านใหม่เพื่อความปลอดภัย </span></p>
			<a href="index.php?a=login">เข้าสู่ระบบเพื่อใช้งาน</a></div>';
	}
	protected function contentInstall(){
		if(isset($_POST["submit"])){
			$this->okInstall();
			$re=$this->insertDefault();
			if(!$re["result"]){
				$_SESSION["step"]=1;
			}
			
		}else{
			$rm=file_get_contents('อ่านฉัน.txt');
			echo '<div class="content">
				<div>
					<h1>อ่านก่อน</h1>
					<div id="rm" style="border:2px solid green;padding:5px;width:90%;margin:0 auto;overflow:auto;text-align:left;white-space: pre;height:0px;">'.$rm.'</div>
					<input type="checkbox" onclick="if(this.checked){M.id(\'rm\').style.display=\'none\';M.id(\'in\').style.display=\'block\'}else{M.id(\'rm\').style.display=\'block\';M.id(\'in\').style.display=\'none\'}" /> ฉันอ่านแล้ว
				</div>
				<div  id="in" style="display:none;" class="form">
					<h1>ขั้นตอนที่ 1 สร้างฐานข้อมูล</h1>
					<p>ติดตั้งฐานข้อมูล รายละเอียดที่แสดงด้านล่าง <br />คือข้อมูลที่ได้จากไฟล์ config.php ถ้ามีช่องใหนว่างให้ไปแก้ไขที่ ไฟล์ ที่กล่าวมา</p>
					<form method="post">
						<input type="hidden" name="submit" value="clicksubmit" />
						<p><label for="install_databasename">ชื่อฐานข้อมูล</label></p>
						<div><input id="install_databasename" type="text" name="databasename" value="'.$this->cf["database"].'"  readonly="readonly" /></div>
						<p><label for="install_user">ชื่อผู้ใช้</label></p>
						<div><input idr="install_user" type="text" name="user" value="'.$this->cf["user"].'"  readonly="readonly" /></div>
						<p><label for="install_password">รหัสผ่าน</label></p>
						<div><input id="install_password" type="text" value="'.$this->cf["password"].'" name="password"  readonly="readonly" /></div>
						<br />
						<input type="submit" value="ติดตั้ง" />
					</form>
				</div>
			</div><script type="text/javascript">
			let hi=((window.innerHeight-80)>100)?(window.innerHeight-80):100
			document.getElementById("rm").style.height=hi+"px";</script>';
		}
	}
	protected function insertDefault():array{
		$re=["result"=>true,"message_error"=>""];
		$rn=$this->creatRoutines();
		//print_r($rn);
		if(!$rn["result"]){
			$re["result"]=false;
			$re["message_error"]=$rn["message_error"];
			echo '<div class="error c">'.$rn["message_error"].'</div>';
		}else{
			$conn =$this->dbConnect();
			$sql=[];
			$sql["default_unit"]="
				INSERT INTO `unit` (`id`,`sku`,`sku_key`,`sku_root`,`name`) VALUES (1,'default','defaultroot','defaultroot','_ไม่ระบุ') 
				ON DUPLICATE KEY UPDATE `sku`='default',`sku_key`='defaultroot',`sku_root`='defaultroot',`name`='ไม่ระบุ';
			";
			$pw=password_hash("12345678", PASSWORD_DEFAULT);
			$sql["default_user"]="
				INSERT INTO `user` (`id`,`sku`,`sku_key`,`sku_root`,`name`,`lastname`,`email`,`password`,`userceo`) 
				VALUES (1,'0000001','administratorroot','administratorroot','ดู อิท','ยัวร์เซล์ฟ','admin@diy.pos','".$pw."','9') 
				ON DUPLICATE KEY UPDATE `sku`='0000001',`sku_key`='administratorroot',`sku_root`='administratorroot',`name`='ดู อิท',
					`lastname`='ยัวร์เซล์ฟ',`email`='admin@diy.pos',`password`='".$pw."',`userceo`='9';
			";
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
			$sql["default_s"]="
				INSERT INTO `s` (`tr`,`bi_c`,`bil_c`,`bs_c`,`bsl_c`) VALUES(1,0,0,0,0)
				ON DUPLICATE KEY UPDATE `bi_c`=0,`bil_c`=0,`bs_c`=0,`bsl_c`=0;
			";
			$sql["ref_unit"]=$this->ref("unit","sku_key","defaultroot");
			$sql["ref_user"]=$this->ref("user","sku_key","administratorroot");
			$sql["ref_it1"]=$this->ref("it","sku_key","defaultroot");
			$sql["ref_it2"]=$this->ref("it","sku_key","proot");
			$sql["ref_it3"]=$this->ref("it","sku_key","xroot");
			$sql["ref_it4"]=$this->ref("it","sku_key","droot");
			$sql["ref_it5"]=$this->ref("it","sku_key","eroot");
			$se=$this->metMnSql($sql,[]);
			//print_r($se);
			if(!$se["result"]){
				$re["result"]=false;
				$re["message_error"]=$se["message_error"];
				echo '<div class="error c">'.$se["message_error"].'</div>';
			}
		}
		return $re;
	}
	protected function okInstall(){
		echo '<div class="c"><h1>ขั้นตอนที่ 2 สร้างตาราง</h1></div>';		
		$sql=[];
		$sql["ctb"]="";
		try{
			$conn = new PDO("mysql:host=".$this->cf["server"].";charset=utf8", $this->cf["user"],$this->cf["password"]);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
			$conn->exec("CREATE DATABASE IF NOT EXISTS `".$this->cf["database"]."` 
				CHARACTER SET = 'ascii'
				COLLATE = 'ascii_general_ci';");
		}catch(PDOException $e){

			echo '<div class="error c">'.$e->getMessage().'</div>
				<p class="c"><input type="button" value="ย้อนกลับ"  onclick="history.back();" /></p>';
			exit;
		}
		$re= $this->runCreateTable();
		$ok=true;
		$has=1;
		echo '<div class="block c"><table><tr><th colspan="2">ผลการสร้าางตาราง</th></tr>
			<tr><th>ตาราง</th><th>ผล</th></tr>';
		foreach($re as $k=>$v){
			if(strlen(trim($v["message"]))==0&&$v["result"]){
				$v["message"]="สำเร็จ";
			}else if($v["has"]==1){

			}else{
				$has=0;
				$ok=false;
			}
			echo '<tr><td class="l">'.$v["table"].'</td>';
			echo '<td class="l">'.$v["message"].'</td>';
			echo '</tr>';
		}
		echo '</table></div><br />';
		
		if($ok||$has==1){
			$_SESSION["step"]=2;
			echo '<p class="c"><a href="install.php">ขั้นตอนถัดไป</a> </p>';
		}else{
			echo '<p class="c"><a href="install.php">ลองติดตั้งใหม่</a> หรือ <a href="index.php">ไปหน้าหลัก</a></p>';
		}
	}
private function createTable(string $table,string $sql):array{
		$re=array("connect"=>false,
					"connect_message"=>"",
					"result"=>false,
					"has"=>false,
					"message"=>"");
		$re["table"]=$table;			
		try{
			$conn =$this->dbConnect();
			try{
				$result=$conn->exec($sql);
				$re["result"]=true;
				$re["has"]=true;
			}catch(PDOException $e){
				$re["message"]=$e->getMessage();
				$dv=$e->errorInfo;
				if($dv[0]=="42S01"){
					if($dv[1]=="1050"){
						$re["message"]="มีอยู่แล้ว";
						$re["has"]=true;
					}
				}
			}
			$re["connect"]=true;												
		}catch(PDOException $e){
			$re["connect_message"]=$e->getMessage();;		
		}
		return $re;
	}
	private function runCreateTable():array{
		$re=[];
		$tbs=$this->tb;
		$f=$this->fills;
		foreach($tbs as $k=>$v){
			$sql="";
			$fi="";
			$i=0;
			$sql.="CREATE TABLE `".$v["name"]."`(";
			$rf=substr($k,strlen($k)-4,4);
			foreach($v["column"] as $w){
				$f[$w]["name"]=$w;
				$i+=1;
				$cm=($i>1)?",":" ";
				$aut=($i==1&&$rf!="_ref")?" AUTO_INCREMENT ":"";
				$fi.=$cm." `".$f[$w]["name"]."`	".$f[$w]["type"];
				if(isset($f[$w]["length_value"])){
					$vv=$f[$w]["length_value"];
					if(is_array($vv)){
						
						if($f[$w]["type"]=="ENUM"){
							$vv=implode("','",$vv);
							$fi.="('".$vv."')".$aut;
						}else if($f[$w]["type"]=="FLOAT"){
							$vv=implode(",",$vv);
							$fi.="(".$vv.")".$aut;
						}
					}else{
						$fi.="(".$vv.")".$aut;
					}
				if(isset($f[$w]["charset"])){
					$fi.=" CHARACTER SET 'utf8' COLLATE 'utf8_thai_520_w2' ";
				}else if($f["w"]["type"]=="CHAR"||$f["w"]["type"]=="VARCHAR"){
					$fi.=" CHARACTER SET 'ascii' COLLATE 'ascii_general_ci' ";
				}	
					
					if(isset($tbs[$k]["unsigned"])){
						if(in_array($w , $tbs[$k]["unsigned"])){
							$fi.=" UNSIGNED ";
						}
					}
					if(isset($tbs[$k]["not_null"])){
						if(in_array($w , $tbs[$k]["not_null"])){
							$fi.=" NOT NULL ";
						}
					}

				}	
				if(isset($tbs[$k]["default"])){
						if(array_key_exists($w , $tbs[$k]["default"])){
							if($f[$w]["type"]=="TIMESTAMP"){
								$fi.=" NULL DEFAULT  ".$tbs[$k]["default"][$w];
							}else{
								$fi.=" NULL DEFAULT '".$tbs[$k]["default"][$w]."'";
							}
						}
				}
				if(isset($tbs[$k]["on"])){
						if(array_key_exists($w , $tbs[$k]["on"])){
							if($f[$w]["type"]=="TIMESTAMP"){
								$fi.=" ".$tbs[$k]["on"][$w];
							}
						}
				}
				$fi.=" ";
			}
			if(isset($tbs[$k]["unique"])){
				$nui=[];
				foreach($tbs[$k]["unique"] as $m=>$x ){
					$fi.=", UNIQUE  (`".$f[$x]["name"]."`)";
				}
			}
			if(isset($tbs[$k]["index"])){
				$nui=[];
				foreach($tbs[$k]["index"] as $m=>$x ){
					$nui[$m]=$f[$x]["name"];
				}
				$uq=implode("`,`",$nui);
				$fi.=", INDEX(`".$uq."`)";
			}	
			if(isset($tbs[$k]["primary"])){
				$fi.=", PRIMARY KEY(`".$tbs[$k]["primary"]."`)
					,KEY(`id`)";
			}	
			//PRIMARY KEY	
			$sql.=$fi;
			$sql.=") CHARACTER SET ascii COLLATE ascii_general_ci";
			if($k=="lot_list"){
			}
			//print_r($sql);
			$re[$k]=$this->createTable($v["name"],$sql);	
		}
		return $re;
	}
	private function creatRoutines():array{
		$sql=[];
		$sql["key"]="CREATE OR REPLACE FUNCTION `KEY_`() RETURNS CHAR(25) CHARACTER SET 'utf8' COLLATE 'utf8_bin'
			BEGIN
				DECLARE t CHAR(62) DEFAULT '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				DECLARE x CHAR(25);
				SET x=CONCAT(UNIX_TIMESTAMP()	,	
					SUBSTRING(t,FLOOR( 1+(RAND()*62)),1)	,	SUBSTRING(t,FLOOR( 1+(RAND()*62)),1)	,	SUBSTRING(t,FLOOR( 1+(RAND()*62)),1)	,	
					SUBSTRING(t,FLOOR( 1+(RAND()*62)),1)	,	SUBSTRING(t,FLOOR( 1+(RAND()*62)),1)	,	SUBSTRING(t,FLOOR( 1+(RAND()*62)),1)	,
					SUBSTRING(t,FLOOR( 1+(RAND()*62)),1)
				);
			RETURN x;
			END ";
		$sql["set_checkE_CWEBI_"]="DROP PROCEDURE IF EXISTS `ECWEBI_`;";
		$sql["set_ECWEBI_"]="CREATE DEFINER=`root`@`localhost` 
			PROCEDURE `ECWEBI_`(
				IN `lot` VARCHAR(25) CHARACTER SET 'ascii',
				IN `pdskuroot` VARCHAR(25) CHARACTER SET 'ascii', 
				IN `date` TIMESTAMP ,
				OUT `error` VARCHAR(1000))
			NO SQL COMMENT 'แก้ไขทุนใหม่ในใบเสร็จถ้ามีการปรับปรุงราคาในใบนำเข้าสินค้า'
			BEGIN
				DECLARE done INT DEFAULT FALSE;
				DECLARE cost_ FLOAT;
				DECLARE costr_ FLOAT;
				DECLARE r ROW (
					id INT,
					lot VARCHAR(25) CHARACTER SET 'ascii',
					sku VARCHAR(25) CHARACTER SET 'ascii'
				);
				DECLARE cur1 CURSOR FOR 
					SELECT bill_sell_list.bill_in_list_id,bill_sell_list.lot,bill_sell_list.sku
					FROM  bill_sell
					LEFT JOIN bill_sell_list
					ON(bill_sell.sku=bill_sell_list.sku)
					WHERE bill_sell.date_reg>date
						AND bill_sell_list.product_sku_root=`pdskuroot`
						AND  bill_sell_list.lot=`lot`
					GROUP BY bill_sell_list.sku  ;
					
				DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;	
				SET error='';
				#SET error=CONCAT(lot,'-',pdskuroot,'-',date);
				
				OPEN cur1;
					read_loop: LOOP
						FETCH cur1 INTO r;
						IF done THEN
							LEAVE read_loop;
						END IF;
						SET cost_=0;
						SET cost_=(
							SELECT SUM(
								IF(bill_in_list.sum IS NOT NULL,
									(bill_in_list.sum/bill_in_list.n),
									product_ref.cost
								)*IF(bill_sell_list.c>0,bill_sell_list.c,bill_sell_list.u)
							)
							FROM bill_sell_list 
							LEFT JOIN bill_in_list
							ON(bill_in_list.id=bill_sell_list.bill_in_list_id AND bill_sell_list.lot=bill_in_list.bill_in_sku AND bill_sell_list.product_sku_key=bill_in_list.product_sku_key)
							LEFT JOIN product_ref
							ON(bill_sell_list.product_sku_key=product_ref.sku_key)
							WHERE bill_sell_list.sku=r.sku
						);
						SET error=CONCAT(cost_);
						SET costr_=0;
						SET costr_=(
							SELECT SUM(
								IF(bill_in_list.sum IS NOT NULL,
									(bill_in_list.sum/bill_in_list.n),
									product_ref.cost
								)*(bill_sell_list.r)
							)
							FROM bill_sell_list 
							LEFT JOIN bill_in_list
							ON(bill_in_list.id=bill_sell_list.bill_in_list_id AND bill_sell_list.lot=bill_in_list.bill_in_sku AND bill_sell_list.product_sku_key=bill_in_list.product_sku_key)
							LEFT JOIN product_ref
							ON(bill_sell_list.product_sku_key=product_ref.sku_key)
							WHERE bill_sell_list.sku=r.sku
						);
						UPDATE bill_sell 
						SET cost=cost_,costr=costr_
						WHERE sku=r.sku;
						UPDATE bill_in _list
						SET `sum`=`n`*cost_
						WHERE lot=r.sku;
					END LOOP;
				CLOSE cur1;
			END;
		";
		$sql["set_check_BILED_"]="DROP PROCEDURE IF EXISTS `BILED_`;";
		$sql["set_BILED_"]="CREATE DEFINER=`root`@`localhost` 
			PROCEDURE `BILED_`(
				IN `dateandtime` CHAR(19),
				OUT `error` VARCHAR(1000))
			NO SQL COMMENT 'แบ่งข้อมูลเป็นเดือนๆ'
			BEGIN
				SET @yyyymm=CONCAT(YEAR(dateandtime),LPAD(MONTH(dateandtime),2,'0'));
				IF TBEX_(@yyyymm)=0 THEN 
					EXECUTE IMMEDIATE 
						CONCAT(
							'CREATE TABLE IF NOT EXISTS `bill_in_list.',
							@yyyymm,
							'` AS
							SELECT *
							FROM bill_in_list LIMIT 0'
						);
					EXECUTE IMMEDIATE 
						CONCAT(
							'ALTER TABLE `bill_in_list.',
							@yyyymm,
							'` ADD PRIMARY KEY( `id`)'
						);
					EXECUTE IMMEDIATE 
						CONCAT(
							'ALTER TABLE `bill_in_list.',
							@yyyymm,
							'` ADD INDEX( `stkey`, `stroot`, `bill_in_sku`,`product_sku_key`,`product_sku_root`,`balance`)'
						);
					EXECUTE IMMEDIATE 
						CONCAT(
							'ALTER TABLE `bill_in_list.',
							@yyyymm,
							'` CHANGE `id` `id` INT(10) NOT NULL'
						);
				END IF;
			END;
		";
		$sql["set_check_P_V_"]="DROP PROCEDURE IF EXISTS `V_`;";
		$sql["set_P_V_"]="CREATE DEFINER=`root`@`localhost` 
			PROCEDURE `V_`(
				IN `type` VARCHAR(25) ,
				OUT `v` VARCHAR(1000))
			NO SQL COMMENT 'เวชั่น'
			BEGIN
				IF type='dbname' THEN
					SET v='diypos_0.0';
				ELSEIF type='v' THEN
					SET v='0.0';
				ELSEIF type='date_reg' THEN
					SET v=NOW();
				ELSE
					SET v='?';
				END IF;
			END;
		";
		$sql["set_F_V_"]="CREATE OR REPLACE FUNCTION `V_`(type CHAR(25)) RETURNS CHAR(25) CHARACTER SET 'utf8' COLLATE 'utf8_bin'
			BEGIN
				CALL V_(type,@v);
				RETURN @v;
			END ";
		$sql["set_TBEX_"]="CREATE OR REPLACE FUNCTION `TBEX_` ( `table` CHAR(25)) RETURNS INT(1) 
			BEGIN
				SET @c=(SELECT COUNT(*)
				FROM information_schema.tables 
				WHERE table_schema = V_('dbname')
				AND table_name = `table`);
				RETURN @c;
			END ";
		$sql["set_check_CPT_"]="DROP PROCEDURE IF EXISTS `CPT_`;";
		$sql["set_CPT_"]="CREATE DEFINER=`root`@`localhost` 
			PROCEDURE `CPT_`(
				IN `table_` CHAR(25) ,
				IN `_table` CHAR(25) ,
				IN `key` CHAR(25) ,
				IN `value_int` INT(11) ,
				IN `value_str` CHAR(25) ,
				OUT `v` VARCHAR(1000))
			NO SQL COMMENT 'คัดลอก แถว ไปใส่ อีกตาราง'
			BEGIN
				SET @value='';
				IF `value_int` > 0 THEN
					SET @value=`value_int`;
				ELSE 
					SET @value=`value_str`;
				END IF;
				EXECUTE IMMEDIATE 
					CONCAT('INSERT IGNORE INTO `',
						`_table`,
						'` SELECT * FROM `',
						`table_`,
						'`  WHERE  `',
						`table_`,
						'`.`',
						`key`,
						'`  =',
						@value,
						' LIMIT 1; '
					);
			END;
		";
		$sql["set_check_TEST_"]="DROP PROCEDURE IF EXISTS `TEST_`;";
		$sql["set_TEST_"]="CREATE DEFINER=`root`@`localhost` 
			PROCEDURE `TEST_`(
				IN `curtime` FLOAT(12,6),
				IN `note_` VARCHAR(1000) CHARACTER SET utf8)
			NO SQL COMMENT 'ทดสอบ'
			BEGIN
				INSERT INTO test (tms,note) VALUES(`curtime`,`note_`);
			END;
		";
		$sql["set_check_TR_"]="DROP PROCEDURE IF EXISTS `TR_`;";
		$sql["set_TR_"]="CREATE DEFINER=`root`@`localhost` 
			PROCEDURE `TR_`(IN `table_` CHAR(25) CHARACTER SET ascii)
			NO SQL COMMENT 'ช่วงเวลา'
			BEGIN
				DECLARE now TIMESTAMP DEFAULT NOW();
				DECLARE yyyy INT DEFAULT YEAR(now);
				DECLARE mm CHAR(2) DEFAULT LPAD(MONTH(now),2,'0');
				DECLARE ww CHAR(2) DEFAULT LPAD(WEEKOFYEAR(now),2,'0');
				DECLARE dd CHAR(2) DEFAULT LPAD(DAYOFMONTH(now),2,'0');
				DECLARE yyyymm INT DEFAULT CONCAT(yyyy,mm);
				DECLARE yyyyweek INT DEFAULT CONCAT(yyyy,'0',ww);
				DECLARE yyyymmdd INT DEFAULT CONCAT(yyyy,mm,dd);
				DECLARE maxid INT DEFAULT  1;
				IF `table_`='bill_sell_list' THEN
					SET maxid=(SELECT MAX(id) FROM bill_sell_list);
					INSERT INTO `s` (`tr`,`bsl_c`,`bslr_`,`bsl_r`) VALUES (yyyy,1,maxid,maxid) 
					ON DUPLICATE KEY UPDATE  `bsl_c`=(IFNULL(`bsl_c`,0)+1),`bslr_`=IFNULL(`bslr_`,maxid),`bsl_r`=maxid;
					INSERT INTO `s` (`tr`,`bsl_c`,`bslr_`,`bsl_r`) VALUES (yyyymm,1,maxid,maxid) 
					ON DUPLICATE KEY UPDATE  `bsl_c`=(IFNULL(`bsl_c`,0)+1),`bslr_`=IFNULL(`bslr_`,maxid),`bsl_r`=maxid;
					INSERT INTO `s` (`tr`,`bsl_c`,`bslr_`,`bsl_r`) VALUES (yyyyweek,1,maxid,maxid) 
					ON DUPLICATE KEY UPDATE  `bsl_c`=(IFNULL(`bsl_c`,0)+1),`bslr_`=IFNULL(`bslr_`,maxid),`bsl_r`=maxid;
					INSERT INTO `s` (`tr`,`bsl_c`,`bslr_`,`bsl_r`) VALUES (yyyymmdd,1,maxid,maxid) 
					ON DUPLICATE KEY UPDATE  `bsl_c`=(IFNULL(`bsl_c`,0)+1),`bslr_`=IFNULL(`bslr_`,maxid),`bsl_r`=maxid;
					UPDATE `s` SET bsl_c=(bsl_c+1) WHERE tr=1;
				ELSEIF `table_`='bill_sell' THEN
					SET maxid=(SELECT MAX(id) FROM bill_sell);
					INSERT INTO `s` (`tr`,`bs_c`,`bsr_`,`bs_r`) VALUES (yyyy,1,maxid,maxid) 
					ON DUPLICATE KEY UPDATE  `bs_c`=(IFNULL(`bs_c`,0)+1),`bsr_`=IFNULL(`bsr_`,maxid),`bs_r`=maxid;
					INSERT INTO `s` (`tr`,`bs_c`,`bsr_`,`bs_r`) VALUES (yyyymm,1,maxid,maxid) 
					ON DUPLICATE KEY UPDATE  `bs_c`=(IFNULL(`bs_c`,0)+1),`bsr_`=IFNULL(`bsr_`,maxid),`bs_r`=maxid;
					INSERT INTO `s` (`tr`,`bs_c`,`bsr_`,`bs_r`) VALUES (yyyyweek,1,maxid,maxid) 
					ON DUPLICATE KEY UPDATE  `bs_c`=(IFNULL(`bs_c`,0)+1),`bsr_`=IFNULL(`bsr_`,maxid),`bs_r`=maxid;				
					INSERT INTO `s` (`tr`,`bs_c`,`bsr_`,`bs_r`) VALUES (yyyymmdd,1,maxid,maxid) 
					ON DUPLICATE KEY UPDATE  `bs_c`=(IFNULL(`bs_c`,0)+1),`bsr_`=IFNULL(`bsr_`,maxid),`bs_r`=maxid;		
					UPDATE `s` SET bs_c=(bs_c+1) WHERE tr=1;	
				ELSEIF `table_`='bill_in' THEN
					SET maxid=(SELECT MAX(id) FROM bill_in);
					INSERT INTO `s` (`tr`,`bi_c`,`bir_`,`bi_r`) VALUES (yyyy,1,maxid,maxid) 
					ON DUPLICATE KEY UPDATE  `bi_c`=(IFNULL(`bi_c`,0)+1),`bir_`=IFNULL(`bir_`,maxid),`bi_r`=maxid;
					INSERT INTO `s` (`tr`,`bi_c`,`bir_`,`bi_r`) VALUES (yyyymm,1,maxid,maxid) 
					ON DUPLICATE KEY UPDATE  `bi_c`=(IFNULL(`bi_c`,0)+1),`bir_`=IFNULL(`bir_`,maxid),`bi_r`=maxid;
					INSERT INTO `s` (`tr`,`bi_c`,`bir_`,`bi_r`) VALUES (yyyyweek,1,maxid,maxid) 
					ON DUPLICATE KEY UPDATE  `bi_c`=(IFNULL(`bi_c`,0)+1),`bir_`=IFNULL(`bir_`,maxid),`bi_r`=maxid;
					INSERT INTO `s` (`tr`,`bi_c`,`bir_`,`bi_r`) VALUES (yyyymmdd,1,maxid,maxid) 
					ON DUPLICATE KEY UPDATE  `bi_c`=(IFNULL(`bi_c`,0)+1),`bir_`=IFNULL(`bir_`,maxid),`bi_r`=maxid;
					UPDATE `s` SET bi_c=(bi_c+1) WHERE tr=1;
				ELSEIF `table_`='bill_in_list' THEN
					SET maxid=(SELECT MAX(id) FROM bill_in_list);
					INSERT INTO `s` (`tr`,`bil_c`,`bilr_`,`bil_r`) VALUES (yyyy,1,maxid,maxid) 
					ON DUPLICATE KEY UPDATE  `bil_c`=(IFNULL(`bil_c`,0)+1),`bilr_`=IFNULL(`bilr_`,maxid),`bil_r`=maxid;
					INSERT INTO `s` (`tr`,`bil_c`,`bilr_`,`bil_r`) VALUES (yyyymm,1,maxid,maxid) 
					ON DUPLICATE KEY UPDATE  `bil_c`=(IFNULL(`bil_c`,0)+1),`bilr_`=IFNULL(`bilr_`,maxid),`bil_r`=maxid;
					INSERT INTO `s` (`tr`,`bil_c`,`bilr_`,`bil_r`) VALUES (yyyyweek,1,maxid,maxid) 
					ON DUPLICATE KEY UPDATE  `bil_c`=(IFNULL(`bil_c`,0)+1),`bilr_`=IFNULL(`bilr_`,maxid),`bil_r`=maxid;
					INSERT INTO `s` (`tr`,`bil_c`,`bilr_`,`bil_r`) VALUES (yyyymmdd,1,maxid,maxid) 
					ON DUPLICATE KEY UPDATE  `bil_c`=(IFNULL(`bil_c`,0)+1),`bilr_`=IFNULL(`bilr_`,maxid),`bil_r`=maxid;
					UPDATE `s` SET bil_c=(bil_c+1) WHERE tr=1;
				END IF;
			END;
		";
		$sql["set_check_TRC_"]="DROP PROCEDURE IF EXISTS `TRC_`;";
		$sql["set_TRC_"]="CREATE DEFINER=`root`@`localhost` 
			PROCEDURE `TRC_`(IN `table_` CHAR(25) CHARACTER SET ascii,
				IN `date_reg_` TIMESTAMP)
			NO SQL COMMENT 'จำนวน'
			BEGIN
				DECLARE now TIMESTAMP DEFAULT `date_reg_`;
				DECLARE yyyy INT DEFAULT YEAR(now);
				DECLARE mm CHAR(2) DEFAULT LPAD(MONTH(now),2,'0');
				DECLARE ww CHAR(2) DEFAULT LPAD(WEEKOFYEAR(now),2,'0');
				DECLARE dd CHAR(2) DEFAULT LPAD(DAYOFMONTH(now),2,'0');
				DECLARE yyyymm INT DEFAULT CONCAT(yyyy,mm);
				DECLARE yyyyweek INT DEFAULT CONCAT(yyyy,'0',ww);
				DECLARE yyyymmdd INT DEFAULT CONCAT(yyyy,mm,dd);
				IF `table_`='bill_sell' THEN
					UPDATE `s` SET bs_c=(bs_c-1) WHERE tr IN(1,yyyy,yyyymm,yyyyweek,yyyymmdd);				
				ELSEIF `table_`='bill_sell_list' THEN
					UPDATE `s` SET bsl_c=(bsl_c-1) WHERE tr IN(1);
				ELSEIF `table_`='bill_in' THEN
					UPDATE `s` SET bi_c=(bi_c-1) WHERE tr IN(1,yyyy,yyyymm,yyyyweek,yyyymmdd);
				ELSEIF `table_`='bill_in_list' THEN
				
					UPDATE `s` SET bil_c=(bil_c-1) WHERE tr IN(1,yyyy,yyyymm,yyyyweek,yyyymmdd);
				END IF;
			END;
		";
		$sql["create1"]="CREATE OR REPLACE DEFINER=`root`@`localhost` TRIGGER `TR.BSL_`
			AFTER INSERT ON `bill_sell_list`  FOR EACH ROW CALL TR_('bill_sell_list');";
		$sql["create2"]="CREATE OR REPLACE DEFINER=`root`@`localhost` TRIGGER `TR.BS_`
			AFTER INSERT ON `bill_sell`  FOR EACH ROW CALL TR_('bill_sell');";
		$sql["create3"]="CREATE OR REPLACE DEFINER=`root`@`localhost` TRIGGER `TR.BIL_`
			AFTER INSERT ON `bill_in_list`  FOR EACH ROW CALL TR_('bill_in_list');";
		$sql["create4"]="CREATE OR REPLACE DEFINER=`root`@`localhost` TRIGGER `TR.BI_`
			AFTER INSERT ON `bill_in`  FOR EACH ROW CALL TR_('bill_in');";
			
		$sql["create1c"]="CREATE OR REPLACE DEFINER=`root`@`localhost` TRIGGER `TR.BSC_`
			AFTER DELETE ON `bill_sell`  FOR EACH ROW CALL TRC_('bill_sell',OLD.date_reg);";
		$sql["create2c"]="CREATE OR REPLACE DEFINER=`root`@`localhost` TRIGGER `TR.BSLC_`
			AFTER DELETE ON `bill_sell_list`  FOR EACH ROW CALL TRC_('bill_sell_list','0000-00-00 00:00:00');";
		$sql["create3c"]="CREATE OR REPLACE DEFINER=`root`@`localhost` TRIGGER `TR.BIC_`
			AFTER DELETE ON `bill_in`  FOR EACH ROW CALL TRC_('bill_in',OLD.date_reg);";
		$sql["create4c"]="CREATE OR REPLACE DEFINER=`root`@`localhost` TRIGGER `TR.BILC_`
			AFTER DELETE ON `bill_in_list`  FOR EACH ROW 
				BEGIN 
					SET @datereg=(SELECT date_reg FROM bill_in WHERE bill_in.sku=OLD.bill_in_sku);
					CALL TRC_('bill_in_list',@datereg);
					END;";
		$sql["finelotroot"]="CREATE OR REPLACE FUNCTION `FINDLOTROOT_`(
			billinsku_ CHAR(25) CHARACTER SET 'ascii',
			productskuroot_ CHAR(25) CHARACTER SET 'ascii') 
			RETURNS CHAR(25) CHARACTER SET 'ascii' COLLATE 'ascii_general_ci'
			BEGIN
				DECLARE bill_ CHAR(25) DEFAULT '';
				DECLARE lot_ CHAR(25) DEFAULT '';
				DECLARE lotroot_ CHAR(25) DEFAULT '';
				SET bill_=(SELECT bill  from bill_in WHERE sku=billinsku_);
				SET lot_=(SELECT lot FROM bill_sell_list WHERE sku=bill_ AND product_sku_root=productskuroot_);
				SET lotroot_=(SELECT lot_root  from bill_in WHERE sku=lot_);
				RETURN  lotroot_;
			END ";
		$se=$this->metMnSql($sql,[]);
		return $se;
	}
	private function contentSimplePage():void{
		echo '<div class="content">
			<h1>ข้นตอนที่ 3. ติดตั้งข้อมูลตัวอย่าง หรือไม่?</h1>
			<p class="c">ใส่ข้อมูลสินค้าตัวอย่าง ในฐานข้อมูล เพื่อการเรียนรู้ และการใช้งาน ที่เร็วยิ่งขึ้น</p>
			<form name="a"method="post">
				<div><div class="l">
				<input type="radio" name="ok" value="0" checked />ไม่ต้องการ<br />
				<input type="radio" name="ok" value="1" />ต้องการ ข้อมูลสินค้าตัวอย่าง</div></div>
				<div class="c"><p><input type="submit" value="ดำเนินการต่อ"></div>
			</form>
		</div>';
	}
	private function simple():void{
		$sql=[];
		$i=0;
		$s=0;
		$nl="\n";
		$cs=false;
		$ut=[];
		$sql[$s++]="SELECT @reg_date:=(SELECT CONCAT((CURDATE() - INTERVAL 45 DAY),' 00:00:00'));";
		//////////////////////////////////////////////////////////////////
		$handle = @fopen("data/st/ut.txt", "r");
		if ($handle) {
			$i=0;
			while (($buffer = fgets($handle, 1024)) !== false) {
				$cm=",";
				if($i==0){
					$sql[$s]="INSERT INTO unit (name,sku,sku_key,sku_root,date_reg) VALUES";
					$cm="";
				}
				$i+=1;
				if($i<=10){
					$d=explode(",",trim($buffer));
					if(count($d)==2){
						$key=$d[1]."root";
						$sql[$s].=$cm.'("'.$d[0].'","'.$d[1].'","'.$key.'","'.$key.'",@reg_date)';
						$ut[$d[0]]=$key;
					}else{
						$i=$i-1;
					}
					if($i<10){$sql[$s].=$nl;$cs=false;}else{$sql[$s].=";".$nl;$i=0;$s+=1;$cs=true;}
				}
			}
			if(!$cs){
				$sql[$s].=";";
				$cs=false;
			}
			$s+=1;
			if (!feof($handle)) {
				echo "Error: unexpected fgets() fail\n";
			}
			fclose($handle);
		}
		///////////////////////////////////////////////////////////////
		$handle = @fopen("data/st/pd.txt", "r");
		if ($handle) {
			$i=0;
			while (($buffer = fgets($handle, 1024)) !== false) {
				$cm=",";
				if($i==0){
					$sql[$s]="INSERT INTO product (name,barcode,cost,price,unit,sku_key,sku_root,date_reg) VALUES";
					$cm="";
				}
				$i+=1;
				if($i<=10){
					$d=explode("'",trim($buffer));
					if(count($d)==5){
						$utp="defaultroot";
						if(isset($ut[$d[4]])){
							$utp=$ut[$d[4]];
						}
						$key=$this->key("key",7);
						$d[1]=(strlen(trim($d[1]))==0)?'NULL':'"'.$d[1].'"';
						
						$sql[$s].=$cm.'("'.$d[0].'",'.$d[1].','.((float) $d[2]).','.((float) $d[3]).',"'.$utp.'","'.$key.'","'.$key.'",@reg_date)';
						if($i<10){$sql[$s].=$nl;$cs=false;}else{$sql[$s].=";".$nl;$i=0;$s+=1;$cs=true;}
					}else{
						$i=$i-1;
					}
				}
			}
			if(!$cs){
				$sql[$s].=";";
				$cs=false;
			}
			$s+=1;
			if (!feof($handle)) {
				echo "Error: unexpected fgets() fail\n";
			}
			fclose($handle);
		}
		$sql["unit_ref"]="INSERT INTO unit_ref 
			SELECT *
			FROM unit WHERE id>1";
		$sql["product_ref"]="INSERT INTO product_ref 
			SELECT *
			FROM product";
		$se=$this->metMnSql($sql,[]);
	}
}
session_start();
if(!isset($_SESSION["step"])){
	$_SESSION["step"]=1;
}else if($_SESSION["step"]!=1&&$_SESSION["step"]!=2&&$_SESSION["step"]!=3){
	$_SESSION["step"]=1;
}
(new install())->pageInstall();
?>
