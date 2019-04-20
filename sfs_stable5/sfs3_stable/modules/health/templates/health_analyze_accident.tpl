{{* $Id: health_analyze_accident.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

{{dhtml_calendar_init src="`$SFS_PATH_HTML`javascripts/calendar.js" setup_src="`$SFS_PATH_HTML`javascripts/calendar-setup.js" lang="`$SFS_PATH_HTML`javascripts/calendar-tw.js" css="`$SFS_PATH_HTML`javascripts/calendar-brown.css"}}
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="2" class="small" style="font-size:10pt;">
<tr style="color:white;text-align:center;line-height:18pt;font-size:14pt;">
<td colspan="2">查詢條件指定</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td nowrap>起迄時間</td>
<td style="background-color:#f4feff;text-align:left;">
<input type="text" id="start_date" name="start_date" value="{{$smarty.post.start_date}}" style="width:70px;"><input type="button" id="sdate" value="選擇時間"> ～ <input type="text" id="end_date" name="end_date" value="{{$smarty.post.end_date}}" style="width:70px;"><input type="button" id="edate" value="選擇時間">(空白代表不限定)
</td>
</tr>
</table>

<input type="submit" name="start_analyze" value="開始查詢">
{{dhtml_calendar inputField="start_date" button="sdate" singleClick=false}}
{{dhtml_calendar inputField="end_date" button="edate" singleClick=false}}