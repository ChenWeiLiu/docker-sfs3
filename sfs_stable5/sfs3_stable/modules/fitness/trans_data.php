<?php
// $Id: index.php 5310 2009-01-10 07:57:56Z hami $
//心肺適能由分'秒"轉換成秒

if ($_POST['mode']=="trans") {
 
 $buffer = explode("\n",$_POST['data']);
 foreach ($buffer as $B) {
  $data=explode(",",$B);
  echo $data[0].",";
  for ($i=1;$i<count($data);$i++) {
   $D=explode("'",$data[$i]);
   $S=$D[0]*60+$D[1];
   echo $S;
   if ($i<count($data)-1) echo ",";
  }
  echo "<br>";
 }
} // end if


?>
<table border="0">
	<tr>
		<td>心肺適能由分'秒"轉換成秒,</td>
		</tr>
	<tr>
		<td>先把教育部原始資貼到excel, 再另存成 CVS, 再利用記事本開啟貼過來</td>
		</tr>
	</table>
<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
 <input type="hidden" name="mode" value="trans">
 <textarea cols="80" rows="20" name="data"></textarea>
 <input type="submit" value="送出">
</form>
