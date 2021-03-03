<?php
class partner extends main{
	private  $d_cols;
	public function __construct(){
		parent::__construct();
		$file = "php/class/image.php";
		require($file);
		$dir=dirname(__DIR__)."/img/gallery";
		$this->img=new image($dir);
		$this->title = "คู่ค้า";
		$this->a = "partner";
		$this->pn_type = ["s"=>"ผู้ผลิต","n"=>"ตัวแทนจำหน่าย"];
		$this->tp_type = ["0"=>"ต้องไปรับสินค้าเอง","1"=>"ส่งสินค้าถึงร้าน"];
		$this->od_type = ["s"=>"มีคนมาจด","a"=>"โทรศัพท์มาถาม","o"=>"ออกไปซื้อเอง","t"=>"โทรสั่งซื้อ"];
	}
	public function run(){
		$q=["regis","edit","delete"];
		$this->addDir("?a=".$this->a,$this->title);
		if(isset($_GET["b"])&&in_array($_GET["b"],$q)){
			$t=$_GET["b"];
			if($t=="regis"){
				$this->regisPartner();
			}else if($t=="edit"){
				$this->editPartner();
			}else if($t=="delete"){
				$this->deletePartner();
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
	private function xxxxxxxxxdeleteGroup():void{
		if(isset($_POST["sku_root"])){
			$sku_root=$this->getStringSqlSet($_POST["sku_root"]);
			$sql=[];
			$sql["set"]="SELECT @sku_root:=".$sku_root.";";
			$sql["set2"]="SELECT @group_default_key:=(SELECT `sku_key` FROM `group` WHERE `sku_root`=\"defaultroot\");";
			$wx_t = " (1 = 0 ";
			for($i=1;$i<=$this->d_cols;$i++){
				$wx_t .= " OR `d".$i."` = @sku_root ";
			}
			$wx_t .= ") AND @sku_root != \"defaultroot\"";
			$sql["del"]="DELETE FROM `group` WHERE ".$wx_t.";";
			$sql["update_pd"]="UPDATE `product` SET `group_key` =@group_default_key,`group_root`=\"defaultroot\" WHERE `group_root`=".$sku_root.";";
			//print_r($sql);
			$re = $this->metMnSql($sql,[]);
			//print_r($re);
			header('Location:?a='.$this->a.'&d='.$this->group_deep.'&parent='.$this->parent.'&ed='.$_POST["sku_root"]);
		}
	}
	private function xxxxxxxxxxeditGroupPage(string $error):void{
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$sku=(isset($_POST["sku"]))?htmlspecialchars($_POST["sku"]):"";
		$sku_root=(isset($_POST["sku_root"]))?htmlspecialchars($_POST["sku_root"]):"";
		$prop = (isset($_POST["prop"]))?htmlspecialchars($_POST["prop"]):"";
		$prop_arr = explode(",,",substr($prop,1,-1));
		$prop_list = $this->prop_list;

		$group_name = $this->dirTopBar();
		$this->addDir("","แก้ไข ".$this->title." ".$name);		
		$this->pageHead(["title"=>"แก้ไข ".$this->title." DIYPOS","js"=>["group","Gp"],"run"=>["Gp"],"css"=>["group"]]);
		echo '<div class="content">
			<div class="form">
				<h1 class="c">แก้ไข '.$this->title.' '.$name.'</h1>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}		
		echo '		<form name ="add_group" method="post">
					<input type="hidden" name="submit" value="clicksubmit" />
					<input type="hidden" name="sku_root" value="'.$sku_root.'" />
					<input type="hidden" name="prop" value="'.$prop.'" />
					<p><label for="group_name">ชื่อ</label></p>
					<div><input id="group_name" class="want" type="text" name="name" value="'.$name.'" autocomplete="off" /></div>
					<p><label for="group_sku">รหัสภายใน</label></p>
					<div><input id="group_sku" type="text" value="'.$sku.'"  name="sku" autocomplete="off"  /></div>
					<div><br />
						<table id="table_prop" class="group_table_prop">
							<caption>คุณสมบัติ</caption>
							<tr><th>ข้อมูล</th><th>ชนิด</th></tr>';
		//print_r($prop_list);
		//print_r($prop_arr);
		$trn = 0;
		//--style .ให้ตรงกับ group.js::selectOK
		for($i=0;$i<count($prop_arr);$i++){
			if(isset($this->prop_list[$prop_arr[$i]] )){
				$trn+=1;
				$tr = ($trn%2 ==1)?"i1":"i2";
				echo '<tr class="'.$tr.'"><td style="text-align:left;padding:8px 5px">'.$this->prop_list[$prop_arr[$i]]["name"].'</td>
					<td style="text-align:left;padding:8px 5px">'.$this->prop_->data_type[$this->prop_list[$prop_arr[$i]]["data_type"]].'</td></tr>';
			}
		}				
		echo '			</table>
						<input id="button_add_prop" class="group_add_prop" type="button" value="⚙️ คุณสมบัติ" onclick="Gp.ctAddProp(\'add_group\',\'prop\',\'table_prop\')" />
					</div>
					<br />
					<input type="submit" value="แก้ไข '.$this->title.'" />
				</form>
			</div>
		</div>';
		$this->pageFoot();
	}
	private function xxxxxxxxxxxxxxeditGroup():void{
		$error="";
		if(isset($_POST["submit"])&&$_POST["submit"]=="clicksubmit"){
			$se=$this->checkSet($this->a,["post"=>["name","sku","sku_root","prop"]],"post");
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				 $qe=$this->editGroupUpdate();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					 //print_r($qe);
					header('Location:?a='.$this->a.'&d='.$this->group_deep.'&parent='.$this->parent.'&ed='.$_POST["sku_root"]);
				}
			}
			if($error!=""){
				$this->editGroupPage($error);
			}
		}else{
			$sku_root=(isset($_POST["sku_root"]))?$_POST["sku_root"]:"";
			$this->editGroupSetCurent($sku_root);
			$this->editGroupPage($error);
		}
	}
	private function xxxxxxxxxxxxxeditGroupUpdate():array{
		$name=$this->getStringSqlSet($_POST["name"]);
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$prop = $this->getStringSqlSet($this->setPropR($_POST["prop"]));
		$skuk=$this->key("key",7);
		$sku_key=$this->getStringSqlSet($skuk);
		$sku_root=$this->getStringSqlSet($_POST["sku_root"]);
		$d1 = "NULL";
		$d2 = "NULL";
		$d3 = "NULL";
		$d4 = "NULL";
		if($this->group_deep == 1){
			$d1 = $sku_root;
		}
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',";
			for($i=1;$i<=count($this->dn_value);$i++){
				if($this->dn_value["d".$i] !== NULL){
					$sql["set"].="@d".$i.":=\"".$this->dn_value["d".$i]."\",";
				}else{
					if($i == $this->group_deep){
						$sql["set"].="@d".$i.":=".$sku_root.",";
					}else{
						$sql["set"].="@d".$i.":=NULL,";
					}
				}
			}	
			$wc_tx = " `d".($this->group_deep-1)."` = \"".$this->parent."\" AND `d".($this->group_deep)."` IS NOT NULL AND `d".($this->group_deep+1)."` IS  NULL AND ";
			if($this->group_deep == 1){
				$wc_tx="`d2` IS NULL AND ";
			}else if($this->group_deep >=$this->d_cols ){
				$wc_tx = " `d".($this->group_deep-1)."` = \"".$this->parent."\" AND `d".($this->group_deep)."` IS NOT NULL AND ";
			}
			$sql["set"].="@count_name:=(SELECT COUNT(`id`)  FROM `group` WHERE ".$wc_tx." `name`=".$name." AND `sku_root` !=".$sku_root."),
			@count_sku:=(SELECT COUNT(`id`)   FROM `group` WHERE `sku`=".$sku." AND `sku_root` !=".$sku_root.");
		";
		$sql["check"]="
			IF @count_name > 0 THEN 
				SET @message_error='เกิดขอผิดพลาด ชื่อที่แก้ไขมา มีแล้ว โปรดลองชื่ออื่น';
			ELSEIF @count_sku > 0 THEN
				SET @message_error='เกิดขอผิดพลาด รหัสภายในทแก้ไขมา มีแล้ว โปรดลอง รหัสภายในอื่น';
			END IF;			
		";
		$sql["run"]="
			IF LENGTH(@message_error) = 0 THEN
				UPDATE `group` SET  `sku`=".$sku.",`sku_key`=".$sku_key.",  `name`= ".$name." ,  `prop`= ".$prop." WHERE `sku_root`=".$sku_root.";
				UPDATE `product` SET `group_key`=".$sku_key." WHERE `group_root`=".$sku_root.";
				SET @result=1;
			END IF;
		";
		$sql["ref"]=$this->ref("group","sku_key",$skuk);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($sql);exit;
		return $se;
	}
	private function xxxxxxxxxxxxxxxxxxxxeditGroupOldData(string $sku_root):array{
		$sku_root=$this->getStringSqlSet($sku_root);
		$sql=[];
		$sql["result"]="SELECT `name`,`sku`,`prop` FROM `group` WHERE `sku_root`=".$sku_root."";
		$re=$this->metMnSql($sql,["result"]);
		if(isset($re["data"]["result"][0])){
			return $re["data"]["result"][0];
		}
		return [];
	}
	private function xxxxxxxxxxxxxxxxeditGroupSetCurent(string $sku_root):void{
		$od=$this->editGroupOldData($sku_root);
		$fl=["sku","name","prop"];
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
		$img=["result"=>false];
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
				}
				//$this->img->imgSave($se,"123456789gdgdfd");
			}
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				$key=$this->key("key",7);
				 $qe=$this->regisPartnerInsert($key,$mime);
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
	private function regisPartnerInsert(string $keyi,string $mime=""):array{
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
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@sku_key:='".$key."',
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
		if(isset($_POST["icon"])){
			$se=$this->img->imgCheck("icon");
			$this->img->imgSave($se,"123456789gdgdfd");
		}
		//echo $this->propToFromValue(json_encode($this->group_list[$this->parent]["prop"]));
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
				<input id="upload_pic" type="file" accept="image/png,image/gif,image/jpeg" style="display:none" name="picture" onchange="F.fileUploadShow(event,1,\'icon_id\',1024,160)" />
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
		$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["partner"],"js"=>["partner","Pn"],"run"=>["Pn"]]);
			echo '<div class="content">
				<div class="form">
				<h1 class="c">'.$this->title.'</h1>';
		$this->writeContentPartner();		
		echo '<br /><p class="c"><input type="button" value="เพิ่มคู่ค้า" onclick="location.href=\'?a='.$this->a.'&b=regis\'" /></p>';
		echo '</div></div>';
		$this->pageFoot();
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
		$se=$this->getAllPartner();
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
			echo '<tr'.$cm.'><td class="r">'.($i+1).'.</td>
				<td class="l"><img src="img/gallery/64x64_'.$se[$i]["icon"].'"  width="48" onerror="M.le(event)" /></td>
				<td class="l">'.$se[$i]["sku"].'</td>
				<td class="l"><a href="?a='.$this->a.'">'.htmlspecialchars($se[$i]["name"]).'</a></td>
				<td class="action">
					<a onclick="Pn.edit(\''.$se[$i]["sku_root"].'\')" title="แก้ไข">📝</a>
					<a onclick="Pn.delete(\''.$se[$i]["sku_root"].'\',\''.htmlspecialchars($se[$i]["name"]).'\')" title="ทิ้ง">🗑</a>
					'.$ed.'
					</td>
				</tr>';
		}
		echo '</table></form>';
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
	private function getAllPartner():array{
		$re=[];
		$sql=[];
		$sql["get"]="SELECT * FROM `partner` ORDER BY `name` ASC";
		$se=$this->metMnSql($sql,["get"]);
		if($se["result"]){
			$re=$se["data"]["get"];
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
