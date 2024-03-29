<?php
class ret extends main{
	public function __construct(){
		parent::__construct();
		$this->addDir("?a=ret","คืนสินค้า");
	}
	public function run(){
		if(isset($_POST["sku"])&&preg_match("/^[0-9]{1,25}$/",$_POST["sku"])){
			$this->retFormPage($_POST["sku"],"bill");
		}else if(isset($_POST["sku"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_POST["sku"])){
			$this->retFormPage($_POST["sku"],"sku");
		}else{
			$this->retPage();		
		}
		
	}
	public function fetch(){
		if(isset($_POST["a"])
			&&isset($_POST["changto"])
			&&$_POST["a"]=="ret"
			&&($_POST["changto"]=="0"||$_POST["changto"]=="1")){
				if(isset($_POST["confirm"])&&$_POST["confirm"]!="ok"){
					$se=$this->fetchRet($_POST);
					//exit;
					if($se["result"]&&$se["message_error"]==""){
						$se["resp"]="PCF";
						$se["message_error"]="";
						//print_r($se);
						header('Content-type: application/json');
						echo json_encode($se,JSON_NUMERIC_CHECK);
					}else{//print_r($se);
						header('Content-type: application/json');
						echo json_encode($se,JSON_NUMERIC_CHECK);
					}
				}else if(isset($_POST["confirm"])&&$_POST["confirm"]=="ok"){
					//print_r($_POST);
					$se=$this->fetchRet($_POST);
					//print_r($se);exit;
					$we=$this->fetchRetSave($se);
					header('Content-type: application/json');
					echo json_encode($we);
				}
		}else if(isset($_POST["b"])
			&&$_POST["b"]=="viewlot"
			&&isset($_POST["lot"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_POST["lot"])
			&&isset($_POST["pd_sku_root"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_POST["pd_sku_root"])){
				$se=$this->fetchGetLotView($_POST["lot"],$_POST["pd_sku_root"]);
			
		}else{
			header('Content-type: application/json');
			print_r("{}");
		}
	}
	private function fetchGetLotView(string $lot,string $pd_sku_root){
		$re=["result"=>false,"message_error"=>"","data"=>""];
		$dt=$this-> getInfoLot($lot,$pd_sku_root);
		if(count($dt["lot"])>0){
			$tx=$this->billNote("b",$dt["lot"]["bill_head"]);
			$tx=htmlspecialchars($tx);
			if($dt["lot"]["in_type"]=="c"){
				$tx=$this->billNote("c",'ยกเลิกใบเสร็จ '.$dt["lot"]["bill"].' 📌 '.$tx);
			}else if($dt["lot"]["in_type"]=="r"){
				$tx=$this->billNote("r",'คืนสินค้า ใบเสร็จ '.$dt["lot"]["bill"].' 📌 '.$tx);
			}else{
				$tx=$tx;
			}
			$re["result"]=true;
			$re["data"]=$tx;
			header('Content-type: application/json');
			echo json_encode($re);
		}else{
			$tx="EXEERRT";
			$re["data"]=$tx;
			header('Content-type: application/json');
			echo json_encode($re);
		}
	}
	private function fetchRetSave(array $dt):array{
		//print_r($dt);exit;
		$sku_root=$this->getStringSqlSet($_SESSION["sku_root"]);
		$user=$this->getStringSqlSet($_SESSION["sku_root"]);
		$sku=$this->getStringSqlSet($dt["sku"]);
		$ch2=$this->getStringSqlSet($dt["ch2"]);
		$nt_head=$this->getStringSqlSet($dt["nt_head"]);
		$jspd=$this->getStringSqlSet(json_encode($dt["pd"]));
		$re=["result"=>false,"message_error"=>"","sku"=>""];
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@pd_length:=0,
			@jspd:=".$jspd.",
			@sku:=".$sku.",
			@sku_ret:='',
			@ch2:=".$ch2.",
			@nt_head:=".$nt_head.",
			@user:=".$user.",
			@date:='',
			@TEST:='//-',
			@stkey:='proot',
			@sump_ret:=0,
			@ip:='".$_SESSION["ip"]."',
			@time_id:='".$_SESSION["time_id"]."',
			@money_balance:=(SELECT IFNULL(`money_balance`,0) FROM `device_pos` WHERE `ip`='".$_SESSION["ip"]."' AND `user` = '".$_SESSION["sku_root"]."' AND `onoff` = '1'),
			@drawers_id:=(SELECT `drawers_id` FROM `device_pos` WHERE `ip`='".$_SESSION["ip"]."' AND `user` = '".$_SESSION["sku_root"]."' AND `onoff` = '1'),
			@user_key:=(SELECT `sku_key`  FROM `user` WHERE `sku_root`=".$sku_root." LIMIT 1),
			@member_id:=0,
			@user_id:='".$_SESSION["id"]."',
			@max_id:=(SELECT MAX(`id`)+1 FROM `bill_in`),
			@rca_sum:=-1,
			@has:=(SELECT  COUNT(*)  FROM bill_sell WHERE  sku=@sku);
		";
		$sql["set2"]="
			SET @pd_length=JSON_LENGTH(@jspd),@key=JSON_KEYS(@jspd);
		";
		$sql["run"]="BEGIN NOT ATOMIC 
			DECLARE done INT DEFAULT FALSE;
			DECLARE k CHAR(25) DEFAULT '';
			DECLARE bill_sell_list_id INT DEFAULT 0;
			DECLARE n_r INT DEFAULT 0;
			DECLARE c_r INT DEFAULT 0;
			DECLARE c_r_ed INT DEFAULT 0;
			DECLARE c_r_now INT DEFAULT 0;
			DECLARE c_r_ok INT DEFAULT 0;
			DECLARE n_list INT DEFAULT 0;
			DECLARE sum_cost FLOAT DEFAULT 0;
			DECLARE nt CHAR(255)  CHARACTER SET utf8 DEFAULT '';
			DECLARE ky CHAR(255) DEFAULT '';
			DECLARE lot CHAR(255) DEFAULT '';
			DECLARE lastid INT DEFAULT NULL;	
			DECLARE date_reg CHAR(19) DEFAULT NOW();
			DECLARE r__ INT DEFAULT 0;
			DECLARE __r INT DEFAULT 0;
			DECLARE bill_rca_id INT DEFAULT 0;
			DECLARE credit_cut FLOAT DEFAULT 0;
			DECLARE addsku CHAR(25) CHARACTER SET ascii DEFAULT @sku;
			DECLARE r ROW (id INT,
													lot VARCHAR(25),
													product_sku_key VARCHAR(25),
													product_sku_root VARCHAR(25),
													n INT ,
													n_wlv FLOAT ,
													r INT ,
													unit_sku_key VARCHAR(25),
													unit_sku_root VARCHAR(25),
													s_type CHAR(1),
													lot_root VARCHAR(25),
													pn_key VARCHAR(25),
													pn_root VARCHAR(25),
													cost FLOAT,
													name  VARCHAR(255)  CHARACTER SET utf8,
													price FLOAT
													);
			DECLARE rbill ROW (price FLOAT,credit FLOAT,member_id INT);		
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
			DECLARE cur1 CURSOR FOR 
			SELECT  `bill_sell_list`.`id`,IFNULL(bill_sell_list.lot,''), bill_sell_list.product_sku_key , bill_sell_list.product_sku_root	, 
				bill_sell_list.n,bill_sell_list.n_wlv,bill_sell_list.r,
				 bill_sell_list.unit_sku_key ,bill_sell_list.unit_sku_root,`bill_in_list`.`s_type`,
				 `bill_sell_list`.`lot_root`,`bill_sell_list`.`pn_key`,`bill_sell_list`.`pn_root`,
				 (bill_in_list.sum/IF(bill_in_list.s_type='p',bill_in_list.n,bill_in_list.n_wlv)) AS cost,
				product_ref.name,product_ref.price
				FROM bill_sell_list 
				LEFT JOIN bill_in
				ON(bill_sell_list.lot=bill_in.sku  OR bill_sell_list.lot='')
				LEFT JOIN product_ref
				ON(bill_sell_list.product_sku_key=product_ref.sku_key )
				LEFT JOIN bill_in_list
				ON(bill_sell_list.bill_in_list_id=bill_in_list.id AND bill_in_list.bill_in_sku=bill_sell_list.lot AND bill_sell_list.product_sku_root=bill_in_list.product_sku_root)
				WHERE bill_sell_list.sku=CAST(@sku AS CHAR CHARACTER SET ascii) ;
			DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;			
			#SET @TEST=CONCAT(@TEST,'--',CURTIME(6),'');
			SET @TEST=CONCAT(@TEST,'*',@jspd);
			SET @date=date_reg;
			SET k=CONCAT(@sku,'-',@max_id);
			SET n_list=0;
			FOR i IN 0..(@pd_length-1) DO
				SET done = 0;
				SET bill_sell_list_id=JSON_VALUE(@jspd		,		CONCAT('$['	,		i		,'].id'));
				SET ky=JSON_VALUE(@jspd		,		CONCAT('$['	,		i		,'].pd'));
				SET n_r=JSON_VALUE(@jspd	,CONCAT('$['	,	i	,'].n'));
				SET nt=JSON_VALUE(@jspd	,CONCAT('$['	,	i	,'].nt'));
				SET lot=JSON_VALUE(@jspd	,CONCAT('$['	,	i	,'].lot'));
				OPEN cur1;
					read_loop: LOOP
						FETCH cur1 INTO r;
						IF done THEN
							LEAVE read_loop;
						END IF;
						IF n_r<=r.n -r.r && n_r>0 && bill_sell_list_id = r.id && ky=r.product_sku_root && r.lot=lot THEN
							#SET @TEST=CONCAT(@TEST,IF(LENGTH(lot)>0,8,10),'-',r.lot);
							IF r.cost IS NULL THEN
								SET r.cost=(SELECT cost FROM product_ref WHERE sku_key=r.product_sku_key);
							END IF;
							SET @stkey=(SELECT sku_key FROM  it WHERE sku_root='proot');
							UPDATE `bill_sell_list` SET r=(n_r+r.r),note=IF(LENGTH(nt)>0,nt,NULL)
							WHERE  `bill_sell_list`.`id`=bill_sell_list_id
								AND `bill_sell_list`.`product_sku_root`=ky 
								AND  `bill_sell_list`.`sku`=addsku 
								AND  (`bill_sell_list`.`lot`=lot OR (bill_sell_list.lot IS NULL AND  LENGTH(lot)=0));
							INSERT  INTO `bill_in_list`  (`stkey`,`stroot`,`lot`,`bill_in_sku`,`product_sku_key`,`product_sku_root`,
								`s_type`	,`lot_root`		,`pn_key`	,`pn_root`		,`name`,
								`n`												,`balance`,
								`n_wlv`										,`balance_wlv`,
								`sum`,`unit_sku_key`,`unit_sku_root`,`note`) 
							VALUES(@stkey,'proot',IF(LENGTH(r.lot)>0,r.lot,NULL),	k	,r.product_sku_key	,r.product_sku_root	,
								r.s_type		,r.lot_root		,r.pn_key		,r.pn_root	,r.name	,
								n_r											,IF(r.s_type='p',n_r,NULL),
								IF(r.s_type!='p',r.n_wlv,1)			,IF(r.s_type!='p',n_r*r.n_wlv,NULL),
								(r.cost*n_r*r.n_wlv) ,r.unit_sku_key	,r.unit_sku_root,IF(LENGTH(nt)>0,nt,NULL));
							SET lastid=(SELECT LAST_INSERT_ID());
							IF r__=0 THEN 
								SET r__=lastid;
							ELSE
								SET __r=lastid;
							END IF;
							UPDATE bill_in_list SET sq=lastid WHERE id=lastid;
							UPDATE bill_sell SET costr=(costr+(r.cost*n_r*r.n_wlv)),pricer=(IFNULL(pricer,0)+(r.price*n_r*r.n_wlv)),stat='r',user_edit= @user 
							WHERE bill_sell.sku=addsku;							
							SET @result=1;
							SET n_list=n_list+1;
							SET sum_cost=sum_cost+(r.cost*n_r*r.n_wlv);
							LEAVE read_loop;
						END IF;
					END LOOP;
				CLOSE cur1;
			END FOR;				
			IF __r=0 THEN 
				SET __r=r__;
			END IF;
			INSERT INTO  bill_in  (time_id,in_type,sku,lot_from,lot_root,bill,n,sum,changto,user,note,r_,_r) 
			VALUES (@time_id,'r',k,NULL,NULL,@sku,n_list,sum_cost,'0',@user,@nt_head,r__,__r);
			SET @sku_ret=k;
			SET bill_rca_id=(SELECT `id` FROM `bill_in` WHERE `sku`= @sku_ret LIMIT 1);
			SET @TEST=CONCAT(@TEST,';@sku_ret=',@sku_ret,';@sku=',@sku);
			IF  bill_rca_id> 0 THEN
				SELECT `bill_sell`.`price`,`bill_sell`.`credit`,`member_ref`.`id` AS `member_id`
					INTO rbill.price,rbill.credit,rbill.member_id 
					FROM `bill_sell` 
					LEFT JOIN `member_ref`
					ON(`bill_sell`.`member_sku_key`=`member_ref`.`sku_key`) 
					WHERE `bill_sell`.`sku`=@sku LIMIT 1;
				SET @member_id=rbill.member_id;
				SET @sump_ret=(SELECT  SUM(product_ref.price) AS `product_sum_price`
					FROM `bill_in` 
					LEFT JOIN `bill_in_list` 
					ON(bill_in_list.id>=bill_in.r_ AND bill_in_list.id<=bill_in._r And bill_in.sku=bill_in_list.bill_in_sku)
					LEFT JOIN product_ref
					ON(bill_in_list.product_sku_key=product_ref.sku_key)
					WHERE bill_in.sku=@sku_ret);
				SET @rca_sum=(SELECT SUM(`credit`) FROM `rca` WHERE `member_id`=rbill.member_id);
				IF rbill.credit > 0 THEN
					IF @sump_ret <= rbill.credit THEN
						SET credit_cut=@sump_ret;
					ELSEIF @sump_ret > rbill.credit THEN 
						SET credit_cut=rbill.credit;
					END IF;
					INSERT INTO `tran_rca` (
							`time_id`				,`tran_rca_type`		,`bill_rca_id`					,`min`					,`ip`		,`drawers_id`,
							`member_id`			,`user_id`					,`money_balance`			,`date_reg`
						)VALUES(
							@time_id				,'ret'							,bill_rca_id						,credit_cut					,@ip		,@drawers_id,
							rbill.member_id		,@user_id					,(@rca_sum-credit_cut)	,date_reg
						)
					;	
					UPDATE `rca` SET `credit`= (`credit`-credit_cut) WHERE `bill_sell_id` = @sku;
					UPDATE `member` SET `credit` = (@rca_sum-credit_cut) WHERE `id` = @member_id;
				END IF;
				IF @sump_ret - rbill.credit > 0 THEN
					INSERT INTO `tran`(
							`time_id`		,`mout`							,`tran_type`			,`ref`	,`ip`,
							`drawers_id`	,`user`,
							`money_balance`	,
							`date_reg`
						)VALUES(
							@time_id		,(@sump_ret - rbill.credit)	,'ret'						,@sku_ret	,@ip,
							@drawers_id	,@user_key, 
							(@money_balance-(@sump_ret - rbill.credit)),
							date_reg
						)
					;
					UPDATE `device_pos` 
						SET `money_balance` = (@money_balance-(@sump_ret - rbill.credit))
						WHERE `ip`= @ip;	
				END IF;
			END IF;
			SET @TEST=CONCAT(@TEST,';@sump_ret=',@sump_ret);
			SET @TEST=CONCAT(@TEST,';rbill.price=',rbill.price);
			SET @TEST=CONCAT(@TEST,';rbill.credit=',rbill.credit);
		END;	";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@sku_ret AS `sku`,@TEST AS `TEST`";
		$se=$this->metMnSql($sql,["result"]);
		//echo "ggggggggggg";
		//print_r($se);exit;
		if($se["result"]==1&&$se["message_error"]==""){
			$re["result"]=true;
			$re["sku"]=$se["data"]["result"][0]["sku"];
		}else{
			$re["message_error"]=$se["message_error"];
		}
		return $re;
	}
	private function fetchRet(array $dt):array{
		$re=["result"=>false,"data"=>[],"ch2"=>0,"message_error"=>""];
		$se=$this->fetchRetCheckSet($dt);
		$s=0;
		for($i=0;$i<count($se["list"]);$i++){
			foreach($se["pd"] as $k=>$v){
				if($se["list"][$i]["id"]==$v["id"]
					&&$se["list"][$i]["product_sku_root"]==$v["pd"]
					&&$se["list"][$i]["lot"]==$v["lot"]){
					if($se["pd"][$k]["n"]>0){
						$re["data"][$s]=[
							"name"=>$se["list"][$i]["product_name"]."".($se["list"][$i]["s_type"]!="p"?" ".($se["list"][$i]["n_wlv"]*1)." ".$se["list"][$i]["unit_name"]:""),
							"n"=>$se["pd"][$k]["n"],
							"price"=>$se["list"][$i]["product_price"]*($se["list"][$i]["n_wlv"]*1),
							"nt"=>$se["pd"][$k]["nt"]
						];
						$s+=1;
						break;
					}
				}
			}
		}
		//print_r($se["sku"]);
		if(count($re["data"])>0&&$se["stat"]!="c"){
			$re["result"]=true;
			$re["ch2"]=$se["ch2"];				
			$re["pd"]=$se["pd"];
			$re["sku"]="";
			$re["sku"].=$dt["sku"];
			$re["nt_head"]=$se["nt_head"];
		}else if($se["stat"]=="c"){
			$re["message_error"]="ใบเสร็จนี้ถูกยกเลิกไปแล้ว";
		}else if($s==0){
			$re["message_error"]="ไม่มีรายการที่จะคืนสินค้า โปรดเลือก";
		}
		return $re;
	}
	private function fetchRetCheckSet(array $dt):array{
		$re=["list"=>null,"sku"=>null,"pd"=>[],"ch2"=>0,"stat"=>""];
		$l=count($dt);
		if(isset($dt["sku"])&&preg_match("/^[0-9]{9,25}$/",$dt["sku"])){
			$gdt=$this->getBill($dt["sku"]);
			//print_r($gdt);
			$list=$gdt["list"];
			$s=0;
			foreach($dt as $k=>$v){
				if(substr($k,0,3)=="pd_"){
					$pdf=explode("_lot_",$k);
					$pd=substr($pdf[0],3);
					$nt="";
					if(isset($dt["nt_".$pd."_lot_".$pdf[1]])){
						$nt=(string) $dt["nt_".$pd."_lot_".$pdf[1]];
					}		
					$w=(int) $v;
					$lot=explode("_id_",$pdf[1]);
					$id=base_convert($lot[1],17,10);
					$id=base_convert($id,2, 10);
					$re["pd"][$s++]=["id"=>$id,"pd"=>$pd,"n"=>$w,"nt"=>$nt,"lot"=>$lot[0]];
				}
			}
			if($dt["changto"]=="0"||$dt["changto"]=="1"){
				$re["ch2"]=(int) $dt["changto"];
			}
			if(isset($dt["nt_head"])&&strlen(trim($dt["nt_head"]))>0){
				$re["nt_head"]=$dt["nt_head"];
			}else{
				$re["nt_head"]="";
			}
			$re["sku"]=(string) $dt["sku"];
			$re["list"]=$list;
			$re["stat"]=$gdt["head"]["stat"];
		}
		return $re;
	}
	private function retFormPage(string $sku,string $type="sku"):void{
		$dt=$this->getBill($sku,$type);
		//print_r($dt);
		if($dt["result"]["result"]){
			$this->addDir("?a=ret","ใบเสร็จเลขที่ ".$dt["head"]["sku"]);
			$this->pageHead(["title"=>"คืนสินค้า DIYPOS","css"=>["ret"],"js"=>["ret","Rt"]]);
			$this->writeContentRetForm($dt["head"],$dt["list"]);
			$this->pageFoot();
		}else{
			echo "no";
		}
		
	}
	private function writeContentRetForm(array $head,array $list):void{
		echo '<div class="content">
			<h2 class="c">คืนสินค้า ใบเสร็จเลขที่ '.$head["sku"].'</h2><div>';
		if($head["stat"]=="c"){
			echo '<div class="error">ใบเสร็จนี้ถูกยกเลิกไปแล้ว ไม่สามารถคืนสินค้า</div>';
		}
		echo '	<div class="r">👫 ผู้ขาย : '.htmlspecialchars($head["user_name"]).'
				🕒วันที่ : '.$head["date_reg"].' น.
			</div><div class="r">📃 จำนวน : <b>'.count($list).'</b> รายการ
						💰รวมเงิน : <b>'.$head["price"].'</b> บาท
					</div>';
		if($head["member_sku"]!=""){
			$mbty="?";
			if(isset($this->mb_type[$head["mb_type"]])){
				$mbty=$this->mb_type[$head["mb_type"]];
			}
			echo '<div class="r"><b>🧾 ผู้ซื้อ</b> : <a href="?a=member&amp;b=details&amp;sku_root='.$head["member_sku_root"].'">'.htmlspecialchars($head["member_name"]).'</a> ,
				<b>ประเภท</b> : '.$mbty.' ,
				<b>รหัส</b> : '.$head["member_sku"];
		}else{
			echo '<div class="r">🧾 ผู้ซื้อ : บุคลทั่วไปไม่ใช่สมาชิก</div>';
		}
		echo '<form class="form100" name="ret" method="post" action="?a=retr">
			<input type="hidden" name="a" value="ret" />
			<input type="hidden" name="confirm" value="" />
			<input type="hidden" name="sku" value="'.$head["sku"].'" />
			<table class="ret"><tr><th>ที่</th>	
			<th>สินค้า</th>		
			<th>จำนวน</th>
			<th>ต้องการคืน</th>
			<th>หมายเหตุ</th>
			</tr>';
		for($i=0;$i<count($list);$i++){
			$cm=($i%2!=0)?" class=\"i2\"":"";
			$tre="";
			if($list[$i]["r"]>0){
				$tre='<p class="darkgoldenrod l size11">คืน '.$list[$i]["r"].''.(($list[$i]["s_type"]!="p")?"×".($list[$i]["n_wlv"]*1):"").' '.htmlspecialchars($list[$i]["unit_name"]).' 📌 '.htmlspecialchars($list[$i]["note"]).'</p>';
			}
			$id=base_convert($list[$i]["id"],10,2);
			$id=base_convert($id,10, 17);
			echo '<tr'.$cm.'>
				<td>'.($i+1).'</td>
				<td class="l">'.$list[$i]["product_name"].''.(($list[$i]["s_type"]!="p")?" ".($list[$i]["n_wlv"]*1)." ".$list[$i]["unit_name"]:"").''.$tre.'
					<p><span class="pwlv">'.$this->s_type[$list[$i]["s_type"]]["icon"].'</span>,'.$list[$i]["product_barcode"].' <span>฿'.(number_format($list[$i]["product_price"]*$list[$i]["c"]*$list[$i]["n_wlv"],2,'.',',')).'</span></p>
				</td>
				<td class="r"><a onclick="M.popup(this,\'Rt.infoLot()\');Rt.infoLotFetch(this,\''.$list[$i]["lot"].'\',\''.$list[$i]["product_sku_root"].'\')">'.$list[$i]["c"].'</a></td>
				<td><input type="number" name="pd_'.$list[$i]["product_sku_root"].'_lot_'.$list[$i]["lot"].'_id_'.$id.'" value="0" min="0" max="'.($list[$i]["c"]-$list[$i]["r"]).'" /></td>
				<td><input type="text" name="nt_'.$list[$i]["product_sku_root"].'_lot_'.$list[$i]["lot"].'_id_'.$id.'" value="'.htmlspecialchars($list[$i]["note"]).'" /></td>
			</tr>';
		}	
		echo '<tr>
				<td colspan="6">
					<div><p><span>หัวข้อ:</span></p>
						<input type="text" name="nt_head" value="'.htmlspecialchars($head["note"]).'" />
					<br /><br />
						<select name="changto">
						<option value="0">เปลี่ยนเป็น เงิน</option>
						<!--<option value="1">เปลี่ยนเป็นสินค้าแบบเดิม</option>-->
					</select>
					</div>
				</td>
			</tr></table>
			<p class="c"><input type="button" value="ตกลง" onclick="Rt.submit(this)" /></p>
			</form></div></div>';
	}
	private function getBill(string $sku,string $type="sku"):array{
		$re=["head"=>[],"list"=>[],"result"=>[]];
		$sku=$this->getStringSqlSet($sku);
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@sku:=".$sku.",
			@TEST:='',
			@has:=(SELECT  COUNT(*)  FROM bill_sell WHERE  sku=@sku);
		";
		$sql["head"]="SELECT  `bill_sell`.`sku`  AS  `sku`,`bill_sell`.`n`  AS  `n`, `bill_sell`.`stat`  AS  `stat`, 
				`bill_sell`.`price` AS `price`, `bill_sell`.`modi_date` AS `modi_date`, `bill_sell`.`date_reg` AS `date_reg`,
				CONCAT(`user_ref`.`name`,' ', `user_ref`.`lastname`) AS `user_name`,
				CONCAT(`user_ref2`.`name`,' ', `user_ref2`.`lastname`) AS `user_name_edit`,
				TIMESTAMPDIFF(SECOND,bill_sell.date_reg,bill_sell.modi_date) AS dif,
				IFNULL(NVL2(`member_ref`.`id`,CONCAT(`member_ref`.`name`,' ', `member_ref`.`lastname`),''),'') AS `member_name`,
				IFNULL(`member_ref`.`mb_type`,'') AS `mb_type`,IFNULL(`member_ref`.`sku`,'') AS `member_sku`,
				`member_ref`.`sku_root` AS `member_sku_root`
			FROM `bill_sell` 
			LEFT JOIN `user_ref`
			ON( `bill_sell`.`user`=`user_ref`.`sku_key`)
			LEFT JOIN `user_ref` AS user_ref2
			ON( `bill_sell`.`user_edit`=`user_ref`.`sku_key`)
			LEFT JOIN `member_ref`
			ON( `bill_sell`.`member_sku_key`=`member_ref`.`sku_key`)
			WHERE bill_sell.sku=".$sku." LIMIT 1
		";
		$sql["ret_head"]="SELECT note
			FROM bill_in
			WHERE bill_in.bill=".$sku.";
		";
		$sql["list"]="SELECT  
				bill_sell_list.id,bill_sell_list.lot,bill_sell_list.product_sku_root AS product_sku_root,bill_sell_list.n_wlv,
				IF(bill_sell_list.c>0,bill_sell_list.c,bill_sell_list.u) AS `c`,
				bill_sell_list.r AS `r`,IFNULL(bill_sell_list.note,'') AS `note`,
				product_ref.name AS product_name,product_ref.barcode AS product_barcode,
				product_ref.price AS product_price,product_ref.s_type,
				unit_ref.name AS unit_name
			FROM `bill_sell_list` 
			LEFT JOIN product_ref
			ON( `bill_sell_list`.`product_sku_key`=`product_ref`.`sku_key`)
			LEFT JOIN unit_ref
			ON( bill_sell_list.unit_sku_key=unit_ref.sku_key)
			WHERE bill_sell_list.sku=".$sku." AND bill_sell_list.c>0
			ORDER BY `bill_sell_list`.`id` ASC";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@TEST AS `TEST`";
		$se=$this->metMnSql($sql,["head","ret_head","list","result"]);
		//print_r($se);
		if($se["result"]){
			$re["result"]=$se["data"]["result"][0];
			if(isset($se["data"]["head"][0])&&isset($se["data"]["list"])){
				$re["result"]["result"]=true;
				$re["head"]=$se["data"]["head"][0];
				$re["head"]["note"]=(isset($se["data"]["ret_head"][0]["note"]))?$se["data"]["ret_head"][0]["note"]:"";
				$re["list"]=$se["data"]["list"];
			}
		}else{
			$re["result"]=["result"=>false,"message_error"=>$se["message_error"]];
		}
		//print_r($re);
		return $re;
	}
	private function retPage():void{
		$this->pageHead(["title"=>"คืนสินค้า DIYPOS","css"=>["ret"]]);
				echo '<div class="content">
			<h2 class="c">คืนสินค้า</h2>
			<form name="ret" method="post" action="?a=ret">
				<p><label for="ret_sku">เลขที่ใบเสร็จ</label></p>
				<div><input id="ret_sku" type="text" value="" name="sku" /></div>
				<br /><input type="submit" value="ตกลง" />
			</form>
			<div></div></div>';
		$this->pageFoot();
	}
	private function getInfoLot(string $lot,string $pd_sku_root):array{
		$re=["lot"=>[],"result"=>false,"message_error"=>""];
		$lot=$this->getStringSqlSet($lot);
		$pd_sku_root=$this->getStringSqlSet($pd_sku_root);
		$sql=[];
		$sql["get"]="SELECT bill_in.note AS `bill_head`,bill_in.in_type,bill_in.bill,
			bill_in_list.note
			FROM bill_in
			LEFT JOIN bill_in_list
			ON(bill_in.sku=bill_in_list.bill_in_sku)
			WHERE  bill_in.sku=".$lot." AND bill_in_list.product_sku_root=$pd_sku_root;
		";
		$se=$this->metMnSql($sql,["get"]);
		if(isset($se["data"]["get"][0])){
			$re["result"]=true;
			$re["lot"]=$se["data"]["get"][0];
		}else if(isset($se["message_error"])&&$se["message_error"]!=""){
			$re["message_error"]=$se["message_error"];
		}
		//print_r($se);
		return $re;
	}
}
