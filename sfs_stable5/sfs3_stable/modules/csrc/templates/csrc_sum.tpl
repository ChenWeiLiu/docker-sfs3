{{* $Id: csrc_sum.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<script type="text/javascript">
<!--
	function go(a,b) {
		document.myform.act.value=a;
		if (b) document.myform.sel_week.value=b;
		document.myform.submit();
	}
-->
</script>

<form name="myform" method="post" action="{{$smarty.server.SCRIPT_NAME}}">
<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<tr>
<td bgcolor="white">
<table style="width:100%;"><tr><td class="small">
{{$sub_menu}} 
{{if $smarty.post.sub_menu_id}}
{{$year_seme_menu}} {{$class_menu}} {{$work_menu}}{{if $mfile}} {{include file=$mfile}}{{/if}}<br><br>
{{/if}}
<input type="button" value="新增資料" OnClick="go('add');">
<span class="small">
週次&gt;
{{foreach from=$weeks_arr item=d key=i}}{{if $i>0}}{{if $weeks_arr.0==$i}}<span style="color:red;">{{$i}}</span>{{else}}<a href="#" OnClick="go('',{{$i}});">{{$i}}</a>{{/if}}{{if ($weeks_arr|@count)>($i+1)}},{{/if}}{{/if}}{{/foreach}}
</span>
<input type="hidden" name="sel_week" value="{{$weeks_arr.0}}">
<input type="hidden" name="act" value="">

<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr style="background-color:#E6E9F9;text-align:center;">
<td rowspan="2">主類別</td>
<td rowspan="2">次類別</td>
<td rowspan="2">時間</td>
<td rowspan="2">地點</td>
<td rowspan="2">年級</td>
<td rowspan="2">班級</td>
<td colspan="3">人數</td>
<td rowspan="2">紀錄者</td>
<td rowspan="2">備註</td>
<td rowspan="2">功能</td>
</tr>
<tr style="background-color:#E6E9F9;text-align:center;">
<td>死亡</td>
<td>傷病</td>
<td>其他</td>
</tr>
</table>
</td></tr></table>
</td>
</tr>
</table>
</form>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
