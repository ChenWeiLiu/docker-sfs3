<?php
//$Id: export.php 8524 2015-09-09 14:36:41Z chiming $
include "config.php";

//認證
sfs_check();
$blank_name=$_POST['blank_name'];

if ($_POST[do_key]) {
	if ($_POST[data_id]==0) {
		$sss="\"學年度\",\"學校代號\",\"學生姓名\",\"學生性別\",\"出生年月日\",\"學生等級別\",\"學生身分別\",\"年級別\",\"右眼裸視視力\",\"左眼裸視視力\",\"外籍配偶子女\",\"家庭現況\"\r\n";
		$sss.="\"YEAR\",\"SCODE\",\"NAME\",\"SEX\",\"BIRTH\",\"LEVEL\",\"SORTS\",\"YEARS\",\"RIGHT\",\"LEFT\",\"FOREIGN\",\"FAMILY\"\r\n";

		$sel_year=curr_year();
		$sel_seme=curr_seme();
		$lv=($IS_JHORES==0)?"C":"J";
		$sorts_arr=array("一般生"=>10,"其他"=>20,"阿美族"=>21,"泰雅族"=>22,"排灣族"=>23,"布農族"=>24,"卑南族"=>25,"鄒族"=>26,"曹族"=>26,"魯凱族"=>27,"賽夏族"=>28,"達悟族"=>29,"雅美族"=>29,"邵族"=>"2A","噶瑪蘭族"=>"2B","太魯閣族"=>"2C","撒奇萊雅族"=>"2D","賽德克族"=>"2E","僑生"=>"30");
		//取出學校代碼
		$query="select sch_id from school_base";
		$res=$CONN->Execute($query);
		$sch_id=$res->fields[sch_id];

		//取出學生身分別資料
		$query="select student_sn,clan,type_id from stud_subkind where type_id in ('6','9')";
		$res=$CONN->Execute($query) or die('缺少學生身份類別與屬性資料表stud_subkind');
		while(!$res->EOF) {
			$student_sn=$res->fields[student_sn];
			$clan[$student_sn]=trim($res->fields[clan]);
			$type_id[$student_sn]=$res->fields[type_id];
			if (substr($clan[$student_sn],-2,2)!="族") {
				$clan[$student_sn].="族";
			}
			$res->MoveNext();
		}

		$foreign_arr=array("中國"=>1,"中華人民共和國"=>1,"大陸地區"=>1,"中國大陸"=>1,"越南"=>2,"印度尼西亞"=>3,"印尼"=>3,"泰國"=>4,"菲律賓"=>5,"柬埔寨"=>6,"日本"=>7,"馬來西亞"=>"8","美國"=>"9","北韓"=>"10","南韓"=>"10","韓國"=>"10","緬甸"=>"11","新加坡"=>"12","加拿大"=>"13");
				
		//取出學生外籍配偶子女身分別資料
		$query="select student_sn,area from stud_subkind where type_id='".($m_arr['foreign_id']?$m_arr['foreign_id']:'100')."'";
		
		
		$res=$CONN->Execute($query) or die('缺少學生身份類別與屬性資料表stud_subkind');
		while(!$res->EOF) {
			$student_sn=$res->fields[student_sn];
			$foreign_area[$student_sn]=trim($res->fields[area]);
			$foreign_id[$student_sn]=$foreign_arr[$res->fields[area]];
			//假使有定義國別   卻未於指定代號表列  則賦以"其他"(14)
			if($foreign_area[$student_sn] and ! $foreign_id[$student_sn]) { $foreign_id[$student_sn]='14'; }
			$res->MoveNext();
		}
		
		//取出視力資料
		$query="select * from health_sight where year='$sel_year' and semester='$sel_seme'";
		$res=$CONN->Execute($query) or die('缺少學生健康資訊模組資料表 health_sight');
		while(!$res->EOF) {
			$sight_v[$res->fields[student_sn]][$res->fields[side]]=$res->fields[sight_o];
			$res->MoveNext();
		}

		//取出學生資料
		$student_error="";
		$query="select * from stud_base where stud_study_cond in ('0','15') order by curr_class_num";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$student_birthday=$res->fields[stud_birthday];
			$stud_id=$res->fields[stud_id];
			//檢查出生年月日是否未填
			if($student_birthday<>'0000-00-00') {
				$student_sn=$res->fields[student_sn];
				$s=($res->fields[stud_sex]==2)?"F":"M";
				$ss=$clan[$student_sn];
				$fs=$foreign_id[$student_sn];
				if ($type_id[$res->fields[student_sn]]==6)
					$st="30";
				elseif ($ss=="")
					$st="10";
				else {
					$st=$sorts_arr[$ss];
					if ($st=="") $st="20";
				}
				$r=number_format($sight_v[$res->fields[student_sn]][r],1);
				$l=number_format($sight_v[$res->fields[student_sn]][l],1);
				$bday=explode("-",$res->fields[stud_birthday]);
				if($blank_name) $student_name=''; else $student_name=$res->fields[stud_name];
				
				//國中年級-6
				$grade=substr($res->fields[curr_class_num],0,1);
				$years=($lv=='J')?$grade-6:$grade;
				
				//抓取家庭狀況(以學期反向排序，抓取最後學期的註記)    L：家庭現況( 1碼)，1=雙親，2=單親，3=寄親
				//$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
				$sql="SELECT sse_family_kind FROM stud_seme_eduh WHERE stud_id='$stud_id' ORDER BY seme_year_seme DESC";
				$res2=$CONN->Execute($sql) or die('缺少學生輔導資料表 stud_seme_eduh');
				$family_kind=$res2->fields[0]?$res2->fields[0]:1;  //若無記錄則預設為~~ 1.雙親 
				
				
				if($quoted) $sss.="$sel_year,\"".sprintf("%06d",$sch_id)."\",\"$student_name\",\"$s\",".sprintf("%03d%02d",intval($bday[0])-1911,$bday[1],$bday[2]).",\"$lv\",\"$st\",$years,$r,$l,$fs,$family_kind\r\n";
				else  $sss.="$sel_year,".sprintf("%06d",$sch_id).",$student_name,$s,".sprintf("%03d%02d%02d",intval($bday[0])-1911,$bday[1],$bday[2]).",$lv,$st,$years,$r,$l,$fs,$family_kind\r\n";
			} else {
				$student_error.='◎學號：'.$res->fields[stud_id].'  班級座號：'.$res->fields[curr_class_num].'  姓名：'.$res->fields[stud_name].'<br>';
			}
			$res->MoveNext();
		}
		
		//判斷是否有學生生日資料錯誤
		if(! $student_error) {
			header("Content-disposition: attachment; filename=student.csv");
			header("Content-type: application/octetstream; Charset=Big5");
			//header("Pragma: no-cache");
							//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

			header("Expires: 0");
			echo $sss;
			exit;	
		}
	}
}

//資料選單
$sel1 = new drop_select();
$sel1->s_name="data_id";
$sel1->id= $data_id;
$sel1->arr = array("0"=>"學生CSV檔");
$sel1->has_empty = false;
$sel1->is_submit = true;
$smarty->assign("data_sel",$sel1->get_select());

//判斷視力表是否存在
$query="select * from health_sight where 1=0";
$res=$CONN->Execute($query);
if ($res) {
	$smarty->assign("OK1",1);
	$query="select count(*) as nums from health_sight where year='".curr_year()."' and semester='".curr_seme()."'";
	$res=$CONN->Execute($query);
	if ($res->fields['nums']>0) $smarty->assign("OK2",1);
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("SFS_PATH_HTML",$SFS_PATH_HTML);
$smarty->assign("module_name","資料匯出");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("STUDENT_ERROR",$student_error);
if($student_error) $smarty->display("edu_chart_error.tpl"); else $smarty->display("edu_chart_export.tpl");

