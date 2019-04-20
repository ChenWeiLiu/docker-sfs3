{{* $Id: book_query_query.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/book_header.tpl"}}

<style type="text/css">
<!--
table.formdata {
	border: 1px solid #5F6F7E;
	border-collapse:collapse ;
}
table.formdata th {
	background-color:#E2E2E2;
	text-align:center;
	padding:2px 4px 2px 4px ;
	margin:0;
}

table.formdata td {
	text-align:center;
	padding:2px 4px 2px 4px ;
	margin:0;
}

table.formdata tr {
	height:20px;
}
-->
</style>
<script>
<!--
function check_content(){
	if (document.query_form.content_1.value=="" && document.query_form.content_2.value=="" && document.query_form.content_3.value=="") {
		alert("未輸入查詢條件!");
	} else {
		document.query_form.submit();
	}
}
-->
</script>

<table width="100%" style="border: 1px solid #5F6F7E; border-collapse:collapse;"><tr><td align="center" bgcolor='#DDFF78'>
<table width="90%" class="formdata">
  <caption style="color:blue;font-size:20px;">
  <br>查詢條件設定<br>&nbsp;
  </caption>
<tr><th>
<table>
<form name ="query_form" action="{{$smarty.server.PHP_SELF}}" method="post">
<tr><td>
<select name="sel_1">{{html_options options=$sel_arr selected="book_name"}}</select>
</td><td>
<input type="text" name="content_1" size="40">
</td></tr>
<tr><td>
<select name="logic_1">{{html_options options=$logic_arr selected="and"}}</select>
</td><td></td></tr>
<tr><td>
<select name="sel_2">{{html_options options=$sel_arr selected="book_name"}}</select>
</td><td>
<input type="text" name="content_2" size="40">
</td></tr>
<tr><td>
<select name="logic_2">{{html_options options=$logic_arr selected="and"}}</select>
</td><td></td></tr>
<tr><td>
<select name="sel_3">{{html_options options=$sel_arr selected="book_name"}}</select>
</td><td>
<input type="text" name="content_3" size="40">
</td></tr>
<tr><td colspan="2" class="small">
顯示結果每頁筆數<select name="num">{{html_options options=$num_arr selected="10"}}</select>
</td></tr>
<tr><td colspan="2">
<input type="button" OnClick="check_content();" OnKeyPress="check_content();" value="開始查詢">
<input type="hidden" name="query" value="query">
<input type="hidden" name="start_num" value="0">
</td></tr>
</form>
</table>
</th></tr>
</table>
<p>&nbsp;</p>
</td></tr></table>

{{include file="$SFS_TEMPLATE/book_footer.tpl"}}
