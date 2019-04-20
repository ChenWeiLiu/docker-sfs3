{{* $Id: stud_basic_test_dis_stud.tpl 8328 2015-02-25 06:44:47Z brucelyc $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<script>
function selAll() {
	var i=0;
	while (i < document.myform.elements.length)  {
		a=document.myform.elements[i].id.substr(0,4);
		if (a=='sel_') {
			document.myform.elements[i].checked=!document.myform.elements[i].checked;
		}
		i++;
	}
}

function selCheck() {
	var i=0, s=0, g=0;
	while (i < document.myform.elements.length)  {
		a=document.myform.elements[i].id.substr(0,4);
		if (a=='sel_') {
			if (document.myform.elements[i].checked) s=1;
		}
		if (a=='act_') {
			if (document.myform.elements[i].checked) g=1;
		}
		i++;
	}
	if (!g) {
		alert('未選處理項目');
		return;
	}
	if (!s) {
		alert('未選處理學生');
		return;
	}
	document.myform.submit();
}
</script>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td valign=top bgcolor="#CCCCCC">
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF" width="100%" class="main_body">
<tr><td>
<form action="{{$smarty.server.SCRIPT_NAME}}" method="post" name="myform">
學期：{{$year_seme_menu}} 年級：{{$class_year_menu}} {{if $smarty.post.year_name}} 
{{if $smarty.post.sel}}
<input type="button" value="回統計表" OnClick="this.form.sel.value='';this.form.submit();"><br>
{{$menu2}}
<table border='0' width='100%' style='font-size:12px;' bgcolor='#C0C0C0' cellpadding='3' cellspacing='1'>
<tr style="background-color: #D0D0D0; text-align: center;">
<td><input type="checkbox" OnClick="selAll();"></td>
<td>班級</td>
<td>座號</td>
<td>學生姓名</td>
<td>學生<br>身分</td>
<td>身心<br>障礙</td>
<td>低收<br>入戶</td>
<td>中低<br>收入</td>
<td>失業勞<br>工子女</td>
<td>特種<br>身分</td>
<td>畢肄<br>業</td>
<td>資料<br>授權</td>
<td>家長姓名</td>
<td>電話</td>
<td>郵遞<br>區號</td>
<td>地址</td>
<td>緊急聯<br>絡手機</td>
<td>考區<br>代碼</td>
<td>分發<br>區碼</td>
<td>參與<br>排序</td>
</tr>
{{foreach from=$rowdata item=d key=sn}}
<tr style="background-color: {{cycle values="white,#FFFF80"}}; text-align:center;">
<td><input type="checkbox" name="sn[{{$sn}}]" id="sel_{{$sn}}"></td>
<td>{{$smarty.post.sel}}</td>
<td>{{$d.stud_site}}</td>
<td style="color: {{if $d.stud_sex==1}}blue{{else}}red{{/if}};">{{$d.stud_name}}</td>
<td>{{$d.stud_kind}}</td>
<td>{{$d.hand_kind}}</td>
<td>{{$d.lowincome}}
<td>{{$d.midincome}}
<td>{{$d.unemployed}}
<td>{{$d.sp_kind}}</td>
<td>1</td>
<td>1</td>
<td>{{$d.stud_parent}}</td>
<td>{{$d.stud_tel}}</td>
<td><input type="text" name="zip[{{$sn}}]" size="3" maxlength="3" value="{{$d.addr_zip}}"></td>
<td style="text-align: left;"> &nbsp; {{$d.stud_addr}}</td>
<td>{{$d.stud_cell}}</td>
<td>{{$d.area1}}</td>
<td>{{$d.area2}}</td>
<td>{{$d.cal}}</td>
</tr>
{{/foreach}}
</table>
<input type="button" value="確定" OnClick="selCheck();"> <input type="reset" value="清除"> <input type="hidden" name="sel" value="{{$smarty.post.sel}}">
{{else}}
<input type="submit" name="sync" value="學生資料同步化"> <input type="submit" name="lock" value="學生資料封存" {{if $isLock}}disabled{{/if}}> <input type="submit" name="unlock" value="學生資料解除封存" {{if !$isLock}}disabled{{/if}}> <br>匯出：<input type="submit" name="ct_out" value="免試中投區高中職">
<br>{{$menu2}}
<table style="border-width: 0;">
<tr><td style="vertical-align: top;">
<table border='0' style='font-size:12px;' bgcolor='#C0C0C0' cellpadding='3' cellspacing='1'>
<tr style="background-color: #D0D0D0; text-align: center;">
<td>資料設定</td>
<td>班級</td>
<td>學籍<br>學生數</td>
<td>免試作業<br>學生數</td>
<td>參與排序<br>學生數</td>
</tr>
{{foreach from=$rowdata item=d key=seme_class}}
<tr style="background-color: {{cycle values="white,#FFFF80"}}; text-align:center;">
<td><input type="radio" name="sel" value="{{$seme_class}}" OnClick="this.form.submit();"></td>
<td>{{$seme_class}}</td>
<td>{{$d}}</td>
<td style="color: {{if $rowdata2.$seme_class<>$d}}red{{else}}black{{/if}};">{{$rowdata2.$seme_class|intval}}</td>
<td style="color: {{if $rowdata3.$seme_class<>$d}}green{{else}}black{{/if}};">{{$rowdata3.$seme_class|intval}}</td>
</tr>
{{/foreach}}
</table>
</td><td style="vertical-align: top;">
{{*說明*}}
<table class="small" width="100%" bgcolor="#C0C0C0" cellpadding="3" cellspacing="1">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;background-color:white;">
	<ol>
	<li style="color: red;">請務必於 2013-03-01 進行「學生資料封存」以固定學生人數。</li>
	<li style="color: red;">資料若已確定，再同步時請勿選取任何欄位，否則資料將會被還原。</li>
	<li>以下欄位於同步化時會自動取得：
	<ul style="padding-left: 12pt; list-style-type: disc;">
	<li>學生身分</li>
	<li>身心障礙(代碼: 1)</li>
	<li>低收入戶(代碼: 3)</li>
	<li style="color: red;">中低收入戶(代碼: {{$type61}}) 《代碼由系統資料庫抓取》</li>
	<li>失業勞工子女(代碼: 53)</li>
	<li>特種學生</li>
	</ul>
	</li>
	<li>其他判斷「學生身分」時所使用之系統代碼：
	<ul style="padding-left: 12pt; list-style-type: disc;">
	<li>原住民(代碼: 9)</li>
	<li>派外人員子女(代碼: 12)</li>
	<li>蒙藏生(代碼: 51)</li>
	<li>回國僑生(代碼: 6)</li>
	<li>港澳生(代碼: 7)</li>
	<li>退伍軍人(代碼: 52)</li>
	<li>境外優秀科學技術人才子女(代碼: 71)</li>
	</ul>
	</li>
	</ol>
</td></tr>
</table>
</td></tr></table>
{{/if}}
{{/if}}
</form></td>
</tr>
</table>
</td></tr></table>
{{include file="$SFS_TEMPLATE/footer.tpl"}}
