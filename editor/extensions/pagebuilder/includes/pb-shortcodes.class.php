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

    function __construct(  $args = array() ) {
        global $sed_pb_app;
        $args = array_merge( array(
            "type_icon"   => "font",
            "shortcode_type" => "enclosing",
            "is_child"       => false
        ) , $args);
        extract( $args );

        if(!isset($name) || empty($name) || !isset($module) || empty($module) )
            return false;

        $this->module = $module;        //var_dump($args);
        $this->shortcode = $sed_pb_app->array2obj( $args );

        add_shortcode( $this->shortcode->name , array( $this , 'shortcode_render') );

        add_action( 'sed_shortcode_register', array( $this , 'register_module_shortcode' ), 10   );
        //add_action( 'sed_ajax_pb', array( $this , 'ajax_register_shortcode' ), 10 , 1  );
        add_action( 'sed_contextmenu_init', array( $this , 'add_contextmenu' ) , 10 );
        add_filter( 'sed_shortcode_scripts_'.$this->shortcode->name , array( $this , 'call_scripts' ) , 10 , 1 );
        add_filter( 'sed_shortcode_styles_'.$this->shortcode->name , array( $this , 'call_styles' ) , 10 , 1 );
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
               $script = array($handle , $registered->src , $registered->deps , $registered->ver , $registered->args);
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

        if( $hidden_in_mobile && sed_is_mobile_version() ){
            return '';
        }

        if( $show_mobile_only && !sed_is_mobile_version() ){
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

        if( !site_editor_app_on() ){
            $new_custom_class = $sed_pb_app->generate_custom_css_class();

            if( isset( $atts['sed_css'] ) && !empty( $atts['sed_css'] ) ){
                global $sed_apps;
                $css_data = rawurldecode( $atts['sed_css'] );
                $css_data = json_decode( $css_data , true );
                if( !empty( $css_data ) && is_array( $css_data ) ){
                    $new_css_data = array();
                    foreach( $css_data AS $selector => $data ){
                        $selector = str_replace("##sed_custom_class##" , "." . $new_custom_class , $selector );
                        $new_css_data[$selector] = $data;
                    }
                    $sed_apps->framework->dynamic_css_data = array_merge( $sed_apps->framework->dynamic_css_data , $new_css_data );
                }
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

        if( site_editor_app_on() ){
            $sed_attrs .= $this->set_attr( 'sed_model_id', trim($this->atts['sed_model_id']) ) . " ";
        }

        $sed_attrs .= $animate["attr"];

        $this->set_vars( array( "sed_attrs" => $sed_attrs ) );

        array_push( $this->queue , self::$shortcode_counter_id );

        if(!empty( $content )){
            if( in_array( $this->shortcode->name , array("sed_paragraph" , "sed_text_title" , "sed_code_syntax_highlighter" ) ) && site_editor_app_on() ){
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

	public function animation($animation){

        if( !is_array($animation) && !empty($animation)  )
            $animation = explode(",",$animation);
        elseif(!is_array($animation) && empty($animation))
            return array('attr' => '','class' => '');

        $animation[3] = trim( $animation[3] );

        if( !isset($animation[3]) || empty( $animation[3] ) )
            return array('attr' => '','class' => '');


        $animate_attr = "";

        //isset($_REQUEST['preview_type']) && $_REQUEST['preview_type'] == "refresh" &&
        if( ( isset( $_REQUEST['sed_page_ajax'] ) && $_REQUEST['sed_page_ajax'] == "sed_load_modules" ) || ( site_editor_app_on() ) )
            $animate_class = "";
        else
            $animate_class = "wow ";


		if( $animation[0] != "" )
			$animate_attr .= $this->set_attr( 'data-wow-delay', trim($animation[0]) . "ms" );

		if( $animation[1] != "" )
			$animate_attr .= $this->set_attr( 'data-wow-iteration', trim($animation[1]) );

		if( $animation[2] != "")
			$animate_attr .= $this->set_attr( 'data-wow-duration', trim($animation[2]) . "ms" );

		if( $animation[3] != ""){
		    $animate_attr .= $this->set_attr( 'data-sed-animation', trim($animation[3]) );
			$animate_class .= trim($animation[3]) ;
		}

		if( $animation[4] != "")
			$animate_attr .= $this->set_attr( 'data-wow-offset', trim($animation[4]) );

        return array(
            'attr'    => $animate_attr ,
            'class'   => $animate_class
        );
	}

	public function set_attr( $nameAttr, $valueAttr ){

		if( $valueAttr != "" )
		    return  $nameAttr.'="'.$valueAttr.'" ';
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

        if( is_site_editor() )
            $this->create_settings();

        $shortcode['styles']  = array();//$this->call_styles();
        $shortcode['scripts'] = array();//$this->call_scripts();

        if( is_site_editor() ){
            $params = array("params" => $this->settings);
            $shortcode = array_merge($shortcode , $params);
        }

        $shortcode['attrs'] = $this->default_atts();

        if( is_site_editor() ){
            if($shortcode["asModule"])
                $pagebuilder->register_supports( $this->module , $this->supports() );

            $shortcode['panels'] = $this->panels();
        }

        $shortcode['php_class'] = get_class( $this );

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
                'capability'    => 'edit_theme_options' ,
                'type'          => 'fieldset' ,
                'description'   => '' ,
                'priority'      => 10
            ) , $args );
	}

    function supports(){
        return array();
    }

    function shortcode_settings(){
        return array();
    }

    function create_settings(){
        $this->atts = $this->default_atts();
        $params = apply_filters( "sed_shortcode_settings" , $this->shortcode_settings() , $this );

        $params[ 'id' ] = array(
            'type'          => 'text',
            'label'         => __('Module Id', 'site-editor'),
            'desc'          => __('Module Id For Anchor And ...', 'site-editor') ,
            'atts'          => array(
                "disabled"      =>      "disabled"
            ),
            'priority'      => 1001
        );

        $params[ 'class' ] = array(
            'type'          => 'text',
            'label'         => __('Extra class name', 'site-editor'),
            'desc'          => __('Style particular content element differently - add a class name and refer to it in custom CSS.', 'site-editor') ,
            'priority'      => 1000
        );


        $params[ 'hidden_in_mobile' ] = array(
            'type'          => 'checkbox',
            'label'         => __('Hidden In Mobile', 'site-editor'),
            'desc'          => __('Hidden Module In Mobile Version', 'site-editor') ,
            'control_type'  =>  "sed_element",
            'priority'      => 998
        );


        $params[ 'show_mobile_only' ] = array(
            'type'          => 'checkbox',
            'label'         => __('Show In Mobile Only', 'site-editor'),
            'desc'          => __('Show Module In Mobile Only', 'site-editor') ,
            'control_type'  =>  "sed_element",
            'priority'      => 999
        );


        $this->add_style_settings();

        ob_start();
        ?>
        <div class="sed_style_editor_panel_container">

        </div>
        <div id="modules_styles_settings_<?php echo $this->shortcode->name;?>_level_box" data-multi-level-box="true" data-title="" class="sed-dialog content " >

            <div class="styles_settings_container">

            </div>

        </div>
        <?php
        $dialog_content = ob_get_clean();

        if( $this->has_styles_settings === true ){
            $params[ 'design_panel' ] = array(
                'type' => 'panel_button',
                'label' => __('Custom Edit Style',"site-editor"),
                'desc' => '',
                'style' => 'blue' ,
                'class' => 'sed_style_editor_btn' ,
                'dialog_title' => __('Custom Edit Style',"site-editor") ,
                'dialog_content' => $dialog_content ,
                'priority'      => 0
            );
        }

        if(!empty( $params )){

            $this->settings = $this->get_params( $params );
        }

    }

    function add_style_settings(){
        global $sed_pb_app , $site_editor_app;
        $settings = $this->custom_style_settings();

        if( !empty( $settings ) ){

            $this->has_styles_settings = true;

            //$site_editor_app->style_editor_settings[$this->shortcode->name] = $settings;

            $this->style_editor_settings = $settings;

            add_action( "sed_footer" , array( $this , 'print_style_editor_settings' ) );
        }

    }

    function print_style_editor_settings(){
        global $site_editor_app;
        $panels = array();
        $controls = array();

        foreach( $this->style_editor_settings AS $setting ){
            if( is_array( $setting ) && count( $setting ) == 4 && is_array( $setting[2] ) ){

                $panel_id = $this->shortcode->name . '_' . $setting[0] . '_panel';

                $panels[$panel_id] = array(
                    'title'         =>  $setting[3]  ,
                    'label'         =>  $setting[3] ,
                    'capability'    => 'edit_theme_options' ,
                    'type'          => 'accordion_item' ,
                    'description'   => '' ,
                    'parent_id'     => 'root' ,
                    'priority'      => 9 ,
                    'id'            => $panel_id  ,
                    'atts'      =>  array(
                        'class'             => "design_ac_header" ,
                        'data-selector'     => $setting[1]
                    )
                );

                if( !empty($setting[2]) ){
                    foreach( $setting[2] AS $control ){
                        $controls[$this->shortcode->name . '_' . $setting[0] . '_' . $control ] = $site_editor_app->style_editor_controls->add_style_control( $control , $panel_id , $setting[1] );
                    }
                }

            }
        }



        if( !empty( $controls ) ){
            ModuleSettings::$group_id = $this->shortcode->name;
            $style_editor_settings = ModuleSettings::create_settings($controls, $panels);

            ?>
            <script type="text/html"  id="style_editor_panel_<?php echo $this->shortcode->name;?>_tmpl" >
                <div class="accordion-panel-settings">
                <?php echo $style_editor_settings;?>
                </div>
            </script>
            <?php

            ModuleSettings::$group_id = "";
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

    /*case 'group_skin' :
    case 'group_hover_effect' :
    skin_refresh
    */
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
                    $new_params[$key]["value"] =  ( isset($this->atts[$key]) ) ? $this->atts[$key] : ( ( isset($param["value"]) ) ? $param["value"] : "" );
                    $new_params[$key]["is_attr"] = ( isset($this->atts[$key]) ) ? true : ( ( isset($param["is_attr"]) ) ? $param["is_attr"] : false );
                }
            }
        }

        return $new_params;
    }

    //@mixin attachment ID or object
    function set_media( $attachment ){
        global $sed_apps;
        array_push( $sed_apps->editor->attachments_loaded , $attachment );

    }

}
