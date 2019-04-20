<?php

//$Id: doc_add.php 8145 2014-09-23 08:21:31Z smallduh $

//設定檔載入檢查
include "docup_config.php";
// --認證 session 
sfs_check();

//------------------------
if ($_POST[key] == "新增"){ //新增文件
	for ($j = 1;$j < 4 ;$j++){
		$vtemp = 0;
		for ($i = 0;$i < 3; $i++){					
			$temp = "docup_share_".$j."_".($i+1);
			$vtemp += $_POST[$temp]*(1 << $i);
		
		}
		$docup_share .= $vtemp;
	}        
	if (is_file($_FILES[docup_store][tmp_name])) {
		//$subname = substr( strrchr( $GLOBALS[docup_store_name], "." ), 1 );
		if (!check_is_php_file($_FILES['docup_store']['name'])) {

			$temp_fname = explode("/",$_POST[fname]);			
			$docup_store = $temp_fname[count($temp_fname)-1];
			$docup_file_size = $_FILES[docup_store][size];
			$sql_insert = "insert into docup (docup_owerid,docup_p_id,docup_name,docup_date,docup_owner,docup_store,docup_share,teacher_sn,docup_file_size) values ('$_SESSION[session_log_id]','$_POST[docup_p_id]','$_POST[docup_name]','$now','".addslashes($_SESSION[session_tea_name])."','$docup_store','$docup_share','$_SESSION[session_tea_sn]','$docup_file_size')";
			$CONN->Execute($sql_insert)or trigger_error("SQL 錯誤 $sql_insert ",E_USER_ERROR);
			$query = "select count(docup_id) as cc ,max(docup_id) as mm from docup where docup_p_id='$_POST[docup_p_id]'";
			$result = $CONN->Execute($query)or trigger_error("SQL 錯誤 ",E_USER_ERROR);
			$cc =$result->fields[0];
			$mm =$result->fields[1];
			$query = "update docup_p set docup_p_count = $cc where docup_p_id='$_POST[docup_p_id]'";
			$CONN->Execute($query)or trigger_error("SQL 錯誤 ",E_USER_ERROR);

			$alias = $_SESSION[session_log_id]."_".$mm."_".$_FILES[docup_store][name];


			if (!copy($_FILES['docup_store']['tmp_name'],$filePath.$alias)){
				echo "檔案上傳失敗!請重新送出!<br>";
				foot();
				exit;
			}
		}
		else{
			echo "警告：請勿上傳php檔！<br>";
			foot();
			exit;
		}
	}
	else {
	        if ($_POST[txturl]) {
	            $sql_insert = "insert into docup (docup_owerid,docup_p_id,docup_name,docup_date,docup_owner,docup_store,docup_share,teacher_sn,docup_file_size , url ) values ('$_SESSION[session_log_id]','$_POST[docup_p_id]','$_POST[docup_name]','$now','$_SESSION[session_tea_name]','$docup_store','$docup_share','$_SESSION[session_tea_sn]','$docup_file_size' , '$_POST[txturl]' )";
			$CONN->Execute($sql_insert)or trigger_error("SQL 錯誤 $sql_insert ",E_USER_ERROR);      
	                
		}else {
	           echo "提示:無上傳檔案!<br>";
		   foot();
		   exit;
		}  
	}
	header ("Location: doc_list.php?docup_p_id=$_POST[docup_p_id]&doc_kind_id=$_POST[doc_kind_id]");
}


if ($is_standalone!="1") head();  
$post_office_p = room_kind();
$docup_p_id = $_POST[docup_p_id];
if($docup_p_id=='')
	$docup_p_id = $_GET[docup_p_id];
$sql_select = "select teacher_sn,docup_p_name,doc_kind_id from docup_p where  \n";
$sql_select .= "docup_p_id='$docup_p_id' ";
$result = $CONN->Execute($sql_select)or trigger_error("SQL 錯誤 $sql_select ",E_USER_ERROR) ;
$state_name = $post_office_p[$result->fields["doc_kind_id"]];
$docup_p_name = $result->fields["docup_p_name"];
$teacher_sn = $result->fields["teacher_sn"];
$doc_kind_id = $result->fields["doc_kind_id"];
?>
<script language="JavaScript">
    function validate() {
      var Ary = document.myform.docup_store.value.split('\\');
      document.myform.fname.value=Ary[Ary.length-1];
      return true;
    }
</script>

<form enctype="multipart/form-data" method="post" name="myform" action="<?php echo $_SERVER[PHP_SELF] ?>" onSubmit="return validate()" >
<input type="hidden" name="fname">
  <table class=module_body align=center>
    <caption>新增 <font color=blue><b> 
    <?php echo "$state_name--$docup_p_name"; ?>
    </b></font> 文件</caption>
    <tr> 
      <td align="right" valign="top">專案編號:</td>
      <td> 
        <?php echo "$teacher_sn-$docup_p_id" ?>
        <input type="hidden" name="docup_p_id" value="<?php echo $docup_p_id ?>">
        <input type="hidden" name="doc_kind_id" value="<?php echo $doc_kind_id ?>">
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top">文件名稱:</td>
      <td> 
        <input type="text" size="80" maxlength="80" name="docup_name" value="<?php echo $docup_name ?>">
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top">選擇檔案:</td>
      <td> 
        <input type="FILE" size="50" maxlength="50" name="docup_store" >
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top">或</td>
      <td> <font color="#FF0000">(只能選擇一種)</font></td>
    </tr>
    <tr> 
      <td align="right" valign="top">鏈結網頁:</td>
      <td> 
        <input type="text" name="txturl" size="80">
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top">分享設定:</td>
      <td> 所在處室： 
        <input type="checkbox" name="docup_share_1_1" value="1" checked>
        瀏覽&nbsp;&nbsp;&nbsp; 
        <input type="checkbox" name="docup_share_1_2" value="1" checked>
        修改&nbsp;&nbsp;&nbsp; 
        <input type="checkbox" name="docup_share_1_3" value="1" >
        刪除<br>
        本校人員： 
        <input type="checkbox" name="docup_share_2_1" value="1" checked>
        瀏覽&nbsp;&nbsp;&nbsp; 
        <input type="checkbox" name="docup_share_2_2" value="1" >
        修改&nbsp;&nbsp;&nbsp; 
        <input type="checkbox" name="docup_share_2_3" value="1" >
        刪除<br>
        網路來賓： 
        <input type="checkbox" name="docup_share_3_1" value="1" >
        瀏覽 <font color="#FF0000">(確定可以給全世界的人查看時，才選定！)</font></td>
    </tr>
    <tr> 
      <td align=center colspan=2 > 
        <input type="submit" name="key" value="新增">
      </td>
    </tr>
    <tr> 
      <td colspan=2 align=center> 
        <hr size=1>
        <?php echo "<a href=\"doc_list.php?docup_p_id=$docup_p_id&doc_kind_id=$doc_kind_id\">回文件列表</a>"; ?>
      </td>
    </tr>
  </table>

</form>

<?php
if ($is_standalone!="1") foot();
?>
