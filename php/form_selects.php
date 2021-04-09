<?php
class form_selects extends main{
	public function __construct(string $a,string $select_type="one",string $form_name=null,string $id=null,string $partner_list=null){
		parent::__construct();
		$this->a=$a;
		$this->select_type=$select_type;
		$this->form_name=$form_name;
		$this->id=$id;
		$this->partner=null;
		$this->partner_list=$partner_list;
	}
	public function fetch(){
		if($this->a=="partner"){
			if(isset($_POST["c"])&&$_POST["c"]=="partner_get"){
				$this->fetchPartnerGetPage();
			}else{
				require_once("php/partner.php");
				$this->postToGet();
				$this->partner= new partner();
				$this->partner->page=$this->setPageR();
				$this->partner->defaultPageSearch();
				$this->fetchPartnerSelectPage();
			}
		}else if($this->a=="payu"){
			if(isset($_POST["c"])&&$_POST["c"]=="payu_get"){
				$this->fetchPayuGetPage();
			}else{
				require_once("php/payu.php");
				$this->postToGet();
				$this->payu= new payu();
				$this->payu->page=$this->setPageR();
				$this->payu->defaultPageSearch();
				$this->fetchPayuSelectPage();
			}
		}else if($this->a=="product"){
			if(isset($_POST["c"])&&$_POST["c"]=="product_get"){
				$this->fetchProductGetPage();
			}else{
				require_once("php/product.php");
				$this->postToGet();
				$this->product= new product();
				$this->product->page=$this->setPageR();
				$this->product->defaultPageSearch();
				$this->fetchProductSelectPage();
			}
		}
		
	}
	private function postToGet():void{
		if(!isset($_POST["page"])){
			$_GET["page"]=1;
		}else{
			$_GET["page"]=$_POST["page"];
		}
		if(!isset($_POST["tx"])){
			$_GET["tx"]="";
		}else{
			$_GET["tx"]=$_POST["tx"];
		}
		if(!isset($_POST["fl"])){
			$_GET["fl"]="name";
		}else{
			$_GET["fl"]=$_POST["fl"];
		}
		if(!isset($_POST["lid"])){
			$_GET["lid"]=0;
		}else{
			$_GET["lid"]=$_POST["lid"];
		}
		if(!isset($_POST["partner"])){
			$_GET["partner"]="";
		}else{
			$_GET["partner"]=$_POST["partner"];
		}
	}
	private function fetchPartnerSelectPage():void{
		$this->re["result"]=true;
		$this->re["message_error"]="";
		$this->re["data"]=$this->partner->getAllPartner();
		$this->re["data"]["page"]=["page"=>$this->partner->page,"per"=>$this->partner->per];
		header('Content-type: application/json');
		echo json_encode($this->re);
	}
	private function fetchPartnerGetPage():void{
		$pn_list=(isset($_POST["partner_list"]))?$_POST["partner_list"]:"";
		$tin=$this->setPropR($pn_list);
		$tin=substr($tin,1,-1);
		$se=$this->partnerGetData($tin);
		$this->re["result"]=true;
		$this->re["message_error"]="";
		$this->re["data"]=$se;
		header('Content-type: application/json');
		echo json_encode($this->re);
	}
	public function writeForm(string $json_value="{}"){
		if($this->a=="partner"){
			echo '<table id="'.$this->id.'" class="table_select_partner">
				<tr><td colspan="3" class="r"><input type="button" value="เพิ่ม/แก้ไข" onclick="Fsl.ctAddPartner(\'partner\',null,\''.$this->form_name.'\',null,\''.$this->id.'\',\''.$this->partner_list.'\')" /></td></tr>
			</table>';
			echo '<script type="text/javascript">let value='.$json_value.';Fsl.setLoadPartner(\'partner\',\''.$this->form_name.'\',null,\''.$this->id.'\',\''.$this->partner_list.'\',value)</script>';
		}else if($this->a=="payu"){
			echo '<table id="'.$this->id.'" class="table_select_payu r">
				<tr><td colspan="4" class="r">
					<input type="button" value="เพิ่ม/แก้ไข"  onclick="Fsl.ctAddPartner(\'payu\',null,\''.$this->form_name.'\',null,\''.$this->id.'\',\''.$this->partner_list.'\')">
				</td></tr>
			</table>';
			echo '<script type="text/javascript">let value='.$json_value.';Fsl.setLoadPartner(\'payu\',\''.$this->form_name.'\',null,\''.$this->id.'\',\''.$this->partner_list.'\',value)</script>';
		}else if($this->a=="product"){
			echo '<input type="button" value="เพิ่ม/แก้ไขสินค้า"  onclick="Fsl.ctAddPartner(\'product\',\'Bi\',\''.$this->form_name.'\',null,\''.$this->id.'\',\''.$this->partner_list.'\')">';
		}
	}
	private function partnerGetData(string $tin):array{
		$re=[];
		$sql=[];
		$sql["get"]="SELECT `id`,`name`,`icon`,`sku`,`sku_root` 
			FROM `partner` 
			WHERE `sku_root` IN(".$tin.")";
		$se=$this->metMnSql($sql,["get"]);
		if($se["result"]){
			$re=$se["data"]["get"];
		}
		$q=json_decode('['.$tin.']');
		$dt1=$se["data"]["get"];
		for($i=0;$i<count($q);$i++){
			for($j=0;$j<count($dt1);$j++){
				if($dt1[$j]["sku_root"]==$q[$i]){
					$re[$i]=$dt1[$j];
					break;
				}
			}
		}
		//print_r($re);
		return $re;
	}
	public function writeSetValue(string $display_id,$json_value):void{
		echo '<script type="text/javascript">
			let json_value='.$json_value.';
			Fsl.loadSetValue("'.$display_id.'",json_value)
		</script>';
	}
	protected function setPropR(string $prop):string{
		$ar = [];
		if(strlen(trim($prop))>2){
			$prop =trim($prop);
			$ar = explode(",,",substr($prop,1,-1));
		}
		return json_encode($ar);
	}
	###################################################
	private function fetchPayuGetPage():void{
		$pn_list=(isset($_POST["partner_list"]))?$_POST["partner_list"]:"";
		$tin=$this->setPropR($pn_list);
		$tin=substr($tin,1,-1);
		$se=$this->payuGetData($tin);
		$this->re["result"]=true;
		$this->re["message_error"]="";
		$this->re["data"]=$se;
		header('Content-type: application/json');
		echo json_encode($this->re);
	}
	private function payuGetData(string $tin):array{
		$re=[];
		$sql=[];
		$sql["set"]="SELECT @i:=0";
		$sql["get"]="SELECT `id`,`name`,`icon`,`sku`,`sku_root` 
			FROM `payu` 
			WHERE `sku_root` IN(".$tin.")";
		$se=$this->metMnSql($sql,["get"]);
		if($se["result"]){
			$re=$se["data"]["get"];
		}
		$q=json_decode('['.$tin.']');
		$dt1=$se["data"]["get"];
		for($i=0;$i<count($q);$i++){
			for($j=0;$j<count($dt1);$j++){
				if($dt1[$j]["sku_root"]==$q[$i]){
					$re[$i]=$dt1[$j];
					break;
				}
			}
		}
		//print_r($re);
		return $re;
	}
	private function fetchPayuSelectPage():void{
		$this->re["result"]=true;
		$this->re["message_error"]="";
		$this->re["data"]=$this->payu->getAllPayu();
		$this->re["data"]["page"]=["page"=>$this->payu->page,"per"=>$this->payu->per];
		header('Content-type: application/json');
		echo json_encode($this->re);
	}
	#########################################################
	private function fetchProductSelectPage():void{
		$this->re["result"]=true;
		$this->re["message_error"]="";
		$this->re["data"]=$this->product->getAllProduct("form_select");
		$this->re["data"]["page"]=["page"=>$this->product->page,"per"=>$this->product->per];
		header('Content-type: application/json');
		echo json_encode($this->re);
	}
}
