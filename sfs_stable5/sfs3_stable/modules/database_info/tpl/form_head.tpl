<?php

/******************************************************************************

Description : Displays the head of the generating form

Author   	: Andreas Kempf aka 'amalesh' ak@living-source.com
Datum		: 1999-07-29

******************************************************************************/
?>
<form action="generate.php" method="post" target=_blank>
<table border="0" cellpadding="1" cellspacing="1">
<tr>
	<th bgcolor="#C0C0C0" colspan="7">
		PHP FormWizard 1.0 uses table <?php echo $table ?><br>
	</th>
</tr>
<tr>
	<td align="right" valign="bottom" bgcolor="#C0C0C0">
		Name
	</td>
	<td valign="bottom" bgcolor="#C0C0C0">
		Use?
	</td>
	<td valign="bottom" bgcolor="#C0C0C0">
		Input Type
	</td>
	<td valign="bottom" bgcolor="#C0C0C0">
		Size
	</td>
	<td valign="bottom" bgcolor="#C0C0C0">
		Maxlength
	</td>
	<td valign="bottom" bgcolor="#C0C0C0">
		Default
	</td>
	<td valign="bottom" bgcolor="#C0C0C0">
		SQL-Where?
	</td>
</tr>
