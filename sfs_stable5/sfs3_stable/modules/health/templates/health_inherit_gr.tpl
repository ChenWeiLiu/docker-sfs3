{{* $Id: health_inherit_gr.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
<script src="js/DropDownControl.js" language="javascript"></script>
<link href="js/DropDownControl.css" rel="stylesheet" type="text/css"/>
{{* 家族疾病史 *}}
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="5" style="color:white;text-align:center;">家族疾病史</td>
</tr>
<tr bgcolor="#f4feff">
<td>座號</td><td>姓名</td><td>親屬</td><td>遺傳性疾病</td><td>功能選項</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data.$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
{{foreach from=$health_data->stud_base.$sn.inherit item=ddd key=iii}}
<tr style="background-color:white;">
<td style="text-align:center;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};">{{$health_data->stud_base.$sn.stud_name}}</td>
<td>{{$folk_kind_arr.$iii}}</td>
<td>{{$hereditary_disease_kind_arr.$ddd}}</td>
<td>
<input type="image" src="images/edit.gif" alt="編輯這筆資料" OnClick="document.getElementById('act').value='inherit_st';document.getElementById('sn').value='{{$sn}}';document.myform.target='new';document.myform.submit();">
<input type="image" src="images/delete.gif" alt="刪除這筆資料" OnClick="document.getElementById('del').name='del[{{$sn}}][health_inherit][folk_id]';document.getElementById('del').value='{{$ddd}}';document.myform.submit();">
</td>
</tr>
{{foreachelse}}
<tr style="background-color:white;">
<td style="text-align:center;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};">{{$health_data->stud_base.$sn.stud_name}}</td>
<td>-----</td>
<td>-----</td>
<td>
<input type="image" src="images/edit.gif" alt="編輯這筆資料" OnClick="document.getElementById('act').value='inherit_st';document.getElementById('sn').value='{{$sn}}';document.myform.target='new';document.myform.submit();">
</td>
</tr>
{{/foreach}}
{{foreachelse}}
<tr bgcolor="white">
<td colspan="7" style="text-align:center;color:blue;">無資料</td>
</tr>
{{/foreach}}
</table>

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
