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
			alert(111111)
		}else{
			Me.closeTime2Error(re,form,bt)
		}
	}
	closeTime2Error(re,form,bt){
		alert(9999)
	}
}
