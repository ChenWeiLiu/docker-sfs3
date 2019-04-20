<?php
// $Id: dlcsv.php 7710 2013-10-23 12:40:27Z smallduh $

// 引入 SFS3 的函式庫

include_once "../../include/config.php";

require_once "../../include/sfs_case_studclass.php";

require_once "../../include/sfs_case_score.php";
// 認證
sfs_check();

//
// 您的程式碼由此開始


//全域變數轉換區*****************************************************
$Hseme_year_seme=($_GET['Hseme_year_seme'])?$_GET['Hseme_year_seme']:$_POST['Hseme_year_seme'];
$Hstud_seme_class=($_GET['Hstud_seme_class'])?$_GET['Hstud_seme_class']:$_POST['Hstud_seme_class'];
//$point=($_GET['point'])?$_GET['point']:$_POST['point'];
$ss_id_A=($_GET['ss_id_A'])?$_GET['ss_id_A']:$_POST['ss_id_A'];
$Submit2=($_GET['Submit2'])?$_GET['Submit2']:$_POST['Submit2'];
$Submit3=($_GET['Submit3'])?$_GET['Submit3']:$_POST['Submit3'];
$Submit4=($_GET['Submit4'])?$_GET['Submit4']:$_POST['Submit4'];
$Submit5 = $_REQUEST[Submit5];
//********************************************************************
//年級陣列
$school_kind_name=array("幼稚園","一年","二年","三年","四年","五年","六年","一年","二年","三年","一年","年二","三年");

//九年一貫標準領域或科目陣列
$standard_scope=array(1=>"語文",2=>"數學",3=>"自然與生活科技",4=>"社會",5=>"藝術與人文",6=>"生活課程",7=>"健康與體育",8=>"綜合活動",9=>"彈性課程",10=>"日常生活表現",11=>"本國語文",12=>"鄉土語言",13=>"英語");

//努力程度
$oth_arr_score = array("表現優異"=>5,"表現良好"=>4,"表現尚可"=>3,"需再加油"=>2,"有待改進"=>1);
//下載檔案
if($Hseme_year_seme && $Hstud_seme_class && ($Submit2=="下載成績表格"|| $Submit3=="下載各科文字敘述表格"|| $Submit4=="下載日常生活評量表格" || $Submit5 == "下載努力程度表格") && $ss_id_A){
	if ($Submit3 == "下載各科文字敘述表格"){
		$filename=$Hseme_year_seme."_".$Hstud_seme_class."_memo.csv";
		$C_filename=intval(substr($Hseme_year_seme,0,-1))."學年度第".substr($Hseme_year_seme,-1)."學期".$school_kind_name[substr($Hstud_seme_class,0,-2)].substr($Hstud_seme_class,-2)."班文字描述匯入檔";
	}
	elseif ($Submit4 == "下載日常生活評量表格"){
		$filename=$Hseme_year_seme."_".$Hstud_seme_class."_nor.csv";
		$C_filename=intval(substr($Hseme_year_seme,0,-1))."學年度第".substr($Hseme_year_seme,-1)."學期".$school_kind_name[substr($Hstud_seme_class,0,-2)].substr($Hstud_seme_class,-2)."班日常生活評量匯入檔";
	}
	elseif ($Submit5 == "下載努力程度表格"){
		$filename=$Hseme_year_seme."_".$Hstud_seme_class."_study.csv";
		$C_filename=intval(substr($Hseme_year_seme,0,-1))."學年度第".substr($Hseme_year_seme,-1)."學期".$school_kind_name[substr($Hstud_seme_class,0,-2)].substr($Hstud_seme_class,-2)."班努力程度匯入檔";
	}
	else {
		$filename=$Hseme_year_seme."_".$Hstud_seme_class.".csv";
		$C_filename=intval(substr($Hseme_year_seme,0,-1))."學年度第".substr($Hseme_year_seme,-1)."學期".$school_kind_name[substr($Hstud_seme_class,0,-2)].substr($Hstud_seme_class,-2)."班成績匯入檔";
	}
    header("Content-disposition: attachment ; filename=$filename");
    header("Content-type: application/octetstream ; Charset=Big5");
    //header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
    header("Expires: 0");	
    //第一行
   
    $table_score = "stud_seme_score";
    $head.=$C_filename."後面的數字千萬別修改或刪除";	
    if ($Submit4 == "下載日常生活評量表格"){
	$head.=",0";
	$table_score = "stud_seme_score_nor";
    }
    elseif ($Submit5 == "下載努力程度表格"){
	foreach($oth_arr_score as $id2=>$val2)
		$head.=" $id2=$val2 ";
	foreach($ss_id_A as $key0 => $value0)		
		$head.=",$value0"; 
	$table_score = "stud_seme_score_oth";
    }
    else {
	foreach($ss_id_A as $key0 => $value0){		
		$head.=",$value0"; 
	}
     }
	$head.="\n";

	$head.="學號,座號,姓名";
	if ($Submit4 == "下載日常生活評量表格"){
		$head.=",日常生活評量成績,日常生活評量文字敘述";
	}
	else {
		foreach($ss_id_A as $key => $value){
		if ($Submit3 == "下載各科文字敘述表格")
			$subject_name=ss_id_to_subject_name($value)."-文字描述";
		else
			$subject_name=ss_id_to_subject_name($value);

			$head.=",$subject_name"; 
		}
	}
	
	$head.="\n";
	if ($Submit5 == '下載努力程度表格')
		$query = "select b.student_sn,c.ss_val,c.ss_id from stud_seme a ,stud_base b,$table_score c  where a.stud_id=b.stud_id and b.stud_id=c.stud_id and c.ss_kind='努力程度' and b.stud_study_cond IN (0,5)  and a.seme_year_seme='$Hseme_year_seme' and a.seme_year_seme=c.seme_year_seme  and a.seme_class='$Hstud_seme_class' order by a.seme_num ";
	else
		// 取得學籍成績檔
		$query = "select b.student_sn,c.ss_score,c.ss_id,c.ss_score_memo from stud_seme a ,stud_base b,$table_score c  where a.stud_id=b.stud_id and  a.student_sn=b.student_sn and  b.student_sn=c.student_sn and b.stud_study_cond IN(0,5)  and a.seme_year_seme='$Hseme_year_seme' and a.seme_year_seme=c.seme_year_seme  and a.seme_class='$Hstud_seme_class' order by a.seme_num ";
	$rs = $CONN->Execute($query) or die($query);
	$temp_student_arr = array();
	while(!$rs->EOF) {
		if ($Submit5 == '下載努力程度表格'){
			$temp_score_arr[$rs->fields[student_sn]][$rs->fields[ss_id]] = $oth_arr_score[$rs->fields[ss_val]];
		}
		else{
			$temp_score_arr[$rs->fields[student_sn]][$rs->fields[ss_id]] = $rs->fields[ss_score];
			$temp_score_memo_arr[$rs->fields[student_sn]][$rs->fields[ss_id]] = str_replace("\r\n","",$rs->fields[ss_score_memo]);
		}
		$temp_student_arr[$rs->fields[student_sn]][$rs->fields[ss_id]] = 1;
		$rs->MoveNext();
	}
	
	$sql="select a.stud_id , a.seme_num , b.stud_name,b.student_sn  from stud_seme a,stud_base b where a.stud_id=b.stud_id and a.student_sn=b.student_sn and  b.stud_study_cond IN(0,5) and a.seme_year_seme='$Hseme_year_seme' and a.seme_class='$Hstud_seme_class' order by a.seme_num ";
	$rs=$CONN->Execute($sql) or die($sql);
	$sel_year=intval(substr($Hseme_year_seme,0,-1));
	$sel_seme=substr($Hseme_year_seme,-1);
    $i=0;
	while(!$rs->EOF){
	       	$stud_id=$rs->fields['stud_id'];
		$student_sn=$rs->fields['student_sn'];
		$seme_num=$rs->fields['seme_num'];
		$stud_name=$rs->fields['stud_name'];							
		$head.=$stud_id.",".$seme_num.",\"".$stud_name."\"";
		if ($Submit4 == "下載日常生活評量表格"){				
			$head.=",".$temp_score_arr[$student_sn][0].",\"".$temp_score_memo_arr[$student_sn][0]."\""; 
		}else {	
			foreach($ss_id_A as $key1 => $value1){
				//如果是空檔則新增一筆
				if ($temp_student_arr[$student_sn][$value1]<>1){
					$query = "insert into stud_seme_score(seme_year_seme,student_sn,ss_id)values('$Hseme_year_seme',$student_sn,$value1)";	
					$CONN->Execute($query);
				}
				if ($Submit3 == "下載各科文字敘述表格"){				
					$seme_score = "\"".$temp_score_memo_arr[$student_sn][$value1]."\"";
				}	
				else{
					$seme_score = $temp_score_arr[$student_sn][$value1];				
					if($seme_score<=0) $seme_score="";
				}
				$head.=",".$seme_score; 
			}
		}
		$head.="\n";
        
		$rs->MoveNext();
    }		
	echo $head;
}
else{
	header("Location:creat_table.php?Hseme_year_seme=$Hseme_year_seme&Hstud_seme_class=$Hstud_seme_class");

}
//由ss_id找出科目名稱的函數
function  ss_id_to_subject_name($ss_id){
    global $CONN;
    $sql1="select subject_id from score_ss where ss_id=$ss_id";
    $rs1=$CONN->Execute($sql1);
    $subject_id = $rs1->fields["subject_id"];
    if($subject_id!=0){
        $sql2="select subject_name from score_subject where subject_id=$subject_id";
        $rs2=$CONN->Execute($sql2);
        $subject_name = $rs2->fields["subject_name"];
    }
    else{
        $sql3="select scope_id from score_ss where ss_id=$ss_id";
        $rs3=$CONN->Execute($sql3);
        $scope_id = $rs3->fields["scope_id"];
        $sql4="select subject_name from score_subject where subject_id=$scope_id";
        $rs4=$CONN->Execute($sql4);
        $subject_name = $rs4->fields["subject_name"];
    }
    return $subject_name;
}

