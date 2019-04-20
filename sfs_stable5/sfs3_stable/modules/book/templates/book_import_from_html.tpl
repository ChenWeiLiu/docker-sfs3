<html><head><title>匯入圖書資料</title></head>

<script>
<!--
function import_data() 
{
	n=document.import_form.book_no.value;
	ns=n.substring(0,1);
	window.opener.document.bookform.bookch1_id.selectedIndex=ns;
	window.opener.document.bookform.book_no.value=n;
	window.opener.document.bookform.book_name.value=document.import_form.book_name.value;
	window.opener.document.bookform.book_author.value=document.import_form.book_author.value;
	window.opener.document.bookform.book_maker.value=document.import_form.book_maker.value;
	d=document.import_form.book_myear.value;
	window.opener.document.bookform.book_myear.value=d.substr(0,4)+"-"+d.substr(4,2)+"-"+d.substr(6,2);
	if (document.import_form.book_bind.value.substring(0,2)=="精裝")
		window.opener.document.bookform.book_bind[0].checked=true;
	else
		window.opener.document.bookform.book_bind[1].checked=true;
	window.opener.document.bookform.book_price.value=document.import_form.book_price.value;
	window.close();
}
//-->
</script>

<body bgcolor="lemonchiffon">
<table>
<caption><font size=4><B>匯入圖書資料</b></font></caption>
<form name="import_form" action="{{$smarty.server.PHP_SELF}}" method="post">
<tr>
	<td align="right" valign="top">中國圖書分類號</td>
	<td><input type="text" size="40" maxlength="10" name="book_no" value="{{$data_arr.681.0.a}}"></td>
</tr>
<tr>
	<td align="right" valign="top">書名</td>
	<td><input type="text" size="40" maxlength="40" name="book_name" value="{{$data_arr.200.0.a}}"></td>
</tr>
<tr>
	<td align="right" valign="top">作者</td>
	<td><input type="text" size="20" maxlength="20" name="book_author" value="{{$data_arr.200.0.f}}"></td>
</tr>
<tr>
	<td align="right" valign="top">出版商</td>
	<td><input type="text" size="20" maxlength="20" name="book_maker" value="{{$data_arr.210.0.c}}"></td>
</tr>
<tr>
	<td align="right" valign="top">出版日期</td>
	<td><input type="text" size="8" maxlength="8" name="book_myear" value="{{$data_arr.100.0.a}}"> (格式：yyyymmdd)</td>
</tr>
<tr>
	<td align="right" valign="top">裝訂</td>
	<td><input type="text" size="20" maxlength="20" name="book_bind" value="{{$data_arr.010.0.b}}"></td>
</tr>
<tr>
	<td align="right" valign="top">定價</td>
	<td><input type="text" size="11" maxlength="11" name="book_price" value="{{$data_arr.010.0.d}}">元</td>
</tr>
<tr>
	<td align="right" valign="top">ISBN</td>
	<td><input type="text" size="13" maxlength="13" name="book_isbn"  value="{{$data_arr.010.0.a}}"></td>
</tr>
<tr>
	<td colspan=2 align=center><hr size=1>	
	<input type=button name=import value="確定匯入" OnClick="import_data()"></td>
</tr>
</form>
</table>
</body></html>