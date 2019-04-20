<?php
// $Id: admin.php 6558 2011-09-26 07:19:31Z infodaes $
include "config.php";
sfs_check();
$session_tea_sn =  $_SESSION['session_tea_sn'] ;

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

if($c_curr_seme ==''){
	$c_curr_seme = sprintf ("%03s%s",curr_year(),curr_seme()); //現在學年學期
}

$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'] ;
if ( checkid($SCRIPT_FILENAME,1) or ($admin==$session_tea_sn) ){
		$class_seme_p = get_class_seme(); //學年度	
		$seme_temp = "<select name=\"c_curr_seme\" onchange=\"this.form.submit()\">\n";
		while (list($tid,$tname)=each($class_seme_p)){
			if ($c_curr_seme== $tid)
		      		$seme_temp .= "<option value=\"$tid\" selected>$tname</option>\n";
		      	else
		      		$seme_temp .= "<option value=\"$tid\">$tname</option>\n";
		}
		$seme_temp .= "</select>"; 
	$class_avg_a=array("全班平均","男生平均","女生平均");
	$avg_temp=  "<select name='class_avg' onchange='this.form.submit()'>";
	while (list($tid,$tname)=each($class_avg_a)){
		if ($class_avg== $tid)
	      		$avg_temp .= "<option value=\"$tid\" selected>$tname</option>\n";
      		else
      			$avg_temp .= "<option value=\"$tid\">$tname</option>\n";
		}
	$avg_temp .= "</select>"; 	
	$cita_a=array("金質獎章","銀質獎章","銅質獎章");
	$cita_temp=  "<select name='cita_avg' onchange='this.form.submit()'>";
	while (list($tid,$tname)=each($cita_a)){
		if ($cita_avg== $tid)
	      		$cita_temp .= "<option value=\"$tid\" selected>$tname</option>\n";
      		else
      			$cita_temp .= "<option value=\"$tid\">$tname</option>\n";
		}
	$cita_temp .= "</select>"; 	
	$bmi_a=array("身高(以上)","身高(以下)","體重(以上)","體重(以下)","指數(以上)","指數(以下)");
	$bmi_temp=  "<select name='bmi_avg' onchange='this.form.submit()'>";
	while (list($tid,$tname)=each($bmi_a)){
		if ($bmi_avg== $tid)
	      		$bmi_temp .= "<option value=\"$tid\" selected>$tname</option>\n";
      		else
      			$bmi_temp .= "<option value=\"$tid\">$tname</option>\n";
		}
	$bmi_temp .= "</select>"; 	
	$sco_a=array("坐姿前彎","仰臥起坐","立定跳遠","800/1600公尺");
	$sco_temp=  "<select name='sco_avg' onchange='this.form.submit()'>";
	while (list($tid,$tname)=each($sco_a)){
		if ($sco_avg== $tid)
	      		$sco_temp .= "<option value=\"$tid\" selected>$tname</option>\n";
      		else
      			$sco_temp .= "<option value=\"$tid\">$tname</option>\n";
		}
	$sco_temp .= "</select>"; 	

}
else {
		echo "未具管理員資格";
	   //Header("Location: index.php");
	   exit ;
}

// 轉至榮譽榜
if (count ($sel_stud) >0){	
	$now=date("Y-m-d");
	$session_tea_sn =  $_SESSION['session_tea_sn'] ;
	$order_pos=$cita_avg; 
	$data_get=$cita_a[$cita_avg];  
	$sel_year=substr($c_curr_seme,0,-1);
	$sel_seme=substr($c_curr_seme,-1);
	$title=Num2CNum($sel_year)."學年度第".Num2CNum($sel_seme)."學期體適能測驗";
	$body1="恭喜你在".$title."，榮獲";	
	$body2="，我們都為你感到高興，特別頒發獎狀鼓勵，希望你繼續努力，更上層樓。";
	$helper="請填報參與獎章名單，先勾選學生，再點選<font color=red>參與獎章</font>，最後按[確定新增]即可，如有錯誤，可先刪除後再新增 。<br>凡每學期規律參與運動達十二週以上，每週至少三次且每次運動三十分鐘以上，經體育授課老師審核通過者！";
	$kind_set="金質獎章,銀質獎章,銅質獎章,參與獎章,";	
	$sqlstr ="SELECT * FROM cita_kind WHERE doc='$title'";
		  $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    	$row = $result->FetchRow() ;          
        	$id = $row["id"];   
	$end_date = $row["end_date"]; 	
	$beg_date = $row["beg_date"]; 	
	 if($id==''){
		//新增榮譽榜
		$sqlstr ="INSERT INTO cita_kind (title,doc,beg_date,end_date,input_classY,foot,is_hide,grada,kind_set,helper ) 
			VALUES ('$body1', '$title', '$now', '$now', '1,2,3,4,5,6',  '$body2', '1', '0' ,'$kind_set','$helper')";
		 $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;  
		$sqlstr ="SELECT id FROM cita_kind WHERE doc='$title'";
		 $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    		$row = $result->FetchRow() ;          
        		$id = $row["id"];   
		$end_date = $row["end_date"]; 	
		$beg_date = $row["beg_date"]; 	
	}	
	if (date("Y-m-d")>=$beg_date and date("Y-m-d")<=$end_date){
	    for($i=0;$i<count ($sel_stud);$i++){
		$stud_id=sel_data($sel_stud[$i],1);
		$class_id=sel_data($sel_stud[$i],2);
		$num=$i;
		$stud_name=sel_data($sel_stud[$i],3);		
		$sqlstr ="insert into cita_data (kind,stud_id,stud_name,teach_id,class_id,num,data_get,order_pos,up_date) 
					         values ('$id','$stud_id','$stud_name','$session_tea_sn','$class_id','$num','$data_get','$order_pos','$now')";
	             $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
		echo $stud_name ."已匯入<br>";
        	   }
	}else{
	  	echo "填報期限已過";
	}
}






if($Submit<>"列印"){
	head("體適能測驗") ;
	print_menu($menu_p);

	if($chk=='avg') $avg_check="checked";
	if($chk=='cita') $cita_check="checked";
	if($chk=='bmi') $bmi_check="checked";
	if($chk=='sco') $sco_check="checked";

	echo "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\" name=\"chform\">";
	echo  $seme_temp;
	echo  "　<input type='radio' value='avg' $avg_check name='chk'>$avg_temp";
	echo  "　<input type='radio' value='cita' $cita_check  name='chk'>$cita_temp";
	echo  "　<input type='radio' value='bmi' $bmi_check  name='chk'>$bmi_temp <input type='text' name='ass' size='2' value='$ass'>％";
	echo  "　<input type='radio' value='sco' $sco_check  name='chk'>$sco_temp <input type='text' name='asco' size='2' value='$asco'>％";
	echo "　<input type='submit' value='查詢' name='Submit'> <input type='submit' value='列印' name='Submit'>";
	if($chk=='cita' ){
		echo '<br><input type="button" value="全選" onClick="javascript:tagall(1);">';
 		echo '<input type="button" value="取消全選" onClick="javascript:tagall(0);">　';			
		echo "<input type='submit' name='do_key' value='匯出至榮譽榜'>";

	}
}

$main="<br>請點選要查詢的項目！";
if($chk=='avg' )
	$main=avg($class_avg);
if($chk=='cita' )
	$main=cita($cita_avg);
if($chk=='bmi' )
	$main=bmi($bmi_avg,$ass);
if($chk=='sco' )
	$main=sco($sco_avg,$asco);




echo $main;

if($Submit<>"列印"){
	echo "</form>";
	foot() ;
}
//各班統計
function  avg($class_avg){ 
global $CONN,$SCHOOL_BASE,$c_curr_seme,$test,$class_avg_a;

$sex="";
$avg_name=$class_avg_a[$class_avg];
if($class_avg=="1"){
	$sex="and a.stud_sex=1";
}elseif($class_avg=="2"){
	$sex="and a.stud_sex=2";
}

$sel_year=substr($c_curr_seme,0,-1);
$sel_seme=substr($c_curr_seme,-1);
$main=  "<br><font size=4>".$SCHOOL_BASE[sch_cname]." ".$sel_year." 學年度第 ".$sel_seme." 學期 體適能測驗各班 $avg_name 統計表</font>";
$main.= "<table border='1' cellpadding='1' cellspacing='0' style='border-collapse: collapse' bordercolor='#000000' width='100%'>";

$sql = "select  b.seme_class,count(*) as cou,avg(c.tall) as a_tall ,avg(c.weigh) as a_weigh ,avg(c.bmt) as a_bmt ,avg(c.test1) as a_test1 ,avg(c.test2) as a_test2 ,avg(c.test3) as a_test3 ,avg(c.test4) as a_test4 from stud_base a,stud_seme b ,fitness_data c where a.student_sn=b.student_sn and c.student_sn=a.student_sn and (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$c_curr_seme' and c.c_curr_seme='$c_curr_seme' ".$sex." group by b.seme_class"; 
$result =  $CONN->Execute($sql) or user_error("讀取失敗！<br>$query",256) ; 
$main.= "<tr align='center'><td width='12%'>$avg_name</td>";
$main.= "<td width='11%'>人數</td>";
$main.= "<td width='11%'>$test[0]</td>";
$main.= "<td width='11%'>$test[1]</td>";
$main.= "<td width='11%'>BMI指數<br>kg/m2</td>";
$main.= "<td width='11%'>$test[2]</td>";
$main.= "<td width='11%'>$test[3]</td>";
$main.= "<td width='11%'>$test[4]</td>";
$main.= "<td width='11%'>$test[5]</td>";
while ($row = $result->FetchRow() ) {
	$class_num=$row["seme_class"];
	$class_name=class_id2big5($class_num,$sel_year,$sel_seme);
	$cou = $row["cou"];
	$a_tall = round($row["a_tall"],1);
	$a_weigh = round($row["a_weigh"],1);
	$a_bmt = round($row["a_bmt"],1) ;
	$a_test1 = round($row["a_test1"],1) ;
	$a_test2 = round($row["a_test2"],1) ;
	$a_test3 = round($row["a_test3"],1) ;
	$a_test4 = round($row["a_test4"],1) ;
	$main.=  "<tr align='center'><td>$class_name</td><td>$cou</td><td>$a_tall</td><td>$a_weigh</td><td>$a_bmt</td><td>$a_test1</td><td>$a_test2</td><td>$a_test3</td><td>$a_test4</td></tr>" ;   
}
$main.= "</table>";
return $main;
}

//成就獎章
function  cita($cita_avg){ 
global $CONN,$SCHOOL_BASE,$c_curr_seme,$test,$cita_a,$Submit;

$cita_name=$cita_a[$cita_avg];

//取得學生資料陣列
$sql = "select b.seme_class,a.stud_id,a.stud_birthday,a.stud_name,a.stud_sex,b.seme_num,c.*  from stud_base a,stud_seme b ,fitness_data c where a.student_sn=b.student_sn and c.student_sn=a.student_sn and (a.stud_study_cond=0 or a.stud_study_cond=5) and b.seme_year_seme='$c_curr_seme' and  c.c_curr_seme='$c_curr_seme' order by b.seme_class,b.seme_num "; 
$result=$CONN->Execute($sql) or trigger_error("SQL語法錯誤 ", E_USER_ERROR);
$sel_year=substr($c_curr_seme,0,-1);
$sel_seme=substr($c_curr_seme,-1);

$main=  "<br><font size=4>".$SCHOOL_BASE[sch_cname]." ".$sel_year." 學年度第 ".$sel_seme." 學期 體適能測驗全校榮獲 $cita_name 名單</font>";
$main.= "<table border='1' cellpadding='1' cellspacing='0' style='border-collapse: collapse' bordercolor='#000000' width='100%'>";

$main.= "<tr align=center><td width='12%'>年班</td><td width='5%'>座號</td><td width='12%'>姓名</td><td width='6%'>性別</td><td width='5%'>年齡</td>";
$main.= "<td width='15%'>$test[2] [％]</td>";
$main.= "<td width='15%'>$test[3] [％]</td>";
$main.= "<td width='15%'>$test[4] [％]</td>";
$main.= "<td width='15%'>$test[5] [％]</td>";
if($Submit<>"列印"){
		$main.="<td >選擇</td>";
}

$main.= "<tr>";
$s=0;
while ($row = $result->FetchRow()) {
$class_num=$row["seme_class"];
$class_name=class_id2big5($class_num,$sel_year,$sel_seme);

$num=$row["seme_num"];
$stud_id=$row["stud_id"]; 
$name=$row["stud_name"]; 
$stud_name=$name;
if($Submit<>"列印"){
	$url_str_1 = "fitpass.php?stud_id=$stud_id";
	$name="<a onclick=\"openwindow('$url_str_1')\" title='$name 的個人護照'>$name"; 
}

$test1=$row["test1"]; 
$test2=$row["test2"]; 
$test3=$row["test3"]; 
$test4=$row["test4"]; 
$prec1=$row["prec1"]; 
$prec2=$row["prec2"]; 
$prec3=$row["prec3"]; 
$prec4=$row["prec4"]; 
$sex=$row["stud_sex"];
if($sex==1) $sex="男";
elseif($sex==2) $sex="女";
$age=$row["age"];
$text1=text(1,$prec1,$test1);
$text2=text(1,$prec2,$test2);
$text3=text(1,$prec3,$test3);
$text4=text(1,$prec4,$test4);
$cita=cita_c($prec1,$prec2,$prec3,$prec4);
if($cita==$cita_name){
	$class_id=old_class_2_new_id($class_num,$sel_year,$sel_seme);
	$value=$stud_id."#".$class_id."#".$stud_name;
	$main.= "<tr bgcolor='#ffffff' align=center>
	<td  >$class_name </td>
	<td>$num</td>
	<td  > $name</td>
	<td  >$sex </td>
	<td  >$age </td>
	<td  >$text1  $test1 [$prec1]</td>
	<td  >$text2 $test2 [$prec2]</td>
	<td  >$text3 $test3 [$prec3]</td>
	<td >$text4 $test4 [$prec4]</td>";
	if($Submit<>"列印"){
		$main.="<td ><input id=c_$stud_id type=checkbox name=sel_stud[] value=$value></td>";	
	}
$main.="</tr>";
$s++;
}
}
$main.= "</table>";
$main.="共 $s 人";
return $main;

}

//BMI
function  bmi($bmi_avg,$ass=0){ 
global $CONN,$SCHOOL_BASE,$c_curr_seme,$test,$bmi_a,$Submit;

switch ($bmi_avg){
	case 0:
		if($ass==0) $ass=85;
		$bmi_name="身高在". $ass . "％以上";
		$ass_c=" and  c.prec_t>=$ass and c.tall>0 order by c.tall desc";
	break;
	case 1:
		if($ass==0) $ass=15;
		$bmi_name="身高在". $ass . "％以下";
		$ass_c=" and c.prec_t<=$ass  and c.tall>0 order by c.tall ";
	break;
	case 2:
		if($ass==0) $ass=85;
		$bmi_name="體重在". $ass . "％以上";
		$ass_c=" and  c.prec_w>=$ass and c.weigh>0 order by c.weigh desc";
	break;
	case 3:
		if($ass==0) $ass=15;
		$bmi_name="體重在". $ass . "％以下";
		$ass_c=" and c.prec_w<=$ass  and c.weigh>0 order by c.weigh ";
	break;
	case 4:
		if($ass==0) $ass=80;
		$bmi_name="身體質量指數在". $ass . "％以上";
		$ass_c=" and  c.prec_b>=$ass and c.bmt>0 order by c.bmt desc";
	break;
	case 5:
		if($ass==0) $ass=20;
		$bmi_name="身體質量指數在". $ass . "％以下";
		$ass_c=" and c.prec_b<=$ass  and c.bmt>0 order by c.bmt ";
	break;


}
//取得學生資料陣列
$sql = "select b.seme_class,a.stud_id,a.stud_birthday,a.stud_name,a.stud_sex,b.seme_num,c.* from stud_base a,stud_seme b ,fitness_data c where a.student_sn=b.student_sn and c.student_sn=a.student_sn and (a.stud_study_cond=0 or a.stud_study_cond=5) and b.seme_year_seme='$c_curr_seme' and c.c_curr_seme='$c_curr_seme' ".$ass_c.",b.seme_class,b.seme_num"; 
$result=$CONN->Execute($sql) or trigger_error("SQL語法錯誤 ", E_USER_ERROR);
$sel_year=substr($c_curr_seme,0,-1);
$sel_seme=substr($c_curr_seme,-1);

$main=  "<br><font size=4>".$SCHOOL_BASE[sch_cname]." ".$sel_year." 學年度第 ".$sel_seme." 學期 體適能測驗全校 $bmi_name 名單</font>";
$main.= "<table border='1' cellpadding='1' cellspacing='0' style='border-collapse: collapse' bordercolor='#000000' width='100%'>";

$main.= "<tr align=center><td width='12%'>年班</td><td width='5%'>座號</td><td width='12%'>姓名</td><td width='6%'>性別</td><td width='5%'>年齡</td>";
$main.= "<td width='15%'>$test[0] [％]</td>";
$main.= "<td width='15%'>$test[1] [％]</td>";
$main.= "<td width='15%'>身體質量指數<br>BMI(kg/m2)[％]</td>";
$main.= "<tr>";
$s=0;
while ($row = $result->FetchRow()) {
$class_num=$row["seme_class"];
$class_name=class_id2big5($class_num,$sel_year,$sel_seme);
$stud_id=$row["stud_id"]; 
$num=$row["seme_num"];
$name=$row["stud_name"]; 
if($Submit<>"列印"){
	$url_str_1 ="fitpass.php?stud_id=$stud_id";
	$name="<a onclick=\"openwindow('$url_str_1')\" title='$name 的個人護照'>$name"; 
}

$tall=$row["tall"]; 
$weigh=$row["weigh"]; 
$bmt=$row["bmt"]; 
$prec_t=$row["prec_t"]; 
$prec_w=$row["prec_w"]; 
$prec_b=$row["prec_b"]; 

$sex=$row["stud_sex"];
if($sex==1) $sex="男";
elseif($sex==2) $sex="女";
$age=$row["age"];
$text=text(6,$prec_b,$bmt);
$main.= "<tr bgcolor='#ffffff' align=center>
<td  >$class_name </td>
<td>$num</td>
<td  > $name</td>

<td  >$sex </td>
<td  >$age </td>
<td  > $tall [$prec_t]</td>
<td  > $weigh [$prec_w]</td>
<td  >$text $bmt [$prec_b]</td>
</tr>";
$s++;
}

$main.= "</table>";
$main.="共 $s 人";
return $main;

}


//成績
function  sco($sco_avg,$ass=0){ 
global $CONN,$SCHOOL_BASE,$c_curr_seme,$test,$sco_a,$Submit;

		if($ass==0) $ass=75;
		$t=$sco_avg+1;
		$sco_name=$sco_a[$sco_avg]."在". $ass . "％以上";
		$ass_c=" and  c.prec". $t ." >=$ass  and c.test". $t ." >0 order by c.test". $t;
		if( $sco_avg<3) $ass_c.=" desc ";

//取得學生資料陣列
$sql = "select b.seme_class,a.stud_id,a.stud_birthday,a.stud_name,a.stud_sex,b.seme_num,c.* from stud_base a,stud_seme b,fitness_data c where a.student_sn=b.student_sn and c.student_sn=a.student_sn and (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$c_curr_seme' and c.c_curr_seme='$c_curr_seme' ".$ass_c.", b.seme_class,b.seme_num";
$result=$CONN->Execute($sql) or trigger_error("SQL語法錯誤 ", E_USER_ERROR);
$sel_year=substr($c_curr_seme,0,-1);
$sel_seme=substr($c_curr_seme,-1);

$main=  "<br><font size=4>".$SCHOOL_BASE[sch_cname]." ".$sel_year." 學年度第 ".$sel_seme." 學期 體適能測驗全校 $sco_name 名單</font>";
$main.= "<table border='1' cellpadding='1' cellspacing='0' style='border-collapse: collapse' bordercolor='#000000' width='100%'>";
$main.= "<tr align=center><td width='12%'>年班</td><td width='5%'>座號</td><td width='10%'>姓名</td><td width='10%'>成就</td><td width='6%'>性別</td><td width='5%'>年齡</td>";
$main.= "<td width='13%'>$test[2] [％]</td>";
$main.= "<td width='13%'>$test[3] [％]</td>";
$main.= "<td width='13%'>$test[4] [％]</td>";
$main.= "<td width='13%'>$test[5] [％]</td>";
$main.= "<tr>";

$s=0;
while ($row = $result->FetchRow()) {
$class_num=$row["seme_class"];
$class_name=class_id2big5($class_num,$sel_year,$sel_seme);
$num=$row["seme_num"];
$stud_id=$row["stud_id"]; 
$name=$row["stud_name"]; 
if($Submit<>"列印"){
	$url_str_1 ="fitpass.php?stud_id=$stud_id";
	$name="<a onclick=\"openwindow('$url_str_1')\" title='$name 的個人護照'>$name"; 
}

$test1=$row["test1"]; 
$test2=$row["test2"]; 
$test3=$row["test3"]; 
$test4=$row["test4"]; 
$prec1=$row["prec1"]; 
$prec2=$row["prec2"]; 
$prec3=$row["prec3"]; 
$prec4=$row["prec4"]; 
$sex=$row["stud_sex"];
if($sex==1) $sex="男";
elseif($sex==2) $sex="女";
$age=$row["age"];
$text1=text(1,$prec1,$test1);
$text2=text(1,$prec2,$test2);
$text3=text(1,$prec3,$test3);
$text4=text(1,$prec4,$test4);
$cita=cita_c($prec1,$prec2,$prec3,$prec4);

$main.= "<tr bgcolor='#ffffff' align=center>
<td  >$class_name </td>
<td>$num</td>
<td  > $name</td>
<td  > $cita</td>
<td  >$sex </td>
<td  >$age </td>
<td  >$text1  $test1 [$prec1]</td>
<td  >$text2 $test2 [$prec2]</td>
<td  >$text3 $test3 [$prec3]</td>
<td >$text4 $test4 [$prec4]</td>
</tr>";
$s++;
}

$main.= "</table>";
$main.="共 $s 人";
return $main;

}


//優良獎章
function cita_c($t1,$t2,$t3,$t4){
$text="";
if($t1>=85 && $t2>=85 && $t3>=85 && $t4>=85 ){
	$text="金質獎章";
}elseif($t1>=75 && $t2>=75 && $t3>=75 && $t4>=75 ){
	$text="銀質獎章";
}elseif($t1>=50 && $t2>=50 && $t3>=50 && $t4>=50 ){
	$text="銅質獎章";
}
return $text;
}

// 評估
function text($grade,$prec,$s){
$text="";
if($s>0){
	if($grade==1){
		if($prec>=75) $text="<font color=purple>優良</font>";
		if($prec<25) $text="<font color=red>加強</font>";
	}
	if($grade==6){
		if($prec>=80) $text="<font color=red>過重</font>";
		if($prec<20) $text="<font color=red>過輕</font>";
	}	
}
return $text;
}

//切割資料陳列

function sel_data($string,$s) {
$i=1;
$tok = strtok ($string,"#"); 
while ($tok) { 
$data_arr[$i]=$tok; 
$tok = strtok ("#"); 
$i++;
} 
return $data_arr[$s];
}

?>
<script language="JavaScript">
<!--
function openwindow(url_str){
	window.open (url_str,"個人體適能護照","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,copyhistory=no,width=800,height=480");
}
function tagall(status) {		
	var i =0;
	while (i < document.chform.elements.length)  {
		if (document.chform.elements[i].name=='sel_stud[]') {
		document.chform.elements[i].checked=status;
		}
		i++;
	}
}
//  End -->
</script>
