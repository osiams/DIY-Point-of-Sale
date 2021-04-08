"use strict"
class fileupload extends main{
	fileUploadShow(e,n,icon_id,max=1920,maxdisplay=116,type="",divuploadpre="",icon_load_id=null) {//-https://stackoverflow.com/questions/30902360/resize-image-before-sending-to-base64-without-using-img-element
		let ic=null
		let icon_ref=null
		let icon_id_str=null
		let idr=M.rid()
		if(typeof(icon_id)=="object"){
			icon_id[idr]=""
			ic=icon_id
			icon_ref=idr
			icon_id_str=icon_id.toString()
			//alert(icon_id_str)
		}else{alert(88)
			ic=M.id(icon_id)
			icon_ref=ic.id
		}
		if(icon_load_id!=null){
			ic=M.id(icon_load_id)
		}
		let canvas_id=M.rid()
		let img_id=M.rid()
		let div_id=M.rid()
		let cv=M.ce("canvas",{"id":canvas_id,"width":"50px","height":"50px","style":"display:none"})
		let dv =M.ce("div",{"id":div_id,"data-canvas_id":canvas_id})
		
		let dvl=M.ce("div",{"title":"ลบออก","onclick":`Ful.fileUploadDel(this,'${div_id}','${icon_ref}','${canvas_id}','${icon_id_str}')`})
		if(e!=null){
			if(e.target.files[0].type!="image/png"&&e.target.files[0].type!="image/jpeg"&&e.target.files[0].type!="image/gif"&&e.target.files[0].type!="image/webp"){
				alert("ไฟล์ที่เลือก ไม่รองรับ สำหรับการเลือก\nไฟล์คุณ "+ e.target.files[0].type)
				return false
			}
			if(n==1){
				M.rmc_all(e.target.parentNode.childNodes[1])
			}
			e.target.parentNode.childNodes[1].appendChild(cv)
			e.target.parentNode.childNodes[1].appendChild(dv)
				dv.appendChild(dvl)
					dvl.appendChild(M.cn("\u00D7"))
		}else if(type=="load"&&divuploadpre!=""&&M.id(divuploadpre)!=undefined&&ic.value!=""){
			M.id(divuploadpre).appendChild(cv)
			M.id(divuploadpre).appendChild(dv)
				dv.appendChild(dvl)
					dvl.appendChild(M.cn("\u00D7"))
		}else{
			M.id(divuploadpre).appendChild(cv)
		}
		let canvas=document.getElementById(canvas_id)
		let ctx=canvas.getContext("2d")
		let cw=canvas.width
		let ch=canvas.height
		let maxW=max//1920
		let maxH=max//1920
		let img = new Image()
		img.onload = function() {
			let iw=img.width
			let ih=img.height
			let scale=Math.min((maxW/iw),(maxH/ih))
			if(iw<maxW&&ih<maxH){
				scale=1
			}
			let iwScaled=iw*scale
			let ihScaled=ih*scale
			canvas.width=iwScaled
			canvas.height=ihScaled
			ctx.imageSmoothingEnabled=true;
			ctx.imageSmoothingQuality="height";
			ctx.drawImage(img,0,0,iwScaled,ihScaled)
			if(typeof(icon_id)=="object"){
				ic[idr]=canvas.toDataURL()
			}else{
				ic.value=canvas.toDataURL()
			}
			//alert(canvas.toDataURL())
			//alert(canvas)
			//document.getElementById("im").src=canvas.toDataURL()
			setTimeout(Ful.fileUploadPain,0,div_id,n,canvas_id,max,maxdisplay)
		}
		if(e!=null){
			//document.getElementById(img_id).src=URL.createObjectURL(e.target.files[0]);
			img.src = URL.createObjectURL(e.target.files[0]);	

		}else if(type=="load" && ic.value!=""){
			img.src = ic.value
		}
		//alert(img.src)
		//document.getElementById(img_id).src=canvas.toDataURL()
		//setTimeout(F.fileUploadPain,10,div_id,canvas_id)
		
	
		//img.src = "https://i.ytimg.com/vi/HLt7Ze-JUPM/hqdefault.jpg?sqp=-oaymwEiCNIBEHZIWvKriqkDFQgBFQAAAAAYASUAAMhCPQCAokN4AQ==&rs=AOn4CLCT1cUDWu32RiYDo8r9qKFv03MENw"
	}
	fileUploadDel(did,div_id,icon_id,canvas_id,icon_id_=null){
		//alert(icon_id_)
		if(M.id(icon_id)!=undefined){
			M.id(icon_id).value=""
		}else if(icon_id_!=null &&typeof(icon_id_)=="object" &&icon_id_.hasOwnProperty(icon_id)){
			delete icon_id_[icon_id]
		}	
		M.id(div_id).parentNode.removeChild(M.id(div_id))
		M.id(canvas_id).parentNode.removeChild(M.id(canvas_id))
	}
	fileUploadPain(div_id,n,canvas_id,max,maxdisplay){
		document.getElementById(div_id).style.backgroundImage="url(\""+document.getElementById(canvas_id).toDataURL()+"\")"
		document.getElementById(div_id).style. backgroundRepeat="no-repeat"
		document.getElementById(div_id).style.backgroundPosition="center center"
		
		let wi=document.getElementById(canvas_id).width
		let hi=document.getElementById(canvas_id).height
		let h=0
		let w=0
		if((wi==max||hi==max)
			||(wi>=maxdisplay||hi>=maxdisplay)){
			if(wi>=hi){
				h=maxdisplay
				w=(wi/hi)*h
			}else{
				w=maxdisplay
				h=(hi/wi)*w
			}
		}else{
			if(wi>=hi){
				h=hi
				w=(wi/hi)*h
			}else{
				w=wi
				h=(hi/wi)*w
			}
		}
		document.getElementById(div_id).style.backgroundSize=w+"px "+h+"px"
	}
	fileUploadImgs(uploadtype="new",table,key,data,obj_str,url_to=null,url_key=null,callbackresult=null,callbackerror=null){
		//let obj=eval(obj_str)
		let obj=Object.assign({},eval(obj_str))
		let count=Object.keys(obj).length		
		let obj_str_static=JSON.stringify(obj)
		if(uploadtype=="new"){
			if(count>0){
				this.fileUploadImgsPc(1,count)
				this.fileUploadImgsSend(uploadtype,table,key,data,obj_str,obj_str_static,url_to,url_key,callbackresult,callbackerror,count,0)
			}
		}else if(uploadtype=="add"){
			if(count>0){
				this.fileUploadImgsPc(1,count)
				this.fileUploadImgsSend(uploadtype,table,key,data,obj_str,obj_str_static,url_to,url_key,callbackresult,callbackerror,count,0)
			}
		}
	}
	fileDeleteImgs(dt){
		let uploadtype="delete"
		let table=dt.table
		let display_id=dt.display_id
		let key=dt.key
		let key_data=dt.key_data
		let obj_str=dt.obj_str
		let gl_obj_str=dt.gl_obj_str
		let callbackresult=dt.callbackresult
		let callbackerror=dt.callbackerror
		let gl_obj=Object.assign({},eval(gl_obj_str))
		//M.l(eval(gl_obj_str))
		//M.l(gl_obj)
		let count=Object.keys(gl_obj).length		
		if(uploadtype=="delete"){
			let count_gl=Object.keys(gl_obj).length	
			if(count_gl>0){
				this.fileDeleteImgsPc(1,count)
				let gl_obj_str_static=JSON.stringify(gl_obj)
				this.fileUploadImgsSend(
					uploadtype	,table		,key				,key_data		,gl_obj_str,	gl_obj_str_static,
					''					,''				,callbackresult	,callbackerror	,
					count_gl		,0
				)
			}
		}
	}
	fileUploadImgsSend(uploadtype,table,key,data,obj_str,obj_str_static,url_to,url_key,callbackresult,callbackerror,count,index){
		let i=-1;
		let icon=""
		let obj=JSON.parse(obj_str_static)
		
		for(let prop in obj){
			i+=1
			if(i==index){
				if(uploadtype!="delete"){
					icon=obj[prop]
				}else{
					icon=prop
				}	
				break;
			}
		}
		let dt={"data":{"a":"fileupload","table":table,"key":key,"data":data,"icon":icon,"index":index,"obj_str":obj_str,"obj_str_static":obj_str_static,
			"url_to":url_to,"url_key":url_key,"count":count,"uploadtype":uploadtype,"callbackresult":callbackresult,"callbackerror":callbackerror},"result":Ful.uploadResult,"error":Ful.uploadError}		
		this.setFec(dt)
	}
	fileUploadImgsPc(s,count){
		if(s==1){
			this.b.style.overflow="hidden"
			let top=window.scrollY
			let ct=this.ce("div",{"id":"fileuploadpc","class":"furp","style":"top:"+top+"px"})
			let d=this.ce("div",{})
				let p1=this.ce("p",{})
					let s1=this.ce("span",{"id":"fileuploadfinish"})
					this.end(s1,[this.cn("0")])
					let s2=this.ce("span",{})
					this.end(s2,[this.cn("/"+count)])
				this.end(p1,[s1,s2])	
			this.end(d,[p1,this.cn("กำลังอัปโหลดรูป อย่าปิดหน้านี้")])
			this.end(ct,[d])
			this.end(M.b,[ct])		
		}else if(s==0){
			if(count!=0){
				this.b.style.overflow="auto"
			}
			this.id("fileuploadpc").className="furo"
			this.id("fileuploadpc").parentNode.removeChild(this.id("fileuploadpc"))
			
		}
	}
	uploadResult(re,form,bt){
		if(re["result"]){
			let icon_name=re["icon_name"]
			let uploadtype=form.get("uploadtype")
			let table=form.get("table")
			let key=form.get("key")
			let obj_str=form.get("obj_str")
			let obj_str_static=form.get("obj_str_static")
			let url_to=form.get("url_to")
			let url_key=form.get("url_key")
			let count=form.get("count")*1
			let index=form.get("index")*1
			let data=form.get("data")
			let callbackresult=form.get("callbackresult")
			let callbackerror=form.get("callbackerror")
			let idx=index+1
			Ful.setUpResult(count,index,form,icon_name)
			//alert("idx="+idx+";count="+count)
			if(idx<count){
				Ful.fileUploadImgsSend(uploadtype,table,key,data,obj_str,obj_str_static,url_to,url_key,callbackresult,callbackerror,count,idx)
			}/*else{
				
				if(uploadtype=="new"||uploadtype=="add"){
					Ful.fileUploadImgsPc(0,count)
				}else if(uploadtype=="delete"){
					Ful.fileDeleteImgsPc(0,0)
				}
			}*/
		}else{
			Ful.uploadError(re,form,bt)
		}
	}
	uploadError(re,form,bt){
		let index=form.get("index")
		let obj_str=form.get("obj_str")
		let callbackerror=form.get("callbackerror")
		eval(obj_str+"={}")

		let n=index*1+1
		alert("รูปที่ "+(n)+" เกิดข้อผิดพลาด\nไม่สามารถ ส่งไฟล์ไปยัง \n"+window.location.origin+"\n*รูปภาพถูกส่งสำเร็จแล้ว "+(n-1)+" รูป")
		Ful.fileUploadImgsPc(0,0)
		//alert(callbackerror)
		eval(callbackerror)
	}
	setUpResult(count,index,form,icon_name){
		let uploadtype=form.get("uploadtype")
		let callbackresult=form.get("callbackresult")
		if(uploadtype=="new"){
			this.id("fileuploadfinish").innerHTML=(index+1)
			eval(callbackresult)	
		}else if(uploadtype=="add"){
			this.id("fileuploadfinish").innerHTML=(index+1)
			eval(callbackresult)	
		}else if(uploadtype=="delete"){
			this.id("filedeletefinish").innerHTML=(index+1)
			eval(callbackresult)	
		}
		if(index==count-1){
			let data=form.get("data")
			let url_to=form.get("url_to")
			let url_key=form.get("url_key")
			
			alert("สำเร็จ")
			if(url_to!=null&&url_to.length>0){
				location.href=url_to+"&"+url_key+"="+data
			}else if(callbackresult!=null&&callbackresult.length>0){
				let obj_str=form.get("obj_str")

				eval(obj_str+"={}")
				if(uploadtype=="new"){
					Ful.fileUploadImgsPc(0,0)
				}else if(uploadtype=="add"){
					Ful.fileUploadImgsPc(0,0)
				}else{
					Ful.fileDeleteImgsPc(0,0)
				}
				
			}
		}else{
			
		}
	}
	ctFileUploadsDialog(icon_ob_str){
		let ct=this.ce("div",{})
			let p=this.ce("p",{})
			let d1=this.ce("div",{"class":"fileuploadpres c","id":"div_fileuploadpre"})
			let ip=this.ce("input",{"id":"upload_pic","type":"file","acept":"image/png,image/gif,image/jpeg,image/webp","class":"fuif","name":"picture","onchange":"Ful.fileUploadShow(event,20,"+icon_ob_str+",1024,160)"})
		this.end(ct,[p,d1,ip])
		return ct
	}
	fileDeleteImgsPc(s,count){
		if(s==1){
			this.b.style.overflow="hidden"
			let top=window.scrollY
			let ct=this.ce("div",{"id":"filedeletepc","class":"fdrp","style":"top:"+top+"px"})
			let d=this.ce("div",{})
				let p1=this.ce("p",{})
					let s1=this.ce("span",{"id":"filedeletefinish"})
					this.end(s1,[this.cn("0")])
					let s2=this.ce("span",{})
					this.end(s2,[this.cn("/"+count)])
				this.end(p1,[s1,s2])	
			this.end(d,[p1,this.cn("กำลังลบรูป อย่าปิดหน้านี้")])
			this.end(ct,[d])
			this.end(M.b,[ct])		
		}else if(s==0){
			if(count!=0){
				this.b.style.overflow="auto"
			}
			this.id("filedeletepc").className="furo"
			this.id("filedeletepc").parentNode.removeChild(this.id("filedeletepc"))
		}
	}
}
