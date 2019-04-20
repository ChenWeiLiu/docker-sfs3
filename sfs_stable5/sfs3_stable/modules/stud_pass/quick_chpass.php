<?php
//$Id: chc_seme.php 5310 2009-01-10 07:57:56Z hami $

//預設的引入檔，不可移除。
require_once "./module-cfg.php";
include_once "../../include/config.php";

//讀取 LDAP 設定
$query="select * from ldap limit 1";
$res=$CONN->Execute($query); // or die('Error! SQL='.$query);
if (!$res) {
	$LDAP['enable']=$LDAP['enable1']=0;
} else {
	$LDAP=$res->fetchrow();
}

//讀取學生密碼管理模組設定
$stud_pass=&get_module_setup("stud_pass");
//if ($stud_pass['quick_chpass']==0 or $LDAP['enable1']==1) {
if ($stud_pass['quick_chpass']==0) {
	echo "系統不允許在此變更密碼!";
	exit();
}

//目前選定年級，100指未指定
$c_curr_class=$_POST['c_curr_class'];
//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();
$c_curr_seme=$curr_year.$curr_seme;
$s_y = substr($c_curr_seme,0,3);
$s_s = substr($c_curr_seme,-1);
$tmp=&get_class_select($s_y,$s_s,"","c_curr_class","this.form.submit",$c_curr_class);

//做出年級與班級選單
if ($c_curr_class!='') {
	$class=sprintf('%d%02d',substr($c_curr_class,6,2),substr($c_curr_class,9,2));
	//取得學生名單
	$query="select a.stud_name,b.seme_num,b.student_sn from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_class='$class' and b.seme_year_seme='$c_curr_seme' and a.stud_study_cond=0 order by seme_num";
	$res=$CONN->Execute($query) or user_error("讀取錯誤!",256);
	$select_option="";
	while ($row=$res->fetchrow()) {
		$name=mb_substr($row['stud_name'],0,1,"BIG-5")."○".mb_substr($row['stud_name'],-2,null,"BIG-5");
		$select_option.="<option value=\"{$row['student_sn']}\">{$row['seme_num']}.{$name}</option>";
	}


}

//秀出網頁布景標頭
head("學生密碼變更");
if (isset($_POST['student_sn']) and isset($_POST['old_log_pass']) and isset($_POST['new_log_pass1']) and isset($_POST['new_log_pass2']) and $_POST['new_log_pass1']=$_POST['new_log_pass2']) {

	$res = change_stu_pass($_POST['student_sn'], $_POST['new_log_pass1'], $_POST['old_log_pass']);

	head ("學生密碼變更");
	if ($res>0) {
		echo "<div style='margin:auto;width:100%;text-align: center'><span style='color:#0000FF;font-size:20pt'>密碼變更成功!!</span></div>";
	} else {
		echo "<div style='margin:auto;width:100%;text-align: center'><span style='color:#FF0000;font-size:20pt'>密碼變更失敗!!</span></div>";
	}

	foot();

	exit();
}


?>
	<form action='<?php echo $_SERVER['PHP_SELF']?>' method='post' name="myform">
		<table style='width:100%;'>
			<tr><td style='text-align:center;padding:15px;'>
					<div  class='ui-widget-header ui-corner-top'  style='width:350px; padding:5px; margin:auto'>
						<span style='text-align:center;'>學生密碼變更</span>
					</div>
					<div  class='ui-widget-content ui-corner-bottom'  style='width:350px; padding:5px; margin:auto'>
						<table cellspacing='0' cellpadding='3' align='center'>
							<tr class='small'>
								<td nowrap>選擇班級</td><td nowrap>
									<?php echo $tmp;?>
								</td>
							</tr>
							<tr class='small'>
								<td nowrap>選擇學生</td>
								<td nowrap>
									<select size="1" name="student_sn" id="sutdent_sn">
										<option>請選擇...</option>
										<?php echo $select_option; ?>
									</select>
								</td>
							</tr>
							<tr class='small'>
								<td nowrap>輸入舊密碼</td>
								<td>
									<input type='password' id="old_log_pass" name='old_log_pass' size='20' maxlength='24'>
								</td>
							</tr>
							<tr class='small'>
								<td nowrap>輸入新密碼第一次</td>
								<td>
									<input type='password' id="new_log_pass1" name='new_log_pass1' size='20' maxlength='24'>
								</td>
							</tr>
							<tr class='small'>
								<td nowrap>輸入新密碼第二次</td>
								<td>
									<input type='password' id="new_log_pass2" name='new_log_pass2' size='20' maxlength='24'>
								</td>
							</tr>
							<tr class='small'>
								<td nowrap></td>
								<td><input type="button" id="confirm_change" value="確定要變更">
								</td>
							</tr>
						</table>

					</div>
				</td>
			</tr>
			<tr>
				<td align="center">
					<table cellspacing='0' cellpadding='3' style="margin-top:20px">
						<tr>
							<td>1. 密碼至少 4 個字元. </td>
						</tr>
						<tr>
							<td>2. 密碼最好混合數字、英文字母和符號，增加被別人猜中的難度. </td>
						</tr>
						<tr>
							<td>3. 不要使用家裡電話、身分證字號...等容易被猜中的數字當密碼. </td>
						</tr>

					</table>
				</td>
			</tr>
		</table>
	</form>


<?php
foot();
?>
<Script>
	$("#confirm_change").click(function(){
		var student_sn=$("#student_sn").val();
		var old_log_pass=$("#old_log_pass").val();
		var new_log_pass1=$("#new_log_pass1").val();
		var new_log_pass2=$("#new_log_pass2").val();

		if (student_sn=='') {
			alert("你必須選擇你的姓名!");
			return false;
		}

		if (old_log_pass=='') {
			alert("你必須輸入你目前使用的密碼!");
			$("#old_log_pass").focus();
			return false;
		}

		if (new_log_pass1=='') {
			alert("你必須輸入你要變更的密碼!");
			$("#new_log_pass1").focus();
			return false;
		}

		if (new_log_pass2=='') {
			alert("要變更的密碼必須打兩次哦! 而且兩次都要一樣!");
			$("#new_log_pass2").focus();
			return false;
		}

		if (new_log_pass1!=new_log_pass2) {
			alert("你輸入兩次要變更的密碼不一致! \n請重新輸入!");
			$("#new_log_pass1").val("");
			$("#new_log_pass2").val("");
			$("#new_log_pass1").focus();
			return false;
		}

		if (new_log_pass1.length<4) {
			alert("密碼太短!");
			$("#new_log_pass1").val("");
			$("#new_log_pass2").val("");
			$("#new_log_pass1").focus();
			return false;
		}

		document.myform.submit();

	});
</Script>
