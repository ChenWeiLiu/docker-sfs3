{{* $Id: edu_chart_login_edu_select.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}

<script>
<!--
function go() {
	document.base_form.submit();
	window.open("login_edu_page.php","select");
}
//-->
</script>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
	<form name ="base_form" enctype="multipart/form-data" action="login_edu_main.php" method="post" target="main">
    <td width="100%" valign=top bgcolor="#CCCCCC">
		<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
			<tr>
				<td class="title_sbody1">請輸入學校代碼</td>
				<td><input type="text" name="sch_id" value="{{$sch_id}}"></td>
			</tr>
			<tr>
				<td class="title_sbody1">請輸入密碼</td>
				<td><input type="password" name="login_pass" value=""></td>
			</tr>
			<tr>
	    	<td width="100%" align="center" colspan="2" >
				<input type=button name="do_key" value =" 確定登入 " OnClick="javascript:go();"></td>
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
<li class="small">請輸入『定期公務報表』網路作業填報系統之密碼。</a></li>
<li class="small">系統會直接帶出貴校在「學校設定」中所設定之學校代碼，若有錯誤請先更正。</a></li>
</ol>
</td></tr>
</table>
{{/if}}
