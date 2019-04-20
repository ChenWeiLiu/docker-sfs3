<?php
                                                                                                                             
// $Id: booksay.php 8723 2016-01-02 06:00:38Z qfon $

include "book_config.php";
include "header.php";
$sel = intval($_REQUEST['sel']);
$query = "select bs_con from book_say where bs_id=$sel";
$res=$CONN->Execute($query);
echo $res->fields[0];
include "footer.php";
?>
