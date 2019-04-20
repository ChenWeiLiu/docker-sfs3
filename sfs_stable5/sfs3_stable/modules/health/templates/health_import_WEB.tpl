{{* $Id: health_import_WEB.tpl 5500 2009-06-17 16:21:00Z brucelyc $ *}}

<table border="0" cellspacing="0" cellpadding="0"><tr><td style="vertical-align:top;">
<table cellspacing="1" cellpadding="3" class="main_body">
<tr bgcolor="#FFFFFF">
</form>
<form name="form0" enctype="multipart/form-data" action="{{$smarty.server.PHP_SELF}}" method="post">
<td class="title_sbody1" nowrap>上傳檔案：</td>
<td><input type="file" name="upload_file"><input type="submit" name="doup_key" value="上傳"><input type="hidden" name="sub_menu_id" value="{{$smarty.post.sub_menu_id}}"></td>
</form>
</tr>
<form name="form1" action="{{$smarty.server.PHP_SELF}}" method="post">
<tr bgcolor="#FFFFFF">
<td class="title_sbody1" nowrap>匯入檔案類別：</td>
<td>
	<select name="fkind">
	{{html_options options=$file_kind_arr selected=$smarty.post.fkind}}
	</select>
</td>
</tr>
<tr bgcolor="#FFFFFF">
<td class="title_sbody1" nowrap>伺服器內存檔案：</td>
<td>{{$file_menu}}{{if $chk_file}} &nbsp;<span style="color:red;">({{$chk_file}})</span>{{/if}}<input type="hidden" name="sub_menu_id" value="{{$smarty.post.sub_menu_id}}"></td>
</tr>
{{if $rowdata}}
<tr bgcolor="#FFFFFF">
<td class="title_sbody1">資料解析測試：</td>
<td>
<table border="0" cellspacing="0" cellpadding="0"><tr><td style="vertical-align:top;">
<table cellspacing="1" cellpadding="3" class="main_body">
<tr class="title_sbody1" style="text-align:center;">
{{if $smarty.post.fkind=="health_wh"}}
<td>學號</td><td>學生姓名</td><td>學年</td><td>學期</td><td>身高</td><td>體重</td>
</tr>
<tr bgcolor="#FFFFFF" style="text-align:center;">
<td>{{$stud_id}}</td><td>{{$stud_name}}</td><td>{{$rowdata.1}}</td><td>{{$rowdata.2}}</td><td>{{$rowdata.4}}</td><td>{{$rowdata.3}}</td>
{{/if}}
{{if $smarty.post.fkind=="health_sight"}}
<td>學號</td><td>學生姓名</td><td>學年</td><td>學期</td><td>裸視右</td><td>裸視左</td><td>矯視右</td><td>矯視左</td><td>測量日期</td>
</tr>
<tr bgcolor="#FFFFFF" style="text-align:center;">
<td>{{$stud_id}}</td><td>{{$stud_name}}</td><td>{{$rowdata.1}}</td><td>{{$rowdata.2}}</td><td>{{$rowdata.4}}</td><td>{{$rowdata.3}}</td><td>{{$rowdata.6}}</td><td>{{$rowdata.5}}</td><td>{{$rowdata.15}}</td>
{{/if}}
{{if $smarty.post.fkind=="health_teeth"}}
<td>學號</td><td>學生姓名</td><td>年級</td><td>學期</td><td>齲齒</td><td>缺齒</td><td>口腔衛生不良</td><td>牙結石</td><td>牙周炎</td><td>齒列咬合不正</td><td>牙齦炎</td><td>口腔黏膜異常</td><td>狀態</td>
</tr>
<tr bgcolor="#FFFFFF" style="text-align:center;">
<td>{{$stud_id}}</td><td>{{$stud_name}}</td><td>{{$rowdata.1}}</td><td>{{$rowdata.2}}</td><td>{{$rowdata.4}}</td><td>{{$rowdata.5}}</td><td>{{$rowdata.6}}</td><td>{{$rowdata.7}}</td><td>{{$rowdata.8}}</td><td>{{$rowdata.9}}</td><td>{{$rowdata.10}}</td><td>{{$rowdata.15}}</td><td style="color:{{if $ok}}blue{{else}}red{{/if}};">{{if $ok}}檔案格式正確{{else}}檔案格式有誤{{/if}}</td>
</tr>
<tr bgcolor="#FFFFFF">
<td colspan="2" class="title_sbody1" style="text-align:center;">口檢表</td><td colspan="11">{{$rowdata.13}}</td>
{{/if}}
{{if $smarty.post.fkind=="health_inject"}}
<td>學號</td><td>學生姓名</td><td>疫苗類別</td><td>已施打劑數</td>
</tr>
<tr bgcolor="#FFFFFF" style="text-align:center;">
<td>{{$stud_id}}</td><td>{{$stud_name}}</td><td>{{$rowdata.1}}</td><td>{{$rowdata.2}}</td>
{{/if}}
</tr>
</table>
<input type="submit" name="sure" value="確定匯入">
</td></tr></table>
</td>
</tr>
{{/if}}
</form>
</table>
{{*說明*}}
<table class="small">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>請直接從萬豐健康資訊系統Web版<span style="color:red;">(V8.42以上版本)</span>「資料處理」→「資料匯入匯出」→「匯出CSV」匯出各項資料。</li>
	<li>本作業分下列階段進行：<br>(1)「上傳檔案」：先將所需檔案上傳。<span style="color:red;">(請勿使用中文檔名並確認附檔名為CSV)</span><br>(2)選擇「匯入檔案類別」與「伺服器內存檔案」：請先選擇「匯入檔案類別」，再選擇「伺服器內存檔案」。<br>(3)「資料解析測試」：系統會自動從檔案中解析對應得到學生基本資料的最前面一筆資料。<br>(4)「確定匯入」：如確定系統解析出的資料無誤，即可進行匯入作業。</li>
{{if $rowdata}}
	<li style="color:red;">請務必確認「資料分析測試」欄的資料類型正確再進行匯入。</li>
{{/if}}
	</ol>
</td></tr>
</table>
</td>
</tr>
</table>
