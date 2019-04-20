{{* $Id: stud_move_stud_move_home.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<script language="JavaScript">
	function checkok()	{
		var OK=true;	
		if(document.base_form.stud_class.value==0)	{
			alert('未選擇班級');
			OK=false;
		}
		if(document.base_form.student_sn.value=='')	{
			alert('未選擇學生');
			OK=false;
		}	
		if (OK==true) return confirm('確定新增 '+document.base_form.student_sn.options[document.base_form.student_sn.selectedIndex].text+' 在家自學記錄 ?');
		return OK
	}
//-->
</script>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100%" valign=top bgcolor="#CCCCCC">
    <form name ="base_form" action="{{$smarty.server.PHP_SELF}}" method="post" >
		<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
			<tr>
				<td class=title_mbody colspan=2 align=center > 在家自學學生作業 </td>
			</tr>
			<tr>
				<td class=title_sbody2>選擇班級</td>
				<td>{{$class_sel}}</td>
			</tr>
			<tr>
				<td class=title_sbody2>選擇學生</td>
				<td>{{$stud_sel}} </td>
			</tr>
			<tr>
	    	<td width="100%" align="center" colspan="5" >
	    	<input type="hidden" name="update_id" value="{{$smarty.session.session_log_id}}">
				<input type=submit name="do_key" value =" 確定在家自學 " onClick="return checkok()" >    </td>
			</tr>
		</table><br></td>
	</tr>
	<TR>
		<TD>
			<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body ><tr><td colspan=8 class=title_top1 align=center >本學期在家自學學生列表</td></tr>
				<TR class=title_mbody >				
					<TD>學號</TD>
					<TD>姓名</TD>				
					<TD>班級</TD>
					<TD>編修</TD>
				</TR>
				{{section loop=$stud_move name=arr_key}}
					<TR class=nom_2>
						{{assign var=cid value=$stud_move[arr_key].stud_class}}		
						<TD>{{$stud_move[arr_key].stud_id}}</TD>
						<TD>{{$stud_move[arr_key].stud_name}}</TD>					
						<TD>{{$class_list.$cid}}</TD>
						<TD><a href="{{$smarty.post.PHP_SELF}}?do_key=delete&student_sn={{$stud_move[arr_key].student_sn}}" onClick="return confirm('確定取消 {{$stud_move[arr_key].stud_name}} 在家自學記錄 ?');">刪除記錄</a></TD>
					</TR>
				{{/section}}
			</table></TD>
	</TR>
	<TR>
		<TD></TD>
	</TR>
	</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}