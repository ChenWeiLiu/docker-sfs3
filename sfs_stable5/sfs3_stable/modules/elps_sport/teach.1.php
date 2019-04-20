<?php
//$Id: teach.1.php 8769 2016-01-13 14:16:55Z qfon $
include "config.php";
//認證
//sfs_check();
unset($class_num);
$SQL="select class_num from teacher_post where  teacher_sn='$_SESSION[session_tea_sn]' ";
$rs=$CONN->Execute($SQL) or die($SQL);
$arr=$rs->GetArray();
$class_num=$arr[0][class_num];
if (!$class_num || $_SESSION[session_tea_sn]=='') {
	head("權限錯誤".$class_num);
	echo "<CENTER><H3>您非級任教師</H3></CENTER>";foot();exit;
	}

if($_GET[mid]!='' || $_POST[mid]!='' ) {
	($_GET[mid] != '') ? $cmid=$_GET[mid]: $cmid=$_POST[mid];
	if(ch_mid($cmid)!='3') backe("非操作時間");
	}
//echo "<PRE>";print_r($_POST);
#####################  處理新增   #############################
if($_POST[act]=='stu_add') {
	if($_POST[mid]=='') backe("操作錯誤!");
	if($_POST[stu_id]=='') backe("未選擇學生!按下後回上頁重選!");
	$sql_check="select id from sport_res  where  sportorder!=0 and mid='$mid' ";
	$rs = $CONN->Execute($sql_check) or die($sql_check);
	if($rs->RecordCount() > 0 )  backe("檢錄過程己經開始，請與大會人員連絡!");
	$mid=$_POST[mid];
		foreach ($_POST[stu_id] as $key => $value) {
			$key=split("_",$key);//記錄表流水號_學生id_姓名 $value為sportnum
//			$res_id=$key[0];$studid=$key[1];$cname=$key[2];
			$sql_update= "update sport_res set sportnum='$value' where stud_id ='$key[1]' and mid='$mid' ";
			//echo $sql_update."<BR>";
			$rs = $CONN->Execute($sql_update)or die($sql_update);
		}
header("Location:$PHP_SELF?mid=$mid");
	}
#####################  處理修改棒次   #############################
if($_POST[act]=='stu_add2') {
	if($_POST[mid]=='') backe("操作錯誤!");
	if($_POST[Res_id]=='') backe("未選擇學生!按下後回上頁重選!");
/////檢查程序
	$sql_check="select id from sport_res  where   results!='' and mid='$_POST[mid]' ";
	$rs = $CONN->Execute($sql_check) or die($sql_check);
	if($rs->RecordCount() > 0 )  backe("檢錄過程己經開始，請與大會人員連絡!");

	foreach ($_POST[Res_id] as $key => $value) {
	$sql_update= "update sport_res set sportorder='$value' where id ='$key' and mid='$_POST[mid]' and  sportkind='5' ";
	//	echo $sql_update."<BR>";
	$rs = $CONN->Execute($sql_update)or die($sql_update);
		}
	header("Location:$PHP_SELF?mid=$_POST[mid]");
	}
#####################   資料處理結束  ###########################


//秀出網頁布景標頭
head("競賽報名");

//print_menu($memu_p,$link2);
include_once "menu.php";
include_once "chk.js";
#####################   選單  ###########################
if($_GET[mid]=='') { print_menu($school_menu_p1);}
else {$link2="mid=$_GET[mid]"; print_menu($school_menu_p1,$link2);}


#####################   列示主要項目  ###########################
echo "<b>※※※注意：須等全校報名完成才可以填寫編號，否則其他班將無法報名。※※※</b><br>";
mmid_t($_GET[mid]);
?>
<script>
<!--
function moveit2(chi,event) {
	var pKey = event.keyCode;//十字鍵 38向上 40向下;37向左;39向右
	if (pKey==40 || pKey==38  ) {
//	if (pKey==40 || pKey==38 || pKey==37 || pKey==39 ) {
	var max=document.f1.elements.length ;//所有元件數量
	var Go=0;//要移動位置
	TText= new Array ; //文字欄位陣列
	var Tin=0; //文字欄位陣列索引
	var Tin_now=0; //文字欄位陣列索引目前位置
	for(i=0; i<max; i++) {
	var obj = document.f1.elements[i];
	if (obj.type == 'text')
	{
	TText[Tin]=i; //記下它在所有元表中的第幾個
if(obj.name==chi.name ) {Tin_now=Tin;} //如果是傳進來的欄位,就記下該欄位在文字欄位陣列索引值
	Tin=Tin+1;
	}
	}
if (Tin==1 ) return false;//僅一個就不要移了
// if (pKey==40 || pKey==39 ) KK=40;
// if (pKey==38 || pKey==37 ) KK=38;
switch (pKey){ //循迴
	case 40://向下
		Tin=Tin-1;//取得索引值
		(Tin_now == Tin ) ? Go=TText[0] : Go=TText[Tin_now+1];
		document.f1.elements[Go].focus();
		return false;
		break;
	case 38://向上
		Tin=Tin-1;//取得索引值
		(Tin_now == 0 ) ? Go=TText[Tin] : Go=TText[(Tin_now-1)];
		document.f1.elements[Go].focus();
		return false;
		break;
		default:
	return false;
	}
	}
}

//-->
</script>
<?php
echo "<FORM METHOD=POST ACTION='$PHP_SELF' name='f1'>\n
<INPUT TYPE='hidden' name='mid' value='$_GET[mid]'>
<INPUT TYPE='hidden' name='act' value=''>";
if ($_GET[mid]!='' && $class_num!='') item_list($_GET[mid],$class_num);
//if ($class_num!='' && $_GET[mid]!='') stud_list($class_num);
//$color_sex[$arr[$i][stud_sex]]

echo "</FORM>";

//佈景結尾
foot();

#####################  列示項目   #############################
function item_list($mid,$sclass){
		global $CONN,$sportclass,$sportname,$itemkind;
		$sclass2=substr($sclass,0,1);
//$SQL="select DISTINCT a.idclass,a.id,a.mid,a.stud_id,a.cname,a.sportnum,a.itemid,b.item,b.enterclass from sport_res a,sport_item b where a.idclass like '$sclass%' and a.kmaster=0 and a.itemid=b.id and a.mid='$mid' and b.skind=0 group by a.idclass order by a.idclass ";
$SQL="select DISTINCT a.stud_id,a.idclass,a.id,a.mid,a.cname,a.sportnum,a.itemid,b.item,b.enterclass from sport_res a,sport_item b where a.idclass like '$sclass%' and a.kmaster=0 and a.itemid=b.id and a.mid='$mid' and b.skind=0 group by a.stud_id order by a.idclass ";
$rs=$CONN->Execute($SQL) or die($SQL);
$arr=$rs->GetArray();
//echo"<PRE>";print_r($arr);die();
echo "<table border=0  width=100% style='font-size:10pt;'  cellspacing=1 cellpadding=0 bgcolor=silver>
<tr bgcolor=white><TD style='font-size:11pt;COLOR:#800000;'>
<img src='images/12.gif'><B style='color:blue'>填寫運動員編號</B><BR>
<INPUT TYPE='reset' value='重新填寫' class=bu1>
<INPUT TYPE='button' value='填好送出' onclick=\"bb('確定填好了','stu_add');\" class=bu1>
註：單人報名多項目時，上述名單僅會列出一次。<BR>\n";
for($i=0; $i<$rs->RecordCount(); $i++) {
$tmp_str=substr($arr[$i][idclass],1,4)."&nbsp;".$arr[$i][cname]."\n<FONT COLOR='#696969'>(";
$tmp_str.=$sportclass[$arr[$i][enterclass]].$sportname[$arr[$i][item]].")</font>\n";
$tmp_str.="<INPUT TYPE='text' NAME='stu_id[".$arr[$i][id]."_".$arr[$i][stud_id]."_".$arr[$i][cname]."]' ";//記錄表流水號_學生id_姓名
$tmp_str.="value='".$arr[$i][sportnum]."' size=5 class=ipmei ";
$tmp_str.="onfocus=\"this.select();return false ;\" onkeydown=\"moveit2(this,event);\">&nbsp;\n";
if ($i%3==2 && $i!=0 ) $tmp_str.="<BR>";
echo $tmp_str;

}
echo "</TD></TR></TABLE>";

/////處理接力賽類棒次///////////
$SQL="select * from sport_item where sportkind='5' and skind=0 and enterclass like '$sclass2%' and mid='$mid' ";
$rs=$CONN->Execute($SQL) or die($SQL);
if ($rs->RecordCount()==0 || strlen($sclass2)!=1 || strlen($sclass)!=3 ) return '';
echo "<table border=0  width=100% style='font-size:11pt;'  cellspacing=1 cellpadding=0 bgcolor=silver>
<tr bgcolor=white><td><img src='images/12.gif'><B style='color:blue'>填寫接力類組別棒次</B><BR>
<INPUT TYPE='reset' value='重新填寫' class=bu1>
<INPUT TYPE='button' value='填好送出' onclick=\"bb('確定填好了','stu_add2');\" class=bu1><BR>
";
$arr=$rs->GetArray();
for($i=0; $i<$rs->RecordCount(); $i++) {
echo "◎".$sportclass[$arr[$i][enterclass]].$sportname[$arr[$i][item]].$itemkind[$arr[$i][kind]]."<BR>";
$SQL1="select id,itemid,kgp,idclass,cname,sportorder,kgp from sport_res where itemid='".$arr[$i][id]."' and kmaster=0 and sportkind='5' and idclass like '$sclass%' order by kgp,sportorder";
$STU=get_order2($SQL1);
for($y=1; $y<=$arr[$i][kgp]; $y++) {
	$tmp1="<FONT COLOR='#696969'><U>".$sclass."班 第".$y."組代表隊</U>::</font>";
	$tmp_str='';
	for ($x=0;$x<count($STU);$x++) {
	if ($STU[$x][kgp]==$y){
		$tmp_str.=substr($STU[$x][idclass],1,4).$STU[$x][cname]."\n<INPUT TYPE='text' NAME='Res_id[".$STU[$x][id]."]' VALUE='".$STU[$x][sportorder]."' size=2 CLASS='ipmei' onfocus=\"this.select();return false ;\" onkeydown=\"moveit2(this,event);\">&nbsp;\n";}
	}//end $x
	echo "<div style='color:#800000;margin-left:10pt;'>".$tmp1."&nbsp;".$tmp_str."</div>";

}//end for $y

} //end for $i
echo "<BR><BR><BR></td></tr></table>";


}

