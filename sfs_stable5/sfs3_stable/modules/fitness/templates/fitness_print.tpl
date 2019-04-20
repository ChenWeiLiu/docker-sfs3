{{* $Id: fitness_print.tpl 8850 2016-03-11 03:35:43Z chiming $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<script language="JavaScript">
function openwindow(sn){
	window.open ("fitpass.php?student_sn="+sn,"個人護照","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,copyhistory=no,width=600,height=420");
}

function check_years_click() {
  var i =0;
  if (document.myform.check_years_check.checked) {
   var k=true;
  } else {
   var k=false;
  }
  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name.substr(0,15)=="check_years_old") {
      document.myform.elements[i].checked=k;
    }
    i++;
  }

}
</script>

<table bgcolor="#DFDFDF" cellspacing="1" cellpadding="4">
<form action="{{$smarty.server.PHP_SELF}}" method="post" name="myform">
<input type="hidden" name="act" value="">
<input type="hidden" name="cal_age" value="">
<tr>
<td bgcolor="#FFFFFF" valign="top">
{{$seme_menu}} {{$class_menu}} <input type="submit" name="cal_per" value="換算百分等級"> <font size="3" color="blue">測驗日期：<input type="text" name="test_y" size="3" value="{{$smarty.post.test_y}}">年<input type="text" name="test_m" size="3" value="{{$smarty.post.test_m}}">月 <input type="button" value="計算年齡" title="注意！僅計算有勾選的學生!! (全班若尚未輸入無任何資料，將無法計算年齡！)" onclick="check_years()"> <input type="submit" name="print_html" value="列印"> | {{$all_students}} <input type="submit" name="export" value="匯出CSV檔"><input type="submit" name="export2" value="匯出套印成績證明CSV檔"></font><br>
<font color="gold">■</font>85％以上 <font color="silver">■</font>75％以上 <font color="bronze">■</font>50％以上 <font color="red">■</font>24％以下
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%">
<tr bgcolor="#c4d9ff">
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">生月</td>
<td align="center">身高<br>(cm)[％]</td>
<td align="center">體重<br>(kg)[％]</td>
<td align="center">BMI指數<br>(kg/m<sup>2</sup>)[％]</td>
<td align="center">坐姿前彎<br>(cm)[％]</td>
<td align="center">立定跳遠<br>(cm)[％]</td>
<td align="center">仰臥起坐<br>(次)[％]</td>
<td align="center">心肺適能<br>(秒)[％]</td>
<td align="center">年齡<input type="checkbox" name="check_years_check" onclick="check_years_click()"></td>
<td align="center">測驗年月</td>
<td align="center">獎章</td>
</tr>
{{foreach from=$rowdata item=d key=i}}
{{assign var=sn value=$d.student_sn}}
<tr bgcolor="white">
<td class="small">{{$d.seme_num}}</td>
<td class="small"><a OnClick="openwindow('{{$sn}}');" title="{{$d.stud_name}}的個人護照"><font color="{{if $d.stud_sex==1}}blue{{elseif $d.stud_sex==2}}red{{else}}black{{/if}}">{{$d.stud_name}}</font></a></td>
<td style="text-align:right;">{{$d.stud_birthday}}</td>
<td style="text-align:right;">{{$fd.$sn.tall}}<font color="{{if $fd.$sn.prec_t>=85}}gold{{elseif $fd.$sn.prec_t>=75}}silver{{elseif $fd.$sn.prec_t>=50}}bronze{{elseif $fd.$sn.prec_t<25}}red{{else}}black{{/if}}">[{{$fd.$sn.prec_t}}]</font></td>
<td style="text-align:right;">{{$fd.$sn.weigh}}<font color="{{if $fd.$sn.prec_w>=85}}gold{{elseif $fd.$sn.prec_w>=75}}silver{{elseif $fd.$sn.prec_w>=50}}bronze{{elseif $fd.$sn.prec_w<25}}red{{else}}black{{/if}}">[{{$fd.$sn.prec_w}}]</font></td>
<td style="text-align:right;">{{$fd.$sn.bmt}}<font color="{{if $fd.$sn.prec_b>=85}}gold{{elseif $fd.$sn.prec_b>=75}}silver{{elseif $fd.$sn.prec_b>=50}}bronze{{elseif $fd.$sn.prec_b<25}}red{{else}}black{{/if}}">[{{$fd.$sn.prec_b}}]</font></td>
<td style="text-align:right;">{{$fd.$sn.test1}}<font color="{{if $fd.$sn.prec1>=85}}gold{{elseif $fd.$sn.prec1>=75}}silver{{elseif $fd.$sn.prec1>=50}}bronze{{elseif $fd.$sn.prec1<25}}red{{else}}black{{/if}}">[{{$fd.$sn.prec1}}]</font></td>
<td style="text-align:right;">{{$fd.$sn.test3}}<font color="{{if $fd.$sn.prec3>=85}}gold{{elseif $fd.$sn.prec3>=75}}silver{{elseif $fd.$sn.prec3>=50}}bronze{{elseif $fd.$sn.prec3<25}}red{{else}}black{{/if}}">[{{$fd.$sn.prec3}}]</font></td>
<td style="text-align:right;">{{$fd.$sn.test2}}<font color="{{if $fd.$sn.prec2>=85}}gold{{elseif $fd.$sn.prec2>=75}}silver{{elseif $fd.$sn.prec2>=50}}bronze{{elseif $fd.$sn.prec2<25}}red{{else}}black{{/if}}">[{{$fd.$sn.prec2}}]</font></td>
<td style="text-align:right;">{{$fd.$sn.test4}}<font color="{{if $fd.$sn.prec4>=85}}gold{{elseif $fd.$sn.prec4>=75}}silver{{elseif $fd.$sn.prec4>=50}}bronze{{elseif $fd.$sn.prec4<25}}red{{else}}black{{/if}}">[{{$fd.$sn.prec4}}]</font></td>
<td style="text-align:center;">{{$fd.$sn.age}}<input type="checkbox" name="check_years_old[{{$sn}}]"></td>
<td style="text-align:center;">{{$fd.$sn.test_y}}-{{$fd.$sn.test_m}}</td>
<td style="text-align:center;">{{if $fd.$sn.reward}}{{$fd.$sn.reward}}{{else}}--{{/if}}</td>
</tr>
{{/foreach}}
{{foreach from=$avg item=d key=i}}
<tr style="text-align:right;background-color:white;">
<td class="small" colspan="3" bgcolor="#c4d9ff">{{$avg_title.$i}}平均</td><td>{{$d.a_tall|@round:1}}</td><td>{{$d.a_weigh|@round:1}}</td><td>{{$d.a_bmt|@round:1}}</td><td>{{$d.a_test1|@round:1}}</td><td>{{$d.a_test3|@round:1}}</td><td>{{$d.a_test2|@round:1}}</td><td>{{$d.a_test4|@round:1}}</td><td align="center">--</td><td align="center">-----</td><td align="center">--</td>
</tr>
{{/foreach}}
<tr style="text-align:right;background-color:white;">
<td class="small" colspan="3" bgcolor="#c4d9ff">50％以上人數</td>
{{foreach from=$cou item=d}}
<td>{{$d}}</td>
{{/foreach}}
<td align="center">--</td><td align="center">-----</td><td align="center">--</td>
</tr>
</table>
{{*說明*}}
<table class="small" width="100%">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>從2012.12.08起年齡計算改為滿七個月才進一歲(與教育部體適能網站計算方式相同)。</li>
	</ol>
</td></tr>
</table>
</td></tr></form></table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}

<script>
function check_years() {
 if (document.myform.test_m.value=='' || document.myform.test_y.value=='') { 
   alert('注意! 測驗日期輸入不完整! \n 年:請輸入民國年, 若西元2014年, 請輸入 103');
   return false; 
 } else {
   document.myform.cal_age.value="計算年齡";
   document.myform.submit();
 }

}
</Script>
