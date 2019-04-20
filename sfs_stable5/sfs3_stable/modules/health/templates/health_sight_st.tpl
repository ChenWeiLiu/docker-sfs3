{{* $Id: health_sight_st.tpl 5908 2010-03-16 23:47:21Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<script src="js/DropDownControl.js" language="javascript"></script>
<link href="js/DropDownControl.css"rel="stylesheet" type="text/css"/>
<script>
function check_value1(a) {
	var b;
	b=document.getElementById(a).value;
	if (b==-9) {
		if (a=='s1' || a=='s2')
			document.getElementById(a).value='**';
		else {
			alert('只有裸視能輸入「無法測量」。');
			document.getElementById(a).value='';
		}
	} else if (b<0) document.getElementById(a).value='<0.1';
	else if (b>=1) document.getElementById(a).value=b/10;

}
function check_value2(a,b,c) {
	var d;
	d=document.getElementById(b+c+a).checked;
	document.getElementById('M'+c+a).checked=false;
	document.getElementById('H'+c+a).checked=false;
	document.getElementById('A'+c+a).checked=false;
	document.getElementById(b+c+a).checked=d;}
</script>

<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<tr><td bgcolor="white">
<table border="0"><tr><td valign="top">
{{*選單*}}
<table class="tableBg" cellspacing="1" cellpadding="1">
<tr><td align="center" class="leftmenu" id="leftMenu">
{{$stud_menu}}
</td>
</tr>
</table>
</td><td valign="top">

{{if $smarty.post.student_sn}}
{{assign var=sn value=$smarty.post.student_sn}}
{{include file="health_stud_now.tpl"}}
</form>
</td><td valign="top">
<form id="sight_st_form" action="" method="post">
{{* 視力 *}}
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="14" style="color:white;text-align:center;">視力</td>
</tr>
<tr style="background-color:#f4feff;text-align:center;">
<td>學年度</td>
<td>學期</td>
<td>邊</td>
<td>裸視</td>
<td>矯正</td>
<td>近<br>視</td>
<td>遠<br>視</td>
<td>散<br>光</td>
<td>弱<br>視</td>
<td>其<br>他</td>
<td>其他陳述</td>
<td>處置<br>代號</td>
<td>功能<br>選項</td>
</tr>
{{foreach from=$health_data->health_data.$sn item=d key=ys}}
{{assign var=c value=$smarty.post.edit.$sn.$ys}}
{{foreach from=$d item=dd key=side}}
<tr style="background-color:white;text-align:center;">
<td>{{$ys|@substr:0:-1|@intval}}</td>
<td>{{$ys|@substr:-1:1}}</td>
<td>{{if $side=="r"}}右{{elseif $side=="l"}}左{{/if}}</td>
<td>{{if $c}}<input type="text" id="S{{$side}}o" name="update[new][{{$sn}}][{{$ys}}][{{$side}}][sight_o]" value="{{$dd.sight_o}}" size="2" OnChange="check_value1('S{{$side}}o')"><input type="hidden" name="update[old][{{$sn}}][{{$ys}}][{{$side}}][sight_o]" value="{{$dd.sight_o}}">{{else}}{{$dd.sight_o}}{{/if}}</td>
<td>{{if $c}}<input type="text" id="S{{$side}}r" name="update[new][{{$sn}}][{{$ys}}][{{$side}}][sight_r]" value="{{$dd.sight_r}}" size="2" OnChange="check_value1('S{{$side}}r')"><input type="hidden" name="update[old][{{$sn}}][{{$ys}}][{{$side}}][sight_r]" value="{{$dd.sight_r}}">{{else}}{{$dd.sight_r}}{{/if}}</td>
<td><input type="checkbox" {{if $c}}id="M{{$side}}{{$ys}}" name="update[new][{{$sn}}][{{$ys}}][{{$side}}][My]" OnClick="check_value2('{{$ys}}','M','{{$side}}');"{{else}}disabled="true"{{/if}} {{if $dd.My}}checked{{/if}}></td>
<td><input type="checkbox" {{if $c}}id="H{{$side}}{{$ys}}" name="update[new][{{$sn}}][{{$ys}}][{{$side}}][Hy]" OnClick="check_value2('{{$ys}}','H','{{$side}}');"{{else}}disabled="true"{{/if}} {{if $dd.Hy}}checked{{/if}}></td>
<td><input type="checkbox" {{if $c}}id="A{{$side}}{{$ys}}" name="update[new][{{$sn}}][{{$ys}}][{{$side}}][Ast]" OnClick="check_value2('{{$ys}}','A','{{$side}}');"{{else}}disabled="true"{{/if}} {{if $dd.Ast}}checked{{/if}}></td>
<td><input type="checkbox" {{if $c}}name="update[new][{{$sn}}][{{$ys}}][{{$side}}][Amb]"{{else}}disabled="true"{{/if}} {{if $dd.Amb}}checked{{/if}}></td>
<td>
<input type="checkbox" {{if $c}}id="c5" OnChange="check_value2('c5');"{{else}}disabled="true"{{/if}} {{if $dd.other}}checked{{/if}}>
{{if $c}}<input type="hidden" id="cid" name="update[{{$sn}}][{{$ys}}][s0id]" value="{{$d.s0id}}">{{/if}}
</td>
<td>
{{if $c}}
<input type="text" name="update[diag][{{$ys}}][{{$side}}]" size="8" value="{{$dd.diag}}" />
{{else}}
{{$dd.diag}}
{{/if}}
</td>
<td>
{{if $c}}
<input type="text" name="update[manage_id][{{$ys}}][{{$side}}]" OnDblClick="showDropDownItem(this,'{{$sight_kind_str}}',1,0,1);" style="background-color:#FFFFC0;width:25px;" value="{{$dd.manage_id}}">
{{else}}
{{$dd.manage_id}}
{{/if}}
</td>
<td style="text-align:left;">
{{if $smarty.post.edit.$sn.$ys}}
<input type="image" name="ok" src="images/ok.png" OnClick="this.form.ok.value=1">
<input type="image" src="images/no.png">
{{else}}
<input type="image" src="images/edit.png" name="edit[{{$sn}}][{{$ys}}]">
{{if $d.sight_0r!="" && $d.sight_0l!="" && $d.sight_r!="" && $d.sight_l!=""}}
<input type="image" src="images/delete.png" name="del[{{$sn}}][{{$ys}}][sight_0r]" OnClick="return confirm('確定要刪除 {{$health_data->stud_base.$sn.stud_name}} {{$ys|@substr:0:-1|@intval}}學年度第{{$ys|@substr:-1:1}}學期的「視力檢查」資料 ?');">
{{/if}}
{{/if}}
</td>
</tr>
{{/foreach}}
{{/foreach}}
</table>
<input type="button" OnClick="window.opener.renew(2);window.close();" value="關閉本視窗">
<input type="hidden" name="sub_menu_id" value="{{$smarty.post.sub_menu_id}}">
<input type="hidden" name="year_seme" value="{{$smarty.post.year_seme}}">
<input type="hidden" name="class_name" value="{{$smarty.post.class_name}}">
<input type="hidden" name="student_sn" value="{{$smarty.post.student_sn}}">
<input type="hidden" name="nav_prior" value="{{$smarty.post.nav_prior}}">
<input type="hidden" name="nav_next" value="{{$smarty.post.nav_next}}">
<input type="hidden" name="act" value="{{$smarty.post.act}}">
<input type="hidden" name="ok" value="">

{{*說明*}}
<table class="small">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>直接輸入整數，例如：0.8輸入「8」；視力值＜0.1輸入「-1」；裸視無法測量輸入「-9」。</li>
	</ol>
</td></tr>
</table>

</td></tr>

</table>

{{/if}}

</td></tr></table>

</td></tr></table>
</td>
</tr>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
