{{* $Id: health_cioph_gr.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
<script src="js/DropDownControl.js" language="javascript"></script>
<link href="js/DropDownControl.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript">
<!--
function get_item(a,b) {
	var f=document.getElementsByName('item');
	for (var i=0;i<f.length;i++) {
		if (f[i].checked) {
			var c='_'+a+'_'+b;
			var d='i'+c;
			var e='r'+c;
			var v='v'+c;
			var o='o'+c;
			document.getElementById(d).innerHTML=f[i].value;
			document.getElementById(v).value=f[i].value;
			if (f[i].value != document.getElementById(o).value) {
				document.getElementById(d).style.fontSize='20px';
				document.getElementById(d).style.color='red';
			} else {
				document.getElementById(d).style.fontSize='';
				document.getElementById(d).style.color='black';
			}
			document.getElementById(e).checked=false;
		}
	}
}
//-->
</script>

{{* 全身健檢-眼 *}}
<table cellspacing="0" cellpadding="0"><tr><td>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="4" style="color:white;text-align:center;">全身健檢-眼</td>
</tr>
<tr bgcolor="#f4feff">
<td>座號</td><td>姓名</td><td>健檢項目</td><td>檢查單位-人員</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data.$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
<tr style="background-color:white;">
<td style="text-align:center;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};">{{$health_data->stud_base.$sn.stud_name}}</td>
<td>
<input type="radio" id="r_2_{{$sn}}" OnClick="get_item(2,'{{$sn}}');">[<span id="i_2_{{$sn}}">{{$dd.checks.Oph.2}}</span>] 辨色力異常
<input type="hidden" id="v_2_{{$sn}}" name="update[new][{{$sn}}][{{$year_seme}}][health_checks_record][Oph][2]" value="{{$dd.checks.Oph.2}}">
<input type="hidden" id="o_2_{{$sn}}" name="update[old][{{$sn}}][{{$year_seme}}][health_checks_record][Oph][2]" value="{{$dd.checks.Oph.2}}">
<input type="radio" id="r_3_{{$sn}}" OnClick="get_item(3,'{{$sn}}');">[<span id="i_3_{{$sn}}">{{$dd.checks.Oph.3}}</span>] 斜視<select name="update[new][{{$sn}}][{{$year_seme}}][health_checks_record][Oph][PS3]">{{html_options options=$squint_kind_arr selected=$dd.PSOph3}}</select>
<input type="hidden" id="v_3_{{$sn}}" name="update[new][{{$sn}}][{{$year_seme}}][health_checks_record][Oph][3]" value="{{$dd.checks.Oph.3}}">
<input type="hidden" id="o_3_{{$sn}}" name="update[old][{{$sn}}][{{$year_seme}}][health_checks_record][Oph][3]" value="{{$dd.checks.Oph.3}}">
<input type="radio" id="r_4_{{$sn}}" OnClick="get_item(4,'{{$sn}}');">[<span id="i_4_{{$sn}}">{{$dd.checks.Oph.4}}</span>] 睫毛倒插
<input type="hidden" id="v_4_{{$sn}}" name="update[new][{{$sn}}][{{$year_seme}}][health_checks_record][Oph][4]" value="{{$dd.checks.Oph.4}}">
<input type="hidden" id="o_4_{{$sn}}" name="update[old][{{$sn}}][{{$year_seme}}][health_checks_record][Oph][4]" value="{{$dd.checks.Oph.4}}">
<br>
<input type="radio" id="r_5_{{$sn}}" OnClick="get_item(5,'{{$sn}}');">[<span id="i_5_{{$sn}}">{{$dd.checks.Oph.5}}</span>] 眼球震顫
<input type="hidden" id="v_5_{{$sn}}" name="update[new][{{$sn}}][{{$year_seme}}][health_checks_record][Oph][5]" value="{{$dd.checks.Oph.5}}">
<input type="hidden" id="o_5_{{$sn}}" name="update[old][{{$sn}}][{{$year_seme}}][health_checks_record][Oph][5]" value="{{$dd.checks.Oph.5}}">
<input type="radio" id="r_6_{{$sn}}" OnClick="get_item(6,'{{$sn}}');">[<span id="i_6_{{$sn}}">{{$dd.checks.Oph.6}}</span>] 眼瞼下垂
<input type="hidden" id="v_6_{{$sn}}" name="update[new][{{$sn}}][{{$year_seme}}][health_checks_record][Oph][6]" value="{{$dd.checks.Oph.6}}">
<input type="hidden" id="o_6_{{$sn}}" name="update[old][{{$sn}}][{{$year_seme}}][health_checks_record][Oph][6]" value="{{$dd.checks.Oph.6}}">
<input type="radio">[] 其他 <input type="text" name="update[new][{{$sn}}][{{$year_seme}}][health_checks_record][Oph][PS99]" style="width:75px;">
</td>
<td>
<input type="text" style="width:104px;" value="{{$dd.checks.Ora.hospital}}"><br>
<input type="text" style="width:120px;" value="{{$dd.checks.Ora.doctor}}">
</td>
</tr>
{{foreachelse}}
<tr bgcolor="white">
<td colspan="7" style="text-align:center;color:blue;">無資料</td>
</tr>
{{/foreach}}
</table>
<input type="button" name="sure" value="確定修改" OnClick="document.getElementById('act').value='';document.getElementById('sure').value='1';document.getElementById('sn').value='';document.myform.target='';this.form.submit();">
</td>
<td>&nbsp;</td>
<td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="3" style="color:white;text-align:center;">代碼表</td>
</tr>
{{assign var=en value=1}}
{{foreach from=$diag_arr item=dr key=kr}}
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
<input type="hidden" id="sure" name="sure" value="">
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
