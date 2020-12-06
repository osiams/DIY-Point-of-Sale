 /*--apt DIY_POS;--ext js;--version 0.0;*/
"use strict"
class barcode extends main{
	constructor(){
		super()
		this.bc=null
		this.pw=null
		this.ph=null
		this.lw=null
		this.lh=null
		this.pl=null
		this.pt=null
		this.gc=null
		this.gr=null
		this.zm=null
		this.lb=null
		this.pvzm=null
		this.pv=null
		this.sh=null
		this.dt={}
		this.le=0
		this.has=0
	}
	run(){
		this.bc=this.id("barcode")
		this.pw=this.id("barcode_pagewidth")
		this.ph=this.id("barcode_pageheight")
		this.lw=this.id("barcode_labelwidth")
		this.lh=this.id("barcode_labelheight")
		this.pl=this.id("barcode_paddingleft")
		this.pt=this.id("barcode_paddingtop")
		this.gc=this.id("barcode_gapcol")
		this.gr=this.id("barcode_gaprow")
		this.zm=this.id("barcode_zoom")
		this.lb=this.id("barcode_labelborder")
		this.pvzm=this.id("barcode_page_prev_zm")
		this.pvo=this.id("barcode_page_prev_out")
		this.pvi=this.id("barcode_page_prev_in")
		this.sh=this.id("sh")
		this.pvo.style.width=this.pw.value+"mm"
		this.pvo.style.height=this.ph.value+"mm"
		this.pvzm.style.width=this.pw.value+"mm"
		this.pvzm.style.height=this.ph.value+"mm"
		this.setLabel(1)
		window.onbeforeprint=function(event) {Bc.setSH(0)}
		//this.pvo.parentNode.addEventListener('wheel',Bc.setZoomW);
	}
	load(){
		for (let k in this.dt) {
			if(this.id(k)!=undefined){
				this.setConT( this.dt[k])
			}
		}
	}
	setR(v){
		let r=parseFloat(v)
		if(r<0.1){r=0}
		return r
	}
	setpw(){
		this.pvo.style.width=this.setR(this.pw.value)+"mm"
		this.pvzm.style.width=this.setR(this.pw.value)+"mm"
		this.setLabel()
	}
	setph(){
		this.pvo.style.height=this.setR(this.ph.value)+"mm"
		this.pvzm.style.height=this.setR(this.ph.value)+"mm"
		this.setLabel()
	}
	setpl(){
		this.setLabel()
	}
	setpt(){
		this.setLabel()
	}
	setlw(){
		this.le=1
		this.setLabel(1)
	}
	setlh(){
		this.le=1
		this.setLabel(1)
	}
	setgc(){
		this.setLabel()
	}
	setgr(){
		this.setLabel()
	}
	setlb(){
		this.setLabel()
	}
	setSH(i=1){
		this.setZoom100()
		if(i==1){
			if(this.sh.style.zIndex=="10"){
				this.bc.style.gridTemplateColumns="200px auto"
				this.sh.style.zIndex="-10"
			}else{
				this.bc.style.gridTemplateColumns="0px auto"
				this.sh.style.zIndex="10"
			}
		}else if(i==0){
			this.bc.style.gridTemplateColumns="0px auto"
			this.sh.style.zIndex="10"
		}
	}
	setLabel(l=0){
		let gc=this.setR(this.gc.value)
		let gr=this.setR(this.gr.value)
		let lw=this.setR(this.lw.value)
		let lh=this.setR(this.lh.value)
		let lwg=lw+gc
		let lhg=lh+gr
		let w=this.setR(this.pw.value)-this.setR(this.pl.value)+gc
		let h=this.setR(this.ph.value)-this.setR(this.pt.value)+gr
		let maxw=Math.floor(w/lwg)
		let maxh=Math.floor(h/lhg)
		//M.l( w+","+ lwg+","+gc+","+maxw+","+this.setR(this.pw.value))
		let has=maxw*maxh
		this.has=has
		let cla=""
		for(let i=0;i<maxw;i++){
			cla+="auto "
		}
		this.pvi.style.gridTemplateColumns=cla;

		this.pvi.style.gridGap=+gr+"mm "+gc+"mm"
		this.pvi.style.marginLeft=this.setR(this.pl.value)+"mm"
		this.pvi.style.marginTop=this.setR(this.pt.value)+"mm"
		let bd=""
		let lbw=0
		let mm=(parseInt(this.pvo.style.width)/this.pvo.offsetWidth)
		if(this.lb.checked){
			bd=";border:1px dashed black;border-radius:0px"
			lbw=2*mm
		}
		if(l==1){
			this.rmc_all(this.pvi)
			for(let i=0;i<has;i++){
				let pdt=(i<maxw)?this.setR(this.pt.value):"0"
				let c=this.ce("div",{"id":"label_"+(i+1),"style":"width:"+(lw-lbw)+"mm;height:"+(lh-lbw)+"mm;"+bd,"onclick":"Bc.action(this,event)","data-width":"180"})
					let at=this.ce("p",{"class":"at"})
					this.end(at,[this.cn((i+1))])
				this.end(c,[at])
				this.end(this.pvi,[c])
			}
		}else if(l==0){
				
			
			for(let i=0;i<this.pvi.childNodes.length;i++){
				if(bd!=""){
					if(i<has){
						this.pvi.childNodes[i].style.border="1px dashed black"
						this.pvi.childNodes[i].style.borderRadius="0px"
					}
					this.pvi.childNodes[i].style.width=(lw-lbw)+"mm"
					this.pvi.childNodes[i].style.height=(lh-lbw)+"mm"
				}else{
					this.pvi.childNodes[i].style.border="0px dashed black"
					this.pvi.childNodes[i].style.borderRadius="6px"
					this.pvi.childNodes[i].style.width=(lw-lbw)+"mm"
					this.pvi.childNodes[i].style.height=(lh-lbw)+"mm"
				}
				if(i>=has){
					this.pvi.childNodes[i].style.height=0
				}
			}
		}
	}
	action(did,e){
		if(this.le==1){
			for (let k in this.dt) {
				if(this.id(k)!=undefined){
					this.setConT( this.dt[k])
				}
			}
		}
		this.le=0
		M.popup(did,'Bc.actionMenu(did)',null,e)
	}
	actionMenu(did){
		let ct=this.ce("div",{})
			let cti=this.ce("div",{"class":"labelaction_ct"})
				let a1=this.ce("a",{"class":"","data-id":"bcselectproduct","onclick":"Bc.selectProduct(this,'"+did.id+"')"})
				this.end(a1,[this.cn("👆 เลือกสินค้าใส่")])
				let a2=this.ce("a",{"class":"","data-id":"bcsetn","onclick":"Bc.setN(this,'"+did.id+"')","data-width":"170"})
				this.end(a2,[this.cn("↔️ จำนวน")])
				let a3=this.ce("a",{"class":"","onclick":"Bc.clearLb(1,'"+did.id+"')"})
				this.end(a3,[this.cn("🧹 ล้างออก")])
				let a4=this.ce("a",{"class":"","onclick":"Bc.clearLb(0)"})
				this.end(a4,[this.cn("🗑 ล้างออก ทั้งหน้า")])
			if(this.dt.hasOwnProperty(did.id)){
				this.end(cti,[a1,a2,a3,a4])
			}else{
				this.end(cti,[a1])
			}
		this.end(ct,[cti])
		return ct
	}
	clearLb(type,id=null){
		if(type==1){
			let li=this.id(id)
			let no=this.getNo(id)
			this.rmc_all(li)
			let at=this.ce("p",{"class":"at"})
			this.end(at,[this.cn(no)])
			this.end(li,[at])
			if(this.dt.hasOwnProperty(id)){
				delete this.dt[id]
			}
		}else if(type==0){
			this.dt={}
			this.setLabel(1)
		}
	}
	setZoom100(){
		this.zm.value=100
		this.setZoom(this.zm)
	}
	setZoom(did){
		let z=parseFloat(did.value)/100
		if(z<0.1){
			z=0.1
			did.value=10
		}
		//M.l(z)
		this.pvo.style.transform="scale("+z+","+z+")"
		this.pvzm.style.width=(this.pvo.clientWidth*z)+"px"
		this.pvzm.style.height=(this.pvo.clientHeight*z)+"px"
		if(z<1){
			this.pvo.style.left="-"+((this.pvo.clientWidth-(this.pvo.clientWidth*z))/2)+"px"
			this.pvo.style.top="-"+((this.pvo.clientHeight-(this.pvo.clientHeight*z))/2)+"px"
			M.l(this.pvo.clientWidth+","+(this.pvo.clientWidth*z))
		}else{
			this.pvo.style.left="0px"
			this.pvo.style.top="0px"
		}
	}
	setZoomW(event){
		let n=event.deltaY>0?1:-1
		let y=parseInt(Bc.zm.value)-n
		y=y>10?y:10
		Bc.zm.value=y
		Bc.setZoom(Bc.zm)
	}
	setN(did,id){
		this.popup(did,'Bc.setNP(did,\''+id+'\')')
	}
	setNP(did,id){
		let ct=this.ce("div",{"data-width":"100px","class":"size14"})
			let tb=this.ce("table",{})
				let cpt=this.ce("caption",{})
				this.end(cpt,[this.cn("️⬅️ เพื่มจำนวน ➡️")])
				let tr1=this.ce("tr",{})
					let th1=this.ce("td",{})
					this.end(th1,[this.cn("ก่อนหน้า")])
					let th2=this.ce("td",{})
					this.end(th2,[this.cn("ต่อหลัง")])
				this.end(tr1,[th1,th2])
				let tr2=this.ce("tr",{})
					let td1=this.ce("td",{})
						let ip1=this.ce("input",{"id":"bcsetnl","type":"number","value":0,"class":"bcsetn"})
					this.end(td1,[ip1])
					let td2=this.ce("td",{})
						let ip2=this.ce("input",{"id":"bcsetnr","type":"number","value":0,"class":"bcsetn"})
					this.end(td2,[ip2])
				this.end(tr2,[td1,td2])
			this.end(tb,[cpt,tr1,tr2])
			let sb=this.ce("input",{"type":"button","value":"ยืนยัน","onclick":"Bc.setNPok(this,\'"+id+"\')"})
		this.end(ct,[tb,sb])
		return ct
	}
	setNPok(did,id){
		let l=this.setR(this.id("bcsetnl").value)
		let r=this.setR(this.id("bcsetnr").value)
		this.popupClear("bcsetn")
		let no=this.setR(this.getNo(id))
		for(let i=no-1;i>=no-l;i--){
			let k="label_"+i
			if(i>0){
				this.dt[k]=Object.assign({},this.dt[id]);
				this.dt[k].for_id=k
				this.id(k).innerHTML=this.id(id).innerHTML
			}else{
				break;
			}
		}
		for(let i=no+1;i<=no+r;i++){
			let k="label_"+i
			if(i<=this.has){
				this.dt[k]=Object.assign({},this.dt[id]);
				this.dt[k].for_id=k
				this.id(k).innerHTML=this.id(id).innerHTML
			}else{
				break;
			}
		}
	}
	getNo(label_id){
		let no=label_id.substring(6, label_id.length);
		return no
	}
	selectProduct(did,id){
		this.popup(did,'G.search(did,\'label\',\''+id+'\')')
		/*let ct=this.ce("div",{"onclick":"M.popup(this,'G.search(did,\'sell\')')"})
		this.end(ct,[this.cn("👆 เลือกสินค้าใส่")])
		M.popup(did,ct)*/
	}
	productSelect(d,n=null){
		if(d.sku_root!=undefined){
			let sku_root=d.sku_root
			this.setConT(d)
			this.popupClear("bcselectproduct")
			/*if(!S.pd.hasOwnProperty(sku_root)){
				S.getPdFromServer("sku_root",sku_root,n)
			}else{
				let bc=S.pd[sku_root].barcode
				S.insertList(bc,n)
			}*/
		}
	}
	setConT(d){
		let mmppx=(this.pvo.clientWidth/parseInt(this.pvo.style.width))
		//alert( mmppx)
		M.l(d.for_id)
		let li=this.id(d.for_id)
		let lv=[19,24,29,34,49,99]
		let c=0
		let h=this.setR(this.lh.value)
		for(let i=0;i<lv.length;i++){
			if(h<=lv[i]){
				c=lv[i]
				break;
			}
		}
		this.dt[d.for_id]=d
		this.rmc_all(li)
		let ct=this.ce("div",{"class":"lt"+c+"gr"})
			let din=this.ce("div",{})
				let p=this.ce("div",{"class":"lt"+c+"f"})
				let name=d.name+" ฿"+d.price
				this.end(p,[this.cn(name)])
				let divi=this.ce("div",{})
					let img=this.ce("img",{"src":"?a=bill58&b=barcodelabel&barcode="+d.barcode+"&type=1&br=2&height="+(mmppx*this.lh.value*2)+"&width="+(mmppx*this.lw.value*2)})
				this.end(divi,[img])
			this.end(din,[p,divi])	
			let pn=this.ce("div",{"class":"lt"+c+"f"})
			this.end(pn,[this.cn(d.barcode)])
		this.end(ct,[din,pn])
		this.end(li,[ct])
		//alert(li.childNodes[0].childNodes[0].childNodes[0].offsetheight)
	}
	changptf(did){
		let a=did.value.split("/")
		let ap=a[0].split("x")
		let al=a[1].split("x")
		let ad=a[2].split(",")
		let ag=a[3].split(",")
		this.pw.value=ap[0]
		this.ph.value=ap[1]
		this.lw.value=al[0]
		this.lh.value=al[1]
		this.pl.value=ad[0]
		this.pt.value=ad[1]
		this.gc.value=ag[0]
		this.gr.value=ag[1]
		this.lb.checked=(a[4]=="1")?true:false
			this.le=1
			this.setLabel(1)
	}
}
