<?php


class SFS3Server {

	function __construct() {
	}

	function getConfig($configFile,$pattern){
		$handle = @fopen($configFile, "r");
		if ($handle) {
			while (($buffer = fgets($handle, 4096)) !== false) {
				$arr = explode("=",$buffer);
			  if (trim($arr[0]) == $pattern){
					$value = str_replace('"',"",$arr[1]);
					return trim(str_replace(';',"",$value));
				}
			}
		}
	}


	function json($data){
    if (is_array($data)){
      $json = json_encode($data);
      return $json;
    }
  }


}

/*
$obj = new SFS3Config();

$configFile = "../../../../include/config.php";
#print $SFS_PATH_HTML = $obj->getConfig($configFile,'$SFS_PATH_HTML');
print $UPLOAD_URL = $obj->getConfig($configFile,'$UPLOAD_URL');
$HOME_URL = $obj->getConfig($configFile,'$HOME_URL');

*/
