<?php
//$Id: sign_form.php 8450 2015-06-10 08:56:34Z brucelyc $

//載入設定檔
require("config.php") ;

// 認證檢查
sfs_check();

$class_year_p = class_base(); //班級

($IS_JHORES==0) ? $UP_YEAR=6:$UP_YEAR=9;//判斷國中小
//增加升學的學校名稱欄位
$rs01=$CONN->Execute("select new_school from grad_stud where 1");
if(!$rs01) $CONN->Execute("ALTER TABLE grad_stud ADD new_school varchar(40)");

	
$curr_year = curr_year() ;
	
$Submit =$_POST['Submit'];
$curr_grade_school =$_POST['curr_grade_school'];

if ($Submit=="同步化") {
	/*
	$sqlstr = "select s.student_sn, s.stud_id, s.curr_class_num, g.grad_sn, g.student_sn as sn  from stud_base as s LEFT JOIN grad_stud as g ON s.stud_id=g.stud_id  where s.stud_study_cond in ('0','15') and s.curr_class_num like '$UP_YEAR%' and g.stud_grad_year='$curr_year'";   
	$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
	while ($row = $result->FetchRow() ) {    
		$stud_id = $row["stud_id"] ;
		$student_sn = $row['student_sn'];
		$grad_sn = $row["grad_sn"];
		$y = substr($row["curr_class_num"],0,1) ;
		$c = substr($row["curr_class_num"],1,2) ;
		if ($grad_sn>0 && $row['sn']==0) {
			$query = "update grad_stud set student_sn='$student_sn' where grad_sn='$grad_sn'";
			$CONN->Execute($query);
			} elseif ($grad_sn==0){ //已存在資料
			$sqlstr = "insert into grad_stud (grad_sn , stud_grad_year , class_year,  class_sort,  stud_id, student_sn, new_school  )
						  values ('0',  '$curr_year','$y', '$c', '$stud_id', '$student_sn',  '')  " ;
			$rs2 =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
			//echo $sqlstr;
		}
	} 
	*/
	//先刪除不在籍的, 再加入不在資料表的 2015-06-09
	$sqlstr = "select student_sn from stud_base where stud_study_cond in ('0','15') and curr_class_num like '$UP_YEAR%'";
	$result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
	$temp_arr = array();
	while ($row = $result->FetchRow()) {
		$temp_arr[] = $row['student_sn'];
	}
	if (count($temp_arr)>0) {
		$temp_str = "'".implode("','",$temp_arr)."'";
		$sqlstr = "DELETE FROM grad_stud WHERE stud_grad_year='$curr_year' and student_sn not in ($temp_str)";
		$result = $CONN->Execute($sqlstr) or user_error("刪除失敗！<br>$sqlstr",256);
	}
	
	$sqlstr = "select * from grad_stud WHERE stud_grad_year='$curr_year'";
	$result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
	$temp_arr = array();
	while ($row = $result->FetchRow()) {
		$temp_arr[] = $row['student_sn'];
	}
	$temp_str = "";
	if (count($temp_arr)>0) {
		$temp_str = "and student_sn not in ('".implode("','",$temp_arr)."')";
	}
	$sqlstr = "select student_sn,stud_id,curr_class_num from stud_base where stud_study_cond in ('0','15') and curr_class_num like '$UP_YEAR%' $temp_str";
	$result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
	$values = "";
	while ($row = $result->FetchRow()) {
		$stud_id = $row["stud_id"];
		$student_sn = $row['student_sn'];
		$y = substr($row["curr_class_num"],0,1) ;
		$c = substr($row["curr_class_num"],1,2) ;
		$values.="('0','$curr_year','$y','$c','$stud_id','$student_sn',''),";
	}
	if (strlen($values)>0) {
		$values = substr($values,0,-1);
		$sqlstr = "insert into grad_stud (grad_sn, stud_grad_year, class_year,  class_sort, stud_id, student_sn, new_school) values $values";
		$rs2 =$CONN->Execute($sqlstr) or user_error("同步化失敗！<br>$sqlstr",256);
		//echo $sqlstr;exit;
	}
	
	/*
	//新的程式碼  先將既有紀錄刪除  再重新依照現在畢業年級的學生重新建立
	$sqlstr = "DELETE FROM grad_stud WHERE stud_grad_year='$curr_year'";   
	$result =$CONN->Execute($sqlstr) or user_error("刪除失敗！<br>$sqlstr",256);
	
	$sqlstr = "select student_sn,stud_id,curr_class_num from stud_base where stud_study_cond in ('0','15') and curr_class_num like '$UP_YEAR%'";   
	$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
	while ($row = $result->FetchRow() ) {
		$stud_id = $row["stud_id"] ;
		$student_sn = $row['student_sn'];
		$y = substr($row["curr_class_num"],0,1) ;
		$c = substr($row["curr_class_num"],1,2) ;

		$values.="('0','$curr_year','$y','$c','$stud_id','$student_sn',''),";		
	}
	$values=substr($values,0,-1);
	$sqlstr = "insert into grad_stud (grad_sn , stud_grad_year , class_year,  class_sort,  stud_id, student_sn, new_school  ) values $values" ;
	$rs2 =$CONN->Execute($sqlstr) or user_error("同步化失敗！<br>$sqlstr",256); 
	*/
}


if (($Submit=="設定學校") and $curr_grade_school) {
   //預設全部學生  升學到特定學校  
   //$sqlstr = "select s.stud_id ,s.curr_class_num , g.grad_sn  from stud_base as s ,grad_stud as g 
   //         where  s.stud_id=g.stud_id and  s.stud_study_cond = '0'  and s.curr_class_num like '$UP_YEAR%' ";
   $sqlstr = "select s.stud_id ,s.curr_class_num , g.grad_sn  from stud_base as s ,grad_stud as g 
            where  s.student_sn=g.student_sn and  s.stud_study_cond in ('0','15') and s.curr_class_num like '$UP_YEAR%' ";
   //搜尋條件將s.stud_id=g.stud_id修改成 s.student_sn=g.student_sn，避免畢業生十年學號重複問題  modify by kai,103.4.30 
                
   $result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
   while ($row = $result->FetchRow() ) {    
        $stud_id = $row["stud_id"] ;
        $grad_sn = $row["grad_sn"];
        $y = substr($row["curr_class_num"],0,1) ;
        $c = substr($row["curr_class_num"],1,2) ;
        if ($grad_sn>0) { //已存在資料
          $sqlstr = " update grad_stud set stud_grad_year = '$curr_year', class_year = '$y' , class_sort = '$c' ,new_school = '$curr_grade_school'
                        where grad_sn = '$grad_sn' ";
        //else 
         // $sqlstr = "insert into grad_stud (grad_sn , stud_grad_year , class_year,  class_sort,  stud_id,new_school  )
         //             values ('0',  '$curr_year','$y', '$c', '$stud_id',  '$curr_grade_school')  " ;
        	$rs2 =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
	}
   }              

}


if ($Submit=="依班級座號設定証書字號" or $Submit=="依學號設定証書字號") {
	
	if($Submit=="依班級座號設定証書字號") $order="order by s.curr_class_num"; else  $order="order by s.stud_id";
	
   $kword = "(".$curr_year.")".$grade_word;  //九一南營畢字
   $m_str = "%0" . $grade_num_len ."d" ;        //前方補 0
   
   $id =0 ;

   //$sqlstr = "select s.stud_id ,s.curr_class_num , g.grad_sn  from stud_base as s  LEFT JOIN grad_stud as g ON s.stud_id=g.stud_id 
   //         where s.stud_study_cond = '0'  and s.curr_class_num like '$UP_YEAR%' order by s.curr_class_num  ";    
   $sqlstr = "select s.stud_id ,s.curr_class_num , g.grad_sn  from stud_base as s , grad_stud as g 
            where s.stud_study_cond = '0' and  s.student_sn=g.student_sn  and s.curr_class_num like '$UP_YEAR%' $order";
            //搜尋條件將s.stud_id=g.stud_id修改成 s.student_sn=g.student_sn，避免畢業生十年學號重複問題  modify by kai,103.4.30    

   $result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
   while ($row = $result->FetchRow() ) {    
        $stud_id = $row["stud_id"] ;
        $grad_sn = $row["grad_sn"];
        $y = substr($row["curr_class_num"],0,1) ;
        $c = substr($row["curr_class_num"],1,2) ;        
        $id ++ ;
        $id_word = sprintf($m_str , $id ) ;
        if ($grad_sn>0) { //已存在資料
          $sqlstr = " update grad_stud set stud_grad_year = '$curr_year', class_year = '$y' , class_sort = '$c' ,
                      grad_word = '$kword' , grad_num = '$id_word'
                        where grad_sn = '$grad_sn' ";
        //else 
        //  $sqlstr = "insert into grad_stud (grad_sn , stud_grad_year , class_year,  class_sort,  stud_id , grad_word , grad_num )
          //            values ('0',  '$curr_year','$y', '$c', '$stud_id', '$kword' , '$id_word')  " ;
        	$rs2 =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;       
	}
   }                                   
    
}        
        
//取得升學學校
$grade_school = get_grade_school_table();
$def_grade_school = get_grade_school();

// 查看是否已有資料

$query = "SELECT COUNT(*) AS cc FROM grad_stud WHERE stud_grad_year = '$curr_year'";
$res = $CONN->Execute($query) or die($query);
$cc = $res->fields['cc'];

head() ;  

print_menu($menu_p);
?>

<table width=100% bgcolor="#CCCCCC" >
  <tr><td align="center">
<div align="center">

        <p><b>
          <?php echo curr_year(); ?>
          學年度畢業學生一覽表</b> 
        <form name="form1" method="post" action="<?php echo $PHP_SELF ?>">
          <table width="70%" cellspacing='0'  cellpadding='2' bordercolorlight='#333354' bordercolordark='#FFFFFF' border='1' bgcolor='#99CCCC' >
	  <tr> 
              <td bgcolor="#99CCCC"> 
                <div align="center">同步化應屆學生資料
                  <input type="submit" name="Submit" value="同步化" onClick="return confirm('同步化後~~\r\n本學年度原有的畢業生作業資料不會被刪除\r\n只會依據目前所有應屆[在籍]與[在家自行教育]學生名單未同步化的進行增刪\r\n\r\n確定要這麼做？')">
                  </div>
              </td>
            </tr>
	    
            <tr> 
              <td bgcolor="#99CCCC"> 
                <div align="center">全部預設升學至 
                  <select name="curr_grade_school">
                    <option value=''>---</option>
                    <?php
                    foreach($def_grade_school as $tkey => $tvalue ) 
	                echo  sprintf("<option value='%s'>%s</option>\n",$tvalue,$tvalue);

                  ?>
                  </select>
                  <input type="submit" name="Submit" value="設定學校" <?php if ($cc==0) echo "disabled" ?> >
                </div>
              </td>
            </tr>
            <tr> 
              <td bgcolor="#99CCCC"> 
                <div align="center"> 
                  <input type="submit" name="Submit" value="依班級座號設定証書字號" <?php if ($cc==0) echo "disabled" ?>>
                  　　<input type="submit" name="Submit" value="依學號設定証書字號" <?php if ($cc==0) echo "disabled" ?>>
                  <br></div>
              </td>
            </tr>
          </table>
        </form>
        <table cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="80%" border="1" >

      <tr class=title_sbody1>
        <td  align="center" >
          班級
        </td>
        <td  align="center" >
          男生人數</td>
        <td  align="center"  >
          女生人數</td>
          <?php
          	if (count($grade_school)>0){
		
          		foreach ( $grade_school as   $tkey => $tvalue  ){   
          			echo '<td colspan=2 align="center" >';
	          		echo '升學至 '.$tvalue.'</td>';
        	  	}
		}	
          ?>
        <td  align="center" width="77" >合計</td>
      </tr>

<?php
	//$query = "select count(stud_sex=1) as boy , count(stud_sex=2) as girl, count(*) as cc,substring(curr_class_num,1,3)as classn  ";
        $sqlstr = " select sum(s.stud_sex=1) as boy , sum(s.stud_sex=2) as girl, count(*) as cc,substring(s.curr_class_num,1,3) as classn  "; 

                  
if (count($grade_school)>0){
	foreach ( $grade_school as   $tkey => $tvalue  ){  
		$tvalue = addslashes($tvalue); 
		$sqlstr .= ",sum(g.new_school ='$tvalue' and s.stud_sex=1) as m_$tkey ";
		$sqlstr .= ",sum(g.new_school ='$tvalue' and s.stud_sex=2) as l_$tkey ";
	}
}

//$sqlstr .= "from stud_base as s  LEFT JOIN grad_stud as g ON s.student_sn=g.student_sn 
//            where g.stud_grad_year=".curr_year()." and s.stud_study_cond = '0'  and s.curr_class_num like '$UP_YEAR%' group by classn ";
$sqlstr .= "from stud_base as s  LEFT JOIN grad_stud as g ON s.student_sn=g.student_sn 
            where g.stud_grad_year=".curr_year()." and s.stud_study_cond in ('0','15') and s.curr_class_num like '$UP_YEAR%' group by classn ";
//修改搜尋條件s.stud_study_cond = '0'為s.stud_study_cond in ('0','15')，修正同步化後在家教育學生未顯現的錯誤
//echo $sqlstr ;

$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
while ($row = $result->FetchRow() ) {
	$boy = $row[boy];
	$girl = $row[girl];
	$tol = $row[cc];
	$classn = $row["classn"] ;
	$classn = $class_year_p[$classn];
	if ($i++ % 2 ==0)
		echo '<tr class=nom_1>' ;
	else
		echo '<tr class=nom_2>' ;
	$tboy += $boy;
	$tgirl += $girl;
	$ttol += $tol;
?>


        <td align="center"  >
          <?php echo $classn ?>
        </td>
        <td align="center"  >
          <?php echo $boy ?>
        </td>
        <td align="center"  >
         <?php echo $girl ?>
        </td>
         <?php
          	if (count($grade_school)>0) {
         		reset($grade_school);
	         	$x =0;
        	  	while(list($tkey,$tvalue)= each ($grade_school)) {
          			$mmm ="m_$tkey" ;
          			$mmm = $row[$mmm];
		      		$lll ="l_$tkey" ;
          			$lll = $row[$lll];
		         	$a_mmm[$x] += $mmm;
          			$a_lll[$x++] += $lll;          		
	          		echo '<td align="center" >';
        	 		echo $mmm;
        			echo '</td>';
        			echo '<td align="center"  >';
	         		echo $lll;
        			echo '</td>';
          		}          		
		}
         ?>
        <td align="center" >
           <?php echo $tol ?>
        </td>
      </tr>
  
<?php 
}
?>
       <tr class=title_sbody1>
        <td align="center" >
          合計
        </td>
        <td align="center" >
          <?php echo $tboy ?></td>
        <td align="center" >
          <?php echo $tgirl ?></td>
         <?php 
         for ($i=0;$i<count($grade_school);$i++) { 
        	echo '<td align="center" >'.$a_mmm[$i].'</td>';
        	echo '<td align="center" >'.$a_lll[$i].'</td>';
         }
          ?>
        <td align="center" ><?php echo $ttol ?></td>
      </tr>

  </table>
</div>
</table>
<?php
foot() ;
?>
