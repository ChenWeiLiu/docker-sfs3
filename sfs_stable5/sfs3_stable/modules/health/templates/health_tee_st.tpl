{{* $Id: health_tee_st.tpl 5736 2009-11-03 07:25:40Z brucelyc $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<script src="js/DropDownControl.js" language="javascript"></script>
<link href="js/DropDownControl.css"rel="stylesheet" type="text/css"/>
<script>
function cal() {
	var c=new Array();

	for (var i=0, len=document.myform.elements.length; i< len; i++) {
		a=document.myform.elements[i].id.substr(0,1);
		if (a=='T') {
			b=document.myform.elements[i].value;
			if (b!="") {
				if (typeof(c[b]) == "undefined")
					c[b]=1;
				else
					c[b]++;
			}
		} 
	}
	for (i=1; i<=6; i++) {
		a="C"+i;
		if (typeof(c[i]) == "undefined") c[i]=0;
		if (document.getElementById(a).innerHTML!=c[i]) document.getElementById(a).style.color="red";
		document.getElementById(a).innerHTML=c[i];
	}
}
</script>

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
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
{{include file="health_stud_now.tpl"}}

</td><td valign="top">
{{* 口腔檢查 *}}
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="16" style="color:white;text-align:center;">牙齒位置圖</td>
</tr>
<tr style="background-color:#f4feff;text-align:center;">
{{foreach from=$tee_arr.1 item=id}}
<td>{{$id}}</td>
{{/foreach}}
</tr>
<tr style="background-color:white;text-align:center;">
{{foreach from=$tee_arr.1 item=id}}
{{assign var=d value="T$id"}}
<td>
<input type="text" id="T{{$id}}" name="update[new][{{$sn}}][{{$year_seme}}][T{{$id}}]" value="{{$dd.$d}}" OnDblClick="showDropDownItem(this,'{{$tee_chk_str}}',1,0,1);" style="background-color:white;width:20px;" OnChange="cal();">
<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][T{{$id}}]" value="{{$dd.$d}}">
</td>
{{/foreach}}
</tr>
<tr style="background-color:#f4feff;text-align:center;">
<td colspan="3" rowspan="4" style="background-color:#9ebcdd;color:white;"><p>上</p><p style="text-align:right;">右　</p><p>下</p></td>
{{foreach from=$tee_arr.2 item=id}}
<td>{{$id}}</td>
{{/foreach}}
<td colspan="3" rowspan="4" style="background-color:#9ebcdd;color:white;"><p>上</p><p style="text-align:left;">　左</p><p>下</p></td>
</tr>
<tr style="background-color:white;text-align:center;">
{{foreach from=$tee_arr.2 item=id}}
{{assign var=d value="T$id"}}
<td>
<input type="text" id="T{{$id}}" name="update[new][{{$sn}}][{{$year_seme}}][T{{$id}}]" value="{{$dd.$d}}" OnDblClick="showDropDownItem(this,'{{$tee_chk_str}}',1,0,1);" style="background-color:white;width:20px;" OnChange="cal();">
<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][T{{$id}}]" value="{{$dd.$d}}">
</td>
{{/foreach}}
</tr>
<tr style="background-color:#f4feff;text-align:center;">
{{foreach from=$tee_arr.3 item=id}}
<td>{{$id}}</td>
{{/foreach}}
</tr>
<tr style="background-color:white;text-align:center;">
{{foreach from=$tee_arr.3 item=id}}
{{assign var=d value="T$id"}}
<td>
<input type="text" id="T{{$id}}" name="update[new][{{$sn}}][{{$year_seme}}][T{{$id}}]" value="{{$dd.$d}}" OnDblClick="showDropDownItem(this,'{{$tee_chk_str}}',1,0,1);" style="background-color:white;width:20px;" OnChange="cal();">
<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][T{{$id}}]" value="{{$dd.$d}}">
</td>
{{/foreach}}
</tr>
<tr style="background-color:#f4feff;text-align:center;">
{{foreach from=$tee_arr.4 item=id}}
<td>{{$id}}</td>
{{/foreach}}
</tr>
<tr style="background-color:white;text-align:center;">
{{foreach from=$tee_arr.4 item=id}}
{{assign var=d value="T$id"}}
<td>
<input type="text" id="T{{$id}}" name="update[new][{{$sn}}][{{$year_seme}}][T{{$id}}]" value="{{$dd.$d}}" OnDblClick="showDropDownItem(this,'{{$tee_chk_str}}',1,0,1);" style="background-color:white;width:20px;" OnChange="cal();">
<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][T{{$id}}]" value="{{$dd.$d}}">
</td>
{{/foreach}}
</tr>
<tr>
<td colspan="16" style="color:white;text-align:center;">牙齒狀態統計</td>
</tr>
<tr style="background-color:#f4feff;text-align:center;">
{{assign var=cid value="C1"}}
<td colspan="4">齲齒</td><td colspan="4" style="background-color:white;"><div id="{{$cid}}">{{$dd.$cid|intval}}</div></td>
{{assign var=cid value="C2"}}
<td colspan="4">缺牙</td><td colspan="4" style="background-color:white;"><div id="{{$cid}}">{{$dd.$cid|intval}}</div></td>
</tr>
<tr style="background-color:#f4feff;text-align:center;">
{{assign var=cid value="C3"}}
<td colspan="4">已矯治</td><td colspan="4" style="background-color:white;"><div id="{{$cid}}">{{$dd.$cid|intval}}</div></td>
{{assign var=cid value="C4"}}
<td colspan="4">待拔牙</td><td colspan="4" style="background-color:white;"><div id="{{$cid}}">{{$dd.$cid|intval}}</div></td>
</tr>
<tr style="background-color:#f4feff;text-align:center;">
{{assign var=cid value="C5"}}
<td colspan="4">阻生牙</td><td colspan="4" style="background-color:white;"><div id="{{$cid}}">{{$dd.$cid|intval}}</div></td>
{{assign var=cid value="C6"}}
<td colspan="4">贅生牙</td><td colspan="4" style="background-color:white;"><div id="{{$cid}}">{{$dd.$cid|intval}}</div></td>
</tr>
</table>
<input type="submit" name="sure" value="儲存">
<input type="submit" value="回復原值">
<input type="button" OnClick="window.opener.renew(2);window.close();" value="關閉本視窗">

{{*說明*}}
<table class="small">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>為便於輸入，代號改為數字。</li>
	<li>無異狀可不輸入值。</li>
	<li>狀態統計若有紅色數字表示輸入後尚未儲存，請務必記得儲存。</li>
	</ol>
</td></tr>
</table>

</td><td valign="top">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr style="color:white;background-color:#aecced;">
<td>代碼表</td>
</tr>
<tr bgcolor="#f4feff">
<td>0.無異狀</td>
</tr>
<tr bgcolor="white">
<td>1.齲齒</td>
</tr>
<tr bgcolor="#f4feff">
<td>2.缺牙</td>
</tr>
<tr bgcolor="white">
<td>3.已矯治</td>
</tr>
<tr bgcolor="#f4feff">
<td>4.待拔牙</td>
</tr>
<tr bgcolor="white">
<td>5.阻生牙</td>
</tr>
<tr bgcolor="#f4feff">
<td>6.贅生牙</td>
</tr>
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
</td></tr>
</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
