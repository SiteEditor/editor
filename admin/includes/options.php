<?php

$tabs = array(
    "dashboard"                 => __('Dashboard' , 'site-editor' ),
    "general-settings"          => __('General Settings' , 'site-editor' ),
    /*"favicon"                   => __('Favicon Options' , 'site-editor' ),
    "colors"                    => __( 'Colors' , 'site-editor' ) ,
    "custom_font"               => __( 'Custom Font' , 'site-editor' ) ,
    "typography"                => __( 'Typography' , 'site-editor' ) ,
    "custom_css"                => __( 'Custom css' , 'site-editor' ) ,*/
    "edit_pages"                => __( 'Pages Edit' , 'site-editor' ) ,
    /*"theme_less_compile"        => __( 'Theme Less Compile' , 'site-editor' ) ,
    "import_export"             => __( 'import & export' , 'site-editor') ,*/
);

$items = array(
    /*"general"   => array(
        "tracking_code"      => array(
            "type"      =>"textarea",
            "label"     => __('Tracking Code' , 'site-editor' ) ,
            "desc"      => __('Paste your Google Analytics (or other) tracking code here. This will be added into the header template of your theme. Please put code inside script tags.' , 'site-editor' ),
            "std"       => ""
        ),
        "before_head"      => array(
            "type"      =>"textarea",
            "label"     => __("Space before &lt;/head&gt;" , 'site-editor' ) ,
            "desc"      => __('Add code before the </head> tag.' , 'site-editor' ),
            "std"       => ""
        ),
        "before_body"      => array(
            "type"      =>"textarea",
            "label"     => __("Space before &lt;/body&gt;" , 'site-editor' )." </body>" ,
            "desc"      => __('Add code before the </body> tag.' ),
            "std"       => ""
        ),

    ),

    "favicon"   => array(
        "favicon"      => array(
            "type"      => "uploader",
            "label"     => __('Favicon' , 'site-editor' )." </body>" ,
            "desc"      => __('Favicon for your website (16px x 16px).' ),
            "std"       => ""
        ),
        "iphone_icon"      => array(
            "type"      => "uploader",
            "label"     => __('Apple iPhone Icon Upload' , 'site-editor' ) ,
            "desc"      => __('Favicon for Apple iPhone (57px x 57px).' ),
            "std"       => ""
        ),
        "iphone_retina_icon"      => array(
            "type"      => "uploader",
            "label"     => __('Apple iPhone Retina Icon Upload' , 'site-editor' ) ,
            "desc"      => __('Favicon for Apple iPhone Retina Version (114px x 114px' ),
            "std"       => ""
        ),
        "ipad_icon"      => array(
            "type"      => "uploader",
            "label"     => __('Apple iPad Icon Upload' , 'site-editor' ) ,
            "desc"      => __('Favicon for Apple iPad (72px x 72px).' ),
            "std"       => ""
        ),
        "ipad_retina_icon"      => array(
            "type"      => "uploader",
            "label"     => __('Apple iPad Retina Icon Upload' , 'site-editor' ) ,
            "desc"      => __('Favicon for Apple iPad Retina Version (144px x 144px).' ),
            "std"       => ""
        ),
    ),

    "custom_font"   => array(
        "custom_font_title"      => array(
            "type"      =>"text",
            "label"     => __('Custom Font Title' , 'site-editor' ) ,
            "desc"      => __('This field is required' , 'site-editor'  ) ,
            'std'      =>'',
        ),
        "custom_font_name"      => array(
            "type"      =>"text",
            "label"     => __('Custom Font Family' , 'site-editor' ) ,
            "desc"      => __('This field is required' , 'site-editor'  ) ,
             'std'      =>'',
        ),
        "custom_font_woff" => array(
            "type"      => "uploader",
            "label"     => __('Custom Font .woff' , 'site-editor' ) ,
            "desc"      => __('Upload the .woff font file, This field is required' ),
            "std"       => ""
        ),
        "custom_font_ttf"      => array(
            "type"      => "uploader",
            "label"     => __('Custom Font .ttf' , 'site-editor' ) ,
            "desc"      => __('Upload the .ttf font file, This field is required' ),
            "std"       => ""
        ),
        "custom_font_svg"      => array(
            "type"      => "uploader",
            "label"     => __('Custom Font .svg' , 'site-editor' ) ,
            "desc"      => __('Upload the .svg font file, This field is required' ),
            "std"       => ""
        ),
        "custom_font_eot"      => array(
            "type"      => "uploader",
            "label"     => __('Custom Font .eot' , 'site-editor' ) ,
            "desc"      => __('Upload the .eot font file, This field is required' ),
            "std"       => ""
        ),
    ),

    "import_export"      => array(
        "import_settings"      => array(
            "type"          => "html",
            "label"         => __('Import Demo Data' , 'site-editor' ) ,
            "html"          => 'import_demo_data'//$this->pages_edit_links( )
        ),
    ),

    "custom_css"      => array(
        "custom_css"      => array(
            "type"      =>"textarea",
            "label"     => __('Custom css' , 'site-editor' ) ,
            "desc"      => __('Paste your CSS code, do not include any tags or HTML in this field. Any custom CSS entered here will override the theme CSS. In some cases, the !important tag may be needed.' , 'site-editor' ),
            "std"       => ""
        ),
    ),*/

    "dashboard"  => array(

        "dashboard_settings"      => array(
            "type"          => "html",
            "label"         => __('Welcome to SiteEditor' , 'site-editor' ) ,
            "html"          => 'dashboard'//$this->pages_edit_links( )
        ),
        
    ),

    "general-settings"      => array(

        "developer-sample-options"      => array(
            "type"          => "checkbox",
            "label"         => __('Developer Sample Options' , 'site-editor' ) ,
            "desc"          => __('Show Developer Sample Options on Site Editor Layout Tab' , 'site-editor' ) ,
            "std"           => ''
        ),

    ),

    "edit_pages"      => array(
        "edit_pages"      => array(
            "type"          => "html",
            "label"         => __('Extra Pages Edit' , 'site-editor' ) ,
            "html"          => 'pages_edit_links'//$this->pages_edit_links( )
        ),
    ),

    /*"theme_less_compile"      => array(
        "theme_less_compile"      => array(
            "type"          => "html",
            "label"         => __('Theme Less Compile' , 'site-editor' ) ,
            "html"          => 'theme_less_compile'//$this->pages_edit_links( )
        ),
    ),

    "colors"      => array(

        "sed-color-palette"      => array(
            "type"          => "html",
            "label"         => __('Color Palette' , 'site-editor' ) ,
            "desc"          => __('Use color palette Or select custom and create your own custom palette using the colors below.' , 'site-editor' ) ,
            "html"          => 'color_palette',//$this->color_palette( "sed-color-palette" ) ,
            "std"           => 'custom'
        ),

        "sed-main-color"      => array(
            "type"          => "color",
            "label"         => __('Main Color' , 'site-editor' ) ,
            "desc"          => __('using for active items , icons , hover and .... ' , 'site-editor' ) ,
             'std'          => '#19cbe5',
        ),

        "sed-perfect-main-color"      => array(
            "type"      => "color",
            "label"     => __('Perfect Main Color' , 'site-editor' ) ,
            "desc"          => __('one perfect color for main color' , 'site-editor' ) ,
            'std'      => '#FAC300',
        ),

        "sed-base-color1"      => array(
            "type"      => "color",
            "label"     => __('Base Color 1' , 'site-editor' ) ,
            "desc"          => __('using for backgrounds in body and menu and other area basic and ...' , 'site-editor' ) ,
            'std'      => '#FFFFFF',
        ),

        "sed-base-color2"      => array(
            "type"      => "color",
            "label"     => __('Base Color 2' , 'site-editor' ) ,
            "desc"          => __('using for border color & gradients & heading background & ...' , 'site-editor' ) ,
            'std'      => '#D5D5D5',
        ),

        "sed-base-color3"      => array(
            "type"      => "color",
            "label"     => __('Base Color 3' , 'site-editor' ) ,
            "desc"          => __('using for footer & row backgrounds & base text color & ...' , 'site-editor' ) ,
            'std'      => '#000000',
        ),
    ),

    "typography"      => array(

        "font-family-base"      => array(
            "type"          => "html",
            "label"         => __('Select Base Font Family' , 'site-editor' ) ,
            //"desc"          => __('Use color palette Or select custom and create your own custom palette using the colors below.' , 'site-editor' ) ,
            "html"          => 'get_font_families', //$this->get_font_families( "font-family-base" )  ,
            'std'           => 'Open Sans,arial,helvetica,sans-serif'
        ),


        "font-size-base"      => array(
            "type"          => "text",
            "label"         => __('Select Base Font Size' , 'site-editor' ) ,
            //"desc"          => __('Use color palette Or select custom and create your own custom palette using the colors below.' , 'site-editor' ) ,
            "std"          => "13px"//$this->color_palette( "sed-color-palette" )
        ),

        "line-height-base"      => array(
            "type"          => "text",
            "label"         => __('Select base Line Height' , 'site-editor' ) ,
            //"desc"          => __('Use color palette Or select custom and create your own custom palette using the colors below.' , 'site-editor' ) ,
            "std"           => 1.758571429//$this->color_palette( "sed-color-palette" )
        ),

        "headings-font-family"      => array(
            "type"          => "html",
            "label"         => __('Select Headings Font Family' , 'site-editor' ) ,
            //"desc"          => __('Use color palette Or select custom and create your own custom palette using the colors below.' , 'site-editor' ) ,
            "html"          => 'get_font_families',//$this->get_font_families( "headings-font-family" )
            'std'           =>'Open Sans,arial,helvetica,sans-serif',
        ),

        "headings-line-height"      => array(
            "type"          => "text",
            "label"         => __('Select Headings Line Height' , 'site-editor' ) ,
            //"desc"          => __('Use color palette Or select custom and create your own custom palette using the colors below.' , 'site-editor' ) ,
            "std"          => 1.43//$this->color_palette( "sed-color-palette" )
        ),

        "headings-font-weight"   => array(
            "type"          => "text",
            "label"         => __('Select Headings Font Weight' , 'site-editor' ) ,
            //"desc"          => __('Use color palette Or select custom and create your own custom palette using the colors below.' , 'site-editor' ) ,
            "std"          => 400//$this->color_palette( "sed-color-palette" )
        ),

        "gfont_settings"   => array(
            "label" => __("Google Font Settings", "site-editor"),
            "desc" => __("Adjust the settings below to load different character sets and types for fonts. More character sets and types equals to slower page load. Please read <a href='http://stars-ideas.com/typography'>How to configure google web fonts settings</a> for more info.", "site-editor"),
            "std" => "400,400italic,700,700italic:latin,greek-ext,cyrillic,latin-ext,greek,cyrillic-ext,vietnamese",
            "type" => "text"
         )

    ),*/

);
