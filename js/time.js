"use strict"
class time extends main{
	constructor(){
		super()
	}
	run(){
	}
	logout(){
		let f=document.forms.time
		f.b.value="logout"
		f.submit()
	}
	newTimeSubmit(){
		let f=document.forms.time
		f.b.value="regis"
		f.submit()
	}
	closeTime(user){
		let rid=this.rid()
		let dt={"title":"โปรดยืนยัน การปิดกะ","msg":"คุณต้องการ ปิดกะผู้ใช้ "+user,"width":350,"callback":"Ti.closeTime2('"+rid+"')","rid":rid}
		this.dialogConfirm(dt)
	}
	closeTime2(rid){
		this.dialogClose(rid)
		let dt={"data":{"a":"time","b":"closeother"},"result":Ti.closeTime2Result,"error":Ti.closeTime2Error}		
		this.setFec(dt)
	}
	closeTime2Result(re,form,bt){
		if(re["result"]){
			let rid=M.rid()
			let msg="ปิดกะ และ ออกจากระบบ สำเร็จ "
			let dt={"msg":msg,"rid":rid,"callback":"M.dialogClose('"+rid+"',1,);location.href='index.php'","width":300}
			M.dialogAlert(dt)
		}else{
			Ti.closeTime2Error(re,form,bt)
		}
	}
	closeTime2Error(re,form,bt){
		alert(re["message_error"])
	}
}
