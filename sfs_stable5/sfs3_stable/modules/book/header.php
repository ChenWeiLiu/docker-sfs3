<?php

// $Id: header.php 8732 2016-01-05 07:01:17Z hsiao $


$book_path= updir($_SERVER[PHP_SELF]);
if( $_POST[sortq] !=""){
	header("Location: $book_path/".$_POST[sortq]);
}
else if($_POST[sortq2] !=""){
	header("Location: $book_path/".$_POST[sortq2]);
}
else if($_POST[qbook] !=""){
header("Location: $book_path/".$_POST[qbook]);
}
else if($_POST[bookadm] !=""){
	if ($_POST[bookadm]=="check")
		header("Location: $SFS_PATH_HTML"."include/sfs_case_studauth.php?chkpath=$_SERVER[SCRIPT_FILENAME]");
	else
		header("Location: $book_path/".$_POST[bookadm]);
}

head("圖書管理");
?>
<center>

<table bgColor="#619360" border="0" cellPadding="2" cellSpacing="0" width="608">
  <tbody>
    <tr>
      <td vAlign="center" width="10">　</td>
      <td vAlign="center" width="70%"><font color="#ffffff" size="4"><b><a style="text-decoration: none"><?php echo $school_sshort_name?>圖書室</a></b></font>
      </td>
      <td align="right" vAlign="center" nowrap><font color="#ffffc1" size=2><a href="qbook.php" target="_top">整頁瀏覽</a>&nbsp;
        </font></td>
    </tr>
  </tbody>
</table>
<FORM NAME="myform" action="<?php echo $_SERVER[PHP_SELF] ?>" method="post">

<table border="0" cellspacing="2" cellpadding="0" width="610">

  <tr bgcolor="green">

    <td align="center"><table border="0" cellspacing="1" cellpadding="3" width="100%">

      <tr bgcolor="#FEFBC0">

<td align="CENTER">
<SELECT NAME="qbook" SIZE=1 style="BACKGROUND-COLOR: #FEFBC0; font-family: 新細明體; font-size: 12pt" onchange="document.myform.submit()">
<OPTION SELECTED VALUE="">--- 圖書查詢 ---
<OPTION VALUE="qbook.php">圖書目錄

<OPTION VALUE="qbooktol.php">書目統計
<OPTION VALUE="qbookstud.php">讀者借閱查詢
<OPTION VALUE="qbooktea.php">教師借閱查詢
</SELECT>
</td>
<td align="CENTER">
<SELECT NAME="sortq" SIZE=1 style="BACKGROUND-COLOR: #FEFBC0; font-family: 新細明體; font-size: 12pt" onchange="document.myform.submit()">
<OPTION SELECTED VALUE="">--- 排行榜 ---
<OPTION VALUE="booksort.php">熱門書藉排行

<OPTION VALUE="classsort.php">班級借閱排行
<OPTION VALUE="studsort.php">讀者借閱排行
</SELECT>
</td>
<td align="CENTER">
<SELECT NAME="bookadm" SIZE=1 style="BACKGROUND-COLOR: #FEFBC0; font-family: 新細明體; font-size: 12pt" onchange="document.myform.submit()">
<OPTION SELECTED VALUE="">--- 圖書管理 ---
<OPTION VALUE="yetreturn.php">逾期查詢
<OPTION VALUE="qbookout.php">學生借閱狀況表
<OPTION VALUE="bookcode.php">條碼列印
<OPTION VALUE="bookcode_new.php">條碼列印(無分類號)
<OPTION VALUE="bro_book.php">學生借還書作業*
<OPTION VALUE="bro_tea_book.php">教師借還書作業*
<OPTION VALUE="class_code.php">班級條碼列印*
<OPTION VALUE="add_book.php">批次新增圖書*
<OPTION VALUE="book_new.php">圖書新增作業*
<OPTION VALUE="book_input.php">圖書修改作業*
<OPTION VALUE="qbookout_tea.php">教師借閱狀況表*
<OPTION VALUE="book_dump.php">圖書匯出作業*
<OPTION VALUE="check">授權學生管理*
</SELECT>
</td>

<td align="CENTER">
<SELECT NAME="sortq2" SIZE=1 style="BACKGROUND-COLOR: #FEFBC0; font-family: 新細明體; font-size: 12pt" onchange="document.myform.submit()">
<OPTION SELECTED VALUE="">--- 圖書室介紹 ---
<?php echo get_booksay_option() ?>
<OPTION VALUE="booksay_edit.php">選項編修(管理員)*
</SELECT>
</td>
      </tr>

    </table>

    </td>

  </tr>
</table>

</center><div align="center"><center>
  <table  class=module_body border="0" cellPadding="0" cellSpacing="0" width="608">
</FORM>
    <tbody>
      <tr>
        <td>
