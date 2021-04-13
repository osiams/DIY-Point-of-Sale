<?php
class fileupload extends main{
	public function __construct(){
		parent::__construct();
		$this->re=[
			"result"=>false,
			"data"=>[],
			"message_error"=>""
		];
		$this->max_squar=["bill_in"=>256,];
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
									$max=(isset($this->max_squar[$_POST["table"]]))?$this->max_squar[$_POST["table"]]:256;
									if(isset($_POST["uploadtype"])&&$_POST["uploadtype"]=="new"){
										$se=$this->setIconArr($_POST["table"],$_POST["key"],$_POST["data"],$key,$img,$mime);
										if($se["result"]){
											$this->re["result"]=true;
											$this->img->imgSave($img,$key,$max);
										}else{
											$this->re["message_error"]=$se["message_error"];
										}
									}else if(isset($_POST["uploadtype"])&&$_POST["uploadtype"]=="add"){
										$se=$this->addIconGl($_POST["table"],$_POST["key"],$_POST["data"],$key,$img,$mime);
										if($se["result"]){
											$this->re["result"]=true;
											$this->img->imgSave($img,$key,$max);
											$this->re["icon_name"]=$se["icon_name"];
										}else{
											$this->re["message_error"]=$se["message_error"];
										}
									}
								}else{
									if(isset($_POST["uploadtype"])&&$_POST["uploadtype"]=="delete"){
										$file=$_POST["icon"];
										$pt="/^[0-9a-zA-Z]{1,}.png$/";
										if(preg_match($pt,$file)){
											$this->delImgs($file);
											$this->delIconGl($_POST["table"],$_POST["key"],$_POST["data"],$file);
											$this->re["icon_name"]=$file;
											$this->re["result"]=true;
										}
									}else{
										$this->re["message_error"]=$img["message_error"];
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
	private function delIconGl(string $table,string $key,string $data,string $file){
		$a=explode(".",$file);
		$sku_key=trim($a[0]);
		$re=["result"=>false,"message_error"=>"","icon_name"=>""];
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@icon_arr_json:=(SELECT IFNULL(`icon_arr`,'[]')  FROM `".$table."` WHERE `".$key."`='".$data."' ),
			@icon_gl_json:=(SELECT IFNULL(`icon_gl`,'[]')  FROM `".$table."` WHERE `".$key."`='".$data."' ),
			@js_key:='',
			@icon_json:=''
		";
		$sql["set_gl"]="
			IF 1<2 THEN
				SET @js_key=(SELECT JSON_SEARCH(@icon_gl_json,'one','".$file."'));
				SET @icon_json=(SELECT JSON_REMOVE(@icon_gl_json,JSON_UNQUOTE(@js_key)));
				UPDATE `".$table."`
					SET  `icon_gl`	=	@icon_json
					WHERE `".$key."`='".$data."' AND @js_key IS NOT NULL;
				SET @js_key=(SELECT JSON_SEARCH(@icon_arr_json,'one','".$file."'));
				SET @icon_json=(SELECT JSON_REMOVE(@icon_arr_json,JSON_UNQUOTE(@js_key)));
				UPDATE `".$table."`
					SET  `icon_arr`	=	@icon_json
					WHERE `".$key."`='".$data."' AND @js_key IS NOT NULL;
				DELETE FROM `gallery` WHERE `sku_key`='".$sku_key."';
			END IF;
		";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		//print_r($sql);
		$se=$this->metMnSql($sql,["result"]);
	}
	private function delImgs(string $name):void{
		$sq=[16,32,64,128,256,512,1024];
		$file=$this->gallery_dir."/".$name;
		if(file_exists($file)){
			unlink($file);
		}
		for($i=0;$i<count($sq);$i++){
			$file=$this->gallery_dir."/".$sq[$i]."x".$sq[$i]."_".$name;
			if(file_exists($file)){
				unlink($file);
			}
		}
	}
	private function addIconGl(string $table,string $key,string $data,string $icon_key,array $img,string $mime=""):array{
		$re=["result"=>false,"message_error"=>"","icon_name"=>""];
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
			@icon_json:=(SELECT IFNULL(`icon_gl`,'[]')  FROM `".$table."` WHERE `".$key."`='".$data."' );
		";
		$sql["run"]="
			IF 1<2 THEN
				SET @icon_json=JSON_ARRAY_APPEND(@icon_json, '$', @icon);
				UPDATE `".$table."` SET  
					`icon_gl`	=	@icon_json	
				WHERE `".$key."`='".$data."';
				INSERT  INTO `gallery` (
					`sku_key`		,`gl_key`		,`name`		,`a_type`		,`mime_type`		,`md5`,
					`user`			,`size`			,`width`		,`height`
				) VALUES (
					@sku_key		,\"".$data."\"	,\"".$data."\"		,'billin'		,".$mimefull."				,".$md5.",
					".$user."		,".$size."		,".$width."		,".$height."
				);
				SET @result=1;
			END IF;
		";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@icon AS `icon_name`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($se);
		if($se["result"]){
			$re["result"]=$se["data"]["result"][0]["result"];
			$re["message_error"]=$se["data"]["result"][0]["message_error"];
			$re["icon_name"]=$se["data"]["result"][0]["icon_name"];
		}
		return $re;
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
					`sku_key`		,`gl_key`		,`name`		,`a_type`		,`mime_type`		,`md5`,
					`user`			,`size`			,`width`		,`height`
				) VALUES (
					@sku_key		,\"".$data."\"	,\"".$data."\"		,'billin'		,".$mimefull."				,".$md5.",
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
	}
}
