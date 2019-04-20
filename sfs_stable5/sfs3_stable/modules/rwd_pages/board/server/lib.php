<?php

abstract class Chinese {

  public function big52utf8($str){
    $str = mb_convert_encoding($str, "UTF-8", "BIG5");
    $i=1;
    while ($i != 0){
    //print $str;
    $pattern = '/&#\d+\;/';
    preg_match($pattern, $str, $matches);
    $i = sizeof($matches);
      if ($i !=0){
        $unicode_char = mb_convert_encoding($matches[0], 'UTF-8', 'HTML-ENTITIES');
        $str = preg_replace("/$matches[0]/",$unicode_char,$str);
      } //end if
    } //end wile
    return $str;
  }

}

class sfs3 extends Chinese {
	protected $dbname ;
	protected $username ;
	protected $password ;

  public function big52utf8($str){
    //$str = mb_convert_encoding($str, "UTF-8", "BIG5");
    $str = parent::big52utf8($str);;
		//$str = $this->strRevise($str);
    return $str;
  }

  public function json($data){
    if (is_array($data)){
      $json = json_encode($data);
      return $json;
    }
  }

/*
  public function strRevise($str){
		$patterns = array();
		$patterns[0] = "/\\\/";
		$patterns[1] = "/\s+/";
		$replacements = array();
		$replacements[0] = '\u005c';
		$replacements[1] = '\u0020';

		return preg_replace($patterns, $replacements, $str);

  }
*/

	public function getConfig($configFile){
		$handle = @fopen($configFile, "r");
		if ($handle) {
			while (($buffer = fgets($handle, 4096)) !== false) {
					//$pattern = Array('$mysql_user','$mysql_pass', '$mysql_db');
					$arr = explode("=",$buffer);
					switch (trim($arr[0])) {
						case '$mysql_user':
							$this->mysql_user = str_replace('"',"",$arr[1]);
							$this->mysql_user = trim(str_replace(';',"",$this->mysql_user));
						break;

						case '$mysql_pass':
							$this->mysql_pass = str_replace('"',"",$arr[1]);
							$this->mysql_pass = trim(str_replace(';',"",$this->mysql_pass));
						break;

						case '$mysql_db':
							$this->mysql_db = str_replace('"',"",$arr[1]);
							$this->mysql_db = trim(str_replace(';',"",$this->mysql_db));
						break;
					}
			}
			if (!feof($handle)) {
					echo "Error: unexpected fgets() fail\n";
			}
			fclose($handle);
		}
	}
		

}
