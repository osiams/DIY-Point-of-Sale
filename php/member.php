<?php
class member extends main{
	private  $d_cols;
	public function __construct(){
		parent::__construct();
		$this->img=null;
		$this->title = "สมาชิก";
		$this->a = "member";
		$this->mb_type = ["s"=>"🏠 ผู้ประกอบการ","p"=>"🧑 ผู้บริโภค"];
		$this->sex = ["m"=>"♂️ ชาย","f"=>"♀️ หญิง","n"=>"ไม่ระบุ"];
		$this->per=10;
		$this->page=1;
		$this->txsearch="";
		$this->fl="";
		$this->lid=0;
		$this->sh="";
		$this->max_squar=256;
	}
	public function run(){
		$file = "php/class/image.php";
		require($file);		
		$this->img=new image($this->gallery_dir);		
		$this->page=$this->setPageR();
		$q=["regis","edit","delete","details","iframeregissuccess"];
		$this->addDir("?a=".$this->a,$this->title);
		if(isset($_GET["b"])&&in_array($_GET["b"],$q)){
			$t=$_GET["b"];
			if($t=="regis"){
				$this->regisMember();
			}else if($t=="edit"){
				$this->editMember();
			}else if($t=="delete"){
				$this->deleteMember();
			}else if($t=="details"){
				if(isset($_GET["sku_root"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["sku_root"])){
					require("php/member_details.php");
					(new member_details($_GET["sku_root"]))->run();
				}else{
					$this->pageMember();
				}
			}else if($t=="iframeregissuccess"){
				$this->iframeRegisSuccess();
			}
		}else{
			$this->pageMember();
		}
	}
	public function fetch(){
		$re=["result"=>false,"message_error"=>"","data"=>[]];
		if(isset($_POST["b"])&&$_POST["b"]=="getmember1"){
			if(isset($_POST["sku_root"])&&$this->isSKU($_POST["sku_root"])){
				$se=$this->fetchGetMember1($_POST["sku_root"]);
				if(count($se["data"])>0){
					$re["result"]=true;
					$re["data"]=$se["data"];
				}else{
					$re["message_error"]=$se["message_error"];
				}
			}		
		}
		header('Content-type: application/json');
		echo json_encode($re);
	}
	private function fetchGetMember1(string $sku_root):array{
		$re=["result"=>false,"message_error"=>"","data"=>[]];
		$sql=[];
		$sql["get"]="SELECT `name`,IFNULL(`lastname`,'') AS `lastname`,IFNULL(`icon`,'null.png') AS `icon`,`sku`,`sku_root` ,
				`mb_type`
			FROM `member` 
			WHERE `sku_root`='".$sku_root."'";
		$se=$this->metMnSql($sql,["get"]);
		if($se["result"]){
			if(count($se["data"]["get"])>0){
				$re["result"]=true;
				$re["data"]=$se["data"]["get"][0];
			}
		}else{
			$re["message_error"]=$se["message_error"];
		}
		return $re;
	}
	private function deleteMember():void{
		if(isset($_POST["sku_root"])){
			$url_refer=(isset($_GET["url_refer"]))?$_GET["url_refer"]:"";
			$sku_root=$this->getStringSqlSet($_POST["sku_root"]);
			$sql=[];
			$sql["set"]="SELECT @sku_root:=".$sku_root.";";
			$sql["del"]="DELETE FROM `member` WHERE `sku_root`=".$sku_root;
			$re = $this->metMnSql($sql,[]);
			$pt="/&ed=[0-9a-zA-Z-+\.&\/]{1,25}/";
			$pr='';
			$url=preg_replace($pt, $pr,$url_refer)."&ed=".$_POST["sku_root"];
			header('Location:'.$url);
		}
	}
	private function editMemberPage(string $error):void{
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$icon_load=(isset($_POST["icon_load"]))?$_POST["icon_load"]:"";
		$icon=(isset($_POST["icon"]))?$_POST["icon"]:"";
		$lastname=(isset($_POST["lastname"]))?htmlspecialchars($_POST["lastname"]):"";
		$idc=(isset($_POST["idc"]))?htmlspecialchars($_POST["idc"]):"";	
		$sex=(isset($_POST["sex"])&&isset($this->sex[$_POST["sex"]]))?$_POST["sex"]:"n";
		$birthday=(isset($_POST["birthday"]))?htmlspecialchars($_POST["birthday"]):"";
		$tel=(isset($_POST["tel"]))?htmlspecialchars($_POST["tel"]):"";
		$mb_type = (isset($_POST["mb_type"]))?htmlspecialchars($_POST["mb_type"]):"";
		$mb_type=(!isset($this->mb_type[$mb_type]))?"p":$mb_type;
		$no=(isset($_POST["no"]))?htmlspecialchars($_POST["no"]):"";
		$alley=(isset($_POST["alley"]))?htmlspecialchars($_POST["alley"]):"";
		$road=(isset($_POST["road"]))?htmlspecialchars($_POST["road"]):"";
		$distric=(isset($_POST["distric"]))?htmlspecialchars($_POST["distric"]):"";
		$country=(isset($_POST["country"]))?htmlspecialchars($_POST["country"]):"";
		$province=(isset($_POST["province"]))?htmlspecialchars($_POST["province"]):"";
		$post_no=(isset($_POST["post_no"]))?htmlspecialchars($_POST["post_no"]):"";
		$disc=(isset($_POST["disc"]))?htmlspecialchars($_POST["disc"]):"";
		$sku_root=(isset($_POST["sku_root"]))?htmlspecialchars($_POST["sku_root"]):"";
		$this->addDir("","แก้ไข".$this->title);
		$this->pageHead(["title"=>"แก้ไข".$this->title." DIYPOS","js"=>["member","Mb","fileupload","Ful"],"run"=>["Mb"],"css"=>["member","fileupload"]]);
		echo '<div class="content">
			<div class="form">
				<h1 class="c">แก้ไข'.$this->title.'</h1>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}		
		echo '		<form  name="member" method="post" onsubmit="Mb.checkIsChange()">
			<input type="hidden" name="submit" value="clicksubmit" />
			<input type="hidden" id="icon_load_id" name="icon_load" value="'.$icon_load.'" />
			<input type="hidden" id="icon_id" name="icon" value="" />
			<input type="hidden" id="setimgnull" name="setimgnull" value="0" />
			<input type="hidden" name="sku_root" value="'.$sku_root.'" />
			<p><label for="member_name">ชื่อ</label></p>
			<div><input id="member_name" class="want" type="text" name="name" value="'.$name.'" autocomplete="off" /></div>
			<p><label for="member_lastame">นามสกุล</label></p>
			<div><input id="member_lastame" type="text" name="lastname" value="'.$lastname.'" autocomplete="off" /></div>
			<p><label for="sex">เพศ</label></p>
			<div>
				<select name="sex">
					<option value=""'.($sex == ""?" selected":"").'>'.$this->sex["n"].'</option>
					<option value="m"'.($sex == "m"?" selected":"").'>'.$this->sex["m"].'</option>					
					<option value="f"'.($sex == "f"?" selected":"").'>'.$this->sex["f"].'</option>
				</select>
			</div>
			<p><label for="member_birthday">วัน-เดือน-ปี เกิด</label></p>
			<div><input id="member_birthday" type="date" name="birthday" value="'.$birthday.'" /></div>
			<p><label for="mb_type">ประเภทสมาชิก</label></p>
			<div>
				<select name="mb_type">
					<option value="p"'.($mb_type == "p"?" selected":"").'>'.$this->mb_type["p"].'</option>					
					<option value="s"'.($mb_type == "s"?" selected":"").'>'.$this->mb_type["s"].'</option>
				</select>
			</div>
			<p><label for="tel">โทรศัพท์</label></p>
			<div><input id="tel" type="text" value="'.$tel.'"  name="tel" autocomplete="off"  /></div>
			<p><label for="no">ที่อยู่ เลขที่</label></p>
			<div><input id="no" type="text" value="'.$no.'"  name="no" autocomplete="off"  /></div>
			<p><label for="alley">ซอย</label></p>
			<div><input id="alley" type="text" value="'.$alley.'"  name="alley" autocomplete="off"  /></div>
			<p><label for="road">ถนน</label></p>
			<div><input id="road" type="text" value="'.$road.'"  name="road" autocomplete="off"  /></div>
			<p><label for="distric">แขวง/ตำบล</label></p>
			<div><input id="distric" type="text" value="'.$distric.'"  name="distric" autocomplete="off"  /></div>
			<p><label for="country">เขต/อำเภอ</label></p>
			<div><input id="country" type="text" value="'.$country.'"  name="country" autocomplete="off"  /></div>
			<p><label for="province">จังหวัด</label></p>
			<div><input id="province" type="text" value="'.$province.'"  name="province" autocomplete="off"  /></div>
			<p><label for="post_no">รหัสไปรษณี</label></p>
			<div><input id="post_no" type="text" value="'.$post_no.'"  name="post_no" autocomplete="off"  /></div>
			<p><label for="member_disc">รายละเอียดย่อ</label></p>
			<div><input id="member_disc" type="text" value="'.$disc.'"  name="disc" autocomplete="off"  /></div>
			<div>
				<div id="div_fileuploadpre" class="fileuploadpre1"></div>
				<input id="upload_pic" type="file" accept="image/png,image/gif,image/jpeg,image/webp" class="fuif" name="picture" onchange="Ful.fileUploadShow(event,1,\'icon_id\',1024,160)" />
				<label for="upload_pic"  class="fubs">+เลือกรูปภาพ</label>
			</div>	
		';			
		echo '</table>
					<br />
					<input type="submit" value="แก้ไข '.$this->title.'" onclick="Mb.checkIsChange()" />
				</form>
				<script type="text/javascript">Ful.fileUploadShow(null,1,\'icon_id\',480,160,\'load\',\'div_fileuploadpre\',\'icon_load_id\');Mb.setOldData();</script>
			</div>
		</div>';
		$this->pageFoot();
	}
	private function editMember():void{
		$error="";
		$img=["result"=>false,"set"=>0];
		$mime="";
		$set_img=0;
		if(isset($_POST["submit"])&&$_POST["submit"]=="clicksubmit"){
			$se=$this->checkSet("member",["post"=>["name","lastname","sex","birthday","mb_type","idc","tel","no","alley","road","distric","country","province","post_no","disc"]],"post");
			if(isset($_POST["icon"])&&$_POST["icon"]!=""){
				$img=$this->img->imgCheck("icon");
				if($img["result"]==false&&$se["result"]){
					$se["result"]=false;
					$se["message_error"]=$img["message_error"];
				}
				if($img["result"]){
					$a=explode("/",$img["mime"]);
					$mime=$a[1];
					$img["set"]=1;
				}
			}else if(isset($_POST["icon_load"])&&$_POST["icon_load"]==""){
				$img=["result"=>false];
				$img["set"]=-1;
			}
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				$key=$this->key("key",7);
				 $qe=$this->editMemberUpdate($key,$img,$mime);
				 print_r($qe);
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					 if($img["result"]){
						$this->img->imgSave($img,$key,$this->max_squar);
					}
					header('Location:?a='.$this->a.'&ed='.$_POST["sku_root"]);
				}
			}
			if($error!=""){
				$this->editMemberPage($error);
			}
		}else{
			$sku_root=(isset($_POST["sku_root"]))?$_POST["sku_root"]:"";
			$this->editMemberSetCurent($sku_root);
			$this->editMemberPage($error);
		}
	}
	private function editMemberUpdate(string $key,array $img,string $mime):array{
		//print_r($img);
		$name=$this->getStringSqlSet($_POST["name"]);
		$lastname=$this->getStringSqlSet($_POST["lastname"]);
		$icon=(isset($_POST["icon"]))?$_POST["icon"]:"";
		$icon_load=(isset($_POST["icon_load"]))?$_POST["icon_load"]:"";
		$sex=(isset($_POST["sex"])&&isset($this->sex[$_POST["sex"]]))?$this->getStringSqlSet($_POST["sex"]):"NULL";
		$setimgnull=(isset($_POST["setimgnull"])&&$_POST["setimgnull"]=="1")?1:0;
		$tel=$this->getStringSqlSet($_POST["tel"]);
		$idc=$this->getStringSqlSet($_POST["idc"]);
		$mb_type = $this->getStringSqlSet($_POST["mb_type"]);
		$birthday=$_POST["birthday"];
		$birthday=$this->setDateR($birthday,"00:00:00");
		$birthday=($birthday=="")?"NULL":$this->getStringSqlSet($birthday);
		$no=$this->getStringSqlSet($_POST["no"]);
		$alley=$this->getStringSqlSet($_POST["alley"]);
		$road=$this->getStringSqlSet($_POST["road"]);
		$distric=$this->getStringSqlSet($_POST["distric"]);
		$country=$this->getStringSqlSet($_POST["country"]);
		$province=$this->getStringSqlSet($_POST["province"]);
		$post_no=$this->getStringSqlSet($_POST["post_no"]);
		$disc=$this->getStringSqlSet($_POST["disc"]);
		$sku_root=$this->getStringSqlSet($_POST["sku_root"]);
		$skuk=$key;
		$sku_key=$this->getStringSqlSet($skuk);
		
		$mimefull="NULL";
		$md5="NULL";
		$user="NULL";
		$size=0;
		$width=0;
		$height=0;
		if($img["set"]==1){
			$mimefull=$this->getStringSqlSet($img["mime"]);
			$md5=$this->getStringSqlSet(md5($img["file"]));
			$user=$this->getStringSqlSet($_SESSION["sku_root"]);
			$size=(int) $img["size"];
			$width=(int) $img["width"];
			$height=(int) $img["height"];
		}
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@img_set:=".$img["set"].",
			@count_name:=(SELECT COUNT(`id`)  FROM `member` WHERE `name`=".$name." AND `sku_root`!=".$sku_root."),
			@count_lastname:=(SELECT COUNT(`id`)  FROM `member` WHERE `lastname`=".$lastname." AND `sku_root`!=".$sku_root."),
			@count_tel:=(SELECT COUNT(`id`)   FROM `member` WHERE `tel`=".$tel."  AND `sku_root`!=".$sku_root."),
			@count_idc:=(SELECT COUNT(`id`)   FROM `member` WHERE `idc`=".$idc." AND `sku_root`!=".$sku_root.");
		";
		$sql["check"]="
			IF @count_name > 0 AND @count_lastname > 0 THEN 
				SET @message_error='เกิดขอผิดพลาด ชื่อ และนามสกุลที่ส่งมา มีแล้ว โปรดลองชื่ออื่น';
			ELSEIF @count_sku > 0 THEN
				SET @message_error='เกิดขอผิดพลาด รหัสภายในที่ส่งมา มีแล้ว โปรดลอง รหัสภายในอื่น';
			END IF;			
		";
		$icon_tx="";
		if($icon!=""){
			$icon_tx=",`icon`='".$key.".".$mime."'";
		}else if($icon_load==""){
			$icon_tx=",`icon`=NULL";
		}else if($setimgnull==1){
			$icon_tx=",`icon`=NULL";
		}
		$sql["run"]="
			IF LENGTH(@message_error) = 0 THEN
				UPDATE `member` SET  
					`sku_key`=".$sku_key."		,`name`= ".$name." 			,`lastname`= ".$lastname." ,
					`mb_type`=".$mb_type."	".$icon_tx."			,`birthday`=".$birthday."					,`sex`=".$sex.",
					`no`= ".$no."					,`alley`= ".$alley.",	`road`= ".$road.",
					`distric`= ".$distric."			,`country`= ".$country."		,`province`= ".$province."	,`post_no`= ".$post_no.",
					`tel`= ".$tel."					,`idc`= ".$idc."					,`disc`= ".$disc."	
				WHERE `sku_root`=".$sku_root.";
				IF @img_set=1 THEN
					INSERT  INTO `gallery` (
						`sku_key`		,`name`		,`a_type`		,`mime_type`		,`md5`,
						`user`			,`size`			,`width`		,`height`
					) VALUES (
						".$sku_key."	,".$name."		,'member'		,".$mimefull."				,".$md5.",
						".$user."		,".$size."		,".$width."		,".$height."
					);
				END IF;
				SET @result=1;
			END IF;
		";
		$sql["ref"]=$this->ref("member","sku_key",$skuk);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		$se=$this->metMnSql($sql,["result"]);
		print_r($sql);
		return $se;
	}
	private function editMemberOldData(string $sku_root):array{
		$sku_root=$this->getStringSqlSet($sku_root);
		$sql=[];
		$sql["result"]="SELECT 
				`name`		,`lastname`		,`sku`		,`sku_root`	,`mb_type`		,IFNULL(`icon`,'') AS `icon`,
				`sex`		,`birthday`	,`tel`			,`idc`				,`no`,
				`alley`		,`road`			,`distric`,
				`country`,`province`,`post_no`,`disc`
			FROM `member` 
			WHERE `sku_root`=".$sku_root."";
		$re=$this->metMnSql($sql,["result"]);
		if(isset($re["data"]["result"][0])){
			if($re["data"]["result"][0]["icon"]!=""){
				$re["data"]["result"][0]["icon_load"]=$this->img2Base64($this->gallery_dir."/".$re["data"]["result"][0]["icon"]);
				unset($re["data"]["result"][0]["icon"]);
			}
			$re["data"]["result"][0]["birthday"]=explode(" ",$re["data"]["result"][0]["birthday"])[0];
			return $re["data"]["result"][0];
		}
		return [];
	}
	private function editMemberSetCurent(string $sku_root):void{
		$od=$this->editMemberOldData($sku_root);
		$fl=["sku","name","lastname","mb_type","icon_load",
					"sex","birthday","no","alley","road","distric","country",
					"province","post_no",
					"tel","idc","disc"];
		foreach($fl as $v){
			if(isset($od[$v])){
				$_POST[$v]=$od[$v];
				if($v == "prop"){
					$_POST[$v] = $this->propToFromValue($od[$v]);
				}
			}
		}
	}
	protected function regisMember():void{
		$error="";
		$img=["result"=>false,"set"=>0];
		$mime="";
		if(isset($_POST["submit"])&&$_POST["submit"]=="clicksubmit"){
			$se=$this->checkSet("member",["post"=>["name","lastname","sex","birthday","mb_type","idc","tel","no","alley","road","distric","country","province","post_no","disc"]],"post");
			if(isset($_POST["icon"])&&$_POST["icon"]!=""){
				$img=$this->img->imgCheck("icon");
				if($img["result"]==false&&$se["result"]){
					$se["result"]=false;
					$se["message_error"]=$img["message_error"];
				}
				if($img["result"]){
					$a=explode("/",$img["mime"]);
					$mime=$a[1];
					$img["set"]=1;
				}
			}
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				$key=$this->key("key",7);
				 $qe=$this->regisMemberInsert($key,$img,$mime);
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					if($img["result"]){
						$this->img->imgSave($img,$key,$this->max_squar);
					}
					$iframe=(isset($_GET["iframe"])&&$_GET["iframe"]=="1")?1:0;
					$dialog_id=(isset($_GET["dialog_id"]))?$_GET["dialog_id"]:"";
					if($iframe==1){
						header('Location:?a='.$this->a.'&b=iframeregissuccess&dialog_id='.$dialog_id);
					}else{
						header('Location:?a='.$this->a.'&ed='.$key);
					}
				}
			}
			if($error!=""){
				$this->regisMemberPage($error);
			}
		}else{
			$this->regisMemberPage($error);
		}
	}
	private function iframeRegisSuccess(){
		$dialog_id=(isset($_GET["dialog_id"]))?$_GET["dialog_id"]:"";
		$this->home=1;
		$this->pageHead([]);
		$ar_tx='\\\''.$dialog_id.'\\\',0';
		echo '<div class="c"><img src="img/pos/64x64_member.png"><br /><b>ลงทะเบียน สมาชิกสำเร็จ</b><br /><button onclick="window.parent.tran2(\'M\',\'dialogClose\',\''.$ar_tx.'\')">ปิดหน้าต่างนี้</button></div>';
	}
	private function regisMemberInsert(string $keyi,array $img,string $mime=""):array{
		//print_r($this->dn_value);exit;
		$name=$this->getStringSqlSet($_POST["name"]);
		$lastname=$this->getStringSqlSet($_POST["lastname"]);
		$icon=(isset($_POST["icon"]))?$_POST["icon"]:"";
		$idc=$this->getStringSqlSet($_POST["idc"]);
		$tel=$this->getStringSqlSet($_POST["tel"]);
		$mb_type = $this->getStringSqlSet($_POST["mb_type"]);
		$sex=(isset($_POST["sex"])&&isset($this->sex[$_POST["sex"]]))?$this->getStringSqlSet($_POST["sex"]):"NULL";
		$birthday=$_POST["birthday"];
		$birthday=$this->setDateR($birthday,"00:00:00");
		$birthday=($birthday=="")?"NULL":$this->getStringSqlSet($birthday);
		$no=$this->getStringSqlSet($_POST["no"]);
		$alley=$this->getStringSqlSet($_POST["alley"]);
		$road=$this->getStringSqlSet($_POST["road"]);
		$distric=$this->getStringSqlSet($_POST["distric"]);
		$country=$this->getStringSqlSet($_POST["country"]);
		$province=$this->getStringSqlSet($_POST["province"]);
		$post_no=$this->getStringSqlSet($_POST["post_no"]);
		$disc=$this->getStringSqlSet($_POST["disc"]);
		$key=$keyi;
		
		$mimefull="NULL";
		$md5="NULL";
		$user="NULL";
		$size=0;
		$width=0;
		$height=0;
		if($img["set"]==1){
			$mimefull=$this->getStringSqlSet($img["mime"]);
			$md5=$this->getStringSqlSet(md5($img["file"]));
			$user=$this->getStringSqlSet($_SESSION["sku_root"]);
			$size=(int) $img["size"];
			$width=(int) $img["width"];
			$height=(int) $img["height"];
		}
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@sku_key:='".$key."',
			@img_set:=".$img["set"].",
			@icon:=".($icon!=""?"'".$key."".($mime!=""?".".$mime:"")."'":"NULL").",
			@lastid:=(SELECT AUTO_INCREMENT
								FROM information_schema.tables
								WHERE table_name = 'member'),
			@count_name:=(SELECT COUNT(`id`)  FROM `member` WHERE `name`=".$name."),
			@count_lastname:=(SELECT COUNT(`id`)  FROM `member` WHERE `lastname`=".$lastname."),
			@count_tel:=(SELECT COUNT(`id`)   FROM `member` WHERE `tel`=".$tel."),
			@count_idc:=(SELECT COUNT(`id`)   FROM `member` WHERE `idc`=".$idc.");
		";
		$sql["check"]="
			IF @count_name > 0 AND @count_lastname > 0 THEN 
				SET @message_error='เกิดขอผิดพลาด ชื่อ และนามสกุลที่ส่งมา มีแล้ว โปรดลองชื่ออื่น';
			ELSEIF @count_sku > 0 THEN
				SET @message_error='เกิดขอผิดพลาด รหัสภายในที่ส่งมา มีแล้ว โปรดลอง รหัสภายในอื่น';
			END IF;			
		";
		$sql["run"]="
			IF LENGTH(@message_error) = 0 THEN
				INSERT INTO `member`  (
					`id`				,`sku`				,`sku_key`		,`sku_root`		,`name`				,`lastname`,
					`mb_type`	,`birthday`		,`sex`				,`icon`				,`no`				,`alley`					,`road`,
					`distric`		,`country`			,`province`		,`post_no`			,`tel`,
					`idc`				,`disc`
				) VALUES (
					`id`				,LPAD(@lastid,6,'0')	,@sku_key			,@sku_key			,".$name."				,".$lastname.",
					".$mb_type."	,".$birthday."			,".$sex."					,@icon				,".$no."					,".$alley."				,".$road.",
					".$distric."		,".$country."		,".$province."		,".$post_no."			,".$tel.",
					".$idc."			,".$disc."
				);
				IF @img_set=1 THEN
					INSERT  INTO `gallery` (
						`sku_key`		,`name`		,`a_type`		,`mime_type`		,`md5`,
						`user`			,`size`			,`width`		,`height`
					) VALUES (
						@sku_key		,".$name."		,'partner'		,".$mimefull."			,".$md5.",
						".$user."		,".$size."		,".$width."		,".$height."
					);
				END IF;
				SET @result=1;
			END IF;
		";
		$sql["ref"]=$this->ref("member","sku_key",$key);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($sql);exit;
		return $se;
	}
	protected function regisMemberPage(string $error):void{
		$iframe=(isset($_GET["iframe"])&&$_GET["iframe"]=="1")?1:0;
		$dialog_id=(isset($_GET["dialog_id"]))?$_GET["dialog_id"]:"";
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$icon=(isset($_POST["icon"]))?$_POST["icon"]:"";
		$lastname=(isset($_POST["lastname"]))?htmlspecialchars($_POST["lastname"]):"";
		$idc=(isset($_POST["idc"]))?htmlspecialchars($_POST["idc"]):"";
		$sex=(isset($_POST["sex"])&&isset($this->sex[$_POST["sex"]]))?$_POST["sex"]:"n";
		$birthday=(isset($_POST["birthday"]))?htmlspecialchars($_POST["birthday"]):"";
		$tel=(isset($_POST["tel"]))?htmlspecialchars($_POST["tel"]):"";
		$mb_type = (isset($_POST["mb_type"]))?htmlspecialchars($_POST["mb_type"]):"";
		$mb_type=(!isset($this->mb_type[$mb_type]))?"p":$mb_type;
		$no=(isset($_POST["no"]))?htmlspecialchars($_POST["no"]):"";
		$alley=(isset($_POST["alley"]))?htmlspecialchars($_POST["alley"]):"";
		$road=(isset($_POST["road"]))?htmlspecialchars($_POST["road"]):"";
		$distric=(isset($_POST["distric"]))?htmlspecialchars($_POST["distric"]):"";
		$country=(isset($_POST["country"]))?htmlspecialchars($_POST["country"]):"";
		$province=(isset($_POST["province"]))?htmlspecialchars($_POST["province"]):"";
		$post_no=(isset($_POST["post_no"]))?htmlspecialchars($_POST["post_no"]):"";
		$disc=(isset($_POST["disc"]))?htmlspecialchars($_POST["disc"]):"";

		$text_qr_iframe="";
		if($iframe==1){
			$this->home=1;
			$text_qr_iframe="&iframe=1&dialog_id=$dialog_id";
		}	
		$this->addDir("","เพิ่ม".$this->title);
		$this->pageHead(["title"=>"เพิ่ม".$this->title." DIYPOS","js"=>["member","Mb","fileupload","Ful"],"run"=>["Mb"],"css"=>["member","fileupload"]]);
		
		echo '<div class="content">
			<div class="form">
				<h1 class="c">เพิ่ม'.$this->title.' </h1>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}		
		echo '		<form name ="add_member" method="post" action="?a=member&b=regis'.$text_qr_iframe.'">
			<input type="hidden" name="submit" value="clicksubmit" />
			<input type="hidden" id="icon_id" name="icon" value="'.$icon.'" />
			<p><label for="member_name">ชื่อ</label></p>
			<div><input id="member_name" class="want" type="text" name="name" value="'.$name.'" autocomplete="off" /></div>
			<p><label for="member_lastame">นามสกุล</label></p>
			<div><input id="member_lastame" type="text" name="lastname" value="'.$lastname.'" autocomplete="off" /></div>
			<p><label for="sex">เพศ</label></p>
			<div>
				<select name="sex">
					<option value=""'.($sex == ""?" selected":"").'>'.$this->sex["n"].'</option>
					<option value="m"'.($sex == "m"?" selected":"").'>'.$this->sex["m"].'</option>					
					<option value="f"'.($sex == "f"?" selected":"").'>'.$this->sex["f"].'</option>
				</select>
			</div>
			<p><label for="member_birthday">วัน-เดือน-ปี เกิด</label></p>
			<div><input id="member_birthday" type="date" name="birthday" value="'.$birthday.'" /></div>
			<p><label for="mb_type">ประเภทสมาชิก</label></p>
			<div>
				<select name="mb_type">
					<option value="p"'.($mb_type == "p"?" selected":"").'>'.$this->mb_type["p"].'</option>					
					<option value="s"'.($mb_type == "s"?" selected":"").'>'.$this->mb_type["s"].'</option>
				</select>
			</div>
			<p><label for="tel">โทรศัพท์</label></p>
			<div><input id="tel" type="text" value="'.$tel.'"  name="tel" autocomplete="off"  /></div>
			<p><label for="idc">เลขที่บัตรประชาชน</label></p>
			<div><input id="idc" type="text" value="'.$idc.'"  name="idc" autocomplete="off"  /></div>
			<p><label for="no">ที่อยู่ เลขที่</label></p>
			<div><input id="no" type="text" value="'.$no.'"  name="no" autocomplete="off"  /></div>
			<p><label for="alley">ซอย</label></p>
			<div><input id="alley" type="text" value="'.$alley.'"  name="alley" autocomplete="off"  /></div>
			<p><label for="road">ถนน</label></p>
			<div><input id="road" type="text" value="'.$road.'"  name="road" autocomplete="off"  /></div>
			<p><label for="distric">แขวง/ตำบล</label></p>
			<div><input id="distric" type="text" value="'.$distric.'"  name="distric" autocomplete="off"  /></div>
			<p><label for="country">เขต/อำเภอ</label></p>
			<div><input id="country" type="text" value="'.$country.'"  name="country" autocomplete="off"  /></div>
			<p><label for="province">จังหวัด</label></p>
			<div><input id="province" type="text" value="'.$province.'"  name="province" autocomplete="off"  /></div>
			<p><label for="post_no">รหัสไปรษณี</label></p>
			<div><input id="post_no" type="text" value="'.$post_no.'"  name="post_no" autocomplete="off"  /></div>
			<p><label for="disc">รายละเอียดย่อ</label></p>
			<div><textarea   name="disc">'.$disc.'</textarea></div>
			<div>
				<div id="div_fileuploadpre" class="fileuploadpre1"></div>
				<input id="upload_pic" type="file" accept="image/png,image/gif,image/jpeg,image/webp" class="fuif" name="picture" onchange="Ful.fileUploadShow(event,1,\'icon_id\',1024,160)" />
				<label for="upload_pic"  class="fubs">+เลือกรูปภาพ</label>
			</div>	
			<script type="text/javascript">Ful.fileUploadShow(null,1,\'icon_id\',480,160,\'load\',\'div_fileuploadpre\')</script>
		';			
		echo '';
		
		if($iframe==1){
			echo '<div class="member_submit_iframe"><input type="submit" value="➕ เพิ่ม'.$this->title.'" /></div>';
		}else{
			echo'<br />
					<input type="submit" value="➕ เพิ่ม'.$this->title.'" />';
		}
		echo '		</form>
			</div>
		</div>';
		$this->pageFoot();
	}
	protected function pageMember(){
		
		$this->defaultPageSearch();
		$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["member"],"js"=>["member","Mb"],"run"=>["Mb"]]);
			
			echo '<div class="content">
				<div class="form">
				<h1 class="c">'.$this->title.'</h1>
				<div class="pn_search">
					<form class="form100" name="pd_search" action="" method="get">
						<input type="hidden" name="a" value="member" />
						<input type="hidden" name="lid" value="0" />
						<label><select id="product_search_fl" name="fl">
							<option value="name"'.(($this->fl=="name")?" selected":"").'>ชื่อ</option>
							<option value="lastname"'.(($this->fl=="lastname")?" selected":"").'>นามสกุล</option>
							<option value="sku"'.(($this->fl=="sku")?" selected":"").'>รหัส</option>
							<option value="tel"'.(($this->fl=="tel")?" selected":"").'>เบอร์โทรศัพท์</option>
							<option value="idc"'.(($this->fl=="idc")?" selected":"").'>บัตรประชาชน</option>
						</select>
						 <input id="product_search_tx" type="text" name="tx" value="'.str_replace("\\","",htmlspecialchars($this->txsearch)).'" />
						 <input  type="submit" value="🔍" /> </label></form>
				</div>';
		$this->writeContentMember();		
		echo '<br /><p class="c"><input type="button" value="เพิ่ม'.$this->title.'" onclick="location.href=\'?a='.$this->a.'&amp;b=regis\'" /></p>';
		echo '</div></div>';
		$this->pageFoot();
	}
	private function defaultSearch():string{
		$fla=["sku","lastname","name","idc","tel"];
		$fl="name";
		$tx="";
		$se="";
		if(isset($_GET["fl"])){
			if(in_array($_GET["fl"],$fla)){
				if(($_GET["fl"]=="sku")
				&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["tx"])){
					$fl=$_GET["fl"];
				}	
			}
		}
		$this->fl=$fl;
		if(isset($_GET["tx"])&&strlen(trim($_GET["tx"]))>0){
			$tx=$this->getStringSqlSet($_GET["tx"]);
		}
		if($tx!=""){
			$tx=substr($tx,1,-1);
			$this->txsearch=$tx;
			$se=" WHERE `member`.`".$fl."` LIKE  \"%".$tx."%\"  ";
		}
		return $se;
	}
	public function defaultPageSearch():void{
		$fla=["sku","lastname","name","idc","tel"];
		$fl="name";
		$tx="";
		$se="";
		if(isset($_GET["fl"])){
			if(in_array($_GET["fl"],$fla)){
				$fl=$_GET["fl"];
				if($_GET["fl"]=="sku"){
					if(preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["tx"])){
						$fl=$_GET["fl"];
					}else{
						$_GET["tx"]="=*/?";
					}
				}
			}
		}
		$this->fl=$fl;
		if(isset($_GET["tx"])&&strlen(trim($_GET["tx"]))>0){
			$tx=$this->getStringSqlSet($_GET["tx"]);
		}
		if(isset($_GET["lid"])&&preg_match("/^[0-9]{1,12}$/",$_GET["lid"])){
			$this->lid=$_GET["lid"];
		}
		if($tx!=""){
			$tx=substr($tx,1,-1);
			$this->txsearch=$tx;
			$idsearch=">=".$this->lid." ";
			if($this->lid>0){
				$idsearch="<=".$this->lid." ";
			}
			$this->sh=" WHERE `member`.`id`".$idsearch." AND `member`.`".$fl."` LIKE  \"%".$tx."%\""  ;
		}
	}
	protected function pageSearch(int $row):void{
		$href='?a='.$this->a.'&amp;fl='.$this->fl.'&amp;tx='.$this->txsearch.'&amp;page=';
		if($this->page>1){
			echo '<a onclick="history.back()">⬅️ก่อนหน้า</a>';
		}
		echo '<span class="member_page_search">หน้า '.$this->page.'</span>';
		if($row>$this->per){
			echo '<a href="'.$href.''.($this->page+1).'&amp;lid='.$this->lid.'">ถัดไป➡️</a>';
		}
	}
	private function writeContentMember():void{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$dt=$this->getAllMember();
		$se=$dt["get"];
		echo '<form class="form100" name="'.$this->a.'" method="post">
			<input type="hidden" name="sku_root" value="" />';
		echo '	<table class="partnerview" style="width:100%;">
				<tr><th>ที่</th>
				<th>รูป</th>
				<th>รหัส</th>
				<th>ชื่อ</th>
				<th>ประเภท</th>
				<th>กระทำ</th>
				</tr>';
		for($i=0;$i<count($se);$i++){
			$ed='';
			if($se[$i]["sku_root"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}
			$cm=($i%2!=0)?" class=\"i2\"":"";
			$name=htmlspecialchars($se[$i]["name"]);
			$lastname=$se[$i]["lastname"];
			$sku=$se[$i]["sku"];
			if($this->txsearch!=""){
				$ts=htmlspecialchars(str_replace("\\","",$this->txsearch));
				if($this->fl=="name"){
					$name=str_replace($ts,'<span class="bgyl">'.$ts.'</span>',$name);
				}else if($this->fl=="brand_name"){
					$brand_name=str_replace($ts,'<span class="bgyl">'.$ts.'</span>',brand_name);
				}else if($this->fl=="sku"){
					$sku=str_replace($ts,'<span class="bgyl">'.$ts.'</span>',$sku);
				}
				
			}
			$sn=strlen(trim($se[$i]["sku"]))>0?substr(trim($se[$i]["sku"]),0,15):(mb_substr(trim($se[$i]["name"]),0,15));
			echo '<tr'.$cm.'><td class="r">'.($se[$i]["id"]).'</td>
				<td class="l"><div class="img48"><img  class="viewimage" src="img/gallery/64x64_'.$se[$i]["icon"].'"   onerror="this.src=\'img/pos/64x64_null.png\'" alt="'.$sn.'" onclick="G.view(this)"  title="เปิดดูภาพ" /></div></td>
				<td class="l">'.$sku.'</td>
				<td class="l"><a href="?a='.$this->a.'&amp;b=details&amp;sku_root='.$se[$i]["sku_root"].'">'.$name.' '.$lastname.'</a></td>
				<td class="l">'.$this->mb_type[$se[$i]["mb_type"]].'</td>
				<td class="action">
					<a onclick="Mb.edit(\''.$se[$i]["sku_root"].'\')" title="แก้ไข">📝</a>
					<a onclick="Mb.delete(\''.$se[$i]["sku_root"].'\',\''.htmlspecialchars($se[$i]["name"]." ".$se[$i]["lastname"]).'\')" title="ทิ้ง">🗑</a>
					'.$ed.'
					</td>
				</tr>';
				$this->lid=$se[$i]["id"];
		}
		echo '</table></form>';
		//print_r($dt);
		$count=(isset($dt["count"][0]["count"]))?$dt["count"][0]["count"]:0;
		if($this->txsearch==""){
			$this->page($count,$this->per,$this->page,"?a=member&amp;page=");
		}else{
			$this->pageSearch(count($se));
		}
	}
	public function getAllMember():array{
		$sh=$this->defaultSearch();
		$re=[];
		$sql=[];
		$limit_page=(($this->page-1)*$this->per).",".($this->per+1);
		if($this->txsearch!=""){
				$limit_page=$this->per+1;
		}
		$sql["count"]="SELECT COUNT(*) AS `count`  FROM `member`";
		$sql["get"]="SELECT `id`,`name`,IFNULL(`lastname`,'') AS `lastname`,IFNULL(`icon`,'null.png') AS `icon`,`sku`,`sku_root` ,
				`mb_type`
			FROM `member` 
			".$this->sh." 
			ORDER BY `id` DESC LIMIT ".$limit_page."";
		$se=$this->metMnSql($sql,["count","get"]);
		if($se["result"]){
			$re=$se["data"];//["get"];
		}
		return $re;
	}
}
