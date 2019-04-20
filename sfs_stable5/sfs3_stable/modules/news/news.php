<?php
  //$Id: news.php 8808 2016-02-05 16:20:00Z qfon $
  require "config.php";
 //sfs_check();
  //$debug = 1;
  $query = trim($_POST['query']) ;
  $sortmode = $_GET['sortmode'] ;
  $msg_id = $_GET['msg_id'] ;
  $showpage = $_GET['showpage'] ;
  $poster = addslashes($_GET['poster']) ;
  $fromagent = $_POST['fromagent'] ;
  if ($fromagent)$query=addslashes(iconv("utf-8","big5",$query));
  
  $Submit = $_POST['Submit'] ;
  if (!$query)
     $query = $_GET['query'] ;
  
  
  if ($query) $do = "search" ;
  
  $pre_day = GetdayAdd(date("Y-m-d"),$showdays*-1) ;
  $today = date("Y-m-d") ;

  //取得發佈者資料
  $sqlstr = "SELECT poster_job , count(*) as dd  FROM $tbname  where msg_date > '$pre_day' and poster_job<>'' group by  poster_job " ;
  $result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
  while ($row = $result->FetchRow() ) {
      $job_array[] =  $row["poster_job"] ;
  } 
  foreach ($job_array as $key =>$v ) {
    if ($v == $poster)
       $sel_poster .= "<option value=\"$v\" selected >$v \n" ;
    else    
       $sel_poster .= "<option value=\"$v\">$v \n" ;
  }  
  
  $sel_poster =  "<select name=\"poster\" onChange=\"changepage()\" > \n<option value=\"\">顯示全部單位 \n " . $sel_poster . "</select>" ;

  if ($poster) 
     //$where_poster = " and poster_job ='$poster'  " ;
       $where_poster = " and poster_job =?  " ;
    
  //把曾置頂但已過期的資料，置頂設定移除
  $tsqlstr =  " update $tbname  SET `msg_date_expire` = NULL , TopNews='0' where msg_date_expire < '$today' and TopNews<>'0' " ; 	
  $result = $CONN->Execute( $tsqlstr) ; 
  
  //mysqli
   $mysqliconn = get_mysqli_conn("");
   
  //讀取資料庫
    $sqlstr = "SELECT count(*) FROM $tbname  ";

  if ($do == "search" ){
     //$sqlstr =$sqlstr .  " where    ((msg_body like '%$query%') or (msg_subject like '%$query%') )  $where_poster " ;
      $sqlstr =$sqlstr .  " where    ((msg_body like ?) or (msg_subject like ?) )  $where_poster " ;
  }
  else  {
      $sqlstr =$sqlstr . " where ( (msg_date > '$pre_day' and msg_date_expire  is null) or  ( msg_date_expire >= '$today' ) )  $where_poster " ;
  }

$stmt = "";
$stmt = $mysqliconn->prepare($sqlstr);

if ($do == "search" ){
	$query="%$query%";
	//$query=addslashes($query);
	$stmt->mbind_param('s',$query);
	$stmt->mbind_param('s',$query);
}
if ($poster)$stmt->mbind_param('s',$poster);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($totalnum);
$stmt->fetch();
$stmt->close();
  
  //讀取資料庫
  //$sqlstr = "SELECT * FROM $tbname  " ;
    $sqlstr = "SELECT userid,poster_name,poster_job,msg_id,msg_subject,msg_body,msg_hit,msg_date,attach,msg_date_expire,inSchool,url,TopNews FROM $tbname  ";

  if ($do == "search" ){
     //$sqlstr =$sqlstr .  " where    ((msg_body like '%$query%') or (msg_subject like '%$query%') )  $where_poster " ;
      $sqlstr =$sqlstr .  " where    ((msg_body like ?) or (msg_subject like ?) )  $where_poster " ;
  }
  else  {
      $sqlstr =$sqlstr . " where ( (msg_date > '$pre_day' and msg_date_expire  is null) or  ( msg_date_expire >= '$today' ) )  $where_poster " ;
  }

   
  //排序方式   
  switch ($sortmode) {  
    case 0:  	
        
        if ($do=="search") 
           $sqlstr .= " order by msg_id DESC " ;       
        else         
           $sqlstr .= " order by TopNews DESC ,msg_id DESC " ;  
        break ;
    case 1:
        $sqlstr .= " order by msg_date DESC " ;  
        break ;
    case 2:
        $sqlstr .= " order by msg_hit DESC " ;  
        break ;         
  }      

  if ($debug ) echo $sqlstr ;


  //計算頁數
  
  //$result = $CONN->Execute( $sqlstr) ;
  //if ($result) {
	if ($totalnum){
    //$totalnum = $result->RecordCount() ;
	
    $totalpage =ceil( $totalnum / $msgs_per_page) ;
    
    if (!$showpage)  $showpage = 1 ;  
 
  }  

  if (!$totalpage) $totalpage= 1 ;
  
  head("公佈欄");
  include "head.php" ; //上方抬頭列


//<!----------------------------------------------------------------------------------
//                                     要顯示文件內容
//------------------------------------------------------------------------------------>
if ($msg_id) {
  //加一次	 
  $msg_id=intval($msg_id);
  $tsqlstr =  " update $tbname set msg_hit = msg_hit+1 where msg_id='$msg_id' " ; 	
  $result = $CONN->Execute( $tsqlstr) ; 
  
  //讀取此筆
  $tsqlstr =  " SELECT * FROM $tbname where msg_id = $msg_id " ; 
  $result = $CONN->Execute( $tsqlstr) ;   
  if($result) {
    $nb = $result->FetchRow()  ;	
    
    userdata($nb[userid]) ;	
    

    //只公佈在校內
        $inschool = $nb["inSchool"] ;
        if ($inschool and (!check_home_ip()) ) {
            $is_stop = true ;
            $picstr = $stop_icon ; }
        else 
            $is_stop = false ;
		
		if ($fromagent && $inschool)
		{
			$is_stop = true ;
			$picstr = $stop_icon ; 
		}
?>

<table border="0" cellpadding="1" cellspacing="1" align="center" width="90%" bgcolor="#EBEBEB"
	bordercolor="#FFFFFF">
  <tr bgcolor="#006699" class="titleshow"> 
    <th  align="center" nowrap width="5%" valign="top" class="titleshow"> 編號 </th>
    <th  align="center" nowrap width="60%" valign="top" class="titleshow"> 主 旨 
    </th>
    <th  align="center" nowrap  valign="top" class="titleshow"> 職稱 
    </th>
    <th align="center" nowrap  valign="top" class="titleshow"> 公告人 
    </th>
    <th align="center" nowrap  valign="top" class="titleshow"> 閱覽 
    </th>
    <th  align="center" nowrap width="110" valign="top" class="titleshow"> 公告日期 
    </th>
  </tr>
  <tr bgcolor="D8E9FE" class="bodytext"> 
    <td align="center"  width="5%" valign="top" class="bodytext"><?php echo $nb[msg_id] ?></td>
    <td align="left"  width="60%" valign="top" class="bodytext"> <a href=javascript:show_profile("profile.php?msg_id=<?php echo $nb[msg_id] ?>",450,400)><img src="<?php echo $print_icon ?>" border="0"></a>
      <?php echo $nb[msg_subject] ?>
    </td>
    <td align="center"   valign="top" class="bodytext"> <?php echo $nb[poster_job]; ?> </td>
    <td align="center" nowrap  valign="top" class="bodytext">
           <?php 
             if ($user_eamil) echo "<a href=\"mailto:$user_eamil\"> $user_name </a> " ;
             else echo " $user_name " ;
           ?> 
    </td>
    <td align="center"   valign="top" class="bodytext"> <?php echo $nb[msg_hit] ?> </td>
    <td align="center"  width="110" valign="top" class="bodytext"> <?php echo $nb[msg_date] ?> </td>
  </tr>
<?php
if ($is_stop) {
?>
  <tr bgcolor="#F0F5FF"> 
    <td  colspan="6" width="100%" class="doctext"> 
      <div>
        <blockquote> 
          <img src="<?php echo $stop_icon ?>"  border="0" >本份公告僅限校園內讀取！
        </blockquote>
      </div>  
    </td>
  </tr>
<?php
  }
else{ 
?>
  <tr bgcolor="#F0F5FF"> 
    <td  colspan="6" width="100%" class="doctext"> 
      <div>
        <blockquote> 
          <?php echo disphtml($nb[msg_body]) ?>
        </blockquote>
      </div>  
    </td>
  </tr>
<?php
  //有附件檔案
  if ($nb[attach]) {
     echo "  <tr bgcolor=\"#F0F5FF\"> " ;
     echo '    <td  colspan="6" width="100%" class="bodytext"> ' ;

     echo '<blockquote> [附件] ' . "<a href=\"$htmlsavepath". $nb[attach] ."\" target=\"_blank\"> $nb[attach] </a>   </blockquote> " ;

     echo " </td></tr> " ;
  } 
  //有相關網址：
  if (strlen($nb["url"])>8) {
     echo "  <tr bgcolor=\"#F0F5FF\"> " ;
     echo '    <td  colspan="6" width="100%" class="bodytext"> ' ;

     echo "<blockquote> [相關網址：] <a href=\"". $nb["url"]. "\" target=\"_blank\">". $nb["url"] ."</a>   </blockquote> " ;

     echo " </td></tr> " ;
  } 
}
?>    

 </table>

<?php
   }
}

?>


<!----------------------------------------------------------------------------------
                                     文件標題資料
------------------------------------------------------------------------------------>
<div align="center">
  <center>
    <table border="0" cellpadding="1" cellspacing="1" align="center" width="90%" bgcolor="#EBEBEB" >
      <tr bgcolor="#006699" > 
        <th  align="center" nowrap width="5%" valign="top" class="titleshow"> 
          編號 </th>
        <th  align="center" nowrap  width="60%" valign="top" class="titleshow" bgcolor="#006699"> 
          主 旨 </th>
        <th  align="center" nowrap  valign="top" class="titleshow"> 
          職稱 </th>
        <th align="center" nowrap  valign="top" class="titleshow"> 
          公告人 </th>
        <th align="center" nowrap  valign="top" class="titleshow"> 閱覽 
        </th>
        <th  align="center" nowrap width="110" valign="top" class="titleshow"> 
          公告日期 </th>
  </tr>
  

<?php

  $sqlstr .= ' LIMIT ' . ($showpage-1)*$msgs_per_page . ', ' . $msgs_per_page  ;  
  //$result =  $CONN->PageExecute("$sqlstr", $msgs_per_page , $showpage );

$stmt = "";
$stmt = $mysqliconn->prepare($sqlstr);

if ($do == "search" ){
	$query="%$query%";
	//$query=addslashes($query);
	$stmt->mbind_param('s',$query);
	$stmt->mbind_param('s',$query);
}
if ($poster)$stmt->mbind_param('s',$poster);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($userid,$poster_name,$poster_job,$msg_idx,$msg_subject,$msg_body,$msg_hit,$msg_date,$attach,$msg_date_expire,$inSchool,$url,$TopNews);


  $nday = GetdayAdd(date("Y-m-d"),$showday * -1) ;
  //if($result) 
  	//while ($nb=$result->FetchRow()) { 
     if ($totalnum)
      while ($stmt->fetch()) {
		
        //userdata($nb[userid]) ;
          userdata($userid) ;		
        $linkdata =  basename($PHP_SELF) . "?query=$query&sortmode=$sortmode&showpage=$showpage&poster=$poster&" ;
        
	//$top_news = $nb["TopNews"] ;
	$top_news = $TopNews ;
	
	//目前筆所在圖示
        $picstr = $main_icon ;
        //if ($msg_id ==$nb[msg_id]) 
		if ($msg_id ==$msg_idx) 
          $picstr = $reading_icon ;
        else 
          if ($top_news) 
             $picstr = $top_icon ; 
         //elseif ($nb[msg_date]>$nday)
          elseif ($msg_date>$nday) 		 
             $picstr = $new_main_icon ;
          

    //只秀出月日時間
        //$showdate = $nb["msg_date"]  ;
		$showdate = $msg_date  ;
        $showd = explode("-", $showdate);
        $showdate = intval($showd[1]) . "-" .$showd[2] ;
    //只公佈在校內
        //$inschool = $nb["inSchool"] ;
        if ($inschool and (!check_home_ip()) ) {
            $is_stop = true ;
            $picstr = $stop_icon ; }
        else 
            $is_stop = false ;
		
		if ($fromagent && $inschool)
		{
			$is_stop = true ;
			$picstr = $stop_icon ; 
		}
        
?>  
  <tr bgcolor="#D8E9FE" class="bodytext"> 
    <td align="center"  width="5%" valign="top" class="bodytext"><?php echo $msg_idx ?></td>
        <td align="left"  width="60%" valign="top" class="bodytext">
         <?php
          if ($is_stop )  {
         ?> 
          <img src="<?php echo $picstr ?>"  border="0" alt="限校內查看"  > <?php echo $msg_subject ?>
         <?php } else { ?> 
          <a href="<?php echo $linkdata . 'msg_id=' . $msg_idx ?>">
          <img src="<?php echo $picstr ?>"  border="0" > <?php echo $msg_subject ?></a>

         <?php } ?> 
        </td>
        <td align="center" nowrap valign="top" class="bodytext"> <?php echo $poster_job ?> </td>
        <td align="center" nowrap  valign="top" class="bodytext"> 
           <?php 
             if ($user_eamil) echo "<a href=\"mailto:$user_eamil\"> $user_name </a> " ;
             else echo " $user_name " ;
           ?>  
        </td>
    <td align="center"   valign="top" class="bodytext"><?php echo $msg_hit ?></td>
        <td align="center"  width="110" valign="top" class="bodytext"> <?php echo  $showdate ?> </td>
  </tr>
<?php
}


//最下方搜尋畫面
?>  
      <tr> 
        <td align="center" nowrap colspan="6" valign="top" > 

            <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr class="bodytext"> 

              <td width="40%" align="left">
                  <input type="hidden" name="msg_id" value="0">
                  <input type="hidden" name="sortmode" value="<?php echo $sortmode ?>">
                </td>


              <td width="20%" align="right" class="bodytext">關鍵字</td>
              <td width="20%" align="left" colspan="2"><font size="2"> 
                <input type="text" name="query" size="16">
                </font> </td>
              <td width="20%" align="left" class="bodytext"> 
                <input type="submit" value="尋找" name="Submit">
              </td>
            </tr>
          </table>


        </td>
      </tr>
    </table>

  </center>
</div>
</form>


<?php
  foot() ;
?>