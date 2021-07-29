<?php
class time_view_bill_sell_all extends main{
	public function __construct(){
		parent::__construct();
		$this->title = "ใบเสร็จรับเงิน (ขายสินค้า)";
		$this->a = "time";
		$this->c = 0;
		$this->r_more=[];
	}
	public function run(){
		$var_sum="1";
		$var_get=[];
		$var_chg="";
		$pay=[];
		$dt0=$this->getBillSellAll();
		$dt=$dt0["get"];
		$this->addDir("",$this->title);
		$this->pageHead(["title"=>$this->title." DIYPOS","css"=>["time"],"js"=>["time","Ti"],"run"=>["Ti"],"r_more"=>$this->r_more]);
		$today=date('Y-m-d') ;//== date('Y-m-d', strtotime($timestamp));
		$yesterday= Date('Y-m-d', strtotime('-1 day'));
		$date="";
		echo '<div class="content_rmore">';
		echo '<h2>'.$this->title.' กะที่ '.$this->c.'</h2>';
		echo '<table>
			<tr>
				<th>ที่</th>
				<th>เวลา</th>
				<th>เลขที่ใบเสร็จ</th>
				<th>👥</th>
				<th>ชำระโดย</th>
				<th>จำนวนเงิน</th>
			</tr>';
		$s=0;
		$q=0;
		$all_pay=0;
		$all_chg=0;
		$all_pay_credit_bill=0;
		for($i=count($dt)-1;$i>=0;$i--){
			$all_pay+=(float) $dt[$i]["price"];
			$all_chg+=(float) $dt[$i]["mout"];
			$s+=1;
			$d=explode(" ",$dt[$i]["date_reg"]);
			$pd=json_decode($dt[$i]["payu_ref_json"],true);
			$pay_money="";
			foreach($pd as $k=>$v){
				$ky=$v["sku_root"];
				$vy=$v["value"];
				if(!isset($pay[$ky])){
					$pay[$ky]=["name"=>$v["name"],"count"=>1,"sum"=>$vy];
				}else{
					$pay[$ky]["count"]+=1;
					$pay[$ky]["sum"]+=(float) $vy;
				}
				$pr0=number_format($vy,2,".",",");
				$pr=(substr($pr0,-3,3)==".00")?(int) $vy:$pr0;
				if($ky=="defaultroot"){
					$pay_money.='<span class="paycolor1">'.$pr.'</span>';
				}else if($ky=="creditroot"){
					$all_pay_credit_bill+=1;
					$pay_money.='<span class="paycolor2">'.$pr.'</span>';
				}else{
					$pay_money.='<span class="paycolor3">'.$pr.'</span>';
				}
			}
			if($d[0]!=$date){
				$q=1;
				
				if($d[0]==$today){
					echo '<tr><td colspan="7" class="time_log_date_th">↓ วันนี้</td></tr>';
				}else if($d[0]==$yesterday){
					echo '<tr><td colspan="7" class="time_log_date_th">↓ เมื่อวานนี้</td></tr>';
				}else{
					echo '<tr><td colspan="7" class="time_log_date_th">↓ '.$d[0].'</td></tr>';
				}
				$date=$d[0];
			}
			$q+=1;
			$tr=($q%2)+1;			
			$mem=$dt[$i]["member_sku_root"]!=""?"👥":"";
			echo '<tr class="i'.$tr.'">
				<td class="r">'.($s).'</td>
				<td class="l">'.substr($d[1],0,5).'</td>
				<td class="l"><a href="?a=bills&amp;c=sell&amp;b=view&amp;sku='.$dt[$i]["sku"].'">'.$dt[$i]["sku"].'</a></td>
				<td class="c">'.($mem).'</td>
				<td class="r">'.($pay_money).'</td>
				<td class="r">'.number_format($dt[$i]["price"],2,".",",").'</td>
			</tr>';
		}
		$e=0;
		$count_bill=count($dt)>0?count($dt):1;
		echo '</table>';
		echo '<br /><table class="select" style="max-width:640px;">
			<tr><th colspan="3">สรุปย่อ</th></tr>
			<tr class="i'.((($e++)%2)+1).'">
				<td class="l">จำนวนใบเสร็จที่ออก</td>
				<td class="r">'.number_format(count($dt),0,".",",").'</td>
				<td class="r">ใบ</td>
			</tr>
			<tr class="i'.((($e++)%2)+1).'">
				<td class="l"><span class="var var_sum">1</span> จำนวนยอดชำระทั้งหมด</td>
				<td class="r">'.number_format($all_pay,2,".",",").'</td>
				<td class="r"> บาท</td>
			</tr>
			<tr class="i'.((($e++)%2)+1).'">
				<td class="l">เฉลี่ยอดชำระต่อ 1 ใบเสร็จ</td>
				<td class="r">'.number_format($all_pay/$count_bill,2,".",",").'</td>
				<td class="r"> บาท</td>
			</tr>';
		foreach($pay as $k=>$v){
			echo '<tr class="i'.((($e++)%2)+1).'">
				<td class="l">จำนวนใบเสร็จที่มีชำระโดย '.$v["name"].'</td>
				<td class="r">'.number_format($v["count"],0,".",",").'</td>
				<td class="r"> ใบ</td>
			</tr>';
		}
		$var_n=1;
		foreach($pay as $k=>$v){
			$var_n+=1;
			array_push($var_get,(string)$var_n);
			echo '<tr class="i'.((($e++)%2)+1).'">
				<td class="l"><span class="var var_pay">'.$var_n.' </span> รับ '.$v["name"].'</td>
				<td class="r">'.number_format($v["sum"],2,".",",").'</td>
				<td class="r"> บาท</td>
			</tr>';
		}
		$var_n+=1;
		$var_chg=(string) $var_n;
		echo '<tr class="i'.((($e++)%2)+1).'">
			<td class="l"><span class="var var_change">'.($var_n).' </span> ทอน เงินสด</td>
			<td class="r">'.number_format($all_chg,2,".",",").'</td>
			<td class="r"> บาท</td>
		</tr>';
		echo '</table>';

		echo '<br><p class="c">';
			$q=0;
			foreach($var_get as $k=>$v){
				$q+=1;
				$ps=($q>1)?" + ":"";
				echo $ps.'<span class="var var_pay">'.$v.'</span>';
			}
			echo ' - <span class="var var_sum">'.$var_sum.'</span>';
			echo ' = <span class="var var_change">'.$var_chg.'</span>';
		echo '</p>';

		echo '<br><p class="c">';
			foreach($pay as $k=>$v){
				$i=3;
				if($k=="defaultroot"){
					$i=1;
				}else if($k=="creditroot"){
					$i=2;
				}
				echo '<span class="money_log_note_disc"><span class="paycolor'.$i.'">dd</span> = '.$v["name"].'</span>';
			}
		echo '</p>';
		echo '</div>';
		//print_r($pay);
		$this->pageFoot();
	}
	private function getBillSellAll():array{
		$re=["get"=>[],"message_error"=>""];
		$sql=[];
		$sql["set"]="SELECT 
			@id:=".$this->c.";
		";
		$sql["get"]="
			SELECT `bill_sell`.`sku`		,`bill_sell`.`price`		,`bill_sell`.`mout`,
					IFNULL(`bill_sell`.`member_sku_root`,'') AS `member_sku_root`,	
					GetPayuArrRefData_(IFNULL(`bill_sell`.`payu_ref_json`,'{}')) AS `payu_ref_json`,
					`bill_sell`.`date_reg`
				FROM `bill_sell`
				WHERE `bill_sell`.`time_id`=@id AND `bill_sell`.`id` >=65
				ORDER BY `bill_sell`.`id` DESC;
		";
		$se=$this->metMnSql($sql,["get"]);
		if($se["result"]){
			if(isset($se["data"]["get"])){
				if(isset($se["data"]["get"])){
					$re["get"]=$se["data"]["get"];
				}
			}
		}
		//print_r($se);
		return $re;
	}
}
