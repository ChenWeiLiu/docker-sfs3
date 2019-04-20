<?php
//$Id: ad_item.php 8952 2016-08-29 02:23:59Z infodaes $
include "config.php";
sfs_check();

//if ($_POST){
//	echo "<PRE>";print_r($_POST);print_r($_GET);echo "</PRE>";
//	die();
//	}

//phpinfo();
//$_SESSION
#####################   權限檢查  ###########################
$ad_array=who_is_root();
if (!is_array($ad_array[$_SESSION[session_tea_sn]])){
if ($_POST[mid] || $_GET[mid] || $_POST[main_id] ) {
	$bb='';
	($_POST[mid]!='' ) ? $bb=$_POST[mid]:$bb;
	($_GET[mid]!='' ) ? $bb=$_GET[mid]:$bb;
	($_POST[main_id]!='' ) ? $bb=$_POST[main_id]:$bb;
if (check_man($_SESSION[session_tea_sn],$bb ,2)!='YES'   ) backe("您無權限操作");
}}
//echo "<PRE>";
//print_r($_POST);
#####################   項目處理選單  ###########################

if ($_POST[act]=='item_add') {
	if (strlen($_POST[enterclass])==0 || $_POST[playera]=='' || $_POST[passera]=='') backe("未填妥！再來一次！");
	if ($_POST[sportkind]=='未選擇'|| $_POST[place]=='') backe("未填妥！再來一次！");
	if ( $_POST[sunit]=='' ||$_POST[sord]=='') backe("無計分格式或排列方式！再來一次！");
	if ( $_POST[mid]=='' ||$_POST[item]==''  ) backe("無代號！再來一次！");
	$sporttime=$_POST[year1]."-".$_POST[year2]."-".$_POST[year3]." ".$_POST[year4].":00:00";
	$mid=$_POST[mid];
//	echo$sporttime."--<BR>";
	if($_POST[sportkind]==5){$kgp=$_POST[kgp];$kgm=$_POST[kgm];}
	else {$kgp=0;$kgm=0;}
	for ($i=0;$i<(strlen($_POST[enterclass])/2);$i++) {
	$worda=substr($_POST[enterclass],$i*2,2);//每次取出一個年級性別
	if ($_POST[createnexttime]=='yes'){
	$sql_insert = "insert into sport_item(mid,item,enterclass,sportkind,playera,passera,place,kind,skind ,sporttime,overtime,sunit,sord,imemo) values ('$_POST[mid]','$_POST[item]','$worda','$_POST[sportkind]','$_POST[playera]','$_POST[passera]','$_POST[place]','1','0' ,'$sporttime','$sporttime','$_POST[sunit]','$_POST[sord]','$_POST[imemo]') ";
	$rs=$CONN->Execute($sql_insert) or die($sql_insert);
	$linkid=$CONN->Insert_ID();
	$sql_insert = "insert into sport_item(mid,item,enterclass,sportkind,playera,passera,kgp,kgm,place,kind,skind ,sporttime,overtime,sunit,sord,imemo) values ('$_POST[mid]','$_POST[item]','$worda','$_POST[sportkind]','$_POST[playera]','$_POST[passera]','$kgp','$kgm','$_POST[place]','2', '$linkid' ,'$sporttime','$sporttime','$_POST[sunit]','$_POST[sord]','$_POST[imemo]') ";
	$rs=$CONN->Execute($sql_insert)or die($sql_insert);;
		}
	else{
		$SQL="insert into sport_item(mid,item,enterclass,sportkind,playera,passera,kgp,kgm,place,kind,skind ,sporttime,overtime,sunit,sord,imemo) values ('$_POST[mid]','$_POST[item]','$worda','$_POST[sportkind]','$_POST[playera]','$_POST[passera]','$kgp','$kgm','$_POST[place]','2','0' ,'$sporttime','$sporttime','$_POST[sunit]','$_POST[sord]','$_POST[imemo]') ";
	$rs=$CONN->Execute($SQL) or die($SQL);//直接建立決賽
			}
		}//結束for迴圈
		$url=$PHP_SELF."?mid=".$mid;header("Location:$url");
	}

#####################   項目修改  ###########################
if (substr($_POST[act],0,6)=='update'){
	if ( $_POST[mid]==''  ) backe("無代號！再來一次！");
	$key=split("_",$_POST[act]);
	if ($key[1]=='' ) backe("操作錯誤");
	foreach( $_POST[$key[1]]as $kk=>$val) {
		$sql="update sport_item set ".$key[1]."='$val' where id ='$kk' ";
		$rs = $CONN->Execute($sql) or die($sql);
		}
		$url=$PHP_SELF."?mid=".$_POST[mid];header("Location:$url");
	}
#####################   項目刪除  ###########################
if ($_GET[act]=="del" && $_GET[item]!='' && $_GET[mid]!='') {
	$rs = $CONN->Execute("select id from sport_res where itemid='$_GET[item]' ");
	if ( $rs->RecordCount()==0 ) {
		$CONN->Execute("DELETE FROM sport_item WHERE id='$_GET[item]' ");
		$url=$PHP_SELF."?mid=".$_GET[mid];header("Location:$url");
		}
	else {backe("已有資料，無法刪除！");
	}
	}
#####################   項目更新  ###########################
if ($_POST[act]=="item_update"){
	if ($_POST[mid]=='' ||$_POST[id]=='' ) backe("未填妥！再來一次！");
	if ( $_POST[sporttime]==''&&  $_POST[overtime]=='' ) backe("未選擇！再來一次！");
	if ( $_POST[enterclass]==''&&  $_POST[item]=='' ) backe("未選擇！再來一次！");
	if ( $_POST[sportorder]==''&&  $_POST[sportkind]=='' ) backe("未選擇！再來一次！");
	if ( $_POST[playera]==''&&  $_POST[passera]=='' ) backe("未選擇！再來一次！");
	if ( $_POST[place]=='') backe("未選擇！再來一次！");
	$sql="update sport_item set item='$_POST[item]',enterclass='$_POST[enterclass]', sportorder= '$_POST[sportorder]' , sportkind='$_POST[sportkind]' ,playera='$_POST[playera]', passera='$_POST[passera]' , place='$_POST[place]', sporttime='$_POST[sporttime]' , overtime='$_POST[overtime]' ,kind='$_POST[kind]' where id='$_POST[id]' ";
	$rs=$CONN->Execute($sql) or die($sql);
	$url=$PHP_SELF."?mid=$mid&act=modify&item=".$_POST[id];header("Location:$url");

	}

#####################   項目時間更新1  ###########################
if ($_POST[act]=="time_change1"){
	if ($_POST[mid]=='' ||$_POST[time_valve1]=='' ) backe("未填妥！再來一次！");
	if ( $_POST[Ta]==''&&  $_POST[Tb]=='' ) backe("未選擇！再來一次！");
	if ( $_POST[ttime]=='') backe("請選擇方式！再來一次！");
	if ( $_POST[ttimea]=='') backe("請選擇方式！再來一次！");
	$mid=$_POST[mid];
($_POST[ttime]=='d') ? $tt=' - ': $tt='';
	foreach( $_POST[Ta] as $kk=>$val) {
	$sql="update sport_item set sporttime=DATE_ADD(sporttime, INTERVAL $tt $_POST[time_valve1] $_POST[ttimea]) where id ='$kk' ";
	$rs = $CONN->Execute($sql) or die($sql);}
	if ($_POST[Tb]=='' ) {header("Location:".$PHP_SELF."?mid=$mid&act=chtime");}

	foreach( $_POST[Tb] as $kk=>$val) {
	$sql="update sport_item set  overtime=DATE_ADD( overtime, INTERVAL $tt $_POST[time_valve1] $_POST[ttimea])  where id ='$kk' ";
	$rs = $CONN->Execute($sql) or die($sql);
	}
	$url=$PHP_SELF."?mid=$mid&act=chtime";header("Location:$url");
	}
#####################   項目時間更新2  ###########################
if ($_POST[act]=="time_change2"){
	if ($_POST[mid]=='' ||$_POST[time_valve2]=='' ) backe("未填妥！再來一次！");
	if ( $_POST[Ta]==''&&  $_POST[Tb]=='' ) backe("未選擇！再來一次！");
	if ( $_POST[Ttimeb]=='') backe("請選擇方式！再來一次！");
	$mid=$_POST[mid];
	foreach( $_POST[Ta] as $kk=>$val) {
	$sql="select  sporttime  from  sport_item where  id ='$kk' ";
	$rs = $CONN->Execute($sql) or die($sql);
	$arr=$rs->GetArray();
	$new_t=CT_1($_POST[Ttimeb],$arr[0][sporttime],$_POST[time_valve2]);
	$sql="update sport_item set  sporttime='$new_t'  where id ='$kk' ";
	$rs = $CONN->Execute($sql) or die($sql);
//	echo $sql."<BR>";
	}
	foreach( $_POST[Tb] as $kk=>$val) {
	$sql="select   overtime from  sport_item where  id ='$kk' ";
	$rs = $CONN->Execute($sql) or die($sql);
	$arr=$rs->GetArray();
	$new_t=CT_1($_POST[Ttimeb],$arr[0][overtime],$_POST[time_valve2]);
	$sql="update sport_item set  overtime='$new_t'  where id ='$kk' ";
	$rs = $CONN->Execute($sql) or die($sql);
//	echo $sql."<BR>";
	}
	$url=$PHP_SELF."?mid=$mid&act=chtime";header("Location:$url");
	}

function CT_1($in,$t,$v) {
$ck=$in-1;
$ta=split("[- :]",$t);
	for ($i=0;$i<count($ta);$i++) {
	($ck===$i) ? $ta[$i]=$v : $ta[$i]=$ta[$i];
	}
	$time=$ta[0]."-".$ta[1]."-".$ta[2]." ".$ta[3].":".$ta[4].":".$ta[5];
	return $time;
}


#####################   選單與表頭  ###########################

head("競賽管理");
include_once "menu.php";
include_once "chk.js";
if($_GET[mid]=='') { print_menu($school_menu_p3);}
else {$link2="mid=$_GET[mid]"; print_menu($school_menu_p3,$link2);}

#####################   程式主體  ###########################
($_GET[mid]=='') ? mmid2():  mmid2($_GET[mid]);
if ($_GET[mid]!='' && $_GET[act]=='') {
	($_GET[tb]=='' ) ? list_item($_GET[mid]): list_item($_GET[mid],$_GET[tb]);
	}
if ($_GET[mid]!='' && $_GET[act]=='add_tb') {
	item_tb($_GET[mid]);	}
if ($_GET[mid]!='' && $_GET[act]=='modify' && $_GET[item]!='' ) {
	item_edit($_GET[mid],$_GET[item]);}
if ($_GET[mid]!='' && $_GET[act]=='chtime' ) {
	list_time($_GET[mid]);}

foot();

#####################   單筆編修表單  ###########################
function item_edit($mid,$item){
	global $CONN,$sportclass,$sportkind_name,$sportname,$itemkind;
	$SQL="select * from sport_item where id='$item' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();

include_once"chi_text.js";

?>

<?php
btt();
?>



<table border=0 width='80%' cellspacing=1 cellpadding=0 bgcolor=silver style='color:#800000;font-size:11pt;'>
<form method="post" action="<?=$_SERVER[PHP_SELF]?>" name="f1">
<tr bgcolor=#f2f2f2>
<td width=100% colspan=4 ><img src='images/21.gif'>&nbsp;<B>單項修改</B>
<?php btr("images/ch_back2.gif","重新填寫");bt('item_update','寫好送出','images/ch_save.gif')?>
<INPUT TYPE='button' value='返回上頁' onclick="location='<?=$_SERVER[PHP_SELF]?>?mid=<?=$mid?>';">
<INPUT TYPE='hidden' name='act' value=''>
</td>
</tr>
<tr bgcolor=white>
<td width=20% nowrap>編號</td>
<td width=30% nowrap><input type=hidden name=id value='<?=$arr[0][id]?>'><?=$arr[0][id]?> </td>
<td width=20% nowrap>主題</td>
<td width=30% nowrap><input type=hidden name=mid value='<?=$arr[0][mid]?>'><?=$arr[0][mid]?></td>
</tr>
<tr bgcolor=white>
<td width=20% nowrap>組別</td>
<td width=30% nowrap>
<?php set_sport_selectb("enterclass",$sportclass,$arr[0][enterclass]);?>
<?php set_sport_selectb("item",$sportname,$arr[0][item]);?>
<td width=20% nowrap>名稱/類別</td>
<td width=30% nowrap><?php set_sport_selectb("kind",$itemkind,$arr[0][kind]);?>
<?php set_sport_selectb("sportkind",$sportkind_name,$arr[0][sportkind]);?>
</td>
</tr>
<tr bgcolor=white>
<td width=20% nowrap>比賽順序</td>
<td width=30% nowrap><input type='text' name='sportorder' value='<?=$arr[0][sportorder]?>' class=ipmei size=4 onfocus="this.select();return false ;" onkeydown="moveit2(this,event);"></td>

<td width=20% nowrap>每組/錄取</td>
<td width=30% nowrap>每組
<input type='text' name='playera' value='<?=$arr[0][playera]?>' class=ipmei size=4 onfocus="this.select();return false ;" onkeydown="moveit2(this,event);">

錄取
<input type='text' name='passera' value='<?=$arr[0][passera]?>' class=ipmei size=4 onfocus="this.select();return false ;" onkeydown="moveit2(this,event);">
</td>
</tr>

<tr bgcolor=white>
<td width=20% nowrap>比賽場地</td>
<td width=80% nowrap colspan=3>
<input type='text' name='place' value='<?=$arr[0][place]?>' class=ipmei size=30 onfocus="this.select();return false ;" onkeydown="moveit2(this,event);">
</td></tr>


<tr bgcolor=white>
<td width=20% nowrap>比賽時間</td>
<td width=30% nowrap><input type='text' name='sporttime' value='<?=$arr[0][sporttime]?>' class=ipmei size=16 onfocus="this.select();return false ;" onkeydown="moveit2(this,event);"> </td>
<td width=20% nowrap>結束時間</td>
<td width=30% nowrap><input type='text' name='overtime' value='<?=$arr[0][overtime]?>' class=ipmei size=16 onfocus="this.select();return false ;" onkeydown="moveit2(this,event);"></td>
</tr>
<tr bgcolor=white>
<td width=20% nowrap>相關賽程</td>
<td width=80% nowrap colspan=3>
<?php
$net_item=get_next_item($arr[0][id]);
$TTTT="<B style='color:blue;'>".$sportclass[$net_item[enterclass]].$sportname[$net_item[item]].$itemkind[$net_item[kind]]."</B>";
$FFFF="<B style='color:blue;'>無相關比賽</B>";
echo ($net_item=='') ? $FFFF:$TTTT ;
?></td></tr>
</form>
</table>
<B style='color:red;'>※若有相關賽程，請小心修改。</B>

   	  
<?php
}

#####################   新增表單1  ###########################
function item_tb($mid) {
	global $CONN,$sportclass,$sportkind_name,$sportname;
	$SQL="select * from sport_main where id='$mid' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	echo"<form method='POST' action='$PHP_SELF' name='f1'>";
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function add(n) {
	var str1=document.f1.enterclass.value;
	var str2=new String(n);
	if (str1.indexOf(str2,0)==-1) {
	document.f1.enterclass.value=str1+n;}
	else {
	document.f1.enterclass.value=str1.replace(n,'');}
}

//-->
</SCRIPT><BR>
<table align='center' bgColor='#eef8ff' border='0' borderColor='#9EBCDD' cellPadding='0' cellSpacing='1'>
<tr>
<td><img src='images/21.gif'>&nbsp;新增比賽項目</td>
<td>
<INPUT TYPE='hidden' name='act' value=''>
<INPUT TYPE='text' NAME='ifo' value='' size='20' disabled style=' border-width: 0px; background-color:#eef8ff; font-size:12pt;color:#800000;'>
<input TYPE='image' align='top' border=0 SRC='images/ch_back2.gif' onclick="this.form.reset();return false;" alt='重新選擇' onmouseover="f1.ifo.value='重新選擇';" onmouseout="f1.ifo.value='';">
<input TYPE='image' align='top' border=0 SRC='images/ch_save.gif' 
onclick=" if (window.confirm('輸入完成？')){this.form.act.value='item_add';this.form.sumit();}return false;" alt='輸入完成' 
onmouseover="f1.ifo.value='輸入完成';" onmouseout="f1.ifo.value='';">
</td>
</tr>
<tr style='color:red'>
<td align='right' bgColor='#9EBCDD'>填寫說明</td>
<td style='color:red;font-size:9pt'>本表會一次建立每一分組的項目資料。<BR>
但每一分組的競賽順序與時間仍須一一設定。<BR>
否則檢錄單與賽程表上不會出現相關資料。
</td>
</tr><tr>
<td align='right' bgColor='#9EBCDD'>資料編號</td>
<td>
主題編號 
<?php
echo "<INPUT TYPE='hidden' name='id' value=''>";
echo"<INPUT TYPE='hidden' name='mid' value='$mid'>$mid";
?>


</td></tr>
<tr>
<td align='right' bgColor='#9EBCDD'><FONT  COLOR='red'>*</FONT>項目名稱</td>
<td>
<?php
set_sport_selectb("item",$sportname);
?>
<input type='checkbox' name='createnexttime' value='yes'><B style='color:#800000;'>一併建立初賽</B>
<B style='font-size:10pt;'>(不選時直接建立決賽)</B>
</td>
</tr>
<tr>
<td align='right' bgColor='#9EBCDD'><FONT  COLOR='red'>*</FONT>比賽日期</td>
<td>
<?php
set_time_select("year1",date("Y")-5,date("Y")+5,date("Y"));//年度
echo"年";
set_time_select("year2",1,13,date("m"));//月
echo"月";
set_time_select("year3",1,32,date("d"));//日
echo"日";
set_time_select("year4",1,24,date("G"));//時
echo"時&nbsp;&nbsp;<FONT  COLOR='red'>*</FONT>類別";
set_sport_selectb("sportkind",$sportkind_name);
?>
</td></tr>
<tr>
<td align='right' bgColor='#9EBCDD'><FONT  COLOR='red'>*</FONT>可參賽組別</td><td>
<input name='enterclass' size='20' value='' readonly class=ipmei>
<FONT  COLOR='#999999'>未註明男女則男女不分組</FONT><br>
<?php
$i=0;
foreach($sportclass as $key => $value) {

echo "
<input type='checkbox' name='enterclassa[".($i+1)."]' value='$key' 
onclick=\"add(this.value);\">$value";
	if ($i%6==5){echo"<BR>";}
	$i++;
}
?>
</td>
</tr>
<tr>
<td align='right' bgColor='#9EBCDD'>賽程</td>
<td>初賽每組
<input name='playera' size='3' style='font-size:14pt;color:blue;background-color: #FFCCCC;border-width:0px;'>
<FONT  COLOR='red'>*</FONT>人，錄取前
<input name='passera' size='3' style='font-size:14pt;color:blue;background-color: #FFCCCC;border-width:0px;'>
<FONT  COLOR='red'>*</FONT>名進入決賽。<BR>
<FONT  COLOR='red'>*</FONT>額外說明
<input name='imemo' size='18' style='font-size:14pt;color:blue;background-color: #FFCCCC;border-width:0px;'>
<BR>
<FONT SIZE='-1' COLOR='blue'>所謂每組,是指每次同時間多少人比賽。例如同次賽跑的人<BR>
額外說明：例如『擇優2人』。沒有額外的條件就不要填。</FONT>
</td></tr>
<tr>
<td align='right' bgColor='#9EBCDD'>成績與計分</td>
<td><FONT  COLOR='red'>*</FONT>計分格式
<input name='sunit' size='10' style='font-size:14pt;color:blue;background-color: #FFCCCC;border-width:0px;'><BR>
<FONT SIZE='-1' COLOR='blue'>競賽類用0.00.00.0表示時.分.秒.x<BR>
田賽類用00.00.0 表示公尺.公分.x<BR>
語文類用000表示得分</FONT>
<BR><FONT  COLOR='red'>*</FONT>
排列方式<SELECT NAME='sord'>
<option value=''>未選擇</option>
<option value='1'>分數低，成績好</option>
<option value='2'>分數高，成績好</option>
</SELECT><BR>
<FONT SIZE='-1' COLOR='blue'>分數低，成績好：如賽跑。<BR>分數高，成績好：如跳高跳遠。</FONT>
</td></tr>
<tr>
<td align='right' bgColor='#9EBCDD'>說明</td>
<td>所謂每組,是指每次同時間多少人比賽。例如同次賽跑的人</td>
</tr>
<tr>
<td align='right' bgColor='#9EBCDD'><FONT  COLOR='red'>*</FONT>比賽地點</td>
<td><input name='place' size='20' style='font-size:14pt;color:blue;background-color: #FFCCCC;border-width:0px;'>
</td></tr>
<tr>
<td align='right' bgColor='#9EBCDD'><FONT  COLOR='red'>*</FONT>接力類選項</td>
<td>每單位可報名組數
<input name='kgp' size='3' style='font-size:14pt;color:blue;background-color: #FFCCCC;border-width:0px;' value='0'>
每組人數
<input name='kgm' size='3' style='font-size:14pt;color:blue;background-color: #FFCCCC;border-width:0px;' value='0'>
<FONT SIZE='-1' COLOR='blue'>(非接力類為0)</FONT></td></tr>

</FORM>
<tr><td align='right' bgColor='#9EBCDD' colspan='2'> 
<p align='center'>程式設計:二林國小紀明村</td> 
</tr>
</table>
</table> <?php
}


#####################   列示主要項目  ###########################
function mmid2($gmid='') {
			global $CONN;
	($gmid=='') ? $SQL="select * from sport_main order by year desc": $SQL="select * from sport_main where id='$gmid' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
echo "<TABLE border=0 width=100% style='font-size:11pt;'  cellspacing=1 cellpadding=0 bgcolor=#9EBCDD><TR align=center bgcolor='#9EBCDD'><TD width=6%>項次</TD>
	<TD width=30% >名稱  </TD>
	<TD width=10%>日期</TD>
	<TD width=27%>班級報名與截止</TD>
	<TD width=27%>大會操作與截止</TD>
</TR>";
for($i=0; $i<$rs->RecordCount(); $i++) {

echo "<TR align=center bgcolor='#FFFFFF'><TD>".$arr[$i][id]."</TD>
	<TD align=left><A HREF='$PHP_SELF?mid=".$arr[$i][id]."'>".$arr[$i][title]."</A></TD>
	<TD>".$arr[$i][year]."</TD>
	<TD style='font-size:9pt;' >".substr($arr[$i][signtime],0,13)." -- ".substr($arr[$i][stoptime],0,13)."</TD>
	<TD style='font-size:9pt;'>".substr($arr[$i][work_start],0,13)." -- ".substr($arr[$i][work_end],0,13)."</TD>
</TR>";
}
echo "</TABLE>";
}

#####################   列示子項目  ###########################
function list_item($mid,$tb='') {
			global $CONN,$sportclass,$sportname,$itemkind,$sportkind_name;
	$SQL="select * from sport_item where mid='$mid' order by  enterclass,sportkind,kind ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
include_once"chi_text.js";

echo "<TABLE border=0 width=100% style='font-size:10pt;'  cellspacing=1 cellpadding=0 bgcolor=Silver>
<tr bgcolor=#F2F2F2 >
<TD width=100% colspan=13>
<img src='images/21.gif'><A HREF='$_SERVER[PHP_SELF]'>返回主題列表</A>
□編修選項<img src='images/arrow.gif'>
<img src='images/dia_bluve.gif'><A HREF='$PHP_SELF?mid=$mid&act=add_tb'>新增項目</A>
<img src='images/dia_bluve.gif'><A HREF='$PHP_SELF?mid=$mid&tb=playera'>每組人數</A>
<img src='images/dia_bluve.gif'><A HREF='$PHP_SELF?mid=$mid&tb=passera'>錄取人數</A>
<img src='images/dia_bluve.gif'><A HREF='$PHP_SELF?mid=$mid&tb=kgp'>組數(接力)</A>
<img src='images/dia_bluve.gif'><A HREF='$PHP_SELF?mid=$mid&tb=kgm'>人數(接力)</A>
<img src='images/dia_bluve.gif'><A HREF='$PHP_SELF?mid=$mid&tb=sportorder'>賽程順序</A>
<img src='images/dia_bluve.gif'><A HREF='$PHP_SELF?mid=$mid&tb=sunit'>格式</A>
<img src='images/dia_bluve.gif'><A HREF='$PHP_SELF?mid=$mid&tb=sord'>依據</A>
<img src='images/dia_bluve.gif'><A HREF='$PHP_SELF?mid=$mid&tb=imemo'>額外</A>
<img src='images/dia_bluve.gif'><A HREF='$PHP_SELF?mid=$mid&act=chtime'>時間調整</A>
</TD></tr>";
if ($tb!='' ){
$echo_str="<tr bgcolor=#F2F2F2 align=center><TD colspan=13><INPUT TYPE='hidden' name=mid value='".$mid."'>
<INPUT TYPE='hidden' name=act value='update_$tb'>
<INPUT TYPE='reset'>&nbsp;<INPUT TYPE='submit' value='填好送出'></TD></tr>";
}

echo"
<FORM METHOD=POST ACTION='$PHP_SELF' name='f1'>$echo_str<tr bgcolor=#F2F2F2 align=center>
<TD width=4% nowrap>項次</TD>
	<TD width=18%  nowrap>名稱 </TD>
	<TD width=12%  nowrap>動作</TD>
	<TD width=8% nowrap>類別</TD> 
	<TD width=6% nowrap>每組</TD>
	<TD width=6% nowrap>錄取</TD>
	<TD width=6% nowrap>組數</TD>
	<TD width=6% nowrap>人數</TD>
<TD width=6% nowrap>賽程序</TD>
	<TD width=6% nowrap>己報名</TD>

	<TD width=8% nowrap>成績格式</TD>
	<TD width=6% nowrap>成績依據</TD>
	<TD width=10% nowrap>額外</TD>
</TR><tr bgcolor=#F2F2F2><TD colspan=12 style='color:blue;'>成績依據：1表示分數低成績好,2表示分數高成績好<BR>
組數：指接力類每班可報幾組，人數指每組可有多少人，非接力類請填0。
<TD></TR>";

for($i=0; $i<$rs->RecordCount(); $i++) {
($i%10==9 && $i!=0 )? $bgc="#F2F2F2": $bgc="#FFFFFF";
($tb=='playera')? $arr[$i][playera]="<INPUT TYPE='text' NAME='playera[".$arr[$i][id]."]' value='".$arr[$i][playera]."' size=2 class=ip2  onfocus=\"this.select();\" onkeydown=\"moveit2(this,event);\" >":$arr[$i][playera]=$arr[$i][playera];
($tb=='passera')? $arr[$i][passera]="<INPUT TYPE='text' NAME='passera[".$arr[$i][id]."]' value='".$arr[$i][passera]."' size=2 class=ip2 onfocus=\"this.select();\" onkeydown=\"moveit2(this,event);\" >":$arr[$i][passera];
($tb=='sportorder')? $arr[$i][sportorder]="<INPUT TYPE='text' NAME='sportorder[".$arr[$i][id]."]' value='".$arr[$i][sportorder]."' size='3' class='ip2' onfocus=\"this.select();\" onkeydown=\"moveit2(this,event);\" >":$arr[$i][sportorder];
($tb=='sunit')? $arr[$i][sunit]="<INPUT TYPE='text' NAME='sunit[".$arr[$i][id]."]' value='".$arr[$i][sunit]."' size='10' class='ip2' onfocus=\"this.select();\" onkeydown=\"moveit2(this,event);\" >":$arr[$i][sunit];
($tb=='sord')? $arr[$i][sord]="<INPUT TYPE='text' NAME='sord[".$arr[$i][id]."]' value='".$arr[$i][sord]."' size='3' class='ip2' onfocus=\"this.select();\" onkeydown=\"moveit2(this,event);\" >":$arr[$i][sord];
($tb=='imemo')? $arr[$i][imemo]="<INPUT TYPE='text' NAME='imemo[".$arr[$i][id]."]' value='".$arr[$i][imemo]."' size='12' class='ip2' onfocus=\"this.select();\" onkeydown=\"moveit2(this,event);\" >":$arr[$i][imemo];
($tb=='kgp')? $arr[$i][kgp]="<INPUT TYPE='text' NAME='kgp[".$arr[$i][id]."]' value='".$arr[$i][kgp]."' size='2' class='ip2' onfocus=\"this.select();\" onkeydown=\"moveit2(this,event);\" >":$arr[$i][kgp];
($tb=='kgm')? $arr[$i][kgm]="<INPUT TYPE='text' NAME='kgm[".$arr[$i][id]."]' value='".$arr[$i][kgm]."' size='2' class='ip2' onfocus=\"this.select();\" onkeydown=\"moveit2(this,event);\" >":$arr[$i][kgm];


($arr[$i][skind]==0) ? $bb="":$bb="&nbsp;&nbsp;";
echo "<TR align=center bgcolor=$bgc><TD>".$arr[$i][id]."</TD>
<TD align=left  nowrap>$bb".$sportclass[$arr[$i][enterclass]].$sportname[$arr[$i][item]].$itemkind[$arr[$i][kind]]."</TD>
<TD nowrap><A HREF='$PHP_SELF?mid=$mid&act=modify&item=".$arr[$i][id]."'>修改</A>
<A HREF='$PHP_SELF?mid=$mid&act=del&item=".$arr[$i][id]."'>刪除</A></TD>
<TD nowrap>".$sportkind_name[$arr[$i][sportkind]]."-". $itemkind[$arr[$i][kind]]."</TD>
<TD>".$arr[$i][playera]."人</TD>
<TD>".$arr[$i][passera]."名</TD>
<TD>".$arr[$i][kgp]."組</TD>
<TD>".$arr[$i][kgm]."人</TD>
<TD>".$arr[$i][sportorder]."</TD>
<TD>".$arr[$i][res]."</TD>
<TD>".$arr[$i][sunit]."</TD>
<TD>".$arr[$i][sord]."</TD>
<TD>".$arr[$i][imemo]."</TD>


</TR>";
}
echo "</FORM></TABLE>";
}
#####################   修改時間  ###########################
function list_time($mid) {
			global $CONN,$sportclass,$sportname,$itemkind,$sportkind_name;
	$SQL="select * from sport_item where mid='$mid' and skind=0 order by  enterclass,sportkind,kind ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
echo "<TABLE border=0 width=100% style='font-size:11pt;'  cellspacing=1 cellpadding=0 bgcolor=Silver>
<tr bgcolor=#F2F2F2><TD width=100%>
<img src='images/21.gif'><A HREF='$_SERVER[PHP_SELF]'>返回主題列表</A>
□編修選項<img src='images/arrow.gif'>
<img src='images/dia_bluve.gif'><A HREF='$PHP_SELF?mid=$mid&act=add_tb'>新增項目</A>
<img src='images/dia_bluve.gif'><A HREF='$PHP_SELF?mid=$mid&tb=playera'>每組人數</A>
<img src='images/dia_bluve.gif'><A HREF='$PHP_SELF?mid=$mid&tb=passera'>錄取人數</A>
<img src='images/dia_bluve.gif'><A HREF='$PHP_SELF?mid=$mid&tb=sportorder'>賽程順序</A>
<img src='images/dia_bluve.gif'><A HREF='$PHP_SELF?mid=$mid&act=chtime'>時間調整</A>
</TD></tr>";

?>

<tr bgcolor=#ffffff ><TD width=100%>
<FONT COLOR='#990000'>調整作業：</FONT><?php btt();?>
<FORM METHOD=POST ACTION='<?=$PHP_SELF?>' name='f1'>
<INPUT TYPE='hidden' name=act value=''>
<INPUT TYPE='hidden' name=mid value='<?=$mid?>'>
<div style="margin-left: 40pt;background-color:#F2F2F2">
<?php bt('time_change1','採用方式１將鉤選項目變更時間','images/00_check.gif')?>
方式1：將鉤選者
<INPUT TYPE='radio' NAME='ttime' value='a'>增加
<INPUT TYPE='radio' NAME='ttime' value='d'>減少
<INPUT TYPE='text' NAME='time_valve1' class=ipmei size=6>
<?php
$gg=array('1'=>'年','2'=>'月','3'=>'日','4'=>'時','5' =>'分');
$gg1=array('MONTH'=>'月','DAY'=>'日','HOUR'=>'時','MINUTE' =>'分');
//$gg1=array(m=>"MONTH",d=>"DAY",h=>"HOUR"=>"時",MINUTE =>"分");

set_sport_selectb("ttimea",$gg1);
?>
<BR>
<?php bt('time_change2','採用方式２將鉤選項目變更時間','images/00_check.gif')?>

方式2：改變鉤選者的<?php set_sport_selectb("Ttimeb",$gg);?>改變為 
<INPUT TYPE='text' NAME='time_valve2' class=ipmei size=6><BR>
<?php btr("images/ch_back2.gif");?>

</div>
</TD></TR><tr bgcolor=#ffffff ><TD width=100% nowrap style='font-size:10pt;'>
<FONT COLOR='#990000'>資料格式：</FONT>項目-開始時間..結束時間<BR>
<?php 
	//2004-12-05 05:06:00
for($i=0; $i<$rs->RecordCount(); $i++) {
	$Ta=split("[- :]",$arr[$i][sporttime]);
	$Tb=split("[- :]",$arr[$i][overtime]);
echo $bb."<img src='images/closedb.gif'>".
	$sportclass[$arr[$i][enterclass]].$sportname[$arr[$i][item]].$itemkind[$arr[$i][kind]].
	"<INPUT TYPE='checkbox' NAME='Ta[".$arr[$i][id]."]'>\n".
	"<B style='color:#cccccc;'>".$Ta[0]."</B>&nbsp;".
	"&nbsp;<B style='color:#FF0000;'>".$Ta[1]."-".$Ta[2]."</B>&nbsp;".
	"<FONT COLOR='#0000ff'>".$Ta[3].":".$Ta[4]."</FONT> 至 ".
	"<INPUT TYPE='checkbox' NAME='Tb[".$arr[$i][id]."]'>".
	"<B style='color:#cccccc;'>".$Tb[0]."</B>&nbsp;".
	"&nbsp;<B style='color:#FF0000;'>".$Tb[1]."-".$Tb[2]."</B>&nbsp;".
	"<FONT COLOR='#0000ff'>".$Tb[3].":".$Tb[4]."</FONT>\n&nbsp;&nbsp;".sub_item($arr[$i][id])."<BR>";
}
echo "</TD></TR></TABLE></FORM>";
}

function sub_item($aa){
			global $CONN,$sportclass,$sportname,$itemkind,$sportkind_name;
	$SQL="select * from sport_item where skind='$aa'  ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$BB='';
	if ($rs->RecordCount()==1) {
	$arr=$rs->GetArray();
	$Ta=split("[- :]",$arr[0][sporttime]);
	$Tb=split("[- :]",$arr[0][overtime]);

	$BB="<img src='images/closedb.gif'>".
	$sportclass[$arr[0][enterclass]].$sportname[$arr[0][item]].$itemkind[$arr[0][kind]].
	"<INPUT TYPE='checkbox' NAME='Ta[".$arr[0][id]."]'>\n".
	"<B style='color:#cccccc;'>".$Ta[0]."</B>&nbsp;".
	"&nbsp;<B style='color:#FF0000;'>".$Ta[1]."-".$Ta[2]."</B>&nbsp;".
	"<FONT COLOR='#0000ff'>".$Ta[3].":".$Ta[4]."</FONT> 至 ".
	"<INPUT TYPE='checkbox' NAME='Tb[".$arr[0][id]."]'>".
	"<B style='color:#cccccc;'>".$Tb[0]."</B>&nbsp;".
	"&nbsp;<B style='color:#FF0000;'>".$Tb[1]."-".$Tb[2]."</B>&nbsp;".
	"<FONT COLOR='#0000ff'>".$Tb[3].":".$Tb[4]."</FONT>\n";}

return $BB;

}
?>
