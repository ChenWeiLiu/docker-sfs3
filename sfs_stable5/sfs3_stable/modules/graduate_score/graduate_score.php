<?php
// $Id: graduate_score.php 8051 2014-05-29 00:40:22Z kwcmath $

include "config.php";
include "../../include/sfs_case_score.php";

//認證
sfs_check();

$ss_link=array("語文"=>"language","數學"=>"math","生活"=>"life","自然與生活科技"=>"nature","社會"=>"social","藝術與人文"=>"art","健康與體育"=>"health","綜合活動"=>"complex","日常生活表現"=>"nor");
$link_ss=array("language"=>"語文","math"=>"數學","life"=>"生活","nature"=>"自然與生活科技","social"=>"社會","art"=>"藝術與人文","health"=>"健康與體育","complex"=>"綜合活動","nor"=>"日常生活表現");

$specific=array();

if ($IS_JHORES==0) $f_seme=12; else $f_seme=6;

if($_POST["step"]=="" OR $_POST[stud_class]==""){
	head("畢業生成績試算");
	
echo <<<HERE
<script>
function tagall(status) {
  var i =0;
  while (i<document.area.elements.length)  {
    if(document.area.elements[i].id!='') {
		var text=document.area.elements[i].id;
		if(text.indexOf('area_')==0) document.area.elements[i].disabled=status;
    }
    i++;
  }
}
</script>
HERE;
	
	//取得教師所上年級、班級  此處要改為先檢視有沒有管理權設定
	$session_tea_sn = $_SESSION['session_tea_sn'];
	$query =" select class_num  from teacher_post  where teacher_sn  ='$session_tea_sn'  ";
	$result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;
	$row = $result->FetchRow() ;
	$class_num = $row["class_num"];

	$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'];
	if($class_num<>"" OR checkid($SCRIPT_FILENAME,1)) { 
	
	
	//產生要計算的領域選項
	$area="<form name=\"area\" method=\"post\" action=\"$_SERVER[PHP_SELF]\"><input type='hidden' name='step' value='@'>
		<table border=1 cellpadding=10 cellspacing=0 style='border-collapse: collapse' bordercolor='#111111' width='100%'>
		<tr><td bgcolor='#CFCFAA'  width=250>步驟</td><td width=300 bgcolor='#CFCFAA'>參數設定</td><td rowspan=5  bgcolor='#AACFCF'>
		<pre><font color=#2288FF><img src='./images/edit.gif'> 模組說明:<a href='mailto:infodaes@seed.net.tw'> (by infodaes  2005/05/13)</a>
		
    本模組係自[註冊組]-[畢業生升學資料]
的[畢業成績]修改而來，因考量導師使用的
便利性，故將其獨立出來。
    本模組設計的目的，係配合學校畢業時，
各班級往往會有採行彈性的學習領域或學期的
加權方式來計算班級學生某些特殊需要排定成
績次序的成績，以方便作為給定獎學金或畢業
相關獎項的參考。

※參數設定:請至[模組權限管理]設定模組變數
※管理權設定:非班級導師使用須有管理權
※2011-09-05 增加領域加權使用原來課程設定選項

※使用步驟:
  1.選定要計算的[班級]
  2.設定[領域加權]
  3.設定學期加權
  4.設定名次列序人數:
  5.按[按此計算後列示]顯示結果
	</font></pre><center><input type='submit' value='按此計算後列示' name='go' style='font-family: 標楷體; font-size: 14 pt' onClick='return checkok();'></center></td>";

	
	//目標班級
	if($class_num) $stud_class=sprintf("%03d_%d_%02d_%02d",curr_year(),curr_seme(),substr($class_num,0,1),substr($class_num,-2));
	$class_list=get_class_select(curr_year(),curr_seme(),"","stud_class","",$stud_class);
	if( !checkid($SCRIPT_FILENAME,1) AND $class_num) {
		$class_list="<input type='hidden' name='stud_class' value='$stud_class'>".str_replace("'stud_class'","'stud_class_xxx' disabled",$class_list);
	}
	if(checkid($SCRIPT_FILENAME,1)) $slect_all_grade="　<input type='checkbox' name='all_classes'>該年級一併計算"; else $slect_all_grade="";
	$area.="</tr><tr bgcolor='#CCAAFF'><td><img src='./images/icon.gif'>Step 1: 選定要計算的班級:</td><td>$class_list $slect_all_grade</td></tr>
		<tr bgcolor='#CCFFAA'><td><img src='./images/icon.gif'>Step 2: 領域的加權比重:<br><br><font color='blue'><center><input type='checkbox' name='original_rate' checked onclick='tagall(this.checked);'>使用原課程設定加權</center></td><td>";

	//print_r($semesters);
	$range_select=$range_select?$range_select:50;
	
	foreach($ss_link as $key=>$value) {	
		$area.="$key:<select name='$value' id='area_$value' disabled>";
		for($i=0;$i<=$range_select;$i++) { $area.="<option".(($m_arr[$value]==$i)?" selected":"").">$i</option>";}
		$area.="</select><BR>";
	}

	$weight="";
	for($i=1;$i<=$f_seme;$i++) {
		$weight.=floor(($i+1)/2).(($i % 2)?"上":"下").":<select name='$i'>"; 
		for($k=0;$k<=$range_select;$k++)  $weight.="<option".(($semesters[$i-1]==$k)?" selected":"").">$k</option>";
		$weight.="</select>".(($i % 2)?"　　":"<BR>");
	}
	
	$rank_count="<input type='text'  size=5 name='rank_count' value='".$m_arr['rank_count']."'>　　　<input type='checkbox' name='show_detail'>顯示各生各領域成績";
	$area.="<tr bgcolor='#FFCCAA'><td><img src='./images/icon.gif'>Step 3:各學期的加權比重:<BR></td><td>$weight</td></tr>";
	$area.="<tr bgcolor='#FFACCA'><td><img src='./images/icon.gif'>Step 4:名次列序人數:<BR></td><td>$rank_count</td></tr>";
	$area.="</table></form>";
	echo $area;
	} else { echo "<h2><center><BR><BR><font color=#FF0000>您並未被授權使用此模組(非導師或模組管理員)</font></center></h2>"; } 
	
	foot();

} ELSE {	
	set_time_limit(0);  //避免班級數過多而產生timeout問題	
	
	//取得領域加權數至 $specific 陣列
	foreach($ss_link as $key=>$value){
		$specific[$value]=$_POST[$value];
		$total_rate+=$specific[$value];
	}
	//取得處理的班級
	$stud_class=($_POST[stud_class]);
	$class_data=explode('_',$stud_class);
        $class_id=$class_data[2]*100+$class_data[3];
        
        $SQL_filter=$_POST[all_classes]?(" like '".substr($class_id,0,1)."%'"):"='$class_id'";
        $show_detail=$_POST[show_detail];
        
	//print_r($specific);  print_r($stud_class);
	//echo "<BR>領域總加權數:$total_rate<BR>";
	
	//取得學期比重
	for($i=0;$i<$f_seme;$i++) $seme_weight[$i]=$_POST[$i+1];
	
	//echo "<BR>###############";
	//print_r($seme_weight);
	
	//設定只能計算本學年度
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."_".$sel_seme;


	if ($_POST["step"]=="@") {
		$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
		//$seme_class=$_POST[year_name].sprintf("%02d",$_POST[me]);
		//$query="select a.*,b.stud_name from stud_seme a,stud_base b where a.student_sn=b.student_sn AND a.seme_year_seme='$seme_year_seme' and a.seme_class='$class_id' and b.stud_study_cond in ('0','15') order by a.seme_num";
		$query="select a.*,b.stud_name,b.curr_class_num from stud_seme a,stud_base b where a.student_sn=b.student_sn AND a.seme_year_seme='$seme_year_seme' and a.seme_class $SQL_filter and b.stud_study_cond=0 order by b.curr_class_num";
		
//echo $query."<BR>";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$sn[]=$res->fields[student_sn];
			$curr_class_num[]=$_POST[all_classes]?$res->fields[curr_class_num]:$res->fields[seme_num];
			$stud_name[]=$res->fields[stud_name];
			$stud_id[]=$res->fields[stud_id];
			$res->MoveNext();
		}
		
		//print_r($sn);
		//print_r($stud_name);
		//print_r($stud_id);
		
		//exit;
	
		$rank_count=$_POST['rank_count'];
				
		$query="select stud_study_year from stud_base where student_sn='".pos($sn)."'";  //取得入學年
		$res=$CONN->Execute($query);
		$stud_study_year=$res->fields[0];
		if ($IS_JHORES==0)
			$f_year=5;
		else
			$f_year=2;
			for ($i=0;$i<=$f_year;$i++) {
				for ($j=1;$j<=2;$j++) {
					$semes[]=sprintf("%03d",$stud_study_year+$i).$j;
					$show_year[]=$stud_study_year+$i;
					$show_seme[]=$j;
				}
		}
		$fin_score=cal_fin_score($sn,$semes,"",array($sel_year,$sel_seme,$_POST[year_name]));
		$fin_nor_score=cal_fin_nor_score($sn,$semes);

		$sm=&get_all_setup("",$sel_year,$sel_seme,$_POST[year_name]);
		$rule=explode("\n",$sm[rule]);
		while(list($s,$v)=each($fin_score)) {
			$fin_score[$s][avg][str]=score2str($fin_score[$s][avg][score],"",$rule);
		}


		$final_score=array();
	
		
		//領域成績加入日常生活表現成績(視為領域之一)
		foreach($fin_nor_score as $student_sn=>$nor_score)
		{
				$fin_score[$student_sn]['nor']=$fin_nor_score[$student_sn];
		}
	
		//修正原計算的加權
		if($_POST[original_rate]) {	  //只計算學期不計算領域
			for($i=0;$i<=count($sn)+1;$i++){
				$final_score[$sn[$i]]=0;
				foreach($specific as $key=>$value) {
					$fin_score[$sn[$i]][$key]['avg']['score']=0;
					$fin_score[$sn[$i]][$key]['avg']['rate']=0;
					//計算學期比例
					for($j=0;$j<=count($semes);$j++) {
						$fin_score[$sn[$i]][$key][$semes[$j]][rate]=$fin_score[$sn[$i]][$key][$semes[$j]][rate]*$seme_weight[$j];
						$fin_score[$sn[$i]][$key]['avg']['score']+=$fin_score[$sn[$i]][$key][$semes[$j]][score]*$fin_score[$sn[$i]][$key][$semes[$j]][rate];
						$fin_score[$sn[$i]][$key]['avg']['rate']+=$fin_score[$sn[$i]][$key][$semes[$j]][rate];
					}
					$final_score[$sn[$i]]+=$fin_score[$sn[$i]][$key]['avg']['score'];
				}	
			}
		} else {   //依設定計算
			for($i=0;$i<=count($sn)+1;$i++){
				$fin_score[$sn[$i]]['avg']['score']=0;
				foreach($specific as $key=>$value) {
					$fin_score[$sn[$i]][$key]['avg']['score']=0;
					//計算學期比例
					for($j=0;$j<=count($semes);$j++) {
						$fin_score[$sn[$i]][$key]['avg']['score']+=$fin_score[$sn[$i]][$key][$semes[$j]][score]*$seme_weight[$j];
					}
					//計算領域比例
					$fin_score[$sn[$i]]['avg']['score']+=$fin_score[$sn[$i]][$key]['avg']['score']*$value;
				}
				$final_score[$sn[$i]]=$fin_score[$sn[$i]]['avg']['score'];
			}
		}
		//針對最後結果做排序
		arsort($final_score);
	
		//寫入名次
		$rank=0;
		$rank_list="<table border=1 cellpadding=0 cellspacing=0 bordercolor=#5555AA style='border-collapse: collapse' width=100%><tr bgcolor=#FFCAAC><td align='center'>排序</td><td align='center'>班級座號</td><td align='center'>姓名</td><td align='center'>加權總分</td><td align='center'>備　　　　註</td></tr>";
		foreach($final_score as $key=>$value) {
		if($key){
			$rank+=1;
			$fin_score[$key]['avg']['rank']=$rank;
			if($rank<=$rank_count AND $rank<=count($sn)) {
			$rank_list.="<tr><td align='center'>$rank</td><td align='center'>".$curr_class_num[array_search($key,$sn)]."</td><td align='center'>".$stud_name[array_search($key,$sn)]."</td><td align='center'>".$final_score[$key]."</td><td></td></tr>"; }
		}
		}
		$rank_list.="</table>";
	}
	
	//顯示部分
	$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
	$smarty->assign("module_name","畢業成績試算"); 
	$smarty->assign("SFS_MENU",$menu_p); 
	$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme)); 
	$smarty->assign("class_year_menu",class_year_menu($sel_year,$sel_seme,$_POST[year_name])); 
	$smarty->assign("class_name_menu",class_name_menu($sel_year,$sel_seme,$_POST[year_name],$_POST[me])); 
	$smarty->assign("show_year",$show_year);
	$smarty->assign("show_seme",$show_seme);
	$smarty->assign("semes",$semes);
	$smarty->assign("stud_id",$stud_id);
	$smarty->assign("student_sn",$sn);
	$smarty->assign("curr_class_num",$curr_class_num);
	$smarty->assign("stud_name",$stud_name);
	$smarty->assign("stud_num",count($stud_id));
	$smarty->assign("fin_score",$fin_score);
	$smarty->assign("final_score",$final_score);
	$smarty->assign("fin_nor_score",$fin_nor_score);
	$smarty->assign("ss_link",$ss_link);
	$smarty->assign("link_ss",$link_ss);
	$smarty->assign("ss_num",count($ss_link));
	$smarty->assign("rule",$rule_all);
	$smarty->assign("jos",$IS_JHORES);
	$smarty->assign("class_base",class_base($seme_year_seme));
	$smarty->assign("seme_class",$_POST[year_name].sprintf("%02d",$_POST[me]));
	$smarty->assign("seme_weight",$seme_weight);
	$smarty->assign("specific",$specific);
	$smarty->assign("rank_list",$rank_list);
	$smarty->assign("show_detail",$show_detail);
	
	
	//if ($_POST[friendly_print]) {
	//	$smarty->display("stud_grad_grad_score_print.tpl");
	//} else {
		$smarty->display("graduate_score.tpl");
	//}
	
}
?>
