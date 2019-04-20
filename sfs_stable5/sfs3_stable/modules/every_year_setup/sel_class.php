<?php
// $Id: sel_class.php 5310 2009-01-10 07:57:56Z hami $

include "../../include/config.php";
sfs_check();

if ($_POST[do_key] == "確定選擇"){

setcookie("cookie_sel_teacher",$_POST[sel_teacher]);

echo "<html><body>
        <script LANGUAGE=\"JavaScript\">\n
        window.opener.history.go(0);\n
        window.close();
        </script>
        </body>
        </html>";
        exit;

}

if(!empty($_COOKIE[cookie_sel_teacher])) {
	$sel_teacher_arr =explode(",","none,".$_COOKIE[cookie_sel_teacher]);
}

$query = "select a.teacher_sn,a.name from teacher_base a ,teacher_post b where a.teacher_sn=b.teacher_sn and a.teach_condition=0";
$res = $CONN->Execute($query) or die($query);
while(!$res->EOF) {
	if(in_array($res->fields[0],$sel_teacher_arr))
		$in_arr[$res->fields[0]]= $res->fields[1];
	else
		$yet_arr[$res->fields[0]]= $res->fields[1];
		
	$res->MoveNext();
}

?>
<html>
<meta http-equiv="Content-Type" content="text/html; Charset=Big5">
<head>
<title>選擇教師</title>

</head>
<body>

<form name="myform" method="post" action="<?php echo $_SERVER[PHP_SELF] ?>">
<table cellspacing=3 cellpadding=2 bgcolor="#cccccc" width=98%>
<tr>
<td>
  <table width="100%" align="center" >
  <tr><td align=center>點按名單加入</td><td align=center>點按名單取消</td></tr>
    <tr>
      <td width="50%" bgcolor="#effeee" align=center>
	<select name="selObj1" size =15 onChange = "showItem(this.form.selObj1,this.form.selObj2)" >
	<option value="none">選擇加入教師</option>
	<?php
		while(list($id,$val) = each($yet_arr))
			echo "<option value=\"$id\">$val</option>\n";
	?>
	</select>
  
	  </td>
	  <td width="50%" bgcolor="#d3fdfc" align=center>
	<select name="selObj2" size=15 onChange = "showItem(this.form.selObj2,this.form.selObj1)"  >
	<option value="none">已選擇教師</option>
	<?php
		while(list($id,$val) = each($in_arr))
			echo "<option value=\"$id\">$val</option>\n";
	?>
	</select>
  
	  </td>
    </tr>
  </table>
</td>
</tr>
</table>
<input type="hidden" name="sel_teacher" >
<br>
<center>
<input type="submit" name="do_key" value="確定選擇"  onClick="checkok(this.form.selObj2,this.form.sel_teacher)">
&nbsp;&nbsp;<input type="button" name="b1" value="取消所有教師" onClick="cancelall(this.form.selObj2)">
</center>


</form>


</body>
</html>

<script language="JavaScript">
<!--
function showItem(selField,selField2) {
	var _F=document.form1;
	var optText = "";
	var counter = 1;
	var j=0;
	for (var i=0; i < selField.length; i++) {
		if (selField[i].selected ) {
			var opp1 = selField[i].text;
			var opp2 = selField[i].value;
			if (check_select_arr(opp1,selField2.options)== false && opp2 != 'none' ){
				 selField2.options[selField2.options.length] = new Option(opp1,opp2);
				 selField.options[i] = null;
			}
		}
	}
	 	
}

function check_select_arr(sel_val,sel_arr) {
	var _SS = sel_arr;
	var _TT = sel_val;
	for (var i=0;i<_SS.length;i++){
		if (_SS[i].text==_TT) {
			return true;

		}
	}
	return false;
}

function checkok(sel,text1) {
	text1.value='';
	for(var i=0;i<sel.options.length;i++) {
		if (sel.options[i].value !='none'){
			text1.value = text1.value+sel.options[i].value+',';
		}
	}
}

function cancelall(sel) {
	var tol = sel.options.length;
        i=1;
	while(i < sel.options.length){
		sel.options[i]=null;
	}
}


//-->
</script>



