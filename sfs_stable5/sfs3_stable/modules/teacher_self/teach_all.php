<?php
$birth_p = array( "01 台北市","02 高雄市","03 宜蘭縣","04 基隆市","05 台北縣","06 桃園縣","07 新竹縣","08 新竹市","09 苗栗縣","10 台中縣","11 台中市","12 南投縣","13 彰化縣","14 雲林縣","15 嘉義縣","16 嘉義市","17 台南縣","18 台南市","19 高雄縣","20 屏東縣","21 台東縣","22 花蓮縣","23 澎湖縣","24 金門縣","25 連江縣");

$official_level_p = array ("1 簡任","2 薦任","3 委任");
$remove_p = array ("1 調出","2 退休","3 代課期滿","4 資遣","5 刪除記錄");

/**
 *	左邊選單控制 *
 *	@param $sql_select - SQL敘述
 *	@return string 選單內容
 */

function teacher_list($sql_select)
{
	global $conID,$curr_name,$curr_teach_id,$curr_teach_condition,$teach_next;
	$result = mysql_query ($sql_select,$conID)or die($sql_select);
	$tol_num = mysql_num_rows($result);
	if ($tol_num > 0){
		$temp_menu ="<table><form name=\"mform\" method=\"post\"><tr><td align=right><font size=2>總人數:$tol_num 人</font></td></tr><tr><td><select name=curr_teach_id  size=18 onchange=\"document.mform.submit()\">";
		$tempi = 0;
		while ($row = mysql_fetch_array($result)) {
			$teach_id = $row["teach_id"];
			$name = $row["name"];
			if ($flag==1) {
				$teach_next = $teach_id;
				$flag=0;
			} //記錄下一位
			if ($teach_id == $curr_teach_id or ($curr_teach_id =="" and $tempi ==0 )){
				$temp_menu .="<option value=\"$teach_id\" selected >$teach_id--$name</option>\n";
				$curr_name =$name;
				$flag = 1;
				$curr_teach_id = $teach_id;
			}
			else
				$temp_menu .="<option value=\"$teach_id\">$teach_id--$name</option>\n";
			$tempi++;
		}
		$temp_menu .="</td></tr>";
	}
	else
		$temp_menu .= "<table><tr><td>無資料</td></tr>";
	$temp_menu .= "<tr><td align=right><font size=2>";
	if ($curr_teach_condition == 0)
		$temp_menu .= "<a href=\"$PHP_SELF?curr_teach_id=$curr_teach_id&curr_teach_condition=1\">顯示離職資料</a>";
	else
		$temp_menu .= "<a href=\"$PHP_SELF?curr_teach_id=$curr_teach_id&curr_teach_condition=0\">顯示在職資料</a>";
	$temp_menu .= "</font></td></tr></form></table>";
	
	return $temp_menu; 
}


/**
 *	上方選單 *
 *	@param $key_prob - 選單ID
 */
function teach_prob($key_prob)
{
	global $curr_name,$curr_teach_id,$curr_teach_condition;
	$prob = array ("teach_list.php"=>"基本資料","teach_post.php"=>"任職資料","teach_class.php"=>"課務安排");
	echo "<table align=center  bgcolor=#D0DCE0 ><tr>";
	$i =1;
	while ( list( $key, $val ) = each( $prob ) ){
		if ($key_prob == $i++)
			echo "<td bgcolor=yellow ><a href=\"$key?curr_teach_id=$curr_teach_id&curr_teach_condition=$curr_teach_condition\">$val</a></td>";
		else
			echo "<td><a href=\"$key?curr_teach_id=$curr_teach_id&curr_teach_condition=$curr_teach_condition\">$val</a></td>";
	}
	echo "<td nowrap> -- <b><font color=blue>$curr_name</font></b></td>
	</tr></table>";
}
?>