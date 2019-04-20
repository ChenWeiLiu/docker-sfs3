<?php
// $Id: photo_old.php 8744 2016-01-08 14:30:27Z qfon $
  require "config.php";
  
  sfs_check();
  
  $showpage = $_GET['showpage'] ;
  $query = $_GET['query'] ;

  function geticon($dir){
     //取得第一張小圖

     global   $htmpath ,$savepath ,$big ;
     $picdir=$htmpath  . $dir ;
     $updir=$savepath  . $dir ;

   if ( is_dir($updir) ) {
 
     $dirs = dir($updir) ;
     $dirs ->rewind() ;
     while ( ($filelist = $dirs->read()) and !$stop_m) {
     	 if (($filelist!=".") && ($filelist!="..")){

     	   //windows      
     	   if (WIN_PHP_OS() ) {
     	       if (eregi("(.jpg|.jpeg|.png|.gif|.bmp)$", $filelist)) 
         	 $filelist_arr[] =$picdir."/" .$filelist ;
     	   }else {	
     	      //其他   	
         	if (strstr($filelist,'!!!_'))   	//縮小圖	
         	  $filelist_arr[] =$picdir."/" .$filelist ;
           }
         }
     }
     $dirs->close() ;  	
     sort ($filelist_arr) ;
     return $filelist_arr[0] ;
     
   }
  }
  
  if ($query) $do = "search" ;
  
  //讀取資料庫

  ///mysqli
  $sqlstr = "SELECT count(*) FROM $tbname  " ;
  
  if ($do == "search") 
     $sqlstr =$sqlstr .  " where act_info like ? " ;
  $sqlstr .= " order by act_ID  DESC " ;  
  
  if ($debug ) echo $sqlstr ;

 
$mysqliconn = get_mysqli_conn();
$stmt = "";
$query="%$query%";
$stmt = $mysqliconn->prepare($sqlstr);
$stmt->bind_param('s',$query);
$stmt->execute();
$stmt->bind_result($totalnum);
$stmt->fetch();
$stmt->close();



  if ($totalnum) {
	
	$totalpage = ceil( $totalnum / $pagesites) ;
    
    if (!$showpage)  $showpage =1 ; 
	
   $sqlstr = "SELECT act_ID,act_date,act_name,act_info,act_dir,act_postdate,act_auth,act_view FROM $tbname  " ;
  
  if ($do == "search") 
     $sqlstr =$sqlstr .  " where act_info like ? " ;
  $sqlstr .= " order by act_ID  DESC " ;  
 
    $sqlstr .= ' LIMIT ' . ($showpage-1)*$pagesites . ', ' . $pagesites  ;  
    //$result = $CONN->PageExecute("$sqlstr", $pagesites , $showpage );

$query="%$query%";	
$stmt = $mysqliconn->prepare($sqlstr);
$stmt->bind_param('s', $query); 
$stmt->execute();
$stmt->bind_result($act_ID,$act_date,$act_name,$act_info,$act_dir,$act_postdate,$act_auth,$act_view);
 
 
 
 
  }  

///mysqli
  
  /*
  $sqlstr = "SELECT * FROM $tbname  " ;
  
  if ($do == "search") 
     $sqlstr =$sqlstr .  " where act_info like '%$query%' " ;
  $sqlstr .= " order by act_ID  DESC " ;  
  
  if ($debug ) echo $sqlstr ;

  //計算頁數
  $result = $CONN->Execute( $sqlstr) ;
  if ($result) {
    $totalnum =  $result->RecordCount() ;
    $totalpage = ceil( $totalnum / $pagesites) ;
    
    if (!$showpage)  $showpage =1 ;  

    $result = $CONN->PageExecute("$sqlstr", $pagesites , $showpage );
 
  }  
  */
  
  if (!$totalpage) $totalpage= 1 ;
  
  head("相片展")
?>

<script language="JavaScript">

function chk_empty(item) {
   if (item.value=="") { return true; } 
}

function dosearch() {
   var errors='' ;
   if (chk_empty(document.myform.query))   {
      errors = '搜尋文字不可以空白' ; }

   
   if (errors=='') { 
     window.location.href="<?php echo basename($PHP_SELF)."?do=search&query="?>" + document.myform.query.value  ;}
   else      alert(errors) ;
}	

function changepage() { 
   var errors='' ;

   if (errors=='')
      window.location.href="<?php echo basename($PHP_SELF)."?showpage="?>" + document.myform.selpage.options[document.myform.selpage.selectedIndex].value  ;
   else     
      alert(errors) ;
}

function gotourl(id,selpage,dirstr) {
   var PROFILE = null;	
        PROFILE =  window.open ("view.php?updir="+dirstr);

   window.location.href="<?php echo basename($PHP_SELF)."?showpage=" ?>" + selpage +"&id=" +id;
}


</script>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<style type="text/css">
<!--
.daystyl {  font-size: 12pt; background-color: #FF9999}
.tdbody {  font-size: 12pt; color: #000000}
.info {  font-size: 12pt; color: #3333FF}
.auth {  font-size: 10pt}
-->
</style>
</head>




<form method="post" action="<?php echo basename($PHP_SELF) ?>" name="myform">
  <table width="95%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr> 
    <td colspan="2"> 
        <h2>相片展</h2>
    </td>
    <td width="200" nowrap>
      
<?php 
    if ($showpage > 1){
      $pre = $showpage -1 ;
      echo "<a href=" . basename($PHP_SELF) . "?showpage=$pre><img src='images/prev.gif' border=0 title='前一頁' alt='前一頁' ></a>&nbsp;" ;
    }  
    echo '<select name="selpage" onChange="changepage()" >' ;
    for ($i=1 ;$i<=$totalpage;$i++) {
    	if ($i==$showpage) echo "<option value=\"$i\" selected>跳到第" ;
        else echo "<option value=\"$i\">跳到第" ;
        echo  $i . "頁 </option> \n" ;
    }
    echo "</select>" ;
    if ($showpage < $totalpage) {
      $next = $showpage+1 ;
      echo "&nbsp;<a href=" . basename($PHP_SELF) . "?showpage=$next><img src='images/next.gif' border=0 title='後一頁' alt='後一頁' ></a>" ;
    }  

?>            

     </td>
      <td width="220">搜尋:
<input type="text" name="query" size="10">
        <a href="Javascript:dosearch();"><img src="images/go.gif" width="41" height="20" border="0"></a> 
      </td>
      <td width="60"><a href="photo_admin2.php">新增</a></td>

  </tr>
</table>
  <hr noshade>
<?php
  //if($result) 
  	//while ($nb=$result->FetchRow() ) {    
	  if ($totalnum)
        while ($stmt->fetch()) {
?>
<table width="95%" border="0" cellspacing="0" cellpadding="4"  align="center">
  <tr > 
      <td bgcolor="#CCCCFF" class="tdbody" width="80%">
        <span class="daystyl"><?php echo  '第' . $act_ID .'則['. $act_date . ']' ?></span> 
        <a href = "<?php echo "view.php?id=$act_ID" ?>">   
          <?php echo $act_name ; ?>
        </a> 
        [<?php echo  $act_auth ; ?>公佈]
      </td>  
    <td  nowrap bgcolor="#CCCCFF" class="auth" width="60"> 
       <?php echo "點閱: $act_view" ;?>
    </td>      
    <td  width="100"> 
      <a href="photoshow.php?id=<?php echo $act_ID ?>" target="photoshow"><img src="images/show_time.gif"   border="0" alt="展示"></a> 
      <a href="photo_admin.php?do=edit&id=<?php echo $act_ID ?>"><img src="images/edit.gif"   border="0" alt="編修"></a> 
      <a href="photo_admin.php?do=delete&id=<?php echo $act_ID ?>"><img src="images/delete.gif"   border="0" alt="刪除"></a> 
    </td>

  </tr>
  <tr>
    <td colspan="2"> 
<?php   
      echo "<blockquote> <p class=\"info\"> " ;   
      //出現小圖
      $pic = geticon( $act_dir ) ;
      //echo  $pic ;
      if ($pic) 
        if (WIN_PHP_OS()) {
           list($width, $height, $type, $attr) = getimagesize(  $big);     
           $w = $width/8;
           $h = $height/8 ;   
           echo "<img src='$pic'  wight=$w height=$h align='left' border=1> " ;

        }else         
          echo "<img src=\"$pic\"  align=\"left\" border=\"1\"> " ;
      
      echo   nl2br($act_info) . " </blockquote> </p>" ;
?>      

    </td>
  </tr>

</table>



<?php
}  
?>  

</form>

<?php
  foot() ;
?>  