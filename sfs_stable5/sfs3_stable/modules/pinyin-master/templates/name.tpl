<!-- $Id: name.htm 7049 2012-12-26 05:16:27Z smallduh $ -->
	{{include file="$SFS_TEMPLATE/header.tpl"}}
	{{include file="$SFS_TEMPLATE/menu.tpl"}}
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<style type="text/css">
table.formdata {
 border:1px solid gray;
 border-collapse:collapse;
 color:#fff;
 font:normal 18px verdana, arial, helvetica, sans-serif;
}

ul {
	font-size: 18px;
}
table.formdata tr {
  border: 1px solid #FFFFF2;
  background-color:#FFFFF2;
  color:#000000 ;
  text-align:left;
  font-weight:normal;
  padding:2px 4px 2px 4px ;
  margin:0;
}

td, th { color:#363636;
 padding:.4em;
}

tr { border:1px 
		dotted gray;
}
thead th, tfoot th { background:#5C443A;
 color:#FFFFFF;
 padding:3px 10px 3px 10px;
 text-align:left;
 text-transform:uppercase;
}

table.formdata tr.altrow {
  background-color: #DFE7F2;;
  color: #000000;
}

table.formdata  tr:hover {
  background-color: #CCCCCC;
  color: #000000;
}
table.formdata tr.altrow:hover {
  background-color: #CCCCCC;
  color: #000000;
}


span.groove {
	display: inline;
  width: 100%;
	height:350px;
	border: 1px solid #5F6F79;
	float: right;
  border-style:dashed;
	border-width:1px;
	border-color: #8f8f8f;
	border-radius: 9px;
}
span.msg{
  text-decoration:underline;
  font-size:150%
}
#columns, #top{
 width: 100%;
 padding: 10px;
 margin: 0px auto;
}

#top {
	padding: 0px;
}

#diva{
 color: #333;
 margin: 0px 5px 5px 0px;
 padding: 10px;
 width: 45%;
 float: left;
}

#divb{
 color: #333;
 margin: 20px 0px 0px 0px;
 padding: 10px;
 width: 35%;
 display: inline;
 float: left;
}
</style>
</head>

<div id="columns">
 <div id="top">
	<div id="diva">
		<form name='form1' method='post' action=''>
		{{$class_select}}
		</form>

<Form id="form1" method="POST" action="sys/pinyin.module.php" target="_blank">
	 <input name="upload_path" type="hidden" value="{{$upload_path}}">
{{foreach key=id item=stud from=$raw_data }}
	 <input name="raw_data[{{$stud.stud_id}}]" type="hidden" value="{{$stud.urlencode_stud_name}}">
	 <input name="keep_data[{{$stud.stud_id}}][number]" type="hidden" value="{{$stud.site_num}}">
	 <input name="keep_data[{{$stud.stud_id}}][exist_eng_name]" type="hidden" value="{{$stud.stud_name_eng}}">
{{/foreach}}
	 <input name="keep_data[class_name]" type="hidden" value="{{$keep_data.class_name}}">
		  <input type="submit"  class="go" name="comentari" value="送出" style="position:relative; left: 0%;">
			</Form>
		<table class="formdata" >
			<th>
			姓名
			</th>
			<th>
			目前姓名譯音
			</th>
		<tr>

{{foreach key=id item=stud from=$raw_data }}
<tr>
{{if $id is not div by 2}}
  <tr class="altrow">
{{else }}
  <tr>
{{/if}}

<td>
	{{$stud.site_num}}
	{{$stud.stud_name}}
</td>
<td>
	{{$stud.stud_name_eng}}
	
</td>
</tr>
{{*$raw_data | @print_r*}}
{{/foreach}}

		</table>
	</div>

	<div id="divb">
	<span class="groove">
	<span class="msg">{{$system_title}}</span>
	<ul>
	<li> 拼音方式預選為漢語拼音,請依學生意願更改</li>
  <li> 學生正式文件應與護照簽證的英文譯音一致</li>
  <li> 人名之譯寫，必須尊重當事人之意願。</li>
	</ul> 
	</span>
	</div>


</div>
</div>
{{include file="$SFS_TEMPLATE/footer.tpl"}}

