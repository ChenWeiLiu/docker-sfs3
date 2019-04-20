<?php
//$Id: chc_940622.php 9264 2018-07-02 10:38:47Z chiming $
/*引入學務系統設定檔*/
require "config.php";
//require "../../include/sfs_core_schooldata.php";
//秀出網頁布景標頭
$stud_kind_ary=array("0"=>"一般生","1"=>"特殊生","2"=>"雙胞胎同班","3"=>"雙胞胎不同班");
sfs_check();

if ($_POST[act]=='update' && $_POST[newstud_sn]!='' && $_POST[stud_kind]!='' && $_POST[stud_study_year]!='' ){
		$bao_id = strtoupper($_POST[bao_id]);
	if ($_POST[temp_id]==$bao_id && ($_POST[stud_kind]=='2'|| $_POST[stud_kind]=='3')) backe('您輸入了相同的流水號！');
		$stud_study_year=$_POST[stud_study_year];
	if ( $_POST[stud_kind]!='2'){
		$SQL="select  newstud_sn,temp_id,stud_kind,bao_id from new_stud where newstud_sn='$_POST[newstud_sn]' and stud_study_year='$stud_study_year'  ";
		$rs=$CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$arr=$arr[0];
		if ($arr[stud_kind]=='2' || $arr[stud_kind]=='3'  ) {
			$SQL="update new_stud set  stud_kind ='0',bao_id=''  where  temp_id='".$arr[bao_id]."'  and stud_study_year='$stud_study_year' ";
			$rs=$CONN->Execute($SQL) or die($SQL);
			$SQL="update new_stud set  stud_kind ='$_POST[stud_kind]',bao_id=''  where newstud_sn='$_POST[newstud_sn]' and stud_study_year='$stud_study_year'  ";
			$rs=$CONN->Execute($SQL) or die($SQL);
		} else{
			$SQL="update new_stud set  stud_kind ='$_POST[stud_kind]'  where newstud_sn='$_POST[newstud_sn]'  and stud_study_year='$stud_study_year' ";
			$rs=$CONN->Execute($SQL) or die($SQL);
			}
		}
	if ( $_POST[stud_kind]=='2' || $_POST[stud_kind]=='3' ){
		$kind=$_POST[stud_kind];
		$SQL="select  newstud_sn,temp_id from new_stud where temp_id='$bao_id'  and stud_study_year='$stud_study_year'  ";
		$rs=$CONN->Execute($SQL) or die($SQL);
		if ($rs->RecordCount()!=1) backe('該編號查無相關學生！');
		$arr=$rs->GetArray();
		$arr=$arr[0];
		$SQL="update new_stud set  stud_kind ='$kind',bao_id='$_POST[temp_id]'  where newstud_sn='".$arr[newstud_sn]."'  and stud_study_year='$stud_study_year' ";
		$rs=$CONN->Execute($SQL) or die($SQL);
		$SQL="update new_stud set  stud_kind ='$kind',bao_id='$bao_id'  where newstud_sn='$_POST[newstud_sn]'  and stud_study_year='$stud_study_year' ";
		$rs=$CONN->Execute($SQL) or die($SQL);
		}
	$URL=$_SERVER[PHP_SELF]."?year=".$stud_study_year."&page=".$_POST[page];
	header("location:$URL");
}
head("新生編班");
print_menu($menu_p);
##################陣列列示函式2##########################
// 1.smarty物件
//$template_dir = $SFS_PATH."/".get_store_path()."/templates/";
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
$template_file= $SFS_PATH."/".get_store_path()."/chc_940622.htm";



$SQL="select  stud_study_year from new_stud group by stud_study_year order by stud_study_year desc  ";
$rs=$CONN->Execute($SQL) or die($SQL); 
$year_arr=$rs->GetArray();
$smarty->assign("year_arr",$year_arr);

if ($_GET[year]!=''){
	($_GET[page]=='') ? $page=0:$page=$_GET[page];
	$SQL="select  newstud_sn  from new_stud where stud_study_year='$_GET[year]' and  sure_study='1' ";
	$rs=$CONN->Execute($SQL) or die($SQL); 
	$num=$rs->RecordCount();
	$page_size=50;
	$total= ceil($rs->RecordCount()/$page_size);//總頁數
	$page_link='頁:';
	for ($i=0;$i<$total;$i++){
	($i==$page) ? $page_link.="<b>[<U>".($i+1)."</U>]</b> ":$page_link.="<a href='$_SERVER[PHP_SELF]?year=$_GET[year]&page=$i'>".($i+1)."</a> ";
	}

	$SQL="select * from new_stud where stud_study_year='$_GET[year]' and  sure_study='1' order by temp_id limit ".$page*$page_size.",$page_size ";
	$rs=$CONN->Execute($SQL) or die($SQL); 
	$stu=$rs->GetArray();
	for ($i=0;$i<count($stu);$i++){
		if ($stu[$i][stud_kind]=='') $stu[$i][stud_kind]='0';
	}

	$smarty->assign("LINK",$page_link);//頁數連結字串
	$smarty->assign("page",$page);//目前選取頁數
	$smarty->assign("stu",$stu);//學生資料
	$smarty->assign("stud_kind",$stud_kind_ary);//學生類別
	$smarty->assign("SEX",array(1=>"<FONT COLOR='blue'>男</FONT>",2=>"<FONT  COLOR='red'>女</FONT>"));//學生類別
	$SQL="select temp_id,stud_name  from new_stud where stud_study_year='$_GET[year]' and  sure_study='1' ";
	$rs=$CONN->Execute($SQL) or die($SQL); 
	while ($ro=$rs->FetchNextObject(false)) {
		$temp_id_name[$ro->temp_id]=$ro->stud_name;
		}
	$smarty->assign("temp_id_name",$temp_id_name);//學生姓名
}

$smarty->assign("PHP_SELF",$_SERVER[PHP_SELF]);
$smarty->display($template_file);


foot();

##################回上頁函式1#####################
function backe($st="未填妥!按下後回上頁重填!") {
	echo "<HTML><HEAD><META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=big5\">
<TITLE>$st</TITLE></HEAD><BODY background='images/bg.jpg'>\n";
echo"<BR><BR><BR><BR><CENTER><form>
	<input type='button' name='b1' value='$st' onclick=\"history.back()\" style='font-size:16pt;color:red'>
	</form></CENTER>";
	exit;
	}


