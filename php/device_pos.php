<?php
class device_pos extends device{
	public function __construct(){
		parent::__construct();
		$this->a = "device";
		$this->title = "เครื่องขายเงินสด";
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
		$this->addDir("?a=device&amp;b=pos",$this->title);
		if(isset($_GET["c"])&&in_array($_GET["c"],$q)){
			$t=$_GET["c"];
			if($t=="regis"){
				$this->regisPOSPage();
			}else if($t=="edit"){
				$gall = "php/gallery.php";
				require($gall);	
				$this->editPage();
			}else if($t=="delete"){
				$this->deletePOS();
			}else if($t=="details"){
				if(isset($_GET["ip"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["ip"])){
					require("php/device_pos_details.php");
					(new device_pos_details($_GET["ip"]))->run();
				}else{
					$this->pagePOS();
				}
			}
		}else{
			$this->pagePOS();
		}
	}
	private function deletePOS():void{
		if(isset($_POST["ip"])){
			$ip=$_POST["ip"];
			$url_refer=(isset($_GET["url_refer"]))?$_GET["url_refer"]:"";
			$sku_root=$this->getStringSqlSet($_POST["ip"]);
			$sql=[];
			$sql["set"]="SELECT @result:=0,
				@message_error:='',
				@ip:='".$ip."',
				@icon_gl:=(SELECT icon_gl FROM `device_pos` WHERE `ip`=@ip LIMIT 1)
			";
			
			$sql["del"]="DELETE FROM `device_pos` WHERE `ip`=@ip";
			$sql["del_img"]="IF 1=1 THEN 
				DELETE FROM `gallery` WHERE `a_type`='device_pos' AND `gl_key`=@ip;
				SET @result=1;
			END IF";
			$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@ip AS `ip`,@icon_gl AS `icon_gl`";
			
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
			$url=preg_replace($pt, $pr,$url_refer)."&ed=".$_POST["ip"];
			header('Location:'.$url);
		}
	}
	public function fetch(){
		$p=["regis","edit"];
		if(isset($_POST["c"])&&in_array($_POST["c"],$p)){
			$t=$_POST["c"];
			if($t=="regis"){
				$this->fetchPOSRegis();
			}else if($t=="edit"){
				$this->fetchPOSEdit();
			}
		}else{
			header('Content-type: application/json');
			print_r("{}");
		}		
	}
	private function fetchPOSEdit():void{
		$error="";
		if(isset($_POST["submith"])&&$_POST["submith"]=="clicksubmit"){
			$se=$this->checkSet("device_pos",["post"=>["name","sku","no","disc","drawers_id"]],"post");
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				$qe=$this->fetchPOSUpdate();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["result"]==0){
					$error=$qe["message_error"];
				}else if($qe["result"]==1){
					header('Content-type: application/json');
					$d=["result"=>true,"message_error"=>"","data"=>["ip"=>$qe["ip"]]];
					echo json_encode($d,true);
				}
			}
			if($error!=""){
				$this->fetchPOSPage($error);
			}
		}else{
			$this->fetchPOSPage($error);
		}
	}
	private function fetchPOSUpdate():array{
		$disc=$this->getStringSqlSet($_POST["disc"]);
		$name=$this->getStringSqlSet($_POST["name"]);
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$no=$this->getStringSqlSet($_POST["no"]);
		$drawers_id=(int) $_POST["drawers_id"];
		$sku_root=$this->getStringSqlSet($_SESSION["sku_root"]);
		$ip=$this->getStringSqlSet($this->userIPv4());
		$icon_arr=$this->setPropR($this->getStringSqlSet($_POST["gallery_list"]));
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@ip:=".$ip.",
			@sku:=".$sku.",
			@name:=".$name.",
			@no:=".$no.",
			@disc:=".$disc.",
			@drawers_id:=".$drawers_id.",
			@icon_arr:=".$this->getStringSqlSet(json_encode($icon_arr)).",
			@count_drawers:=(SELECT COUNT(*)  FROM `device_drawers` WHERE `id`=@drawers_id ),
			@user:=(SELECT `sku_key`  FROM `user` WHERE `sku_root`=".$sku_root." LIMIT 1);
		";
		$sql["check"]="
			IF @count_drawers = 0 && @drawers_id != 0 THEN 
				SET @message_error='เกิดขอผิดพลาด ลิ้นชักที่ระบุมมาไม่มี';
			END IF;			
		";
		$sql["run"]="BEGIN NOT ATOMIC 
			IF @drawers_id = 0 THEN
				SET @drawers_id = NULL;
			END IF;
			IF LENGTH(@message_error) = 0 THEN
				UPDATE `device_pos`  SET
						`sku`=@sku,	`name`=@name		,`drawers_id`=@drawers_id	,`no`=@no	,
						`ip`=@ip		,`disc`=@disc,`icon_arr`=JSON_UNQUOTE(@icon_arr)
					WHERE `ip`=@ip;
				SET @result=1;
			END IF;
		END;";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@ip AS `ip`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($se);exit;
		return $se["data"]["result"][0];
	}
	private function fetchPOSRegis():void{
		$error="";
		if(isset($_POST["submith"])&&$_POST["submith"]=="clicksubmit"){
			$se=$this->checkSet("device_pos",["post"=>["name","sku","no","disc","drawers_id"]],"post");
			if(!$se["result"]){
				$error=$se["message_error"];
			}else{
				$qe=$this->fetchPOSInsert();
				if(!$qe["result"]){
					$error=$qe["message_error"];
				}else if($qe["result"]==0){
					$error=$qe["message_error"];
				}else if($qe["result"]==1){
					if(isset($_SESSION["time_stat"])&&$_SESSION["time_stat"]=="device_regis"){
						$_SESSION["time_stat"]="view_me";
					}
					header('Content-type: application/json');
					$d=["result"=>true,"message_error"=>"","data"=>["ip"=>$qe["ip"]]];
					echo json_encode($d,true);
				}
			}
			if($error!=""){
				$this->fetchPOSPage($error);
			}
		}else{
			$this->fetchPOSPage($error);
		}
	}
	private function fetchPOSInsert():array{
		$disc=$this->getStringSqlSet($_POST["disc"]);
		$name=$this->getStringSqlSet($_POST["name"]);
		$sku=$this->getStringSqlSet($_POST["sku"]);
		$drawers_id=(int) $_POST["drawers_id"];
		$no=$this->getStringSqlSet($_POST["no"]);
		$sku_root=$this->getStringSqlSet($_SESSION["sku_root"]);
		$ip=$this->getStringSqlSet($this->userIPv4());
		$sql=[];
		$sql["set"]="SELECT @result:=0,
			@message_error:='',
			@ip:=".$ip.",
			@sku:=".$sku.",
			@name:=".$name.",
			@no:=".$no.",
			@disc:=".$disc.",
			@drawers_id:=".$drawers_id.",
			@count_drawers:=(SELECT COUNT(*)  FROM `device_drawers` WHERE `id`=@drawers_id ),
			@user:=(SELECT `sku_key`  FROM `user` WHERE `sku_root`=".$sku_root." LIMIT 1);
		";
		$sql["check"]="
			IF @count_drawers = 0 && @drawers_id != 0 THEN 
				SET @message_error='เกิดขอผิดพลาด ลิ้นชักที่ระบุมมาไม่มี';
			END IF;			
		";
		$sql["run"]="BEGIN NOT ATOMIC 
			IF @drawers_id = 0 THEN
				SET @drawers_id = NULL;
			END IF;
			IF LENGTH(@message_error) = 0 THEN
				INSERT  INTO `device_pos`  (`sku`,`name`,`no`,`ip`,`disc`,`drawers_id`) 
				VALUES(@sku,@name,@no,@ip,@disc,@drawers_id);
				SET @result=1;
			END IF;
		END;";
		$sql["result"]="SELECT @result AS `result`,@message_error AS `message_error`,@ip AS `ip`";
		$se=$this->metMnSql($sql,["result"]);
		//print_r($se);
		return $se["data"]["result"][0];
	}
	private function fetchPOSPage($error){
		$re=["result"=>false,"message_error"=>""];
		$re["message_error"]=$error;
		$js=json_encode($re);
		header('Content-type: application/json');
		echo $js;
	}
	protected function pagePOS(){
		$this->defaultPageSearch();
		$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["device"],"js"=>["device","Dv"],"run"=>["Dv"]]);
			
			echo '<div class="content">
				<div class="form">
				<h1 class="c">'.$this->title.'</h1>
				<div class="pn_search">
					<form class="form100_search" name="pd_search" action="" method="get">
						<input type="hidden" name="a" value="device" />
						<input type="hidden" name="b" value="pos" />
						<input type="hidden" name="lid" value="0" />
						<label><select id="product_search_fl" name="fl">
							<option value="sku"'.(($this->fl=="sku")?" selected":"").'>รหัสภายใน</option>
							<option value="name"'.(($this->fl=="name")?" selected":"").'>ชื่อ</option>
							<option value="ip"'.(($this->fl=="ip")?" selected":"").'>หมายเลข IP</option>
						</select>
						 <input id="product_search_tx" type="text" name="tx" value="'.str_replace("\\","",htmlspecialchars($this->txsearch)).'" />
						 <input  type="submit" value="🔍" /> </label></form>
				</div>';
		$has_ip=$this->writeContentPOS();		
		if($has_ip!=""){
			echo '<br />หมายเลข IP '.$has_ip.' (อุปกรณ์นี้) ยังไม่มีในระบบ เครื่องขายเงินสด<br /><p class="c"><input type="button" value="ลงทะเบียน '.$this->title.'" onclick="location.href=\'?a='.$this->a.'&amp;b=pos&amp;c=regis\'" /></p>';
		}
		echo '</div></div>';
		$this->pageFoot();
	}
	private function defaultSearch():string{
		$fla=["sku","ip","name"];
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
			$se=" WHERE `device_pos`.`".$fl."` LIKE  \"%".$tx."%\"  ";
		}
		return $se;
	}
	public function defaultPageSearch():void{
		$fla=["sku","name","ip"];
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
			$this->sh=" WHERE `device_pos`.`id`".$idsearch." AND `device_pos`.`".$fl."` LIKE  \"%".$tx."%\""  ;
		}
	}
	private function writeContentPOS():string{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$dt=$this->getAllPOS();
		$se=$dt["get"];
		echo '<form class="form100" name="'.$this->a.'" method="post">
			<input type="hidden" name="ip" value="" />';
		echo '	<table class="table_view_all_device" style="width:100%;">
				<tr><th>ที่</th>
				<th>รูป</th>
				<th>IP</th>
				<th>รหัส</th>
				<th>ชื่อ</th>
				<th>กระทำ</th>
				</tr>';
		$has=false;
		$ip=$this->userIPv4();
		for($i=0;$i<count($se);$i++){
			if($se[$i]["ip"]==$ip){
				$has=true;
			}
			$ed='';
			if($se[$i]["ip"]==$edd){
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
			$onoff=($se[$i]["onoff"]==0)?"off":"on";
			echo '<tr'.$cm.'><td class="r">'.($se[$i]["id"]).'</td>
				<td class="l"><div class="img48"><img  class="viewimage" src="img/gallery/64x64_'.$icons[0].'"   onerror="this.src=\'img/pos/64x64_null.png\'" alt="'.$sn.'" onclick="G.view(this)"  title="เปิดดูภาพ" /></div></td>
				<td class="l"><span class="device_pos_'.$onoff.'"><a href="?a='.$this->a.'&amp;b=pos&amp;c=details&amp;ip='.$se[$i]["ip"].'">'.$se[$i]["ip"].'</a></span></td>
				<td class="l">'.$sku.'</td>
				<td class="l">'.$name.'</td>
				<td class="action">';
			if($ip==$se[$i]["ip"]){
				echo '	<a onclick="Dv.edit(\''.$se[$i]["ip"].'\')" title="แก้ไข">📝</a>';
			}
			echo '		<a onclick="Dv.delete(\''.$se[$i]["ip"].'\',\''.htmlspecialchars($se[$i]["name"]).'\')" title="ทิ้ง">🗑</a>
					'.$ed.'
					</td>
				</tr>';
				$this->lid=$se[$i]["id"];
			
		}
		echo '</table></form>';
		if(!$has){
			return $ip;
		}else{
			return "";
		}
	}	
	public function getAllPOS():array{
		$sh=$this->defaultSearch();
		$re=[];
		$sql=[];
		$limit_page=(($this->page-1)*$this->per).",".($this->per+1);
		if($this->txsearch!=""){
				$limit_page=$this->per+1;
		}
		$sql["count"]="SELECT COUNT(*) AS `count`  FROM `device_pos`";
		$sql["get"]="SELECT `id`,`name`,`sku`,`onoff` ,IFNULL(`icon_arr`,'[]') AS `icon_arr`,
				`ip`,`disc`
			FROM `device_pos` 
			".$this->sh." 
			ORDER BY `id` DESC LIMIT ".$limit_page."";
		$se=$this->metMnSql($sql,["count","get"]);
		if($se["result"]){
			$re=$se["data"];//["get"];
		}
		return $re;
	}
	private function regisPOSPage(){
		$this->addDir("?a=device&amp;b=pos&amp;c=regis","ลงทะเบียน เครื่องขายเงินสด ");
		$this->pageHead(["title"=>"ลงทะเบียน เครื่องขายเงินสด","js"=>["device","Dv","fileupload","Ful"],"css"=>["device","fileupload"]]);
			echo '<div class="content">
				<div class="form">
					<h1 class="c">ลงทะเบียน เครื่องขายเงินสด</h1>';
			$this->writeContentPOSPage();
			echo '<br /><p class="c">
				
			</p>';
			echo '</div></div>';
			$this->pageFoot();
	}
	private function writeContentPOSPage():void{
		$ip=$this->userIPv4();
		echo '<form  name="device_pos" method="post">
			<p><label for="ip">หมายเลข IP</label></p>
			<div><input id="ip" type="text" value="'.$ip.'"  name="ip" autocomplete="off"  readonly class="want" /></div>
			<p><label for="no">เลขที่ เครื่องบันทึกเงินสด</label></p>
			<div><input id="no" type="text" value=""  name="no" autocomplete="off" /></div>
			<p><label for="sku">รหัสภายใน</label></p>
			<div><input id="sku" type="text" value=""  name="sku" autocomplete="off" /></div>
			<p><label for="name">ชื่ออุปกรณ์</label></p>
			<div><input id="name" type="text" value=""  name="name" autocomplete="off" /></div>
			<p><label for="disc">รายละเอียดย่อ</label></p>
			<div><textarea   name="disc"></textarea></div>';
		$this->writeSelectDrawers();
		echo '<br /><div><div class="billinfileimg">
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
		<input type="button" onclick="Dv.regisSubmit(\'pos\')" value="ลงทะเบียน" /></form>';

	}
	private function writeSelectDrawers(array $dt=null,int $drawers_selected=0):void{
		$a=$dt;
		if(!is_array($a)){
			$a=$this->getListDrawers();
		}
		echo '<p><label for="drawers">ลิ้นชัก/ทีเก็บเงิน</label></p>
			<div>
				<select name="drawers_id">';	
		$no=["id"=>0,"name"=>"ไม่ใช้งาน","sku"=>""];
		array_unshift($a, $no);		
		for($i=0;$i<count($a);$i++){
			$sl=($a[$i]["id"]==$drawers_selected)?" selected":"";
			echo '<option value="'.$a[$i]["id"].'"'.$sl.'>'.htmlspecialchars($a[$i]["name"]).' [ '.$a[$i]["sku"].']</option>';
		}
		echo '		</select>
			</div>';
	}
	private function getListDrawers():array{
		$re=[];
		$sql=[];
		$sql["get"]="SELECT `id`,`sku`,`name`
			FROM `device_drawers` ";
		$se=$this->metMnSql($sql,["get"]);
		if($se["result"]){
			$re=$se["data"]["get"];
		}
		//print_r($re);
		return $re;
	}
	private function editPage(){
		$se=$this->checkSet("device_pos",["post"=>["ip"]],"post");
		if(!$se["result"]){
			$error=$se["message_error"];
		}else{
			$dt=$this->getDataView1($_POST["ip"]);
				if(count($dt)>0){
					$this->addDir("","แก้ไขเครื่องขายเงินสด ".htmlspecialchars($dt["pos"]["ip"]));
					$this->pageHead(["title"=>"แก้ไขเครื่องขายเงินสด DIYPOS","js"=>["device","Dv","fileupload","Ful","gallery","Gl"],"css"=>["device","fileupload","gallery"]]);
						echo '<div class="content">
							<div class="form">
								<h2 class="c">แก้ไข  เครื่องขายเงินสด '.htmlspecialchars($dt["pos"]["ip"]).'</h2>';
						$this->writeContentInPOSEdit($_POST["ip"],$dt);
						echo '</div></div>';
						//$this->writeJsDataEdit($pd,$editable,$sku_root,$id,$product_list_id);
						//$this->writeJsDataEdit($dt,$editable,null);
						$this->pageFoot();
				}else{
					$this->addDir("","แก้ไข");
					$this->pageHead(["title"=>"แก้ไขเครื่องขายเงินสด DIYPOS","css"=>["device"]]);
					echo '<main><p class="error c">ไม่พบข้อมูล ที่ส่งมา</p></main>';
					$this->pageFoot();
				}
		}
	}
	private function writeContentInPOSEdit(string $sku,array $dt):void{
		$pos=$dt["pos"];
		$drawers=$dt["drawers"];
		$gallery=$this->propToFromValue($pos["icon_arr"]);
		$gallery_gl=$this->propToFromValue($pos["icon_gl"]);
		$gallery_list_id=$this->key("key",7);
		$gallery_gl_list_id=$this->key("key",7);
		echo '<form  name="device_pos" method="post">
			<input type="hidden" name="ip" value="'.htmlspecialchars($pos["ip"]).'" />
			<input type="hidden" id="'.$gallery_list_id.'" name="gallery_list" value="'.$gallery.'" />
			<input type="hidden" id="'.$gallery_gl_list_id.'" name="gallery_gl_list" value="'.$gallery_gl.'" />
			<p><label for="ip">หมายเลข IP</label></p>
			<div><input id="ip" type="text" value="'.$pos["ip"].'"  name="ip" autocomplete="off"  readonly class="want" /></div>
			<p><label for="no">เลขที่ เครื่องบันทึกเงินสด</label></p>
			<div><input id="no" type="text" value="'.$pos["no"].'"  name="no" autocomplete="off" /></div>
			<p><label for="sku">รหัสภายใน</label></p>
			<div><input id="sku" type="text" value="'.$pos["sku"].'"  name="sku" autocomplete="off" /></div>
			<p><label for="name">ชื่ออุปกรณ์</label></p>
			<div><input id="name" type="text" value="'.$pos["name"].'"  name="name" autocomplete="off" /></div>
			<p><label for="disc">รายละเอียดย่อ</label></p>
			<div><textarea   name="disc">'.$pos["disc"].'</textarea></div>
			';
		$this->writeSelectDrawers($drawers,$pos["drawers_id"]);
		echo '<br />';


		$gal_id=$this->key("key",7);
		$this->gall=new gallery("device_pos","ip",$pos["ip"],"device_pos",$gal_id,$gallery_list_id,$gallery_gl_list_id,"Dv.icon");	
		$this->gall->writeForm();	
		echo '<br /><br />
		<input type="button" onclick="Dv.deviceSumit(\'pos\')" value="แก้ไข เครื่องขายเงินสด" /></form>';
	}
	private function getDataView1(string $ip):array{
		$ip=$this->getStringSqlSet($ip);
		$re=["pos"=>[],"drawers"=>[]];
		$sql=[];
		$sql["get"]="SELECT `ip`,`sku`,`name`,`no`,`disc`,
				IFNULL(`drawers_id`,0) AS `drawers_id`,
				IFNULL(`icon_arr`,'[]') AS `icon_arr`,
				IFNULL(`icon_gl`,'[]') AS `icon_gl`
			FROM `device_pos` WHERE `ip`=".$ip."";
		$sql["drawers"]="SELECT `id`,`sku`,`name`
			FROM `device_drawers` ";	
		$se=$this->metMnSql($sql,["get","drawers"]);
		if($se["result"]){
			if(isset($se["data"]["get"][0])){
				$re["pos"]=$se["data"]["get"][0];
				$re["drawers"]=$se["data"]["drawers"];
			}
		}
		//print_r($re);
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
