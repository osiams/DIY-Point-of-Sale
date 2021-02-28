"use strict"
class setting extends main{
	constructor(){
		super()
		this.printerdata=null
		this.ptint="userprinter"
	}
	newInstall(docroot,dir){
		let psm=window.location.protocol+"//"+window.location.hostname+""+((window.location.port==80||window.location.port==443||window.location.port=="")?"":":"+window.location.port)+"/phpmyadmin"
		let ri=docroot
		let t=`1.เข้าไปที่ ${ psm}
2.ลบฐานข้อมูล ชื่อ diypos_0.0 
3.เข้าไปที่ ${ docroot} 
4.ลบ แฟ้ม ${ dir}
5.เริ่มต้น ใหมด้วยกสร สร้างแฟ้มชื่อที่ต้องการ แล้วนำโค๊ดโปรแกรมมาวางไว้
6.ทำเหมือนที่ได้เคยติดตั้งมาแล้ว หรือไปที่ไฟล์ อ่านฉัน.txt`
		alert(t)
	}
	setPt(printerdata,id){
		this.printerdata=printerdata
		let name_perfix=this.setNamePf(id)
		let pt=localStorage.getItem(name_perfix)
		if(pt==null){
			localStorage.setItem(name_perfix,0)
			pt=0
		}
		let name=name_perfix.substr(0,8)
		if(!this.printerdata.hasOwnProperty(name+""+pt)){
			pt=0
		}
		this.set_cookie(name_perfix, pt, 60*60*24*30*12*10)
		this.setInHtml(pt,id)
	}
	setInHtml(pt=0,id){
		this.id(id).innerHTML="ชื่อ = "+F.htmlspecialchars(printerdata["printer_"+pt]["name"])
				+"<br />,ที่อยู่ = "+F.htmlspecialchars(printerdata["printer_"+pt]["address"])
				+"<br />,ขนาดความกว้าง ="+printerdata["printer_"+pt]["width"]+" มม. "
				+"<br />,ตัดกระดาษอัตโนมัติ ="+printerdata["printer_"+pt]["cut"]
				+"<br />,เปิดลิ้นชัก ="+printerdata["printer_"+pt]["pulse"]	
				+"<br />,บรรทัดว่างชดเชยท้ายใบเสร็จ = "+printerdata["printer_"+pt]["feed"]+""
	}
	changePt(did,id){
		return this.ctPt(id)
	}
	setNamePf(id){
		let name_perfix="printer_";
		if(id=="userprinterlabel"){
			name_perfix="printer_label_";
		}
		return 	name_perfix
	}
	setPrinter(did,id){
		let name_perfix=this.setNamePf(id)
		this.set_cookie(name_perfix, did.value, 60*60*24*30*12*10)
		localStorage.setItem(name_perfix, did.value)
		this.setInHtml( did.value,id)
	}
	ctPt(id){
		let name_perfix=this.setNamePf(id)
		let pt=localStorage.getItem(name_perfix)
		let ct=this.ce("div",{"class":"changePt"})
			let p=this.ce("p",{})
			this.end(p,[this.cn("เลือกเครื่องพิมพ์")])
			let form=this.ce("form",{})
			let r=[]
			let i=0
			for (let property in this.printerdata) {
				if(property.substring(0,8)=="printer_"){
					if(this.printerdata[property]["status"]==1){
						i+=1
						r["p_"+i]=this.ce("p",{})
							r["radio_"+i]=this.ce("input",{"type":"radio","id":"radio_"+i,"name":"printer_","value":property.replace("printer_",""),"onclick":"Stt.setPrinter(this,'"+id+"')"})
							r["label_"+i]=this.ce("label",{"for":"radio_"+i})
							if(property.replace("printer_","")==pt){
								r["radio_"+i].setAttribute("checked","checked")
							}
							this.end(r["label_"+i],[this.cn(this.printerdata[property]["name"])])
						this.end(r["p_"+i],[r["radio_"+i],r["label_"+i]])
						this.end(form,[r["p_"+i]])
					//console.log(`${property}: ${object[property]}`);
					}
				}
			}
			
		this.end(ct,[p,form])
		return ct
	}
	printTest(id){
		if(id==null){
			id=""
		}
		let name_perfix=this.setNamePf(id)
		let pt=localStorage.getItem(name_perfix)
		if(pt==null){
			localStorage.setItem(name_perfix,0)
			pt=0
		}
		let dt={"data":{"a":"bill58","b":"print_test","no":pt,"for":id},"result":Stt.printTestResult,"error":Stt.printTestError}
		this.setFec(dt)
	}
	printTestResult(re,form,bt){
		if(re["result"]){

		}else{
			Stt.printTestError(re,form,bt)
		}
	}
	printTestError(re,form,bt){
		alert("❌ เกิดข้อผิดพลาด  "+re["message_error"])
	}
}
