<?php
require_once("../php/main.php");
require_once("../config.php");	
class tool extends main{
	public function __construct(){
		parent::__construct();
	}
	public function creatDataProductTxt(){
		$sql=[];
		$sql["pdl"]="SELECT product.name,product.barcode,product.cost,product.price,unit.name as unit_name
			FROM product
			LEFT JOIN unit
			ON product.unit=unit.sku_root
			ORDER BY product.name
		";
		$se=$this->metMnSql($sql,["pdl"]);
		$nl="\n";
		header('Content-Type: application/txt');
		echo "/* ชื่อสินค้า'รหัสแท่ง'ต้นทุ่น'ราคา'หน่วย (*แบ่งข้อมูลด้วย ' )*/";
		foreach($se["data"]["pdl"] as $k=>$v){
			echo $nl.''.$v["name"].'\''.$v["barcode"].'\''.$v["cost"].'\''.$v["price"].'\''.$v["unit_name"];
		}
	}
}
$tool=new tool();
$tool->creatDataProductTxt();
?>
