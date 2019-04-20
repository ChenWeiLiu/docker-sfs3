<?php

/******************************************************************************

Description : Displays the foot of the generating form

Author   	: Andreas Kempf aka 'amalesh' ak@living-source.com
Datum		: 1999-07-29

******************************************************************************/

?>
<tr>
	<td align="center" valign="bottom" bgcolor="#C0C0C0" colspan="7">
		Style:
		<select name="styles">
		<?php echo $styleoptions ?>
		</select>
	</td>

</tr>
<tr>
	<td align="center" valign="bottom" bgcolor="#C0C0C0" colspan="7">
		<input type="Submit" value=" Generate ">  <input type="checkbox" name="is_view_source" value="1" checked >  View Source
	</td>
</tr>

<input type=hidden name=host value=<?php echo $host ?>>
<input type=hidden name=user value=<?php echo $user ?>>
<input type=hidden name=password value=<?php echo $password ?>>
<input type=hidden name=database value=<?php echo $database ?>>

</table>
