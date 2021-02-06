<?php
class prop extends main{
	public function __construct(){
		parent::__construct();
		$this->title = "คุณสมบัติ";
		$this->a ="prop";
		$this->data_type = ["s"=>"ข้อความ","n"=>"จำนวน","b"=>"จริงเท็จ","u"=>""];
		$this->prop_list = null;
	}
	public function run(){
		$q=["regis","edit","delete"];
		$this->addDir("?a=".$this->a ,$this->title);
		if(isset($_GET["b"])&&in_array($_GET["b"],$q)){
			$t=$_GET["b"];
			if($t=="regis"){
				$this->regisProp();
			}else if($t=="edit"){
				$this->editProp();
			}else if($t=="delete"){
				$this->deleteProp();
			}
		}else{
			$this->pageProp();
		}
	}
	public function fetch(string $b):void{
		$re=["result"=>false,"message_error"=>"","data"=>[],"confirm"=>0];
		if($b == "get_all_list"){
			$re ["data"]= $this->get("all_list");
			if(count($re) >= 0){
				$re["result"] = true;
			}
		}
		header('Content-type: application/json');
		echo json_encode($re);
	}
	public function get(string $cm):array{
		$se = [];
		if($cm == "all_list" || $cm == "all_list_key_value"){
			$sql=[];
			$sql["get"]="SELECT sku_root,name,data_type FROM prop ORDER BY name ASC";
			$re=$this->metMnSql($sql,["get"]);
			
			if($re["result"] && $re  ["message_error"] == ""){
				if($cm == "all_list_key_value"){
					for($i=0;$i<count($re["data"]["get"]);$i++){
						$se[$re["data"]["get"][$i]["sku_root"]] = $re["data"]["get"][$i];
					}
				}else{
					$se = $re["data"]["get"];
				}
			}
		}
		return $se;
	}
	public function validate(string $prop_sku_root,string $value):string{
		$re = "";
		if($this->prop_list === null){
			$this->prop_list = $this->get("all_list_key_value");
		}
		if(isset($this->prop_list[$prop_sku_root])){
			if($this->prop_list[$prop_sku_root]["data_type"] == "n" ){
				if(strlen($value) == 0){
					$re = "";
				}else if(!preg_match("/^[0-9]{1,}((.?[0-9]{1,})?)$/",$value)){
					$re = "คุณสมบัติ : ".$this->prop_list[$prop_sku_root]["name"]." ไม่อยู่ในรูปแบบ จำนวน";
				}
			}else if($this->prop_list[$prop_sku_root]["data_type"] == "b" ){
				if(!preg_match("/^(-1|0|1)$/",$value)){
					$re = "คุณสมบัติ : ".$this->prop_list[$prop_sku_root]["name"]." ไม่อยู่ในรูปแบบ จริงเท็จ";
				}
			}
				
		}
		return $re;
	}
	private function deleteProp():void{
		if(isset($_POST["sku_root"])){
			$sku_root=$this->getStringSqlSet($_POST["sku_root"]);
			$sql=[];
			$sql["del"]="DELETE FROM `prop` WHERE `sku_root`=".$sku_root."";
			$this->metMnSql($sql,[]);
			header('Location:?a='.$this->a.'&ed='.$_POST["sku_root"]);
		}
	}
	private function editProp():void{
		$error="";
		if(isset($_POST["submit"])&&$_POST["submit"]=="clicksubmit"){
			$se=$this->checkSet($this->a,["post"=>["name","sku","sku_root","data_type"]],"post");
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				 $qe=$this->editPropUpdate();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					 //print_r($qe);
					header('Location:?a='.$this->a.'&ed='.$_POST["sku_root"]);
				}
			}
			if($error!=""){
				$this->editPropPage($error);
			}
		}else{
			$sku_root=(isset($_POST["sku_root"]))?$_POST["sku_root"]:"";
			$this->editPropSetCurent($sku_root);
			$this->editPropPage($error);
		}
	}
	private function editPropUpdate():array{
		$name=$this->getStringSqlSet($_POST["name"]);
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$data_type=$this->getStringSqlSet($_POST["data_type"]);
		$skuk=$this->key("key",7);
		$sku_key=$this->getStringSqlSet($skuk);
		$sku_root=$this->getStringSqlSet($_POST["sku_root"]);
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@count_name:=(SELECT COUNT(`id`)  FROM `prop` WHERE `name`=".$name." AND `sku_root` !=".$sku_root."),
			@count_sku:=(SELECT COUNT(`id`)   FROM `prop` WHERE `sku`=".$sku." AND `sku_root` !=".$sku_root.");
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
				UPDATE `prop` SET  `sku`=".$sku.",`sku_key`=".$sku_key.",  `name`= ".$name." ,  `data_type`= ".$data_type." WHERE `sku_root`=".$sku_root.";
				SET @result=1;
			END IF;
		";
		$sql["ref"]=$this->ref("prop","sku_key",$skuk);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($sql);
		return $se;
	}
	public function editPropSetCurent(string $sku_root):void{
		$od=$this->editPropOldData($sku_root);
		$fl=["sku","name","data_type"];
		foreach($fl as $v){
			if(isset($od[$v])){
				$_POST[$v]=$od[$v];
			}
		}
	}
	private function editPropOldData(string $sku_root):array{
		$sku_root=$this->getStringSqlSet($sku_root);
		$sql=[];
		$sql["result"]="SELECT `name`,`sku`,`data_type` FROM `prop` WHERE `sku_root`=".$sku_root."";
		$re=$this->metMnSql($sql,["result"]);
		if(isset($re["data"]["result"][0])){
			return $re["data"]["result"][0];
		}
		return [];
	}
	private function editPropPage(string $error):void{
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$sku=(isset($_POST["sku"]))?htmlspecialchars($_POST["sku"]):"";
		$sku_root=(isset($_POST["sku_root"]))?htmlspecialchars($_POST["sku_root"]):"";
		$data_type = (isset($_POST["data_type"]))?htmlspecialchars($_POST["data_type"]):"";
		$data_type = ($data_type == "s" || $data_type == "n" || $data_type == "b")?$data_type:"s";
		$this->addDir("","แก้ไข".$this->title."สินค้า ".$name);
		$this->pageHead(["title"=>"แก้ไข".$this->title."สินค้า DIYPOS"]);
		echo '<div class="content">
			<div class="form">
				<h1 class="c">แก้ไข'.$this->title.'สินค้า</h1>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}		
		echo '		<form method="post">
					<input type="hidden" name="submit" value="clicksubmit" />
					<input type="hidden" name="sku_root" value="'.$sku_root.'" />
					<p><label for="prop_name">ชื่อ</label></p>
					<div><input id="prop_name" class="want" type="text" name="name" value="'.$name.'" autocomplete="off" /></div>
					<p><label for="prop_sku">รหัสภายใน</label></p>
					<div><input id="prop_sku" type="text" value="'.$sku.'"  name="sku"  autocomplete="off" /></div>
					<p><label for="prop_sku">ชนิดข้อมูล</label></p>
					<div>
						<select name="data_type">					
							<option value="s"'.($data_type == "s"?" selected":"").'>'.$this->data_type["s"].'</option>
							<option value="n"'.($data_type == "n"?"selected":"").'>'.$this->data_type["n"].'</option>
							<option value="b"'.($data_type == "b"?"selected":"").'>'.$this->data_type["b"].'</option>
						</select>
					</div>
					<input type="submit" value="แก้ไข" />
				</form>
			</div>
		</div>';
	}
	private function regisProp():void{
		$error="";
		if(isset($_POST["submit"])&&$_POST["submit"]=="clicksubmit"){
			$se=$this->checkSet($this->a,["post"=>["name","sku"]],"post");
			//print_r($se);
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				 $qe=$this->regisPropInsert();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					 //print_r($qe);
					header('Location:?a='.$this->a.'&ed='.$qe["data"]["result"][0]["sku_root"]);
				}
			}
			if($error!=""){
				$this->regisPropPage($error);
			}
		}else{
			$this->regisPropPage($error);
		}
	}
	private function regisPropInsert():array{
		$name=$this->getStringSqlSet($_POST["name"]);
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$data_type = (
				isset($_POST["data_type"]) 
				&& (
					$_POST["data_type"] == "s" 
					|| $_POST["data_type"] == "n"
					|| $_POST["data_type"] == "b"
				 )
			)?$_POST["data_type"]:"s";
		$data_type = $this->getStringSqlSet($data_type);
		$skuk=$this->key("key",7);
		$sku_root=$this->getStringSqlSet($skuk);
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@sku_root:=".$sku_root.",
			@count_name:=(SELECT COUNT(`id`)  FROM `prop` WHERE `name`=".$name."),
			@count_sku:=(SELECT COUNT(`id`)   FROM `prop` WHERE `sku`=".$sku.");
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
				INSERT INTO `prop`  (`sku`,`sku_key`,`sku_root`,`name`,`data_type`) VALUES (".$sku.",".$sku_root.",".$sku_root.",".$name.",".$data_type.");
				SET @result=1;
			END IF;
		";
		$sql["ref"]=$this->ref("prop","sku_key",$skuk);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@sku_root AS `sku_root`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($sql);
		return $se;
	}
	private function regisPropPage(string $error):void{
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$sku=(isset($_POST["sku"]))?htmlspecialchars($_POST["sku"]):"";
		$this->addDir("?a=".$this->a."&amp;b=regis","เพิ่ม ".$this->title);
		$this->pageHead(["title"=>"เพิ่ม ".$this->title." DIYPOS"]);
		echo '<div class="content">
			<div class="form">
				<h1 class="c">'.$this->title.'</h1>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}		
		echo '		<form method="post">
					<input type="hidden" name="submit" value="clicksubmit" />
					<p><label for="prop_name">ชื่อ</label></p>
					<div><input id="prop_name" class="want" type="text" name="name" value="'.$name.'" autocomplete="off" /></div>
					<p><label for="prop_sku">รหัสภายใน</label></p>
					<div><input id="prop_sku" type="text" value="'.$sku.'"  name="sku" autocomplete="off"  /></div>
					<p><label for="prop_sku">ชนิดข้อมูล</label></p>
					<div>
						<select name="data_type">					
							<option value="s">'.$this->data_type["s"].'</option>
							<option value="n">'.$this->data_type["n"].'</option>
							<option value="b">'.$this->data_type["b"].'</option>
						</select>
					</div>
					<br />
					<input type="submit" value="เพิ่ม" />
				</form>
			</div>
		</div>';
		$this->pageFoot();
	}
	private function pageProp(){
		$this->pageHead(["title"=>$this->title." DIYPOS","js"=>["prop","Prp"]]);
			echo '<div class="content">
				<div class="form">
					<h1 class="c">'.$this->title.'</h1>';
			$this->writeContentProp();
			echo '<br /><p class="c"><input type="button" value="เพิ่ม"'.$this->title.' onclick="location.href=\'?a='.$this->a.'&b=regis\'" /></p>';
		$this->pageFoot();
	}
	private function writeContentProp():void{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$se=$this->getAllProp();
		echo '<form class="form100" name="'.$this->a.'" method="post">
			<input type="hidden" name="sku_root" value="" />
			<table><tr><th>ที่</th>
			<th>รหัสภายใน</th>
			<th>ชื่อ</th>
			<th>ชนิดข้อมูล</th>
			<th>กระทำ</th>
			</tr>';
		for($i=0;$i<count($se);$i++){
			$ed='';
			if($se[$i]["sku_root"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}
			$cm=($i%2!=0)?" class=\"i2\"":"";
			echo '<tr'.$cm.'><td class="r">'.$se[$i]["id"].'</td>
				<td class="l">'.$se[$i]["sku"].'</td>
				<td class="l">'.htmlspecialchars($se[$i]["name"]).'</td>
				<td class="l">'.$this->data_type[$se[$i]["data_type"]].'</td>
				<td class="action">
					<a onclick="Prp.edit(\''.$se[$i]["sku_root"].'\')" title="แก้ไข">📝</a>
					<a onclick="Prp.delete(\''.$se[$i]["sku_root"].'\',\''.htmlspecialchars($se[$i]["name"]).'\')" title="ทิ้ง">🗑</a>
					'.$ed.'
					</td>
				</tr>';
		}
		echo '</table></form>';
	}
	private function getAllProp():array{
		$re=[];
		$sql=[];
		$sql["get"]="SELECT * FROM `prop` ORDER BY `id` DESC";
		$se=$this->metMnSql($sql,["get"]);
		if($se["result"]){
			$re=$se["data"]["get"];
		}
		return $re;
	}
}
?>
