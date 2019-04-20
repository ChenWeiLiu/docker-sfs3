{{* $Id: system_chk_login_img.tpl 7927 2014-03-13 06:18:04Z brucelyc $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table bgcolor="#DFDFDF" cellspacing="1" cellpadding="1">
<tr>
<td bgcolor="#FFFFFF">
<table border="0">
<tr><td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" class="small">
<form name="v" method="post" action="{{$smarty.server.PHP_SELF}}">
<tr style="text-align:center;color:blue;background-color:#bedcfd;">
<td colspan="2">登入圖片檢查</td>
</tr>
{{assign var=c value=$smarty.post.chk-1}}
{{assign var=d value=$smarty.post.dot-1}}
{{assign var=s value=$smarty.post.slope-1}}
{{assign var=r value=$smarty.post.color-1}}
{{assign var=t value=$smarty.post.type}}
{{if ($t=="")}}{{assign var=t value="font"}}{{/if}}
<tr style="background-color:white;text-align:center;">
<td>現在狀態</td><td style="color:{{if $c}}red{{else}}green{{/if}};"><div OnClick="document.v.chk.value='{{$c*-1}}';document.v.submit();" style="cursor:pointer;">{{if $smarty.post.chk}}開{{else}}關{{/if}}</div></td>
</tr>
<tr style="text-align:center;color:blue;background-color:#bedcfd;">
<td colspan="2"><input type="radio" name="img_type"{{if $t=="font"}} checked{{/if}} OnClick="document.v.type.value='font';document.v.submit();">登入圖片展示</td>
</tr>
<tr style="background-color:white;text-align:center;">
<td colspan="2"><img src="{{$SFS_PATH_HTML}}pass_img.php" style="vertical-align:middle;"></td>
</tr>
<tr style="background-color:white;text-align:center;">
<td>字型</td><td><select name="font_no" OnChange="this.form.submit();">
{{html_options options=$font_arr selected=$smarty.post.font_no}}
</select></td>
</tr>
<tr style="background-color:white;text-align:center;">
<td>背景雜點</td><td style="color:{{if $d}}red{{else}}green{{/if}};"><div OnClick="document.v.dot.value='{{$d*-1}}';document.v.submit();" style="cursor:pointer;">{{if $smarty.post.dot}}開{{else}}關{{/if}}</div></td>
</tr>
<tr style="background-color:white;text-align:center;">
<td>字體傾斜</td><td style="color:{{if $s}}red{{else}}green{{/if}};"><div OnClick="document.v.slope.value='{{$s*-1}}';document.v.submit();" style="cursor:pointer;">{{if $smarty.post.slope}}開{{else}}關{{/if}}</div></td>
</tr>
<tr style="background-color:white;text-align:center;">
<td>字體顏色</td><td style="color:{{if $r}}red{{else}}green{{/if}};"><div OnClick="document.v.color.value='{{$r*-1}}';document.v.submit();" style="cursor:pointer;">{{if $smarty.post.color}}開{{else}}關{{/if}}</div></td>
</tr>
<tr style="text-align:center;color:blue;background-color:#bedcfd;">
<td colspan="2"><input type="radio" name="img_type"{{if $t=="kitten"}} checked{{/if}} OnClick="document.v.type.value='kitten';document.v.submit();">小貓圖片展示</td>
</tr>
<tr style="background-color:white;text-align:center;">
<td colspan="2"><img src="{{$SFS_PATH_HTML}}kitten_img.php" style="vertical-align:middle;"></td>
</tr>
<input type="hidden" name="chk" value="{{$smarty.post.chk}}">
<input type="hidden" name="dot" value="{{$smarty.post.dot}}">
<input type="hidden" name="slope" value="{{$smarty.post.slope}}">
<input type="hidden" name="color" value="{{$smarty.post.color}}">
<input type="hidden" name="type" value="{{$smarty.post.type}}">
</form>
</table>
</td>
</tr>
<table>
<tr bgcolor="#FBFBC4">
<td><img src="{{$SFS_PATH_HTML}}images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td>
</tr>
<tr><td style="line-height:150%;">
<ol>
<li class="small">為防止自動登入程式攻擊，可將此功能開啟，本系統會在登入畫面增加一隨機字串圖片。</li>
<li class="small">若看不到「登入圖片展示」或「小貓圖片展示」欄的圖片，請勿將本功能開啟。</li>
<li class="small">若本功能開啟後在登入畫面無法顯示圖片的話，請將「/sfs3/data/system/chk_login_img」刪除即可。</li>
</ol>
</td></tr>
</table>
</td></tr></table>
</td>
</tr>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}