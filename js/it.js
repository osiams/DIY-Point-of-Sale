"use strict"
class it extends main{
	constructor(){
		super()
		this.itm={"billinlistid":null,"skuroot":null,"skuroot_has":0,"skuroot1":null,"skuroot2":null,"skuroot_n":1,"skuroot1_n":0,"skuroot2_n":0,"skuroot1_name":null,"skuroot2_mame":null,"confirm":0}
		this.itmmm=null
	}
	edit(sku){
		let f=document.forms.it
		f.action="?a=it&b=edit"
		f.sku_root.value=sku
		f.submit()
	}
	delPd(billinsku,itroot,itname,pdroot,pdname,n,unitname){
		let tx=`คุณต้องการลบ "${pdname}"
        ทั้งหมดจำนวน ${n} ${unitname}
ออกจากคลัง "${itname}"
        โปรดเลือกเหตุผลด้วย
        🗑เสีย,ชำรุด\tพิมพ์ 1
        🗑หมดอาย\t\tพิมพ์ 2
        🗑อื่นๆ\t\tพิมพ์ 8 <ใส่เหตุผล>`
		let y=prompt(tx)
		if(y!==null){
			let dt={"data":{"a":"it","c":"lot","delete":1,"billinsku":billinsku,"y":y},"result":It.delPdResult,"error":It.delPdError}
			this.setFec(dt)
		}
	}
	delPdResult(re,form,bt){
		if(re["result"]){
			alert("✔️ สำเร็จ")
			location.reload();
		}else{
			It.sortError(re,form,bt)
		}
	}
	delPdError(re,form,bt){
		alert("❌ เกิดข้อผิดพลาด "+re["message_error"])
	}
	delete(sku_root,name,default_sku){
		let y=confirm("คุณต้องการลบคลัง\n     "+name+" \nถ้าในคลังมิสินค้าอยู่สินค้าทุกอันจะไปอยู่ที่ \n    คลังรหัส "+default_sku+" ")
		if(y){
			let dt={"data":{"a":"it","b":"delete","sku_root":sku_root},"result":It.delResult,"error":It.delError}
			this.setFec(dt)
		}
	}
	delResult(re,form,bt){
		if(re["result"]){
			alert("✔️ สำเร็จ")
			location.reload();
		}else{
			It.delError(re,form,bt)
		}
	}
	delError(re,form,bt){
		alert("❌ เกิดข้อผิดพลาด "+re["message_error"])
	}
	sort(dis,actionid,t_id,at,error=""){
		let dbt_id=[]
		let id_sort=[]
		let did=this.id(actionid)
		let tb=did.parentNode.parentNode.parentNode.parentNode
		let s=0
		for(let i=1;i<tb.rows.length;i++){
			dbt_id[s++]=Number(tb.rows[i].cells[0].getAttribute("data-id"))
		}
		M.l(dbt_id)
		let note=did.parentNode.parentNode.cells[1].innerHTML
		note=note.replace(/<a.{1,}">|<\/a>/g, "")
		let nb=prompt("จัดลำดับงวดตัดสินค้า\n\n"+note+"\n------------------------------------\nใส่ลำดับงวด ที่ต้องการสำหรับสินค้างวดนี้\nขณะนี้อยู่ลำดับที่ "+at+"\n"+error)
		let q=0
		let id_select=did.parentNode.parentNode.cells[0].getAttribute("data-id")
		if(typeof nb=="string"){
			if(/^[0-9]{1,}$/.test(nb)){
				nb=Number(nb)
				if(nb>=1&&nb<=dbt_id.length){
					error=""
					for(let i=0;i<dbt_id.length;i++){
						if(i<nb-1&&nb<=at){
							if(i<at-1){
								id_sort[i]=dbt_id[i]
							}
						}else if(nb-1==i){
							id_sort[i]=t_id*1
							for(let k=0;k<at;k++){
								if(k>nb-1&&k<at&&nb<=at){
									id_sort[k]=dbt_id[k-1]
								}
							}
							for(let k=at;k<nb;k++){
								 if(k<nb&&k>at-1&&nb>at){
									id_sort[k-1]=dbt_id[k]
								}
							}
						}else if(i>nb-1&&nb<=at){
							for(let k=at;k<dbt_id.length;k++){
									id_sort[k]=dbt_id[k]
							}
						}else if(i<nb-1&&nb>at){
							if(i<at-1){
								id_sort[i]=dbt_id[i]
							}
						}else if(i>nb-1&&nb>at){
							for(let k=nb;k<dbt_id.length;k++){
									id_sort[k]=dbt_id[k]
							}
						}
					}
				}else{
					error="❌ โปรดกรอก เลข 1 ถึง "+(dbt_id.length)
				}
			}else{
				error="❌ โปรดกรอกตัวเลขเป็นจำนวนนับ"
			}
			if(error!=""){
				this.sort(did,t_id,at,error)
			}else{
				let sort={}
				let sq=dbt_id.sort(function(a, b){return a-b});
				for(let i=0;i<id_sort.length;i++){
					sort[id_sort[i].toString()]=sq[i]
				}
				M.l(sq)
				M.l(sort)
				let dt={"data":{},"result":It.sortResult,"error":It.sortError}
				let form=document.forms["it_view_lot"]
				for(let i=0;i<form.length;i++){
					dt.data[form[i].name]=form[i].value
				}
				dt.data["sort"]=JSON.stringify(sort)
				this.setFec(dt)
			}
		}else{
			alert("คุณยกเลิก")
		}
	}
	sortResult(re,form,bt){
		if(re["result"]){
			alert("✔️ สำเร็จ")
			location.reload();
		}else{
			It.sortError(re,form,bt)
		}
	}
	sortError(re,form,bt){
		alert("❌ เกิดข้อผิดพลาด "+re["message_error"])
	}
	move(dis,actionid,billinid,n,st){
		let dt={"data":{"a":"it","b":"getitformove","dis":dis,"actionid":actionid,"billinid":billinid,"n":n,"st":st},"result":It.getItResult,"error":It.getItError}
		this.setFec(dt)
	}
	getItResult(re,form,bt){
		if(re["result"]){
			It.move1(form,re["data"])
		}else{
			It.getItError(re,form,bt)
		}
	}
	getItError(re,form,bt){
		let rid = M.rid()
		let msg=re["message_error"]
		let dt={"msg":msg,"rid":rid,"callback":"M.dialogClose('"+rid+"')"}
		M.dialogAlert(dt)
	}
	move1(form,data){
		let dis=form.get("dis")
		let actionid=form.get("actionid")
		let billinid=form.get("billinid")
		let n=form.get("n")
		let st=form.get("st")
		let stock=data
		let ct=this.ce("div",{"class":"formg"})
				let p=this.ce("p",{"style":"padding:10px 10px 0px 10px"})
				this.end(p,[this.cn("โปรดเลือกคลังสินค้า ปลายทาง และจำนวนที่ต้องการย้ายไป")])
				let d1=this.ce("div",{})
				let f=this.ce("form",{"name":"itformove","class":"form100"})
					let list=[]
					let l=this.ce("label",{"id":"label_it_sel0_n__stock","onfocus":"M.formTransFocus(this);","for":"it_sel0_n__stock","class":"formg_label"})
					this.end(l,[this.cn("คลังสินค้า")])					
					let sel=this.ce("select",{"id":"it_sel0_n__stock","name":"_stock","onfocus":"M.formTransFocus(this)","onblur":"M.formTransBlur(this)"})
					for(let i=0;i<stock.length;i++){
						if(stock[i].sku_root!=st){
							list[i]=this.ce("option",{"value":stock[i].sku})
						}else{
							list[i]=this.ce("option",{"value":stock[i].sku,"disabled":"disabled"})
						}
						this.end(list[i],[this.cn(stock[i].name)])
						this.end(sel,[list[i]])
					}
					let l0=this.ce("label",{"id":"label_it_input0_n__stock","onfocus":"M.formTransFocus(this)","for":"it_input0_n__stock","class":"formg_label"})
						this.end(l0,[this.cn("จำนวน")])
					let i0=this.ce("input",{"type":"number","name":"_n","id":"it_input0_n__stock","onfocus":"M.formTransFocus(this)","onblur":"M.formTransBlur(this)"})
					let l1=this.ce("label",{"id":"label_it_input1_n__stock","onfocus":"M.formTransFocus(this)","for":"it_input1_n__stock","class":"formg_label"})
						this.end(l1,[this.cn("หมายเหตุ")])
					let i1=this.ce("input",{"type":"text","name":"_note","id":"it_input1_n__stock","onfocus":"M.formTransFocus(this)","onblur":"M.formTransBlur(this)"})
				this.end(f,[l,sel,l0,i0,l1,i1])
				this.end(d1,[f])
		this.end(ct,[p,d1])
		let rid = this.rid()
		let bts = [
			{"value":"ยกเลิก","onclick":"M.dialogClose('"+rid+"')"},
			{"value":"ตกลง","id":"pressbuttoninputok","onclick":"It.move2('"+rid+"','"+dis+"','"+actionid+"','"+billinid+"','"+n+"','"+st+"','itformove')"}
		]
		M.dialog({"rid":rid,"display":1,"ct":ct,"bts":bts,"title":"โปรดเลือก","width":"250"})
		
	}
	move2(rid,dis,actionid,billinid,n,st,form){
		let f=document.forms[form]
		let _stock=f._stock.value
		let move=_stock+"="+f._n.value
		let _note=f._note.value
		let dt={"data":{"a":"it","c":"lot","move":move,"billinid":billinid,"st":st,"note":_note,"rid":rid},"result":It.move2Result,"error":It.move2Error}
		this.setFec(dt)
	}
/*	move(dis,actionid,billinid,n,st){
		let did=this.id(actionid)
		let note=did.parentNode.parentNode.cells[1].innerHTML
		note=note.replace(/<a.{1,}">|<\/a>/g, "")
		let move=prompt("ย้ายสินค้าใน\n\n"+note+"\n------------------------------------\nไปที่คลังอื่น\nโปรดใส่ รหัสภายในคลังสินค้า=จำนวนที่ต้องการย้ายไป\n เช่น x=2")
		if(typeof move=="string"){
			let dt={"data":{"a":"it","c":"lot","move":move,"billinid":billinid,"st":st},"result":It.moveResult,"error":It.moveError}
			this.setFec(dt)
		}else{
			alert("คุณยกเลิก")
		}
	}*/
	move2Result(re,form,bt){
		if(re["result"]){
			It.move2ResultOption(re,form)
			//let dt={"data":{"a":"bill58","b":"print_move","sku":re["sku"]},"result":It.printMoveResult,"error":It.printMoveError}
			//M.setFec(dt);
			//alert("✔️ สำเร็จ เรียบร้อย");
			//location.reload();
		}else{
			It.move2Error(re,form,bt)
		}
	}
	move2ResultOption(re,form){
		let rid_=form.get("rid")
		M.dialogClose(rid_)
		let rid = M.rid()
		let ct=this.ce("div",{})
			let f=this.ce("form",{"class":"form100"})
				let d1=this.ce("div",{"class":"it_move_success_option"})
					let i1=this.ce("input",{"type":"button","value":"พิมพ์","onclick":"It.move2Print('"+re["sku"]+"','"+re["message_error"]+"')"})
				this.end(d1,[i1])
				let d2=this.ce("div",{"class":"it_move_success_option"})
					let i2=this.ce("input",{"type":"button","value":"โหลดหน้านี้ใหม่","onclick":"location.reload()"})
				this.end(d2,[i2])
				let d3=this.ce("div",{"class":"it_move_success_option"})
					let i3=this.ce("input",{"type":"button","value":"ดูใบย้ายสินค้า","onclick":"location.href='?a=bills&c=move&b=view&sku="+re["sku"]+"'"})
				this.end(d3,[i3])	
			this.end(f,[d1,d2,d3])
		this.end(ct,[f])
		let bts=[]
		M.dialog({"rid":rid,"display":1,"ct":ct,"bts":bts,"title":"สำเร็จ","width":"250"})
	}
	move2Print(sku,message_error){
		let dt={"data":{"a":"bill58","b":"print_move","sku":sku,"message_error":message_error},"result":It.printMoveResult,"error":It.printMoveError}
		M.setFec(dt);
	}
	move2Error(re,form,bt){
		alert("❌ เกิดข้อผิดพลาด "+re["message_error"])
	}
	printMoveResult(re,form,bt){ 
		if(re["result"]){

		}else{
			It.printMoveError(re,form,bt)
		}
	}
	printMoveError(re,form,bt){
		alert("❌ พิมพ์ใบย้ายสินค้า ไม่สำเร็จ\n\n"+re["message_error"])
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	m(dis,actionid,t_id,pdroot,n,skuroot1_n,error=""){
		this.itmmm=dis
		this.itm={}
		this.itm.skuroot=pdroot
		this.itm.skuroot_has=n
		this.itm.skuroot_n=1
		this.itm.confirm=0
		this.itm.billinlistid=t_id
		if(skuroot1_n<=0){
			this.popup(dis,'It.mm(did,\''+actionid+'\',\''+pdroot+'\')')
		}else{
			let dt={"data":{"a":"it","b":"mmmgetused","dis":dis,"actionid":actionid,"t_id":t_id,"pdroot":pdroot},"result":It.mLoadUsedResult,"error":It.mLoadUsedError}
			this.setFec(dt)
		}
	}
	mLoadUsedResult(re,form,bt){
		M.popup(It.itmmm,'It.mm(did,\''+form.get('actionid')+'\',\''+form.get('pdroot')+'\','+re.data+')')
	}
	mLoadUsedError(re,form,bt){
		M.popup(It.itmmm,'It.mm(did,\''+form.get('actionid')+'\',\''+form.get('pdroot')+'\')')
	}
	mm(dis,actionid,pdroot,data=[]){
		if(data.length==0){
			data["skuroot1"]=null
			data["skuroot2"]=null
			data["skuroot1_name"]="เลือกสินค้าหลัก"
			data["skuroot2_name"]="เลือกสินค้าหลักแถม ที่ไม่เหมือนสินค้าหลัก"
			data["skuroot1_barcode"]=""
			data["skuroot2_barcode"]=""
			data["skuroot1_unit"]=""
			data["skuroot2_unit"]=""
			data["skuroot1_n"]="0"
			data["skuroot2_n"]="0"
			data["skuroot1_nane"]=""
			data["skuroot2_name"]=""
		}else{
			this.itm.skuroot1=data["skuroot1"]
			this.itm.skuroot1_n=data["skuroot1_n"]*1
			this.itm.skuroot1_name=data["skuroot1_name"]
			this.itm["skuroot1_n_df"]=this.itm.skuroot1_n
			if(data["skuroot2"].length>1){
				this.itm.skuroot2=data["skuroot2"]
				this.itm.skuroot2_n=data["skuroot2_n"]*1
				this.itm.skuroot2_name=data["skuroot2_name"]
				this.itm["skuroot2_n_df"]=this.itm.skuroot2_n
			}
		}
		let did=this.id(actionid)
		let namehtml=did.parentNode.parentNode.cells[3].firstChild.firstChild.innerHTML
		let barcodehtml=did.parentNode.parentNode.cells[3].childNodes[2].innerHTML
		let unithtml=did.parentNode.parentNode.cells[7].innerHTML
		let ct=this.ce("div",{"data-width":"100px","class":"size14"})
			let tb=this.ce("table",{"class":"it_m_product"})
				let cpt=this.ce("caption",{})
				this.end(cpt,[this.cn("️แตกหน่วยขาย")])
				let tr2=this.ce("tr",{})
					let td1=this.ce("td",{"class":"l"})
					td1.innerHTML=namehtml+"<br>"+barcodehtml
					let td2=this.ce("td",{})
						let ip2=this.ce("input",{"id":"skuroot_n","type":"number","value":1,"max":this.itm.skuroot_has,"min":1,"class":"it_m_product","onchange":"It.setNM(this)"})
					this.end(td2,[ip2])
					let td3=this.ce("td",{})
					td3.innerHTML=unithtml
				this.end(tr2,[td1,td2,td3])
			this.end(tb,[cpt,tr2])
			let divar=this.ce("div",{"class":"divar"})
			this.end(divar,[this.cn("⬇️")])
			this.end(tb,[cpt,tr2])
			let tb2=this.ce("table",{"class":"it_m_product1"})
				let tr21=this.ce("tr",{})
					let tds1=this.ce("td",{"class":"select c","id":"skuroot1","onclick":"It.selectProduct(this,'"+actionid+"')"})
					this.end(tds1,[this.cn("👆")])
					let td21=this.ce("td",{"class":"l","data-id":"it_m_product_skuroot1"})
						let code1=this.ce("code",{"id":"code_skuroot1_name","contenteditable":"true"})
						this.end(code1,[this.cn(data["skuroot1_name"])])
						let code1_barcode=this.ce("code",{"style":"display:block"})
						this.end(code1_barcode,[this.cn(" "+data["skuroot1_barcode"])])
					this.end(td21,[code1,code1_barcode])
					let td22=this.ce("td",{})
						let ip22=this.ce("input",{"id":"skuroot1_n","type":"number","value":"0","class":"it_m_product","disabled":"disabled","onchange":"It.setN(this)","min":0,"value":data["skuroot1_n"]})
					this.end(td22,[ip22])
					let td23=this.ce("td",{})
					td23.innerHTML=data["skuroot1_unit"]
				this.end(tr21,[tds1,td21,td22,td23])
				let tr22=this.ce("tr",{})
					let tds2=this.ce("td",{"class":"select c","id":"skuroot2","onclick":"It.selectProduct(this,'"+actionid+"')"})
					this.end(tds2,[this.cn("👆")])
					let td31=this.ce("td",{"class":"l","data-id":"it_m_product_skuroot2"})
						let code2=this.ce("code",{"id":"code_skuroot2_name","contenteditable":"true"})
						this.end(code2,[this.cn(data["skuroot2_name"]+" "+data["skuroot2_barcode"])])
						let code2_barcode=this.ce("code",{"style":"display:block"})
						this.end(code2_barcode,[this.cn(" "+data["skuroot2_barcode"])])
					this.end(td31,[code2,code2_barcode])
					let td32=this.ce("td",{})
						let ip32=this.ce("input",{"id":"skuroot2_n","type":"number","value":"0","class":"it_m_product","disabled":"disabled","onchange":"It.setN(this)","min":0,"value":data["skuroot2_n"]})
					this.end(td32,[ip32])
					let td33=this.ce("td",{})
					td33.innerHTML=data["skuroot2_unit"]
				this.end(tr22,[tds2,td31,td32,td33])
			this.end(tb2,[tr21,tr22])
			let sb=this.ce("input",{"type":"button","value":"ยืนยัน","onclick":"It.mmm()"})
		this.end(ct,[tb,divar,tb2,sb])
		if(data["skuroot1_n"]>1){
			ip22.removeAttribute("disabled")
			if(data["skuroot2_n"]>0){
				ip32.removeAttribute("disabled")
			}
		}
		return ct
	}
	mmm(){
		if(!this.itm.hasOwnProperty("skuroot1")){
			alert("สินค้าหลัก ยังไม่ได้เลือก")
		}else if(!this.itm.hasOwnProperty("skuroot1_n")||this.itm.skuroot1_n<=1){
			alert("จำนวนสินค้าหลัก ต้องมากกว่า 1")
		}else{
			this.itm["skuroot1_name"]=this.id("code_skuroot1_name").innerHTML
			this.itm["skuroot2_name"]=this.id("code_skuroot2_name").innerHTML
			let itm=JSON.stringify(this.itm)
			let dt={"data":{"a":"it","b":"mmm","data":itm},"result":It.mmmResult,"error":It.mmmError}
			this.setFec(dt)
		}
	}
	mmmResult(re,form,bt){
		if(re["result"]){
			alert("✔️ สำเร็จ")
			location.reload();
		}else{
			It.mmmError(re,form,bt)
		}
	}
	mmmError(re,form,bt){
		if(re["confirm"]=="1"){
			let y=confirm(re["message_error"])
			if(y){
				It.itm.confirm=1
				It.mmm()
			}
		}else{
			alert("❌ เกิดข้อผิดพลาด "+re["message_error"])
		}
		It.itm.confirm=0
	}
	selectProduct(did,id){
		if(this.itm.skuroot1==null&&did.id=="skuroot2"){
			alert("เลือกสินค้าหลัก ก่อน")
		}else{
			this.popup(did,'G.search(did,\'itmw\',\''+did.id+'\')')
		}
	}
	productSelect(d,n=null){
		if(d.sku_root!=undefined){
			let sku_root=d.sku_root
			this.setConT(d)
			this.popupClear("it_m_product_"+d.for_id)
		}
	}
	setConT(d){
		let ob=this.id(d.for_id)
		this.itm[d.for_id]=d.sku_root
		ob.parentNode.cells[1].firstChild.innerHTML=d.name
		ob.parentNode.cells[1].childNodes[1].innerHTML=" "+d.barcode
		ob.parentNode.cells[3].innerHTML=d.unit
		ob.parentNode.cells[2].firstChild.disabled=false
	}
	setN(did){
		this.itm[did.id]=did.value
		this.itm[did.id+"_df"]=this.itm[did.id]
	}
	setNM(did){
		this.itm[did.id]=did.value
		this.itm["skuroot1_n"]=this.itm["skuroot1_n_df"]*this.itm[did.id]
		this.id("skuroot1_n").value=this.itm["skuroot1_n"]
		this.itm["skuroot2_n"]=this.itm["skuroot2_n_df"]*this.itm[did.id]
		this.id("skuroot2_n").value=this.itm["skuroot2_n"]
	}
}
