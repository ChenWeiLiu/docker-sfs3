<?php
// 載入設定檔
include "stud_move_config.php";
include "../../include/sfs_case_dataarray.php";
// 認證檢查
sfs_check();

############### 更新異動資料 stud_move ##########################
if ( $_POST[act]=='write_move'  && $_POST[move_id]!='' ){
	$update_time=date("Y-m-d H:i:s");
	$SQL="update stud_move set  move_kind='$_POST[move_kind]',move_year_seme='$_POST[move_year_seme]',move_date ='$_POST[move_date]',move_c_unit ='$_POST[move_c_unit]',move_c_date='$_POST[move_c_date]',move_c_word='$_POST[move_c_word]',move_c_num='$_POST[move_c_num]',school='$_POST[school]',update_time='$update_time' , update_id='$_SESSION[session_tea_sn]',update_ip='$_SERVER[REMOTE_ADDR]' where student_sn='$_POST[student_sn]' and move_id='$_POST[move_id]' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$url=$_SERVER[PHP_SELF]."?Seme=".$_POST[Seme]."&Sclass=".$_POST[Sclass]."&St_sn=".$_POST[student_sn];
	header("Location:$url");
	}
############### 刪除異動資料 stud_move ##########################

if ( $_POST[act]=='del_move'  && $_POST[move_id]!='' ){
	$SQL="delete from stud_move where student_sn='$_POST[student_sn]'  and move_id='$_POST[move_id]' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$url=$_SERVER[PHP_SELF]."?Seme=".$_POST[Seme]."&Sclass=".$_POST[Sclass]."&St_sn=".$_POST[student_sn];
	header("Location:$url");
	}
############### 新增異動資料 stud_move ##########################

if ( $_POST[act]=='add_move'  && $_POST[student_sn]!='' && $_POST[stud_id]!='' ){
	if ($_POST[move_kind]=='') backe("您未輸入異動代碼！按下後重來！");
	$update_time=date("Y-m-d H:i:s");
	$SQL="insert into stud_move (stud_id,move_kind,move_year_seme,move_date,move_c_unit,move_c_date,move_c_word,move_c_num,update_time,update_id,update_ip,school,student_sn) values('$_POST[stud_id]','$_POST[move_kind]','$_POST[move_year_seme]',
	'$_POST[move_date]','$_POST[move_c_unit]','$_POST[move_c_date]','$_POST[move_c_word]',
	'$_POST[move_c_num]','$update_time','$_SESSION[session_tea_sn]','$_SERVER[REMOTE_ADDR]','$_POST[school]','$_POST[student_sn]' )";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$url=$_SERVER[PHP_SELF]."?Seme=".$_POST[Seme]."&Sclass=".$_POST[Sclass]."&St_sn=".$_POST[student_sn];
	header("Location:$url");
	}
###############   程式開始    ###################################
($_GET[Seme]!='') ? $Seme=$_GET[Seme]:$Seme=sprintf("%03d",curr_year()).curr_seme();//目前學期//目前學年
if($_GET[Sclass]!='') $Sclass=$_GET[Sclass];
($Sclass) ? $LINK=link_a($Seme,$Sclass): $LINK=link_a($Seme);

head("學籍編修");
//myheader();
$linkstr = "Sclass=$Sclass&Seme=$Seme";
print_menu($student_menu_p);
//print_menu($student_menu_p);
$now_class=split("_",$Sclass);
$now_class=$now_class[1];

echo "
<TABLE border=0 width=100% style='font-size:11pt;'  cellspacing=1 cellpadding=0 bgcolor=#9EBCDD>
<TR bgcolor=#9EBCDD><FORM name=p2><TD  nowrap> $LINK
&nbsp;查詢的學年度&nbsp;<INPUT TYPE='text' NAME='Seme' value='$Seme' size=6 class=ipmei>
<INPUT TYPE='submit' value='返回'>
</TD></TR></FORM></TABLE>";

###############  擷取資料  ##########################
if($Sclass!='') {

	$SQL1="select b.stud_id, b.seme_num, a.stud_name, a.stud_sex,  a.stud_person_id, a.student_sn  ,a.stud_study_year,a.stud_study_cond,a.curr_class_num from  stud_base a , stud_seme b where  a.student_sn= b.student_sn  and b.seme_year_seme='$Seme' and b.seme_class='$now_class'   order by  b.seme_num ";

	$arr_a=get_order2($SQL1);

if ($_GET[St_sn]!=''){
	$SQL1="select * from  stud_move where  student_sn='$_GET[St_sn]'  order by move_year_seme ";
	$arr_b=get_order2($SQL1);//個人異動資料
	$count_b=count($arr_b);
	$SQL1="select * from  stud_base where  student_sn='$_GET[St_sn]' ";
	$arr_c=get_order2($SQL1);//個人基本資料
	$arr_c=$arr_c[0];
	}


// ;concat(YEAR(a.stud_birthday)-1911, MONTH(a.stud_birthday), DAY(a.stud_birthday))(a.stud_birthday - INTERVAL 1911 YEAR) as bir

###############  選單列示  ##########################


$stud_coud=study_cond();//學籍資料代碼1
foreach ($stud_coud as $tk=>$tv){$stud_coud2[$tk]=$tk.'-'.$tv;} //學籍資料代碼2
$now_seme=sprintf("%03d",curr_year()).curr_seme();//目前學期//目前學年
// smarty template 路徑
$template_dir = $SFS_PATH."/".get_store_path()."/templates";
// 使用 smarty tag
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
//學校全銜
$smarty->assign("PHP_SELF",$_SERVER[PHP_SELF]);
$smarty->assign("school_long_name",$school_long_name);
$smarty->assign("now_seme",$now_seme);
$smarty->assign("arr_a",$arr_a);//全班資料
$smarty->assign("arr_b",$arr_b);//個人異動資料
$smarty->assign("count_b",$count_b);//異動筆數
$smarty->assign("arr_c",$arr_c);//個人基本資料
//$smarty->assign("arr_seme_score",$arr_seme_score);


$smarty->assign("template_dir",$template_dir);
$smarty->assign("stud_coud",$stud_coud);
$smarty->assign("stud_coud2",$stud_coud2);
$smarty->display("$template_dir/move_tool.htm");
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
<?
}

#####################   班級選單  ###########################
function link_a($Seme,$Sclass=''){
//		global $PHP_SELF;//$CONN,
	$class_name_arr = class_base($Seme) ;
	$ss="選擇班級：<select name='Sclass' size='1' class='small' onChange=\"location.href='$_SERVER[PHP_SELF]?Seme='+p2.Seme.value+'&Sclass='+this.options[this.selectedIndex].value;\">
	<option value=''>未選擇</option>\n ";
	foreach($class_name_arr as $key=>$val) {
	//$key1=substr($Seme,0,3)."_".substr($Seme,3,1)."_".sprintf("%02d",substr($key,0,1))."_".substr($key,1,2);
	$key1=$Seme."_".$key;
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
function backe($st="未填妥!按下後回上頁重填!") {
echo"<BR><BR><BR><BR><CENTER><form>
	<input type='button' name='b1' value='$st' onclick=\"history.back()\" style='font-size:12pt;color:red'>
	</form></CENTER>";
	exit;
	}
?>
