<?php
/*
Module Name: Modules
Module URI: http://www.siteeditor.org/modules/modules
Description: Module Modules For Site Editor Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/




$pagebuilder = new PageBuilderApplication();

$site_editor_app->pagebuilder = $pagebuilder;
//add new group to modules tab
$pagebuilder->add_module_group( "modules" , "basic" , __("Basic","site-editor") );

$pagebuilder->add_module_group( "modules" , "theme" , __("Theme","site-editor") );

$pagebuilder->add_module_group( "modules" , "stracture" , __("structure","site-editor") );

$pagebuilder->add_module_group( "modules" , "apps" , __("Apps","site-editor") );

$pagebuilder->add_module_group( "modules" , "slideshow" , __("Slideshow","site-editor") );

$pagebuilder->add_module_group( "modules" , "gallery" , __("Gallery","site-editor") );

$pagebuilder->add_module_group( "modules" , "media" , __("Media","site-editor") );

$pagebuilder->add_module_group( "modules" , "socials" , __("Socials","site-editor") );

$pagebuilder->add_module_group( "modules" , "content" , __("Content","site-editor") );


//add new menu for button module
//$button_menu = $pagebuilder->contextmenu->create_menu( "button" , __("Button","site-editor") , 'button' , 'class' , 'element' , '#button_contextmenu' );

// add sed_pb_modules to settings for save in db
sed_add_settings( array(
    'sed_pb_modules' => array(
        'value'     => '',
        'transport'   => 'postMessage' ,
        'type'        =>  'module'
    ),
));


add_action("after_inner_tab_content_modules" , "other_modules_panel");

function other_modules_panel(){
    require SED_BASE_SED_APP_PATH."/modules/modules/view/other_modules_panel.php";
}

/*******pattern for complex shortcode include child shortcode
sample for sed_img_text shortcode
[sed_img_text]
    optional content if isset content
    [sed_image src='test.png']  [/sed_image]
    [sed_text_paragraph] the test paragraph [/sed_text_paragraph]
[/sed_img_text]

array(
...
"pattern_type"  => "complex" ,
"pattern"       => array(
    'children'    => array(
        'sed_text_paragraph' => 20 ,
        'sed_image'          => 10
    ),
    'content'    => 0
)
)
********/
//developer can using action for add new module to page builder
do_action( 'sed_page_builder',$pagebuilder );
