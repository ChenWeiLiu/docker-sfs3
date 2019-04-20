<?php

// $Id: signin.php 7786 2013-11-25 07:05:38Z infodaes $

include "config.php";

sfs_check();

$session_tea_sn =  $_SESSION['session_tea_sn'] ;

$Submit = $_POST['Submit'] ;
$class_num = $_POST['class_num'] ;
$id = $_GET["id"] ;
$show_inp = $_POST["show_inp"] ;

if ($_POST['Submit_emp']) {
   //班上無需報名，填報記錄
   $kind = $_POST['kind'] ;   
   $o_class_num = $_POST['o_class_num'] ;
   $query =" replace into sign_data set kind='$id', item='0' ,order_pos='0' ,
             stud_name='---' , data_get='---' ,
        	 class_id='$o_class_num' ";
   //echo $query ;        	 
   $result = $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;          
}    

if ($Submit=="報名完成") {
    
    $txt_stud_id = $_POST['txt_stud_id'] ;
    $txtName = $_POST['txtName'] ;
    $txtInput = $_POST['txtInput'] ;
    $txtData = $_POST['txtData'] ;
    
    $o_class_num = $_POST['o_class_num'] ;
    $max_kind = $_POST['max_kind'] ;
    $max_get = $_POST['max_get'] ;
    $get_arr = $_POST['get_arr'] ;
    $max_input = $_POST['max_input'] ;
    
    $kind = $_POST['kind'] ;
    $kind_max = $_POST['kind_max'] ;
    $kind_pmax = $_POST['kind_pmax'] ;

     

    
    /*
    $class_num 班級
    $max_kind  類別
    $max_get   匯出項數
    $max_input 輸入欄位數
    
    $kind[]     各類類別代號
    $kind_max[] 各類別正取人數
    $kind_pmax[]各類別備取人數
    */

    for($k = 0 ; $k <$max_kind	; $k++) {
    	$kid = $kind[$k] ;
    	
    	if (trim($txt_stud_id[$k])<>"") {
    	   //快速輸入區	

    	   $stud_arr = preg_split ("/[\s,]+/", trim($txt_stud_id[$k]) ); //空白或逗號
    	   //echo count($stud_arr) ;
    	   for ($i=0 ; $i < count($stud_arr) and ($i < $kind_max[$k] + $kind_pmax[$k]) ; $i++){
    	      if ($stud_arr[$i]	>0) {

                  $stud_data = Get_stud_data ($o_class_num ,  $stud_arr[$i] , $get_arr) ; //取得姓名及匯入資料   
                  $stud_name = addslashes($stud_data[0]) ; 		
                  if ($stud_name ) {
            	      //匯入	
        	      $stud_get_data = addslashes($stud_data[1]) ;	
        	      //echo "$stud_name --- $stud_get_data -- $stud_input_data <br>" ;
        	      $query =" replace into sign_data set kind='$id', item='$kid' ,order_pos='$i' ,
        	            stud_name='$stud_name' , data_get='$stud_get_data' ,
        	            class_id='$o_class_num' ";
        	      $result = $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;       
        	  }          
    	    }  
    	   }	
	   $query =" DELETE FROM sign_data WHERE class_id = '$o_class_num' AND kind = '$id' and item='$kid' and order_pos>='$i' ";	 	
	   $result = $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ; 
    	}else {
    		
        	//正取
        	for ($m = 0 ; $m < $kind_max[$k] + $kind_pmax[$k] ; $m ++) {
        	  $tmp_id =  $txtName[$k][$m] ;	
        	  if (trim($tmp_id)<>"") {
            	    if ($tmp_id>0) {  	
            	        //學生座號
 
                        $stud_data = Get_stud_data ( $o_class_num , $tmp_id  , $get_arr) ; //取得姓名及匯入資料   
                        $stud_name = addslashes($stud_data[0]) ; 		
            	        //匯入	(全部所需資料，以|分隔)
            	        $stud_get_data =  addslashes($stud_data[1]) ;
        	    }else {  
        	        //使用姓名讀取
        	        $stud_name = $tmp_id ; 		
            	        //匯入	
            	        $stud_get_data =  @implode("##" , $txtData[$k][$m]) ;      
        	    }         
                    //輸入
            	    $stud_input_data = @implode("##" , $txtInput[$k][$m]) ;	
            	    if ($stud_name) {
        	      $query =" replace into sign_data set kind='$id', item='$kid' ,order_pos='$m' ,
        	            stud_name='$stud_name' , data_get='$stud_get_data' ,data_input ='$stud_input_data' ,
        	            class_id='$o_class_num' ";
        	       $result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;       
                    }
        	    	    
    	         }else{
    	            //刪除空格
    	            $query =" DELETE FROM sign_data WHERE class_id = '$o_class_num' AND kind = '$id' and item='$kid' and order_pos='$m' ";
    	             $result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ; 
    	         }
    	         //echo $query ;
    	         //exit ;
    	        
               }
        }
    
    }    		

    //去除 先前設定為空值的情形
    $query =" DELETE FROM sign_data where   kind='$id' and item='0' and order_pos='0' and class_id='$o_class_num' " ;
    $result = $CONN->Execute($query) ;    

}
//限制的年級
$year_arr = Get_year_limit($id) ;
$class_base_p = class_base("" , $year_arr);

$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'] ;

if ( checkid($SCRIPT_FILENAME,1)){
   if (!isset($class_num) ) 
      $class_num = "601" ;	 

 //管理者可以做的事
    $class_num_temp .= "班級 :<select name=\"class_num\" onchange=\"this.form.submit()\"><option value=''>*請選取班級*</option>";
		foreach ($class_base_p as $key => $value) {
			if ($key == $class_num)
				$class_num_temp .= "<option value=\"$key\" selected>$value</option>\n";
			else
				$class_num_temp .= "<option value=\"$key\">$value</option>\n";							
		}
    $class_num_temp .= "</select>" ;

}
else {
	//取得教師所上年級、班級
	$query =" select class_num  from teacher_post  where teacher_sn  ='$session_tea_sn'  ";
	$result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ; 
	$row = $result->FetchRow();
	$class_num = $row["class_num"];
	 
	if (($class_num <= 0) or (!in_array(substr($class_num,0,1),$year_arr) ) )  {
	   Header("Location: index.php");
	   exit ;
	}	
	$class_num_temp = $class_base_p[$class_num] ;
	
}




  head("班級報表單") ;
  
  
  echo "
        <script language='JavaScript'>
	function show_input(f) {	
		window.location.href=\"?id=$id&show_inp=\"+f;		
	}
	</script>  
<style type='text/css'>
<!--
.noinput {
	color: #999999;
	background-color: #EEEEEE;
}
-->
</style>	
	
	
	" ;
	
  print_menu($menu_p);
  $main = Input($id) ;
  echo $main ;
   foot() ;
  //----------------------------------------------------------------------------
function Get_year_limit($id) {  
    global $CONN  ;
    $sqlstr =" select *  from sign_kind  where id = $id   " ;
	//echo $query ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    $row = $result->FetchRow() ;
    $id = $row["id"] ;
    $input_classY = $row["input_classY"] ;
    $class_year_arr = @split (",", $input_classY);      
    //$year = substr($class_num,0,1)  ;
    //$re_value = in_array ($year , $class_year_arr)  ;
    return $class_year_arr ;
}      
  
  
//年級可否輸入判斷  
function Can_Input($id, $class_num ) {
    global $CONN  ;
    $sqlstr =" select *  from sign_kind  where id = $id   " ;
	//echo $query ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    $row = $result->FetchRow() ;
    $id = $row["id"] ;
    $input_classY = $row["input_classY"] ;
    $class_year_arr = @split (",", $input_classY);      
    $year = substr($class_num,0,1)  ;
    $re_value = in_array ($year , $class_year_arr)  ;
    return $re_value ;
}      

function Input($id) {
    global $CONN ,$STUD_FIELD ,$class_num_temp ,$class_base_p ,$class_num ,$show_inp;
    $sqlstr =" select *  from sign_kind  where id = $id   " ;
	//echo $query ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    $row = $result->FetchRow() ;
      $id = $row["id"] ;	
      $beg_date = $row["beg_date"] ;	
      $end_date = $row["end_date"] ;	
      $doc = $row["doc"] ;	
      $title = $row["title"] ;
      $input_classY = $row["input_classY"] ;
      $kind_set = $row["kind_set"] ;
      $data_item = $row["data_item"] ;
      $input_data_item = $row["input_data_item"] ;
      $admin = $row["admin"] ;
      $helper = $row["helper"] ;
     
      $is_hide = $row["is_hide"] ;
      
      //年級限制

      $class_year_arr = @split (",", $input_classY);      

      //日期判斷
        
      $today =  date("Y-m-d") ;
 
      if ( ($today <$beg_date ) or ($today >$end_date ) ) {
         echo "<br/>填報日期已過或未到開始報名時間！" ;
         exit () ;
      }  
      
      //項目
      $tmparr = split (",", $kind_set);   
      for ($i= 1 ; $i <= count($tmparr) ; $i++) {
      	if ($tmparr[$i-1]<>"") {
      	   $ni = $i ;	
      	   
      	   $tmparr1 = split ("##",$tmparr[$i-1]) ; //代號|類別名|正取|備取|文字說明
      	   $kind_arr[] = $tmparr1 ;
        }  
      }    
      $max_kind = $ni ;
      
      
      //一併匯出
      $data_item_arr = split (",", $data_item);   
      $max_get = count($data_item_arr) ;
      
      
      //額外欄位
      $tmparr="" ;
      $ni = 0 ;
      $tmparr = split (",", $input_data_item);   
      for ($i= 1 ; $i <= count($tmparr) ; $i++) {

      	if ($tmparr[$i-1]<>"") {
      	   $ni = $i ;	
      	   $input_arr[$i-1] = split ("##",$tmparr[$i-1]) ; //代號|欄名|格式代號|長度|預設值,      
      	}
      } 	 
      $max_input = $ni ;  
      
      
      // 每區報名的表格開頭
      $tab_title = "<table width='100%' border='1' cellspacing='0' cellpadding='4'  bordercolor='#33CCFF' >
          <tr bgcolor='#66CCFF'> 
            <td width='50'>ord</td>
            <td >姓名(座號)</td> \n" ;
            
            for ($i_i = 0 ; $i_i < $max_input ; $i_i ++) {
              $tab_title .= "<td  bgcolor='#66CCFF'>".$input_arr[$i_i][1]."</td>" ;
            }  
            for ($d_i = 0 ; $d_i < $max_get ; $d_i ++) 
              $tab_title .= "<td  class='noinput'>".$STUD_FIELD[$data_item_arr[$d_i]] ."</td>\n" ;      
      
      
//---------------------------------------------------------
    //班上報名資料
    $sqlstr =" select *  from sign_data   where kind = '$id' and class_id ='$class_num' order by item , order_pos  " ;
	//echo $query ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    while ($row = $result->FetchRow() ) {
        $did = $row["id"] ;	
        $item = $row["item"] ;
        $order_pos = $row["order_pos"] ;
        $stud_name = $row["stud_name"] ;
        $data_get = $row["data_get"] ;
        $data_input = $row["data_input"] ;     
        if ($item >0) {        
          $haved_input[$item] = TRUE ;            //那一組已有報名
          
          $d_stud_name[$item][$order_pos] =  $stud_name ;
  
          if ($data_get)  
             $d_data_get_arr[$item][$order_pos] = split ("##",$data_get) ;
          if ($data_input)  
             $d_data_input_arr[$item][$order_pos] = split ("##",$data_input) ;
        }else {
          //已填報為空值
          $my_class_is_empt = true ;
            
        }            

   }
   
      
      
      //-----------------------------------------------------------
      $main = "<form name='form1' method='post' action='$PHP_SELF'>
      <div align='center'> $class_num_temp  " ;
  if ($is_hide) { //預設是隱藏的
     $check_str = ($show_inp ) ? "checked" : ""  ;
     $main .= "    
       <input type='checkbox' name='show_inp' value='1' $check_str  onchange=\"this.form.submit()\">展開完整報名表\n" ;
    
  }       
  
  if ($my_class_is_empt) 
     $main .=" <font color='blue'>(班上已填報無人參加！)</font> " ;
  else 
     $main .=" <input type='submit' name='Submit_emp' value='班上無需填報資料'> " ;  
     
  $main .=" </div> " ;    
  
  $main .= "<table width='100%' border='1' cellspacing='0' cellpadding='4' bgcolor='#CCFFFF' bordercolor='#33CCFF' >\n" ;
  $main .=" <tr><td><font size = +2 > $title </font><br>". nl2br($doc)." </td></tr>" ;

  //各項報名資料  
  for ($i = 0 ; $i < $max_kind ; $i++) {   //代號|類別名|正取|備取|文字說明
    
    //分群配色
      if ($i % 2 ==0) {
        $item_bgcolor = '#33CCCC' ;
        $doc_bgcolor = '#CCFFFF' ;
    } else {
        $item_bgcolor = '#66FF00' ;
        $doc_bgcolor = '#EEEEEE' ;
      }  
          
      $kind_str = $kind_arr[$i][0] ;
    $main .= "<tr> 
      <td bgcolor= '$item_bgcolor' ><font size='+1' color='black' >" . $kind_arr[$i][1] . "<br>說明:" . nl2br($kind_arr[$i][4]) ."</font>
      <div align='center'><input type='submit' name='Submit' value='報名完成'></div>
      </td>
    </tr>" ;

    if (!$haved_input[$kind_str])       //該組有報名，快速輸入不出現
      $main .= "<tr><td bgcolor= '$doc_bgcolor'>快速輸入區(輸入座號):<input type='text' name='txt_stud_id[$i]'  size='40'><font color='red' >
      <br>(以逗號或空白分隔，如需要一併輸入其他資料者不適用，請使用下方區塊輸入。)</font></td></tr>" ;
    
    if ($haved_input[$kind_str] or !($is_hide) or $show_inp) {    //有資料或不隱藏時，會出現
    $main .= "<tr> 
      <td  bgcolor= '$doc_bgcolor'>\n 
        $tab_title "; 

          //每個人  
          
        for ($man = 0 ; $man < $kind_arr[$i][2] ; $man ++) {

          $main .="</tr>
          <tr > 
            <td >". ($man+1) ."</td>
            <td > 
              <input type='text' name='txtName[$i][$man]' maxlength='6' size='6' value='". $d_stud_name[$kind_str][$man] . "'>
            </td>\n" ;
            
            //輸入欄位
            for ($i_i = 0 ; $i_i < $max_input ; $i_i ++) {
              if ($haved_input[$kind_str] <> TRUE )  //預設值
                 $d_data_input_arr[$kind_str][$man][$i_i] = $input_arr[$i_i][4] ; 
              $main .="<td  > 
              <input type='text' name='txtInput[$i][$man][]' size='".$input_arr[$i_i][3]."' value='". $d_data_input_arr[$kind_str][$man][$i_i] . "'>
              </td> \n" ;
            }
              
            //匯出欄位  
            for ($d_i = 0 ; $d_i < $max_get ; $d_i ++) {  
               $main .="<td   class='noinput' > 
              <input type='text' name='txtData[$i][$man][]' size='10' value='". $d_data_get_arr[$kind_str][$man][$d_i] . "' class='noinput' >
            </td>\n" ;
            }
        
          $main .="</tr>\n" ;
        }  
        
        //備取
        for ($man = 0 ; $man < $kind_arr[$i][3] ; $man ++) {
          $nm = $man + $kind_arr[$i][2] ;
          $main .="</tr>
          <tr bgcolor='#CCCCCC'> 
            <td >備". ($man+1) ."</td>
            <td > 
              <input type='text' name='txtName[$i][$nm]' maxlength='6' size='6' value='". $d_stud_name[$kind_str][$nm] . "' >
            </td>\n" ;
            
            //輸入欄位
            //$input_arr[$i][4] 預設值
            for ($i_i = 0 ; $i_i < $max_input ; $i_i ++) {
              if ($haved_input[$kind_str] <> TRUE )  //預設值
                 $d_data_input_arr[$kind_str][$nm][$i_i] = $input_arr[$i_i][4] ;                 
              $main .="<td  bgcolor='#CCCCCC'> 
              <input type='text' name='txtInput[$i][$nm][]' size='".$input_arr[$i_i][3]."' value='". $d_data_input_arr[$kind_str][$nm][$i_i] . "'>
              </td> \n" ;
            }
              
            //匯出欄位  
            for ($d_i = 0 ; $d_i < $max_get ; $d_i ++) {  
               $main .="<td  class='noinput' bgcolor='#9999CC'> 
              <input type='text' name='txtData[$i][$nm][]' size='10' value='". $d_data_get_arr[$kind_str][$nm][$d_i] . "' class='noinput' >
            </td>\n" ;
            }
        
          $main .="</tr>\n" ;
        }          

      $main .="</table></td>
    </tr>\n" ;  //各類別
    }
    }
    //說明 
    $main .="<tr > 
      <td bgcolor='#CCCCCC' > 
        <div align='center'><font size='+1' >項目報名資料，正取、備取(學生可直接輸入座號)</font></div>
      </td>
    </tr>\n" ;

   $main .="</table>

  <p> 
    <input type='submit' name='Submit' value='報名完成'>
    <input type='reset' name='Submit2' value='重設'>
    <input type='hidden' name='o_class_num' value='$class_num'>
    <input type='hidden' name='max_kind' value='$max_kind'>
    <input type='hidden' name='max_get' value='$max_get'>
    <input type='hidden' name='get_arr' value='$data_item'>
    <input type='hidden' name='max_input' value='$max_input'>\n " ;
    /*
    $class_num 班級
    $max_kind  類別
    $max_get   匯出項數
    $max_input 輸入欄位數
    
    $kind[]     各類類別代號
    $kind_max[] 各類別正取人數
    $kind_pmax[]各類別備取人數
    */
    for ($i= 0 ; $i < $max_kind ; $i++) {
        $main .= "<input type='hidden' name='kind[]' value='". $kind_arr[$i][0] ."'>
        <input type='hidden' name='kind_max[]' value='". $kind_arr[$i][2] ."'>
        <input type='hidden' name='kind_pmax[]' value='". $kind_arr[$i][3] ."'> \n" ;
    }    
    $mian .="</form>" ;

  return $main ;
}
