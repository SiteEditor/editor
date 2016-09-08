<?php
Class SiteEditorThemeFramework{

	function __construct( ){
        $this->page_mata_key = "";
        $this->theme_option_name = "";

        add_action( "sed_site_options_framework" , array( $this , "get_site_options" ) );

        add_action( "sed_theme_options_framework" , array( $this , "get_theme_options" ) );

        add_action( "sed_page_options_framework" , array( $this , "get_page_options" ) );

        add_action( "sed_layout_options_framework" , array( $this , "get_layout_options" ) );

        add_action( "sed_page_content_options_framework" , array( $this , "get_content_options" ) , 10 , 1 );

        add_action( 'sed_app_register' ,  array( $this, 'register_settings' ) );
	}

    function get_site_options(){

        $params = array_merge( $this->get_default_params()['site_options'] , $this->get_params()['site_options'] );

        $sed_options_engine->set_group_params( "sed_site_options" , __("Site Options" , "site-editor") , $params , $this->panels['site_options'] , "site-options" );
    }

    function get_theme_options(){

        $params = array_merge( $this->default_theme_params()['theme_options'] , $this->theme_params()['theme_options'] );

        $sed_options_engine->set_group_params( "sed_theme_options" , __("Theme Options" , "site-editor") , $params , $this->panels['theme_options'] , "theme-options" );
    }


    function get_page_options(){

        $params = array_merge( $this->default_page_params()['page_options'] , $this->page_params()['page_options'] );

        $sed_options_engine->set_group_params( "sed_page_options" , __("Page Options" , "site-editor") , $params , $this->panels['page_options'] , "layout-options" );
    }


    function get_layout_options( $layout ){

        $params = array_merge( $this->default_page_params()['page_options'] , $this->page_params()['page_options'] );

        $sed_options_engine->set_group_params( "sed_page_options" , __("Page Options" , "site-editor") , $params , $this->panels['page_options'] , "page-options" );
    }

    function get_content_options( $info ){

        $group_options = $this->get_group_options( $info )['id'];

        $params = array_merge( $this->default_page_params()[$group_options] , $this->page_params()[$group_options] );

        $sed_options_engine->set_group_params( "sed_" . $group_options  , sprintf( __("%s Options" , "site-editor") , $this->get_group_options( $info )['title'] ) , $params , $this->panels[$group_options] , "content-options" );

    }

    function get_group_options(){

    }

    function add_panel( $id, $args = array() ) {
	    if( is_array($args) )
            $this->panels['site_options'][ $id ] = array_merge( array(
                'id'            => $id  ,
                'title'         => ''  ,
                'capability'    => 'edit_theme_options' ,
                'type'          => 'fieldset' ,
                'description'   => '' ,
                'priority'      => 10
            ) , $args );
	}

    function get_default_params(){

        $params = array();

        return $params;
    }

    function get_params(){

        $params = array();

        return $params;
    }

    function register_settings(){

    }

    function get_settings(){

        $settings = array();

        return $settings;
    }

    function get_default_settings(){

        $settings = array();

        return $settings;
    }

}

new SiteEditorThemeFramework;

Class StarsIdeasTheme extends SiteEditorThemeFramework {

    function __construct( ){
        $this->page_mata_key = "";
        $this->theme_option_name = "";
    }

    function site_params(){

        $params = array(
            'layouts_manager' => array(
                'type'              =>  'custom',
                //'in_box'            =>   true ,
                'html'              =>  $html ,
                'control_type'      => 'layouts_manager' ,
                'control_category'  => 'app-settings' ,
                'settings_type'     => "sed_layouts_settings" ,
            )
        );

        return $params;
    }

    function site_settings(){

        $settings['sed_general_theme_options'] = array(
            'default'        => get_option( 'sed_general_theme_options' ),
            'capability'     => 'manage_options',
            'option_type'    => 'option' ,
            'transport'      => 'postMessage'
        );

        return $settings;
    }


    function theme_params(){

        $params = array(
            'layouts_manager' => array(
                'type'              =>  'custom',
                //'in_box'            =>   true ,
                'html'              =>  $html ,
                'control_type'      => 'layouts_manager' ,
                'control_category'  => 'app-settings' ,
                'settings_type'     => "sed_layouts_settings" ,
            )
        );

        return $params;
    }

    function theme_settings(){

        $settings['sed_general_theme_options'] = array(
            'default'        => get_option( 'sed_general_theme_options' ),
            'capability'     => 'manage_options',
            'option_type'    => 'option' ,
            'transport'      => 'postMessage'
        );

        return $settings;
    }


    function page_params(){

        $params = array(
            'layouts_manager' => array(
                'type'              =>  'custom',
                //'in_box'            =>   true ,
                'html'              =>  $html ,
                'control_type'      => 'layouts_manager' ,
                'control_category'  => 'app-settings' ,
                'settings_type'     => "sed_layouts_settings" ,
            )
        );

        return $params;
    }

    function page_settings(){

        $settings['sed_general_theme_options'] = array(
            'default'        => get_option( 'sed_general_theme_options' ),
            'capability'     => 'manage_options',
            'option_type'    => 'option' ,
            'transport'      => 'postMessage'
        );

        return $settings;
    }

}


/*
All Settings Group for Theme Builder :
1. site settings :
    @like per page , site title , tagline , Front page displays , site description , favicon , custom css , ...
    @save in any options && theme mode
    @not need to scope && preset
    @sample :

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

2. page settings :
    @like page sheet width , page length , backgound , ...
    @save in sed_post_settings post meta
    @sed_post_settings model example :
    $sed_post_settings_model = array(
        "sheet_width"       =>  1100 ,
        "page_length"       =>  "wide"
    );
    @in this settings not allowed using theme mode or options settings
    @this type setting is base
    @public model save in sed_general_page_options
    @sed_general_page_options model example :
    $sed_general_page_options_model = array(
        'page'  =>  array(
            "sheet_width"       =>  1100 ,
            "page_length"       =>  "wide"
        ),
        'post'  =>  array(
            "sheet_width"       =>  1100 ,
            "page_length"       =>  "wide"
        ),
        ...
    );

3. content setting


4. sub theme models ------------- sed_layouts_models
    array(
        'single_post'   =>   array(
            array(
              'order'         =>    10 ,
              'theme_id'      =>    'theme_id_5' ,
              'after_content' =>    true ,
              'main_row'      =>    true
            ),
            array(
              'order'         =>    7 ,
              'theme_id'      =>    'theme_id_7' ,
              'after_content' =>    false ,
              'exclude'       =>    array()
            )
        ),
        'archive'   =>   array(
            array(
              'order'         =>    2 ,
              'theme_id'      =>    'theme_id_5' ,
              'after_content' =>    true ,
            ),
            array(
              'order'         =>    3 ,
              'theme_id'      =>    'theme_id_7' ,
              'after_content' =>    true ,
            )
        ),
        'page'   =>   array(

        ),

    )

5. sub theme models content -------- sed_layouts_content
    array(
      'theme_id_1' =>
          array (
              array (
                'parent_id' => string 'root' (length=4)
                'tag' => string 'sed_row' (length=7)
                'id' => string 'sed-bp-module-row-4-1' (length=21)
                'attrs' => array(),
              ),
              array (
                'parent_id' => string 'root' (length=4)
                'tag' => string 'sed_row' (length=7)
                'id' => string 'sed-bp-module-row-4-1' (length=21)
                'attrs' => array(),
              )
          )
      'theme_id_2' =>
          array (
              array (
                'parent_id' => string 'root' (length=4)
                'tag' => string 'sed_row' (length=7)
                'id' => string 'sed-bp-module-row-4-1' (length=21)
                'attrs' => array(),
              ),
              array (
                'parent_id' => string 'root' (length=4)
                'tag' => string 'sed_row' (length=7)
                'id' => string 'sed-bp-module-row-4-1' (length=21)
                'attrs' => array(),
              )
          )
    )

Shortcode Module Presets
  1. create post type sed_preset
  2. create taxonomy sed_preset_category
  3. example preset for image module :
      preset title : Image preset 1 ,
      preset slug : image-preset-1 ,
      preset category : "image" , //module name
      preset content model : json_encode( array(
          shortcode model 1 ,
          shortcode model 2 ,
          ...
      ));
      preset content : '[sed_image alt="" attachment_id="10" title=""][/sed_image]';

Shortcode page content template
  1. create post type sed_template
  2. create taxonomy sed_template_category
  3. example template :
      template title : template 1 ,
      template slug : template-1 ,
      template category : "landing page" , //module name
      template content model : json_encode( array(
          shortcode model 1 ,
          shortcode model 2 ,
          ...
      ));
      template content : '[sed_image alt="" attachment_id="10" title=""][/sed_image]';

*/



























