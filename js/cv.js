"use strict"
class cv extends main{
	constructor(){
		super()
		this.contime=Date.now()
		this.gl_dir="img/gallery"
		this.cvblock = null
		this.area_close_xy = 50
		this.is_md = false
		this.is_app_open=false
		this.dp = 10/1
		this.k={"NumpadMultiply":"*","NumpadAdd":"+","Minus":"-","NumpadSubtract":"-","NumpadDecimal":".",
			"Digit0":"0","Digit1":"1","Digit2":"2","Digit3":"3","Digit4":"4",
			"Digit5":"5","Digit6":"6","Digit7":"7","Digit8":"8","Digit9":"9",
			"Numpad0":"0","Numpad1":"1","Numpad2":"2","Numpad3":"3","Numpad4":"4",
			"Numpad5":"5","Numpad6":"6","Numpad7":"7","Numpad8":"8","Numpad9":"9",
			"ArrowDown":"-","ArrowUp":"+","NumpadDivide":"/"
		}
		this.cv_img=null
		this.cv_name=null
		this.cv_price=null
		this.kl="";
	}
	run(){
		window.onload=()=>{
			this.cvblock = this.id("cv_display")
			this.cv_img = this.id("cv_img")
			this.cv_name = this.id("cv_name")
			this.cv_price = this.id("cv_price")
			this.cvblock.addEventListener("mousedown",Cv.md,true)
			this.cvblock.addEventListener("mouseup",Cv.mu)
			this.cvblock.addEventListener("touchstart",Cv.md,true)
			this.cvblock.addEventListener("touchend",Cv.mu)
			window.addEventListener("keydown",Cv.kb)
			window.addEventListener("keyup",Cv.kb)
		}
	}

	md(event){
		Cv.tmds = 0
		let w_x = window.innerWidth
		let w_y = window.innerHeight
		let e_x = event.target.offsetWidth
		let e_y = event.target.offsetHeight
		let m_x = (event.clientX!=undefined)?event.clientX:event.touches[0].pageX
		let m_y = (event.clientY!=undefined)?event.clientY:event.touches[0].pageY
		if(Math.abs(w_x - m_x) <= Cv.area_close_xy && Math.abs(w_y - m_y) <= Cv.area_close_xy && e_x >= Cv.area_close_xy){
			Cv.is_md = true	
			Cv.is_app_open=false
			setTimeout("Cv.countmd()",100)
		}
	}
	countmd(){
		Cv.tmds+=100
		if(Cv.tmds >= Cv.dp ){
			Cv.is_md = false
			this.b.style.overflow = "auto"
			this.cvblock.style.width = "0%"
			this.cvblock.style.height = "0%"
			
		}
		if(Cv.is_md == true){
			setTimeout("Cv.countmd()",100)
		}
	}
	showDisplay(did){	
		did.blur()
		this.b.style.overflow = "hidden"
		this.cvblock.style.width = "100%"
		this.cvblock.style.height = "100%"
		this.is_app_open=true
	}
	insertList(bc){
		this.getPdFromServer("barcode",bc)
	}
	getPdFromServer(field,value){
		let formData = new FormData()
		formData.append("a","sell")			
		formData.append("submith","clicksubmit")		
		formData.append("b","get_bc")		
		formData.append("field",field)		
		formData.append("bc",value)		
		M.fec("POST","",Cv.getPdFromServerResult,Cv.getPdFromServerError,null,formData)
	}
	getPdFromServerResult(re,form,bt){
		if(re["result"]){
				Cv.showData(re)
		}else{
			Cv.getPdFromServerError(re,form,bt)
		}
	}
	getPdFromServerError(re,form,bt){
		if(re["message_error"]!=undefined){
			alert(re["message_error"])
		}else{
			alert("เกิดข้อผิดพลาด บางอย่าง")
		}
	}
	showData(dt){
		this.cvblock.style.backgroundImage=`url("${this.gl_dir}/${dt.icon}.png")`
		this.cv_name.innerHTML=dt.name
		this.cv_price.innerHTML=this.nb(dt.price,2)
	}
	kb(event){
		if(Cv.is_app_open){
			let act=document.activeElement.tagName
			if(act=="BODY" && Cv.listen_cm == null){
				let el=event.target		
				let key=event.code
				if(event.type=="keyup"){
					let skey=(key!=Cv.kl)?Cv.kl+""+key:Cv.kl
					
					if(Cv.k.hasOwnProperty(skey)){
						Cv.sco1=Cv.sco2
						Cv.sco2=Cv.k[skey]
						let tn=Date.now()
						let dif=tn-Cv.scs
						
						if(dif<200){
							Cv.sca+=dif
							if(Cv.sct==""){
								Cv.sct=Cv.sco1+""+Cv.sco2
							}else{
								Cv.sct+=Cv.sco2
							}
						}else{
							Cv.sct=""
							Cv.sca=0
							Cv.scs=0
						}
						Cv.scs=tn
						Cv.bc+=Cv.k[skey]
						Cv.kl=""
					}else{
						if(skey=="NumpadEnter"||skey=="Enter"){	
							let av=(Cv.sca/Cv.sct.length-1) //--NaN
							//M.l("avg="+av+">>"+S.sct)
							if(av<30&&Cv.sct.length>=4){
								Cv.insertList(Cv.sct)
							}else{
								let bc=Cv.bc.split("✂️")
								Cv.comand(bc[bc.length-1])
							}
							Cv.bc="✂️"
							Cv.kl=""
						}else if(skey=="ShiftLeft"){
							Cv.kl="ShiftLeft"
						}else{
							Cv.kl=""			
						}
					}
				}
			}else if(event.type=="keyup"&&Cv.listen_cm=="pressInput"){
				Cv.ipFloat(event,"  ")
			}
		}
	}
}
