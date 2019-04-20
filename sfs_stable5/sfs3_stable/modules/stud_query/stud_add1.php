<?php

// $Id: stud_add1.php 5310 2009-01-10 07:57:56Z hami $

/* 學務系統設定檔 */
include "stud_query_config.php";  

// --認證 session 
sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

//檔頭
head("村里別統計");
//選單
print_menu($menu_p);
//建立 treemenu 類別
$tree1 = new TreeMenu();
$tree1 ->default_p = "0";
//班級陣列
$curr_class = class_base();
//按鍵處理
switch ($key) {
	case c : //班級分布
		$query = "select sum(stud_base.stud_sex=1) as boy,
			sum(stud_base.stud_sex=2)as girl,			
			count(stud_addr.addr_id) as aa,
			substring(curr_class_num,1,3)as gg
			from stud_addr,stud_base 
			where stud_base.addr_id=stud_addr.addr_id
			and stud_base.stud_study_cond=0 
			and stud_addr.stud_addr_h_b='$h_b'
			and stud_addr.stud_addr_h_c='$h_c'
			group by gg  order by gg  ";
		$result = mysql_query($query) or die($query);		
		$list_con .= "<tr class=title_sbody1 ><td align=center>序號</td><td align=center>班級</td><td align=right>人數</td><td align=right>男</td><td align=right>女</td></tr>\n";
		while ($row= mysql_fetch_array($result)) {
			$list_con .= sprintf("<tr><td align=center>%d</td><td align=center>%s</td><td align=right>%d</td><td align=right><font color=blue>%d</font></td><td align=right><font color=red>%d</font> </td></tr>\n",++$i,$curr_class[$row[gg]],$row[aa],$row[boy],$row[girl]);
			$boy +=$row[boy];
			$girl +=$row[girl];
			$total +=$row[aa];
		}
		$list_con .= sprintf("<tr><td align=center colspan=2> 合計 %d 班</td><td align=right>%d</td><td align=right><font color=blue>%d</font></td><td align=right><font color=red>%d</font> </td></tr>\n",$i,$total,$boy,$girl);
		$list_con = "<tr class=title_mbody ><td colspan=5 align=center >$h_b -- $h_c 學生人數統計表</td></tr>\n".$list_con;
	break;
	case b : //村里別統計
		$query = "select sum(stud_base.stud_sex=1) as boy,
			sum(stud_base.stud_sex=2)as girl,
			stud_addr.stud_addr_h_b,
			stud_addr.stud_addr_h_c,
			count(stud_addr.stud_addr_h_b) as aa			
			from stud_addr,stud_base 
			where stud_base.addr_id=stud_addr.addr_id
			and stud_base.stud_study_cond=0 
			and stud_addr.stud_addr_h_b='$h_b'
			group by stud_addr.stud_addr_h_c  order by aa desc ,stud_addr.stud_addr_h_c  ";
		
		$result = mysql_query($query) or die($query);		
		$list_con .= "<tr class=title_sbody1 ><td align=center>序號</td><td align=center>村里別</td><td align=right>人數</td><td align=right>男</td><td align=right>女</td></tr>\n";
		while ($row= mysql_fetch_array($result)) {
			if ($row[stud_addr_h_c]=="") $row[stud_addr_h_c] ="<font color=red>未輸入村里</font>";
			$list_con .= sprintf("<tr><td align=center>%d</td><td align=center>%s</td><td align=right>%d</td><td align=right><font color=blue>%d</font></td><td align=right><font color=red>%d</font> </td></tr>\n",++$i,$row[stud_addr_h_c],$row[aa],$row[boy],$row[girl]);
			$boy +=$row[boy];
			$girl +=$row[girl];
			$total +=$row[aa];
		}
		$list_con .= sprintf("<tr><td align=center colspan=2> 合計 %d 個村里</td><td align=right>%d</td><td align=right><font color=blue>%d</font></td><td align=right><font color=red>%d</font> </td></tr>\n",$i,$total,$boy,$girl);
		$list_con = "<tr class=title_mbody ><td colspan=5 align=center >$h_b 村里學生人數統計表</td></tr>\n".$list_con;
	break;
	default : //鄉別統計
			$query = "select sum(stud_base.stud_sex=1) as boy,
			sum(stud_base.stud_sex=2)as girl,
			stud_addr.stud_addr_h_b,			
			count(stud_addr.stud_addr_h_b) as aa			
			from stud_addr,stud_base 
			where stud_base.addr_id=stud_addr.addr_id
			and stud_base.stud_study_cond=0 			
			group by stud_addr.stud_addr_h_b  order by aa desc  ";
		$result = mysql_query($query) or die($query);		
		$list_con .= "<tr class=title_sbody1 ><td align=center>序號</td><td align=center>鄉鎮別</td><td align=right>人數</td><td align=right>男</td><td align=right>女</td></tr>\n";
		while ($row= mysql_fetch_array($result)) {			
			$list_con .= sprintf("<tr><td align=center>%d</td><td align=center>%s</td><td align=right>%d</td><td align=right><font color=blue>%d</font></td><td align=right><font color=red>%d</font> </td></tr>\n",++$i,$row[stud_addr_h_b],$row[aa],$row[boy],$row[girl]);
			$boy +=$row[boy];
			$girl +=$row[girl];
			$total +=$row[aa];
		}
		$list_con .= sprintf("<tr><td align=center colspan=2> 合計 %d 個鄉鎮</td><td align=right>%d</td><td align=right><font color=blue>%d</font></td><td align=right><font color=red>%d</font> </td></tr>\n",$i,$total,$boy,$girl);
		$list_con = "<tr class=title_mbody ><td colspan=5 align=center >$h_b 鄉鎮學生人數統計表</td></tr>\n".$list_con;
	break;
}
$query = "select stud_addr.stud_addr_h_b, stud_addr.stud_addr_h_c, count(stud_addr.stud_addr_h_b)as aa  from stud_addr,stud_base where stud_base.addr_id=stud_addr.addr_id and stud_base.stud_study_cond=0 group by  stud_addr.stud_addr_h_c  order by stud_addr.stud_addr_h_b ,aa desc ";
//echo $query ;
$result = mysql_query($query) or die ($query);
$temp_value="";
$i = 0;
$tol_num = mysql_num_rows($result);
$doexe[] = ".選擇村里 ($tol_num)";
while ($row = mysql_fetch_array($result)){
	
	if ($row[stud_addr_h_b] <> $temp_value) {							
		if ($temp_count)
			$doexe[$temp_count-1] .=" ($i)^^{$_SERVER['PHP_SELF']}?key=b&h_b=$temp_name";
		
		$doexe[] = "..$row[stud_addr_h_b]";
		$temp_count = count($doexe);
		$temp_name = $row[stud_addr_h_b];
		$i = 0;
	}				
	$doexe[] = "...$row[stud_addr_h_c]($row[aa])^^{$_SERVER['PHP_SELF']}?key=c&h_b=$row[stud_addr_h_b]&h_c=$row[stud_addr_h_c]";
	$temp_value = $row[stud_addr_h_b];
	$i++;			
	
}
if ($temp_count)
	$doexe[$temp_count-1] .=" ($i)^^{$_SERVER['PHP_SELF']}?key=b&h_b=$temp_name";
	

$tree1->doexe= $doexe;
?>	

<table border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="#CCCCCC">
  <tr><td valign=top  width=200 nowrap> 
   <?php
   	
   	$tree1->print_tree($p);
   ?>
   </td>
   <td valign=top width=100%>
   <!------ right content --->
    <table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
	
   	<?php echo $list_con ?>
   	
   	</table>
   <!------ end content --->   	
   </td>
   </tr>
</table>
<?php foot(); ?>
