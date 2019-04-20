function positionInfo(object) {

  var p_elm = object;

  this.getElementLeft = getElementLeft;
  function getElementLeft() {
    var x = 0;
    var elm;
    if(typeof(p_elm) == "object"){
      elm = p_elm;
    } else {
      elm = document.getElementById(p_elm);
    }
    while (elm != null) {
      x+= elm.offsetLeft;
      elm = elm.offsetParent;
    }
    return parseInt(x);
  }

  this.getElementWidth = getElementWidth;
  function getElementWidth(){
    var elm;
    if(typeof(p_elm) == "object"){
      elm = p_elm;
    } else {
      elm = document.getElementById(p_elm);
    }
    return parseInt(elm.offsetWidth);
  }

  this.getElementRight = getElementRight;
  function getElementRight(){
    return getElementLeft(p_elm) + getElementWidth(p_elm);
  }

  this.getElementTop = getElementTop;
  function getElementTop() {
    var y = 0;
    var elm;
    if(typeof(p_elm) == "object"){
      elm = p_elm;
    } else {
      elm = document.getElementById(p_elm);
    }
    while (elm != null) {
      y+= elm.offsetTop;
      elm = elm.offsetParent;
    }
    return parseInt(y);
  }

  this.getElementHeight = getElementHeight;
  function getElementHeight(){
    var elm;
    if(typeof(p_elm) == "object"){
      elm = p_elm;
    } else {
      elm = document.getElementById(p_elm);
    }
    return parseInt(elm.offsetHeight);
  }

  this.getElementBottom = getElementBottom;
  function getElementBottom(){
    return getElementTop(p_elm) + getElementHeight(p_elm);
  }
}

function DropDownControl() {

  var DropDownId = 'DropDownControl';


  var dateField = null;

  function getProperty(p_property){
    var p_elm = DropDownId;
    var elm = null;

    if(typeof(p_elm) == "object"){
      elm = p_elm;
    } else {
      elm = document.getElementById(p_elm);
    }
    if (elm != null){
      if(elm.style){
        elm = elm.style;
        if(elm[p_property]){
          return elm[p_property];
        } else {
          return null;
        }
      } else {
        return null;
      }
    }
  }

  function setElementProperty(p_property, p_value, p_elmId){
    var p_elm = p_elmId;
    var elm = null;

    if(typeof(p_elm) == "object"){
      elm = p_elm;
    } else {
      elm = document.getElementById(p_elm);
    }
    if((elm != null) && (elm.style != null)){
      elm = elm.style;
      elm[ p_property ] = p_value;
    }
  }

  function setProperty(p_property, p_value) {
    setElementProperty(p_property, p_value, DropDownId);
  }



  
/*  
  this.setItem = setItem;
  function setItem(sItem, iAppend) {
    if (dateField) {
      var dateString = sItem;
      var dataString = new String(dateField.value);
      
      if (iAppend==1)
      {
       dateField.value +=dateString;
//中間加,        
//        if (dataString=="")
//            dateField.value +=dateString;
//        else 
//            dateField.value += ','+dateString;
      } else
        dateField.value =dateString;
                dateField.focus();
      hide();
    }
    return;
  }
  
  */
  

  this.setItem = setItem;
  function setItem(sItem, iAppend) {
    if (dateField) {
      var dateString = sItem;
      var dataString = new String(dateField.value);
      
      
      switch (iAppend)
      {
        case 1: 
                dateField.value +=dateString;
                break;

        
        case 2:
                if (dataString=="")
                    dateField.value +=dateString;
                else 
                    dateField.value += ','+dateString;  
                break;          
        
        default: 
                dateField.value =dateString;
                break;   


      }
      

      dateField.focus();
      hide();
    }
    return;
  }
  



  function DropDownDrawTable(sItems, iRepeatDirection, iAppend, iFirst) {

    var dayOfMonth = 1;
    var validDay = 0;
    var aItems = sItems.split(",")
    var Item;
    var sList="";

    

    var table = "<table cellspacing='0' cellpadding='2' border='1' bgcolor=yellow>";
    table = table + "<tr>";
    table = table + "  <th>請選擇</th>";

    table = table + "</tr>";

    var sSplit="&nbsp &nbsp";
    
    if (iRepeatDirection==1)
      sSplit="<br>";
        

    for (Item in aItems)
    {
        sItem=aItems[Item];
        
    
        sList+="<div align=left fgcolor=white><u><a  href=\"javascript:setItemData('"+sItem+"',"+iAppend+","+iFirst+")\">"+sItem+"</a>"+sSplit + "</u></div>";
    }
    
    table = table + "<tr align=left><td align=left> "+sList+"</td></tr>"; 
    table = table + "<trhalign=left><td><a href='javascript:hideDropDownControl();'>關閉</a></td></tr>";    
    table = table + "</table>";

    return table;
  }

  this.show = show;
  
  function show(field, sItems, iRepeatDirection, iAppend, iFirst) {
  
    // If the DropDown is visible and associated with
    // this field do not do anything.
    if (dateField == field) {
      return;
    } else {
      dateField = field;
    }

    if(dateField) {
      try {
        var dateString = new String(dateField.value);
        var dateParts = dateString.split("/");
        
       
      } catch(e) {}
    }



    if(document.getElementById){

      DropDown = document.getElementById(DropDownId);
      DropDown.innerHTML = DropDownDrawTable(sItems, iRepeatDirection,iAppend, iFirst);

      setElementProperty('display', 'block', 'DropDownControlIFrame');
      setProperty('display', 'block');

      var fieldPos = new positionInfo(dateField);
      var DropDownPos = new positionInfo(DropDownId);

      var x = fieldPos.getElementLeft();
      var y = fieldPos.getElementBottom();

      setProperty('left', x + "px");
      setProperty('top', y + "px");
      setElementProperty('left', x + "px", 'DropDownControlIFrame');
      setElementProperty('top', y + "px", 'DropDownControlIFrame');
      setElementProperty('width', DropDownPos.getElementWidth() + "px", 'DropDownControlIFrame');
      setElementProperty('height', DropDownPos.getElementHeight() + "px", 'DropDownControlIFrame');
    }
  }

  this.hide = hide;
  function hide() {
    if(dateField) {
      setProperty('display', 'none');
      setElementProperty('display', 'none', 'DropDownControlIFrame');
      dateField = null;
    }
  }
}

var DropDownControl = new DropDownControl();


  //入口  field 欲輸入的欄位, 最可能是TextBox, sItem 清單, 
//  iRepeatDirection 1:直 其它 橫 
//  iAppend 1, 附加,2 附加並加逗號  0 單一, 
//  iFirst: 只取最前iFirst碼, 0: 不取



function showDropDownItem(textField, sItems, iRepeatDirection, iAppend, iFirst) {
  DropDownControl.show(textField, sItems, iRepeatDirection, iAppend, iFirst);
}

function hideDropDownControl() {
  DropDownControl.hide();
}

function setItemData(sItem, iAppend, iFirst) {
    var sFirst;
  if (iFirst>0)  
     sFirst=sItem.substring(0,iFirst);
  else
     sFirst=sItem;
      
    
  
  DropDownControl.setItem(sFirst, iAppend);
}

function changeDropDownControlYear(change) {
  DropDownControl.changeYear(change);
}

function changeDropDownControlMonth(change) {
  DropDownControl.changeMonth(change);
}



document.write("<iframe id='DropDownControlIFrame' src='javascript:false;' frameBorder='0' scrolling='no'></iframe>");
document.write("<div id='DropDownControl'></div>");
