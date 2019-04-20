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
				<td>{{$data_sel}} <input type='checkbox' name='blank_name' value='Y' checked>清空姓名 <input type='checkbox' name='quoted' value='Y'>雙引號包覆</td>
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
<li class="small">學生視力資料請由「學生健康資訊」模組匯入或輸入資料。</li>
<li class="small">下載檔案前必須先在「學生身份類別與屬性」內做好「原住民(9)」及「外籍配偶子女(100)」資料。</a></li>
<li class="small">若貴校「外籍配偶子女」的身分註記代號不是100, 請至模組變數中設定正確代號。</a></li>
<li class="small">原住民族別請填列在第一個屬性欄位(族別), 外籍配偶子女之親代國別請填列在第二個屬性欄位(國籍)。</a></li>
<li class="small">100學年度新增：家庭現況(1碼)，1=雙親，2=單親，3=寄親, 其資料取自學期輔導-家庭類型，未註記則預設為~~1.雙親。</a></li>
</ol>
</td></tr>
</table>
{{/if}}
{{include file="$SFS_TEMPLATE/footer.tpl"}}
