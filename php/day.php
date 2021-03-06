<?php
class day extends main{
	public function __construct(){
		parent::__construct();
	}
	public function run(){
		$day=date("Y-m-d");
		if(isset($_GET["date"])&&preg_match("/^(20)[0-9]{2}-(01|02|03|04|05|06|07|08|09|10|11|12)-(0|1|2|3)[0-9]{1}$/",$_GET["date"])){
			$day=$_GET["date"];
		}
		$this->pageMain($day);
	}
	private function pageMain(string $day):void{
		$this->addDir("?a=day","สรุปประจำวัน");
		$this->pageHead(["title"=>"สรุปประจำวัน DIYPOS","css"=>["day"],"js"=>["day","D"]]);
		$this->writeContent($day);
		$this->pageFoot();
	}
	private function writeContent(string $day):void{
		$dt=$this->getData($day);
		//print_r($dt);
		echo '<div class="content">
			<h2 class="c">สรุปประจำวัน </h2>
			<div class="day_select_day">
				<label for="daydate">เลือกวันที่:</label>
				<input id="daydate" type="date" value="'.$day.'" />
				<input type="button" value="ไป"  onclick="D.go()"/>
			</div>
			';
		echo '<table class="day_bill">
			 <caption>ใบเสร็จรับเงิน</caption>
			<tr><th>รายการ</th><th>ค่า</th><th>หน่วย</th></tr>
			<tr><td class="l">จำนวน ที่ออก</td><td class="r">'.$dt["count"][0]["countt"].'</td><td class="l">ใบ</td></tr>
			<tr class="i2"><td class="l">จำนวน ที่ถูกยกเลิก</td><td class="r">'.$dt["count_c"][0]["count_c"].'</td><td class="l">ใบ</td></tr>
			<tr><td class="l">จำนวนใบเสร็จที่มี คืนสินค้า่</td><td class="r">'.$dt["count_r"][0]["count_r"].'</td><td class="l">ใบ</td></tr>
			<tr class="i2">
				<td class="l">ยอดขายรวม <br /><span class="size12 blue">ยังไม่หัก(ยกเลิก+คืนสินค้า)</span></td>
				<td class="r">'.number_format($dt["sum_price_profit"][0]["sum_price"],2,'.',',').'</td>
				<td class="l">บ.</td></tr>
			<tr>
			<tr>
				<td class="l">ยอดขาย รวม<br /><span class="size12 darkgreen">่หักยกเลิก+หักคืนสินค้า</span></td>
				<td class="r">'.number_format($dt["sum_price_profit_real"][0]["prices"],2,'.',',').'</td>
				<td class="l">บ.</td>
			</tr>
			<tr class="i2">
				<td class="l">กำไรรวม<br /><span class="size12 blue">ยังไม่หัก(ยกเลิก+คืนสินค้า)</span></td>
				<td class="r">'.number_format($dt["sum_price_profit"][0]["profit"],2,'.',',').'</td>
				<td class="l">บ.</td>
			</tr>
			<tr>
				<td class="l">กำไรรวม<br /><span class="size12 darkgreen">่หักยกเลิก+หักคืนสินค้า</span></td>
				<td class="r">'.number_format($dt["sum_price_profit_real"][0]["pf"],2,'.',',').'</td>
				<td class="l">บ.</td>
			</tr>
		</table>';
		$this->writePdO($dt["pdo"]);
		echo '</div>';
	}
	private function writePdO(array $dt):void{
		echo '<table class="day_pdo">
			 <caption>สินค้าที่ขายได้ทังหมด</caption>
			<tr>
			<th>ที่</th>
			<th>สินค้า</th>
			<th>จำนวน</th>
			<th>กำไร</th>
		</tr>';
		$pf=0;
		for($i=0;$i<count($dt);$i++){
			$cm=($i%2!=0)?" class=\"i2\"":"";
			$pf+=$dt[$i]["profit"];
			echo '<tr'.$cm.'>
				<td>'.($i+1).'</td>
				<td>'.htmlspecialchars($dt[$i]["product_name"]).' <span class="blue">฿'.number_format($dt[$i]["product_price"],2,'.',',').'/'.htmlspecialchars($dt[$i]["unit_name"]).'</span>
				<p><span class="pwlv">'.$this->s_type[$dt[$i]["s_type"]]["icon"].'</span>
				 '.$dt[$i]["barcode"].'</p>
				</td>
				<td>'.($dt[$i]["n"]*1).'
					<p>'.htmlspecialchars($dt[$i]["unit_name"]).'</p></td>
				<td>'.number_format($dt[$i]["profit"],2,'.',',').'</td>
			</tr>';
		}
		echo '<tr><td  colspan="4"><p class="r">กำไรรวม <b class="darkgreen">'.number_format($pf,2,'.',',').'</b> บ.</p></td></tr>';
		echo '</table>';
	}
	private function getData(string $day):array{
		$tr=str_replace("-","",$day);
		$sql=[];
		$sql["set"]="SET @date=CAST('".$day."' AS CHAR CHARACTER SET ascii)";
		$sql["t2_1"]="SELECT CURTIME(6);";
		$sql["setrr"]="SELECT @r_:=bsr_,@_r:=bs_r FROM s  WHERE tr=".$tr."  LIMIT 1;";
		$sql["set2"]="SET @_r=IF(@_r IS NULL,@r_+1,@_r);";
		$sql["count"]="SELECT COUNT(*) AS `countt` FROM bill_sell WHERE id>=@r_ AND id<=@_r;";
		$sql["sum_price_profit"]="SELECT 
			SUM(price) AS `sum_price` ,
			SUM(price-cost) AS `profit`
			FROM bill_sell  WHERE id>=@r_ AND id<=@_r;";
		$sql["count_r"]="SELECT COUNT(*)  AS `count_r`
			FROM bill_sell 
			WHERE id>=@r_ AND id<=@_r AND stat='r'";
		$sql["count_c"]="SELECT COUNT(*)  AS `count_c`
			FROM bill_sell 
			WHERE id>=@r_ AND id<=@_r AND stat='c'";
		$sql["sum_price_profit_real"]="SELECT SUM(price-pricer) AS `prices`,SUM(price-pricer-(cost-costr)) AS `pf` FROM bill_sell WHERE id>=@r_ AND id<=@_r AND IFNULL(stat,'')!='c';";
		$sql["pdo"]="SELECT product_ref.barcode,product_ref.name AS `product_name`, product_ref.price AS `product_price`,`product_ref`.`s_type`,
			unit_ref.name AS `unit_name`,
			SUM(		(IF(bill_sell_list.c>0,bill_sell_list.c,bill_sell_list.u)-bill_sell_list.r)*bill_sell_list.n_wlv) AS `n`,
			SUM(
				(
					(
						product_ref.price*(	
							IF(
								bill_sell_list.c>0,bill_sell_list.c,0
							)
							-bill_sell_list.r
						)
					)
					-IFNULL(
						(
							bill_in_list.sum/IF(
								bill_in_list.s_type='p',bill_in_list.n,bill_in_list.n_wlv
							)
						),
						product_ref.cost)
					*(	
						IF(
							bill_sell_list.c>0,bill_sell_list.c,0
						)
						-bill_sell_list.r	
					)
				)
				*bill_sell_list.n_wlv
			)
			 AS `profit`,
			bill_sell.stat
			FROM bill_sell
			LEFT JOIN bill_sell_list
			ON(bill_sell.sku=bill_sell_list.sku)
			LEFT JOIN product_ref
			ON(bill_sell_list.product_sku_key=product_ref.sku_key)
			LEFT JOIN unit_ref
			ON(bill_sell_list.unit_sku_key=unit_ref.sku_key)
			LEFT JOIN bill_in_list
			ON(bill_sell_list.bill_in_list_id=bill_in_list.id)
			WHERE bill_sell.id>=@r_ AND bill_sell.id<=@_r  AND IFNULL(bill_sell.stat,'') != 'c'
			GROUP BY bill_sell_list.product_sku_root 
			ORDER BY n DESC ,profit DESC
		";
		$se=$this->metMnSql($sql,["count","sum_price_profit","count_r","count_c","sum_price_profit_real","pdo"]);
		//print_r($se);
		return $se["data"];
	}
}
?>
