 /*--apt DIY_POS;--ext js;--version 0.0;*/
class billsell extends main{
	constructor(){
		super()
	}
	delete(did){
		let sku=did.getAttribute("data-sku")
		let d=prompt("คุณต้องการลบใบเสร็จเลขที่\n"+sku+"\nใส่หมายเหตุด้านล่าง หรือปล่อยว่าง")
		if(d!=null){
			let formData = new FormData()
			formData.append("a","bills")			
			formData.append("submith","clicksubmit")		
			formData.append("c","bill_sell_delete")		
			formData.append("b","delete")		
			formData.append("sku",sku)		
			formData.append("note",d)	
			M.fec("POST","",Bs.deleteResult,Bs.deleteError,null,formData)
		}
	}
	deleteResult(re,form,bt){
		if(re["result"]){
			alert("✅ สำเร็จ ยกเลิกสำเร็จ")
			location.reload(); 
		}else{
			Bs.deleteError(re,form,bt)
		}
	}
	deleteError(re,form,bt){
		alert("❌ ไม่สำเร็จ\n\n"+re["message_error"])
	}
	selectLotCut(did,pdroot){
		alert(pdroot)
		let dt={"data":{"a":"it","b":"select","sku_root":"proot","pdroot":pdroot},"result":Bs.selectResult,"error":Bs.selectError}
		this.setFec(dt)
	/*	let ct=this.ce("div",{})
		let p=this.ce("p",{})
		this.end(p,[this.cn("5555")])
		this.end(ct,[p])
		return ct*/
	}
	selectResult(re,form,bt){
		if(re["result"]){
			alert("✔️ สำเร็จ")
			location.reload();
		}else{
			Bs.selectError(re,form,bt)
		}
	}
	selectError(re,form,bt){
		alert("❌ เกิดข้อผิดพลาด "+re["message_error"])
	}
}
