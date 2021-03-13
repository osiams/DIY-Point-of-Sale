<?php
class product extends main{
	public function __construct(){
		parent::__construct();
		$this->prop_ = NULL;
		$this->prop_list = NULL;
		$this->group_ = NULL;
		$this->group_list =[];
		#############
		$this->setDir();
		$this->select=[];
		$this->per=10;
		$this->page=1;
		$this->txsearch="";
		$this->fl="";
		$this->lid=0;
		$this->sh="";
		
		$this->form_pn=null;
	}
	public function run(){
		$this->page=$this->setPageR();
		$q=["regis","edit","delete","in","select","details"];
		if(isset($_GET["b"])&&in_array($_GET["b"],$q)){
			$this->getSelect();
			$t=$_GET["b"];
			if($t == "regis" ||$t == "edit"){
				$this->loadGroupAndProp();				
			}
			if($t=="regis"||$t=="edit"){
				$file = "php/form_selects.php";
				require($file);			
			}
			if($t=="regis"){
				$this->regisProduct();
			}else if($t=="edit"){
				$this->editProduct();
			}else if($t=="delete"){
				$this->deleteProduct();
			}else if($t=="select"){
				$for=["billsin","sell","label","itmw"];
				if(isset($_GET["for"])&&in_array($_GET["for"],$for)){
					$this->selectProductPage($_GET["for"]);
				}
			}else if($t=="details"){
				require_once("php/product_".$_GET["b"].".php");
				eval("(new product_".$_GET["b"]."())->run();");
			}else if($t=="delete"){
				$this->deleteProduct();
			}
		}else{
			$this->loadGroupAndProp();	
			$this->pageProduct();
		}
	}
	protected function loadGroupAndProp():void{
		$this->prop_ = new prop();
		$this->prop_list = $this->prop_->get("all_list_key_value");
		$this->group_ = new group();
		$this->group_list = $this->group_->get("all_list_key_value");
	}
	private function setDir():void{
		$this->addDir("?a=product","สินค้า");
	}
	public function fetch(){	
		$re=["result"=>false,"message_error"=>""];
		$sku_root=(isset($_POST["sku_root"]))?$_POST["sku_root"]:"";
		$y=(isset($_POST["y"]))?$_POST["y"]:"";
		if(isset($_POST["b"])){
			if($_POST["b"]=="stat"){
				$se=$this->statCheck($sku_root,$y);
				if(!$se["r"]){
					$re["message_error"]=$se["em"];
				}else{
					//print_r($se);
					$se=$this->statUpdate($sku_root,$se["pdstat"],$se["statnote"]);
					if($se){
						$re["result"]=true;
					}
				}
			}else if($_POST["b"]=="checkunq"){
			
					if(isset($_POST["key"])&&strlen(trim($_POST["key"]))!=0
						&&isset($_POST["val"])&&strlen(trim($_POST["val"]))!=0
						&&($_POST["key"]=="barcode"||$_POST["key"]=="sku")){
							$se=$this->fetchCheckUnq($_POST["key"],$_POST["val"]);
							if($se){
								$re["message_error"]="\n\"".$_POST["val"]."\" มีแล้ว";
								$re["result"]=true;
							}else{
								$re["result"]=false;
							}
					}
			}
		}
		header('Content-type: application/json');
		echo json_encode($re);

	}
	private function fetchCheckUnq(string $fil,string $val):bool{
		$fil=$this->getStringSqlSet($fil);
		$val=$this->getStringSqlSet($val);
		$fil=substr($fil,1,-1);
		$re=false;
		$sql=[];
		$sql["check"]="SELECT  count(*) AS `count`  FROM product WHERE `".$fil."`=".$val." LIMIT 1;";
		$se=$this->metMnSql($sql,["check"]);
		
		if(isset($se["data"]["check"][0]["count"])){
			if($se["data"]["check"][0]["count"]==1){
				$re=true;
			}
		}
		return $re;
	}
	private function statUpdate(string $sku_root,string $pdstat, string $statnote):bool{
		$pdstat=$this->getStringSqlSet($pdstat);
		$statnote=$this->getStringSqlSet($statnote);
		$re=false;
		$sql=[];
		$sql["set"]="SELECT @result:=0,@skukey:='';";
		$sql["run"]="BEGIN NOT ATOMIC
			SET @k=KEY_();
			SET @skukey=(SELECT sku_key FROM product WHERE sku_root='".$sku_root."');
			IF @skukey!='' THEN
				UPDATE product SET sku_key=@k,pdstat=".$pdstat.",statnote=".$statnote." WHERE sku_root='".$sku_root."';
				INSERT IGNORE INTO `product_ref`SELECT * FROM `product` WHERE  `product`.`sku_key`=@k;
				SET @result=1;
			END IF;
		END;";
		$sql["result"]="SELECT @result AS `result`;";
		$se=$this->metMnSql($sql,["result"]);
			//print_r($se);
		if(isset($se["data"]["result"][0]["result"])&&(int) $se["data"]["result"][0]["result"]==1){
			$re=true;
		}
		return $re;
	}
	private function statCheck(string $sku_root,string $y):array{
		$re=["r"=>false,"em"=>"","pdstat"=>"","statnote"=>""];
		$y=trim($y);
		$s=substr($y,0,1);
		$n=mb_substr($y,2,strlen($y));
		if(!$this->isSKU($sku_root)){
			$re["em"]="รหัสรากสินค้าไม่ถูกต้อง่";
		}else if($s!="b"&&$s!="r"&&$s!="y"&&$s!="c"){
			$re["em"]="ค่า \"".htmlspecialchars($y)."\" ไม่ถูกต้อง";
		}else{
			$re["r"]=true;
			$re["pdstat"]=$s;
			$re["statnote"]=mb_substr($n,0,83);
		}
		return $re;
	}
	private function selectProductPage(string $for=null){
		$sea=$this->getAllProduct($for);
		//print_r($sea);
		$se=$sea["row"];
		$this->pageHead(["title"=>"สินค้า DIYPOS","dir"=>false,"css"=>["product"]]);
		echo '<div class="content">
			<div>';
		echo '<form class="form100"  name="product" method="post">
			<input type="hidden" name="sku_root" value="" />
			<table  class="select">';
		for($i=0;$i<count($se);$i++){
			$nm=htmlspecialchars($se[$i]["name"]);
			$kbc=htmlspecialchars($se[$i]["barcode"]);
			if($this->fl=="name"){
				$nm=str_replace($this->txsearch,'<span class="bgyl">'.$this->txsearch.'</span>',$nm);
			}else if($this->fl=="barcode"){
				$kbc=str_replace($this->txsearch,'<span class="bgyl">'.$this->txsearch.'</span>',$kbc);
			}
			echo '<tr><td>'.($i+1).'.</td>
				<td class="l"><p>'.$nm.'</p><p class="p_bc"><span class="pwlv">'.$this->s_type[$se[$i]["s_type"]]["icon"].'</span> ';
			$bcsku="";	
			$bc=0;
			if(strlen($se[$i]["barcode"])>0){
				$bc=1;
				$bcsku.=$kbc;
			}	
			
			if(strlen($se[$i]["sku"])>0){
				$cm=($bc==1)?" , ":"";
				$bcsku.=$cm."".$se[$i]["sku"];
			}	
			echo $bcsku.'</p></td>
				<td class="r">'.($se[$i]["balance"]*1).'</td>
				<td class="l">'.$se[$i]["unit_name"].'</td>
				<td class="action c">';
			if($for=="billsin"){
				$namejs=$se[$i]["name"];
				$namejs=str_replace("\\","&#92;&#92;&#92;&#92;",$namejs);
				$namejs=str_replace("\"","\\&quot;",$namejs);		
				$namejs=str_replace("'","&#92;&#39;",$namejs);				

				$dt=json_encode([
					"sku_root"=>$se[$i]["sku_root"],
					"bcsku"=>$se[$i]["barcode"],
					"name"=>$namejs,
					"unit"=>$se[$i]["unit_name"],
					"s_type"=>$se[$i]["s_type"],
					"price"=>$se[$i]["price"]*1,
					"cost"=>$se[$i]["cost"]*1]);
				$dt=str_replace("\"","&quot;",$dt);
				$dt=str_replace("'","&apos;",$dt);
				//print_r($se[$i]["name"]);
				echo '	<a class="userselectnone" onclick="window.parent.tran(\'Bi\',\'productSelect\',\''.$dt.'\')" title="เลือก">⬆️</a>';
			}else if($for=="sell"){
				$dt=json_encode(["sku_root"=>$se[$i]["sku_root"]]);
				$dt=str_replace("\"","&quot;",$dt);
				echo '	<a class="userselectnone"  onclick="window.parent.tran(\'S\',\'productSelect\',\''.$dt.'\')" title="เลือก">⬆️</a>';
			}else if($for=="label"||$for=="itmw"){
				$for_id=(isset($_GET["for_id"]))?$_GET["for_id"]:"";
				$for_id=$this-> jsD($for_id);
				$name=$this-> jsD($se[$i]["name"]);
				$s_type=$this-> jsD($se[$i]["s_type"]);
				$unit=$this-> jsD($se[$i]["unit_name"]);
				$dt=json_encode(["sku_root"=>$se[$i]["sku_root"],"barcode"=>$se[$i]["barcode"],"name"=>$name,"s_type"=>$s_type,"price"=>$se[$i]["price"],"unit"=>$unit,"for_id"=>$for_id]);
				$dt=str_replace("\"","&quot;",$dt);
				$its="Bc";
				if($for=="itmw"){
					$its="It";
				}
				echo '	<a class="userselectnone"  onclick="window.parent.tran(\''.$its.'\',\'productSelect\',\''.$dt.'\')" title="เลือก">⬆️</a>';
			}
			echo '	</td>
			</tr>';
		}
		echo '</table></form>';
		echo '<p class="c gray555">แสดงรายการสูงสุดที่ 20 รายการ</p>';
		$this->pageFoot();
	}
	private function  jsD(string  $t):string{
		$t=str_replace('\\','&amp;#47;',$t);
		$t=str_replace("\"","&amp;quot;",$t);
		$t=str_replace("'","&amp;#39;",$t);
		$t=str_replace("<","&amp;lt;",$t);
		$t=str_replace(">","&amp;gt;",$t);
		$t=str_replace("\n","",$t);
		return $t;
	}
	private function deleteProduct():void{
		if(isset($_POST["sku_root"])){
			$sku_root=$this->getStringSqlSet($_POST["sku_root"]);
			$sql=[];
			$sql["del"]="DELETE FROM `product` WHERE `sku_root`=".$sku_root."";
			$sql["del_balance"]="UPDATE bill_in_list SET balance=0 WHERE  `product_sku_root`=".$sku_root." AND balance > 0";
			$se=$this->metMnSql($sql,[]);
			//print_r($se);
			header('Location:?a=product&ed='.$_POST["sku_root"]);
		}
	}
	private function editProduct():void{
		$error="";
		if(isset($_POST["submit"])&&$_POST["submit"]=="clicksubmit"){
			$_POST["partner"]=isset($_POST["partner_list"])?$_POST["partner_list"]:"";
			$se=$this->checkSet("product",["post"=>["name","sku","barcode","price","cost","unit","group_root"]],"post");
			$ckp = $this->checkValidateProp();
			if(!$se["result"]){
				$error=$se["message_error"];
			}else if($ckp != ""){
				$error=$ckp;
			}else{
				 $qe=$this->editProductUpdate();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					 //print_r($qe);
					header('Location:?a=product&ed='.$_POST["sku_root"]);
				}
			}
			if($error!=""){
				$this->editProductPage($error);
			}
		}else{
			$sku_root=(isset($_POST["sku_root"]))?$_POST["sku_root"]:"";
			$this->editProductSetCurent($sku_root);
			$this->editProductPage($error);
		}
	}
	private function editProductUpdate():array{
		$name=$this->getStringSqlSet($_POST["name"]);
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$barcode=$this->getStringSqlSet($_POST["barcode"]);
		$price=(strlen(trim($_POST["price"]))>0)?$_POST["price"]:"NULL";
		$cost=(strlen(trim($_POST["cost"]))>0)?$_POST["cost"]:"NULL";
		$unit=$this->getStringSqlSet($_POST["unit"]);
		$s_type=$this->getStringSqlSet($_POST["s_type"]);
		$group_root=$this->getStringSqlSet($_POST["group_root"]);
		$skuk=$this->key("key",7);
		$sku_key=$this->getStringSqlSet($skuk);
		$sku_root=$this->getStringSqlSet($_POST["sku_root"]);
		$props = $this->getStringSqlSet($this->setPropR());
		$pn=$this->setPartnerR($_POST["partner"]);
		$partner = $this->getStringSqlSet($pn);
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@group_root:=".$group_root.",
			@props:=".$props.",
			@count_name:=(SELECT COUNT(`id`)  FROM `unit` WHERE `name`=".$name." AND `sku_root` !=".$sku_root."),
			@count_sku:=(SELECT COUNT(`id`)   FROM `unit` WHERE `sku`=".$sku." AND `sku_root` !=".$sku_root."),
			@count_barcode:=(SELECT COUNT(`id`)   FROM `product` WHERE `barcode`=".$barcode." AND `sku_root` !=".$sku_root."),
			@count_unit:=(SELECT COUNT(`id`)   FROM `unit` WHERE `sku_root`=".$unit."),
			@group_key:=(SELECT IFNULL((SELECT IFNULL(`sku_key`,\"defaultroot\") AS `sku_key`   FROM `group` WHERE `sku_root`=".$group_root."),\"defaultroot\") AS `sku_key`);
		";
		$sql["check"]="
			IF @count_name > 0 THEN 
				SET @message_error='เกิดขอผิดพลาด ชื่อที่แก้ไขมา มีแล้ว โปรดลองชื่ออื่น';
			ELSEIF @count_sku > 0 THEN
				SET @message_error='เกิดขอผิดพลาด รหัสภายในทแก้ไขมา มีแล้ว โปรดลอง รหัสภายในอื่น';
			ELSEIF @count_barcode > 0 THEN
				SET @message_error='เกิดขอผิดพลาด รหัสแท่งที่ส่งมา มีแล้ว โปรดลอง รหัสแท่งอื่น';
			ELSEIF @count_unit = 0 THEN
				SET @message_error='เกิดขอผิดพลาด หน่วยที่ส่งมา ไม่มีในระบบ โปรดลอง หน่วยอื่น' ; 
			END IF;			
		";
		$sql["run"]="
			IF LENGTH(@message_error) = 0 THEN
				UPDATE `product` SET  `sku`=".$sku.",`sku_key`=".$sku_key.",  `name`= ".$name." ,  `barcode`=".$barcode.", `price`=".$price.",`s_type`=".$s_type.",
					 `cost`=".$cost.", `unit`=".$unit." ,`group_key` = @group_key,`group_root` = @group_root,`props` = JSON_MERGE_PATCH(`props`,@props),
					 `partner`=".$partner." 
				WHERE `sku_root`=".$sku_root.";
				SET @result=1;
			END IF;
		";
		$sql["ref"]=$this->ref("product","sku_key",$skuk);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($sql);
		return $se;
	}
	private function editProductPage($error){
		$sku_root=(isset($_POST["sku_root"]))?$_POST["sku_root"]:"";
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$sku=(isset($_POST["sku"]))?htmlspecialchars($_POST["sku"]):"";
		$barcode=(isset($_POST["barcode"]))?htmlspecialchars($_POST["barcode"]):"";
		$price=(isset($_POST["price"]))?htmlspecialchars($_POST["price"]):"";
		$cost=(isset($_POST["cost"]))?htmlspecialchars($_POST["cost"]):"";
		$unit=(isset($_POST["unit"]))?htmlspecialchars($_POST["unit"]):"";
		$partner=(isset($_POST["partner"]))?htmlspecialchars($_POST["partner"]):"";
		$s_type=(isset($_POST["s_type"]))?$_POST["s_type"]:"p";
		$group = "defaultroot";
		//echo $_POST["group_root"];
		if(isset($_POST["group_root"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_POST["group_root"])){
			$group = $_POST["group_root"];
		}
		$sku_root=(isset($_POST["sku_root"]))?htmlspecialchars($_POST["sku_root"]):"";
		$this->addDir("","แก้ไข สินค้า ".$name);
		$this->pageHead(["title"=>"แก้ไขสินค้า DIYPOS","js"=>["product","Pd","form_selects","Fsl"],"run"=>["Fsl"],"css"=>["form_selects"]]);
			echo '<div class="content">
				<div class="form">
					<h1 class="c">แก้ไขสินค้า</h1>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}		
		$partner_list_id=$this->key("key",7);
		echo '			<form  name="edit_product" method="post">
						<input type="hidden" name="submit" value="clicksubmit" />
						<input type="hidden" name="sku_root" value="'.$sku_root.'" />
						<input type="hidden" id="'.$partner_list_id.'" name="partner_list" value="'.$partner.'" />
						<p><label for="product_regis_name">ชื่อสินค้า</label></p>
						<div><input id="product_regis_name" type="text" name="name" value="'.$name.'" class="want" /></div>
						<p><label for="product_regis_barcode">รหัสแท่ง</label></p>
						<div><input id="product_regis_barcode" type="text" name="barcode" value="'.$barcode.'"  /></div>
						<p><label for="product_regis_sku">รหัสภายใน</label></p>
						<div><input id="product_regis_sku" type="text" value="'.$sku.'"  name="sku"  /></div>
						<p><label for="product_regis_price">ราคา</label></p>
						<div><input id="product_regis_price" type="text" value="'.$price.'"  name="price"  /></div>
						<p><label for="product_regis_cost">ต้นทุน</label></p>
						<div><input id="product_regis_cost" type="text" value="'.$cost.'"  name="cost"  /></div>';
			$this->writeStype($s_type);
			echo '<p><label for="product_regis_unit">หน่วย</label></p>
							<div><select id="product_regis_unit" name="unit">';
							$this->writeSelectOption("unit",$unit,$s_type);
			echo '</select></div>';		
			
			echo '<p><label for="product_regis_cost">คู่ค้า</label></p>
				<div>';
			$this->form_pn=new form_selects("partner","คู่ค้า","edit_product",$this->key("key",7),$partner_list_id);	
			$this->form_pn->writeForm("edit_product");
			echo '</div>';
			
			echo '<p><label for="product_regis_unit">กลุ่ม</label></p>
							<div>';
							$this->group_->writeSelectGroup($group);
			echo '</div>';
			echo '			<br />
						<input type="submit" value="แก้ไขสินค้า" />
					</form>
				</div>
			</div>';
		$this->pageFoot();	
	}
	private function regisProduct():void{
		$error="";
		if(isset($_POST["submitt"])&&$_POST["submitt"]=="clicksubmit"){
			$_POST["partner"]=isset($_POST["partner_list"])?$_POST["partner_list"]:"";
			$se=$this->checkSet("product",["post"=>["barcode","sku","name","price","cost","unit","group_root","partner"]],"post");
			//print_r($se);exit;
			$ckp = $this->checkValidateProp();
			if(!$se["result"]){
				$error=$se["message_error"];
			}else if($ckp != ""){
				$error=$ckp;
			}else{
				 $qe=$this->regisProductInsert();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					 //print_r($qe);
					header('Location:?a=product&ed='.$qe["data"]["result"][0]["sku_root"]);
				}
			}
			if($error!=""){
				$this->regisProductPage($error);
			}
		}else{
			$this->regisProductPage($error);
		}
	}
	private function checkValidateProp():string{
		$re = "";
		foreach($_POST as $k=>$v){
			if(substr($k,0,5) == "prop_"){
				$kr = substr($k,5);
				$se = $this->prop_->validate($kr,$v);
				if($se != ""){
					$re = $se;
					break;
				}
			}
		}
		return $re;
	}
	private function regisProductInsert():array{
		
		$name=$this->getStringSqlSet($_POST["name"]);
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$barcode=$this->getStringSqlSet($_POST["barcode"]);
		$price=(strlen(trim($_POST["price"]))>0)?$_POST["price"]:"NULL";
		$cost=(strlen(trim($_POST["cost"]))>0)?$_POST["cost"]:"NULL";
		$unit=$this->getStringSqlSet($_POST["unit"]);
		$s_type=$this->getStringSqlSet($_POST["s_type"]);
		$group_root=$this->getStringSqlSet($_POST["group_root"]);
		$skuk=$this->key("key",7);
		$sku_root=$this->getStringSqlSet($skuk);
		$props = $this->getStringSqlSet($this->setPropR());
		$pn=$this->setPartnerR($_POST["partner"]);
		$partner = $this->getStringSqlSet($pn);
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@sku_root:=".$sku_root.",
			@group_root:=".$group_root.",
			@props:=".$props.",
			@count_name:=(SELECT COUNT(`id`)  FROM `product` WHERE `name`=".$name."),
			@count_sku:=(SELECT COUNT(`id`)   FROM `product` WHERE `sku`=".$sku."),
			@count_barcode:=(SELECT COUNT(`id`)   FROM `product` WHERE `barcode`=".$barcode."),
			@count_unit:=(SELECT COUNT(`id`)   FROM `unit` WHERE `sku_root`=".$unit."),
			@group_key:=(SELECT IFNULL((SELECT IFNULL(`sku_key`,\"defaultroot\") AS `sku_key`   FROM `group` WHERE `sku_root`=".$group_root."),\"defaultroot\") AS `sku_key`);
		";
		$sql["check"]="
			IF @count_name > 0 THEN 
				SET @message_error='เกิดขอผิดพลาด ชื่อที่ส่งมา มีแล้ว โปรดลองชื่ออื่น';
			ELSEIF @count_sku > 0 THEN
				SET @message_error='เกิดขอผิดพลาด รหัสภายในที่ส่งมา มีแล้ว โปรดลอง รหัสภายในอื่น';
			ELSEIF @count_barcode > 0 THEN
				SET @message_error='เกิดขอผิดพลาด รหัสแท่งที่ส่งมา มีแล้ว โปรดลอง รหัสแท่งอื่น';
			ELSEIF @count_unit = 0 THEN
				SET @message_error='เกิดขอผิดพลาด หน่วยที่ส่งมา ไม่มีในระบบ โปรดลอง หน่วยอื่น' ; 
			END IF;			
		";
		$sql["run"]="
			IF LENGTH(@message_error) = 0 THEN
				INSERT INTO `product`  (`sku`,`sku_key`,`sku_root`,`barcode`,`name`,`price`,`cost`,`unit`,`group_key`,`group_root`,`props`,`s_type`,`partner`) 
				VALUES (".$sku.",".$sku_root.",".$sku_root.",".$barcode.",".$name.",".$price.",".$cost.",".$unit.",@group_key,@group_root,@props,".$s_type.",".$partner."); 
				SET @result=1;
			END IF;
		";
		$sql["ref"]=$this->ref("product","sku_key",$skuk);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@sku_root AS `sku_root`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($sql);
		return $se;
	}
	private function editProductSetCurent(string $sku_root):void{
		$od=$this->editProductOldData($sku_root);
		$fl=["sku","barcode","name","price","cost","unit","group_root","s_type","partner"];
		foreach($fl as $v){
			if(isset($od[$v])){
				$_POST[$v]=$od[$v];
			}
		}
		$_POST["partner"]=$this->propToFromValue($_POST["partner"]);
		
		if(!isset($od["props"])){
			$od["props"]="[]";
		}
		$props = json_decode($od["props"],true);
		$this->props2PostProp_($props);
	}
	private function props2PostProp_(array $props):void{
		foreach($props as $k=>$v){
			$_POST["prop_".$k]=$v;
		}		
	}
	private function editProductOldData(string $sku_root):array{
		$sku_root=$this->getStringSqlSet($sku_root);
		$sql=[];
		$sql["result"]="SELECT `name`,`sku`,`barcode` ,`price`,`cost`,`unit`,IFNULL(`group_root`,\"defaultroot\") AS `group_root`,
			IFNULL(`props`,\"[]\") AS `props` ,`s_type`,IFNULL(`partner`,\"[]\") AS `partner`
			FROM `product` 
			WHERE `sku_root`=".$sku_root."";
		$re=$this->metMnSql($sql,["result"]);
		
		if(isset($re["data"]["result"][0])){
			return $re["data"]["result"][0];
		}
		
		return [];
	}
	private function regisProductPage($error){
		//$_POST["partner"]=",1614832914JSo91vq,,1614833089Xiir69p,,1614750911CPOeH4Y,,1614820290k7vNQSU,";
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$sku=(isset($_POST["sku"]))?htmlspecialchars($_POST["sku"]):"";
		$barcode=(isset($_POST["barcode"]))?htmlspecialchars($_POST["barcode"]):"";
		$price=(isset($_POST["price"]))?htmlspecialchars($_POST["price"]):"";
		$cost=(isset($_POST["cost"]))?htmlspecialchars($_POST["cost"]):"";
		$unit=(isset($_POST["unit"]))?htmlspecialchars($_POST["unit"]):"";
		$partner=(isset($_POST["partner"]))?htmlspecialchars($_POST["partner"]):"";
		$s_type=(isset($_POST["s_type"]))?$_POST["s_type"]:"p";
		$group = "defaultroot";
		if(isset($_POST["group_root"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_POST["group_root"])){
			$group = $_POST["group_root"];
		}
		if(isset($_POST["sku_root"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_POST["sku_root"])){
			$dt=$this->regisSetDataAs($_POST["sku_root"]);
			$group=$dt["group_root"];
			$this->prop_ ->editPropSetCurent($_POST["sku_root"]);
			$props = json_decode($dt["props"],true);
			$this->props2PostProp_($props);
			//print_r($_POST);
			if(count($dt)>0){
				$name=htmlspecialchars($dt["name"]);
				$sku=htmlspecialchars($dt["sku"]);
				$barcode=htmlspecialchars($dt["barcode"]);
				$price=$dt["price"];
				$cost=$dt["cost"];
				$unit=htmlspecialchars($dt["unit"]);
			}
		}
		$this->addDir("?a=product&amp;b=regis","ลงทะเบียนสินค้า");
		$this->pageHead(["title"=>"ลงทะเบียนสินค้า DIYPOS","js"=>["product","Pd","form_selects","Fsl"],"run"=>["Fsl"],"css"=>["form_selects"]]);
			echo '<div class="content">
				<div class="form">
					<h1 class="c">ลงทะเบียนสินค้า</h1>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}	
		$partner_list_id=$this->key("key",7);
		echo '			<form  id="product_regisq" name="regis_product" method="post" action="">
						<input type="hidden" name="submitt" value="clicksubmit" />
						<input type="hidden" id="'.$partner_list_id.'" name="partner_list" value="'.$partner.'" />
						<p><label for="product_regis_name">ชื่อสินค้า</label></p>
						<div><input id="product_regis_name" type="text" name="name" value="'.$name.'" class="want" /></div>
						<p><label for="product_regis_barcode">รหัสแท่ง</label></p>
						<div><input id="product_regis_barcode" type="text" name="barcode" value="'.$barcode.'" onblur="Pd.checkUnq(this,\'barcode\')"  /></div>
						<p><label for="product_regis_sku">รหัสภายใน</label></p>
						<div><input id="product_regis_sku" type="text" value="'.$sku.'"  name="sku"  /></div>
						<p><label for="product_regis_price">ราคา</label></p>
						<div><input id="product_regis_price" type="text" value="'.$price.'"  name="price"  /></div>
						<p><label for="product_regis_cost">ต้นทุน</label></p>
						<div><input id="product_regis_cost" type="text" value="'.$cost.'"  name="cost"  /></div>';
			$this->writeStype($s_type);
			echo '<p><label for="product_regis_unit">หน่วย</label></p>
							<div><select id="product_regis_unit" name="unit">';
							$this->writeSelectOption("unit",$unit,$s_type);
			echo '</select></div>';	
			
			echo '<p><label for="product_regis_cost">คู่ค้า</label></p>
				<div>';
			$this->form_pn=new form_selects("partner","คู่ค้า","regis_product",$this->key("key",7),$partner_list_id);	
			$this->form_pn->writeForm("regis_product");
			echo '</div>';
			
			echo '<p><label for="product_regis_unit">กลุ่ม</label></p>
							<div>';
							$this->group_->writeSelectGroup($group);
			echo '</div>';
			echo '			<br />
						<input type="button" value="ลงทะเบียนสินค้า" onclick="document.getElementById(\'product_regis_barcode\').blur();Pd.regisSubmit()"  />
					</form>
				</div>
			</div>';
		$this->pageFoot();	
	}
	private function writeStype(string $s_type="p"):void{
		echo '<p><label for="product_regis_unit">รูปแบบการขาย</label></p>
							<div>
								<select id="product_regis_s_type" name="s_type" onchange="Pd.setUnitSelect(this)">
									<option value="p"'.($s_type=="p"?" selected":"").'>'.$this->s_type["p"]["icon"].' '.$this->s_type["p"]["desc"].'</option>
									<option value="w"'.($s_type=="w"?" selected":"").'>'.$this->s_type["w"]["icon"].' '.$this->s_type["w"]["desc"].'</option>
									<option value="l"'.($s_type=="l"?" selected":"").'>'.$this->s_type["l"]["icon"].' '.$this->s_type["l"]["desc"].'</option>
									<option value="v"'.($s_type=="v"?" selected":"").'>'.$this->s_type["v"]["icon"].' '.$this->s_type["v"]["desc"].'</option>
								</select>
							</div>
		';
	}
	private function regisSetDataAs(string $sku_root):array{
		$re=[];
		$sql=[];
		$sql["get"]="SELECT name,sku,barcode,cost,price,unit,IFNULL(group_root,\"defaultroot\") AS group_root,IFNULL(props,\"[]\") AS props FROM product WHERE sku_root='".$sku_root."' LIMIT 1";
		$se=$this->metMnSql($sql,["get"]);
		//print_r($se);
		if($se["result"]){
			$re=$se["data"]["get"][0];
		}
		return $re;
	}
	private function writeSelectOption(string $table,string $sku_root,string $s_type):void{
		$ho="";
		$n_ho=0;
		$type=["p"=>"อัน","w"=>"น้ำหนัก","l"=>"ความยาว","v"=>"ปริมาตร"];
		foreach($this->select[$table] as $k => $v){
			$txh="";
			if($v["s_type"]!=$ho){
				$ho = $v["s_type"];
				$n_ho += 1;
				$txh = ' <optgroup data-s_type="'.$v["s_type"].'" label="'.$type[$v["s_type"]].'"'.($s_type!=$v["s_type"]?" hidden":"").'>';
				if($n_ho > 1){
					$txh = '</optionfroup>'.$txh;
				}
			}
			$sel=($v["sku_root"]==$sku_root)?' selected="selected"':'';
			echo $txh;
			echo '<option data-s_type="'.$v["s_type"].'" value="'.$v["sku_root"].'"'.$sel.'>'.$v["name"].'</option>';
		}
		echo '</optionfroup>';
	}
	private function getSelect():void{
		$sql=[];
		$sql["unit"]="SELECT * FROM `unit` ORDER BY `s_type` ASC,`name` ASC";
		$se=$this->metMnSql($sql,["unit"]);
		if($se["result"]){
			$this->select["unit"]=$se["data"]["unit"];
		}
	}
	private function pageProduct(){
		$this->defaultPageSearch();
		$this->pageHead(["title"=>"สินค้า DIYPOS","css"=>["product"],"js"=>["product","Pd"]]);
			echo '<div class="content">
				<div class="form">
					<h1 class="c">สินค้า</h1>
					<div class="pd_search">
						<form class="form100" name="pd_search" action="" method="get">
							<input type="hidden" name="a" value="product" />
							<input type="hidden" name="lid" value="0" />
							<label><select id="product_search_fl" name="fl">
								<option value="name"'.(($this->fl=="name")?" selected":"").'>ชื่อ</option>
								<option value="barcode"'.(($this->fl=="barcode")?" selected":"").'>รหัสแท่ง</option>
								<option value="sku"'.(($this->fl=="sku")?" selected":"").'>รหัสภายใน</option>
							</select>
							 <input id="product_search_tx" type="text" name="tx" value="'.str_replace("\\","",htmlspecialchars($this->txsearch)).'" />
							 <input  type="submit" value="🔍" /> </label></form>
					</div>';
			$this->writeContentProduct();
			echo '<br /><p class="c">
				
				<input type="button" value="ลงทะเบียนสินค้าใหม่" onclick="location.href=\'?a=product&b=regis\'" />
			</p>';
			$this->pageFoot();
	}
	private function writeContentProduct():void{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$sea=$this->getAllProduct();
		$se=$sea["row"];
		echo '<form class="form100"  name="product" method="post">
			<input type="hidden" name="sku_root" value="" />
			<table id="product"><tr><th>ที่</th>
			<th>ป.</th>			
			<th>รหัสภายใน</th>
			<th>รหัสแท่ง</th>

			<th>ชื่อ</th>
			<th>ราคา</th>
			<th>ต้นทุน</th>
			<th>จำนวน</th>
			<th>หน่วย</th>
			<th>กระทำ</th>
			</tr>';
		//$nl=(count($se)>$this->per)?$this->per:count($se);
		for($i=0;$i<count($se);$i++){
			$ed='';
			if($se[$i]["sku_root"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}
			$cm=($i%2!=0)?" class=\"i2\"":"";
			$name=htmlspecialchars($se[$i]["name"]);
			$barcode=$se[$i]["barcode"];
			$sku=$se[$i]["sku"];
			if($this->txsearch!=""){
				$ts=htmlspecialchars(str_replace("\\","",$this->txsearch));
				if($this->fl=="name"){
					$name=str_replace($ts,'<span class="bgyl">'.$ts.'</span>',$name);
				}else if($this->fl=="barcode"){
					$barcode=str_replace($ts,'<span class="bgyl">'.$ts.'</span>',$barcode);
				}else if($this->fl=="sku"){
					$sku=str_replace($ts,'<span class="bgyl">'.$ts.'</span>',$sku);
				}
				
			}
			$stat="";
			if($se[$i]["pdstat"]=="b"){
				$stat=" ⬛";
			}else if($se[$i]["pdstat"]=="r"){
				$stat=" 🟥";
			}else if($se[$i]["pdstat"]=="y"){
				$stat=" 🟨";
			}
			$namejs=htmlspecialchars(trim($se[$i]["name"]));
			$namejs=str_replace("\\","\\\\",$namejs);
			$namejs=str_replace("'","\'",$namejs);
			echo '<tr'.$cm.'><td>'.$se[$i]["id"].'</td>
				<td class="pwlv">'.$this->s_type[$se[$i]["s_type"]]["icon"].'</td>
				<td class="l">'.$sku.'</td>
				<td class="l">'.$barcode.'</td>
				
				<td class="l">
					<div><a href="?a=product&amp;b=details&amp;sku_root='.$se[$i]["sku_root"].'">'.$name.'</a>'.$stat.'</div>
					<div>'.$sku.','.$barcode.'</div>
					<div>';
			$pn_arr=json_decode($se[$i]["partner"],true);	
			$this->writePartnerList($pn_arr);		
			echo $this->writeDirGroup($se[$i]["group_root"],[$se[$i]["d1"],$se[$i]["d2"],$se[$i]["d3"],$se[$i]["d4"]]).'</div>
				</td>
				<td class="r">
					<div>'.number_format($se[$i]["price"],2,'.',',').'</div>
					<div>'.number_format($se[$i]["cost"],2,'.',',').'</div>
				</td>
				<td class="r">'.number_format($se[$i]["cost"],2,'.',',').'</td>
				<td class="r">
					<div>'.($se[$i]["s_type"]=="p"?number_format($se[$i]["balance"],0,'.',','):$se[$i]["balance"]*1).'</div>
					<div>'.$se[$i]["unit_name"].'</div>
				</td>
				<td class="l">'.$se[$i]["unit_name"].'</td>
				<td class="action">
						<a onclick="G.action(this)" data-width="350" title="เลือกกระทำ">⚙️</a>
						<a onclick="Pd.productEdit(\''.$se[$i]["sku_root"].'\')" title="แก้ไข">📝</a>
						<a onclick="Pd.productReg(\''.$se[$i]["sku_root"].'\')" title="ลงทะเบียนสินค้าใหม่ โดยใช้ข้อมูลเริ่มต้นานค้านี้">📋</a>
						<a onclick="Pd.productStat(\''.$se[$i]["sku_root"].'\',\''.$namejs.'\')" title="สถานะ">📯</a>
						<a onclick="Pd.label(\''.$se[$i]["sku_root"].'\',\''.$namejs.'\')" title="พิมพ์ป้ายแสดงราคา">🏷</a>
						<a onclick="Pd.labelSticker(\''.$se[$i]["sku_root"].'\')" title="พิมพ์ป้ายสติ๊กเกอร์แปะสินค้า">▒</a>
						<a onclick="Pd.productDelete(\''.$se[$i]["sku_root"].'\',\''.$namejs.'\')" title="ทิ้ง">🗑</a>
					 '.$ed.'
					</td>
				</tr>';
				$this->lid=$se[$i]["id"];
		}
		echo '</table></form>';
		
		$count=(isset($sea["count"]))?$sea["count"]:0;
		if($this->txsearch==""){
			$this->page($count,$this->per,$this->page,"?a=product&amp;page=");
		}else{
			$this->pageSearch(count($se));
		}
	}
	private function writePartnerList(array $pn):void{
		foreach($pn as $k=>$v){
			echo '<div style="display:inline-block;padding:0px 2px;cursor:pointer">
				<div class="img24" title="'.htmlspecialchars($v["name"]).'" onclick="location.href=\'?a=partner&b=details&sku_root='.$k.'\'">
					<img src="img/gallery/32x32_'.$v["icon"].'" onerror="this.src=\'img/pos/64x64_null.png\'" alt="'.htmlspecialchars($v["name"]).'" />
			</div></div>';
		}
	}
	protected function writeDirGroup(string $sku_root,array $d):string{
		$t = '📁 /';
		for($i=0;$i<count($d) ;$i++){
			if($d[$i]!=""){
				$t.=$this->group_list[$d[$i]]["name"]."/";
			}else{
				if($i==0){
					$t.=$this->group_list[$sku_root]["name"]."/";
				}else{
					break;
				}
			}
		}
		$t.='';
		return $t;
	}
	private function getAllProduct(string $for=null):array{
		$sh=$this->defaultSearch();
		$re=[];
		$sql=[];
		$sql["count"]="SELECT @count:=COUNT(*) FROM product";
		if($for=="sell"||$for=="label"||$for=="itmw"){
			$sql["get"]="SELECT 
					 `product`.`sku`, `product`.`sku_root`, `product`.`barcode`, 
					`product`.`name`, `product`.`price`, `product`.`cost`, `product`.`s_type`,
					 `unit`.`name` AS `unit_name`,
					 SUM(IF(bill_in_list.s_type='p',`bill_in_list`.`balance`,`bill_in_list`.`balance_wlv`)) AS balance
				FROM `product` 
				LEFT JOIN (`unit`) 
				ON (`product`.`unit` = `unit`.`sku_root`) 
				LEFT JOIN (`bill_in_list`) 
				ON (`product`.`sku_root` = `bill_in_list`.`product_sku_root` 
					AND  IF(bill_in_list.s_type='p',`bill_in_list`.`balance`,`bill_in_list`.`balance_wlv`)>0 AND bill_in_list.stroot='proot') 
				".$sh." ".($for=="itmw"?(strlen(trim($sh))==0?" WHERE ":" AND ")." product.s_type='p' ":"")."
				GROUP BY product.sku_root ORDER BY `product`.`id` DESC  LIMIT 20
			";
		}else if($for=="billsin"){
			$sql["get"]="SELECT 
				`product`.`id`, `product`.`sku`, `product`.`sku_root`, `product`.`barcode`, 
				`product`.`name`, `product`.`price`, `product`.`cost`,  `product`.`s_type`,
				`product`.`unit` AS `unit_sku_root`, `unit`.`name` AS `unit_name`,
				SUM(IF(bill_in_list.s_type='p',`bill_in_list`.`balance`,`bill_in_list`.`balance_wlv`)) AS balance
			FROM `product` 
			LEFT JOIN (`unit`) 
			ON (`product`.`unit` = `unit`.`sku_root`) 
			LEFT JOIN (`bill_in_list`) 
			ON (`product`.`sku_root` = `bill_in_list`.`product_sku_root` 
				AND  IF(bill_in_list.s_type='p',`bill_in_list`.`balance`,`bill_in_list`.`balance_wlv`)>0 AND bill_in_list.stroot='proot') 
			".$sh." 
			GROUP BY product.sku_root ORDER BY `product`.`id` DESC LIMIT 20
			";
		}else{
			$limit_page=(($this->page-1)*$this->per).",".($this->per+1);
			if($this->txsearch!=""){
					$limit_page=$this->per+1;
			}
			//print_r($sh);
			$sql["get"]="SELECT 
				`product`.`id`, `product`.`sku`, `product`.`sku_root`, `product`.`barcode`, 
				`product`.`name`, `product`.`price`, `product`.`cost`, `product`.`s_type`, GetListPartner_(IFNULL(`product`.`partner`,'[]')) AS `partner`,
				`product`.`unit` AS `unit_sku_root`,product.pdstat, IFNULL(`product`.`group_root`,\"defaultroot\") AS `group_root`,`unit`.`name` AS `unit_name`,
				`group`.`d1` AS `d1`,`group`.`d2` AS `d2`,`group`.`d3` AS `d3`,`group`.`d4` AS `d4`,
				SUM(IF(bill_in_list.stroot='proot',IF(`bill_in_list`.`s_type`='p',`bill_in_list`.`balance`,`bill_in_list`.`balance_wlv`),0)) AS balance
			FROM `product` 
			LEFT JOIN (`unit`) 
			ON (`product`.`unit` = `unit`.`sku_root`) 
			LEFT JOIN (`group`) 
			ON (`product`.`group_root` = `group`.`sku_root`) 
			LEFT JOIN (`bill_in_list`) 
			ON (bill_in_list.stroot='proot' AND IF(`bill_in_list`.`s_type`='p',`bill_in_list`.`balance`,`bill_in_list`.`balance_wlv`)> 0 AND `product`.`sku_root` = `bill_in_list`.`product_sku_root`  ) 
			".$this->sh." 
			GROUP BY product.sku_root  ORDER BY `product`.`id` DESC LIMIT ".$limit_page."
			";
		}
		$sql["result"]="SELECT @count AS count";
		//print_r($sql);
		$se=$this->metMnSql($sql,["get","result"]);
		//print_r($se);
		if($se["result"]){
			$re=["row"=>$se["data"]["get"],"count"=>$se["data"]["result"][0]["count"]];
		}
		return $re;
	}
	private function defaultSearch():string{
		$fla=["sku","barcode","name"];
		$fl="name";
		$tx="";
		$se="";
		if(isset($_GET["fl"])){
			if(in_array($_GET["fl"],$fla)){
				if(($_GET["fl"]=="barcode"||$_GET["fl"]=="sku")
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
			$se=" WHERE `product`.`".$fl."` LIKE  \"%".$tx."%\"  ";
		}
		return $se;
	}
	private function defaultPageSearch():void{
		$fla=["sku","barcode","name"];
		$fl="name";
		$tx="";
		$se="";
		if(isset($_GET["fl"])){
			if(in_array($_GET["fl"],$fla)){
				if(($_GET["fl"]=="barcode"||$_GET["fl"]=="sku")
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
			$this->sh=" WHERE `product`.`id`".$idsearch." AND `product`.`".$fl."` LIKE  \"%".$tx."%\""  ;
		}
	}
	protected function pageSearch(int $row):void{
		$href='?a=product&amp;fl='.$this->fl.'&amp;tx='.$this->txsearch.'&amp;page=';
		if($this->page>1){
			echo '<a onclick="history.back()">⬅️ก่อนหน้า</a>';
		}
		echo '<span class="product_page_search">หน้า '.$this->page.'</span>';
		if($row>$this->per){
			echo '<a href="'.$href.''.($this->page+1).'&amp;lid='.$this->lid.'">ถัดไป➡️</a>';
		}
	}
	protected function setPropR():string{
		$re=[];
		foreach($_POST as $k=>$v){
			if(substr($k,0,5) == "prop_"){
				$kr = substr($k,5);
				$val = trim($_POST["prop_".$kr]);
				if(isset($this->prop_list[$kr]["data_type"]) && $this->prop_list[$kr]["data_type"] == "n"){
					$val = (float) $val;
				}
				$re[$kr] = $val;
			}
		}
		return json_encode($re,JSON_FORCE_OBJECT);
	}
	protected function setPartnerR(string $prop):string{
		$ar = [];
		if(strlen(trim($prop))>2){
			$prop =trim($prop);
			$ar = explode(",,",substr($prop,1,-1));
		}
		return json_encode($ar);
	}
	private function propToFromValue(string $prop):string{
		$t = str_replace("\",\"",",,",substr($prop,1,-1));
		$t = str_replace("\"",",",$t);
		//echo $t;
		return $t;
	}
}
?>
