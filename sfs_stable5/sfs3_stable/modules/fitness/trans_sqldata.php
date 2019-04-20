<?php
// $Id: index.php 5310 2009-01-10 07:57:56Z hami $
//心肺適能由分'秒"轉換成秒

if ($_POST['mode']=="trans") {
 
 $m_stnum=$_POST['m_stnum'];
 $m_no=$_POST['m_no'];
 $m_sex=$_POST['m_sex'];
 
 $buffer = explode("\n",$_POST['data']);
 
 $D=array();
 
 foreach ($buffer as $B) {
    $W=explode("\t",$B);
    $P=$W[0];
    //從 $W[1]~$W[9] 分別是 10歲至18歲的百分比常模數據
   for ($i=1;$i<10;$i++) {
     $age=9+$i;
     $D[$age][$P]=$W[$i];  //某年級某百分比的常模數據   
   } // end for
 } // end foreach
  
  
 for ($i=10;$i<=18;$i++) {
 	$c=$m_stnum+$i-10;
  $P_DATA="INSERT INTO fitness_mod VALUES (".$c.",".$m_no.",".$m_sex.",".$i;
  //把1~99的數據加上來
  for ($ii=1;$ii<100;$ii++) {
  	$P_DATA.=",".$D[$i][$ii];  
  }
  $P_DATA.=");";
  echo $P_DATA."<br>";
 } 
} // end if


?>
<table border="0">
	<tr>
		<td>常模更新轉成 sql 指令</td>
		</tr>
	<tr>
		<td>貼 CVS 檔</td>
		</tr>
	</table>
	<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
 <input type="hidden" name="mode" value="trans">
	<table border="0" width="100%">
			<tr>
			<td>常模前置起始編號<input type="text" size="5" name="m_stnum"></td>
		</tr>
		<tr>
			<td>常模編號(0身高,1體重,2坐姿體前彎,3仰臥起坐60秒,4立定跳遠,5心肺適能,6BMI) <input type="text" size="30" name="m_no"></td>
		</tr>
		<tr>
			<td>常模性別(1男生,2女生) <input type="text" size="30" name="m_sex"></td>
		</tr>
		<tr>
		 <td>
		 	<textarea cols="80" rows="20" name="data"></textarea>
		 	</td>	
		</tr>
		</table>

 
 
 <input type="submit" value="送出">
</form>
