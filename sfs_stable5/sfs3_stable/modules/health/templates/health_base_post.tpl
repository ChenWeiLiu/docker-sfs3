{{* $Id: health_base_post.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
<script>
function selall() {
	var i =0;

	for (var i=0, len=document.myform.elements.length; i< len; i++) {
		a=document.myform.elements[i].id.substr(0,1);
		if (a=='C') {
			document.myform.elements[i].checked=true;
		}
	}
}
function resel() {
	var i =0;

	for (var i=0, len=document.myform.elements.length; i< len; i++) {
		a=document.myform.elements[i].id.substr(0,1);
		if (a=='C') {
			document.myform.elements[i].checked=!document.myform.elements[i].checked;
		}
	}
}
</script>

<span class="small" style="line-height:18pt;">����H�G<input type="radio" name="re" value="1" checked>�ǥͮa�� <input type="radio" name="re" value="2">�ǥͥ��H</span><br>
<input type="submit" name="print" value="�C�L�W�U">
<input type="button" value="����" OnClick="selall();">
<input type="button" value="�Ͽ�" OnClick="resel();">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="2" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td>��</td>
<td>�~��</td>
<td>�Z��</td>
<td>�y��</td>
<td>�m�W</td>
<td>��</td>
<td>�~��</td>
<td>�Z��</td>
<td>�y��</td>
<td>�m�W</td>
<td>��</td>
<td>�~��</td>
<td>�Z��</td>
<td>�y��</td>
<td>�m�W</td>
<td>��</td>
<td>�~��</td>
<td>�Z��</td>
<td>�y��</td>
<td>�m�W</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{assign var=year_name value=$i|@substr:0:-2}}
{{assign var=class_name value=$i|@substr:-2:2}}
{{foreach from=$seme_class item=d key=seme_num}}
{{assign var=j value=$j+1}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
{{if $j % 4==1}}
<tr style="background-color:white;">
{{/if}}
{{counter assign=i}}
<td><input type="checkbox" name="student_sn[{{$i}}]" id="C{{$i}}" value="{{$sn}}"></td>
<td style="background-color:#f4feff;">{{$year_name}}</td>
<td style="background-color:#f4feff;">{{$class_name}}</td>
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
{{if $j % 4==0}}
</tr>
{{/if}}
{{/foreach}}
{{foreachelse}}
<tr style="background-color:white;text-align:center;color:red;">
<td colspan="20">�L���</td>
</tr>
{{/foreach}}
</table>
<input type="submit" name="print" value="�C�L�W�U">
<input type="button" value="����" OnClick="selall();">
<input type="button" value="�Ͽ�" OnClick="resel();">
</td></tr></table>