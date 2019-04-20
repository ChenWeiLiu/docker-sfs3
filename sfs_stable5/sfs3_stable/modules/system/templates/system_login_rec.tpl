{{* $Id: system_bad_login.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<form name="log" method="post" action="{{$smarty.server.PHP_SELF}}">
◎頁碼選擇：
{{foreach from=$pages_array item=v key=i}}
  <input type="radio" value="{{$i-1}}" name="pages" onclick="document.log.curr_page.value={{$i-1}}; document.log.submit();"{{if ($i-1)==$curr_page}} checked{{/if}}>{{$i}}  
{{/foreach}}
<br>◎每頁顯示筆數：{{$detail_list}}
<table border="0">
<tr><td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr style="text-align:center;color:blue;background-color:#bedcfd;" align='center'>
<td>NO.</td><td>登入時間</td><td>登入者身分</td><td>登入者流水號</td><td>登入者姓名</td><td>登入IP</td>
</tr>
{{foreach from=$rowdata item=v key=i}}
<tr bgcolor="white" align='center'>
<td>{{$curr_no++}}</td><td>{{$v.login_time}}</td><td>{{$v.who}}</td><td>{{$v.teacher_sn}}</td><td>{{$v.name}}</td><td>{{$v.ip}}</td>
</tr>
{{foreachelse}}
<tr bgcolor="white">
<td colspan="4" style="text-align:center;color:blue;">查無資料</td>
</tr>
{{/foreach}}
<input type="hidden" name="curr_page" value={{$curr_page}}>
</form>
</table>
</td>
</tr>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
