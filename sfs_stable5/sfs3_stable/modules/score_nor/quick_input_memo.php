<?php
// $Id: quick_input_memo.php 7701 2013-10-23 08:09:14Z smallduh $

// 取得設定檔
include "config.php";

//使用者認證
sfs_check();

//顯示欄數
$col_num = 3;
$signBtn = "登錄評語";
$temp_path = $UPLOAD_PATH.$path_str;

$seme_year_seme=($_GET['seme_year_seme'])?$_GET['seme_year_seme']:$_POST['seme_year_seme'];
$class_id=($_GET['class_id'])?$_GET['class_id']:$_POST['class_id'];

if($_POST[save_memo] =="y") {

	switch($_POST[save_type]){
		case "input":
				//存入資料庫 
				$sn_id_temp_arr = explode(",",$_POST[sn_id_hidden]);
				$error = false;
				
				while(list($id,$sn)=each($sn_id_temp_arr)) {
					
					if($sn){
					//如果沒有輸入代號,不更新評語
						
						if($_POST["m_$sn"]!=""){
							$comment="";
							$m_id = explode(";",$_POST["m_$sn"]);
							for($i=0;$i<count($m_id);$i++){
								$t_comment = addslashes(trans_id_to_memo($m_id[$i]));
							
								if(!$t_comment){
									$msg .= "編號".($id+1).":沒有".$_POST["m_$sn"]."這個代號的評語<br>";
									$error = true;
									continue;
									//exit;
								}
								$comment .= $t_comment."，";	
							}
							$comment = substr($comment,0,-2);	
							$query = "update  stud_seme_score_nor set ss_score_memo='".$comment."' where student_sn =$sn and seme_year_seme =$seme_year_seme";
							$CONN->Execute($query) or die($query);
							
						}	
					}
				}
				
				break;
		case "import":
				echo "處理中!!!!<br>";
				
				//檢查檔名是否相符
				$filename=$seme_year_seme."_".$class_id."_nor_memo.csv";
				if (strcmp($filename,$_FILES['upload_file']['name'])!= 0){
					echo "匯入檔名錯誤 !! ,請找尋 $filename 檔名匯入!!";
					exit;
				}				
				$temp_file= $temp_path."nor_memo.csv";	
				
				copy($_FILES['upload_file']['tmp_name'] , $temp_file);	
				$fd = fopen($temp_file,"r");
				//取出該班學生student_sn放入陣列
				$sql="select a.student_sn,a.seme_num  from stud_seme a,stud_base b where a.seme_year_seme='$seme_year_seme' and a.seme_class='$class_id' and a.student_sn=b.student_sn and b.stud_study_cond=0 order by a.seme_num";

				$rs=$CONN->Execute($sql) or die($sql);
				$all_stud_sn = array();
				
				while (!$rs->EOF) {
					$all_stud_sn[]=$rs->fields[student_sn];
					$rs->MoveNext();
				}
				
				$j =0;
				$error = false;
				while($ck_tt = sfs_fgetcsv($fd, 2000, ",")) {
					if ($j++ == 0) //第一筆為抬頭，不檢查
                    				continue ;
                			
					if (substr($ck_tt[0],0,1)==0)
						$student_sn= substr($ck_tt[0],1);
					else
						$student_sn= trim($ck_tt[0]);
										
					if(!in_array($ck_tt[0],$all_stud_sn)){
						$msg .= "座號".$ck_tt[2]."的學生流水號".$student_sn."不對!!<br>";
						$error = true;
						continue;
					}
					
					//有評語資料才處理
					$comment="";		
					if($ck_tt[4]){
						//如果是代碼,先轉換資料
						if($_POST[m_type]=="digit"){
							$m_id = explode(";",$ck_tt[4]);	
							for($i=0;$i<count($m_id);$i++){
								$t_comment = addslashes(trans_id_to_memo($m_id[$i]));	
								
								if(!$t_comment){
									$msg .= "座號".$ck_tt[2].":沒有".$ck_tt[4]."這個代號的評語<br>";
									$error = true;
									continue;							
									//exit;
								}
								$comment .= $t_comment."，";	
							}
							$comment = substr($comment,0,-2);
						}
						else
							$comment = addslashes($ck_tt[4]);	
						$query = "update  stud_seme_score_nor set ss_score_memo='".$comment."' where seme_year_seme = $seme_year_seme and student_sn=$student_sn";
						$CONN->Execute($query) or die($query);
						
					}
					else{
						$msg .= "座號".$ck_tt[2].":沒有輸入評語資料<br>";
						$error = "true";
					}	
					$j++;
													    			
				}
				unlink($temp_file);
				
				break;	
		case "export":
   				$filename = $_POST[comm_length]."_".$_POST[level]."_memo.csv";
   				
   				$kind_sel="select kind_name from comment_kind where kind_serial = $_POST[comm_length] and (kind_teacher_id='0' or kind_teacher_id='$teacher_id')";
				$comm_kind=$CONN->Execute($kind_sel);
				
  				$level_sel="select level_serial,level_name  from comment_level where  level_teacher_id='0' or level_teacher_id='$teacher_id'";
  				
				$comm_level=$CONN->Execute($level_sel);
				$level_array=array();
				while(!$comm_level->EOF){
					$level_array[$comm_level->fields[level_serial]]=$comm_level->fields[level_name];
					$comm_level->MoveNext();				
				}
   				if($_POST[level]=="all")
   					$what_level="";
   				else 	$what_level = "and level='$_POST[level]'";
   				$sel="select serial,level,comm from comment where kind='$_POST[comm_length]' $what_level and (teacher_id='0' or teacher_id='$teacher_id') order by level,serial";
				$comm_text=$CONN->Execute($sel);
				$str = "序號,類型,等級,評語\n";
				while(!$comm_text->EOF){
					$str .= $comm_text->fields[serial].",".$comm_kind->fields[kind_name].",".$level_array[$comm_text->fields[level]].",".$comm_text->fields[comm]."\n";
					$comm_text->MoveNext();
				}
   				
    				header("Content-disposition: filename=$filename");
    				header("Content-type: application/octetstream ; Charset=Big5");
    				//header("Pragma: no-cache");
    								//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

    				header("Expires: 0");				
				echo $str;
				exit;
				break;
		
	}
	if ($_POST[save_memo]) {
			$today=date("Y-m-d G:i:s",mktime (date("G"),date("i"),date("s"),date("m"),date("d"),date("Y")));
			$teacher_sn=$_SESSION['session_tea_sn'];
			$sel_year = intval(substr($seme_year_seme,0,3));
			$sel_seme = substr($seme_year_seme,3,1);
			$year = sprintf("%02d",substr($class_id,0,1));
			$sel_class_id = substr($seme_year_seme,0,3)."_".$sel_seme."_".$year."_".substr($class_id,1,2);
			$sql_update="select ccm_id from class_comment_admin where teacher_sn='$teacher_sn' and class_id='$sel_class_id' and sel_year='$sel_year' and sel_seme='$sel_seme'";
			$rs=$CONN->Execute($sql_update) or die($sql_update);
			$update_data=$rs->FetchRow();
			if (empty($update_data)) {
				$sql_update="insert into class_comment_admin (teacher_sn,class_id,sel_year,sel_seme,update_time,update_teacher_sn,sendmit) values ('$teacher_sn','$sel_class_id','$sel_year','$sel_seme','$today','$teacher_sn','1')";
			} else {
				$ccm_id=$rs->$update_data[ccm_id];
				$sendmit=($send_comment)?'0':'1';
				$sql_update="update class_comment_admin set update_time='$today',update_teacher_sn='$teacher_sn',sendmit='$sendmit' where ccm_id='$ccm_id'";
			}
			$CONN->Execute($sql_update) or die($sql_update);
	}
	echo "<html><body>
	<script LANGUAGE=\"JavaScript\">
	window.opener.document.form2.submit(); ";
	if(!$error)		
		echo "\n  alert('作業完成!!');\n  window.close(); ";
		   
   	echo "</script>
	 $msg
	</body>
	</html>";
	exit;
}


switch ($_GET[act]){
	case "makefile":
			
			make_import_file();
			break;
	case "importfile":
			show_head("匯入描述文字檔案");
			import_csv();
			
			break;
	case "exportfile":
			show_head("匯出描述文字代號對應檔案");
			export_memo();
			break;	
				
	default:
			show_head("輸入描述文字代號");
			main();
			break;
	
}	


function main(){
	global $CONN,$seme_year_seme,$class_id,$col_num,$signBtn;
$html="	
<table border=\"0\" bgcolor=\"#9ebcdd\" cellspacing=\"1\" cellpadding=\"3\">
<tr bgcolor=\"white\">
<td>
<form name=\"myform1\" action=".$_SERVER['PHP_SELF']." method=\"post\">";
//檢查stud_seme_score_nor是否已經有學生資料
	$sql="select a.student_sn,a.seme_num ,b.stud_name from stud_seme a,stud_base b where a.seme_year_seme='$seme_year_seme' and a.seme_class='$class_id' and a.student_sn=b.student_sn and b.stud_study_cond=0 order by a.seme_num";
	$rs=$CONN->Execute($sql) or die($sql);;
	$i=0;
	$stud_sn=array();
	while (!$rs->EOF) {
		$sn=$rs->fields["student_sn"];
		$stud_sn[]=$sn;
		$all_sn.=$sn.",";
		$st_name[$sn] = $rs->fields["stud_name"];
		$i++;
		$rs->MoveNext();
	}
	$all_sn=substr($all_sn,0,-1);
	$stud_numbers=$i;


$query = "select count(student_sn) as cc from stud_seme_score_nor where student_sn in ($all_sn) and seme_year_seme='$seme_year_seme'";
$res = $CONN->Execute($query);

if ($res->fields[0]<$stud_numbers) {
	
	for($j=0;$j<count($stud_sn);$j++){
		$sst = $stud_sn[$j];
		
		$sql="select student_sn from stud_seme_score_nor where student_sn='$sst'  and seme_year_seme='$seme_year_seme'";
		$rs=$CONN->Execute($sql);
		if (empty($rs->fields[student_sn])) {
			$query = "INSERT INTO stud_seme_score_nor(seme_year_seme,student_sn,ss_id,ss_score_memo)values('$seme_year_seme','$sst','0','')";
			$CONN->Execute($query);
		}
		$res->MoveNext();
	}
}

//檢查結束

$query = "select a.student_sn,a.ss_score_memo,b.seme_num from stud_seme_score_nor a ,stud_seme  b where a.student_sn=b.student_sn and a.seme_year_seme='$seme_year_seme' and b.seme_year_seme='$seme_year_seme' and b.student_sn in ($all_sn) order by b.seme_num ";
$res= $CONN->Execute($query) or die($query);

	$html .="<table border=0 bgcolor=\"#9ebcdd\" cellspacing=\"1\" cellpadding=\"3\">\n";
	$ii =0;
	$sn_id_hidden =""; 		
	while(!$res->EOF){

		$sn = $res->fields['student_sn'];		
		$sit_num = $res->fields['seme_num'];
		$stud_name = $st_name[$sn];

		if($ii % $col_num == 0)
			$html .= "<tr bgcolor='white'>";
		$html .= "<td>$sit_num</td>\n";
		if($res->fields[ss_score_memo])  $html .= "<td nowrap bgcolor=\"#C4D9FF\">$stud_name</td>\n";
			else 	$html .= "<td nowrap bgcolor=\"#FED3DB\">$stud_name</td>\n";
		$html .= "<td><input type=\"text\" name=\"m_$sn\" size=6 maxlength=10 value=\"\" onFocus=\"set_ower(this,$ii)\" onBlur=\"unset_ower(this)\"></td>\n";
		
		if($ii++ % $col_num == ($col_num-1))
			$html .= "</tr>\n";
		$sn_id_hidden .= "$sn,";
		$res->MoveNext();
	}
	
	if(($ii%$col_num)!=0){
		$html .= "<td colspan=\"".(3*($col_num-($ii%$col_num1)))."\"></td>"; 
		$html .= "</tr>\n";
	}		
	$html .= "</table>
<input type=\"button\" name=\"do_key\" value=".$signBtn." onClick=\"document.myform1.submit()\">
&nbsp;&nbsp;<input type=\"button\" name=\"go_away\" value=\"放棄\" onClick=\"check_change()\">
&nbsp;&nbsp;<input type=\"button\" name=\"reset_allBtn\" value=\"清空\" onClick=\"reset_all()\">
<input type=\"hidden\" name=\"seme_year_seme\" value=\"".$seme_year_seme."\">
<input type=\"hidden\" name=\"class_id\" value=\"".$class_id."\">
<input type=\"hidden\" name=\"sn_id_hidden\" value=\"".$sn_id_hidden."\">
<input type=\"hidden\" name=\"save_memo\" value=\"y\">
<input type=\"hidden\" name=\"save_type\" value=\"input\">
</form>
</td>
<td valign=\"top\">使用說明：
<li>已經有描述文字資料學生姓名背景顏色為<span style=\"background-color: #C4D9FF\">藍色</span>，尚未有資料學生姓名背景為<span style=\"background-color: #FED3DB\">紅色</span>。</li>
<li>如果沒有輸入資料，則資料不會更新。</li> 
<li>要延續評語，請使用分號(;)分隔，例如:56;12。</li> 

</td>
</table>
<script >
var ss=0;
var is_change = false;
function set_default(){
document.myform1.elements[ss].focus();
}

function check_change(){
if(is_change){
	if (confirm('您已經更改資料是否要離開 ?'))
		window.close();
}
else
	window.close();
}


function set_ower(thetext,ower) {
ss=ower;
thetext.style.background = '#FFFF00';
//thetext.select();
return true;
}

function unset_ower(thetext) {
thetext.style.background = '#FFFFFF';
return true;
}

function reset_all() {
	for (var i=0;i<document.myform1.elements.length;i++)
	 {
	    var e = document.myform1.elements[i];
	    if (e.type == 'text')
        	       e.value = '';
	}
  document.myform1.elements[0].focus();
}

// handle keyboard events
if (navigator.appName == \"Mozilla\")
   document.addEventListener(\"keyup\",keypress,true);
else if (navigator.appName == \"Netscape\")
   document.captureEvents(Event.KEYPRESS);

if (navigator.appName != \"Mozilla\")
    document.onkeypress=keypress;

function keypress(e) {
	
   if (navigator.appName == \"Microsoft Internet Explorer\")
      tmp = window.event.keyCode;
   else if (navigator.appName == \"Navigator\")
	tmp = e.which;
   else if (navigator.appName == \"Navigator\"||navigator.appName == \"Netscape\")
       tmp = e.keyCode;
  if( document.myform1.elements[ss].type != 'text')
		return true;
        else if (tmp == 13){ 
		var tt = parseFloat(document.myform1.elements[ss].value);
		if (isNaN(tt)){			
			alert('錯誤的代號!');
			document.myform1.elements[ss].value ='';
			return false;
		}
		else{
			ss++;
			document.myform1.elements[ss].focus();
			is_change = true;
			return true;
		}	
		
	}
        else    return true;
}
</script>
</body>
</html>";
	echo $html;
	

}
//取得評語內容
function trans_id_to_memo($id){
	global $CONN,$teacher_id;
	$sql = "SELECT comm FROM comment WHERE serial='$id' and (teacher_id='0' or teacher_id='$teacher_id')";

	$rs=$CONN->Execute($sql);
	$comm = $rs->fields['comm'];
	return $comm;	
	
}




//匯入檔案
function import_csv(){
	global $seme_year_seme,$class_id,$ss_id;;
$html="	
  <table border=\"0\" width=\"92%\" bgcolor=\"#9ebcdd\" cellspacing=\"1\" cellpadding=\"2\">
    <tr>
      <td width=\"50%\" bgcolor=\"#C4D9FF\">
       <form method=\"POST\" action=\"".$_SERVER[PHP_SELF]."\" enctype=\"multipart/form-data\" name=\"myform1\">
       		<font size=2>請按『瀏覽』選擇匯入檔案來源：</font><br><input type=file name='upload_file'><br>
       		<font size=2>請選擇匯入檔案評語類型：</font><br>
       		<input type=\"radio\" value=\"digit\" name=\"m_type\">代碼
       		<input type=\"radio\" value=\"word\" name=\"m_type\">文字內容
       		
          <p>
          <input type=\"submit\" value=\"匯入檔案\" name=\"B1\" onClick=\"return checkok();\"></p>
	<input type=\"hidden\" name=\"ss_id\" value=\"".$ss_id."\">
	<input type=\"hidden\" name=\"class_id\" value=\"".$class_id."\">
	<input type=\"hidden\" name=\"seme_year_seme\" value=\"".$seme_year_seme."\">
	<input type=\"hidden\" name=\"save_memo\" value=\"y\">
	<input type=\"hidden\" name=\"save_type\" value=\"import\">
        </form>
        <p>　</td>
        <td bgcolor=\"#FFFFFF\" valign=\"top\">
        <b>使用說明：</b>
        <li>匯入檔案，請依照製作出之檔案輸入，第一列為欄位名稱，不會被處理。</li>
        <li>要延續評語，請使用分號(;)分隔，例如:56;12。</li> 
        <li>匯入檔案類型，可以為文字評語或數字代碼，在匯入時要選擇正確類型。</li>
        </td>
    </tr>
  </table>
<script language=\"JavaScript\">  
function set_default(){
}
function checkok() {
	if (document.myform1.upload_file.value=='') {
		  alert('必須選擇檔案');
		  return false;
	}	
	if (!(document.myform1.m_type[0].checked ||document.myform1.m_type[1].checked )) {
		  alert('必須選擇檔案內評語類型');
		  return false;
	}		
}
//-->
</script>  
";
      
 echo $html;	
	
}
//製作匯入檔
function make_import_file(){
	global $CONN,$seme_year_seme,$class_id,$ss_id;	
	$query="select a.student_sn,a.seme_num ,b.stud_name ,b.stud_id from stud_seme a,stud_base b where a.seme_year_seme='$seme_year_seme' and a.seme_class='$class_id' and a.student_sn=b.student_sn and b.stud_study_cond=0 order by a.seme_num";
	$res=$CONN->Execute($query) or die($query);;

   	$filename = $seme_year_seme."_".$class_id."_nor_memo.csv";
    	header("Content-disposition: filename=$filename");
    	header("Content-type: application/octetstream ; Charset=Big5");
    	//header("Pragma: no-cache");
    					//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

    	header("Expires: 0");
    	$str = "學生流水號,學生學號,座號,姓名,評語或評語代碼\n";
	while(!$res->EOF){
		
		$str.= $res->fields[student_sn].",".$res->fields[stud_id].",".$res->fields[seme_num].",".$res->fields[stud_name].",\n";
		$res->MoveNext();
	}
	echo $str;
	exit;
}
//匯出評語檔
function export_memo(){
	global $CONN,$teacher_id;

	$sel="select * from comment_kind where kind_teacher_id='0' or kind_teacher_id='$teacher_id'";
	$comm_len=$CONN->Execute($sel);
	while(!$comm_len->EOF){
		$tmp_value=$comm_len->fields[0];
		$tmp_name=$comm_len->fields[2];
		$selected=($comm_length==$tmp_value)?"selected":"";
		$len.="<option value='$tmp_value' $selected>$tmp_name</option>\n";
		if($selected=='selected') $tmp_kind=$tmp_name;
		$comm_len->MoveNext();
	}
	$comm_length_select="類別：<select name='comm_length'>
				<option value=''>選擇類別</option>$len</select>\n";
	
	$sel="select * from comment_level where level_teacher_id='0' or level_teacher_id='$teacher_id'";
	$comm_lev=$CONN->Execute($sel);
	while(!$comm_lev->EOF){
		$tmp_value=$comm_lev->fields[0];
		$tmp_name=$comm_lev->fields[2];
		$selected=($level==$tmp_value)?"selected":"";
		$select.="<option value='$tmp_value' $selected>$tmp_name</option>\n";
		if($selected=='selected') $tmp_level=$tmp_name;
		$comm_lev->MoveNext();
	}
	$level_select="等級：<select name='level'>
			<option value=''>選擇等級</option>$select
			<option value='all'>全部</option>
			</select>\n";
	
	
echo "
<table cellSpacing=\"1\" cellPadding=\"4\" width=\"100%\" bgColor=\"#1e3b89\">
  <tbody>
    <tr bgColor=\"#e1ecff\">
      <td width=\"50%\">
        <form name=\"myform1\" action=\"".$_SERVER[PHP_SELF]."\" method=\"post\">
        ".$comm_length_select.$level_select."
        
       </td> 
       <td rowspan=\"2\" valign=\"top\" bgColor=\"#ffffff\">
        <b>使用說明：</b>
        <li>教師只能使用共同的或自己建立的評語．</li>
        </td>
     </tr>
     <tr bgColor=\"#ffffff\">
       <td><input type=\"submit\" value=\"匯出代號對應表\" name=\"send_comm_back\" onClick=\"return checkok();\" >
       <input type=\"hidden\" name=\"save_memo\" value=\"y\">
	<input type=\"hidden\" name=\"save_type\" value=\"export\">
       </td>
    </tr>
  </tbody>
</table>
<script language=\"JavaScript\">
function checkok() {
	if (document.myform1.comm_length.selectedIndex=='') {
		  alert('類別必須選擇');
		  return false;
	}	
	if (document.myform1.level.selectedIndex=='') {
		  alert('等級必須選擇');
		  return false;
	}		
}

function set_default(){
}
//-->
</script>		
</form>";

	
}
//
function show_head($msg){
	global $seme_year_seme,$class_id,$ss_id,$teacher_course;
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; Charset=Big5\">
<head>
<title>".$msg."</title>
</head>
<body onLoad=\"set_default()\">
<table border=\"0\" width=\"90%\">
	<tr>
	<td align=\"center\"><font size=2><a href=\"".$_SERVER['PHP_SELF']."?class_id=".$class_id."&teacher_course=".$teacher_course."&ss_id=".$ss_id."&seme_year_seme=".$seme_year_seme."\">輸入代號</a></font>
	</td>
	<td align=\"center\"><font size=2><a href=\"".$_SERVER['PHP_SELF']."?class_id=".$class_id."&teacher_course=".$teacher_course."&ss_id=".$ss_id."&seme_year_seme=".$seme_year_seme."&act=importfile"."\">匯入評語檔案</a></font>
	</td>
	<td align=\"center\"><font size=2><a href=\"".$_SERVER['PHP_SELF']."?class_id=".$class_id."&teacher_course=".$teacher_course."&ss_id=".$ss_id."&seme_year_seme=".$seme_year_seme."&act=makefile"."\">製作匯入檔案</a></font>
	</td>
	<td align=\"center\"><font size=2><a href=\"".$_SERVER['PHP_SELF']."?class_id=".$class_id."&teacher_course=".$teacher_course."&ss_id=".$ss_id."&seme_year_seme=".$seme_year_seme."&act=exportfile"."\">匯出代號對應表</a></font>
	</td>
	</tr>
</table>";	
}		
		
?>
