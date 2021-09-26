<?php
class partner_details_claim extends main{
	public function __construct(){
		parent::__construct();
		$this->dir=null;
		$this->sku_root=null;
		$this->r_more=null;
		$this->a="partner";
		$this->url="?a=partner&amp;b=details";
	}
	public function run(){
		$this->url.="&amp;sku_root=".$this->sku_root;
		$this->addDir("","สินค้าต้องส่งเคลม");
		$this->detailsPage();
	}
	public function fetch(){
		$re=["result"=>false,"message_error"=>""];
		if(true){
			$se=$this->claimSend();
			//print_r($se);
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
		$user_root=$this->getStringSqlSet($_SESSION["sku_root"]);
		if($sku_root!=""&&$list!=""&&$ckck_key_value){	
			$pn_root=$this->getStringSqlSet($_POST["sku_root"]);
			$list=$this->getStringSqlSet($_POST["list"]);
			$a= array_keys(json_decode($_POST["list"],true));
			$where_in=substr($this->getStringSqlSet("(".implode(", ",$a).")"),1,-1);
			$sql=[];
			$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@pn_root:=".$pn_root.",
			@pn_key:=(SELECT `sku_key` FROM `partner` WHERE `sku_root`=".$pn_root."),
			@list_json:=".$list.",
			@time_id:='".$_SESSION["time_id"]."',
			@TEST:='',
			@user_id:=".$_SESSION["id"].",
			@has_pn:=(SELECT COUNT(*) FROM `partner` WHERE `sku_root`='".$pn_root."'),
			@user:=(SELECT `sku_key`  FROM `user` WHERE `sku_root`=".$user_root." LIMIT 1);
			";
			$sql["run"]="BEGIN NOT ATOMIC 
				DECLARE done INT DEFAULT FALSE;
				DECLARE bill_claim_id_ INT(10) default 0;
				DECLARE bill_claim_sku_ CHAR(25) default '';
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
					SET bill_claim_id_=(SELECT IFNULL((SELECT MAX(id) FROM `bill_claim`),0)+1);
					SET bill_claim_sku_=(SELECT CONCAT('00',LPAD(CAST(bill_claim_id_ AS CHAR(25)),7,'0')));
					OPEN cur1;
						read_loop: LOOP
							FETCH cur1 INTO r;
							IF done THEN
								LEAVE read_loop;
							END IF;
							SET @n_=IFNULL(JSON_VALUE(@list_json		,		CONCAT('$.'	,		r.id		,'')),-1);
							IF r.s_type='p'   THEN
								IF @n_=r.n THEN
									UPDATE `bill_in_list` SET `claim_stat`='s' WHERE `id`=r.id;
								END IF;
								INSERT INTO `bill_claim_list`(
									`bill_claim_id`	,`bill_in_list_id`	,`claim_stat`	,`n`	,`n_wlv`
								)VALUES(
									bill_claim_id_	,r.id						,'s'	,@n_	,NULL
								);
							ELSEIF r.s_type!='p'  THEN
								IF @n_=r.n_wlv THEN
									UPDATE `bill_in_list` SET `claim_stat`='s' WHERE `id`=r.id;
								END IF;
								INSERT INTO `bill_claim_list`(
									`bill_claim_id`	,`bill_in_list_id`	,`claim_stat`	,`n`	,`n_wlv`
								)VALUES(
									bill_claim_id_	,r.id						,'s'			,null	,@n_
								);
							END IF;
							
						END LOOP;
						SET @count_pd_sku_root_dist=(
							SELECT COUNT(DISTINCT (`bill_in_list`.`product_sku_root`)) 
							FROM `bill_claim_list` 
							LEFT JOIN `bill_in_list`
							ON(`bill_claim_list`.`bill_in_list_id`=`bill_in_list`.`id`)
							WHERE `bill_claim_list` .`bill_claim_id`=bill_claim_id_
						);
						SET @sum_cost=(
							SELECT SUM(`bill_in_list`.`sum`*(IF(`bill_in_list`.`s_type`='p',`bill_claim_list`.`n`,`bill_claim_list`.`n_wlv`)/(
								IF(`bill_in_list`.`s_type`='p',`bill_in_list`.`n`,`bill_in_list`.`n_wlv`)
							))) 
							FROM `bill_claim_list` 
							LEFT JOIN `bill_in_list`
							ON(`bill_claim_list`.`bill_in_list_id`=`bill_in_list`.`id`)
							WHERE `bill_claim_list` .`bill_claim_id`=bill_claim_id_
						);
						INSERT INTO `bill_claim` (
							`id`		,`time_id`	,`sku`	,`claim_stat`	,n,
							`cost`	,`pn_key`	,`pn_root`		,`user`	,`note`
						)VALUES(
							bill_claim_id_		,@time_id		,bill_claim_sku_		,'s'	,@count_pd_sku_root_dist,
							@sum_cost	,@pn_key ,@pn_root	,@user ,NULL
						);						
						CALL SPNCN_(@pn_root);
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
			$re["message_error"]="เกิดข้อผิดพลาดรูปแบบที่ส่งมามีบางอย่าไม่ถูกต้อง เช่น ไม่ใช่ตัวเลข หรือมีค่าว่าง ไม่ได้เลือก";
		}
		
		return $re;
	}
	private function checkRightKeyValue(string $json):bool{
		$re=true;
		$q=json_decode($json,true);
		
		if(gettype($q)!="array"){
			$re=false;
		}else{
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
		$pd=$this->detailsGetProduct();
		echo '<form class="form100" name="claim_form"><table class="table_details_claim">
			<input type="hidden" name="sku_root" value="'.htmlspecialchars($this->sku_root).'" />
			<caption>สินค้าต้องส่งเคลม</caption>
			<tr><th></th><th>ที่</th><th>ป.</th><th>ชื่อ</th><th>จำนวน</th><th>ส่งเคลม</th></tr>';
		for($i=0;$i<count($pd);$i++){
			
			$s_type=($pd[$i]["s_type"]!==""&&isset($this->s_type[$pd[$i]["s_type"]]))?$this->s_type[$pd[$i]["s_type"]]["icon"]:"";
			$n=0;
			if($pd[$i]["s_type"]=="p"){
				$n=number_format($pd[$i]["balance"]-$pd[$i]["sum_n_send"],0,".",",");
			}else{
				$n=number_format($pd[$i]["balance_wlv"]-$pd[$i]["sum_n_wlv_send"],3,".",",");
			}
			echo '<tr class="bg_yes_claim">
				<td><input type="checkbox"  name="cb_'.$pd[$i]["id"].'" value="'.$pd[$i]["id"].'" onchange="Pn.setChangeClaim(this)" checked></td>
				<td>'.($i+1).'</td>
				<td>'.$s_type.'</td>
				<td class="l">
					<a href="?a=product&amp;b=details&amp;sku_root='.$pd[$i]["sku_root"].'">'.htmlspecialchars($pd[$i]["name"]).'</a>
					<p>'.$pd[$i]["barcode"].'</p>
				</td>
				<td class="r">'.$n.'</td>
				<td class="r"><input type="number" value="'.$n.'" dir="rtl" name="nb_'.$pd[$i]["id"].'"></td>
			</tr>';
		}
		echo '</table>';
		echo '<br /><p class="c"><input type="button" value="ส่งเคลมเดียวนี้" onclick="Pn.claimSend(\'claim_form\')" /></p>';
		echo '</form>';
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
	private function detailsGetProduct(){
		$sku_root=$this->getStringSqlSet($this->sku_root);
		$re=["get"=>[]];
		$sql=[];
		$sql["product"]="
			SELECT  `bill_in_list`.`id`,
				`product_ref`.`sku_root`,
				`product_ref`.`name`	,`product_ref`.`barcode`	,IFNULL(`bill_in_list`.`s_type`,'') AS `s_type`,
				IF(`bill_in_list`.`s_type`='p',IFNULL(`bill_in_list`.`balance`,0),0) AS `balance`,
				IF(`bill_in_list`.`s_type`!='p',IFNULL(`bill_in_list`.`balance_wlv`,0),0) AS `balance_wlv`,
				SUM(IF(`bill_in_list`.`s_type`='p',IFNULL(`bill_claim_list`.`n`,0),0)) AS `sum_n_send`,
				SUM(IF(`bill_in_list`.`s_type`!='p',IFNULL(`bill_claim_list`.`n_wlv`,0),0)) AS `sum_n_wlv_send`
			FROM `bill_in_list`
			LEFT JOIN `product_ref`
			ON(`bill_in_list`.`product_sku_key`=`product_ref`.`sku_key`)
			LEFT JOIN `bill_claim_list`
			ON(`bill_in_list`.`id`=`bill_claim_list`.`bill_in_list_id` AND `bill_claim_list`.`claim_stat`='s')
			
			WHERE `bill_in_list`.`stroot`='croot' 
				AND `bill_in_list`.`claim_stat`='w' 
				AND `bill_in_list`.`pn_root`=".$sku_root."
			GROUP BY `bill_in_list`.`id`;
		";
		//,`barcode`,`sku_root`,IFNULL(`s_type`,'') AS `s_type` FROM `product`
		//	WHERE JSON_SEARCH(`partner`, 'one', ".$sku_root.") = ".$sku_root." AND ;
		$se=$this->metMnSql($sql,["product"]);
		if($se["result"]){
			$re=$se["data"]["product"];
		}
		//print_r($se);
		return $re;
	}
}
