<?php
class fileupload extends main{
	public function __construct(){
		parent::__construct();
		$this->re=[
			"result"=>false,
			"data"=>[],
			"message_error"=>""
		];
	}
	public function fetch(){
		$file = "php/class/image.php";
		require($file);		
		$this->img=new image($this->gallery_dir);		
		$this->pc();
	}
	protected function pc():void{
		$t=[
			"bill_in"=>["sku"=>1]
		];
		if(isset($_POST["icon"])&&$_POST["icon"]!=""){
			if(isset($_POST["table"])){
				if(isset($_POST["key"])){
					if(isset($t[$_POST["table"]])){
						if(isset($_POST["data"])){
							if(isset($t[$_POST["table"]][$_POST["key"]])){
								$img=$this->img->imgCheck("icon");
								$key=$this->key("key",7);
								if($img["result"]){
									$a=explode("/",$img["mime"]);
									$mime=$a[1];
									$this->re["result"]=true;
									$this->re["data"]='{"mime":"'.$mime.'"}';
									$se=$this->setIconArr($_POST["table"],$_POST["key"],$_POST["data"],$key,$img,$mime);
									if($se["result"]){
										$this->re["result"]=true;
										$this->img->imgSave($img,$key);
									}else{
										$this->re["message_error"]=$se["message_error"];
									}
								}
							}
						}
					}
				}
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($this->re);
	}
	private function setIconArr(string $table,string $key,string $data,string $icon_key,array $img,string $mime=""):array{
		$re=["result"=>false,"message_error"=>""];
		$mimefull=$this->getStringSqlSet($img["mime"]);
		$md5=$this->getStringSqlSet(md5($img["file"]));
		$user=$this->getStringSqlSet($_SESSION["sku_root"]);
		$size=(int) $img["size"];
		$width=(int) $img["width"];
		$height=(int) $img["height"];
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@icon_key:='".$icon_key."',
			@sku_key:='".$icon_key."',
			@icon:='".$icon_key."".($mime!=""?".".$mime:"")."',
			@icon_json:=(SELECT IFNULL(`icon_arr`,'[]')  FROM `".$table."` WHERE `".$key."`='".$data."' );
		";
		$sql["run"]="
			IF 1<2 THEN
				SET @icon_json=JSON_ARRAY_APPEND(@icon_json, '$', @icon);
				UPDATE `".$table."` SET  
					`icon_arr`	=	@icon_json,`icon_gl`	=	@icon_json	
				WHERE `".$key."`='".$data."';
				INSERT  INTO `gallery` (
					`sku_key`		,`name`		,`a_type`		,`mime_type`		,`md5`,
					`user`			,`size`			,`width`		,`height`
				) VALUES (
					@sku_key		,\"".$data."\"		,'billin'		,".$mimefull."				,".$md5.",
					".$user."		,".$size."		,".$width."		,".$height."
				);
				SET @result=1;
			END IF;
		";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($se);
		if($se["result"]){
			$re["result"]=$se["data"]["result"][0]["result"];
			$re["message_error"]=$se["data"]["result"][0]["message_error"];
		}
		return $re;
		/*	INSERT  INTO `gallery` (
				`sku_key`		,`name`		,`a_type`		,`mime_type`		,`md5`,
				`user`			,`size`			,`width`		,`height`
			) VALUES (
				".$sku_key."	,".$name."		,'payu'		,".$mimefull."				,".$md5.",
				".$user."		,".$size."		,".$width."		,".$height."
			);	*/	
	}
}
