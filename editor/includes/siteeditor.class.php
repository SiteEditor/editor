<?php

Class SiteEditorApplication extends SiteEditorModules {

     var $types = array();

     var $current_type;

     var $default_type;

     var $wp_theme;

     public $pagebuilder;

     public $style_editor_settings;

     public $template;

     public $layout_patterns;

    function __construct(  ) {

        $this->app_name = 'siteeditor';

        $this->app_modules_dir = SED_EXT_PATH;

        $this->components = array( "header" , "toolbar" , "panel" , "settings" );

        $this->add_type("general" , __("General","site-editor"));

        $this->default_type = "general";

        $this->wp_theme = wp_get_theme( isset( $_REQUEST['theme'] ) ? $_REQUEST['theme'] : null );

        add_action('sed_footer', array($this,'print_sed_loading') );
        add_action('sed_footer', array($this,'tpl_sed_loading') );
		add_action('wp_footer', array($this,'tpl_sed_loading_iframe') );
    }


    function load_components(){

        foreach( $this->components AS $component ){
            //load application components
            $filename = strtolower("app_{$component}.class.php");
            include (SED_INC_EDITOR_DIR . DS . "components" . DS . $filename);
            $class = "App".ucfirst( strtolower( $component ) );
            $this->$component = new $class();
        }

    }

    function add_type( $type_key , $type_value ){
        $this->types[$type_key] = $type_value;
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

    function js_I18n(){
        $I18n = array();
        return apply_filters( "sed_js_I18n" , $I18n);
    }

    function addon_settings(){
        $sed_addon_settings = array();
        return apply_filters( "sed_addon_settings" , $sed_addon_settings);
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

}

