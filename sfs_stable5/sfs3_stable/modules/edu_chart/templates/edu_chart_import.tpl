{{* $Id: edu_chart_import.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
	<form name ="base_form" enctype="multipart/form-data" action="{{$smarty.server.PHP_SELF}}" method="post" >
    <td width="100%" valign=top bgcolor="#CCCCCC">
		<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
			<tr>
				<td class="title_mbody" colspan="2" align="center" >匯入檔案</td>
			</tr>
			<tr>
				<td class="title_sbody1">選擇匯入資料</td>
				<td>{{$data_sel}}</td>
			</tr>
			<tr>
				<td class="title_sbody1">選擇上傳檔案</td>
				<td><input type="file" name="upload_file"></td>
			</tr>
			<tr>
	    	<td width="100%" align="center" colspan="2" >
				<input type=submit name="do_key" value =" 確定匯入 "></td>
			</tr>
		</table>
	</tr>
	</form>
</table>
{{if $smarty.post.data_id=="" || $smarty.post.data_id==0}}
<table>
<tr bgcolor='#FBFBC4'><td><img src="{{$SFS_PATH_HTML}}/images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td></tr>
<tr><td style="line-height: 150%;">
<ol>
<li class="small"><a href="student.csv">範例檔</a></li>
<li class="small">本上傳作業主要欄位為「學號」、「右眼裸視」、「左眼裸視」，也就是以學號為辨識依據，將右眼裸視、左眼裸視值寫入系統，所以其他欄位並不重要。</a></li>
</ol>
</td></tr>
</table>
{{/if}}
{{include file="$SFS_TEMPLATE/footer.tpl"}}
