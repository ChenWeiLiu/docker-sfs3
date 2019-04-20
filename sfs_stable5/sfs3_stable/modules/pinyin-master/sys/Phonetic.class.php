<?php

interface INameFormat{
  static public function passport_format($last_name,$first_name=Array());
  static public function passport_format_no_hyphen($last_name,$first_name=Array());
  static public function passport_format_western($last_name,$first_name=Array());
  static public function common_format($last_name,$first_name=Array());
}


abstract class Chinese{

    static public function decimal_notation_converting($string){
      $i=1;
      while ($i != 0){
      //print $string;
      $pattern = '/&#\d+\;/';
      preg_match($pattern, $string, $matches);
      $i = sizeof($matches);
	if ($i !=0){
	  $unicode_char = mb_convert_encoding($matches[0], 'UTF-8', 'HTML-ENTITIES');
	  $string = preg_replace("/$matches[0]/",$unicode_char,$string);
	} //end if
      } //end wile
      return $string;
    }

    static public function mb_str_split($string) {
      mb_regex_encoding("UTF-8");
      $string = mb_ereg_replace("(\s+|\t+)","",$string);
      return preg_split('/(?<!^)(?!$)/u', $string ); //return array;
    }

}

class Phonetic extends Chinese {
    private $_pdo;   //連接資料庫 必要

    private $_users = Array(); //feed format -> users_name[$id]
    private $_pinyin_selected_values = Array();

  public function __construct(PDO $pdo,$users=Array()) {
    $this->_pdo = $pdo;
    $this->_users = $users;
	
  }

  public function getUsers(){
    return $this->_users ;
  }


  public function getPinyinSelectedValues(){
    return $this->_pinyin_selected_values ;
  }

	public function check_selected($pattern,$_post=Array()){
		if (!empty($_post[$pattern])) {
			return $_post[$pattern];
		}
	}


	//設定拼音方法, 預設為hy,$pinyin_selected_values是由$_POST帶回來的array
	public function set_pinyin_method($pinyin_selected_values,$default_pinyin){
		$this->_pinyin_selected_values = $pinyin_selected_values;
		foreach($this->_users as $id => $row){
			if (empty($this->_pinyin_selected_values[$id])){
				$this->_pinyin_selected_values[$id] = $default_pinyin;
			}
		}
		return $this->_pinyin_selected_values;
	}

  public function query($char_pos_weight,$query_string,$pinyin_method){
			$result = Array();
			$this->_pdo->beginTransaction();
				$query = "SELECT * from ph where chinese = '{$query_string}' ORDER BY $char_pos_weight DESC ";
				$stmt = $this->_pdo->prepare($query);
				$stmt->execute();
				$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$tmp = Array();
				foreach($data as $row => $col){
						//$tmp['sn'] = $col['sn']; 
						//$tmp['chinese'] = $col['chinese']; 
						//$tmp['ph'] = $col['ph']; 
						$tmp = $col ;
						$tmp['pinyin'] = $col[$pinyin_method];
						$result[] = $tmp;
				}

		$this->_pdo->commit();
		return $result;
  }
	
  public function update($post_ph_selected_values){
	    if(sizeof($post_ph_selected_values) != 0){ 
	    $this->_pdo->beginTransaction();
	    //print_r($post_ph_selected_values);
	    foreach($post_ph_selected_values as $id => $name){
		    foreach($name as $pos => $ph_sn){
			    if ($pos == 0){
				    $query = "UPDATE ph SET last_name_weight =
										    (select (last_name_weight)+1  from ph where sn = $ph_sn )
									    where sn = $ph_sn ;";
			    }else{
				    $query = "UPDATE ph SET first_name_weight =
									    (select (first_name_weight)+1  from ph where sn = $ph_sn )
									    where sn = $ph_sn ;";
			    }
			    $stmt = $this->_pdo->prepare($query);
			    $stmt->execute();
		    }
	    }
	    $this->_pdo->commit();
	}
 }

	public function set_multiph($users){
		foreach ($users as $id => $char_pos){
			$pinyin_method = $this->_pinyin_selected_values[$id];
			$name_length = sizeof($users[$id]);
			for($pos=0;$pos<$name_length;$pos++){
				$char = $users[$id][$pos];
				$char_pos_weight = ($pos == 0)?"last_name_weight" :"first_name_weight" ;
				$res=$this->query($char_pos_weight,$char,$pinyin_method);
				if (sizeof($res)>1){
					$users_name_multiph[$id][$pos] = $res;
					//$this->set_multiph($id,$pos,$res) ;
				}
			} //end for($pos=0;$pos<$name_length;$pos++)
		} // end foreach
		//print_r($this->_users_name_multiph);
		return  $users_name_multiph;
	} //end public funcion set_multiph


	public function set_char_to_pinyin($users){
		foreach ($users as $id => $char_pos){
			$pinyin_method = $this->_pinyin_selected_values[$id];
			$name_length = sizeof($users[$id]);
			for($pos=0;$pos<$name_length;$pos++){
				$char = $users[$id][$pos];
				$char_pos_weight = ($pos == 0)?"last_name_weight" :"first_name_weight" ;
				$res=$this->query($char_pos_weight,$char,$pinyin_method);
				foreach($res as $row => $data){
					$sn = $data['sn'];
					$pinyin = $data['pinyin'];
					$users_name_char_pinyin[$id][$pos][$sn] = $pinyin;
					//$this->_users_name_char_pinyin[$id][$pos][$sn] = $pinyin;
				}//end foreach
			} //end for
		} // end foreach
			return $users_name_char_pinyin;
	} //end public funcion set_char_to_pinyin()

  public function __desstruct() {
		$this->_pdo = null;
	}
	
} // end class Ph

class UsersNamePhonetic extends Phonetic implements INameFormat{

  public function user_name_eng($USERS,$post_ph_selected_values,$users_name_char_pinyin){

    $user_name_eng = Array();
    foreach($USERS as $id => $name){
      for($pos=0;$pos<sizeof($name);$pos++){

	if (isset($post_ph_selected_values[$id][$pos])){
	  $sn = $post_ph_selected_values[$id][$pos] ;
	}else{ $sn = 'null'; }

	if(sizeof($users_name_char_pinyin[$id][$pos])!=0){
	  if (array_key_exists($sn,$users_name_char_pinyin[$id][$pos])){
	    $user_name_eng[$id][$pos] = $users_name_char_pinyin[$id][$pos][$sn];
	  }else{
	    $tmp = Array();
	    $tmp['sn'] = array_keys($users_name_char_pinyin[$id][$pos]);
	    $tmp['pinyin'] = array_values($users_name_char_pinyin[$id][$pos]);
	    $user_name_eng[$id][$pos] = $tmp['pinyin'][0];

	  }//end if else
	  unset($tmp);
	}//end if(sizeof($users_name_char_pinyin[$id][$pos])!=0)
      } //end for
    }
    return $user_name_eng;

  }

    static public function passport_format($last_name,$first_name=Array()){
	    $last_name = strtoupper($last_name).',';
	    $first_name = strtoupper(implode("-", $first_name));
	    return $last_name.$first_name;
    }
    
    static public function common_format($last_name,$first_name=Array()){
	    $last_name = ucfirst(strtolower($last_name)).' ';

	    for ($i=1; $i< sizeof($first_name);$i++){
		    $pattern = '/^[aoe]/';
		    preg_match($pattern,strtolower($first_name[$i]),$matches);
		    if (sizeof($matches)!=0){
			    $first_name[$i] = "'".$first_name[$i];
		    }
	    }
	    $first_name = ucfirst(implode("", $first_name));
	    return $last_name.$first_name;
    }

    static public function passport_format_no_hyphen($last_name,$first_name=Array()){
	    $last_name = strtoupper($last_name).',';
	    $first_name = strtoupper(implode(" ", $first_name));
	    return $last_name.$first_name;
    }

    static public function passport_format_western($last_name,$first_name=Array()){ 
	    $last_name = strtoupper($last_name);
	    $first_name = strtoupper(implode(' ', $first_name));
	    return $first_name.' '.$last_name;
    }

	function eng_name_format($user_name_eng,$_post_name_format){
	  $eng_name_format = Array();
	  foreach($user_name_eng as $id => $name){
	    $last_name = array_shift($user_name_eng[$id]);
	    $first_name = ($user_name_eng[$id]);
	    $eng_name_format[$id] = $this->$_post_name_format($last_name, $first_name);
	  }
	  return $eng_name_format;
	}

	function show_hanzi_has_multiph($users_name_multiph){
	  $hanzi = Array(); $hanzi_multi_ph = Array();  $data = Array();
	  if (sizeof($users_name_multiph)>0) {
	    foreach($users_name_multiph as $id => $name){
	      foreach($name as $pos => $row){
		for($i=0;$i<sizeof($row);$i++){
		  $sn = $users_name_multiph[$id][$pos][$i]['sn'];
		  $ph = $users_name_multiph[$id][$pos][$i]['ph'];
		  $tmp[$sn] = $ph;

		  if (empty($hanzi[$id][$pos]['chinese'])){
		    $char = $users_name_multiph[$id][$pos][$i]['chinese'];
		    $hanzi[$id][$pos]['chinese'] = $char;
		    //$hanzi[$id][$pos]['chinese'] = $char;
		  }//end if

		}//end for($i=0;$i<sizeof($row);$i++)
		$hanzi_multi_ph[$id][$pos] = $tmp;
		unset($tmp);
	      }//end foreach($name as $pos => $value)
	    } //end foreach($studPh->_users_name_multiph as $id => $name)
	  }//end if
	  $data['hanzi'] = $hanzi;
	  $data['hanzi_multi_ph'] = $hanzi_multi_ph;
	  return $data;
	}



}

class pdoDbException extends PDOException {

  public function __construct(PDOException $e) {
    if(strstr($e->getMessage(), 'SQLSTATE[')) {
      preg_match('/SQLSTATE\[(\w+)\] \[(\w+)\] (.*)/', $e->getMessage(), $matches);
      $this->code = ($matches[1] == 'HT000' ? $matches[2] : $matches[1]);
      $this->message = $matches[3];
    }
  }
}

?>
