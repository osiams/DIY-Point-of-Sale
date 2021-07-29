let head=M.ob_page[M.s_page].nt
let msg=""
let img=[
]
//let ipr=M.v_s("192.168.1.1")

M.blockHead(head)
M.blockStep(
	head,
	`<p>สำหรับผู้ใช้ Firefox Browser ในการใช้โปรแกรม DIYPOS นี้ 
<br>วิธี ปิดคีย์ลัดค้นหาแบบเร็วของ Firefox Browser 
<br>ถ้าไม่ปิดเวลาใช้เครื่อง  สแกนบาร์โค๊ดจะอ่านไม่ได้ในกรณีที่แป้นพิพม์เป็น ไม่เป็นภาษาอังกฤษ</p><code class="code">1.พิมพ์ที่ ช่อง ที่อยู่ about:config แล้วกดเอ็นเทอ
2.กด ยอมรับ
3. ค้นหา accessibility.typeaheadfind.manual
4. เปลี่ยนค่าให้เป็น false
</code>
	`,
	[]
)
M.sticky()
