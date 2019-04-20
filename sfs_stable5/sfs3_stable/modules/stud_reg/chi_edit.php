<?php
//$Id: chi_edit.php 6901 2012-09-23 06:20:18Z hami $
/*引入學務系統設定檔*/

include "stud_reg_config.php";

include "../../include/sfs_case_subjectscore.php";

//使用者認證
sfs_check();

$Sex=array(1=>'男',2=>'女');
$Sex_img=array(1=>'<img src=images/boy.gif>',2=>'<img src=images/girl.gif>');

$ThisScriptV=array("stud_sex"=>"性別","stud_name_eng"=>"英文姓名","bir"=>"生日","stud_person_id"=>"身分証字號","stud_tel_1"=>"戶籍電話",
"stud_tel_2"=>"連絡電話","stud_tel_3"=>"行動電話","stud_addr_1"=>"戶籍地址","addr_move_in"=>"戶籍遷入日期","stud_addr_2"=>"連絡地址","stud_study_year"=>"入學年","enroll_school"=>"入學時學校","addr_zip"=>"郵遞區號","stud_mschool_name"=>"入學前國小","stud_preschool_name"=>"入學前幼稚園","email_pass"=>"Email_Pass");
$ThisScriptV2=array("stud_sex","stud_name_eng","bir","stud_person_id","stud_tel_1","stud_tel_2","stud_tel_3","stud_addr_1","addr_move_in","stud_addr_2","stud_study_year","enroll_school","addr_zip","stud_mschool_name","stud_preschool_name","email_pass");
$T_long=array("stud_sex"=>4,"stud_name_eng"=>20,"bir"=>10,"stud_person_id"=>15,"stud_tel_1"=>20,"stud_tel_2"=>20,"stud_tel_3"=>20,"stud_addr_1"=>40,"addr_move_in"=>12,"stud_addr_2"=>40,"stud_study_year"=>5,"enroll_school"=>30,"addr_zip"=>6,"stud_mschool_name"=>15,"stud_preschool_name"=>15,"email_pass"=>15);


############### 更新學籍資料 c_curr_seme c_curr_class  ##########################

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

############### 複制學籍資料 A_To_B  ##########################
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
###############   程式開始    ###################################

($_GET[c_curr_seme]!='') ? $c_curr_seme=$_GET[c_curr_seme]:$c_curr_seme=sprintf("%03d",curr_year()).curr_seme();//目前學期//目前學年
if($_GET[c_curr_class]!='') $c_curr_class=$_GET[c_curr_class];

if($c_curr_class!='') {
	$SS=class_id_2_old($c_curr_class);
	$Sclass=$SS[2];
}

($Sclass) ? $LINK=link_a($c_curr_seme,$c_curr_class): $LINK=link_a($c_curr_seme);

head("學籍編修");
myheader();
$linkstr = "c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme&stud_id=";
print_menu($menu_p,$linkstr);


echo "
<TABLE border=0 width=100% style='font-size:11pt;'  cellspacing=1 cellpadding=0 bgcolor=#9EBCDD>
<TR bgcolor=#9EBCDD><FORM name=p2><TD  nowrap> $LINK
<INPUT TYPE='text' NAME='c_curr_seme' value='$c_curr_seme' size=6 class=ipmei>
<INPUT TYPE='submit' value='返回'>&nbsp;&nbsp;
<img src='images/arrow.gif'  border='0' >
<a href='chi_dom.php?".$linkstr."'>戶口資料整班編修<img src='images/new.gif'  border='0' ></a>
</TD></TR></FORM></TABLE>";

###############  擷取資料  ##########################
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

###############  選單列示  ##########################
echo "<div style='color:red;font-size:11pt;'>◎選擇修改項目：<FONT SIZE='2' COLOR='#009900'>(您可使用上下鍵在編修格中移動;修改性別時..男1 女2)。<BR>入學年方便錯誤時更正,隨意變更,易致其他資料無法正確索引,請小心使用。<BR>
正確的入學年不分國中小一律以何年入學為主。
</FONT><BR>";
$yy=0;
foreach($ThisScriptV as $kk1=>$kk2) {
	if ($yy!=0 && ($yy % 8)==7) echo "<BR>";
	($_GET[IT]==$kk1) ? $img="<img src=images/arrow.gif>":$img="<img src=images/closedb.gif>";
	echo $img."<A HREF='$_SERVER[PHP_SELF]?IT=$kk1&c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme&stud_id='>$kk2</A>&nbsp;\n";
	$yy++;
	}//end foreach
	($_GET[act]=='SIT') ? $img="<img src=images/arrow.gif>":$img="<img src=images/closedb.gif>";
	echo $img."<A HREF='$_SERVER[PHP_SELF]?c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme&act=SIT'>特別操作</A>&nbsp;<img src=images/closedb.gif><A HREF='$_SERVER[PHP_SELF]?c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme'>返回</A>\n";
echo "</div>";


###############  1.列示資料  ##########################
if ($_GET[IT]=='' && $_GET[act]=='') {
echo "<TABLE border=0 width=100% style='font-size:10pt;'  cellspacing=1 cellpadding=0 bgcolor=#9EBCDD>
	<TR  bgcolor=#9EBCDD align=center>
	<TD nowrap>學號</TD><TD nowrap>座號</TD>
	<TD nowrap>姓名</TD><TD nowrap>性別</TD><TD nowrap>英文姓名</TD>
	<TD nowrap>出生年月日</TD><TD nowrap>身分証</TD>
	<TD nowrap>戶籍電話</TD><TD nowrap>連絡電話</TD>
	<TD nowrap>行動電話</TD><TD nowrap>戶籍地址</TD><TD nowrap>戶籍遷入日期</TD>
	<TD nowrap>連絡地址</TD><TD nowrap>入學時學校</TD></TR>";
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

###############  2.編修資料  ##########################
if( array_key_exists($_GET[IT],$ThisScriptV)  && $_GET[act]=='') {
//編修資料

moveit2("f1");
//include_once'chi_text.js';
echo "<FORM name='f1'  METHOD=POST ACTION='$_SERVER[PHP_SELF]'>
<INPUT TYPE='hidden' name='act' value=''>
<INPUT TYPE='hidden' name='PSeme' value='$_GET[c_curr_seme]'>
<INPUT TYPE='hidden' name='PClass' value='$_GET[c_curr_class]'>
<INPUT TYPE='hidden' name='update_item' value='$_GET[IT]'>";
echo "<TABLE border=0 width=100% style='font-size:12pt;'  cellspacing=1 cellpadding=0 bgcolor=#9EBCDD>
<TR  bgcolor=#9EBCDD  align=center>
	<TD nowrap width=8%>流水號</TD>
	<TD nowrap width=8%>學號</TD>
	<TD nowrap width=6%>座號</TD>
	<TD nowrap width=10%>姓名</TD>
	<TD nowrap width=68% align=left><img src=images/arrow.gif><i style='color:blue;'>目前修改→".$ThisScriptV[$_GET[IT]]."</i></TD>
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
	echo "<INPUT TYPE='reset' value='重新選擇'><INPUT TYPE=button  value='選好送出' onclick=\" bb('確定？OK？要寫到資料庫了喔！','edit');\" >";
}
echo "</TD></TR></form></TABLE>";
}


	} //end if get

###############  3.特別編修  ##########################
if( $_GET[act]=='SIT' ) {

$kk=array(Ta,Tb,Tc);
$kk1=array(Ta=>'stud_tel_1',Tb=>'stud_tel_2',Tc=>'stud_tel_3');
$TEL=array(Ta=>"戶籍電話",Tb=>  "連絡電話", Tc=> "行動電話");

echo "<TABLE border=0 width=65% style='font-size:14pt;'  cellspacing=1 cellpadding=0 bgcolor=#9EBCDD>
<FORM name=f1  METHOD=POST ACTION='$_SERVER[PHP_SELF]'><TR bgcolor=#9EBCDD >
<TD>特別操作
</TD></TR><TR bgcolor=white ><TD style='font-size:13pt;' >\n
<img src=images/arrow.gif>這項操作 『<b style='COLOR:blue'>前者</b>』 會將 『<b style='COLOR:red'>後者</b>』 完全覆蓋，請小心使用！</FONT><HR size=1 color=#9EBCDD>\n";
$kk=array(Ta,Tb,Tc);
$kk1=array(Ta=>'stud_tel_1',Tb=>'stud_tel_2',Tc=>'stud_tel_3');
$TEL=array(Ta=>"戶籍電話",Tb=>  "連絡電話", Tc=> "行動電話");
foreach($kk as $A1) {
	foreach($kk1 as $A2=> $val ) {
		if($A1!=$A2) { 
			$A3=$A1."@@@".$val;
			echo "<INPUT TYPE='radio' NAME='kkind' value='$A3'>將 <FONT COLOR='blue'>$TEL[$A1] </font>  >> 複製到 >>  <FONT COLOR='red'>$TEL[$A2] </font> <BR>\n";}
		}
}
echo "<HR size=1 color=#9EBCDD><INPUT TYPE='radio' NAME='kkind' value='Adda@@@stud_addr_2'>將 <FONT COLOR='blue'>戶籍地址</font> >> 複製到 >>  <FONT COLOR='red'>連絡地址</font> <BR>\n";
echo "<INPUT TYPE='radio' NAME='kkind' value='Addb@@@stud_addr_1'>將 <FONT COLOR='blue'>連絡地址</font> >> 複製到 >>  <FONT COLOR='red'>戶籍地址</font> <BR>\n";



for ($i=0;$i<count($arr);$i++) {
	echo "<INPUT TYPE='hidden' name='Ta[".$arr[$i][student_sn]."]' value='".$arr[$i][stud_tel_1]."'>\n";
	echo "<INPUT TYPE='hidden' name='Tb[".$arr[$i][student_sn]."]' value='".$arr[$i][stud_tel_2]."'>\n";
	echo "<INPUT TYPE='hidden' name='Tc[".$arr[$i][student_sn]."]' value='".$arr[$i][stud_tel_3]."'>\n";
	echo "<INPUT TYPE='hidden' name='Adda[".$arr[$i][student_sn]."]' value='".$arr[$i][stud_addr_1]."'>\n";
	echo "<INPUT TYPE='hidden' name='Addb[".$arr[$i][student_sn]."]' value='".$arr[$i][stud_addr_2]."'>\n";
	}

	echo "<INPUT TYPE='reset' value='重設表單'>
<INPUT TYPE=button  value='選好送出' onclick=\" bb('確定？OK？不後悔？','A_To_B');\" >
<INPUT TYPE='hidden' name='act' value=''>
<INPUT TYPE='hidden' name='PSeme' value='$_GET[c_curr_seme]'>
<INPUT TYPE='hidden' name='PClass' value='$_GET[c_curr_class]'>
	</TD></TR></form></TABLE>";

}
#####################  結尾  ###########################
echo "<BR><BR><FONT SIZE=2 COLOR='blue'>◎By 彰化縣學務系統推廣小組</FONT>";

foot();

#####################   CSS  ###########################

function myheader(){
?>
<style type="text/css">



body{background-color:#f9f9f9;font-size:12pt}
.ipmei{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:14pt;}
.ipme2{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:14pt;color:red;font-family:標楷體 新細明體;}
.bu1{border-style: groove;border-width:1px: groove;background-color:#CCCCFF;font-size:12px;Padding-left:0 px;Padding-right:0 px;}
.bub{border-style: groove;border-width:1px: groove;background-color:#FFCCCC;font-size:14pt;}
.bur2{border-style: groove;border-width:1px: groove;background-color:#FFCCCC;font-size:12px;Padding-left:0 px;Padding-right:0 px;}
A:link  {text-decoration:none;color:blue; }
A:visited {text-decoration:none;color:blue; }
A:hover {background-color:rgb(230, 236, 240);color: #000000;text-decoration: underline; }
</style>
<?php
}

#####################   班級選單  ###########################
function link_a($c_curr_seme,$Sclass=''){
//		global $PHP_SELF;//$CONN,
	$class_name_arr = class_base($c_curr_seme) ;
	$ss="選擇班級：<select name='c_curr_class' size='1' class='small' onChange=\"location.href='$_SERVER[PHP_SELF]?c_curr_seme='+p2.c_curr_seme.value+'&c_curr_class='+this.options[this.selectedIndex].value+'&stud_id=';\">
	<option value=''>未選擇</option>\n ";
	foreach($class_name_arr as $key=>$val) {
	$key1=substr($c_curr_seme,0,3)."_".substr($c_curr_seme,3,1)."_".sprintf("%02d",substr($key,0,1))."_".substr($key,1,2);
		($Sclass==$key1) ? $cc=" selected":$cc="";
		$ss.="<option value='$key1' $cc>$val </option>\n";
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