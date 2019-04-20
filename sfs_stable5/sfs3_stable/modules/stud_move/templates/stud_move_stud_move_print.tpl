{{* $Id: stud_move_stud_move_print.tpl 7395 2013-07-26 03:19:17Z infodaes $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

{{dhtml_calendar_init}}
<script>
<!--
var ss;
function chc() {
	document.base_form.action="movein_ps.php";
	document.base_form.year.value=ss;
	document.base_form.submit();
}
function tc_100_ps() {
	document.base_form.action="tc_100_ps.php";
	document.base_form.year.value=ss;
	document.base_form.submit();
}
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
//-->
</script>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100%" valign=top bgcolor="#CCCCCC">
    <form name ="base_form" action="{{$smarty.server.PHP_SELF}}" method="post" >
		<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
			<tr>
				<td class=title_mbody colspan=2 align=center > 異動報表作業 </td>
			</tr>
			<tr>
				<td class=title_sbody2>異動類別</td>
				<td>{{$move_kind_sel}}</td>	      
			</tr>
			{{if $smarty.post.move_kind}}
				{{if $year_seme_sel!=""}}
				<tr>
					<td class=title_sbody2>異動學期</td>
					<td>{{$year_seme_sel}}</td>	      
				</tr>
				{{/if}}
				{{if $smarty.post.move_kind!="13"}}
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
				{{/if}}
			<tr>
	    	<td width="100%" align="center" colspan="2">
	    	<input type="hidden" name="update_id" value="{{$smarty.session.session_log_id}}">
				<input type=submit name="do_key" value =" 列印封面 "> <input type=submit name="do_key" value =" 列印報表 "> {{if $smarty.post.move_kind=="13"}}<input type="button" value="網頁式報表" OnClick="chc()"><input type='hidden' name='year' value=''> {{else}}<input type="submit" name="do_key" value=" 填入文字號 ">{{/if}}<input type=submit name="do_key" value =" 列印封底內頁 "><input type="button" value="列印臺中市101學年度國小新生入學一覽表" OnClick="tc_100_ps()"></td>
			</tr>
		</table><br></td>
	</tr>
	<TR>
		<TD>
			<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body><tr><td colspan="8" class=title_top1 align=center>{{$cseme}}<Script>document.write(document.base_form.move_kind.options[document.base_form.move_kind.selectedIndex].text+'記錄')</Script></td></tr>
				<TR class=title_mbody >				
					{{if $form_kind=="1"}}
						<TD>選擇</TD>
						<TD>學號</TD>
						<TD>姓名</TD>
						<TD>出生年月日</TD>
						<TD>異動日期</TD>
						<TD>核准單位</TD>
						<TD>核准日期</TD>
						<TD>核准文字號</TD>
					{{else}}
						<TD>選擇</TD>
						<TD>學年度</TD>				
						<TD>異動日期</TD>
						<TD>核准單位</TD>
						<TD>核准日期</TD>
						<TD>核准文字號</TD>
						<TD>筆數</TD>
					{{/if}}
				</TR>
				{{section loop=$stud_move name=arr_key}}
					<TR class=nom_2>		
					{{if $form_kind=="1"}}
						<TD><input type="checkbox" name="choice[{{$stud_move[arr_key].move_id}}]"></TD>
						<TD>{{$stud_move[arr_key].stud_id}}</TD>
						<TD>{{$stud_move[arr_key].stud_name}}</TD>
						<TD>{{$stud_move[arr_key].stud_birthday}}</TD>							
						<TD>{{$stud_move[arr_key].move_date}}</TD>
						<TD>{{if $stud_move[arr_key].move_c_unit}}{{$stud_move[arr_key].move_c_unit}}{{else}}<font color="red">尚未輸入</font>{{/if}}</TD>
						<TD>{{$stud_move[arr_key].move_c_date}}</TD>
						<TD>{{$stud_move[arr_key].move_c_word}}字第{{$stud_move[arr_key].move_c_num}}號</TD>
					{{else}}
						<TD><input type="radio" name="choice[]" value="{{$stud_move[arr_key].move_year_seme}}_{{$stud_move[arr_key].move_c_unit}}_{{$stud_move[arr_key].move_c_date}}_{{$stud_move[arr_key].move_c_num}}" {{if $smarty.post.move_kind==13}}OnClick="ss={{$stud_move[arr_key].move_year}}"{{/if}}></TD>
						<TD>{{$stud_move[arr_key].move_year}}</TD>					
						<TD>{{$stud_move[arr_key].move_date}}</TD>
						<TD>{{if $stud_move[arr_key].move_c_unit}}{{$stud_move[arr_key].move_c_unit}}{{else}}<font color="red">尚未輸入</font>{{/if}}</TD>
						<TD>{{$stud_move[arr_key].move_c_date}}</TD>
						<TD>{{$stud_move[arr_key].move_c_word}}字第{{$stud_move[arr_key].move_c_num}}號</TD>
						<TD>{{$stud_move[arr_key].num}}</TD>
					{{/if}}
					</TR>
				{{/section}}
			{{/if}}
			</table></TD>
	</TR>
	<TR>
		<TD></TD>
	</TR>
	</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
