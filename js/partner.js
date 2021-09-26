"use strict"
class partner extends main{
	constructor(){
		super()
	}
	run(){
		
	}
	edit(sku_root,s_type){
		let f=document.forms.partner
		f.action="?a=partner&b=edit&url_refer="+encodeURIComponent(window.location.href)
		f.sku_root.value=sku_root
		f.submit()
	}
	delete(sku_root,name){
		let y=confirm("คุณต้องการลบ "+name)
		if(y){
			let f=document.forms.partner
			f.action="?a=partner&b=delete&url_refer="+encodeURIComponent(window.location.href)
			f.sku_root.value=sku_root
			f.submit()
		}
	}
	setChangeClaim(did){
		if(did.checked){
			did.parentNode.parentNode.className="bg_yes_claim"
		}else{
			did.parentNode.parentNode.className="bg_no_claim"
		}
	}
	claimSend(form_name,confirm=0,rid=null){
		if(confirm==0){
			let rid=this.rid()
			let dt={"title":"โปรดยืนยัน","msg":"คุณต้องการส่งเคลมสินค้าที่เลือก","width":300,"callback":"Pn.claimSend('"+form_name+"',1)","rid":rid}
			this.dialogConfirm(dt)
		}else{
			this.dialogClose(rid)
			let dt={list:{}}
			let f=document.forms[form_name];
			dt.sku_root=f.sku_root.value
			for(let i=0;i<f.length;i++){
				if(f[i].type=="checkbox"){
					if(f[i].checked){
						let n=f[i+1].value
						dt.list[f[i].value]=n*1
						i=i+1
					}
				}
			}
			M.l(dt)
			let ds={"data":{"a":"partner","bb":"partner_details_claim","sku_root":dt.sku_root,"list":JSON.stringify(dt.list)},"result":Pn.claimSendResult,"error":Pn.claimSendError}
			this.setFec(ds)
		}
	}
	claimSendResult(re,form,bt){
		if(!re["result"]){
			Pn.claimSendError(re,form,bt)
		}else{
			let rid=M.rid()
			let sku_root=form.get("sku_root")
			let msg="สำเร็จ"
			let dt={"msg":msg,"callback":"M.dialogClose('"+rid+"');location.href='?a=partner&b=details&sku_root="+sku_root+"&bb=partner_details_claimsend';"}
			M.dialogAlert(dt)
		}
	}
	claimSendError(re,form,bt){
		let rid=M.rid()
		let msg="❌"+re["message_error"]
		let dt={"msg":msg,"callback":"M.dialogClose('"+rid+"');","width":300}
		M.dialogAlert(dt)
	}
	claimCancel(pn_root,bill_sku,date_reg,confirm=0,rid=""){
		if(confirm==0){
			let rid=this.rid()
			let dt={"title":"โปรดยืนยัน ","msg":"คุณต้องการ ยกเลิกการส่งเคลม","width":300,"callback":"Pn.claimCancel('"+pn_root+"','"+bill_sku+"','"+date_reg+"',1)","rid":rid}
			this.dialogConfirm(dt)
		}else{
			this.dialogClose(rid)
			let ds={"data":{"a":"partner","bb":"partner_details_claimsend_viewbill","sku_root":pn_root,"bill_sku":bill_sku,"date_reg":date_reg},"result":Pn.claimCancelResult,"error":Pn.claimCancelError}
			this.setFec(ds)
			//alert(pn_root+"-"+bill_sku+"-"+date_reg)
		}
	}
	claimCancelResult(re,form,bt){
		if(!re["result"]){
			Pn.claimCancelError(re,form,bt)
		}else{
			let rid=M.rid()
			let sku_root=form.get("sku_root")
			let msg="สำเร็จ"
			let dt={"msg":msg,"callback":"M.dialogClose('"+rid+"');location.href='?a=partner&b=details&sku_root="+sku_root+"&bb=partner_details_claimsend';"}
			M.dialogAlert(dt)
		}
	}
	claimCancelError(re,form,bt){
		let rid=M.rid()
		let msg="❌"+re["message_error"]
		let dt={"msg":msg,"callback":"M.dialogClose('"+rid+"');","width":300}
		M.dialogAlert(dt)
	}
}
