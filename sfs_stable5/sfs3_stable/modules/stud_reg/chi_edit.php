<?php
//$Id: chi_edit.php 6901 2012-09-23 06:20:18Z hami $
/*�ޤJ�ǰȨt�γ]�w��*/

include "stud_reg_config.php";

include "../../include/sfs_case_subjectscore.php";

//�ϥΪ̻{��
sfs_check();

$Sex=array(1=>'�k',2=>'�k');
$Sex_img=array(1=>'<img src=images/boy.gif>',2=>'<img src=images/girl.gif>');

$ThisScriptV=array("stud_sex"=>"�ʧO","stud_name_eng"=>"�^��m�W","bir"=>"�ͤ�","stud_person_id"=>"�������r��","stud_tel_1"=>"���y�q��",
"stud_tel_2"=>"�s���q��","stud_tel_3"=>"��ʹq��","stud_addr_1"=>"���y�a�}","addr_move_in"=>"���y�E�J���","stud_addr_2"=>"�s���a�}","stud_study_year"=>"�J�Ǧ~","enroll_school"=>"�J�ǮɾǮ�","addr_zip"=>"�l���ϸ�","stud_mschool_name"=>"�J�ǫe��p","stud_preschool_name"=>"�J�ǫe���X��","email_pass"=>"Email_Pass");
$ThisScriptV2=array("stud_sex","stud_name_eng","bir","stud_person_id","stud_tel_1","stud_tel_2","stud_tel_3","stud_addr_1","addr_move_in","stud_addr_2","stud_study_year","enroll_school","addr_zip","stud_mschool_name","stud_preschool_name","email_pass");
$T_long=array("stud_sex"=>4,"stud_name_eng"=>20,"bir"=>10,"stud_person_id"=>15,"stud_tel_1"=>20,"stud_tel_2"=>20,"stud_tel_3"=>20,"stud_addr_1"=>40,"addr_move_in"=>12,"stud_addr_2"=>40,"stud_study_year"=>5,"enroll_school"=>30,"addr_zip"=>6,"stud_mschool_name"=>15,"stud_preschool_name"=>15,"email_pass"=>15);


############### ��s���y��� c_curr_seme c_curr_class  ##########################

if ( $_POST[act]=='edit'  && in_array($_POST[update_item],$ThisScriptV2)  ){
	if ( $_POST[update_item]!='bir'){
			foreach($_POST[stud_sn] as $Sn=>$Var) {
				$SQL="update stud_base set $_POST[update_item]='$Var' where student_sn='$Sn' ";
//				echo $SQL."<BR>";
				$rs=$CONN->Execute($SQL) or die($SQL);
			}
			$url=$_SERVER[PHP_SELF]."?c_curr_class=".$_POST[PClass]."&c_curr_seme=".$_POST[PSeme];
			header("Location:$url");
			}
	if ( $_POST[update_item]=='bir'){
			foreach($_POST[stud_sn] as $Sn=>$tmp_Var) {
			$Var=split('-',$tmp_Var);
			$Var[0]=$Var[0]+1911;
			$Var=$Var[0]."-".$Var[1]."-".$Var[2];
			$SQL="update stud_base set stud_birthday ='$Var' where student_sn='$Sn' ";
//			echo $SQL."<BR>";
			$rs=$CONN->Execute($SQL) or die($SQL);
			}
			$url=$_SERVER[PHP_SELF]."?c_curr_class=".$_POST[PClass]."&c_curr_seme=".$_POST[PSeme];
			header("Location:$url");
			}
}//post

############### �ƨ���y��� A_To_B  ##########################
if ($_POST[act]=='A_To_B' && $_POST[kkind]!='' ){
	$Var=split('@@@',$_POST[kkind]);
		foreach($_POST[$Var[0]] as $Sn=>$tmp_Var) {
			if ($tmp_Var=='') continue;
			$SQL="update stud_base set $Var[1] ='$tmp_Var' where student_sn='$Sn' ";
//			echo $SQL."<BR>";
			$rs=$CONN->Execute($SQL) or die($SQL);
		}
			$url=$_SERVER[PHP_SELF]."?c_curr_class=".$_POST[PClass]."&c_curr_seme=".$_POST[PSeme];
			header("Location:$url");
	}
###############   �{���}�l    ###################################

($_GET[c_curr_seme]!='') ? $c_curr_seme=$_GET[c_curr_seme]:$c_curr_seme=sprintf("%03d",curr_year()).curr_seme();//�ثe�Ǵ�//�ثe�Ǧ~
if($_GET[c_curr_class]!='') $c_curr_class=$_GET[c_curr_class];

if($c_curr_class!='') {
	$SS=class_id_2_old($c_curr_class);
	$Sclass=$SS[2];
}

($Sclass) ? $LINK=link_a($c_curr_seme,$c_curr_class): $LINK=link_a($c_curr_seme);

head("���y�s��");
myheader();
$linkstr = "c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme&stud_id=";
print_menu($menu_p,$linkstr);


echo "
<TABLE border=0 width=100% style='font-size:11pt;'  cellspacing=1 cellpadding=0 bgcolor=#9EBCDD>
<TR bgcolor=#9EBCDD><FORM name=p2><TD  nowrap> $LINK
<INPUT TYPE='text' NAME='c_curr_seme' value='$c_curr_seme' size=6 class=ipmei>
<INPUT TYPE='submit' value='��^'>&nbsp;&nbsp;
<img src='images/arrow.gif'  border='0' >
<a href='chi_dom.php?".$linkstr."'>��f��ƾ�Z�s��<img src='images/new.gif'  border='0' ></a>
</TD></TR></FORM></TABLE>";

###############  �^�����  ##########################
if($Sclass!='') {
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function bb(a,b) {
var objform=document.f1;
if (window.confirm(a)){
objform.act.value=b;
objform.submit();}
}
//-->
</SCRIPT>
<?php
	$SQL1="	select right((a.stud_birthday - INTERVAL 1911 YEAR),8) as bir,b.stud_id, b.seme_num, a.stud_name, a.stud_sex, a.stud_name_eng,a.stud_person_id, a.stud_tel_1, a.stud_tel_2, a.stud_tel_3, a.stud_addr_1, a.addr_move_in, a.stud_addr_2 ,a.student_sn  ,a.stud_study_year,a.enroll_school,a.addr_zip ,a.stud_mschool_name,a.stud_preschool_name,a.email_pass from  stud_base a , stud_seme b where  a.student_sn= b.student_sn  and b.seme_year_seme='$c_curr_seme' and b.seme_class='$Sclass'  and a.stud_study_cond=0  order by  b.seme_num ";

	$arr=get_order2($SQL1);

// ;concat(YEAR(a.stud_birthday)-1911, MONTH(a.stud_birthday), DAY(a.stud_birthday))(a.stud_birthday - INTERVAL 1911 YEAR) as bir

###############  ���C��  ##########################
echo "<div style='color:red;font-size:11pt;'>����ܭקﶵ�ءG<FONT SIZE='2' COLOR='#009900'>(�z�i�ϥΤW�U��b�s�׮椤����;�ק�ʧO��..�k1 �k2)�C<BR>�J�Ǧ~��K���~�ɧ�,�H�N�ܧ�,���P��L��ƵL�k���T����,�Фp�ߨϥΡC<BR>
���T���J�Ǧ~�����ꤤ�p�@�ߥH��~�J�Ǭ��D�C
</FONT><BR>";
$yy=0;
foreach($ThisScriptV as $kk1=>$kk2) {
	if ($yy!=0 && ($yy % 8)==7) echo "<BR>";
	($_GET[IT]==$kk1) ? $img="<img src=images/arrow.gif>":$img="<img src=images/closedb.gif>";
	echo $img."<A HREF='$_SERVER[PHP_SELF]?IT=$kk1&c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme&stud_id='>$kk2</A>&nbsp;\n";
	$yy++;
	}//end foreach
	($_GET[act]=='SIT') ? $img="<img src=images/arrow.gif>":$img="<img src=images/closedb.gif>";
	echo $img."<A HREF='$_SERVER[PHP_SELF]?c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme&act=SIT'>�S�O�ާ@</A>&nbsp;<img src=images/closedb.gif><A HREF='$_SERVER[PHP_SELF]?c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme'>��^</A>\n";
echo "</div>";


###############  1.�C�ܸ��  ##########################
if ($_GET[IT]=='' && $_GET[act]=='') {
echo "<TABLE border=0 width=100% style='font-size:10pt;'  cellspacing=1 cellpadding=0 bgcolor=#9EBCDD>
	<TR  bgcolor=#9EBCDD align=center>
	<TD nowrap>�Ǹ�</TD><TD nowrap>�y��</TD>
	<TD nowrap>�m�W</TD><TD nowrap>�ʧO</TD><TD nowrap>�^��m�W</TD>
	<TD nowrap>�X�ͦ~���</TD><TD nowrap>������</TD>
	<TD nowrap>���y�q��</TD><TD nowrap>�s���q��</TD>
	<TD nowrap>��ʹq��</TD><TD nowrap>���y�a�}</TD><TD nowrap>���y�E�J���</TD>
	<TD nowrap>�s���a�}</TD><TD nowrap>�J�ǮɾǮ�</TD></TR>";
for ($i=0;$i<count($arr);$i++) {
	if($arr[$i][addr_move_in]=='0000-00-00') $arr[$i][addr_move_in]='';
echo "<TR bgcolor=white>
<TD>".$arr[$i][stud_id]."</TD>
<TD align='center'>".$arr[$i][seme_num]."</TD><TD nowrap>".$arr[$i][stud_name]."</TD>
<TD align='center'>".$Sex[$arr[$i][stud_sex]]."</TD><TD>".$arr[$i][stud_name_eng]."</TD><TD align='center'>".$arr[$i][bir]."</TD>
<TD>".$arr[$i][stud_person_id]."</TD><TD>".$arr[$i][stud_tel_1]."</TD>
<TD>".$arr[$i][stud_tel_2]."</TD><TD>".$arr[$i][stud_tel_3]."</TD>
<TD>".$arr[$i][stud_addr_1]."</TD><TD align='center'>".$arr[$i][addr_move_in]."</TD><TD>".$arr[$i][stud_addr_2]."</TD><TD>".$arr[$i][enroll_school]."</TD>
</TR>";
	}
	echo "</TABLE>";
}

###############  2.�s�׸��  ##########################
if( array_key_exists($_GET[IT],$ThisScriptV)  && $_GET[act]=='') {
//�s�׸��

moveit2("f1");
//include_once'chi_text.js';
echo "<FORM name='f1'  METHOD=POST ACTION='$_SERVER[PHP_SELF]'>
<INPUT TYPE='hidden' name='act' value=''>
<INPUT TYPE='hidden' name='PSeme' value='$_GET[c_curr_seme]'>
<INPUT TYPE='hidden' name='PClass' value='$_GET[c_curr_class]'>
<INPUT TYPE='hidden' name='update_item' value='$_GET[IT]'>";
echo "<TABLE border=0 width=100% style='font-size:12pt;'  cellspacing=1 cellpadding=0 bgcolor=#9EBCDD>
<TR  bgcolor=#9EBCDD  align=center>
	<TD nowrap width=8%>�y����</TD>
	<TD nowrap width=8%>�Ǹ�</TD>
	<TD nowrap width=6%>�y��</TD>
	<TD nowrap width=10%>�m�W</TD>
	<TD nowrap width=68% align=left><img src=images/arrow.gif><i style='color:blue;'>�ثe�ק��".$ThisScriptV[$_GET[IT]]."</i></TD>
</TR>";
$size=$T_long[$_GET[IT]];
for ($i=0;$i<count($arr);$i++) {

$ED_Item="<INPUT TYPE='text' size=$size  NAME='stud_sn[".$arr[$i][student_sn]."]' value='".$arr[$i][$_GET[IT]]."'  onfocus=\"this.select();return false ;\" onkeydown=\"moveit2(this,event);\"  class=bub>";

echo "<TR bgcolor=white>
<TD>".$arr[$i][student_sn]."</TD>
<TD>".$arr[$i][stud_id]."</TD>
<TD>".$arr[$i][seme_num]."</TD><TD nowrap>".$Sex_img[$arr[$i][stud_sex]].$arr[$i][stud_name]."</TD>
<TD>".$ED_Item."&nbsp;</TD>
</TR>";
	}
//<TD>".$SexV.$birV.$stuIDV.$Tel1V.$Tel2V.$Tel3V.$Addr1V.$Addr2V."</TD>

echo "=<TR bgcolor=white><TD colspan=4 align=center></TD><TD>";
if (modify_flag){
	echo "<INPUT TYPE='reset' value='���s���'><INPUT TYPE=button  value='��n�e�X' onclick=\" bb('�T�w�HOK�H�n�g���Ʈw�F��I','edit');\" >";
}
echo "</TD></TR></form></TABLE>";
}


	} //end if get

###############  3.�S�O�s��  ##########################
if( $_GET[act]=='SIT' ) {

$kk=array(Ta,Tb,Tc);
$kk1=array(Ta=>'stud_tel_1',Tb=>'stud_tel_2',Tc=>'stud_tel_3');
$TEL=array(Ta=>"���y�q��",Tb=>  "�s���q��", Tc=> "��ʹq��");

echo "<TABLE border=0 width=65% style='font-size:14pt;'  cellspacing=1 cellpadding=0 bgcolor=#9EBCDD>
<FORM name=f1  METHOD=POST ACTION='$_SERVER[PHP_SELF]'><TR bgcolor=#9EBCDD >
<TD>�S�O�ާ@
</TD></TR><TR bgcolor=white ><TD style='font-size:13pt;' >\n
<img src=images/arrow.gif>�o���ާ@ �y<b style='COLOR:blue'>�e��</b>�z �|�N �y<b style='COLOR:red'>���</b>�z �����л\�A�Фp�ߨϥΡI</FONT><HR size=1 color=#9EBCDD>\n";
$kk=array(Ta,Tb,Tc);
$kk1=array(Ta=>'stud_tel_1',Tb=>'stud_tel_2',Tc=>'stud_tel_3');
$TEL=array(Ta=>"���y�q��",Tb=>  "�s���q��", Tc=> "��ʹq��");
foreach($kk as $A1) {
	foreach($kk1 as $A2=> $val ) {
		if($A1!=$A2) { 
			$A3=$A1."@@@".$val;
			echo "<INPUT TYPE='radio' NAME='kkind' value='$A3'>�N <FONT COLOR='blue'>$TEL[$A1] </font>  >> �ƻs�� >>  <FONT COLOR='red'>$TEL[$A2] </font> <BR>\n";}
		}
}
echo "<HR size=1 color=#9EBCDD><INPUT TYPE='radio' NAME='kkind' value='Adda@@@stud_addr_2'>�N <FONT COLOR='blue'>���y�a�}</font> >> �ƻs�� >>  <FONT COLOR='red'>�s���a�}</font> <BR>\n";
echo "<INPUT TYPE='radio' NAME='kkind' value='Addb@@@stud_addr_1'>�N <FONT COLOR='blue'>�s���a�}</font> >> �ƻs�� >>  <FONT COLOR='red'>���y�a�}</font> <BR>\n";



for ($i=0;$i<count($arr);$i++) {
	echo "<INPUT TYPE='hidden' name='Ta[".$arr[$i][student_sn]."]' value='".$arr[$i][stud_tel_1]."'>\n";
	echo "<INPUT TYPE='hidden' name='Tb[".$arr[$i][student_sn]."]' value='".$arr[$i][stud_tel_2]."'>\n";
	echo "<INPUT TYPE='hidden' name='Tc[".$arr[$i][student_sn]."]' value='".$arr[$i][stud_tel_3]."'>\n";
	echo "<INPUT TYPE='hidden' name='Adda[".$arr[$i][student_sn]."]' value='".$arr[$i][stud_addr_1]."'>\n";
	echo "<INPUT TYPE='hidden' name='Addb[".$arr[$i][student_sn]."]' value='".$arr[$i][stud_addr_2]."'>\n";
	}

	echo "<INPUT TYPE='reset' value='���]����'>
<INPUT TYPE=button  value='��n�e�X' onclick=\" bb('�T�w�HOK�H���ᮬ�H','A_To_B');\" >
<INPUT TYPE='hidden' name='act' value=''>
<INPUT TYPE='hidden' name='PSeme' value='$_GET[c_curr_seme]'>
<INPUT TYPE='hidden' name='PClass' value='$_GET[c_curr_class]'>
	</TD></TR></form></TABLE>";

}
#####################  ����  ###########################
echo "<BR><BR><FONT SIZE=2 COLOR='blue'>��By ���ƿ��ǰȨt�α��s�p��</FONT>";

foot();

#####################   CSS  ###########################

function myheader(){
?>
<style type="text/css">



body{background-color:#f9f9f9;font-size:12pt}
.ipmei{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:14pt;}
.ipme2{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:14pt;color:red;font-family:�з��� �s�ө���;}
.bu1{border-style: groove;border-width:1px: groove;background-color:#CCCCFF;font-size:12px;Padding-left:0 px;Padding-right:0 px;}
.bub{border-style: groove;border-width:1px: groove;background-color:#FFCCCC;font-size:14pt;}
.bur2{border-style: groove;border-width:1px: groove;background-color:#FFCCCC;font-size:12px;Padding-left:0 px;Padding-right:0 px;}
A:link  {text-decoration:none;color:blue; }
A:visited {text-decoration:none;color:blue; }
A:hover {background-color:rgb(230, 236, 240);color: #000000;text-decoration: underline; }
</style>
<?php
}

#####################   �Z�ſ��  ###########################
function link_a($c_curr_seme,$Sclass=''){
//		global $PHP_SELF;//$CONN,
	$class_name_arr = class_base($c_curr_seme) ;
	$ss="��ܯZ�šG<select name='c_curr_class' size='1' class='small' onChange=\"location.href='$_SERVER[PHP_SELF]?c_curr_seme='+p2.c_curr_seme.value+'&c_curr_class='+this.options[this.selectedIndex].value+'&stud_id=';\">
	<option value=''>�����</option>\n ";
	foreach($class_name_arr as $key=>$val) {
	$key1=substr($c_curr_seme,0,3)."_".substr($c_curr_seme,3,1)."_".sprintf("%02d",substr($key,0,1))."_".substr($key,1,2);
		($Sclass==$key1) ? $cc=" selected":$cc="";
		$ss.="<option value='$key1' $cc>$val </option>\n";
	}
	$ss.="</select>";
Return $ss;
}

##################����ƨ禡###########################
function get_order2($SQL) {
	global $CONN ;
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr = $rs->GetArray();
	return $arr ;
}
?>