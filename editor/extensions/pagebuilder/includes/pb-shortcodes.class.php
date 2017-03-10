<?php
class PBShortcodeClass{

    public $skin_vars = array();
    public $settings = array();
    public $shortcode;
    public $scripts = array();
    public $styles = array();
    public $atts = array();
    public $init_module_js = false;
    //public $module_less_files = array();
    //public $custom_sub_skin = "";
    public $module;

    public $has_styles_settings = false;
    public $style_editor_settings = array();

    protected $panels = array();

    public $is_render = false;

    public static $shortcode_counter_id = 0;

    public $queue = array();

    /**
     * Module Actions Support , Allowed : "remove" , "duplicate" , "edit" , "move"
     *
     * @var string
     * @access public
     */
    public $actions = array( 'edit' , 'remove' , 'duplicate' , 'move' );

    /**
     * prefix for controls ids for prevent conflict
     *
     * @var string
     * @access public
     */
    public $control_prefix = '';


    function __construct(  $args = array() ) {
        global $sed_pb_app;

        $args = array_merge( array(
            "type_icon"   => "font",
            "shortcode_type" => "enclosing",
            "is_child"       => false ,
            "title"          => "" ,
            "description"    => ""
        ) , $args);

        extract( $args );

        if(!isset($name) || empty($name) || !isset($module) || empty($module) )
            return false;

        $this->module = $module;        //var_dump($args);

        $this->shortcode = $sed_pb_app->array2obj( $args );

        $this->control_prefix = $this->shortcode->name;

        add_shortcode( $this->shortcode->name , array( $this , 'shortcode_render') );

        if( !in_array( $this->shortcode->name , PageBuilderApplication::$shortcodes_tagnames ) )
            array_push( PageBuilderApplication::$shortcodes_tagnames , $this->shortcode->name );

        add_action( 'sed_shortcode_register', array( $this , 'register_module_shortcode' ), 10   );

        //add_action( 'sed_ajax_pb', array( $this , 'ajax_register_shortcode' ), 10 , 1  );

        add_action( 'sed_contextmenu_init', array( $this , 'add_contextmenu' ) , 10 );

        add_filter( "sed_shortcode_scripts_{$this->shortcode->name}" , array( $this , 'call_scripts' ) , 10 , 1 );

        add_filter( "sed_shortcode_styles_{$this->shortcode->name}" , array( $this , 'call_styles' ) , 10 , 1 );

        add_action( "sed_register_{$this->shortcode->name}_options" , array( $this , 'create_settings' ) );

        add_action( "sed_register_{$this->shortcode->name}_options" , array( $this, 'register_shortcode_group' ) , -9999 );
    }

    function add_script($handle , $src = "" , $deps = array() , $version = SED_APP_VERSION , $in_footer = true){
        global $wp_scripts;
        if(!empty($src)){
            $script = array($handle , $src , $deps , $version , $in_footer);

            if($this->is_render === true)
                call_user_func_array( "wp_enqueue_script" , $script );

            return $script;
        }else{
            
           if( wp_script_is( $handle, 'registered' ) ) {

               if($this->is_render === true)
                  wp_enqueue_script( $handle );

               $registered = $wp_scripts->registered[$handle];

               $src = $registered->src;

               $content_url = content_url();

               if ( ! preg_match( '|^(https?:)?//|', $src ) && ! ( $content_url && 0 === strpos( $src, $content_url ) ) ) {
                   $src = site_url() . $src;
               }

               /** This filter is documented in wp-includes/class.wp-scripts.php */
               $src = esc_url( apply_filters( 'script_loader_src', $src, $handle ) );

               if ( ! $src )
                   return false;

               $script = array($handle , $src , $registered->deps , $registered->ver , $registered->args);
               return $script;
           }
        }

        return false;
    }

    function add_style($handle , $src = "" , $deps = array() , $version = SED_APP_VERSION , $media = "all"){
        global $wp_styles;
        if(!empty($src)){
            $style = array($handle , $src , $deps , $version , $media);
            if($this->is_render === true)
                call_user_func_array( "wp_enqueue_style" , $style );

            return $style;
        }else{
           if( wp_style_is( $handle, 'registered' ) ) {

               if($this->is_render === true)
                   wp_enqueue_style( $handle );

               $registered = $wp_styles->registered[$handle];
               $style = array($handle , $registered->src , $registered->deps , $registered->ver , $registered->args);

               return $style;
           }
        }

        return false;
    }

    //@type :::::: ------------- main || skins(skin)
    function add_less( $handle , $module = "" , $type = "main" , $skin = "default" ){
        global $sed_apps;

        $module = !empty($module) ? $module : $this->module;

        $less_info = null;

        if($type == "main") {
            if( isset( $sed_apps->module_info[$module]['less'][$handle] ) )
                $less_info = $sed_apps->module_info[$module]['less'][$handle];
        }else{
            if( isset( $sed_apps->module_info[$module]['skins'][$skin] ) )
                $less_info = $sed_apps->module_info[$module]['skins'][$skin]['less'][$handle];

        }

        if( !is_null( $less_info ) && !empty( $less_info ) ){
            $less = array( $handle , SED_UPLOAD_URL. "/modules" . $less_info['src_rel'] , $less_info['deps'] , $less_info['ver'] , $less_info['media'] );

            if($this->is_render === true)
                call_user_func_array( "wp_enqueue_style" , $less );

            return $less;
        }

        return false;
    }

    function scripts(){
        return array();
    }

    function styles(){
        return array();
    }

    function less(){
        return array();
    }

    function call_styles( $new_styles = array() ){
        $styles = $this->styles();
        //$new_styles = array();

        if( !empty($styles) ){
            foreach( $styles AS $style ){
                if($new_style = call_user_func_array( array( $this , "add_style" ) , $style ) )
                    $new_styles[] = $new_style;
            }
        }

        $lesses = $this->less();

        if( !empty($lesses) ){
            foreach( $lesses AS $style ){
                if($new_style = call_user_func_array( array( $this , "add_less" ) , $style ) )
                    $new_styles[] = $new_style;
            }
        }

        return $new_styles;
    }

    function call_scripts( $new_scripts = array() ){
        $scripts = $this->scripts();
        //$new_scripts = array();

        if( !empty($scripts) ){
            foreach( $scripts AS $script ){
                if($new_script = call_user_func_array( array( $this , "add_script" ) , $script ) )
                    $new_scripts[] = $new_script;
            }
        }

        return $new_scripts;
    }

    function default_atts(){
        $atts = $this->get_atts();
        $atts = ( !empty($atts) && is_array($atts) ) ? $atts: array();
        $default_atts = array_merge( array(
            'id'                => "",
            'title'             => "",
            'skin'              => 'default' ,
            'sub_skin'          => '',
            'animation'         => '1000,1,1000,,0' ,
            'parent_module'     => '',
            'parent_skin'       => '',
            'parent_sub_skin'   => '',
            'class'             => '',
            'default_width'     => "100%" ,
            'default_height'    => "auto" ,
            'hidden_in_mobile'  => false ,
            'show_mobile_only'  => false ,
            'sed_css'           => '' ,
            'sed_model_id'      => ''  ,
        ), $atts );

        $default_atts['skin'] = 'default' ;

        return $default_atts;
    }

    function attrs_filter( $atts ){
        foreach( $atts  AS $key => $value ){
            if($value === 'false' || $value === 'true' )
                $atts[$key] = filter_var( $value , FILTER_VALIDATE_BOOLEAN );
        }

        return $atts;
    }

    function sed_remove_wpautop($content, $autop = false) {
        $content = do_shortcode( shortcode_unautop($content) );
        $content = preg_replace( '#^<\/p>|^<br \/>|<p>$#', '', $content );
        return $content;
    }

    function shortcode_render( $atts , $content = null){
        global $sed_pb_app;

        self::$shortcode_counter_id++;

        $hidden_in_mobile = ( !isset( $atts['hidden_in_mobile'] ) || $atts['hidden_in_mobile'] === "false" || !$atts['hidden_in_mobile'] ) ? false : true;
        $show_mobile_only = ( !isset( $atts['show_mobile_only'] ) || $atts['show_mobile_only'] === "false" || !$atts['show_mobile_only'] ) ? false : true;

        if( $hidden_in_mobile && ( sed_is_mobile_version() || wp_is_mobile() ) ){
            return '';
        }

        if( $show_mobile_only && !sed_is_mobile_version() && !wp_is_mobile() && !site_editor_app_on() ){
            return '';
        }


        if( $this->shortcode->name == "sed_row" ){   //sed_main_content_row
            if( isset( $atts['sed_main_content_row'] ) || isset( $atts['sed_main_content'] ) ){
                $main_class = ( isset($atts['sed_main_content_row']) ) ? 'sed-main-content-row-role' : 'sed-main-content-role' ;
                if(empty($atts['class']) || !$atts['class']){
                    $atts['class'] = $main_class;
                }else{
                    $atts['class'] = trim( $atts['class'] ) . " " . $main_class;
                }
            }
        }

        if( !site_editor_app_on() && ( !isset( $_POST['action'] ) || $_POST['action'] != "load_modules" ) ){

            if( isset( $atts['sed_css_class'] ) ) {

                $new_custom_class = $atts['sed_css_class'];

                if (isset($atts['sed_css']) && !empty($atts['sed_css'])) {
                    global $sed_apps;
                    $css_data = rawurldecode($atts['sed_css']);
                    $css_data = json_decode($css_data, true);
                    if (!empty($css_data) && is_array($css_data)) {
                        $new_css_data = array();
                        foreach ($css_data AS $selector => $data) {
                            $selector = str_replace("##sed_custom_class##", "." . $new_custom_class, $selector);
                            $new_css_data[$selector] = $data;
                        }
                        $sed_apps->framework->dynamic_css_data = array_merge($sed_apps->framework->dynamic_css_data, $new_css_data);
                    }
                }

            }else{

                $new_custom_class = $sed_pb_app->generate_custom_css_class();

            }


            if(empty($atts['class']) || !$atts['class']){
                $atts['class'] = $new_custom_class;
            }else{
                $atts['class'] = trim( $atts['class'] ) . " " . $new_custom_class;
            }

            $this->set_vars( array( "sed_custom_css_class" => $new_custom_class ) );
        }

        $this->is_render = true;
        $this->atts = array();

        $default_atts = $this->default_atts();

        $input_atts = $atts;

        $atts = shortcode_atts( $default_atts , $atts);

        //convert to booleen for 'false' && 'true'
        $atts = $this->attrs_filter( $atts );

        //add class to shortcodes for contextmenu
        if( !in_array( $this->shortcode->name , array("sed_module" , "sed_row") ) && !isset( $input_atts["contextmenu_disabled"] ) ){
            if(empty($atts['class']) || !$atts['class'])
                $atts['class'] = "module_" .$this->shortcode->name."_contextmenu";
            else
                $atts['class'] = trim( $atts['class'] ) . " module_" .$this->shortcode->name."_contextmenu";
        }

        if( $this->shortcode->is_child === false  && !isset( $input_atts["settings_disabled"] ) ){
            $atts['class'] .= " sed-pb-module-container";
        }

        if( isset($atts["has_cover"]) ){
            $atts["has_cover"] = 'sed-module-cover="has-cover"';
        }

        $animate = $this->animation( $atts['animation'] );

        if(empty($atts['class']) || !$atts['class'])
            $atts['class'] = $animate["class"];
        else
            $atts['class'] = trim( $atts['class'] ) . " " . $animate["class"];

        $this->atts = $atts;
        $this->add_shortcode($atts , $content);

        $this->set_vars( $this->atts );

        // $this->add_shortcode( $atts , $content);

        $this->call_styles();
        $this->call_scripts();

        if( site_editor_app_on() && !$this->shortcode->is_child && !$this->init_module_js && isset( $sed_pb_app->js_modules[$this->shortcode->module] ) ){

            $js_module = $sed_pb_app->js_modules[$this->shortcode->module]; 

            if( isset($js_module[0]) && !empty( $js_module[0] ) && isset($js_module[1]) && !empty( $js_module[1] ) ){
                $activate_modules = SiteEditorModules::pb_module_active_list();
                $handle = $js_module[0];

                $src = content_url( "/" . dirname( dirname( $activate_modules[ $this->shortcode->module ] ) ) . "/" . $js_module[1] );

                $deps       = (isset($js_module[2]) && is_array( $js_module[2] )) ? $js_module[2] : array();

                $ver        = (isset($js_module[3]) && !empty( $js_module[3] )) ? $js_module[3] : SED_APP_VERSION;

                $in_footer  = (isset($js_module[4])) ? $js_module[4] : true;

                wp_enqueue_script( $handle , $src , $deps, $ver, $in_footer );
            }

            $this->init_module_js = true;

        }

        $sed_attrs = '';

        if( site_editor_app_on() || ( isset( $_POST['action'] )  &&  $_POST['action'] == "load_modules" ) ){ 
            $sed_attrs .= $this->set_attr( 'sed_model_id', trim($this->atts['sed_model_id']) ) . " ";
        }

        if( isset( $this->atts['id'] ) && !empty( trim( $this->atts['id'] ) ) ){

            $sed_attrs .= $this->set_attr( 'id', trim($this->atts['id']) );

        }

        $sed_attrs .= $animate["attr"];

        $this->set_vars( array( "sed_attrs" => $sed_attrs ) );

        array_push( $this->queue , self::$shortcode_counter_id );

        if(!empty( $content )){
            if( in_array( $this->shortcode->name , array("sed_paragraph" , "sed_text_title" , "sed_raw_js" , "sed_code_syntax_highlighter" ) ) && site_editor_app_on() ){
                $content = $content;
            }else{
                $content = do_shortcode($content);
            }
        }else{
            $content = '';//__('Module is empty.' , 'site-editor' );
        }

        $current_id = array_pop( $this->queue );

        $content =  apply_filters( "sed_shortcode_content_".$this->shortcode->name , $content );

        $this->skin_vars[$current_id] = array_merge( $this->skin_vars[$current_id] ,  array( "content" => $content ) );

        extract( $this->skin_vars[$current_id] );

        $module_name = (!empty($parent_module)) ? $parent_module : $this->module;

        $custom_sub_skin = (!empty($parent_module) && !empty($parent_sub_skin)) ? $parent_sub_skin : $sub_skin;

        if( $this->shortcode->is_child === false ){
            //using in save page As one template
            /*array_push( $sed_pb_app->page_modules_using , array(
                "id"        => $id  ,
                "module"    => $this->module ,
                "skin"      => $skin
            ));*/
        }

        $output = $this->load_skin( $skin, $parent_module , $custom_sub_skin , $parent_skin , $current_id );
        if( $output == '' && site_editor_app_on() )
            $output = "empty module";

        $this->is_render = false;

        return $output; 
    }

    function add_shortcode( $atts , $content = null ){

    }

    function get_atts( ){
        return array();
    }

    function load_skin( $skin , $parent_module , $custom_sub_skin , $parent_skin = '' , $current_id ){
        global $sed_pb_app;

        $pb_skin = $sed_pb_app->skin;
        $pb_skin->module = $this->module;
        $activate_modules = SiteEditorModules::pb_module_active_list();
        $pb_skin->modules_path = WP_CONTENT_DIR . "/" . dirname( dirname( $activate_modules[ $this->module ] ) );
        $pb_skin->modules_base_url = content_url( "/" . dirname( dirname( $activate_modules[ $this->module ] ) ) ); 
        $pb_skin->shortcode = $this->shortcode->name;
        $pb_skin->skin_vars = $this->skin_vars[$current_id];

        $content = $pb_skin->load_skin( $skin , $parent_module , $custom_sub_skin , $parent_skin );

        return $content;
    }

    function set_vars( $vars ){
        $id = self::$shortcode_counter_id;
        if(is_array($vars)){

            if( !isset( $this->skin_vars[$id] ) || !is_array( $this->skin_vars[$id] ) )
                $this->skin_vars[$id] = array();

            $this->skin_vars[$id] = array_merge( $this->skin_vars[$id] , $vars);
        }
    }

	public function animation( $animation ){

        return sed_set_animation( $animation );

	}

	public function set_attr( $nameAttr, $valueAttr ){

		if( $valueAttr != "" )
		    return  $nameAttr.'="'. esc_attr( $valueAttr ) .'" ';
	}

    function ajax_register_shortcode( $ajax_pb ){
        global $sed_pb_app;

        $shortcode = $sed_pb_app->obj2array($this->shortcode);

        $shortcode["asModule"] = ($shortcode["is_child"] === true) ? false: true;
        if($shortcode["is_child"] === false){
            $shortcode['moduleName'] = $shortcode['module'];
        }else{
            $shortcode['parentModule'] = $shortcode['module'];
        }

        $shortcode['moduleLocation'] = $this->module;

        unset($shortcode['module']);
        unset($shortcode["is_child"]);

        $shortcode['styles']  = $this->call_styles();
        $shortcode['scripts'] = $this->call_scripts();

        $shortcode['attrs'] = $this->default_atts();

        $ajax_pb->register_shortcode( $shortcode );
    }

    function register_module_shortcode() {
        global $sed_pb_app , $site_editor_app;

        $pagebuilder = $site_editor_app->pagebuilder;

        $shortcode = $sed_pb_app->obj2array($this->shortcode);

        $shortcode["asModule"] = ($shortcode["is_child"] === true) ? false: true;
        if($shortcode["is_child"] === false){
            $shortcode['moduleName'] = $shortcode['module'];
        }else{
            $shortcode['parentModule'] = $shortcode['module'];
        }

        $shortcode['moduleLocation'] = $this->module;

        unset($shortcode['module']);
        unset($shortcode["is_child"]);

        /*if( is_site_editor() )
            $this->create_settings();*/

        $shortcode['styles']  = array();//$this->call_styles();
        $shortcode['scripts'] = array();//$this->call_scripts();

        /*if( is_site_editor() ){
            $params = array("params" => $this->settings);
            $shortcode = array_merge($shortcode , $params);
        }*/

        $shortcode['attrs'] = $this->default_atts();

        if( is_site_editor() ){
            if($shortcode["asModule"])
                $pagebuilder->register_supports( $this->module , $this->supports() );

            //$shortcode['panels'] = $this->panels();
        }

        $shortcode['php_class'] = get_class( $this );

        $shortcode['actions'] = $this->actions;

        $pagebuilder->register_shortcode( $shortcode , $this );

    }

	public function panels() {
		return $this->panels;
	}

	public function add_panel( $id, $args = array() ) {
	    if( is_array($args) )
		    $this->panels[ $id ] = array_merge( array(
                'id'            => $id  ,
                'title'         => ''  ,
                'description'   => '' ,
                'capability'    => 'edit_theme_options' ,
                'type'          => 'default' ,
                'priority'      => 10
            ) , $args );
	}

    function supports(){
        return array();
    }

    function shortcode_settings(){
        return array();
    }

    public function register_shortcode_group(){

        SED()->editor->manager->add_group( $this->shortcode->name , array(
            'capability'        => 'edit_theme_options',
            'theme_supports'    => '',
            'title'             => $this->shortcode->title ,
            'description'       => $this->shortcode->description ,
            'type'              => 'default',
        ));

        SED()->editor->manager->add_group( $this->shortcode->name . "_design_group" , array(
            'capability'        => 'edit_theme_options',
            'theme_supports'    => '',
            'title'             => __('Custom Edit Style' , 'site-editor') ,
            'description'       => '' ,
            'type'              => 'default',
        ));

    }

    function create_settings(){

        $this->atts = $this->default_atts();

        $params = apply_filters( "sed_shortcode_settings" , $this->shortcode_settings() , $this );

        if( $this->shortcode->name != "sed_row" ) {

            $this->add_panel( 'module_general_settings_outer' , array(
                'title'                 =>  __("General Settings" , "site-editor") ,
                'capability'            => 'edit_theme_options' ,
                'type'                  => 'inner_box' ,
                //'description'           => __("Module General Settings" , "site-editor") ,
                'priority'              => 510 ,
                'btn_style'             => 'menu' ,
                'has_border_box'        => false ,
                'icon'                  => 'sedico-setting-item' ,
                'field_spacing'         => 'sm'
            ));

            $this->add_panel( 'module_general_settings' , array(
                'title'                 =>  __("General Settings" , "site-editor") ,
                'capability'            => 'edit_theme_options' ,
                'type'                  => 'default' ,
                'parent_id'             => "module_general_settings_outer",
            ));

            $params[ 'id' ] = array(
                'type'            => 'text',
                'label'           => __('Module Id', 'site-editor'),
                'description'     => __('Module Id For Anchor And ...', 'site-editor') ,
                'has_border_box'  => false,
                'priority'        => 1001 ,
                'panel'           => 'module_general_settings'
            );

            $params[ 'class' ] = array(
                'type'            => 'text',
                'label'           => __('Extra class name', 'site-editor'),
                'description'     => __('Style particular content element differently - add a class name and refer to it in custom CSS.', 'site-editor') , 
                'has_border_box'  => false,
                'priority'        => 1000 ,
                'panel'           => 'module_general_settings'
            ); 

        }

        /*$this->add_panel( 'module_mobile_settings' , array(
            'title'         =>  __("Mobile Settings" , "site-editor") ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'default' ,
            //'description'   => __("Mobile Settings" , "site-editor") ,
            'priority'      => 999 ,
        ));

        $params[ 'hidden_in_mobile' ] = array(
            'type'              => 'checkbox',
            'label'             => __('Hidden In Mobile', 'site-editor'),
            'description'       => __('Hidden Module In Mobile Version', 'site-editor') ,
            'priority'          => 998 ,
            'has_border_box'    => false ,
            'panel'             => 'module_mobile_settings'
        );

        $params[ 'show_mobile_only' ] = array(
            'type'              => 'checkbox',
            'label'             => __('Show In Mobile Only', 'site-editor'),
            'description'       => __('Show Module In Mobile Only', 'site-editor') ,
            'priority'          => 999 ,
            'has_border_box'    => false ,
            'panel'             => 'module_mobile_settings'
        );*/


        $this->add_style_settings();

        if( $this->has_styles_settings === true ){
            //$option_group :: shortcode name , $css_setting_type :: "module"
            $params[ 'design_panel' ] = SED()->editor->design->get_design_options_field( $this->shortcode->name , "module" );
        }

        global $sed_pb_options_engine;

        $params = $sed_pb_options_engine->params_type_process( $params , $this );

        $params = $this->get_params( $params );

        foreach( $params AS $key => $param ){

            if( $key != "content" ){

                $param = $this->add_control_param( $this->shortcode->name , $key , $param );

            }

            if( isset( $param["preview_params"] ) && $param['is_attr'] ){

                $preview_params = $param["preview_params"];

                $preview_params['settingId'] = 'sed_pb_modules';

                $preview_params['shortcode'] = $this->shortcode->name;

                $preview_params['attr'] = $param['attr_name'];

                $param["preview_params"] = $preview_params;

            }

            $params[$key] = $param;

        }

        $panels = $this->panels();

        foreach( $panels AS $key => $panel ){

            if( $panel['type'] == 'fieldset' ){
                $panels[$key]['type'] = 'default';
            }else if( $panel['type']  == 'accordion_item' ){
                $panels[$key]['type'] = 'expanded';
            }

            $panels[$key]['option_group'] = $this->shortcode->name;

        }

        $new_options = sed_options()->fix_controls_panels_ids( $params , $panels , $this->control_prefix );

        $new_params = $new_options['fields'];

        $new_panels = $new_options['panels'];

        sed_options()->add_fields( $new_params );

        sed_options()->add_panels( $new_panels );

    }

    function get_params( $params ){

        $new_params = array();

        if( !empty( $params ) ){
            foreach( $params AS $key => $param ){
                if( is_array( $param ) ){

                    if( isset( $param["type"] ) && $param["type"] == "skin" ){
                        if( !isset( $param["atts"] ) )
                            $param["atts"] = array();

                        $param["atts"]['data-module-name']  = $this->module;
                    }


                    $new_params[$key] = $param;

                    if( isset( $param["desc"] ) ){

                        unset( $new_params[$key]["desc"] );

                        $new_params[$key]["description"] = $param["desc"];

                    }

                    if( isset( $param["options"] ) ){

                        unset( $new_params[$key]["options"] );

                        $new_params[$key]["choices"] = $param["options"];

                    }

                    /**
                     * if isset
                     */
                    $new_params[$key]["value"] =  ( isset($this->atts[$key]) ) ? $this->atts[$key] : ( ( isset($param["value"]) ) ? $param["value"] : "" );

                    $new_params[$key]["is_attr"] = ( isset($this->atts[$key]) ) ? true : ( ( isset($param["is_attr"]) ) ? $param["is_attr"] : false );

                }
            }
        }

        return $new_params;
    }

    /**
     * @param $name
     * @param $key
     * @param $param
     * @return mixed
     */
    public function add_control_param( $name , $key , $param ){

        $param = $this->filter_param_category( $param );

        $param = $this->filter_param_setting_id( $param );

        $is_style_setting = ( isset( $param['is_style_setting'] ) && is_bool( $param['is_style_setting'] ) ) ?  $param['is_style_setting'] : false;

        $is_style_setting = ( $param['category'] == "style-editor" ) ? true : $is_style_setting;

        $param['is_style_setting'] = $is_style_setting;

        $param['sub_category'] = $name;

        $value = isset( $param["value"] ) ? $param["value"] :  "";

        global $sed_pb_options_engine;

        $param['default_value'] = is_array( $value ) ?  implode("," , $value): $sed_pb_options_engine->sanitize_control_value( $value );

        unset( $param["value"] );

        $param['option_group'] = $name;

        //edit risk
        $is_attr = isset( $param["is_attr"] ) ? $param["is_attr"]: false;

        if( isset( $param['setting_id'] ) && $param['setting_id'] == 'sed_pb_modules' ){
            $param['shortcode'] = $name;
            $param['attr_name'] = ( isset( $param['attr_name'] ) && !empty( $param['attr_name'] ) ) ? $param['attr_name'] : $key;
            $param['is_attr']   = $is_attr;
        }

        return $param;
    }

    /**
     * @param $param
     * @return mixed
     */
    public function filter_param_setting_id( $param ){

        //for design editor fields ( like background ) not need to identify setting id
        if( !isset( $param['settings_type'] ) && !isset( $param['setting_id'] ) && $param['category'] == "style-editor" ){
            return $param;
        }

        if( !isset( $param['setting_id'] ) && isset($param['settings_type']) ) {

            $setting_id = ( !empty( $param['settings_type'] ) ) ? $param['settings_type'] : 'sed_pb_modules';

            $param['setting_id'] = $setting_id;

            unset( $param['settings_type'] );

        }else if( !isset( $param['setting_id'] ) || empty( $param['setting_id'] ) ){

            $param['setting_id'] = 'sed_pb_modules';

        }

        if( isset($param['settings_type']) ){
            unset( $param['settings_type'] );
        }

        return $param;
    }

    /**
     * @param $param
     * @return mixed
     */
    public function filter_param_category( $param ){

        if( !isset( $param['category'] ) && isset($param['control_category']) ) {

            $category = ( !empty( $param['control_category'] ) ) ? $param['control_category'] : 'module-settings';

            $param['category'] = $category;

            unset( $param['control_category'] );
        }else if( !isset( $param['category'] ) || empty( $param['category'] ) ){

            $param['category'] = 'module-settings';

        }

        if( isset($param['control_category']) ){
            unset( $param['control_category'] );
        }

        return $param;
    }

    public function add_style_settings(){

        $settings = $this->custom_style_settings();

        if( !empty( $settings ) ){

            $this->has_styles_settings = true;

            $this->style_editor_settings = $settings;

            $option_group = $this->shortcode->name . "_design_group";

            $control_prefix = $option_group;

            SED()->editor->design->add_style_options( $settings , $option_group , $control_prefix , $this->shortcode->name );

        }

    }

    function custom_style_settings(){
        return array();
    }

    function add_contextmenu(  ){
        global $site_editor_app;
        $context_menu = $site_editor_app->contextmenu;
        $context_menu->current_module = $this->module;
        $this->contextmenu( $context_menu );
        $context_menu->current_module = "";
    }

    function contextmenu( $context_menu ){

    }

    //@mixin attachment ID or object
    function set_media( $attachment ){ 

        if( site_editor_app_on() ) {
            array_push(SED()->editor->attachments_loaded, $attachment);
        }

    }

}
