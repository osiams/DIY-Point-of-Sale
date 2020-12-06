 /*--apt DIY_POS;--ext js;--version 0.0;*/
"use strict"
class product extends main{
	constructor(){
		super()
	}
	label(sku_root){
		let dt={"data":{"a":"bill58","b":"labelPrint","sku_root":sku_root},"result":Pd.labelResult,"error":Pd.labelError}
		this.setFec(dt)
	}
	labelSticker(skuroot){
		location.href="?a=barcode&skuroot="+skuroot
	}
	labelResult(re,form,bt){
		if(re["result"]){

		}else{
			Pd.labelError(re,form,bt)
		}
	}
	labelError(re,form,bt){
		alert("❌ เกิดข้อผิดพลาด "+re["message_error"])
	}
	productDelete(sku_root,name){
		let y=confirm("คุณต้องการลบ \n\""+name+"\"\nรายชื่อ และจำนวนจะหายไปจากระบบ ทั้งหมด\n แต่ประวัติการขาย ยังอยู่ ")
		if(y){
			let f=document.forms.product
			f.action="?a=product&b=delete"
			f.sku_root.value=sku_root
			f.submit()
		}
	}
	productStat(sku_root,name,value=""){
		let y=prompt("คุณต้องการปรับสถานะ \n\""+name+"\"\เป็น\nพิมพ์ b   ขึ้นบัญชีดำ\nพิมพ์ r   ขึ้นบัญชีแดง หยุดขาย,พักขาย\nพิมพ์ y   ขึ้นบัญชีเหลือง ต้องตรวจสอบเป็นพิเศษ \nพิมพ์ c   สินค้าขายได้ตามปกติ \n***ใส่หมายเหตุ ให้เว้นวรรค 1 ที ตามด้วยข้อความ\nเช่น\nb สินค้าไม่มาตรฐาน",value)
		if(y!==null){
			let dt={"data":{"a":"product","b":"stat","sku_root":sku_root,"y":y,"name":name},"result":Pd.statResult,"error":Pd.statError}
			this.setFec(dt)
		}
	}
	statResult(re,form,bt){
		if(re["result"]){
			alert("✔️ สำเร็จ")
			location.reload();
		}else{
			Pd.statError(re,form,bt)
		}
	}
	statError(re,form,bt){
		alert("❌ เกิดข้อผิดพลาด "+re["message_error"])
		this.productStat(form.get("sku_root"),form.get("name"),form.get("y"))
	}
	productEdit(sku_root){
		let f=document.forms.product
		f.action="?a=product&b=edit"
		f.sku_root.value=sku_root
		f.submit()
	}
	productReg(sku_root){
		let f=document.forms.product
		f.action="?a=product&b=regis"
		f.sku_root.value=sku_root
		f.submit()
	}
	regisSubmit(a){
		let y=document.getElementById("product_regisq")
		y.submit()
	}
	checkUnq(did,key){
		if(did.value.trim().length>0){
			let dt={"data":{"a":"product","b":"checkunq","key":key,"val":did.value.trim()},"result":Pd.unqResult,"error":Pd.unqError}
			this.setFec(dt)
		}
	}
	unqResult(re,form,bt){
		if(re["result"]){
			Pd.labelError(re,form,bt)
		}else{
			
		}
	}
	unqError(re,form,bt){
		alert("❌ เกิดข้อผิดพลาด "+re["message_error"])
	}
	xxproductAction(did){
		M.popup(did,'Pd.productActionMenu(did)')
	}
	xxproductActionMenu(did){
		let ct=this.ce("div",{"class":"l"})
		let w=[]
		let o=did.parentNode
		let c=0;
		for(let i=0;i<o.childNodes.length;i++){
			let u=o.childNodes[i]
			if(u.tagName=="A"){
				c=c+1
				if(c>1){
					w[c]=this.ce("a",{"onclick":u.getAttribute("onclick")})
					this.end(w[c],[this.cn(u.innerHTML+" "+u.title)])
					this.end(ct,[w[c]])
				}
			}
		}
		return ct
	}
}
