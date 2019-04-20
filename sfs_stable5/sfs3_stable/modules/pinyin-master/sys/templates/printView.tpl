
<!DOCTYPE html>
<html>
<head>


<title>
<{$my_title}>
</title>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

<style type="text/css">
	body {
		font-family: "Helvetica Neue", Helvetica, Arial, "微軟正黑體", sans-serif;
	}

	table.table {
		font-size: 18px;
	}

	table.submitdata {
		font-size: 16px;
	}

	table.list{
		width: 100%;
	}
	table.list td{
		border: 1px solid #ddd;
	}

</style>

<script src="http://code.jquery.com/jquery-1.9.1.js"></script>

</head>




<body class="container">

<{foreach key=id item=name from=$users_name_data }>

<table class="table table-hover table-condensed table-striped table-bordered" cellspacing="0" width="100%" >
    <tr class="danger"> 
	<td class="col-md-12"> 雙語畢業證書姓名英譯調查 </td>
    </tr>

    <tr>
        <td>
          <table class="list" style="width:100%">
	    <tr>
	      <td width="15%" align="left" valign="middle">
		座號:<{$keep_data[$id]['number']}> <{*座號*}></br>
		學號:<{$id}>  <{*學號*}> 
	      </td>
	      <td width="25%" align="center" valign="middle">
               姓名:<{$name}><{*姓名*}> 
	      </td>
	      <td width="60%">
		 請於下方擇一勾選，以利製作雙語畢業證書，有護照者，從護照之音譯。 
	      </td>
	    </tr>
	  </table>
        </td>
    </tr>

    <tr>
      <td>
	姓名英譯:
	<ul>
    <{*姓名英譯*}>
    </br>
    <li> 已有護照。音譯為_____________________________ <br> (請交護照影本供導師核對)</li>
    <li> 漢語:  <{$eng_name_format['hy'].$id}> </li>
    <li> 通用: <{$eng_name_format['ty'].$id}> </li>
    <li> 威妥碼: <{$eng_name_format['wg'].$id}> </li>
    <li> 國音二式: <{$eng_name_format['g2'].$id}> </li>
    <br>
    <li> 其它:___________________________________
      </ul>
      </td>
    </tr>

    <tr>
        <td>
          <table class="list" style="width:100%">
	    <tr>
	      <td width="20%">
		<br>
		家長簽名
	      </td>
	      <td width="80%" style="text-align: right;" >
		  ____年_____月_____日
	      </td>
	    </tr>
	  </table>
        </td>
    </tr>

</table>
<br>
<br>

<{/foreach}>

</body>
</html>
