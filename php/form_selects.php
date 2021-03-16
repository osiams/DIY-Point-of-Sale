<?php
class form_selects extends main{
	public function __construct(string $a,string $name=null,string $form_name=null,string $id=null,string $partner_list=null){
		parent::__construct();
		$this->a=$a;
		$this->name=$name;
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
				$this->partner= new partner();
				$this->partner->page=$this->setPageR();
				$this->partner->defaultPageSearch();
				$this->fetchPartnerSelectPage();
			}
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
	public function writeForm(){
		echo '<table id="'.$this->id.'" class="table_select_partner">
			<tr><td colspan="3" class="r"><input type="button" value="เพิ่ม/แก้ไข" onclick="Fsl.ctAddPartner(\''.$this->form_name.'\',null,\''.$this->id.'\',\''.$this->partner_list.'\')" /></td></tr>
		</table>';
		echo '<script type="text/javascript">Fsl.setLoadPartner(\''.$this->form_name.'\',null,\''.$this->id.'\',\''.$this->partner_list.'\')</script>';
	}
	private function partnerGetData(string $tin):array{
		$re=[];
		$sql=[];
		$sql["get"]="SELECT `id`,`name`,`icon`,`sku`,`sku_root` 
			FROM `partner` 
			WHERE `sku_root` IN(".$tin.")
			ORDER BY `id` DESC ";
		$se=$this->metMnSql($sql,["get"]);
		if($se["result"]){
			$re=$se["data"]["get"];
		}
		return $re;
	}
	protected function setPropR(string $prop):string{
		$ar = [];
		if(strlen(trim($prop))>2){
			$prop =trim($prop);
			$ar = explode(",,",substr($prop,1,-1));
		}
		return json_encode($ar);
	}
}