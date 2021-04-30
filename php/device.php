<?php
class device extends main{
	public function __construct(){
		parent::__construct();
		$this->title = "อุปกรณ์";
		$this->a = "device";
		$this->addDir("?a=".$this->a,$this->title);
	}
	public function run(){
		
		$b=["pos","drawers"];
		if(isset($_GET["b"])&&in_array($_GET["b"],$b)){
			$t=$_GET["b"];
			if($t=="pos"){
				require("php/device_".$t.".php");
				eval("(new device_".$t."())->run();");
			}else if($t=="drawers"){
				require("php/device_".$t.".php");
				eval("(new device_".$t."())->run();");
			}
		}else{
			$this->defaultDevicePage();
		}
	}
	public function fetch(){
		$p=["pos","drawers"];
		if(isset($_POST["b"])&&in_array($_POST["b"],$p)){
			$t=$_POST["b"];
			if(1==1){
				require("php/device_".$t.".php");
				eval("(new device_".$t."())->fetch();");
			}
		}else{
			header('Content-type: application/json');
			print_r("{}");
		}		
	}
	private function defaultDevicePage():void{
		$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["device"],"js"=>["device","Dv"],"run"=>["Dv"]]);
		echo '<main>
			
			<div class="content">
			<h1>'.$this->title.'</h1>
				<div class="device_main">
					<div onclick="location.href=\'?a=device&amp;b=pos\'">
						<div class="icon_device_pos"></div>
						<div>เครื่องขายเงินสด
						</div>
					</div>
					<div onclick="location.href=\'?a=device&amp;b=drawers\'">
						<div class="icon_device_drawers"></div>
						<div>ลิ้นชักเก็บเงิน
						</div>
					</div>
				</div>
			</div>
		</main>';
		$this->pageFoot();
	}
	protected function propToFromValue(string $prop):string{
		$t=implode(",,",json_decode($prop));
		$t=(strlen(trim($t))>0)?",".$t.",":"";
		return $t;
	}
}
