{{* $Id: score_certi_certi_htm.tpl 8291 2015-01-15 14:07:34Z brucelyc $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<script>
function tagall(name,status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name==name) {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
function check() {
  var i=0,j=0,k=0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name=='sel_stud[]') {
      if (document.myform.elements[i].checked==1) {
        j=1;
      }
    }
    if (document.myform.elements[i].name=='sel_seme[]') {
      if (document.myform.elements[i].checked==1) {
        k=1;
      }
    }
    i++;
  }
  if (j==0) {
  	alert('未選學生');
  	return false;
  }
  if (k==0) {
  	alert('未選學期');
  	return false;
  }
  return true;
}
</script>
<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<tr><td bgcolor='#FFFFFF'>
<form name="myform" method="post" action="{{$smarty.server.PHP_SELF}}" OnSubmit="return check('sel_stud[]')">
<table width="100%">
<tr>
<td>{{$year_seme_menu}} {{$class_year_menu}} {{if $smarty.post.year_seme}}{{$class_name_menu}}{{/if}}
{{if $smarty.post.me}}
<font size=2 color='red'>　　◎成績顯示的精度：<select name='precision'><option value=0>整數</option><option value=1 selected>小數1位</option><option value=2>小數2位</option></select></font>
<table>
<tr valign="top"><td>
<fieldset>
<legend><font color="#000088">學生選單</font></legend>
<table border="1">
{{foreach from=$stud_study_cond item=cond key=i}}
{{if $i mod 5 == 0}}<tr class="title_sbody1">{{/if}}
<td align="left"><input type="checkbox" id="c_{{$stud_id[$i]}}" name="sel_stud[]" value="{{$student_sn[$i]}}">{{$stud_site.$i}}.{{$stud_name[$i]}}{{if $cond != 0 && $cond != 5}}<font color="#ff0000">({{$study_cond.$cond}})</font>{{/if}}</td>
{{if $i mod 5 == 4}}</tr>{{/if}}
{{/foreach}}
</table>
<input type="button" value="全選" onClick="javascript:tagall('sel_stud[]',1);"><input type="button" value="取消全選" onClick="javascript:tagall('sel_stud[]',0);">
</fieldset>
<span class="small"><input type="checkbox" name="include_nor">含日常成績 <input type="checkbox" name="include_avg" checked>含平均  <input type="checkbox" name="include_no">含證明字列  起始號：<input type="text" name="start_no" size=3 value=""></span> <br>
<input type="submit" name="form1" value="列印成績表(八科)"><input type="submit" name="form1" value="列印成績表(七領域)"><input type="submit" name="form1" value="列印英文成績表">
<input type="hidden" name="stud_study_year" value="{{$stud_study_year}}">
</td><td>
<fieldset>
<legend><font color="#000088">學期選單</font></legend>
<table border="1">
{{foreach from=$show_year item=year key=j}}
<tr class="title_sbody1">
<td><input type="checkbox" id="y_{{$semes.$j}}" name="sel_seme[]" value="{{$year}}_{{$show_seme.$j}}" checked>{{$year}}學年度第{{$show_seme.$j}}學期</td>
</tr>
{{/foreach}}
</table>
<input type="button" value="全選" onClick="javascript:tagall('sel_seme[]',1);"><input type="button" value="取消全選" onClick="javascript:tagall('sel_seme[]',0);">
</fieldset>
</td><td>
<fieldset>
<legend><font color="#000088">樣式選單</font></legend>
<table border="1">
<tr class="title_sbody1">
<td align="left"><input type="radio" name="sel_sty" value="1" checked>簡易型<br>　　<font color="#FF0000">(請蓋教務處戳章)</font></td>
</tr>
<tr class="title_sbody1">
<td align="left" nowrap><input type="radio" name="sel_sty" value="2">標準型<br>　　<font color="#FF0000">(請蓋組長、製表人章)</font></td>
</tr>
<tr class="title_sbody1">
<td align="left" nowrap><input type="radio" name="sel_sty" value="3">正式型<br>　　<font color="#FF0000">(請蓋校長、官防章)</font></td>
</tr>
</table>
</fieldset>
<fieldset>
<legend><font color="#000088">紙張選單</font></legend>
<table border="1" width="100%">
<tr class="title_sbody1">
<td align="left"><input type="radio" name="sel_paper" value="1">A4每張一人</td>
</tr>
<tr class="title_sbody1">
<td align="left"><input type="radio" name="sel_paper" value="2" checked>A4每張兩人</td>
</tr>
</table>
</fieldset>
</td></tr>
</table>
{{*說明*}}
<table class="small" width="100%">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>本成績計算時將以「學期初設定」之「計算學期各領域總平均加權模式」設定為參考，如果沒有設定或設定為「學習領域算數平均」，計算時將以各域領等比例計算；如果設定為「學分式加權平均」，則計算時將以課程設定時的加權做計算。</li>
	</ol>
</td></tr>
</table>
</td></tr>
{{/if}}
</table>
</form>
</td></tr>
</tr>
</table>
</td></tr>
</table>
{{include file="$SFS_TEMPLATE/footer.tpl"}}
