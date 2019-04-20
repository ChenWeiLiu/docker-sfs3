{{* $Id: class_import.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table cellspacing="1" cellpadding="3" class="main_body">
<tr bgcolor="#FFFFFF">
<form name="form0" enctype="multipart/form-data" action="{{$smarty.server.PHP_SELF}}" method="post">
<td class="title_sbody1" nowrap>編班資料檔：</td>
<td colspan="2"><input type="file" name="upload_file"><input type="submit" name="doup_key" value="上傳"></td>
</form>
</tr>
{{if $msg}}
<tr><td colspan="3" style="background-color:white;text-align:left;">
<br>
{{$msg}}
<br>
</td></tr>
{{/if}}
</table>

<table style="width:100%;">
<tr bgcolor="#FBFBC4">
<td><img src="/sfs3/images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td>
</tr>
<tr><td style="line-height:150%;">
<ol>
<li class="small">請選擇一個檔案上傳處理。</li>
<li class="small">本程式僅處理學生編班資料。</li>
<li class="small"><a href="newin.csv">範例檔</a>。</li>
</ol>
</td></tr>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
