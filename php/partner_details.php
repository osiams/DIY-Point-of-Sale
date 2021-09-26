<?php
class partner_details extends partner{
	public function __construct(){
		parent::__construct();
		$this->sku_root=null;
		$this->r_more=[];
	}
	public function run(){
		$this->addDir("?a=partner","คู่ค้า");
		$this->setRMore($this->sku_root);
		$qq=["partner_details_product","partner_details_claim","partner_details_claimsend"];
		if(isset($_GET["bb"])&&in_array($_GET["bb"],$qq)){
			$t=$_GET["bb"];
			$this->r_more["active"]=$t;
			require_once("php/".$t.".php");
			eval("\$d=new ".$t."();");
			$dt=$this->detailsGetData()["partner"];
				if(count($dt)>0){
					$pn_name=$dt["name"];
					$this->r_more["menu"][0]["name"]=$this->firstList($pn_name,$dt["icon"]);
					$this->r_more["menu"][2]["name"].=' ('.$dt["claim_n_w"].')';
					$this->r_more["menu"][3]["name"].=' ('.$dt["claim_n_s"].')';
					$this->addDir("?a=partner&amp;b=details&amp;sku_root=".$this->sku_root,htmlspecialchars($pn_name));
					$d->dir=$this->dir;
					$d->sku_root=$this->sku_root;
					$d->r_more=$this->r_more;
					$d->run();					
				}else{
					$this->detailsPage();
				}
		}else{
			$this->detailsPage();
		}
		
		
	}
	private function firstList(string $name,string $icon):string{
		$t='<div class="c"><img  class="viewimage" src="img/gallery/64x64_'.$icon.'"   onerror="this.src=\'img/pos/64x64_null.png\'" alt="" />
			<br>'.htmlspecialchars($name).'</div>
		';
		return $t;
	}
	private function detailsPage():void{
		$dt=$this->detailsGetData()["partner"];
		//print_r($dt);
		if(count($dt)>0){
			$pn_name=$dt["name"];
			$this->r_more["menu"][0]["name"]=$this->firstList($pn_name,$dt["icon"]);
			$this->r_more["menu"][2]["name"].=' ('.$dt["claim_n_w"].')';
			$this->addDir("",htmlspecialchars($pn_name));
			$this->pageHead(["title"=>"คู่ค้า ".htmlspecialchars($pn_name),"css"=>["partner"],"js"=>["partner","Pn"],"run"=>["Pn"],"r_more"=>$this->r_more]);
			echo '<div class="content_rmore">
				<div class="form">
					<h1 class="c">'.$pn_name.'</h1>';
			$this->writeContentPartner($dt);		
			$this->pageFoot();
		}else{
			$pn_name="ไม่พบข้อมูลคู่ค้า";
			$this->addDir("",htmlspecialchars($pn_name));
			$this->pageHead(["title"=>"คู่ค้า ".htmlspecialchars($pn_name),"css"=>["partner"],"js"=>["partner","Pn"],"run"=>["Pn"]]);
			echo '<div class="content">
				<div class="form">
					<h1 class="c">'.$pn_name.'</h1>';
			$this->pageFoot();
		}
	}
	private function writeContentPartner(array $dt):void{
		//print_r($dt);
		$s=-1;
		echo '<table class="table_details">
			<tr><th>ค่า</th><th>รายละเอียด</th></tr>';
		if(!empty($dt["icon"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">รูป</td><td class="c"><img src="img/gallery/256x256_'.$dt["icon"].'" class="viewimage"  onclick="G.view(this)"  title="เปิดดูภาพ" /></td></tr>';
		}
		if(!empty($dt["name"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">ชื่อ</td><td class="l">'.htmlspecialchars($dt["name"]).'</td></tr>';
		}
		if(!empty($dt["brand_name"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">ชื่อการค้า</td><td class="l">'.htmlspecialchars($dt["brand_name"]).'</td></tr>';
		}
		if(!empty($dt["sku"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">รหัสภายใน</td><td class="l">'.$dt["sku"].'</td></tr>';
		}
		if(!empty($dt["tax"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">เลขที่ผู้เสียภาษี</td><td class="l">'.$dt["tax"].'</td></tr>';
		}
		if(!empty($dt["pn_type"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">ประเภทคู่ค้า</td><td class="l">'.$this->pn_type[$dt["pn_type"]].'</td></tr>';
		}
		if(!empty($dt["od_type"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">รูปแบบการสั่งซื้อหลัก</td><td class="l">'.$this->od_type[$dt["od_type"]].'</td></tr>';
		}
		if(!empty($dt["tp_type"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">การส่งสินค้า</td><td class="l">'.$this->tp_type[$dt["tp_type"]].'</td></tr>';
		}
		if(!empty($dt["tel"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">โทรศัพท์</td><td class="l">'.$dt["tel"].'</td></tr>';
		}
		if(!empty($dt["fax"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">แฟ็ก์</td><td class="l">'.$dt["fax"].'</td></tr>';
		}
		if(!empty($dt["web"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">เว็บไซต์</td><td class="l">'.$dt["web"].'</td></tr>';
		}
		if(!empty($dt["note"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">รายละเอียดย่อ์</td><td class="l">'.htmlspecialchars($dt["note"]).'</td></tr>';
		}
		$addres="";
		if(!empty($dt["no"])){
			$addres.="เลขที่ ".htmlspecialchars($dt["no"])."<br />";
		}		
		if(!empty($dt["alley"])){
			$addres.="ซอย ".htmlspecialchars($dt["alley"])."<br />";
		}
		if(!empty($dt["road"])){
			$addres.="ถนน ".htmlspecialchars($dt["road"])."<br />";
		}
		if(!empty($dt["distric"])){
			$addres.="แขวง/ตำบล ".htmlspecialchars($dt["distric"])."<br />";
		}
		if(!empty($dt["country"])){
			$addres.="เขต/อำเภอ ".htmlspecialchars($dt["country"])."<br />";
		}
		if(!empty($dt["province"])){
			$addres.="จังหวัด ".htmlspecialchars($dt["province"])."<br />";
		}
		if(!empty($dt["post_no"])){
			$addres.="รหัสไปรษณี ".htmlspecialchars($dt["post_no"])."<br />";
		}
		if(!empty($addres)){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">ที่อยู่</td><td class="l">'.$addres.'</td></tr>';
		}
		echo '</table>';
		
		
	}
	private function detailsGetData(){
		$sku_root=$this->getStringSqlSet($this->sku_root);
		$re=["partner"=>[]];
		$sql=[];
		$sql["partner"]="SELECT * FROM `partner`
			WHERE `sku_root`=".$sku_root.";
		";
		$se=$this->metMnSql($sql,["partner"]);
		if($se["result"]&&isset($se["data"]["partner"][0])){
			$re["partner"]=$se["data"]["partner"][0];
			if(empty($re["partner"]["icon"])){
				$re["partner"]["icon"]="";
			}
		}
		return $re;
	}
	private function setRMore(string $sku_root):void{
		$url="?a=partner&amp;b=details&amp;sku_root=".$sku_root;
		$data=[
			"menu"=>[
				["b"=>"","name"=>"ข้อมูล คู่ค้า","link"=>$url."&amp;bb=partner_details"],
				["b"=>"partner_details_product","name"=>"สินค้า","link"=>$url."&amp;bb=partner_details_product"],
				["b"=>"partner_details_claim","name"=>"สินต้าต้องส่งเคลม","link"=>$url."&amp;bb=partner_details_claim"],	
				["b"=>"partner_details_claimsend","name"=>"สินต้าส่งเคลมแล้ว","link"=>$url."&amp;bb=partner_details_claimsend"],	
			],
			"active"=>""
		];
		$this->r_more=$data;
	}
}
