/**

$Id: sfs_script_value.js 5311 2009-01-10 08:11:55Z hami $
 js_value.js

LogicalValue:ノ耞癸禜琌才兵ン瞷矗ㄑ匡拒Τ
integer俱计耞タ俱计㎝璽俱计
number 疊翴计妓タ璽计
date ら戳や穿﹚だ筳才腹ら戳Α箇砞琌'-'だ筳才腹
string 耞﹃珹┪ぃ珹琘ㄇ﹃
肚true┪false

把计
ObjStr 肚
ObjType矪瞶篈('integer','number','date','string'ぇ)

ㄤ弧
肚岿粇癟

Author:PPDJ

*/
function LogicalValue(ObjStr,ObjType)
{
var str='';
if ((ObjStr==null) || (ObjStr=='') || ObjType==null)
{
alert('ㄧ计 LogicalValue ぶ把计');
return false;
}
var obj = document.all(ObjStr);
if (obj.value=='') return false;
for (var i=2;i<arguments.length;i++)
{
if (str!='')
str += ',';
str += 'arguments['+i+']';
}
str=(str==''?'obj.value':'obj.value,'+str);
var temp=ObjType.toLowerCase();
if (temp=='integer')
{
return eval('IsInteger('+str+')');
}
else if (temp=='number')
{
return eval('IsNumber('+str+')');
}
else if (temp=='string')
{
return eval('SpecialString('+str+')');
}
else if (temp=='date')
{
return eval('IsDate('+str+')');
}
else if (temp=='twToDate')
{
return eval('twToDate('+str+')');
}
else
{
alert('"'+temp+'"摸瞷セいゼ矗ㄑ');
return false;
}
}

/**
IsInteger: ノ耞计才﹃琌俱
耞琌琌タ俱计┪璽俱计true┪false
string: 惠璶耞﹃
sign: 璝璶耞琌タ俱计ㄏノ琌タノ'+'璽'-'ぃノ玥ボぃ耞
Author: PPDJ
sample:
var a = '123';
if (IsInteger(a))
{
alert('a is a integer');
}
if (IsInteger(a,'+'))
{
alert(a is a positive integer);
}
if (IsInteger(a,'-'))
{
alert('a is a negative integer');
}
*/

function IsInteger(string ,sign)
{
var integer;
if ((sign!=null) && (sign!='-') && (sign!='+'))
{
alert('IsInter(string,sign)把计岿\nsignnull┪"-"┪"+"');
return false;
}
integer = parseInt(string);
if (isNaN(integer))
{
return false;
}
else if (integer.toString().length==string.length)
{
if ((sign==null) || (sign=='-' && integer<0) || (sign=='+' && integer>0))
{
return true;
}
else
return false;
}
else
return false;
}

/**
twToDate: 盢チ瓣ら戳锣﹁じら戳
把计
DateString 惠璶耞﹃
Dilimeter  ら戳だ筳才腹箇砞'-'
Author: hami
*/

function twToDate(DateString,Dilimeter)
{
if (DateString==null) return false;
if (Dilimeter=='' || Dilimeter==null)
Dilimeter = '-';
var tempArray;
var tempa=0;	
var ttt ;
tempArray = DateString.split(Dilimeter);
tempa = parseInt(tempArray[0])+1911;	
ttt = tempa.toString();
ttt = ttt+Dilimeter+tempArray[1]+Dilimeter+tempArray[2];	
return  ttt;	
}

/*

true┪false

把计
DateString 惠璶耞﹃
Dilimeter  ら戳だ筳才腹箇砞'-'
Author: 


/**
IsDate: ノ耞﹃琌琌ら戳Α﹃


true┪false

把计
DateString 惠璶耞﹃
Dilimeter  ら戳だ筳才腹箇砞'-'
Author: PPDJ 


sample:
var date = '1999-1-2';
if (IsDate(date))
{
alert('You see, the default separator is "-");
}
date = '1999/1/2";
if (IsDate(date,'/'))
{
alert('The date\'s separator is "/");
}
*/

function IsDate(DateString , Dilimeter)
{
if (DateString==null) return false;
if (Dilimeter=='' || Dilimeter==null)
Dilimeter = '-';
var tempy='';
var tempm='';
var tempd='';
var mm=0;
var tempArray;
if (DateString.length<8 && DateString.length>10)
return false; 
tempArray = DateString.split(Dilimeter);
if (tempArray.length!=3)
return false;
if (tempArray[0].length==4)
{
tempy = tempArray[0];
tempd = tempArray[2];
}
else
{
tempy = tempArray[2];
tempd = tempArray[1];
}
tempm = tempArray[1];
if((tempm.length==2)&&(tempm.substring(0,1)=='0'))
tempm = tempm.substring(2,1);
if((tempd.length==2)&& (tempd.substring(0,1)=='0'))
tempd = tempd.substring(2,1);
var tDateString = tempy + '/'+tempm.toString() + '/'+tempd.toString()+' 8:0:0';//?琌?и????
var tempDate = new Date(tDateString);
if (isNaN(tempDate))
return false;
if (((tempDate.getUTCFullYear()).toString()==tempy) && (tempDate.getMonth()==parseInt(tempm)-1) && (tempDate.getDate()==parseInt(tempd)))
{
return true;
}
else
{
return false;
}
}


/**
IsNumber: ノ耞计﹃琌计
临耞琌琌タ┪璽true┪false
string: 惠璶耞才﹃
sign: 璝璶耞琌タ俱计ㄏノ琌タノ'+'璽'-'ぃノ玥ボぃ耞
Author: PPDJ
sample:
var a = '123';
if (IsNumber(a))
{
alert('a is a number');
}
if (IsNumber(a,'+'))
{
alert(a is a positive number);
}
if (IsNumber(a,'-'))
{
alert('a is a negative number');
}
*/

function IsNumber(string,sign)
{
var number;
if (string==null) return false;
if ((sign!=null) && (sign!='-') && (sign!='+'))
{
alert('IsNumber(string,sign)把计岿\nsignnull┪"-"┪"+"');
return false;
}
number = new Number(string);
if (isNaN(number))
{
return false;
}
else if ((sign==null) || (sign=='-' && number<0) || (sign=='+' && number>0))
{
return true;
}
else
return false;
}



/**
SpecialString: ノ耞才﹃琌Τ┪ぃΤ琘ㄇ才


true┪false

把计
string  惠璶耞﹃
compare  ゑ耕才(膀非﹃)
BelongOrNot true┪false"true"ボstring–﹃常compareい
"false"ボstring–才常ぃcompareい

Author: PPDJ
sample1:
var str = '123G';
if (SpecialString(str,'1234567890'))
{
alert('Yes, All the letter of the string in \'1234567890\'');
}
else
{
alert('No, one or more letters of the string not in \'1234567890\'');
}
狦磅︽琌else场だ
sample2:
var password = '1234';
if (!SpecialString(password,'\'"@#$%',false))
{
alert('Yes, The password is correct.');
}
else
{
alert('No, The password is contain one or more letters of \'"@#$%\'');
}
狦磅︽琌else场だ
*/
function SpecialString(string,compare,BelongOrNot)
{
if ((string==null) || (compare==null) || ((BelongOrNot!=null) && (BelongOrNot!=true) && (BelongOrNot!=false)))
{
alert('function SpecialString(string,compare,BelongOrNot)????');
return false;
}
if (BelongOrNot==null || BelongOrNot==true)
{
for (var i=0;i<string.length;i++)
{
if (compare.indexOf(string.charAt(i))==-1)
return false
}
return true;
}
else
{
for (var i=0;i<string.length;i++)
{
if (compare.indexOf(string.charAt(i))!=-1)
return false
}
return true;
}
}
function checkok()
{
	var OK=true;	
	var chk_date='';	
	chk_date = twToDate(document.myform.tempbirthday.value,'-');
	alert(chk_date);
	if(IsDate(chk_date))
	{
		document.myform.birthday.value = chk_date;		
	}
	else
	{
		alert(document.myform.tempbirthday.value + '\n ぃ琌タ絋ら戳');
		OK=false;
	}	
	return OK
}

function setfocus(element) {
	element.focus();
 return;
}
