"use strict"
class sell extends main{
	constructor(){
		super()
		this.pd={}
		this.fl={}
		this.dt={}
		this.member={"name":"บุคลทั่วไป","lastname":"","sku_root":"","icon":"null.png"}
		this.member_default={"name":"บุคลทั่วไป","lastname":"","sku_root":"","icon":"null.png"}
		this.bc="✂️"
		this.idn=null
		this.idmb=null
		this.dpro=null
		this.dlist=null
		this.dsums=null
		this.dlast=null
		this.spnlist=null
		this.datenow=null
		this.iplus=null
		this.kl="";
		this.loadlocalst=0
		this.k={"NumpadMultiply":"*","NumpadAdd":"+","Minus":"-","NumpadSubtract":"-","NumpadDecimal":".",
			"Digit0":"0","Digit1":"1","Digit2":"2","Digit3":"3","Digit4":"4",
			"Digit5":"5","Digit6":"6","Digit7":"7","Digit8":"8","Digit9":"9",
			"Numpad0":"0","Numpad1":"1","Numpad2":"2","Numpad3":"3","Numpad4":"4",
			"Numpad5":"5","Numpad6":"6","Numpad7":"7","Numpad8":"8","Numpad9":"9",
			"ArrowDown":"-","ArrowUp":"+","NumpadDivide":"/"
		}
		this.float={"NumpadDecimal":".","Period":".","Backspace":"",
			"Digit0":"0","Digit1":"1","Digit2":"2","Digit3":"3","Digit4":"4",
			"Digit5":"5","Digit6":"6","Digit7":"7","Digit8":"8","Digit9":"9",
			"Numpad0":"0","Numpad1":"1","Numpad2":"2","Numpad3":"3","Numpad4":"4",
			"Numpad5":"5","Numpad6":"6","Numpad7":"7","Numpad8":"8","Numpad9":"9"
		}
		this.sco1=""
		this.sco2=""
		this.sct=""
		this.sca=0
		this.scs=0
		this.sce=1000
		this.st=null
		this.sellcon={"n_unit":0,"n_wlv":1,"n_list":0,"sum":0,"skurootlast":null,"n_before":0,"n_now":0,"n_last":0,}
		this.ip=""
		this.mykey=""
		this.listen_cm=null
		this.listen_target_id=null
		this.bypass_pressInput=[]
		this.mb_type = {"s":"🏠 ผู้ประกอบการ","p":"🧑 ผู้บริโภค"};
	}
	run(){
		this.writeContent()
		this.idn=this.id("n")
		this.idmb=this.id("imember")
		this.dpro="dpro"
		this.dlist=this.id("dlist")
		this.dlast=this.id("dlast")
		this.dsums=this.id("dsums")
		this.spnlist=this.id("spnlist")
		this.iplus=this.id("iplus")
		this.setiplus()
		window.addEventListener("keydown",S.kb)
		window.addEventListener("keyup",S.kb)
		//window.addEventListener('fullscreenchange',S.fullScreenChange)
		
	}
	wsRegis(){
		if(Ws.ws.readyState == 1){
			if(Ws.mykey == ""){
				Ws.mykey =this.ip
				Ws.myoto = "S"
				let data = {"command":"","name":this.ip,"type":"regis","_oto":"S"}
				Ws.send([],data)
			}
		}else{
			setTimeout("S.wsRegis()",50)
		}
	}
	onMessage(data){
		if(data.command == "now"){
			let dt = {}
			dt["_oto"] = "Cd"
			dt["command"] = "now"
			dt["type"] = "message"
			dt["_key"] = data.key
			dt["member"] = this.member.name+" "+ this.member.lastname
			this.sellcon.n_unit = this.getNUnit()
			
			dt["message"] = this.sellcon
			M.l(dt)
			Ws.send([],dt)
		}
		//alert(JSON.stringify(data)+"555+")
	}
	send2cd(data_jsn,command=null){
		let data = {}
		data["type"] = "message"		
		data["_oto"] = "Cd"		
		if(data_jsn["command"] != undefined){
			if(data_jsn["command"] == "success"){
				for(let prop in data_jsn){
					data[prop] = data_jsn[prop]
				}
			}
		}else{
			if(command=="changemember"){
				data["command"] = "changemember"
			}else{
				data["command"] = "sell"
			}
			data_jsn["n_unit"] = this.getNUnit()
			let k = data_jsn["skurootlast"]
			data_jsn["name"] = (this.pd[k] != undefined)?this.pd[k]["name"]:""
			data_jsn["barcode"] = (this.pd[k] != undefined)?this.pd[k]["barcode"]:""
			data_jsn["price"] = (this.pd[k] != undefined)?this.pd[k]["price"]*this.pd[k]["n_wlv"]:""
			data_jsn["unit"] = (this.pd[k] != undefined)?(this.pd[k]["n_wlv"]*1!=1?this.pd[k]["n_wlv"]+" ":"")+""+this.pd[k]["unit_name"]:""
			data["message"] = data_jsn
			data["member"] = this.member.name+" "+ this.member.lastname
		}
		Ws.send([],data)		
	}
	getNUnit(){
		let n_unit = 0
		for (let prop in this.dt){
			n_unit += this.dt[prop].n
		}
		return 	n_unit
	}
	writeContent(){
		let ct=this.ce("div",{"id":"sell"})
			let dtop=this.ce("div",{})
				let dbarl=this.ce("div",{})
					let iamount=this.ce("input",{"id":"n","type":"button","value":"+1","onclick":"M.popup(this,'G.calc(did)','G.calcDefault(\\'n\\')')"})
					let isearch=this.ce("input",{"type":"button","value":"🔍","data-width":340,"onclick":"M.popup(this,'G.search(did,\\'sell\\')')"})
					let ileave=this.ce("input",{"type":"button","data-id":"ileave","value":"🗑","onclick":"M.popup(this,'S.clearSell(did)')"})
					let iplus=this.ce("input",{"id" :"iplus","data-id":"iplus_pobup","type":"button","value":"📍0","onclick":"M.popup(this,'S.showSellList()')"})
					let ibills=this.ce("input",{"type":"button","value":"🧾","onclick":"M.popup(this,'S.billsSell(did)')"})
					let imember0=this.ce("div",{})
						let form=this.ce("form",{"name":"form_sell"})
							let ipmember=this.ce("input",{"id":"ipmember","type":"hidden","value":""})
							let imember=this.ce("div",{"data-select_type":"one","id":"imember","onclick":"M.popup(this,'S.selectMember(did)')"})
							this.end(imember,[this.cn(this.member.name)])
							this.end(form,[ipmember])		
						this.end(form,[imember])
					this.end(imember0,[form])
				this.end(dbarl,[iamount,isearch, ileave,iplus,ibills,imember0])
				let dbarc=this.ce("div",{})
				/*	let dmu=this.ce("div",{})
						this.end(dmu,[this.cn("👥")])
					let dpmu=this.ce("div",{"id":"tmember","onclick":"M.popup(this,'G.me(did)')"})
						this.end(dpmu,[this.cn("บุคตลทั่วไป")])
				this.end(dbarc,[dmu,dpmu])*/
				let dbarr=this.ce("div",{})
					let dme=this.ce("div",{})
						let spme=this.ce("span",{"data-id":"popupidsellme","onclick":"M.popup(this,'G.me(did)')"})
						this.end(spme,[this.cn("👤")])
					this.end(dme,[spme])
				this.end( dbarr,[dme])	
			this.end(dtop,[dbarl,dbarc,dbarr])
			let dpro=this.ce("div",{"id":"dpro"})
				let dprohead=this.ce("div",{})
					let dat=this.ce("div",{})
					this.end(dat,[this.cn("ที่")])
					let dst=this.ce("div",{})
					this.end(dst,[this.cn("🏘️")])
					let dbc=this.ce("div",{})
					this.end(dbc,[this.cn("รหัสแท่ง")])
					let dname=this.ce("div",{})
						let spnlist=this.ce("span",{"id":"spnlist"})
						this.end(spnlist,[this.cn("[0]")])
					this.end(dname,[this.cn("สินค้า "),spnlist])
					let dn=this.ce("div",{})
					this.end(dn,[this.cn("จำนวน")])
					let dun=this.ce("div",{})
					this.end(dun,[this.cn("หน่วย")])
					let dpu=this.ce("div",{})
					this.end(dpu,[this.cn("ราคา/หน่วย")])
					let dsum=this.ce("div",{})
					this.end(dsum,[this.cn("ราคารวม")])
				this.end(dprohead,[dat,dbc,dst,dname,dn,dun,dpu,dsum])
				let dprolist=this.ce("div",{"id":"dlist"})
			this.end(dpro,[dprohead,dprolist])
			let dtotal=this.ce("div",{})
				let dlast=this.ce("div",{"id":"dlast"})
				this.end(dlast,[this.cn("ยังไม่มีสินค้าถูกเลือก")])
				let dtsum=this.ce("div",{})
					let dsums=this.ce("div",{"id":"dsums"})
					this.end(dsums,[this.cn("0.00")])
					let dpay=this.ce("div",{})
						let ipay=this.ce("input",{"type":"button","value":"ชำระเงิน","onclick":"S.smile()"})
					this.end(dpay,[ ipay])
				this.end(dtsum,[dsums,dpay])	
			this.end(dtotal,[dlast,dtsum])
		this.end(ct,[dtop,dpro,dtotal])
		this.end(document.body,[ct])
		this.thmSet("load")
	}
	insertList(bc,n=null){
		if(!this.fl.hasOwnProperty(bc)){
			this.getPdFromServer("barcode",bc)
		}else if(this.idn.value*1!=0){
			n=(n!==null)?n:this.idn.value*1
			this.setdateNow()
			let sku_root=this.fl[bc]
			if(!this.dt.hasOwnProperty(sku_root)){
				let re=this.insertList2Dt(sku_root,n)
				if(re){
					this.sellcon.n_last=n
					this.insertNewList(sku_root)
					this.sums()
					this.setLast(sku_root)
					this.id(this.dpro).scrollTo({ top: this.id(this.dpro).scrollHeight,left:0,behavior: "smooth"})
					
				}
			}else{
				this.sellcon.n_before=this.dt[sku_root]["n"]
				this.sellcon.n_last=n
				let re=this.updateList2Dt(sku_root,n)
				if(re){
					this.updateNewList(sku_root)
					this.sums()
					this.setLast(sku_root)
				}else{
					let ob=this.deleteList2Dt(sku_root)
					this.deleteNewList(sku_root)
					this.sums()
					this.setLastDelete(ob)
				}
			}
			this.setSt("open")
			this.setItemSt(sku_root)
			//alert(this.pd[sku_root].name+"="+n+" sum "+JSON.stringify(this.sums("get")))
		}else{
			this.idn.value="+1"
		}
	}
	setdateNow(){
		if(Object.keys(this.dt).length==0&&this.datenow==null){
			this.datenow=Date.now()
		}
	}
	setItemSt(sku_root){
		if(Object.keys(this.dt).length>0){
			localStorage.setItem(this.datenow, JSON.stringify(this.dt))
			localStorage.setItem(this.datenow+"_last", sku_root)
			localStorage.setItem(this.datenow+"_member",JSON.stringify(this.member))
			this.setItemStList()
		}else{
			this.clearItemStList()
			localStorage.removeItem(this.datenow.toString());
			localStorage.removeItem(this.datenow.toString()+"_last");
			localStorage.removeItem(this.datenow.toString()+"_member");
			this.datenow=null
		}
	}
	setItemStList(){
		let str=localStorage.getItem('selllist')
		if(str==null){
			str=""
		}
		let key=","+this.datenow.toString()+","
		let patt=new RegExp(key)
		let re=patt.test(str)
		if(!re){
			str+=key
			localStorage.setItem("selllist",str)
		}
		this.setiplus(str)
	}
	setiplus(str=null){
		str=(str!=null)?str:localStorage.getItem('selllist')
		if(str!=null){
			let n=str.split(",,")
			if(n[0].trim().length==0){
				n=[]
			}	
			this.iplus.value="📍"+n.length	
		}
	}
	showSellList(){
		let str=localStorage.getItem('selllist')
		str=(str==null)?"":str
		let n=str.split(",,")
		n=(n[0].trim().length==0)?[]:n
		let list=[]
		let ct=this.ce("div",{"class":"selllist"})
			let th=this.ce("div",{})
				let th_at=this.ce("div",{})
				this.end(th_at,[this.cn("#")])
				let th_date=this.ce("div",{})
				this.end(th_date,[this.cn("วันที่")])
				let th_n_list=this.ce("div",{})
				this.end(th_n_list,[this.cn("จ.ร.")])
				let th_act=this.ce("div",{})
				this.end(th_act,[this.cn("กระทำ")])
			this.end(th,[th_at,th_date,th_n_list,th_act])
		this.end(ct,[th])
			let dct=this.ce("div",{})
			for(let i=n.length-1;i>=0;i--){
				let tm=n[i].replace(/,/g,'')
				let n_list_ob=JSON.parse(localStorage.getItem(tm))		
				let n_list=Object.keys(n_list_ob).length
				let d=new Date(Number(tm))
				let date=F.getThaiDate(d)
				let member=this.jsonToObject(localStorage.getItem(tm+"_member"),this.member)
				let ti=(i%2==0)?"i2 i1i2":"i1 i1i2"
				list[i]=this.ce("div",{"class":ti})
					if(this.datenow==Number(tm)){
						list[i].setAttribute("class","selllist_curent")
					}
					list[i+"at"]=this.ce("div",{})
					this.end(list[i+"at"],[this.cn(i+1)])
					list[i+"date"]=this.ce("div",{})
					this.end(list[i+"date"],[this.cn(date)])
					if(member.sku_root!=""){
						let mb=this.ce("p",{})
							let sp=this.ce("span",{})
							this.end(sp,[this.cn(" "+member.sku)])
						this.end(mb,[this.cn(member.name+" "+member.lastname),sp])		
						this.end(list[i+"date"],[mb])				
					}
					
					list[i+"n_list"]=this.ce("div",{})
					this.end(list[i+"n_list"],[this.cn(n_list)])
					list[i+"n_act"]=this.ce("div",{"class":"action"})
						let spdel=this.ce("span",{"onclick":"S.clearSellList(this,null,"+(i+1)+",'"+tm+"')"})
						this.end(spdel,[this.cn("🗑")])
						let spsec=this.ce("span",{"onclick":"S.loadSellList('"+tm+"')"})
						this.end(spsec,[this.cn("⬆")])
					this.end(list[i+"n_act"],[spdel,spsec])
				this.end(list[i],[list[i+"at"],list[i+"date"],list[i+"n_list"],list[i+"n_act"]])
				this.end(dct,[list[i]])
			}
		let dadd=this.ce("div",{"class":"c"})
			let iadd=this.ce("input",{"type":"button","value":"➕ เพิ่มรายการขายใหม่","onclick":"S.newSell()"})
		this.end(dadd,[iadd])
		this.end(ct,[dct,dadd])
		return ct
	}
	loadSellList(time){
		this.loadlocalst=1
		G.loading("sell_loading58x","start")
		let dt=JSON.parse(localStorage.getItem(time))
		let dt_last=localStorage.getItem(time+"_last").trim()
		let dt_member=this.jsonToObject(localStorage.getItem(time+"_member"),this.member)
		let n_last=0
		this.loadSellListClearBefore(time)
		let timeout=0
		G.loading("sell_loading58x","sethas",Object.keys(dt).length)
		for (let k in dt) {
			if(k==dt_last){
				n_last=Number(dt[k]["last"])
			}
			let d={"sku_root":k}
			//this.idn.value="+"+dt[k]["n"]
			setTimeout(S.productSelect, timeout+=200,d,Number(dt[k]["n"]))
		}
		setTimeout(S.setLastWhenLoadSellList,timeout+=200,dt_last,n_last,9)
		this.setMemberFromSt(dt_member.sku_root)
	}
	setLastWhenLoadSellList(dt_last,n_last,n){
		let sku_root=dt_last
		let d=M.id(sku_root)
		if(d==null&&n>0){
			setTimeout(S.setLastWhenLoadSellList,10,dt_last,n_last,n-1)
		}else{
			S.dt[dt_last]["last"]=n_last
			S.setItemSt(dt_last)
			let at=d.childNodes[0].innerHTML
			let name=S.pd[sku_root]["name"]
			let last=S.dt[sku_root]["last"]
			let n=S.dt[sku_root]["n"]
			if(last>0){
				this.dlast.innerHTML=`**ล่าสุด:#${at}  ${name} เพิ่ม ${last} กลายเป็น ${n}`
			}else{
				this.dlast.innerHTML=`**ล่าสุด:#${at}  ${name} ลด ${last} กลายเป็น ${n}`
			}
		}
	}
	loadSellListClearBefore(time){
		this.dt={}
		this.rmc_all(this.dlist)
		this.dlast.innerHTML="ยังไม่มีสินค้าถูกเลือก"
		this.dsums.innerHTML="0.00"
		this.spnlist.innerHTML="[0]"
		this.datenow=time
		this.popupClear('iplus_pobup')
	}
	clearItemStList(){
		if(this.datenow!=null){
			let str=localStorage.getItem('selllist')
			let key=","+this.datenow.toString()+","
			str=str.replace(key,"")
			localStorage.setItem("selllist",str)
		}
	}
	deleteList2Dt(sku_root){
		let re=Object.assign({},this.dt[sku_root])
		let d=this.id(sku_root)
		let at=d.childNodes[0].innerHTML
		re["at"]=at
		re["sku_root"]=sku_root
		delete this.dt[sku_root]
		return re
	}
	insertList2Dt(sku_root,n){
		let re=true
		//let n=this.idn.value*1
		
		if(n>0){
			this.dt[sku_root]={"n":n,"last":Number(n),"n_wlv":this.pd[sku_root]["n_wlv"]}		
		}else{
			re=false
		}
		this.idn.value="+1"
		return re
	}
	updateList2Dt(sku_root,n){
		let re=true
		//let n=this.idn.value*1
		let nn=this.dt[sku_root]["n"]+n*1
		if(nn>0){
			this.dt[sku_root]["n"]=nn
			this.dt[sku_root]["last"]=n*1
		}else{
			re=false
		}
		this.idn.value="+1"
		return re
	}
	insertNewList(sku_root){
		let n_at=this.dlist.childNodes.length
		let at=1
		if(n_at>=1){
			at=this.dlist.lastChild.childNodes[0].innerHTML*1+1//Object.keys(this.dt).length	//this.dpro.childNodes.length+1
		}
		
		let name=this.pd[sku_root]["name"]
		let per=this.nb(this.pd[sku_root]["price"])
		let bc=(this.pd[sku_root]["barcode"]!=null)?this.pd[sku_root]["barcode"]:""
		let n=this.dt[sku_root]["n"]
		let n_wlv=""
		if(this.pd[sku_root]["s_type"]!="p"){
			n_wlv="×"+this.dt[sku_root]["n_wlv"]
		}
		let un=this.pd[sku_root]["unit_name"]
		let sum=this.nb(n*this.pd[sku_root]["n_wlv"]*this.pd[sku_root]["price"])
		let dlist=this.ce("div",{"id":sku_root})
			let dat=this.ce("div",{})
			this.end(dat,[this.cn(at)])
			let dst=this.ce("div",{})
			this.end(dst,[this.cn("")])
			let dbc=this.ce("div",{})
			this.end(dbc,[this.cn(bc)])
			let dname=this.ce("div",{})
				let wlvt=""
				if(this.pd[sku_root]["s_type"]!="p"){
					wlvt=" [ชั่งตวงวัด]"
				}
				let dnamenbc=this.ce("div",{})
					let dnamest=this.ce("span",{})
					let dnamebc=this.ce("span",{})
					this.end(dnamebc,[this.cn(bc)])	
						let dnameprice=this.ce("span",{})
						this.end(dnameprice,[this.cn(" ฿"+per)])	
						let wlv_print=this.ce("span",{})
						if(this.pd[sku_root]["s_type"]!="p"){
							wlv_print.setAttribute("style","cursor:pointer;")
							wlv_print.setAttribute("onclick","let y=confirm('คุณต้องการพิมพิ์ฉลาก ติดสินค้า');if(y){S.label('"+sku_root+"',null,"+this.dt[sku_root]["n_wlv"]+");}")
							wlv_print.setAttribute("title","กดเพื่อพิมพิ์ฉลากติดสินค้า")
							this.end(wlv_print,[this.cn(" 🖨️ "+wlvt)])
						}				
				this.end(dnamenbc,[ dnamest,this.cn(" " ), dnamebc,dnameprice])
			this.end(dname,[this.cn(name),dnamenbc,wlv_print])			
			let dn=this.ce("div",{"class":"set_n","onclick":"S.setN(this)"})
				let gn_group=this.ce("div",{})
					let dnn=this.ce("div",{})
					this.end(dnn,[this.cn(n+""+n_wlv)])
					let dnun=this.ce("div",{})
					this.end(dnun,[this.cn(un)])
				this.end(gn_group,[dnn,dnun])	
			this.end(dn,[gn_group])
			let dun=this.ce("div",{})
			this.end(dun,[this.cn(un)])
			let dper=this.ce("div",{})
			this.end(dper,[this.cn(per)])
			let dsum=this.ce("div",{})
			this.end(dsum,[this.cn(sum)])
		this.end(dlist,[dat,dbc,dst,dname,dn,dun,dper,dsum])
		this.end(this.dlist,[dlist])
	}
	updateNewList(sku_root){
		let n=this.dt[sku_root]["n"]
		let n_wlv=""
		if(this.pd[sku_root]["s_type"]!="p"){
			n_wlv="×"+this.dt[sku_root]["n_wlv"]
		}
		let sum=this.nb(n*this.pd[sku_root]["n_wlv"]*this.pd[sku_root]["price"])
		let d=this.id(sku_root)
		this.id(this.dpro).scrollTo({top:d.offsetTop-57,left:0,behavior: "smooth"});
		d.childNodes[4].childNodes[0].childNodes[0].innerHTML=n+""+n_wlv
		d.childNodes[7].innerHTML=sum
		if(d.className=="updater"){
			d.className="updater2"
		}else if(d.className=="updater2"){
			d.className="updater"
		}else{
			d.className="updater"
		}
	}
	deleteNewList(sku_root){
		let d=this.id(sku_root)
		d.parentNode.removeChild(d);
	}
	setLast(sku_root){	//--สินค้าล่าสุด การเปลี่ยนแปลง สินค้ายังอยู่ในรายการขาย
		let d=this.id(sku_root)
		let at=d.childNodes[0].innerHTML
		let name=this.pd[sku_root]["name"]
		let last=this.dt[sku_root]["last"]
		let n=this.dt[sku_root]["n"]
		if(last>0){
			this.dlast.innerHTML=`ล่าสุด:#${at}  ${name} เพิ่ม ${last} กลายเป็น ${n}`
		}else{
			this.dlast.innerHTML=`ล่าสุด:#${at}  ${name} ลด ${last} กลายเป็น ${n}`
		}
		
		this.sellcon.skurootlast=sku_root
		this.sellcon.sum=this.dsums.innerHTML
		this.sellcon.n_now=n
		this.send2cd(this.sellcon)
	}
	setLastDelete(ob){	//--สินค้าล่าสุด การเปลี่ยนแปลง สินค้าไม่อยู่ในรายการแล้ว
		let at=ob.at
		let name=this.pd[ob.sku_root]["name"]
		this.dlast.innerHTML=`ล่าสุด:ลบ #${at}  ${name}`
		this.sellcon.skurootlast=ob.sku_root
		this.sellcon.n_now=0
		this.sellcon.sum=this.dsums.innerHTML
		this.send2cd(this.sellcon)
	}
	sums(type="set"){
		let sums=0;
		let n_list=0
		for (let k in this.dt) {
			n_list+=1
			sums+=this.pd[k]["price"]*this.dt[k]["n"]*this.dt[k]["n_wlv"]
		}
		this.sellcon.n_list=n_list
		if(type=="set"){
			this.dsums.innerHTML=this.nb(sums)
			this.spnlist.innerHTML="["+n_list+"]"
		}else if(type=="get"){
			return {"n_list":n_list,"sums":sums}
		}
	}
	setFl2Pd2Insert(re,n=null){
		let bc=re["barcode"]
		if(re["barcode"]==null){
			bc="__"+re["sku_root"]
		}
		let sku_root=re["sku_root"]
		this.fl[bc]=sku_root
		this.pd[sku_root]=re
		delete this.pd[sku_root]["result"]
		delete this.pd[sku_root]["message_error"]
		//M.l(bc+"***"+n)
		this.insertList(bc,n)
	}
	getPdFromServer(field,value,n=null){
		let nt=(n==null)?"":n
		let formData = new FormData()
		formData.append("a","sell")			
		formData.append("submith","clicksubmit")		
		formData.append("b","get_bc")		
		formData.append("field",field)		
		formData.append("bc",value)		
		formData.append("n",nt)		
		M.fec("POST","",S.getPdFromServerResult,S.getPdFromServerError,null,formData)
	}
	getPdFromServerResult(re,form,bt){
		if(S.loadlocalst==1){G.loading("sell_loading58x","setget",0,1,"S.loadlocalst")}
		if(re["result"]){
			let n=form.get("n")
			let nt=(n.trim().length==0)?null:Number(n)
			if(re["s_type"]=="p"){
				re["n_wlv"]=1
				S.setFl2Pd2Insert(re,nt)
			}else{
				if(re["bc_type"]=="bc_wlv"){
					re["sku_root"]=re["sku_root"]+"_"+re["barcode"]
					re["n_wlv"]=S.get_n_wlv(re["barcode"])
					S.setFl2Pd2Insert(re,nt)	
				}else if(re["bc_type"]=="bc_wlv_null"){
					re["barcode"]=null
					re["sku_root"]=re["sku_root"]+"_"+re["barcode_wlv_no_bc"]
					re["n_wlv"]=S.get_n_wlv(re["barcode_wlv_no_bc"])
					S.setFl2Pd2Insert(re,nt)	
				}else{
					S.pressInput(re,nt)
				}
			}
		}else{
			S.getPdFromServerError(re,form,bt)
		}
	}
	get_n_wlv(barcode_wlv){
		let length_bc=barcode_wlv.substring(barcode_wlv.length-2,barcode_wlv.length)*1
		let length_int_n_wlv=barcode_wlv.substring(barcode_wlv.length-4,barcode_wlv.length)
		length_int_n_wlv=length_int_n_wlv.substring(0,2)
		let n_wlv=barcode_wlv.substring(length_bc,barcode_wlv.length-4)
		let b_wlv_int=n_wlv.substring(0,length_int_n_wlv)
		let b_wlv_float=n_wlv.substring(length_int_n_wlv,n_wlv.length)
		let float=(b_wlv_int+"."+b_wlv_float)*1
		return float
	}
	pressInput(re,nt){
		this.listen_cm="pressInput"
		let tt={"w":"น้ำหนัก","l":"ความยาว","v":"ปริมาตร"}
		let bt=["7","8","9","4","5","6","1","2","3","0",".","⬅"]
		let rid = this.rid()
		let rid_listen = this.rid()
		this.listen_target_id=rid_listen
		let ct = this.ce("div",{"class":"cal_d_press"})
			let p=this.ce("p",{})
			this.end(p,[this.cn(re["name"]+" "+re["unit_name"]+"ละ ฿"+this.nb(re["price"]))])
			let ip=this.ce("input",{"type":"text","id":rid_listen,"data-unit_name":re["unit_name"],"value":" "+re["unit_name"]})
			let d=this.ce("div",{})
				for(let i=0;i<bt.length;i++){
					let t=bt[i]
					let dn=this.ce("button",{"type":"button","onclick":"S.ipFloat(event,'"+t+"')"})
					this.end(dn,[this.cn(bt[i])])
					this.end(d,[dn])	
				}
		this.end(ct,[p,ip,d])		
		this.bypass_pressInput=[re,nt]		
		let bts = [
			{"value":"ยกเลิก","onclick":"S.listen_cm=null,M.dialogClose('"+rid+"')"},
			{"value":"พิมพิ์","onclick":"S.label('"+re["sku_root"]+"','"+rid_listen+"')"},
			{"value":"ตกลง","id":"pressbuttoninputok","onclick":"S.pressInputOK('"+rid+"','"+rid_listen+"')"}
		]
		M.dialog({"rid":rid,"display":1,"ct":ct,"bts":bts,"title":"โปรดกรอก "+tt[re["s_type"]],"width":"250"})
		ip.readOnly = true
		ip.focus()
	}
	label(sku_root,id_listen,n_wlv_float=1){
		let n_wlv=n_wlv_float
		if(this.id(id_listen)!=null){
			n_wlv=this.id(id_listen).value.split(" ")[0]*1
		}else{
			sku_root=sku_root.split("_")[0]
		}
		if(n_wlv>0){
			let dt={"data":{"a":"bill58","b":"labelWLVPrint","sku_root":sku_root,"n_wlv":n_wlv},"result":S.labelResult,"error":S.labelError}
			this.setFec(dt)
		}else{
			alert("รูปแบบ น้ำหนักไม่ถูกต้อง\nหรือน้ำหรักเป็น 0")
		}
	}
	labelResult(re,form,bt){
		if(!re["result"]){
			S.labelError(re,form,bt)
		}else{
			
		}
	}
	labelError(re,form,bt){
		alert("❌ พิมพ์ป้ายฉลาก ไม่สำเร็จ\n\n"+re["message_error"])
	}
	pressInputOK(id_dialog,id_listen){
		let re=this.bypass_pressInput[0]
		let nt=parseInt(this.idn.value)
		let n_wlv=this.id(id_listen).value.split(" ")[0]*1
		if(n_wlv>0){
			M.dialogClose(id_dialog)
			S.listen_cm=null
			let bc=this.doBarcode(re["barcode"],n_wlv)
			re["sku_root"]=re["sku_root"]+"_"+bc
			if(re["barcode"]!=null){
				re["barcode"]=bc
			}
			re["name"]=re["name"]+" "+n_wlv.toString()+" "+re["unit_name"]
			re["n_wlv"]=n_wlv
			this.setFl2Pd2Insert(re,nt)	
			this.bypass_pressInput=[]
		}
	}
	doBarcode(barcode="",float){
		if(barcode==null){
			barcode=""
		}
		let f=""
		let q=float.toString().split(".")
		if(q.length==2){
			f=barcode+""+q[0]+""+q[1]+""+(q[0].length>9?q[0].length:"0"+q[0].length)+""+(barcode.length>9?barcode.length:"0"+barcode.length)
			if(f.length%2==1){
				f=barcode+"0"+q[0]+""+q[1]+""+(q[0].length+1>9?q[0].length+1:"0"+(q[0].length+1))+""+(barcode.length>9?barcode.length:"0"+barcode.length)
			}	
		}else{
			f=barcode+""+q[0]+""+(q[0].length>9?q[0].length:"0"+q[0].length)+""+(barcode.length>9?barcode.length:"0"+barcode.length)
			if(f.length%2==1){
				f=barcode+"0"+q[0]+""+(q[0].length+1>9?q[0].length+1:"0"+(q[0].length+1))+""+(barcode.length>9?barcode.length:"0"+barcode.length)
			}			
		}

		return f
	}
	getPdFromServerError(re,form,bt){
		if(re["message_error"]!=undefined){
			alert(re["message_error"])
		}else{
			alert("เกิดข้อผิดพลาด บางอย่าง")
		}
	}
	kb(event){
		let act=document.activeElement.tagName
		if(act=="BODY" && S.listen_cm == null){
			let el=event.target		
			let key=event.code
			if(event.type=="keyup"){
				let skey=(key!=S.kl)?S.kl+""+key:S.kl
				if(S.k.hasOwnProperty(skey)){
					S.sco1=S.sco2
					S.sco2=S.k[skey]
					let tn=Date.now()
					let dif=tn-S.scs
					if(dif<200){
						S.sca+=dif
						if(S.sct==""){
							S.sct=S.sco1+""+S.sco2
						}else{
							S.sct+=S.sco2
						}
					}else{
						S.sct=""
						S.sca=0
						S.scs=0
					}
					S.scs=tn
					S.bc+=S.k[skey]
					S.kl=""
				}else{
					if(skey=="NumpadEnter"||skey=="Enter"){	
						let av=(S.sca/S.sct.length-1) //--NaN
						//M.l("avg="+av+">>"+S.sct)
						if(av<30&&S.sct.length>=4){
							S.insertList(S.sct)
						}else{
							let bc=S.bc.split("✂️")
							S.comand(bc[bc.length-1])
						}
						S.bc="✂️"
						S.kl=""
					}else if(skey=="ShiftLeft"){
						S.kl="ShiftLeft"
					}else{
						S.kl=""			
					}
				}
			}
		}else if(event.type=="keyup"&&S.listen_cm=="pressInput"){
			S.ipFloat(event,"  ")
		}
	}
	ipFloat(event,t=""){
		let ip=this.id(this.listen_target_id)
		let ep=ip.value.split(" ")		
		if(event.type=="keyup"){
			let skey = event.code
			if(this.float.hasOwnProperty(skey)){
				t=this.float[skey]
			}else if(skey=="NumpadEnter"||skey=="Enter"){
				this.id("pressbuttoninputok").click()
			}
		}else if(event.type=="click"){
			if(t.length!=1||"0123456789.⬅".indexOf(t)==-1){
				t="  "
			}else if(t=="⬅"){
				t=""
			}
			event.target.blur()
		}
		if(t=="."&&ep[0].indexOf(".")>=0){
						
		}else if(t==""){
			ip.value=ep[0].substring(0,ep[0].length-1)+" "+ep[1]
		}else{
			let float = ep[0]+""+t.trim()
			if(ep[0].indexOf(".")<0&&t!="."){
				float=(ep[0]+""+t.trim())*1
			}else if(ep[0].length==0&&t=="."){
				float="0."
			}
			ip.value=float+" "+ep[1]
		}
	}
	comand(cm){
		let c=cm.substring(0,1)
		let t=cm.substring(1)
		
		if(c=="*"){
			let v=t*1
			if(v!=0&&!isNaN(v)&&t.length>0){
				if(v>0){
					this.idn.value="+"+v
				}else{
					this.idn.value=v
				}
			}else if((/^\/[0-9]+(\+|\-)[0-9]+$/g).test(t)){
				let q=t.split("/")
				let d1=q[1].split("+")
				let d2=q[1].split("-")
				let e=(d1.length==2)?d1:d2
				let m=(d1.length==2)?1:-1
				let o=this.dlist
				for(let i=0;i<o.childNodes.length;i++){
					let tid=o.childNodes[i].childNodes[0].textContent
					if(tid==e[0]){
						let bc=""+o.childNodes[i].childNodes[1].textContent+""
						if(bc.trim().length==0){
							bc="__"+o.childNodes[i].id
						}
						this.insertList(bc,e[1]*m)
						break;
					}
				}
			}else if(t=="..."){
				let y=confirm("คุณต้องการล้างรายการที่กำลังทำอยู่ ?")
				if(y){
					this.clearSellOk()
				}
			}else if(t.length==0){
				this.smile()
			}
		}
	}
	productSelect(d,n=null){
		if(d.sku_root!=undefined){
			let sku_root=d.sku_root
			let bc=null
			if(!S.pd.hasOwnProperty(sku_root)){
				S.getPdFromServer("sku_root",sku_root,n)
			}else{
				if(S.pd[sku_root].barcode!=null){
					bc=S.pd[sku_root].barcode
				}else{
					bc="__"+sku_root
				}
				if(S.loadlocalst==1){G.loading("sell_loading58x","setget",0,1,"S.loadlocalst")}
				//M.l("**"+bc+"="+n)
				if(S.pd[sku_root].s_type=="p"){
					S.insertList(bc,n)
				}else if(S.pd.hasOwnProperty(sku_root)){
					let re=S.pd[sku_root]
					let nt=n
					S.setFl2Pd2Insert(re,nt)
				}else{
					S.pressInput(S.pd[sku_root],0)
				}
			}
		}
	}
	clearSell(did){
		let ct=this.ce("div",{})
			let p=this.ce("p",{"class":"s14"})
			this.end(p,[this.cn("คุณต้องการล้าง "+Object.keys(this.dt).length+" รายการ ทั้งหมดนี้ แล้วเริ่มขายใหม่")])
			let db=this.ce("div",{"class":"c"})
				let bthome=this.ce("input",{"type":"button","value":"ยกเลิก","onclick":"M.popupClear('ileave')"})
				let btout=this.ce("input",{"type":"button","value":"ยืนยัน","onclick":"S.clearSellOk()"})
			this.end(db,[bthome,btout])
		this.end(ct,[p,db])
		return ct
	}
	clearSellOk(){
		this.setSt("close")
		this.dt={}
		this.rmc_all(this.dlist)
		this.dlast.innerHTML="ยังไม่มีสินค้าถูกเลือก"
		this.dsums.innerHTML="0.00"
		this.spnlist.innerHTML="[0]"
		this.popupClear('ileave')
		if(this.datenow!=null){
			localStorage.removeItem(this.datenow.toString());
			localStorage.removeItem(this.datenow.toString()+"_last");
		}
		this.clearItemStList()
		this.setiplus()
		this.datenow=null
		this.clearSellCon()
		this.send2cd(this.sellcon)
	}
	clearSellCon(){
		this.sellcon["sn_unit"] = 0
		this.sellcon["n_list"] = 0
		this.sellcon["n_wlv"] = 1
		this.sellcon["skurootlast"] = ""
		this.sellcon["sum"] = 0
		this.sellcon["n_before"] = 0
		this.sellcon["n_now"] = 0
		this.sellcon["n_last"] = 0		
	}
	clearSellList(did,confem=null,at,tm){
		if(confem==null){
			let y=confirm('คุณต้องการลบ รายการที่ '+at)
			if(y){
				let n=did.parentNode.parentNode
				n.parentNode.removeChild(n);
				if(this.datenow==tm){
					this.clearSellOk()
				}else{
					localStorage.removeItem(tm);
					localStorage.removeItem(tm+"_last");
					let str=localStorage.getItem('selllist')
					let key=","+tm+","
					str=str.replace(key,"")
					localStorage.setItem("selllist",str)
					this.setiplus()
				}
			}
		}
	}
	newSell(){
		this.dt={}
		this.rmc_all(this.dlist)
		this.dlast.innerHTML="ยังไม่มีสินค้าถูกเลือก"
		this.dsums.innerHTML="0.00"
		this.spnlist.innerHTML="[0]"
		this.popupClear('iplus_pobup')
		this.setiplus()
		this.datenow=null
		this.setMember0()
	}
	smile(error=""){
		let t=this.sums("get")
		if(t.n_list>0){
			let c=prompt(t.n_list+" รายการ รวมเป็นเงิน "+this.nb(t.sums)+" บาท\n"+error+"\nกรุณาใส่จำนวนเงิน ที่รับมา")
			if(c!=null){
				if(!F.isNumber(c)){
						let error="\n❌ \""+c+"\"  ไม่อยู่ในรูปแบบตัวเลข โปรดลองใหม่"
				this.smile(error)
				}else if(Number(c)-t.sums<0){
					let error="\n❌ "+c+"  น้อยกว่ายอดที่ต้องชำระ"
					this.smile(error)
				}else{
					this.success(t.sums,Number(c))
				}
			}
		}
	}
	success(sum,get){
		let pd=JSON.stringify(this.dt)
		let formData = new FormData()
		formData.append("a","sell")			
		formData.append("submith","clicksubmit")		
		formData.append("b","smile")		
		formData.append("sum",sum)	
		formData.append("get",get)	
		formData.append("pd",pd)		
		M.fec("POST","",S.successResult,S.successError,null,formData)
	}
	successResult(re,form,bt){
		if(re["result"]){
			let id=re.billid
			let s=form.get("sum")*1
			let g=form.get("get")*1
			let formData = new FormData()
			formData.append("submith","clicksubmit")		
			formData.append("a","bill58")	
			formData.append("b","print")	
			formData.append("sku",id)	
			M.fec("POST","",S.printResult,S.printError,null,formData)
			let data = {"command":"success","get":M.nb(g),"change":M.nb(g-s)}
			S.send2cd(data)
			
			alert("✅ สำเร็จ บันทึกรายการเรียบร้อย\nรับมา  \t "+M.nb(g)+"\tบาท\nเงินทอน\t "+M.nb(g-s)+"\tบาท")
			S.clearSellOk()
		}else{
			S.successError(re,form,bt)
		}
	}
	printResult(re,form,bt){
		if(!re["result"]){
			S.printError(re,form,bt)
		}
	}
	printError(re,form,bt){
		alert("❌ พิมพ์ใบเร็จ ไม่สำเร็จ\n\n"+re["message_error"])
	}
	successError(re,form,bt){
		//M.l(re);
		alert("❌ ไม่สำเร็จ\n\n"+re["message_error"])
	}
	billsSell(did,fol=""){
		let idf=this.rid()
		let ct=this.ce("div",{"class":""})
			let c=this.ce("iframe",{"id":idf,"src":"?a=bills&b=select&c=sell&for="+fol,"width":"100%","height":"300","allowtransparency":"true"})
		this.end(ct,[c])		
		return ct
	}
	thm(did){
		let id=this.id("sell")
		let cl=did.value
		this.thmSet("set",cl)
	}
	thmSet(type="load",thms="black"){
		let ob=this.id("sell")
		let th=ob.childNodes[1].childNodes[0]
		let bt=ob.childNodes[2].childNodes[1]
		let sm=ob.childNodes[2].childNodes[1].childNodes[0]
		let bs=ob.childNodes[2].childNodes[1].childNodes[1].childNodes[0]
		let thm=thms
		let cls = ["thm_bg_black", "thm_bg_gray","thm_bg_white"];
		let cls_th = ["thm_bg_th_black", "thm_bg_th_gray","thm_bg_th_white"];
		let cls_bt = ["thm_bt_black", "thm_bt_gray","thm_bt_white"];
		if(type=="load"){
			let str=localStorage.getItem('thm')
			if(str==null){
				localStorage.setItem("thm",thm)
			}else if(str!="black"&&str!="gray"&&str!="white"){
				localStorage.setItem("thm",thm)
			}else if(str=="black"||str=="gray"||str=="white"){
				thm=str
			}
		}else if(type=="set"){
			localStorage.setItem("thm",thm)
		}
		ob.classList.remove(...cls);
		ob.classList.add("thm_bg_"+thm)
		th.classList.remove(...cls_th);
		th.classList.add("thm_bg_th_"+thm)
		bt.classList.remove(...cls_bt);
		bt.classList.add("thm_bt_"+thm)
	}
	setSt(type){
		if(type=="open"&&this.loadlocalst==0){
			if(this.st!=null){
				this.st.close();
				this.st=null
			}
			let q=Object.keys(this.dt)
			let k=[]
			for(let i=0;i<q.length;i++){
				let w=q[i].split("_")
				if(k.indexOf(w[0]) == -1){
					k.push(w[0])
				}
			}
			//M.l(k)
			k=JSON.stringify(k)
			this.st=new EventSource("?a=sell&b=st&k="+k);
			this.st.onmessage=S.getSt			
			this.st.onerror=S.getStE
		}else if(type=="close"){
			if(this.st!=null){
				this.st.close();
				this.st=null
			}
		}
	}
	getSt(e){
		let st=JSON.parse(e.data)
		if(S.loadlocalst==1){return false}
		let n=0;
		for(let property in S.dt) {
			let q=property.split("_")
			if(st.hasOwnProperty(property)){
				if(M.id(property)!=undefined){
					M.id(property).childNodes[2].innerHTML=M.nb(st[property],0)
					M.id(property).childNodes[3].childNodes[1].childNodes[0].innerHTML=M.nb(st[property],0)
				}
			}else if(q.length==2){
				if(M.id(property)!=undefined&&st[q[0]]!=undefined){
					M.id(property).childNodes[2].innerHTML=st[q[0]]*1
					M.id(property).childNodes[3].childNodes[1].childNodes[0].innerHTML=st[q[0]]*1
				}else{
					M.id(property).childNodes[2].innerHTML=0
					M.id(property).childNodes[3].childNodes[1].childNodes[0].innerHTML=0
				}
			}else{
				if(M.id(property)!=undefined){
					M.id(property).childNodes[2].innerHTML=0
					M.id(property).childNodes[3].childNodes[1].childNodes[0].innerHTML=0
				}
			}
			n+=1
		}
		if(n==0){
			S.setSt("close")
		}
	}
	setN(did,error=""){
		let at=did.parentNode.childNodes[0].textContent
		let y=prompt(error+"เพิ่ม หรือ ลดจำนวน\nเช่น +3 หรือ -1")
		if(y!=null){
			 if((/^(\-|\+)?[0-9]+$/g).test(y)){
				let d1=y.split("+")
				let d2=y.split("-")
				let e=y
				if(d2.length==2){
					e=d2[1]
				}else if(d1.length==2){
					e=d1[1]
				}
				let k=(d2.length==2)?"-":"+"
				
				 let cm="*/"+at+""+k+""+e
				 this.comand(cm)
			 }else{
				 let error="\""+y+"\" ไม่อยู่ในรูปแบบ\n"
				 S.setN(did,error)
			 }
		}		
	}
	printCm(type=null){
		let t=`
     รูปแบบ คำสั่งทางแป้นพิมพ์     
หน้าขายสินค้านี้ดักจับเหตุการณ์ ของแป้น
พิมพ์ และเครื่องสแกนบาร์โค๊ด
เริ่มต้นคำสั่งด้วยเครื่องหมาย *
สินสุดคำสั่งด้วยการกดปุ่ม  Enter
-----------------------------
ตัวอย่าง 
หมายเหตุ E แทนด้วยการกดปุ่ม Enter
-----------------------------
สแกนบาร์โค๊ดขายทเดียว 20 ชิ้น 
 กด *+20E  แล้วสแกนบาร์โค๊ดที่สินต้า
-----------------------------
เพิ่มสินค้าในรายการที่ 3 จำนวน 5 ชิ้น
 กด */3+5E 
----------------------------- 
ลดสินค้าในรายการที่ 7 จำนวน 2 ชิ้น
 กด */7-2E 
----------------------------- 
ลบรายการสินค้าที่ 4 ออก
 กด */4-xE
  x คือ จำนวนที่มากกว่าหรือเท่ากับ 
 จำนวนที่มีอยู่ในรายการ
 ----------------------------- 
 ล้างรายการที่กำลังขายอยู่
  กด *...E
----------------------------- 
ชำระเงิน
  กด *E
----------------------------- 
  
`
		if(type=="alert"){
			alert(t)
		}else{
			let dt={"data":{"a":"bill58","b":"textPrint","text":t},"result":S.cmResult,"error":S.cmError}
			this.setFec(dt)
		}
	}	
	cmResult(re,form,bt){
		if(re["result"]){

		}else{
			S.cmError(re,form,bt)
		}
	}
	cmError(re,form,bt){
		alert("❌ เกิดข้อผิดพลาด "+re["message_error"])
	}
	selectMember(did){
		let ct=this.ce("div",{})
			let p=this.ce("div",{"class":"popup_member s14"})
			let tx=""
			let d0=this.ce("div",{})
				let img=null
			let d1=this.ce("div",{})
			if(this.member.sku_root==""){
				img=this.ce("img",{"class":"viewimage","src":"img/gallery/128x128_"+this.member.icon,"onclick":"G.view(this,0)"})
				tx="ลูกค้า"+this.member.name+" ไม่ใช่สมาชิก"
				this.end(d1,[this.cn(tx)])
			}else{
					img=this.ce("img",{"class":"viewimage","src":"img/gallery/128x128_"+this.member.icon,"onclick":"G.view(this)"})
					let d2=this.ce("div",{})
					this.end(d2,[this.cn("ชื่อ-นามสกุล:"+this.member.name+" "+this.member.lastname)])
						
					let d4=this.ce("div",{})
					this.end(d4,[this.cn("รหัส:"+this.member.sku)])
		
					let d6=this.ce("div",{})
					this.end(d6,[this.cn("ประเภท:"+this.mb_type[this.member.mb_type])])
				this.end(d1,[d2,d4,d6])	
				
			}
			this.end(d0,[img])
			this.end(p,[d0,d1])
			let q=this.ce("a",{"onclick":"M.popupClear();S.setMember0(this)"})
			this.end(q,[this.cn("เลือกเป็นบุคลทั่วไปไม่ใช่สมาชิก")])
			let db=this.ce("div",{"class":"c"})
				let bthome=this.ce("input",{"type":"button","value":"ลงทะเบียนใหม่","onclick":"S.regisMember(this)"})
				let btout=this.ce("input",{"type":"button","value":"เลือกสมาชิก","onclick":"M.popupClear();Fsl.ctAddPartner('member','S','form_sell',null,'imember'		,'ipmember','new',1,null,null,		0,'name','',0)"})
			this.end(db,[bthome,btout])
		this.end(ct,[p])
		if(this.member.sku_root!=""){
			this.end(ct,[q])
		}
		this.end(ct,[db])
		return ct
	}
	setMember0(){
		this.member=Object.assign({},this.member_default)
		let t=this.id("imember")
		this.rmc_all(t)
		let div=this.ce("div",{"class":"form_select_div_one_select"})
			let div_img=this.ce("div",{"class":"img32"})
			let img=this.ce("img",{"src":"img/gallery/32x32_"+this.member["icon"],"alt":this.member["name"],"onerror":"this.src='img/pos/32x32_null.png'"})	
			this.end(div_img,[img])	
			let span=this.ce("div",{})
			this.end(span,[this.cn(this.member["name"]+" "+this.member["lastname"])])
		this.end(div,[div_img,span])	
		this.end(t,[div])
		this.setStMember()
	}
	selectPartnerOK(display_id,partner,partner_full_data){
		let t=this.id(display_id)
		for (let prop in partner[display_id]) {
			this.member=partner_full_data[display_id][prop]
			let div=this.ce("div",{"class":"form_select_div_one_select"})
				let div_img=this.ce("div",{"class":"img32"})
					let img=this.ce("img",{"src":"img/gallery/32x32_"+partner_full_data[display_id][prop]["icon"],"alt":partner_full_data[display_id][prop]["name"],"onerror":"this.src='img/pos/32x32_null.png'"})	
				this.end(div_img,[img])	
				let span=this.ce("div",{})
				this.end(span,[this.cn(partner_full_data[display_id][prop]["name"]+" "+partner_full_data[display_id][prop]["lastname"])])
			this.end(div,[div_img,span])	
			this.end(t,[div])
		}
		this.setStMember()
		this.send2cd({},"changemember")
	}
	setStMember(){
		if(Object.keys(this.dt).length>0){
			localStorage.setItem(this.datenow+"_member",JSON.stringify(this.member))
		}
	}
	regisMember(did){
		let rid = this.rid()
		let ct=this.ce("div",{})
			let ifm=this.ce("iframe",{"src":"?a=member&b=regis&iframe=1&dialog_id="+rid,"width":"100%","height":"400"})
		this.end(ct,[ifm])
		let bts=[]
		M.dialog({"rid":rid,"display":1,"bts":bts,"ct":ct,"title":"ลงทะเบียนสมาชิก","width":"400","bts0":1,"ofc":0})
	}
	setMemberFromSt(sku_root){
		let dt={"data":{"a":"member","b":"getmember1","sku_root":sku_root},"result":S.getMember1Result,"error":S.getMember1Error}
		this.setFec(dt)
	}
	getMember1Result(re,form,bt){
		if(re["result"]){
			if(re["data"].hasOwnProperty("name")){
				S.member=re["data"]
				Fsl.partner["imember"]={}
				Fsl.partner["imember"][re["data"]["sku_root"]]=re["data"]
				M.l(re["data"])
				if(!Fsl.partner_full_data.hasOwnProperty("imember")){
					Fsl.partner_full_data["imember"]={}
				}
				Fsl.partner_full_data["imember"][re["data"]["sku_root"]]=re["data"]
				Fsl.setEmptyTable("imember")
				S.selectPartnerOK("imember",Fsl.partner,Fsl.partner_full_data)
			}
		}else{
			S.member=Object.assign({},S.member_default)
			S.setMember0()
		}
	}
	getMember1Error(re,form,bt){
		alert(555555555555555)
	}
}
