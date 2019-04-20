<style>
#sight_form_list table{background:#ccc; font-size:12px}
</style>
<script>
function check_value1(a) {
	var b;
	b=document.getElementById(a).value;
	if (b==-9) {
		if (a=='s1' || a=='s2')
			document.getElementById(a).value='**';
		else {
			alert('只有裸視能輸入「無法測量」。');
			document.getElementById(a).value='';
		}
	} else if (b<0) document.getElementById(a).value='<0.1';
	else if (b>=1) document.getElementById(a).value=b/10;

}
function check_value2(a,b,c) {
	var d;
	d=document.getElementById(b+c+a).checked;
	document.getElementById('M'+c+a).checked=false;
	document.getElementById('H'+c+a).checked=false;
	document.getElementById('A'+c+a).checked=false;
	document.getElementById(b+c+a).checked=d;}
</script>
<div id="sight_form_list">
<table>
	<tr style="background-color: #f4feff; text-align: center;">
		<td>邊</td>
		<td>裸視</td>
		<td>矯正</td>
		<td>近<br>
		視</td>
		<td>遠<br>
		視</td>
		<td>散<br>
		光</td>
		<td>弱<br>
		視</td>
		<td>其<br>
		他</td>
		<td>其他陳述</td>
		<td>處置<br>
		代號</td>
		<td>功能<br>
		選項</td>
	</tr>
		<tr>
		<td>右</td>
		<td >{{$data.r.sight_o}}</td>
		<td>{{$data.r.sight_r}}</td>
		<td>{{$data.r.My}}</td>
		<td>{{$data.r.Hy}}</td>
		<td>{{$data.r.Ast}}</td>
		<td>{{$data.r.Amb}}</td>
		<td></td>
		<td>{{$data.r.diag}}</td>
		<td>
</td>

	</tr>
	<tr>
		<td>左</td>
		<td >{{$data.l.sight_o}}</td>
		<td>{{$data.l.sight_r}}</td>
		<td>{{$data.l.My}}</td>
		<td>{{$data.l.Hy}}</td>
		<td>{{$data.l.Ast}}</td>
		<td>{{$data.l.Amb}}</td>
		<td></td>
		<td>{{$data.l.diag}}</td>
		<td>
</td>

	</tr>
</table>
</div>
