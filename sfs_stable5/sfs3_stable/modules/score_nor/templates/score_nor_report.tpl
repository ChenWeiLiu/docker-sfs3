{{* $Id: score_nor_report.tpl 7581 2013-09-27 07:10:48Z chiming $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<script>
<!--
function tagall() {
var i =0;
while (i < document.f1.elements.length) {
var obj=document.f1.elements[i];
var objx=document.f1.elements[i].name;//取得名稱
// objx.substr(0,4)取得部分字串
if (obj.type=='checkbox' && objx.substr(0,8)=='class_id') {
if (obj.checked==1 ) {obj.checked=0;}
else { obj.checked=1;}
}
i++;
}
}
function untagall() {
var i =0;
while (i < document.f1.elements.length) {
var obj=document.f1.elements[i];
var objx=document.f1.elements[i].name;//取得名稱
// objx.substr(0,4)取得部分字串
if (obj.type=='checkbox' && objx.substr(0,8)=='class_id') {
if (obj.checked==1 ) {obj.checked=0;}
else { obj.checked=0;}
}
i++;
}
}

function bb1(a,b) {
var objform=document.f1;
if (window.confirm(a)){
objform.act.value=b;
objform.submit();}
}
function bb2(a,b) {
var objform=document.f2;
if (window.confirm(a)){
objform.act.value=b;
objform.submit();}
}

//-->
</script>  

<body>
<FORM METHOD=POST ACTION="{{$smarty.server.PHP_SELF}}" name=f1 target=_blank>
<TABLE border=0 width=100% style='font-size:12pt;' cellspacing='1' cellpadding=3  bgcolor=#9EBCDD>

<TR bgcolor=#BCFEDD>
<td width=100%  style="vertical-align: top;" colspan=2><font color="#FF0000">■</font>請選擇簽章方式：<input type='radio' name='room_sign' value='0' onclick='document.fx.room_sign.value="0"' checked>三級人員核章 <input type='radio' name='room_sign' value='1' onclick='document.fx.room_sign.value="1"'>處室核章</td>
</TR>

<TR bgcolor=#9EBCDD>
<td width=100%  style="vertical-align: top;" colspan=2>
<!-- 第1格內容 -->
<font color="#FF0000">■</font>請選擇班級：{{$sel_year}}{{$sel_grade}}
{{if $smarty.get.grade!='' && $smarty.get.year_seme!=''}}
<INPUT TYPE=button  value='全選/反向' onclick="tagall();"  class=bur2>
<INPUT TYPE=button  value='取消' onclick="untagall();" class=bur2>
<INPUT TYPE=button  value='選好送出' onclick=" bb1('選好了？？有點久，請耐心等待！','OK');" class=bur2>
<INPUT TYPE='hidden' NAME='act'  value=''>
{{/if}}
</td>
</tr>
<TR bgcolor=white>
<td colspan=2>
{{if $smarty.get.grade!='' && $smarty.get.year_seme!=''}}
{{assign var="i" value=1}}
<TABLE  border=0 width=100% style='font-size:10pt;' cellspacing='1' cellpadding=1  bgcolor='lightGray'><TR bgcolor=white>
{{foreach from=$sel_class item=data}}

{{if $i!=1 && ($i%10)==1 }}  <TR bgcolor=white>{{/if}}

{{$data.c_name}}
{{if $i!=0 && ($i%10)==0 }} </TR>{{/if}}
{{assign var="i" value=$i+1}}
{{/foreach}}
</TABLE>
{{/if}}
</td></tr>
<INPUT TYPE='hidden' NAME='type'  value='{{$smarty.request.type}}'>
</FORM>
<FORM METHOD=POST ACTION="{{$smarty.server.PHP_SELF}}" name=fx target=_blank>
<input type="hidden" name="room_sign" value="0">
<tr><td>
<font color="#FF0000">■</font>或輸入學號(在學生)：<input type="text" name="list_stud_id" size="30" class=ipmei>
<INPUT type="reset" button  value='取消'  class=bur2>
<INPUT TYPE=button  value='選好送出' onclick="if( window.confirm('確定了？')) {this.form.act.value='OK';this.form.submit();}" class=bur2>
<INPUT type="hidden" name="act" value=''><br>
</td></tr>
<tr style="background-color:white;"><td></td></tr>
</FORM>
<FORM METHOD=POST ACTION="{{$smarty.server.PHP_SELF}}" name=fx2 target=_blank>
<input type="hidden" name="room_sign" value="0">
<tr><td>
<font color="#FF0000">■</font>或輸入學號(非在學生)：<input type="text" name="list_stud_id" size="30" class=ipmei>
<INPUT type="reset" button  value='取消'  class=bur2>
<INPUT TYPE=button  value='選好送出' onclick="if( window.confirm('確定了？')) {this.form.act.value='OK';this.form.submit();}" class=bur2>
<INPUT type="hidden" name="act" value=''>
<INPUT type="hidden" name="stud_cond" value='OUT'>
<br>
</td></tr>
</FORM>

<tr><td bgcolor="white">
僅接受兩種輸入格式：<div style="font-size:9pt;color:blue">1.僅輸入單一人學號。<br>2.或輸入一個範圍。例:90123-90130</div>
</td></tr>
</table>
{{include file="$SFS_TEMPLATE/footer.tpl"}}
