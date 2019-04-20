<?php

abstract class Chinese{

      public function big5_to_utf8($str){
      $str = mb_convert_encoding($str, "UTF-8", "BIG5");

      $i=1;

      while ($i != 0){
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


class Sfs3Data extends Chinese{

     public function array_big5_to_utf8(array $data){
	foreach($data as $key=>$value){
	  if (is_array($value)){
		$data[$key] = $this->array_big5_to_utf8($value);
	  }else{
		$value = $this->big5_to_utf8($value);
		$data[$key] = htmlspecialchars($value);
	  }
		
	}
	return $data;	
     }

}

/*
$data = [1,2,3=>[4,5,6=>[7,8,9=>10,11,12=>[13,14,15=>[16,17,18]]]]];
$obj = new Sfs3Data;
$res = $obj->array_big5_to_utf8($data);

print_r($data);
print_r($res);
*/
