<?
/******************************************************************************

Description : standard

Edit, copy, rename this file if needed

Author   	: Andreas Kempf aka 'amalesh' ak@living-source.com
Datum		: 1999-07-29

******************************************************************************/
?>


<?php
	// Checkbox
	if ($input_type[$x] == "checkbox")
	{
?>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo "<?php echo \$field_data[".$field_name[$x]."][d_field_cname] ?>" ?></td>
	<td align="right" CLASS="title_sbody2"><input type="checkbox" name="<?php echo $field_name[$x] ?>" value="<?php echo $default[$x] ?>"></td>
</tr>
<?php	
	}
	
	// Hidden
	if ($input_type[$x] == "hidden")
	{
?>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo "<?php echo \$field_data[".$field_name[$x]."][d_field_cname] ?>" ?></td>
	<td CLASS="gendata"><input type="hidden" name="<?php echo $field_name[$x] ?>" value="<?php echo $default[$x] ?>"></td>
</tr>
<?php	
	}

	// Radio
	if ($input_type[$x] == "radio")
	{
?>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo "<?php echo \$field_data[".$field_name[$x]."][d_field_cname] ?>" ?></td>
	<td CLASS="gendata"><input type="radio" name="<?php echo $field_name[$x] ?>" value="<?php echo $default[$x] ?>"></td>
</tr>
<?php	
	}

	// Selectbox
	if ($input_type[$x] == "selectbox")
	{
?>	
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo "<?php echo \$field_data[".$field_name[$x]."][d_field_cname] ?>" ?></td>
	<td>
		<select name="<?php echo $field_name[$x] ?>">
		<option value="<?php echo $default[$x] ?>"><?php echo $default[$x] ?>
		<option value="<?php echo $default[$x] ?>"><?php echo $default[$x] ?>
		<option value="<?php echo $default[$x] ?>"><?php echo $default[$x] ?>
		</select>
	</td>
</tr>
<?php	
	}

	// Text
	if ($input_type[$x] == "text")
	{
?>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo "<?php echo \$field_data[".$field_name[$x]."][d_field_cname] ?>" ?></td>
	<td CLASS="gendata"><input type="text" size="<?php echo $size[$x] ?>" maxlength="<?php echo $maxlen[$x] ?>" name="<?php echo $field_name[$x] ?>" value="<?php echo $default[$x] ?>"></td>
</tr>
<?php	
	}
	// Textarea
	if ($input_type[$x] == "textarea")
	{
?>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo "<?php echo \$field_data[".$field_name[$x]."][d_field_cname] ?>" ?></td>
	<td><textarea name="<?php echo $field_name[$x] ?>" cols=40 rows=5 wrap=virtual><?php echo $default[$x] ?></textarea></td>
</tr>
<?php } ?>
