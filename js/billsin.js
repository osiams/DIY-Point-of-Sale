 /*--apt DIY_POS;--ext js;--version 0.0;*/
"use strict"
class billsin extends main{
	constructor(){
		super()
	}
	setAct(did){
		let o=did.parentNode.parentNode
		if(o.tagName=="TD"){
			o=o.parentNode
		}
		let name=o.cells[1].childNodes[0]
		let n=o.cells[2].childNodes[0].childNodes[0]
		let sum=o.cells[4].childNodes[0]
		
		let name_old=name.getAttribute("data-old")
		let balance=n.getAttribute("data-balance")
		let n_old=n.getAttribute("data-old")
		let n_min=n_old*1-balance*1
		let sum_old=sum.getAttribute("data-old")
		let act=o.cells[5].childNodes[0]
		let avg=M.nb(sum.value/n.value)
		o.cells[4].childNodes[1].innerHTML=avg
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
		

	}
	insertData(data,editable){
		for(let i=0;i<data.length;i++){
			this.productSelect(data[i],editable)
		}
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
		if(M.id(a.sku_root)!=undefined){
			alert("❌\n"+a.name+"\nเลือกไปแล้ว อยู่อันดับที่ "+M.id(a.sku_root).innerHTML)
			return false
		}
		let t=M.id("billsin")
		if(t.getAttribute("data-type")!=null){
			return this.billsinSelectEdit(a,editable)
		}
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
		let ipn=M.ce("input",{"class":"wwp","onfocus":"this.classList.replace('wwp', 'wpp')","onblur":"this.classList.replace('wpp', 'wwp')","type":"text","value":a.name})
		//p1.appendChild(ipn)
		let p2=M.ce("p",{"class":"p_bc"})
		p2.appendChild(M.cn(a.bcsku))
		cell1.appendChild(ipn)
		cell1.appendChild(p2)
		let amu=0
		if(a.n!=undefined){
			amu=a.n
		}
		let div3_1=M.ce("div",{})
		let ip=M.ce("input",{"class":"wwp","type":"number","onchange":"Bi.setAct(this)","min":"0","class":"c","value":amu})
		div3_1.appendChild(ip)
		let div3_2=M.ce("div",{})
		div3_2.appendChild(M.cn(a.unit))
		cell2.appendChild(div3_1)
		cell2.appendChild(div3_2)
		let ah=M.ce("a",{"onclick":"Bi.billsinNo(this)","title":"ไม่เอา"})
		ah.appendChild(M.cn(" ✖ "))		
		cell5.appendChild(ah)

		let tol=a.cost*amu
		if(a.sum!=undefined){
			tol=a.sum
		}
		let ip2=M.ce("input",{"type":"number","min":"0","class":"r","value":tol,"onchange":"Bi.setAct(this)","step":"any"})
		let tavg=this.nb(tol/amu,2)
		let avg=M.ce("p",{})
			avg.appendChild(M.cn(tavg))
		cell4.appendChild(ip2)
		cell4.appendChild(avg)
		if(ls%2==0){
			row.className="i2"
		}
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	billsinSelectEdit(a,editable=true){
		let	old_n=""
		let	old_name=""
		let	old_sum=""
		if(a.n!=undefined){
			old_name=a.name
			old_n=a.n
			old_sum=a.sum
		}
		
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
		p2.appendChild(M.cn(a.bcsku))
		cell1.appendChild(ipn)
		cell1.appendChild(p2)
		let amu=0
		if(a.n!=undefined){
			amu=a.n
		}
		let div3_1=M.ce("div",{})
		let ip=M.ce("input",{"type":"number","data-old":amu,"data-balance":balance,"min":"0","class":"c","onchange":"Bi.setAct(this)","value":amu})
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
		let ip2=M.ce("input",{"data-old":old_sum,"type":"number","min":"0","onchange":"Bi.setAct(this)","class":"r","value":tol,"step":"any"})
		let tavg=this.nb(tol/amu,2)
		let avg=M.ce("p",{})
			avg.appendChild(M.cn(tavg))
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
		
	}
	billsinNo(did){
		let o=did.parentNode.parentNode
		let name=o.cells[1].childNodes[0].value
		let y=confirm("คุณไม่ต้องการ\n"+name)
		if(y){
			let t=M.id("billsin")
			this.l(o)
			let ix=o.rowIndex
			t.deleteRow(ix)
			let l=t.rows.length
			let ls=l-2			
			for(let i=ix;i<ls;i++){
				t.rows[i].cells[0].innerHTML=i+"."
				if(i%2!=0){
						t.rows[i].removeAttribute("class")
				}else{
					t.rows[i].className="i2"
				}
			}
		}
	}
	productInSearch(e){
		let f=document.forms.billsin
		let fr=this.id("iframeproductin")
		if(e==undefined){
			fr.src="?a=product&b=select&fl="+f.fl.value+"&tx="+f.tx.value+"&for=billsin"
		}else{
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
		let l=t.rows.length
		let ls=l-2
		if(!editable){
			ls=l
		}
		let dt=[]
		let n_list=0;
		let error=false
		for(let i=1;i<ls;i++){
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
			dt[n_list]={"name":name,"sku_root":sku_root,"n":n,"sum":sum,"act":act}
			n_list+=1
		}
		if( n_list==0&&editable){
			alert("ไม่มีสินค้านำเข้า คุณยังไม่ได้เลือก")
		}else if(!error){
			let note=null
			let formData = new FormData()
			formData.append("a","bills")			
			formData.append("submith","clicksubmit")		
			formData.append("c","bills_in")				
			formData.append("product",JSON.stringify(dt))		
			//M.l(dt)
			if(f.note!=undefined){
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
			}
		}
	}
	billsinSaveResult(re,form,bt){
		if(re["result"]){
			alert("สำเร็จ")
			let ed=re["data"]["sku"]
			location.href="?a=bills&c=in&ed="+ed
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
}
