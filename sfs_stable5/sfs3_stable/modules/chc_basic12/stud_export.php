<?php

//$Id: chc_seme.php 5310 2009-01-10 07:57:56Z hami $

//ini_set('display_errors', '1');
//ini_set('output_buffering', '1');

include "config.php";
include "chc_func_class.php";
include_once "../../include/sfs_case_excel.php";

//認證
sfs_check();
chk_login('教務處');

//建立物件
$obj= new chc_seme($CONN,$smarty);

//判別國中6/國小0 變數
$obj->IS_JHORES=$IS_JHORES;

//顯示內容
if(isset($_POST) and count($_POST)>0){
	//$obj->debug_msg("第".__LINE__."行 _POST ", $_POST);
	ob_clean();
	$aa=curr_year();
	$bb=curr_seme();
	//$obj->all();
	if($_POST[basic_excel]=='匯出EXCEL(方法1)'){
		get_stud_data($aa,'excel');
	}elseif($_POST[basic_excel_2]=='匯出EXCEL(方法2)'){
		get_stud_data($aa,'excel_2');
	}elseif($_POST[chc_excel]=='匯出EXCEL(方法1)'){ //彰化縣資料
		output_excel($obj,'chc','excel');
	}elseif($_POST[chc_excel_2]=='匯出EXCEL(方法2)'){  //彰化縣資料
		output_excel($obj,'chc','excel_2');
	}elseif($_POST[basic_txt]=='匯出TXT'){
		get_stud_data($aa,'txt');
//	}elseif($_POST[chc_txt]=='匯出TXT'){  //彰化縣資料
//		get_chc_data($aa);
	}elseif($_POST[chc_excel_103]=='匯出EXCEL'){  //彰化縣資料103年
		get_chc_data_103year($aa,$obj);
	}
}

function output_excel($obj,$kind, $output_type) {
	global $smarty,$SFS_PATH; 

	$obj->all();

	//$obj->debug_msg("第".__LINE__."行 this->sch ", $this->sch);
	//$obj->debug_msg("第".__LINE__."行 this->stu ", $this->stu);
	//$obj->debug_msg("第".__LINE__."行 kind ", $kind);
	//$obj->debug_msg("第".__LINE__."行 output_type ", $output_type);
	$mem=0;
	if($kind=='chc'){
		foreach($obj->stu as $key=>$val){
			//品德服務分數最高20分
            $score_morality=$val['score_service']+$val['score_reward']+$val['score_fault'];
			if($score_morality>20){
				$score_morality=20;
			}
			//績優表現分數最高16分
            $score_display=$val['score_balance']+$val['score_club']+$val['score_race']+$val['score_physical'];
			if($score_display>16){
				$score_display=16;
			}
			if($output_type=='excel'){
				$data1[$mem][stud_name]=$val[stud_name];
				$data1[$mem][stud_person_id]=$val[stud_person_id];
				$data1[$mem][birth_year]=intval($val[birth_year]);
				$data1[$mem][birth_month]=intval($val[birth_month]);
				$data1[$mem][birth_day]=intval($val[birth_day]);
				$data1[$mem][income]=$val[income];
				$data1[$mem][score_nearby]=$val[score_nearby];
				$data1[$mem][score_service]=$val[score_service]; //服務學習
				$data1[$mem][score_reward]=$val[score_reward];  //獎勵紀錄
				$data1[$mem][score_fault]=$val[score_fault];   //生活教育
                $data1[$mem][score_morality]=$score_morality;    //品德服務
				$data1[$mem][score_balance]=$val[score_balance];  //均衡學習
				$data1[$mem][score_club]=$val[score_club];  //社團參與
				$data1[$mem][score_race]=$val[score_race];   //競賽表現
				$data1[$mem][score_physical]=$val[score_physical];   //體適能
                $data1[$mem][score_display]=$score_display;    //績優表現
			}elseif($output_type=='excel_2'){
				//102.10.28起採用xls匯出方式，整理資料順序
				$data1[$mem][]=$val[stud_name];
				$data1[$mem][]=$val[stud_person_id];
				$data1[$mem][]=intval($val[birth_year]);
				$data1[$mem][]=intval($val[birth_month]);
				$data1[$mem][]=intval($val[birth_day]);
				$data1[$mem][]=$val[income];
				$data1[$mem][]=$val[score_nearby];
				$data1[$mem][]=$val[score_service];
				$data1[$mem][]=$val[score_reward];
				$data1[$mem][]=$val[score_fault];
                $data1[$mem][]=$score_morality;    //品德服務
				$data1[$mem][]=$val[score_balance];
				$data1[$mem][]=$val[score_club];   //社團參與
				$data1[$mem][]=$val[score_race];
				$data1[$mem][]=$val[score_physical];
                $data1[$mem][]=$score_display;    //績優表現
			}
			$mem++;
		}

	}elseif($kind=='basic'){
		$filename ="basic_export.xls" ;
	}
	//$obj->debug_msg("第".__LINE__."行 data1 ", $data1);
//die();
	if($output_type=='excel'){
		$filename ="chc_export_1.xls" ;
		ob_clean();
		header("Content-disposition: filename=$filename");
		header("Content-type: application/octetstream");
		header("Pragma: no-cache");
		header("Expires: 0");

	    //使用樣版
	    $template_dir = $SFS_PATH."/".get_store_path()."/templates";
	    // 使用 smarty tag
	    $smarty->left_delimiter="{{";
	    $smarty->right_delimiter="}}";
	    //$smarty->debugging = true;
	    $smarty->assign("data_array",$data1);
	    $smarty->assign("template_dir",$template_dir);
	    if($kind=='chc'){
	    	$smarty->display("$template_dir/chc_excel.htm");
		}
	}elseif($output_type=='excel_2'){
		//102.10.28起採用xls匯出方式
		$filename ="chc_export_2.xls" ;
		$myhead1=array('學生姓名','身分證統一編號','出生年(民國年)','出生月','出生日','經濟弱勢','就近入學','服務學習','獎勵紀錄','生活教育','品德服務','均衡學習','社團參與','競賽表現','體適能','績優表現');

		$x=new sfs_xls();
		$x->setUTF8();//$x->setVersion(8);
		$x->setBorderStyle(1);
		$x->filename=$filename;
		$x->setRowText($myhead1);
		$x->addSheet("year".$aa);
		$x->items=$data1;
		$x->writeSheet();
		$x->process();
	}
	exit;


}



//學生基本資料
function get_stud_data($year,$kind){
	global $CONN,$school_sshort_name,$obj,$smarty,$SFS_PATH;

//查詢得多筆
	$sql_select="select c.sch_id,a.stud_id,a.curr_class_num,a.stud_name,a.stud_person_id,a.stud_sex,
	                    a.stud_birthday, b.graduation,b.kind_id,b.special,b.income,b.unemployed, 
	                    d.guardian_name,a.stud_tel_2,a.stud_tel_3,a.addr_zip,a.stud_addr_2
               from chc_basic12 b 
               left join stud_base a on a.student_sn = b.student_sn 
               left join stud_domicile d on a.student_sn = d.student_sn,school_base c
               where b.academic_year=".$year." order by a.curr_class_num";
               //echo "<br>".__LINE__."<br>".$sql_select."<br>";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
  //序號    
	$i = 0;
	$mem=0;
	while( list($sch_id,$stud_id,$curr_class_num,$stud_name,$stud_person_id,$stud_sex,$stud_birthday,$graduation,$kind_id,$special,$income,$unemployed,$guardian_name,$stud_tel_2,$stud_tel_3,$addr_zip,$stud_addr_2)=$recordSet->FetchRow() ){
	    //1.地區代碼，2碼，彰化為08
	    $area = "08";
	
	    //2.集報單位，6碼，靠右，不足左側補0
	    $sch_id = sprintf('%06s',$sch_id);
	    
	    //3.序號，5碼，靠右，不足左側補0
	    $i++;
	    $sn_excel=$i;
	    $sn = sprintf("%05s",$i);
	    
	    //4.學號，8碼，靠左，不足右側補半形空白
	    $stud_id_excel = $stud_id;
	    $stud_id = sprintf("%-8s",$stud_id);
	    
	    //5.班級，2碼, 靠右，不足左側補0
	    $class = substr($curr_class_num,1,2);
	    
	    //6.座號，2碼，靠右，不足左側補0
	    $num = substr($curr_class_num,3,2);
	    
	    //7.姓名去空白，半形和全形
	    $stud_name = str_replace(" ","",$stud_name);
	    $stud_name = str_replace("　","",$stud_name);
	    
	    //姓名共要30碼，靠左，不足補半形空白
	    $stud_name = sprintf("%-30s",$stud_name);
	    
	    //8.身分證首字要大寫，共要10碼，不足補*
	    $stud_person_id = sprintf("%-'*10s",strtoupper($stud_person_id));
	    
	    //9.性別1碼$stud_sex
	    
	    //生日拆成年月日，10.年3碼，11.月2碼，12.日2碼
	    $birth_date = explode("-",$stud_birthday);
	    $birth_year = sprintf("%03s",$birth_date[0]-1911);
	    $birth_mon = sprintf("%02s",$birth_date[1]);
	    $birth_day = sprintf("%02s",$birth_date[2]);
	    
	    //13.畢業學校代碼$sch_id，6碼，與集報單位同    
	    //14.畢業年為$year，民國年3碼    
	    //15.畢肄業為$graduation，1碼    
	    //16.學生身分為$kind_id，1碼    
	    //17.身心障礙為$special，1碼
	        
	    //18.就學區，2碼，留2格半形空白
	    $stu_area = "  ";
	    
	    //19.低收入戶及20.中低收入戶各1碼
	    if($income == 0){
	      $low_income = 0;
	      $mlow_income = 0;
	    }elseif($income == 1){
	      $low_income = 0;
	      $mlow_income = 1;
	    }elseif($income ==2){
	      $low_income = 1;
	      $mlow_income = 0;
	    }
    
	    //21.失業？1碼$unemployed
	    
	    //22.資料授權，1碼，全部要同意授權
	    $authorize = "1";
	    
	    //23.家長姓名去空白，半形和全形
	    $guardian_name = str_replace(" ","",$guardian_name);
	    $guardian_name = str_replace("　","",$guardian_name);
	    
	    //家長姓名共要30碼，不足補半形空白，靠左，不足補半形空白
	    $guardian_name = sprintf("%-30s",$guardian_name);
	    
	    //24.室內電話14碼，加區碼，靠左，不足補空白？？
	    //去空白和括號和減號
	    $stud_tel_2 = str_replace(" ","",$stud_tel_2);
	    $stud_tel_2 = str_replace("-","",$stud_tel_2);
	    $stud_tel_2 = str_replace("(","",$stud_tel_2);
	    $stud_tel_2 = str_replace(")","",$stud_tel_2);
	    
	     //若留室內電話者(9碼以下，因芬園鄉電話有8碼)，補04
	    if(strlen($stud_tel_2)<9 and strlen($stud_tel_2)>0){
	      $stud_tel_2 = sprintf("%-14s","04".$stud_tel_2);
	    }else{
	      $stud_tel_2 = sprintf("%-14s",$stud_tel_2);
	    }
	    
	    //25.行動電話14碼，靠左，不足補空白？？
	    $stud_tel_3 = str_replace(" ","",$stud_tel_3);
	    $stud_tel_3 = str_replace("-","",$stud_tel_3);
	    $stud_tel_3 = str_replace("(","",$stud_tel_3);
	    $stud_tel_3 = str_replace(")","",$stud_tel_3);
	    //取行動電話前10碼，因為有的欄位被填上兩支行動電話
	    $stud_tel_3 = substr($stud_tel_3,0,10);
	    $stud_tel_3 = sprintf("%-14s",$stud_tel_3);
	    
	    //26.郵遞區號，取前3碼，有的填上5碼，靠左，不足補空白？？
	    $addr_zip = substr($addr_zip,0,3);
	    $addr_zip = sprintf("%-3s",$addr_zip);
	    
	    //27.地址80碼，靠左，不足補空白？？數字以半形阿拉伯數字表示？(暫不理)    
	    $stud_addr_2 = sprintf("%-80s",$stud_addr_2);
	                                  
	    if($kind=='txt'){
			$all_data.=  $area." ".$sch_id." ".$sn." ".$stud_id." ".$class." ".$num." ".$stud_name." ".$stud_person_id." ".$stud_sex." ".$birth_year." ".$birth_mon." ".$birth_day." ".$sch_id." ".$year." ".$graduation." ".$kind_id." ".$special." ".$stu_area." ".$low_income." ".$mlow_income." ".$unemployed." ".$authorize." ".$guardian_name." ".$stud_tel_2." ".$stud_tel_3." ".$addr_zip." ".$stud_addr_2."\r\n";
    	}elseif($kind=='excel'){
	    	$all_data[$mem]['area']=$area;
	    	$all_data[$mem]['sch_id']=$sch_id;
	    	$all_data[$mem]['sn']=$sn_excel;
	    	$all_data[$mem]['stud_id']=$stud_id_excel;
	    	$all_data[$mem]['class']=$class;
	    	$all_data[$mem]['num']=$num;
	    	$all_data[$mem]['stud_name']=$stud_name;
	    	$all_data[$mem]['stud_person_id']=$stud_person_id;
	    	$all_data[$mem]['stud_sex']=$stud_sex;
	    	$all_data[$mem]['birth_year']=$birth_year;
	    	$all_data[$mem]['birth_mon']=$birth_mon;
	    	$all_data[$mem]['birth_day']=$birth_day;
	    	$all_data[$mem]['sch_id']=$sch_id;
	    	$all_data[$mem]['year']=$year;
	    	$all_data[$mem]['graduation']=$graduation;
	    	$all_data[$mem]['kind_id']=$kind_id;
	    	$all_data[$mem]['special']=$special;
	    	$all_data[$mem]['stu_area']=$stu_area;
	    	$all_data[$mem]['low_income']=$low_income;
	    	$all_data[$mem]['mlow_income']=$mlow_income;
	    	$all_data[$mem]['unemployed']=$unemployed;
	    	$all_data[$mem]['authorize']=$authorize;
	    	$all_data[$mem]['guardian_name']=$guardian_name;
	    	$all_data[$mem]['stud_tel_2']=$stud_tel_2;
	    	$all_data[$mem]['stud_tel_3']=$stud_tel_3;
	    	$all_data[$mem]['addr_zip']=$addr_zip;
	    	$all_data[$mem]['stud_addr_2']=$stud_addr_2;
		}elseif($kind=='excel_2'){
	    	$all_data[$mem][]=$area;
	    	$all_data[$mem][]=$sch_id;
	    	$all_data[$mem][]=$sn_excel;
	    	$all_data[$mem][]=$stud_id_excel;
	    	$all_data[$mem][]=$class;
	    	$all_data[$mem][]=$num;
	    	$all_data[$mem][]=$stud_name;
	    	$all_data[$mem][]=$stud_person_id;
	    	$all_data[$mem][]=$stud_sex;
	    	$all_data[$mem][]=$birth_year;
	    	$all_data[$mem][]=$birth_mon;
	    	$all_data[$mem][]=$birth_day;
	    	$all_data[$mem][]=$sch_id;
	    	$all_data[$mem][]=$year;
	    	$all_data[$mem][]=$graduation;
	    	$all_data[$mem][]=$kind_id;
	    	$all_data[$mem][]=$special;
	    	$all_data[$mem][]=$stu_area;
	    	$all_data[$mem][]=$low_income;
	    	$all_data[$mem][]=$mlow_income;
	    	$all_data[$mem][]=$unemployed;
	    	$all_data[$mem][]=$authorize;
	    	$all_data[$mem][]=$guardian_name;
	    	$all_data[$mem][]=$stud_tel_2;
	    	$all_data[$mem][]=$stud_tel_3;
	    	$all_data[$mem][]=$addr_zip;
	    	$all_data[$mem][]=$stud_addr_2;
		}
			$mem++;
	};
  	//echo "<pre>";
	//print_r($all_data);
	//die();
	if($kind=='txt'){
		$filename=$year."學年".$school_sshort_name."免試入學學生基本資料.txt";
		header("Content-disposition: attachment;filename=$filename");
		header("Content-type: text/txt ; Charset=Big5");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo $all_data;
	}elseif($kind=='excel'){
		$filename ="basic_export_1.xls" ;
		//使用樣版
	    $template_dir = $SFS_PATH."/".get_store_path()."/templates";

		ob_clean();
		header("Content-disposition: filename=$filename");
		header("Content-type: application/octetstream");
		header("Pragma: no-cache");
		header("Expires: 0");

	    // 使用 smarty tag
	    $smarty->left_delimiter="{{";
	    $smarty->right_delimiter="}}";
	    //$smarty->debugging = true;
	    $smarty->assign("data_array",$all_data);
	    $smarty->assign("template_dir",$template_dir);
	    $smarty->display("$template_dir/basic_excel.htm");
	}elseif($kind=='excel_2'){
		$filename ="basic_export_2.xls" ;
		//102.10.28起採用xls匯出方式
		$myhead=array("地區代碼","集報單位代碼","序號","學號","班級","座號","學生姓名","身分證","性別","出生年(民國年)","出生月","出生日","畢業學校代碼","畢業年(民國年)","畢肄業","學生身分","身心障礙","就學區","低收入戶","中低收入戶","失業勞工","資料授權","家長姓名","市內電話","行動電話","郵遞區號","地址");
		//include_once "../../include/sfs_case_excel.php";
		$x=new sfs_xls();
		$x->setUTF8();//$x->setVersion(8);
		$x->setBorderStyle(1);
		$x->filename=$filename;
		$x->setRowText($myhead);
		$x->addSheet("year".$year);
		$x->items=$all_data;
		$x->writeSheet();

		$x->process();
	}
	exit;
}

//彰化縣報表
function get_chc_data($year){
  global $CONN,$school_sshort_name;
//查詢得多筆
  $sql_select="select a.curr_class_num,a.stud_name,a.stud_person_id,a.stud_birthday,
                      b.income,b.score_nearby,b.score_service,b.score_reward,b.score_fault,
                      b.score_balance,b.score_club,b.score_race,b.score_physical 
               from chc_basic12 b 
               left join stud_base a 
               on a.student_sn = b.student_sn 
               where b.academic_year=".$year." 
               order by a.curr_class_num";
               //echo "<br>".__LINE__."<br>".$sql_select."<br>";
  $recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
  while( list($curr_class_num,$stud_name,$stud_person_id,$stud_birthday,$income,$score_nearby,$score_service,$score_reward,$score_fault,$score_balance,$score_club,$score_race,$score_physical)=$recordSet->FetchRow() ){
    //尚未輸入資料者，分數暫為0
    if(empty($score_service)) $score_service = "0";
    if(empty($score_reward)) $score_reward = "0";
    if(empty($score_fault)) $score_fault = "0";
    if(empty($score_balance)) $score_balance = "0";
    if(empty($score_club)) $score_club = "0";
    if(empty($score_race)) $score_race = "0";
    if(empty($score_physical)) $score_physical = "0";
    
    //1.姓名去空白，半形和全形
    $stud_name = str_replace(" ","",$stud_name);
    $stud_name = str_replace("　","",$stud_name);
    
    //姓名共要30碼，不足補半形空白
    $stud_name = sprintf("%-30s",$stud_name);
    
    //2.身分證首字要大寫，共要10位元，不足補*
    $stud_person_id = sprintf("%-'*10s",strtoupper($stud_person_id));
    
    //生日拆成年月日，3.年3碼，4.月2碼，5.日2碼
    $birth_date = explode("-",$stud_birthday);
    $birth_year = sprintf("%03s",$birth_date[0]-1911);
    $birth_mon = sprintf("%02s",$birth_date[1]);
    $birth_day = sprintf("%02s",$birth_date[2]);
    
    //6.經濟弱勢1碼$income
    //7.就近入學1碼$score_nearby
    
    //8.服務學習小數後一位，共3碼，若為整數，補.0
    $score_service = sprintf("%.1f",$score_service);
    
    //9.獎勵紀錄小數後一位，共3碼，若為整數，補.0
    $score_reward = sprintf("%.1f",$score_reward);
    
    //10.生活教育1碼$score_fault
    //11.均衡學習1碼$score_balance
    //12.社團參與1碼$score_club
    //13.競賽表現，共3碼，若為整數，補.0
    $score_race = sprintf("%.1f",$score_race);
    
    //14.體適能1碼$score_physical
                   //1.學生姓名     2.身分證            3.出生年         4.出生月        5.出生日        6.經濟弱勢   7.就近入學         8.服務學習           9.獎勵紀錄         10.生活教育       11.均衡學習         12.社團參與       13.競賽表現      14.體適能
      $all_data.=  $stud_name." ".$stud_person_id." ".$birth_year." ".$birth_mon." ".$birth_day." ".$income." ".$score_nearby." ".$score_service." ".$score_reward." ".$score_fault." ".$score_balance." ".$score_club." ".$score_race." ".$score_physical."\r\n";
  };
//print_r($all_data);
//die();
  $filename=$year."學年".$school_sshort_name."免試入學比序資料.txt";
  header("Content-disposition: attachment;filename=$filename");
  header("Content-type: text/txt ; Charset=Big5");
  header("Pragma: no-cache");
  header("Expires: 0");

  echo $all_data;
  exit;
}


//103學年度彰化區高級中等學校免試入學分發學生資料
function get_chc_data_103year($year, $obj){
	global $CONN,$school_sshort_name;

	//畢業年=學年度+1
	$grad_year=sprintf("%03d",$year+1);

//取得第一部份資料(基本資料)
	$sql_select="select c.sch_id,a.stud_id,a.curr_class_num,a.stud_name,a.stud_person_id,a.stud_sex,
	                    a.stud_birthday, b.graduation,b.kind_id,b.special,b.income,b.unemployed,
	                    d.guardian_name,a.stud_tel_1,a.stud_tel_3,a.addr_zip,a.stud_addr_2
                 from chc_basic12 b 
                 left join stud_base a 
                 on a.student_sn = b.student_sn 
                 left join stud_domicile d 
                 on a.student_sn = d.student_sn,school_base c
                 where b.academic_year=".$year." 
                 order by a.curr_class_num";
               //echo "<br>".__LINE__."<br>".$sql_select."<br>";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
  //序號    
	$i=0;
	while( list($sch_id,$stud_id,$curr_class_num,$stud_name,$stud_person_id,$stud_sex,$stud_birthday,$graduation,$kind_id,$special,$income,$unemployed,$guardian_name,$stud_tel_2,$stud_tel_3,$addr_zip,$stud_addr_2)=$recordSet->FetchRow() ){
	    //1.地區代碼，2碼，彰化為08
	    $area = "08";

	    //2.集報單位，6碼，靠右，不足左側補0
	    $sch_id = sprintf('%06s',$sch_id);
	    
	    //3.序號，5碼，靠右，不足左側補0
	    $i++;
	    $sn_excel=$i;
	    $sn = sprintf("%05s",$i);
	    
	    //4.學號，8碼，靠左，不足右側補半形空白
	    $stud_id_excel = $stud_id;
	    $stud_id = sprintf("%-8s",$stud_id);
	    
	    //5.班級，2碼, 靠右，不足左側補0
	    $class = substr($curr_class_num,1,2);
	    
	    //6.座號，2碼，靠右，不足左側補0
	    $num = substr($curr_class_num,3,2);
	    
	    //7.姓名去空白，半形和全形
	    $stud_name = str_replace(" ","",$stud_name);
	    $stud_name = str_replace("　","",$stud_name);
	    
	    //姓名共要30碼，靠左，不足補半形空白
	    $stud_name = sprintf("%-30s",$stud_name);
	    
	    //8.身分證首字要大寫，共要10碼，不足補*
	    $stud_person_id = sprintf("%-'*10s",strtoupper($stud_person_id));
	    
	    //9.性別1碼$stud_sex
	    
	    //生日拆成年月日，10.年2碼，11.月2碼，12.日2碼
	    $birth_date = explode("-",$stud_birthday);
	    $birth_year = sprintf("%02s",$birth_date[0]-1911);
	    $birth_mon = sprintf("%02s",$birth_date[1]);
	    $birth_day = sprintf("%02s",$birth_date[2]);
	    
	    //13.畢業學校代碼$sch_id，6碼，與集報單位同    
	    //14.畢業年為$grad_year，民國年3碼    
	    //15.畢肄業為$graduation，1碼    
	    //16.學生身分為$kind_id，1碼    
	    //17.身心障礙為$special，1碼
	        
	    //18.就學區，2碼，留2格半形空白
	    $stu_area = "  ";
	    
	    //19.低收入戶及20.中低收入戶各1碼
	    if($income == 0){
	      $low_income = 0;
	      $mlow_income = 0;
	    }elseif($income == 1){
	      $low_income = 0;
	      $mlow_income = 1;
	    }elseif($income ==2){
	      $low_income = 1;
	      $mlow_income = 0;
	    }
    
	    //21.失業？1碼$unemployed
	    
	    //22.資料授權，1碼，全部要同意授權
	    $authorize = "1";
	    
	    //23.家長姓名去空白，半形和全形
	    $guardian_name = str_replace(" ","",$guardian_name);
	    $guardian_name = str_replace("　","",$guardian_name);
	    
	    //家長姓名共要30碼，不足補半形空白，靠左，不足補半形空白
	    $guardian_name = sprintf("%-30s",$guardian_name);
	    
	    //24.室內電話14碼，加區碼，靠左，不足補空白？？
	    //去空白和括號和減號
	    $stud_tel_2 = str_replace(" ","",$stud_tel_2);
	    $stud_tel_2 = str_replace("-","",$stud_tel_2);
	    $stud_tel_2 = str_replace("(","",$stud_tel_2);
	    $stud_tel_2 = str_replace(")","",$stud_tel_2);
	    
	     //若留室內電話者(9碼以下，因芬園鄉電話有8碼)，補04
	    if(strlen($stud_tel_2)<9 and strlen($stud_tel_2)>0){
	      $stud_tel_2 = sprintf("%-14s","04".$stud_tel_2);
	    }else{
	      $stud_tel_2 = sprintf("%-14s",$stud_tel_2);
	    }
	    
	    //25.行動電話14碼，靠左，不足補空白？？
	    $stud_tel_3 = str_replace(" ","",$stud_tel_3);
	    $stud_tel_3 = str_replace("-","",$stud_tel_3);
	    $stud_tel_3 = str_replace("(","",$stud_tel_3);
	    $stud_tel_3 = str_replace(")","",$stud_tel_3);
	    //取行動電話前10碼，因為有的欄位被填上兩支行動電話
	    $stud_tel_3 = substr($stud_tel_3,0,10);
	    $stud_tel_3 = sprintf("%-14s",$stud_tel_3);
	    
	    //26.郵遞區號，取前3碼，有的填上5碼，靠左，不足補空白？？
	    $addr_zip = substr($addr_zip,0,3);
	    $addr_zip = sprintf("%-3s",$addr_zip);
	    
	    //27.地址80碼，靠左，不足補空白？？數字以半形阿拉伯數字表示？(暫不理)    
	    $stud_addr_2 = sprintf("%-80s",$stud_addr_2);
		$mem=$stud_person_id;                    
    	$all_data[$mem][]=$area;
    	$all_data[$mem][]=$sch_id;
    	$all_data[$mem][]=$sn_excel;
    	$all_data[$mem][]=$stud_id_excel;
    	$all_data[$mem][]=$class;
    	$all_data[$mem][]=$num;
    	$all_data[$mem][]=$stud_name;
    	$all_data[$mem][]=$stud_person_id;
    	$all_data[$mem][]=$stud_sex;
    	$all_data[$mem][]=$birth_year;
    	$all_data[$mem][]=$birth_mon;
    	$all_data[$mem][]=$birth_day;
    	$all_data[$mem][]=$sch_id;
    	$all_data[$mem][]=$grad_year;
    	$all_data[$mem][]=$graduation;
    	$all_data[$mem][]=$kind_id;
    	$all_data[$mem][]=$special;
    	$all_data[$mem][]=$stu_area;
    	$all_data[$mem][]=$low_income;
    	$all_data[$mem][]=$mlow_income;
    	$all_data[$mem][]=$unemployed;
    	$all_data[$mem][]=$authorize;
    	$all_data[$mem][]=$guardian_name;
    	$all_data[$mem][]=$stud_tel_2;
    	$all_data[$mem][]=$stud_tel_3;
    	$all_data[$mem][]=$addr_zip;
    	$all_data[$mem][]=$stud_addr_2;
	};




//取得第二部份資料(彰化縣專屬資料)
	$obj->all();
	foreach($obj->stu as $key=>$val){
    	//品德服務分數最高20分
        $score_morality=$val['score_service']+$val['score_reward']+$val['score_fault'];
		if($score_morality>20){
			$score_morality=20;
		}
		//績優表現分數最高16分
        $score_display=$val['score_balance']+$val['score_club']+$val['score_race']+$val['score_physical'];
		if($score_display>16){
			$score_display=16;
		}
		$stud_person_id=$val[stud_person_id];
		$mem=$stud_person_id;
		//$data1[$mem][]=$val[stud_name];
		//$data1[$mem][]=$val[stud_person_id];
		//$data1[$mem][]=intval($val[birth_year]);
		//$data1[$mem][]=intval($val[birth_month]);
		//$data1[$mem][]=intval($val[birth_day]);
		//$result = array_merge((array)$beginning, (array)$end);
		$data1[$mem]=$all_data[$mem];
		$data1[$mem][]='';  //自補欄位
		$data1[$mem][]='';  //自補欄位
		$data1[$mem][]='';  //自補欄位
		$data1[$mem][]=$val[income];
		$data1[$mem][]=$val[score_nearby];
		$data1[$mem][]=$val[score_service];
		$data1[$mem][]=$val[score_reward];
		$data1[$mem][]=$val[score_fault];
		//$data1[$mem][]=$score_morality;   //品德服務分數
		$data1[$mem][]=$val[score_balance];
		$data1[$mem][]=$val[score_club];
		$data1[$mem][]=$val[score_race];
		$data1[$mem][]=$val[score_physical];
        //$data1[$mem][]=$score_display;    //績優表現分數
		//$mem++;
	}

	//$obj->debug_msg("第".__LINE__."行 data1 ", $data1);


	$filename ="chc_export_".$grad_year.".xls" ;
	//103.03.21起採用
	$myhead=array("考區代碼","集報單位代碼","序號","學號","班級","座號","學生姓名","身分證統一編號","性別","出生年(民國年)","出生月","出生日","畢業學校代碼","畢業年(民國年)","畢肄業","學生身分","身心障礙","就學區","低收入戶","中低收入戶","失業勞工子女","資料授權","家長姓名","市內電話","行動電話","郵遞區號","通訊地址","原住民是否含母語認證","非中華民國身分證號","特殊生加分百分比","經濟弱勢","就近入學","服務學習","獎勵紀錄","生活教育","均衡學習","社團參與","競賽表現","體適能");
	//include_once "../../include/sfs_case_excel.php";
	$x=new sfs_xls();
	$x->setUTF8();//$x->setVersion(8);
	$x->setBorderStyle(1);
	$x->filename=$filename;
	$x->setRowText($myhead);
	$x->addSheet("year".$grad_year);
	$x->items=$data1;
	$x->writeSheet();

	$x->process();

}


//秀出網頁布景標頭
head("匯出資料");
print_menu($menu_p);

//顯示SFS連結選單(欲使用請拿開註解)

echo make_menu($school_menu_p);


$obj->display();

//佈景結尾
//foot();





//物件class

class chc_seme{

	var $CONN;//adodb物件

	var $smarty;//smarty物件
	var $stu;//學生資料
	var $subj;//科目陣列
	var $rule;//等第

	var $Stu_Seme;//學生的學期陣列

	var $IS_JHORES;//國中小

	var $year;//學年

	var $seme;//學期

	var $YS='year_seme';//下拉式選單學期的奱數名稱

	var $year_seme;//下拉式選單班級的奱數值

	var $Sclass='class_id';//下拉式選單班級的奱數名稱



	//建構函式

	function chc_seme($CONN,$smarty){
		global $IS_JHORES;
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
		$this->IS_JHORES=$IS_JHORES;
		$aa=curr_year();
		$bb=curr_seme();
		$this->YearSeme=$aa.$bb;
		$this->Year=$aa;
	}

	//初始化

	function init() {}

	//程序


	//擷取資料

	function all(){
		$this->sch=get_school_base();
		$this->stu=$this->get_stu();

	}

	//顯示

	function display(){

		echo '<table  width="100%"  border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#9EBCDD" style="table-layout: fixed;word-wrap:break-word;font-size:10pt">
<tr style="font-size:11pt" bgcolor="#9EBCDD"><td>
	說明：<br>
	1.根據檔案規格書之規範，提供「TXT」及「EXCEL」兩種資料檔格式之匯出。<br>
	2.僅匯出「現今學年度」之資料檔。<br>
	3.若您使用『匯出EXCEL(方法1)』所產出的檔案內容會有亂碼現象，請改用『匯出EXCEL(方法2)』來產出檔案，此兩者的資料內容完全相同。<br>
	<hr>
	</td></tr></table>';
	echo '<table  width="100%"  border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#9EBCDD" style="table-layout: fixed;word-wrap:break-word;font-size:10pt">
	<tr style="font-size:11pt" bgcolor="#9EBCDD">
	
	<td>'.$this->select.'</td></tr>';
		echo '<tr style="font-size:11pt" bgcolor="#9EBCDD">
	
	<td>';

//  <input type="submit" name="chc_txt" value="匯出TXT">　||　

		echo '<form name="form1" method="post" action="">
     學生基本資料檔<br>
  <input type="submit" name="basic_txt" value="匯出TXT">　||　 
  <input type="submit" name="basic_excel" value="匯出EXCEL(方法1)">
  <input type="submit" name="basic_excel_2" value="匯出EXCEL(方法2)">
  </form>
<hr>
<form name="form2" method="post" action="">
  彰化縣免試入學超額比序項目積分資料檔<br>
  <input type="submit" name="chc_excel" value="匯出EXCEL(方法1)">
  <input type="submit" name="chc_excel_2" value="匯出EXCEL(方法2)">
</form>
<hr>
<form name="form2" method="post" action="">
  彰化區高級中等學校免試入學分發學生資料檔<br>
  <font color=red>※※操作步驟</font><br>
  1.在資料檔中，「原住民是否含母語認證」、「非中華民國身分證號」、「特殊生加分百分比」三欄位請自行輸入。<br>
  2.將本資料檔內容資料貼到「彰化區高級中等學校報名免試系統平臺」的範本檔中。<br>
  　　方法：開啟匯出檔，「全選」->「複製」->「貼上」（請用「選擇性貼上」->「值」），<br>
  　　再匯入到「彰化區高級中等學校報名免試系統平臺」<br>
  

  <input type="submit" name="chc_excel_103" value="匯出EXCEL">
</form>';
		echo '</td></tr>';
		echo '</table>';

	}



	//除錯
	function debug_msg($title, $showarry){
		echo "<pre>";
		echo "<br>$title<br>";
		print_r($showarry);
	}

/* 取學生陣列,取自stud_base表與stud_seme表*/
	function get_stu(){

		$SQL="select a.*, b.*, c.* 
		      from stud_base a, stud_seme b, chc_basic12 c  
		      where c.student_sn=a.student_sn 
		      and c.student_sn=b.student_sn 
		      and c.academic_year='".$this->Year."' 
		      and b.seme_year_seme='".$this->YearSeme."' 
		      order by b.seme_year_seme asc, a.curr_class_num asc";
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		if ($rs->RecordCount()==0) return"找不到學生！";

	    while ($row = $rs->FetchRow() ) {
	    	$tmp_birth=explode('-',$row[stud_birthday]);
	    	$row[birth_year]=$tmp_birth[0]-1911;
	    	$row[birth_month]=$tmp_birth[1];
	    	$row[birth_day]=$tmp_birth[2];
			$Stu_Seme[]=$row;
		}

		return $Stu_Seme;
	}





}

