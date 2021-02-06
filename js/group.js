"use strict"
class group extends main{
	constructor(){
		super()
		this.table_prop = null
		this.button_add_prop = null
		this.i = 0
		this.data_type = {"s":"ข้อความ","n":"จำนวน","b":"จริงเท็จ"}
		this.a = "group"
	}
	run(){
		window.onload=()=>{
			this.inst = Gp
			this.button_add_prop = this.id("button_add_prop")
			this.table_prop = this.id("table_prop")
		}
	}
	ctAddProp(form_name,prop_list,display_id){
		let dt={"data":{"a":"prop","b":"get_all_list","display_id":display_id,"from_name":form_name,"prop_list":prop_list},"result":Gp.getListPropResult,"error":Gp.getListPropError}
		this.setFec(dt)
	}
	getListPropResult(re,form,bt){
		if(re["result"]){
			Gp.ctSelectProp(re,form,bt)
		}else{
			Gp.getListPropError(re,form,bt)
		}
	}
	getListPropError(re,form,bt){
		alert("error")
		//Gp.ctSelectProp(re,form,bt)
	}
	ctSelectProp(re,form,bt){
		let rid = this.rid()
		let arr = re.data
		let display_id = form.get("display_id")
		let form_name = form.get("from_name")
		let prop_list = document.forms[form_name][form.get("prop_list")].value
		let ct = this.ce("div",{})
		let fron = this.ce("form",{"name":rid,"style":"width:100%;text-align:center;"})
		let d1 = this.ce("div",{"style":"width:80%;text-align:left;display:inline-block;margin:0 auto;"})
		let prop_has = prop_list.substring(1, prop_list.length-1).split(",,")
			for(let i=0;i<arr.length;i++){
				let boc = this.ce("label",{"class":"list_checkbox"})
					let ck = this.ce("input",{"type":"checkbox","name":"checkbox_"+rid,"data-name":arr[i]["name"],"data-data_type":arr[i]["data_type"],"value":arr[i]["sku_root"]})
					if(prop_has.includes(arr[i]["sku_root"])){
						ck.checked = true
					}					
					let tn = this.cn(arr[i]["name"])
				this.end(boc,[ck,tn])
				this.end(d1,[boc])
			}
		this.end(fron,[d1])
		this.end(ct,[fron])	
		let bts = [
			{"value":"ยกเลิก","onclick":"M.dialogClose('"+rid+"')"},
			{"value":"ตกลง","onclick":"Gp.selectOK('"+rid+"','"+form_name+"','"+form.get("prop_list")+"','"+display_id+"')"}
		]
		M.dialog({"rid":rid,"display":1,"ct":ct,"bts":bts,"title":"เลือกคุณสมบัติ","width":"250"})
	}
	selectOK(rid,form_name,prop_list,display_id){
		//alert(document.forms[rid]["checkbox_"+rid][0].value)
		let prop_lists =  document.forms[form_name][prop_list]
		let ck = document.forms[rid]["checkbox_"+rid]
		let tb = this.id(display_id)
		let tb_length = tb.rows.length
		for(let i=tb_length-1;i>0;i--){
			tb.deleteRow(i);
		}
		prop_lists.value = ""
		let index = 0	//--มี th อยู่
		//--style .ให้ตรงกับ group.php::regisGroupPage
		for(let i=0;i<ck.length;i++){
			if(ck[i].checked){
				prop_lists.value +=","+ck[i].value+","
				let name = ck[i].getAttribute("data-name")
				let data_type = ck[i].getAttribute("data-data_type")
				index+=1
				tb.insertRow(index)
				tb.rows[index].className = "i"+(((index-1)%2)+1)
				tb.rows[index].insertCell(0)
				tb.rows[index].insertCell(1)
				tb.rows[index].cells[0].style = "text-align:left;padding:8px 5px"
				tb.rows[index].cells[1].style = "text-align:left;padding:8px 5px"
				tb.rows[index].cells[0].innerHTML = name
				tb.rows[index].cells[1].innerHTML = this.data_type[data_type]
			}
		}
		this.dialogClose(rid)
	}
	addProp(){
		this.i += 1
		let index = this.table_prop.rows.length
		let ip1 = this.ce("input",{"type":"text"})
		let ip2 = this.ce("input",{"type":"text"})
		let sl = this.ce("select",{})
			let op1 = this.ce("option",{})
			this.end(op1,[this.cn("ข้อความ")])
			let op2 = this.ce("option",{})
			this.end(op2,[this.cn("จำนวน")])
			let op3 = this.ce("option",{})
			this.end(op3,[this.cn("จริงเท็จ")])
		this.end(sl,[op1,op2,op3])
		let setg = this.ce("a",{})
		this.end(setg,[this.cn("⚙️")])
		this.table_prop.insertRow(index);
		this.table_prop.rows[index].insertCell(0)
		this.table_prop.rows[index].insertCell(1)
		this.table_prop.rows[index].insertCell(2)
		this.table_prop.rows[index].insertCell(3)
		this.table_prop.rows[index].cells[0].appendChild(ip1)
		this.table_prop.rows[index].cells[1].appendChild(ip2)
		this.table_prop.rows[index].cells[2].appendChild(sl)
		this.table_prop.rows[index].cells[3].appendChild(setg)
	}
	edit(sku_root,deep,parent){
		let f=document.forms.group
		f.action="?a="+this.a+"&b=edit&d="+deep+"&parent="+parent
		f.sku_root.value=sku_root
		f.submit()
	}
	delete(sku_root,name,deep,parent){
		let y=confirm("คุณต้องการลบ กลุ่ม \n\""+name+"\"\nรายการสินค้าที่อยู่ในกลุ่มนี้ \nจถูกย้ายไปอยู่ในกลุ่ม \n\"_ไม่ระบุ\"")
		if(y){
			let f=document.forms.group
			f.action="?a="+this.a+"&b=delete&d="+deep+"&parent="+parent
			f.sku_root.value=sku_root
			f.submit()
		}
	}
}
