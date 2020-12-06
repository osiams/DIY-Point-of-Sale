<?php  /*--apt DIY_POS;--ext php;--version 0.0;*/
class setting extends main{
	public function __construct(){
		parent::__construct();
		$this->file="config.php";
		$this->receipt=json_decode(file_get_contents("set/receipt.json"));
		$this->printer=json_decode(file_get_contents("set/printer.json"));
		$this->shop=json_decode(file_get_contents("set/shop.json"));
	}
	public function run(){
		$q=["edit"];
		//$this->addDir("?a=setting","ตั้งค่า");
		if(isset($_GET["b"])&&in_array($_GET["b"],$q)){
			$t=$_GET["b"];
			if($t=="edit"){
				$this->editSetting();
			}
		}else{
			$this->editSetting();
		}
	}
	private function editSettingUpdate():array{
		$re=["result"=>null];
		$set=$_POST["set"];
		if($this->bak()){
			$se=file_put_contents($this->file, $set, LOCK_EX);
			require_once("config.php");
			$re["result"]=$se;
		}
		return $re;
	}
	private function bak():bool{
		$re=true;
		if(!defined("ERROR")){
			$file="config.php";
			$newfile="config.bak.php";
			if (!copy($file, $newfile)) {
				$re=false;
			}
		}
		return $re;
	}
	private function editSetting():void{
		$error="";
		if(isset($_POST["submith"])&&$_POST["submith"]=="clicksubmit"&&isset($_POST["ps"])){
			$qe=$this->editSettingUpdate();
			$result=gettype($qe["result"]);
			if($result!="integer"){
				$error="เกิดข้อผิดพลาดบางอย่างในการเขียนไฟล์";
			}else{
				header('Location:index.php');
			}
			if($error!=""){
				$file=$this->editSettingSetCurent();
				$this->editSettingPage($file,$error);
			}
		}else{
			$file=$this->editSettingSetCurent();
			$this->editSettingPage($file,$error);
		}
	}
	private function editSettingSetCurent():string{
		$od=$this->editSettingOldData();
		return $od;
	}
	private function editSettingOldData():string{
		$file=file_get_contents($this->file);
		return $file;
	}
	private function editSettingPage(string $file,string $error):void{
		$this->addDir("","ตั้งค่า");
		$this->pageHead(["title"=>"ตั้งค่า DIYPOS","css"=>["setting"],"js"=>["setting","Stt"]]);
		if(defined("ERROR")){
			 echo '<div class="error">เกิดข้อผิดพลาด 
				ไฟล์:'.ERROR["file"].'
				ความผิดพลาด:'.ERROR["message"].'
				บรรทัดที่:'.ERROR["line"].'
				**ระบบกำลังใช้ไฟล์ '.dirname(__DIR__).'/config.bak.php นี้ทำงานอยู่
			</div>';
		 }
		echo '<div class="content">
			<div class="form100">
				<h2 class="c">ตั้งค่า</h2>';
		if($error!=""){
			echo '<div class="error">'.$error.'</div>';
		}		
		
		$lan=$_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']."".$_SERVER['PHP_SELF'];
		$lan2="http://".$this->findIPv4().":".CF["http_port"]."".$_SERVER['PHP_SELF'];
		$lan3="https://".$this->findIPv4().":".CF["https_port"]."".$_SERVER['PHP_SELF'];
		/*echo '		<form class="form100" name="setting" method="post">
					<input type="hidden" name="submith" value="clicksubmit" />
					<input type="hidden" name="ps" value="" />
					<p><label for="setting_set">ไฟล์ config.php</label></p>
					<div class="setting"><textarea id="setting_set" name="set">'.$file.'</textarea></div>
					<br />
					<input type="button" name="ok" onclick="G.settingSubmit()" value="แก้ไข" /> 
					<input type="button" name="newinstall" onclick="Stt.newInstall(\''.str_replace("\\","\\\\",dirname(dirname(dirname(__FILE__)))).'\',\''.basename(dirname(__DIR__)).'\')" value="อยากลบทิ้งทั้งหมด แล้วติดตั้งใหม่" />
				</form>
			</div>
			<br />*/
	echo '	<table>
				<caption>เครื่องแม่ขาย</caption>
				<tr><th>รายละเอียด</th><th>ค่า</th></tr>
				<tr><td class="l">ที่อยู่แฟ้ม</td><td class="l">'.dirname(dirname(__FILE__)).'</td></tr>
				<tr><td class="l">ชื่อครื่อ</td><td class="l">'.$_SERVER['SERVER_NAME'].'</td></tr>
				<tr><td class="l">ที่อยู่ครื่อง</td><td class="l">'.$_SERVER['SERVER_ADDR'].'</td></tr>
				<tr><td class="l">ที่อยู่ครื่อง IPv4</td><td class="l">'.$this->findIPv4().'</td></tr>
				<tr><td class="l">พอร์ต</td><td class="l">'.$_SERVER['SERVER_PORT'].'</td></tr>
				<tr><td class="l">ที่อยู่สำหรับเรียกใช้งาน </td><td class="l">'.$lan.'<br /><img src="?a=qrc&amp;s=qrq&d='.$lan.'" />
				<hr />
				'.$lan2.'<br /><img src="?a=qrc&amp;s=qrq&d='.$lan2.'" />
				<hr />
				'.$lan3.'<br /><img src="?a=qrc&amp;s=qrq&d='.$lan3.'" />
				</td></tr>
				<tr>
					<td class="l">ฟ้อนต์สำหรับพิมพ์_0</td>
					<td class="l">ที่อยู่ = '.$this->printer->font_0.' <br />
						ขนาด = '.$this->printer->font_0_size.' 
					</td>
				</tr>
				<tr>
					<td class="l">ฟ้อนต์สำหรับพิมพ์_1</td>
					<td class="l">ที่อยู่ = '.$this->printer->font_1.' <br />
						ขนาด = '.$this->printer->font_1_size.' 
					</td>
				</tr>
				<tr><td class="l">เครื่องพิมพ์ ปริยาย</td><td class="l">ชื่อ = '.htmlspecialchars($this->printer->printer_0->name).'
				<br />,ที่อยู่ = '.htmlspecialchars($this->printer->printer_0->address).' 
				<br />,ขนาดความกว้าง = '.htmlspecialchars($this->printer->printer_0->width).' มล. 
				<br />,ตัดกระดาษอัตโนมัติ = '.htmlspecialchars($this->printer->printer_0->cut).' 
				<br />,เปิดลิ้นชัก = '.htmlspecialchars($this->printer->printer_0->pulse).'
				<br />,บรรทัดว่างชดเชยท้ายใบเสร็จ = '.$this->printer->printer_0->feed.'
				<br /><a onclick="Stt.printTest(0)">ทดสอบพิมพ์</a>
				</td></tr>	
			</table>
			<br />
			<table>
				<caption>เครื่องผู้ใช้</caption>
				<tr><th>รายละเอียด</th><th>ค่า</th></tr>
				<tr><td class="l">ซอฟต์แวร์</td><td class="l">'.$_SERVER['HTTP_USER_AGENT'].'</td></tr>
				<tr><td class="l">IPv4 ของ ผู้ใช้</td><td class="l">'.$this->userIPv4().'</td></tr>
				<tr><td class="l">เครื่องพิมพ์ที่ใช้<br />(ที่เครื่องแม่ข่าย)</td><td class="l"><div id="userprinter" onclick="M.popup(this,\'Stt.changePt(did)\')"></div>
					<a onclick="Stt.printTest()">ทดสอบพิมพ์</a>
				</td></tr>	
			</table>
			<br />
			<table>
				<caption>ร้านค้า</caption>
				<tr><th>รายละเอียด</th><th>ค่า</th></tr>
				<tr><td class="l">ชื่อ</td><td class="l">'.htmlspecialchars($this->shop->name).'</td></tr>
				<tr><td class="l">รายละเอียด</td><td class="l">'.htmlspecialchars($this->shop->head).'</td></tr>
			</table>
			<br />
			<table>
				<caption>ใบเสร็จรับเงินอย่างย่อ</caption>
				<tr><th>รายละเอียด</th><th>ค่า</th></tr>
				<tr><td class="l">แสดงโลโก้</td><td class="l">'.$this->receipt->receipt58->sale->logo.'</td></tr>
				<tr><td class="l">ส่วนหัวใบเสร็จ</td><td class="l">'.htmlspecialchars($this->receipt->receipt58->sale->head).'</td></tr>
				<tr><td class="l">ชื่อใบเสร็จ</td><td class="l">'.htmlspecialchars($this->receipt->receipt58->sale->name).'</td></tr>
				<tr><td class="l">ข้อความท้ายใบเสร็จ</td><td class="l">'.htmlspecialchars($this->receipt->receipt58->sale->foot).'</td></tr>
			</table>
			</div>
			<script type="text/javascript">let printerdata='.json_encode($this->printer).'
			Stt.setPt( printerdata,\'userprinter\')
			</script>
			</div>';
			
		$this->pageFoot();
	}
	private function findIPv4():string{
		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		socket_connect($sock, "8.8.8.8", 53);
		socket_getsockname($sock, $name);
		return $name;
	}
	private function userIPv4():string{
		$re=$_SERVER['REMOTE_ADDR'];
		if($re==$_SERVER["SERVER_ADDR"]){
			$re=$this->findIPv4();
		}
		return $re;
	}
}
?>
