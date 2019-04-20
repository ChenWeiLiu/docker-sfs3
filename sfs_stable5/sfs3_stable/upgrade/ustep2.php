<?php 
// $Id: ustep2.php 5310 2009-01-10 07:57:56Z hami $
	if ( !@mysql_connect ("$session_mysql_host","$session_mysql_user","$session_mysql_password")) {
		echo "未受認證進入!!";
		exit;
	}
	
	if ($step!= 2 )
		$dis = " disabled ";
	else
		$dis ="";
	echo "<form method=\"POST\" $dis >";	
?>
<a name="this_step2">
<!- 第二步更改處室資料及學籍資料轉檔 --->	
        <table cellspacing="0" cellpadding="0" width=450>
          <tr bgColor="#999999">
            <td align="right"><b>第二步：</b></td>
            <td><b>學生資料轉檔</b></td>
          </tr>
          
            <td bgColor="#999999" colSpan="2" align="right"></td>
          </tr>
          <tr  bgColor="#cccccc">
            <td align="right">住址轉入：</td>
            <td>預設縣市：<input type="text" name="default_sheng" size="10" value="台中縣" <?php echo $dis ?>>&nbsp;&nbsp; 
              預設鄉鎮：<input type="text" name="default_coun" size="10" value="外埔鄉" <?php echo $dis ?>></td>
          </tr>
          <tr>
            <td bgColor="#999999" colSpan="2" align="right"></td>
          </tr>
          <tr  bgColor="#cccccc">
            <td align="right"> </td>
            <td></td>
          </tr> 
          <tr  bgColor="#cccccc">
            <td  colspan=2>將建立sfs2 資料庫，並將原sfs1.1 資料複製至新資料庫</td>
            
          </tr> 
          <tr>
	<td colspan=2>	
	<?php 
		/*if (check_db("$Mysql_db") and $step==2) {
			echo "●●● sfs2 資料庫已存在，<font color=red>sfs2 資料庫將被清除再重建立!!</font><br>";
		}			
		*/
          ?>
          </td></tr>
          <tr  bgColor="#cccccc">
            <td align="right"> </td>
            <td></td>
          </tr>
          <tr  bgColor="#cccccc">
          	<td  colspan=2>          	
		<input type="hidden" value="2" name="dostep" >
		
          	<p><span style="background-color: #FFFF00"><input type="submit" value="確定" name="B1" onclick="blankWin()" <?php echo $dis ?>> &nbsp;&nbsp; ● 轉檔時間視資料筆數多寡而有不同，可能需數分鐘 ●</span></p>

          	 </td>
          </tr>
        </table>
  	
	</form>
<hr>