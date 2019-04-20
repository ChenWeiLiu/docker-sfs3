{{* $Id: health_inject_noti.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
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

<fieldset class="small" style="width:40%;">
<legend style="color:blue;font-size:12pt;">接種疫苗</legend>
<input type="checkbox" name="inj[3]" {{if $smarty.post.inj.3}}checked{{/if}}>小兒麻痺口服疫苗<br>
<input type="checkbox" name="inj[4]" {{if $smarty.post.inj.4}}checked{{/if}}>減量破傷風白喉非細胞性百日咳混合疫苗（Tdap）<br>
<input type="checkbox" name="inj[5]" {{if $smarty.post.inj.5}}checked{{/if}}>日本腦炎疫苗<br>
<input type="checkbox" name="inj[7]" {{if $smarty.post.inj.7}}checked{{/if}}>麻疹、腮腺炎、德國麻疹混合疫苗<br>
<input type="checkbox" name="inj[8]" {{if $smarty.post.inj.8}}checked{{/if}}>流感疫苗
</fieldset><br>

<input type="submit" name="print" value="列印">
<input type="button" value="全選" OnClick="selall();">
<input type="button" value="反選" OnClick="resel();">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="2" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td>選</td>
<td>年級</td>
<td>班級</td>
<td>座號</td>
<td>姓名</td>
<td>選</td>
<td>年級</td>
<td>班級</td>
<td>座號</td>
<td>姓名</td>
<td>選</td>
<td>年級</td>
<td>班級</td>
<td>座號</td>
<td>姓名</td>
<td>選</td>
<td>年級</td>
<td>班級</td>
<td>座號</td>
<td>姓名</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{assign var=year_name value=$i|@substr:0:-2}}
{{assign var=class_name value=$i|@substr:-2:2}}
{{foreach from=$seme_class item=d key=seme_num name=rows}}
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
<tr><td colspan="20" style="background-color:white;text-align:center;color:red;">無資料</td></tr>
{{/foreach}}
</table>
<input type="submit" name="print" value="列印"> <input type="button" value="全選" OnClick="selall();"> <input type="button" value="反選" OnClick="resel();">
</td></tr></table>
</td>
</tr>
</table>
