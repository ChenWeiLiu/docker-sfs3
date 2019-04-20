/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.language = 'zh';
	config.skin='kama';
	config.filebrowserBrowseUrl = 'include/ckfinder/ckfinder.html';
  config.filebrowserImageBrowseUrl = 'include/ckfinder/ckfinder.html?Type=Images';
  config.filebrowserFlashBrowseUrl = 'include/ckfinder/ckfinder.html?Type=Flash';
  config.filebrowserImageUploadUrl = 'include/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';//可上傳圖檔
  config.filebrowserFlashUploadUrl = 'include/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash';//可上傳Flash檔案 
	
	config.font_names ='Arial/Arial, Helvetica, sans-serif;	Comic Sans MS/Comic Sans MS, cursive;	Courier New/Courier New, Courier, monospace;	Georgia/Georgia, serif;	Lucida Sans Unicode/Lucida Sans Unicode, Lucida Grande, sans-serif;	Tahoma/Tahoma, Geneva, sans-serif;	Times New Roman/Times New Roman, Times, serif;	Trebuchet MS/Trebuchet MS, Helvetica, sans-serif;	Verdana/Verdana, Geneva, sans-serif;	新細明體;	標楷體;	微軟正黑體' ;
	config.extraPlugins += (config.extraPlugins ?',lineheight' :'lineheight');
        config.toolbar_simple=[
            [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ],
            [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ],
            [ 'NumberedList', 'BulletedList'],
            [ 'Link', 'Unlink'],
            [ 'Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak' ],
            [ 'Styles', 'Format', 'Font', 'FontSize' ],
            [ 'TextColor', 'BGColor' ]
        ];

    //線上補考 - question 的功能表 簡略型
    config.toolbar_question=[
        [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript','TextColor', 'FontSize','-','Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'RemoveFormat','Image', 'Table','NumberedList', 'BulletedList' ]
    ];

    //線上補考 - choice 的功能表 簡略型
    config.toolbar_choice=[
        [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript','-','RemoveFormat','Image']
    ];

    config.enterMode = CKEDITOR.ENTER_BR;
};

