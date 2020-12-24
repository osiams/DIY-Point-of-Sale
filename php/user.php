<?php
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
		$user_delto=implode(', ', $this->pem[$this->user_ceo]["user_delto"]);
		$merror="";
		$error="";
		if(isset($_POST["sku_root"])&&preg_match("/^[0-9a-zA-Z]{1,25}$/",$_POST["sku_root"])){
			$sku_root=$this->getStringSqlSet($_POST["sku_root"]);
			$sql=[];
			$sql["set"]="SELECT @result:=0,
				@message_error:='".$merror."',
				@name:='',
				@ceo:=(SELECT  userceo FROM user WHERE `sku_root`=".$sku_root."),
				@name:=(SELECT email FROM user WHERE `sku_root`=".$sku_root."),
				@n_system:=(SELECT COUNT(*) FROM user WHERE `sku_root`='systemroot'),
				@n_admin:=(SELECT COUNT(*) FROM user WHERE `sku_root`='administratorroot');
			";
			$sql["check"]="
				IF IFNULL(@ceo,-1)=-1 THEN 
					SET @message_error='ไม่พบผู้ใช้ ที่ส่งมาโปรดตรวจสอบความถูกต้อง';
				ELSEIF ".$sku_root."=\"administratorroot\" && @n_admin=1 THEN
					SET @message_error=CONCAT(CAST('ไม่สามารถลบผู้ใช้ระดับสูงสุด ' AS CHAR CHARACTER SET utf8),@name,CAST(' ได้ เพราะระบบต้องมีผู้ใช้ระดับสูงสุด อย่างน้อย 1 ผู้ใช้' AS CHAR CHARACTER SET utf8));
				ELSEIF ".$sku_root."=\"systemroot\" && @n_system=1 THEN
					SET @message_error=CONCAT(CAST('ไม่สามารถลบผู้ใช้ ' AS CHAR CHARACTER SET utf8),@name,CAST(' ได้ เพราะระบบต้องมีผู้ใช้สำหรับระบบ อย่างน้อย 1 ผู้ใช้' AS CHAR CHARACTER SET utf8));
				ELSEIF @ceo = 0 THEN	
					SET @message_error=CONCAT(CAST('มีข้อผิดพลาด ไม่สามารถลบผู้ใช ้' AS CHAR CHARACTER SET utf8),@name,CAST(' เนื่องจากเป็นผู้ใช้สำหรับระบบ' AS CHAR CHARACTER SET utf8));
				ELSEIF @ceo NOT IN (".$user_delto.") THEN
					SET @message_error=CONCAT(CAST('มีข้อผิดพลาด ตำแหน่งหรือระดับของคุณ ไม่มีสิทธิ์ ลบ ผู้ใช้ ' AS  CHAR CHARACTER SET utf8),@name);
				END IF;	
				
				
			";
			$sql["del"]="
				IF LENGTH(@message_error) = 0 THEN
					DELETE FROM `user` WHERE `sku_root`=".$sku_root.";
				END IF";
			$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@name AS `name`";
			$qe=$this->metMnSql($sql,["result"]);
			//print_r($qe);
			if(isset($qe["data"]["result"][0])&&$qe["data"]["result"][0]["message_error"]!=""){
					$error=$qe["data"]["result"][0]["message_error"];
			}else if(!$qe["result"]&&$qe["message_error"]!=""){
				$error=$qe["message_error"];
			}else{
				$this->deletePage($qe["data"]["result"][0]["name"],true);
				//header('Location:?a=user&ed='.$_POST["sku_root"]);
			}
			if($error!=""){
				$this->deletePage($error);
			}
		}else{
			$error="ค่าที่ส่งมามบางอย่างผิดพลาด";
			$this->deletePage($error);
		}
		
	}
	protected function deletePage(string $error,bool $result=false):void{
		$this->addDir("","ลบผู้ใช้");
		$this->pageHead(["title"=>"ลบผู้ใช้"]);
		if(!$result){
			echo '<div class="error">'.$error.'</div>';
		}else if($result){
			echo '<div class="success">ผู้ใช้ '.$error.' ไม่มีอยู่ในระบบแล้ว</div>';
		}
		echo '<p class="c"><br /><button onclick="history.back();">ย้อนกลับ</button></p>';
		$this->pageFoot();
	}
	protected function editUser():void{
		//unset($_POST["sku_root"]);
		$sku_root=(isset($_POST["sku_root"]))?$_POST["sku_root"]:"";
		$error="";
		if(isset($_POST["submit"])&&$_POST["submit"]=="clicksubmit"){
			$se=$this->checkSet("user",["post"=>["userceo","sku_root"]],"post");
			//print_r($_POST);
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
			$this->editUserSetCurent($sku_root);
			$this->editUserPage($error);
				//$this->editUserPageBack($error);
			}
		}else{
			$this->editUserSetCurent($sku_root);
			$this->editUserPage($error);
		}
	}
	private function editUserUpdate():array{
		$int_userceo=(int) $_POST["userceo"];
		$sku_root=$this->getStringSqlSet($_POST["sku_root"]);
		$userceo=$this->getStringSqlSet($_POST["userceo"]);
		$skuk=$this->key("key",7);
		$sku_key=$this->getStringSqlSet($skuk);
		$user_editto=implode(', ', $this->pem[$this->user_ceo]["user_editto"]);
		$user_regceoto=implode(', ', $this->pem[$this->user_ceo]["user_regceoto"]);
		$ceo_name="";
		if(((int) $_POST["userceo"]) >=0 && isset($this->cf["userceo"][$_POST["userceo"]])){
			$ceo_name=htmlspecialchars($this->cf["userceo"][(int) $_POST["userceo"]]["name"]);
		}
		$merror="";
		$resetps="";
		if(!isset($_POST["userceo"])||!preg_match("/^[0-9]{1}$/",$_POST["userceo"])){
			$merror="มีข้อผิดพลาดข้อมูลที่ส่งมา userceo";
		}/*else if((int) $_SESSION["userceo"]<=(int) $_POST["userceo"]&&(int) $_SESSION["userceo"]!=9){
			$merror="มีข้อผิดพลาด ระดับคุณต่ำกว่าหรือเท่ากัน กับผู้ใช้ที่กำลังแก้ไข";
		}*/
		if((int) $_SESSION["userceo"]>=8){
			if(isset($_POST["resetpassword"])&&$_POST["resetpassword"]=="12345678"){
				$resetps=" ,`password`=\"".password_hash("12345678", PASSWORD_DEFAULT)."\" ";
			}
		}
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='".$merror."',
			@ceo:=(SELECT  userceo FROM user WHERE `sku_root`=".$sku_root.");
		";
		$sql["check"]="
			IF @ceo NOT IN (".$user_editto.") THEN
				SET @message_error='มีข้อผิดพลาด ตำแหน่งหรือระดับ ของคุณ ไม่มีสิทธิ์แก้ไข้ผู้ใช้นี้ได้';
			ELSEIF ".$int_userceo."=0 THEN
				SET @message_error='มีข้อผิดพลาด  ".$ceo_name." เป็นผู้ใช้สำหรับระบบ ไม่สามาถแก้ไขได้';	
			ELSEIF ".$int_userceo." NOT IN (".$user_regceoto.") THEN
				SET @message_error='มีข้อผิดพลาด คุณไม่มีสิทธิ์ ตั้งระดับผู้ใช้นี้ให้เป็น ".$ceo_name."';
			END IF;
			
		";
		$sql["run"]="
			IF LENGTH(@message_error) = 0 THEN
				UPDATE `user` SET  `sku_key`=".$sku_key.",`userceo`=".$userceo."".$resetps."  WHERE `sku_root`=".$sku_root." AND `sku_root` != 'systemroot';
				SET @result=1;
			END IF;
		";
		$sql["ref"]=$this->ref("user","sku_key",$skuk);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($sql);exit;
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
		$this->addDir("","แก้ไขผู้ใช้ ".$name);
		$this->pageHead(["title"=>"แก้ไขผู้ใช้ DIYPOS"]);
		$pem=true;
		$dis="";
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
			if($sku_root==$_SESSION["sku_root"]){
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
		$this->pageFoot();
	}
	protected function editUserPageBack(string $error):void{
		$this->addDir("","แก้ไขผู้ใช้ ผิดพลาด");
		$this->pageHead(["title"=>"แก้ไขผู้ใช้ ผิดพลาด"]);
		echo '<div class="error">'.$error.'</div>
			<br />
			<p class="c">
				<button  onclick="location.href=\'?a=user\'">ไปหน้าแสดงผู้ใช้อื่นๆ</button>
			</p>';
		$this->pageFoot();
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
		$int_userceo=(int) $_POST["userceo"];
		$name=$this->getStringSqlSet($_POST["name"]);
		$lastname=$this->getStringSqlSet($_POST["lastname"]);
		$email=$this->getStringSqlSet($_POST["email"]);
		$userceo=$this->getStringSqlSet($_POST["userceo"]);
		$password=$this->getStringSqlSet(password_hash("12345678", PASSWORD_DEFAULT));
		$skur=$this->key("key",7);
		$sku_root=$this->getStringSqlSet($skur);
		$user_regceoto=implode(', ', $this->pem[$this->user_ceo]["user_regceoto"]);
		$ceo_name="";
		if(((int) $_POST["userceo"]) >=0 && isset($this->cf["userceo"][$_POST["userceo"]])){
			$ceo_name=htmlspecialchars($this->cf["userceo"][(int) $_POST["userceo"]]["name"]);
		}
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
			ELSEIF ".$int_userceo." NOT IN (".$user_regceoto.") THEN
				SET @message_error='มีข้อผิดพลาด คุณไม่มีสิทธิ์ ลงทะเบียนผู้ใช้ให้เป็น ".$ceo_name."';
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
			if($v >= 0){
				$sel=($v==$sku_root)?' selected="selected"':'';
				if(isset($this->cf["userceo"][$v])&&isset($this->cf["userceo"][$v]["name"])){
					$tx=$this->cf["userceo"][$v]["name"];
					echo '<option value="'.$v.'"'.$sel.'>'.$tx.'</option>';
				}
			}
		}
	}
	private function pageUser(){
		$this->pageHead(["title"=>"ผู้ใช้ DIYPOS","css"=>["user"]]);
			echo '<div class="content">
				<div class="form">
					<h1 class="c">ผู้ใช้</h1>';
					//echo implode(', ', $this->pem[$this->user_ceo]["user_editto"]);
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
			if($this->cf["userceo"][$u]!=null){
				$userceo=$this->cf["userceo"][$u]["name"];
			}
			$cm=($i%2!=0)?" class=\"i2\"":"";
			echo '<tr'.$cm.'><td>'.$se[$i]["id"].'</td>
				<td>'.$se[$i]["sku"].'</td>
				<td>
					<div class="l">'.$se[$i]["name"].' '.$se[$i]["lastname"].'</div>
					<div>'.$se[$i]["email"].'</div>
				</td>
				<td class="l">'.$se[$i]["email"].'</td>
				<td>
					<div class="l">'.$userceo.'</div>
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
