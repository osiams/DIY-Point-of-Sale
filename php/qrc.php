<?php 
require("library/barcode-master/barcode.php");
class qrc extends main{
	public function run(){
		$generator = new barcode_generator($_REQUEST['d'], $_REQUEST);
		 $generator->output_image("png", $_REQUEST['s'], $_REQUEST['d'],"png");
	}
}
?>
