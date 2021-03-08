"use strict"
class form_selects{
	constructor(main){
		this.main=main
		this.partner={}
	}
	run(){

	}
	ctAddPartner(form_name,display_id){
		this.partner[display_id]={}
		let partner_list=document.forms[form_name]["partner_list"].value
		let dt={"data":{"a":"form_selects","b":"partner","display_id":display_id,"from_name":form_name,"partner_list":partner_list},"result":Fsl.getListPartnerResult,"error":Fsl.getListPartnerError}
		this.main.setFec(dt)
	}
	getListPartnerResult(re,form,bt){
		if(re["result"]){
			Fsl.ctSelectPartner(re,form,bt)
		}else{
			Fsl.getListPartnerError(re,form,bt)
		}
	}
	getListPartnerError(re,form,bt){
		alert("error")
		//Gp.ctSelectProp(re,form,bt)
	}
	ctSelectPartner(re,form,bt){
		let rid = this.main.rid()
		let arr = re.data["get"]
		
		let display_id = form.get("display_id")
		let form_name = form.get("from_name")
		
		let partner_list = form.get("partner_list")
		let ct=this.main.ce("div",{})
			let ct0 = this.main.ce("div",{"id":"ct0_partner_"+display_id})
			let fron = this.main.ce("form",{"name":rid,"style":"width:100%;text-align:center;"})
			let d1 = this.main.ce("div",{"class":"selects_list_partner"})

			let partner_has = partner_list.substring(1, partner_list.length-1).split(",,")
				for(let i=0;i<arr.length;i++){
					let ckrid = this.main.rid()
					let div1=this.main.ce("div",{"class":"i"+((i%2)+1)})
						let ck = this.main.ce("input",{"type":"checkbox","id":ckrid,"name":"checkbox_"+rid,"data-icon":arr[i]["icon"],"data-name":arr[i]["name"],"value":arr[i]["sku_root"],"onchange":"Fsl.selectCkPartner(this,'"+display_id+"')"})
				
						if(partner_has.includes(arr[i]["sku_root"])){
							ck.checked = true
						}	
						let div_img=this.main.ce("div",{"class":"img32"})
							let img=this.main.ce("img",{"src":"img/gallery/32x32_"+arr[i]["icon"],"alt":arr[i]["name"],"onerror":"this.src='img/pos/64x64_null.png'"})	
						this.main.end(div_img,[img])	
						let boc = this.main.ce("label",{"for":ckrid})		
							let tn = this.main.cn(arr[i]["name"])
						this.main.end(boc,[tn])
						let s=this.main.ce("div",{})
						this.main.end(s,[this.main.cn("⬆")])
					this.main.end(div1,[ck,div_img,boc,s])
					this.main.end(d1,[div1])
				}
			this.main.end(fron,[d1])
			this.main.end(ct0,[fron])	
			let ct1 = this.main.ce("div",{"id":"ct1_partner_"+display_id})
		this.main.end(ct,[ct0,ct1])	
		let bts = [
			{"value":"⬅ เลือกเพิ่ม","style":"visibility:hidden","id":"bt_back_select_"+display_id,"onclick":"Fsl.backSelectPartner(this,'"+display_id+"')"},
			{"value":"ดูที่เลือก (0)","id":"bt_select_n_"+display_id,"onclick":"Fsl.viewSlectedPartner(this,'"+display_id+"')"}
		]
		M.dialog({"rid":rid,"display":1,"ct":ct,"bts":bts,"title":"เลือกคู่ค้า","width":"250"})
	}
	selectCkPartner(did,display_id){
		let sku_root=did.value
		let name=did.getAttribute("data-name")
		let icon=did.getAttribute("data-icon")		
		if(did.checked){
			if(!this.partner[display_id].hasOwnProperty(sku_root)){
				this.partner[display_id][sku_root]={
					"icon":icon,
					"name":name
				}
			}
			
		}else{
			if(this.partner[display_id].hasOwnProperty(sku_root)){
				delete this.partner[display_id][sku_root]
			}
		}
		let count=Object.keys(this.partner[display_id]).length
		this.main.id("bt_select_n_"+display_id).value="ดูที่เลือก ("+count+")"
	}
	viewSlectedPartner(did,display_id){
		this.main.id("ct0_partner_"+display_id).style.display="none"
		this.main.id("ct1_partner_"+display_id).style.display="block"
		this.viewPartnerSlected(did,display_id)
		this.main.id("bt_back_select_"+display_id).style.visibility="visible"
		did.value="ยืนยันที่เลือก"
		did.parentNode.parentNode.click()
	}
	backSelectPartner(did,display_id){
		this.main.id("ct0_partner_"+display_id).style.display="block"
		this.main.id("ct1_partner_"+display_id).style.display="none"
		did.style.visibility="hidden"
		let count=Object.keys(this.partner[display_id]).length
		this.main.id("bt_select_n_"+display_id).value="ดูที่เลือก ("+count+")"
	}
	viewPartnerSlected(did,display_id){
		let ct=this.main.id("ct1_partner_"+display_id)
		this.main.rmc_all(ct)
		let i=-1
		for (let prop in this.partner[display_id]) {
			i=i+1
			let d1 = this.main.ce("div",{"class":"selected_list_partner"})
				let d2 = this.main.ce("div",{"data-sku_root":prop,"id":"select_at_"+i,"class":"i"+((i%2)+1)})
					let div_at=this.main.ce("div",{})
					this.main.end(div_at,[this.main.cn(i+1)])	
					let div_img=this.main.ce("div",{"class":"img32"})
						let img=this.main.ce("img",{"src":"img/gallery/32x32_"+this.partner[display_id][prop]["icon"],"alt":this.partner[display_id][prop]["name"],"onerror":"this.src='img/pos/64x64_null.png'"})	
					this.main.end(div_img,[img])	
					let div_name=this.main.ce("div",{})
					this.main.end(div_name,[this.main.cn(this.partner[display_id][prop]["name"])])	
					let div_move=this.main.ce("div",{"onclick":"Fsl.selectPartnerMove(this,'"+display_id+"',"+i+")"})
					this.main.end(div_move,[this.main.cn("⇅")])
					let div_del=this.main.ce("div",{"title":"ลบออก"})
					this.main.end(div_del,[this.main.cn("×")])	
				this.main.end(d2,[div_at,div_img,div_name,div_move,div_del])	
			this.main.end(d1,[d2])	
			this.main.end(ct,[d1])	
		}
	}
	selectPartnerMove(did,display_id,index){
		let a=this.main.id("ct1_partner_"+display_id)
		for(let i=0;i<a.childNodes.length;i++){
			let b=a.childNodes[i].childNodes[0].childNodes[3]
			if(i!=index){
				b.innerHTML="🚩"
				b.style.backgroundColor="LightGreen"
				b.setAttribute("onclick","Fsl.selectPartnerMoveSet(this,'"+display_id+"',"+index+","+i+")")
				b.onmouseover=()=>{}
				b.onmouseout=()=>{}
			}
		}
	}
	selectPartnerMoveSet(did,display_id,index_from,index_to){
		let no={}//Object.assign({}, this.partner[display_id])
		
		let a=this.main.id("ct1_partner_"+display_id)
		let newnode=a.childNodes[index_from].cloneNode(true);
		a.insertBefore(newnode, a.childNodes[index_to])
		if(index_to<index_from){
			a.removeChild(a.childNodes[index_from+1])
		}else{
			a.removeChild(a.childNodes[index_from])
		}		
		for(let i=0;i<a.childNodes.length;i++){
			let k=a.childNodes[i].childNodes[0].getAttribute("data-sku_root")
			console.log(k)
			no[k]=this.partner[display_id][k]
			let b=a.childNodes[i].childNodes[0].childNodes[3]
			a.childNodes[i].childNodes[0].childNodes[0].innerHTML=i+1
			b.innerHTML="⇅"
			b.style.backgroundColor="gray"
			b.setAttribute("onclick","Fsl.selectPartnerMove(this,'"+display_id+"',"+i+")")
			b.onmouseover=()=>{b.style.backgroundColor="orange"}
			b.onmouseout=()=>{b.style.backgroundColor="gray"}
		}
		this.partner[display_id]=no

	}
}
