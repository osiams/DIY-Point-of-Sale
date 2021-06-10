"use strict"
class pay extends main{
	constructor(){
		super()
	}
	printAgain(sku){
		let y=confirm("คุณต้องการพิมพ์ใหม่\nโปรดเรียกเก็บใบเสร็จเดิมคืนด้วย")
		if(y){
			let dt={"data":{"a":"bill58","b":"print_pay","sku":sku,"submith":"clicksubmit"},"result":Pa.printAgainResult,"error":Pa.printAgainError}
			this.setFec(dt)
		}
	}
	printAgainResult(re,form,bt){
		if(re["result"]){

		}else{
			Pa.printAgainError(re,form,bt)
		}
	}
	printAgainError(re,form,bt){
		alert("❌ เกิดข้อผิดพลาด "+re["message_error"])
	}
}
