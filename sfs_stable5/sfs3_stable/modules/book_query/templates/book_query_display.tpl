{{* $Id: book_query_display.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/book_header.tpl"}}

<style type="text/css">
<!--
table.formdata {
	border: 1px solid #5F6F7E;
	border-collapse:collapse ;
}

table.formdata th {
	border: 1px solid #5F6F7E;
	background-color:#E2E2E2;
	color:#000000 ;
	text-align:center;
	font-weight:normal;
	padding:2px 4px 2px 4px ;
	margin:0;
}

table.formdata th.title {
	border:0;
	background-color:#5F6F7E;
	color:white;
	text-align:left;
	font-size:16px;
	padding:2px 4px 2px 4px ;
	margin:0;
}

table.formdata td {
	border: 1px solid #5F6F7E;
	text-align:center;
	padding:2px 4px 2px 4px ;
	margin:0;
}

table.formdata td.out {
	border: 1px solid #5F6F7E;
	text-align:center;
	color: #FF0000;
	padding:2px 4px 2px 4px ;
	margin:0;
}

table.formdata tr {
	background-color: #FFFFFF;
	color: #000000;
	height:20px;
}

table.formdata tr:hover {
	background-color: #F0F0F0;
	color: #000000;
	height:20px;
}

table.formdata tr.altrow {
	background-color: #DFE7F2;
	color: #000000;
	height:20px;
}

table.formdata tr.altrow:hover {
	background-color: #F8F8F8;
	color: #000000;
	height:20px;
}
-->
</style>
<script>
<!--
function query_act(a,b)
{
	document.query_form.query.value="query";
	document.query_form.sel_1.value=a;
	document.query_form.content_1.value=b;
	document.query_form.submit();
}
function booking()
{
	alert("本功能尚未開放!");
}
-->
</script>

<table width="100%" style="border: 1px solid #5F6F7E; border-collapse:collapse;">
<tr>
<td align="center" bgcolor="#DDFF78" width="30%" valign="top">
<table width="90%" class="formdata">
  <caption style="color:blue;font-size:20px;">
  <br>相關查詢<br>&nbsp;
  </caption>
	<tr>
	<th class="title">這著者的更多作品</th>
	</tr>
	<tr>
	<td style="text-align:left;">{{if $data_arr.0.book_author}}‧<a href="#" OnClick="query_act('book_author','{{$data_arr.0.book_author}}')">{{$data_arr.0.book_author}}</a><br>{{/if}}&nbsp;</td>
	</tr>
	<tr>
	<th class="title">這出版者的更多出版品</th>
	</tr>
	<tr>
	<td style="text-align:left;">{{if $data_arr.0.book_maker}}‧<a href="#" OnClick="query_act('book_maker','{{$data_arr.0.book_maker}}')">{{$data_arr.0.book_maker}}</a><br>{{/if}}&nbsp;</td>
	</tr>
	<tr>
	<th class="title">網頁查詢</th>
	</tr>
	<tr>
	<td style="text-align:left;">
	{{if $data_arr.0.book_author}}‧<a href="http://www.google.com.tw/search?q={{$data_arr.0.book_author}}">{{$data_arr.0.book_author}}</a><br>{{/if}}
	{{if $data_arr.0.book_maker}}‧<a href="http://www.google.com.tw/search?q={{$data_arr.0.book_maker}}">{{$data_arr.0.book_maker}}</a><br>{{/if}}
	&nbsp;</td>
	</tr>
	<tr>
	<th class="title">書店查詢</th>
	</tr>
	<tr>
	<td style="text-align:left;">
	‧<a href="http://www.sanmin.com.tw/page-qsearch.asp?mscssid=&ct=search_booknm&x=18&y=14&qu={{$data_arr.0.book_name}}">三民網路書店</a><br>
	‧<a href="http://search.eslitebooks.com/search/searchResultSAP.asp?FontType=Trid&basadv=bas&Item=10&At=1&range=%B0%D3%AB%7E&SortType=0&qc=0&query={{$data_arr.0.book_name}}">誠品網路書店</a><br>
	‧<a href="http://search.books.com.tw/exep/openfind_all.php?key={{$data_arr.0.book_name}}">博客來網路書店</a><br>
	‧<a href="http://www.amazon.com/exec/obidos/external-search?tag=bookland0b&mode=books&Search=Search&keyword={{$data_arr.0.book_name}}">Amazon</a><br>&nbsp;
	</td>
	</tr>
</table>
</td>
<td align="left" bgcolor="#DDFF78" valign="top">
<form name ="query_form" action="{{$smarty.server.PHP_SELF}}" method="post">
<table width="70%" class="formdata">
  <caption style="color:blue;font-size:20px;">
  <br>查詢結果<br>&nbsp;
  </caption>
{{foreach from=$data_arr item=v key=i}}
	<tr>
    <th width="25%">書名</th><td>{{$data_arr.$i.book_name}}</td>
	</tr>
	<tr>
    <th>作者</th><td>{{$data_arr.$i.book_author}}</td>
	</tr>
	<tr>
    <th>出版地</th><td></td>
	</tr>
	<tr>
    <th>出版者</th><td>{{$data_arr.$i.book_maker}}</td>
	</tr>
	<tr>
    <th>出版日期</th><td>{{$data_arr.$i.book_myear}}</td>
	</tr>
	<tr>
    <th>ISBN</th><td>{{$data_arr.$i.book_isbn}}</td>
	</tr>
	<tr>
    <th>敘述</th><td></td>
	</tr>
	<tr>
    <th>版本</th><td></td>
	</tr>
{{foreachelse}}
  <tr>
    <td colspan="6" align="center">找不到符合查詢條件的資料</td>
  </tr>
{{/foreach}}
</table>
<br>
<input type="submit" value="重新查詢">
<br>&nbsp;
<table width="90%" class="formdata">
  <tr>
    <th colspan="6" style="background-color:#5F6F7E;border:0;color:white;">館藏 / 其他複本</th>
  </tr>
  <tr>
    <th>索書號</th>
    <th>館藏地</th>
    <th>現況</th>
    <th>到期日</th>
    <th>條碼號</th>
    <th></th>
  </tr>
{{foreach from=$oth_data_arr item=v key=i}}
  <tr {{if $i mod 2 == 1}}class="altrow"{{/if}}>
    <td>{{$oth_data_arr.$i.bookch1_id}}</td>
    <td></td>
{{assign var=s value=$oth_data_arr.$i.book_isout}}
    <td>{{$book_status_arr.$s}}</td>
    <td>{{if $s==1}}{{else}}-----{{/if}}</td>
    <td>{{$oth_data_arr.$i.book_id}}</td>
    <td>{{if $s==0}}<a href="#" OnClick="booking()">預約</a>{{else}}-----{{/if}}</td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="6" align="center">找不到符合查詢條件的資料</td>
  </tr>
{{/foreach}}
</table><br>
</td></tr>
<input type="hidden" name="sel_1" value="">
<input type="hidden" name="content_1" value="">
<input type="hidden" name="num" value="10">
<input type="hidden" name="query" value="">
</form></table>

{{include file="$SFS_TEMPLATE/book_footer.tpl"}}