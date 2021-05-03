<?php
class device_pos_details extends device_pos{
	public function __construct(string $ip){
		parent::__construct();
		$this->ip=$ip;
	}
	public function run(){
		$this->addDir("?a=device&amp;b=pos","เครื่องขายเงินสด");
		$this->detailsPage();
		
	}
	private function detailsPage():void{
		$dt=$this->detailsGetData()["pos"];
		if(count($dt)>0){
			$pn_name=$dt["ip"];
			$this->addDir("",htmlspecialchars($pn_name));
			$this->pageHead(["title"=>$this->title." ".htmlspecialchars($pn_name),"css"=>["device"],"js"=>[],"run"=>[]]);
			echo '<div class="content">
				<div class="form">
					<h1 class="c">'.$pn_name.'</h1>';
			$this->writeContentPOS($dt);		
			echo '<br />';
			$this->pageFoot();
		}else{
			$pn_name="ไม่พบข้อมูลเครื่องขายเงินสด";
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
		if(!empty($dt["disc"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">รายละเอียด</td><td class="l">'.htmlspecialchars($dt["disc"]).'</td></tr>';
		}
		if(!empty($dt["drawers_sku"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">ใช้งานกับลิ้นชัก/ที่เก็บเงิน</td><td class="l">'.htmlspecialchars($dt["drawers_name"]).' ['.$dt["drawers_sku"].']</td></tr>';
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
		$ip=$this->getStringSqlSet($this->ip);
		$re=["pos"=>[]];
		$sql=[];
		$sql["pos"]="SELECT `device_pos`.`sku`,`device_pos`.`name`,`device_pos`.`no`,`device_pos`.`ip`,
			IFNULL(`device_pos`.`icon_arr`,'[]') AS `icon_arr`,
				`device_pos`.`disc`,`device_pos`.`date_reg` ,
				IFNULL(`device_drawers`.`name`,'ไม่ใช้ลิ้นชัก/ที่เก็บเงิน') AS `drawers_name`,
				IFNULL(`device_drawers`.`sku`,'') AS `drawers_sku`
			FROM `device_pos`
			LEFT JOIN `device_drawers`
			ON(`device_pos`.`drawers_id`=`device_drawers`.`id`) 
			WHERE `device_pos`.`ip`=".$ip.";
		";
		$se=$this->metMnSql($sql,["pos"]);
		if($se["result"]&&isset($se["data"]["pos"][0])){
			$re["pos"]=$se["data"]["pos"][0];
			$icon_arr=json_decode($se["data"]["pos"][0]["icon_arr"],true);
			if(count($icon_arr)>0){
				$re["pos"]["icon"]=$icon_arr[0];
			}
			$re["pos"]["icon_arr"]=$icon_arr;
			
		}
		return $re;
	}
}
