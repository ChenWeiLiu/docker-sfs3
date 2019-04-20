{{* $Id: health_sight_ntu_noti.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
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

<input type="submit" name="print" value="列印清單">
<input type="submit" name="noti" value="列印通知單">
<input type="button" value="全選" OnClick="selall();">
<input type="button" value="反選" OnClick="resel();">
<span class="small">回條繳交日期<input type="text" name="rmonth" size="2">月<input type="text" name="rday" size="2">日</span>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="2" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td>選</td>
<td>年級</td>
<td>班級</td>
<td>座號</td>
<td>姓名</td>
<td>性別</td>
<td>身份證字號</td>
<td>診斷</td>
<td>其他診斷</td>
<td>醫院</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{assign var=year_name value=$i|@substr:0:-2}}
{{assign var=class_name value=$i|@substr:-2:2}}
{{foreach from=$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->stud_base.$sn}}
<tr style="background-color:white;">
{{counter assign=i}}
<td><input type="checkbox" name="student_sn[{{$i}}]" id="C{{$i}}" value="{{$sn}}"></td>
<td style="background-color:#f4feff;">{{$year_name}}</td>
<td style="background-color:#f4feff;">{{$class_name}}</td>
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $dd.stud_sex==1}}blue{{elseif $dd.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$dd.stud_name}}</td>
<td style="text-align:center;">{{if $dd.stud_sex==1}}男{{elseif $dd.stud_sex==2}}女{{/if}}</td>
<td>{{$dd.stud_person_id}}</td>
<td></td>
<td></td>
<td></td>
</tr>
{{/foreach}}
{{foreachelse}}
<tr><td colspan="10" style="background-color:white;text-align:center;color:red;">無資料</td></tr>
{{/foreach}}
</table>
<input type="submit" name="print" value="列印清單">
<input type="submit" name="noti" value="列印通知單">
<input type="button" value="全選" OnClick="selall();">
<input type="button" value="反選" OnClick="resel();">
</td></tr></table>
</td>
</tr>
</table>
