class device extends main{
	constructor(){
		super()
		this.icon={};
	}
	regisSubmit(device){
		let f=null
		if(device=="pos"){
			f=document.forms.device_pos
		}else if(device=="drawers"){
			f=document.forms.device_drawers
		}
		let dt={"data":{"a":"device","submith":"clicksubmit","b":device,"c":"regis",
			"no":f.no.value,"sku":f.sku.value,
			"name":f.name.value,"disc":f.disc.value},
			"result":Dv.devicePOSSaveResult,"error":Dv.devicePOSSaveError}	
		if(device=="pos"){
			dt.data.drawers_id=f.drawers_id.value
		}	
		this.setFec(dt)	
	}
	devicePOSSaveResult(re,form,bt){
		if(re["result"]){
			let data=null
			let url_to=""
			let url_key=form.get("url_key")
			let c=form.get("c")
			let b=form.get("b")
			let uploadtype=(c=="regis")?"new":"add"
			let count_img=Object.keys(Dv.icon).length
			if(b=="pos"){
				data=re["data"]["ip"]
				url_to="?a=device&b=pos"
				Ful.fileUploadImgs(uploadtype,'device_pos','ip',data,'Dv.icon',url_to,'ed',null,'Dv.devicePOSUploafFileError(\''+data+'\')')
				if(uploadtype=="add"||count_img==0){
					alert("สำเร็จ")
					let ed=re["data"]["sku"]
					location.href="?a=device&b=pos&ed="+ed
				}
			}else if(b=="drawers"){
				data=re["data"]["sku"]
				url_to="?a=device&b=drawers"
				Ful.fileUploadImgs(uploadtype,'device_drawers','sku',data,'Dv.icon',url_to,'ed',null,'Dv.deviceDrawersUploafFileError(\''+data+'\')')
				if(uploadtype=="add"||count_img==0){
					alert("สำเร็จ")
					let ed=re["data"]["sku"]
					location.href="?a=device&b=drawers&ed="+ed
				}
			}
		}else{
			Dv.devicePOSSaveError(re,form,bt)
		}
	}
	devicePOSUploafFileError(ed=""){
		location.href="?a=device&b=pos&ed="+ed
	}
	devicePOSSaveError(re,form,bt){
		if(re["result"]!=undefined){
			alert("เกิดข้อผิดพลาด\n"+re["message_error"])
		}
	}
	deviceDrawersUploafFileError(ed=""){
		location.href="?a=device&b=drawers&ed="+ed
	}
	deviceDrawersSaveError(re,form,bt){
		if(re["result"]!=undefined){
			alert("เกิดข้อผิดพลาด\n"+re["message_error"])
		}
	}
	delete(ip,name){
		let rid=this.rid()
		let dt={"title":"โปรดยืนยัน การลบเครื่อวขายเงินสด","msg":"คุณต้องการลบ \""+name+"\"","width":320,"callback":"Dv.delete1('"+rid+"','"+ip+"')","rid":rid}
		this.dialogConfirm(dt)
	}
	delete1(rid,ip){
		let f=document.forms.device
		f.action="?a=device&b=pos&c=delete&url_refer="+encodeURIComponent(window.location.href)
		f.ip.value=ip
		f.submit()
	}
	edit(ip){
		let f=document.forms.device
		f.action="?a=device&b=pos&c=edit&url_refer="+encodeURIComponent(window.location.href)
		f.ip.value=ip
		f.submit()
	}
	deleteDrawers(sku,name){
		let rid=this.rid()
		let dt={"title":"โปรดยืนยัน การลบลิ้นชักเก็บเงิน ","msg":"คุณต้องการลบ \""+name+"\"","width":320,"callback":"Dv.deleteDrawers1('"+rid+"','"+sku+"')","rid":rid}
		this.dialogConfirm(dt)
	}
	deleteDrawers1(rid,sku){
		let f=document.forms.device
		f.action="?a=device&b=drawers&c=delete&url_refer="+encodeURIComponent(window.location.href)
		f.sku.value=sku
		f.submit()
	}
	editDrawers(sku){
		let f=document.forms.device
		f.action="?a=device&b=drawers&c=edit&url_refer="+encodeURIComponent(window.location.href)
		f.sku.value=sku
		f.submit()
	}
	deviceSumit(device){
		let f=null
		let gl_list_value=""
		if(device=="pos"){
			f=document.forms.device_pos
		}else if(device=="drawers"){
			f=document.forms.device_drawers
		}
		if(f.gallery_list!=undefined){
				gl_list_value=f.gallery_list.value
		}		
		let dt={}
		if(device=="pos"){
			dt={"data":{"a":"device","submith":"clicksubmit","b":device,"c":"edit",
				"no":f.no.value,"sku":f.sku.value,"gallery_list":gl_list_value,
				"name":f.name.value,"disc":f.disc.value,"drawers_id":f.drawers_id.value},
				"result":Dv.devicePOSEditResult,"error":Dv.devicePOSEditError}	
		}else if(device=="drawers"){
			dt={"data":{"a":"device","submith":"clicksubmit","b":device,"c":"edit",
				"no":f.no.value,"id":f.id.value,"sku":f.sku.value,"gallery_list":gl_list_value,
				"name":f.name.value,"disc":f.disc.value},
				"result":Dv.deviceDrawersEditResult,"error":Dv.deviceDrawersEditError}	
		}
		this.setFec(dt)	
	}
	devicePOSEditResult(re,form,bt){
		if(re["result"]){
			let data=re["data"]["ip"]
			let url_to="?a=device&b=pos"
			let url_key=form.get("url_key")
			let c=form.get("c")
			let uploadtype=(c=="regis")?"new":"add"
			let count_img=Object.keys(Dv.icon).length
			Ful.fileUploadImgs(uploadtype,'device_pos','ip',data,'Dv.icon',url_to,'ed',null,'Dv.posUploafFileError(\''+data+'\')')
			if(uploadtype=="add"||count_img==0){
				alert("สำเร็จ")
				let ed=re["data"]["sku"]
				location.href="?a=device&b=pos&ed="+ed
			}
		}else{
			Dv.devicePOSEditError(re,form,bt)
		}
	}
	posUploafFileError(ed=""){
		location.href="?a=device&b=pos&ed="+ed
	}
	devicePOSEditError(re,form,bt){M.l(re)
		if(re["result"]!=undefined){
			alert("เกิดข้อผิดพลาด\n"+re["message_error"])
		}
	}
	deviceDrawersEditResult(re,form,bt){
		if(re["result"]){
			let data=re["data"]["id"]
			let url_to="?a=device&b=pos"
			let url_key=form.get("url_key")
			let c=form.get("c")
			let uploadtype=(c=="regis")?"new":"add"
			let count_img=Object.keys(Dv.icon).length
			Ful.fileUploadImgs(uploadtype,'device_drawers','id',data,'Dv.icon',url_to,'ed',null,'Dv.drawersUploafFileError(\''+data+'\')')
			if(uploadtype=="add"||count_img==0){
				alert("สำเร็จ")
				let ed=re["data"]["sku"]
				location.href="?a=device&b=drawers&ed="+ed
			}
		}else{
			Dv.deviceDrawersEditError(re,form,bt)
		}
	}
	drawersUploafFileError(ed=""){
		location.href="?a=device&b=drawers&ed="+ed
	}
	deviceDrawersEditError(re,form,bt){M.l(re)
		if(re["result"]!=undefined){
			alert("เกิดข้อผิดพลาด\n"+re["message_error"])
		}
	}
}
