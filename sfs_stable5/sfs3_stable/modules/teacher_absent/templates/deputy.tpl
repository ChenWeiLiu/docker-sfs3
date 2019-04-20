{{* $Id: list.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

{{include file="$SFS_TEMPLATE/header.tpl"}}

{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">

<tr><td bgcolor="#FFFFFF">

<table width="100%">

<form name="menu_form" method="post" action="{{$smarty.server.PHP_SELF}}">

<tr>

<td>{{$year_seme_menu}} {{$leave_teacher_menu}} {{$month}} {{$d_check4_menu}}</td>

</tr>

<tr><td>

<table border="0" cellspacing="1" cellpadding="4" width="100%" bgcolor="#cccccc" class="main_body">

<tr bgcolor="#E1ECFF" align="center">

<td>序號</td>
<td>教師姓名</td>
<td>假別</td>
<td>事由</td>
<td width=120>開始時間<br>結束時間</td>

<td>日數時數</td>

<td>課程安排</td>

<td>職務代理人</td>
<td>{{$check1}}</td>
<td>{{$check2}}</td>
<td>{{$check3}}</td>
<td>{{$check4}}</td>
</tr>

{{foreach from=$absent item=a name=absent}}

<tr bgcolor="#ddddff" align="center" OnMouseOver="sbar(this)" OnMouseOut="cbar(this)">

<td>
{{*$smarty.foreach.absent.iteration*}}
{{$a.id}}
</td>

<td><font size=3>{{$tea_arr[$a.teacher_sn]}}</font></td>
<td>{{$abs_kind_arr[$a.abs_kind]}}</td>
<td>{{$a.reason}}</td>
<td><font size=3>{{$a.start_date|date_format:"%Y-%m-%d %H:%M"}}<br>

{{$a.end_date|date_format:"%Y-%m-%d %H:%M"}}</font></td>

<td>{{$a.day}}日{{$a.hour}}時</td>

<td>
{{$course_kind_arr[$a.class_dis]}}<br>
</td>

<td>
{{$tea_arr[$a.deputy_sn]}}<br>
{{if $a.check1_sn =="0" }}
{{if $a.status=="1" }}
	<input type="image" src="images/del.png" name="del[{{$a.id}}]" alt="我要取消">
{{else}}

<font color="red">待確認</font><input type="image" src="images/edit.png" name="edit[{{$a.id}}]" alt="我要簽章"> 

{{/if}}
{{/if}}
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