<?php
// $Id: prolin.php 6865 2012-08-27 17:18:16Z hsiao $

//載入設定檔
require("config.php") ;

// 認證檢查
sfs_check();

($IS_JHORES==0) ? $UP_YEAR=6:$UP_YEAR=9;//判斷國中小
$y[] = $UP_YEAR ;
$class_year_p =  class_base("",$y); //班級  
//學校名稱陣列
$temp_grade = get_grade_school_table();
head("名冊列印") ;
print_menu($menu_p);

//學年度
$curr_year =  curr_year() ;
//javascript
js();

/////----------------升學學校名冊------////
/* 舊有含直式格式
$main =  "<table width=100%  bgcolor='#CCCCCC' >
  		<tr><td align='center'>	 
	<H2>$curr_year 學年度畢業生名冊列印</H2>
	<form action='' method='post' name='p1'> 
	<table width=50%  cellspacing='0' cellpadding='2' bordercolorlight='#333354' bordercolordark='#FFFFFF' border='1' bgcolor='#99CCCC' > 
<tr><td colspan=2 align=center>全部升學學校<br>
<input type='button' name='bb' value='直式依學校匯出' onclick=\"bb1('直式報表','依學校匯出')\">
<input type='button' name='bb' value='橫式依學校匯出' onclick=\"bb2('橫式報表','依學校匯出')\"><br>
<input type='button' name='bb' value='直式依班級匯出' onclick=\"bb1('直式報表','依班級匯出')\">
<input type='button' name='bb' value='橫式依班級匯出' onclick=\"bb2('橫式報表','依班級匯出')\">

<input type='hidden' name='key' value=''>

</td></tr>
	<tr ><td align=right>升學學校</td><td><select name='curr_grade_school'>
	<option value= 'all' selected >全部學校</option> \n";
*/
//新去除直式格式
$main =  "<table width=100%  bgcolor='#CCCCCC' >
  		<tr><td align='center'>	 
	<H2>$curr_year 學年度畢業生名冊列印(給升入國中)</H2>
	<form action='' method='post' name='p1'> 
	<table width=50%  cellspacing='0' cellpadding='2' bordercolorlight='#333354' bordercolordark='#FFFFFF' border='1' bgcolor='#99CCCC' > 
<tr><td colspan=2 align=center>全部升學學校<br>
<input type='button' name='bb' value='橫式依學校匯出' onclick=\"bb2('橫式報表','依學校匯出')\">
<input type='button' name='bb' value='橫式依班級匯出' onclick=\"bb2('橫式報表','依班級匯出')\">

<input type='hidden' name='key' value=''>

</td></tr>
	<tr ><td align=right>升學學校</td><td><select name='curr_grade_school'>
	<option value= 'all' selected >全部學校</option> \n";
//	$temp_grade =  get_grade_school_table(); 
	
	foreach( $temp_grade as $tkey => $tvalue) {
		if ($tvalue == $curr_grade_school)
			$main .=   sprintf ("<option value='%s' selected>%s</option>\n",$tvalue,$tvalue);
		else
			$main .=  sprintf ("<option value='%s'>%s</option>\n",$tvalue,$tvalue);
	}

	$main .= "</select></td></tr> \n
	     <tr ><td align=right>選擇班級</td><td><select name='curr_class_name'>
	     <option value='$UP_YEAR'>全學年</option> ";
	     $class_temp='';
        foreach ( $class_year_p as $tkey => $tvalue) {
			 $class_temp .=   sprintf ("<option value='%02d'>%s</option>\n",$tkey,$tvalue);
	}             	 
          
$main .=  $class_temp ;
/*舊有直式
	$main .= " </select></td></tr>
<tr><td colspan=2 align=center>
<input type='button' name='bb' value='直式匯出 sxw' onclick=\"bb1('直式報表','匯出 sxw')\">
<input type='button' name='bb' value='橫式匯出 sxw' onclick=\"bb2('橫式報表','匯出 sxw')\">
</td></tr>
<tr><td colspan=2 align=center>
<input type='button' name='bb' value='畢業學生名冊封面' onclick=\"bb6('列印畢業學生名冊封面','grad_cover')\">
<input type='button' name='bb' value='修業學生名冊封面' onclick=\"bb6('列印修業學生名冊封面','disgrad_cover')\"><br>
<input type='button' name='bb' value='畢業學生名冊' onclick=\"bb7('列印畢業學生名冊','grade')\">
<input type='button' name='bb' value='修業學生名冊' onclick=\"bb7('列印修業學生名冊','disgrade')\"><br>
<input type='button' name='bb' value='畢業學生名冊封底內頁' onclick=\"bb6('列印畢業學生名冊封底內頁','grad_bottom')\">
<input type='button' name='bb' value='修業學生名冊封底內頁' onclick=\"bb6('列印修業學生名冊封底內頁','disgrad_bottom')\">
</td></tr>
	         </table></form>
	         </td></tr></table>" ;
*/
//新去除直式
	$main .= " </select></td></tr>
<tr><td colspan=2 align=center>
<input type='button' name='bb' value='橫式匯出 sxw' onclick=\"bb2('橫式報表','匯出 sxw')\">
</td></tr>
<tr><td colspan=2 align=center>
<input type='button' name='bb' value='畢業學生名冊封面' onclick=\"bb6('列印畢業學生名冊封面','grad_cover')\">
<input type='button' name='bb' value='修業學生名冊封面' onclick=\"bb6('列印修業學生名冊封面','disgrad_cover')\"><br>
<input type='button' name='bb' value='畢業學生名冊' onclick=\"bb7('列印畢業學生名冊','grade')\">
<input type='button' name='bb' value='修業學生名冊' onclick=\"bb7('列印修業學生名冊','disgrade')\"><br>
<input type='button' name='bb' value='畢業學生名冊封底內頁' onclick=\"bb6('列印畢業學生名冊封底內頁','grad_bottom')\">
<input type='button' name='bb' value='修業學生名冊封底內頁' onclick=\"bb6('列印修業學生名冊封底內頁','disgrad_bottom')\">
</td></tr>
	         </table></form>
	         </td></tr></table>" ;
echo  $main ;

/////----------------畢業生一覽表列印------////
$main =  "<table width=100% bgcolor='#CCCCCC' >
<tr><td align='center'>	
<center><H2>畢業生一覽表列印(給教育局)</H2><form method='post' action='' name='p2'>

<table  width=50% cellspacing='0'  cellpadding='2' bordercolorlight='#333354' bordercolordark='#FFFFFF' border='1' bgcolor='#99CCCC' >
<tr><td align=right>選擇班級</td><td><select name='curr_class_name'>
<option value='$UP_YEAR'>全學年</option>\n";
	$main .=  $class_temp ;
	/*舊含直式
	$main .= " </select></td></tr>
<tr><td colspan=2 align=center><input type='hidden' name='key' value=''>
<input type='button' name='aa' value='直式匯出 SXW' onclick=\"bb3('直式報表','匯出 SXW')\">
<input type='button' name='aa' value='橫式匯出 SXW' onclick=\"bb4('橫式報表','匯出 SXW')\" >
<input type='button' name='aa' value='橫式匯出 SXW(含身份證字號)' onclick=\"bb42('橫式報表(含身份證字號)','匯出 SXW')\" >
</td></tr></table></form></center>
</td></tr></table>" ;		
*/
//新去除直式
$main .= " </select></td></tr>
<tr><td colspan=2 align=center><input type='hidden' name='key' value=''>
<input type='button' name='aa' value='橫式匯出 SXW' onclick=\"bb4('橫式報表','匯出 SXW')\" >
<input type='button' name='aa' value='橫式匯出 SXW(含身份證字號)' onclick=\"bb42('橫式報表(含身份證字號)','匯出 SXW')\" >
</td></tr></table></form></center>
</td></tr></table>" ;	    
echo $main ;


/////----------------畢業生資料匯出------////
$main = "<table width=100% bgcolor='#CCCCCC' >
  		<tr><td align='center'>	 
  		<H2><br>畢業生資料匯出(畢業證書印製)</H2>
  		<form action='' method='post' name='p3'> 
	        <table width=50%  cellspacing='0' cellpadding='2' bordercolorlight='#333354' bordercolordark='#FFFFFF' border='1' bgcolor='#99CCCC'>
	           <tr><td align=right>選擇班級</td>
	             <td><select name='curr_class_name'>
	             <option value='$UP_YEAR'>全學年</option>
	             $class_temp 
	             </select></td>
	           </tr>
	           <tr><td colspan=2 align=center>
	           <input type='hidden' name='key' value=''>
	          <input type='button' name='aa' value='匯出 CSV' onclick=\"bb5('匯出 CSV?','匯出 CSV')\" >
	           </td></tr>
	         </table>
	         </form>
	        </td></tr></table>" ;       
echo $main ;


foot() ;

function js(){
	global $CONN;

//統計同步化人數
$query = "SELECT COUNT(*) AS cc FROM grad_stud WHERE stud_grad_year='".curr_year()."'";
$res = $CONN->Execute($query) or die($query);
$cc = $res->fields['cc'];

//統計畢修業人數
$g=array();
$query = "SELECT grad_kind, COUNT(*) AS g FROM grad_stud WHERE stud_grad_year='".curr_year()."' group by grad_kind";
$res = $CONN->Execute($query) or die($query);
while(!$res->EOF) {
	$g[$res->fields['grad_kind']]=$res->fields['g'];
	$res->MoveNext();
}
$gg = intval($g[1]);
$ng = intval($g[2]);

echo "
<script>
<!--

function bb1(a,b) {";
if ($cc==0) echo "alert('本學年畢業生資料尚未進行同步化, 請先進行同步化動作。');}";
else echo "
var objform=document.p1;
if (window.confirm(a)){
objform.action='grad_print2.php';
objform.key.value=b;
objform.submit();}
}";
echo "
function bb2(a,b) {";
if ($cc==0) echo "alert('本學年畢業生資料尚未進行同步化, 請先進行同步化動作。');}";
else echo "
var objform=document.p1;
if (window.confirm(a)){
objform.action='grad_print_v.php';
objform.key.value=b;
objform.submit();}
}";
echo "
function bb3(a,b) {";
if ($cc==0) echo "alert('本學年畢業生資料尚未進行同步化, 請先進行同步化動作。');}";
else echo "
var objform=document.p2;
if (window.confirm(a)){
objform.action='grad_list_print.php';
objform.key.value=b;
objform.submit();}
}";
echo "
function bb4(a,b) {";
if ($cc==0) echo "alert('本學年畢業生資料尚未進行同步化, 請先進行同步化動作。');}";
else echo "
var objform=document.p2;
if (window.confirm(a)){
objform.action='grad_list_print_v.php';
objform.key.value=b;
objform.submit();}
}";
echo "
function bb42(a,b) {";
if ($cc==0) echo "alert('本學年畢業生資料尚未進行同步化, 請先進行同步化動作。');}";
else echo "
var objform=document.p2;
if (window.confirm(a)){
objform.action='grad_list_print_v2.php';
objform.key.value=b;
objform.submit();}
}";
echo "
function bb5(a,b) {";
if ($cc==0) echo "alert('本學年畢業生資料尚未進行同步化, 請先進行同步化動作。');}";
else echo "
var objform=document.p3;
if (window.confirm(a)){
objform.action='grade_data.php';
objform.key.value=b;
objform.submit();}
}";
echo "
function bb6(a,b) {
var objform=document.p1;
if (window.confirm(a)){
objform.action='cover.php';
objform.key.value=b;
objform.submit();}
}
function bb7(a,b) {";
if ($cc==0) echo "alert('本學年畢業生資料尚未進行同步化, 請先進行同步化動作。');}";
else echo "
var gg=$gg;
var ng=$ng;
var objform=document.p1;
if (b=='grade' && gg==0) {
	alert('本學年畢業生資料中標記為畢業的人數為0, 請先完成「畢業字號」內的畢修業註記。');
} else if (b=='disgrade' && ng==0) {
	alert('本學年畢業生資料中標記為修業的人數為0, 不必進行列印。');
} else {
	if (window.confirm(a)){
	objform.action='grad_list.php';
	objform.key.value=b;
	objform.submit();}
}
}";
echo "

//-->
</script>
";
}
?>
