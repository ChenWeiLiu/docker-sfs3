<?php
  // $Id: fixed.php 8984 2016-10-11 03:10:41Z chiming $
  require "config.php" ;
 

  //sfs2 升級 sfs3 欄位調整
  $rs01=$CONN->Execute("select userid from fixedtb where 1");
  if (!$rs01) $CONN->Execute(" ALTER TABLE `fixedtb` ADD `userid` VARCHAR( 12 ) NOT NULL ");
 
  $rs01=$CONN->Execute("select teacher_sn from fixed_check where 1");
  if (!$rs01) $CONN->Execute("ALTER TABLE `fixed_check` ADD `teacher_sn` INT DEFAULT '0' NOT NULL ");
 
  $rs01=$CONN->Execute("select Email_list from fixed_kind where 1");
  if (!$rs01) $CONN->Execute(" ALTER TABLE `fixed_kind` ADD `Email_list` VARCHAR( 100 ) NOT NULL  ");
  
  
  //$debug = 1;
  $showunit = $_REQUEST['showunit'];    
  $showmode = $_REQUEST['showmode'];    
  $showpage = $_REQUEST['showpage'];  
  $user_limited = $_REQUEST['user_limited'];  
 
  
  // 預設顯示情形 
  if (!isset($showunit)) $showunit= "" ; //全部單位
  if (!isset($showmode)) $showmode= -1 ;  //顯示全部
  
  
  
  //計算頁數
    //$sqlstr = "SELECT ID,even_T,even_doc,unitId,user,even_date,even_mode,rep_date,rep_user,rep_doc,rep_mode,userid FROM $tbname  " ;
    $sqlstr = "SELECT count(*) FROM $tbname  " ;
 if ($showunit) {		//有指定顯示單位  
    //$showunit=intval($showunit);  
    //$sqlstr .=  " where unitId = '$showunit' " ;
    $sqlstr .=  " where unitId = ? " ;	
    //if ($showmode>=0){$showmode=intval($showmode);$sqlstr .=  " and rep_mode = '$showmode' " ;}
	if ($showmode>=0) $sqlstr .=  "  and rep_mode = ? " ;
  }
  else 				//有指定顯示特定資料(尚待處理、處理中、已修復)
  {
    //if ($showmode>=0) {$showmode=intval($showmode);$sqlstr .=  " where rep_mode = '$showmode' " ;}
	if ($showmode>=0) $sqlstr .=  " where rep_mode = ? " ;
  }	
		//增加教師過濾
	if($user_limited) $sqlstr .=  (strpos($sqlstr, ' where ')?' and':' where')." user = ? " ;
    
  //$result1 = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
  
 ///mysqli		  
$mysqliconn = get_mysqli_conn();
$stmt = "";
$stmt = $mysqliconn->prepare($sqlstr);

 if ($showunit) { 
 	$stmt->mbind_param('s',$showunit);
    if ($showmode>=0)$stmt->mbind_param('i',$showmode);
  }
  else 
  {
    if ($showmode>=0)$stmt->mbind_param('i',$showmode);
  }
if($user_limited)$stmt->mbind_param('s',$user_limited);
$stmt->execute();
$stmt->bind_result($totalnum);

$stmt->fetch();
$stmt->close();

///mysqli

  if ($totalnum) {
    $totalpage =ceil( $totalnum / $msgs_per_page) ;	//總頁數
	
  }  
  if (!$totalpage) $totalpage= 1 ;  //無資料，總頁數1，顯示第一頁
  if (!$showpage)  $showpage = 1 ;  
  
  //讀取資料
    $sqlstr = "SELECT ID,even_T,even_doc,unitId,user,even_date,even_mode,rep_date,rep_user,rep_doc,rep_mode,userid FROM $tbname  " ;
 if ($showunit) {		//有指定顯示單位  
    //$showunit=intval($showunit);  
    //$sqlstr .=  " where unitId = '$showunit' " ;
    $sqlstr .=  " where unitId = ? " ;	
    //if ($showmode>=0){$showmode=intval($showmode);$sqlstr .=  " and rep_mode = '$showmode' " ;}
	if ($showmode>=0) $sqlstr .=  "  and rep_mode = ? " ;
  }
  else 				//有指定顯示特定資料(尚待處理、處理中、已修復)
  {
    //if ($showmode>=0) {$showmode=intval($showmode);$sqlstr .=  " where rep_mode = '$showmode' " ;}
	if ($showmode>=0) $sqlstr .=  " where rep_mode = ? " ;
  }	
	//增加教師過濾
	if($user_limited) $sqlstr .=  (strpos($sqlstr, ' where ')?' and':' where')." user = ? " ;

  
  $sqlstr .= ' order By ID DESC ' ;
  $sqlstr .= ' LIMIT ' . ($showpage-1)*$msgs_per_page . ', ' . $msgs_per_page  ;  
  //echo $sqlstr ;
  //$result =  $CONN->PageExecute("$sqlstr", $msgs_per_page , $showpage );

$stmt = $mysqliconn->prepare($sqlstr);
 if ($showunit) { 
 	$stmt->mbind_param('s',$showunit);
    if ($showmode>=0)$stmt->mbind_param('i',$showmode);
  }
  else 
  {
    if ($showmode>=0)$stmt->mbind_param('i',$showmode);
  }
if($user_limited)$stmt->mbind_param('s',$user_limited); 
$stmt->execute();
$stmt->bind_result($ID,$even_T,$even_doc,$unitId,$user,$even_date,$even_mode,$rep_date,$rep_user,$rep_doc,$rep_mode,$userid);
 

  /*
  $sqlstr = "SELECT * FROM $tbname  " ;
  if ($showunit) {		//有指定顯示單位
    $sqlstr .=  " where unitId = '$showunit' " ;
    if ($showmode>=0) $sqlstr .=  " and rep_mode = '$showmode' " ;
  }
  else 				//有指定顯示特定資料(尚待處理、處理中、已修復)
    if ($showmode>=0) $sqlstr .=  " where rep_mode = '$showmode' " ;
	
		//增加教師過濾
	if($user_limited) $sqlstr .=  (strpos($sqlstr, ' where ')?' and':' where')." user = '$user_limited' " ;

    
  $result1 = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
  if ($result1) {
	  
	 
    $totalnum = $result1->RecordCount() ;		//總筆數
    $totalpage =ceil( $totalnum / $msgs_per_page) ;	//總頁數
	//echo $totalnum;
  }  
  if (!$totalpage) $totalpage= 1 ;  //無資料，總頁數1，顯示第一頁
  if (!$showpage)  $showpage = 1 ;  
  
  
  //讀取資料
  $sqlstr .= ' order By ID DESC ' ;
  //$sqlstr .= ' LIMIT ' . ($showpage-1)*$msgs_per_page . ', ' . $msgs_per_page  ;  
  if ($debug) echo $sqlstr ;
  $result =  $CONN->PageExecute("$sqlstr", $msgs_per_page , $showpage );
  */
  
  $date_diff=$date_diff?$date_diff:120;
  $user_list="SELECT user,count(*) AS amount FROM fixedtb WHERE datediff(CURDATE(),even_date)<=$date_diff GROUP BY user ORDER BY amount DESC";
  $result_list =  $CONN->Execute($user_list) or user_error("讀取失敗！<br>$user_list",256) ;
  $user_select="<select name='user_limited' onchange='this.form.submit();'><option value=''></option>";
	while(!$result_list->EOF){
		$user_name=$result_list->fields['user'];
		$user_counter=$result_list->fields['amount'];
		$selected=$user_limited==$user_name?'selected':'';
		$user_select.="<option value='$user_name' $selected>$user_name($user_counter)</option>";
		$result_list->MoveNext();
	}
  $user_select.="</select>";
  
  head("維修通報") ;
  print_menu($menu_p);

?>

<style type="text/css">
<!--
.tr1 {  text-align: center; white-space: nowrap; font-size: 10pt}
.tr2 {  background-color: #faeaea; text-align: center; white-space: nowrap; font-size: 10pt}
.trtop {  font-weight: bold; background-color: #CCCCFF; background-position: center; white-space: nowrap ;text-align: center}


-->
</style>


<body bgcolor="#FFFFFF">
<form name="myform" method="get" action="fixed.php"  >
  <h1 align="center" nowrap>報修系統 </h1>
  <table width="95%" border="0" cellspacing="0" cellpadding="4" align="center" bgcolor="#FFCCCC">
    <tr > 
      <td nowrap>◎單位： 
        <select name="showunit" onChange="this.form.submit()">
          
          <?php 
            //顯示單位名稱
            if ($showunit=="") echo "<option value='' selected>全部單位</option> ";
            else echo "<option value='' >全部單位</option> ";
            foreach( $unitstr as $key => $value) {
                
               $chkstr = ($key==$showunit) ? "selected" : "" ;
               echo "<option value='$key' $chkstr>$value</option> \n"  ;
            }    
                        
     
           ?>         
        </select>
        　◎處理情形： 
        <select name="showmode" onChange="this.form.submit()">
          <?php 
            //處理情形
            if ($showmode== -1) echo "<option value=\"-1\" selected>全部</option>" ;
            else echo "<option value=\"-1\" >全部</option>" ;
            foreach( $checkmode as $key => $value) {
                
               $chkstr = ($key==$showmode) ? "selected" : "" ;
               echo "<option value='$key' $chkstr>$value</option> \n"  ;
            }              
     
           ?>          
        </select>
		　◎報修者限定
		 <?php 
			//報修者限定
			echo "( $date_diff 日內 )：".$user_select;
		?> 
      </td><td >移動頁：  
        <select name="showpage" onChange="this.form.submit()" >
          <?php 
            //跳頁
            for ($i=1 ;$i<=$totalpage;$i++) {
    	      if ($i==$showpage) echo "<option value=\"$i\" selected>跳到第" ;
              else echo "<option value=\"$i\">跳到第" ;
              echo  $i . "頁 </option> \n" ;
            }
          ?>            
        </select>            
      </td>
      <td nowrap><a href="fixedadmin.php">填報</a></td>
    </tr>
  </table>
  <table width="95%" border="1" cellspacing="0" cellpadding="4" bordercolorlight="#CCCCCC" bordercolordark="#FFFFFF" align="center">
    <tr  class="trtop" > 
      <td nowrap >編號</td>
      <td nowrap >日期</td>
      <td nowrap >報修內容</td>
      <td nowrap >等級</td>
      <td nowrap >填報人</td>

      <td nowrap >通知單位</td>
      <td nowrap >回覆者</td>
      <td  nowrap >日期</td>
      <td nowrap >處理回覆</td>
    </tr>
<?php

  //列出各筆資料
  
  
  //if ($result)	
  //while ($nb = $result->FetchRow() ) {  
	if ($totalnum)
	while ($stmt->fetch()) {	  

       //$user = $nb[user] ;
       //$rep_mode= $nb[rep_mode];
        
      if ($rowi) {	//隔行分色判斷
        echo '<tr class="tr2"> ' ; 
        $rowi = 0 ; }   
      else {   
        echo '<tr class="tr1"> ' ; 
        $rowi = 1 ; } 
      echo "<td>" ; 
      
      echo "<img src='$chk_image[$rep_mode]' alt='事件類別圖示'>" ;
      //echo " $nb[ID] </td> \n" ;
	  echo " $ID </td> \n" ;
      //填表日期

      //echo "<td> $nb[even_date]</td> \n" ;
      echo "<td> $even_date</td> \n" ;	  
      //事主旨
	  
      //echo "<td ><a href=\"fixedview.php?id=$nb[ID]\">$nb[even_T]</a></td> \n" ;
      echo "<td ><a href=\"fixedview.php?id=$ID\">$even_T</a></td> \n" ;
     
	 //嚴重情形
      //$ti  =$nb[even_mode] ;
	  $ti  =$even_mode;
      echo "<td ><img src='$mode_image[$ti]' alt='事件等級圖示'>$evenmode[$ti]</td> \n" ;      
      
      $edit_link ='' ;
      //編修連結
      if ($_SESSION['session_log_id'] ) 
         //if (!strnatcasecmp($nb[userid] , $_SESSION['session_log_id']) and ($rep_mode <> 2))
           if (!strnatcasecmp($userid , $_SESSION['session_log_id']) and ($rep_mode <> 2))          
	        $edit_link = "<a href=\"fixedadmin.php?do=edit&id=$ID\"><img src=\"images/edit.gif\" alt='修改通報內容' title='修改通報內容' border=\"0\"> </a>\n" ; 
           
      /*
      else     
         if ($rep_mode <> 2) //未結案
            $edit_link = "<a href=\"fixedadmin.php?do=edit&id=$nb[ID]\"><img src=\"images/edit.gif\" alt='編輯'  border=\"0\"> </a> \n" ; 
      */
                  
      //填表人
      echo "<td >$user $edit_link</td>\n" ;      

      
			$u_edit_link ='' ;
      //負責單位
      //$ti = $nb[unitId] ;
	  $ti = $unitId ;
      if ((board_checkid($ti)) and ($rep_mode <> 2))
      	//$u_edit_link = "<a href=\"fixedadmin.php?do=edit&id=$nb[ID]\"><img src=\"images/edit.gif\" alt='修改通報內容' title='修改通報內容' border=\"0\"> </a>\n" ;
      	  $u_edit_link = "<a href=\"fixedadmin.php?do=edit&id=$ID\"><img src=\"images/edit.gif\" alt='修改通報內容' title='修改通報內容' border=\"0\"> </a>\n" ;    
	 echo "<td >$unitstr[$ti] $u_edit_link</td> \n " ;

      //回覆者
      //echo "<td >$nb[rep_user] &nbsp;</td> \n" ;
	  echo "<td >$rep_user &nbsp;</td> \n" ;
      //回覆日期
      //echo "<td >$nb[rep_date] &nbsp;</td> \n" ;
	  echo "<td >$rep_date &nbsp;</td> \n" ;
      //處理情形
      //$ti  =$nb[rep_mode] ;
	  $ti  =$rep_mode ;
      if ($ti<>2)
         //echo "<td ><a href=\"fixedadmin.php?do=reply&id=$nb[ID]\">$checkmode[$ti]</a></td> \n" ;
         echo "<td ><a href=\"fixedadmin.php?do=reply&id=$ID\">$checkmode[$ti]</a></td> \n" ;
	 else 
         echo "<td >$checkmode[$ti]</td> \n" ;  
         
      echo "</tr>\n" ;
   }  
   
?> 
  </table>
 
</form>

<?php foot(); ?>
