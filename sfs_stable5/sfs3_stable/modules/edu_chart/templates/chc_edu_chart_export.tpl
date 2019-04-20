{{* $Id: edu_chart_export.tpl 6590 2011-10-18 07:07:27Z infodaes $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
	<form name ="base_form" action="{{$smarty.server.PHP_SELF}}" method="post" >
    <td width="100%" valign=top bgcolor="#CCCCCC">
		<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
			<tr>
				<td class="title_mbody" colspan="2" align="center" >檔案下載</td>
			</tr>
			<tr>
				<td class="title_sbody1">選擇下載資料</td>
				<td>{{$data_sel}}</td>
			</tr>
			<tr>
				<td width="100%" align="center" colspan="2" >
					<input type=submit name="do_key" value =" 確定下載 ">
					{{if !$OK1 || !$OK2}}<input type="button" name="do_key" value =" 先上傳檔案 " OnClick="this.form.action='import.php';this.form.submit();">{{/if}}
				</td>
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
<li class="small">學生視力資料請由「學生健康資訊」模組(health)匯入或輸入資料，請務必有安裝此模組。</li>
<li class="small">請務必在<span class=like_button><a href=../stud_subkind/setsubkind.php target=_blank>學生身份類別與屬性</a></span>填列所有原住民(9)學生的族別, 及外籍配偶子女(100)之親代國籍。</a></li>
<li class="small">若貴校「外籍配偶子女」的身分註記代號不是100, 請至<span class=like_button><a href=../sfs_text/st1.php target=_blank>系統選項清單設定</a></span>中設定正確代號。</a></li>
<li class="small">104學年度修改。</a></li>
</ol>
</td></tr>
</table>
{{/if}}
{{include file="$SFS_TEMPLATE/footer.tpl"}}
