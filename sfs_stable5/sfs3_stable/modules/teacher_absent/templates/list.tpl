{{* $Id: list.tpl 9132 2017-08-22 04:51:08Z chiming $ *}}

{{include file="$SFS_TEMPLATE/header.tpl"}}

{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">

<tr><td bgcolor="#FFFFFF">

<table width="100%">

<form name="menu_form" method="post" action="{{$smarty.server.PHP_SELF}}">

<tr>

<td>{{$year_seme_menu}} {{$abs_kind}} {{$month}} {{$d_check4_menu}} {{$sum_day}}　<a  href='record.php'><u>新增假單</u></a>、<a  href='person_year.php'><u>年度列表</u></a></td>

</tr>

<tr><td>

<table border="0" cellspacing="1" cellpadding="4" width="100%" bgcolor="#cccccc" class="main_body">

<tr bgcolor="#E1ECFF" align="center">

<td >序別</td>
<td>教師姓名</td>
<td>假別</td>
<td>事由</td>
<td width=120>開始時間<br>結束時間</td>

<td>日數時數</td>

<td>課務</td>

<td>職務代理人</td>
<td>{{$check1}}</td>
<td>{{$check2}}</td>
<td>{{$check3}}</td>
<td>{{$check4}}</td>
</tr>

{{foreach from=$absent item=a name=absent}}

<tr bgcolor="#ddddff" align="center" OnMouseOver="sbar(this)" OnMouseOut="cbar(this)">

<td width=30>{{$a.id}}{{$a.status}}
<br>
{{if ($a.status=="0" and $a.check1_sn =="0" and $a.check2_sn =="0" and $a.check3_sn =="0" and $a.check4_sn =="0") or ($isnotteacher==3 and $a.status=="0" and $a.check1_sn =="0" and $a.check3_sn =="0" and $a.check4_sn =="0") }}
	<input type="image" src="images/edit.png" name="edit[{{$a.id}}]" alt="修改資料"> 
	<input type="image" src="images/del.png" name="del[{{$a.id}}]" alt="刪除假單">
{{/if}}
</td>

<td>{{$tea_arr[$a.teacher_sn]}}</td>
<td>{{$abs_kind_arr[$a.abs_kind]}}
	{{if ($a.abs_kind==52 )}} 
		<input type="image" src="images/supply.png" name="outlay[{{$a.id}}]" alt="差旅費處理">
	{{/if}}
	<br><font color=blue>{{$a.note}}</font></td>
<td>{{$a.reason}}<br><font color=blue>{{$a.locale}}</font>
{{if $a.note_file}}<a href="{{$upload_url}}school/teacher_absent/{{$a.note_file}}" target=_blank>下載證明文件</a>{{/if}}
</td>
<td><font size=3>{{$a.start_date|date_format:"%Y-%m-%d %H:%M"}}<br>

{{$a.end_date|date_format:"%Y-%m-%d %H:%M"}}</font></td>

<td>
{{if $a.day>0 }}{{$a.day}}日{{/if}}
{{if $a.hour>0 }}{{$a.hour}}時{{/if}}
</td>

<td>
{{$course_kind_arr[$a.class_dis]}}<br>

<input type="image" src="images/supply.png" name="class_t[{{$a.id}}]" alt="課務處理">

</td>

<td><font size=3>
{{$tea_arr[$a.deputy_sn]}}</font><br>
<font color="{{if $a.status=="1"}}black{{else}}red{{/if}}">{{$status_kind_arr[$a.status]}}</font>
</td>
<td>{{$tea_arr[$a.check1_sn]}}</td>
<td>{{$tea_arr[$a.check2_sn]}}</td>
<td>{{$tea_arr[$a.check3_sn]}}</td>
<td>{{$tea_arr[$a.check4_sn]}}</td>


</tr>

{{/foreach}}

</table></td>

</tr>

</form>

</table></td>

</tr>

</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}



<script language="JavaScript1.2">

<!-- Begin

function sbar(st){st.style.backgroundColor="#F3F3F3";}

function cbar(st){st.style.backgroundColor="";}

//  End -->

</script>
