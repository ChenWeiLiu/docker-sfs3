{{* $Id: add.tpl 8885 2016-04-28 15:31:27Z tuheng $ *}}
{{dhtml_calendar_init src="`$SFS_PATH_HTML`javascripts/calendar.js" setup_src="`$SFS_PATH_HTML`javascripts/calendar-setup.js" lang="`$SFS_PATH_HTML`javascripts/calendar-tw.js" css="`$SFS_PATH_HTML`javascripts/calendar-brown.css"}}
<Script src="../../include/ckeditor/ckeditor.js"></Script>
{{if $smarty.get.edit}}
{{assign var=editdata value=$this->getData($smarty.get.edit)}}
{{assign var=room_id value=$editdata.room_id}}
{{else}}
{{assign var=room_id value=$this->get_user_room_id()}}
{{/if}}
<FORM METHOD=POST ACTION='{{$smarty.server.PHP_SELF}}' Name='e1' enctype='multipart/form-data'>
<table style="width:100%">
<tr>
<td>公告日期</td>
<td><input type='text' name='open_date' value='{{$editdata.open_date}}' size='10' id="open_date"> <button id="date_1" title="選擇日期">...</button></td>
</tr>
<tr><td>處室</td>
<td>
<select name ="room_id">
{{html_options options=$this->getRoomArr() selected=$room_id}}
</select>
</td>
</tr>
<tr><td>主旨</td>
<td><input type="text"  name="title" size=60 value='{{$editdata.title}}'></td></tr>
<tr><td>內容</td>
<td>
<textarea name="report_content" wrap=virtual>{{$editdata.content}}</textarea>
</td></tr>
<tr>
<td></td>
<td>
<INPUT TYPE='hidden' Name='form_act' value=''>
<INPUT TYPE='hidden' Name='page' Value='{{$this->page}}'>
<INPUT TYPE='hidden' Name='year_seme' Value='{{if $editdata}}{{$editdata.year_seme}}{{else}}{{$smarty.request.year_seme}}{{/if}}'>
<INPUT TYPE='hidden' Name='week_num' Value='{{if $editdata}}{{$editdata.weeks}}{{else}}{{$smarty.request.week_num}}{{/if}}'>
<INPUT TYPE='button' value='填好送出' onclick="if( window.confirm('要寫入了喔？確定？')){this.form.form_act.value='{{if $editdata}}update{{else}}add{{/if}}';this.form.submit()}">
{{if $editdata}}<input type="hidden" name="id"  value="{{$editdata.id}}">{{/if}}
<INPUT TYPE='button' value='取消返回' onclick="history.back();" class=bur2>
</td></tr>
</table>
</FORM>
{{dhtml_calendar inputField="open_date" button="date_1" singleClick=false}}
