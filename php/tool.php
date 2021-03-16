<?php
class tool extends main{
	public function __construct(){
		parent::__construct();
		$this->title = "เครื่องมือ";
		$this->a = "tool";
	}
	public function run(){
		$this->addDir("?a=".$this->a,$this->title);
		$this->defaultToolPage();
	}
	private function defaultToolPage():void{
		$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["tool"]]);
		echo '<main>
			
			<div class="content">
			<h1>'.$this->title.'</h1>
				<div class="tool_main">
					<div>
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
