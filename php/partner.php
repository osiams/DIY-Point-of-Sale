<?php
class partner extends main{
	private  $d_cols;
	public function __construct(){
		parent::__construct();
		$this->img=null;
		$this->title = "คู่ค้า";
		$this->a = "partner";
		$this->pn_type = ["s"=>"ผู้ผลิต","n"=>"ตัวแทนจำหน่าย"];
		$this->tp_type = ["0"=>"ต้องไปรับสินค้าเอง","1"=>"ส่งสินค้าถึงร้าน"];
		$this->od_type = ["s"=>"มีคนมาจด","a"=>"โทรศัพท์มาถาม","o"=>"ออกไปซื้อเอง","t"=>"โทรสั่งซื้อ"];
		$this->per=2;
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
				$this->regisPartner();
			}else if($t=="edit"){
				$this->editPartner();
			}else if($t=="delete"){
				$this->deletePartner();
			}else if($t=="details"){
				if(isset($_GET["sku_root"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["sku_root"])){
					require("php/partner_details.php");
					(new partner_details($_GET["sku_root"]))->run();
				}else{
					$this->pagePartner();
				}
			}
		}else{
			$this->pagePartner();
		}
	}
	public function xxxxxxxxxxget(string $cm):array{
		$se = [];
		if($cm == "all_list_key_value"){
			$se = $this->getGroupList();
		}
		return $se;
	}
	protected function xxxxxxxxxxxxxxgetGroupList():array{
		$se = [];
		$sql=[];
		$dn = "";
		for($i=1;$i<=$this->d_cols;$i++){
			$dn.=",`d".$i."`";
		}
		$sql["get"]="SELECT `sku`,`sku_root`,`name`".$dn.",`prop` FROM `group` ORDER BY name ASC";
		$re=$this->metMnSql($sql,["get"]);
		//print_r($re);
		if($re["result"] && $re  ["message_error"] == ""){
			for($i=0;$i<count($re["data"]["get"]);$i++){
				$re["data"]["get"][$i]["prop"] = json_decode($re["data"]["get"][$i]["prop"]);
				$re["data"]["get"][$i]["prop"] = ($re["data"]["get"][$i]["prop"]  === NULL)?[]:$re["data"]["get"][$i]["prop"] ;
				$se[$re["data"]["get"][$i]["sku_root"]] = $re["data"]["get"][$i];
			}
		}
		return $se;
	}
	private function deletePartner():void{
		if(isset($_POST["sku_root"])){
			$sku_root=$this->getStringSqlSet($_POST["sku_root"]);
			$sql=[];
			$sql["set"]="SELECT @sku_root:=".$sku_root.";";
			$sql["del"]="DELETE FROM `partner` WHERE `sku_root`=".$sku_root;
			//print_r($sql);
			$re = $this->metMnSql($sql,[]);
			//print_r($re);
			header('Location:?a='.$this->a.'&ed='.$_POST["sku_root"]);
		}
	}
	private function editPartnerPage(string $error):void{
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$icon_load=(isset($_POST["icon_load"]))?$_POST["icon_load"]:"";
		$icon=(isset($_POST["icon"]))?$_POST["icon"]:"";
		$brand_name=(isset($_POST["brand_name"]))?htmlspecialchars($_POST["brand_name"]):"";
		$sku=(isset($_POST["sku"]))?htmlspecialchars($_POST["sku"]):"";
		$tax=(isset($_POST["tax"]))?htmlspecialchars($_POST["tax"]):"";
		$tel=(isset($_POST["tel"]))?htmlspecialchars($_POST["tel"]):"";
		$fax=(isset($_POST["fax"]))?htmlspecialchars($_POST["fax"]):"";
		$web=(isset($_POST["web"]))?htmlspecialchars($_POST["web"]):"";
		$pn_type = (isset($_POST["pn_type"]))?htmlspecialchars($_POST["pn_type"]):"";
		$tp_type = (isset($_POST["tp_type"]))?htmlspecialchars($_POST["tp_type"]):"";
		$od_type = (isset($_POST["od_type"]))?htmlspecialchars($_POST["od_type"]):"";
		$no=(isset($_POST["no"]))?htmlspecialchars($_POST["no"]):"";
		$alley=(isset($_POST["alley"]))?htmlspecialchars($_POST["alley"]):"";
		$road=(isset($_POST["road"]))?htmlspecialchars($_POST["road"]):"";
		$distric=(isset($_POST["distric"]))?htmlspecialchars($_POST["distric"]):"";
		$country=(isset($_POST["country"]))?htmlspecialchars($_POST["country"]):"";
		$province=(isset($_POST["province"]))?htmlspecialchars($_POST["province"]):"";
		$post_no=(isset($_POST["post_no"]))?htmlspecialchars($_POST["post_no"]):"";
		$note=(isset($_POST["note"]))?htmlspecialchars($_POST["note"]):"";
		$sku_root=(isset($_POST["sku_root"]))?htmlspecialchars($_POST["sku_root"]):"";
		$this->addDir("","แก้ไขคู่ค้า");
		$this->pageHead(["title"=>"แก้ไขคู่ค้า DIYPOS","js"=>["partner","Pn"],"run"=>["Pn"],"css"=>["partner"]]);
		echo '<div class="content">
			<div class="form">
				<h1 class="c">แก้ไขคู่ค้า</h1>';
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
			<p><label for="partner_brand_name">ชื่อการค้า</label></p>
			<div><input id="partner_brand_name" type="text" name="brand_name" value="'.$brand_name.'" autocomplete="off" /></div>
			<p><label for="partner_sku">รหัสภายใน</label></p>
			<div><input id="partner_sku" type="text" value="'.$sku.'"  name="sku" autocomplete="off"  /></div>
			<p><label for="tax">เลขที่ผู้เสียภาษี</label></p>
			<div><input id="tax" type="text" value="'.$tax.'"  name="tax" autocomplete="off"  /></div>
			<p><label for="pn_type">ประเภทคู่ค้า</label></p>
			<div>
				<select name="pn_type">
					<option value=""'.($pn_type == ""?" selected":"").'></option>					
					<option value="s"'.($pn_type == "s"?" selected":"").'>'.$this->pn_type["s"].'</option>
					<option value="n"'.($pn_type == "n"?"selected":"").'>'.$this->pn_type["n"].'</option>
				</select>
			</div>
			<p><label for="od_type">รูปแบบการสั่งซื้อหลัก</label></p>
			<div>
				<select name="od_type">	
					<option value=""'.($od_type == ""?" selected":"").'></option>				
					<option value="s"'.($od_type == "s"?" selected":"").'>'.$this->od_type["s"].'</option>
					<option value="a"'.($od_type == "a"?"selected":"").'>'.$this->od_type["a"].'</option>
					<option value="o"'.($od_type == "o"?" selected":"").'>'.$this->od_type["o"].'</option>
					<option value="t"'.($od_type == "t"?"selected":"").'>'.$this->od_type["t"].'</option>
				</select>
			</div>
			<p><label for="tp_type">การส่งสินค้า</label></p>
			<div>
				<select name="tp_type">	
					<option value=""'.($tp_type == ""?" selected":"").'></option>				
					<option value="1"'.($tp_type == "1"?" selected":"").'>'.$this->tp_type["1"].'</option>
					<option value="0"'.($tp_type == "0"?"selected":"").'>'.$this->tp_type["0"].'</option>
				</select>
			</div>
			<p><label for="tel">โทรศัพท์</label></p>
			<div><input id="tel" type="text" value="'.$tel.'"  name="tel" autocomplete="off"  /></div>
			<p><label for="fax">แฟ็ก</label></p>
			<div><input id="fax" type="text" value="'.$fax.'"  name="fax" autocomplete="off"  /></div>
			<p><label for="web">เว็บไซต์</label></p>
			<div><input id="web" type="text" value="'.$web.'"  name="web" autocomplete="off"  /></div>
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
			<p><label for="note">รายละเอียดย่อ</label></p>
			<div><input id="note" type="text" value="'.$note.'"  name="note" autocomplete="off"  /></div>
			<div>
				<div id="div_fileuploadpre" class="fileuploadpre1"></div>
				<input id="upload_pic" type="file" accept="image/png,image/gif,image/jpeg,image/webp" style="display:none" name="picture" onchange="F.fileUploadShow(event,1,\'icon_id\',1024,160)" />
				<label for="upload_pic" style="border-radius: 2px 2px 2px 2px;text-align: center;line-height:32px;display:block;background-image: linear-gradient(to bottom right, #bbbbbb, #888888);color:#ffffff;;width:100%;height:32px;">+เลือกรูปภาพ</label>
			</div>	
			<script type="text/javascript">F.fileUploadShow(null,1,\'icon_id\',480,160,\'load\',\'div_fileuploadpre\',\'icon_load_id\')</script>
		';			
		echo '</table>
					<br />
					<input type="submit" value="แก้ไขคู่ค้า" />
				</form>
			</div>
		</div>';
		$this->pageFoot();
	}
	private function editPartner():void{
		$error="";
		$img=["result"=>false,"set"=>0];
		$mime="";
		$set_img=0;
		if(isset($_POST["submit"])&&$_POST["submit"]=="clicksubmit"){
			$se=$this->checkSet("partner",["post"=>["name","brand_name","sku","tax","pn_type","od_type","tp_type","tel","fax","web","no","alley","road","distric","country","province","post_no","note"]],"post");
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
				 $qe=$this->editPartnerUpdate($key,$img,$mime);
				 print_r($qe);
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
				$this->editPartnerPage($error);
			}
		}else{
			$sku_root=(isset($_POST["sku_root"]))?$_POST["sku_root"]:"";
			$this->editPartnerSetCurent($sku_root);
			$this->editPartnerPage($error);
		}
	}
	private function editPartnerUpdate(string $key,array $img,string $mime):array{
		//print_r($img);
		$name=$this->getStringSqlSet($_POST["name"]);
		$brand_name=$this->getStringSqlSet($_POST["brand_name"]);
		$icon=(isset($_POST["icon"]))?$_POST["icon"]:"";
		$icon_load=(isset($_POST["icon_load"]))?$_POST["icon_load"]:"";
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$tax=$this->getStringSqlSet($_POST["tax"]);
		$tel=$this->getStringSqlSet($_POST["tel"]);
		$fax=$this->getStringSqlSet($_POST["fax"]);
		$web=$this->getStringSqlSet($_POST["web"]);
		$pn_type = $this->getStringSqlSet($_POST["pn_type"]);
		$tp_type = $this->getStringSqlSet($_POST["tp_type"]);
		$od_type = $this->getStringSqlSet($_POST["od_type"]);
		$no=$this->getStringSqlSet($_POST["no"]);
		$alley=$this->getStringSqlSet($_POST["alley"]);
		$road=$this->getStringSqlSet($_POST["road"]);
		$distric=$this->getStringSqlSet($_POST["distric"]);
		$country=$this->getStringSqlSet($_POST["country"]);
		$province=$this->getStringSqlSet($_POST["province"]);
		$post_no=$this->getStringSqlSet($_POST["post_no"]);
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
			@count_name:=(SELECT COUNT(`id`)  FROM `partner` WHERE `name`=".$name." AND `sku_root` !=".$sku_root."),
			@count_sku:=(SELECT COUNT(`id`)   FROM `partner` WHERE `sku`=".$sku." AND `sku_root` !=".$sku_root.");
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
				UPDATE `partner` SET  
					`sku`	=	".$sku."				,`sku_key`=".$sku_key."	,`name`= ".$name." 			,`brand_name`= ".$brand_name." ,
					`pn_type`=".$pn_type."		".$icon_tx."						,`no`= ".$no."					,`alley`= ".$alley.",	`road`= ".$road.",
					`distric`= ".$distric."			,`country`= ".$country."		,`province`= ".$province."	,`post_no`= ".$post_no.",
					`tel`= ".$tel."					,`fax`= ".$fax."					,`tax`= ".$tax."					,`web`= ".$web.",
					`tp_type`= ".$tp_type."		,`od_type`= ".$od_type."	,`note`= ".$note."	
				WHERE `sku_root`=".$sku_root.";
				IF @img_set=1 THEN
					INSERT  INTO `gallery` (
						`sku_key`		,`name`		,`a_type`		,`mime_type`		,`md5`,
						`user`			,`size`			,`width`		,`height`
					) VALUES (
						".$sku_key."	,".$name."		,'partner'		,".$mimefull."				,".$md5.",
						".$user."		,".$size."		,".$width."		,".$height."
					);
				END IF;
				SET @result=1;
			END IF;
		";
		$sql["ref"]=$this->ref("partner","sku_key",$skuk);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		$se=$this->metMnSql($sql,["result"]);
		print_r($sql);
		return $se;
	}
	private function editPartnerOldData(string $sku_root):array{
		$sku_root=$this->getStringSqlSet($sku_root);
		$sql=[];
		$sql["result"]="SELECT 
				`name`		,`brand_name`		,`sku`		,`sku_root`	,`pn_type`		,IFNULL(`icon`,'') AS `icon`,
				`tel`			,`fax`					,`tax`		,`web`			,`no`,
				`tp_type`	,`od_type`			,`pn_type`	,`alley`		,`road`			,`distric`,
				`country`,`province`,`post_no`,`note`
			FROM `partner` 
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
	private function editPartnerSetCurent(string $sku_root):void{
		$od=$this->editPartnerOldData($sku_root);
		$fl=["sku","name","brand_name","pn_type","icon_load",
					"no","alley","road","distric","country",
					"province","post_no",
					"tel","fax","tax","web","tp_type",
					"od_type","note"];
		foreach($fl as $v){
			if(isset($od[$v])){
				$_POST[$v]=$od[$v];
				if($v == "prop"){
					$_POST[$v] = $this->propToFromValue($od[$v]);
				}
			}
		}
	}
	protected function regisPartner():void{
		$error="";
		$img=["result"=>false,"set"=>0];
		$mime="";
		if(isset($_POST["submit"])&&$_POST["submit"]=="clicksubmit"){
			$se=$this->checkSet("partner",["post"=>["name","brand_name","sku","tax","pn_type","od_type","tp_type","tel","fax","web","no","alley","road","distric","country","province","post_no","note"]],"post");
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
				 $qe=$this->regisPartnerInsert($key,$img,$mime);
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
				$this->regisPartnerPage($error);
			}
		}else{
			$this->regisPartnerPage($error);
		}
	}
	private function regisPartnerInsert(string $keyi,array $img,string $mime=""):array{
		//print_r($this->dn_value);exit;
		$name=$this->getStringSqlSet($_POST["name"]);
		$brand_name=$this->getStringSqlSet($_POST["brand_name"]);
		$icon=(isset($_POST["icon"]))?$_POST["icon"]:"";
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$tax=$this->getStringSqlSet($_POST["tax"]);
		$tel=$this->getStringSqlSet($_POST["tel"]);
		$fax=$this->getStringSqlSet($_POST["fax"]);
		$web=$this->getStringSqlSet($_POST["web"]);
		$pn_type = $this->getStringSqlSet($_POST["pn_type"]);
		$tp_type = $this->getStringSqlSet($_POST["tp_type"]);
		$od_type = $this->getStringSqlSet($_POST["od_type"]);
		$no=$this->getStringSqlSet($_POST["no"]);
		$alley=$this->getStringSqlSet($_POST["alley"]);
		$road=$this->getStringSqlSet($_POST["road"]);
		$distric=$this->getStringSqlSet($_POST["distric"]);
		$country=$this->getStringSqlSet($_POST["country"]);
		$province=$this->getStringSqlSet($_POST["province"]);
		$post_no=$this->getStringSqlSet($_POST["post_no"]);
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
			@count_name:=(SELECT COUNT(`id`)  FROM `partner` WHERE `name`=".$name."),
			@count_sku:=(SELECT COUNT(`id`)   FROM `partner` WHERE `sku`=".$sku.");
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
				INSERT INTO `partner`  (
					`sku`			,`sku_key`		,`sku_root`		,`brand_name`		,`name`,
					`pn_type`		,`icon`				,`no`				,`alley`					,`road`,
					`distric`		,`country`			,`province`		,`post_no`			,`tel`,
					`fax`			,`tax`				,`web`				,`tp_type`				,`od_type`,
					`note`
				) VALUES (
					".$sku."			,@sku_key			,@sku_key			,".$brand_name."	,".$name.",
					".$pn_type."	,@icon				,".$no."				,".$alley."				,".$road.",
					".$distric."		,".$country."		,".$province."		,".$post_no."			,".$tel.",
					".$fax."			,".$tax."			,".$web."			,".$tp_type."			,".$od_type.",
					".$note."
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
		$sql["ref"]=$this->ref("partner","sku_key",$key);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		$se=$this->metMnSql($sql,["result"]);
		print_r($sql);
		return $se;
	}
	protected function xxxxxxxxxxxxxxxxsetPropR(string $prop):string{
		$ar = [];
		if(strlen(trim($prop))>2){
			$prop =trim($prop);
			$ar = explode(",,",substr($prop,1,-1));
		}
		return json_encode($ar);
	}
	protected function regisPartnerPage(string $error):void{
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$icon=(isset($_POST["icon"]))?$_POST["icon"]:"";
		$brand_name=(isset($_POST["brand_name"]))?htmlspecialchars($_POST["brand_name"]):"";
		$sku=(isset($_POST["sku"]))?htmlspecialchars($_POST["sku"]):"";
		$tax=(isset($_POST["tax"]))?htmlspecialchars($_POST["tax"]):"";
		$tel=(isset($_POST["tel"]))?htmlspecialchars($_POST["tel"]):"";
		$fax=(isset($_POST["fax"]))?htmlspecialchars($_POST["fax"]):"";
		$web=(isset($_POST["web"]))?htmlspecialchars($_POST["web"]):"";
		$pn_type = (isset($_POST["pn_type"]))?htmlspecialchars($_POST["pn_type"]):"";
		$tp_type = (isset($_POST["tp_type"]))?htmlspecialchars($_POST["tp_type"]):"";
		$od_type = (isset($_POST["od_type"]))?htmlspecialchars($_POST["od_type"]):"";
		$no=(isset($_POST["no"]))?htmlspecialchars($_POST["no"]):"";
		$alley=(isset($_POST["alley"]))?htmlspecialchars($_POST["alley"]):"";
		$road=(isset($_POST["road"]))?htmlspecialchars($_POST["road"]):"";
		$distric=(isset($_POST["distric"]))?htmlspecialchars($_POST["distric"]):"";
		$country=(isset($_POST["country"]))?htmlspecialchars($_POST["country"]):"";
		$province=(isset($_POST["province"]))?htmlspecialchars($_POST["province"]):"";
		$post_no=(isset($_POST["post_no"]))?htmlspecialchars($_POST["post_no"]):"";
		$note=(isset($_POST["note"]))?htmlspecialchars($_POST["note"]):"";
		$this->addDir("","เพิ่มคู่ค้า");
		$this->pageHead(["title"=>"เพิ่มคู่ค้า DIYPOS","js"=>["partner","Pn"],"run"=>["Pn"],"css"=>["partner"]]);
		echo '<div class="content">
			<div class="form">
				<h1 class="c">เพิ่มคู่ค้า</h1>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}		
		echo '		<form name ="add_partner" method="post">
			<input type="hidden" name="submit" value="clicksubmit" />
			<input type="hidden" id="icon_id" name="icon" value="'.$icon.'" />
			<p><label for="partner_name">ชื่อ</label></p>
			<div><input id="partner_name" class="want" type="text" name="name" value="'.$name.'" autocomplete="off" /></div>
			<p><label for="partner_brand_name">ชื่อการค้า</label></p>
			<div><input id="partner_brand_name" type="text" name="brand_name" value="'.$brand_name.'" autocomplete="off" /></div>
			<p><label for="partner_sku">รหัสภายใน</label></p>
			<div><input id="partner_sku" type="text" value="'.$sku.'"  name="sku" autocomplete="off"  /></div>
			<p><label for="tax">เลขที่ผู้เสียภาษี</label></p>
			<div><input id="tax" type="text" value="'.$tax.'"  name="tax" autocomplete="off"  /></div>
			<p><label for="pn_type">ประเภทคู่ค้า</label></p>
			<div>
				<select name="pn_type">
					<option value=""'.($pn_type == ""?" selected":"").'></option>					
					<option value="s"'.($pn_type == "s"?" selected":"").'>'.$this->pn_type["s"].'</option>
					<option value="n"'.($pn_type == "n"?"selected":"").'>'.$this->pn_type["n"].'</option>
				</select>
			</div>
			<p><label for="od_type">รูปแบบการสั่งซื้อหลัก</label></p>
			<div>
				<select name="od_type">	
					<option value=""'.($od_type == ""?" selected":"").'></option>				
					<option value="s"'.($od_type == "s"?" selected":"").'>'.$this->od_type["s"].'</option>
					<option value="a"'.($od_type == "a"?"selected":"").'>'.$this->od_type["a"].'</option>
					<option value="o"'.($od_type == "o"?" selected":"").'>'.$this->od_type["o"].'</option>
					<option value="t"'.($od_type == "t"?"selected":"").'>'.$this->od_type["t"].'</option>
				</select>
			</div>
			<p><label for="tp_type">การส่งสินค้า</label></p>
			<div>
				<select name="tp_type">	
					<option value=""'.($tp_type == ""?" selected":"").'></option>				
					<option value="1"'.($tp_type == "1"?" selected":"").'>'.$this->tp_type["1"].'</option>
					<option value="0"'.($tp_type == "0"?"selected":"").'>'.$this->tp_type["0"].'</option>
				</select>
			</div>
			<p><label for="tel">โทรศัพท์</label></p>
			<div><input id="tel" type="text" value="'.$tel.'"  name="tel" autocomplete="off"  /></div>
			<p><label for="fax">แฟ็ก</label></p>
			<div><input id="fax" type="text" value="'.$fax.'"  name="fax" autocomplete="off"  /></div>
			<p><label for="web">เว็บไซต์</label></p>
			<div><input id="web" type="text" value="'.$web.'"  name="web" autocomplete="off"  /></div>
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
			<p><label for="note">รายละเอียดย่อ</label></p>
			<div><input id="note" type="text" value="'.$note.'"  name="note" autocomplete="off"  /></div>
			<div>
				<div id="div_fileuploadpre" class="fileuploadpre1"></div>
				<input id="upload_pic" type="file" accept="image/png,image/gif,image/jpeg,image/webp" style="display:none" name="picture" onchange="F.fileUploadShow(event,1,\'icon_id\',1024,160)" />
				<label for="upload_pic" style="border-radius: 2px 2px 2px 2px;text-align: center;line-height:32px;display:block;background-image: linear-gradient(to bottom right, #bbbbbb, #888888);color:#ffffff;;width:100%;height:32px;">+เลือกรูปภาพ</label>
			</div>	
			<script type="text/javascript">F.fileUploadShow(null,1,\'icon_id\',480,160,\'load\',\'div_fileuploadpre\')</script>
		';			
		echo '</table>
					<br />
					<input type="submit" value="เพิ่มคู่ค้า" />
				</form>
			</div>
		</div>';
		$this->pageFoot();
	}
	protected function pagePartner(){
		$this->defaultPageSearch();
		$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["partner"],"js"=>["partner","Pn"],"run"=>["Pn"]]);
			echo '<div class="content">
				<div class="form">
				<h1 class="c">'.$this->title.'</h1>
				<div class="pn_search">
					<form class="form100" name="pd_search" action="" method="get">
						<input type="hidden" name="a" value="partner" />
						<input type="hidden" name="lid" value="0" />
						<label><select id="product_search_fl" name="fl">
							<option value="name"'.(($this->fl=="name")?" selected":"").'>ชื่อ</option>
							<option value="brand_name"'.(($this->fl=="brand_nam")?" selected":"").'>ชื่อการค้า</option>
							<option value="sku"'.(($this->fl=="sku")?" selected":"").'>รหัสภายใน</option>
						</select>
						 <input id="product_search_tx" type="text" name="tx" value="'.str_replace("\\","",htmlspecialchars($this->txsearch)).'" />
						 <input  type="submit" value="🔍" /> </label></form>
				</div>';
		$this->writeContentPartner();		
		echo '<br /><p class="c"><input type="button" value="เพิ่มคู่ค้า" onclick="location.href=\'?a='.$this->a.'&b=regis\'" /></p>';
		echo '</div></div>';
		$this->pageFoot();
	}
	private function defaultSearch():string{
		$fla=["sku","brand_name","name"];
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
			$se=" WHERE `partner`.`".$fl."` LIKE  \"%".$tx."%\"  ";
		}
		return $se;
	}
	private function defaultPageSearch():void{
		$fla=["brand_name","sku","name"];
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
			$this->sh=" WHERE `partner`.`id`".$idsearch." AND `partner`.`".$fl."` LIKE  \"%".$tx."%\""  ;
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
	private function xxxxxxxxxxxxxxxxxdirTopBar():string{
		$group_name = "กลุ่มสินค้าหลัก";
		if($this->parent !== NULL && isset($this->group_list[$this->parent])){
			//$group_name =htmlspecialchars($this->group_list[$this->parent]["name"] );
			//$this->addDir("?a=group&amp;d=".$this->group_deep."&amp;parent=".$this->parent,$group_name);
			for($i=1;$i<$this->group_deep;$i++){
				if(isset($this->dn_value["d".$i]) && $this->dn_value["d".$i] !== NULL){
					$group_name =htmlspecialchars($this->group_list[$this->dn_value["d".$i]]["name"] );
					$this->addDir("?a=group&amp;d=".($i+1)."&amp;parent=".$this->dn_value["d".$i],$group_name);
				}
			}
		}
		return $group_name;
	}
	private function writeContentPartner():void{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$dt=$this->getAllPartner();
		$se=$dt["get"];
		echo '<form class="form100" name="'.$this->a.'" method="post">
			<input type="hidden" name="sku_root" value="" />';
		echo '	<table style="width:100%;">
				<tr><th>ที่</th>
				<th>รูป</th>
				<th>รหัสภายใน</th>
				<th>ชื่อ</th>
				<th>กระทำ</th>
				</tr>';
		for($i=0;$i<count($se);$i++){
			$ed='';
			if($se[$i]["sku_root"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}
			$cm=($i%2!=0)?" class=\"i2\"":"";
			$name=htmlspecialchars($se[$i]["name"]);
			$brand_name=$se[$i]["brand_name"];
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
				<td class="l"><div class="img48"><img src="img/gallery/64x64_'.$se[$i]["icon"].'"  alt="'.$sn.'" /></div></td>
				<td class="l">'.$sku.'</td>
				<td class="l"><a href="?a='.$this->a.'&amp;b=details&amp;sku_root='.$se[$i]["sku_root"].'">'.$name.'</a></td>
				<td class="action">
					<a onclick="Pn.edit(\''.$se[$i]["sku_root"].'\')" title="แก้ไข">📝</a>
					<a onclick="Pn.delete(\''.$se[$i]["sku_root"].'\',\''.htmlspecialchars($se[$i]["name"]).'\')" title="ทิ้ง">🗑</a>
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
	private function xxxxxxxxxxxxxxxxxxwriteThisProp():void{
		$this_sku_root = "";
		for($i=1;$i<=count($this->dn_value);$i++){
			if($this->dn_value["d".$i] !== NULL){
				$this_sku_root = $this->dn_value["d".$i];
			}else{
				break;
			}
		}
		if($this->group_deep>1){
			$group_name = $this->group_list[$this_sku_root ]["name"];
			echo '<div class="group_this_prop">คุณสมบัติกลุ่ม :  <b>'.$group_name.'</b><br /> '.$this->getNamePropList(json_encode($this->group_list[$this_sku_root ]["prop"])).'</div>';
		}
	}
	private function xxxxxxxxxxxxxxxxxnoteColorDataType():string{
		return '<br><div>
			<div class="prop_data_type data_type_s"></div> = ข้อความ
			, <div class="prop_data_type data_type_n"> </div> = จำนวน
			, <div class="prop_data_type data_type_b"> </div> = จริงเท็จ
		</div>';
	}
	private function xxxxxxxxxxxxxxxxxxgetNamePropList(string $json_arr):string{
		$t = "";
		$arr = json_decode($json_arr);
		//$arr = (gettype($arr) === NULL)?[]:$arr;
		$s = 0;
		for($i=0;$i<count($arr);$i++){
			if(isset($this->prop_list[$arr[$i]])){
				$s+=1;
				$cm = ($s>1)?"":"";
				$t.=$cm."<span class=\"prop_data_type data_type_".$this->prop_list[$arr[$i]]["data_type"]."\">".$this->prop_list[$arr[$i]]["name"]."</span>";
			}
		}
		return $t;
	}
	public function getAllPartner():array{
		$sh=$this->defaultSearch();
		$re=[];
		$sql=[];
		$limit_page=(($this->page-1)*$this->per).",".($this->per+1);
		if($this->txsearch!=""){
				$limit_page=$this->per+1;
		}
		$sql["count"]="SELECT COUNT(*) AS `count`  FROM `partner`";
		$sql["get"]="SELECT `id`,`name`,`brand_name`,`icon`,`sku`,`sku_root` 
			FROM `partner` 
			".$this->sh." 
			ORDER BY `id` DESC LIMIT ".$limit_page."";
		$se=$this->metMnSql($sql,["count","get"]);
		if($se["result"]){
			$re=$se["data"];//["get"];
		}
		return $re;
	}
	private function xxxxxxxxxxxxxxxxgetDnValue():array{
		$re = [];
		for($i=1;$i<=$this->d_cols;$i++){
			$re["d".$i] = null;
		}
		if(isset($this->group_list[$this->parent])){
			for($i=1;$i<=$this->d_cols;$i++){
				$dn = $this->group_list[$this->parent]["d".$i];
				if($dn !== NULL){
					$re["d".$i] = $dn;
				}
			}
		}
		return $re;
	}
	private function xxxxxxxxxxxxxxxxxxxpropToFromValue(string $prop):string{
		$t = str_replace("\",\"",",,",substr($prop,1,-1));
		$t = str_replace("\"",",",$t);
		//echo $t;
		return $t;
	}
	private function xxxxxxxxxxxxxxxxxwriteDirGroup(int $deep = 0):void{
		echo '<div class="group_dir"><a href="?a=group">📁</a> /';
		for($i=1;$i<count($this->dir) + $deep;$i++){
			echo $this->dir[$i]."/";
		}
		echo '</div>';
	}
	public function xxxxxxxxxxxxxxxxwriteSelectGroup(string $value = "defaultroot"):void{
		#วนให้เท่ากับ $this->d_cols
		$this->group_list = $this->get("all_list_key_value");
		$value  = (isset($this->group_list[$value]))?$value:"defaultroot";
		$dt = [];
		$skuroot =[];
		$g = $this->group_list;
		for($o=1;$o<=$this->d_cols;$o++){
			foreach($g as $k=>$v){
				if($this->d_cols == 1){
					# $this->d_cols >1
				}else{
					if($o == 1){
						if(!isset($dt["$k"]) && $v["d".($o+1)] === null){
							$dt[$k] = [];
							unset($g[$k]);
						}
					}else if($o == 2){//echo $k;
						if(!isset($dt[$v["d".($o-1)]][$v["d".($o)]]) && $v["d".($o)] !== null && $v["d".($o+1)] === null){
							$dt[$v["d".($o-1)]][$v["d".($o)]]= [];
							unset($g[$v["d".($o-1)]][$v["d".($o)]]);
						}
					}else if($o == 3){
						if(!isset($dt[$v["d".($o-2)]][$v["d".($o-1)]][$v["d".($o)]]) && $v["d".($o)] !== null && $v["d".($o+1)] === null){
							$dt[$v["d".($o-2)]][$v["d".($o-1)]][$v["d".($o)]]= [];
							unset($g[$v["d".($o-2)]][$v["d".($o-1)]][$v["d".($o)]]);
						}
					//--อันสุดท้าย
					}else if($o == 4){
						if(!isset($dt[$v["d".($o-3)]][$v["d".($o-2)]][$v["d".($o-1)]][$v["d".($o)]]) && $v["d".($o)] !== null ){
							$dt[$v["d".($o-3)]][$v["d".($o-2)]][$v["d".($o-1)]][$v["d".($o)]]= [];
						}
					}
				}
			}
		}
		$nbr ="\n";
		echo '<script type="text/javascript">Pd.prop_list = '.json_encode($this->prop_list).'
			Pd.group_list = '.json_encode($this->group_list).'
			//let prop_post = '.$this->propCurentPostAsJs().'
			Pd.prop_b4edit = '.$this->propCurentPostAsJs().'
			Pd.group_b4edit = "'.$value.'"
		</script>';
		echo '<select name="group_root" onchange="Pd.setProp(this,\'table_product_group_prop\',\'\')">';
		foreach($dt as $a=>$b){
			$n1 = '/'.$this->group_list[$a]["name"].'/';
			echo $nbr.'<option value="'.$a.'"'.($value==$a?" selected =\"selected\"":"").'>'.$n1.'</option>';
			foreach($b as $c=>$d){
				$n2 = $n1.''.$this->group_list[$c]["name"].'/';
				echo $nbr.'<option value="'.$c.'"'.($value==$c?" selected =\"selected\"":"").'>'.$n2.'</option>';
				foreach($d as $e=>$f){
					$n3 = $n2.''.$this->group_list[$e]["name"].'/';
					echo $nbr.'<option value="'.$e.'"'.($value==$e?" selected =\"selected\"":"").'>'.$n3.'</option>';
					foreach($f as $g=>$h){
						$n4 = $n3.''.$this->group_list[$g]["name"].'/';
						echo $nbr.'<option value="'.$g.'"'.($value==$g?" selected =\"selected\"":"").'>'.$n4.'</option>';
					}
				}
			}
		}
		echo '</select>';
		$this->writePropValue($value);
		//print_r($dt);
	}
	protected function xxxxxxxxxxxxxxxxxpropCurentPostAsJs():string{
		//print_r($_POST);echo "****";
		$re=[];
		foreach($_POST as $k=>$v){
			if(substr($k,0,5) == "prop_"){
				$kr = substr($k,5);
				$val = trim($_POST["prop_".$kr]);
				if(isset($this->prop_list[$kr]["data_type"]) && $this->prop_list[$kr]["data_type"] == "n"){

				}
				$re[$kr] = $val;
			}
		}
		return json_encode($re,JSON_NUMERIC_CHECK);
	}
	private function xxxxxxxxxxxxxxxxxxxxxwritePropValue(string $sku_root):void{
		//print_r($_POST);
		//echo $sku_root;
		//print_r($this->prop_list);
		//print_r($this->group_list[$sku_root]["prop"]);
		echo '<table id="table_product_group_prop" style="width:100%;margin:5px 0;">
			<caption>คุณสมบัติกลุ่ม</caption>
			<tr><th>รายละเอียด</th><th>ค่า</th></tr>';
		for($i=0;$i<count($this->group_list[$sku_root]["prop"]);$i++){
			$prop = $this->prop_list[$this->group_list[$sku_root]["prop"][$i]];
			$name = htmlspecialchars($prop["name"]);
			$lr = ($prop["data_type"] == "n")?"r":"l";
			$tr = ($i%2 == 1)?"2":"1";
			echo '<tr class="i'.$tr.'">
				<td class="l">'.$name .'</td>
				<td>';
			if($prop["data_type"] == "n" || $prop["data_type"] == "s"){
				$vlt =(isset($_POST["prop_".$prop["sku_root"]]))?htmlspecialchars($_POST["prop_".$prop["sku_root"]]):"";
				echo '	<input name="prop_'.$prop["sku_root"].'" type="text"  style="width:calc(100% - 8px);" class="'.$lr.'" value="'.$vlt.'" />';
			}else if($prop["data_type"] == "b"){
				$vlt =(isset($_POST["prop_".$prop["sku_root"]])
					&&($_POST["prop_".$prop["sku_root"]] == "-1"
					|| $_POST["prop_".$prop["sku_root"]] =="1"))?htmlspecialchars($_POST["prop_".$prop["sku_root"]]):"0";
				echo '<select name="prop_'.$prop["sku_root"].'" style="width:100%;appearance: none;background-color:white;">
					<option value="0"'.($vlt == "0"?" selected=\"selected\"":"").'>❔</option>
					<option value="1"'.($vlt == "1"?" selected=\"selected\"":"").'>✔️</option>
					<option value="-1"'.($vlt == "-1"?" selected=\"selected\"":"").'>❌</option>
				</select>';
			}
			echo '	</td>
			</tr>';
		}	
		echo '</table>';
		//echo "prop_".$prop["sku_root"];
	}
}
