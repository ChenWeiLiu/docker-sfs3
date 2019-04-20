{{* $Id: health_input_worm.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
<script>
function go(a,b) {
	c='d'+b+'_'+a;
	d=document.getElementById(c).checked;
	if (d) {
		if (a==1) {
			for(i=2;i<=5;i++) {
				c='d'+b+'_'+i;
				document.getElementById(c).checked=false;
			}
			for(i=3;i<=5;i++) {
				c='d'+b+'_'+i;
				document.getElementById(c).disabled=true;
			}
			document.getElementById('v'+b+'_1').value=1;
			document.getElementById('v'+b+'_2').value='';
			document.getElementById('v'+b+'_3').value='';
		}
		if (a==2) {
			c='d'+b+'_1';
			document.getElementById(c).checked=false;
			for(i=3;i<=5;i++) {
				c='d'+b+'_'+i;
				document.getElementById(c).disabled=false;
			}
			document.getElementById('v'+b+'_1').value=2;
			document.getElementById('v'+b+'_2').value='';
		}
		if (a==3 || a==4) {
			c='d'+b+'_'+(7-a);
			document.getElementById(c).checked=false;
			document.getElementById('v'+b+'_1').value=2;
			document.getElementById('v'+b+'_2').value=(a-2);
		}
		if (a==5) {
			document.getElementById('v'+b+'_3').value='1';
		}
	} else {
		if (a==1) {
			document.getElementById('v'+b+'_1').value='';
		}
		if (a==2) {
			for(i=3;i<=4;i++) {
				c='d'+b+'_'+i;
				document.getElementById(c).checked=false;
				document.getElementById(c).disabled=true;
			}
			document.getElementById('v'+b+'_1').value='';
		}
		if (a==3 || a==4) {
			document.getElementById('v'+b+'_2').value='';
		}
		if (a==5) {
			document.getElementById('v'+b+'_3').value='';
		}
	}
}
function fillall() {
	var i =0;

	while (i < document.myform.elements.length)  {
		a=document.myform.elements[i].id.substr(0,1);
		if (a=='d') {
			b=document.myform.elements[i].id.length - 1;
			c=document.myform.elements[i].id.substr(b,1);
			if (c==1) {
				document.myform.elements[i].checked=true;
			} else {
				document.myform.elements[i].checked=false;
				if (c>2) document.myform.elements[i].disabled=true;
			}
		} else{
			if (a=='v') {
				b=document.myform.elements[i].id.length - 1;
				c=document.myform.elements[i].id.substr(b,1);
				if (c==1) document.myform.elements[i].value='1';
				if (c==2) document.myform.elements[i].value='';
			}
		}
		i++;
	}
}
</script>

<input type="submit" name="save" value="確定儲存">
<input type="reset" value="放棄儲存">
<input type="button" value="全部設為正常" OnClick="fillall();">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="2" class="small">
<tr bgcolor="#c4d9ff">
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">正常</td>
<td align="center">初檢<br>異常</td>
<td align="center">已<br>服藥</td>
<td align="center">複檢<br>正常</td>
<td align="center">複檢<br>異常</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">正常</td>
<td align="center">初檢<br>異常</td>
<td align="center">已<br>服藥</td>
<td align="center">複檢<br>正常</td>
<td align="center">複檢<br>異常</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">正常</td>
<td align="center">初檢<br>異常</td>
<td align="center">已<br>服藥</td>
<td align="center">複檢<br>正常</td>
<td align="center">複檢<br>異常</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data.$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->stud_data.$sn.$year_seme}}
{{assign var=ddd value=$health_data->health_data.$sn.$year_seme.exp.worm}}
{{if $smarty.foreach.rows.iteration % 3==1}}
<tr style="background-color:white;">
{{/if}}
{{counter assign=d}}
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
<td align="center"><input type="checkbox" {{if $ddd.1.status==1}}checked{{/if}} id="d{{$d}}_1" OnClick="go('1',{{$d}});"></td>
<td align="center"><input type="checkbox" {{if $ddd.1.status==2}}checked{{/if}} id="d{{$d}}_2" OnClick="go('2',{{$d}});"></td>
<td align="center"><input type="checkbox" {{if $ddd.1.med==1}}checked{{/if}} id="d{{$d}}_5" OnClick="go('5',{{$d}});" {{if $ddd.1.status<2}}disabled{{/if}}></td>
<td align="center"><input type="checkbox" {{if $ddd.2.status==1}}checked{{/if}} id="d{{$d}}_3" OnClick="go('3',{{$d}});" {{if $ddd.1.status<2}}disabled{{/if}}></td>
<td align="center"><input type="checkbox" {{if $ddd.2.status==2}}checked{{/if}} id="d{{$d}}_4" OnClick="go('4',{{$d}});" {{if $ddd.1.status<2}}disabled{{/if}}></td>
<input type="hidden" name="update[new][{{$sn}}][{{$year_seme}}][1][worm]" id="v{{$d}}_1" value="{{$ddd.1.status}}">
<input type="hidden" name="update[new][{{$sn}}][{{$year_seme}}][1][med]" id="v{{$d}}_3" value="{{$ddd.1.med}}">
<input type="hidden" name="update[new][{{$sn}}][{{$year_seme}}][2][worm]" id="v{{$d}}_2" value="{{$ddd.2.status}}">
<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][1][worm]" value="{{$ddd.1.status}}">
<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][1][med]" value="{{$ddd.1.med}}">
<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][2][worm]" value="{{$ddd.2.status}}">
{{if $smarty.foreach.rows.iteration % 3==0}}
</tr>
{{/if}}
{{/foreach}}
</table>
<input type="submit" name="save" value="確定儲存">
<input type="reset" value="放棄儲存">
<input type="button" value="全部設為正常" OnClick="fillall();">
