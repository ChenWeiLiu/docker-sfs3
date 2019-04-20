{{* $Id: stud_basic_test_chart.tpl 6730 2012-03-26 15:47:34Z brucelyc $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<form name="menu_form" method="post" action="{{$smarty.server.SCRIPT_NAME}}">
<input type="hidden" name="step" value="{{$smarty.post.step}}">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td style="vertival-align:top;background-color:#CCCCCC;">
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF" width="100%" class="main_body">
<tr><td>學期：{{$year_seme_menu}} 年級：{{$class_year_menu}} 
{{if $smarty.post.sel}}《<span style="color:red";>{{if $smarty.post.sel==2}}101年擴大高中職(五專)免試入學 (申請模式){{else}}高中職免試入學 (薦送模式){{/if}}</span>》<input type="hidden" name="sel" value="{{$smarty.post.sel}}">{{/if}}
{{if $smarty.post.sel==2}}
<table class="main_body" cellspacing="0" cellpadding="0">
<tr style="vertical-align: top;"><td>
<br>應屆學生班級座號：
<br><textarea name="stud_str" cols="20" rows="20"></textarea>
<br><input type="submit" name="default" value="一般生證明單(列所選)"> <input type="submit" name="default_sp" value="特種身分生證明單(列所選)">
<br><input type="submit" name="n_all" value="一般生證明單(全年級)"> <input type="submit" name="sp_all" value="特種身分生證明單(全年級)">
<br>
</td><td><br>
{{if $smarty.post.stud_id}}
《<span style="color: blue;">新增非應屆學生</span>》<span style="color: white;">請選擇學生</span>
{{else}}
非應屆學生學號：<input type="text" size="7" maxlength="7" name="stud_id"><input type="submit" name="add" value="新增"><input type="submit" name="print" value="列印勾選">
{{/if}}
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF" class="main_body" width="300">
<tr style="background-color: #FFFFCC">
<td style="text-align: center;">選擇</td>
<td>入學學年度</td>
<td>學　號　　</td>
<td>姓　名　　</td>
</tr>
{{if $smarty.post.stud_id}}
{{foreach from=$rowdata item=d key=sn}}
<tr style="background-color: white;">
<td style="text-align: center;"><input type="radio" name="sn" value="{{$sn}}" OnClick="this.form.submit();"></td>
<td>{{$d.stud_study_year}}</td>
<td>{{$d.stud_id}}</td>
<td style="color: {{if $d.stud_sex==1}}blue{{else}}red{{/if}};">{{$d.stud_name}}</td>
</tr>
{{foreachelse}}
<tr><td colSpan="4" style="background-color: white; color:red; text-align: center;">目前無資料</td></tr>
{{/foreach}}
{{else}}
{{foreach from=$predata item=d key=sn}}
<tr style="background-color: white;">
<td style="text-align: center;"><input type="checkbox" name="sel_sn[{{$sn}}]"></td>
<td>{{$d.stud_study_year}}</td>
<td>{{$d.stud_id}}</td>
<td style="color: {{if $d.stud_sex==1}}blue{{else}}red{{/if}};">{{$d.stud_name}}</td>
</tr>
{{foreachelse}}
<tr><td colSpan="4" style="background-color: white; color:red; text-align: center;">目前無資料</td></tr>
{{/foreach}}
{{/if}}
</table>
</tr>
{{*說明*}}
<table class="small" width="100%">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;background-color:white;">
	<ol>
	<li>請先輸入學生班級座號(以換行分隔，四位數字，例：0102)，輸入完成後再選證明單型式。</li>
	</ol>
</td></tr>
</table>
{{elseif $smarty.post.sel==3}}
<br>學生班級座號：
<br><textarea name="stud_str" cols="20" rows="20"></textarea>
<br><input type="submit" name="TCC" value="中投版證明單"> <input type="submit" name="CHC" value="彰縣版證明單">
<br>
{{*說明*}}
<table class="small" width="100%">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;background-color:white;">
	<ol>
	<li>請先輸入學生班級座號(以換行分隔，四位數字，例：0102)，輸入完成後再選證明單型式。</li>
	</ol>
</td></tr>
</table>
{{else}}
<br><br>
<table border="0" style="font-size:12px;" bgcolor="#C0C0C0" cellpadding="3" cellspacing="1">
<tr bgcolor="#FFFFCC" align="center">
<td>選</td>
<td>項目</td>
<td>狀態</td>
<td>解除封存</td>
</tr>
<tr bgcolor="#EEEEEE" align="center">
<td><input type="radio" name="sel" value="2" {{if $chk_arr.2==1}}OnClick="this.form.submit();"{{else}}disabled="true"{{/if}}></td>
<td>101年擴大高中職(五專)免試入學(申請)</td>
<td><span style="color:{{if $chk_arr.2==1}}blue{{else}}red{{/if}};">{{if $chk_arr.2==1}}已封存{{else}}未封存{{/if}}</span></td>
<td>{{if $chk_arr.2==1}}<input type="submit" name="del[2]" value="解除">{{else}}---{{/if}}</td>
</tr>
<tr bgcolor="white" align="center">
<td><input type="radio" name="sel" value="3" {{if $chk_arr.3==1}}OnClick="this.form.submit();"{{else}}disabled="true"{{/if}}></td> 
<td>99年高中職免試入學(薦送)</td>
<td><span style="color:{{if $chk_arr.3==1}}blue{{else}}red{{/if}};">{{if $chk_arr.3==1}}已封存{{else}}未封存{{/if}}</span></td>
<td>{{if $chk_arr.3==1}}<input type="submit" name="del[3]" value="解除">{{else}}---{{/if}}</td>
</tr>
</table>
<br>
{{*說明*}}
<table class="small" width="100%">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;background-color:white;">
	<ol>
	<li>使用本功能前請先將資料封存（不允許重新計算），若需重新計算，請先將資料解除封存狀態。</li>
	</ol>
</td></tr>
</table>
{{/if}}
</tr>
</table>
</td></tr>
</table>
</form>
{{include file="$SFS_TEMPLATE/footer.tpl"}}
