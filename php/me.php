<?php
class me extends main{
	public function __construct(){
		parent::__construct();
		$this->my_time=[];
	}
	public function run(){
		$q=["edit"];
		$this->addDir("?a=me","ฉัน");
		if(isset($_GET["b"])&&in_array($_GET["b"],$q)){
			$t=$_GET["b"];
			if($t=="edit"){
				$this->editMe();
			}
		}else{
			$this->editMe();
		}
	}
	private function editMe():void{
		$error="";
		$_POST["sku_root"]=$_SESSION["sku_root"];
		if(isset($_POST["submith"])&&$_POST["submith"]=="clicksubmit"&&isset($_POST["ps"])&&$_SESSION["sku_root"]!="systemroot"){
			$_POST["password"]=(isset($_POST["ps1"])&&strlen(trim($_POST["ps1"]))>0)?$_POST["ps1"]:"00000000";
			$se=$this->checkSet("user",["post"=>["name","lastname","email","sku_root","password"]],"post");
			if(!$se["result"]){
				$error=$se["message_error"];
			}else if(!$this->checkMe($_POST["ps"])){
				$error="รหัสผ่านยืนยันตัวคุณไม่ถูกต้อง คุณไม่ใช่ <b><i>".$_SESSION["name"]." ".$_SESSION["lastname"]."</i></b>";
			}else if((isset($_POST["ps1"])||isset($_POST["ps2"]))&&trim($_POST["ps1"])!=trim($_POST["ps2"])){
				$error="รหัสผ่านใหม่ของคุณ กับรหัสผ่านใหม่อีกครั้ง ไม่ตรงกัน ";
			}else if($_SESSION["onoff"]!=1){
				$error="คุณต้องเปิดกะทำงานก่อน ถึงจะแก้ไขได้ ";
			}else{
				 $qe=$this->editMeUpdate();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					 //print_r($qe);
					header('Location:?a=me');
				}
			}
			if($error!=""){
				$this->editMePage($error);
			}
		}else{
			$sku_root=(isset($_POST["sku_root"]))?$_POST["sku_root"]:"";
			$this->editMeSetCurent($sku_root);
			$this->editMePage($error);
		}
	}
	private function editMeUpdate():array{
		$name=$this->getStringSqlSet($_POST["name"]);
		$lastname=$this->getStringSqlSet($_POST["lastname"]);
		$email=$this->getStringSqlSet($_POST["email"]);	
		$sku_root=$this->getStringSqlSet($_SESSION["sku_root"]);
		$new_password=(isset($_POST["ps1"])&&strlen($_POST["ps1"])>=8)?password_hash($_POST["ps1"], PASSWORD_DEFAULT):"";
		$text_pass_set="";
		if($new_password!=""){
			$text_pass_set=" ,`password`=\"".$new_password."\" ";
		}
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@count_name:=(SELECT COUNT(`id`)  FROM `user` WHERE `name`=".$name." AND `sku_root` !=".$sku_root." LIMIT 1),
			@count_lastname:=(SELECT COUNT(`id`)  FROM `user` WHERE `lastname`=".$lastname." AND `sku_root` !=".$sku_root." LIMIT 1),
			@count_email:=(SELECT COUNT(`id`)   FROM `user` WHERE `email`=".$email." AND `sku_root` !=".$sku_root."  LIMIT 1);
		";
		$sql["check"]="
			IF @count_name > 0  && @count_lastname > 0  THEN 
				SET @message_error='เกิดขอผิดพลาด ชื่อและนามสกุล ที่แก้ไขมา มีแล้ว โปรดลองชื่อและนามสกุลอื่น';
			ELSEIF @count_email > 0 THEN
				SET @message_error='เกิดขอผิดพลาด อีเมลที่แก้ไขมา มีแล้ว โปรดลอง อีเมลในอื่น';
			END IF;			
		";
		$sql["run"]="
			IF LENGTH(@message_error) = 0 THEN
				UPDATE `user` SET  `lastname`=".$lastname.",  `name`= ".$name."  ,  `email`= ".$email." ".$text_pass_set." WHERE `sku_root`=".$sku_root." LIMIT 1;
				SET @result=1;
			END IF;
		";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		$se=$this->metMnSql($sql,["result"]);
		return $se;
	}
	private function editMeSetCurent(string $sku_root):void{
		$od=$this->editMeOldData($sku_root);
		$fl=["name","lastname","email","sku"];
		foreach($fl as $v){
			if(isset($od[$v])){
				$_POST[$v]=$od[$v];
			}
		}
	}
	private function editMeOldData(string $sku_root):array{
		$sku_root=$this->getStringSqlSet($sku_root);
		$sql=[];
		$sql["result"]="SELECT * FROM `user` WHERE `sku_root`=".$sku_root."";
		$sql["get_pos"]="SELECT `id`,`time_id`,`name`,`sku`,`user`,`ip`,
				IFNULL(`money_start`,0) AS `money_start`,
				IFNULL(`money_balance`,0) AS `money_balance`,
				`date_reg`
			FROM `device_pos` 
			WHERE `ip`='".$_SESSION["ip"]."'
		";
		$re=$this->metMnSql($sql,["result","get_pos"]);
		
		if(isset($re["data"]["result"][0])){
			if(isset($re["data"]["get_pos"][0])){
				$this->my_time=$re["data"]["get_pos"][0];
			}	
			return $re["data"]["result"][0];
		}
		return [];
	}
	private function editMePage(string $error):void{
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$lastname=(isset($_POST["lastname"]))?htmlspecialchars($_POST["lastname"]):"";
		$sku=(isset($_POST["sku"]))?htmlspecialchars($_POST["sku"]):"";
		$email=(isset($_POST["email"]))?htmlspecialchars($_POST["email"]):"";
		$userceo=$_SESSION["userceo"];
		$this->addDir("","แก้ไข ฉัน");
		$this->pageHead(["title"=>"ฉัน DIYPOS","css"=>["me"],"js"=>["me","Me"]]);
		$pem=true;
		$dis="";
		echo '<div class="content"><div class="form">';
		$this->writeMyTime();
		echo '<h2 class="c">แก้ไข ฉัน</h2>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}	
		if($_SESSION["sku_root"]=="systemroot"){
			$pem=false;
			$dis=' disabled="disabled"';
			echo '<div class="warning">[[SYSTEM]] คือผู้ใช้ที่ระบบสร้างขึ้นมาเฉพาะ ไม่สามารถแก้ไขได้</div>';
		}	
		echo '		<form name="me" method="post" action="">
					<input type="hidden" name="submith" value="clicksubmit" />
					<input type="hidden" name="ps" value="" />
					<input type="hidden" name="logout" value="" />
					<p><label for="me_name">ชื่อ</label></p>
					<div><input id="me_name" name="name" class="want" type="text" value="'.$name.'"'.$dis.' autocomplete="off" /></div>
					<p><label for="me_lastname">นามสกุล</label></p>
					<div><input  id="me_lastname" type="text" name="lastname" value="'.$lastname.'"'.$dis.' autocomplete="off" /></div>
					<p><label for="me_sku" >รหัสภายใน</label></p>
					<div><input id="me_sku" name="sku"  type="text"  value="'.$sku.'" autocomplete="off"  disabled="disabled" /></div>
					<p><label for="me_email">อีเมล</label></p>
					<div><input id="me_email" name="email" class="want" type="text"  value="'.$email.'"'.$dis.' autocomplete="off" /></div>
					<p><label for="me_userceo">ระดับ</label></p>
					<div><input  id="me_userceo" type="text" name="userceo" value="'.$this->cf["userceo"][$userceo]["name"].'" readonly="readonly"   disabled="disabled" /></div>
					<p><label for="me_ps1">รหัสผ่านใหม่</label></p>
					<div><input id="me_ps1" type="password" value=""  name="ps1"  autocomplete="off"'.$dis.' /></div>
					<p><label for="me_ps2">รหัสผ่านใหม่อีกครั้ง</label></p>
					<div><input id="me_ps2" type="password" value=""  name="ps2"  autocomplete="off"'.$dis.' /></div>
					<br />';
		if($_SESSION["onoff"]==1){
			echo '<input type="button" name="ok" onclick="G.meSubmit()" value="แก้ไข"'.$dis.' /> ';
		}
		echo '<input type="button" name="logoubt" onclick="G.logout()" value="ออกจากระบบ" />
				</form>
			
			</div>
		</div>';
		$this->pageFoot();
	}
	private function writeMyTime():void{
		$ms=number_format($this->my_time["money_start"],2,'.',',');
		$mb=number_format($this->my_time["money_balance"],2,'.',',');
		$d=explode(" ",$this->my_time["date_reg"]);
		$mb="523,254.75";
		echo '<div class="me_time">
			<p>กะทำงานของฉัน</p>
			<div>
				<div class="start">ปิดกะ วันที่<div>'.$d[0].'</div></div>
				<div class="start">เปิดกะ เวลา<div>'.$d[1].' น.</div></div>
				<div id="time_ago" class="time_ago">เปิดกะมานาน<div></div></div>
				<div class="money_start">เงินสดเริ่มต้น<div>'.$ms.'</div></div>
				<div class="money_balance">เงินสดขฌะนี้<div>'.$mb.'</div></div>
			</div><script type="text/javascript">F.showTimeAgo(\'time_ago\',\''.$this->my_time["date_reg"].'\')</script>
		</div>';
	}
}
