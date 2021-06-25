<?php
class time_view extends main{
	public function __construct(){
		parent::__construct();
		$this->title = "กะ";
		$this->a = "time";
		$this->r_more=[];
		$this->addDir("?a=".$this->a."&amp;b=time_all",$this->title);
	}
	public function run(){
		$q=["time_view_bill_sell_all"];
		$id=(isset($_GET["c"]))?(int) $_GET["c"]:0;
		$this->setRMore($id);
		if(isset($_GET["d"])&&in_array($_GET["d"],$q)){
			$h="กะที่ ".$id;
			$this->addDir("?a=time&amp;b=time_view&amp;c=".$id."",$h);
			$t=$_GET["d"];
			$this->r_more["active"]=$t;
				require_once("php/".$t.".php");
				eval("\$d=new ".$t."();");
				$d->dir=$this->dir;
				$d->c=$id;
				$d->r_more=$this->r_more;
				$d->run();
		}else{
			$this->defaultPage($id);
		}
	}
	private function defaultPage(int $id):void{
		$s=-1;
		$h="กะที่ ".$id;
		$d0=$this->getTime($id);
		$dt=$d0["time"];
		$dt["bill_sell_n"]=$d0["bill_sell_n"];
		//print_r($dt);
		$this->addDir("",$h);
		$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["time"],"js"=>["time","Ti"],"run"=>["Ti"],"r_more"=>$this->r_more]);
		echo '	<div class="content_rmore">
			<h1>'.$h.'</h1>';
		echo '<table>
			<tr><th>ค่า</th><th>รายละเอียด</th></tr>';
		if(1==1){$s+=1;
			$tst=($dt["stat"]==1)?'<b class="green">กำลังทำงาน</b>':'ปิดกะแล้ว';
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">สถานะ</td><td class="l">'.$tst.'</td></tr>';
		}
		if(!empty($dt["device_name"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">ชื่อเครื่อง</td><td class="l">'.htmlspecialchars($dt["device_name"]).'</td></tr>';
		}
		if(!empty($dt["ip"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">IP</td><td class="l">'.$dt["ip"].'</td></tr>';
		}
		if(1==1){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">ลิ้นชัก (ชื่อ)</td><td class="l">'.htmlspecialchars($dt["drawers_name"]).'</td></tr>';
		}
		if(1==1){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">ลิ้นชัก (รหัส)</td><td class="l">'.htmlspecialchars($dt["drawers_sku"]).'</td></tr>';
		}
		if(1==1){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">ผู้ใช้</td><td class="l">'.$dt["user_name"].'</td></tr>';
		}
		if(!empty($dt["money_start"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">เงินลิ้นชัก เปิดกะ</td><td class="l">฿ '.number_format($dt["money_start"],2,'.',',').'</td></tr>';
		}
		if(!empty($dt["money_balance"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">เงินลิ้นชัก ปิดกะ</td><td class="l">฿ '.number_format($dt["money_balance"],2,'.',',').'</td></tr>';
		}
		if(!empty($dt["date_reg"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">วันเวลา เปิดกะ</td><td class="l">'.$dt["date_reg"].'</td></tr>';
		}
		if(!empty($dt["date_exp"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">วันเวลา ปิดกะ</td><td class="l">'.$dt["date_exp"].'</td></tr>';
		}
		if(!empty($dt["dif"])){$s+=1;
			$tst=($dt["stat"]==1)?'<span class="bold green" id="time_ago">'.$this->ago2($dt["dif"]).'</span> ผ่านไป':$this->ago2($dt["dif"]);
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">ชัวโมงงาน hh:mm:ss</td><td class="l">'.$tst.'
			<script type="text/javascript">F.showTimeAgo(\'time_ago\',\''.$dt["date_reg"].'\')</script>
			</td></tr>';
		}
		if(1 == 1){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">จำนวนใบเสร็จที่ออก</td><td class="l">'.number_format($dt["bill_sell_n"],0,'.',',').'</td></tr>';
		}
		if(!empty($dt["note"])){$s+=1;
			echo '<tr class="i'.(($s%2)+1).'"><td class="l">หมายเหตุ</td><td class="l">'.$dt["note"].'</td></tr>';
		}
		echo '</table></div>';
		$this->pageFoot();
	}
	private function getTime(int $id):array{
		$re=["time"=>[],"bill_sell_n"=>0,"message_error"=>""];
		$sql=[];
		$sql["set"]="SELECT @ago:=(SELECT SUBDATE(NOW(),31)),
			@id:=".$id.",
			@ed:=(SELECT COUNT(*)FROM `time` WHERE `id` = @id);
		";
		$sql["bill_sell_n"]="SELECT COUNT(*)  AS `bill_sell_n` FROM `bill_sell` WHERE `time_id`=@id";
		$sql["time"]="
			IF @ed=1 THEN
				SELECT 0 AS `stat`,`time`.`id`,IFNULL(`device_pos`.`name`,'') AS `device_name` ,
					IFNULL(`time`.`money_start`,0) AS `money_start`,
					IFNULL(`time`.`money_balance`,0) AS `money_balance`,
					IFNULL(`time`.`note`,'') AS `note`,
					`time`.`ip`,`time`.`date_reg`,`time`.`date_exp`,
					TIMESTAMPDIFF(SECOND,`time`.`date_reg`,`time`.`date_exp`) AS `dif`,
					IFNULL(`device_drawers`.`name`,'') AS `drawers_name`,
					IFNULL(`device_drawers`.`sku`,'') AS `drawers_sku`,
					CONCAT(`user`.`name`,`user`.`lastname`) AS `user_name`
					FROM `time`
					LEFT JOIN `device_pos`
					ON(`time`.`ip`=`device_pos`.`ip`)
					LEFT JOIN `device_drawers`
					ON(`time`.`drawers_id`=`device_drawers`.`id`)
					LEFT JOIN `user`
					ON(`device_pos`.`user`=`user`.`sku_root`)
					WHERE `time`.`id`=@id;
			ELSE
				SELECT 1 AS `stat`,`device_pos`.`id`,IFNULL(`device_pos`.`name`,'') AS `device_name` ,
					IFNULL(`device_pos`.`money_start`,0) AS `money_start`,
					IFNULL(`device_pos`.`money_balance`,0) AS `money_balance`,
					'' AS `note`,
					`device_pos`.`ip`,`device_pos`.`date_reg`,'' AS `date_exp`,
					TIMESTAMPDIFF(SECOND,`device_pos`.`date_reg`,NOW()) AS `dif`,
					IFNULL(`device_drawers`.`name`,'') AS `drawers_name`,
					IFNULL(`device_drawers`.`sku`,'') AS `drawers_sku`,
					CONCAT(`user`.`name`,`user`.`lastname`) AS `user_name`
					FROM `device_pos`
					LEFT JOIN `device_drawers`
					ON(`device_pos`.`drawers_id`=`device_drawers`.`id`)
					LEFT JOIN `user`
					ON(`device_pos`.`user`=`user`.`sku_root`)
					WHERE `device_pos`.`time_id`=@id;			
			END IF;
		";
		$se=$this->metMnSql($sql,["bill_sell_n","time"]);
		if($se["result"]){
			if(isset($se["data"]["time"])){
				if(isset($se["data"]["time"][0])){
					$re["time"]=$se["data"]["time"][0];
				}
			}
			if(isset($se["data"]["bill_sell_n"])){
				if(isset($se["data"]["bill_sell_n"][0])){
					$re["bill_sell_n"]=$se["data"]["bill_sell_n"][0]["bill_sell_n"];
				}
			}
		}
		//print_r($se);
		return $re;
	}
	private function setRMore(int $id):void{
		$url="?a=time&amp;b=time_view&amp;c=".$id;
		$data=[
			"menu"=>[
				["b"=>"","name"=>"กะที่ ".$id,"link"=>$url],
				["b"=>"time_view_bill_sell_all","name"=>"ใบเสร็จรับเงิน (ขายสินค้า)","link"=>$url."&amp;d=time_view_bill_sell_all"]
				
			],
			"active"=>""
		];
		$this->r_more=$data;
	}
}
