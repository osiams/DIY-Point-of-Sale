<?php
class bills_in extends bills{
	public function __construct(){
		parent::__construct();
		$this->per=10;
		$this->page=1;
	}
	public function run(){
		$this->page=$this->setPageR();
		$q=["edit","fill","view","delete"];
		$this->addDir("?a=bills&amp;c=in","ใบนำเข้าสินค้า");
		if(isset($_GET["b"])&&in_array($_GET["b"],$q)){
			//$this->getSelect();
			$t=$_GET["b"];
			if($t=="fill"){
				$this->billsinPage();
			}else if($t=="view"){
				$this->view();
			}else if($t=="edit"){
				$this->editPage();
			}else if($t=="delete"){
				$this->deleteBillsIn();
			}
		}else{
			$this->pageBillsIn();
		}
	}
	public function fetch(){
		if(isset($_POST["b"])&&$_POST["b"]=="fill"){
			$this->fetchBillsinFill();
		}else if(isset($_POST["b"])&&$_POST["b"]=="edit"){
			$this->fetchBillsinEdit();
		}else{
			header('Content-type: application/json');
			print_r("{}");
		}
	}
	private function fetchBillsinEdit():void{
		$error="";
		if(isset($_POST["submith"])&&$_POST["submith"]=="clicksubmit"){
			//echo $_POST["product"];exit;
			$se=$this->billsinCheck("edit");
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				$qe=$this->fetchBillsinUpdate();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					header('Content-type: application/json');
					$d=["result"=>true,"message_error"=>"","data"=>["sku"=>$qe["data"]["result"][0]["sku"]]];
					echo json_encode($d,true);
				}
			}
			if($error!=""){
				$this->fetchBillsinPage($error);
			}
		}else{
			$this->fetchBillsinPage($error);
		}
	}
	private function fetchBillsinUpdate():array{
		$note=$this->getStringSqlSet($_POST["note"]);
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$user=$this->getStringSqlSet($_SESSION["sku_root"]);
		$pd=json_decode($_POST["product"],true);
		$n=0;
		$sum=0;
		//print_r($_POST["sku"]);exit;
		foreach($pd as $k=>$v){
			if((string) $v["act"]!="0"){
				$n+=1;
				$sum+=$v["sum"];
			}
		}
		$sku_root=$this->getStringSqlSet($_SESSION["sku_root"]);
		$jspd=$this->getStringSqlSet($_POST["product"]);
		//print_r($_POST["sku"]);exit;
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@sku:=".$sku.",
			@lot='',
			@jspd:=".$jspd.",
			@pd_length:=0,
			@note:=".$note.",
			@n:=".$n.",
			@sum:=".$sum.",
			@TEST:='',
			@ischange:=0,
			@ischange_lot:='',
			@ischange_pdsku:='',
			@ischange_date:='',
			@user:=(SELECT `sku_key`  FROM `user` WHERE `sku_root`=".$sku_root." LIMIT 1);
		";
		$sql["check"]="
			IF @n = 0 THEN 
				SET @message_error='เกิดขอผิดพลาด จำนวนรายการสินค้า ที่ส่งมา 0 รายการ';
			END IF;			
		";
		$sql["run"]="
			BEGIN NOT ATOMIC 
				DECLARE lastid INT DEFAULT NULL;	
				DECLARE r ROW (
					r__ INT,
					__r INT,
					date_reg TIMESTAMP );
				IF LENGTH(@message_error)=0 THEN
					UPDATE bill_in SET n=@n,sum=@sum,user_edit=@user,note=@note WHERE sku=".$sku.";
					SET @pd_length=JSON_LENGTH(@jspd);
					SELECT r_,_r,date_reg INTO r.r__, r.__r,r.date_reg FROM bill_in WHERE sku=".$sku.";
					SET @date=r.date_reg;
					SET @lot=@sku;
					SET @stkey=(SELECT sku_key FROM  it WHERE sku_root='proot');
					FOR i IN 0..(@pd_length-1) DO
						IF JSON_VALUE(@jspd,CONCAT('$[',i,'].act'))='2' THEN
							UPDATE bill_in_list SET
								name=JSON_VALUE(@jspd,CONCAT('$[',i,'].name')),
								balance=IF(s_type='p',IFNULL(balance,0)-(IFNULL(n,0)-JSON_VALUE(@jspd,CONCAT('$[',i,'].n'))),NULL),
								n=IF(s_type='p',IFNULL(n,0)-(IFNULL(n,0)-JSON_VALUE(@jspd,CONCAT('$[',i,'].n'))),NULL),
								balance_wlv=IF(s_type!='p',IFNULL(balance_wlv,0)-(IFNULL(n_wlv,0)-JSON_VALUE(@jspd,CONCAT('$[',i,'].n'))),NULL),
								n_wlv=IF(s_type!='p',IFNULL(n_wlv,0)-(IFNULL(n_wlv,0)-JSON_VALUE(@jspd,CONCAT('$[',i,'].n'))),NULL),
								sum=JSON_VALUE(@jspd,CONCAT('$[',i,'].sum'))
							WHERE bill_in_list.id>=r.r__ AND bill_in_list.id<=r.__r  AND bill_in_sku=".$sku." AND product_sku_root=JSON_VALUE(@jspd,CONCAT('$[',i,'].sku_root')) LIMIT 1;
							SET @cost=(SELECT sum/IF(s_type='p',bill_in_list.n,bill_in_list.n_wlv) FROM bill_in_list WHERE bill_in_list.id>=r.r__ AND bill_in_list.id<=r.__r  AND bill_in_sku=".$sku." AND product_sku_root=JSON_VALUE(@jspd,CONCAT('$[',i,'].sku_root')) LIMIT 1);
							UPDATE bill_in_list SET
								name=JSON_VALUE(@jspd,CONCAT('$[',i,'].name')),
								sum=@cost*IFNULL(bill_in_list.n,1)*IFNULL(bill_in_list.n_wlv,1)
							WHERE bill_in_list.id>=r.r__ 
								AND lot=".$sku." 
								AND product_sku_root=JSON_VALUE(@jspd,CONCAT('$[',i,'].sku_root')) ;
							IF @ischange=0 THEN 
								SET @ischange=1;
								SET @ischange_lot=@lot;
								SET @ischange_pdsku=JSON_VALUE(@jspd,CONCAT('$[',i,'].sku_root'));
								SET @ischange_date=@date;
							END IF;
							#SET @TEST=CONCAT(@TEST,'-',@error);
						ELSEIF JSON_VALUE(@jspd,CONCAT('$[',i,'].act'))='0' THEN
							DELETE FROM bill_in_list WHERE bill_in_list.id>=r.r__ 
								AND bill_in_list.id<=r.__r AND bill_in_sku=".$sku."  
								AND product_sku_root=JSON_VALUE(@jspd,CONCAT('$[',i,'].sku_root')) 
								AND IF(bill_in_list.s_type='p',bill_in_list.n,bill_in_list.n_wlv)=IF(bill_in_list.s_type='p',bill_in_list.balance,bill_in_list.balance_wlv)
								LIMIT 1 ;
						ELSEIF JSON_VALUE(@jspd,CONCAT('$[',i,'].act'))='4' THEN
							INSERT  INTO `bill_in_list`  (`stkey`,`stroot`,`bill_in_sku`,`product_sku_key`,`product_sku_root`,`name`,`n`,balance,`sum`,`unit_sku_key`,`unit_sku_root`) 
							SELECT  @stkey,'proot',@sku,`product`.`sku_key`,`product`.`sku_root`,
							JSON_VALUE(@jspd,CONCAT('$[',i,'].name')),
							`product`.`s_type`,
							IF(`product`.`s_type`='p',JSON_VALUE(@jspd,CONCAT('$[',i,'].n')),NULL),
							IF(`product`.`s_type`='p',JSON_VALUE(@jspd,CONCAT('$[',i,'].n')),NULL),
							IF(`product`.`s_type`!='p',JSON_VALUE(@jspd,CONCAT('$[',i,'].n')),NULL),
							IF(`product`.`s_type`!='p',JSON_VALUE(@jspd,CONCAT('$[',i,'].n')),NULL),
							JSON_VALUE(@jspd,CONCAT('$[',i,'].sum')),
								`unit`.`sku_key`,
								`product`.`unit` 
							 FROM `product` 
							 LEFT JOIN `unit`
							 ON (`product`.`unit`=`unit`.`sku_root`)
							 WHERE  `product`.`sku_root` =JSON_VALUE(@jspd,CONCAT('$[',i,'].sku_root')) LIMIT 1 ;
							 SET lastid=(SELECT LAST_INSERT_ID());
							UPDATE bill_in_list SET sq=lastid WHERE id=lastid;
							UPDATE bill_in SET _r=lastid WHERE sku=".$sku.";
						END IF;	 
					END FOR;
					IF @ischange=1 THEN 
						SET @TEST=CONCAT(@TEST,'-',@ischange_date);
						CALL ECWEBI_(@ischange_lot,@ischange_pdsku,@ischange_date,@error);
						SET @TEST=CONCAT(@TEST,'--',@ischange_lot,@ischange_pdsku,@ischange_date,@error);
					END IF;
					SET @result=1;
				END IF;
			END;";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@sku AS `sku`,@TEST AS `TEST`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($se);
		return $se;
	}
	private function fetchBillsinPage($error){
		$re=["result"=>false,"message_error"=>""];
		$re["message_error"]=$error;
		$js=json_encode($re);
		header('Content-type: application/json');
		echo $js;
	}
	private function fetchBillsinFill():void{
		$error="";
		if(isset($_POST["submith"])&&$_POST["submith"]=="clicksubmit"){
			//echo $_POST["product"];exit;
			$se=$this->billsinCheck();
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				$qe=$this->fetchBillsinInsert();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					header('Content-type: application/json');
					$d=["result"=>true,"message_error"=>"","data"=>["sku"=>$qe["data"]["result"][0]["sku"]]];
					echo json_encode($d,true);
				}
			}
			if($error!=""){
				$this->fetchBillsinPage($error);
			}
		}else{
			$this->fetchBillsinPage($error);
		}
	}
	private function fetchBillsinInsert():array{
		$note=$this->getStringSqlSet($_POST["note"]);
		$user=$this->getStringSqlSet($_SESSION["sku_root"]);
		$pd=json_decode($_POST["product"],true);
		$n=0;
		$sum=0;
		foreach($pd as $k=>$v){
			$n+=1;
			$sum+=$v["sum"];
		}
		$sku=$this->getStringSqlSet($this->key("key",7));
		$sku_root=$this->getStringSqlSet($_SESSION["sku_root"]);
		$jspd=$this->getStringSqlSet($_POST["product"]);
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@sku:=".$sku.",
			@jspd:=".$jspd.",
			@pd_length:=0,
			@note:=".$note.",
			@n:=".$n.",
			@sum:=".$sum.",
			@stkey:='proot',
			@user:=(SELECT `sku_key`  FROM `user` WHERE `sku_root`=".$sku_root." LIMIT 1);
		";
		$sql["check"]="
			IF @n = 0 THEN 
				SET @message_error='เกิดขอผิดพลาด จำนวนรายการสินค้า ที่ส่งมา 0 รายการ';
			END IF;			
		";
		$sql["run"]="BEGIN NOT ATOMIC 
			DECLARE r__ INT DEFAULT 0;
			DECLARE __r INT DEFAULT 0;
			DECLARE lastid INT DEFAULT NULL;	
			IF LENGTH(@message_error) = 0 THEN
				SET @dateandtime=NOW();
				#SET @yyyymm=CONCAT(YEAR(@dateandtime),LPAD(MONTH(@dateandtime),2,'0'));
				#CALL BILED_ (@dateandtime,@BILED_error);
				SET @stkey=(SELECT sku_key FROM  it WHERE sku_root='proot');
				SET @pd_length=JSON_LENGTH(@jspd);
				FOR i IN 0..(@pd_length-1) DO
					INSERT  INTO `bill_in_list`  (`stkey`,`stroot`,`bill_in_sku`,`product_sku_key`,`product_sku_root`,`name`,`s_type`,`n`,balance,`n_wlv`,`balance_wlv`,`sum`,`unit_sku_key`,`unit_sku_root`) 
					SELECT @stkey, 'proot',@sku,`product`.`sku_key`,`product`.`sku_root`,
						JSON_VALUE(@jspd,CONCAT('$[',i,'].name')),
						`product`.`s_type`,
						IF(`product`.`s_type`='p',JSON_VALUE(@jspd,CONCAT('$[',i,'].n')),NULL),
						IF(`product`.`s_type`='p',JSON_VALUE(@jspd,CONCAT('$[',i,'].n')),NULL),
						IF(`product`.`s_type`!='p',JSON_VALUE(@jspd,CONCAT('$[',i,'].n')),NULL),
						IF(`product`.`s_type`!='p',JSON_VALUE(@jspd,CONCAT('$[',i,'].n')),NULL),
						JSON_VALUE(@jspd,CONCAT('$[',i,'].sum')),
						`unit`.`sku_key`,
						`product`.`unit` 
					 FROM `product` 
					 LEFT JOIN `unit`
					 ON (`product`.`unit`=`unit`.`sku_root`)
					 WHERE  `product`.`sku_root` =JSON_VALUE(@jspd,CONCAT('$[',i,'].sku_root')) LIMIT 1 ;
					 SET lastid=(SELECT LAST_INSERT_ID());
					IF r__=0 THEN 
						SET r__=lastid;
					ELSE
						SET __r=lastid;
					END IF;
					UPDATE bill_in_list SET sq=lastid WHERE id=lastid;
					#CALL CPT_('bill_in_list',CONCAT('bill_in_list.',@yyyymm),'id',lastid,'',@CPT_error);
				END FOR;
				IF __r=0 THEN 
					SET __r=r__;
				END IF;
				IF r__>0 THEN
					INSERT INTO `bill_in`  (in_type,sku,lot_from,lot_root,n,sum,user,note,r_,_r,date_reg) 
					VALUES ('b',@sku,NULL,@sku,@n,@sum,@user,@note,r__,__r,@dateandtime);
				END IF;
				SET @result=1;
			END IF;
		END;";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@sku AS `sku`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($se);
		return $se;
	}
	private function deleteBillsIn():void{
		$error="";
		$note="";
		if(isset($_POST["sku"])&&strlen(trim($_POST["sku"]))>0){
			$sku=$this->getStringSqlSet($_POST["sku"]);
			$sql=[];			
			$sql["set"]="SELECT @result:=0,
				@message_error:='',
				@sku:=".$sku.",
				@note:=(SELECT note FROM `bill_in` WHERE `sku`=@sku LIMIT 1),
				@sum:=(SELECT (SUM(bill_in_list.n)-SUM(bill_in_list.balance)) 
					FROM bill_in_list 
					LEFT JOIN bill_in
					ON(bill_in.sku=@sku)
					WHERE  bill_in_list.id>=bill_in.r_ AND bill_in_list.id<=bill_in._r AND bill_in_list.bill_in_sku=@sku);
			";
			$sql["check"]="
				IF @sum > 0 THEN 
					SET @message_error='สินค้าบางรายการถูกขายไปแล้ว ไม่สามารถลบได้';
				END IF;			
			";
			$sql["run"]="BEGIN NOT ATOMIC 
				DECLARE r ROW (r__ INT,__r INT);
				IF LENGTH(@message_error) = 0 THEN
					SELECT r_,_r INTO r.r__,r.__r FROM bill_in WHERE sku=@sku LIMIT 1;
					DELETE FROM `bill_in_list` WHERE  id>=r.r__ AND id<=r.__r AND `bill_in_sku`=@sku ;
					DELETE FROM `bill_in` WHERE `sku`=@sku  LIMIT 1;
					SET @result=1;
				END IF;
			END;";
			$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@sku AS `sku`,@note AS note,@sum";
			$se=$this->metMnSql($sql,["result"]);
			if($se["result"]&&$se["data"]["result"][0]["result"]==1){
				header('Location:?a=bills&c=in&ed='.$_POST["sku"]);
			}else if($se["message_error"]!=""){
				$error=$se["message_error"]."**";
			}else if($se["data"]["result"][0]["result"]!=""){
				$note=$se["data"]["result"][0]["note"];
				$error=$se["data"]["result"][0]["message_error"];
				
			}
		}
		if($error!=""){
			$this->deletePage($error,$note);
		}		
	}
	private function deletePage(string $error,string $note):void{
		$this->addDir("","ลบ ".htmlspecialchars($note));
		$this->pageHead(["title"=>"ลบนำเข้า สินค้า DIYPOS","js"=>["billsin","Bi"],"css"=>["billsin"]]);
		echo '<div class="content">';
		echo '<p class="error">'.htmlspecialchars($error).'</p>';
		echo '</div>';
		$this->pageFoot();
	}
	private function editPage(){
		
		$se=$this->checkSet("bills",["post"=>["sku"]],"post");
		if(!$se["result"]){
			$error=$se["message_error"];
		}else{
			$dt=$this->getDataView1($_POST["sku"]);
				if(count($dt)>0){
					$this->addDir("","แก้ไข ".htmlspecialchars($dt[0]["note"]));
					$this->pageHead(["title"=>"แก้ไขนำเข้า สินค้า DIYPOS","js"=>["billsin","Bi"],"css"=>["billsin"]]);
						$editable=true;
						if($dt[0]["in_type"]=="c"||$dt[0]["in_type"]=="r"||$dt[0]["in_type"]=="m"){
							$editable=false;
						}
						echo '<div class="content">
							<div class="form">
								<h2 class="c">แก้ไข '.htmlspecialchars($dt[0]["note"]).'</h2>';
						$this->writeContentInBillsinEdit($_POST["sku"],$dt[0]["note"],$editable);
						echo '<br /><p class="c">
							
						</p>';
						echo '</div></div>';
						$this->writeJsDataEdit($dt,$editable);
						$this->pageFoot();
				}else{
					$this->addDir("","แก้ไข");
					$this->pageHead(["title"=>"แก้ไขนำเข้า สินค้า DIYPOS","css"=>["billsin"]]);
					echo '<main><p class="error c">ไม่พบข้อมูล ที่ส่งมา</p></main>';
					$this->pageFoot();
				}
		}
	}
	private function writeJsDataEdit(array $dt,bool $editable=true):void{
		//print_r($dt);
		$br="\n";
		$edb=($editable)?"true":"false";
		echo '<script type="text/javascript">Bi.insertData([';
		for($i=0;$i<count($dt);$i++){
			$name=$this->jsD((string) $dt[$i]["name"]);
			$unit=$this->jsD((string) $dt[$i]["unit_name"]);
			echo  $br.'{"name":"'.$name.'","n":"'.intval($dt[$i]["n"]).'","balance":"'.intval($dt[$i]["balance"]).'","sum":"'.number_format($dt[$i]["sum"],2,".","").'","bcsku":"'.$dt[$i]["barcode"].' , '.$dt[$i]["product_sku"].'","sku_root":"'.$dt[$i]["sku_root"].'","unit":"'.$unit.'","s_type":"'.$dt[$i]["s_type"].'","price":"'.$dt[$i]["price"].'","cost":"'.$dt[$i]["cost"].'"},';
		}
		echo '],'.$edb.');</script>';
	}
	private function  jsD(string  $t):string{
		$t=str_replace('\\','\\\\',$t);
		$t=str_replace('"','\"',$t);
		$t=str_replace("\n","",$t);
		return $t;
	}
	private function writeContentInBillsinEdit(string $sku,string $note,bool $editable=true):void{
		echo '<form class="form100"  name="billsin" method="post">
			<input type="hidden" name="sku" value="'.htmlspecialchars($sku).'" />
			<div class="billsin_note_edit"><input type="text" name="note" value="'.htmlspecialchars($note).'" /></div>
			<table id="billsin" data-type="edit"><tr><th>ที่</th>
			<th>ชื่อ</th>
			<th>จำนวน</th>
			<th>หน่วย</th>
			<th>ราคารวม<br />ราคาต่อชิ้น</th>
			<th>กระ<br />ทำ</th>
			</tr>';
		if($editable){		
			echo '<tr>
				<td colspan="6">
					<label>
					<select name="fl">
						<option value="sku">รห้สภายใน</option>
						<option value="barcode">รห้สแท่ง</option>
						<option value="name" selected="selected">ชื่อ</option>
					</select>
					<input type="text" name="tx" onkeydown="Bi.productInSearch(event);"/>
					<input type="button" onclick="Bi.productInSearch()" value="ค้นหา" /></label>
					</td>
			</tr>';
		
			echo '<tr><td colspan="6" style="font-size:0px;padding:0px;">
				<div class="iframe"><iframe id="iframeproductin" title="เลือกสินค้า" src="?a=product&amp;b=select&amp;for=billsin" class="iframe_product_in"></iframe></div>
				</td></tr>';
		}
		$et=($editable)?"true":"false";
		echo '</table>
		<br />
		<input type="button" onclick="Bi.billsinSumit('.$et.')" value="แก้ไข นำเข้าสินค้า" /></form>';
	}
	private function billsinCheck(string $type="insert"):array{
		$re=["result"=>false,"message_error"=>""];
		if(!isset($_POST["product"])){
			$re["message_error"]="ไม่มีสินค้าที่เลือก";
		}else if(!isset($_POST["note"])){
			$re["message_error"]="ไม่มรายละเอียดอย่างย่อ";
		}else if(gettype(json_decode($_POST["product"],true))!="array"){
			$re["message_error"]="สินค้าที่เลือกหรือข้อมูลที่ส่งมาไม่อยู่ในรูปแบบ";
		}else if(strlen($_POST["note"])>$this->fills["note"]["length_value"]-3){
			$re["message_error"]="รายละเอียดอย่างย่อ ยาวเกินไป";
		}else if(!isset($_POST["sku"])&&$type=="edit"){
			$re["message_error"]="ไม่พบใบนำเข้าที่ส่งมา";
		}else if(isset($_POST["sku"])&&strlen(trim($_POST["sku"]))==0&&$type=="edit"){
			$re["message_error"]="รหัสชี่ใบนำเข้าว่าง";
		}else{
			$re["result"]=true;
		}
		return $re;
	}
	private function billsinPage(){
		$this->addDir("?a=bills&amp;b=fill&amp;c=in","นำเข้าสินค้า ");
		$this->pageHead(["title"=>"นำเข้า สินค้า DIYPOS","js"=>["billsin","Bi"],"css"=>["billsin"]]);
			echo '<div class="content">
				<div class="form">
					<h1 class="c">นำเข้าสินค้า</h1>';
			$this->writeContentInBillsin();
			echo '<br /><p class="c">
				
			</p>';
			echo '</div></div>';
			$this->pageFoot();
	}
	private function writeContentInBillsin():void{
		echo '<form class="form100"  name="billsin" method="post">
			<input type="hidden" name="sku" value="" />
			<table id="billsin"><tr><th>ที่</th>
			<th>ชื่อ</th>
			<th>จำนวน</th>
			<th>หน่วย</th>
			<th>ราคารวม<br />ราคาต่อชิ้น</th>
			<th>กระทำ</th>
			</tr>';
		echo '<tr id="trsearch">
				<td colspan="6">
					<label>
					<select name="fl">
						<option value="sku">รห้สภายใน</option>
						<option value="barcode">รห้สแท่ง</option>
						<option value="name" selected="selected">ชื่อ</option>
					</select>
					<input type="text" name="tx" onkeydown="Bi.productInSearch(event);"/>	
					<input type="button" onclick="Bi.productInSearch()" value="ค้นหา" />
					</label>
					</td>
			</tr>
			<tr><td colspan="6" style="font-size:0px;padding:0px;">
			<div class="iframe"><iframe id="iframeproductin" title="เลือกสินค้า" src="?a=product&amp;b=select&amp;for=billsin" class="iframe_product_in"></iframe></div>
		</td></table>
		<br />
		<input type="button" onclick="Bi.billsinSumit()" value="นำเข้าสินค้าเพิ่ม" /></form>';
	}
	private function pageBillsIn():void{
		$this->pageHead(["title"=>"ใบนำเข้าสินค้า DIYPOS","js"=>["billsin","Bi"],"css"=>["billsin"]]);
		echo '<div class="content">
				<div class="form">
					<h1 class="c">ใบนำเข้าสินค้า</h1>';
			$this->writeContentBillsIn();
			echo '</div></div>';
		$this->pageFoot();
	}
	private function writeContentBillsIn():void{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$sea=$this->getAllBillsIn();
		$se=$sea["row"];
		echo '<form class="form100" name="billsin" method="post">
			<input type="hidden" name="sku" value="" />
			<table id="billsin"><tr><th>ที่</th>
			<th>บันทึกย่อ</th>
			<th>รก.</th>
			<th>จำนวนเงิน</th>
			<th>ผู้บันทึก</th>
			<th>วันบันทึก</th>
			<th>กระทำ</th>
			</tr>';
		for($i=0;$i<count($se);$i++){
			$ed='';
			if($se[$i]["sku"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}
			$cm=($i%2!=0)?" class=\"i2\"":"";
			$tx="";
			if($se[$i]["in_type"]=="c"){
				$tx=$this->billNote("c",''.$se[$i]["bill"],$se[$i]["note"]);
			}else if($se[$i]["in_type"]=="r"){
				$tx=$this->billNote("r",''.$se[$i]["bill"],$se[$i]["note"]);
			}else if($se[$i]["in_type"]=="m"){
				$tx=$this->billNote("m",''.$se[$i]["sku"],$se[$i]["note"]);
			}else if($se[$i]["in_type"]=="b"){
				$tx=$this->billNote("b",''.$se[$i]["note"],'');
			}
			echo '<tr'.$cm.'><td>'.$se[$i]["id"].'</td>
				<td class="l">
					<div><a href="?a=bills&amp;b=view&amp;c=in&amp;sku='.$se[$i]["sku"].'">'.$tx.'</a></div>
					<div>'.$se[$i]["user_name"].' '.substr($se[$i]["date_reg"],0,-3).'</div>
				</td>
				<td class="r">'.$se[$i]["n"].'</td>
				<td class="r">'.number_format($se[$i]["sum"],2,'.',',').'</td>
				<td class="l">'.$se[$i]["user_name"].'</td>
				<td>'.$se[$i]["date_reg"].'</td>
				<td class="action">
					<a onclick="Bi.billsInEdit(\''.$se[$i]["sku"].'\')" title="แก้ไข">📝</a>
					<a onclick="Bi.billsInDelete(\''.$se[$i]["sku"].'\',\''.htmlspecialchars($se[$i]["note"]).'\')" title="ทิ้ง">🗑</a>
					'.$ed.'
					</td>
				</tr>';
		}
		echo '</table>
		</form>';
		$count=(isset($sea["count"]))?$sea["count"]:0;
		$this->page($count,$this->per,$this->page,"?a=bills&amp;c=in&amp;page=");
		echo '<br /><input type="button" value="นำเข้า สินค้า" onclick="location.href=\'?a=bills&b=fill&c=in\'" />';
	}
	private function getAllBillsIn():array{
		$re=[];
		$sql=[];
		$sql["count"]="SELECT @count:=COUNT(*) FROM bill_in WHERE bill_in.in_type='b' ";
		$sql["get"]="SELECT  `bill_in`.`id`  AS  `id`,`bill_in`.`in_type`  AS  `in_type`,`bill_in`.`sku`  AS  `sku`,IFNULL(`bill_in`.`bill`,bill_in.id)  AS  `bill`,IFNULL(`bill_in`.`note`,'')  AS  `note`, 
				SUM(`bill_in`.`n`) AS `n`, SUM(`bill_in`.`sum`) AS `sum`, `bill_in`.`date_reg` AS `date_reg`,
				CONCAT(`user_ref`.`name`,' ', `user_ref`.`lastname`) AS `user_name`
			FROM `bill_in` 
			LEFT JOIN `user_ref`
			ON( `bill_in`.`user`=`user_ref`.`sku_key`)
			WHERE bill_in.in_type='b' 
			GROUP BY bill_in.date_reg
			ORDER BY `bill_in`.`id` DESC LIMIT ".(($this->page-1)*$this->per).",".$this->per."";
		$sql["result"]="SELECT @count AS count";	
		$se=$this->metMnSql($sql,["get","result"]);
		if($se["result"]){
			$re=["row"=>$se["data"]["get"],"count"=>$se["data"]["result"][0]["count"]];
		}
		return $re;
	}
	private function view():void{
		$error="";
		if(isset($_GET["sku"])){
			$se=$this->checkSet("bills",["get"=>["sku"]],"get");
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				$this->viewPageDefault($error,$_GET["sku"]);
			}
			if($error!=""){
				$this->viewPageDefault($error);
			}
		}else{
			$this->billsinPage();
		}
	}
	private function viewPageDefault(string $error,string $sku=null):void{
		
		$dt=$this->getDataView1($sku);
		if(count($dt)>0){
			$tx="";
			$c="in";
			if($dt[0]["in_type"]=="c"){
				$tx=$this->billNote("c",''.$dt[0]["bill"],$dt[0]["note"]);
				$c="sell";
			}else if($dt[0]["in_type"]=="r"){
				$tx=$this->billNote("r",''.$dt[0]["bill"],$dt[0]["note"]);
				$c="ret";
			}else if($dt[0]["in_type"]=="m"){
				$tx=$this->billNote("m",''.$dt[0]["sku"],$dt[0]["note"]);
				$c="move";
			}else if($dt[0]["in_type"]=="b"){
				$tx=$this->billNote("b",''.$dt[0]["note"],'');
				$c="in";
			}
			$txdir=$tx.'';
			if(count($dt)>0){
				$this->addDir("?a=bills&amp;b=view&amp;c=in&amp;sku=".$dt[0]["sku"],"".$txdir);
			}
			$this->pageHead(["title"=>"รายละเอียด DIYPOS","js"=>["billsin","Bi"],"css"=>["billsin"]]);
			echo '<div class="content">';
			if(count($dt)>0){
				echo '<h2 class="c"> '.htmlspecialchars($dt[0]["note"]).'</h2>';
				$aut="";
				if($dt[0]["in_type"]=="c"||$dt[0]["in_type"]=="r"){
					$key=$dt[0]["sku"];
					if($dt[0]["in_type"]=="c"){
						$key=$dt[0]["bill"];
					}
					$tx='<a href="?a=bills&amp;b=view&amp;c='.$c.'&amp;sku='.$key.'">'.$tx.'</a>';
					$aut='<div class="warning">นำเข้าอัตโนมัติ จาก '.$tx.'</div>';
				}
				$this->writeContentVeiew($dt,$aut);
			}else{
				echo '<div class="error">ไม่พบข้อมูล</div>';
			}
			echo '</div>';
			$this->pageFoot();
		}else{
			$this->pageHead(["title"=>"รายละเอียด DIYPOS","js"=>["billsin","Bi"],"css"=>["billsin"]]);
			echo '<div class="warning c">ไม่พบข้อมูล</div>';
			$this->pageFoot();
		}
	}
	private function writeContentVeiew(array $dt,string $aut=""):void{
		//print_r($dt);
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		echo '<div>
			'.$aut.'
			<table class="page r"><tr><td>บันทึกโดย : '.$dt[0]["user_name"].' วันที่ '.$dt[0]["date_reg"].'
				<br />⏳ผ่านมาแล้ว '.$this->ago(time()-strtotime($dt[0]["date_reg"])).'
				</td></tr><tr><td>
			<table  id="billinlist" class="l"><tr>
				<th>ที่</th>
				<th>ป.</th>
				<th>รหัสแท่ง</th><th>สินค้า</th><th>จำนวน</th>
				<th>คงเเหลือ<sup class="q" onclick="M.tooltups(this,\'นับเฉพาะ ในคลังสินค้าพร้อมขาย หมายเหตุ 100/105 >>100 = จำนวนในงวดนี้ , 105 = รวมจำนวนทั้งหมด (รวมงวดอื่นด้วย)\')">?</sup></th>
				<th>หน่วย</th>
			<th>ราคา / หน่วย</th><th>ราคารวม</th>
			</tr>';
		$tl=0;
		for($i=0;$i<count($dt);$i++){
			$pu=($dt[$i]["sum"]/$dt[$i]["n"]);
			$putx=number_format($pu, 2, '.', ',');
			$tl+=$dt[$i]["sum"];
			$sumtx=number_format($dt[$i]["sum"], 2, '.', ',');
			$cl=(($i%2)!=0)?" class=\"i2\"":"";
			$bl=(($i%2)!=0)?"i2":"";
			if($dt[$i]["product_sku_root"]==$edd){
				$cl=' class="ed"';
			}
			echo '<tr'.$cl.'><td>'.($i+1).'</td>
			<td class="pwlv">'.$this->s_type[$dt[$i]["s_type"]]["icon"].'</td>
			<td class="l">'.$dt[$i]["barcode"].'</td>
			<td><div>'.htmlspecialchars($dt[$i]["name"]).'</div>
				<div>'.$dt[$i]["barcode"].'</div>
			</td>
			<td class="r"><div>'.($dt[$i]["n"]*1).'</div>
				<div>'.$dt[$i]["unit_name"].'</div>
			</td>
			<td class="r billinbalance'.$bl.'">'.($dt[$i]["balance"]*1).'/<a href="?a=it&amp;b=view&amp;sku_root=proot&amp;c=lot&amp;pd='.$dt[$i]["product_sku_root"].'">'.($dt[$i]["sum_balance"]*1).'</a></td>
			<td>'.$dt[$i]["unit_name"].'</td>
			<td class="r">'.$putx.'</td>
			<td class="r">'.$sumtx.'</td>
			</tr>';
		}
		$tltx=number_format($tl, 2, '.', ',');
		echo '</td></tr></table></td</tr>
			<tr><td>จำนวน '.count($dt).' รายการ , รวมเงิน <b>'.$tltx.'</b> บ.</td></tr></table>
			</div>';
		
	}
	private function getDataView1(string $sku):array{
		$sku=$this->getStringSqlSet($sku);
		$re=[];
		$sql=[];
		$sql["set"]="SELECT @date_reg:=(SELECT date_reg FROM bill_in WHERE sku=".$sku." ),
			@bill:=(SELECT IFNULL(bill,'') FROM bill_in WHERE sku=".$sku.")
		";
		$sql["get"]="BEGIN NOT ATOMIC 
			DECLARE r ROW (r__ INT,__r INT);
			SELECT r_,_r INTO r.r__,r.__r FROM bill_in WHERE sku=@sku LIMIT 1;
			SELECT  `bill_in`.`id`  AS  `id`,`bill_in`.`in_type`  AS  `in_type`,`bill_in`.`sku`  AS  `sku`,IFNULL(`bill_in`.`note`,'')  AS  `note`, 
				`bill_in`.`bill`  AS  `bill`,`bill_in`.`n` AS `n_list`, `bill_in`.`sum` AS `sum`, `bill_in`.`date_reg` AS `date_reg`,
				CONCAT(`user_ref`.`name`,' ', `user_ref`.`lastname`) AS `user_name`,
				bill_in_list.id AS bill_in_list_id,bill_in_list.product_sku_key  AS `pd_sku_root`,bill_in_list.product_sku_root,bill_in_list.product_sku_root AS sku_root ,
				IF(`bill_in_list`.`s_type`='p',bill_in_list.n,bill_in_list.n_wlv) AS `n` ,
				IF(`bill_in_list`.`s_type`='p',bill_in_list.balance,bill_in_list.balance_wlv) AS `balance`,bill_in_list.sum ,bill_in_list.name,
				IFNULL(SUM(IF(`bill_in_list2`.`s_type`='p',bill_in_list2.balance,bill_in_list2.balance_wlv)),0) AS `sum_balance`,
				unit_ref.name AS unit_name,
				product_ref.barcode AS barcode,product_ref.sku AS product_sku,`product_ref`.`s_type`,
				product.price,product.cost
			FROM `bill_in` 
			LEFT JOIN bill_in_list 
			ON(bill_in_list.id>=bill_in.r_ AND bill_in_list.id<=bill_in._r  AND bill_in.sku=bill_in_list.bill_in_sku AND bill_in_list.stroot='proot' )		
			LEFT JOIN bill_in_list AS bill_in_list2
			ON(IF(bill_in_list2.s_type='p',bill_in_list2.balance,bill_in_list2.balance_wlv)>0 AND bill_in_list.product_sku_root=bill_in_list2.product_sku_root  AND bill_in_list2.stroot='proot')			
			LEFT JOIN `user_ref`
			ON( `bill_in`.`user`=`user_ref`.`sku_key`)
			

			LEFT JOIN unit_ref
			ON(bill_in_list.unit_sku_key=unit_ref.sku_key)
			LEFT JOIN product_ref
			ON(bill_in_list.product_sku_key=product_ref.sku_key)
			LEFT JOIN product
			ON(bill_in_list.product_sku_root=product.sku_root)
			WHERE bill_in.in_type='b' AND bill_in.sku=".$sku."
			GROUP BY bill_in_list.product_sku_root
			ORDER BY `bill_in_list`.`id` ASC ;
		END;";
		$se=$this->metMnSql($sql,["get"]);
		if($se["result"]){
			$re=$se["data"]["get"];
		}
		//print_r($se);
		return $re;
	}
}
?>
