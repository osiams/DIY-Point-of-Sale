<?php
class barcode extends main{
	public function __construct(){
		parent::__construct();
		$this->home=1;
	}
	public function run(){
		$this->defaultPage();
	}
	private function defaultPage(){
		$skuroot=(isset($_GET["skuroot"])&&preg_match("/^[0-9a-zA-Z-+\.&\/]{1,25}$/",$_GET["skuroot"]))?$_GET["skuroot"]:"";
		$defalutxa=["210x297/35x25/0,0/0,0/0","210x297/40x30/0,0/0,0/0","210x297/34x20/0,0/0,0/0","210x297/34x11/0,0/0,0/0"];
		$dfs=["p"=>[210,297],"l"=>[35,25],"d"=>[0,0],"g"=>[0,0],"q"=>0];
		$defalutx="210x297/35x25/0,0/0,0/0";
		$df=["pagewidth"=>$dfs["p"][0],"pageheight"=>$dfs["p"][1],"paddingtop"=>$dfs["d"][1],"paddingleft"=>$dfs["d"][0],
			"gapcol"=>$dfs["g"][0],"gaprow"=>$dfs["g"][1],"labelwidth"=>$dfs["l"][0],"labelheight"=>$dfs["l"][1],"border"=>$dfs["q"]];
		$this->addDir("?a=barcode","สร้าง และ พิมพ์ รหัสแท่งติดสินค้า");		
		$this->pageHead(["title"=>"พิมพ์สติกเกอร์ DIYPOS","css"=>["barcode"],"js"=>["barcode","Bc"]]);		
		//❯
		echo '<div id="barcode">
						<div>
							<div>
								<select name="ptf" class="bdselectlabel" onchange="Bc.changptf(this)">';
			for($i=0;$i<count($defalutxa);$i++){
				$st=($defalutxa[$i]==$defalutx)?" selected":"";
				echo '<option value="'.$defalutxa[$i].'"'.$st.'>'.$defalutxa[$i].'</option>';
			}
		echo '						</select>
							</div>
							<div>
								<form name="me" method="post" action="">
									<input type="hidden" name="submith" value="clicksubmit" />
									<input type="hidden" name="ps" value="" />
									<input type="hidden" name="logout" value="" />
									<fieldset>
										<legend>กระดาษ</legend>
										<div>
											<div>
												<label for="barcode_pagewidth">กว้าง</label>
												<input id="barcode_pagewidth" name="pagewidth"  type="number"  onchange="Bc.setpw()" value="'.$df["pagewidth"].'"  />
											</div>
											<div>x</div>
											<div>
												<label for="barcode_pagewidth">สูง</label>
												<input  id="barcode_pageheight"   type="number" name="pageheight"   onchange="Bc.setph()" value="'.$df["pageheight"].'"  />
											</div>
										</div>
									</fieldset>
									<fieldset>
										<legend>ป้าย</legend>
										<div>
											<div>
												<label for="barcode_labelwidth">กว้าง</label>
												<input id="barcode_labelwidth" name="labelwidth"  type="number"     step="0.1" onchange="Bc.setlw()"  value="'.$df["labelwidth"].'"  />
											</div>
											<div>x</div>
											<div>
												<label for="barcode_labelwidth">สูง</label>
												<input  id="barcode_labelheight"  name="labelheight"  type="number"   step="0.1"  onchange="Bc.setlh()" value="'.$df["labelheight"].'"  />
											</div>
										</div>
									</fieldset>
									<fieldset>
										<legend>ระยะห่างจากขอบ</legend>
										<div>
											<div>
												<label for="barcode_paddingleft">ซ้าย</label>
												<input id="barcode_paddingleft" name="paddingleft" type="number"  step="0.1"  onchange="Bc.setpl()" value="'.$df["paddingleft"].'"  />
											</div>
											<div>x</div>
											<div>
												<label for="barcode_paddingtop">บน</label>
												<input  id="barcode_paddingtop" type="number" name="paddingtop" step="0.1"  onchange="Bc.setpt()" value="'.$df["paddingtop"].'"  />
											</div>
										</div>
									</fieldset>
									<fieldset>
										<legend>ช่องว่างระหว่างป้าย</legend>
										<div>
											<div>
												<label for="barcode_gapcol">แนวนอน</label>
												<input id="barcode_gapcol" name="gapcol"  step="0.1" onchange="Bc.setgc()" type="number" value="'.$df["gapcol"].'"  />
											</div>
											<div>x</div>
											<div>
												<label for="barcode_gaprow">แนวตั้ง</label>
												<input  id="barcode_gaprow"  type="number" name="gaprow"   step="0.1" onchange="Bc.setgr()" value="'.$df["gaprow"].'"  />
											</div>
										</div>
									</fieldset>
									<fieldset>
										<legend>ค่าอื่นๆ</legend>
										<div>
											<div>
												<label for="barcode_labelborder">เส้นขอบ</label> <input id="barcode_labelborder"  onchange="Bc.setlb()"  type="checkbox" '.($df["border"]==1?"checked":"").' /> 
											</div>
											<div></div>
											<div>
												<label for="barcode_zoom">ขยาย%</label>
												<input id="barcode_zoom" type="number"  value="100" onchange="Bc.setZoom(this)" />
											</div>
										</div>
									</fieldset>	
									<br /><div  class="c">💬 หน่วย มิลลิเมตร</div><br />
								</form>
								
							</div>
							<div>
								<input type="button" name="exit" onclick="location.href=\'index.php\'" value="ออก" />
								<input type="button" name="exit" onclick="window.print();" value="พิมพ์" />
								<input type="button" name="view"  onclick="Bc.setSH()" value="❮" /> 
							</div>
						</div>
						<div>
							<div id="barcode_page_prev_zm">
								<div id="barcode_page_prev_out">
									<div id="barcode_page_prev_in"></div>
								</div>
							</div>
						</div>
					</div>
					<div id="sh"><input type="button" name="view"  onclick="Bc.setSH()" value="❯"  /> </div>
					<script type="text/javascript">Bc.run();';
		$this->setJsDt($skuroot);
		echo '			</script>';
		$this->pageFoot();
	}
	private function setJsDt(string $skuroot):void{
		if($skuroot!=""){
			$sql=[];
			$sql["r"]="SELECT product.name AS name	,product.price		,product.barcode	,product.s_type	,unit.name AS unit
				FROM product 
				LEFT JOIN unit 
				ON(product.unit=unit.sku_root) 
				WHERE  product.sku_root='".$skuroot."'";
			$se=$this->metMnSql($sql,["r"]);
			echo "Bc.dt={\n";
			if(isset($se["data"]["r"][0])){
				$y=$se["data"]["r"][0];
				for($i=1;$i<=66;$i++){
					if($i>1){
						echo ',';
					}
					$dt=[
						"for_id"=> "label_".$i,
						"barcode"=>$y["barcode"],
						"name"=>$y["name"],
						"s_type"=>$y["s_type"],
						"price"=>$y["price"],
						"sku_root"=>$skuroot,
						"unit"=>$y["unit"]
					];
					if($dt["s_type"]!="p"){
						$dt["name"]=$dt["name"]." 1 ".$dt["unit"];
						$dt["barcode"]=$this->createBcWLV($dt["barcode"],1);
					}
					echo  "\"label_".$i."\":".json_encode($dt)."\n";
				}
			}
			echo '};Bc.load()';
		}
	}
}
?>
