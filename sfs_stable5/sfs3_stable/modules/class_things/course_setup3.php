<?php


// $Id: course_setup3.php 7728 2013-10-28 09:02:05Z smallduh $

/* 取得基本設定檔 */
require_once "config.php";

include_once "$SFS_PATH/include/sfs_oo_overlib.php";
include_once "../../include/sfs_case_score.php";
//include_once "../../include/sfs_case_subjectscore.php";

$m_arr = &get_sfs_module_set('course_paper');
extract($m_arr, EXTR_OVERWRITE);
if ($midnoon=='') $midnoon=5;

//$CONN->debug = true;
sfs_check();

$now_teacher_sn=$_SESSION['session_tea_sn'];//取得登入老師的id
//找出任教班級
$class_name=teacher_sn_to_class_name($now_teacher_sn);
$class_id =$class_name[3] ; // 格式：094_1_06_03  


if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}


if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

//錯誤設定
if($error==1){
	$act="error";
	$error_title="無年級和班級設定";
	$error_main="找不到第 ".$sel_year." 學年度，第 ".$sel_seme." 學期的年級、班級設定，故您無法使用此功能。<ol><li>請先到『<a href='".$SFS_PATH_HTML."modules/every_year_setup/class_year_setup.php'>班級設定</a>』設定年級以及班級資料。<li>以後記得每一學期的學期出都要設定一次喔！</ol>";
}



//執行動作判斷
if($act=="error"){
	$main=&error_tbl($error_title,$error_main);
}elseif($act=="儲存課表"){
	save_class_table($sel_year,$sel_seme,$class_id,$ss_id,$teacher_sn,$room);
	header("location: {$_SERVER['PHP_SELF']}");
}elseif($act=="delete"){
	$dd = explode("_",$sel);
	$query = "delete from score_course  where  day='$dd[0]' and sector='$dd[1]' and year=$sel_year and semester=$sel_seme and class_id='$class_id'";
	$CONN->Execute($query) or trigger_error("SQL 錯誤!! $query",E_USER_ERROR);
	$act="list_class_table";
	$main=&list_class_table($sel_year,$sel_seme,$class_id);

}elseif($act=="重新設定"){
	$query = "delete from score_course where year=$sel_year and semester=$sel_seme and class_id='$class_id' and teacher_sn = '$now_teacher_sn' ";
	$CONN->Execute($query) or trigger_error("SQL 錯誤!! $query",E_USER_ERROR);
	header("location: {$_SERVER['PHP_SELF']}?act=view_class&sel_year=$sel_year&sel_seme=$sel_seme&class_id=$class_id");
}elseif($act=="downlod_ct"){	
	downlod_ct($class_id,$sel_year,$sel_seme);
	
	//downlod_all_ct($class_id,$mode,$sel_year,$sel_seme);
	//header("location: {$_SERVER['PHP_SELF']}?act=list_class_table&sel_year=$sel_year&sel_seme=$sel_seme&class_id=$class_id");

}elseif($act=="downlod_ct_htm"){	

	download_ct_htm($class_id,$sel_year,$sel_seme);
	//header("location: {$_SERVER['PHP_SELF']}?act=list_class_table&sel_year=$sel_year&sel_seme=$sel_seme&class_id=$class_id");

}else{
  	$act="list_class_table";

	$main=list_class_table($sel_year,$sel_seme,$class_id);
	//$main=&class_form($sel_year,$sel_seme);
}


//秀出網頁
head("本班功課表");

?>

<style type="text/css">
<!--
.noborder {
	border: none;
	background-color: #E0DDFF;
}
.showborder {
  /*background-color: #FFFFFF;*/
}	
.editing {
	border: none;
  background-color: #E1ECFF;
}	
-->
</style>

<script language="JavaScript">
<!-- Begin


//指定科目
function setkmo(idx,ss_id,ss_name, ID) {
	
	var replay;
	//
	if (ID.checked==true) {
		
  	replay=select_sub(idx , ss_id , ss_name);

		if (replay=='OK'){ID.checked=false;}
		//處理失敗,表示沒有定任何節次
		if (replay=='NO'){alert("注意：\n\n先選節次，再選科目！！");
		ID.checked=false;}
	}
	return ;	
}	

///----- 做選擇的副函式-----------------

function  select_sub(idx ,ss_id , ss_name) {
	var i =0;
	var check_i ;
	var ok = 0 ;
	var v ,v_ss , v_tea ,v_tea_name;
	

	while (i < document.F2.elements.length) {
		var obj=document.F2.elements[i];
		//如果是checkbox物件，而且是被按下的，而且是啟用的disabled==false
		if (obj.type=='checkbox'&& obj.checked==1  ) {
			v = obj.value ;
			
			//科目名
			v_ss = "inp_ss_id[" + v + "]" ;
			MM_changeProp(v_ss ,'','value', ss_name ,'INPUT/TEXT') ;
			MM_changeProp(v_ss ,'','className', 'editing' ,'INPUT/TEXT') ; 				
			 
			//科目代號		
			v_ss = "ss_id[" + v + "]" ;
			MM_changeProp(v_ss ,'','value',ss_id,'INPUT/hidden') ;
      
			
			obj.checked=false ;
			ok = 1 ;
		}
		i++;
	}
	if (ok ==1 )
	   return 'OK' ; //傳回處理成功的訊息
	else    
	   return 'NO';//否則就傳回失敗

}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_setTextOfTextfield(objName,x,newText) { //v3.0
  var obj = MM_findObj(objName); if (obj) obj.value = newText;
}

function MM_changeProp(objName,x,theProp,theValue) { //v6.0
	
  var obj = MM_findObj(objName);
  if (obj && (theProp.indexOf("style.")==-1 || obj.style)){
    if (theValue == true || theValue == false)
      eval("obj."+theProp+"="+theValue);
    else eval("obj."+theProp+"='"+theValue+"'");

  }
  //alert("obj."+theProp+"='"+theValue+"'") ;
}


//  End -->
</script>

<?php

echo $main;
foot();

/*
函式區
*/




//列出某個班級的課表
function list_class_table($sel_year,$sel_seme,$class_id="",$mode=""){
	global $CONN,$class_year,$conID,$weekN,$menu_p,$SFS_PATH_HTML ,$course_input,$midnoon;

	//取得班級資料
	$the_class=get_class_all($class_id);

	$class_data=class_id_2_old($class_id);
	$class_teacher=get_class_teacher($class_data[2]);
	$class_man=$class_teacher[name];
	$class_man_sn=$class_teacher[sn];
	

	//每週的日數
	$dayn=sizeof($weekN)+1;
	
	//找出某班所有課程
	$sql_select = "select course_id,teacher_sn,day,sector,ss_id,room from score_course where class_id='$class_id' order by day,sector";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($course_id,$teacher_sn,$day,$sector,$ss_id,$room)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$a[$k]=$ss_id;
		$b[$k]=$teacher_sn;
		$r[$k]=$room;
	}

	//取得星期那一列
	for ($i=1;$i<=count($weekN); $i++) {
		$main_a.="<td align='center' >星期".$weekN[$i-1]."</td>";
	}
	
	//取得考試所有設定
	$sm=&get_all_setup("",$sel_year,$sel_seme,$the_class[year]);
	$sections=$sm[sections];
	if($sections==0)
		trigger_error("請先設定 $sel_year 學年 $sel_seme 學期 [成績設定]項目,再操作課表設定<br><a href=\"$SFS_PATH_HTML/modules/every_year_setup/score_setup.php\">進入設定</a>",E_USER_ERROR);

	if(!empty($class_id)){
	
		//取得該班使用科目
		$select_ss_arr= &get_class_subject_name_arr($class_id,$sel_year,$sel_seme,$the_class[year]) ;


		//班級的排課情形
		$course_class_arr = get_course_class_arr($sel_year,$sel_seme,$class_id);
	
		$def_color = $color;
		
		//列出科目選單 prolin 20050804
		if ($course_input) {  //允許修改課表
    		$i = 0 ;
    		$set_kmo_str = "<input name=\"radiobutton\" type=\"radio\" value=\"$i\" onClick=\"setkmo('$i' ,'0' ,  '-' ,this)\">清除 &nbsp;| &nbsp; \n" ;	
    		foreach( $select_ss_arr as 	$k=> $v ) {
    			 $i++ ;
    		   $set_kmo_str .= "<input name=\"radiobutton\" type=\"radio\" value=\"$i\" onClick=\"setkmo('$i' , '$k' ,'$v', this )\" >$v &nbsp;| &nbsp; \n" ;
    		}
	  }
	  
	  
		//取得課表
		for ($j=1;$j<=$sections;$j++){

			if ($j==$midnoon){
				$all_class.= "<tr bgcolor='white'><td colspan='$dayn' align='center'>午休</td></tr>\n";
			}


			$all_class.="<tr bgcolor='#E1ECFF'><td align='center'>$j</td>";
			
			//列印出各節			
			for ($i=1;$i<=count($weekN); $i++) {
				$color = $def_color;
				$k2=$i."_".$j;
				
				

				$teacher_sel='';
				$subject_sel='';
				$room_sel='';
				$re_set ='';
				

				//本班已有的課表
				if(!empty($course_class_arr[$k2][ss_id])) {
					$chk_str ="" ;
					$teacher_sel = "<font color='blue' size=2>".$course_class_arr[$k2][name]."</font>";
					$subject_sel =  $select_ss_arr[$a[$k2]];
					$room_sel="<font color='#000000' size=2>".$r[$k2]."</font>";
					$color = "#FFE5E5";
					
					//導師自已的課
					if ($course_class_arr[$k2][teacher_sn] == $class_man_sn )  {
						$color = "#E0DDFF" ;
					  // $re_set = "<a href=\"$del_link&sel=$k2\"><img src=\"images/remove.png\" border=0 alt=\"刪除\"></a>";
					  //科目的下拉選單
					  $chk_str = "<input name=\"chk[$k2]\" type=\"checkbox\" id=\"chk[$k2]\" value=\"$k2\">\n" ;
					
					  $subject_sel = "<input name=\"inp_ss_id[$k2]\" type=\"text\" value=\"". $select_ss_arr[$a[$k2]]."\" size=\"8\" readonly class=\"noborder\"> \n" ;
					  $subject_sel .= "<input type=\"hidden\" name=\"ss_id[$k2]\" value=\"".$a[$k2]."\">\n";

					  $teacher_sel = "<input type=\"hidden\" name=\"teacher_sn[$k2]\" value=\"".$class_man_sn."\">\n";
					  $room_sel="<font color='#000000' size=2>".$r[$k2]."</font>";
					  $re_set ="";					   
				  }
				}
				//未排課
				else{
					//科目的下拉選單
					$chk_str = "<input name=\"chk[$k2]\" type=\"checkbox\" id=\"chk[$k2]\" value=\"$k2\">\n" ;

					$subject_sel = " <input name=\"inp_ss_id[$k2]\" type=\"text\" value=\"\" size=\"8\" readonly  class=\"showborder\" > \n" ;
					$subject_sel .= "<input type=\"hidden\" name=\"ss_id[$k2]\" value=\"0\">\n";
					
					$teacher_sel = "<input type=\"hidden\" name=\"teacher_sn[$k2]\" value=\"".$class_man_sn."\">\n";
					$room_sel="" ;
					$re_set ="";
				}
				
				//每一格
				$debug_str=($debug)?"<small><font color='#aaaaaa'>-".$a[$k2]."</font></small><br>":"";
				$all_class.="<td $align bgcolor='$color'>
				$chk_str
				$subject_sel
				$re_set<br>$debug_str
				$teacher_sel

				</td>\n";
			

			}

			$all_class.= "</tr>\n" ;
		}

    if ($course_input) {  //允許修改課表
		   $submit="<input type='submit' name='act' value='儲存課表'>
		   <input type='submit' name='act' value='重新設定' onClick=\"return confirm('確定重新設定貴班課表？\\n課表設定將刪除!!!');\">";
    }
    
    
		//該班課表
		$main_class_list="
		<form action='{$_SERVER['PHP_SELF']}' method='post' name= 'F2' >
		<tr><td colspan='6'  bgcolor='#FFFFFF'><font color=blue>先選定□節次，再指定科目。注意：在做其他動作前要先做儲存！</font>
		<a href='{$_SERVER['PHP_SELF']}?act=downlod_ct&class_id=$class_id&sel_year=$sel_year&sel_seme=$sel_seme'>
		<img src='images/dl_ct.png' alt='使用OpenOffice輸出課表' width='84' height='24' hspace='6' vspace='0' border='0' align='middle'>
		</a> | 
		<a href='?act=downlod_ct_htm&class_id=$class_id&sel_year=$sel_year&sel_seme=$sel_seme' target='_blank' >
		輸出網頁課表
		</a>
		<br>
		$set_kmo_str</td></tr>
		<tr bgcolor='#E1ECFF'><td align='center'>節</td>$main_a</tr>
		$all_class
		
		<tr bgcolor='#E1ECFF'><td colspan='6' align='center'>
		<input type='hidden' name='sel_year' value='$sel_year'>
		<input type='hidden' name='sel_seme' value='$sel_seme'>
		<input type='hidden' name='class_id' value='$class_id'>
		<input type='hidden' name='set_teacher_sn' value='$set_teacher_sn'>
		
		$submit
		</td></tr>
		</form>
		";
	}else{
		$main_class_list="";
	}
	
	$tool_bar=&make_menu($menu_p);
	

		
	$url_str =$SFS_PATH_HTML.get_store_path()."/sel_class.php";

	$main="
	$tool_bar

		<table border='0' cellspacing='1' cellpadding='4' bgcolor='#9EBCDD'>

		$main_class_list
		</table>

	";
	return  $main;
}




//儲存課表
function save_class_table($sel_year="",$sel_seme="",$class_id="",$ss_id="",$teacher_sn="",$room=""){
	global $CONN;
	reset($ss_id);
	while(list($k,$v)=each($ss_id)){
		$kk=explode("_",$k);
		$day=$kk[0];
		$sector=$kk[1];

		$teacher=$teacher_sn[$k];
    //echo " $k $teacher <br>";
		$subject=$ss_id[$k];
		$r=$room[$k];
		//先取得看看有無課程
		$c=&get_course("",$day,$sector,$class_id);
		//假如沒有課程資料，資料庫中也無該課程，那麼跳過
		if(empty($subject) and empty($c[course_id]))continue;
		
		if(empty($c[course_id])){
			add_course($sel_year,$sel_seme,$teacher,$class_id,$day,$sector,$subject,$r);
		}else{
			update_course($c[course_id],$sel_year,$sel_seme,$teacher,$class_id,$day,$sector,$subject,$r);
		}

	}
	return ;
}

//儲存一筆課程資料（一班一天的某一節）
function add_course($sel_year,$sel_seme,$teacher,$class_id,$day,$sector,$subject,$room){
	global $CONN;
	//把class_id換成舊的學年
	$c=class_id_2_old($class_id);

	$sql_insert = "insert into score_course
	 (year,semester,class_id,teacher_sn, class_year,class_name,day,sector,ss_id,room) values
	($sel_year,'$sel_seme','$class_id','$teacher','$c[3]','$c[4]','$day','$sector','$subject','$room')";
	if($CONN->Execute($sql_insert))	return true;
	die($sql_insert);
	return false;
}

//更新一筆課程資料（一班一天的某一節）
function update_course($course_id="",$sel_year="",$sel_seme="",$teacher,$class_id="",$day,$sector,$subject,$room){
	global $CONN;
	//把class_id換成舊的?レ~
	$c=class_id_2_old($class_id);

	if(!empty($course_id)){
		$where="where course_id = '$course_id'";
	}else{
		$where="where class_id = '$class_id'  and  day='$day'  and sector='$sector'";
	}
	$sql_update = "update score_course set year=$sel_year, semester='$sel_seme', class_id='$class_id',teacher_sn='$teacher', class_year='$c[3]',class_name='$c[4]', day='$day', sector='$sector', ss_id='$subject', room='$room' $where";
//	echo $sql_update;
	$CONN->Execute($sql_update) or die($sql_update);
	return true;
}



//取得某一筆課程資料
function &get_course($course_id="",$day="",$sector="",$class_id=""){
	global $CONN;
	if(!empty($course_id)){
		$where="where course_id = '$course_id'";
	}else{
		$where="where class_id='$class_id' and day='$day' and sector='$sector'";
	}
	$sql_select = "select * from score_course $where";
	
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	$array = $recordSet->FetchRow();
	return $array;
}




//所有教師一星期的排課情形陣列(判斷是否有衝堂)
function get_course_tea_arr($sel_year,$sel_seme) {
	global $CONN;
	$query = "select ss_id ,class_id,day,sector,teacher_sn from score_course where year='$sel_year' and semester='$sel_seme' ";
	$res = $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
	while (!$res->EOF) {
		$temp_ds = $res->fields[day]."_".$res->fields[sector];
		$temp_arr[$res->fields[teacher_sn]][$temp_ds][ss_id] = $res->fields[ss_id];
		$temp_arr[$res->fields[teacher_sn]][$temp_ds][class_id] = $res->fields[class_id];
		$res->MoveNext();
	}
	return $temp_arr;
}

//某班的排課情形
function get_course_class_arr($sel_year,$sel_seme,$class_id) {
	global $CONN;
	$query = "SELECT a.teacher_sn,a.ss_id,a.day,a.sector,b.name FROM score_course a RIGHT JOIN teacher_base b ON a.teacher_sn=b.teacher_sn WHERE a.year='$sel_year' and a.semester='$sel_seme' and a.class_id='$class_id' ";
	$res = $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
	while (!$res->EOF) {
		$temp_ds = $res->fields[day]."_".$res->fields[sector];
		$temp_arr[$temp_ds][teacher_sn]=$res->fields[teacher_sn];
		$temp_arr[$temp_ds][name]=$res->fields[name];
		$temp_arr[$temp_ds][ss_id]=$res->fields[ss_id];
		$res->MoveNext();
	}
	return $temp_arr;
}


//由班級序號class_id查出年級[year]，班級[sort]，班名[name]
function get_class_all($class_id=""){
	global $CONN,$school_kind_name;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	// init $the_class
	$the_class=array();

	$sql_select = "select c_year,c_name,c_sort from school_class where class_id='$class_id'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	list($the_class[year],$name,$the_class[sort]) = $recordSet->FetchRow();
	$y=$the_class[year];
	$the_class[name]=$school_kind_name[$y]."".$name."班";
	return $the_class;
}


//取得科目名稱陣列
function &get_subject_name_arr(){
	global $CONN;

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$sql_select = "select subject_id,subject_name,enable from score_subject where enable = '1' ";

	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

	// init $tmp_arr
	$temp_arr=array();

	while (!$recordSet->EOF) {
		$temp_arr[$recordSet->fields[subject_id]][subject_name] = $recordSet->fields[subject_name];
		$temp_arr[$recordSet->fields[subject_id]][enable] = $recordSet->fields[enable];
		$recordSet->MoveNext();
	}

//	$name=($subject_enable=='1')?$subject_name:"<font color='red'>$subject_name</font>";

	return  $temp_arr;
}

//取得該班使用科目
function  &get_class_subject_name_arr($class_id , $sel_year , $sel_seme, $the_class_year ) {
    global $CONN;
    

		$subject_name_arr =  &get_subject_name_arr();
		
		$sql_select="select ss_id,scope_id,subject_id,enable from score_ss where class_id='$class_id' and enable='1' order by sort,sub_sort";

		$res = $CONN->Execute($sql_select) or trigger_error("SQL 錯誤 $sql_select",E_USER_ERROR);
		if ($res->RecordCount() ==0){
			$sql_select="select ss_id,scope_id,subject_id,enable from score_ss where class_year='$the_class_year' and year='$sel_year' and semester='$sel_seme' and enable='1' and class_id='' order by sort,sub_sort";
			$res = $CONN->Execute($sql_select) or trigger_error("SQL 錯誤 $sql_select",E_USER_ERROR);
		}
		

		while(!$res->EOF){
			$scope_id = $res->fields[scope_id];
			$subject_id = $res->fields[subject_id];
			
			$subject_name= $subject_name_arr[$subject_id][subject_name];
			if (empty($subject_name))
				$subject_name= $subject_name_arr[$scope_id][subject_name];

			//if($subject_name_arr[$scope_id][enable])
			//	$subject_name =  "<font color='red'>$subject_name</font>";

			$select_ss_arr[$res->fields[ss_id]] = $subject_name;  //科目陣列
			$res->MoveNext();
		}	
		return $select_ss_arr ;
}			


//取出各節上課時間陣列
function section_table_this($sel_year,$sel_seme){
    global $CONN;
	$query="select * from section_time where year='$sel_year' and semester='$sel_seme' order by sector";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$section_table[$res->fields[sector]]=explode("-",$res->fields[stime]);
		$res->MoveNext();
	}
	$query = "select MAX(sections) from score_setup where year = '$sel_year' and semester='$sel_seme'";
	$res=$CONN->Execute($query);
	$max_sector=$res->fields[0];
	for ($i=1;$i<=$max_sector;$i++) {
		if ($section_table[$i][0]=="") {
			$section_table[$i][0]=" ";
			$section_table[$i][1]=" ";
		}
	}
	return $section_table;
}

//下載功課表
function downlod_ct($class_id="",$sel_year="",$sel_seme=""){
	global $CONN,$weekN,$school_kind_name,$midnoon;
	if(empty($class_id))trigger_error("無班級編號，無法下載。因為沒有接班級編號，故無法取得班級課程資料以便下載。", E_USER_ERROR);

	$oo_path = "ooo_course";
	
	
	$filename="course_".$class_id.".sxw";
	
	if(empty($class_id)){
		//取得任教班級代號
		$class_num=get_teach_class();
	}
	
	//取得班級資料
	$the_class=get_class_all($class_id);
	
	//每週的日數
	$dayn=sizeof($weekN)+1;
	
	$sql_select = "select course_id,teacher_sn,day,sector,ss_id,room from score_course where class_id='$class_id' order by day,sector";

	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($course_id,$teacher_sn,$day,$sector,$ss_id,$room)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$a[$k]=$ss_id;
		$b[$k]=$teacher_sn;
		$r[$k]=$room;
	}
	
	//取得該班使用科目
	$select_ss_arr= &get_class_subject_name_arr($class_id,$sel_year,$sel_seme,$the_class[year]) ;

	
  //轉換班級代碼
	$class=class_id_2_old($class_id);
	$class_teacher=get_class_teacher($class[2]);
	$class_man=$class_teacher[name];
	
		//每節上課時間
	$section_table=&section_table_this($sel_year,$sel_seme);	

	//取得考試所有設定
	$sm=&get_all_setup("",$sel_year,$sel_seme,$the_class[year]);
	$sections=$sm[sections];
	if(!empty($class_id)){
		//取得課表
		for ($j=1;$j<=$sections;$j++){
			//若是最後一列要用不同的樣式
			$ooo_style=($j==$sections)?"4":"2";
			
			if ($j==$midnoon){
				//預設的午休OpenOffice.org表格程式碼
				$all_class.= "<table:table-row table:style-name=\"course_tbl.3\"><table:table-cell table:style-name=\"course_tbl.A3\" table:number-columns-spanned=\"6\" table:value-type=\"string\"><text:p text:style-name=\"P12\">午間休息</text:p></table:table-cell><table:covered-table-cell/><table:covered-table-cell/><table:covered-table-cell/><table:covered-table-cell/><table:covered-table-cell/></table:table-row>";
			}
			//echo $j . $time_set[$j] ;
			$all_class.="<table:table-row table:style-name=\"course_tbl.1\"><table:table-cell table:style-name=\"course_tbl.A".$ooo_style."\" table:value-type=\"string\"><text:p text:style-name=\"P8\">第 $j 節</text:p><text:p text:style-name=\"P15\">" . $section_table[$j][0] .'~' . $section_table[$j][1] . "</text:p></table:table-cell>";
			//列印出各節
			$wn=count($weekN);
			for ($i=1;$i<=$wn;$i++) {
				//若是最後一格要用不同的樣式
				$ooo_style2=($i==$wn)?"F":"B";
			
				$k2=$i."_".$j;
				
				//$teacher_search_mode=(!empty($tsn) and $tsn==$b[$k2])?true:false;
				//科目
				//$subject_sel=&get_ss_name("","","短",$a[$k2]);
        $ss_id = $a[$k2] ;
				$subject_sel= $select_ss_arr[$ss_id] ;
				//echo $subject_sel ;
				
				//教師
				$teacher_sel=get_teacher_name($b[$k2]);
				
				//每一格
				if ($class_man <> $teacher_sel ) 
				   $all_class.="<table:table-cell table:style-name=\"course_tbl.".$ooo_style2.$ooo_style."\" table:value-type=\"string\"><text:p text:style-name=\"P14\">$subject_sel</text:p><text:p text:style-name=\"P10\"><text:span text:style-name=\"teacher_name\">$teacher_sel</text:span></text:p></table:table-cell>";
				else 
				   $all_class.="<table:table-cell table:style-name=\"course_tbl.".$ooo_style2.$ooo_style."\" table:value-type=\"string\"><text:p text:style-name=\"P9\">$subject_sel</text:p><text:p text:style-name=\"P10\"><text:span text:style-name=\"teacher_name\">$teacher_sel</text:span></text:p></table:table-cell>";
			}
			$all_class.="</table:table-row>";
		}
		
	}else{
		$all_class="";
	}
	


	//取得學校資料
	$s=get_school_base();

	//新增一個 zipfile 實例
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);
	$ttt->addDir('META-INF');
	$ttt->addfile("settings.xml");
	$ttt->addfile("styles.xml");
	$ttt->addfile("meta.xml");

	//讀出 content.xml 
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content.xml");

	//將 content.xml 的 tag 取代
	$temp_arr["city_name"] = "";//$s[sch_sheng];
	$temp_arr["school_name"] = $s[sch_cname];
	$temp_arr["Cyear"] = $stu[stud_name];
	$temp_arr["stu_class"] = $class[5];
	$temp_arr["teacher_name"] = $class_man;
	$temp_arr["year"] = $sel_year;
	$temp_arr["seme"] = $sel_seme;
	$temp_arr["all_course"] = $all_class;
	/*
	$temp_arr["time1"] = "07:50~08:05";
	$temp_arr["time2"] = "08:05~08:20";
	$temp_arr["time3"] = "08:20~08:40";
  */
	// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
	$replace_data = $ttt->change_temp($temp_arr,$data,0);
	
	// 加入 content.xml 到zip 中
	$ttt->add_file($replace_data,"content.xml");
	
	//產生 zip 檔
	$sss = & $ttt->file();

	//以串流方式送出 ooo.sxw
	header("Content-disposition: attachment; filename=$filename");
	//header("Content-type: application/octetstream");
	header("Content-type: application/vnd.sun.xml.writer");
	//header("Pragma: no-cache");
					//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	header("Expires: 0");

	echo $sss;
	
	exit;
	return;
}

//下載功課表
function download_ct_htm($class_id="",$sel_year="",$sel_seme=""){
	global $CONN,$weekN,$school_kind_name,$smarty,$midnoon,$SFS_PATH;
	if(empty($class_id))trigger_error("無班級編號，無法下載。因為沒有接班級編號，故無法取得班級課程資料以便下載。", E_USER_ERROR);


	
	//取得班級資料
	$the_class=get_class_all($class_id);
	

	//取得該班使用科目
	$select_ss_arr= &get_class_subject_name_arr($class_id,$sel_year,$sel_seme,$the_class[year]) ;	
	
	$sql_select = "select course_id,teacher_sn,day,sector,ss_id,room from score_course where class_id='$class_id' order by day,sector";

	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($course_id,$teacher_sn,$day,$sector,$ss_id,$room)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$all_class[a][$day][$sector]=$select_ss_arr[$ss_id] ;
		$all_class[b][$day][$sector]=get_teacher_name($teacher_sn);
		$all_class[c][$day][$sector]=$room;
	}
	
	
  //轉換班級代碼
	$class=class_id_2_old($class_id);
	$class_teacher=get_class_teacher($class[2]);
	$class_man=$class_teacher[name];
	
	//每節上課時間
	$section_table=&section_table_this($sel_year,$sel_seme);	

	//取得考試所有設定
	$sm=&get_all_setup("",$sel_year,$sel_seme,$the_class[year]);
	$sections=$sm[sections];
  
  //學校資料
  $s=get_school_base();


//使用樣版
$template_dir = $SFS_PATH."/".get_store_path()."/templates";
// 使用 smarty tag
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
//$smarty->debugging = true;

$smarty->assign("school_name",$s[sch_cname]); 
$smarty->assign("class_name",$class[5]); 
$smarty->assign("class_teacher",$class_man); 
$smarty->assign("year",$sel_year); 
$smarty->assign("seme",$sel_seme); 
$smarty->assign("all_class",$all_class); 
$smarty->assign("weekN",$weekN); 
$smarty->assign("midnoon",$midnoon-1);
$smarty->assign("sections",$sections);
$smarty->assign("section_table",$section_table); 
$smarty->assign("template_dir",$template_dir);
$smarty->display("$template_dir/course_prn.htm");


	
	exit;
	return;
}
?>
