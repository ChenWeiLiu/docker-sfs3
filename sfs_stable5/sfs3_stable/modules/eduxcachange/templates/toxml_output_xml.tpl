{{* $Id:$ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{* include file="$SFS_TEMPLATE/menu.tpl" *}}
{{$SFS_MENU}}
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100%" valign=top bgcolor="#CCCCCC">
    <form name ="base_form" action="" method="post" >

		<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
			<tr>
				<td class=title_mbody colspan=2 align=center > XML��X�@�~</td>
			</tr>
			<tr>
	    	<td width="100%" align="center" colspan="2">
	    	<input type="hidden" name="update_id" value="{{$smarty.session.session_log_id}}">
				<BR>�����X�ɮ׫e�A�Х��T�{�t�Τw�w�� 1.�ǥͼ��g(reward) 2.�ǥͨ������O�P�ݩ�(stud_subkind) �ҲաI 
				<input type=submit name="output_xml" value ="����XML��" onClick="return confirm('�Э@�ߵ��ԡA���w�@�M���a�I');"></td>
			</tr>
		</table><br></td>
	</tr>	
</table>
<br>
<div id=process_res align=center>
</div>
</form>

{{include file="$SFS_TEMPLATE/footer.tpl"}}

<script type='text/javascript'>
function go_download(filepath) {
	//document.getElementById("<%= hdnPath.ClientID%>").value = filepath;
	//document.getElementById('frmAttachment').submit();
	        
	var iframe;
	iframe = document.getElementById('hiddenDownloader');
	if (iframe == null) {
	   iframe = document.createElement('iframe');
	   iframe.id = 'hiddenDownloader';
	   iframe.style.visibility = 'hidden';
	   iframe.style.display = 'none';
	   iframe.style.border = 'none';
	   iframe.style.width = '0';
	   iframe.style.height = '0';
	   document.body.appendChild(iframe);
    }
	iframe.src = filepath;   
	return false;
};
</script>