"use strict"
class form_selects{
	constructor(main){
		this.main=main
		this.partner_full_data={}
		this.partner_old={}
		this.partner={}
		this.search={}
		this.delsearch=0
	}
	run(){

	}
	ctAddPartner(	a						,callback=null		,form_name		,dialog_id=null		,display_id,
							partner_list_id	,get_type="new"	,page=1			,oshid=null			,ipshid=null,
							lid=0					,fl="name"				,tx=""				,ofc=1){
		let did=event.target					
		if(this.partner[display_id]==undefined){
			this.partner[display_id]={}
			this.search[display_id]={}
		}else{
			if(this.partner_old[display_id]==undefined){
				this.partner_old[display_id]={}
			}else{
				//this.partner[display_id]=Object.assign({}, this.partner_old[display_id]);
			}
		}
		//alert("99999999999999999"+partner_list_id)
		dialog_id=(dialog_id==null)?this.main.rid():dialog_id
		let partner_list=document.forms[form_name][partner_list_id].value
		
		let dt={"data":{"a":"form_selects","b":a,"callback":callback,"dialog_id":dialog_id,"display_id":display_id,
				"from_name":form_name,"partner_list":partner_list,"partner_list_id":partner_list_id,"get_type":get_type,"page":page,
				"oshid":oshid,"ipshid":ipshid,"lid":lid,"fl":fl,"tx":tx,"ofc":ofc,"for":""},"result":Fsl.getListPartnerResult,"error":Fsl.getListPartnerError}
		if(a=="product"){
			if((eval(callback)).partner!=null){
				dt.data.partner=eval(callback).partner
			}
		}
		this.main.setFec(dt)
	}
	setPartnerOld(display_id){
		this.partner_old[display_id]=Object.assign({}, this.partner[display_id]);
	}
	getListPartnerResult(re,form,bt){
		if(re["result"]){
			Fsl.ctSelectPartner(re,form,bt)
		}else{
			Fsl.getListPartnerError(re,form,bt)
		}
	}
	getListPartnerError(re,form,bt){
		alert("error 9999999999")
		//Gp.ctSelectProp(re,form,bt)
	}
	ctSelectPartner(re,form,bt){
		let a=form.get("b")
		let callback=form.get("callback")
		let title_bar={"partner":"เลือกคู่ค้า","payu":"เลือกรูปแบบการชำระ","product":"เลือกสินค้า","member":"เลือกสมาชิก"}
		let tsh_prop={"partner":{"name":"ชื่อ","brand_name":"ชื่อการค้า","sku":"รหัสภายใน"},
			"payu":{"name":"ชื่อ","sku":"รหัสภายใน"},
			"product":{"name":"ชื่อ","sku":"รหัสภายใน","barcode":"รหัสแท่ง"},
			"member":{"name":"ชื่อ","lastname":"นามสกุล","sku":"รหัสสมาชิก","tel":"เบอร์โทรศัพท์","idc":"หมายเลขบัตรประชาชน"}
		}
		if(a!=null&&!title_bar.hasOwnProperty(a)){
			return false
		}else{
			
		}

		let rid = form.get("dialog_id")
		let arr = re.data["get"]

		let display_id = form.get("display_id")
		let dialog_id = form.get("dialog_id")
		let partner_list_id = form.get("partner_list_id")
		let form_name = form.get("from_name")
		let get_type=form.get("get_type")
		let partner_list = form.get("partner_list")
		let ofc = form.get("ofc")
		let tsh=tsh_prop[a]
		let disabled={};
		if(this.main.id(partner_list_id).getAttribute("data-disabled")!=null){
			let dab=this.main.id(partner_list_id).getAttribute("data-disabled")
			disabled=F.arrayToObjectKey(F.valueListToArray(dab))
		}
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
			let dibtsearch="dlbtsearch"
			let its=this.main.ce("input",{"id":ipshid,"type":"text","onkeyup":"Fsl.isEnter(event,this,'"+dibtsearch+"')"})
			let itd=this.main.ce("div",{"id":"ipshidet","onclick":"Fsl.setValueDel('"+ipshid+"')"})
				this.main.end(itd,[this.main.cn("↩")])
			let ibs=this.main.ce("input",{"id":dibtsearch,"type":"button","value":"🔍","onclick":"Fsl.selectPartnerSearch('"+a+"','"+callback+"','"+oshid+"','"+ipshid+"','"+form_name+"','"+dialog_id+"','"+display_id+"','"+partner_list_id+"')"})		
		this.main.end(cpn,[pn_sh,its,ibs,itd])
		let ct=this.main.ce("div",{})
			let ct0 = this.main.ce("div",{"id":"ct0_partner_"+display_id})
			let fron = this.main.ce("form",{"name":rid,"style":"width:100%;text-align:center;"})
			let d1 = this.main.ce("div",{"class":"selects_list_partner"})

			let lid=form.get("lid")
				
			let partner_has = F.valueListToArray(partner_list)
										
				for(let i=0;i<arr.length;i++){
					let ckrid = "checkboxid_"+arr[i]["sku_root"]
					let diab=0
					M.l(disabled)
					if(disabled.hasOwnProperty(arr[i]["sku_root"])){
						 diab=1
					}
					
					let div1=this.main.ce("div",{"class":"i"+((i%2)+1)})
						let ck = this.main.ce("input",{"type":"checkbox","id":ckrid,"name":"checkbox_"+rid,"data-icon":arr[i]["icon"],"data-name":arr[i]["name"],"value":arr[i]["sku_root"],"onchange":"Fsl.selectCkPartner(this,'"+display_id+"')"})
				
						if(diab==1){
							ck.disabled = true
						}
						if(partner_has.includes(arr[i]["sku_root"]) || this.partner[display_id].hasOwnProperty(arr[i]["sku_root"]) ){
							ck.checked = true
						}	
						let div_img=this.main.ce("div",{"class":"img32"})
							let img=this.main.ce("img",{"src":"img/gallery/32x32_"+arr[i]["icon"],"alt":arr[i]["name"],"onerror":"this.src='img/pos/64x64_null.png'"})	
						this.main.end(div_img,[img])	
						let boc = this.main.ce("label",{"for":ckrid})		
							//let tn = this.main.cn(arr[i]["name"])
							let tn = this.setNameContent(form,arr[i])
							let tn1=this.main.cn(tn[0])
							let tna=tn[1]!=undefined?tn[1]:""
							let tn2=this.main.cn(tna)
						this.main.end(boc,[tn1])
						if(tna!=""){
							//let br=this.main.ce("span",{})
							this.main.end(boc,[tn2])
						}
						let s=null
						if(diab==0){
							s=this.main.ce("div",{"data-rid_close":rid,"onclick":"Fsl.select1Partner(this,'"+a+"','"+callback+"','"+display_id+"','"+partner_list_id+"',"+ofc+")","title":"เลือก 1 รายการนี้เท่านั้น"})
							this.main.end(s,[this.main.cn("⬆")])
						}else{
							s=this.main.ce("div",{})
							this.main.end(s,[this.main.cn("-")])
						}
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
			{"value":"⬅ เลือกเพิ่ม","style":"visibility:hidden","id":"bt_back_select_"+display_id,"onclick":"Fsl.backSelectPartner(this,'"+a+"','"+callback+"','"+display_id+"','"+partner_list_id+"')"},
			{"value":"ดูที่เลือก ("+count+")","rid_close":rid,"id":"bt_select_n_"+display_id,"onclick":"Fsl.viewSlectedPartner(this,'"+a+"','"+callback+"','"+display_id+"','"+partner_list_id+"')"}
		]
		
		let select_type=null
		if(this.main.id(display_id)!=null){
			select_type=this.main.id(display_id).getAttribute("data-select_type")
		}
		if(select_type=="one"){
			bts[1]["style"]="visibility:hidden"
		}		
		
		if(get_type=="new"){
			let dty={"rid":rid,"display":1,"pn":cpn,"ct":ct,"bts":bts,"title":title_bar[a],"width":"400","ofc":ofc}
			if(select_type=="one"){
				dty["bts0"]=1
			}
			M.dialog(dty)
		}else if(get_type=="update"){
			this.main.rmc_all(this.main.id("ct0_partner_"+display_id))
			this.main.end(this.main.id("ct0_partner_"+display_id),[fron,div_page])	
			//this.main.id("ct0_partner_"+display_id).appenChild()
		}
	}
	setNameContent(form,arr){
		let display_id = form.get("display_id")
		let t=[arr["name"]]
		let a=form.get("b")
		if(a=="member"){

			t[1]=" "+arr["name"]+" "+arr["lastname"]
			t[0]="["+arr["sku"]+"]"
			if(this.partner_full_data[display_id]==undefined){
				this.partner_full_data[display_id]={}
			}
			this.partner_full_data[display_id][arr["sku_root"]]=arr
		}
		return t
	}
	setValueDel(id){
		let o=M.id(id)
		o.value=o.value.substr(0,o.value.length-1)
	}
	isEnter(event,did,id){
		let key=event.code
		if(key=="NumpadEnter"||key=="Enter"){	
			this.main.id(id).click()
		}
	}
	selectPartnerSearch(a,callback,option_search_id,input_search_id,form_name,dialog_id,display_id,partner_list_id,page=1,lid=0){
		let fl=this.main.id(option_search_id).value
		let tx=this.main.id(input_search_id).value
		this.ctAddPartner(a,callback,form_name,dialog_id,display_id,partner_list_id,"update",page,option_search_id,input_search_id,(lid),fl,tx,)
	}
	ctPage(re,form,form_name,dialog_id,display_id,partner_list_id,lid=0){
		let a=form.get("b")
		let callback=form.get("callback")
		let per=re.data.page["per"]
		let page=re.data.page["page"]
		let count=re.data.count[0]["count"]*1
		
		let n_page=Math.ceil(count/per)
		let tx= form.get("tx")
		let ct=this.main.ce("div",{"class":"c"})
			if(tx.trim()==""){
				let sl=this.main.ce("select",{"onchange":"Fsl.partnerGoPage(this,'"+a+"','"+callback+"','"+form_name+"','"+dialog_id+"','"+display_id+"','"+partner_list_id+"')"})
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
					let nex_tx="Fsl.selectPartnerSearch('"+a+"','"+callback+"','"+oshid+"','"+ipshid+"','"+form_name+"','"+dialog_id+"','"+display_id+"','"+partner_list_id+"',"+(page-1)+","+lid_ref+")"
						let ahf=this.main.ce("a",{"onclick":nex_tx})
						this.main.end(ahf,[this.main.cn("⬅️ก่อนหน้า️")])
						this.main.end(ct,[ahf])
				}				
				
				this.main.end(sp,[this.main.cn("หน้า : "+page)])
				this.main.end(ct,[sp])
	
				if(page==1){
					if(count>per){	
						this.search[display_id][page]=0
						let nex_tx="Fsl.selectPartnerSearch('"+a+"','"+callback+"','"+oshid+"','"+ipshid+"','"+form_name+"','"+dialog_id+"','"+display_id+"','"+partner_list_id+"',"+(page+1)+","+lid+")"
						let ahf=this.main.ce("a",{"onclick":nex_tx})
						this.main.end(ahf,[this.main.cn("ถัดไป➡️")])
						this.main.end(ct,[ahf])
					}
				}	
			}
		return ct
	}
	partnerGoPage(did,a,callback,form_name,dialog_id,display_id,partner_list_id){
		this.ctAddPartner(a,callback,form_name,dialog_id,display_id,partner_list_id,"update",did.value)
	}
	selectCkPartner(did,display_id){
		let sku_root=did.value
		let name=did.getAttribute("data-name")
		let icon=did.getAttribute("data-icon")		
		if(did.checked){
			if(!this.partner[display_id].hasOwnProperty(sku_root)){
				this.partner[display_id][sku_root]={
					"icon":icon,
					"name":name,
					"value":0
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
	viewSlectedPartner(did,a,callback,display_id,partner_list_id){
		this.main.id("ct0_partner_"+display_id).style.display="none"
		this.main.id("cpn0_partner_"+display_id).style.display="none"
		this.main.id("ct1_partner_"+display_id).style.display="block"
		this.viewPartnerSlected(did,display_id)
		this.main.id("bt_back_select_"+display_id).style.visibility="visible"
		did.value="ยืนยันที่เลือก"
		did.setAttribute("onclick","Fsl.selectPartnerOK(this,'"+a+"','"+callback+"','"+display_id+"','"+partner_list_id+"')")
		did.parentNode.parentNode.click()
	}
	backSelectPartner(did,a,callback,display_id,partner_list_id){
		this.main.id("ct0_partner_"+display_id).style.display="block"
		this.main.id("cpn0_partner_"+display_id).style.display="grid"
		this.main.id("ct1_partner_"+display_id).style.display="none"
		did.style.visibility="hidden"
		let count=Object.keys(this.partner[display_id]).length
		this.main.id("bt_select_n_"+display_id).value="ดูที่เลือก ("+count+")"
		this.main.id("bt_select_n_"+display_id).setAttribute("onclick","Fsl.viewSlectedPartner(this,'"+a+"','"+callback+"','"+display_id+"','"+partner_list_id+"')")
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
	selectPartnerOK(did,a,callback,display_id,partner_list_id,ofc=1){
		this.setEmptyTable(display_id)
		let rid_close=did.getAttribute("data-rid_close")
		this.selectPartnerListValue(a,display_id,partner_list_id)
		
		if(a=="partner"){
			this.selectPartnerOKAppend(a,display_id)
		}else if(a=="payu"){
			this.selectPayuOKAppend(a,display_id)
		}else if(a=="member"){
			let select_type=this.main.id(display_id).getAttribute("data-select_type")
			if(select_type=="one"){
				if(callback!=""){
					eval(callback).selectPartnerOK(display_id,this.partner,this.partner_full_data)
				}else{
					this.selectPartnerOKAppendOne(a,display_id)
				}
			}else{
				
			}
		}else if(a=="product"){
			eval(callback).selectPartnerOK(display_id,partner_list_id)
		}
		
		this.partner_old[display_id]=Object.assign({}, this.partner[display_id]);
		M.dialogClose(rid_close,ofc)
	}
	selectPartnerOKAppend(a,display_id){
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
	selectPartnerOKAppendOne(a,display_id){
		let t=this.main.id(display_id)
		for (let prop in this.partner[display_id]) {
			let div=this.main.ce("div",{"class":"form_select_div_one_select"})
				let div_img=this.main.ce("div",{"class":"img32"})
					let img=this.main.ce("img",{"src":"img/gallery/32x32_"+this.partner[display_id][prop]["icon"],"alt":this.partner[display_id][prop]["name"],"onerror":"this.src='img/pos/64x64_null.png'"})	
				this.main.end(div_img,[img])	
				let span=this.main.ce("div",{})
				this.main.end(span,[this.main.cn(this.partner[display_id][prop]["name"])])
			this.main.end(div,[div_img,span])	
			this.main.end(t,[div])
		}
	}
	selectPayuOKAppend(a,display_id,callback){//alert(callback)
		let t=this.main.id(display_id)
		let i=-1
		for (let prop in this.partner[display_id]) {
			i=i+1
			let r=t.insertRow(i);
			let cell0=r.insertCell(0)
			let cell1=r.insertCell(1)
			let cell2=r.insertCell(2)
			let cell3=r.insertCell(3)
			let div_img=this.main.ce("div",{"class":"img32"})
				let img=this.main.ce("img",{"src":"img/gallery/32x32_"+this.partner[display_id][prop]["icon"],"alt":this.partner[display_id][prop]["name"],"onerror":"this.src='img/pos/64x64_null.png'"})	
			this.main.end(div_img,[img])	
			cell0.innerHTML=i+1+"."
			this.main.end(cell1,[div_img])
			cell2.innerHTML=this.partner[display_id][prop]["name"]
			let val=this.main.nb(this.partner[display_id][prop]["value"],2)
			if(this.partner[display_id][prop]["value"]*1==0){
				val=""
			}
			let ip=this.main.ce("input",{"name":"payu_"+prop,"type":"number","step":"0.01","value":val,"onchange":"Fsl.setValue(this,'"+display_id+"','"+prop+"')"})
			this.main.end(cell3,[ip])	
			if(callback!=""){
				eval(callback)
			}
		}		
	}
	panelNum(did,display_id,prop){
		document.body.style.overflow="hidden"
		let p=did.getBoundingClientRect()
		console.log(p)
		let rid=this.main.rid()
		let xy=this.main.getXY(did)
		let top=xy.top
		let ct=this.main.ce("div",{"id":rid,"class":"num_panel","style":"height:"+p.height+"px;width:"+p.width+"px;top:"+top+"px;left:"+p.left+"px;"})
			let pn=this.main.ce("div",{"style":"height:"+p.height+"px;width:"+p.width+"px;margin:0 auto;background-color:white;border:1px solid gray"})
		this.main.end(ct,[pn])
		document.body.appendChild(ct)
		
		setTimeout(Fsl.panelNumAn,10,rid,p.width,p.height,p.left,p.top,p.width,p.height,p.left,p.top)
	}
	panelNumAn(id,width,height,left,top,width1,height1,left1,top1){
		let w=window.innerWidth
		let h=window.innerHeight
		let d=M.id(id)
		let rw=0;
		let rh=0;
		if(Math.abs(width1-w)<1){
			d.style.width="100%"
			rw=1
		}
		if(Math.abs(height1-h)<1){
			d.style.height="100%"
			rh=1
		}
		let aw=1
		let ah=1
		if(width1<w&&rw==0){
			let q=w-width1
			if(q>128){
				aw=32
			}else if(q>64){
				aw=16
			}else if(q>32){
				aw=8
			}else if(q>16){
				aw=4
			}else if(q>8){
				aw=2
			}else if(q>4){
				aw=1
			}
			d.style.width=width1+"px"
			let y=width1-width
			let z=w-width
			let l=(y/z)*left
			d.style.left=(left-l)+"px"
			//setTimeout(Fsl.panelNumAn,10,id,width,height,left,top,width1+a,height1,left1,top1)
		}
		if(height1<h&&rh==0){
			let q=h-height1
			if(q>128){
				ah=32
			}else if(q>64){
				ah=16
			}else if(q>32){
				ah=8
			}else if(q>16){
				ah=4
			}else if(q>8){
				ah=2
			}else if(q>4){
				ah=1
			}
			d.style.height=height1+"px"
			let y=height1-height
			let z=h-height
			let t=(y/z)*top
			d.style.top=(top-t)+"px"
			
		}
		
		if(rw==0||rh==0){
			if(rw==1){
				aw=0
			}
			if(rh==1){
				ah=0
			}
			setTimeout(Fsl.panelNumAn,10,id,width,height,left,top,width1+aw,height1+ah,left1,top1)
		}
	}
	setValue(did,display_id,prop){
		if(this.partner[display_id][prop]["value"]!=undefined){
			this.partner[display_id][prop]["value"]=did.value
		}
	}
	setEmptyTable(display_id){
		if(this.main.id(display_id)!=undefined){
			let t=this.main.id(display_id)
			if(this.main.id(display_id).tagName=="TABLE"){
				let len=t.rows.length
				for(let i=len-2;i>=0;i--){
					t.deleteRow(i)
				}
			}else{
				this.main.rmc_all(t)
			}
		}
	}
	selectPartnerListValue(a,display_id,partner_list_id){
		let v=this.main.id(partner_list_id)
		v.value=""
		for (let prop in this.partner[display_id]) {
			v.value+=","+prop+","
		}
	}
	select1Partner(did,a,callback,display_id,partner_list_id,ofc=1){
		let d=did.parentNode.childNodes[0]
		let sku_root=d.value
		let name=d.getAttribute("data-name")
		let icon=d.getAttribute("data-icon")		
		this.partner[display_id]={}
		this.partner[display_id][sku_root]={
					"icon":icon,
					"name":name,
					"value":0
				}
		let count=Object.keys(this.partner[display_id]).length
		this.partner_old[display_id]=Object.assign({}, this.partner[display_id]);
		this.main.id("bt_select_n_"+display_id).value="ดูที่เลือก ("+count+")"
		this.selectPartnerOK(did,a,callback,display_id,partner_list_id,ofc)
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
	setLoadPartner(a,form_name,dialog_id=null,display_id,partner_list_id,ob_value,callback=""){
		let a_get={"partner":"partner_get","payu":"payu_get"};
		if(a!=null&&!a_get.hasOwnProperty(a)){
			return false
		}else{
			
		}
		if(this.partner[display_id]==undefined){
			this.partner[display_id]={}
			this.search[display_id]={}
		}
		dialog_id=(dialog_id==null)?this.main.rid():dialog_id
		let partner_list=document.forms[form_name][partner_list_id].value
		let dt={"data":{"a":"form_selects","b":a,"c":a_get[a],"dialog_id":dialog_id,"display_id":display_id,
			"from_name":form_name,"partner_list":partner_list,"partner_list_id":partner_list_id,"js_value":JSON.stringify(ob_value),"callback":callback},
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
		//alert(55555);
	}
	loadSetPartner(re,form,bt){
		let a=form.get("b")
		let callback=form.get("callback")
		let partner_list=form.get("partner_list")
		
		let ob_value=JSON.parse(form.get("js_value"))
		
		let display_id = form.get("display_id")
		let dt=re["data"]
		let st=F.valueListToArray(partner_list)
		
		for (let i=0;i<dt.length;i++) {
			let value=ob_value[dt[i]["sku_root"]]!=undefined?ob_value[dt[i]["sku_root"]]:0
			this.partner[display_id][dt[i]["sku_root"]]={
				"icon":dt[i]["icon"],
				"name":dt[i]["name"],
				"value":value
			}
		}	
		
		this.setPartnerOld(display_id)
		
		if(a=="partner"){
			this.selectPartnerOKAppend(a,display_id)	
		}else if(a=="payu"){
			this.selectPayuOKAppend(a,display_id,callback)
		}
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

}
