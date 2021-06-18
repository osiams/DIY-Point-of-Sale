<?php
class bills_pay extends bills{
	public function __construct(){
		parent::__construct();
		$this->title="ใบเสร็จรับเงิน ชำระค้างจ่าย";
		$this->per=10;
		$this->page=1;
		$this->mb_type = [
			"s"=>["icon"=>"🏠","name"=>"ผู้ประกอบการ"],
			"p"=>["icon"=>"🧑","name"=> "ผู้บริโภค"]
		];
		
	}
	public function run(){
		$this->page=$this->setPageR();
		$q=["select","selectview","view","delete"];
		$this->addDir("?a=bills&amp;c=pay",$this->title);
		if(isset($_GET["b"])&&in_array($_GET["b"],$q)){
			$t=$_GET["b"];
			if($t=="view"&&(isset($_GET["sku"])&&preg_match("/^[0-9]{1,25}$/",$_GET["sku"]))){
				$this->pageView($_GET["sku"]);
			}else if($t=="selectview"){
				$this->pageSelectView($_GET["sku"]);
			}else if($t=="select"){
				$for=[];
				if(isset($_GET["for"])&&in_array($_GET["for"],$for)){
					$this->selectBillsSellPage($_GET["for"]);
				}else{
					$this->selectBillsSellPage();
				}
			}
		}else{
			$this->pageBillPay();
		}
	}
	public function fetch(){
		if(isset($_POST["b"])&&$_POST["b"]=="delete"){
			require_once("php/bill_sell_".$_GET["b"].".php");
			eval("(new bill_sell_".$_GET["b"]."())->run();");
		}else{
			header('Content-type: application/json');
			print_r("{}");
		}
	}
	private function pageView(string $sku):void{
		$se=$this->getBillPayList($sku);
		//print_r($se);
		if(count($se["head"])>0&&count($se["list"])>0){
			$this->addDir("?a=bills&amp;c=pay&amp;b=view&amp;sku=".$se["head"]["sku"],"เลขที่ ".$se["head"]["sku"]);
			$this->pageHead(["title"=>"ใบเสร็จ รับเงิน ชำระค้างจ่าย DIYPOS","xdir"=>true,"css"=>["bill_pay"],"js"=>["pay","Pa"]]);
			$this->writeContentpageView($se["head"],$se["list"]);
			$this->pageFoot();
		}
	}
	private function writeContentpageView(array $head,array $list):void{
		//print_r($head);
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		echo '<div class="content">
			<h2 class="c">ใบเสร็จชำระค้างจ่าย เลขที่ '.$head["sku"].'</h2>
			<div>';
		if($head["onoff"]=="0"){
			
			
/*$reg=new DateTime($head["date_reg"]);
$mo=new DateTime($head["modi_date"]);
echo $mo->format('U')-$reg->format("U");*/

			echo '<div class="error">ใบเสร็จนี้ถูกยกเลิก โดย '.htmlspecialchars($head["user_name_edit"]).'
			หลังจากออกใบเสร็จไป '.$this->ago($head["dif"]).'
			</div>';
		}
		echo '	<div class="l"><b>👫 ผู้รับชำระ</b> : '.htmlspecialchars($head["user_name"]).' ,
				<b>🕒 วันที่</b> : '.$head["date_reg"].' น.
			</div>';

		
		if($head["member_sku"]!=""){
			$mbty="?";
			if(isset($this->mb_type[$head["mb_type"]])){
				$mbty=$this->mb_type[$head["mb_type"]]["icon"]." ".$this->mb_type[$head["mb_type"]]["name"];
			}
			echo '<div class="l"><b>🧾 ผู้ซื้อ</b> : <a href="?a=member&amp;b=details&amp;sku_root='.$head["member_sku_root"].'">'.htmlspecialchars($head["member_name"]).'</a> ,
				<b>ประเภท</b> : '.$mbty.' ,
				<b>รหัส</b> : '.$head["member_sku"];
		}else{
			echo '<div class="l">🧾 ผู้ซื้อ : บุคลทั่วไปไม่ใช่สมาชิก</div>';
		}
		$payu=json_decode($head["payu_json"],true);
		//print_r($payu);
		echo '	</div>
			<table class="billpaylist">
				<tr>
					<th>ที่</th>
					<th>ใบเสร็จ</th>
					<th>ค้างชำระ</th>
					<th>ชำระ</th>
					<th>คงเหลือ</th>
				</tr>';
				for($i=0;$i<count($list);$i++){
					if(1==1){
						$rt="";
						$cm=($i%2!=1)?" class=\"i2\"":"";

						echo '<tr'.$cm.'>
							<td>'.($i+1).'</td>
							<td class="l"><a href="?a=bills&amp;c=sell&amp;b=view&amp;sku='.$list[$i]["bill_sell_sku"].'">'.$list[$i]["bill_sell_sku"].'</a> ['.substr($list[$i]["bill_sell_date_reg"],0,16).']</td>
							<td>'.number_format($list[$i]["credit"],2,".",",").'</td>
							<td>'.number_format($list[$i]["min"],2,".",",").'</td>
							<td>'.number_format($list[$i]["money_balance"],2,".",",").'</td>
						</tr>';
					}
				}
		echo '</table>
				<div>
					<div class="r">📃 จำนวน : <b>'.count($list).'</b> รายการ</div>
					<div class="r">📃 ชำระมา : <b>'.number_format($head["pay"],2,".",",").'</b> บาท</div>
					<div class="r">📃 ยอดค้างชำระคงเหลือ : <b>'.number_format($head["credit"],2,".",",").'</b> บาท</div>
		</div>';
	
		//----------------
		echo '<div class="r"><b>💰 รูปแบบการชำระ</b> : [';
		$e=0;
		foreach($payu as $k=>$v){
			if($v["value"]>0){
				$e+=1;
				$cm=($e>1)?" ,":"";
				echo $cm.'<span>'.htmlspecialchars($v["name"]).' = '.number_format($v["value"],2,".",",").'</span>';
			}
		}		
		echo ']</div>';	
		echo '</div><br />';
		//------------------
		echo '		<br /><img src="?a=bill58&amp;b=viewbillpay&amp;sku='.$head["sku"].'" class="imgbill" alt="ภาพใบเสร็จเลขที่ '.$head["sku"].'"  /><br />
			<a onclick="M.printAgain(\'bill58\',\'print_pay\',\''.$head["sku"].'\')">🖨 พิมพ์อีกครั้ง</a><br /><br />
		</div></div>';		
		//echo $TEST;
		$this->stockCut($head["sku"]);
	}
	private function stockCut(string $sku):void{
		echo '<table><caption>การตัดสินค้า</caption>
			<tr><th>ที่</th><th>สินค้า</th><th>จำนวน</th><th>งวด</th><th>ตัดได้</th><th>ตัดไม่ได้</th></tr>';
		$dt=$this->getBillsSellListCut($sku);	
		//print_r($dt);
		$n=count($dt);
		$s=0;
		$at=1;
		$pd_root_now="";
		for($i=$n-1;$i>=0;$i--){
			$cm=($dt[$i]["sq"]%2!=1)?" class=\"i2\"":"";
			echo '<tr'.$cm.'>';
			if($i<$n&&$dt[$i]["product_sku_root"]!=$pd_root_now&&$i-1>=0){
				if($dt[$i]["product_sku_root"]==$dt[$i-1]["product_sku_root"]){
					$rowspan=$this->findRowSpan($dt,$i);
					$pd_root_now=$dt[$i]["product_sku_root"];
					echo '	<td rowspan="'.$rowspan.'">'.($at++).'</td>
						<td rowspan="'.$rowspan.'">'.$dt[$i]["name"].''.($dt[$i]["s_type"]=="p"?"":" ".($dt[$i]["n_wlv"]*1)." ".$dt[$i]["unit_name"]).'</td>
						<td class="r" rowspan="'.$rowspan.'">'.$dt[$i]["n"].''.($dt[$i]["s_type"]=="p"?"":"×".($dt[$i]["n_wlv"]*1)).'</td>';
				}else{
					$pd_root_now=$dt[$i]["product_sku_root"];
				echo '
				<td>'.($at++).'</td>
				<td>'.$dt[$i]["name"].''.($dt[$i]["s_type"]=="p"?"":" ".($dt[$i]["n_wlv"]*1)." ".$dt[$i]["unit_name"]).'</td>
				<td class="r">'.$dt[$i]["n"].''.($dt[$i]["s_type"]=="p"?"":"×".($dt[$i]["n_wlv"]*1)).'</td>';		
				}
			}else if($dt[$i]["product_sku_root"]!=$pd_root_now){
				$pd_root_now=$dt[$i]["product_sku_root"];
				echo '
				<td>'.($at++).'</td>
				<td>'.$dt[$i]["name"].''.($dt[$i]["s_type"]=="p"?"":" ".($dt[$i]["n_wlv"]*1)." ".$dt[$i]["unit_name"]).'</td>
				<td class="r">'.$dt[$i]["n"].''.($dt[$i]["s_type"]=="p"?"":"×".($dt[$i]["n_wlv"]*1)).'</td>';			
			}
			$tx="";
			$c="in";
			if($dt[$i]["in_type"]=="c"){
				$tx=$this->billNote("c",''.$dt[$i]["bill"],$dt[$i]["note"]);
				$c="sell";
			}else if($dt[$i]["in_type"]=="r"){
				$tx=$this->billNote("r",''.$dt[$i]["bill"],$dt[$i]["note"]);
				$c="ret";
			}else if($dt[$i]["in_type"]=="m"){
				$tx=$this->billNote("m",''.$dt[$i]["sku"],$dt[$i]["note"]);
				$c="move";
			}else if($dt[$i]["in_type"]=="b"){
				$tx=$this->billNote("b",''.$dt[$i]["note"],'');
				$c="in";
			}
			$key=$dt[$i]["sku"];
			if($dt[$i]["in_type"]=="c"){
				$key=$dt[$i]["bill"];
			}
			if($tx!=""){
				$tx='<a href="?a=bills&amp;b=view&amp;c='.$c.'&amp;sku='.$key.'&amp;ed='.$pd_root_now.'">'.$tx.'</a>';
			}else{
				$tx='<!--<a onclick="M.popup(this,\'Bs.selectLotCut(did,\\\''.$pd_root_now.'\\\')\')">-->ไม่พบใบนำเข้า<!--</a>-->';
			}
			
			echo '	<td>'.$tx.'</td>
				<td class="r">'.$dt[$i]["c"].''.($dt[$i]["s_type"]=="p"?"":"×".($dt[$i]["n_wlv"]*1)).'</td>
				<td class="r">'.$dt[$i]["u"].''.($dt[$i]["s_type"]!="p"&&$dt[$i]["u"]>0?"×".($dt[$i]["n_wlv"]*1):"").'</td>
			</tr>';
		}
		echo '</table>';
	}
	private function findRowSpan(array $dt,int $c):int{
		$rsp=1;
		for($i=$c;$i>0;$i--){
			if($dt[$i]["product_sku_root"]==$dt[$i-1]["product_sku_root"]){
				$rsp+=1;
			}else{
				break;
			}
		}
		return $rsp;
	}
	private function getBillsSellListCut($sku):array{
		$sku=$this->getStringSqlSet($sku);
		$re=[];
		$sql=[];
		$sql["cut"]="SELECT bill_sell_list.lot,bill_sell_list.product_sku_root,bill_sell_list.n,bill_sell_list.n_wlv,bill_sell_list.c,bill_sell_list.u,bill_sell_list.sq,
			product_ref.name,product_ref.s_type,
			bill_in.in_type,bill_in.sku,IFNULL(bill_in.note,'') AS `note`,bill_in.bill,unit_ref.name AS unit_name
			FROM bill_sell_list
			LEFT JOIN product_ref
			ON(bill_sell_list.product_sku_key=product_ref.sku_key)
			LEFT JOIN bill_in
			ON(bill_sell_list.lot=bill_in.sku)
			LEFT JOIN unit_ref
			ON(bill_sell_list.unit_sku_key=unit_ref.sku_key)
			WHERE bill_sell_list.sku=".$sku." 
			ORDER BY bill_sell_list.id DESC
		";
		$se=$this->metMnSql($sql,["cut"]);
		//print_r($se);
		if($se["result"]){
			$re=$se["data"]["cut"];
		}
		return $re;
	}
	private function getBillPayList(string $sku):array{
		$re=["head"=>[],"list"=>[]];
		$sku=$this->getStringSqlSet($sku);
		$sql=[];
		$sql["head"]="SELECT  @bill_rca_id:=`bill_rca`.`id`  AS  `id`,`bill_rca`.`sku`  AS  `sku`,
				`bill_rca`.`pay`,`bill_rca`.`credit`,`bill_rca`.`onoff`,
				GetPayuArrRefData_(`bill_rca`.`payu_json`) AS `payu_json`,
				`bill_rca`.`date_reg` AS `date_reg`,
				CONCAT(`user_ref`.`name`,' ', `user_ref`.`lastname`) AS `user_name`,
				`user_ref`.`sku` AS `user_sku`,
				CONCAT(`user_ref2`.`name`,' ', `user_ref2`.`lastname`) AS `user_name_edit`,
				IFNULL(NVL2(`member_ref`.`id`,CONCAT(`member_ref`.`name`,' ', `member_ref`.`lastname`),''),'') AS `member_name`,
				IFNULL(`member_ref`.`mb_type`,'') AS `mb_type`,IFNULL(`member_ref`.`sku`,'') AS `member_sku`,
				IFNULL(`member_ref`.`mb_type`,'') AS `mb_type`,
				`member_ref`.`sku_root` AS `member_sku_root`
			FROM `bill_rca` 
			LEFT JOIN `user_ref`
			ON( `bill_rca`.`user`=`user_ref`.`sku_key`)
			LEFT JOIN `user_ref` AS `user_ref2`
			ON( `bill_rca`.`user_edit`=`user_ref2`.`sku_key`)
			LEFT JOIN `member_ref`
			ON( `bill_rca`.`member_sku_key`=`member_ref`.`sku_key`)
			WHERE `bill_rca`.`sku`=".$sku."";
		$sql["list"]="SELECT  
				bill_rca_list.credit	,bill_rca_list.min	,bill_rca_list.money_balance,
				`bill_sell`.`sku` AS `bill_sell_sku`,
				`bill_sell`.`date_reg` AS `bill_sell_date_reg`
			FROM `bill_rca_list` 
			LEFT JOIN `bill_sell`
			ON(bill_rca_list.bill_sell_id=`bill_sell`.`id`)
			WHERE `bill_rca_id`=@bill_rca_id 
			ORDER BY `bill_rca_list`.`id` ASC";
		$se=$this->metMnSql($sql,["head","list"]);
		//print_r($se);
		if($se["result"]&&isset($se["data"]["head"][0])){
			$re["head"]=$se["data"]["head"][0];
			$re["list"]=$se["data"]["list"];
		}
		return $re;
	}
	private function pageBillPay():void{
		$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["bill_pay"],"js"=>[]]);
		echo '<div class="content">
				<div class="form">
					<h1 class="c">'.$this->title.'</h1>';
			$this->writeContentBillPay();
			echo '</div></div>';
		$this->pageFoot();
	}
	private function writeContentBillPay():void{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$sea=$this->getAllBillPay();
		$se=$sea["row"];
		echo '<form class="form100" name="billsin" method="post">
			<input type="hidden" name="sku" value="" />
			<table class="billpay"><tr><th>ที่</th>
			<th>วันบันทึก</th>			
			<th  class="showhide">เลขที่</th>		
			<th>ชำระเงิน</th>
			<th>ค้างชำระ</th>
		
			<!--<th>กระทำ</th>-->
			</tr>';
		for($i=0;$i<count($se);$i++){
			$ed='';
			if($se[$i]["sku"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}

			$cm=($i%2!=0)?" class=\"i2\"":"";


			$mb=htmlspecialchars($this->setMemberTxt($se[$i]["member_name"],$se[$i]["mb_type"],""));

			echo '<tr'.$cm.'><td>'.$se[$i]["id"].'</td>
				<td>
					<div><a href="?a=bills&amp;c=pay&amp;b=view&amp;sku='.$se[$i]["sku"].'" title="ดูรายละเอียด">'.$se[$i]["sku"].'</a></div>
					<div>'.substr($se[$i]["date_reg"],0,10).'</div>
					<div>'.$mb.'</div>
				</td>
				<td  class="showhide l"><a href="?a=bills&amp;c=pay&amp;b=view&amp;sku='.$se[$i]["sku"].'" title="ดูรายละเอียด">'.$se[$i]["sku"].'</a><div class="r">'.$mb.'</div></td>
				<td class="r">'.number_format($se[$i]["pay"],2,'.',',').'</td>
				<td class="r darkred">'.number_format($se[$i]["credit"],2,'.',',').'</td>

				<!--<td class="action">
					<a data-sku="'.$se[$i]["sku"].'" onclick="Bs.delete(this)" title="ทิ้ง">🗑</a>
					'.$ed.'
					</td>-->
				</tr>';
		}
		echo '</table></form>';
			$count=(isset($sea["count"]))?$sea["count"]:0;
			$this->page($count,$this->per,$this->page,"?a=bills&amp;c=pay&amp;page=");
	}
	private function getAllBillPay():array{
		$re=[];
		$sql=[];
		$sql["count"]="SELECT @count:=bp_c FROM `s` WHERE `tr`=1";
		$sql["get"]="SELECT  `bill_rca`.`id`  AS  `id`,`bill_rca`.`sku`  AS  `sku`,
				`bill_rca`.`pay`,`bill_rca`.`credit`,
				`bill_rca`.`date_reg` AS `date_reg`,
				CONCAT(`user_ref`.`name`,' ', `user_ref`.`lastname`) AS `user_name`,
				IFNULL(NVL2(`member_ref`.`id`,CONCAT(`member_ref`.`name`,' ', `member_ref`.`lastname`),''),'') AS `member_name`,
				IFNULL(`member_ref`.`mb_type`,'') AS `mb_type`
			FROM `bill_rca` 
			LEFT JOIN `user_ref`
			ON( `bill_rca`.`user`=`user_ref`.`sku_key`)
			LEFT JOIN `member_ref`
			ON( `bill_rca`.`member_sku_key`=`member_ref`.`sku_key`)
			ORDER BY `bill_rca`.`id` DESC LIMIT ".(($this->page-1)*$this->per).",".$this->per."";
		$sql["result"]="SELECT @count AS count";
		$se=$this->metMnSql($sql,["get","result"]);
		if($se["result"]){
			$re=["row"=>$se["data"]["get"],"count"=>$se["data"]["result"][0]["count"]];
		}
		//print_r($se);
		return $re;
	}
	private function selectBillsSellPage(string $for=null){
		$sea=$this->getAllBillsSell($for);
		$se=$sea["row"];
		$this->pageHead(["title"=>"ใบเสร็จ DIYPOS","dir"=>false,"css"=>["bill_pay"],"js"=>[]]);
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		//$se=$this->getAllBillsSell();
		echo '<form class="form100" name="billsin" method="post">
			<input type="hidden" name="sku" value="" />
			<table class="billssellsell"><tr><th>ที่</th>
			<th>วันบันทึก</th>			
			<th  class="showhide">เลขที่</th>		
			<th>รายการ</th>
			<th>จำนวนเงิน</th>
			<th>ผู้บันทึก</th>

			<th>กระทำ</th>
			</tr>';
		for($i=0;$i<count($se);$i++){
			$ed='';
			if($se[$i]["sku"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}
			$stat="";
			if($se[$i]["stat"]=="c"){
				$stat=' <span class="red bold">[ถูกยกเลิก]</span>';
			}else if($se[$i]["stat"]=="r"){
				$stat=' <span class="saddlebrown bold"> [มีคืนสินค้า]</span>';
			}
			$mb=htmlspecialchars($this->setMemberTxt($se[$i]["member_name"],$se[$i]["mb_type"],""));
			$cm=($i%2!=0)?" class=\"i2\"":"";
			$s_credit="";
			if($se[$i]["credit"]>0){
				$s_credit=' <span class="s_credit">👎 '.number_format($se[$i]["credit"],2,".",",").'</span>';
			}
			echo '<tr'.$cm.'><td>'.$se[$i]["id"].'</td>
				<td>
					<div><a href="?a=bills&amp;c=sell&amp;b=selectview&amp;sku='.$se[$i]["sku"].'" title="ดูรายละเอียด">'.$se[$i]["sku"].''.$stat.'</a>'.$s_credit.'</div>
					<div>'.$se[$i]["date_reg"].'</div>
					<div>'.$se[$i]["user_name"].'</div>
					<div>'.$mb.'</div>
				</td>
				<td  class="showhide l"><a href="?a=bills&amp;c=sell&amp;b=selectview&amp;sku='.$se[$i]["sku"].'" title="ดูรายละเอียด">'.$se[$i]["sku"].''.$stat.'</a>'.$s_credit.'<div>'.$mb.'</div></td>
				<td class="r">'.$se[$i]["n"].'</td>
				<td class="r">'.number_format($se[$i]["price"],2,'.',',').'</td>
				<td class="l">'.$se[$i]["user_name"].'</td>

				<td class="action c">
					<a data-sku="'.$se[$i]["sku"].'" onclick="Bs.delete(this)" title="ทิ้ง">🗑</a>
					'.$ed.'
					</td>
				</tr>';
		}
		echo '</table></form>';
		$count=(isset($sea["count"]))?$sea["count"]:0;
		$this->page($count,$this->per,$this->page,"?a=bills&amp;b=select&amp;c=sell&amp;page=");
		$this->pageFoot();
	}
	private function setMemberTxt(string $name="",string $type="",string $default=""):string{
		$mb=$default;
		if($name!=""){
			if(isset($this->mb_type[$type])){
				$mb=$this->mb_type[$type]["icon"]." ".$name;
			}
		}
		return $mb;
	}
	private function pageSelectView(string $sku):void{
		$se=$this->getBillsSellList($sku);
		if(count($se["head"])>0&&count($se["list"])>0){
			$this->pageHead(["title"=>"ใบเสร็จ DIYPOS","dir"=>false,"css"=>["bill_pay"]]);
			echo '<div class="billssellback"><a onclick="history.back()">🔙 กลับ</a></div>';
			$this->writeContentpageView($se["head"],$se["list"]);
			$this->pageFoot();
		}
	}
	private function defaultSearch():string{
		$fla=["id","date_start","date_start","username"];
		$fl="name";
		$tx="";
		$se="";
		if(isset($_GET["fl"])){
			if(in_array($_GET["fl"],$fla)){
				$fl=$_GET["fl"];
			}
		}
		if(isset($_GET["tx"])&&strlen(trim($_GET["tx"]))>0){
			$tx=$this->getStringSqlSet($_GET["tx"]);
		}
		if($tx!=""){
			$tx=substr($tx,1,-1);
			$se=" WHERE `product`.`".$fl."` LIKE  \"%".$tx."%\"  ";
		}
		return $se;
	}
}
?>
