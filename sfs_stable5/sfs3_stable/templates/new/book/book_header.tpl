{{* $Id: book_header.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
<html>
<meta http-equiv="Content-Type" content="text/html; Charset=Big5">
<head>
<title>圖書管理系統</title>
<style type="text/css">
body	{
	background-color: #FFFFFF;
}
.title {
	font-size: 24px;
	background-color: #FFF878 ; 
	color: #1E3EBE; 
	padding: 4px;
}
.small{ 
	font-family: "Arial,taipei,新細明體";
	font-size: 12px
}
</style>
</head>

<body>
<table border="0" cellpadding="4" cellspacing="0" width="96%" align="center">
<tr><td align="center" bgcolor="#C9BE9E">
<img src="{{$BOOK_URL}}/images/logo.png" alt="" border="0" align="left">
</td></tr>
</table>
<table border="0" cellpadding="2" cellspacing="0" width="96%" align="center" class="small">
<tr bgcolor="#DACFFF">
<td></td><td nowrap>
<table border="0" cellpadding="0" cellspacing="0" align="left" class="small">
<tr>
{{foreach from=$menu_p item=v key=k}}
<td background="{{$BOOK_URL}}/images/button.png">&nbsp;<a href="{{$v}}">{{$k}}</a>&nbsp;</td><td>&nbsp;</td>
{{/foreach}}
</tr></table></td>
{{* $top_tool *}}
<td align="right" nowrap> </td>
<td width="200" align="right" nowrap>{{if $smarty.session.session_tea_name}}{{$smarty.session.session_tea_name}}登入｜<a href="{{$SFS_PATH_HTML}}login.php?logout=yes"><img src="{{$BOOK_URL}}/images/exit.png" alt="" width="16" height="16" hspace="3" border="0" align="absmiddle">登出</a>{{else}}未登入{{/if}}</td>
<td></td>
</tr>
</table>
<table border="0" cellpadding="4" cellspacing="0" width="96%" align="center">
<tr> <td valign="top">