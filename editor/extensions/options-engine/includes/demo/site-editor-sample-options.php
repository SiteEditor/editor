<?php

function sed_add_sample_toolbar_elements(){
    global $site_editor_app;

    $site_editor_app->toolbar->add_element(
        "layout" ,
        "settings" ,
        "sample-options" ,
        __("Sample Options","site-editor") ,
        "sample_options_element" ,     //$func_action
        "" ,                //icon
        "" ,  //$capability=
        array( ),// "class"  => "btn_default3"
        array( "row" => 1 ,"rowspan" => 2 ),
        array('module' => 'options-engine' , 'file' => 'sample_options.php'),
        //array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
        'all' ,
        array(),
        array()
    );

}

add_action( "sed_editor_init" , "sed_add_sample_toolbar_elements" );

function sed_register_sample_group(){

    SED()->editor->manager->add_group('sed_sample_options', array(
        'capability'        => 'edit_theme_options',
        'theme_supports'    => '',
        'title'             => __("Sample Options", "site-editor"),
        'description'       => __("Sample demo options for developer", "site-editor"),
        'type'              => 'default',
    ));

}

add_action( 'sed_after_init_manager', 'sed_register_sample_group' , 10  );

function sed_sample_options_register(){

    //support full nesting level panels
    $panels = array(

        'panel_id1' =>  array(
            'priority'          => 9,
            'type'              => 'inner_box',
            'title'             => __('My Panel 1', 'textdomain'),
            'description'       => __('My Description', 'textdomain'),
            'option_group'      => 'sed_sample_options' ,
            //'capability'        => '' ,
            //'theme_supports'    => '' ,
            'parent_id'         => "root",
            'atts'              => array() ,
            //'active_callback'   => ''
            'dependency' => array(
                'controls'  =>  array(
                    "control"   => "switch_field_id" ,
                    "value"     => true , //value with @string , values with @array
                    "is_panel"  => true
                )
            )
        ) ,

        'panel_id2' =>  array(
            'priority'          => 8,
            'type'              => 'default',
            'title'             => __('My Panel 2', 'textdomain'),
            'description'       => __('My Description', 'textdomain'),
            'option_group'      => 'sed_sample_options' ,
            //'capability'        => '' ,
            //'theme_supports'    => '' ,
            'parent_id'         => "panel_id1",
            'atts'              => array() ,
            //'active_callback'   => ''
            'dependency' => array(
                'controls'  =>  array(
                    "control"   => "checkbox_field_id" ,
                    "value"     => true , //value with @string , values with @array
                    "is_panel"  => true
                )
            )
        ) ,

        'panel_id3' =>  array(
            'priority'          => 9,
            'type'              => 'expanded',
            'title'             => __('My Panel 3', 'textdomain'),
            'description'       => __('My Description', 'textdomain'),
            'option_group'      => 'sed_sample_options' ,
            //'capability'        => '' ,
            //'theme_supports'    => '' ,
            //'parent_id '        => "root",
            'atts'              => array() ,
            //'active_callback'   => ''
            'dependency' => array(
                'controls'  =>  array(
                    "control"   => "toggle_field_id" ,
                    "value"     => true , //value with @string , values with @array
                    "is_panel"  => true
                )
            )
        ) ,

        'panel_id4' =>  array(
            'priority'          => 18,
            'type'              => 'expanded',
            'title'             => __('My Panel 4', 'textdomain'),
            'description'       => __('My Description', 'textdomain'),
            'option_group'      => 'sed_sample_options' ,
            //'capability'        => '' ,
            //'theme_supports'    => '' ,
            'parent_id'        => "panel_id3",
            'atts'              => array() ,
            //'active_callback'   => ''
        ),

        'panel_id5' =>  array(
            'priority'          => 21,
            'type'              => 'inner_box',
            'title'             => __('My Panel 5', 'textdomain'),
            'description'       => __('My Description', 'textdomain'),
            'option_group'      => 'sed_sample_options' ,
            //'capability'        => '' ,
            //'theme_supports'    => '' ,
            'parent_id'         => "panel_id2",
            'atts'              => array() ,
            //'active_callback'   => ''
        )

    );

    sed_options()->add_panels( $panels );

    sed_options()->add_field( 'radio_field_id' , array(
        'setting_id'        => 'my_setting2',
        'label'             => __('My custom control', 'translation_domain'),
        'type'              => 'radio',
        'priority'          => 10,
        'default'           => 'options3_key',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        'choices'           => array(
            "options1_key"      =>    "options1_value" ,
            "options2_key"      =>    "options2_value" ,
            "options3_key"      =>    "options3_value" ,
            "options4_key"      =>    "options4_value" ,
        ) ,
        'panel'             =>  'panel_id1'
        //'input_attrs'
    ));


    sed_options()->add_field( 'checkbox_field_id' , array(
        'setting_id'        => 'my_setting3',
        'label'             => __('Checkbox', 'translation_domain'),
        'type'              => 'checkbox',
        'priority'          => 8,
        'default'           => false,
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        'panel'             =>  'panel_id1'
        //'input_attrs'
    ));

    sed_options()->add_field( 'color_field_id' , array(
        'setting_id'        => 'my_setting4',
        'label'             => __('Color control', 'translation_domain'),
        'type'              => 'color',
        'priority'          => 8,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        'panel'             =>  'panel_id1' ,
        'dependency' => array(
            'controls'  =>  array(
                "relation"     =>  "and" ,
                array(
                    "control"  => "radio_field_id" ,
                    "value"    => "options2_key" , //value with @string , values with @array
                    "type"     => "exclude"
                ),
                array(
                    "control"  => "checkbox_field_id" ,
                    "value"    => false ,
                )
            )
        )
        //'input_attrs'
    ));

    sed_options()->add_field( 'icon_field_id' , array(
        'setting_id'        => 'my_setting5',
        'label'             => __('Icon control', 'translation_domain'),
        'type'              => 'icon',
        'priority'          => 14,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        'panel'             => 'panel_id2' ,
        'has_border_box'    => false
        //'input_attrs'
    ));

    sed_options()->add_field( 'multi-icon_field_id' , array(
        'setting_id'        => 'my_setting6',
        'label'             => __('Multi Icons control', 'translation_domain'),
        'type'              => 'multi-icon',
        'priority'          => 15,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        'panel'             =>  'panel_id2' ,
        'has_border_box'    => false
        //'input_attrs'
    ));

    sed_options()->add_field( 'image_field_id' , array(
        'setting_id'        => 'my_setting7',
        'label'             => __('Image control', 'translation_domain'),
        'type'              => 'image',
        'priority'          => 16,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        'panel'             =>  'panel_id2' ,
        'has_border_box'    => false
        //'input_attrs'
    ));

    sed_options()->add_field( 'multi-image_field_id' , array(
        'setting_id'        => 'my_setting8',
        'label'             => __('Multi Images control', 'translation_domain'),
        'type'              => 'multi-image',
        'priority'          => 17,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        'panel'             =>  'panel_id3'
        //'input_attrs'
    ));

    sed_options()->add_field( 'multi-check_field_id' , array(
        'setting_id'        => 'my_setting9',
        'label'             => __('Multi Checkbox', 'translation_domain'),
        'type'              => 'multi-check',
        'priority'          => 18,
        'default'           => 'options3_key',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        'choices'           => array(
            "options1_key"      =>    "options1_value" ,
            "options2_key"      =>    "options2_value" ,
            "options3_key"      =>    "options3_value" ,
            "options4_key"      =>    "options4_value" ,
        ) ,
        //'input_attrs',
        'js_params' => array(
            "options_selector" => ".sed-bp-checkbox-input"
        ) ,
        'panel'             =>  'panel_id3' ,
        'has_border_box'    => false

    ));

    sed_options()->add_field( 'select_field_id' , array(
        'setting_id'        => 'my_setting10',
        'label'             => __('Select', 'translation_domain'),
        'type'              => 'select',
        'priority'          => 19,
        'default'           => 'options3_key',
        'subtype'           => 'single',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        'choices'           => array(
            "options1_key"      =>    "options1_value" ,
            "options2_key"      =>    "options2_value" ,
            "options3_key"      =>    "options3_value" ,
            "options4_key"      =>    "options4_value" ,
        ) ,
        'panel'             =>  'panel_id3'
        //'input_attrs'
    ));

    sed_options()->add_field( 'multiselect_field_id' , array(
        'setting_id'        => 'my_setting11',
        'label'             => __('Multi Select', 'translation_domain'),
        'type'              => 'select',
        'priority'          => 20,
        'default'           => 'options3_key',
        'subtype'           => 'multiple',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        'choices'           => array(
            "options1_key"      =>    "options1_value" ,
            "options2_key"      =>    "options2_value" ,
            "options3_key"      =>    "options3_value" ,
            "options4_key"      =>    "options4_value" ,
        ) ,
        'panel'             =>  'panel_id4'
        //'input_attrs'
    ));

    sed_options()->add_field( 'spinner_field_id' , array(
        'setting_id'        => 'my_setting12',
        'label'             => __('Spinner control', 'translation_domain'),
        'type'              => 'spinner',
        'priority'          => 21,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        'panel'             =>  'panel_id4'
        //'input_attrs'
    ));

    sed_options()->add_field( 'text_field_id' , array(
        'setting_id'        => 'my_setting13',
        'label'             => __('Text control', 'translation_domain'),
        'type'              => 'text',
        'priority'          => 22,
        'default'           => 'test value',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        'panel'             =>  'panel_id5'
        //'input_attrs'
    ));

    sed_options()->add_field( 'text_field_id_2' , array(
        'setting_id'        => 'my_setting14',
        'label'             => __('Text control', 'translation_domain'),
        'type'              => 'text',
        'subtype'           => 'email',
        'priority'          => 23,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        'panel'             =>  'panel_id5'
        //'input_attrs'
    ));

    sed_options()->add_field( 'textarea_field_id' , array(
        'setting_id'        => 'my_setting15',
        'label'             => __('Textarea control', 'translation_domain'),
        'type'              => 'textarea',
        'priority'          => 24,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'toggle_field_id' , array(
        'setting_id'        => 'my_setting16',
        'label'             => __('Toggle', 'translation_domain'),
        'type'              => 'toggle',
        'priority'          => 25,
        'default'           => true,
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'switch_field_id' , array(
        'setting_id'        => 'my_setting17',
        'label'             => __('Switch', 'translation_domain'),
        'type'              => 'switch',
        'priority'          => 26,
        'default'           => true,
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        'choices'           => array(
            "on"       =>    "ON" ,
            "off"      =>    "OFF" ,
        ) ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'radio_buttonset_field_id' , array(
        'setting_id'        => 'my_setting18',
        'label'             => __('Radio Buttonset control', 'translation_domain'),
        'type'              => 'radio-buttonset',
        'priority'          => 26,
        'default'           => 'options3_key',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        'choices'           => array(
            "options1_key"      =>    "One" ,
            "options2_key"      =>    "Two" ,
            "options3_key"      =>    "Three" ,
        ) ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'radio_image_field_id' , array(
        'setting_id'        => 'my_setting19',
        'label'             => __('Radio Image control', 'translation_domain'),
        'type'              => 'radio-image',
        'priority'          => 27,
        'default'           => 'options3_key',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        'choices'           => array(
            "options1_key"      =>   SED_ASSETS_URL.'/images/no_pic-110x83.png',
            "options2_key"      =>   SED_ASSETS_URL.'/images/no_pic-110x83.png',
            "options3_key"      =>   SED_ASSETS_URL.'/images/no_pic-110x83.png',
        ) ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'slider_field_id' , array(
        'setting_id'        => 'my_setting20',
        'label'             => __('Slider', 'translation_domain'),
        'type'              => 'slider',
        'priority'          => 28,
        'default'           => 30,
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        //'input_attrs',
        'js_params' => array(
            "min" => 20,
            "max" => 150,
        )
    ));

    sed_options()->add_field( 'sortable_field_id' , array(
        'setting_id'        => 'my_setting21',
        'label'             => __('Sortable control', 'translation_domain'),
        'type'              => 'sortable',
        'priority'          => 29,
        'default'           => 'options3_key',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        'choices'           => array(
            "options1_key"      =>    "One" ,
            "options2_key"      =>    "Two" ,
            "options3_key"      =>    "Three" ,
            "options4_key"      =>    "Four" ,
            "options5_key"      =>    "Five" ,
        ) ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'date_field_id' , array(
        'setting_id'        => 'my_setting22',
        'label'             => __('Date', 'translation_domain'),
        'type'              => 'date',
        'priority'          => 30,
        'default'           => "",
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'dimension_field_id' , array(
        'setting_id'        => 'my_setting23',
        'label'             => __('Dimension', 'translation_domain'),
        'type'              => 'dimension',
        'priority'          => 31,
        'default'           => 0,
        'invalid_value'     => "Invalid Value",
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'code_field_id' , array(
        'setting_id'        => 'my_setting24',
        'label'             => __('Code', 'translation_domain'),
        'type'              => 'code',
        'priority'          => 32,
        'default'           => "",
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        //'input_attrs',
        'js_params' => array(
            "mode" => "html",
        )
    ));

    sed_options()->add_field( 'multi-color_field_id' , array(
        'setting_id'        => 'my_setting25',
        'label'             => __('Multicolor control', 'translation_domain'),
        'type'              => 'multi-color',
        'priority'          => 33,
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        //'input_attrs',
        'choices'     => array(
            'link'    => 'Color',
            'hover'   => 'Hover',
            'active'  => 'Active',
        ),
        'default'     => array(
            'link'    => '#0088cc',
            'hover'   => '#00aaff',
            'active'  => '#00ffff',
        ),
    ));

    sed_options()->add_field( 'file_field_id' , array(
        'setting_id'        => 'my_setting26',
        'label'             => __('Change file', 'translation_domain'),
        'type'              => 'file',
        'priority'          => 34,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'audio_field_id' , array(
        'setting_id'        => 'my_setting27',
        'label'             => __('Change Audio', 'translation_domain'),
        'type'              => 'audio',
        'priority'          => 35,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'video_field_id' , array(
        'setting_id'        => 'my_setting28',
        'label'             => __('Change Video', 'translation_domain'),
        'type'              => 'video',
        'priority'          => 36,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_sample_options',
        'transport'         => 'postMessage' ,
        //'input_attrs'
    ));

}

add_action( "sed_register_sed_sample_options_options" , "sed_sample_options_register" );

/*function register_default_groups(){

    sed_options()->add_group('site', array(
        'capability'        => 'edit_theme_options',
        'theme_supports'    => '',
        'title'             => __("Site Options", "site-editor"),
        'description'       => __("General Site Options", "site-editor"),
        'type'              => 'default',
    ));

    sed_options()->add_group('theme', array(
        'capability'        => 'edit_theme_options',
        'theme_supports'    => '',
        'title'             => __("Theme Options", "site-editor"),
        'description'       => __("Theme Options for any theme", "site-editor"),
        'type'              => 'default',
    ));

    sed_options()->add_group('add_layout', array(
        'capability'        => 'edit_theme_options',
        'theme_supports'    => '',
        'title'             => __("Add Layout", "site-editor"),
        'description'       => __("Add Custom Layout", "site-editor"),
        'type'              => 'default',
    ));

    sed_options()->add_group('pages_layouts', array(
        'capability'        => 'edit_theme_options',
        'theme_supports'    => '',
        'title'             => __("Layout settings", "site-editor"),
        'description'       => __("Page layout settings", "site-editor"),
        'type'              => 'default',
    ));

    sed_options()->add_group('page_general', array(
        'capability'        => 'edit_theme_options',
        'theme_supports'    => '',
        'title'             => __("Page options", "site-editor"),
        'description'       => __("Page general settings", "site-editor"),
        'type'              => 'default',
    ));

    sed_options()->add_group('single_posts', array(
        'capability'        => 'edit_theme_options',
        'theme_supports'    => '',
        'title'             => __("Single posts options", "site-editor"),
        'description'       => __("Single posts settings", "site-editor"),
        'type'              => 'default',
    ));

    sed_options()->add_group('posts_archive', array(
        'capability'        => 'edit_theme_options',
        'theme_supports'    => '',
        'title'             => __("Posts archive options", "site-editor"),
        'description'       => __("Archive settings", "site-editor"),
        'type'              => 'default',
    ));

    sed_options()->add_group('sed_image', array(
        'capability'        => 'edit_theme_options',
        'theme_supports'    => '',
        'title'             => __("Image settings", "site-editor"),
        'description'       => __("Image module settings", "site-editor"),
        'type'              => 'default',
    ));

}

add_action( 'sed_app_register_components', 'register_default_groups' );


function register_site_options(){

    sed_options()->add_panel('panel_id', array(
        'priority'          => 10,
        'type'              => 'inner_panel',
        'title'             => __('My Title', 'textdomain'),
        'description'       => __('My Description', 'textdomain'),
        'option_group'      => 'site_options',
        'capability'        => '',
        'theme_supports'    => '',
        'parent_id '        => "root",
        'atts'              => array(),
        'active_callback'   => ''
    ));

    sed_options()->add_field('field_id' , array(
        'settings'          => 'my_setting',
        'label'             => __('My custom control', 'translation_domain'),
        'type'              => 'text',
        'priority'          => 10,
        'default'           => 'some-default-value',
        //panel or group
        'panel'             => 'panel_id',
        'option_group'      => 'site_options',
    ));

}

add_action( 'sed_register_site_options' , 'register_site_options' );


function register_params( )
{
    $panels = array(

        'panel_id1' =>  array(
            'priority'          => 10,
            'type'              => 'inner_panel',
            'title'             => __('My Title', 'textdomain'),
            'description'       => __('My Description', 'textdomain'),
            'option_group'      => 'group_id' ,
            'capability'        => '' ,
            'theme_supports'    => '' ,
            'parent_id '        => "root",
            'atts'              => array() ,
            'active_callback'   => ''
        ) ,

        new myCustomOptionsPanel( 'panel_id2' , array(
            'priority'          => 10,
            'type'              => 'inner_panel',
            'title'             => __('My Title', 'textdomain'),
            'description'       => __('My Description', 'textdomain'),
            'option_group'      => 'group_id' ,
            'capability'        => '' ,
            'theme_supports'    => '' ,
            'parent_id '        => "root",
            'atts'              => array() ,
            'active_callback'   => ''
        )) ,

        'panel_id3' =>  array(
            'priority'          => 10,
            'type'              => 'inner_panel',
            'title'             => __('My Title', 'textdomain'),
            'description'       => __('My Description', 'textdomain'),
            'option_group'      => 'group_id' ,
            'capability'        => '' ,
            'theme_supports'    => '' ,
            'parent_id '        => "root",
            'atts'              => array() ,
            'active_callback'   => ''
        ) ,

    );

    sed_options()->add_panels( $panels );


    $fields = array(

        'field_id1'=> array(
            'settings' => 'my_setting',
            'label' => __('My custom control', 'translation_domain'),
            'section' => 'my_section',
            'type' => 'text',
            'priority' => 10,
            'default' => 'some-default-value',
            //panel or group
            'panel' => 'panel_id'
        ) ,

        new myCustomField( 'field_id2' , array(
            'settings' => 'my_setting',
            'label' => __('My custom control', 'translation_domain'),
            'section' => 'my_section',
            'type' => 'text',
            'priority' => 10,
            'default' => 'some-default-value',
            //panel or group
            'panel' => 'panel_id'
        )) ,

        'field_id3'=> array(
            'settings' => 'my_setting',
            'label' => __('My custom control', 'translation_domain'),
            'section' => 'my_section',
            'type' => 'text',
            'priority' => 10,
            'default' => 'some-default-value',
            //panel or group
            'panel' => 'panel_id'
        ) ,

    );

    sed_options()->add_fields( $fields );
}

add_action( "sed_load_options_group_id1" , "register_params" );
*/