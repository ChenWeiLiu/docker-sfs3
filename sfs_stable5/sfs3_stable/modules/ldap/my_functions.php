<?php
function get_ldap_setup() {
	
  global $CONN;  
   
  //若模組設定中無此模組設定, 表示未安裝模組
  if(chk_ldap_module()==0) {
    $row['enable']=0;
    $row['enable1']=0;
  } else {   
  	$query="select * from ldap limit 1";
  	$res=$CONN->Execute($query); // or die('Error! SQL='.$query);  
  	if (!$res) {
  		$row['enable']=0;
  	  $row['enable1']=0;
  	} else {
  		$row=$res->fetchrow();  
  	}
  }
  	return $row;
} // end function

//確認 ldap 模組是否安裝
function chk_ldap_module() {
  
  global $CONN;
  
  $query="select * from sfs_module where dirname='ldap' and islive='1'";
  $res=$CONN->Execute($query) or die('Error! SQL='.$query);;
  
  if ($res->RecordCount()>0) {
  	return true;
  } else {
    return false;
  }

} // end function

?>