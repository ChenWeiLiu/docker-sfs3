<?php
// $Id: month_paper1.php 8963 2016-09-05 05:17:43Z smallduh $
// 引入 SFS3 的函式庫
//include "../../include/config.php";

// 引入您自己的 config.php 檔
require "config.php";

// 認證
sfs_check();

	//轉換成全域變數
$act=($_POST['act'])?"{$_POST['act']}":"{$_GET['act']}";
$test_sort=($_POST['test_sort'])?"{$_POST['test_sort']}":"{$_GET['test_sort']}";
$class_num=($_POST['class_num'])?"{$_POST['class_num']}":"{$_GET['class_num']}";

if($act=="dl_oo"){

	OOO($test_sort,$class_num);

}elseif($act=="dl_pdf" ){//pdf報表產出
	//2016.02.15 改成可選擇學期 by smallduh.
	$c_curr_seme=($_GET['c_curr_seme']=="")?sprintf("%03d%1d",curr_year(),curr_seme()):$_GET['c_curr_seme'];
	$curr_year=substr($c_curr_seme,0,3);
	$curr_seme=substr($c_curr_seme,-1);

	//$curr_year = curr_year();
	//$curr_seme = curr_seme();
	$title.=$school_long_name;
	$class_id=sprintf("%03d_%d_%02d_%02d",$curr_year,$curr_seme,substr($class_num,0,-2),substr($class_num,-2));

	$title.=curr_year()."學年度第".$curr_seme."學期".class_id_to_full_class_name($class_id);
	$title.="第".$test_sort."次定期考查";
	//echo $title;

	//header
	$header=array();
	$SS=class_id2subject($class_id);
	array_push($header,"座號","姓名");
	foreach($SS as $ss_id => $subject_name){
		array_push($header,$subject_name);
	}
	array_push($header,"總分","平均","名次");

	//print_r($header);

	$st_array=class_id_to_student_sn($class_id);
	$p=0;
	foreach ($st_array as $student_sn){
		foreach($SS as $ss_id => $s_name){
			//成績
			$score_b[$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
			if($score_b[$ss_id]==-100) $score_b[$ss_id]="";
			if($score_b[$ss_id]!="") {$count_b[$p]++; $count_stud[$ss_id]++;}
			$total_score_b[$p]=$total_score_b[$p]+$score_b[$ss_id];
			$calss_score[$ss_id]=$calss_score[$ss_id]+$score_b[$ss_id];
		}
		$avg_score_b[$p]=$total_score_b[$p]/$count_b[$p];
		//echo $avg_score_b[$p]."<br>";
		$p++;
	}

	$i=0;
	$j=0;
	foreach ($st_array as $student_sn){
		$data[$i]=array();
		//找出座號，姓名，由學生流水號
		$classinfo_array=student_sn_to_classinfo($student_sn);
		array_push($data[$i],"$classinfo_array[2]","$classinfo_array[4]");
		foreach($SS as $ss_id => $subject_name){
			//成績
			$score=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
			if($score==-100) $score="";
			if($score!="") $count_subj[$j]++;
			array_push($data[$i],"$score");
			if($score) $total_score[$i]=$total_score[$i]+$score;
		}
		$avg_score[$i]=$total_score[$i]/$count_subj[$j];
		if($total_score[$i]) $avg_score_r[$i]=round($total_score[$i]/$count_subj[$j],2);
		//排名
		//echo $avg_score[$i].$avg_score_b."<br>";
		if($avg_score[$i]) $sort_name[$i]=sort_sort($avg_score[$i],$avg_score_b);
		array_push($data[$i],"$total_score[$i]","$avg_score_r[$i]","$sort_name[$i]");
		$i++;
		$j++;
	}

	creat_pdf($title,$header,$data,$comment);
}
else{

	// 叫用 SFS3 的版頭
	head("月考成績單");

	// 您的程式碼由此開始
	print_menu($menu_p);
	$c_curr_seme=($_POST['c_curr_seme']=="")?sprintf("%03d%1d",curr_year(),curr_seme()):$_POST['c_curr_seme'];
	$curr_year=substr($c_curr_seme,0,3);
	$curr_seme=substr($c_curr_seme,-1);
    //echo $curr_year.$curr_seme;
	//  $curr_year = curr_year();
	//  $curr_seme = curr_seme();
	
	//由teacher_sn找出他是哪一班的導師
	$class_num=get_teach_class();
	
	//該任教班級已在學的總學期數
  $select_seme=get_class_seme_select($class_num);  													//array [1001]="100學年第1學期"

  $seme_select="<select size=\"1\" name=\"c_curr_seme\" onchange=\"this.form.submit()\">";
		
		foreach ($select_seme as $k=>$v) {
			$seme_select.="<option value='".$k."'".(($k==$c_curr_seme)?" selected":"").">".$v."</option>";
		}

		$seme_select.="</select>";


	
	if($class_num){
		//階段選單
		$option=test_sort_select($curr_year,$curr_seme,$class_num);
		if($test_sort)	$download="<td><font style='border: 2px outset #EAF6FF'><a href='{$_SERVER['PHP_SELF']}?act=dl_oo&c_curr_seme=$c_curr_seme&test_sort=$test_sort&class_num=$class_num'>下載sxw</a></font><font style='border: 2px outset #EAF6FF'><a href='{$_SERVER['PHP_SELF']}?act=dl_pdf&c_curr_seme=$c_curr_seme&test_sort=$test_sort&class_num=$class_num'>下載PDF</a></font></td>";
		$main="<table><tr><td><form action='{$_SERVER['PHP_SELF']}' method='POST'>".$seme_select."<select name='test_sort' onchange='this.form.submit()'>$option</select></form></td>$download</tr></table>";
		if($test_sort){		
			//本學期任教班級代號
			//if(sizeof($curr_year)<3) $curr_year="0".$curr_year;

            //如果非本年度, $class_num 要改變 如 801 -> 701
            if ($curr_year!=curr_year()) {
              $the_year=curr_year();
              $S=$the_year-$curr_year;
              $class_num=$class_num-$S*100;
            }

			$class_id=sprintf("%03d_%1d_%02d_%02d",$curr_year,$curr_seme,substr($class_num,0,-2),substr($class_num,-2,2));
            //echo ",".$class_id;
			//科目
			$SS=class_id2subject($class_id);
			$t1="<table  cellspacing=1 cellpadding=6 border=0 bgcolor='#A7A7A7' width='100%' >";
			$t2="<tr bgcolor='#00C000'><td>座號</td><td>姓名</td>";
			foreach($SS as $ss_id => $subject_name){
				$t2.="<td>$subject_name</td>";
			}
			$t2.="<td>總分</td><td>平均</td><td>名次</td></tr>";
			//學生與成績
			$t3="<tr>";
			//找出該班學生流水號陣列
			$st_array=class_id_to_student_sn($class_id);

			$p=0;
			foreach ($st_array as $student_sn){
				foreach($SS as $ss_id => $s_name){
					//成績
					$score_b[$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
					if($score_b[$ss_id]==-100) $score_b[$ss_id]="";
					if($score_b[$ss_id]!="") {$count_b[$p]++; $count_stud[$ss_id]++;}
					$total_score_b[$p]=$total_score_b[$p]+$score_b[$ss_id];
					$calss_score[$ss_id]=$calss_score[$ss_id]+$score_b[$ss_id];

				}
				$avg_score_b[$p]=$total_score_b[$p]/$count_b[$p];
				//echo $avg_score_b[$p]."<br>";
				$p++;
			}

			$i=0;
			$j=0;
			foreach ($st_array as $student_sn){
				//找出座號，姓名，由學生流水號
				$classinfo_array=student_sn_to_classinfo($student_sn);
				$t3.="<td bgcolor='#A3C7FD'>$classinfo_array[2]</td><td  bgcolor='#84A2CE'>$classinfo_array[4]</td>";
				foreach($SS as $ss_id => $subject_name){
					//成績
					$score=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
					if($score==-100) $score="";
					if($score!="") $count_subj[$j]++;
					$bgcolor=($score<60 && $score!="")?"#F8AAAA":"#FFFFFF";
					$t3.="<td bgcolor='$bgcolor' >".$score."</td>";
					if($score) $total_score[$i]=$total_score[$i]+$score;
				}
				$avg_score[$i]=$total_score[$i]/$count_subj[$j];
				if($total_score[$i]) $avg_score_r[$i]=round($total_score[$i]/$count_subj[$j],2);
				//排名
				//echo $avg_score[$i].$avg_score_b."<br>";
				if($avg_score[$i]) $sort_name[$i]=sort_sort($avg_score[$i],$avg_score_b);
				$t3.="<td bgcolor='#BED7FD'>$total_score[$i]</td><td bgcolor='#A3C7FD'>$avg_score_r[$i]</td><td bgcolor='#84A2CE'>$sort_name[$i]</td></tr>";
				$i++;
				$j++;
			}

			foreach($SS as $ss_id => $subject_name){
				if($calss_score[$ss_id]) $X[$ss_id]=round($calss_score[$ss_id]/$count_stud[$ss_id],2);
				$X_str.="<td>".$X[$ss_id]."</td>";

			}

			$t4="<tr bgcolor='#DF9D57'><td colspan=2>平均數</td>$X_str<td></td><td></td><td></td></tr>";
		}
			$main.=$t1.$t2.$t3.$t4."</table>";
	}
	else{
		$main="<table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%' align='center'><tr><td align='center'><h1><img src='images/caution.png' align='middle' border=0> 警告</h1></font></td></tr><tr><td align='center' bgcolor='#FFFFFF' width='90%'>".$_SESSION['session_tea_name']."您不具導師身份！ 無法進行操作！<br>若有疑問請檢查『教師管理』的任職資料。</td></tr></table>";
	}
	//設定主網頁顯示區的背景顏色
	$back_ground="
		<table cellspacing=1 cellpadding=6 border=0 bgcolor='#B0C0F8' width='100%'>
			<tr bgcolor='#FFFFFF'>
				<td>
					$main
				</td>
			</tr>
		</table>";
	echo $back_ground;

	// SFS3 的版尾
	foot();
}

function ooo($test_sort,$class_num){
	global $CONN,$school_long_name;

	$oo_path = "ooo_total";

	$filename=$class_num."_".$test_sort.".sxw";

    //新增一個 zipfile 實例
	$ttt = new zipfile;

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

	//讀出 content.xml
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content.xml");

	//將 content.xml 的 tag 取代	
	//$curr_year = curr_year();
	//$curr_seme = curr_seme();
	//2016.02.15 改成可選擇學期 by smallduh
	$c_curr_seme=($_GET['c_curr_seme']=="")?sprintf("%03d%1d",curr_year(),curr_seme()):$_GET['c_curr_seme'];
	$curr_year=substr($c_curr_seme,0,3);
	$curr_seme=substr($c_curr_seme,-1);

	if(sizeof($curr_year)<3) $curr_year="0".$curr_year;
	$class_id=$curr_year."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));
	$class_info=curr_year()."學年度第".$curr_seme."學期".class_id_to_full_class_name($class_id);
	$school_name=$school_long_name;
	$test_info="第".$test_sort."次定期考查";

	//科目
	$SS=class_id2subject($class_id);
	foreach($SS as $ss_id => $subject_name){
		$subj_str.="<table:table-cell table:style-name='tablec1.A1' table:value-type='string'><text:p text:style-name='Table Contents'>$subject_name</text:p></table:table-cell>";
	}
	
	
	//找出該班學生流水號陣列
	$st_array=class_id_to_student_sn($class_id);
	$p=0;		
	foreach ($st_array as $student_sn){			
		foreach($SS as $ss_id => $s_name){				
			//成績
			$score_b[$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
			if($score_b[$ss_id]==-100) $score_b[$ss_id]="";
			if($score_b[$ss_id]!="") {$count_b[$p]++; $count_stud[$ss_id]++;}
			$total_score_b[$p]=$total_score_b[$p]+$score_b[$ss_id];
			$calss_score[$ss_id]=$calss_score[$ss_id]+$score_b[$ss_id];
		}
		$avg_score_b[$p]=$total_score_b[$p]/$count_b[$p];
		$p++;		
	}

	$i=0;
	$j=0;
	foreach ($st_array as $student_sn){
		//找出座號，姓名，由學生流水號
		$classinfo_array=student_sn_to_classinfo($student_sn);
		$one_student.="
			<table:table-row>
			<table:table-cell table:style-name='tablec1.A2' table:value-type='string'>
			<text:p text:style-name='P3'>$classinfo_array[2]
			</text:p>
			</table:table-cell>
			<table:table-cell table:style-name='tablec1.A2' table:value-type='string'>
			<text:p text:style-name='P3'>$classinfo_array[4]
			</text:p>
			</table:table-cell>";
		foreach($SS as $ss_id => $subject_name){
			//成績
			$score=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
			if($score==-100) $score="";
			if($score!="") $count_subj[$j]++;
			$bgcolor=($score<60 && $score!="")?"#F8AAAA":"#FFFFFF";
			$one_student.="
			<table:table-cell table:style-name='tablec1.A2' table:value-type='string'>
			<text:p text:style-name='P3'>$score
			</text:p>
			</table:table-cell>";
			if($score) $total_score[$i]=$total_score[$i]+$score;
		}
		$avg_score[$i]=$total_score[$i]/$count_subj[$j];
		if($total_score[$i]) $avg_score_r[$i]=round($total_score[$i]/$count_subj[$j],2);
		//排名
		//echo $avg_score[$i].$avg_score_b."<br>";
		if($avg_score[$i]) $sort_name[$i]=sort_sort($avg_score[$i],$avg_score_b);
		//$t3.="<td bgcolor='#BED7FD'>$total_score[$i]</td><td bgcolor='#A3C7FD'>$avg_score_r[$i]</td><td bgcolor='#84A2CE'>$sort_name[$i]</td></tr>";	
		$one_student.="
		<table:table-cell table:style-name='tablec1.A2' table:value-type='string'>
		<text:p text:style-name='P3'>$total_score[$i]
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='tablec1.A2' table:value-type='string'>
		<text:p text:style-name='P3'>$avg_score_r[$i]</text:p></table:table-cell>
		<table:table-cell table:style-name='tablec1.G2' table:value-type='string'>
		<text:p text:style-name='P3'>$sort_name[$i]
		</text:p>
		</table:table-cell>
		</table:table-row>
		";
		$i++;
		$j++;	
	}

	foreach($SS as $ss_id => $subject_name){
		if($calss_score[$ss_id]) $X[$ss_id]=round($calss_score[$ss_id]/$count_stud[$ss_id],2);
		//$X_str.="<td>".$X[$ss_id]."</td>";
		$X_str.="
		<table:covered-table-cell/>
		<table:table-cell table:style-name='tablec1.A2' table:value-type='string'>
		<text:p text:style-name='P3'>$X[$ss_id]
		</text:p>
		</table:table-cell>";
	}
		
	
    $temp_arr["school_name"] = $school_name;
	$temp_arr["class_info"] = $class_info;
	$temp_arr["test_info"] = $test_info;
	$temp_arr["subj"] = $subj_str;	
	$temp_arr["colum"] = count($SS)+5;
	$temp_arr["one_student"] = $one_student;
	$temp_arr["X_str"] = $X_str;
	
	// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
	$replace_data = $ttt->change_temp($temp_arr,$data);

	// 加入 content.xml 到zip 中
	$ttt->add_file($replace_data,"content.xml");

	//產生 zip 檔
	$sss = $ttt->file();

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

//根據班級取得任課班的所有學期
function get_class_seme_select($class_num) {
	global $IS_JHORES;	
	$data_arr=array();	
	$I=substr($class_num,0,1)-$IS_JHORES-1;	
	
	for ($i=0;$i<=$I;$i++) {
	  $now_year=curr_year()-$i;
 	  if ($i>0 or curr_seme()==2) {
	  	$k=sprintf("%03d",$now_year)."2";
	  	$v=$now_year."學年度第2學期";
	    $data_arr[$k]=$v;
	  } //end if

	  $k=sprintf("%03d",$now_year)."1";
	  $v=$now_year."學年度第1學期";
	  $data_arr[$k]=$v;
	  
	}	// end for
	
	return $data_arr; 

} //end function
?>
