<?php
function loadMike42($class){
	$file = "library/escpos-php-development/src/".str_replace("\\","/",$class).".php";
	if(file_exists ($file)){
		require($file);
	}	
}
spl_autoload_register ("loadMike42");
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\GdEscposImage;
class bill58 extends main{
	private $bill_width=(384*3)/2;
	private $bill_height=35;
	private $font_file;
	private $font_file2;
	private $font_size=18;
	private $font_size2=17;
	private $white;
	private $black;
	private $cnt="usb";
	private $pip=["ip"=>null,"port"=>null];
	public $connector=null;
	protected $printer=null;
	protected $printer_label=null;
	public function __construct(){
		parent::__construct();
		$this->receipt=json_decode(file_get_contents("set/receipt.json"));
		$this->pt=json_decode(file_get_contents("set/printer.json"));
		$this->setPrinterNo($this->pt);
		$this->font_file=$this->pt->font_0;
		$this->font_file2=$this->pt->font_1;
		$this->font_size=$this->pt->font_0_size;
		$this->font_size2=$this->pt->font_1_size;
		$this->usb=$this->printer->address;
		$this->setCNT($this->usb);
		$this->bc=null;
		/*$p=getcwd();
		if(preg_match("/\\?/",$p)){
			$this->font_file=$p."\data\font\TlwgTypewriter-Bold.ttf";
			$this->font_file2=$p."\data\font\Laksaman-Bold.ttf";
		}else{
			$this->font_file=$p."/data/font/TlwgTypewriter-Bold.ttf";
			$this->font_file2=$p."/data/font/Laksaman-Bold.ttf";
		}*/
	}
	protected function setPrinterNo(object $pt):void{
		if(isset($_COOKIE["printer_"])&&preg_match("/^[0-9]{1,10000}$/",$_COOKIE["printer_"])
			&&isset($pt->{"printer_".$_COOKIE["printer_"]})&&$pt->{"printer_".$_COOKIE["printer_"]}->status==1){
			$this->printer=$pt->{"printer_".$_COOKIE["printer_"]};
			if(isset($_POST["no"])&&$_POST["no"]=="0"){
				$this->printer=$pt->printer_0;
			}
		}else{
			$this->printer=$pt->printer_0;
		}
		if(isset($_COOKIE["printer_label_"])&&preg_match("/^[0-9]{1,10000}$/",$_COOKIE["printer_label_"])
			&&isset($pt->{"printer_".$_COOKIE["printer_label_"]})&&$pt->{"printer_".$_COOKIE["printer_label_"]}->status==1){
			$this->printer_label=$pt->{"printer_".$_COOKIE["printer_label_"]};
		}else{
			$this->printer_label=$pt->printer_0;
		}
	}
	public function run(){
		if(isset($_GET["b"])){
			if($_GET["b"]=="viewbill"){
				$this->viewBill($_GET["sku"]);
			}else if($_GET["b"]=="viewret"&&isset($_GET["sku"])
				&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["sku"])){
				$this->viewRet($_GET["sku"]);
			}else if($_GET["b"]=="viewmove"&&isset($_GET["sku"])
				&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["sku"])){
				$this->viewMove($_GET["sku"]);
			}else if($_GET["b"]=="viewmmm"&&isset($_GET["sku"])
				&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["sku"])){
				require_once("php/class/barcode.php");
				$this->bc=new barcode($this->font_file2);
				$this->viewmmm($_GET["sku"]);
			}else if($_GET["b"]=="viewbillpay"&&isset($_GET["sku"])
				&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["sku"])){
				$this->viewPay($_GET["sku"]);
			}else if($_GET["b"]=="labelimg"&&isset($_GET["sku_root"])&&$this->isSKU($_GET["sku_root"])){
				require_once("php/class/barcode.php");
				$this->bc=new barcode($this->font_file2);
				$this->labelImg($_GET["sku_root"]);
			}else if($_GET["b"]=="barcodeimg"||$_GET["b"]=="barcodelabel"){
				require_once("php/class/barcode.php");
				$type=(isset($_GET["type"])&&$_GET["type"]=="ean13")?"ean13":"itf";
				$barcode=(isset($_GET["barcode"])&&is_numeric($_GET["barcode"]))?$_GET["barcode"]:"00";
				$type=(strlen($barcode)==13)?"ean13":"itf";
				$height=(isset($_GET["height"])&&is_numeric($_GET["height"]))?(int) $_GET["height"]:40;
				$width=(isset($_GET["width"])&&is_numeric($_GET["width"]))?(int) $_GET["width"]:40;
				$br=(isset($_GET["br"])&&is_numeric($_GET["br"]))?(int) $_GET["br"]:2;
				$this->bc=new barcode($this->font_file2);
				if($_GET["b"]=="barcodeimg"){
					$this->barcodeImg($type,$barcode,$width,40,$br,10,10);
				}else if($_GET["b"]=="barcodelabel"){
					$this->barcodeImg($type,$barcode,$width,$height,$br,0,0);
				}
			}
		}
	}
	protected function setCNT(string $uri):void{
		if($uri===null){$uri="";}
		if(preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}:[0-9]{1,5}$/",$uri)){
			$this->cnt="network";
			$ui=explode(":",$uri);
			$this->pip["ip"]=$ui[0];
			$this->pip["port"]=$ui[1];
		}else{
			$this->cnt="usb";
			$this->pip=["ip"=>null,"port"=>null];
		}
	}
	protected function checkNP(string $typecheck):bool{
		$re=false;
		if($typecheck=="exists"){
			if($this->cnt=="network"||($this->cnt=="usb"&&file_exists($this->usb))){
				$re=true;
			}
		}else if($typecheck=="writable"){
			if($this->cnt=="network"||(is_writable($this->usb)&&$this->cnt=="usb")){
				$re=true;
			}
		}
		return $re;
	}
	protected function fnConect():object{	
		if($this->cnt=="network"){
			return (new NetworkPrintConnector($this->pip["ip"],$this->pip["port"]));
		}else{
			return (new FilePrintConnector($this->usb));
		}
	}
	protected function printLogo(object $printer):void{
		if($this->receipt->receipt58->sale->logo==1){
			$logo=EscposImage::load("img/logo.png");
			$printer ->initialize();
			$printer ->setPrintLeftMargin(0);
						//printer ->bitImage($logo);
			$printer ->bitImageColumnFormat($logo);
			$printer ->initialize();
		}
	}
	protected function printCut(object $printer):void{
		if($this->printer->cut==1){
			$printer -> cut();
		}
	}
	protected function pulse(object $printer):void{
		if($this->printer->pulse==1){
			$printer ->pulse();
		}else if($this->printer->_ucdp==1&&$this->printer->_ucda!=""){
			$handleq = fopen($this->printer->_ucda, "w"); 
			fwrite($handleq, chr(27).chr(112).chr(0).chr(100).chr(250));
			fclose($handleq); 
		}
	}
	public function fetch(){
		if(isset($_POST["b"])&&$_POST["b"]=="print"
			&&isset($_POST["submith"])&&$_POST["submith"]=="clicksubmit"
			&&(isset($_POST["sku"])&&preg_match("/^[0-9]{1,25}$/",$_POST["sku"]))){
			$this->print($_POST["sku"]);
			$re=["result"=>true,"message_error"=>""];	
			//header('Content-type: application/json');
		}else if(isset($_POST["b"])&&$_POST["b"]=="print_ret"
			&&(isset($_POST["sku"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_POST["sku"]))){
			$this->retPrint($_POST["sku"]);
		}else if(isset($_POST["b"])&&$_POST["b"]=="print_pay"
			&&(isset($_POST["sku"])&&preg_match("/^[0-9]{1,25}$/",$_POST["sku"]))){
			$this->payPrint($_POST["sku"]);
		}else if(isset($_POST["b"])&&$_POST["b"]=="print_mmm"
			&&(isset($_POST["sku"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_POST["sku"]))){
			require_once("php/class/barcode.php");
			$this->bc=new barcode($this->font_file);
			$this->mmmPrint($_POST["sku"]);
		}else if(isset($_POST["b"])&&$_POST["b"]=="print_move"
			&&(isset($_POST["sku"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_POST["sku"]))){
			$this->movePrint($_POST["sku"]);
		}else if(isset($_POST["b"])&&($_POST["b"]=="labelPrint"||$_POST["b"]=="labelWLVPrint")
			&&isset($_POST["sku_root"])&&$this->isSKU($_POST["sku_root"])){
			require_once("php/class/barcode.php");
			$this->bc=new barcode($this->font_file2);
			if($_POST["b"]=="labelPrint"){
				$this->labelPrint($_POST["sku_root"]);
			}else{
				$this->labelWLVPrint($_POST["sku_root"]);
			}
		}else if(isset($_POST["b"])&&$_POST["b"]=="textPrint"){
			$this->textPrint($_POST["text"]);
		}else if(isset($_POST["b"])&&$_POST["b"]=="print_test"){
			require_once("php/class/barcode.php");
			$this->bc=new barcode($this->font_file2);
			$this->testPrint();
		}else{
			header('Content-type: application/json');
			print_r("{}");
		}
	}
	protected function testPrint():void{
		$re=["result"=>false,"message_error"=>""];
		try {
			if(isset($_POST["for"])){
				if($_POST["for"]=="userprinterlabel"){
					$this->printer=$this->printer_label;
					$this->usb=$this->printer_label->address;
					$this->setCNT($this->usb);									
				}else if($_POST["for"]==null){
					$this->printer=$this->pt->{"printer_0"};
					$this->usb=$this->printer->address;
					$this->setCNT($this->usb);
				}
			}
			if($this->checkNP("exists")){
				if($this->checkNP("writable")){
					if(file_exists($this->font_file)){
						if(file_exists($this->font_file2)){
							$printer = new Printer($this->fnConect());	
							$foo0 = new GdEscposImage();
							$imr0=$this->createImgTest(0);
							$foo0 -> readImageFromGdResource($imr0);
							$printer->bitImageColumnFormat($foo0);
							$foo1 = new GdEscposImage();
							$imr1=$this->createImgTest(1);
							$foo1 -> readImageFromGdResource($imr1);
							$printer->bitImageColumnFormat($foo1);
							$this->pulse($printer);		
							$this->printCut($printer);
							$printer -> close();
							$re["result"]=true;
						}else{
							throw new Exception("ไม่พบที่อยู่ฟ้อนค์ \n".$this->font_file2." \nโปรดตรวจสอบ\nการตั้งค่าเครื่องพิมพ์ \${PATH}/set/printer.json ค่า font_1 \n*** \${PATH} = ".dirname(__DIR__)."");
						}	
					}else{
						throw new Exception("ไม่พบที่อยู่ฟ้อนค์ \n".$this->font_file." \nโปรดตรวจสอบ\nการตั้งค่าเครื่องพิมพ์  ที่ \${PATH}/set/printer.json ค่า font_0 \n*** \${PATH} = ".dirname(__DIR__)."");
					}	
				}else{
					throw new Exception("ไมสามารถเขียนไฟล์ได้ \n".$this->usb." \nโปรดตรวจสอบ\nการตั้งค่าผู้ใช้ในกลุ่มเครื่องพิมพ์");
				}
			}else{
				throw new Exception("ไม่พบที่อยู่ปลายทาง \n".$this->usb." \nโปรดตรวจสอบการเชื่อมต่ออุปกรณ์ \nหรือการตั้งค่าที่อยู่ของเครื่องพิมพ์");
			}
		}catch (Exception $e) {
			$re["message_error"]=$e->getMessage();
		}
		header('Content-type: application/json');
		echo json_encode($re);
	}
	protected function createImgTest(int $font_n){
		$fontfile=$this->font_file;
		$fontsize=$this->font_size;
		$zx=($font_n==0)?1.5:1;
		if($zx==1){
			$this->bill_width=384;
			$fontfile=$this->font_file2;
			$fontsize=$this->font_size2;
		}
		$tm=[];
		$tm["tyy"]=[["t"=>"--------------------------------------------------------","lcr"=>"c"]];
		$hn=$this->bc->cutWord2(0,$fontsize,$fontfile,"ฟ้อนต์_".$font_n." = ".$fontfile,384*$zx);
		for($i=0;$i<count($hn);$i++){
			$tm["font_".$i]=[["t"=>$hn[$i],"lcr"=>"l"]];
		}
		$tm["font_s"]=[["t"=>"ฟ้อนต์_".$font_n."_size = ".$fontsize,"lcr"=>"l"]];
		$tm["texts0"]=[["t"=>"ยินดีต้อนรับสู่  DIY Point of sale","lcr"=>"c"]];
		$tm["list_0"]=[
					["t"=>202,"lcr"=>"l"],
					["t"=>"ถุงผ้า 200x240 ซม.","lcr"=>"l"],
					["t"=>"฿25.00 ","lcr"=>"r"],
					["t"=>"300.00","lcr"=>"r"]
			];
		$tm["list_1"]=[
					["t"=>1,"lcr"=>"l"],
					["t"=>"สินค้าตัวอย่าง 150มล.","lcr"=>"l"],
					["t"=>"฿125.00","lcr"=>"r"],
					["t"=>"150.00","lcr"=>"r"]
			];
		$tm["list_2"]=[
					["t"=>2,"lcr"=>"l"],
					["t"=>"โปรแกรม ขายหน้าร้าน รุ่นฟรีๆๆๆๆ","lcr"=>"l"],
					["t"=>"฿1,005.25","lcr"=>"r"],
					["t"=>"2,010.50","lcr"=>"r"]
			];
			for($i=0;$i<$this->printer->feed;$i++){
				$tm["line_feed_".$i]=[["t"=>" ","lcr"=>"l"]];
			}
		
		$xy=imagettfbbox($fontsize, 0, $fontfile,"กูกี่"); //$v."กูกี่"
		$ht=$xy[1]-$xy[7];
		$this->bill_height=$ht*count($tm);
		
		$xy=imagettfbbox($fontsize, 0, $fontfile,"                 ");
		$ww=$xy[2]-$xy[0];
		$ws=$this->bill_width-($xy[2]-$xy[0]);
		
		$im=imagecreatetruecolor($this->bill_width,$this->bill_height);
		$this->white=imagecolorallocate($im, 255, 255, 255);
		$this->black=imagecolorallocate($im, 0, 0, 0);		
		imagefilledrectangle($im, 0, 0,$this->bill_width, $this->bill_height, $this->white);
		$top=0;
		foreach($tm as $k=>$v){
			$j=0;
			foreach($tm[$k] as $l=>$m){
				$lcr=$m["lcr"];
				$j+=1;
				$vu=$m["t"];

				$xy=imagettfbbox($fontsize, 0, $fontfile,$vu);
				$h=$xy[1]-$xy[7];
				$h=($h<$ht)?$ht:$h;
				$h+=2;
				$w=$xy[2]-$xy[0];
				$top=($j==1)?$top+$h:$top;
				$left=0;
				if($lcr=="c"){
					$left=($this->bill_width-$w)/2;
				}else if($lcr=="r"){
					$left=($this->bill_width-$w-2);
				}	
				if(substr($k,0,5)=="list_"){
					if($j==2){
						$left=10.69*3*$zx;
					}else if($j==3){
						$left=$left-(10.69*9*$zx);
						imagefilledrectangle($im, $left, $top-$ht,$this->bill_width, $top+10, $this->white);
					}					
				}

				imagettftext($im,$fontsize,0,$left, $top, $this->black, $fontfile,$vu);
			}
		}
		$imr = imagescale ($im,(1/$zx)*$this->bill_width,$this->bill_height,IMG_BILINEAR_FIXED);
		return $imr;
	}
	protected function print(string $billid){
		$re=["result"=>false,"message_error"=>""];
		try {
			if($this->checkNP("exists")){
				if($this->checkNP("writable")){
					$logo=EscposImage::load("img/logo.png");			
					$printer =	new Printer($this->fnConect()) ;
					$this->printLogo($printer);
					$foo = new GdEscposImage();
					$tm0=$this->setTm0($billid);
					$imr=$this->createImg($tm0[0],$tm0[1]);
					$foo -> readImageFromGdResource($imr);
					$this->pulse($printer);
					$printer->bitImageColumnFormat($foo);
					$this->printCut($printer);
					$printer -> close();
					$re["result"]=true;
				}else{
					throw new Exception("ไมสามารถเขียนไฟล์ได้ \n".$this->usb." \nโปรดตรวจสอบ\nการตั้งค่าผู้ใช้ในกลุ่มเครื่องพิมพ์");
				}
			}else{
				throw new Exception("ไม่พบที่อยู่ปลายทาง \n".$this->usb." \nโปรดตรวจสอบการเชื่อมต่ออุปกรณ์ \nหรือการตั้งค่าที่อยู่ของเครื่องพิมพ์");
			}
		}catch (Exception $e) {
			$re["message_error"]=$e->getMessage();
		}
		header('Content-type: application/json');
		echo json_encode($re);
	}
	protected function viewBill(string $billid){
		$tm0=$this->setTm0($billid);
		$im=$this->createImg($tm0[0],$tm0[1]);
		header('Content-Type: image/png');
		imagepng($im);
		imagedestroy($im);
	}
	protected function createImg(array $tm,string $stat=NULL){
		$xy=imagettfbbox($this->font_size, 0, $this->font_file,"กูกี่"); //$v."กูกี่"
		$ht=$xy[1]-$xy[7];
		$this->bill_height=$ht*count($tm)+$ht/3;

		$xy=imagettfbbox($this->font_size, 0, $this->font_file,"                 ");
		$ww=$xy[2]-$xy[0];
		$ws=$this->bill_width-($xy[2]-$xy[0]);
		
		$im=imagecreatetruecolor($this->bill_width,$this->bill_height);
		$this->white=imagecolorallocate($im, 255, 255, 255);
		$this->black=imagecolorallocate($im, 0, 0, 0);		
		imagefilledrectangle($im, 0, 0,$this->bill_width, $this->bill_height, $this->white);
		$top=0;
		foreach($tm as $k=>$v){
			$j=0;
			foreach($tm[$k] as $l=>$m){
				$lcr=$m["lcr"];
				$j+=1;
				$vu=$m["t"];

				$xy=imagettfbbox($this->font_size, 0, $this->font_file,$vu);
				$h=$xy[1]-$xy[7];
				$h=($h<$ht)?$ht:$h;
				$h+=2-2;
				$w=$xy[2]-$xy[0];
				$top=($j==1)?$top+$h:$top;
				$left=0;
				if($lcr=="c"){
					$left=($this->bill_width-$w)/2;
				}else if($lcr=="r"){
					$left=($this->bill_width-$w-2);
				}	
				if(substr($k,0,5)=="list_"){
					if($j==2){
						$left=10.69*3*(3/2);
					}else if($j==3){
						$left=$left-(10.69*9*(3/2));
						imagefilledrectangle($im, $left, $top-$ht+5,$this->bill_width, $top+10, $this->white);
					}					
				}
				imagettftext($im,$this->font_size,0,$left, $top, $this->black, $this->font_file,$vu);
			}
		}
		if($stat=="c"){
			$wt = imagecreatefrompng('img/pos/384x256_canc.png');
			imagealphablending($wt, false); 
			imagesavealpha($wt, true);
			imagefilter($wt, IMG_FILTER_COLORIZE, 0,0,0,127*0.2);
			
			//imagecopyresampled($im, $wt, 0, 0, 0, 0, 575*3/2, 256, 575, 256);
			//$wt = imagescale ($wt,576,256,IMG_BILINEAR_FIXED);
			imagecopy($im, $wt, 0, 0, 0, 0,576, 256);
			imagedestroy($wt);	
		}
		$imr = imagescale ($im,(2/3)*$this->bill_width,$this->bill_height,IMG_BILINEAR_FIXED);
		/*if($stat=="c"){
			$wt = imagecreatefrompng('img/pos/384x256_canc.png');
			imagealphablending($wt, false); 
			imagesavealpha($wt, true);
			imagefilter($wt, IMG_FILTER_COLORIZE, 0,0,0,127*0.0);
			imagecopy($imr, $wt, 0, 0, 0, 0,380, 256);
			imagedestroy($wt);	
		}*/
		return $imr;
	}
	protected function setTm0(string $sku):array{
		$re=[];
		$se=$this->getBillData($sku);
		$payu=json_decode($se["head"]["payu_json"],true);
		$payu=$this->cutValue0($payu);
		//print_r($se);exit;
		if(count($se["head"])>0&&count($se["list"])>0){
			$l=explode("\n",$this->receipt->receipt58->sale->head);
			for($i=0;$i<count($l);$i++){
				$re["shop_".$i]=[["t"=>$l[$i],"lcr"=>"l"]];
			}
			$re["usertime"]=[
				["t"=>"#".$se["head"]["sku"]." @".$se["head"]["user_sku"]." " ,"lcr"=>"l"],
				["t"=>substr($se["head"]["date_reg"],0,-3)."" ,"lcr"=>"r"]
			];
			if($se["head"]["member_sku"]!=""){
				$re["member"]=[
					["t"=>"สมาชิก : ".$se["head"]["member_name"]." #".$se["head"]["member_sku"]." " ,"lcr"=>"l"]
				];
			}
			$re["recv"]=[["t"=>$this->receipt->receipt58->sale->name,"lcr"=>"c"]];
			for($i=0;$i<count($se["list"]);$i++){
				$re["list_".$i]=[
					["t"=>$se["list"][$i]["n"],"lcr"=>"l"],
					["t"=>"".$se["list"][$i]["product_name"].''.($se["list"][$i]["s_type"]!="p"?" ".($se["list"][$i]["n_wlv"]*1)." ".$se["list"][$i]["unit_name"]:""),"lcr"=>"l"],
					["t"=>" ฿".($se["list"][$i]["s_type"]=="p"?number_format($se["list"][$i]["product_price"],2,'.',','):number_format(($se["list"][$i]["product_price"]*$se["list"][$i]["n_wlv"]),2,'.',','))."","lcr"=>"r"],
					["t"=>number_format($se["list"][$i]["product_price"]*$se["list"][$i]["n"]*$se["list"][$i]["n_wlv"],2,'.',','),"lcr"=>"r"]
				];
			}
			$re["total"]=[
				["t"=>"รวม ".$se["head"]["n"]." รายการ","lcr"=>"l"],
				["t"=>"รวมเงิน ".number_format($se["head"]["price"],2,'.',',')." บาท ","lcr"=>"r"]
			];
			//$re["hr1"]=[["t"=>"- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ","lcr"=>"c"]];
			$cash_only=0;
			if(count($payu)==1&&isset($payu["defaultroot"])){
				if(isset($payu["defaultroot"])){
					$cash_only=1;
					$re["payu_cash"]=[
						["t"=>"รับเงินสด ".number_format($se["head"]["min"],2,".",",")." ","lcr"=>"l"],
						["t"=>"เงินทอน ".number_format($se["head"]["mout"],2,'.',',')." บาท ","lcr"=>"r"]			
					];		
				}
			}else{
				$re["pay_by"]=[["t"=>"ชำระโดย","lcr"=>"c"]];
				$q=0;
				if(count($payu)==1&&isset($payu["creditroot"])){
					$re["pay_by_cash"]=[
						["t"=>"เงินสด/โอน/เช็ก","lcr"=>"l"],
						["t"=>"0.00 บาท ","lcr"=>"r"]			
					];	
				}
				foreach($payu as $k=>$v){
					$q+=1;
					$re["payu_".$q]=[
						["t"=>"".htmlspecialchars($v["name"])." ","lcr"=>"l"],
						["t"=>"".number_format($v["value"],2,'.',',')." บาท ","lcr"=>"r"]			
					];	
				}
			}
			//$re["hr2"]=[["t"=>"- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ","lcr"=>"c"]];
			if($se["head"]["mout"]>0&&$cash_only==0){
					$re["payu_mout"]=[
						["t"=>"เงินทอน ".number_format($se["head"]["mout"],2,'.',',')." บาท ","lcr"=>"r"]			
					];	
			}
			if($se["head"]["credit"]>0){
				$re["recv"][0]["t"].="/ใบแจ้งหนี้ "  ;
				$re["payu_credit"]=[
					["t"=>"***ใบเสร็จนี้ มียอดค้างชำระ ".number_format($se["head"]["credit"],2,'.',',')." บาท ","lcr"=>"r"]			
				];	
			}
			$re["tail"]=[["t"=>$this->receipt->receipt58->sale->foot,"lcr"=>"c"]];
			for($i=0;$i<$this->printer->feed;$i++){
				$re["line_feed_".$i]=[["t"=>" ","lcr"=>"l"]];
			}
		}
		return [$re,$se["head"]["stat"]];
	}
	private function cutValue0(array $payu):array{
		$re=[];
		foreach($payu as $k=>$v){
			if((float) $v["value"] > 0){
				$re[$k]=$v;
			}
		}
		return $re;
	}
	private function getBillData($sku):array{
		$re=["head"=>[],"list"=>[]];
		$sku=$this->getStringSqlSet($sku);
		$sql=[];
		$sql["head"]="SELECT  `bill_sell`.`sku`  AS  `sku`,`bill_sell`.`n`  AS  `n`, 
				IFNULL(`bill_sell`.`min`,0) AS `min`,IFNULL(`bill_sell`.`mout`,0) AS `mout`,
				IFNULL(`bill_sell`.`credit`,0) AS `credit`,`bill_sell`.`stat`,
				GetPayuArrRefData_(`bill_sell`.`payu_ref_json`) AS `payu_json`,
				`bill_sell`.`price` AS `price`, `bill_sell`.`date_reg` AS `date_reg`,
				`user_ref`.`sku` AS `user_sku`,
				CONCAT(`user_ref`.`name`,' ', `user_ref`.`lastname`) AS `user_name`,
				IFNULL(NVL2(`member_ref`.`id`,CONCAT(`member_ref`.`name`,' ', `member_ref`.`lastname`),''),'') AS `member_name`,
				IFNULL(`member_ref`.`sku`,'') AS `member_sku`
			FROM `bill_sell` 
			LEFT JOIN `user_ref`
			ON( `bill_sell`.`user`=`user_ref`.`sku_key`)
			LEFT JOIN `member_ref`
			ON( `bill_sell`.`member_sku_key`=`member_ref`.`sku_key`)
			WHERE bill_sell.sku=".$sku." LIMIT 1
		";
		$sql["list"]="SELECT  
				bill_sell_list.n AS n,bill_sell_list.n_wlv AS n_wlv,
				product_ref.name AS product_name,product_ref.barcode AS product_barcode,product_ref.price AS product_price,product_ref.s_type,
				unit_ref.name AS unit_name
			FROM `bill_sell_list` 
			LEFT JOIN product_ref
			ON( `bill_sell_list`.`product_sku_key`=`product_ref`.`sku_key`)
			LEFT JOIN unit_ref
			ON( bill_sell_list.unit_sku_key=unit_ref.sku_key)
			WHERE bill_sell_list.sku=".$sku."
				GROUP BY bill_sell_list.product_sku_root, bill_sell_list.sq
			ORDER BY `bill_sell_list`.`id` ASC";
		$se=$this->metMnSql($sql,["head","list"]);
		//print_r($se);
		if($se["result"]){
			$re["head"]=$se["data"]["head"][0];
			$re["list"]=$se["data"]["list"];
		}
		return $re;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	private function viewRet(string $sku){
		$tm0=$this->retSetTm0($sku);
		$im=$this->createImg($tm0);
		header('Content-Type: image/png');
		imagepng($im);
		imagedestroy($im);
	}
	protected function retPrint(string $sku){
		$re=["result"=>false,"message_error"=>"","sku"=>$sku];
		try {
			if($this->checkNP("exists")){
				if($this->checkNP("writable")){
					$printer = new Printer($this->fnConect());	
					$this->printLogo($printer);
					$foo = new GdEscposImage();
					$tm0=$this->retSetTm0($sku);
					$imr=$this->createImg($tm0);
					$foo -> readImageFromGdResource($imr);
					$this->pulse($printer);
					$printer->bitImageColumnFormat($foo);
					$this->printCut($printer);
					$printer -> close();
					$re["result"]=true;
				}else{
					throw new Exception("ไมสามารถเขียนไฟล์ได้ \n".$this->usb." \nโปรดตรวจสอบ\nการตั้งค่าผู้ใช้ในกลุ่มเครื่องพิมพ์");
				}
			}else{
				throw new Exception("ไม่พบที่อยู่ปลายทาง \n".$this->usb." \nโปรดตรวจสอบการเชื่อมต่ออุปกรณ์ \nหรือการตั้งค่าที่อยู่ของเครื่องพิมพ์");
			}
		}catch (Exception $e) {
			$re["message_error"]=$e->getMessage();
		}
		header('Content-type: application/json');
		echo json_encode($re);
	}
	private function retSetTm0(string $sku):array{
		$re=[];
		$se=$this->getRetBillData($sku);
		//print_r($se);
		if(count($se["list"])>0&&count($se["head"])>0){
			//$rt=preg_replace("/\\D/","", $se["head"][0]["date_reg"])."-".$se["head"][0]["bill"];
			$rt=$se["head"][0]["sku"];
			$l=explode("\n",$this->receipt->receipt58->sale->head);
			for($i=0;$i<count($l);$i++){
				$re["shop_".$i]=[["t"=>$l[$i],"lcr"=>"l"]];
			}
			$re["usertime"]=[
				["t"=>" พ:".$se["head"][0]["user_sku"]." " ,"lcr"=>"l"],
				["t"=>substr($se["head"][0]["date_reg"],0,-3)." " ,"lcr"=>"r"]
			];
			if($se["head"][0]["member_sku"]!=""){
				$re["member"]=[
					["t"=>"สมาชิก : ".$se["head"][0]["member_name"]." #".$se["head"][0]["member_sku"]." " ,"lcr"=>"l"]
				];
			}
			$re["rt"]=[["t"=>"#".$rt." " ,"lcr"=>"c"]];
			$re["recv"]=[["t"=>"ใบคืนสินค้า","lcr"=>"c"]];
			$n_list=0;
			$sum=0;
			for($i=0;$i<count($se["list"]);$i++){
				$n_list+=1;
				$sum+=$se["list"][$i]["n"]*$se["list"][$i]["product_price"]*($se["list"][$i]["s_type"]=="p"?1:($se["list"][$i]["n_wlv"]));
				$re["list_".$i]=[
					["t"=>$se["list"][$i]["n"],"lcr"=>"l"],
					["t"=>"   ".$se["list"][$i]["product_name"]."".($se["list"][$i]["s_type"]=="p"?"":" ".($se["list"][$i]["n_wlv"]*1)." ".$se["list"][$i]["unit_name"]),"lcr"=>"l"],
					["t"=>"  ฿".number_format($se["list"][$i]["product_price"]*($se["list"][$i]["s_type"]=="p"?1:($se["list"][$i]["n_wlv"])),2,'.',',')."","lcr"=>"r"],
					["t"=>number_format($se["list"][$i]["product_price"]*$se["list"][$i]["n"]*($se["list"][$i]["s_type"]=="p"?1:$se["list"][$i]["n_wlv"]),2,'.',','),"lcr"=>"r"]
				];
			}
			$re["total"]=[
				["t"=>"รวม ".$n_list." รายการ","lcr"=>"l"],
				["t"=>"รวมเงิน ".number_format($sum,2,'.',',')." บาท ","lcr"=>"r"]
			];
			$re["listhreg"]=[["t"=>"------------------------------------------------------------------------","lcr"=>"c"]];
			$re["rett"]=[
				["t"=>"คืนเป็นเงินสด","lcr"=>"l"],
				["t"=>number_format($sum,2,'.',',')." บาท ","lcr"=>"r"]
			];
			$re["tail"]=[["t"=>$this->receipt->receipt58->sale->foot,"lcr"=>"c"]];
			for($i=0;$i<$this->printer->feed;$i++){
				$re["line_feed_".$i]=[["t"=>" ","lcr"=>"l"]];
			}
		}
		//print_r($re);
		return $re;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	private function viewmmm(string $sku){
		$tm0=$this->mmmSetTm0($sku);
		$im=$this->createImg($tm0);
		header('Content-Type: image/png');
		imagepng($im);
		imagedestroy($im);
	}
	private function mmmCreateImg(array $tm){
		$xy=imagettfbbox($this->font_size, 0, $this->font_file,"กูกี่"); //$v."กูกี่"
		$ht=$xy[1]-$xy[7];
		$this->bill_height=$ht*count($tm);
		
		$xy=imagettfbbox($this->font_size, 0, $this->font_file,"                 ");
		$ww=$xy[2]-$xy[0];
		$ws=$this->bill_width-($xy[2]-$xy[0]);
		$im=imagecreatetruecolor($this->bill_width,$this->bill_height);
		$this->white=imagecolorallocate($im, 255, 255, 255);
		$this->black=imagecolorallocate($im, 0, 0, 0);		
		imagefilledrectangle($im, 0, 0,$this->bill_width, $this->bill_height, $this->white);
		$top=0;
		foreach($tm as $k=>$v){
			$j=0;
			foreach($tm[$k] as $l=>$m){
				$lcr=$m["lcr"];
				$j+=1;
				$vu=$m["t"];

				$xy=imagettfbbox($this->font_size, 0, $this->font_file,$vu);
				$h=$xy[1]-$xy[7];
				$h=($h<$ht)?$ht:$h;
				$h+=1;
				$w=$xy[2]-$xy[0];
				$top=($j==1)?$top+$h:$top;
				$left=0;
				if($lcr=="c"){
					$left=($this->bill_width-$w)/2;
				}else if($lcr=="r"){
					$left=($this->bill_width-$w-2);
				}	
				if($j==3&&substr($k,0,5)=="list_"){
					imagefilledrectangle($im, $ws, $top-$ht,$this->bill_width, $top, $this->white);
				}
				imagettftext($im,$this->font_size,0,$left, $top, $this->black, $this->font_file,$vu);
			}
		}
		$imr = imagescale ($im,(3/5)*$this->bill_width,$this->bill_height,IMG_BILINEAR_FIXED);
		return $imr;
	}
	protected function mmmPrint(string $sku){
		$re=["result"=>false,"message_error"=>"","sku"=>$sku];
		try {
			if($this->checkNP("exists")){
				if($this->checkNP("writable")){
					$printer = new Printer($this->fnConect());	
					$this->printLogo($printer);
					$foo = new GdEscposImage();
					$tm0=$this->mmmSetTm0($sku);
					$imr=$this->createImg($tm0);
					$foo -> readImageFromGdResource($imr);
					$this->pulse($printer);
					$printer->bitImageColumnFormat($foo);
					$this->printCut($printer);
					$printer -> close();
					$re["result"]=true;
				}else{
					throw new Exception("ไมสามารถเขียนไฟล์ได้ \n".$this->usb." \nโปรดตรวจสอบ\nการตั้งค่าผู้ใช้ในกลุ่มเครื่องพิมพ์");
				}
			}else{
				throw new Exception("ไม่พบที่อยู่ปลายทาง \n".$this->usb." \nโปรดตรวจสอบการเชื่อมต่ออุปกรณ์ \nหรือการตั้งค่าที่อยู่ของเครื่องพิมพ์");
			}
		}catch (Exception $e) {
			$re["message_error"]=$e->getMessage();
		}
		header('Content-type: application/json');
		echo json_encode($re);
	}
	private function mmmSetTm0(string $sku):array{
		$re=[];
		$se=$this->getmmmBillData($sku);
		//print_r($se);
		if(count($se["list"])>0&&count($se["head"])>0){
			$rt=$se["head"][0]["sku"];
			$l=explode("\n",$this->receipt->receipt58->sale->head);
			for($i=0;$i<count($l);$i++){
				$re["shop_".$i]=[["t"=>$l[$i],"lcr"=>"l"]];
			}
			$re["usertime"]=[
				["t"=>" พ:".$se["head"][0]["user_sku"]." " ,"lcr"=>"l"],
				["t"=>substr($se["head"][0]["date_reg"],0,-3)." " ,"lcr"=>"r"]
			];
			$re["rt"]=[["t"=>"#".$rt." " ,"lcr"=>"c"]];
			$re["recv"]=[["t"=>"ใบแตกสินค้า","lcr"=>"c"]];
			$re["itq"]=[["t"=>"คลัง ".$se["list"][0]["it_name"],"lcr"=>"l"]];
			$hn=$this->bc->cutWord2(0,$this->font_size,$this->font_file,$se["head"][0]["pdname"],384*(3/2));
			for($i=0;$i<count($hn);$i++){
				$re["hp".$i]=[["t"=>$hn[$i],"lcr"=>"l"]];
			}
			$re["hbc"]=[["t"=>$se["head"][0]["barcode"],"lcr"=>"l"],
				["t"=>"จำนวน ".$se["head"][0]["n"]." ".$se["head"][0]["unit_name"],"lcr"=>"r"]
			];
			$re["art".$i]=[["t"=>"แตกเป็น","lcr"=>"c"]];
			$n_list=0;
			$sum=0;
			for($i=0;$i<count($se["list"]);$i++){
				$pg=$this->bc->cutWord2(0,$this->font_size,$this->font_file,$se["list"][$i]["product_name"],384*(3/2));
				
				for($j=0;$j<count($pg);$j++){
					if($j==0){
						$re["listhr_".$i."".$j]=[["t"=>"------------------------------------------------------------------------","lcr"=>"l"]];
					}					
					$re["list_".$i."".$j]=[["t"=>$pg[$j],"lcr"=>"l"]];

				}
				$re["pgbc".$i]=[["t"=>$se["list"][$i]["product_barcode"],"lcr"=>"l"],
					["t"=>"จำนวน ".$se["list"][$i]["n"]." ".$se["list"][$i]["unit_name"],"lcr"=>"r"]
				];
			}
			$re["listhreg_".$i."".$j]=[["t"=>"------------------------------------------------------------------------","lcr"=>"l"]];
			$re["tail"]=[["t"=>$this->receipt->receipt58->sale->foot,"lcr"=>"c"]];
			for($i=0;$i<$this->printer->feed;$i++){
				$re["line_feed_".$i]=[["t"=>" ","lcr"=>"l"]];
			}
		}
		//print_r($re);
		return $re;
	}
	private function getmmmBillData(string $sku):array{
		$re=["list"=>[],"head"=>[]];
		$sku=$this->getStringSqlSet($sku);
		$sql=[];
		$sql["set"]="SELECT @date_reg:=(SELECT date_reg FROM bill_in WHERE sku=".$sku." ),
			@bill:=(SELECT bill FROM bill_in WHERE sku=".$sku.")
		";
		$sql["head"]="SELECT bill_in.id,bill_in.sku,bill_in.bill,
			mmm.skuroot_n AS skuroot_n,product_ref.barcode AS barcode,
				bill_in.user,bill_in.date_reg,
				bill_in_list.name AS pdname,
				(bill_in_list.sum/bill_in_list.n) AS cost1,
				unit_ref.name AS unit_name,
				CONCAT(`user_ref`.`name`,' ', `user_ref`.`lastname`) AS `user_name`,`user_ref`.`sku` AS user_sku,
				bill_in.n
			FROM bill_in
			LEFT JOIN mmm
			ON(bill_in.id=mmm.bill_in_id)
			LEFT JOIN bill_in_list
			ON(bill_in.lot_root=bill_in_list.bill_in_sku AND bill_in_list.product_sku_root=mmm.skuroot)
			LEFT JOIN product_ref
			ON(mmm.skukey=product_ref.sku_key)
			LEFT JOIN unit_ref
			ON(bill_in_list.unit_sku_key=unit_ref.sku_key)
			LEFT JOIN `user_ref`
			ON( `bill_in`.`user`=`user_ref`.`sku_key`)
			WHERE bill_in.sku=".$sku."
		";
		$sql["list"]="SELECT  `bill_in_list`.`n`  AS  `n`, 
				`bill_in_list`.`product_sku_root` ,
				bill_in_list.sum AS sum,
				product_ref.barcode AS `product_barcode`,product_ref.name AS `product_name`,product_ref.price AS `product_price`,
				unit_ref.name AS `unit_name`,it.name AS it_name
			FROM `bill_in` 
			LEFT JOIN `bill_in_list` 
			ON(bill_in_list.id>=bill_in.r_ AND bill_in_list.id<=bill_in._r And bill_in.sku=bill_in_list.bill_in_sku)
			LEFT JOIN product_ref
			ON(bill_in_list.product_sku_key=product_ref.sku_key)
			LEFT JOIN unit_ref
			ON(bill_in_list.unit_sku_key=unit_ref.sku_key)
			LEFT JOIN it
			ON (bill_in_list.stkey=it.sku_key)
			WHERE bill_in.sku=".$sku."
		";
		$se=$this->metMnSql($sql,["head","list"]);
		//print_r($se);
		if($se["result"]){
			$re["head"]=$se["data"]["head"];
			$re["list"]=$se["data"]["list"];
		}
		return $re;
	}
	private function getRetBillData(string $sku):array{
		$re=["list"=>[],"head"=>[]];
		$sku=$this->getStringSqlSet($sku);
		$sql=[];
		$sql["set"]="SELECT @date_reg:=(SELECT date_reg FROM bill_in WHERE sku=".$sku." ),
			@bill:=(SELECT bill FROM bill_in WHERE sku=".$sku.")
		";
		$sql["head"]="SELECT bill_in.id,bill_in.sku,bill_in.bill,
				bill_in.changto,bill_in.note,bill_in.user,bill_in.date_reg,
				CONCAT(`user_ref`.`name`,' ', `user_ref`.`lastname`) AS `user_name`,`user_ref`.`sku` AS `user_sku`,
				bill_in.n,
				IFNULL(NVL2(`member_ref`.`id`,CONCAT(`member_ref`.`name`,' ', `member_ref`.`lastname`),''),'') AS `member_name`,
				IFNULL(`member_ref`.`mb_type`,'') AS `mb_type`,IFNULL(`member_ref`.`sku`,'') AS `member_sku`,
				`member_ref`.`sku_root` AS `member_sku_root`
			FROM bill_in
			LEFT JOIN `user_ref`
			ON( `bill_in`.`user`=`user_ref`.`sku_key`)
			LEFT JOIN `bill_sell`
			ON( `bill_in`.`bill`=`bill_sell`.`sku`)
			LEFT JOIN `member_ref`
			ON( `bill_sell`.`member_sku_key`=`member_ref`.`sku_key`)
			WHERE bill_in.sku=".$sku."
		";
		$sql["list"]="SELECT  `bill_in_list`.`n`  AS  `n`, `bill_in_list`.`n_wlv`  AS  `n_wlv`,bill_in_list.s_type, `bill_in_list`.`note`  AS  `note`, 
				`bill_in_list`.`product_sku_root` ,
				product_ref.barcode AS `product_barcode`,product_ref.name AS `product_name`,product_ref.price AS `product_price`,
				unit_ref.name AS `unit_name`
			FROM `bill_in` 
			LEFT JOIN `bill_in_list` 
			ON(bill_in_list.id>=bill_in.r_ AND bill_in_list.id<=bill_in._r AND bill_in.sku=bill_in_list.bill_in_sku)
			LEFT JOIN product_ref
			ON(bill_in_list.product_sku_key=product_ref.sku_key)
			LEFT JOIN unit_ref
			ON(bill_in_list.unit_sku_key=unit_ref.sku_key)
			WHERE bill_in.sku=".$sku."
		";
		$se=$this->metMnSql($sql,["head","list"]);
		//print_r($se);
		if($se["result"]){
			$re["head"]=$se["data"]["head"];
			$re["list"]=$se["data"]["list"];
		}
		return $re;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	private function viewMove(string $sku){
		$tm0=$this->moveSetTm0($sku);
		$im=$this->createImg($tm0,"view");
		header('Content-Type: image/png');
		imagepng($im);
		imagedestroy($im);
	}
	protected function movePrint(string $sku){
		$re=["result"=>false,"message_error"=>"","sku"=>$sku];
		try {
			if($this->checkNP("exists")){
				if($this->checkNP("writable")){
					$printer = new Printer($this->fnConect());	
					$this->printLogo($printer);
					$foo = new GdEscposImage();
					$tm0=$this->moveSetTm0($sku);
					$imr=$this->createImg($tm0);
					$foo -> readImageFromGdResource($imr);
					$this->pulse($printer);
					$printer->bitImageColumnFormat($foo);
					$this->printCut($printer);
					$printer -> close();
					$re["result"]=true;
				}else{
					throw new Exception("ไมสามารถเขียนไฟล์ได้ \n".$this->usb." \nโปรดตรวจสอบ\nการตั้งค่าผู้ใช้ในกลุ่มเครื่องพิมพ์");
				}
			}else{
				throw new Exception("ไม่พบที่อยู่ปลายทาง \n".$this->usb." \nโปรดตรวจสอบการเชื่อมต่ออุปกรณ์ \nหรือการตั้งค่าที่อยู่ของเครื่องพิมพ์");
			}
		}catch (Exception $e) {
			$re["message_error"]=$e->getMessage();
		}
		header('Content-type: application/json');
		echo json_encode($re);
	}
	protected function moveSetTm0(string $sku):array{
		$re=[];
		$se=$this->getMoveBillData($sku);
		//print_r($se);
		if(count($se["list"])>0&&count($se["head"])>0){
			$l=explode("\n",$this->receipt->receipt58->sale->head);
			for($i=0;$i<count($l);$i++){
				$re["shop_".$i]=[["t"=>$l[$i],"lcr"=>"l"]];
			}
			$re["usertime"]=[
				["t"=>" พ:".$se["head"][0]["user_sku"]." " ,"lcr"=>"l"],
				["t"=>substr($se["head"][0]["date_reg"],0,-3)." " ,"lcr"=>"r"]
			];
			$re["move_to"]=[["t"=>$se["head"][0]["st_name"]." >> ".$se["head"][0]["st2_name"],"lcr"=>"l"]];
			$re["rt"]=[["t"=>"#".$se["head"][0]["sku"]." " ,"lcr"=>"c"]];
			$re["recv"]=[["t"=>"ใบย้ายสินค้า","lcr"=>"c"]];
			$re["mnh_list"]=[["t"=>"--------------------------------------------------------------------------------","lcr"=>"l"]];
			$n_list=0;
			$sum=0;
			for($i=0;$i<count($se["list"]);$i++){
				$n_list+=1;
				$sum+=$se["list"][$i]["n"]*$se["list"][$i]["product_price"];
				$re["mlist_".$i]=[
					["t"=>$se["list"][$i]["product_name"],"lcr"=>"l"]

				];
				$re["mnlist_".$i]=[
					["t"=>$se["list"][$i]["product_barcode"],"lcr"=>"l"],
					["t"=>$se["list"][$i]["n"]."".($se["list"][$i]["s_type"]!="p"?"×".$se["list"][$i]["n_wlv"]*1:"")." ".$se["list"][$i]["unit_name"]."","lcr"=>"r"]
				];
				$re["mn_list_".$i]=[
					["t"=>"--------------------------------------------------------------------------------","lcr"=>"l"]
				];
			}
			$re["total"]=[
				["t"=>"รวม ".$n_list." รายการ","lcr"=>"c"]
			];
			for($i=0;$i<$this->printer->feed;$i++){
				$re["line_feed_".$i]=[["t"=>" ","lcr"=>"l"]];
			}
		}
		//print_r($re);
		return $re;
	}
	private function getMoveBillData(string $sku):array{
		$re=["list"=>[],"head"=>[]];
		$sku=$this->getStringSqlSet($sku);
		$sql=[];
		$sql["head"]="SELECT bill_in.id,bill_in.sku,bill_in.n,
				IFNULL(bill_in.note,'') AS `note`,bill_in.date_reg,
				CONCAT(`user_ref`.`name`,' ', `user_ref`.`lastname`) AS `user_name`,user_ref.sku AS user_sku,
				it_ref.name AS `st_name`,it2_ref.name AS `st2_name`		
			FROM bill_in
			LEFT JOIN bill_in_list
			ON(  bill_in_list.id>=bill_in.r_  AND bill_in_list.id<=bill_in._r AND  bill_in.sku=bill_in_list.bill_in_sku)
			LEFT JOIN `user_ref`
			ON( `bill_in`.`user`=`user_ref`.`sku_key`)
			LEFT JOIN it_ref
			ON(bill_in.stkey_=it_ref.sku_key)
			LEFT JOIN it_ref as it2_ref
			ON(bill_in_list.stkey=it2_ref.sku_key)
			WHERE bill_in.sku=".$sku." 
		";
		$sql["list"]="SELECT  `bill_in_list`.`id`,`bill_in_list`.`bill_in_sku`  AS  `sku`,`bill_in_list`.`n`  AS  `n`, 
				bill_in_list.n_wlv,bill_in_list.s_type,
				`bill_in_list`.`product_sku_root` ,bill_in_list.name AS `product_name`,
				product_ref.barcode AS `product_barcode`,`product_ref`.`price` AS `product_price`,
				unit_ref.name AS `unit_name`
			FROM `bill_in_list` 
			LEFT JOIN `bill_in` 
			ON(bill_in_list.bill_in_sku=bill_in.sku)
			LEFT JOIN product_ref
			ON(bill_in_list.product_sku_key=product_ref.sku_key)
			LEFT JOIN unit_ref
			ON(bill_in_list.unit_sku_key=unit_ref.sku_key)
			WHERE  bill_in_list.id>=bill_in.r_  AND bill_in_list.id<=bill_in._r AND  bill_in_list.bill_in_sku=".$sku." 
		";
		$se=$this->metMnSql($sql,["head","list"]);
		//print_r($se);
		if($se["result"]){
			$re["head"]=$se["data"]["head"];
			$re["list"]=$se["data"]["list"];
		}
		return $re;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	private function barcodeImg(string $type,string $barcode,int $width,int $height,int $br,int $mgt,int $mgbt){
		$im=$this->bc->createImgBarcode($type,$barcode,$width,$height,$br,$mgt,$mgbt);
		header('Content-Type: image/png');
		imagepng($im);
		imagedestroy($im);
	}
	private function labelImg(string $sku_root){
		$se=$this->getPd($sku_root);
		$im=$this->bc->createImgLabel($se["name"],$se["barcode"],$se["price"],$se["unit_name"],$this->font_size2);
		header('Content-Type: image/png');
		imagepng($im);
		imagedestroy($im);
	}
	private function labelPrint(string $sku_root){
		$re=["result"=>false,"message_error"=>""];
		try {
			if($this->checkNP("exists")){
				if($this->checkNP("writable")){
					$printer = new Printer($this->fnConect());	
					$foo = new GdEscposImage();
					$se=$this->getPd($sku_root);
					$imr=$this->bc->createImgLabel($se["name"],$se["barcode"],$se["price"],$se["unit_name"],$this->font_size2);
					$foo -> readImageFromGdResource($imr);
					//$this->pulse($printer);
					$printer->bitImageColumnFormat($foo);
					//$this->printCut($printer);
					$printer -> close();
					$re["result"]=true;
				}else{
					throw new Exception("ไมสามารถเขียนไฟล์ได้ \n".$this->usb." \nโปรดตรวจสอบ\nการตั้งค่าผู้ใช้ในกลุ่มเครื่องพิมพ์");
				}
			}else{
				throw new Exception("ไม่พบที่อยู่ปลายทาง \n".$this->usb." \nโปรดตรวจสอบการเชื่อมต่ออุปกรณ์ \nหรือการตั้งค่าที่อยู่ของเครื่องพิมพ์");
			}
		}catch (Exception $e) {
			$re["message_error"]=$e->getMessage();
		}
		header('Content-type: application/json');
		echo json_encode($re);
	}
	private function getPd(string $sku_root):array{
		$re=[];
		$sql=[];
		$sql["get"]="SELECT product.name,product.s_type,IFNULL(product.barcode,'') AS `barcode`,IFNULL(product.price,'') AS `price`,unit.name  AS `unit_name`
			FROM product 
			LEFT JOIN unit ON (product.unit=unit.sku_root) 
			WHERE product.sku_root='".$sku_root."';";
		$se=$this->metMnSql($sql,["get"]);
		if(isset($se["data"]["get"][0])){
			$re=$se["data"]["get"][0];
		}	
		return $re;
	}
	###########################################################
	private function labelWLVPrint(string $sku_root){
		$re=["result"=>false,"message_error"=>""];
		$n_wlv=isset($_POST["n_wlv"])?(float) $_POST["n_wlv"]*1:0;
		try {
			if($n_wlv>0){
				$this->usb=$this->printer_label->address;
				$this->setCNT($this->usb);
				if($this->checkNP("exists")){
					if($this->checkNP("writable")){
						$printer = new Printer($this->fnConect());	
						$foo = new GdEscposImage();
						$se=$this->getPd($sku_root);
						$se["n_wlv"]=$n_wlv;
						$se["barcode"]=$this->createBcWLV(($se["barcode"]===null?"":$se["barcode"]),$n_wlv);
						$imr=$this->bc->createImgLabelWLV($se,$this->font_size2);
						$foo -> readImageFromGdResource($imr);
						$printer->bitImageColumnFormat($foo);
						$printer -> close();
						$re["result"]=true;
					}else{
						throw new Exception("ไมสามารถเขียนไฟล์ได้ \n".$this->usb." \nโปรดตรวจสอบ\nการตั้งค่าผู้ใช้ในกลุ่มเครื่องพิมพ์");
					}
				}else{
					throw new Exception("ไม่พบที่อยู่ปลายทาง \n".$this->usb." \nโปรดตรวจสอบการเชื่อมต่ออุปกรณ์ \nหรือการตั้งค่าที่อยู่ของเครื่องพิมพ์");
				}
			}else{
					throw new Exception("จำนวน ไม่อยู่ในรูปแบบ หรือ มีค่า = 0");
			}
		}catch (Exception $e) {
			$re["message_error"]=$e->getMessage();
		}
		header('Content-type: application/json');
		echo json_encode($re);
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	private function textPrint(string $text){
		$re=["result"=>false,"message_error"=>""];
		try {
			if($this->checkNP("exists")){
				if($this->checkNP("writable")){
					$printer = new Printer($this->fnConect());	
					$foo = new GdEscposImage();
					$tm0=$this->textSetTm0($text);
					$imr=$this->createImg($tm0);
					$foo -> readImageFromGdResource($imr);
					$this->pulse($printer);
					$printer->bitImageColumnFormat($foo);
					$this->printCut($printer);
					$printer -> close();
					$re["result"]=true;
				}else{
					throw new Exception("ไมสามารถเขียนไฟล์ได้ \n".$this->usb." \nโปรดตรวจสอบ\nการตั้งค่าผู้ใช้ในกลุ่มเครื่องพิมพ์");
				}
			}else{
				throw new Exception("ไม่พบที่อยู่ปลายทาง \n".$this->usb." \nโปรดตรวจสอบการเชื่อมต่ออุปกรณ์ \nหรือการตั้งค่าที่อยู่ของเครื่องพิมพ์");
			}
		}catch (Exception $e) {
			$re["message_error"]=$e->getMessage();
		}
		header('Content-type: application/json');
		echo json_encode($re);
	}
	private function textSetTm0(string $text):array{
		$re=[];
		$t=explode("\n",$text);
		for($i=0;$i<count($t);$i++){
			$re["t".$i]=[["t"=>$t[$i],"lcr"=>"l"]];
		}
			for($i=0;$i<$this->printer->feed;$i++){
				$re["line_feed_".$i]=[["t"=>" ","lcr"=>"l"]];
			}
		//print_r($re);
		return $re;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	private function viewPay(string $sku){
		$tm0=$this->paySetTm0($sku);
		$im=$this->createImg($tm0);
		header('Content-Type: image/png');
		imagepng($im);
		imagedestroy($im);
	}
	protected function payPrint(string $sku){
		$re=["result"=>false,"message_error"=>"","sku"=>$sku];
		try {
			if($this->checkNP("exists")){
				if($this->checkNP("writable")){
					$printer = new Printer($this->fnConect());	
					$this->printLogo($printer);
					$foo = new GdEscposImage();
					$tm0=$this->paySetTm0($sku);
					$imr=$this->createImg($tm0);
					$foo -> readImageFromGdResource($imr);
					$this->pulse($printer);
					$printer->bitImageColumnFormat($foo);
					$this->printCut($printer);
					$printer -> close();
					$re["result"]=true;
				}else{
					throw new Exception("ไมสามารถเขียนไฟล์ได้ \n".$this->usb." \nโปรดตรวจสอบ\nการตั้งค่าผู้ใช้ในกลุ่มเครื่องพิมพ์");
				}
			}else{
				throw new Exception("ไม่พบที่อยู่ปลายทาง \n".$this->usb." \nโปรดตรวจสอบการเชื่อมต่ออุปกรณ์ \nหรือการตั้งค่าที่อยู่ของเครื่องพิมพ์");
			}
		}catch (Exception $e) {
			$re["message_error"]=$e->getMessage();
		}
		header('Content-type: application/json');
		echo json_encode($re);
	}
	private function paySetTm0(string $sku):array{
		$re=[];
		$se=$this->getPayBillData($sku);
		$payu=json_decode($se["head"]["payu_json"],true);
		$payu=$this->cutValue0($payu);
		//print_r($se);
		if(count($se["list"])>0&&count($se["head"])>0){
			$rt=$se["head"]["sku"];
			$l=explode("\n",$this->receipt->receipt58->sale->head);
			for($i=0;$i<count($l);$i++){
				$re["shop_".$i]=[["t"=>$l[$i],"lcr"=>"l"]];
			}
			$re["usertime"]=[
				["t"=>" พ:".$se["head"]["user_sku"]." " ,"lcr"=>"l"],
				["t"=>substr($se["head"]["date_reg"],0,-3)." " ,"lcr"=>"r"]
			];
			if($se["head"]["member_sku"]!=""){
				$re["member"]=[
					["t"=>"สมาชิก : ".$se["head"]["member_name"]."  รหัส : ".$se["head"]["member_sku"]." " ,"lcr"=>"l"]
				];
			}
			$re["rt"]=[["t"=>"เลขที่ : ".$rt." " ,"lcr"=>"l"]];
			$re["recv"]=[["t"=>"ใบเสร็จรับเงินชำระค้างจ่าย","lcr"=>"c"]];
			$n_list=0;
			$credit=0;
			$sum=0;
			$re["thr_0"]=[
				["t"=>"-----------------------------------------------------------------------","lcr"=>"c"]
			];	
			$re["th"]=[
				["t"=>"  รายละเอียด ใบเสร็จ","lcr"=>"l"],
				["t"=>"ตัดยอดชำระ  ","lcr"=>"r"]
			];
			$re["thr_1"]=[
				["t"=>"-----------------------------------------------------------------------","lcr"=>"c"]
			];
			for($i=0;$i<count($se["list"]);$i++){
				$n_list+=1;
				$sum+=$se["list"][$i]["min"];
				$credit+=$se["list"][$i]["credit"];
				$re["listp_".$i]=[
					["t"=>$se["list"][$i]["bill_sell_sku"]." ".substr($se["list"][$i]["bill_sell_date_reg"],0,-3),"lcr"=>"l"],
					["t"=>number_format($se["list"][$i]["min"],2,".",","),"lcr"=>"r"]
				];
				$re["blo_".$i]=[
					["t"=>"  *ยอดค้าง ".number_format($se["list"][$i]["credit"],2,".",",")."","lcr"=>"l"]

				];		
			$re["hr_".$i]=[
				["t"=>"-----------------------------------------------------------------------","lcr"=>"c"]
			];	
			}
			/*for($i=0;$i<count($se["list"]);$i++){
				$re["listp_".$i]=[
					["t"=>$se["list"][$i]["bill_sell_sku"]." ".substr($se["list"][$i]["bill_sell_date_reg"],0,-3),"lcr"=>"l"],
					["t"=>number_format($se["list"][$i]["min"],2,".",","),"lcr"=>"r"]

				];
			}*/
			$re["total"]=[
				["t"=>"ค้างชำระ ".$n_list." ใบเสร็จ เงินรวม","lcr"=>"l"],
				["t"=>"".number_format($credit,2,'.',',')." บาท ","lcr"=>"r"]
			];
			$re["paynow"]=[
				["t"=>"ชำระครั้งนี้","lcr"=>"l"],
				["t"=>"".number_format($sum,2,'.',',')." บาท ","lcr"=>"r"]
			];
			
			$q=0;	
			$re["bal"]=[
				["t"=>"ยอดค้างชำระ คงเหลือ","lcr"=>"l"],
				["t"=>"".number_format(($credit-$sum),2,'.',',')." บาท ","lcr"=>"r"]
			];
			$re["pay_by"]=[["t"=>"ชำระโดย","lcr"=>"c"]];			
			$sum_pay=0;
			foreach($payu as $k=>$v){
				$q+=1;
				$sum_pay+=$v["value"];
				$re["payu_".$q]=[
					["t"=>"".htmlspecialchars($v["name"])." ","lcr"=>"l"],
					["t"=>"".number_format($v["value"],2,'.',',')." บาท ","lcr"=>"r"]			
				];	
			}

			$mout=$sum_pay-$sum;
			$mout=($mout<0)?0:$mout;
			$re["payu_mout"]=[
				["t"=>"เงินทอน ".number_format($mout,2,'.',',')." บาท ","lcr"=>"r"]			
			];	
			
			$re["tail"]=[["t"=>$this->receipt->receipt58->sale->foot,"lcr"=>"c"]];
			for($i=0;$i<$this->printer->feed;$i++){
				$re["line_feed_".$i]=[["t"=>" ","lcr"=>"l"]];
			}
		}
		//print_r($re);exit;
		return $re;
	}
	private function getPayBillData(string $sku):array{
		$re=["head"=>[],"list"=>[]];
		$sku=$this->getStringSqlSet($sku);
		$sql=[];
		$sql["head"]="SELECT  @bill_rca_id:=`bill_rca`.`id`  AS  `id`,`bill_rca`.`sku`  AS  `sku`,
				`bill_rca`.`pay`,`bill_rca`.`min`,`bill_rca`.`credit`,`bill_rca`.`onoff`,
				GetPayuArrRefData_(`bill_rca`.`payu_json`) AS `payu_json`,
				`bill_rca`.`date_reg` AS `date_reg`,
				CONCAT(`user_ref`.`name`,' ', `user_ref`.`lastname`) AS `user_name`,
				`user_ref`.`sku` AS `user_sku`,
				CONCAT(`user_ref2`.`name`,' ', `user_ref2`.`lastname`) AS `user_name_edit`,
				IFNULL(NVL2(`member_ref`.`id`,CONCAT(`member_ref`.`name`,' ', `member_ref`.`lastname`),''),'') AS `member_name`,
				IFNULL(`member_ref`.`mb_type`,'') AS `mb_type`,IFNULL(`member_ref`.`sku`,'') AS `member_sku`,
				IFNULL(`member_ref`.`mb_type`,'') AS `mb_type`,
				`member_ref`.`sku_root` AS `member_sku_root`
			FROM `bill_rca` 
			LEFT JOIN `user_ref`
			ON( `bill_rca`.`user`=`user_ref`.`sku_key`)
			LEFT JOIN `user_ref` AS `user_ref2`
			ON( `bill_rca`.`user_edit`=`user_ref2`.`sku_key`)
			LEFT JOIN `member_ref`
			ON( `bill_rca`.`member_sku_key`=`member_ref`.`sku_key`)
			WHERE `bill_rca`.`sku`=".$sku."";
		$sql["list"]="SELECT  
				bill_rca_list.credit	,bill_rca_list.min	,bill_rca_list.money_balance,
				`bill_sell`.`sku` AS `bill_sell_sku`,
				`bill_sell`.`date_reg` AS `bill_sell_date_reg`
			FROM `bill_rca_list` 
			LEFT JOIN `bill_sell`
			ON(bill_rca_list.bill_sell_id=`bill_sell`.`id`)
			WHERE `bill_rca_id`=@bill_rca_id 
			ORDER BY `bill_rca_list`.`id` ASC";
		$se=$this->metMnSql($sql,["head","list"]);
		//print_r($se);
		if($se["result"]&&isset($se["data"]["head"][0])){
			$re["head"]=$se["data"]["head"][0];
			$re["list"]=$se["data"]["list"];
		}
		return $re;
	}
}
?>
