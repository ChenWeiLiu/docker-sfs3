<?php

// $Id: class.health.php 8149 2014-09-27 02:32:17Z smallduh $

class health_chart {
	var $sn_arr=array(); //學生流水號陣列
	var $snb_arr=array(); //男學生陣列
	var $sng_arr=array(); //女學生陣列
	var $sn_str=""; //SQL用學生流水號字串
	var $year_seme_arr=array(); //學期陣列
	var $year_seme_str=""; //SQL用學期字串
	var $sight=array(); //視力陣列
	var $stud_base=array(); //學生基本資料陣列
	var $get_stud_base=true;	//是否取得學生基本資料陣列
	var $stud_data=array(); //學生資料陣列
	var $avg_data=array(); //平均資料陣列
	var $default_arr=false;	//是否初始化空陣列
	var $today; //記錄日期 Y-m-d H:i:s
	var $measure_date; //測量日期 Y-m-d
	var $teacher_sn;
	var $checks_arr=array(); //健檢學年陣列
	var $BMI_ARR=array(); //BMI陣列
	var $GHD_ARR=array(); //GHD陣列

	function health_chart() {
		global $_SESSION;

		$this->today=date("Y-m-d H:i:s");
		if ($_POST['update']['myear'] && $_POST['update']['mmonth'] && $_POST['update']['mday']) $this->measure_date=$_POST['update']['myear']."-".$_POST['update']['mmonth']."-".$_POST['update']['mday'];
		$this->teacher_sn=$_SESSION['session_tea_sn'];
	}

	function set_stud ($sn_arr=array(),$sel_year="",$sel_seme="") {
		if (count($sn_arr)>0) {
			$this->sn_str="'".implode("','",$sn_arr)."'";
			$this->sn_arr=$sn_arr;
			$this->get_stud_base($sel_year,$sel_seme);
		}
	}

	function set_class_name ($sel_year,$sel_seme,$class_num) {
		$this->get_stud_base($sel_year,$sel_seme,$class_num);
	}

	function set_year_seme ($year_seme_arr=array()) {
		if (count($year_seme_arr)>0) {
			$this->year_seme_str="'".implode("','",$year_seme_arr)."'";
			$this->year_seme_arr=$year_seme_arr;
		} else {
			$ys=sprintf("%03d",curr_year()).curr_seme();
			$this->year_seme_str="'$ys'";
			$this->year_seme_str2="and year='".curr_year()."' and semester='".curr_seme()."'";
			$this->year_seme_arr=array($ys);
		}
	}

	function set_class_num ($sel_year,$sel_seme) {
		global $CONN;

		if ($this->sn_str) {
			$ys=sprintf("%03d",$sel_year).$sel_seme;
			$query="select * from stud_seme where seme_year_seme='$ys' and student_sn in (".$this->sn_str.")";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$this->stud_base[$res->fields[student_sn]][seme_class]=$res->fields['seme_class'];
				$this->stud_base[$res->fields[student_sn]][seme_num]=$res->fields['seme_num'];
				$res->MoveNext();
			}
		}
	}

	//取得學生資料
	function get_stud_base($sel_year="",$sel_seme="",$class_num="",$mode=0) {
		global $CONN;

		if ($sel_year && $sel_seme && $class_num) {
			if (strlen($class_num)==1)
			$class_str="and b.seme_class like '$class_num%'";
			elseif ($class_num=="all")
			$class_str="";
			else
			$class_str="and b.seme_class='$class_num'";
			$query="select a.*,b.seme_class,b.seme_num from stud_base a, stud_seme b where a.student_sn=b.student_sn and a.stud_study_cond in (0,5) and b.seme_year_seme='".sprintf("%03d",$sel_year).$sel_seme."' $class_str order by b.seme_year_seme,b.seme_class,b.seme_num";
			$set_sn=true;
		} else {
			if (empty($sel_year)) $sel_year=curr_year();
			if (empty($sel_seme)) $sel_seme=curr_seme();
			$query="select a.*,b.seme_class,b.seme_num from stud_base a, stud_seme b where a.student_sn=b.student_sn and a.stud_study_cond in (0,5) and b.seme_year_seme='".sprintf("%03d",$sel_year).$sel_seme."' and a.student_sn in (".$this->sn_str.") order by b.seme_year_seme,b.seme_class,b.seme_num";
		}
		$res=$CONN->Execute($query);
		$sn_arr=array();
		while(!$res->EOF) {
			$sn_arr[]=$res->fields['student_sn'];
			$this->stud_base[$res->fields[student_sn]]=array();
			if ($mode) {
				$this->stud_base[$res->fields[student_sn]][seme_class]=$res->fields['seme_class'];
				$this->stud_base[$res->fields[student_sn]][seme_num]=$res->fields['seme_num'];
			} else {
				$this->stud_data[$res->fields['seme_class']][$res->fields['seme_num']][student_sn]=$res->fields['student_sn'];
			}
			$res->MoveNext();
		}
		if ($set_sn) {
			$this->sn_str="'".implode("','",$sn_arr)."'";
			$this->sn_arr=$sn_arr;
		}

		//血型陣列
		$blood_arr=array(0=>"未設定",1=>"A",2=>"B",3=>"O",4=>"AB");
		//取得學生基本資料
		$query="select * from stud_base where student_sn in (".$this->sn_str.")";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$this->stud_base[$res->fields[student_sn]][stud_id]=$res->fields['stud_id'];
			$this->stud_base[$res->fields[student_sn]][student_sn]=$res->fields['student_sn'];
			$this->stud_base[$res->fields[student_sn]][stud_name]=$res->fields['stud_name'];
			$this->stud_base[$res->fields[student_sn]][stud_sex]=$res->fields['stud_sex'];
			if ($res->fields['stud_sex']==1) $this->snb_arr[]=$res->fields[student_sn];
			elseif ($res->fields['stud_sex']==2) $this->sng_arr[]=$res->fields[student_sn];
			$this->stud_base[$res->fields[student_sn]][stud_study_cond]=$res->fields['stud_study_cond'];
			$this->stud_base[$res->fields[student_sn]][stud_study_year]=$res->fields['stud_study_year'];
			$this->stud_base[$res->fields[student_sn]][stud_person_id]=$res->fields['stud_person_id'];
			$this->stud_base[$res->fields[student_sn]][stud_birthday]=$res->fields['stud_birthday'];
			$this->stud_base[$res->fields[student_sn]][stud_blood_type]=$blood_arr[$res->fields['stud_blood_type']];
			$this->stud_base[$res->fields[student_sn]][stud_addr_2]=$res->fields['stud_addr_2'];
			$this->stud_base[$res->fields[student_sn]][stud_tel_2]=$res->fields['stud_tel_2'];
			$this->stud_base[$res->fields[student_sn]][curr_class_num]=$res->fields['curr_class_num'];
			$res->MoveNext();
		}

		//取得父母資料
		$query="select * from stud_domicile where student_sn in (".$this->sn_str.")";
		$res=$CONN->Execute($query);
		foreach ($res as $row) {
			$this->stud_base[$row[student_sn]][fath_name]=$row['fath_name'];
			$this->stud_base[$row[student_sn]][moth_name]=$row['moth_name'];
			$this->stud_base[$row[student_sn]][guardian_name]=$row['guardian_name'];
		}
	}

	//取得BMI陣列
	function get_BMI() {
		global $CONN;

		if (count($this->BMI_ARR)==0) {
			$query="select * from BMI order by `year`,`sex`,`range`";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$this->BMI_ARR[$res->fields['year']][$res->fields['sex']][$res->fields['range']]=$res->fields['value'];
				$res->MoveNext();
			}
		}
	}

	//取得GHD陣列
	function get_GHD() {
		global $CONN;

		if (count($this->GHD_ARR)==0) {
			$query="select * from GHD order by year,sex";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$this->GHD_ARR[$res->fields['year']][$res->fields['sex']]=$res->fields['value'];
				$res->MoveNext();
			}
		}
	}

	//取得身高體重資料
	function get_wh() {
		global $CONN,$IS_JHORES;

		if (count($this->sn_arr)>0) {
			reset($this->sn_arr);
			if ($this->default_arr) {
				foreach($this->sn_arr as $sn) {
					for($i=0;$i<=8;$i++) {
						for($j=1;$j<=2;$j++) {
							$year_seme=sprintf("%03d",($this->stud_base[$sn][stud_study_year]-$IS_JHORES+$i)).$j;
							$this->health_data[$sn][$year_seme][height]="";
							$this->health_data[$sn][$year_seme][weight]="";
						}
					}
				}
			}
			$this->get_BMI();
			$this->get_GHD();
			$query="select * from health_WH where student_sn in (".$this->sn_str.")";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$year_seme=sprintf("%03d",$res->fields[year]).$res->fields[semester];
				$h=$res->fields['height'];
				$w=$res->fields['weight'];
				$this->health_data[$res->fields[student_sn]][$year_seme][height]=$h;
				$this->health_data[$res->fields[student_sn]][$year_seme][weight]=$w;
				$y=intval(substr($this->stud_base[$res->fields[student_sn]][curr_class_num],0,-4));
				$this->avg_data[height][$y][value]+=$h;
				$this->avg_data[height][$y][nums]++;
				$this->avg_data[weight][$y][value]+=$w;
				$this->avg_data[weight][$y][nums]++;
				//計算BMI
				$BMI=0;
				if (intval($h)>0 && intval($w)>0) {
                                    //修改BMI小數位數
					$BMI=round($w/$h/$h*10000,2);
					$this->health_data[$res->fields[student_sn]][$year_seme][BMI]=$BMI;
				}
				//計算年齡
				if ($res->fields['measure_date']!="0000-00-00") {
					$dm_arr=explode("-",$res->fields['measure_date']);
					$db_arr=explode("-",$this->stud_base[$res->fields[student_sn]][stud_birthday]);
					//					$dm_arr[1]+=6; //為了四捨五入, 所以先加六個月
					//					if ($dm_arr[1]>12) {
					//						$dm_arr[1]-=12;
					//						$dm_arr[0]++;
					//					}
					//					$years=$dm_arr[0]-$db_arr[0]-1;
					//					if ($dm_arr[1]>$db_arr[1]) $years++;
					//					elseif ($dm_arr[1]==$db_arr[1] && $dm_arr[2]>$db_arr[2]) $years++;
					$years=$dm_arr[0]-$db_arr[0];
					if ($dm_arr[1]<$db_arr[1] || ($dm_arr[1]==$db_arr[1] && $dm_arr[2]<$db_arr[2])) $years--;
					$this->health_data[$res->fields[student_sn]][$year_seme][years]=$years;
					//計算生長評值
					$Bid=0;
					if ($BMI) {
						foreach($this->BMI_ARR[$years][$this->stud_base[$res->fields[student_sn]][stud_sex]] as $id=>$v) {
							if ($BMI>$v) $Bid=$id;
						}
						$this->health_data[$res->fields[student_sn]][$year_seme][Bid]=$Bid;
					}
					//查出GHD值
					$this->health_data[$res->fields[student_sn]][$year_seme][GHD]=$this->GHD_ARR[$years][$this->stud_base[$res->fields[student_sn]][stud_sex]];
					//判斷生長遲緩
					if ($h<=$this->health_data[$res->fields[student_sn]][$year_seme][GHD]) $this->health_data[$res->fields[student_sn]][$year_seme][stunting]=1;
				}
				$res->MoveNext();
			}
		}
	}

	//更新身高體重資料
	function update_wh($update=array(),$mode="") {
		global $CONN;
    reset($update['new']);
		while(list($sn,$v)=each($update['new'])) {
			reset($v);
			while(list($ys,$vv)=each($v)) {
				$year=intval(substr($ys,0,-1));
				$semester=substr($ys,-1,1);
				reset($vv);
				while(list($k,$value)=each($vv)) {
					if ($mode=="del") {
						//刪除資料
						$res=$CONN->Execute("delete from health_WH where student_sn='$sn' and year='$year' and semester='$semester'");
					} else {
						//更新資料
						$query="select * from health_WH where student_sn='$sn' and year='$year' and semester='$semester'";
						$res=$CONN->Execute($query);
						if ($res->RecordCount()>0) {
							if ($update['old'][$sn][$ys][$k]!=$value) {
								if ($this->measure_date) $m_str=",measure_date='".$this->measure_date."'";
								$query="update health_WH set $k='$value', teacher_sn='".$this->teacher_sn."' $m_str where student_sn='$sn' and year='$year' and semester='$semester'";
								$res=$CONN->Execute($query);
							}
						} else {
							if ($value!="") {
								$query="insert into health_WH (year,semester,student_sn,$k,teacher_sn) values ('$year','$semester','$sn','$value','".$this->teacher_sn."')";
								$res=$CONN->Execute($query);
							}
						}
					}
				}
			}
		}
		//更新被清空欄位
		while(list($sn,$v)=each($update['old'])) {
			reset($v);
			while(list($ys,$vv)=each($v)) {
				$year=intval(substr($ys,0,-1));
				$semester=substr($ys,-1,1);
				reset($vv);
				while(list($k,$value)=each($vv)) {
					if ($update['new'][$sn][$ys][$k]=="") {
						$query="update health_WH set $k='' where student_sn='$sn' and year='$year' and semester='$semester'";
						$res=$CONN->Execute($query);
					}
				}
			}
		}
	}

	//取得身高體重平均
	function get_wh_avg() {
		global $CONN;

		if (count($this->sn_arr)==0) {
			$this->set_year_seme();
			$query="select student_sn from stud_seme where seme_year_seme='".$this->year_seme_str."'";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$sn[]=$res->fields['student_sn'];
				$res->MoveNext();
			}
			if (count($sn)>0) {
				$sn_str="'".implode("','",$sn)."'";
				$sn=array();
				$query="select student_sn from stud_base where stud_study_cond='0' and student_sn in ($sn_str)";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$sn[]=$res->fields['student_sn'];
					$res->MoveNext();
				}
			}
			if (count($sn)>0) {
				$this->set_stud($sn);
			}
		}
		$this->get_wh();
	}

	//取得視力檢查資料
	function get_sight() {
		global $CONN,$IS_JHORES;

		if (count($this->sn_arr)>0) {
			reset($this->sn_arr);
			if ($this->default_arr) {
				foreach($this->sn_arr as $sn) {
					for($i=0;$i<=8;$i++) {
						for($j=1;$j<=2;$j++) {
							$year_seme=sprintf("%03d",($this->stud_base[$sn][stud_study_year]-$IS_JHORES+$i)).$j;
							$this->health_data[$sn][$year_seme]['r']['sight_o']="";
							$this->health_data[$sn][$year_seme]['r']['sight_r']="";
							$this->health_data[$sn][$year_seme]['l']['sight_o']="";
							$this->health_data[$sn][$year_seme]['l']['sight_r']="";
						}
					}
				}
			}
			$query="select * from health_sight where student_sn in (".$this->sn_str.")";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$sn=$res->fields['student_sn'];
				$side=$res->fields['side'];
				$year_seme=sprintf("%03d",$res->fields[year]).$res->fields[semester];
				$this->health_data[$sn][$year_seme][$side]['sight_o']=$res->fields['sight_o'];
				$this->health_data[$sn][$year_seme][$side]['sight_r']=$res->fields['sight_r'];
				$this->health_data[$sn][$year_seme][$side]['My']=$res->fields['My'];
				$this->health_data[$sn][$year_seme][$side]['Hy']=$res->fields['Hy'];
				$this->health_data[$sn][$year_seme][$side]['Ast']=$res->fields['Ast'];
				$this->health_data[$sn][$year_seme][$side]['Amb']=$res->fields['Amb'];
				$this->health_data[$sn][$year_seme][$side]['other']=$res->fields['other'];
				$this->health_data[$sn][$year_seme][$side]['manage_id']=$res->fields['manage_id'];
				$this->health_data[$sn][$year_seme][$side]['diag']=$res->fields['diag'];
				$this->health_data[$sn][$year_seme][$side]['hospital']=$res->fields['hospital'];
				$res->MoveNext();
			}
		}
	}

	//更新視力檢查資料
	function update_sight($update=array(),$mode="") {
		global $CONN;

		while(list($sn,$v)=each($update['new'])) {
			reset($v);
			while(list($ys,$vv)=each($v)) {
				$year=intval(substr($ys,0,-1));
				$semester=substr($ys,-1,1);
				reset($vv);
				while(list($side,$vvv)=each($vv)) {
					reset($vvv);
					while(list($k,$value)=each($vvv)) {
						if ($mode=="del") {
							//刪除資料
							$res=$CONN->Execute("delete from health_sight where student_sn='$sn' and year='$year' and semester='$semester'");
						} else {
							if ($osn) {
								if ($osn==$sn && $oside==$side)
								continue;
								else {
									$osn="";
									$oside="";
								}
							}
							//更新資料
							$query="select * from health_sight where student_sn='$sn' and year='$year' and semester='$semester' and side='$side'";
							$res=$CONN->Execute($query);
							if ($res->RecordCount()>0) {
								if (in_array($k,array("sight_o","sight_r"))) {
									if ($update['old'][$sn][$ys][$side][$k]!=$value) {
										if ($this->measure_date) $m_str=",measure_date='".$this->measure_date."'";
										$query="update health_sight set $k='$value', teacher_sn='".$this->teacher_sn."' $m_str where student_sn='$sn' and year='$year' and semester='$semester' and side='$side'";
										$res=$CONN->Execute($query);
									}
								} elseif ($vvv['My'] || $vvv['Hy'] || $vvv['Ast'] || $vvv['Amb'] || $vvv['other']) {
									$query="update health_sight set My='".$vvv['My']."', Hy='".$vvv['Hy']."', Ast='".$vvv['Ast']."', Amb='".$vvv['Amb']."', other='".$vvv['other']."', teacher_sn='".$this->teacher_sn."' where student_sn='$sn' and year='$year' and semester='$semester' and side='$side'";
									$res=$CONN->Execute($query);
									$osn=$sn;
									$oside=$side;
								}
							} else {
								if (in_array($k,array("sight_o","sight_r"))) {
									if ($value!="") {
										$query="insert into health_sight (year,semester,student_sn,side,$k,measure_date,teacher_sn) values ('$year','$semester','$sn','$side','$value','".$this->measure_date."','".$this->teacher_sn."')";
										$res=$CONN->Execute($query);
									}
								} elseif ($vvv['My'] || $vvv['Hy'] || $vvv['Ast'] || $vvv['Amb'] || $vvv['other']) {
									$query="insert into health_sight (year,semester,student_sn,side,My,Hy,Ast,Amb,other,teacher_sn) values ('$year','$semester','$sn','$side','".$vvv['My']."','".$vvv['Hy']."','".$vvv['Ast']."','".$vvv['Amb']."','".$vvv['other']."','".$this->teacher_sn."')";
									$res=$CONN->Execute($query);
									$osn=$sn;
									$oside=$side;
								}
							}
						}
					}
				}
			}
		}
		while(list($sn,$v)=each($update['old'])) {
			reset($v);
			while(list($ys,$vv)=each($v)) {
				$year=intval(substr($ys,0,-1));
				$semester=substr($ys,-1,1);
				reset($vv);
				while(list($side,$vvv)=each($vv)) {
					if (count($update['new'][$sn][$ys][$side])==0) {
						if ($vvv['My'] || $vvv['Hy'] || $vvv['Ast'] || $vvv['Amb'] || $vvv['other']) {
							$query="update health_sight set My='', Hy='', Ast='', Amb='', other='', teacher_sn='".$this->teacher_sn."' where year='$year' and semester='$semester' and student_sn='$sn' and side='$side'";
						} else {
							$query="update health_sight set sight_o='',sight_r='', teacher_sn='".$this->teacher_sn."' where year='$year' and semester='$semester' and student_sn='$sn' and side='$side'";
						}
						$res=$CONN->Execute($query);
					}
				}
			}
		}

		// 更新處置狀況
		$student_sn = (int)$_POST['student_sn'];
		while(list($year_seme,$data) = each($update['diag'])) {
			$year = (int)substr($year_seme,0,-1);
			$semester = (int)substr($year_seme,-1);
			while(list($side, $val) = each($data)) {
				$diag = strip_tags(trim($val));
				$manage_id = strip_tags(trim($update['manage_id'][$year_seme][$side]));
				$query = "UPDATE health_sight SET  manage_id='$manage_id' , diag='$diag', teacher_sn=".$this->teacher_sn.
			 " WHERE student_sn=$student_sn AND year=$year AND semester=$semester and side='$side' 	";
				$CONN->Execute($query) or die($query);
			}
		}

	}

	//取得立體感資料
	function get_ntu(){
		global $CONN;

		if ($this->sn_str) {
			$query="select * from health_sight_ntu where student_sn in (".$this->sn_str.")";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$this->stud_base[$res->fields[student_sn]][ntu]=$res->fields['ntu'];
				$res->MoveNext();
			}
		}
	}

	//更新立體感資料
	function update_ntu($update=array()){
		global $CONN;

		while(list($sn,$v)=each($update['new'])) {
			reset($v);
			while(list($k,$value)=each($v)) {
				if ($k=="ntu" && $update['new'][$sn]['ntu']!=$update['old'][$sn]['ntu']) {
					$query="replace into health_sight_ntu (student_sn,ntu,teacher_sn) values ('$sn','$value','".$this->teacher_sn."')";
					$res=$CONN->Execute($query);
				}
			}
		}
	}

	//取得辨色力異常資料
	function get_co(){
		global $CONN;

		if ($this->sn_str) {
			$query="select * from health_sight_co where student_sn in (".$this->sn_str.")";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$this->stud_base[$res->fields[student_sn]][co]=$res->fields['co'];
				$res->MoveNext();
			}
		}
	}

	//更新辨色力異常資料
	function update_co($update=array()){
		global $CONN;

		while(list($sn,$v)=each($update['new'])) {
			reset($v);
			while(list($k,$value)=each($v)) {
				if ($k=="co" && $update['new'][$sn]['co']!=$update['old'][$sn]['co']) {
					$query="replace into health_sight_co (student_sn,co,teacher_sn) values ('$sn','$value','".$this->teacher_sn."')";
					$res=$CONN->Execute($query);
				}
			}
		}
	}

	//取得寄生蟲資料
	function get_worm(){
		global $CONN;

		if ($this->sn_str) {
			$query="select * from health_worm where student_sn in (".$this->sn_str.")";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$year_seme=sprintf("%03d",$res->fields['year']).$res->fields['semester'];
				$this->health_data[$res->fields['student_sn']][$year_seme]['exp']['worm'][$res->fields['no']]['status']=$res->fields['worm'];
				$this->health_data[$res->fields['student_sn']][$year_seme]['exp']['worm'][$res->fields['no']]['med']=$res->fields['med'];
				$this->health_data[$res->fields['student_sn']][$year_seme]['exp']['worm'][$res->fields['no']]['date']=$res->fields['measure_date'];
				$res->MoveNext();
			}
		}
	}

	//更新寄生蟲資料
	function update_worm($update=array()){
		global $CONN;

		if ($this->measure_date) {
			$sstr=",measure_date";
			$istr=",'".$this->measure_date."'";
			$ustr=",measure_date='".$this->measure_date."'";
		}
		while(list($sn,$v)=each($update['new'])) {
			reset($v);
			while(list($ys,$vv)=each($v)) {
				$year=intval(substr($ys,0,-1));
				$semester=substr($ys,-1,1);
				reset($vv);
				while(list($no,$vvv)=each($vv)) {
					reset($vvv);
					while(list($k,$value)=each($vvv)) {
						if (($k=="worm" || $k=="med")&& $update['new'][$sn][$ys][$no][$k]!=$update['old'][$sn][$ys][$no][$k]) {
							$query="select * from health_worm where year='$year' and semester='$semester' and student_sn='$sn' and no='$no'";
							$res=$CONN->Execute($query);
							if ($res->RecordCount()>0)
							$query="update health_worm set $k='$value', teacher_sn='".$this->teacher_sn."' $ustr where year='$year' and semester='$semester' and student_sn='$sn' and no='$no'";
							else
							$query="insert into health_worm (year,semester,student_sn,no,$k,teacher_sn $sstr) values ('$year','$semester','$sn','$no','$value','".$this->teacher_sn."' $istr)";
							$res=$CONN->Execute($query);
						}
					}
				}
			}
		}
		while(list($sn,$v)=each($update['old'])) {
			reset($v);
			while(list($ys,$vv)=each($v)) {
				$year=intval(substr($ys,0,-1));
				$semester=substr($ys,-1,1);
				reset($vv);
				while(list($no,$vvv)=each($vv)) {
					reset($vvv);
					while(list($k,$value)=each($vvv)) {
						if ($k=="worm" && $update['new'][$sn][$ys][$no][$k]=="" && $value!="") {
							$query="delete from health_worm where year='$year' and semester='$semester' and student_sn='$sn' and no='$no'";
							$res=$CONN->Execute($query);
						}
					}
				}
			}
		}
	}

	//取得尿液篩檢資料
	function get_uri(){
		global $CONN;

		if ($this->sn_str) {
			$query="select * from health_uri where student_sn in (".$this->sn_str.")";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$year_seme=sprintf("%03d",$res->fields['year']).$res->fields['semester'];
				$this->health_data[$res->fields['student_sn']][$year_seme]['exp']['uri'][$res->fields['no']]['pro']=$res->fields['pro'];
				$this->health_data[$res->fields['student_sn']][$year_seme]['exp']['uri'][$res->fields['no']]['glu']=$res->fields['glu'];
				$this->health_data[$res->fields['student_sn']][$year_seme]['exp']['uri'][$res->fields['no']]['bld']=$res->fields['bld'];
				$this->health_data[$res->fields['student_sn']][$year_seme]['exp']['uri'][$res->fields['no']]['ph']=$res->fields['ph'];
				$this->health_data[$res->fields['student_sn']][$year_seme]['exp']['uri'][$res->fields['no']]['date']=$res->fields['measure_date'];
				$res->MoveNext();
			}
		}
	}

	//更新尿液篩檢資料
	function update_uri($update=array()){
		global $CONN;

		if ($this->measure_date) {
			$sstr=",measure_date";
			$istr=",'".$this->measure_date."'";
			$ustr=",measure_date='".$this->measure_date."'";
		}
		$item_arr=array("pro","glu","bld","ph");
		$tbl_name="health_uri";
		while(list($sn,$v)=each($update['new'])) {
			reset($v);
			while(list($ys,$vv)=each($v)) {
				$year=intval(substr($ys,0,-1));
				$semester=substr($ys,-1,1);
				reset($vv);
				while(list($no,$vvv)=each($vv)) {
					reset($vvv);
					while(list($k,$value)=each($vvv)) {
						if (in_array($k,$item_arr) && $update['new'][$sn][$ys][$no][$k]!=$update['old'][$sn][$ys][$no][$k]) {
							$query="select * from $tbl_name where year='$year' and semester='$semester' and student_sn='$sn' and no='$no'";
							$res=$CONN->Execute($query);
							if ($res->RecordCount()>0)
							$query="update $tbl_name set $k='$value', teacher_sn='".$this->teacher_sn."' $ustr where year='$year' and semester='$semester' and student_sn='$sn' and no='$no'";
							else
							$query="insert into $tbl_name (year,semester,student_sn,no,$k,teacher_sn $sstr) values ('$year','$semester','$sn','$no','$value','".$this->teacher_sn."' $istr)";
							$res=$CONN->Execute($query);
						}
					}
				}
			}
		}
		/*
		 while(list($sn,$v)=each($update['old'])) {
		 reset($v);
		 while(list($ys,$vv)=each($v)) {
		 $year=intval(substr($ys,0,-1));
		 $semester=substr($ys,-1,1);
		 reset($vv);
		 while(list($no,$vvv)=each($vv)) {
		 reset($vvv);
		 while(list($k,$value)=each($vvv)) {
		 if ($k=="worm" && $update['new'][$sn][$ys][$no][$k]=="" && $value!="") {
		 $query="delete from health_worm where year='$year' and semester='$semester' and student_sn='$sn' and no='$no'";
		 $res=$CONN->Execute($query);
		 }
		 }
		 }
		 }
		 }
		 */
	}

	//取得預防接種資料
	function get_inject($mode=0){
		global $CONN;

		if ($this->sn_str) {
			$query="select * from health_yellowcard where student_sn in (".$this->sn_str.")";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$this->health_data[$res->fields['student_sn']]['inject'][0][0]['times']=$res->fields['value'];
				$res->MoveNext();
			}
			$query="select * from health_inject_record where student_sn in (".$this->sn_str.")";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$sn=$res->fields['student_sn'];
				$kid=$res->fields['kid'];
				$id=$res->fields['id'];
				$this->health_data[$sn]['inject'][$kid][$id]['times']=$res->fields['times'];
				if ($mode) {
					for($i=0;$i<=4;$i++) {
						$d="date".$i;
						$dd=$res->fields[$d];
						if ($dd=="0000-00-00")
						$ddd="";
						else {
							$dddd=explode("-",$dd);
							$dddd[0]-=1911;
							$ddd=$dddd[0]."-".$dddd[1]."-".$dddd[2];
						}
						$this->health_data[$sn]['inject'][$kid][$id][$d]=$ddd;
					}
				} else {
					for($i=0;$i<=4;$i++) {
						$d="date".$i;
						$this->health_data[$sn]['inject'][$kid][$id][$d]=$res->fields[$d];
					}
				}
				$res->MoveNext();
			}
		}
	}

	//更新預防接種資料
	function update_inject($update=array()){
		global $CONN;

		while(list($sn,$v)=each($update['new'])) {
			while(list($n,$vv)=each($v)) {
				if ($n=="inject") {
					while(list($kid,$vvv)=each($vv)) {
						while(list($id,$vvvv)=each($vvv)) {
							if ($id==0) {
								$tbl_name="health_yellowcard";
								if ($vvvv['times']!=$update['old'][$sn]['inject'][$kid][$id]['times'] && $vvvv['times']!="") {
									$query="select * from $tbl_name where student_sn='$sn'";
									$res=$CONN->Execute($query);
									if ($res->RecordCount()>0)
									$query="update $tbl_name set value='".$vvvv['times']."' where student_sn='$sn'";
									else
									$query="insert into $tbl_name (student_sn,value) values ('$sn','".$vvvv['times']."')";
									$res=$CONN->Execute($query);
								}
							} else {
								$tbl_name="health_inject_record";
								if ($vvvv['times']!="" && $vvvv['times']!=$update['old'][$sn]['inject'][$kid][$id]['times']) {
									$query="select * from $tbl_name where student_sn='$sn' and id='$id'";
									$res=$CONN->Execute($query);
									if ($res->RecordCount()>0)
									$query="update $tbl_name set times='".$vvvv['times']."' where student_sn='$sn' and kid='$kid' and id='$id'";
									else
									$query="insert into $tbl_name (student_sn,kid,id,times) values ('$sn','$kid','$id','".$vvvv['times']."')";
									$res=$CONN->Execute($query);
								}
								for ($i=0;$i<=4;$i++) {
									$colname="date".$i;
									$colvalue=$vvvv[$colname];
									if ($colvalue) {
										$y=intval(substr($colvalue,0,-4));
										if ($y<1900) $y+=1911;
										$m=substr($colvalue,-4,-2);
										$d=substr($colvalue,-2);
										$d0=$y."-".$m."-".$d;
										$d1=strtotime($d0);
										if ($d1) $d2=date("Y-m-d",$d1);
										if ($d0==$d2) {
											$query="select * from $tbl_name where student_sn='$sn' and kid='$kid' and id='$id'";
											$res=$CONN->Execute($query);
											$num=$res->RecordCount();
											if ($num>0) $query="update $tbl_name set $colname='$d0' where student_sn='$sn' and kid='$kid' and id='$id'";
											else $query="insert into $tbl_name (student_sn,kid,id,$colname) values ('$sn','$kid','$id','$d0')";
											$res=$CONN->Execute($query);
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

	//取得牙齒檢查資料
	function get_teeth() {
		global $CONN,$IS_JHORES;

		if ($this->sn_str) {
			$query="select * from health_teeth where student_sn in (".$this->sn_str.")";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$year_seme=sprintf("%03d",$res->fields[year]).$res->fields[semester];
				$name=$res->fields['no'];
				$this->health_data[$res->fields[student_sn]][$year_seme][$name]=$res->fields['status'];
				$this->health_data[$res->fields[student_sn]][$year_seme]["C".$res->fields['status']]++;
				$this->health_data[$res->fields[student_sn]][$year_seme][(($name<"T51")?"N":"n").$res->fields['status']]++;
				$this->health_data[$res->fields[student_sn]][$year_seme][(($name<"T51")?"N":"n")."Total"]++;
				$this->health_data[$res->fields[student_sn]][$year_seme][DisTeeth]=1;
				$res->MoveNext();
			}
		}
	}

	//更新牙齒檢查資料
	function update_teeth($update=array(),$mode="") {
		global $CONN;

		while(list($sn,$v)=each($update['new'])) {
			reset($v);
			while(list($ys,$vv)=each($v)) {
				$year=intval(substr($ys,0,-1));
				$semester=substr($ys,-1,1);
				reset($vv);
				while(list($k,$value)=each($vv)) {
					if ($mode=="del") {
						//刪除資料
						$res=$CONN->Execute("delete from health_sight where student_sn='$sn' and year='$year' and semester='$semester'");
					} else {
						if (substr($k,0,1)=="T" && $value!=$update['old'][$sn][$ys][$k]) {
							if (intval($value)>0) {
								//更新資料
								$query="replace into health_teeth (year,semester,student_sn,no,status,teacher_sn) values ('$year','$semester','$sn','$k','$value','".$this->teacher_sn."')";
							} else {
								//更新資料
								$query="delete from health_teeth where year='$year' and semester='$semester' and student_sn='$sn' and no='$k'";
							}
							$res=$CONN->Execute($query);
						}
					}
				}
			}
		}
	}

	//取得疾病及重大疾病資料
	function get_disease() {
		global $CONN;

		$arr=array("disease"=>"health_disease","serious"=>"health_diseaseserious");
		$k_arr=array("health_disease"=>"disease","health_diseaseserious"=>"serious");
		$arr2=array("health_diag_record","health_status_record");
		$str="'".implode("','",$arr)."'";
		while(list($col_name,$tbl_name)=each($arr)) {
			$query="select * from $tbl_name where student_sn in (".$this->sn_str.") order by student_sn,di_id";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$this->stud_base[$res->fields[student_sn]][$col_name][]=$res->fields['di_id'];
				$res->MoveNext();
			}
		}
		$query="select * from health_diag_record where student_sn in (".$this->sn_str.") and tbl in ($str)";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$this->stud_base[$res->fields[student_sn]][diag_record][$k_arr[$res->fields['tbl']]][$res->fields['item']]=$res->fields['memo'];
			$res->MoveNext();
		}
		$query="select * from health_status_record where student_sn in (".$this->sn_str.") and tbl in ($str)";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$this->stud_base[$res->fields[student_sn]][status_record][$k_arr[$res->fields['tbl']]][$res->fields['item']]=$res->fields['memo'];
			$res->MoveNext();
		}
	}

	//更新疾病及重大疾病資料
	function update_disease($update=array(),$mode="") {
		global $CONN;

		$tbl_arr=array("health_disease","health_diseaseserious");
		$tbl_arr2=array("health_diag_record","health_status_record");
		while(list($sn,$v)=each($update)) {
			reset($v);
			while(list($tbl_name,$vv)=each($v)) {
				if (in_array($tbl_name,$tbl_arr)) {
					reset($vv);
					while(list($col_name,$value)=each($vv)) {
						if ($mode=="del") {
							//刪除資料
							$res=$CONN->Execute("delete from $tbl_name where student_sn='$sn' and $col_name='$value'");
							foreach ($tbl_arr2 as $tbl_name2)	$res=$CONN->Execute("delete from $tbl_name2 where student_sn='$sn' and tbl='$tbl_name' and item='$value'");
						} else {
							if ($value) {
								$query="select * from $tbl_name where student_sn='$sn' and $col_name='$value'";
								$res=$CONN->Execute($query);
								if ($res->RecordCount()>0)
								$query="update $tbl_name set $col_name='$value', teacher_sn='".$this->teacher_sn."' where student_sn='$sn'";
								else
								$query="insert into $tbl_name (student_sn,$col_name,teacher_sn) values ('$sn','$value','".$this->teacher_sn."')";
								$res=$CONN->Execute($query);
							}
						}
					}
				} elseif (in_array($tbl_name,$tbl_arr2)) {
					reset($vv);
					while(list($tbl_name2,$vvv)=each($vv)) {
						reset($vvv);
						while(list($item,$value)=each($vvv)) {
							if ($mode=="del") {
								$res=$CONN->Execute("delete from $tbl_name where student_sn='$sn' and tbl='$tbl_name2' and item='$item'");
							} else {
								$value=trim($value);
								if ($value) {
									$query="select * from $tbl_name where student_sn='$sn' and tbl='$tbl_name2' and item='$item'";
									$res=$CONN->Execute($query);
									if ($res->RecordCount()>0)
									$query="update $tbl_name set memo='".nl2br(trim($value))."', teacher_sn='".$this->teacher_sn."' where student_sn='$sn' and tbl='$tbl_name2' and item='$item'";
									else
									$query="insert into $tbl_name (year,semester,student_sn,tbl,item,id,memo,teacher_sn) values ('".$this->stud_base[$sn]['stud_study_year']."',1,'$sn','$tbl_name2','$item',0,'".nl2br(trim($value))."','".$this->teacher_sn."')";
									$res=$CONN->Execute($query);
								}
							}
						}
					}
				}
			}
		}
	}

	//取得身心障礙手冊資料
	function get_bodymind() {
		global $CONN;

		if ($this->sn_str) {
			$query="select * from health_bodymind where student_sn in (".$this->sn_str.")";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$this->stud_base[$res->fields[student_sn]][bodymind][bm_id]=$res->fields['bm_id'];
				$this->stud_base[$res->fields[student_sn]][bodymind][bm_level]=$res->fields['bm_level'];
				$res->MoveNext();
			}
		}
	}

	//更新身心障礙手冊資料
	function update_bodymind($update=array(),$mode="") {
		global $CONN;

		while(list($sn,$v)=each($update)) {
			reset($v);
			while(list($tbl_name,$vv)=each($v)) {
				reset($vv);
				while(list($col_name,$value)=each($vv)) {
					if ($mode=="del") {
						//刪除資料
						$res=$CONN->Execute("delete from $tbl_name where student_sn='$sn'");
					} else {
						$query="select * from $tbl_name where student_sn='$sn'";
						$res=$CONN->Execute($query);
						if ($res->RecordCount()>0)
						$query="update $tbl_name set $col_name='$value', teacher_sn='".$this->teacher_sn."' where student_sn='$sn'";
						else
						$query="insert into $tbl_name (student_sn,$col_name,teacher_sn) values ('$sn','$value','".$this->teacher_sn."')";
						$res=$CONN->Execute($query);
					}
				}
			}
		}
	}

	//取得家族疾病史資料
	function get_inherit() {
		global $CONN;

		if ($this->sn_str) {
			$query="select * from health_inherit where student_sn in (".$this->sn_str.") order by student_sn,folk_id";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$this->stud_base[$res->fields[student_sn]][inherit][$res->fields['folk_id']]=$res->fields['di_id'];
				$res->MoveNext();
			}
		}
	}

	//更新家族疾病史資料
	function update_inherit($update=array(),$mode="") {
		global $CONN;

		while(list($sn,$v)=each($update)) {
			reset($v);
			while(list($tbl_name,$vv)=each($v)) {
				reset($vv);
				while(list($col_name,$value)=each($vv)) {
					if ($col_name=="folk_id") $folk_id=$value;
					if ($mode=="del" && $col_name=="folk_id") {
						//刪除資料
						$res=$CONN->Execute("delete from $tbl_name where student_sn='$sn' and $col_name='$value'");
					} else {
						$query="select * from $tbl_name where student_sn='$sn' and folk_id='$folk_id'";
						$res=$CONN->Execute($query);
						if ($res->RecordCount()>0)
						$query="update $tbl_name set $col_name='$value', teacher_sn='".$this->teacher_sn."' where student_sn='$sn' and folk_id='$folk_id'";
						elseif ($col_name=="folk_id")
						$query="insert into $tbl_name (student_sn,$col_name,teacher_sn) values ('$sn','$value','".$this->teacher_sn."')";
						else
						$query="";
						if ($query) $res=$CONN->Execute($query);
					}
				}
			}
		}
	}

	//取得學生護送醫院資料
	function get_hospital() {
		global $CONN;

		if ($this->sn_str) {
			$query="select * from health_hospital_record where student_sn in (".$this->sn_str.")";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$this->stud_base[$res->fields['student_sn']]['hospital'][$res->fields['no']]=$res->fields['id'];
				$res->MoveNext();
			}
		}
	}

	//更新學生護送醫院資料
	function update_hospital($update=array(),$mode) {
		global $CONN;

		while(list($sn,$v)=each($update)) {
			reset($v);
			while(list($tbl_name,$vv)=each($v)) {
				reset($vv);
				while(list($col_name,$value)=each($vv)) {
					$value=trim(intval($value));
					if ($mode=="del") {
						$CONN->Execute("delete from $tbl_name where student_sn='$sn' and $col_name='$value'");
						$query="select * from $tbl_name where student_sn='$sn' order by no";
						$res=$CONN->Execute($query);
						$i=1;
						while(!$res->EOF) {
							$CONN->Execute("update $tbl_name set no='$i', teacher_sn='".$this->teacher_sn."' where student_sn='$sn' and id='".$res->fields['id']."'");
							$i++;
							$res->MoveNext();
						}
					} else {
						$query="select * from $tbl_name where student_sn='$sn' and $col_name='$value'";
						$res=$CONN->Execute($query);
						if ($res->RecordCount()==0 && $value!='0') {
							$query="select max(no) as num from $tbl_name where student_sn='$sn'";
							$res=$CONN->Execute($query);
							$num=$res->fields['num']+1;
							$CONN->Execute("insert into $tbl_name (student_sn,no,id,teacher_sn) values ('$sn','$num','$value','".$this->teacher_sn."')");
						}
					}
				}
			}
		}
	}

	//取得學生保險資料
	function get_insurance() {
		global $CONN;

		if ($this->sn_str) {
			$query="select * from health_insurance_record where student_sn in (".$this->sn_str.")";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$this->stud_base[$res->fields['student_sn']]['insurance'][$res->fields['id']]=1;
				$res->MoveNext();
			}
		}
	}

	//更新學生保險資料
	function update_insurance($update=array(),$mode) {
		global $CONN;

		while(list($sn,$v)=each($update)) {
			reset($v);
			while(list($tbl_name,$vv)=each($v)) {
				reset($vv);
				while(list($col_name,$vvv)=each($vv)) {
					if ($mode=="del") {
						$CONN->Execute("delete from $tbl_name where student_sn='$sn' and $col_name='$vvv'");
						$query="select * from $tbl_name where student_sn='$sn' order by no";
						$res=$CONN->Execute($query);
						$i=1;
						while(!$res->EOF) {
							$CONN->Execute("update $tbl_name set no='$i', teacher_sn='".$this->teacher_sn."' where student_sn='$sn' and id='".$res->fields['id']."'");
							$i++;
							$res->MoveNext();
						}
					} elseif ($mode=="ins") {
						$query="select * from $tbl_name where student_sn='$sn' order by no";
						$res=$CONN->Execute($query);
						$temp_arr=array();
						$i=1;
						while(!$res->EOF) {
							$id=$res->fields['id'];
							if ($id==$vvv) $vvv=0;
							if ($id>$vvv && $vvv>0) {
								$temp_arr[$i]=$vvv;
								$i++;
							}
							$temp_arr[$i]=$res->fields['id'];
							$i++;
							$res->MoveNext();
						}
						if ($i==1) $temp_arr[$i]=$vvv;
						$CONN->Execute("delete from $tbl_name where student_sn='$sn'");
						foreach($temp_arr as $k=>$d) {
							$CONN->Execute("insert into $tbl_name (student_sn,no,id,teacher_sn) values ('$sn','$k','$d','".$this->teacher_sn."')");
						}
					} else {
						$CONN->Execute("delete from $tbl_name where student_sn='$sn'");
						reset($vvv);
						while(list($i,$id)=each($vvv)) {
							$CONN->Execute("insert into $tbl_name (student_sn,no,id,teacher_sn) values ('$sn','".($i+1)."','$id','".$this->teacher_sn."')");
						}
					}
				}
			}
		}
		// 是否加入其他保險單位
		if ($_POST['other_insurance']!='') {
			$name = strip_tags(trim($_POST['other_insurance']));
			$query = "SELECT id, enable FROM health_insurance WHERE name='$name'";
			$res = $CONN->Execute($query);
			$id = 0;
			// 如果沒有則加入一筆
			if ($res->recordCount() == 0) {
				$query = "INSERT INTO health_insurance (name) VALUES('$name')";
				$CONN->Execute($query) or die ($query);
				$id = $CONN->Insert_ID();
			}
			//如果已有則且 enable='0' 則重設為 enable='1'
			else if ($res->fields['enable'] == '0') {
				$id = $res->fields['id'];
				$query = "UPDATE health_insurance SET enable='1' WHERE id=".$id;
				$CONN->Execute($query) or die ($query);
			}
			// 如果重複 設id = 0, 放棄新增
			if ($id) {
				$student_sn = (int) $_POST['student_sn'];
				$no = count($update[$student_sn]['health_insurance_record']['id'])+1;
				$query = "INSERT INTO health_insurance_record (student_sn, no, id)
					 VALUES($student_sn, $no, $id)";
				$CONN->Execute($query) or die($query);
			}
		}
	}

	//取得學生臨時性檢查資料
	function get_exam() {
		global $CONN;

		if ($this->sn_str) {
			$query="select * from health_exam_record where student_sn in (".$this->sn_str.")";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$year_seme=sprintf("%03d",$res->fields['year']).$res->fields['semester'];
				$this->health_data[$res->fields['student_sn']][$year_seme][exam][$res->fields['id']][mdate]=$res->fields['measure_date'];
				$this->health_data[$res->fields['student_sn']][$year_seme][exam][$res->fields['id']][diag]=$res->fields['diag'];
				$this->health_data[$res->fields['student_sn']][$year_seme][exam][$res->fields['id']][dhos]=$res->fields['diag_hos'];
				$this->health_data[$res->fields['student_sn']][$year_seme][exam][$res->fields['id']][rediag]=$res->fields['rediag'];
				$res->MoveNext();
			}
		}
	}

	//更新學生臨時性檢查資料
	function update_exam() {
		global $CONN;
	}

	//取得學生定期檢查資料
	function get_checks($subject=""){
		global $CONN;

		if ($this->sn_str) {
			$subject_str=($subject)?" and subject='$subject'":"";
			$query="select * from health_checks_record where student_sn in (".$this->sn_str.") $subject_str";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$year_seme=sprintf("%03d",$res->fields[year]).$res->fields[semester];
				$no=$res->fields['no'];
				$st=$res->fields['status'];
				$sub=$res->fields['subject'];
				$sn=$res->fields['student_sn'];
				$this->health_data[$sn][$year_seme][checks][$sub][$no]=$st;
				//標記無異常部位
				if ($st==0 && $this->health_data[$sn][$year_seme]["Dis".$sub]=="") $this->health_data[$sn][$year_seme]["Dis".$sub]=0;
				//標記有異常部位
				if ($no>0 && $st>0) $this->health_data[$sn][$year_seme]["Dis".$sub]=1;
				//標記有附註項目
				if ($res->fields['ps']>0) $this->health_data[$sn][$year_seme]["PS".$sub.$no]=$res->fields['ps'];
				//標記已檢查部位
				$this->health_data[$sn][$year_seme]["chk".$sub]=1;
				$res->MoveNext();
			}
			$query="select distinct(concat(year,semester)) as y,year,semester from health_checks_record where student_sn in (".$this->sn_str.") $subject_str";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$this->checks_arr[sprintf("%04d",$res->fields['y'])]=1;
				$res->MoveNext();
			}			
		}
	}

	//更新學生定期檢查資料
	function update_checks($update=array()){
		global $CONN;

		while(list($sn,$v)=each($update['new'])) {
			reset($v);
			while(list($ys,$vv)=each($v)) {
				$year=intval(substr($ys,0,-1));
				$semester=substr($ys,-1,1);
				reset($vv);
				while(list($tbl_name,$vvv)=each($vv)) {
					reset($vvv);
					while(list($subject,$vvvv)=each($vvv)) {
						reset($vvvv);
						while(list($no,$value)=each($vvvv)) {
							if ($mode=="del") {
								//刪除資料
								$res=$CONN->Execute("delete from health_WH where student_sn='$sn' and year='$year' and semester='$semester'");
							} else {
								//如果是附屬資料, 則另外處理
								if (strlen($no)>2 && substr($no,0,2)=="PS") {
									$no=substr($no,2,strlen($no)-2);
									$query="select * from $tbl_name where student_sn='$sn' and year='$year' and semester='$semester' and subject='$subject' and no='$no'";
									$res=$CONN->Execute($query);
									if ($res->RecordCount()>0) {
										$query="update $tbl_name set ps='$value',teacher_sn='".$this->teacher_sn."' where student_sn='$sn' and year='$year' and semester='$semester' and subject='$subject' and no='$no'";
										$res=$CONN->Execute($query);
									}
									continue;
								}
								//如果是其他資料, 則另外處理
								if ($no=="99") continue;
								//更新資料
								$query="select * from $tbl_name where student_sn='$sn' and year='$year' and semester='$semester' and subject='$subject' and no='$no'";
								$res=$CONN->Execute($query);
								if ($res->RecordCount()>0) {
									if ($update['old'][$sn][$ys][$tbl_name][$subject][$no]!=$value) {
										if ($value=="0") {
											$query="delete from $tbl_name where student_sn='$sn' and year='$year' and semester='$semester' and subject='$subject' and no='$no'";
										} else {
											$query="update $tbl_name set status='$value',teacher_sn='".$this->teacher_sn."' where student_sn='$sn' and year='$year' and semester='$semester' and subject='$subject' and no='$no'";
										}
										$res=$CONN->Execute($query);
									}
								} else {
									if ($value!="") {
										$query="insert into $tbl_name (year,semester,student_sn,subject,no,status,teacher_sn) values ('$year','$semester','$sn','$subject','$no','$value','".$this->teacher_sn."')";
										$res=$CONN->Execute($query);
									}
								}
								//如果增加了任何一筆資料, 將「無異狀」資料刪除
								if ($value!="0") {
									$query="delete from $tbl_name where student_sn='$sn' and year='$year' and semester='$semester' and subject='$subject' and no='0'";
									$res=$CONN->Execute($query);
								}
							}
						}
					}
				}
			}
		}
	}

	//取得傷病資料
	function get_accident(){
		global $CONN;

		if ($this->sn_str) {
			$query="select * from health_accident_record where student_sn in (".$this->sn_str.") order by student_sn,year,semester,sign_time";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$temp_arr=array();
				$year_seme=sprintf("%03d",$res->fields[year]).$res->fields[semester];
				$sn=$res->fields[student_sn];
				$id=$res->fields['id'];
				$temp_arr[id]=$id;
				$temp_arr[sign_time]=$res->fields['sign_time'];
				$temp_arr[obs_min]=$res->fields['obs_min'];
				$temp_arr[temp]=$res->fields['temp'];
				$temp_arr[memo]=$res->fields['memo'];
				$temp_arr[place_id]=$res->fields['place_id'];
				$temp_arr[reason_id]=$res->fields['reason_id'];
				$query="select * from health_accident_part_record where id='$id'";
				$res2=$CONN->Execute($query);
				while(!$res2->EOF) {
					$temp_arr[part_id][]=$res2->fields['part_id'];
					$res2->MoveNext();
				}
				$query="select * from health_accident_status_record where id='$id'";
				$res2=$CONN->Execute($query);
				while(!$res2->EOF) {
					$temp_arr[status_id][]=$res2->fields['status_id'];
					$res2->MoveNext();
				}
				$query="select * from health_accident_attend_record where id='$id'";
				$res2=$CONN->Execute($query);
				while(!$res2->EOF) {
					$temp_arr[attend_id][]=$res2->fields['attend_id'];
					$res2->MoveNext();
				}
				$this->health_data[$sn][$year_seme][accident][]=$temp_arr;
				$res->MoveNext();
			}
		}
	}

	//更新傷病資料
	function update_accident($update=array()){
		global $CONN;

		if ($update['del'][0]) {
			while(list($i,$id)=each($update['del'])) {
				$temp_id[]=$id;
			}
			$temp_str="'".implode("','",$temp_id)."'";
			$CONN->Execute("delete from health_accident_record where id in ($temp_str)");
			$CONN->Execute("delete from health_accident_part_record where id in ($temp_str)");
			$CONN->Execute("delete from health_accident_status_record where id in ($temp_str)");
			$CONN->Execute("delete from health_accident_attend_record where id in ($temp_str)");
		} elseif (count($update['new'])>0) {
			while(list($sn,$v)=each($update['new'])) {
				reset($v);
				while(list($ys,$vv)=each($v)) {
					$year=intval(substr($ys,0,-1));
					$semester=substr($ys,-1,1);
					reset($vv);
					$aid=0;
					while(list($tbl_name,$vvv)=each($vv)) {
						switch($tbl_name) {
							case "health_accident_record":
								if ($vvv['sign_time']) {
									if ($vvv['update_id']) {
										$aid=$vvv['update_id'];
										$query="update $tbl_name set sign_time='".$vvv['sign_time']."',obs_min='".$vvv['obs_min']."',temp='".$vvv['temp']."',place_id='".$vvv['place_id']."',reason_id='".$vvv['reason_id']."',memo='".addslashes($vvv['memo'])."',teacher_sn='".$this->teacher_sn."' where id='$aid'";
										$CONN->Execute($query);
										$CONN->Execute("delete from health_accident_part_record where id='$aid'");
										$CONN->Execute("delete from health_accident_status_record where id='$aid'");
										$CONN->Execute("delete from health_accident_attend_record where id='$aid'");
									} else {
										$query="insert into $tbl_name (year,semester,student_sn,sign_time,obs_min,temp,place_id,reason_id,memo,teacher_sn) values ('$year','$semester','$sn','".$vvv['sign_time']."','".$vvv['obs_min']."','".$vvv['temp']."','".$vvv['place_id']."','".$vvv['reason_id']."','".addslashes($vvv['memo'])."','".$this->teacher_sn."')";
										$CONN->Execute($query);
										$aid=$CONN->Insert_ID();
									}
								}
								break;
							case "health_accident_part_record":
								if ($aid) {
									$CONN->Execute("delete from $tbl_name where id='$aid'");
									reset($vvv);
									$id_name="part_id";
									while(list($id,$vvvv)=each($vvv[$id_name])) {
										$CONN->Execute("insert into $tbl_name (id,$id_name) values ('$aid','$id')");
									}
								}
								break;
							case "health_accident_status_record":
								if ($aid) {
									$CONN->Execute("delete from $tbl_name where id='$aid'");
									reset($vvv);
									$id_name="status_id";
									while(list($id,$vvvv)=each($vvv[$id_name])) {
										$CONN->Execute("insert into $tbl_name (id,$id_name) values ('$aid','$id')");
									}
								}
								break;
							case "health_accident_attend_record":
								if ($aid) {
									$CONN->Execute("delete from $tbl_name where id='$aid'");
									reset($vvv);
									$id_name="attend_id";
									while(list($id,$vvvv)=each($vvv[$id_name])) {
										$CONN->Execute("insert into $tbl_name (id,$id_name) values ('$aid','$id')");
									}
								}
								break;
						}
					}
				}
			}
		}
	}

	//取得含氟水記錄
	function get_frecord() {
		global $CONN;

		if (count($this->sn_arr)>0) {
			$query="select * from health_frecord where student_sn in (".$this->sn_str.")";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$year_seme=sprintf("%03d",$res->fields['year']).$res->fields['semester'];
				$this->health_data[$res->fields['student_sn']][$year_seme]['frecord']=$res->FetchRow();
			}
		}
	}

	//更新含氟水記錄
	function update_frecord($update=array()) {
		global $CONN;

		while(list($sn,$v)=each($update['new'])) {
			while(list($ys,$vv)=each($v)) {
				$year=intval(substr($ys,0,-1));
				$semester=substr($ys,-1,1);
				while(list($tbl_name,$vvv)=each($vv)) {
					while(list($col_name,$value)=each($vvv)) {
						if ($value!=$update['old'][$sn][$ys][$tbl_name][$col_name]) {
							$query="select * from $tbl_name where year='$year' and semester='$semester' and student_sn='$sn'";
							$res=$CONN->Execute($query);
							if ($res->RecordCount())
							$query="update $tbl_name set $col_name='$value' where year='$year' and semester='$semester' and student_sn='$sn'";
							else
							$query="insert into $tbl_name (year,semester,student_sn,$col_name) values ('$year','$semester','$sn','$value')";
							$res=$CONN->Execute($query);
							if ($col_name=="agree" && $value==0) {
								$query="";
								for($i=1;$i<=25;$i++) $query.="w$i='',";
								$query="update $tbl_name set ".substr($query,0,-1)."where year='$year' and semester='$semester' and student_sn='$sn'";
								$CONN->Execute($query);
							}
						}
					}
				}
			}
		}
	}

	//取得健檢醫院醫師資料
	function get_checks_doctor() {
		global $CONN;

		//與頭頸一同檢查的科別
		$temp_arr=array("Pul","Dig","Spi");
		if (count($this->sn_arr)>0) {
			$query="select * from health_checks_doctor where student_sn in (".$this->sn_str.")";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$year_seme=sprintf("%03d",$res->fields[year]).$res->fields[semester];
				$this->health_data[$res->fields[student_sn]][$year_seme][checks][$res->fields[subject]][hospital]=$res->fields[hospital];
				$this->health_data[$res->fields[student_sn]][$year_seme][checks][$res->fields[subject]][doctor]=$res->fields[doctor];
				$this->health_data[$res->fields[student_sn]][$year_seme][checks][$res->fields[subject]][date]=$res->fields[measure_date];
				//如果是頭頸科的話, 順便把記錄加入一同檢查的科別
				if ($res->fields[subject]=="Hea") {
					foreach($temp_arr as $d) {
						$this->health_data[$res->fields[student_sn]][$year_seme][checks][$d][hospital]=$res->fields[hospital];
						$this->health_data[$res->fields[student_sn]][$year_seme][checks][$d][doctor]=$res->fields[doctor];
						$this->health_data[$res->fields[student_sn]][$year_seme][checks][$d][date]=$res->fields[measure_date];
					}
				}
				$res->MoveNext();
			}
		}
	}

	//更新健檢醫院醫師資料
	function update_checks_doctor($y="",$s="",$h="",$d="",$m="",$a=array()) {
		global $CONN;

		$y=intval($y);
		$s=intval($s);
		$h=trim($h);
		$d=trim($d);
		$m=trim($m);
		if ($y && $s && $h && $d && $m && $a) {
			$s_arr=array();
			$query="select distinct subject from health_checks_item";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$s_arr[]=$res->fields['subject'];
				$res->MoveNext();
			}
			if (count($a)>0 && count($this->sn_arr)>0) {
				while(list($k,$v)=each($a)) {
					if (in_array($v,$s_arr)) {
						reset($this->sn_arr);
						while(list($kk,$sn)=each($this->sn_arr)) {
							$c=substr($this->stud_base[$sn]['seme_class'],0,-2);
							$query="select * from health_checks_doctor where year='$y' and semester='$s' and student_sn='$sn' and subject='$v'";
							$res=$CONN->Execute($query);
							if ($res->RecordCount()>0) {
								$query="update health_checks_doctor set hospital='$h',doctor='$d',measure_date='$m',cyear='$c' where year='$y' and semester='$s' and student_sn='$sn' and subject='$v'";
							} else {
								$query="insert into health_checks_doctor (year,semester,student_sn,subject,hospital,doctor,measure_date,cyear) values ('$y','$s','$sn','$v','$h','$d','$m','$c')";
							}
							$res=$CONN->Execute($query);
						}
					}
				}
			}
		}
	}

	//取得所有健康資料
	function get_all() {
		global $CONN;

		if (count($this->sn_arr)>0) {
			$this->get_wh();
			$this->get_sight();
			$this->get_teeth();
			$this->get_checks();
			$this->get_checks_doctor();
			$this->get_disease();
			$this->get_ntu();
			$this->get_bodymind();
			$this->get_insurance();
			$this->get_worm();
			$this->get_uri();
			$this->get_hospital();
		}
	}
}
?>
