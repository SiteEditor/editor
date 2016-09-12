<?php

class SiteEditorManager{

    protected $previewing = false;

    protected $nonce_tick;

    protected $settings   = array();

    protected $controls = array();

    protected $post_controls = array();

    private $_post_values;

    private $_page_settings;

    function __construct(  ) {

      if( is_sed_save() || is_site_editor() || site_editor_app_on() ){
          add_action( 'wp_footer', array( $this, 'wp_styles_loaded' ) , 10000 );
          add_action( 'wp_footer', array( $this, 'wp_scripts_loaded' ) , 10000 );

          add_action( 'setup_theme',  array( $this, 'setup_theme' ) );
          add_action( 'wp_loaded',    array( $this, 'wp_loaded' ) );



          add_action( 'wp_redirect_status', array( $this, 'wp_redirect_status' ), 1000 );

          // Do not spawn cron (especially the alternate cron) while running the Customizer.
          remove_action( 'init', 'wp_cron' );

          //add_action('wp_print_scripts',         array( $this, 'wp_print_scripts_action'), 0);
          //add_action('wp_print_footer_scripts',  array( $this, 'wp_print_scripts_action'), 0);

      }

      add_action( 'sed_app_register' ,  array( $this, 'register_settings' ) );

      //add_action( 'sed_page_builder', array( $this , 'register_settings' ) , 10 , 1 );

      if( site_editor_app_on() ){
          if( !class_exists('SEDAjaxLess') )
              require_once SED_PLUGIN_DIR . DS . 'wp-inc' . DS . 'SEDAjaxLess' . DS  . 'SEDAjaxLess.php';

          new SEDAjaxLess();
      }

      $this->wp_theme = wp_get_theme( isset( $_REQUEST['theme'] ) ? $_REQUEST['theme'] : null );

    }

    function get_file($path) {

    	if ( function_exists('realpath') )
    		$path = realpath($path);

    	if ( ! $path || ! @is_file($path) )
    		return '';

    	return @file_get_contents($path);
    }

    static private function wp_print_scripts_action(){
        global $wp_scripts;
		if (! is_a($wp_scripts, 'WP_Scripts')) return;

        $queue = $wp_scripts->queue;
        $wp_scripts->all_deps($queue);
        $scripts_handle = $wp_scripts->to_do;

        if(is_array($scripts_handle) && !empty($scripts_handle))
            $scripts = implode(",", $scripts_handle);

        //self::print_js_script_tag( site_url( "/wp-admin/load-scripts.php?c=1&load=".$scripts ) );
        $out = "";
        foreach( $scripts_handle as $handle ) {
        	if ( !array_key_exists($handle, $wp_scripts->registered) )
        		continue;

        	$path = ABSPATH . $wp_scripts->registered[$handle]->src;
        	$out .= self::get_file($path) . "\n";
        }

        foreach( $wp_scripts->to_do as $key => $handle ) {
            // Standard way
            if ( $wp_scripts->do_item( $handle, $group ) ) {
                $wp_scripts->done[] = $handle;
            }
            unset( $wp_scripts->to_do[$key] );
        }


    }

    static private function print_js_script_tag($url, $conditional = '', $is_cache = true, $localize = '', $error_message = '') {

        if ($localize) {
            echo "<script type='text/javascript'>\n/* <![CDATA[ */\n$localize\n/* ]]> */\n</script>\n";
        }

        if ($conditional) {
            echo "<!--[if " . $conditional . "]>\n";
        }

        echo '<script type="text/javascript" src="' . $url . '">' . ($is_cache ? '/*Cache!*/' : '') . $error_message . '</script>' . "\n";

        if ($conditional) {
            echo "<![endif]-->" . "\n";
        }
    }

    function wp_scripts_loaded() {
        global $wp_scripts;
        //$queue = $wp_scripts->queue;
        //$wp_scripts->all_deps($queue);
        $all_scripts = $wp_scripts->done; 
        ?>
        <script type="text/javascript">
            var _wpScripts = <?php echo wp_json_encode( $all_scripts ); ?>;
        </script>
        <?php
    }


    function wp_styles_loaded() {
        global $wp_styles;
        //$queue = $wp_styles->queue;
        //$wp_styles->all_deps($queue);
        $all_styles = $wp_styles->done;
        ?>
        <script type="text/javascript">
            var _wpStyles = <?php echo wp_json_encode( $all_styles ); ?>;
        </script>
        <?php
    }

    function render_site_editor_base_scripts(){

        wp_enqueue_script("jquery-ui-full");

        wp_enqueue_script('sed-guidelines');

        wp_enqueue_script('sed-overlap');

        wp_enqueue_script( 'underscore' );

        wp_enqueue_script( 'modernizr' );

        wp_enqueue_script( 'handlebars' );

        wp_enqueue_script('sed-handlebars');

        wp_enqueue_script('jquery-contextmenu');

        //wp_enqueue_script('jquery-contenteditable');

        wp_enqueue_script('column-resize');

        wp_enqueue_script( 'siteeditor-base' );

        //plugins
        wp_enqueue_script( 'delete-plugin');
        wp_enqueue_script( 'select-plugin');
        wp_enqueue_script( 'media-plugin');
        wp_enqueue_script( 'preview-plugin' );
        wp_enqueue_script( 'sub-themes-plugin' );
        wp_enqueue_script( 'duplicate-plugin' );

        wp_enqueue_script( 'siteeditor-modules-scripts' );

        wp_enqueue_script( 'siteeditor-ajax' );

        wp_enqueue_script( 'tinycolor' );

        wp_enqueue_script( 'siteeditor-css' );

        //wp_enqueue_script( 'sed-app-synchronization' );

		wp_enqueue_script( 'sed-app-preview' );

        wp_enqueue_script( 'sed-app-contextmenu-render' );

        wp_enqueue_script( 'sed-app-preview-render' );

        //wp_enqueue_script('sed-style-editor');

        wp_enqueue_script("sed-tinymce");

        wp_enqueue_script("site-iframe");

        wp_enqueue_script('sed-app-shortcode-builder');

        wp_enqueue_script('sed-pagebuilder');

        wp_enqueue_script( 'sed-module-free-draggable');

        wp_enqueue_script('sed-app-widgets');

        wp_enqueue_script('bootstrap-tooltip' );

        wp_enqueue_script('bootstrap-popover' );


        /*
        global $site_editor_app;
        $modules_options = $site_editor_app->pagebuilder->modules;

        $modules_scripts = $site_editor_app->pagebuilder->modules_scripts;
        if(!empty($modules_scripts)){
            foreach($modules_scripts as $module => $scripts){
                if($modules_options[$module]['transport'] == "default"){
                    foreach($scripts as $script){
                        if(isset( $script[0] )){
                            $script[1] = !isset($script[1]) ? false: $script[1];
                            $script[2] = !isset($script[2]) ? array(): $script[2];
                            $script[3] = !isset($script[3]) ? false: $script[3];
                            $script[4] = !isset($script[4]) ? "all": $script[4];

                            wp_enqueue_script($script[0] , $script[1] , $script[2] , $script[3] , $script[4]);
                        }
                    }
                }
            }
        } */
    }


    function render_site_editor_base_styles(){
        //wp_enqueue_style("jquery-ui-full");
        wp_enqueue_style("contextmenu");
        wp_enqueue_style("site-iframe");
        wp_enqueue_style("fonts-sed-iframe");
        wp_enqueue_style("bootstrap-popover");

        /*
        global $site_editor_app;
        $modules_options = $site_editor_app->pagebuilder->modules;

        $modules_styles = $site_editor_app->pagebuilder->modules_styles;
        if(!empty($modules_styles)){
            foreach($modules_styles as $module => $styles){
                if($modules_options[$module]['transport'] == "default"){
                    foreach($styles as $style){
                        if(isset( $style[0] )){
                            $style[1] = !isset($style[1]) ? false: $style[1];
                            $style[2] = !isset($style[2]) ? array(): $style[2];
                            $style[3] = !isset($style[3]) ? false: $style[3];
                            $style[4] = !isset($style[4]) ? "all": $style[4];

                            wp_enqueue_style($style[0] , $style[1] , $style[2] , $style[3] , $style[4]);
                        }
                    }
                }
            }
        }*/
    }

	/**
	 * Is it a theme preview?
	 *
	 * @since 3.4.0
	 *
	 * @return bool True if it's a preview, false if not.
	 */
	public function is_preview() {
		return (bool) $this->previewing;
	}

	/**
	 * Start previewing the selected theme by adding filters to change the current theme.
	 *
	 */
	public function start_previewing_theme() {
		// Bail if we're already previewing.
        //var_dump( $this->is_preview() );
		if ( $this->is_preview() )
			return;
                            //
		$this->previewing = true;

		/**
		 * Fires once the Customizer theme preview has started.
		 *
		 * @since 3.4.0
		 *
		 * @param WP_Customize_Manager $this WP_Customize_Manager instance.
		 */
		do_action( 'sed_start_previewing_theme', $this );
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

	/**
	 * Get the registered settings.
	 *
	 * @since 3.4.0
	 *
	 * @return array
	 */
	public function settings() {
		return $this->settings;
	}

	/**
	 * Add a customize setting.
	 *
	 * @since 3.4.0
	 *
	 * @param SedAppSettings|string $id Customize Setting object, or ID.
	 * @param array $args                     Setting arguments; passed to SedAppSettings
	 *                                        constructor.
	 */
	public function add_setting( $id, $args = array() ) {
		if ( is_a( $id, 'SedAppSettings' ) )
			$setting = $id;
		else
			$setting = new SedAppSettings( $this, $id, $args );

		$this->settings[ $setting->id ] = $setting;
	}

	/**
	 * Retrieve a customize setting.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id Customize Setting ID.
	 * @return WP_Customize_Setting
	 */
	public function get_setting( $id ) {
		if ( isset( $this->settings[ $id ] ) )
			return $this->settings[ $id ];
	}

	/**
	 * Remove a customize setting.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id Customize Setting ID.
	 */
	public function remove_setting( $id ) {
		unset( $this->settings[ $id ] );
	}


	/**
	 * Get the registered controls.
	 *
	 * @since 3.4.0
	 *
	 * @return array
	 */
	public function controls() {
		return $this->controls;
	}

	/**
	 * Add a customize control.
	 *
	 * @since 3.4.0
	 *
	 * @param WP_Customize_Control|string $id   Customize Control object, or ID.
	 * @param array                       $args Control arguments; passed to WP_Customize_Control
	 *                                          constructor.
	 */
	public function add_control( $id, $args = array() ) {
		/*if ( is_a( $id, 'SEDAppControl' ) )
			$control = $id;
		else
			$control = new WP_Customize_Control( $this, $id, $args );
        */

		$this->controls[ $id ] = $args;
	}

	/**
	 * Retrieve a customize control.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id ID of the control.
	 * @return WP_Customize_Control $control The control object.
	 */
	public function get_control( $id ) {
		if ( isset( $this->controls[ $id ] ) )
			return $this->controls[ $id ];
	}

	/**
	 * Remove a customize control.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id ID of the control.
	 */
	public function remove_control( $id ) {
		unset( $this->controls[ $id ] );
	}

	/**
	 * Prevents AJAX requests from following redirects when previewing a theme
	 * by issuing a 200 response instead of a 30x.
	 *
	 * Instead, the JS will sniff out the location header.
	 *
	 * @since 3.4.0
	 *
	 * @param $status
	 * @return int
	 */
	public function wp_redirect_status( $status ) {
		if ( $this->is_preview() && !is_site_editor() )
			return 200;

		return $status;
	}

	/**
	 * Start preview and customize theme.
	 *
	 * Check if customize query variable exist. Init filters to filter the current theme.
	 *
	 * @since 3.4.0
	 */
	public function setup_theme() {
		send_origin_headers();
        global $sed_apps;

		if ( is_site_editor() && ! is_user_logged_in() )
		    auth_redirect();

		if ( $sed_apps->doing_ajax() && ! is_user_logged_in() ){
		    $sed_apps->sed_die( 0 );
        }
		show_admin_bar( false );

		if( !current_user_can( 'edit_theme_options' ) )
			$sed_apps->sed_die( -1 );

		//$this->original_stylesheet = get_stylesheet();

		//$this->theme = wp_get_theme( isset( $_REQUEST['theme'] ) ? $_REQUEST['theme'] : null );
        /*
		if ( $this->is_theme_active() ) {
			// Once the theme is loaded, we'll validate it.
			add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
		} else {
			// If the requested theme is not the active theme and the user doesn't have the
			// switch_themes cap, bail.
			if ( ! current_user_can( 'switch_themes' ) )
				$this->sed_die( -1 );

			// If the theme has errors while loading, bail.
			if ( $this->theme()->errors() )
				$this->sed_die( -1 );

			// If the theme isn't allowed per multisite settings, bail.
			if ( ! $this->theme()->is_allowed() )
				$this->sed_die( -1 );
		}  */

		// All good, let's do some internal business to preview the theme.
		$this->start_previewing_theme();
	}

    function wp_loaded(){

        //do_action( 'sed_app_register', $this );
		if ( $this->is_preview() && !is_site_editor() && !is_sed_save()  )
			$this->sed_app_preview_init();

    }


	/**
	 * Print javascript settings.
	 *
	 * @since 3.4.0
	 */
	public function sed_app_preview_init() {
	    global $sed_apps;
		$this->nonce_tick = check_ajax_referer( 'sed_app_preview_' . $this->get_stylesheet(), 'nonce' );

        $this->render_site_editor_base_scripts();
        $this->render_site_editor_base_styles();

        do_action("render_sed_scripts");
        do_action("render_sed_styles");
                    //wp_print_styles

        add_action( 'wp', array( $this, 'sed_preview_override_404_status' ) );

        add_action( 'wp_footer', array( $this, 'render_wow_js_editor' ) );
        add_action( 'wp_head', array( $this, 'editor_preview_base' ) );
        add_action( 'wp_head', array( $this, 'editor_html5' ) );

        add_action( 'wp_footer', array( $this, 'sed_app_preview_settings' ), 20 ); //wp_footer

        add_action( 'shutdown', array( $this, 'sed_app_preview_signature' ), 1000 );
        add_filter( 'wp_die_handler', array( $this, 'remove_preview_signature' ) );

		foreach ( $this->settings as $setting ) {
			$setting->preview();
		}

        do_action( 'sed_app_preview_init', $this );

        //site-editor template
        add_filter( 'template_include', array($sed_apps,'template_chooser') );

        // Add specific CSS class by filter
        add_filter( 'body_class', array( $this, 'sed_app_body_class' ) );
    }


	/**
	 * Prevent sending a 404 status when returning the response for the customize
	 * preview, since it causes the jQuery AJAX to fail. Send 200 instead.
	 *
	 * @since 4.0.0
	 * @access public
	 */
	public function sed_preview_override_404_status() {
		if ( is_404() ) {
			status_header( 200 );
		}
	}

	/**
	 * Print javascript settings for preview frame.
	 *
	 * @since 3.4.0
	 */
	public function sed_app_preview_settings() {
        global $site_editor_app;

	  	$settings = array(
			'values'  => array(),
            'types'   => array(),
			'channel' => esc_js( $_POST['customize_messenger_channel'] ),
            'post'    => array(
                'id'     =>   0
            )
		);

		if ( 2 == $this->nonce_tick ) {
			$settings['nonce'] = array(
				'save'    => wp_create_nonce( 'sed_app_save_' . $this->get_stylesheet() ),
				'preview' => wp_create_nonce( 'sed_app_preview_' . $this->get_stylesheet() ),
                'refresh' => wp_create_nonce( 'sed_app_refresh_settings_' . $site_editor_app->get_stylesheet() )
			);
		}

        /*$def_settings = array_merge($site_editor_app->toolbar->settings , $site_editor_app->settings->settings);

		foreach ( $def_settings AS $id => $values ) {
		    if( isset( $values['type'] ) )
                $stype = $values['type'];
            else
                $stype = "general";

			$settings['types'][ $id ] = $stype;
		}

        $settings = array_merge($settings , $this->post_value); */
		foreach ( $this->settings as $id => $setting ) {
			$settings['values'][ $id ] = $setting->js_value();

		    if( !empty( $setting->type ) )
                $stype = $setting->type;
            else
                $stype = "general";

			$settings['types'][ $id ] = $stype;

		}            //var_dump( $settings['values'] );

        $sed_addon_settings = $site_editor_app->addon_settings();

        $sed_js_I18n = $site_editor_app->js_I18n();

		?>

		<script type="text/javascript">
                var SED_PB_MODULES_URL = "<?php echo SED_BASE_URL."applications/pagebuilder/modules/"?>";
                var SED_UPLOAD_URL = "<?php echo site_url("/wp-content/uploads/site-editor/");?>";
                var SED_BASE_URL = "<?php echo SED_BASE_URL;?>";
                var IS_SSL = <?php if( is_ssl() ) echo "true";else echo "false";?>;
				var IS_RTL = <?php if( is_rtl() ) echo "true";else echo "false";?>;
                var LIBBASE = {url : "<?php echo SED_BASE_URL;?>libraries/"};
                var SEDAJAX = {url : "<?php echo SED_BASE_URL;?>libraries/ajax/site_editor_ajax.php"};
		        var _sedAppEditorSettings = <?php echo wp_json_encode( $settings ); ?>;
                //var _sedAppPageBuilderModulesScripts = <?php echo wp_json_encode( $site_editor_app->pagebuilder->modules_scripts ); ?>;
                //var _sedAppPageBuilderModulesStyles = <?php echo wp_json_encode( $site_editor_app->pagebuilder->modules_styles ); ?>;
                var _sedAppEditorI18n = <?php echo wp_json_encode( $sed_js_I18n )?>;
                var _sedAppEditorAddOnSettings = <?php echo wp_json_encode( $sed_addon_settings )?>;
                var _sedAppPageContentInfo = <?php echo wp_json_encode( $this->get_page_content_info() )?>;
		</script>
		<?php

	}

    function get_page_content_info(){
        $info = array();

        if(is_category() || is_tag() || is_tax()){

            $object = get_queried_object();

            $info['type']     = "taxonomy";
            $info['taxonomy'] = $object->taxonomy;
            //$info['sub_type'] = "term";
            $info['term_id']  =  $object->term_id;

        } elseif( is_home() === true && is_front_page() === true ){
            $info['type']     = "home_blog";
        } elseif( is_home() === false && is_front_page() === true ){
            $sed_post_id = get_queried_object()->ID;
            $info['type']     = "home_page";
            $info['post_id']  = $sed_post_id;
        } elseif( is_home() === true && is_front_page() === false  ){
            $sed_post_id        = get_queried_object()->ID;
            $info['type']       = "index_blog";
            $info['post_id']    = $sed_post_id;
        } elseif ( is_search() ) {
            $info['type']       = "search_results";
        } elseif ( is_404() ) {
            $info['type']       = "404_page";
        } elseif( is_singular() ){
            $post = get_queried_object();
            $info['type']       = "single";
            $info['post_id']    = $post->ID;
            $info['post_type']    = $post->post_type;
        } elseif ( is_post_type_archive() ) {
            $sed_post_type = get_queried_object()->name;
            $info['type']       = "post_type_archive";
            $info['post_type']  = $sed_post_type;
        } elseif ( is_author() ) {
            $info['type']       = "author_archive";
        } elseif ( is_date() || is_day() || is_month() || is_year() || is_time() ) {
            $info['type']       = "date_archive";
        }

        $info = apply_filters( "sed_page_content_info" , $info );

        return $info;
    }
	/**
	 * Print a workaround to handle HTML5 tags in IE < 9
	 *
	 * @since 3.4.0
	 */
	public function editor_html5() { ?>
		<!--[if lt IE 9]>
		<script type="text/javascript">
			var e = [ 'abbr', 'article', 'aside', 'audio', 'canvas', 'datalist', 'details',
				'figure', 'footer', 'header', 'hgroup', 'mark', 'menu', 'meter', 'nav',
				'output', 'progress', 'section', 'time', 'video' ];
			for ( var i = 0; i < e.length; i++ ) {
				document.createElement( e[i] );
			}
		</script>
		<![endif]--><?php
	}

	/**
	 * Print base element for editor preview frame.
	 *
	 * @since 3.4.0
	 */
	public function editor_preview_base() {
		?><base href="<?php echo home_url( '/' ); ?>" /><?php
	}

    //fix wow js bug : prevent wow bug when first time add animation to modules that no render any wow element in page
    function render_wow_js_editor(){
        echo '<div class="wow rollOut site-editor-wow"></div>';
    }

    function sed_app_body_class( $classes ) {
    	// add 'class-name' to the $classes array
    	$classes[] = 'siteeditor-app';
    	// return the $classes array
    	return $classes;
    }

    /**
     * Prints a signature so we can ensure the customizer was properly executed.
     *
     * @since 3.4.0
     */
    public function sed_app_preview_signature() {
        echo 'SED_APP_SIGNATURE';
    }

    /**
     * Removes the signature in case we experience a case where the customizer was not properly executed.
     *
     * @since 3.4.0
     */
    public function remove_preview_signature( $return = null ) {
        remove_action( 'shutdown', array( $this, 'customize_preview_signature' ), 1000 );

        return $return;
    }

	/**
	 * Decode the $_POST['sed_page_customized'] values for a specific Customize Setting.
	 *
	 * @since 3.4.0
	 *
	 * @param SedAppSettings $setting A SedAppSettings derived object
	 * @return string $post_value Sanitized value
	 */
	public function post_value( $setting ) {

		if ( ! isset( $this->_post_values ) ) {

             //&& isset($_POST['preview_type']) && in_array( $_POST['preview_type'] , array("refresh" , "new"))
            //using in save , refresh && new url preview
            if ( isset( $_POST['sed_page_customized'] ) ){

                $_post_values = json_decode( wp_unslash( $_POST['sed_page_customized'] ), true );

                if( isset($_POST['preview_type']) && $_POST['preview_type'] == "new" ){
                    foreach ( $_post_values as $id => $value ) {
                       $curr_setting = $this->settings[ $id ];
                       if($curr_setting->option_type == "base" || empty( $curr_setting->option_type ) )
                           unset( $_post_values[$id] );  //$setting->id

                    }
                }

                $this->_post_values = apply_filters( "sed_current_page_options" , $_post_values );

             //var_dump( $this->_post_values );

			}else{

				$this->_post_values = false;
            }
		}

		if ( isset( $this->_post_values[ $setting->id ] ) )
			return $setting->sanitize( $this->_post_values[ $setting->id ] );

	}
                                  //$pagebuilder
    function register_settings( ){

        //for typography
        $this->add_setting( 'page_mce_used_fonts', array(
			'default'        => array() ,
			'option_type'    => 'base' ,
            'transport'      => 'postMessage'
		) );

        $this->add_setting( 'theme_content' , array(
            'default'       => false,
            'option_type'    => 'base' ,
            'capability'     => 'manage_options',
            'transport'     => 'postMessage'
        ));

		/*$this->add_setting( 'show_on_front', array(
			'default'        => get_option( 'show_on_front' ),
			'capability'     => 'manage_options',
			'option_type'    => 'option'
		) );

		$this->add_setting( 'page_on_front', array(
			'option_type'   => 'option',
			'capability'    => 'manage_options',
		//	'theme_supports' => 'static-front-page',
		) );

		$this->add_setting( 'page_for_posts', array(
			'option_type'    => 'option',
			'capability'     => 'manage_options'
		) ); */

    }



    /*function print_post_settings(){
        echo in template/default/footer.php
    } */


    function sed_page_settings(){
        global $sed_apps;

        if ( ! isset( $this->_page_settings ) ) {

            $sed_page_id = $sed_apps->sed_page_id;
            $sed_page_type = $sed_apps->sed_page_type;
                                               //var_dump( "sed_page_id ------ : " , $sed_page_id );
            $sed_settings = sed_get_page_options($sed_page_id , $sed_page_type);

            return $this->_page_settings = $sed_settings;
        }else
            return $this->_page_settings;

    }


}


/**
 * App Setting Class.
 *
 * Handles saving and sanitizing of settings.
 *
 * @package SiteEditor
 * @subpackage Settings
 * @since 3.4.0
 */
class SedAppSettings{
	/**
	 * @access public
	 * @var WP_Customize_Manager
	 */
	public $manager;

	/**
	 * @access public
	 * @var string
	 */
	public $id;

	/**
	 * @access public
	 * @var string
	 */
	public $type = 'general';  //general || style-editor || module || post

	/**
	 * @access public
	 * @var string
	 */
	public $option_type = 'base';  //option || post_meta || post || theme_mod || custom || base

	/**
	 * Capability required to edit this setting.
	 *
	 * @var string
	 */
	public $capability = 'edit_theme_options';

	/**
	 * Feature a theme is required to support to enable this setting.
	 *
	 * @access public
	 * @var string

	public $theme_supports  = ''; */
	public $default         = '';
	public $transport       = 'refresh';

	/**
	 * Server-side sanitization callback for the setting's value.
	 *
	 * @var callback
	 */
	public $sanitize_callback    = '';
	public $sanitize_js_callback = '';

	protected $id_data = array();

	/**
	 * Cached and sanitized $_POST value for the setting.
	 *
	 * @access private
	 * @var mixed
	 */
	private $_post_value;

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 * @since 3.4.0
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string               $id      An specific ID of the setting. Can be a
	 *                                      theme mod or option name.
	 * @param array                $args    Setting arguments.
	 * @return SedAppSettings $setting
	 */
	public function __construct( $manager, $id, $args = array() ) {
		$keys = array_keys( get_object_vars( $this ) );
		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) )
				$this->$key = $args[ $key ];
		}

		$this->manager = $manager;
		$this->id = $id;

		// Parse the ID for array keys.
		$this->id_data[ 'keys' ] = preg_split( '/\[/', str_replace( ']', '', $this->id ) );
		$this->id_data[ 'base' ] = array_shift( $this->id_data[ 'keys' ] );

		// Rebuild the ID.
		$this->id = $this->id_data[ 'base' ];
		if ( ! empty( $this->id_data[ 'keys' ] ) )
			$this->id .= '[' . implode( '][', $this->id_data[ 'keys' ] ) . ']';

		if ( $this->sanitize_callback )
			add_filter( "sed_app_sanitize_{$this->id}", $this->sanitize_callback, 10, 2 );

		if ( $this->sanitize_js_callback )
			add_filter( "sed_app_sanitize_js_{$this->id}", $this->sanitize_js_callback, 10, 2 );

		return $this;
	}

/*
function meta_filter_posts( $query ) {
	if( !is_admin() )
		return $query;
	if( isset( $_GET['the_meta'] ) ) {
		if( $_GET['the_meta'] > 0 ) {
		// $query is the WP_Query object, set is simply a method of the WP_Query class that sets a query var parameter
		$query->set( 'meta_key', '_EventStartDate' );
		$query->set( 'meta_value', '2010-01-07 00:00:00' );
	}
	}
	return $query;
}
add_filter( 'pre_get_posts', 'meta_filter_posts' );
*/
	/**
	 * Handle previewing the setting.
	 *
	 * @since 3.4.0
	 */
	public function preview() {

		switch( $this->option_type ) {
			case 'theme_mod' :
				add_filter( 'theme_mod_' . $this->id_data[ 'base' ], array( $this, '_preview_filter' ) );
				break;
			case 'option' :
				if ( empty( $this->id_data[ 'keys' ] ) )
					add_filter( 'pre_option_' . $this->id_data[ 'base' ], array( $this, '_preview_filter' ) );
				else {
					add_filter( 'option_' . $this->id_data[ 'base' ], array( $this, '_preview_filter' ) );
					add_filter( 'default_option_' . $this->id_data[ 'base' ], array( $this, '_preview_filter' ) );
				}
				break;
            /*case 'post_meta' :
                add_filter( "get_". $this->id_data[ 'base' ] ."_metadata", array( $this, '_preview_filter' ) );
                break;
            case 'post' :
                //add_filter( "get_". $this->id_data[ 'base' ] ."_metadata", array( $this, '_preview_filter' ) );
                break; */
			default :

				/**
				 * Fires when the {@see SedAppSettings::preview()} method is called for settings
				 * not handled as theme_mods or options.
				 *
				 * The dynamic portion of the hook name, `$this->id`, refers to the setting ID.
				 *
				 * @since 3.4.0
				 *
				 * @param SedAppSettings $this {@see SedAppSettings} instance.
				 */
				do_action( "sed_app_preview_{$this->id}", $this );

				/**
				 * Fires when the {@see SedAppSettings::preview()} method is called for settings
				 * not handled as theme_mods or options.
				 *
				 * The dynamic portion of the hook name, `$this->option_type`, refers to the setting type.
				 *
				 * @since 4.1.0
				 *
				 * @param SedAppSettings $this {@see SedAppSettings} instance.
				 */
				do_action( "sed_app_preview_{$this->option_type}", $this );
		}
	}

	/**
	 * Callback function to filter the theme mods and options.
	 *
	 * @since 3.4.0
	 * @uses SedAppSettings::multidimensional_replace()
	 *
	 * @param mixed $original Old value.
	 * @return mixed New or old value.
	 */
	public function _preview_filter( $original ) {
		return $this->multidimensional_replace( $original, $this->id_data[ 'keys' ], $this->post_value() );
	}

	/**
	 * Check user capabilities and theme supports, and then save
	 * the value of the setting.
	 *
	 * @since 3.4.0
	 *
	 * @return false|null False if cap check fails or value isn't set.
	 */
	public final function save() {
		$value = $this->post_value();

		if ( ! $this->check_capabilities() || ! isset( $value ) )
			return false;

		/**
		 * Fires when the SedAppSettings::save() method is called.
		 *
		 * The dynamic portion of the hook name, `$this->id_data['base']` refers to
		 * the base slug of the setting name.
		 *
		 * @since 3.4.0
		 *
		 * @param SedAppSettings $this {@see SedAppSettings} instance.
		 */
		do_action( 'sed_app_save_' . $this->id_data[ 'base' ], $this );

        $value = apply_filters( 'sed_app_save_' . $this->id_data[ 'base' ] , $value );

		$this->update( $value );
	}

	/**
	 * Fetch and sanitize the $_POST value for the setting.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $default A default value which is used as a fallback. Default is null.
	 * @return mixed The default value on failure, otherwise the sanitized value.
	 */
	public final function post_value( $default = null ) {
		// Check for a cached value
		if ( isset( $this->_post_value ) )
		   	return $this->_post_value;


		// Call the manager for the post value
		$result = $this->manager->post_value( $this );

		if ( isset( $result ) )
			return $this->_post_value = $result;
		else
			return $default;
	}

	/**
	 * Sanitize an input.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $value The value to sanitize.
	 * @return mixed Null if an input isn't valid, otherwise the sanitized value.
	 */
	public function sanitize( $value ) {
		$value = wp_unslash( $value );

		/**
		 * Filter a Customize setting value in un-slashed form.
		 *
		 * @since 3.4.0
		 *
		 * @param mixed                $value Value of the setting.
		 * @param SedAppSettings $this  SedAppSettings instance.
		 */
		return apply_filters( "sed_app_sanitize_{$this->id}", $value, $this );
	}

	/**
	 * Save the value of the setting, using the related API.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $value The value to update.
	 * @return mixed The result of saving the value.
	 */
	protected function update( $value ) {
		switch( $this->option_type ) {
			case 'theme_mod' :
				return $this->_update_theme_mod( $value );

			case 'option' :
				return $this->_update_option( $value );
            /*
            case 'post_meta' :
                return $this->_update_post_meta( $value );

            case 'post' :
            in site-editor-posts.php > function update_post
            */

			default :

				/**
				 * Fires when the {@see SedAppSettings::update()} method is called for settings
				 * not handled as theme_mods or options.
				 *
				 * The dynamic portion of the hook name, `$this->option_type`, refers to the option_type of setting.
				 *
				 * @since 3.4.0
				 *
				 * @param mixed                $value Value of the setting.
				 * @param SedAppSettings $this  SedAppSettings instance.
				 */
				return do_action( 'sed_app_update_' . $this->option_type, $value, $this );
		}
	}

	/**
	 * Update the theme mod from the value of the parameter.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $value The value to update.
	 * @return mixed The result of saving the value.
	 */
	protected function _update_theme_mod( $value ) {
		// Handle non-array theme mod.
		if ( empty( $this->id_data[ 'keys' ] ) )
			return set_theme_mod( $this->id_data[ 'base' ], $value );

		// Handle array-based theme mod.
		$mods = get_theme_mod( $this->id_data[ 'base' ] );
		$mods = $this->multidimensional_replace( $mods, $this->id_data[ 'keys' ], $value );
		if ( isset( $mods ) )
			return set_theme_mod( $this->id_data[ 'base' ], $mods );
	}

	/**
	 * Update the option from the value of the setting.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $value The value to update.
	 * @return bool|null The result of saving the value.
	 */
	protected function _update_option( $value ) {
		// Handle non-array option.
		if ( empty( $this->id_data[ 'keys' ] ) )
			return update_option( $this->id_data[ 'base' ], $value );

		// Handle array-based options.
		$options = get_option( $this->id_data[ 'base' ] );
		$options = $this->multidimensional_replace( $options, $this->id_data[ 'keys' ], $value );
		if ( isset( $options ) )
			return update_option( $this->id_data[ 'base' ], $options );
	}

	/**
	 * Fetch the value of the setting.
	 *
	 * @since 3.4.0
	 *
	 * @return mixed The value.
	 */
	public function value() {

        //(only using on ****top iframe****) this condition using only site editor page not sed app iframes
        if( $this->option_type == "base" || empty( $this->option_type ) ){

			//only using on ****sed app iframes****
			if ( isset( $_POST['sed_page_customized'] ) && isset($_POST['preview_type']) && $_POST['preview_type'] == "refresh" ){

				$value = $this->post_value();
				return $value;

			}else{

				$sed_settings = $this->manager->sed_page_settings();

				if( isset($sed_settings[$this->id]) )
					return $sed_settings[$this->id];
				else
					return $this->default;

			}

        }

         //using for all options except base options(page options)
        //using on ****sed app iframes**** and ****top iframe****
		// Get the callback that corresponds to the setting option_type.
		switch( $this->option_type ) {
			case 'theme_mod' :
				$function = 'get_theme_mod';
				break;
			case 'option' :
				$function = 'get_option';
				break;
			/*case 'post_meta' :
				$function = 'get_post_meta';
				break;
			case 'post' :
				$function = 'get_post';
				break; */
			default :

				/**
				 * Filter a Customize setting value not handled as a theme_mod or option.
				 *
				 * The dynamic portion of the hook name, `$this->id_date['base']`, refers to
				 * the base slug of the setting name.
				 *
				 * For settings handled as theme_mods or options, see those corresponding
				 * functions for available hooks.
				 *
				 * @since 3.4.0
				 *
				 * @param mixed $default The setting default value. Default empty.
				 */
				return apply_filters( 'sed_app_value_' . $this->id_data[ 'base' ], $this->default );
		}

		// Handle non-array value
		if ( empty( $this->id_data[ 'keys' ] ) )
			return $function( $this->id_data[ 'base' ], $this->default );



		// Handle array-based value
		$values = $function( $this->id_data[ 'base' ] );
		return $this->multidimensional_get( $values, $this->id_data[ 'keys' ], $this->default );
	}

	/**
	 * Sanitize the setting's value for use in JavaScript.
	 *
	 * @since 3.4.0
	 *
	 * @return mixed The requested escaped value.
	 */
	public function js_value() {

		/**
		 * Filter a Customize setting value for use in JavaScript.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to the setting ID.
		 *
		 * @since 3.4.0
		 *
		 * @param mixed                $value The setting value.
		 * @param SedAppSettings $this  {@see SedAppSettings} instance.
		 */
		$value = apply_filters( "sed_app_sanitize_js_{$this->id}", $this->value(), $this );

		if ( is_string( $value ) )
			return html_entity_decode( $value, ENT_QUOTES, 'UTF-8');

		return $value;
	}

	/**
	 * Validate user capabilities whether the theme supports the setting.
	 *
	 * @since 3.4.0
	 *
	 * @return bool False if theme doesn't support the setting or user can't change setting, otherwise true.
	 */
	public final function check_capabilities() {
		if ( $this->capability && ! call_user_func_array( 'current_user_can', (array) $this->capability ) )
			return false;

		/*if ( $this->theme_supports && ! call_user_func_array( 'current_theme_supports', (array) $this->theme_supports ) )
			return false;
        */
		return true;
	}

	/**
	 * Multidimensional helper function.
	 *
	 * @since 3.4.0
	 *
	 * @param $root
	 * @param $keys
	 * @param bool $create Default is false.
	 * @return null|array Keys are 'root', 'node', and 'key'.
	 */
	final protected function multidimensional( &$root, $keys, $create = false ) {
		if ( $create && empty( $root ) )
			$root = array();

		if ( ! isset( $root ) || empty( $keys ) )
			return;

		$last = array_pop( $keys );
		$node = &$root;

		foreach ( $keys as $key ) {
			if ( $create && ! isset( $node[ $key ] ) )
				$node[ $key ] = array();

			if ( ! is_array( $node ) || ! isset( $node[ $key ] ) )
				return;

			$node = &$node[ $key ];
		}

		if ( $create && ! isset( $node[ $last ] ) )
			$node[ $last ] = array();

		if ( ! isset( $node[ $last ] ) )
			return;

		return array(
			'root' => &$root,
			'node' => &$node,
			'key'  => $last,
		);
	}

	/**
	 * Will attempt to replace a specific value in a multidimensional array.
	 *
	 * @since 3.4.0
	 *
	 * @param $root
	 * @param $keys
	 * @param mixed $value The value to update.
	 * @return
	 */
	final protected function multidimensional_replace( $root, $keys, $value ) {
		if ( ! isset( $value ) )
			return $root;
		elseif ( empty( $keys ) ) // If there are no keys, we're replacing the root.
			return $value;

		$result = $this->multidimensional( $root, $keys, true );

		if ( isset( $result ) )
			$result['node'][ $result['key'] ] = $value;

		return $root;
	}

	/**
	 * Will attempt to fetch a specific value from a multidimensional array.
	 *
	 * @since 3.4.0
	 *
	 * @param $root
	 * @param $keys
	 * @param mixed $default A default value which is used as a fallback. Default is null.
	 * @return mixed The requested value or the default value.
	 */
	final protected function multidimensional_get( $root, $keys, $default = null ) {
		if ( empty( $keys ) ) // If there are no keys, test the root.
			return isset( $root ) ? $root : $default;

		$result = $this->multidimensional( $root, $keys );
		return isset( $result ) ? $result['node'][ $result['key'] ] : $default;
	}

	/**
	 * Will attempt to check if a specific value in a multidimensional array is set.
	 *
	 * @since 3.4.0
	 *
	 * @param $root
	 * @param $keys
	 * @return bool True if value is set, false if not.
	 */
	final protected function multidimensional_isset( $root, $keys ) {
		$result = $this->multidimensional_get( $root, $keys );
		return isset( $result );
	}
}


class SEDContextmenuProvider{

    function __construct(  ) {
        if( site_editor_app_on() ){
            add_action( 'wp_footer', array( $this , 'sed_app_contextmenu' ), 20 );
            add_action( 'wp_footer', array( $this , 'sed_app_contextmenu_settings' ), 20 );
        }
    }

    public function sort_menu_items_by_priority( $items ){

        if(!empty( $items )){

            $items_priorities = array();
            foreach($items AS $name => $item){
                $items_priorities[$name] = $item->priority;
            }
            asort( $items_priorities );
            $new_items = array();
            foreach($items_priorities AS $name => $priority){
                $new_items[$name] = $items[$name];
            }

            return $new_items;
        }else{
            return $items;
        }
    }

    public function sed_app_contextmenu(){
        global $site_editor_app;

        $menus = $site_editor_app->contextmenu->menus;



        if(!empty( $menus )){
           foreach($menus AS $name => $menu){
        ?>
        <ul id="<?php echo $name; ?>_contextmenu" class="jeegoocontext cm_default sed_app_contextmenu">
        <?php
          echo $this->context_menu_output( $menu->items , $name , $name);
        ?>
        </ul>
        <?php
           }
        }
    }

    public function sed_app_contextmenu_item_settings( $items = array() , $top_menu_name , $parent_name ){
        $item_contextmenu_settings = array();
        if(!empty( $items )){
            foreach($items AS $item_name => $item){
                if($item->is_submenu === true){
                    $item_contextmenu_settings = array_merge($item_contextmenu_settings , $this->sed_app_contextmenu_item_settings( $item->items , $top_menu_name , $item_name ) );
                }else{
                    $item_contextmenu_settings[$top_menu_name . "_" . $parent_name . "_" . $item_name] = array(
                        'item_name'     =>   $item_name ,
                        'options'       =>   $item->options  ,
                    );
                }
            }
        }
        return $item_contextmenu_settings;
    }

    public function sed_app_contextmenu_settings(){
        global $site_editor_app;

        $contextmenu_settings = array();
        $item_contextmenu_settings = array();
        $menus = $site_editor_app->contextmenu->menus;
        if(!empty( $menus )){
           foreach($menus AS $name => $menu){
                $contextmenu_settings[$name] = array(
                    'type'      =>   $menu->type  ,
                    'selector'  =>   $menu->selector  ,
                    'menu_id'   =>   $menu->menu_id  ,
                    'shortcode' =>   $menu->shortcode
                );
                $item_contextmenu_settings = array_merge($item_contextmenu_settings , $this->sed_app_contextmenu_item_settings( $menu->items , $name , $name  ) );
           }
    		?>
    		<script type="text/javascript">
    		        var _sedAppEditorContextMenuSettings = <?php echo wp_json_encode( $contextmenu_settings ); ?>;
                    var _sedAppEditorItemContextMenuSettings = <?php echo wp_json_encode( $item_contextmenu_settings ); ?>;
    		</script>
    		<?php
        }
    }

    function context_menu_output($items = array() , $top_menu_name , $parent_name){
        $output = '';
        if(!empty( $items )){
            $items = $this->sort_menu_items_by_priority( $items );
            foreach($items AS $name => $item){
                if( $item->type_icon == "class" ){
                    $icon_class = $item->icon;
                    $icon_img = "";
                }else if( $item->type_icon == "src" ){
                    $icon_class = "";
                    $icon_img = '<img src="'. $item->icon .'" alt="'. $item->title .'" />';
                }

                $attr_string = '';
                $classes = '';
                if(!empty($item->attr)){
                    foreach($item->attr AS $attr => $value){
                        if(strtolower($attr) == "class"){
                            $classes .= $value;
                        }else{
                            $attr_string .= $attr . '="' . $value . '" ';
                        }
                    }
                }


                if(!empty($item->custom_html))
                    $output .= '<li class="contextmenu-item-container contextmenu-custom '. $classes .'" data-name="'.$name.'" data-action="'.$item->action.'" id="'. $top_menu_name . "_" . $parent_name . "_" . $name .'" tabindex="-1" role="menuitem" '. $attr_string .'>'.$item->custom_html;
                else
                    $output .= '<li class="contextmenu-item-container '. $classes .'" data-name="'.$name.'" data-action="'.$item->action.'" id="'. $top_menu_name . "_" . $parent_name . "_" . $name .'" tabindex="-1" role="menuitem" '. $attr_string .'><a><span class="menu_item_icon '. $icon_class .'">'.$icon_img.'</span><span class="menu_item_txt">'.$item->title.'</span></a>';

                if($item->is_submenu === true){
                    $output .= '<ul>'. $this->context_menu_output($item->items , $top_menu_name , $name) .'</ul>';
                }

                $output .= '</li>';
            }
        }
        return $output;
    }

}

new SEDContextmenuProvider();


class SEDPBModuleProvider{

    public $pb_modules_tmpl;
                            

    function __construct(  ) {
        if( site_editor_app_on() ){
            add_action( 'wp_footer', array( $this, 'sed_app_pagebuilder_modules' ), 20 );
            add_action( 'wp_footer', array( &$this, 'siteeditor_check_less_compailer' ), 10 );
        }
    }

    function siteeditor_check_less_compailer(){
       global $sed_apps;

       $modules_activate = array_keys( sed_get_setting("live_module") );
       $modules = $sed_apps->module_info;
       $not_compiled_files = array();

        foreach( $modules_activate AS $module_name  ){
            $module_info = $modules[$module_name];
            $skins = $module_info['skins'];
            foreach( $skins AS $skin => $skin_info ){
                $lesses = $skin_info['less'];
                if( !empty( $lesses ) )
                    $not_compiled_files = array_merge( $not_compiled_files , $this->check_lesses_compiled( $lesses ) );
            }
            $lesses = $module_info['less'];



            if( !empty( $lesses ) )
                $not_compiled_files = array_merge( $not_compiled_files , $this->check_lesses_compiled( $lesses ) );
        }


        if( !empty( $not_compiled_files ) ){
            include SED_PLUGIN_DIR . DS . 'wp-inc' . DS . 'SEDAjaxLess' . DS  .'view'. DS . 'modal_less_compile.php';
        }

    }

    function check_lesses_compiled( $lesses ){
        $not_compiled_files = array();
        foreach( $lesses AS $handle => $less_info ){
            $less_files = array();
            $less_files[] = WP_CONTENT_DIR . substr( str_replace('/' , DS , $less_info["src"] ) , 0 , -4) . ".less";

            $import_files = $less_info['import'];
            if( !empty( $import_files ) && is_array( $import_files ) ){
                $wp_upload = wp_upload_dir();

        		$constants = array(
        			'@sed-root'				=> SED_PLUGIN_DIR  ,
        			'@sed-framework-root'	=> SED_LIB_PATH. DS ."less-framework" ,
        			'@sed-modules-root'		=> SED_PB_MODULES_PATH ,
                    '@sed-images-root'		=> SED_PB_IMAGES_PATH ,
        			'@sed-plugins-root'		=> WP_PLUGIN_DIR ,
        			'@sed-themes-root'		=> get_theme_root(),
                    '@sed-uploads-root'		=> $wp_upload['basedir']  ,
        		);

                foreach( $import_files AS $file_path ){
                    foreach( $constants As $var => $val ){
                        $file_path = str_replace( $var , $val , $file_path );
                    }

                    $file_path = str_replace( '/' , DS , $file_path );
                    $wp_base = str_replace( '/' , DS , dirname( dirname( WP_PLUGIN_DIR ) ) );
                    $is_abs_path = strpos( $file_path , $wp_base  );

                    if( $is_abs_path === false ){
                        $file_path = dirname( $less_files[0] ) . DS . $file_path;
                    }

                    $less_files[] = $file_path;
                }
            }

            $css_abs_path = SED_UPLOAD_PATH . str_replace( '/' , DS , '/modules' . $less_info["src_rel"] );

            if( !class_exists( 'SEDAppLess' ) ) 
                require_once SED_APP_PATH . DS . 'sed_app_less.class.php';

            $is_compiled = SEDAppLess::checkedCompile( $less_files , $css_abs_path );
            if( !$is_compiled )
                $not_compiled_files[$handle] = array( $less_info["src"] , $less_info["src_rel"] );
        }

        return $not_compiled_files;
    }

    public function sed_app_pagebuilder_modules(){
        global $sed_apps;

      	$attachments = array_map( 'wp_prepare_attachment_for_js', $sed_apps->attachments_loaded );
      	$attachments = array_filter( $attachments );

        $modules_activate = array_keys( sed_get_setting("live_module") );
        $modules_info = array();
        foreach( $modules_activate AS $module_name ){
            $module_info = $sed_apps->module_info[$module_name];

            $skins = $module_info['skins'];
            foreach( $skins AS $skin => $skin_info ){
                $skin_scripts = $skin_info['scripts'];
                $scripts = array();

                if( !empty( $skin_scripts ) ){
                    foreach( $skin_scripts AS $key => $script ){
                        $scripts[] = array( $script['handle'] , content_url( "/" . $script['src'] ), $script['deps'] , $script['ver'] , $script['in_footer'] );
                    }
                }

                $modules_info[$module_name]['skins'][$skin]['scripts'] = $scripts;

                $styles = array();
                $lesses = $skin_info['less'];
                if( !empty( $lesses ) ){
                    foreach( $lesses AS $handle => $less_info  ){
                        $styles[] = array( $less_info['handle'] , SED_UPLOAD_URL. "/modules" .$less_info['src_rel'] , $less_info['deps'] , $less_info['ver'] , $less_info['media'] );
                    }
                }

                $skin_styles = $skin_info['styles'];
                $css_styles = array();

                if( !empty( $skin_styles ) ){
                    foreach( $skin_styles AS $key => $style ){
                        $css_styles[] = array( $style['handle'] , content_url( "/" . $script['src'] ) , $style['deps'] , $style['ver'] , $style['media'] );
                    }
                }

                $modules_info[$module_name]['skins'][$skin]['styles'] = array_merge( $css_styles , $styles );
                   
            }

 
        }
                /*var _sedAppPageBuilderModulesScripts = <?php echo wp_json_encode( $site_editor_app->pagebuilder->modules_scripts ); ?>;
                //var _sedAppPageBuilderModulesStyles = <?php echo wp_json_encode( $site_editor_app->pagebuilder->modules_styles ); ?>; */
        ?>

		<script type="text/javascript">
                var _sedAppPageBuilderModulesInfo = <?php echo wp_json_encode( $modules_info ); ?>;
                var _sedAppPBAttachmentsSettings = <?php echo wp_json_encode( $attachments ); ?>;

		</script>

        <?php

    }

}

new SEDPBModuleProvider();

