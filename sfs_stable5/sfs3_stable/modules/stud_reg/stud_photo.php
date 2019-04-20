<?php
//$Id: chi_photo.php 7563 2013-09-22 09:00:32Z smallduh $
header('Content-type: text/html;charset=big5');

require_once("stud_reg_config.php");

//使用者認證
sfs_check();

$targetFolder = '/sfs3/data/photo/student/101'; // 設定要上傳檔案的資料夾

if (!empty($_FILES)) {
	  $the_class=$_POST['the_class'];
	  $name_mode=$_POST['name_mode'];
    $tempFile = $_FILES['Filedata']['tmp_name'];
    //$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder; // 設定要上傳檔案的資料夾絕對路徑
		$targetPath=$UPLOAD_PATH.'photo/student/';
    $targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];
    // Validate the file type
    $fileTypes = array('jpg','jpeg','png'); // 可以限制檔案副檔名
    $fileParts = pathinfo($_FILES['Filedata']['name']);
    if (in_array($fileParts['extension'],$fileTypes)) {
        move_uploaded_file($tempFile,$targetFile);
        $F=$_FILES['Filedata']['name'];
        $nf=explode('.',$F);
        $key_word=$nf[0];
        if ($name_mode==1) {
        	$sql="select stud_id,stud_study_year from stud_base where stud_id='$key_word' and curr_class_num like '".$the_class."%' and  (stud_study_cond=0 or stud_study_cond=15)";
			  } elseif ($name_mode==2) {
			    $key_word=substr($the_class,0,1).$key_word;
			    $sql="select stud_id,stud_study_year from stud_base where curr_class_num='$key_word' and curr_class_num like '".$the_class."%' and (stud_study_cond=0 or stud_study_cond=15)";
			  } else {
			    exit();
			  }
				$res=$CONN->Execute($sql);
				if ($res->recordcount()==1) {
					$stud_id=$res->fields['stud_id'];
					$year=$res->fields['stud_study_year'];
        	$new_file=rtrim($targetPath,'/') . '/' .$year.'/'.$stud_id;
        	rename($targetFile,$new_file);
        	echo "success-".$stud_id;;
				} else {
				  unlink($targetFile);
				  echo 'No this student : '.$key_word;
				}
        //echo $targetFolder . '/' . $_FILES['Filedata']['name'];
    } else {
        echo 'The filetype must be jpg or png .';
    }
 
 exit();
} 


//如果是經由標籤頁過來
if ($_POST['c_curr_seme']) {
 $c_curr_seme=$_POST['c_curr_seme'];
 //099_1_07_01
 $c_curr_class=sprintf("%03d_%d_%02d_%02d",substr($c_curr_seme,0,3),substr($c_curr_seme,-1),substr($_POST['the_class'],0,1),substr($_POST['the_class'],1,2));
 $name_mode=$_POST['name_mode'];
} else {
 $c_curr_class=$_GET['c_curr_class'];
 $c_curr_seme=$_GET['c_curr_seme'];
} 

if ($name_mode==0) $name_mode=1;

$linkstr = 'c_curr_class='.$c_curr_class.'&c_curr_seme='.$c_curr_seme;

//取得所有學期資料, 每年有兩個學期
$class_seme_p = get_class_seme(); //學年度
	
//抓取班級設定裡的班級名稱
$class_base= class_base($c_curr_seme);
$the_class=substr($c_curr_class,7,1).substr($c_curr_class,9,2);

head("學生相片管理2");
print_menu($menu_p,$linkstr);
?>
<script type="text/javascript" src="include/jquery.uploadify.min.js"></script>
<link rel="stylesheet" type="text/css" href="include/uploadify.css">
<form method="post" name="myform" action="<?php echo $_SERVER['php_self'];?>">
	<select name="c_curr_seme" size="1" onchange="document.myform.submit()">
		<?php
		foreach ($class_seme_p as $k=>$v) {
		?>
			<option value="<?php echo $k;?>"<?php if ($k==$c_curr_seme) echo " selected";?>><?php echo $v;?></option>
		<?php
		}
		?>
	</select>
	<select name="the_class" size="1" onchange="document.myform.submit()">
		<?php
		foreach ($class_base as $k=>$v) {
		?>
			<option value="<?php echo $k;?>"<?php if ($k==$the_class) echo " selected";?>><?php echo $v;?></option>
		<?php
		}
		?>
	</select>
	&nbsp;&nbsp;上傳檔名格式：<input type="radio" name="name_mode" value="1"<?php if ($name_mode==1) echo " checked";?> onclick="document.myform.submit()">學號 <input type="radio" name="name_mode" value="2"<?php if ($name_mode==2) echo " checked";?> onclick="document.myform.submit()">班級座號(4碼)
	<input name="file_upload" id="file_upload" type="file">
	<?php
	if ($c_curr_seme and $the_class) {
	 $sql="select a.student_sn,a.stud_id,a.seme_num,b.stud_sex,b.stud_name,b.stud_study_year from stud_seme a,stud_base b where a.student_sn=b.student_sn and a.seme_class='$the_class' and a.seme_year_seme='$c_curr_seme' order by seme_num";
	 $res=$CONN->Execute($sql) or die("讀取學生資料發生錯誤 SQL=".$sql);
	 $i=0;
	 $base=$UPLOAD_PATH.'photo/student/';
	 $base_url=$UPLOAD_URL.'photo/student/';
	 ?>
	 <table  border=1 cellspacing='0' cellpadding='0' style="border-collapse: collapse;border-color:#8CCCCA;background-color:#8CCCCA " >
	 <?php
	  while ($row=$res->fetchRow()) {
	  	$i++;
	  	$year=$row['stud_study_year'];
			$path=$base.$year;
			if ($base==$path) die("學生無入學年請檢查！");
			if (!file_exists($path)) @mkdir($path,0755);
			
			//img 的 src 判斷
			$new_file=$path."/".$row['stud_id'];
			if (is_file($new_file)) {
				$FIG=$base_url.$year."/".$row['stud_id'];
			} else {
			  $FIG="./images/nopic.png";
			}
			
			//id指定
			$ID="p_".$row['stud_id'];

	  	if ($i%5==1) echo "<tr>";
	  	$font_color=($row['stud_sex']==1)?"#0000DD":"#DD0000";
	  	
	  	?>
	  	<td style="color:<?php echo $font_color;?>;font-size:10pt" align="center" valign="top" height="180" width="125">
	  	  <?php echo $row['seme_num'].".".$row['stud_name'];?><br>
	  	  <?php echo $row['stud_id'];?><br>
	  	  <img src="<?php echo $FIG;?>" id="<?php echo $ID;?>" width="120" border="0">	  		
	  	</td>	  
	    <?php
	   	if ($i%5==0) echo "</tr>";
	  } // end while
	 ?>
	 
	 </table>
	 <?php
	}
	?>

</form>
<?php
foot();
?>
<script type="text/javascript">
	var BASE_URL='<?php echo $base_url.$year."/";?>';
  $(function() {
      $('#file_upload').uploadify({
            'swf'      : 'include/uploadify.swf',
            'uploader' : 'stud_photo.php',
            'formData' : { 'the_class' : '<?php echo $the_class;?>','name_mode':'<?php echo $name_mode;?>' } ,
            'onUploadSuccess' : function(file,the_data,response) {
            	var N=the_data;
            	if (N.substr(1,7)=='success') {
            	 
            	 var NewArray = new Array();
　						 var NewArray = N.split("-");
							 var stud='p_'+NewArray[1];
							 var new_pic=BASE_URL+NewArray[1];
               chfig(stud,new_pic);
              } else {
               alert('發生錯誤：'+the_data);
              }
        		}
        });
  });
	
	function chfig(stud,new_pic) {
		document.getElementById(stud).src=new_pic; 
	}
	
</Script>
