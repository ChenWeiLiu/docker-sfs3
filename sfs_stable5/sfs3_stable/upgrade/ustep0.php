<!-- $Id: ustep0.php 5310 2009-01-10 07:57:56Z hami $ -->
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100%"><b> 請先建立資料庫 sfs2 ，按下列步驟：</b>  
      <ol> 
        <li> 備份原 sfs 資料庫<br>  
          <br> 
          <span style="background-color: #CCCCFF">mysqldump sfs > sfsdump.sql -uroot -p<br>  
          <br> 
          </span></li> 
        <li>建立 sfs2 資料庫&nbsp;<br> 
          <br>
          <span style="background-color: #CCCCFF">mysqladmin create sfs2 -uroot -p<br>  
          <br> 
          </span></li> 
        <li>將 sfs 的資料回存至 sfs2&nbsp;<br> 
          <br>
          <span style="background-color: #CCCCFF">mysql sfs2 &lt; sfsdump.sql -uroot -p</span></li> 	 
      </ol> 
      
    </td> 
  </tr> 
  <tr>
    <td width="100%">更新程式執行時間，因資料量及主機速度，而有不同，<br>
      若因更新時間過久，造成錯誤如下：<b><br>
      Fatal error</b>: Maximum execution time of 30 seconds exceeded
      <p>請修改 php.ini設定，將時間設長一些，如下</p>
      <p><span style="background-color: #CCCCFF">max_execution_time = <font color="#FF0000"><b>60</b></font></span></p>
      <p>修改後，重啟 apache <br>
      
    </td> 
  </tr> 
</table> 

<hr>