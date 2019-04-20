<?php
//$Id: mgr_order.php 8769 2016-01-13 14:16:55Z qfon $
include "config.php";
//認證
sfs_check();
//if ($_POST){
//	echo "<PRE>";print_r($_POST);print_r($_GET);echo "</PRE>";
//	die();
//	}

#####################   權限檢查與時間  ###########################
if($_GET[mid] || $_POST[mid] ) {
	($_GET[mid] == '') ? $cmid=$_POST[mid]: $cmid=$_GET[mid];
	if(ch_mid_t($cmid)!=3 ) backe("非操作時間");
	}
$ad_array=who_is_root();
if (!is_array($ad_array[$_SESSION[session_tea_sn]])){
if ($_POST[mid] || $_GET[mid] || $_POST[main_id] ) {
	$bb='';
	($_POST[mid]!='' ) ? $bb=$_POST[mid]:$bb;
	($_GET[mid]!='' ) ? $bb=$_GET[mid]:$bb;
	($_POST[main_id]!='' ) ? $bb=$_POST[main_id]:$bb;
if (check_man($_SESSION[session_tea_sn],$bb ,1)!='YES'   ) backe("您無權限操作");
	if(ch_mid_t($bb)!=3) backe("非操作時間");

}}


#####################  分組加入   #############################
if($_POST[act]=='act_select' ) {
	if($_POST[mid]=='') backe("操作錯誤!");
	if($_POST[item]=='') backe("操作錯誤!");
	if($_POST[astu]=='') backe("未選擇學生!按下後回上頁重選!");
	$SQL="select id from sport_res  where itemid ='$_POST[item]' and mid='$_POST[mid]' and results != '' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
if ($rs->RecordCount() > 0 )  backe("該項目己開始輸入成績了!禁止重排！");
	$all_stu=count($_POST[astu]);
	$LimtA=($_POST[spk]-1)*$_POST[item_limt_man];
	$La=$LimtA+1;
	$Lb=($_POST[spk]*$_POST[item_limt_man]);
	if ($all_stu!= $_POST[item_limt_man])  $Lb=$La+$all_stu-1;
	$rrr=range($La,$Lb);
if ($_POST[act_k]=='1') shuffle($rrr);//選擇亂數分組
	$i=0;
	foreach ($_POST[astu] as $key => $cname) {
		$kk=split("_",$key);
	$SQL="update sport_res set sportorder='".$rrr[$i]."'  where id='".$kk[0]."' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
//echo $SQL."<BR>";
		$i++;
		}
//	gonow($PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item]);exit;
	$url=$PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item];header("Location:$url");
}
#####################  分組加入2單組重排   #############################
if($_POST[act]=='act_select2' ) {
	if($_POST[mid]=='') backe("操作錯誤!");
	if($_POST[item]=='') backe("操作錯誤!");
	if($_POST[spk]=='') backe("未選擇第幾組!按下後回上頁重選!");
	if($_POST[item_limt_man]=='') backe("操作錯誤!沒有該組人數!");
	$SQL="select id from sport_res  where itemid ='$_POST[item]' and mid='$_POST[mid]' and results != '' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
if ($rs->RecordCount() > 0 )  backe("該項目己開始輸入成績了!禁止重排！");
	$LimtA=($_POST[spk]-1)*$_POST[item_limt_man];
	$La=$LimtA+1;
	$Lb=($_POST[spk]*$_POST[item_limt_man]);
	$SQL="select id from sport_res  where itemid ='$_POST[item]' and mid='$_POST[mid]' and sportorder >= '$La' and  sportorder <= '$Lb' order by sportorder ";
//	die($SQL);
	$rs=$CONN->Execute($SQL) or die($SQL);
if ($rs->RecordCount() < $_POST[item_limt_man] ) $Lb=$La+$rs->RecordCount()-1;
	$rrr=range($La,$Lb);
	shuffle($rrr);//選擇亂數分組
	$arr=$rs->GetArray();
	for($i=0; $i<$rs->RecordCount(); $i++) {
		$SQL1="update sport_res set sportorder='".$rrr[$i]."'  where id = '".$arr[$i][id]."' ";
		$rsa=$CONN->Execute($SQL1) or die($SQL1);
		}
//	gonow($PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item]);exit;
	$url=$PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item];header("Location:$url");
}

#####################  分組加入   #############################
if($_POST[act]=='del_select' ) {
	if($_POST[mid]=='') backe("操作錯誤!");
	if($_POST[item]=='') backe("操作錯誤!");
	if($_POST[dstu]=='') backe("未選擇學生!按下後回上頁重選!");
	$SQL="select id from sport_res  where itemid ='$_POST[item]' and mid='$_POST[mid]' and results != '' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
if ($rs->RecordCount() > 0 )  backe("該項目己開始輸入成績了!禁止重排！");
	foreach ($_POST[dstu] as $key => $cname) {
		$kk=split("_",$key);
		$SQL="update sport_res set sportorder='0'  where id='".$kk[0]."' ";
		$rs=$CONN->Execute($SQL) or die($SQL);
		}
//	gonow($PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item]);exit;
	$url=$PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item];header("Location:$url");
}

#####################  處理移除   #############################
if($_POST[act]=='del_all' ) {
	if($_POST[mid]=='') backe("操作錯誤!");
	if($_POST[item]=='') backe("操作錯誤!");
	$SQL="select id from sport_res  where itemid ='$_POST[item]' and mid='$_POST[mid]' and results != '' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
if ($rs->RecordCount() > 0 )  backe("該項目己開始輸入成績了!禁止重排！");
	$SQL="update sport_res set sportorder=0  where  itemid ='$_POST[item]' and mid='$_POST[mid]'  ";
	$rs=$CONN->Execute($SQL) or die($SQL);
//	gonow($PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item]);exit;
	$url=$PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item];header("Location:$url");
}

#####################  處理依運動員編號編組   #############################
if($_POST[act]=='act_sportnum' ) {
	if($_POST[mid]=='') backe("操作錯誤!");
	if($_POST[item]=='') backe("操作錯誤!");
	$SQL="select id from sport_res  where itemid ='$_POST[item]' and mid='$_POST[mid]' and results != '' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
if ($rs->RecordCount() > 0 )  backe("該項目己開始輸入成績了!禁止重排！");
	$SQL="select id from sport_res  where itemid ='$_POST[item]' and mid='$_POST[mid]'  order by  sportnum ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	for($i=0; $i<$rs->RecordCount(); $i++) {
		$y=$i+1;
	$SQL1="update sport_res set sportorder='$y'  where id = '".$arr[$i][id]."' ";
	$rsa=$CONN->Execute($SQL1) or die($SQL1);
	}
//	gonow($PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item]);exit;
	$url=$PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item];header("Location:$url");

}

#####################  處理依班級座號編組   #############################
if($_POST[act]=='act_idclass' ) {
	if($_POST[mid]=='') backe("操作錯誤!");
	if($_POST[item]=='') backe("操作錯誤!");
	$SQL="select id from sport_res  where itemid ='$_POST[item]' and mid='$_POST[mid]' and results != '' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
if ($rs->RecordCount() > 0 )  backe("該項目己開始輸入成績了!禁止重排！");
	$SQL="select id from sport_res  where itemid ='$_POST[item]' and mid='$_POST[mid]'  order by  idclass ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	for($i=0; $i<$rs->RecordCount(); $i++) {
		$y=$i+1;
	$SQL1="update sport_res set sportorder='$y'  where id = '".$arr[$i][id]."' ";
	$rsa=$CONN->Execute($SQL1) or die($SQL1);
	}
//	gonow($PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item]);exit;
	$url=$PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item];header("Location:$url");
}

#####################  電腦亂數編組   #############################
if($_POST[act]=='act_computer' ) {
	if($_POST[mid]=='') backe("操作錯誤!");
	if($_POST[item]=='') backe("操作錯誤!");
	$SQL="select id from sport_res  where itemid ='$_POST[item]' and mid='$_POST[mid]' and results != '' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
if ($rs->RecordCount() > 0 )  backe("該項目己開始輸入成績了!禁止重排！");
	$SQL="select id from sport_res  where itemid ='$_POST[item]' and mid='$_POST[mid]'  order by  idclass ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$rrr=range(1,$rs->RecordCount());
	shuffle($rrr);
	for($i=0; $i<$rs->RecordCount(); $i++) {
	$SQL1="update sport_res set sportorder='".$rrr[$i]."'  where id = '".$arr[$i][id]."' ";
	$rsa=$CONN->Execute($SQL1) or die($SQL1);
//echo $SQL1."<BR>";
	}
//	gonow($PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item]);exit;
	$url=$PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item];header("Location:$url");
}

#####################  電腦亂數編組--對拆   #############################
if($_POST[act]=='act_computer2' ) {
	if($_POST[mid]=='') backe("操作錯誤!");
	if($_POST[item]=='') backe("操作錯誤!");
	$SQL="select id from sport_res  where itemid ='$_POST[item]' and mid='$_POST[mid]' and results != '' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
if ($rs->RecordCount() > 0 )  backe("該項目己開始輸入成績了!禁止重排！");
	$SQL="select id from sport_res  where itemid ='$_POST[item]' and mid='$_POST[mid]' order by  idclass ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$rrr=range(1,$rs->RecordCount());
for ($i=1;$i<=$rs->RecordCount();$i++){
	($i%2==0) ? $BBB[]=$i:$i;
	($i%2==1) ? $AAA[]=$i:$i;
	}
	shuffle($AAA);shuffle($BBB);// echo"<PRE>";
	//print_r($AAA); print_r($BBB);

for($i=0; $i<$rs->RecordCount(); $i++) {
if ($i%2==0){
	$SQL1="update sport_res set sportorder='".$AAA[floor($i/2)]."'  where id = '".$arr[$i][id]."' ";
	$rsa=$CONN->Execute($SQL1) or die($SQL1);
	}
if ($i%2==1){
	$SQL1="update sport_res set sportorder='".$BBB[floor($i/2)]."'  where id = '".$arr[$i][id]."' ";
	$rsa=$CONN->Execute($SQL1) or die($SQL1);
	}
	}
	//gonow($PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item]);exit;
	$url=$PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item];header("Location:$url");
}
#####################  電腦亂數編組--三拆   #############################
if($_POST[act]=='act_computer3' ) {
	if($_POST[mid]=='') backe("操作錯誤!");
	if($_POST[item]=='') backe("操作錯誤!");
	$SQL="select id from sport_res  where itemid ='$_POST[item]' and mid='$_POST[mid]' and results != '' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
if ($rs->RecordCount() > 0 )  backe("該項目己開始輸入成績了!禁止重排！");
	$SQL="select id from sport_res  where itemid ='$_POST[item]' and mid='$_POST[mid]' order by  idclass ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
for ($i=1;$i<=$rs->RecordCount();$i++){
	($i%3==0) ? $CCC[]=$i:$i;
	($i%3==2) ? $BBB[]=$i:$i;
	($i%3==1) ? $AAA[]=$i:$i;
	}
	shuffle($AAA);shuffle($BBB);shuffle($CCC);
// echo"<PRE>"; print_r($AAA); print_r($BBB); print_r($CCC);
for($i=0; $i<$rs->RecordCount(); $i++) {
if ($i%3==0){
	$SQL1="update sport_res set sportorder='".$AAA[floor($i/3)]."'  where id = '".$arr[$i][id]."' ";
//	echo $SQL1."__".(floor($i/3))."--AAA<BR>";
	$rsa=$CONN->Execute($SQL1) or die($SQL1);
	}
if ($i%3==1){
	$SQL1="update sport_res set sportorder='".$BBB[floor($i/3)]."'  where id = '".$arr[$i][id]."' ";
//	echo $SQL1."__".(floor($i/3))."--BBB<BR>";
	$rsa=$CONN->Execute($SQL1) or die($SQL1);
	}
if ($i%3==2){
	$SQL1="update sport_res set sportorder='".$CCC[floor($i/3)]."'  where id = '".$arr[$i][id]."' ";
//	echo $SQL1."__".(floor($i/3))."--CCC<BR>";
	$rsa=$CONN->Execute($SQL1) or die($SQL1);
		}
	}
//	gonow($PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item]);exit;
	$url=$PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item];header("Location:$url");
}

#####################  分組加入   #############################
if($_POST[act]=='act_text_me' ) {
	if($_POST[mid]=='') backe("操作錯誤!");
	if($_POST[item]=='') backe("操作錯誤!");
	if($_POST[chstu]=='') backe("未選擇學生!按下後回上頁重選!");
	$SQL="select id from sport_res  where itemid ='$_POST[item]' and mid='$_POST[mid]' and results != '' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
if ($rs->RecordCount() > 0 )  backe("該項目己開始輸入成績了!禁止重排！");
	foreach ($_POST[chstu] as $key => $value) {
		$kk=split("_",$key);
	$SQL="update sport_res set sportorder='$value'  where id='".$kk[0]."' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
//	echo $SQL."<BR>";
		}
//	gonow($PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item]);exit;
$url=$PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item];header("Location:$url");
}


//秀出網頁布景標頭
head("競賽報名");

include_once "menu.php";
include_once "chk.js";

if($_GET[mid]=='') { print_menu($school_menu_p2);}
else {$link2="mid=$_GET[mid]&item=$_GET[item]&sclass=$_GET[sclass]"; print_menu($school_menu_p2,$link2);}

mmid($_GET[mid]);



//echo "<FORM METHOD=POST ACTION='$PHP_SELF' name='f1'>\n<INPUT TYPE='hidden' name='act' value=''>";
if ($_GET[mid]!='') echo item_list($_GET[mid],$_GET[item]);
if ($_GET[item]!='') stud_list($_GET[mid],$_GET[item]);
// if ($class_num!='' && $_GET[mid]!='') stud_list($class_num);
//$color_sex[$arr[$i][stud_sex]]

//echo "</FORM>";









//佈景結尾
foot();

#####################  列示學生   #############################
function stud_list($mid,$item) {
		global $CONN,$sportname,$itemkind,$sportclass;
	$SQL="select * from sport_item where id ='$item' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$item_arr=$rs->GetArray();
	if ($item_arr[0][sportkind]==5) return'';//非接力賽結束
	$limit=$item_arr[0][playera];
	$ITEM_NAME=$sportclass[$item_arr[0][enterclass]].$sportname[$item_arr[0][item]].$itemkind[$item_arr[0][kind]];
	$SQL="select * from sport_res where itemid ='$item' and mid='$mid' and kmaster=0 order by sportorder  ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$all_col=ceil($rs->RecordCount()/$item_arr[0][playera]);

//$color_col=array("#f9f9f9","#F2F2F2");//顏色#e8efe2
//(($i%2)==0) ? $v_color=$color_col[0]:$v_color=$color_col[1];
// stud_sex
$color_sex=array(1=>"blue",2=>"green");//顏色#e8efe2
$img_sex=array(1=>"<img src='images/boy.gif' width=15 >",2=>"<img src='images/girl.gif' width=15>");//顏色#e8efe2
?>
<script>
<!--
function tagall() {
var i =0;
while (i < document.f1.elements.length) {
var obj=document.f1.elements[i];
var objx=document.f1.elements[i].name;
if (obj.type=='checkbox' && objx.substr(0,4)=='astu') {
	if (obj.checked==1 ) {obj.checked=0;}
	else { obj.checked=1;}
	}
i++;}
}

function chk_sel() {

var radio1=0;//第1項是否選擇
var spk_value=0;//第1項的選擇值
var radio2=0;//第2項是否選擇
var checkbox1=0;//己選擇數
var check_all=<?=$rs->RecordCount()?>;//全部人數
var check_one=<?=$limit?>;//每組人數
var check_col=<?=$all_col?>;//分組數
var i =0;
while (i < document.f1.elements.length) {
var obj=document.f1.elements[i];
var objx=document.f1.elements[i].name;
if (obj.type=='radio' && objx=='spk') {
	if(obj.checked==1 ) { radio1=1;spk_value=obj.value;}
	}
if (obj.type=='radio' && objx=='act_k') {
	(obj.checked==1 ) ? radio2=1:radio2;
	}
if (obj.type=='checkbox' && objx.substr(0,4)=='astu') {
	(obj.checked==1 ) ? checkbox1++:checkbox1;
	}
	i++;
	}

if (radio1!=1 || radio2!=1 ) {
	alert("未選妥，檢查一下！請選擇亂數排序or依班別");
	return false;
	}
//	if (spk_value!=check_col ){
//	if ( checkbox1!=check_one && checkbox1!=(check_one-1) && checkbox1!=(check_one-2)){
//	alert("人數不對！應選"+check_one+"人 \n\n 您選了"+checkbox1+"人！");//人數過少限制
//	return false;}
//	}
//	AA=check_all-(check_one*(check_col-1));
//if (spk_value==check_col ){
//	if (AA!= checkbox1 &&  checkbox1!=(check_one-1) && checkbox1!=(check_one-2)) {
//		alert("人數不對！再選"+AA+"人就好了 \n\n 可是您選了"+checkbox1+"人！");
//		return false;}
//	}

	var objform=document.f1;
	if (window.confirm("選擇完畢？本程式己不作人數的確定，請自行確定人數！")){
		objform.act.value="act_select";
		objform.submit();
		}
}

function bb2(a,b,c) {
var objform=document.f1;
if (window.confirm(c)){
objform.act.value=a;
//objform.spk.value=b;
objform.submit();}
}

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
function showtalk() {
	alert("您用的這個選項，將忽略您編號空缺的現象！");
}
function showtalk1() {
	alert("您用的這個選項，編號重複將僅取其一的學生！\n\n而道次的出現全依您填寫的編號而定！");
}

//-->
</script>
<TABLE border=0 width=100%><FORM METHOD=POST ACTION='<?=$PHP_SELF?>' name='f1'><TR><TD colspan=2><img src='images/12.gif'><B><?=$ITEM_NAME?>&nbsp;參賽學生分組列表</B>
</TD></TR>
<TR><TD colspan=2><img src='images/12.gif'><B>項目資訊</B>
己報名<?=$rs->RecordCount()?>人，每組<?=$limit?>人，取<?=$item_arr[0][passera]?>名，
可分為<?=$all_col?>組。<BR><INPUT TYPE='button' value='使用跳高類表格印出本項目' onclick="window.open('mgr_prt.1.php?mid=<?=$mid?>&item=<?=$item?>&Spk=all&kitem=heigh','','scrollbars=yes,resizable=yes,height=500,width=600');" class=bu1>&nbsp;
<INPUT TYPE='button' value='使用跳遠類表格印出本項目' onclick="window.open('mgr_prt.1.php?mid=<?=$mid?>&item=<?=$item?>&Spk=all&kitem=long','','scrollbars=yes,resizable=yes,height=500,width=600');" class=bu1>

</TD></TR>
<TR><TD style='font-size:10pt;' width=80% valign=top>
<?php
for ($a=1;$a<=$all_col ;$a++){
	$tmp_str='';$y=0;
	$LimtA=($a-1)*$limit;
	$La=$LimtA+1;
	$LimtB=($a*$limit)+1;
	$Lb=$LimtB-1;
	for($i=0; $i<$rs->RecordCount(); $i++) {
	($arr[$i][results]!=0 || $arr[$i][results]!='' ) ? $dd=" disabled":$dd='' ;
	if ( $arr[$i][sportorder] > $LimtA && $arr[$i][sportorder] < $LimtB ) {
		if ($_GET[txt]=='open') {
			$tmp_str.="<INPUT TYPE='text' NAME='chstu[".$arr[$i][id]."_".$item."]' size=3 value='".$arr[$i][sportorder]."' $dd onfocus=\"this.select();return false ;\" onkeydown=\"moveit2(this,event);\" class=bur>".$arr[$i][cname]."(<B style='color:red'>".substr($arr[$i][idclass],1,4)."</B>)\n";
			}
		else {
			$tmp_str.="<INPUT TYPE='checkbox' NAME='dstu[".$arr[$i][id]."_".$item."]' value='".$arr[$i][cname]."' $dd><B style='color:blue'>".$arr[$i][sportorder]."</B>_".$arr[$i][cname]."(<B style='color:red'>".substr($arr[$i][idclass],1,4)."</B>)\n";
			}
		
		($y%4==3 && $y!=0 ) ? $tmp_str.="<BR>": $tmp_str;
		$y++;}
		}
	$echo_STR.= "<INPUT TYPE='radio' NAME='spk' value='$a'><B style='color:blue'>□第 $a 組</B>&nbsp;
	($La - $Lb)&nbsp;<INPUT TYPE='button' value='印出第 $a 組檢錄單' onclick=\"window.open('mgr_prt.1.php?mid=$mid&item=$item&Spk=$a&kitem=speed','','scrollbars=yes,resizable=yes,height=500,width=600');\" class=bu1>
	<INPUT TYPE='button' value='非制式 $a 組' onclick=\"showtalk();window.open('mgr_prt_new.php?mid=$mid&item=$item&Spk=$a&kitem=speed&ord=na','','scrollbars=yes,resizable=yes,height=500,width=600');\" class=bu1>
	<INPUT TYPE='button' value='依編號式 $a 組' onclick=\"showtalk1();window.open('mgr_prt_new.php?mid=$mid&item=$item&Spk=$a&kitem=speed&ord=local','','scrollbars=yes,resizable=yes,height=500,width=600');\" class=bu1>
	<div style='margin-left:10pt'>".$tmp_str."</div>";
	}
echo "<table border=0  width=100% style='font-size:11pt;'  cellspacing=0 cellpadding=0 bgcolor=silver>
<tr bgcolor=white><td><fieldset><legend><img src='images/pin_orange.gif'><B>己分組選手名單</B></legend>".
$echo_STR."</fieldset></td></tr></table>";

($_GET[txt]=='open') ? $txt_tb="<INPUT TYPE=button  value='依我填寫的編號送出' onclick=\" bb('依我填寫的編號送出？真的？','act_text_me');\" class=bu1> <INPUT TYPE=button  value='取消返回' onclick=\"self.history.back();\" class=bu1>":$txt_tb="<INPUT TYPE=button  value='自行調整分組' onclick=\"location.href='$PHP_SELF?mid=$mid&item=$item&txt=open';\" class=bu1>";

echo "<table border=0  width=100% style='font-size:10pt;'  cellspacing=0 cellpadding=0 bgcolor=silver>
<tr bgcolor=white><td><fieldset><legend><img src='images/win.gif'><B>操作選項</B></legend>
<INPUT TYPE='hidden' name='mid' value='$mid'>
<INPUT TYPE='hidden' name='item' value='$item'>
<INPUT TYPE='hidden' name='act' value=''>
<INPUT TYPE='hidden' name='item_limt_man' value='$limit'>
<INPUT TYPE='radio' NAME='act_k' value='1'>亂數排序
<INPUT TYPE='radio' NAME='act_k' value='2'>依班別<BR>

<INPUT TYPE='reset' value='重新選擇' class=bu1>&nbsp;
<INPUT TYPE=button  value='所有分組取消' onclick=\" bb('所有分組取消？真的？','del_all');\" class=bu1>&nbsp;
 $txt_tb

<BR>
<INPUT TYPE=button  value='將選擇的組↑重排' onclick=\" bb('選擇的組編號亂數重排？真的？','act_select2');\" class=bu1>&nbsp;
<INPUT TYPE=button  value='將↓鉤選者加入選擇的分組↑' onclick=\" chk_sel();\" class=bu1>&nbsp;
<INPUT TYPE=button  value='將↑鉤選者分組取消' onclick=\" bb('選擇的組取消編號？真的？','del_select');\" class=bu1>&nbsp;
<BR>
<INPUT TYPE=button  value='全部由電腦全權負責分組' onclick=\" bb('由電腦負責分組？全部？','act_computer');\" class=bur>&nbsp;
<INPUT TYPE=button  value='全部依班級順序分組' onclick=\" bb('全部依班級順序分組？全部？','act_idclass');\" class=bur>&nbsp;
<INPUT TYPE=button  value='全部依運動員編號分組' onclick=\" bb('全部依運動員編號分組？全部？','act_sportnum');\" class=bur>
<BR>
<INPUT TYPE=button  value='全部依對拆亂數方式分組' onclick=\" bb('由電腦負責分組？全部？','act_computer2');\" class=bur>&nbsp;
<INPUT TYPE=button  value='全部依三拆亂數方式分組' onclick=\" bb('全部依班級順序分組？全部？','act_computer3');\" class=bur>&nbsp;

</fieldset></td></tr></table>";
//bb('上面、下面、中間都選好了嗎？','act_select');

################	未分組處理		######################
	$tmp_str='';$y=0;
	for($i=0; $i<$rs->RecordCount(); $i++) {
		if ( $arr[$i][sportorder] ==0 ) {
		($arr[$i][results]!=0 || $arr[$i][results]!='' ) ? $dd=" disabled":$dd='' ;
		if ($_GET[txt]=='open') {
			$tmp_str.="<INPUT TYPE='text' NAME='chstu[".$arr[$i][id]."_".$item."]' size=3 value='".$arr[$i][sportorder]."' $dd onfocus=\"this.select();return false ;\" onkeydown=\"moveit2(this,event);\" class=bur>".$arr[$i][cname]."(<B style='color:red'>".substr($arr[$i][idclass],1,4)."</B>)\n";
			}
		else {
			$tmp_str.="<INPUT TYPE='checkbox' NAME='astu[".$arr[$i][id]."_".$item."]' value='".$arr[$i][cname]."'  $dd >".$arr[$i][cname]."(<B style='color:blue'>".substr($arr[$i][idclass],1,4)."</B>)\n";}
		($y%5==4 && $y!=0 ) ? $tmp_str.="<BR>": $tmp_str;
		$y++;
		}
		}
echo "<table border=0  width=100% style='font-size:10pt;' cellspacing=0 cellpadding=0 bgcolor=silver>
<tr bgcolor=white><td><fieldset><legend><img src='images/pin_red.gif'><B>未分組選手名單</B></legend>
<INPUT TYPE='checkbox'  onClick=\"tagall();\" ><B style='color:#800000'>全部/取消/反向選擇</B>
<BR>".$tmp_str."</fieldset></td></tr></table>";




//	$stu_num=substr($arr[$i][idclass],1,4);
//	echo "<INPUT TYPE='checkbox' NAME='stu[".$arr[$i][id]."_".$mid."_".$item."]' value='".$arr[$i][cname]."'>".$arr[$i][cname]."(".$arr[$i][sportorder].")\n";
//	if ($i%6==5 && $i!=0) echo "<BR>";
//	}自動方式 自由亂數 對拆 對拆亂數 三拆 三拆亂數 依班級順序
//手動方式 逐一分組 手動填入
echo "</TD><TD valign=top align=left style='font-size:10pt;color:#800000'><img src='images/booksm.gif'>說明<BR>
+操作前題:<div style='margin-left:10pt;color:blue'>己有輸入成績，則任何操作無效。</div>
+文字欄位:<div style='margin-left:10pt;color:blue'>可以使用方向鍵上下移動。</div>
+報名數:<div style='margin-left:10pt;color:blue'>指己報名的學生數。</div>
+編號數:<div style='margin-left:10pt;color:blue'>指己填寫運動員編號的學生數。</div>
+檢錄數:<div style='margin-left:10pt;color:blue'>指己排出賽順序編號的學生數。</div>
+成績數:<div style='margin-left:10pt;color:blue'>指己輸入成績的學生數。</div>
+重新選擇:<div style='margin-left:10pt;color:blue'>取消所有的選擇動作 。</div>
+所有分組取消:<div style='margin-left:10pt;color:blue'>將所有分組的道次記錄歸零。</div>
+將鉤選的組↑重排:<div style='margin-left:10pt;color:blue'>將選擇的組道次編號亂數重排</div>
+將↓鉤選者加入選擇的分組↑:<div style='margin-left:10pt;color:blue'>選上面的組別及下面要加入的學生。</div>
+將↑鉤選者分組取消:<div style='margin-left:10pt;color:blue'>自由鉤選要取消道次記錄的學生</div>

</TD></TR><TR><TD colspan=2><hr color=#800000 SIZE=1></TD></TR></FORM></TABLE>";
}

#####################  列示項目   #############################
function item_list($mid,$item=''){
		global $CONN,$sportclass,$sportname,$itemkind;
	$SQL="select *  from sport_item   where  mid='$mid' and  skind=0 and sportkind!=5  order by  kind, enterclass ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();

	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' group by itemid ";
	$arr_1=initArray("itemid,nu",$SQL);//全部人數
	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' and  sportnum!='' group by itemid ";
	$arr_2=initArray("itemid,nu",$SQL);//己檢錄人數(編排順序)
	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' and  sportorder!=0 group by itemid ";
	$arr_3=initArray("itemid,nu",$SQL);//己檢錄人數(編排順序)
	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' and  results!='' and  kmaster=0  group by itemid ";
	$arr_4=initArray("itemid,nu",$SQL);//有成績人數

	$ss="<FORM name=p2>選擇項目：<select name='link2' size='1' class='bur' onChange=\"if(document.p2.link2.value!='')change_link(document.p2.link2.value);\">\n<option value='$PHP_SELF?mid=$_GET[mid]&item='>未選擇</option> ";

for($i=0; $i<$rs->RecordCount(); $i++) {
//	($_GET[item]==$arr[$i][id]) ? $gg='images/arrow.gif':$gg='images/closedb.gif';
//		$Nu_arr=chk4num($arr[$i][id]);////報名,沒成績,沒排序
//	(
		($arr_1[$arr[$i][id]]=='') ? $Nu1=0:$Nu1=$arr_1[$arr[$i][id]];
		($arr_2[$arr[$i][id]]=='') ? $Nu2=0:$Nu2=$arr_2[$arr[$i][id]];
		($arr_3[$arr[$i][id]]=='') ? $Nu3=0:$Nu3=$arr_3[$arr[$i][id]];
		($arr_4[$arr[$i][id]]=='') ? $Nu4=0:$Nu4=$arr_4[$arr[$i][id]];

		($item==$arr[$i][id]) ? $cc=" selected":$cc="";
		$ss.="<option value='$PHP_SELF?mid=$_GET[mid]&item=".$arr[$i][id]."'$cc>".$sportclass[$arr[$i][enterclass]].$sportname[$arr[$i][item]].$itemkind[$arr[$i][kind]].
		"&nbsp;(報名數: $Nu1 編號數: $Nu2 檢錄數: $Nu3 成績數: $Nu4)</option>\n";
//	echo "<img src='$gg'><A HREF='$PHP_SELF?mid=$_GET[mid]&item=".$arr[$i][id]."'>". $sportclass[$arr[$i][enterclass]].$sportname[$arr[$i][item]].$itemkind[$arr[$i][kind]]."</A>(<B style='color:#c0c0c0' >".$arr[$i][bu]."</B>)";
	}
	$ss.="</select></FORM>";
Return $ss;


}

function link_a($sclass){
	$class_name_arr = class_base() ;
	$ss="<FORM name=p2>選擇班級：<select name='link2' size='1' class='small' onChange=\"if(document.p2.link2.value!='')change_link(document.p2.link2.value);\"> ";
	foreach($class_name_arr as $key=>$val) {
		($sclass==$key) ? $cc=" selected":$cc="";
		$ss.="<option value='$PHP_SELF?mid=$_GET[mid]&sclass=$key'$cc>$val</option>\n";
	}
	$ss.="</select></FORM>";
Return $ss;
}


?>
