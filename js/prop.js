"use strict"
class prop extends main{
	constructor(){
		super()
		this.a = "prop"
	}
	edit(sku_root){
		let f=document.forms.prop
		f.action="?a="+this.a+"&b=edit"
		f.sku_root.value=sku_root
		f.submit()
	}
	delete(sku_root,name){
		let y=confirm("คุณต้องการลบ คุณสมบัติ \n\""+name+"\"\nรายการกลุ่ม และสินค้าที่เคยมีคุณสมบัตนี้ิ จะไม่มีคุณสมบัตินี้ อีกต่อไป")
		if(y){
			let f=document.forms.prop
			f.action="?a="+this.a+"&b=delete"
			f.sku_root.value=sku_root
			f.submit()
		}
	}
}
