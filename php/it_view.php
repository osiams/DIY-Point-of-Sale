<?php
class it_view extends it{
	public function __construct(){
		parent::__construct();
		$this->setDir();
		$this->per=10;
		$this->page=1;
		$this->txsearch="";
		$this->fl="";
		$this->lid=0;
		$this->sh="";
	}
	public function run(){
		$this->page=$this->setPageR();
		if(isset($_GET["sku_root"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["sku_root"])){
			$sku_root=$_GET["sku_root"];
			$this->defaultPageSearch();
			$se=$this->getPdList($sku_root);
			if($se["message_error"]==""&&count($se["it"])>0&&count($se["product"])>=0){
				$href="?a=it&amp;b=view&amp;sku_root=".$sku_root;
				$text=" ".htmlspecialchars($se["it"]["name"]);
				$this->addDir($href,$text);
				if(isset($_GET["c"])&&$_GET["c"]=="lot"){
					(new it_view_lot(["dir"=>$this->dir]))->run();
				}else{
					$this->_page($sku_root,$se);
				}		
			}else{
				$this->addDir("",$se["message_error"]);
				$se["it"]["name"]=$se["message_error"];
				$this->_page($sku_root,$se);
			}	
		}
	}
	protected function _page(string $sku_root,array $se):void{
		$this->pageHead(["title"=>"คลังสินค้า DIYPOS","css"=>["it"],"js"=>["it","It"]]);
		if(count($se)>0&&$se["message_error"]==""){
			$this->writeContentItView($sku_root,$se["it"],$se["product"],$se["count"]);
		}else{
			$this->writeContentItView($sku_root,$se["it"],[],0);
		}
		$this->pageFoot();		
	}
	private function writeContentItView(string $sku_root,array $it,array $pd,int $count=0):void{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		echo '<div class="content">
				<div class="form">
					<h2 class="c">คลังสินค้า '.htmlspecialchars($it["name"]).' [ทั้งหมด '.$count.' ชนิด]</h2>';
		echo 	'<div class="pd_search">
						<form class="form100" name="pd_search" action="" method="get">
							<input type="hidden" name="a" value="it" />
							<input type="hidden" name="b" value="view" />
							<input type="hidden" name="sku_root" value="'.$sku_root.'" />
							<input type="hidden" name="lid" value="0" />
							<label><select name="fl">
								<option value="name"'.(($this->fl=="name")?" selected":"").'>ชื่อ</option>
								<option value="barcode"'.(($this->fl=="barcode")?" selected":"").'>รหัสแท่ง</option>
								<option value="sku"'.(($this->fl=="sku")?" selected":"").'>รหัสภายใน</option>
							</select> <input type="text" name="tx" value="'.str_replace("\\","",htmlspecialchars($this->txsearch)).'" /> <input type="submit" value="🔍" /></label></form>
					</div>';
		echo '<form class="form100"  name="product" method="post">
			<input type="hidden" name="sku_root" value="" />
			<table id="it_view"><tr><th>ที่</th>
			<th>รหัสภายใน</th>
			<th>รหัสแท่ง</th>
			<th>ชื่อ</th>
			<th>ราคา</th>
			<th>ต้นทุน</th>
			<th>จำนวน</th>
			<th>หน่วย</th>
			<th>งวด</th>
			</tr>';
		$se=$pd;
		for($i=0;$i<count($se);$i++){
			$ed='';
			if($se[$i]["product_sku_root"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}
			$id=($this->page-1)*$this->per+$i+1;
			$cm=($i%2!=0)?" class=\"i2\"":"";
			$name=htmlspecialchars($se[$i]["name"]);
			$barcode=$se[$i]["barcode"];
			$sku=$se[$i]["sku"];
			if($this->txsearch!=""){
				$ts=htmlspecialchars(str_replace("\\","",$this->txsearch));
				if($this->fl=="name"){
					$name=str_replace($ts,'<span class="bgyl">'.$ts.'</span>',$name);
				}else if($this->fl=="barcode"){
					$barcode=str_replace($ts,'<span class="bgyl">'.$ts.'</span>',$barcode);
				}else if($this->fl=="sku"){
					$sku=str_replace($ts,'<span class="bgyl">'.$ts.'</span>',$sku);
				}
				
			}
			echo '<tr'.$cm.'><td>'.$id.'</td>
				<td>'.$sku.'</td>
				<td>'.$barcode.'</td>
				<td class="l">
					<div><a href="?a=it&amp;b=view&amp;sku_root='.$sku_root.'&amp;c=lot&amp;pd='.$se[$i]["product_sku_root"].'">'.$name.'</a></div>
					<div>'.$sku.','.$barcode.'</div>
				</td>
				<td class="r">
					<div>'.$se[$i]["price"].'</div>
					<div>'.number_format($se[$i]["cost"],2,'.',',').'</div>
				</td>
				<td class="r">'.number_format($se[$i]["cost"],2,'.',',').'</td>
				<td class="r">
					<div>'.number_format($se[$i]["balance"],0,'.',',').'</div>
					<div>'.htmlspecialchars($se[$i]["unit_name"]).'</div>
				</td>
				<td class="l">'.$se[$i]["unit_name"].'</td>
				<td class="r">'.number_format($se[$i]["count"],0,'.',',').'</td>
				</tr>';
				$this->lid=$se[$i]["id"];
		}
		echo '</table></form>';

		if($this->txsearch==""){
			$this->page($count,$this->per,$this->page,"?a=it&amp;b=view&amp;sku_root=".$sku_root."&amp;page=");	
		}else{
			$this->pageSearch($sku_root,count($se));
		}
		echo '</div></div>';
		//$this->page($count,$this->per,$this->page,"?a=it&amp;b=view&amp;sku_root=".$sku_root."&amp;page=");	
	}
	private function getPdList(string $sku_root):array{
		$sh=$this->defaultSearch();
		$re=["it"=>[],"message_error"=>""];
		$sku_root=$this->getStringSqlSet($sku_root);
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@sku_root:=".$sku_root.",
			@it:=(SELECT COUNT(*)  FROM `it` WHERE `sku_root`=".$sku_root.");
		";
		$sql["check"]="
			IF @it = 0 THEN 
				SET @message_error='เกิดขอผิดพลาด ไม่พบคลังสินค้าที่ต้องการดู';
			END IF;			
		";
		$sql["count"]="SELECT COUNT(DISTINCT (bill_in_list.product_sku_root)) AS `count`
			FROM bill_in_list 
			WHERE bill_in_list.balance>0 AND bill_in_list.stroot=@sku_root;
		";
		$sql["it"]="SELECT  * FROM it WHERE sku_root=@sku_root;";
		$limit_page=(($this->page-1)*$this->per).",".($this->per+1);
		if($this->txsearch!=""){
				$limit_page=$this->per+1;
		}
		$sql["product"]="SELECT bill_in_list.sum,bill_in_list.n,bill_in_list.product_sku_root,
					IFNULL(SUM(bill_in_list.balance),0) AS balance,
					IFNULL(COUNT(*),0) AS `count`,
					`product`.`id`,product.sku,product.barcode,product.name,product.price,product.cost,
					unit.name AS unit_name
				FROM bill_in_list
				LEFT JOIN product
				ON(bill_in_list.product_sku_root=product.sku_root)
				LEFT JOIN unit
				ON(product.unit=unit.sku_root)
				WHERE  bill_in_list.balance>0 AND bill_in_list.stroot=@sku_root
				".$this->sh." 
				GROUP BY bill_in_list.product_sku_root  ORDER BY `product`.`id` LIMIT ".$limit_page.";
		";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@TEST AS `TEST`";
		$se=$this->metMnSql($sql,["it","count","product","result"]);
		if($se["result"]){
			if(isset($se["data"]["result"][0])&&$se["data"]["result"][0]["message_error"]==""){
				$re["it"]=$se["data"]["it"][0];
				$re["count"]=(isset($se["data"]["count"][0]["count"]))?$se["data"]["count"][0]["count"]:0;
				$re["product"]=$se["data"]["product"];
				$re["result"]=$se["data"]["result"][0];
			}else{
				$re["message_error"]=$se["data"]["result"][0]["message_error"];
			}
		}
		//print_r($se);
		return $re;
	}
	private function setDir():void{
		$this->addDir("?a=it","คลังสินค้า");
	}
	private function defaultSearch():string{
		$fla=["sku","barcode","name"];
		$fl="name";
		$tx="";
		$se="";
		if(isset($_GET["fl"])){
			if(in_array($_GET["fl"],$fla)){
				if(($_GET["fl"]=="barcode"||$_GET["fl"]=="sku")
				&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["tx"])){
					$fl=$_GET["fl"];
				}	
			}
		}
		$this->fl=$fl;
		if(isset($_GET["tx"])&&strlen(trim($_GET["tx"]))>0){
			$tx=$this->getStringSqlSet($_GET["tx"]);
		}
		if($tx!=""){
			$tx=substr($tx,1,-1);
			$this->txsearch=$tx;
			$se=" AND `product`.`".$fl."` LIKE  \"%".$tx."%\"  ";
		}
		return $se;
	}
	protected function pageSearch(string $sit_sku_root,int $row):void{
		$href='?a=it&amp;b=view&amp;sku_root='.$sit_sku_root.'&amp;fl='.$this->fl.'&amp;tx='.$this->txsearch.'&amp;page=';
		if($this->page>1){
			echo '<a onclick="history.back()">⬅️ก่อนหน้า</a>';
		}
		echo '<span class="it_product_page_search">หน้า '.$this->page.'</span>';
		if($row>$this->per){
			echo '<a href="'.$href.''.($this->page+1).'&amp;lid='.$this->lid.'">ถัดไป➡️</a>';
		}
	}
	private function defaultPageSearch():void{
		$fla=["sku","barcode","name"];
		$fl="name";
		$tx="";
		$se="";
		if(isset($_GET["fl"])){
			if(in_array($_GET["fl"],$fla)){
				if(($_GET["fl"]=="barcode"||$_GET["fl"]=="sku")
				&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["tx"])){
					$fl=$_GET["fl"];
				}
			}
		}
		$this->fl=$fl;
		if(isset($_GET["tx"])&&strlen(trim($_GET["tx"]))>0){
			$tx=$this->getStringSqlSet($_GET["tx"]);
		}
		if(isset($_GET["lid"])&&preg_match("/^[0-9]{1,12}$/",$_GET["lid"])){
			$this->lid=$_GET["lid"];
		}
		if($tx!=""){
			$tx=substr($tx,1,-1);
			$this->txsearch=$tx;
			$this->sh=" AND `product`.`id`>=".$this->lid." AND `product`.`".$fl."` LIKE  \"%".$tx."%\""  ;
		}
	}
}
class it_view_lot extends it_view{
	public function __construct(array $dt=["dir"=>null]){
		parent::__construct();
		$this->dir=$dt["dir"];
	}
	public function run(){
		if(isset($_GET["sku_root"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["sku_root"])
		&&isset($_GET["pd"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["pd"])){
				$this->_pageLot($_GET["sku_root"],$_GET["pd"]);
		}
	}
	public function fetch(){
		$re=["result"=>false,"message_error"=>""];
		if(isset($_POST["sort"])){
			$jsc = json_decode($_POST["sort"]);
			if (json_last_error() === JSON_ERROR_NONE) {
				$se=$this->fetchSortLotUpdate($_POST["sort"]);
				if($se["result"]
					&&isset($se["data"]["result"][0])
					&&$se["data"]["result"][0]["message_error"]==""){
						$re["result"]=true;
				}
			}
			//header('Content-type: application/json');
			//echo json_encode($re);
		}else if(isset($_POST["delete"])&&isset($_POST["billinsku"])&&isset($_POST["y"])){
			if(preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_POST["billinsku"])){
				$y=trim($_POST["y"]);
				if($y=="1"||$y=="2"||preg_match("/^((8 ).*)|(8)$/",$y)){
					echo $y;
				}
			}else{
				$re["message_error"]="รหัสใบเลขที่ไม่ถูกต้อง";
			}
		}else if(isset($_POST["move"])&&isset($_POST["billinid"])&&isset($_POST["st"])){
			if(preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}(=){1}[0-9]{1,10}$/",$_POST["move"])
				&&preg_match("/^[0-9]{1,10}$/",$_POST["billinid"])
				&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_POST["st"])){
				$dt=explode("=",$_POST["move"]);
				$billinid=$_POST["billinid"];
				$it_sku=$dt[0];
				$n=$dt[1];
				$se=$this->fetchMoveStock($billinid,$it_sku,$n,$_POST["st"]);
				if($se["message_error"]!=""||!$se["result"]){
					$re["message_error"]=$se["message_error"];
				}else{
					$re["result"]=true;
					$re["sku"]=$se["sku"];
				}
			}else{
				$re["message_error"]="\n".htmlspecialchars($_POST["move"])."\nไม่อยู่ในรูปแบบ";
			}
		}
		//if(!$re["result"]){
			header('Content-type: application/json');
			echo json_encode($re);
		//}
	}
	private function fetchMoveStock(int $billinid,string $it_sku,int $n,string $st):array{
		$it_sku=$this->getStringSqlSet($it_sku);
		$st=$this->getStringSqlSet($st);
		$user=$this->getStringSqlSet($_SESSION["sku_root"]);
		$re=["result"=>false,"message_error"=>""];
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@billinid:=".$billinid.",
			@it_sku:=".$it_sku.",
			@n:=".$n.",
			@bill_in_id:=0,
			@user:=".$user.",
			@stkey_:='',
			@stroot_:=".$st.",
			@stroot:='',
			@sku_n:='',
			@has_it:=(SELECT COUNT(*) FROM it WHERE  sku=".$it_sku."),
			@balance:=(SELECT balance FROM bill_in_list WHERE  id=".$billinid."),
			@TEST:=''";
		$sql["reck"]="BEGIN NOT ATOMIC 
			DECLARE lastid INT DEFAULT NULL;	
			DECLARE key_n CHAR(25) DEFAULT '';
			DECLARE r__ INT DEFAULT 0;
			DECLARE __r INT DEFAULT 0;
			DECLARE r ROW (id INT,
													stroot VARCHAR(25),
													stkey VARCHAR(25),
													lot VARCHAR(25),
													product_sku_key VARCHAR(25),
													product_sku_root VARCHAR(25),
													sum FLOAT ,
													name  VARCHAR(255)  CHARACTER SET utf8,
													unit_sku_key VARCHAR(25),
													unit_sku_root VARCHAR(25)	,	
													lot_root VARCHAR(25)
													);
			IF @has_it=0 THEN 
				SET @message_error=CONCAT('ไม่พบ รหัสภายในคลังสินค้า  \"',@it_sku,'\" ');
			ELSEIF @n>@balance THEN
				SET @message_error=CONCAT('จำนวนที่ย้ายมีมากกว่า จำนวนที่มีอยู่  มีอยู่ ',@balance);
			ELSE
				SET key_n=KEY_();
				SET @stkey_=(SELECT sku_key FROM it WHERE sku_root=@stroot_);
				SET @stroot=(SELECT sku_root FROM it WHERE sku=@it_sku);
				SET @stkey=(SELECT sku_key FROM it WHERE sku=@it_sku);
				SELECT bill_in_list.id,bill_in_list.stroot,bill_in_list.stkey,bill_in_list.bill_in_sku AS `lot`,bill_in_list.product_sku_key,bill_in_list.product_sku_root,
					(bill_in_list.sum/bill_in_list.n)*@n AS `sum`,bill_in_list.name,
					bill_in_list.unit_sku_key ,bill_in_list.unit_sku_root ,
					bill_in.lot_root AS `lot_root`
				FROM bill_in_list
				LEFT JOIN bill_in
				ON(bill_in_list.bill_in_sku=bill_in.sku)
				WHERE  bill_in_list.id=@billinid
				INTO r;
				IF @stroot!=r.stroot THEN
					UPDATE bill_in_list SET balance=(balance-@n) WHERE id=@billinid;
					INSERT  INTO `bill_in_list`  (`stkey`,`stroot`,`bill_in_sku`,`product_sku_key`,`product_sku_root`,`name`,`n`,`balance`,`sum`,`unit_sku_key`,`unit_sku_root`,`note`,`idkey`) 
					VALUES(@stkey,@stroot,key_n,r.product_sku_key	,r.product_sku_root	,r.name	,@n,@n,r.sum ,r.unit_sku_key	,r.unit_sku_root,NULL,r.id);
					SET lastid=(SELECT LAST_INSERT_ID());
					UPDATE bill_in_list SET sq=lastid WHERE id=lastid;
					INSERT INTO  bill_in  (in_type,sku,lot_from,lot_root,bill,n,sum,user,stkey_,stroot_,r_,_r) 
					VALUES ('m',key_n,r.lot,r.lot_root,NULL,@n/@n,r.sum,@user,@stkey_,@stroot_,lastid,lastid);			
					SET @result=1;
					SET @sku_n=key_n;
				ELSE
					SET @message_error='สินค้าที่ย้ายอยู่คลังเดียวกันอยู่';
				END IF;
			END IF;			
		END;	";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@sku_n AS `sku`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($se);
		if(isset($se["data"]["result"])){
			$re=$se["data"]["result"][0];
		}
		//print_r($re);
		return $re;
	}
	private function fetchSortLotUpdate(string $sort):array{
		$re=[];
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@lot_length:=0,
			@lot:='".$sort."',
			@TEST:=''";
		$sql["update"]="BEGIN NOT ATOMIC
			SET @lot_length=JSON_LENGTH(@lot);
			SET @key=JSON_KEYS(@lot);
			FOR i IN 0..(@lot_length-1) DO
				SET @k=JSON_VALUE(@key		,		CONCAT('$['	,		i		,']')	);
				SET @v=JSON_VALUE(@lot,CONCAT('$.'	,	@k) );
				UPDATE bill_in_list
				SET sq=@v 
				WHERE id=CAST(@k AS INT);
				SET @TEST=CONCAT(@TEST,'-',@k,'=',@v);
			END FOR;	
			SET @result=1;
		END;
		";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@TEST AS `TEST`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($se);
		return $se;
	}
	protected function _pageLot(string $sku_root,string $pd):void{	
		$se=$this->getPdLot($sku_root,$pd);
		if(count($se["it"])>0&&count($se["lot"])>=0){
			$pdname=(isset($se["product"]["name"]))?$se["product"]["name"]:"ไม่มีงวดสินค้า";
			$this->addDir("?a=it&amp;b=view&amp;sku_root=".$sku_root."&amp;c=lot&amp;pd=".$pd," ".htmlspecialchars($pdname));
			$this->pageHead(["title"=>"คลังสินค้า DIYPOS","css"=>["it"],"js"=>["it","It"]]);
			$this->writeContentItViewLot($sku_root,$se["product"],$se["it"],$se["lot"],);
			$this->pageFoot();
		}
	}
	private function writeContentItViewLot(string $sku_root,array $product,array $it,array $pd):void{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$pdname=(isset($product["name"]))?$product["name"]:"ไม่มีงวดสินค้า";
		echo '<div class="content">
				<div class="form">
					<h2 class="c">งวดสินค้า '.htmlspecialchars($pdname).'</h2>';
		echo '<form class="form100"  name="it_view_lot" method="post">
			<input type="hidden" name="a" value="it" />
			<input type="hidden" name="b" value="view" />
			<input type="hidden" name="c" value="lot" />
			<input type="hidden" name="sku_root" value="'.$sku_root.'" />
			<table id="it_view_lot"><tr><th>ที่</th>
			<th>งวด (ตัดสินค้าจากบน ลง ล่าง)</th>
			<th>ชื่อ</th>
			<th>ต้นทุน</th>
			<th>รับเข้า</th>
			<th>เหลือ</th>
			<th>หน่วย</th>
			<th>กระทำ</th>
			</tr>';
		$se=$pd;
		for($i=0;$i<count($se);$i++){
			$ed='';
			if($se[$i]["product_sku_root"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}
			$id=$i+1;
			$cm=($i%2!=0)?" class=\"i2\"":"";
			$tx="";
			$c="in";
			if($se[$i]["in_type"]=="c"){
				$tx=$this->billNote("c",''.$se[$i]["bill"],$se[$i]["note"]);
				$c="sell";
			}else if($se[$i]["in_type"]=="r"){
				$tx=$this->billNote("r",''.$se[$i]["bill"],$se[$i]["note"]);
				$c="ret";
			}else if($se[$i]["in_type"]=="m"){
				$tx=$this->billNote("m",''.$se[$i]["sku"],$se[$i]["note"]);
				$c="move";
			}else if($se[$i]["in_type"]=="mm"){
				$tx=$this->billNote("mm",''.$se[$i]["sku"],$se[$i]["note"]);
				$c="mmm";
			}else if($se[$i]["in_type"]=="b"){
				$tx=$this->billNote("b",''.$se[$i]["note"],'');
				$c="in";
			}else if($se[$i]["in_type"]=="x"){
				$tx=$this->billNote("x",''.$se[$i]["note"],'');
				$c="move";
			}
			$key=$se[$i]["sku"];
			if($se[$i]["in_type"]=="c"){
				$key=$se[$i]["bill"];
			}
			if($edd==$key){
				if($cm==""){
					$cm=' class="ed"';
				}else{
					$cm=substr($cm,0,-1).' ed"';
				}
			}
			//--<td class="l"><div><a href="?a=produc..... ต้องติดกัน 
			$tx='<a href="?a=bills&amp;b=view&amp;c='.$c.'&amp;sku='.$key.'&amp;ed='.$se[$i]["product_sku_root"].'">'.$tx.'</a>';
			echo '<tr'.$cm.'><td data-id="'.$se[$i]["id"].'">'.$id.'</td>
				<td class="l">'.$tx.'<p>ผ่านมา '.$this->ago(time()-strtotime($se[$i]["date_reg"])).'</p></td>
				<td class="l"><div><a href="?a=product&amp;b=details&amp;sku_root='.$se[$i]["product_sku_root"].'">'.$se[$i]["product_name"].'</a>
						<span> ทุนรวม '.number_format($se[$i]["cost"],2,'.',',').'</span>
						</div>
					<div>'.$se[$i]["product_sku"].','.$se[$i]["barcode"].'</div>
				</td>
				<td class="r">'.number_format($se[$i]["cost"],2,'.',',').'</td>
				<td class="r">'.$se[$i]["n"].'</td>
				<td class="r">
					<div>'.$se[$i]["balance"].'</div>
					<div>'.$se[$i]["unit_name"].'</div>
				</td>
				<td class="l">'.$se[$i]["unit_name"].'</td>
				<td class="action">
					<a  id="actionid_'.$se[$i]["id"].'"  data-width="180" onclick="G.action(this)" title="เลือกกระทำ">⚙️</a>
					<a onclick="It.sort(this,\'actionid_'.$se[$i]["id"].'\',\''.$se[$i]["id"].'\','.$id.')" title="จัดลำดับงวด">🔃</a>
					<a onclick="It.move(this,\'actionid_'.$se[$i]["id"].'\',\''.$se[$i]["id"].'\','.$id.',\''.$se[$i]["stroot"].'\')" title="ย้ายไปคลังอืน">🏘️</a>
					<a id="itmmm" onclick="It.m(this,\'actionid_'.$se[$i]["id"].'\',\''.$se[$i]["id"].'\',\''.$se[$i]["product_sku_root"].'\','.($se[$i]["balance"]*1).','.($se[$i]["skuroot1_n"]*1).')" title="แตกหน่วยขาย">💦</a>
					
					';
			if($it["sku_root"]=="xroot"){
				echo '	<a onclick="It.delPd(\''.$se[$i]["sku"].'\',\''.$it["sku_root"].'\',\''.htmlspecialchars($it["name"]).'\',\''.$se[$i]["product_sku_root"].'\',\''.htmlspecialchars($se[$i]["product_name"]).'\','.$se[$i]["balance"].',\''.htmlspecialchars($se[$i]["unit_name"]).'\')" title="ลบทิ้ง จากคลังสินค้านี้">🗑</a>';
			}
			echo		''.$ed.'
					</td>
				</tr>';
		}
		echo '</table></form>';
		echo '</div></div>';
	}
	private function getPdLot(string $sku_root,string $pd):array{
		$re=["it"=>[],"lot"=>[]];
		$sku_root=$this->getStringSqlSet($sku_root);
		$pd=$this->getStringSqlSet($pd);
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@sku_root:=".$sku_root.",
			@pd:=".$pd.",
			@it:=(SELECT COUNT(*)  FROM `it` WHERE `sku_root`=".$sku_root."),
			@pdt:=(SELECT COUNT(*)  FROM `product` WHERE `sku_root`=".$pd.");
		";
		$sql["check"]="
			IF @it = 0 THEN 
				SET @message_error='เกิดขอผิดพลาด ไม่พบคลังสินค้าที่ต้องการดู';
			ELSEIF @pdt = 0 THEN 
				SET @message_error='เกิดขอผิดพลาด ไม่พบสินค้า';
			END IF;			
		";
		$sql["product"]="SELECT name FROM product WHERE sku_root=@pd";
		$sql["it"]="SELECT  * FROM it WHERE sku_root=@sku_root;";
		$sql["lot"]="SELECT bill_in_list.id,bill_in_list.stroot,bill_in_list.n ,bill_in_list.balance,bill_in_list.sum,bill_in_list.product_sku_root,
				bill_in_list.name AS `product_name`,(bill_in_list.sum/bill_in_list.n) AS cost,
				bill_in.in_type,bill_in.bill,IFNULL(bill_in.note,'')  AS bill_note,
				bill_in.sku,IFNULL(bill_in.note,'') AS `note`,bill_in.date_reg,
				product_ref.barcode,product_ref.sku AS product_sku,
				IFNULL(product.skuroot1,'') AS skuroot1,IFNULL(product.skuroot1_n,0) AS skuroot1_n,
				IFNULL(product.skuroot2,'') AS skuroot2,IFNULL(product.skuroot2_n,0) AS skuroot2_n,
				unit_ref.name AS unit_name
			FROM bill_in_list
			LEFT JOIN bill_in
			ON( bill_in_list.bill_in_sku=bill_in.sku)
			LEFT JOIN product
			ON(bill_in_list.product_sku_root=product.sku_root)
			LEFT JOIN product_ref
			ON(bill_in_list.product_sku_key=product_ref.sku_key)
			LEFT JOIN unit_ref
			ON(bill_in_list.unit_sku_key=unit_ref.sku_key)
			WHERE bill_in_list.product_sku_root=@pd AND  bill_in_list.balance>0  AND bill_in_list.stroot=@sku_root ORDER BY bill_in_list.sq,bill_in_list.id ASC;
		";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@TEST AS `TEST`";
		$se=$this->metMnSql($sql,["it","product","lot","result"]);
		if($se["result"]){
			$re["it"]=$se["data"]["it"][0];
			$re["product"]=(isset($se["data"]["product"][0]))?$se["data"]["product"][0]:[];
			$re["lot"]=$se["data"]["lot"];
			$re["result"]=$se["data"]["result"][0];
		}
		//print_r($se);
		return $re;
	}
}
?>
