{{* $Id: health_whole_renew1.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}

<form action="{{$smarty.server.SCRIPT_NAME}}" method="post" target="_blank">
{{assign var=sn value=$smarty.post.student_sn}}

{{* 健康基本資料 *}}
<table style="background-color:#9ebcdd;" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="2" style="color:white;">健康基本資料</td>
</tr>
<tr style="background-color:#f4feff;">
<td><input type="image" src="images/edit.gif" OnClick="this.form.act.value='disease_st';">個人疾病史</td>
<td>
{{foreach from=$health_data->stud_base.$sn.disease item=dd name=dis}}
{{$smarty.foreach.dis.iteration}}. {{$disease_kind_arr.$dd}}<br>
{{foreachelse}}
無
{{/foreach}}
</td>
</tr>
<tr style="background-color:white;">
<td><input type="image" src="images/edit.gif" OnClick="this.form.act.value='serious_st';">重大傷病卡</td>
<td>
{{foreach from=$health_data->stud_base.$sn.serious item=dd name=dis}}
{{$smarty.foreach.dis.iteration}}. {{$serious_kind_arr.$dd}}<br>
{{foreachelse}}
無
{{/foreach}}
</td>
</tr>
<tr style="background-color:#f4feff;">
<td nowrap><input type="image" src="images/edit.gif" OnClick="this.form.act.value='bodymind_st';">身心障礙手冊</td>
<td>
{{if $health_data->stud_base.$sn.bodymind}}
{{assign var=dd value=$health_data->stud_base.$sn.bodymind.bm_id}}
{{assign var=lv value=$health_data->stud_base.$sn.bodymind.bm_level}}
{{$bodymind_kind_arr.$dd}}<br><span style="color:blue;">({{$bodymind_level_arr.$lv}})</span>
{{else}}
無
{{/if}}
</td>
</tr>
<tr style="background-color:white;">
<td><input type="image" src="images/edit.gif" OnClick="this.form.act.value='inherit_st';">家族疾病史</td>
<td>
{{foreach from=$health_data->stud_base.$sn.inherit item=dd key=i}}
{{$folk_kind_arr.$i}}-{{$hereditary_disease_kind_arr.$dd}}<br>
{{foreachelse}}
無
{{/foreach}}
</td>
</tr>
<tr style="background-color:#f4feff;">
<td><input type="image" src="images/edit.gif" OnClick="this.form.act.value='hospital_st';">護送醫院</td>
<td>
{{foreach from=$health_data->stud_base.$sn.hospital item=dd name=hospital}}
{{assign var=id value=$dd|string_format:"%02d"}}
{{$smarty.foreach.hospital.iteration}}.{{$hos_arr.$id}}<br>
{{foreachelse}}
未輸入
{{/foreach}}
</td>
</tr>
<tr style="background-color:white;">
<td><input type="image" src="images/edit.gif" OnClick="this.form.act.value='insurance_st';">保險</td>
<td>
{{foreach from=$health_data->stud_base.$sn.insurance item=dd key=i name=insurance}}
{{$smarty.foreach.insurance.iteration}}.{{$ins_arr.$i}}<br>
{{foreachelse}}
無
{{/foreach}}
</td>
</tr>
<tr style="background-color:#f4feff;">
<td><input type="image" src="images/edit.gif" OnClick="this.form.act.value='ntu_st';">立體感</td>
<td>{{if $health_data->health_data.$sn.ntu==1}}無異狀{{elseif $health_data->health_data.$sn.ntu==2}}有異狀{{else}}未輸入{{/if}}</td>
</tr>
</table>

{{* 在校期間重大傷病事故 *}}
<table style="background-color:#9ebcdd;" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td style="color:white;"><input type="image" src="images/edit.gif" OnClick="this.form.act.value='accserious_st';">在校期間重大傷病事故</td>
</tr>
<tr style="background-color:#f4feff;">
<td>重大傷病:無</td>
</tr>
</table>
<input type="hidden" name="sub_menu_id" value="{{$smarty.post.sub_menu_id}}">
<input type="hidden" name="year_seme" value="{{$smarty.post.year_seme}}">
<input type="hidden" name="class_name" value="{{$smarty.post.class_name}}">
<input type="hidden" name="student_sn" value="{{$smarty.post.student_sn}}">
<input type="hidden" name="act" value="">
</form>
</div>
