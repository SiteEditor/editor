<?php
/*
Module Name: Page Builder
Module URI: http://www.siteeditor.org/modules/modules
Description: Page Builder Extension For Site Editor Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

require_once dirname( __FILE__ ) . DS . "index.php";

$pagebuilder = new PageBuilderApplication();

global $site_editor_app;

$site_editor_app->pagebuilder = $pagebuilder;

function sed_pb_add_toolbar_elements(){

    global $site_editor_app;

    $toolbar = $site_editor_app->toolbar;

    $toolbar->add_new_tab("modules" , __("Modules","site-editor") , "" , "tab" , array( "class" => "modules-tb" ));

    //add new group to modules tab
    $toolbar->add_element_group("modules", "basic", __("Basic", "site-editor"));

    $toolbar->add_element_group("modules", "theme", __("Theme", "site-editor"));

    $toolbar->add_element_group("modules", "stracture", __("structure", "site-editor"));

    $toolbar->add_element_group("modules", "apps", __("Apps", "site-editor"));

    $toolbar->add_element_group("modules", "slideshow", __("Slideshow", "site-editor"));

    $toolbar->add_element_group("modules", "gallery", __("Gallery", "site-editor"));

    $toolbar->add_element_group("modules", "media", __("Media", "site-editor"));

    $toolbar->add_element_group("modules", "socials", __("Socials", "site-editor"));

    $toolbar->add_element_group("modules", "content", __("Content", "site-editor"));

}

add_action( "sed_editor_init" , "sed_pb_add_toolbar_elements" , -9999 );
//add new menu for button module
//$button_menu = $pagebuilder->contextmenu->create_menu( "button" , __("Button","site-editor") , 'button' , 'class' , 'element' , '#button_contextmenu' );

function sed_pb_register_settings(){
// add sed_pb_modules to settings for save in db
    sed_add_settings(array(
        'sed_pb_modules' => array(
            'value' => '',
            'transport' => 'postMessage',
            'type' => 'module'
        ),
    ));
}

add_action( 'sed_app_register' ,  'sed_pb_register_settings' );




function other_modules_panel(){
    require SED_EXT_PATH . "/pagebuilder/view/other_modules_panel.php";
}

add_action("after_inner_tab_content_modules" , "other_modules_panel");

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
