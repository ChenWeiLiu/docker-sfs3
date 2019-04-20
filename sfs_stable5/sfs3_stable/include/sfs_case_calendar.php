<?php
// $Id: sfs_case_calendar.php 5310 2009-01-10 07:57:56Z hami $
// PHP Calendar Class Version 1.4 (5th March 2001)
//  
// Copyright David Wilkinson 2000 - 2001. All Rights reserved.
// 
// This software may be used, modified and distributed freely
// providing this copyright notice remains intact at the head 
// of the file.
//
// This software is freeware. The author accepts no liability for
// any loss or damages whatsoever incurred directly or indirectly 
// from the use of this script. The author of this software makes 
// no claims as to its fitness for any purpose whatsoever. If you 
// wish to use this software you should first satisfy yourself that 
// it meets your requirements.
//
// URL:   http://www.cascade.org.uk/software/php/calendar/
// Email: davidw@cascade.org.uk
// 修改： tad@www.tnc.edu.tw

class Calendar
{
    var  $linkStr="";
	/*
        Constructor for the Calendar class
    */
    function Calendar()
    {
    }
    
    
    /*
        Get the array of strings used to label the days of the week. This array contains seven 
        elements, one for each day of the week. The first entry in this array represents Sunday. 
    */
    function getDayNames()
    {
        return $this->dayNames;
    }
    

    /*
        Set the array of strings used to label the days of the week. This array must contain seven 
        elements, one for each day of the week. The first entry in this array represents Sunday. 
    */
    function setDayNames($names)
    {
        $this->dayNames = $names;
    }
    
    /*
        Get the array of strings used to label the months of the year. This array contains twelve 
        elements, one for each month of the year. The first entry in this array represents January. 
    */
    function getMonthNames()
    {
        return $this->monthNames;
    }
    
    /*
        Set the array of strings used to label the months of the year. This array must contain twelve 
        elements, one for each month of the year. The first entry in this array represents January. 
    */
    function setMonthNames($names)
    {
        $this->monthNames = $names;
    }
    
    
    
    /* 
        Gets the start day of the week. This is the day that appears in the first column
        of the calendar. Sunday = 0.
    */
      function getStartDay()
    {
        return $this->startDay;
    }
    
    /* 
        Sets the start day of the week. This is the day that appears in the first column
        of the calendar. Sunday = 0.
    */
    function setStartDay($day)
    {
        $this->startDay = $day;
    }
    
    
    /* 
        Gets the start month of the year. This is the month that appears first in the year
        view. January = 1.
    */
    function getStartMonth()
    {
        return $this->startMonth;
    }
    
    /* 
        Sets the start month of the year. This is the month that appears first in the year
        view. January = 1.
    */
    function setStartMonth($month)
    {
        $this->startMonth = $month;
    }
    
    
    /*
        Return the URL to link to in order to display a calendar for a given month/year.
        You must override this method if you want to activate the "forward" and "back" 
        feature of the calendar.
        
        Note: If you return an empty string from this function, no navigation link will
        be displayed. This is the default behaviour.
        
        If the calendar is being displayed in "year" view, $month will be set to zero.
    */
    function getCalendarLink($month, $year)
    {
        return "";
    }
    
    /*
        Return the URL to link to  for a given date.
        You must override this method if you want to activate the date linking
        feature of the calendar.
        
        Note: If you return an empty string from this function, no navigation link will
        be displayed. This is the default behaviour.
    */
    function getDateLink($day, $month, $year)
    {
        return "";
    }


    /*
        Return the HTML for the current month
    */
    function getCurrentMonthView()
    {
        $d = getdate(time());
        return $this->getMonthView($d["mon"], $d["year"]);
    }
    

    /*
        Return the HTML for the current year
    */
    function getCurrentYearView()
    {
        $d = getdate(time());
        return $this->getYearView($d["year"]);
    }
    
    
    /*
        Return the HTML for a specified month
    
    function getMonthView($month, $year)
    {
        return $this->getMonthHTML($month, $year);
    }
	*/
	
	function getMonthView($month, $year , $day="")
    {        
		return $this->getMonthHTML($month, $year ,1, $day);
    }
	
	
	function getMonthView_with_stud_id($month, $year , $day="",$stud_id="")
    {        
		return $this->getMonthHTML_with_stud_id($month, $year ,1, $day,$stud_id);
    }   

    /*
        Return the HTML for a specified year
    */
    function getYearView($year)
    {
        return $this->getYearHTML($year);
    }
    
    
    
    /********************************************************************************
    
        The rest are private methods. No user-servicable parts inside.
        
        You shouldn't need to call any of these functions directly.
        
    *********************************************************************************/


    //計算天數
    function getDaysInMonth($month, $year){
        $d=date ("t", mktime(0,0,0,$month,1,$year));    
        return $d;
    }


    /*
        Generate the HTML for a given month
    */
    //function getMonthHTML($m, $y, $showYear = 1)
	function getMonthHTML($m, $y, $showYear = 1 ,$myd="")
    {
        $s = "";
        
        $a = $this->adjustDate($m, $y);
        $month = $a[0];
        $year = $a[1];        
        
    	$daysInMonth = $this->getDaysInMonth($month, $year);
    	$date = getdate(mktime(12, 0, 0, $month, 1, $year));
    	
    	$first = $date["wday"];
    	$monthName = $this->monthNames[$month - 1];
    	
    	$prev = $this->adjustDate($month - 1, $year);
    	$next = $this->adjustDate($month + 1, $year);
    	
    	if ($showYear == 1)
    	{
    	    $prevMonth = $this->getCalendarLink($prev[0], $prev[1]);
    	    $nextMonth = $this->getCalendarLink($next[0], $next[1]);
    	}
    	else
    	{
    	    $prevMonth = "";
    	    $nextMonth = "";
    	}
    	
    	$header = $monthName . (($showYear > 0) ? " " . $year : "");
    	
    	$s .= "<table class=\"calendar\" cellspacing='2' cellpadding=\"2\">\n";
    	$s .= "<tr>\n";
    	$s .= "<td align=\"center\" valign=\"top\">" . (($prevMonth == "") ? "&nbsp;" : "<a href=\"$prevMonth".$this->linkStr."\">&lt;&lt;</a>")  . "</td>\n";
    	$s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\" colspan=\"5\">$header</td>\n"; 
    	$s .= "<td align=\"center\" valign=\"top\">" . (($nextMonth == "") ? "&nbsp;" : "<a href=\"$nextMonth".$this->linkStr."\">&gt;&gt;</a>")  . "</td>\n";
    	$s .= "</tr>\n";
    	//星期列
    	$s .= "<tr>\n";
    	$s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\">" . $this->dayNames[($this->startDay)%7] . "</td>\n";
    	$s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\">" . $this->dayNames[($this->startDay+1)%7] . "</td>\n";
    	$s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\">" . $this->dayNames[($this->startDay+2)%7] . "</td>\n";
    	$s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\">" . $this->dayNames[($this->startDay+3)%7] . "</td>\n";
    	$s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\">" . $this->dayNames[($this->startDay+4)%7] . "</td>\n";
    	$s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\">" . $this->dayNames[($this->startDay+5)%7] . "</td>\n";
    	$s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\">" . $this->dayNames[($this->startDay+6)%7] . "</td>\n";
    	$s .= "</tr>\n";
    	
    	// We need to work out what date to start at so that the first appears in the correct column
    	$d = $this->startDay + 1 - $first;
    	while ($d > 1)
    	{
    	    $d -= 7;
    	}

        // Make sure we know when today is, so that we can use a different CSS style
        $today = getdate(time());
    	
    	while ($d <= $daysInMonth)
    	{
    	    $s .= "<tr>\n";       
    	    
    	    for ($i = 0; $i < 7; $i++)
    	    {
        	    //$class = ($year == $today["year"] && $month == $today["mon"] && $d == $today["mday"]) ? "calendarToday" : "calendar";
				
				if($year == $today["year"] && $month == $today["mon"] && $d == $today["mday"]){
					$class ="calendarToday";
				}elseif($year == $y && $month == $m && $d == $myd && !empty($myd)){
					$class ="calendarTheday";			
				}else{
					$class ="calendar";	
				}
				
    	        $s .= "<td class=\"$class\" align=\"right\" valign=\"top\">";       
    	        if ($d > 0 && $d <= $daysInMonth)
    	        {
	    	        $link = $this->getDateLink($d, $month, $year);
    	            $s .= (($link == "") ? $d : "<a href=\"$link\">$d</a>");
    	        }
    	        else
    	        {
    	            $s .= "&nbsp;";
    	        }
      	        $s .= "</td>\n";       
        	    $d++;
    	    }
    	    $s .= "</tr>\n";    
    	}
    	
    	$s .= "</table>\n";
    	
    	return $s;  	
    }
    
	function getMonthHTML_with_stud_id($m, $y, $showYear = 1 ,$myd="",$stud_id="")
    {
        $s = "";
        
        $a = $this->adjustDate($m, $y);
        $month = $a[0];
        $year = $a[1];        
        
    	$daysInMonth = $this->getDaysInMonth($month, $year);
    	$date = getdate(mktime(12, 0, 0, $month, 1, $year));
    	
    	$first = $date["wday"];
    	$monthName = $this->monthNames[$month - 1];
    	
    	$prev = $this->adjustDate($month - 1, $year);
    	$next = $this->adjustDate($month + 1, $year);
    	
    	if ($showYear == 1)
    	{
    	    $prevMonth = $this->getCalendarLink($prev[0], $prev[1]);
    	    $nextMonth = $this->getCalendarLink($next[0], $next[1]);
    	}
    	else
    	{
    	    $prevMonth = "";
    	    $nextMonth = "";
    	}
    	
    	$header = $monthName . (($showYear > 0) ? " " . $year : "");
    	
    	$s .= "<table class=\"calendar\" cellspacing='2' cellpadding=\"2\">\n";
    	$s .= "<tr>\n";
    	$s .= "<td align=\"center\" valign=\"top\">" . (($prevMonth == "") ? "&nbsp;" : "<a href=\"$prevMonth".$this->linkStr."\">&lt;&lt;</a>")  . "</td>\n";
    	$s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\" colspan=\"5\">$header</td>\n"; 
    	$s .= "<td align=\"center\" valign=\"top\">" . (($nextMonth == "") ? "&nbsp;" : "<a href=\"$nextMonth".$this->linkStr."\">&gt;&gt;</a>")  . "</td>\n";
    	$s .= "</tr>\n";
    	//星期列
    	$s .= "<tr>\n";
    	$s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\">" . $this->dayNames[($this->startDay)%7] . "</td>\n";
    	$s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\">" . $this->dayNames[($this->startDay+1)%7] . "</td>\n";
    	$s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\">" . $this->dayNames[($this->startDay+2)%7] . "</td>\n";
    	$s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\">" . $this->dayNames[($this->startDay+3)%7] . "</td>\n";
    	$s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\">" . $this->dayNames[($this->startDay+4)%7] . "</td>\n";
    	$s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\">" . $this->dayNames[($this->startDay+5)%7] . "</td>\n";
    	$s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\">" . $this->dayNames[($this->startDay+6)%7] . "</td>\n";
    	$s .= "</tr>\n";
    	
    	// We need to work out what date to start at so that the first appears in the correct column
    	$d = $this->startDay + 1 - $first;
    	while ($d > 1)
    	{
    	    $d -= 7;
    	}

        // Make sure we know when today is, so that we can use a different CSS style
        $today = getdate(time());
    	
    	while ($d <= $daysInMonth)
    	{
    	    $s .= "<tr>\n";       
    	    
    	    for ($i = 0; $i < 7; $i++)
    	    {
        	    //$class = ($year == $today["year"] && $month == $today["mon"] && $d == $today["mday"]) ? "calendarToday" : "calendar";
				
				if($year == $today["year"] && $month == $today["mon"] && $d == $today["mday"]){
					$class ="calendarToday";
				}elseif($year == $y && $month == $m && $d == $myd && !empty($myd)){
					$class ="calendarTheday";			
				}else{
					$class ="calendar";	
				}
				
    	        $s .= "<td class=\"$class\" align=\"right\" valign=\"top\">";       
    	        if ($d > 0 && $d <= $daysInMonth)
    	        {
	    	        $link = $this->getDateLink_with_stud_id($d, $month, $year,$stud_id);
    	            $s .= (($link == "") ? $d : "<a href=\"$link\">$d</a>");
    	        }
    	        else
    	        {
    	            $s .= "&nbsp;";
    	        }
      	        $s .= "</td>\n";       
        	    $d++;
    	    }
    	    $s .= "</tr>\n";    
    	}
    	
    	$s .= "</table>\n";
    	
    	return $s;  	
    }    
    /*
        Generate the HTML for a given year
    */
    function getYearHTML($year)
    {
        $s = "";
    	$prev = $this->getCalendarLink(0, $year - 1);
    	$next = $this->getCalendarLink(0, $year + 1);
        
        $s .= "<table border=\"0\" cellspacing=\"1\" cellpadding=\"5\" class=\"calendar\">\n";
        $s .= "<tr bgcolor=\"#E9FCE0\">";
    	$s .= "<td align=\"center\" valign=\"top\" align=\"left\">" . (($prev == "") ? "&nbsp;" : "<a href=\"$prev\">&lt;&lt;</a>")  . "</td>\n";
        $s .= "<td class=\"calendarHeader\" valign=\"top\" align=\"center\">" . (($this->startMonth > 1) ? $year . " - " . ($year + 1) : $year) ."</td>\n";
    	$s .= "<td align=\"center\" valign=\"top\" align=\"right\">" . (($next == "") ? "&nbsp;" : "<a href=\"$next\">&gt;&gt;</a>")  . "</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(0 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(1 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(2 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(3 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(4 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(5 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(6 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(7 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(8 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(9 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(10 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(11 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "</tr>\n";
        $s .= "</table>\n";
        
        return $s;
    }

    /*
        Adjust dates to allow months > 12 and < 0. Just adjust the years appropriately.
        e.g. Month 14 of the year 2001 is actually month 2 of year 2002.
    */
    function adjustDate($month, $year)
    {
        $a = array();  
        $a[0] = $month;
        $a[1] = $year;
        
        while ($a[0] > 12)
        {
            $a[0] -= 12;
            $a[1]++;
        }
        
        while ($a[0] <= 0)
        {
            $a[0] += 12;
            $a[1]--;
        }
        
        return $a;
    }

    /* 
        The start day of the week. This is the day that appears in the first column
        of the calendar. Sunday = 0.
    */
    var $startDay = 0;

    /* 
        The start month of the year. This is the month that appears in the first slot
        of the calendar in the year view. January = 1.
    */
    var $startMonth = 1;

    /*
        The labels to display for the days of the week. The first entry in this array
        represents Sunday.
	var $dayNames = array("S", "M", "T", "W", "T", "F", "S");
    */
    
    var $dayNames = array("日", "一", "二", "三", "四", "五", "六");
    /*
        The labels to display for the months of the year. The first entry in this array
        represents January.
    
    var $monthNames = array("January", "February", "March", "April", "May", "June",
                            "July", "August", "September", "October", "November", "December");
	*/
	var $monthNames = array("一月", "二月", "三月", "四月", "五月", "六月",
                            "七月", "八月", "九月", "十月", "十一月", "十二月");                            
                            
    /*
        The number of days in each month. You're unlikely to want to change this...
        The first entry in this array represents January.
    */
    var $daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    
}

//加入上下月瀏覽，以及日期連結
class MyCalendar extends Calendar
{
    function getCalendarLink($month, $year)
    {
        // Redisplay the current page, but with some parameters
        // to set the new month and year
        $s = getenv('SCRIPT_NAME');
        return "$s?month=$month&year=$year";
    }
	
	function getDateLink($day, $month, $year)
    {
	    // Only link the first day of every month 
        $s = getenv('SCRIPT_NAME');
        
        $this_date=date ("Y-m-d", mktime(0,0,0,$month,$day,$year));
		$link = "$s?act=$_REQUEST[act]&this_date=$this_date".$this->linkStr;
        
        return $link;
    }
	function getDateLink_with_stud_id($day, $month, $year,$stud_id)
    {
	    // Only link the first day of every month 
        $s = getenv('SCRIPT_NAME');
        
        $this_date=date ("Y-m-d", mktime(0,0,0,$month,$day,$year));
		$link = "$s?act=$_REQUEST[act]&this_date=$this_date&stud_id=$stud_id".$this->linkStr;
        
        return $link;
    }	
	
	//事件行事曆
	 function getMonthThingView($month, $year , $day="")
    {
        return $this->getMonthThingHTML($month, $year ,1, $day);
    }
	
	function getThingDateLink($day, $month, $year)
    {
        // Only link the first day of every month 
        $s = getenv('SCRIPT_NAME');
		$link = "$s?this_date=$year-$month-$day";
        
        return $link;
    }
	
	//列出一個月的行事曆，含事件
	function getMonthThingHTML($m, $y, $showYear = 1 ,$myd=""){
		$s = "";
	       
		$a = $this->adjustDate($m, $y);
		$month = $a[0];
		$year = $a[1];
	       
	   	$daysInMonth = $this->getDaysInMonth($month, $year);
	   	$date = getdate(mktime(12, 0, 0, $month, 1, $year));
	   	
	   	$first = $date["wday"];
	   	$monthName = $this->monthNames[$month - 1];
	   	
	   	$prev = $this->adjustDate($month - 1, $year);
	   	$next = $this->adjustDate($month + 1, $year);
	   	
	   	if ($showYear == 1)
	   	{
	   	    $prevMonth = $this->getCalendarLink($prev[0], $prev[1]);
	   	    $nextMonth = $this->getCalendarLink($next[0], $next[1]);
	   	}
	   	else
	   	{
	   	    $prevMonth = "";
	   	    $nextMonth = "";
	   	}
	   	
	   	$header = $monthName . (($showYear > 0) ? " " . $year : "");
	   	
	   	$s .= "<table cellspacing='1' cellpadding='3' bgcolor='#C0C0C0'>\n";
	   	$s .= "<tr bgcolor='#FFEEE6' class='calendarTr'>\n";
	   	$s .= "<td align='center' valign='top'>" . (($prevMonth == "") ? "&nbsp;" : "<a href='$prevMonth&day=$myd&act=$_GET[act]'>&lt;&lt;</a>")  . "</td>\n";
	   	$s .= "<td align='center' valign='top' colspan='5'>$header</td>\n"; 
	   	$s .= "<td align='center' valign='top'>" . (($nextMonth == "") ? "&nbsp;" : "<a href='$nextMonth&day=$myd&act=$_GET[act]'>&gt;&gt;</a>")  . "</td>\n";
	   	$s .= "</tr>\n";
	   	
	   	$s .= "<tr bgcolor='#ffffff'>\n";
	   	$s .= "<td align='center' valign='top' class='calendarHeader'>" . $this->dayNames[($this->startDay)%7] . "</td>\n";
	   	$s .= "<td align='center' valign='top' class='calendarHeader'>" . $this->dayNames[($this->startDay+1)%7] . "</td>\n";
	   	$s .= "<td align='center' valign='top' class='calendarHeader'>" . $this->dayNames[($this->startDay+2)%7] . "</td>\n";
	   	$s .= "<td align='center' valign='top' class='calendarHeader'>" . $this->dayNames[($this->startDay+3)%7] . "</td>\n";
	   	$s .= "<td align='center' valign='top' class='calendarHeader'>" . $this->dayNames[($this->startDay+4)%7] . "</td>\n";
	   	$s .= "<td align='center' valign='top' class='calendarHeader'>" . $this->dayNames[($this->startDay+5)%7] . "</td>\n";
	   	$s .= "<td align='center' valign='top' class='calendarHeader'>" . $this->dayNames[($this->startDay+6)%7] . "</td>\n";
	   	$s .= "</tr>\n";
	   	
	   	// We need to work out what date to start at so that the first appears in the correct column
	   	$d = $this->startDay + 1 - $first;
	   	while ($d > 1){
	   	    $d -= 7;
	   	}
	
	       // Make sure we know when today is, so that we can use a different CSS style
	       $today = getdate(time());
	   	
	   	while ($d <= $daysInMonth){
	   	    $s .= "<tr bgcolor='#FFFFFF'>\n";       
	   	    
	   	    for ($i = 0; $i < 7; $i++){
				if($year == $today["year"] && $month == $today["mon"] && $d == $today["mday"]){
					$class ="calendarToday";
				}elseif($year == $y && $month == $m && $d == $myd && !empty($myd)){
					$class ="calendarTheday";			
				}else{
					$class ="calendar";	
				}
				
				$thing=getSimpleThing($year,$month,$d);
				
	   	        $s .= "<td width='100' height='100' valign='top' class='$class'>";       
	   	        if ($d > 0 && $d <= $daysInMonth){
	   	            $link = $this->getThingDateLink($d, $month, $year);
	   	            $s .= (($link == "") ? $d : "<a href='$link' class='box'>$d $thing</a>");
	   	        }else{
	   	            $s .= "&nbsp;";
	   	        }
	     	        $s .= "</td>\n";       
	       	    $d++;
	   	    }
	   	    $s .= "</tr>\n";    
	   	}
	   	
		$s .= "</table>\n";
	   	
		return $s;  	
	}
}


?>
