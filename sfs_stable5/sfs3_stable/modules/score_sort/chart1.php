<?php
include "config.php";
sfs_check();

include "../../include/sfs_case_score.php";

$az=$_REQUEST['scopeall'];

$countaz=count($az);

$vx=iconv("UTF-8","big5","各領域PR值");
$ms1=iconv("UTF-8","big5","處理中,請稍後...");
$ms2=iconv("UTF-8","big5","處理完成");
$student_sn=$_REQUEST['student_sn'];
$sel_year=$_REQUEST['sel_year'];
$sel_seme=$_REQUEST['sel_seme'];

$ye=substr($_REQUEST['class_id'],7,1);


if ($student_sn)
{
$sa=student_sn_to_classinfo2($student_sn,$sel_year,$sel_seme);	
$sname=$sa[4];

$seme_year_seme_x=$sel_year.$sel_seme;

$vx=iconv("UTF-8","BIG5","各領域PR值如下：");
$vx1=iconv("UTF-8","BIG5","年");
$vx2=iconv("UTF-8","BIG5","班");
$vx3=iconv("UTF-8","BIG5","號");

if ($sa[0]>6)$sa[0]=$sa[0]-6;
$vx=$sa[0].$vx1.$sa[1].$vx2.$sa[2].$vx3." ".$sname.$vx;


$vx4=iconv("UTF-8","BIG5","每學期及格領域數與不及格領域數分布");
$vx5=iconv("UTF-8","BIG5","學期");
$vx6=iconv("UTF-8","BIG5","及格數/不及格數");
$vx7=iconv("UTF-8","BIG5","及格領域數");
$vx8=iconv("UTF-8","BIG5","不及格領域數");



//echo "<pre>";
//print_r ($ss);
//echo "</pre>";


}




function get_seme_year_seme_from_student_sn()
{
    global $CONN,$student_sn,$seme_year_seme_x;
		     $sql="select seme_year_seme as tt from (
	            select seme_year_seme from stud_seme WHERE student_sn='$student_sn' and seme_year_seme<='$seme_year_seme_x'
	  UNION ALL select seme_year_seme from stud_seme_import WHERE student_sn='$student_sn' and seme_year_seme<='$seme_year_seme_x'
	  ) MyDerivedTable Order By tt";
	  
		$rs=$CONN->Execute($sql);
        	
	
     	if(is_object($rs))
		{
         $succstr="";
		 $failstr="";
		 while (!$rs->EOF) 
		 {
              $seme_year_seme = $rs->fields["tt"];	
			  
			  $student_sn_array=array($student_sn);

              $seme_array=array($seme_year_seme);

              $ss=cal_fin_score($student_sn_array,$seme_array,"","",1);
			  
		   
			  $succ=$ss[$student_sn][succ]; 
	          if (empty($succ))$succ=0;
            
			  $fail=$ss[$student_sn][fail]; 
			  if (empty($fail))$fail=0;
			  
			  $seme_year_seme_v=substr($seme_year_seme,0,3)."-".substr($seme_year_seme,3,1);
			
			  
			  $succstr.="{y:$succ ,label:\"$seme_year_seme_v\"},";
			  $failstr.="{y:$fail ,label:\"$seme_year_seme_v\"},";
			  
				  
			  $rs->MoveNext();
	
			
		 }
		 
		 $succstr=substr($succstr,0,-1);
		 $failstr=substr($failstr,0,-1);
		 
		 return $succstr."#".$failstr;
        }    


}


$stra=explode("#",get_seme_year_seme_from_student_sn());



?>


<!DOCTYPE HTML>
<html>
<head>

<script type="text/javascript" src="../../javascripts/jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../../javascripts/jquery/jquery.canvasjs.min.js"></script>
<script type="text/javascript" src="../../javascripts/jquery/canvasjs.min.js"></script></head>
<script type="text/javascript" src="../../javascripts/jquery.progressbar.min.js"></script>


<script type="text/javascript">


$(document).ready(function(){



   var chart = new CanvasJS.Chart("chartContainer1",
	{
		
		
		title:{
			text: "<?php echo $vx4;?>"
		},
		
		height:300,
	        axisY:{
        
           //maximum: 100
          },
		
		axisX:{
			title: "<?php echo $vx5;?>"
		},
		axisY:{
			title: "<?php echo $vx6;?>"
		},
		data: [
		{
			color: "#FA8072",
			type: "stackedColumn",
			legendText: "<?php echo $vx8;?>",
			showInLegend: "true",
			indexLabel: "{y}",
			indexLabelPlacement: "inside",
			indexLabelFontColor: "white",
			dataPoints: [
				<?php
				echo $stra[1];
				?>
				
			]
		},
		{
			color: "#00BFFF",
			type: "stackedColumn",
			legendText: "<?php echo $vx7;?>",
			showInLegend: "true",
			indexLabel: "{y}",
			indexLabelPlacement: "inside",
			indexLabelFontColor: "white",
			dataPoints: [
	           <?php
				echo $stra[0];
				?>		
				
			]
		}
		]
	});
	

	
var pp=0, d;
var options;
var dataPointsx = [];
var ye=<?php echo $ye;?>

$('#pb1').progressBar(0);
$('#proc').show();
$('#msg').html("<?php echo $ms1;?>");


	$.ajaxSetup({async:true}); 	
	        <?php  
	        for($k=0;$k<$countaz;$k++)
			{
             ?>
	
            $.ajax({
                type : "POST",
                url : "threadgetchart.php",
				data:{
                sel_year:"<?php echo $_REQUEST['sel_year'];?>",
                sel_seme:"<?php echo $_REQUEST['sel_seme'];?>",
                class_id:"<?php echo $_REQUEST['class_id'];?>",
                ss_id:"<?php echo $_REQUEST['ss_id'];?>",
                test_sort:"<?php echo $_REQUEST['test_sort'];?>",
                test_kind:"<?php echo $_REQUEST['test_kind'];?>",
                rate:"<?php echo $_REQUEST['rate'];?>",
                student_sn:"<?php echo $_REQUEST['student_sn'];?>",
                c_name:"<?php echo $_REQUEST['c_name'];?>", 
				subject1:"<?php echo $_REQUEST['subject1'];?>",
				colorid:"<?php echo $k;?>",
				scopeall:"<?php echo $az[$k];?>"				
				},

				dataType: 'json',
                success: function(data){
                
			      processData(data);
                   
		
		 
                }
            });
			<?php
			}
			?>
	
			
			
	    function processData(allText) 
		{
			
			
		   pp+=100/<?php echo $countaz;?>;
		   
		   
		   if (pp>=99)
		   {
			   pp=100;
			   
			   $('#msg').html("<?php echo $ms2;?>");
			   
			
		   }
		
           $('#pb1').progressBar(pp);
		
		  options = {
			 
		    title: {
			text: "<?php echo $vx;?>"
		    },
                //animationEnabled: true,
				height:500,
	        axisY:{
        
           maximum: 100
          },
               
		     data: []
	        };
			
           if (allText !="")
		   {
			   
		    var allLinesArray = allText.split('&');
             //alert(allText);
			 			 
			 
		   if (ye==1 || ye==7)
			{
		
		      dataPointsx.push({
			  showInLegend: true,
              legendText: allLinesArray[0],
              color: allLinesArray[1],
			  type: 'spline', 
			  dataPoints: [
		        { label: allLinesArray[2], y: parseInt(allLinesArray[3])},
				{ label: allLinesArray[4], y: parseInt(allLinesArray[5])},
			  ]
		     });
			 
			}		 
			 
			 
		    if (ye==2 || ye==8)
			{
		
		      dataPointsx.push({
			  showInLegend: true,
              legendText: allLinesArray[0],
              color: allLinesArray[1],
			  type: 'spline', 
			  dataPoints: [
		        { label: allLinesArray[2], y: parseInt(allLinesArray[3])},
				{ label: allLinesArray[4], y: parseInt(allLinesArray[5])},
				{ label: allLinesArray[6], y: parseInt(allLinesArray[7])},
				{ label: allLinesArray[8], y: parseInt(allLinesArray[9])},
			  ]
		     });
			 
			}		
			
		    if (ye==3 || ye==9)
			{
		
		      dataPointsx.push({
			  showInLegend: true,
              legendText: allLinesArray[0],
              color: allLinesArray[1],
			  type: 'spline', 
			  dataPoints: [
		        { label: allLinesArray[2], y: parseInt(allLinesArray[3])},
				{ label: allLinesArray[4], y: parseInt(allLinesArray[5])},
				{ label: allLinesArray[6], y: parseInt(allLinesArray[7])},
				{ label: allLinesArray[8], y: parseInt(allLinesArray[9])},
				{ label: allLinesArray[10], y: parseInt(allLinesArray[11])},
				{ label: allLinesArray[12], y: parseInt(allLinesArray[13])}
			  ]
		     });
			 
			}
			
			 
			 
		    if (ye==4)
			{
		
		      dataPointsx.push({
			  showInLegend: true,
              legendText: allLinesArray[0],
              color: allLinesArray[1],
			  type: 'spline', 
			  dataPoints: [
		        { label: allLinesArray[2], y: parseInt(allLinesArray[3])},
				{ label: allLinesArray[4], y: parseInt(allLinesArray[5])},
				{ label: allLinesArray[6], y: parseInt(allLinesArray[7])},
				{ label: allLinesArray[8], y: parseInt(allLinesArray[9])},
				{ label: allLinesArray[10], y: parseInt(allLinesArray[11])},
				{ label: allLinesArray[12], y: parseInt(allLinesArray[13])},
				{ label: allLinesArray[14], y: parseInt(allLinesArray[15])},
				{ label: allLinesArray[16], y: parseInt(allLinesArray[17])}
			  ]
		     });
			 
			}		


		    if (ye==5)
			{
		
		      dataPointsx.push({
			  showInLegend: true,
              legendText: allLinesArray[0],
              color: allLinesArray[1],
			  type: 'spline', 
			  dataPoints: [
		        { label: allLinesArray[2], y: parseInt(allLinesArray[3])},
				{ label: allLinesArray[4], y: parseInt(allLinesArray[5])},
				{ label: allLinesArray[6], y: parseInt(allLinesArray[7])},
				{ label: allLinesArray[8], y: parseInt(allLinesArray[9])},
				{ label: allLinesArray[10], y: parseInt(allLinesArray[11])},
				{ label: allLinesArray[12], y: parseInt(allLinesArray[13])},
				{ label: allLinesArray[14], y: parseInt(allLinesArray[15])},
				{ label: allLinesArray[16], y: parseInt(allLinesArray[17])},
				{ label: allLinesArray[18], y: parseInt(allLinesArray[19])},
				{ label: allLinesArray[20], y: parseInt(allLinesArray[21])}
			  ]
		     });
			 
			}			 			
			 
		    if (ye==6)
			{
		
		      dataPointsx.push({
			  showInLegend: true,
              legendText: allLinesArray[0],
              color: allLinesArray[1],
			  type: 'spline', 
			  dataPoints: [
		        { label: allLinesArray[2], y: parseInt(allLinesArray[3])},
				{ label: allLinesArray[4], y: parseInt(allLinesArray[5])},
				{ label: allLinesArray[6], y: parseInt(allLinesArray[7])},
				{ label: allLinesArray[8], y: parseInt(allLinesArray[9])},
				{ label: allLinesArray[10], y: parseInt(allLinesArray[11])},
				{ label: allLinesArray[12], y: parseInt(allLinesArray[13])},
				{ label: allLinesArray[14], y: parseInt(allLinesArray[15])},
				{ label: allLinesArray[16], y: parseInt(allLinesArray[17])},
				{ label: allLinesArray[18], y: parseInt(allLinesArray[19])},
				{ label: allLinesArray[20], y: parseInt(allLinesArray[21])},
				{ label: allLinesArray[22], y: parseInt(allLinesArray[23])},
				{ label: allLinesArray[24], y: parseInt(allLinesArray[25])}
			  ]
		     });
			 
			}			 				 
			  
			
			  options.data=dataPointsx;
			
		   }	
 			

           $("#chartContainer").CanvasJSChart(options);
			
			
        
       }
	   
  

 chart.render();

			
});




</script>

</head>
<body>
<center>
<div id="proc" style="display:none;">
<span class="progressBar" id="pb1">0%</span>
<div id="msg">
</div>
<div id="chartContainer" style="height: 500px; width: 90%;"></div>
</div>
<div class="show" id="show"></div>

<div id="chartContainer1" style="height: 200px; width: 90%;"></div>
</center>
</body>

</html>
