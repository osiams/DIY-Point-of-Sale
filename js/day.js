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
