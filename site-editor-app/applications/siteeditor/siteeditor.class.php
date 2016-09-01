<?php

Class SiteEditorApplication extends Applications {

     //var $registry;

     var $tbl_diagram;

     var $types = array();

     var $current_type;

     var $default_type;

     var $prefix;

     var $wp_theme;

     public $pagebuilder;

     public $style_editor_settings;

     public $template;

     public $layout_patterns;

     public $widget;
    /**
    * @var    icon base url for toolbar element
    * @since  1.0.0
    */
    //public $icon_url;

    /**
    * @var    array contain all tabs and elements
    * @since  1.0.0
    */
    //public $panels = array();

    /**
    * Class constructor.
    *
    * @param   $args
    *
    *
    * @desc    zmind_parse_args do not orginal zmind or php fuction
    *
    *
    * @since   1.0.0
    */
    //Set parent defaults
    function __construct(  )   //$registry
    {
        global $wpdb;
        $this->db = $wpdb;

        parent::__construct( array(
            'app_name' => 'siteeditor'
        ) , array("header","toolbar","panel","contextmenu" , "settings"));
        //$this->registry = $registry;
        $this->tbl_diagram = $wpdb->prefix .'site_editor';
        $this->prefix = "siteeditor_";

        $this->add_type("general" , __("General","site-editor"));
        $this->default_type = "general";

        $this->widget = new SEDWidget();

        $this->wp_theme = wp_get_theme( isset( $_REQUEST['theme'] ) ? $_REQUEST['theme'] : null );

        add_action('sed_footer', array($this,'print_sed_loading') );
        add_action('sed_footer', array($this,'tpl_sed_loading') );
		add_action('wp_footer', array($this,'tpl_sed_loading_iframe') );
    }

    function print_sed_loading(){

        echo '<div id="sed_full_editor_loading"><div class="sed-loading-continer" ><div class="sed-loading" ></div></div></div>';

    }
    function tpl_sed_loading(){
     ?>
       <script id="tmpl-sed-ajax-loading" type="text/html">
       <# type = (data.type) ? data.type + "-" : ""; #>
       <div class="sed-loading-{{type}}continer" ><div class="sed-loading" ></div></div>
       </script>
     <?php

    }

    function tpl_sed_loading_iframe(){
     ?>
       <script id="sed-ajax-loading-tpl" type="text/html">
       <# type = ( type ) ? type + "-" : ""; #>
       <div class="sed-loading-{{type}}continer" ><div class="sed-loading" ></div></div>
       </script>
     <?php

    }



    function add_type( $type_key , $type_value ){
        $this->types[$type_key] = $type_value;
    }

    function library_diagram_load(){

    }

    function update_site_editor($data, $where , $format = null, $where_format = null){

        $this->db->update(
        	$this->tbl_diagram ,
        	$data ,
        	$where,
        	$format,
        	$where_format
        );

    }

	/**
	 * Retrieve the template name of the previewed theme.
	 *
	 * @since 3.4.0
	 *
	 * @return string Template name.
	 */
	public function get_template() {
		return $this->wp_theme()->get_template();
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
	 * Filter the current theme and return the name of the previewed theme.
	 *
	 * @since 3.4.0
	 *
	 * @param $current_theme {@internal Parameter is not used}
	 * @return string Theme name.
	 */
	public function current_theme( $current_theme ) {
		return $this->wp_theme()->display('Name');
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



    function mm_base_url(){
       return SED_BASE_URL."applications/siteeditor/";
    }

    function js_I18n(){
        $I18n = array();
        return apply_filters( "sed_js_I18n" , $I18n);
    }

    function addon_settings(){
        $sed_addon_settings = array();
        return apply_filters( "sed_addon_settings" , $sed_addon_settings);
    }

}

Class SEDWidget {

    public $scripts = array();

    public $styles = array();

    function __construct(  )   //$registry
    {

    }

    function add_script( $widget , $handle, $src, $deps = array(), $ver = false, $in_footer = null ){

        if( !isset( $this->scripts[$widget] ) )
            $this->scripts[$widget] = array();

        $this->scripts[$widget][$handle] = array(
                                     "handle" => $handle,
                                     "src"    => $src,
                                     "deps"   => $deps,
                                     "ver"    => $ver,
                                     "in_footer"   => $in_footer
                                     );

    }


}

?>
