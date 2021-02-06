<?php
class group extends main{
	private  $d_cols;
	public function __construct(){
		parent::__construct();
		$this->title = "กลุ่มสินค้า";
		$this->d_cols = 4;	#สัมพันธ์ กับ writeSelectGroup:
		$this->group_deep = 1;
		$this->parent = null;
		$this->prop_ = new prop();
		$this->a = "group";
		$this->prop_list = $this->prop_->get("all_list_key_value");
		$this->group_list =[];
		$this->dn_value =[];
	}
	public function run(){
		//print_r($this->get("all_list_key_value"));
		$this->group_list = $this->get("all_list_key_value");
		$this->dn_value = $this->getDnValue();
		$q=["regis","edit","delete"];
		$this->addDir("?a=group","กลุ่มสินค้า");
		if(isset($_GET["parent"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["parent"]) && isset($this->group_list[$_GET["parent"]])){
			$this->parent = $_GET["parent"];
			$this->dn_value = $this->getDnValue();
			if(isset($_GET["d"])&&preg_match("/^(1|2|3|4|5){1}$/",$_GET["d"]) ){
				for($i=0;$i<count($this->dn_value);$i++){
					if($this->dn_value["d".($i+1)] != NULL){
						$this->group_deep = $i+1+1;
						//echo $this->group_deep."**".$this->parent."********".$this->dn_value["d".($i+1)]."<br>";
					}else{
						break;
					}
				}
			}
			//print_r($this->group_list[$_GET["parent"]]);
		}	
		if(isset($_GET["b"])&&in_array($_GET["b"],$q) && (($this->parent!=NULL && $this->group_deep > 1) || $this->group_deep == 1)){
			$t=$_GET["b"];
			if($t=="regis"){
				$this->regisGroup();
			}else if($t=="edit"){
				$this->editGroup();
			}else if($t=="delete"){
				$this->deleteGroup();
			}
		}else{
			$this->pageGroup();
		}
	}
	public function get(string $cm):array{
		$se = [];
		if($cm == "all_list_key_value"){
			$se = $this->getGroupList();
		}
		return $se;
	}
	protected function getGroupList():array{
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
	private function deleteGroup():void{
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
	private function editGroupPage(string $error):void{
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
	private function editGroup():void{
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
	private function editGroupUpdate():array{
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
	private function editGroupOldData(string $sku_root):array{
		$sku_root=$this->getStringSqlSet($sku_root);
		$sql=[];
		$sql["result"]="SELECT `name`,`sku`,`prop` FROM `group` WHERE `sku_root`=".$sku_root."";
		$re=$this->metMnSql($sql,["result"]);
		if(isset($re["data"]["result"][0])){
			return $re["data"]["result"][0];
		}
		return [];
	}
	private function editGroupSetCurent(string $sku_root):void{
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
	protected function regisGroup():void{
		$error="";
		if(isset($_POST["submit"])&&$_POST["submit"]=="clicksubmit"){
			$se=$this->checkSet("group",["post"=>["name","sku","prop"]],"post");
			//print_r($se);
			if(!$se["result"]){
				$error=$se["message_error"];
			}else if($this->group_deep > $this->d_cols){
				$error="ไม่สามารถ เพิ่มกลุ่มย่อยได้อีกแล้ว โปรแกรมถูกกำหนดให้มี่ ". $this->d_cols." ระดับชั้นกลุ่ม";
			}else{
				 $qe=$this->regisGroupInsert();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					 //print_r($qe);
					header('Location:?a='.$this->a.'&d='.$this->group_deep.'&parent='.$this->parent.'&ed='.$qe["data"]["result"][0]["sku_root"]);
				}
			}
			if($error!=""){
				$this->regisGroupPage($error);
			}
		}else{
			$this->regisGroupPage($error);
		}
	}
	private function regisGroupInsert():array{
		//print_r($this->dn_value);exit;
		$name=$this->getStringSqlSet($_POST["name"]);
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$prop = $this->getStringSqlSet($this->setPropR($_POST["prop"]));
		$skuk=$this->key("key",7);
		$sku_root=$this->getStringSqlSet($skuk);
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@sku_root:=".$sku_root.",";
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
			@count_sku:=(SELECT COUNT(`id`)   FROM `group` WHERE `sku`=".$sku.");
		";
		$sql["check"]="
			IF @count_name > 0 THEN 
				SET @message_error='เกิดขอผิดพลาด ชื่อที่ส่งมา มีแล้ว โปรดลองชื่ออื่น';
			ELSEIF @count_sku > 0 THEN
				SET @message_error='เกิดขอผิดพลาด รหัสภายในที่ส่งมา มีแล้ว โปรดลอง รหัสภายในอื่น';
			END IF;			
		";
		$d_tx = "";
		$w_tx = "";
		for($i=1;$i<=count($this->dn_value);$i++){
			$d_tx.=",`d".$i."`";
			$w_tx.=",@d".$i."";
		}
		$sql["run"]="
			IF LENGTH(@message_error) = 0 THEN
				INSERT INTO `group`  (`sku`,`sku_key`,`sku_root`".$d_tx.",`name`,`prop`) 
				VALUES (".$sku.",".$sku_root.",".$sku_root."".$w_tx.",".$name.",".$prop.");
				SET @result=1;
			END IF;
		";
		//print_r($sql);exit;
		$sql["ref"]=$this->ref("group","sku_key",$skuk);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@sku_root AS `sku_root`";
		$se=$this->metMnSql($sql,["result"]);
		
		return $se;
	}
	protected function setPropR(string $prop):string{
		$ar = [];
		if(strlen(trim($prop))>2){
			$prop =trim($prop);
			$ar = explode(",,",substr($prop,1,-1));
		}
		return json_encode($ar);
	}
	protected function regisGroupPage(string $error):void{
		//echo $this->propToFromValue(json_encode($this->group_list[$this->parent]["prop"]));
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$sku=(isset($_POST["sku"]))?htmlspecialchars($_POST["sku"]):"";
		
		$prop_js = "";
		if($this->group_deep > 1){
			$prop_js = $this->propToFromValue(
								json_encode($this->group_list[$this->parent]["prop"])
							);
		}
		$prop = (isset($_POST["prop"]))?htmlspecialchars($_POST["prop"]):$prop_js;
		$prop_arr = explode(",,",substr($prop,1,-1));
		$prop_list = $this->prop_->get("all_list_key_value");
		
		
		$group_name = $this->dirTopBar();
		$this->addDir("","เพิ่มกลุ่มสินค้า");
		$this->pageHead(["title"=>"เพิ่มกลุ่มสินค้า DIYPOS","js"=>["group","Gp"],"run"=>["Gp"],"css"=>["group"]]);
		echo '<div class="content">
			<div class="form">
				<h1 class="c">เพิ่มกลุ่มสินค้า</h1>';
		
		$this->writeDirGroup(-1);		
		$this->writeThisProp();
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}		
		echo '		<form name ="add_group" method="post">
					<input type="hidden" name="submit" value="clicksubmit" />
					<input type="hidden" name="prop" value="'.$prop.'" />
					<p><label for="unit_name">ชื่อ</label></p>
					<div><input id="unit_name" class="want" type="text" name="name" value="'.$name.'" autocomplete="off" /></div>
					<p><label for="unit_sku">รหัสภายใน</label></p>
					<div><input id="unit_sku" type="text" value="'.$sku.'"  name="sku" autocomplete="off"  /></div>
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
					<input type="submit" value="เพิ่ม กลุ่ม" />
				</form>
			</div>
		</div>';
		$this->pageFoot();
	}
	protected function pageGroup(){
		$group_name = $this->dirTopBar();
		$this->pageHead(["title"=>"กลุ่มสินค้า DIYPOS","css"=>["group"],"js"=>["group","Gp"],"run"=>["Gp"]]);
			echo '<div class="content">
				<div class="form">
					<h1 class="c">'.$group_name.'</h1>';
			$this->writeContentGroup();		
			if($this->group_deep <= $this->d_cols){		
				$text_group_add = ($this->group_deep == 1)?"เพิ่มกลุ่มหลัก":"เพิ่มกลุ่มย่อย";
				echo $this->noteColorDataType();
				echo '<br /><p class="c"><input type="button" value="'.$text_group_add.'" onclick="location.href=\'?a=group&b=regis&d='.($this->group_deep).'&parent='.$this->parent.'\'" /></p>';
			}
		$this->pageFoot();
	}
	private function dirTopBar():string{
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
	private function writeContentGroup():void{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$se=$this->getAllGroup();
		echo '<form class="form100" name="'.$this->a.'" method="post">
			<input type="hidden" name="sku_root" value="" />';
		$this->writeDirGroup();
		$this->writeThisProp();
		if($this->group_deep <= $this->d_cols){	
			$text_group_add = ($this->group_deep == 1)?"กลุ่มหลัก":"กลุ่มย่อย";
			echo '	<table style="width:100%;">
				<caption>'.$text_group_add.' ('.count($se).')</caption>
				<tr><th>ที่</th>
				<th>รหัสภายใน</th>
				<th>ชื่อ</th>
				<th>คุณสมบัติ</th>
				<th>กระทำ</th>
				</tr>';
			for($i=0;$i<count($se);$i++){
				$ed='';
				if($se[$i]["sku_root"]==$edd){
					$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
				}
				$cm=($i%2!=0)?" class=\"i2\"":"";
				echo '<tr'.$cm.'><td class="r">'.($i+1).'.</td>
					<td class="l">'.$se[$i]["sku"].'</td>
					<td class="l"><a href="?a='.$this->a.'&amp;d='.($this->group_deep+1).'&amp;parent='.$se[$i]["sku_root"].'">'.htmlspecialchars($se[$i]["name"]).'</a></td>
					<td class="l">'.$this->getNamePropList($se[$i]["prop"]).'</td>
					<td class="action">
						<a onclick="Gp.edit(\''.$se[$i]["sku_root"].'\','.($this->group_deep+1).',\''.$this->parent.'\')" title="แก้ไข">📝</a>
						<a onclick="Gp.delete(\''.$se[$i]["sku_root"].'\',\''.htmlspecialchars($se[$i]["name"]).'\','.($this->group_deep+1).',\''.$this->parent.'\')" title="ทิ้ง">🗑</a>
						'.$ed.'
						</td>
					</tr>';
			}
		}
		echo '</table></form>';
	}
	private function writeThisProp():void{
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
	private function noteColorDataType():string{
		return '<br><div>
			<div class="prop_data_type data_type_s"></div> = ข้อความ
			, <div class="prop_data_type data_type_n"> </div> = จำนวน
			, <div class="prop_data_type data_type_b"> </div> = จริงเท็จ
		</div>';
	}
	private function getNamePropList(string $json_arr):string{
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
	private function getAllGroup():array{
		$re=[];
		$sql=[];
		$d_tx = "";
		for($i=1;$i<=$this->d_cols;$i++){
			$d_tx.=",d".$i;
		}
		$w_tx = "";
		for($i=0;$i<$this->d_cols;$i++){
			if(isset($this->dn_value["d".($i+1)]) && $this->dn_value["d".($i+1)] !== NULL){
				$w_tx.=" AND `d".($i+1)."` = \"".$this->dn_value["d".($i+1)]."\"";
			}
		}
		if($this->parent !== NULL && isset($this->group_list[$this->parent])&&$this->group_deep >= 1){
			$parent = $this->getStringSqlSet($this->parent);
			if($this->group_deep<$this->d_cols){
				$sql["get"]="SELECT id,sku,sku_root".$d_tx.",name,IFNULL(`prop`,'[]') AS `prop` 
					FROM `group` WHERE `d".($this->group_deep)."` IS  NOT NULL
						AND `d".($this->group_deep+1)."` IS  NULL
						".$w_tx."
					ORDER BY `name` ASC";
			}else if($this->group_deep == $this->d_cols){
				$sql["get"]="SELECT id,sku,sku_root".$d_tx.",name,IFNULL(`prop`,'[]') AS `prop` 
					FROM `group` WHERE 1 = 1
						AND `d".($this->group_deep)."` IS  NOT NULL
						".$w_tx."
					ORDER BY `name` ASC";
			}else{
				$sql["get"]="SELECT id,sku,sku_root".$d_tx.",name,IFNULL(`prop`,'[]') AS `prop` 
					FROM `group` WHERE 1 != 1
						".$w_tx."
					ORDER BY `name` ASC";
			}
		}else{
			$sql["get"]="SELECT id,sku,sku_root,name,IFNULL(`prop`,'[]') AS `prop` 
				FROM `group` 
				WHERE `sku_root` = `d1` ORDER BY `name` ASC";
		}
		$se=$this->metMnSql($sql,["get"]);
		if($se["result"]){
			$re=$se["data"]["get"];
		}
		//print_r($sql);
		//print_r($se);
		return $re;
	}
	private function getDnValue():array{
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
	private function propToFromValue(string $prop):string{
		$t = str_replace("\",\"",",,",substr($prop,1,-1));
		$t = str_replace("\"",",",$t);
		//echo $t;
		return $t;
	}
	private function writeDirGroup(int $deep = 0):void{
		echo '<div class="group_dir"><a href="?a=group">📁</a> /';
		for($i=1;$i<count($this->dir) + $deep;$i++){
			echo $this->dir[$i]."/";
		}
		echo '</div>';
	}
	public function writeSelectGroup(string $value = "defaultroot"):void{
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
	protected function propCurentPostAsJs():string{
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
	private function writePropValue(string $sku_root):void{
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
