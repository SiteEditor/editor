<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


Class PageBuilderApplication {

    /**
    * @var    icon base url for toolbar element
    * @since  1.0.0
    */

    public $template;

    public $shortcodes = array();

    static $shortcode_tag_counter = 1;

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

    public static $shortcodes_tagnames = array();

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

        $this->template = 'default';
        $this->current_app = 'siteeditor';

        //remove extra p && br tag from site editor & add to default wp editor only
        //remove_filter( 'the_content', 'wpautop' );

        add_filter('the_excerpt', array($this, 'sed_excerpt_filter') );

        //load helper shortcodes & ready for do shortcode
        add_action( "wp_loaded" , array( $this , "register_helper_shortcodes" ) , 99999   ); //after_sed_pb_modules_loaded

        if( site_editor_app_on() ){
            add_action( 'the_post'      , array( $this  , 'preview_setup_post_content' ) );
            add_action( 'the_post'      , array( &$this , 'parse_editable_content' ), 99999 );
            add_action( 'wp_footer'     , array( $this  , "get_pb_posts_shortcode_content") );
            add_action( 'wp_footer'     , array( $this  , 'load_front_end_tmpl' ) );
        }

        if( !site_editor_app_on() ){
            add_filter('the_content', array($this, 'sed_post_ready'), 10);
        }

        add_action("sed_footer" , array( $this, 'registered_shortcodes_settings') , 10000 );
        add_action("sed_footer" , array( $this, "write_shortcode_settings") );
        //add_action("sed_footer" , array( $this, "write_style_editor_settings") );

        add_action( "site_editor_ajax_load_modules" , array($this, "page_builder_load_modules") );
        //add_filter( "sed_addon_settings" , array($this,'load_modules_settings') );

        add_filter( "sed_app_refresh_nonces" , array( $this, 'set_nonces' ) , 10 , 2 );

        /*======================================================
            new system for theme content load hooks
        ========================================================*/
        add_filter( "sed_helper_shortcodes" , array( $this , "add_base_pattern_helper_shortcodes" ) );

        add_action( 'sed_region_template', array( $this , 'sed_process_template' ) );

        add_action( "sed_editor_init" , array( $this, "add_toolbar_elements" ) );

        add_filter( "sed_start_page_customize_rows" , array( $this, "get_start_page_rows" ) , 10 , 1 );

        add_filter( "sed_before_layout_row"  , array( $this, "get_before_layout_rows" ) , 10 , 2 );

        add_filter( "sed_after_layout_row"  , array( $this, "get_after_layout_rows" ) , 10 , 2 );

        add_filter( "sed_end_page_customize_rows"  , array( $this, "get_end_page_rows" ) , 10 , 1 );

        add_filter( 'sed_pb_builder_module_content', 'wptexturize');

        add_filter( 'sed_pb_builder_module_content', 'convert_smilies');

        add_filter( 'sed_pb_builder_module_content', 'convert_chars');

        add_filter( 'sed_pb_builder_module_content', array($this, 'the_module_content'));

        /**
         * WordPress 4.4 Responsive Images support */
        global $wp_version;
        if (version_compare($wp_version, '4.4', '>=')) {
            add_filter('sed_pb_builder_module_content', 'wp_make_content_images_responsive');
        }

    }

    /**
     * Add filter to module content
     * @param string $content
     * @return string
     */
    function the_module_content($content) {
        global $wp_embed;
        $content = $wp_embed->run_shortcode($content);
        $content = do_shortcode(shortcode_unautop($content));
        $content = $this->autoembed_adjustments($content);
        $content = $wp_embed->autoembed($content);
        $content = htmlspecialchars_decode($content);
        return $content;
    }

    /**
     * Adjust autoembed filter
     * @param string $content
     * @return string
     */
    function autoembed_adjustments($content) {
        $pattern = '|<p>\s*(https?://[^\s"]+)\s*</p>|im'; // pattern to check embed url
        $to = '<p>' . PHP_EOL . '$1' . PHP_EOL . '</p>'; // add line break
        $content = preg_replace($pattern, $to, $content);
        return $content;
    }


    function preview_setup_post_content( $post ){

        if( !isset( $_POST['sed_posts_content']  ) ){
            return ;
        }

        $all_posts_content_models = json_decode( wp_unslash( $_POST['sed_posts_content'] ), true );

        static $prevent_setup_post_content_recursion = false;
        if ( $prevent_setup_post_content_recursion || !isset( $all_posts_content_models[$post->ID] ) ) {
            return;
        }

        $shortcodes = $all_posts_content_models[$post->ID];

        if(!empty($shortcodes)){
            $tree_shortcodes = SED()->editor->save->build_tree_shortcode( $shortcodes , "root" );
            $content = SED()->editor->save->create_shortcode_content( $tree_shortcodes , array() , $post->ID );
        }else{
            $content = "";
        }

        $post->post_content = $content;

        $prevent_setup_post_content_recursion = true;
        setup_postdata( $post );
        $prevent_setup_post_content_recursion = false;

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
    public function register_helper_shortcodes(  ){
        global $shortcode_tags;

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

            self::$shortcodes_tagnames = array_merge( self::$shortcodes_tagnames , array_keys( $sed_helper_shortcodes ) );

            self::$shortcodes_tagnames = array_unique( self::$shortcodes_tagnames );

            foreach( $sed_helper_shortcodes AS $shortcode => $main_shortcode_name ){
                if( shortcode_exists( $main_shortcode_name ) ){ //var_dump( $shortcode_tags[$main_shortcode_name] );
                    add_shortcode( $shortcode , $shortcode_tags[$main_shortcode_name] );
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

        $post_id = (int) $sed_apps->framework->sed_page_id;

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
        $output = '<div id="sed-post-content-container" data-post-id="'.$id.'" data-content-type="post" drop-placeholder="'.__("Drop Each Module Into The Content Area" , "site-editor").'" data-parent-id="root" class="sed-pb-post-container sed-pb-rows-box sed-pb-component">';
        $output .= '</div>';

		return $output;
	}

    function sed_post_ready($content){
        global $post , $sed_data;

        if( !$post )
            return $content;

        if( is_singular() && $sed_data['page_id'] == $post->ID  ){
            $id = $post->ID;
            $output = '<div id="sed-pb-post-container'.$id.'" data-post-id="'.$id.'" data-content-type="post" drop-placeholder="'.__("Drop Each Module Into The Content Area" , "site-editor").'" data-parent-id="root" class="sed-pb-post-container sed-pb-rows-box sed-pb-component">';
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
                    //'attrs_query'   => '' ,
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
        				//'attrs_query'   => $matches[3][ $index ],
        				'attrs'         => shortcode_parse_atts( $matches[3][ $index ] ),
        				'id'            => $id,
        				'parent_id'     => $parent_id,
        			);

                    $attrs_query = $matches[3][ $index ];

                    //For fix Checkboxes or options with false && true 
                    if( is_array( $shortcode['attrs'] ) && !empty( $shortcode['attrs'] ) ) {

                        $shortcode['attrs'] = array_map(array(__CLASS__, "sanitize_control_value"), $shortcode['attrs']);
                    }

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

                            if( site_editor_app_on() ) {
                                $sed_apps->framework->dynamic_css_data = array_merge($sed_apps->framework->dynamic_css_data, $new_css_data);
                            }

                            $shortcode['attrs']['sed_css'] = $new_css_data;
                        }

                    }

                    if( isset( $shortcode['attrs']['sed_theme_id'] ) ){
                        $shortcode['theme_id'] = $shortcode['attrs']['sed_theme_id'];
                        unset( $shortcode['attrs']['sed_theme_id'] );
                    }

                    if( isset( $shortcode['attrs']['sed_row_type'] ) ){
                        $shortcode['row_type'] = $shortcode['attrs']['sed_row_type'];
                        unset( $shortcode['attrs']['sed_row_type'] );
                    }

                    if( isset( $shortcode['attrs']['sed_rel_theme_id'] ) ){
                        $shortcode['rel_theme_id'] = $shortcode['attrs']['sed_rel_theme_id'];
                        unset( $shortcode['attrs']['sed_rel_theme_id'] );
                    }

                    if( isset( $shortcode['attrs']['sed_is_customize'] ) ){
                        $shortcode['is_customize'] = $shortcode['attrs']['sed_is_customize'];
                        unset( $shortcode['attrs']['sed_is_customize'] );
                    }

                    if( isset( $shortcode['attrs']['sed_is_hidden'] ) ){
                        $shortcode['is_hidden'] = $shortcode['attrs']['sed_is_hidden'];
                        unset( $shortcode['attrs']['sed_is_hidden'] );
                    }

                    $shortcodes[] = $shortcode ;

                    $children = self::get_pattern_shortcodes( $matches[5][$index] , $id , $module , $module_shortcode , $tagnames );

                    if( !empty( $children['shortcodes'] ) ){
                        $shortcodes = array_merge($shortcodes , $children['shortcodes']);
                    }

        			$string .= '[' . $string_shortcode_tag . ' sed_model_id="' . $id . '" ' . $attrs_query . ']' . $children['string'] . '[/' . $string_shortcode_tag . ']' ;

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

        return '[sed_row type="static-element" class="module_sed_wp_text_editor_contextmenu_container" from_wp_editor="true"]
                    [sed_module class="module_sed_wp_text_editor_contextmenu_container" ]
                        [sed_wp_text_editor]
                            '.$ex_content.'
                        [/sed_wp_text_editor]
                    [/sed_module]    
                [/sed_row]';

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
            $tagnames = self::$shortcodes_tagnames;
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

        }
    }

    public function add_toolbar_elements(){
        global $site_editor_app;
        foreach( $this->modules AS $name => $options ){

            extract( $options );

            if($show_ui_in_toolbar === true){
                $element_html = $this->toolbar_module($name,$title , $icon, $type_icon );

                $site_editor_app->toolbar->add_element(
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
            "actions"           => array() ,
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

            //move to pb-shortcodes.class.php
            /*if( !in_array( $name , self::$shortcodes_tagnames ) )
                array_push( self::$shortcodes_tagnames , $name );*/

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

            }
       }

    }

    public function add_module_group($parent_tab , $name , $title , $include = "all" ){
        global $site_editor_app;
        $site_editor_app->toolbar->add_element_group($parent_tab , $name , $title , $include);
    }


    private function toolbar_module($name , $title , $icon="", $icon_type="font"){
        if($icon_type == "font")
            $icon_class = $icon;
        elseif($icon_type == "img")
            $icon_img = $icon;

        ob_start();

        if(file_exists(SED_TMPL_PATH . DS . $this->template . DS . "modules/modules/view/module_element.php" )){
            require SED_TMPL_PATH . DS . $this->template . DS . "modules/modules/view/module_element.php" ;
        }elseif(file_exists( SED_EXT_PATH . "/pagebuilder/view/module_element.php" )){
            require SED_EXT_PATH . "/pagebuilder/view/module_element.php" ;
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

    public static function sanitize_control_value( $value ){

        if( $value === "true" )
            $value = true;
        else if( $value === "false" )
            $value = false;

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

    public function registered_shortcodes_settings(){
        global $sed_pb_app;
        $pattern_settings   = array();
        $scripts    = array();
        $styles     = array();
        $shortcodes = $this->shortcodes;
        $shortcodes_settings = array();

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
            var _sedRegisteredShortcodesSettings = <?php if( !empty( $shortcodes_settings ) ) echo wp_json_encode( $shortcodes_settings ); else "{}"; ?>;
            var _sedShortcodesDefaultPatterns = <?php echo wp_json_encode( $pattern_settings ); ?>;
            var _sedRegisteredShortcodesScripts = <?php echo wp_json_encode( $scripts ); ?>;
            var _sedRegisteredShortcodesStyles = <?php echo wp_json_encode( $styles ); ?>;
            var _sedAppJsModulesForEditor = <?php echo wp_json_encode( $js_modules ); ?>;

            var _sedAppDefaultHelperShortcodes = <?php echo wp_json_encode( $helper_shortcodes ); ?>;
		</script>
		<?php

    }

    //writes the php config file for the default pattern
    function write_shortcode_settings(){

        $shortcode_settings_content = '<?php $shortcodes_tagnames = array();';

        foreach( self::$shortcodes_tagnames as $tag ){
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
                $pattern = apply_filters( "sed_default_shortcode_pattern" , $shortcode['pattern'] , $shortcode );

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

    }//pb_modules , load_patterns , load_modules_settings


    public function set_nonces( $nonces , $manager ){

        $nonces['module'] = array(
            'load'                  =>  wp_create_nonce( 'sed_app_modules_load_' . $manager->get_stylesheet() ) ,
            //'update'                =>  wp_create_nonce( 'sed_app_modules_update_' . $manager->get_stylesheet() ) ,
            //'load_patterns'         =>  wp_create_nonce( 'sed_app_default_patterns_' . $manager->get_stylesheet() )
        );

        return $nonces;
    }


    function sed_page_builder_post_ready($content){
        global $post , $sed_data;

        if( is_singular() && $sed_data['page_id'] == $post->ID  ){
            $id = $post->ID;
            $output = '<div id="sed-pb-post-container'.$id.'" data-post-id="'.$id.'" data-content-type="post" drop-placeholder="'.__("Drop Each Module Into The Content Area" , "site-editor").'" data-parent-id="root" class="sed-pb-post-container sed-pb-rows-box sed-pb-component">';
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


    function page_builder_load_modules(){
        global $sed_apps ;  //@args ::: sed_page_ajax , nonce
        $sed_apps->editor->manager->check_ajax_handler('sed_load_modules' , 'sed_app_modules_load');

        $parent_id = $_REQUEST['parent_id'];

        $content_shortcodes = json_decode( wp_unslash( $_REQUEST['pattern'] ), true );

        $tree_shortcodes = $sed_apps->editor->save->build_tree_shortcode( $content_shortcodes , $parent_id );

        $content = $sed_apps->editor->save->create_shortcode_content( $tree_shortcodes , array() );

        $output = apply_filters( 'the_content' , $content );

        //$output = do_shortcode( $content );

        wp_send_json_success( $output );
    }


    function get_theme_shortcode_content(){
        return $this->sed_theme_content;
    }

    /*
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

        /*

        $module_base_url = content_url( "/" . dirname( dirname( $activate_modules[ $module ] ) ) );

        $default_pattern = str_replace("{{@sed_module_url}}", $module_base_url , $default_pattern );

         */
    //set helper shortcodes like setHelperShortcodes in pagebuilder.min.js
    function set_helper_shortcodes( $shortcodes_models )
    {

        foreach ($shortcodes_models AS $mkey => $model) {
            if (isset($model['attrs']) && isset($model['attrs']['have_helper_id'])) {
                foreach ($shortcodes_models AS $key => $in_model) {
                    if ($in_model['parent_id'] == $model['parent_id'] && $in_model['id'] != $model['id']) {
                        if (isset($in_model['attrs']) && isset($in_model['attrs']['is_helper_id'])) {
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

    public static function theme_row_order($a, $b) {
		if ( $a['order'] === $b['order'] ) {
			return $a['instance_number'] - $b['instance_number'];
		} else {
			return $a['order'] - $b['order'];
		}
    }

    /*
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
     */


    function get_pb_posts_shortcode_content(){
        global $sed_data , $post;

        $sed_page_id = $sed_data['page_id'];
        $sed_page_type = $sed_data['page_type'];

        $sed_posts_content       = array();
        $sed_pages_theme_content = array();

        if( $sed_page_type == "post" ){
            $id = $sed_page_id;
            $content_shortcode = ( isset( $this->sed_post_shortcodes_model[$id] ) ) ? $this->sed_post_shortcodes_model[$id] : array() ;
            $content_shortcode = apply_filters( "sed_post_shortcode_content_output" , $content_shortcode );
            $sed_posts_content[$id] = $content_shortcode;
        }

        $theme_shortcode = $this->get_theme_shortcode_content();
        $theme_shortcode = apply_filters( "sed_theme_shortcode_content_output" , $theme_shortcode );

        $sed_pages_theme_content[$sed_page_id] = $theme_shortcode;

        $output = "<script>";
        $output .= "var _sedAppPagesThemeContent = " .wp_json_encode( $sed_pages_theme_content).";";
        $output .= "var _sedAppPostsContent = " . wp_json_encode( $sed_posts_content ) . ";";
        $output .= "</script>";
        $output .= $this->sed_post_content_tpl;
        echo $output;

    }

    public function load_front_end_tmpl(){
        include SED_BASE_PB_APP_PATH . "/view/front-end-tmpl.php";
    }

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



/*======================================================
    new system for theme content load
    /*
     * sed_layouts_settings
     * sed_pages_layouts
     * sed_layouts_models ------
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

    function sed_process_template(){

        //get wp page content
        $main_content = $this->main_content_template();

        global $sed_data;

        //Page Layouts Models
        $sub_themes_models = get_option("sed_layouts_models");

        require_once SED_EXT_PATH . "/layout/includes/site-editor-layout.php";
        $page_layout = SiteEditorLayoutManager::get_page_layout();

        //Current Page Layout Models
        $curr_sub_themes_models = $sub_themes_models[ $page_layout ]; 

        //Current Page Layout Content
        $sed_layouts_content = get_option("sed_layouts_content");

        //Sort current page layout models by order attribute
        $i = 1;
        foreach( $curr_sub_themes_models AS $key => $model ){

            //remove row if marked as hidden in this page and site editor front end is not on
            if( !isset( $model['hidden'] ) || !in_array( $sed_data['page_id'] , $model['hidden'] ) || site_editor_app_on() ){
                $curr_sub_themes_models[$key]['instance_number'] = $i;
                $i++;
            }else
                unset( $curr_sub_themes_models[$key] );

        }

        uasort( $curr_sub_themes_models , array( __CLASS__ , 'theme_row_order' ) );

        self::fix_page_theme_content( $curr_sub_themes_models );

        //Create current page content
        $shortcodes_pattern_string = "";

        $shortcodes_pattern_string = apply_filters( "sed_start_page_customize_rows" , $shortcodes_pattern_string );

        foreach( $curr_sub_themes_models AS $model ){

            $shortcodes_pattern_string = apply_filters( "sed_before_layout_row"  , $shortcodes_pattern_string , $model['theme_id'] );

            if( !in_array( $sed_data['page_id'] , $model['exclude'] ) ) {
                $shortcodes_pattern_string .= $sed_layouts_content[$model['theme_id']];
            }else{
                $shortcodes_pattern_string .= $this->get_row_customized_content( $model['theme_id'] );
            }

            $shortcodes_pattern_string = apply_filters( "sed_after_layout_row"  , $shortcodes_pattern_string , $model['theme_id'] );

        }

        $shortcodes_pattern_string = apply_filters( "sed_end_page_customize_rows" , $shortcodes_pattern_string );

        do_action( "sed_after_layout_" . $page_layout , $model );

        do_action( "sed_after_layout" , $model );

        $shortcodes_models = self::get_pattern_shortcodes( $shortcodes_pattern_string );

        $content = do_shortcode( $shortcodes_models["string"] );//apply_filters( 'the_content' , $shortcodes_models["string"] );

        //set current page content shortcodes models in @$this->sed_theme_content for js
        $this->sed_theme_content = $shortcodes_models["shortcodes"];

        //replace wp page content in current page content
        $content = str_replace( "{{content}}" , $main_content , $content );

        echo $content;

    }

    function main_content_template() {

        global $sed_static_template_output;

        return sprintf( '<div class="sed-page-content">%s</div>', $sed_static_template_output );

    }

    /**
     * theme_content MODEL :
     * array(
     *      0  =>  array(
     *          "content"       => [sed_button] New Button [/sed_button] ,
     *          "rel_theme_id"  => "theme_id_7" ,
     *          "row_type"          => "after"
     *      ),
     *
     *      1  =>  array(
     *          "content"       => [sed_image] [/sed_image] ,
     *          "rel_theme_id"  => "theme_id_2" ,
     *          "row_type"          => "before"
     *      ),
     *
     *      2  =>  array(
     *          "content"       => [sed_text_title] Title [/sed_text_title] ,
     *          "rel_theme_id"  => "" ,
     *          "row_type"          => "start"
     *      ),
     *
     *      3  =>  array(
     *          "content"       => [sed_paragraph] New Button [/sed_paragraph] ,
     *          "rel_theme_id"  => "" ,
     *          "row_type"          => "end"
     *      ) ,
     *
     *      3  =>  array(
     *          "content"       => [sed_paragraph] New Button [/sed_paragraph] ,
     *          "theme_id"      => "theme_id_7" ,
     *          "is_customize"  => true
     *      )
     *
     * );
     * @param $shortcodes_pattern_string
     * @return mixed
     */
    function get_row_customized_content( $theme_id ){
        global $sed_data;

        $content = '';

        if( isset( $sed_data['theme_content'] ) && is_array( $sed_data['theme_content'] ) && !empty( $sed_data['theme_content'] ) ){

            foreach( $sed_data['theme_content'] AS $index => $row ){
                if( isset( $row['is_customize'] ) && isset( $row['theme_id'] ) && $row['theme_id'] == $theme_id ){
                    $content = $row['content'];
                    break;
                }
            }

        }

        return $content;
    }

    function get_start_page_rows( $shortcodes_pattern_string ){
        global $sed_data;

        if( isset( $sed_data['theme_content'] ) && is_array( $sed_data['theme_content'] ) && !empty( $sed_data['theme_content'] ) ){

            foreach( $sed_data['theme_content'] AS $index => $row ){
                if( isset( $row['row_type'] ) && $row['row_type'] == "start" ){
                    $shortcodes_pattern_string .= $row['content'];
                }
            }

        }

        return $shortcodes_pattern_string;
    }

    function get_before_layout_rows( $shortcodes_pattern_string , $theme_id ){
        global $sed_data;

        if( isset( $sed_data['theme_content'] ) && is_array( $sed_data['theme_content'] ) && !empty( $sed_data['theme_content'] ) ){

            foreach( $sed_data['theme_content'] AS $index => $row ){
                if( isset( $row['row_type'] ) && $row['row_type'] == "before" && isset( $row['rel_theme_id'] )  && $row['rel_theme_id'] == $theme_id ){
                    $shortcodes_pattern_string .= $row['content'];
                }
            }

        }

        return $shortcodes_pattern_string;
    }

    function get_after_layout_rows( $shortcodes_pattern_string , $theme_id ){
        global $sed_data;

        if( isset( $sed_data['theme_content'] ) && is_array( $sed_data['theme_content'] ) && !empty( $sed_data['theme_content'] ) ){

            foreach( $sed_data['theme_content'] AS $index => $row ){
                if( isset( $row['row_type'] ) && $row['row_type'] == "after" && isset( $row['rel_theme_id'] )  && $row['rel_theme_id'] == $theme_id ){
                    $shortcodes_pattern_string .= $row['content'];
                }
            }

        }

        return $shortcodes_pattern_string;
    }

    function get_end_page_rows( $shortcodes_pattern_string ){
        global $sed_data;

        if( isset( $sed_data['theme_content'] ) && is_array( $sed_data['theme_content'] ) && !empty( $sed_data['theme_content'] ) ){

            foreach( $sed_data['theme_content'] AS $index => $row ){
                if( isset( $row['row_type'] ) && $row['row_type'] == "end" ){
                    $shortcodes_pattern_string .= $row['content'];
                }
            }

        }

        return $shortcodes_pattern_string;
    }

    /**
     * @param $curr_sub_themes_models
     *
     * @Fix Current Page Theme Content ( Private Rows )
     * if remove a public row from layout or page layout changed
     * all related private rows have wrong rel_theme_id & row_type
     * @this Func will fixed rel_theme_id & row_type for this private rows
     * too if removed a public row & we customized in this page , we removed
     * this customized row from this page , Although this customized row can not appear
     * in this page Even if not removed from this page ( for optimize & clean database from extra data that not needed ).
     * too if changed page layout we done like work top
     */
    public static function fix_page_theme_content( $curr_sub_themes_models ){
        global $sed_data;

        $theme_ids = wp_list_pluck( $curr_sub_themes_models , 'theme_id' );

        if( isset( $sed_data['theme_content'] ) && is_array( $sed_data['theme_content'] ) && !empty( $sed_data['theme_content'] ) ){

            $changed = false;

            foreach( $sed_data['theme_content'] AS $index => $row ){

                if( isset( $row['rel_theme_id'] ) && !empty( $row['rel_theme_id']  ) && ! in_array( $row['rel_theme_id']  , $theme_ids ) ){

                    $info = self::find_valid_public_row( $theme_ids , $row['rel_theme_id'] , $row['row_type'] );

                    $sed_data['theme_content'][$index]['row_type'] = $info['row_type'];

                    $sed_data['theme_content'][$index]['rel_theme_id'] = $info['rel_theme_id'];

                    $changed = true;

                }else if( isset( $row['theme_id'] ) && isset( $row['is_customize'] ) && $row['is_customize'] && ! in_array( $row['theme_id']  , $theme_ids ) ){

                    unset( $sed_data['theme_content'][$index] );

                    $changed = true;

                }

            }

            if( $changed === true ){
                //update_option(  )
            }

        }
    }

    /**
     * @When Public To Private
     * @When removed a public row
     * @When unchecked a public row for specify layout
     *
     * @model : removed Model
     *   array(
     *
     *       "default"   =>  array(
     *           0  =>  array(
     *               "theme_id"         =>  "theme_id_3" ,
     *               "after"            =>  array(
     *                    "rel_theme_id"    =>      "theme_id_4" ,
     *                    "row_type"        =>      "before"
     *                ) ,
     *               "before"           =>  array(
     *                    "rel_theme_id"    =>      "theme_id_1"
     *                    "row_type"        =>      "after"
     *               ) ,
     *           ),
     *
     *           1  =>  array(
     *               "theme_id"         =>  "theme_id_3" ,
     *               "after"            =>  array(
     *                   "rel_theme_id"    =>      "theme_id_4" ,
     *                   "row_type"        =>      "before"
     *               ) ,
     *               "before"           =>  array(
     *                   "rel_theme_id"    =>      "theme_id_1"
     *                   "row_type"        =>      "after"
     *               ) ,
     *           )
     *       ),
     *
     *       "page"   =>  array(
     *           ...
     *       )
     *
     *   );
     */
    public static function find_valid_public_row( $theme_ids , $theme_id , $row_type ){

        $sed_layouts_removed_rows = get_option( "sed_layouts_removed_rows" );

        $page_layout = SiteEditorLayoutManager::get_page_layout();

        //for prevent error if not exist $theme_id in removed rows models
        $rel_theme_id   = '';
        $new_row_type   = 'end';

        if( isset( $sed_layouts_removed_rows[ $page_layout ] ) && is_array( $sed_layouts_removed_rows[ $page_layout ] ) ) {
            foreach ( $sed_layouts_removed_rows[ $page_layout ] AS  $index => $row ){
                if( $row['theme_id'] == $theme_id ){

                    if( $row_type == "after" ){

                        $rel_theme_id   = $row['after']['rel_theme_id'];

                        $new_row_type   = $row['after']['row_type'];

                        if( !empty( $rel_theme_id  ) && ! in_array( $rel_theme_id  , $theme_ids ) ){
                            return self::find_valid_public_row( $theme_ids , $rel_theme_id , $new_row_type );
                        }

                    }else if( $row_type == "before" ){

                        $rel_theme_id   = $row['before']['rel_theme_id'];

                        $new_row_type   = $row['before']['row_type'];

                        if( !empty( $rel_theme_id  ) && ! in_array( $rel_theme_id  , $theme_ids ) ){
                            return self::find_valid_public_row( $theme_ids , $rel_theme_id , $new_row_type );
                        }

                    }

                    break;
                }
            }
        }

        return array(
            "rel_theme_id"    => $rel_theme_id  ,
            "row_type"        => $new_row_type
        );
    }

}

