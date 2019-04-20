<?php
//$Id: config_sport.php 8769 2016-01-13 14:16:55Z qfon $
//$sport_GO_num=array(8=>1,7=>1,6=>2,5=>2,4=>3,3=>3,2=>4,1=>4);
//$sport_GO_num6=array(6=>1,5=>1,4=>2,3=>2,2=>3,1=>3);
//前為比賽人數
//後者為跑道數,意指當人數未滿時要從那個跑道開始編排道次
//$GO_num=array(4,5,3,6,2,7,1,8);
//$GO_num6=array(3,4,2,5,1,6);
check_update9311();
$GO_num_data=initArray("kkey,na","select kkey,na from sport_var where gp='road_num' ");//初始化項目
$sport_GO_num=get_num_start($GO_num_data[num]);
$GO_num=get_num_out($GO_num_data[num]);
//print_r($GO_num);
////------以下生手請勿再做變更--更改環境設定結束------//////
function check_update9311() {
	global $CONN;
	$SQL="select kkey,na from sport_var where gp='road_num' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
if ($rs->RecordCount()==0){
		$SQL="insert into  sport_var(gp,kkey,na)values('road_num','num','8') ";
		$rs=$CONN->Execute($SQL) or die($SQL);
	}
	return"";
}
////------以下生手請勿再做變更--更改環境設定結束------//////
function get_num_out($GO_num) {
	$all= intval($GO_num);
	$nn=array();
for($i=0;$i<$all;$i++){
	switch ($all%2) {
		case 0:
			(($i%2)==0) ? $num=$all/2-$i/2:$num=ceil($all/2+$i/2);break;
		case 1:
			(($i%2)==1) ? $num=$all/2-$i/2:$num=ceil($all/2+$i/2);break;
		default:"";
		}
		$nn[$i]=$num;//4 5 3 6 2 7 1 8echo $num."<BR>";	
	}
	return $nn;
}
////------以下生手請勿再做變更--更改環境設定結束------//////
function get_num_start($GO_num) {
	$all= intval($GO_num);
	$nn=array();
for($i=1;$i<=$all;$i++){
	switch ($all%2) {
		case 0:
		(($i%2)==1) ? $num=ceil(($all-$i)/2):$num=ceil(($all-$i)/2)+1;break;
		case 1:
		(($i%2)==0) ? $num=ceil(($all-$i)/2):$num=ceil(($all-$i)/2)+1;break;
		default:"";
	}
//	echo "\$nn[$i]:=".$num."<BR>";
	$nn[$i]=$num;//4 5 3 6 2 7 1 8echo $num."<BR>";	
	}
	return $nn;
}

//決賽時成績第1名排第4道第2名排第5道...
/*
$sportname=array(7=>"跳高",8=>"跳遠",9=>"棒球擲遠",10=>"打字",11=>"壘球擲遠",
12=>"60公尺",13=>"80公尺",14=>"100公尺",
15=>"200公尺",16=>"學生調查",17=>"大隊接力",18=>"推鉛球",19=>"鐵餅",20=>"標槍",
1=>"作文",2=>"演講",3=>"注音",4=>"查字典",5=>"書法",6=>"朗讀");
*/
$sportname=initArray("kkey,na","select kkey,na from sport_var where gp='sportname' ");//初始化項目
////------以下生手請勿再做變更--更改環境設定結束------//////
////////////////////////////////////////////////////////////
//$s_unit=array("long"=>"遠度","heigh"=>"高度","speed"=>"速度","score"=>"分數",);//計量單位
/*$sportclass=array(
"1a"=>"一男","1b"=>"一女","2a"=>"二男","2b"=>"二女",
"3a"=>"三男","3b"=>"三女","4a"=>"四男","4b"=>"四女",
"5a"=>"五男","5b"=>"五女","6a"=>"六男","6b"=>"六女",
"1c"=>"1年級","2c"=>"2年級","3c"=>"3年級","4c"=>"4年級",
"5c"=>"5年級","6c"=>"6年級");*/
if($IS_JHORES==0) $sportclass=initArray("kkey,na","select kkey,na from sport_var where gp='sportclass' ");//國小
if($IS_JHORES==6) $sportclass=initArray("kkey,na","select kkey,na from sport_var where gp='sportclass7' ");//國中
$sportkind_name=array(1=>"競賽類",5=>"競賽(接力)",2=>"田賽類",3=>"語文類",4=>"其他類");//關連到sport_item.sportkind 
$kind_unit=array(1=>"x.xx.xx.x(時.分.秒.x)",2=>"xx.xx.x(公尺.公分.x)");
$k_unit=array('1'=>'0.00.00.0','2'=>'00.00.0');//計分格式
$itemkind=array("1"=>"初賽","2"=>"決賽","3"=>"不分");//關連到sport_item.kind 

##################人員檢查sfs3###########################
function check_man($login,$mid,$pa) {
	global $CONN;
	$SQL="select * from  sport_teach where  teacher_sn='$login' and  tmid='$mid' and pa >='$pa' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$rtn='NO';
if ($rs->RecordCount()==1 ) $rtn='YES';
	return $rtn;
	}
##################取到領隊 每班僅一人 ###########################
function get_Master($mid,$sclass) {
	global $CONN;
	$SQL="select * from  sport_res where mid='$mid' and idclass like '$sclass%' and kmaster=1 ";
//die($SQL);
	$rs=$CONN->Execute($SQL) or die($SQL);
if ($rs->RecordCount()!=1 ) return "";
	$arr=$rs->GetArray();
	return $arr[0];
	}

##################時間檢查###########################
function chk_time_out($begin,$stop,$str) {
	if($str=='a') {
		$pri=array("--報名已截止","--尚未開放","--操作受理中..");}
	else {
		$pri=array("--操作期限己過","--尚未開始作業","--開放作業中..");}
	$be=split("[- :]",$begin);//年0,月1,日2,時3,分4,秒5
	$st=split("[- :]",$stop);
	$begin_T=mktime($be[3],$be[4],0,$be[1],$be[2],$be[0]);//時分秒月日年
	$stop_T=mktime($st[3],$st[4],0,$st[1],$st[2],$st[0]);
	$now=mktime(date("H"),date("i"),0,date("m"),date("d"),date("Y")); 
	if ($now > $stop_T && $now > $begin_T ) $rn=array("1",$pri[0]);
	if ($now < $begin_T && $stop_T > $now) $rn=array("2",$pri[1]);
	if ( $begin_T < $now && $stop_T > $now) $rn=array("3",$pri[2]);
	Return $rn;//編號,文字
}
#####################   列示主要項目  ###########################
function mmid($gmid='') {
	global $CONN;
($gmid=='')? $SQL="select * from  sport_main   order by  year ":$SQL="select * from  sport_main  where id='$gmid' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
for($i=0; $i<$rs->RecordCount(); $i++) {
	$strA= "<img  src='images/21.gif' border=0>";
	$strB="<A HREF='$PHP_SELF?mid=".$arr[$i][id]."'>";
	$strD="</A>";
	$str_time=chk_time_out($arr[$i][work_start],$arr[$i][work_end],b);//檢查時間
		switch($str_time[0]) {
			case '1':echo $img.$strA.$arr[$i][title].$str_time[1];break;
			case '2':echo $img.$strA.$arr[$i][title].$str_time[1];break;
			case '3':echo $img.$strA.$strB.$arr[$i][title].$str_time[1].$strD;break;
			default:}
	echo "&nbsp;(".$arr[$i][year].")<BR>\n";
	}
}
#####################   列示主要項目  ###########################
function mmid_t($gmid='') {
	global $CONN;
($gmid=='')? $SQL="select * from  sport_main   order by  year ":$SQL="select * from  sport_main  where id='$gmid' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
for($i=0; $i<$rs->RecordCount(); $i++) {
	$strA= "<img  src='images/21.gif' border=0>";
	$strB="<A HREF='$PHP_SELF?mid=".$arr[$i][id]."'>";
	$strD="</A>";
	$str_time=chk_time_out($arr[$i][signtime],$arr[$i][stoptime],a);//檢查時間
		switch($str_time[0]) {
			case '1':echo $img.$strA.$arr[$i][title].$str_time[1];break;
			case '2':echo $img.$strA.$arr[$i][title].$str_time[1];break;
			case '3':echo $img.$strA.$strB.$arr[$i][title].$str_time[1].$strD;break;
			default:}
	echo "&nbsp;(".$arr[$i][year].")<BR>\n";
	}
}

#####################   檢查開放時間如果可以操作,則傳回3   ###########################
function ch_mid_t($gmid) {
	global $CONN;
	$SQL="select * from  sport_main  where id='$gmid' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	if($rs->RecordCount()==1 ) {
		$str_time=chk_time_out($arr[0][work_start],$arr[0][work_end],b);//檢查時間
		Return $str_time[0];
		}
}
#####################   檢查開放時間如果可以操作,則傳回3   ###########################
function ch_mid($gmid='') {
	global $CONN;
	$SQL="select * from  sport_main  where id='$gmid' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	if($rs->RecordCount()==1 ) {
		$str_time=chk_time_out($arr[0][signtime],$arr[0][stoptime],b);//檢查時間
		Return $str_time[0];
		}
}

##################人員計算###########################
function chkman4($item) {
	global $CONN ;
	$SQL="select * from  sport_res  where itemid='$item' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
return $rs->RecordCount();
}
##################人員計算###########################
function chkman_nu($item) {
	global $CONN ;
	$SQL="select * from  sport_res  where itemid='$item' and kmaster=2 ";
	$rs=$CONN->Execute($SQL) or die($SQL);
return $rs->RecordCount();
}
##################計算人數函式###########################
function chk4num($item) {
	global $CONN ;
$ra=$CONN->Execute("select id from sport_res where itemid=$item ");//該項目人員
$rb=$CONN->Execute("select id from sport_res where itemid=$item and results=0 ");//沒有成績
$rc=$CONN->Execute("select id from sport_res where itemid=$item and sportorder=0 ");//沒有排序的
$a=array($ra->RecordCount(),$rb->RecordCount(),$rc->RecordCount());
unset($ra);
unset($rb);
unset($rc);
return $a;
}
##################取得項目資訊函式###########################
function initArray($F1,$SQL){
	global $CONN ;
//	global $db;
// 當尚未到達 記錄集 $rs 的結束位置(EOF：End Of File)時，(即：還有記錄尚未取出時)
	$col=split(",",$F1);
	$rs = $CONN->Execute($SQL) or die($SQL);
	$sch_all = array();
	if (!$rs) {
    Return $CONN->ErrorMsg(); 
	} else {
		while (!$rs->EOF) {
//	print $rs->fields['sch_id'] . " " . $rs->fields['sch_name'];
	if(count($col)==2) {
//		$index=$col[0];$val=$col[1];
//		$index=$rs->fields[0];
//		$sch_all[$rs->fields[$index]]=$rs->fields[$val]; 
		$sch_all[$rs->fields[0]]=$rs->fields[1]; 
//		echo $rs->fields[0]."_".$rs->fields[1]."<BR>";
		}
	if(count($col)==3) {
		$sch_all[$rs->fields[0]]=array($val=>$rs->fields[1],$val2=>$rs->fields[2]);
//		$index=$col[0];$val=$col[1];$val2=$col[2];
//		$sch_all[$rs->fields[$index]]=array($val=>$rs->fields[$val],$val2=>$rs->fields[$val2]);
		}
	$rs->MoveNext(); // 移至下一筆記錄
	}
	}
	Return $sch_all;
}

##################取得項目資訊函式###########################
function get_order($item,$kind,$str='',$KM='') {
	//項目,方式,(第幾組,每組人數,排序依)
	global $CONN ;
	$LL=split(",",$str);//排序依,第幾組,每組人數
//$SQL="SELECT * FROM sport_res WHERE itemid='$item' ";
($KM=='') ? $add_KM='': $add_KM=' and kmaster=2 ';
//echo $add_KM;
if ($LL[1]!='' && $kind!='all'){
	$Q=$LL[0];
	$La=($LL[1]-1)*$LL[2];
	$Lb=$LL[1]*$LL[2];
	$SQL="SELECT * FROM sport_res WHERE itemid='$item' and sportorder>$La and sportorder<= $Lb  $add_KM  order by $Q ";
	}
if ($kind=='all' && $LL[0]!=''){
	$Q=$LL[0];
	$SQL="SELECT * FROM sport_res WHERE itemid='$item'  $add_KM   order by $Q ";}

($SQL=='') ? $SQL="SELECT * FROM sport_res WHERE itemid='$item'  $add_KM   ":$SQL;
$rs=$CONN->Execute($SQL) or die($SQL);
$arr = $rs->GetArray();
return $arr ;
}
##################取得項目資訊函式###########################
function get_order2($SQL) {
	//項目,方式,(第幾組,每組人數,排序依)
	global $CONN ;
$rs=$CONN->Execute($SQL) or die($SQL);
$arr = $rs->GetArray();
return $arr ;
}
##################取得相關項目資訊函式##################
function get_next_item($id) {
	global $CONN ;
	$SQL="select * from sport_item where skind ='$id' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	if($rs->RecordCount()==1 ) {
	$arr=$rs->GetArray();
	return $arr[0];
		}
	}
##################取得上一個相關項目資訊函式##################
function get_item($id) {
	global $CONN ;
	$SQL="select * from sport_item where id ='$id' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	if($rs->RecordCount()==1 ) {
	$arr=$rs->GetArray();
	return $arr[0];
		}
}
########IP檢查函式--村仔#################################
function checkip($rip) {
	global $remote_in;
	$allowIP=$remote_in;
	$rip=split("\.",$rip);
	$login='off';
for ($i=0;$i<count($allowIP);$i++) {
	$AIP=split("\.",$allowIP[$i]);
if ($rip[0]==$AIP[0] && $rip[1]==$AIP[1] &&  $rip[2]==$AIP[2] ) {
	$AIPB=split("-",$AIP[3]);
	for ($j=$AIPB[0];$j<=$AIPB[1];$j++) {
		if ($j==$rip[3]) {$login='on';}
		}
	}//if
	}//for
return $login;
}
##################回應函式#####################
function walert($st='已有其他相關資料！\n\n無法執行您的操作動作！') {

echo"
<SCRIPT LANGUAGE=\"JavaScript\">
<!--
alert('$st');
//-->
</SCRIPT>";
}
##################陣列列示函式##########################
function set_sport_select($name,$array_name,$select_t="") {
	//名稱,起始值,結束值,選擇值
echo"<select name='$name'>\n";
echo "<option value='未選擇'>----</option>\n";
for ($i=0;$i<count($array_name);$i++) {

if ($i==$select_t)
	{echo "<option value=".$i." selected>".$array_name[$i]."</option>\n";}
	else {
	echo "<option value=".$i." >".$array_name[$i]."</option>\n";	}
}
echo "</select>";
 }
##################陣列列示函式2##########################
function set_sport_selectb($name,$array_name,$select_t='') {
	//名稱,起始值,結束值,選擇值
echo"<select name='$name' >\n";
echo "<option value='未選擇'>-未選擇-</option>\n";
foreach($array_name as $key=>$val) {
 ($key==$select_t) ? $bb=' selected':$bb='';
	echo "<option value='$key' $bb>$val</option>\n";
	}

echo "</select>";
 }
##################陣列列示函式2##########################
function chi_sel($name,$array_name,$select_t='') {
	//名稱,起始值,結束值,選擇值
$str="<select name='$name' >\n";
$str.="<option value=''>-未選擇-</option>\n";
foreach($array_name as $key=>$val) {
 ($key==$select_t) ? $bb=' selected':$bb='';
	$str.= "<option value='$key' $bb>$val</option>\n";
	}
$str.="</select>";
return $str;
 }
##################時間函式######################
function set_time_select($name,$start,$stop,$select_t="") {
	//名稱,起始值,結束值,選擇值
echo"<select name='$name' size=1>\n";
echo "<option value='未選擇'>----</option>\n";
for ($i=$start;$i<$stop;$i++) {

if ($i==$select_t)
	{echo "<option value='$i' selected>$i</option>\n";}
	else {
	echo "<option value='$i' >$i</option>\n";	}
}
echo "</select>";
 }
##################年級班級座號函式############################
function set_class($name,$start,$stop,$select_t="") {
	//名稱,起始值,結束值,選擇值
echo"<select name=".$name." class=b14>\n";
echo "<option value=''>未選擇</option>\n";
for ($i=$start;$i<$stop;$i++) {

if ($i==$select_t)
	{echo "<option value=\"".sprintf("%02d",$i)."\" selected>".$i."</option>\n";}
	else {
	echo "<option value=\"".sprintf("%02d",$i)."\" >".$i."</option>\n";	}
}
echo "</select>";
 }

##################回上頁函式1#####################
function backinput($st="未填妥!按下後回上頁重填!") {
echo"<CENTER><form>
	<input type='button' name='b1' value='$st' onclick=\"history.back()\" style='font-size:12pt;color:red'>
	</form></CENTER>";
	}
##################回上頁函式1#####################
function backe($st="未填妥!按下後回上頁重填!") {
echo"<BR><BR><BR><BR><CENTER><form>
	<input type='button' name='b1' value='$st' onclick=\"history.back()\" style='font-size:12pt;color:red'>
	</form></CENTER>";
	exit;
	}
##################回上頁函式2############################
function backurl($st="未填妥!按下後回上頁重填!",$url) {
echo"<CENTER><form>
<input type='button' name='b1' value='$st' onclick=\"location='$url'\" style='font-size:12pt;color:red'>
</form></CENTER>";
}

##################轉頁函式#######################################
//代表圖示,請自訂
function gonow($afterurl) {
echo"<META HTTP-EQUIV=REFRESH CONTENT=\"0;URL=$afterurl\">";
	exit;}
###############回上頁函式#####################################
function backhome($url,$st="回首頁") {
	echo"<CENTER><form>
	<input type='button' name='b1' value='$st' onclick=\"location.href='$url';\" style='font-size:12pt;color:red'>
	</form></CENTER>";
	exit;}

function btt() {
?><FORM  NAME='ShowText'>
<INPUT TYPE='text' NAME='ifo' value='' size='30' disabled
style=' border-width: 0px; background-color:White; font-size:12pt;color:red;'>
</FORM>
<?php
	}
function btr($img,$word="重新選擇填寫") {
?><input TYPE='image' align='top' border=0 SRC='<?=$img?>' 
onclick="this.form.reset();return false;" alt='<?=$word?>' 
onmouseover="ShowText.ifo.value='<?=$word?>';" onmouseout="ShowText.ifo.value='';">
<?php
	}
function bt($act,$word,$img) {
?>
<input TYPE='image' align='top' border=0 SRC='<?=$img?>' 
onclick=" if (window.confirm('<?=$word?>？')){this.form.act.value='<?=$act?>';this.form.sumit();}return false;" alt='<?=$word?>' onmouseover="ShowText.ifo.value='<?=$word?>';" onmouseout="ShowText.ifo.value='';">

<?php
	}

?>
