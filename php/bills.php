<?php
class bills extends main{
	public function __construct(){
		parent::__construct();
		$this->setDir();
	}
	public function run(){
		$q=["in","sell","ret","move","mmm","pay","claim"];
		if(isset($_GET["c"])&&in_array($_GET["c"],$q)){
			require_once("php/bills_".$_GET["c"].".php");		
			eval("(new bills_".$_GET["c"]."())->run();");
		}else{
			$this->pageBills();
		}
	}
	private function pageBills():void{
		$this->pageHead(["title"=>"หน่วยสินค้า DIYPOS"]);
		echo '<div class="content">
				<div class="form">
					<h1 class="c">ใบ</h1>';
			echo '<table><tr>
					<td class="l">1.</td>
					<td class="l "><a href="?a=bills&amp;c=in">ใบซื้อสินค้าเข้า</a></td>
				</tr>
				<tr>
					<td class="l">2.</td>
					<td class="l"><a href="?a=bills&amp;c=claim&amp;in_type=cl">ใบเคลมสินค้าออก</a><br /> ส่งเคลม สินค้า ถึง คู่ค้า</td>
				</tr>
				<tr>
					<td class="l">3.</td>
					<td class="l"><a href="?a=bills&amp;c=in&amp;in_type=cl">ใบเคลมสินค้าเข้า</a><br /> รับสินค้า ที่ ได้รับการเคลม จาก คู่ค้า เข้าร้าน </td>
				</tr>
				<tr>
					<td class="l">4.</td>
					<td class="l"><a href="?a=bills&amp;c=sell">ใบเสร็จรับเงิน</a></td>
				</tr>
				<tr>
					<td class="l">5.</td>
					<td class="l"><a href="?a=bills&amp;c=pay">ใบรับชำระค้างจ่าย</a></td>
				</tr>
				<tr>
					<td class="l">6.</td>
					<td class="l"><a href="?a=bills&amp;c=mmm">ใบแตกสินค้า</a></td>
				</tr>
				<tr>
					<td class="l">7.</td>
					<td class="l"><a href="?a=bills&amp;c=ret">ใบคืนสินค้า</a></td>
				</tr>
				<tr>
					<td class="l">8.</td>
					<td class="l"><a href="?a=bills&amp;c=move">ใบย้ายสินค้า</a></td>
				</tr>
			</table>';
			echo '</div></div>';
		$this->pageFoot();
	}
	private function setDir():void{
		$this->addDir("?a=bills","ใบ");
	}
	protected function loadClass(string $file):void{
		require($file);	
	}
}
?>
