<?php
//$Id: mgr_stu5.php 8769 2016-01-13 14:16:55Z qfon $
$mid=intval($mid);
$SQL="select *  from sport_item where mid='$mid' and enterclass like '$class_num_1%' and skind=0 and sportkind=5 order by  kind, enterclass ";
$rs=$CONN->Execute($SQL) or die($SQL);
$arr=$rs->GetArray();//取項目與報名數


for($i=0; $i<$rs->RecordCount(); $i++) {	//取項目迴圈
	$S_Name=$sportclass[$arr[$i][enterclass]].$sportname[$arr[$i][item]].$itemkind[$arr[$i][kind]];
echo "<U>◎".$S_Name."</U>&nbsp;&nbsp;<FONT  COLOR='blue'>";
echo "可報名：".$arr[$i][kgp]."組;每組選".$arr[$i][kgm]."人</FONT><BR>\n";
$Button=0;//控制接鈕變數
$W='';
for ($X=1;$X<=$arr[$i][kgp];$X++){	//取組別迴圈
	switch(ChkK5($arr[$i][id],$class_num,$X)) {
	case 0:
		if ($Button==1) break;
		if ($X!=1) $W='也';
		echo "<INPUT TYPE=button  value='本班".$W."要參加第$X 組' onclick=\"if(window.confirm('確定參加？')){location='$_SERVER[PHP_SELF]?mid=$mid&sclass=$class_num&act=K5&GP=$X&item=".$arr[$i][id]."' ;}\" class=bur>\n";
		$Button=1;//第2個未報名的組不再出現
		break;
	case 1:
		echo "<INPUT TYPE='radio' NAME='item' VALUE='".$arr[$i][id]."_".$arr[$i][sportkind]."_".$X."'>$class_num 班代表隊第 $X 組&nbsp;\n
		<INPUT TYPE=button  value='X不參加' onclick=\"if(window.confirm('不參加了？')){location='$_SERVER[PHP_SELF]?mid=$mid&sclass=$class_num&act=Del_K5&GP=$X&item=".$arr[$i][id]."' };\" class=bur2>\n";
		echo Show_K5($arr[$i][id],$class_num,$X);//印出本組人員
		break;
	default:;
	}
	}//end $X
echo "<BR><BR>";

}

##################檢查團體組報名單###########################
function ChkK5($item,$Class,$GP) {
	global $CONN ;
	$SQL="select * from sport_res where itemid=$item and  idclass like '$Class%' and  sportkind=5 and kmaster=2 and kgp='$GP' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	return $rs->RecordCount();
}
################## Show團體組報名單 ###########################
function Show_K5($item,$Class,$GP) {
	global $CONN ;
	$SQL="select * from sport_res where kgp='$GP' and  itemid=$item and  idclass like '$Class%' and  sportkind=5 and  kmaster=0 order by idclass ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();//取項目與報名數
	$Str='';
	for($i=0; $i<$rs->RecordCount(); $i++) {
		$Str.= "<INPUT TYPE='checkbox' NAME='del_id[".$arr[$i][id]."_".$item."]' value='".$arr[$i][cname]."' >".substr($arr[$i][idclass],3,2).$arr[$i][cname]." \n";}//得到 checkbox 選項
	return "<div style='color:#800000;margin-left:5pt;'>$Str</div>";
}

?>