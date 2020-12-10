<?php
class bills_mmm extends bills{
	public function __construct(){
		parent::__construct();
		$this->per=20;
		$this->page=1;
	}
	public function run(){
		$this->page=$this->setPageR();
		$q=["view"];
		$this->addDir("?a=bills&amp;c=mmm","ใบแตกสินค้า");
		if(isset($_GET["b"])&&in_array($_GET["b"],$q)){
			$t=$_GET["b"];
			if($t=="view"&&isset($_GET["sku"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["sku"])){
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
			$this->pageBillMmm();
		}
	}
	private function pageView(string $sku):void{
		$se=$this->getBillmmmList($sku);
		//print_r($se);
		if(count($se["list"])>0&&count($se["head"])>0){
			$rt=$se["head"][0]["sku"];
			$this->addDir("?a=bills&amp;c=mmm&amp;b=view&amp;sku=".$sku,"เลขที่ ".$rt);
			$this->pageHead(["title"=>"ใบแตกสินค้า DIYPOS","xdir"=>true,"css"=>["billmmm"]]);
			$this->writeContentpageView($sku,$rt,$se["head"],$se["list"]);
			$this->pageFoot();
		}
	}
	private function writeContentpageView(string $sku,string $rt,array $head,array $list):void{
		$ed=(isset($_GET["ed"]))?$_GET["ed"]:"";
		echo '<div class="content">
			<h2 class="c">ใบแตกสินค้าเลขที่ '.$rt.'</h2>
			<div>';
		echo '	<div class="l">👫 ผู้บันทึก : '.htmlspecialchars($head[0]["user_name"]).'
				🕒วันที่ : '.$head[0]["date_reg"].' น. 🏘️ คลังสินค้า <a href="?a=it&amp;b=view&amp;sku_root='.$list[0]["it_root"].'">'.$list[0]["it_name"].'</a>
			</div>
			
			<table class="billmmmlist">
				<tr>
					<th>ที่</th>
					<th>รหัสแท่ง</th>
					<th>สินค้า</th>
					<th>จำนวน</th>
					<th>หน่วย</th>
					<th>ทุน/น.</th>
					<th>รวม</th>
				</tr>';
				$prices=0;
				for($i=0;$i<count($head);$i++){
					$rt="";
					$cm=($i%2!=0)?" class=\"i2\"":"";
					if($list[$i]["product_sku_root"]==$ed){
						$cm=' class="ed"';
					}
					echo '<tr'.$cm.'>
						<td>'.($i+1).'</td>
						<td>'.$head[$i]["barcode"].'</td>
						<td class="l"><div>'.$head[$i]["pdname"].''.$rt.'</div>
							<div class="l gray555 size12">'.htmlspecialchars($list[$i]["product_barcode"]).'</div>
						</td>
						<td><div class="r">'.$head[$i]["skuroot_n"].'</div>
						<div class="r">'.$head[$i]["unit_name"].'</div>
						</td>
						<td>'.$head[$i]["unit_name"].'</td>
						<td class="r">'.number_format($head[$i]["cost1"],2,'.',',').'</td>
						<td class="r">'.number_format(($head[$i]["cost1"]*$head[$i]["skuroot_n"]),2,'.',',').'</td>
					</tr>';
					$prices+=$list[$i]["product_price"]*$list[$i]["n"];
				}
		echo '</tr></table>
			<h1>⬇️</h1>
			<table class="billmmmlist">
				<tr>
					<th>ที่</th>
					<th>รหัสแท่ง</th>
					<th>สินค้า</th>
					<th>จำนวน</th>
					<th>หน่วย</th>
					<th>ทุน/น.</th>
					<th>รวม</th>
				</tr>';
				$prices=0;
				for($i=0;$i<count($list);$i++){
					$rt="";
					$cm=($i%2!=0)?" class=\"i2\"":"";
					if($list[$i]["product_sku_root"]==$ed){
						$cm=' class="ed"';
					}
					echo '<tr'.$cm.'>
						<td>'.($i+1).'</td>
						<td>'.$list[$i]["product_barcode"].'</td>
						<td class="l"><div>'.$list[$i]["product_name"].''.$rt.'</div>
							<div class="l gray555 size12">'.htmlspecialchars($list[$i]["product_barcode"]).'</div>
						</td>
						<td><div class="r">'.$list[$i]["n"].'</div>
						<div class="r">'.$list[$i]["unit_name"].'</div>
						</td>
						<td>'.$list[$i]["unit_name"].'</td>
						<td class="r">'.number_format($list[$i]["sum"]/$list[$i]["n"],2,'.',',').'</td>
						<td class="r">'.number_format(($list[$i]["sum"]),2,'.',',').'</td>
					</tr>';
					$prices+=$list[$i]["product_price"]*$list[$i]["n"];
				}
		echo '</tr></table>
					<div class="r">📃 จำนวน : <b>'.count($list).'</b> รายการ
					</div><br /><img src="?a=bill58&amp;b=viewmmm&amp;sku='.$sku.'" class="imgbill" alt="ใบคืนสินค้าเลขที่ '.$sku.'" /><br />
					<a onclick="M.printAgain(\'bill58\',\'print_mmm\',\''.$sku.'\')">🖨 พิมพ์อีกครั้ง</a><br />
					<br />
		</div></div>';		
		//$this->stockCut($head["sku"]);
	}
	private function getBillmmmList(string $sku):array{
		$re=["list"=>[],"head"=>[]];
		$sku=$this->getStringSqlSet($sku);
		$sql=[];
		$sql["set"]="SELECT @date_reg:=(SELECT date_reg FROM bill_in WHERE sku=".$sku." ),
			@bill:=(SELECT bill FROM bill_in WHERE sku=".$sku.")
		";
		$sql["head"]="SELECT bill_in.id,bill_in.sku,bill_in.bill,
			mmm.skuroot_n AS skuroot_n,product_ref.barcode AS barcode,
				bill_in.user,bill_in.date_reg,
				bill_in_list.name AS pdname,
				(bill_in_list.sum/bill_in_list.n) AS cost1,
				unit_ref.name AS unit_name,
				CONCAT(`user_ref`.`name`,' ', `user_ref`.`lastname`) AS `user_name`,
				bill_in.n
			FROM bill_in
			LEFT JOIN mmm
			ON(bill_in.id=mmm.bill_in_id)
			LEFT JOIN bill_in_list
			ON(bill_in.lot_root=bill_in_list.bill_in_sku AND bill_in_list.product_sku_root=mmm.skuroot)
			LEFT JOIN product_ref
			ON(mmm.skukey=product_ref.sku_key)
			LEFT JOIN unit_ref
			ON(bill_in_list.unit_sku_key=unit_ref.sku_key)
			LEFT JOIN `user_ref`
			ON( `bill_in`.`user`=`user_ref`.`sku_key`)
			WHERE bill_in.sku=".$sku."
		";
		$sql["list"]="SELECT  `bill_in_list`.`n`  AS  `n`, 
				`bill_in_list`.`product_sku_root` ,
				bill_in_list.sum AS sum,
				product_ref.barcode AS `product_barcode`,product_ref.name AS `product_name`,product_ref.price AS `product_price`,
				unit_ref.name AS `unit_name`,it.name AS it_name,it.sku_root AS it_root
			FROM `bill_in` 
			LEFT JOIN `bill_in_list` 
			ON(bill_in_list.id>=bill_in.r_ AND bill_in_list.id<=bill_in._r And bill_in.sku=bill_in_list.bill_in_sku)
			LEFT JOIN product_ref
			ON(bill_in_list.product_sku_key=product_ref.sku_key)
			LEFT JOIN unit_ref
			ON(bill_in_list.unit_sku_key=unit_ref.sku_key)
			LEFT JOIN it
			ON (bill_in_list.stkey=it.sku_key)
			WHERE bill_in.sku=".$sku."
		";
		$se=$this->metMnSql($sql,["head","list"]);
		//print_r($se);
		if($se["result"]){
			$re["head"]=$se["data"]["head"];
			$re["list"]=$se["data"]["list"];
		}
		return $re;
	}
	private function pageBillMmm():void{
		$this->pageHead(["title"=>"ใบแตกสินค้า DIYPOS","js"=>[],"css"=>["billmmm"]]);
		echo '<div class="content">
				<div class="form">
					<h2 class="c">ใบแตกสินค้า</h2>';
			$this->writeContentBillMmm();
			echo '</div></div>';
		$this->pageFoot();
	}
	private function writeContentBillMmm():void{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$sea=$this->getAllBillMmm();
		$se=$sea["row"];
		echo '<form class="form100" name="billsret" method="post">
			<input type="hidden" name="sku" value="" />
			<table class="billmmm"><tr><th>ที่</th>
			<th>วันบันทึก</th>
			<th>เลขที่</th>				
			<th>สินค้า</th>
			<th>จำนวน</th>
			<th>หน่วย</th>
			<th>ผู้บันทึก</th>
			</tr>';
		for($i=0;$i<count($se);$i++){
			$ed='';
			if($se[$i]["sku"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}
			$cm=($i%2!=0)?" class=\"i2\"":"";
			echo '<tr'.$cm.'><td>'.$se[$i]["id"].'</td>
				<td>'.$se[$i]["date_reg"].'</td>
				<td>
					<div class="l"><a href="?a=bills&amp;c=mmm&amp;b=view&amp;sku='.$se[$i]["sku"].'" title="ดูรายละเอียด">'.$se[$i]["sku"].'</a></div>
					<div>👫 '.htmlspecialchars($se[$i]["user_name"]).'</div>
					<div>'.$se[$i]["date_reg"].'</div>
				</td>
				<td class="l"> '.htmlspecialchars($se[$i]["pdname"]).'</td>
				<td><div> '.$se[$i]["n"].'</div><div>'.htmlspecialchars($se[$i]["unitname"]).'</div></td>
				<td> '.htmlspecialchars($se[$i]["unitname"]).'</td>
				<td class="l">'.htmlspecialchars($se[$i]["user_name"]).'</td>

				</tr>';
		}
		echo '</table></form>';
			$count=(isset($sea["count"]))?$sea["count"]:0;
			$this->page($count,$this->per,$this->page,"?a=bills&amp;c=ret&amp;page=");
	}
	private function getAllBillMmm():array{
		$re=[];
		$sql=[];
		$sql["count"]="SELECT @count:=COUNT(*) FROM bill_in WHERE bill_in.in_type='mm'";
		$sql["get"]="SELECT  `bill_in`.`id`  AS  `id`,`bill_in`.`sku`  AS  `sku`,
				`bill_in`.`date_reg` AS `date_reg`,mmm.skuroot_n AS n,
				`bill_in_list2`.`name` AS pdname,unit_ref.name AS unitname,
				CONCAT(`user_ref`.`name`,' ', `user_ref`.`lastname`) AS `user_name`
			FROM `bill_in` 
			LEFT JOIN mmm
			ON(bill_in.id=mmm.bill_in_id)
			LEFT JOIN `user_ref`
			ON( `bill_in`.`user`=`user_ref`.`sku_key`)
			LEFT JOIN bill_in_list
			ON(bill_in.sku=bill_in_list.bill_in_sku)
			LEFT JOIN bill_in_list AS bill_in_list2
			ON(bill_in.lot_root= bill_in_list2.bill_in_sku AND bill_in_list2.product_sku_root=mmm.skuroot)
			LEFT JOIN unit_ref
			ON(bill_in_list2.unit_sku_key=unit_ref.sku_key)
			WHERE bill_in.in_type='mm'
			ORDER BY `bill_in`.`id` DESC LIMIT ".(($this->page-1)*$this->per).",".$this->per."";
		$sql["result"]="SELECT @count AS count";
		$se=$this->metMnSql($sql,["get","result"]);
		//print_r($se);
		if($se["result"]){
			$re=["row"=>$se["data"]["get"],"count"=>$se["data"]["result"][0]["count"]];
		}
		return $re;
	}
}
?>
