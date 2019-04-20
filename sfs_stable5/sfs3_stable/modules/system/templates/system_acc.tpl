{{* $Id: system_acc.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table bgcolor="#DFDFDF" cellspacing="1" cellpadding="4" class="small">
<form name="myform" method="post" action="{{$smarty.server.PHP_SELF}}">
<tr>
<td bgcolor="#FFFFFF">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" class="small">
<tr style="color:blue;background-color:#bedcfd;">
<td>系統暫存目錄</td><td>設定狀況</td>
</tr>
<tr bgcolor="white">
<td><input type="text" name="dir_name" value="{{if $smarty.post.dir_name}}{{$smarty.post.dir_name}}{{else}}/tmp{{/if}}"></td>
<td style="color:red;text-align:center;">{{if $status}}開啟{{else}}關閉{{/if}}</td>
</tr>
</table>
<input type="submit" name="test" value="測試並儲存">
<input type="submit" name="cancel" value="取消設定">
<br><br>
{{if $err_msg}}<div style="color:red;">測試結果：{{$err_msg}}<br><br></div>{{/if}}
本設定的目的是要將 /sfs3/data/templates_c 移至系統的暫存目錄，以加快 smarty 的寫檔速度，使用與否請自行�秣u。
</td>
</tr>
</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
