<?php
//$Id: mgr_prt.1.php 8769 2016-01-13 14:16:55Z qfon $
include "config.php";
//認證
sfs_check();
//if ($_POST){
//	echo "<PRE>";print_r($_POST);print_r($_GET);echo "</PRE>";
//	die();
//	}

#####################   權限檢查與時間  ###########################
if($_GET[mid] || $_POST[mid] ) {
	($_GET[mid] == '') ? $cmid=$_POST[mid]: $cmid=$_GET[mid];
	if(ch_mid_t($cmid)!=3 ) backe("非操作時間");
	}
$ad_array=who_is_root();
if (!is_array($ad_array[$_SESSION[session_tea_sn]])){
if ($_POST[mid] || $_GET[mid] || $_POST[main_id] ) {
	$bb='';
	($_POST[mid]!='' ) ? $bb=$_POST[mid]:$bb;
	($_GET[mid]!='' ) ? $bb=$_GET[mid]:$bb;
	($_POST[main_id]!='' ) ? $bb=$_POST[main_id]:$bb;
if (check_man($_SESSION[session_tea_sn],$bb ,1)!='YES'   ) backe("您無權限操作");
	if(ch_mid_t($bb)!=3) backe("非操作時間");

}}
#####################   主程式區  ###########################
if ($_GET[mid] && $_GET[item] && $_GET[Spk] && $_GET[kitem]){

	switch($_GET[kitem]) {
	case "heigh":
		print_high($_GET[mid],$_GET[item]);break;
	case "long":
		print_long($_GET[mid],$_GET[item]);break;
	case "speed":
		print_speed($_GET[mid],$_GET[item],$_GET[Spk]);break;
	case "long_high":
		print_long_high($_GET[mid],$_GET[item],$_GET[Spk]);break;
	case "pspeed":
		print_pspeed($_GET[mid],$_GET[item],$_GET[Spk]);break;
	case "book":
		print_item($_GET[mid],$_GET[item]);break;
	case "sclass":
		print_class($_GET[mid],$_GET[Spk]);break;
	case "item":
		print_sitem($_GET[mid],$_GET[Spk]);break;

	default:}

}
#####################   田賽類印成績  ###########################
function print_long_high($mid,$item,$Spk){
	global $CONN,$sportname,$itemkind,$sportclass,$sportkind_name,$SCHOOL_BASE,$sport_GO_num;
	$SQL="select a.*,b.title,count(c.id) as nu_all from sport_item a ,sport_main b ,sport_res c where a.id ='$item' and a.mid=b.id  and c.itemid='$item' group by a.id ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$item_info=$arr[0];
	$sport_group=ceil($item_info[nu_all]/$item_info[playera]);//全部組數

	$LimtA=($Spk-1)*$item_info[playera];
	$La=$LimtA+1;
	$Lb=$Spk*$item_info[playera];
	$SQL="select * from sport_res  where itemid ='$item' and mid='$mid' and sportorder >= '$La' and  sportorder <= '$Lb' order by sportorder ";
//	die($SQL);
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
($item_info[skind]==0)? $endstr=",擇優<B>2</B>人":$endstr='';

////-------依$item計算記錄資料表內人數-------------
// onload="pp();return true;"
?>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>檢錄列印系統</title>
<SCRIPT LANGUAGE="JavaScript">
<!--
function pp() {   

	if (window.confirm('開始列印？')){
	self.print();}
}
//-->
</SCRIPT>
<link rel="stylesheet" type="text/css" href="sport.css"> 
<style>
.td1 {line-height:20px;} 
.td2 {line-height:30px;} 
</style>
</head> 
<BODY onload="pp();return true;">
<?php
//onload="pp();return true;"
echo"<CENTER><H3> ".$SCHOOL_BASE[sch_cname_s].$item_info[title]."<BR>";
echo $sportkind_name[$item_info[sportkind]]."成績 記錄表</H3></CENTER>";
?>
<table align='center' border='0' width='90%'><tr width='100%'><td align='right' width='20%'>■組別：</td>
<td  width='50%'>
<?php
echo $sportclass[$item_info[enterclass]].$sportname[$item_info[item]].$itemkind[$item_info[kind]];
?>
</td>
<td width='30%'>比賽地點：<?=$item_info[place]?>
</td>
</tr><tr><td align='right' width='20%'>■錄取：</td>
<td width='50%'><FONT SIZE='3'>
<?php
echo"共<B>$sport_group</B>組，每組<B>$item_info[playera]</B>人，錄取<B>$item_info[passera]</B>名".$item_info[imemo]."。";
?>
</FONT></td><td width='30%'>日期：<?php echo"<B>".substr($item_info[sporttime],0,11)."</B>";?>
</td></tr></table>

<table cellPadding='0' border=1 cellSpacing='1' width='90%' align=center style='border-collapse:collapse;font-size:14pt;' >
<tr bgcolor=white align='center'>
<td width='14%'><b>順序</b></td>
<td width='14%'><b> 單位</b></td>
<td width='14%'><b>號碼</b></td>
<td width='14%'><b> 姓名</b></td>
<td width='14%'><b>成績記錄</b></td>
<td width='15%'><b>名次</b></td>
<td width='15%'><b>備註</b></td>
</tr>
<?php
$AA=Count($arr);
//print_r($arr);
for ($i=0;$i<$AA;$i++) {
//	$y=$i+1;

	($arr[$i][idclass]=='') ? $tt_str="&nbsp;": $tt_str=substr($arr[$i][idclass],1,2)."班".substr($arr[$i][idclass],3,2)."號";
	($arr[$i][cname]=='') ? $Cname="&nbsp;":$Cname=$arr[$i][cname];
	($arr[$i][sportnum]=='') ? $Sportnum="&nbsp;":$Sportnum=$arr[$i][sportnum];
	($arr[$i][results]=='') ? $Results="&nbsp;":$Results=$arr[$i][results];
	($arr[$i][num]=='0') ? $Num="&nbsp;":$Num=$arr[$i][num];
	($arr[$i][sportorder]=='') ? $Sportorder="&nbsp;":$Sportorder=$arr[$i][sportorder];

echo"
<tr bgcolor=white align='center'><td width='14%'>$Sportorder </td><td width='14%'>$tt_str</td>
<td width='14%'>$Sportnum</td><td width='14%'>$Cname</td>
<td width='14%'>$Results</td><td width='15%'>$Num</td><td width='15%'>&nbsp;</td></tr>";
}
for ($i=0;$i<3;$i++){
echo"<tr bgcolor=white align='center'><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td width='15%'>&nbsp;</td><td>&nbsp;</td></tr>";
}


echo"</table>";


}
#####################   競賽類--列印成績  ###########################
function print_pspeed($mid,$item,$Spk){
	global $CONN,$sportname,$itemkind,$sportclass,$sportkind_name,$SCHOOL_BASE,$sport_GO_num;
	$SQL="select id,sportkind from sport_item where id='$item' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
if ($arr[0][sportkind]=='5') {
	$SQL="select a.*,b.title,count(c.id) as nu_all from sport_item a ,sport_main b ,sport_res c where a.id ='$item' and a.mid=b.id  and c.itemid='$item' and c.kmaster='2' group by a.id ";

} else {

	$SQL="select a.*,b.title,count(c.id) as nu_all from sport_item a ,sport_main b ,sport_res c where a.id ='$item' and a.mid=b.id  and c.itemid='$item' group by a.id ";

}

	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$item_info=$arr[0];
	$sport_group=ceil($item_info[nu_all]/$item_info[playera]);//全部組數
//($item_info[skind]==0)? $endstr=",擇優<B>2</B>名":$endstr='';

////-------依$item計算記錄資料表內人數-------------
// onload="pp();return true;"
?>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>檢錄列印系統</title>
<SCRIPT LANGUAGE="JavaScript">
<!--
function pp() {   

	if (window.confirm('開始列印？')){
	self.print();}
}
//-->
</SCRIPT>
<link rel="stylesheet" type="text/css" href="sport.css"> 
<style>
.td1 {line-height:20px;} 
.td2 {line-height:30px;} 
</style>
</head> 
<BODY onload="pp();return true;">
<?php
//onload="pp();return true;"
echo"<CENTER><H3> ".$SCHOOL_BASE[sch_cname_s].$item_info[title]."<BR>";
echo $sportkind_name[$item_info[sportkind]]." 成績記錄表</H3></CENTER>";
?>
<table align='center' border='0' width='90%'><tr width='100%'><td align='right' width='20%'>■組別：</td>
<td  width='50%'>
<?php
echo $sportclass[$item_info[enterclass]].$sportname[$item_info[item]].$itemkind[$item_info[kind]];
?>
</td>
<td width='30%'>比賽地點：<?=$item_info[place]?>
</td>
</tr><tr><td align='right' width='20%'>■錄取：</td>
<td width='50%'><FONT SIZE='3'>
<?php
echo"共<B>$sport_group</B>組，每組<B>$item_info[playera]</B>人，錄取<B>$item_info[passera]</B>名 ".$item_info[imemo]." 。";
?>
</FONT></td><td width='30%'>日期：<?php echo"<B>".substr($item_info[sporttime],0,11)."</B>";?>
</td></tr></table>

<table cellPadding='0' border=1 cellSpacing='0' width='90%' align=center  style='border-collapse:collapse;font-size:14pt;'>
<tr bgcolor=white align='center'>
<td width='14%'><b>道次</b></td>
<td width='14%'><b> 單位</b></td>
<td width='14%'><b>號碼</b></td>
<td width='14%'><b> 姓名</b></td>
<td width='14%'><b>成績記錄</b></td>
<td width='15%'><b>名次</b></td>
<td width='15%'><b>備註</b></td>
</tr>
<?php

for ($a=1;$a<=$sport_group;$a++) {
	$LimtA=($a-1)*$item_info[playera];
	$La=$LimtA+1;
	$Lb=$a*$item_info[playera];
	if ($item_info[sportkind]==5){
	$SQL="select * from sport_res  where itemid ='$item' and mid='$mid' and kmaster=2 and sportorder >= '$La' and  sportorder <= '$Lb' order by sportorder ";
	}else {
	$SQL="select * from sport_res  where itemid ='$item' and mid='$mid' and sportorder >= '$La' and  sportorder <= '$Lb' order by sportorder ";}
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$AA=Count($arr);
	$start_key=$sport_GO_num[$AA];
//$sport_GO_num=array(8=>1,7=>1,6=>2,5=>2,4=>3,3=>3,2=>4);
	$start_ok=0;
	$y=0;
echo"<tr bgcolor=white ><td colspan=7><B>◎".
$sportclass[$item_info[enterclass]].$sportname[$item_info[item]].$itemkind[$item_info[kind]]."
第 $a 組</B></td></tr>";

for ($i=1;$i<9;$i++) {
($i==$start_key) ?  $start_ok=1:$start_ok;
if ($start_ok==1 && !isset($y)) $y=0;
if ($start_ok==1 ) {
	if ($arr[$y][idclass]=='')  $tt_str="&nbsp;";
	if ($arr[$y][idclass]!=''&& $item_info[sportkind]==5 ) {
		$tt_str=$arr[$y][idclass]."班第".$arr[$y][kgp]."組";}
	if ($arr[$y][idclass]!=''&& $item_info[sportkind]!=5 ) {
		$tt_str=substr($arr[$y][idclass],1,2)."班".substr($arr[$y][idclass],3,2)."號";}

	if ($arr[$y][cname]=='') $Cname="&nbsp;";
	if ($arr[$y][cname]!='' ) {
		($item_info[sportkind]==5) ? $Cname=$arr[$y][cname]."-".$arr[$y][kgp]:$Cname=$arr[$y][cname];
		}
	($arr[$y][num]=='0') ? $Num="&nbsp;":$Num=$arr[$i][num];
	echo"<tr bgcolor=white align='center'><td width='14%'> $i &nbsp;</td><td width='14%'>$tt_str</td>
	<td width='14%'>".$arr[$y][sportnum]."&nbsp;</td><td width='14%'>".$Cname."</td>
	<td width='14%'>".$arr[$y][results]."</td><td width='15%'>$Num</td><td width='15%'>&nbsp;</td></tr>";
	$y++;
}
else {
echo"<tr bgcolor=white align='center'>
<td width='14%'> $i &nbsp;</td>
<td width='14%'>&nbsp;</td><td width='14%'>&nbsp;</td><td width='14%'>&nbsp;</td>
<td width='14%'>&nbsp;</td><td width='15%'>&nbsp;</td><td width='15%'>&nbsp;</td></tr>";}
}//end $i


}//end $a
?>
</table>
<?php

}

#####################   列印班級名冊  ###########################
function print_class($mid,$Spk){
	global $CONN,$sportname,$itemkind,$sportclass,$sportkind_name;

//print_r(get_class_teacher());
?>
<style>
<!--
.t0 {position:absolute; left: 30px;font-size:11pt;}
.t1 {position:absolute;left: 180px;font-size:11pt;}
.t2 {position:absolute;left: 330px;font-size:11pt;}
.t3 {position:absolute;left: 480px;font-size:11pt;}
.t4 {position:absolute;left: 630px;font-size:11pt;}
-->
</style>
<?php

$Spk = str_replace("a","",$Spk);
$all_class=split("_",$Spk);
sort($all_class);
foreach($all_class as $val ) {
	if ($val=='') continue;
	$SQL="select a.* ,b.item from sport_res a,sport_item b where a.mid ='$mid'  and a.idclass like '$val%' and a.itemid=b.id and b.skind=0 and a.kmaster=0 order by a.idclass  ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$Master=get_Master($mid,$val);//取得隊長
	$teach=get_class_teacher($val);//取得教師
	$tmp_str="<B>■ ".substr($val,0,1)."年".substr($val,1,2)."班 參賽隊員名冊　導師：".$teach[name]."</B>";
	$tmp_str.="<div style='margin-left: 30pt;font-size:12pt'>隊長：".$Master[cname]."</div>";
	$tmp_str2="<div style='margin-left: 20pt;font-size:11pt'><table cellPadding='0' border=0 cellSpacing='0' width='680'  style='font-size:10pt;'><tr>\n";
	$tmp_str3='';
	for($i=0; $i<$rs->RecordCount(); $i++) {
			$tmp_str3.="<td>".$arr[$i][sportnum]."(".substr($arr[$i][idclass],3,2).")".$arr[$i][cname]."-".$sportname[$arr[$i][item]]."</td>";
//			$Lg=strlen($tmp_str3);
//			$tmp_str2.=$tmp_str3.gNB($Lg);
//			$tmp_str3.=$tmp_str3;
			if($i%4==3 && $i!=0  ) $tmp_str3.="</tr><tr>";
		}//end for 

echo $tmp_str.$tmp_str2.$tmp_str3."</tr></table></div>";
	}//end foreach
}

function gNB($L){
//每行幾個，目前到第幾固
$a=20-$L;
for ($i=0;$i<$a;$i++){
	$word.='.';
	}
return $word;
}


#####################   列印比賽項目人員名冊  ###########################
function print_sitem($mid,$Spk){
	global $CONN,$sportname,$itemkind,$sportclass,$sportkind_name,$SCHOOL_BASE,$sport_GO_num;
	$Spk = str_replace("a","",$Spk);
	$all_item=split("_",$Spk);
	sort($all_item);//將資料排序
	$SQL="select id ,sportkind  from  sport_item  where  mid='$mid' ";
	$Item=initArray("id,sportkind",$SQL);//取得每項目類別
foreach($all_item as $val ) {
	if ($val=='') continue;
	if ($Item[$val]==5){
		$SQL="select a.*,count(c.id) as nu_all from sport_item a ,sport_res c where a.id ='$val'  and c.itemid='$val' and c.kmaster=2 group by a.id order by c.sportorder ";}
	else {
		$SQL="select a.*,count(c.id) as nu_all from sport_item a ,sport_res c where a.id ='$val'  and c.itemid='$val' group by a.id ";}
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$item_info=$arr[0];
	$sport_group=ceil($item_info[nu_all]/$item_info[playera]);//全部組數
	($Item[$val]==5) ? $Uv='隊':$Uv='人';
	$tmp_str="■".$sportclass[$item_info[enterclass]].$sportname[$item_info[item]].$itemkind[$item_info[kind]];
	$tmp_str.="　　共<B>".$item_info[nu_all]."</B>$Uv 分<B>$sport_group</B>組 --- 每組<B>$item_info[playera]</B>$Uv 取<B>$item_info[passera]</B>名 $item_info[imemo] 。<BR>";
	$tmp_str2='';
	for ($y=1;$y<=$sport_group;$y++) {
		$arr_stu=get_sub_item($val,$y,$item_info[playera],$Item[$val]);
		$tmp_str2.="第 $y 組：<div style='margin-left: 15pt;font-size:10pt'>";
		for ($i=0;$i<count($arr_stu);$i++) {
			if ($Item[$val]==5) {
			$tmp_str2.=$arr_stu[$i][cname]."班(第".$arr_stu[$i][kgp].$Uv.") 道次序:".$arr_stu[$i][sportorder]."&nbsp;\n";
			$G_stu=PK5($val,$arr_stu[$i][kgp],$arr_stu[$i][idclass]);
			$tmp_str2.=$G_stu."<BR>\n";
			}
			else {
			$tmp_str2.=sprintf("%02d",$arr_stu[$i][sportorder])."_".$arr_stu[$i][sportnum].$arr_stu[$i][cname]."(".substr($arr_stu[$i][idclass],1,4).")&nbsp;";}
			if($i%4==3 && $i!=0 ) $tmp_str2.= "<BR>";
		}//end for 
		$tmp_str2.="</div>";
	}//end for 

	echo $tmp_str.$tmp_str2."<BR>";

	}//end foreach
}//end func
#####################   取單組人員  ###########################
function PK5($item,$gp,$sclass){
	global $CONN;
	$SQL="select * from sport_res  where itemid ='$item' and kmaster=0 and kgp='$gp' and idclass like '$sclass%' order by sportorder ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$str='';
	for($i=0; $i<$rs->RecordCount(); $i++) {
	$str.=sprintf("%02d",$arr[$i][sportorder]).$arr[$i][cname].$arr[$i][sportnum]."&nbsp;&nbsp;";
	}
return $str;
}//end func

#####################   取單組人員  ###########################
function get_sub_item($item,$how,$nu,$k5){
	global $CONN;
	$La=($how-1)*$nu;
	$Lb=$how*$nu;
if ($k5==5){
	$SQL="select * from sport_res  where itemid ='$item' and kmaster=2 and sportorder > '$La' and  sportorder <= '$Lb' order by sportorder ";}
else {
	$SQL="select * from sport_res  where itemid ='$item'  and sportorder > '$La' and  sportorder <= '$Lb' order by sportorder ";}
	$rs=$CONN->Execute($SQL) or die($SQL);
//	echo $SQL."<br>";
	$arr=$rs->GetArray();
return $arr;
}//end func
#####################   競賽類 檢錄單 分組別列印  ###########################
function print_speed($mid,$item,$Spk){
	global $CONN,$sportname,$itemkind,$sportclass,$sportkind_name,$SCHOOL_BASE,$sport_GO_num;
	$SQL="select a.*,b.title,count(c.id) as nu_all from sport_item a ,sport_main b ,sport_res c where a.id ='$item' and a.mid=b.id  and c.itemid='$item' group by a.id ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$item_info=$arr[0];
	$sport_group=ceil($item_info[nu_all]/$item_info[playera]);//全部組數

	$LimtA=($Spk-1)*$item_info[playera];
	$La=$LimtA+1;
	$Lb=$Spk*$item_info[playera];
if ($item_info[sportkind]==5){
	$SQL="select * from sport_res  where itemid ='$item' and kmaster='2' and sportorder >= '$La' and  sportorder <= '$Lb' order by sportorder ";
	}else {
	$SQL="select * from sport_res  where itemid ='$item' and mid='$mid' and sportorder >= '$La' and  sportorder <= '$Lb' order by sportorder ";}
//	die($SQL);
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
//($item_info[skind]==0)? $endstr=",擇優<B>2</B>人":$endstr='';

////-------依$item計算記錄資料表內人數-------------
// onload="pp();return true;"
?>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>檢錄列印系統</title>
<SCRIPT LANGUAGE="JavaScript">
<!--
function pp() {   

	if (window.confirm('開始列印？')){
	self.print();}
}
//-->
</SCRIPT>
<link rel="stylesheet" type="text/css" href="sport.css"> 
<style>
td {line-height:30px;}
.td1 {line-height:20px;} 
.td2 {line-height:30px;} 
</style>
</head> 
<BODY onload="pp();return true;">
<?php
//onload="pp();return true;"
echo"<CENTER><H3> ".$SCHOOL_BASE[sch_cname_s].$item_info[title]."<BR>";
echo $sportkind_name[$item_info[sportkind]]."檢錄記錄表</H3></CENTER>";
?>
<table align='center' border='0' width='90%'><tr width='100%'><td align='right' width='20%'>■組別：</td>
<td  width='50%'>
<?php
echo $sportclass[$item_info[enterclass]].$sportname[$item_info[item]].$itemkind[$item_info[kind]];
echo "第&nbsp;<B>".$Spk."&nbsp;</B>組";
?>
</td>
<td width='30%'>比賽地點：<?=$item_info[place]?>
</td>
</tr><tr><td align='right' width='20%'>■錄取：</td>
<td width='50%'><FONT SIZE='3'>
<?php
echo"共<B>$sport_group</B>組，每組<B>$item_info[playera]</B>人，錄取<B>$item_info[passera]</B>名<B>".$item_info[imemo]."</B> 。";
?>
</FONT></td><td width='30%'>日期：<?php echo"<B>".substr($item_info[sporttime],0,11)."</B>";?>
</td></tr></table>

<table cellPadding='0' cellSpacing='0' border='1'  width='90%' align=center style='border-collapse:collapse;font-size:14pt;'>
<tr bgcolor=white align='center'>
<td width='14%'><b>道次</b></td>
<td width='14%'><b> 單位</b></td>
<td width='14%'><b>號碼</b></td>
<td width='14%'><b> 姓名</b></td>
<td width='14%'><b>成績記錄</b></td>
<td width='15%'><b>名次</b></td>
<td width='15%'><b>備註</b></td>
</tr>
<?php
$AA=Count($arr);
$start_key=$sport_GO_num[$AA];

//$sport_GO_num=array(8=>1,7=>1,6=>2,5=>2,4=>3,3=>3,2=>4);
$start_ok=0;
for ($i=1;$i<9;$i++) {
($i==$start_key) ?  $start_ok=1:$start_ok;
if ($start_ok==1 && !isset($y)) $y=0;
if ($start_ok==1 ) {
	if ($arr[$y][idclass]=='')  $tt_str="&nbsp;";
	if ($arr[$y][idclass]!=''&& $item_info[sportkind]==5 ) {
		$tt_str=$arr[$y][idclass]."班第".$arr[$y][kgp]."組";}
	if ($arr[$y][idclass]!=''&& $item_info[sportkind]!=5 ) {
		$tt_str=substr($arr[$y][idclass],1,2)."班".substr($arr[$y][idclass],3,2)."號";}

	if ($arr[$y][cname]=='') $Cname="&nbsp;";
	if ($arr[$y][cname]!='' ) {
		($item_info[sportkind]==5) ? $Cname=$arr[$y][cname]."-".$arr[$y][kgp]:$Cname=$arr[$y][cname];
		}

	($arr[$y][sportnum]=='') ? $Sportnum="&nbsp;":$Sportnum=$arr[$y][sportnum];

echo"<tr bgcolor=white align='center'><td width='10%'> $i </td><td width='18%'>$tt_str</td>
	<td width='14%'>$Sportnum</td><td width='14%'>$Cname</td>
	<td width='14%'>&nbsp;</td><td width='15%'>&nbsp;</td><td width='15%'>&nbsp;</td></tr>";
	$y++;
}
else {
echo"<tr bgcolor=white align='center'>
<td width='10%'> $i </td>
<td width='18%'>&nbsp;</td><td width='14%'>&nbsp;</td><td width='14%'>&nbsp;</td>
<td width='14%'>&nbsp;</td><td width='15%'>&nbsp;</td><td width='15%'>&nbsp;</td></tr>";}
}
?>
</table>
<CENTER><table border='0' cellPadding='0' cellSpacing='0' width='600'>
<tr align='middle'>
<td width='199' class=td2><b style='font-size:10pt'>
■徑賽裁判長：</b></td>
<td width='199' class=td2><b style='font-size:10pt'>
■終點裁判長：</b></td>
<td width='200' class=td2><b style='font-size:10pt'>
■記錄員：</b></td>
</tr>
</table></CENTER>
<br>
<br>
<BR><BR>
<!--第二張---->
<?php

echo"<CENTER><H3> ".$SCHOOL_BASE[sch_cname_s].$item_info[title]."<BR>";
echo $sportkind_name[$item_info[sportkind]]."檢錄記錄表</H3></CENTER>";
?>
<table align='center' border='0' width='90%'><tr width='100%'><td align='right' width='20%'>■組別：</td>
<td>
<?php
echo $sportclass[$item_info[enterclass]].$sportname[$item_info[item]].$itemkind[$item_info[kind]];
echo "第&nbsp;<B>".$Spk."&nbsp;</B>組";
?>
</td>
<td width='30%'>比賽地點：<?=$item_info[place]?>
</td>

</tr><tr><td align='right' width='20%'>■錄取：</td>
<td width='50%'><FONT SIZE='3'>
<?php
echo"共<B>$sport_group</B>組，每組<B>$item_info[playera]</B>人，錄取<B>$item_info[passera]</B>名 ".$item_info[imemo]." 。";
?>
</FONT></td><td width='30%'>日期：<?php echo"<B>".substr($item_info[sporttime],0,11)."</B>";?>
</td></tr></table>

<table cellPadding='0' border=1 cellSpacing='0' width='90%' align=center style='border-collapse:collapse;font-size:14pt;'>
<tr bgcolor=white align='center'>
<td width='10%'><b>道次</b></td>
<td width='18%'><b> 單位</b></td>
<td width='14%'><b>號碼</b></td>
<td width='14%'><b> 姓名</b></td>
<td width='14%'><b>成績記錄</b></td>
<td width='15%'><b>名次</b></td>
<td width='15%'><b>備註</b></td>
</tr>
<?php
$start_ok=0;
unset($y);
for ($i=1;$i<9;$i++) {
($i==$start_key) ?  $start_ok=1:$start_ok;
if ($start_ok==1 && !isset($y)) $y=0;
if ($start_ok==1 ) {
	if ($arr[$y][idclass]=='')  $tt_str="&nbsp;";
	if ($arr[$y][idclass]!=''&& $item_info[sportkind]==5 ) {
		$tt_str=$arr[$y][idclass]."班第".$arr[$y][kgp]."組";}
	if ($arr[$y][idclass]!=''&& $item_info[sportkind]!=5 ) {
		$tt_str=substr($arr[$y][idclass],1,2)."班".substr($arr[$y][idclass],3,2)."號";}
	if ($arr[$y][cname]=='') $Cname="&nbsp;";
	if ($arr[$y][cname]!='' ) {
		($item_info[sportkind]==5) ? $Cname=$arr[$y][cname]."-".$arr[$y][kgp]:$Cname=$arr[$y][cname];
		}


	($arr[$y][sportnum]=='') ? $Sportnum="&nbsp;":$Sportnum=$arr[$y][sportnum];


echo"<tr bgcolor=white align='center'><td width='10%'> $i </td><td width='18%'>$tt_str</td>
	<td width='14%'>$Sportnum</td><td width='14%'>$Cname</td>
	<td width='14%'>&nbsp;</td><td width='15%'>&nbsp;</td><td width='15%'>&nbsp;</td></tr>";
	$y++;
}
else {
echo"<tr bgcolor=white align='center'>
<td width='10%'> $i </td>
<td width='18%'>&nbsp;</td><td width='14%'>&nbsp;</td><td width='14%'>&nbsp;</td>
<td width='14%'>&nbsp;</td><td width='15%'>&nbsp;</td><td width='15%'>&nbsp;</td></tr>";}
}
?>
</table>
<CENTER><table border='0' cellPadding='0' cellSpacing='0' width='600'>
<tr align='middle'>
<td width='199' class=td2><b style='font-size:10pt'>
■徑賽裁判長：</b></td>
<td width='199' class=td2><b style='font-size:10pt'>
■終點裁判長：</b></td>
<td width='200' class=td2><b style='font-size:10pt'>
■記錄員：</b></td></tr>
</table></CENTER>
<?php

}
#####################   跳遠檢錄單  ###########################
function print_long($mid,$item){
	global $CONN,$sportname,$itemkind,$sportclass,$sportkind_name,$SCHOOL_BASE;
	$SQL="select a.*,b.title from sport_item a ,sport_main b where a.id ='$item' and a.mid=b.id ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$item_info=$arr[0];
	$SQL="select * from sport_res where itemid ='$item'  and mid='$mid' order by sportorder ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
?>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>SFS3檢錄列印系統</title>
<SCRIPT LANGUAGE="JavaScript">
function pp() {   

	if (window.confirm('開始列印？')){
	self.print();}
}
</SCRIPT>
</head>
<BODY onload="pp();return true;">
<?php
//onload="pp();return true;"
echo"<CENTER><H3> ".$SCHOOL_BASE[sch_cname_s].$item_info[title]."<BR>";
echo $sportkind_name[$item_info[sportkind]]."檢錄記錄表</H3></CENTER>";
?>
<table align='center' border='0' width='80%'>

<tr width='100%'>
<td align='right' width='20%'>■組別：</td>
<td>
<?php
echo $sportclass[$item_info[enterclass]].$sportname[$item_info[item]].$itemkind[$item_info[kind]];
?>
</td>
<td width='30%'>比賽地點：<?=$item_info[place]?>
</td>
</tr><tr><td align='right' width='20%'>■錄取：</td>
<td width='50%'><FONT SIZE='3'>
<?php
echo"全<B>1</B>組，每組<B> $item_info[playera] </B>人，錄取<B> $item_info[passera] </B>名".$item_info[imemo]."。";
?>
</FONT></td><td width='30%'><?php
echo"日期：<B>".substr($item_info[sporttime],0,11)."</B>";
?>
</td></tr></table>

<table cellPadding='0' border=1 cellSpacing='0' width='90%' align=center style='border-collapse:collapse;font-size:12pt;'>
<tr bgcolor=white align='center'>
<td width='3%' rowspan='2'><b>序</b></td><td width='8%' rowspan='2'><b>編號</b></td>
<td width='10%' rowspan='2'><b>姓名</b></td><td width='63%' colspan='7'><b>遠度</b></td>
<td width='9%' rowspan='2'><b>決賽<br>成績</b></td><td width='7%' rowspan='2'><b>名次</b></td></tr>
<tr bgcolor=white align='center'><td width='9%'>成績1</td><td width='9%'>成績2</td><td width='9%'>成績3</td>
<td width='9%'>初賽</td><td width='9%'>成績1</td><td width='9%'>成績2</td><td width='9%'>成績3</td></tr>
<?php

for ($i=0;$i<(count($arr)+5);$i++) {
	($arr[$i][cname]=='') ? $V_i='&nbsp;':$V_i=$i+1;
	($arr[$i][cname]=='') ? $Cname="&nbsp;":$Cname=$arr[$i][cname];
	($arr[$i][sportnum]=='') ? $Sportnum="&nbsp;":$Sportnum=$arr[$i][sportnum];
echo"
<tr bgcolor=white height=30>
<td align='center'>$V_i</td><td align='center'>$Sportnum</td><td align='center'>$Cname</td>
<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	}
?>
</table>
<table border='0' width='100%'><tr align='center'>
<td width='33%'><b><font face='標楷體'>田賽主任:</font></b></td>
<td width='33%'><b><font face='標楷體'>裁判:</font></b></td>
<td width='34%'><b><font face='標楷體'>記錄員:</font></b></td>
</tr>
</table></body><?php
	}
#####################   跳高檢錄單  ###########################
function print_high($mid,$item){
	global $CONN,$sportname,$itemkind,$sportclass,$sportkind_name,$SCHOOL_BASE;
//		global $CONN,$sportname,$itemkind,$sportclass,$sportkind_name;
	$SQL="select a.*,b.title from sport_item a ,sport_main b where a.id ='$item' and a.mid=b.id ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$item_info=$arr[0];
	$SQL="select * from sport_res where itemid ='$item'  and mid='$mid' order by sportorder ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();

?><html><head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>SFS3檢錄列印系統</title>
<SCRIPT LANGUAGE="JavaScript">
function pp() {   
	if (window.confirm('開始列印？')){
	self.print();}
}
</SCRIPT></head><BODY onload="pp();return true;">
<?php
echo"<CENTER><H3> ".$SCHOOL_BASE[sch_cname_s].$item_info[title]."<BR>";
echo $sportkind_name[$item_info[sportkind]]."檢錄記錄表</H3></CENTER>";
?>
<table align='center' border='0' width='80%'>

<tr width='100%'><td align='right' width='20%'>■組別：</td>
<td ><?php echo $sportclass[$item_info[enterclass]].$sportname[$item_info[item]].$itemkind[$item_info[kind]];?>
全<B>1</B>組</td>
<td width='30%'>比賽地點：<?=$item_info[place]?></td>
</tr>
<tr><td align='right' width='20%'>■錄取：</td><td width='50%'><FONT SIZE='3'>
<?php
echo"全<B>1</B>組，每組<B> $item_info[playera] </B>人，錄取<B> $item_info[passera] </B>名".$item_info[imemo]."。";
?>
</FONT></td><td width='30%'>
<?php
echo"日期：<B>".substr($item_info[sporttime],0,11)."</B>";
?>
</td></tr></table>
<table cellPadding='0' border=1 cellSpacing='0' width='90%' align=center style='border-collapse:collapse;font-size:14pt;'>
<tr bgcolor=white>
<td width='3%' rowspan='2' align='center'><b>序</b></td><td width='6%' rowspan='2' align='center'><b>編號</b></td>
<td width='9%' rowspan='2' align='center'><b>姓名</b></td><td width='72%' colspan='24' align='center'><b>高度</b></td>
<td width='5%' rowspan='2' align='center'><b>成<br>績</b></td>
<td width='5%' rowspan='2' align='center'  ><b>名<br>次</b></td></tr>
<tr bgcolor=white>
<td width='9%' colspan='3'>&nbsp;</td><td width='9%' colspan='3'>&nbsp;</td><td width='9%' colspan='3'>&nbsp;</td>
<td width='9%' colspan='3'>&nbsp;</td><td width='9%' colspan='3'>&nbsp;</td><td width='9%' colspan='3'>&nbsp;</td>
<td width='9%' colspan='3'>&nbsp;</td><td width='9%' colspan='3'>&nbsp;</td></tr>
<?php $line=1;
for ($i=0;$i<(count($arr)+3);$i++) {
	($arr[$i][cname]=='') ? $V_i='&nbsp;':$V_i=$i+1;
	($arr[$i][cname]=='') ? $Cname="&nbsp;":$Cname=$arr[$i][cname];
	($arr[$i][sportnum]=='') ? $Sportnum="&nbsp;":$Sportnum=$arr[$i][sportnum];

echo
"<tr bgcolor=white height=30><td width='3%' align=center>".$V_i."</td>
<td width='6%' align='center'><B>$Sportnum</B></td>
<td width='9%' align='center'>$Cname</td>";
?>

<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
<td width='5%'>&nbsp;</td><td width='5%'>&nbsp;</td></tr>
<?php
	}
?>
</tr></table>
<table border='0' width='100%'><tr>
<td width='33%' align='center'><b><font face='標楷體'>田賽主任:</font></b></td>
<td width='33%' align='center'><b><font face='標楷體'>裁判:</font></b></td>
<td width='34%' align='center'><b><font face='標楷體'>記錄員:</font></b></td>
</tr></table></body>
<?php
}




######################################################################################
function  print_item($mid,$item) {
		global $CONN,$sportclass,$sportname,$itemkind;
echo "<HTML><HEAD><TITLE>比賽檢錄系統</TITLE></HEAD><BODY>";
	$sql="select * from sport_item where mid='$mid' order by  enterclass ";
	$rs = $CONN->Execute($sql) or die($sql);
	$arr = $rs->GetArray();
	//(共人,分組, 每組取名, 擇優2名)
for ($i=0;$i<$rs->RecordCount();$i++) {
	$str=$sportclass[$arr[$i][enterclass]].$sportname[$arr[$i][item]].$itemkind[$arr[$i][kind]];
echo "<B>◢".$str."◤</B>--共".$arr[$i][res]."名參賽<BR><BR>";
	$sql2="select * from sport_res where itemid='".$arr[$i][id]."' order by sportorder,idclass ";
	$rs2 = $CONN->Execute($sql2) or die($sql2);
	$arr2 = $rs2->GetArray();
	$stu_str1='';
for ($y=0;$y<$rs2->RecordCount();$y++) {
	$stu_str1.="◇".$arr2[$y][sportnum].$arr2[$y][cname]."&nbsp;&nbsp;\n";
	if($y%4==3)  $stu_str1.="<BR>" ;
	}//end $y
 echo  "<div>".$stu_str1."</div><BR>" ;
	
	}//end $i
	

	echo "</BODY></HTML>";

	}

?>
