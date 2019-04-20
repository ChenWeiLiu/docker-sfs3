<?php

//$Id:$

if(!$CONN){
        echo "go away !!";
        exit;
}
$SQL="UPDATE stud_addr_zip SET town='北屯區' WHERE zip='406'";
$rs=$CONN->Execute($SQL);
$SQL="UPDATE stud_addr_zip SET town='西屯區' WHERE zip='407'";
$rs=$CONN->Execute($SQL);
$SQL="UPDATE stud_addr_zip SET town='南屯區' WHERE zip='408'";
$rs=$CONN->Execute($SQL);

?>