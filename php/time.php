<?php
class time extends main{
	public function __construct(){
		parent::__construct();
		$this->title = "กะทำงาน";
		$this->a = "time";
		$this->addDir("?a=".$this->a,$this->title);
	}
	public function run(){
		//$a=$this->checkTime();
		$b=["regis","logout"];
		if(isset($_POST["b"])&&in_array($_POST["b"],$b)){
			$t=$_POST["b"];
			if($t=="regis"){
				$this->newTimeRegisPage();
			}else if($t=="logout"){
				require_once("php/login.php");
				(new login)->logout();
			}
		}else{
			$this->defaultTimePage();
		}
	}
	public function fetch(){
		$p=["pos","drawers"];
		if(isset($_POST["b"])&&in_array($_POST["b"],$p)){
			$t=$_POST["b"];
			if(1==1){
				require("php/device_".$t.".php");
				eval("(new device_".$t."())->fetch();");
			}
		}else{
			header('Content-type: application/json');
			print_r("{}");
		}		
	}
	private function newTimeRegisPage():void{
		$c=$this->checkPOS();
		if($c["result"]){
			$_SESSION["onoff"]=1;
			$this->viewTimeMe();
		}else{
			if($c["error_code"]=="01"){
				$_SESSION["time_stat"]="device_regis";
				$this->defaultTimePage();
			}
		}
		
		/*$this->addDir("","เริ่มกะทำงานใหม่");
		$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["Time"],"js"=>["Time","Ti"],"run"=>[]]);
		
		$this->pageFoot();*/
	}
	private function checkPOS():array{
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@error_code:='0',
			@count_ip:=(SELECT COUNT(*) AS `count`  FROM `device_pos` 
					WHERE `ip`='".$_SESSION["ip"]."');
		";
		$sql["check"]="
			IF @count_ip = 0  THEN 
				SET @message_error='อุปกรณ์ หมายเลข IP นี้ ยังไม่มีในระบบ';
				SET @error_code='01';
			END IF;			
		";
		$sql["run"]="
			IF LENGTH(@message_error) = 0 THEN
				SET @result=1;
			END IF;
		";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@error_code AS `error_code`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($se);exit;
		return $se["data"]["result"][0];
	}
	private function defaultTimePage():void{
		if(!isset($_SESSION["time_stat"])){
			$a=$this->checkTime();
			$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["time"],"js"=>["time","Ti"],"run"=>["Ti"]]);
			if($a["is_on"]==0&&$a["count_user"]==0){
				echo '<div class="content">
					<div class="form">
						<h2>'.$this->title.'</h2>
						<form name="time" method="post" action="?a=time">
							<input type="hidden" name="b" value="" />
						
							<div class="c">
								<input type="button" value="เริ่มกะทำงานใหม่" onclick="Ti.newTimeSubmit()" />
								<input type="button" value="ออกจากระบบ" onclick="Ti.logout()" />
							</div>
						</form>
					</div>
				</div>';
				
			}
		}else{
			if($_SESSION["time_stat"]=="device_regis"){
				if(isset($_GET["a"])&&$_GET["a"]=="device"/*||isset($_POST["a"])&&$_POST["a"]=="device"*/){
					$_POST["b"]="pos";
					$_POST["c"]="regis";
					require_once("php/device.php");
					(new device)->run();				
				}else{
					$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["time"],"js"=>["time","Ti"],"run"=>["Ti"]]);
					echo '<div class="content">
						<div class="form">
							<br />
							<div class="error">อุปกรณ์ หมายเลข IP '.$_SESSION["ip"].' นี้ ยังไม่มีในระบบ</div>
							<br />
							<input type="button" value="ลงทะเบียนอุปกรณ์" onclick="location.href=\'?a=device\'" />
						</div>
					</div>';
					$this->pageFoot();
				}
			}else if($_SESSION["time_stat"]=="view_me"){
				unset($_SESSION["time_stat"]);
				$_SESSION["onoff"]=1;
				$this->viewTimeMe();
			}
		}
		
	}
	private function viewTimeMe(){
		header('Location:?a=me');
		exit;
	}
	private function timeRegis():array{
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
		";
		$sql["run"]="BEGIN NOT ATOMIC 
			INSERT INTO `time`
		END";
	}
	protected function checkTime():array{
		$re=["count_user"=>0,"is_on"=>0,"get_user"=>[]];
		$sql=[];
		$sql["count_user"]="SELECT COUNT(*) AS `count`  FROM `device_pos` 
					WHERE `user`='".$_SESSION["sku_root"]."' AND `onoff`=1
		";
		$sql["is_on"]="SELECT COUNT(*) AS `count`  FROM `device_pos` 
					WHERE `ip`='".$_SESSION["ip"]."' AND `onoff`=1
		";
		$sql["get_user"]="SELECT `id`,`name`,`sku`,`user`,`ip`
			FROM `device_pos` 
			WHERE `user`='".$_SESSION["sku_root"]."' AND `onoff`=1 AND `ip`='".$_SESSION["ip"]."'
		";
		$se=$this->metMnSql($sql,["count_user","is_on","get_user"]);
		if($se["result"]){
			$re["count_user"]=$se["data"]["count_user"][0]["count"];
			$re["is_on"]=$se["data"]["is_on"][0]["count"];
			if(isset($se["data"]["get_user"][0])){
				$re["get_user"]=$se["data"]["get_user"][0];
			}
			//print_r($re);
		}
		return $re;
	}
	protected function getLastTime():array{
		$sql=[];
		$sql="SELECT `ip` FROM `time`";
	}
	protected function propToFromValue(string $prop):string{
		$t=implode(",,",json_decode($prop));
		$t=(strlen(trim($t))>0)?",".$t.",":"";
		return $t;
	}
}
