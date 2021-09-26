<?php
class cv extends main{
	public function __construct(){
		parent::__construct();
		$this->shop=json_decode(file_get_contents("set/shop.json"));
	}
	public function run(){
		$this->addDir("?a=cv","จอตรวจสอบราคาสินค้า");
		$this->pageHead(["title"=>"จอตรวจสอบราคาสินค้า","manifest"=>"set/manifest_checkprice.json","icon"=>"img/favicon_checkprice.png",
			"titleimg"=>"img/pwa/cd_128.png",
			"js"=>["cv","Cv"],"run"=>["Cv"],"css"=>["cv"]]);
		echo '<div class="content">
			<div class="form">
				<h2 class="c">จอตรวจสอบราคาสินค้า</h2>
				<br />
				<form name="me" method="post" action="" onsubmit="return false;">
					<div class="l" id="device_wh"></div>
					<div class="l">** ซ่อนจอแสดงผลสำหรับลูกค้า ให้กด มุมขวาล่าง ค้างไว้ 3 วินาที</div>
					<input type="button" name="logoubt" onclick="Cv.showDisplay(this)" value="เริ่มหน้าจอ" /> 
				</form>
				
			</div>
		</div>
		<script type="text/javascript">M.id("device_wh").innerHTML="* อุปกรณ์คุณ กว้าง "+window.screen.width+"px ,สูง "+window.screen.height+"px"</script>
		<div id="cv_display">
			<div id="cv_name"></div>
			<div id="cv_img"></div>
			<div id="cv_price"></div>
		</div>	
		<div class="cv_prompt">
		
		</div>
		';
		$this->pageFoot();
	}
}
