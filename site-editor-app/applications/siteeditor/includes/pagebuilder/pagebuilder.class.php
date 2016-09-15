<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


Class PageBuilderApplication {

    /**
    * @var    icon base url for toolbar element
    * @since  1.0.0
    */
    public $toolbar;

    public $contextmenu;

    public $template;

    public $shortcodes = array();

    static $shortcode_tag_counter = 1;

    public $prefix;

    public $modules = array();

    public $shortcodes_tmpl = array();

    public $modules_scripts = array();

    public $modules_styles = array();

    public $modules_less_files = array();

    public $modules_inline_js = array();

    public $modules_inline_css = array();

    public $settings_supports = array();

    public $modules_length = array();

    public $shortcodes_length = array();

    public $sed_post_content_tpl = '';

    public $sed_post_shortcodes_model = array();

    public $shortcodes_tagnames = array();

    public $sed_theme_content = array();

    /**
    * Class constructor.
    *
    * @param   $args
    *
    *
    * @desc
    *
    *
    * @since   1.0.0
    */
    function __construct(  $args = array() ) {
        global $site_editor_app;

        $this->toolbar = $site_editor_app->toolbar;
        $this->contextmenu = $site_editor_app->contextmenu;
        $this->prefix = $site_editor_app->prefix;
        $this->template = 'default';
        $this->current_app = 'siteeditor';

        if( site_editor_app_on() ){
            add_action( 'the_post', array( &$this , 'parse_editable_content' ), 99999 );
            add_action("wp_footer" , array( $this, "get_pb_posts_shortcode_content") );
        }

        //remove extra p && br tag from site editor & add to default wp editor only
        remove_filter( 'the_content', 'wpautop' );

        add_filter('the_excerpt', array($this, 'sed_excerpt_filter') );

        if( !site_editor_app_on() ){
            add_filter('the_content', array($this, 'sed_post_ready'), 10);
        }

        add_action("sed_footer" , array($this, 'registered_shortcodes_settings') , 10000 );
        add_action("sed_footer" , array( $this, "write_shortcode_settings") );
        add_action("sed_footer" , array( $this, "write_style_editor_settings") );

        add_action("site_editor_ajax_load_modules", array($this, "page_builder_load_modules") );
        add_filter( "sed_addon_settings", array($this,'load_modules_settings'));

        //load helper shortcodes & ready for do shortcode
        add_action( "sed_page_builder" , array( $this , "register_helper_shortcodes" ) , 9999 , 1  ); //after_sed_pb_modules_load

        /*======================================================
            new system for theme content load hooks
        ========================================================*/
        add_filter( "sed_helper_shortcodes" , array( $this , "add_base_pattern_helper_shortcodes" ) );
        add_action( 'sed_region_template', array( $this , 'sed_process_template' ) );

    }

    function vc_shortcode_custom_css_class( $param_value, $prefix = '' ) {
    	$css_class = preg_match( '/\s*\.([^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', $param_value ) ? $prefix . preg_replace( '/\s*\.([^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', '$1', $param_value ) : '';

    	return $css_class;
    }


    /**
    * @save helper shortcodes --only-- after save post & theme content
    * @helper shortcodes is for wp shortcode nesed level
    * @like :
    * [sed_row]
    *   [sed_row_inner shortcode_tag = "sed_row" ] [/sed_row_inner]
    * [/sed_row]
    * ----------------------
    * @this function load saved helper shortcodes & add for new shortcode
    * @helper shortcodes models are :
    * ----------------------
    * array( "helper_shortcode_name1" => "main_shortcode_name1"  , "helper_shortcode_name2" => "main_shortcode_name2" )
    * ----------------------
    * Main purpose of this function is ready helper shortcodes for do_shortcode
    */
    public function register_helper_shortcodes( $pagebuilder ){
        $sed_helper_shortcodes = $this->get_helper_shortcodes();
        $sed_helper_shortcodes = ( $sed_helper_shortcodes && is_array( $sed_helper_shortcodes ) ) ? $sed_helper_shortcodes : array();

        if( !empty( $this->modules ) ){
            foreach( $this->modules AS $module_name => $options  ){
                if( isset( $options['helper_shortcodes'] ) && is_array( $options['helper_shortcodes'] ) ){
                    $sed_helper_shortcodes = array_merge( $sed_helper_shortcodes , $options['helper_shortcodes'] );
                }
            }
        }

        $sed_helper_shortcodes = apply_filters( "sed_helper_shortcodes" , $sed_helper_shortcodes );

        if( !empty( $sed_helper_shortcodes ) ){

            $this->shortcodes_tagnames = array_merge( $this->shortcodes_tagnames , array_keys( $sed_helper_shortcodes ) );

            $this->shortcodes_tagnames = array_unique( $this->shortcodes_tagnames );

            foreach( $sed_helper_shortcodes AS $shortcode => $main_shortcode_name ){
                if( isset( $this->shortcodes[ $main_shortcode_name ] ) ){
                    $main_shortcode_obj = $this->shortcodes[ $main_shortcode_name ]['object'];
                    add_shortcode( $shortcode , array( $main_shortcode_obj , 'shortcode_render' ) );
                }
            }
        }

    }

    public function get_helper_shortcodes( ){
        return get_option( 'sed_helper_shortcodes' );
    }

    public function get_post_helper_shortcodes( $post_id ){
        $helper_shortcodes = get_post_meta( $post_id, 'sed_helper_shortcodes' , true );
        if(empty( $helper_shortcodes )){
            return false;
        }else{
            return $helper_shortcodes;
        }
    }

    public function update_helper_shortcodes( $new_value ){
        $option_name = 'sed_helper_shortcodes';

        if ( get_option( $option_name ) !== false ) {

            // The option already exists, so we just update it.
            update_option( $option_name, $new_value );

        } else {

            // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
            $deprecated = null;
            $autoload = 'yes';
            add_option( $option_name, $new_value, $deprecated, $autoload );
        }
    }

    public function update_post_helper_shortcodes( $post_id , $new_value ){
        $option_name = 'sed_helper_shortcodes';

        if( !update_post_meta( $post_id , $option_name , $new_value ) )
            add_post_meta( $post_id , $option_name , $new_value, true );

    }

	/**
	 * @param Wp_Post $post
	 */
	public function parse_editable_content( $post ) {
        global $sed_apps;

        $post_id = (int) $sed_apps->sed_page_id;

        if ( $post_id > 0 && $post->ID === $post_id && ! defined( 'SED_LOADING_EDITABLE_CONTENT' )) {
            define( 'SED_LOADING_EDITABLE_CONTENT', true );

            remove_filter( 'the_content', 'wpautop' );

			ob_start();
			$this->get_shortcodes_model_by_content( $post->post_content );

			$post_content = ob_get_clean();

            $this->sed_post_content_tpl = '<script type="template/html" id="sed_template_post_content" style="display:none">' . rawurlencode( apply_filters( 'the_content', $post_content ) ) . '</script>';
			// We already used the_content filter, we need to remove it to avoid double-using
			remove_all_filters( 'the_content' );
			// Used for just returning $post->post_content
			add_filter( 'the_content', array( &$this, 'sed_editable_post_content' ) );
        }

	}

	/**
	 * @param $content
	 *
	 * @return string
	 */
	public function sed_editable_post_content( $content ) {
		// same addContentAnchor
		do_shortcode( $content ); // this will not be outputted, but this is needed to enqueue needed js/styles.

        global $post;
        $id = $post->ID;
        $output = '<div id="sed-post-content-container" data-post-id="'.$id.'" data-content-type="post" drop-placeholder="'.__("Drop Each Module Into The Content Area" , "site-editor").'" data-parent-id="root" class="sed-pb-post-container sed-pb-rows-box bp-component">';
        $output .= '</div>';

		return $output;
	}

    function sed_post_ready($content){
        global $post , $sed_data;

        if( is_singular() && $sed_data['page_id'] == $post->ID  ){
            $id = $post->ID;
            $output = '<div id="sed-pb-post-container'.$id.'" data-post-id="'.$id.'" data-content-type="post" drop-placeholder="'.__("Drop Each Module Into The Content Area" , "site-editor").'" data-parent-id="root" class="sed-pb-post-container sed-pb-rows-box bp-component">';
            $output .= $content;
            $output .= '</div>';
        }elseif( $post->ID ){
            $output = '<div class="sed-pb-post-container-disable-editing" sed-disable-editing="yes">';
            $output .= $content;
            $output .= '</div>';
        }else{
            $output = $content;
        }

        return $output;
    }

	/**
	 * @param $content
	 *
	 */
	function get_shortcodes_model_by_content( $content ) {
		if ( ! empty( $this->sed_post_shortcodes_model ) ) {
			return;
		}

        global $post;

        $content = $this->post_content_synchronization( $content );

		$shortcodes_models = self::get_pattern_shortcodes( $content );

        $this->sed_post_shortcodes_model[ $post->ID ] = $shortcodes_models['shortcodes'];

        echo $shortcodes_models['string'];
	}


	/**
	 * @param $content
	 * @param bool $is_container
	 * @param bool $parent_id
	 *
	 * @return string
	 */
	public static function get_pattern_shortcodes( $content_pattern, $parent_id = "root" , $module = "" , $module_shortcode = "" , $tagnames = array() ) {
		$string = '';
        $shortcodes = array();
        $content = array();
        $content_init = false;

        if( !empty( $tagnames ) && is_array( $tagnames ) )
            $pattern = self::shortcodes_regexp( $tagnames );
        else
            $pattern = self::shortcodes_regexp();
                                                      // '/s'
        $except_content = preg_split('/'. $pattern .'/s'  , $content_pattern );

        $j = 0;
        foreach( $except_content AS $ex_content){
            $ex_content = trim($ex_content);
            if(!empty($ex_content)){

                //$id = md5( time() . '-' . self::$shortcode_tag_counter ++ );
                $id = "sed_model_" . self::$shortcode_tag_counter ++;

                $content = array(
                    'tag'           => 'content',
                    'attrs_query'   => '' ,
                    'attrs'         => array(),
                    'id'            => $id,
                    'content'       => $ex_content ,
                    'parent_id'     => $parent_id,
                );

                $content['attrs']['sed_model_id'] = $id;

                $content_order = $j;
                break;
            }
            $j++;
        }

        if( count($except_content) > 1 ){
            if (   preg_match_all(  '/'. $pattern .'/s'  , $content_pattern , $matches ) && array_key_exists( 2, $matches ) ){
                $i = 0;
        		foreach ( $matches[2] as $index => $tag ) {

                    if(!empty($content) && $content_order == $i){
                        $shortcodes[]  = $content;
                        $content_init = true;
                    }

        			//$id = md5( time() . '-' . self::$shortcode_tag_counter ++ );
                    $id = "sed_model_" . self::$shortcode_tag_counter ++;

        			$shortcode = array(
        				'tag'           => $tag,
        				'attrs_query'   => $matches[3][ $index ],
        				'attrs'         => shortcode_parse_atts( $matches[3][ $index ] ),
        				'id'            => $id,
        				'parent_id'     => $parent_id,
        			);

                    $shortcode['attrs']['sed_model_id'] = $id;

                    $string_shortcode_tag = $shortcode['tag'];

                    if(isset( $shortcode['attrs']['shortcode_tag'] ) ){

                        $shortcode['tag'] = $shortcode['attrs']['shortcode_tag'];
                        unset($shortcode['attrs']['shortcode_tag']);
                    }

                    if( !empty($module) && $module_shortcode != $shortcode['tag'] && !in_array( $shortcode['tag'] , array( "sed_row" , "sed_module" ) )  && (!isset($shortcode['attrs']['parent_module']) || empty($shortcode['attrs']['parent_module']) ) ){
                        $shortcode['attrs']['parent_module'] = $module;
                    }else if( in_array( $shortcode['tag'] , array( "sed_row" , "sed_module" ) ) ){
                        $shortcode['attrs']['parent_module'] = "";
                    }

                    if( isset( $shortcode['attrs']['sed_css'] ) && !empty( $shortcode['attrs']['sed_css'] ) ){
                        global $sed_apps;
                        $css_data = rawurldecode( $shortcode['attrs']['sed_css'] );
                        $css_data = json_decode( $css_data , true );
                        if( !empty( $css_data ) && is_array( $css_data ) ){
                            $new_css_data = array();
                            foreach( $css_data AS $selector => $data ){
                                $selector = str_replace("##sed_custom_class##" , '[sed_model_id="'.$id.'"]' , $selector );
                                $new_css_data[$selector] = $data;
                            }
                            $sed_apps->dynamic_css_data = array_merge( $sed_apps->dynamic_css_data , $new_css_data );
                            $shortcode['attrs']['sed_css'] = $new_css_data;
                        }

                    }

                    if( isset( $shortcode['attrs']['sed_theme_id'] ) ){
                        $shortcode['theme_id'] = $shortcode['attrs']['sed_theme_id'];
                        unset( $shortcode['attrs']['sed_theme_id'] );
                    }

                    $shortcodes[] = $shortcode ;

                    $children = self::get_pattern_shortcodes( $matches[5][$index] , $id , $module , $module_shortcode , $tagnames );

                    if( !empty( $children['shortcodes'] ) ){
                        $shortcodes = array_merge($shortcodes , $children['shortcodes']);
                    }

        			$string .= '[' . $string_shortcode_tag . ' sed_model_id="' . $id . '" ' . $shortcode['attrs_query'] . ']' . $children['string'] . '[/' . $string_shortcode_tag . ']' ;

                }

                if(!empty($content) && $content_init === false){
                    $shortcodes[]           = $content;
                    $content_init           = true;
                }

            }
        }elseif( count($except_content) == 1 && !empty($content) ){
            $shortcodes[] = $content ;
            $string .= do_shortcode( $ex_content );
        }

		return array(
            "string"        => $string,
            "shortcodes"    => $shortcodes
        );
	}

    function add_row_synchronization( $matches ){

        $ex_content = wpautop( $matches[1] );

        return '[sed_row type="static-element" from_wp_editor="true"]'.$ex_content.'[/sed_row]';
    }

    function post_content_synchronization( $content ){
         global $sed_apps;

        $content = shortcode_unautop( trim( $content ) );
        $not_shortcodes = preg_split('/'. self::shortcodes_regexp( ) .'/', $content );

        foreach( $not_shortcodes AS $string){

			$temp = str_replace( array(
				'<p>',
				'</p>',
			), '', $string ); // just to avoid autop @todo maybe do it better like vc_wpnop in js.

            if( strlen(trim($temp))>0 ) {
                $content = preg_replace_callback("/(".preg_quote($string, '/')."(?!\[\/))/", array( $this , 'add_row_synchronization' ) , $content);
            }

        }

        return $content;
    }

    public static function shortcodes_regexp( $tagnames = array() ){

        if( empty($tagnames) ){
            global $site_editor_app;
            $tagnames = $site_editor_app->pagebuilder->shortcodes_tagnames;
        }

        if( isset( $_POST['default_helper_shortcodes'] ) && is_array( $_POST['default_helper_shortcodes'] ) ){
            $tagnames = array_merge( $tagnames , $_POST['default_helper_shortcodes'] );
        }

    	$tagregexp = join( '|', array_map('preg_quote', $tagnames ) );

        return '\\['                              // Opening bracket
		. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
		. "($tagregexp)"                     // 2: Shortcode name
		. '(?![\\w-])'                       // Not followed by word character or hyphen
		. '('                                // 3: Unroll the loop: Inside the opening shortcode tag
		.     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
		.     '(?:'
		.         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
		.         '[^\\]\\/]*'               // Not a closing bracket or forward slash
		.     ')*?'
		. ')'
		. '(?:'
		.     '(\\/)'                        // 4: Self closing tag ...
		.     '\\]'                          // ... and closing bracket
		. '|'
		.     '\\]'                          // Closing bracket
		.     '(?:'
		.         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
		.             '[^\\[]*+'             // Not an opening bracket
		.             '(?:'
		.                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
		.                 '[^\\[]*+'         // Not an opening bracket
		.             ')*+'
		.         ')'
		.         '\\[\\/\\2\\]'             // Closing shortcode tag
		.     ')?'
		. ')'
		. '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]

    }

    public function sed_excerpt_filter($output){
        global $post;

        if (empty($output) && !empty($post->post_content)) {
            $content = $post->post_content;
            $text = strip_tags(do_shortcode( $content ) , "<style><script>");
            $excerpt_length = apply_filters('excerpt_length', 250);
            $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
            $text = wp_trim_words($text, $excerpt_length, $excerpt_more);
            return $text; //wpautop($text)
        }

        return $output;
    }

    public function add_shortcode_module( $options = array() ){

        $module_options_arr = array_merge(array(
            "group"             => "" ,
            "name"              => "",
            "title"             => "",
            "description"       => "",
            "icon"              => "",
            "type_icon"         => "font",
            "shortcode"         => "",
            /*
            *@transport : the way shuolde added module to page or update in page , 3 way :: default || ajax || refresh
            * modules in default sended with default transport(default update with postMassage) like image module , icon module , ...
            * some modules create with ajax , they sended with ajax transport(default update with Ajax) like menu module , layer slider module , ...
            * some modules are base theme module and not appear in siteeditor toolbar and only create by theme , in this modules default update with refresh like archive module , posts module , ....
            */
            "transport"         => "default" ,
            "sub_modules"             => array() ,
            "show_settings_on_create" => true,
            "site_editor_type"  => "all" ,        //
            "module_type"        => "general" ,   // general || theme || app
            "show_ui_in_toolbar" => true ,
            "helper_shortcodes"  => array() , //support nested for same tagnames
        ), $options);

        extract($module_options_arr , EXTR_SKIP);

       if(empty($group) || empty($name) || empty($title) || empty($icon) || empty($type_icon) || $this->exist_module( $name ) === true){
            return false;
       }else{

            $this->modules[$name] = $module_options_arr;

            $tmpl = self::shortcode_tmpl_pattern( $name );
            if(empty($tmpl)){
                $tmpl = $this->shortcode_tmpl($this->shortcodes[$shortcode] , $name );
                $this->shortcodes[ $shortcode ]['has_pattern_tpl'] = false;
            }else
                $this->shortcodes[ $shortcode ]['has_pattern_tpl'] = true;

            $this->shortcodes_tmpl[$name] = $tmpl;


            if($show_ui_in_toolbar === true){
                $element_html = $this->toolbar_module($name,$title , $icon, $type_icon );

                $this->toolbar->add_element(
                    "modules" ,
                    $group ,
                    $name ,
                    $title ,
                    $element_html ,     //$def_content
                    "" ,                //icon
                    "" ,  //$capability=
                    array(),
                    array( "row" => 1 ,"rowspan" => 2 ) ,
                    array() ,
                    $site_editor_type //mixed string(eg all) or array( "pages" , "blog" , "woocommerce" , "search" , "single_post" , "archive" )
                );
            }

        }
    }

    public function exist_module( $module ){
        if(isset($this->modules[$module]))
            return true;
        else
            return false;
    }

    public static function shortcode_tmpl_pattern( $module ){

        $activate_modules = SiteEditorModules::pb_module_active_list();
        $module_path = WP_CONTENT_DIR . DS . dirname( $activate_modules[ $module ] );

        $path = $module_path . DS . "skins" . DS . "default" . DS;
        $def_skin_files = glob($path.'*');
        $tmpl = "";
        if(!empty($def_skin_files)){
            foreach ($def_skin_files as $file) {
                $file_name = basename($file);

                if( $file_name == "shortcode.pattern" ){
                    ob_start();
                        include $file;
                    $content = ob_get_contents();
                    ob_end_clean();

                    $module_base_url = content_url( "/" . dirname( $activate_modules[ $module ] ) );
                    $content = str_replace("{{@sed_module_url}}", $module_base_url , $content );
                    $content = str_replace("{{@sed_skin_url}}", $module_base_url . "/skins/default", $content );

                    $tmpl = $content;
                    break;
                }
            }
        }
        return $tmpl;
    }

    public function shortcode_tmpl( $shortcode_option , $module_name ){

        $shortcode = $shortcode_option['name'];
        $shortcode_type = $shortcode_option['shortcode_type'];
        $params = $shortcode_option['params'];
        //$pattern_type = $shortcode_option['pattern_type'];
        //$pattern = $shortcode_option['pattern'];

        $inline_js = $shortcode_option['inline_js'];
        $inline_css = $shortcode_option['inline_css'];

        if(!empty($inline_js)){
            if(!array_key_exists( $module_name , $this->modules_inline_js) )
                $this->modules_inline_js[ $module_name ] = array($inline_js);
            else
                $this->modules_inline_js[ $module_name ][] = $inline_js;
        }

        if(!empty($inline_css)){
            if(!array_key_exists( $module_name , $this->modules_inline_css) )
                $this->modules_inline_css[ $module_name ] = array($inline_css);
            else
                $this->modules_inline_css[ $module_name ][] = $inline_css;
        }

        $tmpl = "[". $shortcode ." ";
        //$attr_value = $this->get_shortcode_attrs($params , $shortcode_option['attrs']);
        $content_shortcode = "";
        $content = isset( $params['content']["value"] ) ? $params['content']["value"]: "";

        /*if(!empty($attr_value)){
            $str = implode(" ", $attr_value);
            $tmpl .= $str;
        }*/

        $tmpl .= "]";

        if($shortcode_type == "enclosing"){
            $tmpl .= $content_shortcode;
            $tmpl .= "[/". $shortcode."]";
        }

        return $tmpl;

    }

    public function register_supports( $module_name , $supports = array() ){
        $this->settings_supports[$module_name] = $supports;
    }

    public function register_shortcode( $options = array() , $shortcode_object ){
        global $site_editor_app;

        $shortcode_options_arr = array_merge(array(
            "name"              => "",
            "title"             => "",
            "description"       => "",
            "icon"              => "",
            "type_icon"         => "font",
            "shortcode_type"    => "enclosing", //self-closing shortcode: [tag] | enclosing shortcode: [tag]content[/tag]
            "attrs"             => array(),
            "params"            => array(),
            "panels"            => array(),
            //"settings"          => array(),
            //"controls"          => array(),
            "asModule"          => false,
            "moduleName"        => "",
            "moduleLocation"    => "",
            "parentModule"      => "",
            "inline_js"         => '',
            'inline_css'        => '',
            //"pattern_type"      => "default",    //  default || complex
            "pattern"           => "",
            "scripts"           => array(),      //array($handle, $src, $deps, $ver, $in_footer) ,array($handle, $src, $deps, $ver, $in_footer)
            "styles"            => array(),       //$handle, $src, $deps, $ver, $media
            "php_class"         => ""   ,
            'object'            => $shortcode_object
        ), $options);

        //$name = $shortcode_options_arr['name'];
        //$title = $shortcode_options_arr['title'];
        //$params = $shortcode_options_arr['params'];
             //var_dump(  "sed_module_name : " . $shortcode_options_arr['name'] );
             //var_dump(  $shortcode_options_arr['params'] );
       extract( $shortcode_options_arr );

       if(empty($name) ){  //|| empty($title)
            return false;
       }else{
            $this->shortcodes[$name] = $shortcode_options_arr;

            if( !in_array( $name , $this->shortcodes_tagnames ) )
                array_push( $this->shortcodes_tagnames , $name );

            if( is_site_editor() ){
                $module_name = (!empty($moduleName)) ? $moduleName : $parentModule;

                if( !empty( $styles ) && is_array( $styles ) ){
                    foreach( $styles As $style ){
                        if( !isset($this->modules_styles[$module_name]) )
                            $this->modules_styles[$module_name] = array();

                        array_push( $this->modules_styles[$module_name] , $style );
                    }
                }

                if( !empty( $scripts ) && is_array( $scripts ) ){
                    foreach( $scripts As $script ){
                        if( !isset($this->modules_scripts[$module_name]) )
                            $this->modules_scripts[$module_name] = array();

                        array_push( $this->modules_scripts[$module_name] , $script );
                    }
                }

                $tmpl = self::shortcode_tmpl_pattern( $module_name );
                if(empty($tmpl))
                    $tmpl = $this->shortcode_tmpl($this->shortcodes[$name] , $module_name );

                $this->shortcodes[$name]['pattern'] = $tmpl;

                if(!empty($params)){
                    sed_add_params( $name , $title , $params , $panels );
                }

            }
       }

    }

    public function add_module_group($parent_tab , $name , $title , $include = "all" ){
        $this->toolbar->add_element_group($parent_tab , $name , $title , $include);
    }


    private function toolbar_module($name , $title , $icon="", $icon_type="font"){
        if($icon_type == "font")
            $icon_class = $icon;
        elseif($icon_type == "img")
            $icon_img = $icon;

        ob_start();

        if(file_exists(SED_TMPL_PATH . DS . $this->template . DS . "modules/modules/view/module_element.php" )){
            require SED_TMPL_PATH . DS . $this->template . DS . "modules/modules/view/module_element.php" ;
        }elseif(file_exists(SED_APPS_PATH . DS . $this->current_app . DS . "modules/modules/view/module_element.php" )){
            require SED_APPS_PATH . DS . $this->current_app . DS .  "modules/modules/view/module_element.php" ;
        }

        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    public static function sanitize_attr_value( $value ){
        if( is_bool( $value ) )
            $value = ($value === true) ? "true" : "false";

        return $value;
    }

    function get_shortcode_attrs($shortcode_params , $shortcode_attrs = array()){
        $attrs = array();
        if(!empty($shortcode_attrs) && is_array($shortcode_attrs)){
            foreach($shortcode_attrs AS $attr => $value){
                if(!is_array($fsparam["value"])){
                    $attrs[] = $attr . '="' . self::sanitize_attr_value( $value ) .'"';
                }else{
                    $attrs[] = $attr . '="' . implode("," , $value).'"';
                }
            }
        }else{
            if(!empty($shortcode_params)){
                foreach($shortcode_params AS $key => $param){

                    if($key != "content" && !preg_match("/^fieldset/", $key)){
                        if(!is_array($param["value"])){
                            $attrs[] = $key . '="' . self::sanitize_attr_value( $param["value"] ).'"';
                        }else{
                            $attrs[] = $key . '="' . implode("," , $param["value"]).'"';
                        }
                    }elseif(preg_match("/^fieldset/", $key)){
                        foreach( $param as $fskey => $fsparam ){
                            if(!is_array($fsparam["value"])){
                                $attrs[] = $fskey . '="' . self::sanitize_attr_value( $fsparam["value"] ).'"';
                            }else{
                                $attrs[] = $fskey . '="' . implode("," , $fsparam["value"]).'"';
                            }
                        }
                    }
                }
            }
        }
        return $attrs;
    }

    function get_shortcode_js_attrs($shortcode_params){
        $attrs = array();
        if(!empty($shortcode_params)){
            foreach($shortcode_params AS $key => $param){

                if($key != "content" && !preg_match("/^fieldset/", $key)){
                    $attrs[$key] = $param["value"];
                }elseif(preg_match("/^fieldset/", $key)){
                    foreach( $param as $fskey => $fsparam ){
                        $attrs[$fskey] = $fsparam["value"];
                    }
                }
            }
        }
        return $attrs;
    }

    public function registered_shortcodes_settings(){
        global $sed_pb_app;
        $pattern_settings   = array();
        $scripts    = array();
        $styles     = array();
        $shortcodes = $this->shortcodes;

        foreach( $shortcodes AS $name => $shortcode){
            if($shortcode['asModule'])
                $parent_module = $shortcode['moduleName'];
            elseif( !empty( $shortcode['attrs']['parent_module'] ) )
                $parent_module = $shortcode['attrs']['parent_module'];
            else
                $parent_module = "";

            $scripts[$name] = apply_filters( 'sed_shortcode_scripts_'.$name , $shortcode['scripts'] );
            $styles[$name] = apply_filters( 'sed_shortcode_styles_'.$name , $shortcode['styles'] );

            if( $shortcode['asModule'] ){
                if( !$shortcode['has_pattern_tpl'] ){
                    //$settings[$name] = self::get_pattern_shortcodes( $shortcode['pattern'] , $parent_module , $name );
                //else
                    $pattern_settings[$name] = array( array( "attrs" => "" , "children" => array() , "name" => $name ) );
                }
            }

            $new_shortcode = $shortcode;
            unset( $new_shortcode['object'] );
            $shortcodes_settings[$name] = $new_shortcode;
        }

        $modules_options = $this->modules;
        $activate_modules = SiteEditorModules::pb_module_active_list();

        $js_modules = array();
        foreach( $sed_pb_app->js_modules AS $module_name => $js_module ){

            if( isset($js_module[0]) && !empty( $js_module[0] ) && isset($js_module[1]) && !empty( $js_module[1] ) ){
                $handle = $js_module[0];

                $src = content_url( "/" . dirname( dirname( $activate_modules[ $module_name ] ) ) . "/" . $js_module[1] );

                $deps       = (isset($js_module[2]) && is_array( $js_module[2] )) ? $js_module[2] : array();

                $ver        = (isset($js_module[3]) && !empty( $js_module[3] )) ? $js_module[3] : SED_APP_VERSION;

                $in_footer  = (isset($js_module[4])) ? $js_module[4] : true;

                $js_modules[$module_name] = array( $handle , $src , $deps, $ver, $in_footer );
            }
        }

        $helper_shortcodes = array();
        foreach( $this->modules AS $module_name => $module_options ){
            if( isset( $module_options['helper_shortcodes'] ) && is_array( $module_options['helper_shortcodes'] ) ){
                $helper_shortcodes = array_merge( $helper_shortcodes , $module_options['helper_shortcodes'] );
            }
        }

		?>
		<script type="text/javascript">
            var _sedAppEditorPageBuilderModules = <?php echo wp_json_encode( $modules_options ); ?>;
            var _sedRegisteredShortcodesSettings = <?php echo wp_json_encode( $shortcodes_settings ); ?>;
            var _sedShortcodesDefaultPatterns = <?php echo wp_json_encode( $pattern_settings ); ?>;
            var _sedRegisteredShortcodesScripts = <?php echo wp_json_encode( $scripts ); ?>;
            var _sedRegisteredShortcodesStyles = <?php echo wp_json_encode( $styles ); ?>;
            var _sedAppJsModulesForEditor = <?php echo wp_json_encode( $js_modules ); ?>;

            var _sedAppModulesSettingsSupports = <?php echo wp_json_encode( $this->settings_supports ); ?>;

            var _sedAppModulesGeneralSettings = <?php echo wp_json_encode( array( 'design_panel' , 'row_container' ) ); ?>;

            var _sedAppDefaultHelperShortcodes = <?php echo wp_json_encode( $helper_shortcodes ); ?>;
		</script>
		<?php

    }

    //writes the php config file for the default pattern
    function write_shortcode_settings(){

        $shortcode_settings_content = '<?php $shortcodes_tagnames = array();';

        foreach( $this->shortcodes_tagnames as $tag ){
            $shortcode_settings_content .= ' $shortcodes_tagnames[] = "'.$tag.'";';
        }
  		$shortcode_settings_content .= ' $shortcodes = array();';
  		foreach($this->shortcodes as $name => $shortcode){

            if($shortcode['asModule'])
                $parent_module = $shortcode['moduleName'];
            elseif( !empty( $shortcode['attrs']['parent_module'] ) )
                $parent_module = $shortcode['attrs']['parent_module'];
            else
                $parent_module = "";

            if( $shortcode['asModule'] ){
                //if( $shortcode['has_pattern_tpl'] ){
                $pattern = $shortcode['pattern'];

                $shortcode_settings_content .= "\r\n".'$shortcodes["'.$name.'"] = array( "pattern" => "'.addslashes($pattern).'"  , "parent_module" => "'.$parent_module.'" );';
                //}
            }
  		}

        $upload_dir = wp_upload_dir();

        if (!file_exists(trailingslashit($upload_dir['basedir']) . "site-editor")) {
            mkdir(trailingslashit($upload_dir['basedir']) . "site-editor", 0777, true);
        }

        $filename = trailingslashit($upload_dir['basedir']) . "site-editor/shortcodes.patterns.php";

        global $wp_filesystem;
        if( empty( $wp_filesystem ) ) {
            require_once( ABSPATH .'/wp-admin/includes/file.php' );
            WP_Filesystem();
        }

        if( $wp_filesystem ) {
            $wp_filesystem->put_contents(
                $filename,
                $shortcode_settings_content,
                FS_CHMOD_FILE // predefined mode settings for WP files
            );
        }

    }

    function write_style_editor_settings(){
        global $site_editor_app;
  		$style_editor_settings_content = '<?php $style_editor_settings = ' . var_export($site_editor_app->style_editor_settings, true) . ';';
        $style_editor_settings_content .= "\r\n".' $labeles = ' . var_export($site_editor_app->style_editor_controls->labeles, true) . ';';
        $style_editor_settings_content .= "\r\n".' $icons_classes = ' . var_export($site_editor_app->style_editor_controls->icons_classes, true) . ';';


        $upload_dir = wp_upload_dir();

        if (!file_exists(trailingslashit($upload_dir['basedir']) . "site-editor")) {
            mkdir(trailingslashit($upload_dir['basedir']) . "site-editor", 0777, true);
        }

        $filename = trailingslashit($upload_dir['basedir']) . "site-editor/style_editor_settings.php";

        global $wp_filesystem;
        if( empty( $wp_filesystem ) ) {
            require_once( ABSPATH .'/wp-admin/includes/file.php' );
            WP_Filesystem();
        }

        if( $wp_filesystem ) {
            $wp_filesystem->put_contents(
                $filename,
                $style_editor_settings_content,
                FS_CHMOD_FILE // predefined mode settings for WP files
            );
        }

    }

    function load_modules_settings( $sed_addon_settings ){
        global $site_editor_app;
        $sed_addon_settings['pb_modules'] = array(
            'nonce'  => array(
                'load'      =>  wp_create_nonce( 'sed_app_modules_load_' . $site_editor_app->get_stylesheet() ) ,
                'update'    =>  wp_create_nonce( 'sed_app_modules_update_' . $site_editor_app->get_stylesheet() )
            )
        );

        $sed_addon_settings['load_patterns'] = array(
            'nonce'  => array(
                'load'      =>  wp_create_nonce( 'sed_app_default_patterns_' . $site_editor_app->get_stylesheet() )
            )
        );

        return $sed_addon_settings;
    }

    function page_builder_load_modules(){
        global $sed_apps ;  //@args ::: sed_page_ajax , nonce
        $sed_apps->check_ajax_handler('sed_load_modules' , 'sed_app_modules_load');

        $shortcodes = json_decode( wp_unslash( $_REQUEST['pattern'] ), true );
        $parent_id = $_REQUEST['parent_id'];
        $tree_shortcodes = $sed_apps->app_save->build_tree_shortcode( $shortcodes , $parent_id );
        //convert to normal content with sed_do_shortcode
        $content = $this->sed_do_shortcode( $tree_shortcodes );

        $output = do_shortcode( $content );

        wp_send_json_success( $output );
    }

    function sed_page_builder_post_ready($content){
        global $post , $sed_data;

        if( is_singular() && $sed_data['page_id'] == $post->ID  ){
            $id = $post->ID;
            $output = '<div id="sed-pb-post-container'.$id.'" data-post-id="'.$id.'" data-content-type="post" drop-placeholder="'.__("Drop Each Module Into The Content Area" , "site-editor").'" data-parent-id="root" class="sed-pb-post-container sed-pb-rows-box bp-component">';
            $output .= $content;
            $output .= '</div>';
        }elseif( $post->ID ){
            $output = '<div class="sed-pb-post-container-disable-editing" sed-disable-editing="yes">';
            $output .= $content;
            $output .= '</div>';
        }else{
            $output = $content;
        }

        return $output;
    }


    function get_theme_shortcode_content(){
        return $this->sed_theme_content;
    }

    /*function get_shortcodes_modules_length_tag( $shortcodes , $tag ){
        $shortcodes_length = array();
        if(!empty( $shortcodes )){
            foreach($shortcodes AS $shortcode){
                if( $shortcode['tag'] == $tag  ){
                    if( isset($shortcodes_lengh[$tag]) ){
                        $shortcodes_length[$tag]['length'] += 1;
                    }else{
                        $shortcodes_length[$tag] = array( 'length' => 1 );
                    }
                }
            }
            return $shortcodes_length[$tag]['length'];
        }else{
            return 0;
        }


    }*/

    function get_main_content_shortcode_pattern( $skin = "default" , $module = "posts" , $shortcode = "sed_post" , $base_path = SED_PB_MODULES_PATH ){


        $activate_modules = SiteEditorModules::pb_module_active_list();
        $module_path = WP_CONTENT_DIR . DS . dirname( $activate_modules[ $module ] );

        $path_skin = $module_path . DS .'skins'. DS . $skin . DS;

        foreach (glob($path_skin.'*') as $file) {
            if( is_file($file) ){
                $file_name = basename($file);

                if( $file_name == "shortcode.pattern" ){
                    $content = file_get_contents( $file );

                    $module_base_url = content_url( "/" . dirname( $activate_modules[ $module ] ) );
                    $content = str_replace("{{@sed_module_url}}", $module_base_url , $content );
                    $content = str_replace("{{@sed_skin_url}}", $module_base_url . "/skins/$skin", $content );
                    continue;
                }
            }
        }
        return $content;

    }

    function get_default_theme_main_model( $skin = "default" , $module = "posts" , $shortcode = "sed_posts" , $include_row = true , $parent_id = "root" , $base_path = SED_PB_MODULES_PATH ){
        global $site_editor_app , $sed_apps ,$sed_data;

        $sed_page_id = $sed_data['page_id'];
        $sed_page_type = $sed_data['page_type'];

        $content = $this->get_main_content_shortcode_pattern( $skin , $module , $shortcode);

        //$content = do_shortcode( $content );

        if( $include_row === true ){
            $default_pattern = $site_editor_app->layout_patterns['default'] ;

            if( !in_array( "sed_module_outer" , $this->shortcodes_tagnames ) )
                array_push( $this->shortcodes_tagnames , "sed_module_outer");

            if( !in_array( "sed_row_outer" , $this->shortcodes_tagnames ) )
                array_push( $this->shortcodes_tagnames , "sed_row_outer");

            if( !in_array( "sed_column_outer" , $this->shortcodes_tagnames ) )
                array_push( $this->shortcodes_tagnames , "sed_column_outer");

            if( !in_array( "sed_columns_outer" , $this->shortcodes_tagnames ) )
                array_push( $this->shortcodes_tagnames , "sed_columns_outer");

            if( !in_array( "sed_module_outer_outer" , $this->shortcodes_tagnames ) )
                array_push( $this->shortcodes_tagnames , "sed_module_outer_outer");

            if( !in_array( "sed_row_outer_outer" , $this->shortcodes_tagnames ) )
                array_push( $this->shortcodes_tagnames , "sed_row_outer_outer");

            $activate_modules = SiteEditorModules::pb_module_active_list();
            $module_base_url = content_url( "/" . dirname( dirname( $activate_modules[ $module ] ) ) );

            $default_pattern = str_replace("{{@sed_module_url}}", $module_base_url , $default_pattern );
            $default_pattern = str_replace("{{content}}" , $content ,$default_pattern );
        }else{
            $default_pattern .= '[sed_row_outer shortcode_tag="sed_row" type="static-element" sed_main_content = "true" ]
              [sed_module_outer shortcode_tag="sed_module"]
                '.$content.'
              [/sed_module_outer]
            [/sed_row_outer]';

            if( !in_array( "sed_module_outer" , $this->shortcodes_tagnames ) )
                array_push( $this->shortcodes_tagnames , "sed_module_outer");

            if( !in_array( "sed_row_outer" , $this->shortcodes_tagnames ) )
                array_push( $this->shortcodes_tagnames , "sed_row_outer");

        }


        //create base pattern
        $shortcodes = $this->get_pattern_shortcodes( $default_pattern );


        //$this->load_pb_stock_modules_shortcodes();


        //convert to shortcode model
        $shortcodes_models = $this->build_shortcode_models($shortcodes , $parent_id , $sed_page_id );

        //set Helper Id
        $shortcodes_models = $this->set_helper_shortcodes( $shortcodes_models );

        return $shortcodes_models;
    }

    function get_default_theme_shortcode( $skin = "default" , $module = "posts" , $shortcode = "sed_posts" , $base_path = SED_PB_MODULES_PATH ){

        $shortcodes_models = $this->get_default_theme_main_model( $skin , $module , $shortcode , true , "root" , $base_path );

        $contents = $this->load_page_theme_content( $shortcodes_models , false );

        return $contents;
    }

    //set helper shortcodes like setHelperShortcodes in pagebuilder.min.js
    function set_helper_shortcodes( $shortcodes_models ){

        foreach( $shortcodes_models AS $mkey => $model ){
            if( isset($model['attrs']) && isset($model['attrs']['have_helper_id']) ){
                foreach( $shortcodes_models AS $key => $in_model ){
                    if($in_model['parent_id'] == $model['parent_id'] && $in_model['id'] != $model['id'] ){
                        if( isset($in_model['attrs']) && isset($in_model['attrs']['is_helper_id']) ){
                            $shortcodes_models[$key]['attrs']['module_helper_id'] = $model['id'];
                            unset($shortcodes_models[$key]['attrs']['is_helper_id']);
                        }
                    }
                }
                unset($shortcodes_models[$mkey]['attrs']['have_helper_id']);
            }
        }

        return $shortcodes_models;

    }

    //START ******** for sub_theme module-----
    public static function get_sub_themes_content_models(){
        if( get_option( 'sed_layouts_content' ) !== false )
            return get_option( 'sed_layouts_content' );
        else
            return array();
    }

    public static function get_theme_main_contents(){
        if( get_option( 'sed_main_theme_content' ) !== false )
            return get_option( 'sed_main_theme_content' );
        else
            return array();
    }

    public static function theme_row_order($a, $b) {
		if ( $a['order'] === $b['order'] ) {
			return $a['instance_number'] - $b['instance_number'];
		} else {
			return $a['order'] - $b['order'];
		}
    }

    function get_theme_rows_models( $curr_sub_themes_models , $theme_orders , $sed_layouts_content ){
        global $sed_data;
                                    //var_dump( $sed_layouts_content );
        if( empty( $curr_sub_themes_models ) )
            return array();

        $theme_rows = array();
        $orders = array();

        $i = 1;
        foreach( $curr_sub_themes_models AS $key => $model ){
            if( !in_array( $sed_data['page_id'] , $model['exclude'] ) ){
                $curr_sub_themes_models[$key]['instance_number'] = $i;
                $i++;
            }else
                unset( $curr_sub_themes_models[$key] );
        }

        uasort( $curr_sub_themes_models , array( 'PageBuilderApplication', 'theme_row_order' ) );

        foreach( $curr_sub_themes_models AS $model ){
            /*if( is_array($theme_orders) && isset( $theme_orders[ $model['theme_id'] ] ) ){
                $order = $theme_orders[ $model['theme_id'] ];
            }else */
                $order = $model['order'];

            $order = $this->get_theme_row_order( $orders , $order );

            array_push( $orders , $order );

            $new_model = $model;

            unset( $new_model['order'] );

            $new_model['content'] = $sed_layouts_content[ $model['theme_id'] ];

            $theme_rows[$order] = $new_model;

        }

        return $theme_rows;

    }

    function get_theme_row_order( $orders , $order ){
        if( in_array( $order , $orders ) ){
            $order +=1;
            return $this->get_theme_row_order( $orders , $order );
        }else
            return $order;
    }

    function load_sub_theme( $def_sub_theme , $skin , $module , $shortcode , $base_path = SED_PB_MODULES_PATH ){

        global $sed_data , $site_editor_app , $post , $content_width;


        if( !is_array( $sed_data['theme_content'] ) )
            $sed_data['theme_content'] = array();

        $sub_themes_models = get_option("sed_layouts_models");

        $sub_theme = !empty( $sed_data['page_layout'] ) ? $sed_data['page_layout'] : $def_sub_theme;

        if( isset( $sub_themes_models[ $sub_theme ] ) && is_array( $sub_themes_models[ $sub_theme ] ) && !empty( $sub_themes_models[ $sub_theme ] ) ){
            $curr_sub_themes_models = $sub_themes_models[ $sub_theme ];

            $sed_layouts_content = self::get_sub_themes_content_models();

            $is_customize_main_row = false;

            if( is_array( $sed_data['theme_content'] ) && !empty( $sed_data['theme_content'] ) ){

                foreach( $sed_data['theme_content'] AS $index => $row ){
                    foreach( $row AS $key => $sh_model ){
                        if( isset( $sh_model['attrs'] ) && isset( $sh_model['attrs']['sed_main_content_row'] ) && $sh_model['attrs']['sed_main_content_row'] && isset( $sh_model['is_customize'] ) && $sh_model['is_customize']   ){
                            $is_customize_main_row = true;
                            break;
                        }
                    }
                }

            }

            $load_stock_mod_sh = true;



            $main_content = false;
            $main_row_theme_id = false;

            if( $is_customize_main_row === false ){

                $sub_theme_ids = !empty( $curr_sub_themes_models ) ? wp_list_pluck( $curr_sub_themes_models, 'theme_id' ) : array();

                $sed_main_theme_content = self::get_theme_main_contents();

                foreach( $sed_main_theme_content AS $main_cmodel ){
                    if( in_array( $main_cmodel['theme_id'] , $sub_theme_ids ) ) {

                        $main_row_theme_id = $main_cmodel['theme_id'];

                        if( $main_cmodel['module'] == $module ){
                            $main_content = $main_cmodel['content'];
                            break;
                        }

                    }
                }

                if( $main_row_theme_id === false && ( !isset( $sed_data['page_sync'] ) || $sed_data['page_sync'] === false || ( isset( $sed_data['changed_sub_theme'] ) && $sed_data['changed_sub_theme'] === true && isset( $sed_data['changed_sub_theme_mode'] ) && $sed_data['changed_sub_theme_mode'] == "has_main_content" ) )  ){

                    $sed_data['theme_content'][] = $this->get_default_theme_main_model( $skin , $module , $shortcode , true , "root" , $base_path );

                    $load_stock_mod_sh = false;

                }else if( $main_row_theme_id !== false && $main_content === false ){

                    foreach( $sed_layouts_content[$main_row_theme_id] AS $key => $shortcode ){
                        if( isset($shortcode['attrs'] ) && isset($shortcode['attrs']['sed_main_content'] ) ){

                            $main_content = $this->get_default_theme_main_model( $skin , $module , $shortcode , false , $shortcode['parent_id'] , $base_path );

                            array_splice( $sed_layouts_content[$main_row_theme_id] , $key , 1 , $main_content);

                        }
                    }

                    $load_stock_mod_sh = false;


                }else if( $main_row_theme_id !== false && $main_content !== false ){
                    $index = 0;
                    foreach( $sed_layouts_content[$main_row_theme_id] AS $key => $shortcode ){
                        if( isset($shortcode['attrs'] ) && isset($shortcode['attrs']['sed_main_content'] ) ){
                            $index = $key;
                            $parent_id = $shortcode['parent_id'];
                        }
                    }
                         // var_dump( $main_content );
                    $main_content[0]['parent_id'] = $parent_id;   //var_dump( $parent_id );

                    array_splice( $sed_layouts_content[$main_row_theme_id] , $index , 1 , $main_content );

                }
            }
                                
            $theme_rows_models = $this->get_theme_rows_models( $curr_sub_themes_models , $sed_data['page_theme_rows_orders'] , $sed_layouts_content );
            //var_dump( $theme_rows_models );

            $theme_content_models = array();

            $num_rows = count( $sed_data['theme_content'] ) + count( $curr_sub_themes_models ) ;

            $j = 0;

            for ($i=0; $i < $num_rows  ; $i++)  {

                if( isset( $theme_rows_models[$i] ) ){
                    if( isset( $theme_rows_models[$i]['content'] ) && is_array( $theme_rows_models[$i]['content'] ) ){
                        $theme_content_models = array_merge( $theme_content_models , $theme_rows_models[$i]['content'] );
                    }

                    unset( $theme_rows_models[$i] );
                }else if( isset( $sed_data['theme_content'][$j] ) ){

                    $sh_model = $sed_data['theme_content'][$j][0];
                    if( isset( $sh_model['attrs'] ) && isset( $sh_model['attrs']['sed_main_content_row'] ) && $sh_model['attrs']['sed_main_content_row'] && !isset( $sh_model['is_customize'] ) && $main_row_theme_id !== false ){
                        continue;
                    }

                    $theme_content_models = array_merge( $theme_content_models , $sed_data['theme_content'][$j] );
                    $j++;
                }

            }

            //merge other theme rows with order more than $num_rows
            if( !empty( $theme_rows_models ) ){
                foreach( $theme_rows_models AS $model ){
                    $theme_content_models = array_merge( $theme_content_models , $model['content'] );
                }
            }

            $sed_data['theme_content'] = $theme_content_models;

        }else{

            if( !isset( $sed_data['page_sync'] ) || $sed_data['page_sync'] === false ){
                $sed_data['theme_content'] = $this->get_default_theme_main_model( $skin , $module , $shortcode , true , "root" , $base_path );
                $load_stock_mod_sh = false;
            }else{
                                                      //var_dump( $sed_data['theme_content'] );
                $theme_content_models = array();

                $num_rows = count( $sed_data['theme_content'] ) ;

                for ($i=0; $i < $num_rows  ; $i++)  {
                    $theme_content_models = array_merge( $theme_content_models , $sed_data['theme_content'][$i] );
                }

                $sed_data['theme_content'] =  $theme_content_models;

                $load_stock_mod_sh = true; 

            }
        }



        $theme_content = $sed_data['theme_content'];

        if( !empty( $theme_content ) ){
            foreach( $theme_content AS $key => $model ){
                if( isset($model['attrs'] ) && isset($model['attrs']['sed_main_content'] ) ){
                    if( isset($theme_content[$key-1]['attrs'] ) && isset($theme_content[$key-1]['attrs']['width'] ) ){
                        $width = $theme_content[$key-1]['attrs']['width'];
                        $width = str_replace( "%" , "" , $width );
                        if( isset( $sed_data['sheet_width'] ) ){
                            $sheet_width = $sed_data['sheet_width'];
                        }else{
                            $sheet_width = 1100;
                        }

                        $content_width = floor( ($width * $sheet_width)/100 );
                                    
                        if( isset( $theme_content[$key+1]['attrs'] ) && isset( $theme_content[$key+1]['attrs']['spacing_right'] ) ){
                            $spacing_right = $theme_content[$key+1]['attrs']['spacing_right'];
                            $spacing_right = ( $spacing_right == "auto" ) ? 0 : (int)$spacing_right;
                            $content_width -= $spacing_right;
                        }

                        if( isset( $theme_content[$key+1]['attrs'] ) && isset( $theme_content[$key+1]['attrs']['spacing_left'] ) ){
                            $spacing_left = $theme_content[$key+1]['attrs']['spacing_left'];
                            $spacing_left = ( $spacing_left == "auto" ) ? 0 : (int)$spacing_left;
                            $content_width -= $spacing_left;
                        }

                        if( is_archive() ){
                            $content_width -= 52;
                        }

                    }else{
                        //add one wp error
                    }

                    break;
                }
            }
        }

        $content = $site_editor_app->pagebuilder->load_page_theme_content( $theme_content , $load_stock_mod_sh );

        ob_start();
        ?>
         <script>
            var _sedAppMainContentShortcode = "<?php echo $shortcode;?>";
            var _sedAppMainContentModule = "<?php echo $module;?>";
            var _sedAppDefaultSubTheme = "<?php echo $def_sub_theme;?>";
         </script>
        <?php
        $content .= ob_get_clean();

        return $content;
    }
    //END ********* for sub_theme module-----

    function load_page_theme_content( $shortcodes_models , $load_stock_mod_sh = true ){
        global $sed_apps;

        $this->sed_theme_content = $shortcodes_models;

        //if($load_stock_mod_sh === true)
            //$this->load_pb_stock_modules_shortcodes();

        //convert to tree model
        $tree_shortcodes = $sed_apps->app_save->build_tree_shortcode( $shortcodes_models , "root" );

        $contents = $this->sed_do_shortcode( $tree_shortcodes );

        //convert to normal content with sed_do_shortcode
        $contents = do_shortcode( $contents );

        return $contents;
    }

    function do_shortcode_post_content( $content ){
        global $site_editor_app , $post, $sed_apps ;

        if(isset( $_POST['sed_page_customized'] ) && isset( $_POST['sed_posts_content'] ) ){
            $sed_posts_content = json_decode( wp_unslash( $_POST['sed_posts_content'] ), true );
        }else{
            $sed_posts_content = array();
        }

        $sed_posts_content = apply_filters( "sed_posts_content_filter" , $sed_posts_content );

        if( isset($sed_posts_content[ $post->ID ]) )
            $shortcodes_models = $sed_posts_content[ $post->ID ];


        if( !isset( $shortcodes_models ) ){
            //sync content
            $content = $this->post_content_synchronization( $content , $post->ID );

            //create base pattern
            $shortcodes = $this->get_pattern_shortcodes( $content );

            //load old saved shortcodes models
            $old_shortcodes = $this->get_pb_post_content( $post->ID );

            //convert to shortcode model
            $shortcodes_models = $this->build_shortcode_models($shortcodes , "root" , $post->ID , $old_shortcodes );
        }//else
            //$shortcodes_models = array();

        //add model to js
        $this->sed_post_shortcodes_model[$post->ID] = $shortcodes_models;

        //convert to tree model
        $tree_shortcodes = $sed_apps->app_save->build_tree_shortcode( $shortcodes_models , "root" );

        //convert to normal content with sed_do_shortcode
        $contents = $this->sed_do_shortcode( $tree_shortcodes );

        return  $contents;
    }

    function sed_do_shortcode( $tree_shortcodes ){

        $content = $this->sed_shortcode_content( $tree_shortcodes , array() );

        return $content;
    }

    function sed_shortcode_content( $tree_shortcodes , $tree_path  ){

        $content = "";
        if(!empty( $tree_shortcodes ) && is_array( $tree_shortcodes ) ){
            foreach( $tree_shortcodes AS $shortcode ){

                $attrs_string = "";
                if(!empty( $shortcode['attrs'] )){
                    foreach($shortcode['attrs'] AS $attr => $value){
                        $attrs_string .= $attr.'="'.self::sanitize_attr_value( $value ).'" ';
                    }
                }
                $shortcode_content = "";

                if($shortcode['tag'] != "content"){
                    $shortcode_content .= '['.$shortcode['tag'] . ' ' . $attrs_string .']';

                    if( isset($shortcode['children']) ){
                        $new_path = $tree_path;
                        array_push( $new_path , $shortcode['tag'] );

                        $shortcode_content .= $this->sed_shortcode_content( $shortcode['children'] , $new_path  );

                    }

                    $shortcode_content .= '[/'.$shortcode['tag'].']';

                    if( in_array( $shortcode['tag'] , $tree_path ) || ( isset( $shortcode['attrs'] ) && isset( $shortcode['attrs']['force_do_shortcode'] ) && $shortcode['attrs']['force_do_shortcode'] == "true"   ) ){
                        $shortcode_content = do_shortcode( $shortcode_content );
                        $shortcode_content = $this->filter_shortcode_code( $shortcode_content );
                    }

                }else{
                    $shortcode_content = $shortcode['content'];
                }

                $content .= $shortcode_content;

            }
        }

        return $content;
    }

    function filter_shortcode_code( $shortcode_content ){
        global $shortcode_tags;

        $tagnames = array_keys($shortcode_tags);
    	$tagregexp = join( '|', array_map('preg_quote', $tagnames) );
    	$pattern = "/\\[($tagregexp)/s";

    	if ( 1 === preg_match( $pattern, $shortcode_content ) ) {

            $pattern = get_shortcode_regex();
            $shortcode_content = preg_replace_callback( "/$pattern/s", array( $this , 'modify_shortcode_code' ) , $shortcode_content );

    	}

        return $shortcode_content;

    }

    function modify_shortcode_code( $m ){
        return "[" . $m[0] . "]";
    }

    function get_shortcodes_modules_length( $shortcodes , $asModule = "true" , $id ){

        if(!empty($shortcodes) && is_array($shortcodes) ){

            foreach($shortcodes AS $shortcode){
                $tag = $shortcode['tag'];
                if( $this->shortcodes[$tag]["asModule"] !== false  && $asModule == "true" ){
                    $name = $this->shortcodes[$tag]['moduleName'];
                    if( isset($this->modules_length[$id][$name]) ){
                        $this->modules_length[$id][$name]['length'] += 1;
                    }else{
                        $this->modules_length[$id][$name] = array( 'length' => 1 );
                    }
                }elseif( $this->shortcodes[$tag]["asModule"] === false  && $asModule == "false" ){
                    if( isset($this->shortcodes_length[$id][$tag]) ){
                        $this->shortcodes_length[$id][$tag]['length'] += 1;
                    }else{
                        $this->shortcodes_length[$id][$tag] = array( 'length' => 1 );
                    }
                }elseif($tag == "content" && $asModule == "false" ){
                    if( isset($this->shortcodes_length[$id][$tag]) ){
                        $this->shortcodes_length[$id][$tag]['length'] += 1;
                    }else{
                        $this->shortcodes_length[$id][$tag] = array( 'length' => 1 );
                    }
                }
            }
        }

    }

    function get_pb_posts_shortcode_content(){
        global $sed_data , $post;

        $sed_page_id = $sed_data['page_id'];
        $sed_page_type = $sed_data['page_type'];

        $sed_posts_content       = array();
        $sed_pages_theme_content = array();
                                     //|| (is_front_page() === true && is_home() === false)
        if( $sed_page_type == "post" ){
            $id = $sed_page_id;
            $content_shortcode = ( isset( $this->sed_post_shortcodes_model[$id] ) ) ? $this->sed_post_shortcodes_model[$id] : array() ;
            $content_shortcode = apply_filters( "sed_post_shortcode_content_output" , $content_shortcode );
            $sed_posts_content[$id] = $content_shortcode;
        }

        $theme_shortcode = $this->get_theme_shortcode_content();
        $theme_shortcode = apply_filters( "sed_theme_shortcode_content_output" , $theme_shortcode );

        $sed_pages_theme_content[$sed_page_id] = $theme_shortcode;
        //$home_patterns = $this->get_home_main_content_patterns();

        $output = "<script>";
        $output .= "var _sedAppPagesThemeContent = " .wp_json_encode( $sed_pages_theme_content).";";
        $output .= "var _sedAppPostsContent = " . wp_json_encode( $sed_posts_content ) . ";";
        $output .= "</script>";
        $output .= $this->sed_post_content_tpl;
        echo $output;

    }

    /*public function get_home_main_content_patterns(){
        $latest_posts_pattern = $this->get_main_content_shortcode_pattern( "default" , "archive" , "sed_archive" );
        $static_page_pattern = $this->get_main_content_shortcode_pattern( "default" , "posts" , "sed_post" );
        return array(

        );
    } */

    public function find_shortcode_model( $shortcodes_models , $id ){
        if(!empty( $shortcodes_models ) && is_array( $shortcodes_models )){
            foreach( $shortcodes_models AS $shortcode ){
                if( isset( $shortcode['id'] ) && $shortcode['id'] == $id ){
                    return $shortcode;
                }
            }
        }

        return false;
    }
           // loadPattern in js === this function
    public function build_shortcode_models( $tree_shortcodes , $parent_id = 'root' , $post_id , $old_shortcodes = array() ){

        $shortcodes = array();
        if(is_array($tree_shortcodes) && !empty($tree_shortcodes) ){
            foreach( $tree_shortcodes AS $shortcode ){
                //( isset( $shortcode['parent_id'] ) && !empty(  $shortcode['parent_id'] ) && isset( $shortcode['id'] ) && !empty(  $shortcode['id'] ) )

                $model_finded = null;
                if( isset($shortcode['attrs']) && isset($shortcode['attrs']['id']) && !is_null($old_shortcodes) && !empty( $old_shortcodes ) && is_array( $old_shortcodes ) ){
                    $model_finded = $this->find_shortcode_model( $old_shortcodes , $shortcode['attrs']['id'] );
                    //var_dump( $model_finded );
                }

                if( !is_null( $model_finded ) && $model_finded )
                    $new_shortcode = $model_finded;
                else{
                    if( $shortcode['name'] == "sed_module" && count( $shortcode['children'] ) > 0 ){
                        $shortcode_name = $shortcode['children'][0]['name'];
                    }else if( $shortcode['name'] == "sed_row" && count( $shortcode['children'] ) > 0 ){
                        $sed_module = $shortcode['children'][0];

                        if( count( $sed_module['children'] ) > 0 )
                            $shortcode_name = $sed_module['children'][0]['name'];
                        else
                            $shortcode_name = "";
                    }else{
                        $shortcode_name = "";
                    }
                    $new_shortcode = $this->add_new_shortcode_model( $shortcode , $parent_id , $post_id , $shortcode_name );
                }

                if( is_null( $new_shortcode ) )
                    continue;

                array_push( $shortcodes , $new_shortcode );

                if(is_array( $shortcode['children'] ) && count( $shortcode['children'] ) > 0){
                    $shortcodes_children = $this->build_shortcode_models( $shortcode['children'] , $new_shortcode['id'] , $post_id , $old_shortcodes);
                    foreach( $shortcodes_children AS $shortcode_model ){
                        array_push( $shortcodes , $shortcode_model );
                    }
                }
            }
        }

        return $shortcodes;
    }

    function add_new_shortcode_model( $shortcode , $parent_id , $post_id , $shortcode_name ){

        if( !isset( $shortcode['name'] ) )
            return false;

        $shortcode_info = ( isset( $this->shortcodes[$shortcode['name']] ) ) ? $this->shortcodes[$shortcode['name']] : null;

        if( $shortcode['name'] != "content" && !is_null( $shortcode_info ) ){

            if( !empty( $shortcode_info ) ){
                if($shortcode_info['asModule']){
                    $id = $this->get_new_id( $shortcode_info['moduleName'] , 'module' , $post_id );
                }else{
                    $id = $this->get_new_id( $shortcode['name'] , "shortcode" , $post_id );
                }
            }

        }else{
            $shortcode['name'] = "content";
            $id = $this->get_new_id( $shortcode['name'] , "shortcode" , $post_id );
        }
               // var_dump( $id );
        $new_shortcode = array(
          'parent_id' => $parent_id,
          'tag'       => $shortcode['name'],
          'id'        => $id,
        );

        if( isset($shortcode['attrs']) )
            $new_shortcode['attrs'] = $shortcode['attrs'];
        else
            $new_shortcode['attrs'] = array();

            $new_shortcode['attrs']['id'] = $id;

        $new_shortcode['attrs'] = self::set_module_contextmenu_class( $new_shortcode['attrs'] , $shortcode['name'] , $shortcode_name  );

        if( $shortcode['name'] == "content" ){
            $new_shortcode['content'] = $shortcode['content'];
        }

        return $new_shortcode;
    }

    public static function set_module_contextmenu_class( $attrs , $shortcode_name , $shortcode_contextmenu ){

        if( ( $shortcode_name != "sed_module" && $shortcode_name != "sed_row" ) || !$shortcode_contextmenu )
            return $attrs;

        if( isset( $attrs['class'] ) ){
            $attrs['class'] .= " module_" . $shortcode_contextmenu . "_contextmenu_container";
        }else{
            $attrs['class'] = "module_" . $shortcode_contextmenu . "_contextmenu_container";
        }
        return $attrs;
    }


    function get_new_id( $name , $type = "module" , $pid ){

        if($type == "module"){

            if( !isset( $this->modules_length[$pid]) ){
                $this->modules_length[$pid] = array();
            }

            if( !isset( $this->modules_length[$pid][$name] ) ){
                $this->modules_length[$pid][$name] = array(
                    'length' => 1
                );
            }else{
                $this->modules_length[$pid][$name]['length'] += 1;
            }

            $id = 'sed-bp-module-' . $name . "-" . $pid . "-" . $this->modules_length[$pid][$name]['length'];
        }else{

            if( !isset( $this->shortcodes_length[$pid]) ){
                $this->shortcodes_length[$pid] = array();
            }

            if( !isset( $this->shortcodes_length[$pid][$name] ) ){
                $this->shortcodes_length[$pid][$name] = array(
                    'length' => 1
                );
            }else{
                $this->shortcodes_length[$pid][$name]['length'] += 1;
            }

            $id = 'sed-bp-shortcode-' . $name . "-" . $pid . "-" . $this->shortcodes_length[$pid][$name]['length'];
        }

        return $id;
    }

/*======================================================
    new system for theme content load
    /*
     * sed_layouts_settings
     * sed_pages_layouts
     * sed_layouts_models ------
     * sed_layout_shortcodes_content -------------
     * sed_layouts_content ------------
     * sed_last_theme_id ------------

========================================================*/
    function add_base_pattern_helper_shortcodes( $helper_shortcodes ){
        $helper_shortcodes['sed_row_outer_outer'] = 'sed_row';
        $helper_shortcodes['sed_module_outer_outer'] = 'sed_module';
        $helper_shortcodes['sed_columns_outer'] = 'sed_columns';
        $helper_shortcodes['sed_column_outer'] = 'sed_column';
        $helper_shortcodes['sed_row_outer'] = 'sed_row';
        $helper_shortcodes['sed_module_outer'] = 'sed_module';
        return $helper_shortcodes;
    }

    function check_exist_main_content_model(){

        global $site_editor_app;
        $page_layout = $site_editor_app->layout->get_page_layout();
        $sed_layout_models = get_option( 'sed_layouts_models' );

        $has_main_row = false;
        $sed_last_theme_id = get_option( 'sed_last_theme_id' );

        if( $sed_layout_models !== false && is_array( $sed_layout_models ) && isset( $sed_layout_models[$page_layout] ) ){

            foreach( $sed_layout_models[$page_layout] AS $key => $model ){
                if( isset($model['main_row']) ){
                    $has_main_row = true;
                }
            }

        }

        if( $has_main_row === false ){
            if( $sed_last_theme_id !== false ){
                $sed_last_theme_id++;
                update_option( 'sed_last_theme_id' , $sed_last_theme_id );
            }else{
                $sed_last_theme_id = 1;
                $deprecated = null;
                $autoload = 'yes';
                add_option( 'sed_last_theme_id' , array() , $deprecated, $autoload );
            }

            $theme_id = "theme_id_" . $sed_last_theme_id;

            $new_model = array(
                'order'         =>    0 ,
                'theme_id'      =>    $theme_id ,
                //'after_content' =>    true ,
                'main_row'      =>    true ,
                'exclude'       =>    array() ,
                'title'         =>    __("Content" , "site-editor")
            );

            $sed_newlayout_models = $sed_layout_models;

            if( !is_array( $sed_newlayout_models ) ){
                $sed_newlayout_models = array();
            }

            if( !isset( $sed_newlayout_models[$page_layout] ) ){
                $sed_newlayout_models[$page_layout] = array();
            }

            array_push( $sed_newlayout_models[$page_layout] , $new_model );

            if( $sed_layout_models === false ){
                $deprecated = null;
                $autoload = 'yes';
                add_option( 'sed_layouts_models' , $sed_newlayout_models , $deprecated, $autoload );
            }else{
                update_option( 'sed_layouts_models' , $sed_newlayout_models );
            }

            $default_layout = '[sed_row_outer_outer class="module_sed_content_layout_contextmenu_container" sed_theme_id="' . $theme_id . '" sed_main_content_row="true" shortcode_tag="sed_row" type="static-element" length="boxed"]
                    [sed_module_outer_outer class="module_sed_content_layout_contextmenu_container" shortcode_tag="sed_module"]
                        [sed_content_layout layout="without-sidebar" title="columns"]
                            [sed_content_layout_column width="100%" sed_main_content="yes" parent_module="content-layout"]
                                {{content}}
                            [/sed_content_layout_column]
                        [/sed_content_layout]
                    [/sed_module_outer_outer]
                [/sed_row_outer_outer]';

            global $sed_main_row_shortcodes_string;

            $shortcodes_models = self::get_pattern_shortcodes( $default_layout );
            $sed_main_row_shortcodes_string = $shortcodes_models["string"];
            $main_row_models = $shortcodes_models["shortcodes"];

            $sed_layout_shortcodes_content = get_option( 'sed_layout_shortcodes_content' );

            $new_sed_layout_shortcodes_content = $sed_layout_shortcodes_content;

            if( !is_array( $new_sed_layout_shortcodes_content ) ){
                $new_sed_layout_shortcodes_content = array();
            }

            $new_sed_layout_shortcodes_content[$theme_id] = $sed_main_row_shortcodes_string;

            if( $sed_layout_shortcodes_content === false ){
                $deprecated = null;
                $autoload = 'yes';
                add_option( 'sed_layout_shortcodes_content' , $new_sed_layout_shortcodes_content , $deprecated, $autoload );
            }else{
                update_option( 'sed_layout_shortcodes_content' , $new_sed_layout_shortcodes_content );
            }

            $sed_layouts_content = get_option( 'sed_layouts_content' );

            $new_sed_layouts_content = $sed_layouts_content;

            if( !is_array( $new_sed_layouts_content ) ){
                $new_sed_layouts_content = array();
            }

            $this->sed_theme_content = $main_row_models;

            $new_sed_layouts_content[$theme_id] = $main_row_models;

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

    function sed_process_template(){
        $main_content = $this->main_content_template();

        if( !$this->check_exist_main_content_model() ){
            global $sed_main_row_shortcodes_string;
            $content = do_shortcode( $sed_main_row_shortcodes_string );
        }else{
            global $sed_data;
            $sub_themes_models = get_option("sed_layouts_models");

            global $site_editor_app;
            $page_layout = $site_editor_app->layout->get_page_layout();

            //if( isset( $sub_themes_models[ $sub_theme ] ) && is_array( $sub_themes_models[ $sub_theme ] ) && !empty( $sub_themes_models[ $sub_theme ] ) ){
            $curr_sub_themes_models = $sub_themes_models[ $page_layout ];

            $sed_layout_shortcodes_content = get_option("sed_layout_shortcodes_content");

            $sed_layouts_content = self::get_sub_themes_content_models();

            /*if( empty( $curr_sub_themes_models ) )
                return array();*/

            $theme_rows = array();
            $orders = array();

            $i = 1;
            foreach( $curr_sub_themes_models AS $key => $model ){
                if( !in_array( $sed_data['page_id'] , $model['exclude'] ) ){
                    $curr_sub_themes_models[$key]['instance_number'] = $i;
                    $i++;
                }else
                    unset( $curr_sub_themes_models[$key] );
            }

            uasort( $curr_sub_themes_models , array( 'PageBuilderApplication', 'theme_row_order' ) );

            $shortcodes_pattern_string = "";
            foreach( $curr_sub_themes_models AS $model ){
                /*do_action( "sed_before_layout_row_" . $model['theme_id'] , $model );
                $this->sed_theme_content = array_merge( $this->sed_theme_content , $sed_layouts_content[ $model['theme_id'] ] );*/

                $shortcodes_pattern_string = apply_filters( "sed_before_layout_row_" . $model['theme_id'] , $shortcodes_pattern_string );
                $shortcodes_pattern_string .= $sed_layout_shortcodes_content[ $model['theme_id'] ];
            }

            do_action( "sed_after_layout_" . $page_layout , $model );

            do_action( "sed_after_layout" , $model );

            $shortcodes_models = self::get_pattern_shortcodes( $shortcodes_pattern_string );
            $content = do_shortcode( $shortcodes_models["string"] );
            $this->sed_theme_content = $shortcodes_models["shortcodes"];

        }
           /*var_dump( get_option( 'sed_layouts_models' ) );
           var_dump( get_option( 'sed_last_theme_id' ) );
           var_dump( get_option( 'sed_pages_layouts' ) );
           var_dump( get_option( 'sed_layouts_settings' ) );*/
        $content = str_replace( "{{content}}" , $main_content , $content );
        echo $content;

    }

    function main_content_template() {

      //if ( pl_is_static_template() ) {

        global $sed_static_template_output;

        return sprintf( '<div class="sed-page-content">%s</div>', $sed_static_template_output );

      //}

    }


}

