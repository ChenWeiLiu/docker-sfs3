<?php

// $Id: board_user_list.php 5310 2009-01-10 07:57:56Z hami $

// --系統設定檔
include "board_man_config.php";
// --認證
sfs_check();
$teacher_arr = teacher_array();
switch ($key) {
	case "確定新增" :
		$sql_insert = "insert into jboard_check (pro_kind_id,post_office,teacher_sn,teach_title_id,is_admin) values ('$bk_id','$post_office','$teacher_sn','$teach_title_id','$is_admin')";
		mysql_query($sql_insert);
	break;
	case "delete" :
		$sql_update = "delete  from jboard_check where pc_id='$pc_id'";
		mysql_query($sql_update) or die ($sql_update);
	break;
}

//預設第一個版區
if (!$bk_id) {
	$query = "select bk_id from jboard_kind order by bk_id limit 0,1 ";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
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
$query .= " where title_kind >= '$titl_kind' and enable=1 order by title_kind,teach_title_id ";
$result = mysql_query($query,$conID)or die ($query);          
while ($row= mysql_fetch_array($result))
	$title_p[$row["teach_title_id"]] = $row["title_name"];

//顯示資料
$query = "select * from jboard_kind where bk_id ='$bk_id' ";
$result = mysql_query ($query,$conID) or die ($query); 
if ($result) {
	$row = mysql_fetch_array($result);
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
	/*
	$grid1 = new sfs_grid_menu;  //建立選單	   
	//$grid1->bgcolor = $gridBgcolor;  // 顏色   
	//$grid1->row = $gri ;	     //顯示筆數
	$grid1->key_item = "bk_id";  // 索引欄名  	
	$grid1->display_item = array("bk_order","bk_id","board_name");  // 顯示欄名   	
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select bk_id,board_name,bk_order from jboard_kind order by bk_order,bk_id";   //SQL 命令   
	$grid1->do_query(); //執行命令   
	
	$grid1->print_grid($bk_id); // 顯示畫面   
*/
	?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>">
 <select name="bk_id" onchange="this.form.submit()" size="20">
	<option value="">─────</option>

<?php
	$query = "select * from jboard_kind order by bk_order,bk_id ";
	$result= $CONN->Execute($query) or die ($query);
	while( $row = $result->fetchRow()){
		$P=($row['position']>0)?"".str_repeat("|--",$row['position']):"";
		/*
		if ($row["bk_id"] == $bk_id  ){
			echo sprintf(" <option style='color:%s' value=\"%s\" selected>[%05d] %s%s%s</option>",$position_color[$row['position']],$row["bk_id"],$row['bk_order'],$P,$row['bk_order'],$row["board_name"]);
			$board_name = $row["board_name"];
		}
		else
			echo sprintf(" <option style='color:%s' value=\"%s\">[%05d] %s%s%s</option>",$position_color[$row['position']],$row["bk_id"],$row['bk_order'],$P,$row['bk_order'],$row["board_name"]);
  	}
	 */
		if ($row["bk_id"] == $bk_id  ){
			echo sprintf(" <option style='color:%s' value=\"%s\" selected>[%05d] %s%s(%s)</option>",$position_color[$row['position']],$row["bk_id"],$row['bk_order'],$P,$row["board_name"],$row["bk_id"]);
			$board_name = $row["board_name"];
		}
		else
			echo sprintf(" <option style='color:%s' value=\"%s\">[%05d] %s%s(%s)</option>",$position_color[$row['position']],$row["bk_id"],$row['bk_order'],$P,$row["board_name"],$row["bk_id"]);
	  }

	
	echo "</select>";

	?>
</form>
	
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
$sql_select = "select pc_id,pro_kind_id,post_office,teacher_sn,teach_title_id,is_admin from jboard_check where pro_kind_id = '$bk_id' ";
$result = mysql_query ($sql_select,$conID);

while ($row = mysql_fetch_array($result)) {

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
