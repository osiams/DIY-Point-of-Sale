"use strict"
class billsin extends main{
	constructor(){
		super()
		this.dow_n=0
		this.icon={};
		this.at=0
		this.group=null
		this.partner=null
		this.pd={}
	}
	loadProduct(did){
		this.at=0
		let index=did.selectedIndex
		let group=did.options[index].getAttribute("data-group")
		if(group=="partner"){
			location.href="?a=bills&b=fill&c=in&pn_partner=partner&sku_root="+did.value
		}
		
	}
	setAct(did){
		let o=did.parentNode.parentNode
		if(o.tagName=="TD"){
			o=o.parentNode
		}
		let sku_root=o.cells[0].id
		let name=o.cells[1].childNodes[0]
		let n=o.cells[2].childNodes[0].childNodes[0]
		let sum=o.cells[4].childNodes[0]
		
		let name_old=name.getAttribute("data-old")
		let balance=n.getAttribute("data-balance")
		let n_old=n.getAttribute("data-old")
		let n_min=n_old*1-balance*1
		let sum_old=sum.getAttribute("data-old")
		let act=o.cells[5].childNodes[0]

		let bill_type=document.forms.billsin.bill_type.value
		let per1=this.pd[sku_root]["sum_edit"]/this.pd[sku_root]["n_edit"]
		if(sum.value==0){
			per1=this.pd[sku_root]["cost"]
			if(bill_type=="v0"){
				per1=per1*100/(100+(this.pd[sku_root]["vat_p"]*1))
			}
		}
		if(sum.value==this.pd[sku_root]["sum_edit"]){
			sum.value=per1*n.value
		}
		
		let avg=M.nb(sum.value/n.value,3)
		o.cells[4].childNodes[1].innerHTML=avg		
		
		this.pd[sku_root]["n_edit"]=n.value
		this.pd[sku_root]["sum_edit"]=sum.value
		if(act.value !="4"&&n_old!=null){
			if(name.value!=name_old||n.value!=n_old||sum.value!=sum_old){
				if(n.value<n_min){
					n.value=n_min
				}
				if(n.value==0){
					o.className="billsin_delete"
					act.selectedIndex = "0"
				}else{
					o.className="billsin_edit"
					act.selectedIndex = "2"
				}
			}else {
				act.selectedIndex = "1"
				o.className="i1"
			}
		}/*else{
			o.className="billsin_add"
			act.selectedIndex = "2"			
		}*/
		this.setDisplayTV()

	}
	setDisplayTV(){
		let bill_type=document.forms.billsin.bill_type.value
		let product_list=document.forms.billsin.product_list.value
		let tv0=this.id("billin_tv0")
		let tv1=this.id("billin_tv1")
		let tv2=this.id("billin_tv2")
		let sun=0
		let vat=0
		let sum=0
		let product_has = F.valueListToArray(product_list)
		for(let i=0;i<product_has.length;i++){
			let prop=product_has[i]
			let v_p=this.pd[prop]["vat_p"]*1
			let sm=this.pd[prop]["sum_edit"]*1
			if(bill_type=="v1"||bill_type=="c"){
				let sl=sm*100/(100+v_p)
				
				sun+=sl
				vat+=sm-sl
				M.l("vat="+vat)
				sum+=sm
			}else{
				sun+=sm
				vat+=(v_p*sm)/100		
				sum+=sm		
			}
		}
		tv0.innerHTML=this.nb(sun,3)
		tv1.innerHTML=this.nb(vat,3)
		tv2.innerHTML=this.nb(sun+vat,3)
		return {"pv0":sun,"v":vat,"pv1":sun+vat}
	}
	getSumPay(){
		
	}
	setNameAct(did){
		let o=did.parentNode.parentNode
		if(o.tagName=="TD"){
			o=o.parentNode
		}
		let sku_root=o.cells[0].id
		this.pd[sku_root]["name_edit"]=did.value
	}
	setPartner(sku_root){
		this.partner=sku_root
	}
	insertData(data,editable,id=null,product_list_id){
		if(Fsl.partner[id]==undefined){
			Fsl.partner[id]={}
			Fsl.partner_old[id]={}
		}
		Bi.at=0
		for(let i=0;i<data.length;i++){
			if(id!=null){
				let root=data[i]["sku_root"]
				if(this.pd[root]==undefined){
					this.pd[root]=data[i]
					this.pd[root]["name_edit"]=this.pd[root]["name"]
					this.pd[root]["n_edit"]=0
					this.pd[root]["sum_edit"]=0
					this.pd[root]["display_id"]=id
					this.pd[root]["partner_list_id"]=product_list_id
				}
				Fsl.partner[id][root]={
							"icon":null,
							"name":data[i]["name"],
							"value":0
				}
			}
			Bi.productSelect(data[i],editable)
		}
		Fsl.setPartnerOld(id)
	}
	billsinSelect(did,sku_root,cost){
		let o=did.parentNode.parentNode
		let name=o.cells[1].childNodes[0].innerHTML
		let bcsku=o.cells[1].childNodes[1].innerHTML
		let unit=o.cells[3].innerHTML
		let a={"name":name,"bcsku":bcsku,"unit":unit,"sku_root":sku_root,"cost":cost}
		window.parent.billsinSelect(a)
	}
	productSelect(a,editable=true){
		//M.l(a)
		let step="1"
		if(a.s_type!="p"){
			step="any"
		}
		if(M.id(a.sku_root)!=undefined){
			alert("❌\n"+a.name+"\nเลือกไปแล้ว อยู่อันดับที่ "+M.id(a.sku_root).innerHTML)
			return false
		}
		let t=M.id("billsin")
		if(t.getAttribute("data-type")!=null){
			return this.billsinSelectEdit(a,editable)
		}
		Bi.at+=1
		let ls=Bi.at
		/*let l=t.rows.length
		let ls=l
		if(!editable){
			ls=l
			
		}*/
		let row=t.insertRow(ls)
		
		let cell0 = row.insertCell(0)
		let cell1 = row.insertCell(1)
		let cell2 = row.insertCell(2)
		let cell3 = row.insertCell(3)
		let cell4 = row.insertCell(4)
		let cell5 = row.insertCell(5)
		cell0.innerHTML=ls+"."
		cell0.setAttribute("id",a.sku_root);
		cell3.innerHTML=a.unit
		//let p1=M.ce("p",{"class":"l","contenteditable":"true"})
		let ipn=M.ce("input",{"class":"wwp","onfocus":"this.classList.replace('wwp', 'wpp')","onblur":"this.classList.replace('wpp', 'wwp')","type":"text","value":a.name_edit,"onchange":"Bi.setNameAct(this)"})
		//p1.appendChild(ipn)
		let p2=M.ce("p",{"class":"p_bc"})
			let s_wlv=M.ce("span",{"class":"s_wlvt"})
			M.end(s_wlv,[M.cn("ชั่งตวงวัด")])
			let s_price=M.ce("span",{})
			M.end(s_price,[M.cn(" (ปัจจุบัน ขาย ฿"+a.price)])
			let s_cost=M.ce("span",{})
			M.end(s_cost,[M.cn(",ทุน ฿"+a.cost+")")])
		if(a.s_type!="p"){
			p2.appendChild(s_wlv)
		}
		p2.appendChild(M.cn(a.bcsku==null?"":a.bcsku))
		M.end(p2,[s_price,s_cost])
		cell1.appendChild(ipn)
		cell1.appendChild(p2)
		let amu=0
		if(a.n_edit!=undefined){
			amu=a.n_edit
		}
		let div3_1=M.ce("div",{})
		let ip=M.ce("input",{"class":"wwp","type":"number","onkeyup":"Bi.setAct(this)","onchange":"Bi.setAct(this)","min":"0","step":step,"class":"c","value":amu})
		div3_1.appendChild(ip)
		let div3_2=M.ce("div",{})
		div3_2.appendChild(M.cn(a.unit))
		cell2.appendChild(div3_1)
		cell2.appendChild(div3_2)
		let ah=M.ce("a",{"onclick":"Bi.billsinNo(this)","title":"ไม่เอา"})
		ah.appendChild(M.cn(" ✖ "))		
		cell5.appendChild(ah)

		let tol=a.cost*amu
		if(a.sum_edit!=undefined){
			tol=a.sum_edit
		}
		let ip2=M.ce("input",{"type":"number","min":"0","class":"r","value":tol,"onkeyup":"Bi.setAct(this)","onchange":"Bi.setAct(this)","step":"any"})
		let tavg=M.nb(tol/amu,2)
		let avg=M.ce("p",{})
			avg.appendChild(M.cn(isNaN(tavg)?"0.00":tavg))
		cell4.appendChild(ip2)
		cell4.appendChild(avg)
		if(ls%2==0){
			row.className="i2 ty"
		}else{
			row.className="ty"
		}
		row.id=M.rid()
		let row_an=t.insertRow(ls+1)
		row_an.id=row.id+"_an"
		row_an.style.height="0px"
		let ho=row.offsetHeight
		row.style.display="none"
		setTimeout("Bi.setTrHeight('"+row.id+"',"+ho+",0)",10)
	}
	setTrHeight(id,n,n_an){
		let m=1
		let y=[10,9,8,7,6,5,4,3,2,1,1];
		let q=Math.floor((n_an/n)*10)
		m=y[q]
		if(n_an<n){
			M.id(id+"_an").style.height=n_an+m+"px"
			setTimeout("Bi.setTrHeight('"+id+"',"+n+","+(n_an+m)+")",10)
		}else{
			M.id(id+"_an").parentNode.removeChild(M.id(id+"_an"))
			M.id(id).style.display="table-row"
		}
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	billsinSelectEdit(a,editable=true){
		let	old_n=""
		let	old_name=""
		let	old_sum=""
		if(a.n!=undefined){
			old_name=a.name
			old_n=a.n*1
			old_sum=a.sum*1
		}
		let step="1"
		if(a.s_type!="p"){
			step="any"
		}
		M.l(a)
		let	balance=a.balance
		if(M.id(a.sku_root)!=undefined){
			alert("❌\n"+a.name+"\nเลือกไปแล้ว อยู่อันดับที่ "+M.id(a.sku_root).innerHTML)
			return false
		}
		let t=M.id("billsin")
		let l=t.rows.length
		let ls=l-2
		if(!editable){
			ls=l
		}

		let row=t.insertRow(ls)
		
		let cell0 = row.insertCell(0)
		let cell1 = row.insertCell(1)
		let cell2 = row.insertCell(2)
		let cell3 = row.insertCell(3)
		let cell4 = row.insertCell(4)
		let cell5 = row.insertCell(5)
		cell0.innerHTML=ls+"."
		cell0.setAttribute("id",a.sku_root);
		cell3.innerHTML=a.unit
		//let p1=M.ce("p",{"class":"l","contenteditable":"true"})
		let ipn=M.ce("input",{"class":"wwp","data-old":a.name,"type":"text","onchange":"Bi.setAct(this)","onfocus":"this.classList.replace('wwp', 'wpp')","onblur":"this.classList.replace('wpp', 'wwp')","value":a.name})
		//p1.appendChild(ipn)
		let p2=M.ce("p",{"class":"p_bc"})
			let s_wlv=M.ce("span",{"class":"s_wlvt"})
			this.end(s_wlv,[this.cn("ชั่งตวงวัด")])
			let s_price=M.ce("span",{})
			this.end(s_price,[M.cn(" (ปัจจุบัน ขาย ฿"+a.price)])
			let s_cost=M.ce("span",{})
			this.end(s_cost,[M.cn(",ทุน ฿"+a.cost+")")])
		if(a.s_type!="p"){
			p2.appendChild(s_wlv)
		}
		p2.appendChild(M.cn(a.bcsku==null?"":a.bcsku))
		this.end(p2,[s_price,s_cost])
		cell1.appendChild(ipn)
		cell1.appendChild(p2)
		let amu=0
		if(a.n!=undefined){
			amu=a.n
		}
		let div3_1=M.ce("div",{})
		let ip=M.ce("input",{"type":"number","data-old":amu,"data-balance":balance,"min":"0","step":step,"class":"c","onkeyup":"Bi.setAct(this)","onchange":"Bi.setAct(this)","value":amu})
		div3_1.appendChild(ip)
		let div3_2=M.ce("div",{})
		div3_2.appendChild(M.cn(a.unit))
		cell2.appendChild(div3_1)
		cell2.appendChild(div3_2)
		if(old_n!=""){
			let select=M.ce("select",{ "disabled": "disabled","name":"act"})
			let op1=M.ce("option",{"value":"0"})
			op1.appendChild(M.cn("ลบ"))
			let op2=M.ce("option",{"value":"1","selected":"selected"})
			op2.appendChild(M.cn("คงเดิม"))
			let op3=M.ce("option",{"value":"2"})
			op3.appendChild(M.cn("แก้ไข"))
			select.appendChild(op1)
			select.appendChild(op2)
			select.appendChild(op3)
			cell5.appendChild(select)
		}else{
			let select=M.ce("select",{ "disabled": "false","name":"act"})
			let op1=M.ce("option",{"value":"4","selected":"selected"})
			op1.appendChild(M.cn("เพิ่ม"))
			select.appendChild(op1)
			cell5.appendChild(select)
			let ah=M.ce("a",{"onclick":"Bi.billsinNo(this)","title":"ไม่เอา"})
			ah.appendChild(M.cn(" ✖ "))		
			cell5.appendChild(ah)
		}
		let tol=a.cost*amu
		if(a.sum!=undefined){
			tol=a.sum
		}
		let ip2=M.ce("input",{"data-old":old_sum,"type":"number","min":"0","onkeyup":"Bi.setAct(this)","onchange":"Bi.setAct(this)","class":"r","value":tol,"step":"any"})
		let tavg=this.nb(tol/amu,2)
		let avg=M.ce("p",{})
			avg.appendChild(M.cn(isNaN(tavg)?"0.00":tavg))
		cell4.appendChild(ip2)
		cell4.appendChild(avg)

		if(old_n!=""){
			let pb=M.ce("p",{"class":"r","style":"font-size:11px;"})
			pb.appendChild(M.cn("เหลือ "+balance+"/"+a.n))
			cell2.appendChild(pb)
		}

		if(old_n==""){
			row.className="billsin_add"
		}
		if(!editable){
			ls=l
			ipn.setAttribute("readonly","readony")
			ip.setAttribute("readonly","readony")
			ip2.setAttribute("readonly","readony")
		}
		row.id=this.rid()
		let row_an=t.insertRow(ls+1)
		row_an.id=row.id+"_an"
		row_an.style.height="0px"
		let ho=row.offsetHeight
		row.style.display="none"
		setTimeout("Bi.setTrHeight('"+row.id+"',"+ho+",0)",10)
	}
	billsinNo(did){
		let o=did.parentNode.parentNode
		let sku_root=o.cells[0].id

		let name=o.cells[1].childNodes[0].value
		let y=confirm("คุณไม่ต้องการ\n"+name)
		if(y){
			let t=M.id("billsin")
			//this.l(o)
			let ix=o.rowIndex
			t.deleteRow(ix)
			let ls=Bi.at		
			for(let i=ix;i<ls;i++){
				t.rows[i].cells[0].innerHTML=i+"."
				if(i%2!=0){
						t.rows[i].removeAttribute("class")
				}else{
					t.rows[i].className="i2"
				}
			}
			
			let display_id=this.pd[sku_root]["display_id"]
			let partner_list_id=this.pd[sku_root]["partner_list_id"]
			delete Fsl.partner[display_id][sku_root]
			Bi.at-=1
			Fsl.setPartnerOld(display_id)
			Fsl.selectPartnerListValue("product",display_id,partner_list_id)
			
			this.setDisplayTV()
		}
	}
	productInSearch(e){
		let f=document.forms.billsin
		let fr=this.id("iframeproductin")
		if(e==undefined){
			fr.src="?a=product&b=select&fl="+f.fl.value+"&tx="+f.tx.value+"&for=billsin"
		}else{
			M.l("up="+f.tx.value)
			let k=e.key.toLowerCase()
			if(k=="enter"){
				fr.src="?a=product&b=select&fl="+f.fl.value+"&tx="+f.tx.value+"&for=billsin"
				e.preventDefault()
			}
		}
	}
	billsinSumit(editable=true){
		let f=document.forms.billsin
		let t=M.id("billsin")
		//let l=t.rows.length
		let ls=Bi.at
		/*let ls=l-2
		if(!editable){
			ls=l
		}*/
		let dt=[]
		let n_list=0;
		let error=false
		for(let i=1;i<=ls;i++){
			let row=t.rows[i]
			let sku_root=row.cells[0].id
			let od=row.cells[0].innerHTML
			let name=row.cells[1].childNodes[0].value.trim()
			let n=row.cells[2].childNodes[0].childNodes[0].value
			let sum=row.cells[4].childNodes[0].value
			let act=row.cells[5].childNodes[0].value
			let n_old=row.cells[2].childNodes[0].childNodes[0].getAttribute("data-old")
			if(n==0&&(n_old==""||n_old==null)){
				alert("รายการที่ "+od+" จำนวน ต้องมากกว่า 0")
				error=true
				break
			}
			let vat_p=this.pd[sku_root]["vat_p"]
			dt[n_list]={"name":name,"sku_root":sku_root,"n":n,"sum":sum,"vat_p":vat_p,"act":act}
			n_list+=1
		}
		if( n_list==0&&editable){//alert(n_list+"*"+editable)
			alert("ไม่มีสินค้านำเข้า คุณยังไม่ได้เลือก")
		}else if(!error){
			let note=null
			/*let formData = new FormData()
			formData.append("a","bills")			
			formData.append("submith","clicksubmit")		
			formData.append("c","bills_in")				
			formData.append("product",JSON.stringify(dt))	*/
			let payu=this.getPayuOb()
			let dt2={"data":{"a":"bills","submith":"clicksubmit","c":"bills_in","product":JSON.stringify(dt),
				"bill_no":f.bill_no.value,"pn":f.pn.value,
				"bill_date":f.bill_date.value,"bill_type":f.bill_type.value,"note":f.note.value,"payu":JSON.stringify(payu),
				"b":"fill"},
				"result":Bi.billsinSaveResult,"error":Bi.billsinSaveError}	
			M.l(dt)
			let patt = /^([1-9])[0-9]{3}-(0|1)[0-9]-(0|1|2|3)[0-9]$/g;
			let date_result = patt.test(f.bill_date.value);
			let sumPay1=this.getPay1()
			let sumPay2=this.getPay2()
			//alert(sumPay1+""+sumPay2)
			if(f.bill_no.value.trim().length==0){
				alert("เลขที่ใบเสร็จ คุณว่าง")
			}else if(!date_result){
				alert("วันที่ในใบเสร็จ ไม่ถูกต้อง หรือ ว่าง")
			}else if(f.bill_type.value!="c"&&f.bill_type.value!="v1"&&f.bill_type.value!="v0"){
				alert("ค่ารูปแบบใบเสร็จไม่ถูกต้อง")
			}else if(sumPay1+0.99<sumPay2){
				alert("ยอดเงินรวม ในรูปแบบการชำระ น้อยกว่า ยอดราคารวมสินค้า")
			}else{
				this.setFec(dt2)
			}
			/*if(f.note!=undefined){
				note=confirm("คุณยืยยันที่จะแกไข\n"+f.note.value)
				if(note){
					formData.append("b","edit")
					formData.append("sku",f.sku.value)
					formData.append("note",f.note.value)
					M.fec("POST","",Bi.billsinSaveResult,Bi.billsinSaveError,null,formData)
				}
			}else{
				note=prompt("ใส่ข้อความที่มา รายละเอียดแบบย่อ\nชื่อคู่ค้า/วันที่ในใบเสร็จ/เลขที่ใบเสร็จ\n\nเช่น Thainamthip/12022020/151257xxxx\nเช่น Market/31042020/004131xxxxxx")
				if(note!=null){
					formData.append("b","fill")
					formData.append("note",note)
					M.fec("POST","",Bi.billsinSaveResult,Bi.billsinSaveError,null,formData)
				}			
			}*/
		}
	}
	getPay1(){
		let re=0
		let f=document.forms.billsin
		//let payu_has = f.payu_list.value.substring(1, f.payu_list.value.length-1).split(",,")
		let payu_has=F.valueListToArray(f.payu_list.value)
		for(let i=0;i<payu_has.length;i++){
			re+=f["payu_"+payu_has[i]].value*1
		}
		return re
	}
	getPayuOb(){
		let re={}
		let f=document.forms.billsin
		let payu_has=F.valueListToArray(f.payu_list.value)
		for(let i=0;i<payu_has.length;i++){
			re["payu_"+payu_has[i]]=f["payu_"+payu_has[i]].value*1
		}
		return re
	}
	getPay2(){
		let r=this.setDisplayTV()
		return r.pv1
	}
	billsinSaveResult(re,form,bt){
		if(re["result"]){
			let data=re["data"]["sku"]
			let url_to="?a=bills&c=in"
			let url_key=form.get("url_key")
			Ful.fileUploadImgs('bill_in','sku',data,'Bi.icon',url_to,'ed')
			/*alert("สำเร็จ")
			let ed=re["data"]["sku"]
			location.href="?a=bills&c=in&ed="+ed*/
		}else{
			Bi.billsinSaveError(re,form,bt)
		}
	}
	billsinSaveError(re,form,bt){
		if(re["result"]!=undefined){
			alert("เกิดข้อผิดพลาด\n"+re["message_error"])
		}
	}
	billsInEdit(sku){
		let f=document.forms.billsin
		f.action="?a=bills&b=edit&c=in"
		f.sku.value=sku
		f.submit()
	}
	billsInDelete(sku,name){
		let y=confirm("คุณต้องการลบ "+name+"\n*จะลบได้ต่อเมื่อสินค้าในใบนำเข้าสินค้า\nยังไม่มีการขายออกทุกรายการ")
		if(y){
			let f=document.forms.billsin
			f.action="?a=bills&b=delete&c=in"
			f.sku.value=sku
			f.submit()
		}
	}
	selectPartnerOK(display_id,partner_list_id){
		this.setEmptyTable(display_id)
		let data=[]
		let i=0
		for (let prop in Fsl.partner[display_id]) {
			data[i]=this.pd[prop]
			i+=1
		}
		this.insertData(data,true,"bullin")
		this.setDisplayTV()
	}
	setEmptyTable(display_id){
		if(this.id("billsin")!=undefined){
			let t=this.id("billsin")
			let len=t.rows.length
			for(let i=len-1;i>0;i--){
				t.deleteRow(i)
			}
		}
	}
}
