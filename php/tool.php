<?php
class tool extends main{
	public function __construct(){
		parent::__construct();
		$this->title = "เครื่องมือ";
		$this->a = "tool";
		$this->about=json_decode(file_get_contents("set/about.json"));
	}
	public function run(){
		$this->addDir("?a=".$this->a,$this->title);
		$b=["tool_pdtxt"];
		if(isset($_GET["b"])&&in_array($_GET["b"],$b)){
			$t=$_GET["b"];
			if($t=="tool_pdtxt"){
				require("php/".$t.".php");
				eval("(new ".$t."())->run();");
			}
		}else{
			$this->defaultToolPage();
		}
	}
	public function fetch(){
		$p=[];
		if(isset($_POST["b"])&&in_array($_POST["b"],$p)){
			$t=$_POST["b"];
			if($t=="tool_pdtxt"){
				require("php/".$t.".php");
				eval("(new ".$t."())->run();");
			}
		}else{
			header('Content-type: application/json');
			print_r("{}");
		}		
	}
	private function defaultToolPage():void{
		$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["tool"],"js"=>["tool","Tl"],"run"=>["Tl"]]);
		echo '<main>
			
			<div class="content">
			<h1>'.$this->title.'</h1>
				<div class="tool_main">
					<div onclick="Tl.resetFactory()">
						<div class="icon_tool_reset_to_factory"></div>
						<div>ตั้งค่าโรงงาน
						<br /><b class="">'.$this->about->name.' Version '.$this->about->version.'</b> ('.$this->about->date.')
						</div>
					</div>
					<div onclick="Tl.pdtxtDownload()">
						<div class="icon_tool_pd_to_data_txt"></div>
						<div>สินค้าแปลงเป็นข้อมูลข้อความ
						</div>
					</div>
				</div>
			</div>
		</main>';
		$this->pageFoot();
	}
}
