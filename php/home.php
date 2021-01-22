<?php
class home extends main{
	public function __construct(){
		parent::__construct();
		$this->home=1;
	}
	public function run(){
		$this->pageHead(["title"=>"หน้าหลัก DIYPOS","css"=>["home"]]);
		$this->pageHome();
		$this->pageFoot();
	}
	protected function pageHome(){
		$this->avatarWrite("home");
		echo '<main>';
		echo ''.$this->os("sell",'<div  class="icon icon_sell" onclick="location.href=\'?a=sell\'">หน้าจอขาย</div>').'';
		echo ''.$this->os("product",'<div  class="icon icon_in" onclick="location.href=\'?a=bills&b=fill&c=in\'">นำเข้าสินค้า</div>').'';
		echo ''.$this->os("product",'<div  class="icon icon_product" onclick="location.href=\'?a=product\'">สินค้า</div>').'';
		echo ''.$this->os("unit",'<div  class="icon icon_unit" onclick="location.href=\'?a=unit\'">หน่วยสินค้า</div>').'';
		echo ''.$this->os("user",'<div  class="icon icon_user" onclick="location.href=\'?a=user\'">ผู้ใช้</div>').'';
		echo ''.$this->os("bills",'<div  class="icon icon_bills" onclick="location.href=\'?a=bills\'">ใบ</div>').'';
		echo ''.$this->os("ret",'<div  class="icon icon_ret" onclick="location.href=\'?a=ret\'">คืนสินค้า</div>').'';
		echo ''.$this->os("it",'<div  class="icon icon_it" onclick="location.href=\'?a=it\'">คลังสินค้า</div>').'';
		echo ''.$this->os("day",'<div  class="icon icon_day" onclick="location.href=\'?a=day\'">สรุป ประจำวัน</div>').'';
		echo ''.$this->os("barcode",'<div  class="icon icon_barcode" onclick="location.href=\'?a=barcode\'">รหัสแท่ง</div>').'';
		echo ''.$this->os("setting",'<div  class="icon icon_setting" onclick="location.href=\'?a=setting\'">ตั้งค่า</div>').'';
		echo ''.$this->os("cd",'<div  class="icon icon_cd" onclick="location.href=\'?a=cd\'">จอแสดงผล สำหรับลูกค้า</div>').'';
		echo '<script type="text/javascript">M.b.classList.add(\'bghome\');M.b.classList.add(\'sd\');</script>';
		echo '</main>';
	}
}
?>
