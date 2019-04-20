{{* $Id: health_oral_st.tpl 5708 2009-10-23 15:33:08Z brucelyc $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<script src="js/DropDownControl.js" language="javascript"></script>
<link href="js/DropDownControl.css"rel="stylesheet" type="text/css"/>

<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<tr><td bgcolor="white">
<table border="0"><tr><td valign="top">
{{*選單*}}
<table class="tableBg" cellspacing="1" cellpadding="1">
<tr><td align="center" class="leftmenu">
{{$stud_menu}}
</td>
</tr>
</table>
</td><td valign="top">

{{if $smarty.post.student_sn}}
{{assign var=sn value=$smarty.post.student_sn}}
{{include file="health_stud_now.tpl"}}

</td><td valign="top">
{{* 口腔檢查 *}}
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="13" style="color:white;text-align:center;">口腔檢查</td>
</tr>
<tr style="background-color:#f4feff;text-align:center;">
<td>年級</td>
<td>學期</td>
<td>實施</td>
<td>口腔<br>衛生<br>不良</td>
<td>牙結石</td>
<td>牙週炎</td>
<td>咬合<br>不正</td>
<td>牙齦炎</td>
<td>口腔<br>黏膜<br>異常</td>
<td>其他</td>
<td>其他<br>陳述</td>
<td>口檢表</td>
<td>功能<br>選項</td>
</tr>
{{foreach from=$health_data->health_data.$sn item=d key=ys}}
<tr style="background-color:white;text-align:center;">
{{assign var=year value=$ys|@substr:0:-1}}
<td>{{$year|@intval}}</td>
<td>{{$ys|@substr:-1:1}}</td>
<td style="text-align:center;"><input type="checkbox" {{if $d.O0}}checked{{/if}}></td>
<td style="color:{{if $d.O1}}red{{else}}black{{/if}};">{{if $d.O1}}異常{{else}}正常{{/if}}</td>
<td style="color:{{if $d.O2}}red{{else}}black{{/if}};">{{if $d.O2}}異常{{else}}正常{{/if}}</td>
<td style="color:{{if $d.O3}}red{{else}}black{{/if}};">{{if $d.O3}}異常{{else}}正常{{/if}}</td>
<td style="color:{{if $d.O4}}red{{else}}black{{/if}};">{{if $d.O4}}異常{{else}}正常{{/if}}</td>
<td style="color:{{if $d.O5}}red{{else}}black{{/if}};">{{if $d.O5}}異常{{else}}正常{{/if}}</td>
<td style="color:{{if $d.O6}}red{{else}}black{{/if}};">{{if $d.O6}}異常{{else}}正常{{/if}}</td>
<td> </td>
<td> </td>
<td style="text-align:left;">
{{assign var=i value=0}}
{{foreach from=$d item=dd key=k}}
{{if ($k|@substr:0:1)=="T"}}{{if $i % 3==0 && $i!=0}}<br>{{/if}}{{$k|@substr:1:2}}{{$teesb.$dd}}{{assign var=i value=$i+1}}{{/if}}
{{/foreach}}
</td>
<td><input type="image" src="images/edit.gif" alt="編輯這筆資料"></td>
</tr>
{{/foreach}}
</table>
<input type="button" OnClick="window.opener.renew(2);window.close();" value="關閉本視窗">

{{*說明*}}
<table class="small">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>口檢表標記一律輸入大寫，乳齒恆齒以象限判斷。</li>
	</ol>
</td></tr>
</table>

</td></tr>
<input type="hidden" name="sub_menu_id" value="{{$smarty.post.sub_menu_id}}">
<input type="hidden" name="year_seme" value="{{$smarty.post.year_seme}}">
<input type="hidden" name="class_name" value="{{$smarty.post.class_name}}">
<input type="hidden" name="student_sn" value="{{$smarty.post.student_sn}}">
<input type="hidden" name="nav_prior" value="{{$smarty.post.nav_prior}}">
<input type="hidden" name="nav_next" value="{{$smarty.post.nav_next}}">
<input type="hidden" name="act" value="{{$smarty.post.act}}">
</form></table>
{{/if}}
</td></tr></table>
</td></tr></table>
</td>
</tr>
</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
