<?php

// $Id:$

//include_once "../../include/sfs_case_ado.php";
include_once "../../include/sfs_case_dataarray.php";
include_once "../../include/sfs_case_score.php";
include_once "../../include/sfs_case_subjectscore.php";
include_once "../../include/sfs_oo_zip2.php";
include_once "../12basic_career/my_fun.php";

class sfsxmlfile
{
	var $student_sn;
	var $out_arr;
	var $sn_str;
	var $zip;

	function sfsxmlfile()
	{
		$this->init();
	}

	function init()
	{
		$this->out_arr=array();
		$this->zip = new EasyZip;
	}

	function output()
	{
		if (count($this->student_sn)>0) {
			$this->sn_str="'".implode("','",array_keys($this->student_sn))."'";
			$this->base();
//			$this->seme();
//			$this->move();
//			$this->mid_seme();
		}
	}

	function base()
	{
		global $CONN,$addr,$all_reward,$IS_JHORES;
		global $class_name_kind,$class_name_kind_1,$class_name_kind_2,$class_name_kind_3;
		
		//取出班級導師姓名參照，下面的學期資料會用到
		$class_teacher_name=class_teacher();
		
		//取得領域名稱(彈性課程需顯示名稱)
        $subject_name_arr=get_subject_name_arr();
		
		//輔導資料參照陣列
		$sse_relation_arr = sfs_text("父母關係");
		$sse_family_kind_arr=sfs_text("家庭類型");
		$sse_family_air_arr=sfs_text("家庭氣氛");
		$sse_teach_arr=sfs_text("管教方式");
		$sse_live_state_arr=sfs_text("居住情形");
		$sse_rich_state_arr=sfs_text("經濟狀況");

		$sse_arr= array("1"=>"喜愛困難科目","2"=>"喜愛困難科目","3"=>"特殊才能","4"=>"興趣","5"=>"生活習慣","6"=>"人際關係","7"=>"外向行為","8"=>"內向行為","9"=>"學習行為","10"=>"不良習慣","11"=>"焦慮行為");
		while(list($id,$val)= each($sse_arr)){
			$temp_sse_arr = sfs_text("$val");
			${"sse_arr_$id"} = $temp_sse_arr;
		}
		
		//日常生活表現類別參照
		$ss_arr=array('0'=>'[日常行為]','1'=>'[團體活動]','2'=>'[公共服務-校內]','3'=>'[公共服務-社區]','4'=>'[特殊表現-校內]','4'=>'[特殊表現-校外]');
		
		//異動陣列參照
		$study_cond_arr=study_cond();
		
		//取出 stud_base 資料
		$query="select a.*,left(a.curr_class_num,length(a.curr_class_num)-4) as year_num,mid(a.curr_class_num,length(a.curr_class_num)-3,2) as class_num,right(a.curr_class_num,2) as site_num,b.grad_kind,b.grad_date,b.grad_word,b.grad_num from stud_base a left join grad_stud b on a.student_sn=b.student_sn where a.student_sn in ($this->sn_str) order by a.student_sn";
		$res=$CONN->Execute($query) or die("SQL錯誤： $query");

		while(!$res->EOF) {
			//處理學生身分別 1~18 為標準選項
			$oth_arr=array();
			$stud_kind_arr=explode(",",$res->fields[stud_kind]);
			$stud_id=$res->fields['stud_id'];
			$student_sn=$res->fields['student_sn'];
			reset($stud_kind_arr);
			while(list($k,$v)=each($stud_kind_arr)) {
				if (intval($v)<19 and $v!="") $oth_arr[stud_kind][]=$v;
			}
			//處理戶籍住址和連絡住址

			//假使調出紀錄裡面有記載遷移新址	將地址替換為該新址	
			//$sql_move="select new_address from stud_seme_move where student_sn=$student_sn and move_id=8 order by move_date desc";
			$sql_move="select new_address from stud_move where stud_id='$stud_id' and move_kind='8' order by move_date desc";
			$res_move=$CONN->Execute($sql_move);
			$addr=$res_move->fields['new_address'];


			if($addr) $oth_arr[stud_addr_1]=change_addr_str_xml($addr);	else
			{			
				$addr=$res->fields[stud_addr_1];
				$oth_arr[stud_addr_1]=change_addr_str_xml($addr);
				//$oth_arr[stud_addr_1][12]=implode("",array_slice($oth_arr[stud_addr_1],4,8));
			}

			$addr=$res->fields[stud_addr_2];
			$oth_arr[stud_addr_2]=change_addr_str_xml($addr);
			//$oth_arr[stud_addr_2][12]=implode("",array_slice($oth_arr[stud_addr_2],4,8));
			$this->out_arr[$res->fields[student_sn]]=array_merge($res->FetchRow(),$oth_arr);
			
		}
		//取出 stud_domicile 資料
		$query="select * from stud_domicile where student_sn in ($this->sn_str) order by student_sn";
		$res=$CONN->Execute($query) or die("SQL錯誤： $query");
		while(!$res->EOF) {
			 $this->out_arr[$res->fields[student_sn]]=array_merge($this->out_arr[$res->fields[student_sn]],$res->FetchRow());
		}

		//取出 stud_brother_sister 資料
		$query="select bs_id,bs_name,bs_calling,bs_gradu,bs_birthyear,student_sn from stud_brother_sister where student_sn in ($this->sn_str) order by student_sn";
		$res=$CONN->Execute($query) or die("SQL錯誤： $query");
		while(!$res->EOF) {
			 $this->out_arr[$res->fields[student_sn]][bro_sis][$res->fields[bs_id]]=$res->FetchRow();
		}
		//取出 stud_kinfolk 資料
		$query="select kin_id,kin_name,kin_calling,kin_phone,kin_hand_phone,kin_email,student_sn from stud_kinfolk where student_sn in ($this->sn_str) order by student_sn";
		$res=$CONN->Execute($query) or die("SQL錯誤： $query");
		while(!$res->EOF) {
			 $this->out_arr[$res->fields[student_sn]][kinfolk][$res->fields[kin_id]]=$res->FetchRow();
		}
		
		//取出原住民資料(stud_subkind)
		$query="select student_sn,clan,area from stud_subkind where type_id=9 AND student_sn in ($this->sn_str) order by student_sn";
		$res=$CONN->Execute($query) or die("SQL錯誤： $query");
		while(!$res->EOF) {
			 $this->out_arr[$res->fields[student_sn]][yuanzhumin]=$res->FetchRow();
		}
		
		//抓取日常生活表現紀錄資料
		$query="select * from stud_seme_score_nor where student_sn in ($this->sn_str) order by student_sn,seme_year_seme,ss_id";
		$res=$CONN->Execute($query) or die("SQL錯誤： $query");
		while(!$res->EOF) {
			$current_student_sn=$res->fields[student_sn];
			$current_seme_year_seme=$res->fields[seme_year_seme];
			$ss_id=$res->fields[ss_id];

			if($res->fields[ss_score_memo])  $this->out_arr[$current_student_sn][semester_score_nor][$current_seme_year_seme][ss_score_memo].=$ss_arr[$ss_id].$this->zip->change_str($res->fields[ss_score_memo],1,0).' ';			
			$res->MoveNext();
		}
		
		//抓取出缺席統計資料
		$query="select a.*,b.student_sn from stud_seme_abs a,stud_base b where b.student_sn in ($this->sn_str) AND a.stud_id=b.stud_id order by b.student_sn,a.abs_kind";
		$res=$CONN->Execute($query) or die("SQL錯誤： $query");
		while(!$res->EOF) {
			$current_student_sn=$res->fields[student_sn];
			$current_seme_year_seme=$res->fields[seme_year_seme];
			$absence_kind=$res->fields['abs_kind'];
			$absence_days=$res->fields['abs_days'];
			$this->out_arr[$current_student_sn][semester_absence][$current_seme_year_seme][$absence_kind]=$absence_days;
			//加總其他假數
			if($absence_kind>3) $this->out_arr[$current_student_sn][semester_absence][$current_seme_year_seme][others]+=$absence_days;
			$res->movenext();
		}

		 
		//抓取特殊表現資料
		$query="select a.*,b.student_sn from stud_seme_spe a,stud_base b where b.student_sn in ($this->sn_str) AND a.stud_id=b.stud_id order by b.student_sn,a.seme_year_seme";
		$res=$CONN->Execute($query) or die("SQL錯誤： $query");
		while(!$res->EOF) {
			$current_student_sn=$res->fields[student_sn];
			$current_seme_year_seme=$res->fields[seme_year_seme];
			$ss_id=$res->fields[ss_id];
			$this->out_arr[$current_student_sn][semester_spe][$current_seme_year_seme][$ss_id][sp_date]=$res->fields[sp_date];
			$this->out_arr[$current_student_sn][semester_spe][$current_seme_year_seme][$ss_id][sp_memo]=$res->fields[sp_memo];
			$res->movenext();
		}
		
		//抓取心理測驗紀錄
		$query="select * from stud_psy_test where student_sn in ($this->sn_str) order by student_sn,year,semester";
		$res=$CONN->Execute($query) or die("SQL錯誤： $query");
		while(!$res->EOF) {
			$current_student_sn=$res->fields[student_sn];
			$current_seme_year_seme=sprintf("%03d%d",$res->fields[year],$res->fields[semester]);
			$sn=$res->fields[sn];
			$this->out_arr[$current_student_sn][psy_test][$current_seme_year_seme][$sn][test_date]=$res->fields[test_date];
			$this->out_arr[$current_student_sn][psy_test][$current_seme_year_seme][$sn][item]=$res->fields[item];
			$this->out_arr[$current_student_sn][psy_test][$current_seme_year_seme][$sn][score]=$res->fields[score];
			$this->out_arr[$current_student_sn][psy_test][$current_seme_year_seme][$sn][model]=$res->fields[model];
			$this->out_arr[$current_student_sn][psy_test][$current_seme_year_seme][$sn][standard]=$res->fields[standard];
			$this->out_arr[$current_student_sn][psy_test][$current_seme_year_seme][$sn][pr]=$res->fields[pr];
			$this->out_arr[$current_student_sn][psy_test][$current_seme_year_seme][$sn][explanation]=$res->fields[explanation];
			$res->movenext();
		}
		
		//抓取輔導紀錄A表
		$query="select a.*,b.student_sn from stud_seme_eduh a,stud_base b where b.student_sn in ($this->sn_str) AND a.stud_id=b.stud_id order by b.student_sn,a.seme_year_seme";
		$res=$CONN->Execute($query) or die("SQL錯誤： $query");
		while(!$res->EOF) {
			$current_student_sn=$res->fields[student_sn];
			$current_seme_year_seme=$res->fields[seme_year_seme];
			$row_data=$res->FetchRow();
			//$this->out_arr[$current_student_sn][semester_eduh][$current_seme_year_seme]=$row_data;
 			
 			//父母關係
			$this->out_arr[$current_student_sn][semester_eduh][$current_seme_year_seme][sse_relation]=$sse_relation_arr[$row_data[sse_relation]];
			//家庭類型
			$this->out_arr[$current_student_sn][semester_eduh][$current_seme_year_seme][sse_family_kind]=$sse_family_kind_arr[$row_data[sse_family_kind]];
			//家庭氣氛
			$this->out_arr[$current_student_sn][semester_eduh][$current_seme_year_seme][sse_family_air]=$sse_family_air_arr[$row_data[sse_family_air]];
			//管教方式
			$this->out_arr[$current_student_sn][semester_eduh][$current_seme_year_seme][sse_father]=$sse_teach_arr[$row_data[sse_farther]];
			$this->out_arr[$current_student_sn][semester_eduh][$current_seme_year_seme][sse_mother]=$sse_teach_arr[$row_data[sse_mother]];
			//居住情形
			$this->out_arr[$current_student_sn][semester_eduh][$current_seme_year_seme][sse_live_state]=$sse_live_state_arr[$row_data[sse_live_state]];
			//經濟狀況
			$this->out_arr[$current_student_sn][semester_eduh][$current_seme_year_seme][sse_rich_state]=$sse_rich_state_arr[$row_data[sse_rich_state]];

			$sse_arr= array("1"=>"喜愛困難科目","2"=>"喜愛困難科目","3"=>"特殊才能","4"=>"興趣","5"=>"生活習慣","6"=>"人際關係","7"=>"外向行為","8"=>"內向行為","9"=>"學習行為","10"=>"不良習慣","11"=>"焦慮行為");	
			foreach($sse_arr as $key=>$val) {
				$sse_no="sse_s$key";
				$sse_no_data=explode(",",$row_data[$sse_no]);
				$temp_arr=${"sse_arr_$key"};
				foreach($sse_no_data as $key2=>$val2) $sse_no_data[$key2]=$temp_arr[$val2];
				$this->out_arr[$current_student_sn][semester_eduh][$current_seme_year_seme][$sse_no]=$sse_no_data;
			}
		}

		//取出輔導訪談紀錄
		$query="select a.*,b.student_sn from stud_seme_talk a,stud_base b where b.student_sn in ($this->sn_str) and a.stud_id=b.stud_id order by b.student_sn,a.seme_year_seme,a.sst_date";
		$res=$CONN->Execute($query) or die("SQL錯誤： $query");
		while(!$res->EOF) {
			$current_student_sn=$res->fields[student_sn];
			$current_seme_year_seme=$res->fields[seme_year_seme];			
			$sst_id=$res->fields[sst_id];
			$row_data=$res->FetchRow();
			$this->out_arr[$current_student_sn][semester_talk][$current_seme_year_seme][$sst_id]=$row_data;
		}
	
		//取出異動紀錄 (聯集stud_move & stud_move_import)
		$query="(select * from stud_move_import where student_sn in ($this->sn_str)) UNION DISTINCT (select * from stud_move where student_sn in ($this->sn_str)) order by student_sn,move_date";
		$res=$CONN->Execute($query) or die("SQL錯誤： $query");
		while(!$res->EOF) {
			$current_student_sn=$res->fields[student_sn];
			$move_id=$res->fields[move_id];
			$move_kind=$res->fields[move_kind];
			$row_data=$res->FetchRow();
			
			//轉換異動類別為文字
			$row_data[move_kind]=$study_cond_arr[$move_kind];
			$this->out_arr[$current_student_sn][stud_move][$move_id]=$row_data;			
		}
		
		//取出學習領域文字敘述
		$query="SELECT a.seme_year_seme, a.ss_id, a.ss_score, a.ss_score_memo,student_sn,a.student_sn,b.subject_id, b.ss_id, b.link_ss
FROM stud_seme_score a, score_ss b
WHERE a.student_sn in ($this->sn_str) AND a.ss_id = b.ss_id AND a.ss_score IS  NOT  NULL  AND b.enable =  '1'
ORDER  BY b.year, b.semester, b.class_year, b.sort";
		$res_score_memo=$CONN->Execute($query) or die("SQL錯誤： $query");
		
		$link_ss=array("語文"=>"language","語文-本國語文"=>"chinese","語文-本土語言"=>"local","語文-英語"=>"english","數學"=>"math","生活"=>"life","自然與生活科技"=>"nature","社會"=>"social","藝術與人文"=>"art","健康與體育"=>"health","綜合活動"=>"complex","彈性課程"=>"elasticity");
		while(!$res_score_memo->EOF) {
			$current_student_sn=$res_score_memo->fields['student_sn'];
			$current_seme=$res_score_memo->fields['seme_year_seme'];
			$current_area=$res_score_memo->fields['link_ss'];
			$current_area=$link_ss[$current_area];
			if($current_area=='elasticity'){  //彈性課程
				$subject_id=$res_score_memo->fields['subject_id'];
				$this->out_arr[$current_student_sn][semester_score_memo][$current_seme][elasticity][$subject_id][subject_name]=$subject_name_arr[$subject_id][subject_name];
				$this->out_arr[$current_student_sn][semester_score_memo][$current_seme][elasticity][$subject_id][score]=$res_score_memo->fields['ss_score'];
			} else {
				$this->out_arr[$current_student_sn][semester_score_memo][$current_seme][$current_area].= $this->zip->change_str($res_score_memo->fields['ss_score_memo'],1,0);
			}
			$res_score_memo->MoveNext();
		}
		
		//取出本校就學學期與成績資料
		$query="select seme_year_seme,left(seme_year_seme,3) as year,right(seme_year_seme,1) as semester,left(seme_class,1) as study_year,right(seme_class,2) as study_class,seme_class_name,seme_num,student_sn from stud_seme where student_sn in ($this->sn_str) order by student_sn,seme_year_seme";
		$res=$CONN->Execute($query) or die("SQL錯誤： $query");
		while(!$res->EOF) {
			$current_student_sn=$res->fields[student_sn];
			$current_seme_year_seme=$res->fields[seme_year_seme];
			$row_data=$res->FetchRow();
			$this->out_arr[$current_student_sn][semester][$current_seme_year_seme]=$row_data;
			//加入班級導師姓名
			$class_id=sprintf("%03d_%d_%02d_%02d",$row_data['year'],$row_data['semester'],$row_data['study_year'],$row_data['study_class']);
			$this->out_arr[$current_student_sn][semester][$current_seme_year_seme][teacher]=$class_teacher_name[$class_id];
			
			//抓取此生學期成績資料
			$query="select distinct seme_year_seme from stud_seme_score where student_sn=$current_student_sn order by seme_year_seme";
			$res_score=$CONN->Execute($query) or die("SQL錯誤： $query");
			while(!$res_score->EOF) {
				 $score_semester_list_arr[]=$res_score->fields['seme_year_seme'];
				 $res_score->MoveNext();
			}
			$current_student_score=cal_fin_score(array($current_student_sn),$score_semester_list_arr,"","",2);
			$this->out_arr[$current_student_sn][semester_score]=$current_student_score[$current_student_sn];
		}
		
		//取出他校就學學期與成績資料

		$query="select seme_year_seme,left(seme_year_seme,3) as year,right(seme_year_seme,1) as semester,seme_class_grade as study_year,seme_class_name,seme_num,student_sn,teacher_name from stud_seme_import where student_sn in ($this->sn_str) order by student_sn,seme_year_seme";
		$res=$CONN->Execute($query) or die("SQL錯誤： $query");
		while(!$res->EOF) {
			$current_student_sn=$res->fields[student_sn];
			$current_seme_year_seme=$res->fields[seme_year_seme];
			$row_data=$res->FetchRow();
			$this->out_arr[$current_student_sn][semester][$current_seme_year_seme]=$row_data;
			//加入班級導師姓名
			//$class_id=sprintf("%03d_%d_%02d_%02d",$row_data['year'],$row_data['semester'],$row_data['study_year'],$row_data['study_class']);
			$this->out_arr[$current_student_sn][semester][$current_seme_year_seme][teacher]=$res->fields[teacher_name];
			//就讀班級序號 轉換成數字
			$seme_class_seme=$res->fields['seme_class_name'];
				if ($seme_class_seme>0) {
					$study_class=$seme_class_seme;
				} else {
					$study_class=array_search($res->fields['seme_class_name'],$class_name_kind);
					if (!$study_class or $study_class==0) {
						$study_class=array_search($res->fields['seme_class_name'],$class_name_kind_1);
						if (!$study_class or $study_class==0) {
							$study_class=array_search($res->fields['seme_class_name'],$class_name_kind_2);
							if (!$study_class or $study_class==0) {
								$study_class=array_search($res->fields['seme_class_name'],$class_name_kind_3);
								if (!$study_class or $study_class==0) {
									$study_class=99;
								}
							}

						}

					}
				}

			$this->out_arr[$current_student_sn][semester][$current_seme_year_seme][study_class]=$study_class;
			
			//抓取此生學期成績資料
			$query="select distinct seme_year_seme from stud_seme_score where student_sn=$current_student_sn order by seme_year_seme";
			$res_score=$CONN->Execute($query) or die("SQL錯誤： $query");
			while(!$res_score->EOF) {
				 $score_semester_list_arr[]=$res_score->fields['seme_year_seme'];
				 $res_score->MoveNext();
			}
			$current_student_score=cal_fin_score(array($current_student_sn),$score_semester_list_arr,"","",2);
			$this->out_arr[$current_student_sn][semester_score]=$current_student_score[$current_student_sn];
		}
		
		//抓取期中缺席資料

		$current_year=curr_year();
		$current_seme=curr_seme();
		$current_year_seme=sprintf("%03d%d",$current_year,$current_seme);
		$query="select a.sasn,a.year,a.semester,a.absent_kind,year(a.date) as abs_year,month(a.date) as abs_month,b.student_sn from stud_absent a,stud_base b where b.student_sn in ($this->sn_str) AND a.year=$current_year AND a.semester=$current_seme AND a.stud_id=b.stud_id order by b.student_sn,a.year,a.semester";
		$res=$CONN->Execute($query) or die("SQL錯誤： $query");
		$abs_kind_arry=stud_abs_kind();
		while(!$res->EOF) {
			$current_student_sn=$res->fields[student_sn];
			$current_sasn=$res->fields[sasn];
			$current_abs_kind=array_search($res->fields[absent_kind],$abs_kind_arry);
			$current_year_month=sprintf("%03d%d",$res->fields[abs_year],$res->fields[abs_month]);
			$this->out_arr[$current_student_sn][absent][$current_year_seme][monthly][$current_year_month][year]=$res->fields[abs_year]-1911;
			$this->out_arr[$current_student_sn][absent][$current_year_seme][monthly][$current_year_month][month]=$res->fields[abs_month];

			if($current_abs_kind>3){
				$this->out_arr[$current_student_sn][absent][$current_year_seme][summary][others]+=1;
				$this->out_arr[$current_student_sn][absent][$current_year_seme][monthly][$current_year_month][others]+=1;				
			} else {
				$this->out_arr[$current_student_sn][absent][$current_year_seme][summary][$current_abs_kind]+=1;
				$this->out_arr[$current_student_sn][absent][$current_year_seme][monthly][$current_year_month][$current_abs_kind]+=1;	
			}
			$res->MoveNext();
		}	
		
		//抓取期中獎懲資料
		$reward_array=array();
		$reward_array['1']=array('kind'=>"嘉獎",'amount'=>"1");
		$reward_array['2']=array('kind'=>"嘉獎",'amount'=>"2");
		$reward_array['3']=array('kind'=>"小功\",'amount'=>"1");
		$reward_array['4']=array('kind'=>"小功\",'amount'=>"2");
		$reward_array['5']=array('kind'=>"大功\",'amount'=>"1");
		$reward_array['6']=array('kind'=>"大功\",'amount'=>"2");
		$reward_array['7']=array('kind'=>"大功\",'amount'=>"3");
		$reward_array['-1']=array('kind'=>"警告",'amount'=>"1");
		$reward_array['-2']=array('kind'=>"警告",'amount'=>"2");
		$reward_array['-3']=array('kind'=>"小過",'amount'=>"1");
		$reward_array['-4']=array('kind'=>"小過",'amount'=>"2");
		$reward_array['-5']=array('kind'=>"大過",'amount'=>"1");
		$reward_array['-6']=array('kind'=>"大過",'amount'=>"2");
		$reward_array['-7']=array('kind'=>"大過",'amount'=>"3");
		
		$current_year_seme=sprintf("%3d%d",$current_year,$current_seme);
		$current_seme_year_seme=$current_year_seme=sprintf("%03d%d",$current_year,$current_seme);	
		$semester_limit=$all_reward?"and reward_year_seme=$current_year_seme":"";
		$query="select * from reward where student_sn in ($this->sn_str) $semester_limit and reward_cancel_date='0000-00-00' order by student_sn,reward_date";
		$res=$CONN->Execute($query) or die("SQL錯誤： $query");
		while(!$res->EOF) {
			$current_student_sn=$res->fields[student_sn];
			$reward_id=$res->fields[reward_id];
			$row_data=$res->FetchRow();
			$reward_kind=$row_data['reward_kind'];
			$row_data['kind']=$reward_array[$reward_kind][kind];
			$row_data['amount']=$reward_array[$reward_kind][amount];
			$this->out_arr[$current_student_sn][reward][$current_seme_year_seme][$reward_id]=$row_data;
		}
		

		//抓取期中成績資料
		$target_table="score_semester_".$current_year."_".$current_seme;
		
		$query="SELECT a.ss_id,a.test_sort,a.test_kind,a.score,a.student_sn,b.subject_id,b.scope_id,b.rate,b.link_ss
				FROM $target_table a, score_ss b
				WHERE a.student_sn in ($this->sn_str) AND a.ss_id=b.ss_id AND b.enable='1' AND test_kind ='定期評量'
				ORDER BY a.ss_id,a.test_sort";
		$res_scoreArr=$CONN->Execute($query) or die("SQL錯誤： $query");
		
		foreach($res_scoreArr as $res_score) {
			$current_student_sn=$res_score->fields['student_sn'];
			$current_area_chinese=$res_score->fields['link_ss'];
			$current_test_sort=$res_score->fields['test_sort'];
			$current_area=$link_ss[$current_area_chinese];
			$subject_id=$res_score->fields['subject_id']?$res_score->fields['subject_id']:$res_score->fields['scope_id'];
			if(substr($current_area_chinese,0,4)=="語文") {
				$this->out_arr[$current_student_sn][this_semester_score][$current_seme_year_seme][language][$current_test_sort][$current_area]=$res_score->fields['score'];
			} elseif($current_area=='elasticity'){  //彈性課程
				$this->out_arr[$current_student_sn][this_semester_score][$current_seme_year_seme][elasticity][$subject_id][subject_name]=$subject_name_arr[$subject_id][subject_name];
				$this->out_arr[$current_student_sn][this_semester_score][$current_seme_year_seme][elasticity][$subject_id][score]=$res_score->fields['score'];
			} else {
				$this->out_arr[$current_student_sn][this_semester_score][$current_seme_year_seme][$current_area][$current_test_sort][subjects][$subject_id][subject_name]=$subject_name_arr[$subject_id][subject_name];
				$this->out_arr[$current_student_sn][this_semester_score][$current_seme_year_seme][$current_area][$current_test_sort][subjects][$subject_id][rate]=$res_score->fields['rate'];
				$this->out_arr[$current_student_sn][this_semester_score][$current_seme_year_seme][$current_area][$current_test_sort][subjects][$subject_id][score]=$res_score->fields['score'];
				
				$this->out_arr[$current_student_sn][this_semester_score][$current_seme_year_seme][$current_area][$current_test_sort][area_score][rate]+=$res_score->fields['rate'];
				$this->out_arr[$current_student_sn][this_semester_score][$current_seme_year_seme][$current_area][$current_test_sort][area_score][score]+=$res_score->fields['rate']*$res_score->fields['score'];
				
				//計算加權平均
				$total_score=$this->out_arr[$current_student_sn][this_semester_score][$current_seme_year_seme][$current_area][$current_test_sort][area_score][score];
				$total_rate=$this->out_arr[$current_student_sn][this_semester_score][$current_seme_year_seme][$current_area][$current_test_sort][area_score][rate];
				$this->out_arr[$current_student_sn][this_semester_score][$current_seme_year_seme][$current_area][$current_test_sort][area_score][average]=$total_score/$total_rate;
			}
			
		}

		if($_POST['career']){
			$min=$IS_JHORES?7:4;
			$max=$IS_JHORES?9:6;
			//抓取個性、各項活動參照表
			$personality_items=SFS_TEXT('個性(人格特質)');
			$activity_items=SFS_TEXT('各項活動');
			//抓取生涯輔導手冊資料
			//取得我的成長故事既有資料
			$query="select student_sn,personality,interest,specialty from career_mystory where student_sn in ($this->sn_str)";
			$res=$CONN->Execute($query) or die("SQL錯誤： $query");
			while(!$res->EOF){
				$current_student_sn=$res->fields['student_sn'];
				//抓取自我認識各個項目的內容
				$personality_array=unserialize($res->fields['personality']);
				foreach($personality_array as $grade=>$grade_value){
					foreach($grade_value as $key=>$value){
						$this->out_arr[$current_student_sn]['career']['self'][$grade]['personality'][]=$personality_items[$key];
					}
				}

				$interest_array=unserialize($res->fields['interest']);
				foreach($interest_array as $grade=>$grade_value){
					foreach($grade_value as $key=>$value){
						$this->out_arr[$current_student_sn]['career']['self'][$grade]['interest'][]=$activity_items[$key];
					}
				}
				
				$specialty_array=unserialize($res->fields['specialty']);
				foreach($specialty_array as $grade=>$grade_value){
					foreach($grade_value as $key=>$value){
						$this->out_arr[$current_student_sn]['career']['self'][$grade]['specialty'][]=$activity_items[$key];
					}
				}
	
				$res->MoveNext();
			}
			
			//取得我的成長故事既有資料
			$query="select student_sn,occupation_suggestion,occupation_myown,occupation_others,occupation_weight from career_mystory where student_sn in ($this->sn_str)";
			$res=$CONN->Execute($query) or die("SQL錯誤： $query");
			while(!$res->EOF){
				$current_student_sn=$res->fields['student_sn'];
				//抓取自我認識各個項目的內容
				$suggestion_array=unserialize($res->fields['occupation_suggestion']);
				foreach($suggestion_array as $grade=>$grade_value){
					foreach($grade_value as $key=>$value){
						$this->out_arr[$current_student_sn]['career']['job'][$grade]['suggestion'][$key]=$value;
					}
				}

				$myown_array=unserialize($res->fields['occupation_myown']);
				foreach($myown_array as $grade=>$grade_value){
					foreach($grade_value as $key=>$value){
						$this->out_arr[$current_student_sn]['career']['job'][$grade]['myown'][$key]=$value;
					}
				}
		
				$others_array=unserialize($res->fields['occupation_others']);
				foreach($myown_array as $grade=>$grade_value){
						$this->out_arr[$current_student_sn]['career']['job'][$grade]['others']=$grade_value;
				}
					
				//抓取選擇職業時重視的條件參照表
				$weight_items=SFS_TEXT('選擇職業時重視的條件');
				$weight_array=unserialize($res->fields['occupation_weight']);

				foreach($weight_array as $grade=>$grade_value){
					foreach($grade_value as $key=>$value){
						$this->out_arr[$current_student_sn]['career']['job'][$grade]['weight'][$key]=$weight_items[$key];
					}
				}

				$res->MoveNext();
			}
			
			//各項心理測驗
			$menu_arr=array(1=>'性向測驗',2=>'興趣測驗',3=>'其他測驗');
			//取得心理測驗既有資料
			$query="select * from career_test where student_sn in ($this->sn_str)";
			$res=$CONN->Execute($query) or die("SQL錯誤： $query");
			while(!$res->EOF){
				$current_student_sn=$res->fields['student_sn'];
				$id=$res->fields['id'];
				$sn=$res->fields['sn'];
				$content=unserialize($res->fields['content']);
				$title=$content['title'];
				$test_result=$content['data'];
				$study=$res->fields['study'];
				$job=$res->fields['job'];
				
				$this->out_arr[$current_student_sn]['career']['psy'][$sn]['id']=$menu_arr[$id];
				$this->out_arr[$current_student_sn]['career']['psy'][$sn]['title']=$title;
				$this->out_arr[$current_student_sn]['career']['psy'][$sn]['study']=$study;
				$this->out_arr[$current_student_sn]['career']['psy'][$sn]['job']=$job;
				foreach($test_result as $key=>$value) $this->out_arr[$current_student_sn]['career']['psy'][$sn]['item'][$key]=$value;

				$res->MoveNext();
			}

			//抓取自我省思
			$query="select * from career_self_ponder where student_sn in ($this->sn_str)";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			while(!$res->EOF){
				$current_student_sn=$res->fields['student_sn'];
				$id=explode('-',$res->fields['id']);
				$main=$id[0];
				$sub=$id[1];
				
				$contents=unserialize($res->fields['content']);
				foreach($contents as $key=>$value){
					$id=explode('-',$key);
					$grade=$id[0];
					$semester=$id[1];
					$this->out_arr[$current_student_sn]['career']['ponder'][$main][$sub][$grade][$semester]=$value;
				}
				$res->MoveNext();
			}
			
			//抓取教育會考成績資料
			$subject_arr=array('w','c','e','m','n','s');
			$query="select * from career_exam where student_sn in ($this->sn_str)";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			while(!$res->EOF){
				$current_student_sn=$res->fields['student_sn'];
				foreach($subject_arr as $key=>$value) $this->out_arr[$current_student_sn]['career']['exam'][$value]=$res->fields[$value];			
				$res->MoveNext();
			}
			

			//抓取體適能檢測資料
			$item_arr=array('age','test_y','test_m','up_date','test1','test2','test3','test4','prec1','prec2','prec3','prec4','tall','weigh','bmt','prec_t','prec_w','prec_b','reward','organization');
			$query="select * from fitness_data where student_sn in ($this->sn_str) and test_y>0 and test_m>0 order by c_curr_seme";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			while(!$res->EOF){
				$current_student_sn=$res->fields['student_sn'];
				$id=$res->fields['id'];
				foreach($item_arr as $key=>$value) $this->out_arr[$current_student_sn]['career']['fitness'][$id][$value]=$res->fields[$value];
				$res->MoveNext();
			}
			
			//抓取就學學期資料
			$stud_seme_arr=array();
			$table=array('stud_seme_import','stud_seme');
			foreach($table as $key=>$value){
				$query="select * from $value where student_sn in ($this->sn_str) order by student_sn,seme_year_seme";
				$res=$CONN->Execute($query) or die("SQL錯誤： $query");
				while(!$res->EOF){
					$current_student_sn=$res->fields['student_sn'];
					$stud_grade=substr($res->fields['seme_class'],0,-2);
					$year_seme=$res->fields['seme_year_seme'];
					$semester=substr($year_seme,-1);	
					$stud_seme_arr[$current_student_sn][$year_seme]['grade']=$stud_grade;
					$res->MoveNext();
				}
			}
			
			//幹部資料已經寫在career_self_ponder內  前面已經叫出了!
			//社團資料
			$item_arr=array('score','association_name','description','stud_post','stud_feedback');
			$query="select * from association where student_sn in ($this->sn_str) order by seme_year_seme";
			$res=$CONN->Execute($query) or die("SQL錯誤： $query");
			while(!$res->EOF){
				$sn=$res->fields['sn'];
				$current_student_sn=$res->fields['student_sn'];
				$seme_year_seme=$res->fields['seme_year_seme'];
				$this->out_arr[$current_student_sn]['career']['association'][$sn]['grade']=$stud_seme_arr[$current_student_sn][$seme_year_seme]['grade'];
				$this->out_arr[$current_student_sn]['career']['association'][$sn]['semester']=substr($seme_year_seme,-1);
				foreach($item_arr as $key=>$value) $this->out_arr[$current_student_sn]['career']['association'][$sn][$value]=$res->fields[$value];				

				$res->MoveNext();
			}
			
			
			//各項競賽成果
			$level_array=array(1=>'國際',2=>'全國、臺灣區',3=>'區域性（跨縣市）',4=>'省、直轄市',5=>'縣市區（鄉鎮）',6=>'校內');
			$squad_array=array(1=>'個人賽',2=>'團體賽');
			$item_arr=array('name','rank','certificate_date','sponsor','memo');
			
			$query="select * from career_race where student_sn in ($this->sn_str) order by certificate_date";
			$res=$CONN->Execute($query) or die("SQL錯誤： $query");
			while(!$res->EOF){
				$sn=$res->fields['sn'];
				$current_student_sn=$res->fields['student_sn'];
				foreach($item_arr as $key=>$value) $this->out_arr[$current_student_sn]['career']['race'][$sn][$value]=$res->fields[$value];
				$this->out_arr[$current_student_sn]['career']['race'][$sn]['level']=$level_array[$res->fields['level']];
				$this->out_arr[$current_student_sn]['career']['race'][$sn]['squad']=$squad_array[$res->fields['squad']];				
				
				$res->MoveNext();
			}
			
			//獎懲紀錄
			$seme_reward=array();
			$sql="SELECT * FROM reward WHERE student_sn in ($this->sn_str) ORDER BY reward_year_seme,reward_date";
			$res=$CONN->Execute($sql) or die("SQL錯誤： $query");
			while(!$res->EOF)
			{
				$sn=$res->fields['sn'];
				$current_student_sn=$res->fields['student_sn'];
				$reward_kind=$res->fields['reward_kind'];
				$reward_cancel_date=$res->fields['reward_cancel_date'];
				//學期統計
				$reward_year_seme=$res->fields['reward_year_seme'];
				$semester=substr($reward_year_seme,-1);
				$stud_grade=$stud_seme_arr[$current_student_sn][$seme_year_seme]['grade'];
				switch($reward_kind){
					case 1:	$this->out_arr[$current_student_sn]['career']['reward_effective'][$stud_grade][$semester][1]++;	break;
					case 2:	$this->out_arr[$current_student_sn]['career']['reward_effective'][$stud_grade][$semester][1]+=2; break;
					case 3:	$this->out_arr[$current_student_sn]['career']['reward_effective'][$stud_grade][$semester][3]++;	break;
					case 4:	$this->out_arr[$current_student_sn]['career']['reward_effective'][$stud_grade][$semester][3]+=2; break;
					case 5:	$this->out_arr[$current_student_sn]['career']['reward_effective'][$stud_grade][$semester][9]++;	break;
					case 6:	$this->out_arr[$current_student_sn]['career']['reward_effective'][$stud_grade][$semester][9]+=2; break;
					case 7:	$this->out_arr[$current_student_sn]['career']['reward_effective'][$stud_grade][$semester][9]+=3; break;
					case -1: $this->out_arr[$current_student_sn]['career']['reward_effective'][$stud_grade][$semester]['a']++; break;
					case -2: $this->out_arr[$current_student_sn]['career']['reward_effective'][$stud_grade][$semester]['a']+=2; break;
					case -3: $this->out_arr[$current_student_sn]['career']['reward_effective'][$stud_grade][$semester]['b']++; break;
					case -4: $this->out_arr[$current_student_sn]['career']['reward_effective'][$stud_grade][$semester]['b']+=2; break;
					case -5: $this->out_arr[$current_student_sn]['career']['reward_effective'][$stud_grade][$semester]['c']++; break;
					case -6: $this->out_arr[$current_student_sn]['career']['reward_effective'][$stud_grade][$semester]['c']+=2; break;
					case -7: $this->out_arr[$current_student_sn]['career']['reward_effective'][$stud_grade][$semester]['c']+=3; break;
					/*
					case 1:	$seme_reward_effective[$current_student_sn][$seme_key][1]++;	$seme_reward_effective[$current_student_sn]['sum'][1]++;	break;
					case 2:	$seme_reward_effective[$current_student_sn][$seme_key][1]+=2;	$seme_reward_effective[$current_student_sn]['sum'][1]+=2; break;
					case 3:	$seme_reward_effective[$current_student_sn][$seme_key][3]++;	$seme_reward_effective[$current_student_sn]['sum'][3]++;	break;
					case 4:	$seme_reward_effective[$current_student_sn][$seme_key][3]+=2;	$seme_reward_effective[$current_student_sn]['sum'][3]+=2; break;
					case 5:	$seme_reward_effective[$current_student_sn][$seme_key][9]++;	$seme_reward_effective[$current_student_sn]['sum'][9]++;	break;
					case 6:	$seme_reward_effective[$current_student_sn][$seme_key][9]+=2;	$seme_reward_effective[$current_student_sn]['sum'][9]+=2; break;
					case 7:	$seme_reward_effective[$current_student_sn][$seme_key][9]+=3;	$seme_reward_effective[$current_student_sn]['sum'][9]+=3; break;
					case -1: $seme_reward_effective[$current_student_sn][$seme_key][-1]++;	$seme_reward_effective[$current_student_sn]['sum'][-1]++; break;
					case -2: $seme_reward_effective[$current_student_sn][$seme_key][-1]+=2;	$seme_reward_effective[$current_student_sn]['sum'][-1]+=2; break;
					case -3: $seme_reward_effective[$current_student_sn][$seme_key][-3]++;	$seme_reward_effective[$current_student_sn]['sum'][-3]++; break;
					case -4: $seme_reward_effective[$current_student_sn][$seme_key][-3]+=2;	$seme_reward_effective[$current_student_sn]['sum'][-3]+=2; break;
					case -5: $seme_reward_effective[$current_student_sn][$seme_key][-9]++;	$seme_reward_effective[$current_student_sn]['sum'][-9]++; break;
					case -6: $seme_reward_effective[$current_student_sn][$seme_key][-9]+=2;	$seme_reward_effective[$current_student_sn]['sum'][-9]+=2; break;
					case -7: $seme_reward_effective[$current_student_sn][$seme_key][-9]+=3;	$seme_reward_effective[$current_student_sn]['sum'][-9]+=3; break;
					*/
				}
				//銷過統計
				if($reward_cancel_date<>'0000-00-00'){
					switch($reward_kind){
						case -1: $this->out_arr[$current_student_sn]['career']['reward_canceled'][$stud_grade][$semester]['a']++; break;
						case -2: $this->out_arr[$current_student_sn]['career']['reward_canceled'][$stud_grade][$semester]['a']+=2; break;
						case -3: $this->out_arr[$current_student_sn]['career']['reward_canceled'][$stud_grade][$semester]['b']++; break;
						case -4: $this->out_arr[$current_student_sn]['career']['reward_canceled'][$stud_grade][$semester]['b']+=2; break;
						case -5: $this->out_arr[$current_student_sn]['career']['reward_canceled'][$stud_grade][$semester]['c']++; break;
						case -6: $this->out_arr[$current_student_sn]['career']['reward_canceled'][$stud_grade][$semester]['c']+=2; break;
						case -7: $this->out_arr[$current_student_sn]['career']['reward_canceled'][$stud_grade][$semester]['c']+=3; break;
						/*
						case -1: $seme_reward_canceled[$current_student_sn][$seme_key][-1]++; $seme_reward_canceled[$current_student_sn]['sum'][-1]++; break;
						case -2: $seme_reward_canceled[$seme_key][-1]+=2; $seme_reward_canceled[$current_student_sn]['sum'][-1]+=2; break;
						case -3: $seme_reward_canceled[$current_student_sn][$seme_key][-3]++; $seme_reward_canceled[$current_student_sn]['sum'][-3]++; break;
						case -4: $seme_reward_canceled[$current_student_sn][$seme_key][-3]+=2; $seme_reward_canceled[$current_student_sn]['sum'][-3]+=2; break;
						case -5: $seme_reward_canceled[$current_student_sn][$seme_key][-9]++; $seme_reward_canceled[$current_student_sn]['sum'][-9]++; break;
						case -6: $seme_reward_canceled[$current_student_sn][$seme_key][-9]+=2; $seme_reward_canceled[$current_student_sn]['sum'][-9]+=2; break;
						case -7: $seme_reward_canceled[$current_student_sn][$seme_key][-9]+=3; $seme_reward_canceled[$current_student_sn]['sum'][-9]+=3; break;
						*/
					}
				}			
				$res->MoveNext();
			}
			
			//服務學習
			$room_arr=room_kind();
			$item_arr=array('minutes','bonus','studmemo','feedback','year_seme','service_date','item','memo','sponsor');
			$query="select a.*,b.* from stud_service_detail a inner join stud_service b on a.item_sn=b.sn where confirm=1 and a.student_sn in ($this->sn_str) order by year_seme";
			$res=$CONN->Execute($query) or die("SQL錯誤： $query");
			while(!$res->EOF){
				$recno++;
				$current_student_sn=$res->fields['student_sn'];
				$year_seme=$res->fields['year_seme'];
				$semester=substr($reward_year_seme,-1);
				$grade=$stud_seme_arr[$current_student_sn][$year_seme]['grade'];
				$this->out_arr[$current_student_sn]['career']['service'][$recno]['grade']=$grade;
				$this->out_arr[$current_student_sn]['career']['service'][$recno]['semester']=$semester;
				foreach($item_arr as $key=>$value) $this->out_arr[$current_student_sn]['career']['service'][$recno][$value]=$res->fields[$value];
				$this->out_arr[$current_student_sn]['career']['service'][$recno]['department']=$room_arr[$res->fields['department']];
				$this->out_arr[$current_student_sn]['career']['service'][$recno]['hours']=$this->out_arr[$current_student_sn]['career']['service'][$recno]['minutes']/60;
			
				$res->MoveNext();
			}
		
			//抓取個性、各項活動參照表
			$course_array=SFS_TEXT('生涯試探學程及群科');
			$activity_array=SFS_TEXT('生涯試探活動方式');
			
			$query="select * from career_explore where student_sn in ($this->sn_str) order by seme_key";
			$res=$CONN->Execute($query) or die("SQL錯誤： $query");
			while(!$res->EOF){
				$sn=$res->fields['sn'];
				$current_student_sn=$res->fields['student_sn'];				
				$id=explode('-',$res->fields['seme_key']);
				$this->out_arr[$current_student_sn]['career']['explore'][$sn]['grade']=$id[0];
				$this->out_arr[$current_student_sn]['career']['explore'][$sn]['semester']=$id[1];
				$this->out_arr[$current_student_sn]['career']['explore'][$sn]['course']=$course_array[$res->fields['course_id']];
				$this->out_arr[$current_student_sn]['career']['explore'][$sn]['activity']=$activity_array[$res->fields['activity_id']];
				$this->out_arr[$current_student_sn]['career']['explore'][$sn]['degree']=$res->fields['degree'];
				$this->out_arr[$current_student_sn]['career']['explore'][$sn]['ponder']=$res->fields['self_ponder'];				
				
				$res->MoveNext();
			}
			
			//抓取生涯方向思考項目參照表
			$ponder_items=SFS_TEXT('生涯方向思考項目');
			//抓取生涯選擇方向參照表
			$direction_items=SFS_TEXT('生涯選擇方向');
			
			//取得既有資料
			$query="select * from career_view where student_sn in ($this->sn_str) order by update_time";
			$res=$CONN->Execute($query) or die("SQL錯誤： $query");
			while(!$res->EOF){
				$sn=$res->fields['sn'];
				$current_student_sn=$res->fields['student_sn'];
				$ponder_array=unserialize($res->fields['ponder']);				
				foreach($ponder_items as $key=>$value){
					$this->out_arr[$current_student_sn]['career']['think'][$value]=$ponder_array[$key];
				}
				
				$direction_array=unserialize($res->fields['direction']);
				foreach($direction_array['item'] as $key=>$value)
					foreach($value as $key2=>$value2) 
						if($key2<>'memo') $direction_array['item'][$key][$key2]=$direction_items[$value2];  //進行代碼-文字轉換
				$this->out_arr[$current_student_sn]['career']['direction']=$direction_array;		
				
				$res->MoveNext();
			}
		
			//抓取課程志願
			$item_arr=array('school','course','position','transportation','transportation_time','transportation_toll','memo');
			$query="select * from career_course where aspiration_order>0 and student_sn in ($this->sn_str) order by aspiration_order";
			$res=$CONN->Execute($query) or die("SQL錯誤： $query");
			while(!$res->EOF){
				$aspiration_order=$res->fields['aspiration_order'];
				$current_student_sn=$res->fields['student_sn'];
				foreach($item_arr as $key=>$value) $this->out_arr[$current_student_sn]['career']['aspiration'][$aspiration_order][$value]=$res->fields[$value];
				$this->out_arr[$current_student_sn]['career']['aspiration'][$aspiration_order]['factor']=unserialize($res->fields['factor']);
				$res->MoveNext();
			}
			
			//取得各項測驗最高分測驗資料
			$query="select sn,student_sn,id,highest,study update_time  from career_test where student_sn in ($this->sn_str)";
			$res=$CONN->Execute($query) or die("SQL錯誤： $query");
			while(!$res->EOF){
				$sn=$res->fields['sn'];
				$current_student_sn=$res->fields['student_sn'];
				$id=$res->fields['id'];
				$highest_arr=explode(',',$res->fields['highest']);
				foreach($highest_arr as $key=>$value) $this->out_arr[$current_student_sn]['career']['test'][$id]=$highest_arr;	
			
				$res->MoveNext();
			}

			//抓取想升讀的學校資料
			$id=0;
			$query="select student_sn,aspiration_order,school from career_course where student_sn in ($this->sn_str) order by aspiration_order";
			$res=$CONN->Execute($query) or die("SQL錯誤： $query");
			while(!$res->EOF){
				$current_student_sn=$res->fields['student_sn'];
				if(!array_search($res->fields['school'],$this->out_arr[$current_student_sn]['career']['school'])) {
					$id++;
					$this->out_arr[$current_student_sn]['career']['school'][$id]=$res->fields['school'];
				}
				$res->MoveNext();
			}
			
			//抓取生涯選擇方向參照表
			$direction_items=SFS_TEXT('生涯選擇方向');
			
			//取得師長綜合意見既有資料
			$item_arr=array('parent','tutor','guidance');
			$query="select * from career_opinion where student_sn in ($this->sn_str)";
			$res=$CONN->Execute($query);
			while(!$res->EOF){
				$sn=$res->fields['sn'];	
				$current_student_sn=$res->fields['student_sn'];				
				foreach($item_arr as $key=>$value){
					if($res->fields[$value]){  //避免空值經Explode後仍會產生空陣列元素
						$items=explode(',',$res->fields[$value]);
						foreach($items as $key2=>$value2) $this->out_arr[$current_student_sn]['career']['opinion'][$value][$key2]=$direction_items[$value2];
					}
					$memo=$value.'_memo';
					$this->out_arr[$current_student_sn]['career']['opinion'][$value]['memo']=$res->fields[$memo];
				}
				$res->MoveNext();
			}
			
			//抓取既有諮詢紀錄
			$item_arr=array('guidance_date','target','emphasis','teacher_name');
			$query="select * from career_guidance where student_sn in ($this->sn_str) order by guidance_date";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			while(!$res->EOF){
				$sn=$res->fields['sn'];
				$current_student_sn=$res->fields['student_sn'];
				foreach($item_arr as $key=>$value) $this->out_arr[$current_student_sn]['career']['guidance'][$sn][$value]=$res->fields[$value];
				$res->MoveNext();
			}
			
			$item_arr=array('consultation_date','teacher_name','emphasis','memo');
			$query="select * from career_consultation where student_sn in ($this->sn_str) order by consultation_date";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			while(!$res->EOF){
				$sn=$res->fields['sn'];
				$current_student_sn=$res->fields['student_sn'];
				foreach($item_arr as $key=>$value) $this->out_arr[$current_student_sn]['career']['consultation'][$sn][$value]=$res->fields[$value];
				$seme_key=explode('-',$res->fields['seme_key']);
				$this->out_arr[$current_student_sn]['career']['consultation'][$sn]['grade']=$seme_key[0];
				$this->out_arr[$current_student_sn]['career']['consultation'][$sn]['semester']=$seme_key[1];
				$res->MoveNext();
			}
			
			$items_ref=array(1=>'我的成長故事',2=>'各項心理測驗',3=>'學習成果及特殊表現',4=>'生涯輔導紀錄',5=>'生涯統整面面觀',6=>'生涯發展規劃書');
			$item_arr=array('suggestion','suggestion_date','tutor_confirm ','tutor_name'); 
			$query="select * from career_parent where student_sn in ($this->sn_str) order by suggestion_date";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			while(!$res->EOF){
				$sn=$res->fields['sn'];
				$current_student_sn=$res->fields['student_sn'];
				foreach($item_arr as $key=>$value) $this->out_arr[$current_student_sn]['career']['parent'][$sn][$value]=$res->fields[$value];
				$seme_key=explode('-',$res->fields['seme_key']);
				$this->out_arr[$current_student_sn]['career']['parent'][$sn]['grade']=$seme_key[0];
				$this->out_arr[$current_student_sn]['career']['parent'][$sn]['semester']=$seme_key[1];
				$items=unserialize($res->fields['items']);
				foreach($items as $key=>$value) $this->out_arr[$current_student_sn]['career']['parent'][$sn]['consult'][$key]=$items_ref[$key];

				$res->MoveNext();
			}




/*
echo '<pre>';
print_r($this->out_arr[$current_student_sn]['career']['ponder']);
echo '</pre>';
exit;
*/

		}		
		
	}
}
?>