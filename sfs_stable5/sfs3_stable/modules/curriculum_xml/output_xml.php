<?php
// $Id: output_xml.php 6036 2010-08-26 05:39:46Z infodaes $

require "config.php";

sfs_check();

//如果確定輸出XML檔案
if ($_POST[act]) {
	//如果按下彰化縣匯出(20150112學籍小組於線西國中修正)
	if($_POST['act']=='彰縣匯出XML'){
	$_POST['order'] = 1 ;
	$_POST['cert'] = 1 ;
	$_POST['mask'] = array();
	$_POST['name'] = 3 ;
	$_POST['stylesheet'] = 2 ;
	
	}//(20150112學籍小組於線西國中修正)
	
	$out_arr=array();
	//設定參照陣列
	$semester_arr=array(1=>'上學期',2=>'下學期');
	$class_year_arr=array(0=>'不分年級',1=>'一年級',2=>'二年級',3=>'三年級',4=>'四年級',5=>'五年級',6=>'六年級',7=>'七年級',8=>'八年級',9=>'九年級',10=>'十年級',11=>'十一年級',12=>'十二年級');
	$dow_arr=array(1=>'週一',2=>'週二',3=>'週三',4=>'週四',5=>'週五',6=>'週六',7=>'週日');
	$sector_arr=array(1=>'第一節',2=>'第二節',3=>'第三節',4=>'第四節',5=>'第五節',6=>'第六節',7=>'第七節',8=>'第八節',9=>'第九節',10=>'第十節',11=>'第十一節',12=>'第十二節',13=>'第十三節',14=>'第十四節');
	
	//抓取科目名稱
	$subject_arr=array();
	$sql="SELECT subject_id,subject_name FROM score_subject WHERE enable=1";
	$res=$CONN->Execute($sql) or user_error("讀取課表設定資料失敗！<br>$sql",256);
	while(!$res->EOF){
		$subject_id=$res->fields['subject_id'];
		$subject_arr[$subject_id]=$res->fields['subject_name'];
		$res->MoveNext();
	}	
//echo "<pre>";
//print_r($subject_arr);	
//echo "</pre>";	
	//抓取課程資料
	$ss_arr=array();
	$sql_ss="SELECT scope_id,ss_id,subject_id,link_ss FROM score_ss WHERE enable=1 ORDER BY year,semester,class_id";
	$res_ss=$CONN->Execute($sql_ss) or user_error("讀取課表設定資料失敗！<br>$sql_ss",256);
	while(!$res_ss->EOF){
		$ss_id=$res_ss->fields['ss_id'];
		$scope_id=$res_ss->fields['scope_id'];
		$subject_id=$res_ss->fields['subject_id'];
		$pos=strpos('>>'.$subject_arr[$scope_id],'彈性');
		$ss_arr[$ss_id]['category']=$pos?'彈性學習節數':'領域學習節數';
		$pos=strpos('>>'.$res_ss->fields['link_ss'],'語文');
		//將 語文-本國語文 語文-英語 語文-鄉土語文  通通改為  語文
		$ss_arr[$ss_id]['learningareas']=$pos?'語文':$res_ss->fields['link_ss'];
		$ss_arr[$ss_id]['subject']=$subject_arr[$subject_id]?$subject_arr[$subject_id]:$subject_arr[$scope_id];
//echo $ss_id.'-->'.$ss_arr[$ss_id]['learningareas'].'-->'.$ss_arr[$ss_id]['subject'].'---->'.$pos.'<br>';		
		$res_ss->MoveNext();
	}
	
	//抓取課表資料進行陣列儲存	
	foreach($_POST[year_seme] as $key=>$year_seme){
		$tmp=explode('_',$year_seme);
		$this_year=$tmp[0];
		$this_semester=$tmp[1];
		
		//抓取班級名稱(school_class)		
		$class_name_arr=array();
		$sql_class="SELECT class_id,c_name FROM school_class WHERE enable=1 AND year=$this_year AND semester=$this_semester ORDER BY class_id";
		$res_class=$CONN->Execute($sql_class) or user_error("讀取課表設定資料失敗！<br>$sql_class",256);
		while(!$res_class->EOF){
			$class_id=$res_class->fields['class_id'];
			$class_name_arr[$class_id]=$res_class->fields['c_name'];
			$res_class->MoveNext();
		}
		
		
		$out_arr[$year_seme]['year']=$this_year;
		$out_arr[$year_seme]['semester']=iconv("Big5","UTF-8",$semester_arr[$this_semester]);
		
		$sql="SELECT * FROM score_course WHERE year=$this_year AND semester=$this_semester ORDER BY ";
		$sql.=$_POST['order']?'class_id,day,sector':'teacher_sn,day,sector';
		$res=$CONN->Execute($sql) or user_error("讀取課表設定資料失敗！<br>$sql",256);
		while(!$res->EOF){
			$teacher_sn=$res->fields['teacher_sn'];
			$course_id=$res->fields['course_id'];
			//尚未有資料才進行教師資料擷取
			if(!$out_arr[$year_seme]['teacherdata'][$teacher_sn]['name']){
				$sql_teacher="SELECT * FROM teacher_base WHERE teacher_sn=$teacher_sn";
				$res_teacher=$CONN->Execute($sql_teacher) or user_error("讀取課表設定資料失敗！<br>$sql_teacher",256);
				$teach_person_id=$res_teacher->fields['teach_person_id'];
				$target_id='';
				for($i=0;$i<10;$i++){
					if($_POST[mask][$i]) $char='*'; else $char=substr($teach_person_id,$i,1);
					$target_id.=$char;
				}
				$out_arr[$year_seme]['teacherdata'][$teacher_sn]['teach_person_id']=$target_id;	
				$name=$res_teacher->fields['name'];
				switch($_POST['name']){
					case 0: $name=''; break;
					case 1: $name='○○○'; break;
					case 2: $name=substr($name,0,2).'○○'; break;
				}
				
				$out_arr[$year_seme]['teacherdata'][$teacher_sn]['name']=iconv("Big5","UTF-8",$name);
				
				//抓取教師證資料(SFS3為單一一筆資料，新資料庫應該要為多筆)
				$out_arr[$year_seme]['teacherdata'][$teacher_sn]['certificates'][$key2]['certdate']=iconv("Big5","UTF-8",$res_teacher->fields['certdate']);
				$out_arr[$year_seme]['teacherdata'][$teacher_sn]['certificates'][$key2]['certgroup']=iconv("Big5","UTF-8",$res_teacher->fields['certgroup']);
				$out_arr[$year_seme]['teacherdata'][$teacher_sn]['certificates'][$key2]['certarea']=iconv("Big5","UTF-8",$res_teacher->fields['certarea']);
				$out_arr[$year_seme]['teacherdata'][$teacher_sn]['certificates'][$key2]['certsujbect']=iconv("Big5","UTF-8",$res_teacher->fields['teach_sub_kind']);
				$out_arr[$year_seme]['teacherdata'][$teacher_sn]['certificates'][$key2]['certnumber']=iconv("Big5","UTF-8",$res_teacher->fields['teach_check_word']);
				
				//分解任教領域與科目
				$domain_subject_arr=explode(';',$res_teacher->fields['master_subjects']); 
				foreach($domain_subject_arr as $key2=>$subject_data){
					$subject_data_arr=explode('_',$subject_data);
					$out_arr[$year_seme]['teacherdata'][$teacher_sn]['teachersubjects'][$key2]['domain']=iconv("Big5","UTF-8",$subject_data_arr[0]);
					$out_arr[$year_seme]['teacherdata'][$teacher_sn]['teachersubjects'][$key2]['expertise']=iconv("Big5","UTF-8",$subject_data_arr[1]);				
				}				
			}
			//抓取課表資料
			$out_arr[$year_seme]['curriculums'][$course_id]['teacheridnumber']=$out_arr[$year_seme]['teacherdata'][$teacher_sn]['teach_person_id'];
			$class_year=$res->fields['class_year'];
			$out_arr[$year_seme]['curriculums'][$course_id]['classyear']=iconv("Big5","UTF-8",$class_year_arr[$class_year]);
			$class_id=$res->fields['class_id'];
			
			//如果按下彰化縣匯出(20150112學籍小組於線西國中修正)
			if($_POST['act']=='彰縣匯出XML'){
				$class_num = explode("_",$class_id);//改一班.甲班.忠班為01班
				$out_arr[$year_seme]['curriculums'][$course_id]['classname'] = iconv("Big5","UTF-8",$class_num[3].'班');
			}else{
				$out_arr[$year_seme]['curriculums'][$course_id]['classname']=iconv("Big5","UTF-8",$class_name_arr[$class_id].'班');
			}
			//如果按下彰化縣匯出(20150112學籍小組於線西國中修正)
			
			$dow=$res->fields['day'];
			$out_arr[$year_seme]['curriculums'][$course_id]['week']=iconv("Big5","UTF-8",$dow_arr[$dow]);
			$sector=$res->fields['sector'];
			$out_arr[$year_seme]['curriculums'][$course_id]['classtime']=iconv("Big5","UTF-8",$sector_arr[$sector]);

			$ss_id=$res->fields['ss_id'];
			$out_arr[$year_seme]['curriculums'][$course_id]['category']=iconv("Big5","UTF-8",$ss_arr[$ss_id]['category']);
			$out_arr[$year_seme]['curriculums'][$course_id]['learningareas']=iconv("Big5","UTF-8",$ss_arr[$ss_id]['learningareas']);
			$out_arr[$year_seme]['curriculums'][$course_id]['subject']=iconv("Big5","UTF-8",$ss_arr[$ss_id]['subject']);
			
			$res->MoveNext();
		}
	}
	
	//開始產生BIG5版XML
	
	//抓取學校資料
	$sql='SELECT sch_sheng,sch_cname,sch_id FROM school_base';
	$res=$CONN->Execute($sql) or user_error("讀取學校基本資料失敗！<br>$sql",256);
	$cityname=iconv("Big5","UTF-8",$res->fields['sch_sheng']);
	$schoolname=iconv("Big5","UTF-8",$res->fields['sch_cname']);
	$schoolid=iconv("Big5","UTF-8",$res->fields['sch_id']);
	
	$smarty->assign("cityname",$cityname);
	$smarty->assign("schoolname",$schoolname);
	$smarty->assign("schoolid",$schoolid);
	
	$smarty->assign("cert",$_POST['cert']);
	$smarty->assign("out_arr",$out_arr);
	
	switch($_POST['stylesheet']){
		case 0: $stylesheet=''; break;
		case 1: $stylesheet='<?xml-stylesheet type="text/xsl" href="./excoursetransform.xsl"?>'; break;
		case 2: $stylesheet='<?xml-stylesheet type="text/xsl" href="'.$SFS_PATH_HTML.get_store_path().'/excoursetransform.xsl"?>'; break;
	}
	$smarty->assign("stylesheet",$stylesheet);
				

	//將smarty輸出的資料先cache住
	ob_start();
	$smarty->display("curriculum_1_0.tpl");
	$xmls=ob_get_contents();
	ob_end_clean();
	
	$filename=$SCHOOL_BASE['sch_id'].$school_long_name.date('Ymd')."班級與教師課表XML交換資料.xml";
	header("Content-disposition: attachment; filename=$filename");
	header("Content-Type:text/xml; charset=utf-8");

	echo $xmls;
	exit;
}


head('中教司班級與教師課表交換XML匯出');
print_menu($menu_p);
echo <<<HERE
<script>
function tagall(status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name=='year_seme[]') {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}

function check_select() {
  var i=0; j=0; answer=true;
  while (i < document.myform.elements.length)  {
    if(document.myform.elements[i].name=='year_seme[]') {
		if(document.myform.elements[i].checked) j++;
    }
    i++;
  }
  
  if(j==0) { alert("尚未選取任何學期！"); answer=false; }
  
  return answer;
}

</script>
HERE;

//抓取有課表學期，提供選單之用
$sql="SELECT distinct year,semester FROM score_course ORDER BY year desc,semester desc";
$res=$CONN->Execute($sql) or user_error("讀取課表設定資料失敗！<br>$sql",256);

$main.="<form name='myform' method='post'>
		<table border=2 cellpadding=10 cellspacing=0 style='border-collapse: collapse; font-size=12pt;' bordercolor='#ffcfcf'>
		<tr align='center' bgcolor='#ffffaa'><td><input type='checkbox' name='tag_all' onClick='javascript:tagall(this.checked);'>選擇學期</td><td>輸出選項</td></tr><tr><td>";
while(!$res->EOF) {
	if(curr_year()-$res->fields[year]<$years) {
		$year_seme=$res->fields[year].'_'.$res->fields[semester];
		$year_seme_name=$res->fields[year].'學年度第'.$res->fields[semester].'學期';
		$this_yeae_seme=curr_year().'_'.curr_seme();
		$checked=$this_yeae_seme==$year_seme?'checked':''; 
		$main.="<input type='checkbox' name='year_seme[]' value='$year_seme' $checked>$year_seme_name<br>";
	}
	$res->MoveNext();
}

$id_mask_list='';
for($i=0;$i<10;$i++){
	$show=$i?$i:'字母';
	$mask_char=substr($masks,$i,1);
	$checked=($mask_char=='*')?'checked':'';
	$id_mask_list.="<input type='checkbox' name='mask[$i]' value='$show' $checked>$show ";
}
//

$main.="</td><td valign='top'>
<br>◎課表資料排序方式：<input type='radio' name='order' value=0 checked>班級節次 <input type='radio' name='order' value=1>教師節次 
<br><br>◎教師證資料輸出：<input type='radio' name='cert' value=1 checked>不輸出 <input type='radio' name='cert' value=2>只輸出日期與證號 <input type='radio' name='cert' value=3>輸出詳細資料
<br><br>◎身分證統一編號遮罩：$id_mask_list
<br><br>◎教師姓名輸出：<input type='radio' name='name' value=0 checked>空白 <input type='radio' name='name' value=1>○○○ <input type='radio' name='name' value=2>輸出首字 (陳○○) <input type='radio' name='name' value=3>輸出全名 (陳大中)
<br><br>◎檢視樣式參照：<input type='radio' name='stylesheet' value=0>無 <input type='radio' name='stylesheet' value=1 checked>相對路徑(./excoursetransform.xsl) <input type='radio' name='stylesheet' value=2>學校SFS3樣式檔的參照絕對路徑
</td></tr>
<tr><td colspan=2>
<input type='submit' style='border-width:1px; cursor:hand; color:white; background:#ff5555; font-size:20px; width=100%; height=80' value='匯出XML' name='act' onclick='return check_select();'>
<!---(20150112學籍小組於線西國中修正)--->
<input type='submit' style='border-width:1px; cursor:hand; color:white; background:#ff5555; font-size:20px; width=100%; height=80' value='彰縣匯出XML' name='act' onclick='return check_select();'>
<!---(20150112學籍小組於線西國中修正)--->
</td></tr></table></form>";

echo $main;

foot();


?>