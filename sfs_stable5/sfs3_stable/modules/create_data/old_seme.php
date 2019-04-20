<?php

// $Id: old_seme.php 6849 2012-08-07 00:30:37Z hami $

// --系統設定檔
require "create_data_config.php";

//--認證 session
sfs_check();

$year_seme=($_GET['year_seme'])?$_GET['year_seme']:$_POST['year_seme'];
$year_name=($_GET['year_name'])?$_GET['year_name']:$_POST['year_name'];
$me=($_GET['me'])?$_GET['me']:$_POST['me'];
$Submit11=($_GET['Submit11'])?$_GET['Submit11']:$_POST['Submit11'];
while(list($key , $val) = each($_POST['stud_id_num'])) {
	$stud_id_num[$key]=$val;    
}
$old_year=($_GET['old_year'])?$_GET['old_year']:$_POST['old_year'];
$old_seme=($_GET['old_seme'])?$_GET['old_seme']:$_POST['old_seme'];
$source_year=($_GET['source_year'])?$_GET['source_year']:$_POST['source_year'];

//印出檔頭
head("複製學生的學期資料");
print_menu($menu_p);
echo "<table cellspacing='1' cellpadding='10' class=main_body width='98%'><tr bgcolor='#E1ECFF'><td>";
//開始主網頁顯示區---------------------------------------------------------------------------------------
	
	if($Submit11=="複製"){		
		$old_year=trim($old_year);
		$old_seme=trim($old_seme);
		$old_year_seme=sprintf("%03d%d",$old_year,$old_seme);
		while(list($key , $val) = each($stud_id_num)) {
			//先檢查，若無再寫入
			//echo $val."<br>";
			$val_A=explode("_",$val);
			$stud_id=$val_A[0];
			$seme_class=$val_A[1];
			$seme_num=$val_A[2];
			$student_sn=$val_A[3];
			//echo substr("$seme_class",0,-2)."-".($source_year-$old_year)."------".$seme_num."<br>";			
			$old_seme_class=(intval(substr("$seme_class",0,-2))-($source_year-$old_year)).substr("$seme_class",-2);
			if($old_seme_class<1){ echo "執行錯誤，請重新操作！"; break;}
			else {
				//班名查詢
				$AAA=intval(substr("$seme_class",0,-2))-($source_year-$old_year);
				$BBB=intval(substr("$seme_class",-2));
				$class_name_sql="select c_name from school_class where year='$old_year' and semester='$old_seme' and c_year='$AAA' and c_sort='$BBB'";				
				$class_name_rs=$CONN->Execute($class_name_sql) or die($class_name_sql);
				$seme_class_name=$class_name_rs->fields["c_name"];
				if($seme_class_name=="")
					trigger_error("對不起！".$old_year."學年度第".$old_seme."學期的班級尚未設定！",E_USER_ERROR);				
			}
			//echo $old_seme_class."<br>";
			$rs_chk=$CONN->Execute("select seme_class from stud_seme where stud_id='$stud_id' and  seme_year_seme='$old_year_seme'");
			$seme_class=$rs_chk->fields["seme_class"];
			if($seme_class==""){
				$insert_seme="insert into stud_seme (seme_year_seme,stud_id,seme_class,seme_class_name,seme_num,student_sn) values('$old_year_seme','$stud_id','$old_seme_class','$seme_class_name','$seme_num','$student_sn')";
				$CONN->Execute($insert_seme);
				$msg.= "<br>$old_year_seme---$stud_id---$old_seme_class---$seme_num";
			}
			else $msg.= "<br>".$old_year_seme."的".$stud_id."已經存在";	
		}
	
	}
		
	//學期選單
	$col_name="year_seme";
    $id=$year_seme;    
	$show_year_seme=select_year_seme($id,$col_name);
    $year_seme_menu="
        <form name='form0' method='post' action='{$_SERVER['PHP_SELF']}'>
            <select name='$col_name' onChange='jumpMenu0()'>
                $show_year_seme
            </select>
        </form>";
	
	//年級選單
    if($year_seme){
	$year_seme_A=explode("_",$year_seme);
	$sel_year=$year_seme_A[0];
	$sel_seme=$year_seme_A[1];
	$col_name="year_name";
    $id=$year_name;
    $show_class_year=select_school_class($id,$col_name,$sel_year,$sel_seme);
    $class_year_menu="
        <form name='form1' method='post' action='{$_SERVER['PHP_SELF']}'>
            <select name='$col_name' onChange='jumpMenu1()'>
                $show_class_year
            </select>
			<input type='hidden' name='year_seme' value='$year_seme'>
        </form>";
	}
	
    //班級選單
    if($year_seme && $year_name){
		$year_seme_A=explode("_",$year_seme);
		$sel_year=$year_seme_A[0];
		$sel_seme=$year_seme_A[1];        
		$col_name="me";
        $id=$me;
        $show_class_year_name=select_school_class_name($year_name,$id,$col_name,$sel_year,$sel_seme);
        $class_year_name_menu="
        <form name='form2' method='post' action='{$_SERVER['PHP_SELF']}'>
            <select name='$col_name' onChange='jumpMenu2()'>
                $show_class_year_name
            </select>
            <input type='hidden' name='year_name' value='$year_name'>
			<input type='hidden' name='year_seme' value='$year_seme'>
        </form>";
    }
	//echo $year_seme.$year_name.$me;	
	echo "<table cellspacing='1' cellpadding='10' bgcolor='#F4C2FF' width='100%'><tr bgcolor='#FFFFFF'><td width='5%' valign='top'>$year_seme_menu$class_year_menu$class_year_name_menu</td>";
	
	if($year_seme && $year_name && $me){
		$select_year_seme=sprintf("%03d%d",$sel_year,$sel_seme);
		$not_all=sprintf("%d%02d",$year_name,$me);
		$seme_class_s=($me=="all")?" seme_class like '$year_name%'":" seme_class='$not_all'";
		$stud_list_sql="select * from stud_seme where seme_year_seme='$select_year_seme' and  $seme_class_s";
		//echo $stud_list_sql;
		$stud_list_rs=$CONN->Execute($stud_list_sql) or die($stud_list_sql);
		echo "<form name='form11' method='post' action='{$_SERVER['PHP_SELF']}'>";
		echo "<td rowspan='2' valign='top'  width='5%'><input type='submit' name='Submit11' value='複製'></td>";
		echo "<td rowspan='2' valign='top'><input type='text' name='old_year' value='$old_year' size='4' maxlength='3'>學年度<select name='old_seme'><option value='1'>上</option><option value='2'>下</option></select>學期</td></tr>";				
		$i=0;
    	echo "<tr bgcolor='#F4DBFF'><td colspan='3'>";
		while (!$stud_list_rs->EOF) {
	        	$stud_id[$i]=$stud_list_rs->fields["stud_id"]; 
	        	$student_sn[$i]=$stud_list_rs->fields["student_sn"]; 
			$seme_class[$i]=$stud_list_rs->fields["seme_class"];
			$seme_num [$i]=$stud_list_rs->fields["seme_num"];     
        	echo $stud_id[$i]." ".stud_id_to_stud_name($stud_id[$i])."<br>";
			echo "<input type='hidden' name='source_year' value='$sel_year'>";
			echo "<input type='hidden' name='stud_id_num[$i]' value='$stud_id[$i]_$seme_class[$i]_$seme_num[$i]_$student_sn[$i]'>";
			$i++;
        	$stud_list_rs->MoveNext();
    	}
		echo "</form>";
		echo "</td></tr></table>";
	}
	else echo "<td width='20%' valign='top'>
						程式說明：<br>
						&nbsp;&nbsp;本程式主要是提供舊生快速補上之前的學期資料<br>
						使用限制	：<br>
						&nbsp;&nbsp;1.您必須先將該學期的班級先做設定<br>
						&nbsp;&nbsp;2.補上之班級，座號會沿用目前的設定<br>
						使用說明：<br>
						&nbsp;&nbsp;首先選擇來源的學期學生資料<br>
						&nbsp;&nbsp;再設定要複製的學期，程式就會將學生資料做複製<br>
						&nbsp;&nbsp;並會選擇適當的年級<br>
						</td></tr></table>";
	echo $msg;
//結束主網頁顯示區---------------------------------------------------------------------------------------
echo "</td></tr></table>";

foot();

//本校目前學年與學期下拉式選單
function select_year_seme($id,$col_name){
    global $CONN;
    $sql="select * from school_class";
    $rs=$CONN->Execute($sql);

    $option="<option value=''>選擇學年度</option>\n";
    $i=0;
    while (!$rs->EOF) {
        $year[$i]=$rs->fields["year"];
        $semester[$i]=$rs->fields['semester'];
        $year_semester[$i]=$year[$i]."_".$semester[$i];
        $i++;
        $rs->MoveNext();
    }
    $year_semester=deldup($year_semester);
    for($i=0;$i<count($year_semester);$i++){
        $selected=($id==$year_semester[$i])?"selected":"";
        $YS=explode("_",$year_semester[$i]);
        $option.="<option value='$year_semester[$i]' $selected>".$YS[0]."學年度第".$YS[1]."學期</option>\n";
    }
    $select_school_class="<select name='$col_name'>$option</select>";
	//return $select_school_class;
    return $option;
}

//本校目前年級下拉式選單
function select_school_class($id,$col_name,$sel_year,$sel_seme){
    global $CONN;
    $sql="select * from school_class where year=$sel_year and semester=$sel_seme";
    $rs=$CONN->Execute($sql);
    $school_kind_name=array("幼稚園","一年","二年","三年","四年","五年","六年","一年","二年","三年","一年","二年","三年");
    $option="<option value=''>選擇年級</option>\n";
    $i=0;
    while (!$rs->EOF) {
        $c_year[$i]=$rs->fields["c_year"];
        $i++;
        $rs->MoveNext();
    }
    if($i==0) trigger_error("對不起！您尚未設定班級！",E_USER_ERROR);
    $c_year=deldup($c_year);
    for($i=0;$i<count($c_year);$i++){
        $selected=($id==$c_year[$i])?"selected":"";
        $option.="<option value='$c_year[$i]' $selected>".$school_kind_name[$c_year[$i]]."級</option>\n";
    }
    $select_school_class="<select name='$col_name'>$option</select>";
	//return $select_school_class;
    return $option;
}

//本校目前該年級的所有班級下拉式選單
function select_school_class_name($c_year,$id,$col_name,$sel_year,$sel_seme){
    global $CONN;
    if(empty($c_year)) $c_year=1;
    $sql="select * from school_class where year=$sel_year and semester=$sel_seme and c_year=$c_year";
    $rs=$CONN->Execute($sql);
    $option="<option value=''>選擇班級</option>\n";
	$c=($id=="all")?"selected":"";
	$option.="<option value='all' $c>該年所有班</option>\n";
    $i=0;
    while (!$rs->EOF) {
        $c_name[$i]=$rs->fields["c_name"];
        $c_sort[$i]=$rs->fields["c_sort"];
        $i++;
        $rs->MoveNext();
    }
    if($i==0) trigger_error("對不起！您尚未設定班級！",E_USER_ERROR);
    $c_name=deldup($c_name);
    $c_sort=deldup($c_sort);
    for($i=0;$i<count($c_name);$i++){
        $selected=($id==$c_sort[$i])?"selected":"";
        $option.="<option value='$c_sort[$i]' $selected>".$c_name[$i]."班</option>\n";
    }
    $select_school_class_name="<select name='$col_name'>$option</select>";
	//return $select_school_class_name;
    return $option;
}

//一個比較兩個陣列，然後去除重複的值的函數
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

//由stud_id找出學生的姓名
function  stud_id_to_stud_name($stud_id){
    global $CONN;
    $rs=&$CONN->Execute("select  stud_name  from  stud_base where stud_id='$stud_id'");
    $stud_name=$rs->fields['stud_name'];
    return $stud_name;
}

?>

<script language="JavaScript1.2">
<!-- Begin
function jumpMenu0(){
	var str, classstr ;
 if (document.form0.year_seme.options[document.form0.year_seme.selectedIndex].value!="") {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" + document.form0.year_seme.options[document.form0.year_seme.selectedIndex].value;
	}
}

function jumpMenu1(){
	var str, classstr ;
 if ((document.form1.year_name.value!="") & (document.form1.year_name.options[document.form1.year_name.selectedIndex].value!="")) {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" + document.form1.year_seme.value + "&year_name=" + document.form1.year_name.options[document.form1.year_name.selectedIndex].value;
	}
}

function jumpMenu2(){
	var str, classstr ;
 if ((document.form2.year_name.value!="") & (document.form2.me.options[document.form2.me.selectedIndex].value!="")) {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" + document.form2.year_seme.value + "&year_name=" + document.form2.year_name.value + "&me=" + document.form2.me.options[document.form2.me.selectedIndex].value;
	}
}


//  End -->
</script>
