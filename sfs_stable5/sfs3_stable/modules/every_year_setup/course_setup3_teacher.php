<?php

// $Id: course_setup3_teacher.php 8497 2015-08-26 04:55:12Z smallduh $

/* 取得基本設定檔 */
require_once "config.php";
require_once "../../include/sfs_oo_zip2.php";
require_once "../../include/sfs_case_PLlib.php";
include ("$SFS_PATH/include/sfs_oo_overlib.php");

$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);
$m_arr = &get_sfs_module_set('course_paper');
extract($m_arr, EXTR_OVERWRITE);
if ($midnoon=='') $midnoon=5;

//$CONN->debug = true;
sfs_check();

if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}


//若有選擇學年學期，進行分割取得學年及學期
if(!empty($year_seme)){
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期


if($act == "儲存課表"){
	save_class_table($sel_year,$sel_seme,$hide_class_id,$ss_id,$teacher_sn,$co_sn,$room,$c_kind);
	//$to="list_class_table" ;
	$act="list_class_table";
	$main=list_teacher_table($sel_year,$sel_seme,$teacher_sn,$set_class_id);
	//header("location: {$_SERVER['PHP_SELF']}?act=$to&sel_year=$sel_year&sel_seme=$sel_seme&class_id=$class_id&teacher_sn=$teacher_sn");
}elseif($act=="list_class_table" or $act=="開始設定"){
	$act="list_class_table";
	//if ($_POST['class_id']<>0) $set_class_id= $_POST['class_id'] ;
	$main=list_teacher_table($sel_year,$sel_seme,$teacher_sn,$set_class_id);
}else{
	$main=&class_form($sel_year,$sel_seme);
}


//秀出網頁
head("以教師排課");

?>

<style type="text/css">
<!--
.noborder {
	border: none;
	background-color: #F5E5E5;
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

function jumpMenu(){
	location="<?php echo $_SERVER['PHP_SELF']?>?act=<?php echo $act;?>&sel_year=<?php echo $sel_year;?>&sel_seme=<?php echo $sel_seme;?>&teacher_sn=<?php echo $teacher_sn ?>&class_id=" + document.myform.class_id.options[document.myform.class_id.selectedIndex].value;
}
function openwindow(url_str){
window.open (url_str,"選擇教師","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,copyhistory=no,width=600,height=420");
}

//顯示目前班級已排課情形
function show_class_mode() {
	//取得教師 sn
	var class_idx = document.myform.class_id.selectedIndex ;	
	//清除先前的排課圖示
	reset_img() ;
	
	//取得教師 sn、姓名
	var class_id = class_id_id[class_idx] ;

  //在他班的排課 節次
  var begi = the_class_course_star[class_id] ;
  var endi = the_class_course_end[class_id] ;


  //出現已排課圖示
  for (var i = begi ; i <= endi ; i++)
  {
    var posi = the_class_course_pos[i] ;
    var img_name = "img[" + posi + "]" ;
    var str = "已排-" + the_class_course[i] ;

    MM_changeProp(img_name ,'','src', 'images/cour2.gif' ) ; 			
    MM_changeProp(img_name ,'','title', str ) ; 		
    MM_changeProp(img_name ,'','alt', str ) ; 		
  }		
	
	//MM_changeProp('set_class_id' ,'','value',class_id,'INPUT/hidden') ;
}	

//圖示還原
function  reset_img() {
  var i = 0 ;
	while ( i < document.F2.elements.length) {
		var obj=document.F2.elements[i];
		//如果是checkbox物件，
		if (obj.type=="checkbox"   ) {
			var v = obj.value ;
			//由chkbox 來取得 image 圖示
			var img_name= "img[" + v + "]" ; 

			var str = "可排課" ;
			MM_changeProp(img_name ,'','src', 'images/cour1.gif' ) ; 
			MM_changeProp(img_name ,'','title', str ) ; 		
      MM_changeProp(img_name ,'','alt', str ) ;
		}
    i ++ ;
	}

}

//---------------------------------------------------
//指定科目
function setkmo(idx,ss_id,ss_name, ID) {
	
	var replay;
	//
	if (ID.checked==true) {
		
  	replay=select_sub(idx , ss_id , ss_name);

		if (replay=='OK'){ID.checked=false;}
		//處理失敗,表示沒有定任何節次
		if (replay=='NO'){alert("注意：\n\n先選節次及任課教師，再選科目！！");
			ID.checked=false;}
	}
	return ;	
}	

///----- 做選擇的副函式-----------------

function  select_sub(idx ,ss_id , ss_name ) {
	var i =0;
	var check_i ;
	var ok = 0 ;
	var v ,v_ss , v_tea ,v_tea_name;
	

	//取得班級選單資料

	var class_idx = document.myform.class_id.selectedIndex ;

	
	if (idx == 0 ) {  //清空
		 class_idx = 0 ;
	} else {
	  if (document.myform.class_id.selectedIndex == 0) 
		  //是否已指定班級
		  return 'NO' ;
	}	
	//取得班級 id、名稱
	var c_id = class_id_id[class_idx] ;
	var c_name = class_id_name[class_idx] ;
	//alert (tea_sn_id + tea[tea_idx] ) ;
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
			tmp_ss_id = "**-" + ss_id ;   //待轉換成正式 ss_id
			MM_changeProp(v_ss ,'','value',tmp_ss_id,'INPUT/hidden') ;
      
      //班級 id
      if (idx != 0 ) {
			  v_tea = "hide_class_id[" + v + "]" ; 
			  MM_changeProp(v_tea ,'','value', c_id ,'INPUT/hidden') ; 
      }
			
			//班級名稱
			v_tea_name = "show_class_id[" + v + "]" ; 
			MM_changeProp(v_tea_name ,'','value', c_name ,'INPUT/TEXT') ; 	
			MM_changeProp(v_tea_name ,'','className', 'editing' ,'INPUT/TEXT') ; 				
			
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

//基本設定表單
function &class_form($sel_year,$sel_seme){
	global $school_menu_p,$act;
	
	//取得年度與學期的下拉選單
	$date_select=&class_ok_setup_year($sel_year,$sel_seme,"year_seme","jumpMenu_seme");
	//年級與班級選單
	$class_select=&get_class_select($sel_year,$sel_seme,"","class_id");
	//if(empty($class_select) or empty($date_select))	header("location:{$_SERVER['PHP_SELF']}?error=1");

	//說明
	$help_text="
	請選擇一個學年、學期以做設定。||
	<span class='like_button'>開始設定</span> 會開始進行該學年班級課表設定。||
	<span class='like_button'>觀看設定</span>會列出該學年學期班級課表的設定。
	";
	$help=&help($help_text);

	$tool_bar=&make_menu($school_menu_p);

	$main="
	<script language='JavaScript'>
	function jumpMenu_seme(){
		if(document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value!=''){
			location=\"{$_SERVER['PHP_SELF']}?act=$act&year_seme=\" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value;
		}
	}
	function check_ok(){
		if (document.myform.class_id.value==''){
			alert('未選擇班級');
			return false;
		}
		else 
			return true;
	}
	</script>
	$tool_bar
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
	<tr bgcolor='#FFFFFF'><td>
		<table>
		<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
  		<tr><td>請選擇欲設定的學年度：</td><td>$date_select</td></tr>
		<tr><td>請選擇欲設定的班級：</td><td>$class_select</td></tr>
		<tr><td colspan='2'><input type='submit' name='act' value='開始設定' onclick=\"return check_ok()\">
		<input type='submit' name='act' value='觀看設定'>
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


//列出某位教師課表

function list_teacher_table($sel_year,$sel_seme,$teacher_sn="",$set_class_id="", $mode=""){
	global $CONN,$class_year,$conID,$weekN,$school_menu_p,$go_on,$debug,$midnoon,$SFS_PATH_HTML;

	//取得學年
	$semester_name=($sel_seme=='2')?"下":"上";
	$date_text="<font color='#607387'>
	<font color='#000000'>$sel_year</font> 學年
	<font color='#000000'>$semester_name</font>學期
	</font>
	<input type=hidden name=sel_year value='$sel_year'>
	<input type=hidden name=sel_seme value='$sel_seme'>
	";


	//每週的日數
	$dayn=sizeof($weekN)+1;
	
	//找出某教師所有課程
	$sql_select = "select course_id,teacher_sn,cooperate_sn,day,sector,ss_id,room,c_kind ,class_id from score_course where `year` = '$sel_year' and semester='$sel_seme' and  teacher_sn='$teacher_sn' order by day,sector";
//echo $sql_select.'<br>';
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($course_id,$t_sn,$co_sn,$day,$sector,$ss_id,$room,$c_kind ,$class_id)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$a[$k]=$ss_id;
		$b[$k]=$t_sn;
		$co[$k]=$co_sn;
		$c[$k]=$class_id;
		$r[$k]=$room;
		$ckind[$k]=$c_kind;
		
	}

	//取得星期那一列
	for ($i=1;$i<=count($weekN); $i++) {
		$main_a.="<td align='center' >星期".$weekN[$i-1]."</td>";
	}
	
	//取得最多節數
	$query = "select MAX(sections) as mx from score_setup where year = '$sel_year' and semester='$sel_seme'";
	$res=$CONN->Execute($query);
	$sections=$res->fields[mx];
	/*
	//取得考試所有設定
	$sm=&get_all_setup("",$sel_year,$sel_seme);
	
	$sections=$sm[sections];

	//$sections= 7 ;
	*/
	if($sections==0)
		trigger_error("請先設定 $sel_year 學年 $sel_seme 學期 [成績設定]項目,再操作課表設定<br><a href=\"$SFS_PATH_HTML/modules/every_year_setup/score_setup.php\">進入設定</a>",E_USER_ERROR);

  //----------------------------------------------------------
	//班級選單
	//$all_class_array = &get_class_array($sel_year,$sel_seme);
	if (empty($_COOKIE[cookie_sel_class_id])){
			$class_array= &get_class_array($sel_year,$sel_seme);
	}	else {
	    $tmp_List = split(',' , $_COOKIE[cookie_sel_teacher]) ;
			$tea_temp_str = substr($_COOKIE[cookie_sel_class_id],0,-1);
			foreach( $tea_temp_str as $k => $tc_id) {
	      //取得班級資料
	      $the_class=get_class_all($tc_id);
	      $class_array[$tc_id] =  $the_class[name]  ;
			}
	}
	
		//班級選單
		$class_select= "<select name=\"class_id\"  onChange=\"show_class_mode()\" size=\"1\" style=\"background-color:#FFFFFF;font-size:13px;\"> 
           <option value=\"0\">選擇班級 \n" ;
   

    foreach ( $class_array as $k => $v) {
    	  if ($k == $set_class_id) 
    	     $class_select .= "<option  value=\"$k\" selected >$v</option> \n" ;
    	  else 	
    	     $class_select .= "<option  value=\"$k\">$v</option> \n" ;
    	  
    }	  
    $class_select .= " </select>	  \n" ;

  //---------------------------------------------------------- 
  //取得全部有使用的科目陣列
  $kmo_list_array = get_kmo_array($sel_year,$sel_seme) ;
  //取得科目選單
	if (empty($_COOKIE[cookie_sel_kmo_id])){
			$kmo_array = $kmo_list_array ;
	}	else {
	    $tmp_List = split(',' , $_COOKIE[cookie_sel_kmo_id]) ;

			foreach( $tmp_List as $k => $tkmo_id ) {
	      //取得科目資料
	      if ($tkmo_id)
	        $kmo_array[$tkmo_id]=$kmo_list_array[$tkmo_id] ;
			}
	}  
		//列出科目選單
		$i = 0 ;
		$set_kmo_str = "<input name=\"radiobutton\" type=\"radio\" value=\"$i\" onClick=\"setkmo('$i' ,'0' ,  '-' ,this)\">清除 &nbsp;| &nbsp; \n" ;	
		foreach( $kmo_array as 	$k=> $v ) {
			 $i++ ;
		   $set_kmo_str .= "<input name=\"radiobutton\" type=\"radio\" value=\"$i\" onClick=\"setkmo('$i' , '$k' ,'$v', this )\" >$v &nbsp;| &nbsp; \n" ;
		}
      
    
    //---------------------------------------------------------- 
		//取得教師陣列
		$all_teacher_array = my_teacher_array();
		if (empty($_COOKIE[cookie_sel_teacher])){
			$tea_temp_arr = $all_teacher_array;
		}
		else{
			$tea_temp_str = substr($_COOKIE[cookie_sel_teacher],0,-1);
			$query = "select teacher_sn,name from teacher_base where teacher_sn in ($tea_temp_str)  order by name";
			//echo $query ;
			$res = $CONN->Execute($query);
			while(!$res->EOF){
				$tea_temp_arr[$res->fields[0]] = $res->fields[1];
				$res->MoveNext();
			}
		}


			
		//教師選單	
	  $sel = new drop_select();
	  $sel->id= $teacher_sn;
	  $sel->s_name = "teacher_sn";
	  $sel->arr = $tea_temp_arr;
	  $sel->top_option = "選擇教師";
	  $sel->is_submit = TRUE ;
	  $set_teacher_sn_select = $sel->get_select();    
	  
	  
	  
	  //----------------------------------------------------------
		//取得科目名稱陣列
		$subject_name_arr =  &get_subject_name_arr();
		
		$sql_select="select ss_id,scope_id,subject_id,enable from score_ss where year = $sel_year and semester = '$sel_seme'  and  enable='1' order by sort,sub_sort";
		//echo $sql_select ;
		$res = $CONN->Execute($sql_select) or trigger_error("SQL 錯誤 $sql_select",E_USER_ERROR);
		while(!$res->EOF){
			$scope_id = $res->fields[scope_id];
			$subject_id = $res->fields[subject_id];
			
			$subject_name= $subject_name_arr[$subject_id][subject_name];
			
			if (empty($subject_name))
				$subject_name= $subject_name_arr[$scope_id][subject_name];


			$select_ss_arr[$res->fields[ss_id]] = $subject_name;  //科目陣列
			$res->MoveNext();
		}			
		

	
  //------------------------------------------------------ 
	if($teacher_sn){


		$def_color = $color;

		
		//===============================================
		//javascript 
		//class_array 班級的陣列資料
		$i = 0 ;
		foreach ( $class_array as $k => $v) {
			$i ++ ;
			$js_str .= "class_id_name[$i]= '$v' ; \n" ;
			$js_str .= "class_id_id[$i]= '$k' ; \n" ;
    }	
    
    /*
    //取得目前班級陣列中，非現今排課教師的課表
    $i = 0 ;
		foreach ( $class_array as $k => $v) {
			 $one_class_course = get_course_class_arr($sel_year,$sel_seme,$k) ;

			 foreach ($one_class_course as $k2=> $v2) {
  			 	
			 	 if ($v2[ss_id] and ($v2[teacher_sn]<>$teacher_sn) ){ //該班有課
			     if (!isset($sn_star[$k])) 	{
			 	     $sn_star[$k] = $i ;		
			 	     $sn_end[$k] = $i ;	
			     }				 	 	
			 	   $js_str1 .= "the_class_course[$i]= '$v" .  addslashes($select_ss_arr[$v2[ss_id]]) . "(". addslashes( $all_teacher_array[ $v2[teacher_sn] ] )  . ")' ;\n " ;
			 	   
				   $js_str2 .= "the_class_course_pos[$i] = '$k2' ;\n " ;
				   $sn_end[$k] = $i ;				
				   $i++ ;
			 	 }
	
		
			}	
	
			if (isset($sn_star[$k])) 
				   $js_str3 .= " the_class_course_star['$k']=$sn_star[$k] ;  \n the_class_course_end['$k']=$sn_end[$k] ;\n" ;
	  }						   
			  
    */
    
    //取得全部排課內容
 	  $query = " SELECT class_id , teacher_sn,cooperate_sn, class_year, class_name, day ,sector ,ss_id FROM `score_course`
              WHERE `year` = '$sel_year' AND `semester` = '$sel_seme'  and  teacher_sn <> '$teacher_sn' ORDER BY `class_id` ASC  " ;
   // echo  $query ;           
    $res = $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
    $i = 0 ; 
	  while (!$res->EOF) {

	     $now_class_id = $res->fields[class_id] ;
	     //echo   $now_class_id ;  
	     
			     if (!isset($sn_star[$now_class_id])) 	{
			 	     $sn_star[$now_class_id] = $i ;		
			 	     $sn_end[$now_class_id] = $i ;	
			     }				 	 	
			 	   //$js_str1 .= "the_class_course[$i]= '".  $class_array[$res->fields[class_id]] .  addslashes($select_ss_arr[$res->fields[ss_id]]) . "(". addslashes( $all_teacher_array[ $res->fields[teacher_sn] ] )  . ")' ;\n " ;
				   $js_str1 .= "the_class_course[$i]= '".  addslashes($select_ss_arr[$res->fields[ss_id]]) . "(". addslashes( $all_teacher_array[ $res->fields[teacher_sn] ] ) . ")' ;\n " ;
			 	   
				   $js_str2 .= "the_class_course_pos[$i] = '" .$res->fields[day] ."_" . $res->fields[sector] ."' ;\n " ;
				   $sn_end[$now_class_id] = $i ;				
				   $i++ ;	    

     
       //換班級
	     //if (isset($sn_star[$now_class_id])) {
	     if (  $old_class_id <>  $now_class_id )  {
	         if (isset($old_class_id ) ) 
				      $js_str3 .= " the_class_course_star['$old_class_id']=$sn_star[$old_class_id] ;  \n the_class_course_end['$old_class_id']=$sn_end[$old_class_id] ;\n" ;
				   $old_class_id = $now_class_id ;
			 }	   
			 
       $res->MoveNext();
	  }	    
	  //最後班級記錄
	  if (isset($old_class_id ) ) {
				   $js_str3 .= " the_class_course_star['$old_class_id']=$sn_star[$old_class_id] ;  \n the_class_course_end['$old_class_id']=$sn_end[$old_class_id] ;\n" ;
				   //$old_class_id = $now_class_id ;
		}
    
	  
	  echo "
	  <script language=\"JavaScript\">
    <!-- Begin 
    //教師資料陣列
    var class_id_name = new Array() ;
    var class_id_id = new Array() ;
    
    class_id_name[0]='_' ;
    class_id_id[0]='0' ;
    
    $js_str
    
    var the_class_course = new Array() ;
    $js_str1 
    var the_class_course_pos = new Array() ;
    $js_str2 
    
    var the_class_course_star = new Array() ;
    var the_class_course_end = new Array() ;
    $js_str3
     -->
    </script>

    " ;		
    
	
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
				
				
				//$del_link = "{$_SERVER['PHP_SELF']}?act=delete&sel_year=$sel_year&sel_seme=$sel_seme&class_id=$class_id&teacher_sn=".$course_class_arr[$k2][teacher_sn];
				$teacher_sel='';
				$subject_sel='';
				$room_sel='';
				$re_set ='';
				$class_cel_sel ='' ;
				//$co_sn_name ='' ;
				
				//協同教學教師選單	
				$sel = new drop_select();
				$sel->id= $co[$k2];
				$sel->s_name = "co_sn[$k2]";
				$sel->arr = $all_teacher_array;
				$sel->top_option = "";
				$sel->is_submit = False ;
				$co_sn_name = $sel->get_select();
					

				if(!empty( $a[$k2] )) {
					//有排課
					$color = "#F5E5E5";

					

  
					//科目的下拉選單
					$chk_str = "<input name=\"chk[$k2]\" type=\"checkbox\" id=\"chk[$k2]\" value=\"$k2\"> 
					            <img  src=\"images/cour1.gif\" name=\"img[$k2]\" alt=\"可排\" title=\"可排\" >\n" ;



					$subject_sel = "<input name=\"inp_ss_id[$k2]\" type=\"text\" value=\"". $select_ss_arr[$a[$k2]]."\" size=\"8\" readonly class=\"noborder\"> \n" ;
					$subject_sel .= "<input type=\"hidden\" name=\"ss_id[$k2]\" value=\"".$a[$k2]."\">\n";
					
					$class_cel_sel = "班級:<input name=\"show_class_id[$k2]\" type=\"text\" value=\"". $class_array[$c[$k2]]."\" size=\"8\" readonly class=\"noborder\" > <br>\n" ;
					$class_cel_sel .= "<input type=\"hidden\" name=\"hide_class_id[$k2]\" value=\"".$c[$k2]."\">\n";

					$room_sel =&select_room($sel_year,$sel_seme,"room[$k2]",$r[$k2]);
					$checked=$ckind[$k2]?'checked':'';
					$kind_check="<input type='radio' name='c_kind[$k2]' value='0' checked>否";
					$kind_check.="<input type='radio' name='c_kind[$k2]' value='1' $checked>是";
					
					$re_set ="";					
				}
				//未排課
				else{
					//科目的下拉選單
					$chk_str = "<input name=\"chk[$k2]\" type=\"checkbox\" id=\"chk[$k2]\" value=\"$k2\"> 
					            <img  src=\"images/cour1.gif\"  name=\"img[$k2]\" alt=\"可排\" title=\"可排\" >\n" ;



					$subject_sel = " <input name=\"inp_ss_id[$k2]\" type=\"text\" value=\"\" size=\"8\" readonly  class=\"showborder\" > \n" ;
					$subject_sel .= "<input type=\"hidden\" name=\"ss_id[$k2]\" value=\"0\">\n";
					
					  $class_cel_sel = "班級:<input name=\"show_class_id[$k2]\" type=\"text\" value=\"\" size=\"8\" readonly class=\"showborder\" > <br>\n" ;
								$class_cel_sel .= "<input type=\"hidden\" name=\"hide_class_id[$k2]\" value=\"\">\n";
					
					$room_sel =&select_room($sel_year,$sel_seme,"room[$k2]",$r[$k2]);
					$checked=$ckind[$k2]?'checked':'';
					$kind_check="<input type='radio' name='c_kind[$k2]' value='0' checked>否";
					$kind_check.="<input type='radio' name='c_kind[$k2]' value='1' $checked>是";

					$re_set ="";
				}
				
				//每一格
				$debug_str=($debug)?"<small><font color='#aaaaaa'>-".$a[$k2]."</font></small><br>":"";
				$color=$checked?'#ccffaa':$color;
				$all_class.="<td $align bgcolor='$color'>
				$chk_str
				$subject_sel
				$re_set<br>$debug_str
				$class_cel_sel
				教室:$room_sel 
				<br>兼課:$kind_check
				<br>協同教學教師: $co_sn_name
				</td>\n";
			

			}

			$all_class.= "</tr>\n" ;
		}

		$submit="<input type='submit'  name ='act' value='儲存課表'>";

		//該班課表
		$main_class_list="
		<form action='{$_SERVER['PHP_SELF']}' method='post' name= 'F2' >
		<tr><td colspan='6'  bgcolor='#FFFFFF'><font color=red>先選定[班級]及[□節次]，再指定科目。注意：在離開、換排課教師前要先做儲存！</font>		$submit<br>
		$set_kmo_str</td></tr>
		<tr bgcolor='#E1ECFF'><td align='center'>節</td>$main_a</tr>
		$all_class
		<tr><td colspan='6'  bgcolor='#FFFFFF'>$set_kmo_str</td></tr>
		<tr bgcolor='#E1ECFF'><td colspan='6' align='center'>
		<input type='hidden' name='sel_year' value='$sel_year'>
		<input type='hidden' name='sel_seme' value='$sel_seme'>
		<input type='hidden' name='teacher_sn' value='$teacher_sn'>

		$submit
		</td></tr>
		";
	}else{
		$main_class_list="";
	}
	
	$tool_bar=&make_menu($school_menu_p);
	

		
	//教師選單
	$ol  = new overlib($SFS_PATH_HTML."include");
	$ol->ol_capicon=$SFS_PATH_HTML."images/componi.gif";
	$overss = $ol->over("您可以先篩選教師,便於操作","選擇教師");	
	$url_str =$SFS_PATH_HTML.get_store_path()."/sel_class.php";
	$open_window = "<a $overss  onclick=\"openwindow('$url_str')\" alt=\"選擇教師\"><img src='./images/wedit.png' border='0'></a>";
	
	/*
	//科目班級選單
	$overss = $ol->over("您可以先任教科目及年級,便於操作","任教班級");
	$url_str =$SFS_PATH_HTML.get_store_path()."/sel_class_id.php";
	$open_window2 = "<a $overss  onclick=\"openwindow('$url_str')\" alt=\"選擇任教科目及年級\"><img src='./images/wedit.png' border='0'></a>";
	*/	
	
	$main="
	$tool_bar
	<table cellspacing=0 cellpadding=0><tr><td>
		<table border='0' cellspacing='1' cellpadding='4' bgcolor='#9EBCDD'>
		<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
		<input type='hidden' name='act' value='list_class_table'>
		<tr><td colspan='6' nowrap bgcolor='#FFFFFF'>
		$date_text 
		任教班級:$class_select $open_window2
		任課教師: $set_teacher_sn_select $open_window &nbsp;&nbsp; 

    </td>
		</tr>
		
		</form>
		$main_class_list
		</table>
	</td>

	</tr></table></form>
	";
	return  $main;
}




//儲存課表
function save_class_table($sel_year="",$sel_seme="",$hide_class_id="",$ss_id="",$teacher_sn="",$co_sn="",$room="",$c_kind=""){
	global $CONN;
	while(list($k,$v)=each($ss_id)){
		$kk=explode("_",$k);
		$day=$kk[0];
		$sector=$kk[1];

		$teacher=$teacher_sn;
		$cooperate=$co_sn[$k];
		$class_id = $hide_class_id[$k] ;
		
		if (substr($ss_id[$k],0,3) == '**-' ) {
		   list($tt, $tss_id) = split('-' , $ss_id[$k] ) ;
		   if ($tss_id)
		      $subject=kmo_to_ss_id($sel_year,$sel_seme,$class_id ,$tss_id ) ;
		   else 
		     $subject=0 ;    
		}else 
		   $subject=$ss_id[$k];
		   
		
		//echo "$k -- $class_id == $teacher = $subject <br>" ;
		$r=$room[$k];
		
		if ($subject == 0 and $class_id) {
			 delete_course($c[course_id],$sel_year,$sel_seme,$teacher,$cooperate,$class_id,$day,$sector,$subject,$r); 
		                 
		}	else {
    		//先取得看看有無課程
    		$c=&get_course("",$day,$sector,$class_id);
    		//假如沒有課程資料，資料庫中也無該課程，那麼跳過
    		if(empty($subject) and empty($c[course_id]))continue;
    		
    		if(empty($c[course_id])){
    			add_course($sel_year,$sel_seme,$teacher,$cooperate,$class_id,$day,$sector,$subject,$r,$c_kind);
    		}else{
    			update_course($c[course_id],$sel_year,$sel_seme,$teacher,$cooperate,$class_id,$day,$sector,$subject,$r,$c_kind);
    		}
		}

	}
	return ;
}

//儲存一筆課程資料（一班一天的某一節）
function add_course($sel_year,$sel_seme,$teacher,$cooperate,$class_id,$day,$sector,$subject,$room,$c_kind){
	global $CONN;
	//把class_id換成舊的學年
	$c=class_id_2_old($class_id);

	$sql_insert = "insert into score_course
	 (year,semester,class_id,teacher_sn,cooperate_sn, class_year,class_name,day,sector,ss_id,room,c_kind) values
	($sel_year,'$sel_seme','$class_id','$teacher','$cooperate','$c[3]','$c[4]','$day','$sector','$subject','$room','$c_kind')";
	if($CONN->Execute($sql_insert))	return true;
	die($sql_insert);
	return false;
}

//更新一筆課程資料（一班一天的某一節）
function update_course($course_id="",$sel_year="",$sel_seme="",$teacher,$cooperate,$class_id="",$day,$sector,$subject,$room,$c_kind){
	global $CONN;
	//把class_id換成舊的?レ~
	$c=class_id_2_old($class_id);

	if(!empty($course_id)){
		$where="where course_id = '$course_id'";
	}else{
		$where="where class_id = '$class_id'  and  day='$day'  and sector='$sector'";
	}
	$sql_update = "update score_course set year=$sel_year, semester='$sel_seme', class_id='$class_id',teacher_sn='$teacher',cooperate_sn='$cooperate', class_year='$c[3]',class_name='$c[4]', day='$day', sector='$sector', ss_id='$subject', room='$room', c_kind='$c_kind' $where";
//	echo $sql_update;
	$CONN->Execute($sql_update) or die($sql_update);
	return true;
}

//刪除一筆課程資料（一班一天的某一節）
function delete_course($course_id="",$sel_year="",$sel_seme="",$teacher,$cooperate="",$class_id="",$day,$sector,$subject,$room){
	global $CONN;
	$sql_update = "delete from score_course where year=$sel_year and semester='$sel_seme' and teacher_sn = '$teacher'  and  day='$day'  and sector='$sector'";
  //echo $sql_update;
	//exit();
	$CONN->Execute($sql_update) or die ("刪除失敗! SQL Error! sql=".$sql_update);
	return true;
}

//由科目列表，改取得該學年 ss_id
function kmo_to_ss_id($sel_year,$sel_seme,$class_id ,$tss_id ) {
   global $CONN;
   $the_class=get_class_all($class_id);
   
   $where_class = " and (class_id = '$class_id' or class_year= '$the_class[year]') "; 

   $sql_select = "select ss_id from score_ss  where  year = $sel_year and semester = '$sel_seme'  and enable ='1' and ( (scope_id= '$tss_id' and subject_id='0') or (subject_id= '$tss_id') )  $where_class";
   
   //echo $sql_select ;
	 $recordSet=$CONN->Execute($sql_select) or die($sql_select);
	 $ss_id =  $recordSet->fields[ss_id] ;
	 return $ss_id ;
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

//教師名字陣列依姓名排列
function my_teacher_array(){
	global $CONN;
	$query= "select a.teacher_sn,a.name from teacher_base a where a.teach_condition=0 order by a.name ";
	/*
	//  只取得科任
	$query= " select a.teacher_sn,a.name  from teacher_base  a , teacher_post b 
            where a.teach_condition=0  and a.teacher_sn = b.teacher_sn and b.class_num =''
             and b.teach_title_id <>109 and b.teach_title_id <>1 and b.teach_title_id <>101 and b.teach_title_id <>102 and b.teach_title_id <>111  and b.teach_title_id <>50 and b.teach_title_id <>106
            order by a.name  " ;
  */
  
            
	$res=$CONN->Execute($query);
	$temp_arr = array();
	while(!$res->EOF){
		$temp_arr[$res->fields[0]] = $res->fields[1];
		//echo $res->fields[1];
		$res->MoveNext();

	}
	return $temp_arr;
}



//教室的下拉選單
function &select_room($sel_year,$sel_seme,$name="room",$now_room){
	global $CONN;
	$sql_select = "select room_name from spec_classroom order by room_name";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($room)= $recordSet->FetchRow()) {
		$selected=($now_room==$room)?"selected":"";
		$data.="<option value='$room' $selected>$room</option>";
	}
	$main="<select name='$name' size='1'><option value='' selected></option>$data</select>";
	
	//$main="<input type='text' name='$name' value='$now_room' size='8'>";
	return $main;
}


//某年級的班級陣列，若沒指定年級則是全校
function get_Cyear_class_id($sel_year,$sel_seme,$cyear=""){
	global $CONN;
	$and_cyear=(empty($cyear))?"":"and c_year='$cyear'";
	$sql_select = "select class_id from school_class where year='$sel_year' and semester = '$sel_seme' and enable='1' $and_cyear order by c_year,c_sort";
	$recordSet=$CONN->Execute($sql_select)  or trigger_error($sql_select, E_USER_ERROR);
	$class_id=$recordSet->FetchRow();
	return $class_id;
}




//某班的排課情形
function get_course_class_arr($sel_year,$sel_seme,$class_id) {
	global $CONN;
	$query = "SELECT a.teacher_sn,a.cooperate_sn,a.ss_id,a.day,a.sector,a.c_kind,b.name FROM score_course a RIGHT JOIN teacher_base b ON a.teacher_sn=b.teacher_sn WHERE a.year='$sel_year' and a.semester='$sel_seme' and a.class_id='$class_id' ";
	$res = $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
	while (!$res->EOF) {
		$temp_ds = $res->fields[day]."_".$res->fields[sector];
		$temp_arr[$temp_ds][teacher_sn]=$res->fields[teacher_sn];
		$temp_arr[$temp_ds][cooperate]=$res->fields[cooperate];
		$temp_arr[$temp_ds][name]=$res->fields[name];
		$temp_arr[$temp_ds][ss_id]=$res->fields[ss_id];
		$temp_arr[$temp_ds][c_kind]=$res->fields[c_kind];
		$res->MoveNext();
	}
	return $temp_arr;
}

/*
//某位教師的排課情形
function get_course_teacher_arr($sel_year,$sel_seme,$teacher_sn) {
	global $CONN;
	$query = "SELECT a. class_id ,a.ss_id,a.day,a.sector , room  FROM score_course a WHERE a.year='$sel_year' and a.semester='$sel_seme' and a.teacher_sn='$teacher_sn' ";
	$res = $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
	while (!$res->EOF) {
		$temp_ds = $res->fields[day]."_".$res->fields[sector];
		$temp_arr[$temp_ds][class_id]=$res->fields[class_id];
		$temp_arr[$temp_ds][room]=$res->fields[room];
		//$temp_arr[$temp_ds][name]=$res->fields[name];
		$temp_arr[$temp_ds][ss_id]=$res->fields[ss_id];
		$res->MoveNext();
	}
	return $temp_arr;
}

*/

//年級或班級的陣列內容
function &get_class_array($sel_year="",$sel_seme="",$Cyear=""){
	global $CONN,$school_kind_name,$school_kind_color;

	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	//假如年級位置不是空的，則僅列出該年級選單
	$and_Cyear=($Cyear == '')?"":" and c_year='$Cyear'";
	$sql_select = "select class_id,c_year,c_name from school_class where year='$sel_year' and semester = '$sel_seme' and enable='1' $and_Cyear order by c_year,c_sort";
	//$class_name_option="";
	$recordSet=$CONN->Execute($sql_select)  or trigger_error($sql_select, E_USER_ERROR);
	while(list($class_id,$c_year,$c_name) = $recordSet->FetchRow()){
		$tmp[$class_id] = $school_kind_name[$c_year]."".$c_name."班" ;
		
		//$selected=($curr_class_id==$class_id)?"selected":"";
		//$class_name_option.=($mode=="短")?"<option value='$class_id' $selected style='background-color: $school_kind_color[$c_year]'></option>\n":"<option value='$class_id' $selected style='background-color: $school_kind_color[$c_year];'>".$school_kind_name[$c_year]."".$c_name."班</option>\n";
	}
	if(empty($tmp))trigger_error("查無班級資料", E_USER_ERROR);

	return $tmp;
}


//取得某學年使用科目名稱
function get_kmo_array($sel_year="",$sel_seme="",$Cyear="" , $class_id="") {
	global $CONN ;
	$query = " SELECT subject_id , subject_name FROM score_subject  where enable ='1'  " ;
	$recordSet=$CONN->Execute($query)  or trigger_error($query, E_USER_ERROR);
	while(list($subject_id,$subject_name ) = $recordSet->FetchRow()){
	  $kmo_name[$subject_id] =  $subject_name ;
  }
  
  //使用到的科目
  if ($Cyear)  $where_class_year= " and class_year='Cyear' " ;
  if ($class_id)  $where_class_id= " and class_id='class_id' " ;
  $query = " SELECT scope_id , subject_id FROM score_ss  
             where   	year='$sel_year' and enable='1' and semester = '$sel_seme'   $where_class_year $where_class_id 
             group by scope_id , subject_id " ;
             
	$recordSet=$CONN->Execute($query)  or trigger_error($query, E_USER_ERROR);
	while(list($scope_id,$subject_id ) = $recordSet->FetchRow()){
		$id = $subject_id ;
    if ($subject_id ==0 ) $id = $scope_id ;
		$tmp[$id] = $kmo_name[$id]  ;
  }
   return $tmp ;
	
}	
?>
