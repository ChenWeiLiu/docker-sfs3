<?
//$Id: quick_num_edit.php 5310 2009-01-10 07:57:56Z hami $
include "stud_year_config.php";
//include "../../include/sfs_case_subjectscore.php";

//認證
##################取資料函式###########################
function get_order2($SQL) {
	//項目,方式,(第幾組,每組人數,排序依)
	global $CONN ;
$rs=$CONN->Execute($SQL) or die($SQL);
$arr = $rs->GetArray();
return $arr ;
}
###############################################
sfs_check();
//echo $_POST[act];
if($_POST[act]=='wri' && $_POST[Syear]!='' && $_POST[Sclass]!='' &&$_POST[stu_sn]!=''){
	$now_Syear=sprintf("%03d",curr_year()).curr_seme();//目前學期
	foreach($_POST[stu_sn] as $stu_sn=>$stu_num){
		$Sql_1="update stud_seme set  seme_num='$stu_num' where seme_year_seme='$_POST[Syear]' and  seme_class= '$_POST[Sclass]' and  student_sn ='$stu_sn' ";
		$curr_class_num=$_POST[Sclass].sprintf("%02d",$stu_num);//組合curr_class_num值
		$Sql_2="update stud_base set curr_class_num='$curr_class_num' where  student_sn ='$stu_sn' ";
		$rs=$CONN->Execute($Sql_1) or die($Sql_1);
		if ($now_Syear==$_POST[Syear]){
			$rs=$CONN->Execute($Sql_2) or die($Sql_2);
			}//目前學年才異動curr_class_num值
//		echo $Sql_1."<BR>".$Sql_2."<BR>";
		}//end foreach
	header("Location:$_SERVER[PHP_SELF]?Syear=$_POST[Syear]&Sclass=$_POST[Sclass]");
}



$Sex=array(1=>"<FONT  COLOR='blue'>男</FONT>",2=>"<FONT SIZE='' COLOR='red'>女</FONT>");
$Sex_img=array(1=>"<img src=$SFS_PATH_HTML"."modules/stud_reg/images/boy.gif>",2=>"<img src=$SFS_PATH_HTML"."modules/stud_reg/images/girl.gif>");
$Ord=array("a.stud_sex"=>"性別","b.stud_id"=>"學號","b.seme_num"=>"原座號","a.stud_name"=>"姓名","a.stud_birthday"=>"生日");
$Ord1=array("ASC"=>"由低到高","DESC"=>"由高到低");

###############   程式開始    ###################################

($_GET[Syear]!='') ? $Syear=$_GET[Syear]:$Syear=sprintf("%03d",curr_year()).curr_seme();//目前學期//目前學年
if($_GET[Sclass]!='') $Sclass=$_GET[Sclass];
($Sclass) ? $LINK=link_a($Syear,$Sclass): $LINK=link_a($Syear);
head("編班作業");
myheader();
$linkstr = "Syear=$Syear&Sclass=$Sclass";
print_menu($menu_p,$linkstr);
echo "
<TABLE border=0 width=100% style='font-size:10pt;'  cellspacing=1 cellpadding=0 bgcolor=#9EBCDD>
<TR bgcolor=#9EBCDD><FORM name=p2><TD  nowrap> $LINK
學年度<INPUT TYPE='text' NAME='Syear' value='$Syear' size=6 class=ipmei>
<INPUT TYPE='submit' value='依學年度與班級列出'>
</TD></TR></FORM></TABLE>";


if($_POST[aa] && $_POST[bb]){
	$Ord_word=array();
	foreach($_POST[aa] as $pkey=>$pval) {
	if ($pval!='') $Ord_word[]=$pval." ".$_POST[bb][$pkey];
	}
	$Ord_word2 = implode (",", $Ord_word);
	}
if ($Ord_word2=='' )$Ord_word2="b.seme_num";

$SQL1="select b.stud_id, b.seme_num, a.stud_name, a.stud_sex, a.student_sn  ,a.stud_study_year, a.stud_birthday  from  stud_base a , stud_seme b where  a.student_sn= b.student_sn  and b.seme_year_seme='$Syear' and b.seme_class='$Sclass'  and a.stud_study_cond=0  order by  $Ord_word2 ";
$arr=get_order2($SQL1);

echo "
<table border=0  width=100% style='font-size:10pt;'  cellspacing=0 cellpadding=0 bgcolor=silver>
<TR bgcolor=white>
<TD width=40% valign=top><fieldset><legend><B>操作項目</B></legend>
<FONT SIZE=2 COLOR='blue'>※操作說明：</FONT><BR>
1.先選擇班級列示學生。<BR>
2.選擇優先條件(可單選或多選),<BR>再按【依我選擇列示】。<BR>
3.滿意後,再移到最下方按<BR>【依目前預設新座號更新資料】。<BR>
<FONT COLOR='red'>4.本程式適合於學期初時配合編班使用,期中時不建議使用,以免影響其他資料的正確性。</FONT>
<BR>
<FORM METHOD=POST ACTION='$_SERVER[PHP_SELF]?Syear=$Syear&Sclass=$Sclass' name='C1'>";


//for($i=0;$i<count($Ord);$i++) {
$i=0;
foreach($Ord as $View_key=>$View_val){
echo "<FONT COLOR='#0000FF'>■第<B style='color:red;'>".($i+1)."</B>優先</FONT><BR>\n";
echo set_select("aa[$i]",$Ord,$_POST[aa][$i]);
echo set_select("bb[$i]",$Ord1,$_POST[bb][$i])."<BR><BR>\n";
$i++;
}

echo "
<INPUT TYPE='button' NAME='b1' value='重新設定' onclick=\"location.href='$_SERVER[PHP_SELF]?Syear=$Syear&Sclass=$Sclass';\">
<INPUT TYPE='submit' NAME='b2'  value='依我選擇列示'></FORM>
<BR>
<FONT COLOR='#0000FF'>彰化縣學務系統推廣小組</FONT>
</fieldset></TD>";


echo"<TD width=60% valign=top><fieldset><legend><B>列示原班名冊</B></legend>
<table border=0  width=100% style='font-size:10pt;'  cellspacing=1 cellpadding=1 bgcolor=silver>
<TR align=center bgcolor=#9EBCDD><TD><FONT  COLOR='#FF0000'>預設<BR>新座號</FONT></TD>
<TD>姓別</TD>
<TD>原座號</TD>
<TD>學號</TD>
<TD>姓名</TD>
<TD>生日</TD></TR><FORM METHOD=POST ACTION='$_SERVER[PHP_SELF]' name='C2'>
<INPUT TYPE='hidden' NAME='act'  value=''>
<INPUT TYPE='hidden' NAME='Syear'  value='$Syear'>
<INPUT TYPE='hidden' NAME='Sclass'  value='$Sclass'>";
//列示原班人馬
for ($i=0;$i<count($arr);$i++) {
$SO_S="<INPUT TYPE='text' NAME='stu_sn[".$arr[$i][student_sn]."]' value='".($i+1)."' size=5 class=ip3>";
//echo<INPUT TYPE='text' NAME='' value=''> "<TR><TD>".$SO_S.$Sex[$arr[$i][stud_sex]].$arr[$i][seme_num].$arr[$i][stud_name]."&nbsp;</td></tr>";
echo "<TR bgcolor=white>
<TD width=15%>$SO_S</TD>
<TD width=15%>".$Sex[$arr[$i][stud_sex]]."</TD>
<TD width=15%>".$arr[$i][seme_num]."</TD>
<TD width=15%>".$arr[$i][stud_id]."</TD>
<TD width=20%>".$arr[$i][stud_name]."</TD>
<TD width=20%>".$arr[$i][stud_birthday]."</TD></TR>";
//if($i%7==6 && $i!=0 ) echo "<BR>";
}
echo "<TR bgcolor=white><TD colspan=6>
<INPUT TYPE='button' NAME='b1' value='依目前預設新座號更新資料' onclick=\"this.form.act.value='wri';this.form.submit();\">
</TD></TABLE>";

echo "</fieldset></TD></TR></FORM></TABLE>";
//&date_select($a, $b, $c, $d, $e)

#####################   班級選單  ###########################
function link_a($Syear,$Sclass=''){
//		global $PHP_SELF;//$CONN,
	$class_name_arr = class_base($Syear) ;
	$ss="選擇班級：<select name='Sclass' size='1' class='small' onChange=\"location.href='$_SERVER[PHP_SELF]?Syear='+p2.Syear.value+'&Sclass='+this.options[this.selectedIndex].value;\">
	<option value=''>未選擇</option>\n ";
	foreach($class_name_arr as $key=>$val) {
//	$key1=substr($Syear,0,3)."_".substr($Syear,3,1)."_".sprintf("%02d",substr($key,0,1))."_".substr($key,1,2);
		($Sclass==$key) ? $cc=" selected":$cc="";
		$ss.="<option value='$key' $cc>$val </option>\n";
	}
	$ss.="</select>";
Return $ss;
}
##################陣列列示函式##########################
function set_select($name,$array_name,$select_t="") {
	//名稱,起始值,結束值,選擇值
$word="<select name='".$name."' class=ip2>\n";
$word .="<option value=''>--未選擇--</option>\n";

foreach( $array_name as $key=>$val) {
//	echo $key."--".$val."<BR>";
	if ($key==$select_t)
		{$word .= "<option value='".$key."' selected>".$val."</option>\n";}
		else {
		$word .="<option value='".$key."'>".$val."</option>\n";	}
	}
$word .="</select>";
Return $word;
}
#####################   CSS  ###########################
function myheader(){
?>
<style type="text/css">
body{background-color:#f9f9f9;font-size:12pt}
.ipmei{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:14pt;}
.ipme2{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:14pt;color:red;font-family:標楷體 新細明體;}
.ip2{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:10pt;color:red;font-family:新細明體 標楷體;}
.ip3{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:12pt;color:blue;font-family:新細明體 標楷體;}
.bu1{border-style: groove;border-width:1px: groove;background-color:#CCCCFF;font-size:12px;Padding-left:0 px;Padding-right:0 px;}
.bub{border-style: groove;border-width:1px: groove;background-color:#FFCCCC;font-size:14pt;}
.bur2{border-style: groove;border-width:1px: groove;background-color:#FFCCCC;font-size:12px;Padding-left:0 px;Padding-right:0 px;}
A:link  {text-decoration:none;color:blue; }
A:visited {text-decoration:none;color:blue; }
A:hover {background-color:rgb(230, 236, 240);color: #000000;text-decoration: underline; }
</style><?
}


?>
