{{* $Id: check_score_error2.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<script type="text/javascript">
<!--
function chk() {
	document.getElementById('mform').action='{{$smarty.server.SCRIPT_NAME}}';
	document.menu_form.submit();
}
//-->
</script>

<form name="menu_form" id="mform" method="post" action="{{$smarty.server.SCRIPT_NAME}}">
<input type="hidden" name="step" value="{{$smarty.post.step}}">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td style="vertival-align:top;background-color:#CCCCCC;">
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF" width="100%" class="main_body">
<tr><td>學期：{{$year_seme_menu}}</td></tr>
<tr style="background-color:white;font-size:12pt;"><td>
{{if $rowdata}}
錯誤成績列表如下：<br><br>
{{foreach from=$rowdata item=d key=seme_class}}
{{foreach from=$d item=num key=ss_id}}
<input type="submit" name="del[{{$seme_class}}][{{$ss_id}}]" value="刪"> {{$c_arr.$seme_class}} ---&gt; {{$ss_all_arr.$ss_id}} ..... 共{{$num}}筆 <span style="color:blue;font-size:10pt;">[課程代碼:{{$ss_id}}]</span><br>
{{/foreach}}
{{/foreach}}
<br>
{{else}}
<br>本學期未檢查出錯誤成績。<br><br>
{{/if}}
</td></tr>
{{*說明*}}
<tr></td>
<table class="small" width="100%">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;background-color:white;">
	<ol>
	<li>本程式用來檢查期中成績表內的錯誤資料。</li>
	</ol>
</td></tr>
</table>
</td>
</tr>
</table>
</td></tr>
</table>
</form>
{{include file="$SFS_TEMPLATE/footer.tpl"}}
