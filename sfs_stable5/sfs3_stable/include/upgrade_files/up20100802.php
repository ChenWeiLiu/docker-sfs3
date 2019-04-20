<?php

//$Id: up20090921.php 5649 2009-09-20 17:08:17Z brucelyc $

if(!$CONN){
        echo "go away !!";
        exit;
}
$query ="update sfs_text  set  t_name='ºq°Û' where  t_name='°Ûºq' and  t_kind='¯S®í¤~¯à'";
mysql_query($query);
?>
