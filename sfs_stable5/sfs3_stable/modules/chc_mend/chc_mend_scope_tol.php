<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
include "config.php";

//認證
sfs_check();
//程式使用的Smarty樣本檔
//建立物件
$obj= new basic_chc($CONN,$smarty,$IS_JHORES);
//初始化
//$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("12basic_chc模組");之前
$obj->process();
//秀出網頁布景標頭
head("補考成績管理");
//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p,$obj->linkstr);//
// print_menu($school_menu_p);//,$obj->linkstr
$obj->display();
//佈景結尾
foot();
//物件class
class basic_chc{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=10;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數
//	var $scope=array(1=>'語文',2=>'數學',3=>'自然與生活科技',4=>'社會',
//	5=>'健康與體育',6=>'藝術與人文',7=>'綜合活動',8=>'全部領域');
	var $scope=array(8=>'全部領域');
	var $scope2=array(1=>'語文',2=>'數學',3=>'自然與生活科技',4=>'社會',
	5=>'健康與體育',6=>'藝術與人文',7=>'綜合活動');
	var $scopefailnum=array(1=>'一個領域以上不及格',2=>'二個領域以上不及格',3=>'三個領域以上不及格',4=>'四個領域以上不及格',
	5=>'五個領域以上不及格',6=>'六個領域以上不及格',7=>'七個領域以上不及格',8=>'全部領域不及格'); 
    var $Semesfailnum=array(1=>'一個學期成績列表',2=>'二個學期成績列表',3=>'三個學期成績列表',4=>'四個學期成績列表',5=>'五個學期成績列表',6=>'六個學期成績列表');
	var $all_seme_array_smarty=array();
	var $linkstr;
	//建構函式
	function basic_chc($CONN,$smarty,$IS_JHORES){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
		$this->IS_JHORES=$IS_JHORES;
	}
	//初始化
	function init() {
		//過濾字串及決定GET或POST變數
		$Y=gVar('Y');
		//$G=gVar('G');$S=gVar('S');$Sfailnum=gVar('Sfailnum');$Semesnum=gVar('Semesnum');		
		//學年度格式 92_2,或102_1
		if (preg_match("/^[0-9]{2,3}_[1-2]$/",$Y)) $this->Y=$Y;		
		//年級格式..1-6小學,7-9國中
//		if (preg_match("/^[1-9]$/",$G)) $this->G=$G;		
		//領域代碼1-7,8表示全部領域
//		if (preg_match("/^[1-8]$/",$S)) $this->S=$S;
		//不及格領域數代碼1-7,8表示全部領域
//		if (preg_match("/^[1-8]$/",$Sfailnum)) $this->Sfailnum=$Sfailnum;		
		//不及格學期數代碼1-6
//	    if (preg_match("/^[1-6]$/",$Semesnum)) $this->Semesnum=$Semesnum;			
		//學年度選單
		$this->sel_year=sel_year('Y',$this->Y);
		//年級選單
//		$this->sel_grade=sel_grade('G',$this->G,$_SERVER['PHP_SELF'].'?Y='.$this->Y.'&G=');
		//頁數
		//$this->page=($_GET[page]=='') ? 0:$_GET[page];
		//其他分頁連結參數
//		$this->linkstr="Y={$this->Y}&G={$this->G}&S={$this->S}&Sfailnum={$this->Sfailnum}&Semesnum={$this->Semesnum}";
		$this->linkstr="Y={$this->Y}";
	}
		//程序
	function process() {
		$this->init();
		$this->all();
	}
	//顯示
	function display(){
		$temp2 = dirname (__file__)."/templates/chc_mend_scope_tol.htm";
        ($this->S == "8") ? $tpl = $temp2 : $tpl = $temp2;
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){
        //$this->Y 所選的學期 103_2
		if ($this->Y=='') return;
		//$this->G 所選的年級 國中 7 8 9
		//$this->S 全部領域 8個領域
		//$this->Sfailnum 不及格領域數
		//$this->Semesnum 學期數
	    //$test=$this->chc_mend_score_tol($this->Y,$this_G,$this_S,$this_Sfailnum,$this_Semesnum); 
	    $ys=explode("_",$this->Y);
		$sel_year=$ys[0];
		$sel_seme=$ys[1];
          if ($sel_seme==1) {
		     $test[7]=$this->chc_mend_score_tol($this->Y,7,8,1,1);
	         $test[8]=$this->chc_mend_score_tol($this->Y,8,8,1,3);
	         $test[9]=$this->chc_mend_score_tol($this->Y,9,8,1,5); 
		  } else {
		     $test[7]=$this->chc_mend_score_tol($this->Y,7,8,1,2);
	         $test[8]=$this->chc_mend_score_tol($this->Y,8,8,1,4);
	         $test[9]=$this->chc_mend_score_tol($this->Y,9,8,1,6);
	         $test[seven]=$this->chc_mend_score_tol($this->Y,7,8,1,2);
	         $test[eight]=$this->chc_mend_score_tol($this->Y,8,8,1,2);
	         $test[nine]=$this->chc_mend_score_tol($this->Y,9,8,1,2);  
		  }
      $this->stu_data=$test;	    

	}	
	
	function cal_fin_score($student_sn=array(),$seme=array(),$succ="",$strs="",$precision=1)   //$succ:需合格領域數 $strs:等第評斷代換字串
   {

	//取出學期初設定中  畢業成績計算方式  0:算數平均   1:加權平均(學分概念加權)

	global $CONN;
	if (count($seme)==0) return;
	$SQL="select * from pro_module where pm_name='every_year_setup' AND pm_item='FIN_SCORE_RATE_MODE'";
        $RES=$CONN->Execute($SQL);
        $FIN_SCORE_RATE_MODE=INTVAL($RES->fields['pm_value']);

	$sslk=array("語文-本國語文"=>"chinese","語文-鄉土語文"=>"local","語文-英語"=>"english","健康與體育"=>"health","生活"=>"life","社會"=>"social","藝術與人文"=>"art","自然與生活科技"=>"nature","數學"=>"math","綜合活動"=>"complex");
	if (count($student_sn)>0 && count($seme)>0) {
		$all_sn="'".implode("','",$student_sn)."'";
		$all_seme="'".implode("','",$seme)."'";
		//取得科目成績
		$query="select a.*,b.link_ss,b.rate from stud_seme_score a left join score_ss b on a.ss_id=b.ss_id where a.student_sn in ($all_sn) and a.seme_year_seme in ($all_seme) and b.enable='1' and b.need_exam='1'";
		// 若彰化縣..則修正資料庫語法,加入針對SS_ID的年級作檢查,是否與學生所在年級相符
/*		$sch=get_school_base();
		if($sch[sch_sheng]=='彰化縣'){
			$query="select a.*,b.link_ss,b.rate,b.class_year ,b.year as chc_year,b.semester as chc_semester,c.seme_class as chc_seme_class from stud_seme_score a left join score_ss b on a.ss_id=b.ss_id left join stud_seme as c on (a.seme_year_seme=c.seme_year_seme and a.student_sn =c.student_sn) where a.student_sn in ($all_sn) and a.seme_year_seme in ($all_seme) and b.enable='1' and b.need_exam='1' and (b.class_year=left(c.seme_class,1))";
		}
*/		
		$res=$CONN->Execute($query);
		//取得各學期領域學科成績.加權數並加總
		while(!$res->EOF) {
			//取得領域加權總分
			$subj_score[$res->fields[student_sn]][$res->fields[link_ss]][$res->fields[seme_year_seme]]+=$res->fields[ss_score]*$res->fields[rate];
			//領域總加權數
			$rate[$res->fields[student_sn]][$res->fields[link_ss]][$res->fields[seme_year_seme]]+=$res->fields[rate];
			$res->MoveNext();
		}
		//處理各學期領域平均
		$IS5=false;
		$IS7=false;
		while(list($sn,$v)=each($subj_score)) {
			$sys=array();
			reset($v);
			while(list($link_ss,$vv)=each($v)) {
				reset($vv);
				$ls=$sslk[$link_ss];
				if($ls){  //學期成績計算排除九年一貫對應為"非預設領域科目"與"彈性課程"(非五大或七大領域) 的成績 
					if($ls=="life") $IS5=true;
					if($ls=="art") $IS7=true;
					//計算各領域學期成績
					while(list($seme_year_seme,$s)=each($vv)) {
						$fin_score[$sn][$ls][$seme_year_seme][score]=number_format($s/$rate[$sn][$link_ss][$seme_year_seme],$precision);
						$fin_score[$sn][$ls][$seme_year_seme][rate]=$rate[$sn][$link_ss][$seme_year_seme];
						//$FIN_SCORE_RATE_MODE=1為加權平均  0為算數平均   假設畢業總平均加權數來自原始科目加權數   須注意各學期加權是否合理  比如  前一學期以100 200  500 設定   但次一學期以節數 2  3 6  設定  如此會造成單一學期的該領域成績比重失衡問題
						if($FIN_SCORE_RATE_MODE=='1') {
							//領域畢業總成績
							$fin_score[$sn][$ls][total][score]+=$fin_score[$sn][$ls][$seme_year_seme][score]*$rate[$sn][$link_ss][$seme_year_seme];
							//領域畢業總平均
							$fin_score[$sn][$ls][total][rate]+=$rate[$sn][$link_ss][$seme_year_seme];
						} else {
							$fin_score[$sn][$ls][total][score]+=$fin_score[$sn][$ls][$seme_year_seme][score];
							$fin_score[$sn][$ls][total][rate]+=1;
						}
						//當學期學期總平均處理
						if ($ls=="chinese" || $ls=="local" || $ls=="english") {
							//語文領域特別處理部份
							if ($sys[$seme_year_seme]!=1) $sys[$seme_year_seme]=1;
							$fin_score[$sn][language][$seme_year_seme][score]+=$fin_score[$sn][$ls][$seme_year_seme][score]*$fin_score[$sn][$ls][$seme_year_seme][rate];
							$fin_score[$sn][language][$seme_year_seme][rate]+=$fin_score[$sn][$ls][$seme_year_seme][rate];
						} else {
							if($FIN_SCORE_RATE_MODE=='1') {
								$fin_score[$sn][$seme_year_seme][total][score]+=$fin_score[$sn][$ls][$seme_year_seme][score]*$rate[$sn][$link_ss][$seme_year_seme];
								$fin_score[$sn][$seme_year_seme][total][rate]+=$rate[$sn][$link_ss][$seme_year_seme];
							} else {
								$fin_score[$sn][$seme_year_seme][total][score]+=$fin_score[$sn][$ls][$seme_year_seme][score];
								$fin_score[$sn][$seme_year_seme][total][rate]+=1;
							}
						}
					}
				}
				$fin_score[$sn][$ls][avg][score]=number_format($fin_score[$sn][$ls][total][score]/$fin_score[$sn][$ls][total][rate],$precision);
				//除 本國語文  鄉土語言  英語  和 彈性課程 外   將其他領域平均成績加入"畢業"總成績
				if ($ls!="chinese" && $ls!="local" && $ls!="english" && $ls!="") {
					if($FIN_SCORE_RATE_MODE=='1') {
						$fin_score[$sn][total][score]+=$fin_score[$sn][$ls][total][score];
						$fin_score[$sn][total][rate]+=$fin_score[$sn][$ls][total][rate];
					} else {
						$fin_score[$sn][total][score]+=$fin_score[$sn][$ls][avg][score];
						$fin_score[$sn][total][rate]+=1;
//echo $sn."---".$fin_score[$sn][total][score]." --- ".$fin_score[$sn][$ls][avg][score]."---".$fin_score[$sn][total][rate]."<BR>";
					}
					//判斷及格領域數
					if ($fin_score[$sn][$ls][avg][score] >= 60) $fin_score[$sn][succ]++;
				}
			}
			//生活領域成績特別處理
			if($IS5 && $IS7) {
				$fin_score[$sn][art][total][score]+=$fin_score[$sn][life][avg][score]*$fin_score[$sn][life][total][rate]/3;
				$fin_score[$sn][nature][total][score]+=$fin_score[$sn][life][avg][score]*$fin_score[$sn][life][total][rate]/3;
				$fin_score[$sn][social][total][score]+=$fin_score[$sn][life][avg][score]*$fin_score[$sn][life][total][rate]/3;
				$fin_score[$sn][art][total][rate]+=$fin_score[$sn][life][total][rate]/3;
				$fin_score[$sn][nature][total][rate]+=$fin_score[$sn][life][total][rate]/3;
				$fin_score[$sn][social][total][rate]+=$fin_score[$sn][life][total][rate]/3;
				$fin_score[$sn][art][avg][score]=number_format($fin_score[$sn][art][total][score]/$fin_score[$sn][art][total][rate],$precision);
				$fin_score[$sn][nature][avg][score]=number_format($fin_score[$sn][nature][total][score]/$fin_score[$sn][nature][total][rate],$precision);
				$fin_score[$sn][social][avg][score]=number_format($fin_score[$sn][social][total][score]/$fin_score[$sn][social][total][rate],$precision);
			}
			//語文領域成績特別獨立計算
			if (count($sys)>0) {
				$r=0;
				while(list($seme_year_seme,$s)=each($sys)) {
					$fin_score[$sn][language][$seme_year_seme][score]=number_format($fin_score[$sn][language][$seme_year_seme][score]/$fin_score[$sn][language][$seme_year_seme][rate],$precision);
					if($FIN_SCORE_RATE_MODE=='1')	{
						$fin_score[$sn][language][avg][score]+=$fin_score[$sn][language][$seme_year_seme][score]*$fin_score[$sn][language][$seme_year_seme][rate];
						$fin_score[$sn][language][total][score]+=$fin_score[$sn][language][$seme_year_seme][score]*$fin_score[$sn][language][$seme_year_seme][rate];
						$fin_score[$sn][language][total][rate]+=$fin_score[$sn][language][$seme_year_seme][rate];
						$fin_score[$sn][$seme_year_seme][total][score]+=$fin_score[$sn][language][$seme_year_seme][score]*$fin_score[$sn][language][$seme_year_seme][rate];
						$r+=$fin_score[$sn][language][$seme_year_seme][rate];
		//echo $sn."---".$r."---".$fin_score[$sn][language][$seme_year_seme][rate]."---".$fin_score[$sn][language][avg][score]."<BR>";
						$fin_score[$sn][$seme_year_seme][total][rate]+=$fin_score[$sn][language][$seme_year_seme][rate];
					} else {
						$fin_score[$sn][language][avg][score]+=$fin_score[$sn][language][$seme_year_seme][score];
						$fin_score[$sn][$seme_year_seme][total][score]+=$fin_score[$sn][language][$seme_year_seme][score];
						$r+=1;
						$fin_score[$sn][$seme_year_seme][total][rate]+=1;
					}
					$fin_score[$sn][$seme_year_seme][avg][score]=number_format($fin_score[$sn][$seme_year_seme][total][score]/$fin_score[$sn][$seme_year_seme][total][rate],$precision);
				}
				$fin_score[$sn][language][avg][score]=number_format($fin_score[$sn][language][avg][score]/$r,$precision);
				if($FIN_SCORE_RATE_MODE=='1')	{
					$fin_score[$sn][total][score]+=$fin_score[$sn][language][avg][score]*$r;
					$fin_score[$sn][total][rate]+=$r;
				} else {
					$fin_score[$sn][total][score]+=$fin_score[$sn][language][avg][score];
					$fin_score[$sn][total][rate]+=1;
				}
				$fin_score[$sn][avg][score]=number_format($fin_score[$sn][total][score]/$fin_score[$sn][total][rate],$precision);
				//複製到排名陣列
				$rank_score[$sn]=$fin_score[$sn]['total']['score'];
				if ($fin_score[$sn][language][avg][score] >= 60) $fin_score[$sn][succ]++;
			}
			if ($succ) {
				if ($fin_score[$sn][succ] < $succ) $show_score[$sn]=$fin_score[$sn];
			}
      //針對最後結果做排序
			arsort($rank_score);
			//計算名次
			$rank=0;
			foreach($rank_score as $key=>$value) {
				$rank+=1;
				$fin_score[$key]['total']['rank']=$rank;
			}
		}
		if ($succ)
			return $show_score;
		else
			return $fin_score;
	} elseif (count($student_sn)==0) {
		return "沒有傳入學生流水號";
	} else {
		return "沒有傳入學期";
	}
   }
   //每一學期補考成績
   function chc_mend_one_seme_score($this_Y,$this_G,$this_S)
   {
        if ($this_Y=='') return;
		if ($this_G=='') return;
		if ($this_S=='') return;
		$ys=explode("_",$this_Y);
		$sel_year=$ys[0];
		$sel_seme=$ys[1];
		$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
		$seme_class=$this_G."%";
		$Scope=$this_S;
		//增加判斷是否為今年再籍學生，如此轉學生也會顯現
		//本學年度-選擇學年+選擇年級=目前年級，才被選取。
		//但是如此一來又會產生畢業生無法被選取的另一個問題
		//在籍生：a.stud_study_cond=0 畢業生：a.stud_study_cond=5 其中 stud_base a		
		$curr_y=curr_year();
		$sel_y=substr($this_Y,0,-2);
		$sel_g=$this_G;
		$opt=$curr_y-$sel_y+$sel_g;
		//判斷畢業與否
		if ($opt > 9) {
		 $graduated=" a.stud_study_cond=5 ";
	     $grad_con ="畢業";
	    }
		else {
		 $opt=$opt."%";	
		 $graduated=" a.stud_study_cond=0 
		AND a.curr_class_num LIKE '".$opt."' "; 	
		 $grad_con ="在學";
		} 
		//執行資料庫
		$query=
		"SELECT a.stud_id,a.stud_name,a.stud_sex,
		b.seme_class,b.seme_num,b.seme_year_seme,c.* 
		FROM stud_base a,chc_mend c LEFT JOIN stud_seme b 
		ON (c.student_sn=b.student_sn  
		AND b.seme_year_seme='$seme_year_seme'  
		AND b.seme_class like '$seme_class' )
		WHERE a.student_sn=c.student_sn 
		AND c.seme='$this_Y' 
		AND".$graduated.
	   "ORDER BY b.seme_class,b.seme_num ";
		$res=$this->CONN->Execute($query);
		$ALL=$res->GetArray();
		if ($Scope=="8") {
			$New=array();
			foreach ($ALL as $ary){
				$sn=$ary['student_sn'];//學號
				$snlist[]=$ary['student_sn'];//學號列表
				$ss=$ary['scope'];//領域
				$semes=$ary['seme'];//學期
				$score_end = $ary['score_end'];//補考完成績
	            //$New['A']為學生基本資料
				$New['A'][$sn]=$ary;
				//$New['A'][$sn]['grad_con']為學生畢業或在學狀況
				$New['A'][$sn]['grad_con']=$grad_con;
				//$New['B']為全領域成績
				$New['B'][$sn][$semes][$ss]=$ary;
				//$New['C']、$New['D']為補考後成績
				$New['C'][$sn][$semes][$ss]=$score_end;
				$New['D'][$sn][$semes][$ss]=$score_end;
//				$New['G'][$sn][$semes][$ss]=$score_end;
				//$New['C']要計算補考通過領域數total_ss_pass、$New['D']要計算補考領域數total_ss]
				$New['D'][$sn][$semes]['total_ss']= (count($New['D'][$sn][$semes])==1)?(count($New['D'][$sn][$semes])):(count($New['D'][$sn][$semes])-1);
				$New['C'][$sn][$semes]['total_ss_pass']= 0;		
                foreach ($New['C'][$sn][$semes] as $ss => $v) {
				     if ($v ==60) {
						 $New['C'][$sn][$semes]['total_ss_pass']++;
					  } 
				 }
				 //$New['E']要計算補考未通過領域數total_ss_Nopass
				 $New['E'][$sn][$semes]['total_ss']=$New['D'][$sn][$semes]['total_ss'];
				 $New['E'][$sn][$semes]['total_ss_pass']=$New['C'][$sn][$semes]['total_ss_pass'];
				 $New['E'][$sn][$semes]['total_ss_Nopass']=$New['E'][$sn][$semes]['total_ss']-$New['E'][$sn][$semes]['total_ss_pass'];
                 $New['A'][$sn]['total_ss_Nopass']=$New['E'][$sn][$semes]['total_ss_Nopass'];
			}	 			
			$snlist_uniqe = array_unique($snlist);			
			sort($snlist_uniqe);
		    $New[snlist]=$snlist_uniqe;										
	    }
     return $New;
   }
   
   //將所有學期補考成績與所有學期成績合併後,產出各領域不及格人數統計與1~7領域不及格人數統計狀況
   function chc_mend_score_tol($this_Y,$this_G,$this_S,$this_Sfailnum,$this_Semesnum) {
        //var $scope2=array(1=>'語文',2=>'數學',3=>'自然與生活科技',4=>'社會',5=>'健康與體育',6=>'藝術與人文',7=>'綜合活動');
		 $cal_fin_score_ss = array("language"=>"1","math"=>"2","nature"=>"3","social"=>"4","health"=>"5","art"=>"6","complex"=>"7");
		//$this->Y 所選的學期 103_2
		if ($this_Y=='') return;
		//$this->G 所選的年級 國中 7 8 9
		if ($this_G=='') return;
		//$this->S 全部領域 8個領域
		if ($this_S=='') return;
		//$this->Sfailnum 不及格領域數
		if ($this_Sfailnum=='') return;
		//$this->Semesnum 學期數
		if ($this_Semesnum=='') return;
		//學年學期選單取出的值ex 103_1
		//學年學期選單取出的值ex 103_1
		$ys=explode("_",$this_Y);
		$sel_year=$ys[0];
		$sel_seme=$ys[1];
		$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
		
        //要列出成績的學期數Semesnum ex:Semesnum=5 列出1011 1012 1021 1022 1031 用於mysql為$all_seme_mysql 用於smarty為$all_seme_array_smarty
        //97行~182行為上述程式碼
        if ($sel_seme==1) {
		 switch ($this_Semesnum) {
		  case 1:
		  	$all_seme_array[1] = $ys[0]."_".$ys[1];
		  	$Test[1]=$this->chc_mend_one_seme_score($all_seme_array[1],$this_G,$this_S);
			break;
		  case 2:	 
		    $all_seme_array[1] = ($ys[0]-1)."_".($ys[1]+1);
		    $all_seme_array[2] = $ys[0]."_".$ys[1];
		    $Test[1]=$this->chc_mend_one_seme_score($all_seme_array[1],$this_G-1,$this_S);
		    $Test[2]=$this->chc_mend_one_seme_score($all_seme_array[2],$this_G,$this_S);
			break;	
		  case 3:	
		    $all_seme_array[1]= ($ys[0]-1)."_".$ys[1];
            $all_seme_array[2]= ($ys[0]-1)."_".($ys[1]+1);
            $all_seme_array[3]= $ys[0]."_".$ys[1];
            $Test[1]=$this->chc_mend_one_seme_score($all_seme_array[1],$this_G-1,$this_S);
		    $Test[2]=$this->chc_mend_one_seme_score($all_seme_array[2],$this_G-1,$this_S);
		    $Test[3]=$this->chc_mend_one_seme_score($all_seme_array[3],$this_G,$this_S);
			break;
		  case 4:	
		    $all_seme_array[1]= ($ys[0]-2)."_".($ys[1]+1);
            $all_seme_array[2]= ($ys[0]-1)."_".$ys[1];
            $all_seme_array[3]= ($ys[0]-1)."_".($ys[1]+1);
            $all_seme_array[4]= $ys[0]."_".$ys[1];
            $Test[1]=$this->chc_mend_one_seme_score($all_seme_array[1],$this_G-2,$this_S);
		    $Test[2]=$this->chc_mend_one_seme_score($all_seme_array[2],$this_G-1,$this_S);
		    $Test[3]=$this->chc_mend_one_seme_score($all_seme_array[3],$this_G-1,$this_S);
		    $Test[4]=$this->chc_mend_one_seme_score($all_seme_array[4],$this_G,$this_S);
			break;		
		  case 5:	
		    $all_seme_array[1]= ($ys[0]-2)."_".($ys[1]);
            $all_seme_array[2]= ($ys[0]-2)."_".($ys[1]+1);
            $all_seme_array[3]= ($ys[0]-1)."_".$ys[1];
            $all_seme_array[4]= ($ys[0]-1)."_".($ys[1]+1);
            $all_seme_array[5]= $ys[0]."_".$ys[1];
            $Test[1]=$this->chc_mend_one_seme_score($all_seme_array[1],$this_G-2,$this_S);
		    $Test[2]=$this->chc_mend_one_seme_score($all_seme_array[2],$this_G-2,$this_S);
		    $Test[3]=$this->chc_mend_one_seme_score($all_seme_array[3],$this_G-1,$this_S);
		    $Test[4]=$this->chc_mend_one_seme_score($all_seme_array[4],$this_G-1,$this_S);
		    $Test[5]=$this->chc_mend_one_seme_score($all_seme_array[5],$this_G,$this_S);
			break;
		  case 6:	
		    $all_seme_array[1]= ($ys[0]-3)."_".($ys[1]+1);
            $all_seme_array[2]= ($ys[0]-2)."_".($ys[1]);
            $all_seme_array[3]= ($ys[0]-2)."_".($ys[1]+1);
            $all_seme_array[4]= ($ys[0]-1)."_".$ys[1];
            $all_seme_array[5]= ($ys[0]-1)."_".($ys[1]+1);
            $all_seme_array[6]= $ys[0]."_".$ys[1];
            $Test[1]=$this->chc_mend_one_seme_score($all_seme_array[1],$this_G-3,$this_S);
		    $Test[2]=$this->chc_mend_one_seme_score($all_seme_array[2],$this_G-2,$this_S);
		    $Test[3]=$this->chc_mend_one_seme_score($all_seme_array[3],$this_G-2,$this_S);
		    $Test[4]=$this->chc_mend_one_seme_score($all_seme_array[4],$this_G-1,$this_S);
		    $Test[5]=$this->chc_mend_one_seme_score($all_seme_array[5],$this_G-1,$this_S);
		    $Test[6]=$this->chc_mend_one_seme_score($all_seme_array[6],$this_G,$this_S);
			break;		
	      }
		} else {
		  switch ($this_Semesnum) {
		  case 1:
		  	$all_seme_array[1] = $ys[0]."_".$ys[1];
		  	$Test[1]=$this->chc_mend_one_seme_score($all_seme_array[1],$this_G,$this_S);
			break;
		  case 2:	
		    $all_seme_array[1] = ($ys[0])."_".($ys[1]-1);
		    $all_seme_array[2] = $ys[0]."_".$ys[1];
		    $Test[1]=$this->chc_mend_one_seme_score($all_seme_array[1],$this_G,$this_S);
		    $Test[2]=$this->chc_mend_one_seme_score($all_seme_array[2],$this_G,$this_S);
			break;	
		  case 3:	
		    $all_seme_array[1]= ($ys[0]-1)."_".$ys[1];
            $all_seme_array[2]= ($ys[0])."_".($ys[1]-1);
            $all_seme_array[3]= $ys[0]."_".$ys[1]; 
            $Test[1]=$this->chc_mend_one_seme_score($all_seme_array[1],$this_G-1,$this_S);
		    $Test[2]=$this->chc_mend_one_seme_score($all_seme_array[2],$this_G,$this_S);
		    $Test[3]=$this->chc_mend_one_seme_score($all_seme_array[3],$this_G,$this_S);
			break;
		  case 4:	
		    $all_seme_array[1]= ($ys[0]-1)."_".($ys[1]-1);
            $all_seme_array[2]= ($ys[0]-1)."_".$ys[1];
            $all_seme_array[3]= ($ys[0])."_".($ys[1]-1);
            $all_seme_array[4]= $ys[0]."_".$ys[1];
            $Test[1]=$this->chc_mend_one_seme_score($all_seme_array[1],$this_G-1,$this_S);
		    $Test[2]=$this->chc_mend_one_seme_score($all_seme_array[2],$this_G-1,$this_S);
		    $Test[3]=$this->chc_mend_one_seme_score($all_seme_array[3],$this_G,$this_S);
		    $Test[4]=$this->chc_mend_one_seme_score($all_seme_array[4],$this_G,$this_S);
			break;		
		  case 5:	
		    $all_seme_array[1]= ($ys[0]-2)."_".($ys[1]);
            $all_seme_array[2]= ($ys[0]-1)."_".($ys[1]-1);
            $all_seme_array[3]= ($ys[0]-1)."_".($ys[1]);
            $all_seme_array[4]= ($ys[0])."_".($ys[1]-1);
            $all_seme_array[5]= $ys[0]."_".$ys[1];
            $Test[1]=$this->chc_mend_one_seme_score($all_seme_array[1],$this_G-2,$this_S);
		    $Test[2]=$this->chc_mend_one_seme_score($all_seme_array[2],$this_G-1,$this_S);
		    $Test[3]=$this->chc_mend_one_seme_score($all_seme_array[3],$this_G-1,$this_S);
		    $Test[4]=$this->chc_mend_one_seme_score($all_seme_array[4],$this_G,$this_S);
		    $Test[5]=$this->chc_mend_one_seme_score($all_seme_array[5],$this_G,$this_S);
			break;
		  case 6:	
		    $all_seme_array[1]= ($ys[0]-2)."_".($ys[1]-1);
            $all_seme_array[2]= ($ys[0]-2)."_".($ys[1]);
            $all_seme_array[3]= ($ys[0]-1)."_".($ys[1]-1);
            $all_seme_array[4]= ($ys[0]-1)."_".($ys[1]);
            $all_seme_array[5]= ($ys[0])."_".($ys[1]-1);
            $all_seme_array[6]= $ys[0]."_".$ys[1];
            $Test[1]=$this->chc_mend_one_seme_score($all_seme_array[1],$this_G-2,$this_S);
		    $Test[2]=$this->chc_mend_one_seme_score($all_seme_array[2],$this_G-2,$this_S);
		    $Test[3]=$this->chc_mend_one_seme_score($all_seme_array[3],$this_G-1,$this_S);
		    $Test[4]=$this->chc_mend_one_seme_score($all_seme_array[4],$this_G-1,$this_S);
		    $Test[5]=$this->chc_mend_one_seme_score($all_seme_array[5],$this_G,$this_S);
		    $Test[6]=$this->chc_mend_one_seme_score($all_seme_array[6],$this_G,$this_S);
			break;
	      }
		}
		$this->all_seme_array_smarty=$all_seme_array;
		$all_seme_array_mysql=str_replace("_","",$all_seme_array);	
		$all_seme_array2mysql=array_combine($all_seme_array,$all_seme_array_mysql);	
		$all_student_sn=array();		
       for ($i=1;$i<=$this_Semesnum;$i++) {
			for ($j=0;$j<count($Test[$i][snlist]);$j++) {
			array_push($all_student_sn,$Test[$i][snlist][$j]);
			}					
		}		
        $all_student_sn_unique=array_unique($all_student_sn);
        sort($all_student_sn_unique);
        for ($i=1;$i<=$this_Semesnum;$i++) {
            foreach ($all_student_sn_unique as $value_sn) {
				if ($Test[$i]['A'][$value_sn]!="") {
				//$New['A']為學生基本資料
				$New['A'][$value_sn]=$Test[$i]['A'][$value_sn];
			    }
		            foreach ($all_seme_array as $value_seme){
			        	    foreach ($cal_fin_score_ss as $index_ss => $value_ss){
			        	           if ($Test[$i]['B'][$value_sn][$value_seme][$value_ss]!="") {
			        	              //$New['B']為全領域成績
				                      $New['B'][$value_sn][$value_seme][$value_ss]=$Test[$i]['B'][$value_sn][$value_seme][$value_ss];
					                }
					                if ($Test[$i]['D'][$value_sn][$value_seme][$value_ss]!="") {
			        	              //$New['E']要計算補考未通過領域數total_ss_Nopass
				                      $New['E'][$value_sn][$value_seme][$value_ss]=$Test[$i]['D'][$value_sn][$value_seme][$value_ss];
					                }					                				                
			                }
			                if ($Test[$i]['E'][$value_sn][$value_seme]['total_ss']!="") {
				                $New['E'][$value_sn][$value_seme]['total_ss']=$Test[$i]['E'][$value_sn][$value_seme]['total_ss'];
					        }
					        if ($Test[$i]['E'][$value_sn][$value_seme]['total_ss_pass']!="") {			        	          
				                $New['E'][$value_sn][$value_seme]['total_ss_pass']=$Test[$i]['E'][$value_sn][$value_seme]['total_ss_pass'];
					        }
					        if ($Test[$i]['E'][$value_sn][$value_seme]['total_ss_Nopass']!="") {			        	          
				                $New['E'][$value_sn][$value_seme]['total_ss_Nopass']=$Test[$i]['E'][$value_sn][$value_seme]['total_ss_Nopass'];
					        }
					        if ($Test[$i]['E'][$value_sn][$value_seme]['total_ss']!=""&&$Test[$i]['E'][$value_sn][$value_seme]['total_ss_Nopass']!="") {			        	          
				                $New['E'][$value_sn][$value_seme]['total_ss_pass']=$New['E'][$value_sn][$value_seme]['total_ss']-$New['E'][$value_sn][$value_seme]['total_ss_Nopass'];
					        }					       
				     }	        
		    }
	    }
        $cal_fin_score_array = $this->cal_fin_score($all_student_sn_unique,$all_seme_array_mysql,"","",2);	
        foreach ($all_student_sn_unique as $value_sn){
			   foreach ($all_seme_array as $value_seme){
				  foreach ($cal_fin_score_ss as $index_ss => $value_ss){					  
					  //$New['F']要取得未補考領域成績
					  $New['F'][$value_sn][$value_seme][$value_ss] = $cal_fin_score_array[$value_sn][$index_ss][$all_seme_array2mysql[$value_seme]];
					  //$New['I']要整理$New['F']未補考領域成績,$New['I']為未補考領域成績
					  $New['I'][$value_sn][$value_ss][$value_seme] = $New['F'][$value_sn][$value_seme][$value_ss];
					  //$New['G']要計算各領域各學期平均成績
					  //$New['G'][$value_sn][$value_ss][$value_seme][score_end] 為補考後各領域各學期平均成績 
					  $New['G'][$value_sn][$value_ss][$value_seme]['score_end'] =$New['B'][$value_sn][$value_seme][$value_ss]['score_end'];
					  //$New['G'][$value_sn][$value_ss][$value_seme][score_src] 為補考前各領域各學期平均成績
					  $New['G'][$value_sn][$value_ss][$value_seme]['score_src'] =$New['B'][$value_sn][$value_seme][$value_ss]['score_src'];				      
				  } 
			   } 				
			}
			//補足$New['G']空缺的成績
        	foreach ($all_student_sn_unique as $value_sn){
			   foreach ($all_seme_array as $value_seme){
				  foreach ($cal_fin_score_ss as $index_ss => $value_ss){
					 if ($New['G'][$value_sn][$value_ss][$value_seme]['score_end'] =="") {
					  $New['G'][$value_sn][$value_ss][$value_seme]['score_end'] = $New['I'][$value_sn][$value_ss][$value_seme]['score'];
					 }
					 if ($New['G'][$value_sn][$value_ss][$value_seme]['score_src'] =="") {
					  $New['G'][$value_sn][$value_ss][$value_seme]['score_src'] = $New['I'][$value_sn][$value_ss][$value_seme]['score'];
					 }
	              } 
			   } 				
			}
			foreach ($all_student_sn_unique as $value_sn){			   
			   foreach ($cal_fin_score_ss as $index_ss => $value_ss){				
				  foreach ($all_seme_array as $value_seme){
					  //計算$New['G']總成績  
					  $New['G'][$value_sn][$value_ss]['total_score_end']=$New['G'][$value_sn][$value_ss]['total_score_end']+$New['G'][$value_sn][$value_ss][$value_seme]['score_end'];
                      //計算$New['G']總成績  
					  $New['G'][$value_sn][$value_ss]['total_score_src']=$New['G'][$value_sn][$value_ss]['total_score_src']+$New['G'][$value_sn][$value_ss][$value_seme]['score_src'];					  
					  if ($New['G'][$value_sn][$value_ss][$value_seme]['score_end']!="") $New['G'][$value_sn][$value_ss]['rate_score_end']++;
                      if ($New['G'][$value_sn][$value_ss][$value_seme]['score_src']!="") $New['G'][$value_sn][$value_ss]['rate_score_src']++;
					  //計算$New['G']平均成績
					  $New['G'][$value_sn][$value_ss]['avg_score_end']=round($New['G'][$value_sn][$value_ss]['total_score_end']/$New['G'][$value_sn][$value_ss]['rate_score_end'],2);
					  $New['G'][$value_sn][$value_ss]['avg_score_src']=round($New['G'][$value_sn][$value_ss]['total_score_src']/$New['G'][$value_sn][$value_ss]['rate_score_src'],2);
			          //$New['H']要計算各領域(1,2,3,4,5,6)學期平均成績來計算通過領域數
					  $New['H'][$value_sn][$value_ss]['avg_score_end']=$New['G'][$value_sn][$value_ss]['avg_score_end'];
					  $New['H'][$value_sn][$value_ss]['avg_score_src']=$New['G'][$value_sn][$value_ss]['avg_score_src'];
				  } 				 
				  if ($New['H'][$value_sn][$value_ss]['avg_score_end']>=60) $New['H'][$value_sn]['total_ss_pass_end']++;
				  $New['H'][$value_sn]['total_ss_Nopass_end']=7-$New['H'][$value_sn]['total_ss_pass_end'];
				  if ($New['H'][$value_sn][$value_ss]['avg_score_src']>=60) $New['H'][$value_sn]['total_ss_pass_src']++;
				  $New['H'][$value_sn]['total_ss_Nopass_src']=7-$New['H'][$value_sn]['total_ss_pass_src'];
			   } 				
			}
			//$total_num_fail_end 補考後不及格總人數
			$total_num_fail_end=0;
	        //$total_num_language_end  補考後語文領域不及格人數
	        $total_num_language_end=0;
	        //$total_num_math_end  補考後數學領域不及格人數
	        $total_num_math_end=0;
	        //$total_num_nature_end 補考後自然與生活科技領域不及格人數
	        $total_num_nature_end=0;
	        //$total_num_social_end 補考後社會領域不及格人數
	        $total_num_social_end=0;
	        //$total_num_health_end 補考後健康與體育領域不及格人數
	        $total_num_health_end=0;
	        //$total_num_art_end 補考後藝術與人文不及格人數
	        $total_num_art_end =0;
	        //$total_num_complex_end 補考後綜合活動領域不及格人數
	        $total_num_complex_end=0;
	        //$total_num_fail_scope_1_end 補考後1個領域不及格人數
			$total_num_fail_scope_1_end=0;
			//$total_num_fail_scope_2_end 補考後2個領域不及格人數
			$total_num_fail_scope_2_end=0;
			//$total_num_fail_scope_3_end 補考後3個領域不及格人數
			$total_num_fail_scope_3_end=0;
			//$total_num_fail_scope_4_end 補考後4個領域不及格人數
			$total_num_fail_scope_4_end=0;
			//$total_num_fail_scope_5_end 補考後5個領域不及格人數
			$total_num_fail_scope_5_end=0;
			//$total_num_fail_scope_6_end 補考後6個領域不及格人數
			$total_num_fail_scope_6_end=0;
			//$total_num_fail_scope_7_end 補考後7個領域不及格人數
			$total_num_fail_scope_7_end=0;
			
			//$total_num_fail_src 補考前不及格總人數
			$total_num_fail_src=0;
	        //$total_num_language_src  補考前語文領域不及格人數
	        $total_num_language_src=0;
	        //$total_num_math_src  補考前數學領域不及格人數
	        $total_num_math_src=0;
	        //$total_num_nature_src 補考前自然與生活科技領域不及格人數
	        $total_num_nature_src=0;
	        //$total_num_social_src 補考前社會領域不及格人數
	        $total_num_social_src=0;
	        //$total_num_health_src 補考前健康與體育領域不及格人數
	        $total_num_health_src=0;
	        //$total_num_art_src 補考前藝術與人文不及格人數
	        $total_num_art_src=0;
	        //$total_num_complex_src 補考前綜合活動領域不及格人數
	        $total_num_complex_src=0;
	        //$total_num_fail_scope_1_src 補考前1個領域不及格人數
			$total_num_fail_scope_1_src=0;
			//$total_num_fail_scope_2_src 補考前2個領域不及格人數
			$total_num_fail_scope_2_src=0;
			//$total_num_fail_scope_3_src 補考前3個領域不及格人數
			$total_num_fail_scope_3_src=0;
			//$total_num_fail_scope_4_src 補考前4個領域不及格人數
			$total_num_fail_scope_4_src=0;
			//$total_num_fail_scope_5_src 補考前5個領域不及格人數
			$total_num_fail_scope_5_src=0;
			//$total_num_fail_scope_6_src 補考前6個領域不及格人數
			$total_num_fail_scope_6_src=0;
			//$total_num_fail_scope_7_src 補考前7個領域不及格人數
			$total_num_fail_scope_7_src=0;
			
			foreach ($all_student_sn_unique as $value_sn){			   
			   $total_num_fail_end++;
			     if ($New['H'][$value_sn][1]['avg_score_end'] < 60) $total_num_language_end++; 
			     if ($New['H'][$value_sn][2]['avg_score_end'] < 60) $total_num_math_end++;
			     if ($New['H'][$value_sn][3]['avg_score_end'] < 60) $total_num_nature_end++; 
			     if ($New['H'][$value_sn][4]['avg_score_end'] < 60) $total_num_social_end++;
			     if ($New['H'][$value_sn][5]['avg_score_end'] < 60) $total_num_health_end++; 
			     if ($New['H'][$value_sn][6]['avg_score_end'] < 60) $total_num_art_end++;
			     if ($New['H'][$value_sn][7]['avg_score_end'] < 60) $total_num_complex_end++;
			     if ($New['H'][$value_sn]['total_ss_Nopass_end'] == 1) $total_num_fail_scope_1_end++;
			     if ($New['H'][$value_sn]['total_ss_Nopass_end'] == 2) $total_num_fail_scope_2_end++;
			     if ($New['H'][$value_sn]['total_ss_Nopass_end'] == 3) $total_num_fail_scope_3_end++;
			     if ($New['H'][$value_sn]['total_ss_Nopass_end'] == 4) $total_num_fail_scope_4_end++;
			     if ($New['H'][$value_sn]['total_ss_Nopass_end'] == 5) $total_num_fail_scope_5_end++;
			     if ($New['H'][$value_sn]['total_ss_Nopass_end'] == 6) $total_num_fail_scope_6_end++;
			     if ($New['H'][$value_sn]['total_ss_Nopass_end'] == 7) $total_num_fail_scope_7_end++;

			   $total_num_fail_src++;
			     if ($New['H'][$value_sn][1]['avg_score_src'] < 60) $total_num_language_src++; 
			     if ($New['H'][$value_sn][2]['avg_score_src'] < 60) $total_num_math_src++;
			     if ($New['H'][$value_sn][3]['avg_score_src'] < 60) $total_num_nature_src++; 
			     if ($New['H'][$value_sn][4]['avg_score_src'] < 60) $total_num_social_src++;
			     if ($New['H'][$value_sn][5]['avg_score_src'] < 60) $total_num_health_src++; 
			     if ($New['H'][$value_sn][6]['avg_score_src'] < 60) $total_num_art_src++;
			     if ($New['H'][$value_sn][7]['avg_score_src'] < 60) $total_num_complex_src++;
			     if ($New['H'][$value_sn]['total_ss_Nopass_src'] == 1) $total_num_fail_scope_1_src++;
			     if ($New['H'][$value_sn]['total_ss_Nopass_src'] == 2) $total_num_fail_scope_2_src++;
			     if ($New['H'][$value_sn]['total_ss_Nopass_src'] == 3) $total_num_fail_scope_3_src++;
			     if ($New['H'][$value_sn]['total_ss_Nopass_src'] == 4) $total_num_fail_scope_4_src++;
			     if ($New['H'][$value_sn]['total_ss_Nopass_src'] == 5) $total_num_fail_scope_5_src++;
			     if ($New['H'][$value_sn]['total_ss_Nopass_src'] == 6) $total_num_fail_scope_6_src++;
			     if ($New['H'][$value_sn]['total_ss_Nopass_src'] == 7) $total_num_fail_scope_7_src++;
			}
			$New['L'][all_seme_array]=$all_seme_array;
			$New['L']['total_num_fail_end']=$total_num_fail_end;
			$New['L']['total_num_language_end']=$total_num_language_end;
			$New['L']['total_num_math_end']=$total_num_math_end;
			$New['L']['total_num_nature_end']=$total_num_nature_end;
			$New['L']['total_num_social_end']=$total_num_social_end;
			$New['L']['total_num_health_end']=$total_num_health_end;
			$New['L']['total_num_art_end']=$total_num_art_end;
			$New['L']['total_num_complex_end']=$total_num_complex_end;
			$New['L']['total_num_fail_scope_1_end']=$total_num_fail_scope_1_end;
			$New['L']['total_num_fail_scope_2_end']=$total_num_fail_scope_2_end;
			$New['L']['total_num_fail_scope_3_end']=$total_num_fail_scope_3_end;
			$New['L']['total_num_fail_scope_4_end']=$total_num_fail_scope_4_end;
			$New['L']['total_num_fail_scope_5_end']=$total_num_fail_scope_5_end;
			$New['L']['total_num_fail_scope_6_end']=$total_num_fail_scope_6_end;
			$New['L']['total_num_fail_scope_7_end']=$total_num_fail_scope_7_end;	

            $New['L']['total_num_fail_src']=$total_num_fail_src;
			$New['L']['total_num_language_src']=$total_num_language_src;
			$New['L']['total_num_math_src']=$total_num_math_src;
			$New['L']['total_num_nature_src']=$total_num_nature_src;
			$New['L']['total_num_social_src']=$total_num_social_src;
			$New['L']['total_num_health_src']=$total_num_health_src;
			$New['L']['total_num_art_src']=$total_num_art_src;
			$New['L']['total_num_complex_src']=$total_num_complex_src;
			$New['L']['total_num_fail_scope_1_src']=$total_num_fail_scope_1_src;
			$New['L']['total_num_fail_scope_2_src']=$total_num_fail_scope_2_src;
			$New['L']['total_num_fail_scope_3_src']=$total_num_fail_scope_3_src;
			$New['L']['total_num_fail_scope_4_src']=$total_num_fail_scope_4_src;
			$New['L']['total_num_fail_scope_5_src']=$total_num_fail_scope_5_src;
			$New['L']['total_num_fail_scope_6_src']=$total_num_fail_scope_6_src;
			$New['L']['total_num_fail_scope_7_src']=$total_num_fail_scope_7_src;

        return $New['L'];
   
   }
       
}
