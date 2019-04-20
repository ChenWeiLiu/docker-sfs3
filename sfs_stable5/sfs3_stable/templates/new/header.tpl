{{php}}
//$Id: header.tpl 5959 2010-06-14 23:40:50Z hami $
// 額外的 javascript 加入
global $injectJavascript;
$injectJavascript = ($this->_smarty_vars['capture']['injectJavascript'])?$this->_smarty_vars['capture']['injectJavascript']: '';
//表頭
head($this->get_template_vars("module_name"));
{{/php}}