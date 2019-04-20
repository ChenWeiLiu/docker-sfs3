{{* $Id: health_input_wh.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
<script>
function chk_h(a) {
	b="h"+a;
	c=document.getElementById(b).value;
	if (c < 70 || c > 226) {
		alert("合理身高範圍應介於70公分～226公分之間！\n請重新輸入！");
		d="oh"+a;
		document.getElementById(b).value=document.getElementById(d).value;
		document.getElementById(b).focus();
	}
}
function chk_w(a) {
	b="w"+a;
	c=document.getElementById(b).value;
	if (c < 10 || c > 150) {
		alert("合理體重範圍應介於10公斤～150公斤之間！\n請重新輸入！");
		d="ow"+a;
		document.getElementById(b).value=document.getElementById(d).value;
		document.getElementById(b).focus();
	}
}
function restore() {
	if (confirm('先前未儲存的資料將會遺失!\n確定放棄?')) {
		document.myform.reset();
	}
}
function chk_file() {
	if (document.myform.upload_file.value=="") {
		alert("請先選擇上傳檔案");
	} else {
		document.myform.encoding="multipart/form-data";
		document.myform.submit();
	}
}
</script>

<input type="submit" name="save" value="確定儲存">
<input type="button" value="放棄儲存" OnClick="return restore();">
<input type="submit" name="csv" value="下載CSV檔">
<input type="file" name="upload_file">
<input type="button" name="upload" value="上傳CSV檔" OnClick="chk_file();">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="2" class="small">
<tr bgcolor="#c4d9ff">
{{if $smarty.post.wh_input}}
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">體重</td>
<td align="center">身高</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">體重</td>
<td align="center">身高</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">體重</td>
<td align="center">身高</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">體重</td>
<td align="center">身高</td>
{{else}}
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">身高</td>
<td align="center">體重</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">身高</td>
<td align="center">體重</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">身高</td>
<td align="center">體重</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">身高</td>
<td align="center">體重</td>
{{/if}}
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data.$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
{{if $smarty.foreach.rows.iteration % 4==1}}
<tr style="background-color:white;">
{{/if}}
{{counter assign=i}}
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
{{if $smarty.post.wh_input}}
<td align="center"><input type="text" id="w{{$i}}" name="update[new][{{$sn}}][{{$year_seme}}][weight]" value="{{$dd.weight}}" size="5" style="background-color:#f8f8f8;font-size:12px;" OnChange="chk_w('{{$i}}');"><input type="hidden" id="ow{{$i}}" name="update[old][{{$sn}}][{{$year_seme}}][weight]" value="{{$dd.weight}}"></td>
<td align="center"><input type="text" id="h{{$i}}" name="update[new][{{$sn}}][{{$year_seme}}][height]" value="{{$dd.height}}" size="5" style="background-color:#f8f8f8;font-size:12px;" OnChange="chk_h('{{$i}}');"><input type="hidden" id="oh{{$i}}" name="update[old][{{$sn}}][{{$year_seme}}][height]" value="{{$dd.height}}"></td>
{{else}}
<td align="center"><input type="text" id="h{{$i}}" name="update[new][{{$sn}}][{{$year_seme}}][height]" value="{{$dd.height}}" size="5" style="background-color:#f8f8f8;font-size:12px;" OnChange="chk_h('{{$i}}');"><input type="hidden" id="oh{{$i}}" name="update[old][{{$sn}}][{{$year_seme}}][height]" value="{{$dd.height}}"></td>
<td align="center"><input type="text" id="w{{$i}}" name="update[new][{{$sn}}][{{$year_seme}}][weight]" value="{{$dd.weight}}" size="5" style="background-color:#f8f8f8;font-size:12px;" OnChange="chk_w('{{$i}}');"><input type="hidden" id="ow{{$i}}" name="update[old][{{$sn}}][{{$year_seme}}][weight]" value="{{$dd.weight}}"></td>
{{/if}}
{{if $smarty.foreach.rows.iteration % 4==0}}
</tr>
{{/if}}
{{/foreach}}
</table>
</td></tr></table>
<input type="submit" name="save" value="確定儲存">
<input type="button" value="放棄儲存" OnClick="return restore();">
<input type="submit" name="csv" value="下載CSV檔">
{{*說明*}}
<table class="small">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>「下載CSV檔」所下載檔案內第一欄為年級班級、第二欄為座號、第三欄為學號、第四欄為身高、第五欄為體重。</li>
	<li>身高數值檢查合理範圍為70～226公分，體重數值檢查合理範圍為10～150公斤。</li>
	<li>若使用全自動身高體重測量儀，而儀器送出的數據為先體重後身高的話，請至「系統選項設定」→「身高體重輸入設定」完成正確設定再使用即可。</li>
	</ol>
</td></tr>
</table>
