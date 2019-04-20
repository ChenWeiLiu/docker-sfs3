{{* $Id: stud_basic_test_score_input.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<script type="text/javascript">
<!--
function go(a) {
	var i =0;
	document.menu_form.student_sn.value=a;
	document.menu_form.submit();
}
//-->
</script>

<form name="menu_form" method="post" action="{{$smarty.server.SCRIPT_NAME}}">
<input type="hidden" name="student_sn">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td style="vertival-align:top;background-color:#CCCCCC;">
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF" width="100%" class="main_body">
<tr><td>學期：{{$year_seme_menu}} 年級：{{$class_year_menu}}
{{if $smarty.post.student_sn}}
{{* 補登模式 *}}
<input type="submit" name="sure" value="確定儲存"> <input type="reset" value="還原"> <input type="submit" value="離開">
{{assign var=sn value=$smarty.post.student_sn}}
<br>
<table border="0" width="100%" style="font-size:12px;" bgcolor="#C0C0C0" cellpadding="3" cellspacing="1">
<tr bgcolor="#FFFFCC" align="center">
<td>班級</td>
<td colSpan="6" style="background-color:white;text-align:left;">&nbsp; &nbsp;{{$rowdata.$sn.seme_class}}</td>
</tr>
<tr bgcolor="#FFFFCC" align="center">
<td>座號</td>
<td colSpan="6" style="background-color:white;text-align:left;">&nbsp; &nbsp;{{$rowdata.$sn.seme_num}}</td>
</tr>
<tr bgcolor="#FFFFCC" align="center">
<td>學生姓名</td>
<td colSpan="6" style="background-color:white;text-align:left;color:{{if $rowdata.$sn.stud_sex==1}}blue{{else}}red{{/if}};">&nbsp; &nbsp;{{$rowdata.$sn.stud_name}}</td>
</tr>
<tr bgcolor="#FFFFCC" align="center">
<td>轉入日</td>
<td colSpan="6" style="background-color:white;text-align:left;">&nbsp; &nbsp;{{$rowdata.$sn.move_date}}</td>
</tr>
<tr bgcolor="#FFFFCC" align="center">
<td>科目 \ 學期</td>
{{foreach from=$semes item=d}}
<td>{{$d}}</td>
{{/foreach}}
</tr>
{{foreach from=$ss_link item=dd key=s_no}}
<tr bgcolor="{{cycle values="white,#f0f0f0"}}" align="center">
<td>{{$s_arr.$s_no}}</td>
{{foreach from=$semes item=d key=s}}
{{if $rowdata.$sn.move_year_seme>$s}}{{assign var=ss value=1}}{{elseif $rowdata.$sn.move_year_seme==$s}}{{assign var=ss value=2}}{{else}}{{assign var=ss value=3}}{{/if}}
{{assign var=t value=$times.$s}}
{{assign var=ff_arr value=$ff.$t}}
<td>{{if $ss!=3}}{{foreach from=$ff_arr item=f}}階段{{$f}}<input type="text" size="5" name="score[{{$sn}}][{{$s}}][{{$s_no}}][{{$f}}]" value="{{if $score_arr.$sn.$s.$s_no.$f>0}}{{$score_arr.$sn.$s.$s_no.$f}}{{/if}}"><br>{{/foreach}}{{else}}-----{{/if}}</td>
{{/foreach}}
</tr>
{{/foreach}}
</table>
<br><input type="submit" name="sure" value="確定儲存"> <input type="reset" value="還原">
{{*說明*}}
<table class="small" width="100%">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;background-color:white;">
	<ol>
	<li>補登次數為各學期設定之定期考查次數。</li>
	<li>因為每學年採計的方式無法確定，所以目前預設補登成績以所有學期來考量，若確定不採計的學期，則可以不補登。</li>
	<li>若無成績則無需補登。</li>
	</ol>
</td></tr>
</table>
{{elseif $smarty.post.year_name}}
{{* 列表模式 *}}
<br>
<table border="0" width="100%" style="font-size:12px;" bgcolor="#C0C0C0" cellpadding="3" cellspacing="1">
<tr bgcolor="#FFFFCC" align="center">
<td>選</td>
<td>班級</td>
<td>座號</td>
<td>學生姓名</td>
<td>轉入日</td>
{{foreach from=$semes item=d}}
<td>{{$d}}</td>
{{/foreach}}
</tr>
{{foreach from=$sn_arr item=sn}}
<tr bgcolor="white" align="center">
<td><input type="checkbox" OnClick="go({{$sn}});"></td>
<td>{{$rowdata.$sn.seme_class}}</td>
<td>{{$rowdata.$sn.seme_num}}</td>
<td style="color:{{if $rowdata.$sn.stud_sex==1}}blue{{else}}red{{/if}};">{{$rowdata.$sn.stud_name}}</td>
<td>{{$rowdata.$sn.move_date}}</td>
{{foreach from=$semes item=d key=s}}
{{if $rowdata.$sn.move_year_seme>$s}}{{assign var=ss value=1}}{{elseif $rowdata.$sn.move_year_seme==$s}}{{assign var=ss value=2}}{{else}}{{assign var=ss value=3}}{{/if}}
<td style="background-color:{{if $ss==1}}white{{elseif $ss==2}}#FFFF80;color:red;{{else}}#E0E0E0;color:grey{{/if}};">{{if $ss==1}}需補登{{$times.$s}}次{{elseif $ss==2}}視情況補登{{else}}不需補登{{/if}}</td>
{{/foreach}}
</tr>
{{/foreach}}
</table>
{{*說明*}}
<table class="small" width="100%">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;background-color:white;">
	<ol>
	<li>黃底色代表該學期為轉入學期。</li>
	<li>補登次數為各學期設定之定期考查次數。</li>
	<li>因為每學年採計的方式無法確定，所以目前預設補登成績以所有學期來考量，若確定不採計的學期，則可以不補登。</li>
	</ol>
</td></tr>
</table>
{{else}}
{{*說明*}}
<table class="small" width="100%">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;background-color:white;">
	<ol>
	<li>本資料成績補登目前僅供免試入學計算使用。</li>
	<li>補登欄位為系統自動判斷，但請只補登錄轉入前的階段成績。</li>
	</ol>
</td></tr>
</table>
{{/if}}
</tr>
</table>
</td></tr>
</table>
</form>
{{include file="$SFS_TEMPLATE/footer.tpl"}}
