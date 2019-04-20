{{* $Id: health_inflection_form.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
<script>
<!--
function go(){
	if (document.myform.class_name.value=='')
		alert("未選班級");
	else if (document.myform.student_sn.value=='')
		alert("未選學生");
	else if (document.myform.iid.value=='')
		alert("未選生病原因");
	else {
		document.myform.act.value='sure';
		document.myform.submit();
	}
}
-->
</script>

<input type="button" value="確定{{if $rowdata}}修改{{else}}新增{{/if}}" OnClick="go();">
<input type="hidden" name="act" value="add">
<input type="hidden" name="iid" value="{{$smarty.post.iid}}">
{{foreach from=$rowdata.id item=d key=i}}
<input type="hidden" name="id[{{$i}}]" value="{{$d}}">
{{/foreach}}
<br>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr style="background-color:white;text-align:center;">
<td style="background-color:#E6E9F9;">班級</td>
<td style="text-align:left;">{{$class_menu2}}</td>
</tr>
<tr style="background-color:white;text-align:center;">
<td style="background-color:#E6E9F9;">姓名</td>
<td style="text-align:left;">{{$stud_menu}}</td>
</tr>
<tr style="background-color:white;text-align:center;">
<td style="background-color:#E6E9F9;">生病原因</td>
<td style="text-align:left;">
{{foreach from=$inf_arr item=d}}
<input type="radio" name="chgid" OnClick="document.myform.iid.value='{{$d.iid}}';" {{if $smarty.post.iid==$d.iid}}checked{{/if}}>{{$d.name}}
{{/foreach}}
<br><span style="color:red;">(「有就醫者」請依醫師診斷病名點選，「未就醫者」則請校護協助依症狀點選)</span>
</td>
</tr>
<tr style="background-color:white;text-align:center;">
<td style="background-color:#E6E9F9;" colspan="2">生病日</td>
</tr>
{{foreach from=$cweekday item=d key=i}}
<tr style="background-color:white;text-align:center;">
<td style="background-color:#E6E9F9;">{{$d}}<br>[{{$weekday_arr[$i]}}]</td>
<td style="text-align:left;">
<input type="radio" name="status[{{$weekday_arr[$i]}}]" value="" {{if $rowdata.weekday.$i==""}}checked{{/if}}>無
<input type="radio" name="status[{{$weekday_arr[$i]}}]" value="A" {{if $rowdata.weekday.$i=="A"}}checked{{/if}}>生病仍上課
<input type="radio" name="status[{{$weekday_arr[$i]}}]" value="B" {{if $rowdata.weekday.$i=="B"}}checked{{/if}}>生病在家休息
<input type="radio" name="status[{{$weekday_arr[$i]}}]" value="C" {{if $rowdata.weekday.$i=="C"}}checked{{/if}}>生病住院
</td>
</tr>
{{/foreach}}
<tr style="background-color:white;text-align:center;">
<td style="background-color:#E6E9F9;">備註</td>
<td style="text-align:left;"><textarea name="oth_txt[1]" rows="2" cols="50">{{$oth_txt.1|br2nl}}</textarea></td>
</tr>
</table>
