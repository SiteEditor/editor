<?php
class AjaxPBApplication{

    public $shortcodes = array();
    public $shortcodes_tagnames = array();

    function __construct(  ) {

    }

    function register_shortcode( $options ){
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
        ), $options);


        extract( $shortcode_options_arr );

        if(empty($name) ){  //|| empty($title)
            return false;
        }else{
            $this->shortcodes[$name] = $shortcode_options_arr;

            if( !in_array( $name , $this->shortcodes_tagnames ) )
            array_push( $this->shortcodes_tagnames , $name );

            $module_name = (!empty($moduleName)) ? $moduleName : $parentModule;


            $tmpl = PageBuilderApplication::shortcode_tmpl_pattern( $module_name );
            if(empty($tmpl))
            $tmpl = $this->shortcode_tmpl($this->shortcodes[$name] , $module_name );

            $this->shortcodes[$name]['pattern'] = $tmpl;
        }
    }

    function get_shortcode_attrs( $shortcode_attrs = array()){
        $attrs = array();
        if(!empty($shortcode_attrs) && is_array($shortcode_attrs)){
            foreach($shortcode_attrs AS $attr => $value){
                if(!is_array($fsparam["value"])){
                    $attrs[] = $attr . '="' . PageBuilderApplication::sanitize_attr_value( $value ) .'"';
                }else{
                    $attrs[] = $attr . '="' . implode("," , $value).'"';
                }
            }
        }

        return $attrs;
    }

    public function shortcode_tmpl( $shortcode_option , $module_name ){

        $shortcode = $shortcode_option['name'];
        $shortcode_type = $shortcode_option['shortcode_type'];
        $params = $shortcode_option['params'];
        $pattern_type = $shortcode_option['pattern_type'];
        $pattern = $shortcode_option['pattern'];

        $tmpl = "[". $shortcode ." ";
        $attr_value = $this->get_shortcode_attrs($params , $shortcode_option['attrs']);
        $content_shortcode = "";
        $content = isset( $params['content']["value"] ) ? $params['content']["value"]: "";

        if(!empty($attr_value)){
            $str = implode(" ", $attr_value);
            $tmpl .= $str;
        }

        $tmpl .= "]";

        if($shortcode_type == "enclosing"){
            $tmpl .= $content_shortcode;
            $tmpl .= "[/". $shortcode."]";
        }

        return $tmpl;

    }

    function get_default_patterns(){
        global $sed_ajax , $sed_error;
        $sed_ajax->check_ajax_handler('sed_ajax_default_patterns' , 'sed_app_default_patterns');

        $settings   = array();
        $scripts    = array();
        $styles     = array();
        $shortcodes = $this->shortcodes;
        $num = 0;

        foreach( $shortcodes AS $name => $shortcode){
            if($num > 10 )
                break;
            if($shortcode['asModule'])
                $parent_module = $shortcode['moduleName'];
            elseif( !empty( $shortcode['attrs']['parent_module'] ) )
                $parent_module = $shortcode['attrs']['parent_module'];
            else
                $parent_module = "";

            $scripts[$name] = $shortcode['scripts'];
            $styles[$name] = $shortcode['styles'];

            $settings[$name] = PageBuilderApplication::get_pattern_shortcodes( $shortcode['pattern'] , $parent_module , $name , $this->shortcodes_tagnames );
            $num++;
        }

        wp_send_json_success( array(
            'output'    => array(
                "shortcodes"    => $shortcodes  ,
                "settings"      => $settings  ,
                "scripts"       => $scripts  ,
                "styles"        => $styles  ,
            )
        ) );

        /*var _sedRegisteredShortcodesSettings = <?php echo json_encode(  ); ?>;
        //var _sedShortcodesDefaultPatterns = <?php echo json_encode(  ); ?>;
        //var _sedRegisteredShortcodesScripts = <?php echo json_encode(  ); ?>;
        var _sedRegisteredShortcodesStyles = <?php echo json_encode(  ); ?>;*/

    }

}

class SEDAppAjax{

    public $wp_theme;

    function __construct(  ) {

        $this->wp_theme = wp_get_theme( isset( $_REQUEST['theme'] ) ? $_REQUEST['theme'] : null );

    }

    function router(){
        if( isset( $_POST['action'] ) ){
            $action = $_POST['action'];
            if( is_callable( array($this , $action ) ) ){
                $this->$action();
                return false;
            }
        }
    }

    function compile_less(){
        if( !class_exists('SEDAjaxLess') )
            require_once SED_PLUGIN_DIR . DS . 'framework' . DS . 'SEDAjaxLess' . DS  . 'SEDAjaxLess.php';

        add_action("site_editor_ajax_compile_less" , array( 'SEDAjaxLess' , 'driver' ) );

    }

    function shortcodes_default_pattern(){

        include_once( SED_EDITOR_DIR . DS . 'application' . DS . "modules.class.php"  );
        $pb_modules = new SEDPageBuilderModules(  );
        $pb_modules->app_modules_dir = SED_EDITOR_DIR . DS . 'applications' . DS . 'pagebuilder' . DS . 'modules';
        require_once SED_EDITOR_DIR . DS . 'applications' . DS . 'pagebuilder' . DS . 'index.php';


        $ajax_pb = new AjaxPBApplication();
        do_action( 'sed_ajax_pb', $ajax_pb );

        add_action("site_editor_ajax_shortcodes_default_pattern" , array( $ajax_pb , 'get_default_patterns' ) );

    }

    function check_ajax_handler($ajax , $nonce){
        if ( is_admin() && ! $this->doing_ajax() )
            auth_redirect();
        elseif ( $this->doing_ajax() && ! is_user_logged_in() ){
            $this->sed_die( 0 );
        }

        if ( ! current_user_can( 'edit_theme_options' ) )
            $this->sed_die( -1 );

        if( !check_ajax_referer( $nonce . '_' . $this->get_stylesheet(), 'nonce' , false ) ){
            $this->sed_die( -2 );
        }
        if( !isset($_POST['sed_page_ajax']) || $_POST['sed_page_ajax'] !=  $ajax){
            $this->sed_die( -2 );
        }
    }

	/**
	 * Return true if it's an AJAX request.
	 *
	 * @since 3.4.0
	 *
	 * @return bool
	 */
	public function doing_ajax() {
		return isset( $_POST['sed_page_customized'] ) || ( defined( 'DOING_SITE_EDITOR_AJAX' ) && DOING_SITE_EDITOR_AJAX );
	}

	/**
	 * Custom wp_die wrapper. Returns either the standard message for UI
	 * or the AJAX message.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $ajax_message AJAX return
	 * @param mixed $message UI message
	 */
	public function sed_die( $message = null ) {
        if ( is_scalar( $message ) )
            die( (string) $message );
        die( '0' );
	}

	/**
	 * Retrieve the stylesheet name of the previewed theme.
	 *
	 * @since 3.4.0
	 *
	 * @return string Stylesheet name.
	 */
	public function get_stylesheet() {
		return $this->wp_theme()->get_stylesheet();
	}

	/**
	 * Checks if the current theme is active.
	 *
	 * @since 3.4.0
	 *
	 * @return bool
	 */
	/*public function is_theme_active() {
		return $this->get_stylesheet() == $this->original_stylesheet;
	} */

    function wp_theme(){
        return $this->wp_theme;
    }

}

$sed_ajax = new SEDAppAjax();
$GLOBALS['sed_ajax'] = $sed_ajax ;
$sed_ajax->router();

