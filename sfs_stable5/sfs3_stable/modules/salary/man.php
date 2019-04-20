<?php
//$Id:$

include_once "config.php";
sfs_check();
//秀出網頁
head("薪津查詢管理");

if (! $is_admin) {
	echo "<h2 style='margin:20px auto'>非管理人員,權限不足!</h2>";
	foot();
	exit;
}

if ($_POST['act']) {
	setlocale(LC_ALL, 'zh_TW.BIG5');
	$fp = fopen($_FILES['upload_csv']['tmp_name'],"r");
	$rows = 0;
	while(($ar = sfs_fgetcsv($fp, 1000, ","))!=false) {
	  if ($rows!=0) {
	    foreach($ar as $k=>$v) {
	       $d['data'][$rows][$d['field'][$k]] = $v;
	    }
	  } else {
	    foreach($ar as $k=>$v) {
	      $d['field'][$k] = $v;
	    }
	  }
	  $rows++;
	}

	$message = array();
	$fields = "`".implode("`,`",$d['field'])."`";
	foreach ($d['data'] as $val) {
		// 刪除之前匯入
		$query =  "DELETE FROM salary WHERE InType='{$val['InType']}' AND ID='{$val['ID']}'";
		$CONN->Execute($query) or die($query);
		$query = "INSERT INTO `salary` ( $fields ) VALUES ";
		$query .= "('".implode("','",array_values($val))."')";
		$CONN->Execute($query) or die($query);
		$message[] = $val['Name'];
	}



}

//橫向選單標籤
print_menu($menu_p);
//InType	ID	Name	DutyType	JobType	JobTitle	MaxPoint	MaxExtPoint	Point	Thirty	ClassTMFactor	Insurance1Factor	Insurance2Factor	Insurance3Factor	InsureAmount	InsuranceLevel	Family	Memo	BankName1	AccountID1	BankName2	AccountID2	Mg1	Mg2	Mg3	Mg4	Mg5	Mg6	Mg7	Mg8	Mg9	Mh1	Mh2	Mh3	Mh4	Mh5	Mh6	Mh7	Mh8	Mh9	Mi1	Mi2	Mi3	Mi4	Mi5	Mi6	Mi7	Mi8	Mi9
?>
<style>
<!--
#upload-area {border:thin #ccc;background:#ccc;padding:1px}
#result {background:#ff6}
-->
</style>
<div id="upload-area">
<form method="post" enctype="multipart/form-data" id="myform" action="">
<div style="background:#fff;padding:5px;">
<label for="upload_csv">上傳薪資csv 檔案</label>
<input type="file" id="upload_csv" name="upload_csv" size="20" />
<input type="submit" value="上傳" name="act" />
</div>
</form>
<?php if ($message):?>
<div id="result">
 	<p>本次匯入成功共 <?php echo count($message) ?> 筆,如下名單:</p>
 	<p><?php echo implode(",",$message)?>
 </div>
 <?php endif ?>
</div>
<div id="memo">
<h3>說明</h3>
<ul>
<li>本模組配合簡易出納系統(<a href="http://163.17.154.130/eschool/esoft.htm">http://163.17.154.130/eschool/esoft.htm</a>) ，產生入帳的CSV檔,匯入後,提供教師薪資查詢用.<li>
<li>關於簡易出納系統匯出,請參考上述網站說明</li>
</ul>
</div>

