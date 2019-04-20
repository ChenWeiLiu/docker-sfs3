<?php
// $Id: output_xml.php 6036 2010-08-26 05:39:46Z infodaes $

require "config.php";

sfs_check();


//如果確定輸出XML檔案
if ($_POST[act]) {
	$out_arr=array();

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
//exit;
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
		//將"生活"改為"生活課程"
		if($ss_arr[$ss_id]['learningareas']=='生活') $ss_arr[$ss_id]['learningareas']='生活課程';
		//將"彈性學習"改為"彈性課程"
		if($ss_arr[$ss_id]['learningareas']=='彈性學習') $ss_arr[$ss_id]['learningareas']='彈性課程';
                //未定義九年一貫對應則改為彈性課程
		if($ss_arr[$ss_id]['learningareas']=='') $ss_arr[$ss_id]['learningareas']='彈性課程';
		$res_ss->MoveNext();
	}
	
	//抓取課表資料進行陣列儲存	
	foreach($_POST[year_seme] as $key=>$year_seme){
		$tmp=explode('_',$year_seme);
		$this_year=$tmp[0];
		$this_semester=$tmp[1];
		
		$sql="SELECT * FROM score_course WHERE year=$this_year AND semester=$this_semester ORDER BY teacher_sn,ss_id,class_id";
		$res=$CONN->Execute($sql) or user_error("讀取課表設定資料失敗！<br>$sql",256);
		while(!$res->EOF){
			$teacher_sn=$res->fields['teacher_sn'];
			$ss_id=$res->fields['ss_id'];
			//尚未有資料才進行教師資料擷取
			if(!$out_arr[$year_seme]['teacherdata'][$teacher_sn]['name']){
				$sql_teacher="SELECT * FROM teacher_base WHERE teacher_sn=$teacher_sn";
				$res_teacher=$CONN->Execute($sql_teacher) or user_error("讀取課表設定資料失敗！<br>$sql_teacher",256);
				$teach_person_id=$res_teacher->fields['teach_person_id'];
				$out_arr[$year_seme]['teacherdata'][$teacher_sn]['teach_person_id']=$teach_person_id;
				$name=$res_teacher->fields['name'];
				switch($_POST['name']){
					case 0: $name=''; break;
					case 1: $name='○○○'; break;
					case 2: $name=substr($name,0,2).'○○'; break;
				}
				$out_arr[$year_seme]['teacherdata'][$teacher_sn]['name']=iconv("Big5","UTF-8",$name);
			}
			//抓取課表資料
			$out_arr[$year_seme]['teacherdata'][$teacher_sn]['subjects'][$ss_id]['subject_name']=iconv("Big5","UTF-8",$ss_arr[$ss_id]['subject']);
			$out_arr[$year_seme]['teacherdata'][$teacher_sn]['subjects'][$ss_id]['learningareas']=iconv("Big5","UTF-8",$ss_arr[$ss_id]['learningareas']);
			
			//依正兼課分別計算
			$counter_type='counter_'.$res->fields['c_kind'];
			$out_arr[$year_seme]['teacherdata'][$teacher_sn]['subjects'][$ss_id][$counter_type]=$out_arr[$year_seme]['teacherdata'][$teacher_sn]['subjects'][$ss_id][$counter_type]+1;
			$out_arr[$year_seme]['teacherdata'][$teacher_sn]['subjects'][$ss_id]['counter']=$out_arr[$year_seme]['teacherdata'][$teacher_sn]['subjects'][$ss_id]['counter']+1;
			
			$class_name=sprintf('%d%02d',$res->fields['class_year'],$res->fields['class_name']).',';
			$class_check=' '.$out_arr[$year_seme]['teacherdata'][$teacher_sn]['subjects'][$ss_id]['class_list'];
			if(! strpos($class_check,$class_name)) $out_arr[$year_seme]['teacherdata'][$teacher_sn]['subjects'][$ss_id]['class_list'].=$class_name;
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
	$smarty->assign("x_id",$_POST['x_id']);
	$smarty->assign("x_pwd",$_POST['x_pwd']);
			
	$smarty->assign("this_year",$this_year);
	$smarty->assign("this_semester",$this_semester);
	
	$smarty->assign("cert",$_POST['cert']);
	$smarty->assign("out_arr",$out_arr);

	//將smarty輸出的資料先cache住
	ob_start();
	$smarty->display("curriculum_x.tpl");
	$xmls=ob_get_contents();
	ob_end_clean();
	
	$filename=$SCHOOL_BASE['sch_id'].$school_long_name.date('Ymd')."中小學教師員額系統課表XML交換資料.xml";
	header("Content-disposition: attachment; filename=$filename");
	header("Content-Type:text/xml; charset=utf-8");
 //因應 IE 6,7,8 在 SSL 模式下無法下載
	header("Cache-Control: max-age=0");
	header("Pragma: public");
	header("Expires: 0");

	echo $xmls;
	exit;
}


head('國教司中小學教師員額系統課表交換XML匯出');
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
		<tr align='center' bgcolor='#ffffaa'><td>選擇學期</td><td>輸出選項</td></tr><tr><td>";
		//<tr align='center' bgcolor='#ffffaa'><td><input type='checkbox' name='tag_all' onClick='javascript:tagall(this.checked);'>選擇學期</td><td>輸出選項</td></tr><tr><td>";
while(!$res->EOF) {
	if(curr_year()-$res->fields[year]<$years) {
		$year_seme=$res->fields[year].'_'.$res->fields[semester];
		$year_seme_name=$res->fields[year].'學年度第'.$res->fields[semester].'學期';
		$this_yeae_seme=curr_year().'_'.curr_seme();
		$checked=$this_yeae_seme==$year_seme?'checked':''; 
		//$main.="<input type='checkbox' name='year_seme[]' value='$year_seme' $checked>$year_seme_name<br>";
		$main.="<input type='radio' name='year_seme[]' value='$year_seme' $checked>$year_seme_name<br>";
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
/*
$main.="</td><td valign='top' align='center'>
<br><br>◎匯入帳號：<input type='text' name='x_id' value='$x_id'>
<br><br>◎匯入密碼：<input type='PASSWORD' name='x_pwd' value='$x_pwd'>
<br><br>◎教師姓名輸出：<input type='radio' name='name' value=0>空白 <input type='radio' name='name' value=1>○○○ <input type='radio' name='name' value=2>輸出首字 (陳○○) <input type='radio' name='name' value=3 checked>輸出全名 (陳大中)
<br><br><br><font size=2 color='red'>※預設的帳號密碼可至模組變數設定</font>
</td></tr>
<tr><td colspan=2>
<input type='submit' style='border-width:1px; cursor:hand; color:white; background:#ff5555; font-size:20px; width=100%; height=80' value='匯出XML' name='act' onclick='return check_select();'></td></tr></table></form>";
*/
$main.="</td><td valign='top' align='center'><br><br>◎教師姓名輸出：<input type='radio' name='name' value=0>空白 <input type='radio' name='name' value=1>○○○ <input type='radio' name='name' value=2>輸出首字 (陳○○) <input type='radio' name='name' value=3 checked>輸出全名 (陳大中)
<br><br>
<input type='submit' style='border-width:1px; cursor:hand; color:white; background:#ff5555; font-size:20px; width=100%; height=80' value='匯出XML' name='act' onclick='return check_select();'></td></tr></table></form>";

echo $main;

foot();


?>