{{* $Id: health_input_uri.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
<script>
function fillall() {
	var i =0;

	while (i < document.myform.elements.length)  {
		a=document.myform.elements[i].id.substr(0,1);
		if (a=='u') {
			b=document.myform.elements[i].id.length - 1;
			c=document.myform.elements[i].id.substr(b,1);
			if (c==4) {
				document.myform.elements[i].value=6;
			} else {
				document.myform.elements[i].value=0;
			}
		}
		i++;
	}
}
function chk_v(a) {
	b=document.getElementById(a).value;
	if (!(b==0 || b==1 || b==2 || b==3 || b==4)) {
		alert("合理值的範圍應為0、1、2、3、4價！\n請重新輸入！");
		c="o"+a;
		document.getElementById(a).value=document.getElementById(c).value;
		document.getElementById(a).focus();
	}
}
function chk_ph(a) {
	b=document.getElementById(a).value;
	if (b<2 || b>8) {
		alert("合理PH值的範圍應介於2～8之間！\n請重新輸入！");
		c="o"+a;
		document.getElementById(a).value=document.getElementById(c).value;
		document.getElementById(a).focus();
	}
}
function restore() {
	if (confirm('先前未儲存的資料將會遺失!\n確定放棄?')) {
		document.myform.reset();
	}
}
</script>

<input type="submit" name="save" value="確定儲存">
<input type="button" value="放棄儲存" OnClick="return restore();">
<input type="button" value="全部設為正常" OnClick="fillall();">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="2" class="small">
<tr bgcolor="#c4d9ff">
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">尿<br>蛋白</td>
<td align="center">尿糖</td>
<td align="center">潛血</td>
<td align="center">酸鹼<br>度</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">尿<br>蛋白</td>
<td align="center">尿糖</td>
<td align="center">潛血</td>
<td align="center">酸鹼<br>度</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">尿<br>蛋白</td>
<td align="center">尿糖</td>
<td align="center">潛血</td>
<td align="center">酸鹼<br>度</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">尿<br>蛋白</td>
<td align="center">尿糖</td>
<td align="center">潛血</td>
<td align="center">酸鹼<br>度</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data.$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->stud_data.$sn.$year_seme}}
{{assign var=ddd value=$health_data->health_data.$sn.$year_seme.exp.uri}}
{{if $smarty.foreach.rows.iteration % 4==1}}
<tr style="background-color:white;">
{{/if}}
{{counter assign=i}}
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
<td align="center"><input type="text" name="update[new][{{$sn}}][{{$year_seme}}][1][pro]" id="u{{$i}}_1" value="{{$ddd.1.pro}}" size="2" style="background-color:#f8f8f8;font-size:12px;" OnChange="chk_v('u{{$i}}_1')"></td>
<td align="center"><input type="text" name="update[new][{{$sn}}][{{$year_seme}}][1][glu]" id="u{{$i}}_2" value="{{$ddd.1.glu}}" size="2" style="background-color:#f8f8f8;font-size:12px;" OnChange="chk_v('u{{$i}}_2')"></td>
<td align="center"><input type="text" name="update[new][{{$sn}}][{{$year_seme}}][1][bld]" id="u{{$i}}_3" value="{{$ddd.1.bld}}" size="2" style="background-color:#f8f8f8;font-size:12px;" OnChange="chk_v('u{{$i}}_3')"></td>
<td align="center"><input type="text" name="update[new][{{$sn}}][{{$year_seme}}][1][ph]" id="u{{$i}}_4" value="{{$ddd.1.ph}}" size="4" style="background-color:#f8f8f8;font-size:12px;" OnChange="chk_ph('u{{$i}}_4')"></td>
<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][1][pro]" id="ou{{$i}}_1" value="{{$ddd.1.pro}}">
<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][1][glu]" id="ou{{$i}}_2" value="{{$ddd.1.glu}}">
<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][1][bld]" id="ou{{$i}}_3" value="{{$ddd.1.bld}}">
<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][1][ph]" id="ou{{$i}}_4" value="{{$ddd.1.ph}}">
{{if $smarty.foreach.rows.iteration % 4==0}}
</tr>
{{/if}}
{{/foreach}}
</table>
<input type="submit" name="save" value="確定儲存">
<input type="button" value="放棄儲存" OnClick="return restore();">
<input type="button" value="全部皆設為正常" OnClick="fillall();">

{{*說明*}}
<table class="small" style="width:100%">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>輸入方式：正常為0, +為1, ++為2, +++為3。</li>
	</ol>
</td></tr>
</table>
