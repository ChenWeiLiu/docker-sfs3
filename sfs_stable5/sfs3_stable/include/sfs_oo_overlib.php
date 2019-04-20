<?php

// $Id: sfs_oo_overlib.php 8130 2014-09-23 07:56:38Z smallduh $
// ¨ú¥N class.overlib.php

    /* 
	This is version 1.11 of class.overlib for php (http://www.php.net) 
	written 1999, 2000, 2001 Patrick Hess <hess@dland.de>
	This software is distributed under GPL.
	overLib is from Eric Bosrup (http://www.bosrup.com/web/overlib/)
	This class is just a driver/container, so most of this wonderful
	work is done by Eric Bosrup! Keep this in mind...
    */

	class Overlib {
	  var $ol_path          = "include";
	  var $ol_sticky        = false;
	  var $ol_align	        = 0;
	  var $ol_valign        = 0;
	  var $ol_fgcolor       = "#fcfcfc";
	  var $ol_bgcolor       = "#0080C0";
	  var $ol_capcolor      = "#ffffff";
	  var $ol_textcolor     = "";
	  var $ol_closecolor    = "";
	  var $ol_textfont      = "";
	  var $ol_captionfont   = "";
	  var $ol_closefont     = "";
	  var $ol_textsize      = 0;
	  var $ol_captionsize   = 0;
	  var $ol_closesize     = 0;
	  var $ol_height        = 0;
	  var $ol_width         = 0;
	  var $ol_border        = 3;
	  var $ol_offsetx       = 0;
	  var $ol_offsety       = 0;
	  var $ol_fgbackground  = "";
	  var $ol_bgbackground  = "";
	  var $ol_closetext     = "Close";
	  var $ol_close         = true;
	  var $ol_noclosetext   = false;
	  var $ol_autostatus    = false;
	  var $ol_autostatuscap = false;
	  var $ol_capicon       = "images/forum/question.gif";
	  var $ol_snapx         = 0;
	  var $ol_snapy         = 0;
	  var $ol_padxl         = 0;
	  var $ol_padxr         = 0;
	  var $ol_padyt         = 0;
	  var $ol_padyb         = 0;
	  var $ol_fixy          = 0;
	  var $ol_background    = "";
	  var $ol_fullhtml      = false;
	  var $ol_timeout	= -1;
	  var $ol_delay		= -1;
	  var $ol_vauto         = false;
	  var $ol_hauto         = false;

	  function overLib($path = "") {
	  	if (strlen($path)) $this->ol_path = $path;
?>
<nolink rel='stylesheet' href=<?php echo "'$this->ol_path/overlib.css' "; ?> 
      type='text/css'>
<div id='overDiv' style='position:absolute; visibility:hide; z-index: 1000;'>
</div>
<script language='javascript' src=<?php echo "'$this->ol_path/sfs_script_overlib.js'"; ?>>
</script>
<?php
	  }

	  function set($var, $value) {
		$v = "ol_$var";
		$this->$v = $value;
	  }

	  function get($var) {
		$v = "ol_$var";
		return($this->$v);
	  }

	  function over($text, $title = "", $status = "")
	  {
	    $cmd = "'$text'";

	    if(strlen($title)) 
		$cmd .= ", CAPTION, '$title'";

	    if(strlen($status)) 
		$cmd .= ", STATUS, '$status'";

	    if($this->ol_sticky)	
		$cmd .= ", STICKY";

	    if($this->ol_align) {
		switch($this->ol_align) {
		    case 1: $cmd .= ", LEFT";	break;
		    case 2: $cmd .= ", CENTER";	break;
		    case 3: $cmd .= ", RIGHT";	break;
		    default: 			break;
		}
	    }

	    if($this->ol_valign) {
		switch($this->ol_valign) {
		    case 1: $cmd .= ", ABOVE";	break;
		    case 2: $cmd .= ", BELOW";	break;
		    default: 			break;
		}
	    }

	    if (strlen($this->ol_fgbackground)) {
		$cmd .= ", FGCOLOR, '', FGBACKGROUND, '$this->ol_fgbackground'";
	    } else {
	    	if (strlen($this->ol_fgcolor))
			$cmd .= ", FGCOLOR, '$this->ol_fgcolor'";
	    }

	    if (strlen($this->ol_bgbackground)) {
		$cmd .= ", BGCOLOR, '', BGBACKGROUND, '$this->ol_bgbackground'";
	    } else {
	    	if (strlen($this->ol_bgcolor))
			$cmd .= ", BGCOLOR, '$this->ol_bgcolor'";
	    }

	    if (strlen($this->ol_capcolor))
		$cmd .= ", CAPCOLOR, '$this->ol_capcolor'";

	    if (strlen($this->ol_textcolor))
		$cmd .= ", TEXTCOLOR, '$this->ol_textcolor'";

	    if (strlen($this->ol_closecolor))
		$cmd .= ", CLOSECOLOR, '$this->ol_closecolor'";

	    if (strlen($this->ol_textfont))
		$cmd .= ", TEXTFONT, '$this->ol_textfont'";

	    if (strlen($this->ol_captionfont))
		$cmd .= ", CAPTIONFONT, '$this->ol_captionfont'";

	    if (strlen($this->ol_closefont))
		$cmd .= ", CLOSEFONT, '$this->ol_closefont'";

	    if ($this->ol_textsize)
		$cmd .= ", TEXTSIZE, $this->ol_textsize";

	    if ($this->ol_captionsize)
		$cmd .= ", CAPTIONSIZE, $this->ol_captionsize";

	    if ($this->ol_closesize)
		$cmd .= ", CLOSESIZE, $this->ol_closesize";

	    if ($this->ol_width)
		$cmd .= ", WIDTH, $this->ol_width";

	    if ($this->ol_height)
		$cmd .= ", HEIGHT, $this->ol_height";

	    if ($this->ol_border >= 0)
		$cmd .= ", BORDER, $this->ol_border";

	    if ($this->ol_offsetx)
		$cmd .= ", OFFSETX, $this->ol_offsetx";

	    if ($this->ol_offsety)
		$cmd .= ", OFFSETY, $this->ol_offsety";

	    if (strlen($this->ol_closetext))
		$cmd .= ", CLOSETEXT, '$this->ol_closetext'";

	    if ($this->ol_noclose)
		$cmd .= ", NOCLOSETEXT";

	    if ($this->ol_autostatus)
		$cmd .= ", AUTOSTATUS";

	    if ($this->ol_autostatuscap)
		$cmd .= ", AUTOSTATUSCAP";

	    if (strlen($this->ol_capicon))
		$cmd .= ", CAPICON, '$this->ol_capicon'";

	    if ($this->ol_snapx)
		$cmd .= ", SNAPX, $this->ol_snapx";

	    if ($this->ol_snapy)
		$cmd .= ", SNAPY, $this->ol_snapy";

	    if ($this->ol_fixy)
		$cmd .= ", FIXY, $this->ol_fixy";

	    if ($this->ol_padxl || $this->ol_padxr)
		$cmd .= ", PADX, $this->ol_padxl, $this->ol_padxr";

	    if ($this->ol_padyt || $this->ol_padyb)
		$cmd .= ", PADY, $this->ol_padyt, $this->ol_padyb";

	    if (strlen($this->ol_background))
		$cmd .= ", BACKGROUND, '$this->ol_background'";

	    if ($this->ol_fullhtml)
		$cmd .= ", FULLHTML";

	    if ($this->ol_timeout >= 0)
		$cmd .= ", TIMEOUT, $this->ol_timeout";

	    if ($this->ol_delay >= 0)
		$cmd .= ", DELAY, $this->ol_delay";

	    if ($this->ol_hauto) {
		$cmd .= ", HAUTO";
		$this->ol_hauto = false;
	    }

	    if ($this->ol_vauto) {
		$cmd .= ", VAUTO";
		$this->ol_hauto = false;
	    }

	    $output=" onMouseOver=\"return overlib($cmd);\" ";
	    $output.=" onMouseOut=\"nd();\" ";

	    return ($output);
	  }

	  function pover ($text, $title = "", $status = "") 
	  {
	    echo $this->over($text, $title, $status);
	  }
	}
?>
