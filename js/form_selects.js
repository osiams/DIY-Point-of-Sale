"use strict"
class form_selects{
	constructor(main){
		this.main=main
		this.partner={}
		this.search={}
	}
	run(){

	}
	ctAddPartner(form_name,dialog_id=null,display_id,partner_list_id,get_type="new",page=1,oshid=null,ipshid=null,lid=0,fl="name",tx=""){
		if(this.partner[display_id]==undefined){
			this.partner[display_id]={}
			this.search[display_id]={}
		}
		dialog_id=(dialog_id==null)?this.main.rid():dialog_id
		
		let partner_list=document.forms[form_name][partner_list_id].value
		
		let dt={"data":{"a":"form_selects","b":"partner","dialog_id":dialog_id,"display_id":display_id,
				"from_name":form_name,"partner_list":partner_list,"partner_list_id":partner_list_id,"get_type":get_type,"page":page,
				"oshid":oshid,"ipshid":ipshid,"lid":lid,"fl":fl,"tx":tx},"result":Fsl.getListPartnerResult,"error":Fsl.getListPartnerError}		
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
		//Gp.ctSelectProp(re,form,bt)
	}
	ctSelectPartner(re,form,bt){
		let rid = form.get("dialog_id")
		let arr = re.data["get"]

		let display_id = form.get("display_id")
		let dialog_id = form.get("dialog_id")
		let partner_list_id = form.get("partner_list_id")
		let form_name = form.get("from_name")
		let get_type=form.get("get_type")
		
		let partner_list = form.get("partner_list")
		let tsh={"name":"ชื่อ","brand_name":"ชื่อการค้า","sku":"รหัสภายใน"}
		let cpn=this.main.ce("div",{"id":"cpn0_partner_"+display_id,"class":"selected_list_partner_search"})
			let oshid="option_search_partner_id_"+display_id
			let pn_sh=this.main.ce("select",{"id":oshid})
				for (let prop in tsh) {
					let op_sh=this.main.ce("option",{"value":prop})
					let op_tx=this.main.cn(tsh[prop])
					this.main.end(op_sh,[op_tx])
					this.main.end(pn_sh,[op_sh])
				}
			let ipshid="input_search_partner_id_"+display_id
			let its=this.main.ce("input",{"id":ipshid,"type":"text"})
			let ibs=this.main.ce("input",{"type":"button","value":"🔍","onclick":"Fsl.selectPartnerSearch('"+oshid+"','"+ipshid+"','"+form_name+"','"+dialog_id+"','"+display_id+"','"+partner_list_id+"')"})		
		this.main.end(cpn,[pn_sh,its,ibs])
		let ct=this.main.ce("div",{})
			let ct0 = this.main.ce("div",{"id":"ct0_partner_"+display_id})
			let fron = this.main.ce("form",{"name":rid,"style":"width:100%;text-align:center;"})
			let d1 = this.main.ce("div",{"class":"selects_list_partner"})

			let lid=form.get("lid")
			
			let partner_has = partner_list.substring(1, partner_list.length-1).split(",,")
				for(let i=0;i<arr.length;i++){
					let ckrid = "checkboxid_"+arr[i]["sku_root"]
					let div1=this.main.ce("div",{"class":"i"+((i%2)+1)})
						let ck = this.main.ce("input",{"type":"checkbox","id":ckrid,"name":"checkbox_"+rid,"data-icon":arr[i]["icon"],"data-name":arr[i]["name"],"value":arr[i]["sku_root"],"onchange":"Fsl.selectCkPartner(this,'"+display_id+"')"})
				
						if(partner_has.includes(arr[i]["sku_root"]) || this.partner[display_id].hasOwnProperty(arr[i]["sku_root"]) ){
							ck.checked = true
						}	
						let div_img=this.main.ce("div",{"class":"img32"})
							let img=this.main.ce("img",{"src":"img/gallery/32x32_"+arr[i]["icon"],"alt":arr[i]["name"],"onerror":"this.src='img/pos/64x64_null.png'"})	
						this.main.end(div_img,[img])	
						let boc = this.main.ce("label",{"for":ckrid})		
							let tn = this.main.cn(arr[i]["name"])
						this.main.end(boc,[tn])
						let s=this.main.ce("div",{"data-rid_close":rid,"onclick":"Fsl.select1Partner(this,'"+display_id+"','"+partner_list_id+"')"})
						this.main.end(s,[this.main.cn("⬆")])
					this.main.end(div1,[ck,div_img,boc,s])
					this.main.end(d1,[div1])
					lid=arr[i]["id"]
				}
			this.main.end(fron,[d1])
			let div_page=this.ctPage(re,form,form_name,dialog_id,display_id,partner_list_id,lid)
			this.main.end(ct0,[fron,div_page])	
			let ct1 = this.main.ce("div",{"id":"ct1_partner_"+display_id})
		this.main.end(ct,[ct0,ct1])	
		let count=Object.keys(this.partner[display_id]).length
		let bts = [
			{"value":"⬅ เลือกเพิ่ม","style":"visibility:hidden","id":"bt_back_select_"+display_id,"onclick":"Fsl.backSelectPartner(this,'"+display_id+"','"+partner_list_id+"')"},
			{"value":"ดูที่เลือก ("+count+")","rid_close":rid,"id":"bt_select_n_"+display_id,"onclick":"Fsl.viewSlectedPartner(this,'"+display_id+"','"+partner_list_id+"')"}
		]
		if(get_type=="new"){
			
			M.dialog({"rid":rid,"display":1,"pn":cpn,"ct":ct,"bts":bts,"title":"เลือกคู่ค้า","width":"250"})
		}else if(get_type=="update"){
			this.main.rmc_all(this.main.id("ct0_partner_"+display_id))
			this.main.end(this.main.id("ct0_partner_"+display_id),[fron,div_page])	
			//this.main.id("ct0_partner_"+display_id).appenChild()
		}
	}
	selectPartnerSearch(option_search_id,input_search_id,form_name,dialog_id,display_id,partner_list_id,page=1,lid=0){
		let fl=this.main.id(option_search_id).value
		let tx=this.main.id(input_search_id).value
		this.ctAddPartner(form_name,dialog_id,display_id,partner_list_id,"update",page,option_search_id,input_search_id,(lid),fl,tx,)
	}
	ctPage(re,form,form_name,dialog_id,display_id,partner_list_id,lid=0){
		let per=re.data.page["per"]
		let page=re.data.page["page"]
		let count=re.data.count[0]["count"]*1
		let n_page=Math.ceil(count/per)
		let tx= form.get("tx")
		let ct=this.main.ce("div",{"class":"c"})
			if(tx.trim()==""){
				let sl=this.main.ce("select",{"onchange":"Fsl.partnerGoPage(this,'"+form_name+"','"+dialog_id+"','"+display_id+"','"+partner_list_id+"')"})
					for(let i=1;i<=n_page;i++){
						let op =this.main.ce("option",{})
							let t=this.main.cn(i)
							if(page==i){
								
							}
						this.main.end(op,[t])
						this.main.end(sl,[op])
					}
					sl.selectedIndex=(page-1)
				this.main.end(ct,[this.main.cn("หน้า : "),sl])
			}else{
				count=re.data["get"].length
				lid=lid
				let sp=this.main.ce("span",{"class":"fsl_page_search"})
				let oshid=form.get("oshid")
				let ipshid=form.get("ipshid")
				
				if(page>1){
					let lid_ref=(this.search[display_id][page-1]!=undefined)?this.search[display_id][page-1]:0
					let nex_tx="Fsl.selectPartnerSearch('"+oshid+"','"+ipshid+"','"+form_name+"','"+dialog_id+"','"+display_id+"','"+partner_list_id+"',"+(page-1)+","+lid_ref+")"
						let a=this.main.ce("a",{"onclick":nex_tx})
						this.main.end(a,[this.main.cn("⬅️ก่อนหน้า️")])
						this.main.end(ct,[a])
				}				
				
				this.main.end(sp,[this.main.cn("หน้า : "+page)])
				this.main.end(ct,[sp])
				

				if(page==1){
					if(count>per){
						this.search[display_id][page]=0
						let nex_tx="Fsl.selectPartnerSearch('"+oshid+"','"+ipshid+"','"+form_name+"','"+dialog_id+"','"+display_id+"','"+partner_list_id+"',"+(page+1)+","+lid+")"
						alert("page="+page+";"+nex_tx)
						let a=this.main.ce("a",{"onclick":nex_tx})
						this.main.end(a,[this.main.cn("ถัดไป➡️")])
						this.main.end(ct,[a])
					}
				}
			}
		return ct
	}
	partnerGoPage(did,form_name,dialog_id,display_id,partner_list_id){
		this.ctAddPartner(form_name,dialog_id,display_id,partner_list_id,"update",did.value)
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
	viewSlectedPartner(did,display_id,partner_list_id){
		this.main.id("ct0_partner_"+display_id).style.display="none"
		this.main.id("cpn0_partner_"+display_id).style.display="none"
		this.main.id("ct1_partner_"+display_id).style.display="block"
		this.viewPartnerSlected(did,display_id)
		this.main.id("bt_back_select_"+display_id).style.visibility="visible"
		did.value="ยืนยันที่เลือก"
		did.setAttribute("onclick","Fsl.selectPartnerOK(this,'"+display_id+"','"+partner_list_id+"')")
		did.parentNode.parentNode.click()
	}
	backSelectPartner(did,display_id,partner_list_id){
		this.main.id("ct0_partner_"+display_id).style.display="block"
		this.main.id("cpn0_partner_"+display_id).style.display="grid"
		this.main.id("ct1_partner_"+display_id).style.display="none"
		did.style.visibility="hidden"
		let count=Object.keys(this.partner[display_id]).length
		this.main.id("bt_select_n_"+display_id).value="ดูที่เลือก ("+count+")"
		this.main.id("bt_select_n_"+display_id).setAttribute("onclick","Fsl.viewSlectedPartner(this,'"+display_id+"','"+partner_list_id+"')")
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
					let div_del=this.main.ce("div",{"onclick":"Fsl.deletePartner(this,'"+display_id+"','"+prop+"')","title":"ลบออก"})
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
	selectPartnerOK(did,display_id,partner_list_id){
		this.setEmptyTable(display_id)
		let rid_close=did.getAttribute("data-rid_close")
		
		this.selectPartnerOKAppend(display_id)

		this.selectPartnerListValue(display_id,partner_list_id)
		M.dialogClose(rid_close)
	}
	selectPartnerOKAppend(display_id){
		let t=this.main.id(display_id)
		let i=-1
		for (let prop in this.partner[display_id]) {
			i=i+1
			let r=t.insertRow(i);
			let cell0=r.insertCell(0)
			let cell1=r.insertCell(1)
			let cell2=r.insertCell(2)
			let div_img=this.main.ce("div",{"class":"img32"})
				let img=this.main.ce("img",{"src":"img/gallery/32x32_"+this.partner[display_id][prop]["icon"],"alt":this.partner[display_id][prop]["name"],"onerror":"this.src='img/pos/64x64_null.png'"})	
			this.main.end(div_img,[img])	
			cell0.innerHTML=i+1+"."
			this.main.end(cell1,[div_img])
			cell2.innerHTML=this.partner[display_id][prop]["name"]
		}		
	}
	setEmptyTable(display_id){
		let t=this.main.id(display_id)
		let len=t.rows.length
		for(let i=len-2;i>=0;i--){
			t.deleteRow(i)
		}
	}
	selectPartnerListValue(display_id,partner_list_id){
		let v=this.main.id(partner_list_id)
		v.value=""
		for (let prop in this.partner[display_id]) {
			v.value+=","+prop+","
		}
	}
	select1Partner(did,display_id,partner_list_id){
		let d=did.parentNode.childNodes[0]
		let sku_root=d.value
		let name=d.getAttribute("data-name")
		let icon=d.getAttribute("data-icon")		
		this.partner[display_id]={}
		this.partner[display_id][sku_root]={
					"icon":icon,
					"name":name
				}
		let count=Object.keys(this.partner[display_id]).length
		this.main.id("bt_select_n_"+display_id).value="ดูที่เลือก ("+count+")"
		this.selectPartnerOK(did,display_id,partner_list_id)
	}
	deletePartner(did,display_id,sku_root){
		did.parentNode.parentNode.removeChild(did.parentNode)
		if(this.partner[display_id][sku_root]!=undefined){
			delete this.partner[display_id][sku_root]
		}
		if(this.main.id("checkboxid_"+sku_root)!=undefined){
			this.main.id("checkboxid_"+sku_root).checked=false
		}
	}
	setLoadPartner(form_name,dialog_id=null,display_id,partner_list_id){
		if(this.partner[display_id]==undefined){
			this.partner[display_id]={}
			this.search[display_id]={}
		}
		dialog_id=(dialog_id==null)?this.main.rid():dialog_id
		let partner_list=document.forms[form_name][partner_list_id].value
		let dt={"data":{"a":"form_selects","b":"partner","c":"partner_get","dialog_id":dialog_id,"display_id":display_id,
			"from_name":form_name,"partner_list":partner_list,"partner_list_id":partner_list_id},
			"result":Fsl.getListPartnerLoadResult,"error":Fsl.getListPartnerLoadError
		}
		this.main.setFec(dt)
	}
	getListPartnerLoadResult(re,form,bt){
		if(re["result"]){
			Fsl.loadSetPartner(re,form,bt)
		}else{
			Fsl.getListPartnerLoadError(re,form,bt)
		}
	}
	getListPartnerLoadError(re,form,bt){

	}
	loadSetPartner(re,form,bt){
		let display_id = form.get("display_id")
		let dt=re["data"]
		for (let i=0;i<dt.length;i++) {
			this.partner[display_id][dt[i]["sku_root"]]={
				"icon":dt[i]["icon"],
				"name":dt[i]["name"]
			}
		}	
		this.selectPartnerOKAppend(display_id)	
	}
}
