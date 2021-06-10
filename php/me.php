<?php
class me extends main{
	public function __construct(){
		parent::__construct();
		$this->my_time=[];
		$this->my_tran=[];
		$this->r_more=[];
	}
	public function run(){
		$q=["edit","time","tran_log"];
		$this->addDir("?a=me","ฉัน");
		$this->setRMore();
		if(isset($_GET["b"])&&in_array($_GET["b"],$q)){
			$t=$_GET["b"];
			$this->r_more["active"]=$t;
			if($t=="edit"){
				$this->editMe();			
			}else if($t=="time"){
				$this->timeMe();
			}else if($t=="tran_log"){
				$this->tranLog();
			}
		}else{
			$this->timeMe();
		}
	}
	private function tranLog():void{
		$this->getTranLog();
		//print_r($this->my_tran);
		$this->tranLogPage();
	}
	private function getTranLog():void{
		$sql=[];
		$sql["get_pos"]="SELECT `device_pos`.`id`,`device_pos`.`time_id`,`device_pos`.`name`,
				`device_pos`.`sku`,`device_pos`.`user`,`device_pos`.`ip`,
				IFNULL(`device_pos`.`money_start`,0) AS `money_start`,
				IFNULL(`device_pos`.`money_balance`,0) AS `money_balance`,
				@time_start:=`device_pos`.`date_reg` AS `date_reg`,
				IFNULL(`device_drawers`.`sku`,'') AS `drawers_sku`,
				IFNULL(`device_drawers`.`name`,'') AS `drawers_name`
				#IFNULL(`time`.`money_balance`,0) AS `time_money_start`
			FROM `device_pos` 
			LEFT JOIN `device_drawers`
			ON(`device_pos`.`drawers_id`=`device_drawers`.`id`)
			LEFT JOIN `time`
			ON(`device_pos`.`time_id`=`time`.`id`)
			WHERE `device_pos`.`ip`='".$_SESSION["ip"]."'
		";
		$sql["get_tran"]="SELECT 
				`tran`.`id`			,`tran`.`tran_type`	,`tran`.`ref`		,`tran`.`min`		,`tran`.`mout`,
				`tran`.`money_balance`,`tran`.`note`		,`tran`.`date_reg`
			FROM `tran` 
			WHERE `tran`.`ip`='".$_SESSION["ip"]."' AND `date_reg` >= @time_start
		";
		$re=$this->metMnSql($sql,["get_pos","get_tran"]);
		//print_r($re);
		if(isset($re["data"]["get_tran"])){
			$this->my_tran=$re["data"]["get_tran"];
		}
		if(isset($re["data"]["get_pos"][0])){
			$this->my_time=$re["data"]["get_pos"][0];
		}
	}
	private function tranLogPage():void{
		$tl="บันทึกเงินสด เข้า-ออก ลิ้นชัก";
		$this->addDir("",$tl);
		$this->pageHead(["title"=>$tl." DIYPOS","css"=>["me"],"js"=>["me","Me"],"run"=>["Me"],"r_more"=>$this->r_more]);
		echo '<div class="content">
			<h2>'.$tl.'</h2>';
		$this->writeTranLog();
		echo '</div>';
		$this->pageFoot();
	}
	private function writeTranLog():void{//print_r($this->my_time);
		if(count($this->my_time)>0){
			$type=[
				"min"=>["icon"=>"📥","name"=>"นำเงินเข้าลิ้นชัก"],
				"mout"=>["icon"=>"📤","name"=>"นำเงินออกลิ้นชัก"],
				"sell"=>["icon"=>"🛒","name"=>"ขายสินค้า"],
				"ret"=>["icon"=>"↪️","name"=>"คืนสินค้า"],
				"pay"=>["icon"=>"💸️","name"=>"ชำระค้างจ่าย"],
				"canc"=>["icon"=>"❌️","name"=>"ยกเลิกใบเสร็จที่ออกไปแล้ว"]
				
			];
			$today=date('Y-m-d') ;//== date('Y-m-d', strtotime($timestamp));
			$yesterday= Date('Y-m-d', strtotime('-1 day'));
			$date="";
			//print_r($this->my_time);
			echo '<div class="me_time_log_disc_head">
				<div class="r">กะ : </div><div class="l bold">'.$_SESSION["time_id"].'</div>
				<div class="r">เริ่มเปิดกะ : </div><div class="l bold">'.$this->my_time["date_reg"].'</div>
				<div class="r">ชื่อลิ้นชัก : </div><div class="l bold">'.$this->my_time["name"].'</div>
				<div class="r">รหัสลิ้นชัก : </div><div class="l bold">'.$this->my_time["sku"].'</div>
				<div class="r">เงินสดเริ่มต้น : </div><div class="l bold">'.number_format($this->my_time["money_start"],2,".",",").'</div>
			</div>';
			echo '<table>
				<tr><th>ที่</th><th>เวลา</th><th>ประเภท</th><th>💬</th><th>เข้า</th><th>ออก</th><th>คงเหลือ</th></tr>';		
			$q=0;
			//print_r($this->my_tran);
			for($i=0;$i<count($this->my_tran);$i++){
				$d=explode(" ",$this->my_tran[$i]["date_reg"]);
				
				if($d[0]!=$date){
					$q=1;
					if($d[0]==$today){
						echo '<tr><td colspan="7" class="me_time_log_date_th">↓ วันนี้</td></tr>';
					}else if($d[0]==$yesterday){
						echo '<tr><td colspan="7" class="me_time_log_date_th">↓ เมื่อวานนี้</td></tr>';
					}else{
						echo '<tr><td colspan="7" class="me_time_log_date_th">↓ '.$d[0].'</td></tr>';
					}
					$date=$d[0];
				}
				$min_txt=($this->my_tran[$i]["min"]>0)?"+".number_format($this->my_tran[$i]["min"],2,".",","):"";
				$mout_txt=($this->my_tran[$i]["mout"]>0)?"-".number_format($this->my_tran[$i]["mout"],2,".",","):"";
				$balance_txt=($this->my_tran[$i]["money_balance"]>0)?number_format($this->my_tran[$i]["money_balance"],2,".",","):"";
				
				$type_icon=$type[$this->my_tran[$i]["tran_type"]]["icon"];
				$q+=1;
				$tr=($q%2)+1;
				$cm=($this->my_tran[$i]["note"]!="")?"<span class=\"me_time_log_note\" onclick=\"M.tooltups(this,'".htmlspecialchars($this->my_tran[$i]["note"])."',200)\">💬</span>":"";
				$tt=$this->my_tran[$i]["tran_type"];
				$type_tx=$type_icon;
				if($tt=="sell"){
					$type_tx='<span class="me_time_log_span" onclick="Me.showBill(this,\''.$tt.'\',\''.$this->my_tran[$i]["ref"].'\','.($i+1).')" title="ใบเสร็จเลขที่ '.$this->my_tran[$i]["ref"].'">'.$type_icon.'</span>';
				}else if($tt=="pay"){
					$type_tx='<span class="me_time_log_span" onclick="Me.showBill(this,\''.$tt.'\',\''.$this->my_tran[$i]["ref"].'\','.($i+1).')" title="ใบเสร็จเลขที่ '.$this->my_tran[$i]["ref"].'">'.$type_icon.'</span>';
				}else if($tt=="ret"){
					$type_tx='<span class="me_time_log_span" onclick="Me.showBill(this,\''.$tt.'\',\''.$this->my_tran[$i]["ref"].'\','.($i+1).')" title="ใบเสร็จเลขที่ '.$this->my_tran[$i]["ref"].'">'.$type_icon.'</span>';
				}else if($tt=="canc"){

					$bil=preg_replace(["/.[0-9]{1,}$/"],[""], $this->my_tran[$i]["ref"]);
					$type_tx='<span class="me_time_log_span" onclick="Me.showBill(this,\'sell\',\''.$bil.'\','.($i+1).')" title="ใบเสร็จเลขที่ '.$bil.'">'.$type_icon.'</span>';
				}
				echo '<tr class="i'.$tr.'">
					<td>'.($i+1).'.</td>
					<td>'.substr($d[1],0,5).'</td>
					<td>'.$type_tx.'</td>
					<td class="c">'.$cm.'</td>
					<td class="r">'.$min_txt.'</td>
					<td class="r">'.$mout_txt.'</td>
					<td class="r">'.$balance_txt.'</td>
				</tr>';
			}
			echo '</table>';	
			echo '<p class="c">';
			foreach($type as $k=>$v){
				echo '<span class="me_time_log_note_disc">'.$v["icon"].' = '.$v["name"].'</span>';
			}
			echo '</p>';
		}else{
			$this->regisDevice();
		}
	}
	private function setRMore():void{
		$url="?a=me";
		$data=[
			"menu"=>[
				["b"=>"time","name"=>"กะทำงานฉัน","link"=>$url."&amp;b=time"],
				["b"=>"edit","name"=>"แก้ไขฉัน","link"=>$url."&amp;b=edit"],
				["b"=>"tran_log","name"=>"ประวัติ เงินเข้า-ออก ลิ้นชัก","link"=>$url."&amp;b=tran_log"],
				["b"=>"log_out","name"=>"ออกจากระบบ","link"=>"",
					"html"=>"<input class=\"me_bt_rmore_logout\" type=\"button\" name=\"logoubt\" onclick=\"G.logout2()\" value=\"ออกจากระบบ\" />"],
				
			],
			"active"=>""
		];
		$this->r_more=$data;
	}
	private function editMe():void{
		$error="";
		$_POST["sku_root"]=$_SESSION["sku_root"];
		if(isset($_POST["submith"])&&$_POST["submith"]=="clicksubmit"&&isset($_POST["ps"])&&$_SESSION["sku_root"]!="systemroot"){
			$_POST["password"]=(isset($_POST["ps1"])&&strlen(trim($_POST["ps1"]))>0)?$_POST["ps1"]:"00000000";
			$se=$this->checkSet("user",["post"=>["name","lastname","email","sku_root","password"]],"post");
			if(!$se["result"]){
				$error=$se["message_error"];
			}else if(!$this->checkMe($_POST["ps"])){
				$error="รหัสผ่านยืนยันตัวคุณไม่ถูกต้อง คุณไม่ใช่ <b><i>".$_SESSION["name"]." ".$_SESSION["lastname"]."</i></b>";
			}else if((isset($_POST["ps1"])||isset($_POST["ps2"]))&&trim($_POST["ps1"])!=trim($_POST["ps2"])){
				$error="รหัสผ่านใหม่ของคุณ กับรหัสผ่านใหม่อีกครั้ง ไม่ตรงกัน ";
			}else if($_SESSION["onoff"]!=1){
				$error="คุณต้องเปิดกะทำงานก่อน ถึงจะแก้ไขได้ ";
			}else{
				 $qe=$this->editMeUpdate();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					 //print_r($qe);
					header('Location:?a=me');
				}
			}
			if($error!=""){
				$this->editMePage($error);
			}
		}else{
			$sku_root=(isset($_POST["sku_root"]))?$_POST["sku_root"]:"";
			$this->editMeSetCurent($sku_root);
			$this->editMePage($error);
		}
	}
	private function editMeUpdate():array{
		$name=$this->getStringSqlSet($_POST["name"]);
		$lastname=$this->getStringSqlSet($_POST["lastname"]);
		$email=$this->getStringSqlSet($_POST["email"]);	
		$sku_root=$this->getStringSqlSet($_SESSION["sku_root"]);
		$new_password=(isset($_POST["ps1"])&&strlen($_POST["ps1"])>=8)?password_hash($_POST["ps1"], PASSWORD_DEFAULT):"";
		$text_pass_set="";
		if($new_password!=""){
			$text_pass_set=" ,`password`=\"".$new_password."\" ";
		}
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@count_name:=(SELECT COUNT(`id`)  FROM `user` WHERE `name`=".$name." AND `sku_root` !=".$sku_root." LIMIT 1),
			@count_lastname:=(SELECT COUNT(`id`)  FROM `user` WHERE `lastname`=".$lastname." AND `sku_root` !=".$sku_root." LIMIT 1),
			@count_email:=(SELECT COUNT(`id`)   FROM `user` WHERE `email`=".$email." AND `sku_root` !=".$sku_root."  LIMIT 1);
		";
		$sql["check"]="
			IF @count_name > 0  && @count_lastname > 0  THEN 
				SET @message_error='เกิดขอผิดพลาด ชื่อและนามสกุล ที่แก้ไขมา มีแล้ว โปรดลองชื่อและนามสกุลอื่น';
			ELSEIF @count_email > 0 THEN
				SET @message_error='เกิดขอผิดพลาด อีเมลที่แก้ไขมา มีแล้ว โปรดลอง อีเมลในอื่น';
			END IF;			
		";
		$sql["run"]="
			IF LENGTH(@message_error) = 0 THEN
				UPDATE `user` SET  `lastname`=".$lastname.",  `name`= ".$name."  ,  `email`= ".$email." ".$text_pass_set." WHERE `sku_root`=".$sku_root." LIMIT 1;
				SET @result=1;
			END IF;
		";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		$se=$this->metMnSql($sql,["result"]);
		return $se;
	}
	private function editMeSetCurent(string $sku_root):void{
		$od=$this->editMeOldData($sku_root);
		$fl=["name","lastname","email","sku"];
		foreach($fl as $v){
			if(isset($od[$v])){
				$_POST[$v]=$od[$v];
			}
		}
	}
	private function editMeOldData(string $sku_root):array{
		$sku_root=$this->getStringSqlSet($sku_root);
		$sql=[];
		$sql["result"]="SELECT * FROM `user` WHERE `sku_root`=".$sku_root."";
		$re=$this->metMnSql($sql,["result"]);
		if(isset($re["data"]["result"][0])){
			return $re["data"]["result"][0];
		}
		return [];
	}
	
	private function editMePage(string $error):void{
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$lastname=(isset($_POST["lastname"]))?htmlspecialchars($_POST["lastname"]):"";
		$sku=(isset($_POST["sku"]))?htmlspecialchars($_POST["sku"]):"";
		$email=(isset($_POST["email"]))?htmlspecialchars($_POST["email"]):"";
		$userceo=$_SESSION["userceo"];
		$this->addDir("","แก้ไข ฉัน");
		$this->pageHead(["title"=>"ฉัน DIYPOS","css"=>["me"],"js"=>["me","Me"],"run"=>["Me"],"r_more"=>$this->r_more]);
		$pem=true;
		$dis="";
		echo '<div class="content"><div class="form">';
		echo '<h2 class="c">แก้ไข ฉัน</h2>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}	
		if($_SESSION["sku_root"]=="systemroot"){
			$pem=false;
			$dis=' disabled="disabled"';
			echo '<div class="warning">[[SYSTEM]] คือผู้ใช้ที่ระบบสร้างขึ้นมาเฉพาะ ไม่สามารถแก้ไขได้</div>';
		}	
		echo '		<form name="me" method="post" action="">
					<input type="hidden" name="submith" value="clicksubmit" />
					<input type="hidden" name="ps" value="" />
					<input type="hidden" name="logout" value="" />
					<p><label for="me_name">ชื่อ</label></p>
					<div><input id="me_name" name="name" class="want" type="text" value="'.$name.'"'.$dis.' autocomplete="off" /></div>
					<p><label for="me_lastname">นามสกุล</label></p>
					<div><input  id="me_lastname" type="text" name="lastname" value="'.$lastname.'"'.$dis.' autocomplete="off" /></div>
					<p><label for="me_sku" >รหัสภายใน</label></p>
					<div><input id="me_sku" name="sku"  type="text"  value="'.$sku.'" autocomplete="off"  disabled="disabled" /></div>
					<p><label for="me_email">อีเมล</label></p>
					<div><input id="me_email" name="email" class="want" type="text"  value="'.$email.'"'.$dis.' autocomplete="off" /></div>
					<p><label for="me_userceo">ระดับ</label></p>
					<div><input  id="me_userceo" type="text" name="userceo" value="'.$this->cf["userceo"][$userceo]["name"].'" readonly="readonly"   disabled="disabled" /></div>
					<p><label for="me_ps1">รหัสผ่านใหม่</label></p>
					<div><input id="me_ps1" type="password" value=""  name="ps1"  autocomplete="off"'.$dis.' /></div>
					<p><label for="me_ps2">รหัสผ่านใหม่อีกครั้ง</label></p>
					<div><input id="me_ps2" type="password" value=""  name="ps2"  autocomplete="off"'.$dis.' /></div>
					<br />';
		if($_SESSION["onoff"]==1){
			echo '<input type="button" name="ok" onclick="G.meSubmit()" value="แก้ไข"'.$dis.' /> ';
		}
		echo '<input type="button" name="logoubt" onclick="G.logout()" value="ออกจากระบบ" />
				</form>
			
			</div>
		</div>';
		$this->pageFoot();
	}
	private function getMyTime():void{
		$sql=[];
		$sql["get_pos"]="SELECT `device_pos`.`id`,`device_pos`.`time_id`,`device_pos`.`name`,
				`device_pos`.`sku`,`device_pos`.`user`,`device_pos`.`ip`,
				IFNULL(`device_pos`.`money_start`,0) AS `money_start`,
				IFNULL(`device_pos`.`money_balance`,0) AS `money_balance`,
				`device_pos`.`date_reg`,
				IFNULL(`device_drawers`.`sku`,'') AS `drawers_sku`,
				IFNULL(`device_drawers`.`name`,'') AS `drawers_name`
			FROM `device_pos` 
			LEFT JOIN `device_drawers`
			ON(`device_pos`.`drawers_id`=`device_drawers`.`id`)
			WHERE `device_pos`.`ip`='".$_SESSION["ip"]."'
		";
		$re=$this->metMnSql($sql,["get_pos"]);
		if(isset($re["data"]["get_pos"][0])){
			$this->my_time=$re["data"]["get_pos"][0];
		}
	}
	private function timeMe():void{
		$this->getMyTime();
		$this->timeMePage();
	}
	private function timeMePage():void{
		$tl="กะทำงาน ฉัน";
		$this->addDir("",$tl);
		$this->pageHead(["title"=>"ฉัน DIYPOS","css"=>["me"],"js"=>["me","Me"],"run"=>["Me"],"r_more"=>$this->r_more]);
		$pem=true;
		$dis="";
		echo '<div class="content">';
		$this->writeMyTime();
		echo '</div>';
		/*$this->btMore([
			["link"=>"?a=device&b=pos","name"=>"เครื่องขาย"],
			["link"=>"?a=me&b=tran_log","name"=>"บันทึกเงินสดเข้า-ออกลิ้นชัก"]
		]);*/
		$this->pageFoot();
	}
	private function writeMyTime():void{//print_r($this->my_time);
		if(count($this->my_time)>0){
			if($this->my_time["user"]==$_SESSION["sku_root"]){
				$ms=number_format($this->my_time["money_start"],2,'.',',');
				$mb=number_format($this->my_time["money_balance"],2,'.',',');
				$d=explode(" ",$this->my_time["date_reg"]);
				//$mb="523,254.75";
				echo '<div class="me_time">
					<p>กะทำงานของฉัน';
				if($this->my_time["drawers_sku"]==""){		
					echo '<span class="warning me_drawers_wn">อุปกรณ์นี้ไม่ได้ระบุ ลิ้นชัก/ที่เก็บเงิน จะไม่สามารถทำกิจกรรมที่เกียวข้องกับ การรับ จ่าย ทอน เงิดสดได้</span>';
				}
				echo '</p>';
				echo '<div>
						<div class="me_pos">เครื่องนี้ IP<div>'.$this->userIPv4().'</div></div>
						<div class="me_pos">เครื่องนี้ ชื่อ<div>'.htmlspecialchars($this->my_time["name"]).'</div></div>';
				$drawers_sku="";
				$drawers_name="";
				if($this->my_time["drawers_sku"]!=""){		
					$drawers_sku=$this->my_time["drawers_sku"];
					$drawers_name=htmlspecialchars($this->my_time["drawers_name"]);
					echo '	<div class="me_drawers">ลิ้นชัก/ที่เก็บเงินสด รหัส<div>'.$drawers_sku.'</div></div>
						<div class="me_drawers">ลิ้นชัก/ที่เก็บเงินสด ชื่อ<div>'.$drawers_name.'</div></div>';
				}
				echo '<div class="start_time">ปิดกะ วันที่<div>'.$d[0].'</div></div>
						<div class="start_time">เปิดกะ เวลา<div>'.$d[1].' น.</div></div>
						<div class="start_time">เปิดกะมานาน<div id="time_ago">00:00:00</div></div>
						<div class="time_id">กะ<div>'.$_SESSION["time_id"].'</div></div>';
				if($this->my_time["drawers_sku"]!=""){		
					echo '	<div class="money_start">เงินสดเริ่มต้น<div>'.$ms.'</div></div>
						<div class="money_balance">เงินสดขฌะนี้<div id="me_money_balance">'.$mb.'</div></div>
						<div><input type="button" value="นำเงินเข้า" onclick="Me.min(\''.$drawers_sku.'\',\''.$drawers_name.'\')" /></div>
						<div><input type="button" value="นำเงินออก" onclick="Me.mout(\''.$drawers_sku.'\',\''.$drawers_name.'\')" /></div>
					
					';
				}
				echo '</div><div><input type="button" value="ปิดกะ และออกจากระบบ" onclick="Me.closeTime()" /></div>
					<script type="text/javascript">F.showTimeAgo(\'time_ago\',\''.$this->my_time["date_reg"].'\')</script>
				</div>';
			}
		}else{
			$this->regisDevice();
		}
	}
	private function regisDevice():void{
		echo '<div class="content">
			<div class="form">
				<br />
				<div class="error">อุปกรณ์ หมายเลข IP '.$_SESSION["ip"].' นี้ ยังไม่มีในระบบ</div>
				<br />
				<input type="button" value="ลงทะเบียนอุปกรณ์" onclick="location.href=\'?a=device\'" />
			</div>
		</div>';		
	}
}
