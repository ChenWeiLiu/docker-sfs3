{{* $Id: health_hospital_gr.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
<script src="js/DropDownControl.js" language="javascript"></script>
<link href="js/DropDownControl.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript">
<!--
function get_item(a) {
	var f=document.getElementsByName('item');
	for (var i=0;i<f.length;i++) {
		if (f[i].checked) {
			document.getElementById(a).value=f[i].value;
		}
	}
}
//-->
</script>

{{* 護送醫院 *}}
<table cellspacing="0" cellpadding="0"><tr><td>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="4" style="color:white;text-align:center;">護送醫院</td>
</tr>
<tr bgcolor="#f4feff">
<td>座號</td><td>姓名</td><td>醫院</td><td>功能選項</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data.$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
{{foreach from=$health_data->stud_base.$sn.hospital item=ddd}}
{{assign var=id value=$ddd|string_format:"%02d"}}
<tr style="background-color:white;">
<td style="text-align:center;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};">{{$health_data->stud_base.$sn.stud_name}}</td>
<td>{{$hos_arr.$id}}</td>
<td>
<input type="checkbox" id="a_{{$sn}}" name="update[{{$sn}}][health_hospital_record][id]" OnClick="get_item('a_{{$sn}}');" {{if $sn==$tempsn}}disabled{{/if}}>
<input type="image" src="images/edit.gif" alt="編輯這筆資料" OnClick="document.getElementById('act').value='hospital_st';document.getElementById('sn').value='{{$sn}}';document.myform.target='new';document.myform.submit();">
<input type="image" src="images/delete.gif" alt="刪除這筆資料" OnClick="document.getElementById('del').name='del[{{$sn}}][health_hospital_record][id]';document.getElementById('del').value='{{$ddd}}';document.myform.submit();">
</td>
</tr>
{{assign var=tempsn value=$sn}}
{{foreachelse}}
<tr style="background-color:white;">
<td style="text-align:center;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};">{{$health_data->stud_base.$sn.stud_name}}</td>
<td>-----</td>
<td>
<input type="checkbox" id="a_{{$sn}}" name="update[{{$sn}}][health_hospital_record][id]" OnClick="get_item('a_{{$sn}}');">
<input type="image" src="images/edit.gif" alt="編輯這筆資料" OnClick="document.getElementById('act').value='hospital_st';document.getElementById('sn').value='{{$sn}}';document.myform.target='new';document.myform.submit();">
</td>
</tr>
{{/foreach}}
{{foreachelse}}
<tr bgcolor="white">
<td colspan="7" style="text-align:center;color:blue;">無資料</td>
</tr>
{{/foreach}}
</table>
<input type="button" name="sure" value="確定修改" OnClick="document.getElementById('act').value='';document.getElementById('sn').value='';document.myform.target='';this.form.submit();">
</td>
<td>&nbsp;</td>
<td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="3" style="color:white;text-align:center;">護送醫院選項</td>
</tr>
{{assign var=en value=1}}
{{foreach from=$hos_arr item=dr key=kr}}
<tr style="background-color:white;">
<td><input type="radio" name="item" {{if $en}}checked{{/if}} value="{{$kr}}"></td>
<td>{{$kr}}</td>
<td>{{$dr}}</td>
</tr>
{{assign var=en value=0}}
{{/foreach}}
</table>
</td></tr></table>

</td></tr>
<input type="hidden" name="sub_menu_id" value="{{$smarty.post.sub_menu_id}}">
<input type="hidden" name="year_seme" value="{{$smarty.post.year_seme}}">
<input type="hidden" name="class_name" value="{{$smarty.post.class_name}}">
<input type="hidden" id="sn" name="student_sn" value="">
<input type="hidden" id="act" name="act" value="">
{{if !$smarty.post.edit}}
<input type="hidden" id="del" name="aaa" value="">
{{/if}}
</form></table>
</td></tr></table>
</td></tr></table>
</td>
</tr>
</form>
</table>
