"use strict"
class time extends main{
	constructor(){
		super()
	}
	run(){
	}
	logout(){
		let f=document.forms.time
		f.b.value="logout"
		f.submit()
	}
	newTimeSubmit(){
		let f=document.forms.time
		f.b.value="regis"
		f.submit()
	}
}
