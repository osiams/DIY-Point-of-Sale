"use strict"
class product extends main{
	constructor(){
		super()
		this.group_list = []
		this.prop_list = []
		this.prop_b4edit= []
		this.group_b4edit = null
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
		let y=confirm("คุณต้องการลบ \n\""+name+"\"\nรายชื่อ และจำนวนจะหายไปจากระบบ ทั้งหมด\n แต่ประวัติการขาย และรูปภาพสินค้า ยังคงอยู่ ยังอยู่ ")
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
		f.action="?a=product&b=edit&url_refer="+encodeURIComponent(window.location.href)
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
	setProp(did,table_id,prop_list){
		let prop = this.group_list[did.value]["prop"]
		let tb = this.id(table_id)
		let tb_length = tb.rows.length
		let index = 0	//--มี th อยู่
		for(let i=tb_length-1;i>0;i--){
			tb.deleteRow(i);
		}
		for(let i=0;i<prop.length;i++){
			let name = this.prop_list[prop[i]]["name"]
			let sku_root =  this.prop_list[prop[i]]["sku_root"]
			let data_type = this.prop_list[prop[i]]["data_type"]
			index+=1
			tb.insertRow(index)
			tb.rows[index].className = "i"+(((index-1)%2)+1)
			tb.rows[index].insertCell(0)
			tb.rows[index].insertCell(1)
				tb.rows[index].cells[0].style = "text-align:left;"
			tb.rows[index].cells[0].innerHTML = name
			let lr = "left"
			if(data_type == "n"){
				lr = "right"
			}
			let vae = ""
			if(did.value==this.group_b4edit || 1 == 1){
				if(this.prop_b4edit[sku_root] != undefined){
					vae = this.prop_b4edit[sku_root]
				}
			}
			let ip =this.ce("input",{"name":"prop_"+sku_root,"type":"text","style":"width:calc(100% - 8px);text-align:"+lr+"","value":vae})
			if(data_type =="b"){
				ip = this.ce("select",{"name":"prop_"+sku_root,"style":"width:100%;appearance: none;"})
					let op1 =this.ce("option",{"value":"0"})
					this.end(op1,[this.cn("❔")])
					let op2 =this.ce("option",{"value":"1"})
					this.end(op2,[this.cn("✔")])
					let op3 =this.ce("option",{"value":"-1"})
					this.end(op3,[this.cn("❌")])
				this.end(ip,[op1,op2,op3])	
			}
			tb.rows[index].cells[1].appendChild(ip)
		}
	}
	setUnitSelect(did){
		let f=this.id("product_regis_unit")
		let hg=f.querySelectorAll("optgroup")

		let has=0;
		for(let i=0;i<hg.length;i++){
			if(hg[i].getAttribute("data-s_type") == did.value){
				has=1;
				hg[i].hidden=false
				//hg[i].childNodes[0].selected
				f.value=hg[i].childNodes[0].value
			}else{
				hg[i].hidden=true
			}
		}
		if(has==0){
			did.value="p"
			f.value="defaultroot"		
			for(let i=0;i<hg.length;i++){
				if(hg[i].getAttribute("data-s_type") =="p"){
					hg[i].hidden=false
				}
			}		
		}
	}
}
