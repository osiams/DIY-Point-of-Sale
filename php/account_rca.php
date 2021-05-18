<?php
class account_rca extends account{
	public function __construct(){
		parent::__construct();
		$this->b="account_rca";
		$this->get_rca=[];
		$this->per=1;
		$this->page=1;
		$this->txsearch="";
		$this->fl="";
		$this->lid=0;
		$this->sh="";
	}
	public function run(){
		$this->page=$this->setPageR();
		$this->defaultAccountRcaPage();
	}
	protected function defaultAccountRcaPage(){
		$this->defaultPageSearch();
		$data=$this->getAllMember();
		$tl="ลูกหนี้ ค้างชำระ";
		$this->addDir("",$tl);
		$this->pageHead(["title"=>$tl." DIYPOS","css"=>["account"],"js"=>["account","Ac"],"run"=>["Ac"]]);
		echo '<div class="content">';
	
		echo '	<div class="form">
			<h1 class="c">'.$tl.'</h1>';
		$this->writeDashboard($data);	
		echo '	<div class="pn_search">
				<form class="form100" name="pd_search" action="" method="get">
					<input type="hidden" name="a" value="account" />
					<input type="hidden" name="b" value="account_rca" />
					<input type="hidden" name="lid" value="0" />
					<label><select id="product_search_fl" name="fl">
						<option value="name"'.(($this->fl=="name")?" selected":"").'>ชื่อ</option>
						<option value="lastname"'.(($this->fl=="lastname")?" selected":"").'>นามสกุล</option>
						<option value="sku"'.(($this->fl=="sku")?" selected":"").'>รหัส</option>
						<option value="tel"'.(($this->fl=="tel")?" selected":"").'>เบอร์โทรศัพท์</option>
						<option value="idc"'.(($this->fl=="idc")?" selected":"").'>บัตรประชาชน</option>
					</select>
						<input id="product_search_tx" type="text" name="tx" value="'.str_replace("\\","",htmlspecialchars($this->txsearch)).'" />
						<input  type="submit" value="🔍" /> </label>
				</form>
			</div>';
		$this->writeContentMember($data);		
		echo '<br /><p class="c"><input type="button" value="เพิ่ม'.$this->title.'" onclick="location.href=\'?a='.$this->a.'&amp;b=regis\'" /></p>';
		echo '</div></div>';
		$this->pageFoot();
		
		/*echo '<div class="content">
			<h2>'.$tl.'</h2>';
		$this->writeRca();
		echo '</div>';
		$this->pageFoot();*/
	}
	private function writeRca():void{
		$this->getRca();
		//print_r($this->get_rca);
		
	}
	private function getRca(){
		$sql=[];
		$sql["get_rca"]="SELECT `member`.`sku`	,`member`.`name`	,`member`.`lastname`,
				`member`.`credit`
			FROM `member` 
			WHERE `member`.`credit`> 0;
		";
		$se=$this->metMnSql($sql,["get_rca"]);
		//print_r($se);
		if(isset($se["data"]["get_rca"])){
			$this->get_rca=$se["data"]["get_rca"];
		}
	}
	private function writeDashboard(array $dt):void{
		$count=$dt["count"][0]["count"];
		$sum=$dt["sum"][0]["sum"];
		$per=round($sum/$count,2);
		echo '<div>
			<div class="account_rca_dashboard"><div>';
		echo '<div class="rca_n_member">จำนวน ลูกหนี้<div>'.number_format($count,0,".",",").'</div></div>
				<div class="rca_n_member">จำนวนเงินค้างชำระรวม<div>฿ '.number_format($sum,2,".",",").'</div></div>
				<div class="rca_n_member">เฉลี่ยค้างชำระ / คน<div>฿ '.number_format($per,2,".",",").'</div></div>';
		echo '</div></div>
		</div>';
	}
	private function writeContentMember(array $data):void{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$dt=$data;
		$se=$dt["get"];
		echo '<form class="form100" name="'.$this->a.'" method="post">
			<input type="hidden" name="sku_root" value="" />';
		echo '	<table class="table_view_all_member" style="width:100%;">
				<tr><th>ที่</th>
				<th>รูป</th>
				<th>รหัส</th>
				<th>ชื่อ</th>
				<th>ประเภท</th>
				<th>ค้างชำระ</th>
				<th>กระทำ</th>
				</tr>';
		for($i=0;$i<count($se);$i++){
			$ed='';
			if($se[$i]["sku_root"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}
			$cm=($i%2!=0)?" class=\"i2\"":"";
			$name=htmlspecialchars($se[$i]["name"]);
			$lastname=$se[$i]["lastname"];
			$sku=$se[$i]["sku"];
			if($this->txsearch!=""){
				$ts=htmlspecialchars(str_replace("\\","",$this->txsearch));
				if($this->fl=="name"){
					$name=str_replace($ts,'<span class="bgyl">'.$ts.'</span>',$name);
				}else if($this->fl=="brand_name"){
					$brand_name=str_replace($ts,'<span class="bgyl">'.$ts.'</span>',brand_name);
				}else if($this->fl=="sku"){
					$sku=str_replace($ts,'<span class="bgyl">'.$ts.'</span>',$sku);
				}
				
			}
			$sn=strlen(trim($se[$i]["sku"]))>0?substr(trim($se[$i]["sku"]),0,15):(mb_substr(trim($se[$i]["name"]),0,15));
			echo '<tr'.$cm.'><td class="r">'.($se[$i]["id"]).'</td>
				<td class="l"><div class="img48"><img  class="viewimage" src="img/gallery/64x64_'.$se[$i]["icon"].'"   onerror="this.src=\'img/pos/64x64_null.png\'" alt="'.$sn.'" onclick="G.view(this)"  title="เปิดดูภาพ" /></div></td>
				<td class="l">'.$sku.'</td>
				<td class="l"><a href="?a=member&amp;b=details&amp;sku_root='.$se[$i]["sku_root"].'">'.$name.' '.$lastname.'</a></td>
				<td class="l">'.$this->mb_type[$se[$i]["mb_type"]].'</td>
				<td class="r">'.number_format($se[$i]["credit"],2,'.',',').'</td>
				<td class="action">
						<a onclick="G.action(this)" data-width="350" title="เลือกกระทำ">⚙️</a>
						<a onclick="Pd.productEdit(\''.$se[$i]["sku_root"].'\')" title="ชำระเงิน">💰</a>
						<a onclick="Pd.productEdit(\''.$se[$i]["sku_root"].'\')" title="ประวัติ ยอดค้างชำระ/ชำระ">🕐</a>
					</td>
				</tr>';
				$this->lid=$se[$i]["id"];
		}
		echo '</table></form>';
		//print_r($dt);
		$count=(isset($dt["count"][0]["count"]))?$dt["count"][0]["count"]:0;
		if($this->txsearch==""){
			$this->page($count,$this->per,$this->page,"?a=".$this->a."&amp;b=".$this->b."&amp;page=");
		}else{
			$this->pageSearch(count($se));
		}
	}
	public function getAllMember():array{
		$where=($this->sh!="")?$this->sh:" WHERE `credit` > 0 ";
		$re=[];
		$sql=[];
		$limit_page=(($this->page-1)*$this->per).",".($this->per+1);
		if($this->txsearch!=""){
				$limit_page=$this->per+1;
		}
		$sql["count"]="SELECT COUNT(*) AS `count`  FROM `member` WHERE `credit` > 0 ";
		$sql["sum"]="SELECT SUM(`credit`) AS `sum`  FROM `member` WHERE `credit` > 0 ";
		$sql["get"]="SELECT `id`,`name`,IFNULL(`lastname`,'') AS `lastname`,IFNULL(`icon`,'null.png') AS `icon`,`sku`,`sku_root` ,
				`mb_type`,`credit`
			FROM `member` 
			".($where)." 
			ORDER BY `credit` DESC LIMIT ".$limit_page."";
		$se=$this->metMnSql($sql,["count","sum","get"]);
		//print_r($sql);
		if($se["result"]){
			$re=$se["data"];//["get"];
		}
		return $re;
	}
	private function defaultSearch():string{
		$fla=["sku","lastname","name","idc","tel"];
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
			$se=" WHERE `member`.`credit`> 0 AND `member`.`".$fl."` LIKE  \"%".$tx."%\"  ";
		}
		return $se;
	}
	protected function pageSearch(int $row):void{
		$href='?a='.$this->a.'&amp;b='.$this->b.'&amp;fl='.$this->fl.'&amp;tx='.$this->txsearch.'&amp;page=';
		if($this->page>1){
			echo '<a onclick="history.back()">⬅️ก่อนหน้า</a>';
		}
		echo '<span class="member_page_search">หน้า '.$this->page.'</span>';
		if($row>$this->per){
			echo '<a href="'.$href.''.($this->page+1).'&amp;lid='.$this->lid.'">ถัดไป➡️</a>';
		}
	}
	public function defaultPageSearch():void{
		$fla=["sku","lastname","name","idc","tel"];
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
			$this->sh=" WHERE `member`.`id`".$idsearch." AND `member`.`credit`> 0 AND `member`.`".$fl."` LIKE  \"%".$tx."%\""  ;
		}
	}
	
}
?>
