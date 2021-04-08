"use strict"
class gallery{
	constructor(main){
		this.main=main
		this.gallery_gl={}
		this.gallery_old={}
		this.gallery={}
		this.search={}
	}
	run(){

	}
	ctAddGallery(table,key,data_key,form_name,dialog_id=null,display_id,gallery_list_id,gallery_gl_list_id,get_type="new",icon_ob=null){
		let a="gallery"
		if(this.gallery[display_id]==undefined){
			this.gallery[display_id]={}
		}else{
			if(this.gallery_old[display_id]==undefined){
				this.gallery_old[display_id]={}
			}else{
				this.gallery[display_id]=Object.assign({}, this.gallery_old[display_id]);
			}
		}
		dialog_id=(dialog_id==null)?this.main.rid():dialog_id
		let gallery_list=document.forms[form_name][gallery_list_id].value
		let gallery_gl_list=document.forms[form_name][gallery_gl_list_id].value
		let dt={"data":{"a":a,"table":table,"key":key,"dialog_id":dialog_id,"display_id":display_id,"get_type":get_type,
				"from_name":form_name,"gallery_list":gallery_list,"gallery_gl_list":gallery_gl_list,"key_data":data_key,
				"gallery_list_id":gallery_list_id,"gallery_gl_list_id":gallery_gl_list_id,"icon_ob":icon_ob},
				"result":Gl.getListGalleryResult,"error":Gl.getListGalleryError}		
		this.main.setFec(dt)
	}
	setGalleryOld(display_id){
		this.gallery_old[display_id]=Object.assign({}, this.gallery[display_id]);
	}
	getListGalleryResult(re,form,bt){
		/*
		if(re["result"]){
			Fsl.ctSelectPartner(re,form,bt)
		}else{
			Fsl.getListPartnerError(re,form,bt)
		}*/
	}
	getListGalleryError(re,form,bt){
		Gl.ctSelectGallery(re,form,bt)
	}
	ctSelectGallery(re,form,bt){
		let a=form.get("a")
		let callback=form.get("callback")
		let title_bar="เลือกรูปภาพ"

		let table=form.get("table")
		let key=form.get("key")
		let key_data=form.get("key_data")
		let icon_ob=form.get("icon_ob")

		let rid = form.get("dialog_id")
		
		let arr =F.valueListToArray(this.main.id(form.get("gallery_gl_list_id")).value)
		//let arr = re.data["get"]
		let get_type=form.get("get_type")
		let display_id = form.get("display_id")
		let dialog_id = form.get("dialog_id")
		let gallery_list_id = form.get("gallery_list_id")
		let gallery_gl_list_id = form.get("gallery_gl_list_id")
		let form_name = form.get("from_name")

		let gallery_list = form.get("gallery_list")

		let ct=this.main.ce("div",{})
			let ct0 = this.main.ce("div",{"id":"ct0_gallery_"+display_id})
			let fron = this.main.ce("form",{"name":rid,"style":"width:100%;text-align:center;"})
			let d1 = this.main.ce("div",{"id":"select_list_gallery","class":"selects_list_gallery"})

					
			let gallery_has = F.valueListToArray(gallery_list)
										
				for(let i=0;i<arr.length;i++){
					let ckrid = "checkboxid_"+arr[i]
					let div1=this.main.ce("div",{"id":"select_"+arr[i],"class":"i"+((i%2)+1)})
						let ck = this.main.ce("input",{"type":"checkbox","id":ckrid,"name":"checkbox_"+rid,"data-icon":arr[i],"value":arr[i],"onchange":"Gl.selectCkGallery(this,'"+display_id+"')"})
				
						if(gallery_has.includes(arr[i]) || this.gallery[display_id].hasOwnProperty(arr[i]) ){
							ck.checked = true
						}	
						let div_img=this.main.ce("div",{"class":"img96"})
							let img=this.main.ce("img",{"class":"viewimage","src":"img/gallery/128x128_"+arr[i],"alt":"","onerror":"this.src='img/pos/64x64_null.png'","onclick":"G.view(this,0)"})	
						this.main.end(div_img,[img])	
						let boc = this.main.ce("label",{"for":ckrid})		
							let tn = this.main.cn("")
						this.main.end(boc,[tn])
						let s=this.main.ce("div",{"data-rid_close":rid,"onclick":"Gl.select1Gallery(this,'"+a+"','"+display_id+"','"+gallery_list_id+"')"})
						this.main.end(s,[this.main.cn("⬆")])
					this.main.end(div1,[ck,div_img,boc,s])
					this.main.end(d1,[div1])
				}
			this.main.end(fron,[d1])						

			
			this.main.end(ct0,[fron])	
			let ct1 = this.main.ce("div",{"id":"ct1_gallery_"+display_id})
		this.main.end(ct,[ct0,ct1])	

		let count=Object.keys(this.gallery[display_id]).length
		let bts = [
			{"value":"➕เพิ่มiรูป","style":"display:inline-block","id":"bt_add_select_"+display_id,"onclick":"Gl.addImgGallery(this,'"+a+"','"+display_id+"','"+dialog_id+"','"+gallery_list_id+"','"+gallery_gl_list_id+"','"+table+"','"+key+"','"+key_data+"','"+icon_ob+"')"},
			{"value":"⬅ เลือกเพิ่ม","style":"display:none","id":"bt_back_select_"+display_id,"onclick":"Gl.backSelectGallery(this,'"+a+"','"+display_id+"','"+gallery_list_id+"','"+gallery_gl_list_id+"')"},
			{"value":"🗑️ลบ","rid_close":rid,"style":"display:none","id":"bt_delete_"+display_id,"onclick":"Gl.deleteSlectedGallery('"+a+"','"+display_id+"','"+dialog_id+"','"+gallery_list_id+"','"+gallery_gl_list_id+"','"+table+"','"+key+"','"+key_data+"','"+icon_ob+"')"},
			{"value":"ดูที่เลือก ("+count+")","rid_close":rid,"id":"bt_select_n_"+display_id,"onclick":"Gl.viewSlectedGallery(this,'"+a+"','"+display_id+"','"+gallery_list_id+"','"+gallery_gl_list_id+"')"}
		]
		if(get_type=="new"){
			M.dialog({"rid":rid,"display":1,"ct":ct,"bts":bts,"title":title_bar,"width":"355"})
		}else if(get_type=="update"){
			this.main.rmc_all(this.main.id("ct0_gallery_"+display_id))
			this.main.end(this.main.id("ct0_gallery_"+display_id),[fron,div_page])	
			//this.main.id("ct0_partner_"+display_id).appenChild()
		}
	}
	deleteSlectedGallery(a,display_id,dialog_id,gallery_list_id,gallery_gl_list_id,table,key,key_data,icon_ob){
		let count=Object.keys(this.gallery[display_id]).length
		let rid=this.main.rid()
		let dt={"rid":rid,"title":"คุณต้องการ?","msg":"คุณต้องการลบรูป "+count+" รายการ  ที่ได้เลือกไว้ อย่างถาวร","ofc":0,
			"callback":"Gl.deleteSlectedGalleryOK(\'"+rid+"\',\'"+a+"\',\'"+display_id+"\',\'"+dialog_id+"\',\'"+gallery_list_id+"\',\'"+gallery_gl_list_id+"\',\'"+table+"\',\'"+key+"\',\'"+key_data+"\',\'"+icon_ob+"\')"
		}
		this.main.dialogConfirm(dt)
	}
	deleteSlectedGalleryOK(acp_id,a,display_id,dialog_id,gallery_list_id,gallery_gl_list_id,table,key,key_data,icon_ob){
		
		this.main.dialogClose(acp_id,0)
		let dt={
			"table":table			,"display_id":display_id		,"key":key		,"key_data":key_data,
			"obj_str":icon_ob	,"gl_obj_str":"Gl.gallery['"+display_id+"']",
			"callbackresult":'Gl.deleteImgGalleryResult(form,icon_name,\''+display_id+'\',\''+gallery_list_id+'\',\''+gallery_gl_list_id+'\')',
			"calbackerror":'Gl.deleteImgGalleryError(form,icon_name,\'"+display_id+"\',\'"+dialog_id+"\\\',\'"+rid+"\')'
		}
		Ful.fileDeleteImgs(dt)
	}
	deleteImgGalleryResult(form,icon_name,display_id,gallery_list_id,gallery_gl_list_id){
		//alert("icon_name="+icon_name+";display_id="+display_id+";gallery_list_id="+gallery_list_id+";gallery_gl_list_id="+gallery_gl_list_id);
		//alert(8888)
		let d=this.main.id("checkboxid_"+icon_name)
		//alert(777)
		////////////////////////////////////////--1
		if(d!=undefined){
			d.parentNode.parentNode.removeChild(d.parentNode)
		}
		////////////////////////////////////////--2
		let e=this.main.id("selected_"+icon_name)
		if(e!=undefined){
			e.parentNode.removeChild(e)
		}
		////////////////////////////////////////--3
		let gl_val=this.main.id(gallery_gl_list_id)
		let gl=F.valueListToArray(gl_val.value)
		let val=""
		for(let i=0;i<gl.length;i++){
			if(gl[i]!=icon_name){
				val+=","+gl[i]+","
			}
		}
		gl_val.value=val
		////////////////////////////////////////--4
		if(Gl.gallery[display_id]!=undefined){
			if(Gl.gallery[display_id][icon_name]!=undefined){
				delete Gl.gallery[display_id][icon_name]
			}
			if(Gl.gallery_old[display_id][icon_name]!=undefined){
				delete Gl.gallery_old[display_id][icon_name]
			}
		}
		////////////////////////////////////////--5
		let p=this.main.id("select_"+icon_name)
		if(p!=undefined){
			p.parentNode.removeChild(p)
		}
		////////////////////////////////////////--6
		let c=this.main.id("div_fileset_div_"+icon_name)
		if(c!=undefined){
			c.parentNode.removeChild(c)
		}
	}
	deleteImgGalleryError(re,form,bt){
		alert("deleteImgGalleryError")
	}
	xxxxselectPartnerSearch(a,callback,option_search_id,input_search_id,form_name,dialog_id,display_id,partner_list_id,page=1,lid=0){
		let fl=this.main.id(option_search_id).value
		let tx=this.main.id(input_search_id).value
		this.ctAddPartner(a,callback,form_name,dialog_id,display_id,partner_list_id,"update",page,option_search_id,input_search_id,(lid),fl,tx,)
	}
	xxxxctPage(re,form,form_name,dialog_id,display_id,partner_list_id,lid=0){
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
	xxxxpartnerGoPage(did,a,callback,form_name,dialog_id,display_id,partner_list_id){
		this.ctAddPartner(a,callback,form_name,dialog_id,display_id,partner_list_id,"update",did.value)
	}
	selectCkGallery(did,display_id){
		let sku_root=did.value
		let name=did.getAttribute("data-name")
		let icon=did.getAttribute("data-icon")		
		if(did.checked){
			if(!this.gallery[display_id].hasOwnProperty(sku_root)){
				this.gallery[display_id][sku_root]={
					"icon":icon,
					"name":name
				}
			}
		}else{
			if(this.gallery[display_id].hasOwnProperty(sku_root)){
				delete this.gallery[display_id][sku_root]
			}
		}
		let count=Object.keys(this.gallery[display_id]).length
		this.main.id("bt_select_n_"+display_id).value="ดูที่เลือก ("+count+")"
	}
	viewSlectedGallery(did,a,display_id,gallery_list_id,gallery_gl_list_id){
		this.main.id("ct0_gallery_"+display_id).style.display="none"
		this.main.id("ct1_gallery_"+display_id).style.display="block"
		this.viewGallerySlected(did,display_id)
		this.main.id("bt_back_select_"+display_id).style.display="inline-block"
		this.main.id("bt_delete_"+display_id).style.display="inline-block"
		this.main.id("bt_add_select_"+display_id).style.display="none"
		did.value="ยืนยันที่เลือก"
		did.setAttribute("onclick","Gl.selectGalleryOK(this,'"+a+"','"+display_id+"','"+gallery_list_id+"')")
		did.parentNode.parentNode.click()
	}
	backSelectGallery(did,a,display_id,gallery_list_id){
		this.main.id("ct0_gallery_"+display_id).style.display="block"
		this.main.id("ct1_gallery_"+display_id).style.display="none"
		did.style.display="none"
		this.main.id("bt_add_select_"+display_id).style.display="inline-block"
		this.main.id("bt_delete_"+display_id).style.display="none"
		let count=Object.keys(this.gallery[display_id]).length
		this.main.id("bt_select_n_"+display_id).value="ดูที่เลือก ("+count+")"
		this.main.id("bt_select_n_"+display_id).setAttribute("onclick","Gl.viewSlectedGallery(this,'"+a+"','"+display_id+"','"+gallery_list_id+"')")
	}
	viewGallerySlected(did,display_id){
		let ct=this.main.id("ct1_gallery_"+display_id)
		this.main.rmc_all(ct)
		let i=-1
		for (let prop in this.gallery[display_id]) {
			i=i+1
			let d1 = this.main.ce("div",{"class":"selected_list_gallery","id":"selected_"+prop})
				let d2 = this.main.ce("div",{"data-sku_root":prop,"id":"select_at_"+i,"class":"i"+((i%2)+1)})
					let div_at=this.main.ce("div",{})
					this.main.end(div_at,[this.main.cn(i+1)])	
					let div_img=this.main.ce("div",{"class":"img96"})
						let img=this.main.ce("img",{"class":"viewimage","src":"img/gallery/128x128_"+this.gallery[display_id][prop]["icon"],"alt":"","onerror":"this.src='img/pos/64x64_null.png'","onclick":"G.view(this,0)"})	
					this.main.end(div_img,[img])	
					let div_name=this.main.ce("div",{})
					this.main.end(div_name,[this.main.cn("")])	
					let div_move=this.main.ce("div",{"onclick":"Gl.selectGalleryMove(this,'"+display_id+"',"+i+")"})
					this.main.end(div_move,[this.main.cn("⇅")])
					let div_del=this.main.ce("div",{"onclick":"Gl.deleteGallery(this,'"+display_id+"','"+prop+"')","title":"นำออก ไม่เลือก"})
					this.main.end(div_del,[this.main.cn("×")])	
				this.main.end(d2,[div_at,div_img,div_name,div_move,div_del])	
			this.main.end(d1,[d2])	
			this.main.end(ct,[d1])	
		}
	}
	selectGalleryMove(did,display_id,index){
		let a=this.main.id("ct1_gallery_"+display_id)
		for(let i=0;i<a.childNodes.length;i++){
			let b=a.childNodes[i].childNodes[0].childNodes[3]
			if(i!=index){
				b.innerHTML="🚩"
				b.style.backgroundColor="LightGreen"
				b.setAttribute("onclick","Gl.selectGalleryMoveSet(this,'"+display_id+"',"+index+","+i+")")
				b.onmouseover=()=>{}
				b.onmouseout=()=>{}
			}
		}
	}
	selectGalleryMoveSet(did,display_id,index_from,index_to){
		let no={}//Object.assign({}, this.partner[display_id])
		
		let a=this.main.id("ct1_gallery_"+display_id)
		let newnode=a.childNodes[index_from].cloneNode(true);
		a.insertBefore(newnode, a.childNodes[index_to])
		if(index_to<index_from){
			a.removeChild(a.childNodes[index_from+1])
		}else{
			a.removeChild(a.childNodes[index_from])
		}		
		for(let i=0;i<a.childNodes.length;i++){
			let k=a.childNodes[i].childNodes[0].getAttribute("data-sku_root")
			no[k]=this.gallery[display_id][k]
			let b=a.childNodes[i].childNodes[0].childNodes[3]
			a.childNodes[i].childNodes[0].childNodes[0].innerHTML=i+1
			b.innerHTML="⇅"
			b.style.backgroundColor="gray"
			b.setAttribute("onclick","Gl.selectGalleryMove(this,'"+display_id+"',"+i+")")
			b.onmouseover=()=>{b.style.backgroundColor="orange"}
			b.onmouseout=()=>{b.style.backgroundColor="gray"}
		}
		this.gallery[display_id]=no
	}
	selectGalleryOK(did,a,display_id,gallery_list_id){
		this.setEmptyTable(display_id)
		let rid_close=did.getAttribute("data-rid_close")
		this.selectGalleryListValue(a,display_id,gallery_list_id)
		this.selectGalleryOKAppend(display_id)
		this.gallery_old[display_id]=Object.assign({}, this.gallery[display_id]);
		M.dialogClose(rid_close)
	}
	selectGalleryOKAppend(display_id){
		let d=this.main.id(display_id)
		for (let prop in this.gallery[display_id]) {
			let div_img=this.main.ce("div",{"id":"div_fileset_div_"+this.gallery[display_id][prop]["icon"]})
				let img=this.main.ce("img",{"class":"viewimage","src":"img/gallery/128x128_"+this.gallery[display_id][prop]["icon"],"alt":this.gallery[display_id][prop]["name"],"onerror":"this.src='img/pos/64x64_null.png'","onclick":"G.view(this)" })	
			this.main.end(div_img,[img])	
			this.main.end(d,[div_img])	
		}
		/*let t=this.main.id(display_id)
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
		}*/	
	}
	xxxxselectPayuOKAppend(a,display_id){
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
			let ip=this.main.ce("input",{"name":"payu_"+prop,"type":"text","value":val,"onchange":"Fsl.setValue(this,'"+display_id+"','"+prop+"')"})
			this.main.end(cell3,[ip])	
		}		
	}
	xxxxpanelNum(did,display_id,prop){
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
	xxxxpanelNumAn(id,width,height,left,top,width1,height1,left1,top1){
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
	xxxxsetValue(did,display_id,prop){
		if(this.partner[display_id][prop]["value"]!=undefined){
			this.partner[display_id][prop]["value"]=did.value
		}
	}
	setEmptyTable(display_id){
		if(this.main.id(display_id)!=undefined){
			let t=this.main.id(display_id)
			this.main.rmc_all(t)
		}
	}
	selectGalleryListValue(a,display_id,gallery_list_id){
		let v=this.main.id(gallery_list_id)
		v.value=""
		for (let prop in this.gallery[display_id]) {
			v.value+=","+prop+","
		}
	}
	select1Gallery(did,a,display_id,gallery_list_id){
		let d=did.parentNode.childNodes[0]
		let sku_root=d.value
		let name=d.getAttribute("data-name")
		let icon=d.getAttribute("data-icon")		
		this.gallery[display_id]={}
		this.gallery[display_id][sku_root]={
					"icon":icon,
					"name":name
				}
		let count=Object.keys(this.gallery[display_id]).length
		this.gallery_old[display_id]=Object.assign({}, this.gallery[display_id]);
		this.main.id("bt_select_n_"+display_id).value="ดูที่เลือก ("+count+")"
		this.selectGalleryOK(did,a,display_id,gallery_list_id)
	}
	deleteGallery(did,display_id,sku_root){
		did.parentNode.parentNode.removeChild(did.parentNode)
		if(this.gallery[display_id][sku_root]!=undefined){
			delete this.gallery[display_id][sku_root]
		}
		if(this.main.id("checkboxid_"+sku_root)!=undefined){
			this.main.id("checkboxid_"+sku_root).checked=false
		}
	}
	setLoadGallery(table,key,data_key,form_name,dialog_id=null,display_id,gallery_list_id,gallery_gl_list_id){
		let a="gallery"
		let a_get={"gallery":"gallery_get"}
		
		if(a!=null&&!a_get.hasOwnProperty(a)){
			return false
		}else{
			
		}
		if(this.gallery[display_id]==undefined){
			this.gallery[display_id]={}
		}
		dialog_id=(dialog_id==null)?this.main.rid():dialog_id
		let gallery_list=document.forms[form_name][gallery_list_id].value
		let gallery_gl_list=document.forms[form_name][gallery_gl_list_id].value
		let dt={"data":{"a":a,"table":table,"key":key,"data_key":data_key,"dialog_id":dialog_id,"display_id":display_id,
			"from_name":form_name,"gallery_list":gallery_list,"gallery_gl_list":gallery_gl_list,
			"gallery_list_id":gallery_list_id,"gallery_gl_list_id":gallery_gl_list_id},
			"result":Gl.getListGalleryLoadResult,"error":Gl.getListGalleryLoadError
		}
		this.main.setFec(dt)
	}
	getListGalleryLoadResult(re,form,bt){
		/*if(re["result"]){
			Gl.loadSetGallery(re,form,bt)
		}else{
			Gl.getListGalleryLoadError(re,form,bt)
		}*/
	}
	getListGalleryLoadError(re,form,bt){
		Gl.loadSetGallery(re,form,bt)
	}
	loadSetGallery(re,form,bt){//alert(this.main.id(form.get("gallery_gl_list_id")).value)
		let display_id = form.get("display_id")
		let dt=F.valueListToArray(this.main.id(form.get("gallery_list_id")).value)
		let dt_gl=F.valueListToArray(this.main.id(form.get("gallery_gl_list_id")).value)
		for (let i=0;i<dt.length;i++) {
			this.gallery[display_id][dt[i]]={
				"icon":dt[i],
				"name":null
			}
		}	
		this.setGalleryOld(display_id)
		this.selectGalleryOKAppend(display_id)	
	}
	addImgGallery(did,a,display_id,dialog_id,gallery_list_id,gallery_gl_list_id,table,key,key_data,icon_ob){
		let ct=Ful.ctFileUploadsDialog(icon_ob)
		let rid=this.main.rid()
		let title_bar="เพิ่มรูปรูปภาพ"
		let bts = [
			{"value":"+ไฟล์รูปภาพ","onclick":"document.getElementById('upload_pic').click()"},
			{"value":"ยืนยัน","onclick":"Ful.fileUploadImgs('add','"+table+"','"+key+"','"+key_data+"','"+icon_ob+"','','','Gl.addImgGalleryResult(form,icon_name,\\\'"+display_id+"\\\',\\\'"+dialog_id+"\\\',\\\'"+rid+"\\\',\\\'"+gallery_list_id+"\\\',\\\'"+gallery_gl_list_id+"\\\')','Gl.addImgGalleryError(form,\\\'"+display_id+"\\\',\\\'"+dialog_id+"\\\',\\\'"+gallery_list_id+"\\\',\\\'"+gallery_gl_list_id+"\\\')')"
			}
		]
		M.dialog({"rid":rid,"display":1,"ct":ct,"bts":bts,"title":title_bar,"width":"250","ofc":0})
	}
	addImgGalleryResult(form,icon_name="",display_id,dialog_id,rid,gallery_list_id,gallery_gl_list_id){	//re,form,bt,display_id,dialog_id,rid
		//let ct=this.main.ce("input",{"value":icon_name})
		//this.main.id("ct1_gallery_"+display_id).appendChild(ct)
		//let obj_str = form.get("obj_str")
		//alert(obj_str)
		//////////////////-1
		let d1=this.main.id("select_list_gallery")
			let ckrid = "checkboxid_"+icon_name
			let div1=this.main.ce("div",{"id":"select_"+icon_name,"class":"i_new"})
				let ck = this.main.ce("input",{"type":"checkbox","id":ckrid,"name":"checkbox_"+rid,"data-icon":icon_name,"value":icon_name,"onchange":"Gl.selectCkGallery(this,'"+display_id+"')"})
				let div_img=this.main.ce("div",{"class":"img96"})
					let img=this.main.ce("img",{"class":"viewimage","src":"img/gallery/128x128_"+icon_name,"alt":"","onerror":"this.src='img/pos/64x64_null.png'","onclick":"G.view(this,0)"})	
				this.main.end(div_img,[img])	
				let boc = this.main.ce("label",{"for":ckrid})		
					let tn = this.main.cn("")
				this.main.end(boc,[tn])
				let s=this.main.ce("div",{"data-rid_close":dialog_id,"onclick":"Gl.select1Gallery(this,'gallery','"+display_id+"','"+gallery_list_id+"')"})
				this.main.end(s,[this.main.cn("⬆")])
			this.main.end(div1,[ck,div_img,boc,s])
		this.main.end(d1,[div1])
			let obs=d1.parentNode.parentNode.parentNode
			obs.scrollTo({"top": obs.scrollHeight,"behavior": "smooth" })		
		////////////////////////////////////////--2
		let gl_val=this.main.id(gallery_gl_list_id)
		let gl=F.valueListToArray(gl_val.value)
		gl.push(icon_name); 
		let val=""
		for(let i=0;i<gl.length;i++){
				val+=","+gl[i]+","
		}
		gl_val.value=val
		Ful.dialogClose(rid,0)
	}
	addImgGalleryError(form,display_id,dialog_id,rid){//alert(dialog_id)
		Ful.dialogClose(rid,0)
		//Ful.dialogClose(dialog_id,1)
	}
}
