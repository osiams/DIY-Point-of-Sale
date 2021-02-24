"use strict"
class ws extends main{
	constructor(){
		super()
		this.wssrc = ((location.protocol == "https:")?"wss:":"ws:")+"//"+location.host+"/websocket/"
		this.ws=null
		this.mykey = ""
		this.myoto = ""
	}
	run(){
		this.ws = new WebSocket(this.wssrc)
		this.ws.onopen=(event)=>this.onOpen(event)
		this.ws.onmessage=(event)=>this.onMessage(event)
		this.ws.onerror=(event)=>this.onError(event)	
		this.tryConnect()
	}
	onMessage(event){//alert("ws---"+event.data)
		M.l(event.data)
		let data = JSON.parse(event.data)
		if(data._oto == this.myoto){
			eval(this.myoto).onMessage(data)
		}else if(data.type == "system"){
			eval(this.myoto).onMessage(data)
		}
	}
	start(){
		  
	}
	send(to,data){
		data["user_name"] = M.gck("ud","name")
		data["to"] = to
		data["key"] = this.mykey
		data["oto"] = this.myoto
		if(this.ws.readyState==1){
			this.ws.send(JSON.stringify(data))
		}
	}
	getStat(){
		let st = this.ws.readyState
		if(st == 1){
			//alert("websocket "+this.wssrc+" เปิดใช้งานอยู่")
		}
	}
	tryConnect(){
		if(this.ws.readyState == 3){
			this.ws =  new WebSocket(this.wssrc)
			setTimeout("Ws.tryConnect()",5000)
		}
	}
	statSet(button_id){
		if(this.ws.readyState == 1){
			this.id(button_id).style.backgroundColor = "LimeGreen"
			this.id(button_id).innerHTML = "ทำงานอยู่"
		}else{
			this.id(button_id).style.backgroundColor = "Silver"
			this.id(button_id).innerHTML = "ไม่ทำงาน"
		}
		setTimeout("Ws.statSet('"+button_id+"')",1000)
	}
	onError(event){
		alert("เกิดข้อผิดพลาด ในการเชื่อมต่อ "+this.wssrc);
		this.tryConnect()
	}
	onOpen(){

	}
	createKey(){
		let a="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"
		let ms=Date.now()
		let t="_"+ms
		for(let i=0;i<7;i++){
			t+=a.charAt(Math.floor(Math.random()*63))
		}
		return t
	}
}
