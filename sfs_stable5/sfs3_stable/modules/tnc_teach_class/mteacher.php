<?php

// $Id: mteacher.php 5310 2009-01-10 07:57:56Z hami $

// --系統設定檔
include "teach_config.php";

//--認證 session
sfs_check();

// 不需要 register_globals
/*
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}
*/
$teacher_sn=$_GET['teacher_sn'];
$sel=$_GET['sel'];
$act=$_POST['act'];

if ($act=="批次建立資料"){
	$main=&main_form();
	$main.=import($_FILES['userdata']['tmp_name'],$_FILES['userdata']['name'],$_FILES['userdata']['size']);
    alter_about_privielege();
}else{
	$main=&main_form();
}

//印出檔頭
head("批次建立教師資料");
echo $main;
foot();


//主要表格
function &main_form(){
	global $CONN,$teach_menu_p;
    $toolbar=&make_menu($teach_menu_p);
    $sql="select * from school_base";
    $rs=$CONN->Execute($sql) or die($sql) ;
    $sch_url=$rs->fields[sch_url];
	$main="
	$toolbar
	<table border='0' cellspacing='0' cellpadding='0' >
	<tr><td valign=top>
		<table cellspacing='1' cellpadding='10' class=main_body >
		<form action ='{$_SERVER['PHP_SELF']}' enctype='multipart/form-data' method=post>
		<tr><td  nowrap valign='top' bgcolor='#E1ECFF'>
		<p>請按『瀏覽』選擇匯入檔案來源：</p>
		<input type=file name='userdata'>
		<p><input type=submit name='act' value='批次建立資料'></p>
		</td>
		<td valign='top' bgcolor='#FFFFFF'>
		<p><b><font size='4'>教師資料批次建檔說明</font></b></p>
		<ol>
		<li>本程式為台南縣公立中小學配合人事系統建立教師基本與任職資料，其他資料，需至教職員資料管理程式建立。</li>
		<li>各校所需匯入的檔案請至<a href='http://cvs.tnc.edu.tw/people_thing/make_cvs_new2.php?sch_url=$sch_url'>台南縣學務系統教師資料區</a>下載。</li>
		<li>本程式會限制需由校內ip連線並需輸入行政密碼。</li>
        <li>匯入之後請自行修正相關資料。</li>
		</ol>
		</td>
		</tr>
		</table>
	</form>
	</td></tr></table>
	";
	return $main;
}


//匯入資料
function import($userdata,$userdata_name,$userdata_size){
	global $UPLOAD_PATH,$CONN;
    $temp_file= $UPLOAD_PATH."/teacher_data.csv";	
	if(is_file($temp_file)) unlink($temp_file);
	if ($userdata_size>0 && $userdata_name!=""){
		copy($userdata , $temp_file);
		$fd = fopen ($temp_file,"r");
		while ($tt = fgetcsv ($fd, 2000, ",")) {
			$teach_id = trim ($tt[0]);
			$teach_person_id = trim ($tt[1]);
			$name = trim (addslashes($tt[2]));
			$sex = trim ($tt[3]);
			$age = trim ($tt[4]);
			$birthday = trim ($tt[5]);
			$birth_place = trim ($tt[6]);
			$marriage = trim ($tt[7]);
			$address = trim (addslashes($tt[8]));
			$home_phone = trim ($tt[9]);
			$cell_phone = trim ($tt[10]);
			$office_home = trim ($tt[11]);
			$teach_condition = trim ($tt[12]);
			$teach_memo = trim ($tt[13]);
			$login_pass = trim ($tt[14]);
			$teach_edu_kind = trim ($tt[15]);
			$teach_edu_abroad = trim ($tt[16]);
			$teach_sub_kind = trim (addslashes($tt[17]));
			$teach_check_kind = trim ($tt[18]);
			$teach_check_word = trim (addslashes($tt[19]));
			$teach_is_cripple = trim ($tt[20]);
			$update_time = trim ($tt[21]);
			$post_kind = trim ($tt[22]);
			$post_office = trim ($tt[23]);
			$post_level = trim ($tt[24]);
			$official_level = trim ($tt[25]);
			$post_class = trim ($tt[26]);
			$post_num = trim ($tt[27]);
			$bywork_num = trim ($tt[28]);
			$salay = trim ($tt[29]);
			$appoint_date = trim ($tt[30]);
			$arrive_date = trim ($tt[31]);
			$approve_date = trim ($tt[32]);
			$approve_number = trim ($tt[33]);
			$teach_title_id = trim ($tt[34]);
			$update_id =$_SESSION['session_log_id'];
			$login_pass="password";
			//檢查該師的身分證是否已存於teacher_base，若無則新增，若無則更新
			$sql="select count(*) from teacher_base where teach_person_id='$teach_person_id' ";						
			$rs=$CONN->Execute($sql) or trigger_error("$sql",256);
			$ether_live=$rs->fields[0];
			if($ether_live>0){//存在，更新
				//teach_id,和login_pass不更新
				 //name='$name',sex='$sex',age='$age',birthday='$birthday',birth_place='$birth_place',marriage='$marriage',address='$address',home_phone='$home_phone',cell_phone='$cell_phone',office_home='$office_home',teach_condition='$teach_condition',teach_memo='$teach_memo',teach_edu_kind='$teach_edu_kind',teach_edu_abroad='$teach_edu_abroad',teach_sub_kind='$teach_sub_kind',teach_check_kind='$teach_check_kind',teach_check_word='$teach_check_word',teach_is_cripple='$teach_is_cripple',update_time='$update_time',update_id='$update_id'
				$sql2="update teacher_base set name='$name',sex='$sex',age='$age',birthday='$birthday',birth_place='$birth_place',marriage='$marriage',address='$address',home_phone='$home_phone',cell_phone='$cell_phone',office_home='$office_home',teach_condition='$teach_condition',teach_memo='$teach_memo',teach_edu_kind='$teach_edu_kind',teach_edu_abroad='$teach_edu_abroad',teach_sub_kind='$teach_sub_kind',teach_check_kind='$teach_check_kind',teach_check_word='$teach_check_word',teach_is_cripple='$teach_is_cripple',update_time='$update_time',update_id='$update_id'   where teach_person_id='$teach_person_id' ";
				$rs2=$CONN->Execute($sql2) or trigger_error("$sql2",256);
				if($rs2) $msg.="<font color='#DFCC3B'>$teach_person_id -- ".stripslashes($name)." </font><font color='#EAC0DF'>基本資料更新成功！</font><br>";
				else $msg.="<font color='#DFCC3B'>$teach_person_id -- ".stripslashes($name)." </font><font color='#D61414'>基本資料更新失敗！</font><br>";
			}else{//不存在，新增
				$sql2="insert into teacher_base(teach_id,teach_person_id,name,sex,age,birthday,birth_place,marriage,address,home_phone,cell_phone,office_home,teach_condition,teach_memo,login_pass,teach_edu_kind,teach_edu_abroad,teach_sub_kind,teach_check_kind,teach_check_word,teach_is_cripple,update_time,update_id) values('$teach_id','$teach_person_id','$name','$sex','$age','$birthday','$birth_place','$marriage','$address','$home_phone','$cell_phone','$office_home','$teach_condition','$teach_memo','$login_pass','$teach_edu_kind','$teach_edu_abroad','$teach_sub_kind','$teach_check_kind','$teach_check_word','$teach_is_cripple','$update_time','$update_id') ";
				$rs2=$CONN->Execute($sql2) or trigger_error("$sql2",256);
				if($rs2) $msg.="<font color='#DFCC3B'>$teach_person_id -- ".stripslashes($name)." </font><font color='#EAC0DF'>基本資料新增成功！</font><br>";
				else $msg.="<font color='#DFCC3B'>$teach_person_id -- ".stripslashes($name)." </font><font color='#D61414'>基本資料新增失敗！</font><br>";
			}
			//由該師的身分證於teacher_base找出teacher_sn，在由tetacher_sn找出是否已存於teach_post，若無則新增，若無則更新
			$sql3="select teacher_sn from teacher_base where teach_person_id='$teach_person_id' ";
			$rs3=$CONN->Execute($sql3) or trigger_error("$sql3",256);
			$teacher_sn=$rs3->fields['teacher_sn'];
			$sql4="select count(*) from teacher_post where teacher_sn='$teacher_sn' ";
			$rs4=$CONN->Execute($sql4) or trigger_error("$sql4",256);
			$ether_live2=$rs4->fields[0];
			if($ether_live2>0){//存在，更新
				//class_num（任教班級）不更新				
				$sql5="update teacher_post set post_kind='$post_kind',post_office='$post_office',post_level='$post_level',official_level='$official_level',post_class='$post_class',post_num='$post_num',bywork_num='$bywork_num',salay='$salay',appoint_date='$appoint_date',arrive_date='$arrive_date',approve_date='$approve_date',approve_number='$approve_number',teach_title_id='$teach_title_id',update_time='$update_time',update_id='$update_id'  where teacher_sn='$teacher_sn' ";
				$rs5=$CONN->Execute($sql5) or trigger_error("$sql5",256);
				if($rs5) $msg.="<font color='#A3C7FD'>$teach_person_id -- ".stripslashes($name)." </font><font color='#00C900'>基本資料更新成功！</font><br>";
				else $msg.="<font color='#A3C7FD'>$teach_person_id -- ".stripslashes($name)." </font><font color='#D61414'>基本資料更新失敗！</font><br>";
			}else{//不存在，新增
				$sql6="insert into teacher_post(teacher_sn,post_kind,post_office,post_level,official_level,post_class,post_num,bywork_num,salay,appoint_date,arrive_date,approve_date,approve_number,teach_title_id,update_time,update_id) values('$teacher_sn','$post_kind','$post_office','$post_level','$official_level','$post_class','$post_num','$bywork_num','$salay','$appoint_date','$arrive_date','$approve_date','$approve_number','$teach_title_id','$update_time','$update_id') ";
				$rs6=$CONN->Execute($sql6) or trigger_error("$sql6",256);
				if($rs6) $msg.="<font color='#A3C7FD'>$teach_person_id -- ".stripslashes($name)." </font><font color='#00C900'>任職資料新增成功！</font><br>";
				else $msg.="<font color='#A3C7FD'>$teach_person_id -- ".stripslashes($name)." </font><font color='#D61414'>任職資料新增失敗！</font><br>";
			}
			$i++;
		}
		fclose ($fd);
		if(is_file($temp_file)) unlink($temp_file);
    }
    else{
		$msg="檔案格式錯誤";
	}
	return $msg;
}
//配合學校端修改學務系統相關權限
function alter_about_privielege(){
    global $CONN;
    /*找出由人事系統匯入時的處室名稱和職稱名稱*/
    $sql="select * from teacher_post";
    $rs=$CONN->Execute($sql) or die($sql) ;
    $i=0;
    while(!$rs->EOF){
        $post_office_name[$i]=$rs->fields[post_office];
        //if(is_integer($post_office_name[$i])) unset($post_office_name[$i]);
        $teach_title_id_name[$i]=$rs->fields[teach_title_id];
        //if(is_integer($teach_title_id_name[$i])) unset($teach_title_id_name[$i]);
        $rs->MoveNext();
        $i++;
    }
    /*過濾重複的*/
    $new_post_office_name=deldup($post_office_name);
    $new_teach_title_id_name=deldup($teach_title_id_name);
    /*濾掉數字*/
    $new_post_office_name=delint($new_post_office_name);
    $new_teach_title_id_name=delint($new_teach_title_id_name);
    /*找出處室資料表的處室名稱*/
    $sql="select * from school_room where enable=1";
    $rs=$CONN->Execute($sql) or die($sql) ;
    $i=0;
    while(!$rs->EOF){
        $room_name[$i]=$rs->fields[room_name];
        $rs->MoveNext();
        $i++;
    }
    //$room_name=deldup($room_name);
    //for($j=0;$j<count($room_name);$j++){
    //    echo $room_name[$j];
    //}
    //echo count($room_name)."<br>";
    /*對照並新增到處室資料表*/
    $must_insert_room_name=delarray($new_post_office_name,$room_name);
    for($j=0;$j<count($must_insert_room_name);$j++){
        //echo $must_insert_room_name[$j];
        $sql="insert into school_room(room_name) values('$must_insert_room_name[$j]')";
        $CONN->Execute($sql) or die($sql) ;
    }
    /*找出職稱資料表的職稱名稱*/
    $sql="select * from teacher_title where enable=1";
    $rs=$CONN->Execute($sql) or die($sql) ;
    $i=0;
    while(!$rs->EOF){
        $title_name[$i]=$rs->fields[title_name];
        $rs->MoveNext();
        $i++;
    }
    /*對照並新增到職稱資料表*/
    $must_insert_title_name=delarray($new_teach_title_id_name,$title_name);
    for($j=0;$j<count($must_insert_title_name);$j++){
        //echo $must_insert_title_name[$j];
        $sql="insert into teacher_title(title_name) values('$must_insert_title_name[$j]')";
        $CONN->Execute($sql) or die($sql) ;
    }
    /*修改teacher_post資料表處室名稱為編號，職稱名稱為編號*/
    $sql="select * from teacher_post";
    $rs=$CONN->Execute($sql) or die($sql) ;
    $i=0;
    while(!$rs->EOF){
        $post_office_name[$i]=$rs->fields[post_office];
        $sql01="select room_id from school_room where room_name='$post_office_name[$i]' and enable=1";
        $rs01=$CONN->Execute($sql01) or die($sql01) ;
        $room_id[$i]=$rs01->fields[room_id];
        if($room_id[$i]){
            $CONN->Execute("UPDATE teacher_post SET post_office='$room_id[$i]' where post_office='$post_office_name[$i]'") or die("修改處室名稱為編號失敗！") ;
        }
        $teach_title_id_name[$i]=$rs->fields[teach_title_id];
        $sql02="select teach_title_id from teacher_title where title_name='$teach_title_id_name[$i]' and enable=1";
        $rs02=$CONN->Execute($sql02) or die($sql02) ;
        $teach_title_id[$i]=$rs02->fields[teach_title_id];
        if($teach_title_id[$i]){
            $CONN->Execute("UPDATE teacher_post SET teach_title_id='$teach_title_id[$i]' where teach_title_id='$teach_title_id_name[$i]'") or die("修改職稱名稱為編號失敗！") ;
        }
        $rs->MoveNext();
        $i++;
    }


}


//去除陣列中重覆的值
function  deldup($a){

        $i=count($a);
        for  ($j=0;$j<=$i;$j++){
                      for  ($k=0;$k<$j;$k++){
                                    if($a[$k]==$a[$j]){
                                            $a[$j]="";
                                    }
                      }
        }
        $q=0;
        for($r=0;$r<=$i;$r++){
                      if($a[$r]!=""){
                                      $d[$q]=$a[$r];
                                      $q++;
                      }
          }

return  $d;
}

//一個比較兩個陣列，然後去除重複的值的函數
function  delarray($a,$b){

                for($i=0;$i<count($a);$i++){
                            for($j=0;$j<count($b);$j++){
                                          if  ($a[$i]==$b[$j])  $a[$i]="";
                            }
                  }
                            $q=0;
                            for($r=0;$r<=$i;$r++){
                                                if($a[$r]!=""){
                                                                  $d[$q]=$a[$r];
                                                                  $q++;
                                                }
                              }
                      return  $d;
}

function delint($str){
    for($i=0;$i<count($str);$i++){
        if($str[$i]=="0") $str[$i]="1";
        $inter[$i]=intval($str[$i]);
    }
    $j=0;
    for($i=0;$i<count($inter);$i++){
        if($inter[$i]==0){
            $newstr[$j]=$str[$i];
            $j++;
        }
    }
    return $newstr;
}
?>
