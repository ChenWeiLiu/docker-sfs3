{{* $Id: health_whole.tpl 5908 2010-03-16 23:47:21Z hami $ *}}

<style type="text/css">
<!--
form{margin:0px;display: inline}
-->
</style>
<script type="text/javascript" src="{{$SFS_PATH_HTML}}javascripts/setinnerhtml.js"></script>
<script type="text/javascript">
function renew(num) {
	var j;
	if ((num.length == 0) || num<1 || num>4) num="1";
	$.post('{{$smarty.server.SCRIPT_NAME}}',{ sub_menu_id: 12, year_seme: "{{$smarty.post.year_seme}}", class_name: "{{$smarty.post.class_name}}", student_sn: "{{$smarty.post.student_sn}}", ajax: "ajax", colnum: num},function(data){
		if (data!=''){
			set_innerHTML('mycontent'+num, data, 0);
		}
	});
}
{{if $smarty.post.student_sn}}
$(document).ready(function()
{
	for(i=1;i<=4;i++) renew(i);
})
function showdg(a,b) {
	var $dialog = $('<div id="newdg"></div>')
		.dialog({
			title: b,
			width: 800,
			height: 600,
			modal: true,
			resizable: false
		});
	$.post('{{$smarty.server.SCRIPT_NAME}}',{ sub_menu_id: 12, year_seme: "{{$smarty.post.year_seme}}", class_name: "{{$smarty.post.class_name}}", student_sn: "{{$smarty.post.student_sn}}", act: a}, function(data){
		if (data!=''){
			set_innerHTML('newdg', data, 0);
		}
	});
}
{{/if}}
</script>

<table border="0" cellspacing="1" cellpadding="2" width="100%" style="background-color:#cccccc;">
<tr><td style="background-color:white;">
<table border="0"><tr><td valign="top">
<table class="tableBg" cellspacing="1" cellpadding="1">
<tr><td align="center" class="leftmenu">
{{$stud_menu}}
</td>
</tr>
</table>
{{if $smarty.post.student_sn}}
</td><td valign="top" style="width:200px;">
{{assign var=sn value=$smarty.post.student_sn}}
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=d value=$health_data->health_data.$sn.$year_seme}}
<table style="background-color:#9ebcdd;" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="2" style="color:white;">學生基本資料</td>
</tr>
<tr style="background-color:#f4feff;">
<td>統編</td><td>{{$health_data->stud_base.$sn.stud_person_id}}</td>
</tr>
<tr style="background-color:white;">
<td>學生</td><td>{{$health_data->stud_base.$sn.stud_name}}</td>
</tr>
<tr style="background-color:#f4feff;">
<td>學號</td><td>{{$health_data->stud_base.$sn.stud_id}}</td>
</tr>
<tr style="background-color:white;">
<td>生日</td><td>{{$health_data->stud_base.$sn.stud_birthday}}</td>
</tr>
<tr style="background-color:white;">
<td>血型</td><td>{{$health_data->stud_base.$sn.stud_blood_type}}</td>
</tr>
<tr style="background-color:#f4feff;">
<td>父親</td><td>{{$health_data->stud_base.$sn.fath_name}}</td>
</tr>
<tr style="background-color:white;">
<td>母親</td><td>{{$health_data->stud_base.$sn.moth_name}}</td>
</tr>
<tr style="background-color:#f4feff;">
<td>緊急連絡</td><td>{{$health_data->stud_base.$sn.stud_tel_2}}</td>
</tr>
</table>

<div id="mycontent1">
</div>
</td>
<td style="vertical-align:top;">
<div id="mycontent2">
</div>
</td>
<td style="vertical-align:top;">
<div id="mycontent3">
</div>
</td>
<td style="vertical-align:top;">
<div id="mycontent4">
</div>
</td></tr></table>

{{elseif $smarty.post.class_name}}
{{* 整班模式 *}}
</td><td valign="top">
<table style="background-color:#9ebcdd;" cellspacing="1" cellpadding="4" width="100%" class="small">
<form name="myform" action="{{$smarty.server.SCRIPT_NAME}}" method="post">
<tr style="background-color:white;"><td style="width:100%;">{{$kmenu}}
{{if $ifile}}<br>{{include file=$ifile}}{{/if}}
</td></tr>
<input type="hidden" name="sub_menu_id" value="{{$smarty.post.sub_menu_id}}">
<input type="hidden" name="year_seme" value="{{$smarty.post.year_seme}}">
<input type="hidden" name="class_name" value="{{$smarty.post.class_name}}">
</table>
</form>
{{/if}}
</td></tr></table>
</td></tr></table>
