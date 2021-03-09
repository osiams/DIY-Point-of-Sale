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
			require_once("php/partner.php");
			$this->partner= new partner();
			$this->fetchPartnerSelectPage();
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
	public function writeForm(){
		echo '<table id="'.$this->id.'" class="table_select_partner">
			<tr><td colspan="3" class="r"><input type="button" value="เพิ่ม/แก้ไข" onclick="Fsl.ctAddPartner(\''.$this->form_name.'\',\''.$this->id.'\',\''.$this->partner_list.'\')" /></td></tr>
		</table>';
	}
	private function partnerGetData():array{
		
	}
}
