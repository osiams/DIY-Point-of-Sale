<?php
class bill_in_class extends main{
	public function __construct(){
		$this->in_type="b";
		$this->ita=[
			"b"=>["qurey"=>"?a=bills&amp;c=in","text"=>"ใบซื้อสินค้าเข้า"],
			"cl"=>["qurey"=>"?a=bills&amp;c=in&amp;in_type=cl","text"=>"ใบเคลมสินค้าเข้า"],
		];
	}
	public function writeJsDataEdit(array $dt,bool $editable=true,string $partner=null,string $id=null,string $product_list_id=null):void{
		$tid=($id!=null)?"\"".$id."\"":"null";
		$pid=($product_list_id!=null)?"\"".$product_list_id."\"":"null";
		$br="\n";
		$edb=($editable)?"true":"false";
		//print_r($dt);
		echo '<script type="text/javascript">Bi.insertData([';
		for($i=0;$i<count($dt);$i++){
			$name=$this->jsD((string) $dt[$i]["name"]);
			$unit=$this->jsD((string) $dt[$i]["unit_name"]);
			$n=($dt[$i]["s_type"]=="p")?intval($dt[$i]["n"]):floatval($dt[$i]["n"]);
			$balance=($dt[$i]["s_type"]=="p")?intval($dt[$i]["balance"]):floatval($dt[$i]["balance"]);
			echo  $br.'{"name":"'.$name.'","n":"'.$n.'","balance":"'.$balance.'","sum":"'.number_format($dt[$i]["sum"],2,".","").'","bcsku":"'.$dt[$i]["barcode"].' , '.$dt[$i]["product_sku"].'","sku_root":"'.$dt[$i]["sku_root"].'","unit":"'.$unit.'","s_type":"'.$dt[$i]["s_type"].'","price":"'.$dt[$i]["price"].'","cost":"'.$dt[$i]["cost"].'","vat_p":"'.number_format($dt[$i]["vat_p"],2,".","").'"},';
		}
		echo '],'.$edb.','.$tid.','.$pid.');';
		if($partner!=null){
			echo 'Bi.partner="'.$partner.'";';
		}
		echo 'Fsl.selectPartnerListValue("product","'.$id.'","'.$product_list_id.'");';
		echo '</script>';
	}	
	protected function jsD(string  $t):string{
		$t=str_replace('\\','\\\\',$t);
		$t=str_replace('"','\"',$t);
		$t=str_replace("\n","",$t);
		return $t;
	}
	public function writeJsDataEditPrompt(array $dt,bool $editable=true,string $partner=null,string $id=null,string $product_list_id=null):void{
		$tid=($id!=null)?"\"".$id."\"":"null";
		$pid=($product_list_id!=null)?"\"".$product_list_id."\"":"null";
		$br="\n";
		$edb=($editable)?"true":"false";
		echo '<script type="text/javascript">Bi.insertDataPrompt([';
		for($i=0;$i<count($dt);$i++){
			$name=$this->jsD((string) $dt[$i]["name"]);
			$unit=$this->jsD((string) $dt[$i]["unit_name"]);
			echo  $br.'{"name":"'.$name.'","n":"'.intval($dt[$i]["n"]).'","balance":"'.intval($dt[$i]["balance"]).'","sum":"'.number_format($dt[$i]["sum"],2,".","").'","bcsku":"'.$dt[$i]["barcode"].' , '.$dt[$i]["product_sku"].'","sku_root":"'.$dt[$i]["sku_root"].'","unit":"'.$unit.'","s_type":"'.$dt[$i]["s_type"].'","price":"'.$dt[$i]["price"].'","cost":"'.$dt[$i]["cost"].'","vat_p":"'.number_format($dt[$i]["vat_p"],2,".","").'"},';
		}
		echo '],'.$edb.','.$tid.','.$pid.');';
		if($partner!=null){
			echo 'Bi.partner="'.$partner.'";';
		}
		echo 'Fsl.selectPartnerListValue("product","'.$id.'","'.$product_list_id.'");';
		echo '</script>';
	}
	public function billsinCheck(string $type="insert"):array{
		$re=["result"=>false,"message_error"=>""];
		$se=$this->checkSet("bill_in",["post"=>["bill_no","bill_type"]],"post");
		if(!isset($_POST["product"])){
			$re["message_error"]="ไม่มีสินค้าที่เลือก";
		}else if(empty(trim($_POST["bill_no"]))){
			$re["message_error"]="เลขที่ใบเสร็จต้องไม่ว่าง";
		}else if(gettype(json_decode($_POST["product"],true))!="array"){
			$re["message_error"]="สินค้าที่เลือกหรือข้อมูลที่ส่งมาไม่อยู่ในรูปแบบ";
		}else if(strlen($_POST["note"])>$this->fills["note"]["length_value"]-3){
			$re["message_error"]="หมายเหตุ ยาวเกินไป";
		}else if(isset($_POST["pn"])&&!preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_POST["pn"])){
			$re["message_error"]="ใบนำเข้าหรือ คู่ค้าไม่ถูกต้อง";
		}else if(isset($_POST["bill_date"])&&!preg_match("/^([1-9])[0-9]{3}-(0|1)[0-9]-(0|1|2|3)[0-9]$/",$_POST["bill_date"])){
			$re["message_error"]="วันที่ ไม่อยู่ในรูปแบบ  yyy-mm-dd";
		}else if(!$se["result"]){
			$re["message_error"]=$se["message_error"];
		}else if(!isset($_POST["sku"])&&$type=="edit"){
			$re["message_error"]="ไม่พบใบนำเข้าที่ส่งมา";
		}else if(isset($_POST["sku"])&&strlen(trim($_POST["sku"]))==0&&$type=="edit"){
			$re["message_error"]="รหัสชี่ใบนำเข้าว่าง";
		}else{
			$re["result"]=true;
		}
		return $re;
	}
	public function setInType():void{
		if(isset($_GET["in_type"])){
			$a=$_GET["in_type"];
			if($a=="cl"){
				$this->in_type="cl";
			}
		}
	}
}
?>
