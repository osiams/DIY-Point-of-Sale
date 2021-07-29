"use strict"
class main{
	constructor(){
		this.ob_query = new URLSearchParams(window.location.search);
		this.app_name="Do It Yourself Point Of Sale"
		this.app_disc="ระบบขายสินค้า ทำด้วยตัวเอง"
		this.s_g_home="Home"
		this.s_g_win="Windows"
		this.s_g_linux_lite="Linux_Lite"
		this.s_page_default="home"
		this.step=0
		this.observer=null
		this.ob_page={}
		this.ob_page["home_____________________"]={
			"nt":"หน้าหลัก",
			"nf":"home.js",
			"di":[],
			"as":null}
		this.ob_page["pos______________________"]={
			"nt":"การติดตั้ง DIY POS",
			"nf":"install_DIY_POS_home.js",
			"hd":"ติดตั้ง DIY POS โปรแกรมขายสินค้าหน้ร้าน",
			"ts":"โปรดเลือก รุ่น",
			"ty":"select",
			"di":[],
			"as":null}
			this.ob_page["pos__0.6_________________"]={
				"nt":"V0.6",
				"nf":"DIY_POS_v0.6_home.js",
				"hd":"DIY POS v0.6",
				"ts":"โปรดเลือก ระบบ",
				"ty":"select",
				"di":["pos______________________"],
				"as":null}
				this.ob_page["pos__0.6__win____________"]={
					"nt":"Windows",
					"nf":"DIY_POS_v0.6_Windows_home.js",
					"hd":"ติดตั้ง บน Windows",
					"ts":"โปรดเลือก ทำตามลำดับ ",
					"ty":"order",
					"di":["pos______________________","pos__0.6_________________"],
					"as":null}
						this.ob_page["pos_v6_win_install_xampp"]={
							"nt":"ติดตั้ง XAMPP",
							"nf":"DIY_POS_v0.6_Windows_install_XAMPP.js",
							"di":["pos______________________","pos__0.6_________________","pos__0.6__win____________"],
							"as":null}
						this.ob_page["pos_v0.6_win_shortcut-axampp"]={
							"nt":"ทำ Shortcut เปิด XAMPP Control Panel",
							"nf":"DIY_POS_v0.6_Windows_shortcut_XAMPP.js",
							"di":["pos______________________","pos__0.6_________________","pos__0.6__win____________"],
							"as":null}		
						this.ob_page["pos_v6_win_set_https"]={
							"nt":"ทำ https:// และ ชื่อ Domain",
							"nf":"DIY_POS_v0.6_Windows_https.js",
							"di":["pos______________________","pos__0.6_________________","pos__0.6__win____________"],
							"as":null}
						this.ob_page["pos_v6_win_set_php-ini"]={
							"nt":"ตั้งค่า php.ini",
							"nf":"DIY_POS_v0.6_Windows_phpini.js",
							"di":["pos______________________","pos__0.6_________________","pos__0.6__win____________"],
							"as":null}
						this.ob_page["pos_v6_win_set_wss-ws"]={
							"nt":"ตั้งค่า ws:// และ wss:// ให้ Server",
							"nf":"DIY_POS_v0.6_Windows_wss.js",
							"di":["pos______________________","pos__0.6_________________","pos__0.6__win____________"],
							"as":null}
						this.ob_page["pos_v6_win_set_root-password"]={
							"nt":"ตั้งรหัสผ่าน ให้ root ในฐานข้อมูล",
							"nf":"DIY_POS_v0.6_Windows_dbmr.js",
							"di":["pos______________________","pos__0.6_________________","pos__0.6__win____________"],
							"as":"DIY_POS_v0.6_Linux_Lite_dbmr.js"}
						this.ob_page["pos__0.6__win__adusr_____"]={
							"nt":"เพิ่มผู้ใช้ ในฐานข้อมูล",
							"nf":"DIY_POS_v0.6_Windows_mradduser.js",
							"di":["pos______________________","pos__0.6_________________","pos__0.6__win____________"],
							"as":null}
						this.ob_page["pos__0.6__win__mrupg_____"]={
							"nt":"ทำ Mysql Upgrade",
							"nf":"DIY_POS_v0.6_Windows_mysql_upgrad.js",
							"di":["pos______________________","pos__0.6_________________","pos__0.6__win____________"],
							"as":null}
						this.ob_page["pos__0.6__win__share_____"]={
							"nt":"ตั้งค่า ให้อุปกรณ์ในเครือข่ายได้ LAN เดียวกันติดต่อเข้ามาได้",
							"nf":"DIY_POS_v0.6_Windows_alowc.js",
							"di":["pos______________________","pos__0.6_________________","pos__0.6__win____________"],
							"as":null}
						this.ob_page["pos__0.6__win__setIP_____"]={
							"nt":"ตั้งค่า IP เครื่อง ตามต้องการ",
							"nf":"DIY_POS_v0.6_Windows_set-ip.js",
							"di":["pos______________________","pos__0.6_________________","pos__0.6__win____________"],
							"as":null}
						this.ob_page["pos__0.6__win__set-diy-pos_____"]={
							"nt":"ติดตั้ง โปรแกรม DIY POS",
							"nf":"DIY_POS_v0.6_Windows_setdiypos.js",
							"di":["pos______________________","pos__0.6_________________","pos__0.6__win____________"],
							"as":null}
						this.ob_page["pos_v0.6_Windows_shortcut-diypos"]={
							"nt":"ทำ Shortcut เปิด DIY POS Web Application",
							"nf":"DIY_POS_v0.6_Windows_shortcut_diypos.js",
							"di":["pos______________________","pos__0.6_________________","pos__0.6__win____________"],
							"as":null}
						this.ob_page["pos_v0.6_Windows_shortcut-filediypos"]={
							"nt":"ทำ Shortcut เปิด แฟ้ม source code DIY POS Web Application",
							"nf":"DIY_POS_v0.6_Windows_shortcut_filediypos.js",
							"di":["pos______________________","pos__0.6_________________","pos__0.6__win____________"],
							"as":null}
				this.ob_page["pos__0.6__lnl____________"]={
					"nt":"Linux Lite",
					"nf":"DIY_POS_v0.6_Linux_Lite_home.js",
					"hd":"ติดตั้ง บน Linux Lite",
					"ts":"โปรดเลือก ทำตามลำดับ ",
					"ty":"order",
					"di":["pos______________________","pos__0.6_________________"],
					"as":null}
					this.ob_page["pos__0.6__lnl__isxam_____"]={
						"nt":"ติดตั้ง XAMPP",
						"nf":"DIY_POS_v0.6_Linux_Lite_install_XAMPP.js",
						"di":["pos______________________","pos__0.6_________________","pos__0.6__lnl____________"],
						"as":null}
					this.ob_page["pos_v0.6_linux-Light_shortcut-axampp"]={
						"nt":"ทำ Shortcut เปิด XAMPP Control Panel",
						"nf":"DIY_POS_v0.6_Linux_Lite_shortcut_XAMPP.js",
						"di":["pos______________________","pos__0.6_________________","pos__0.6__lnl____________"],
						"as":null}
					this.ob_page["pos__0.6__lnl__https_____"]={
						"nt":"ทำ https:// และ ชื่อ Domain",
						"nf":"DIY_POS_v0.6_Linux_Lite_https.js",
						"di":["pos______________________","pos__0.6_________________","pos__0.6__lnl____________"],
						"as":null}
					this.ob_page["pos__0.6__lnl__setphp_____"]={
						"nt":"ตั้งค่า php.ini",
						"nf":"DIY_POS_v0.6_Linux_Lite_phpini.js",
						"di":["pos______________________","pos__0.6_________________","pos__0.6__lnl____________"],
						"as":null}
					this.ob_page["pos__0.6__lnl__setws_____"]={
						"nt":"ตั้งค่า ws:// และ wss:// ให้ Server",
						"nf":"DIY_POS_v0.6_Linux_Lite_wss.js",
						"di":["pos______________________","pos__0.6_________________","pos__0.6__lnl____________"],
						"as":null}
					this.ob_page["pos__0.6__lnl__smrrt_____"]={
						"nt":"ตั้งรหัสผ่าน ให้ root ในฐานข้อมูล",
						"nf":"DIY_POS_v0.6_Linux_Lite_dbmr.js",
						"di":["pos______________________","pos__0.6_________________","pos__0.6__lnl____________"],
						"as":null}
					this.ob_page["pos__0.6__lnl__adusr_____"]={
						"nt":"เพิ่มผู้ใช้ ในฐานข้อมูล",
						"nf":"DIY_POS_v0.6_Linux_Lite_mradduser.js",
						"di":["pos______________________","pos__0.6_________________","pos__0.6__lnl____________"],
						"as":null}
					this.ob_page["pos__0.6__lnl__mrupg_____"]={
						"nt":"ทำ Mysql Upgrade",
						"nf":"DIY_POS_v0.6_Linux_Lite_mysql_upgrad.js",
						"di":["pos______________________","pos__0.6_________________","pos__0.6__lnl____________"],
						"as":null}
					this.ob_page["pos__0.6__lnl__setpr_____"]={
						"nt":"ตั้งค่า กลุ่มผู้ใช้ให้เครื่องพิมพิ์",
						"nf":"DIY_POS_v0.6_Linux_Lite_group_printer.js",
						"di":["pos______________________","pos__0.6_________________","pos__0.6__lnl____________"],
						"as":null}
					this.ob_page["pos__0.6__lnl__setpo_____"]={
						"nt":"ทำ port ให้เป็นชื่ออ่นๆ คงที่",
						"nf":"DIY_POS_v0.6_Linux_Lite_sttport.js",
						"di":["pos______________________","pos__0.6_________________","pos__0.6__lnl____________"],
						"as":null}
					this.ob_page["pos__0.6__lnl__share_____"]={
						"nt":"ตั้งค่า ให้ติดต่อกับอุปกรณ์ในเครือข่ายได้ LAN ",
						"nf":"DIY_POS_v0.6_Linux_Lite_alowc.js",
						"di":["pos______________________","pos__0.6_________________","pos__0.6__lnl____________"],
						"as":null}
					this.ob_page["pos__0.6__lnl__setIP_____"]={
						"nt":"ตั้งค่า IP เครื่อง ตามต้องการ",
						"nf":"DIY_POS_v0.6_Linux_Lite_set-ip.js",
						"di":["pos______________________","pos__0.6_________________","pos__0.6__lnl____________"],
						"as":null}
					this.ob_page["pos__0.6__lnl__set-diy-pos_____"]={
						"nt":"ติดตั้ง โปรแกรม DIY POS",
						"nf":"DIY_POS_v0.6_Linux_Lite_setdiypos.js",
						"di":["pos______________________","pos__0.6_________________","pos__0.6__lnl____________"],
						"as":null}
					this.ob_page["pos_v0.6_linux-Light_shortcut-diypos"]={
						"nt":"ทำ Shortcut เปิด DIY POS Web Application",
						"nf":"DIY_POS_v0.6_Linux_Lite_shortcut_diypos.js",
						"di":["pos______________________","pos__0.6_________________","pos__0.6__lnl____________"],
						"as":null}
					this.ob_page["pos_v0.6_linux-Light_shortcut-filediypos"]={
						"nt":"ทำ Shortcut เปิด แฟ้ม source code DIY POS Web Application",
						"nf":"DIY_POS_v0.6_Linux_Lite_shortcut_filediypos.js",
						"di":["pos______________________","pos__0.6_________________","pos__0.6__lnl____________"],
						"as":null}
		this.ob_page["use"]={
			"nt":"การใช้งาน DIY POS",
			"nf":"use.js",
			"hd":"การใช้งาน DIY POS โปรแกรมขายสินค้าหน้ร้าน",
			"ts":"โปรดเลือกตามสิงที่ต้องการรู้",
			"di":[],
			"as":null}
				this.ob_page["user_frist"]={
					"nt":"เริ่มต้นใช้งานหลังการติดตั้ง",
					"nf":"use_first.js",
					"hd":"เริ่มต้นใช้งานหลังการติดตั้ง",
					"ts":"โปรดเลือกทำตามลำดับ",
					"di":["use"],
					"as":null}
					this.ob_page["user_frist_change-password"]={
						"nt":"เปลี่ยนรหัสผ่านใหม่สำหรับผู้ดูแลระบบ",
						"nf":"use_first_change-password.js",
						"di":["use","user_frist"],
						"as":null}
					this.ob_page["user_frist_add_user"]={
						"nt":"เพิ่มผู้ใช้งาน",
						"nf":"use_first_add-user.js",
						"di":["use","user_frist"],
						"as":null}
					this.ob_page["user_frist_add_partner"]={
						"nt":"กรอกข้อมูล คู่ค้า ไว้เลือก",
						"nf":"use_first_add-partner.js",
						"di":["use","user_frist"],
						"as":null}
					this.ob_page["user_frist_add_unit"]={
						"nt":"กรอกข้อมูล หน่วยสินค้า ไว้เลือก",
						"nf":"use_first_add-unit.js",
						"di":["use","user_frist"],
						"as":null}
					this.ob_page["user_frist_add_prop"]={
						"nt":"กรอกข้อมูล คุณสมบัติสินค้า ไว้เลือก",
						"nf":"use_first_add-prop.js",
						"di":["use","user_frist"],
						"as":null}
					this.ob_page["user_frist_add_group"]={
						"nt":"กรอกข้อมูล กลุ่มสินค้า ไว้เลือก",
						"nf":"use_first_add-group.js",
						"di":["use","user_frist"],
						"as":null}
					this.ob_page["user_frist_add_stock"]={
						"nt":"กรอกข้อมูล คลังสินค้า ไว้เลือก",
						"nf":"use_first_add-stock.js",
						"di":["use","user_frist"],
						"as":null}
					this.ob_page["user_frist_add_product"]={
						"nt":"กรอกข้อมูล สินค้า ไว้เลือก",
						"nf":"use_first_add-product.js",
						"di":["use","user_frist"],
						"as":null}
					this.ob_page["user_frist_add_payu"]={
						"nt":"กรอกข้อมูล รูปแบบการชำระ ไว้เลือก",
						"nf":"use_first_add-payu.js",
						"di":["use","user_frist"],
						"as":null}
					this.ob_page["user_frist_add_in"]={
						"nt":"กรอกข้อมูล นำเข้าสินค้า",
						"nf":"use_first_add-in.js",
						"di":["use","user_frist"],
						"as":null}
					this.ob_page["user_frist_set-shop"]={
						"nt":"ตั้งค่าข้อมูลร้านค้า",
						"nf":"use_first_set-shop.js",
						"di":["use","user_frist"],
						"as":null}
					this.ob_page["user_frist_set-printer"]={
						"nt":"ตั้งค่าเครื่องพิมพ์",
						"nf":"use_first_add-printer.js",
						"di":["use","user_frist"],
						"as":null}						
					this.ob_page["user_frist_set-bill"]={
						"nt":"ตั้งค่าใบเสร็จ",
						"nf":"use_first_set-bill.js",
						"di":["use","user_frist"],
						"as":null}
				this.ob_page["guse"]={
					"nt":"การใช้งานทั่วไป",
					"nf":"guse.js",
					"hd":"การใช้งานทั่วไป",
					"ts":"เลือกตามที่ต้องการรู้",
					"di":["use"],
					"as":null}	
						this.ob_page["guse_customer-display"]={
							"nt":"หน้าจอแสดงผลลูกค้า",
							"nf":"guse_customer-display.js",
							"di":["use","guse"],
							"as":null}
				this.ob_page["technic"]={
					"nt":"เทคนิค",
					"nf":"technic.js",
					"hd":"เทคนิค",
					"ts":"เทคนิค ต่างๆ",
					"di":["use"],
					"as":null}
					this.ob_page["technic_find-font-path-window"]={
						"nt":"หาที่อยู่ฟ้อนต์ ใน Windows 10",
						"nf":"technic_find-font-path-window.js",
						"di":["use","technic"],
						"as":null}
					this.ob_page["technic_change-own"]={
						"nt":"เปลี่ยนเจ้าของไฟล์ (Linux)",
						"nf":"technic_change-own.js",
						"di":["use","technic"],
						"as":null}
					this.ob_page["technic_close-short-search-firefox"]={
						"nt":"ปิดคีย์ลัดค้นหาแบบเร็วของ Firefox",
						"nf":"technic_close-short-search-firefox.js",
						"di":["use","technic"],
						"as":null}
					this.ob_page["technic_zoom-page"]={
						"nt":"ซูมหน้าจอขายให้ใหญ่ขึ้น",
						"nf":"technic_zoom-page.js",
						"di":["use","technic"],
						"as":null}
		this.ob_page["news_____________________"]={
			"nt":"ข่าวสาร",
			"nf":"news.js",
			"di":[],
			"as":null}
		this.ob_page["about____________________"]={
			"nt":"เกี่ยวกับ",
			"nf":"about.js",
			"di":[],
			"as":null}
		this.s_page="home_____________________"
		this.s_page_error="ไม่พบหน้าที่ต้องการ"
	}
	run(){
		this.tran()
	}
	getQuery_array(){
		alert(this.ob_query.get("page"))
	}
	tran(){
		let page=this.ob_query.get("page")
		if(page!=null){
			if(this.ob_page.hasOwnProperty(page)){
				this.s_page=page
				this.contentOpen()
				this.write(`<script src="js/${this.ob_page[page]["nf"]}" type="text/javascript"></script>`)
				M.contentClose()
			}else{
				this.pageNotFound()
			}
		}else{
			this.contentOpen()
			this.pageDefault()
			M.contentClose()
		}
	}
	pageNotFound(){
		this.setTitle("ไม่พบที่ต้องการ")
		this.write(`<script src="js/${this.s_page_error}.js" type="text/javascript"></script>`)
	}
	pageDefault(){
		this.write(`<script src="js/${this.s_page_default}.js" type="text/javascript"></script>`)
	}
	lMenu(){
		let s_di = this.ob_page[this.s_page]["di"].toString()
		for(let prop in this.ob_page){
			let s_dii=this.ob_page[prop]["di"].toString()
			if(s_di==s_dii){
				let pt=(this.s_page==prop)?"class=\"active\" ":""
				this.write(`<div><a ${pt}  onclick="M.href('${prop}')">${this.ob_page[prop]["nt"]}</a></div>`)
			}
		}	
	}
	contentOpen(){
		this.write(`
			<div class="ct">
				<div>
					<div>${this.app_name}</div>
					<div>${this.app_disc}</div>
				</div>`)
		this.dir()
		if(this.s_page!="home_____________________"){
			this.write(`<div>`)
		}else{
			this.write(`<div class="home">`)
		}
		this.write(`<div class="content">`)
	}
	contentClose(){
		if(this.s_page!="home_____________________"){
			this.write(`		
						</div>
						<div>
			`)		
							this.lMenu()
			this.write(`		
						</div>
				</div>
			</div>
			`)
		}else{
			this.write(`		
						</div>
				</div>
			</div>
			`)
		}
	}
	setTitle(s_txt=""){
		if(s_txt!=""){
			document.title=s_txt
		}else{
			document.title=this.ob_page[this.s_page]["nt"]
		}
	}
	href(s_page){
		let k="?page="+s_page
		if(s_page=="home_____________________"){
			k="index.html"
		}
		location.href=k
	}
	write(t){
		document.write(t)
	}
	w(t){
		document.write(t)
	}
	dir(){
		this.setTitle()
		let hm='<a href="?page=home_____________________">'+this.ob_page["home_____________________"]["nt"]+'</a>'
		let a_di = this.ob_page[this.s_page]["di"]
		if(this.ob_page[this.s_page]["di"]==0){
			if(this.s_page=="home_____________________"){
				this.write(`<div class="dir"></div>`)
			}else{
				this.write(`<div class="dir">`)
				this.write(hm)
				this.write(` » <a href="?page=${this.s_page}">${this.ob_page[this.s_page]["nt"]}</a>`)
				this.write(`</div>`)
			}
		}else{
			this.write(`<div class="dir">`)
			this.write(hm)
			for(let i=0;i<a_di.length;i++){
				this.write(` » <a href="?page=${a_di[i]}">${this.ob_page[a_di[i]]["nt"]}</a>`)
			}
			this.write(` » <a href="?page=${this.s_page}">${this.ob_page[this.s_page]["nt"]}</a>`)
			this.write(`</div>`)
		}
	}
	select(){
		let ou="<ul>"
			if(this.ob_page[this.s_page]["ty"]=="order"){
				ou="<ol>"
			}
		let l0	=	this.ob_page[this.s_page]["di"].length
		let a_di = this.ob_page[this.s_page]["di"]
		this.w(`
			<div class="select">
				<h2>${this.ob_page[this.s_page]["hd"]}</h2>
				<p class="h_select">${this.ob_page[this.s_page]["ts"]}<p>
				<div>
					${ou}`)
				for(let prop in this.ob_page){
					let l=this.ob_page[prop]["di"].length
					if(l0+1==l){
						if(this.ob_page[prop]["di"][l-1]==this.s_page){
							this.w(`<li><a href="?page=${prop}">${this.ob_page[prop]["nt"]}</a></li>`)
						}
					}
				}
		this.w(`
					${ou}
				</div>
			</div>
		`)
	}
	link_s(s_txt,s_url){
		if(s_txt==null){
			s_txt=s_url
		}
		return '<a href="'+s_url+'" title="ไปหน้าเว็บไซต์ '+s_url+'">'+s_txt+'</a>';
	}
	blockHead(s_msg){
		this.w(`<h1 class="c">${s_msg}</h1>`)
	}
	blockDesc(s_msg){
		this.w(`
			<div class="description"></div>
		`)
	}
	blockStep(s_head,s_msg,a_img){
		this.step+=1
		let s_imgt=""
		for(let i=0;i<a_img.length;i++){
			s_imgt+=`<div class="img">
				<img onclick="M.viewImg(this)" src="img/${a_img[i]}" />
				<div><i>${a_img[i+1]}</i></div>
			</div>`
			i=i+1
		}
		if(this.step==1){
			this.w(`<div>`)
		}
		this.w(`
			<div class="step">
				<div class="head step_head1">
					<div class="step_head_step1"><div>ลำดับที่ ${this.step}</div></div> 
					${s_head}
				</div>
				<div class="desc">${s_msg}</div>
				<div class="img">${s_imgt}</div>
			</div>
		`)
		if(this.step==1){
			this.w(`</div>`)
		}
	}
	blockRef(a_){
		this.w(`<div class="refer"><p>อ้างอิง</p><ol>`)
		for(let i=0;i<a_.length;i++){
			this.w(`<li>${a_[i]}</li>`)
		}
		this.w(`</ol></div>`)
	}
	blockNote(s_){
		this.w(`<div class="note"><p>หมายเหตุ</p><div>${s_}</div></div>`)
	}
	viewImg(did){
		let a=document.createElement("div")
		a.setAttribute("class","divviewimg")
		a.setAttribute("id","id_divviewimg")
			let x=document.createElement("div")
			let y=document.createElement("div")
				let p=document.createElement("img")
				p.setAttribute("src",did.src)
			y.appendChild(p)
			let z=document.createElement("div")
		a.appendChild(x)
		a.appendChild(y)
		a.appendChild(z)
		document.body.style.overflow="hidden"
		document.body.appendChild(a)
		let m=document.createElement("div")
		m.setAttribute("class","divviewclose")
		m.setAttribute("id","id_divviewclose")
		m.setAttribute("onclick","M.viewImgClose()")
		m.appendChild(document.createTextNode("×"))
		document.body.appendChild(m)
	}
	viewImgClose(){
		document.body.style.overflow="auto"
		document.getElementById("id_divviewimg").parentNode.removeChild(document.getElementById("id_divviewimg"))
		document.getElementById("id_divviewclose").parentNode.removeChild(document.getElementById("id_divviewclose"))
	}
	sticky(){
		let a=document.querySelectorAll("div.step > div.head")
		let options = {
			//root: document.querySelector("div.ct > div:nth-child(3) "),
			rootMargin: '-0.5px',
			threshold: 1
		}
		this.observer=new IntersectionObserver(M.stickyDo,options) 
		for(let i=0;i<this.step;i++){
			this.observer.observe(a[i])
		}

	}
	stickyDo(entries, observer){
		for(let i=0;i<entries.length;i++){
			let o=entries[i].target
			let all=entries[i].intersectionRatio
			let u=entries[i].isIntersecting
			if(all<1){
				o.className="head step_head0"
				o.childNodes[1].childNodes[0].className="step_head_step0"
				
			}else{
				o.className="head step_head1"
				o.childNodes[1].childNodes[0].className="step_head_step1"
			}
		}
	}
	v_s(s_t){
		return '<span class="var">'+s_t+'</span>'
	}
	tm_s(s_t){
		return '<code class="teminal">'+s_t+'</code>'
	}
	li_s(a_){
		let t="<div class=\"uol_var\"><div>ค่าที่ต้องใช้ อ้างอิงและละคนอาจจะได้แตกต่างกันไป</div><ul>"
		for(let i=0;i<a_.length;i++){
			t+="<li>"+a_[i]+"</li>"
		}
		t+="</ul></div>"
		return t
	}
}
