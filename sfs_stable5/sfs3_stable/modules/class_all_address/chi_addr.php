<?php
//$Id: chi_addr.php 6960 2012-10-23 08:04:32Z hami $
/*引入學務系統設定檔*/
include "../../include/config.php";

require_once "./module-cfg.php";

//使用者認證
sfs_check();

$Sex=array(1=>'男',2=>'女');

($_GET[Year]!='') ? $Year=$_GET[Year]:$Year=sprintf("%03d",curr_year()).curr_seme();//目前學期//目前學年
if($_GET[Sclass]!='') $Sclass=$_GET[Sclass];
($_GET[Sclass]) ? $LINK=link_a($Year,$_GET[Sclass]): $LINK=link_a($Year);

head("班級事務");
myheader();
print_menu($menu_p);


echo "
<TABLE border=0 width=100% style='font-size:11pt;'  cellspacing=1 cellpadding=0 bgcolor=#9EBCDD>
<TR bgcolor=#9EBCDD><FORM name=p2><TD  nowrap> $LINK
<INPUT TYPE='text' NAME='Year' value='$Year' size=6 class=ipmei>
</TD></TR></FORM>
<TR bgcolor=#9EBCDD><TD  nowrap></TD></TR></TABLE>";

if($_GET[Sclass]!='') {
//	$SQL="	select  b.stud_id, b.seme_num, a.stud_name, a.stud_sex, a.stud_birthday, a.stud_person_id, a.stud_tel_1, a.stud_tel_2, a.stud_tel_3, a.stud_addr_1, a.stud_addr_2  from  stud_base a , stud_seme b where  a.student_sn= b.student_sn  and b.seme_year_seme='$Year' and b.seme_class='$Sclass' order by  b.seme_num ";
	$SQL1="	select right((a.stud_birthday - INTERVAL 1911 YEAR),8) as bir,b.stud_id, b.seme_num, a.stud_name, a.stud_sex,  a.stud_person_id, a.stud_tel_1, a.stud_tel_2, a.stud_tel_3, a.stud_addr_1, a.stud_addr_2 
 from  stud_base a , stud_seme b where  a.student_sn= b.student_sn  and b.seme_year_seme='$Year' and b.seme_class='$Sclass'  and a.stud_study_cond in (0,5) order by  b.seme_num ";

	$arr=get_order2($SQL1);

// ;concat(YEAR(a.stud_birthday)-1911, MONTH(a.stud_birthday), DAY(a.stud_birthday))(a.stud_birthday - INTERVAL 1911 YEAR) as bir


echo "<TABLE border=0 width=100% style='font-size:10pt;'  cellspacing=1 cellpadding=0 bgcolor=#9EBCDD>
<TR bgcolor=white>

<TD nowrap colspan=11 style='color:red'>
□<A HREF='chi_paddr.php?act=csv&Year=$Year&Sclass=$Sclass' target =_blank>輸出CSV</A> &nbsp;
□<A HREF='chi_paddr.php?act=word&Year=$Year&Sclass=$Sclass' target =_blank>輸出WORD</A> &nbsp;
□<A HREF='chi_paddr.php?act=excel&Year=$Year&Sclass=$Sclass' target =_blank>輸出Excel</A> &nbsp;
□<A HREF='chi_paddr.php?act=Allcsv&Year=$Year&Sclass=$Sclass' target =_blank>輸出全年級CSV</A> &nbsp;
□<A HREF='chi_paddr.php?act=Allword&Year=$Year&Sclass=$Sclass' target =_blank>輸出全年級Word</A> &nbsp;
□<A HREF='chi_paddr.php?act=Allexcel&Year=$Year&Sclass=$Sclass' target =_blank>輸出全年級Excel</A>
</TD></TR><TR bgcolor=white align=center>
	<TD nowrap>學號</TD>
	<TD nowrap>座號</TD>
	<TD nowrap>姓名</TD>
	<TD nowrap>性別</TD>
	<TD nowrap>生日</TD>
	<TD nowrap>身分証</TD>
	<TD nowrap> 戶籍</TD>
	<TD nowrap>連絡</TD>
	<TD nowrap>行動</TD>
	<TD nowrap> 戶籍地址</TD>
	<TD nowrap> 連絡地址</TD>
</TR>";

for ($i=0;$i<count($arr);$i++) {

echo "<TR bgcolor=white>
<TD>".$arr[$i][stud_id]."</TD>
<TD>".$arr[$i][seme_num]."</TD><TD nowrap>".$arr[$i][stud_name]."</TD>
<TD>".$Sex[$arr[$i][stud_sex]]."</TD><TD>".$arr[$i][bir]."</TD>
<TD>".$arr[$i][stud_person_id]."</TD><TD>".$arr[$i][stud_tel_1]."</TD>
<TD>".$arr[$i][stud_tel_2]."</TD><TD>".$arr[$i][stud_tel_3]."</TD>
<TD>".$arr[$i][stud_addr_1]."</TD><TD>".$arr[$i][stud_addr_2]."</TD>
</TR>";

}


echo "</TABLE>";

	} //end if get

foot();

#####################   CSS  ###########################

function myheader(){
?>
<style type="text/css">

body{background-color:#f9f9f9;font-size:12pt}
.ipmei{border-style: solid; border-width: 0px; background-color: rgb(230, 236, 240); font-size:14pt;}
.ipme2{border-style: solid; border-width: 0px; background-color: #FFCCFF; font-size:14pt;}
.bu1{border-style: groove;border-width:1px: groove;background-color:#CCCCFF;font-size:12px;Padding-left:0 px;Padding-right:0 px;}
.bur{border-style: groove;border-width:1px: groove;background-color:#FFFFFF;font-size:12px;Padding-left:0 px;Padding-right:0 px;}
.bur2{border-style: groove;border-width:1px: groove;background-color:#FFCCCC;font-size:12px;Padding-left:0 px;Padding-right:0 px;}
A:link  {text-decoration:none;color:blue; }
A:visited {text-decoration:none;color:blue; }
A:hover {background-color:rgb(230, 236, 240);color: #000000;text-decoration: underline; }
</style>
<?php
}

#####################   班級選單  ###########################
function link_a($Year,$Sclass=''){
		global $PHP_SELF;//$CONN,
	$class_name_arr = class_base() ;
	$ss="選擇班級：<select name='Sclass' size='1' class='small' onChange=\"location.href='$PHP_SELF?Year='+p2.Year.value+'&Sclass='+this.options[this.selectedIndex].value;\">
	<option value=''>未選擇</option>\n ";
	foreach($class_name_arr as $key=>$val) {
		($Sclass==$key) ? $cc=" selected":$cc="";
		$ss.="<option value='$key' $cc>$val </option>\n";
	}
	$ss.="</select>";
Return $ss;
}

##################取資料函式###########################
function get_order2($SQL) {
	global $CONN ;
$rs=$CONN->Execute($SQL) or die($SQL);
$arr = $rs->GetArray();
return $arr ;
}
?>