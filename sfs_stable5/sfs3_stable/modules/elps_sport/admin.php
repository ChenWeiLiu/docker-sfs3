<?php
include "config.php";
 include "$SFS_PATH/pnadodb/tohtml.inc.php";
sfs_check();

//if ($_POST){
//	echo "<PRE>";print_r($_POST);print_r($_GET);echo "</PRE>";
//	die();
//	}
#####################   項目修改  ###########################
if (substr($_POST[act],0,6)=='update'){
	$key=split("_",$_POST[act]);//update_XXX
	if ($key[1]=='' ) backe("操作錯誤");
	$Col_name=$key[1];
	if($Col_name=='add' ) {
		if ($_POST[gp]=='' ) backe("未填妥，按下後重填！");
		if ($_POST[kkey]=='' ) backe("未填妥，按下後重填！");
		if ($_POST[na]=='' ) backe("未填妥，按下後重填！");
		$sql="insert into  sport_var(gp,kkey,na)values('$_POST[gp]','$_POST[kkey]','$_POST[na]') ";
		$rs = $CONN->Execute($sql) or die($sql);
		header("Location:$_SERVER[PHP_SELF]");
		}
	foreach( $_POST[$Col_name] as $id=>$val) {
		$sql="update sport_var set ".$Col_name."='$val' where id ='$id' ";
//		echo$sql."<BR>";
		$rs = $CONN->Execute($sql) or die($sql);
		}
		header("Location:$_SERVER[PHP_SELF]");
//	gonow($_SERVER[PHP_SELF]."?mid=$mid");exit;
	}

//phpinfo();
//$_SESSION
#####################   權限檢查  ###########################
$ad_array=who_is_root();
if (!is_array($ad_array[$_SESSION[session_tea_sn]])) backe("您非系統管理者！無操作權限！");
//if (check_man($_SESSION[session_tea_sn],$bb ,2)!='YES'   )
//echo "<PRE>";print_r($ad_array);
head("競賽管理");
include_once "menu.php";
include_once "chk.js";
include_once "chi_text.js";

if($_GET[mid]=='') { print_menu($school_menu_p3);}
else {$link2="mid=$_GET[mid]"; print_menu($school_menu_p3,$link2);}

//rs2html($rs,'border=0 cellpadding=1',array('編號','分類','索引值','資料值'));
?>
<table border=0 width='100%' >
<tr bgcolor=white><td width='60%'  valign=top>

<table border=0 width='100%' style='font-size:11pt;'  cellspacing=1 cellpadding=0 bgcolor=silver>
<tr bgcolor=white>
<TD width=100% colspan=4>
□編修選項<?php
echo "<img src='images/dia_bluve.gif'><A HREF='$_SERVER[PHP_SELF]?tb=add'>新增選項</A>
<img src='images/dia_bluve.gif'><A HREF='?tb=gp'>分類編修</A>
<img src='images/dia_bluve.gif'><A HREF='?tb=kkey'>索引值調整</A>
<img src='images/dia_bluve.gif'><A HREF='?tb=na'>資料值調整</A>
</TD></tr><FORM METHOD=POST ACTION='$_SERVER[PHP_SELF]' NAME='f1'>";?>
<tr bgcolor=white align=center>
<td width=10%>編號</td><td width=30%>分類/變數名稱</td>
<td width=20%>索引值</td><td width=40%>資料值</td>
</tr>
<?php
if($_GET[tb]!='') {
	echo "<tr bgcolor=white align=center><td colspan=4 align=right>
<INPUT TYPE='reset' value='重新選擇' class=bur>&nbsp;
<INPUT TYPE=button  value='寫好送出資料' onclick=\" bb('寫入資料？OK？','update_$_GET[tb]');\" class=bur>
<INPUT TYPE=button  value='取消返回' onclick=\"location.href='$_SERVER[PHP_SELF]';\" class=bur>
<INPUT TYPE='hidden' name='act' value=''>
</td></tr>";

}

if($_GET[tb]=='add') {
echo "<tr bgcolor=white align=center>
<td width=10%><img src='images/arrow.gif'></td><td width=30%>
<INPUT TYPE='text' NAME='gp' value='' size=20 class=ip2  onfocus=\"this.select();\" onkeydown=\"moveit2(this,event);\" ></td>
<td width=20%>
<INPUT TYPE='text' NAME='kkey' value='' size=6 class=ip2  onfocus=\"this.select();\" onkeydown=\"moveit2(this,event);\" ></td><td width=40%>
<INPUT TYPE='text' NAME='na' value='' size=20 class=ip2  onfocus=\"this.select();\" onkeydown=\"moveit2(this,event);\" ></td>
</tr>";

}
	$SQL="select * from sport_var order by gp ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
for($i=0; $i<count($arr); $i++) {
$id_1=$arr[$i][id];
($_GET[tb]=='gp') ? $gp_1="<INPUT TYPE='text' NAME='gp[".$arr[$i][id]."]' value='".$arr[$i][gp]."' size=20 class=ip2  onfocus=\"this.select();\" onkeydown=\"moveit2(this,event);\" >":$gp_1=$arr[$i][gp];
($_GET[tb]=='kkey') ? $kkey_1="<INPUT TYPE='text' NAME='kkey[".$arr[$i][id]."]' value='".$arr[$i][kkey]."' size=6 class=ip2  onfocus=\"this.select();\" onkeydown=\"moveit2(this,event);\" >":$kkey_1=$arr[$i][kkey];
($_GET[tb]=='na') ? $na_1="<INPUT TYPE='text' NAME='na[".$arr[$i][id]."]' value='".$arr[$i][na]."' size=20 class=ip2  onfocus=\"this.select();\" onkeydown=\"moveit2(this,event);\" >":$na_1=$arr[$i][na];




echo "
<tr bgcolor=white align=center>
<td>$id_1</td><td>$gp_1</td>
<td>$kkey_1</td><td align=left>$na_1</td>
</tr>";
}


?>

</FORM>
</table></td>
<td width='40%' valign=top>
<PRE style='color:#800000;font-size:10pt;'>註：
1.同一個分類下索引值是唯一的，不能重複。

2.資料值的改變適宜安裝模組後馬上修改。
  但當您己使用本模組且實際運作後，改變資料值
  將使您以前比賽的記錄抓到新的名稱。
  所以當您實際運作後就不宜再改，但新增則不受限。

3.萬一這種情形發生了，您還是可以再改回以前的設定
  則以前比賽的記錄將會再抓到原有的名稱值。
  </PRE>
</td>
</tr>
</table>
<BR><BR><BR>
<?php
foot();
?>
