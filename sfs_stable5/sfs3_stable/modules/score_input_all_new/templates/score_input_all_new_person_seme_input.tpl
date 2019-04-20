{{* $Id: score_input_all_new_person_seme_input.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<table border="0" cellspacing="1" cellpadding="2" style="width:100%;background-color:#cccccc;">
<tr><td style="background-color:white;">
<table border="0" style="width:100%;">
<form name="myform" method="post" action="{{$smarty.server.SCRIPT_NAME}}">
<tr>
{{if $smarty.post.stud_id}}
<td style="vertical-align:top;">
	<table cellspacing="0" cellpadding="0">
	<tr class="title_sbody1">
	<td>學生學號</td>
	<td><input type="text" size="10" name="stud_id" value="{{$smarty.post.stud_id}}"></td>
{{if $smarty.post.student_sn}}
	<td><input type="text" size="6" value="{{$stud_name}}"><input type="submit" name="change" value="更換學生"></td>
	<td>{{$seme_menu}}<input type="hidden" name="student_sn" value="{{$smarty.post.student_sn}}"></td>
{{else}}
	<td style="width:300;"></td>
{{/if}}
	</tr>
{{if $stud_arr}}
	<tr><td colspan="3"><br>
	<table border="0" cellspacing="0" cellpadding="1" style="background-color:#9EBCDD;"><tr><td>
	<table border="0" cellspacing="0" cellpadding="4" style="width:100%;">
	<tr class="title_sbody1">
	<td style="text-align:center;background-color:#9EBCDD;color:white;">請先選擇學生</td>
	</tr>
{{foreach from=$stud_arr item=d}}
	<tr class="title_sbody1">
	{{assign var=cond value=$d.stud_study_cond}}
	<td><input type="radio" name="student_sn" value="{{$d.student_sn}}" OnClick="this.form.submit();"><span style="color:{{if $d.stud_sex==1}}blue{{elseif $d.stud_sex==2}}red{{else}}black{{/if}};">{{$d.stud_name}}</span>({{$d.stud_study_year}}年入學)({{$cond_arr.$cond}})</td>
	</tr>
{{/foreach}}
	</table></td></tr>
	</table></td></tr>
{{/if}}
	</table>
{{else}}
<td style="{{if !$class_name_menu}}width:50%;{{/if}}vertical-align:top;">
	<table cellspacing="0" cellpadding="0">
	<tr>
	<td>{{$year_seme_menu}}</td><td>{{$year_name_menu}}</td><td>{{$class_name_menu}}</td><td>{{$stud_menu}}</td><td></td>
	</tr>
	<input type="hidden" name="old_year_seme" value="{{$smarty.post.year_seme}}">
	<input type="hidden" name="old_year_name" value="{{$smarty.post.year_name}}">
	<input type="hidden" name="old_me" value="{{$smarty.post.me}}">
	</table>
{{/if}}
{{if $rowdata}}
	<table border="0" cellpadding="2" cellspacing="1" style="background-color:#9EBCDD;">
	<tr class="title_sbody2">
	<td colspan="5" style="text-align:center;background-color:#c4d9ff;">各領域成績</td>
	</tr>
	<tr class="title_sbody1" style="background-color:#E1ECFF;">
		<td style="text-align:center;">科目</td>
		<td style="text-align:center;">努力程度</td>
		<td style="text-align:center;">等第</td>
		<td style="text-align:center;">學期平均</td>
		<td style="text-align:center;">文字描述</td>
	</tr>
{{foreach from=$rowdata.ss_id item=d key=i}}
	<tr class="title_sbody2">
		<td class="title_sbody1"><p align='center'>{{$rowdata.subject.$i}}</p>
		<td style="background-color:white;text-align:center;"><select name="ss_val[{{$d}}]">{{$rowdata.ss_val.$d}}</select></td>
		<td style="background-color:white;text-align:center;">{{$rowdata.cstr.$d}}</td>
		<td style="background-color:white;text-align:center;"><input type="text" name="score[{{$d}}]" value="{{$rowdata.score.$d}}" style="width:70;"></td>
		<td style="background-color:white;text-align:center;"><input type="text" name="memo[{{$d}}]" value="{{$rowdata.memo.$d}}" style="width:400;"'></td>
	</tr>
{{/foreach}}
	<tr class="title_sbody2">
	<td colspan="5" style="text-align:center;background-color:#c4d9ff;">日常表現成績</td>
	</tr>
{{foreach from=$rowdata.nor.ss_item item=dd key=i}}
	<tr class="title_sbody1">
	<td style="text-align:center;">{{$dd}}</td>
	<td style="background-color:white;"><select name="nor_val[{{$i}}]">{{$rowdata.nor.ss_val.$i}}</select></td>
{{if $i==1}}
	<td rowspan="4" style="background-color:white;text-align:center;">{{$rowdata.nor.cstr}}</td>
	<td rowspan="4" style="background-color:white;text-align:center;"><input type="text" name="score_nor" value="{{$rowdata.nor.score}}" style="width:70;"></td>
	<td rowspan="4" style="background-color:white;text-align:center;"><input type="text" name="memo_nor" value="{{$rowdata.nor.memo}}" style="width:400;"></td>
{{/if}}
	</tr>
{{/foreach}}
	</table>
	<input type="submit" name="save" value="儲存">
{{if !$smarty.post.stud_id}}
	<table>
	<tr style="background-color:#FBFBC4;">
	<td><img src="{{$SFS_PATH_HTML}}/images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td>
	</tr>
	<tr>
	<td style="line-height:150%;">
	<ol>
	<li class="small">請注意！為了補登作業方便，班級名單內所顯示的固定為本學期的編班名單，並不隨所選學期而改變。</li>
	</ol>
	</td>
	</tr>
	</table>
{{/if}}
{{/if}}
</td>
{{if !$class_name_menu && !$smarty.post.stud_id}}
<td style="vertical-align:top;">
	<table cellspacing=0 cellpadding=0>
	<tr class="title_sbody1">
	<td style="background-color:white;">學生學號</td>
	<td style="background-color:white;">
	<form name='form5' method='post' action='/sfs3_stable/modules/score_input_all_new/person_seme_input.php'>
	<input type='text' size='10' name='stud_id' value=''><input type='submit' value='更換學生'>&nbsp;
	</form>
	</td>
	<td style="background-color:white;">
	</td>
	</tr>
	</table>
</td>
{{/if}}
</tr>
</form>
</table>
</td>
</tr>
</table>
{{include file="$SFS_TEMPLATE/footer.tpl"}}
