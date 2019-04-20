<?php

// 引入 SFS3 的函式庫
include "../../include/config.php";
include "../../include/sfs_case_dataarray.php";

// 引入您自己的 config.php 檔
require "config.php";

// 認證
sfs_check();



//轉換成全域變數
$act=($_POST['act'])?"{$_POST['act']}":"{$_GET['act']}";
$test_sort=($_POST['test_sort'])?"{$_POST['test_sort']}":"{$_GET['test_sort']}";
$class_num=($_POST['class_num'])?"{$_POST['class_num']}":"{$_GET['class_num']}";
$student_sn=($_POST['student_sn'])?"{$_POST['student_sn']}":"{$_GET['student_sn']}";
$class_seme=($_POST['class_seme'])?"{$_POST['class_seme']}":"{$_GET['class_seme']}";
$class_base=($_POST['class_base'])?"{$_POST['class_base']}":"{$_GET['class_base']}";
$curr_year=($_POST['curr_year'])?"{$_POST['curr_year']}":"{$_GET['curr_year']}";
$curr_seme=($_POST['curr_seme'])?"{$_POST['curr_seme']}":"{$_GET['curr_seme']}";

$add_nor=$_GET['add_nor'];
$add_wet=$_GET['add_wet'];
$add_teacher=$_GET['add_teacher'];
$add_date=$_GET['add_date'];
//if(!$curr_year) $curr_year = curr_year();
//if(!$curr_seme) $curr_seme = curr_seme();

    if($class_seme) {
		$curr_year = intval(substr($class_seme,0,-1));
		$curr_seme =  substr($class_seme,-1);				
	} 
	else{
		$curr_year = curr_year();
		$curr_seme = curr_seme();	
	}





if($act=="dl_oo_one"){
    
	ooo_one($test_sort,$class_num,$student_sn);

}
elseif($act=="dl_oo_class"){

	ooo_class($test_sort,$class_num);

}
elseif($act=="dl_pdf_one"){	
	
	
		if($add_nor){
			$checked=" checked";
			$ratio=test_ratio($curr_year,$curr_seme);//本學期的成績設定
			$R0=($ratio[substr($class_num,0,-2)][$test_sort-1][0])*100/($ratio[substr($class_num,0,-2)][$test_sort-1][0] + $ratio[substr($class_num,0,-2)][$test_sort-1][1]);
			$R1=($ratio[substr($class_num,0,-2)][$test_sort-1][1])*100/($ratio[substr($class_num,0,-2)][$test_sort-1][0] + $ratio[substr($class_num,0,-2)][$test_sort-1][1]);
			
		if (ceil($R0)!=$R0)$R0=round($R0,2);
		if (ceil($R1)!=$R1)$R1=round($R1,2);
		}
		if($add_wet){
			$wchecked=" checked";
		}
		
		if($add_teacher){
			$tchecked=" checked";
		}
		
		if($add_date){
			$dchecked=" checked";
		}
		
		//成績單標題
		$title=$school_short_name.$curr_year."學年度第".$curr_seme."學期第".$test_sort."次定期考查\n";
		if(sizeof($curr_year)==2)$curr_year="0".$curr_year;	
		$class_id=$curr_year."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));
		$st_arr=student_sn_to_name_num($student_sn);
		$st=student_sn_to_id_name_num($student_sn,$curr_year,$curr_seme);        

		$cla_arr=class_id_to_full_class_name($class_id);
		$title.="班級：".$cla_arr."\n姓名：".$st_arr[1]." 座號：".$st[2];		
		if($add_nor) $header=array("科目","月考*$R0%","平時*$R1%","成績");
		else $header=array("科目","成績");
		//if(sizeof($curr_year)<3) $curr_year="0".$curr_year;
		//$class_id=$curr_year."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));
		//科目
		$SS=class_id2subject($class_id);
		$i=0;
		$i_nor=0;
		//$total=0;
		//$total_nor=0;
		$k=0;
		$data=array();
		foreach($SS as $ss_id => $s_name){
			$data[$k]=array();
			$wet=subj_wet($ss_id);
			$an_score="";
			if($add_nor){
				//平時考成績
				$score_b_nor[$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="平時成績",$test_sort);
				if($score_b_nor[$ss_id]==-100) $score_b_nor[$ss_id]="";
				if($score_b_nor[$ss_id]!="") {
					$i_nor++;
					if($add_wet) {
						$total_nor=$total_nor+$score_b_nor[$ss_id]*$wet;
						$i_nor_wet=$i_nor_wet+$wet;
					}
					else $total_nor=$total_nor+$score_b_nor[$ss_id];
				}
			}
			//月考成績
			$score_b[$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
			if($score_b[$ss_id]==-100) $score_b[$ss_id]="";
			if($score_b[$ss_id]!="") {
				$i++;
				if($add_wet) {
					$total=$total+$score_b[$ss_id]*$wet;
					$i_wet=$i_wet+$wet;
				}
				else $total=$total+$score_b[$ss_id];
			}

			if($add_wet){
				if($add_nor){
					if($score_b[$ss_id] || $score_b_nor[$ss_id]) {
						$an_score=((($score_b[$ss_id]*$R0)+($score_b_nor[$ss_id]*$R1))/($R0+$R1));
						
						$an_score=number_format($an_score,2);
						
						$an_total=$an_total+$an_score*$wet;
					}
					
				    $score_b[$ss_id]=number_format($score_b[$ss_id],2);
					$score_b_nor[$ss_id]=number_format($score_b_nor[$ss_id],2);
					
					
					array_push($data[$k],"$s_name*$wet","$score_b[$ss_id]","$score_b_nor[$ss_id]","$an_score");
					//echo "$s_name*$wet","$score_b[$ss_id]","$score_b_nor[$ss_id] <br>";
				}else{
					
					 $score_b[$ss_id]=number_format($score_b[$ss_id],2);
					
					array_push($data[$k],"$s_name*$wet","$score_b[$ss_id]");
				}
			}else{
				if($add_nor){
					if($score_b[$ss_id] || $score_b_nor[$ss_id]) {
						$an_score=((($score_b[$ss_id]*$R0)+($score_b_nor[$ss_id]*$R1))/($R0+$R1));
						
						$an_score=number_format($an_score,2);
						
						$an_total=$an_total+$an_score;
					}
					
				    $score_b[$ss_id]=number_format($score_b[$ss_id],2);
					$score_b_nor[$ss_id]=number_format($score_b_nor[$ss_id],2);
					
					array_push($data[$k],"$s_name","$score_b[$ss_id]","$score_b_nor[$ss_id]","$an_score");
				}else{
					
					 $score_b[$ss_id]=number_format($score_b[$ss_id],2);
					array_push($data[$k],"$s_name","$score_b[$ss_id]");
				}
			}
			$k++;
		}

		$data[$k]=array();
		if($add_wet){
			if($add_nor){
				array_push($data[$k],"總分"," "," ","$an_total");
			}else{
				array_push($data[$k],"總分","$total");
			}
		}else{
			if($add_nor){
				array_push($data[$k],"總分"," "," ","$an_total");
			}else{
				array_push($data[$k],"總分","$total");
			}
		}
		$k++;
		$data[$k]=array();
		if($add_wet){
			if($add_nor) {
				if(max($i_wet,$i_nor_wet)) $mi=max($i_wet,$i_nor_wet);
				if($an_total) $aver=round($an_total/$mi,2);
				array_push($data[$k],"平均"," "," ","$aver");
			}else{
				if($i_wet>0) $aver=round($total/$i_wet,2);
				array_push($data[$k],"平均","$aver");
			}
		}else{
			if($add_nor) {
				if(max($i,$i_nor)) $mi=max($i,$i_nor);
				if($an_total) $aver=round($an_total/$mi,2);
				array_push($data[$k],"平均"," "," ","$aver");
			}else{
				if($i>0) $aver=round($total/$i,2);
				array_push($data[$k],"平均","$aver");
			}
		}
    
	$comment2="";
	
	if ($add_teacher)
	{
	   //取得某學期某班導師姓名	
      
        $class_teacher_all=class_teacher();
        $teacher=$class_teacher_all[$class_id];
		
		$comment2="導師：$teacher \n家長：";
	}
	if ($add_date)$comment2.="\n      列印日期:".date("Y-m-d");
	creat_pdf($title,$header,$data,$comment1,$comment2);
}
elseif($act=="dl_pdf_class"){
	if($add_nor){
		
		$checked=" checked";
		$class_seme=($_POST['class_seme'])?"{$_POST['class_seme']}":"{$_GET['class_seme']}";
        $class_base=($_POST['class_base'])?"{$_POST['class_base']}":"{$_GET['class_base']}";
		
		$ratio=test_ratio($curr_year,$curr_seme);//本學期的成績設定		
		$R0=($ratio[substr($class_num,0,-2)][$test_sort-1][0])*100/($ratio[substr($class_num,0,-2)][$test_sort-1][0] + $ratio[substr($class_num,0,-2)][$test_sort-1][1]);
		$R1=($ratio[substr($class_num,0,-2)][$test_sort-1][1])*100/($ratio[substr($class_num,0,-2)][$test_sort-1][0] + $ratio[substr($class_num,0,-2)][$test_sort-1][1]);
		
		if (ceil($R0)!=$R0)$R0=round($R0,2);
		if (ceil($R1)!=$R1)$R1=round($R1,2);
		
	}
	if($add_wet){
		$wchecked=" checked";
	}
	
	if($add_teacher){
			$tchecked=" checked";
	}
	
	if($add_date){
	        $dchecked=" checked";
	 }

	$class_id=sprintf("%03d",$curr_year)."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));
	$student_sn_arr=class_id_to_seme_student_sn($class_id,$yn='0');
	$class=class_id_to_full_class_name($class_id);
	$title=$school_short_name.$curr_year."學年度第".$curr_seme."學期"."第".$test_sort."次定期考查\n".$class;

	if($add_nor) $header=array("科目","月考*$R0%","平時*$R1%","成績");
	else $header=array("科目","成績");

	$data=array();
	$m=0;
	foreach($student_sn_arr as $student_sn){
		$data[$m]=array();
		$st=student_sn_to_id_name_num($student_sn,$curr_year,$curr_seme);
		$name=$st[1];
		$num=$st[2];
		$comment1[]="姓名：".$name." 座號：".$num;

		//科目
		$count[$student_sn]=0;
		$SS=class_id2subject($class_id);
		$k=0;
		$i[$m]=0;
		foreach($SS as $ss_id => $s_name){
			$data[$m][$k]=array();
			$wet=subj_wet($ss_id);
			$an_score="";
			if($add_nor){
				//平時考成績
				$score_b_nor[$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="平時成績",$test_sort);
				if($score_b_nor[$ss_id]==-100) $score_b_nor[$ss_id]="";
				if($score_b_nor[$ss_id]!="") {
					$i_nor[$m]++;
					if($add_wet) {
						$total_nor=$total_nor+$score_b_nor[$ss_id]*$wet;
						$i_nor_wet[$m]=$i_nor_wet[$m]+$wet;
					}
					else $total_nor=$total_nor+$score_b_nor[$ss_id];
				}
			}
			//月考成績
			$score_b[$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
			if($score_b[$ss_id]==-100) $score_b[$ss_id]="";
			if($score_b[$ss_id]!="") {
				$i[$m]++;
				if($add_wet) {
					$total[$m]=$total[$m]+$score_b[$ss_id]*$wet;
					$i_wet[$m]=$i_wet[$m]+$wet;
				}
				else $total[$m]=$total[$m]+$score_b[$ss_id];
			}

			if($add_wet){
				if($add_nor){
					if($score_b[$ss_id] || $score_b_nor[$ss_id]) {
						$an_score=((($score_b[$ss_id]*$R0)+($score_b_nor[$ss_id]*$R1))/($R0+$R1));
						
						$an_score=number_format($an_score,2);
						
						$an_total[$m]=$an_total[$m]+$an_score*$wet;
					}
					
				    $score_b[$ss_id]=number_format($score_b[$ss_id],2);
					$score_b_nor[$ss_id]=number_format($score_b_nor[$ss_id],2);
					
					array_push($data[$m][$k],"$s_name*$wet","$score_b[$ss_id]","$score_b_nor[$ss_id]","$an_score");
				}else{
					
					
					 $score_b[$ss_id]=number_format($score_b[$ss_id],2);

					array_push($data[$m][$k],"$s_name*$wet","$score_b[$ss_id]");
				}
			}else{
				if($add_nor){
					if($score_b[$ss_id] || $score_b_nor[$ss_id]) {
						$an_score=((($score_b[$ss_id]*$R0)+($score_b_nor[$ss_id]*$R1))/($R0+$R1));
						
						$an_score=number_format($an_score,2);
						
						$an_total[$m]=$an_total[$m]+$an_score;
					}
					
					$score_b[$ss_id]=number_format($score_b[$ss_id],2);
					$score_b_nor[$ss_id]=number_format($score_b_nor[$ss_id],2);
					
					array_push($data[$m][$k],"$s_name","$score_b[$ss_id]","$score_b_nor[$ss_id]","$an_score");
				}else{
					
					$score_b[$ss_id]=number_format($score_b[$ss_id],2);
					
					array_push($data[$m][$k],"$s_name","$score_b[$ss_id]");
				}
			}
			$k++;
		}

		$data[$m][$k]=array();
		if($add_wet){
			if($add_nor){
				array_push($data[$m][$k],"總分"," "," ","$an_total[$m]");
			}else{
				array_push($data[$m][$k],"總分","$total[$m]");
			}
		}else{
			if($add_nor){
				array_push($data[$m][$k],"總分"," "," ","$an_total[$m]");
			}else{
				array_push($data[$m][$k],"總分","$total[$m]");
			}
		}
		$k++;
		$data[$m][$k]=array();
		if($add_wet){
			if($add_nor) {
				if(max($i_wet[$m],$i_nor_wet[$m])) $mi[$m]=max($i_wet[$m],$i_nor_wet[$m]);
				if($an_total[$m]) $aver[$m]=round($an_total[$m]/$mi[$m],2);
				array_push($data[$m][$k],"平均"," "," ","$aver[$m]");
			}else{
				if($i_wet[$m]>0) $aver[$m]=round($total[$m]/$i_wet[$m],2);
				array_push($data[$m][$k],"平均","$aver[$m]");
			}
		}else{
			if($add_nor) {
				if(max($i[$m],$i_nor[$m])) $mi[$m]=max($i[$m],$i_nor[$m]);
				if($an_total[$m]) $aver[$m]=round($an_total[$m]/$mi[$m],2);
				array_push($data[$m][$k],"平均"," "," ","$aver[$m]");
			}else{
				if($i[$m]>0) $aver[$m]=round($total[$m]/$i[$m],2);
				array_push($data[$m][$k],"平均","$aver[$m]");
			}
		}
		$m++;
	}

	$comment2="";
	if ($add_teacher)
	{
	   //取得某學期某班導師姓名	
       $class_teacher_all=class_teacher();
       $teacher=$class_teacher_all[$class_id];
		
		$comment2="導師：$teacher \n家長：";
	}
	if ($add_date)$comment2.="\n      列印日期:".date("Y-m-d");
	//print_r($data);
	creat_pdf($title,$header,$data,$comment1,$comment2);
}

else{
	// 叫用 SFS3 的版頭
	head("月考成績單");
	
	// 您的程式碼由此開始
	print_menu($menu_p);

	//學年學期班級選單
	$class_seme_array=get_class_seme();
	$class_seme_select.="<form action='{$_SERVER['PHP_SELF']}' method='POST' name='form1'>\n<select  name='class_seme' onchange='this.form.submit()'>\n";
	$i=0;
	foreach($class_seme_array as $k => $v){
		if(!$class_seme) $class_seme=sprintf("%03d%d",curr_year(),curr_seme());
		$selected[$i]=($class_seme==$k)?" selected":" ";	
		$class_seme_select.="<option value='$k'$selected[$i] >$v</option> \n";
		$i++;
	}		
	$class_seme_select.="</select></form>";
	
	
	$class_base_array=class_base($class_seme);
	$class_base_select.="<form action='{$_SERVER['PHP_SELF']}' method='POST' name='form2'>\n<select  name='class_base' onchange='this.form.submit()'>\n";
	$j=0;
	foreach($class_base_array as $k2 => $v2){
		if(!$class_base) $class_base=$k2;
		$selected2[$j]=($class_base==$k2)?" selected":" ";	
		$class_base_select.="<option value='$k2'$selected2[$j] >$v2</option> \n";
		$j++;
	}
	$class_base_select.="</select><input type='hidden' name='class_seme' value='$class_seme'></form>\n";
	$menu="<td nowrap width='1%' align='left'> $class_seme_select </td><td nowrap width='1%' align='left'> $class_base_select </td>";
	$class_num=$class_base;
	
	$curr_year = intval(substr($class_seme,0,-1));
	$curr_seme =  substr($class_seme,-1);	
	
	
	if($class_num){
		//階段選單
		$option=test_sort_select($curr_year,$curr_seme,$class_num);
		//if($test_sort)	$download="<td nowrap  align='left' width='96%'><font style='border: 2px outset #EAF6FF'><a href='{$_SERVER['PHP_SELF']}?act=dl_oo&test_sort=$test_sort&class_num=$class_num&class_seme=$class_seme'>下載成績總表</a></font></td>";
		$menu.="<td nowrap  align='left'><form action='{$_SERVER['PHP_SELF']}' method='POST'><select name='test_sort' onchange='this.form.submit()'>$option</select><input type='hidden' name='class_seme' value='$class_seme'><input type='hidden' name='class_base' value='$class_base'></form></td>";
		if($test_sort)	{
			$student_select=logn_stud_sel($curr_year,$curr_seme,$class_num);
			$student_select="<tr><td>
			<form action='{$_SERVER['PHP_SELF']}' method='POST' name='sel_id'>\n
			<select name='student_sn' style='background-color:#DDDDDC;font-size: 13px' size='16' onchange='this.form.submit()'>\n
			$student_select
			</select>
			<input type='hidden' name='class_seme' value='$class_seme'>
			<input type='hidden' name='class_base' value='$class_base'>			
			<input type='hidden' name='class_num' value='$class_num'>
			<input type='hidden' name='test_sort' value='$test_sort'>		
			</form>\n
			</td></tr>";
		}
	}

	if($class_num && $test_sort && $student_sn){
		
		if($add_nor){
			$checked=" checked";
			$ratio=test_ratio($curr_year,$curr_seme);//本學期的成績設定
			$R0=($ratio[substr($class_num,0,-2)][$test_sort-1][0])*100/($ratio[substr($class_num,0,-2)][$test_sort-1][0] + $ratio[substr($class_num,0,-2)][$test_sort-1][1]);
			$R1=($ratio[substr($class_num,0,-2)][$test_sort-1][1])*100/($ratio[substr($class_num,0,-2)][$test_sort-1][0] + $ratio[substr($class_num,0,-2)][$test_sort-1][1]);
			$rowspan=" rowspan='2'";
			$colspan=" colspan='2'";
			
		if (ceil($R0)!=$R0)$R0=round($R0,2);
		if (ceil($R1)!=$R1)$R1=round($R1,2);
			
		}
		if($add_wet){
			$wchecked=" checked";
		}
		
		if($add_teacher){
			$tchecked=" checked";
		}
		
		if($add_date){
			$dchecked=" checked";
		}
		
		$nor_form="<tr><td><form><input type='hidden' name='class_seme' value='$class_seme'><input type='hidden' name='student_sn' value='$student_sn'><input type='hidden' name='class_num' value='$class_num'><input type='hidden' name='test_sort' value='$test_sort'><input type='hidden' name='add_wet' value='$add_wet'><input type='hidden' name='add_teacher' value='$add_teacher'><input type='hidden' name='add_date' value='$add_date'><input type='hidden' name='class_base' value='$class_base'><input type='checkbox' name='add_nor'$checked value='1' onclick='this.form.submit()'>包含平時成績</form></td></tr>";
		$wet_form="<tr><td><form><input type='hidden' name='class_seme' value='$class_seme'><input type='hidden' name='student_sn' value='$student_sn'><input type='hidden' name='class_num' value='$class_num'><input type='hidden' name='test_sort' value='$test_sort'><input type='hidden' name='add_nor' value='$add_nor'><input type='hidden' name='add_teacher' value='$add_teacher'><input type='hidden' name='add_date' value='$add_date'><input type='hidden' name='class_base' value='$class_base'><input type='checkbox' name='add_wet'$wchecked value='1' onclick='this.form.submit()'>包含各科加權</form></td></tr>";
		$teacher_form="<tr><td><form><input type='hidden' name='class_seme' value='$class_seme'><input type='hidden' name='student_sn' value='$student_sn'><input type='hidden' name='class_num' value='$class_num'><input type='hidden' name='test_sort' value='$test_sort'><input type='hidden' name='add_nor' value='$add_nor'><input type='hidden' name='add_wet' value='$add_wet'><input type='hidden' name='add_date' value='$add_date'><input type='hidden' name='class_base' value='$class_base'><input type='checkbox' name='add_teacher'$tchecked value='1' onclick='this.form.submit()'>導師及家長</form></td></tr>";
		$date_form="<tr><td><form><input type='hidden' name='class_seme' value='$class_seme'><input type='hidden' name='student_sn' value='$student_sn'><input type='hidden' name='class_num' value='$class_num'><input type='hidden' name='test_sort' value='$test_sort'><input type='hidden' name='add_nor' value='$add_nor'><input type='hidden' name='add_wet' value='$add_wet'><input type='hidden' name='add_teacher' value='$add_teacher'><input type='hidden' name='class_base' value='$class_base'><input type='checkbox' name='add_date'$dchecked value='1' onclick='this.form.submit()'>列印日期</form></td></tr>";
	
		
		$download="<tr><td><font style='border: 2px outset #EAF6FF'><a href='{$_SERVER['PHP_SELF']}?act=dl_oo_one&class_seme=$class_seme&test_sort=$test_sort&class_num=$class_num&student_sn=$student_sn&add_nor=$add_nor&add_wet=$add_wet&add_teacher=$add_teacher&class_base=$class_base&add_date=$add_date'>下載個人SXW</a></font></td></tr>";
		$download2="<tr><td><font style='border: 2px outset #EAF6FF'><a href='{$_SERVER['PHP_SELF']}?act=dl_oo_class&class_seme=$class_seme&test_sort=$test_sort&class_num=$class_num&add_nor=$add_nor&add_wet=$add_wet&add_teacher=$add_teacher&class_base=$class_base&add_date=$add_date'>下載全班SXW</a></font></td></tr>";
		
		$download3="<tr><td><font style='border: 2px outset #EAF6FF'><a href='{$_SERVER['PHP_SELF']}?act=dl_pdf_one&class_seme=$class_seme&test_sort=$test_sort&class_num=$class_num&student_sn=$student_sn&add_nor=$add_nor&add_wet=$add_wet&add_teacher=$add_teacher&class_base=$class_base&add_date=$add_date'>下載個人PDF</a></font></td></tr>";
		$download4="<tr><td><font style='border: 2px outset #EAF6FF'><a href='{$_SERVER['PHP_SELF']}?act=dl_pdf_class&class_seme=$class_seme&test_sort=$test_sort&class_num=$class_num&add_nor=$add_nor&add_wet=$add_wet&add_teacher=$add_teacher&class_base=$class_base&add_date=$add_date'>下載全班PDF</a></font></td></tr>";

		//成績單標題

		$title=$school_short_name."<br>".$curr_year."學年度第".$curr_seme."學期第".$test_sort."次定期考查<br>";
		if(sizeof($curr_year)==2) $curr_year="0".$curr_year;	
		$class_id=$curr_year."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));
		$st_arr=student_sn_to_name_num($student_sn);
		
        $st=student_sn_to_id_name_num($student_sn,$curr_year,$curr_seme);
		
		$cla_arr=class_id_to_full_class_name($class_id);
		$title.="班級：".$cla_arr."<br>姓名：".$st_arr[1]." 座號：".$st[2];		
		if($add_nor){
			$paper="<table  cellspacing=1 cellpadding=6 border=0 bgcolor='#A7A7A7' width='100%' >
			<tr bgcolor='#EFFFFF'>
			<td colspan='4'>".$title."</td></tr>";
		}else{
			$paper="<table  cellspacing=1 cellpadding=6 border=0 bgcolor='#A7A7A7' width='100%' >
			<tr bgcolor='#EFFFFF'>
			<td colspan='2'>".$title."</td></tr>";
		}
		//if(sizeof($curr_year)<3) $curr_year="0".$curr_year;
		//$class_id=$curr_year."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));
		//科目
		$SS=class_id2subject($class_id);
		$i=0;
		$i_nor=0;
		$total=0;
		$total_nor=0;
		foreach($SS as $ss_id => $s_name){
			$wet=subj_wet($ss_id);
			$an_score="";
			if($add_nor){
				//平時考成績
				$score_b_nor[$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="平時成績",$test_sort);
				if($score_b_nor[$ss_id]==-100) $score_b_nor[$ss_id]="";
				if($score_b_nor[$ss_id]!="") {
					$i_nor++;
					if($add_wet) {
						$total_nor=$total_nor+$score_b_nor[$ss_id]*$wet;
						$i_nor_wet=$i_nor_wet+$wet;
					}
					else $total_nor=$total_nor+$score_b_nor[$ss_id];
				}
			}
			//月考成績
			$score_b[$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
			if($score_b[$ss_id]==-100) $score_b[$ss_id]="";
			if($score_b[$ss_id]!="") {
				$i++;
				if($add_wet) {
					$total=$total+$score_b[$ss_id]*$wet;
					$i_wet=$i_wet+$wet;
				}
				else $total=$total+$score_b[$ss_id];
			}


			if($add_wet){
				if($add_nor){
					if($score_b[$ss_id] || $score_b_nor[$ss_id]) {
						$an_score=((($score_b[$ss_id]*$R0)+($score_b_nor[$ss_id]*$R1))/($R0+$R1));
						
						$an_score=number_format($an_score,2);
						
						$an_total=$an_total+$an_score*$wet;
					}
					
					$score_b[$ss_id]=number_format($score_b[$ss_id],2);
					$score_b_nor[$ss_id]=number_format($score_b_nor[$ss_id],2);
					
					$paper.="<tr bgcolor='#E4EDFF'><td$rowspan>".$s_name."*".$wet."</td><td>月考 $R0 %</td><td>$score_b[$ss_id]</td><td$rowspan>".$an_score."</td></tr>";
					$paper.="<tr bgcolor='#E4EDFF'><td>平時 $R1 %</td><td>$score_b_nor[$ss_id]</td></tr>";
				}else{
					$paper.="<tr bgcolor='#E4EDFF'><td>".$s_name."*".$wet."</td><td>$score_b[$ss_id]</td></tr>";
				}
			}else{
				if($add_nor){
					if($score_b[$ss_id] || $score_b_nor[$ss_id]) {
						$an_score=((($score_b[$ss_id]*$R0)+($score_b_nor[$ss_id]*$R1))/($R0+$R1));
						
						$an_score=number_format($an_score,2);
						
						$an_total=$an_total+$an_score;
					}
					$paper.="<tr bgcolor='#E4EDFF'><td$rowspan>$s_name</td><td>月考 $R0 %</td><td>$score_b[$ss_id]</td><td$rowspan>".$an_score."</td></tr>";
					$paper.="<tr bgcolor='#E4EDFF'><td>平時 $R1 %</td><td>$score_b_nor[$ss_id]</td></tr>";
				}else{
					$paper.="<tr bgcolor='#E4EDFF'><td>$s_name</td><td>$score_b[$ss_id]</td></tr>";
				}
			}
		}

		if($add_wet){
			if($add_nor){
				$paper.="<tr bgcolor='#D6D8FD'><td colspan='2'>總分</td><td colspan='2' align='center'>$an_total</td></tr>";
			}else{
				$paper.="<tr bgcolor='#D6D8FD'><td>總分</td><td>$total</td></tr>";
			}
		}else{
			if($add_nor){
				$paper.="<tr bgcolor='#D6D8FD'><td colspan='2'>總分</td><td colspan='2' align='center'>$an_total</td></tr>";
			}else{
				$paper.="<tr bgcolor='#D6D8FD'><td>總分</td><td>$total</td></tr>";
			}
		}

		if($add_wet){
			if($add_nor) {
				if(max($i_wet,$i_nor_wet)) $mi=max($i_wet,$i_nor_wet);
				if($an_total) $aver=round($an_total/$mi,2);
				$paper.="<tr bgcolor='#B2B9F6'><td colspan='2'>平均</td><td colspan='2' align='center'>".$aver."</td></tr>";
			}else{
				if($i_wet>0) $aver=round($total/$i_wet,2);
				$paper.="<tr bgcolor='#B2B9F6'><td>平均</td><td>".$aver."</td></tr>";
			}
		}else{
			if($add_nor) {
				if(max($i,$i_nor)) $mi=max($i,$i_nor);
				if($an_total) $aver=round($an_total/$mi,2);
				$paper.="<tr bgcolor='#B2B9F6'><td colspan='2'>平均</td><td colspan='2' align='center'>".$aver."</td></tr>";
			}else{
				if($i>0) $aver=round($total/$i,2);
				$paper.="<tr bgcolor='#B2B9F6'><td>平均</td><td>".$aver."</td></tr>";
			}
		}

		$paper.="</table>";

	}
	$list="<table><tr><td><form action='{$_SERVER['PHP_SELF']}' method='POST'><input type='hidden' name='class_seme' value='$class_seme'><input type='hidden' name='class_base' value='$class_base'><input type='hidden' name='student_sn' value='$student_sn'></form></td></tr>$student_select $nor_form $wet_form $teacher_form $date_form $download $download2 $download3 $download4</table>";
	$main="<table><tr><td valign='top'>$list</td><td valign='top'>$paper</td></tr></table>";

	//設定主網頁顯示區的背景顏色
	$back_ground="
		<table cellspacing=1 cellpadding=0 border=0  bgcolor='#BBBBBB' width='100%'>
			<tr>
				<td>
					<table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFFFFF' width='100%'>
						<tr>
							$menu
						</tr>
					</table>
					<table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFFFFF' width='100%'>
						<tr>
							<td colspan='2'>
								$main
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>";
	echo $back_ground;

	// SFS3 的版尾
	foot();
	
	
	    /*
		$title=$school_short_name."<br>".$curr_year."學年度第".$curr_seme."學期第".$test_sort."次定期考查<br>";
		if(sizeof($curr_year)==2) $curr_year="0".$curr_year;	
		$class_id=$curr_year."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));
		$st_arr=student_sn_to_name_num($student_sn);			
		$cla_arr=class_id_to_full_class_name($class_id);
		$title.="班級：".$cla_arr."<br>姓名：".$st_arr[1]." 座號：".$st_arr[2];
		$paper="<table  cellspacing=1 cellpadding=6 border=0 bgcolor='#A7A7A7' width='100%' >
		<tr bgcolor='#EFFFFF'>
		<td colspan='2'>".$title."</td></tr>";
		if(sizeof($curr_year)==2) $curr_year="0".$curr_year;
		$class_id=$curr_year."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));		
		//科目
		$SS=class_id2subject($class_id);
		$i=0;
		foreach($SS as $ss_id => $s_name){
			//成績			
			$score_b[$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
			if($score_b[$ss_id]==-100) $score_b[$ss_id]="";
			if($score_b[$ss_id]!="") {$i++; $total=$total+$score_b[$ss_id];}
			$paper.="<tr bgcolor='#E4EDFF'><td>$s_name</td><td>$score_b[$ss_id]</td></tr>";			
		}
		$paper.="<tr bgcolor='#D6D8FD'><td>總分</td><td>$total</td></tr>";
		if($i>0) $aver=round($total/$i,2);
		$paper.="<tr bgcolor='#B2B9F6'><td>平均</td><td>".$aver."</td></tr>";
		$paper.="</table>";

	}
	$list="<table><tr><td><form action='{$_SERVER['PHP_SELF']}' method='POST'><input type='hidden' name='student_sn' value='$student_sn'></form></td></tr>$student_select $download $download2 </table>";
	$main="<table><tr><td valign='top'>$list</td><td valign='top'>$paper</td></tr></table>";

	//設定主網頁顯示區的背景顏色
	$back_ground="
		<table cellspacing=1 cellpadding=0 border=0  bgcolor='#BBBBBB' width='100%'>
			<tr>
				<td>
					<table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFFFFF' width='100%'>
						<tr>
							$menu
						</tr>
					</table>
					<table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFFFFF' width='100%'>
						<tr>
							<td colspan='2'>
								$main
							</td>
						</tr>		
					</table>
				</td>
			</tr>
		</table>";	

	echo $back_ground;

	// SFS3 的版尾
	foot();
	*/
}


function ooo_one($test_sort,$class_num,$student_sn){
	global $CONN,$school_short_name,$class_seme,$add_nor,$add_wet,$add_teacher,$add_date;

	$oo_path = "ooo_one";

	$filename=$class_num."_".$test_sort."_".$student_sn.".sxw";

	$ttt = new EasyZip;
	$ttt->setPath($oo_path);
	$ttt->addDir('META-INF');
	$ttt->addfile('settings.xml');
	$ttt->addfile('styles.xml');
	$ttt->addfile('meta.xml');

	//讀出 content.xml
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content.xml");

	//將 content.xml 的 tag 取代
	
	if($class_seme) {
		$curr_year = intval(substr($class_seme,0,-1));
		$curr_seme =  substr($class_seme,-1);				
	} 
	else{
		$curr_year = curr_year();
		$curr_seme = curr_seme();	
	}
	
	if(sizeof($curr_year)==2) $curr_year="0".$curr_year;
	$class_id=$curr_year."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));	
	$year_seme_sort=$curr_year."學年度第".$curr_seme."學期"."第".$test_sort."次定期考查";
	$class=class_id_to_full_class_name($class_id);
	$school_name=$school_short_name;
	$st=student_sn_to_id_name_num($student_sn,$curr_year,$curr_seme);
	$name=$st[1];
	$num=$st[2];
	//echo $school_name.$year_seme_sort.$class_info.$name.$num;
	
    $view_num=2;
	$pe1="";
	
	//科目
	$count=0;
	$total=0;
	$SS=class_id2subject($class_id);
    

	foreach($SS as $ss_id => $subject_name){
			
			$wet=subj_wet($ss_id);
			$an_score="";
			$i_nor=0;
			

			$ratio=test_ratio($curr_year,$curr_seme);//本學期的成績設定
			$R0=($ratio[substr($class_num,0,-2)][$test_sort-1][0])*100/($ratio[substr($class_num,0,-2)][$test_sort-1][0] + $ratio[substr($class_num,0,-2)][$test_sort-1][1]);
			$R1=($ratio[substr($class_num,0,-2)][$test_sort-1][1])*100/($ratio[substr($class_num,0,-2)][$test_sort-1][0] + $ratio[substr($class_num,0,-2)][$test_sort-1][1]);

		    if (ceil($R0)!=$R0)$R0=round($R0,2);
		    if (ceil($R1)!=$R1)$R1=round($R1,2);
			
			if($add_nor)
			{			
		        $pe1=" *".$R0."%";
				$score_b_nor[$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="平時成績",$test_sort);
				//平時考成績
				if($score_b_nor[$ss_id]==-100) $score_b_nor[$ss_id]="";
				if($score_b_nor[$ss_id]!="") {
					$i_nor++;
					if($add_wet) {
						$total_nor=$total_nor+$score_b_nor[$ss_id]*$wet;
						$i_nor_wet=$i_nor_wet+$wet;
					}
					else $total_nor=$total_nor+$score_b_nor[$ss_id];
				}
				
				
$nor_view="
<table:table-cell table:style-name='table1.B1' table:value-type='string'>
<text:p text:style-name='Table Heading'>
平時 *$R1%
</text:p>
</table:table-cell>
<table:table-cell table:style-name='table1.B1' table:value-type='string'>
<text:p text:style-name='Table Heading'>
成績
</text:p>
</table:table-cell>";

$view_num=4;
			
			}

		//月考成績
			$score_b[$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
			if($score_b[$ss_id]==-100) $score_b[$ss_id]="";
			if($score_b[$ss_id]!="") {
				$i++;
				if($add_wet) {
					$total=$total+$score_b[$ss_id]*$wet;
					$i_wet=$i_wet+$wet;
				}
				else 
				{
					$count++;
					$total=$total+$score_b[$ss_id];
				}
			}


			if($add_wet)
			{
				if($add_nor)
				{
					if($score_b[$ss_id] || $score_b_nor[$ss_id]) {
						$an_score=((($score_b[$ss_id]*$R0)+($score_b_nor[$ss_id]*$R1))/($R0+$R1));						
						$an_score=number_format($an_score,2);				
						$an_total=$an_total+$an_score*$wet;
					}
					
					$score_b[$ss_id]=number_format($score_b[$ss_id],2);
					$score_b_nor[$ss_id]=number_format($score_b_nor[$ss_id],2);
					
				}
			}
			else
			{
				if($add_nor)
				{
					if($score_b[$ss_id] || $score_b_nor[$ss_id]) 
					{
						$an_score=((($score_b[$ss_id]*$R0)+($score_b_nor[$ss_id]*$R1))/($R0+$R1));						
						$an_score=number_format($an_score,2);				
						$an_total=$an_total+$an_score;
					}
				}		
			}
		

		//成績
		//$score_b[$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
		//if($score_b[$ss_id]==-100) $score_b[$ss_id]="";
		//if($score_b[$ss_id]!="") {$count++; $total=$total+$score_b[$ss_id];}
	    
		if ($add_wet)$subject_name=$subject_name ."*".$wet;
		
		$sj_sc.="
			<table:table-row>			
			<table:table-cell table:style-name='table1.A2' table:value-type='string'>
			<text:p text:style-name='P3'>
			$subject_name
			</text:p>
			</table:table-cell>
			
			<table:table-cell table:style-name='table1.B2' table:value-type='string'>
			<text:p text:style-name='P3'>
			{$score_b[$ss_id]}
			</text:p>
			</table:table-cell>";
			
			if($add_nor)
			{				
			$sj_sc.="
			<table:table-cell table:style-name='table1.B2' table:value-type='string'>
			<text:p text:style-name='P3'>
			{$score_b_nor[$ss_id]}
			</text:p>
			</table:table-cell>
			
			<table:table-cell table:style-name='table1.B2' table:value-type='string'>
			<text:p text:style-name='P3'>
			{$an_score}
			</text:p>
			</table:table-cell>";
			}
			
		   $sj_sc.="</table:table-row>";

	     }
		 
		 
    if($count>0) 
	{
	$aver=round($total/$count,2);
	$total_nor_aver=round($total_nor/$count,2);
	$an_total_aver=round($an_total/$count,2);
	}
	else
	{
	$aver=round($total/$i_wet,2);
	$total_nor_aver=round($total_nor/$i_nor_wet,2);
	$an_total_aver=round($an_total/$i_wet,2);		
		
	}
	
	
	//$teacher=$_SESSION['session_tea_name'];
	
	$totalx="
	<table:table-cell table:style-name='table1.B2' table:value-type='string'>
    <text:p text:style-name='P3'>
    $total
    </text:p>
    </table:table-cell>";
	
	if($add_nor)
	{	
	$totalx.="
	<table:table-cell table:style-name='table1.B2' table:value-type='string'>
    <text:p text:style-name='P3'>
    $total_nor
    </text:p>
    </table:table-cell>
	<table:table-cell table:style-name='table1.B2' table:value-type='string'>
    <text:p text:style-name='P3'>
    $an_total
    </text:p>
    </table:table-cell>";
		
	}
	
	$averx="
	<table:table-cell table:style-name='table1.B2' table:value-type='string'>
    <text:p text:style-name='P3'>
    $aver
    </text:p>
    </table:table-cell>";
	if($add_nor)
	{	
	$averx.="
	<table:table-cell table:style-name='table1.B2' table:value-type='string'>
    <text:p text:style-name='P3'>
   $total_nor_aver
    </text:p>
    </table:table-cell>
	<table:table-cell table:style-name='table1.B2' table:value-type='string'>
    <text:p text:style-name='P3'>
   $an_total_aver
    </text:p>
    </table:table-cell>";
    }
	
	if ($add_teacher)
	{
		
	   //取得某學期某班導師姓名	
       $class_teacher_all=class_teacher();
       $teacher=$class_teacher_all[$class_id];
		
		$teacher_view="導師:";
		$parent_view="家長:";
	}
	else
	{
		$teacher_view="";
		$teacher="";
		$parent_view="";
	}

	
	if ($add_date)
	{
		$date_view="列印日期:".date("Y-m-d");
	}
	else
	{
		$date_view="";
	}
		
	//變數替換
    $temp_arr["school_name"] = $school_name;
	$temp_arr["year_seme_sort"] = $year_seme_sort;
	$temp_arr["class"] = $class;	
	$temp_arr["name"] = $name;
	$temp_arr["pe1"] = $pe1;
    $temp_arr["view_num"] = $view_num;	
	$temp_arr["num"] = $num;	
	$temp_arr["nor_view"]=$nor_view;
	$temp_arr["sj_sc"] = $sj_sc;
	$temp_arr["total"] = $totalx;	
	$temp_arr["aver"] = $averx;
	$temp_arr["teacher_view"] = $teacher_view;
	$temp_arr["teacher"] = $teacher;
	$temp_arr["parent_view"] = $parent_view;
	$temp_arr["date_view"] = $date_view;
	
	// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
	$replace_data = $ttt->change_temp($temp_arr,$data,0);

	// 加入 content.xml 到zip 中
	$ttt->add_file($replace_data,"content.xml");
	
	//產生 zip 檔
	$sss = & $ttt->file();

	//以串流方式送出 ooo.sxw
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: application/vnd.sun.xml.writer");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
	echo $sss;
	exit;
	return;
}

function ooo_class($test_sort,$class_num){
	global $CONN,$school_short_name,$class_seme,$add_nor,$add_wet,$add_teacher,$add_date;

	$oo_path = "ooo_class";
	$filename=$class_seme."_".$class_num."_".$test_sort.".sxw";	
	//換頁 tag
	$break ="<text:p text:style-name=\"break_page\"/>";
	    
	//新增一個 zipfile 實例
	//$ttt = new zipfile;
	
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);
	$ttt->addDir('META-INF');
	$ttt->addfile('settings.xml');
	$ttt->addfile('styles.xml');
	$ttt->addfile('meta.xml');
	

	//讀出 xml 檔案
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/META-INF/manifest.xml");

	//加入 xml 檔案到 zip 中，共有五個檔案
	//第一個參數為原始字串，第二個參數為 zip 檔案的目錄和名稱
	$ttt->add_file($data,"/META-INF/manifest.xml");

	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/settings.xml");
	$ttt->add_file($data,"settings.xml");

	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/styles.xml");
	$ttt->add_file($data,"styles.xml");
                                   
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/meta.xml");
	$ttt->add_file($data,"meta.xml");
	
	if($class_seme) {
		$curr_year = intval(substr($class_seme,0,-1));
		$curr_seme =  substr($class_seme,-1);				
	} 
	else{
		$curr_year = curr_year();
		$curr_seme = curr_seme();	
	}
	
	if(sizeof($curr_year)==2) $curr_year="0".$curr_year;
	$class_id=sprintf("%03d",$curr_year)."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));			
	$student_sn_arr=class_id_to_seme_student_sn($class_id,$yn='0');
	
	foreach($student_sn_arr as $student_sn){	
		//讀出 content.xml
		$content_body = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_body.xml");

		//將 content_body.xml 的 tag 取代	

		$year_seme_sort=$curr_year."學年度第".$curr_seme."學期"."第".$test_sort."次定期考查";
		$class=class_id_to_full_class_name($class_id);
		$school_name=$school_short_name;
		$st=student_sn_to_id_name_num($student_sn,$curr_year,$curr_seme);
		$name=$st[1];
		$num=$st[2];
		//echo $school_name.$year_seme_sort.$class_info.$name.$num;

        $pe1="";
	    $view_num=2;
	   //科目	
		$count[$student_sn]=0;
		$total[$student_sn]=0;
		$SS=class_id2subject($class_id);	
		
		foreach($SS as $ss_id => $subject_name){
		
         	$wet=subj_wet($ss_id);
			$an_score[$student_sn]="";
			$i_nor[$student_sn]=0;

			$ratio=test_ratio($curr_year,$curr_seme);//本學期的成績設定
			$R0=($ratio[substr($class_num,0,-2)][$test_sort-1][0])*100/($ratio[substr($class_num,0,-2)][$test_sort-1][0] + $ratio[substr($class_num,0,-2)][$test_sort-1][1]);
			$R1=($ratio[substr($class_num,0,-2)][$test_sort-1][1])*100/($ratio[substr($class_num,0,-2)][$test_sort-1][0] + $ratio[substr($class_num,0,-2)][$test_sort-1][1]);

		    if (ceil($R0)!=$R0)$R0=round($R0,2);
		    if (ceil($R1)!=$R1)$R1=round($R1,2);
			
			if($add_nor)
			{				
		        $pe1=" *".$R0."%";
				$score_b_nor[$student_sn][$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="平時成績",$test_sort);
				//平時考成績
				if($score_b_nor[$student_sn][$ss_id]==-100) $score_b_nor[$student_sn][$ss_id]="";
				if($score_b_nor[$student_sn][$ss_id]!="") {
					$i_nor[$student_sn]++;
					if($add_wet) {
						$total_nor[$student_sn]=$total_nor[$student_sn]+$score_b_nor[$student_sn][$ss_id]*$wet;
						$i_nor_wet[$student_sn]=$i_nor_wet[$student_sn]+$wet;
					}
					else $total_nor[$student_sn]=$total_nor[$student_sn]+$score_b_nor[$student_sn][$ss_id];
				}
				
				
$nor_view[$student_sn]="
<table:table-cell table:style-name='table1.B1' table:value-type='string'>
<text:p text:style-name='Table Heading'>
平時 *$R1%
</text:p>
</table:table-cell>
<table:table-cell table:style-name='table1.B1' table:value-type='string'>
<text:p text:style-name='Table Heading'>
成績
</text:p>
</table:table-cell>";

$view_num=4;
			
			}

		//月考成績
			$score_b[$student_sn][$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
			if($score_b[$student_sn][$ss_id]==-100) $score_b[$student_sn][$ss_id]="";
			if($score_b[$student_sn][$ss_id]!="") {
				$i[$student_sn]++;
				if($add_wet) {
					$total[$student_sn]=$total[$student_sn]+$score_b[$student_sn][$ss_id]*$wet;
					$i_wet[$student_sn]=$i_wet[$student_sn]+$wet;
				}
				else 
				{
					$count[$student_sn]++;
					$total[$student_sn]=$total[$student_sn]+$score_b[$student_sn][$ss_id];
				}
			}


			if($add_wet)
			{
				if($add_nor)
				{
					if($score_b[$student_sn][$ss_id] || $score_b_nor[$student_sn][$ss_id]) {
						$an_score[$student_sn]=((($score_b[$student_sn][$ss_id]*$R0)+($score_b_nor[$student_sn][$ss_id]*$R1))/($R0+$R1));						
						$an_score[$student_sn]=number_format($an_score[$student_sn],2);				
						$an_total[$student_sn]=$an_total[$student_sn]+$an_score[$student_sn]*$wet;
					}
					
					$score_b[$student_sn][$ss_id]=number_format($score_b[$student_sn][$ss_id],2);
					$score_b_nor[$student_sn][$ss_id]=number_format($score_b_nor[$student_sn][$ss_id],2);
					
				}
			}
			else
			{
				if($add_nor)
				{
					if($score_b[$student_sn][$ss_id] || $score_b_nor[$student_sn][$ss_id]) 
					{
						$an_score[$student_sn]=((($score_b[$student_sn][$ss_id]*$R0)+($score_b_nor[$student_sn][$ss_id]*$R1))/($R0+$R1));						
						$an_score[$student_sn]=number_format($an_score[$student_sn],2);				
						$an_total[$student_sn]=$an_total[$student_sn]+$an_score[$student_sn];
					}
				}		
			}
		

		//成績
		//$score_b[$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
		//if($score_b[$ss_id]==-100) $score_b[$ss_id]="";
		//if($score_b[$ss_id]!="") {$count++; $total=$total+$score_b[$ss_id];}
	    
		if ($add_wet)$subject_name=$subject_name ."*".$wet;
		
		$sj_sc[$student_sn].="
			<table:table-row>			
			<table:table-cell table:style-name='table1.A2' table:value-type='string'>
			<text:p text:style-name='P3'>
			$subject_name
			</text:p>
			</table:table-cell>
			
			<table:table-cell table:style-name='table1.B2' table:value-type='string'>
			<text:p text:style-name='P3'>
			{$score_b[$student_sn][$ss_id]}
			</text:p>
			</table:table-cell>";
			
			if($add_nor)
			{				
			$sj_sc[$student_sn].="
			<table:table-cell table:style-name='table1.B2' table:value-type='string'>
			<text:p text:style-name='P3'>
			{$score_b_nor[$student_sn][$ss_id]}
			</text:p>
			</table:table-cell>
			
			<table:table-cell table:style-name='table1.B2' table:value-type='string'>
			<text:p text:style-name='P3'>
			{$an_score[$student_sn]}
			</text:p>
			</table:table-cell>";
			}
			
		   $sj_sc[$student_sn].="</table:table-row>";

	     }
		 
		 
    if($count[$student_sn]>0) 
	{
	$aver[$student_sn]=round($total[$student_sn]/$count[$student_sn],2);
	$total_nor_aver[$student_sn]=round($total_nor[$student_sn]/$count[$student_sn],2);
	$an_total_aver[$student_sn]=round($an_total[$student_sn]/$count[$student_sn],2);
	}
	else
	{
	$aver[$student_sn]=round($total[$student_sn]/$i_wet[$student_sn],2);
	$total_nor_aver[$student_sn]=round($total_nor[$student_sn]/$i_nor_wet[$student_sn],2);
	$an_total_aver[$student_sn]=round($an_total[$student_sn]/$i_wet[$student_sn],2);		
		
	}
	
	
	//$teacher=$_SESSION['session_tea_name'];
	
	$totalx[$student_sn]="
	<table:table-cell table:style-name='table1.B2' table:value-type='string'>
    <text:p text:style-name='P3'>
    $total[$student_sn]
    </text:p>
    </table:table-cell>";
	
	if($add_nor)
	{	
	$totalx[$student_sn].="
	<table:table-cell table:style-name='table1.B2' table:value-type='string'>
    <text:p text:style-name='P3'>
    $total_nor[$student_sn]
    </text:p>
    </table:table-cell>
	<table:table-cell table:style-name='table1.B2' table:value-type='string'>
    <text:p text:style-name='P3'>
    $an_total[$student_sn]
    </text:p>
    </table:table-cell>";
		
	}
	
	$averx[$student_sn]="
	<table:table-cell table:style-name='table1.B2' table:value-type='string'>
    <text:p text:style-name='P3'>
    $aver[$student_sn]
    </text:p>
    </table:table-cell>";
	if($add_nor)
	{	
	$averx[$student_sn].="
	<table:table-cell table:style-name='table1.B2' table:value-type='string'>
    <text:p text:style-name='P3'>
    $total_nor_aver[$student_sn]
    </text:p>
    </table:table-cell>
	<table:table-cell table:style-name='table1.B2' table:value-type='string'>
    <text:p text:style-name='P3'>
    $an_total_aver[$student_sn]
    </text:p>
    </table:table-cell>";
    }
		    /*
			//成績
			$score_b[$student_sn][$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
			if($score_b[$student_sn][$ss_id]==-100) $score_b[$student_sn][$ss_id]="";
			if($score_b[$student_sn][$ss_id]!="") {$count[$student_sn]++; $total[$student_sn]=$total[$student_sn]+$score_b[$student_sn][$ss_id];}

			$sj_sc[$student_sn].="
				<table:table-row>
				<table:table-cell table:style-name='table1.A2' table:value-type='string'>
				<text:p text:style-name='P3'>
				$subject_name
				</text:p>
				</table:table-cell>
				<table:table-cell table:style-name='table1.B2' table:value-type='string'>
				<text:p text:style-name='P3'>
				{$score_b[$student_sn][$ss_id]}
				</text:p>
				</table:table-cell>
				</table:table-row>
				";
		}
		if($count[$student_sn]>0) $aver[$student_sn]=round($total[$student_sn]/$count[$student_sn],2);
		$teacher=$_SESSION['session_tea_name'];
        */
		
	if ($add_teacher)
	{
	   //取得某學期某班導師姓名	
       $class_teacher_all=class_teacher();
       $teacher=$class_teacher_all[$class_id];
		
		$teacher_view="導師:";
		$parent_view="家長:";
	}
	else
	{
		$teacher_view="";
		$teacher="";
		$parent_view="";
	}
	
	if ($add_date)
	{
		$date_view="列印日期:".date("Y-m-d");
	}
	else
	{
		$date_view="";
	}
		
		//變數替換
		$temp_arr["school_name"] = $school_name;
		$temp_arr["year_seme_sort"] = $year_seme_sort;
		$temp_arr["class"] = $class;	
		$temp_arr["name"] = $name;		
        $temp_arr["pe1"] = $pe1;		
		$temp_arr["view_num"] = $view_num;	
	    $temp_arr["num"] = $num;	
	    $temp_arr["nor_view"]=$nor_view[$student_sn];
	    $temp_arr["sj_sc"] = $sj_sc[$student_sn];
	    $temp_arr["total"] = $totalx[$student_sn];	
	    $temp_arr["aver"] = $averx[$student_sn];
		$temp_arr["teacher_view"] = $teacher_view;
	    $temp_arr["teacher"] = $teacher;
	    $temp_arr["parent_view"] = $parent_view;
		$temp_arr["date_view"] = $date_view;
		
		//換行
		$content_body .= $break;
		
		//change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
		$replace_data.= $ttt->change_temp($temp_arr,$content_body,0);
	}
	
	//讀出 XML 檔頭
	$doc_head = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_head.xml");
	//讀出 XML 檔尾
	$doc_foot = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_foot.xml");

	$replace_data =$doc_head.$replace_data.$doc_foot;
	// 加入 content.xml 到zip 中
	$ttt->add_file($replace_data,"content.xml");

	//產生 zip 檔
	$sss = & $ttt->file();

	//以串流方式送出 ooo.sxw
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: application/vnd.sun.xml.writer");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");

	echo $sss;

	exit;
	return;
}








?>
