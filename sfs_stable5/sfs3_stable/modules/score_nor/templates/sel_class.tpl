<!-- //$Id: sel_class.tpl 5310 2009-01-10 07:57:56Z hami $ -->
<SCRIPT LANGUAGE="JavaScript">
<!--
function chk1(formName, elementName,chk_type) {
	for(var i = 0; i < document.forms[formName].elements[elementName].length; i++) {
		if (chk_type == 0) 
			document.forms[formName].elements[elementName][i].checked = false;
		if (chk_type == 1) 
			document.forms[formName].elements[elementName][i].checked = true;
		if (chk_type == 2) {
			var $chk_now = document.forms[formName].elements[elementName][i].checked;
			if ($chk_now) 
				document.forms[formName].elements[elementName][i].checked = false;
			else
				document.forms[formName].elements[elementName][i].checked =  true;
		}
	}
}
//-->
</SCRIPT>
<form name='score' action="{{$PHP_SELF}}" method="post" >
	<select name=year_seme onChange='submit()'>
		{{html_options options=$year_seme_ary selected=$year_seme}}
	</select>
	<select name=grade onChange='submit()'>
		{{html_options options=$grade_ary selected=$grade}}
	</select>

</form>
<form name=prn action='prn_class_seme_score_nor.php' method='post' target=_blank>
	<input type=hidden name=prn_type>
	<input type=hidden name=year_seme value="{{$year_seme}}">
	<input type=hidden name=test_sort value="{{$test_sort}}">
	<input type=hidden name=grade value="{{$grade}}">
	<table bgcolor="ghostwhite" cellPadding='0' border=1 cellSpacing='0' width='90%' align=left style='empty-cells:show;border-collapse:collapse;text-align:center;' >
	<tr>
		<td align=left colspan=10>
			請勾選班級 
			<input type=button value='全選'  onClick="chk1('prn', 'sel_class[]',1);">
			<input type=button value='取消' onClick="chk1('prn', 'sel_class[]',0);">
			<input type=button value='反向選擇' onClick="chk1('prn', 'sel_class[]',2);">
			<input type=submit value='列印' >
		</td>
	</tr>
	<!---- 95/01/14 修正 -->
	<!---- 有些學校的班級名稱 c_name 並不是數字，如：甲乙丙或忠孝仁愛  -->
	<!---- 因此需修正判斷的方式 -->
	{{counter assign='clano' start=0 skip=1 print=false}}
	{{foreach from=$class_ary key=class_id item=c_name}}
		{{counter print=false}}
	 	{{if $clano % 10 == 1}}<tr>{{/if}}
	 	<td>
	 	{{if $c_name !=''}}
	 		<label>
	 			<input type="checkbox" name="sel_class[]" value="{{$class_id}}" >{{$c_name}}
	 		</label>
	 	{{/if}}
	 	</td>
	 	{{if $clano % 10 == 0}}</tr>{{/if}}
	{{/foreach}}
 	{{if $clano % 10 != 0}}</tr>{{/if}}
 	<!---- 95/01/14 修正結束 -->
	</table>
</form>
<p>
<pre>
<br>
<br>
<FONT>
<DIV style="color:blue" onclick="alert('作者群：\n陽明 江添河 和群 姚榮輝\n二林 紀明村 草湖 曾彥鈞\n北斗 李欣欣 大城 林畯城\n大村 鄭培華');"> 
◎By 彰化縣學務系統開發小組</DIV></FONT>
<br>
列印時，紙張請設成 B4 橫印
</pre>
