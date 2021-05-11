"use strict"
class me extends main {
	constructor(){
		super()

	}
	run(){


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
	min(drawers_sku,drawers_name){
		let rid = this.rid()
		let min="mt_time_min"
		let ref="mt_time_ref"
		let note="mt_time_note"
	let pn=this.ce("p",{"class":"me_min_pn"})
	this.end(pn,[this.cn("รหัสลิ้นชัก : "+drawers_sku)])
	this.end(pn,[this.ce("br",{})])
	this.end(pn,[this.cn("ชื่อ ลิ้นชัก : "+drawers_name)])
	let ct0=this.ce("div",{"class":"me_min"})
		let c1=this.ce("div",{"class":"min"})
			let db=this.ce("div",{"id":"cb"})
				let dm=this.ce("div",{"class":"min"})
				this.end(dm,[this.cn("💰")])
			this.end(db,[dm])
		this.end(c1,[db])
		let ct=this.ce("div",{"class":"formg"})
			let d=this.ce("div",{})
				let f=this.ce("form",{"class":"form100"})
					
				
					let l1=this.ce("label",{"class":"formg_label","id":"label_"+min,"for":min,"onfocus":"Me.formTransFocus(this)"})
					this.end(l1,[this.cn("จำนวนเงิน")])
					let i1=this.ce("input",{"id":min,"class":"want","type":"number","onfocus":"Me.formTransFocus(this)","onblur":"Me.formTransBlur(this)","step":"0.01"})
					
					let l2=this.ce("label",{"class":"formg_label","id":"label_"+ref,"for":ref,"onfocus":"Me.formTransFocus(this)"})
					this.end(l2,[this.cn("รหัส ref อ้างอิง")])
					let i2=this.ce("input",{"id":ref,"type":"text","onfocus":"Me.formTransFocus(this)","onblur":"Me.formTransBlur(this)"})
					
					let l3=this.ce("label",{"class":"formg_label","id":"label_"+note,"for":note,"onfocus":"Me.formTransFocus(this)"})
					this.end(l3,[this.cn("หมายเหตุ")])
					let i3=this.ce("input",{"id":note,"type":"text","onfocus":"Me.formTransFocus(this)","onblur":"Me.formTransBlur(this)"})
				this.end(f,[l1,i1,l2,i2,l3,i3])
			this.end(d,[f])
		this.end(ct,[d])
		this.end(ct0,[c1,ct])
		let bts = [
			{"value":"ยกเลิก","onclick":"M.dialogClose('"+rid+"')"},
			{"value":"ตกลง","id":"pressbuttoninputok","onclick":"Me.formTransSend('min','"+min+"','"+ref+"','"+note+"')"}
		]
		this.dialog({"rid":rid,"display":1,"pn":pn,"bts":bts,"ct":ct0,"title":"นำเงินเข้าลิ้นชัก","width":"250","ofc":1})
		this.id(min).focus()
	}
	formTransFocus(){
		let a=event.target
		if(a.tagName=="INPUT"&&a.parentNode.parentNode.parentNode.className=="formg"){
			M.id("label_"+a.id).className="formg_label_focus"			
		}
	}
	formTransBlur(){
		let a=event.target
		if(a.value.length==0){
			M.id("label_"+a.id).className="formg_label"
		}
	}
	formTransSend(type,min,ref,note){
		let min_=this.id(min).value
		let note_=this.id(note).value
		let dt={"data":{"a":"tran","b":"savetran_"+type,"min":min_,"note":note_},"result":Me.formTransSendResult,"error":Me.formTransSendError}		
		this.setFec(dt)
	}
	formTransSendResult(re,form,bt){
		if(re["result"]){
			M.dialogClose()
			let balance=re.data.money_balance
			Me.formSetBalanceHtml(balance)
		}else{
			Me.formTransSendError(re,form,bt)
		}
	}
	formTransSendError(re,form,bt){
		alert(re["message_error"])
	}
	formSetBalanceHtml(balance){
		if(this.id("me_money_balance")!=undefined){
			let t=this.nb(balance,2)
			this.id("me_money_balance").innerHTML=t
			let rid=this.rid()
			let msg="บันทึก การนำเงินเข้าลิ้นชัก สำเร็จ ลิ้นชักคุณจะมีเงินอยู่ "+t+ "บาท"
			let dt={"msg":msg,"rid":rid,"width":300,"callback":"M.dialogClose('"+rid+"',1)"}
			this.dialogAlert(dt)
		}
	}
	mout(drawers_sku,drawers_name){
		let rid = this.rid()
		let mout="mt_time_mout"
		let ref="mt_time_ref"
		let note="mt_time_note"
	let pn=this.ce("p",{"class":"me_min_pn"})
	this.end(pn,[this.cn("รหัสลิ้นชัก : "+drawers_sku)])
	this.end(pn,[this.ce("br",{})])
	this.end(pn,[this.cn("ชื่อ ลิ้นชัก : "+drawers_name)])
	let ct0=this.ce("div",{"class":"me_min"})
		let c1=this.ce("div",{"class":"mout"})
			let db=this.ce("div",{"id":"cb"})
				let dm=this.ce("div",{"class":"mout"})
				this.end(dm,[this.cn("💰")])
			this.end(db,[dm])
		this.end(c1,[db])
		let ct=this.ce("div",{"class":"formg"})
			let d=this.ce("div",{})
				let f=this.ce("form",{"class":"form100"})
					let l1=this.ce("label",{"class":"formg_label","id":"label_"+mout,"for":mout,"onfocus":"Me.formTransFocus(this)"})
					this.end(l1,[this.cn("จำนวนเงิน")])
					let i1=this.ce("input",{"id":mout,"class":"want","type":"number","onfocus":"Me.formTransFocus(this)","onblur":"Me.formTransBlur(this)","step":"0.01"})
					
					let l3=this.ce("label",{"class":"formg_label","id":"label_"+note,"for":note,"onfocus":"Me.formTransFocus(this)"})
					this.end(l3,[this.cn("หมายเหตุ")])
					let i3=this.ce("input",{"id":note,"type":"text","onfocus":"Me.formTransFocus(this)","onblur":"Me.formTransBlur(this)"})
				this.end(f,[l1,i1,l3,i3])
			this.end(d,[f])
		this.end(ct,[d])
		this.end(ct0,[c1,ct])
		let bts = [
			{"value":"ยกเลิก","onclick":"M.dialogClose('"+rid+"')"},
			{"value":"ตกลง","id":"pressbuttoninputok","onclick":"Me.formTransSend('mout','"+mout+"','"+ref+"','"+note+"')"}
		]
		this.dialog({"rid":rid,"display":1,"pn":pn,"bts":bts,"ct":ct0,"title":"นำเงินออกลิ้นชัก","width":"250","ofc":1})
		this.id(mout).focus()
	}
}
