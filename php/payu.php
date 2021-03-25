<?php
class payu extends main{
	public function __construct(){
		parent::__construct();
		$this->img=null;
		$this->title = "รูปแบบการชำระ";
		$this->a = "payu";
		$this->per=3;
		$this->page=1;
		$this->txsearch="";
		$this->fl="";
		$this->lid=0;
		$this->sh="";
	}
	public function run(){
		$file = "php/class/image.php";
		require($file);		
		$this->img=new image($this->gallery_dir);		
		$this->page=$this->setPageR();
		$q=["regis","edit","delete","details"];
		$this->addDir("?a=".$this->a,$this->title);
		if(isset($_GET["b"])&&in_array($_GET["b"],$q)){
			$t=$_GET["b"];
			if($t=="regis"){
				$this->regisPayu();
			}else if($t=="edit"){
				$this->editPayu();
			}else if($t=="delete"){
				$this->deletePayu();
			}else if($t=="details"){
				if(isset($_GET["sku_root"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["sku_root"])){
					require("php/payu_details.php");
					(new payu_details($_GET["sku_root"]))->run();
				}else{
					$this->pagePayu();
				}
			}
		}else{
			$this->pagePayu();
		}
	}
	private function deletePayu():void{
		if(isset($_POST["sku_root"])){
			$sku_root=$this->getStringSqlSet($_POST["sku_root"]);
			$sql=[];
			$sql["set"]="SELECT @sku_root:=".$sku_root.";";
			$sql["del"]="DELETE FROM `payu` WHERE `sku_root`=".$sku_root." AND `sku_root` != 'defaultroot'";
			//print_r($sql);
			$re = $this->metMnSql($sql,[]);
			//print_r($re);
			header('Location:?a='.$this->a.'&ed='.$_POST["sku_root"]);
		}
	}
	private function editPayuPage(string $error):void{
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$icon_load=(isset($_POST["icon_load"]))?$_POST["icon_load"]:"";
		$icon=(isset($_POST["icon"]))?$_POST["icon"]:"";
		$sku=(isset($_POST["sku"]))?htmlspecialchars($_POST["sku"]):"";
		$money_type = (isset($_POST["money_type"]))?htmlspecialchars($_POST["money_type"]):"ca";
		$note=(isset($_POST["note"]))?htmlspecialchars($_POST["note"]):"";
		$sku_root=(isset($_POST["sku_root"]))?htmlspecialchars($_POST["sku_root"]):"";
		$this->addDir("","แก้ไข".$this->title.' '.$name );
		$this->pageHead(["title"=>"แก้ไข".$this->title." DIYPOS","js"=>["partner","Pn","fileupload","Ful"],"run"=>["Pn"],"css"=>["partner"]]);
		echo '<div class="content">
			<div class="form">
				<h1 class="c">แก้ไข'.$this->title.'</h1>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}		
		echo '		<form  method="post">
			<input type="hidden" name="submit" value="clicksubmit" />
			<input type="hidden" id="icon_load_id" name="icon_load" value="'.$icon_load.'" />
			<input type="hidden" id="icon_id" name="icon" value="" />
			<input type="hidden" name="sku_root" value="'.$sku_root.'" />
			<p><label for="partner_name">ชื่อ</label></p>
			<div><input id="partner_name" class="want" type="text" name="name" value="'.$name.'" autocomplete="off" /></div>
			<p><label for="partner_sku">รหัสภายใน</label></p>
			<div><input id="partner_sku" type="text" value="'.$sku.'"  name="sku" autocomplete="off"  /></div>
			<p><label for="tp_type">ประเภทเงิน</label></p>
			<div>
				<select name="money_type">	
					<option value="ca"'.($money_type == "ca"?" selected":"").'>'.$this->money_type["ca"]["icon"].' '.$this->money_type["ca"]["name"].'</option>				
					<option value="tr"'.($money_type == "tr"?" selected":"").'>'.$this->money_type["tr"]["icon"].' '.$this->money_type["tr"]["name"].'</option>
					<option value="ck"'.($money_type == "ck"?"selected":"").'>'.$this->money_type["ck"]["icon"].' '.$this->money_type["ck"]["name"].'</option>
					<option value="cd"'.($money_type == "cd"?"selected":"").'>'.$this->money_type["cd"]["icon"].' '.$this->money_type["cd"]["name"].'</option>
				</select>
			</div>
			<p><label for="note">รายละเอียดย่อ</label></p>
			<div><input id="note" type="text" value="'.$note.'"  name="note" autocomplete="off"  /></div>
			<div>
				<div id="div_fileuploadpre" class="fileuploadpre1"></div>
				<input id="upload_pic" type="file" accept="image/png,image/gif,image/jpeg,image/webp" class="fuif" name="picture" onchange="Ful.fileUploadShow(event,1,\'icon_id\',1024,160)" />
				<label for="upload_pic" class="fubs">+เลือกรูปภาพ</label>
			</div>	
			<script type="text/javascript">Ful.fileUploadShow(null,1,\'icon_id\',480,160,\'load\',\'div_fileuploadpre\',\'icon_load_id\')</script>
		';			
		echo '</table>
					<br />
					<input type="submit" value="แก้ไข'.$this->title.'" />
				</form>
			</div>
		</div>';
		$this->pageFoot();
	}
	private function editPayu():void{
		$error="";
		$img=["result"=>false,"set"=>0];
		$mime="";
		$set_img=0;
		if(isset($_POST["submit"])&&$_POST["submit"]=="clicksubmit"){
			$se=$this->checkSet("partner",["post"=>["name","sku","money_type","note"]],"post");
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
				 $qe=$this->editPayuUpdate($key,$img,$mime);
				 //print_r($qe);
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					 if($img["result"]){
						$this->img->imgSave($img,$key);
					}
					header('Location:?a='.$this->a.'&ed='.$_POST["sku_root"]);
				}
			}
			if($error!=""){
				$this->editPayuPage($error);
			}
		}else{
			$sku_root=(isset($_POST["sku_root"]))?$_POST["sku_root"]:"";
			$this->editPayuSetCurent($sku_root);
			$this->editPayuPage($error);
		}
	}
	private function editPayuUpdate(string $key,array $img,string $mime):array{
		//print_r($img);
		$name=$this->getStringSqlSet($_POST["name"]);
		$icon=(isset($_POST["icon"]))?$_POST["icon"]:"";
		$icon_load=(isset($_POST["icon_load"]))?$_POST["icon_load"]:"";
		$money_type = (isset($_POST["money_type"]))?$this->getStringSqlSet($_POST["money_type"]):"\"ca\"";
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$note=$this->getStringSqlSet($_POST["note"]);
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
			@count_name:=(SELECT COUNT(`id`)  FROM `payu` WHERE `name`=".$name." AND `sku_root` !=".$sku_root."),
			@count_sku:=(SELECT COUNT(`id`)   FROM `payu` WHERE `sku`=".$sku." AND `sku_root` !=".$sku_root.");
		";
		$sql["check"]="
			IF @count_name > 0 THEN 
				SET @message_error='เกิดขอผิดพลาด ชื่อที่แก้ไขมา มีแล้ว โปรดลองชื่ออื่น';
			ELSEIF @count_sku > 0 THEN
				SET @message_error='เกิดขอผิดพลาด รหัสภายในทแก้ไขมา มีแล้ว โปรดลอง รหัสภายในอื่น';
			END IF;			
		";
		$icon_tx="";
		if($icon!=""){
			$icon_tx=",`icon`='".$key.".".$mime."'";
		}else if($icon_load==""){
			$icon_tx=",`icon`=NULL";
		}
		$sql["run"]="
			IF LENGTH(@message_error) = 0 THEN
				UPDATE `payu` SET  
					`sku`	=	".$sku."				,`sku_key`=".$sku_key."	,`name`= ".$name." ,
					`money_type`=".$money_type."		".$icon_tx."	,`note`= ".$note."	
				WHERE `sku_root`=".$sku_root.";
				IF @img_set=1 THEN
					INSERT  INTO `gallery` (
						`sku_key`		,`name`		,`a_type`		,`mime_type`		,`md5`,
						`user`			,`size`			,`width`		,`height`
					) VALUES (
						".$sku_key."	,".$name."		,'payu'		,".$mimefull."				,".$md5.",
						".$user."		,".$size."		,".$width."		,".$height."
					);
				END IF;
				SET @result=1;
			END IF;
		";
		$sql["ref"]=$this->ref("payu","sku_key",$skuk);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($sql);
		return $se;
	}
	private function editPayuOldData(string $sku_root):array{
		$sku_root=$this->getStringSqlSet($sku_root);
		$sql=[];
		$sql["result"]="SELECT 
				`name`		,`sku`		,`sku_root`	,`money_type`		,IFNULL(`icon`,'') AS `icon`,`note`
			FROM `payu` 
			WHERE `sku_root`=".$sku_root."";
		$re=$this->metMnSql($sql,["result"]);
		if(isset($re["data"]["result"][0])){
			if($re["data"]["result"][0]["icon"]!=""){
				$re["data"]["result"][0]["icon_load"]=$this->img2Base64($this->gallery_dir."/".$re["data"]["result"][0]["icon"]);
				unset($re["data"]["result"][0]["icon"]);
			}
			return $re["data"]["result"][0];
		}
		
		return [];
	}
	private function editPayuSetCurent(string $sku_root):void{
		$od=$this->editPayuOldData($sku_root);
		$fl=["sku","name","icon","money_type","note","icon_load"];
		foreach($fl as $v){
			if(isset($od[$v])){
				$_POST[$v]=$od[$v];
			}
		}
	}
	protected function regisPayu():void{
		$error="";
		$img=["result"=>false,"set"=>0];
		$mime="";
		if(isset($_POST["submit"])&&$_POST["submit"]=="clicksubmit"){
			$se=$this->checkSet("payu",["post"=>["name","sku","note","money_type"]],"post");
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
				//$this->img->imgSave($se,"123456789gdgdfd");
			}
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				$key=$this->key("key",7);
				 $qe=$this->regisPayuInsert($key,$img,$mime);
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					if($img["result"]){
						$this->img->imgSave($img,$key);
					}
					header('Location:?a='.$this->a.'&ed='.$key);
				}
			}
			if($error!=""){
				$this->regisPayuPage($error);
			}
		}else{
			$this->regisPayuPage($error);
		}
	}
	private function regisPayuInsert(string $keyi,array $img,string $mime=""):array{
		//print_r($this->dn_value);exit;
		$name=$this->getStringSqlSet($_POST["name"]);
		$icon=(isset($_POST["icon"]))?$_POST["icon"]:"";
		$money_type = (isset($_POST["money_type"]))?$this->getStringSqlSet($_POST["money_type"]):"\"ca\"";
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$note=$this->getStringSqlSet($_POST["note"]);
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
			@count_name:=(SELECT COUNT(`id`)  FROM `payu` WHERE `name`=".$name."),
			@count_sku:=(SELECT COUNT(`id`)   FROM `payu` WHERE `sku`=".$sku.");
		";
		$sql["check"]="
			IF @count_name > 0 THEN 
				SET @message_error='เกิดขอผิดพลาด ชื่อที่ส่งมา มีแล้ว โปรดลองชื่ออื่น';
			ELSEIF @count_sku > 0 THEN
				SET @message_error='เกิดขอผิดพลาด รหัสภายในที่ส่งมา มีแล้ว โปรดลอง รหัสภายในอื่น';
			END IF;			
		";
		$sql["run"]="
			IF LENGTH(@message_error) = 0 THEN
				INSERT INTO `payu`  (
					`sku`			,`sku_key`		,`sku_root`		,`name`,
					`icon`			,`money_type`	,`note`
				) VALUES (
					".$sku."			,@sku_key			,@sku_key			,".$name.",
					@icon			,".$money_type."			,".$note."
				);
				IF @img_set=1 THEN
					INSERT  INTO `gallery` (
						`sku_key`		,`name`		,`a_type`		,`mime_type`		,`md5`,
						`user`			,`size`			,`width`		,`height`
					) VALUES (
						@sku_key		,".$name."		,'payu'		,".$mimefull."			,".$md5.",
						".$user."		,".$size."		,".$width."		,".$height."
					);
				END IF;
				SET @result=1;
			END IF;
		";
		$sql["ref"]=$this->ref("partner","sku_key",$key);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($se);exit;
		return $se;
	}
	protected function regisPayuPage(string $error):void{
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$sku=(isset($_POST["sku"]))?htmlspecialchars($_POST["sku"]):"";
		$icon=(isset($_POST["icon"]))?$_POST["icon"]:"";
		$money_type = (isset($_POST["money_type"]))?htmlspecialchars($_POST["money_type"]):"ca";
		$note=(isset($_POST["note"]))?htmlspecialchars($_POST["note"]):"";
		$this->addDir("","เพิ่มคู่ค้า");
		$this->pageHead(["title"=>"เพิ่ม".$this->title." DIYPOS","js"=>["payu","Py","fileupload","Ful"],"run"=>["Py"],"css"=>["payu"]]);
		echo '<div class="content">
			<div class="form">
				<h1 class="c">เพิ่ม'.$this->title.'</h1>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}		
		echo '		<form name ="add_partner" method="post">
			<input type="hidden" name="submit" value="clicksubmit" />
			<input type="hidden" id="icon_id" name="icon" value="'.$icon.'" />
			<p><label for="partner_name">ชื่อ</label></p>
			<div><input id="partner_name" class="want" type="text" name="name" value="'.$name.'" autocomplete="off" /></div>
			<p><label for="partner_sku">รหัสภายใน</label></p>
			<div><input id="partner_sku" type="text" value="'.$sku.'"  name="sku" autocomplete="off"  /></div>
			<p><label for="tp_type">ประเภทเงิน</label></p>
			<div>
				<select name="money_type">	
					<option value="ca"'.($money_type == "ca"?" selected":"").'>'.$this->money_type["ca"]["icon"].' '.$this->money_type["ca"]["name"].'</option>				
					<option value="tr"'.($money_type == "tr"?" selected":"").'>'.$this->money_type["tr"]["icon"].' '.$this->money_type["tr"]["name"].'</option>
					<option value="ck"'.($money_type == "ck"?"selected":"").'>'.$this->money_type["ck"]["icon"].' '.$this->money_type["ck"]["name"].'</option>
					<option value="cd"'.($money_type == "cd"?"selected":"").'>'.$this->money_type["cd"]["icon"].' '.$this->money_type["cd"]["name"].'</option>
				</select>
			</div>
			<p><label for="note">รายละเอียดย่อ</label></p>
			<div><input id="note" type="text" value="'.$note.'"  name="note" autocomplete="off"  /></div>
			<div>
				<div id="div_fileuploadpre" class="fileuploadpre1"></div>
				<input id="upload_pic" type="file" accept="image/png,image/gif,image/jpeg,image/webp" class="fuif" name="picture" onchange="Ful.fileUploadShow(event,1,\'icon_id\',1024,160)" />
				<label for="upload_pic"  class="fubs">+เลือกรูปภาพ</label>
			</div>	
			<script type="text/javascript">Ful.fileUploadShow(null,1,\'icon_id\',480,160,\'load\',\'div_fileuploadpre\')</script>
		';			
		echo '</table>
					<br />
					<input type="submit" value="เพิ่ม'.$this->title.'" />
				</form>
			</div>
		</div>';
		$this->pageFoot();
	}
	protected function pagePayu(){
		$this->defaultPageSearch();
		$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["payu"],"js"=>["payu","Py"],"run"=>["Py"]]);
			echo '<div class="content">
				<div class="form">
				<h1 class="c">'.$this->title.'</h1>
				<div class="pn_search">
					<form class="form100" name="py_search" action="" method="get">
						<input type="hidden" name="a" value="payu" />
						<input type="hidden" name="lid" value="0" />
						<label><select id="product_search_fl" name="fl">
							<option value="name"'.(($this->fl=="name")?" selected":"").'>ชื่อ</option>
							<option value="sku"'.(($this->fl=="sku")?" selected":"").'>รหัสภายใน</option>
						</select>
						 <input id="product_search_tx" type="text" name="tx" value="'.str_replace("\\","",htmlspecialchars($this->txsearch)).'" />
						 <input  type="submit" value="🔍" /> </label></form>
				</div>';
		$this->writeContentPayu();		
		echo '<br /><p class="c"><input type="button" value="เพิ่ม'.$this->title.'" onclick="location.href=\'?a='.$this->a.'&b=regis\'" /></p>';
		echo '</div></div>';
		$this->pageFoot();
	}
	private function defaultSearch():string{
		$fla=["sku","name"];
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
			$se=" WHERE `payu`.`".$fl."` LIKE  \"%".$tx."%\"  ";
		}
		return $se;
	}
	public function defaultPageSearch():void{
		$fla=["sku","name"];
		$fl="name";
		$tx="";
		$se="";
		if(isset($_GET["fl"])){
			if(in_array($_GET["fl"],$fla)){
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
			$this->sh=" WHERE `payu`.`id`".$idsearch." AND `payu`.`".$fl."` LIKE  \"%".$tx."%\""  ;
		}
	}
	protected function pageSearch(int $row):void{
		$href='?a=partner&amp;fl='.$this->fl.'&amp;tx='.$this->txsearch.'&amp;page=';
		if($this->page>1){
			echo '<a onclick="history.back()">⬅️ก่อนหน้า</a>';
		}
		echo '<span class="partner_page_search">หน้า '.$this->page.'</span>';
		if($row>$this->per){
			echo '<a href="'.$href.''.($this->page+1).'&amp;lid='.$this->lid.'">ถัดไป➡️</a>';
		}
	}
	private function writeContentPayu():void{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$dt=$this->getAllPayu();
		$se=$dt["get"];
		echo '<form class="form100" name="'.$this->a.'" method="post">
			<input type="hidden" name="sku_root" value="" />';
		echo '	<table class="table_view_all_payu">
				<tr><th>ที่</th>
				<th>รูป</th>
				<th>รหัสภายใน</th>
				<th>ชื่อ</th>
				<th>ประเภทเงิน</th>
				<th>กระทำ</th>
				</tr>';
		for($i=0;$i<count($se);$i++){
			$ed='';
			if($se[$i]["sku_root"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}
			$cm=($i%2!=0)?" class=\"i2\"":"";
			$name=htmlspecialchars($se[$i]["name"]);
			$sku=$se[$i]["sku"];
			if($this->txsearch!=""){
				$ts=htmlspecialchars(str_replace("\\","",$this->txsearch));
				if($this->fl=="name"){
					$name=str_replace($ts,'<span class="bgyl">'.$ts.'</span>',$name);
				}else if($this->fl=="sku"){
					$sku=str_replace($ts,'<span class="bgyl">'.$ts.'</span>',$sku);
				}
				
			}
			$sn=strlen(trim($se[$i]["sku"]))>0?substr(trim($se[$i]["sku"]),0,15):(mb_substr(trim($se[$i]["name"]),0,15));
			$mg=$se[$i]["money_type"];
			$mn=($mg !="" && isset($this->money_type[$mg]))?$this->money_type[$mg]["name"]:"";
			echo '<tr'.$cm.'><td class="r">'.($se[$i]["id"]).'</td>
				<td class="l"><div class="img48"><img src="img/gallery/64x64_'.$se[$i]["icon"].'"  alt="'.$sn.'" /></div></td>
				<td class="l">'.$sku.'</td>
				<td class="l"><a href="?a='.$this->a.'&amp;b=details&amp;sku_root='.$se[$i]["sku_root"].'">'.$name.'</a></td>
				<td class="l">'.$mn.'</td>
				<td class="action">
					<a onclick="Py.edit(\''.$se[$i]["sku_root"].'\')" title="แก้ไข">📝</a>
					<a onclick="Py.delete(\''.$se[$i]["sku_root"].'\',\''.htmlspecialchars($se[$i]["name"]).'\')" title="ทิ้ง">🗑</a>
					'.$ed.'
					</td>
				</tr>';
				$this->lid=$se[$i]["id"];
		}
		echo '</table></form>';
		//print_r($dt);
		$count=(isset($dt["count"][0]["count"]))?$dt["count"][0]["count"]:0;
		if($this->txsearch==""){
			$this->page($count,$this->per,$this->page,"?a=partner&amp;page=");
		}else{
			$this->pageSearch(count($se));
		}
	}
	public function getAllPayu():array{
		$sh=$this->defaultSearch();
		$re=[];
		$sql=[];
		$limit_page=(($this->page-1)*$this->per).",".($this->per+1);
		if($this->txsearch!=""){
				$limit_page=$this->per+1;
		}
		$sql["count"]="SELECT COUNT(*) AS `count`  FROM `payu`";
		$sql["get"]="SELECT `id`,`name`,`icon`,`sku`,`sku_root`,IFNULL(`money_type`,'ddd') AS `money_type`
			FROM `payu` 
			".$this->sh." 
			ORDER BY `id` DESC LIMIT ".$limit_page."";
		$se=$this->metMnSql($sql,["count","get"]);
		if($se["result"]){
			$re=$se["data"];//["get"];
		}
		return $re;
	}
}
