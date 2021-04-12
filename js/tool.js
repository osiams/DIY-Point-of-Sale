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
	resetFactory(){
		let rid=this.rid()
		let dt={"title":"โปรดยืนยัน การตั้งค่าโรงงาน","msg":"คุณต้องการ ล้างข้อมูล ในฐานข้อมูลโปรแกรม ให้เป็นค่า จากโรงงาน ข้อมูลที่มีอยู่จะสูญหายอย่างถาวร รวมทั้งรูปภาพทั้งหมดใน แฟ้ม /img/gallery/","width":400,"callback":"Tl.resetFactory2('"+rid+"')","rid":rid}
		this.dialogConfirm(dt)
	}
	resetFactory2(rid){
		this.dialogClose(rid,0)
		let rid2=this.rid()
		let a= Math.floor(Math.random()*1000)/10
		let b=Math.floor(Math.random()*1000)/10
		let dt={"title":"โปรดยืนยัน การตั้งค่าโรงงาน อีกครั้ง","msg":"โปรด กรอก ผลคูณของ "+a+"×"+b,"width":350,"rid":rid,"callback":"Tl.resetFactory3("+a+","+b+",'"+rid+"')"}
		this.dialogPrompt(dt)
	}
	resetFactory3(a,b,rid){
		let f="prompt_input_"+rid
		let c=this.id(f).value*1
		let l=(((a*10)*(b*10))/100)
		//alert("a="+a+";b="+b)
		//alert((((a*10)*(b*10))/100)+"="+c)
		let msg=""
		
		if(l==c){
			this.dialogClose(rid,0)
			this.process(1,null,null,"กำลังดำเนินการอยู่...")
			let dt={"data":{"a":"factory","b":"reset"},"result":Tl.resetFactory3Result,"error":Tl.resetFactory3Error}		
			this.setFec(dt)
		}else{
			msg=a+"×"+b+" ≠ "+c
			let rid2=this.rid()
			let dt={"msg":msg,"rid":rid2,"callback":"M.dialogClose('"+rid2+"',0,'"+f+"')"}
			this.dialogAlert(dt)
		}
	}
	resetFactory3Result(re,form,bt){
		if(re["result"]){
			M.process(0,1,1,null)
			localStorage.clear()
		}else{
			Tl.resetFactory3Error(re,form,bt)
		}
	}
	resetFactory3Error(re,form,bt){
		M.process(0,1,0,null)
		alert(re["message_error"])
	}
	getDownloadResult(re,form,bt){
		
	}
	getDownloadError(re,form,bt){
		
	}
}
