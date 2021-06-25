<?php
class time_view_bill_sell_all extends main{
	public function __construct(){
		parent::__construct();
		$this->title = "ใบเสร็จรับเงิน (ขายสินค้า)";
		$this->a = "time";
		$this->c = 0;
		$this->r_more=[];
	}
	public function run(){
		$dt=$this->getBillSellAll();
		$this->addDir("",$this->title);
		$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["time"],"js"=>["time","Ti"],"run"=>["Ti"],"r_more"=>$this->r_more]);
		$this->pageFoot();
	}
	private function getBillSellAll():array{
		$re=["get"=>[],"message_error"=>""];
		$sql=[];
		$sql["set"]="SELECT 
			@id:=".$this->c.";
		";
		$sql["get"]="
			SELECT `bill_sell`.`sku`		,`bill_sell`.`price`		,`bill_sell`.`payu_json`,
					`bill_sell`.`date_reg`
				FROM `bill_sell`
				WHERE `bill_sell`.`time_id`=@id
				ORDER BY `bill_sell`.`id` DESC;
		";
		$se=$this->metMnSql($sql,["get"]);
		if($se["result"]){
			if(isset($se["data"]["get"])){
				if(isset($se["data"]["time"][0])){
					$re["get"]=$se["data"]["get"][0];
				}
			}
		}
		print_r($se);
		return $re;
	}
}
