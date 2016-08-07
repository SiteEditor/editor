<?php
/*
* Module Name: API Test Module
* Module URI: http://www.siteeditor.org/modules/api-test-module
* Description: API Test Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

class PBAPITestModule extends PBShortcodeClass{

	function __construct() {
		parent::__construct( array(
                "name"        => "sed_api_test",                               //*require
                "title"       => __("API Test Module","site-editor"),                 //*require for toolbar
                "description" => __("Add API Test Module To Page","site-editor"),
                "icon"        => "icon-api-test-module",                              //*require for icon toolbar
                "module"      =>  "api-test-module"         //*require
            ) // Args
		);

	}

    function get_atts(){
        $atts = array(
            "attribute1"    =>  "" ,
            "attribute2"    =>  "" ,
            "attribute3"    =>  "" ,
            "attribute4"    =>  "" ,
            "attribute5"    =>  "" ,
            "attribute6"    =>  "" ,
            "attribute7"    =>  "" ,
            "attribute8"    =>  "" ,
            "attribute9"    =>  "" ,
            "attribute10"   =>  "" ,
            "attribute11"   =>  "" ,
            "attribute12"   =>  "" ,
            "attribute13"   =>  "" ,
            "attribute14"   =>  "" ,
            "attribute15"   =>  true ,
            "attribute16"   =>  "options2_key,options4_key" ,
            "attribute17"   =>  "options3_key" ,
            "attribute18"   =>  "" ,
            "attribute19"   =>  "" ,
            "attribute20"   =>  "" ,
            "attribute21"   =>  "thumbnail" ,
            "attribute22"   =>  "" ,
            "attribute23"   =>  "" ,
            "attribute24"   =>  "" ,
            "attribute25"   =>  0 ,
            "attribute26"   =>  0 ,
            "attribute27"   =>  0 ,
            "attribute28"   =>  true ,
            "attribute29"   =>  "" ,
            "attribute30"   =>  "" ,
            "attribute31"   =>  "" ,
            "attribute32"   =>  "" ,
            "link"          => "" ,
            "link_target"   => "" ,
            "length"        => "boxed" ,
            "image_source"  => "attachment" ,
            "image_url"     => '' ,
            "attachment_id" => 0  ,
            "default_image_size"  => "thumbnail" ,
            "custom_image_size"   => "" ,
            "external_image_size" => ""
        );
        return $atts;
    }

    function add_shortcode( $atts , $content = null ){

    }

    function shortcode_settings(){

        $this->add_panel( 'text_settings_panel' , array(
            'title'         =>  __('Text Fields',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        /*
        $params['param_name'] = array(
            "type"          => "param_type"  , //field type and control type if field type supported in controls types
            "label"         => "param_label" , //field title(label)
            "desc"          => "param_description" ,
            "atts"          => "param_field_attributes" ,
            "dependency"    => "param_dependencies" ,
            "panel"         => "param_panel_id" ,
            "settings_type" => "param_settings_type" ,
            "control_type"  => "param_control_type" ,
            "control_param" => "param_control_parameter" ,
            "is_attr"       => "param_is_shortcode_attribute" ,  //only support in module settings. if (param_name === shortcode_attribute ) is_attr = true;
            "attr_name"     => "param_shortcode_attribute_name" , //only support in module settings. should is_attr = true , if (param_name === shortcode_attribute ) attr_name = param_name;
            "control_category" => "param_control_category" , // in modules as default control_category is 'module-settings' , values include  'module-settings' || 'style-editor' || other custom categories
            "value"         => "param_value" , //in module settings as default , value equal to shortcode attribute value
            "priority"      => "param_priority" ,
            "html"          => "param_html" , //only support if type === custom
            "options"       => "param_options" , //types support : select , multi-checkbox , multi-select,
            "groups"        => "param_select_groups" , //only for select and multi-select type
        );
        */

        $params = array();

        $params['attribute1'] = array(
            "type"          => "text" ,
            "label"         => __("Text Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "placeholder"   => __("Enter Your text", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "text_settings_panel"
        );

        $params['attribute2'] = array(
            "type"          => "tel" ,
            "label"         => __("Tel Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "placeholder"   => __("E.g +989190765018", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "text_settings_panel"
        );

        $params['attribute3'] = array(
            "type"          => "password" ,
            "label"         => __("Password Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "placeholder"   => __("password", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "text_settings_panel"
        );

        $params['attribute4'] = array(
            "type"          => "search" ,
            "label"         => __("Search Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "placeholder"   => __("Keyword ...", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "text_settings_panel"
        );

        $params['attribute5'] = array(
            "type"          => "url" ,
            "label"         => __("Url Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "placeholder"   => __("E.g www.siteeditor.org", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "text_settings_panel"
        );

        $params['attribute6'] = array(
            "type"          => "email" ,
            "label"         => __("Email Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "placeholder"   => __("E.g info@siteeditor.org", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "text_settings_panel"
        );

        $params['attribute7'] = array(
            "type"          => "date" ,
            "label"         => __("Date Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "text_settings_panel"
        );

        $params['attribute8'] = array(
            "type"          => "time" ,
            "label"         => __("Time Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "placeholder"   => __("Enter Your paragraph", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "text_settings_panel"
        );

        $params['attribute9'] = array(
            "type"          => "textarea" ,
            "label"         => __("Textarea Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "placeholder"   => __("Enter Your paragraph", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "text_settings_panel"
        );

        $params['attribute10'] = array(
            "type"          => "range" ,
            "label"         => __("Range Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
                "min"          =>    0 ,
                "max"          =>    100
            ),
            "panel"         => "text_settings_panel"
        );


        $this->add_panel( 'select_settings_panel' , array(
            'title'         =>  __('Select Fields',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 10 ,
        ) );

        $params['attribute11'] = array(
            "type"          => "select" ,
            "label"         => __("Single Select Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "options"       =>  array(
                "options1_key"      =>    "options1_value" ,
                "options2_key"      =>    "options2_value" ,
                "options3_key"      =>    "options3_value" ,
                "options4_key"      =>    "options4_value" ,
            ),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "select_settings_panel" ,
        );

        $params['attribute12'] = array(
            "type"          => "multi-select" ,
            "label"         => __("Multiple Select Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "options"       =>  array(
                "options1_key"      =>    "options1_value" ,
                "options2_key"      =>    "options2_value" ,
                "options3_key"      =>    "options3_value" ,
                "options4_key"      =>    "options4_value" ,
            ),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "select_settings_panel" ,
        );

        $params['attribute13'] = array(
            "type"          => "select" ,
            "label"         => __("optgroup Single Select Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "optgroup"      => true ,
            "groups"        => array(
                "group1_key"    =>   "group1_title"  ,
                "group2_key"    =>   "group2_title"  ,
            ),
            "options"       =>  array(
                "group1_key"    =>  array(
                    "options1_key"      =>    "options1_value" ,
                    "options2_key"      =>    "options2_value" ,
                ) ,

                "group2_key"    => array(
                    "options3_key"      =>    "options3_value" ,
                    "options4_key"      =>    "options4_value" ,
                )
            ),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "select_settings_panel" ,
        );

        $params['attribute14'] = array(
            "type"          => "multi-select" ,
            "label"         => __("optgroup multi Select Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "optgroup"      => true ,
            "groups"        => array(
                "group1_key"    =>   "group1_title"  ,
                "group2_key"    =>   "group2_title"  ,
            ),
            "options"       =>  array(
                "group1_key"    =>  array(
                    "options1_key"      =>    "options1_value" ,
                    "options2_key"      =>    "options2_value" ,
                ) ,

                "group2_key"    => array(
                    "options3_key"      =>    "options3_value" ,
                    "options4_key"      =>    "options4_value" ,
                )
            ),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "select_settings_panel" ,
        );

        $params['attribute15'] = array(
            "type"          => "checkbox" ,
            "label"         => __("Checkbox Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
        );

        $params['attribute16'] = array(
            "type"          => "multi-checkbox" ,
            "label"         => __("Multi Checkbox Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "options"       =>  array(
                "options1_key"      =>    "options1_value" ,
                "options2_key"      =>    "options2_value" ,
                "options3_key"      =>    "options3_value" ,
                "options4_key"      =>    "options4_value" ,
            ),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            )
        );

        $params['attribute17'] = array(
            "type"          => "radio" ,
            "label"         => __("Radio Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "options"       =>  array(
                "options1_key"      =>    "options1_value" ,
                "options2_key"      =>    "options2_value" ,
                "options3_key"      =>    "options3_value" ,
                "options4_key"      =>    "options4_value" ,
            ),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
        );

        $params['attribute19'] = array(
            "type"          => "color" ,
            "label"         => __("Color Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
        );


        $params['attribute20'] = array(
            "type"          => "image" ,
            "label"         => __("Single Image Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            'remove_btn'        => true ,
            "control_param"     => array(
                "rel_size_control"          => "sed_api_test_attribute21"
            ),
        );

        $params['attribute21'] = array(
            "type"          => "image_size" ,
            "label"         => __("Image Size Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            'dependency' => array(
                'controls'  =>  array(
                    "control"  => "attribute20" ,
                    "values"   => array( "" , 0 ),
                    "type"     => "exclude"
                )
            )
        );

        $params['attribute22'] = array(
            'type'              => 'video',
            'label'             => __('Video Field (MP4)', 'site-editor'),
            'desc'              => __('the Video OGV Upload option allows you to upload a .OGV format of your video file. .OGV files are optional. You can choose a video with this format from the library by clicking on the button in this section.','site-editor'),
            "control_param"     => array(
                "subtypes"          => array( "mp4" )
            ),
            //"panel"   => "video_background_row_container"
        );

        $params['attribute23'] = array(
            'type'              => 'audio',
            'label'             => __('Audio Field', 'site-editor'),
            'desc'              => __('This feature lets you upload a Mp3 audio; for compatibility it is required to upload files with this format.','site-editor'),
            "control_param"     => array(
                "subtypes"          => array( "mp3" )
            ),
        );

        $params['attribute24'] = array(
            'type'              => 'file',
            'label'             => __('File Field', 'site-editor'),
            'desc'              => __('Poll File For Download','site-editor'),
            "selcted_type"      => 'single',
            "control_param"     => array(
                //"subtypes"          => array( "zip" , "rar" , "pdf" ) ,
                "lib_title"         => __( "Media Library" , "site-editor" ),
                "btn_title"         => __( "Select File" , "site-editor" ),
                "support_types"     => array( "archive" , "document" )  //"archive" , "document" , "spreadsheet" , "interactive" , "text" , "audio" , "video" , "image" || "all" ----- only is array
            )
        );


        $this->add_panel( 'spinner_settings_panel' , array(
            'title'         =>  __('Spinner Fields',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 10 ,
        ) );

        $params['attribute18'] = array(
            "type"          => "spinner" ,
            "label"         => __("Spinner Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "spinner_settings_panel"
        );

        $lock_id = "sed_pb_sed_api_test_attribute28";

        $spinner_class = 'sed-spinner-api-test-module';
        $spinner_class_selector = '.' . $spinner_class;

        $params['attribute25'] = array(
            "type"          => "spinner" ,
            "label"         => __("Spinner1 with lock", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2 " . $spinner_class ,
                "data-custom"  =>    "test" ,
            ),
            'control_param'     =>  array(
                'lock'    => array(
                    'id'       => $lock_id,
                    'spinner'  => $spinner_class_selector,
                    'controls' => array( "sed_api_test_attribute27" , "sed_api_test_attribute26" )
                ),
                'min'   =>  0 ,
            ),
            "panel"         => "spinner_settings_panel"
        );

        $params['attribute26'] = array(
            "type"          => "spinner" ,
            "label"         => __("Spinner2 with lock", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2 " . $spinner_class ,
                "data-custom"  =>    "test" ,
            ),
            'control_param'     =>  array(
                'lock'    => array(
                    'id'       => $lock_id,
                    'spinner'  => $spinner_class_selector,
                    'controls' => array( "sed_api_test_attribute27" , "sed_api_test_attribute25" )
                ),
                'min'   =>  0 ,
            ),
            "panel"         => "spinner_settings_panel"
        );

        $params['attribute27'] = array(
            "type"          => "spinner" ,
            "label"         => __("Spinner3 with lock", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2 " . $spinner_class,
                "data-custom"  =>    "test"  ,
            ),
            'control_param'     =>  array(
                'lock'    => array(
                    'id'       => $lock_id,
                    'spinner'  => $spinner_class_selector,
                    'controls' => array( "sed_api_test_attribute26" , "sed_api_test_attribute25" )
                ),
                'min'   =>  0 ,
            ),
            "panel"         => "spinner_settings_panel"
        );

        $params['attribute28'] = array(
            "type"          => "checkbox" ,
            "label"         => __("Spinner Lock Field", "site-editor"),
            "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
            'atts'  => array(
                "class" =>   "sed-lock-spinner"
            ) ,
            'control_param'     =>  array(
                'spinner' =>  $spinner_class_selector ,
                'controls' => array( "sed_api_test_attribute27" , "sed_api_test_attribute26" , "sed_api_test_attribute25" )
            ),
            'subtype' =>  'single' ,
            'control_type'      =>  "spinner_lock" ,
            "panel"         => "spinner_settings_panel"
        );

        $params['attribute29'] = array(
            "type"          => "icon" ,
            "label"         => __("Icon Field", "site-editor"),
            "desc"          => __("This option allows you to set a icon for your module.", "site-editor"),
            'remove_btn'    => true ,
        );

        $params['icon_color'] = array(
            "type"              => "color" ,
            "label"             => __("Icon Color Field", "site-editor"),
            "desc"              => "",
            "value"             => "#000000" ,
            'control_param'     =>  array(
                'selector'          =>  '.my-icon-single' ,
                'style_props'       =>  "color" ,
            ),
            'control_category'  => "style-editor" ,
            'settings_type'     =>  "font_color",
        );

        $params['icon_size'] = array(
            "type"              => "spinner" ,
            "label"             => __("Icon Size Field", "site-editor"),
            "desc"              => "",
            "value"             => 16 ,
            'control_param'     =>  array(
                'selector'          =>  '.my-icon-single' ,
                'style_props'       =>  "font-size" ,
            ),
            'control_category'  => "style-editor" ,
            'settings_type'     =>  "font_size",
        );

        $params['attribute30'] = array(
            "type"          => "multi-icon" ,
            "label"         => __("Select Icons Field", "site-editor"),
            "desc"          => __("This option allows you to set a icon for your module.", "site-editor"),
        );


        $params['icon_color_group'] = array(
            "type"              => "color" ,
            "label"             => __("Icon Color Group Field", "site-editor"),
            "desc"              => "",
            "value"             => "#000000" ,
            'control_param'     =>  array(
                'selector' =>  '.icon-group-single' ,
                'style_props'       =>  "color" ,
            ),
            'control_category'  => "style-editor" ,
            'settings_type'     =>  "font_color",
        );

        $params['icon_size_group'] = array(
            "type"              => "spinner" ,
            "label"             => __("Icon Size Group Field", "site-editor"),
            "desc"              => "",
            "value"             => 16 ,
            'control_param'     =>  array(
                'selector'          =>  '.icon-group-single' ,
                'style_props'       =>  "font-size" ,
            ),
            'control_category'  => "style-editor" ,
            'settings_type'     =>  "font_size",
        );

        $params['attribute32'] = array(
            "type"          => "multi-image" ,
            "label"         => __("Select Images Field", "site-editor"),
            "desc"          => __("This option allows you to set a icon for your module.", "site-editor"),
        );

        /*
        "animation"  =>  array(
            "type"          => "animation" ,
            "label"         => __("Animation Settings", "site-editor"),
        ),
        */
        $params['animation'] = array(
            "type"          => "animation" ,
            "label"         => __("Animation Settings", "site-editor"),
            'dependency' => array(
                'controls'  =>  array(
                    "control"  => "length" ,
                    "value"    => "boxed" ,
                    "type"     => "include"
                )
            )
        );

        /*
        "skin"  =>  array(
            "type"          => "skin" ,
            "label"         => __("Change skin", "site-editor"),
        ),
        */
        $params['skin'] = array(
            "type"          => "skin" ,
            "label"         => __("Change skin Control", "site-editor"),
        );

        /*
        "spacing"  =>  array(
            "type"          => "spacing" ,
            "label"         => __("Spacing", "site-editor"),
            "value"         => "20 10 10 0"
        ),
        */
        $params['spacing'] = array(
            "type"          => "spacing" ,
            "label"         => __("Spacing", "site-editor"),
            "value"         => "20 10 10 0"
        );

        $params['style_color'] = array(
            "type"              => "color" ,
            "label"             => __("Style Editor Color Field", "site-editor"),
            "desc"              => "",
            "value"             => "#FF0033" ,
            'control_param'     =>  array(
                'selector' =>  '.style-color-test' ,
                'style_props'       =>  "color" ,
            ),
            'control_category'  => "style-editor" ,
            'settings_type'     =>  "font_color",
        );

        $params['style_bg_color'] = array(
            "type"              => "color" ,
            "label"             => __("background Color", "site-editor"),
            "desc"              => "",
            "value"             => "#FFFFFF" ,
            'control_param'     =>  array(
                'selector' =>  'sed_current' ,
                'style_props'       =>  "background-color" ,
            ),
            'control_category'  => "style-editor" ,
            'settings_type'     =>  "background_color",
        );

        /*
        "align"  =>  array(
            "type"          => "align" ,
            "label"         => __("Align", "site-editor"),
            "value"         => "center"
        ),
        */
        $params['align'] = array(
            "type"          => "align" ,
            "label"         => __("Align", "site-editor"),
            "value"         => "center"
        );

        $params['row_container'] = array(
            "type"          => "row_container" ,
            "label"         => __("Row Container Settings", "site-editor")
        );

        /*
        "link" => array(
            "type"          => "link" ,
            "label"         => __("Link Panel Settings", "site-editor"),
        ),
        */
        $params['link'] = array(
            "type"          => "link" ,
            "label"         => __("Link Panel Settings", "site-editor"),
            "dependency"    => array(
                'controls'  =>  array(
                    "control"  => "length" ,
                    "value"    => "wide" ,
                    "type"     => "include" ,
                    "is_panel" => true
                )
            )
        );

        $params['length'] = array(
            "type"          => "length" ,
            "label"         => __("Length", "site-editor"),
        );

        $params['group_skin'] = array(
            'type'       =>  "group_skin" ,
            'value'      =>  "default",
            'sub_module' =>  "image",
            'group'      =>  "image_thumb",
            'label'      =>  __('Images Change Skin', 'site-editor'),
            'control_param' =>  array(
                "support"  =>  array(
                    "type"     =>  "exclude" ,
                    "fields"   =>  array(
                        "tape-style"

                     )
                )
            ),
        );

        $params['change_image_panel'] = array(
            "type"          => "sed_image" ,
            "label"         => __("Select Image Panel", "site-editor"),
        );

        $dropdown_html = "";
        $dropdown_control = "sed_api_test_attribute31";
        ob_start();
        ?>
            <div class="dropdown" id="sed-app-control-<?php echo $dropdown_control ;?>">

                  <div class="dropdown-content sed-dropdown content">
                      <div>
                        <ul>
                            <li>
                            <a class="heading-item" href="#"><?php echo __("Custom Dropdown Control" ,"site-editor");  ?></a>
                            </li>
                            <li>
                             <ul class="box-items">
                                <li class="dropdown-item-selector selected-item" data-value="value1" >value1<a  href="#"></a></li>
                                <li class="dropdown-item-selector" data-value="value2" ><a href="#">value2</a></li>
                                <li class="dropdown-item-selector" data-value="value3" ><a  href="#">value3</a></li>
                                <li class="dropdown-item-selector" data-value="value4" ><a  href="#">value4</a></li>
                                <li class="dropdown-item-selector" data-value="value5" ><a  href="#">value5</a></li>
                                <li class="dropdown-item-selector" data-value="value6" ><a  href="#">value6</a></li>
                                <li class="dropdown-item-selector" data-value="value7" ><a  href="#">value7</a></li>
                                <li class="dropdown-item-selector" data-value="value8" ><a  href="#">value8</a></li>
                                <li class="dropdown-item-selector" data-value="value9" ><a  href="#">value9</a></li>
                                <li class="dropdown-item-selector" data-value="value10" ><a  href="#">value10</a></li>
                                <li class="clr"></li>
                             </ul>
                            </li>
                        </ul>
                      </div>
                  </div>

          </div>
        <?php
        $dropdown_html = ob_get_contents();
        ob_end_clean();

        $params['attribute31'] = array(
            'type'              =>  'dropdown',
            'in_box'            =>   true ,
            'html'              =>  $dropdown_html ,
            'control_param'     =>  array(
                'options_selector'    => '.dropdown-item-selector',
                'selected_class'      => 'selected-item'
            ),
        );


        return $params;
    }

    /*
    * @New dependency (relations) Api
      $params['animation'] = array(
        "type"          => "animation" ,
        "label"         => __("Animation Settings", "site-editor"),
        "dependency"    => array(
            'controls'  =>  array(
                "control"  => "length" ,
                "value"    => "boxed" ,
                "type"     => "include"
            ),
        )
      );
      "animation"  =>  array(
          "type"          => "animation" ,
          "label"         => __("Animation Settings", "site-editor"),
          "dependency"    => array(
              'controls'  =>  array(
                  "control"  => "length" ,
                  "value"    => "boxed" ,
                  "type"     => "include"
              ),
          )
      ),

    */

    function contextmenu( $context_menu ){
        $api_test_menu = $context_menu->create_menu("api-test-module" , __("API Test Module","site-editor") , 'api-test-module' , 'class' , 'element' , '' , "sed_api_test" , array() );
    }
}

new PBAPITestModule();
global $sed_pb_app;

/**
* Register module with siteeditor.
*/
$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "api-test-module",
    "title"       => __("API Test Module","site-editor"),
    "description" => __("Add Full Customize API Test Module","site-editor"),
    "icon"        => "icon-api-test-module",
    "type_icon"   => "font",
    "shortcode"   => "sed_api_test",
    "tpl_type"    => "underscore"
    //"sub_modules"   => array('title', 'paragraph', 'image', 'icons' , 'separator'),
    //"js_module"   => array( 'sed_api_test _module_script', 'api-test-module /js/sed-api-test-module.min.js', array('sed-frontend-editor') )
));
