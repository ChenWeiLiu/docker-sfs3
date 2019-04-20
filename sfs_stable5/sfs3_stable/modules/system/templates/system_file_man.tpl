{{* $Id: system_file_man.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table bgcolor="#DFDFDF" cellspacing="1" cellpadding="4" class="small">
<form name="myform" method="post" action="{{$smarty.server.SCRIPT_NAME}}">
<tr>
<td bgcolor="#FFFFFF">
目前的目錄&gt; <span style="color:blue;">data/{{foreach from=$smarty.post.path item=d}}{{$d}}/{{/foreach}}</span><br><br>
{{foreach from=$rowdata item=d}}
<img src="images/{{$d.kind}}.png"> {{if $d.kind=="dir"}}<a href="#" OnClick="document.getElementById('path').value='{{$d.name}}';document.myform.submit();">{{$d.name}}</a>{{else}}<a href="{{$url}}/{{$d.name}}" target="new">{{$d.name}}</a> <a href="#" OnClick="document.getElementById('del').value='{{$d.name}}';document.myform.submit();"><img src="images/del.png" border="0"></a>{{/if}}<br>
{{/foreach}}
<br>
<table>
<tr bgcolor="#FBFBC4">
<td><img src="{{$SFS_PATH_HTML}}images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td>
</tr>
<tr><td style="line-height:150%;">
<ol>
<li class="small">本功能用於檢視data目錄中的檔案(不含系統檔案)。</li>
<li class="small">若對檔案的功能不了解請勿任意刪檔。</li>
<li class="small">若檔案無法刪除，表示apache對檔案的權限不足，請檢查檔案權限設定是否正確。</li>
<li class="small">若檔案無法瀏覽，表示httpd.conf內upfiles的alias設定不正確。</li>
</ol>
</td></tr>
</table>
</td>
</tr>
{{assign var=i value=0}}
{{foreach from=$smarty.post.path item=d}}
<input type="hidden" name="path[{{$i}}]" value="{{$d}}">
{{assign var=i value=$i+1}}
{{/foreach}}
<input type="hidden" name="path[{{$i}}]" id="path">
<input type="hidden" name="del" id="del">
</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
