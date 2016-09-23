<?php
Class SiteEditorThemeFramework{

    /*
     * 1. add panels done
     * 2. custom settings done
     * 3. content settings done
     * 4. add scope tab to content settings && page options done
     * 5. content settings load & tab done
     * 6. create all default settings
     * 7. test create all type settings
     * 8. style editor settings test
     * 9. close & open dialog & ajax
     * 10. dependencies
     * 11. call options in theme
     */
	function __construct( ){
        $this->post_mata_key        = "sed_post_settings";
        $this->theme_option_name    = "sed_theme_options";
        $this->layout_option_name   = "sed_layout_options";

        //add_action( "sed_site_options_framework" , array( $this , "get_site_options" ) );
        add_action( "sed_ajax_load_options_sed_site_options" , array( $this, "get_site_options" ) );

        add_action( "sed_ajax_load_options_sed_theme_options" , array( $this , "get_theme_options" ) );

        add_action( "sed_ajax_load_options_sed_page_options" , array( $this , "get_page_options" ) );

        add_action( "sed_ajax_load_options_sed_content_options" , array( $this , "get_content_options" ) , 10 , 1 );

        add_action( 'sed_app_register' ,  array( $this, 'register_settings' ) );
	}

    public function get_site_options(){
        global $sed_options_engine;

        $params = array();

        $site_params = array_merge( $this->default_site_options()['params'] , $this->site_options()['params'] );
        $panels = array_merge( $this->default_site_options()['panels'] , $this->site_options()['panels'] );

        foreach( $site_params AS $id => $args ){
            $args['control_category']  = 'site-settings';
            $params[$id] = $args;
        }

        $sed_options_engine->set_group_params( "sed_site_options" , __("Site Options" , "site-editor") , $params , $panels , "site-settings" );
    }

    private function view_tab_scope( $layout = true ){
        ob_start();

        ?>
            <div class="sed-tab-scope-options" sed-role="tab-scope">
                <ul>
                    <li data-type="public-scope" class="tab-scope-item active"><a href="#"><span><?php echo __( "Public" , "site-editor");?></span></a></li>
                    <?php if( $layout === true ){ ?>
                    <li data-type="layout-scope" class="tab-scope-item"><a href="#"><span><?php echo __( "Current Layout" , "site-editor");?></span></a></li>
                    <?php } ?>
                    <li data-type="page-customize-scope" class="tab-scope-item"><a href="#"><span><?php echo __( "Current Page" , "site-editor");?></span></a></li>
                </ul>
            </div>
        <?php

        return ob_get_clean();
    }

    public function get_theme_options(){
        global $sed_options_engine;

        $params = array();
        $panels = array_merge( $this->default_theme_options()['panels'] , $this->theme_options()['panels'] );

        $theme_params = array_merge( $this->default_theme_options()['params'] , $this->theme_options()['params'] );

        foreach( $theme_params AS $id => $args ){
            $args['settings_type'] = $this->theme_option_name . "[" . $args['settings_type'] . "]";
            $args['control_category']  = 'theme-settings';
            $params[$id] = $args;
        }

        $sed_options_engine->set_group_params( "sed_theme_options" , __("Theme Options" , "site-editor") , $params , $panels , "theme-settings" );
    }

    private function get_panel( $id, $args = array() ){
        /**
         * Define the array of defaults
         */
        $defaults = array(
            'id'            => $id  ,
            'title'         => ''  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 10
        );

        /**
         * Parse incoming $args into an array and merge it with $defaults
         */
        $args = wp_parse_args( $args, $defaults );

        return $args;
    }

    public function get_page_options(){
        global $sed_options_engine;

        $params = array();
        $page_params = array_merge( $this->default_page_options()['params'] , $this->page_options()['params'] );

        $panels = array();
        $page_panels = array_merge( $this->default_page_options()['panels'] , $this->page_options()['panels'] );

        $params['sed_tab_scope_options'] = array(
            'type'              =>  'custom',
            'html'              =>  $this->view_tab_scope() ,
            'priority'          => -10000
        );

        foreach( $page_panels AS $key => $args ){

            if( !isset( $args['atts'] ) ){
                $args['atts'] = array();
            }

            if( isset( $args['atts']['class'] ) ){
                $org_class = $args['atts']['class'] . " ";
            }else{
                $org_class = "";
            }

            $args['atts']['class'] = $org_class . "page-customize-scope sed-option-scope";
            $panels[ $key ] = $this->get_panel( $key , $args );

            $args['atts']['class'] = $org_class . "layout-scope sed-option-scope";
            $panels[ "sed_layout_" . $key ] = $this->get_panel( "sed_layout_" . $key , $args );

            $args['atts']['class'] = $org_class . "public-scope sed-option-scope";
            $panels[ "sed_public_" . $key ] = $this->get_panel( "sed_public_" . $key , $args );

        }

        foreach( $page_params AS $id => $args ){

            $args['control_category']  = 'page-settings';

            if( !isset( $args['panel'] ) ) {
                if (!isset($args['atts'])) {
                    $args['atts'] = array();
                }

                if (isset($args['atts']['class'])) {
                    $org_class = $args['atts']['class'] . " ";
                } else {
                    $org_class = "";
                }
            }

            if( !isset( $args['panel'] ) )
                $args['atts']['class'] = $org_class . "page-customize-scope sed-option-scope";
            else
                $org_panel = $args['panel'];

            $params[$id] = $args;

            $settings_type = $args['settings_type'];

            if( !isset( $args['panel'] ) )
                $args['atts']['class'] = $org_class . "layout-scope sed-option-scope";
            else
                $args['panel'] = "sed_layout_" . $org_panel;

            $args['settings_type'] = $this->layout_option_name . "[" . $settings_type . "]";
            $params["sed_layout_" . $id] = $args;

            if( !isset( $args['panel'] ) )
                $args['atts']['class'] = $org_class . "public-scope sed-option-scope";
            else
                $args['panel'] = "sed_public_" . $org_panel;

            $args['settings_type'] = $this->theme_option_name . "[" . $settings_type . "]";
            $params["sed_public_" . $id] = $args;
        }

        $sed_options_engine->set_group_params( "sed_page_options" , __("Page Options" , "site-editor") , $params , $panels , "page-settings" );
    }


    public function get_content_options( ){
        global $sed_options_engine;

        $params = array();
        $panels = array();

        $content = $this->get_content_info( $_POST['content_info'] );
        $content_type = $content['content_type'];
        $content_title = $content['title'];

        $content_params = array_merge( $this->default_content_options()['params'] , $this->content_options()['params'] );
        $content_settings = array_merge( $this->default_content_options()['settings'] , $this->content_options()['settings'] );
        $content_panels = array_merge( $this->default_content_options()['panels'] , $this->content_options()['panels'] );

        foreach( $content_panels AS $key => $args ){

            if( !isset( $args['atts'] ) ){
                $args['atts'] = array();
            }

            if( isset( $args['atts']['class'] ) ){
                $org_class = $args['atts']['class'] . " ";
            }else{
                $org_class = "";
            }

            $args['atts']['class'] = $org_class . "page-customize-scope sed-option-scope";
            $panels[ $key ] = $this->get_panel( $key , $args );

            $args['atts']['class'] = $org_class . "public-scope sed-option-scope";
            $panels[ "sed_public_" . $key ] = $this->get_panel( "sed_public_" . $key , $args );

        }

        $params['sed_tab_scope_options'] = array(
            'type'              =>  'custom',
            'html'              =>  $this->view_tab_scope( false ) ,
            'priority'          => -10000
        );

        foreach( $content_params AS $id => $args ){

            $setting = $content_settings[ $args['settings_type'] ];

            if( $setting['content_type'] == $content_type ) {

                $args['control_category'] = 'content-settings';

                if( !isset( $args['panel'] ) ) {

                    if (!isset($args['atts'])) {
                        $args['atts'] = array();
                    }

                    if (isset($args['atts']['class'])) {
                        $org_class = $args['atts']['class'] . " ";
                    } else {
                        $org_class = "";
                    }

                }else
                    $org_panel = $args['panel'];

                if( isset( $setting['costomizable'] ) && $setting['costomizable'] === true ) {
                    if( !isset( $args['panel'] ) )
                        $args['atts']['class'] = $org_class . "page-customize-scope sed-option-scope";

                    $params[$id] = $args;
                }

                if( !isset( $args['panel'] ) )
                    $args['atts']['class'] = $org_class . "public-scope sed-option-scope";
                else
                    $args['panel'] = "sed_public_" . $org_panel;

                $args['settings_type'] = $this->theme_option_name . "[" . $args['settings_type'] . "]";
                $params["sed_public_" . $id] = $args;
            }
        }
        //"sed_content_" . $content_type
        $sed_options_engine->set_group_params( "sed_content_options" , sprintf( __("%s Options" , "site-editor") , $content_title ) , $params , $panels , "content-settings" );

    }


    private function get_content_info( $info ){

        switch( $info['type'] ){
            case "home_blog" :
            case "index_blog" :
            case "author_archive" :
            case "date_archive" :
            case "date_archive" :

                $content_type = "archive";
                $title = __( "Archive" , "site-editor");

                break;
            case "search_results":

                $content_type = "search_results";
                $title = __( "Search Results" , "site-editor");

                break;
            case "home_page":

                $content_type = "page";
                $title = __( "Page" , "site-editor");

                break;
            case "404_page":

                $content_type = "404_page";
                $title = __( "404 Page" , "site-editor");

                break;
            case "single":

                if( $info['post_type'] == "page" ) {

                    $content_type = "page";
                    $title = __("Page", "site-editor");

                }else if( $info['post_type'] == "post" ) {

                    $content_type = "single_post";
                    $title = __("Single Post", "site-editor");

                }else{

                    $content_type = "single_" . $info['post_type'] ;
                    $title = sprintf( __("%s Single", "site-editor") , $info['post_type'] );
                }

                break;
            case "taxonomy":

                if( $info['taxonomy'] == "category" ||  $info['taxonomy'] == "post_tag" ) {
                    $content_type = "archive";
                    $title = __( "Archive" , "site-editor");
                }else{
                    $content_type = "custom_taxonomy_" . $info['taxonomy'] ;
                    $title = sprintf( __("Custom %s Taxonomy", "site-editor") , $info['taxonomy'] );
                }

                break;
            case "post_type_archive":

                $content_type = "post_type_archive_" . $info['post_type'] ;
                $title = sprintf( __("%s Post Type Archive", "site-editor") , $info['post_type'] );

                break;
        }

        return apply_filters( "sed_content_section_info" , array(
            "title"         =>  $title ,
            "content_type"  =>  $content_type
        ) , $info );

    }


    public function register_settings(){

        $settings = $this->get_settings();

        sed_add_settings( $settings );
    }

    private function get_settings(){

        $settings = array_merge( $this->default_site_options()['settings'] , $this->site_options()['settings'] );

        $theme_settings = array_merge( $this->default_theme_options()['settings'] , $this->theme_options()['settings'] );

        foreach( $theme_settings AS $id => $setting ){
            $setting['option_type'] = 'option';
            $settings[ $this->theme_option_name . "[" . $id . "]" ] = $setting;
        }

        $page_settings = array_merge( $this->default_page_options()['settings'] , $this->page_options()['settings'] );

        foreach( $page_settings AS $id => $setting ){
            $setting['option_type'] = 'option';
            $settings[ $this->theme_option_name . "[" . $id . "]" ] = $setting;

            $settings[ $this->layout_option_name . "[" . $id . "]" ] = $setting;

            $setting['option_type'] = 'base';
            $settings[ $id ] = $setting;
        }


        $content_settings = array_merge( $this->default_content_options()['settings'] , $this->content_options()['settings'] );

        foreach( $content_settings AS $id => $setting ){
            $setting['option_type'] = 'option';
            $settings[ $this->theme_option_name . "[" . $id . "]" ] = $setting;

            if( isset( $setting['costomizable'] ) && $setting['costomizable'] === true ) {
                $setting['option_type'] = 'base';
                $settings[ $id ] = $setting;
            }

        }

        return $settings;
    }


    public function default_site_options(){

        $settings   = array();
        $params     = array();
        $panels     = array();

        return array(
            "settings"  => $settings ,
            "params"    => $params ,
            "panels"    => $panels ,
        );
    }

    public function site_options(){

        $settings   = array();
        $params     = array();
        $panels     = array();

        return array(
            "settings"  => $settings ,
            "params"    => $params ,
            "panels"    => $panels ,
        );
    }


    public function default_theme_options(){

        $settings   = array();
        $params     = array();
        $panels     = array();

        return array(
            "settings"  => $settings ,
            "params"    => $params ,
            "panels"    => $panels ,
        );
    }

    public function theme_options(){

        $settings   = array();
        $params     = array();
        $panels     = array();

        return array(
            "settings"  => $settings ,
            "params"    => $params ,
            "panels"    => $panels ,
        );
    }

    public function default_page_options(){

        $settings   = array();
        $params     = array();
        $panels     = array();

        return array(
            "settings"  => $settings ,
            "params"    => $params ,
            "panels"    => $panels ,
        );
    }

    public function page_options(){

        $settings   = array();
        $params     = array();
        $panels     = array();

        return array(
            "settings"  => $settings ,
            "params"    => $params ,
            "panels"    => $panels ,
        );
    }

    public function default_content_options(){

        $settings   = array();
        $params     = array();
        $panels     = array();

        return array(
            "settings"  => $settings ,
            "params"    => $params ,
            "panels"    => $panels ,
        );
    }

    public function content_options(){

        $settings   = array();
        $params     = array();
        $panels     = array();

        return array(
            "settings"  => $settings ,
            "params"    => $params ,
            "panels"    => $panels ,
        );
    }

}

Class StarsIdeasTheme extends SiteEditorThemeFramework {

    function __construct( ){

        $this->post_mata_key        = "sed_post_settings";
        $this->theme_option_name    = "sed_theme_options";
        $this->layout_option_name   = "sed_layout_options";

        parent::__construct();

    }

    function site_options(){

        $panels = array(

            'static_front_page' => array(
                'id'            => 'static_front_page' ,
                'title'         =>  __('Static Front Page',"site-editor")  ,
                'capability'    => 'edit_theme_options' ,
                'type'          => 'fieldset' ,
                'description'   => '' ,
                'priority'      => 9 ,
            )

        );

        $settings = array(
            /*
             * @site options
             * @Show in site settings dialog
            */
            'show_on_front' => array(
                'value'          => get_option( 'show_on_front' ),
                'capability'     => 'manage_options',
                'option_type'    => 'option' ,
                'transport'      => 'refresh'//'postMessage'
            ),

            'page_on_front' => array(
                'value'         => get_option( 'page_on_front' ),
                'option_type'   => 'option',
                'capability'    => 'manage_options',
                'transport'     => 'refresh'//'postMessage'
            ),

            'page_for_posts' => array(
                'value'          => get_option( 'page_for_posts' ),
                'option_type'    => 'option',
                'capability'     => 'manage_options',
                'transport'      => 'refresh'//'postMessage' ,
            )

        );

        $pages = get_pages();
        $pages_list = array();
        $pages_list['0'] = __( "Select Page" , "site-editor" );

        foreach ( $pages as $page ) {
            $pages_list[$page->ID] = $page->post_title;
        }

        $params = array(

            'show_on_front' => array(
                "type"          => "radio" ,
                "label"         => __("Front page displays", "site-editor"),
                'value'         => get_option( 'show_on_front' ),
                "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
                "options"       =>  array(
                    "posts"      =>    __( "Your latest posts" , "site-editor" ) ,
                    "page"       =>    __( "A static page" , "site-editor" ) ,
                ),
                'settings_type'     => "show_on_front" ,
                'panel'             => "static_front_page"
            ),

            'front_page' => array(
                "type"          => "select" ,
                "label"         => __("Front page", "site-editor"),
                'value'         => get_option( 'page_on_front' ),
                "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
                "options"       => $pages_list,
                'settings_type'     => "page_on_front" ,
                'panel'             => "static_front_page" ,
                'dependency' => array(
                    'controls'  =>  array(
                        "control"  => "show_on_front" ,
                        "value"    => "page",
                    )
                )
            ),

            'posts_page' => array(
                "type"          => "select" ,
                "label"         => __("Posts page", "site-editor"),
                'value'         => get_option( 'page_for_posts' ),
                "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
                "options"       =>  $pages_list,
                'settings_type'     => "page_for_posts" ,
                'panel'             => "static_front_page" ,
                'dependency' => array(
                    'controls'  =>  array(
                        "control"  => "show_on_front" ,
                        "value"    => "page",
                    )
                )
            )
        );

        return array(
            "settings"  => $settings ,
            "params"    => $params ,
            "panels"    => $panels ,
        );
    }


    function theme_options(){

        $settings = array(

            'logo' => array(
                'value'          => '',
                'capability'     => 'edit_theme_options',
                'transport'      => 'postMessage'
            ),

            'favicon' => array(
                'value'          => '' ,
                'capability'     => 'edit_theme_options',
                'transport'      => 'postMessage'
            ),

        );

        $params = array(

            'site_logo' => array(
                "type"          => "image" ,
                "label"         => __("Select Logo", "site-editor"),
                "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
                'remove_btn'        => true ,
                'settings_type'     => "logo" ,
            ),

            'site_favicon' => array(
                "type"          => "image" ,
                "label"         => __("Select Favicon", "site-editor"),
                "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
                'remove_btn'        => true ,
                'settings_type'     => "favicon" ,
            )

        );

        return array(
            "settings"  => $settings ,
            "params"    => $params ,
            "panels"    => array()
        );
    }


    function page_options(){


        $panels = array(

            'general_page_style' => array(
                'title'         =>  __('Static Front Page',"site-editor")  ,
                'label'         => __('Page General Style',"site-editor")  ,
                'capability'    => 'edit_theme_options' ,
                'type'          => 'inner_box' ,
                'description'   => '' ,
                'priority'      => 9 ,
            )

        );

        $settings = array(

            'sheet_width' => array(
                'value'          => 1100,
                'capability'     => 'edit_theme_options',
                'transport'      => 'postMessage'
            ),

            'page_length' => array(
                'value'          => 'wide',
                'capability'     => 'edit_theme_options',
                'transport'      => 'postMessage'
            ),

            'page_background' => array(
                'value'          => 'wide',
                'capability'     => 'edit_theme_options',
                'transport'      => 'postMessage'
            ),

        );

        $params = array(

            'page_sheet_width' => array(
                "type"              => "spinner" ,
                "label"             => __("Sheet Width", "site-editor"),
                'value'             => 1100,
                'after_field'       => "px" ,
                "desc"              => __("This option allows you to set a title for your image.", "site-editor"),
                'settings_type'     => "sheet_width" ,
            ),

            'page_length' => array(
                "type"              => "select" ,
                "label"             => __("Page Length", "site-editor"),
                "desc"              => __("This option allows you to set a title for your image.", "site-editor"),
                'value'             => 'wide',
                "options"       =>  array(
                    "wide"          =>    __( "Wide" , "site-editor" ) ,
                    "boxed"         =>    __( "Boxed" , "site-editor" ) ,
                ),
                'settings_type'     => "sheet_width" ,
                'panel'             => 'general_page_style'
            ),

            'change_image_panel' => array(
                "type"              => "image" ,
                "label"             => __("Background Image", "site-editor"),
                "desc"              => __("This option allows you to set a title for your image.", "site-editor"),
                'remove_btn'        => true ,
                'settings_type'     => "page_background" ,
                'panel'             => 'general_page_style'
            )

        );

        return array(
            "settings"  => $settings ,
            "params"    => $params ,
            "panels"    => $panels ,
        );

    }

    function content_options(){

        $settings = array(

            'single_page_show_comments' => array(
                'value'          => false ,
                'capability'     => 'edit_theme_options',
                'transport'      => 'postMessage' ,
                'content_type'   => 'page' ,
                'costomizable'   => true
            ),

            'single_page_show_featured_image' => array(
                'value'          => false ,
                'capability'     => 'edit_theme_options',
                'transport'      => 'postMessage' ,
                'content_type'   => 'page' ,
                'costomizable'   => true
            ),

        );


        $params = array(

            'show_comments' => array(
                "type"          => "checkbox" ,
                "label"         => __("Allow Comments on Pages", "site-editor"),
                "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
                'settings_type'     => "single_page_show_comments" ,
            ) ,

            'show_featured_image' => array(
                "type"          => "checkbox" ,
                "label"         => __("Featured Images on Pages", "site-editor"),
                "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
                'settings_type'     => "single_page_show_featured_image" ,
            )

        );

        return array(
            "settings"  => $settings ,
            "params"    => $params ,
            "panels"    => array() ,
        );
    }

}

new StarsIdeasTheme;


 /*       $panels = array();

        $styles_settings = array( 'background','gradient' ,'padding' ); //,'margin'

        $general_style_controls = new ModuleStyleControls( "general_style_editor" );

        if( !empty($styles_settings) ){
            foreach( $styles_settings AS $control ){
                $general_style_controls->$control();
            }
        }

        $general_controls = array();

        if( !empty( $general_style_controls->controls ) ){
            foreach(  $general_style_controls->controls AS $styles_setting => $controls ){

                $panel_id = 'general_'.$styles_setting.'style_editor_panel';

                $panels[$panel_id] = array(
                    'title'         =>  $general_style_controls->labeles[ $styles_setting ]."&nbsp;". __("Settings","site-editor")  ,
                    'label'         =>  $general_style_controls->labeles[ $styles_setting ]."&nbsp;". __("Settings","site-editor") ,
                    'capability'    => 'edit_theme_options' ,
                    'type'          => 'inner_box' ,
                    'description'   => '' ,
                    'parent_id'     => 'root' ,
                    'priority'      => 9 ,
                    'id'            => $panel_id  ,
                    'atts'      =>  array(
                        //'class'             => "design_ac_header" ,
                        'data-selector'     => "#main"
                    )
                );

                foreach(  $controls AS $id => $control ){
                    $controls[$id]['panel'] = $panel_id;
                }

                $general_controls = array_merge( $general_controls , $controls);
            }
        }


        $controls_settings = array();
        if( !empty( $general_controls ) ){
            foreach( $general_controls As $id => $control ){

                if(isset($control["control_type"])){
                    $value = $control['value'];

                    if( $value === "true" )
                        $value = true;
                    else if( $value === "false" )
                        $value = false;

                    $args = array(
                        'settings'     => array(
                            'default'       => $control["settings_type"]
                        ),
                        'type'                =>  $control["control_type"],
                        'category'            =>  'style-editor',
                        'sub_category'        =>  'general_settings',
                        'default_value'       =>  $value,
                        'is_style_setting'    =>  true ,
                        'panel'               =>  $control["panel"] ,
                    );

                    if(!empty($control["control_param"]))
                        $args = array_merge( $args , $control["control_param"]);

                    if(!empty($control["style_props"]))
                        $args['style_props'] = $control["style_props"];

                    $controls_settings[$id] = $args;

                }

            }
        }


        if( !empty( $controls ) ){
            ModuleSettings::$group_id = "";
            $style_editor_settings = ModuleSettings::create_settings($general_controls, $panels);

            echo $style_editor_settings;

            ModuleSettings::$group_id = "";

            sed_add_controls( $controls_settings );

        }

        $settings = array(
            'page_length' => array(
                'type' => 'select',
                'value' => 'wide' ,
                'label' => __('Length', 'site-editor'),
                'desc' => '',
                'options' =>array(
                    'wide'    => __('Wide', 'site-editor'),
                    'boxed'   => __('Boxed', 'site-editor')
                ),
                'priority'      => 15
            ),

            'sheet_width_page' => array(
                'type' => 'spinner',
                'after_field'  => 'px',
                'value' => 1100 ,
                'label' => __("Sheet Width" ,"site-editor"),
                'desc' => '',
                'priority'      => 20
            ),

        );

        $cr_settings = ModuleSettings::create_settings($settings , array());

        echo $cr_settings;*/


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



























