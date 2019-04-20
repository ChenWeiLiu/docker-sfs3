<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=Big5" http-equiv="content-type">
  <title></title>
</head>

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
//檢查有沒有勾選要列印的項目
  var i =0;
  var chk=0;
  while (i < objform.elements.length)  {
    if (objform.elements[i].name.substr(0,5)=='stud_') {
       if (objform.elements[i].checked==1) chk++;
    }
    i++;
  }
  if (chk==0) {
  	alert("您未勾選任何要列印項目!!");
  	return false;
  }
//檢查有沒有勾選班級
  var i =0;
  var chk=0;
  while (i < objform.elements.length)  {
    if (objform.elements[i].name.substr(0,8)=='class_id') {
       if (objform.elements[i].checked==1) chk++;
    }
    i++;
  }
  if (chk==0) {
  	alert("您未勾選任何班級!!");
  	return false;
  }


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

<TABLE border=0 width=100% style='font-size:12pt;' cellspacing='1' cellpadding=3  bgcolor=#9EBCDD>
<TR bgcolor=#9EBCDD>
<FORM METHOD=POST ACTION="club_serv_p.php" name=f1 target=_blank>
<td width=100%  style="vertical-align: top;" colspan=2>
<!-- 第1格內容 -->
{{$sel_year}}{{$sel_grade}}
{{if $smarty.get.grade!='' && $smarty.get.year_seme!=''}}
</td>
<TR bgcolor=#9EBCDD>
<td width=100%  style="vertical-align: top;" colspan=2>
<font color="#800000"><b>請勾選列印班級：</b></font>
<INPUT TYPE=button  value='全選/反向' onclick="tagall();" style="font-size:9pt">
</tr>

{{assign var="i" value=1}}
<TR bgcolor=#9EBCDD>
	<td colspan=2>

	<TABLE  border=0 width=700 style='font-size:10pt;' cellspacing='1' cellpadding=1  bgcolor='lightGray'><TR bgcolor=white>
	{{foreach from=$sel_class item=data}}
		{{if $i!=1 && ($i%10)==1 }}  <TR bgcolor=white>{{/if}}
		{{$data.c_name}}
		{{if $i!=0 && ($i%10)==0 }} </TR>{{/if}}
		{{assign var="i" value=$i+1}}
	{{/foreach}}
	</TABLE>
</td>
</TR>
<TR bgcolor=#9EBCDD>
	<td colspan=2>
	<font color="#800000"><b>請勾選列印項目：</b></font><br>
 {{if $IS_JHORES==6}}
 	<table border="0">
 		<tr>
 			<td>。<font color="#0000FF"><INPUT TYPE='checkbox' name='stud_chk_data' value='checked' {{if $M_SETUP.stud_chk_data==1}}checked{{/if}}>日常生活表現(<INPUT TYPE='checkbox' name='stud_chk_data_detail' value='checked' {{if $M_SETUP.stud_chk_data_detail==1}}checked{{/if}}>含檢核表)</font></td>
 		</tr>
 		<tr>
 			<td>。<font color="#0000FF"><INPUT TYPE='checkbox' name='stud_absent' value='checked' {{if $M_SETUP.stud_absent}}checked{{/if}}>出缺席資料(<INPUT TYPE='checkbox' name='stud_absent_detail' value='checked'>含明細)</font></td>
 		</tr>
 		<tr>
 			<td>。<font color="#0000FF"><INPUT TYPE='checkbox' name='stud_leader' value='checked' {{if $M_SETUP.stud_leader}}checked{{/if}}>幹部資料</font></td>
 		</tr>
 		<tr>
 			<td>。<font color="#0000FF"><INPUT TYPE='checkbox' name='stud_reward' value='checked' {{if $M_SETUP.stud_reward}}checked{{/if}}>獎懲資料(<INPUT TYPE='checkbox' name='stud_reward_detail' value='checked'>含明細)</font></td>
 		</tr>
 		<tr>
 			<td>。<font color="#0000FF"><INPUT TYPE='checkbox' name='stud_club' value='checked' {{if $M_SETUP.stud_club}}checked{{/if}}>社團活動記錄(<INPUT TYPE='checkbox' name='stud_club_score' value='checked'>含成績)</font></td>
 		</tr>
 		<tr>
 			<td>。<font color="#0000FF"><INPUT TYPE='checkbox' name='stud_service' value='checked' {{if $M_SETUP.stud_service}}checked{{/if}}>服務學習記錄</font></td>
 		</tr>
 		<tr>
 			<td>。<font color="#0000FF"><INPUT TYPE='checkbox' name='stud_race' value='checked' {{if $M_SETUP.stud_race}}checked{{/if}}>競賽資料</font></td>
 		</tr>
 	</table>
 {{/if}}


</td>
</tr>
<tr bgcolor=#9EBCDD>
	<td>
		<br>
		<table border="0">
			<tr>
				<td><font color="#800000"><b>通知單標題：</b></font><br><input type="text" name="default_title" value="{{$M_SETUP.print_title}}" size="80"></td>
			</tr>
			<tr>
				<td><br><font color="#800000"><b>通知單加註文字：</b></font>(請依需要自行修改, 可接受HTML標籤進行格式設定)</td>
			</tr>
 			<tr>
  			<td><textarea name="default_txt" rows=8 cols=80>{{$default_txt}}</textarea></td>
 			</tr>
		</table>
	</td>
</tr>
<tr bgcolor=#9EBCDD>
	<td>
	<INPUT TYPE=button  value='選好送出' onclick=" bb1('選好了？？有點久，請耐心等待！','OK');" class=bur2>
	<INPUT TYPE='hidden' NAME='act'  value=''>
	</td>
</tr>
{{/if}}
</table>
</FORM>

