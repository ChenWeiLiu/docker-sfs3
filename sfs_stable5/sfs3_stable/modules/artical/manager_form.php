<script>
$(function() {

	$("#start_date").datepicker({
		dateFormat: 'yy-mm-dd' ,
		showOn: 'button',
		buttonImage: 'images/calendar.gif',
		buttonImageOnly: true
		 });
	$("#end_date").datepicker({
		dateFormat: 'yy-mm-dd' ,
		showOn: 'button',
		buttonImage: 'images/calendar.gif',
		buttonImageOnly: true
		 });

	$.metadata.setType("attr", "validate");

	$("#signForm").validate();


});
</script>


<form action="" method="post" id="signForm">
<dl>
	<dt>期別:</dt>
	<dd><input type="text" name="title" id="title" size="24" value="<?php if($res->fields['title'])echo $res->fields['title'];else echo date('Y年m月');?>"  validate="required:true" /></dd>
	<dt>投稿開始時間:</dt>
	<dd><input type="text" name="start_date" id="start_date" size="12"
		value="<?php echo $res->fields['start_date']?>"  validate="required:true" /></dd>
	<dt>投稿結束時間:</dt>
	<dd><input type="text" name="end_date" id="end_date" size="12" value="<?php echo $res->fields['end_date']?>" validate="required:true" /></dd>
	<dt>是否發布:</dt>
	<dd><input type="checkbox" name="is_publish" id="is_publish" value="1" <?php if ($res->fields['is_publish']):?>checked=checked<?php endif?> /></dd>
	<dt></dt>
	<dd><input type="submit" name="act" id="act" value="確定" /></dd>
</dl>
<input type="hidden" name="id"  value="<?php echo $res->fields['id']?>" />
</form>
