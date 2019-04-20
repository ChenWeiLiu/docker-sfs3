<?php
header('Content-type: text/html; charset=utf-8');
include_once ('config.php');
include_once ('my_function.php');

ini_set('max_execution_time', 0); //更改執行時間


if (!isset($_SESSION['LOGIN_ID'])) {
  echo "<Script language=\"JavaScript\">window.close();</Script>";
	exit();
}


//取得教師等級
$query="select teacher_sn from teacher_base where teach_id='".$_SESSION['LOGIN_ID']."'";
$result=mysql_query($query);
list($teacher_sn)=mysql_fetch_row($result);
$query="select post_kind from teacher_post where teacher_sn='".$teacher_sn."'";
$result=mysql_query($query);
list($POST_KIND)=mysql_fetch_row($result);

?>
<html>
<head>
<title>發送訊息</title>
<style>
A:link {font-size:9pt;color:#ff0000; text-decoration: none}
A:visited {font-size:9pt;color: #ff0000; text-decoration: none;}
A:hover {font-size:9pt;color: #ffff00; text-decoration: underline}
td {font-size:10pt}
</style>

<script src="./include/jquery.js" type="text/javascript"></script>
<script src='./include/jquery.MultiFile.js' type="text/javascript" language="javascript"></script>
<script type="text/javascript" src="./include/JSCal2-1.9/src/js/jscal2.js"></script>
<script type="text/javascript" src="./include/JSCal2-1.9/src/js/lang/b5.js"></script>
<link type="text/css" rel="stylesheet" href="./include/JSCal2-1.9/src/css/jscal2.css">

<script language="javascript">
function choice()
{
	if (document.form1.m_to.value=='') {
    document.form1.m_to.value=document.form1.set.value;
  }else{
    document.form1.m_to.value=document.form1.m_to.value+";"+document.form1.set.value;
  }
}
function checkdata1() {
	
	var save=1;
	 
	if (document.form1.msg.value=='') {
	  alert('您必須輸入內容!!');
	  document.form1.msg.focus();
	  save=0;
    return false;
	}
	 
	 
	//私人訊息
	if (document.form1.option1.value==0) {
	  if (document.form1.m_to.value=='') {
  		alert('沒有輸入訊息接收對象的帳號！');
  		document.form1.m_to.focus();
    	save=0;
    	return false;	    
	  }	
	}


	//電子圖
	if (document.form1.option1.value==4) {
	  if (document.form1.stdate.value=='') {
  		alert('沒有輸入起始日期！');
    	save=0;
    	return false;	    
	  }	
	  if (document.form1.enddate.value=='') {
  		alert('沒有輸入結束日期！');
    	save=0;
    	return false;	    
	  }	
	  if (document.form1.delay_sec.value=='') {
  		alert('沒有輸入延遲秒數！');
    	save=0;
    	return false;	    
	  }	
	  if (document.form1.pic_file.value=='') {
  		alert('沒有選擇檔案！');
    	save=0;
    	return false;	    
	  }	

	}

  if (save==1) {
   document.form1.submit();
  }
  
  return false;
  
} // end function

</script>
</head>
<body bgcolor="#FFFFFF">
	<?php
//輸入表單
if ($_POST['act']=="") {
?>
<div align="center">
  <center>
<form name="form1" method="post" action="msg_posting.php" enctype="multipart/form-data">
	<input type="hidden" name="act" value="">
	<input type="hidden" name="option1" value="0">

  <font color="#FF0000">發送訊息</font>
  <table border="1" cellpadding="3" cellspacing="0" width="100%" bordercolorlight="#800000" bordercolordark="#FFFFFF" bordercolor="#FFFFFF">
		<tr>
			<td bgColor="#ffffcc" width="60" style="font-size: 10pt">訊息類別</td>
			<td bgColor="#CCFFCC" style="font-size: 10pt">
			<input value="0" type="radio" name="data_kind" onclick="chkpublic();">公開 
			<input value="1" CHECKED type="radio" name="data_kind" onclick="chkprivate();">私人 
			<?php
			 if ($POST_KIND<6) {
			 ?>
			<input value="3" type="radio" name="data_kind" onclick="chkhorse();" ><font color=#FF0000>首頁「報馬仔」</font> 
			<input value="4" type="radio" name="data_kind" onclick="chkpic();" ><font color=#FF0000>看板(圖)</font> 
			<?php
		   } // end if
			?>
			</td>
		</tr>
		<tr id="Myprivate" style="display:block">
			<td bgColor="#ffffcc" width="60" style="font-size: 10pt">接收對象</td>
			<td bgColor="#CCFFCC" style="font-size: 10pt">
			<input tabIndex="1" type="text" name="m_to" value="<?php echo $m_to;?>">
			
			<input type="button" style="font-size:10px" value="全校帳號" onclick="javascript:window.open('teachers_id.php?form_name=form1&item_name=m_to&selected_text=document.form1.m_to.value','test','toolbar=no,left=0,top=0,screenX=0,screenY=0,height=400,width=740,resizable=1,scrollbars')" title="挑選帳號">
			或
			<img style="cursor:pointer" border="1" width="16" height="16" src="./images/online.jpg" onclick="window.location='msg_online.php'" title="由上線列表選取">
			</td>
		</tr>
		<tr id="Mypublic" style="display:block">
			<td bgColor="#ffffcc" width="60" style="font-size: 10pt">
			展示期限</td>
			<td bgColor="#CCFFCC" style="font-size: 10pt">
			<input value="3" type="radio" name="lasttime">3天 
			<input value="5" type="radio" name="lasttime">5天 
			<input value="7" type="radio" name="lasttime" CHECKED>7天
			<input value="10" type="radio" name="lasttime">10天 
			<input value="14" type="radio" name="lasttime">14天 
			<input value="30" type="radio" name="lasttime">30天</td> 
		</tr>
		<tr>
			<td bgColor="#ffffcc" width="60" style="font-size: 10pt">
			訊息內容(限200字以內)</td>
			<td bgColor="#ccffcc" style="font-size: 10pt">
			<!--webbot bot="Validation" b-value-required="TRUE" i-maximum-length="200" --><textarea tabIndex="2" rows="6" cols="28" name="msg"></textarea> 
			</td>
			
		</tr>

		<tr id="Myfile" style="display:block">
			<td style="font-size: 10pt" bgColor="#ffffcc">
			附加檔案</td>
		
			<td bgColor="#ccffcc" style="font-size: 10pt">
				<table border="0" width="100%">
					<tr>
						<td><input type="file" class="multi" name="thefile[]"></td>
						<td align="left"><input type="button" value="加入此檔" name="B1"></td>
					</tr>
				</table>		
			</td>
		</tr>
  	<tr id="M_private" style="display:block">
			<td style="font-size: 10pt" bgColor="#ffffcc">說明</td>
			<td bgColor="#ccffcc" style="font-size: 10pt;color:#0000FF">
				1.您可以傳送私人訊息給校內的教職同仁，當同仁登入校園MSN即可接收到訊息。<br>
				2.發送的訊息可以夾帶檔案，但不能僅夾帶檔案卻不填寫訊息內容，所以建議在訊息內容中填入檔案的說明。
			</td>
		</tr>
  	<tr id="M_public" style="display:none">
			<td style="font-size: 10pt" bgColor="#ffffcc">說明</td>
			<td bgColor="#ccffcc" style="font-size: 10pt;color:#FF0000">
				1.此公開訊息會在【校園MSN】的「校內訊息交流」欄位中，以捲動畫面的方式呈現。<br>
				2.只要是使用校內的電腦都能看見此訊息，但如果是校外的電腦，則必須登入才能看見這個訊息。<br>
				3.如果是要分享檔案, 如會議資料等, 請利用『分享檔案』的功能。
		</tr>
  	<tr id="M_horse" style="display:none">
			<td style="font-size: 10pt" bgColor="#ffffcc">說明</td>
			<td bgColor="#ccffcc" style="font-size: 10pt;color:#0000FF">
				這個訊息將會在學校首頁的「豐南報馬仔」出現，所有校外人士都看得見喔！<br>
			</td>
		</tr>
		
  	<tr id="M_pic_sttime" style="display:none">
			<td style="font-size: 10pt" bgColor="#ffffcc">開始時間</td>
			<td bgColor="#ccffcc" style="font-size: 10pt;color:#0000FF"><input type="text" id="stdate" name="stdate" value=""></td>
					<script type="text/javascript">
					new Calendar({
  		    	inputField: "stdate",
   		    	dateFormat: "%Y-%m-%d",
    	    	trigger: "service_date",
    	    	bottomBar: true,
    	    	weekNumbers: false,
    	    	showTime: 24,
    	    	onSelect: function() {this.hide();}
		    	});
					</script>
		</tr>
  	<tr id="M_pic_endtime" style="display:none">
			<td style="font-size: 10pt" bgColor="#ffffcc">結束時間</td>
			<td bgColor="#ccffcc" style="font-size: 10pt;color:#0000FF"><input type="text" id="enddate" name="enddate" value=""></td>
					<script type="text/javascript">
					new Calendar({
  		    	inputField: "enddate",
   		    	dateFormat: "%Y-%m-%d",
    	    	trigger: "service_date",
    	    	bottomBar: true,
    	    	weekNumbers: false,
    	    	showTime: 24,
    	    	onSelect: function() {this.hide();}
		    	});
					</script>		</tr>
  	<tr id="M_pic_delay" style="display:none">
			<td style="font-size: 10pt" bgColor="#ffffcc">延遲秒數</td>
			<td bgColor="#ccffcc" style="font-size: 10pt;color:#0000FF"><input type="text" name="delay_sec" value="5"></td>
		</tr>
		<tr id="M_pic_file" style="display:none">
			<td style="font-size: 10pt" bgColor="#ffffcc">
			附加檔案
		  </td>
			<td>
			 <input type="file" name="pic_file">
			</td>
		</tr>
		
  	<tr id="M_pic" style="display:none">
			<td style="font-size: 10pt" bgColor="#ffffcc">說明</td>
			<td bgColor="#ccffcc" style="font-size: 10pt;color:#0000FF">
				1.文字說明, 請盡量簡短<br>
				2.只能上傳 jpg/png/gif/swf 四種圖片
			</td>
		</tr>
  </table>
  
  <table border="0" width="100%" bgcolor="#FFFFFF">
    <tr> 
     <td colspan="2" align="right">
       <input type="button" onclick="b_submit()" value="送出" name="B1">&nbsp;<input type="button" value="關閉" name="B2" onclick="window.close()">
      </td>
    </tr>
  </table>
 </form>
 <table border="0" width="100%">
  <tr id="info" style="display:block">
  	<td style="color:#FF0000">
  		注意! 訊息僅保留30天(包括訊息中夾帶的檔案)，發送訊息後請通知對方要記得收訊息。
  	</td>
  </tr>
  <tr id="wait" style="display:none">
    <td><br>資料處理中, 請稍候...</td>
  </tr>
 </table>

  </center>
</div>

</body>
</html>
<script language="javascript">
document.form1.m_to.focus();
chkprivate();

function chkprivate() {
  Myprivate.style.display="block";
  Myfile.style.display="block";
  Mypublic.style.display="none";
  M_public.style.display="none";
  M_private.style.display="block";
  M_horse.style.display="none";
 info.style.display="block";
 wait.style.display="none";

  M_pic_sttime.style.display="none";
  M_pic_endtime.style.display="none";
  M_pic_delay.style.display="none";
  M_pic_file.style.display="none";
  M_pic.style.display="none";


 document.form1.option1.value="0";
}


function chkpublic() {
  Mypublic.style.display="block";
  Myprivate.style.display="none";
  Myfile.style.display="none";
  M_public.style.display="block";
  M_private.style.display="none";
  M_horse.style.display="none";
  
  M_pic_sttime.style.display="none";
  M_pic_endtime.style.display="none";
  M_pic_delay.style.display="none";
  M_pic_file.style.display="none";
  M_pic.style.display="none";

  
  document.form1.option1.value="1";
}


function chkhorse() {
  Mypublic.style.display="block";
  Myprivate.style.display="none";
  Myfile.style.display="none";
  M_public.style.display="none";
  M_private.style.display="none";
  M_horse.style.display="block";

  M_pic_sttime.style.display="none";
  M_pic_endtime.style.display="none";
  M_pic_delay.style.display="none";
  M_pic_file.style.display="none";
  M_pic.style.display="none";
  
  document.form1.option1.value="3";
}

function chkpic() {
  Mypublic.style.display="none";
  Myprivate.style.display="none";
  Myfile.style.display="none";
  M_public.style.display="none";
  M_private.style.display="none";
  M_horse.style.display="none";
  
  M_pic_sttime.style.display="block";
  M_pic_endtime.style.display="block";
  M_pic_delay.style.display="block";
  M_pic_file.style.display="block";
  M_pic.style.display="block";
   document.form1.option1.value="4";
}


function b_submit() {
 info.style.display="none";
 wait.style.display="block";
 document.form1.submit();
}

</script>
<?php
} // end if act=="" 
?>  