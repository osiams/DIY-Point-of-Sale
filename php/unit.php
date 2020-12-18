<?php
class unit extends main{
	public function __construct(){
		parent::__construct();
	}
	public function run(){
		$q=["regis","edit","delete"];
		$this->addDir("?a=unit","หน่วยสินค้า");
		if(isset($_GET["b"])&&in_array($_GET["b"],$q)){
			$t=$_GET["b"];
			if($t=="regis"){
				$this->regisUnit();
			}else if($t=="edit"){
				$this->editUnit();
			}else if($t=="delete"){
				$this->deleteUnit();
			}
		}else{
			$this->pageUnit();
		}
	}
	private function deleteUnit():void{
		if(isset($_POST["sku_root"])){
			$sku_root=$this->getStringSqlSet($_POST["sku_root"]);
			$sql=[];
			$sql["del"]="DELETE FROM `unit` WHERE `sku_root`=".$sku_root." AND  `sku_root` != \"defaultroot\"";
			$this->metMnSql($sql,[]);
			header('Location:?a=unit&ed='.$_POST["sku_root"]);
		}
	}
	protected function editUnit():void{
		$error="";
		if(isset($_POST["submit"])&&$_POST["submit"]=="clicksubmit"){
			$se=$this->checkSet("unit",["post"=>["name","sku","sku_root"]],"post");
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				 $qe=$this->editUnitUpdate();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					 //print_r($qe);
					header('Location:?a=unit&ed='.$_POST["sku_root"]);
				}
			}
			if($error!=""){
				$this->editUnitPage($error);
			}
		}else{
			$sku_root=(isset($_POST["sku_root"]))?$_POST["sku_root"]:"";
			$this->editUnitSetCurent($sku_root);
			$this->editUnitPage($error);
		}
	}
	private function editUnitUpdate():array{
		$name=$this->getStringSqlSet($_POST["name"]);
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$skuk=$this->key("key",7);
		$sku_key=$this->getStringSqlSet($skuk);
		$sku_root=$this->getStringSqlSet($_POST["sku_root"]);
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@count_name:=(SELECT COUNT(`id`)  FROM `unit` WHERE `name`=".$name." AND `sku_root` !=".$sku_root."),
			@count_sku:=(SELECT COUNT(`id`)   FROM `unit` WHERE `sku`=".$sku." AND `sku_root` !=".$sku_root.");
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
				UPDATE `unit` SET  `sku`=".$sku.",`sku_key`=".$sku_key.",  `name`= ".$name." WHERE `sku_root`=".$sku_root.";
				SET @result=1;
			END IF;
		";
		$sql["ref"]=$this->ref("unit","sku_key",$skuk);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($sql);
		return $se;
	}
	protected function editUnitSetCurent(string $sku_root):void{
		$od=$this->editUnitOldData($sku_root);
		$fl=["sku","name"];
		foreach($fl as $v){
			if(isset($od[$v])){
				$_POST[$v]=$od[$v];
			}
		}
	}
	protected function editUnitOldData(string $sku_root):array{
		$sku_root=$this->getStringSqlSet($sku_root);
		$sql=[];
		$sql["result"]="SELECT `name`,`sku` FROM `unit` WHERE `sku_root`=".$sku_root."";
		$re=$this->metMnSql($sql,["result"]);
		if(isset($re["data"]["result"][0])){
			return $re["data"]["result"][0];
		}
		return [];
	}
	protected function editUnitPage(string $error):void{
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$sku=(isset($_POST["sku"]))?htmlspecialchars($_POST["sku"]):"";
		$sku_root=(isset($_POST["sku_root"]))?htmlspecialchars($_POST["sku_root"]):"";
		$this->addDir("","แก้ไขหน่วยสินค้า ".$name);
		$this->pageHead(["title"=>"แก้ไขหน่วยสินค้า DIYPOS"]);
		echo '<div class="content">
			<div class="form">
				<h1 class="c">แก้ไขหน่วยสินค้า</h1>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}		
		echo '		<form method="post">
					<input type="hidden" name="submit" value="clicksubmit" />
					<input type="hidden" name="sku_root" value="'.$sku_root.'" />
					<p><label for="unit_name">ชื่อ</label></p>
					<div><input id="unit_name" class="want" type="text" name="name" value="'.$name.'" autocomplete="off" /></div>
					<p><label for="unit_sku">รหัสภายใน</label></p>
					<div><input id="unit_sku" type="text" value="'.$sku.'"  name="sku"  autocomplete="off" /></div>
					<br />
					<input type="submit" value="แก้ไข" />
				</form>
			</div>
		</div>';
	}
	protected function regisUnit():void{
		$error="";
		if(isset($_POST["submit"])&&$_POST["submit"]=="clicksubmit"){
			$se=$this->checkSet("unit",["post"=>["name","sku"]],"post");
			//print_r($se);
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				 $qe=$this->regisUnitInsert();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					 //print_r($qe);
					header('Location:?a=unit&ed='.$qe["data"]["result"][0]["sku_root"]);
				}
			}
			if($error!=""){
				$this->regisUnitPage($error);
			}
		}else{
			$this->regisUnitPage($error);
		}
	}
	protected function regisUnitInsert():array{
		$name=$this->getStringSqlSet($_POST["name"]);
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$skuk=$this->key("key",7);
		$sku_root=$this->getStringSqlSet($skuk);
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@sku_root:=".$sku_root.",
			@count_name:=(SELECT COUNT(`id`)  FROM `unit` WHERE `name`=".$name."),
			@count_sku:=(SELECT COUNT(`id`)   FROM `unit` WHERE `sku`=".$sku.");
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
				INSERT INTO `unit`  (`sku`,`sku_key`,`sku_root`,`name`) VALUES (".$sku.",".$sku_root.",".$sku_root.",".$name.");
				SET @result=1;
			END IF;
		";
		$sql["ref"]=$this->ref("unit","sku_key",$skuk);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@sku_root AS `sku_root`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($sql);
		return $se;
	}
	protected function regisUnitPage(string $error):void{
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$sku=(isset($_POST["sku"]))?htmlspecialchars($_POST["sku"]):"";
		$this->addDir("?a=unit&amp;b=regis","เพิ่มหน่วยสินค้า");
		$this->pageHead(["title"=>"เพิ่มหน่วยสินค้า DIYPOS"]);
		echo '<div class="content">
			<div class="form">
				<h1 class="c">เพิ่มหน่วยสินค้า</h1>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}		
		echo '		<form method="post">
					<input type="hidden" name="submit" value="clicksubmit" />
					<p><label for="unit_name">ชื่อ</label></p>
					<div><input id="unit_name" class="want" type="text" name="name" value="'.$name.'" autocomplete="off" /></div>
					<p><label for="unit_sku">รหัสภายใน</label></p>
					<div><input id="unit_sku" type="text" value="'.$sku.'"  name="sku" autocomplete="off"  /></div>
					<br />
					<input type="submit" value="เพิ่ม" />
				</form>
			</div>
		</div>';
		$this->pageFoot();
	}
	protected function regisUnitCheck():array{
		
	}
	protected function pageUnit(){
		$this->pageHead(["title"=>"หน่วยสินค้า DIYPOS"]);
			echo '<div class="content">
				<div class="form">
					<h1 class="c">หน่วยสินค้าสินค้า</h1>';
			$this->writeContentUnit();
			echo '<br /><p class="c"><input type="button" value="เพิ่มหน่วยสินค้า" onclick="location.href=\'?a=unit&b=regis\'" /></p>';
		$this->pageFoot();
	}
	protected function writeContentUnit():void{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$se=$this->getAllUnit();
		echo '<form class="form100" name="unit" method="post">
			<input type="hidden" name="sku_root" value="" />
			<table><tr><th>ที่</th>
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
			echo '<tr'.$cm.'><td class="r">'.$se[$i]["id"].'</td>
				<td class="l">'.$se[$i]["sku"].'</td>
				<td class="l">'.htmlspecialchars($se[$i]["name"]).'</td>
				<td class="action">
					<a onclick="G.unitEdit(\''.$se[$i]["sku_root"].'\')" title="แก้ไข">📝</a>
					<a onclick="G.unitDelete(\''.$se[$i]["sku_root"].'\',\''.htmlspecialchars($se[$i]["name"]).'\')" title="ทิ้ง">🗑</a>
					'.$ed.'
					</td>
				</tr>';
		}
		echo '</table></form>';
	}
	protected function getAllUnit():array{
		$re=[];
		$sql=[];
		$sql["get"]="SELECT * FROM `unit` ORDER BY `id` DESC";
		$se=$this->metMnSql($sql,["get"]);
		if($se["result"]){
			$re=$se["data"]["get"];
		}
		return $re;
	}
}
?>
