<?php
//$Id: showpaper.php 8952 2016-08-29 02:23:59Z infodaes $
  include "config.php" ;

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}
  $paper_id=intval($paper_id);
  if (!$paper_id)  header("location:paper_list.php") ;
  
  
    
    $sqlstr =  "select a.* ,b.chap_path,c.book_path 
                from magazine_paper a ,magazine_chap b ,  magazine c
                where a.id=$paper_id and a.chap_num = b.id and b.book_num= c.id  " ;   
               
    $result = $CONN->Execute( $sqlstr) ;
    if ($result) 
       while ($row=$result->FetchRow()) {
         $tmode = $row["tmode"] ;
         $title = $row["title"] ;
         $author = $row["author"] ;
         $type_name = $row["type_name"] ;
         $teacher = $row["teacher"] ;
         $parent = $row["parent"] ;
         $doc = $row["doc"] ;

         $classnum = $row["class_name"] ;
         $pic_name = $row["pic_name"] ;
         $chap_path = $row["chap_path"] ;
         $book_path = $row["book_path"] ;
             $doc =htmlspecialchars($doc) ;
 
             $doc = ereg_replace("\n","</p><p>",$doc) ;
             $doc = ereg_replace("[[:space:]]","&nbsp",$doc);  
      }   

?>
<html>
<head>
<title>查看結果</title>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
</head>

<body bgcolor="#FFFFFF">
<table width="85%" border="0" cellspacing="1" cellpadding="4" align="center">
  <tr bgcolor="#66CCFF"> 
    <td width="72%" rowspan="3" bgcolor="#66CCFF"> 
      <div align="center"><font size="+2"> 
        <?php echo $title ?>
        </font></div>
    </td>
    <td width="28%"> 
      <?php echo $classnum . "&nbsp;" . $author ?>
    </td>
  </tr>
  <tr bgcolor="#99CCFF"> 
    <td width="28%" bgcolor="#66CCFF"> 
      <?php if ($tmode==0)  echo "家長:" . $parent ; ?>
    </td>
  </tr>
  <tr bgcolor="#99CCFF">
    <td width="28%" bgcolor="#66CCFF"> 
      <?php if ($tmode==0)   echo "指導老師:" . $teacher ; ?>
    </td>
  </tr>
  <tr> 
    <td colspan="2"> 
      <?php 
    if ($tmode==1) {  //圖檔
       echo "<div align=\"center\">" ;
       if ($doc) echo $doc ."<br>" ;
       echo "<img src=\"" .$htmlpath  . $book_path ."/".$chap_path ."/" . $pic_name ."\" border=\"0\"> ";
       echo "</div> " ;
    }
    elseif($tmode==4) {  //SWF動畫
       echo "<div align=\"center\">" ;
       if ($doc) echo $doc ."<br>" ;
       echo "<OBJECT classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' width='800' height='600'><PARAM NAME=movie VALUE='$htmlpath".$book_path."/".$chap_path."/".$pic_name."'></OBJECT>";
       echo "</div> " ;
    }
    else { 
        if ($pic_name) 
        echo "<img src=\"" .$htmlpath  . $book_path ."/".$chap_path ."/" . $pic_name ."\" align=\"left\" border=\"0\">" ;
        echo "<p>" . $doc  ;
    }
    ?>
    </td>
  </tr>

  <tr bgcolor="#CCCCCC"> 
    <td colspan="2"> 
      <div align="center"><a href="javascript:history.go(-1)">回上頁</a></div>
    </td>
  </tr>
</table>
</body>
</html>
