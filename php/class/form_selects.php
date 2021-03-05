<?php
class form_selects{
	public function __construct(string $a,string $name,string $id){
		$this->a=
		$this->name=$name;
		$this->id=$id;
	}
	public function writeForm(){
		echo '<table id="'.$this->id.'" class="form100 radius3">
			<tr><td colspan="2" class="r"><input type="button" value="เพิ่ม"></td></tr>
		</table>';
	}
}
