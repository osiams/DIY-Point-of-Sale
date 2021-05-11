<?php
class tran extends main{
	public function __construct(){
		parent::__construct();
		$this->title="เงินสดเข้า-ออก";
	}
	public function run(){
		$this->addDir("?a=bills",htmlspecialchars($this->title));
	}
	public function fetch(){
		$re=["result"=>false,"message_error"=>"","data"=>[]];
		if(isset($_POST["b"])){
			if($_POST["b"]=="savetran_min"){
				$se=$this->checkSet("tran",["post"=>["min","ref","note"]],"post");
				$min=(float) $_POST["min"];
				if($se["result"]){
					if($min<=0){
						$se["result"]=false;
						$se["message_error"]="เงินทีใส่ลิ้นชักต้อง > 0";
					}
				}
				if($se["result"]){
					$te=$this->fetchSaveTran();
					if($te["result"]){
						$re["result"]=true;
						$re["data"]=$te["data"];
					}else{
						$re["message_error"]=$te["message_error"];
					}
				}else{
					$re["message_error"]=$se["message_error"];
				}
			}else if($_POST["b"]=="savetran_mout"){
				echo "ffff";
			}
		}
		header('Content-type: application/json');
		echo json_encode($re);
	}
	private function fetchSaveTran():array{
		$re=["result"=>false,"message_error"=>"","data"=>["money_balance"=>0]];
		$min=(float) $_POST["min"];
		$ref=$this->getStringSqlSet(trim($_POST["ref"]));
		$note=$this->getStringSqlSet($_POST["note"]);
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@min:=".$min.",
			@ref:=".$ref.",
			@note:=".$note.",
			@user:='".$_SESSION["sku_root"]."',
			@ip:='".$_SESSION["ip"]."',
			@last_id:=0,
			@tran_type:='min',
			@note:=".$note.",
			@money_balance:=(SELECT IFNULL(`money_balance`,0) FROM `device_pos` WHERE `ip`='".$_SESSION["ip"]."' AND `user` = '".$_SESSION["sku_root"]."' AND `onoff` = '1'),
			@drawers_id:=(SELECT `drawers_id` FROM `device_pos` WHERE `ip`='".$_SESSION["ip"]."' AND `user` = '".$_SESSION["sku_root"]."' AND `onoff` = '1'),
			@count_ref:=(SELECT COUNT(`id`)  FROM `ref` WHERE `sku_key`=".$ref." AND `sku_key` IS NOT NULL);
		";
		$sql["check"]="
			IF @count_ref = 0 AND @ref !='NULL' THEN 
				SET @message_error=CONCAT('ไม่พบรหัสอ้างอิง ',@ref) ;
			END IF;			
		";
		$sql["run"]="
			IF LENGTH(@message_error) = 0 THEN
				INSERT INTO `tran` (
					`tran_type`	,`ref`	,`drawers_id`	,`min`	,`user`	,`ip`	,`note`
				)VALUES(
					@tran_type	,@ref	,IFNULL(@drawers_id,NULL)	,@min	,@user	,@ip ,@note
				);
				SET @last_id=(SELECT LAST_INSERT_ID());
				IF @last_id > 0 THEN
					UPDATE `tran` SET `money_balance`=(@money_balance+`min`)
						WHERE `id`=@last_id;
					SET @money_balance=(SELECT `money_balance` FROM `tran` WHERE `id` = @last_id);
					UPDATE `device_pos` SET `money_balance` = @money_balance WHERE `ip`='".$_SESSION["ip"]."';
					SET @result=1;
				END IF;
			END IF;
		";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@money_balance AS `money_balance`,@last_id";
		$se=$this->metMnSql($sql,["result"]);
		if($se["result"]){
			if(isset($se["data"]["result"][0]["result"])){
				if($se["data"]["result"][0]["result"]){
					$re["result"]=true;
					$re["data"]["money_balance"]=(float) $se["data"]["result"][0]["money_balance"];
				}else{
					$re["message_error"]=$se["data"]["result"][0]["message_error"];
				}
			}
		}else{
			$re["message_error"]=$se["message_error"];
		}
		//print_r($se);
		return $re;
	}
}
?>
