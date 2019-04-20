<?php
//$Id: chi_paddr.php 8527 2015-09-15 01:52:08Z chiming $
/*引入學務系統設定檔*/
include "../../include/config.php";

//require_once "./module-cfg.php";
$Sex=array(1=>'男',2=>'女');
//使用者認證
##################取資料函式###########################
function get_order2($SQL) {
	global $CONN ;
$rs=$CONN->Execute($SQL) or die($SQL);
$arr = $rs->GetArray();
return $arr ;
}

sfs_check();

################################    輸出 CSV  ##################################

if ($_GET[act]=='csv' && $_GET[Year]!='' && $_GET[Sclass]!='' ){
	$Year=$_GET[Year];$Sclass=$_GET[Sclass];
	$SQL="select right((a.stud_birthday - INTERVAL 1911 YEAR),8) as bir,b.stud_id, b.seme_num, a.stud_name, a.stud_sex,  a.stud_person_id, a.stud_tel_1, a.stud_tel_2, a.stud_tel_3, a.stud_addr_1, a.stud_addr_2  from  stud_base a , stud_seme b where  a.student_sn= b.student_sn  and b.seme_year_seme='$Year' and b.seme_class='$Sclass'  and a.stud_study_cond=0  order by  b.seme_num ";
	$arr=get_order2($SQL) or die($SQL);
	$filename = $Year."_".$Sclass."_".date("Ymd").".csv";
	$Str="代號,姓名,性別,班級,座號,生日,身分證字號, 戶籍電話,連絡電話,行動電話,戶籍地址,連絡地址\n";
for ($i=0;$i<count($arr);$i++) {
	$Str.=$arr[$i][stud_id].",".$arr[$i][stud_name].",".$Sex[$arr[$i][stud_sex]].",".$Sclass.",".
	$arr[$i][seme_num].",".$arr[$i][bir].",".$arr[$i][stud_person_id].",".
	$arr[$i][stud_tel_1].",".$arr[$i][stud_tel_2].",".$arr[$i][stud_tel_3].",".
	$arr[$i][stud_addr_1].",".$arr[$i][stud_addr_2]."\n";
}

header("Content-disposition: attachment; filename=$filename");
header("Content-type: text/x-csv");
//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

header("Expires: 0");
echo $Str;
}
################################    輸出WORD EXCEL  ##################################

if (($_GET[act]=='word'||$_GET[act]=='excel') && $_GET[Year]!='' && $_GET[Sclass]!='' ){
	$Year=$_GET[Year];$Sclass=$_GET[Sclass];
	$SQL="select right((a.stud_birthday - INTERVAL 1911 YEAR),8) as bir,b.stud_id, b.seme_num, a.stud_name, a.stud_sex,  a.stud_person_id, a.stud_tel_1, a.stud_tel_2, a.stud_tel_3, a.stud_addr_1, a.stud_addr_2  from  stud_base a , stud_seme b where  a.student_sn= b.student_sn  and b.seme_year_seme='$Year' and b.seme_class='$Sclass'  and a.stud_study_cond=0  order by  b.seme_num ";
	$arr=get_order2($SQL) or die($SQL);
	
	($_GET[act]=='word') ? $filename = $Sclass."_".date("Y_m_d").".doc": $filename = $Sclass."_".date("Y_m_d").".xls";
	($_GET[act]=='word') ? $dd=' ': $dd='&nbsp;';
	$Str="<TABLE border=1 cellspacing=0 cellpadding=0 width='100%' bgcolor=black style='width:100.0%;background:black;border-collapse:collapse;border:none; mso-border-alt:solid windowtext .5pt;mso-padding-alt:0cm 0cm 0cm 0cm'><TR bgcolor=white><TD>代號</TD><TD>姓名</TD><TD>性別</TD><TD>班級</TD><TD>座號</TD><TD>生日</TD><TD>身分證字號</TD><TD> 戶籍電話</TD><TD>連絡電話</TD><TD>行動電話</TD><TD>戶籍地址</TD><TD>連絡地址</TD></TR>";
	
for ($i=0;$i<count($arr);$i++) {
	$Str.="<TR bgcolor=white><TD>".$arr[$i][stud_id]."</TD><TD>".$arr[$i][stud_name]."</TD><TD>".
	$Sex[$arr[$i][stud_sex]]."</TD><TD>".$Sclass."</TD><TD>".
	$arr[$i][seme_num]."</TD><TD>$dd".$arr[$i][bir]."</TD><TD>".$arr[$i][stud_person_id]."</TD><TD>".
	$arr[$i][stud_tel_1]."</TD><TD>".$arr[$i][stud_tel_2]."</TD><TD>".$arr[$i][stud_tel_3]."</TD><TD>".
	$arr[$i][stud_addr_1]."</TD><TD>".$arr[$i][stud_addr_2]."</TD></TR>";
}

	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream ; Charset=Big5");
	//header("Pragma: no-cache");
					//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	header("Expires: 0");
	echo $Str."</TABLE>";
	}
################################    輸出全學年CSV  ##################################

if ($_GET[act]=='Allcsv' && $_GET[Year]!='' && $_GET[Sclass]!='' ){
	$Year=$_GET[Year];$Sclass=$_GET[Sclass];
$Sclass=substr($Sclass,0,1);
	$SQL="select right((a.stud_birthday - INTERVAL 1911 YEAR),8) as bir,b.stud_id, b.seme_class,b.seme_num, a.stud_name, a.stud_sex,  a.stud_person_id, a.stud_tel_1, a.stud_tel_2, a.stud_tel_3, a.stud_addr_1, a.stud_addr_2  from  stud_base a , stud_seme b where  a.student_sn= b.student_sn  and b.seme_year_seme='$Year' and b.seme_class like '$Sclass%'  and a.stud_study_cond=0  order by   b.seme_class,b.seme_num ";
	$arr=get_order2($SQL) or die($SQL);
	$filename =$Year."_ALL_".$Sclass."_".date("Ymd").".csv";
	$Str="代號,姓名,性別,班級,座號,生日,身分證字號,戶籍電話,連絡電話,行動電話,戶籍地址,連絡地址\n";
for ($i=0;$i<count($arr);$i++) {
	$Str.=$arr[$i][stud_id].",".$arr[$i][stud_name].",".$Sex[$arr[$i][stud_sex]].",".$arr[$i][seme_class].",".
	$arr[$i][seme_num].",".$arr[$i][bir].",".$arr[$i][stud_person_id].",".
	$arr[$i][stud_tel_1].",".$arr[$i][stud_tel_2].",".$arr[$i][stud_tel_3].",".
	$arr[$i][stud_addr_1].",".$arr[$i][stud_addr_2]."\n";
}

header("Content-disposition: attachment; filename=$filename");
header("Content-type: text/x-csv");
//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

header("Expires: 0");
echo $Str;
}
################################    輸出全學年word,excel   ##################################
if (($_GET[act]=='Allword'||$_GET[act]=='Allexcel') && $_GET[Year]!='' && $_GET[Sclass]!='' ){
	$Year=$_GET[Year];$Sclass=$_GET[Sclass];
$Sclass=substr($Sclass,0,1);
	$SQL="select right((a.stud_birthday - INTERVAL 1911 YEAR),8) as bir,b.stud_id, b.seme_class,b.seme_num, a.stud_name, a.stud_sex,  a.stud_person_id, a.stud_tel_1, a.stud_tel_2, a.stud_tel_3, a.stud_addr_1, a.stud_addr_2  from  stud_base a , stud_seme b where  a.student_sn= b.student_sn  and b.seme_year_seme='$Year' and b.seme_class like '$Sclass%'  and a.stud_study_cond=0  order by   b.seme_class,b.seme_num ";
	$arr=get_order2($SQL) or die($SQL);
	
	($_GET[act]=='Allword') ? $filename = "All_".$Sclass."_".date("Y_m_d").".doc": $filename = "All_".$Sclass."_".date("Y_m_d").".xls";
	($_GET[act]=='Allword') ? $dd=' ': $dd='&nbsp;';
	$Str="<html><meta http-equiv=\"Content-Type\" content=\"text/html; Charset=Big5\">
<body><TABLE border=1 cellspacing=0 cellpadding=0 width='100%' bgcolor=black style='width:100.0%;background:black;border-collapse:collapse;border:none; mso-border-alt:solid windowtext .5pt;mso-padding-alt:0cm 0cm 0cm 0cm'><TR bgcolor=white><TD>代號</TD><TD>姓名</TD><TD>性別</TD><TD>班級</TD><TD>座號</TD><TD>生日</TD><TD>身分證字號</TD><TD> 戶籍電話</TD><TD>連絡電話</TD><TD>行動電話</TD><TD>戶籍地址</TD><TD>連絡地址</TD></TR>";
	
for ($i=0;$i<count($arr);$i++) {
	$Str.="<TR bgcolor=white><TD>".$arr[$i][stud_id]."</TD><TD>".$arr[$i][stud_name]."</TD><TD>".
	$Sex[$arr[$i][stud_sex]]."</TD><TD>".$arr[$i][seme_class]."</TD><TD>".
	$arr[$i][seme_num]."</TD><TD>$dd".$arr[$i][bir]."</TD><TD>".$arr[$i][stud_person_id]."</TD><TD>$dd ".
	$arr[$i][stud_tel_1]."</TD><TD>$dd ".$arr[$i][stud_tel_2]."</TD><TD>$dd ".$arr[$i][stud_tel_3]."</TD><TD>".
	$arr[$i][stud_addr_1]."</TD><TD>".$arr[$i][stud_addr_2]."</TD></TR>";
}

	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream ; Charset=Big5");
	//header("Pragma: no-cache");
					//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	header("Expires: 0");
	echo $Str."</TABLE>";
	}
