<?php
//$Id: tabbox.php 5310 2009-01-10 07:57:56Z hami $
echo '
<style type="text/css">
<!--
.menu_list_black { font-family: "新細明體", "細明體", "Arial"; font-size: 13px; line-height: 130%; color: #000000}
.menu_list_red { font-family: "新細明體", "細明體", "Arial"; font-size: 13px; line-height: 120%; color: #CC0000}
.menu_list_blue { font-family: "新細明體", "細明體", "Arial"; font-size: 13px; line-height: 120%; color: #6633FF}
-->
</style> ';

function OpenTable() {
    echo '<table border="0" cellspacing="0" cellpadding="0" width="150" >
          <tr>' ;
    echo '<td width="5" ><img src=p_u_l.gif border=0 width="5" height="4"  ></td>' ;
    
    echo '<td  bgcolor="#FFC77B"><img src="spacer.gif"  height="4"></td> ';
    echo '<td width="5"><img src=p_u_r.gif border="0" width="5" height="4" ></td>';
    
    echo '<tr><td bgcolor="#FFC77B" width="5"><img src="spacer.gif"  width="5" ></td>';
    echo '<td bgcolor="#FFC77B" width="140">';
}

function CloseTable() {
    echo '</td>
    <td bgcolor="#FFC77B" width="5"><img src="spacer.gif"  width="5" ></td></tr>' ;

    echo '<tr>
          <td width="5"><img src="p_d_l.gif" width="5" height="4"></td>
          <td  bgcolor="#FFC77B"><img src="spacer.gif"  height="4"></td>
          <td width="5"><img src="p_d_r.gif" width="5" height="4"></td>
          </tr></table>' ;
}

?>