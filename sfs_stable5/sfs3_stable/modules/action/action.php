<?php
// $Id: action.php 8708 2015-12-30 12:42:06Z qfon $

  require("config.php") ;
  //$debug = 1;
  
  $showpage = $_GET['showpage'] ;
  $query = $_GET['query'] ;
  $do = $_GET['do'] ;
  $id = (int)$_GET['id'] ;

  if ($id) {
    //加一次	 
    $tsqlstr =  " update $tbname set act_view = act_view+1 where act_ID='$id' " ; 	
    $result = $CONN->Execute( $tsqlstr) or user_error("讀取失敗！<br>$tsqlstr",256) ; 
  }    
  
  if ($query) $do = "search" ;
  
  //讀取資料庫
 // $sqlstr = "SELECT * FROM $tbname  " ;
 // if ($do == "search") $sqlstr =$sqlstr .  " where act_info like '%$query%' " ;
  $sqlstr = "SELECT count(*) FROM $tbname  " ;
  if ($do == "search") $sqlstr =$sqlstr .  " where act_info like ? " ; 
 $sqlstr .= " order by act_ID DESC " ;  
  
  if ($debug ) echo $sqlstr ;
  
  //$result = $CONN->Execute( $sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
  
///mysqli	
$mysqliconn = get_mysqli_conn();
$stmt = "";
$query = "%$query%";
if ($query <> "") {
    $stmt = $mysqliconn->prepare($sqlstr);
    $stmt->bind_param('s', $query);
} 
$stmt->execute();
$stmt->bind_result($totalnum);
$stmt->fetch();
$stmt->close();
///mysqli

   
  if ($totalnum) {
   // $totalnum = $result->RecordCount() ;
    $totalpage =ceil( $totalnum / $pagesites) ;
    
    if (!$showpage)  $showpage =1 ;  
	
  $sqlstr = "SELECT act_ID,act_date,act_name,act_info,act_icon,act_dir,act_index,act_postdate,act_auth,act_view FROM $tbname  " ;
  if ($do == "search") $sqlstr =$sqlstr .  " where act_info like ? " ; 
  $sqlstr .= " order by act_ID DESC " ;  
     
	 $sqlstr .= ' LIMIT ' . ($showpage-1)*$pagesites . ' , ' . $pagesites ;
    //$result = $CONN->PageExecute("$sqlstr", $pagesites , $showpage );
    
	$stmt = $mysqliconn->prepare($sqlstr); 
    $stmt->execute();
    $stmt->bind_result($act_ID,$act_date,$act_name,$act_info,$act_icon,$act_dir,$act_index,$act_postdate,$act_auth,$act_view);

 }  
  if (!$totalpage) $totalpage= 1 ;
  
  head("活動花絮") ;

?>
<html>
<head>
<title><?php echo $titlestr ?></title>
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
   window.location.href="<?php echo basename($PHP_SELF)."?showpage=" ?>" + selpage +"&id=" +id;

        PROFILE =  window.open (dirstr);

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
        <h2>活動花絮</h2>
    </td>
    <td width="100">
      <select name="selpage" onChange="changepage()" >
<?php 
    for ($i=1 ;$i<=$totalpage;$i++) {
    	if ($i==$showpage) echo "<option value=\"$i\" selected>跳到第" ;
        else echo "<option value=\"$i\">跳到第" ;
        echo  $i . "頁 </option> \n" ;
    }
?>            
      </select>
     </td>
      <td width="220">搜尋:
<input type="text" name="query" size="10">
        <a href="Javascript:dosearch();"><img src="images/go.gif" width="41" height="20" border="0"></a> 
      </td>
      
      <td width="74"><a href="action_admin.php">新增</a></td>
      <td width="200"> <a href="xppublish.htm" target="doc">xp網頁公佈說明</a> | <a href="XPPubWiz.php?step=reg">取得機碼</a></td>
  </tr>
</table>
  <hr noshade>
<?php
  //if($result) 
  	//while ($nb=$result->FetchRow()) { 
	if ($totalnum)
	while ($stmt->fetch()) {
  	//$dirstr= $htmpath  .$nb[act_dir]. '/' .$nb[act_index] ;	
     $dirstr= $htmpath  .$act_dir. '/' .$act_index ;	

	//有網頁
    //if ($nb[act_index] )  
    	//$gotostr= '"' .$nb[act_ID] . '","' .$showpage . '","' . $dirstr .'"' ;
      if ($act_index )  
    	$gotostr= '"' .$act_ID . '","' .$showpage . '","' . $dirstr .'"' ;

	
	else    $gotostr= "" ;

?>
<table width="95%" border="0" cellspacing="0" cellpadding="4"  align="center">
  <tr > 
      <td bgcolor="#CCCCFF" class="tdbody" width="90%">
        <span class="daystyl"><?php echo  '第' . $act_ID .'則['. $act_date . ']' ?></span> 
<?php
    if ($gotostr) {
       //有網頁
?>
        <a href = javascript:gotourl(<?php echo $gotostr ?>) > 
          <?php echo $act_name ?>
        </a> 
<?php
        }
    else 
      echo $act_name ;
?>      
        [<?php echo $act_auth ?>公佈]
      </td>  
    <td  nowrap bgcolor="#CCCCFF" class="auth" width="60"> 
       <?php echo "點閱: $act_view" ;?>
    </td>      
    <td  width="40"> 
      <a href="action_admin.php?do=edit&id=<?php echo $act_ID ?>"><img src="images/edit.gif"  align="left" border="0" alt="編修"></a> 
      <a href="action_admin.php?do=delete&id=<?php echo $act_ID ?>"><img src="images/delete.gif"  align="left" border="0" alt="刪除"></a> 
    </td>

  </tr>
  <tr>
    <td colspan="2"> 
<?php   
      echo "<blockquote> <p class=\"info\"> " ;   
      if ($act_icon) 
        echo "<img src=\"$htmpath" . "$act_dir/$act_icon\"  align=\"left\"> " ;
      echo   nl2br($act_info) . " </blockquote> </p>" ;
?>      

    </td>
  </tr>

</table>
<br>


<?php
}  
?>  

</form>

<?php foot() ;?>