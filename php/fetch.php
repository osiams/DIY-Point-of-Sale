<?php
class fetch extends main{
	public function __construct(){
		parent::__construct();
	}
	public function run():void{
		$a=["bills","sell","bill58","ret","it","product","prop","form_selects","tool"];
		if(isset($_POST["a"])&&in_array($_POST["a"],$a)){
			require_once("php/".$_POST["a"].".php");
			if($_POST["a"]=="sell"||$_POST["a"]=="ret"||$_POST["a"]=="product"|$_POST["a"]=="tool"){
				eval("(new ".$_POST["a"]."())->fetch();");
			}else if($_POST["a"]=="bill58"){
				eval("(new ".$_POST["a"]."())->fetch();");
			}else if($_POST["a"]=="prop"){
				eval("(new ".$_POST["a"]."())->fetch('".$_POST["b"]."');");
			}else if($_POST["a"]=="bills"){
				$c=["bills_in","bill_sell_delete"];
				if(isset($_POST["c"])&&in_array($_POST["c"],$c)){
					require_once("php/".$_POST["c"].".php");
					eval("(new ".$_POST["c"]."())->fetch();");
				}
			}else if($_POST["a"]=="it"){
				$c=["lot"];
				$b=["delete","select","mmm","mmmgetused"];
				if(isset($_POST["c"])&&in_array($_POST["c"],$c)){
					require_once("php/it_view.php");
					eval("(new it_view_lot())->fetch();");
				}else if(isset($_POST["b"])&&in_array($_POST["b"],$b)){
					require_once("php/it.php");
					(new it())->fetchM($_POST["b"]);
				}
			}else if($_POST["a"]=="form_selects"){
				$b=["partner","payu","product"];
				
				if(isset($_POST["b"])&&in_array($_POST["b"],$b)){
					eval("(new form_selects(\"".$_POST["b"]."\"))->fetch();");
				}
			}
		}
	}
}
?>
