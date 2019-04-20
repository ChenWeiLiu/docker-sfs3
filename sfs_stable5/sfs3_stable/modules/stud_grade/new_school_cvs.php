<?php
//$Id: new_school_cvs.php 8014 2014-04-30 22:11:33Z yjtzeng $
//載入設定檔
require ("config.php");

// 認證檢查
sfs_check();

$english_name=$_POST["english_name"]?"checked":"";
$move_in=$_POST["move_in"]?"checked":"";

//echo $english_name."---".$move_in;

($IS_JHORES==0) ? $UP_YEAR=6:$UP_YEAR=9;//判斷國中小

$postBtn = "新生入學資料轉出csv檔";
$class_name = class_base();
if ($_POST[do_key]==$postBtn){
	$curr_year =curr_year()+1;
	$new_school_str=($_POST[curr_grade_school])?"and g.new_school= '$_POST[curr_grade_school]'":"";
	//$str ="入學年,舊校名,身分證字號,姓名".($english_name)?",英文姓名":"".",性別(男生:1，女生:2),電話,生日（西元）,家長姓名,住址,原班級".($move_in)?",戶籍遷入日期":""."\n";
	$str ="入學年,舊校名,身分證字號,姓名,";
	$str.=($english_name)?"英文姓名,":"";
	$str.="性別(男生:1，女生:2),電話,生日（西元）,家長姓名,戶籍住址,原班級";
	$str.=($move_in)?",戶籍遷入日期":"";
	$str.=($_POST[curr_grade_school])?"":",升學學校";
	$str.=($_POST[stud_addr_2])?",聯絡住址":"";
	$str.=($_POST[stud_tel_3])?",聯絡手機":"";
	$str.="\r\n";
	//$sqlstr = "select s.stud_id, s.stud_person_id, s.stud_addr_1, s.stud_addr_2,stud_tel_3, s.stud_name, s.stud_sex, s.stud_birthday, s.stud_tel_1, s.curr_class_num, g.grad_sn, g.new_school, d.guardian_name, s.stud_name_eng, s.addr_move_in from stud_base as s, stud_domicile d, grad_stud g where s.stud_id=g.stud_id AND s.stud_id=d.stud_id and s.stud_study_cond='0' $new_school_str and s.curr_class_num like '$UP_YEAR%' order by g.new_school,s.curr_class_num";
	//$sqlstr = "select s.stud_id, s.stud_person_id, s.stud_addr_1, s.stud_addr_2,stud_tel_3, s.stud_name, s.stud_sex, s.stud_birthday, s.stud_tel_1, s.curr_class_num, g.grad_sn, g.new_school, d.guardian_name, s.stud_name_eng, s.addr_move_in from stud_base as s left join stud_domicile d on s.stud_id=d.stud_id, grad_stud g where s.stud_id=g.stud_id and s.stud_study_cond='0' $new_school_str and s.curr_class_num like '$UP_YEAR%' order by g.new_school,s.curr_class_num";
	$sqlstr = "select s.stud_id, s.stud_person_id, s.stud_addr_1, s.stud_addr_2,stud_tel_3, s.stud_name, s.stud_sex, s.stud_birthday, s.stud_tel_1, s.curr_class_num, g.grad_sn, g.new_school, d.guardian_name, s.stud_name_eng, s.addr_move_in from stud_base as s left join stud_domicile d on s.student_sn=d.student_sn, grad_stud g where s.student_sn=g.student_sn and s.stud_study_cond='0' $new_school_str and s.curr_class_num like '$UP_YEAR%' order by g.new_school,s.curr_class_num";
	//將s.stud_id=d.stud_id修改成 s.student_sn=d.student_sn。s.stud_id=g.stud_id改成s.student_sn=g.student_sn避免畢業生十年學號重複問題  modify by kai,103.4.30
	
	$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;    	
	
	while(!$result->EOF){
		//班級
		$c_name = $class_name[substr($result->fields[curr_class_num],0,-2)];
		$str.="\"".$curr_year."\",";
		$str.="\"".$SCHOOL_BASE[sch_cname_ss]."\",";
		$str.="\"".$result->fields[stud_person_id]."\",";
		$str.="\"".$result->fields[stud_name]."\",";
		$str.=($english_name)?"\"".($result->fields[stud_name_eng])."\",":"";
		$str.="\"".$result->fields[stud_sex]."\",";
		$str.="\"".$result->fields[stud_tel_1]."\",";
		$str.="\"".$result->fields[stud_birthday]."\",";
		$str.="\"".$result->fields[guardian_name]."\",";
		$str.="\"".$result->fields[stud_addr_1]."\",";
		$str.="\"".$c_name;
		$str.=($move_in)?"\",\"".($result->fields[addr_move_in]):"";
		$str.=($_POST[curr_grade_school])?"":"\",\"".$result->fields[new_school];
		$str.=($_POST[stud_addr_2])?"\",\"".$result->fields[stud_addr_2]:"";
		$str.=($_POST[stud_tel_3])?"\",\"".$result->fields[stud_tel_3]:"";
		$str.="\"\r\n";

		$result->MoveNext();
	}
	
	header("Content-disposition: attachment; filename=".$SCHOOL_BASE[sch_cname_ss].curr_year()."學年度升入".$_POST[curr_grade_school]."學生名冊.csv");
	header("Content-type: text/x-csv");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");

	echo $str;	
	exit;
}

head();
print_menu($menu_p);


?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr><td valign=top bgcolor="#CCCCCC" align=center >
<table width="80%" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td>  
  <form name ="myform" action="<?php echo $PHP_SELF ?>" method="post" >
   <table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"   class=main_body >	
   <tr>
	
	<td class=title_mbody colspan=4 align=center >
	<?php 
		$curr_grade_school=stripslashes($_REQUEST[curr_grade_school]);
		$def_grade_school = get_grade_school();	
		$sel1 = new drop_select();
		$sel1->s_name="curr_grade_school";
		$sel1->is_submit = true;
		$sel1->use_val_as_key = true;
		$sel1->top_option = "選擇轉出學校(未選則全列)";
		$sel1->id = $curr_grade_school;
		$sel1->arr = $def_grade_school;
		$sel1->do_select();
		
//		echo sprintf("%d學年第%d學期 ",$curr_year,$curr_seme);
		

	?>  <BR>
  <input type="checkbox" name="english_name" <?php echo $english_name; ?> onclick='this.form.submit()'>英文姓名　
  <input type="checkbox" name="move_in" <?php echo $move_in; ?> onclick='this.form.submit()'>戶籍遷入日期
  <input type="checkbox" name="stud_addr_2" <?php if ($_POST[stud_addr_2]) echo "checked" ?> onclick='this.form.submit()'>聯絡住址
  <input type="checkbox" name="stud_tel_3" <?php if ($_POST[stud_tel_3]) echo "checked" ?> onclick='this.form.submit()'>聯絡手機
  <BR><input type="submit" name="do_key" value="<?php echo $postBtn ?>">
</td>
   </tr>
</table>
</form>
   <table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"   class=main_body >	
   <tr class=title_sbody1 ><td align=center>班級</td><td align=center>
   座號</td><td align=center>學號</td><td align=center>姓名</td><?php echo ($english_name)?"<td>英文姓名</td>":""; ?><td align=center>升學學校</td><td>原班級</td><?php echo ($move_in)?"<td>戶籍遷入日期</td>":""; ?><?php echo ($_POST[stud_addr_2])?"<td>聯絡住址</td>":""; ?><?php echo ($_POST[stud_tel_3])?"<td>聯絡手機</td>":""; ?></tr>
<?php
	//學號、姓名、升學
          /*
          $sqlstr = "select s.stud_id , s.stud_name ,s.curr_class_num , 
             g.grad_sn , g.new_school  from stud_base as s  LEFT JOIN grad_stud as g ON s.stud_id=g.stud_id 
             where s.stud_study_cond = '0' and new_school= '$_POST[curr_grade_school]' and s.curr_class_num like '$UP_YEAR%' order by s.curr_class_num ";  
		  */
		  $sqlstr = "select s.stud_id , s.stud_name ,s.curr_class_num , 
             g.grad_sn , g.new_school  from stud_base as s  LEFT JOIN grad_stud as g ON s.student_sn=g.student_sn 
             where s.stud_study_cond = '0' and new_school= '$_POST[curr_grade_school]' and s.curr_class_num like '$UP_YEAR%' order by s.curr_class_num ";  
	//將s.stud_id=g.stud_id修改成 s.student_sn=g.student_sn。避免畢業生十年學號重複問題  modify by kai,103.4.30
	
	$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;    	
	 while ($row = $result->FetchRow() ) {
	        $stud_id = $row['stud_id'] ;
	        $stud_name = $row['stud_name'] ;
	        $stud_name_eng= $row['stud_name_eng'] ;
	        $addr_move_in=$row['addr_move_in'] ;
			$stud_addr_2=$row['stud_addr_2'] ;
			$stud_tel_3=$row['stud_tel_3'] ;
	        
	        $curr_class_num = $row['curr_class_num'] ;
	        $grad_sn = $row['grad_sn'] ;
	        $new_school = $row['new_school'] ;
		$cname = $class_name[substr($curr_class_num,0,-2)];
			$sel1->s_name = "change_class_$stud_id"; //選單名稱
			echo ($i++ % 2 ==0)? "<tr class=nom_1>":"<tr class=nom_2>";
   			echo "<td align=center>".substr($curr_class_num,0,3)."</td>"; 
   			echo "<td align=center>".substr($curr_class_num,-2)."</td>"; 
   			echo "<td align=center>$stud_id</td>"; 
   			echo "<td align=center>$stud_name</td>";
   			echo $english_name?"<td align=center>$stud_name_eng</td>":"";
   			echo "<td align=center>"; 
   			echo $new_school ;
   			echo "</td>";
			echo "<td>$cname</td>";	
			echo $move_in?"<td align=center>$addr_move_in</td>":"";
			echo $_POST[stud_addr_2]?"<td align=center>$stud_addr_2</td>":"";
			echo $move_in?"<td align=center>$stud_tel_3</td>":"";
   			echo "</tr>\n";
   	 }

?>
</table>
</td>
<td valign="top" width=300>
<!-- 說明 -->
<table
style="width: 100%; text-align: left; background-color: rgb(255, 255, 204);"
border="1" cellpadding="1" cellspacing="1">
<tbody>
<tr>
<td style="vertical-align: top;">
<ul>
<li>本程式可依不同的國中輸出新生名冊，方便學區內國中新生編班操作，關於新生編班參考 <a
href="../temp_compile/">&lt;&lt; 新生編班模組 &gt;&gt;</a></li>
</ul>
<ul>
<li>進行學生資料轉移時，請依個人資料保護法相關規定，避
免學生資料外洩。</li>
</ul>
</td>
</tr>
</tbody>
</table>


<!-- 說明結束 -->
</td>

</tr>
</table> 
</td>
</tr>
</table> 
<?php
foot();
?>
