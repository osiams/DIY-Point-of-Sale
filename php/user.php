<?php  /*--apt DIY_POS;--ext php;--version 0.0;*/
class user extends main{
	public function __construct(){
		parent::__construct();
	}
	public function run(){
		$q=["regis","edit","delete"];
		$this->addDir("?a=user","ผู้ใช้");
		if(isset($_GET["b"])&&in_array($_GET["b"],$q)){
			$t=$_GET["b"];
			if($t=="regis"){
				$this->regisUser();
			}else if($t=="edit"){
				$this->editUser();
			}else if($t=="delete"){
				$this->deleteUser();
			}
		}else{
			$this->pageUser();
		}
	}
	private function deleteUser():void{
		if(isset($_POST["sku_root"])){
			$sku_root=$this->getStringSqlSet($_POST["sku_root"]);
			$sql=[];
			$sql["del"]="DELETE FROM `user` WHERE `sku_root`=".$sku_root." AND  `sku_root` != \"administratorroot\"";
			$this->metMnSql($sql,[]);
			header('Location:?a=user&ed='.$_POST["sku_root"]);
		}
	}
	protected function editUser():void{
		$error="";
		if(isset($_POST["submit"])&&$_POST["submit"]=="clicksubmit"){
			$se=$this->checkSet("user",["post"=>["userceo"]],"post");
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				 $qe=$this->editUserUpdate();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					 //print_r($qe);
					header('Location:?a=user&ed='.$_POST["sku_root"]);
				}
			}
			if($error!=""){
				$this->editUserPage($error);
			}
		}else{
			$sku_root=(isset($_POST["sku_root"]))?$_POST["sku_root"]:"";
			$this->editUserSetCurent($sku_root);
			$this->editUserPage($error);
		}
	}
	private function editUserUpdate():array{
		$sku_root=$this->getStringSqlSet($_POST["sku_root"]);
		$userceo=$this->getStringSqlSet($_POST["userceo"]);
		$skuk=$this->key("key",7);
		$sku_key=$this->getStringSqlSet($skuk);
		if($_POST["sku_root"]==$_SESSION["sku_root"]){
			$userceo="\"9\"";
			$resetps="";
			if(isset($_POST["resetpassword"])&&$_POST["resetpassword"]=="12345678"){
				$resetps=" ,`password`=\"".password_hash("12345678", PASSWORD_DEFAULT)."\" ";
			}$this->getStringSqlSet($_POST["sku_root"]);
		}
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='';
		";
		$sql["run"]="
			IF LENGTH(@message_error) = 0 THEN
				UPDATE `user` SET  `sku_key`=".$sku_key.",`userceo`=".$userceo."".$resetps."  WHERE `sku_root`=".$sku_root.";
				SET @result=1;
			END IF;
		";
		$sql["ref"]=$this->ref("user","sku_key",$skuk);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($sql);
		return $se;
	}
	protected function editUserSetCurent(string $sku_root):void{
		$od=$this->editUserOldData($sku_root);
		foreach($od as $k=>$v){
			$_POST[$k]=$od[$k];
		}
		$fl=["userceo"];
		foreach($fl as $v){
			if(isset($od[$v])){
				$_POST[$v]=$od[$v];
			}
		}
	}
	protected function editUserOldData(string $sku_root):array{
		$sku_root=$this->getStringSqlSet($sku_root);
		$sql=[];
		$sql["result"]="SELECT `name`,`lastname`,`sku`,`userceo`,`email` FROM `user` WHERE `sku_root`=".$sku_root."";
		$re=$this->metMnSql($sql,["result"]);
		if(isset($re["data"]["result"][0])){
			return $re["data"]["result"][0];
		}
		return [];
	}
	protected function editUserPage(string $error):void{
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$lastname=(isset($_POST["lastname"]))?htmlspecialchars($_POST["lastname"]):"";
		$sku=(isset($_POST["sku"]))?htmlspecialchars($_POST["sku"]):"";
		$email=(isset($_POST["email"]))?htmlspecialchars($_POST["email"]):"";
		$sku_root=(isset($_POST["sku_root"]))?htmlspecialchars($_POST["sku_root"]):"";
		$userceo=(isset($_POST["userceo"]))?htmlspecialchars($_POST["userceo"]):"0";
		$this->addDir("","แก้ไขหน่วยสินค้า ".$name);
		$this->pageHead(["title"=>"แก้ไขหน่วยสินค้า DIYPOS"]);
		echo '<div class="content">
			<div class="form">
				<h1 class="c">แก้ไขผู้ใช้</h1>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}		
		echo '		<form method="post">
					<input type="hidden" name="submit" value="clicksubmit" />
					<input type="hidden" name="sku_root" value="'.$sku_root.'" />
					<p><label for="user_name">ชื่อ</label></p>
					<div><input id="user_name" class="want" type="text" name="name" value="'.$name.'" autocomplete="off"   disabled="disabled" /></div>
					<p><label for="user_lastname">นามสกุล</label></p>
					<div><input id="user_lastname" type="text" name="lastname" value="'.$lastname.'" autocomplete="off" disabled="disabled" /></div>
					<p><label for="user_sku">อีเมล</label></p>
					<div><input id="user_sku" type="text" value="'.$sku.'"  name="sku"  autocomplete="off"  disabled="disabled" /></div>
					<p><label for="user_email">อีเมล</label></p>
					<div><input  id="user_email" class="want" type="text" value="'.$email.'"  name="email" autocomplete="off"   disabled="disabled" /></div>
					';
			$admindis="";
			if($_POST["sku_root"]==$_SESSION["sku_root"]){
				$admindis=" disabled=\"disabled\"";
			}		
			echo '<p><label for="user_userceo">ระดับ</label></p>
							<div><select id="user_userceo" name="userceo"'.$admindis.'>';
							$this->writeSelectOption($userceo);
			echo '</select></div>
				<fieldset>
				<legend>คืนค่ารหัสผ่านเริมต้น</legend>
				<div><input type="checkbox" name="resetpassword" value="12345678"'.$admindis.' /> คืนค่ารหัสผ่านเริ่มต้น เป็น 12345678</div>
				</fieldset>';	
					echo '<br />
					<input type="submit" value="แก้ไข" />
				</form>
			</div>
		</div>';
	}
	protected function regisUser():void{
		$error="";
		if(isset($_POST["submith"])&&$_POST["submith"]=="clicksubmit"){
			$se=$this->checkSet("user",["post"=>["name","lastname","email","userceo"]],"post");
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				 $qe=$this->regisUserInsert();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					 //print_r($qe);
					header('Location:?a=user&ed='.$qe["data"]["result"][0]["sku_root"]);
				}
			}
			if($error!=""){
				$this->regisUserPage($error);
			}
		}else{
			$this->regisUserPage($error);
		}
	}
	protected function regisUserInsert():array{
		$name=$this->getStringSqlSet($_POST["name"]);
		$lastname=$this->getStringSqlSet($_POST["lastname"]);
		$email=$this->getStringSqlSet($_POST["email"]);
		$userceo=$this->getStringSqlSet($_POST["userceo"]);
		$password=$this->getStringSqlSet(password_hash("12345678", PASSWORD_DEFAULT));
		$skur=$this->key("key",7);
		$sku_root=$this->getStringSqlSet($skur);
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@sku_root:=".$sku_root.",
			@id:=(SELECT (SELECT MAX(id) FROM `user`)+1),
			@sku:=(SELECT CONCAT('00',LPAD(CAST(@id AS CHAR),5,'0'))),
			@count_name:=(SELECT COUNT(`id`)  FROM `user` WHERE `name`=".$name."),
			@count_lastname:=(SELECT COUNT(`id`)  FROM `user` WHERE `lastname`=".$lastname."),
			@count_email:=(SELECT COUNT(`id`)   FROM `user` WHERE `email`=".$email.");
		";
		$sql["check"]="
			IF @count_name > 0  && @count_lastname > 0  THEN 
				SET @message_error='เกิดขอผิดพลาด ชื่อและนามสกุล ที่ส่งมา มีแล้ว โปรดลองชื่อและนามสกุลอื่น';
			ELSEIF @count_email > 0 THEN
				SET @message_error='เกิดขอผิดพลาด อีเมลที่ส่งมา มีแล้ว โปรดลอง อีเมลในอื่น';
			END IF;			
		";
		$sql["run"]="
			IF LENGTH(@message_error) = 0 THEN
				INSERT INTO `user`  (`id`,`sku`,`sku_key`,`sku_root`,`name`,`lastname`,`email`,`password`,`userceo`) 
				VALUES (@id,@sku,".$sku_root.",".$sku_root.",".$name.",".$lastname.",".$email.",".$password.",".$userceo.");
				SET @result=1;
			END IF;
		";
		$sql["ref"]=$this->ref("user","sku_key",$skur);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@sku_root AS `sku_root`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($sql);
		return $se;
	}
	protected function regisUserPage(string $error):void{
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$lastname=(isset($_POST["lastname"]))?htmlspecialchars($_POST["lastname"]):"";
		$userceo=(isset($_POST["userceo"]))?htmlspecialchars($_POST["userceo"]):"1";
		$email=(isset($_POST["email"]))?htmlspecialchars($_POST["email"]):"";
		$this->addDir("?a=user&amp;b=regis","เพิ่มผู้ใช้");
		$this->pageHead(["title"=>"เพิ่มผู้ใช DIYPOS"]);
		echo '<div class="content">
			<div class="form">
				<h1 class="c">เพิ่มผู้ใช้</h1>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}		
		echo '		<form method="post">
					<input type="hidden" name="submith" value="clicksubmit" />
					<p><label for="user_name">ชื่อ</label></p>
					<div><input id="user_name" class="want" type="text" name="name" value="'.$name.'" autocomplete="off" /></div>
					<p><label for="user_lastname">นามสกุล</label></p>
					<div><input  id="user_lastname"  type="text" name="lastname" value="'.$lastname.'" autocomplete="off" /></div>
					<p><label for="user_email">อีเมล</label></p>
					<div><input  id="user_email" class="want" type="text" value="'.$email.'"  name="email" autocomplete="off"  /></div>
					<p><label for="user_password">รหัสผ่าน</label></p>
					<div><input  id="user_password" type="text" value="12345678"  name="password" autocomplete="off"  readonly="readonly" disabled="disabled" /></div>';
			echo '<p><label for="user_userceo">ระดับ</label></p>
							<div><select id="user_userceo" name="userceo">';
							$this->writeSelectOption($userceo);
			echo '</select></div>';	
			echo '		<br />
					<input type="submit" value="เพิ่มผู้ใช้" />
				</form>
			</div>
		</div>';
		$this->pageFoot();
	}
	private function writeSelectOption(string $sku_root="1"):void{
		foreach($this->fills["userceo"]["length_value"] as $v){
			$sel=($v==$sku_root)?' selected="selected"':'';
			if(isset(CF["userceo"][$v])&&isset(CF["userceo"][$v]["name"])){
				$tx=CF["userceo"][$v]["name"];
				echo '<option value="'.$v.'"'.$sel.'>'.$tx.'</option>';
			}
		}
	}
	private function pageUser(){
		$this->pageHead(["title"=>"ผู้ใช้ DIYPOS","css"=>["user"]]);
			echo '<div class="content">
				<div class="form">
					<h1 class="c">ผู้ใช้</h1>';
			$this->writeContentUser();
			echo '<br /><p class="c"><input type="button" value="เพิ่มผู้ใช้" onclick="location.href=\'?a=user&b=regis\'" /></p>';
		$this->pageFoot();
	}
	protected function writeContentUser():void{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$se=$this->getAllUser();
		echo '<form class="form100" name="user" method="post">
			<input type="hidden" name="sku_root" value="" />
			<table id="user"><tr><th>ที่</th>
			<th>รหัสภายใน</th>
			<th>ชื่อ-นามสกุล</th>
			<th>อีเมล</th>
			<th>ระดับ</th>
			<th>วันที่ลงทะเบียน</th>
			<th>กระทำ</th>
			</tr>';
		for($i=0;$i<count($se);$i++){
			$ed='';
			if($se[$i]["sku_root"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}
			$u=$se[$i]["userceo"];
			if(CF["userceo"][$u]!=null){
				$userceo=CF["userceo"][$u]["name"];
			}
			$cm=($i%2!=0)?" class=\"i2\"":"";
			echo '<tr'.$cm.'><td>'.$se[$i]["id"].'</td>
				<td>'.$se[$i]["sku"].'</td>
				<td>
					<div class="l">'.$se[$i]["name"].' '.$se[$i]["lastname"].'</div>
					<div>'.$se[$i]["email"].'</div>
				</td>
				<td>'.$se[$i]["email"].'</td>
				<td>
					<div>'.$userceo.'</div>
					<div>'.$se[$i]["sku"].'</div>
				</td>
				<td>'.$se[$i]["date_reg"].'</td>
				<td class="action">
					<a onclick="G.userEdit(\''.$se[$i]["sku_root"].'\')" title="แก้ไข">📝</a>';
			if($_SESSION["sku_root"]!=$se[$i]["sku_root"]	){
				echo '	<a onclick="G.userDelete(\''.$se[$i]["sku_root"].'\',\''.htmlspecialchars($se[$i]["name"]." ".$se[$i]["lastname"]).'\')" title="ทิ้ง">🗑</a>';
			}
			echo 		''.$ed.'
					</td>
				</tr>';
		}
		echo '</table></form>';
	}
	protected function getAllUser():array{
		$re=[];
		$sql=[];
		$sql["get"]="SELECT * FROM `user` ORDER BY `id` DESC";
		$se=$this->metMnSql($sql,["get"]);
		if($se["result"]){
			$re=$se["data"]["get"];
		}
		return $re;
	}
}
?>
