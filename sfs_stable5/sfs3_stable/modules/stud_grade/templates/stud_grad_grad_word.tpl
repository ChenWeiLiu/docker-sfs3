{{* $Id: stud_grad_grad_word.tpl 8399 2015-04-22 07:14:09Z chiming $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

{{dhtml_calendar_init src="`$SFS_PATH_HTML`javascripts/calendar.js" setup_src="`$SFS_PATH_HTML`javascripts/calendar-setup.js" lang="`$SFS_PATH_HTML`javascripts/calendar-tw.js" css="`$SFS_PATH_HTML`javascripts/calendar-brown.css"}}

<script>
function selected(cal, date) {
	cal.sel.value = date;
}
function tagall(name,status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].id==name) {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
function fillall(name,value) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].id==name) {
      document.myform.elements[i].value=value;
    }
    i++;
  }
}
</script>
<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<tr><td bgcolor="#FFFFFF">
<form name="myform" method="post" action="{{$smarty.server.PHP_SELF}}">
<table width="100%">
<tr>
<td>{{$year_seme_menu}} {{$class_year_menu}} {{if $smarty.post.year_seme}}{{$class_name_menu}} <select name="show_kind" onchange="this.form.submit()";>{{html_options options=$grad_kind_arr selected=$smarty.post.show_kind}}</select>{{/if}}</td>
</tr>
{{if $smarty.post.year_name}}
<tr>
<td>
<input type="submit" name="save" value="儲存">
<input type="button" value="全部畢業" onClick="javascript:tagall('grad',1);">
<input type="text" name="grad_date" id="grad_date" size="10" value={{$smarty.post.grad_date}}>
<input type="button" value="選擇日期" id="date_1">
<input type="button" value="日期複製" onClick="javascript:fillall('P_date',document.myform.grad_date.value);"><br>
<input type="text" name="grad_word" size="10" value={{$smarty.post.grad_word}}>
<input type="button" value="證書字複製" onClick="javascript:fillall('P_word',document.myform.grad_word.value);">
<input type="text" name="grad_num" size="10" value="">
<input type="submit" name="start_num" value="證書號起始值">
</tr>
<tr>
<td>
<table border="0" cellspacing="1" cellpadding="4" bgcolor="#cccccc" class="main_body">
<tr bgcolor="#ddddff" align="center">
<td>班級<td>座號<td>姓名<td>成績<td>畢業<td>修業<td>證書日期<td>證書字<td>證書號</td>
</tr>
{{foreach from=$seme_class item=class_name key=sn name=sn}}
<tr bgcolor="#E1ECFF" align="left">
<td>{{$class_base.$class_name}}<input type="hidden" name="seme_class[{{$sn}}]" value={{$seme_class.$sn}}>
<td>{{$seme_num.$sn}}
<td>{{$stud_name.$sn}}
<td>{{$grad_score.$sn}}
<td><input type="radio" id="grad" name="sure_grad[{{$sn}}]" value="1" {{if $smarty.post.sure_grad.$sn=="1" || $grad_kind.$sn==1}}checked{{/if}}>
<input type="hidden" name="stud_id[{{$sn}}]" value={{$stud_id.$sn}}>
<td><input type="radio" name="sure_grad[{{$sn}}]" value="2"  {{if $smarty.post.sure_grad.$sn=="2" || $grad_kind.$sn==2}}checked{{/if}}>
<td><input type="text" id="P_date" name="P_date[{{$sn}}]" size="10" maxlength="10" value="{{if $smarty.post.P_date[$sn]}}{{$smarty.post.P_date[$sn]}}{{elseif $P_date.$sn}}{{$P_date.$sn}}{{/if}}">
<td><input type="text" id="P_word" name="P_word[{{$sn}}]" size="16" maxlength="40" value="{{if $smarty.post.P_word[$sn]}}{{$smarty.post.P_word[$sn]}}{{elseif $P_word.$sn}}{{$P_word.$sn}}{{/if}}">
<td><input type="text" id="P_num" name="P_num[{{$sn}}]" size="16" maxlength="40" value="{{if $smarty.post.grad_num!=""}}{{$smarty.post.grad_num+$smarty.foreach.sn.iteration-1|string_format:$grade_num_len}}{{elseif $smarty.post.P_num.$sn!=""}}{{$smarty.post.P_num.$sn}}{{elseif $P_num.$sn}}{{$P_num.$sn}}{{/if}}"></td>
</tr>
{{/foreach}}
</table>
</td>
</tr>
{{/if}}
</table>
</form>
{{if  $smarty.post.year_name}}
{{dhtml_calendar inputField="grad_date" button="date_1" singleClick=false}}
{{/if}}
</td></tr>
</table>
{{include file="$SFS_TEMPLATE/footer.tpl"}}