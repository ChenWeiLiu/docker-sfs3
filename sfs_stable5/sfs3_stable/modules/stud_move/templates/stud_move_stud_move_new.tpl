{{* $Id: stud_move_stud_move_new.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

{{dhtml_calendar_init}}
<script>
function showCalendar(id, format, showsTime, showsOtherMonths) {
	var el = document.getElementById(id);
	if (_dynarch_popupCalendar != null) {
		_dynarch_popupCalendar.hide();
	} else {
		var cal = new Calendar(1, null, selected, closeHandler);
		cal.weekNumbers = false;
		cal.showsTime = false;
		cal.time24 = (showsTime == "24");
		if (showsOtherMonths) {
			cal.showsOtherMonths = true;
		}
		_dynarch_popupCalendar = cal;
		cal.setRange(2000, 2030);
		cal.create();
	}
	_dynarch_popupCalendar.setDateFormat(format);
	_dynarch_popupCalendar.parseDate(el.value);
	_dynarch_popupCalendar.sel = el;
	_dynarch_popupCalendar.showAtElement(el.nextSibling, "Br");

	return false;
}
function closeHandler(cal) {
	cal.hide();
	_dynarch_popupCalendar = null;
}
function selected(cal, date) {
	cal.sel.value = date;
}
</script>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100%" valign=top bgcolor="#CCCCCC">
    <form name ="base_form" action="{{$smarty.server.PHP_SELF}}" method="post" >
		<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
			<tr>
				<td class=title_mbody colspan=2 align=center > 新生入學作業 </td>
			</tr>
			<tr>
				<td class=title_sbody2 >選擇學年度</td>
				<td>{{$year_id_sel}} 新生計 {{$tol}} 人： 男生：<font color="blue">{{$boy}}</font> 人 ,&nbsp; 女生：<font color="red">{{$girl}}</font> 人	      
    		</td>
			</tr>
			<tr>
				<td class=title_sbody2>生效日期</td>
				<td> 西元 <input type="text" size="10" maxlength="10" name="move_date" id="move_date" value="{{if $default_date}}{{$default_date}}{{else}}{{$smarty.now|date_format:"%Y-%m-%d"}}{{/if}}"><input type="reset" value="選擇日期" onclick="return showCalendar('move_date', '%Y-%m-%d', '12');"></td>
			</tr>
			<tr>
				<td align="right" CLASS="title_sbody1">異動核准機關名稱</td>
				<td><input type="text" size="30" maxlength="30" name="move_c_unit" value="{{$default_unit}}"></td>
			</tr>
			<tr>
				<td align="right" CLASS="title_sbody1">核准日期</td>
				<td> 西元 <input type="text" size="10" maxlength="10" name="move_c_date" id="move_c_date" value="{{if $default_c_date}}{{$default_c_date}}{{else}}{{$smarty.now|date_format:"%Y-%m-%d"}}{{/if}}"><input type="reset" value="選擇日期" onclick="return showCalendar('move_c_date', '%Y-%m-%d', '12');"></td>
			</tr>
			<tr>
				<td align="right" CLASS="title_sbody1">核准字</td>
				<td><input type="text" size="20" maxlength="20" name="move_c_word" value="{{$default_word}}"> 字 </td>
			</tr>
			<tr>
				<td align="right" CLASS="title_sbody1">核准號</td>
				<td> 第 <input type="text" size="14" maxlength="14" name="move_c_num" value="{{if $default_c_num}}{{$default_c_num}}{{/if}}"> 號 </td>
			</tr>
			<tr>
	    	<td width="100%" align="center" colspan="5" >
	    	<input type="hidden" name="update_id" value="{{$smarty.session.session_log_id}}">
				<input type=submit name="do_key" value =" 新增資料 " onClick="return confirm('確定新增 '+document.base_form.year_id.value+' 學年新生記錄 ?')" >    </td>
			</tr>
		</table><br></td>
	</tr>
	<TR>
		<TD>
			<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body ><tr><td colspan=8 class=title_top1 align=center >新生入學記錄</td></tr>
				<TR class=title_mbody >				
					<TD>生效日期</TD>
					<TD>學年度</TD>				
					<TD>核准單位</TD>
					<TD>字號</TD>
					<TD>筆數</TD>
					<TD>編修</TD>
				</TR>
				{{section loop=$stud_move name=arr_key}}
					<TR class=nom_2>		
						<TD>{{$stud_move[arr_key].move_date}}</TD>
						<TD>{{$stud_move[arr_key].move_year}}</TD>					
						<TD>{{if $stud_move[arr_key].move_c_unit}}{{$stud_move[arr_key].move_c_unit}}{{else}}<font color="red">尚未輸入</font>{{/if}}</TD>
						<TD>{{$stud_move[arr_key].move_c_date}} {{$stud_move[arr_key].move_c_word}}字第{{$stud_move[arr_key].move_c_num}}號</TD>
						<TD>{{$stud_move[arr_key].num}}</TD>
						<TD><a href="{{$smarty.post.PHP_SELF}}?do_key=edit&year={{$stud_move[arr_key].move_year}}&date={{$stud_move[arr_key].move_date}}&unit={{$stud_move[arr_key].move_c_unit}}&c_date={{$stud_move[arr_key].move_c_date}}&word={{$stud_move[arr_key].move_c_word}}&num={{$stud_move[arr_key].move_c_num}}">編輯</a>&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="{{$smarty.post.PHP_SELF}}?do_key=delete&move_year_seme={{$stud_move[arr_key].move_year_seme}}" onClick="return confirm('確定取消 {{$stud_move[arr_key].move_year}} 學年新生入學記錄 ?');">刪除記錄</a></TD>
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