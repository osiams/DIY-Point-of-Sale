class account extends main{
	constructor(){
		super()
		this.icon={};
		this.pay_list={}//--{bill_id:{rca_credit:xx,pay:xx},[]}
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
	xxxcheckbox(did,id){
		alert(did.checked)
	}
}
