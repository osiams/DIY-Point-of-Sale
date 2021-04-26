<?php
class gallery extends main{
	public function __construct(string $table=null,string $key,string $data_key=null,string $form_name=null,string $display_id=null,string $gallery_list=null,string $gallery_gl_list=null,string $icon_ob){
		parent::__construct();
		$this->table=$table;
		$this->key=$key;
		$this->data_key=$data_key;
		$this->form_name=$form_name;
		$this->icon_ob=$icon_ob;
		$this->display_id=$display_id;
		$this->gallery_list=$gallery_list;
		$this->gallery_gl_list=$gallery_gl_list;
	}
	public function fetch(){
		if(isset($_POST["c"])&&$_POST["c"]=="partner_get"){
					$this->fetchPartnerGetPage();
		}
	}
	public function writeForm(string $json_value="{}"):void{
		echo '	<p><label>ห้องแสดงภาพ</label></p>
			<div class="gallery_file_set">
				<div id="'.$this->display_id.'"></div>
				<div><input type="button" value="เพิ่ม/แก้ไข" onclick="Gl.ctAddGallery(\''.$this->table.'\',\''.$this->key.'\',\''.$this->data_key.'\',\''.$this->form_name.'\',dialog_id=null,\''.$this->display_id.'\',\''.$this->gallery_list.'\',\''.$this->gallery_gl_list.'\',\'new\',\''.$this->icon_ob.'\')" /></div>
			</div>
			<script type="text/javascript">Gl.setLoadGallery(\''.$this->table.'\',\''.$this->key.'\',\''.$this->data_key.'\',\''.$this->form_name.'\',dialog_id=null,\''.$this->display_id.'\',\''.$this->gallery_list.'\',\''.$this->gallery_gl_list.'\')</script>
		';
	}
}
