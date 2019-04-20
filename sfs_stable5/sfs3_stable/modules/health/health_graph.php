<?php
//$Id: health_graph.php 5310 2009-01-10 07:57:56Z hami $
include_once ("../../include/config.php");
include_once ("../../include/sfs_case_graph.php");
include_once ("../../open_flash_chart/open-flash-chart.php");

if (!is_array($_SESSION["ydata"])) exit;

$graph_kind=($_SESSION["graph_kind"])?$_SESSION["graph_kind"]:"bar";
switch ($graph_kind) {
	case "bar":

		// Create the graph. 
		$g = new sfs_nbar();
		$g->set_y($_SESSION["ydata"]);
		if ($_SESSION["horizontal"]) $g->set90();
		$g->set_mtitle($_SESSION["mtitle"]);

		// Setup X-axis title.
		$g->set_xtitle($_SESSION["xtitle"]);
		if ($_SESSION["xlabel"]) $g->set_xlabel($_SESSION["xlabel"]);
		if ($_SESSION["xclabel"]) $g->set_xlfont();
		if ($_SESSION["xangle"]) $g->set_xlableangel($_SESSION["xangle"]);

		// Setup Y-axis title.
		$g->set_ytitle($_SESSION["ytitle"]);
		$g->set_shownum($_SESSION["num_format"],$_SESSION["unit"]);

		// Setup Legend title
		$g->set_ltitle($_SESSION["legend"]);

		// .. and finally stroke the image back to browser
		$g->draw();

		break;
	case "pie":

		// Create the graph. 
		$g = new sfs_pie3d();
		$g->set_mtitle($_SESSION["mtitle"]);
		$g->set_data($_SESSION["ydata"]);

		// Setup Legend title
		$g->set_ltitle($_SESSION["legend"]);
		$g->set_shownum($_SESSION["num_format"],$_SESSION["unit"]);

		// .. and finally stroke the image back to browser
		$g->draw();

		break;
	case "flashbar":
		$maxy=0;
		$ybcolor=array("#9999ef","#993366","#ffff00","#00ff00","#b8860b");
		while(list($k,$v)=each($_SESSION["ydata"])) {
			$bar[$k] = new bar_outline( 50, $ybcolor[$k], $ybcolor[$k]);
			$bar[$k] -> key(iconv('big5','utf-8',$_SESSION["legend"][$k]),10);
			while(list($kk,$vv)=each($v)) {
				if ($maxy < $vv) $maxy=$vv;
				$bar[$k]->add_data_tip( $vv, iconv('big5','utf-8',$_SESSION["xlabel"][$kk]."<br>".$_SESSION["legend"][$k].":".$vv));
			}
		}

		if ($maxy>50) {
			for($i=10;$i<=20;$i+=2) {
				$t=ceil($maxy/$i);
				if ($t%5==0) {
					$maxy=(ceil($maxy/$i))*$i;
					$stepy=$i/2;
					break;
				}
			}
		} elseif ($maxy>4) {
			for($i=20;$i>=4;$i--) {
				if (($maxy/$i)==intval($maxy/$i)) {
					$maxy=(ceil($maxy/$i))*$i;
					if ($i%2==0 && $i>=10)
						$stepy=$i/2;
					else
						$stepy=$i;
					break;
				}
			}
		} else
			$maxy=(ceil($maxy/5))*5;

		$g = new flash_graph();
//		$g->set_inner_background('#E3F0FD', '#CBD7E6', 90);
		$g->title( iconv('big5','utf-8',$_SESSION["mtitle"]), '{font-size: 18px;}');
		$g->set_base($SFS_PATH_HTML);
		$g->set_swf_path($SFS_PATH_HTML);
		$g->set_js_path($SFS_PATH_HTML);
		$g->data_sets = $bar;
		$g->set_tool_tip('#tip#');
		$g->set_x_legend(iconv('big5','utf-8',$_SESSION["xtitle"]), 12, '#000000' );
		$g->x_axis_colour('#000000', '#ffffff');
		if ($_SESSION["xlabel"]) {
			while(list($k,$v)=each($_SESSION["xlabel"])) $_SESSION["xlabel"][$k]=iconv('big5','utf-8',$v);
			$g->set_x_labels($_SESSION["xlabel"]);
		}

		$g->set_y_max($maxy);
		$g->set_y_legend(iconv('big5','utf-8',$_SESSION["ytitle"]), 12, '#000000');
		$g->y_axis_colour('#000000', '#e0e0e0');
		if ($stepy) $g->y_label_steps($stepy);

		echo $g->render();
		break;
	case "flashpie":
		$g = new flash_graph();
		$g->pie(60,'#000000','{font-size: 12px; color: #000000;');
		$g->title(iconv('big5','utf-8',$_SESSION["mtitle"]),'{font-size: 18px;padding-bottom: 24px;}');
		$g->set_base($SFS_PATH_HTML);
		$g->set_swf_path($SFS_PATH_HTML);
		$g->set_js_path($SFS_PATH_HTML);
		if ($_SESSION["legend"]) {
			while(list($k,$v)=each($_SESSION["legend"])) $_SESSION["legend"][$k]=iconv('big5','utf-8',$v);
		}
		$g->pie_values($_SESSION["ydata"], $_SESSION["legend"]);
		$g->set_tool_tip('#x_label#<br>#val#'.iconv('big5','utf-8',$_SESSION["unit"]));
		$g->pie_slice_colours(array("#9999ef","#993366","#ffff00","#00ff00","#b8860b"));
		echo $g->render();
		break;
}

		unset($_SESSION["ydata"]);
		unset($_SESSION["horizontal"]);
		unset($_SESSION["mdata"]);
		unset($_SESSION["mtitle"]);
		unset($_SESSION["xtitle"]);
		unset($_SESSION["xlabel"]);
		unset($_SESSION["xclabel"]);
		unset($_SESSION["xangle"]);
		unset($_SESSION["ytitle"]);
		unset($_SESSION["num_format"]);
		unset($_SESSION["unit"]);
		unset($_SESSION["legend"]);
		unset($_SESSION["gkind"]);
?>