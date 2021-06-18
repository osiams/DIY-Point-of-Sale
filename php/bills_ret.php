<?php
class bills_ret extends bills{
	public function __construct(){
		parent::__construct();
		$this->per=20;
		$this->page=1;
	}
	public function run(){
		$this->page=$this->setPageR();
		$q=["select","selectview","view","delete"];
		$this->addDir("?a=bills&amp;c=ret","ใบคืนสินค้า");
		if(isset($_GET["b"])&&in_array($_GET["b"],$q)){
			$t=$_GET["b"];
			if($t=="view"&&isset($_GET["sku"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["sku"])){
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
			$this->pageBillsRet();
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
		$se=$this->getBillsRetList($sku);
		//print_r($se);
		if(count($se["list"])>0&&count($se["head"])>0){
			//$rt=preg_replace("/\\D/","", $se["head"][0]["date_reg"])."-".$se["head"][0]["bill"];
			$rt=$se["head"][0]["sku"];
			$this->addDir("?a=bills&amp;c=ret&amp;b=view&amp;sku=".$sku,"เลขที่ ".$rt);
			$this->pageHead(["title"=>"ใบคืนสินค้า DIYPOS","xdir"=>true,"css"=>["ret"]]);
			$this->writeContentpageView($sku,$rt,$se["head"],$se["list"]);
			$this->pageFoot();
		}
	}
	private function writeContentpageView(string $sku,string $rt,array $head,array $list):void{
		$ed=(isset($_GET["ed"]))?$_GET["ed"]:"";
		echo '<div class="content">
			<h2 class="c">ใบคืนสินค้าเลขที่ '.$rt.'</h2>
			<div>';
		echo '	<div class="l">👫 ผู้รับคืน : '.htmlspecialchars($head[0]["user_name"]).'
				🕒วันที่ : '.$head[0]["date_reg"].' น. <a href="?a=bills&c=sell&b=view&sku='.$head[0]["bill"].'">🧾ใบซื้อเลขที่ '.$head[0]["bill"].'</a>
			</div>
			<table class="billretlist">
				<tr>
					<th>ที่</th>
					<th>ป.</th>
					<th>รหัสแท่ง</th>
					<th>สินค้า</th>
					<th>จำนวน</th>
					<th>หน่วย</th>
					<th>ราคา/น.</th>
					<th>รวม</th>
				</tr>';
				$prices=0;
				for($i=0;$i<count($list);$i++){
					$rt="";
					$cm=($i%2!=0)?" class=\"i2\"":"";
					if($list[$i]["product_sku_root"]==$ed){
						$cm=' class="ed"';
					}
					echo '<tr'.$cm.'>
						<td>'.($i+1).'</td>
						<td class="pwlv">'.$this->s_type[$list[$i]["s_type"]]["icon"].'</td>
						<td>'.$list[$i]["product_barcode"].'</td>
						<td class="l"><div>'.$list[$i]["product_name"].''.($list[$i]["s_type"]!='p'?" ".($list[$i]["n_wlv"])." ".$list[$i]["unit_name"]:"").''.$rt.'</div>
							<div class="l gray555 size12"><span class="pwlv">'.$this->s_type[$list[$i]["s_type"]]["icon"].'</span>
								'.htmlspecialchars($list[$i]["product_barcode"]).'</div>
							<div class="l saddlebrown size12">'.htmlspecialchars($list[$i]["note"]).'</div>
						</td>
						<td><div class="r">'.$list[$i]["n"].''.($list[$i]["s_type"]!='p'?"×".($list[$i]["n_wlv"]*1):"").'</div>
						<div class="r">'.$list[$i]["unit_name"].'</div>
						</td>
						<td>'.$list[$i]["unit_name"].'</td>
						<td class="r">'.number_format($list[$i]["product_price"],2,'.',',').'</td>
						<td class="r">'.number_format(($list[$i]["product_price"]*$list[$i]["n"]*($list[$i]["s_type"]=='p'?1:$list[$i]["n_wlv"])),2,'.',',').'</td>
					</tr>';
					$prices+=$list[$i]["product_price"]*$list[$i]["n"]*($list[$i]["s_type"]=='p'?1:$list[$i]["n_wlv"]);
				}
		echo '</tr></table>
					<div class="r">📃 จำนวน : <b>'.count($list).'</b> รายการ
						💰รวมเงิน : <b>'.number_format($prices,2,'.',',').'</b> บาท
					</div><br /><img src="?a=bill58&amp;b=viewret&amp;sku='.$sku.'" class="imgbill" alt="ใบคืนสินค้าเลขที่ '.$sku.'" /><br />
					<a onclick="M.printAgain(\'bill58\',\'print_ret\',\''.$sku.'\')">🖨 พิมพ์อีกครั้ง</a><br />
					<br />
		</div></div>';		
		//$this->stockCut($head["sku"]);
	}
	private function stockCut(string $sku):void{
		echo '<table><caption>การตัดสินค้า</caption>
			<tr><th>ที่</th><th>สินค้า</th><th>จำนวน</th><th>งวด</th><th>ตัดได้</th><th>ตัดไม่ได้</th></tr>';
		$dt=$this->getBillsSellListCut($sku);	
		//print_r($dt);
		$n=count($dt);
		$s=0;
		$pd_root_now="";
		for($i=$n-1;$i>=0;$i--){
			$cm=($s++%2!=0)?" class=\"i2\"":"";
			echo '<tr'.$cm.'>';
			if($i<$n&&$dt[$i]["product_sku_root"]!=$pd_root_now&&$i-1>=0){
				if($dt[$i]["product_sku_root"]==$dt[$i-1]["product_sku_root"]){
					$rowspan=$this->findRowSpan($dt,$i);
					$pd_root_now=$dt[$i]["product_sku_root"];
					echo '	<td rowspan="'.$rowspan.'">'.($n-$i).'</td>
						<td rowspan="'.$rowspan.'">'.$dt[$i]["name"].'</td>
						<td rowspan="'.$rowspan.'">'.$dt[$i]["n"].'</td>';
				}else{
					$pd_root_now=$dt[$i]["product_sku_root"];
					echo '
					<td>'.($n-$i).'</td>
					<td>'.$dt[$i]["name"].'</td>
					<td>'.$dt[$i]["n"].'</td>';			
				}
			}else if($dt[$i]["product_sku_root"]!=$pd_root_now){
				$pd_root_now=$dt[$i]["product_sku_root"];
				echo '
				<td>'.($n-$i).'</td>
				<td>'.$dt[$i]["name"].'</td>
				<td>'.$dt[$i]["n"].'</td>';			
			}
			$tx=$this->billNote("b",$dt[$i]["note"]);
			$tx=htmlspecialchars($tx);
			if($dt[$i]["in_type"]=="c"){
				$tx=$this->billNote("c",'ยกเลิกใบเสร็จ <a href="?a=bills&amp;b=view&amp;c=sell&amp;sku='.$dt[$i]["bill"].'&amp;ed='.$dt[$i]["product_sku_root"].'">'.$dt[$i]["bill"].'</a> 📌 '.$tx);
			}else if($dt[$i]["in_type"]=="r"){
				$tx=$this->billNote("r",'คืนสินค้า ใบเสร็จ <a href="?a=bills&amp;b=view&amp;c=sell&amp;sku='.$dt[$i]["bill"].'&amp;ed='.$dt[$i]["product_sku_root"].'">'.$dt[$i]["bill"].'</a> 📌 '.$tx);
			}else{
				$tx='<a href="?a=bills&amp;b=view&amp;c=in&amp;sku='.$dt[$i]["sku"].'&amp;ed='.$dt[$i]["product_sku_root"].'">'.$tx.'</a>';
			}
			echo '	<td>'.$tx.'</td>
				<td>'.$dt[$i]["c"].'</td>
				<td>'.$dt[$i]["u"].'</td>
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
		$sql["cut"]="SELECT bill_sell_list.lot,bill_sell_list.product_sku_root,bill_sell_list.n,bill_sell_list.c,bill_sell_list.u,
			product_ref.name,
			bill_in.in_type,bill_in.sku,IFNULL(bill_in.note,'') AS `note`,bill_in.bill
			FROM bill_sell_list
			LEFT JOIN product_ref
			ON(bill_sell_list.product_sku_key=product_ref.sku_key)
			LEFT JOIN bill_in
			ON(bill_sell_list.lot=bill_in.sku)
			WHERE bill_sell_list.sku=".$sku." ORDER BY bill_sell_list.id DESC
		";
		$se=$this->metMnSql($sql,["cut"]);
		//print_r($se);
		if($se["result"]){
			$re=$se["data"]["cut"];
		}
		return $re;
	}
	private function getBillsRetList(string $sku):array{
		$re=["list"=>[],"head"=>[]];
		$sku=$this->getStringSqlSet($sku);
		$sql=[];
		$sql["set"]="SELECT @date_reg:=(SELECT date_reg FROM bill_in WHERE sku=".$sku." ),
			@bill:=(SELECT bill FROM bill_in WHERE sku=".$sku.")
		";
		$sql["head"]="SELECT bill_in.id,bill_in.sku,bill_in.bill,
				bill_in.changto,bill_in.note,bill_in.user,bill_in.date_reg,
				CONCAT(`user_ref`.`name`,' ', `user_ref`.`lastname`) AS `user_name`,
				bill_in.n
			FROM bill_in
			LEFT JOIN `user_ref`
			ON( `bill_in`.`user`=`user_ref`.`sku_key`)
			WHERE bill_in.sku=".$sku."
		";
		$sql["list"]="SELECT  `bill_in_list`.`n`  AS  `n`, `bill_in_list`.`n_wlv`  AS  `n_wlv`,bill_in_list.s_type,`bill_in_list`.`note`  AS  `note`, 
				`bill_in_list`.`product_sku_root` ,
				product_ref.barcode AS `product_barcode`,product_ref.name AS `product_name`,product_ref.price AS `product_price`,
				unit_ref.name AS `unit_name`
			FROM `bill_in` 
			LEFT JOIN `bill_in_list` 
			ON(bill_in_list.id>=bill_in.r_ AND bill_in_list.id<=bill_in._r And bill_in.sku=bill_in_list.bill_in_sku)
			LEFT JOIN product_ref
			ON(bill_in_list.product_sku_key=product_ref.sku_key)
			LEFT JOIN unit_ref
			ON(bill_in_list.unit_sku_key=unit_ref.sku_key)
			WHERE bill_in.sku=".$sku."
		";
		$se=$this->metMnSql($sql,["head","list"]);
		//print_r($se);
		if($se["result"]){
			$re["head"]=$se["data"]["head"];
			$re["list"]=$se["data"]["list"];
		}
		return $re;
	}
	private function pageBillsRet():void{
		$this->pageHead(["title"=>"ใบคืนสินค้า DIYPOS","js"=>[],"css"=>["bill_ret"]]);
		echo '<div class="content">
				<div class="form">
					<h2 class="c">ใบคืนสินค้า</h2>';
			$this->writeContentBillsRet();
			echo '</div></div>';
		$this->pageFoot();
	}
	private function writeContentBillsRet():void{
		$edd=(isset($_GET["ed"]))?$_GET["ed"]:"";
		$sea=$this->getAllBillsRet();
		$se=$sea["row"];
		echo '<form class="form100" name="billsret" method="post">
			<input type="hidden" name="sku" value="" />
			<table class="billret"><tr><th>ที่</th>
			<th>วันบันทึก</th>
			<th>เลขที่</th>			
			<th  class="showhide">ใบเสร็จรับเงิน</th>		
			<th>รายการ</th>
			<th>เปลี่ยนเป็น</th>
			<th>ผู้บันทึก</th>
			<!--<th>กระทำ</th>-->
			</tr>';
		for($i=0;$i<count($se);$i++){
			$ed='';
			if($se[$i]["sku"]==$edd){
				$ed='<span title="โอเค เรียบร้อย"> 👌 </span>';
			}
			$stat="";
			if($se[$i]["changto"]=="0"){
				$change='เงิน';
			}else if($se[$i]["changto"]=="1"){
				$change=' สินค้า';
			}
			//$rt=preg_replace("/\\D/","", $se[$i]["date_reg"])."-".$se[$i]["bill"];
			$rt=$se[$i]["sku"];
			$cm=($i%2!=0)?" class=\"i2\"":"";
			echo '<tr'.$cm.'><td>'.$se[$i]["id"].'</td>
				<td>'.$se[$i]["date_reg"].'</td>
				<td>
					<div><a href="?a=bills&amp;c=ret&amp;b=view&amp;sku='.$se[$i]["sku"].'" title="ดูรายละเอียด">'.$rt.'</a></div>
					<div><a href="?a=bills&amp;c=sell&amp;b=view&amp;sku='.$se[$i]["bill"].'" title="ดูรายละเอียด">&nbsp;🧾&nbsp;</a>'.$se[$i]["date_reg"].'</div>
					<div>👫 '.htmlspecialchars($se[$i]["user_name"]).'</div>
				</td>
				<td  class="showhide l"><a href="?a=bills&amp;c=sell&amp;b=view&amp;sku='.$se[$i]["bill"].'" title="ดูรายละเอียด">'.$se[$i]["bill"].''.$stat.'</td>
				<td class="r">'.$se[$i]["n"].'</a></td>
				<td class="l">'.$change.'</td>
				<td class="l">'.htmlspecialchars($se[$i]["user_name"]).'</td>

				<!--<td>
					<a data-sku="'.$se[$i]["sku"].'" onclick="Bs.delete(this)" title="ทิ้ง">🗑</a>
					'.$ed.'
					</td>-->
				</tr>';
		}
		echo '</table></form>';
			$count=(isset($sea["count"]))?$sea["count"]:0;
			$this->page($count,$this->per,$this->page,"?a=bills&amp;c=ret&amp;page=");
	}
	private function getAllBillsRet():array{
		$re=[];
		$sql=[];
		$sql["count"]="SELECT @count:=COUNT(*) FROM bill_in WHERE bill_in.in_type='r'";
		$sql["get"]="SELECT  `bill_in`.`id`  AS  `id`,`bill_in`.`sku`  AS  `sku`,`bill_in`.`bill`  AS  `bill`,`bill_in`.`note`  AS  `note`,
				`bill_in`.`changto`,`bill_in`.`date_reg` AS `date_reg`,
				CONCAT(`user_ref`.`name`,' ', `user_ref`.`lastname`) AS `user_name`,
				`bill_in`.`n` AS `n`
			FROM `bill_in` 
			LEFT JOIN `user_ref`
			ON( `bill_in`.`user`=`user_ref`.`sku_key`)
			WHERE bill_in.in_type='r'
			ORDER BY `bill_in`.`id` DESC LIMIT ".(($this->page-1)*$this->per).",".$this->per."";
		$sql["result"]="SELECT @count AS count";
		$se=$this->metMnSql($sql,["get","result"]);
		//print_r($se);
		if($se["result"]){
			$re=["row"=>$se["data"]["get"],"count"=>$se["data"]["result"][0]["count"]];
		}
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
			<table class="billssell"><tr><th>ที่</th>
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
				$stat=' <apan class="red bold">[ถูกยกเลิก]</span>';
			}else if($se[$i]["stat"]=="r"){
				$stat=' <apan class="darkgoldenrod bold"> [มีคืนสินค้า]</span>';
			}
			$cm=($i%2!=0)?" class=\"i2\"":"";
			echo '<tr'.$cm.'><td>'.$se[$i]["id"].'</td>
				<td>
					<div><a href="?a=bills&amp;c=sell&amp;b=selectview&amp;sku='.$se[$i]["sku"].'" title="ดูรายละเอียด">'.$se[$i]["sku"].''.$stat.'</a></div>
					<div>'.$se[$i]["date_reg"].'</div>
					<div>'.$se[$i]["user_name"].'</div>
				</td>
				<td  class="showhide l"><a href="?a=bills&amp;c=sell&amp;b=selectview&amp;sku='.$se[$i]["sku"].'" title="ดูรายละเอียด">'.$se[$i]["sku"].''.$stat.'</a></td>
				<td class="r">'.$se[$i]["n"].'</td>
				<td class="r">'.number_format($se[$i]["price"],2,'.',',').'</td>
				<td class="l">'.$se[$i]["user_name"].'</td>

				<td class="c">
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
