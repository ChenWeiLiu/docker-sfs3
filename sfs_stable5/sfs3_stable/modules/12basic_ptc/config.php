<?php

//預設的引入檔，不可移除。
include_once "../../include/config.php";
require_once "./module-cfg.php";
require_once "./module-upgrade.php";
include_once "my_fun.php";
include_once "../../include/sfs_case_score.php";
include_once "../../include/sfs_case_dataarray.php";

//教育會考地區代碼
$area_code='12';

//學校代碼
$school_id=$SCHOOL_BASE['sch_id'];

//畢業年級
$graduate_year=$IS_JHORES?9:6;

//畢業資格積分  0.修業 1.畢業
$graduate_score=array(0=>0,1=>2,2=>0);  //2014-11-23校對  未修改

//志願序積分
$rank_score='0,7,7,7,5,5,5,3,3,3,2,2,2,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1';   //2014-11-23校對修改
$rank_score_array=explode(',',$rank_score);


//均衡學習單一領域及格積分得分
$balance_score=3;
$balance_score_max=9;
//$balance_semester=array('1021','1022','1031','1032','1041');   //2015-10-30校對修改
//$balance_semester=array('1031','1032','1041','1042','1051');   //2016-10-20校對修改
$balance_semester=array('1041','1042','1051','1052','1061');   //2017-09-15校對修改
$balance_area=array('health','art','complex');


//服務表現積分得分
//班級幹部 & 特殊服務表現
$leader_allowed=array(1=>'班長',2=>'副班長',3=>'學藝股長',4=>'風紀股長',5=>'衛生股長',6=>'服務股長',7=>'總務股長',8=>'事務股長',9=>'康樂股長',10=>'體育股長',11=>'輔導股長',12=>'特殊服務表現');
$class_leader=3;    //2014-11-23校對修改
$leader_semester=array('7-1','7-2','8-1','8-2','9-1');

//社團社長
$association_leader=2;    //2014-11-23校對修改
//$association_semester="'1021','1022','1031','1032','1041'";  //2015-10-30校對修改
//$association_semester="'1031','1032','1041','1042','1051'";  //2016-10-20校對修改
$association_semester="'1041','1042','1051','1052','1061'";  //2017-09-15校對修改

$service_score_max=10;  //2014-11-23校對修改

$diversification_score_max=28;  //2014-12-10 新增

//無記過紀錄
$fault_none=10;    //2014-11-23校對修改
$fault_warning=7;    //2014-11-23校對修改
$fault_peccadillo=4;
$fault_score_max=10;    //2014-11-23校對修改
//$reward_date_limit='2016-04-30';    //2014-11-23校對新增
//$reward_date_limit='2017-04-30';    //2016-10-20校對修改
$reward_date_limit='2018-04-30';    //2017-09-15校對修改

//獎勵紀錄  屏東縣未使用       //2014-11-23校對未修改
/*
$reward_score[1]=0.5;
$reward_score[3]=1;
$reward_score[9]=3;
$reward_score_max=4;
*/




//體適能
/*
$fitness_score_one=1;
$fitness_score_one_max=4;
$fitness_addon=array('gold'=>2,'silver'=>1,'copper'=>0.5);
$fitness_semester="'1001','1002','1011','1012','1021','1022'";
$fitness_score_disability=4;
$fitness_score_max=6;
$fitness_medal=array('gold'=>'金','silver'=>'銀','copper'=>'銅','no'=>'--');
*/
$fitness_score_test_all=2;  //2014-11-23校對新增
$fitness_score_one=2;
//$fitness_semester="'1021','1022','1031','1032','1041','1032'";  改為用日期判定
$fitness_score_disability=8;
$fitness_score_max=10;
//$fitness_date_limit='2015-04-30';    //2014-11-23校對新增
//$fitness_date_limit='106-04';    //2017-03-06 修正
$fitness_date_limit='107-04';    //2017-09-15 修正

//適性發展
$my_aspiration=2;
$domicile_suggestion=2;
$guidance_suggestion=2;


//低收入、中低收入戶積分加分
$disadvantage_level=array(0=>'無 ',2=>'低收入戶',1=>'中低收入戶');

//經濟弱勢最高分
$disadvantage_score_max=2;

//教育會考
$exam_subject=array('c'=>'國文','m'=>'數學','e'=>'英語','s'=>'社會','n'=>'自然');
$exam_score_well=5;
$exam_score_ok=3;
$exam_score_no=1;
$exam_score_max=25;

//總分
$max_score=79;

//取得模組參數的類別設定
$m_arr = &get_module_setup("12basic_ptc");
extract($m_arr,EXTR_OVERWRITE);

//身分類別代碼
$stud_kind_arr=array('0'=>'一般生','1'=>'原住民','2'=>'派外人員子女','3'=>'蒙藏生','4'=>'回國僑生','5'=>'港澳生','6'=>'退伍軍人','7'=>'境外優秀科學技術人才子女');

//身心障礙
//$stud_disability_arr=array('0'=>'非身心障礙考生','1'=>'智能障礙','2'=>'視覺障礙','3'=>'聽覺障礙','4'=>'語言障礙','5'=>'肢體障礙','6'=>'身體病弱','7'=>'情緒行為障礙','8'=>'學習障礙','9'=>'多重障礙','A'=>'自閉症','B'=>'其他障礙');
$stud_disability_arr=array('0'=>'非身心障礙考生','1'=>'智能障礙','2'=>'視覺障礙','3'=>'聽覺障礙','4'=>'語言障礙','5'=>'肢體障礙','6'=>'腦性麻痺','7'=>'身體病弱','8'=>'情緒行為障礙','9'=>'學習障礙','A'=>'多重障礙','B'=>'自閉症','C'=>'發展遲緩','D'=>'其他障礙');

//身分類別低收失業對照
$stud_free_arr=array('0'=>'一般生','1'=>'低收入戶','2'=>'中低收入戶','3'=>'失業勞工');
$stud_free_rate=array(0=>0,1=>2,2=>1,3=>0.5);

//競賽成績 ( 搭配 career_race 資料表 )  ( 得要通告學校  有效的名次選項 )
$level_array=array(1=>'國際',2=>'全國、臺灣區',3=>'區域性（跨縣市）',4=>'省、直轄市',5=>'縣市區（鄉鎮）',6=>'校內'); 
//$squad_array=array(1=>'個人賽',2=>'團體賽');
//$squad_team=array('0.5'=>'4人','0.25'=>'20人');
$race_score[1]=array('第一名'=>10,'冠軍'=>10,'金牌'=>10,'特優'=>10,'白金獎'=>10,'第二名'=>9,'亞軍'=>9,'銀牌'=>9,'優等'=>9,'金獎'=>9,'第三名'=>8,'季軍'=>8,'銅牌'=>8,'甲等'=>8,'銀獎'=>8,'第四名'=>7,'殿軍'=>7,'佳作'=>7,'第五名'=>6,'第六名'=>5,'第七名'=>4,'第八名'=>3);
$race_score[2]=array('第一名'=>8,'冠軍'=>8,'金牌'=>8,'特優'=>8,'金獎'=>8,'第二名'=>7,'亞軍'=>7,'銀牌'=>7,'優等'=>7,'銀獎'=>7,'第三名'=>6,'季軍'=>6,'銅牌'=>6,'甲等'=>6,'銅獎'=>6,'第四名'=>5,'殿軍'=>5,'佳作'=>5,'第五名'=>4,'入選'=>4,'第六名'=>3,'第七名'=>2,'第八名'=>1,'特別獎'=>5,'最佳鄉土教材獎'=>5,'最佳團隊合作獎'=>5,'最佳創意獎'=>5);
$race_score[4]=array('第一名'=>5,'冠軍'=>5,'金牌'=>5,'特優'=>5,'第二名'=>4,'亞軍'=>4,'銀牌'=>4,'優等'=>4,'第三名'=>3,'季軍'=>3,'銅牌'=>3,'甲等'=>3,'第四名'=>2,'殿軍'=>2,'第五名'=>1,'第六名'=>0.5,'佳作'=>0.5,'特別獎'=>0.5,'最佳鄉土教材獎'=>0.5,'最佳團隊合作獎'=>0.5,'最佳創意獎'=>0.5);  //,'入選'=>1
$race_score_max=10;


//屬性欄位順序名稱對照
$kind_field_mirror=array(1=>'clan',2=>'area',3=>'memo',4=>'note');

//因為這三個模組變數為後面產生  怕學校已安裝未取回  所以設個預設值
$native_id=intval($native_id)?$native_id:9;
$native_language_sort=intval($native_language_sort)?$native_language_sort:3;
$native_language_text=$native_language_text?$native_language_text:'是';




?>
