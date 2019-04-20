<?php 
// $Id: ustep3.php 5310 2009-01-10 07:57:56Z hami $
	if ( !@mysql_connect ("$session_mysql_host","$session_mysql_user","$session_mysql_password")) {
		echo "未受認證進入!!";
		exit;
	}
	
	if ($step!= 3 )
		$dis = " disabled ";
	else
		$dis ="";
	echo "<form method=\"POST\" $dis >";	
?>
<a name="this_step3">
<!- 第三步更改處室資料及學籍資料轉檔 --->	
        <table cellspacing="0" cellpadding="0" width=450>
          <tr bgColor="#999999">
            <td align="right"><b>第三步：</b></td>
            <td><b>系統資料、教師資料轉檔</b></td>
          </tr>
          
            <td bgColor="#999999" colSpan="2" align="right"></td>
          </tr>
          
            <td bgColor="#999999" colSpan="2" align="right"></td>
          </tr>
          <tr  bgColor="#cccccc">
            <td align="right"> </td>
            <td></td>
          </tr> 
          
        
          <tr  bgColor="#cccccc">
            <td align="right"> </td>
            <td></td>
          </tr>
          <tr  bgColor="#cccccc">
          	<td  colspan=2>          	
		<input type="hidden" value="3" name="dostep" >
		
          	<p><span style="background-color: #FFFF00"><input type="submit" value="確定" name="B1" onclick="blankWin()" <?php echo $dis ?>> &nbsp;&nbsp; ● 轉檔時間視資料筆數多寡而有不同，可能需數分鐘 ●</span></p>

          	 </td>
          </tr>
        </table>
  	
	</form>
<hr>