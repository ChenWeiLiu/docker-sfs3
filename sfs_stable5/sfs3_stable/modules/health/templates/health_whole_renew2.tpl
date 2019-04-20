{{* $Id: health_whole_renew2.tpl 5908 2010-03-16 23:47:21Z hami $ *}}
<style>
#openSigntDialog {float:right; cursor:pointer;}
#dialog {display:none;}
</style>
<script>
$(document).ready(function(){

	$("#openSigntDialog").live('click',function(){
		var studentName = $("select[name='student_sn'] option[selected]").text();
		$("#dialog").attr('title',studentName);

		var studentSn = '{{$smarty.post.student_sn}}';
		var yearSeme = '{{$smarty.post.year_seme}}';
		// 取得視力檢查處置對話方塊
		$.get('input_ajax.php',
				{kind:'sight_form',
				 yearSeme: yearSeme,
				 studentSn: studentSn
				},
				function(data){
					$("#dialog").html(data);
					$("#dialog").dialog({
						autoOpen: false,
						height: 240,
						width:600,
						modal: true
					});
					$("#dialog").dialog('open');
		});
	});
});
</script>
<form action="{{$smarty.server.SCRIPT_NAME}}" method="post" target="_blank">
{{assign var=sn value=$smarty.post.student_sn}}
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=d value=$health_data->health_data.$sn.$year_seme}}

{{* 身高體重 *}}
<table style="background-color:#9ebcdd;" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="2" style="color:white;"><input type="image" src="images/edit.gif" OnClick="this.form.act.value='wh_st';">身高體重</td>
</tr>
<tr style="background-color:#f4feff;">
<td>身高</td><td>{{$d.height}}</td>
</tr>
<tr style="background-color:white;">
<td>體重</td><td>{{$d.weight}}</td>
</tr>
<tr style="background-color:#f4feff;">
{{assign var=Bid value=$d.Bid}}
<td>評值</td><td>{{$Bid_arr.$Bid}}</td>
</tr>
<tr style="background-color:white;">
<td>實歲</td><td>{{$d.years}}</td>
</tr>
</table>

{{* 視力 *}}
<table style="background-color:#9ebcdd;" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="3" style="color:white;"><input type="image" src="images/edit.gif" OnClick="this.form.act.value='sight_st';">視力

</td>
</tr>
<tr style="background-color:#f4feff;">
<td>邊</td>
<td>右</td>
<td>左</td>
</tr>
<tr style="background-color:white;">
<td>裸視</td>
<td><font color="{{if $d.r.sight_o>'0.8'}}blue{{else}}red{{/if}}">{{$d.r.sight_o}}</font></td>
<td><font color="{{if $d.l.sight_o>'0.8'}}blue{{else}}red{{/if}}">{{$d.l.sight_o}}</font></td>
</tr>
<tr style="background-color:#f4feff;">
<td>矯正</td>
<td><font color="{{if $d.r.sight_r>'0.8'}}blue{{else}}red{{/if}}">{{$d.r.sight_r}}</font></td>
<td><font color="{{if $d.l.sight_r>'0.8'}}blue{{else}}red{{/if}}">{{$d.l.sight_r}}</font></td>
</tr>
<tr style="background-color:white;">
<td>近視</td>
<td><input type="checkbox" disabled="true" {{if $d.r.My}}checked{{/if}}></td>
<td><input type="checkbox" disabled="true" {{if $d.l.My}}checked{{/if}}></td>
</tr>
<tr style="background-color:#f4feff;">
<td>遠視</td>
<td><input type="checkbox" disabled="true" {{if $d.r.Hy}}checked{{/if}}></td>
<td><input type="checkbox" disabled="true" {{if $d.l.Hy}}checked{{/if}}></td>
</tr>
<tr style="background-color:white;">
<td>散光</td>
<td><input type="checkbox" disabled="true" {{if $d.r.Ast}}checked{{/if}}></td>
<td><input type="checkbox" disabled="true" {{if $d.l.Ast}}checked{{/if}}></td>
</tr>
<tr style="background-color:#f4feff;">
<td>弱視</td>
<td><input type="checkbox" disabled="true" {{if $d.r.Amb}}checked{{/if}}></td>
<td><input type="checkbox" disabled="true" {{if $d.l.Amb}}checked{{/if}}></td>
</tr>
<tr style="background-color:white;">
<td>其他</td>
<td><input type="checkbox" disabled="true" {{if $d.r.other}}checked{{/if}}></td>
<td><input type="checkbox" disabled="true" {{if $d.l.other}}checked{{/if}}></td>
</tr>
<tr style="background-color:#f4feff;">
<td>處置</td><td colspan="2">
{{if $d.l.manage_id}}左眼:{{$sight_kind[$d.l.manage_id]}}{{/if}}
{{if $d.l.diag}}:{{$d.l.diag}}{{/if}}
 <br/>
 {{if $d.r.manage_id}}右眼:{{$sight_kind[$d.r.manage_id]}}{{/if}}
{{if $d.r.diag}}:{{$d.r.diag}}{{/if}}
 </td>
</tr>
</table>
{{* 口腔檢查 *}}
<table cellspacing="1" cellpadding="4" width="100%" class="small" style="background-color:#9ebcdd;">
<tr>
<td colspan="2" style="color:white;"><input type="image" src="images/edit.gif" OnClick="this.form.act.value='oral_st';"><input type="image" src="images/edit.gif" OnClick="this.form.act.value='tee_st';">口腔檢查</td>
</tr>
<tr style="background-color:#f4feff;">
<td>口腔檢查</td><td style="text-align:center;"><input type="checkbox" disabled="true" {{if $d.chkOra}}checked{{/if}}></td>
</tr>
<tr style="background-color:white;">
<td>齲齒</td><td style="text-align:center;">{{if $d.C1}}異常{{else}}無異狀{{/if}}</td>
</tr>
<tr style="background-color:#f4feff;">
<td>缺牙</td><td style="text-align:center;">{{if $d.C2}}異常{{else}}無異狀{{/if}}</td>
</tr>
<tr style="background-color:white;">
<td>口腔衛生不良</td><td style="text-align:center;{{if $d.Ora1}}color:red;{{/if}}">{{if $d.checks.Ora1}}異常{{else}}無異狀{{/if}}</td>
</tr>
<tr style="background-color:#f4feff;">
<td>齒列咬合不正</td><td style="text-align:center;{{if $d.Ora4}}color:red;{{/if}}">{{if $d.checks.Ora4}}異常{{else}}無異狀{{/if}}</td>
</tr>
<tr style="background-color:white;">
<td>牙齦炎</td><td style="text-align:center;{{if $d.Ora5}}color:red;{{/if}}">{{if $d.checks.Ora5}}異常{{else}}無異狀{{/if}}</td>
</tr>
<tr style="background-color:#f4feff;">
<td>口腔黏膜異常</td><td style="text-align:center;{{if $d.Ora6}}color:red;{{/if}}">{{if $d.checks.Ora6}}異常{{else}}無異狀{{/if}}</td>
</tr>
<tr style="background-color:white;">
<td>其他</td><td>　</td>
</tr>
<tr style="background-color:#f4feff;">
<td>其他陳述</td><td>　</td>
</tr>
<tr style="background-color:white;">
<td>口檢表</td><td>
{{assign var=i value=0}}
{{foreach from=$d item=dd key=k}}
{{if ($k|@substr:0:1)=="T"}}{{if $i % 3==0 && $i!=0}}<br>{{/if}}{{$k|@substr:1:2}}{{$teesb.$dd}}{{assign var=i value=$i+1}}{{/if}}
{{/foreach}}
</td></tr>
</table>
<input type="hidden" name="sub_menu_id" value="{{$smarty.post.sub_menu_id}}">
<input type="hidden" name="year_seme" value="{{$smarty.post.year_seme}}">
<input type="hidden" name="class_name" value="{{$smarty.post.class_name}}">
<input type="hidden" name="student_sn" value="{{$smarty.post.student_sn}}">
<input type="hidden" name="act" value="">
</form>
<div id="dialog" title="視力檢查處置情形">

</div>
