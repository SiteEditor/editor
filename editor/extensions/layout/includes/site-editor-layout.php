<?php


if(!class_exists('SiteEditorLayoutManager')){

    /**
     * Class SiteEditorLayoutManager
     */
    class SiteEditorLayoutManager{

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

            add_action( 'sed_app_register' ,  array( $this, 'register_settings' ) );

            $this->scope_control_id = "main_layout_row_scope_control";

            add_action( "sed_editor_init" , array( $this, "add_toolbar_elements" ) );

            add_action( 'sed-app-save-data' , array( $this , 'save_check_main_content' ) , 10 , 2 );

            //add_action( "sed_register_sed_add_layout_options" , array( $this , 'add_layout_options' ) );

            add_action( "sed_app_register" , array( $this, 'register_add_layout_group' ) , -9999 );

            add_action( "sed_register_sed_pages_layouts_options" , array( $this , 'pages_layouts_options' ) );

            add_action( "sed_register_sed_pages_layouts_options" , array( $this, 'register_pages_layouts_group' ) , -9999 );

            add_action( 'sed_app_register' , array($this, 'add_layout_options'));

            //add_action( 'wp_footer'  , array( $this , 'add_dynamic_settings' ) );

		}

        
        function register_add_layout_group(){

            SED()->editor->manager->add_group( 'sed_add_layout' , array(
                'capability'        => 'edit_theme_options',
                'theme_supports'    => '',
                'title'             => __("Add New Layout" , "site-editor") ,
                'description'       => __("Add New Layout And Manage Layouts" , "site-editor") ,
                'type'              => 'default',
            ));

        }
        
        
        function add_layout_options(){

            ob_start();
            include dirname( dirname( __FILE__ ) ) . DS . "view" . DS . "add_layout.tpl.php";
            $html = ob_get_clean();


            sed_options()->add_field( 'layouts_manager' , array(
                'type'              => 'custom',
                'has_border_box'    => false ,
                'custom_template'   => $html ,
                'js_type'           => 'layouts_manager' ,
                'category'          => 'app-settings' ,
                'setting_id'        => "sed_layouts_settings" ,
                'option_group'      => 'sed_add_layout',
                'default'           => get_option( 'sed_layouts_settings' ),
                'capability'        => 'manage_options',
                'option_type'       => 'option' ,
                'transport'         => 'postMessage'
            ));

        }


        public function add_dynamic_settings(){

            SED()->editor->manager->add_dynamic_settings( array( 'sed_layouts_settings' ) );

        }


        function register_pages_layouts_group(){

            SED()->editor->manager->add_group( 'sed_pages_layouts' , array(
                'capability'        => 'edit_theme_options',
                'theme_supports'    => '',
                'title'             => __("Pages Layouts Settings" , "site-editor"),
                'description'       => __("Pages Layouts Settings" , "site-editor") ,
                'type'              => 'default',
            ));

        }

        function pages_layouts_options(){
 
            /*ob_start();
            include dirname( dirname( __FILE__ ) ) . DS . "view" . DS . "pages_layouts.tpl.php";
            $html = ob_get_clean();*/

            $fields = array();
            $panels = array();

            $panels['current_page_layout_panel'] = array(
                'id'            => 'current_page_layout_panel'  ,
                'title'         =>  __('Current Page Layout Settings',"site-editor")  ,
                'capability'    => 'edit_theme_options' ,
                'type'          => 'default' ,
                'description'   => '' ,
                'priority'      => 9 ,
                'option_group'  => 'sed_pages_layouts'
            );

            $fields['page_layout'] = array(
                'type'              => 'select',
                'default'           => $_POST['page_layout'] ,
                'label'             => __("Select page layout" ,"site-editor"),
                'description'       => '',
                'choices'           => array(
                    ''                  =>   __( "Using Default settings" , "site-editor" )
                ),
                'atts'              => array(
                    "class"             =>  "sed_all_layouts_options_select"
                ),
                'priority'          => 9 ,
                'category'          => 'app-settings' ,
                'setting_id'        => "page_layout" ,
                "panel"             => 'current_page_layout_panel' ,
                'option_group'      => 'sed_pages_layouts'
            );

            $default_pages_layouts = $this->default_pages_layouts_list();

            $default_pages_layouts_labels = array(
                "posts_archive"     =>  __('Post Archive Pages Layout',"site-editor"),
                "home_blog"         =>  __('Home Blog Page Layout',"site-editor") ,
                "front_page"        =>  __('Front Page Layout',"site-editor") ,
                "blog"              =>  __('Blog Page Layout',"site-editor") ,
                "search_results"    =>  __('Search Results Page Layout',"site-editor") ,
                "404_page"          =>  __('404 Page Layout',"site-editor") ,
                "single_post"       =>  __('Single Posts Layout',"site-editor") ,
                "single_page"       =>  __('Single Pages Layouts',"site-editor") ,
                "author_page"       =>  __('Author Pages Layout',"site-editor") ,
                "date_archive"      =>  __('Date Archive Pages Layout',"site-editor") ,
            );

            $current_pages_layouts = get_option('sed_pages_layouts');

            $panels['default_pages_layouts'] = array(
                'id'            => 'default_pages_layouts' ,
                'title'         =>  __('Default Pages Layouts',"site-editor")  ,
                'capability'    => 'edit_theme_options' ,
                'type'          => 'default' ,
                'description'   => '' ,
                'priority'      => 10 ,
                'option_group'  => 'sed_pages_layouts'
            );

            foreach( $default_pages_layouts AS $page_group => $layout ){

                $id = 'sed_pages_layouts[' . $page_group . ']';
                $layout = $current_pages_layouts[$page_group];

                $fields["group_".$page_group] = array(
                    'type'              => 'select',
                    'default'           => $layout ,
                    'label'             => $default_pages_layouts_labels[$page_group],
                    'description'       => '',
                    'choices'           => array(),
                    'atts'              => array(
                        "class"             =>  "sed_all_layouts_options_select"
                    ),
                    'priority'          => 15 ,
                    'category'          => 'app-settings' ,
                    'setting_id'        => $id ,
                    "panel"             => ( $_POST['current_layout_group'] == $page_group ) ?  "current_page_layout_panel" : "default_pages_layouts" ,
                    'option_group'      => 'sed_pages_layouts'
                );
            }

            $post_types = get_post_types( array( 'show_in_nav_menus' => true , 'public' => true ), 'object' );

            if ( !empty( $post_types ) ) {

                $panels['custom_post_types_layouts'] = array(
                    'id'            => 'custom_post_types_layouts' ,
                    'title'         =>  __('Custom Post Types Layouts',"site-editor")  ,
                    'capability'    => 'edit_theme_options' ,
                    'type'          => 'default' ,
                    'description'   => '' ,
                    'priority'      => 10 ,
                    'option_group'  => 'sed_pages_layouts'
                );

                foreach ($post_types AS $post_type_name => $post_type) {

                    if( in_array( $post_type_name , array( "post" , "page" ) ) )
                        continue;

                    $page_group = 'post_type_archive_' . $post_type_name ;
                    $id = 'sed_pages_layouts[' . $page_group . ']';
                    $layout = $current_pages_layouts[$page_group];

                    $fields["group_".$page_group] = array(
                        'type'              => 'select',
                        'default'           => $layout ,
                        'label'             => sprintf( __("%s post type archive layout" , "site-editor") , $post_type->labels->name ),
                        'description'       => '',
                        'choices'           => array(),
                        'atts'              => array(
                            "class"             =>  "sed_all_layouts_options_select"
                        ),
                        'priority'          => 15 ,
                        'category'          => 'app-settings' ,
                        'setting_id'        => $id ,
                        "panel"             => ( $_POST['current_layout_group'] == $page_group ) ?  "current_page_layout_panel" : "custom_post_types_layouts" ,
                        'option_group'      => 'sed_pages_layouts'
                    );


                    $page_group = 'single_' . $post_type_name ;
                    $id = 'sed_pages_layouts[' . $page_group . ']';
                    $layout = $current_pages_layouts[$page_group];

                    $fields["group_".$page_group] = array(
                        'type'              => 'select',
                        'default'           => $layout ,
                        'label'             => sprintf( __("%s single pages layout" , "site-editor") , $post_type->labels->name ),
                        'description'       => '',
                        'choices'           => array(),
                        'atts'              => array(
                            "class"         =>  "sed_all_layouts_options_select"
                        ),
                        'priority'          => 15 ,
                        'category'          => 'app-settings' ,
                        'setting_id'        => $id ,
                        "panel"             => ( $_POST['current_layout_group'] == $page_group ) ?  "current_page_layout_panel" : "custom_post_types_layouts" ,
                        'option_group'      => 'sed_pages_layouts'
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

                $panels['custom_taxonomies_layouts'] = array(
                    'id'            => 'custom_taxonomies_layouts'  ,
                    'title'         =>  __('Custom Taxonomies Layouts',"site-editor")  ,
                    'capability'    => 'edit_theme_options' ,
                    'type'          => 'fieldset' ,
                    'description'   => '' ,
                    'priority'      => 10 ,
                    'option_group'  => 'sed_pages_layouts'
                );

                foreach ( $taxonomies  as $taxonomy ) {

                    $page_group = 'taxonomy_' . $taxonomy->name ;
                    $id = 'sed_pages_layouts[' . $page_group . ']';
                    $layout = $current_pages_layouts[$page_group];

                    $fields["group_".$page_group] = array(
                        'type'              => 'select',
                        'default'           => $layout ,
                        'label'             => sprintf( __("%s term pages layout" , "site-editor") , $taxonomy->label ),
                        'description'       => '',
                        'choices'           => array(),
                        'atts'              => array(
                            "class"         =>  "sed_all_layouts_options_select"
                        ),
                        'priority'          => 15 ,
                        'category'          => 'app-settings' ,
                        'setting_id'        => $id ,
                        "panel"             => ( $_POST['current_layout_group'] == $page_group ) ?  "current_page_layout_panel" : "custom_taxonomies_layouts" ,
                        'option_group'      => 'sed_pages_layouts'
                    );

                }
            }

            sed_options()->add_panels( $panels );

            sed_options()->add_fields( $fields );

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

        public function add_js_plugin() {

            wp_register_script("sed-app-layout", SED_EXT_URL . 'layout/js/app-layout-plugin.min.js' , array( 'siteeditor' ) , SED_APP_VERSION ,1 );

            wp_enqueue_script( 'sed-app-layout' );

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
            wp_enqueue_script( 'app-layout-module', SED_EXT_URL . 'layout/js/app-layout-module.min.js', array( 'sed-frontend-editor' ) , SED_APP_VERSION , 1);
        }

        function print_wp_footer(){
            $page_layout = self::get_default_page_layout(); 
          ?>
            <script type="text/javascript">
                var _sedAppDefaultPageLayout = "<?php echo $page_layout;?>";
                var _sedAppCurrentLayoutGroup = "<?php echo $this->get_current_layout_group();?>";
            </script>
          <?php
        }

        function print_app_data(){
            $control_id = $this->scope_control_id;
            $sed_sub_themes = array();
            $sed_sub_themes = apply_filters( "sed_sub_themes" , $sed_sub_themes );
            include_once dirname( dirname( __FILE__ ) ) . DS . "view" . DS . "layout-template.php";
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

                $default_layout = '[sed_row_outer_outer class="module_sed_content_layout_contextmenu_container" sed_theme_id="' . $main_row_theme_id . '" sed_main_content_row="true" shortcode_tag="sed_row" type="static-element" length="boxed"]
                    [sed_module_outer_outer class="module_sed_content_layout_contextmenu_container" shortcode_tag="sed_module"]
                        [sed_content_layout layout="without-sidebar" title="columns"]
                            [sed_content_layout_column width="100%" sed_main_content="yes" parent_module="content-layout"]
                                {{content}}
                            [/sed_content_layout_column]
                        [/sed_content_layout]
                    [/sed_module_outer_outer]
                [/sed_row_outer_outer]';

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

        function save_check_main_content( $sed_page_customized , $all_posts_content ){

            if( isset( $sed_page_customized['sed_layouts_models'] ) ) {

                $value = $sed_page_customized['sed_layouts_models'] ;

                if (is_array($value) && !empty($value)) {

                    foreach ($value AS $layout => $models) {

                        self::check_exist_main_content_model($layout);

                    }

                }
            }

        }

        function register_settings( ){
            $settings = array();

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

            /*$settings['sed_layouts_settings'] = array(
    			'default'        => get_option( 'sed_layouts_settings' ),
    			'capability'     => 'manage_options',
    			'option_type'    => 'option' ,
                'transport'      => 'postMessage'
    		);*/

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

            if ( get_option( 'sed_layouts_removed_rows' ) === false ) {

                //The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                $deprecated = null;
                $autoload = 'yes';
                add_option( 'sed_layouts_removed_rows' , array() , $deprecated, $autoload );
            }

            update_option( 'sed_pages_layouts' , $current_pages_layouts );

            $settings['sed_layouts_removed_rows'] = array(
                'default'        => get_option( 'sed_layouts_removed_rows' ),
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

            //register sed layouts content settings
            require_once dirname( __FILE__ ) . '/content-layout-setting.php';

            new SedLayoutContentSetting();

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

                $layout_group = "home_blog";

            } elseif( is_home() === false && is_front_page() === true ){

                $layout_group = "front_page" ;

            } elseif( is_home() === true && is_front_page() === false  ){

                $layout_group = "blog" ;

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