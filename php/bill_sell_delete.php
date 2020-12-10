<?php
class bill_sell_delete extends bills{
	public function __construct(){
		parent::__construct();
		$this->sku="";
	}
	public function fetch(){
		if(isset($_POST["sku"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_POST["sku"])){
			$this->sku=$_POST["sku"];
			if(isset($_POST["note"])){
				//echo strlen($_POST["note"]);
				if(strlen($_POST["note"])>$this->fills["note"]["length_value"]/3){
					$_POST["note"]=substr($_POST["note"],0,floor($this->fills["note"]["length_value"]/3));
				}
			}else{
				$_POST["note"]="";
			}
			$se=$this->fectchRun($this->sku,$_POST["note"]);
			$re=["result"=>false,"message_error"=>""];
			if($se["result"]&&$se["message_error"]==""){
				$re["result"]=true;
			}else{
				$re["message_error"]=$se["message_error"];
			}
			header('Content-type: application/json');
			echo json_encode($re);
		}
	}
	private function fectchRun(string $sku,string $note):array{
		$note=$this->getStringSqlSet($note);
		$sku=$this->getStringSqlSet($sku);
		$user=$this->getStringSqlSet($_SESSION["sku_root"]);
		$sql=[];
		$sqlx=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@sku:=".$sku.",
			@note:=".$note.",
			@user:=".$user.",
			@TEST:='',
			@ded:=0,
			@has:=(SELECT  COUNT(*)  FROM bill_sell WHERE  sku=@sku);
		";
		$sql["check"]="
			IF @has = 0 THEN 
				SET @message_error=CONCAT('เกิดขอผิดพลาด ไม่พบใบเสร็จเลขที่ ',@sku);
			ELSE 
				SET @ded=(SELECT stat FROM bill_sell WHERE sku=@sku);
				IF @ded='c' THEN
					SET @message_error=CONCAT('เกิดขอผิดพลาด ใบเสร็จนี้ ถูกยกเลิกไปแล้ว');
				END IF;
			END IF;			
		";
		$sql["run"]="BEGIN NOT ATOMIC 
			DECLARE done INT DEFAULT FALSE;
			DECLARE k CHAR(25) DEFAULT '';
			DECLARE n_list INT DEFAULT 0;
			DECLARE sums FLOAT DEFAULT 0;
			DECLARE r ROW (bill_in_list_id INT ,
													lot VARCHAR(25),
													product_sku_key VARCHAR(25),
													product_sku_root VARCHAR(25),
													n INT,
													c INT,
													u INT,
													unit_sku_key VARCHAR(25),
													unit_sku_root VARCHAR(25),
													sku VARCHAR(25),
													lot_root VARCHAR(25),
													cost FLOAT,
													price FLOAT,
													name VARCHAR(255) CHARACTER SET utf8,
													balance INT );
			DECLARE lastid INT DEFAULT NULL;			
			DECLARE r__ INT DEFAULT 0;
			DECLARE __r INT DEFAULT 0;			
			DECLARE cur1 CURSOR FOR 
			SELECT bill_sell_list.bill_in_list_id,bill_sell_list.lot	, bill_sell_list.product_sku_key	, bill_sell_list.product_sku_root, 
					bill_sell_list.n	,bill_sell_list.c,
					bill_sell_list.u ,bill_sell_list.unit_sku_key ,bill_sell_list.unit_sku_root ,
					bill_in.sku,bill_in.lot_root,(bill_in_list.sum/bill_in_list.n) AS cost,product_ref.price,
					bill_in_list.name	,bill_in_list.balance
				FROM bill_sell_list 
				LEFT JOIN bill_sell
				ON(bill_sell.stat!='c' )
				LEFT JOIN bill_in
				ON(bill_sell_list.lot=bill_in.sku )
				LEFT JOIN bill_in_list
				ON(bill_sell_list.bill_in_list_id=bill_in_list.id AND bill_in_list.bill_in_sku=bill_sell_list.lot AND bill_sell_list.product_sku_root=bill_in_list.product_sku_root)
				LEFT JOIN product_ref
				ON(bill_sell_list.product_sku_key=product_ref.sku_key)
				WHERE bill_sell_list.sku=@sku 
				GROUP BY bill_sell_list.product_sku_root;
			DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;			
			IF @message_error='' THEN
				SET @stkey=(SELECT sku_key FROM  it WHERE sku_root='proot');	
				SET k=KEY_();		
				OPEN cur1;
					read_loop: LOOP
						FETCH cur1 INTO r;
						IF done THEN
							LEAVE read_loop;
						END IF;
						SET @TEST=CONCAT(@TEST,'-',r.n);
						IF r.c > 0 THEN 
							SET n_list=n_list+1;
							SET sums=sums+(r.price*r.n);
							INSERT  INTO `bill_in_list`  (`stkey`,`stroot`,`bill_in_sku`,`product_sku_key`,`product_sku_root`,`name`,`n`,balance,`sum`,`unit_sku_key`,`unit_sku_root`) 
							VALUES(@stkey,'proot',k	,r.product_sku_key	,r.product_sku_root	,r.name	,r.n	,r.n	,(r.cost*r.n),r.unit_sku_key	,r.unit_sku_root);
							SET lastid=(SELECT LAST_INSERT_ID());
							IF r__=0 THEN 
								SET r__=lastid;
							ELSE
								SET __r=lastid;
							END IF;
							UPDATE bill_in_list SET sq=lastid WHERE id=lastid;
							UPDATE bill_sell SET  user_edit=@user,stat='c' WHERE bill_sell.sku=@sku;
						END IF;
					END LOOP;
				CLOSE cur1;
				IF __r=0 THEN 
					SET __r=r__;
				END IF;
				IF n_list>0 THEN
					INSERT INTO  bill_in  (in_type,sku,lot_from,lot_root,bill,n,sum,user,note,r_,_r) 
					VALUES ('c',k,NULL,NULL,@sku,n_list,sums,@user,@note,r__,__r);			
				END IF;
			END IF;
		END;	";
				
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@TEST AS `TEST`";
		$se=$this->metMnSql($sql,["result"]);
		if(isset($se["data"]["result"][0]["message_error"])&&$se["data"]["result"][0]["message_error"]!=""){
			$se["result"]=0;
			$se["message_error"]=$se["data"]["result"][0]["message_error"];
		}
		print_r($se);
		return $se;
	}
	
}
