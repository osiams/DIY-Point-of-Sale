<?php  /*--apt DIY_POS;--ext php;--version 0.0;*/
class barcode{
	public function __construct(string $font){
		$this->font=$font;
	}
	public function createImgBarcode(string $type,string $barcode,int $width=0,int $height=40,int $br=4,int $margintop=10,int $marginbottom=10){

		if($type=="ean13"){
			$widthb=((strlen($barcode)*7)+9);
		}else{
			$barcode=(strlen($barcode)==0)?"00":$barcode;
			$barcode=(strlen($barcode)%2==1)?"0".$barcode:$barcode;
			$widthb=((strlen($barcode)*7)+12);
		}
		if($widthb*$br<$width){
			for($i=1;$i<10000;$i++){
				if($widthb*($br+$i)>=$width){
					$width=$widthb*($br+$i);
					$br=$br+$i;
					break;
				}
			}
		}else{
			$width=$widthb*$br;
		}
				
		$heightt=$height+$margintop+$marginbottom;
		$im=imagecreatetruecolor($width,$heightt);
imagealphablending($im, false);
		$fontsize=14;
		$white=imagecolorallocatealpha($im, 255, 255, 255,127);
		$black=imagecolorallocate($im, 0, 0, 0);
		imagefilledrectangle($im, 0, 0,$width, $heightt, $white);
imagealphablending($im,true);
		$barcodetop=$margintop;
		$hi=$height;
		//$br=4;
		$cbr=0;
		$xy=imagettfbbox($fontsize, 0, $this->font,"กี้รู");
		$w=$xy[2]-$xy[0];
		$h=$xy[1]-$xy[7];
		if($type=="ean13"){
			$bcd=$this->ean13($barcode);
			$bcw=count($bcd)*$br;
			$cbr=($width-$bcw)/2;
			for($i=0;$i<count($bcd);$i++){
				$color=($bcd[$i]==1)?$black:$white;
				imagefilledrectangle($im,$cbr, $barcodetop, $cbr+$br-1,$hi+$barcodetop, $color);
				$cbr+=$br;
			}
		}else if($type=="itf14"){
			$bcd=$this->itf14($barcode);
			//$br=3;
			$w2=0;
			for($i=0;$i<count($bcd);$i++){
				$w2+=($bcd[$i]==1)?$br*2:$br*1;
			}
			$cbr=($width-$w2)/2;
			for($i=0;$i<count($bcd);$i++){
				$color=(($i+1)%2==1)?$black:$white;
				$widt=($bcd[$i]==1)?$br*2:$br*1;
				imagefilledrectangle($im,$cbr, $barcodetop, $cbr+$widt-1,$hi+$barcodetop, $color);
				$cbr+=$widt;
			}
		}else if($type=="itf"){
			$bcd=$this->itf($barcode);
			//$br=4;
			$bcw=count($bcd)*$br;
			$cbr=($width-$bcw)/2;
			for($i=0;$i<count($bcd);$i++){
				$color=($bcd[$i]==1)?$black:$white;
				imagefilledrectangle($im,$cbr, $barcodetop, $cbr+$br-1,$hi+$barcodetop, $color);
				$cbr+=$br;
			}
			imagefilledrectangle($im,$cbr, $barcodetop, $cbr+$br-2,$hi+$barcodetop, $white);
		}
		$heightr=$hi+$barcodetop+$marginbottom;
		$imr=imagecreatetruecolor($width,$heightr);
		for($i=0;$i<1;$i++){
			imagecopy($imr,$im,0,$i*$heightr,0,0,$width,$heightr);
		}//exit;
		imagealphablending($im,false);
		imagesavealpha($im,true);
		return $im;
	}
	public function createImgLabel($name=NULL,$barcode=NULL,$price=NULL,$unit,int $font_size2=17){
		$name=" ".$name;
		$unit="/ ".$unit;
		$arrowtop=3;
		$arrowwidth=24;
		$arrowheight=24;
		$width=384;
		$height=220;
		$im=imagecreatetruecolor($width,$height);
		$fontsize=$font_size2;
		$white=imagecolorallocate($im, 255, 255, 255);
		$pinks=imagecolorallocate($im, 255, 255, 255);
		$black=imagecolorallocate($im, 0, 0, 0);
		imagefilledrectangle($im, 0, 0,$width, $height, $white);
		$textarr=$this->cutWord2($arrowwidth,$fontsize,$this->font,$name,384);
		//--รหัสแท่ง
		$barcodetop=55;
		$hi=120;
		$br=2.5;
		$cbr=0;
		$texbchi=0;
		if(isset($textarr[1])){
			$hi=150;
			$texbchi=30;
		}
		if(strlen($barcode)==13){
			$bcd=$this->ean13($barcode);
			for($i=0;$i<count($bcd);$i++){
				$color=($bcd[$i]==1)?$black:$white;
				imagefilledrectangle($im,$cbr, $barcodetop, $cbr+$br,$hi, $color);
				$cbr+=$br;
				
			}
		}elseif (1<2){
			$bcd=$this->itf14($barcode);
			$br=2;
			for($i=0;$i<count($bcd);$i++){
				$color=(($i+1)%2==1)?$black:$white;
				$width=($bcd[$i]==1)?$br*2:$br*1;
				imagefilledrectangle($im,$cbr, $barcodetop, $cbr+$width,$hi, $color);
				$cbr+=$width;
			}
		}
		imagettftext($im,$fontsize,0,0,145+$texbchi,$black,$this->font,$barcode);
		$xy=imagettfbbox($fontsize, 0, $this->font,"กี้รู");
		$w=$xy[2]-$xy[0]+$arrowwidth;
		$h=$xy[1]-$xy[7];

		$margintop=0;
		
		if(isset($textarr[1])){
			imagefilledrectangle($im,0, $h,384,$h+$margintop+$h+$h-($h/2), $white);
			imagettftext($im,$fontsize,0,0,$h+$margintop+$h-0, $black,$this->font,$textarr[1]);
		}	
		imagefilledrectangle($im,0, 0,384,$h+$margintop, $white);
		$values=$this->drawArrow($arrowtop);
		imagefilledpolygon($im, $values, 7, $black);	
		imagettftext($im,$fontsize,0,$arrowwidth,$h+ 0, $black,$this->font,$textarr[0]);

		//----ราคา
		$textprice=(string)$price;
		$tparr=explode(".",$textprice);
		$tparr[1]=" .".$tparr[1];
		$xy=imagettfbbox($fontsize, 0, $this->font,$tparr[1]);
		$wf=$xy[2]-$xy[0];
		imagefilledrectangle($im,260,$h+12+36,384,$h+12+$h+12, $white);
		imagettftext($im,$fontsize,0,384-$wf,$h+$margintop+$h+32, $black,$this->font,$tparr[1]);
		$baht="บาท";
		$xy=imagettfbbox($fontsize-4, 0, $this->font,$baht);
		$wb=$xy[2]-$xy[0];
		imagettftext($im,$fontsize-4,0,384-$wb,$h+$margintop+$h+60, $black,$this->font,$baht);
		$xy=imagettfbbox($fontsize+30, 0, $this->font,$tparr[0]);
		$wp=$xy[2]-$xy[0];
		imagettftext($im,$fontsize+30,0,384-$wf-$wp,$h+$margintop+$h+60, $black,$this->font,$tparr[0]);
		//---หน่วย
		$xy=imagettfbbox($fontsize, 0, $this->font,$unit);
		$wu=$xy[2]-$xy[0];
		imagettftext($im,$fontsize,0,384-$wu,$h+$margintop+$h+65+$h+$margintop, $black,$this->font,$unit);
		return $im;
	}
	public function cutWord2(int $arrowwidth,int $fontsize,string $fontfile,string $text,int $max):array{
		$re=[];
		$xy=imagettfbbox($fontsize, 0, $fontfile,$text);
		$w=$xy[2]-$xy[0]+$arrowwidth;
		if($w>$max){
			$w=0;
			$tp="";
			for($i=1;$i<=mb_strlen($text);$i++){
				$t=mb_substr($text,0,$i,"utf-8");
				$xy=imagettfbbox($fontsize, 0, $fontfile,$t);
				$w=$xy[2]-$xy[0]+$arrowwidth;
				if($w>$max){
					$re[0]=$tp;
					$re[1]=mb_substr($text,$i-1,mb_strlen($text),"utf-8");
					break;
				}else{
					$re[0]=$text;
				}
				$tp=$t;
			}
		}else{
			$re[0]=$text;
		}
		return $re;
	}
	protected function drawArrow($arrowtop):array{
		$re =[
            0,  12+$arrowtop, 
            12,  0+$arrowtop, 
            24,  12+$arrowtop, 
            18, 12+$arrowtop, 
            18, 24+$arrowtop,  
            6,  24+$arrowtop,   
            6,  12+$arrowtop
       ];
       return $re;
	}
	public function ean13(string $text):array{
		$re=[];
		$cd=[
			"l"=>[
				"l"=>[
					0=>[0,0,0,1,1,0,1],
					1=>[0,0,1,1,0,0,1],
					2=>[0,0,1,0,0,1,1],
					3=>[0,1,1,1,1,0,1],
					4=>[0,1,0,0,0,1,1],
					5=>[0,1,1,0,0,0,1],
					6=>[0,1,0,1,1,1,1],
					7=>[0,1,1,1,0,1,1],
					8=>[0,1,1,0,1,1,1],
					9=>[0,0,0,1,0,1,1]
				],
				"r"=>[
					0=>[0,1,0,0,1,1,1],
					1=>[0,1,1,0,0,1,1],
					2=>[0,0,1,1,0,1,1],
					3=>[0,1,0,0,0,0,1],
					4=>[0,0,1,1,1,0,1],
					5=>[0,1,1,1,0,0,1],
					6=>[0,0,0,0,1,0,1],
					7=>[0,0,1,0,0,0,1],
					8=>[0,0,0,1,0,0,1],
					9=>[0,0,1,0,1,1,1]
				]
			],
			"r"=>[
				0=>[1,1,1,0,0,1,0],
				1=>[1,1,0,0,1,1,0],
				2=>[1,1,0,1,1,0,0,],
				3=>[1,0,0,0,0,1,0],
				4=>[1,0,1,1,1,0,0],
				5=>[1,0,0,1,1,1,0],
				6=>[1,0,1,0,0,0,0],
				7=>[1,0,0,0,1,0,0],
				8=>[1,0,0,1,0,0,0],
				9=>[1,1,1,0,1,0,0]
			],
			"p"=>[
				0=>[0,0,0,0,0,0],
				1=>[0,0,1,0,1,1],
				2=>[0,0,1,1,0,1],
				3=>[0,0,1,1,1,0],
				4=>[0,1,0,0,1,1],
				5=>[0,1,1,0,0,1],
				6=>[0,1,1,1,0,0],
				7=>[0,1,0,1,0,1],
				8=>[0,1,0,1,1,0],
				9=>[0,1,1,0,1,0]
			]
		];
		$t=$text;	
		$fist=(int)$t[0];
		$wd=$cd["p"][$fist];
		array_push($re,1,0,1);
		for($i=1;$i<7;$i++){
			$s=(int)$t[$i];
			if($i>0&&$i<7){//--ตัวเลขลำดับ2-7
				$lr="l";
				if($wd[$i-1]==1){
					$lr="r";
				}
				for($l=0;$l<7;$l++){
					array_push($re,$cd["l"][$lr][$s][$l]);
				}
			}
		}
		array_push($re,0,1,0,1,0);
		for($i=7;$i<strlen($t);$i++){
			$s=(int)$t[$i];
			if($i>6&&strlen($t)){//--ตัวเลขลำดับ7-12,13
				for($l=0;$l<7;$l++){
					array_push($re,$cd["r"][$s][$l]);
				}
			}
		}
		array_push($re,1,0,1,0);
		return $re;
	}
	public function itf14(string $text=null):array{
		$re=[];
		//$text=str_pad($text,14,"0",STR_PAD_LEFT);
		$cd=[
			0=>[0,0,1,1,0],
			1=>[1,0,0,0,1],
			2=>[0,1,0,0,1],
			3=>[1,1,0,0,0],
			4=>[0,0,1,0,1],
			5=>[1,0,1,0,0],
			6=>[0,1,1,0,0],
			7=>[0,0,0,1,1],
			8=>[1,0,0,1,0],
			9=>[0,1,0,1,0]
		];
		array_push($re,0,0,0,0);
		for($i=0;$i<strlen($text);$i++){
			if(($i+1)%2==0){
				$n1=(int)$text[$i-1];
				$n2=(int)$text[$i];
				for($j=0;$j<5;$j++){
					array_push($re,$cd[$n1][$j]);
					array_push($re,$cd[$n2][$j]);
				}
			}
		}
		array_push($re,1,0,0,0,);
		return $re;
	}
	public function itf(string $text):array{
		$text=(strlen($text)==0)?"00":$text;
		$text=(strlen($text)%2==1)?"0".$text:$text;
		$cd=[
			0=>[1,1,2,2,1],
			1=>[2,1,1,1,2],
			2=>[1,2,1,1,2],
			3=>[2,2,1,1,1],
			4=>[1,1,2,1,2],
			5=>[2,1,2,1,1],
			6=>[1,2,2,1,1],
			7=>[1,1,1,2,2],
			8=>[2,1,1,2,1],
			9=>[1,2,1,2,1]
		];
		//print_r($cd[0][0]);
		$l=strlen($text);
		$dt=[1,1,1,1];
		$c=0;
		for($i=0;$i<$l-1;$i++){
			$n=intval(substr($text,$i,1));
			$n2=intval(substr($text,$i+1,1));
			for($k=0;$k<5;$k++){
				$dt[4+$c]=$cd[$n][$k];
				$c+=1;
				$dt[4+$c]=$cd[$n2][$k];
				$c+=1;
			}
			$i+=1;
		}
		$dl=count($dt);
		$dt[$dl]=2;
		$dt[$dl+1]=1;
		$dt[$dl+2]=1;
		$re=[];
		$n=0;
		for($i=0;$i<count($dt);$i++){
			$b=($i%2==0)?1:0;
			if($dt[$i]==1){
				$re[$n]=$b;
				$n++;
			}else if($dt[$i]==2){
				$re[$n]=$b;
				$n++;
				$re[$n]=$b;
				$n++;
			}
		}
		return $re;
	}
}
