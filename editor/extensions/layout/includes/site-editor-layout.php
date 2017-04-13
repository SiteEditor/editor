<?php


if(!class_exists('SiteEditorLayoutManager')){

    /**
     * Class SiteEditorLayoutManager
     */
    final class SiteEditorLayoutManager{

        /**
         * @var SedThemeContentSetting
         */
        public $page_theme_content_settings;

        /**
         * @var string
         */
        public $scope_control_id;

        /**
         * SiteEditorLayoutManager constructor.
         */
        function __construct( ) {

            require_once dirname( __FILE__ ) . '/theme-content-setting.class.php';

            $this->page_theme_content_settings = new SedThemeContentSetting();

            add_filter('sed_enqueue_scripts' , array( $this, 'add_js_plugin' ) );

            add_action( 'wp_enqueue_scripts' , array( $this, 'render_scripts' ) );

            add_filter( "sed_js_I18n" , array( $this ,'js_I18n' ) );

            add_action( "sed_footer" , array( $this , "print_app_data" ) );

            if( site_editor_app_on() )
                add_action( "wp_footer" , array( $this , "print_wp_footer" ) );

            $this->scope_control_id = "main_layout_row_scope_control";

            add_action( 'sed-app-save-data' , array( $this , 'save_check_main_content' ) , 10 , 2 );

            add_action( 'sed_app_register' ,  array( $this, 'register_settings' ) );

            require_once dirname( __FILE__ ) . '/site-editor-layouts-manager-options.class.php';

            new SiteEditorLayoutsManagerOptions();

            require_once dirname( __FILE__ ) . '/site-editor-assign-layouts-options.class.php';

            new SiteEditorAssignLayoutsOptions( $this );

		}

        public function add_js_plugin() {

            //wp_register_script("sed-app-layout", SED_EXT_URL . 'layout/js/app-layout-plugin.min.js' , array( 'siteeditor' ) , SED_APP_VERSION ,1 );
            //wp_enqueue_script("sed-app-layout");

            wp_enqueue_script("sed-layouts-content", SED_EXT_URL . 'layout/js/layouts-content-plugin.js' , array( 'siteeditor' ) , SED_APP_VERSION ,1 );

            wp_enqueue_script("sed-layouts-remove-row", SED_EXT_URL . 'layout/js/layouts-remove-row-plugin.js' , array( 'siteeditor' ) , SED_APP_VERSION ,1 );

            wp_enqueue_script("sed-app-layout", SED_EXT_URL . 'layout/js/layouts-main-plugin.js' , array( 'siteeditor' ) , SED_APP_VERSION ,1 );

            wp_enqueue_script("sed-layout-scope-control", SED_EXT_URL . 'layout/js/layout-scope-control-plugin.js' , array( 'siteeditor' ) , SED_APP_VERSION ,1 );

            wp_enqueue_script("layouts-manager-control", SED_EXT_URL . 'layout/js/layouts-manager-control-plugin.js' , array( 'siteeditor' ) , SED_APP_VERSION ,1 );

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
            $I18n['invalid_layout_row_title']   =  __("Row title not validate , only using letter , number , - , _ and space" , "site-editor");
            $I18n['empty_layout_slug']          =  __("Please enter slug" , "site-editor");
            $I18n['invalid_layout_slug']        =  __("Layout slug not validate , It should be English and only using letter , number , - and _" , "site-editor");
            $I18n['layout_already_exist']       =  __("The item is already exist in your leyouts." , "site-editor");
            $I18n['remove_default_layout']      =  __("you can not remove default layout" , "site-editor");
            $I18n['remove_current_layout']      =  __("you can not remove current page layout" , "site-editor");
            $I18n['layout_not_exist']           =  __("this layout not exist." , "site-editor");
            $I18n['invalid_layout']             =  __("The item is not valid layout." , "site-editor");
            $I18n['main_row_content']           =  __("Content" , "site-editor");

            return $I18n;
        }

        public function render_scripts(){

            wp_enqueue_script( 'app-layout-module', SED_EXT_URL . 'layout/js/app-layout-module.js', array( 'sed-frontend-editor' ) , SED_APP_VERSION , 1);

        }

        public function print_wp_footer(){
            $page_layout = self::get_default_page_layout(); 
          ?>
            <script type="text/javascript">
                var _sedAppDefaultPageLayout = "<?php echo $page_layout;?>";
                var _sedAppCurrentLayoutGroup = "<?php echo $this->get_current_layout_group();?>";
            </script>
          <?php
        }

        public function print_app_data(){
            $control_id = $this->scope_control_id;
            $sed_sub_themes = array();
            $sed_sub_themes = apply_filters( "sed_sub_themes" , $sed_sub_themes );
            include_once dirname( dirname( __FILE__ ) ) . DS . "view" . DS . "layout-template.php";
        }

        public static function default_pages_layouts_list(){

            $default_pages_layouts = array(
                "posts_archive"     =>  "archive" ,
                "index_blog"        =>  "default" ,
                "front_page"        =>  "default" ,
                "search_results"    =>  "default" ,
                "404_page"          =>  "default" ,
                "single_post"       =>  "post" ,
                "single_page"       =>  "page" ,
                "author_page"       =>  "archive" ,
                "date_archive"      =>  "archive" ,
            );

            return $default_pages_layouts;
        }

        public static function check_exist_main_content_model( $page_layout = '' ){

            if( empty( $page_layout ) ) {
                
                $page_layout = self::get_page_layout();
            }

            $sed_layout_models = get_option( 'sed_layouts_models' );

            $has_main_row = false;
            $sed_last_theme_id = get_option( 'sed_last_theme_id' );
            $main_row_theme_id = '';

            if( $sed_layout_models !== false && is_array( $sed_layout_models ) && isset( $sed_layout_models[$page_layout] ) ){

                foreach( $sed_layout_models[$page_layout] AS $key => $model ){
                    if( isset($model['main_row']) ){
                        $has_main_row = true;
                        $main_row_theme_id = $model['theme_id'];
                    }
                }

            }

            $sed_layouts_content = get_option( 'sed_layouts_content' );

            if( $has_main_row === false ) {
                if ($sed_last_theme_id !== false) {
                    $sed_last_theme_id += 1;
                    update_option('sed_last_theme_id', $sed_last_theme_id);
                } else {
                    $sed_last_theme_id = 1;
                    $deprecated = null;
                    $autoload = 'yes';
                    add_option('sed_last_theme_id', $sed_last_theme_id , $deprecated, $autoload);
                }

                $theme_id = "theme_id_" . $sed_last_theme_id;

                $new_model = array(
                    'order'     => 0,
                    'theme_id'  => $theme_id,
                    'main_row'  => true,
                    'hidden'    => array(),
                    'exclude'   => array(),
                    'title'     => __("Content", "site-editor")
                );

                $main_row_theme_id = $theme_id;

                $sed_newlayout_models = $sed_layout_models;

                if (!is_array($sed_newlayout_models)) {
                    $sed_newlayout_models = array();
                }

                if (!isset($sed_newlayout_models[$page_layout])) {
                    $sed_newlayout_models[$page_layout] = array();
                }

                array_push($sed_newlayout_models[$page_layout], $new_model);

                if ($sed_layout_models === false) {
                    $deprecated = null;
                    $autoload = 'yes';
                    add_option('sed_layouts_models', $sed_newlayout_models, $deprecated, $autoload);
                } else {
                    update_option('sed_layouts_models', $sed_newlayout_models);
                }
            }

            if( !is_array( $sed_layouts_content ) || ! isset( $sed_layouts_content[ $main_row_theme_id ] ) ){

                $default_layout = self::get_main_content_pattern( $main_row_theme_id );

                $new_sed_layouts_content = $sed_layouts_content;

                if( !is_array( $new_sed_layouts_content ) ){
                    $new_sed_layouts_content = array();
                }

                $new_sed_layouts_content[$main_row_theme_id] = $default_layout;

                if( $sed_layouts_content === false ){
                    $deprecated = null;
                    $autoload = 'yes';
                    add_option( 'sed_layouts_content' , $new_sed_layouts_content , $deprecated, $autoload );
                }else{
                    update_option( 'sed_layouts_content' , $new_sed_layouts_content );
                }

                return false;

            }

            return true;

        }

        public static function get_main_content_pattern( $main_row_theme_id ){

            $default_layout = '[sed_row_outer_outer sed_theme_id="' . $main_row_theme_id . '" sed_main_content_row="true" shortcode_tag="sed_row" type="static-element" length="wide"]
                    [sed_module_outer_outer shortcode_tag="sed_module"]
                        [sed_content_layout layout="without-sidebar" title="columns"]
                            [sed_content_layout_column width="100%" sed_main_content="yes" parent_module="content-layout"]
                                {{content}}
                            [/sed_content_layout_column]
                        [/sed_content_layout]
                    [/sed_module_outer_outer]
                [/sed_row_outer_outer]';

            return $default_layout;

        }

        public function save_check_main_content( $sed_page_customized , $all_posts_content ){

            if( isset( $sed_page_customized['sed_layouts_models'] ) ) {

                $value = $sed_page_customized['sed_layouts_models'] ;

                if (is_array($value) && !empty($value)) {

                    foreach ($value AS $layout => $models) {

                        self::check_exist_main_content_model($layout);

                    }

                }
            }

        }

        /**
         * Only will call after activate plugin
         */
        public static function init_data_layout(){

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


            if ( get_option( 'sed_layouts_settings' ) === false ) {

                //The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                $deprecated = null;
                $autoload = 'yes';

                add_option( 'sed_layouts_settings' , $default_layouts , $deprecated, $autoload );

                //@TODO call this codes after install for prevent error in front end in first time
                foreach( $default_layouts AS $layout => $layout_settings ) {
                    self::check_exist_main_content_model( $layout );
                }
            }

            $default_pages_layouts = self::default_pages_layouts_list();

            if ( get_option( 'sed_pages_layouts' ) === false ) {

                //The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                $deprecated = null;
                $autoload = 'yes';

                $current_pages_layouts = $default_pages_layouts;

                add_option( 'sed_pages_layouts' , $default_pages_layouts , $deprecated, $autoload );
            }

            if ( get_option( 'sed_layouts_models' ) === false ) {

                //The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                $deprecated = null;
                $autoload = 'yes';
                add_option( 'sed_layouts_models' , array() , $deprecated, $autoload );
            }

            if ( get_option( 'sed_layouts_removed_rows' ) === false ) {

                //The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                $deprecated = null;
                $autoload = 'yes';
                add_option( 'sed_layouts_removed_rows' , array() , $deprecated, $autoload );
            }

            if ( get_option( 'sed_last_theme_id' ) === false ) {

                //The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                $deprecated = null;
                $autoload = 'yes';
                add_option( 'sed_last_theme_id' , 0 , $deprecated, $autoload );
            }

            if ( get_option( 'sed_layouts_content' ) === false ) {

                //The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                $deprecated = null;
                $autoload = 'yes';
                add_option( 'sed_layouts_content' , array() , $deprecated, $autoload );

            }

        }

        public function register_settings( ){
            $settings = array();

            $settings['sed_layouts_settings'] = array(
    			'default'        => get_option( 'sed_layouts_settings' ),
    			'capability'     => 'manage_options',
    			'option_type'    => 'option' ,
                'transport'      => 'postMessage'
    		);

            $default_pages_layouts = self::default_pages_layouts_list();

            $current_pages_layouts = get_option('sed_pages_layouts');

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

            update_option( 'sed_pages_layouts' , $current_pages_layouts );

            $settings['sed_layouts_models'] = array(
    			'default'        => get_option( 'sed_layouts_models' ),
    			'capability'     => 'manage_options',
    			'option_type'    => 'option' ,
                'transport'      => 'postMessage'
    		);

            $settings['sed_layouts_removed_rows'] = array(
                'default'        => get_option( 'sed_layouts_removed_rows' ),
                'capability'     => 'manage_options',
                'option_type'    => 'option' ,
                'transport'      => 'postMessage'
            );

            $settings['sed_last_theme_id'] = array(
    			'default'        => get_option( 'sed_last_theme_id' ),
    			'capability'     => 'manage_options',
    			'option_type'    => 'option' ,
                'transport'      => 'postMessage'
    		);

            //register sed layouts content settings
            require_once dirname( __FILE__ ) . '/content-layout-setting.php';

            new SedLayoutContentSetting();

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

        public static function exist_layout( $layout ){
            $layout_settings = get_option( 'sed_layouts_settings' );

            if( $layout_settings === false || !is_array( $layout_settings ) ){
                return false;
            }

            $layouts = array_keys( $layout_settings );

            return in_array( $layout , $layouts );
        }

        public static function get_page_layout(){
            global $sed_data;
            $page_layout = ( isset( $sed_data['page_layout'] ) && !empty( $sed_data['page_layout'] ) ) ? $sed_data['page_layout'] : self::get_default_page_layout();

            if( !self::exist_layout( $page_layout ) ){
                return "default";
            }

            return $page_layout;
        }

        public static function get_default_page_layout(){

            $sed_pages_layouts = get_option( 'sed_pages_layouts' );

            if( is_category() || is_tag() ){

                $page_layout = $sed_pages_layouts["posts_archive"];

            }elseif( is_tax() ){

                $tax = get_queried_object();
                $page_layout = isset( $sed_pages_layouts[ "taxonomy_" . $tax->taxonomy ] ) ? $sed_pages_layouts[ "taxonomy_" . $tax->taxonomy ] : "default";

            } elseif( is_home() === true && is_front_page() === true ){

                $page_layout = $sed_pages_layouts[ "index_blog" ];

            } elseif( is_home() === false && is_front_page() === true ){

                $page_layout = $sed_pages_layouts[ "front_page" ];

            } elseif( is_home() === true && is_front_page() === false  ){

                $page_layout = $sed_pages_layouts[ "index_blog" ];

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

        /*
        *@@args----
        *-----@type : post , tax , custom
        *-----@post_type : post , page , product , .... ---- type :  tax , post , custom
        *-----@post_id : 10 , 2000 , ...
        *-----@taxonomy : tag , category , .... ---- type :  tax
        *-----@term_id : 10 , 2000 , ...
        *-----@is_front_page( one page )     ---- type :  post , post_type : page
        *-----@is_home_blog                  ---- type :  custom
        *-----@is_index_blog(one page)       ---- type :  post , post_type : page
        *-----@is_search_page                ---- type :  custom
        *-----@is_404_page                   ---- type :  custom
        *-----@is_post_type_archive          ---- type :  custom
        *-----@is_author_page                ---- type :  custom
        *-----@is_date_archive               ---- type :  custom
        */
        function get_current_layout_group(){

            if( is_category() || is_tag() ){

                $layout_group = "posts_archive";

            }elseif( is_tax() ){

                $tax = get_queried_object();
                $layout_group = "taxonomy_" . $tax->taxonomy;

            } elseif( is_home() === true && is_front_page() === true ){

                $layout_group = "index_blog";

            } elseif( is_home() === false && is_front_page() === true ){

                $layout_group = "front_page" ;

            } elseif( is_home() === true && is_front_page() === false  ){

                $layout_group = "index_blog" ;

            } elseif ( is_search() ) {

                $layout_group = "search_results";

            } elseif ( is_404() ) {

                $layout_group = "404_page";

            } elseif( is_singular() ){

                global $post;
                $layout_group = "single_" . $post->post_type ;

            } elseif ( is_post_type_archive() ) {

                $sed_post_type = get_queried_object()->name;
                $layout_group = "post_type_archive_" . $sed_post_type;

            } elseif ( is_author() ) {

                $layout_group = "author_page";

            } elseif ( is_date() || is_day() || is_month() || is_year() || is_time() ) {

                $layout_group = "date_archive" ;

            }

            return $layout_group;

        }

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

}