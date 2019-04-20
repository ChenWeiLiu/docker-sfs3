<?php
//$Id: config.php 6064 2010-08-31 12:26:33Z infodaes $
//預設的引入檔，不可移除。
include_once "../../include/config.php";
require_once "./module-cfg.php";
include "my_fun.php";
include "../../include/sfs_case_score.php";
require_once "./module-upgrade.php";

//教育會考地區代碼
$area_code='09';

//學校代碼
$school_id=$SCHOOL_BASE['sch_id'];

//畢業年級
$graduate_year=$IS_JHORES?9:6;

//畢業資格級分  0.修業 1.畢業 2.肄業
$graduate_score=array(0=>0,1=>1,2=>0);

//志願序積分
$rank_score='0,9,9,9,9,7,7,7,7,5,5,5,5,3,3,3,3';
$rank_score_array=explode(',',$rank_score);

//扶助弱勢積分	
//經濟弱勢(低收入、中低收入戶積分加分)
//$disadvantage_level=array(0=>'無 ',1=>'中低收入戶',2=>'低收入戶');(中投)
$disadvantage_level=array(0=>'不符合',1=>'符合');

//國中三年偏遠地區學校者積分加分
$remote_level=array(0=>'不符偏遠小校加分條件',2=>'7班以下',1=>'8-12班');
//改自模組變數 remote_school 中抓取 

//就近入學積分
$nearby_level=array(1=>'符合',0=>'不符合');

//品德服務積分
//獎勵紀錄
$reward_kind=array(1=>'嘉獎一次',2=>'嘉獎二次',3=>'小功\一次',4=>'小功\二次',5=>'大功\一次',6=>'大功\二次',7=>'大功\三次',-1=>'警告一次',-2=>'警告二次',-3=>'小過一次',-4=>'小過二次',-5=>'大過一次',-6=>'大過二次',-7=>'大過三次');		//獎懲類別
$reward_semester="'1051','1052','1061','1062','1071'";		//獎懲紀錄取用學期(取國1~國3上)
$reward_score[1]=0.5;
$reward_score[3]=1.5;
$reward_score[9]=4.5;
$reward_score_max=15;
//無記過紀錄
$fault_start_semester=1051;		//開始採計學期
$fault_none=5;
$fault_warning=1;
//$fault_peccadillo=0;
$fault_score_max=5;
//出缺席紀錄
$absence_score='5,3,3,3,3,3,1,1,1,1,1';
$absence_score_array=explode(',',$absence_score);
$absence_semester="'1051','1052','1061','1062','1071'";		//取國1~國3上
//$absence_semester="'1011','1012','1021'";		//取國2~國3上
$absence_score_max=5;

//多元學習表現積分
//均衡學習單一領域及格積分得分
$balance_score=3;
$balance_score_max=9;
$balance_semester=array('1051','1052','1061','1062','1071');
$balance_area=array('health','art','complex');
/*
//社團積分得分(中投)
$association_semester_count=1;
$association_semester_score_qualtified=0; //0標楷體參加標楷體
$association_semester_score=1;
$association_score_max=2;
//服務學習積分得分(中投)
$service_semester_minutes=360;
$service_semester_score=1;
$service_score_max=3;
//適性發展 (屏東)
$my_aspiration=2;
$domicile_suggestion=2;
$guidance_suggestion=2;
*/
//競賽成績 ( 搭配 career_race 資料表 )  ( 得要通告學校  有效的名次選項 )
//$level_array=array(1=>'國際',2=>'全國、臺灣區',3=>'區域性（跨縣市）',4=>'省、直轄市',5=>'縣市區（鄉鎮）',6=>'校內');
$level_array=array(1=>'國際',2=>'全國',3=>'全縣',4=>'全縣');
$squad_array=array(1=>'個人賽',2=>'團體賽');
$squad_team=array('0.5'=>'4人','0.25'=>'20人');
//$race_score[1]=array('第一名'=>7,'特優'=>7,'金牌'=>7,'冠軍'=>7,'第二名'=>7,'優等'=>7,'銀牌'=>7,'亞軍'=>7,'第三名'=>7,'甲等'=>7,'銅牌'=>7,'季軍'=>7,'第四名'=>7,'佳作'=>7,'入選'=>7,'殿軍'=>7);
//$race_score[2]=array('第一名'=>7,'特優'=>7,'金牌'=>7,'冠軍'=>7,'第二名'=>6,'優等'=>6,'銀牌'=>6,'亞軍'=>6,'第三名'=>5,'甲等'=>5,'銅牌'=>5,'季軍'=>5,'第四名'=>4,'佳作'=>4,'入選'=>4,'殿軍'=>4,'第五名'=>4,'第六名'=>4);
//$race_score[3]=array('第一名'=>3,'特優'=>3,'金牌'=>3,'冠軍'=>3,'第二名'=>2,'優等'=>2,'銀牌'=>2,'亞軍'=>2,'第三名'=>1,'甲等'=>1,'銅牌'=>1,'季軍'=>1,'第四名'=>0.5,'佳作'=>0.5,'入選'=>0.5,'殿軍'=>0.5,'第五名'=>0.5,'第六名'=>0.5);
//$race_score[4]=array('第一名'=>3,'特優'=>3,'金牌'=>3,'冠軍'=>3,'第二名'=>2,'優等'=>2,'銀牌'=>2,'亞軍'=>2,'第三名'=>1,'甲等'=>1,'銅牌'=>1,'季軍'=>1,'第四名'=>0.5,'佳作'=>0.5,'入選'=>0.5,'殿軍'=>0.5,'第五名'=>0.5,'第六名'=>0.5);
$race_score[1]=array('第一名'=>7,'金牌'=>7,'冠軍'=>7,'第二名'=>7,'銀牌'=>7,'亞軍'=>7,'第三名'=>7,'銅牌'=>7,'季軍'=>7,'第四名'=>7,'佳作'=>7,'殿軍'=>7);
//$race_score[2]=array('第一名'=>7,'金牌'=>7,'冠軍'=>7,'第二名'=>6,'銀牌'=>6,'亞軍'=>6,'第三名'=>5,'銅牌'=>5,'季軍'=>5,'第四名'=>4,'佳作'=>4,'殿軍'=>4,'第五名'=>4,'第六名'=>4);
$race_score[2]=array('第一名'=>7,'特優'=>7,'金牌'=>7,'冠軍'=>7,'第二名'=>6,'優等'=>6,'銀牌'=>6,'亞軍'=>6,'第三名'=>5,'甲等'=>5,'銅牌'=>5,'季軍'=>5,'第四名'=>4,'佳作'=>4,'入選'=>4,'殿軍'=>4,'第五名'=>4,'第六名'=>4);
$race_score[3]=array('第一名'=>3,'第二名'=>2,'第三名'=>1,'第四名'=>0.5,'佳作'=>0.5,'第五名'=>0.5,'第六名'=>0.5);
$race_score[4]=array('第一名'=>3,'第二名'=>2,'第三名'=>1,'第四名'=>0.5,'佳作'=>0.5,'第五名'=>0.5,'第六名'=>0.5);

$race_score_max=9;

//體適能
$fitness_score_one=3;
$fitness_score_one_max=6;
$fitness_addon=array('gold'=>0,'silver'=>0,'copper'=>0);
$fitness_semester="'1051','1052','1061','1062','1071'";
$fitness_score_max=6;
$fitness_medal=array('gold'=>'金','silver'=>'銀','copper'=>'銅','no'=>'--');

//獎勵+競賽+體適能分數上限
$reward_competetion_fitness_score_max=25;

//教育會考積分
$exam_subject=array('c'=>'國文','m'=>'數學','e'=>'英語','s'=>'社會','n'=>'自然');
$exam_score_well=6;
$exam_score_ok=4;
$exam_score_no=2;
$exam_score_max=30;

//總分
$max_score=90;
$editable_hint=" <font size=1 color='brown'>◎出現手指型鼠標時，可快按兩下可進行修改◎</font>";


//取得模組參數的類別設定
$m_arr = &get_module_setup("12basic_ylc");
extract($m_arr,EXTR_OVERWRITE);

//身分類別代碼對照與權值
$stud_kind_arr_12ylc=array('0'=>'一般生','1'=>'原住民','2'=>'派外人員子女','3'=>'蒙藏生','4'=>'回國僑生','5'=>'港澳生','6'=>'退伍軍人','7'=>'境外優秀科學技術人才子女');
//$stud_kind_rate=array('0'=>'0','1'=>'10','2'=>'20','3'=>'30','4'=>'40','5'=>'50','6'=>'60','7'=>'70','8'=>'80');
$stud_kind_rate=explode(',',$kind_evaluate);

/*
echo '<pre>';
print_r($stud_kind_rate);
echo '</pre>';
*/

//屬性欄位順序名稱對照
$kind_field_mirror=array(1=>'clan',2=>'area',3=>'memo',4=>'note');

//因為這三個模組變數為後面產生  怕學校已安裝未取回  所以設個預設值
$native_id=intval($native_id)?$native_id:9;
$native_language_sort=intval($native_language_sort)?$native_language_sort:3;
$native_language_text=$native_language_text?$native_language_text:'是';

//身心障礙
$stud_disability_arr_12ylc=array('0'=>'非身心障礙考生','1'=>'智能障礙','2'=>'視覺障礙','3'=>'聽覺障礙','4'=>'語言障礙','5'=>'肢體障礙','6'=>'腦性麻痺','7'=>'身體病弱','8'=>'情緒行為障礙','9'=>'學習障礙','A'=>'多重障礙','B'=>'自閉症','C'=>'發展遲緩','D'=>'其他障礙');

//身分類別低收失業對照
$stud_free_arr_12ylc=array('0'=>'一般生','1'=>'低收入戶','2'=>'中低收入戶','3'=>'失業勞工子女');
$stud_free_rate=array(0=>0,1=>2,2=>1,3=>0);


//是否顯示大頭照
//$pic_checked=$_POST[pic_checked];


?>
