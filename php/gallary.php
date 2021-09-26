<?php
class gallary extends main{
	public function __construct(){
		parent::__construct();
		$this->title = "เพิ่มคลังรูปภาพ";
		$this->a = "gallary";
		$this->table="";
		$this->col="";
		$this->value="";
		$this->gl_key="";
		$this->url_to="";
		$this->head=[];
		$this->url_refer="";
		$this->gallery_name="";
		$this->r_more=[];
		$this->act="view";//[add,view,set1,setslideshow]
		$this->dot=["image/png"=>"png","image/gif"=>"gif","image/jpeg"=>"jpeg"];
	}
	public function run(){
		if(isset($_GET["act"])){
			$q=$_GET["act"];
			if($q=="add"||$q=="view"||$q=="aset1"||$q=="setslideshow"){
				$this->act=$_GET["act"];
			}
		}
		$this->addDir("",$this->title);
		$b=["product"];
		if(isset($_GET["b"])&&in_array($_GET["b"],$b)){
			$t=$_GET["b"];
			if($t=="product"){
				$this->table="product";
				$this->col="sku_root";
				$this->value=$_GET["sku_root"];
				if(isset($_GET["ref"])){
					$this->url_refer=$_GET["ref"];
				}else{
					$this->url_refer="?a=product";
				}
				$this->gallery_name="สินค้า";				
				$this->url_to="?a=gallary&b=product&sku_root=".$this->value."&act=view&ref=".urlencode($this->url_refer);

			}
			if($t=="product"){
				if($this->act=="add"){
					$this->pageGallaryProduct();
				}else if($this->act=="view"){
					$this->pageGallaryProductView();
				}
			}
		}else{
			$this->errorPageGallary();
		}
	}
	public function fetch(){
		$re=["result"=>false,"message_error"=>"","data"=>[]];
		$b=["product"];
		if(isset($_POST["table"])&&in_array($_POST["table"],$b)){
			$t=$_POST["table"];
			if($t=="product"){
				$this->table="product";
				$this->col="sku_root";
				$this->value=$_POST["sku_root"];
				$this->gl_key=$_POST["gl_key"];
				$file=$this->gl_key.".png";
				$pt="/^[0-9a-zA-Z]{1,}$/";
				if($this->isSKU($this->gl_key)){
					if(isset($_POST["acttype"])){
						$w=$_POST["acttype"];
						if($w=="delete"){
							$this->delImg($file);
							$this->delImgDB();
							$re["data"]=$this->gl_key;
							$re["result"]=true;
						}else if($w=="drop"){
							if(isset($_POST["gl_stat"])){
								$t=$_POST["gl_stat"];
								if($t=="0"||$t=="1"){
									$this->dontUseImgDB($t);
									$re["result"]=true;
								}
							}
						}else if($w=="primary"){
								$this->setPrimaryImgDB();
								$re["result"]=true;
						}
					}
				}else{
					$re["message_error"]='ค่าที่ส่งมามีบางอย่างผิดพลาด';
				}
			}
		}
		header('Content-type: application/json');
		echo json_encode($re);
	}
	private function delImg(string $name):void{
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
	private function delImgDB():void{
		$sku_key=$this->gl_key;
		$re=["result"=>false,"message_error"=>"","icon_name"=>""];
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:=''
		";
		$sql["set_gl"]="
			IF 1<2 THEN
				DELETE FROM `gallery` WHERE `sku_key`='".$sku_key."';
				SET @result=1;
			END IF;
		";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		//print_r($sql);
		$se=$this->metMnSql($sql,["result"]);
		//print_r($se);
	}
	private function dontUseImgDB(string $gl_stat):void{
		$sku_key=$this->gl_key;
		$re=["result"=>false,"message_error"=>"","icon_name"=>""];
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:=''
		";
		$sql["set_gl"]="
			IF 1<2 THEN
				UPDATE `gallery` SET `gl_stat`='".$gl_stat."' WHERE `sku_key`='".$sku_key."';
				SET @result=1;
			END IF;
		";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`";
		//print_r($sql);
		$se=$this->metMnSql($sql,["result"]);
		//print_r($se);
	}
	private function getHeadDataProduct():array{
		$re=[];
		$value=$this->getStringSqlSet($this->value);
		$sql=[];
		$sql["get"]="
			SELECT `name`,`barcode`,IFNULL(`icon`,'') AS `icon` FROM `".$this->table."` WHERE `".$this->col."`=".$value.";
		";
		$se=$this->metMnSql($sql,["get"]);
		
		if($se["result"]){
			if(count($se["data"]["get"])==1){
				$re=$se["data"]["get"][0];
			}
		}
		return $re;
	}
	private function pageGallaryProduct():void{
		$this->head=$this->getHeadDataProduct();
		if(count($this->head)==0){
			$this->errorPageGallary();
		}else{
			//gallarySave(table,col,data,url_to,url_key){
			$head=$this->rmoreHead($this->head);
			$this->setRMore($head);
			$this->r_more["active"]=$this->act;
			$this->addDir($this->url_refer,$this->gallery_name);
			$this->addDir("?a=gallary&amp;b=product&amp;sku_root=".$this->value,$this->head["name"]);
			$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["form_selects.css","fileupload"],"js"=>["form_selects","Fsl","fileupload","Ful","gallery","Gl"],"run"=>["Fsl","Ful"],"r_more"=>$this->r_more]);
			echo '
				<div class="content_rmore">
					<h1>'.$this->title.'</h1>
					<h2>สินค้า : '.htmlspecialchars($this->head["name"]).' ('.$this->head["barcode"].')</h2>
					<div>
							<div>
								<div id="div_fileuploadpre" class="fileuploadpres"></div>
								<input id="upload_pic" type="file" accept="image/png,image/gif,image/jpeg,image/webp" class="fuif" name="picture" onchange="Ful.fileUploadShow(event,20,Gl.icon,1024,160)" />
								<label for="upload_pic"  class="fubs">+เลือกรูปภาพ</label>
							</div>
						</div>	
						<br /><br /><input type="button" onclick="Gl.gallarySave(\''.$this->table.'\',\''.$this->col.'\',\''.$this->value.'\',\''.$this->url_to.'\')" value="บันทึกเพิ่ม" />
						<script type="text/javascript"></script>
					</div>
				</div>';
			$this->pageFoot();
		}
	}
	private function rmoreHead(array $head):string{
		$ig="";
		if($head["icon"]!=""){
			$ig='<img class="gallary_logor" src="img/gallery/64x64_'.$head["icon"].'.png"><br />';
		}
		$re=$ig.''.htmlspecialchars($head["name"]).' <br /> '.$head["barcode"].'';
		return $re;
	}
	private function errorPageGallary():void{
		$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["tool"],"js"=>["tool","Tl"],"run"=>["Tl"]]);
		echo '<main>
			
			<div class="content">
			<h1>เกิดข้อผิดพลาด ค่าที่ส่งมา </h1>

			</div>
		</main>';
		$this->pageFoot();
	}
	private function setRMore(string $head):void{
		$url="?a=gallary&amp;b=".$this->table."&amp;".$this->col."=".$this->value."&amp;ref=".$this->url_refer;
		$data=[
			"menu"=>[
				["b"=>"","name"=>$head,"link"=>"","topic"=>1],
				["b"=>"view","name"=>"ดูรปภาพทั้งหมด","link"=>$url."&amp;act=view"],
				["b"=>"add","name"=>"เพิ่มรูปภาพ","link"=>$url."&amp;act=add"],
				["b"=>"set1","name"=>"ตั้งรูปภาพหลัก 1 รูป","link"=>$url."&ampact=set1"],	
				["b"=>"setslideshow","name"=>"ตั้งรูปภาพตัวอย่าง หลายรูป","link"=>$url."&amp;act=setslideshow"],	
			],
			"active"=>""
		];
		$this->r_more=$data;
	}
	private function pageGallaryProductView():void{
		$this->head=$this->getHeadDataProduct();
		$this->gl_list=$this->getListImage();
		//print_r($this->gl_list);
		if(count($this->head)==0){
			$this->errorPageGallary();
		}else{
			//gallarySave(table,col,data,url_to,url_key){
			$head=$this->rmoreHead($this->head);
			$this->setRMore($head);
			$this->r_more["active"]=$this->act;
			$this->addDir($this->url_refer,$this->gallery_name);
			$this->addDir("?a=gallary&amp;b=product&amp;sku_root=".$this->value,$this->head["name"]);
			$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["gallary","form_selects.css","fileupload"],"js"=>["gallary","Ga","form_selects","Fsl","fileupload","Ful","gallery","Gl"],"run"=>["Ga","Fsl","Ful"],"r_more"=>$this->r_more]);

			$this->writeJsData($this->gl_list);
			echo '
				<div class="content_rmore">
					<h1>รูปภาพทั้งหมด</h1>
					<h2>สินค้า : '.htmlspecialchars($this->head["name"]).' ('.$this->head["barcode"].')</h2>';
			echo '<hr>';
			echo '<p class="gallary_p_use">รูปภาพหลัก</p>';
			if($this->gl_list["icon"]!=""){
				$fn='img/gallery/256x256_'.$this->gl_list["icon"].'.png';
				echo '<br/><div class="c"><img src="'.$fn.'" /></div>';
			}else{
				echo '<p class="c">ยังไม่มีรูปหลัก หรือยังไม่ได้ตั้งค่า</p>';
			}
			echo '<div><img src="" /></div>';
			echo '<hr>';
			echo '<p class="gallary_p_use">รูปภาพที่ใช้งาน</p>';
			echo '<div class="gallary">';
			for($i=0;$i<count($this->gl_list["stat_1"]);$i++){
				$et=$this->dot[$this->gl_list["stat_1"][$i]["mime_type"]];
				echo '<div id="IMG_'.$this->gl_list["stat_1"][$i]["sku_key"].'"><div onclick="Ga.viewImg(this)" style="background-image:url(&quot;img/gallery/'.$this->gl_list["stat_1"][$i]["sku_key"].'.'.$et.'&quot;)">
						<div onclick="Ga.setImg(this,\''.$this->table.'\',\''.$this->value.'\',\''.$this->gl_list["stat_1"][$i]["sku_key"].'\',\''.$this->gl_list["stat_1"][$i]["gl_stat"].'\',glm)">⚙️</div>
					</div>
					</div>';
			}
			echo '	</div>';
			echo '<hr>';
			echo '<p class="gallary_p_use">รูปภาพที่ไม่ใช้งาน</p>';
			echo '<div class="gallary">';
			for($i=0;$i<count($this->gl_list["stat_0"]);$i++){
				$et=$this->dot[$this->gl_list["stat_0"][$i]["mime_type"]];
				echo '<div id="IMG_'.$this->gl_list["stat_0"][$i]["sku_key"].'"><div onclick="Ga.viewImg(this)" style="background-image:url(&quot;img/gallery/'.$this->gl_list["stat_0"][$i]["sku_key"].'.'.$et.'&quot;)">
						<div onclick="Ga.setImg(this,\''.$this->table.'\',\''.$this->value.'\',\''.$this->gl_list["stat_0"][$i]["sku_key"].'\',\''.$this->gl_list["stat_0"][$i]["gl_stat"].'\',glm)">⚙️</div>
					</div>
					</div>';
			}
			echo '	</div>';
			echo '	</div>';
			$this->pageFoot();
		}
	}
	private function writeJsData(array $d):void{
		$br="\n";
		echo '<script type="text/javascript">';
		echo 'let glm={}';
		for($i=0;$i<count($this->gl_list["stat_1"]);$i++){
				$u=str_replace("\\","\\\\",$this->gl_list["stat_1"][$i]["user"]);
				$u=str_replace("\"","\\\"",$u);
				echo $br.'glm["'.$this->gl_list["stat_1"][$i]["sku_key"].'"]={"size":'.$this->gl_list["stat_1"][$i]["size"].',"width":'.$this->gl_list["stat_1"][$i]["width"].',"height":'.$this->gl_list["stat_1"][$i]["height"].',"date_reg":"'.$this->gl_list["stat_1"][$i]["date_reg"].'","by":"'.$u.'"}';
		}
		for($i=0;$i<count($this->gl_list["stat_0"]);$i++){
				$u=str_replace("\\","\\\\",$this->gl_list["stat_0"][$i]["user"]);
				$u=str_replace("\"","\\\"",$u);
				echo $br.'glm["'.$this->gl_list["stat_0"][$i]["sku_key"].'"]={"size":'.$this->gl_list["stat_0"][$i]["size"].',"width":'.$this->gl_list["stat_0"][$i]["width"].',"height":'.$this->gl_list["stat_0"][$i]["height"].',"date_reg":"'.$this->gl_list["stat_0"][$i]["date_reg"].'","by":"'.$u.'"}';
		}
		echo '</script>';
	}
	private function getListImage():array{
		$re=["icon"=>"","stat_1"=>[],"stat_0"=>[]];
		$value=$this->getStringSqlSet($this->value);
		$sql=[];
		$sql["primary"]="SELECT IFNULL(`icon`,'') AS `icon` FROM `product` WHERE `".$this->col."`='".$this->value."'";
		$sql["get_1"]="
			SELECT `gallery`.`sku_key`,`gallery`.`mime_type`,`gallery`.`gl_stat`,
				`gallery`.`size`,`gallery`.`width`,`gallery`.`height`,`gallery`.`date_reg` ,
			CONCAT(`user`.`name`, ' ',`user`.`lastname`) AS `user`
			FROM `gallery` 
			LEFT JOIN `user`
			ON(`gallery`.`user`=`user`.`sku_root`)
			WHERE `gallery`.`gl_key`=".$value." AND `gallery`.`gl_stat`='1' ORDER BY `gallery`.`date_reg` DESC;
		";
		$sql["get_0"]="
			SELECT `gallery`.`sku_key`,`gallery`.`mime_type`,`gallery`.`gl_stat`,
				`gallery`.`size`,`gallery`.`width`,`gallery`.`height`,`gallery`.`date_reg` ,
			CONCAT(`user`.`name`, ' ',`user`.`lastname`) AS `user`
			FROM `gallery` 
			LEFT JOIN `user`
			ON(`gallery`.`user`=`user`.`sku_root`)
			WHERE `gallery`.`gl_key`=".$value." AND `gallery`.`gl_stat`='0' ORDER BY `gallery`.`date_reg` DESC;
		";
		$se=$this->metMnSql($sql,["primary","get_1","get_0"]);
		
		if($se["result"]){
			if(count($se["data"]["get_1"])>0){
				$re["stat_1"]=$se["data"]["get_1"];
			}
			if(count($se["data"]["get_0"])>0){
				$re["stat_0"]=$se["data"]["get_0"];
			}
			if(count($se["data"]["primary"])>0){
				$re["icon"]=$se["data"]["primary"][0]["icon"];
			}
		}
		//print_r($re);
		return $re;
	}
	private function setPrimaryImgDB():void{
		$sql=[];
		$sql["set"]="BEGIN NOT ATOMIC
			UPDATE `gallery` SET `primary`='0' WHERE `primary`='1';
			UPDATE `gallery` SET `primary`='1' WHERE `sku_key`='".$this->gl_key."';
			UPDATE `product` SET `icon`='".$this->gl_key."' WHERE `sku_root`='".$this->value."';
			CALL CPT_('product','product_ref','sku_root',0,'\"".$this->value."\"',@error);
		END;	";
		$se=$this->metMnSql($sql,["set"]);
		//print_r($sql);
	}
}
