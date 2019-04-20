<?php

/******************************************************************************

Description : Displays a row for the generating form

Author   	: Andreas Kempf aka 'amalesh' ak@living-source.com
Datum		: 1999-07-29

******************************************************************************/

?>
<tr>
	<td valign="top" align="right">
	<?php echo $name ?>
	<input type="hidden" name="field_name[<?php echo $i ?>]" value="<?php echo $name ?>">
	<input type="Hidden" name="field_type[<?php echo $i ?>]" value="<?php echo $type ?>">
	</td>
	<td valign="top" align="center">
		<input type="Checkbox" name="use[<?php echo $i ?>]" value="1" checked>
	</td>
	<td valign="top">
		<select name="input_type[<?php echo $i ?>]">
			<option value="text">text
			<option value="textarea" <?php if ($type=="blob") echo " selected" ?>>textarea
			<option value="selectbox">selectbox
			<option value="radio">radio
			<option value="checkbox" <?php if ($len=="1") echo " selected" ?>>checkbox
			<option value="hidden">hidden
			<option value="password">password
		</select>
	</td>
	<td valign="top">
		<?php if ($type!="blob") { ?>
		<input type="text" size="3" name="size[<?php echo $i ?>]" value="<?php echo $len ?>">
		<?php } ?>
	</td>
	<td valign="top">
		<?php if ($type!="blob") { ?>
		<input type="text" size="3" value="<?php echo $len ?>" name="maxlen[<?php echo $i ?>]">
		<?php } ?>
	</td>
	<td valign="top">
		<input type="text" size="20" name="default[<?php echo $i ?>]" value="&lt;?php echo $<?php echo $name ?> ?&gt;">
	</td>
	<td valign="top" align="center">
		<input type="radio" name="where" value="<?php echo " where $name = '$$name'" ?>">
	</td>
</tr>