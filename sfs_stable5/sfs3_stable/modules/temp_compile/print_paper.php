<?php

// $Id: print_paper.php 5310 2009-01-10 07:57:56Z hami $

/*引入學務系統設定檔*/
require "config.php";

if($_GET['Hstud_study_year']) $Hstud_study_year=$_GET['Hstud_study_year'];
else $Hstud_study_year=$_POST['Hstud_study_year'];
if($_GET['Hclass_year']) $Hclass_year=$_GET['Hclass_year'];
else $Hclass_year=$_POST['Hclass_year'];
if($_GET['Hclass_sort']) $Hclass_sort=$_GET['Hclass_sort'];
else $Hclass_sort=$_POST['Hclass_sort'];
if($_GET['ck']) $ck=$_GET['ck'];
else $ck=$_POST['ck'];

//使用者認證
sfs_check();

//程式檔頭
if(!$_GET['much'] && !$_GET['sort_g']) head("新生編班");

print_menu($menu_p);
//設定主網頁顯示區的背景顏色
echo "
<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc>
<tr>
<td bgcolor='#FFFFFF'>";
$stud_study_year_A=new_stud_study_year();
$ck=($ck==1)?"1":"0";
for($i=0;$i<count($stud_study_year_A);$i++){	
	$mod=$ck%2;	 
	$menu.="<a href='{$_SERVER['PHP_SELF']}?Hstud_study_year=$stud_study_year_A[$i]&ck=$ck'>".$stud_study_year_A[$i]."學年度</a><br>";		
	if($Hstud_study_year==$stud_study_year_A[$i] && $mod==0){		
		$class_year_sort_A=new_class_year_sort($Hstud_study_year);		
		for($j=0;$j<count($class_year_sort_A);$j++){			
			$B[$j]=explode("_",$class_year_sort_A[$j]);
			//echo $Hclass_year."=".$B[$j][0];
			if($Hclass_year==$B[$j][0] && $Hclass_sort==$B[$j][1]) $CSS[$j]="style='background-color: rgb(255, 255, 0);'";			
			$menu.="&nbsp;&nbsp;&nbsp;&nbsp;<span $CSS[$j]><a href='{$_SERVER['PHP_SELF']}?ck=0&Hstud_study_year=$Hstud_study_year&Hclass_year={$B[$j][0]}&Hclass_sort={$B[$j][1]}'>".$B[$j][0]."年".$B[$j][1]."班</a></span><br>";			
		}
	}
}
	if($Hstud_study_year && $Hclass_year && $Hclass_sort){
		$main_sql="select * from new_stud where stud_study_year='$Hstud_study_year' and class_year='$Hclass_year' and class_sort='$Hclass_sort' order by class_site";	//echo $main_sql;
		$main_rs=$CONN->Execute($main_sql);
    	$i=0;
		$main.="<tr bgcolor='#FFFFFF'><td colspan='6'>".$Hstud_study_year."學年度".$school_kind_name[$Hclass_year].$Hclass_sort."班新生名冊&nbsp;&nbsp;&nbsp;&nbsp;[<a href='dlsxw.php?stud_study_year=$Hstud_study_year&class_year=$Hclass_year&class_sort=$Hclass_sort'>下載sxw檔</a>]</td></tr><tr bgcolor='#FFFFFF'><td>姓名</td><td>學號</td><td>座號</td><td>性別</td><td>住址</td><td>電話</td></tr>";
		while(!$main_rs->EOF){        
			$stud_id[$i]= $main_rs->fields['stud_id'];
			$stud_name[$i]= $main_rs->fields['stud_name'];		
			$class_site[$i]= $main_rs->fields['class_site'];
			$stud_sex[$i]= $main_rs->fields['stud_sex'];	
			$stud_sex_name[$i]=($stud_sex[$i]=="1")?"男":"女";		
			$stud_address[$i]= $main_rs->fields['stud_address'];	
			$stud_tel_1[$i]= $main_rs->fields['stud_tel_1'];
			$main.="<tr bgcolor='#FFFFFF'><td>".$stud_name[$i]."</td><td>".$stud_id[$i]."</td><td>".$class_site[$i]."</td><td>".$stud_sex_name[$i]."</td><td>".$stud_address[$i]."</td><td>".$stud_tel_1[$i]."</td></tr>";
			$i++;
			$main_rs->MoveNext(); 
		}
	}
echo "<table bgcolor='black' border='0' cellpadding='2' cellspacing='1' width='99%'><tr bgcolor='#FACDEF'><td valign='top'>$menu</td><td width='85%' valign='top'><table bgcolor='black' border='0' cellpadding='2' cellspacing='1'>$main</table></td></tr></table>";
//結束主網頁顯示區
echo "</td>";
echo "</tr>";
echo "</table>";
foot();
//取得資料表new_stud的所有入學年度
function  new_stud_study_year(){
    global $CONN;
    $sql="select stud_study_year from new_stud";
	$rs=$CONN->Execute($sql);
    $i=0;
    $AAA = array ();
	while(!$rs->EOF){
        $stud_study_year[$i]= $rs->fields['stud_study_year'];		
		//加入陣列之中
		if (!in_array($stud_study_year[$i], $AAA)) $AAA[]=$stud_study_year[$i];				
		$i++;
        $rs->MoveNext();
    }
return  $AAA;
}

//取得資料表new_stud該學年度的所有班級
function  new_class_year_sort($stud_study_year){
    global $CONN;
    $sql="select class_year,class_sort from new_stud where 
stud_study_year='$stud_study_year'";	$rs=$CONN->Execute($sql);
    $i=0;
    $AAA = array ();
	while(!$rs->EOF){
        $class_year[$i]= $rs->fields['class_year'];
		$class_sort[$i]= $rs->fields['class_sort'];
		$class_year_sort[$i]=$class_year[$i]."_".$class_sort[$i];
		//加入陣列之中
		if (!in_array($class_year_sort[$i], $AAA) && $class_year[$i]!="" && 
$class_sort[$i]!="") $AAA[]=$class_year_sort[$i];		$i++;
        $rs->MoveNext();
    }
return  $AAA;
}

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
?>
