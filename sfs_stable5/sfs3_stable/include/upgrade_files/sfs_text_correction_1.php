<?php

//$Id:  $
if(!$CONN){
        echo "go away !!";
        exit;
}
$query = "update sfs_text set t_name='本人身障' where t_name='本人殘障';";
$CONN->Execute($query);
$query = "update sfs_text set t_name='家長身障' where t_name='家長殘障';";
$CONN->Execute($query);
?>
