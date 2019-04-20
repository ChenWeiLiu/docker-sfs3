{{* $Id: stud_grad_disgrad.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<script>
var rd=0;
function tagall(name,status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].id==name) {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
function check(name) {
  var i=0,j=0;

  if (rd!=1) return true;
  while (i < document.myform.elements.length) {
    if (document.myform.elements[i].id==name) {
      if (document.myform.elements[i].checked==1) {
        j=1;
      }
    }
    i++;
  }
  if (j==0) {
  	alert('未選學生');
  	return false;
  }
  return true;
}
</script>
<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<form name="myform" method="post" action="{{$smarty.server.PHP_SELF}}" OnSubmit="return check('sel[]')">
<tr><td bgcolor='#FFFFFF'>
<table width="100%">
<tr>
<td>{{$year_seme_menu}} {{$class_year_menu}} <select name="years" size="1" style="background-color:#FFFFFF;font-size:13px" onchange="this.form.submit()";><option value="5" {{if $smarty.post.years==5}}selected{{/if}}>五學期</option><option value="6" {{if $smarty.post.years==6}}selected{{/if}}>六學期</option></select>{{if $smarty.post.year_name}} <input type="submit" name="friendly_print" value="友善列印">{{/if}}{{if $smarty.post.year_name && $smarty.post.years==6}} <input type="submit" name="disgrade" value="記錄修業名單" OnClick="rd=1">{{/if}}<br>
<input type="checkbox" checked>學習領域平均成績在60分以上者未達<input type="text" name="fail_num" size="1" value="{{if $smarty.post.fail_num == ""}}3{{else}}{{$smarty.post.fail_num}}{{/if}}">項 <font color="red">(必選)</font><br>
{{if $smarty.post.years==6}}<input type="checkbox" name="chk_last"{{if $smarty.post.chk_last}}checked{{/if}} OnChange="this.form.submit();">且第六學期學習領域平均成績在60分以上者未達<input type="text" name="last_fail_num" size="1" value="{{if $smarty.post.last_fail_num == ""}}3{{else}}{{$smarty.post.last_fail_num}}{{/if}}">項{{/if}}</td>
</tr>
{{if $smarty.post.year_name}}
<tr><td>
<table border="0" cellspacing="1" cellpadding="4" width="100%" bgcolor="#cccccc" class="main_body">
<tr bgcolor="#E1ECFF" align="center">
<td><input type="checkbox" name="sel_all" onClick="javascript:tagall('sel[]',document.myform.sel_all.checked);"></td>
<td>班級</td>
<td>座號</td>
<td>學號</td>
<td>姓名</td>
<td>語文</td>
<td>數學</td>
<td>自然與生活科技</td>
<td>社會</td>
<td>健康與體育</td>
<td>藝術與人文</td>
<td>綜合</td>
</tr>
{{foreach from=$show_sn item=sc key=sn}}
<tr bgcolor="#ddddff" align="center">
<td {{if $smarty.post.chk_last}}rowspan="2"{{/if}}><input type="checkbox" id="sel[]" name="sel[{{$sn}}]" value="{{$sclass[$sn]}}" {{if $smarty.post.sel.$sn}}checked{{/if}}></td>
<td {{if $smarty.post.chk_last}}rowspan="2"{{/if}}>{{$sclass[$sn]}}</td>
<td {{if $smarty.post.chk_last}}rowspan="2"{{/if}}>{{$snum[$sn]}}</td>
<td {{if $smarty.post.chk_last}}rowspan="2"{{/if}}>{{$stud_id[$sn]}}</td>
<td {{if $smarty.post.chk_last}}rowspan="2"{{/if}}>{{$stud_name[$sn]}}</td>
{{foreach from=$show_ss item=ssn key=ss}}
<td>{{if $fin_score.$sn.$ss.avg.score < 60}}<font color="red">{{/if}}{{$fin_score.$sn.$ss.avg.score}}{{if $fin_score.$sn.$ss.avg.score < 60}}</font>{{/if}}</td>
{{/foreach}}
</tr>
{{if $smarty.post.chk_last}}
<tr bgcolor="#ddddff" align="center">
{{foreach from=$show_ss item=ssn key=ss}}
<td>{{if $fin_score.$sn.$ss.$si.score < 60}}<font color="red">{{/if}}{{$fin_score.$sn.$ss.$si.score}}{{if $fin_score.$sn.$ss.$si.score < 60}}</font>{{/if}}</td>
{{/foreach}}
</tr>
{{/if}}
{{/foreach}}
</table>
</td></tr>
{{/if}}
</tr>
</table>
</td></tr>
</form>
</table>
{{include file="$SFS_TEMPLATE/footer.tpl"}}