<?php
// $Id: creat_table.php 5310 2009-01-10 07:57:56Z hami $

// 引入您自己的 config.php 檔
require_once "config.php";
//include "config.php";
// 叫用 SFS3 的版頭
head("學期成績補匯");	

// 認證
sfs_check();

// 您的程式碼由此開始
//全域變數轉換區*****************************************************
$ck=($_GET['ck'])?$_GET['ck']:$_POST['ck'];
$Hseme_year_seme=($_GET['Hseme_year_seme'])?$_GET['Hseme_year_seme']:$_POST['Hseme_year_seme'];
$Hstud_seme_class=($_GET['Hstud_seme_class'])?$_GET['Hstud_seme_class']:$_POST['Hstud_seme_class'];
//$point=($_GET['point'])?$_GET['point']:$_POST['point'];
$scope_name=($_GET['scope_name'])?$_GET['scope_name']:$_POST['scope_name'];
$Submit2=($_GET['Submit2'])?$_GET['Submit2']:$_POST['Submit2'];

//********************************************************************

//橫向選單標籤
echo print_menu($MENU_P);


	

	//取得所有學年學期
	$seme_year_seme_A=stud_seme_year_seme();

	//階層選單
	$ck=($ck==1)?"1":"0";
	for($i=0;$i<count($seme_year_seme_A);$i++){	
		$mod=$ck%2;	 
		//學期的中文
		$C_seme_year_seme_A[$i]=intval(substr($seme_year_seme_A[$i],0,-1))."學年度第".substr($seme_year_seme_A[$i],-1)."學期";
		$menu.="<a href='{$_SERVER['PHP_SELF']}?Hseme_year_seme=$seme_year_seme_A[$i]&ck=$ck'>".$C_seme_year_seme_A[$i]."</a><br>";		
		if($Hseme_year_seme==$seme_year_seme_A[$i] && $mod==0){				
			$stud_seme_class_A=stud_seme_class($Hseme_year_seme);
			for($j=0;$j<count($stud_seme_class_A[seme_class]);$j++){			
				if($Hstud_seme_class==$stud_seme_class_A[seme_class][$j]) {$CSS[$j]="style='background-color: rgb(255, 255, 0);"; $point=$j;}			
				//班級的中文
				$C_stud_seme_class_A[seme_class][$j]=$school_kind_name[substr($stud_seme_class_A[seme_class][$j],0,-2)].$stud_seme_class_A[seme_class_name][$j]."班";
				$menu.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span $CSS[$j]><a href='{$_SERVER['PHP_SELF']}?point=$point&ck=0&Hseme_year_seme=$Hseme_year_seme&Hstud_seme_class={$stud_seme_class_A[seme_class][$j]}'>{$C_stud_seme_class_A[seme_class][$j]}</a></span><br>\n";			
				//$menu.=$stud_seme_class_A[seme_class][$j];
			}
			
		}
	}

	//領域或科目勾選表
	if($Hseme_year_seme && $Hstud_seme_class){
		$scope_select="<form name='form1' method='post' action='dlcsv.php'>";
		//for($k=0;$k<$point;$k++) $scope_select="<br>";
		$year=intval(substr($Hseme_year_seme,0,-1));
		$semester=substr($Hseme_year_seme,-1);
		$class_year=substr($Hstud_seme_class,0,-2);				
		$sql="select count(*) from score_ss where year='$year' and semester='$semester' and class_year='$class_year' and enable='1'";		
		$rs=$CONN->Execute($sql);
		$M= $rs->fields['0'];
		//如果都還沒有設過課程的話就由程式自動幫他建立基本課程
		if($M=="0"){
			auto_copy($year,$semester,$class_year,$kind="九年一貫");			
		}
		
		$class_id_tt = sprintf("%03s_%s_%02s_%s",substr($Hseme_year_seme,0,-1),substr($Hseme_year_seme,-1),substr($Hstud_seme_class,0,1),substr($Hstud_seme_class,1,2));
		$sql="select * from score_ss where class_id='$class_id_tt' AND enable='1'";
		$rs_scope=$CONN->Execute($sql);
		if ($rs_scope->RecordCount() ==0){
			$sql="select * from score_ss where year='$year' and semester='$semester' and class_year='$class_year' and class_id='' and enable='1'";
			$rs_scope=$CONN->Execute($sql);
		}

		$i++;
		while(!$rs_scope->EOF){
			$ss_id[$i]= $rs_scope->fields['ss_id'];						
			$i++;
        	$rs_scope->MoveNext();		
		}
		foreach ($ss_id as $ss_id_key => $ss_id_value) {
			//找出該科目的中文名稱
			$subject_name=ss_id_to_subject_name($ss_id_value);			
			$scope_select.="<input type='checkbox' name='ss_id_A[$ss_id_value]' value='$ss_id_value' checked >".$subject_name."<br>\n"; 
		}
		$dltable.="<input type='hidden' name='Hseme_year_seme' value='$Hseme_year_seme'>
								<input type='hidden' name='Hstud_seme_class' value='$Hstud_seme_class'>
								<input type='submit' name='Submit2' value='下載成績表格'>
								<input type='submit' name='Submit3' value='下載各科文字敘述表格'>
								<input type='submit' name='Submit4' value='下載日常生活評量表格'>
								<input type='submit' name='Submit5' value='下載努力程度表格'>
								</form>";

		$dltable.="<br> 匯出說明:<br><li>請用excel或calc等試算表軟體<br>打開此csv檔,按照學生姓名輸入成績，<br>輸完成績之後,以原檔名儲存，<br>交回給註冊組匯入。<li>同上方式進行各科文字描述匯入動作</li><li>努力程度以代號表示,如下:<br>表現優異=>5,表現良好=>4,表現尚可=>3,需再加油=>2,有待改進=>1";


	}


	//最後顯示出來的表格畫面
	echo "<table bgcolor='black' border='0' cellpadding='2' cellspacing='0' width='99%'><tr bgcolor='#FACDEF'><td valign='top' width='20%'>$menu</td><td valign='top' width='20%'>$scope_select</td><td valign='top'>$dltable</td></tr></table>";
// SFS3 的版尾
foot();

//自動加入九年一貫課程
function auto_copy($sel_year,$sel_seme,$Cyear,$kind=""){
	if($kind=="九年一貫"){
		$c[1]=array(1,2,3,4,5);
		$c[2]=array(1,2,3,4,5);
		$c[3]=array(1,2,9,10,11,4,5);
		$c[4]=array(1,2,9,10,11,4,5);
		$c[5]=array(1,2,9,10,11,4,5);
		$c[6]=array(1,2,9,10,11,4,5);
		$c[7]=array(1,2,9,10,11,4,5);
		$c[8]=array(1,2,9,10,11,4,5);
		$c[9]=array(1,2,9,10,11,4,5);
		
		if(sizeof($c[$Cyear])>0){
			$i=1;
			foreach($c[$Cyear] as $subject_id){
				//add_ss($id="",$name="",$kind="",$sel_year="",$sel_seme="",$add_scope_id="",$need_exam='1',$rate='1',$Cyear,$print="",$sort="",$sub_sort="")
				add_ss($subject_id,"","scope",$sel_year,$sel_seme,"","1","1",$Cyear,"0",$i,"");
				$i++;
			}
		}
	}
	return;
}


//新增年度科目
function add_ss($id="",$name="",$kind="",$sel_year="",$sel_seme="",$add_scope_id="",$need_exam='1',$rate='1',$Cyear,$print="",$sort="",$sub_sort=""){
	global $CONN;
	
	if($kind=="scope"){
		$ss_scope_id=$id;
		$ss_scope_name=$name;
		$ss_subject_id="";
		$ss_subject_name="";
	}elseif($kind=="subject"){
		$ss_scope_id=$add_scope_id;
		$ss_scope_name="";
		$ss_subject_id=$id;
		$ss_subject_name=$name;
	}


	//假如完全沒有科目資料則退出
	if(empty($name) && empty($id)){
		return;
	}elseif(check_in_ss($ss_scope_id,$ss_scope_name,$ss_subject_id,$ss_subject_name,$sel_year,$sel_seme,$Cyear)){
		//檢查看看是否已經有該科目
		return;
	}

	if($kind=="scope"){
		//如果輸入的是名稱，看看名稱在不在清單中，若不在則加入。
		if(!empty($name)){
			//檢查$subject_name在不在科目清單中
			$sid=in_subject($name,$kind);
			$scope_id=(empty($sid))?add_subject($name,$kind):$sid;
		}elseif(!empty($id)){
			$scope_id=$id;
		}
	}elseif($kind=="subject"){
		if(!empty($name)){
			//檢查$subject_name在不在科目清單中
			$sid=in_subject($name,$kind);
			$subject_id=(empty($sid))?add_subject($name,$kind):$sid;
		}elseif(!empty($id)){
			$subject_id=$id;
		}
		$scope_id=$add_scope_id;
	}

	//加入一課程資料
	$sql_insert = "insert into score_ss (scope_id,subject_id,year,semester,class_year,enable,need_exam,rate,print,sort,sub_sort) values ('$scope_id','$subject_id','$sel_year','$sel_seme','$Cyear','1','$need_exam','$rate','$print','$sort','$sub_sort')";
	$CONN->Execute($sql_insert);
	
	//若是分科的話，把原課程隱藏起來
	if($add_scope_id){
		if(hidden_ss($scope_id,$sel_year,$sel_seme,$Cyear))	return true;
	}

	return ;
}

//查看要新增的合科或分科名稱是否已經有在裡面
function check_in_ss($scope_id="",$scope_name="",$subject_id="",$subject_name="",$sel_year="",$sel_seme="",$Cyear=""){
	global $CONN;

	if(!empty($scope_id) && !empty($subject_name)){
		$subject_id=get_subject_id($subject_name,'1');
		if(empty($subject_id))return false;
		$and="and scope_id=$scope_id and subject_id=$subject_id";
	}elseif(!empty($scope_id) && !empty($subject_id)){
		$and="and scope_id=$scope_id and subject_id=$subject_id";
	}elseif(!empty($scope_name)){
		$scope_id=get_subject_id($scope_name,'1');
		if(empty($scope_id))return false;
		$and="and scope_id=$scope_id";
	}elseif(!empty($scope_id)){
		$and="and scope_id=$scope_id";
	}else{
		return false;
	}

	$sql_select = "select ss_id  from score_ss where enable='1' and year='$sel_year' and semester='$sel_seme' and class_year='$Cyear' $and";

	$recordSet=$CONN->Execute($sql_select);
	$i=0;
	while (!$recordSet->EOF) {
		$id=$recordSet->fields["ss_id"];
		if(!empty($id))return true;
		$recordSet->MoveNext();
	}
	return false;
}

//由ss_id找出科目名稱的函數
function  ss_id_to_subject_name($ss_id){
    global $CONN;
    $sql1="select subject_id from score_ss where ss_id=$ss_id";
    $rs1=$CONN->Execute($sql1);
    $subject_id = $rs1->fields["subject_id"];
    if($subject_id!=0){
        $sql2="select subject_name from score_subject where subject_id=$subject_id";
        $rs2=$CONN->Execute($sql2);
        $subject_name = $rs2->fields["subject_name"];
    }
    else{
        $sql3="select scope_id from score_ss where ss_id=$ss_id";
        $rs3=$CONN->Execute($sql3);
        $scope_id = $rs3->fields["scope_id"];
        $sql4="select subject_name from score_subject where subject_id=$scope_id";
        $rs4=$CONN->Execute($sql4);
        $subject_name = $rs4->fields["subject_name"];
    }
    return $subject_name;
}
?>
