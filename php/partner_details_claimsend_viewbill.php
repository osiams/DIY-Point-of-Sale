<?php
class partner_details_claimsend_viewbill extends main{
	public function __construct(){
		parent::__construct();
		$this->dir=null;
		$this->sku_root=null;
		$this->bill_sku=null;
		$this->r_more=null;
		$this->a="partner";
		$this->url=null;
	}
	public function run(){
		$this->url.="&amp;viewbill=".htmlspecialchars($this->bill_sku);
		$this->addDir($this->url,"เลขที่ ".htmlspecialchars($this->bill_sku));
		$this->detailsPage();
	}
	public function fetch(){
		$re=["result"=>false,"message_error"=>""];
		if(true){
			$se=$this->claimCancel();
			if($se["result"]){
				$re["result"]=true;
				header('Content-type: application/json');
				echo json_encode($re);			
			}else{
				$re["message_error"]=$se["message_error"];
				header('Content-type: application/json');
				echo json_encode($re);			
			}
		}else{
			header('Content-type: application/json');
			echo json_encode($re);				
		}
	}
	private function claimCancel():array{
		//$_POST["list"]='{"1311042":6,"1311044":2,"1311049":1,"1311050":1,"1311052":3}';
		$re=["result"=>false,"message_error"=>""];
		$sku_root=(isset($_POST["sku_root"])&&$this->isSKU($_POST["sku_root"]))?$_POST["sku_root"]:"";
		$bill_sku=(isset($_POST["bill_sku"])&&$this->isSKU($_POST["bill_sku"]))?$_POST["bill_sku"]:"";
		$pt="/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/";
		$date_reg=(isset($_POST["date_reg"])&&preg_match($pt,$_POST["date_reg"]))?$_POST["date_reg"]:"";
		$ckck_value=true;
		if($sku_root==""||$bill_sku==""||$date_reg==""){
			echo $sku_root."-".$bill_sku."-".$date_reg."**";
			$ckck_value=false;
		}
		if($ckck_value){	
			$pn_root=$this->getStringSqlSet($_POST["sku_root"]);
			$bill_sku=$this->getStringSqlSet($_POST["bill_sku"]);
			$date_reg=$this->getStringSqlSet($_POST["date_reg"]);
			$sql=[];
			$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@pn_root:=".$pn_root.",
			@bill_sku:=".$bill_sku.",
			@date_reg:=".$date_reg.",
			@TEST:='',
			@has_bill:=(SELECT COUNT(*) FROM `bill_claim` WHERE `sku`=".$bill_sku."
				AND  `pn_root`=".$pn_root." 
				AND `date_reg`=".$date_reg.")
			";
			$sql["run"]="BEGIN NOT ATOMIC 
				DECLARE done INT DEFAULT FALSE;
				DECLARE bill_claim_id_ INT DEFAULT 0;
				DECLARE bill_in_list_id_ INT DEFAULT 0;
				DECLARE cur1 CURSOR FOR
					SELECT `bill_claim_list`.`bill_in_list_id` 
					FROM `bill_claim`
					LEFT JOIN `bill_claim_list`
					ON(`bill_claim`.`id`=`bill_claim_list`.`bill_claim_id`)
					WHERE `bill_claim`.`sku`=@bill_sku AND `bill_claim`.`claim_stat`='s';
				DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;	
				IF @has_bill > 0 THEN
				SET @TEST=CONCAT(@TEST,';@has_bill=',@has_bill);
					SELECT `id` INTO bill_claim_id_ FROM `bill_claim` WHERE `sku`=@bill_sku;
					IF bill_claim_id_>0 THEN
						OPEN cur1;
							read_loop: LOOP
								FETCH cur1 INTO bill_in_list_id_;
								IF done THEN
									LEAVE read_loop;
								END IF;
								SET @TEST=CONCAT(@TEST,';bill_claim_id_=',bill_claim_id_);
								UPDATE `bill_claim_list` SET `claim_stat`='r'
								WHERE `bill_claim_id`=bill_claim_id_;
								UPDATE `bill_in_list` SET `claim_stat`='w'
								WHERE `id`=bill_in_list_id_ ;
							END LOOP;
						CLOSE cur1;
						UPDATE `bill_claim` SET `claim_stat`='r'
						WHERE `sku`=@bill_sku ;						
					END IF;
					CALL SPNCN_(@pn_root);
					SET @result=1;
				END IF;
			END;	";
			$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@TEST AS `TEST`";
			$se=$this->metMnSql($sql,["result"]);
			
			//print_r($se);
			if($se["result"]&&$se["data"]["result"][0]["result"]){
				$re["result"]=true;
				$re["message_error"]=$se["data"]["result"][0]["message_error"];
			}else{
				$re["message_error"]=$se["data"]["result"][0]["message_error"];
			}
		}else{
			$re["message_error"]="เกิดข้อผิดพลาดรูปแบบที่ส่งมามีบางอย่าไม่ถูกต้อง เช่น ไม่ใช่ตัวเลข";
		}
		
		return $re;
	}
	private function detailsPage():void{
		$dt=$this->detailsGetData()["partner"];
		if(count($dt)>0){
			$pn_name=$dt["name"];
			$this->pageHead(["title"=>"คู่ค้า ".htmlspecialchars($pn_name),"css"=>["partner"],"js"=>["partner","Pn"],"run"=>["Pn"],"r_more"=>$this->r_more]);
			echo '<div class="content_rmore">
				<div class="form">
					<h1 class="c">ใบเคลม เลขที่ '.htmlspecialchars($this->bill_sku).'</h1>';
			$this->writeContentProduct();
			$this->pageFoot();
		}else{
			$pn_name="ไม่พบข้อมูลคู่ค้า";
			$this->addDir("",htmlspecialchars($pn_name));
			$this->pageHead(["title"=>"คู่ค้า ".htmlspecialchars($pn_name),"css"=>["partner"],"js"=>["partner","Pn"],"run"=>["Pn"]]);
			echo '<div class="content">
				<div class="form">
					<h1 class="c">'.$pn_name.'</h1>';
			$this->pageFoot();
		}
	}

	private function writeContentProduct():void{
		$dt=$this->detailsGetBill();
		//print_r($dt);
		$head=$dt["head"];
		$bill=$dt["bill"];
		echo '<table>
			<tr class="nohover"><td>';
		if($head["claim_stat"]=="s"){
			echo '	<div class="warning">ใบเคลมนี้ ได้ส่งสินค้าเคลมแล้ว กำลังรอรับสินค้าเคลม</div>
			<div style="float:right"><input type="button" value="ยกเลิก" onclick="Pn.claimCancel(\''.$this->sku_root.'\',\''.$head["bill_sku"].'\',\''.$head["date_reg"].'\')" /></div>';
		}else if($head["claim_stat"]=="r"){
			echo '<div class="success">ใบเคลมนี้ ได้รับสินค้าเคลมแล้ว และนำเข้าสินค้าแล้ว
				<br><a href="?a=bills&amp;b=view&amp;c=in&amp;&amp;in_type=cl&amp;sku='.$dt["bill_po_sku"].'">ดูใบนำเข้าสินค้า</a></div>';
		}
		echo 	'<div class="l">คู่ค้า : <a href="?a=partner&amp;b=details&amp;sku_root='.$head["pn_root"].'">'.$head["pn_name"].'</a></div>
			<div class="l">บันทึกโดย : '.$head["user_name"].'</div>
			<div class="l">วันที่ : '.$head["date_reg"].'</div>
			<table class="table_claim_list_product">
			<tr>
				<th>ที่</th>
				<th>ชื่อสินค้า/รหัสแท่ง</th>
				<th>จำนวน</th>
				<th>หน่วย</th>
			</tr>';
		for($i=0;$i<count($bill);$i++){
			$n_txt=0;
			if($bill[$i]["s_type"]=="p"){
				$n_txt=''.number_format($bill[$i]["n_send"],0,".",",").'';
			}else{
				$n_txt=''.number_format($bill[$i]["n_wlv_send"],3,".",",").'';
			}
			echo '<tr>
				<td class="l">'.($i+1).'.</td>
				<td class="l">
					<a href="?a=product&amp;b=details&amp;sku_root='.$bill[$i]["pd_root"].'">'.htmlspecialchars($bill[$i]["pd_name"]).'</a>
					<p>'.$bill[$i]["pd_barcode"].'</p>
				</td>
				<td class="r">'.$n_txt.'</td>
				<td class="r">'.htmlspecialchars($bill[$i]["unit_name"]).'</td>
			</tr>';
		}
		echo '</table>';
		echo '</div><br /><img src="?a=bill58&amp;b=viewclaim&amp;sku='.$this->bill_sku.'" class="imgbill" alt="ใบเสร็จเลขที่ '.$this->bill_sku.'" /><br />
					<a onclick="M.printAgain(\'bill58\',\'print_claim\',\''.$this->bill_sku.'\')">🖨 พิมพ์อีกครั้ง</a><br />
					<br />
		</div></td></tr></table>';
	}
	private function detailsGetData(){
		$sku_root=$this->getStringSqlSet($this->sku_root);
		$re=["partner"=>[]];
		$sql=[];
		$sql["partner"]="SELECT * FROM `partner`
			WHERE `sku_root`=".$sku_root.";
		";
		$se=$this->metMnSql($sql,["partner"]);
		if($se["result"]&&isset($se["data"]["partner"][0])){
			$re["partner"]=$se["data"]["partner"][0];
		}
		return $re;
	}
	private function detailsGetBill(){
		$re=[];
		$sku_root=$this->getStringSqlSet($this->sku_root);
		$bill_sku=$this->getStringSqlSet($this->bill_sku);
		$re=["get"=>[]];
		$sql=[];
		$sql["head"]="SELECT 
			`bill_claim`.`sku` AS `bill_sku`,`bill_claim`.`claim_stat` AS `claim_stat`,
			`partner_ref`.`name` AS `pn_name`,
			CONCAT(`user_ref`.`name`,' ',`user_ref`.`lastname`) AS `user_name`,
			`bill_claim`.`pn_root`,
			`bill_claim`.`date_reg` AS `date_reg`
			FROM `bill_claim`
			LEFT JOIN `partner_ref`
			ON(`bill_claim`.`pn_key`=`partner_ref`.`sku_key`)
			LEFT JOIN `user_ref`
			ON(`bill_claim`.`user`=`user_ref`.`sku_key`)
			WHERE `bill_claim`.`sku`=$bill_sku ;
		";
		$sql["bill"]="SELECT 
			`bill_claim`.`sku`	,`bill_claim`.`date_reg`,
			`bill_claim`.`n`	AS `n_list`,`bill_claim`.`cost`,
			`bill_claim_list`.`n` AS `n_send`,
			IFNULL(`bill_claim_list`.`n`,0) AS `n_send`,
			IFNULL(`bill_claim_list`.`n_wlv`,0) AS `n_wlv_send`,
			`bill_in_list`.`product_sku_root` AS `pd_root`,
			`bill_in_list`.`s_type` AS `s_type`,
			`product_ref`.`name` AS `pd_name`,`product_ref`.`barcode` AS `pd_barcode`,
			CONCAT(`user_ref`.`name`,' ',`user_ref`.`lastname`) AS `user_name`,
			`unit_ref`.`name` AS `unit_name`
			FROM `bill_claim`
			LEFT JOIN `bill_claim_list`
			ON(`bill_claim`.`id`=`bill_claim_list`.`bill_claim_id`)
			LEFT JOIN `bill_in_list`
			ON(`bill_claim_list`.`bill_in_list_id`=`bill_in_list`.`id`)
			LEFT JOIN `user_ref`
			ON(`bill_claim`.`user`=`user_ref`.`sku_key`)
			LEFT JOIN `product_ref`
			ON(`bill_in_list`.`product_sku_key`=`product_ref`.`sku_key`)
			LEFT JOIN `unit_ref`
			ON(`bill_in_list`.`unit_sku_key`=`unit_ref`.`sku_key`)
			WHERE `bill_claim`.`sku`=".$bill_sku." ORDER BY `bill_in_list`.`name` ASC ,`n_send` DESC;
		";
		$sql["bill_po_sku"]="SELECT `bill_in`.`sku`
			FROM `bill_in`
			WHERE `bill_in`.`bill_po_sku`=".$bill_sku.";
		
		";
		$se=$this->metMnSql($sql,["head","bill","bill_po_sku"]);
		if($se["result"]){
			$ref="";
			if(count($se["data"]["bill_po_sku"])==0){
				
			}else{
				$ref=$se["data"]["bill_po_sku"][0]["sku"];
			}
			$re=["bill"=>$se["data"]["bill"],"head"=>$se["data"]["head"][0],"bill_po_sku"=>$ref];
			
		}
		//print_r($re);
		return $re;
	}
}
