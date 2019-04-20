<?php
//$Id: config.php 6064 2010-08-31 12:26:33Z infodaes $
//預設的引入檔，不可移除。
include_once "../../include/config.php";
require_once "./module-cfg.php";
require_once "./module-upgrade.php";
include "my_fun.php";
include "../../include/sfs_case_score.php";

//教育會考地區代碼
$area_code='07';

//學校代碼
$school_id=$SCHOOL_BASE['sch_id'];

//畢業年級
$graduate_year=$IS_JHORES?9:6;

//畢業資格級分  0.修業 1.畢業
$graduate_score=array(0=>0,1=>2,2=>0);

//志願序級分
$rank_score='0,30,29,28,27,26,25,24,23,22,21,20,19,18,17,16,15,14,13,12,11,10,9,8,7,6,5,4,3,2,1';
$rank_score_array=explode(',',$rank_score);

//國中三年就讀偏遠地區學校者級分加分
$remote_level=array(0=>'不符偏遠地區加分條件',1=>'國中三年就讀偏遠地區學校');
	
//低收入、中低收入戶級分加分
$disadvantage_level=array(0=>'無 ',1=>'中低收入戶',2=>'低收入戶');

//均衡學習單一領域及格級分得分
$balance_score=4;
$balance_score_max=12;
//$balance_semester=array('1011','1012');  //試探系統
$balance_semester=array('1041','1042','1051','1052','1061');  //實際報名

$balance_area=array('health','art','complex');

//社團級分得分
$association_semester_count=1;
$association_semester_score_qualtified=0; //0代表有參加就及格
$association_semester_score=1;
$association_score_max=2;
$association_date_limit='2018-04-27';   //2016/1/6新增 (資料表無日期無法限定)

//服務學習級分得分
$service_semester_minutes=360;
$service_semester_score=1;
$service_score_max=3;
$service_date_limit='2018-04-27';   //2016/1/6新增

//無記過紀錄
//$fault_semester=array('1011','1012','1021','1022','1031');
//$fault_semester=array('1021','1022','1031','1032','1041','1042'); 
$fault_none=6;
$fault_warning=3;
$fault_peccadillo=3;
//$fault_date_limit='2016-04-29';   //2016/1/6新增 (無記過記錄改為以日期判定)
//$fault_date_limit='2017-04-28';   //http://www.tc.edu.tw/news/show/id/85315
$fault_date_limit='2018-04-27';   //http://www.tc.edu.tw/news/show/id/85315

//獎勵紀錄
//$reward_semester=array('1031','1032','1041','1042','1051');
$reward_score[1]=0.5;
$reward_score[3]=1;
$reward_score[9]=3;
$reward_score_max=4;
$reward_date_limit='2018-04-27';

//教育會考
$exam_subject=array('w'=>'寫作測驗','c'=>'國文','m'=>'數學','e'=>'英語','s'=>'社會','n'=>'自然');
$exam_score_well=6;
$exam_score_ok=4;
$exam_score_no=2;
$exam_score_max=30;

$score_write_max=6;

//總分
$max_score=100;

//取得模組參數的類別設定
$m_arr = &get_module_setup("12basic_tcntc");
extract($m_arr,EXTR_OVERWRITE);

//身分類別代碼對照與權值
//$stud_kind_arr=array('0'=>'一般生','1'=>'原住民','2'=>'原住民(語言認證)','3'=>'身心障礙','4'=>'其他','5'=>'境外優秀科技人才子女','6'=>'政府派赴國外工作人員子女','7'=>'僑生','8'=>'蒙藏生');  //原中投區
$stud_kind_arr=array('0'=>'一般生','1'=>'原住民','2'=>'派外人員子女','3'=>'蒙藏生','4'=>'回國僑生','5'=>'港澳生','6'=>'退伍軍人','7'=>'境外優秀科學技術人才子女','8'=>'中科園區生');

//身心障礙
//$stud_disability_arr=array('0'=>'非身心障礙考生','1'=>'智能障礙','2'=>'視覺障礙','3'=>'聽覺障礙','4'=>'語言障礙','5'=>'肢體障礙','6'=>'身體病弱','7'=>'情緒行為障礙','8'=>'學習障礙','9'=>'多重障礙','A'=>'自閉症','B'=>'其他障礙');
$stud_disability_arr=array('0'=>'非身心障礙考生','1'=>'智能障礙','2'=>'視覺障礙','3'=>'聽覺障礙','4'=>'語言障礙','5'=>'肢體障礙','6'=>'腦性麻痺','7'=>'身體病弱','8'=>'情緒行為障礙','9'=>'學習障礙','A'=>'多重障礙','B'=>'自閉症','C'=>'發展遲緩','D'=>'其他障礙');

//身分類別低收失業對照
$stud_free_arr=array('0'=>'一般生','1'=>'低收入戶','2'=>'中低收入戶','3'=>'失業勞工');
$stud_free_rate=array(0=>0,1=>2,2=>1,3=>0);

//$stud_kind_rate=array('0'=>'0','1'=>'10','2'=>'20','3'=>'30','4'=>'40','5'=>'50','6'=>'60','7'=>'70','8'=>'80');
$stud_kind_rate=explode(',',$kind_evaluate);

//屬性欄位順序名稱對照
$kind_field_mirror=array(1=>'clan',2=>'area',3=>'memo',4=>'note');

?>
