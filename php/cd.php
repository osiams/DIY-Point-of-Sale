<?php
class cd extends main{
	public function __construct(){
		parent::__construct();
		$this->filevdopath = "vdo/cd/";
		$this->shop=json_decode(file_get_contents("set/shop.json"));
	}
	public function run(){
		$this->addDir("?a=cd","จอแสดงผล สำหรับลูกค้า");
		$this->pageHead(["title"=>"จอแสดงผล สำหรับลูกค้า","manifest"=>"set/manifest_customer.json","icon"=>"img/favicon_customer_display.png",
			"titleimg"=>"img/pwa/cd_128.png",
			"js"=>["ws","Ws","cd","Cd"],"run"=>["Ws","Cd"],"css"=>["cd"]]);
		echo '<script type="text/javascript">'.$this->writeJsFileList().'</script>';
		echo '<div class="content">
			<div class="form">
				<h2 class="c">จอแสดงผล สำหรับลูกค้า</h2>
				<br />
				<form name="me" method="post" action="" onsubmit="return false;">
					<div class="div_ws_stat l">Websocket : <span id="bt_cd_stat_ws"></span></div>
					<p><label for="me_name">IP ของเครื่องขาย</label></p>
					<div><input id="sd_ip" class="want" type="text" value="" autocomplete="on" /></div>
					<br />
					<div class="l" id="device_wh"></div>
					<div class="l">** ซ่อนจอแสดงผลสำหรับลูกค้า ให้กด มุมขวาล่าง ค้างไว้ 3 วินาที</div>
					<input type="button" name="logoubt" onclick="Cd.showDisplay()" value="รับข้อมูล" /> 
				</form>
				
			</div>
		</div>
		<script type="text/javascript">Ws.statSet("bt_cd_stat_ws");M.id("device_wh").innerHTML="* อุปกรณ์คุณ กว้าง "+window.screen.width+"px ,สูง "+window.screen.height+"px"</script>
		<div id="cd_display">
			<div id="cdlogousercd">
				<div id="cdlogo"><div>'.htmlspecialchars($this->shop->name).'</div></div>
				<div id="cdusercd">
					<div id="cduser"></div>
					<div id="cdcd"></div>
				</div>
			</div>
			<div id="divvdo_out">
				<div id="divvdo"><video id="cdvideo"></video></div>
				<div id="cdlistsell">
					<div id="cdlistitem"></div>
					<div id="cdpricetotal">
						<div id="cdprice"><div><span id="cd_nlist"></span> รายการ , <span id="cd_nunit"></span> ชิ้น</div></div>
						<div id="cdtotal"><div>รวมเงิน <span id="cd_sum"></span><xsup><xsup><span class="idotf">.</span><span id="cd_sum_float"></span></xsup></xsup> บาท</div></div>
					</div>
				</div>
			</div>
			<div id="cdstat">สถานะ</div>
		</div>
		<div id="cd_empty"><video id="cd_video_empty"></video></div>
		<div id="thankyou">
			
			<div>
				<div>ขอบคุณ คุณลูกค้า</div>
				<div>โปรดรับใบเสร็จ<span class="red">*</span></div>
				<div>และ</div>
				<div>เงินทอน <span id="cd_change">915.00</span> บาท</div>
				<div><span class="red">*</span>ไว้เป็นหลักฐานในกรณีสินค้ามีปัญหา<div id="countdownclose">0</div></div>
			</div>
		</div>
		<div id="div_soundthank">
				<audio id="soundthank" controls>
				<source src="sound/cd/cd_thank.mp3" type="audio/ogg">
			</audio> 
		</div>		
		';
		$this->pageFoot();
	}
	private function getListFile():array{
		$re=[];
		$filelist = glob($this->filevdopath."*.mp4");
		foreach($filelist as $filename){
			array_push($re, basename($filename)); 
		}
		return $re;
	}
	private function writeJsFileList():string{
		$list = $this->getListFile();
		return 'let list_file='.json_encode($list);
	}
}
