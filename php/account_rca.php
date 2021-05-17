<?php
class account_rca extends account{
	public function __construct(){
		parent::__construct();
		$this->get_rca=[];
	}
	public function run(){
		
		$this->defaultAccountRcaPage();
	}
	protected function defaultAccountRcaPage(){
		$tl="ลูกหนี้ ค้างชำระ";
		$this->addDir("",$tl);
		$this->pageHead(["title"=>$tl." DIYPOS","css"=>["account"],"js"=>["account","Ac"],"run"=>["Ac"]]);
		echo '<div class="content">
			<h2>'.$tl.'</h2>';
		$this->writeRca();
		echo '</div>';
		$this->pageFoot();
	}
	private function writeRca():void{
		$this->getRca();
		print_r($this->get_rca);
	}
	private function getRca(){
		$sql=[];
		$sql["get_rca"]="SELECT `member`.`sku`	,`member`.`name`	,`member`.`lastname`,
				`member`.`credit`
			FROM `member` 
			WHERE `member`.`credit`> 0;
		";
		$se=$this->metMnSql($sql,["get_rca"]);
		print_r($se);
		if(isset($se["data"]["get_rca"])){
			$this->get_rca=$se["data"]["get_rca"];
		}
	}
}
?>
