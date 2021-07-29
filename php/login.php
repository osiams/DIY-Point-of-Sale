<?php
require("library/barcode-master/barcode.php");
class login extends main{
	public function __construct(){
		parent::__construct();
		$this->shop=json_decode(file_get_contents("set/shop.json"));
		$this->about=json_decode(file_get_contents("set/about.json"));
	}
	public function run():void{
		$login=false;
		$ck=NULL;
		$rel=NULL;
		if(isset($_POST["u"])&&isset($_POST["p"])){
			$ck=$this->checkFormLogin($_POST["u"],$_POST["p"]);
			if($ck["user"]&&$ck["password"]){
				$email=$this->getStringSqlSet($_POST["u"]);
				$pw=password_hash($_POST["p"], PASSWORD_DEFAULT);
				$password=$this->getStringSqlSet($pw);
				$sql=[];
				$sql["result"]="SELECT `id`,`name`,`lastname`,`email`,`sku`,`password`,`sku_root`,`userceo`   
					FROM `user`
					WHERE `email`=".$email." LIMIT 1";
				$re=$this->metMnSql($sql,["result"]);
					
				if($re["result"]){
					if(is_array($re["data"])){
						if(count($re["data"]["result"])==1
							&&password_verify($_POST["p"],$re["data"]["result"][0]["password"])){
							/*$s=$this->findIPv4();
							$c=$this->userIPv4();
							if(($s==$c&&$re["data"]["result"][0]["userceo"]>=8)||($s!=$c&&$re["data"]["result"][0]["userceo"]<8)){
							*/	$_SESSION["login"]=true;
								$_SESSION["cookie"]=false;
								$_SESSION["sku_root"]=$re["data"]["result"][0]["sku_root"];
								$_SESSION["id"]=$re["data"]["result"][0]["id"];
								$_SESSION["user"]=$re["data"]["result"][0]["email"];
								$_SESSION["name"]=$re["data"]["result"][0]["name"];
								$_SESSION["lastname"]=$re["data"]["result"][0]["lastname"];
								$_SESSION["email"]=$re["data"]["result"][0]["email"];
								$_SESSION["userceo"]=$re["data"]["result"][0]["userceo"];
								$_SESSION["oa"]=CF["userceo"][$_SESSION["userceo"]]["a"];
								$_SESSION["ip"]=$this->userIPv4();
								$_SESSION["onoff"]=0;
								$_SESSION["time_id"]=0;
								header('Location:index.php?a=time');
								exit;
							/*}else if($s==$c&&$re["data"]["result"][0]["userceo"]<8){
								$rel=array("message_error"=>"ระดับผู้ใช้ของคุณ ไม่สามารถเข้าใช้งานเครื่องนี้ได้ ");
							}*/
						}else{
						$rel=array("message_error"=>"ไม่พบผู้ใช้ และ หรือ รหัส ที่รับมา");
						}
					}else{
						$rel=array("message_error"=>"ไม่พบผู้ใช้ และ หรือ รหัส ที่รับมา");
					}
				}else{
					$rel=array("message_error"=>$re["message_error"]);
				}
			}
		}
		if(isset($_GET["v"])&&$_GET["v"]=="vqrc"){
			$this->qrc();
		}else{
			$this->pageLogin($ck,$rel);
		}
	}
	public function logout(){
		session_unset();
		header('Location:index.php');
		exit;
	}
	private function checkUserLogin(string $table,string $email,string $password):array{
		$re=array("connect"=>false,
					"connect_message"=>"",
					"result"=>false,
					"message_error"=>"",
					"data"=>NULL
		);
		return $re;
	}
	private function checkFormLogin(string $email,string $pass):array{
		$re=array("user"=>false,
			"user_error"=>"ชื่อผู้ใช้ ไม่อยู่ในรูปแบบ",
			"password"=>false,
			"password_error"=>"รหัสผ่านไม่อยู่ในรูปแบบ [a-zA-Z0-9 ] 8-32 อักขระ");
		if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$re["user"]=true;
			$re["user_error"] = "";
		}
		if(preg_match("/^[0-9a-zA-Z]{8,32}$/",$pass)) {
			$re["password"]=true;
			$re["password_error"] = "";
		}
		return $re;
	}
	private function pageLogin(array $ck=NULL,array $re=NULL):void{
		$lan=$_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']."".$_SERVER['PHP_SELF'];
		$lanphpadmin=$_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']."/phpmyadmin/";
		$lan2="http://".$this->findIPv4().":".CF["http_port"]."".$_SERVER['PHP_SELF'];
		$lan3="https://".$this->findIPv4().":".CF["https_port"]."".$_SERVER['PHP_SELF'];
		if(!isset($_COOKIE['printer_'])){
			setcookie("printer_",0,  ["expires"=>time()+(3600*24*30*12*10),"samesite" =>"Lax"]);
		}		
		$u=(isset($_POST["u"]))?$_POST["u"]:"admin@diy.pos";
		$this->home=1;
		$this->pageHead(["title"=>"เข้าสู่ระบบ DIYPOS","css"=>["login"]]);

		$nm=$this->shop->name;
		echo '<table class="login_vh"><tr class="trh"><td class="ranbg"><div><div class="rtyu">
				<div id="trb"></div>
				<script type="text/javascript">
					//M.b.classList.add("ranbg")
					let shop="'.$nm.'"
					let t=""
					for(let i=0;i<shop.length;i++){
						let x=Math.random().toString().substring(3,4)
						t=t+\'<span class="lcr\'+x+\'">\'+shop[i]+\'</span>\'
					}
					M.id("trb").innerHTML=t
				</script>
				<p class=" c pgso">
					'.htmlspecialchars($this->shop->head).'
				</p></div>';
		echo '<div class="login">
				<div class="div_head">เข้าสู่ระบบ : '.$this->findIPv4().'</div>
				<div class="div_content">';
		//--TEST
		//print_r($ck);
		//print_r($re);
		if(isset($ck)){
			if(strlen($ck["user_error"])>0){
				echo '<p class="error">'.$ck["user_error"].'</p>';
			}else if(strlen($ck["password_error"])>0){
				echo '<p class="error">'.$ck["password_error"].'</p>';
			}else if(strlen($re["message_error"])>0){
				echo '<p class="error">'.$re["message_error"].'</p>';
			}
		}
		echo '<form style="width:100%" name="lg" action="" method="post">
				<label for="login_u">ชื่อผู้ใช้</label>
				<div><input id="login_u" name="u" type="text" value="'.$u.'" /></div><br />
				<label for="login_p">รหัสผ่าน</label>
				<div><input id="login_p" name="p" type="password" value="" /></div>
				<br />
				<div class="c">
					<input type="submit" value="เข้าสู่ระบบ" />
				</div>
				<div class="r size0_8 gray">อุปกรณ์คุณ : '.$this->userIPv4().'</div>
				</form>
				</div>
				</div>
				</div>
				
					<div class="login_qrc">
						
						<div><img src="?a=login&amp;v=vqrc&amp;s=qrl&amp;d='.$lan2.'" /></div>
						<div><img src="?a=login&amp;v=vqrc&amp;s=qrl&amp;d='.$lan3.'" /></div>	
						<div class="c">http://</div><div class="c">https://</div>
					</div>	
				<div class="version"><b class="">'.$this->about->name.' Version '.$this->about->version.'</b> ('.$this->about->date.')</div>		
				</td></tr></table>
				';
		$this->pageFoot();
	}
	public function qrc(){
		$generator = new barcode_generator($_REQUEST['d'], $_REQUEST);
		$generator->output_image("png", $_REQUEST['s'], $_REQUEST['d'],"png");
	}
}

?>
