{{* $Id: health_setup_check.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<script>
<!--
function selAll(){
	var i=0;
	while (i < document.myform.elements.length)  {
		a=document.myform.elements[i].id.substring(0,1);
		if (a=='c') {
			document.myform.elements[i].checked=!document.myform.elements[i].checked;
		}
		i++;
	}
}
function chkData(){
	var i=0, k=0, c=0;
	while (i < document.myform.elements.length)  {
		a=document.myform.elements[i].id.substring(0,1);
		if (a=='k' && document.myform.elements[i].checked) k=1;
		if (a=='c' && document.myform.elements[i].checked) c=1;
		i++;
	}
	if (k==0) {
		alert('健檢項目未選！');
		return;
	}
	if (document.myform.hospital.value=='') {
		alert('健檢醫院未輸入！');
		return;
	}
	if (document.myform.doctor.value=='') {
		alert('健檢醫師未輸入！');
		return;
	}
	if (c==0) {
		alert('健檢班級未選！');
		return;
	}
	document.myform.sure.value=1;
	document.myform.submit();
}
function del(a,b,c) {
	document.getElementById('d1').value=a;
	document.getElementById('d2').value=b;
	document.getElementById('d3').value=c;
	document.myform.submit();
}
-->
</script>

<fieldset class="small" style="width:40%;">
<legend style="color:blue;font-size:12pt;">健檢項目</legend>
<input type="checkbox" id="k1" name="checks[Oph]" value="Oph" {{if $smarty.post.checks.Oph}}checked{{/if}}>眼　　　 &nbsp;
<input type="checkbox" id="k2" name="checks[Ent]" value="Ent" {{if $smarty.post.checks.Ent}}checked{{/if}}>耳鼻喉 &nbsp;
<input type="checkbox" id="k3" name="checks[Hea]" value="Hea" {{if $smarty.post.checks.Hea}}checked{{/if}}>頭頸、胸、腹、脊柱四肢<br>
<input type="checkbox" id="k4" name="checks[Uro]" value="Uro" {{if $smarty.post.checks.Uro}}checked{{/if}}>泌尿生殖 &nbsp;
<input type="checkbox" id="k5" name="checks[Der]" value="Der" {{if $smarty.post.checks.Der}}checked{{/if}}>皮膚　 &nbsp;
<input type="checkbox" id="k6" name="checks[Ora]" value="Ora" {{if $smarty.post.checks.Ora}}checked{{/if}}>口腔
</fieldset>
<fieldset class="small" style="width:40%;">
<legend style="color:blue;font-size:12pt;">健檢醫院-醫師-日期</legend>
醫院<input type="text" name="hospital" value="{{$smarty.post.hospital|@stripslashes}}"><br>
醫師<input type="text" name="doctor" value="{{$smarty.post.doctor|@stripslashes}}"><br>
日期<input type="text" name="chkdate" value="{{if $smarty.post.chkdate}}{{$smarty.post.chkdate}}{{else}}{{$smarty.now|date_format:"%Y-%m-%d"}}{{/if}}">
</fieldset>
<fieldset class="small" style="width:80%;">
<legend style="color:blue;font-size:12pt;">健檢班級<span style="color:red;font-size:10pt;">(<input type="checkbox" OnClick="selAll();">全選)</span></legend>
<table class="small" style="width:100%;">
{{foreach from=$class_arr item=d key=i name=c}}
{{if $smarty.foreach.c.iteration%6==1}}
<tr>
{{/if}}
<td><input type="checkbox" id="c{{$i}}" name="sel[{{$i}}]" value="{{$i}}" {{if $smarty.post.sel.$i}}checked{{/if}}>{{$d}}</td>
{{if $smarty.foreach.c.iteration%6==0}}
</tr>
{{/if}}
{{foreachelse}}
<span style="font-size:12pt;color:red;">未選年級</span>
{{/foreach}}
</table>
</fieldset>
{{if $class_arr}}
<input type="button" value="確定儲存" OnClick="chkData();">
<input type="reset" value="放棄儲存">
<input type="hidden" name="sure" value="">
<input type="hidden" id="d1" name="del[subject]" value="">
<input type="hidden" id="d2" name="del[hospital]" value="">
<input type="hidden" id="d3" name="del[doctor]" value="">
<table bgcolor="#7e9cbd" cellspacing="1" cellpadding="4" class="small">
<tr style="background-color:#9ebcdd;color:white;text-align:center;">
<td>健檢年級</td><td>健檢學期</td><td>健檢項目</td><td>健檢單位</td><td>檢查醫師</td><td>受檢人數</td><td>健檢日期</td><td>功能選項</td>
</tr>
{{foreach from=$rowdata item=d}}
<tr bgcolor="white" style="text-align:center;">
<td>{{$d.cyear}}</td><td>&nbsp;{{$d.year}}學年第{{$d.semester}}學期 &nbsp;</td><td>{{$checks_item_arr[$d.subject]}}</td><td>{{$d.hospital}}</td><td>{{$d.doctor}}</td><td>{{$d.nums}}</td><td>{{$d.measure_date}}</td><td><input type="image" src="images/edit.gif"> <input type="image" src="images/delete.gif" OnClick="del('{{$d.subject}}','{{$d.hospital}}','{{$d.doctor}}');"></td>
</tr>
{{foreachelse}}
<tr bgcolor="white" style="text-align:center;">
<td colspan="8" style="color:red;">本學期尚未設定該年級資料</td>
</tr>
{{/foreach}}
</table>
{{/if}}
