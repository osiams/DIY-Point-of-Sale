<?php  /*--apt DIY_POS;--ext php;--version 0.0;*/
class it extends main{
	public function __construct(){
		parent::__construct();
	}
	public function run(){
		$q=["regis","edit","view"];
		$this->addDir("?a=it","คลังสินค้า");
		if(isset($_GET["b"])&&in_array($_GET["b"],$q)){
			$t=$_GET["b"];
			if($t=="regis"){
				$this->regisIt();
			}else if($t=="edit"){
				$this->editIt();
			}else if($t=="view"){
				require_once("php/it_".$t.".php");
				eval("(new it_".$t."())->run();");
			}
		}else{
			$this->pageIt();
		}
	}
	public function fetchM(string $b):void{
		$re=["result"=>false,"message_error"=>"","data"=>[],"confirm"=>0];
		if($b=="delete"){
			if(isset($_POST["sku_root"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_POST["sku_root"])){
				$se=$this->deleteIt($_POST["sku_root"]);
				//print_r($se);
				if($se["result"]){
					$re["result"]=true;
				}else if($se["message_error"]!=""){
					$re["message_erroe"]=$se["message_error"];
				}
			}
		}else if($b=="select"){
			if(isset($_POST["pdroot"])&&$this->isSKU($_POST["pdroot"])
				&&isset($_POST["sku_root"])&&$_POST["sku_root"]=="proot"){
				$se=$this->fetchLot($_POST["pdroot"]);
				if($se["result"]){
					$re["data"]=$se["lot"];
					$re["result"]=true;
				}else{
					$re["message_error"]=$se["message_error"];
				}
			}else{
				$re["message_error"]="ค่าที่ส่งมาไม่ถูกต้อง";
			}
		}else if($b=="mmm"){
			$se=$this->fetchmmm($_POST["data"]);
			if($se["result"]){
				$re["result"]=true;
			}else{
				$re["message_error"]=$se["message_error"];
				$re["confirm"]=$se["confirm"];
			}
		}else if($b=="mmmgetused"){
			if(isset($_POST["pdroot"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_POST["pdroot"])){
				$se=$this->fetchmmmgetused($_POST["pdroot"]);
				if(isset($se["data"]["result"][0])){
					$re["data"]=json_encode($se["data"]["result"][0]);
					$re["result"]=true;
				}else{
					$re["message_error"]=$se["message_error"];
				}
			}else{
				
			}
		}

		header('Content-type: application/json');
		echo json_encode($re);
	}
	private function fetchmmmgetused(string $pdroot):array{
		$sql=[];
		$sql["result"]="SELECT product1.name AS skuroot1_name,product1.barcode AS skuroot1_barcode,
				product.skuroot1 AS  skuroot1,IFNULL(product.skuroot2,'') AS  skuroot2,
				product.skuroot1_n AS  skuroot1_n,IFNULL(product.skuroot2_n,0) AS  skuroot2_n,
				IFNULL(product2.name,'') AS skuroot2_name,IFNULL(product2.barcode,'') AS skuroot2_barcode,
				unit1.name AS skuroot1_unit,
				IFNULL(unit2.name,'') AS skuroot2_unit
			FROM product
			LEFT JOIN product AS product1
			ON(product.skuroot1=product1.sku_root)
			LEFT JOIN unit AS unit1
			ON(product1.unit=unit1.sku_root)
			LEFT JOIN product AS product2
			ON(product.skuroot2=product2.sku_root)		
			LEFT JOIN unit AS unit2
			ON(product2.unit=unit2.sku_root)
			WHERE product.sku_root='".$pdroot."'
		";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($se);
		return $se;
	}
	private function fetchmmm(string $jsondata):array{
		$re=["result"=>false,"message_error"=>""];
		if(gettype(json_decode($jsondata,true))!="array"){
			$re["message_error"]="สินค้าที่เลือกหรือข้อมูลที่ส่งมาไม่อยู่ในรูปแบบ";
		}else{
			$dt=json_decode($jsondata,true);
			if(!isset($dt["skuroot2"])){
				$dt["skuroot2"]="";
				$dt["skuroot2_n"]=0;
			}
			if(!isset($dt["skuroot2_n"])||$dt["skuroot2_n"]<=0){
				$dt["skuroot2"]="";
				$dt["skuroot2_n"]=0;
			}
			if(!isset($dt["billinlistid"])||!isset($dt["skuroot"])||!isset($dt["skuroot1"])||!isset($dt["skuroot_n"])||!isset($dt["skuroot1_n"])){
				$re["message_error"]="ข้อมูลที่ส่งมาไม่ครบถ้วน";
			}else if(!preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$dt["skuroot"])
				||!preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$dt["skuroot1"])
				||($dt["skuroot2"]!=""&&!preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$dt["skuroot2"]))){
				$re["message_error"]="ข้อมูลที่ส่งมาไม่ถูกต้อง";
			}else if(!preg_match("/^[0-9]{1,12}$/",$dt["skuroot_n"])
				||!preg_match("/^[0-9]{1,12}$/",$dt["skuroot1_n"])
				||($dt["skuroot2_n"]!=0&&!preg_match("/^[0-9]{1,12}$/",$dt["skuroot2_n"]))){
				$re["message_error"]="ข้อมูลที่ส่งมา ไม่ถูกต้อง";
			}else{
				$re=$this->mmm($dt);
				
			}
		}
		return $re;
	}
	private function mmm(array $dt):array{
		$re=["result"=>false,"message_error"=>"","confirm"=>0];
		$user=$this->getStringSqlSet($_SESSION["sku_root"]);
		$skuroot1_name=$this->getStringSqlSet($dt["skuroot1_name"]);
		$skuroot2_name=$this->getStringSqlSet($dt["skuroot2_name"]);
		$confirm=(isset($dt["confirm"])&&$dt["confirm"]=="1")?1:0;
		$sql=[];
		$sql["set"]="SELECT @result:=0,@message_error:='',@confirm:=0,@user:=".$user.",
			@confirm:=".$confirm.",
			@billinlistid:='".$dt["billinlistid"]."',
			@skuroot:='".$dt["skuroot"]."',
			@skuroot_n:=".$dt["skuroot_n"].",
			@skuroot1:='".$dt["skuroot1"]."',
			@skuroot1_n:=".$dt["skuroot1_n"].",
			@skuroot1_name:=".$skuroot1_name.",
			@skuroot2_name:=".$skuroot2_name.",
			@skuroot2:=IF(LENGTH('".$dt["skuroot2"]."')=0,NULL,'".$dt["skuroot2"]."'),
			@skuroot2_n:=IF(".$dt["skuroot2_n"]."=0,NULL,".$dt["skuroot2_n"]."),
			@TEST:='',
			@id__skuroot:=(SELECT product_sku_root FROM bill_in_list WHERE id=@billinlistid),
			@skuroot_cost1:=(SELECT sum/n FROM bill_in_list WHERE id=@billinlistid),
			@skuroot1_price:=(SELECT price FROM product WHERE sku_root=@skuroot1),
			@can_n:=(SELECT balance FROM bill_in_list WHERE id=@billinlistid)";
		$sql["check"]="
			IF IFNULL(@can_n,0)<=0 THEN 
				SET @message_error=CONCAT('ไม่มีงวดสินค้าที่ส่งมา หรือ สินค้าไม่มีจำนวน');
			ELSEIF IFNULL(@id__skuroot,'')!=@skuroot THEN 
				SET @message_error=CONCAT('ไม่มีสินค้าที่ส่งมา หรือ สินค้าข้อมูลฟอร์มที่ส่งมาไม่ถูกต้อง');
			ELSEIF @skuroot_n > @can_n THEN
				SET @message_error=CONCAT('จำนวนที่จะแตกออกมากว่าที่มีอยู่ในระบบ');
			ELSEIF @skuroot=@skuroot1 || @skuroot=@skuroot2  THEN
				SET @message_error=CONCAT('สินค้าที่จะแตกหน่วยขาย กับสินค้าที่เลือก ต้องไม่เป็นสินค้าเดียวกัน');
			ELSEIF @skuroot1=@skuroot2 THEN
				SET @message_error=CONCAT('สินค้าที่จะแตกหน่วยขายหลัก กับสินค้าที่แถม  ต้องไม่เป็นสินค้าเดียวกัน');
			ELSEIF MOD(@skuroot1_n ,@skuroot_n)>0 THEN
				SET @message_error=CONCAT('สัดดส่วนการแตกสินค้าต้องเป็นจำนวนเต็ม เช่น 1:12,2:24');
			ELSEIF MOD(@skuroot2_n ,@skuroot_n)>0 THEN
				SET @message_error=CONCAT('สัดดส่วนการแตกสินค้ากับสินค้าแถม ต้องเป็นจำนวนเต็ม เช่น 1:1,2:4');
			ELSEIF (@skuroot_cost1*@skuroot_n)/@skuroot1_n >=@skuroot1_price THEN
				SET @message_error=CONCAT('ดูเหมือนว่าราคาทุนที่ได้จากการแตกสินค้า มากกว่า ราคาขายของสินค้า \nราคาสินต้า = ',
					@skuroot1_price,'  \nราคาทุนที่ได้ =  ',(@skuroot_cost1*@skuroot_n)/@skuroot1_n ,' \nโปรดตรวจสอบ จำนวน ที่แตกออก');
			ELSEIF ((@skuroot1_price/((@skuroot_cost1*@skuroot_n)/@skuroot1_n))-1)*100 > 50  && @confirm=0 THEN
				SET @confirm=1;
				SET @message_error=CONCAT('ดูเหมือนว่าราคาทุนที่ได้จากการแตกสินค้า เมื่อเทียบกับราคาขาย ดูผิดปกติ  \nราคาขายของสินค้า  = ',
					@skuroot1_price,'  \nราคาทุนที่ได้ =  ',(@skuroot_cost1*@skuroot_n)/@skuroot1_n ,'\nได้กำไร ถึง ',FLOOR(((@skuroot1_price/((@skuroot_cost1*@skuroot_n)/@skuroot1_n))-1)*100) ,
					' %  \nโปรดกด ยืนยันว่า ถูกต้อง หรือ ยกเลิก');
			ELSEIF ((@skuroot1_price/((@skuroot_cost1*@skuroot_n)/@skuroot1_n))-1)*100 <15  && @confirm=0 THEN
				SET @confirm=1;
				SET @message_error=CONCAT('ดูเหมือนว่าราคาทุนที่ได้จากการแตกสินค้า เมื่อเทียบกับราคาขาย ดูผิดปกติ  \nราคาขายของสินค้า  = ',
					@skuroot1_price,'\nราคาทุนที่ได้ =  ',(@skuroot_cost1*@skuroot_n)/@skuroot1_n ,'\nได้กำไรแค่ ',FLOOR(((@skuroot1_price/((@skuroot_cost1*@skuroot_n)/@skuroot1_n))-1)*100) ,
					' %   \nโปรดกด ยืนยันว่า ถูกต้อง หรือ ยกเลิก');
			ELSE 
				SET @confirm=0;
			END IF;			
		";
		$sql["run"]="BEGIN NOT ATOMIC
				DECLARE lastid INT DEFAULT NULL;	
				DECLARE pdskuroot1 CHAR(25) DEFAULT NULL;
				DECLARE pdskuroot1_n INT DEFAULT 0;
				DECLARE pdskuroot2 CHAR(25) DEFAULT NULL;
				DECLARE pdskuroot2_n INT DEFAULT 0;
				DECLARE mm_key CHAR(25) DEFAULT KEY_();
				DECLARE lot_from_ CHAR(25) DEFAULT NULL;
				DECLARE sum_ FLOAT DEFAULT 0;
				DECLARE lot_root_ CHAR(25) DEFAULT NULL;
				DECLARE bill_n_ INT DEFAULT 1;
				DECLARE r_billin ROW (	in_type CHAR(2),
														lot_root CHAR(25),
														bill CHAR(25));
				DECLARE r_billinlist ROW (
														stkey  CHAR(25),
														stroot  CHAR(25),
														product_sku_key  CHAR(25),
														product_sku_root  CHAR(25),
														lot_from CHAR(25),
														cost1 FLOAT);
				DECLARE r_billinlist1 ROW (
														product_sku_key  CHAR(25),
														product_sku_root  CHAR(25),
														unit_sku_key  CHAR(25),
														unit_sku_root  CHAR(25));
				DECLARE r_billinlist2 ROW (
														product_sku_key  CHAR(25),
														product_sku_root  CHAR(25),
														unit_sku_key  CHAR(25),
														unit_sku_root  CHAR(25));

				DECLARE r ROW (skuroot1 CHAR(25),
													skuroot1_n INT,
													skuroot2 CHAR(25),
													skuroot2_n INT);		
				DECLARE r__ INT DEFAULT 0;
				DECLARE __r INT DEFAULT 0;
				#SET @message_error=CONCAT('***',@message_error,((@skuroot1_price/((@skuroot_cost1*@skuroot_n)/@skuroot1_n))-1)*100);
				IF @message_error='' THEN
					SELECT skuroot1,IFNULL(skuroot1_n,0),skuroot2,IFNULL(skuroot2_n ,0)
						INTO r.skuroot1, r.skuroot1_n, r.skuroot2, r.skuroot2_n 
						FROM product 
						WHERE sku_root=@skuroot;
					CALL TEST_(CURTIME()+0,@skuroot);	
					IF r.skuroot1!=@skuroot1 OR  r.skuroot1_n!=@skuroot1_n  
						OR r.skuroot2!=IFNULL(@skuroot2,'') OR  r.skuroot2_n!=IFNULL(@skuroot2_n,0)  THEN
						UPDATE product 
							SET sku_key=KEY_(),skuroot1=@skuroot1,
								skuroot1_n=@skuroot1_n/@skuroot_n,
								skuroot2=@skuroot2,
								skuroot2_n=@skuroot2_n/@skuroot_n
							WHERE sku_root=@skuroot;
						INSERT IGNORE INTO `product_ref`  
							SELECT * FROM `product` WHERE  `product`.`sku_root` = @skuroot ;
					END IF;
					

					IF @skuroot2_n>0 THEN
						SET bill_n_=2;
					END IF;
						
					SELECT  stkey,stroot,product_sku_key,product_sku_root,bill_in_sku,(sum/n) INTO r_billinlist.stkey,
							r_billinlist.stroot,r_billinlist.product_sku_key,r_billinlist.product_sku_root,
							r_billinlist.lot_from,r_billinlist.cost1
						FROM bill_in_list
						WHERE id=@billinlistid;
					SET  lot_from_=r_billinlist.lot_from;
					SET  sum_=r_billinlist.cost1;
						
					SELECT in_type,lot_root,bill INTO r_billin.in_type,r_billin.lot_root,r_billin.bill
						FROM bill_in 
						WHERE  sku=lot_from_;
							
					IF r_billin.in_type='r' || r_billin.in_type='c' THEN
						SET lot_root_=FINDLOTROOT_(lot_from_,@skuroot);
					ELSE 
						SET lot_root_=r_billin.lot_root;
					END IF;
								
					UPDATE bill_in_list SET balance=balance-@skuroot_n
						WHERE id=@billinlistid;
						

					INSERT INTO bill_in(in_type,sku,lot_from,lot_root,n,sum,user)
						VALUES('mm',mm_key,lot_from_,lot_root_,bill_n_,(sum_*@skuroot_n),@user);
					SET lastid=(SELECT LAST_INSERT_ID());
					INSERT INTO mmm (bill_in_id,skukey,skuroot,skuroot_n)
						VALUES(lastid,r_billinlist.product_sku_key,r_billinlist.product_sku_root,@skuroot_n);

					SELECT product.sku_key,product.sku_root,
							unit.sku_key ,unit.sku_root
						INTO r_billinlist1.product_sku_key,r_billinlist1.product_sku_root,
							r_billinlist1.unit_sku_key,r_billinlist1.unit_sku_root
						FROM product
						LEFT JOIN unit 
						ON(product.unit=unit.sku_root)
						WHERE  product.sku_root=@skuroot1;
					
					INSERT INTO bill_in_list(stkey,stroot,bill_in_sku,product_sku_key,product_sku_root,
							name,n,balance,sum,unit_sku_key,unit_sku_root)
						VALUES(r_billinlist.stkey,r_billinlist.stroot,mm_key,
											r_billinlist1.product_sku_key,r_billinlist1.product_sku_root,
												@skuroot1_name,@skuroot1_n,@skuroot1_n,(sum_*@skuroot_n),
												r_billinlist1.unit_sku_key,r_billinlist1.unit_sku_root
						);
						
					SET lastid=(SELECT LAST_INSERT_ID());
					UPDATE bill_in_list SET sq=lastid WHERE id=lastid;
					IF r__=0 THEN 
							SET r__=lastid;
					ELSE
							SET __r=lastid;
					END IF;
						
					IF bill_n_=2 THEN
						SELECT product.sku_key,product.sku_root,
								unit.sku_key ,unit.sku_root
							INTO r_billinlist2.product_sku_key,r_billinlist2.product_sku_root,
								r_billinlist2.unit_sku_key,r_billinlist2.unit_sku_root
							FROM product
							LEFT JOIN unit 
							ON(product.unit=unit.sku_root)
							WHERE  product.sku_root=@skuroot2;
						
						INSERT INTO bill_in_list(stkey,stroot,bill_in_sku,product_sku_key,product_sku_root,
								name,n,balance,sum,unit_sku_key,unit_sku_root)
							VALUES(r_billinlist.stkey,r_billinlist.stroot,mm_key,
												r_billinlist2.product_sku_key,r_billinlist2.product_sku_root,
													@skuroot2_name,@skuroot2_n,@skuroot2_n,0,
													r_billinlist2.unit_sku_key,r_billinlist2.unit_sku_root
							);
						SET lastid=(SELECT LAST_INSERT_ID());
						UPDATE bill_in_list SET sq=lastid WHERE id=lastid;
						IF r__=0 THEN 
							SET r__=lastid;
						ELSE
							SET __r=lastid;
						END IF;
					END IF;
					
					IF __r=0 THEN 
						SET __r=r__;
					END IF;			
								
					UPDATE bill_in SET r_=r__,_r=__r WHERE sku=mm_key;
					
					#IF skuroot2_n>0 THEN
					#END IF;	
					
					SET @result=1;

				END IF;
			END;
		";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@confirm AS confirm,@TEST AS `TEST`";	
		$se=$this->metMnSql($sql,["result"]);
		//print_r($se);
		if(isset($se["data"]["result"][0])){
			if($se["data"]["result"][0]["result"]==1){
				$re["result"]=true;
			}else{
				$re["message_error"]=$se["data"]["result"][0]["message_error"];
			}
			$re["confirm"]=$se["data"]["result"][0]["confirm"];
		}
		//print_r($re);
		return $re;
	}
	private function deleteIt(string $sku_root):array{
		$re=["result"=>false,"message_error"=>""];
		if(isset($_POST["sku_root"])){
			$sku_root=$this->getStringSqlSet($sku_root);
			$user=$this->getStringSqlSet($_SESSION["sku_root"]);
			$sql=[];
			$sql["set"]="SELECT @result:=0,@message_error:='',@user:=".$user.",
				@itkey:='',
				@itroot:=".$sku_root.",@TEST:=''";
			$sql["check"]="
				IF @itroot='proot'||@itroot='xroot'||@itroot='defaultroot'||@itroot='droot'||@itroot='eroot' THEN 
					SET @message_error=CONCAT('เกิดขอผิดพลาด ไม่สามรถลบครังนี้ได้ เนื่องจากเป็นคลังสินค้าที่จำเป็นต้องมีในระบบ');
				END IF;			
			";
			$sql["del"]="BEGIN NOT ATOMIC
				DECLARE done INT DEFAULT FALSE;
				DECLARE k CHAR(25) DEFAULT '';
				DECLARE n_list INT DEFAULT 0;
				DECLARE sum FLOAT DEFAULT 0;
				DECLARE stkey CHAR(25) DEFAULT '';
				DECLARE sku_ed CHAR(25) DEFAULT '';
				DECLARE n_list_nw INT DEFAULT 0;
				DECLARE sum_nw FLOAT DEFAULT 0;
				DECLARE sku_nw CHAR(25) DEFAULT '';
				DECLARE date_reg_now CHAR(19) DEFAULT NOW();
				DECLARE lastid INT DEFAULT NULL;	
				DECLARE r__ INT DEFAULT 0;
				DECLARE __r INT DEFAULT 0;
				DECLARE r ROW (id INT,
												in_type CHAR(10),
												sku VARCHAR(25),
												lot_root VARCHAR(25),
												n INT ,
												sum FLOAT,
												changto  CHAR(10),
												stkey VARCHAR(25),
												stroot VARCHAR(25),
												product_sku_key VARCHAR(25),
												product_sku_root VARCHAR(25),
												name  VARCHAR(255),
												balance INT ,
												suml FLOAT,
												sq INT ,
												unit_sku_key VARCHAR(25),
												unit_sku_root VARCHAR(25)
											);
				DECLARE cur1 CURSOR FOR 
				SELECT  bill_in_list.id,bill_in.in_type,bill_in.sku,bill_in.lot_root, bill_in.n,bill_in.sum,bill_in.changto,
					 bill_in_list.stkey,bill_in_list.stroot,bill_in_list.product_sku_key,bill_in_list.product_sku_root,
					 bill_in_list.name,bill_in_list.balance,((bill_in_list.balance/bill_in_list.n)*bill_in_list.sum) AS `suml`,
					 bill_in_list.sq,bill_in_list.unit_sku_key,bill_in_list.unit_sku_root
					FROM bill_in_list
					LEFT JOIN bill_in
					ON(bill_in_list.bill_in_sku=bill_in.sku)
					WHERE bill_in_list.stroot=@itroot AND bill_in_list.balance>0;
				DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;	
				SET k=KEY_();
				SET @itkey=(SELECT sku_key FROM  it WHERE sku_root=@itroot);
				SET stkey=(SELECT sku_key FROM  it WHERE sku_root='defaultroot');
				IF @message_error='' THEN
					OPEN cur1;
						read_loop: LOOP
							FETCH cur1 INTO r;
							IF done THEN
								LEAVE read_loop;
							END IF;
							SET n_list=n_list+1;
							SET sum=sum+r.suml;							
							IF sku_ed='' THEN
								SET sku_ed=r.sku;
							ELSEIF r.sku=sku_ed THEN
								SET @rr=1;
							END IF;
							UPDATE bill_in_list SET balance=0 WHERE id=r.id;
							UPDATE bill_in SET user_edit=@user WHERE  sku=r.sku;
							INSERT bill_in_list (stkey,stroot,bill_in_sku,product_sku_key,product_sku_root,name,n,balance,sum,sq,unit_sku_key,unit_sku_root,idkey)
							VALUES(stkey,'defaultroot',k,r.product_sku_key,r.product_sku_root,r.name,r.balance,r.balance,r.suml,r.sq,r.unit_sku_key,r.unit_sku_root,r.id);
							SET lastid=(SELECT LAST_INSERT_ID());
							IF r__=0 THEN 
								SET r__=lastid;
							ELSE
								SET __r=lastid;
							END IF;
						END LOOP;
						IF n_list>0 THEN
							IF __r=0 THEN 
								SET __r=r__;
							END IF;
							INSERT INTO bill_in (in_type,sku,lot_from,lot_root,n,sum,user,stkey_,stroot_,date_reg,r_,_r)
							VALUES('x',k,NULL,NULL,n_list,sum,@user,@itkey,@itroot,date_reg_now,r__,__r);
						END IF;
						SET @result=1;
					CLOSE cur1;
				END IF;	
				DELETE FROM `it` WHERE `sku_root`=@itroot;
			END;	";
			$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@TEST AS `TEST`";	
			$se=$this->metMnSql($sql,["result"]);
			//print_r($se);
			if($se["result"]){
				if($se["data"]["result"][0]["message_error"]!=""){
					$re["message_error"]=$se["data"]["result"][0]["message_error"];
				}else if($se["data"]["result"][0]["result"]==1){
					$re["result"]=true;
				}
			}
		}
		return $re;
	}
	protected function editIt():void{
		$error="";
		if(isset($_POST["submit"])&&$_POST["submit"]=="clicksubmit"){
			$se=$this->checkSet("unit",["post"=>["name","sku","sku_root","note"]],"post");
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				 $qe=$this->editItUpdate();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					 //print_r($qe);
					header('Location:?a=it&ed='.$_POST["sku_root"]);
				}
			}
			if($error!=""){
				$this->editItPage($error);
			}
		}else{
			$sku_root=(isset($_POST["sku_root"]))?$_POST["sku_root"]:"";
			$this->editItSetCurent($sku_root);
			$this->editItPage($error);
		}
	}
	private function editItUpdate():array{
		$name=$this->getStringSqlSet($_POST["name"]);
		$note=$this->getStringSqlSet($_POST["note"]);
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$skuk=$this->key("key",7);
		$sku_key=$this->getStringSqlSet($skuk);
		$sku_root=$this->getStringSqlSet($_POST["sku_root"]);
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@count_name:=(SELECT COUNT(`id`)  FROM `it` WHERE `name`=".$name." AND `sku_root` !=".$sku_root."),
			@count_sku:=(SELECT COUNT(`id`)   FROM `it` WHERE `sku`=".$sku." AND `sku_root` !=".$sku_root.");
		";
		$sql["check"]="
			IF @count_name > 0 THEN 
				SET @message_error='เกิดขอผิดพลาด ชื่อที่แก้ไขมา มีแล้ว โปรดลองชื่ออื่น';
			ELSEIF @count_sku > 0 THEN
				SET @message_error='เกิดขอผิดพลาด รหัสภายในทแก้ไขมา มีแล้ว โปรดลอง รหัสภายในอื่น';
			END IF;			
		";
		$sql["run"]="
			IF LENGTH(@message_error) = 0 THEN
				UPDATE `it` SET  `sku`=".$sku.",`sku_key`=".$sku_key.",  `name`= ".$name.",  `note`= ".$note."  WHERE `sku_root`=".$sku_root.";
				SET @result=1;
			END IF;
		";
		$sql["ref"]=$this->ref("it","sku_key",$skuk);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($sql);
		return $se;
	}
	protected function editItSetCurent(string $sku_root):void{
		$od=$this->editItOldData($sku_root);
		$fl=["sku","name","note"];
		foreach($fl as $v){
			if(isset($od[$v])){
				$_POST[$v]=$od[$v];
			}
		}
	}
	protected function editItOldData(string $sku_root):array{
		$sku_root=$this->getStringSqlSet($sku_root);
		$sql=[];
		$sql["result"]="SELECT `name`,`sku`,`note` FROM `it` WHERE `sku_root`=".$sku_root."";
		$re=$this->metMnSql($sql,["result"]);
		if(isset($re["data"]["result"][0])){
			return $re["data"]["result"][0];
		}
		return [];
	}
	protected function editItPage(string $error):void{
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$note=(isset($_POST["note"]))?htmlspecialchars($_POST["note"]):"";
		$sku=(isset($_POST["sku"]))?htmlspecialchars($_POST["sku"]):"";
		$sku_root=(isset($_POST["sku_root"]))?htmlspecialchars($_POST["sku_root"]):"";
		$this->addDir("","แก้ไขคลังสินค้า ".$name);
		$this->pageHead(["title"=>"แก้ไขคลังสินค้า DIYPOS"]);
		echo '<div class="content">
			<div class="form">
				<h2 class="c">แก้ไขหน่วยสินค้า</h2>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}		
		echo '		<form method="post">
					<input type="hidden" name="submit" value="clicksubmit" />
					<input type="hidden" name="sku_root" value="'.$sku_root.'" />
					<p><label for="it_sku">รหัสภายใน</label></p>
					<div><input id="it_sku" type="text" value="'.$sku.'"  name="sku"  autocomplete="off" /></div>
					<p><label for="it_name">ชื่อ</label></p>
					<div><input id="it_name"  class="want" type="text" name="name" value="'.$name.'" autocomplete="off" /></div>
					<p><label for="it_note">รายละเอียดย่อ</label></p>
					<div><input id="it_note" type="text" name="note" value="'.$note.'" autocomplete="off" /></div>
					<br />
					<input type="submit" value="แก้ไข" />
				</form>
			</div>
		</div>';
	}
	protected function regisIt():void{
		$error="";
		if(isset($_POST["submit"])&&$_POST["submit"]=="clicksubmit"){
			$se=$this->checkSet("it",["post"=>["name","sku","note"]],"post");
			//print_r($se);
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				 $qe=$this->regisItInsert();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					 //print_r($qe);
					header('Location:?a=it&ed='.$qe["data"]["result"][0]["sku_root"]);
				}
			}
			if($error!=""){
				$this->regisItPage($error);
			}
		}else{
			$this->regisItPage($error);
		}
	}
	protected function regisItInsert():array{
		$name=$this->getStringSqlSet($_POST["name"]);
		$note=$this->getStringSqlSet($_POST["note"]);
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$skuk=$this->key("key",7);
		$sku_root=$this->getStringSqlSet($skuk);
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@sku_root:=".$sku_root.",
			@count_name:=(SELECT COUNT(`id`)  FROM `it` WHERE `name`=".$name."),
			@count_sku:=(SELECT COUNT(`id`)   FROM `it` WHERE `sku`=".$sku.");
		";
		$sql["check"]="
			IF @count_name > 0 THEN 
				SET @message_error='เกิดขอผิดพลาด ชื่อที่ส่งมา มีแล้ว โปรดลองชื่ออื่น';
			ELSEIF @count_sku > 0 THEN
				SET @message_error='เกิดขอผิดพลาด รหัสภายในที่ส่งมา มีแล้ว โปรดลอง รหัสภายในอื่น';
			END IF;			
		";
		$sql["run"]="
			IF LENGTH(@message_error) = 0 THEN
				INSERT INTO `it`  (`sku`,`sku_key`,`sku_root`,`name`,`note`) VALUES (".$sku.",".$sku_root.",".$sku_root.",".$name.",".$note.");
				SET @result=1;
			END IF;
		";
		$sql["ref"]=$this->ref("it","sku_key",$skuk);
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@sku_root AS `sku_root`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($sql);
		return $se;
	}
	protected function regisItPage(string $error):void{
		$name=(isset($_POST["name"]))?htmlspecialchars($_POST["name"]):"";
		$note=(isset($_POST["note"]))?htmlspecialchars($_POST["note"]):"";
		$sku=(isset($_POST["sku"]))?htmlspecialchars($_POST["sku"]):"";
		$this->addDir("?a=it&amp;b=regis","เพิ่มคลังสินค้า");
		$this->pageHead(["title"=>"เพิ่มคลังสินค้า DIYPOS"]);
		echo '<div class="content">
			<div class="form">
				<h2 class="c">เพิ่มคลังสินค้า</h2>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}		
		echo '		<form method="post">
					<input type="hidden" name="submit" value="clicksubmit" />
					<p><label for="it_sku">รหัสภายใน</label></p>
					<div><input id="it_sku" type="text" value="'.$sku.'"  name="sku" autocomplete="off"  /></div>					
					<p><label for="it_name">ชื่อ</label></p>
					<div><input id="it_name" class="want" type="text" name="name" value="'.$name.'" autocomplete="off" /></div>
					<p><label for="it_note">รายละเอียดย่อ</label></p>
					<div><input id="it_note" type="text" name="note" value="'.$note.'" autocomplete="off" /></div>
					<br />
					<input type="submit" value="เพิ่ม" />
				</form>
			</div>
		</div>';
		$this->pageFoot();
	}
	protected function regisUnitCheck():array{
		
	}
	protected function pageIt(){
		$this->pageHead(["title"=>"คลังสินค้า DIYPOS","css"=>["it"],"js"=>["it","It"]]);
			echo '<div class="content">
				<div class="form">
					<h2 class="c">คลังสินค้า</h2>';
			$this->writeContentIt();
			echo '<br /><p class="c"><input type="button" value="เพิ่มคลังสินค้า" onclick="location.href=\'?a=it&b=regis\'" /></p>';
		$this->pageFoot();
	}
	protected function writeContentIt():void{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$se=$this->getAllIt();
		echo '<form class="form100" name="it" method="post">
			<input type="hidden" name="sku_root" value="" />
			<table class="it0"><tr><th>ที่</th>
			<th>รหัสภายใน</th>
			<th>ชื่อ/อธิบาย</th>
			<th>จำนวน<br />งวด</th>
			<th>จำนวน<br />ชนิดสินค้า</th>
			<th>จำนวน<br />หน่วย</th>
			<th>ต้นทน<br />รวม</th>
			<th>กระทำ</th>
			</tr>';
		$default_sku="";
		for($i=0;$i<count($se);$i++){
			$ed='';
			if($se[$i]["sku_root"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}
			$cm=($i%2!=0)?" class=\"i2\"":"";
			if($se[$i]["sku_root"]=="defaultroot"){
				$default_sku=$se[$i]["sku"];
			}
			echo '<tr'.$cm.'><td>'.$se[$i]["id"].'</td>
				<td class="l">'.$se[$i]["sku"].'</td>
				<td class="l"><a href="?a=it&amp;b=view&amp;sku_root='.$se[$i]["sku_root"].'">'.htmlspecialchars($se[$i]["name"]).'</a>
					<div>'.$se[$i]["sku"].'</div>
					<p class="size12 gray555 ti20">'.htmlspecialchars($se[$i]["note"]).'</p></td>
				<td class="r">'.number_format($se[$i]["count"],0,'.',',').'</td>
				<td class="r">'.number_format($se[$i]["list"],0,'.',',').'</td>
				<td class="r">'.number_format($se[$i]["sum"],0,'.',',').'</td>
				<td class="r">'.number_format($se[$i]["costs"],2,'.',',').'</td>
				<td class="action">
					<a onclick="It.edit(\''.$se[$i]["sku_root"].'\')" title="แก้ไข">📝</a>';
			$o=["proot","xroot","defaultroot","eroot","droot"];
			if(!in_array($se[$i]["sku_root"],$o)){
				$st_name=htmlspecialchars($se[$i]["name"]);
				$st_name=str_replace("\\","\\\\",$st_name);
				$st_name=str_replace("'","\'",$st_name);
				$st_name=str_replace("\"","&quot;",$st_name);
				echo ' <a onclick="It.delete(\''.$se[$i]["sku_root"].'\',\''.$st_name.'\',\''.$default_sku.'\')" title="ทิ้ง">🗑</a> ';
			}
			echo '		'.$ed.'
					</td>
				</tr>';
		}
		echo '</table></form>';
	}
	protected function getAllIt():array{
		$re=[];
		$sql=[];
		$sql["get"]="SELECT  it.id,it.sku,it.name,it.note ,it.sku_key,it.sku_root,
			IFNULL(COUNT(bill_in_list.id),0) AS `count`,
			IFNULL(SUM(bill_in_list.balance),0) AS `sum`,
			IFNULL(COUNT(DISTINCT (bill_in_list.product_sku_root)),0) AS `list`,
			IFNULL(SUM(bill_in_list.balance*(bill_in_list.sum/bill_in_list.n)),0) AS `costs`
			FROM `it` 
			LEFT JOIN bill_in_list
			ON (it.sku_root=bill_in_list.stroot AND bill_in_list.balance>0)
			WHERE  1=1
			GROUP BY it.sku_root
			ORDER BY `costs` DESC
		";
		$se=$this->metMnSql($sql,["get"]);
		//print_r($se);
		if($se["result"]){
			$re=$se["data"]["get"];
		}
		return $re;
	}
	private function fetchLot(string $pdroot):array{
		$pdroot=$this->getStringSqlSet($pdroot);
		$re=["result"=>false,"message_error"=>"","data"=>null];
		$sql=[];
		$sql["lot"]="SELECT bill_in_list.id,bill_in_list.stroot,bill_in_list.n ,bill_in_list.balance,bill_in_list.sum,bill_in_list.product_sku_root,
				bill_in_list.name AS `product_name`,(bill_in_list.sum/bill_in_list.n) AS cost,
				bill_in.in_type,bill_in.bill,IFNULL(bill_in.note,'')  AS bill_note,
				bill_in.sku,IFNULL(bill_in.note,'') AS `note`,bill_in.date_reg,
				product_ref.barcode,product_ref.sku AS product_sku,
				unit_ref.name AS unit_name
			FROM bill_in_list
			LEFT JOIN bill_in
			ON( bill_in_list.bill_in_sku=bill_in.sku)
			LEFT JOIN product_ref
			ON(bill_in_list.product_sku_key=product_ref.sku_key)
			LEFT JOIN unit_ref
			ON(bill_in_list.unit_sku_key=unit_ref.sku_key)
			WHERE bill_in_list.product_sku_root=".$pdroot." AND  bill_in_list.balance>0  AND bill_in_list.stroot='proot' ORDER BY bill_in_list.sq,bill_in_list.id ASC;
		";
		$se=$this->metMnSql($sql,["lot"]);
		if($se["result"]){
			$re["lot"]=$se["data"]["lot"];
			$re["result"]=true;
		}else{
			$re["error_message"]=$se["message_error"];
		}
		//print_r($se);
		return $re;
	}
}
?>
