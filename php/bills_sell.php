<?php
class bills_sell extends bills{
	public function __construct(){
		parent::__construct();
		$this->per=10;
		$this->page=1;
		$this->mb_type = [
			"s"=>["icon"=>"🏠","name"=>"ผู้ประกอบการ"],
			"p"=>["icon"=>"🧑","name"=> "ผู้บริโภค"]
		];
	}
	public function run(){
		$this->page=$this->setPageR();
		$q=["select","selectview","view","delete"];
		$this->addDir("?a=bills&amp;c=sell","ใบเสร็จรับเงิน");
		if(isset($_GET["b"])&&in_array($_GET["b"],$q)){
			$t=$_GET["b"];
			if($t=="view"&&(isset($_GET["sku"])&&preg_match("/^[0-9]{1,25}$/",$_GET["sku"]))){
				$this->pageView($_GET["sku"]);
			}else if($t=="selectview"){
				$this->pageSelectView($_GET["sku"]);
			}else if($t=="select"){
				$for=[];
				if(isset($_GET["for"])&&in_array($_GET["for"],$for)){
					$this->selectBillsSellPage($_GET["for"]);
				}else{
					$this->selectBillsSellPage();
				}
			}
		}else{
			$this->pageBillsSell();
		}
	}
	public function fetch(){
		if(isset($_POST["b"])&&$_POST["b"]=="delete"){
			require_once("php/bill_sell_".$_GET["b"].".php");
			eval("(new bill_sell_".$_GET["b"]."())->run();");
		}else{
			header('Content-type: application/json');
			print_r("{}");
		}
	}
	private function pageView(string $sku):void{
		$se=$this->getBillsSellList($sku);
		//print_r($se);
		if(count($se["head"])>0&&count($se["list"])>0){
			$this->addDir("?a=bills&amp;c=sell&amp;b=view&amp;sku=".$se["head"]["sku"],"เลขที่ ".$se["head"]["sku"]);
			$this->pageHead(["title"=>"ใบเสร็จ DIYPOS","xdir"=>true,"css"=>["bills_sell"],"js"=>["billsell","Bs"]]);
			
			$this->writeContentpageView($se["head"],$se["list"]);
			$this->pageFoot();
		}
	}
	private function writeContentpageView(array $head,array $list):void{
		//print_r($head);
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		echo '<div class="content">
			<h2 class="c">ใบเสร็จเลขที่ '.$head["sku"].'</h2>
			<div>';
		if($head["stat"]=="c"){
			
			
/*$reg=new DateTime($head["date_reg"]);
$mo=new DateTime($head["modi_date"]);
echo $mo->format('U')-$reg->format("U");*/

			echo '<div class="error">ใบเสร็จนี้ถูกยกเลิก โดย '.htmlspecialchars($head["user_name_edit"]).'
			หลังจากออกใบเสร็จไป '.$this->ago($head["dif"]).'
			</div>';
		}else if($head["stat"]=="r"){
			echo '<div class="warning">ใบเสร็จนี้มีคืนสินค้า โดย '.htmlspecialchars($head["user_name_edit"]).'
			หลังจากออกใบเสร็จไป '.$this->ago($head["dif"]).'
			</div>';
		}
		echo '	<div class="l"><b>👫 ผู้ขาย</b> : '.htmlspecialchars($head["user_name"]).' ,
				<b>🕒 วันที่</b> : '.$head["date_reg"].' น.
			</div>';

		
		if($head["member_sku"]!=""){
			$mbty="?";
			if(isset($this->mb_type[$head["mb_type"]])){
				$mbty=$this->mb_type[$head["mb_type"]]["icon"]." ".$this->mb_type[$head["mb_type"]]["name"];
			}
			echo '<div class="l"><b>🧾 ผู้ซื้อ</b> : <a href="?a=member&amp;b=details&amp;sku_root='.$head["member_sku_root"].'">'.htmlspecialchars($head["member_name"]).'</a> ,
				<b>ประเภท</b> : '.$mbty.' ,
				<b>รหัส</b> : '.$head["member_sku"];
		}else{
			echo '<div class="l">🧾 ผู้ซื้อ : บุคลทั่วไปไม่ใช่สมาชิก</div>';
		}
		$payu=json_decode($head["payu_json"],true);
		//print_r($payu);
		echo '	</div>
			<table class="billlsselllist">
				<tr>
					<th>ที่</th>
					<th>รหัสแท่ง</th>
					<th>สินค้า</th>
					<th>จำนวน</th>
					<th>หน่วย</th>
					<th>ราคา/น.</th>
					<th>รวม</th>
					<th>กำไร</th>
				</tr>';
				//print_r($head);
				$pf=0;
				$pfr=0;
				$nr_list=0;
				$nr=0;
				$sumr=0;
				$TEST="<br>";
				for($i=0;$i<count($list);$i++){
					if($list[$i]["c"]!=0){
						if($list[$i]["r"]>0){
							$nr+=1;
							if($list[$i]["r"]==$list[$i]["n"]){
								$nr_list+=1;
							}
						}
						$rt="";
						$cm=($list[$i]["sq"]%2!=1)?" class=\"i2\"":"";
						if($list[$i]["product_sku_root"]==$edd){
							$cm=' class="ed"';
						}
						if($list[$i]["r"]>0){
							$rt='<p class="saddlebrown l size12">คืน '.$list[$i]["n_r"].''.($list[$i]["s_type"]!="p"?"×".($list[$i]["n_wlv"]*1):"").' '.$list[$i]["unit_name"].' 📌 '.htmlspecialchars($list[$i]["note"]).'</p>';
						}
						$sum_r=($list[$i]["product_price"]*($list[$i]["c"]-$list[$i]["r"])*$list[$i]["n_wlv"]);
						$TEST.=$sum_r."<br>";
						if($list[$i]["c"]!=0){
							$pft=(($list[$i]["product_price"]-$list[$i]["product_lot_cost"]/$list[$i]["c"])*$list[$i]["c"]*$list[$i]["n_wlv"]);
						}else{
							$pft=0;
						}
						#$pftr=(($list[$i]["product_price"]-$list[$i]["product_lot_cost"]/($list[$i]["n"]))*($list[$i]["n"]-$list[$i]["r"]));
						$pftr=((($list[$i]["c"])*$list[$i]["product_price"])-$list[$i]["product_lot_cost"])*$list[$i]["n_wlv"]-(($list[$i]["product_price"]*$list[$i]["n_r"])-$list[$i]["product_lot_costr"])*$list[$i]["n_wlv"];
						$pf+=$pft;
						$pfr+=$pftr;
						$sumr+=$sum_r;
						$barcode=$list[$i]["product_barcode"];
						
						if($list[$i]["s_type"]!="p" && $barcode!==null){
							$barcode=$this->createBcWLV($barcode,$list[$i]["n_wlv"]*1);
						}
						echo '<tr'.$cm.'>
							<td>'.($list[$i]["sq"]).'</td>
							<td class="l">'.$barcode.'</td>
							<td><div>'.$list[$i]["product_name"].'
								'.($list[$i]["s_type"]!="p"?" ".($list[$i]["n_wlv"]*1)." ".$list[$i]["unit_name"]:"").'
								'.$rt.'</div>
								<div>'.$list[$i]["product_barcode"].'</div>
							</td>
							<td><div class="r">'.$list[$i]["c"].''.($list[$i]["s_type"]!="p"?"×".($list[$i]["n_wlv"]*1):"").'</div>
								<div>'.$list[$i]["unit_name"].'</div>
							</td>
							<td class="l">'.$list[$i]["unit_name"].'</td>
							<td class="r">'.number_format($list[$i]["product_price"],2,'.',',').'</td>
							<td class="r">'.number_format(($list[$i]["product_price"]*$list[$i]["c"]*$list[$i]["n_wlv"]),2,'.',',').'</td>
							<td class="darkgreen r">'.number_format($pft,2,'.',',').'</td>
						</tr>';
					}
				}
		echo '</table>
				<div>
					<div class="r">📃 จำนวน : <b>'.count($list).'</b> รายการ
						💰ยอดขาย : <b>'.$head["price"].'</b> บาท,กำไร <span class="green"><b>'.number_format($pf,2,'.',',').'</b></span> บ.</div>';
		if($nr>0){
				echo '<div class="r">*หักคืนสินค้า จะได้ 📃 จำนวน : <b>'.(count($list)-$nr_list).'</b> รายการ
						💰ยอดขาย : <b>'.$sumr.'</b> บาท,กำไร <span class="darkgreen"><b>'.number_format($pfr,2,'.',',').'</b></span> บ.</div>';
		}		
		//----------------
		echo '<div class="r"><b>💰 รูปแบบการชำระ</b> : [';
		$e=0;
		foreach($payu as $k=>$v){
			if($v["value"]>0){
				$e+=1;
				$cm=($e>1)?" ,":"";
				echo $cm.'<span>'.htmlspecialchars($v["name"]).' = '.number_format($v["value"],2,".",",").'</span>';
			}
		}		
		echo ']</div>';	
		echo '<div class="r"><b>ทอนเงินสด : </b>'.number_format($head["mout"],2,".",",").' บ.</div>';
		echo '</div>';
		//------------------
		echo '		<br /><img src="?a=bill58&amp;b=viewbill&amp;sku='.$head["sku"].'" class="imgbill" alt="ภาพใบเสร็จเลขที่ '.$head["sku"].'"  /><br />
			<a onclick="M.printAgain(\'bill58\',\'print\',\''.$head["sku"].'\')">🖨 พิมพ์อีกครั้ง</a><br /><br />
		</div></div>';		
		//echo $TEST;
		$this->stockCut($head["sku"]);
	}
	private function stockCut(string $sku):void{
		echo '<table><caption>การตัดสินค้า</caption>
			<tr><th>ที่</th><th>สินค้า</th><th>จำนวน</th><th>งวด</th><th>ตัดได้</th><th>ตัดไม่ได้</th></tr>';
		$dt=$this->getBillsSellListCut($sku);	
		//print_r($dt);
		$n=count($dt);
		$s=0;
		$at=1;
		$pd_root_now="";
		for($i=$n-1;$i>=0;$i--){
			$cm=($dt[$i]["sq"]%2!=1)?" class=\"i2\"":"";
			echo '<tr'.$cm.'>';
			if($i<$n&&$dt[$i]["product_sku_root"]!=$pd_root_now&&$i-1>=0){
				if($dt[$i]["product_sku_root"]==$dt[$i-1]["product_sku_root"]){
					$rowspan=$this->findRowSpan($dt,$i);
					$pd_root_now=$dt[$i]["product_sku_root"];
					echo '	<td rowspan="'.$rowspan.'">'.($at++).'</td>
						<td rowspan="'.$rowspan.'">'.$dt[$i]["name"].''.($dt[$i]["s_type"]=="p"?"":" ".($dt[$i]["n_wlv"]*1)." ".$dt[$i]["unit_name"]).'</td>
						<td class="r" rowspan="'.$rowspan.'">'.$dt[$i]["n"].''.($dt[$i]["s_type"]=="p"?"":"×".($dt[$i]["n_wlv"]*1)).'</td>';
				}else{
					$pd_root_now=$dt[$i]["product_sku_root"];
				echo '
				<td>'.($at++).'</td>
				<td>'.$dt[$i]["name"].''.($dt[$i]["s_type"]=="p"?"":" ".($dt[$i]["n_wlv"]*1)." ".$dt[$i]["unit_name"]).'</td>
				<td class="r">'.$dt[$i]["n"].''.($dt[$i]["s_type"]=="p"?"":"×".($dt[$i]["n_wlv"]*1)).'</td>';		
				}
			}else if($dt[$i]["product_sku_root"]!=$pd_root_now){
				$pd_root_now=$dt[$i]["product_sku_root"];
				echo '
				<td>'.($at++).'</td>
				<td>'.$dt[$i]["name"].''.($dt[$i]["s_type"]=="p"?"":" ".($dt[$i]["n_wlv"]*1)." ".$dt[$i]["unit_name"]).'</td>
				<td class="r">'.$dt[$i]["n"].''.($dt[$i]["s_type"]=="p"?"":"×".($dt[$i]["n_wlv"]*1)).'</td>';			
			}
			$tx="";
			$c="in";
			if($dt[$i]["in_type"]=="c"){
				$tx=$this->billNote("c",''.$dt[$i]["bill"],$dt[$i]["note"]);
				$c="sell";
			}else if($dt[$i]["in_type"]=="r"){
				$tx=$this->billNote("r",''.$dt[$i]["bill"],$dt[$i]["note"]);
				$c="ret";
			}else if($dt[$i]["in_type"]=="m"){
				$tx=$this->billNote("m",''.$dt[$i]["sku"],$dt[$i]["note"]);
				$c="move";
			}else if($dt[$i]["in_type"]=="b"){
				$tx=$this->billNote("b",''.$dt[$i]["note"],'');
				$c="in";
			}
			$key=$dt[$i]["sku"];
			if($dt[$i]["in_type"]=="c"){
				$key=$dt[$i]["bill"];
			}
			if($tx!=""){
				$tx='<a href="?a=bills&amp;b=view&amp;c='.$c.'&amp;sku='.$key.'&amp;ed='.$pd_root_now.'">'.$tx.'</a>';
			}else{
				$tx='<!--<a onclick="M.popup(this,\'Bs.selectLotCut(did,\\\''.$pd_root_now.'\\\')\')">-->ไม่พบใบนำเข้า<!--</a>-->';
			}
			
			echo '	<td>'.$tx.'</td>
				<td class="r">'.$dt[$i]["c"].''.($dt[$i]["s_type"]=="p"?"":"×".($dt[$i]["n_wlv"]*1)).'</td>
				<td class="r">'.$dt[$i]["u"].''.($dt[$i]["s_type"]!="p"&&$dt[$i]["u"]>0?"×".($dt[$i]["n_wlv"]*1):"").'</td>
			</tr>';
		}
		echo '</table>';
	}
	private function findRowSpan(array $dt,int $c):int{
		$rsp=1;
		for($i=$c;$i>0;$i--){
			if($dt[$i]["product_sku_root"]==$dt[$i-1]["product_sku_root"]){
				$rsp+=1;
			}else{
				break;
			}
		}
		return $rsp;
	}
	private function getBillsSellListCut($sku):array{
		$sku=$this->getStringSqlSet($sku);
		$re=[];
		$sql=[];
		$sql["cut"]="SELECT bill_sell_list.lot,bill_sell_list.product_sku_root,bill_sell_list.n,bill_sell_list.n_wlv,bill_sell_list.c,bill_sell_list.u,bill_sell_list.sq,
			product_ref.name,product_ref.s_type,
			bill_in.in_type,bill_in.sku,IFNULL(bill_in.note,'') AS `note`,bill_in.bill,unit_ref.name AS unit_name
			FROM bill_sell_list
			LEFT JOIN product_ref
			ON(bill_sell_list.product_sku_key=product_ref.sku_key)
			LEFT JOIN bill_in
			ON(bill_sell_list.lot=bill_in.sku)
			LEFT JOIN unit_ref
			ON(bill_sell_list.unit_sku_key=unit_ref.sku_key)
			WHERE bill_sell_list.sku=".$sku." 
			ORDER BY bill_sell_list.id DESC
		";
		$se=$this->metMnSql($sql,["cut"]);
		//print_r($se);
		if($se["result"]){
			$re=$se["data"]["cut"];
		}
		return $re;
	}
	private function getBillsSellList($sku):array{
		$re=["head"=>[],"list"=>[]];
		$sku=$this->getStringSqlSet($sku);
		$sql=[];
		$sql["head"]="SELECT  `bill_sell`.`sku`  AS  `sku`,`bill_sell`.`n`  AS  `n`, `bill_sell`.`stat`  AS  `stat`, 
				`bill_sell`.`price` AS `price`,bill_sell.w, `bill_sell`.`modi_date` AS `modi_date`, 
				`bill_sell`.`mout` AS `mout`,
				GetPayuArrRefData_(`bill_sell`.`payu_json`) AS `payu_json`,
				`bill_sell`.`date_reg` AS `date_reg`,
				CONCAT(`user_ref`.`name`,' ', `user_ref`.`lastname`) AS `user_name`,
				CONCAT(`user_ref2`.`name`,' ', `user_ref2`.`lastname`) AS `user_name_edit`,
				TIMESTAMPDIFF(SECOND,bill_sell.date_reg,bill_sell.modi_date) AS dif,
				IFNULL(NVL2(`member_ref`.`id`,CONCAT(`member_ref`.`name`,' ', `member_ref`.`lastname`),''),'') AS `member_name`,
				IFNULL(`member_ref`.`mb_type`,'') AS `mb_type`,IFNULL(`member_ref`.`sku`,'') AS `member_sku`,
				`member_ref`.`sku_root` AS `member_sku_root`
			FROM `bill_sell` 
			LEFT JOIN `user_ref`
			ON( `bill_sell`.`user`=`user_ref`.`sku_key`)
			LEFT JOIN `user_ref` AS user_ref2
			ON( `bill_sell`.`user_edit`=`user_ref`.`sku_key`)
			LEFT JOIN `member_ref`
			ON( `bill_sell`.`member_sku_key`=`member_ref`.`sku_key`)
			WHERE bill_sell.sku=".$sku." LIMIT 1
		";
		$sql["list"]="SELECT  
				bill_sell_list.product_sku_root AS product_sku_root,bill_sell_list.n AS n,bill_sell_list.n_wlv AS n_wlv,`bill_sell_list`.`c`  AS  `c`,bill_sell_list.r AS r,IFNULL(bill_sell_list.note,'') AS `note`,
				product_ref.name AS product_name,product_ref.barcode AS product_barcode,product_ref.cost AS product_cost,product_ref.price AS product_price,
				SUM(bill_sell_list.r) AS `n_r`,bill_sell_list.sq,
				unit_ref.name AS unit_name,
				SUM(IFNULL((bill_in_list.sum/(IF(bill_in_list.s_type='p',bill_in_list.n,bill_in_list.n_wlv))),product_ref.cost)*IF(bill_sell_list.c>0,bill_sell_list.c,bill_sell_list.u)) AS product_lot_cost,
				SUM(IFNULL((bill_in_list.sum/(IF(bill_in_list.s_type='p',bill_in_list.n,bill_in_list.n_wlv))),product_ref.cost)*bill_sell_list.r) AS product_lot_costr,
				product_ref.s_type
				
			FROM `bill_sell_list` 
			LEFT JOIN product_ref
			ON( `bill_sell_list`.`product_sku_key`=`product_ref`.`sku_key`)
			LEFT JOIN unit_ref
			ON( bill_sell_list.unit_sku_key=unit_ref.sku_key)
			LEFT JOIN bill_in_list
			ON(bill_sell_list.bill_in_list_id=bill_in_list.id)
			WHERE bill_sell_list.sku=".$sku."
			GROUP BY bill_sell_list.product_sku_root,bill_sell_list.id
			ORDER BY `bill_sell_list`.`id` ASC";
		$se=$this->metMnSql($sql,["head","list"]);
		//print_r($se);
		if($se["result"]&&isset($se["data"]["head"][0])){
			$re["head"]=$se["data"]["head"][0];
			$re["list"]=$se["data"]["list"];
		}
		return $re;
	}
	private function pageBillsSell():void{
		$this->pageHead(["title"=>"ใบเสร็จรับเงิน DIYPOS","js"=>[],"css"=>["bills_sell"],"js"=>["billsell","Bs"]]);
		echo '<div class="content">
				<div class="form">
					<h1 class="c">ใบเสร็จรับเงิน</h1>';
			$this->writeContentBillsSell();
			echo '</div></div>';
		$this->pageFoot();
	}
	private function writeContentBillsSell():void{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$sea=$this->getAllBillsSell();
		$se=$sea["row"];
		echo '<form class="form100" name="billsin" method="post">
			<input type="hidden" name="sku" value="" />
			<table class="billssell"><tr><th>ที่</th>
			<th>วันบันทึก</th>			
			<th  class="showhide">เลขที่</th>		
			<th>รก.</th>
			<th>จำนวนเงิน</th>
			<th>กำไร</th>
			<th>ผู้บันทึก</th>

			<th>กระทำ</th>
			</tr>';
		for($i=0;$i<count($se);$i++){
			$ed='';
			if($se[$i]["sku"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}
			$wn="";
			if($se[$i]["w"]=="1"){
				$wn="⚠️";
			}
			$stat="";
			if($se[$i]["stat"]=="c"){
				$stat=' <apan class="red bold">[ถูกยกเลิก]</span>';
			}else if($se[$i]["stat"]=="r"){
				$stat=' <apan class="saddlebrown bold">[มีคืนสินค้า]</span>';
			}
			$cm=($i%2!=0)?" class=\"i2\"":"";
			$pf=0;
			if($se[$i]["stat"]!="c"){
				$pf=$se[$i]["price"]-$se[$i]["pricer"]-($se[$i]["cost"]-$se[$i]["costr"]);
			}
			$mb=htmlspecialchars($this->setMemberTxt($se[$i]["member_name"],$se[$i]["mb_type"],""));
			$s_credit="";
			if($se[$i]["credit"]>0){
				$s_credit=' <span class="s_credit">👎 '.number_format($se[$i]["credit"],2,".",",").'</span>';
			}
			echo '<tr'.$cm.'><td>'.$se[$i]["id"].'</td>
				<td>
					<div><a href="?a=bills&amp;c=sell&amp;b=view&amp;sku='.$se[$i]["sku"].'" title="ดูรายละเอียด">'.$se[$i]["sku"].''.$wn.''.$stat.'</a>'.$s_credit.'</div>
					<div>'.$se[$i]["date_reg"].'</div>
					<div>'.$se[$i]["user_name"].'</div>
					<div>'.$mb.'</div>
				</td>
				<td  class="showhide l"><a href="?a=bills&amp;c=sell&amp;b=view&amp;sku='.$se[$i]["sku"].'" title="ดูรายละเอียด">'.$se[$i]["sku"].''.$wn.''.$stat.'</a>'.$s_credit.'<div>'.$mb.'</div></td>
				<td class="r">'.$se[$i]["n"].'</td>
				<td class="r">'.number_format($se[$i]["price"],2,'.',',').'</td>
				<td class="r darkgreen">'.number_format($pf,2,'.',',').'</td>
				<td class="l">'.$se[$i]["user_name"].'</td>

				<td class="action">
					<a data-sku="'.$se[$i]["sku"].'" onclick="Bs.delete(this)" title="ทิ้ง">🗑</a>
					'.$ed.'
					</td>
				</tr>';
		}
		echo '</table></form>';
			$count=(isset($sea["count"]))?$sea["count"]:0;
			$this->page($count,$this->per,$this->page,"?a=bills&amp;c=sell&amp;page=");
	}
	private function getAllBillsSell():array{
		$re=[];
		$sql=[];
		$sql["count"]="SELECT @count:=bs_c FROM `s` WHERE `tr`=1";
		$sql["get"]="SELECT  `bill_sell`.`id`  AS  `id`,`bill_sell`.`sku`  AS  `sku`,`bill_sell`.`n`  AS  `n`,`bill_sell`.`stat`  AS  `stat`,
				`bill_sell`.`cost`,`bill_sell`.`costr`,`bill_sell`.`price` AS `price`,`bill_sell`.`pricer` AS `pricer`,IFNULL(bill_sell.w,0) AS `w`, 
				IFNULL(`bill_sell`.`credit`,0) AS `credit`,
				`bill_sell`.`date_reg` AS `date_reg`,
				CONCAT(`user_ref`.`name`,' ', `user_ref`.`lastname`) AS `user_name`,
				IFNULL(NVL2(`member_ref`.`id`,CONCAT(`member_ref`.`name`,' ', `member_ref`.`lastname`),''),'') AS `member_name`,
				IFNULL(`member_ref`.`mb_type`,'') AS `mb_type`
			FROM `bill_sell` 
			LEFT JOIN `user_ref`
			ON( `bill_sell`.`user`=`user_ref`.`sku_key`)
			LEFT JOIN `member_ref`
			ON( `bill_sell`.`member_sku_key`=`member_ref`.`sku_key`)
			ORDER BY `bill_sell`.`id` DESC LIMIT ".(($this->page-1)*$this->per).",".$this->per."";
		$sql["result"]="SELECT @count AS count";
		$se=$this->metMnSql($sql,["get","result"]);
		if($se["result"]){
			$re=["row"=>$se["data"]["get"],"count"=>$se["data"]["result"][0]["count"]];
		}
		//print_r($se);
		return $re;
	}
	private function selectBillsSellPage(string $for=null){
		$sea=$this->getAllBillsSell($for);
		$se=$sea["row"];
		$this->pageHead(["title"=>"ใบเสร็จ DIYPOS","dir"=>false,"css"=>["bills_sell"],"js"=>["billsell","Bs"]]);
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		//$se=$this->getAllBillsSell();
		echo '<form class="form100" name="billsin" method="post">
			<input type="hidden" name="sku" value="" />
			<table class="billssellsell"><tr><th>ที่</th>
			<th>วันบันทึก</th>			
			<th  class="showhide">เลขที่</th>		
			<th>รายการ</th>
			<th>จำนวนเงิน</th>
			<th>ผู้บันทึก</th>

			<th>กระทำ</th>
			</tr>';
		for($i=0;$i<count($se);$i++){
			$ed='';
			if($se[$i]["sku"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}
			$stat="";
			if($se[$i]["stat"]=="c"){
				$stat=' <span class="red bold">[ถูกยกเลิก]</span>';
			}else if($se[$i]["stat"]=="r"){
				$stat=' <span class="saddlebrown bold"> [มีคืนสินค้า]</span>';
			}
			$mb=htmlspecialchars($this->setMemberTxt($se[$i]["member_name"],$se[$i]["mb_type"],""));
			$cm=($i%2!=0)?" class=\"i2\"":"";
			$s_credit="";
			if($se[$i]["credit"]>0){
				$s_credit=' <span class="s_credit">👎 '.number_format($se[$i]["credit"],2,".",",").'</span>';
			}
			echo '<tr'.$cm.'><td>'.$se[$i]["id"].'</td>
				<td>
					<div><a href="?a=bills&amp;c=sell&amp;b=selectview&amp;sku='.$se[$i]["sku"].'" title="ดูรายละเอียด">'.$se[$i]["sku"].''.$stat.'</a>'.$s_credit.'</div>
					<div>'.$se[$i]["date_reg"].'</div>
					<div>'.$se[$i]["user_name"].'</div>
					<div>'.$mb.'</div>
				</td>
				<td  class="showhide l"><a href="?a=bills&amp;c=sell&amp;b=selectview&amp;sku='.$se[$i]["sku"].'" title="ดูรายละเอียด">'.$se[$i]["sku"].''.$stat.'</a>'.$s_credit.'<div>'.$mb.'</div></td>
				<td class="r">'.$se[$i]["n"].'</td>
				<td class="r">'.number_format($se[$i]["price"],2,'.',',').'</td>
				<td class="l">'.$se[$i]["user_name"].'</td>

				<td class="action c">
					<a data-sku="'.$se[$i]["sku"].'" onclick="Bs.delete(this)" title="ทิ้ง">🗑</a>
					'.$ed.'
					</td>
				</tr>';
		}
		echo '</table></form>';
		$count=(isset($sea["count"]))?$sea["count"]:0;
		$this->page($count,$this->per,$this->page,"?a=bills&amp;b=select&amp;c=sell&amp;page=");
		$this->pageFoot();
	}
	private function setMemberTxt(string $name="",string $type="",string $default=""):string{
		$mb=$default;
		if($name!=""){
			if(isset($this->mb_type[$type])){
				$mb=$this->mb_type[$type]["icon"]." ".$name;
			}
		}
		return $mb;
	}
	private function pageSelectView(string $sku):void{
		$se=$this->getBillsSellList($sku);
		if(count($se["head"])>0&&count($se["list"])>0){
			$this->pageHead(["title"=>"ใบเสร็จ DIYPOS","dir"=>false,"css"=>["bills_sell"]]);
			echo '<div class="billssellback"><a onclick="history.back()">🔙 กลับ</a></div>';
			$this->writeContentpageView($se["head"],$se["list"]);
			$this->pageFoot();
		}
	}
	private function defaultSearch():string{
		$fla=["id","date_start","date_start","username"];
		$fl="name";
		$tx="";
		$se="";
		if(isset($_GET["fl"])){
			if(in_array($_GET["fl"],$fla)){
				$fl=$_GET["fl"];
			}
		}
		if(isset($_GET["tx"])&&strlen(trim($_GET["tx"]))>0){
			$tx=$this->getStringSqlSet($_GET["tx"]);
		}
		if($tx!=""){
			$tx=substr($tx,1,-1);
			$se=" WHERE `product`.`".$fl."` LIKE  \"%".$tx."%\"  ";
		}
		return $se;
	}
}
?>
