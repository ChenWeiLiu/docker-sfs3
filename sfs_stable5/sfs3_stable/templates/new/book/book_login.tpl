{{* $Id: book_login.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/book_header.tpl"}}

<body  onload="setfocus()">
<script language="JavaScript">
<!--
function setfocus() {
      document.checkid.log_id.focus();
      return;
 }
-->
</script>
<table width="100%" style="border: 1px solid #5F6F7E; border-collapse:collapse;"><tr><td align="center" bgcolor='#DDFF78'>
<p>&nbsp;</p>
<form action='{{$SFS_PATH_HTML}}login.php' method='post' name='checkid'>
<table width='290' height='136' cellspacing='0' cellpadding='2' align='center' background='http://127.0.0.1/sfs3/themes/new/images/login_bg.png'>
<tr><td><br>
<table cellspacing='0' cellpadding='3' align='center'>
<tr class='small'>
<td nowrap>輸入代號</td>
<td nowrap><input type='text' name='log_id' size='20' maxlength='15'></td>
</tr>
<tr class='small'>
<td nowrap>輸入密碼</td>
<td nowrap><input type='password' name='log_pass' size='20' maxlength='15'></td>
</tr>
<tr class='small'>
<td nowrap>登入身份</td>
<td nowrap>
	<select name='log_who'>
	<option value='教師' selected>教師</option>
	<option value='家長'>家長</option>
	<option value='學生'>學生</option>
	<option value='其他'>其他</option>
	</select>
	<input type='submit' value='確定' name='B1'>
</td>
</tr>
</table>
<input type='hidden' name='go_back' value=''>
</td></tr>
</table>
</form>
<p align="center">
<font size="2">本項服務需檢查管理代號密碼， 若忘記，請洽系統管理者。</font>
<a href="javascript:history.back()">回上頁</a>
</p><p></p>
</td></tr></table>
{{include file="$SFS_TEMPLATE/book_footer.tpl"}}