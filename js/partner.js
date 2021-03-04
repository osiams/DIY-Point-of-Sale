"use strict"
class partner extends main{
	constructor(){
		super()
	}
	run(){
		
	}
	edit(sku_root,s_type){
		let f=document.forms.partner
		f.action="?a=partner&b=edit"
		f.sku_root.value=sku_root
		f.submit()
	}
	delete(sku_root,name){
		let y=confirm("คุณต้องการลบ "+name)
		if(y){
			let f=document.forms.partner
			f.action="?a=partner&b=delete"
			f.sku_root.value=sku_root
			f.submit()
		}
	}
}
