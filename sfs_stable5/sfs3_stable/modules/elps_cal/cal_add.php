<?php
include"config.php";
sfs_check();
##############資料處理############
if($_POST[wek]!='' && $_POST[unit]!='' && $_POST[event]!='' && $_POST[syear]!=''){
$day=date("Y-m-d H:i:s");
foreach( $_POST[wek] as $key=>$val) {
$SQL="INSERT INTO cal_elps(syear,week,unit,event,user,day) VALUES ('$_POST[syear]', '$key', '$_POST[unit]', '$_POST[event]', '$_SESSION[session_tea_sn]','$day')";
//echo $SQL."<br>";
	$rs=$CONN->Execute($SQL) or die($SQL);
	}
	header("Location:cal_edit.php?syear=$_POST[syear]");
}





##########################
head("校務行事曆");
if($_GET[syear]=='') { print_menu($school_menu_p);}
else {$link2="syear=$_GET[syear]"; print_menu($school_menu_p,$link2);}

myheader();
$now_Syear=sprintf("%03d",curr_year()).curr_seme();//目前學期

if ($_GET[syear]){
$SQL="select * from cal_elps_set where syear='$_GET[syear]' ";
$arr=get_data($SQL);
$barr=$arr[0];
}

$cal_name=substr($barr[syear],0,3)."學年度第".substr($barr[syear],3,1)."學期 校務行事曆 新增行事";

echo cal_sel("xxx",$_GET[syear])."<B style='color:red'> << </B>請先選擇";
?>
<TABLE border=0 width=85% style='font-size:12pt;' cellspacing='1' cellpadding=3 bgcolor='lightGray'>
<TR bgcolor=white><td colspan=2 align=center>
<h3><?php echo $cal_name; ?></h3>
</td></tr><form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name=f1>
<TR bgcolor=#9EBCDD>
<td width=25%>名稱</td>
<td width=75%>設定值</td></tr>
<tr bgcolor=white><td>學年度</td>
<td><input type="text" name="syear" size=6 value="<?php echo $barr[syear]; ?>" class=ipmei>
</td></tr>
<tr bgcolor=white><td>選擇行事週別<BR><B style='color:red'>(可複選)</B></td>
<td>
<?php
for ($i=1;$i<=$barr[weeks];$i++){
echo "<input type=checkbox name='wek[$i]' >第 $i 週";
if ($i%5==0)echo "<br>\n";
}
?>
</td></tr>
<tr bgcolor=white><td>單位分類別</td>
<td>
<?php
	//$unit=split("@@@",$barr[unit]);//單位陣列
	$unit=explode("@@@",$barr[unit]);//單位陣列
	$unit_nu=count($unit);//取單位數
echo set_select2("unit",$unit,'');
?>
</td></tr>

<tr bgcolor=white><td>行事記錄<BR><B style='color:red'>僅填一個工作項目</B></td>
<td>
<textarea name="event" rows="5" cols="40" class=ipmei></textarea>
</td></tr>
<tr bgcolor=white><td colspan=2>
<input type="submit" name="sum" value="填好送出"><BR><FONT COLOR='#FF0000'>填寫時以『多週單工』為原則。</FONT><BR>
(一次選多週,每次僅填一個工作項目)<BR>
請先選擇那幾週要做這件工作，再填寫工作內容。<BR><BR>

例如：訓導方面第1週可能有1.檢查手帕 2.廁所衛生檢查 3.路隊訓練三個工作項目<BR>
則分三次填寫。第一次填『檢查手帕』且不要填編號(123..)，以此類推。<BR>
填寫時<I><U>將相同工作項目的週次</U></I>也一併選好。<BR><BR>
以此方法行之，很快就將該學期的行事曆完成了。

</td></tr>
</form>
</table>
<?php



foot();

?>
