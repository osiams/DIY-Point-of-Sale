<?php
class device_drawers extends device{
	public function __construct(){
		parent::__construct();
		$this->a = "device";
		$this->title = "ลิ้นชักเก็บเงิน";
		$this->per=10;
		$this->page=1;
		$this->txsearch="";
		$this->fl="";
		$this->lid=0;
		$this->sh="";
		$this->max_squar=256;
	}
	public function run(){
		$q=["regis","edit","delete","details"];
		$this->addDir("?a=device&amp;b=drawers",$this->title);
		if(isset($_GET["c"])&&in_array($_GET["c"],$q)){
			$t=$_GET["c"];
			if($t=="regis"){
				$this->regisDrawersPage();
			}else if($t=="edit"){
				$gall = "php/gallery.php";
				require($gall);	
				$this->editPage();
			}else if($t=="delete"){
				$this->deleteDrawers();
			}else if($t=="details"){
				if(isset($_GET["sku"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["sku"])){
					require("php/device_drawers_details.php");
					(new device_drawers_details($_GET["sku"]))->run();
				}else{
					$this->pageDrawers();
				}
			}
		}else{
			$this->pageDrawers();
		}
	}
	private function deleteDrawers():void{
		if(isset($_POST["sku"])){
			$sku=$_POST["sku"];
			$url_refer=(isset($_GET["url_refer"]))?$_GET["url_refer"]:"";
			$sql=[];
			$sql["set"]="SELECT @result:=0,
				@message_error:='',
				@sku:='".$sku."',
				@icon_gl:=(SELECT icon_gl FROM `device_drawers` WHERE `sku`=@sku LIMIT 1)
			";
			
			$sql["del"]="DELETE FROM `device_drawers` WHERE `sku`=@sku";
			$sql["del_img"]="IF 1=1 THEN 
				DELETE FROM `gallery` WHERE `a_type`='device_drawers' AND `gl_key`=@sku;
				SET @result=1;
			END IF";
			$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@sku AS `sku`,@icon_gl AS `icon_gl`";
			
			$se = $this->metMnSql($sql,["result"]);
			//print_r($se);exit;
			if($se["result"]&&$se["data"]["result"][0]["result"]==1){
				$files=json_decode($se["data"]["result"][0]["icon_gl"]);
				if(!is_array($files)){
					$files=[];
				}
				$this->delImgs($files);
			}
			//print_r($re);exit;
			$pt="/&ed=[0-9a-zA-Z-+\.&\/]{1,25}/";
			$pr='';
			$url=preg_replace($pt, $pr,$url_refer)."&ed=".$_POST["sku"];
			header('Location:'.$url);
		}
	}
	public function fetch(){
		$p=["regis","edit"];
		if(isset($_POST["c"])&&in_array($_POST["c"],$p)){
			$t=$_POST["c"];
			if($t=="regis"){
				$this->fetchDrawersRegis();
			}else if($t=="edit"){
				$this->fetchDrawersEdit();
			}
		}else{
			header('Content-type: application/json');
			print_r("{}");
		}		
	}
	private function fetchDrawersEdit():void{
		$error="";
		if(isset($_POST["submith"])&&$_POST["submith"]=="clicksubmit"){
			$se=$this->checkSet("device_drawers",["post"=>["id","name","sku","no","disc"]],"post");
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				$qe=$this->fetchDrawersUpdate();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					header('Content-type: application/json');
					$d=["result"=>true,"message_error"=>"","data"=>["id"=>$qe["data"]["result"][0]["id"]]];
					echo json_encode($d,true);
				}
			}
			if($error!=""){
				$this->fetchDrawersPage($error);
			}
		}else{
			$this->fetchDrawersPage($error);
		}
	}
	private function fetchDrawersUpdate():array{
		$id=trim($_POST["id"]);
		$disc=$this->getStringSqlSet($_POST["disc"]);
		$name=$this->getStringSqlSet($_POST["name"]);
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$no=$this->getStringSqlSet($_POST["no"]);
		$sku_root=$this->getStringSqlSet($_SESSION["sku_root"]);
		$icon_arr=$this->setPropR($this->getStringSqlSet($_POST["gallery_list"]));
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@id:=".$id.",
			@sku:=".$sku.",
			@name:=".$name.",
			@no:=".$no.",
			@disc:=".$disc.",
			@count_name:=(SELECT COUNT(`id`)  FROM `device_drawers` WHERE `name`=".$name."),
			@count_sku:=(SELECT COUNT(`id`)  FROM `device_drawers` WHERE `sku`=".$name." AND `id` != @id),
			@icon_arr:=".$this->getStringSqlSet(json_encode($icon_arr)).",
			@user:=(SELECT `sku_key`  FROM `user` WHERE `sku_root`=".$sku_root." LIMIT 1);
		";
		$sql["check"]="
			IF @count_name > 0 AND @count_lastname > 0 THEN 
				SET @message_error='เกิดขอผิดพลาด ชื่อ ที่ส่งมา มีแล้ว โปรดลองชื่ออื่น';
			ELSEIF @count_sku > 0 THEN
				SET @message_error='เกิดขอผิดพลาด รหัสภายในที่ส่งมา มีแล้ว โปรดลอง รหัสภายในอื่น';
			END IF;			
		";
		$sql["run"]="BEGIN NOT ATOMIC 
			IF LENGTH(@message_error) = 0 THEN
				UPDATE `device_drawers`  SET
						`sku`=@sku,	`name`=@name		,`no`=@no	,`disc`=@disc,`icon_arr`=JSON_UNQUOTE(@icon_arr)
					WHERE `id`=@id;
				SET @result=1;
			END IF;
		END;";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@id AS `id`";
		$se=$this->metMnSql($sql,["result"]);
		$se=$this->metMnSql($sql,["result"]);
		//print_r($se);
		return $se;
	}
	private function fetchDrawersRegis():void{
		$error="";
		if(isset($_POST["submith"])&&$_POST["submith"]=="clicksubmit"){
			$se=$this->checkSet("device_drawers",["post"=>["name","sku","no","disc"]],"post");
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				$qe=$this->fetchDrawersInsert();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["data"]["result"][0]["result"]==0){
					$error=$qe["data"]["result"][0]["message_error"];
				}else if($qe["data"]["result"][0]["result"]==1){
					header('Content-type: application/json');
					$d=["result"=>true,"message_error"=>"","data"=>["sku"=>$qe["data"]["result"][0]["sku"]]];
					echo json_encode($d,true);
				}
			}
			if($error!=""){
				$this->fetchDrawersPage($error);
			}
		}else{
			$this->fetchDrawersPage($error);
		}
	}
	private function fetchDrawersInsert():array{
		$disc=$this->getStringSqlSet($_POST["disc"]);
		$name=$this->getStringSqlSet($_POST["name"]);
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$no=$this->getStringSqlSet($_POST["no"]);
		$sku_root=$this->getStringSqlSet($_SESSION["sku_root"]);
		$ip=$this->getStringSqlSet($this->userIPv4());
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@sku:=".$sku.",
			@name:=".$name.",
			@no:=".$no.",
			@disc:=".$disc.",
			@count_name:=(SELECT COUNT(`id`)  FROM `device_drawers` WHERE `name`=".$name."),
			@count_sku:=(SELECT COUNT(`id`)  FROM `device_drawers` WHERE `sku`=".$name."),
			@user:=(SELECT `sku_key`  FROM `user` WHERE `sku_root`=".$sku_root." LIMIT 1);
		";
		$sql["check"]="
			IF @count_name > 0 AND @count_lastname > 0 THEN 
				SET @message_error='เกิดขอผิดพลาด ชื่อ ที่ส่งมา มีแล้ว โปรดลองชื่ออื่น';
			ELSEIF @count_sku > 0 THEN
				SET @message_error='เกิดขอผิดพลาด รหัสภายในที่ส่งมา มีแล้ว โปรดลอง รหัสภายในอื่น';
			END IF;			
		";
		$sql["run"]="BEGIN NOT ATOMIC 
			IF LENGTH(@message_error) = 0 THEN
				INSERT  INTO `device_drawers`  (`sku`,`name`,`no`,`disc`) 
				VALUES(@sku,@name,@no,@disc);
				SET @result=1;
			END IF;
		END;";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@sku AS `sku`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($se);
		return $se;
	}
	private function fetchDrawersPage($error){
		$re=["result"=>false,"message_error"=>""];
		$re["message_error"]=$error;
		$js=json_encode($re);
		header('Content-type: application/json');
		echo $js;
	}
	protected function pageDrawers(){
		$this->defaultPageSearch();
		$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["device"],"js"=>["device","Dv"],"run"=>["Dv"]]);
			
			echo '<div class="content">
				<div class="form">
				<h1 class="c">'.$this->title.'</h1>
				<div class="pn_search">
					<form class="form100" name="pd_search" action="" method="get">
						<input type="hidden" name="a" value="device" />
						<input type="hidden" name="b" value="drawers" />
						<input type="hidden" name="lid" value="0" />
						<label><select id="product_search_fl" name="fl">
							<option value="sku"'.(($this->fl=="sku")?" selected":"").'>รหัสภายใน</option>
							<option value="name"'.(($this->fl=="name")?" selected":"").'>ชื่อ</option>
						</select>
						 <input id="product_search_tx" type="text" name="tx" value="'.str_replace("\\","",htmlspecialchars($this->txsearch)).'" />
						 <input  type="submit" value="🔍" /> </label></form>
				</div>';
		$this->writeContentDrawers();		
		echo '<p class="c"><input type="button" value="ลงทะเบียน '.$this->title.'" onclick="location.href=\'?a='.$this->a.'&amp;b=drawers&amp;c=regis\'" /></p>';

		echo '</div></div>';
		$this->pageFoot();
	}
	private function defaultSearch():string{
		$fla=["sku","name"];
		$fl="name";
		$tx="";
		$se="";
		if(isset($_GET["fl"])){
			if(in_array($_GET["fl"],$fla)){
				if(($_GET["fl"]=="sku")
				&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["tx"])){
					$fl=$_GET["fl"];
				}	
			}
		}
		$this->fl=$fl;
		if(isset($_GET["tx"])&&strlen(trim($_GET["tx"]))>0){
			$tx=$this->getStringSqlSet($_GET["tx"]);
		}
		if($tx!=""){
			$tx=substr($tx,1,-1);
			$this->txsearch=$tx;
			$se=" WHERE `device_drawers`.`".$fl."` LIKE  \"%".$tx."%\"  ";
		}
		return $se;
	}
	public function defaultPageSearch():void{
		$fla=["sku","name"];
		$fl="name";
		$tx="";
		$se="";
		if(isset($_GET["fl"])){
			if(in_array($_GET["fl"],$fla)){
				$fl=$_GET["fl"];
				if($_GET["fl"]=="sku"){
					if(preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["tx"])){
						$fl=$_GET["fl"];
					}else{
						$_GET["tx"]="=*/?";
					}
				}
			}
		}
		$this->fl=$fl;
		if(isset($_GET["tx"])&&strlen(trim($_GET["tx"]))>0){
			$tx=$this->getStringSqlSet($_GET["tx"]);
		}
		if(isset($_GET["lid"])&&preg_match("/^[0-9]{1,12}$/",$_GET["lid"])){
			$this->lid=$_GET["lid"];
		}
		if($tx!=""){
			$tx=substr($tx,1,-1);
			$this->txsearch=$tx;
			$idsearch=">=".$this->lid." ";
			if($this->lid>0){
				$idsearch="<=".$this->lid." ";
			}
			$this->sh=" WHERE `device_drawers`.`id`".$idsearch." AND `device_drawers`.`".$fl."` LIKE  \"%".$tx."%\""  ;
		}
	}
	private function writeContentDrawers(){
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$dt=$this->getAllDrawers();
		$se=$dt["get"];
		echo '<form class="form100" name="'.$this->a.'" method="post">
			<input type="hidden" name="sku" value="" />';
		echo '	<table class="table_view_all_device" style="width:100%;">
				<tr><th>ที่</th>
				<th>รูป</th>
				<th>รหัส</th>
				<th>ชื่อ</th>
				<th>กระทำ</th>
				</tr>';
		for($i=0;$i<count($se);$i++){
			$ed='';
			if($se[$i]["sku"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}
			$icons=json_decode($se[$i]["icon_arr"],true);
			if(count($icons)==0){
				$icons=["null.png"];
			}
			$cm=($i%2!=0)?" class=\"i2\"":"";
			$name=htmlspecialchars($se[$i]["name"]);
			$sku=$se[$i]["sku"];
			if($this->txsearch!=""){
				$ts=htmlspecialchars(str_replace("\\","",$this->txsearch));
				if($this->fl=="name"){
					$name=str_replace($ts,'<span class="bgyl">'.$ts.'</span>',$name);
				}else if($this->fl=="ip"){
					$brand_name=str_replace($ts,'<span class="bgyl">'.$ts.'</span>',brand_name);
				}else if($this->fl=="sku"){
					$sku=str_replace($ts,'<span class="bgyl">'.$ts.'</span>',$sku);
				}
				
			}
			$sn=strlen(trim($se[$i]["sku"]))>0?substr(trim($se[$i]["sku"]),0,15):(mb_substr(trim($se[$i]["name"]),0,15));
			echo '<tr'.$cm.'><td class="r">'.($se[$i]["id"]).'</td>
				<td class="l"><div class="img48"><img  class="viewimage" src="img/gallery/64x64_'.$icons[0].'"   onerror="this.src=\'img/pos/64x64_null.png\'" alt="'.$sn.'" onclick="G.view(this)"  title="เปิดดูภาพ" /></div></td>
				<td class="l"><a href="?a='.$this->a.'&amp;b=drawers&amp;c=details&amp;sku='.$se[$i]["sku"].'">'.$se[$i]["sku"].'</a></td>
				<td class="l">'.$name.'</td>
				<td class="action">';
			
				echo '	<a onclick="Dv.editDrawers(\''.$se[$i]["sku"].'\')" title="แก้ไข">📝</a>';
			
			echo '		<a onclick="Dv.deleteDrawers(\''.$se[$i]["sku"].'\',\''.htmlspecialchars($se[$i]["name"]).'\')" title="ทิ้ง">🗑</a>
					'.$ed.'
					</td>
				</tr>';
				$this->lid=$se[$i]["id"];
			
		}
		echo '</table></form>';
	}	
	public function getAllDrawers():array{
		$sh=$this->defaultSearch();
		$re=[];
		$sql=[];
		$limit_page=(($this->page-1)*$this->per).",".($this->per+1);
		if($this->txsearch!=""){
				$limit_page=$this->per+1;
		}
		$sql["count"]="SELECT COUNT(*) AS `count`  FROM `device_drawers`";
		$sql["get"]="SELECT `id`,`name`,`sku` ,IFNULL(`icon_arr`,'[]') AS `icon_arr`,
				`disc`
			FROM `device_drawers` 
			".$this->sh." 
			ORDER BY `id` DESC LIMIT ".$limit_page."";
		$se=$this->metMnSql($sql,["count","get"]);
		if($se["result"]){
			$re=$se["data"];//["get"];
		}
		return $re;
	}
	private function regisDrawersPage(){
		$this->addDir("?a=device&amp;b=drawers&amp;c=regis","ลงทะเบียน ".$this->title);
		$this->pageHead(["title"=>"ลงทะเบียน ".$this->title,"js"=>["device","Dv","fileupload","Ful"],"css"=>["device","fileupload"]]);
			echo '<div class="content">
				<div class="form">
					<h1 class="c">ลงทะเบียน '.$this->title.'</h1>';
			$this->writeContentDrawersPage();
			echo '<br /><p class="c">
				
			</p>';
			echo '</div></div>';
			$this->pageFoot();
	}
	private function writeContentDrawersPage():void{
		echo '<form  name="device_drawers" method="post">
			<p><label for="sku">รหัสภายใน</label></p>
			<div><input id="sku" type="text" value=""  name="sku" class="want" autocomplete="off" /></div>
			<p><label for="name">ชื่ออุปกรณ์</label></p>
			<div><input id="name" type="text" value=""   class="want" name="name" autocomplete="off" /></div>
			<p><label for="no">เลขที่ '.$this->title.'</label></p>
			<div><input id="no" type="text" value=""  name="no" autocomplete="off" /></div>
			<p><label for="disc">รายละเอียดย่อ</label></p>
			<div><textarea   name="disc"></textarea></div>
			<div>';

		echo '<div class="billinfileimg">
			<div>
				<p><span>รูปภาพอุปกรณ์</span></p>
				<div>
					<div id="div_fileuploadpre" class="fileuploadpres"></div>
					<input id="upload_pic" type="file" accept="image/png,image/gif,image/jpeg,image/webp" class="fuif" name="picture" onchange="Ful.fileUploadShow(event,20,Dv.icon,1024,160)" />
					<label for="upload_pic"  class="fubs">+เลือกรูปภาพ</label>
				</div>
			</div>	
			<script type="text/javascript">/*F.fileUploadShow(null,1,\'icon_id\',480,160,\'load\',\'div_fileuploadpre\')*/</script>
		</div>
		<br /><br />
		<input type="button" onclick="Dv.regisSubmit(\'drawers\')" value="ลงทะเบียน" /></form>';

	}
	private function editPage(){
		$se=$this->checkSet("device_drawers",["post"=>["sku"]],"post");
		if(!$se["result"]){
			$error=$se["message_error"];
		}else{
			$dt=$this->getDataView1($_POST["sku"]);
				if(count($dt)>0){
					$this->addDir("","แก้ไข ".$this->title." รหัส : ".htmlspecialchars($dt[0]["sku"]));
					$this->pageHead(["title"=>"แก้ไข ".$this->title." DIYPOS","js"=>["device","Dv","fileupload","Ful","gallery","Gl"],"css"=>["device","fileupload","gallery"]]);
						echo '<div class="content">
							<div class="form">
								<h2 class="c">แก้ไข  '.$this->title.' รหัส : '.htmlspecialchars($dt[0]["sku"]).'</h2>';
						$this->writeContentInDrawersEdit($_POST["sku"],$dt);
						echo '</div></div>';
						//$this->writeJsDataEdit($pd,$editable,$sku_root,$id,$product_list_id);
						//$this->writeJsDataEdit($dt,$editable,null);
						$this->pageFoot();
				}else{
					$this->addDir("","แก้ไข");
					$this->pageHead(["title"=>"แก้ไข ".$this->title." DIYPOS","css"=>["device"]]);
					echo '<main><p class="error c">ไม่พบข้อมูล ที่ส่งมา</p></main>';
					$this->pageFoot();
				}
		}
	}
	private function writeContentInDrawersEdit(string $sku,array $dt):void{
		$d=$dt[0];
		$gallery=$this->propToFromValue($dt[0]["icon_arr"]);
		$gallery_gl=$this->propToFromValue($dt[0]["icon_gl"]);
		$gallery_list_id=$this->key("key",7);
		$gallery_gl_list_id=$this->key("key",7);
		echo '<form  name="device_drawers" method="post">
			<input type="hidden" name="id" value="'.htmlspecialchars($d["id"]).'" />
			<input type="hidden" id="'.$gallery_list_id.'" name="gallery_list" value="'.$gallery.'" />
			<input type="hidden" id="'.$gallery_gl_list_id.'" name="gallery_gl_list" value="'.$gallery_gl.'" />
			<p><label for="sku">รหัสภายใน</label></p>
			<div><input id="sku" type="text" value="'.$d["sku"].'"  class="want" name="sku" autocomplete="off" /></div>
			<p><label for="name">ชื่ออุปกรณ์</label></p>
			<div><input id="name" type="text" value="'.$d["name"].'"  class="want"  name="name" autocomplete="off" /></div>
			<p><label for="no">เลขที่ เครื่องบันทึกเงินสด</label></p>
			<div><input id="no" type="text" value="'.$d["no"].'"  name="no" autocomplete="off" /></div>
			<p><label for="disc">รายละเอียดย่อ</label></p>
			<div><textarea   name="disc">'.$d["disc"].'</textarea></div>
			';

		echo '<br />';


		$gal_id=$this->key("key",7);
		$this->gall=new gallery("device_drawers","sku",$d["sku"],"device_drawers",$gal_id,$gallery_list_id,$gallery_gl_list_id,"Dv.icon");	
		$this->gall->writeForm();	
		echo '<br /><br />
		<input type="button" onclick="Dv.deviceSumit(\'drawers\')" value="แก้ไข '.$this->title.'" /></form>';
	}
	private function getDataView1(string $sku):array{
		$sku=$this->getStringSqlSet($sku);
		$re=[];
		$sql=[];
		$sql["get"]="SELECT `id`,`sku`,`name`,`no`,`disc`,
				IFNULL(`icon_arr`,'[]') AS `icon_arr`,
				IFNULL(`icon_gl`,'[]') AS `icon_gl`
			FROM `device_drawers` WHERE `sku`=".$sku."";
		$se=$this->metMnSql($sql,["get"]);
		if($se["result"]){
			$re=$se["data"]["get"];
		}
		//print_r($se);
		return $re;
	}
	protected function setPropR(string $prop):string{
		$ar = [];
		if(strlen(trim($prop))>2){
			$prop =trim($prop);
			$ar = explode(",,",substr($prop,2,-2));
		}
		if($ar[0]==""){
			$ar=[];
		}
		return json_encode($ar);
	}
}
?>
