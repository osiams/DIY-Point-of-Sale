<?php
class device_drawers_details extends device_drawers{
	public function __construct(string $sku){
		parent::__construct();
		$this->sku=$sku;
	}
	public function run(){
		$this->addDir("?a=device&amp;b=drawers","ลิ้นชักเก็บเงิน");
		$this->detailsPage();
		
	}
	private function detailsPage():void{
		$dt=$this->detailsGetData()["drawers"];
		if(count($dt)>0){
			$pn_name=$dt["sku"];
			$this->addDir("","รหัส : ".htmlspecialchars($pn_name));
			$this->pageHead(["title"=>$this->title." ".htmlspecialchars($pn_name),"css"=>["device"],"js"=>[],"run"=>[]]);
			echo '<div class="content">
				<div class="form">
					<h1 class="c">รหัส : '.$pn_name.'</h1>';
			$this->writeContentPOS($dt);		
			echo '<br />';
			$this->pageFoot();
		}else{
			$pn_name="ไม่พบข้อมูล ลิ้นชักเก็บเงิน";
			$this->addDir("",htmlspecialchars($pn_name));
			$this->pageHead(["title"=>htmlspecialchars($pn_name),"css"=>["device"],"js"=>["device","Dv"]]);
			echo '<div class="content">
				<div class="form">
					<h1 class="c">'.$pn_name.'</h1>';
			$this->pageFoot();
		}
	}
	private function writeContentPOS(array $dt):void{
		//print_r($dt);
		$s=-1;
		echo '<table class="table_details">
			<tr><th>ค่า</th><th>รายละเอียด</th></tr>';
		if(!empty($dt["icon"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">รูป</td><td class="c">
						<div class="device_details_icon">
							<img src="img/gallery/256x256_'.$dt["icon"].'" class="viewimage"  onclick="G.view(this)"  title="เปิดดูภาพ" />
						</div>';
				if(count($dt["icon_arr"])>1){
					echo '<div class="device_details_gallery">';
						for($i=0;$i<count($dt["icon_arr"]);$i++){
							echo '<img src="img/gallery/64x64_'.$dt["icon_arr"][$i].'" class="viewimage"  onclick="G.view(this)"  title="เปิดดูภาพ" />';
						}
					echo '</div>';
				}
			echo '</td></tr>';
		}
		if(!empty($dt["name"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">ชื่อ</td><td class="l">'.htmlspecialchars($dt["name"]).'</td></tr>';
		}
		if(!empty($dt["ip"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">IP</td><td class="l">'.htmlspecialchars($dt["ip"]).'</td></tr>';
		}
		if(!empty($dt["lastname"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">นามสกุล</td><td class="l">'.htmlspecialchars($dt["lastname"]).'</td></tr>';
		}
		if(!empty($dt["sku"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">รหัสภายใน</td><td class="l">'.$dt["sku"].'</td></tr>';
		}		
		if(!empty($dt["sex"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">เพศ</td><td class="l">'.htmlspecialchars($this->sex[$dt["sex"]]).'</td></tr>';
		}
		if(!empty($dt["birthday"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">วัน-เดือน-ปี เกิด</td><td class="l">'.$dt["birthday"].'</td></tr>';
		}
		if(!empty($dt["idc"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">เลขที่บัตรประชาชน</td><td class="l">'.$dt["idc"].'</td></tr>';
		}
		if(!empty($dt["mb_type"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">ประเภทสมาชิก</td><td class="l">'.htmlspecialchars($this->mb_type[$dt["mb_type"]]).'</td></tr>';
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
		if(!empty($dt["web"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">เว็บไซต์</td><td class="l">'.$dt["web"].'</td></tr>';
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
		if(!empty($dt["disc"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">รายละเอียด</td><td class="l">'.htmlspecialchars($dt["disc"]).'</td></tr>';
		}
		if(!empty($dt["date_reg"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">วันที่ลงทะเบียน</td><td class="l">'.htmlspecialchars($dt["date_reg"]).'</td></tr>';
		}
		echo '</table>';
		
		
	}
	private function writeContentProduct():void{
		$pd=$this->detailsGetProduct();
		echo '<table class="table_details">
			<caption>สินค้า</caption>
			<tr><th>ที่</th><th>ป.</th><th>รหัสแท่ง</th><th>ชื่อ</th></tr>';
		for($i=0;$i<count($pd);$i++){
			$s_type=($pd[$i]["s_type"]!==""&&isset($this->s_type[$pd[$i]["s_type"]]))?$this->s_type[$pd[$i]["s_type"]]["icon"]:"";
			echo '<tr>
				<td>'.($i+1).'</td>
				<td>'.$s_type.'</td>
				<td class="l">'.$pd[$i]["barcode"].'</td>
				<td class="l"><a href="?a=product&amp;b=details&amp;sku_root='.$pd[$i]["sku_root"].'">'.htmlspecialchars($pd[$i]["name"]).'</a></td>
			</tr>';
		}
		echo '</table>';
	}
	private function detailsGetData(){
		$sku=$this->getStringSqlSet($this->sku);
		$re=["drawers"=>[]];
		$sql=[];
		$sql["drawers"]="SELECT `sku`,`name`,`no`,IFNULL(`icon_arr`,'[]') AS `icon_arr`,
				`disc`,`date_reg` FROM `device_drawers`
			WHERE `sku`=".$sku.";
		";
		$se=$this->metMnSql($sql,["drawers"]);
		if($se["result"]&&isset($se["data"]["drawers"][0])){
			$re["drawers"]=$se["data"]["drawers"][0];
			$icon_arr=json_decode($se["data"]["drawers"][0]["icon_arr"],true);
			if(count($icon_arr)>0){
				$re["drawers"]["icon"]=$icon_arr[0];
			}
			$re["drawers"]["icon_arr"]=$icon_arr;
			
		}
		return $re;
	}
}
