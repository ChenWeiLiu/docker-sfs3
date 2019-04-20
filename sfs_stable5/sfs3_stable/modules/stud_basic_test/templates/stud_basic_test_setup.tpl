{{* $Id: stud_basic_test_setup.tpl 7219 2013-03-12 07:02:20Z brucelyc $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<script>
function selAll() {
	var i =0;

	while (i < document.menu_form.elements.length)  {
		a=document.menu_form.elements[i].id.substring(0,4);
		if (a=='sel_') {
			document.menu_form.elements[i].checked=1-document.menu_form.elements[i].checked;
		}
		i++;
	}
}
</script>
<form name="menu_form" method="post" action="{{$smarty.server.SCRIPT_NAME}}">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr><td style="vertival-align:top;background-color:#CCCCCC;">
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF" class="main_body" width="100%">
<tr><td style="vertical-align: top;">
{{if $stage==1}}
<br>
1.需於「學生子類表」(stud_subkind)中的「(9)原住民」項下加入「族語認證」屬性。<br>
 &nbsp;&nbsp; 資料庫目前記錄之屬性資料分別為：<br>
 &nbsp;&nbsp; (1)<span style="color: blue;"> {{$clan}}</span><br>
 &nbsp;&nbsp; (2)<span style="color: blue;"> {{$area}}</span><br>
 &nbsp;&nbsp; (3)<input type="radio" name="spec" value="memo"><span style="color: blue;">{{if $memo}}{{$memo}}{{else}}尚未設定{{/if}}</span><br>
 &nbsp;&nbsp; (4)<input type="radio" name="spec" value="note"><span style="color: blue;">{{if $note}}{{$note}}{{else}}尚未設定{{/if}}</span><br>
 &nbsp;&nbsp; 請選擇您所要加入的欄位(<span style="color:red;">請注意, 如果該欄位原有資料, 則會將資料全數清除!</span>)。<br>
 &nbsp;&nbsp; <input type="submit" name="sure9" value="確定"><br>
{{elseif $stage==2}}
2.需於「學生身分別」中加入「境外科技人才子女」選項。<br>
 &nbsp;&nbsp; 程式自動加入的「境外科技人才子女」選項代碼為<input type="text" size="2" maxlength="2" name="tech" value="{{$type71}}"><br>
 &nbsp;&nbsp; <input type="submit" name="sure71" value="確定"><br>
{{else}}
<table cellpadding="0" cellspacing="0"><tr><td>
<table style="font-size:12px;" bgcolor="#F0F0F0" cellpadding="3" cellspacing="1">
<tr bgcolor="#FFFFCC">
<td rowspan="2"><input type="checkbox" OnClick="selAll()"></td>
<td rowspan="2">特種身分</td>
<td rowspan="2">加分比例</td>
<td rowspan="2">班級</td>
<td rowspan="2">座號</td>
<td rowspan="2">姓名</td>
<td rowspan="2">學號</td>
<td colspan="3" style="text-align: center;">成績採計學期</td>
</tr>
<tr bgcolor="#FFFFCC">
<td>八上</td>
<td>八下</td>
<td>九上</td>
</tr>
{{if count($rowdata) >0}}
{{foreach from=$rowdata item=d key=kind}}
{{foreach from=$d item=dd key=sn}}
<tr bgcolor="{{cycle values="white,#F0F0F0"}}">
<td style="text-align: center;"><input type="checkbox" name="sel[{{$sn}}]" id="sel_{{$sn}}"></td>
<td>{{$spc_arr.$kind}}{{if $spo_arr.$kind}}(<span style="color: red;">{{$spo_arr.$kind}}</span>){{/if}}</td>
<td style="text-align: center;">{{$plus_arr.$kind}}％</td>
<td style="text-align: center;">{{$dd.seme_class|@substr:-2:2|intval}}</td>
<td style="text-align: center;">{{$dd.seme_num}}</td>
<td style="color:{{if $dd.sex==2}}red{{else}}blue{{/if}};">{{$dd.name}}</td>
<td style="text-align: center;">{{$dd.stud_id}}</td>
<td style="text-align: center;">{{if $chk_arr.$kind || $dd.sp_cal}}<input type="checkbox" name="cal[{{$sn}}][0]" {{if $stud_data.$sn.enable0}}checked{{/if}}>{{else}}<input type="checkbox" checked disabled>{{/if}}</td>
<td style="text-align: center;">{{if $chk_arr.$kind || $dd.sp_cal}}<input type="checkbox" name="cal[{{$sn}}][1]" {{if $stud_data.$sn.enable1}}checked{{/if}}>{{else}}<input type="checkbox" checked disabled>{{/if}}</td>
<td style="text-align: center;">{{if $chk_arr.$kind || $dd.sp_cal}}<input type="checkbox" name="cal[{{$sn}}][2]" {{if $stud_data.$sn.enable2}}checked{{/if}}>{{else}}<input type="checkbox" checked disabled>{{/if}}</td>
</tr>
{{/foreach}}
{{/foreach}}
{{else}}
<tr bgcolor="white">
<td colSpan="10" style="color: red; text-align: center;">未進行同步化或沒有資料</td>
</tr>
{{/if}}
</table>
<input type="submit" name="print" value="列出所選證明單"> <input type="submit" name="save" value="儲存採計學期">
{{if $smarty.get.hid==1}}
</td><td style="vertical-align: top;">
<table style="font-size:12px;" bgcolor="#F0F0F0" cellpadding="3" cellspacing="1" width="100%">
<tr style="background-color: green; color: white;">
<td>請輸入採計部份成績學生學號：<input type="text" name="stud_id" size="5"><br><input type="submit" name="add" value="確定新增">
</td>
</tr>
</table>
{{/if}}
{{if $smarty.get.sp==1}}
</td><td style="vertical-align: top;">
<table style="font-size:12px;" bgcolor="#F0F0F0" cellpadding="3" cellspacing="1" width="100%">
<tr style="background-color: green; color: white;">
<td>請輸入要部份採計的特殊生學號後按下確定新增：<input type="text" name="stud_id" size="5"><br><input type="submit" name="sp" value="確定新增">
</td>
</tr>
</table>
{{/if}}
{{if $smarty.get.noCal==1}}
</td><td style="vertical-align: top;">
<table style="font-size:12px;" bgcolor="#F0F0F0" cellpadding="3" cellspacing="1" width="100%">
<tr style="background-color: blue; color: white;">
<td>請輸入不排序成績學生學號：<input type="text" name="stud_id" size="5"><br><input type="submit" name="del" value="確定新增">
</td>
</tr>
</table>
{{/if}}
{{/if}}
</td></tr></table>
{{*說明*}}
<table class="small" width="100%">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;background-color:white;">
	<ol>
	<li style="color: red;">本系統僅提供作業平台，作業方式請依各招生區或縣市規定辦理，請勿自主決定以免影響學生權益。</li>
{{if $stage>2}}
	<li>若加分百分比未出現，則表示設定不完全。
	<li>顯示之學生資料若有錯誤，「族語認證」資料請至<a href="{{$SFS_PATH_HTML}}/modules/stud_subkind/">「學生身份類別與屬性」</a>模組修正，其他資料請至<a href="{{$SFS_PATH_HTML}}/modules/stud_reg/">「學籍管理」</a>模組修正。
{{/if}}	
	</ol>
</td></tr>
</table>
</tr>
</table>
</td></tr>
</table>
</form>
{{include file="$SFS_TEMPLATE/footer.tpl"}}
