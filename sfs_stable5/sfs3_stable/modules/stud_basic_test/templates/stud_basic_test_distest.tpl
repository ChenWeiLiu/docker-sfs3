{{* $Id: stud_basic_test_distest.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<form name="menu_form" method="post" action="{{$smarty.server.SCRIPT_NAME}}">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td style="vertival-align:top;background-color:#CCCCCC;">
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF" width="100%" class="main_body">
<tr><td>學期：{{$year_seme_menu}} 年級：{{$class_year_menu}} <input type="submit" name="xls" value="XLS輸出" {{if !$smarty.post.year_name}}disabled="true"{{/if}}> {{$menu2}}
{{if $smarty.post.year_name}}
<br>
<table border="0" width="100%" style="font-size:12px;" bgcolor="#C0C0C0" cellpadding="3" cellspacing="1">
<tr bgcolor="#FFFFCC" align="center">
<td>招生學校</td>
<td>班級</td>
<td>學號</td>
<td>姓名</td>
<td>身分證號</td>
<td>性別</td>
<td>生日</td>
<td>電話</td>
<td>郵遞區號</td>
<td>地址</td>
{{foreach from=$col_arr item=d}}
<td>{{$d}}</td>
{{/foreach}}
<td>總平均</td>
<td>免報名費</td>
</tr>
{{foreach from=$student_sn item=d key=seme_class}}
{{foreach from=$d item=sn key=site_num}}
<tr bgcolor="#ddddff" align="center">
<td></td>
<td>{{$seme_class|@substr:-2:2|intval}}</td>
<td>{{$stud_data.$sn.stud_id}}</td>
<td>{{$stud_data.$sn.stud_name}}</td>
<td>{{$stud_data.$sn.stud_person_id}}</td>
<td>{{$stud_data.$sn.stud_sex}}</td>
<td>{{$stud_data.$sn.stud_birthday}}</td>
<td>{{$stud_data.$sn.stud_tel}}</td>
<td>{{$stud_data.$sn.addr_zip}}</td>
<td>{{$stud_data.$sn.stud_addr_1}}</td>
{{foreach from=$col2_arr item=si}}
{{foreach from=$ss_link item=sl}}
<td>{{s2s score=$fin_score.$sn.$sl.$si.score rule=$rule.$si}}</td>
{{/foreach}}
{{/foreach}}
<td>{{s2s score=$fin_score.$sn.avg.score rule=$rule.$seme_year_seme}}</td>
<td></td>
</tr>
{{/foreach}}
{{/foreach}}
</table>
</td></tr>
{{else}}
<br>請先檢查學期成績是否有多餘資料，以確保成績計算正確。<input type="submit" name="check" value="先檢查成績">
{{*說明*}}
<table class="small" width="100%">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;background-color:white;">
	<ol>
	<li>本資料為南區五專聯招免試入學使用。</li>
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
