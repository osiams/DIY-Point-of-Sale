<?php
class image{
	protected $size_max=524288;	//-500Kb
	protected $type_allow=array(1,2,3);	//-1=IMAGETYPE_GIF,2=IMAGETYPE_PNG,3=IMAGETYPE_JPEG
	protected $dir="";
	public function __construct(string $dir=""){
		$this->dir=$dir;
		$this->dot=["image/png"=>"png","image/gif"=>"gif","image/jpeg"=>"jpeg"];
		//pathinfo($_SERVER["SCRIPT_FILENAME"])["dirname"]."/media/group/";
		//echo substr(sprintf('%o', fileperms($this->dir)), -4);
	}
	public function imgCheck(string $icon_name):array{
		$re=array(
			"size"=>0,
			"width"=>0,
			"type"=>0,
			"height"=>0,
			"mime"=>"",
			"file"=>"",
			"squar"=>[32,64,128,256,512,1024],
			"message_error"=>"",
			"result"=>false
		);
		$a=explode(";",$_POST[$icon_name]);
		$b=[];
		if(isset($a[1])){
			$b=explode(",",$a[1]);
		}
		if(isset($b[1])){
			if(is_writable($this->dir)){
				$re["size"]=(int) (strlen(rtrim($b[1], '=')) * 3 / 4);
				$p=getimagesize($_POST[$icon_name]);
				$re["width"]=$p[0];
				$re["height"]=$p[1];
				$re["mime"]=$p["mime"];
				$re["file"]=$_POST[$icon_name];
				if($re["width"]>0&&$re["height"]>0){
					if($re["mime"]=="image/png"||$re["mime"]=="image/gif"||$re["mime"]=="image/jpeg"){
						$re["result"]=true;
					}else{
						$re["message_error"]="เกิดข้อผิดพลาดเกี่ยวกับ ข้อมูล รูป";
					}
				}	
			}else{
				$re["message_error"]="ไม่สามารถเขียนไฟล์ได้ที่ใน ".$this->dir." โปรดตรวจสอบ สิทธิ์ ของแฟ้ม ";
			}		
		}else{
			$re["message_error"]="เกิดข้อผิดพลาดเกี่ยวกับ ข้อมูล รูป";
		}
		return $re;
	}
	public function imgSave(array $dt,string $sku_key,int $max_squar=256):array{
		$re=array(
			"result"=>false
		);
		if($dt["result"]){
			//print_r($dt);exit;
			//--รูปที่ปรับขนาดรูป
			$original=null;
			if($dt["mime"]=="image/png"){
				$original = imagecreatefrompng($dt["file"]);
				$target_file=$this->dir."/".$sku_key.".".$this->dot[$dt["mime"]];
				imagesavealpha($original, true);
				imagepng($original, $target_file,9);
			}else if($dt["type"]==3){
				
				$original = imagecreatefromjpeg($dt["tmp_name"]);
				
			}else if($dt["type"]==1){
				$original = imagecreatefromgif($dt["tmp_name"]);
			}	
					
			for($i=0;$i<count($dt["squar"]);$i++){
				if(($dt["width"]>=$dt["squar"][$i]||$dt["height"]>=$dt["squar"][$i])||$dt["squar"][$i]<=$max_squar){
					$target_file=$this->dir."/".$dt["squar"][$i]."x".$dt["squar"][$i]."_".$sku_key.".".$this->dot[$dt["mime"]];
					//echo $target_file;
					$new_width=$dt["squar"][$i];
					$new_height=$dt["squar"][$i];			
					if($dt["width"]>=$dt["height"]){
						$new_height=($dt["height"]/$dt["width"])*$new_width;
					}else{
						$new_width=($dt["width"]/$dt["height"])*$new_height;
					}

					$thumb    = imagecreatetruecolor($new_width, $new_height);
					if($dt["mime"]=="image/png"){
						$background = imagecolorallocate($thumb , 0, 0, 0);
						imagecolortransparent($thumb, $background);
						imagealphablending($thumb, false);
						imagesavealpha($thumb, true);
						
					}else if($dt["type"]==3){
						//$original = imagecreatefrompng($dt["tmp_name"]);
						$background = imagecolorallocate($thumb , 0, 0, 0);
						imagecolortransparent($thumb, $background);
						imagealphablending($thumb, false);
						imagesavealpha($thumb, true);
						
					}else if($dt["type"]==1){
						//$original = imagecreatefromgif($dt["tmp_name"]);
						$background = imagecolorallocatealpha($thumb , 0, 0, 0,127);
						imagecolortransparent($thumb, $background);
						imagefill($thumb, 0, 0, $background);
						imagealphablending($thumb, true);
					}
					
					imagecopyresampled($thumb, $original, 0, 0, 0, 0, $new_width,$new_height, $dt["width"], $dt["height"]);
					if($dt["type"]==2){
						imagejpeg($thumb, $target_file,100);
					}else if($dt["mime"]=="image/png"){
						imagepng($thumb, $target_file,9);
						
					}else if($dt["type"]==1){
						imagegif($thumb, $target_file,100);
					}
				}
			}
			//--รูปดั้งเดิม
			/*
			$target_file=$this->dir."/".$file_name."".$dot;
			if(move_uploaded_file($dt["tmp_name"],$target_file)) {
				$re["result"]=true;
			}else{
				
			}*/
		}
		//print_r($re);
		return $re;
	}
	public function delete(string $path,string $name):array{
		$re=array(
			"result"=>false,
			"name"=>array(),
			"path"=>array()
		);
		$re["name"]=$name;
		$re["path"]=$path;
		$file=$path."/".$name;
		if (file_exists($file)) {
			unlink($file);
			$re["result"]=true;
		}else {

		}
		return $re;
	}
}
?>
