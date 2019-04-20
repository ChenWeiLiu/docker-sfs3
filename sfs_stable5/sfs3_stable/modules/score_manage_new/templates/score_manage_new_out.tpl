{{* $Id: score_manage_new_out.tpl 6238 2010-10-21 05:47:54Z infodaes $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<form name="menu_form" method="post" action="{{$smarty.server.SCRIPT_NAME}}">
<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<tr><td bgcolor='#FFFFFF'>
<table width="100%">
<tr>
<td>{{$year_seme_menu}}</td>
</tr>
<tr><td>
<table cellspacing="0" cellpadding="0"><tr><td>
<table bgcolor="#9EBCDD" cellspacing="1" cellpadding="4">
<tr class="title_sbody2">
<td align="center" colspan="2" vlign="middle">學生學號</td>
<td bgcolor="white" colspan="6" align="left">
<input type="text" size="10" name="sel_stud_id" value="">
<input type="submit" name="add_id" value="新增學生"> 或
</td></tr>
<tr class="title_sbody2">
<td align="center" colspan="2">班級座號</td>
<td bgcolor="white" colspan="6" align="left">
<input type="text" size="2" name="sel_year_name" value="">年級
<input type="text" size="2" name="sel_class_num" value="">班
<input type="text" size="2" name="sel_site_num" value="">號
<input type="submit" name="add_num" value="新增學生">
</td></tr>
<tr class="title_sbody2">
<td align="center" colspan="2" vlign="middle">原　　因</td>
<td bgcolor="white" colspan="6" align="left">
<input type="text" size="30" name="reason" value="">
<input type="hidden" id="deldata" name="del" value="">
</td></tr></table>
</td></tr></table>
</td></tr>
<tr><td>
<table border="0" cellspacing="1" cellpadding="4" width="100%" bgcolor="#cccccc" class="main_body">
<tr style="background-color:#E1ECFF;text-align:center;">
<td>班級</td>
<td>座號</td>
<td>學號</td>
<td>姓名</td>
<td>原因</td>
<td>功能</td>
</tr>
{{foreach from=$rowdata item=d key=sn}}
<tr style="background-color:#ddddff;text-align:center;">
<td>{{$d.seme_class}}</td>
<td>{{$d.seme_num}}</td>
<td>{{$d.stud_id}}</td>
<td style="color:{{if $d.stud_sex==2}}red{{else}}blue{{/if}};">{{$d.stud_name}}</td>
<td>{{$d.reason}}</td>
<td><input type="image" src="../reward/images/del.png" OnClick="document.getElementById('deldata').value='{{$sn}}';this.form.submit();"></td>
</tr>
{{foreachelse}}
<tr style="background-color:white;text-align:center;">
<td colspan="6" style="color:red;">目前無資料</td>
</tr>
{{/foreach}}
</table>
</td></tr>
</table>
</td></tr></table>
</form>
{{*說明*}}
<table class="small" style="width:100%;">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>本功能用於班級成績計算時排除資源班學生，以使各班成績狀態合理呈現。</li>
	<li>若要使本列表於計算時生效，請務必於相關頁面勾選「套用排除名單」。</li>
	<li style="color:red;">請勿在學期中任意改變本列表，以免成績單發放混亂而造成各班老師、學生與學生家長對學校成績處理失去信心的危機。</li>
	</ol>
</td></tr>
</table>
{{include file="$SFS_TEMPLATE/footer.tpl"}}
