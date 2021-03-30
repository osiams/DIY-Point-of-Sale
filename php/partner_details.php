<?php
class partner_details extends partner{
	public function __construct(string $sku_root){
		parent::__construct();
		$this->sku_root=$sku_root;
	}
	public function run(){
		$this->addDir("?a=partner","คู่ค้า");
		$this->detailsPage();
		
	}
	private function detailsPage():void{
		$dt=$this->detailsGetData()["partner"];
		$pn_name=$dt["name"];
		$this->addDir("",htmlspecialchars($pn_name));
		$this->pageHead(["title"=>"คู่ค้า ".htmlspecialchars($pn_name),"css"=>["partner"],"js"=>["partner","Pn"],"run"=>["Pn"]]);
		echo '<div class="content">
			<div class="form">
				<h1 class="c">'.$pn_name.'</h1>';
		$this->writeContentPartner($dt);		
		echo '<br />';
		$this->writeContentProduct();
		$this->pageFoot();
	}
	private function writeContentPartner(array $dt):void{
		//print_r($dt);
		$s=-1;
		echo '<table class="table_details">
			<tr><th>ค่า</th><th>รายละเอียด</th></tr>';
		if(!empty($dt["icon"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">รูป</td><td class="c"><img src="img/gallery/256x256_'.$dt["icon"].'" class="viewimage"  onclick="G.view(this)"  title="เปิดดูภาพ" /></td></tr>';
		}
		if(!empty($dt["name"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">ชื่อ</td><td class="l">'.htmlspecialchars($dt["name"]).'</td></tr>';
		}
		if(!empty($dt["brand_name"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">ชื่อการค้า</td><td class="l">'.htmlspecialchars($dt["brand_name"]).'</td></tr>';
		}
		if(!empty($dt["sku"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">รหัสภายใน</td><td class="l">'.$dt["sku"].'</td></tr>';
		}
		if(!empty($dt["tax"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">เลขที่ผู้เสียภาษี</td><td class="l">'.$dt["tax"].'</td></tr>';
		}
		if(!empty($dt["pn_type"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">ประเภทคู่ค้า</td><td class="l">'.$this->pn_type[$dt["pn_type"]].'</td></tr>';
		}
		if(!empty($dt["od_type"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">รูปแบบการสั่งซื้อหลัก</td><td class="l">'.$this->od_type[$dt["od_type"]].'</td></tr>';
		}
		if(!empty($dt["tp_type"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">การส่งสินค้า</td><td class="l">'.$this->tp_type[$dt["tp_type"]].'</td></tr>';
		}
		if(!empty($dt["tel"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">โทรศัพท์</td><td class="l">'.$dt["tel"].'</td></tr>';
		}
		if(!empty($dt["fax"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">แฟ็ก์</td><td class="l">'.$dt["fax"].'</td></tr>';
		}
		if(!empty($dt["web"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">เว็บไซต์</td><td class="l">'.$dt["web"].'</td></tr>';
		}
		if(!empty($dt["note"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">รายละเอียดย่อ์</td><td class="l">'.htmlspecialchars($dt["note"]).'</td></tr>';
		}
		$addres="";
		if(!empty($dt["no"])){
			$addres.="เลขที่ ".htmlspecialchars($dt["no"])."<br />";
		}		
		if(!empty($dt["alley"])){
			$addres.="ซอย ".htmlspecialchars($dt["alley"])."<br />";
		}
		if(!empty($dt["road"])){
			$addres.="ถนน ".htmlspecialchars($dt["road"])."<br />";
		}
		if(!empty($dt["distric"])){
			$addres.="แขวง/ตำบล ".htmlspecialchars($dt["distric"])."<br />";
		}
		if(!empty($dt["country"])){
			$addres.="เขต/อำเภอ ".htmlspecialchars($dt["country"])."<br />";
		}
		if(!empty($dt["province"])){
			$addres.="จังหวัด ".htmlspecialchars($dt["province"])."<br />";
		}
		if(!empty($dt["post_no"])){
			$addres.="รหัสไปรษณี ".htmlspecialchars($dt["post_no"])."<br />";
		}
		if(!empty($addres)){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">ที่อยู่</td><td class="l">'.$addres.'</td></tr>';
		}
		echo '</table>';
		
		
	}
	private function writeContentProduct():void{
		$pd=$this->detailsGetProduct();
		echo '<table class="table_details">
			<caption>สินค้า</caption>
			<tr><th>ที่</th><th>ป.</th><th>รหัสแท่ง</th><th>ชื่อ</th></tr>';
		for($i=0;$i<count($pd);$i++){
			$s_type=($pd[$i]["s_type"]!==""&&isset($this->s_type[$pd[$i]["s_type"]]))?$this->s_type[$pd[$i]["s_type"]]["icon"]:"";
			echo '<tr>
				<td>'.($i+1).'</td>
				<td>'.$s_type.'</td>
				<td class="l">'.$pd[$i]["barcode"].'</td>
				<td class="l"><a href="?a=product&amp;b=details&amp;sku_root='.$pd[$i]["sku_root"].'">'.htmlspecialchars($pd[$i]["name"]).'</a></td>
			</tr>';
		}
		echo '</table>';
	}
	private function detailsGetData(){
		$sku_root=$this->getStringSqlSet($this->sku_root);
		$re=["partner"=>[]];
		$sql=[];
		$sql["partner"]="SELECT * FROM `partner`
			WHERE `sku_root`=".$sku_root.";
		";
		$se=$this->metMnSql($sql,["partner"]);
		if($se["result"]){
			$re["partner"]=$se["data"]["partner"][0];
		}
		return $re;
	}
	private function detailsGetProduct(){
		$sku_root=$this->getStringSqlSet($this->sku_root);
		$re=["get"=>[]];
		$sql=[];
		$sql["product"]="SELECT `name`,`barcode`,`sku_root`,IFNULL(`s_type`,'') AS `s_type` FROM `product`
			WHERE JSON_SEARCH(`partner`, 'one', ".$sku_root.") IS NOT NULL;
		";
		$se=$this->metMnSql($sql,["product"]);
		if($se["result"]){
			$re=$se["data"]["product"];
		}
		//print_r($se);
		return $re;
	}
}
