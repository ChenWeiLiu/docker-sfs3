<?php // $Id: head.php 8952 2016-08-29 02:23:59Z infodaes $ ?>
<html>
<head>
<title>學校公告</title>
<meta http-equiv="Content-Type" content="text/html; charset=big5">

<script language="JavaScript"><!--
function show_profile(action,winwidth,winheight) {
	var PROFILE = null;
        PROFILE =  window.open ("", "ProfileWindow", "toolbar=no,width="+winwidth+",height="+winheight+",directories=no,status=no,scrollbars=yes,resizable=yes,menubar=yes,toolbar=yes");
        if (PROFILE != null) {
               if (PROFILE.opener == null) {
                   PROFILE.opener = self;
        	   }
	       PROFILE.location.href = action;
               }
}

function changepage() { 
   var errors='' ;
   var poster ='' ;
   if (errors=='') {
      //window.location.href="<?php echo basename($PHP_SELF)."?query=$query&sortmode=$sortmode&showpage="?>" + (document.myform.selpage.selectedIndex+1)  ;
      poster = "poster=" + document.myform.poster.options[document.myform.poster.selectedIndex].value ;
      window.location.href="<?php echo basename($PHP_SELF)."?query=$query&sortmode=$sortmode&showpage="?>" + document.myform.selpage.options[document.myform.selpage.selectedIndex].value + "&" + poster  ;
   }else     
      alert(errors) ;
}
// --></script>
<style>
.bodytext {  font-size: small}
.doctext {  font-size: small}
.titleshow {  font-size: small; color: #FFFFFF; text-align: center}
A:link { text-decoration: none};
A:visited { text-decoration: none};
A:active { text-decoration: none};


</style>
</head>


<?php

  
  switch ($sortmode) {
  	case 0 : $sortmodestr  = "依編號顯示" ;
  	         break ;
  	case 1 : $sortmodestr  = "依日期顯示" ;
  	         break ;
  	case 2 : $sortmodestr  = "依瀏灠次數顯示" ;
  	         break ;
  }
  $linkdata =  basename($PHP_SELF) . "?query=$query&poster=$poster&sortmode=" ;
?>
<form method="post" name = "myform" action="<?php echo basename($PHP_SELF). "?poster=$poster" ?>">

<div align="center">
  <center>
    <table border="0" cellpadding="1" cellspacing="1" width="90%" 
		bgcolor="#EBEBEB" bordercolor="#FFFFFF" align="center">
      <tr bgcolor="#006699"> 
        <th align="center"  valign="top" colspan="6" height="18" class="titleshow"> 
          學校公告</th>
  </tr>
  <tr bgcolor="#D8E9FE" class="bodytext"> 
    <td width="36%" align="center" valign="top" colspan="2" class="bodytext">總計:<?php echo $totalnum ?>則公告</td>
    <td width="28%" align="center" valign="top" colspan="2" class="bodytext">第<?echo "$showpage/$totalpage" ?>頁</td>
    <td width="36%" align="center" valign="top" colspan="2" class="bodytext">目前狀態：<?php echo $sortmodestr .  $sel_poster  ?></td>
  </tr>
  <tr bgcolor="#EBEBEB" class="bodytext"> 
    <td  colspan="5" align="right" class="bodytext"> 
<?php 
if (!$msg_id) {

//一般抬頭======================================
   echo '|<a href="'. $linkdata .'0"> 依編號</a> |<a href="' . $linkdata .'1"> 依日期</a> ' ; 
   echo '|<a href="'. $linkdata .'2"> 依瀏覽次數</a> |<a href="news_admin.php"> 張貼</a> |<a href="news_stats.php"> 統計</a> ' ;


}
else {
//進入文章抬頭=====================================
   echo '|<a href="'. $linkdata .'0"> 依編號</a> |<a href="' . $linkdata .'1"> 依日期</a> ' ; 
   echo '|<a href="'. $linkdata .'2"> 依瀏覽次數</a> |<a href="news_admin.php"> 張貼</a> |<a href="news_stats.php"> 統計</a> ' ;
   echo "|<a href=\"news_admin.php?do=edit&msg_id=$msg_id\"> 修改</a> |<a href=\"news_admin.php?msg_id=$msg_id&do=delete\"> 刪除</a> " ;
     
}
?>
     </td>
    <td  align="right" class="bodytext"> 
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
  </tr>
</table>
  </center>
</div>
<br>
