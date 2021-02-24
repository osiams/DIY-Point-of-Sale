"use strict"
class cd extends main{
	constructor(){
		super()
		this.contime=Date.now()
		this.dt={}
		this.cdblock = null
		this.area_close_xy = 50
		this.tmds = 0
		this.is_md = false
		this.dp = 3000/1
		this.inst = null
		this.cdlogo = null
		this.cduser = null
		this.cdvdo = null
		this.divvdo = null
		this.divvdo_out = null
		this.div_empty = null
		this.vedolist = null
		this.thankyou = null
		this.cd_change = null
		this.countdownclose = null 
		this.soundthank =null
		this.filedir = "vdo/cd"
		this.ip = null
		this.mykey=""
		this.cdlistitem = null
		this.cd_nlist = null
		this.cd_nunit = null
		this.cd_sum = null
		this.cd_sum_float = null
		this.empty = 1
		this.thank = false
		this.time_thand = 20
		this.time_thank = 20
	}
	run(){
		window.onload=()=>{
			this.cdblock = this.id("cd_display")
			this.cdstnby = this.id("cdstnby")
			this.cdlogo = this.id("cdlogo")
			this.cduser = this.id("cduser")
			this.cdvdo = this.id("cdvideo")
			this.cdvdo_empty = this.id("cd_video_empty")
			
			this.div_empty = this.id("cd_empty")
			this.thankyou = this.id("thankyou")
			this.cd_change = this.id("cd_change")
			this.countdownclose = this.id("countdownclose")
			this.soundthank = this.id("soundthank")
			this.divvdo = this.id("divvdo")
			this.divvdo_out = this.id("divvdo_out")
			this.vedolist = list_file
			
			this.cdlistitem= this.id("cdlistitem")
			
			this.cd_nlist = this.id("cd_nlist")
			this.cd_nunit = this.id("cd_nunit")
			this.cd_sum = this.id("cd_sum")
			this.cd_sum_float = this.id("cd_sum_float")
			
			this.cdblock.addEventListener("mousedown",Cd.md,true)
			this.cdblock.addEventListener("mouseup",Cd.mu)
			this.cdblock.addEventListener("touchstart",Cd.md,true)
			this.cdblock.addEventListener("touchend",Cd.mu)
			
			this.cdvdo.addEventListener("ended",Cd.playAgian)
			/////////this.cdvdo.addEventListener("mousedown",Cd.md,true)
			////////////this.cdvdo.addEventListener("mouseup",Cd.mu)
			
			this.cdvdo_empty.addEventListener("ended",Cd.playAgian)
			this.cdvdo_empty.addEventListener("mousedown",Cd.md,true)
			this.cdvdo_empty.addEventListener("mouseup",Cd.mu)
			this.cdvdo_empty.addEventListener("touchstart",Cd.md,true)
			this.cdvdo_empty.addEventListener("touchend",Cd.mu)
			
			let ip_value=localStorage.getItem("cd_ip_value")
			if( ip_value !== null){
				this.id("sd_ip").value =ip_value
			}
			this.textBrand()	
		}
	}
	play(ob){
		
	}
	onMessage(data){//alert(JSON.stringify(data))
		//alert(data.ip+"-"+data.key+"-"+this.ip+"-type-="+data.type)
		if(data.type == "regis"){
			let data = {"command":"now","type":"message","_oto":"S"}	
			Ws.send([this.ip],data)
		}else if(data.type == "system"){
			if(data.action == "out"&&data.ip==this.ip){
				this.empty = 1
				this.showVDO()
			}
		}else if(data.key == this.ip){
			if(data.command == "now"){
				data.message.user_name = data.user_name
				this.setNowLNS(data.message)
			}else if(data.command == "sell"){
				data.message.user_name = data.user_name
				this.setNowLNS(data.message)
			}else if(data.command == "success"){
				//data.change="2,025.30"
				this.cd_change.innerHTML = this.nb(data.change.replace(/,/g,""))
				this.showThank()
			}	
		}
	}
	showThank(){
		this.time_thank = this.time_thand
		this.cdvdo_empty.muted = true
		this.soundthank.play()
		this.thankyou.style.visibility = "visible"
		this.thankClose()
	}
	thankClose(type = 1){
		if(Cd.time_thank > 0 && type != 0){
			Cd.time_thank -=1
			Cd.countdownclose.innerHTML = Cd.time_thank
			setTimeout("Cd.thankClose()",1000)
		}else{
			Cd.time_thank = 0
			Cd.thankyou.style.visibility = "hidden"
			Cd.cdvdo_empty.muted = false
		}
	}
	setNowLNS(message){
		let y='{"n_unit":5000,"n_list":4000,"sum":1445.2,"skurootlast":"1608573175bU8igMg","n_before":1,"n_now":1,"n_last":1,"name":"ปลากระป๋อง 3 แม่ครัวในซอสมะเขือเทศ 350 กรัม.ปลากระป๋อง 3 แม่ครัวในซอสมะเขือเทศ 350 กรัม.","price":"15.12","unit":"กระป๋อง","barcode":"8853620002102"}'
		let data = message
		if(parseFloat(data.sum) > 0){
			this.empty = 0
			this.showVDO()
			let add = (data.n_last > 0)?"add":"minus"
			let n_last = (data.n_last*-1 > data.n_before)?data.n_before:data.n_last
			let d = this.ce("div",{"class":add})
				let s_add = this.ce("span",{"class":add})
				this.end(s_add,[this.cn(Math.abs(n_last))])
				let tn1 = this.cn(data.name)
				let br = this.ce("br",{})
				let s_barcode = this.ce("span",{"class":"pdbarcode"})
				this.end(s_barcode,[this.cn(data.barcode)])
				let s_price = this.ce("span",{"class":"pdprice"})
				this.end(s_price,[this.cn(this.nb(data.price,2))])
				let s_unit = this.ce("span",{"class":"pdunit"})
				this.end(s_unit,[this.cn(data.unit)])
			this.end(d,[s_add,tn1,br,s_barcode,s_price,s_unit])
			this.end(this.cdlistitem,[d])
			//this.cdlistitem.innerHTML += '<div class="'+add+'"><span class="'+add+'">'+Math.abs(n_last)+'</span> '+data.name+'<br /><span class="pdbarcode">'+data.barcode+'</span><span class="pdprice">'+data.price+'</span><span class="pdunit">'+data.unit+'</span></div>'	
			this.cd_nlist.innerHTML = this.nb(data.n_list,0)
			this.cd_nunit.innerHTML = this.nb(data.n_unit,0)
			this.cd_sum.innerHTML = this.nb(this.getIntFloat(data.sum.replace(/,/g,"")),0)
			this.cd_sum_float.innerHTML = this.getIntFloat(data.sum.replace(/,/g,""),"float")
			this.cdlistitem.scrollTo({top:this.cdlistitem.scrollHeight,behavior: "smooth"});
			this.cduser.innerHTML = data.user_name
		}else{
			this.empty = 1
			this.showVDO()
		}
	}
	showVDO(){
		if(this.empty == 1){
			this.cdvdo.pause()
			this.div_empty.style.visibility = "visible"
			if(this.cdvdo.readyState == 0){
				this.randFiles(this.cdvdo_empty)
			}else{
				this.cdvdo_empty.src = this.cdvdo.currentSrc
				this.cdvdo_empty.currentTime = this.cdvdo.currentTime
			}
			if(this.cdblock.style.width != "0%"){
				this.cdvdo_empty.play().then(ok =>{}).catch(error =>{})
			}
			this.clearInHTML()
		}else if(this.empty == 0){
			if(this.cdvdo.paused){
				this.div_empty.style.visibility = "hidden"
				this.cdvdo_empty.pause()
				this.cdvdo_empty.currentTime = 0
				this.cdvdo.src = this.cdvdo_empty.currentSrc
				this.cdvdo.currentTime = this.cdvdo_empty.currentTime
				this.cdvdo.play()
				this.cdvdo.muted = true
				this.thankClose(0)
			}
		}
	}
	clearInHTML(){
		this.cdlistitem.innerHTML = ""
		this.cd_nlist.innerHTML = ""
		this.cd_nunit.innerHTML = ""
		this.cd_sum.innerHTML = ""
		this.cd_sum_float.innerHTML = ""
	}
	getIntFloat(ft,type = "int",n_float = 2){
		let i =parseInt(ft)
		let f=Math.floor(	((ft - i)*100))
		if(type == "int"){
			return i
		}else if(type == "float"){
			f = (f<10)?"0"+f:f
			return f
		}else{
			return this.nb(ft,2)
		}
	}
	playAgian(){
		this.pause()
		this.currentTime = 0
		Cd.randFiles(this)
		this.play()
	}
	md(event){
		Cd.tmds = 0
		let w_x = window.innerWidth
		let w_y = window.innerHeight
		let e_x = event.target.offsetWidth
		let e_y = event.target.offsetHeight
		let m_x = (event.clientX!=undefined)?event.clientX:event.touches[0].pageX
		let m_y = (event.clientY!=undefined)?event.clientY:event.touches[0].pageY
		if(Math.abs(w_x - m_x) <= Cd.area_close_xy && Math.abs(w_y - m_y) <= Cd.area_close_xy && e_x >= Cd.area_close_xy){
			Cd.is_md = true	
			setTimeout("Cd.countmd()",100)
		}
	}
	mu(event){
		Cd.is_md = false
	}
	countmd(){
		Cd.tmds+=100
		if(Cd.tmds >= Cd.dp ){
			Cd.is_md = false
			this.b.style.overflow = "auto"
			this.cdblock.style.width = "0%"
			this.cdblock.style.height = "0%"
			this.div_empty.style.visibility = "hidden"
			if(this.cdvdo.readyState != 0){
				this.cdvdo.pause();
				this.cdvdo.currentTime = 0;
			}
			if(this.cdvdo_empty.readyState != 0){
				this.cdvdo_empty.pause();
				this.cdvdo_empty.currentTime = 0;
			}
		}
		if(Cd.is_md == true){
			setTimeout("Cd.countmd()",100)
		}
	}
	showDisplay(){	
		if(Ws.ws.readyState == 1){
			if(Ws.mykey == ""){
				this.ip = this.id("sd_ip").value.trim()
				localStorage.setItem("cd_ip_value",this.ip)
				Ws.mykey =Ws.createKey()
				Ws.myoto = "Cd"
				let data = {"command":"","name":Ws.mykey,"type":"regis","_oto":"Cd"}	
				Ws.send([],data)
			}	
			this.b.style.overflow = "hidden"
			this.cdblock.style.width = "100%"
			this.cdblock.style.height = "100%"
			this.cdstart(this.cdvdo)

		}
	}
	cdstart(vdo_ob){
		if(this.empty == 0){	
			this.randFiles(vdo_ob)
			vdo_ob.play()
		}else{
			this.showVDO()
		}
	}
	randFiles(vdo_ob){
		let ran = Math.floor(Math.random() * Math.floor(this.vedolist.length))
		vdo_ob.src = this.filedir+"/"+this.vedolist[ran]		
	}
	textBrand(){
		let cil = ["Red","Aqua","Chartreuse","Yellow","Cyan","DeepPink","Fuchsia","Gold","GreenYellow","DeepSkyBlue"]
		let t = this.cdlogo.childNodes[0].innerHTML
		let n = ""
		for(let i=0;i<t.length;i++){
			let ran = Math.floor(Math.random() * Math.floor(cil.length))
			n+='<span style="color:'+cil[ran]+'">'+t[i]+'</span>'
		}
		this.cdlogo.childNodes[0].innerHTML = n
	}
}
