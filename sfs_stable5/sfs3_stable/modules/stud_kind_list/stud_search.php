<?php
// $Id: stud_search.php 5310 2009-01-10 07:57:56Z hami $

  //載入設定檔
  require("config.php") ;
 
  // 認證檢查
  sfs_check();
 
  $c_curr_seme = sprintf ("%03s%s",curr_year(),curr_seme()); //現在學年學期  
  $class_name_arr = class_base() ;	//班級陣列
  
  head("搜尋");
  //選單
  print_menu($menu_p);
  //-----------------------------------------------------------
?>

<form name="form1" method="post" action="<?php echo $PHP_SELF ?>">
  <table width=60% bgcolor="#FDDDAB" align="center" border="1" cellspacing="0" >
    <tr>
      <td align=right width="26%"> 姓 名：<br>
        (部份姓名)</td>
      <td width="74%"> 
        <input type="text" name="searchname"></td></tr>
  <tr>
      <td align=right width="26%"> 學 號：</td>
      <td width="74%"> 
        <input type="text" name="search_id">  </td>
  </tr>
  <tr>
      <td align=right width="26%"> 監護人：</td>
      <td width="74%"> 
        <input type="text" name="search_f_name">  </td>
  </tr>  
  <tr>
      <td align=right width="26%"> 電話：</td>
      <td width="74%"> 
        <input type="text" name="search_phon">  </td>
  </tr>    
  <tr>
      <td align=right width="26%"> 地址：</td>
      <td width="74%">
        <input type="text" name="search_town">
      </td>
  </tr> 
  <tr>
      <td align=right width="26%"> 
        <input type="submit" name="Submit" value="送出"></td>
      <td width="74%"> 
        
      </td>
  </tr></table>  
</form>

<?php
//-----------------------------------
  //座號、姓名、生日、地址、電話、家長、家長工作、工作電話
  
  $Submit =$_POST['Submit'];
  $searchname =$_POST['searchname'];
  $search_id =$_POST['search_id'];
  $search_f_name =$_POST['search_f_name'];
  $search_phon =$_POST['search_phon'];
  $search_town =$_POST['search_town'];
  $search_village =$_POST['search_village'];

  
  $sql_select = "select s.stud_id, s.stud_name, s.stud_person_id , 
                  s.stud_study_cond , s.curr_class_num ,
                  d.guardian_name ,d.fath_name ,d.moth_name 
                  from stud_base as s  LEFT JOIN stud_domicile as d ON s.stud_id=d.stud_id";  
  //$sql_select .= " where s.stud_study_cond = 0 " ;
                   
  if ($Submit =="送出") {
	
    if (trim($searchname)<>""){	
        //學生姓名
   	    $searchname= trim($searchname) ;
   	    //echo $searchname ;
        $searchname = addslashes($searchname);

        $sqlstr = " and s.stud_name like '%".($searchname)."%'"  ;

        //echo $sql_select ;
    }     
    elseif (trim($search_id)<>""){	
        //學號
   	    $search_id= trim($search_id) ;
        $sqlstr = " and   s.stud_id like '%$search_id%' "  ;

    }   
    elseif (trim($search_f_name)<>"") {
        //家長姓名
   	    $search_f_name= trim($search_f_name) ;
        $search_f_name = addslashes($search_f_name);

   	    $sqlstr = " and ( d.guardian_name  like '%" .$search_f_name."%' 
   	                 or  d.fath_name   like '%" .$search_f_name."%' 
   	                or  d.moth_name    like '%" .$search_f_name."%' ) "  ;

        
    }	 
    elseif (trim($search_phon)<>"") {
        //家中電話
      
   	    $search_phon= trim($search_phon) ;
   	    $sqlstr = " and ( s.stud_tel_1   ='$search_phon' 
   	            or  s.stud_tel_2   ='$search_phon'  
   	            or  s.stud_tel_3  ='$search_phon' ) "  ;

    }	     
    elseif ($search_town) {
        //地址
        $search_town = trim($search_town) ;
        $search_town = addslashes($search_town);
        if ($search_town)  
           $sqlstr =  " and  ( s.stud_addr_1   like '%$search_town%' or s.stud_addr_2   like '%$search_town%' ) " ;

    }
    
  }	
   if ($sqlstr) {
   	$sel_year=curr_year();
   	$sel_seme=curr_seme();
   	$query="select * from school_class where year='$sel_year' and semester='$sel_seme'";
   	$res=$CONN->Execute($query);
   	while (!$res->EOF) {
   		$cclass[$res->fields[c_year]][$res->fields[c_sort]]=$res->fields[c_name];
   		$res->MoveNext();
   	}
   	$sqlstr=substr($sqlstr,4);
        $sql_select .= " where $sqlstr  order by stud_study_cond , s.curr_class_num , stud_id ";
        //echo  $sql_select ;
        echo "<table align=center >";
        echo "<tr></td>學生搜尋結果列表</td></tr><hr>";

       echo "<tr>";
       echo "<td align=center>學號</td><td align=center>班別</td><td align=center>座號</td><td align=center>姓名</td><td align=center>父親</td><td align=center>母親</td><td align=center>監護人</td> ";
       echo "</tr>";

       $result =$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256) ; 
       $i =0;
       while ($row =  $result->FetchRow() ) {
        	$stud_id = $row["stud_id"];
        	$stud_name = $row["stud_name"];
        	$stud_person_id = $row["stud_person_id"];
        	$stud_study_cond  = $row["stud_study_cond"] ;
        	
        	$class_num_curr = $row["curr_class_num"];		//目前班級、座號

        	$classid = intval(substr($class_num_curr,0,3));	//取得班級	
        	$class_name_arr[$classid] ;
        	$s_num = intval (substr($class_num_curr,-2));	//座號
        	  	
       	
        	$d_guardian_name =$row["guardian_name"]  ;
        	$fath_name =$row["fath_name"]  ;
        	$moth_name =$row["moth_name"]  ;

                echo ($i%2 == 1) ? "<tr  BGCOLOR=\"#E2E9F8\" >" : "<tr BGCOLOR=\"#E6F7E2\">";
		echo "<td><a href=\"stu_list.php?stud_id=$stud_id\">$stud_id</a></td>";
        	//echo "<td>" .$class_year[$s_year] . $class_name[$s_class]."班</td>";
        	echo "<td>". $class_name_arr[$classid] ."</td>";
        	echo "<td align=right>$s_num</td>";	
        	echo "<td>$stud_name</td>";
       	
        	
        	echo "<td>$fath_name</td>";
        	echo "<td>$moth_name</td>";
        	echo "<td>$d_guardian_name</td>";
                $s_classnum = substr($class_num_curr,0,3);	//班級代號

        	echo "<td>" ;
            if ($stud_study_cond==0) {

	        $c_curr_class = sprintf("%03s_%s_%02s_%02s",curr_year(),curr_seme(),substr($class_num_curr,0,1),substr($class_num_curr,1,2));
                echo "<a href=\"../stud_base/stud_base.php?stud_id=$stud_id&c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme\">基本資料</a>" ;
            }else 
               echo "(非在學)" ;  
            echo "</td>" ;
            
		    echo "</tr>\n";
		    $i++;
          };
          echo "</table>";

   }     
foot();
?>