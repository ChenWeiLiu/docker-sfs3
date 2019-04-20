<?php

// $Id: boardadmin.php 8129 2014-09-23 07:39:39Z smallduh $

//設定檔載入檢查
  require "config.php" ;

  // --認證 
  sfs_check();
  $SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'] ;
  if ( !checkid($SCRIPT_FILENAME,1)){
    head("報名表單設計") ;
    print_menu($menu_p);
    echo "無管理者權限！<br>請進入 系統管理 / 模組權限管理 修改 fixed 模組授權。" ;
    foot();
    exit ;    
    //  Header("Location: index.php"); 
  }  

  $key = $_POST['key'] ;
  $bk_id=($_GET['bk_id']) ? $_GET['bk_id'] : $_POST['bk_id'];
  $board_name =  $_POST['board_name'];
  $email_list = $_POST['email_list'];          


switch($key) {

	case "確定新增" :
	$sql_insert = "insert into fixed_kind (bk_id,board_name,Email_list) values ('$bk_id','$board_name','$email_list' )";
	$CONN->Execute($sql_insert) or user_error("讀取失敗！<br>$sql_insert",256) ; 
	//echo $sql_insert  ;
	break;
	case "確定修改" :
	$sql_update = "update fixed_kind set board_name='$board_name ',Email_list='$email_list' where bk_id='$bk_id' ";
	$CONN->Execute($sql_update) or user_error("讀取失敗！<br>$sql_update",256) ; 
	break;
	case "確定刪除" :
	$sql_update = "delete  from fixed_kind  where bk_id='$bk_id'";	
	$CONN->Execute($sql_update) or user_error("讀取失敗！<br>$sql_update",256) ; 
	break;
}

//if (empty($bk_id)) $bk_id = 0 ;

if ($key != "新增版區"){
        
   //  --目前資料
   if ($bk_id)
	$sqlstr = "select * from fixed_kind where bk_id ='$bk_id' ";
   else
        $sqlstr = "select * from fixed_kind order by bk_id limit 0,1 ";
   $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
   $row = $result->FetchRow() ;
}

	
$bk_id = $row["bk_id"];
$board_name = $row["board_name"];

$email_list = $row["Email_list"];

//  --程式檔頭
head();
//選單連結字串
$linkstr = "bk_id=$bk_id";
print_menu($menu_p,$linkstr); 

if ($key == "刪除"){
	echo "<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
	        <form action=\"$PHP_SELF\" name=eform method=\"post\">";
	echo "  <input type=hidden name=\"bk_id\" value=\"$bk_id\">";	
	echo sprintf ("<td align=center>確定刪除 <B><font color=red>%s</font></B></td></tr>",$board_name);
	echo "<tr><td align=center><input type=submit name=key value=\"確定刪除\">";
	echo "</td></tr></form></table>";
	foot();
	exit;
}
?>



<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr><td valign=top bgcolor="#CCCCCC">
 <table border="0" width="100%" cellspacing="0" cellpadding="0" >
    <tr>
      <td  valign="top" >    
	<?php      
	//建立左邊選單	
	$grid1 = new sfs_grid_menu;  //建立選單	   
	//$grid1->bgcolor = $gridBgcolor;  // 顏色   
	//$grid1->row = $gri ;	     //顯示筆數
	$grid1->key_item = "bk_id";  // 索引欄名  	
	$grid1->display_item = array("bk_id","board_name");  // 顯示欄名   	
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select bk_id,board_name from fixed_kind order by bk_id";   //SQL 命令   
	$grid1->do_query(); //執行命令   
	
	$grid1->print_grid($bk_id); // 顯示畫面   

	?>
     </td></tr></table>
  </td>

<td width="100%" valign=top bgcolor="#CCCCCC">
   <form action="<?php echo $PHP_SELF; ?>" name='eform' method="post" > 

        <table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
          <tr> 
            <td align="right"  nowrap>單位代號</td>
            <td> 
            <?php 
            if ($key =="新增版區"  or empty($bk_id)) 
              echo "<input type='text' size='12' maxlength='12' name='bk_id' value='$bk_id'>" ;
            else {
              echo    $bk_id ;
              echo "<input type='hidden' name='bk_id' value='$bk_id'>" ;
            }  
            ?>  
            </td>
          </tr>
          <tr> 
            <td align="right"  nowrap>單位名稱</td>
            <td> 
              <input type="text" size="20" maxlength="20" name="board_name" value="<?php echo $board_name ?>">
            </td>
          </tr>
          <tr> 
            <td align="right"  nowrap>email通知</td>
            <td> 
              <input type="text" size="60" name="email_list" value="<?php echo $email_list ?>">
            </td>
          </tr>
          <tr> 
          <td align="center"  nowrap colspan="2">
<?php	
	if ($bk_id == "")
		echo "<input type='submit' name='key' value=\"確定新增\">  ";
	else if ($key != "新增版區" ){
		echo "<input type='submit' name='key' value=\"確定修改\">  ";
		echo "<input type='submit' name='key' value=\"刪除\">  ";
		echo "<input type='submit' name='key' value=\"新增版區\">  ";
	}
	else{
		echo "<input type=submit name=key value=\"確定新增\">";
	}

?>            
          </td>
          </tr>
        </table>
    </form>

</td></tr>
</table>

<?php
	foot();
?>