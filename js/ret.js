"use strict"
class ret extends main{
	constructor(){
		super()
		this.ret=document.forms.ret;
	}
	submit(did){
		//let dtx={"data":{"a":"bill58","b":"print_ret","date":"2020-05-30 05:20:16","sku":"000000012"},"result":Rt.printResult,"error":Rt.printError}
	//M.setFec(dtx);
		//M.l(did)
		let dt={"data":{},"result":Rt.submitResult,"error":Rt.submitError}
		for(let i=0;i<did.form.length;i++){
			dt.data[did.form[i].name]=did.form[i].value
		}
		this.l(did.form)
		this.setFec(dt);
	}
	submitResult(re,form,bt){
		if(re["result"]){
			if(re["resp"]=="PCF"){
				M.l(re)
				Rt.plessConfirm(re["data"],re["ch2"])
			}else{
				let dt={"data":{"a":"bill58","b":"print_ret","date":re["date"],"sku":re["sku"]},"result":Rt.printResult,"error":Rt.printError}
				M.setFec(dt);
				alert("✅ เรียบร้อย");
			}
		}else{
			Rt.submitError(re,form,bt)
		}
	}
	printResult(re,form,bt){ 
		if(re["result"]){
			location.href="?a=bills&c=ret&b=view&sku="+re["sku"]
		}else{
			Rt.printError(re,form,bt)
		}
	}
	printError(re,form,bt){
		alert("❌ พิมพ์ใบเร็จ ไม่สำเร็จ\n\n"+re["message_error"])
		location.href="?a=bills&c=ret&b=view&sku="+re["sku"]
	}
	submitError(re,form,bt){
		alert("❌ "+re["message_error"])
	}
	plessConfirm(dt,ch){
		let ch2=(ch==1)?"สินค้าแบบเดิม":"เงิน"
		let t="คุณต้องการคืนสินค้า เป็น "+ch2+"\n";
		let sums=0
		let ls=0;
		//M.l(dt);
		for(let i=0;i<dt.length;i++){
			ls+=1;
			let sum=dt[i]["price"]*dt[i]["n"]
			sums+=sum
			t+="\n"+dt[i]["name"]+"\n"+dt[i]["price"]+" บ.\t x "+dt[i]["n"]+" \tเป็นเงิน "+sum+" บ.\n"
		}
		t+="\n---------------------------------------- \nจำนวน "+ls+" รายการ\tรวมเป็นเงิน "+sums+"บ."
		let c=confirm(t);
		if(c){
			document.forms.ret.confirm.value="ok"
			document.forms.ret[document.forms.ret.length-1].click()
		}
	}
	infoLot(){
		let ct=this.ce("div",{})
			let dv=this.ce("div",{"id":"ret_view_lot","class":"size14"})
			this.end(dv,[this.cn("กำลังดึงข้อมูล"),this.ce("br",{}),this.ce("br",{})])
		this.end(ct,[dv])
		return ct
	}
	infoLotFetch(did,lot,pd_sku_root){
		let dt={"data":{},"result":Rt.viewLotResult,"error":Rt.viewLotError}
		dt["data"]["a"]="ret"
		dt["data"]["b"]="viewlot"
		dt["data"]["lot"]=lot
		dt["data"]["pd_sku_root"]=pd_sku_root
		this.setFec(dt);
	}
	viewLotResult(re,form,bt){ 
		if(re["result"]){
			M.id("ret_view_lot").innerHTML="<b>เป็นสินค้าของงวด </b><br />"+re["data"]
		}
	}
	viewLotError(re,form,bt){
	
	}
}
