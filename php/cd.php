<?php
class cd extends main{
	public function __construct(){
		parent::__construct();
	}
	public function run(){
		if(isset($_GET["k"])&&preg_match("/^[0-9a-zA-Z]{16}$/",$_GET["k"])){
			echo "OK";
		}else{
			echo "NO";
		}
	}
}
