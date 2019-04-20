<?php
//$Id: mgr_input.php 8769 2016-01-13 14:16:55Z qfon $
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


#####################  成績寫入   #############################
if ($_POST[act]=='add_stu'){
	if($_POST[mid]=='') backe("操作錯誤!");
	if($_POST[item]=='') backe("操作錯誤!");
	if($_POST[sportkind]=='') backe("操作錯誤!");
	if(count($_POST[stu])==0) backe("未選擇學生!按下後回上頁重選!");
	$SQL="select * from sport_item where id='$_POST[item]' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$Chk=split("\.",$arr[0][sunit]);
	$SK=$arr[0][sportkind];
	foreach($_POST[stu] as $key => $va) {
		$Id=split('_',$key);
		$str='';
		$str=sp_nu($va,$Chk,$SK);//值,檢查格式,類別
		if (strlen($str)==strlen($arr[0][sunit])){//再檢查一次格式
			$SQL="update sport_res set  results='$str'  where id='$Id[0]'  ";
			$rs=$CONN->Execute($SQL) or die($SQL);
			}
		}//end foreach
		$url=$PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item];header("Location:$url");
	}
#####################  原始成績寫入   #############################
if ($_POST[act]=='add_stu_sco'){
	if($_POST[mid]=='') backe("操作錯誤!");
	if($_POST[item]=='') backe("操作錯誤!");
	if($_POST[sportkind]=='') backe("操作錯誤!");
	if(count($_POST[stu])==0) backe("未選擇學生!按下後回上頁重選!");
	foreach($_POST[stu] as $key => $va) {
		$Id=split('_',$key);
			$SQL="update sport_res set  results='$va'  where id='$Id[0]'  ";
			$rs=$CONN->Execute($SQL) or die($SQL);
		}//end foreach
		$url=$PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item];header("Location:$url");
	}

#####################  成績歸零   #############################
if ($_POST[act]=='del_all_stu'){
	if($_POST[mid]=='') backe("操作錯誤!");
	if($_POST[item]=='') backe("操作錯誤!");
	if($_POST[sportkind]=='') backe("操作錯誤!");
	$SQL="UPDATE sport_res SET results = '' WHERE itemid='$_POST[item]' and mid='$_POST[mid]' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$url=$PHP_SELF."?mid=".$_POST[mid]."&item=".$_POST[item];header("Location:$url");
	}

function sp_nu($Arr,$Brr,$K){//	值,檢查格式,類別
		$SOC=$Arr;
		$Arr=split("\.",trim($Arr));
		$Arr=array_pad($Arr,-count($Brr),'0');
//		echo "<PRE>";print_r($Arr);
		$nu=count($Brr)-1;
	if(strlen($Arr[$nu]) > strlen($Brr[$nu])) return'';
	for($a=0;$a<count($Brr);$a++){
		$i=$nu-$a;//取陣列最後一位
		$j=$i-1;
		($K==1 || $K==5) ? $Sp=60:$Sp=100;//除60或100(時間或長度)
		if ($a==0){$tp1=$Arr[$i];}//小數點後不處理
		else {
		$tp1=$Arr[$i]% $Sp;$tp2=floor($Arr[$i]/$Sp);//檢查是否進位
		if ($tp2 > 0) $Arr[$j]+=$tp2;}//進位則加入
		$Arr[$i]=$tp1;
		}
	$tt=array();
	for($i=0;$i<count($Brr);$i++){
		$x=strlen($Brr[$i]);
		$y=strlen($Arr[$i]);
		$forM="%0".$x."d";
	if ($y > $x ) return '';//die("格式差異太大，無法判讀！");
		$tt[$i]=sprintf($forM,$Arr[$i]);
		}
		$tt=join('.',$tt);
	Return $tt;
	}





//秀出網頁布景標頭
head("競賽報名");


//print_menu($memu_p,$link2);
include_once "menu.php";
include_once "chk.js";

if($_GET[mid]=='') { print_menu($school_menu_p2);}
else {$link2="mid=$_GET[mid]&item=$_GET[item]&sclass=$_GET[sclass]"; print_menu($school_menu_p2,$link2);}
mmid($_GET[mid]);
if ($_GET[mid]!='') echo item_list($_GET[mid],$_GET[item]);
if ($_GET[item]!='') stud_list($_GET[mid],$_GET[item]);


//佈景結尾
foot();

#####################  列示學生   #############################
function stud_list($mid,$item) {
		global $CONN,$sportname,$itemkind,$sportclass,$kind_unit;
	$SQL="select * from sport_item where id ='$item' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$iarr=$rs->GetArray();
	$item_arr=$iarr[0];
//	if ($item_arr[sportkind]==5) return'';//非接力賽結束
	unset($iarr);
	$limit=$item_arr[playera];
	$ITEM_NAME=$sportclass[$item_arr[enterclass]].$sportname[$item_arr[item]].$itemkind[$item_arr[kind]];
if ($item_arr[sportkind]==5){
	$SQL="select *  from sport_res  where itemid ='$item' and kmaster=2 order by sportorder  ";
		}else{
	$SQL="select *  from sport_res  where itemid ='$item' and mid='$mid' order by sportorder  ";
	}
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$all_col=ceil($rs->RecordCount()/$item_arr[playera]);

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

//-->
</script>
<TABLE border=0 width=100%><FORM METHOD=POST ACTION='<?=$PHP_SELF?>' name='f1'><TR><TD colspan=2><img src='images/12.gif'><B><?=$ITEM_NAME?>&nbsp;參賽學生分組列表</B>
</TD></TR>
<TR><TD colspan=2><img src='images/12.gif'><B>項目資訊</B>
己報名<?=$rs->RecordCount()?>人，每組<?=$limit?>人，取<?=$item_arr[passera]?>名<?=$item_arr[imemo]?>，
可分為<?=$all_col?>組。
</TD></TR>
<TR><TD style='font-size:9pt;' width=80% valign=top>
<?php

for ($a=1;$a<=$all_col ;$a++){
	$tmp_str='';$y=0;
	$LimtA=($a-1)*$limit;
	$La=$LimtA+1;
	$LimtB=($a*$limit)+1;
	$Lb=$LimtB-1;
	for($i=0; $i<$rs->RecordCount(); $i++) {
	($arr[$i][results]=='') ? $E_results='0.0' :$E_results=$arr[$i][results];
//	($arr[$i][sportkind]==1 ) ? $dd=" onchange='C1(this.value);'":$dd=" onchange='C2(this.value);'";
	if ( $arr[$i][sportorder] > $LimtA && $arr[$i][sportorder] < $LimtB ) {
		if ($item_arr[sportkind]==5) $Str_1="-".$arr[$i][kgp];
		$tmp_str.="<FONT COLOR='#808080'>(".$arr[$i][sportorder]."_".$arr[$i][sportnum].")</FONT>".$arr[$i][cname]."$Str_1<INPUT TYPE='text' NAME='stu[".$arr[$i][id]."_".$item."]' size=5 value='".$E_results."' $dd onfocus=\"this.select();return false ;\" onkeydown=\"moveit2(this,event);\" class=ipr2>\n";
		($y%3==2 && $y!=0 ) ? $tmp_str.="<BR>": $tmp_str;
		$y++;
		}//end if

	}//end for
	$echo_STR.= "<B style='color:blue'>□第 $a 組</B>&nbsp;
	($La - $Lb)&nbsp;
	<div style='margin-left:0pt'>".$tmp_str."</div>";
	}
echo "<table border=0  width=100% style='font-size:10pt;'  cellspacing=0 cellpadding=0 bgcolor=silver>
<tr bgcolor=white><td><fieldset><legend><img src='images/pin_orange.gif'><B>己分組選手名單</B></legend>
注意：<FONT  COLOR='red'>成績格式為：<B>".$item_arr[sunit]."</B></FONT><BR>

<INPUT TYPE='hidden' name='mid' value='$mid'>
<INPUT TYPE='hidden' name='item' value='$item'>
<INPUT TYPE='hidden' name='act' value=''>
<INPUT TYPE='hidden' name='sportkind' value='$item_arr[sportkind]'>
<INPUT TYPE=button  value='原始格式寫入成績資料' onclick=\" bb('自訂的格式寫入成績資料？OK？','add_stu_sco');\" class=bu1>&nbsp;<INPUT TYPE='reset' value='重新選擇' class=bu1>&nbsp;
<INPUT TYPE=button  value='自動格式寫入成績資料' onclick=\" bb('寫入成績資料？OK？','add_stu');\" class=bu1><BR>
<FONT COLOR='#009900'>※建議使用自動格式寫入,最後一個小數點後的數字,不處理進位問題！</FONT>
<BR>
<INPUT TYPE='button' value='以競賽類印出本項成績' onclick=\"window.open('mgr_prt.1.php?mid=$mid&item=$item&Spk=1&kitem=pspeed','','menubar=yes,scrollbars=yes,resizable=yes,height=500,width=600');\" class=bur>&nbsp;\n<INPUT TYPE='button' value='以田賽類印出本項成績' onclick=\"window.open('mgr_prt.1.php?mid=$mid&item=$item&Spk=1&kitem=long_high','','menubar=yes,scrollbars=yes,resizable=yes,height=500,width=600');\" class=bur><BR>".
$echo_STR."<BR><INPUT TYPE=button  value='XX 讓本項成績歸零，請小心使用！' onclick=\" bb('XXXXXX 成績歸零？有影無？','del_all_stu');\" class=bur2></fieldset></td></tr></table>";


echo "</TD><TD valign=top align=left style='font-size:10pt;color:#800000'><img src='images/booksm.gif'>說明<BR>
+成績輸入:<div style='margin-left:5pt;color:blue'>1.方向鍵上下可移動。<BR>2.至少要有一個小數點</div>
+成績輸入可接受格式:<div style='margin-left:0pt;color:blue'>
===田賽類===<BR>
.12.2=>00.12.2<BR>
3.3.3=>03.03.3<BR>
300.3=>03.00.3<BR>
<FONT COLOR='red'>88=>ｘ無法處理</FONT><BR>
===競賽類===<BR>
666.6=>0.11.06.6<BR>
1.99.6.1=>2.39.06.1<BR>
1.99.99.6=>2.40.39.6<BR>
1.6.3.2=>1.06.03.2<BR>
99.6=>0.01.39.6<BR>
999.=>0.16.39.0<BR>
8888.=>2.28.08.0<BR>
<FONT COLOR='red'>999=>X無法處理</FONT><BR>
<FONT COLOR='red'>超過規定值=>X無法處理</FONT>

</div>

</TD></TR><TR><TD colspan=2><hr color=#800000 SIZE=1></TD></TR></FORM></TABLE>";
}

#####################  列示項目   #############################
function item_list($mid,$item=''){
		global $CONN,$sportclass,$sportname,$itemkind;
		$class_numa=substr($class_num,0,1);
	$SQL="select *  from sport_item   where  mid='$mid' and  skind=0  order by  kind, enterclass ";
	$rs=$CONN->Execute($SQL) or die($SQL);// and sportkind!=5
	$arr=$rs->GetArray();
	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' group by itemid ";
	$arr_1=initArray("itemid,nu",$SQL);//全部人數
	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' and  sportnum!='' group by itemid ";
	$arr_2=initArray("itemid,nu",$SQL);//己檢錄人數(編排順序)
	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' and  sportorder!=0 group by itemid ";
	$arr_3=initArray("itemid,nu",$SQL);//己檢錄人數(編排順序)
	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' and results !='' and kmaster=0   group by itemid ";
	$arr_4=initArray("itemid,nu",$SQL);//有成績人數

	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' and kmaster='2' group by itemid ";
	$Brr_1=initArray("itemid,nu",$SQL);//全部人數
	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' and kmaster='2'  and  sportnum!='' group by itemid ";
	$Brr_2=initArray("itemid,nu",$SQL);//己檢錄人數(編排順序)
	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' and kmaster='2'  and  sportorder!=0 group by itemid ";
	$Brr_3=initArray("itemid,nu",$SQL);//己檢錄人數(編排順序)
	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' and kmaster='2'  and results !=''  group by itemid ";
	$Brr_4=initArray("itemid,nu",$SQL);//有成績人數


	$ss="<FORM name=p2>選擇項目：<select name='link2' size='1' class='bur' onChange=\"if(document.p2.link2.value!='')change_link(document.p2.link2.value);\">\n<option value='$PHP_SELF?mid=$_GET[mid]&item='>未選擇</option> ";

for($i=0; $i<$rs->RecordCount(); $i++) {
	if ($arr[$i][sportkind]!=5){
		($arr_1[$arr[$i][id]]=='') ? $Nu1=0:$Nu1=$arr_1[$arr[$i][id]];
		($arr_2[$arr[$i][id]]=='') ? $Nu2=0:$Nu2=$arr_2[$arr[$i][id]];
		($arr_3[$arr[$i][id]]=='') ? $Nu3=0:$Nu3=$arr_3[$arr[$i][id]];
		($arr_4[$arr[$i][id]]=='') ? $Nu4=0:$Nu4=$arr_4[$arr[$i][id]];
			}else {
		($Brr_1[$arr[$i][id]]=='') ? $Nu1=0:$Nu1=$Brr_1[$arr[$i][id]];
		($Brr_2[$arr[$i][id]]=='') ? $Nu2=0:$Nu2=$Brr_2[$arr[$i][id]];
		($Brr_3[$arr[$i][id]]=='') ? $Nu3=0:$Nu3=$Brr_3[$arr[$i][id]];
		($Brr_4[$arr[$i][id]]=='') ? $Nu4=0:$Nu4=$Brr_4[$arr[$i][id]];
			}

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
