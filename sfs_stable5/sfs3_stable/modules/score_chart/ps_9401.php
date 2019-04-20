<?php 
//$Id: ps_9401.php 8851 2016-03-14 00:47:16Z chiming $
require_once("chc_config.php");

sfs_check();
$ceilox=$_POST[ceilox];
//2012/12/7 by smalldh 加入社團與服務學習記錄
$stud_service=$_POST['stud_service'];
$stud_club=$_POST['stud_club'];
$stud_club_score=$_POST['stud_club_score'];

//////  從SFS3內建的函式取學校資料函式---------------------
$sch_data=get_school_base();

// smarty的樣版路徑設定  -----------------------------------
$template_dir = $SFS_PATH."/".get_store_path()."/templates/";
//  預設的樣本檔  --(命名：ps國小,國中jh_head表頭.htm)
$tpl_defult_ps=array("head"=>"ps_head.htm","body"=>"ps_body.htm","end"=>"ps_end.htm");
$tpl_defult_jh=array("head"=>"jh_head.htm","body"=>"jh_body.htm","end"=>"jh_end.htm");
$tpl_defult_scope=array("head"=>"scope_head.htm","body"=>"scope_body.htm","end"=>"scope_end.htm");
$tpl_defult_scope_simple_chk=array("head"=>"scope_head.htm","body"=>"schk_body.htm","end"=>"schk_end.htm");
$tpl_defult_scope_complete_chk=array("head"=>"scope_head.htm","body"=>"cchk_body.htm","end"=>"schk_end.htm");
$tpl_defult_scope_none=array("head"=>"scope_head.htm","body"=>"scope_none_body.htm","end"=>"scope_end.htm");
$tpl_defult_scope_simple_chk_none=array("head"=>"scope_head.htm","body"=>"schk_none_body.htm","end"=>"schk_end.htm");
$tpl_defult_scope_complete_chk_none=array("head"=>"scope_head.htm","body"=>"cchk_none_body.htm","end"=>"schk_end.htm");

//  自訂的樣本檔名  -----------------------------------
$tpl_self=array("head"=>"my_head.htm","body"=>"my_body.htm","end"=>"my_end.htm");

//判斷範本
switch($_POST[chart_kind]) {
	case 2:
		$tpl_defult=$tpl_defult_scope;
		break;
	case 3:
		$tpl_defult=$tpl_defult_scope_simple_chk;
		break;
	case 4:
		$tpl_defult=$tpl_defult_scope_complete_chk;
		break;
	case 5:
		$tpl_defult=$tpl_defult_scope_none;
		break;
	case 6:
		$tpl_defult=$tpl_defult_scope_simple_chk_none;
		break;
	case 7:
		$tpl_defult=$tpl_defult_scope_complete_chk_none;
		break;
	default:
		($IS_JHORES==6)? $tpl_defult=$tpl_defult_jh:$tpl_defult=$tpl_defult_ps;
}

//  如果沒有自訂的樣本,就用預設的  --------------------
($_POST[chart_kind]==9 && file_exists($template_dir.$tpl_self[head])) ? $tpl=$tpl_self:$tpl=$tpl_defult;

$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";

$chk_item=chk_kind();//讀取檢核表表現狀況文字
$add_memo_file=chk_file($_POST[grade].'.txt');//讀取備註文字
//$add_text_memo=upload_read($_POST[grade].'.txt');
$img_title=get_title_pic();//讀取職稱圖章
	
if($_POST){
	//判斷是否為彰化縣學校
	$pos=strpos($school_name, "彰化縣");
	$is_chc=0;
	if($pos!==false ){
		$is_chc=1;
	}
	$smarty->assign("is_chc",$is_chc);

	$year_seme=split("_",$_POST[year_seme]);//093_1

	$days=get_seme_dates($_POST[year_seme],$_POST[grade]);//上課日數
	$smarty->assign("unit",($IS_JHORES)?"節":"天");

	$smarty->assign("school_name",$sch_data[sch_cname]);

	$smarty->assign("add_memo_file",$add_memo_file);
	$smarty->assign("img_1",$img_title["校長"]);
	$img_2=($img_title["教務主任"]=="")?$img_title["教導主任"]:$img_title["教務主任"];
	$smarty->assign("img_2",$img_2);
	$img_3=($img_title["學務主任"]=="")?$img_title["訓導主任"]:$img_title["學務主任"];
	$smarty->assign("img_3",$img_3);
	
	$smarty->assign("sign_1_name",$sign_1_name);
	$smarty->assign("sign_2_name",$sign_2_name);
	$smarty->assign("sign_3_name",$sign_3_name);
	
	$smarty->assign("sign_1_form",$sign_1_form);
	$smarty->assign("sign_2_form",$sign_2_form);
	$smarty->assign("sign_3_form",$sign_3_form);
	
	if ($sign_3_title=="") $sign_3_title=0;
	$smarty->assign("sign_3_title",$SIGN_3_TITLE_ARR[$sign_3_title]);
	$smarty->assign("text_title",($text_title)?$text_title:"學習描述文字說明");
	$smarty->assign("days",$days);

	$class_ary=get_class_info($_POST[grade],$_POST[year_seme]);
	$smarty->display($template_dir.$tpl[head]);
	$i=0;
	
foreach ($_POST[class_id] as $class_id_key=>$null) {
//095_1_05_01
	////是否列印等第的處理,最終值帶到smarty
	$chk_tmp=split("_",$class_id_key);
	$chk_prt=$chk_tmp[0]-($chk_tmp[2]-$IS_JHORES); 
	//echo $chk_prt;
	($chk_prt >= 94 ) ? $chk_prt=0: $chk_prt= 1;//0不印,1要印

        //新增是否顥示文字描述中分科名稱
	$class_data = new data_class($class_id_key,$disable_subject_memo_title);
//print_r($class_data);	

	$seme_scope=$class_data->seme_scope();

	$seme_nor=$class_data->seme_nor();
	$seme_rew=$class_data->seme_rew();
	$seme_absent=$class_data->seme_absent();

	//echo "<pre>";
	//print_r($seme_scope);

	$smarty->assign("class_info",$class_ary[$class_id_key]);//班級學年度資料
	$smarty->assign("subject_nor",$class_data->subject_nor);
	$smarty->assign("subject_rew",$class_data->subject_rew);
	$smarty->assign("subject_abs",$class_data->subject_abs);
	$smarty->assign("chk_prt",$chk_prt);//將是否列印等第的參考值入
	$smarty->assign("IS_JHORES",$IS_JHORES);
	$smarty->assign("itemdata",get_chk_item($year_seme[0],$year_seme[1]));//檢核表項目
	
	//2012/12/7 by smallduh 增加社團與服務學習記錄=========================================
  $seme_year_seme=sprintf('%03d%1d',$year_seme[0],$year_seme[1]); //1011 ,1001 ,0991 .....格式
  
  $class=class_id_2_old($class_id_key);
  
  $smarty->assign("stud_service",$stud_service);  // 是否列印服務學習資料
  $smarty->assign("stud_club",$stud_club);  // 是否列印社團資料
  $smarty->assign("stud_club_score",$stud_club_score);  // 是否列印社團成績
  //=====================================================================================
  
	foreach ($class_data->stud_base as $student_sn=>$stud) {

		if($i>0){
			$smarty->assign("break_page","<P STYLE='page-break-before: always;'>");
		}else {
			$smarty->assign("break_page","");
		}
    
    
    //2012/12/7 by smallduh 增加社團與服務學習記錄 ================================================================
   	//取得服務學習與社團活動記錄 2012/12/7 以 $student_sn及 $seme_year_seme 為條件索引
	 
   	if ($IS_JHORES==6 and $stud_service=='checked') {
	   
	   $query="select b.sn,a.minutes,a.feedback,b.service_date,b.department,b.item,b.memo,b.sponsor from stud_service_detail a,stud_service b where a.item_sn=b.sn and b.year_seme='$seme_year_seme' and b.confirm=1 and a.student_sn=$student_sn order by service_date";
	   
	   
	   $res=mysql_query($query);
	   $service_detail="";
	   $MINS=0; $HOURS=0;
	   while ($row=mysql_fetch_array($res)) {
	   	 $service_detail[$row['sn']]['service_date']=$row['service_date'];
	     $service_detail[$row['sn']]['department']=getPostRoom($row['department']);
	     $service_detail[$row['sn']]['sponsor']=$row['sponsor'];
	     $service_detail[$row['sn']]['item']=$row['item'];
	     $service_detail[$row['sn']]['memo']=$row['memo'];
	     $service_detail[$row['sn']]['hour']=round($row['minutes']/60,2);
	     $service_detail[$row['sn']]['feedback']=$row['feedback'];
	     $MINS+=$row['minutes'];
	   }
	   
	   $HOURS=round($MINS/60,2);
	   
	   $smarty->assign("service_detail",$service_detail); //本學生本學期的服務明細
	   $smarty->assign("HOURS",$HOURS); //總服務時數
	  }
	
	  if ($IS_JHORES==6 and $stud_club=='checked') {
	   $query="select association_name,score,description,club_sn,stud_feedback from association where seme_year_seme='$seme_year_seme' and student_sn=$student_sn";
	   $res=mysql_query($query);
	   $club_detail="";
	   $t=0;
	   while ($row=mysql_fetch_array($res)) {
	   	$query="select pass_score from stud_club_base where club_sn='".$row['club_sn']."'";
	   	$res_pass=mysql_query($query);
	   	list($pass_score)=mysql_fetch_row($res_pass);
	   	if ($row['score']>=$pass_score) {
	   	  $t++;
	      $club_detail[$t]['association_name']=$row['association_name'];
	      $club_detail[$t]['score']=score2str($row['score'],$class);
	      $club_detail[$t]['description']=$row['description'];
	      $club_detail[$t]['stud_feedback']=$row['stud_feedback'];
	    }
	   }
	  		  	
	   $smarty->assign("club_detail",$club_detail);
	   
	  }
    //============================================================================================================================
    

		$smarty->assign("stud",$stud);
		$smarty->assign("seme_scope",$seme_scope[$student_sn]);
		$smarty->assign("seme_nor",$seme_nor[$student_sn]);
		$smarty->assign("seme_rew",$seme_rew[$student_sn]);
		$smarty->assign("seme_absent",$seme_absent[$student_sn]);
		$smarty->assign("chk_value",get_chk_value($student_sn,$year_seme[0],$year_seme[1],$chk_item,"value"));//讀取檢核表內容
		$smarty->display($template_dir.$tpl[body]);
		$i++;
		}
	}
}

//傳回服務單位 ==================================================================================================================
function getPostRoom($room_id) {
  global $CONN;
  $sql_select = "select room_name from school_room where room_id='$room_id'";
  $result=$CONN->Execute($sql_select);
  $room_name=$result->fields['room_name'];	
  return $room_name;
}
?>
