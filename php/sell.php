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
			$this->pageHead(["title"=>"ขายสินค้า DIYPOS","js"=>["ws","Ws","sell","S"],"run"=>["Ws","S"],"css"=>["sell"]]);
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
	private function fetchCutSt():array{
		$re=["result"=>false,"message_error"=>""];
		$sku_root=$this->getStringSqlSet($_SESSION["sku_root"]);
		$pd=json_decode($_POST["pd"],true);
		$n=0;
		$price=0;
		foreach($pd as $k=>$v){
			$n+=1;
		}
		$jspd=$this->getStringSqlSet($_POST["pd"]);
		//print_r($jspd);
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@pd_length:=0,
			@jspd:=".$jspd.",
			@n:=".$n.",
			@sums:=0,
			@flag:=0,
			@bill_sell_save:=0,
			@TEST:='',
			@over:=0,
			@stock:='{}',
			@id:=(SELECT IFNULL((SELECT MAX(id) FROM `bill_sell`),0)+1),
			@user:=(SELECT `sku_key`  FROM `user` WHERE `sku_root`=".$sku_root." LIMIT 1);
		";
		$sql["set2"]="
			SET @pd_length=JSON_LENGTH(@jspd),@key=JSON_KEYS(@jspd);
		";
		$sql["set_sums"]="
			BEGIN NOT ATOMIC
				DECLARE n_list INT DEFAULT 0;
				DECLARE n_over INT DEFAULT 0;
				DECLARE pd_buy INT DEFAULT 0;
				DECLARE n_price_null INT DEFAULT 0;
				SET @sku=(SELECT CONCAT('00',LPAD(CAST(@id AS CHAR(25)),7,'0')));
				FOR i IN 0..(@pd_length-1) DO
					SET pd_buy=0;
					SET @pdroot=JSON_VALUE(@key		,		CONCAT('$['	,		i		,']'));
					SET pd_buy=JSON_VALUE(@jspd	,		CONCAT('$.'	,		@pdroot		,'.n')			);
					SET @pd_n=0;
					SET @flag=(
						SELECT 
							(
								`price`*JSON_VALUE(
									@jspd	,	
									CONCAT(
										'$.'	,
										JSON_VALUE(@key		,		CONCAT('$['	,		i		,']')		)	,	
										'.n'
									)
								)	
							)	
							FROM `product` WHERE `sku_root`=JSON_VALUE(@key		,		CONCAT('$['	,		i		,']')		) LIMIT 1 
					);

					SET @sums=@sums+@flag;	
					SET @pd_n=(SELECT SUM(bill_in_list.balance) FROM bill_in_list 
						WHERE bill_in_list.balance>0 
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
					SET @message_error=CONCAT(@message_error,'มีสินค้า ',n_over,' รายการ ่มีจำนวนในระบบน้อยกว่า จำนวนที่จะขาย\n');
				END IF;
				IF n_price_null>0 THEN
					SET @message_error=CONCAT(@message_error,'มีสินค้า ',n_price_null,' รายการ  ที่ยังไม่ได้ตั้งราคาขาย หรือ ราคาขาย <= 0.00\n');
				END IF;
			END ;
		";
		$sql["bill_sell_list_save"]="
			BEGIN NOT ATOMIC
				DECLARE i INT DEFAULT 0;
				DECLARE pdl INT DEFAULT 0;
				DECLARE pdroot CHAR(25) DEFAULT '';
				DECLARE pdkey CHAR(25) DEFAULT '';
				DECLARE pdn INT DEFAULT 0;
				DECLARE pdcoat FLOAT DEFAULT 0;
				DECLARE unitkey CHAR(25) DEFAULT 'defaultroot';
				DECLARE unitroot CHAR(25) DEFAULT 'defaultroot';
				DECLARE cuts INT DEFAULT 0;
				DECLARE cut INT DEFAULT 0;
				DECLARE stid INT DEFAULT 0;		
				DECLARE stsku CHAR(25) DEFAULT '';
				DECLARE stn INT DEFAULT 0;			
				DECLARE stbalance INT DEFAULT 0;	
				DECLARE stsum FLOAT DEFAULT 0;	
				DECLARE w CHAR(1) DEFAULT '0';
				DECLARE lastid INT DEFAULT NULL;	
				DECLARE r__ INT DEFAULT 0;
				DECLARE __r INT DEFAULT 0;
				DECLARE addsku CHAR(25) CHARACTER SET ascii DEFAULT '';
				IF @over=0 THEN 
					SET @bill_sell_save=1;
					SET pdl=@pd_length;
					IF @bill_sell_save=1 THEN
						INSERT INTO `bill_sell`  (sku,n,price,user) 
						VALUES (@sku,@n,@sums,@user);					
						WHILE i < pdl DO
							SET stn=0;
							SET stbalance=0;
							SET pdroot=JSON_VALUE(@key		,		CONCAT('$['	,		i		,']')		);
							SET pdn=JSON_VALUE(@jspd	,		CONCAT('$.'	,		pdroot		,'.n')			);
							SET cuts=pdn-cut;
							SELECT id,bill_in_sku,n,balance,sum INTO stid,stsku,stn,stbalance,stsum
								FROM bill_in_list 
								WHERE balance>0 AND  product_sku_root=pdroot AND stroot='proot' 
								ORDER BY sq ASC,id  ASC LIMIT 1;
							SELECT unit.sku_key,unit.sku_root INTO unitkey,unitroot
								FROM product
								LEFT JOIN unit
								ON(product.unit=unit.sku_root)
								WHERE product.sku_root=pdroot LIMIT 1;
							#SET @TEST=CONCAT(@TEST,'-',CURTIME(6));
							SET pdkey=(SELECT sku_key  FROM product WHERE sku_root=pdroot LIMIT 1);	
							IF stn>0 THEN		
								IF cuts<=stbalance THEN
									SET pdcoat=pdcoat+(stsum/stn)*cuts;
									INSERT INTO bill_sell_list (sku,bill_in_list_id,lot,product_sku_key,product_sku_root, n,c,u ,r,unit_sku_key,unit_sku_root)
									VALUE(@sku,stid,stsku,pdkey,pdroot,pdn,cuts,0,0,unitkey,unitroot);
									UPDATE bill_in_list SET balance=(balance-cuts) WHERE id=stid ;
									SET stn=0;
									SET cuts=0;
									SET cut=0;
									SET stbalance=0;
									SET stsum=0;
									SET i=i+1;
								ELSEIF cuts>stbalance THEN
									SET pdcoat=pdcoat+(stsum/stn)*stbalance;
									INSERT INTO bill_sell_list (sku,bill_in_list_id,lot,product_sku_key,product_sku_root, n,c,u ,r,unit_sku_key,unit_sku_root)
									VALUE(@sku,stid,stsku,pdkey,pdroot,pdn,stbalance,(cuts-stbalance),0,unitkey,unitroot);
									UPDATE bill_in_list SET balance=0 WHERE id=stid ;
									SET cut=cut+stbalance;
								END IF;
								SET lastid=(SELECT LAST_INSERT_ID());
								IF lastid!=0 THEN
									IF r__=0 THEN 
										SET r__=lastid;
									ELSE
										SET __r=lastid;
									END IF;
								END IF;
								SET lastid=0;
							ELSE 
								SET w='1';
								SET pdcoat=pdcoat+(SELECT cost FROM product WHERE sku_root=pdroot LIMIT 1)*cuts;
								INSERT INTO bill_sell_list (sku,bill_in_list_id,lot,product_sku_key,product_sku_root, n,c,u ,unit_sku_key,unit_sku_root )
								VALUE(@sku,NULL,NULL,pdkey,pdroot,pdn,0,cuts,unitkey,unitroot);
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
		$dt=$this->getProduct($field,$barcode);
		if(count($dt)==1){
			$dt[0]["result"]=true;
			$dt[0]["message_error"]="";
			settype($dt[0]["price"], "integer");
			settype($dt[0]["cost"], "integer");
		}else{
			$dt[0]=[];
			$dt[0]["result"]=false;
			$dt[0]["message_error"]="ไม่พบสินค้ารหัสแท่ง ".htmlspecialchars($barcode);
		}
		header('Content-type: application/json');
		echo json_encode($dt[0]);
	}
	private function getProduct(string $field,string $bc):array{
		$barcode=$this->getStringSqlSet($bc);
		$re=[];
		$sql=[];
		$sql["get"]="SELECT 
			 `product`.`sku`, `product`.`sku_root`, `product`.`barcode`, 
			`product`.`name`, `product`.`price`, `product`.`cost`, 
			 `unit`.`name` AS `unit_name`
		FROM `product` 
		LEFT JOIN (`unit`) 
		ON (`product`.`unit` = `unit`.`sku_root`) 
		WHERE `product`.`".$field."`=".$barcode." LIMIT 1
		";
		//print_r($sql);
		$se=$this->metMnSql($sql,["get"]);
		if($se["result"]){
			$re=$se["data"]["get"];
		}
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
				$sql["get"]="SELECT product_sku_root,SUM(balance) AS balance
					FROM bill_in_list
					WHERE balance>0  AND product_sku_root IN ".$t."  AND stroot='proot'
					GROUP BY product_sku_root
				";
				$se=$this->metMnSql($sql,["get"]);
			}
		} 
		catch(Exception $e) {
			//print_r($e);
		}
		//print_r($sql);
		
	if(isset($se["data"]["get"][0])){
		foreach($se["data"]["get"] as $k=>$v){
			settype($se["data"]["get"] [$k]["balance"],"integer");
			$re[$se["data"]["get"] [$k]["product_sku_root"]]=$se["data"]["get"] [$k]["balance"];
		}
	}
	
		return $re;
	}
}
