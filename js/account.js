class account extends main{
	constructor(){
		super()
		this.icon={};
		this.pay_list={}//--{bill_id:{rca_credit:xx,pay:xx},[]}

		
	}
	accountRcaRun(){
		this.o_form_rca_pay=document.forms["account_pay"]
		this.o_pay_block=this.id("account_rca_payu")
		this.o_pay=this.o_form_rca_pay.pay
		this.s_sku_root=this.o_form_rca_pay.sku_root.value
		this.f_rca_old=this.o_pay.getAttribute("data-pay_old")*1
		this.o_rca_balance=this.id("account_rca_balance");
		this.a_checkbox=this.o_form_rca_pay.querySelectorAll("[type=checkbox]");
		this.o_pay.addEventListener("keyup",Ac.paySetRca)
		this.o_pay.addEventListener("paste",Ac.paySetRca)
		this.o_pay.addEventListener("drop",Ac.paySetRca)
		
	}
	paySetRca(){
		if(event.type=="drop"){
			event.target.value=event.dataTransfer.getData("text")
		}
		Ac.o_pay_block.style.display="none"
		Ac.checkSet(0)
		if(this.value*1>=0){
			let balance=(Ac.f_rca_old-this.value*1)
			Ac.o_rca_balance.innerHTML=M.nb(balance)
		}
	}
	toPay(member_id){
		let f=document.forms.account
		f.action="?a=account&b=account_rca&c=pay&url_refer="+encodeURIComponent(window.location.href)
		f.member_id.value=member_id
		f.submit()
	}
	paySetListPay(ob){
		this.pay_list=ob
	}
	pay(){
		if(this.o_pay.value*1<=0){
			alert("ยังไม่ได้ระบุจำนวนที่ต้องการชำระ")
		}else if(this.o_pay.value*1>this.f_rca_old){
			alert("จำนวนที่ต้องการชำระ มากกว่าจำนวนค้างชำระ")
		}else{
			if(this.o_pay_block.style.display!="block"){
				this.checkSet(1)
				this.o_pay_block.style.display="block"
			}else{
				let payu=this.getPayuOb()
				let payu_sum=this.payuSum(payu)
				let dt={"data":{"a":"account","b":"account_rca","c":"pay_rca","pay":this.o_pay.value*1,"payu":JSON.stringify(payu),"sku_root":this.s_sku_root},
					"result":Ac.payResult,"error":Ac.payError}
				this.setFec(dt)
			}
		}
	}
	payResult(re,form,bt){
		if(re["result"]){
			
		}else{
			Ac.payError(re,form,bt)
		}
	}
	payError(re,form,bt){
		alert(re["message_error"])
	}
	payuSum(payu){
		let s=0
		for(let prop in payu){
			s=(s*100+(payu[prop]*100))/100
		}
		return s
	}
	checkSet(display=1){
		let bl=this.o_pay.value*1
		if(display==1){
			for(let i=0;i<this.a_checkbox.length;i++){
				let cd=this.a_checkbox[i].getAttribute("data-rca_credit")*1
				let y=this.id("account_div_pay_"+this.a_checkbox[i].value)
				let z=this.id("account_pay_"+this.a_checkbox[i].value)		
				M.l(y)		
				if(bl>0){
					y.style.display="block"
					if(bl>=cd){
						this.a_checkbox[i].checked=true
						z.innerHTML=this.nb(cd,2,"")
						bl=bl-cd
					}else{
						this.a_checkbox[i].checked=true
						z.innerHTML=this.nb(bl,2,"")
						bl=0
					}
				}else{
					y.style.display="none"
					this.a_checkbox[i].checked=false
					
				}
			}
		}else{
			for(let i=0;i<this.a_checkbox.length;i++){
				let y=this.id("account_div_pay_"+this.a_checkbox[i].value)
				y.style.display="none"
				this.a_checkbox[i].checked=false
			}
		}
	}
	getPayuOb(){
		let re={}
		let f=this.o_form_rca_pay
		let payu_has=F.valueListToArray(f.payu_list.value)
		for(let i=0;i<payu_has.length;i++){
			re["payu_"+payu_has[i]]=f["payu_"+payu_has[i]].value*1
		}
		return re
	}
}
