<?php
//$Id: mgr_stu.1.php 8769 2016-01-13 14:16:55Z qfon $
include "config.php";
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
if($_POST[act]=='stu_add') {
	if($_POST[main_id]=='') backe("操作錯誤!");
	if($_POST[stu_id]=='') backe("未選擇學生!按下後回上頁重選!");
	if($_POST[sclass]=='') backe("無班級資料!按下後返回!");
	$sql_check="select id from sport_res  where  sportorder!=0 and mid='$mid' ";
	$rs = $CONN->Execute($sql_check) or die($sql_check);
	if($rs->RecordCount() > 0 )  backe("檢錄過程己經開始，請與大會人員連絡!");
	$mid=$_POST[main_id];$sclass=$_POST[sclass];
		foreach ($_POST[stu_id] as $key => $value) {
			$key=split("_",$key);//記錄表流水號_學生id_姓名 $value為sportnum
			$sql_update= "update sport_res set sportnum='$value' where stud_id ='$key[1]' and mid='$mid' ";
			$rs = $CONN->Execute($sql_update)or die($sql_update);
			}
	$url=$PHP_SELF."?mid=$mid&sclass=$sclass";header("Location:$url");
	}
#####################   資料處理結束  ###########################
#####################  處理新增棒次   #############################
if($_POST[act]=='stu_order') {
	if($_POST[main_id]=='') backe("操作錯誤!");
	if($_POST[Res_id]=='') backe("未選擇學生!按下後回上頁重選!");
	if($_POST[sclass]=='') backe("無班級資料!按下後返回!");
	$mid=$_POST[main_id];$sclass=$_POST[sclass];
/////檢查程序
	$sql_check="select id from sport_res  where   results!='' and mid='$mid' ";
	$rs = $CONN->Execute($sql_check) or die($sql_check);
//	if($rs->RecordCount() > 0 )  backe("檢錄過程己經開始，請與大會人員連絡!");
	foreach ($_POST[Res_id] as $key => $value) {
		$sql_update= "update sport_res set sportorder='$value' where id ='$key' and mid='$mid' and sportkind='5' ";
		$rs = $CONN->Execute($sql_update)or die($sql_update);
		}
	$url=$PHP_SELF."?mid=$mid&sclass=$sclass";header("Location:$url");
	}

#####################  處理新增   #############################
if($_POST[act]=='stu_add2') {
	if($_POST[main_id]=='') backe("操作錯誤!");
	if($_POST[stu_id]=='') backe("未選擇學生!按下後回上頁重選!");
	if($_POST[sclass]=='') backe("無班級資料!按下後返回!");
	if (strlen($_POST[sclass])!=3 )  backe("無班級編號！");
	$mid=$_POST[main_id];$sclass=$_POST[sclass];

		foreach ($_POST[stu_id] as $key => $value) {
			$key=split("_",$key);
			$res_id=$key[0];$item_id=$key[1];
			$sql_2="select id,mid , sportorder from sport_res  where id='$res_id'  ";
			$rs = $CONN->Execute($sql_2) or die($sql_2);
			$arr=$rs->GetArray();
		if ($arr[0][sportorder]=='0') {
				$sql_update= "update sport_res set sportnum='$value' where id ='$res_id' ";
				$rs = $CONN->Execute($sql_update)or die($sql_update);
				}
			else {
			walert($key[2]." 己排入賽程,操作略過！");
			}
		}
	$url=$PHP_SELF."?mid=$mid&sclass=$sclass";header("Location:$url");
	}
#####################  自動填入整班   #############################
if($_POST[act]=='auto_add_class') {
	if($_POST[main_id]=='') backe("操作錯誤!");
	if($_POST[stu_id]=='') backe("未選擇學生!按下後回上頁重選!");
	if($_POST[sclass]=='') backe("無班級資料!按下後返回!");
	if($_POST[auto_add]=='') backe("未填入編號!按下後返回!");
	if (strlen($_POST[sclass])!=3 )  backe("無班級編號！");
	$mid=$_POST[main_id];
	$sclass=$_POST[sclass];
	$auto_add=$_POST[auto_add];
	$sql_2="select  id from sport_res  where mid='$mid' and  idclass like '$sclass%' and  sportorder!=0  ";
	$rs = $CONN->Execute($sql_2) or die($sql_2);
//	if ($rs->RecordCount()!=0)  backe("已經開始檢錄了禁止操作!按下後返回！");
	$sql_2="select id,cname,mid , sportorder from sport_res  where mid='$mid' and idclass like '$sclass%' group by idclass order by idclass ";
	$rs = $CONN->Execute($sql_2) or die($sql_2);
	$arr=$rs->GetArray();
	for($i=0; $i<$rs->RecordCount(); $i++) {
		$sql_update= "update sport_res set sportnum='$auto_add' where id ='".$arr[$i][id]."' ";
		$rs1 = $CONN->Execute($sql_update)or die($sql_update);
		$auto_add++;
		}
	//處理單人單號
	$sql_3="select * from sport_res  where mid='$mid' and idclass like '$sclass%' and sportnum!='' ";
	$rs = $CONN->Execute($sql_3) or die($sql_3);//取到有編號者
	$arr=$rs->GetArray();
	for($i=0; $i<$rs->RecordCount(); $i++) {
		$sql_update= "update sport_res set sportnum='".$arr[$i][sportnum]."' where idclass ='".$arr[$i][idclass]."' and mid='$mid' and sportnum='' ";
		$rs1 = $CONN->Execute($sql_update)or die($sql_update);
		}//end for 
	$url=$PHP_SELF."?mid=$mid&sclass=$sclass";header("Location:$url");
	}
#####################  自動填入整學年   #############################
if($_POST[act]=='auto_add_year') {
	if($_POST[main_id]=='') backe("操作錯誤!");
	if($_POST[stu_id]=='') backe("未選擇學生!按下後回上頁重選!");
	if($_POST[sclass]=='') backe("無班級資料!按下後返回!");
	if($_POST[auto_add]=='') backe("未填入編號!按下後返回!");
	if (strlen($_POST[sclass])!=3 )  backe("無班級編號！");

	$mid=$_POST[main_id];
	$sclass=substr($_POST[sclass],0,1);
	$auto_add=$_POST[auto_add];
	$sql_2="select  sportorder from sport_res  where mid='$mid' and sportorder!=0 and  idclass like '$sclass%' ";
	$rs = $CONN->Execute($sql_2) or die($sql_2);
	if ($rs->RecordCount() !=0 )  backe("已經開始檢錄了禁止操作!按下後返回！");//檢查

	$sql_2="select id,cname,mid , sportorder from sport_res  where mid='$mid' and idclass like '$sclass%' and  kmaster=0 group by idclass order by idclass ";
	$rs = $CONN->Execute($sql_2) or die($sql_2);
	$arr=$rs->GetArray();
	for($i=0; $i<$rs->RecordCount(); $i++) {
				$sql_update= "update sport_res set sportnum='$auto_add' where id ='".$arr[$i][id]."' ";
				$rs1 = $CONN->Execute($sql_update)or die($sql_update);
				$auto_add++;
		}//end for 
	//處理單人單號
	$sql_3="select * from sport_res  where mid='$_POST[main_id]' and idclass like '$sclass%' and sportnum!=''  group by idclass ";
	$rs = $CONN->Execute($sql_3) or die($sql_3);//取到有編號者
	$arr=$rs->GetArray();
	for($i=0; $i<$rs->RecordCount(); $i++) {
		$sql_update= "update sport_res set sportnum='".$arr[$i][sportnum]."' where idclass ='".$arr[$i][idclass]."' and mid='".$_POST[main_id]."' and sportnum='' ";
		$rs1 = $CONN->Execute($sql_update)or die($sql_update);
		}//end for 
		$mid=$_POST[main_id];$sclass=$_POST[sclass];
	$url=$PHP_SELF."?mid=$mid&sclass=$_POST[sclass]";header("Location:$url");
	}
#####################  自動刪除整學年   #############################
if($_POST[act]=='auto_del_year') {
	if($_POST[main_id]=='') backe("操作錯誤!");
	if($_POST[sclass]=='') backe("無班級資料!按下後返回!");
	$mid=$_POST[main_id];
	$sclass=substr($_POST[sclass],0,1);
	$sql_2="select  sportorder from sport_res  where mid='$mid' and sportorder!=0 and  idclass like '$sclass%' ";
	$rs = $CONN->Execute($sql_2) or die($sql_2);
	if ($rs->RecordCount() !=0 )  backe("已經開始檢錄了禁止操作!按下後返回！");//檢查
		$sql_update= "update sport_res set sportnum='' where idclass like '$sclass%' and mid='$mid' ";//and sportnum!='' 
//	 die($sql_2."<BR>".$sql_update);
		$rs1 = $CONN->Execute($sql_update)or die($sql_update);
	$url=$PHP_SELF."?mid=$mid&sclass=$_POST[sclass]";header("Location:$url");
	}
#####################  自動刪除整班   #############################
if($_POST[act]=='auto_del_class') {
	if($_POST[main_id]=='') backe("操作錯誤!");
	if($_POST[sclass]=='') backe("無班級資料!按下後返回!");
	$mid=$_POST[main_id];
	$sclass=$_POST[sclass];
	$sql_2="select  sportorder from sport_res  where mid='$mid' and sportorder!=0 and  idclass like '$sclass%'  ";
	$rs = $CONN->Execute($sql_2) or die($sql_2);
	if ($rs->RecordCount() !=0 )  backe("已經開始檢錄了禁止操作!按下後返回！");//檢查
		$sql_update= "update sport_res set sportnum='' where idclass like '$sclass%' and mid='$mid' ";//and sportnum!='' 
//	 die($sql_2."<BR>".$sql_update);
		$rs1 = $CONN->Execute($sql_update)or die($sql_update);
	$url=$PHP_SELF."?mid=$mid&sclass=$_POST[sclass]";header("Location:$url");
	}
//秀出網頁布景標頭
head("競賽報名");
//print_menu($memu_p,$link2);
include_once "menu.php";
include_once "chk.js";
#####################   選單  ###########################
if($_GET[mid]=='') { print_menu($school_menu_p2);}
else {$link2="mid=$_GET[mid]&item=$_GET[item]&sclass=$_GET[sclass]"; print_menu($school_menu_p2,$link2);}


mmid($_GET[mid]);

if($_GET[mid]!='') echo link_a($_GET[mid],$_GET[sclass]);
if ($_GET[mid]!='' && $_GET[sclass]!=''){
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
echo "<FORM METHOD=POST ACTION='$PHP_SELF' name='f1'>
<INPUT TYPE='hidden' name='main_id' value='$_GET[mid]'>
<INPUT TYPE='hidden' name='act' value=''>
<INPUT TYPE='hidden' name='sclass' value='$_GET[sclass]'>";
 item_list($_GET[mid],$_GET[sclass]);
//if ($class_num!='' && $_GET[mid]!='') stud_list($class_num);
//$color_sex[$arr[$i][stud_sex]]

echo "</FORM>";}


//佈景結尾
foot();
#####################  列示項目   #############################
function item_list($mid,$sclass){
		global $CONN,$sportclass,$sportname,$itemkind;
		$sclass2=substr($sclass,0,1);
//$SQL="select DISTINCT a.idclass,a.id,a.mid,a.stud_id,a.cname,a.sportnum,a.itemid,b.item,b.enterclass from sport_res a,sport_item b where a.idclass like '$sclass%' and a.kmaster=0 and a.itemid=b.id and a.mid='$mid' and b.skind=0 group by a.idclass order by a.idclass ";
$SQL="select DISTINCT a.stud_id,a.idclass,a.id,a.mid,a.cname,a.sportnum,a.itemid,b.item,b.enterclass from sport_res a,sport_item b where a.idclass like '$sclass%' and a.kmaster=0 and a.itemid=b.id and a.mid='$mid' and b.skind=0 group by a.stud_id order by a.sportnum ,a.idclass ";

$rs=$CONN->Execute($SQL) or die($SQL);
$arr=$rs->GetArray();

//echo"<PRE>";print_r($arr);die();
echo "<TABLE border=0 width=100%><TR><TD>
<img src='images/12.gif'><B>填寫運動員編號</B></TD></TR><TR><TD style='font-size:10pt;COLOR:#800000;'>\n";


for($i=0; $i<$rs->RecordCount(); $i++) {
$tmp_str=substr($arr[$i][idclass],1,4)."&nbsp;".$arr[$i][cname]."\n<FONT COLOR='#696969'>(";
$tmp_str.=$sportclass[$arr[$i][enterclass]].$sportname[$arr[$i][item]].")</font>\n";
$tmp_str.="<INPUT TYPE='text' NAME='stu_id[".$arr[$i][id]."_".$arr[$i][stud_id]."]' ";//記錄表流水號_學生id_姓名
$tmp_str.="value='".$arr[$i][sportnum]."' size=5 class=ipmei ";
$tmp_str.="onfocus=\"this.select();return false ;\" onkeydown=\"moveit2(this,event);\">&nbsp;\n";
if ($i%3==2 && $i!=0 ) $tmp_str.="<BR>";
echo $tmp_str;

}
echo "</TD></TR><TR>
<TD style='font-size:10pt;COLOR:#800000;'>
<INPUT TYPE='reset' value='重新填寫' class=bu1>
<INPUT TYPE='button' value='填好送出' onclick=\"bb('確定填好了','stu_add');\" class=bu1><BR>
註：單人報名多項目時，上述名單僅會列出一次。<BR>
<INPUT TYPE='button' value='全班編號刪除' onclick=\"bb('真的嗎？可別後悔！','auto_del_class');\" class=bur>
<INPUT TYPE='button' value='全學年編號刪除' onclick=\"bb('真的嗎？可別後悔！','auto_del_year');\" class=bur>
<BR>
輸入編號：<INPUT TYPE='text' NAME='auto_add' value='' size=6 onfocus=\"this.select();return false ;\" onkeydown=\"moveit2(this,event);\" class=ipmei>
<INPUT TYPE='button' value='由輸入編號開始依座號_自動填入整班' onclick=\"bb('真的嗎？可別後悔！','auto_add_class');\" class=bur>
<INPUT TYPE='button' value='由輸入編號開始依班別座號_自動填入整學年' onclick=\"bb('真的嗎？可別後悔！','auto_add_year');\" class=bur><BR>
<div style='color:#696969;font-size:11pt;'>※自動填入的功能僅能於未檢錄前操作。<BR>
※參賽項目可報多項，但運動員編號僅一個。<BR>
※自動填入整班會由已經報名座號依順序開始逐一填入，同一個人報多項目，也僅會給一個編號。<BR>
※自動填入整學年會由已經報名的班級依班級與座號依順序開始逐一填入，同一個人報多項目，也僅會給一個編號。
</div>
</TD></TR></TABLE>";

/////處理接力賽類棒次///////////
$SQL="select * from sport_item where sportkind='5' and skind=0 and enterclass like '$sclass2%' and mid='$mid' ";
$rs=$CONN->Execute($SQL) or die($SQL);
if ($rs->RecordCount()==0 || strlen($sclass2)!=1 || strlen($sclass)!=3 ) return '';
echo "<table border=0  width=100% style='font-size:11pt;'  cellspacing=1 cellpadding=0 bgcolor=silver>
<tr bgcolor=white><td><img src='images/12.gif'><B style='color:blue'>填寫接力類組別棒次</B><BR>
<INPUT TYPE='reset' value='重新填寫' class=bu1>
<INPUT TYPE='button' value='填好送出' onclick=\"bb('確定填好了','stu_order');\" class=bu1><BR>
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
echo "<BR><BR><BR></td></tr></table><hr color=#800000 SIZE=1>
";
}


function link_a($mid,$sclass){
	$class_name_arr = class_base() ;
//$sql="select left(idclass,3) as aa,COUNT(DISTINCT idclass) as bb from sport_res where mid='$mid' and idclass like '$key%' group by aa";
//求人數
	$sql="select left(idclass,3) as aa,COUNT(id) as bb  from sport_res  where mid='$mid' and idclass like '$key%' group by  aa  ";
	$NUM=initArray("aa,bb",$sql);//求筆數

	$ss="<FORM name=p2>選擇班級：<select name='link2' size='1' class='small' onChange=\"if(document.p2.link2.value!='')change_link(document.p2.link2.value);\"> ";
	foreach($class_name_arr as $key=>$val) {
		($sclass==$key) ? $cc=" selected":$cc="";
		$ss.="<option value='$PHP_SELF?mid=$_GET[mid]&sclass=$key'$cc>$val --".$NUM[$key]."筆</option>\n";
	}
	$ss.="</select><FONT SIZE='-1' COLOR='blue'>※註：單人多項報名亦計算在內。(重複計算)</FONT></FORM>";
Return $ss;
}

?>
