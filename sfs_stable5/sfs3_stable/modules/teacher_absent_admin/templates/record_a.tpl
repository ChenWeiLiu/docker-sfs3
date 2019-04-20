{{* $Id: record.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

{{include file="$SFS_TEMPLATE/header.tpl"}}

{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor="#cccccc">

<tr><td bgcolor="#FFFFFF">

<form name="menu_form" method="post" enctype="multipart/form-data" action="{{$smarty.server.PHP_SELF}}">

<table>

<tr>

<td>{{$year_seme_menu}}</td>

</tr>

</table>

<table border="0" cellspacing="1" cellpadding="2" bgcolor="#cccccc" class="main_body">

<tr bgcolor="#E1ECFF" align="center">

<td>請假人</td>

<td bgcolor="#ffffff" align="left">{{$leave_teacher_menu}}</td>

</tr>

{{if $smarty.post.teacher_sn != ""}}


<tr bgcolor="#E1ECFF" align="center">

<td>假別</td>

<td bgcolor="#ffffff" align="left">{{$abs_kind}}</td>

</tr>

{{if $smarty.post.abs_kind != ""}}
<tr bgcolor="#E1ECFF" align="center">
<td>事由</td>
<td bgcolor="#ffffff" align="left"><input type="text" name="reason" value="{{$smarty.post.reason}}" size="30"></td>
</tr>
<tr bgcolor="#E1ECFF" align="center">

<td>開始日期 時間</td>

<td bgcolor="#ffffff" align="left"><input type="text" style='font-size: 18pt' name="start_date" value="{{if $smarty.post.start_date}}{{$smarty.post.start_date}}{{else}}{{$morning}}{{/if}}"><br><font color="#ff0000">(格式：{{$morning}})</font></td>

</tr>

<tr bgcolor="#E1ECFF" align="center">

<td>結束日期 時間</td>

<td bgcolor="#ffffff" align="left"><input type="text" style='font-size: 18pt' name="end_date" value="{{if $smarty.post.end_date}}{{$smarty.post.end_date}}{{else}}{{$evening}}{{/if}}"><br><font color="#ff0000">(格式：{{$evening}})</font></td>

</tr>
<tr bgcolor="#E1ECFF" align="center">

<td>共計</td>

<td bgcolor="#ffffff" align="left"><input type="text" name="day" value="{{$smarty.post.day}}" size="4">日<input type="text" name="hour" value="{{$smarty.post.hour}}" size="4">時</td>

</tr>

<tr bgcolor="#E1ECFF" align="center">

<td>課程安排</td>

<td bgcolor="#ffffff" align="left">{{$course_menu}}</td>

</tr>

<tr bgcolor="#E1ECFF" align="center">

<td>職務代理人</td>

<td bgcolor="#ffffff" align="left">{{$agent_menu}}</td>

</tr>

<tr bgcolor="#E1ECFF" align="center">
<td>證明文件</td>
<td bgcolor="#ffffff" align="left"><input type="text" name="note" value="{{$smarty.post.note}}" size="30"></td>
</tr>
    <tr bgcolor="#E1ECFF" align="center">
        <td>上傳證明文件</td>
        <td bgcolor="#ffffff" align="left">
            <input type="file" name="note_file" >
            {{if $smarty.post.note_file}}<a href="#" id="del-file">刪除檔案</a>{{/if}}
        </td></tr>
{{if $smarty.post.abs_kind == "52"}}

<tr bgcolor="#E1ECFF" align="center">
<td>出差地點</td>
	<td bgcolor="#ffffff" align="left"><input type="text" name="locale" value="{{$smarty.post.locale}}" size="30"></td>

</tr>
{{/if}}
{{/if}}

{{/if}}

</table>

{{if $smarty.post.abs_kind != ""}}

{{if $smarty.post.act == "edit"}}<input type="submit" name="sure" value="確定修改">{{else}}<input type="submit" name="sure" value="確定新增">{{/if}}

{{/if}}

{{if $smarty.post.act == "edit"}}<input type="hidden" name="act" value="edit"><input type="hidden" name="id" value="{{$id}}">{{else}}<input type="hidden" name="act" value="add">{{/if}}

</form>

{{if $smarty.post.abs_kind != ""}}

<table>

<tr bgcolor="#FBFBC4">

<td><img src="{{$SFS_PATH_HTML}}/images/filefind.png" width=16 height=16 hspace=3 border=0>相關說明</td>

</tr>

<tr>

<td style="line-height: 150%;">

<ol>

<li class="small">請注意日期時間格式中，日期與時間之間要有一個空白，否則讀出的值會出錯。</li>

</ol></td>

</tr>

</table>

{{/if}}

</tr>

</table><p>

</td>

</tr>

</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}

    <script type="text/javascript">
        $(function(){
            $("#del-file").click(function(){
                if (confirm('確定刪除?')){
                    $.get("delete-file.php?id={{$id}}",function(){
                        window.location.reload();
                    });
                }
            });
        });
    </script>