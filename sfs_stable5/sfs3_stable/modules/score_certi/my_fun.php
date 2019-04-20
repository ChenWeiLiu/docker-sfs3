<?php

// $Id: my_fun.php 6278 2011-01-06 13:57:59Z infodaes $

function num2str($money) {
    $ar = array("箂", "滁", "禠", "把", "竩", "ヮ", "嘲", "琺", "", "╤") ;
    $cName = array("", "", "珺", "ㄕ", "", "窾", "珺", "ㄕ", "", "货", "珺", "ㄕ", "");
    $conver = "";
    $cLast = "" ;
    $cZero = 0;
    $i = 0;
    for ($j = strlen($money) ; $j >=1 ; $j--){  
      $cNum = intval(substr($money, $i, 1));
      $cunit = $cName[$j]; //计
      if ($cNum == 0) { //耞计琌0,狦琌0,玥癘魁Τ碭0
         $cZero++;
         if (strpos($cunit,"窾货") >0 && ($cLast == "")){ // '狦琌窾,货,玥计窾货ㄓ干
          $cLast = $cunit ;
         }      
      }else {
        if ($cZero > 0) {// '狦计0Τn,玥箂蠢┮Τ0
          if (strpos("窾货", substr($conver, strlen($conver)-2)) >0) {
             $conver .= $cLast; //'狦程ぃ琌货,窾,玥程干"货窾"
          }
          $conver .=  "箂" ;
          $cZero = 0;
          $cLast = "" ;
        }
         $conver = $conver.$ar[$cNum].$cunit; // '狦计⊿Τ0,玥琌いゅ计+虫          
      }
      $i++;
    }  
  //'耞计程琌0,狦程0,玥р窾货干
     if (strpos("窾货", substr($conver, strlen($conver)-2)) >0) {
       $conver .=$cLast; // '狦程ぃ琌货,窾,玥程干"货窾"
    }
    return $conver;
}

function sc2str($score="",$rule=""){
	
	$r=explode("\n",$rule);
	while(list($k,$v)=each($r)){

		$str=explode("_",$v);
		$du_str = (double)$str[2];
		
		if($str[1]==">="){
			if($score >= $du_str)return $str[0];
		}elseif($str[1]==">"){
			if($score > $du_str)return $str[0];
		}elseif($str[1]=="="){
			if($score == $du_str)return $str[0];
		}elseif($str[1]=="<"){
			if($score < $du_str)return $str[0];
		}elseif($str[1]=="<="){
			if($score <= $du_str)return $str[0];
		}
	}
	$score_name="";
	return $score_name;
}

function year_seme_menu($sel_year,$sel_seme) {
	global $CONN;

	$sql="select year,semester from school_class where enable='1' order by year,semester";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$year=$rs->fields["year"];
		$semester=$rs->fields["semester"];
		if ($year!=$oy || $semester!=$os)
			$show_year_seme[$year."_".$semester]=$year."厩材".$semester."厩戳";
		$oy=$year;
		$os=$semester;
		$rs->MoveNext();
	}
	$scys = new drop_select();
	$scys->s_name ="year_seme";
	$scys->top_option = "匡拒厩戳";
	$scys->id = $sel_year."_".$sel_seme;
	$scys->arr = $show_year_seme;
	$scys->is_submit = true;
	return $scys->get_select();
}

function class_year_menu($sel_year,$sel_seme,$id) {
	global $school_kind_name,$CONN;

	$sql="select distinct c_year from school_class where year='$sel_year' and semester='$sel_seme' and enable='1' order by c_year";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$show_year_name[$rs->fields["c_year"]]=$school_kind_name[$rs->fields["c_year"]]."";
		$rs->MoveNext();
	}
	$scy = new drop_select();
	$scy->s_name ="year_name";
	$scy->top_option = "匡拒";
	$scy->id = $id;
	$scy->arr = $show_year_name;
	$scy->is_submit = true;
	return $scy->get_select();
}

function class_name_menu($sel_year,$sel_seme,$sel_class,$id) {
	global $CONN;

	$sql="select distinct c_name,c_sort from school_class where year='$sel_year' and semester='$sel_seme' and c_year='$sel_class' and enable='1' order by c_sort";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$show_class_year[$rs->fields["c_sort"]]=$rs->fields["c_name"]."痁";
		$rs->MoveNext();
	}
	$sc = new drop_select();
	$sc->s_name ="me";
	$sc->top_option = "匡拒痁";
	$sc->id = $id;
	$sc->arr = $show_class_year;
	$sc->is_submit = true;
	return $sc->get_select();
}
?>