class device extends main{
	constructor(){
		super()
		this.icon={};
	}
	regisSubmit(){
		let f=document.forms.device_pos
		let dt={"data":{"a":"device","submith":"clicksubmit","b":"pos","c":"regis",
			"no":f.no.value,"sku":f.sku.value,
			"name":f.name.value,"disc":f.disc.value},
			"result":Dv.devicePOSSaveResult,"error":Dv.devicePOSSaveError}	
		this.setFec(dt)	
	}
	devicePOSSaveResult(re,form,bt){
		if(re["result"]){
			let data=re["data"]["ip"]
			let url_to="?a=device&b=pos"
			let url_key=form.get("url_key")
			let c=form.get("c")
			let uploadtype=(c=="regis")?"new":"add"
			let count_img=Object.keys(Dv.icon).length
			Ful.fileUploadImgs(uploadtype,'device_pos','ip',data,'Dv.icon',url_to,'ed',null,'Dv.devicePOSUploafFileError(\''+data+'\')')
			if(uploadtype=="add"||count_img==0){
				alert("สำเร็จ")
				let ed=re["data"]["sku"]
				location.href="?a=device&b=pos&ed="+ed
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
}
