<?php
class tool_pdtxt extends tool{
	public function __construct(){
		parent::__construct();
	}
	public function run(){
		$sql=[];
		$sql["pdl"]="SELECT product.name,product.barcode,product.cost,product.price,unit.name as unit_name
			FROM product
			LEFT JOIN unit
			ON product.unit=unit.sku_root
			ORDER BY product.name
		";
		$se=$this->metMnSql($sql,["pdl"]);
		$nl="\n";
		header('Content-Disposition: attachment; filename='.basename('pd.txt'));
		header ('Content-Type: application/octet-stream');
		echo "/* ชื่อสินค้า'รหัสแท่ง'ต้นทุ่น'ราคา'หน่วย (*แบ่งข้อมูลด้วย ' )*/";
		foreach($se["data"]["pdl"] as $k=>$v){
			echo $nl.''.$v["name"].'\''.$v["barcode"].'\''.$v["cost"].'\''.$v["price"].'\''.$v["unit_name"];
		}
	}
}
?>
