<!-- //$Id: chi_text.js 5311 2009-01-10 08:11:55Z hami $ -->
<script language="JavaScript">
<!--


function moveit2(chi,event) {
	var pKey = event.keyCode;//十字鍵 38向上 40向下;37向左;39向右
	if (pKey==40 || pKey==38  ) {
//	if (pKey==40 || pKey==38 || pKey==37 || pKey==39 ) {
	var max=document.f1.elements.length ;//所有元件數量
	var Go=0;//要移動位置
	TText= new Array ; //文字欄位陣列
	var Tin=0; //文字欄位陣列索引
	var Tin_now=0; //文字欄位陣列索引目前位置
	for(i=0; i<max; i++) {
	var obj = document.f1.elements[i];
	if (obj.type == 'text')
	{
	TText[Tin]=i; //記下它在所有元表中的第幾個
if(obj.name==chi.name ) {Tin_now=Tin;} //如果是傳進來的欄位,就記下該欄位在文字欄位陣列索引值
	Tin=Tin+1;
	}
	}
if (Tin==1 ) return false;//僅一個就不要移了
// if (pKey==40 || pKey==39 ) KK=40;
// if (pKey==38 || pKey==37 ) KK=38;
switch (pKey){ //循迴
	case 40://向下
		Tin=Tin-1;//取得索引值
		(Tin_now == Tin ) ? Go=TText[0] : Go=TText[Tin_now+1];
		document.f1.elements[Go].focus();
		return false;
		break;
	case 38://向上
		Tin=Tin-1;//取得索引值
		(Tin_now == 0 ) ? Go=TText[Tin] : Go=TText[(Tin_now-1)];
		document.f1.elements[Go].focus();
		return false;
		break;
		default:
	return false;
	}
	}
}

//-->
</script>
