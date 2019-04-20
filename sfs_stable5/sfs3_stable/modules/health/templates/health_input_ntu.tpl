{{* $Id: health_input_ntu.tpl 5564 2009-08-08 16:07:10Z brucelyc $ *}}
<script>
function go(a,b) {
	c=a+b;
	d='v'+b;
	if (a=='n')
		e=2;
	else
		e=1;
	if (document.getElementById('d'+b).checked==false && document.getElementById('n'+b).checked==false) e='';
	document.getElementById(c).checked=false;
	document.getElementById(d).value=e;
}
function fillall() {
	var i =0;

	while (i < document.myform.elements.length)  {
		a=document.myform.elements[i].id.substring(0,1);
		if (a=='n') {
			document.myform.elements[i].checked=true;
		} else if (a=='d') {
			document.myform.elements[i].checked=false;
		} else if (a=='v') {
			document.myform.elements[i].value='1';
		}
		i++;
	}
}
</script>
<input type="submit" name="save" value="確定儲存">
<input type="button" value="全部皆設為正常" OnClick="fillall();">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="2" class="small">
<tr bgcolor="#c4d9ff">
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">正常</td>
<td align="center">異常</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">正常</td>
<td align="center">異常</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">正常</td>
<td align="center">異常</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">正常</td>
<td align="center">異常</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data.$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn}}
{{if $smarty.foreach.rows.iteration % 4==1}}
<tr style="background-color:white;">
{{/if}}
{{counter assign=d}}
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
<td align="center"><input type="checkbox" {{if $health_data->stud_base.$sn.ntu==1}}checked{{/if}} id="n{{$d}}" OnClick="go('d',{{$d}});"></td>
<td align="center"><input type="checkbox" {{if $health_data->stud_base.$sn.ntu==2}}checked{{/if}} id="d{{$d}}" OnClick="go('n',{{$d}});"></td>
<input type="hidden" name="update[new][{{$sn}}][ntu]" id="v{{$d}}" value="{{$health_data->stud_base.$sn.ntu}}">
<input type="hidden" name="update[old][{{$sn}}][ntu]" value="{{$health_data->stud_base.$sn.ntu}}">
{{if $smarty.foreach.rows.iteration % 4==0}}
</tr>
{{/if}}
{{/foreach}}
</table>
<input type="submit" name="save" value="確定儲存">
<input type="button" value="全部皆設為正常" OnClick="fillall();">
