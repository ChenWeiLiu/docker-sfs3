<?php
include "config.php";
?><html>
<meta http-equiv="Content-Type" content="text/html; Charset=Big5">
<head>
<title>SFS3學務管理系統 -- 競賽成績查詢</title>


<link rel=stylesheet type="text/css" href="<?=$SFS_PATH_HTML?>themes/new/new.css">
</head>
<script language="JavaScript">
	function change_link(url) {
		window.location.href=url;
	}
</script>
<body>
<?php
echo " <CENTER><B>".$SCHOOL_BASE[sch_cname_s]."競賽成績查閱系統</B></CENTER>";
($_GET[mid]!='') ? mmid2($_GET[mid]):mmid2();
if ($_GET[mid]!='' && $_GET[item]=='') echo item_list($_GET[mid]);
if ($_GET[mid]!='' && $_GET[item]!='') echo item_list($_GET[mid],$_GET[item]);
if ($_GET[mid]!='' && $_GET[item]!='') echo stud_list($_GET[mid],$_GET[item]);


#####################  列示學生   #############################
function stud_list($mid,$item) {
	global $sportname,$itemkind,$sportclass,$k_unit;
	$arr_1=get_item($item);//取得初賽項目
	$arr_2=get_next_item($item);//取得複賽項目
//if ($arr_2[sportkind]=='5' || $arr_1[sportkind]=='5') return '';
if ($arr_1=='' && $arr_2=='') return '尚無資料！';
($arr_1[sportkind]=='5') ? $A_nu=chkman_nu($arr_1[id]):$A_nu=chkman4($arr_1[id]);//計算總人/隊數
	$A_one=$arr_1[playera];//每組人數
	$A_go=$arr_1[passera];//錄取人數
	$A_Name=$sportclass[$arr_1[enterclass]].$sportname[$arr_1[item]].$itemkind[$arr_1[kind]];//名稱
	$B_Name=$sportclass[$arr_2[enterclass]].$sportname[$arr_2[item]].$itemkind[$arr_2[kind]];//名稱
	$A_gp=ceil($A_nu/$A_one);//計算組數
?>
<table border=0 width='100%' style='font-size:11pt;'  cellspacing=1 cellpadding=0 bgcolor=silver>
<tr bgcolor=white><td width =50% valign=top>

<?php
///初賽顯示//$arr_1;
($arr_2=='')? $tmp_word='':$tmp_word="進決賽";
echo"<div style='color:#800000;'><FONT COLOR='blue'>■".$A_Name."</FONT><BR> 共 <B>$A_nu</B> 名(隊)參賽,每組<B>$A_one</B>名,錄取<B>$A_go</B>名 <B>$arr_1[imemo]</B>$tmp_word。</div>";
	($arr_1[sord]=='2') ? $ord=' desc':$ord='' ;
echo "<HR size=1 color=#800000><table width=100%>
<TR bgcolor=silver style='color:#800000;font-size:10pt;' align=center><TD>組別</TD>
<TD>編號</TD><TD>序(道次)</TD><TD>班別</TD><TD>姓名</TD><TD>成績</TD></TR>";

for ($a=1;$a<=$A_gp ;$a++){
($arr_1[sportkind]=='5') ? $Arr=get_order($arr_1[id],'par',"results $ord ,$a,$A_one",5):$Arr=get_order($arr_1[id],'par',"results $ord ,$a,$A_one");
	$tmp_str="<tr><td colspan=6>□賽次：第 $a 組</td></tr>";
	for($i=0; $i<count($Arr); $i++) {
			$tmp_order=$Arr[$i][sportorder]-($a-1)*$A_one;
		( $Arr[$i][results]==$arr_1[sunit]) ? $Cor='#696969':$Cor='red';//沒有成績為灰色
		if ($arr_1[sportkind]=='5') {
			$alert=get_gp_man($mid,$Arr[$i][itemid],$Arr[$i][cname],1);//組數未實作
			$tmp_str.="<tr align=center bgcolor=#DFDFDF><td>&nbsp;</td><td>&nbsp;</td><td>".$tmp_order."</td><td colspan=2><A HREF='#' onclick=\"alert('$alert');\">".$Arr[$i][cname]." 班第 ".$Arr[$i][kgp]." 組/隊</a></td><td><FONT COLOR='$Cor'>".$Arr[$i][results]."</FONT></td></tr>\n";
			}
		else {
			$tmp_str.="<tr align=center bgcolor=#DFDFDF><td>&nbsp;</td><td>".$Arr[$i][sportnum]."</td><td>".
			$tmp_order."</td><td>".
			substr($Arr[$i][idclass],0,-2)."</td><td>".$Arr[$i][cname]."</td><td><FONT COLOR='$Cor'>".$Arr[$i][results]."</td></tr>\n";
			}
	}
echo $tmp_str;
	}
unset($tmp_str);
?>

</TABLE></td><td width =50% valign=top>

<?php
///////////////決賽顯示處理 ///////////////////////

if ($arr_2!=''){
($arr_2[sportkind]=='5') ? $B_nu=chkman_nu($arr_2[id]):$B_nu=chkman4($arr_2[id]);//計算總人/隊數
//	$B_nu=chkman4($arr_2[id]);//總人數
	$B_one=$arr_2[playera];//每組人數
	$B_go=$arr_2[passera];//錄取人數
	$B_Name=$sportclass[$arr_2[enterclass]].$sportname[$arr_2[item]].$itemkind[$arr_2[kind]];//名稱
	$B_gp=ceil($B_nu/$B_one);//計算組數



echo"<div style='color:#800000'><FONT  COLOR='blue'>■".$B_Name."</FONT><BR> 共 <B>$B_nu</B> 人進決賽 , 每組 <B>$B_one</B> 人 , 錄取 <B>$B_go</B> 人。</div><HR size=1 color=#800000>";
echo "<table width=100%><TR bgcolor=silver style='color:#800000;font-size:10pt;' align=center><TD>組別</TD>
<TD>編號</TD>
<TD>序(道次)</TD>
<TD>班別</TD>
<TD>姓名</TD>
<TD>成績</TD>
</TR>";

($arr_2[sord]=='2') ? $ord=' desc':$ord='' ;//排序依據
for ($a=1;$a<=$B_gp ;$a++){
($arr_2[sportkind]=='5') ? $Arr=get_order($arr_2[id],'par',"results $ord ,$a,$A_one",5):$Arr=get_order($arr_2[id],'par',"results $ord ,$a,$A_one");
// 是否接力賽
	$tmp_str="<tr><td colspan=6>□賽次：第 $a 組</td></tr>";
	for($i=0; $i<count($Arr); $i++) {
		( $Arr[$i][results]==$arr_1[sunit]) ? $Cor='#696969':$Cor='red';//沒有成績為灰色
		if ($arr_1[sportkind]=='5') {
			$alert=get_gp_man($mid,$Arr[$i][itemid],$Arr[$i][cname],1);//組數未實作
			$tmp_str.="<tr align=center bgcolor=#DFDFDF><td>&nbsp;</td><td>&nbsp;</td><td>".$Arr[$i][sportorder]."</td><td colspan=2 ><A HREF='#' onclick=\"alert('$alert');\">".$Arr[$i][cname]." 班第 ".$Arr[$i][kgp]." 組/隊</A></td><td><FONT COLOR='$Cor'>".$Arr[$i][results]."</FONT></td></tr>\n";
			}
		else {
			$tmp_str.="<tr align=center bgcolor=#DFDFDF><td>&nbsp;</td><td>".$Arr[$i][sportnum]."</td><td>".
			$Arr[$i][sportorder]."</td><td>".
			substr($Arr[$i][idclass],0,-2)."</td><td>".$Arr[$i][cname]."</td><td><FONT COLOR='$Cor'>".$Arr[$i][results]."</td></tr>\n";
			}
	}
echo $tmp_str;
	}

echo"</table>";
}
?>
</td></tr></table>
<?php
}
#####################   列示主要項目  ###########################
function Co_GP($lg,$nu){//組距,編號
$a=ceil($nu/$lg);//計算組數
return $a;
}


#####################   列示主要項目  ###########################
function mmid2($mid) {
			global $CONN; //left join sport_res c (on b.mid=a.id ) 
	$SQL="select * from sport_main order by year desc ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$SQL1="select mid, count(id) as bnu  from sport_item  group by mid ";
	$rsa=$CONN->Execute($SQL1) or die($SQL1);//統計各次比賽開辦項目數
	$arr1=$rsa->GetArray();
	$SQL2="select mid, count(id) as cnu  from sport_res where itemid !='0' group by mid ";
	$rsb=$CONN->Execute($SQL2) or die($SQL2);//統計各次比賽參賽人數
	$arr2=$rsb->GetArray();
//print_r($arr1);
//print_r($arr2);
$view_img="<img src=images/arrow.gif>";
echo "<TABLE border=0 width=100% style='font-size:11pt;'  cellspacing=1 cellpadding=0 bgcolor=#9EBCDD><TR align=center bgcolor='#9EBCDD'><TD width=6%>項次</TD>
	<TD nowarp>比賽名稱&nbsp;</TD>
	<TD nowarp>比賽日期</TD>
	<TD nowarp>開始報名日期</TD>
	<TD nowarp>截止報名日期</TD>
	<TD nowarp>比賽項目數</TD>
	<TD nowarp>報名人次</TD>
</TR>";
for($i=0; $i<$rs->RecordCount(); $i++) {
	$nu1=0;$nu2=0;
	for($x=0; $x<$rsa->RecordCount(); $x++) {
		($arr[$i][id]==$arr1[$x][mid]) ? $nu1=$arr1[$x][bnu]: $nu1=$nu1;
		}	//取出該比賽項目數
	for($x=0; $x<$rsb->RecordCount(); $x++) {
		($arr[$i][id]==$arr2[$x][mid]) ? $nu2=$arr2[$x][cnu]: $nu2=$nu2;
		}	//取出該比賽總人數

($mid==$arr[$i][id]) ? $now_view=$view_img:$now_view='';
echo "<TR align=center bgcolor='#FFFFFF'><TD>".$arr[$i][id]."</TD>
	<TD align=left  nowarp>$now_view<A HREF='$PHP_SELF?mid=".$arr[$i][id]."'>".$arr[$i][title]."</A></TD>
	<TD>".$arr[$i][year]."</TD>
	<TD>".substr($arr[$i][signtime],0,13)."</TD>
	<TD>".substr($arr[$i][stoptime],0,13)."</TD>
	<TD>".$arr[$i][bnu]."$nu1</TD>
	<TD>".$arr[$i][bnu]."$nu2</TD></TR>";
}
echo "</TABLE>";
}

#####################  列示項目   #############################
function item_list($mid,$item=''){
		global $CONN,$sportclass,$sportname,$itemkind;
	$SQL="select *  from sport_item   where  mid='$mid' and  skind=0  order by  kind, enterclass ";//and sportkind!=5 
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();

	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' group by itemid ";
	$arr_1=initArray("itemid,nu",$SQL);//全部人數
	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' and  sportnum!='' group by itemid ";
	$arr_2=initArray("itemid,nu",$SQL);//己檢錄人數(編排順序)
	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' and  sportorder!=0 group by itemid ";
	$arr_3=initArray("itemid,nu",$SQL);//己檢錄人數(編排順序)
	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' and  results!='' and  kmaster=0  group by itemid ";
	$arr_4=initArray("itemid,nu",$SQL);//有成績人數

	$ss="<FORM name=p2>選擇查閱比賽項目：<select name='link2' size='1' class='bur' onChange=\"if(document.p2.link2.value!='')change_link(document.p2.link2.value);\">\n<option value='$PHP_SELF?mid=$_GET[mid]&item='>未選擇</option> ";

for($i=0; $i<$rs->RecordCount(); $i++) {
//	($_GET[item]==$arr[$i][id]) ? $gg='images/arrow.gif':$gg='images/closedb.gif';
//		$Nu_arr=chk4num($arr[$i][id]);////報名,沒成績,沒排序
//	(
		($arr_1[$arr[$i][id]]=='') ? $Nu1=0:$Nu1=$arr_1[$arr[$i][id]];
		($arr_2[$arr[$i][id]]=='') ? $Nu2=0:$Nu2=$arr_2[$arr[$i][id]];
		($arr_3[$arr[$i][id]]=='') ? $Nu3=0:$Nu3=$arr_3[$arr[$i][id]];
		($arr_4[$arr[$i][id]]=='') ? $Nu4=0:$Nu4=$arr_4[$arr[$i][id]];

		($item==$arr[$i][id]) ? $cc=" selected":$cc="";
		$ss.="<option value='$PHP_SELF?mid=$_GET[mid]&item=".$arr[$i][id]."'$cc>".$sportclass[$arr[$i][enterclass]].$sportname[$arr[$i][item]].$itemkind[$arr[$i][kind]].
		"&nbsp;(報名數: $Nu1 編號數: $Nu2 檢錄數: $Nu3 成績數: $Nu4)</option>\n";
//	echo "<img src='$gg'><A HREF='$PHP_SELF?mid=$_GET[mid]&item=".$arr[$i][id]."'>". $sportclass[$arr[$i][enterclass]].$sportname[$arr[$i][item]].$itemkind[$arr[$i][kind]]."</A>(<B style='color:#c0c0c0' >".$arr[$i][bu]."</B>)";
	}
	$ss.="</select></FORM>";
Return $ss;
}
#####################  分組用  #############################
function G_gp($order,$li){
//傳入編號,每組人數
$ss=ceil($order / $li);// 求餘數

Return $ss ;//傳出組別
}

function get_gp_man($mid,$item,$class,$gp){
//傳入編號,每組人數
	global $CONN;
$SQL="select cname from sport_res where mid='$mid' and idclass like '$class%'  and kmaster=0 ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$ss='';
for($i=0; $i<$rs->RecordCount(); $i++) {
$ss.=$arr[$i][cname]."、";
}

Return $ss ;//傳出組別
}

?>