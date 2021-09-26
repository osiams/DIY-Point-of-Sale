<?php
class partner_details_product extends main{
	public function __construct(){
		parent::__construct();
		$this->dir=null;
		$this->sku_root=null;
		$this->r_more=null;
		$this->a="partner";
		$this->url="?a=partner&amp;b=details";
	}
	public function run(){
		$this->url.="&amp;sku_root=".$this->sku_root;
		$this->addDir("","สินค้า");
		$this->detailsPage();
	}
	private function detailsPage():void{
		$dt=$this->detailsGetData()["partner"];
		if(count($dt)>0){
			$pn_name=$dt["name"];
			$this->pageHead(["title"=>"คู่ค้า ".htmlspecialchars($pn_name),"css"=>["partner"],"js"=>["partner","Pn"],"run"=>["Pn"],"r_more"=>$this->r_more]);
			echo '<div class="content_rmore">
				<div class="form">
					<h1 class="c">'.$pn_name.'</h1>';
			$this->writeContentProduct();
			$this->pageFoot();
		}else{
			$pn_name="ไม่พบข้อมูลคู่ค้า";
			$this->addDir("",htmlspecialchars($pn_name));
			$this->pageHead(["title"=>"คู่ค้า ".htmlspecialchars($pn_name),"css"=>["partner"],"js"=>["partner","Pn"],"run"=>["Pn"]]);
			echo '<div class="content">
				<div class="form">
					<h1 class="c">'.$pn_name.'</h1>';
			$this->pageFoot();
		}
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
		if($se["result"]&&isset($se["data"]["partner"][0])){
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
