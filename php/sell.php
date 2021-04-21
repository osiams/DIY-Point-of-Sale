<?php
class sell extends main{
	public function __construct(){
		parent::__construct();
		$this->home=1;
	}
	public function run(){
		$q=["st"];
		if(isset($_GET["b"])&&in_array($_GET["b"],$q)){
			$this->sendSt();
		}else{
			$this->pageHead(["title"=>"ขายสินค้า DIYPOS","js"=>["ws","Ws","sell","S","form_selects","Fsl"],"run"=>["Ws","S","Fsl"],"css"=>["sell","form_selects"]]);
			echo '<script type="text/javascript">S.ip = "'.$this->userIPv4().'";S.wsRegis();</script>';
			$this->pageFoot();
		}
	}
	public function fetch(){
		if(isset($_POST["b"])&&$_POST["b"]=="get_bc"
			&&isset($_POST["bc"])&&strlen(trim($_POST["bc"]))>0
			&&isset($_POST["field"])&&($_POST["field"]=="sku_root"||$_POST["field"]=="barcode")){
			$this->fetchGetBc($_POST["field"],$_POST["bc"]);
		}else if(isset($_POST["b"])&&$_POST["b"]=="smile"
			&&isset($_POST["submith"])&&$_POST["submith"]=="clicksubmit"){
			$re=["result"=>false,"message_error"=>"","billid"=>"0"];	
			$se=$this->fetchCutSt();
			
			header('Content-type: application/json');
			
			if(isset($se["data"]["result"][0]["result"])&&$se["data"]["result"][0]["result"]==1){
				$re["result"]=true;
				$re["billid"]=$se["data"]["result"][0]["billid"];
			}else{
				$re["message_error"]=$se["message_error"];
			}
			if(isset($se["data"]["result"][0]["over"])){
				$re["over"]=$se["data"]["result"][0]["over"];
			}
			if(isset($se["data"]["result"][0]["stock"])){
				$re["stock"]=$se["data"]["result"][0]["stock"];
			}
			if(isset($se["data"]["result"][0]["message_error"])){
				$re["message_error"]=$se["data"]["result"][0]["message_error"];
			}
			echo json_encode($re);
		}else{
			header('Content-type: application/json');
			print_r("{}");
		}
	}
	private function getUniqPdData(array $pd_sell):array{
		$re=[];
		foreach($pd_sell AS $k=>$v){
			$q=explode("_",$k);
			if(count($q)==1){
				$re[$q[0]]=$v;
			}else{
				if(!isset($re[$q[0]])){
					$re[$q[0]]=$v;
					$re[$q[0]]["n"]=$re[$q[0]]["n"]*$re[$q[0]]["n_wlv"];
				}else{
					$re[$q[0]]["n"]+=$v["n"]*$v["n_wlv"];
				}
			}
		}
		return $re;
	}
	private function fetchCutSt():array{
		$member0=(isset($_POST["member"])&&$this->isSKU($_POST["member"]))?$_POST["member"]:"";
		$member=$this->getStringSqlSet($member0);
		$re=["result"=>false,"message_error"=>""];
		$sku_root=$this->getStringSqlSet($_SESSION["sku_root"]);
		$pd=json_decode($_POST["pd"],true);
		$this->getUniqPdData($pd);
		$n=0;
		$price=0;
		foreach($pd as $k=>$v){
			$n+=1;
		}
		$jspd=$this->getStringSqlSet($_POST["pd"]);
		$jspd_wlv=$this->getStringSqlSet(json_encode($this->getUniqPdData($pd)));
		
		//print_r($jspd);exit;
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@pd_length:=0,
			@jspd:=".$jspd.",
			@jspd_wlv:=".$jspd_wlv.",
			@n:=".$n.",
			@sums:=0,
			@flag:=0,
			@bill_sell_save:=0,
			@TEST:='',
			@over:=0,
			@stock:='{}',
			@member:=".$member.",
			@member_key:=".$member.",
			@id:=(SELECT IFNULL((SELECT MAX(id) FROM `bill_sell`),0)+1),
			@member_sku_root_count:=(SELECT COUNT(*)  FROM `member` WHERE '".$member0."' != '' AND`sku_root`='".$member0."' ),
			@user:=(SELECT `sku_key`  FROM `user` WHERE `sku_root`=".$sku_root." LIMIT 1);
		";
		$sql["set2"]="
			SET @pd_length=JSON_LENGTH(@jspd),
			@key=JSON_KEYS(@jspd);
		";
		$sql["check"]="
			IF @member_sku_root_count != 1 && '".$member0."' THEN
				SET @message_error=CONCAT('ไม่พบสมาชิกที่ส่งมา\n');
				SET @over=1;
			ELSE 
				SET @member_key=(SELECT `sku_key` FROM `member` WHERE `sku_root` = '".$member0."' LIMIT 1);
			END IF;
		";
		$sql["set_sums"]="
			BEGIN NOT ATOMIC
				DECLARE n_list INT DEFAULT 0;
				DECLARE n_over INT DEFAULT 0;
				DECLARE pd_buy INT DEFAULT 0;
				DECLARE n_price_null INT DEFAULT 0;
				DECLARE flag TEXT DEFAULT '{}';
				SET @sku=(SELECT CONCAT('00',LPAD(CAST(@id AS CHAR(25)),7,'0')));
				FOR i IN 0..(@pd_length-1) DO
					SET pd_buy=0;
					SET @pdroot=JSON_VALUE(@key		,		CONCAT('$['	,		i		,']'));
				########################
				SET @p=(INSTR(@pdroot,'_'));
				IF @p > 0 THEN
					SET  @pdroot=LEFT(@pdroot, @p-1);
					#SET @TEST=CONCAT(@TEST,'-',@pdroot);
				END IF;
				######################
					SET pd_buy=JSON_VALUE(@jspd_wlv	,		CONCAT('$.'	,		@pdroot		,'.n')			);
					SET @pd_n=0;
					SET @flag=(
						SELECT 
							(
								`price`*JSON_VALUE(
									@jspd_wlv	,	
									CONCAT(
										'$.'	,
										@pdroot	,	
										'.n'
									)
								)	
							)	
							FROM `product` WHERE `sku_root`=@pdroot	 LIMIT 1 
					);
					IF JSON_EXISTS(flag,CONCAT('$.' ,@pdroot)) = 0 THEN
						SET flag=JSON_INSERT(flag,CONCAT('$.' ,@pdroot),1);
						SET @sums=@sums+@flag;
					END IF;
					SET @TEST=CONCAT(@TEST,'@',@sums);
					#SET @sums=@sums+@flag;	
					SET @pd_n=(SELECT SUM(IF(bill_in_list.s_type='p',bill_in_list.balance,bill_in_list.balance_wlv)) AS balance FROM bill_in_list 
						WHERE IF(bill_in_list.s_type='p',bill_in_list.balance,bill_in_list.balance_wlv)>0 
						AND   `product_sku_root`=	@pdroot	
						AND  bill_in_list.stroot='proot'
					);
					IF @pd_n IS NULL THEN
						SET  @pd_n=0;
					END IF;			
					SET @stock=JSON_INSERT(@stock,CONCAT('$.' , @pdroot), @pd_n);
					IF @flag IS NULL  OR @flag <= 0 THEN
						SET @over=1;
						SET n_price_null=n_price_null+1;
					ELSEIF @pd_n<1 THEN
						SET @over=1;
						SET n_list=n_list+1;
					ELSEIF pd_buy > @pd_n THEN 
						SET n_over=n_over+1;
						SET @over=1;
					END IF;
				END FOR;
				
				IF n_list>0 THEN
					SET @message_error=CONCAT('มีสินค้า ',n_list,' รายการ ไม่มีจำนวนในระบบ\n');
				END IF;
				IF n_over>0 THEN
					SET @message_error=CONCAT(@message_error,'มีสินค้า ',n_over,' รายการ มีจำนวนในระบบน้อยกว่า จำนวนที่จะขาย\nและ หรือ\n กรณีสินค้า ชั่งตวงวัด จำนวนสินค้ารวมหลายรายการ อาจมีจำนวนมากกว่า สินค้าที่มีอยู่');
				END IF;
				IF n_price_null>0 THEN
					SET @message_error=CONCAT(@message_error,'มีสินค้า ',n_price_null,' รายการ  ที่ยังไม่ได้ตั้งราคาขาย หรือ ราคาขาย <= 0.00\n');
				END IF;
			END ;
		";
		$sql["bill_sell_list_save"]="
			BEGIN NOT ATOMIC
				DECLARE i INT DEFAULT 0;
				DECLARE `sq` INT DEFAULT 1;
				DECLARE pdl INT DEFAULT 0;
				DECLARE pdroot CHAR(25) DEFAULT '';
				DECLARE pdkey CHAR(25) DEFAULT '';
				DECLARE pdn INT DEFAULT 0;
				DECLARE pdn_wlv FLOAT DEFAULT 1.0000;
				DECLARE pdcoat FLOAT DEFAULT 0;
				DECLARE unitkey CHAR(25) DEFAULT 'defaultroot';
				DECLARE unitroot CHAR(25) DEFAULT 'defaultroot';
				DECLARE cuts FLOAT DEFAULT 0;
				DECLARE cut FLOAT DEFAULT 0;
				DECLARE stid INT DEFAULT 0;		
				DECLARE stsku CHAR(25) DEFAULT '';
				DECLARE stn FLOAT DEFAULT 0.0000;			
				DECLARE stbalance FLOAT DEFAULT 0.0000;	
				DECLARE stsum FLOAT DEFAULT 0;	
				DECLARE `mod` FLOAT DEFAULT 0;
				DECLARE `n_wlv_mod` FLOAT DEFAULT 0;
				DECLARE `cuts_mod` INT DEFAULT 0;
				DECLARE `cut_mod` INT DEFAULT 0;
				DECLARE w CHAR(1) DEFAULT '0';
				DECLARE lastid INT DEFAULT NULL;	
				DECLARE r__ INT DEFAULT 0;
				DECLARE __r INT DEFAULT 0;
				DECLARE addsku CHAR(25) CHARACTER SET ascii DEFAULT '';
				SET @TEST='';
				IF @over=0 THEN 
					SET @bill_sell_save=1;
					SET pdl=@pd_length;
					IF @bill_sell_save=1 THEN
						INSERT INTO `bill_sell`  (sku,n,price,user,member_sku_key,member_sku_root) 
						VALUES (@sku,@n,@sums,@user,@member_key,@member);			
						SET @TEST=CONCAT(@TEST,';pdl=',pdl);		
						WHILE i < pdl DO
							SET stn=0;
							SET stbalance=0;
							SET @pdroot_n_or_wlv=JSON_VALUE(@key		,		CONCAT('$['	,		i		,']')		);
							########################
							SET @p=(INSTR(@pdroot_n_or_wlv,'_'));
							IF @p > 0 THEN
								SET pdroot=LEFT(@pdroot_n_or_wlv, @p-1);	
							ELSE
								SET pdroot=@pdroot_n_or_wlv;
							END IF;
							######################
							SET pdn=JSON_VALUE(@jspd	,		CONCAT('$.'	,		@pdroot_n_or_wlv		,'.n')			);
							SET pdn_wlv=JSON_VALUE(@jspd	,		CONCAT('$.'	,		@pdroot_n_or_wlv		,'.n_wlv')			);
							SET cuts=(pdn-cut);
							CALL GetIdFirstSQ_(pdroot,@sq_frist);
							SELECT id,bill_in_sku,IF(s_type='p',n,n_wlv),IF(s_type='p',balance,balance_wlv),sum  INTO stid,stsku,stn,stbalance,stsum
								FROM bill_in_list 
								WHERE id=@sq_frist;
							SELECT unit.sku_key,unit.sku_root INTO unitkey,unitroot
								FROM product
								LEFT JOIN unit
								ON(product.unit=unit.sku_root)
								WHERE product.sku_root=pdroot LIMIT 1;
							SET @TEST=CONCAT(@TEST,';@sq_frist=',@sq_frist);
							SET @TEST=CONCAT(@TEST,';stid=',stid);
							SET @TEST=CONCAT(@TEST,';stbalance=',stbalance);
							SET @TEST=CONCAT(@TEST,';pdroot=',pdroot);
							SET pdkey=(SELECT sku_key  FROM product WHERE sku_root=pdroot LIMIT 1);	
							IF stn>0 THEN		
							SET @TEST=CONCAT(@TEST,'-','eeeeeeeeeeeeeeeeeeeeeee','^',cuts,'^',stbalance);
								IF `cut_mod`< `cuts_mod` THEN
									IF `mod`>0 && `mod` < `n_wlv_mod` THEN
										SET pdn=1;
										SET pdn_wlv=`mod`;
										SET cuts=1;
									ELSE
										SET pdn_wlv=`n_wlv_mod`;
									END IF;
								END IF;
								IF cuts*pdn_wlv<=stbalance THEN
									SET @TEST=CONCAT(@TEST,'-','44444444444444',cuts*pdn_wlv);
									SET pdcoat=pdcoat+(stsum/stn)*cuts*pdn_wlv;
									INSERT INTO bill_sell_list (sku,bill_in_list_id,lot,product_sku_key,product_sku_root, n,n_wlv,c,u ,r,`sq`,unit_sku_key,unit_sku_root)
									VALUE(@sku,stid,stsku,pdkey,pdroot,pdn,pdn_wlv,cuts,0,0,`sq`,unitkey,unitroot);
									UPDATE bill_in_list 
										SET balance=(IF(s_type='p',balance-cuts*pdn_wlv,NULL)) ,
											balance_wlv=(IF(s_type!='p',balance_wlv-(cuts*pdn_wlv),NULL)) 
									WHERE id=stid ;
									#################
									SET `mod`=0;
									SET `cut_mod`=`cut_mod`+cuts;
									SET cut=cut+cuts;
									IF `cut_mod`< `cuts_mod` THEN
										SET `mod`=`n_wlv_mod`;
									ELSE
										SET stn=0;
										SET cuts=0;
										SET cut=0;
										SET stbalance=0;
										SET stsum=0;
										SET i=i+1;
										SET `cuts_mod`=0;
										SET `cut_mod`=0;
										SET `n_wlv_mod`=0;
										SET `sq`=`sq`+1;
									END IF;
									##################
								ELSEIF cuts*pdn_wlv>stbalance THEN
									SET @TEST=CONCAT(@TEST,'-','555555555555555555');
									SET pdcoat=pdcoat+(stsum/stn)*stbalance;
									SET @cut_able=FLOOR(stbalance/pdn_wlv);
									SET @mod=ABS(@cut_able*pdn_wlv-stbalance);
									INSERT INTO bill_sell_list (sku,bill_in_list_id,lot,product_sku_key,product_sku_root, n,n_wlv,c,u ,r,`sq`,unit_sku_key,unit_sku_root)
									VALUE(@sku,stid,stsku,pdkey,pdroot,pdn,pdn_wlv,@cut_able,(cuts-@cut_able),0,`sq`,unitkey,unitroot);
									INSERT INTO bill_sell_list (sku,bill_in_list_id,lot,product_sku_key,product_sku_root, n,n_wlv,c,u ,r,`sq`,unit_sku_key,unit_sku_root)
									VALUE(@sku,stid,stsku,pdkey,pdroot,1,@mod,1,0,0,`sq`,unitkey,unitroot);
									IF pdn_wlv=1 THEN
										UPDATE bill_in_list 
											SET balance=(IF(s_type='p',0,NULL)) ,
												balance_wlv=(IF(s_type!='p',0,NULL)) 
										WHERE id=stid ;
										SET `mod`=0;
									ELSE 
										UPDATE bill_in_list 
											SET balance_wlv=0
										WHERE id=stid ;
										SET `mod`=pdn_wlv-@mod;
										SET `n_wlv_mod`=pdn_wlv;
										SET `cuts_mod`=cuts;
										SET `cut_mod`=`cut_mod`+@cut_able;
									END IF;
									SET cut=cut+@cut_able;
									################3
									SET @TEST=CONCAT(@TEST,'-','zzzzzzzzzzzzzzz');
									SET @TEST=CONCAT(@TEST,'$',cuts,'@',stbalance);
									#SET i=i+1;
									###################
								END IF;
								SET lastid=(SELECT LAST_INSERT_ID());
								IF lastid!=0 THEN
									IF r__=0 THEN 
										SET r__=(SELECT MAX(id) FROM bill_sell_list LIMIT 1);
									ELSE
										SET __r=lastid;
									END IF;
								END IF;
								SET lastid=0;
							ELSE 
							SET @TEST=CONCAT(@TEST,'-','qqqqqqqqqqqqqqqq');
								SET w='1';
								SET pdcoat=pdcoat+(SELECT cost FROM product WHERE sku_root=pdroot LIMIT 1)*cuts;
								##INSERT INTO bill_sell_list (sku,bill_in_list_id,lot,product_sku_key,product_sku_root, n,n_wlv,c,u ,`sq`,unit_sku_key,unit_sku_root )
								##VALUE(@sku,NULL,NULL,pdkey,pdroot,pdn,pdn_wlv,0,cuts,`sq`,unitkey,unitroot);
								SET stn=0;
								SET cuts=0;
								SET cut=0;
								SET stbalance=0;
								SET stsum=0;
								SET i=i+1;
								SET lastid=(SELECT LAST_INSERT_ID());
								IF lastid!=0 THEN
									IF r__=0 THEN 
										SET r__=lastid;
									ELSE
										SET __r=lastid;
									END IF;
								END IF;
								SET lastid=0;
							END IF;
						END WHILE;
						SET addsku=@sku;
						IF __r=0 THEN 
							SET __r=r__;
						END IF;
						UPDATE bill_sell SET `cost`=pdcoat,`w`=w,`r_`=r__,`_r`=__r WHERE `sku` = addsku ;
					END IF;
					SET @result=1;
				END IF;	
			END;	
		";
		//print_r($sql);exit;
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@sku AS `billid`,@over AS `over`,@stock AS `stock`,@TEST AS `TEST`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($se);
		return $se;
	}
	private function fetchGetBc(string $field,string $barcode):void{
		if($field=="barcode"&&substr($barcode,0,2)=="__"){
			$field="sku_root";
			$barcode=substr($barcode,3);
		}
		$barcode_wlv="";
		$be=(int) substr($barcode,-2);
		if($field=="barcode"&&strlen($barcode)>=8){
			if($be<=strlen($barcode)-4-1&&$be>=2&&$be%2==0){
				$barcode_wlv=substr($barcode,0,$be);
			}
		}
		$wlv_no_bc=false;
		$barcode_wlv_no_bc="";
		if($field=="sku_root"){
			if(strlen($barcode)>0){
				$y=explode("_",$barcode);
				if(count($y)==2&&strlen($y[1])>=4){
					$be2=(int) substr($y[1],-2);
					if($be2<=strlen($y[1])-4-1&&$be2>=2&&$be2%2==0){
						$field="barcode";
						$barcode=$y[1];
						$barcode_wlv=substr($y[1],0,$be2);
					}else{
						$wlv_no_bc=true;
						$barcode_wlv_no_bc=$y[0];
					}
				}
			}
		}
		$dt=$this->getProduct($field,(!$wlv_no_bc?$barcode:$barcode_wlv_no_bc),$barcode_wlv);
		//print_r($dt);
		if(count($dt)==1){
			$dt[0]["result"]=true;
			$dt[0]["message_error"]="";
			settype($dt[0]["price"], "integer");
			settype($dt[0]["cost"], "integer");
			if($dt[0]["bc_type"]=="bc_wlv"||$wlv_no_bc==true){
				$int_start=substr($barcode,-4);
				$int_start=substr($int_start,0,2);
				$wlv_int_float=substr($barcode,$be,-4);
				$wlv_int= (int) substr($wlv_int_float,0,$int_start);
				$wlv_float= substr($wlv_int_float,$int_start);
				$wlv=$wlv_int.".".$wlv_float;
				$wlv=(strlen($wlv_float)==0)?$wlv_int:$wlv;	
				$dt[0]["barcode"]=$barcode;
				$name=$dt[0]["name"];
				$dt[0]["name"]=$name. " ".$wlv." ".$dt[0]["unit_name"];			
				if($wlv_no_bc==true){
					$y=explode("_",$barcode);
					if(count($y)==2){
						$barcode=$y[1];
						$int_start=substr($barcode,-4);
						$int_start=substr($int_start,0,2);
						$wlv_int_float=substr($barcode,$be,-4);
						$wlv_int= (int) substr($wlv_int_float,0,$int_start);
						$wlv_float= substr($wlv_int_float,$int_start);
						$wlv=$wlv_int.".".$wlv_float;
						$wlv=(strlen($wlv_float)==0)?$wlv_int:$wlv;	
						$dt[0]["bc_type"]="bc_wlv_null";
						$dt[0]["barcode"]="";
						$dt[0]["barcode_wlv_no_bc"]=$barcode;
						$dt[0]["name"]=$name. " ".$wlv." ".$dt[0]["unit_name"];	
					}
				}
			}				
		}else{
			$dt[0]=[];
			$dt[0]["result"]=false;
			$dt[0]["message_error"]="ไม่พบสินค้ารหัสแท่ง ".htmlspecialchars($barcode);
		}
		header('Content-type: application/json');
		echo json_encode($dt[0]);
	}
	private function getProduct(string $field,string $bc,string $bc_wlv):array{
		$barcode=$this->getStringSqlSet($bc);
		$barcode_wlv=$this->getStringSqlSet($bc_wlv);
		$re=[];
		$sql=[];
		$sql["set"]="SELECT @n_bc:=(SELECT COUNT(*) FROM `product` WHERE `".$field."`=".$barcode." LIMIT 1),
			@field:='".$field."',
			@bc:=".$barcode.",
			@bc_type:='bc',
			@bc_wlv:=".$barcode_wlv."";
		$sql["set_r"]="IF @n_bc = 0 && @field = 'barcode' THEN
				SET @bc=@bc_wlv;
				SET @bc_type='bc_wlv';
		END IF";
		$sql["get"]="
			SELECT 
				 `product`.`sku`, `product`.`sku_root`, `product`.`barcode`, 
				`product`.`name`, `product`.`price`, `product`.`cost`, 
				 `unit`.`name` AS `unit_name`,`product`.`s_type`,@bc_type AS `bc_type`
			FROM `product` 
			LEFT JOIN (`unit`) 
			ON (`product`.`unit` = `unit`.`sku_root`) 
			WHERE `product`.`".$field."`=@bc LIMIT 1;
		";
		//print_r($sql);
		$se=$this->metMnSql($sql,["get"]);
		if($se["result"]){
			$re=$se["data"]["get"];
		}
		//print_r($se);exit;
		return $re;
	}
	private function sendSt():void{
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
		echo "retry: 10000\n";
		if(isset($_GET["k"])){
			$se=$this->getSt($_GET["k"]);
			echo  'data: '.json_encode($se).'

';//--ห้ามเลื่อนบรรทัด  \n\n 
		}else{
			echo "data:{}\n\n";
		}
		//ob_flush();
		flush();
		
	}
	private function  getSt(string $pdroots_json):array{
		$re=[];
		$t="(";
		$i=0;		
		try {
			$dt=json_decode($pdroots_json, true, $depth=2, JSON_THROW_ON_ERROR);
			foreach($dt as $k=>$v){
				if($this->isSKU($v)){
					$i+=1;
					$cm=($i>1)?",":"";
					$t.=$cm."'".$v."'";
				}
			}
			if($i>0){
				$t.=")";
				$sql=[];
				$sql["get"]="SELECT product_sku_root,SUM(IF(s_type='p',balance,balance_wlv)) AS balance
					FROM bill_in_list
					WHERE IF(s_type='p',balance,balance_wlv)>0  AND product_sku_root IN ".$t."  AND stroot='proot'
					GROUP BY product_sku_root
				";
				$se=$this->metMnSql($sql,["get"]);
			}
		} 
		catch(Exception $e) {
			//print_r($e);
		}
		//print_r($se);exit;
		
	if(isset($se["data"]["get"][0])){
		foreach($se["data"]["get"] as $k=>$v){
			settype($se["data"]["get"] [$k]["balance"],"float");
			$re[$se["data"]["get"] [$k]["product_sku_root"]]=$se["data"]["get"] [$k]["balance"];
		}
	}
	
		return $re;
	}
}
