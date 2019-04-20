<html>
<head>
<meta http-equiv="Content-Type" content="text/html; Charset=Big5">
<title>{{$school_data.sch_cname_s}}學生視力不良清冊</title>
<script type="text/javascript" src="{{$SFS_PATH_HTML}}/javascripts/jquery.min.js"></script>
<style>
table { border:#000 2px solid; border-collapse:collapse; border-spacing:4; margin:auto; width:96% }
tr {background: #F7F7F7; margin:5px; text-align:center}
td ,th{border:#005 thin solid; padding:3px}
caption{font-size:18pt}
</style>
<script>
$(document).ready(function(){
	var manage_item = {"1":"視力保建","2":"點藥治療","3":"配鏡矯治","4":"家長未處理","5":"更換鏡片","6":"定期檢查","7":"遮眼治療","8":"另類治療","9":"配戴隱型眼鏡","N":"其它"}

});
</script>
</head>
<body >
<table>
<caption>{{$school_data.sch_cname_s}}學生視力不良清冊</caption>
<thead>
<tr>
<th rowspan="2">年級</th>
<th rowspan="2">班級</th>
<th rowspan="2">座號</th>
<th rowspan="2">姓名</th>
<th colspan="2">裸視</th>
<th colspan="2">矯正</th>
<th colspan="5">診斷與治療（右眼）</th>
<th colspan="5">診斷與治療（左眼）</th>
<th rowspan="2">處置</th>
<th rowspan="2">就診醫療院所</th>
</tr>
<tr>
<th>右眼</th>
<th>左眼</th>
<th>右眼</th>
<th>左眼</th>
<th>近視</th>
<th>遠視</th>
<th>弱視</th>
<th>散光</th>
<th>其他</th>
<th>近視</th>
<th>遠視</th>
<th>弱視</th>
<th>散光</th>
<th>其他</th>
</tr>
</thead>
<tbody>
{{foreach from=$data key=class_sn  item=row}}
<tr>
<td>{{$row.l.grade}}</td>
<td>{{$row.l.class}}</td>
<td>{{$row.l.number}}</td>
<td>{{$row.l.stud_name}}</td>
<td>{{$row.r.sight_o}}</td>
<td>{{$row.l.sight_o}}</td>
<td>{{$row.r.sight_r}}</td>
<td>{{$row.l.sight_r}}</td>
<td>{{if $row.r.My}}V{{/if}}</td>
<td>{{if $row.r.Hy}}V{{/if}}</td>
<td>{{if $row.r.Ast}}V{{/if}}</td>
<td>{{if $row.r.Amb}}V{{/if}}</td>
<td>{{if $row.r.other}}V{{/if}}</td>
<td>{{if $row.l.My}}V{{/if}}</td>
<td>{{if $row.l.Hy}}V{{/if}}</td>
<td>{{if $row.l.Ast}}V{{/if}}</td>
<td>{{if $row.l.Amb}}V{{/if}}</td>
<td>{{if $row.l.other}}V{{/if}}</td>
<td>
{{if $row.l.manage_id eq 'N'}}
N.{{$row.l.diag}}
{{else}}
{{if $row.l.manage_id}}{{$row.l.manage_id}}.{{$manage_item[$row.l.manage_id]}}{{/if}}
{{/if}}
</td>
<td>{{if $row.l.hospital}}{{$row.l.hospital}}{{/if}}</td>
</tr>
{{/foreach}}

</tbody>
</table>
<br />
<TABLE WIDTH=100% style="margin:auto;">
<TR style="font-size: 10pt;">
		  <TD WIDTH=25%>承辦人</TD>
		  <TD WIDTH=25%>組長</TD>
		  <TD WIDTH=25%>主任</TD>
		  <TD WIDTH=25%>校長</TD>
		  </TR>
<tr style="height:60px">
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
</TABLE>
<div style="float:right ;margin:20px;">
列印時間 : {{$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}}
</div>
</body>
</html>