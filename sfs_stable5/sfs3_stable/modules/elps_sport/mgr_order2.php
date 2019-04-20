<?php
//$Id: mgr_order2.php 9273 2018-11-28 03:05:41Z chiming $
include "config.php";
//認證
sfs_check();

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
#####################   自行挑選  ###########################
if ($_POST['act']=='act_sel'){
//	echo "<PRE>";print_r($_POST);die();
	if($_POST['mid']=='') backe("操作錯誤!");
	if($_POST['item']=='') backe("操作錯誤!");
	if($_POST['sitem']=='') backe("操作錯誤!");
	if($_POST['stu']=='') backe("未選擇學生!按下後回上頁重選!");
	if($_POST['stu_N']=='') backe("未選擇學生!按下後回上頁重選!");
	$mid=$_POST['mid'];$item=$_POST['item'];$sitem=$_POST['sitem'];
	asort($_POST['stu']);//保留索引值,並依小大排序。相反函式為arsort()
	//for($i=0;$i<count($_POST[stu]);$i++) {
	//	list($key,$val)=each($_POST[stu]);
	$i=0;
	foreach($_POST['stu'] as $key=>$val){
	$KK=split("_",$key);////idclass,sportnum,stud_id,sportkind ////接力類idclass,kmaster,kgp,sportkind
	$sql_check1="select id from sport_res  where stud_id='$KK[2]' and mid='$mid' and itemid='$sitem' ";
	$sql_check5="select id from sport_res  where  idclass='$KK[0]' and mid='$mid' and itemid='$sitem' and kmaster=2 and kgp='$KK[2]' ";
	($KK[3]=='5') ? $sql_check=$sql_check5:$sql_check=$sql_check1;//區分接力類
	$rs = $CONN->Execute($sql_check) or die($sql_check);
	if($rs->RecordCount() > 0 )  backe("名單重複了！！！");
	if($rs->RecordCount()==0 ) {
		$SQL1="INSERT INTO sport_res(mid,itemid,idclass,sportnum,stud_id,sportkind,cname,sportorder) VALUES ('$mid','$sitem','$KK[0]','$KK[1]','$KK[2]','$KK[3]','".$_POST[stu_N][$KK[0]]."','$GO_num[$i]')";
		$SQL5="INSERT INTO sport_res(mid,itemid,idclass,kmaster,kgp,sportkind,cname,sportorder) VALUES ('$mid','$sitem','$KK[0]','2','$KK[2]','$KK[3]','$KK[0]','$GO_num[$i]')";
	($KK[3]=='5') ? $SQL=$SQL5:$SQL=$SQL1;
		$SQL2="update sport_item set  res=res+1  where id='$sitem' ";
//	echo $SQL."<BR>".$SQL2."<BR>";
		$rs = $CONN->Execute($SQL) or die($SQL);
		$rs = $CONN->Execute($SQL2) or die($SQL2);
		$i++;
		}
	}
	$url=$PHP_SELF."?mid=$mid&item=$item&sitem=$sitem";header("Location:$url");
//	gonow($PHP_SELF."?mid=$mid&item=$item&sitem=$sitem");exit;
}

#####################   刪除  ###########################
if ($_POST['act']=='act_del'){
//	echo "<PRE>";print_r($_POST);die();
	if($_POST['mid']=='') backe("操作錯誤!");
	if($_POST['item']=='') backe("操作錯誤!");
	if($_POST['sitem']=='') backe("操作錯誤!");
//	if($_POST[stu]!='') backe("選錯邊了！按下後回上頁重選!");
	if(count($_POST['del_stu'])==0) backe("未選擇學生!按下後回上頁重選!");
	$mid=$_POST['mid'];$item=$_POST['item'];$sitem=$_POST['sitem'];
	foreach($_POST['del_stu'] as $key=>$val){
	$SQL="delete from  sport_res where id='$key' ";
	$SQL2="update sport_item set  res=res-1  where id='$sitem' ";
	$rs = $CONN->Execute($SQL) or die($SQL);
	$rs = $CONN->Execute($SQL2);// or die($SQL2);
	}
	$url=$PHP_SELF."?mid=$mid&item=$item&sitem=$sitem";header("Location:$url");
//		gonow($PHP_SELF."?mid=$mid&item=$item&sitem=$sitem");
	
}

#####################   成績與順序  ###########################
if ($_POST[act]=='act_me'){
//	echo "<PRE>";print_r($_POST);die();
	if($_POST[mid]=='') backe("操作錯誤!");
	if($_POST[item]=='') backe("操作錯誤!");
	if($_POST[sitem]=='') backe("操作錯誤!");
	if($_POST[stusco]=='' && $_POST[stuord]=='') backe("輸入錯誤！按下後回上頁重選!");
	$mid=$_POST[mid];$item=$_POST[item];$sitem=$_POST[sitem];
if ($_POST[stusco]!='' && $_POST[stuord]=='' ) {
	$SQL="select * from sport_item where id='$_POST[sitem]' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$Chk=split("\.",$arr[0][sunit]);
	$SK=$arr[0][sportkind];
	foreach($_POST[stusco] as $key => $va) {
		$str='';
		$str=sp_nu($va,$Chk,$SK);//值,檢查格式,類別
		if (strlen($str)==strlen($arr[0][sunit])){//再檢查一次格式
			$SQL="update sport_res set  results='$str'  where id='$key' ";
			$rs=$CONN->Execute($SQL) or die($SQL);
			}
		}//end foreach
	}//end if
if ($_POST[stuord]!='' && $_POST[stusco]=='' ) {
	foreach($_POST[stuord] as $key=>$val) {
	$SQL="update sport_res set  sportorder='$val'  where id='$key' ";
	$rs = $CONN->Execute($SQL) or die($SQL);
		}//end foreach
	}//end if
	$url=$PHP_SELF."?mid=$mid&item=$item&sitem=$sitem";header("Location:$url");
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
include_once "chi_text.js";
if($_GET[mid]=='') { print_menu($school_menu_p2);}
else {$link2="mid=$_GET[mid]&item=$_GET[item]&sitem=$_GET[sitem]"; print_menu($school_menu_p2,$link2);}

mmid($_GET[mid]);
if ($_GET[item]!='' && $_GET[sitem]!='' ){
	echo item_list($_GET[mid],$_GET[sitem]);
	stud_list($_GET[mid],$_GET[sitem]);
}
if ($_GET[item]!='' && $_GET[sitem]=='' ){
	$arr=get_next_item($_GET[item]);
	if ($arr=='') {
		echo item_list($_GET[mid]);}
	else {
		echo item_list($_GET[mid],$arr[id]);
		stud_list($_GET[mid],$arr[id]);}
	}
if ($_GET[item]=='' && $_GET[sitem]=='' ) echo item_list($_GET[mid]);


//佈景結尾
foot();

#####################  列示學生   #############################
function stud_list($mid,$item) {
		global $sportname,$itemkind,$sportclass,$k_unit;
	$arr_2=get_item($item);//取得複賽項目
	$arr_1=get_item($arr_2[skind]);//取得初賽項目
//if ($arr_2[sportkind]=='5' || $arr_1[sportkind]=='5') return '';
if ($arr_1=='' || $arr_2=='') return '';
($arr_2[sportkind]=='5' || $arr_1[sportkind]=='5') ? $A_nu=chkman_nu($arr_1[id]):$A_nu=chkman4($arr_1[id]);//計算總人/隊數
	$A_one=$arr_1[playera];//每組人數
	$A_go=$arr_1[passera];//錄取人數
	$A_Name=$sportclass[$arr_1[enterclass]].$sportname[$arr_1[item]].$itemkind[$arr_1[kind]];//名稱
	$B_Name=$sportclass[$arr_2[enterclass]].$sportname[$arr_2[item]].$itemkind[$arr_2[kind]];//名稱
	$A_gp=ceil($A_nu/$A_one);//計算組數
if ($_GET[txt]=='open') include_once 'chi_text.js';

?>
<table border=0 width='100%' style='font-size:11pt;'  cellspacing=1 cellpadding=0 bgcolor=silver>
<FORM METHOD=POST ACTION='<?=$PHP_SELF?>' name='f1'>
<tr bgcolor=white><td width =40% valign=top>
<INPUT TYPE='hidden' name='mid' value='<?=$mid?>'>
<INPUT TYPE='hidden' name='item' value='<?=$arr_1[id]?>'>
<INPUT TYPE='hidden' name='sitem' value='<?=$item?>'>
<INPUT TYPE='hidden' name='act' value=''>
<?php
//echo "<PRE>";print_r($arr_2);
///初賽顯示//$arr_1;
echo"<div style='color:#800000;font-size:10pt; '><FONT COLOR='blue'>■".$A_Name."</FONT><BR> 共 <B>$A_nu</B> 名/隊 參賽 , 每組 <B>$A_one</B> 名 , 錄取 <B>$A_go</B> 名 $arr_1[imemo] 進決賽。<BR></div>";
//	($arr_1[s_ord]=='2') ? $ord=' desc':$ord='' ;
echo "下列提供兩種鉤選方式進入決賽，<BR>請任用一種，不要混用！<HR size=1 color=#800000>";
////         ADD於 0513 依分數排序方式
($arr_1[sord]==1) ? $ORD='asc':$ORD='desc';
$Arr_order=get_order2("select * from sport_res where mid='$mid' and itemid='$arr_1[id]' and   abs(results)!=0 order by  results $ORD ");
	$tmp_str="□依成績列表<div style='margin-left:10pt'>";
	for($i=0; $i<count($Arr_order); $i++) {
		( $Arr_order[$i][results]==$arr_1[sunit]) ? $Cor='#696969':$Cor='red';//沒有成績為灰色
		$G_gp=G_gp($Arr_order[$i][sportorder],$A_one);
		$NN=$i+1;
		if ($arr_1[sportkind]=='5') {
			$tmp_str.="<INPUT TYPE='hidden' NAME='stu_N[".$Arr_order[$i][idclass]."]' value='".$Arr_order[$i][cname]."'>";
			$tmp_str.="<INPUT TYPE='checkbox' NAME='stu[".$Arr_order[$i][idclass]."_".$Arr_order[$i][kmaster]."_";
			$tmp_str.=$Arr_order[$i][kgp]."_".$Arr_order[$i][sportkind]."]' value='".$Arr_order[$i][results]."'>";
			$tmp_str.="($G_gp)第 $NN 名".$Arr_order[$i][sportorder]."_".$Arr_order[$i][cname]."班第".$Arr_order[$i][kgp]."組<FONT COLOR='$Cor'>".$Arr_order[$i][results]."</FONT><BR>\n";
			}
		else {
			$tmp_str.="<INPUT TYPE='hidden' NAME='stu_N[".$Arr_order[$i][idclass]."]' value='".$Arr_order[$i][cname]."'>";
			$tmp_str.="<INPUT TYPE='checkbox' NAME='stu[".$Arr_order[$i][idclass]."_".$Arr_order[$i][sportnum]."_";
			$tmp_str.=$Arr_order[$i][stud_id]."_".$Arr_order[$i][sportkind]."]' value='".$Arr_order[$i][results]."'>";
			$tmp_str.="($G_gp)第 $NN 名".$Arr_order[$i][sportorder]."_".$Arr_order[$i][cname]."<FONT COLOR='$Cor'>".$Arr_order[$i][results]."</FONT><BR>\n";
			}

	}
echo $tmp_str."</div>";
////         ADD於 0513 依分數排序方式....end

echo "<HR size=1 color=#800000>";
for ($a=1;$a<=$A_gp ;$a++){
($arr_1[sportkind]=='5') ? $Arr=get_order($arr_1[id],'par',"results $ord ,$a,$A_one",5):$Arr=get_order($arr_1[id],'par',"results $ord ,$a,$A_one");
	$tmp_str="□第 $a 組<div style='margin-left:10pt'>";
	for($i=0; $i<count($Arr); $i++) {
		( $Arr[$i][results]==$arr_1[sunit]) ? $Cor='#696969':$Cor='red';//沒有成績為灰色
		if ($arr_1[sportkind]=='5') {
			$tmp_str.="<INPUT TYPE='hidden' NAME='stu_N[".$Arr[$i][idclass]."]' value='".$Arr[$i][cname]."'>";
			$tmp_str.="<INPUT TYPE='checkbox' NAME='stu[".$Arr[$i][idclass]."_".$Arr[$i][kmaster]."_";
			$tmp_str.=$Arr[$i][kgp]."_".$Arr[$i][sportkind]."]' value='".$Arr[$i][results]."'>";
			$tmp_str.=$Arr[$i][sportorder]."_".$Arr[$i][cname]."班第".$Arr[$i][kgp]."組<FONT COLOR='$Cor'>".$Arr[$i][results]."</FONT><BR>\n";
			}
		else {
			$tmp_str.="<INPUT TYPE='hidden' NAME='stu_N[".$Arr[$i][idclass]."]' value='".$Arr[$i][cname]."'>";
			$tmp_str.="<INPUT TYPE='checkbox' NAME='stu[".$Arr[$i][idclass]."_".$Arr[$i][sportnum]."_";
			$tmp_str.=$Arr[$i][stud_id]."_".$Arr[$i][sportkind]."]' value='".$Arr[$i][results]."'>";
			$tmp_str.=$Arr[$i][sportorder]."_".$Arr[$i][cname]."<FONT COLOR='$Cor'>".$Arr[$i][results]."</FONT><BR>\n";
			}
	}
////idclass,sportnum,stud_id,sportkind////接力類idclass,kmaster,kgp,sportkind
echo $tmp_str."</div>";
	}


echo "</td><td width=20% valign=top style='color:#800000'>■操作選項：<BR><BR><BR><CENTER>";

$bu1="<INPUT TYPE=button  value='>>將鉤選者加入>>' onclick=\" bb('這些人進決賽？','act_sel');\" class=bu1><BR><BR>
<INPUT TYPE=button  value='<<將鉤選者移除<<' onclick=\" bb('將鉤選者移除？','act_del');\" class=bu1><BR><BR>";
$bu2="<INPUT TYPE=button  value='編輯決賽道次' onclick=\"location.href='$PHP_SELF?mid=$mid&item=$item&sitem=$arr_2[id]&tb=ord';\"  class=bu1><BR><BR>";
$bu3="<INPUT TYPE=button  value='輸入決賽成績' onclick=\"location.href='$PHP_SELF?mid=$mid&item=$item&sitem=$arr_2[id]&tb=sco';\"  class=bur2><BR><BR>";

$tmp_str1="<INPUT TYPE=button  value='依我填寫的送出' onclick=\" bb('依我填寫的送出？真的？','act_me');\" class=bur> <BR><BR>";
$bu5="<INPUT TYPE=button  value='取消返回' onclick=\"self.history.back();\" class=bur><BR><BR>";

($_GET[tb]!='') ? $txt_tb=$tmp_str1.$bu5:$txt_tb=$bu1.$bu2.$bu3;



echo $txt_tb."<INPUT TYPE='reset'  value='清 除 重 選'  class=bu1>";

echo "</CENTER></td><td width=40% valign=top>";
///////////////決賽顯示處理 ///////////////////////
($arr_2[sportkind]=='5' || $arr_1[sportkind]=='5') ? $B_nu=chkman_nu($arr_2[id]):$B_nu=chkman4($arr_2[id]);//計算總人/隊數
//	$B_nu=chkman4($arr_2[id]);//總人數
	$B_one=$arr_2[playera];//每組人數
	$B_go=$arr_2[passera];//錄取人數
	$B_Name=$sportclass[$arr_2[enterclass]].$sportname[$arr_2[item]].$itemkind[$arr_2[kind]];//名稱
	$B_gp=ceil($B_nu/$B_one);//計算組數

echo"<div style='color:#800000'><FONT  COLOR='blue'>■".$B_Name." 依組別列出</FONT><BR> 共 <B>$B_nu</B> 人進決賽 , 每組 <B>$B_one</B> 人 , 錄取 <B>$B_go</B> 人。</div>";

for ($a=1;$a<=$B_gp ;$a++){
($arr_2[sportkind]=='5') ? $Arr=get_order($arr_2[id],'par',"sportorder,$a,$B_one",5):$Arr=get_order($arr_2[id],'par',"sportorder,$a,$B_one");
//	$Arr=get_order($arr_2[id],'par',"sportorder,$a,$B_one");//排序依,第幾組,每組人數
	$tmp_str="□第 $a 組&nbsp;<INPUT TYPE='button' value='印出第 $a 組檢錄單' onclick=\"window.open('mgr_prt.1.php?mid=$mid&item=$arr_2[id]&Spk=$a&kitem=speed','','scrollbars=yes,resizable=yes,height=500,width=600');\" class=bur><INPUT TYPE='button' value='非制式$a組' onclick=\"window.open('mgr_prt_new.php?mid=$mid&item=$arr_2[id]&Spk=$a&kitem=speed&ord=na','','scrollbars=yes,resizable=yes,height=500,width=600');\" class=bur><INPUT TYPE='button' value='定址式$a組' onclick=\"window.open('mgr_prt_new.php?mid=$mid&item=$arr_2[id]&Spk=$a&kitem=speed&ord=local','','scrollbars=yes,resizable=yes,height=500,width=600');\" class=bur><div style='margin-left:10pt'><div style='margin-left:10pt'>
	";
	for($i=0; $i<count($Arr); $i++) {
		($Arr[$i][results]=='') ? $results='0.0':$results=$Arr[$i][results];
		$INP="<INPUT TYPE='checkbox' NAME='del_stu[".$Arr[$i][id]."]' value='".$Arr[$i][cname]."'>";
		if($_GET[tb]=='ord') { $INP="<INPUT TYPE='text' NAME='stuord[".$Arr[$i][id]."]' value='".$Arr[$i][sportorder]."' size=4  class=ipr onfocus=\"this.select();return false ;\" onkeydown=\"moveit2(this,event);\" >";}
		if ($_GET[tb]=='sco') {
			$INP="<INPUT TYPE='text' NAME='stusco[".$Arr[$i][id]."]' value='".$results."' size=10  class=ipr onfocus=\"this.select();return false ;\" onkeydown=\"moveit2(this,event);\" >";
		}
		$tmp_str.=$INP;
		($arr_2[sportkind]=='5') ? $print_kgp="班第".$Arr[$i][kgp]."組":$print_kgp='';
		$tmp_str.=$Arr[$i][sportorder]."_".$Arr[$i][cname]."$print_kgp<FONT COLOR='#696969'>".$Arr[$i][sportnum]." ($results)</FONT><BR>\n";
		}
echo $tmp_str."</div>";
	}
echo "<hr color=#696969 SIZE=1><FONT  COLOR='blue'>■".$B_Name."未檢錄名單：</FONT><div style='margin-left:10pt'>";
//	$Arr=get_order(,'all',"sportorder ");//排序依,第幾組,每組人數
	$Arr=get_order2("select * from sport_res where itemid='$arr_2[id]' and sportorder=0 ");
	for($i=0; $i<count($Arr); $i++) {
			($Arr[$i][results]=='') ? $results=$arr_2[sunit]:$results=$Arr[$i][results];
		$tmp_str='';
		$INP="<INPUT TYPE='checkbox' NAME='del_stu[".$Arr[$i][id]."]' value='".$Arr[$i][cname]."'>";
		if($_GET[tb]=='ord') { $INP="<INPUT TYPE='text' NAME='stuord[".$Arr[$i][id]."]' value='".$Arr[$i][sportorder]."' size=4  class=ipr onfocus=\"this.select();return false ;\" onkeydown=\"moveit2(this,event);\" >";}
		if ($_GET[tb]=='sco') {
			$INP="<INPUT TYPE='text' NAME='stusco[".$Arr[$i][id]."]' value='".$results."' size=10  class=ipr onfocus=\"this.select();return false ;\" onkeydown=\"moveit2(this,event);\" >";
		
		}		
		$tmp_str.=$INP;
		$tmp_str.=$Arr[$i][sportorder]."_".$Arr[$i][cname]."<FONT COLOR='#696969'>".$Arr[$i][sportnum]." ($results)</FONT><BR>\n";
		echo $tmp_str;
		}


echo"</div></td></tr></FORM></table><hr color=#800000 SIZE=1>";
}
function Co_GP($lg,$nu){//組距,編號
$a=ceil($nu/$lg);//計算組數
return $a;
}
#####################  列示項目   #############################
function item_list($mid,$item=''){
		global $CONN,$sportclass,$sportname,$itemkind;
		$class_numa=substr($class_num,0,1);
	$SQL="select *  from sport_item   where  mid='$mid' and  skind!=0  order by  kind, enterclass ";
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
		$ss.="<option value='$PHP_SELF?mid=$_GET[mid]&item=".$arr[$i][skind]."&sitem=".$arr[$i][id]."'$cc>".$sportclass[$arr[$i][enterclass]].$sportname[$arr[$i][item]].$itemkind[$arr[$i][kind]]."&nbsp;(報名數: $Nu1 編號數: $Nu2 檢錄數: $Nu3 成績數: $Nu4)</option>\n";
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

function G_gp($order,$li){
//傳入編號,每組人數
$ss=ceil($order / $li);// 求餘數

Return $ss ;//傳出組別
}

?>
