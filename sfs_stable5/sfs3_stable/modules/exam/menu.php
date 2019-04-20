<!-- $Id: menu.php 5310 2009-01-10 07:57:56Z hami $ -->
<table border="0" cellPadding="0" cellSpacing="2" width="302" align=center >
  <tbody>
    <tr bgColor="#6666cc">
      <td align="middle">
        <table border="0" cellPadding="3" cellSpacing="1" width="400">
          <tbody>
            <tr bgColor="#fefbc0">
            <?php 
            	//系統管理人員
            	if ($man_flag) {	
              		echo "<td align=\"middle\" width=\"70\"><a href=\"ekind.php\">班級管理</a></td>";
              		echo "<td align=\"middle\" width=\"70\"><a href=\"esystem.php\">系統管理</a></td>";
              	}
             ?>
              <td align="middle" width="70"><a href="exam.php">作業管理</a></td>              
              <td align="middle" width="70"><a href="exam_list.php">作業展示</a></td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>
