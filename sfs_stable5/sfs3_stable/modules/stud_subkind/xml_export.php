<?php
// $Id: xml_export.php 7712 2013-10-23 13:31:11Z smallduh $

include "config.php";
include "../../include/sfs_case_dataarray.php";
sfs_check();

//目標身份t_id
$type_id=($_REQUEST[type_id]);

//原住民身分別註記代號
$m_arr = get_sfs_module_set("stud_subkind");
if($m_arr['foreign_id']=='') $m_arr['foreign_id']='100';
if($m_arr['yuanzhumin_id']=='') $m_arr['yuanzhumin_id']='9';

//學期別
$curr_year_seme = sprintf("%03d%d",curr_year(),curr_seme());

if($type_id==$m_arr['foreign_id'])
{

//定義國別代碼
$nationality=array(
"安道爾"=>"020",
"阿拉伯聯合大公國"=>"784",
"阿富汗"=>"4",
"安地卡及巴布達"=>"28",
"英屬安圭拉"=>"660",
"阿爾巴尼亞"=>"8",
"亞美尼亞"=>"51",
"荷屬安地列斯"=>"530",
"安哥拉"=>"24",
"南極洲"=>"10",
"阿根廷"=>"32",
"美屬薩摩亞"=>"16",
"奧地利"=>"40",
"澳大利亞"=>"36",
"阿魯巴"=>"533",
"亞蘭群島"=>"248",
"亞塞拜然"=>"31",
"波士尼亞赫塞哥維納"=>"70",
"波士尼亞"=>"70",
"巴貝多"=>"52",
"孟加拉"=>"50",
"比利時"=>"56",
"布吉納法索"=>"854",
"保加利亞"=>"100",
"巴林"=>"48",
"蒲隆地"=>"108",
"貝南"=>"204",
"百慕達"=>"60",
"汶萊；汶萊和平之國"=>"96",
"汶萊和平之國"=>"96",
"玻利維亞"=>"68",
"巴西"=>"76",
"巴哈馬"=>"44",
"不丹"=>"64",
"波維特島"=>"74",
"波札那"=>"72",
"白俄羅斯"=>"112",
"貝里斯"=>"84",
"加拿大"=>"124",
"可可斯群島"=>"166",
"剛果民主共和國"=>"180",
"中非；中非共和國"=>"140",
"剛果共和國"=>"178",
"瑞士"=>"756",
"象牙海岸"=>"384",
"科克群島"=>"184",
"庫克群島"=>"184",
"智利"=>"152",
"喀麥隆"=>"120",
"中國大陸"=>"156",
"中華人民共和國"=>"156",
"哥倫比亞"=>"170",
"哥斯大黎加"=>"188",
"古巴"=>"192",
"維德角"=>"132",
"聖誕島"=>"162",
"賽普勒斯"=>"196",
"捷克；捷克共和國"=>"203",
"德國"=>"276",
"吉布地"=>"262",
"丹麥"=>"208",
"多米尼克"=>"212",
"多明尼加共和國"=>"214",
"多明尼加"=>"214",
"阿爾及利亞"=>"12",
"厄瓜多"=>"218",
"愛沙尼亞"=>"233",
"埃及"=>"818",
"西撒哈拉"=>"732",
"厄利垂亞"=>"232",
"西班牙"=>"724",
"衣索比亞"=>"231",
"芬蘭"=>"246",
"斐濟"=>"242",
"福克蘭群島"=>"238",
"密克羅尼西亞"=>"583",
"密克羅尼西亞聯邦"=>"583",
"法羅群島"=>"234",
"法國"=>"250",
"加彭"=>"266",
"英國"=>"826",
"大不列顛與北愛爾蘭聯合王國"=>"826",
"格瑞那達"=>"308",
"喬治亞"=>"268",
"法屬圭亞那"=>"254",
"根西島"=>"831",
"迦納"=>"288",
"直布羅陀"=>"292",
"格陵蘭"=>"304",
"甘比亞"=>"270",
"幾內亞"=>"324",
"瓜德魯普島"=>"312",
"赤道幾內亞"=>"226",
"希臘"=>"300",
"南喬治亞及南桑威奇群島"=>"239",
"瓜地馬拉"=>"320",
"關島"=>"316",
"幾內亞比索"=>"624",
"蓋亞那"=>"328",
"香港"=>"344",
"赫德及麥當勞群島"=>"334",
"宏都拉斯"=>"340",
"克羅埃西亞"=>"191",
"海地"=>"332",
"匈牙利"=>"348",
"印尼"=>"360",
"印度尼西亞"=>"360",
"愛爾蘭"=>"372",
"以色列"=>"376",
"馬恩島"=>"833",
"印度"=>"356",
"英屬印度洋地區"=>"86",
"伊拉克"=>"368",
"伊朗"=>"364",
"伊朗伊斯蘭共和國"=>"364",
"冰島"=>"352",
"義大利"=>"380",
"澤西"=>"832",
"牙買加"=>"388",
"約旦"=>"400",
"日本"=>"392",
"肯亞"=>"404",
"吉爾吉斯"=>"417",
"柬埔寨王國"=>"116",
"吉里巴斯"=>"296",
"葛摩"=>"174",
"聖克里斯多福及尼維斯"=>"659",
"北韓"=>"408",
"朝鮮民主主義人民共和國"=>"408",
"韓國"=>"410",
"大韓民國"=>"410",
"南韓"=>"410",
"科威特"=>"414",
"開曼群島"=>"136",
"哈薩克"=>"398",
"寮國"=>"418",
"寮人民民主共和國"=>"418",
"黎巴嫩"=>"422",
"聖露西亞"=>"662",
"列支敦斯登"=>"438",
"斯里蘭卡"=>"144",
"賴比瑞亞"=>"430",
"賴索托"=>"426",
"立陶宛"=>"440",
"盧森堡"=>"442",
"拉脫維亞"=>"428",
"利比亞"=>"434",
"摩洛哥"=>"504",
"摩納哥"=>"492",
"摩爾多瓦"=>"498",
"摩爾多瓦共和國"=>"498",
"蒙特內哥羅"=>"499",
"馬達加斯加"=>"450",
"馬紹爾群島"=>"584",
"馬其頓"=>"807",
"馬利"=>"466",
"緬甸"=>"104",
"蒙古"=>"496",
"蒙古地方"=>"496",
"澳門"=>"446",
"北馬里亞納群島"=>"580",
"法屬馬丁尼克"=>"474",
"茅利塔尼亞"=>"478",
"蒙瑟拉特島"=>"500",
"馬爾他"=>"470",
"模里西斯"=>"480",
"馬爾地夫"=>"462",
"馬拉威"=>"454",
"墨西哥"=>"484",
"馬來西亞"=>"458",
"莫三比克"=>"508",
"納米比亞"=>"516",
"新喀里多尼亞島"=>"540",
"尼日"=>"562",
"諾福克島"=>"574",
"奈及利亞"=>"566",
"尼加拉瓜"=>"558",
"荷蘭"=>"528",
"挪威"=>"578",
"尼泊爾"=>"524",
"諾魯"=>"520",
"紐威島"=>"570",
"紐埃"=>"570",
"紐西蘭"=>"554",
"阿曼"=>"512",
"巴拿馬"=>"591",
"秘魯"=>"604",
"法屬玻里尼西亞"=>"258",
"巴布亞紐幾內亞"=>"598",
"菲律賓"=>"608",
"巴基斯坦"=>"586",
"波蘭"=>"616",
"聖匹及密啟倫群島"=>"666",
"皮特康島"=>"612",
"波多黎各"=>"630",
"巴勒斯坦"=>"275",
"葡萄牙"=>"620",
"帛琉"=>"585",
"巴拉圭"=>"600",
"卡達"=>"634",
"留尼旺"=>"638",
"羅馬尼亞"=>"642",
"塞爾維亞"=>"688",
"俄羅斯"=>"643",
"俄羅斯聯邦"=>"643",
"盧安達"=>"646",
"沙烏地阿拉伯"=>"682",
"索羅門群島"=>"90",
"塞席爾"=>"690",
"蘇丹"=>"736",
"瑞典"=>"752",
"新加坡"=>"702",
"聖赫勒拿島"=>"654",
"斯洛維尼亞"=>"705",
"斯瓦巴及尖棉島"=>"744",
"斯洛伐克"=>"703",
"獅子山"=>"694",
"聖馬利諾"=>"674",
"塞內加爾"=>"686",
"索馬利亞"=>"706",
"蘇利南"=>"740",
"聖多美普林西比"=>"678",
"薩爾瓦多"=>"222",
"敘利亞"=>"760",
"敘利亞阿拉伯共和國"=>"760",
"史瓦濟蘭"=>"748",
"土克斯及開科斯群島"=>"796",
"查德"=>"148",
"法屬南部屬地"=>"260",
"多哥"=>"768",
"泰國"=>"764",
"塔吉克"=>"762",
"托克勞群島"=>"772",
"東帝汶"=>"626",
"土庫曼"=>"795",
"突尼西亞"=>"788",
"東加"=>"776",
"土耳其"=>"792",
"千里達及托巴哥"=>"780",
"吐瓦魯"=>"798",
"臺灣"=>"158",
"中華民國"=>"158",
"坦尚尼亞"=>"834",
"坦尚尼亞聯合共和國"=>"834",
"烏克蘭"=>"804",
"烏干達"=>"800",
"美國邊疆小島"=>"581",
"美國"=>"840",
"美利堅合眾國"=>"840",
"烏拉圭"=>"858",
"烏茲別克"=>"860",
"教廷"=>"336",
"聖文森及格瑞那丁"=>"670",
"委內瑞拉"=>"862",
"英屬維爾京群島"=>"92",
"美屬維爾京群島"=>"850",
"越南"=>"704",
"萬那杜"=>"548",
"沃里斯與伏塔那島"=>"876",
"薩摩亞"=>"882",
"葉門"=>"887",
"美亞特"=>"175",
"南非"=>"710",
"尚比亞"=>"894",
"辛巴威"=>"716"
);



//今天的日期
$today=(date("Y")-1911).date("年m月d日");


// 取出班級陣列
//$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
//$class_base = class_base($curr_year_seme);




//取得教師所上年級、班級
$session_tea_sn = $_SESSION['session_tea_sn'] ;
$query =" select class_num  from teacher_post  where teacher_sn  ='$session_tea_sn'  ";
$result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;
$row = $result->FetchRow() ;
$class_num = $row["class_num"];

$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'];

if( checkid($SCRIPT_FILENAME,1) OR $class_num) {

//第一階段----取出stud_base合乎資格學生
$type_select="SELECT a.*,left(a.curr_class_num,length(a.curr_class_num)-2) as class_id,right(a.curr_class_num,2) as num,b.* FROM stud_base a left join stud_domicile b ON a.student_sn=b.student_sn WHERE a.stud_study_cond=0 AND a.stud_kind like '%,$type_id,%'";
$type_select.=(!checkid($SCRIPT_FILENAME,1) AND $class_num<>'')?" AND curr_class_num like '$class_num%'":"";
$type_select.=" ORDER BY curr_class_num";
$recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
//將student_sn轉成陣列字串
$select_sn='';
$select_id='';
$key_sn=array();

//取得親屬關係參照
$family_kind=sfs_text("家庭類型");

while ($data=$recordSet->FetchRow()) {
          $select_sn.=$data['student_sn'].",";
		  $select_id.="'".$data['stud_id']."',";
		  $key_sn[$data['stud_id']]=$data['student_sn'];

          $stud_data[$data['student_sn']]['class_id']=$data['class_id'];
          $stud_data[$data['student_sn']]['stud_id']=$data['stud_id'];
          $stud_data[$data['student_sn']]['stud_name']=$data['stud_name'];
          $stud_data[$data['student_sn']]['stud_sex']=($data['stud_sex']==1)?"男":"女";
          $stud_data[$data['student_sn']]['stud_birthday']=$data['stud_birthday'];
          $stud_data[$data['student_sn']]['stud_person_id']=$data['stud_person_id'];
          $stud_data[$data['student_sn']]['stud_kind']=$data['stud_kind'];
          $stud_data[$data['student_sn']]['yuanzhumin']="一般";
		  $stud_data[$data['student_sn']]['sse_family_kind']=$family_kind[$m_arr['default_family_kind']];


		  //解析縣市鄉鎮
		  $analy_arr=array("country"=>array("縣","市"),"downtown"=>array("鄉","鎮","市","區"));
		  $analy_add=array("addr_1"=>$data['stud_addr_1'],"addr_2"=>$data['stud_addr_2']);

		foreach($analy_add as $addr_key=>$addr_value){
		  foreach($analy_arr as $key=>$analy_item){
			foreach($analy_item as $value){
					$pos=strpos($addr_value,$value);
					if($pos) {
						$stud_data[$data['student_sn']][$addr_key][$key]=substr($addr_value,0,$pos+strlen($value));
						$addr_value=substr($addr_value,$pos+strlen($value));
						$stud_data[$data['student_sn']][$addr_key]['left']=$addr_value;
						break;
						}
				}
			}
			}

//echo "<PRE>";
//print_r($stud_data);
//echo "</PRE>";


          $stud_data[$data['student_sn']]['stud_addr_2']=$data['stud_addr_2'];
          $stud_data[$data['student_sn']]['stud_addr_a']=$data['stud_addr_a'];
          $stud_data[$data['student_sn']]['stud_addr_b']=$data['stud_addr_b'];
          $stud_data[$data['student_sn']]['stud_addr_c']=$data['stud_addr_c'];
          $stud_data[$data['student_sn']]['stud_addr_d']=$data['stud_addr_d'];
          $stud_data[$data['student_sn']]['stud_addr_e']=$data['stud_addr_e'];
          $stud_data[$data['student_sn']]['stud_addr_f']=$data['stud_addr_f'];
          $stud_data[$data['student_sn']]['stud_addr_g']=$data['stud_addr_g'];
          $stud_data[$data['student_sn']]['stud_addr_h']=$data['stud_addr_h'];
          $stud_data[$data['student_sn']]['stud_addr_i']=$data['stud_addr_i'];
          $stud_data[$data['student_sn']]['stud_addr_j']=$data['stud_addr_j'];
          $stud_data[$data['student_sn']]['stud_addr_k']=$data['stud_addr_k'];
          $stud_data[$data['student_sn']]['stud_addr_l']=$data['stud_addr_l'];
          $stud_data[$data['student_sn']]['stud_addr_m']=$data['stud_addr_m'];
          $stud_data[$data['student_sn']]['stud_tel_1']=$data['stud_tel_1'];
          $stud_data[$data['student_sn']]['stud_tel_2']=$data['stud_tel_2'];
          $stud_data[$data['student_sn']]['num']=$data['num'];
          $stud_data[$data['student_sn']]['fath_name']=$data['fath_name'];
          $stud_data[$data['student_sn']]['fath_alive']=$data['fath_alive'];
          $stud_data[$data['student_sn']]['moth_name']=$data['moth_name'];
          $stud_data[$data['student_sn']]['moth_alive']=$data['moth_alive'];
          $stud_data[$data['student_sn']]['guardian_name']=$data['guardian_name'];

          $guardian_relation=guardian_relation();
          $relation_code=$data['guardian_relation'];
          $stud_data[$data['student_sn']]['guardian_relation']=$guardian_relation[$relation_code];

          $stud_data[$data['student_sn']]['guardian_phone']=$data['guardian_phone'];
          $stud_data[$data['student_sn']]['guardian_hand_phone']=$data['guardian_hand_phone'];
          }
$select_sn=substr($select_sn,0,-1);
$select_id=substr($select_id,0,-1);

//第二階段----取出stud_subkind紀錄
$type_select="SELECT * FROM stud_subkind WHERE type_id='$type_id' AND student_sn IN ($select_sn)";
$recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);

//將資料加入$stud_data陣列後
while ($data=$recordSet->FetchRow()) {
          $stud_data[$data['student_sn']]['clan']=$data['clan'];
          $stud_data[$data['student_sn']]['area']=$nationality[$data['area']];
          $stud_data[$data['student_sn']]['memo']=$data['memo'];
          $stud_data[$data['student_sn']]['note']=$data['note'];
          }

//3rd階段----取出原住民族群
$type_select="SELECT * FROM stud_subkind WHERE type_id='".$m_arr['yuanzhumin_id']."' AND student_sn IN ($select_sn)";
$recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
//將資料加入$stud_data陣列後
while ($data=$recordSet->FetchRow()) {
	$stud_data[$data['student_sn']]['yuanzhumin']=$data['clan'];
          }

//4th階段----取出親屬狀態
$type_select="SELECT stud_id,sse_family_kind FROM stud_seme_eduh WHERE seme_year_seme='$curr_year_seme' AND stud_id IN ($select_id)";
$recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
//將資料加入$stud_data陣列後
while ($data=$recordSet->FetchRow()) {
	$stud_data[$key_sn[$data['stud_id']]]['sse_family_kind']=$family_kind[$data['sse_family_kind']];
          }


################################    輸出 XML    ##################################
$filename = $school_id.$school_short_name."學生身份 $class_num [ $type_id ] 清冊.XML";
$Str="<?xml version='1.0' encoding='UTF-8' ?>
<!DOCTYPE 學籍交換資料 SYSTEM 'dtd_stu_1.dtd'>
<學籍交換資料>\r\n";

$sn_data=explode(',',$select_sn);

foreach($sn_data as $sn)
{
$Str.="  <學生基本資料>
    <證照號碼>".$stud_data[$sn]['stud_person_id']."</證照號碼>
    <學生姓名>".$stud_data[$sn]['stud_name']."</學生姓名>
    <學生性別>".$stud_data[$sn]['stud_sex']."</學生性別>
    <學生生日>".$stud_data[$sn]['stud_birthday']."</學生生日>
    <身份別>".$stud_data[$sn]['yuanzhumin']."</身份別>
    <教育部_學校代號>".$SCHOOL_BASE['sch_id']."</教育部_學校代號>
    <現在年級>".substr($stud_data[$sn]['class_id'],0,1)."</現在年級>
    <現在班級>".substr($stud_data[$sn]['class_id'],1,2)."</現在班級>
    <戶籍地址_縣市>".$stud_data[$sn]['addr_1']['country']." </戶籍地址_縣市>
    <戶籍地址_鄉鎮市區>".$stud_data[$sn]['addr_1']['downtown']."</戶籍地址_鄉鎮市區>
    <戶籍地址>".$stud_data[$sn]['addr_1']['left']."</戶籍地址>
    <戶籍電話>".$stud_data[$sn]['stud_tel_1']."</戶籍電話>
    <通訊地址_縣市>".$stud_data[$sn]['addr_2']['country']."</通訊地址_縣市>
    <通訊地址_鄉鎮市區>".$stud_data[$sn]['addr_2']['downtown']."</通訊地址_鄉鎮市區>
    <通訊地址>".$stud_data[$sn]['addr_2']['left']."</通訊地址>
    <通訊電話>".$stud_data[$sn]['stud_tel_2']."</通訊電話>
    <親屬狀態>".$stud_data[$sn]['sse_family_kind']."</親屬狀態>
    <監護人_姓名>".$stud_data[$sn]['guardian_name']."</監護人_姓名>
    <監護人_連絡電話>".$stud_data[$sn]['guardian_phone']."</監護人_連絡電話>
    <監護人_行動電話>".$stud_data[$sn]['guardian_hand_phone']."</監護人_行動電話>
    <與監護人之關係>".$stud_data[$sn]['guardian_relation']."</與監護人之關係>
    <父或母為外籍人士>".$stud_data[$sn]['clan']."</父或母為外籍人士>
    <父或母國籍>".$stud_data[$sn]['area']."</父或母國籍>
    <父或母是否取得中華民國國籍>".$stud_data[$sn]['memo']."</父或母是否取得中華民國國籍>
  </學生基本資料>\r\n";
}
$Str.="</學籍交換資料>";

$Str=iconv("Big5","UTF-8",$Str);


header("Content-type: application/xml");
header("Content-disposition: attachment; filename=$filename");
//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
header("Expires: 0");

echo $Str;
} else { echo "\n<script language=\"Javascript\"> alert (\"您並未被授權使用此模組(非導師或模組管理員)！\")</script>"; }
} else { echo "\n<script language=\"Javascript\"> alert (\"請檢查變數設定!!！\")</script>"; }
?>
