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
		echo ''.$this->os("group",'<div  class="icon icon_group" onclick="location.href=\'?a=group\'">กลุ่มสินค้า</div>').'';
		echo ''.$this->os("prop",'<div  class="icon icon_prop" onclick="location.href=\'?a=prop\'">คุณสมบัติ</div>').'';
		echo ''.$this->os("user",'<div  class="icon icon_user" onclick="location.href=\'?a=user\'">ผู้ใช้</div>').'';
		echo ''.$this->os("member",'<div  class="icon icon_member" onclick="location.href=\'?a=member\'">สมาชิก</div>').'';
		echo ''.$this->os("partner",'<div  class="icon icon_partner" onclick="location.href=\'?a=partner\'">คู่ค้า</div>').'';
		echo ''.$this->os("bills",'<div  class="icon icon_bills" onclick="location.href=\'?a=bills\'">ใบ</div>').'';
		echo ''.$this->os("ret",'<div  class="icon icon_ret" onclick="location.href=\'?a=ret\'">คืนสินค้า</div>').'';
		echo ''.$this->os("it",'<div  class="icon icon_it" onclick="location.href=\'?a=it\'">คลังสินค้า</div>').'';
		echo ''.$this->os("day",'<div  class="icon icon_day" onclick="location.href=\'?a=day\'">สรุป ประจำวัน</div>').'';
		echo ''.$this->os("barcode",'<div  class="icon icon_barcode" onclick="location.href=\'?a=barcode\'">รหัสแท่ง</div>').'';
		echo ''.$this->os("payu",'<div  class="icon icon_payu" onclick="location.href=\'?a=payu\'">รูปแบบการชำระ</div>').'';
		echo ''.$this->os("account",'<div  class="icon icon_account" onclick="location.href=\'?a=account\'">บัญชี</div>').'';
		echo ''.$this->os("device",'<div  class="icon icon_device" onclick="location.href=\'?a=device\'">อุปกรณ์</div>').'';
		echo ''.$this->os("time",'<div  class="icon icon_time" onclick="location.href=\'?a=time&amp;b=time_all\'">กะ</div>').'';
		echo ''.$this->os("tool",'<div  class="icon icon_tool" onclick="location.href=\'?a=tool\'">เครื่องมือ</div>').'';
		echo ''.$this->os("setting",'<div  class="icon icon_setting" onclick="location.href=\'?a=setting\'">ตั้งค่า</div>').'';
		echo ''.$this->os("cd",'<div  class="icon icon_cd" onclick="location.href=\'?a=cd\'">จอแสดงผล สำหรับลูกค้า</div>').'';
		echo ''.$this->os("cv",'<div  class="icon icon_cv" onclick="location.href=\'?a=cv\'">จอตรวจสอบราคาสินค้า</div>').'';
		echo '<script type="text/javascript">M.b.classList.add(\'bghome\');M.b.classList.add(\'sd\');</script>';
		echo '</main>';
	}
}
?>
