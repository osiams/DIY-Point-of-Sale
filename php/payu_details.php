<?php
class payu_details extends payu{
	public function __construct(string $sku_root){
		parent::__construct();
		$this->sku_root=$sku_root;
		$this->title="รูปแบบการชำระเงิน";
	}
	public function run(){
		$this->addDir("?a=payu",$this->title);
		$this->detailsPage();
		
	}
	private function detailsPage():void{
		$dt=$this->detailsGetData()["payu"];
		$pn_name=$dt["name"];
		$this->addDir("",htmlspecialchars($pn_name));
		$this->pageHead(["title"=>$this->title." ".htmlspecialchars($pn_name),"css"=>["partner"],"js"=>["partner","Pn"],"run"=>["Pn"]]);
		echo '<div class="content">
			<div class="form">
				<h1 class="c">'.$pn_name.'</h1>';
		$this->writeContentPayu($dt);		
		echo '<br />';
		echo '</div></div>';
		$this->pageFoot();
	}
	private function writeContentPayu(array $dt):void{
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
		if(!empty($dt["sku"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">รหัสภายใน</td><td class="l">'.$dt["sku"].'</td></tr>';
		}
		if(!empty($dt["money_type"])){$s+=1;
			$mt=(isset($dt["money_type"])?$this->money_type[$dt["money_type"]]["icon"]." ".htmlspecialchars($this->money_type[$dt["money_type"]]["name"]):"");
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">ประเภทเงิน</td><td class="l">'.$mt.'</td></tr>';
		}
		if(!empty($dt["note"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">รายละเอียดย่อ</td><td class="l">'.$dt["note"].'</td></tr>';
		}
		if(!empty($dt["date_reg"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">วันที่ลงทะเบียน</td><td class="l">'.$dt["date_reg"].'</td></tr>';
		}
		echo '</table>';
		
		
	}
	private function detailsGetData(){
		$sku_root=$this->getStringSqlSet($this->sku_root);
		$re=["payu"=>[]];
		$sql=[];
		$sql["payu"]="SELECT * FROM `payu`
			WHERE `sku_root`=".$sku_root.";
		";
		$se=$this->metMnSql($sql,["payu"]);
		if($se["result"]){
			$re["payu"]=$se["data"]["payu"][0];
		}
		return $re;
	}
}
