<?php
class form_selects extends main{
	public function __construct(string $a,string $name=null,string $form_name=null,string $id=null){
		parent::__construct();
		$this->a=$a;
		$this->name=$name;
		$this->form_name=$form_name;
		$this->id=$id;
		$this->partner=null;
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
		header('Content-type: application/json');
		echo json_encode($this->re);
	}
	public function writeForm(){
		echo '<table id="'.$this->id.'" class="form100 radius3">
			<tr><td colspan="2" class="r"><input type="button" value="เพิ่ม" onclick="Fsl.ctAddPartner(\''.$this->form_name.'\',\''.$this->id.'\')" /></td></tr>
		</table>';
	}
	private function partnerGetData():array{
		
	}
}
