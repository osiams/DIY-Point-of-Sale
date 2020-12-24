<?php

class login extends main{
	public function __construct(){
		parent::__construct();
		$this->shop=json_decode(file_get_contents("set/shop.json"));
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
				$sql["result"]="SELECT `name`,`lastname`,`email`,`sku`,`password`,`sku_root`,`userceo`   
					FROM `user`
					WHERE `email`=".$email." LIMIT 1";
				$re=$this->metMnSql($sql,["result"]);
					
				if($re["result"]){
					if(is_array($re["data"])){
						if(count($re["data"]["result"])==1
							&&password_verify($_POST["p"],$re["data"]["result"][0]["password"])){
							$_SESSION["login"]=true;
							$_SESSION["cookie"]=false;
							$_SESSION["sku_root"]=$re["data"]["result"][0]["sku_root"];
							$_SESSION["user"]=$re["data"]["result"][0]["email"];
							$_SESSION["name"]=$re["data"]["result"][0]["name"];
							$_SESSION["lastname"]=$re["data"]["result"][0]["lastname"];
							$_SESSION["email"]=$re["data"]["result"][0]["email"];
							$_SESSION["userceo"]=$re["data"]["result"][0]["userceo"];
							$_SESSION["oa"]=CF["userceo"][$_SESSION["userceo"]]["a"];
							header('Location:index.php');
							exit;
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
		$this->pageLogin($ck,$rel);
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
		if(!isset($_COOKIE['printer_'])){
			setcookie("printer_",0,  ["expires"=>time()+(3600*24*30*12*10),"samesite" =>"Lax"]);
		}		
		$u=(isset($_POST["u"]))?$_POST["u"]:"admin@diy.pos";
		$this->home=1;
		$this->pageHead(["title"=>"เข้าสู่ระบบ DIYPOS","css"=>["login"]]);

		$nm=htmlspecialchars($this->shop->name);
		$nm=str_replace("\\","\\\\",$nm);
		$nm=str_replace('&quot;','\"',$nm);
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
				<div class="div_head">เข้าสู่ระบบ</div>
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
				</div><br />
				</form>
				</div>
				</div></div></td></tr></table>';
		$this->pageFoot();
	}
}

?>
