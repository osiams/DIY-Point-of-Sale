 /*--apt DIY_POS;--ext js;--version 0.0;*/
"use strict"
class day extends main{
	constructor(){
		super()
	}
	go(){
		let v=this.id("daydate").value
		location.href="?a=day&date="+v
	}
}
