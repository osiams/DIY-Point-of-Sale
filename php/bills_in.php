<?php
class bills_in extends bills{
	public function __construct(){
		parent::__construct();
		$this->per=10;
		$this->page=1;
		$this->form_py=null;
	}
	public function run(){
		$this->page=$this->setPageR();
		$q=["edit","fill","view","delete"];
		$this->addDir("?a=bills&amp;c=in","ใบนำเข้าสินค้า");
		if(isset($_GET["b"])&&in_array($_GET["b"],$q)){
			//$this->getSelect();
			$t=$_GET["b"];
			if($t=="fill"||$t=="edit"){
				$file = "php/form_selects.php";
				require($file);		
				if($t=="edit"){
					$gall = "php/gallery.php";
					require($gall);	
				}
			}
			if($t=="fill"){
				$q=["po","partner"];
				if(isset($_GET["pn_partner"])&&in_array($_GET["pn_partner"],$q)){
					if($_GET["pn_partner"]=="partner"
						&&isset($_GET["sku_root"])
						&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["sku_root"])){
						$this->billsinPage("partner",$_GET["sku_root"]);
					}else{
						$this->billsinPage();
					}
				}else{
					$this->billsinPage();
				}
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
		$bill_no=$this->getStringSqlSet($_POST["bill_no"]);
		$bill_type=$this->getStringSqlSet($_POST["bill_type"]);
		$bill_date=$this->getStringSqlSet($_POST["bill_date"]." 00:00:00");
		$_POST["gallery_list"]=isset($_POST["gallery_list"])?$_POST["gallery_list"]:"";
		$icon_arr=$this->setPropR($this->getStringSqlSet($_POST["gallery_list"]));
		$pd=json_decode($_POST["product"],true);
		$n=0;
		$sum=0;
		$vat=0;
		foreach($pd as $k=>$v){
			$n+=1;			
			$vt=0;
			if($bill_type=='"v0"'){
				$vt=((float) $v["vat_p"]/100)* (float) $v["sum"];
				$vat+=$vt;
				$sum+=$v["sum"]+$vt;
			}else{
				$vt= (float) $v["sum"] - ((float) $v["sum"]*100/(100+(float) $v["vat_p"]));
				$vat+=$vt;
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
			@bill_no:=".$bill_no.",
			@bill_type:=".$bill_type.",
			@bill_date:=".$bill_date.",
			@icon_arr:=".$this->getStringSqlSet(json_encode($icon_arr)).",
			@lot='',
			@jspd:=".$jspd.",
			@pd_length:=0,
			@note:=".$note.",
			@n:=".$n.",
			@sum:=".$sum.",
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
					UPDATE bill_in 
						SET 	n=@n						,sum=@sum				,user_edit=@user				,bill_no=@bill_no,
								bill_type=@bill_type	,bill_date=@bill_date,
								icon_arr=JSON_UNQUOTE(@icon_arr)	,note=@note 
						WHERE sku=".$sku.";
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
		$pn=$this->getStringSqlSet($_POST["pn"]);
		$bill_no=$this->getStringSqlSet($_POST["bill_no"]);
		$bill_type=$this->getStringSqlSet($_POST["bill_type"]);
		$bill_date=$this->getStringSqlSet($_POST["bill_date"]." 00:00:00");
		$user=$this->getStringSqlSet($_SESSION["sku_root"]);
		$bill_type=$this->getStringSqlSet($_POST["bill_type"]);
		$min=0;
		$mout=0;
		$pd=json_decode($_POST["product"],true);
		$n=0;
		$sum=0;
		$vat=0;
		foreach($pd as $k=>$v){
			$n+=1;			
			$vt=0;
			if($bill_type=='"v0"'){
				$vt=((float) $v["vat_p"]/100)* (float) $v["sum"];
				$vat+=$vt;
				$sum+=$v["sum"]+$vt;
			}else{
				$vt= (float) $v["sum"] - ((float) $v["sum"]*100/(100+(float) $v["vat_p"]));
				$vat+=$vt;
				$sum+=$v["sum"];
			}
		}

		$sku=$this->getStringSqlSet($this->key("key",7));
		$sku_root=$this->getStringSqlSet($_SESSION["sku_root"]);
		$jspd=$this->getStringSqlSet($_POST["product"]);
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@TEST:='',
			@message_error:='',
			@sku:=".$sku.",
			@jspd:=".$jspd.",
			@pd_length:=0,
			@bill_no:=".$bill_no.",
			@bill_type:=".$bill_type.",
			@bill_date:=".$bill_date.",
			@vat_n:=".$vat.",
			@note:=".$note.",
			@pn_root:=".$pn.",
			@pn_key:='',
			@n:=".$n.",
			@sum:=".$sum.",
			@stkey:='proot',
			@time_id:='".$_SESSION["time_id"]."',
			@partner_id:=(SELECT `id` FROM `partner` WHERE `sku_root`=@pn_root),
			@user_id:=".$_SESSION["id"].",
			@user:=(SELECT `sku_key`  FROM `user` WHERE `sku_root`=".$sku_root." LIMIT 1);
		";
		//print_r($sql);exit;
		$sql["check"]="
			IF @n = 0 THEN 
				SET @message_error='เกิดขอผิดพลาด จำนวนรายการสินค้า ที่ส่งมา 0 รายการ';
			END IF;			
		";
		$sql["run"]="BEGIN NOT ATOMIC 
			DECLARE r__ INT DEFAULT 0;
			DECLARE __r INT DEFAULT 0;
			DECLARE lastid INT DEFAULT NULL;	
			DECLARE lastid_bill_in INT DEFAULT 0;
			DECLARE now TIMESTAMP DEFAULT NOW();
			IF LENGTH(@message_error) = 0 THEN
				SET @stkey=(SELECT sku_key FROM  it WHERE sku_root='proot');
				SET @pn_key=(SELECT sku_key FROM  partner WHERE sku_root=@pn_root);
				SET @pd_length=JSON_LENGTH(@jspd);
				FOR i IN 0..(@pd_length-1) DO
					INSERT  INTO `bill_in_list`  (
						`stkey`				,`stroot`		,`bill_in_sku`			,`product_sku_key`		,`product_sku_root`,
						`name`				,`s_type`		,`n`						,balance						,`n_wlv`,
						`balance_wlv`	,`sum`			,`unit_sku_key`		,`unit_sku_root`) 
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
					INSERT INTO `bill_in`  (
						time_id		,in_type		,sku			,lot_from		,lot_root		,n							,sum,
						pn_root		,pn_key		,bill_no			,bill_type	,user				,note	,
						vat_n			,bill_date,
						r_				,_r			,date_reg) 
					VALUES (
						@time_id	,'b'				,@sku		,NULL			,@sku			,@n						,@sum,
						@pn_root	,@pn_key	,@bill_no		,@bill_type	,@user		,@note,
						@vat_n		,@bill_date,
						r__			,__r			,now);
					SET lastid_bill_in=(SELECT LAST_INSERT_ID());
				END IF;
				SET @result=1;
			END IF;
		END;";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@sku AS `sku`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($se);
		return $se;
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
				@icon_gl:=(SELECT icon_gl FROM `bill_in` WHERE `sku`=@sku LIMIT 1),
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
					DELETE FROM `gallery` WHERE `gl_key`=@sku;
					SET @result=1;
				END IF;
			END;";
			$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@sku AS `sku`,@note AS note,@sum,@icon_gl AS `icon_gl`";
			$se=$this->metMnSql($sql,["result"]);
			if($se["result"]&&$se["data"]["result"][0]["result"]==1){
				$files=json_decode($se["data"]["result"][0]["icon_gl"]);
				if(!is_array($files)){
					$files=[];
				}
				$this->delImgs($files);
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
			//echo $_POST["sku"];
			//print_r($dt);
				if(count($dt)>0){
					$this->addDir("","แก้ไขใบนำเข้าสินค้า ".htmlspecialchars($dt[0]["partner_name"])." 🧾".$dt[0]["bill_no"]);
					$this->pageHead(["title"=>"แก้ไขนำเข้า สินค้า DIYPOS","js"=>["billsin","Bi","form_selects","Fsl","fileupload","Ful","gallery","Gl"],"css"=>["billsin","form_selects","fileupload","gallery"],"run"=>["Fsl"]]);
						$editable=true;
						if($dt[0]["in_type"]=="c"||$dt[0]["in_type"]=="r"||$dt[0]["in_type"]=="m"){
							$editable=false;
						}
						echo '<div class="content">
							<div class="form">
								<h2 class="c">แก้ไข '.htmlspecialchars($dt[0]["partner_name"])." 🧾".$dt[0]["bill_no"].'</h2>';
						$this->writeContentInBillsinEdit($_POST["sku"],$dt,$editable);
						echo '<br /><p class="c">
							
						</p>';
						echo '</div></div>';
						//$this->writeJsDataEdit($pd,$editable,$sku_root,$id,$product_list_id);
						//$this->writeJsDataEdit($dt,$editable,null);
						$this->pageFoot();
				}else{
					$this->addDir("","แก้ไข");
					$this->pageHead(["title"=>"แก้ไขนำเข้า สินค้า DIYPOS","css"=>["billsin"]]);
					echo '<main><p class="error c">ไม่พบข้อมูล ที่ส่งมา</p></main>';
					$this->pageFoot();
				}
		}
	}
	private function writeJsDataEdit(array $dt,bool $editable=true,string $partner=null,string $id=null,string $product_list_id=null):void{
		$tid=($id!=null)?"\"".$id."\"":"null";
		$pid=($product_list_id!=null)?"\"".$product_list_id."\"":"null";
		$br="\n";
		$edb=($editable)?"true":"false";
		echo '<script type="text/javascript">Bi.insertData([';
		for($i=0;$i<count($dt);$i++){
			$name=$this->jsD((string) $dt[$i]["name"]);
			$unit=$this->jsD((string) $dt[$i]["unit_name"]);
			echo  $br.'{"name":"'.$name.'","n":"'.intval($dt[$i]["n"]).'","balance":"'.intval($dt[$i]["balance"]).'","sum":"'.number_format($dt[$i]["sum"],2,".","").'","bcsku":"'.$dt[$i]["barcode"].' , '.$dt[$i]["product_sku"].'","sku_root":"'.$dt[$i]["sku_root"].'","unit":"'.$unit.'","s_type":"'.$dt[$i]["s_type"].'","price":"'.$dt[$i]["price"].'","cost":"'.$dt[$i]["cost"].'","vat_p":"'.number_format($dt[$i]["vat_p"],2,".","").'"},';
		}
		echo '],'.$edb.','.$tid.','.$pid.');';
		if($partner!=null){
			echo 'Bi.partner="'.$partner.'";';
		}
		echo 'Fsl.selectPartnerListValue("product","'.$id.'","'.$product_list_id.'");';
		echo '</script>';
	}
	private function writeJsDataEditPrompt(array $dt,bool $editable=true,string $partner=null,string $id=null,string $product_list_id=null):void{
		$tid=($id!=null)?"\"".$id."\"":"null";
		$pid=($product_list_id!=null)?"\"".$product_list_id."\"":"null";
		$br="\n";
		$edb=($editable)?"true":"false";
		echo '<script type="text/javascript">Bi.insertDataPrompt([';
		for($i=0;$i<count($dt);$i++){
			$name=$this->jsD((string) $dt[$i]["name"]);
			$unit=$this->jsD((string) $dt[$i]["unit_name"]);
			echo  $br.'{"name":"'.$name.'","n":"'.intval($dt[$i]["n"]).'","balance":"'.intval($dt[$i]["balance"]).'","sum":"'.number_format($dt[$i]["sum"],2,".","").'","bcsku":"'.$dt[$i]["barcode"].' , '.$dt[$i]["product_sku"].'","sku_root":"'.$dt[$i]["sku_root"].'","unit":"'.$unit.'","s_type":"'.$dt[$i]["s_type"].'","price":"'.$dt[$i]["price"].'","cost":"'.$dt[$i]["cost"].'","vat_p":"'.number_format($dt[$i]["vat_p"],2,".","").'"},';
		}
		echo '],'.$edb.','.$tid.','.$pid.');';
		if($partner!=null){
			echo 'Bi.partner="'.$partner.'";';
		}
		echo 'Fsl.selectPartnerListValue("product","'.$id.'","'.$product_list_id.'");';
		echo '</script>';
	}
	private function jsD(string  $t):string{
		$t=str_replace('\\','\\\\',$t);
		$t=str_replace('"','\"',$t);
		$t=str_replace("\n","",$t);
		return $t;
	}
	private function writeContentInBillsinEdit(string $sku,array $dt,bool $editable=true):void{
		//print_r($dt);
		$gallery=$this->propToFromValue($dt[0]["icon_arr"]);
		$gallery_gl=$this->propToFromValue($dt[0]["icon_gl"]);
		$product_arr=[];
		for($i=0;$i<count($dt);$i++){
			array_push($product_arr,$dt[$i]["product_sku_key"]);
		}
		$product_str="[\"".implode(",,",$product_arr)."\"]";
		$product_str=str_replace(",,","\",\"",$product_str);
		$product=$this->propToFromValue($product_str);
		
		$product_list_id=$this->key("key",7);
		$gallery_list_id=$this->key("key",7);
		$gallery_gl_list_id=$this->key("key",7);
		//echo $sku."**";
		echo '<form class="form100"  name="billsin" method="post">
			<input type="hidden" name="sku" value="'.htmlspecialchars($sku).'" />
			<input type="hidden" id="'.$product_list_id.'" name="product_list" value="'.$product.'" />
			<input type="hidden" id="'.$gallery_list_id.'" name="gallery_list" value="'.$gallery.'" />
			<input type="hidden" id="'.$gallery_gl_list_id.'" name="gallery_gl_list" value="'.$gallery_gl.'" />
			<input type="hidden"  name="pn" value="'.$dt[0]["partner_sku_root"].'" />
			<div class="billinhead">
				<div>
					<p><span>ใบสั่งซื้อ/คู่ค้า</span></p>
					<input name="pn_name" type="text" value="'.htmlspecialchars($dt[0]["partner_name"]).'" readonly/>
				</div>
				<div>
					<p><span>เลขที่ใบเสร็จ</span></p>
					<input name="bill_no" type="text" value="'.htmlspecialchars($dt[0]["bill_no"]).'" />
				</div>
				<div>
					<p><span>วันที่ในใบเสร็จ (ค.ศ.)</span></p>
					<input name="bill_date" type="date" value="'.substr($dt[0]["bill_date"],0,10).'" />
				</div>
				<div>
					<p><span>รูปแบบใบเสร็จ</span></p>
					<select name="bill_type" onchange="Bi.setDisplayTV()">
						<option value="c"'.($dt[0]["bill_type"]=="c"?" selected":"").'>ใบเงินสด</option>
						<option value="v1"'.($dt[0]["bill_type"]=="v1"?" selected":"").'>ใบกำกับภาษี รวมภาษีในราคาสินค้าแล้ว</option>
						<option value="v0"'.($dt[0]["bill_type"]=="v0"?" selected":"").'>ใบกำกับภาษี ยังไม่รวมภาษีในราคาสินค้า</option>
					</select>
				</div>
				<!--<div>
					<p><span>รูปแบบการชำระ</span></p>
				<div>';
			//$display_id=$this->key("key",7);
			//$this->form_py=new form_selects("payu","คู่ค้า","billsin",$display_id,$payu_list_id);	
			//$this->form_py->writeForm($payu_json);
		echo '</div>
				</div>-->
				<div>
					<p><span>หมายเหตุ</span></p>
					<input type="text" name="note" value="'.htmlspecialchars($dt[0]["note"]).'" />
				</div>
			</div>
			
			<table id="billsin" data-type="edit"><tr><th>ที่</th>
			<th>ชื่อ</th>
			<th>จำนวน</th>
			<th>หน่วย</th>
			<th>ราคารวม<br />ราคาต่อชิ้น</th>
			<th>กระ<br />ทำ</th>
			</tr>';
		/*if($editable){		
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
		}*/
	//$this->writeJsDataEdit($pd,$editable,$sku_root,$id,$product_list_id);
		
		$et=($editable)?"true":"false";
		echo '</table>
			<div class="billinvat">
				<div class="r">ราคารวม ยังไม่บวกภาษี</div>
				<div class="r bold" id="billin_tv0">0.00</div>
				<div class="r">ภาษีรวม</div>
				<div class="r bold" id="billin_tv1">0.00</div>
				<div class="r">ราคารวม บวกภาษีรวม</div>
				<div class="r bold" id="billin_tv2">0.00</div>
			</div>';
		//<div><input type="button" value="เลือก/แก้ไข สินค้า" /></div>
		$id=$this->key("key",7);
		$this->form_pd=new form_selects("product","สินค้า","billsin",$id,$product_list_id);	
		$this->form_pd->writeForm("billsin");	
		$this->writeJsDataEdit($dt,$editable,$dt[0]["partner_sku_root"],$id,$product_list_id);
		
		$sku_root=$dt[0]["partner_sku_root"];
		$pn=$this->getPartnerAll();
		if($group="partner"){
			$pd=$this->loadProductPartner($sku_root);
			for($i=0;$i<count($pd);$i++){
				$pd[$i]["n"]=0;
				$pd[$i]["balance"]=0;
				$pd[$i]["sum"]=0;
			}
			$this->writeJsDataEditPrompt($pd,true,$sku_root,$id,$product_list_id);
		}
		$gal_id=$this->key("key",7);
		$this->gall=new gallery("bill_in","sku",$dt[0]["sku"],"billsin",$gal_id,$gallery_list_id,$gallery_gl_list_id,"Bi.icon");	
		$this->gall->writeForm();	
		echo '
		<input type="button" onclick="Bi.billsinSumit(true)" value="แก้ไข นำเข้าสินค้า" /></form>';
	}
	private function billsinCheck(string $type="insert"):array{
		$re=["result"=>false,"message_error"=>""];
		$se=$this->checkSet("bill_in",["post"=>["bill_no","bill_type"]],"post");
		if(!isset($_POST["product"])){
			$re["message_error"]="ไม่มีสินค้าที่เลือก";
		}else if(empty(trim($_POST["bill_no"]))){
			$re["message_error"]="เลขที่ใบเสร็จต้องไม่ว่าง";
		}else if(gettype(json_decode($_POST["product"],true))!="array"){
			$re["message_error"]="สินค้าที่เลือกหรือข้อมูลที่ส่งมาไม่อยู่ในรูปแบบ";
		}else if(strlen($_POST["note"])>$this->fills["note"]["length_value"]-3){
			$re["message_error"]="หมายเหตุ ยาวเกินไป";
		}else if(isset($_POST["pn"])&&!preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_POST["pn"])){
			$re["message_error"]="ใบนำเข้าหรือ คู่ค้าไม่ถูกต้อง";
		}else if(isset($_POST["bill_date"])&&!preg_match("/^([1-9])[0-9]{3}-(0|1)[0-9]-(0|1|2|3)[0-9]$/",$_POST["bill_date"])){
			$re["message_error"]="วันที่ ไม่อยู่ในรูปแบบ  yyy-mm-dd";
		}/*else if(
			!isset($_POST["payu"])
			||
			(
				isset($_POST["payu"]) 
				&& 
				!is_object(json_decode ($_POST["payu"]))
			)
		){
			$re["message_error"]="รูปแบบการชำระที่ส่งมาไม่ถูกต้อง";
		}*/else if(!$se["result"]){
			$re["message_error"]=$se["message_error"];
		}else if(!isset($_POST["sku"])&&$type=="edit"){
			$re["message_error"]="ไม่พบใบนำเข้าที่ส่งมา";
		}else if(isset($_POST["sku"])&&strlen(trim($_POST["sku"]))==0&&$type=="edit"){
			$re["message_error"]="รหัสชี่ใบนำเข้าว่าง";
		}else{
			$re["result"]=true;
		}
		return $re;
	}
	private function billsinPage(string $group="",string $sku_root=""){
		$this->addDir("?a=bills&amp;b=fill&amp;c=in","นำเข้าสินค้า ");
		$this->pageHead(["title"=>"นำเข้า สินค้า DIYPOS","js"=>["billsin","Bi","form_selects","Fsl","fileupload","Ful"],"css"=>["billsin","form_selects","fileupload"],"run"=>["Fsl"]]);
			echo '<div class="content">
				<div class="form">
					<h1 class="c">นำเข้าสินค้า</h1>';
			$this->writeContentInBillsin($group,$sku_root);
			echo '<br /><p class="c">
				
			</p>';
			echo '</div></div>';
			$this->pageFoot();
	}
	private function writeContentInBillsin(string $group="",string $sku_root=""):void{
		$pn=$this->getPartnerAll();	
		$payu=(isset($_POST["payu"]))?htmlspecialchars($_POST["payu"]):",defaultroot,";
		$product=(isset($_POST["product"]))?htmlspecialchars($_POST["product"]):"";
		$payu_list_id=$this->key("key",7);
		$product_list_id=$this->key("key",7);
		$pop=["po","partner"];
		$po_partner=(isset($_GET["po_partner"])&&in_array($_GET["po_partner"],$pop))?htmlspecialchars($_GET["po_partner"]):"partner";
		echo '<form class="form100"  name="billsin" method="post">
			<input type="hidden" name="sku" value="" />
			<input type="hidden" id="'.$payu_list_id.'" name="payu_list" value="'.$payu.'" />
			<input type="hidden" id="'.$product_list_id.'" name="product_list" value="'.$product.'" />
			<input type="hidden" name="po_partner" value="'.$po_partner.'" />
			<div class="billinhead">
				<div>
					<p><span>ใบสั่งซื้อ/คู่ค้า</span></p>
					<select name="pn" onchange="Bi.loadProduct(this)">
						<optgroup label="ใบสั่งซื้อ">
						</optgroup>
						<optgroup label="คู่ค้า">';
						
							echo '<option data-group="partner" value=""></option>';
							for($i=0;$i<count($pn);$i++){
								$st=($pn[$i]["sku_root"]==$sku_root)?" selected":"";
								echo '<option data-group="partner" value="'.$pn[$i]["sku_root"].'"'.$st.'>'.htmlspecialchars($pn[$i]["name"]).'</option>';
							}
		echo '		</optgroup>
					</select>
				</div>
				<div>
					<p><span>เลขที่ใบเสร็จ</span></p>
					<input name="bill_no" type="text" />
				</div>
				<div>
					<p><span>วันที่ในใบเสร็จ (ค.ศ.)</span></p>
					<input name="bill_date" type="date" />
				</div>
				<div>
					<p><span>รูปแบบใบเสร็จ</span></p>
					<select name="bill_type" onchange="Bi.setDisplayTV()">
						<option value="c">ใบเงินสด</option>
						<option value="v1">ใบกำกับภาษี รวมภาษีในราคาสินค้าแล้ว</option>
						<option value="v0">ใบกำกับภาษี ยังไม่รวมภาษีในราคาสินค้า</option>
					</select>
				</div>
				<!--<div>
					<p><span>รูปแบบการชำระ</span></p>
				<div>';
			$payu_json='{"defaultroot":0}';
			$this->form_py=new form_selects("payu","คู่ค้า","billsin",$this->key("key",7),$payu_list_id);	
			$this->form_py->writeForm($payu_json);
		echo '</div>
				</div>-->
				<div>
					<p><span>หมายเหตุ</span></p>
					<input type="text" name="note" />
				</div>
			</div>
			<table id="billsin"><tr><th>ที่</th>
			<th>ชื่อ</th>
			<th>จำนวน</th>
			<th>หน่วย</th>
			<th>ราคารวม<br />ราคาต่อชิ้น</th>
			<th>กระทำ</th>
			</tr>';
		echo '<!--<tr id="trsearch">
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
		</td></tr>--></table>
		<div class="billinvat">
			<div class="r">ราคารวม ยังไม่บวกภาษี</div>
			<div class="r bold" id="billin_tv0">0.00</div>
			<div class="r">ภาษีรวม</div>
			<div class="r bold" id="billin_tv1">0.00</div>
			<div class="r">ราคารวม บวกภาษีรวม</div>
			<div class="r bold" id="billin_tv2">0.00</div>
		</div>';
		//<div><input type="button" value="เลือก/แก้ไข สินค้า" /></div>
		$id=$this->key("key",7);
		$this->form_pd=new form_selects("product","สินค้า","billsin",$id,$product_list_id);	
		$this->form_pd->writeForm("billsin");
		echo '<div class="billinfileimg">
			<div>
				<p><span>รูปภาพใบเสร็จ</span></p>
				<div>
					<div id="div_fileuploadpre" class="fileuploadpres"></div>
					<input id="upload_pic" type="file" accept="image/png,image/gif,image/jpeg,image/webp" class="fuif" name="picture" onchange="Ful.fileUploadShow(event,20,Bi.icon,1024,160)" />
					<label for="upload_pic"  class="fubs">+เลือกรูปภาพ</label>
				</div>
			</div>	
			<script type="text/javascript">/*F.fileUploadShow(null,1,\'icon_id\',480,160,\'load\',\'div_fileuploadpre\')*/</script>
		</div>
		<br /><br />
		<input type="button" onclick="Bi.billsinSumit(false)" value="นำเข้าสินค้าเพิ่ม" /></form>';
		
		if($group=="partner"){
			$pd=$this->loadProductPartner($sku_root);
			for($i=0;$i<count($pd);$i++){
				$pd[$i]["n"]=0;
				$pd[$i]["balance"]=0;
				$pd[$i]["sum"]=0;
			}
			$this->writeJsDataEdit($pd,true,$sku_root,$id,$product_list_id);
		}
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
			<table id="billin_view"><tr><th>ที่</th>
			<th>รูป</th>
			<th>คู่ค้า</th>
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
			$sn=mb_substr(trim($se[$i]["partner_name"]),0,15);
			echo '<tr'.$cm.'><td>'.$se[$i]["id"].'</td>
				<td class="l"><div class="img48"><img src="img/gallery/64x64_'.$se[$i]["partner_icon"].'"  alt="'.$sn.'"  onerror="this.src=\'img/pos/64x64_null.png\'" /></div></td>
				<td class="l">
					<div>
						<a href="?a=partner&b=details&sku_root='.$se[$i]["pn_root"].'">'.htmlspecialchars($se[$i]["partner_name"]).'</a>
						<span><a href="?a=bills&amp;b=view&amp;c=in&amp;sku='.$se[$i]["sku"].'">🧾 '.$se[$i]["bill_no"].'</a></span>
					</div>
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
	private function getPartnerAll():array{
		$re=[];
		$sql=[];
		$sql["get"]="
			SELECT `name`,`icon`,`sku_root` FROM `partner` ORDER BY `name`
		";
		$se=$this->metMnSql($sql,["get"]);
		if($se["result"]){
			$re=$se["data"]["get"];
		}
		return $re;
	}
	private function getAllBillsIn():array{
		$re=[];
		$sql=[];
		$sql["count"]="SELECT @count:=COUNT(*) FROM bill_in WHERE bill_in.in_type='b' ";
		$sql["get"]="SELECT  `bill_in`.`id`  AS  `id`,`bill_in`.`in_type`  AS  `in_type`,`bill_in`.`sku`  AS  `sku`,IFNULL(`bill_in`.`bill`,bill_in.id)  AS  `bill`,
				`bill_in`.`bill_no`  AS  `bill_no`,`bill_in`.`pn_root`  AS  `pn_root`,IFNULL(`bill_in`.`note`,'')  AS  `note`, 
				SUM(`bill_in`.`n`) AS `n`, SUM(`bill_in`.`sum`) AS `sum`, `bill_in`.`date_reg` AS `date_reg`,
				CONCAT(`user_ref`.`name`,' ', `user_ref`.`lastname`) AS `user_name`,
				`partner_ref`.`name` AS `partner_name`,`partner_ref`.`icon` AS `partner_icon`
			FROM `bill_in` 
			LEFT JOIN `user_ref`
			ON( `bill_in`.`user`=`user_ref`.`sku_key`)
			LEFT JOIN `partner_ref`
			ON (`bill_in`.`pn_key`=`partner_ref`.`sku_key`)
			WHERE bill_in.in_type='b' 
			GROUP BY bill_in.date_reg
			ORDER BY `bill_in`.`id` DESC LIMIT ".(($this->page-1)*$this->per).",".$this->per."";
		$sql["result"]="SELECT @count AS count";	
		$se=$this->metMnSql($sql,["get","result"]);
		if($se["result"]){
			$re=["row"=>$se["data"]["get"],"count"=>$se["data"]["result"][0]["count"]];
		}
		//print_r($se);
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
			$txdir=htmlspecialchars($dt[0]["partner_name"]." 🧾".$dt[0]["bill_no"]);
			if(count($dt)>0){
				$this->addDir("?a=bills&amp;b=view&amp;c=in&amp;sku=".$dt[0]["sku"],"".$txdir);
			}
			$this->pageHead(["title"=>"รายละเอียด DIYPOS","js"=>["billsin","Bi"],"css"=>["billsin"]]);
			echo '<div class="content">';
			if(count($dt)>0){
				//echo '<h2 class="c"> '.htmlspecialchars($dt[0]["note"]).'</h2>';
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
		$icon_arr=json_decode($dt[0]["icon_arr"]);
		echo '<div style="max-width:800px;">
			'.$aut.'
			<table class="page r">
				<tr>
					<td class="c">
						<h2>ใบนำเข้าสินค้า</h2>
						<div class="billinview">
							<div>
								<div><img src="img/gallery/64x64_'.$dt[0]["partner_icon"].'" onerror="this.src=\'img/pos/64x64_null.png\'" /></div>
								<div><a href="?a=partner&amp;b=details&amp;sku_root='.$dt[0]["partner_sku_root"].'">'.$dt[0]["partner_name"].'</a><br />🧾'.$dt[0]["bill_no"].'</div>
							</div>
							<div class="r">บันทึกโดย : '.$dt[0]["user_name"].' วันที่ '.$dt[0]["date_reg"].'
								<br />⏳ผ่านมาแล้ว '.$this->ago(time()-strtotime($dt[0]["date_reg"])).'
							</div>
						</div>
					</td>
				</tr>';
		echo '<tr><td>
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
			<td><div><a href="?a=product&amp;b=details&amp;sku_root='.$dt[$i]["sku_root"].'">'.htmlspecialchars($dt[$i]["name"]).'</a></div>
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
		echo '</table>
			<tr><td>จำนวน '.count($dt).' รายการ , รวมเงิน <b>'.$tltx.'</b> บ.</td></tr>';
		if(count($icon_arr)>0){
			echo '<tr colspan="6"><td class="c">
				<h3><b>รูปภาพใบเสร็จ</b></h3>';
			for($i=0;$i<count($icon_arr);$i++){
				echo '<div class="billinarr_icon"><img class="viewimage" src="img/gallery/256x256_'.$icon_arr[$i].'" onclick="G.view(this)"></div>';
			}
			echo '</td></tr>';
		}
		echo '</table>
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
			SELECT  `bill_in`.`id`  AS  `id`,`bill_in`.`bill_no`  AS  `bill_no`,`bill_in`.`in_type`  AS  `in_type`,`bill_in`.`sku`  AS  `sku`,IFNULL(`bill_in`.`note`,'')  AS  `note`, 
				`bill_in`.`bill`  AS  `bill`,`bill_in`.`n` AS `n_list`, `bill_in`.`sum` AS `sum`,`bill_in`.`bill_type`  AS  `bill_type`,
				`bill_in`.`pn_root`  AS  `partner_sku_root`,IFNULL(`bill_in`.`note`,'')  AS  `note`,
				IFNULL(`bill_in`.`icon_arr`,'[]') AS `icon_arr`,IFNULL(`bill_in`.`icon_gl`,'[]') AS `icon_gl`,  
				`bill_in`.`bill_date` AS `bill_date`, `bill_in`.`date_reg` AS `date_reg`,
				CONCAT(`user_ref`.`name`,' ', `user_ref`.`lastname`) AS `user_name`,
				bill_in_list.id AS bill_in_list_id,bill_in_list.product_sku_key  AS `product_sku_key`,bill_in_list.product_sku_root,bill_in_list.product_sku_root AS sku_root ,
				IF(`bill_in_list`.`s_type`='p',bill_in_list.n,bill_in_list.n_wlv) AS `n` ,
				IF(`bill_in_list`.`s_type`='p',bill_in_list.balance,bill_in_list.balance_wlv) AS `balance`,bill_in_list.sum ,bill_in_list.name,
				IFNULL(SUM(IF(`bill_in_list2`.`s_type`='p',bill_in_list2.balance,bill_in_list2.balance_wlv)),0) AS `sum_balance`,
				partner_ref.name AS partner_name,partner_ref.sku_root AS partner_sku_root,partner_ref.icon AS partner_icon,
				unit_ref.name AS unit_name,
				product_ref.barcode AS barcode,product_ref.sku AS product_sku,`product_ref`.`s_type`,product_ref.vat_p,
				product.price,product.cost
			FROM `bill_in` 
			LEFT JOIN bill_in_list 
			ON(bill_in_list.id>=bill_in.r_ AND bill_in_list.id<=bill_in._r  AND bill_in.sku=bill_in_list.bill_in_sku AND bill_in_list.stroot='proot' )		
			LEFT JOIN bill_in_list AS bill_in_list2
			ON(IF(bill_in_list2.s_type='p',bill_in_list2.balance,bill_in_list2.balance_wlv)>0 AND bill_in_list.product_sku_root=bill_in_list2.product_sku_root  AND bill_in_list2.stroot='proot')			
			LEFT JOIN `user_ref`
			ON( `bill_in`.`user`=`user_ref`.`sku_key`)
			LEFT JOIN partner_ref
			ON(bill_in.pn_key=partner_ref.sku_key)
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
	private function loadProductPartner(string $sku_root):array{
		$sku_root=$this->getStringSqlSet($sku_root);
		$re=["get"=>[]];
		$sql=[];
		$sql["product"]="SELECT 
				`product`.`name`		,`product`.`sku` AS `product_sku`	,`product`.`barcode` AS `barcode`		,`product`.`cost`,
				`product`.`price`		,`product`.`sku_root`						,IFNULL(`product`.`s_type`,'') AS `s_type`,IFNULL(`product`.`vat_p`,0) AS `vat_p`,
				`unit`.`name` AS `unit_name`
			FROM `product`
			LEFT JOIN `unit`
			ON(`product`.`unit`=`unit`.`sku_root`)
			WHERE JSON_SEARCH(`partner`, 'one', ".$sku_root.") IS NOT NULL;
		";
		$se=$this->metMnSql($sql,["product"]);
		if($se["result"]){
			$re=$se["data"]["product"];
		}
		return $re;
	}
	private function propToFromValue(string $prop):string{
		$t=implode(",,",json_decode($prop));
		$t=(strlen(trim($t))>0)?",".$t.",":"";
		return $t;
	}
	private function payu_jsonToFromValue(string $payu_json):string{
		$t="";
		$q=json_decode($payu_json,true);
		foreach($q as $k=>$v){
			$t.=",".$k.",";
		}
		return $t;
	}
	protected function setPropR(string $prop):string{
		$ar = [];
		if(strlen(trim($prop))>2){
			$prop =trim($prop);
			$ar = explode(",,",substr($prop,2,-2));
		}
		if($ar[0]==""){
			$ar=[];
		}
		return json_encode($ar);
	}
}
?>
