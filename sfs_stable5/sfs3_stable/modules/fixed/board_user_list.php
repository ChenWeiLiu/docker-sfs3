<?php

// $Id: board_user_list.php 5310 2009-01-10 07:57:56Z hami $

  // --系統設定檔
 require "config.php" ;
 require_once "../../include/sfs_case_dataarray.php";
  // --認證
  sfs_check();
  $SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'] ;
  if ( !checkid($SCRIPT_FILENAME,1)){
    head("維修通告單位授權") ;
    print_menu($menu_p);
    echo "無管理者權限！<br>請進入 系統管理 / 模組權限管理 修改 fixed 模組授權。" ;
    foot();
    exit ;    
    //  Header("Location: index.php"); 
  }  
    
  $teacher_arr = teacher_array();
  

  $key=($_GET['key']) ? $_GET['key'] : $_POST['key'];
  $pc_id= $_GET['pc_id'] ;

  $post_office = $_POST['post_office'] ;
  $teach_title_id = $_POST['teach_title_id'] ;
  $teacher_sn = $_POST['teacher_sn'] ;
  $bk_id=($_GET['bk_id']) ? $_GET['bk_id'] : $_POST['bk_id'];
  
switch ($key) {
	case "確定新增" :
		$sql_insert = "insert into fixed_check (pro_kind_id,post_office,teacher_sn,teach_title_id,is_admin) values ('$bk_id','$post_office','$teacher_sn','$teach_title_id','$is_admin')";
		$CONN->Execute($sql_insert) or user_error("讀取失敗！<br>$sql_insert",256) ; 
	break;
	case "delete" :
		$sql_update = "delete  from fixed_check where pc_id='$pc_id'";
		$CONN->Execute($sql_update) or user_error("讀取失敗！<br>$sql_update",256) ; 
	break;
}

//預設第一個版區
if (!$bk_id) {
	$query = "select bk_id from fixed_kind order by bk_id limit 0,1 ";
	$result = $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ; 
	$row = $result->FetchRow() ;
	$bk_id = $row[0];
}

//  --程式檔頭
head(); 
//選單連結字串
$linkstr = "bk_id=$bk_id";
print_menu($menu_p,$linkstr); 

$post_office_p = room_kind();
$post_office_p[99] = "所有教師";
$title_p = array();
$query = "SELECT *  FROM teacher_title ";
$query .= " where title_kind >= '$titl_kind' order by title_kind,teach_title_id ";
$result = $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;        
while ($row= $result->FetchRow() )
	$title_p[$row["teach_title_id"]] = $row["title_name"];

//顯示資料
$query = "select * from fixed_kind where bk_id ='$bk_id' ";
$result = $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;  
if ($result) {
	$row = $result->FetchRow() ;
	$bk_id = $row["bk_id"];
	$board_name = $row["board_name"];	
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

<!--- 右邊選單 ---->
<form action="<?php echo $PHP_SELF ?>" name=eform method="post">
<input type="hidden"  name="bk_id" value="<?php echo $bk_id ?>">
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<tr> 
	<td align="right" valign="middle" bgcolor="#CCFFFF" width=100 >授權說明</td>
	<td><B><font color=red><?php echo "$board_name" ?></font></b>--授權給下列群組或個人使用(可複選輸入)</td>
</tr>

<tr>
	<td align="right" valign="middle" bgcolor="#CCFFFF">處室群組</td>
	<td>
	<?php  
		$sel1 = new drop_select(); //選單類別	
		$sel1->s_name = "post_office"; //選單名稱		
		$sel1->arr = $post_office_p; //內容陣列		
		$sel1->do_select();	  
	 ?>	
	</td>
</tr>

<tr>
	<td align="right" valign="middle" bgcolor="#CCFFFF">職稱群組</td>
	<td>
	<?php  
		$sel1 = new drop_select(); //選單類別	
		$sel1->s_name = "teach_title_id"; //選單名稱		
		$sel1->arr = $title_p; //內容陣列		
		$sel1->do_select();	  
	 ?>	
	</td>
</tr>
<tr>
	<td align="right" valign="middle" bgcolor="#CCFFFF">個別教師</td>
	<td>
	<?php
	$sel = new drop_select();
	$sel->s_name = "teacher_sn";
	$sel->arr = $teacher_arr;
	$sel->do_select();
	?>
	</td>
	</tr>
<tr>
	<td align="center" valign="middle" colspan =2 BGCOLOR=#cbcbcb >
	<input type=submit name=key value="確定新增">	
	</td>
</tr>
</form>
</table>
<B><font color=red><?php echo "$board_name" ?></font></b> 授權情形
<table width=600 border=1>
<tr><td>處室群組</td><td>職稱群組</td><td>個別教師</td><td>刪除授權</td></tr>
<?php
$sql_select = "select pc_id,pro_kind_id,post_office,teacher_sn,teach_title_id,is_admin from fixed_check where pro_kind_id = '$bk_id' ";
$result = $CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256) ; 

while ($row = $result->FetchRow()) {

	$pc_id = $row["pc_id"];
	$pro_kind_id = $row["pro_kind_id"];
	$post_office = $row["post_office"];
	$teacher_name = $teacher_arr[$row["teacher_sn"]];
	$teach_title_id = $row["teach_title_id"];
	echo sprintf("<tr bgcolor=#FFFFFF><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>\n",$post_office_p[$post_office],$title_p[$teach_title_id],$teacher_name,"<a href=\"$PHP_SELF?key=delete&bk_id=$pro_kind_id&pc_id=$pc_id\">刪除</a>");
}
?>
</table>
</TD></TR>
</TABLE>
<?php
	foot();
?>
