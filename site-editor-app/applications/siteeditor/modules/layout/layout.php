<?php
/*
Module Name: Content
Module URI: http://www.siteeditor.org/modules/content
Description: Module Content For Site Editor Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

if(!class_exists('SiteEditorLayoutManager')){
    class SiteEditorLayoutManager{

        public $scope_control_id;

        function __construct( ) {

            add_filter('sed_custom_js_plugins' , array( $this, 'add_js_plugin' ) );

            add_action( 'wp_enqueue_scripts' , array( $this, 'render_scripts' ) );

            add_filter( "sed_js_I18n" , array( $this ,'js_I18n' ) );

            add_action( "sed_footer" , array( $this , "print_app_data" ) );

            if( site_editor_app_on() )
                add_action( "wp_footer" , array( $this , "print_wp_footer" ) );

            add_filter( "sed_sub_themes" , array( $this, "add_sub_themes" ) , 10 , 1 );

            add_action( 'sed_app_register' ,  array( $this, 'register_settings' ) );

            $this->scope_control_id = "main_layout_row_scope_control";

            add_action( "sed_ajax_load_options_sed_add_layout" , array( $this, "add_layout_options" ) );

            add_action( "sed_ajax_load_options_sed_pages_layouts" , array( $this, "pages_layouts_options" ) );

            //add_filter( "sed_addon_settings", array($this,'icon_settings'));
		}

        function add_layout_options(){
            global $sed_options_engine;
            ob_start();
            include dirname( __FILE__ ) . DS . "view" . DS . "add_layout.tpl.php";
            $html = ob_get_clean();

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

            $sed_options_engine->set_group_params( "sed_add_layout" , __("Add New Layout" , "site-editor") , $params , array() , "app-settings" );
        }


        function pages_layouts_options(){
            global $sed_options_engine;
            ob_start();
            include dirname( __FILE__ ) . DS . "view" . DS . "pages_layouts.tpl.php";
            $html = ob_get_clean();

            $params = array();

            $params['page_layout'] = array(
                'type'      => 'select',
                'value'     => 'default' ,
                'label'     => __("Select page layout" ,"site-editor"),
                'desc'      => '',
                'options'   => array(
                    ''  =>   __( "Using Default settings" , "site-editor" )
                ),
                'atts'      => array(
                    "class"     =>  "sed_all_layouts_options_select"
                ),
                'priority'  => 15 ,
                'control_category'  => 'app-settings' ,
                'settings_type'     => "page_layout" ,
            );


            $current_pages_layouts = get_option('sed_pages_layouts');

            foreach( $current_pages_layouts AS $page_group => $layout ){
                $params["group_".$page_group] = array(
                    'type'      => 'select',
                    'value'     => $layout ,
                    'label'     => $page_group,
                    'desc'      => '',
                    'options'   => array(),
                    'atts'      => array(
                        "class"     =>  "sed_all_layouts_options_select"
                    ),
                    'priority'  => 15 ,
                    'control_category'  => 'app-settings' ,
                    'settings_type'     => 'sed_pages_layouts[' . $page_group . ']' ,
                );
            }

            $sed_options_engine->set_group_params( "sed_pages_layouts" , __("Pages Layouts Settings" , "site-editor") , $params , array() , "app-settings" );
        }

        /*function icon_settings( $sed_addon_settings ){
            global $site_editor_app;
            $sed_addon_settings['iconLibrary'] = array(
                'nonce'  => array(
                    'load'  =>  wp_create_nonce( 'sed_app_icon_font_load_' . $site_editor_app->get_stylesheet() ) ,
                    'remove'  =>  wp_create_nonce( 'sed_app_icon_font_remove_' . $site_editor_app->get_stylesheet() )
                )
            );
            return $sed_addon_settings;
        }*/

        public function add_js_plugin($plugin_array) {
           $plugin_array['appLayout'] = SED_BASE_URL . 'applications/siteeditor/modules/layout/js/app-layout-plugin.min.js';
           return $plugin_array;
        }

        public function js_I18n( $I18n ){
            $I18n['ok_confirm']         =  __("Ok" , "site-editor");
            $I18n['cancel_confirm']     =  __("Cancel" , "site-editor");
            $I18n['no_title']           =  __("No Title" , "site-editor");
            $I18n['private_scope']      =  __("Private" , "site-editor");
            $I18n['public_scope']       =  __("Public" , "site-editor");
            $I18n['customize_scope']    =  __("Customize" , "site-editor");
            $I18n['hidden_scope']       =  __("Hidden" , "site-editor");

            $I18n['empty_layout_title']         =  __("Please enter title" , "site-editor");
            $I18n['invalid_layout_title']       =  __("Layout title not validate , only using letter , number , - , _ and space" , "site-editor");
            $I18n['empty_layout_slug']          =  __("Please enter slug" , "site-editor");
            $I18n['invalid_layout_slug']        =  __("Layout slug not validate , It should be English and only using letter , number , - and _" , "site-editor");
            $I18n['layout_already_exist']       =  __("The item is already exist in your leyouts." , "site-editor");
            $I18n['remove_default_layout']      =  __("you can not remove default layout" , "site-editor");
            $I18n['remove_current_layout']      =  __("you can not remove current page layout" , "site-editor");
            $I18n['layout_not_exist']           =  __("this layout not exist." , "site-editor");
            $I18n['invalid_layout']             =  __("The item is not valid layout." , "site-editor");

            return $I18n;
        }

        public function render_scripts(){
            wp_enqueue_script( 'app-layout-module', SED_BASE_URL . 'applications/siteeditor/modules/layout/js/app-layout-module.min.js', array( 'siteeditor-base' , 'sed-app-preview' , 'sed-pagebuilder' ) ,"1.0.0" , 1);
            //wp_register_script( 'sub-themes-plugin',SED_BASE_URL.'libraries/siteeditor/site-iframe/plugins/app-sub-themes.min.js', array( 'siteeditor-base' , 'sed-app-preview' , 'sed-pagebuilder' ),"",1 );
            // wp_register_style( 'bootstrap-popover',SED_BASE_URL.'libraries/bootstrap/css/popover/popover.css');
        }

        function print_wp_footer(){
          ?>
            <script type="text/javascript">
                var _sedAppDefaultPageLayout = "<?php echo $this->get_default_page_layout();?>";
            </script>
          <?php
        }

        function print_app_data(){
            $control_id = $this->scope_control_id;
            $sed_sub_themes = array();
            $sed_sub_themes = apply_filters( "sed_sub_themes" , $sed_sub_themes );
            include_once dirname( __FILE__ ) . DS . "view" . DS . "layout-template.php";
        }

        function add_toolbar_elements(){
            global $site_editor_app;

            $site_editor_app->toolbar->add_element(
                "layout" ,
                "general" ,
                "add-layout" ,
                __("Add Layout","site-editor") ,
                "add_layout_element" ,     //$func_action
                "" ,                //icon
                "" ,  //$capability=
                array( ),// "class"  => "btn_default3"
                array( "row" => 1 ,"rowspan" => 2 ),
                array('module' => 'layout' , 'file' => 'add_layout.php'),
                //array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                'all' ,
                array(),
                array()
            );

            $site_editor_app->toolbar->add_element(
                "layout" ,
                "general" ,
                "pages-layouts" ,
                __("Layout settings","site-editor") ,
                "sub_theme_element" ,     //$func_action
                "" ,                //icon
                "" ,  //$capability=
                array( ),// "class"  => "btn_default3"
                array( "row" => 1 ,"rowspan" => 2 ),
                array('module' => 'layout' , 'file' => 'pages_layouts.php'),
                //array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                 'all' ,
                 array(),
                 array()
            );

            $site_editor_app->toolbar->add_element(
                "layout" ,
                "settings" ,
                "general-options" ,
                __("Site Settings","site-editor") ,
                "general_options_element" ,     //$func_action
                "" ,                //icon
                "" ,  //$capability=
                array(  ),  //"class"  => "btn_default3"
                array( "row" => 1 ,"rowspan" => 2 ),
                array('module' => 'layout' , 'file' => 'general_options.php'),
                //array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                 'all' ,
                array(
                    'theme_synchronization' => array(
                        'value'     => false,
                        'transport'   => 'postMessage'
                    ),
                    'page_sync' => array(
                        'value'       => false,
                        'transport'   => 'postMessage'
                    ),
                    'theme_content' => array(
                        'value'       => false,
                        'transport'   => 'postMessage'
                    ),
            		'show_on_front' => array(
            			//'value'        => get_option( 'show_on_front' ),
            			//'capability'     => 'manage_options',
            			'option_type'    => 'option' ,
                        'transport'      => 'custom'
            		),

            		'page_on_front' => array(
            			'option_type'   => 'option',
            			//'capability'    => 'manage_options',
                        'transport'     => 'custom'
            		),

            		'page_for_posts' => array(
            			'option_type'    => 'option',
            			//'capability'     => 'manage_options',
                        'transport'      => 'custom'
            		),

                    'sheet_width' => array(
                        'value'     => 1100,
                        'transport'   => 'postMessage'
                    ) ,


                    'page_length' => array(
                        'value'     => 'wide',
                        'transport'   => 'postMessage'
                    ) ,

                ),
                array(
                    'sheet_width_page' => array(
                        'settings'     => array(
                            'default'       => 'sheet_width'
                        ),
                        'type'    => 's_spinner' ,
                        'min'     => 900
                    ),

                    'page_length' => array(
                        'settings'     => array(
                            'default'       => 'page_length'
                        ),
                        'type'    => 'sed_element'
                    ),
                )
            );

            $site_editor_app->toolbar->add_element(
                "layout" ,
                "settings" ,
                "page-options" ,
                __("Page Settings","site-editor") ,
                "page_options_element" ,     //$func_action
                "" ,                //icon
                "" ,  //$capability=
                array(  ),  //"class"  => "btn_default3"
                array( "row" => 1 ,"rowspan" => 2 ),
                array('module' => 'layout' , 'file' => 'page_options.php'),
                //array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                'all' ,
                array() ,
                array()
            );

        }

        function default_pages_layouts_list(){

            $default_pages_layouts = array(
                "posts_archive"     =>  "archive" ,
                "home_blog"         =>  "default" ,
                "front_page"        =>  "default" ,
                "blog"              =>  "archive" ,
                "search_results"    =>  "default" ,
                "404_page"          =>  "default" ,
                "single_post"       =>  "post" ,
                "single_page"       =>  "page" ,
                "author_page"       =>  "archive" ,
                "date_archive"      =>  "archive" ,
            );

            return $default_pages_layouts;
        }

        function register_settings( ){
            $settings = array();

            if ( get_option( 'sed_layouts_settings' ) === false ) {

                //The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                $deprecated = null;
                $autoload = 'yes';

                $default_layouts = array(
                    "archive"   =>  array(
                        "title"         =>  __("Archive" , "site-editor")  ,
                    ),

                    "default"   =>  array(
                        "title"         =>  __("Default" , "site-editor") ,
                    ),

                    "post"      =>  array(
                        "title"         =>  __("Single Post" , "site-editor") ,
                    ),

                    "page"      =>  array(
                        "title"         =>  __("Page" , "site-editor") ,
                    ),
                );

                add_option( 'sed_layouts_settings' , $default_layouts , $deprecated, $autoload );
            }

            $settings['sed_layouts_settings'] = array(
    			'default'        => get_option( 'sed_layouts_settings' ),
    			'capability'     => 'manage_options',
    			'option_type'    => 'option' ,
                'transport'      => 'postMessage'
    		);

            $default_pages_layouts = $this->default_pages_layouts_list();

            if ( get_option( 'sed_pages_layouts' ) === false ) {

                //The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                $deprecated = null;
                $autoload = 'yes';

                $current_pages_layouts = $default_pages_layouts;

                add_option( 'sed_pages_layouts' , $default_pages_layouts , $deprecated, $autoload );
            }else{
                $current_pages_layouts = get_option('sed_pages_layouts');
            }

            foreach( $default_pages_layouts AS $group => $layout ){

                $id = 'sed_pages_layouts[' . $group.']';

                $settings[$id] = array(
                    'default'        => isset( $current_pages_layouts[$group] ) ? $current_pages_layouts[$group] : "default",
                    'capability'     => 'manage_options',
                    'option_type'    => 'option' ,
                    'transport'      => 'postMessage'
                );
            }

            $post_types = get_post_types( array( 'show_in_nav_menus' => true , 'public' => true ), 'object' );

            if ( !empty( $post_types ) ) {
                foreach ($post_types AS $post_type_name => $post_type) {

                    if( in_array( $post_type_name , array( "post" , "page" ) ) )
                        continue;

                    $id = 'sed_pages_layouts[post_type_archive_' . $post_type_name .']';

                    if( !isset( $current_pages_layouts['post_type_archive_' . $post_type_name] ) ){
                        $current_pages_layouts['post_type_archive_' . $post_type_name] = "default";
                    }

                    $settings[$id] = array(
                        'default'        =>  $current_pages_layouts['post_type_archive_' . $post_type_name] ,
                        'capability'     => 'manage_options',
                        'option_type'    => 'option' ,
                        'transport'      => 'postMessage'
                    );


                    $id = 'sed_pages_layouts[single_' . $post_type_name .']';

                    if( !isset( $current_pages_layouts['single_' . $post_type_name] ) ){
                        $current_pages_layouts['single_' . $post_type_name] = "default";
                    }

                    $settings[$id] = array(
                        'default'        => $current_pages_layouts['single_' . $post_type_name] ,
                        'capability'     => 'manage_options',
                        'option_type'    => 'option' ,
                        'transport'      => 'postMessage'
                    );

                }
            }

            $args = array(
                'public'   => true,
                '_builtin' => false

            );

            $output = 'objects';
            $taxonomies = get_taxonomies( $args, $output );
            if ( $taxonomies ) {
                foreach ( $taxonomies  as $taxonomy ) {

                    $id = 'sed_pages_layouts[taxonomy_' . $taxonomy->name .']';

                    if( !isset( $current_pages_layouts['taxonomy_' . $taxonomy->name] ) ){
                        $current_pages_layouts['taxonomy_' . $taxonomy->name] = "default";
                    }

                    $settings[$id] = array(
                        'default'        => $current_pages_layouts['taxonomy_' . $taxonomy->name] ,
                        'capability'     => 'manage_options',
                        'option_type'    => 'option' ,
                        'transport'      => 'postMessage'
                    );

                }
            }

            if ( get_option( 'sed_layouts_models' ) === false ) {

                //The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                $deprecated = null;
                $autoload = 'yes';
                add_option( 'sed_layouts_models' , array() , $deprecated, $autoload );
            }

            update_option( 'sed_pages_layouts' , $current_pages_layouts );

            $settings['sed_layouts_models'] = array(
    			'default'        => get_option( 'sed_layouts_models' ),
    			'capability'     => 'manage_options',
    			'option_type'    => 'option' ,
                'transport'      => 'postMessage'
    		);


            if ( get_option( 'sed_last_theme_id' ) === false ) {

                //The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                $deprecated = null;
                $autoload = 'yes';
                add_option( 'sed_last_theme_id' , 0 , $deprecated, $autoload );
            }

            $settings['sed_last_theme_id'] = array(
    			'default'        => get_option( 'sed_last_theme_id' ),
    			'capability'     => 'manage_options',
    			'option_type'    => 'option' ,
                'transport'      => 'postMessage'
    		);


            if ( get_option( 'sed_layouts_content' ) === false ) {

                //The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                $deprecated = null;
                $autoload = 'yes';
                add_option( 'sed_layouts_content' , array() , $deprecated, $autoload );
            }

            $settings['sed_layouts_content'] = array(
    			'default'        => get_option( 'sed_layouts_content' ),
    			'capability'     => 'manage_options',
    			'option_type'    => 'option' ,
                'transport'      => 'postMessage'
    		);

            $settings['page_layout'] = array(
    			'default'        => '' ,
    			'option_type'    => 'base' ,
                'transport'      => 'refresh'
    		);

            if ( get_option( 'sed_theme_options' ) === false ) {

                //The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                $deprecated = null;
                $autoload = 'yes';
                add_option( 'sed_theme_options' , array() , $deprecated, $autoload );
            }

            $settings['sed_theme_options'] = array(
    			'default'        => get_option( 'sed_theme_options' ),
    			'capability'     => 'manage_options',
    			'option_type'    => 'option' ,
                'transport'      => 'postMessage'
    		);


            if ( get_option( 'sed_general_theme_options' ) === false ) {

                //The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                $deprecated = null;
                $autoload = 'yes';
                add_option( 'sed_general_theme_options' , array() , $deprecated, $autoload );
            }

            $settings['sed_general_theme_options'] = array(
    			'default'        => get_option( 'sed_general_theme_options' ),
    			'capability'     => 'manage_options',
    			'option_type'    => 'option' ,
                'transport'      => 'postMessage'
    		);

            sed_add_settings( $settings );

            $controls = array();

            $controls[$this->scope_control_id] = array(
                'settings'     => array(
                    'default'       => "sed_layouts_models"
                ),
                'type'                =>  "layout_scope",
                'category'            =>  "layout",
                //'sub_category'        =>  $name,           //shortcode name :: sed_image
                'default_value'       => array() ,
            );

            sed_add_controls( $controls );

        }

        //START *********** for sub_theme module -----------
        function add_sub_themes( $sub_themes ) {

            $sub_themes["default"]          = array( "title" => __("Default" , "site-editor") );
            $sub_themes["single_post"]      = array( "title" => __("Single Post" , "site-editor") );
            $sub_themes["page"]             = array( "title" => __("Page" , "site-editor") );
            $sub_themes["archive"]          = array( "title" => __("Archive" , "site-editor") );

            return $sub_themes;
        }

        function exist_layout( $layout ){
            $layout_settings = get_option( 'sed_layouts_settings' );

            if( $layout_settings === false || !is_array( $layout_settings ) ){
                return false;
            }

            $layouts = array_keys( $layout_settings );

            return in_array( $layout , $layouts );
        }

        function get_page_layout(){
            global $sed_data;
            $page_layout = ( isset( $sed_data['page_layout'] ) && !empty( $sed_data['page_layout'] ) ) ? $sed_data['page_layout'] : $this->get_default_page_layout();

            if( !$this->exist_layout( $page_layout ) ){
                return "default";
            }

            return $page_layout;
        }

        function get_default_page_layout(){

            $sed_pages_layouts = get_option( 'sed_pages_layouts' );

            if( is_category() || is_tag() ){

                $page_layout = $sed_pages_layouts["posts_archive"];

            }elseif( is_tax() ){

                $tax = get_queried_object();
                $page_layout = isset( $sed_pages_layouts[ "taxonomy_" . $tax->taxonomy ] ) ? $sed_pages_layouts[ "taxonomy_" . $tax->taxonomy ] : "default";

            } elseif( is_home() === true && is_front_page() === true ){

                $page_layout = $sed_pages_layouts[ "home_blog" ];

            } elseif( is_home() === false && is_front_page() === true ){

                $page_layout = $sed_pages_layouts[ "front_page" ];

            } elseif( is_home() === true && is_front_page() === false  ){

                $page_layout = $sed_pages_layouts[ "blog" ];

            } elseif ( is_search() ) {

                $page_layout = $sed_pages_layouts[ "search_results" ];

            } elseif ( is_404() ) {

                $page_layout = $sed_pages_layouts[ "404_page" ];

            } elseif( is_singular() ){

                global $post;
                $page_layout =  isset( $sed_pages_layouts[ "single_" . $post->post_type ] ) ? $sed_pages_layouts[ "single_" . $post->post_type ] : "default";

            } elseif ( is_post_type_archive() ) {

                $sed_post_type = get_queried_object()->name;
                $page_layout =  isset( $sed_pages_layouts[ "post_type_archive_" . $sed_post_type ] ) ? $sed_pages_layouts[ "post_type_archive_" . $sed_post_type ] : "default";

            } elseif ( is_author() ) {

                $page_layout = $sed_pages_layouts[ "author_page" ];

            } elseif ( is_date() || is_day() || is_month() || is_year() || is_time() ) {

                $page_layout = $sed_pages_layouts[ "date_archive" ];

            }

            return $page_layout;

        }

        /*function get_default_page_layout( ) {

            $args = array();

            if(is_category() || is_tag() || is_tax()){

                $tax = get_queried_object();

                $args['type']        = "tax";
                $args['taxonomy']    = $tax->taxonomy;
                $args['term_id']     = $tax->term_id;

            } elseif( is_home() === true && is_front_page() === true ){
                $args['type']          = "custom";
                $args['is_home_blog']  = true;
            } elseif( is_home() === false && is_front_page() === true ){
                $args['type']          = "post";
                $args['post_type']     = "page";
                $args['is_front_page'] = true;
            } elseif( is_home() === true && is_front_page() === false  ){
                $args['type']          = "post";
                $args['post_type']     = "page";
                $args['is_index_blog'] = true;
            } elseif ( is_search() ) {
                $args['type']        = "custom";
                $args['is_search_page'] = true;
            } elseif ( is_404() ) {
                $args['type']        = "custom";
                $args['is_404_page'] = true;
            } elseif( is_singular() ){
                global $post;

                $args['type']        = "post";
                $args['post_type']   = $post->post_type;
                $args['post_id']     = $post->ID;

            } elseif ( is_post_type_archive() ) {
                 $sed_post_type = get_queried_object()->name;

                $args['type']        = "custom";
                $args['post_type']   = $sed_post_type;
                $args['is_post_type_archive'] = true;

            } elseif ( is_author() ) {
                $args['type']        = "custom";
                $args['is_author_page'] = true;
            } elseif ( is_date() || is_day() || is_month() || is_year() || is_time() ) {
                $args['type']        = "custom";
                $args['is_date_archive'] = true;
            }

            $args = apply_filters( "sed_sub_theme_args" , $args );

            return get_default_page_layout( $args );
        }*/

        /*function add_contextmenu( $pagebuilder ){
            $context_menu = $pagebuilder->contextmenu;
            $context_menu->current_module = $this->module;
            $this->contextmenu( $context_menu );
            $context_menu->current_module = "";

            //Add Context Menu To All Main Row
            $context_menu = $site_editor_app->contextmenu;
            $row_menu = $context_menu->create_menu( "main_row" , __("Main Row","site-editor") , 'layout-row' , 'class' , 'row' , '.sed-layout-row' );

            $context_menu->add_title_bar_item($row_menu , __("Custom Row","site-editor"));
            $row_type_submenu = $context_menu->create_submenu( $row_menu ,"row_type" , __("Row Type","site-editor") , "row_type" , "class" );
            //add new type row on custom template
            do_action( 'add_type_row',$context_menu , $row_type_submenu );

            $context_menu->add_seperator_item($row_menu);
            $context_menu->add_settings_item($row_menu , "custom-row");
            $context_menu->add_seperator_item($row_menu);
            $context_menu->add_edit_style_item( $row_menu);
            $context_menu->add_seperator_item($row_menu);
            $context_menu->add_animation_item($row_menu);
            $context_menu->add_seperator_item($row_menu);
            $context_menu->add_show_on_page_item($row_menu);

            //add new custom menu item for developer using in custom template or ...
            do_action( 'add_item_to_row_menu',$context_menu , $row_menu );
        }*/

    }


    $layout = new SiteEditorLayoutManager();
    $site_editor_app->layout = $layout;
    $layout->add_toolbar_elements();

}


$site_editor_app->layout_patterns = array(

    //sed_main_content_row && sed_main_content attr for sub_theme module
    "default" => '[sed_row_outer_outer class="module_sed_columns_contextmenu_container" sed_main_content_row="true" shortcode_tag="sed_row" shortcode_tag="sed_row" type="static-element" length="boxed"]
            [sed_module_outer_outer class="module_sed_columns_contextmenu_container" shortcode_tag="sed_module"]
                [sed_columns_outer have_helper_id="true" pb_columns="2" shortcode_tag="sed_columns" class="" title="columns"]
                    [sed_column_outer  width="71%" shortcode_tag="sed_column" parent_module="columns"]
                       [sed_row_outer shortcode_tag="sed_row" type="static-element" sed_main_content = "true" ]
                          [sed_module_outer shortcode_tag="sed_module"]
                            {{content}}
                          [/sed_module_outer]
                       [/sed_row_outer]
                    [/sed_column_outer]
                    [sed_column width="29%" parent_module="columns"]

                    [/sed_column]
                [/sed_columns_outer]

                [sed_add_item_pattern is_helper_id="true" parent_module="columns"]
                    [sed_column parent_module="columns"][/sed_column]
                [/sed_add_item_pattern]
            [/sed_module_outer_outer]
        [/sed_row_outer_outer]'
);


//add_action( "sed_footer" , "print_layout_patterns" );
function print_layout_patterns(){
  ?>
    <script type="text/javascript">
        var _sedAppLayoutPatterns = <?php echo wp_json_encode( $site_editor_app->layout_patterns )?>;
    </script>
  <?php
}

/*

//START  ********* ADD && EDIT for sub_theme module
/*
@@args----
-----@type : post , tax , custom
-----@post_type : post , page , product , .... ---- type :  tax , post , custom
-----@post_id : 10 , 2000 , ...
-----@taxonomy : tag , category , .... ---- type :  tax
-----@term_id : 10 , 2000 , ...
-----@is_front_page( one page )     ---- type :  post , post_type : page
-----@is_home_blog                  ---- type :  custom
-----@is_index_blog(one page)       ---- type :  post , post_type : page
-----@is_search_page                ---- type :  custom
-----@is_404_page                   ---- type :  custom
-----@is_post_type_archive          ---- type :  custom
-----@is_author_page                ---- type :  custom
-----@is_date_archive               ---- type :  custom

function get_default_page_leyout( $args ){
    $args = array_merge(
        array(
            "type"                  =>  "post" ,
            "post_type"             =>  "" ,
            "taxonomy"              =>  "" ,
            "is_front_page"         =>  false ,
            "is_home_blog"          =>  false ,
            "is_index_blog"         =>  false ,
            "is_search_page"        =>  false ,
            "is_404_page"           =>  false ,
            "is_post_type_archive"  =>  false ,
            "is_author_page"        =>  false ,
            "is_date_archive"       =>  false ,
        ) , $args
    );

    return apply_filters( "sed_default_page_layout" , "default" , $args ) ;
}
*/
