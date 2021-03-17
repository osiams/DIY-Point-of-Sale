"use strict"
class tool extends main{
	constructor(){
		super()
	}
	run(){
		
	}
	pdtxtDownload(){
		let y=confirm("คุณต้องการ ดาวน์โหลด\npd.txt\n")
		if(y){
			location.href="?a=tool&b=tool_pdtxt"
		}
	}
	getDownloadResult(re,form,bt){
		
	}
	getDownloadError(re,form,bt){
		
	}
}
