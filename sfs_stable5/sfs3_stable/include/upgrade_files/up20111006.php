<?php

//$Id:$

if(!$CONN){
        echo "go away !!";
        exit;
}
	//增加平時成績項目參照
	$SQL="ALTER TABLE `score_ss` ADD `nor_item_kind` VARCHAR( 20 ) NULL;";
	$rs=$CONN->Execute($SQL);

		$SQL="ALTER TABLE `sfs_text` CHANGE `t_name` `t_name` VARCHAR( 100 ) NULL;";
	$rs=$CONN->Execute($SQL);

		$SQL="INSERT INTO sfs_text(`t_order_id`, `t_kind`, `g_id`, `d_id`, `t_name`, `t_parent`, `p_id`, `p_dot`) VALUES ( 0, '平時成績選項', 5, '0', '平時成績選項', '', 0, '');";
	$rs=$CONN->Execute($SQL);

?>