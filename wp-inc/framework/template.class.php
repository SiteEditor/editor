<?php
Class SiteeditorThemeFramework{

	function __construct( ){

	}

    function call_theme_options(){

    }

    function less_compile(){

    }


}

new SiteeditorThemeFramework;


Class SiteeditorThemeOptions{

	function __construct( ){

	}

    //general settings for site like :  per page , site title , tagline , Front page displays , site description , favicon , custom css , ...
    function site_settings(){

    }

    //page sheet width , page length , backgound  ,
    /*
    $general_settings_presets = array(
        "preset_default" , "preset_1" , "preset_2"
    );

    $general_page_settings = array(
        "sheet_width"       => array(
             "preset_default"    =>  1100 ,
             "preset_1"          =>  1200 ,
             "preset_2"          =>  1250 ,
        )  ,
        "page_length"       => array(
             "preset_default"    =>  "wide" ,
             "preset_1"          =>  "wide" ,
             "preset_2"          =>  "wide" ,
        )  ,
        "background_color"  =>  array(
             "preset_default"    =>  "#ccc" ,
             "preset_1"          =>  "#ccc" ,
             "preset_2"          =>  "#fff" ,
        )   ,
    )
    */
    function general_page_settings(){

    }

    // pages content settings , posts content settings , archive content settings , 404 content settings , ....
    function page_content_settings(){

    }

}

$site_settings = array(

    "post_per_page"  => array(
        'default'        => get_option( 'post_per_page' ),
        'capability'     => 'manage_options',
        'option_type'    => 'option' ,
        'transport'      => 'postMessage'
    ) ,

    "site_description"  => array(
        'default'        => get_option( 'site_description' ),
        'capability'     => 'manage_options',
        'option_type'    => 'option' ,
        'transport'      => 'postMessage'
    ) ,

);


$general_page_settings = array(

    "sheet_width"  => array(
        'default'        => 1100,
        'capability'     => 'manage_options',
        'option_type'    => 'base' ,
        'type'           => 'general_page_settings' ,
        'transport'      => 'postMessage'
    ) ,

    "page_length"  => array(
        'default'        => "wide",
        'capability'     => 'manage_options',
        'option_type'    => 'base' ,
        'type'           => 'general_page_settings' ,
        'transport'      => 'postMessage'
    ) ,

);

$general_settings_presets_models = array(

    "default"  =>  array(
        "sheet_width"       =>  1100 ,
        "page_length"       =>  "wide" ,
        "background_color"  =>  "#ccc" ,
    ),

    "preset1"  =>  array(
        "sheet_width"       =>  1200 ,
        "page_length"       =>  "wide" ,
        "background_color"  =>  "#fff" ,
    ) ,

    "preset_2"  =>  array(
        "sheet_width"       =>  1250 ,
        "page_length"       =>  "boxed" ,
        "background_color"  =>  "#ccc" ,
    ) ,

);


$general_settings_sub_theme_presets = array(

    "default"       =>  "default" ,
    "archive"       =>  "preset_1" ,
    "single_post"   =>  "preset_1" ,
    "shop"          =>  "preset_2"

);

// first post meta settings
// 2. sub theme settings
// 3. default created settings


$sed_layouts_models = array(

    "default"       =>  array(

        array(
          'order'       => 0 ,
          'theme_id'    => 'theme_id_1' ,
          'module_id'   => 'module_id_1' ,
          'preset'      => 'preset1' ,
        ) ,

        array(
          'order'       => 2 ,
          'theme_id'    => 'theme_id_5' ,
          'module_id'   => 'module_id_2' ,
          'preset'      => 'preset1' ,
        ) ,

        array(
          'order'       => 11 ,
          'theme_id'    => 'theme_id_9'
          'module_id'   => 'module_id_3' ,
          'preset'      => 'preset5' ,
        ) ,
    ) ,

    "archive"       =>  array(

        array(
          'order'       => 0 ,
          'theme_id'    => 'theme_id_1'
        ) ,

        array(
          'order'       => 4 ,
          'theme_id'    => 'theme_id_3'
        ) ,

        array(
          'order'       => 12 ,
          'theme_id'    => 'theme_id_8'
        )
    )

);


$sed_layouts_content = array(
    'theme_id_1' = > $shortcode_models_array1 ,
    'theme_id_2' = > $shortcode_models_array2 ,
);

$sed_theme_options = array(
    'theme_id_1' => $base_option1 ,
    'theme_id_2' => $base_option2 ,
);

$preset_modules = array(

    'module_id_1' = > array(

        "default"  =>  array(
            "content"       =>  $shortcode_models_array1 ,
            "options"       =>  $base_option1 ,
        ),

        "preset1"  =>  array(
            "content"       =>  $shortcode_models_array2 ,
            "options"       =>  $base_option2 ,
        ) ,

    ),

    'module_id_2' = > array(

        "default"  =>  array(
            "content"       =>  $shortcode_models_array2 ,
            "options"       =>  $base_option2 ,
        ),

        "preset1x"  =>  array(
            "content"       =>  $shortcode_models_array3 ,
            "options"       =>  $base_option3 ,
        ) ,

    ),

);

