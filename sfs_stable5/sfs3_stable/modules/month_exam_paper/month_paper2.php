<?php
// $Id: month_paper2.php 8654 2015-12-19 16:37:10Z qfon $
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
$student_sn=($_POST['student_sn'])?"{$_POST['student_sn']}":"{$_GET['student_sn']}";

if(!$curr_year) $curr_year = curr_year();
if(!$curr_seme) $curr_seme = curr_seme();

if($act=="dl_oo_one"){

	OOO_one($test_sort,$class_num,$student_sn);

}
elseif($act=="dl_oo_class"){

	OOO_class($test_sort,$class_num);

}
elseif($act=="dl_pdf_class"){

	$class_id=sprintf("%03d",$curr_year)."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));
	$student_sn_arr=class_id_to_seme_student_sn($class_id,$yn='0');
	$class=class_id_to_full_class_name($class_id);
	$title=$school_short_name.curr_year()."學年度第".$curr_seme."學期"."第".$test_sort."次定期考查\n".$class;

	$header=array("科目","成績");

	$i=0;
	foreach($student_sn_arr as $student_sn){
		$data[$i]=array();
		$st=student_sn_to_id_name_num($student_sn,$curr_year="",$curr_seme="");
		$name=$st[1];
		$num=$st[2];
		$comment1[]="姓名：".$name." 座號：".$num;

		//科目
		$count[$student_sn]=0;
		$SS=class_id2subject($class_id);
		$j=0;
		foreach($SS as $ss_id => $subject_name){
			$data[$i][$j]=array();
			//成績
			$score_b[$student_sn][$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
			if($score_b[$student_sn][$ss_id]==-100) $score_b[$student_sn][$ss_id]="";
			if($score_b[$student_sn][$ss_id]!="") {$count[$student_sn]++; $total[$student_sn]=$total[$student_sn]+$score_b[$student_sn][$ss_id];}
			array_push($data[$i][$j],"$subject_name","{$score_b[$student_sn][$ss_id]}");
			$j++;
		}
		if($count[$student_sn]>0) $aver[$student_sn]=round($total[$student_sn]/$count[$student_sn],2);
		$data[$i][$j]=array();
		array_push($data[$i][$j],"總分","$total[$student_sn]");
		$j++;
		$data[$i][$j]=array();
		array_push($data[$i][$j],"平均","$aver[$student_sn]");

		$i++;
	}
	$comment2="導師：{$_SESSION['session_tea_name']} \n家長：";
	creat_pdf($title,$header,$data,$comment1,$comment2);

}
elseif($act=="dl_pdf_one"){
	$title=$school_short_name.$curr_year."學年度第".$curr_seme."學期第".$test_sort."次定期考查";
	if(sizeof($curr_year)==2) $curr_year="0".$curr_year;
	$class_id=$curr_year."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));
	$st_arr=student_sn_to_name_num($student_sn);
	$st=student_sn_to_id_name_num($student_sn,$curr_year,$curr_seme);
	$cla_arr=class_id_to_full_class_name($class_id);
	$title.="\n班級：".$cla_arr."\n姓名：".$st_arr[1]." 座號：".$st[2];

	$header=array("科目","分數");
	$data=array();
	$class_id=$curr_year."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));
	//科目
	$SS=class_id2subject($class_id);
	$j=0;
	foreach($SS as $ss_id => $s_name){
		$data[$j]=array();
		//成績
		$score_b[$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
		if($score_b[$ss_id]==-100) $score_b[$ss_id]="";
		if($score_b[$ss_id]!="") {$i++; $total=$total+$score_b[$ss_id];}
		//echo $s_name.$score_b[$ss_id];
		array_push($data[$j],$s_name,$score_b[$ss_id]);
		$j++;
	}
	$data[$j]=array();
	array_push($data[$j],"總分","$total");
	if($i>0) $aver=round($total/$i,2);
	$j++;
	$data[$j]=array();
	array_push($data[$j],"平均","$aver");


	//print_r($data);
	$comment2="導師：{$_SESSION['session_tea_name']} \n家長：";

	creat_pdf($title,$header,$data,$comment1,$comment2);
}
else{
	// 叫用 SFS3 的版頭
	head("月考成績單");
	
	// 您的程式碼由此開始
	print_menu($menu_p);

	//由teacher_sn找出他是哪一班的導師
	$class_num=get_teach_class();
	if($class_num){
		//階段選單
		$option=test_sort_select($curr_year,$curr_seme,$class_num);
		if($test_sort)	{
			$student_select=logn_stud_sel($curr_year,$curr_seme,$class_num);
			$student_select="<tr><td>
			<form action='{$_SERVER['PHP_SELF']}' method='POST' name='sel_id'>\n
			<select name='student_sn' style='background-color:#DDDDDC;font-size: 13px' size='16' onchange='this.form.submit()'>\n
			$student_select
			</select>
			<input type='hidden' name='class_num' value='$class_num'>
			<input type='hidden' name='test_sort' value='$test_sort'>		
			</form>\n
			</td></tr>";
		}
	}

	if($class_num && $test_sort && $student_sn){
		$download="<tr><td><font style='border: 2px outset #EAF6FF'><a href='{$_SERVER['PHP_SELF']}?act=dl_oo_one&test_sort=$test_sort&class_num=$class_num&student_sn=$student_sn'>下載個人SXW</a></font></td></tr>";
		$download2="<tr><td><font style='border: 2px outset #EAF6FF'><a href='{$_SERVER['PHP_SELF']}?act=dl_oo_class&test_sort=$test_sort&class_num=$class_num'>下載全班SXW</a></font></td></tr>";
		$download3="<tr><td><font style='border: 2px outset #EAF6FF'><a href='{$_SERVER['PHP_SELF']}?act=dl_pdf_one&test_sort=$test_sort&class_num=$class_num&student_sn=$student_sn'>下載個人PDF</a></font></td></tr>";
		$download4="<tr><td><font style='border: 2px outset #EAF6FF'><a href='{$_SERVER['PHP_SELF']}?act=dl_pdf_class&test_sort=$test_sort&class_num=$class_num'>下載全班PDF</a></font></td></tr>";
		//成績單標題
		/*
		$title=$school_short_name."<br>".$curr_year."學年度第".$curr_seme."學期第".$test_sort."次定期考查<br>";
		if(sizeof($curr_year)<3) $curr_year="0".$curr_year;
		$class_id=$curr_year."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));
		$st_arr=student_sn_to_name_num($student_sn);
		$cla_arr=class_id_to_full_class_name($class_id);
		$title.="班級：".$cla_arr."<br>姓名：".$st_arr[1]." 座號：".$st_arr[2];
		*/
		$title=$school_short_name."<br>".$curr_year."學年度第".$curr_seme."學期第".$test_sort."次定期考查<br>";
		if(sizeof($curr_year)==2) $curr_year="0".$curr_year;	
		$class_id=$curr_year."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));
		$st_arr=student_sn_to_name_num($student_sn);
        $st=student_sn_to_id_name_num($student_sn,$curr_year,$curr_seme);		
		$cla_arr=class_id_to_full_class_name($class_id);
		$title.="班級：".$cla_arr."<br>姓名：".$st_arr[1]." 座號：".$st[2];		

		$paper="<table  cellspacing=1 cellpadding=6 border=0 bgcolor='#A7A7A7' width='100%' >
		<tr bgcolor='#EFFFFF'>
		<td colspan='2'>".$title."</td></tr>";
		if(sizeof($curr_year)<3) $curr_year="0".$curr_year;
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
	$list="<table><tr><td><form action='{$_SERVER['PHP_SELF']}' method='POST'><select name='test_sort' onchange='this.form.submit()'>$option</select><input type='hidden' name='student_sn' value='$student_sn'></form></td></tr>$student_select $download $download2 $download3 $download4 </table>";
	$main="<table><tr><td valign='top'>$list</td><td valign='top'>$paper</td></tr></table>";

	//設定主網頁顯示區的背景顏色
	$back_ground="
		<table cellspacing=1 cellpadding=2 border=0 bgcolor='#B0C0F8' width='100%'>
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


function ooo_one($test_sort,$class_num,$student_sn){
	global $CONN,$school_short_name;

	$oo_path = "ooo_one";

	$filename=$class_num."_".$test_sort."_".$student_sn.".sxw";

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

	//讀出 content.xml
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content.xml");

	//將 content.xml 的 tag 取代	
	$curr_year = curr_year();
	$curr_seme = curr_seme();
	if(sizeof($curr_year)==2) $curr_year="0".$curr_year;
	$class_id=$curr_year."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));	
	$year_seme_sort=curr_year()."學年度第".$curr_seme."學期"."第".$test_sort."次定期考查";
	$class=class_id_to_full_class_name($class_id);
	$school_name=$school_short_name;
	$st=student_sn_to_id_name_num($student_sn,$curr_year="",$curr_seme="");
	$name=$st[1];
	$num=$st[2];
	//echo $school_name.$year_seme_sort.$class_info.$name.$num;
	

	//科目
	$count=0;
	$SS=class_id2subject($class_id);
	foreach($SS as $ss_id => $subject_name){	
		//成績
		$score_b[$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
		if($score_b[$ss_id]==-100) $score_b[$ss_id]="";
		if($score_b[$ss_id]!="") {$count++; $total=$total+$score_b[$ss_id];}
				
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
			</table:table-cell>
			</table:table-row>
			";
	}
	if($count>0) $aver=round($total/$count,2);
	$teacher=$_SESSION['session_tea_name'];
		
	//變數替換
    $temp_arr["school_name"] = $school_name;
	$temp_arr["year_seme_sort"] = $year_seme_sort;
	$temp_arr["class"] = $class;	
	$temp_arr["name"] = $name;	
	$temp_arr["num"] = $num;	
	$temp_arr["sj_sc"] = $sj_sc;
	$temp_arr["total"] = $total;	
	$temp_arr["aver"] = $aver;
	$temp_arr["teacher"] = $teacher;
	
	// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
	$replace_data = $ttt->change_temp($temp_arr,$data,0);

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

function ooo_class($test_sort,$class_num){
	global $CONN,$school_short_name;

	$oo_path = "ooo_class";

	$filename=$class_num."_".$test_sort.".sxw";
	
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
	
	if($curr_year=="") $curr_year = curr_year();
	if($curr_seme=="") $curr_seme = curr_seme();	
	$class_id=sprintf("%03d",$curr_year)."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));
	$student_sn_arr=class_id_to_seme_student_sn($class_id,$yn='0');

	foreach($student_sn_arr as $student_sn){
		//讀出 content.xml
		$content_body = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_body.xml");

		//將 content_body.xml 的 tag 取代

		$year_seme_sort=curr_year()."學年度第".$curr_seme."學期"."第".$test_sort."次定期考查";
		$class=class_id_to_full_class_name($class_id);
		$school_name=$school_short_name;
		$st=student_sn_to_id_name_num($student_sn,$curr_year,$curr_seme);
		$name=$st[1];
		$num=$st[2];
		//echo $school_name.$year_seme_sort.$class_info.$name.$num;


		//科目
		$count[$student_sn]=0;
		$SS=class_id2subject($class_id);
		foreach($SS as $ss_id => $subject_name){
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

		//變數替換
		$temp_arr["school_name"] = $school_name;
		$temp_arr["year_seme_sort"] = $year_seme_sort;
		$temp_arr["class"] = $class;
		$temp_arr["name"] = $name;
		$temp_arr["num"] = $num;
		$temp_arr["sj_sc"] = $sj_sc[$student_sn];
		$temp_arr["total"] = $total[$student_sn];
		$temp_arr["aver"] = $aver[$student_sn];
		$temp_arr["teacher"] = $teacher;

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

?>
