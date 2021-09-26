<?php
class product_details extends product{
	public function __construct(){
		parent::__construct();
		$this->sku_root="";
		$this->ful=null;
	}
	public function run(){
		
		if(isset($_GET["sku_root"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["sku_root"])){
			$file = "php/fileupload.php";
			require($file);	
			$this->ful=new fileupload($this->gallery_dir);
			$this->loadGroupAndProp();
			$this->sku_root=$_GET["sku_root"];
			$dt=$this->getData($_GET["sku_root"]);
			$pd_name="ไม่พบสินค้า";
			if(count($dt["product"])>0){
				$pd_name=htmlspecialchars($dt["product"][0]["name"]);
			}
			$this->addDir("?a=product&amp;b=details&amp;sku_root=".$_GET["sku_root"],"รายละเอียดสินค้า ".$pd_name);
			$this->pageHead(["title"=>"รายละเอียดสินค้า DIYPOS","css"=>["product_details","fileupload"]]);
			if(count($dt["product"])>0){
				$this->details($dt["product"][0]);
				$this->detailsProp($dt["product"][0]["group_root"],json_decode($dt["product"][0]["props"],true));
			}
			$this->stock($dt["stock"]);
			$this->bill($dt["bill"]);
		}
		
		$this->pageFoot();
	}
	private function bill(array $dt):void{
		
		echo '<table class="productdetails bill"><caption>ใบเสร็จ 10 ใบ ล่าสุด</caption>
			<tr><th>ที่</th><th>วันที่</th><th>เลขที่</th><th>จำนวน</th></tr>';
		$n=count($dt);
		$s=0;
		for($i=0;$i<$n;$i++){
			$cm=($s++%2!=0)?" class=\"i2\"":"";
			$stat="";
			if($dt[$i]["stat"]=="c"){
				$stat=' <apan class="red bold">[ถูกยกเลิก]</span>';
			}else if($dt[$i]["stat"]=="r"){
				$stat=' <apan class="darkgoldenrod bold">[มีคืนสินค้า]</span>';
			}
			echo '<tr'.$cm.'>
				<td>'.($i+1).'</td>
				<td>'.$dt[$i]["date_reg"].'</td>
				<td>
					<div><a href="?a=bills&amp;b=view&amp;c=sell&amp;sku='.$dt[$i]["sku"].'&amp;ed='.$this->sku_root.'">'.$dt[$i]["sku"].'</a>'.$stat.'</div>
					<div>'.htmlspecialchars($dt[$i]["user_name"]).'</div>
				</td>
				<td class="r">'.$dt[$i]["n"].'</td>
			</tr>';
		}	
		echo '</table>';
		
	}
	private function stock(array $dt):void{
		//print_r($dt);
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		echo '<table class="productdetails"><caption>สินค้าคงคลัง ทั้งหมด</caption>
			<tr><th>ที่</th><th>งวด</th><th>รับเข้า</th><th>คงเหลือ</th></tr>';
		$n=count($dt);
		$s=0;
		for($i=$n-1;$i>=0;$i--){
			$ed='';
			if($dt[$i]["product_sku_root"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}
			$cost=number_format($dt[$i]["sum"]/($dt[$i]["n"]*$dt[$i]["n_wlv"]==0?1:$dt[$i]["n"]*$dt[$i]["n_wlv"]),2,'.',',');
			$cm=($s++%2!=0)?" class=\"i2\"":"";
			$tx="";
			$c="in";
			if($dt[$i]["in_type"]=="c"){
				$tx=$this->billNote("c",''.$dt[$i]["bill"],'');
				$c="sell";
			}else if($dt[$i]["in_type"]=="r"){
				$tx=$this->billNote("r",''.$dt[$i]["bill"],$dt[$i]["note"]);
				$c="ret";
			}else if($dt[$i]["in_type"]=="m"){
				$tx=$this->billNote("m",''.$dt[$i]["sku"],$dt[$i]["note"]);
				$c="move";
			}else if($dt[$i]["in_type"]=="mm"){
				$tx=$this->billNote("mm",''.$dt[$i]["note"],'');
				$c="mmm";
			}else if($dt[$i]["in_type"]=="b"){
				$tx=$this->billNote("b",''.$dt[$i]["partner_name"],$dt[$i]["bill_no"]);
				$c="in";
			}else if($dt[$i]["in_type"]=="x"){
				$tx=$this->billNote("x",''.$dt[$i]["note"],'');
				$c="move";
			}
			$key=$dt[$i]["sku"];
			if($dt[$i]["in_type"]=="c"){
				$key=$dt[$i]["bill"];
			}
			echo '<tr'.$cm.'>
				<td>'.($n-$i).'</td>
				<td>
					<div><a href="?a=bills&amp;b=view&amp;c='.$c.'&amp;sku='.$key.'&amp;ed='.$dt[$i]["product_sku_root"].'">'.$tx.'</a></div>
					<div>'.$dt[$i]["date_reg"].'</div>
					<div class="r"><span>ทุน '.$cost.'/น. </span>  <span>🏘️ <a href="?a=it&amp;b=view&amp;sku_root='.$dt[$i]["stroot"].'&c=lot&pd='.$dt[$i]["product_sku_root"].'&amp;ed='.$key.'">'.htmlspecialchars($dt[$i]["it_name"]).'</a></div>
				</td>
				<td class="r">'.($dt[$i]["n"]).''.($dt[$i]["s_type"]=="p"?"":"×".$dt[$i]["n_wlv"]*1).'</td>
				<td class="r">'.($dt[$i]["balance"]*1).'</td>
			</tr>';
		}
		echo '</table>';
	}
	private function detailsProp(string $group_root,array $props):void{
		echo '<main><table class="productdetails"><caption>คุณสมบัติ</caption>
			<tr><th>รายละเอียด</th><th>ค่า</th></tr>';
		$group_props = $this->group_list[$group_root]["prop"];
		foreach($props as $k=>$v){
			if(in_array($k,$group_props)){
				echo '<tr><td>'.$this->prop_list[$k]["name"].'</td><td>'.$v.'</td></tr>';
			}
		}	
		echo '</table></main>';
	}
	private function details(array $dt):void{
		echo '<main><table class="productdetails"><caption>สินค้า</caption>';
		echo '<tr><td colspan="2">';
		echo '<div class="c">'.$this->sIcon($dt["icon"],256).'</div>';;
		$this->ful->listImg($dt["sku_root"]);
		echo '</td></tr>';
		echo '	<tr><th>ค่า</th><th>รายละเอียด</th></tr>
			<tr><td>ที่</td><td>'.$dt["id"].'</td></tr>
			<tr class="i2"><td>ชื่อ</td><td>'.$dt["name"].'</td></tr>
			<tr><td>รหัสภายใน</td><td>'.$dt["sku"].'</td></tr>
			<tr class="i2"><td>รหัสแท่ง</td><td>'.$dt["barcode"].'</td></tr>
			<tr><td>รูปรหัสแท่ง</td><td><img src="?a=bill58&amp;b=barcodeimg&amp;barcode='.$dt["barcode"].'&amp;type=1&amp;br=2" alt="'.$dt["barcode"].'" /></td></tr>
			<tr class="i2"><td>รหัสอ้างอิง</td><td>'.$dt["sku_key"].'</td></tr>
			<tr><td>รหัสราก</td><td>'.$dt["sku_root"].'</td></tr>
			<tr class="i2"><td>ต้นทุน</td><td>'.$dt["cost"].'</td></tr>
			<tr><td>ราคา</td><td>'.$dt["price"].'</td></tr>
			<tr class="i2"><td>หน่วย</td><td>'.$dt["unit_name"].'</td></tr>';
		if($dt["pdstat"]=="b"){
			echo '<t><td>สถานะสินค้า </td><td>⬛ บัญชีดำ<br /><span class="red">'.$dt["statnote"].'</span></td></tr>';
		}else if($dt["pdstat"]=="r"){
			echo '<tr><td>สถานะสินค้า </td><td>🟥  พักขาย,หยุดขาย<br /><span class="red">'.$dt["statnote"].'</span></td></tr>';
		}else if($dt["pdstat"]=="y"){
			echo '<t><td>สถานะสินค้า </td><td>🟨 ต้องตรวจสอบเป็นพิเศษ<br /><span class="red">'.$dt["statnote"].'</span></td></tr>';
		}else{
			echo '<tr><td>สถานะสินค้า </td><td>สินค้าปกติ</td></tr>';
		}
		$pn_arr=json_decode($dt["partner"],true);
		echo '	<tr class="i2"><td>วันที่ลงทะเบียน</td><td>'.$dt["date_reg"].'</td></tr>
			<tr class="i1"><td>กลุ่ม</td><td>'.$this->writeDirGroup($dt["group_root"],[$dt["d1"],$dt["d2"],$dt["d3"],$dt["d4"]]).'</td></tr>
			<tr class="i2"><td>คู่ค้า</td><td class="l">';
			if(is_array($pn_arr)){
				$this->writePartnerList($pn_arr);
			}
		echo '</td></tr>
			<tr><td>ภาษีมูลค่าเพิ่ม %</td><td>'.number_format($dt["vat_p"],2,'.',',').'</td></tr>
		
			</table></main>';
	}
	private function writePartnerList(array $pn):void{
		$i=0;
		echo '<table class="partner_list">';
		foreach($pn as $k=>$v){
			$i=$i+1;
			echo '<tr>
				<td class="l">'.$i.'.</td>
				<td class="partner_list_img"><div class="img32"><img src="img/gallery/32x32_'.$v["icon"].'" onerror="this.src=\'img/pos/64x64_null.png\'" /></div></td>
				<td class="l"><a href="?a=partner&b=details&sku_root='.$k.'">'.htmlspecialchars($v["name"]).'</a></td>
			</tr>';
		}
		echo '</table>';
	}
	private function getData(string $sku_root):array{
		$re=["product"=>[],"stock"=>[]];
		$sku_root=$this->getStringSqlSet($sku_root);
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='';
		";
		$sql["product"]="SELECT product.id AS id,product.sku AS sku,product.sku_key AS sku_key,product.sku_root AS sku_root,
				product.barcode AS barcode,product.name AS name,
				product.cost AS cost,product.price AS price,IFNULL(product.vat_p,0) AS vat_p,product.unit AS unit,product.pdstat,product.statnote,
				IFNULL(product.icon,'') AS icon,product.date_reg AS date_reg,
				IFNULL(`product`.`group_root`,\"defaultroot\") AS `group_root`,IFNULL(`product`.`props`,\"[]\") AS props,
				GetListPartner_(IFNULL(`partner`,'[]')) AS `partner`,
				unit.name AS unit_name,
				`group`.`d1` AS `d1`,`group`.`d2` AS `d2`,`group`.`d3` AS `d3`,`group`.`d4` AS `d4`
			FROM `product` 
			LEFT JOIN(unit)
			ON(product.unit=unit.sku_root)
			LEFT JOIN (`group`) 
			ON (`product`.`group_root` = `group`.`sku_root`) 
			WHERE product.sku_root=".$sku_root." ORDER BY id DESC ";
		$sql["stock"]="SELECT bill_in_list.stroot,bill_in_list.s_type,
				IFNULL(bill_in_list.n,1) AS `n` ,
				IFNULL(bill_in_list.n_wlv,1) AS `n_wlv` ,
				IFNULL(IF(bill_in_list.s_type='p',bill_in_list.balance,bill_in_list.balance_wlv),0) AS `balance`,
				bill_in_list.sum,bill_in_list.product_sku_root,
				bill_in.bill_no,bill_in.in_type,bill_in.bill,IFNULL(bill_in.note,'') AS `note`,
				bill_in.sku,bill_in.date_reg,
				partner_ref.name AS partner_name,partner_ref.sku_root AS partner_sku_root,
				it_ref.name  AS `it_name`
			FROM bill_in_list
			LEFT JOIN bill_in
			ON( bill_in_list.bill_in_sku=bill_in.sku)
			LEFT JOIN partner_ref
			ON( bill_in.pn_key=partner_ref.sku_key)
			LEFT JOIN it_ref
			ON(bill_in_list.stkey=it_ref.sku_key)
			WHERE  IF(bill_in_list.s_type='p',bill_in_list.balance,bill_in_list.balance_wlv)>0 AND bill_in_list.product_sku_root=".$sku_root." ORDER BY bill_in_list.sq DESC ;
		";
		$sql["setpdroot"]="SET @h=CAST(".$sku_root." AS CHAR CHARACTER SET  ascii);";
		$sql["bill"]="SELECT  bill_sell.id, bill_sell.stat,bill_sell.sku,bill_sell.user,bill_sell.r_,bill_sell.date_reg,
			bill_sell_list.n,CONCAT(user_ref.name,' ',user_ref.lastname) AS user_name
			FROM  bill_sell
            LEFT JOIN bill_sell_list
			ON (bill_sell.sku=bill_sell_list.sku  )
			LEFT JOIN user_ref
			ON (bill_sell.user=user_ref.sku_key)
			WHERE bill_sell_list.product_sku_root=@h
			GROUP BY bill_sell.sku
			ORDER BY bill_sell.id DESC LIMIT 10
		";
		$se=$this->metMnSql($sql,["product","stock","bill"]);
		if(isset($se["data"]["product"])&&count($se["data"]["product"])>0){
			$re["product"]=$se["data"]["product"];
			$re["stock"]=$se["data"]["stock"];
			$re["bill"]=$se["data"]["bill"];
		}else{
			$re["product"]=[];
			$re["stock"]=[];
			$re["bill"]=[];
		}
		return $re;
	}
}
