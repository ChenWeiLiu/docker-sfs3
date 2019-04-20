<?php
// $Id: stud_psy_tran1.php 6150 2010-09-14 03:54:47Z brucelyc $

include "config.php";

sfs_check();

if($_POST['BtnSubmit']=='匯入')
{
	if ($_FILES['import']['size'] >0 && $_FILES['import']['name'] != "")
	{
		//讀出csv內容
		$items_arr=array();
		$fp = fopen($_FILES['import']['tmp_name'],"r");
		while ($data = sfs_fgetcsv($fp,2000, ","))
		{
  			$items_arr[]=$data;
		}
		fclose($fp);
		
		//echo "<PRE>";
		//print_r($items_arr);
		//echo "</PRE>";
				
		//準備匯入sql
		if(count($items_arr))
		{	$duplicated="<font size=2 color=#FF0000>";
			$sql.='INSERT INTO stud_psy_test(year,semester,student_sn,item,score,model,standard,pr,explanation,test_date,teacher_sn) VALUES';
			foreach($items_arr as $key=>$value)
			{
				$serial++;
				if($key)    //第一列習慣為標題列  不匯入
				{
					//檢查是否已有紀錄  若有  則不予匯入
					$record_year=$value[0];
					$record_semester=$value[1];
					$record_student_sn=$value[2];
					$record_item=$value[3];
					$record_test_date=$value[9];
					$check_sql="SELECT sn FROM stud_psy_test WHERE year=$record_year AND semester=$record_semester AND student_sn=$record_student_sn AND test_date='$record_test_date' AND item='$record_item';";
					$check_res=$CONN->Execute($check_sql) or user_error("檢查是否重複匯入測驗紀錄失敗！<br>$check_sql",256);
			
					if(!$check_res->recordcount()){
					
						$items_value='';
						foreach($value as $field)
						{
							$items_value.='"'.$field.'",';
						}
						$sql.="(".substr($items_value,0,-1)."),";
					} else $duplicated.="#$serial $record_year 學年度第 $record_semester 學期 $record_student_sn $record_item $record_test_date 重複不予匯入!! <BR>";
				}
			}
			$duplicated.="</font>";
			
			$sql=substr($sql,0,-1);
			
			//echo $sql."<BR><BR>";
			
			$sql=str_replace('""','NULL',$sql);
			//echo $sql;
			//開始進行匯入
			$res=$CONN->Execute($sql) or user_error("匯入測驗紀錄失敗！<br>$sql<br><br>有可能是本檔案已經匯入過了!!",256);
			$executed='<BR><BR><font color=#0000FF>◎ '.date('Y/m/d h:i:s')." 已自 ".$_FILES['import']['name']." 匯入測驗資料</font>";
		}
	}
}


head("心理測驗記錄轉表");
 print_menu($menu_p);

$main="<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%' id='AutoNumber1'>";
$main.="<form name='myform'  enctype='multipart/form-data' method='post' action='$_SERVER[PHP_SELF]'>
<tr bgcolor='#EEEEEE'>
<td colspan='2'>$submenu</td>
</tr>
  <tr bgcolor='#FFCCCC'><td align='center'>說　　明</td><td align='center'>轉換步驟</td></tr><tr>
    <td width='45%'>
<ul>
  <li>
  <p style='margin-top: 0; margin-bottom: 0'><font color='#FF0000' size='2'>為何要進行轉表？</font></p>
  <p style='margin-top: 0; margin-bottom: 0'><font size='2'>SFS3原心理測驗統計紀錄源自教育部學籍資料交換標準 XML2.0 
  設計，96年公佈之3.0標準與原2.0不同。</font></p>
  <p style='margin-top: 0; margin-bottom: 0'><font size='2'>大部分學校反應 2.0 
  標準與學校實務操作有扞格，採行欄位自行定義對照的妥協方式進行紀錄。</font></p>
  <p style='margin-top: 0; margin-bottom: 0'><font size='2'>新公佈的 XML 3.0 
  標準符合既有學校運作模式定義，唯舊有紀錄必須進行轉表動作。</font></p>
  </li>
  <li>
  <p style='margin-top: 0; margin-bottom: 0'><font color='#FF0000' size='2'>一定要進行轉表？</font></p>
  <p style='margin-top: 0; margin-bottom: 0'><font size='2'>輔導紀錄表相關表冊日後會將資料參照轉換到新紀錄表來，若貴校未進行轉表，將會有資料擷取的問題。</font></p>
  </li>
  <li>
  <p style='margin-top: 0; margin-bottom: 0'><font color='#FF0000' size='2'>怎樣進行轉表？</font></p>
  <p style='margin-top: 0; margin-bottom: 0'>
  <font size='2'>SFS3開發團隊考量各校自行定義的欄位參照不同，且單一欄位可能填具了複合性的資料。在很難統一找出邏輯運算規則的情況下，須以手動的方式進行轉表動作。</font></p>
  </li>
  <li>
  <p style='margin-top: 0; margin-bottom: 0'><font color='#FF0000' size='2'>轉表的程序為何？</font></p>
  <p style='margin-top: 0; margin-bottom: 0'><font size='2'>轉表的程序分4階段：</font></p>
  <p style='margin-top: 0; margin-bottom: 0'><font size='2'>(1)將原紀錄轉出為CSV檔。</font></p>
  <p style='margin-top: 0; margin-bottom: 0'><font size='2'>(2)下載新格式。</font></p>
  <p style='margin-top: 0; margin-bottom: 0'><font size='2'>(3)將該檔案以人工審閱調整後，以新格式儲存。</font></p>
  <p style='margin-top: 0; margin-bottom: 0'><font size='2'>(4)將新格式的檔案匯入。</font></p>
  </li>
  <li>
  <p style='margin-top: 0; margin-bottom: 0'><font color='#FF0000' size='2'>轉表後的運作要注意的地方？</font>
  <p style='margin-top: 0; margin-bottom: 0'><font size='2'>(1)日後心理測驗紀錄改為在&quot;心理測驗記錄(XML3.0新) &quot;填寫。</font></p>
  </li>
</ul>
    </td>
    <td width='55%' valign='top'>
    <a href='old_psy_2_csv.php'>STEP 1: 按此下載舊表資料。</a><BR><BR>
    <a href='stud_psy_test.csv'>STEP 2: 下載預備匯入的CSV新格式。</a><BR><BR>
    STEP 3: 對照新格式，將舊資料編輯改正為新格式(記得將欄位用雙引號包覆)。<BR><BR>
    STEP 4: 匯入：<input type='file' name='import' size=15><input type='submit' value='匯入' name='BtnSubmit'><BR><BR>
    $executed<BR><BR>$duplicated
    </td>
  </tr>
</table>
<input type='hidden' name='sel' value='$sel'>
</form>
";

echo $main;

foot();


?>
