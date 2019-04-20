{{* $Id: health_whole_renew3.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}

<form action="{{$smarty.server.SCRIPT_NAME}}" method="post" target="_blank">
{{assign var=sn value=$smarty.post.student_sn}}

{{* 全身健檢 *}}
<table style="background-color:#9ebcdd;" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="2" style="color:white;text-align:center;"><input type="image" src="images/edit.gif" OnClick="this.form.act.value='checkinput_st';">全身健檢<input type="image" src="images/edit.gif" OnClick="this.form.act.value='healthmanage_st';">健康管理</td>
</tr>
<tr style="background-color:#f4feff;">
<td>學期／日期</td><td>第一學期</td>
</tr>
</table>

{{* 實驗室檢查 *}}
<table style="background-color:#9ebcdd;" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="2" style="color:white;text-align:center;">實驗室檢查</td>
</tr>
<tr style="background-color:#f4feff;">
<td><input type="image" src="images/edit.gif">尿液</td><td>初查</td>
</tr>
</table>
<input type="hidden" name="sub_menu_id" value="{{$smarty.post.sub_menu_id}}">
<input type="hidden" name="year_seme" value="{{$smarty.post.year_seme}}">
<input type="hidden" name="class_name" value="{{$smarty.post.class_name}}">
<input type="hidden" name="student_sn" value="{{$smarty.post.student_sn}}">
<input type="hidden" name="act" value="">
</form>
