<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z smallduh $

	//╰参砞﹚郎
	include_once "./module-cfg.php";
	include_once "../../include/config.php";

	//ネ睵徊旧も家舱ㄧΑ
  include_once "../12basic_career/my_fun.php";


	//家舱穝祘Α
	require_once "./module-upgrade.php";
	  

//更セ家舱盡ノㄧΑ畐
include_once ('my_functions.php');


//眔家舱把计摸砞﹚
$m_arr = &get_module_setup("career_leader");
extract($m_arr,EXTR_OVERWRITE);

$name_list_arr=explode(',',$name_list);
$name_list2_arr=explode(',',$name_list2);

if ($max_leader1=='') $max_leader1=8;	
if ($max_leader2=='') $max_leader2=8;	
	
?>


