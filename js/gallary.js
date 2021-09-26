"use strict"
class gallary extends main{
	constructor(main){
		super()
		this.set_click=0;
	}
	run(){

	}
	viewImg(did){
		if(this.set_click==1){
			this.set_click=0
		}else{
			let a=did.style.backgroundImage.split("\"");
			let b=a[1].split("/");
			let c=b[b.length-1]
			let d=new Image();
			let s=""
			for(let i=0;i<b.length-1;i++){
				s=s+"/"+b[i]
			}
			s=s+"/32x32_"+c
			d.src=s
			G.view(d)
		}
	}
	setImg(did,table,pd_root,img_key,gl_stat,glm){
		this.set_click=1
		//alert(table+"-"+pd_root+"-"+img_key)
		let a=did.parentNode.style.backgroundImage.split("\"");
		let b=a[1].split("/");
		let img0=b[b.length-1]
		let rid = this.rid()
		let ct=this.ce("div",{"class":"gallary_view_set"})
			let divimg=this.ce("div",{"class":"div_img","title":"กดเพื่อดูภาพขนาดใหญ่"})
				let img=this.ce("img",{"src":this.gl_dir+"/64x64_"+img0,"onclick":"G.view(this)"})
			this.end(divimg,[img])	
		this.end(ct,[divimg])	
			let d=this.ce("div",{"class":"gallary_set"})
				let ds=this.ce("div",{"class":"l"})
				this.end(ds,[this.cn("มิติภาพ กว้าง x สูง ")])
				let dsv=this.ce("div",{"class":"l"})
				let dst=this.cn(
					" ("+this.nb(glm[img_key].width,0)+" x "+this.nb(glm[img_key].height,0)+")px"
				)
				this.end(dsv,[dst])
				let dd=this.ce("div",{"class":"l"})
				this.end(dd,[this.cn("บันทึก เวลา  ")])
				let ddv=this.ce("div",{"class":"l"})
				let ddt=this.cn(
					glm[img_key].date_reg
				)
				this.end(ddv,[ddt])
				let db=this.ce("div",{"class":"l"})
				this.end(db,[this.cn("บันทึก โดย  ")])
				let dbv=this.ce("div",{"class":"l"})
				let dbt=this.cn(
					glm[img_key].by
				)
				this.end(dbv,[dbt])
			this.end(d,[ds,dsv,dd,ddv,db,dbv])
		this.end(ct,[d])
		let tuse=""
		if(gl_stat=="0"){
			tuse="ใช้รูป"
		}else if(gl_stat=="1"){
			tuse="ไม่ใช้"
		}
		let bts = [
			{"value":"🗑 ลบ","onclick":"Ga.delImg(this,'"+table+"','"+pd_root+"','"+img_key+"')"},
			{"value":"🏷 รูปหลัก","id":"pressbuttoninputok","onclick":"Ga.setPrimaryImg(this,'"+table+"','"+pd_root+"','"+img_key+"')"},
			{"value":"🏷 "+tuse,"onclick":"Ga.dontUseImg(this,'"+table+"','"+pd_root+"','"+img_key+"','"+gl_stat+"')"}
		]
		this.dialog({"rid":rid,"display":1,"bts":bts,"ct":ct,"title":"รายละเเอียด","width":"250","ofc":1})
	}
	delImg(did,table,pd_root,img_key){
		let dt={"data":{"a":"gallary","table":table,"sku_root":pd_root,"gl_key":img_key,
			"acttype":"delete"},"result":Ga.delResult,"error":Ga.delError}		
		this.setFec(dt)
	}
	delResult(re,form,bt){
		M.dialogClose()
		if(re["data"]!=""){
			let id="IMG_"+re["data"]
			let ob=M.id(id)
			ob.parentNode.removeChild(ob)
		}
	}
	delError(re,form,bt){
		
	}
	dontUseImg(did,table,pd_root,img_key,gl_stat){
		let gl_statt="";
		if(gl_stat=="0"){
			gl_statt="1"
		}else if(gl_stat=="1"){
			gl_statt="0"
		}
		let dt={"data":{"a":"gallary","table":table,"sku_root":pd_root,"gl_key":img_key,
			"acttype":"drop","gl_stat":gl_statt},"result":Ga.dontUseResult,"error":Ga.dontUseError}		
		this.setFec(dt)
	}
	dontUseResult(re,form,bt){
		window.location.reload()
	}
	dontUseError(re,form,bt){
		
	}
	setPrimaryImg(did,table,pd_root,img_key,gl_stat){
		let dt={"data":{"a":"gallary","table":table,"sku_root":pd_root,"gl_key":img_key,
			"acttype":"primary"},"result":Ga.setPrimaryResult,"error":Ga.setPrimaryError}		
		this.setFec(dt)
	}
	setPrimaryResult(re,form,bt){
		window.location.reload()
	}
	setPrimaryError(re,form,bt){
		
	}
}
