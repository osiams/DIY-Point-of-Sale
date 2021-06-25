<?php
class time_all extends main{
	public function __construct(){
		parent::__construct();
		$this->title = "กะ";
		$this->a = "time";
		$this->addDir("?a=".$this->a."&amp;b=time_all",$this->title);
	}
	public function run(){
		$this->defaultPage();
	}
	private function defaultPage():void{
		$dt0=$this->getTimeAll();
		$dt=$dt0["now"];
		$du=$dt0["ago"];
		$today=date('Y-m-d') ;
		$yesterday= Date('Y-m-d', strtotime('-1 day'));
		$date="";
		$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["time"],"js"=>["time","Ti"],"run"=>["Ti"]]);
		echo '	<div class="content">
			<h1>'.$this->title.'</h1>';
		echo '<table>
			<caption>ที่กำลังทำงาน</caption>
			<tr><th>กะที่</th><th>IP/ชื่อเครื่อง</th><th>ผู้ใช</th><th>เวลาเปิด</th></tr>';
		$to=[];
		for($i=0;$i<count($dt);$i++){
			$tr=($i%2)+1;
			$id="time_ago".$dt[$i]["time_id"];
			$to[$id]=$dt[$i]["date_reg"];
			echo '<tr class="i'.$tr.'">
				<td class="l"><a href="?a=time&amp;b=time_view&amp;c='.($dt[$i]["time_id"]).'">'.($dt[$i]["time_id"]).'</a></td>
				<td class="l"><a href="?a=device&amp;b=pos&amp;c=details&amp;ip='.$dt[$i]["ip"].'">'.$dt[$i]["ip"].'</a><br /> '.htmlspecialchars($dt[$i]["device_name"]).'</td>
				<td class="l">'.htmlspecialchars($dt[$i]["user_name"]).'</td>
				<td class="r">'.$dt[$i]["date_reg"].'<br><span class="bold green" id="'.$id.'"></span> ผ่านไป</td>
			</tr>';
		}
		echo '</table>';
		echo '<script type="text/javascript">';
		foreach($to as $k=>$v){
			echo 'F.showTimeAgo(\''.$k.'\',\''.$v.'\');';
		}
		echo '</script>';
		echo '<br /><table>
			<caption>กะที่ปิดแล้ว 31 วันล่าสุด</caption>
			<tr><th>กะที่</th><th>IP/ชื่อเครื่อง</th><th>ผู้ใช</th><th>เปิด/ปิด/ชั่งโมงงาน</th></tr>';
		$q=0;
		for($i=0;$i<count($du);$i++){
			$d=explode(" ",$du[$i]["date_exp"]);
			if($d[0]!=$date){
				$q=1;
				if($d[0]==$today){
					echo '<tr><td colspan="4" class="time_log_date_th">↓ วันนี้</td></tr>';
				}else if($d[0]==$yesterday){
					echo '<tr><td colspan="4" class="time_log_date_th">↓ เมื่อวานนี้</td></tr>';
				}else{
					echo '<tr><td colspan="4" class="time_log_date_th">↓ '.$d[0].'</td></tr>';
				}
				$date=$d[0];
			}	
			$q+=1;
			$tr=($q%2)+1;
			echo '<tr class="i'.$tr.'">
				<td class="l"><a href="?a=time&amp;b=time_view&amp;c='.($du[$i]["id"]).'">'.($du[$i]["id"]).'</a></td>
				<td class="l"><a href="?a=device&amp;b=pos&amp;c=details&amp;ip='.$du[$i]["ip"].'">'.$du[$i]["ip"].'</a><br /> '.htmlspecialchars($du[$i]["device_name"]).'</td>
				<td class="l">'.htmlspecialchars($du[$i]["user_name"]).'</td>
				<td class="r">'.$du[$i]["date_reg"].'<br />'.$du[$i]["date_exp"].'<br><span class="bold blue">'.$this->ago2($du[$i]["dif"]).'</span></td>
			</tr>';
		}
		echo '</table>';
		echo '</div>';
		$this->pageFoot();
	}
	private function getTimeAll():array{
		$re=["now"=>[],"ago"=>[]];
		$sql=[];
		$sql["set"]="SELECT @ago:=(SELECT SUBDATE(NOW(),31))";
		$sql["get"]="
			SELECT `device_pos`.`time_id`,IFNULL(`device_pos`.`name`,'') AS `device_name` ,
				`device_pos`.`ip`,`device_pos`.`date_reg`,
				TIMESTAMPDIFF(SECOND,`device_pos`.`date_reg`,NOW()) AS `dif`,
				CONCAT(`user`.`name`,`user`.`lastname`) AS `user_name`
				FROM `device_pos` 
				LEFT JOIN `user`
				ON(`device_pos`.`user`=`user`.`sku_root`)
				WHERE `device_pos`.`onoff`='1' ORDER BY  `device_pos`.`date_reg`DESC;
		";
		$sql["geted"]="
			SELECT `time`.`id`,IFNULL(`device_pos`.`name`,'') AS `device_name` ,
				`time`.`ip`,`time`.`date_reg`,`time`.`date_exp`,
				TIMESTAMPDIFF(SECOND,`time`.`date_reg`,`time`.`date_exp`) AS `dif`,
				CONCAT(`user`.`name`,`user`.`lastname`) AS `user_name`
				FROM `time`
				LEFT JOIN `device_pos`
				ON(`time`.`ip`=`device_pos`.`ip`)
				LEFT JOIN `user`
				ON(`device_pos`.`user`=`user`.`sku_root`)
				WHERE `time`.`date_reg` > @ago
				ORDER BY `time`.`date_reg` DESC;
		";
		$se=$this->metMnSql($sql,["get","geted"]);
		if($se["result"]){
			if(isset($se["data"]["get"])){
				$re["now"]=$se["data"]["get"];
			}
			if(isset($se["data"]["geted"])){
				$re["ago"]=$se["data"]["geted"];
			}
		}
		//print_r($se);
		return $re;
	}
}
