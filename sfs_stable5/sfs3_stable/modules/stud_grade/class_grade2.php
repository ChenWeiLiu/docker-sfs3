<?php
//$Id: class_grade2.php 8364 2015-03-23 07:28:21Z chiming $
//載入設定檔
require("config.php") ;

// 認證檢查
sfs_check();

($IS_JHORES==0) ? $UP_YEAR=6:$UP_YEAR=9;//判斷國中小

//查詢當學期編班情形
$curr_year = curr_year();
$curr_seme = curr_seme();

$key =$_POST['key'];
$sel_year =$_POST['sel_year'];
$stud_id =$_POST['stud_id'];
$sel_school =$_POST['sel_school'];
$txtschool =$_POST['txtschool'];


//按鍵處理
switch ($key){
	case  "確定" :
	        $y = substr($sel_year,0,1) ;
                $c = substr($sel_year,1,2) ;
	        
	        //升學校名
	        if ( $txtschool) 
	           $newschoolName = $txtschool ;	
	        elseif ($sel_school)
		   $newschoolName = $sel_school ;

		if ( $newschoolName <>'') {  
      		   for($i=0;$i<count($stud_id);$i++) {
      		        //修改
      		        $id_arr = split("-" , $stud_id[$i]) ;
      		  	$move_stud_id = $id_arr[0] ;
                        $grad_sn = $id_arr[1] ;
                        if ($grad_sn>0) 
                           $sqlstr = " update grad_stud set new_school =  '$newschoolName' ,class_year = '$y',  class_sort ='$c'
                            where  grad_sn ='$grad_sn' " ;
                        else 
                           $sqlstr = " insert into grad_stud (grad_sn , stud_grad_year , class_year,  class_sort,  stud_id,new_school  )
                                values ('0',  '$curr_year','$y', '$c', '$move_stud_id',  '$newschoolName')  " ;   
                        //echo  $sqlstr ."<br>" ;       
			$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;  
		   }
		}

   	break;	
}



$grade_school_p = get_grade_school() ;

//印出檔頭
head();

print_menu($menu_p);

if (!$s_year && !$sel_year)
	$sel_year= $UP_YEAR . "01"; //六年級第一班 


?>
<script language="JavaScript">
<!--
   function CheckAll()
   {
      for (var i=0;i<document.myform.elements.length;i++)
      {
         var e = document.myform.elements[i];
            e.checked = document.myform.allchk.checked;
      }
   }

//-->
</script>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr><td valign=top bgcolor="#CCCCCC" align=center >
<table width="80%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td>  
  <form name ="myform" action="<?php echo $PHP_SELF ?>" method="post" >
   <table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"   class=main_body >	
   <tr>
	
	<td class=title_mbody colspan=4 align=center >
	<?php 
		
		echo sprintf("%d學年第%d學期 ",$curr_year,$curr_seme);
		$sel1 = new drop_select(); //選單類別
		$sel1->s_name = "sel_year"; //選單名稱		
		$sel1->id = $sel_year;		
		$sel1->has_empty = false;
		$sel1->arr = class_base("",array($UP_YEAR)); //內容陣列(六個學年)
		$sel1->is_submit = true;
		$sel1->bgcolor = "#DDFFEE";
		$sel1->font_style ="font-size: 15px;font-weight: bold";
		$sel1->do_select();
	?> 升學作業 &nbsp; <input name="allchk" type="checkbox" value="1"
   onClick="CheckAll();">全選</td>
   <tr class=title_sbody1 ><td align=center>
   座號</td><td align=center>學號</td><td align=center>姓名</td><td align=center>升學學校</td></tr>
   </tr>
   	<?php
   	  //學號、姓名、升學
          $sqlstr = "select s.stud_id , s.stud_name ,s.curr_class_num ,
             g.grad_sn , g.new_school  from stud_base as s ,grad_stud as g where g.stud_grad_year=".curr_year()." and s.stud_id=g.stud_id
             and s.stud_study_cond in ('0','15') and s.curr_class_num like '$sel_year%' order by s.curr_class_num ";
          $result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;    	

	  while ($row = $result->FetchRow() ) {
	        $stud_id = $row['stud_id'] ;
	        $stud_name = $row['stud_name'] ;
	        $curr_class_num = $row['curr_class_num'] ;
	        $grad_sn = $row['grad_sn'] ;
	        $new_school = $row['new_school'] ;
	
			$sel1->s_name = "change_class_$stud_id"; //選單名稱
			echo ($i++ % 2 ==0)? "<tr class=nom_1>":"<tr class=nom_2>";
   			echo "<td align=center><input type='checkbox' name='stud_id[]' value='$stud_id-$grad_sn'>".substr($curr_class_num,-2)."</td>"; 
   			echo "<td align=center>$stud_id</td>"; 
   			echo "<td align=center>$stud_name</td>";
   			echo "<td align=center>"; 
   			echo $new_school ;
   			echo "</td>";   			
   			echo "</tr>\n";
   	 }
   	?>

	</table>
</td>
    <td valign="top">勾選的學生就讀學校：<br>
                <select name="sel_school">
                    <option value=''>---</option>
                    <?php
                    foreach($grade_school_p as $tkey => $tvalue ) 
	                echo  sprintf("<option value='%s'>%s</option>\n",$tvalue,$tvalue);
                  ?>
                  </select>    
		
      <input type=submit name=key value="確定"><br><br>
      如果選單中沒有，可直接指定:<br><input type="text" name="txtschool">
    </td>
  </tr>
</table>	
</form>
  　</td>
  </tr>
</table>

<?php
foot();
?>
