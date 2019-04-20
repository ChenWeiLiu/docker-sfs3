<?php 
//$Id: ps_9401.php 7078 2013-01-16 07:27:26Z smallduh $

require_once("config.php");
include_once "../../include/sfs_case_score.php";
include_once "../../include/sfs_case_dataarray.php";

require_once ("../score_chart/chc_class2.php");

//獎懲分類
$reward_arr=array("1"=>"嘉獎一次","2"=>"嘉獎二次","3"=>"小功一次","4"=>"小功二次","5"=>"大功一次","6"=>"大功二次","7"=>"大功三次","-1"=>"警告一次","-2"=>"警告二次","-3"=>"小過一次","-4"=>"小過二次","-5"=>"大過一次","-6"=>"大過二次","-7"=>"大過三次");

//競賽分類
$level_array=array(1=>'國際',2=>'全國、臺灣區',3=>'區域性（跨縣市）',4=>'省、直轄市',5=>'縣市區（鄉鎮）',6=>'校內');
$squad_array=array(1=>'個人賽',2=>'團體賽');

sfs_check();

//讀取模組變數
$M_SETUP=get_module_setup('score_nor');

$default_title=$_POST['default_title'];

//2012/12/7 by smalldh 加入社團與服務學習記錄
$stud_service=$_POST['stud_service'];						//是否列印服務學習
$stud_club=$_POST['stud_club'];									//是否列印社團
$stud_club_score=$_POST['stud_club_score'];			//是否列印社團成績
$stud_chk_data=$_POST['stud_chk_data']; 				//是否列印日常生活表現
$stud_chk_data_detail=$_POST['stud_chk_data_detail']; 				//是否列印日常生活檢核表
$stud_reward=$_POST['stud_reward']; 						//是否列印獎懲
$stud_reward_detail=$_POST['stud_reward_detail']; 						//是否列印獎懲明細

$stud_leader=$_POST['stud_leader']; 						//是否列印幹部資料
$stud_race=$_POST['stud_race']; 								//是否列印競賽資料
$stud_absent=$_POST['stud_absent']; 						//是否列印出缺席
$stud_absent_detail=$_POST['stud_absent_detail']; 						//是否列印出缺席明細

$default_txt=$_POST['default_txt'];							//成績單註解文字

//////  從SFS3內建的函式取學校資料函式---------------------
$sch_data=get_school_base();

$img_title=get_title_pic();//讀取職稱圖章

if($_POST){
	
	$year_seme=split("_",$_POST[year_seme]);//093_1	
	$sel_year=$year_seme[0]; $sel_seme=$year_seme[1];  //取得學年及學期
  $seme_year_seme=sprintf('%03d%1d',$year_seme[0],$year_seme[1]); //1011 ,1001 ,0991 .....格式

	//學校名稱, 主任及校長的職章
	$smarty->assign("school_name",$sch_data[sch_cname]);
	$smarty->assign("img_1",$img_title["校長"]);
	$img_3=($img_title["學務主任"]=="")?$img_title["訓導主任"]:$img_title["學務主任"];
	$smarty->assign("img_3",$img_3);
	
  $query="select title_name from teacher_title where teach_title_id=3";
  list($sign_3_title)=mysql_fetch_row(mysql_query($query));
	
	$smarty->assign("sign_3_title",$sign_3_title);


	$class_ary=get_class_info($_POST[grade],$_POST[year_seme]);
	

  //傳送各種勾選列印的項目
  $smarty->assign("default_title",$default_title);
	$smarty->assign("IS_JHORES",$IS_JHORES);
  
  $smarty->assign("stud_service",$stud_service);  			// 是否列印服務學習資料
  $smarty->assign("stud_club",$stud_club);  						// 是否列印社團資料
  $smarty->assign("stud_club_score",$stud_club_score);  // 是否包含社團成績
  $smarty->assign("stud_chk_data",$stud_chk_data);  		// 是否列印日常生活檢核
  $smarty->assign("stud_reward",$stud_reward);  				// 是否列印獎懲資料
 	$smarty->assign("stud_reward_detail",$stud_reward_detail);				// 是否列印獎懲明細
 	$smarty->assign("stud_absent",$stud_absent);  				// 是否列印出缺席資料
 	$smarty->assign("stud_absent_detail",$stud_absent_detail);  				// 是否列印出缺席資料明細
	$smarty->assign("stud_race",$stud_race);  						// 是否列印競賽資料
	$smarty->assign("stud_leader",$stud_leader);  				// 是否列印幹部資料


			//計算學期起迄日
			//起始日
 			$sql="select day from school_day where year='$sel_year' and seme='$sel_seme' and day_kind='start'";
  		$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
 			$seme_start_date=$res->fields[0];
 
 			//結束日
 			$sql="select day from school_day where year='$sel_year' and seme='$sel_seme' and day_kind='end'";
 			$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
 			$seme_end_date=$res->fields[0];
 			
 $smarty->assign("seme_start_date",$seme_start_date);  	//學期起始日
 $smarty->assign("seme_end_date",$seme_end_date); 			//學期結束日

 //日常表現 , 檢核表項目
	$itemdata=get_chk_item($sel_year,$sel_seme);


	//開始
	
	$page_i=0;  //計數, 已列印了幾頁, 第二頁後一開始都要送分頁
	
 foreach ($_POST[class_id] as $class_id_key=>$null) {
 	
 	//依班級不同, 傳入班級學年度資料
	$smarty->assign("class_info",$class_ary[$class_id_key]);//班級學年度資料

  //取得該班級的資料
	$class_data = new data_class($class_id_key,$disable_subject_memo_title);
	
  $class=class_id_2_old($class_id_key);


  //echo "<pre>";
	//print_r($seme_scope);


  //=====================================================================================
 	foreach ($class_data->stud_base as $student_sn=>$stud) {  
 
		if($page_i>0){
			$smarty->assign("break_page","<P STYLE='page-break-before: always;'>");
		}else {
			$smarty->assign("break_page","");
		}
		
		//競賽記錄 依證書日期判定學期 ==============================================================================
		if ($stud_race=='checked') {

		  $RACE=get_race_record($seme_start_date,$seme_end_date,$student_sn);
		  
		  $race_print="
		   	<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse;font-size:10pt' bordercolor='#111111' width='100%'>
  				<tr align='center' bgcolor='#AAAAAA'>
						<td width='15'>NO.</td>
						<td width='120' colspan='2'>範圍性質</td><td>競賽名稱</td><td>得獎名次</td><td>證書日期</td><td>主辦單位</td><td>字號</td><td>備註</td>
				</tr>";
			$i=0;
			if (count($RACE)>0) {
			foreach ($RACE as $sn=>$race) {
	    $i++;
	    $race_print.="
	    <tr>
					<td>".$i."</td>
					<td>".$level_array[$race['level']]."</td>
					<td>".$squad_array[$race['squad']]."</td>
					<td align='left'>".$race['name']."</td>
					<td>".$race['rank']."</td>
					<td>".$race['certificate_date']."</td>
					<td align='left'>".$race['sponsor']."</td>
					<td align='left'>".$race['word']."</td>
					<td align='left'>".$race['memo']."</td>
			</tr>";	    
			}
		 } else {
	    $race_print.="<tr><td colspan='9'>無任何登載記錄</td></tr>";
		 }
     $race_print.="</table>";

		 $smarty->assign("race_print",$race_print);
		
		}
		
		//幹部資料 2013/09/03 =============================================================================================
		if ($stud_leader=='checked') {
			
			$sql="select seme_class from stud_seme where student_sn='$student_sn' and seme_year_seme='$seme_year_seme'";
			$res=$CONN->Execute($sql);
			
			$seme_key=substr($res->fields['seme_class'],0,1)."-".$sel_seme;
			
			//取出該生幹部相關資料(包含所有學期的陣列資料, 以下只顯示本學期, 其他學期則以 hidden 方式)
			$query="select * from career_self_ponder where student_sn='$student_sn' and id='3-2'";
 			$res_ponder=$CONN->Execute($query);
 			$ponder_array=unserialize($res_ponder->fields['content']); //二維陣列
 			
 			//社團幹部
 		 $association_leader="";
		 $query="select association_name,score,stud_post from association where seme_year_seme='$seme_year_seme' and student_sn=$student_sn";
		 $res=$CONN->Execute($query);
	    
	   while ($row=$res->fetchRow()) {
	   	//校內社團, 要檢查分數, 外校社團則一律通過
	   		$query="select pass_score from stud_club_base where club_sn='".$row['club_sn']."'";
	   		$res_pass=mysql_query($query);
	   		list($pass_score)=mysql_fetch_row($res_pass);
	     
	   	if ((($row['score']>=$pass_score and $row['club_sn']>0) or ($row['club_sn']==0) and $row['stud_post']!="")) {
 			 $association_name=$row['association_name'];
 			 $stud_post=$row['stud_post'];
       $association_leader.=($association_leader=='')?$stud_post."(".$association_name.")":"、".$stud_post."(".$association_name.")";
	    }
	   } 				

      $leader_print="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
      <tr><td align='center'>班級幹部</td><td align='center'>小老師</td></td><td align='center'>社團幹部</td></tr>";
      $leader_print.="<tr><td align='center'>";
      $leader_print.=($ponder_array[$seme_key][1][1]!="")?$ponder_array[$seme_key][1][1]:"&nbsp;";
      $leader_print.=($ponder_array[$seme_key][1][2]!="")?"、".$ponder_array[$seme_key][1][2]:"";
      $leader_print.="</td><td align='center'>";
      $leader_print.=($ponder_array[$seme_key][2][1]!="")?$ponder_array[$seme_key][2][1]:"";
      $leader_print.=($ponder_array[$seme_key][2][2]!="")?"、".$ponder_array[$seme_key][2][2]:"&nbsp;";
      $leader_print.="</td><td align='center'>";
      $leader_print.=($association_leader!="")?$association_leader:"&nbsp;";      
      $leader_print.="</td></tr></table>";
				
			$smarty->assign("leader_print",$leader_print);
	  }
		//缺曠課統計 2013/09/02 =============================================================================================
		if ($stud_absent=='checked') {
			//是否印明細
			if ($stud_absent_detail=='checked') {
			 
			$print_str=stud_absent_statForm($sel_year,$sel_seme,$class_id_key,$stud['stud_id'],$seme_start_date,$seme_end_date);
			
			//不需明細, 只要總表時
			} else {
			//取得缺曠課類別
			$absent_kind_array= SFS_TEXT("缺曠課類別");
		
			//增加集會這個類別
			$abkind_TXT="<td>集會</td>";
	
			//製作標題
			foreach($absent_kind_array as $abkind){
			$abkind_TXT.="<td>$abkind</td>";
			}
		
			$print_str="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"100%\">\n
			<tr>
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"14%\">集會</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"14%\">曠課</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"14%\">事假</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"14%\">病假</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"14%\">喪假</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"14%\">公假</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"16%\">不可抗力</td></tr>\n";
		
			//取得該學生出缺席統計資料
			$aaa=getOneAbsent($stud['stud_id'],$sel_year,$sel_seme,"種類");
		
			//各種缺曠課數
		
				$d_b=($i%5==0 || $i==count($stud))?"1.5pt":"0.75pt";
			
				$sections_data="<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt 1.5pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">$aaa[f]</font></td>\n";
			foreach($absent_kind_array as $abkind){
				$r_b=($abkind=="不可抗力")?"1.5pt":"0.75pt";
				$sections_data.="<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt $r_b 1.5pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">$aaa[$abkind]</font></td>\n";
			}
		
			$print_str.="<tr>".$sections_data."</tr></table>\n";
			
			} // end if $stud_abcent_detail
				
					
			$smarty->assign("absent_print",$print_str);
					
		} // end if $stud_absent	
		
		
		//獎懲記錄 2013/09/01 =============================================================================================
		if ($stud_reward=='checked') {
			
			$smarty->assign("reward_kind",$reward_arr);
			$query="select * from reward where student_sn='$student_sn' and reward_year_seme='".$seme_year_seme."' order by reward_div,reward_date desc";
			$res=$CONN->Execute($query);
			$smarty->assign("reward_rows",$res->GetRows());
			for($i=1;$i<=6;$i++) { $f[$i]=0; $t[$i]=0; }
			$smarty->assign("f",$f);
		}
		
    //日常檢核表 2013/08/31 ======================================
		
		if ($stud_chk_data=='checked') {
			
    $sn_value=$student_sn;
    
			$chk_data="";
			
			//是否勾選檢核表
			if ($stud_chk_data_detail=="checked") {
					
			//檢核表值
			$chk_item=chk_kind();
			$chk_value=get_chk_value($sn_value,$sel_year,$sel_seme,$chk_item,"value");
			
			//開始產生HTML資料 檢核表
			$chk_data="<table  STYLE='font-size: ".$item_px."px' border=2 cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolorlight='#000000' bordercolordark='#000000' width='100%'>
						<tr bgcolor='#FFCCCC'><td colspan='2' align='center'>日常生活檢核項目</td><td align='center'>表現狀況</td><td align='center'>備註</td></tr>";
			
			//重整資料為二維陣列
			$data_array=array();			
			foreach($itemdata['items'] as $key=>$value) {
				$main=$value['main'];
				$sub=$value['sub'];
				$data_array[$main][$sub]=$value['item'];
			}
			//詳式檢核項目情形列表
			foreach($data_array as $key=>$main) {
				$rowspan=count($main)-1;
				$chk_data.="<tr><td rowspan=$rowspan align='center'>".$main[0]."</td>";
				for($i=1;$i<=$rowspan;$i++){
					$chk_data.="<td>".$main[$i]."</td>";
					$chk_data.="<td align='center' width='120'>".$chk_value[$key][$i]['score']."</td><td>".$chk_value[$key][$i]['memo']."</td></tr>";					
				}
			}
			$chk_data.="</table>";
			
		  } // end if ($stud_chk_data_detail=="checked")
		  
			//其他表現文字
			$query="select * from stud_seme_score_nor where seme_year_seme='$seme_year_seme' and student_sn='$sn_value' order by ss_id";
			//echo $query;
			//exit();
			$res=$CONN->Execute($query) or die("SQL錯誤! query=$query");
			$r=array();
			while(!$res->EOF) {
				$r[$res->fields['ss_id']]=$res->fields['ss_score_memo'];
				$res->MoveNext();
			}
			$nor_memo=$r;
			
			//行為描述
			$chk_data.="<table border=2 cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolorlight='#000000' bordercolordark='#000000' width='100%'>
						<tr><td rowspan=4 align='center' bgcolor='#c4d9ff' width=80>行為描述<BR>與<BR>具體建議</td>
						<td align='center' bgcolor='#c4d9ff' width=80>日常生活</td><td>$nor_memo[0]</td></tr>
						<tr><td align='center' bgcolor='#c4d9ff' width=80>團體活動</td><td>$nor_memo[1]</td></tr>
						<tr><td align='center' bgcolor='#c4d9ff' width=80>公共服務</td><td>※校內: $nor_memo[2]<br>※社區: $nor_memo[3]</td></tr>
						<tr><td align='center' bgcolor='#c4d9ff' width=80>特殊表現</td><td>※校內: $nor_memo[4] <br>※校外: $nor_memo[5]</td></tr>
						</table>";
		
		$smarty->assign("chk_data",$chk_data);  
		     
    } // end if $stud_chk_data 
    
    
    

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
	
	 
	  //社團活動=================================================================
	  if ($IS_JHORES==6 and $stud_club=='checked') {
	   $query="select association_name,score,description,club_sn,stud_feedback from association where seme_year_seme='$seme_year_seme' and student_sn=$student_sn";
	   $res=mysql_query($query);
	   $club_detail="";
	   $j=0;
	    
	   while ($row=mysql_fetch_array($res)) {
	   	//校內社團, 要檢查分數, 外校社團則一律通過
	   		$query="select pass_score from stud_club_base where club_sn='".$row['club_sn']."'";
	   		$res_pass=mysql_query($query);
	   		list($pass_score)=mysql_fetch_row($res_pass);
	     
	   	if (($row['score']>=$pass_score and $row['club_sn']>0) or ($row['club_sn']==0) ) {
	   	  $j++;
	      $club_detail[$j]['association_name']=$row['association_name'];
	      $club_detail[$j]['score']=score2str($row['score'],$class);
	      $club_detail[$j]['description']=$row['description'];
	      $club_detail[$j]['stud_feedback']=$row['stud_feedback'];
	    }
	   }
	  		  	
	   $smarty->assign("club_detail",$club_detail);
	   
	  }
    //============================================================================================================================

		$smarty->assign("stud",$stud);
		$smarty->assign("default_txt",$default_txt);
		
		$smarty->display("stud_club_serv_p.tpl");

		$page_i++;
    
	} // end foreach stud
	 
 } // end foreach class

} // end if post


//傳回服務單位 ==================================================================================================================
function getPostRoom($room_id) {
  global $CONN;
  $sql_select = "select room_name from school_room where room_id='$room_id'";
  $result=$CONN->Execute($sql_select);
  $room_name=$result->fields['room_name'];	
  return $room_name;
}

//取得某一學生某月的各種缺曠課累積次數
function getOneAbsent($stud_id,$sel_year,$sel_seme,$mode=""){
	global $CONN,$absent_kind_array;
	foreach($absent_kind_array as $abkind){
	 $theData[$abkind]=0;
	}
	$theData[f]=0;
	
	$sql_select="select section, absent_kind from stud_absent where stud_id='$stud_id' and year='$sel_year' and semester='$sel_seme'";
	//echo $sql_select;
	//exit();
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	
	while($row=$recordSet->fetchRow()){
		list($section,$kind)=$row;
		//echo $section.",".$kind."<br>";
		if($mode=="種類"){
			$n=($section=="allday")?7:1;
			$m=($section=="allday")?2:1;
			if ($kind=="曠課" && ($section=="uf" || $section=="df")) $theData[f]+=$m;
			if ($section!="uf" && $section!="df") $theData[$kind]+=$n;
		}else{
			$theData[$section]+=1;	
		}		
	}
  //print_r($theData);
  
	return $theData;
}


//讀取競賽記錄 二維陣列 傳入條件, 限學期, 學生
function get_race_record($st_date,$end_date,$student_sn) {
	
 global $CONN;
 
 $students=array();

 $query="select * from `career_race` where certificate_date>='$st_date' and certificate_date<='$end_date' and student_sn='$student_sn' order by certificate_date";
 $res=$CONN->Execute($query) or die("SQL錯誤:$query");;
 while ($row=$res->FetchRow()) {			//讀取一筆, 並放入陣列 $row 中 

   $student_sn=$row['student_sn'];
   $sn=$row['sn'];
   
   //讀入競賽資料
   foreach($row as $k=>$v) {
     $students[$sn][$k]=$v;
   }
   	   
 } // end while
 
 return $students;

} // end function 

//缺曠課明細
//單一學生的缺況課明細
function &stud_absent_statForm($sel_year,$sel_seme,$class_id,$stud_id,$start_date,$end_date){
	global $CONN,$IS_JHORES;
	//取得某班節數
	$all_sections=get_class_cn($class_id);
	
	for($i=1;$i<=$all_sections;$i++){		
			$sections_txt.="<td>".$i."</td>";		
	}

	$sql="select date,absent_kind,section from stud_absent where (date>='$start_date') and (date<='$end_date') and stud_id='$stud_id' order by date,section";
	$rs=$CONN->Execute($sql);
	$aaa="";
	$data="";
	$total=array();
	$lis=0;
	while(!$rs->EOF){
		$the_date=$rs->fields['date'];
		$absent_kind=$rs->fields['absent_kind'];
		$section=$rs->fields['section'];
		if ($the_date != $pre_date) {
			if ($have_data) {
				$data.=show_data($pre_date,$aaa,$all_sections);
				$aaa="";
			}
			$pre_date=$the_date;
			$have_data=1;
			if ($lis!=0 && ($lis%5)==0 ) $data.="<tr><td colspan=".($all_sections+11)." align='center'><hr size='1'></tr>";
			$lis++;
		}
		$aaa[$section]=$absent_kind;
		$total[$absent_kind][$section]++;
		$total[sum][$section]++;
		$rs->MoveNext();
	}
	if ($lis>0) { $data.=show_data($the_date,$aaa,$all_sections); } else {$data="<td colspan=".($all_sections+11)." align='center'><font size='2'><i>無登載任何缺曠課記錄</i></font></td>";}

	//取得缺曠課類別
	$absent_kind_array= SFS_TEXT("缺曠課類別");
	$sum_data="";
	for ($i=0;$i<count($absent_kind_array);$i++) {
		$section_data="";
		$kind=$absent_kind_array[$i];
		for($j=1;$j<=$all_sections;$j++){
			$k=($IS_JHORES!=0)?$total[$kind][$j]+$total[$kind][allday]:$total[$kind][$j];
			if ($k==0) $k="";
			$section_data.="<td bgcolor='#FFFFFF'>".$k."</td>";
			$ttotal[$kind]+=$total[$kind][$j];
		}
		$ttotal[$kind]+=($IS_JHORES==0)?$total[$kind][allday]:$total[$kind][allday]*$all_sections;
		$sum_data.="<td>".$ttotal[$kind]."</td>";
	}
	if ($IS_JHORES!=0) {
		$section_data="";
		for($j=1;$j<=$all_sections;$j++){
			$section_data.="<td bgcolor='#FFFFFF'></td>";
		}
		$ufs=$total[曠課][uf]+$total[曠課][allday];
		$dfs=$total[曠課][df]+$total[曠課][allday];
		$sum_data="$sum_data<td>".($ufs+$dfs)."</td>";
	}
	
		$query="select * from school_base";
		$res=$CONN->Execute($query);
		$school_name=$res->fields[sch_cname];
		$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
		$query="select a.stud_name,b.seme_num from stud_base a,stud_seme b where b.seme_year_seme='$seme_year_seme' and a.student_sn=b.student_sn and a.stud_id='$stud_id'";
		$res=$CONN->Execute($query);
		$stud_name=$res->fields[stud_name];
		$seme_num=$res->fields[seme_num];
		$query="select * from school_class where class_id='$class_id'";
		$res=$CONN->Execute($query);
		$c_name=$res->fields[c_name];
		$c_year=$res->fields[c_year];
		$today=date("Y-m-d");	
	
	
	//取得缺曠課類別
	$absent_kind_array= SFS_TEXT("缺曠課類別");
	
	$main="
	<center><small>統計時間：".$start_date."～".$end_date."</small><br>
	<table cellspacing='1' cellpadding='3' class='small' width='100%'>
	<tr align='center'>
	<td>缺席日期</td>		
	<td>星期</td>		
	<td>升</td>
	$sections_txt
	<td>降</td><td>曠</td><td>事</td><td>病</td><td>喪</td><td>公</td><td>不</td><td>旗</td>
	</tr>
	<tr>
	<td colspan=".($all_sections+11)." align='center'><hr size='2'></td>
	</tr>
	$data
	<tr>
	<td colspan=".($all_sections+11)." align='center'><hr size='2'></td>
	</tr>
	<tr align='center'>
	<td>累計</td><td colspan=".($all_sections+3)."></td>$sum_data
	</tr>
	</table></center>
	";
	return $main;
}

function show_data($the_date,$a,$all_sections) {
	global $IS_JHORES,$class_name_kind_1;
	//各一節資料
	$w=explode("-",$the_date);
	$ww=date("w", mktime (0,0,0,$w[1],$w[2],$w[0]));
	$section_data="";
	$k="";
	$ak=array("曠課"=>0,"事假"=>0,"病假"=>0,"喪假"=>0,"公假"=>0,"不可抗力"=>0,"旗"=>0);
	if ($IS_JHORES!=0 && !empty($a[allday])) {
		$k=$a[allday];
		$a[uf]=$k;
		$a[df]=$k;
	}
	for($j=1;$j<=$all_sections;$j++){
		if ($k) $a[$j]=$k;
			$section_data.="<td>".substr($a[$j],0,2)."</td>";
			if ($a[$j]) $ak[$a[$j]]++;
		
	}
	$data="
		<tr align='center'>
		<td>$the_date</td>
		<td>".$class_name_kind_1[$ww]."
		<td>".substr($a[uf],0,2)."</td>
		$section_data
		<td>".substr($a[df],0,2)."</td>
		";
	
		if ($a[uf]=="曠課") $ak["旗"]++;
		if ($a[df]=="曠課") $ak["旗"]++;
		while (list($x,$y)=each($ak)) {
			$data.="<td>".intval($y)."</td>";
		}
		$data.="</tr>";
	
	return $data;
}

//取得該班課程節數
function get_class_cn($class_id=""){
	global $CONN;
	//取得某班學生陣列
	$c=class_id_2_old($class_id);
	
	//取得該班有幾節課
	$sql_select = "select sections from score_setup where year = '$c[0]' and semester='$c[1]' and class_year='$c[3]'";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("SQL語法錯誤： $sql_select", E_USER_ERROR);
	list($all_sections) = $recordSet->FetchRow();
	return $all_sections;
}

?>
