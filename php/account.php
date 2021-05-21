<?php
class account extends main{
	public function __construct(){
		parent::__construct();
		$this->title = "บัญชี";
		$this->a = "account";
		$this->addDir("?a=".$this->a,$this->title);
	}
	public function run(){
		$b=["account_rca"];
		if(isset($_GET["b"])&&in_array($_GET["b"],$b)){
			$t=$_GET["b"];
			if($t=="account_rca"){
				//echo "(new ".$t."())->run();";
				require_once("php/".$t.".php");
				eval("(new ".$t."())->run();");
			}
		}else{
			$this->defaultAccountPage();
		}
	}
	public function fetch(){
		$p=["account_rca"];
		if(isset($_POST["b"])&&in_array($_POST["b"],$p)){
			$t=$_POST["b"];
			if($t=="account_rca"){
				require("php/".$t.".php");
				eval("(new ".$t."())->fetch();");
			}
		}else{
			header('Content-type: application/json');
			print_r("{}");
		}		
	}
	private function defaultAccountPage():void{
		$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["account"],"js"=>["account","Ac"],"run"=>["Ac"]]);
		echo '<main>
			
			<div class="content">
			<h1>'.$this->title.'</h1>
				<div class="account_main">
					<div onclick="location.href=\'?a=account&amp;b=account_rca\'">
						<div class="account_icon_rca"></div>
						<div>ลูกหนี้
						<br />ลูกค้า ค้างชำระ
						</div>
					</div>

				</div>
			</div>
		</main>';
		$this->pageFoot();
	}
}
