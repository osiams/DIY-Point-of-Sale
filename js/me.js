"use strict"
class me extends main {
	constructor(){
		super()
	}
	closeTime(){
		let rid=this.rid()
		let dt={"title":"ปิดกะ","msg":"คุณต้องการ ปิดกะ พร้อมกับออกจากระบบ ","width":340,"callback":"Me.closeTime2('"+rid+"')","rid":rid}
		this.dialogConfirm(dt)
	}
	closeTime2(rid){
		this.dialogClose(rid,1)
		let dt={"data":{"a":"time","b":"close"},"result":Me.closeTime2Result,"error":Me.closeTime2Error}		
		this.setFec(dt)
	}
	closeTime2Result(re,form,bt){
		if(re["result"]){
			let rid=M.rid()
			let msg="ปิดกะ และ ออกจากระบบ สำเร็จ "
			let dt={"msg":msg,"rid":rid,"callback":"M.dialogClose('"+rid+"',1,);location.href='index.php'","width":300}
			M.dialogAlert(dt)
		}else{
			Me.closeTime2Error(re,form,bt)
		}
	}
	closeTime2Error(re,form,bt){
		alert("555555555555555555")
	}
	min(){
		let rid = this.rid()
		let ct=this.ce("div",{})
			let l1=this.ce("label",{})
			this.end(l1,[this.cn("จำนวนเงิน : ")])
			let i1=this.ce("input",{"type":"text"})
		this.end(ct,[l1,i1])
		let bts=[]
		this.dialog({"rid":rid,"display":1,"bts":bts,"ct":ct,"title":"นำเงินออกจากลิ้นชัก","width":"400","ofc":1})
	}
}
