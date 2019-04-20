<?php

// $Id: class_year_setup.php 5310 2009-01-10 07:57:56Z hami $

//載入班級設定
include "config.php";

sfs_check();

if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
if(empty($_REQUEST['act']))$_REQUEST['act']="";

//執行動作判斷
if($_REQUEST['act']=="儲存設定"){
	save_all_setup($sel_year,$sel_seme,$c_num,$c_name_kind,$mode);
	header("location: {$_SERVER['PHP_SELF']}?act=view&sel_year=$sel_year&sel_seme=$sel_seme");
}elseif($_REQUEST['act']=="更新班級名稱"){
	update_one_class_name($sel_year,$sel_seme,$c_name);
	header("location: {$_SERVER['PHP_SELF']}?act=view&sel_year=$sel_year&sel_seme=$sel_seme");
}elseif($_REQUEST['act']=="view" or $_REQUEST['act']=="觀看設定"){
	$main=&main_form($sel_year,$sel_seme,"view");
}elseif($_REQUEST['act']=="開始設定" or $_REQUEST['act']=="修改設定" or $_REQUEST['act']=="setup"){
	$main=&main_form($sel_year,$sel_seme,"edit");
}elseif($_REQUEST['act']=="class_setup"){
	$main=&main_form($sel_year,$sel_seme,"view",$Cyear);
}else{
	$main=&pre_form($sel_year,$sel_seme);
}


//秀出網頁
head("班級設定");
echo $main;
foot();


/*
函式區
*/
//基本設定表單
function &pre_form($sel_year,$sel_seme){
	global $school_menu_p;
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);
	//說明
	$help_text="
	請選擇一個學年、學期以做設定。||
	<span class='like_button'>開始設定</span> 會開始進行該學年學期的年級設定。||
	<span class='like_button'>觀看設定</span>會列出該學年學期的年級設定。
	";
	$help=&help($help_text);

	//取得年度與學期的下拉選單
	$date_select=&date_select($sel_year,$sel_seme);

	$main="
	$tool_bar
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
	<tr bgcolor='#FFFFFF'><td>
		<table>
		<form action='{$_SERVER['PHP_SELF']}' method='post'>
  		<tr><td>請選擇欲設定的學年度：</td><td>$date_select</td></tr>
		<tr><td colspan='2'><input type='submit' name='act' value='開始設定' class='b1'>
		<input type='submit' name='act' value='觀看設定' class='b1'>
		</td></tr>
		</form>
		</table>
	</td></tr>
	</table>
	<br>
	$help
	";
	return $main;
}



//主要的設定表格
function &main_form($sel_year,$sel_seme,$mode="edit",$Cyear=""){
	global $CONN,$school_kind_name,$school_kind_end,$school_kind_name_n,$school_menu_p,$class_name_kind,$school_kind_color,$IS_JHORES;
	
	
	foreach($school_kind_name as $key=>$value){
		
		//取得資料庫裡原有班級數
		$num=get_year_class_num($sel_year,$sel_seme,$key);
		//如果是觀看模式，沒有的就不要列出來。
		if(empty($num) and $mode=="view")continue;
		
		//國小秀出 0-6，國中秀出7-12
		if((($IS_JHORES=='0' and $key <= 6) or ($IS_JHORES=='6' and $key >=7) or $_REQUEST['op']==all) or $mode=="view"){
			if($key==0){
				$pre_text="";
			}elseif($key <= 6){
				$pre_text="國小";
			}elseif($key <= 9){
				$pre_text="國中";
			}elseif($key <= 12){
				$pre_text="高中";
			}
			
		}else{
			continue;	
		}
		
		
		$end_txt=($key==0)?"":"級";
		
		if($_REQUEST[act]!="view"){
			$op_link=($_REQUEST[op]=="all")?"<a href='$_SERVER[PHP_SELF]?act=$_REQUEST[act]&sel_year=$sel_year&sel_seme=$sel_seme'>列出預設年級</a>":"<a href='$_SERVER[PHP_SELF]?op=all&act=$_REQUEST[act]&sel_year=$sel_year&sel_seme=$sel_seme'>列出所有年級</a>";
		}else{
			$op_link="";
		}
		
		

		//取得班級命名方式
		$yc_name=get_year_class_name($sel_year,$sel_seme,$key);
		$class_nk="";
		for($i=0;$i<sizeof($class_name_kind);$i++){
			$selected=($yc_name==$i)?"selected":"";
			$class_nk.="<option value='$i' $selected>$class_name_kind[$i]</option>\n";
		}

		//取得班級設定連結表單
		$class_setup_button=(!empty($num))?"<a href='{$_SERVER['PHP_SELF']}?act=class_setup&Cyear=$key&sel_year=$sel_year&sel_seme=$sel_seme'>".$school_kind_name[$key]."各班級設定</a>":"";

		$classnk=($mode=="edit")?"<select name='c_name_kind[$key]'>$class_nk</select>\n":$class_name_kind[$yc_name];
		$select_class_num=($mode=="edit")?"<input type='text' name='c_num[$key]' size='3' value='$num'>\n":$num;
		
		$db_mode=(empty($num))?"insert":"update";
		$insert_or_update=$db_mode."-".$num;

		$all_year.="<tr bgcolor='$school_kind_color[$key]'>
		<td>".$pre_text.$school_kind_name[$key].$end_txt."
		<input type='hidden' name='mode[$key]' value='$insert_or_update'></td>
		<td>共 $select_class_num 班</td>
		<td>$classnk</td>
		<td>$class_setup_button</td>
		</tr>";
	}

	//如果是觀看模式，擇一秀出即可。
	if($mode=="view"){
		$test_ratio_set=($sm[score_mode]=="all")?$all_mode:$severally_mode;
		$submit="修改設定";
	}else{
		$test_ratio_set=$all_mode."\n".$severally_mode;
		$submit="儲存設定";
	}

	$tmp=&class_setup($sel_year,$sel_seme,$Cyear);
	$class_setup=(!is_null($Cyear) and $_REQUEST['act']=="class_setup")?"<td bgcolor='white' width=6></td><td valign='top'>".$tmp."</td>":"";
	


	//說明
	$help_text="
	「本學期學校年級」請勾選目前貴校所有的年級。
	||沒有的年級，就不需要填班級數。
	";
	$help=&help($help_text);

	$semester_name=($sel_seme=='2')?"下":"上";

	$date_text="<font color='#607387'>
	<font color='#000000'>$sel_year</font> 學年
	<font color='#000000'>$semester_name</font>學期
	</font>";


	//相關功能表
	$tool_bar=&make_menu($school_menu_p);

	$main="
	$tool_bar
	<table cellspacing=0 cellpadding=0><tr><td valign='top'>
		<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
			<form action='{$_SERVER['PHP_SELF']}' method='post'>
   			<tr bgcolor='#FFFFFF'>
			<td colspan='4'>欲設定的學年度： $date_text $op_link</td></tr>
			<tr bgcolor='#E1ECFF'><td>學校年級</td><td>班級數</td><td>名稱種類</td><td>各班列表</td></tr>
			$all_year
			<input type='hidden' name='sel_year' value='$sel_year'>
			<input type='hidden' name='sel_seme' value='$sel_seme'>
			<tr bgcolor='#FFFFFF'><td valign='top' colspan='4' align='center'><input type='submit' name='act' value='$submit' class='b1'></td></tr>
			</form>
			</table>
	</td>$class_setup</tr></table>
	<p>
	$help
	</p>
	";
	return $main;
}

//製作班級id
function make_class_id($year,$semester,$c_year,$c_sort){
	//為 $class_id 中的年級欄位做個調整
	if(strlen($year)==2)$year="0".$year;
	if(strlen($c_year)==1)$c_year="0".$c_year;
	if(strlen($c_sort)==1)$c_sort="0".$c_sort;

	$semester=$semester*1;
	$class_id=$year."_".$semester."_".$c_year."_".$c_sort;
	return $class_id;
}


//新增一個班級設定
function add_setup($year,$semester,$c_year,$c_sort,$c_name){
	global $CONN;
	
	$class_id=make_class_id($year,$semester,$c_year,$c_sort);
	if($c_name!=""){
		$sql_insert = "insert into school_class (class_id,year,semester,c_year,c_name,c_sort,enable) values ('$class_id',$year,'$semester','$c_year','$c_name',$c_sort,'1')";
	}else{
		$sql_insert = "insert into school_class (class_id,year,semester,c_year,c_sort,enable) values ('$class_id',$year,'$semester','$c_year',$c_sort,'1')";		
	}	
	$CONN->Execute($sql_insert) or user_error("執行錯誤：$sql_insert<br>",256);
	return mysql_insert_id();
}


//更新一個班級設定
function update_setup($year,$semester,$c_year,$c_sort,$c_name){
	global $CONN;
	if($c_name==""){
		return;
	}
	$class_id=make_class_id($year,$semester,$c_year,$c_sort);
	$sql_update = "update school_class set c_name='$c_name' where class_id='$class_id'";
	
	if($CONN->Execute($sql_update))	return;
	return  false;
}


//刪除一個班級或一整個年級設定
function delete_class($year,$semester,$c_year="",$c_sort=""){
	global $CONN;
	if(!empty($c_sort))$and_c_sort="and c_sort=$c_sort";
	$sql_delete = "delete from school_class where year=$year and semester='$semester' and c_year='$c_year' $and_c_sort";
	if($CONN->Execute($sql_delete))	return;
	return  false;
}

//新增所有班級設定
function save_all_setup($sel_year="",$sel_seme="",$c_num="",$c_name_kind="",$mode=""){
	global $CONN,$class_name_kind_1,$class_name_kind_2,$class_name_kind_3;

	foreach($c_num as $i => $n){
		//分解$mode，第一個是要新增或更新，第二個是記錄有的數目是多少
		$m=explode("-",$mode[$i]);
		$db_mode=$m[0];
		
		//原本的班級數量
		$num=$m[1];
				
		$class_nk="";
		
		//假如班級數不是空白
		if(!empty($n)){
			//分析班級名稱			
			if($c_name_kind[$i]=='1' or $c_name_kind[$i]=='2' or $c_name_kind[$i]=='3'){
				$class_nk=${"class_name_kind_".$c_name_kind[$i]};
			}elseif($c_name_kind[$i]=='4'){
				$class_nk="other";
			}else{
				$class_nk=$class_name_kind_1;
			}
			
			
			//以某年級的班級數為依據
			for($j=1;$j<=$n;$j++){
				//假如 $cnk 中沒有該數字的對應名稱，那麼班級名稱用原數字存檔，若是其他則把 $c_name 設為空白。
				if($class_nk=="other"){
					$c_name="";
				}else{
					$c_name=(empty($class_nk[$j]))?$j:$class_nk[$j];
				}
				//執行新增或更新
				if($db_mode=="insert"){
					add_setup($sel_year,$sel_seme,$i,$j,$c_name);
				}elseif($db_mode=="update"){
					update_setup($sel_year,$sel_seme,$i,$j,$c_name);
				}
			}
			
			//假如原來的資料比剛設定還多，那要刪掉多出來的舊設定。
			if($num > $n){
				$start=$n+1;

				for($k=$start;$k<=$num;$k++){
					delete_class($sel_year,$sel_seme,$i,$k);
				}
			}elseif($num < $n and !empty($num)){
			//假如原來的資料比剛設定還少，那要新增多出來的新設定。
				$start=$num+1;

				for($k=$start;$k<=$n;$k++){
					//班級名稱
					$cnk=$class_nk[$k];
					//假如$cnk中沒有該數字的對應名稱，那麼班級名稱用原數字存檔
					$c_name=(empty($cnk))?$k:$cnk;
					add_setup($sel_year,$sel_seme,$i,$k,$c_name);
				}
			}
			
		}elseif(!empty($num)){
			//假如傳進來資料是空白的，但是原來並非空白的，那要做刪除動作。
			delete_class($sel_year,$sel_seme,$i,$k);
		}
	}
	
	return;
}



//某個年級的班級設定
function &class_setup($sel_year,$sel_seme,$Cyear){
	global $CONN,$school_kind_name,$class_name_kind;

	$sql_select = "select class_id,c_name from school_class where year='$sel_year' and semester = '$sel_seme' and c_year='$Cyear' and enable='1' order by c_sort";
	$recordSet=$CONN->Execute($sql_select);
	while(list($class_id,$c_name)=$recordSet->FetchRow()){
	
		$data.="<tr bgcolor='white'>
		<td align='center'>".$school_kind_name[$Cyear]."<input type='text' name='c_name[$class_id]' value='$c_name' size=3>班</td>
		</tr>";
	}
	
	
	$main="
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
	<form action='{$_SERVER['PHP_SELF']}' method='post'>
	<tr bgcolor='#E1ECFF'><td>修改".$school_kind_name[$Cyear]."級個別班級名稱</td></tr>
	$data
	</table>
	<br>
	<input type='hidden' name='sel_year' value='$sel_year'>
	<input type='hidden' name='sel_seme' value='$sel_seme'>
	<div align='center'><input type='submit' name='act' value='更新班級名稱' class='b1'></div>
	</form>
	";

	return $main;
}

//取得班級數
function get_year_class_num($sel_year,$sel_seme,$key){
	global $CONN;
	$sql_select = "select count(*) from school_class where year='$sel_year' and semester = '$sel_seme' and c_year='$key' and enable='1'";
	$recordSet=$CONN->Execute($sql_select);
	list($num)=$recordSet->FetchRow();
	if($num==0)$num="";
	return $num;
}

//更新某一班級的名稱
function update_one_class_name($sel_year,$sel_seme,$c_name){
	global $CONN;
	if(empty($c_name))return;
	while(list($class_id,$name)=each($c_name)){
		$sql_update = "update school_class set c_name='$name' where year=$sel_year and semester='$sel_seme' and  class_id='$class_id' and enable='1'";
		$CONN->Execute($sql_update) or trigger_error($sql_update, E_USER_ERROR);
	}
	return  true;
}

//取得某一班的命名方式
function get_year_class_name($sel_year,$sel_seme,$c_year){
	global $CONN,$class_name_kind_1,$class_name_kind_2,$class_name_kind_3;

	$sql_select = "select c_name from school_class where year='$sel_year' and semester = '$sel_seme' and c_year='$c_year' and enable='1'";

	$recordSet=$CONN->Execute($sql_select)  or trigger_error($sql_select, E_USER_ERROR);

	while(list($c_name) = $recordSet->FetchRow()){
		if(in_array($c_name,$class_name_kind_1))return 1;
		if(in_array($c_name,$class_name_kind_2))return 2;
		if(in_array($c_name,$class_name_kind_3))return 3;
		if(!empty($c_name))return 4;
	}
	return 0;
}

?>
