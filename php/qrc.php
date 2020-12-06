<?php   /*--apt DIY_POS;--ext php;--version 0.0;*/
require("library/php-qrcode-master/qrcode.php");
class qrc extends main{
	public function run(){
		$generator = new QRCode($_REQUEST['d'], $_REQUEST);
		$generator->output_image();
	}
}
?>
