"use strict"
class member extends main{
	constructor(){
		super()
		this.form_key=["name","lastname","sex","birthday","mb_type","tel","no","alley","road","distric","country","province","post_no","disc","icon"]
		this.form_old={}
	}
	run(){
	}
	edit(sku_root,s_type){
		let f=document.forms.member
		f.action="?a=member&b=edit&url_refer="+encodeURIComponent(window.location.href)
		f.sku_root.value=sku_root
		f.submit()
	}
	delete(sku_root,name){
		let rid=this.rid()
		let dt={"title":"โปรดยืนยัน การลบสมาชิก","msg":"คุณต้องการลบ \""+name+"\"","width":320,"callback":"Mb.delete1('"+rid+"','"+sku_root+"')","rid":rid}
		this.dialogConfirm(dt)
	}
	delete1(rid,sku_root){
		let f=document.forms.member
		f.action="?a=member&b=delete&url_refer="+encodeURIComponent(window.location.href)
		f.sku_root.value=sku_root
		f.submit()
	}
	checkIsChange(){
		let a=0;
		let f=document.forms.member
		for(let i=0;i<this.form_key.length;i++){
			if(this.form_old[this.form_key[i]]!==f[this.form_key[i]].value){
				a=1
				break
			}
		}
		if(a==0){
			let rid=this.rid()
			let dt={"msg":"ไม่มีข้อมูลเปลี่ยนแปลง","rid":rid,"callback":"M.dialogClose('"+rid+"',1)"}
			this.dialogAlert(dt)
		}
	}
	setOldData(){
		let f=document.forms.member
		for(let i=0;i<this.form_key.length;i++){
			this.form_old[this.form_key[i]]=f[this.form_key[i]].value
		}
	}
}
