<?php
class account_history extends account{
	public function __construct(){
		parent::__construct();
		$this->b="account_history";
		$this->title_b="ลูกหนี้ ค้างชำระ";
		$this->title_c="";
	}
	public function xxxxxxxxxxxxxxxxxxxfetch(){//print_r($_POST);
		$re=["result"=>false,"message_error"=>"","data"=>[]];
		if(isset($_POST["c"])){
			if($_POST["c"]=="pay_rca"){
				$se=$this->checkSet("rca",["post"=>["sku_root"]],"post",["sku_root","payu_json","note"],["pay"=>"min","payu"=>"payu_json"]);
				if(!$se["result"]){
					$re["message_error"]=$se["message_error"];
				}else{
					$this->fetchRcaSave($_POST);
				}
			}
		}
		header('Content-type: application/json');
		echo json_encode($re);
	}
	
	private function xxxxxxxxxxxxxxxxxxxxxxxxxfetchRcaSave(array $post):array{
		$pn=(float) $_POST["pay"];
		$payu_json0=$this->cutPerfix($_POST["payu"]);
		$note=(isset($_POST["note"]))?$_POST["note"]:"";
		$note=$this->getStringSqlSet($note);
		//echo $note."****";
		$payu_json=$this->getStringSqlSet($payu_json0);
		$s_payu_key=$this->jsonDocToArrayKeyDoc($payu_json0);//'["a","b",[...]]';
		$member_sku_root=$this->getStringSqlSet($post["sku_root"]);
		$py=json_decode($payu_json0,true);
		$py_sum=0;
		foreach($py as $k=>$v){
			if($v>0){
				$py_sum+=$v;
			}
		}
		$min=0;
		$mout=0;
		if(isset($py["defaultroot"])){
			$min=(float) $py["defaultroot"];
		}		
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@TEST:='',
			@message_error:='',
			@ip:='".$_SESSION["ip"]."',
			@pay:=".$post["pay"].",
			@payu_json0:='".$payu_json0."',
			@payu_json:=".$payu_json.",
			@s_payu_key:='".$s_payu_key."',
			@py_sum:=".$py_sum.",
			@min:=".$min.",
			@member_sku_root:=".$member_sku_root.",
			@user:='".$_SESSION["sku_root"]."',
			@user_id:='".$_SESSION["id"]."',
			@note=".$note.",
			@time_id:='".$_SESSION["time_id"]."',
			@member_id:=(SELECT `id` FROM `member` WHERE `sku_root` =".$member_sku_root." ),
			@member_key:=(SELECT `sku_key` FROM `member` WHERE `id` =@member_id ),
			@user_key:=(SELECT `sku_key` FROM `user` WHERE `id` =@user_id),
			@rca_sum:=(SELECT SUM(`credit`) FROM `rca` WHERE `member_id`=@member_id);
		";
		
		$sql["check"]="
			IF @member_id IS NULL THEN 
				SET @message_error='เกิดขอผิดพลาด ไม่พบสมาชิก ที่ส่งมา';
			ELSEIF @rca_sum = 0 THEN
				SET @message_error='เกิดขอผิดพลาด ไม่รายการค้างจ่ายใด ๆ';
			ELSEIF @py_sum < @pay THEN
				SET @message_error='จำนวนเงิน ใน รูปแบบการชำระ น้อยกว่ายอดที่ต้องการชำระ';
			END IF;			
		";
		$sql["run"]="BEGIN NOT ATOMIC 
			DECLARE done INT DEFAULT FALSE;
			DECLARE r__ INT DEFAULT 0;
			DECLARE __r INT DEFAULT 0;
			DECLARE lastid INT DEFAULT NULL;	
			DECLARE i_fag INT DEFAULT 0;	
			DECLARE py_sum FLOAT DEFAULT @py_sum;
			DECLARE pay FLOAT DEFAULT @pay;
			DECLARE f_mout FLOAT DEFAULT 0;
			DECLARE date_reg_ TIMESTAMP DEFAULT NOW();
			DECLARE bill_rca_sku CHAR(25) DEFAULT NULL;
			DECLARE r0 ROW (
				`id`INT ,
				`ip`CHAR(25),
				`onoff` CHAR(1),
				`drawers_id`INT ,
				`money_start` FLOAT,
				`money_balance` FLOAT,
				`user` CHAR(25),
				`date_reg` TIMESTAMP
			);
			DECLARE r ROW (id INT ,
										bill_sell_id VARCHAR(25),
										member_id VARCHAR(25),
										credit FLOAT);
			DECLARE cur1 CURSOR FOR
				SELECT `id`,`bill_sell_id`,`member_id`,`credit`
					FROM `rca`
					WHERE `credit`> 0 AND `member_id` = @member_id;
			DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;	
			IF LENGTH(@message_error) = 0 THEN
				SET @payu_key_json=GetPayuArrRef_(@payu_json0);
				SELECT `id`		,`ip`		,`onoff`		,`drawers_id`	,`money_start`	,`money_balance`,
							`user`	,`date_reg` 
					INTO r0.id		,r0.ip		,r0.onoff		,r0.drawers_id		,r0.money_start	,r0.money_balance	,
							r0.user	,r0.date_reg
					FROM `device_pos` WHERE `ip`= @ip AND `user`=@user AND `onoff`='1' LIMIT 1;
				INSERT INTO `bill_rca`(
					`time_id`				,`member_id`		,`user_id`				,`pos_id`			,`drawers_id`,
					`pay`					,`min`				,`credit`					,`payu_json`	,`member_sku_key`		,`user`,
					`payu_key_json`	,`date_reg`
				)VALUES(
					@time_id			,@member_id		,@user_id					,r0.id					,r0.drawers_id,
					@pay				,@py_sum				,(@rca_sum-@pay)	,@payu_json		,@member_key		,@user_key,
					@s_payu_key	,date_reg_
				);
				SET lastid=(SELECT LAST_INSERT_ID());
				SET @TEST=CONCAT(@TEST,';lastid=',lastid);
				
				IF lastid!=0 THEN
					IF r__=0 THEN 
						SET r__=IFNULL((SELECT MAX(id) FROM bill_rca_list LIMIT 1),0);
					ELSE
						SET __r=lastid;
					END IF;
				END IF;
				SET @TEST=CONCAT(@TEST,';r_=',r__,';_r=',__r);
				IF lastid > 0 THEN
					SET i_fag=lastid;
					SET bill_rca_sku=LPAD(CAST(i_fag AS CHAR(25)),9,'0');
					UPDATE `bill_rca` SET `sku`=bill_rca_sku WHERE `id`= lastid;
					UPDATE `member` SET `credit` = (@rca_sum-@pay) WHERE `id` = @member_id;
					INSERT INTO `tran_rca` (
						`time_id`				,`tran_rca_type`		,`bill_rca_id`					,`min`		,`mout`					,`ip`		,`drawers_id`,
						`member_id`			,`user_id`					,`money_balance`			,`date_reg`
					)VALUES(
						@time_id				,'pay'							,lastid					,@min		,(@py_sum-@pay)		,@ip		,r0.drawers_id,
						@member_id			,@user_id					,(@rca_sum-@pay)	,date_reg_
					);		
					INSERT INTO `tran` (
						`time_id`				,`tran_type`				,`ref`					,`min`		,`mout`					,`ip`		,`drawers_id`,
						`user`					,`money_balance`		,`date_reg`
					)VALUES(
						@time_id				,'pay'							,bill_rca_sku					,@min		,(@py_sum-@pay)		,@ip		,r0.drawers_id,
						@user					,(r0.money_balance+@min - (@py_sum-@pay))	,date_reg_
					);
					UPDATE `device_pos` 
						SET `money_balance` = (r0.money_balance+@min - (@py_sum-@pay))
						WHERE `ip`= @ip;
						
					OPEN cur1;
						read_loop: LOOP
							FETCH cur1 INTO r;
							IF done THEN
								LEAVE read_loop;
							END IF;
							IF  pay > 0 THEN
								IF r.credit <= pay THEN 
									UPDATE `rca` SET `credit`= 0 WHERE `id` = r.id;
									SET pay = pay - r.credit;
									#column=>[id,bill_rca_id,bill_sell_id,credit,money_balance,min,date_reg],
									INSERT INTO `bill_rca_list`(
										`bill_rca_id`	,`bill_sell_id`		,`credit`	,`money_balance`		,`min`		,`date_reg`
									)VALUES(
										lastid				,r.bill_sell_id		,r.credit		,0								,r.credit		,date_reg_
									);
								ELSEIF r.credit > pay THEN 
									UPDATE `rca` SET `credit`= (`credit` - pay) WHERE `id` = r.id;
									INSERT INTO `bill_rca_list`(
										`bill_rca_id`	,`bill_sell_id`		,`credit`	,`money_balance`		,`min`		,`date_reg`
									)VALUES(
										lastid				,r.bill_sell_id		,r.credit		,r.credit-pay				,pay		,date_reg_
									);
									SET pay = 0;
								END IF;
							ELSE
								INSERT INTO `bill_rca_list`(
									`bill_rca_id`	,`bill_sell_id`		,`credit`	,`money_balance`		,`min`		,`date_reg`
								)VALUES(
									lastid				,r.bill_sell_id		,r.credit		,r.credit				,0		,date_reg_
								);
							END IF;
						END LOOP;
					CLOSE cur1;
					IF __r=0 THEN 
						SET __r=r__;
					END IF;
					UPDATE bill_rca SET `r_`=r__,`_r`=__r WHERE `id` = lastid ;
				END IF;
			END IF;
		END;";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@TEST";
		$se=$this->metMnSql($sql,["result"]);
		print_r($se);
		return $se;
	}
	/*private function rcaCheck(array $post):array{
		$re=["result"=>false,"message_error"=>""];
		if(!isset($post["pay"])){
			$re["message_error"]="ยังไม่ได้ระบุจำนวนเงินที่ต้องการชำระ";
		}else if(!$this->isMoney($post["pay"])){
			$re["message_error"]="ไม่อยู่ในรูปแบบเงิน";
		}else if(!$this->isJSON($post["payu"])){
			$re["message_error"]="ไม่อยู่ในรูปแบบ การรับชำระ";
		}else if(!$this->isSKU($post["sku_root"])){
			$re["message_error"]="ไม่อยู่ในรูปแบบ การรับชำระ";
		}else{
			$re["result"]=true;
		}
		return $re;
	}*/
	public function run(){
		$this->addDir("?a=".$this->a."&amp;b=account_rca",$this->title_b);
		if(isset($_GET["sku_root"])&&$this->isSKU($_GET["sku_root"])){
			$this->title_c="ประวัติชำระ ค้างจ่าย";
			$this->historyPage($_GET["sku_root"]);					
		}else{
			echo "no";
		}
	}
	private function historyPage(string $root):void{
		$data=$this->historyGetData($root);
		//print_r($data);
		$this->title_c="ประวัติหนี้สิน ของ ".htmlspecialchars($data["member"]["name"]." ".$data["member"]["lastname"]);
		$this->addDir("?a=".$this->a."&amp;b=".$this->b."&amp;sku_root=".$root,$this->title_c);
		$this->pageHead(["title"=>$this->title_c." DIYPOS","css"=>["account"],"js"=>["me","Me"],"run"=>[]]);
		echo '<div class="content">';
	
		echo '	<div class="form">
			<h1 class="c">'.$this->title_c.'</h1>';
		$this->historyWriteContent($data);		
		echo '</div></div>';
		$this->pageFoot();
	}
	private function historyWriteContent(array $data):void{
		$type=[
			"sell"=>["icon"=>"📥","name"=>"ซื้อสินค้าแบบมีค้างชำระ"],
			"ret"=>["icon"=>"↪️","name"=>"คืนสินค้า หักยอดค้างชำระออก"],
			"pay"=>["icon"=>"💸️","name"=>"ชำระค้างจ่าย"]
		];
		$today=date('Y-m-d') ;//== date('Y-m-d', strtotime($timestamp));
		$yesterday= Date('Y-m-d', strtotime('-1 day'));
		$date="";
		echo '<table style="width:360px">
			<tr>
				<th>ที่</th>
				<th>เวลา</th>
				<th>ประเภท</th>
				<th>หนี้เพิ่ม</th>
				<th>หนี้ลด</th>
				<th>หนี้คงเหลือ</th>
			</tr>';
		for($i=0;$i<count($data["list"]);$i++){
			$d=explode(" ",$data["list"][$i]["date_reg"]);
			if($d[0]!=$date){
				$q=1;
				if($d[0]==$today){
					echo '<tr><td colspan="6" class="rcapay_time_log_date_th">↓ วันนี้</td></tr>';
				}else if($d[0]==$yesterday){
					echo '<tr><td colspan="6" class="rcapay_time_log_date_th">↓ เมื่อวานนี้</td></tr>';
				}else{
						echo '<tr><td colspan="6" class="rcapay_time_log_date_th">↓ '.$d[0].'</td></tr>';
				}
				$date=$d[0];
			}
			$min_txt="";//($data["list"][$i]["min"]>0)?"+".number_format($data["list"][$i]["min"],2,".",","):"";
			$mout_txt="";//($data["list"][$i]["mout"]>0)?"-".number_format($data["list"][$i]["mout"],2,".",","):"";
			$balance_txt=($data["list"][$i]["money_balance"]>0)?number_format($data["list"][$i]["money_balance"],2,".",","):"";
			
			$type_icon=$type[$data["list"][$i]["tran_rca_type"]]["icon"];
			$q+=1;
			$tr=($q%2)+1;
			//$cm=($this->my_tran[$i]["note"]!="")?"<span class=\"me_time_log_note\" onclick=\"M.tooltups(this,'".htmlspecialchars($this->my_tran[$i]["note"])."',200)\">💬</span>":"";
			$tt=$data["list"][$i]["tran_rca_type"];
			$type_tx=$type_icon;
			if($tt=="sell"){
				$type_tx='<span class="rcapay_time_log_span" onclick="Me.showBill(this,\''.$tt.'\',\''.$data["list"][$i]["ref"].'\','.($i+1).')" title="ใบเสร็จเลขที่ '.$data["list"][$i]["ref"].'">'.$type_icon.'</span>';
				$min_txt=($data["list"][$i]["mout"]>0)?"+".number_format($data["list"][$i]["mout"],2,".",","):"";
			}else if($tt=="pay"){
				$type_tx='<span class="rcapay_time_log_span" onclick="Me.showBill(this,\''.$tt.'\',\''.$data["list"][$i]["ref"].'\','.($i+1).')" title="ใบเสร็จเลขที่ '.$data["list"][$i]["ref"].'">'.$type_icon.'</span>';
				$df=$data["list"][$i]["min"]-$data["list"][$i]["mout"];
				$mout_txt="-".number_format($df,2,".",",");
			}else if($tt=="ret"){
				$type_tx='<span class="rcapay_time_log_span" onclick="Me.showBill(this,\''.$tt.'\',\''.$data["list"][$i]["ref"].'\','.($i+1).')" title="ใบเสร็จเลขที่ '.$data["list"][$i]["ref"].'">'.$type_icon.'</span>';
				$mout_txt=($data["list"][$i]["min"]>0)?"-".number_format($data["list"][$i]["min"],2,".",","):"";
			}
			echo '<tr class="i'.$tr.'">
				<td>'.($i+1).'.</td>
				<td>'.substr($d[1],0,5).'</td>
				<td>'.$type_tx.'</td>
				<td class="r">'.$min_txt.'</td>
				<td class="r">'.$mout_txt.'</td>
				<td class="r">'.$balance_txt.'</td>
			</tr>';
		}
		echo '</table>';
			echo '<p class="c">';
			foreach($type as $k=>$v){
				echo '<span class="rcapay_time_log_note_disc">'.$v["icon"].' = '.$v["name"].'</span>';
			}
			echo '</p>';
	}
	private function historyGetData(string $sku_root):array{
		$re=["member"=>[],"list"=>[]];
		$sql=[];
		$sql["set"]="SELECT @member_sku_root:='".$sku_root."',
			@member_id:=(SELECT `id` FROM `member` WHERE `sku_root`=@member_sku_root)";
		$sql["member"]="SELECT * FROM `member` WHERE `id`=@member_id";
		$sql["list"]="SELECT `tran_rca`.`tran_rca_type`		,RcaGetBillSKU_(`tran_rca`.`tran_rca_type`,`tran_rca`.`bill_rca_id`) AS `ref`,
				`tran_rca`.`min`											,`tran_rca`.`mout`,
				`tran_rca`.`money_balance`							,`tran_rca`.`date_reg`
			FROM `tran_rca`
			WHERE `tran_rca`.`member_id`=@member_id;
		
		";
		$se=$this->metMnSql($sql,["member","list"]);
		/*$sql["set"]="SELECT @member_id:=".$member_id."";
		$sql["get_member"]="SELECT `id`,`name`,IFNULL(`lastname`,'') AS `lastname`,IFNULL(`icon`,'null.png') AS `icon`,`sku`,`sku_root` ,
				`sku`,IFNULL(`tel`,'-') AS `tel`,`mb_type`,`credit`
			FROM `member` WHERE `id`=@member_id";
		$sql["get_rca"]="SELECT `rca`.`bill_sell_id`,`rca`.`credit` AS `rca_credit`	,`rca`.`date_reg`,
				`bill_sell`.`sku`	,`bill_sell`.`credit` AS `bill_sell_credit`
			FROM `rca`
			LEFT JOIN `bill_sell`
			ON(`rca`.`bill_sell_id`=`bill_sell`.`id`)
			WHERE `rca`.`member_id`= @member_id AND `rca`.`credit` > 0;
		";	
		$sql["get_payu_all"]="SELECT `payu`.`name`	,`payu`.`sku_root`,IFNULL(`payu`.`icon`,'') AS `icon`
			FROM `payu`
			WHERE `payu`.`money_type` != 'cd' ORDER BY `id`";
		$se=$this->metMnSql($sql,["get_member","get_rca","get_payu_all"]);*/
		//print_r($se);
		if($se["result"]){
			if(isset($se["data"]["member"])&&count($se["data"]["member"])==1){
				$re["member"]=$se["data"]["member"][0];
				$re["list"]=$se["data"]["list"];
			}
		}
		return $re;
	}
	protected function defaultAccountRcaPage(){
		$this->defaultPageSearch();
		$data=$this->getAllMember();
		$this->pageHead(["title"=>$this->title_b." DIYPOS","css"=>["account"],"js"=>["account","Ac"],"run"=>["Ac"]]);
		echo '<div class="content">';
	
		echo '	<div class="form">
			<h1 class="c">'.$this->title_b.'</h1>';
		$this->writeDashboard($data);	
		echo '	<div class="pn_search">
				<form class="form100" name="pd_search" action="" method="get">
					<input type="hidden" name="a" value="account" />
					<input type="hidden" name="b" value="account_rca" />
					<input type="hidden" name="lid" value="0" />
					<label><select id="product_search_fl" name="fl">
						<option value="name"'.(($this->fl=="name")?" selected":"").'>ชื่อ</option>
						<option value="lastname"'.(($this->fl=="lastname")?" selected":"").'>นามสกุล</option>
						<option value="sku"'.(($this->fl=="sku")?" selected":"").'>รหัส</option>
						<option value="tel"'.(($this->fl=="tel")?" selected":"").'>เบอร์โทรศัพท์</option>
						<option value="idc"'.(($this->fl=="idc")?" selected":"").'>บัตรประชาชน</option>
					</select>
						<input id="product_search_tx" type="text" name="tx" value="'.str_replace("\\","",htmlspecialchars($this->txsearch)).'" />
						<input  type="submit" value="🔍" /> </label>
				</form>
			</div>';
		$this->writeContentMember($data);		
		echo '<br /><p class="c"><input type="button" value="เพิ่ม'.$this->title.'" onclick="location.href=\'?a='.$this->a.'&amp;b=regis\'" /></p>';
		echo '</div></div>';
		$this->pageFoot();
	}
	private function writeRca():void{
		$this->getRca();
		//print_r($this->get_rca);
		
	}
	private function getRca(){
		$sql=[];
		$sql["get_rca"]="SELECT `member`.`sku`	,`member`.`name`	,`member`.`lastname`,
				`member`.`credit`
			FROM `member` 
			WHERE `member`.`credit`> 0;
		";
		$se=$this->metMnSql($sql,["get_rca"]);
		//print_r($se);
		if(isset($se["data"]["get_rca"])){
			$this->get_rca=$se["data"]["get_rca"];
		}
	}
	private function writeDashboard(array $dt):void{
		$count=$dt["count"][0]["count"];
		$sum=$dt["sum"][0]["sum"];
		$per=0;
		if($count>0){
			$per=round($sum/$count,2);
		}
		echo '<div>
			<div class="account_rca_dashboard"><div>';
		echo '<div class="rca_n_member">จำนวน ลูกหนี้<div>'.number_format($count,0,".",",").'</div></div>
				<div class="rca_n_member">จำนวนเงินค้างชำระรวม<div>฿ '.number_format($sum,2,".",",").'</div></div>
				<div class="rca_n_member">เฉลี่ยค้างชำระ / คน<div>฿ '.number_format($per,2,".",",").'</div></div>';
		echo '</div></div>
		</div>';
	}
	private function writeContentMember(array $data):void{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$dt=$data;
		$se=$dt["get"];
		//print_r($data);
		echo '<form class="form100" name="'.$this->a.'" method="post">
			<input type="hidden" name="member_id" value="" />';
		echo '	<table class="table_view_all_member" style="width:100%;">
				<tr><th>ที่</th>
				<th>รูป</th>
				<th>รหัส</th>
				<th>ชื่อ</th>
				<th>ประเภท</th>
				<th>ค้างชำระ</th>
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
				<td class="l"><a href="?a=member&amp;b=details&amp;sku_root='.$se[$i]["sku_root"].'">'.$name.' '.$lastname.'</a></td>
				<td class="l">'.$this->mb_type[$se[$i]["mb_type"]].'</td>
				<td class="r">'.number_format($se[$i]["credit"],2,'.',',').'</td>
				<td class="action">
						<a onclick="G.action(this)" data-width="350" title="เลือกกระทำ">⚙️</a>
						<a onclick="Ac.toPay(\''.$se[$i]["id"].'\')" title="ชำระเงิน">💰</a>
						<a onclick="location.href=\'?a=account&amp;b=account_history\'" title="ประวัติ ยอดค้างชำระ/ชำระ">🕐</a>
					</td>
				</tr>';
				$this->lid=$se[$i]["id"];
		}
		echo '</table></form>';
		//print_r($dt);
		$count=(isset($dt["count"][0]["count"]))?$dt["count"][0]["count"]:0;
		if($this->txsearch==""){
			$this->page($count,$this->per,$this->page,"?a=".$this->a."&amp;b=".$this->b."&amp;page=");
		}else{
			$this->pageSearch(count($se));
		}
	}
	public function getAllMember():array{
		$where=($this->sh!="")?$this->sh:" WHERE `credit` > 0 ";
		$re=[];
		$sql=[];
		$limit_page=(($this->page-1)*$this->per).",".($this->per+1);
		if($this->txsearch!=""){
				$limit_page=$this->per+1;
		}
		$sql["count"]="SELECT COUNT(*) AS `count`  FROM `member` WHERE `credit` > 0 ";
		$sql["sum"]="SELECT IFNULL(SUM(`credit`),0) AS `sum`  FROM `member` WHERE `credit` > 0 ";
		$sql["get"]="SELECT `id`,`name`,IFNULL(`lastname`,'') AS `lastname`,IFNULL(`icon`,'null.png') AS `icon`,`sku`,`sku_root` ,
				`mb_type`,`credit`
			FROM `member` 
			".($where)." 
			ORDER BY `credit` DESC LIMIT ".$limit_page."";
		$se=$this->metMnSql($sql,["count","sum","get"]);
		//print_r($se);
		if($se["result"]){
			$re=$se["data"];//["get"];
		}
		return $re;
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
			$se=" WHERE `member`.`credit`> 0 AND `member`.`".$fl."` LIKE  \"%".$tx."%\"  ";
		}
		return $se;
	}
	protected function pageSearch(int $row):void{
		$href='?a='.$this->a.'&amp;b='.$this->b.'&amp;fl='.$this->fl.'&amp;tx='.$this->txsearch.'&amp;page=';
		if($this->page>1){
			echo '<a onclick="history.back()">⬅️ก่อนหน้า</a>';
		}
		echo '<span class="member_page_search">หน้า '.$this->page.'</span>';
		if($row>$this->per){
			echo '<a href="'.$href.''.($this->page+1).'&amp;lid='.$this->lid.'">ถัดไป➡️</a>';
		}
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
			$this->sh=" WHERE `member`.`id`".$idsearch." AND `member`.`credit`> 0 AND `member`.`".$fl."` LIKE  \"%".$tx."%\""  ;
		}
	}
	private function cutPerfix(string $json):string{
		$re=[];
		$a=json_decode($json,true);
		
		foreach($a as $k=>$v){
			$re[substr($k,5)]=$v;
		}
		if(count($re)==0){
			$re='{}';
		}else{
			$re=json_encode($re);
		}
		return $re;
	}
}
?>
