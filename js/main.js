"use strict"
function tran(ob,mt,dt){
	let js=JSON.parse(dt)
	return eval(ob+"."+mt+"(js)")
}
class main{
	constructor(){
		this.b=document.body
		this.film=document.getElementById("film")
		this.zdl=25
		this.z=1
		this.CookieName={"ud":null}
		this.cookieData={}
		this.hostbase=window.location.protocol+"//"+window.location.hostname+""+((window.location.port==80)?"":":"+window.location.port)+""+window.location.pathname
	}
	run(){
		this.cookie_set_data()
	}
	l(a){console.log(a)}
	id(a){if(a){return document.getElementById(a)}}
	ce(tagName,attribute){let a=document.createElement(tagName);for (let key in attribute){a.setAttribute(key,attribute[key])}return a}
	cn(a){return document.createTextNode(a)}
	rmc_all(obj){if(obj!==null){while (obj.firstChild) {obj.firstChild.remove()}}}
	end(ob,childs){for(let i=0;i<childs.length;i++){ob.appendChild(childs[i])}}
	rid(n=15){let t='';for(let i=0;i<n;i++){t+=String.fromCharCode(Math.floor((Math.random()*26 ) + 65))}return 'ID_'+t}
	getXY(did){return {"left":did.getBoundingClientRect().left+window.scrollX,"top":did.getBoundingClientRect().top+window.scrollY}}
	nb(int,n_float=2){
		if(n_float!=0){
			return (parseFloat(int)).toFixed(n_float).replace(/\d(?=(\d{3})+\.)/g, '$&,')
		}else{
			return String(int).replace(/(.)(?=(\d{3})+$)/g,'$1,')
		}
	}
	cookie_set_data(){
		let a=document.cookie.split(';')
		for(let i=0;i<a.length;i++){
			let b=a[i].split('=')
			this.cookieData[b[0].trim()]=decodeURIComponent(b[1].trim()).replace(/\+/gi, " ")
		}
	}
	set_cookie(cname, cvalue, second) {
		let d = new Date()
			d.setTime(d.getTime() + 1000*second);
		let expires = "expires="+ d.toUTCString();
		document.cookie = cname + "=" +encodeURIComponent( cvalue)+ ";" + expires + ";SameSite=Lax";
	}
	setFec(dt){
		let f = new FormData()
		for (let k in dt.data){
			f.append(k,dt.data[k])	
		}
		M.fec("POST","",dt.result,dt.error,null,f)
	}
	gck(a,prop=null){
		if(prop!=null){
			try{
				let q=JSON.parse(this.cookieData[a])
				if(q[prop]!=undefined){
				return q[prop]
				}else{
					return null
				}				
			}catch{
				return null
			}
		}
		return this.cookieData[a]
	}
	fec(method,qr,callback_result,callbak_error,button,formData,event){
		let data={method: method,credentials:"same-origin"}
		if( formData!=null&&formData!=undefined){
			data.body= formData
		}
		let getData=(response)=>{
			if(response.ok){
					return response.json()
			}
			return response.text().then(function(text) {
					alert(text)
				})
		}
		let callBackOk=(response)=>{
			callback_result(response,formData,button,event)
		}
		let callBackError=(error)=>{
			callbak_error(error.message,formData,button,event)
		}
		let url="?a=fetch&rand="+(new Date().getTime())+""+qr
		
		fetch(url, data)
		.then(response =>getData(response))
		.then(response =>callBackOk(response))
		.catch(error=> callBackError(error))
	}
	tooltups(did,text){
		let ct=this.ce("div",{"class":"qbox"})
		did.setAttribute("data-width","200")
		this.end(ct,[this.cn(text)])
		this.popup(did,ct)
	}
	popup(did,callnext,callBack,event){
		M.zdl+=1
		this.z=M.zdl
		if(callBack==undefined){
			callBack="undefined"
		}
		let p=this.ce("div",{"class":"pp","style":"z-index:"+this.z})
		let ga=did.getAttribute("data-id")
		let pid=""
		let ppwidth=this.b.scrollWidth
		let tstyle=""
		if(did.getAttribute("data-width")!=undefined){
			tstyle="width:"+did.getAttribute("data-width")+"px;"
			ppwidth=did.getAttribute("data-width")*1
		}else{
			ppwidth=360*1
		}
		if(ga===null){
			let id_rand=this.rid()
			p.setAttribute("id",id_rand)
			let film=this.ce("div",{"id":"film_"+id_rand,"data-id":id_rand,"class":"film","style":"z-index:"+(this.z-2)+";background-color:rgba(0,0,0,0.2)","onclick":"M.popupClear('"+id_rand+"','"+callBack.replace(/'/g, "\\'")+"')"})
			this.b.appendChild(film)
			pid=id_rand

		}else{
			let idp=did.getAttribute("data-id")
			p.setAttribute("id",idp)
			
			let film=this.ce("div",{"id":"film_"+idp,"data-id":idp,"class":"film","style":"z-index:"+(this.z-1),"onclick":"M.popupClear('"+idp+"','"+callBack.replace(/'/g, "\\'")+"')"})
			this.b.appendChild(film)
			pid=idp
		}
		if(this.id(pid)==undefined){
			let ct_option=eval(callnext)
			

			
			let window_width=this.b.scrollWidth
			let popup_width=ppwidth
			let cklicleft=this.getXY(did).left
			let left=0;
			//--------------------ตำแหน่ง-----------------------------
			let pt=did.getBoundingClientRect();
			let top=this.getXY(did).top
			if(popup_width<=window_width){
				if(window_width-popup_width<=popup_width){
					left=(window_width-popup_width)/2
					if(left>=cklicleft){
						left=cklicleft
					}else if(left+popup_width<pt.width+pt.left){
						left=pt.width+pt.left-popup_width
						if(window_width-pt.width+pt.left>3){
							left=pt.width+pt.left-popup_width+3
							if(window_width>document.body.clientWidth){
								left=left+(window_width-document.body.clientWidth)
							}
						}
					}else{//--มี scrollbar เกิด
						left=pt.width+pt.left+window.scrollX-popup_width+3
						if(document.body.clientWidth<popup_width){
							left=left+(popup_width-document.body.clientWidth)
						}
					}
				}else{
					left=(window_width-popup_width)/2
					if(left+popup_width<pt.width+pt.left){
						left=pt.width+pt.left-popup_width
						if(window_width-pt.width+pt.left>3){
							left=pt.width+pt.left-popup_width+3
						}
						if(window_width>document.body.clientWidth){
							left=left+(window_width-document.body.clientWidth)
						}
					}else if(cklicleft + popup_width < window_width){
						left=cklicleft
					}
				}
			}
			//this.l("window_width="+window_width+",cklicleft="+cklicleft+",popup_width="+popup_width+",left="+left)
			left=(left<0)?0:left

			let is=this.getXY(did).top+pt.height-5
			p.setAttribute("style",tstyle+"top:"+is+"px;left:"+left+"px;z-index:"+(this.z-1))
			let conner=this.ce("div",{"class":"conner"})
			let content=this.ce("div",{"class":"content"})
			p.appendChild(conner);	
			p.appendChild(content);	
			content.appendChild(ct_option)
			this.b.appendChild(p)
			let lt=pt.width/2+this.getXY(did).left-5
			let rt=this.b.scrollWidth-lt
			lt=lt-left

			/*alert(`
window.innerHeight=${window.innerWidth}
Tag ที่ถูกกด กว้าง ${pt.width}
ระยะห่างซ้าย${left}+ ป็อปอับกวาง${ppwidth}  = ${left+ppwidth}
ระยะมุม ${lt}`)*/
			if(pt.width>=(2*ppwidth)-30-5){
				lt=(ppwidth/2)-5
			}
			conner.setAttribute("style","left:"+lt+"px")
			let y=p.getBoundingClientRect()
			let bt_click=did.getBoundingClientRect()
			let popup=p.getBoundingClientRect()
			if(popup.height<=bt_click.top){//alert(this.getXY(did).top-popup.height)
				is=this.getXY(did).top-popup.height+5
				p.setAttribute("style",tstyle+"top:"+is+"px;left:"+left+"px;z-index:"+(this.z-1))
				this.rmc_all(conner)
				conner.style.transform="rotate(225deg)"
				conner.style.top="-6px"
				p.appendChild(conner);
			}
		}
	}
	popupClear(id,callBack){
		if(callBack!=undefined){
			eval(callBack)
		}


	/*	let q=document.querySelector("body > div#"+id)
		let f=document.querySelector("body > div#film_"+id)
		if(q!=null){
			document.body.removeChild(q)
			if(f!=null){
				document.body.parentNode.removeChild(f)
			}
		}*/

		if(this.id(id)!=undefined){
			this.id(id).style.display="none"
			this.id("film_"+id).style.display="none"
			this.id(id).parentNode.removeChild(this.id(id))
			if(this.id("film_"+id)!=undefined){
				
				this.id("film_"+id).parentNode.removeChild(this.id("film_"+id))
			}
		}
	}
	printAgain(size,type,sku_root){
		//-size={"bill58"},type={"print"}
		let y=confirm("คุณต้องการพิมพ์ใหม่\nโปรดเรียกเก็บใบเสร็จเดิมคืนด้วย")
		if(y){
			let dt={"data":{"a":size,"b":type,"sku":sku_root,"sku_root":sku_root,"submith":"clicksubmit"},"result":M.printAgainResult,"error":M.printAgainError}
			this.setFec(dt)
		}
	}
	printAgainResult(re,form,bt){
		if(re["result"]){

		}else{
			M.printAgainError(re,form,bt)
		}
	}
	printAgainError(re,form,bt){
		alert("❌ เกิดข้อผิดพลาด "+re["message_error"])
	}
	dialog(data){
		let w_x = window.innerWidth
		let w_y = window.innerHeight
		
		M.zdl+=1
		this.z=M.zdl
		let zindex = this.z
		if(data.display ==1){
			let title= data.hasOwnProperty("title") ?data.title:"..."
			let width= data.hasOwnProperty("width") ?"width:"+data.width+"px;":""
			let rid = data.hasOwnProperty("rid") ?data.rid:this.rid()
			let dialog = this.ce("div",{"class":"dialog dialog_active1","id":rid+"_dialog","onmousedown":"this.style.transitionDuration='0s'","onmouseup":"M.setDialogSize(this)","style":width})
				let bar = this.ce("div",{})
				this.end(bar,[this.cn(title)])
				let con = this.ce("div",{})
				this.end(con,[data.ct])
				let sta = this.ce("div",{})
					let sta_in = this.ce("div",{})
						for(let i=0;i<data.bts.length;i++){
							let bt = this.ce("input",{"type":"button","value":data.bts[i].value,"onclick":data.bts[i].onclick})
							this.end(sta_in,[bt])	
						}	
				this.end(sta,[sta_in])	
			this.end(dialog,[bar,con,sta])	
			this.b.style.overflow = "hidden"
			let film =  this.ce("div",{"class":"film","id":rid,"onclick":"M.filmActive(this,'"+rid+"')"})
				film.style.zIndex = zindex
			this.end(this.b,[film,dialog])	
			M.l("window.scrollY = "+window.scrollY)
			let tp = (data.ct.offsetHeight + 76 + 8)/2 - window.scrollY 
			let lt = (data.ct.offsetWidth+8)/2 - window.scrollX 
			/*if(width != ""){
				lt = (width+8)/2
			}*/
			dialog.setAttribute("style","z-index:"+(zindex+1)+";top:calc(50% - "+(tp)+"px);left:calc(50% - "+(lt)+"px);"+width)
			this.setDialogSize(this.id(rid+"_dialog"))
			//alert(data.ct.offsetHeight)
		}
	}
	setDialogSize(did){M.l(did.offsetHeight)
		let width = (did.offsetWidth > window.innerWidth)?window.innerWidth:did.offsetWidth
		width = (width < did.childNodes[2].childNodes[0].offsetWidth+8)?did.childNodes[2].childNodes[0].offsetWidth+8:width
		let height = (did.offsetHeight > window.innerHeight)?window.innerHeight:did.offsetHeight	
		height = (height < 76 + 20 )?76 +20:height	
		let tp = (height)/2  - window.scrollY
		let lt = (width)/2  - window.scrollX
		let zindex = did.style.zIndex

		did.setAttribute("style","z-index:"+(zindex)+";top:calc(50% - "+(tp)+"px);left:calc(50% - "+(lt)+"px);height:"+(height-2)+"px;width:"+(width-2)+"px;")
	}
	filmActive(did,id){
		M.l(did.className)
		this.dialogActive(this.id(id+"_dialog"))
		if(did.className == "film film_active1"){
			did.classList.remove("film_active1")
			did.classList.add("film_active2");
		}else{
			did.classList.remove("film_active2")
			did.classList.add("film_active1");
		}
	}
	dialogActive(did){
		if(did.className == "dialog dialog_active1"){
			did.classList.remove("dialog_active1")
			did.classList.add("dialog_active2");
		}else{
			did.classList.remove("dialog_active2")
			did.classList.add("dialog_active1");
		}
	}
	dialogClose(id){
		this.id(id).parentNode.removeChild(this.id(id))
		this.id(id+"_dialog").parentNode.removeChild(this.id(id+"_dialog"))
		this.b.style.overflow = "auto"
	}
}
class gpu extends main {
	constructor(){
		super()
		this.load={"id":{"has":0,"get":0}}
	}
	loading(id,start,has=0,get=0,loadname,percent=null,listen=null){
		if(start=="start"){
			if(this.load.hasOwnProperty(id)){
				this.load[id]={"has":0,"get":0}
				this.id(id).parentNode.removeChild(this.id(id))
			}else{
				document.body.appendChild(this.loadingCt(id))				
				let z=this.id(id)
				let y=z.firstChild
				let a=y.firstChild				
				this.load[id]={"has":has,"get":0}
				z.style.display="block"
				z.style.zIndex=this.zdl+1
			}
		}else if(start=="sethas"){
			this.load[id].has=has
		}else if(start=="setget"){
			this.load[id].get+=get
			let z=this.id(id)
			let y=z.firstChild
			let x=y.firstChild
			let w=x.childNodes[1]
			let v=w.firstChild
			v.style.width=((this.load[id].get/this.load[id].has)*100)+"%"
			if(this.load[id].get==this.load[id].has){
				eval(loadname+"=0")
				this.id(id).parentNode.removeChild(this.id(id))
				this.load={"id":{"has":0,"get":0}}
			}
		}else if(start=="finish"){
			this.id(id).parentNode.removeChild(this.id(id))
		}
	}
	loadingCt(id){
		let z=this.ce("div",{"id":id,"class":"loading"})
			let y=this.ce("div",{})
				let a=this.ce("div")
					let aa=this.ce("div",{})
						let aaa=this.ce("div",{})
						this.end(aaa,[this.cn("⏳")])
						let aab=this.ce("div",{})
						this.end(aab,[this.cn("⏳")])
						let aac=this.ce("div",{})
						this.end(aac,[this.cn("⏳")])
						let aad=this.ce("div",{})
						this.end(aad,[this.cn("⏳")])
						let aae=this.ce("div",{})
						this.end(aae,[this.cn("⏳")])
					this.end(aa,[aaa/*,aab,aac,aad,aae*/])
					let ab=this.ce("div",{})
						let aba=this.ce("div",{})
					this.end(ab,[aba])
					let ac=this.ce("div",{})
				this.end(a,[aa,ab,ac])
			this.end(y,[a])
		this.end(z,[y])
		return z
	}
	unitEdit(sku_root,s_type){
		let f=document.forms.unit
		f.action="?a=unit&b=edit"
		f.sku_root.value=sku_root
		f.s_type.value=s_type
		f.submit()
	}
	unitDelete(sku_root,name){
		let y=confirm("คุณต้องการลบ "+name)
		if(y){
			let f=document.forms.unit
			f.action="?a=unit&b=delete"
			f.sku_root.value=sku_root
			f.submit()
		}
	}
	logout(){
		let f=document.forms.me
		f.logout.value="logout"
		f.action="?a=login"
		f.submit()
	}
	meSubmit(){
		let f=document.forms.me
		 let me=prompt("โปรดใส่ระหรัสผ่านปัจจุบันของคุณ\nเพื่อเป็นการยืนยันว่าคือคุณ")
		 if(me!=null&&me.trim().length>0){
			 f.ps.value=me
			 f.submit()
		 }
	}
	settingSubmit(){
		let f=document.forms.setting
		 let me=prompt("โปรดใส่ระหรัสผ่านปัจจุบันของคุณ\nเพื่อเป็นการยืนยันว่าคือคุณ")
		 if(me!=null&&me.trim().length>0){
			 f.ps.value=me
			 f.submit()
		 }
	}
	userEdit(sku_root){
		let f=document.forms.user
		f.action="?a=user&b=edit"
		f.sku_root.value=sku_root
		f.submit()
	}
	userDelete(sku_root,name){
		let y=confirm("คุณต้องการลบ "+name)
		if(y){
			let f=document.forms.user
			f.action="?a=user&b=delete"
			f.sku_root.value=sku_root
			f.submit()
		}
	}
	calc(did){
		did.value=""
		let dt=[
			"7","8","9","+",
			"4","5","6",
			"1","2","3",
			"0",".","ว่าง","-"
		]
		let c=M.ce("div",{"class":"calc"})
		for(let k in dt){
			let d=M.ce("div",{})
			let b=M.ce("input",{"type":"button","value":dt[k],"onclick":"G.calcClick(event,'"+did.id+"','"+dt[k]+"')","ontouchstart":"G.calcClick(event,'"+did.id+"','"+dt[k]+"')"})
			c.appendChild(d)
				d.appendChild(b)
		}
		return c
	}
	calcClick(event,id,value){
		event.preventDefault()
		let d=["0","1","2","3","4","5","6","7","8","9"]
		if(d.indexOf(value)!=-1){
			M.id(id).value+=""+value
		}else if(value=="+"||value=="-"){
			let v=M.id(id)
			let q=v.value.substring(0,1)

			if(q!="+"&&q!="-"){
				M.film.click()
				let y=v.value*1
				if(y==0){
					y=""
				}	
				M.id(id).value=value+""+y
			}else if(q=="+"||q=="-"){
				v.value=v.value.replace(q,value)
			}
		}else if(value=="ว่าง"){
			M.id(id).value=""
		}	
	}
	calcDefault(id,idatcfg){
		let a=M.id(id)
		if(a!=undefined){
			if(a.value*1>0){
				a.value="+"+(a.value*1)
			}else if(a.value*1<0){
				a.value="-"+Math.abs((a.value*1))
			}else{
				a.value="+1"
			}
		}
		if(M.id(idatcfg)!=undefined){
			M.id(idatcfg).focus()
		}
	}
	search(did,fol="",for_id=""){
		let ido=this.rid()
		let idt=this.rid()
		let idf=this.rid()
		let ct=this.ce("div",{"class":"search"})
			let dtop=this.ce("div",{})
				let dopt=this.ce("div",{})
					let selec=this.ce("select",{"id":ido})
						let opsku=this.ce("option",{"value":"sku"})
						this.end(opsku,[this.cn("รหัสภายใน")])
						let opbc=this.ce("option",{"value":"barcode"})
						this.end(opbc,[this.cn("รหัสแท่ง")])
						let opname=this.ce("option",{"value":"name","selected":"selected"})
						this.end(opname,[this.cn("ชื่อสินค้า")])
					this.end(selec,[opsku,opbc,opname])	
				this.end(dopt,[selec])
				let dtext=this.ce("div",{})
					let dip=this.ce("input",{"id": idt,"type":"text","onkeyup":"G.searchProduct(event, '"+ido+"', '"+idt+"','"+idf+"','"+fol+"','"+for_id+"')"})
				this.end(dtext,[dip])
				let dsubm=this.ce("div",{})
					let dbt=this.ce("input",{"type":"button","onclick":"G.searchProduct(null, '"+ido+"', '"+idt+"','"+idf+"','"+fol+"','"+for_id+"')","value":"ค้นหา"})
				this.end(dsubm,[dbt])
			this.end(dtop,[dopt,dtext,dsubm])	
			let difram=this.ce("div",{})
				let c=this.ce("iframe",{"id":idf,"src":"?a=product&b=select&for="+fol+"&for_id="+for_id,"width":"100%","height":"250","allowtransparency":"true"})
			this.end(difram,[c])	
		this.end(ct,[dtop,difram])		
		return ct
	}
	searchProduct(e,id_sec,id_text,id_frame,fol="sell",for_id){
		let fl=this.id(id_sec)
		let fr=this.id(id_frame)
		let tx=this.id(id_text)
		let sfor={"sell":null,"label":null,"itmw":null}
		if(!sfor.hasOwnProperty(fol)){
			fol="sell"
		}
		if(e==null){
			fr.src="?a=product&b=select&for="+fol+"&for_id="+for_id+"&fl="+fl.value+"&tx="+tx.value
		}else{
			let k=e.key.toLowerCase()
			if(k=="enter"){
				fr.src="?a=product&b=select&for="+fol+"&for_id="+for_id+"&fl="+fl.value+"&tx="+tx.value
				e.preventDefault()
			}
		}
	}
	me(did){
		let thm=localStorage.getItem('thm')
		let ct=this.ce("div",{})
			let p=this.ce("p",{"class":"s14"})
			this.end(p,[this.cn("สวัสดีคุณ "+M.gck("ud","name"))])
			let pip=this.ce("p",{"class":"s14"})
			this.end(pip,[this.cn("IP เตรื่องขายนี้  "+S.ip)])
			let tem=this.ce("select",{"onchange":"S.thm(this)"})
				let op1=this.ce("option",{"value":"black"})
				let op2=this.ce("option",{"value":"gray"})
				let op3=this.ce("option",{"value":"white"})
				this.end(op1,[this.cn("สีดำ")])
				this.end(op2,[this.cn("สีเทาอ่อน")])
				this.end(op3,[this.cn("สีขาว")])
			this.end(tem,[op1,op2,op3])
			let help=this.ce("div",{"class":"s14"})
				let help_v=this.ce("a",{"onclick":"S.printCm('alert')"})
				this.end(help_v,[this.cn("ดูคำสั่งใช้งาน")])
				let help_a=this.ce("a",{"onclick":"S.printCm()"})
				this.end(help_a,[this.cn("พิมพ์คำสั่งใช้งาน")])
			this.end(help,[help_v,help_a])
			let db=this.ce("div",{"class":"c"})
				let bthome=this.ce("input",{"type":"button","value":"ออกจากหน้านี้","onclick":"location.href='index.php'"})
				let btout=this.ce("input",{"type":"button","value":"ออกจากระบบ","onclick":"location.href='index.php?a=me'"})
			this.end(db,[bthome,btout])
		this.end(ct,[p,pip,tem,help,db])
		for(let i=0;i<tem.length;i++){
			if(tem[i].value==thm){
				tem[i].setAttribute("selected","selected")
				break
			}
		}
		return ct
	}
	action(did){
		M.popup(did,'G.actionMenu(did)')
	}
	actionMenu(did){
		let ct=this.ce("div",{"class":"l"})
		let w=[]
		let o=did.parentNode
		let c=0;
		for(let i=0;i<o.childNodes.length;i++){
			let u=o.childNodes[i]
			if(u.tagName=="A"){
				c=c+1
				if(c>1){
					w[c]=this.ce("a",{"onclick":u.getAttribute("onclick")})
					this.end(w[c],[this.cn(u.innerHTML+" "+u.title)])
					this.end(ct,[w[c]])
				}
			}
		}
		return ct
	}
}
class F{
	static	getThaiDate(time){
		let mo=["ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.",]
		let d=new Date(Number(time))
		let m=mo[d.getMonth()]
		let y=Number(d.getFullYear()+543)
		let dy=d.getDate()
		dy=(dy<10)?"0"+dy:dy
		let h=d.getHours()
		h=(h<10)?"0"+h:h
		let mn=d.getMinutes()
		mn=(mn<10)?"0"+mn:mn
		let wd=dy+"-"+m+"-"+y+" "+h+":"+mn
		return wd
	}
	static	isNumber(t){
		if(/^((\d+)|((\d+)\.(\d*)))$/.test(t)){
			return true
		}
		return false
	}
	static	go(e,qury){
		if(e!=null&&(e.code=="NumpadEnter"||e.code=="Enter")){
			location.href=qury+""+M.id("idpageview").value
		}else if(e==null){
			location.href=qury+""+M.id("idpageview").value
		}
		
	}
	static	htmlspecialchars(str){
		str = str.replace(/&/g, "&amp;");
		str = str.replace(/>/g, "&gt;");
		str = str.replace(/</g, "&lt;");
		str = str.replace(/"/g, "&quot;");
		str = str.replace(/'/g, "&#039;");
		return str;
	}
}
