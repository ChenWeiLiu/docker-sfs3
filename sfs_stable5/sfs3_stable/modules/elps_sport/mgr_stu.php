<?php
//$Id: mgr_stu.php 9283 2019-04-01 02:22:30Z chiming $
include_once "config.php";
//認證
sfs_check();


#####################   權限檢查與時間  ###########################
$ad_array=who_is_root();
if (!is_array($ad_array[$_SESSION[session_tea_sn]])){
if ($_POST[mid] || $_GET[mid] || $_POST[main_id] ) {
	$bb='';
	($_POST[mid]!='' ) ? $bb=$_POST[mid]:$bb;
	($_GET[mid]!='' ) ? $bb=$_GET[mid]:$bb;
	($_POST[main_id]!='' ) ? $bb=$_POST[main_id]:$bb;
if (check_man($_SESSION[session_tea_sn],$bb ,1)!='YES'   ) backe("您無權限操作");
}}
if($_GET[mid]!='' || $_POST[mid]!='' ) {
	($_GET[mid] != '') ? $cmid=$_GET[mid]: $cmid=$_POST[mid];
	if(ch_mid_t($cmid)!='3') backe("非操作時間");
	}
#####################  處理新增   #############################
if($_GET[act]=='K5') {
	if($_GET[mid]=='') backe("操作錯誤!");
	if($_GET[sclass]=='') backe("操作錯誤!");
	if($_GET[item]=='') backe("操作錯誤!");
	if($_GET[GP]=='')  backe("操作錯誤!");
/////檢查類別
	$SQL="select * from sport_item where id ='$_GET[item]'  and  mid='$_GET[mid]' ";
	$arr=get_order2($SQL) or die($SQL);//$Iarr=$arr[0];$arr[$i][kgp]
	if ($arr[0][sportkind]!='5')  backe("操作錯誤!");
/////處理新增
	$SQL1="select * from sport_res where itemid ='$_GET[item]' and sportkind='5' and kmaster='2' and idclass='$_GET[sclass]' and kgp='$_GET[GP]' ";
	$rs = $CONN->Execute($SQL1) or die($SQL1);
if ($rs->RecordCount()==0) {
	$in_sql="INSERT INTO sport_res(mid,itemid,kmaster,kgp,sportkind,cname,idclass) VALUES ('$_GET[mid]','$_GET[item]','2','$_GET[GP]','5','$_GET[sclass]','$_GET[sclass]') ";
	$rs = $CONN->Execute($in_sql) or die($in_sql);
	}
	header("Location:$PHP_SELF?mid=$_GET[mid]&sclass=$_GET[sclass]");
}
#####################  移除   #############################
if($_GET[act]=='Del_K5') {
	if($_GET[mid]=='') backe("操作錯誤!");
	if($_GET[sclass]=='' || strlen($_GET[sclass])!=3 ) backe("操作錯誤!");
	if($_GET[item]=='') backe("操作錯誤!");
	if($_GET[GP]=='')  backe("操作錯誤!");
/////檢查類別
	$SQL="select * from sport_item where id ='$_GET[item]'  and  mid='$_GET[mid]' ";
	$arr=get_order2($SQL) or die($SQL);//$Iarr=$arr[0];$arr[$i][kgp]
	if ($arr[0][sportkind]!='5')  backe("操作錯誤!");
/////處理移除
	$del_sql="delete from  sport_res where itemid ='$_GET[item]' and sportkind='5' and idclass like '$_GET[sclass]%' and kgp='$_GET[GP]' ";
	$rs = $CONN->Execute($del_sql) or die($del_sql);
	header("Location:$PHP_SELF?mid=$_GET[mid]&sclass=$_GET[sclass]");
}
#####################  處理新增   #############################
if($_POST['act']=='add_stu' ) {
	if($_POST['main_id']=='') backe("操作錯誤!");
	if($_POST['stu']=='') backe("未選擇學生!按下後回上頁重選!");
	if($_POST['item']=='') backe("未選擇項目!按下後回上頁重選!");
	if($_POST['sclass']=='') backe("無班級編號!");
	$mid=$_POST['main_id'];$sclass=$_POST['sclass'];
	$item=explode("_",$_POST['item']);//項目,類別
//	$sql_check="select id from sport_res  where  sportorder!=0 and mid='$mid' ";
//	$rs = $CONN->Execute($sql_check) or die($sql_check);
//	if($rs->RecordCount() > 0 )  backe("檢錄過程己經開始，請與大會人員連絡!");
//	取消檢查

//////處理隊長
if ($item[0]=='all') {
	$sql_check="select id from sport_res  where  mid='$mid' and  kmaster=1 and idclass like '$sclass%' ";
	$rs = $CONN->Execute($sql_check) or die($sql_check);
	if($rs->RecordCount() > 0 )  backe("隊長僅能有一人！先移除舊的才能增加新的！");
	if (count($_POST[stu]) > 1 )   backe("隊長僅能有一人！您多挑了！");
	foreach ($_POST['stu'] as $tmp=>$cname){
		$stud=explode("_",$tmp);
	}
	
	$in_sql="INSERT INTO sport_res(mid,kmaster,stud_id,cname,idclass,memo) VALUES ('$mid','1','$stud[0]' ,'$cname','$stud[1]','隊長')";
	$rs = $CONN->Execute($in_sql) or die($in_sql);
	header("Location:$PHP_SELF?mid=$mid&sclass=$sclass");
	}

////// 處理接力類
if ($item[1]=='5') {
	foreach ($_POST[stu] as $key => $cname ) {
	$key=explode("_",$key);////stu[[學生編號sn_目前年班座號]
	$sql_2="select id,mid,itemid,idclass from sport_res where stud_id='$key[0]' and itemid='$item[0]' ";
	//重複報名檢查
	$rs = $CONN->Execute($sql_2) or die($sql_2);
if($rs->RecordCount()==0 ) {
	$in_sql="INSERT INTO sport_res (mid,itemid,kgp,stud_id,sportkind,cname,idclass) VALUES ('$mid','$item[0]','$item[2]','$key[0]' ,'$item[1]','$cname','$key[1]')";
	$up_sql="update sport_item set  res=res+1  where id='$item[0]' ";
//	echo $in_sql.$up_sql."<BR>";
	$rs = $CONN->Execute($in_sql) or die($in_sql);
	$rs = $CONN->Execute($up_sql) or die($up_sql);
			}
		else {walert(" $cname 重複報名,操作略過！");}
		}
	header("Location:$PHP_SELF?mid=$mid&sclass=$sclass");
	}

##########一般項目處理 ##########
foreach ($_POST[stu] as $key => $cname ) {
	$key=explode("_",$key);////stu[[學生編號sn_目前年班座號]
	$sql_2="select id,mid,itemid,idclass from sport_res where stud_id='$key[0]' and mid='$mid' and itemid='$item[0]' ";
	//重複報名檢查
	$rs = $CONN->Execute($sql_2) or die($sql_2);
if($rs->RecordCount()==0 ) {
	$in_sql="INSERT INTO sport_res (mid,itemid,stud_id,sportkind,cname,idclass) VALUES ('$mid','$item[0]','$key[0]' ,'$item[1]','$cname','$key[1]')";
	$up_sql="update sport_item set  res=res+1  where id='$item[0]' ";
//	echo $in_sql.$up_sql."<BR>";
	$rs = $CONN->Execute($in_sql) or die($in_sql);
	$rs = $CONN->Execute($up_sql) or die($up_sql);
			}
		else {walert(" $cname 重複報名,操作略過！");}
		}
	$url=$PHP_SELF."?mid=$mid&sclass=$sclass";header("Location:$url");
}
#####################  處理移除   #############################
//del_id[記錄表流水號_項目編號]
if($_POST[act]=='stu_del' ) {
	if($_POST[main_id]=='') backe("操作錯誤!");
	if($_POST[sclass]=='') backe("無班級編號!");
	if(count($_POST[del_id])==0) backe("未選擇學生!按下後回上頁重選!");
	$mid=$_POST[main_id];$sclass=$_POST[sclass];
	$sql_check="select id from sport_res  where  sportorder!=0 and mid='$mid' ";
//	$rs = $CONN->Execute($sql_check) or die($sql_check);
//	if($rs->RecordCount() > 0 )  backe("檢錄過程己經開始，請與大會人員連絡!");
//	取消檢查
	foreach ($_POST[del_id] as $key => $cname) {
		$key=explode("_",$key);
		$del_sql="delete from  sport_res where id='$key[0]' ";
		$rs = $CONN->Execute($del_sql) or die($del_sql);
	if ($key[1]!="all"){
		$up_sql="update sport_item set  res=res-1  where id='$key[1]'";
		$rs = $CONN->Execute($up_sql) or die($up_sql);}
		}
	$url=$PHP_SELF."?mid=$mid&sclass=$sclass";header("Location:$url");
}

//秀出網頁布景標頭
head("競賽報名");

//print_menu($memu_p,$link2);
include_once "menu.php";
include_once "chk.js";

//$tool_bar=&make_menu($school_menu_p);
//	echo $tool_bar;//	print_r($_SESSION);
if($_GET[mid]=='') { print_menu($school_menu_p2);}
else {$link2="mid=$_GET[mid]&item=$_GET[item]&sclass=$_GET[sclass]"; print_menu($school_menu_p2,$link2);}


mmid($_GET[mid]);


	
if($_GET[mid]!='')  echo link_a($_GET[mid],$_GET[sclass]);

// if ($_GET[mid]!='') item_list($mid,$class_num);
if ($_GET[sclass]!='' && $_GET[mid]!='') {
	echo "<FORM METHOD=POST ACTION='$PHP_SELF' name='f1'>\n<INPUT TYPE='hidden' name='main_id' value='$_GET[mid]'><INPUT TYPE='hidden' name='act' value=''><INPUT TYPE='hidden' name='sclass' value='$_GET[sclass]'>";
	item_list($_GET[mid],$_GET[sclass]);
	stud_list($_GET[sclass]);
//$color_sex[$arr[$i][stud_sex]]
echo "</FORM>";

}






//主要內容
$main="";
echo $main;

//佈景結尾
foot();

#####################  列示項目   #############################
function item_list($mid,$class_num){
		global $CONN,$sportclass,$sportname,$itemkind;
	//取項目與報名數
	$class_num_1=substr($class_num,0,1);
	$SQL="select a.* ,count(b.id) as bu  from sport_item a  LEFT JOIN  sport_res b on b.mid='$mid' and b.itemid=a.id and b.idclass like '$class_num%' where  a.mid='$mid' and LOCATE($class_num_1,a.enterclass) > 0 and a.skind=0 and a.sportkind!=5 group by a.id  order by  a.kind, a.enterclass ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	//取全班報名資料
	$SQLb="select *  from  sport_res where  mid='$mid' and  idclass like '$class_num%' and sportkind!=5 ";
	$rs1=$CONN->Execute($SQLb) or die($SQLb);
	$arr1=$rs1->GetArray();
	$Master=get_Master($mid,$class_num);//取得隊長
(is_array($Master)) ? $ma_str="<INPUT TYPE='checkbox' NAME='del_id[".$Master[id]."_all]' value='".$Master[cname]."' >".substr($Master[idclass],3,2).$Master[cname]."\n" :$ma_str='';
echo "<table border=0  width=100% style='font-size:10pt;'  cellspacing=1 cellpadding=0 bgcolor=silver>
<tr bgcolor=white><TD  colspan=2>
<img src='images/12.gif'><B>可參加項目與己報名學生列表</B>
<INPUT TYPE='reset' value='重新選擇' class=bu1>&nbsp;
<INPUT TYPE='button' value='移除己報名的學生' onclick=\"bb('確定移除','stu_del');\" class=bu1></TD></TR>
<tr bgcolor=white><TD style='font-size:10pt;' width=60% >";
echo "<INPUT TYPE='radio' NAME='item' value='all'>隊長：<div style='color:#800000;margin-left:30pt;'>$ma_str</div>";
for($i=0; $i<$rs->RecordCount(); $i++) {
	$tmp_str='';$tmp_str2='';$NN=0;
	$tmp_str= "<INPUT TYPE='radio' NAME='item' value='".$arr[$i][id]."_".$arr[$i][sportkind]."'>".$sportclass[$arr[$i][enterclass]].$sportname[$arr[$i][item]].$itemkind[$arr[$i][kind]]."(<B style='color:blue' >".$arr[$i][bu]."</B>):";
	for($y=0; $y<$rs1->RecordCount(); $y++) {	
		if($arr1[$y][itemid]==$arr[$i][id])  {
			$tmp_str2.= "<INPUT TYPE='checkbox' NAME='del_id[".$arr1[$y][id]."_".$arr[$i][id]."]' value='".$arr1[$y][cname]."' >".substr($arr1[$y][idclass],3,2).$arr1[$y][cname]."&nbsp;\n";//del_id[記錄表流水號_項目編號]
	if ($NN%5==4 && $NN!=0)  $tmp_str2.= "<BR>";
		$NN++;}
	}
	echo $tmp_str."<div style='color:#800000;margin-left:30pt;'>".$tmp_str2."</div>\n";
//	if ($i%5==4 && $i!=0) echo "<BR>";
}
echo "</TD><TD width=40% style='font-size:10pt;' valign=top>";
##########接力類處理
include_once'mgr_stu5.php';
echo "</TD></TR></TABLE>";
}
#####################  列示學生   #############################
function stud_list($class_num) {
		global $CONN;
$SQL="select stud_id,student_sn,stud_name,stud_sex,stud_birthday,stud_blood_type,curr_class_num,stud_study_cond from stud_base where curr_class_num like '$class_num%' and stud_study_cond=0 order by curr_class_num";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
//$color_col=array("#f9f9f9","#F2F2F2");//顏色#e8efe2
//(($i%2)==0) ? $v_color=$color_col[0]:$v_color=$color_col[1];
// stud_sex
$color_sex=array(1=>"blue",2=>"green");//顏色#e8efe2
$img_sex=array(1=>"<img src='images/boy.gif' width=15 >",2=>"<img src='images/girl.gif' width=15>");//顏色#e8efe2

?><TABLE border=0 width=100%><TR><TD width=30%><img src='images/12.gif'><B>本班學生列表</B></TD><TD width=70%><INPUT TYPE='reset' value='重新選擇' class=bu1>&nbsp;
<INPUT TYPE=button  value="將鉤選者加入點選的項目" onclick=" bb('選好了嗎？','add_stu');" class=bu1></TD></TR><TR><TD style='font-size:10pt;' colspan=2>
<FONT COLOR='red'>註：隊長限一人</FONT><BR>
<?php
for($i=0; $i<$rs->RecordCount(); $i++) {
$stu_num=substr($arr[$i][curr_class_num],3,2);
echo "<INPUT TYPE='checkbox' NAME='stu[".$arr[$i][student_sn]."_".$arr[$i][curr_class_num]."]' value='".$arr[$i][stud_name]."'><FONT  COLOR='".$color_sex[$arr[$i][stud_sex]]."'>".$img_sex[$arr[$i][stud_sex]].$stu_num.$arr[$i][stud_name]."</FONT>\n";
//stu[[學生編號sn_目前年班座號]
if ($i%8==7 && $i!=0) echo "<BR>";
}

echo "</TD></TR><TR><TD colspan=2>";
?>

<?php
echo "<hr color=#800000 SIZE=1></TD></TR></TABLE>";

}
#####################   班級選單  ###########################
function link_a($mid,$sclass){
	$class_name_arr = class_base() ;
//	$sql="select left(idclass,3) as aa,COUNT(DISTINCT idclass) as bb  from sport_res  where mid='$mid' and idclass like '$key%' group by  aa  ";//求人數
	$sql="select left(idclass,3) as aa,COUNT(id) as bb  from sport_res  where  kmaster!=2 and  mid='$mid' and idclass like '$key%' group by  aa  ";//求筆數
	$NUM=initArray("aa,bb",$sql);
	$ss="<FORM name=p2>選擇班級：<select name='link2' size='1' class='small' onChange=\"if(document.p2.link2.value!='')change_link(document.p2.link2.value);\"> ";
	foreach($class_name_arr as $key=>$val) {
		($sclass==$key) ? $cc=" selected":$cc="";
		$ss.="<option value='$PHP_SELF?mid=$_GET[mid]&sclass=$key'$cc>$val --".$NUM[$key]."筆</option>\n";
	}
	$ss.="</select><FONT SIZE='-1' COLOR='blue'>※註：單人多項報名亦計算在內。(重複計算)</FONT></FORM>";
Return $ss;
}

