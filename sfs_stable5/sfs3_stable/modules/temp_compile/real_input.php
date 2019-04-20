<?php
// $Id: real_input.php 7545 2013-09-19 03:31:46Z hami $

/*引入學務系統設定檔*/
require "config.php";
$class_year_b=$_REQUEST['class_year_b'];

//使用者認證
sfs_check();

//程式檔頭
head("新生編班");

print_menu($menu_p);
//設定主網頁顯示區的背景顏色
echo "
<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc>
<tr>
<td bgcolor='#FFFFFF'>";
//網頁內容請置於此處

//檢查是否已經編完班了
$new_sel_year=date("Y")-1911;
if ($_GET[act]=="import") {
	$sql_newstud="select * from new_stud where stud_study_year='$new_sel_year' and sure_study<>'0' and class_year='$class_year_b'";
	$rs_newstud=$CONN->Execute($sql_newstud) or die($sql_newstud);
	echo $sql_newstud;
	while(!$rs_newstud->EOF){
		$stud_id=$rs_newstud->fields['stud_id'];
		$addr_zip=$rs_newstud->fields['addr_zip'];
		$stud_mschool_name=addslashes($rs_newstud->fields['old_school']);
		$sql_base="update stud_base set addr_zip='$addr_zip',stud_mschool_name='$stud_mschool_name' where stud_study_year='$new_sel_year' and stud_id='$stud_id'";
		$CONN->Execute($sql_base) or trigger_error($sql_base, E_USER_ERROR);
		$rs_newstud->MoveNext();
	}
	echo "資料補入完成";
}elseif($class_year_b==""){
$menu="
    <form name='form' method='post' action='{$_SERVER['PHP_SELF']}'>
		請選擇要寫入學籍資料表的年級：<br>
        <select name='class_year_b'>
            <option value='0' $selecteda>幼稚園</option>\n
            <option value='1' $selectedb>小一</option>\n
            <option value='2' $selectedc>小二</option>\n
            <option value='3' $selectedd>小三</option>\n
            <option value='4' $selectede>小四</option>\n
            <option value='5' $selectedf>小五</option>\n
            <option value='6' $selectedg>小六</option>\n
            <option value='7' $selectedh>國一</option>\n
            <option value='8' $selectedi>國二</option>\n
            <option value='9' $selectedj>國三</option>\n
            <option value='10' $selectedk>高一</option>\n
            <option value='11' $selectedl>高二</option>\n
            <option value='12' $selectedm>高三</option>\n
        </select>
        <input type='submit' name='submit' value='確定'>
    </form>";
echo $menu;

}
else{
    $sql="select * from new_stud where stud_study_year='$new_sel_year' and sure_study<>'0' and class_year='$class_year_b'";
    $rs=$CONN->Execute($sql) or die($sql);
    $i=0;
    $j=0;
    while(!$rs->EOF){
        $newstud_sn=$rs->fields['newstud_sn'];
        $stud_id[$i]=$rs->fields['stud_id'];
        $stud_name[$i]=$rs->fields['stud_name'];
        $class_year[$i]=$rs->fields['class_year'];
        $class_sort[$i]=$rs->fields['class_sort'];
        $class_site[$i]=$rs->fields['class_site'];
        if($stud_id[$i] && $class_year[$i] && $class_sort[$i] && $class_site[$i]) $j++;
        else echo $class_year[$i]."年級".$stud_name[$i]."未編班！<br> $j";
        $i++;
        $rs->MoveNext();
    }
    if($j<$i) echo "您尚有以上學生未完成編班，請先進行編班動作！";
    else{
    	
        //檢查學校的舊生是否已經升級了
        $sql_oldstud="select curr_class_num from stud_base where stud_study_cond='0'";
        $rs_oldstud=$CONN->Execute($sql_oldstud);
        $m=0;
        while(!$rs_oldstud->EOF){
            $student_sn[$m]=$rs_oldstud->fields['student_sn'];
            $curr_class_num[$m]=$rs_oldstud->fields['curr_class_num'];
            $curr_class_year[$m]=substr($curr_class_num[$m],0,-4);
            
            if($curr_class_year[$m]==$class_year_b){
                echo "系統中已有".$class_year_b."年級的資料，您可能已有匯入本學年度的新生資料了，或者您尚未完成升級的動作，若是此一情形，則請先進行舊生的升級!<br><a href='{$_SERVER['PHP_SELF']}?act=import&class_year_b=$class_year_b'>補入「原入學國小」及「郵遞區號」</a>";
                exit;
            }
            
            $m++;
            $rs_oldstud->MoveNext();
        }
       
        if($_GET['write']=="1"){
            //echo $class_year_b;
            $sql_newstud="select * from new_stud where stud_study_year='$new_sel_year' and sure_study<>'0' and class_year='$class_year_b'";
            $rs_newstud=$CONN->Execute($sql_newstud) or die($sql_newstud);
            $i=0; 
            
            while(!$rs_newstud->EOF){
            	 
                $newstud_sn=$rs_newstud->fields['newstud_sn'];
                $stud_id[$i]=$rs_newstud->fields['stud_id'];
                $stud_name[$i]=addslashes($rs_newstud->fields['stud_name']);
                $stud_study_year[$i]=$rs_newstud->fields['stud_study_year'];
                $class_year[$i]=$rs_newstud->fields['class_year'];
                $class_sort[$i]=$rs_newstud->fields['class_sort'];
                $class_site[$i]=$rs_newstud->fields['class_site'];
                $curr_class_num[$i]=sprintf("%d%02d%02d",$class_year[$i],$class_sort[$i],$class_site[$i]);
                $seme_year_seme[$i]=sprintf("%03d%d",$stud_study_year[$i],1);
                $seme_class[$i]=sprintf("%d%02d",$class_year[$i],$class_sort[$i]);
                $seme_num[$i]=$class_site[$i];
                $rs_cname=$CONN->Execute("select c_name from school_class where year='$stud_study_year[$i]' and semester='1' and c_year='$class_year[$i]' and c_sort='$class_sort[$i]' and enable=1");
                //echo "select c_name from school_class where year='$stud_study_year[$i]' and semester='1' and c_year='class_year[$i]' and c_sort='class_sort[$i]' and enable=1";
                $seme_class_name[$i]=$rs_cname->fields['c_name'];
                if($seme_class_name[$i]==""){
                    trigger_error("$stud_study_year[$i]學年第1學期的班級尚未設定",E_USER_ERROR);
                    exit;
                }                
                $stud_person_id[$i]=$rs_newstud->fields['stud_person_id'];
                $stud_sex[$i]=$rs_newstud->fields['stud_sex'];
                $stud_tel_1[$i]=$rs_newstud->fields['stud_tel_1'];
                $stud_birthday[$i]=$rs_newstud->fields['stud_birthday'];
                $guardian_name[$i]=addslashes($rs_newstud->fields['guardian_name']);
                $addr_1[$i]=addslashes($rs_newstud->fields['stud_address']);
                $addr_arr = change_addr($addr[$i]);
								$addr_zip[$i]=$rs_newstud->fields['addr_zip'];
								$stud_mschool_name[$i]=addslashes($rs_newstud->fields['old_school']);

                $addr_2[$i]=addslashes($rs_newstud->fields['stud_addr_2']); 					//聯絡地址 2012.05.03增加
                $stud_tel_3[$i]=$rs_newstud->fields['stud_tel_3']; 										//手機號碼 2012.05.03增加
                $stud_name_eng[$i]=addslashes($rs_newstud->fields['stud_name_eng']); 	//英文姓名 2012.05.03增加
                $addr_move_in[$i]=addslashes($rs_newstud->fields['addr_move_in']);    //戶籍遷入日期 2012.05.03增加
                $edu_key[$i] = hash('sha256', strtoupper($rs_newstud->fields['stud_person_id']));
                //新增到學籍資料表
                $sql_base="insert into stud_base(stud_id,stud_name,stud_name_eng,stud_person_id,stud_birthday,stud_sex,
                stud_study_cond,curr_class_num,stud_study_year,stud_addr_a,stud_addr_b,stud_addr_c,stud_addr_d,
                stud_addr_e,stud_addr_f,stud_addr_g,stud_addr_h,stud_addr_i,stud_addr_j,stud_addr_k,stud_addr_l,
                stud_addr_m,stud_addr_1,stud_addr_2,stud_tel_1,stud_tel_2,stud_tel_3,stud_kind,stud_mschool_name,
                addr_zip,addr_move_in, edu_key) values ('$stud_id[$i]','{$stud_name[$i]}','$stud_name_eng[$i] ','$stud_person_id[$i]',
                '$stud_birthday[$i]','$stud_sex[$i]','0','$curr_class_num[$i]','$stud_study_year[$i]','$addr_arr[0]','$addr_arr[1]',
                '$addr_arr[2]','$addr_arr[3]','$addr_arr[4]','$addr_arr[5]','$addr_arr[6]','$addr_arr[7]','$addr_arr[8]','$addr_arr[9]',
                '$addr_arr[10]','$addr_arr[11]','$addr_arr[12]','$addr_1[$i]','$addr_2[$i]','$stud_tel_1[$i]','$stud_tel_2[$i]',
                '$stud_tel_3[$i]','$stud_kind[$i]','$stud_mschool_name[$i]','$addr_zip[$i]','$addr_move_in[$i]','$edu_key[$i]')";
                $CONN->Execute($sql_base) or trigger_error($sql_base, E_USER_ERROR);
                
								$tmp_auto_inc_id=mysql_insert_id();

								//加入家庭狀況資料
								$sql_domicile="insert into stud_domicile (stud_id,fath_name,moth_name,guardian_name,student_sn) values('$stud_id[$i]','$fath_name','$moth_name','$guardian_name[$i]','$tmp_auto_inc_id')";
								$CONN->Execute($sql_domicile) or trigger_error($sql_domicile, E_USER_ERROR);
								//加入學年學期資料
								$sql_seme="insert into stud_seme (seme_year_seme,stud_id,seme_class,seme_num,seme_class_name,student_sn)  values ('$seme_year_seme[$i]','$stud_id[$i]','$seme_class[$i]','$seme_num[$i]','$seme_class_name[$i]','$tmp_auto_inc_id')";
								$CONN->Execute($sql_seme) or trigger_error($sql_seme, E_USER_ERROR);
                $i++;
                $rs_newstud->MoveNext();
            }
            echo "寫入了 ".$i." 筆新生資料！";
        }
        else{
            echo "按下面『寫入正式學籍資料表』，會將新生的資料寫入正式的學籍資料表！<br>";
            echo "<a href='{$_SERVER['PHP_SELF']}?write=1&class_year_b=$class_year_b'><span class='button'>寫入正式學籍資料表</span></a>";
        }
    }

}


//結束主網頁顯示區
echo "</td>";
echo "</tr>";
echo "</table>";

//程式檔尾
foot();


function change_addr($addr) {
	//縣市
	$temp_str = split_str($addr,"縣",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"市",1);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

      	//鄉鎮
	$temp_str = split_str($addr,"鄉",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"鎮",1);

	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"市",1);
	
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"區",1);

	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//村里
	$temp_str = split_str($addr,"村",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"里",1);

	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//鄰
	$temp_str = split_str($addr,"鄰");
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//路
	$temp_str = split_str($addr,"路",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"街",1);

	$res[] = $temp_str[0];
	$addr=$temp_str[1];

      	//段
	$temp_str = split_str($addr,"段");
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

      	//巷
	$temp_str = split_str($addr,"巷");
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//弄
	$temp_str = split_str($addr,"弄");
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//號
	$temp_str = split_str($addr,"號");
	$temp_arr = explode("-",$temp_str);
	if (sizeof($temp_arr)>1){
		$res[]=$temp_arr[0];
		$res[]=$temp_arr[1];
	}else {
		$res[]=$temp_str[0];
		$res[]="";
	}
	$addr=$temp_str[1];

	//樓
	$temp_str = split_str($addr,"樓");
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//樓之
	if ($addr != "")
		$temp_str = substr(chop($addr),2);
	else
		$temp_str ="";

	$res[]=$temp_str ;
      	return $res;
}

function split_str($addr,$str,$last=0) {
      	$temp = explode ($str, $addr);
	if (count($temp)<2 ){
		$t[0]="";
		$t[1]=$addr;
	}else{
		$t[0]=(!empty($last))?$temp[0].$str:$temp[0];
		$t[1]=$temp[1];
	}
	return $t;
}
?>