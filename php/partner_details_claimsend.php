<?php
class partner_details_claimsend extends main{
	public function __construct(){
		parent::__construct();
		$this->dir=null;
		$this->sku_root=null;
		$this->r_more=null;
		$this->a="partner";
		$this->url="?a=partner&amp;b=details";
	}
	public function run(){
		$this->url.="&amp;sku_root=".$this->sku_root."&amp;bb=partner_details_claimsend";
		$this->addDir($this->url,"สินส่งเคลมแล้ว");
		if(isset($_GET["viewbill"])){
			if($this->isSKU($_GET["viewbill"])){
				require("php/partner_details_claimsend_viewbill.php");
				$d=new partner_details_claimsend_viewbill();
				$d->dir=$this->dir;
				$d->sku_root=$_GET["sku_root"];	
				$d->bill_sku=$_GET["viewbill"];	
				$d->url=$this->url;	
				$d->r_more=$this->r_more;
				$d->run();
			}else{
				$this->detailsPage();
			}
		}else{
			$this->detailsPage();
		}
		
	}
	public function fetch(){
		$re=["result"=>false,"message_error"=>""];
		if(true){
			$se=$this->claimSend();
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
	private function claimSend():array{
		//$_POST["list"]='{"1311042":6,"1311044":2,"1311049":1,"1311050":1,"1311052":3}';
		$re=["result"=>false,"message_error"=>""];
		$sku_root=(isset($_POST["sku_root"])&&$this->isSKU($_POST["sku_root"]))?$_POST["sku_root"]:"";
		$list=(isset($_POST["list"])&&$this->isJSON($_POST["list"]))?$_POST["list"]:"";
		$ckck_key_value=$this->checkRightKeyValue($list);
		if($sku_root!=""&&$list!=""&&$ckck_key_value){	
			$pn_root=$this->getStringSqlSet($_POST["sku_root"]);
			$list=$this->getStringSqlSet($_POST["list"]);
			$a= array_keys(json_decode($_POST["list"],true));
			$where_in=substr($this->getStringSqlSet("(".implode(", ",$a).")"),1,-1);
			$sql=[];
			$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@pn_root:=".$pn_root.",
			@list_json:=".$list.",
			@TEST:='',
			@has_pn:=(SELECT COUNT(*) FROM `partner` WHERE `sku_root`='".$pn_root."')
			";
			$sql["run"]="BEGIN NOT ATOMIC 
				DECLARE done INT DEFAULT FALSE;
				DECLARE r ROW (id INT(10),
					`stroot` CHAR(25),
					`s_type` CHAR(1),
					`balance` INT(10),
					`balance_wlv` FLOAT(10,4),
					`n` INT(10),
					`n_wlv` FLOAT(10,4)
				);
				DECLARE list_length INT(10) default 0;
				DECLARE list_key TEXT default '[]';
				DECLARE id_ INT default 0;
				DECLARE cur1 CURSOR FOR 
					SELECT `id`,stroot,s_type,balance,balance_wlv,n,n_wlv FROM bill_in_list
					WHERE `id` IN ".$where_in." ;#AND `claim_stat`='w' AND `pn_root`=@pn_root AND `stroot`='croot`;
				DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
				SET @TEST=CONCAT(@TEST,@pn_root);
				OPEN cur1;
					read_loop: LOOP
						FETCH cur1 INTO r;
						IF done THEN
							LEAVE read_loop;
						END IF;
						SET @n_=IFNULL(JSON_VALUE(@list_json		,		CONCAT('$.'	,		r.id		,'')),-1);
						SET @TEST=CONCAT(@TEST,';',r.n,'-',@n_);
						IF @n=-1 THEN
							SET @message_error='เกิดข้อผิดพลาด ไม่พบค่าไอดีที่ส่งมา';
							SET done = TRUE;
						ELSEIF r.s_type='p' && MOD(@n_,1) > 0 THEN
							SET @message_error='เกิดข้อผิดพลาด สินค้าขายเป็นชิ้น ต้องเป็นจำนวนเต็ม';
							SET done = TRUE;
						ELSEIF r.s_type='p' && @n_>r.n THEN
							SET @message_error='เกิดข้อผิดพลาด จำนวนสินค้าบางรายการเกินจำนวนที่มีอยู่จริง';
							SET done = TRUE;
						ELSEIF r.s_type!='p' && @n_>r.n_wlv THEN
							SET @message_error='เกิดข้อผิดพลาด จำนวนสินค้าบางรายการเกินจำนวนที่มีอยู่จริง';
							SET done = TRUE;
						END IF;
						#	SET @TEST=CONCAT(@TEST,';',@n);
					END LOOP;
				CLOSE cur1;
				IF @message_error='' THEN
					SET done = FALSE;
					OPEN cur1;
						read_loop: LOOP
							FETCH cur1 INTO r;
							IF done THEN
								LEAVE read_loop;
							END IF;
							SET @n_=IFNULL(JSON_VALUE(@list_json		,		CONCAT('$.'	,		r.id		,'')),-1);
							IF r.s_type='p' && @n_=r.n THEN
								UPDATE `bill_in_list` SET `claim_stat`='s' 
								WHERE `id`=r.id;
							ELSEIF r.s_type!='p' && @n_=r.n_wlv THEN
								UPDATE `bill_in_list` SET `claim_stat`='s' 
								WHERE `id`=r.id;
							END IF;
							
						END LOOP;
					CLOSE cur1;
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
			$re["message_error"]="เกิดข้อผิดพลาดรูปแบบที่ส่งมามีบางอย่าไม่ถูกต้อง เช่น ไม่ใช่ตัวเลข หรือไม่ได้เลือกอะไรเลย";
		}
		
		return $re;
	}
	private function checkRightKeyValue(string $json):bool{
		$re=true;
		$q=json_decode($json,true);
		foreach($q as $k=>$v){
			$pt="/^[0-9]{1,10}$/";
			$pt2="/^[0-9]{1,9}((.?[0-9]{1,9})?)$/";
			if(!preg_match($pt,$k)) {
				$re=false;
				break;
			}
			if(!preg_match($pt2,(string) $v)) {
				$re=false;
				break;
			}		
			if((float) $v<=0) {
				$re=false;
				break;
			}		
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
					<h1 class="c">'.$pn_name.'</h1>';
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
		$bill=$this->detailsGetBill();
		echo '<table>
			<tr>
				<th>ที่</th>
				<th>เลขที่</th>
				<th>รายการ</th>
				<th>มูลค่า(ทุน)</th>
				<th>ผู้ส่งเคลม</th>
				<th>วันที่</th>
			</tr>';
		for($i=0;$i<count($bill);$i++){
			echo '<tr>
				<td class="l">'.($i+1).'.</td>
				<td><a href="?a=partner&amp;b=details&sku_root='.$this->sku_root.'&amp;bb=partner_details_claimsend&amp;viewbill='.$bill[$i]["sku"].'">'.$bill[$i]["sku"].'</a></td>
				<td>'.$bill[$i]["n"].'</td>
				<td class="r">'.number_format($bill[$i]["cost"],2,".",",").'</td>
				<td>'.htmlspecialchars($bill[$i]["user_name"]).'</td>
				<td>'.$bill[$i]["date_reg"].'</td>
			</tr>';
		}
		echo '</table>';

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
		$re=["get"=>[]];
		$sql=[];
		$sql["bill"]="SELECT 
			`bill_claim`.`sku`	,`bill_claim`.`date_reg`,
			`bill_claim`.`n`	,`bill_claim`.`cost`,
			CONCAT(`user_ref`.`name`,' ',`user_ref`.`lastname`) AS `user_name`
			FROM `bill_claim`
			LEFT JOIN `user_ref`
			ON(`bill_claim`.`user`=`user_ref`.`sku_key`)
			WHERE `bill_claim`.`pn_root`=".$sku_root." AND `bill_claim`.`claim_stat`='s';
		";
		$se=$this->metMnSql($sql,["bill"]);
		if($se["result"]){
			$re=$se["data"]["bill"];
		}
		//print_r($se);
		return $re;
	}
}
