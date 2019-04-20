<?php
// $Id: sum.php 8952 2016-08-29 02:23:59Z infodaes $

include "stud_query_config.php";

//取得最多班的年級
for ($y=1; $y<=6 ; $y++) {
    $ty[0]= $y ;	
    $tc = class_base('',$ty) ;	
    $t = count( $tc ) ;
    if ($maxclass < $t) {
       $maxY= $y ;
       $maxclass = $t   ;
    }   	
}	
// --認證 session 
  sfs_check();


  //$cnum = count($class_name) ;
  $cnum = $maxclass ;

  //取出級任教師名單
  /*
  $query="select a.teach_id , a.name ,b.class_num FROM teacher_base a , teacher_post b where a.teach_id = b.teach_id and  a.teach_condition = '0' and b.post_office = '8' 
  and  b.teach_title_id < 40
  order by b.class_num ";
  */
  //echo $query ;
  $query="select a.teach_id , a.name ,b.class_num FROM teacher_base a , teacher_post b where a.teacher_sn  = b.teacher_sn  and  a.teach_condition = '0' and b.post_office = '8' 
      order by b.class_num ";
  
  $recordSet=$CONN->Execute($query) or die($query);
  if ($recordSet) 
    while ( $row = $recordSet->FetchRow() ) {
      $classn = $row["class_num"] ;
      $teachname = $row["name"] ;
      $y = substr($classn,0,1) ;
      $c = intval(substr($classn,1)) ;
      $class_tea[$y][$c] = $teachname ;
    }
   
  //建立暫存表格，存放近一個月轉入轉出的資料  
  $query = "SELECT *  FROM tmp_stud_move ";  
  $recordSet=$CONN->Execute($query) ;
  if (!$recordSet) {
    $query = "CREATE TABLE tmp_stud_move (
      move_id bigint(20) NOT NULL auto_increment,
      stud_id varchar(20) NOT NULL default '',
      move_kind varchar(10) NOT NULL default '',
      move_date date NOT NULL default '0000-00-00',
      PRIMARY KEY  (move_id)
      );" ;
    $CONN->Execute($query) ;
  }
  //暫存表清空
  $query= " DELETE FROM tmp_stud_move " ;
  $recordSet=$CONN->Execute($query) or die($query);
  
  
  $m = date('m') ;

  $ny = date('Y') ;
  if ($m ==1) {
     $prem= 12 ;
     $prey = $ny-1;  }
  else {
     $prem = $m-1 ;
     $prey = $ny ; 
  }


  //統計前一個月的資料
  $bdate = $prey ."-". $prem ."-01" ;
  $edate = $ny ."-". $m ."-01" ;

  
  //暫存表格
  $query= " DELETE FROM tmp_stud_move " ;
  $recordSet=$CONN->Execute($query) or die($query);  
  $query= " select *  FROM stud_move where move_date >= '$bdate'  and move_kind <>99 " ;
  //echo $query ;
  $recordSet=$CONN->Execute($query) or die($query);  
  while ($row = $recordSet->FetchRow()) {  
    $query2 = " INSERT INTO tmp_stud_move ( move_id , stud_id , move_kind ,  move_date ) 
      VALUES ('0', '$row[stud_id]' , '$row[move_kind]', '$row[move_date]') " ;
    $CONN->Execute($query2) ;
  }     
  
//取得各班男女統計表，上個月在，	
/*
  $sqlstr= " select LEFT(curr_class_num,3) as Tclass  ,stud_sex, count(*) as TC from stud_base
    where  (stud_study_cond = 0 and  create_date < '$edate')
    or (stud_study_cond <> 0 and  update_time >= '$edate')
    group  by Tclass,stud_sex   " ;
*/
  $query= "select LEFT(s.curr_class_num,3) as Tclass ,s.stud_sex , count(*) as TC
            from stud_base s 
            left join tmp_stud_move m on ( m.stud_id = s.stud_id ) 
    where  (s.stud_study_cond = 0  and m.move_date is NULL )  
      or (s.stud_study_cond = 0  and m.move_date <'$edate'   )
    or (s.stud_study_cond <> 0 and  m.move_date >= '$edate' ) 
    group  by Tclass,s.stud_sex   " ;
  
  //echo     $query ;  
  $recordSet=$CONN->Execute($query) or die($query);
  if ($recordSet) 
    while ($row = $recordSet->FetchRow()) {
      $classn = $row["Tclass"]; 
      $sex = $row["stud_sex"] ; 
      $y = substr($classn,0,1) ;
      $c = intval(substr($classn,1)) ;
      $studn[$y][$c][$sex] = $row["TC"] ;
      $studn[$y][$c][0] += $row["TC"] ;
      $Ystudn[$y][$sex] = $Ystudn[$y][$sex]+$row["TC"] ;              //各年級性別統計
      $Ystudn[$y][0] = $Ystudn[$y][0]+$row["TC"] ;  //各年級總數
    }
    //只取普通班
    for ($i=0 ;$i<=2; $i++)
       for($cy=1; $cy<=6;$cy++){ 
           //$mYstudn[$cy][$i] = $Ystudn[$cy][$i]  - $studn[$cy][10][$i] ;    //普通班
           //$mM_all[$i] = $mM_all[$i] + $mYstudn[$cy][$i] ; 	//普通班總人數 
           $M_all[$i] = $M_all[$i] + $Ystudn[$cy][$i] ; 	//全校人數 
          // $S_all[$i] = $S_all[$i] + $studn[$cy][10][$i] ; 	//智優班 
       }    
           

//增減人數部份依年級、類別、男女

  $query = "select m.* , s.stud_sex , LEFT(s.curr_class_num,1) as Tclass ,count(*) as TC
             from tmp_stud_move m ,stud_base s 
             where m.stud_id=s.stud_id 
             and m.move_date >= '$bdate' 
             and m.move_date < '$edate'
             and m.move_kind <> '99'
             group by Tclass, s.stud_sex , m.move_kind  ";
  //echo  $query ;
  $recordSet=$CONN->Execute($query) or die($query);

  if ($recordSet) 
    while ($row = $recordSet->FetchRow() ) {
      $Tclass = $row["Tclass"] ; 
      $sex = $row["stud_sex"] ; 
      $cond = $row["move_kind"] ; 
      $addn[$Tclass][$cond][$sex] = $row["TC"] ;
      $addn[$Tclass][$cond][0] = $addn[$Tclass][$cond][0]+ $row["TC"] ;
      //年級、類別、人數
    }
  for ($y=1 ;$y<=6 ; $y++) {
     for ($cond=1;$cond<=8;$cond++) {
       $kind[$cond][0] += $addn[$y][$cond][0] ;
       $kind[$cond][1] += $addn[$y][$cond][1] ;
       $kind[$cond][2] += $addn[$y][$cond][2] ;
     }

  }
  for ($i= 2; $i>=1 ; $i--){
    $all_in[$i] = $kind[1][$i]+$kind[2][$i]+$kind[3][$i]+$kind[4][$i] ;
    $all_out[$i] = $kind[8][$i]+$kind[6][$i]+$kind[11][$i] ;
  }
  $all_in[0] =   $all_in[1]+ $all_in[2]   ;
  $all_out[0] =   $all_out[1]+ $all_out[2]   ;

/*
取得之前一個月男女總人數統計表	
*/
  $query= "select s.stud_sex ,s.stud_id, s.stud_name , s.stud_study_cond , m.move_date   ,count(*) as TC
            from stud_base s 
            left join tmp_stud_move m on ( m.stud_id = s.stud_id )
    where  (s.stud_study_cond = 0  and m.move_date is NULL  )  
    or (s.stud_study_cond <> 0 and  m.move_date >= '$bdate') 
    group  by s.stud_sex   " ;

  //echo     $query ;  
  $recordSet=$CONN->Execute($query) or die($query);
  if ($recordSet) 
    while ($row = $recordSet->FetchRow() ) {
      $sex = $row["stud_sex"] ; 
      $pre_studn[$sex] = $row["TC"] ;
    }
  $pre_studn[0]= $pre_studn[1] +$pre_studn[2] ;


          

//include ($head);
?> 
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<style type="text/css">
<!--
.trt {  font-size: 16px; text-align: center }
.trs {  font-size: 12px; text-align: center ; background-color: #FFFFFF}
.ts10 {  font-size: 12px; width: 0.5cm; text-align: center}
.ts20 {  font-size: 12px; width: 1.2cm; text-align: center}
.ts30 {  font-size: 12px; text-align: center; width: 1.5cm}
-->
</style>

<body bgcolor="#FFFFFF" text="#000000">
<table width="900" border="0" cellspacing="0" cellpadding="2" class="trs" bordercolor=\"#000000\" >
  <tr class="trt">
 <td>月報</td>
 <td><?php echo $school_long_name ."出席及異動狀況報告表" ?></td>
 <td><?php echo $ny-1911 . "年" .$prem ."月份" ?></td>
</tr>
</table>

<table width="1280" border="1" cellspacing="0" cellpadding="2" class="trs" bordercolor=\"#000000\">
  <tr class="trt"><td colspan="<?php echo $cnum*3+2+15 ?>" >本月份在籍學生數一覽表人數</td>
</tr>
  <tr class="trs"> 
    <td rowspan="2" class="ts20">年級</td>
    <td rowspan="2" class="ts30">\班級<br>
      項別\</td>
<?php
    
    //while(list($tkey,$tvalue)= each ($class_name)) 
    //   echo "<td colspan=\"3\" class=\"ts30\">" . $class_name[$tkey] ."班</td>" ;
    $ty[0] = $maxY ;
    $className = class_base('',$ty)   ;
    foreach( $className as $k=>$v) 
      echo "<td colspan=\"3\" class=\"ts30\">" . substr($v,4) ."</td>" ;
    echo "<td colspan=\"3\" class=\"ts30\">轉入</td>
          <td colspan=\"3\" class=\"ts30\">休學</td>
          <td colspan=\"3\" class=\"ts30\">轉出</td>
          <td colspan=\"3\" class=\"ts30\">總計</td>" ;
    echo "</tr><tr class=\"trs\"> " ;
    //資料
    for ($i= 0 ; $i<$cnum ; $i++) 
      echo "<td >男</td><td >女</td><td >計</td>" ;
    //後半段
    echo "<td >男</td><td >女</td><td >計</td>" ;
    echo "<td >男</td><td >女</td><td >計</td>" ;
    echo "<td >男</td><td >女</td><td >計</td>" ;
    echo "<td >男</td><td >女</td><td >計</td>" ;
   // echo "<td >男</td><td >女</td><td >計</td>" ;
    echo "</tr>\n" ;

    for ($y=1 ; $y<=6 ; $y++) {
      echo "<tr><td rowspan=\"2\" class=\"ts20\">" .$class_year[$y] ."</td>" ;
      echo " <td  class=\"ts30\">在籍數</td>" ;    	
      for ($i= 0 ; $i <$cnum ; $i++) { 
         if ($studn[$y][$i+1][1]) { 
           echo "<td class=\"st10\">" . $studn[$y][$i+1][1] . "</td>" ;
           echo "<td class=\"st10\">" . $studn[$y][$i+1][2] . "</td>" ;
           echo "<td class=\"st10\">" . $studn[$y][$i+1][0] . "</td>" ;
           $allsum[1] +=  $studn[$y][$i+1][1] ;
           $allsum[2] +=  $studn[$y][$i+1][2] ;
         }
         else  { 
           echo "<td class=\"st10\">&nbsp;</td>" ;
           echo "<td class=\"st10\">&nbsp;</td>" ;
           echo "<td class=\"st10\">&nbsp;</td>" ;
         }

      }
      //轉入
      echo "<td>".$addn[$y][2][1] ."&nbsp;</td><td>" . $addn[$y][2][2] ."&nbsp;</td><td>".$addn[$y][2][0] ."&nbsp;</td>" ;
      //休學
      echo "<td>".$addn[$y][6][1] ."&nbsp;</td><td>" .$addn[$y][6][2] ."&nbsp;</td><td>".$addn[$y][6][0] ."&nbsp;</td>" ;
      //轉出
      echo "<td>".$addn[$y][8][1] ."&nbsp;</td><td>" .$addn[$y][8][2] ."&nbsp;</td><td>".$addn[$y][8][0] ."&nbsp;</td>" ;
      //普通班
 
      // echo "<td>". $mYstudn[$y][1]  ."&nbsp;</td><td>" .$mYstudn[$y][2] ."&nbsp;</td><td>".$mYstudn[$y][0] ."&nbsp;</td>" ;
      //全部班
      echo "<td>".$Ystudn[$y][1]  ."&nbsp;</td><td>" .$Ystudn[$y][2] ."&nbsp;</td><td>".$Ystudn[$y][0] ."&nbsp;</td>" ;

      echo "</tr>\n<tr>" ;
      echo "<td  class=\"ts30\">級任</td>" ;
      for ($i= 0 ; $i <$cnum ; $i++) { 
         echo "<td colspan=\"3\" class=\"st30\">" ;
         if ( $class_tea[$y][$i+1] ) echo  $class_tea[$y][$i+1] ."</td>" ;
         else echo "&nbsp;</td>" ;

      }      
      //後半段
      echo "<td >&nbsp;</td><td >&nbsp;</td><td >&nbsp;</td>" ;
      echo "<td >&nbsp;</td><td >&nbsp;</td><td >&nbsp;</td>" ;
      echo "<td >&nbsp;</td><td >&nbsp;</td><td >&nbsp;</td>" ;
      echo "<td >&nbsp;</td><td >&nbsp;</td><td >&nbsp;</td>" ;
      //echo "<td >&nbsp;</td><td >&nbsp;</td><td >&nbsp;</td>" ;
      echo "</tr>\n" ;        
    }  
    echo "<tr class=\"ts30\"><td colspan=". (($cnum+1)*3-1) .">&nbsp;</td>" ;   
    //智優班統計
    //echo "<td>". $S_all[1] . "</td><td>". $S_all[2] . "</td><td>". ($S_all[1]+$S_all[2]) . "</td>" ;
      //轉入
    echo "<td>".$kind[2][1] ."&nbsp;</td><td>" .$kind[2][2] ."&nbsp;</td><td>".$kind[2][0] ."&nbsp;</td>" ;
      //休學
    echo "<td>".$kind[6][1] ."&nbsp;</td><td>" .$kind[6][2] ."&nbsp;</td><td>".$kind[6][0] ."&nbsp;</td>" ;
      //轉出
    echo "<td>".$kind[8][1] ."&nbsp;</td><td>" .$kind[8][2] ."&nbsp;</td><td>".$kind[8][0] ."&nbsp;</td>" ;
      //普通班
    //echo "<td>". $mM_all[1]  ."&nbsp;</td><td>" .$mM_all[2] ."&nbsp;</td><td>".$mM_all[0] ."&nbsp;</td>" ;

      //全部班
    echo "<td>".$M_all[1]  ."&nbsp;</td><td>" .$M_all[2] ."&nbsp;</td><td>".$M_all[0] ."&nbsp;</td>" ;

    echo "</tr>\n" ;
?> 
 
</table>
<table width="900" border="1" cellspacing="0" cellpadding="2" class="trs" bordercolor=\"#000000\">
  <tr class="trs"> 
    <td rowspan="5" >動態</td>
    <td rowspan="5" >異動狀態</td>
    <td rowspan="2" >項別</td>
    <td rowspan="2"  class="ts20">前月末<br>
      在籍數</td>
    <td colspan="6">本月中增加學生數</td>
    <td colspan="7">本月中減少學數</td>
    <td colspan="2">比較</td>
    <td rowspan="2"  class="ts30">本月在籍數</td>
    <td rowspan="2"  class="ts30">缺席總數</td>
    <td rowspan="2"  class="ts30">出席率</td>
    <td rowspan="5" >本月份出席狀況</td>
    <td rowspan="2"  class="ts30">全月缺席狀況</td>
    <td rowspan="2"  class="ts30">出席總數</td>
  </tr>
  <tr> 
    <td class="ts20" >入學</td>
    <td  class="ts20">轉入</td>
    <td  class="ts20">復學</td>
    <td  class="ts20">留級</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20">計</td>
    <td  class="ts20">轉出</td>
    <td  class="ts20">休學</td>
    <td  class="ts20">退學</td>
    <td  class="ts20">死亡</td>
    <td  class="ts20">畢業</td>
    <td  class="ts20">留級</td>
    <td  class="ts20">計</td>
    <td class="ts20">增</td>
    <td  class="ts20">減</td>
  </tr>
  <tr> 
    <td >男</td>
    <td  class="ts20"><?php echo $pre_studn[1] ?></td>
    <td  class="ts20"><?php echo $kind[1][1] ?>&nbsp;</td>
    <td  class="ts20"><?php echo $kind[2][1]+$kind[12][1] ?>&nbsp;</td>
    <td  class="ts20"><?php echo ($kind[3][1]+$kind[4][1]) ?>&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20"><?php echo $all_in[1] ?>&nbsp;</td>
    <td  class="ts20"><?php echo $kind[8][1] ?>&nbsp;</td>
    <td  class="ts20"><?php echo $kind[6][1] ?>&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20"><?php echo $kind[11][1] ?>&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20"><?php echo $all_out[1] ?>&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts30"><?php echo $allsum[1] ?></td>
    <td  class="ts30">&nbsp;</td>
    <td  class="ts30">&nbsp;</td>
    <td  class="ts30">&nbsp;</td>
    <td  class="ts30">&nbsp;</td>
  </tr>
  <tr> 
    <td >女</td>
    <td  class="ts20"><?php echo $pre_studn[2] ?></td>
    <td  class="ts20"><?php echo $kind[1][2] ?>&nbsp;</td>
    <td  class="ts20"><?php echo $kind[2][2]+$kind[12][2]  ?>&nbsp;</td>
    <td  class="ts20"><?php echo ($kind[3][2]+$kind[4][2]) ?>&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20"><?php echo $all_in[2] ?>&nbsp;</td>
    <td  class="ts20"><?php echo $kind[8][2] ?>&nbsp;</td>
    <td  class="ts20"><?php echo $kind[6][2] ?>&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20"><?php echo $kind[11][2] ?>&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20"><?php echo $all_out[2] ?>&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts30"><?php echo $allsum[2] ?></td>
    <td  class="ts30">&nbsp;</td>
    <td  class="ts30">&nbsp;</td>
    <td  class="ts30">&nbsp;</td>
    <td  class="ts30">&nbsp;</td>
  </tr>
  <tr> 
    <td >計</td>
    <td  class="ts20"><?php echo $pre_studn[0] ?></td>
    <td  class="ts20"><?php echo $kind[1][0] ?>&nbsp;</td>
    <td  class="ts20"><?php echo $kind[2][0]+$kind[12][0] ?>&nbsp;</td>
    <td  class="ts20"><?php echo ($kind[3][0]+$kind[3][0]) ?>&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20"><?php echo $all_in[0] ?>&nbsp;</td>
    <td  class="ts20"><?php echo $kind[8][0] ?>&nbsp;</td>
    <td  class="ts20"><?php echo $kind[6][0] ?>&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20"><?php echo $kind[11][0] ?>&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20"><?php echo $all_out[0] ?>&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts20">&nbsp;</td>
    <td  class="ts30"><?php echo $allsum[1]+ $allsum[2] ?></td>
    <td  class="ts30">&nbsp;</td>
    <td  class="ts30">&nbsp;</td>
    <td  class="ts30">&nbsp;</td>
    <td  class="ts30">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="3">備註</td>
    <td colspan="22">本月份上課日數 &nbsp; &nbsp;&nbsp; 日</td>
  </tr>
</table>
<table width="720" border="0" cellspacing="0" cellpadding="0" align="left" >
<tr class="trs">
<td>註冊組長</td>
<td>&nbsp;</td>
<td>教務處主任</td>
<td>&nbsp;</td>
<td>校長</td>
<td>&nbsp;</td>
<td><?php echo $ny-1911 . "年" .$m ."月". date('d') ."日填報" ?></td>
</tr>
</table>


<?php //include ($foot);	?>