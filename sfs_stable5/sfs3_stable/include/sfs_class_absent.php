<?php
// $Id: sfs_class_absent.php 5310 2009-01-10 07:57:56Z hami $

class absent
{
	var $mode;//請假人類別(teacher/student)
	var $year;//請假學年
	var $seme;//請假學期
	var $sn;//請假人流水號
	var $agent_sn;//代理人流水號
	var $id;//請假資料流水號
	var $err_msg;//錯誤訊息
	var $month;//月份別
	var $reason;//請假事由
	var $start_date;//請假起始日期時間
	var $end_date;//請假結束日期時間
	var $abs_kind;//請假別
	var $class_dis;//課程處理狀態
	var $agent_status;//處理狀態
	var $absent_kind_arr=array();//假別陣列
	function absent($mode)
	{
		if ($mode=="teacher" || $mode=="student") {
			$this->mode=$mode;
			include_once "sfs_case_dataarray.php";
			include_once "sfs_case_absent.php";
			if ($mode=="teacher") {
				$this->absent_kind_arr=tea_abs_kind();
			} else {
				$this->absent_kind_arr=stud_abs_kind();
			}
		} else {
			if (empty($mode)) {
				$this->err_msg="未指定請假人員類別";
			} else {
				$this->err_msg="請假人員類別錯誤";
			}
		}
	}

	function set_year($year)
	{
		if (!empty($year)) $this->year=$year;
	}

	function set_seme($seme)
	{
		if (!empty($seme)) $this->seme=$seme;
	}

	function set_id($id)
	{
		if (!empty($id)) $this->id=$id;
	}

	function set_sn($sn)
	{
		if (!empty($sn)) $this->sn=$sn;
	}

	function set_abs_kind($kind)
	{
		if (!empty($kind)) $this->abs_kind=$kind;
	}

	function set_reason($reason)
	{
		if (!empty($reason)) $this->reason=$reason;
	}

	function set_date($start_date,$end_date)
	{
		$s=explode(" ",$start_date);
		$sd=explode("-",$s[0]);
		$st=explode(":",$s[1]);
		$e=explode(" ",$end_date);
		$ed=explode("-",$e[0]);
		$et=explode(":",$e[1]);
		$this->month=intval($sd[1]);
		$this->start_date=date("Y-m-d H:i:s",mktime($st[0],$st[1],0,$sd[1],$sd[2],$sd[0]));
		$this->end_date=date("Y-m-d H:i:s",mktime($et[0],$et[1],0,$ed[1],$ed[2],$ed[0]));
	}

	function set_agent($sn)
	{
		if (!empty($sn)) $this->agent_sn=$sn;
	}

	function set_class_dis($class_dis)
	{
		if (!empty($class_dis)) $this->class_dis=$class_dis;
	}

	function set_status($status)
	{
		if (!empty($status)) $this->agent_status=$status;
	}

	function add_absent()
	{
		add_absent($this->mode,$this->sn,$this->year,$this->seme,$this->month,$this->abs_kind,$this->reason,$this->start_date,$this->end_date,$this->agent_sn);
	}

	function del_absent()
	{
		if ($this->id) {
			del_absent($this->mode,$this->id);
		} else {
			$this->err_msg="未指定資料流水號";
		}
	}

	function modify_absent()
	{
		if ($this->id) {
			modify_absent($this->mode,$this->id,$this->sn,$this->year,$this->seme,$this->month,$this->abs_kind,$this->reason,$this->start_date,$this->end_date,$this->agent_sn,$this->class_dis,$this->agent_status);
		} else {
			$this->err_msg="未指定資料流水號";
		}
	}
}
?>