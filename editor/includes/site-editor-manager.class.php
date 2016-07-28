<?php

class SiteEditorManager{

    protected $previewing = false;

    protected $nonce_tick;

    protected $settings   = array();

    protected $controls = array();

    protected $post_controls = array();

    private $_post_values;

    private $_base_values;

    private $_page_settings;

    function __construct(  ) {

		require_once SED_INC_EDITOR_DIR . DS . 'site-editor-setting.class.php';
		require_once SED_INC_EDITOR_DIR . DS . 'site-editor-panel.class.php';
		require_once SED_INC_EDITOR_DIR . DS . 'site-editor-control.class.php';

		if( is_site_editor() || site_editor_app_on() || is_sed_save() || (isset( $_POST['sed_app_editor'] ) && $_POST['sed_app_editor'] == "on") ) {
			require_once SED_INC_EDITOR_DIR . DS . 'site-editor-selective-refresh.class.php';
			$this->selective_refresh = new SiteEditorSelectiveRefresh($this);

			add_filter('wp_die_handler', array($this, 'wp_die_handler'));

			add_action('wp_footer', array($this, 'wp_styles_loaded'), 10000);
			add_action('wp_footer', array($this, 'wp_scripts_loaded'), 10000);

			add_action('setup_theme', array($this, 'setup_theme'));
			add_action('wp_loaded', array($this, 'wp_loaded'));

			add_action('wp_redirect_status', array($this, 'wp_redirect_status'), 1000);

			// Do not spawn cron (especially the alternate cron) while running the Customizer.
			remove_action('init', 'wp_cron');

			// Do not run update checks when rendering the controls.
			remove_action('admin_init', '_maybe_update_core');
			remove_action('admin_init', '_maybe_update_plugins');
			remove_action('admin_init', '_maybe_update_themes');

			add_action('wp_ajax_sed_app_refresh_nonces', array($this, 'refresh_nonces'));

			add_action('customize_register', array($this, 'register_dynamic_settings'), 11); // allow code to create settings first
			add_action('sed_controls_enqueue_scripts', array($this, 'enqueue_control_scripts'));

		}

		$this->assets_urls = array(
			'base'=> array(
				'css'     => esc_url_raw( SED_ASSETS_URL . '/css' ) ,
				'fonts'   => esc_url_raw( SED_ASSETS_URL . '/fonts' ) ,
				'images'  => esc_url_raw( SED_ASSETS_URL . '/images' ) ,
				'js'      => esc_url_raw( SED_ASSETS_URL . '/js' )
			) ,

			'editor'=> array(
				'css'     => esc_url_raw( SED_EDITOR_ASSETS_URL . '/css' ) ,
				'fonts'   => esc_url_raw( SED_EDITOR_ASSETS_URL . '/fonts' ) ,
				'images'  => esc_url_raw( SED_EDITOR_ASSETS_URL . '/images' ) ,
				'js'      => esc_url_raw( SED_EDITOR_ASSETS_URL . '/js' )  ,
				'libs'    => esc_url_raw( SED_EDITOR_ASSETS_URL . '/libs' )
			) ,

			'framework' => array(
				'css'     => esc_url_raw( SED_FRAMEWORK_ASSETS_URL . '/css' ) ,
				'fonts'   => esc_url_raw( SED_FRAMEWORK_ASSETS_URL . '/fonts' ) ,
				'images'  => esc_url_raw( SED_FRAMEWORK_ASSETS_URL . '/images' ) ,
				'js'      => esc_url_raw( SED_FRAMEWORK_ASSETS_URL . '/js' )
			)
		);

		/*
		ini_set('xdebug.var_display_max_children',1000 );
		ini_set('xdebug.var_display_max_depth',20 );
		ini_set('xdebug.var_display_max_data' , 100000 );
		var_dump( wp_unslash( $_POST['sed_page_customized'] ) );
		var_dump( json_decode( wp_unslash( $_POST['sed_page_customized'] ), true ) );

		$constants = get_defined_constants(true);
		$json_errors = array();
		foreach ($constants["json"] as $name => $value) {
			if (!strncmp($name, "JSON_ERROR_", 11)) {
				$json_errors[$value] = $name;
			}
		}

		var_dump( $json_errors[json_last_error()] );
		*/

        add_action( 'sed_app_register' ,  array( $this, 'register_settings' ) );

        if( site_editor_app_on() ){
            //if( !class_exists('SEDAjaxLess') )
                //require_once SED_PLUGIN_DIR . DS . 'framework' . DS . 'SEDAjaxLess' . DS  . 'SEDAjaxLess.php';

            //new SEDAjaxLess();
        }

        if( is_site_editor() ){
            add_action( "init" , array(&$this, 'editor_init') );
        }

        $this->wp_theme = wp_get_theme( isset( $_REQUEST['theme'] ) ? $_REQUEST['theme'] : null );

		add_action("wp_footer" , array( $this , 'page_settings' ) );
    }

	function page_settings( ){
		global $sed_apps;
		$info_u = $sed_apps->framework->get_sed_page_info_uniqe();
		$sed_page_id = $info_u['id'];
		$sed_page_type = $info_u['type'];
		?>
		<script>
			var _sedAppCurrentPageInfo = {
				id          : "<?php echo $sed_page_id; ?>"  ,
				type        : "<?php echo $sed_page_type; ?>" ,
				isHome      : <?php if( is_home() ) echo 'true'; else echo 'false'; ?> ,
				isFrontPage : <?php if( is_front_page() ) echo 'true'; else echo 'false'; ?>
			};
		</script>
		<?php
	}

    //only for siteeditor
    function get_page_editor_info(){

        $sed_page_id    =  (isset($_REQUEST['sed_page_id']) && !empty($_REQUEST['sed_page_id'])) ? $_REQUEST['sed_page_id'] : "";
        $sed_page_type  =  (isset($_REQUEST['sed_page_type']) && !empty($_REQUEST['sed_page_type'])) ? $_REQUEST['sed_page_type'] : "";

        if( empty( $_REQUEST['sed_page_id'] ) || empty( $_REQUEST['sed_page_type'] ) ) {

            $page_id = get_option( 'page_on_front' );

            if( get_option( 'show_on_front' ) == "page" && $page_id !== false && $page_id > 0 ){
                $sed_page_id = $page_id;
                $sed_page_type = "post";
            }else{
                $sed_page_id = "general_home";
                $sed_page_type = "general";
            }

        }

        return array( "id" => $sed_page_id, "type" => $sed_page_type);
    }

    //for fix bug auto-draft post open in siteeditor
    function editor_init(){
        $info = $this->get_page_editor_info();

        if( $info['type'] == "post" ) {
            $post = get_post( $info['id'] );

            if ( $post && 'auto-draft' === $post->post_status ) {

                $post_data = array(
                    'ID'            => $post->ID,
                    'post_status'   => 'draft',
                    'post_title'    => '',
                );

                add_filter('wp_insert_post_empty_content', array( $this , 'allow_insert_empty_post' ));

                wp_update_post($post_data, true);

            }

        }
    }

    /**
     * Refresh nonces for the current preview.
     *
     * @since 4.2.0
     */
    public function refresh_nonces() {
        if ( ! $this->is_preview() ) {
            wp_send_json_error( 'not_preview' );
        }

        wp_send_json_success( $this->get_nonces() );
    }

    /**
     * Get nonces for the Customizer.
     *
     * @since 4.5.0
     * @return array Nonces.
     */
    public function get_nonces() {
        $nonces = array(
			'save'    => wp_create_nonce( 'sed_app_save_' . $this->get_stylesheet() ),
			'preview' => wp_create_nonce( 'sed_app_preview_' . $this->get_stylesheet() ),
        );

        /**
         * Filter nonces for Customizer.
         *
         * @since 4.2.0
         *
         * @param array                $nonces Array of refreshed nonces for save and
         *                                     preview actions.
         * @param WP_Customize_Manager $this   WP_Customize_Manager instance.
         */
        $nonces = apply_filters( 'sed_app_refresh_nonces', $nonces, $this );

        return $nonces;
    }

    /**
     * Used for wp filter 'wp_insert_post_empty_content' to allow empty post insertion.
     *
     * @param $allow_empty
     *
     * @return bool
     */
    public function allow_insert_empty_post( $allow_empty ) {
        return false;
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
	 * Register any dynamically-created settings, such as those from $_POST['customized']
	 * that have no corresponding setting created.
	 *
	 * This is a mechanism to "wake up" settings that have been dynamically created
	 * on the front end and have been sent to WordPress in `$_POST['customized']`. When WP
	 * loads, the dynamically-created settings then will get created and previewed
	 * even though they are not directly created statically with code.
	 *
	 * @since 4.2.0
	 * @access public
	 *
	 * @param array $setting_ids The setting IDs to add.
	 * @return array The WP_Customize_Setting objects added.
	 */
	public function add_dynamic_settings( $setting_ids ) {
		$new_settings = array();
		foreach ( $setting_ids as $setting_id ) {
			// Skip settings already created
			if ( $this->get_setting( $setting_id ) ) {
				continue;
			}

			$setting_args = false;
			$setting_class = 'SedAppSettings';

			/**
			 * Filter a dynamic setting's constructor args.
			 *
			 * For a dynamic setting to be registered, this filter must be employed
			 * to override the default false value with an array of args to pass to
			 * the WP_Customize_Setting constructor.
			 *
			 * @since 4.2.0
			 *
			 * @param false|array $setting_args The arguments to the WP_Customize_Setting constructor.
			 * @param string      $setting_id   ID for dynamic setting, usually coming from `$_POST['customized']`.
			 */
			$setting_args = apply_filters( 'customize_dynamic_setting_args', $setting_args, $setting_id );
			if ( false === $setting_args ) {
				continue;
			}

			/**
			 * Allow non-statically created settings to be constructed with custom WP_Customize_Setting subclass.
			 *
			 * @since 4.2.0
			 *
			 * @param string $setting_class WP_Customize_Setting or a subclass.
			 * @param string $setting_id    ID for dynamic setting, usually coming from `$_POST['customized']`.
			 * @param array  $setting_args  WP_Customize_Setting or a subclass.
			 */
			$setting_class = apply_filters( 'customize_dynamic_setting_class', $setting_class, $setting_id, $setting_args );

			$setting = new $setting_class( $this, $setting_id, $setting_args );

			$this->add_setting( $setting );
			$new_settings[] = $setting;
		}
		return $new_settings;
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

		if ( is_site_editor() && ! is_user_logged_in() )
		    auth_redirect();

		if ( $this->doing_ajax() && ! is_user_logged_in() ){
		    $this->sed_die( 0 );
        }
		show_admin_bar( false );

		if( !current_user_can( 'edit_theme_options' ) ) {
            $this->sed_die(-1);
        }

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

		do_action( 'sed_shortcode_register' );

		do_action( 'sed_module_register' );

        do_action( 'sed_app_register', $this );
		
		if ( $this->is_preview() && site_editor_app_on()  )
			$this->sed_app_preview_init();

    }

	/**
	 * Return the AJAX wp_die() handler if it's a customized request.
	 *
	 * @since 3.4.0
	 *
	 * @return string
	 */
	public function wp_die_handler() {
		if ( $this->doing_ajax() || isset( $_POST['sed_page_customized'] ) ) {
			return '_ajax_wp_die_handler';
		}

		return '_default_wp_die_handler';
	}

	/**
	 * Print javascript settings.
	 *
	 * @since 3.4.0
	 */
	public function sed_app_preview_init() {

		$this->nonce_tick = check_ajax_referer( 'sed_app_preview_' . $this->get_stylesheet(), 'nonce' );

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

        var_dump( $settings );

        $sed_addon_settings = $site_editor_app->addon_settings();

        $sed_js_I18n = $site_editor_app->js_I18n();

		?>

		<script type="text/javascript">
                var SED_PB_MODULES_URL = "<?php echo SED_PB_MODULES_URL?>";
                var SED_UPLOAD_URL = "<?php echo site_url("/wp-content/uploads/site-editor/");?>";
                var SED_BASE_URL = "<?php echo SED_EDITOR_FOLDER_URL;?>";
				var SED_SITE_URL = "<?php echo site_url();?>";
				var SEDNOPIC = {url : "<?php echo SED_ASSETS_URL . "/images/no_pic.png";?>"};
                var IS_SSL = <?php if( is_ssl() ) echo "true";else echo "false";?>;
				var IS_RTL = <?php if( is_rtl() ) echo "true";else echo "false";?>;
                var SEDAJAX = {url : "<?php echo SED_EDITOR_FOLDER_URL;?>includes/ajax/site_editor_ajax.php"};
				var _sedAssetsUrls = <?php echo wp_json_encode( $this->assets_urls ); ?>;
		        var _sedAppEditorSettings = <?php echo wp_json_encode( $settings ); ?>;
                //var _sedAppPageBuilderModulesScripts = <?php //echo wp_json_encode( $site_editor_app->pagebuilder->modules_scripts ); ?>;
                //var _sedAppPageBuilderModulesStyles = <?php //echo wp_json_encode( $site_editor_app->pagebuilder->modules_styles ); ?>;
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
            $sed_post_id        = get_option( 'page_for_posts' );
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
	 * Return true if it's an AJAX request.
	 *
	 * @since 3.4.0
	 *
	 * @return bool
	 */
	public function doing_ajax() {
		return isset( $_POST['sed_page_customized'] ) || ( defined( 'DOING_SITE_EDITOR_AJAX' ) && DOING_SITE_EDITOR_AJAX );
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
        remove_action( 'shutdown', array( $this, 'sed_app_preview_signature' ), 1000 );

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

            if ( isset( $_POST['sed_page_customized'] ) ){

                $_post_values = json_decode( wp_unslash( $_POST['sed_page_customized'] ), true );

                $this->_post_values = $_post_values;

			}else{

				$this->_post_values = array();
            }
		}

		if ( isset( $this->_post_values[ $setting->id ] ) )
			return $setting->sanitize( $this->_post_values[ $setting->id ] );

	}

    public function base_value( $setting , $sed_page_id ) {

        if ( ! isset( $this->_base_values ) ) {

            //set base settings like page_layout , include all settings with base option_type
            if ( isset( $_POST['sed_page_base_settings'] ) ){

                $_base_values = json_decode( wp_unslash( $_POST['sed_page_base_settings'] ), true );

                $this->_base_values = apply_filters( "sed_current_page_options" , $_base_values );

            }else{

                $this->_base_values = array();
            }
        }

        if ( isset( $this->_base_values[ $sed_page_id ] ) && isset( $this->_base_values[ $sed_page_id ][ $setting->id ] ) )
            return $setting->sanitize( $this->_base_values[ $sed_page_id ][ $setting->id ] );

    }
                                  //$pagebuilder
    function register_settings( ){

        //for typography
        $this->add_setting( 'page_mce_used_fonts', array(
			'default'        => array() ,
			'option_type'    => 'base' ,
            'transport'      => 'postMessage'
		) );

        $this->add_setting( new SedThemeContentSetting( $this, 'theme_content', array(
				 'default'       => false,
				 'option_type'    => 'base' ,
				 'capability'     => 'manage_options',
				 'transport'     => 'postMessage'
			   )
			)
		);

    }


	/**
	 * Add settings from the POST data that were not added with code, e.g. dynamically-created settings for Widgets
	 *
	 * @since 4.2.0
	 * @access public
	 *
	 * @see add_dynamic_settings()
	 */
	public function register_dynamic_settings() {
		$this->add_dynamic_settings( array_keys( $this->unsanitized_post_values() ) );
	}

	/**
	 * Enqueue scripts for customize controls.
	 *
	 * @since 3.4.0
	 */
	public function enqueue_control_scripts() {
		foreach ( $this->controls as $control ) {
			$control->enqueue();
		}
	}

    //only for site editor( in top and iframe )
    function sed_page_settings(){

        if ( ! isset( $this->_page_settings ) ) {

            if( site_editor_app_on() ){
                global $sed_apps;
                $sed_page_id    = $sed_apps->framework->sed_page_id;
                $sed_page_type  = $sed_apps->framework->sed_page_type;
            }else if( is_site_editor() ){
                $info = $this->get_page_editor_info();
                $sed_page_id    = $info['id'];
                $sed_page_type  = $info['type'];
            }
                                               //var_dump( "sed_page_id ------ : " , $sed_page_id );
            $sed_settings = sed_get_page_options( $sed_page_id , $sed_page_type );

            return $this->_page_settings = $sed_settings;
        }else
            return $this->_page_settings;

    }

}

