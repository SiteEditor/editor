<?php
/**
 * SiteEditor Customize Manager classes
 * Thanks From Wordpress Customizer
 * @package SiteEditor
 * @subpackage Customize
 * @since 1.0.0
 */

/**
 * SiteEditor Customize Manager class.
 *
 * Bootstraps the Customize experience on the server-side.
 *
 * Sets up the theme-switching process if a theme other than the active one is
 * being previewed and customized.
 *
 * Serves as a factory for Customize Controls and Settings, and
 * instantiates default Customize Controls and Settings.
 *
 * @since 1.0.0
 */
class SiteEditorManager{

	/**
	 * An instance of the theme being previewed.
	 *
	 * @since 3.4.0
	 * @access protected
	 * @var WP_Theme
	 */
	protected $theme;

	/**
	 * The directory name of the previously active theme (within the theme_root).
	 *
	 * @since 3.4.0
	 * @access protected
	 * @var string
	 */
	protected $original_stylesheet;

	/**
	 * Whether this is a Customizer pageload.
	 *
	 * @since 3.4.0
	 * @access protected
	 * @var bool
	 */
    protected $previewing = false;

	/**
	 * Methods and properties dealing with selective refresh in the Customizer preview.
	 *
	 * @since 4.5.0
	 * @access public
	 * @var WP_Customize_Selective_Refresh
	 */
	public $selective_refresh;

	/**
	 * Registered instances of WP_Customize_Setting.
	 *
	 * @since 3.4.0
	 * @access protected
	 * @var array
	 */
    protected $settings   = array();

	/**
	 * Registered instances of SiteEditorOptionsGroup.
	 *
	 * @since 4.0.0
	 * @access protected
	 * @var array
	 */
	protected $groups = array();

    /**
	 * Registered static modules instances of SiteEditorStaticModule.
	 *
	 * @since 4.0.0
	 * @access protected
	 * @var array
	 */
	protected $static_modules = array();

	/**
	 * Registered instances of WP_Customize_Panel.
	 *
	 * @since 4.0.0
	 * @access protected
	 * @var array
	 */
	protected $panels = array();

	/**
	 * List of core components.
	 *
	 * @since 4.5.0
	 * @access protected
	 * @var array
	 */
	protected $components = array( );

	/**
	 * Registered instances of WP_Customize_Control.
	 *
	 * @since 3.4.0
	 * @access protected
	 * @var array
	 */
	protected $controls = array();

	/**
	 * Return value of check_ajax_referer() in customize_preview_init() method.
	 *
	 * @since 3.5.0
	 * @access protected
	 * @var false|int
	 */
	protected $nonce_tick;

	/**
	 * Panel types that may be rendered from JS templates.
	 *
	 * @since 4.3.0
	 * @access protected
	 * @var array
	 */
	protected $registered_panel_types = array();

	/**
	 * Control types that may be rendered from JS templates.
	 *
	 * @since 4.1.0
	 * @access protected
	 * @var array
	 */
	protected $registered_control_types = array();

	/**
	 * Group types that may be rendered from JS templates.
	 *
	 * @since 4.1.0
	 * @access protected
	 * @var array
	 */
	protected $registered_group_types = array();

	/**
	 * Initial URL being previewed.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var string
	 */
	protected $preview_url;

	/**
	 * URL to link the user to when closing the Customizer.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var string
	 */
	protected $return_url;

	/**
	 * Unsanitized values for Customize Settings parsed from $_POST['sed_page_customized'].
	 *
	 * @var array
	 */
	private $_post_values;

    /**
     * all pre load settings for current page
     *
     * @since 1.0.0
     * @access public
     * @var array
     */
    public $preload_settings = array();

    /**
     * SiteEditorManager constructor.
     */
    function __construct(  ) {

		require_once SED_INC_EDITOR_DIR . DS . 'site-editor-options-group.class.php';
		require_once SED_INC_EDITOR_DIR . DS . 'site-editor-setting.class.php';
		require_once SED_INC_EDITOR_DIR . DS . 'site-editor-panel.class.php';
		require_once SED_INC_EDITOR_DIR . DS . 'site-editor-control.class.php';

		do_action( 'sed_app_register_components' , $this );
        /**
         * Filter the core Customizer components to load.
         *
         * This allows Core components to be excluded from being instantiated by
         * filtering them out of the array. Note that this filter generally runs
         * during the {@see 'plugins_loaded'} action, so it cannot be added
         * in a theme.
         *
         * @since 4.4.0
         *
         * @see WP_Customize_Manager::__construct()
         *
         * @param array                $components List of core components to load.
         * @param WP_Customize_Manager $this       WP_Customize_Manager instance.
         */
        $components = apply_filters( 'sed_app_loaded_components', $this->components, $this );

        require_once SED_INC_EDITOR_DIR . DS . 'site-editor-selective-refresh.class.php';
        $this->selective_refresh = new SiteEditorSelectiveRefresh($this);

        add_filter('wp_die_handler' , array($this, 'wp_die_handler'));

        add_action('wp_footer'  , array($this, 'wp_styles_loaded'), 10000);
        add_action('wp_footer'  , array($this, 'wp_scripts_loaded'), 10000);

        add_action('setup_theme'    , array($this, 'setup_theme'));
        add_action('wp_loaded'      , array($this, 'wp_loaded'));

        add_action('wp_redirect_status', array($this, 'wp_redirect_status'), 1000);

        // Do not spawn cron (especially the alternate cron) while running the Customizer.
        remove_action('init', 'wp_cron');

        // Do not run update checks when rendering the controls.
        remove_action('admin_init'  , '_maybe_update_core');
        remove_action('admin_init'  , '_maybe_update_plugins');
        remove_action('admin_init'  , '_maybe_update_themes');

        add_action('wp_ajax_sed_app_refresh_nonces' , array($this, 'refresh_nonces'));

        add_action( 'sed_app_register'                  ,  array( $this, 'register_settings' ) );
        add_action( 'sed_app_register'                  ,  array($this, 'register_dynamic_settings'), 11); // allow code to create settings first
        add_action( 'sed_print_footer_scripts'      	,  array($this, 'enqueue_control_scripts') );

		// Render Panel, Group, and Control templates.
		add_action( 'sed_print_footer_scripts', array( $this, 'render_panel_templates' ), 1 );
		add_action( 'sed_print_footer_scripts', array( $this, 'render_group_templates' ), 1 );
		add_action( 'sed_print_footer_scripts', array( $this, 'render_control_templates' ), 1 );

		// Export the settings to JS via the _sedAppEditorSettings variable.
		add_action( 'sed_print_footer_scripts', array( $this, 'sed_app_pane_settings' ), 1000 );

        if( is_site_editor() ){
            add_action( "init" , array(&$this, 'editor_init') );
        }

        $this->wp_theme = wp_get_theme( isset( $_REQUEST['theme'] ) ? $_REQUEST['theme'] : null );

        add_action("wp_footer" , array( $this , 'page_settings' ) );

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

        if( site_editor_app_on() ){
            //if( !class_exists('SEDAjaxLess') )
                //require_once SED_PLUGIN_DIR . DS . 'framework' . DS . 'SEDAjaxLess' . DS  . 'SEDAjaxLess.php';

            //new SEDAjaxLess();
        }

        add_action( "sed_footer" , array($this, 'print_options_template') , 10000 );

    }

	function page_settings( ){
		global $sed_apps;
		$info_u = $sed_apps->framework->get_sed_page_info_uniqe();
		$sed_page_id = $info_u['id'];
		$sed_page_type = $info_u['type'];

		if( $sed_page_type == "post" ){	
			$post = get_post( $sed_page_id );
			$post_type = $post->post_type;
		}else{
			$post_type = '';
		}

		?>
		<script>
			var _sedAppCurrentPageInfo = {
				id          : "<?php echo $sed_page_id; ?>"  ,
				type        : "<?php echo $sed_page_type; ?>" ,
				post_type   : "<?php echo $post_type; ?>" ,
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
         * @param SiteEditorManager $this   SiteEditorManager instance.
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
		 * @param SiteEditorManager $this SiteEditorManager instance.
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
	public function is_theme_active() {
		return $this->get_stylesheet() == $this->original_stylesheet;
	}

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

        if ( $id instanceof SedAppSettings ) {
            $setting = $id;
        } else {
            $class = 'SedAppSettings';

            $args = apply_filters( 'sed_app_dynamic_setting_args', $args, $id );

            $class = apply_filters( 'sed_app_dynamic_setting_class', $class, $id, $args );

            $setting = new $class( $this, $id, $args );
        }

        $this->settings[ $setting->id ] = $setting;

        return $setting;

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
			 * the SedAppSettings constructor.
			 *
			 *
			 * @param false|array $setting_args The arguments to the WP_Customize_Setting constructor.
			 * @param string      $setting_id   ID for dynamic setting, usually coming from `$_POST['customized']`.
			 */
			$setting_args = apply_filters( 'sed_app_dynamic_setting_args', $setting_args, $setting_id );
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
			$setting_class = apply_filters( 'sed_app_dynamic_setting_class', $setting_class, $setting_id, $setting_args );

			$setting = new $setting_class( $this, $setting_id, $setting_args );

			$this->add_setting( $setting );
			$new_settings[] = $setting;
		}
		return $new_settings;
	}

	/**
	 * Retrieve a pre load settings for current page
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings.
	 */
	public function add_preload_settings( $settings ) {

		if( is_array( $settings ) )
			$this->preload_settings = array_merge( $this->preload_settings , $settings );

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
	 * Add a options control.
	 *
	 * @since 3.4.0
	 *
	 * @param SiteEditorOptionsControl|string $id   Customize Control object, or ID.
	 * @param array                       $args Control arguments; passed to WP_Customize_Control
	 *                                          constructor.
	 * @return object ( instance of SiteEditorOptionsControl or extends )
	 */
	public function add_control( $id, $args = array() ) {

		if ( $id instanceof SiteEditorOptionsControl ) {
			$control = $id;
		} else {
			$control = new SiteEditorOptionsControl( $this, $id, $args );
		}

		$this->controls[ $control->id ] = $control;

		return $control;
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

		if ( isset( $this->controls[ $id ] ) ) {
			return $this->controls[$id];
		}

	}

	/**
	 * Remove a customize control.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id ID of the control.
	 */
	public function remove_control( $id ) {

		if ( isset( $this->controls[ $id ] ) ) {
			unset($this->controls[$id]);
		}
	}

    /**
     * Register a customize control type.
     *
     * Registered types are eligible to be rendered via JS and created dynamically.
     *
     * @since 4.1.0
     * @access public
     *
     * @param string $control Name of a custom control which is a subclass of
     *                        {@see WP_Customize_Control}.
     */
    public function register_control_type( $control ) {
        $this->registered_control_types[] = $control;
    }

    /**
     * Render JS templates for all registered control types.
     *
     * @since 4.1.0
     * @access public
     */
    public function render_control_templates() {
        foreach ( $this->registered_control_types as $control_type ) {
            $control = new $control_type( $this, 'temp', array(
                'settings' => array(),
            ) );
            $control->print_template();
        }
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

	/**
	 * Get the registered controls.
	 *
	 * @since 3.4.0
	 *
	 * @return array
	 */
	public function static_modules() {
		return $this->static_modules;
	}

	/**
	 * Add a static module.
	 *
	 * @since 1.0.0
	 *
	 * @param SiteEditorStaticModule|string $id   Customize static module object, or ID.
	 * @param array                       $args Control arguments; passed to SiteEditorStaticModule
	 *                                          constructor.
	 * @return object ( instance of SiteEditorStaticModule or extends )
	 */
	public function add_static_module( $id, $args = array() ) {

		if ( $id instanceof SiteEditorStaticModule ) {
			$module = $id;
		} else {
			$module = new SiteEditorStaticModule( $this, $id, $args );
		}

		$this->static_modules[ $module->id ] = $module;

		return $module;
	}

	/**
	 * Retrieve a static module.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id ID of the control.
	 * @return SiteEditorStaticModule $static_module The static module object.
	 */
	public function get_static_module( $id ) {

		if ( isset( $this->static_modules[ $id ] ) ) {
			return $this->static_modules[$id];
		}

	}

	/**
	 * Remove a customize control.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id ID of the control.
	 */
	public function remove_static_module( $id ) {

		if ( isset( $this->static_modules[ $id ] ) ) {
			unset($this->static_modules[$id]);
		}
	}

	/**
	 * Get the registered controls.
	 *
	 * @since 3.4.0
	 *
	 * @return array
	 */
	public function groups() {
		return $this->groups;
	}

	/**
	 * Add a options control.
	 *
	 * @since 3.4.0
	 *
	 * @param SiteEditorOptionsControl|string $id   Customize Control object, or ID.
	 * @param array                       $args Control arguments; passed to WP_Customize_Control
	 *                                          constructor.
	 * @return object ( instance of SiteEditorOptionsControl or extends )
	 */
	public function add_group( $id, $args = array() ) {

		if ( $id instanceof SiteEditorOptionsGroup ) {
			$group = $id;
		} else {
			$group = new SiteEditorOptionsGroup( $this, $id, $args );
		}

		$this->groups[ $group->id ] = $group;

		return $group;
	}

	/**
	 * Retrieve a customize control.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id ID of the control.
	 * @return WP_Customize_Control $control The control object.
	 */
	public function get_group( $id ) {

		if ( isset( $this->groups[ $id ] ) ) {
			return $this->groups[$id];
		}

	}

	/**
	 * Remove a customize control.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id ID of the control.
	 */
	public function remove_group( $id ) {

		if ( isset( $this->groups[ $id ] ) ) {
			unset($this->groups[$id]);
		}
	}

	/**
	 * Register a customize control type.
	 *
	 * Registered types are eligible to be rendered via JS and created dynamically.
	 *
	 * @since 4.1.0
	 * @access public
	 *
	 * @param string $control Name of a custom control which is a subclass of
	 *                        {@see WP_Customize_Control}.
	 */
	public function register_group_type( $group ) {
		$this->registered_group_types[] = $group;
	}

	/**
	 * Render JS templates for all registered control types.
	 *
	 * @since 4.1.0
	 * @access public
	 */
	public function render_group_templates() {
		foreach ( $this->registered_group_types as $group_type ) {
			$group = new $group_type( $this, 'temp', array(
				'settings' => array(),
			) );
			$group->print_template();
		}
	}

	/**
	 * Get the registered panels.
	 *
	 * @since 3.4.0
	 *
	 * @return array
	 */
	public function panels() {
		return $this->panels;
	}
	
	/**
	 * Add a customize panel.
	 *
	 * @since 4.0.0
	 * @since 4.5.0 Return added WP_Customize_Panel instance.
	 * @access public
	 *
	 * @param WP_Customize_Panel|string $id   Customize Panel object, or Panel ID.
	 * @param array                     $args Optional. Panel arguments. Default empty array.
	 *
	 * @return WP_Customize_Panel             The instance of the panel that was added.
	 */
	public function add_panel( $id, $args = array() ) {

		if ( $id instanceof SiteEditorOptionsPanel ) {
			$panel = $id;
		} else {
			$panel = new SiteEditorOptionsPanel( $this, $id, $args );
		}

		$panel->priority = absint( $panel->priority );

		$this->panels[ $panel->id ] = $panel;

		return $panel;

	}


	/**
	 * Retrieve a customize panel.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $id Panel ID to get.
	 * @return WP_Customize_Panel|void Requested panel instance, if set.
	 */
	public function get_panel( $id ) {
		if ( isset( $this->panels[ $id ] ) ) {
			return $this->panels[ $id ];
		}
	}

	/**
	 * Remove a customize panel.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $id Panel ID to remove.
	 */
	public function remove_panel( $id ) {
		// Removing core components this way is _doing_it_wrong().
		if ( in_array( $id, SED()->editor->manager->components, true ) ) {
			/* translators: 1: panel id, 2: link to 'customize_loaded_components' filter reference */
			$message = sprintf( __( 'Removing %1$s manually will cause PHP warnings. Use the %2$s filter instead.' ),
				$id,
				'<a href="' . esc_url( 'https://developer.wordpress.org/reference/hooks/customize_loaded_components/' ) . '"><code>customize_loaded_components</code></a>'
			);

			_doing_it_wrong( __METHOD__, $message, '4.5' );
		}
		unset( $this->panels[ $id ] );
	}

	/**
	 * Register a customize panel type.
	 *
	 * Registered types are eligible to be rendered via JS and created dynamically.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @see WP_Customize_Panel
	 *
	 * @param string $panel Name of a custom panel which is a subclass of WP_Customize_Panel.
	 */
	public function register_panel_type( $panel ) {
		$this->registered_panel_types[] = $panel;
	}


	/**
	 * Render JS templates for all registered panel types.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function render_panel_templates() {
		foreach ( $this->registered_panel_types as $panel_type ) {
			$panel = new $panel_type( $this, 'temp', array() );
			$panel->print_template();
		}
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

		if ( sed_doing_ajax() && ! is_user_logged_in() ){
		    $this->sed_die( 0 );
        }
		show_admin_bar( false );

		if( !current_user_can( 'edit_theme_options' ) ) {
            $this->sed_die(-1);
        }

		$this->original_stylesheet = get_stylesheet();

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

        do_action( 'sed_static_module_register', $this );

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
		if ( sed_doing_ajax() ) {
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
			'theme' => array(
				'stylesheet' => $this->get_stylesheet(),
				'active'     => $this->is_theme_active(),
			),
			'url' => array(
				'self' => empty( $_SERVER['REQUEST_URI'] ) ? home_url( '/' ) : esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ),
			),
            'types'   => array(),
			'channel' => wp_unslash( $_POST['customize_messenger_channel'] ),
			'nonce' => $this->get_nonces(),
            'post'    => array(
                'id'     =>   0
            ) ,
			'_dirty' => array_keys( $this->unsanitized_post_values() ),
		);


		foreach ( $this->settings as $id => $setting ) {

		    if( !empty( $setting->type ) )
                $stype = $setting->type;
            else
                $stype = "general";

			$settings['types'][ $id ] = $stype;

		}


		?>
		<script type="text/javascript">
			var _sedAppEditorSettings = <?php echo wp_json_encode( $settings ); ?>;
			_sedAppEditorSettings.values = {};
			_sedAppEditorSettings.staticModules = {};
            _sedAppEditorSettings.preLoadSettings = {};

			(function( v ) {
				<?php
				/*
				 * Serialize settings separately from the initial _sedAppEditorSettings
				 * serialization in order to avoid a peak memory usage spike.
				 * @todo We may not even need to export the values at all since the pane syncs them anyway.
				 */
				foreach ( $this->settings as $id => $setting ) {
					if ( $setting->check_capabilities() ) {
						printf(
							"v[%s] = %s;\n",
							wp_json_encode( $id ),
							wp_json_encode( $setting->js_value() )
						);
					}
				}
				?>
			})( _sedAppEditorSettings.values );

            (function( pS ) {
                <?php
                /*
                 * Serialize settings separately from the initial _sedAppEditorSettings
                 * serialization in order to avoid a peak memory usage spike.
                 * @todo We may not even need to export the values at all since the pane syncs them anyway.
                 */
                foreach ( $this->preload_settings as $id ) {

                    $setting = $this->get_setting( $id );

                    if ( isset( $setting ) && $setting->check_capabilities() ) {
                        printf(
                            "pS[%s] = %s;\n",
                            wp_json_encode( $id ),
                            wp_json_encode( array(
                                'value'     	=> $setting->js_value(),
                                'transport' 	=> $setting->transport,
                                'dirty'     	=> $setting->dirty,
                                'type'          => $setting->type ,
                                'option_type'   => $setting->option_type ,
                            ) )
                        );
                    }
                }
                ?>
            })( _sedAppEditorSettings.preLoadSettings );

            (function( sM ) {
                <?php
                /*
                 * Serialize settings separately from the initial _sedAppEditorSettings
                 * serialization in order to avoid a peak memory usage spike.
                 * @todo We may not even need to export the values at all since the pane syncs them anyway.
                 */
                foreach ( $this->static_modules() as $id => $module ) {
                    if ( $module->check_capabilities() && $module->active() ) {
                        printf(
                            "sM[%s] = %s;\n",
                            wp_json_encode( $id ),
                            wp_json_encode( $module->json() )
                        );
                    }
                }
                ?>
            })( _sedAppEditorSettings.staticModules );
		</script>
		<?php

        $sed_addon_settings = $site_editor_app->addon_settings();

        $sed_js_I18n = $site_editor_app->js_I18n();

		$wp_upload = wp_upload_dir();

		$sed_upload_url = $wp_upload['baseurl'] . "/site-editor/";

		$sed_ajax_url = array(
			'url'   => admin_url( 'admin-ajax.php' )
		);
		
		?>

		<script type="text/javascript">
                var SED_PB_MODULES_URL = "<?php echo SED_PB_MODULES_URL?>";
                var SED_UPLOAD_URL = "<?php echo $sed_upload_url;?>";
                var SED_BASE_URL = "<?php echo SED_EDITOR_FOLDER_URL;?>";
				var SED_SITE_URL = "<?php echo site_url();?>";
				var SEDNOPIC = {url : "<?php echo SED_ASSETS_URL . "/images/no_pic.png";?>"};
                var IS_SSL = <?php if( is_ssl() ) echo "true";else echo "false";?>;
				var IS_RTL = <?php if( is_rtl() ) echo "true";else echo "false";?>;
                var SEDAJAX = <?php echo wp_json_encode( $sed_ajax_url );?>;
				var _sedAssetsUrls = <?php echo wp_json_encode( $this->assets_urls ); ?>;
                //var _sedAppPageBuilderModulesScripts = <?php //echo wp_json_encode( $site_editor_app->pagebuilder->modules_scripts ); ?>;
                //var _sedAppPageBuilderModulesStyles = <?php //echo wp_json_encode( $site_editor_app->pagebuilder->modules_styles ); ?>;
                var _sedAppEditorI18n = <?php echo wp_json_encode( $sed_js_I18n )?>;
                var _sedAppEditorAddOnSettings = <?php echo wp_json_encode( $sed_addon_settings )?>;
                var _sedAppPageContentInfo = <?php echo wp_json_encode( $this->get_page_content_info() )?>;
		</script>

		<!-- Full Iframe Loading -->
		<div id="sed_full_editor_loading">
			<div class="sed-loading-continer">
				<div class="sed-loading"></div>
			</div>
		</div>
		<!-- Full Iframe Loading -->

		<?php

	}

    public function get_page_content_info(){
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
            $info['type']     = "front_page";
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

	function check_ajax_handler($ajax , $nonce , $capability = 'edit_theme_options'){ 

		if ( is_admin() && ! sed_doing_ajax() )
			auth_redirect();
		elseif ( sed_doing_ajax() && ! is_user_logged_in() ){
			$this->sed_die( 0 );
		}

		if ( ! current_user_can( $capability ) )
			$this->sed_die( -1 );

		if( !check_ajax_referer( $nonce . '_' . $this->get_stylesheet(), 'nonce' , false ) ){
			$this->sed_die( -1 );
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
	 * Parse the incoming $_POST['sed_page_customized'] JSON data and store the unsanitized
	 * settings for subsequent post_value() lookups.
	 *
	 * @since 4.1.1
	 *
	 * @return array
	 */
	public function unsanitized_post_values() {
		if ( ! isset( $this->_post_values ) ) {
			if ( isset( $_POST['sed_page_customized'] ) ) {
				$this->_post_values = json_decode( wp_unslash( $_POST['sed_page_customized'] ), true );
			}
			if ( empty( $this->_post_values ) ) { // if not isset or if JSON error
				$this->_post_values = array();
			}
		}
		if ( empty( $this->_post_values ) ) {
			return array();
		} else {
			return $this->_post_values;
		}
	}

	/**
	 * Return the sanitized value for a given setting from the request's POST data.
	 *
	 * @since 3.4.0
	 * @since 4.1.1 Introduced 'default' parameter.
	 *
	 * @param WP_Customize_Setting $setting A WP_Customize_Setting derived object
	 * @param mixed $default value returned $setting has no post value (added in 4.2.0).
	 * @return string|mixed $post_value Sanitized value or the $default provided
	 */
	public function post_value( $setting, $default = null ) {
		$post_values = $this->unsanitized_post_values();
		if ( array_key_exists( $setting->id, $post_values ) ) {
			return $setting->sanitize( $post_values[ $setting->id ] );
		} else {
			return $default;
		}
	}

	/**
	 * Override a setting's (unsanitized) value as found in any incoming $_POST['customized'].
	 *
	 * @since 4.2.0
	 * @access public
	 *
	 * @param string $setting_id ID for the WP_Customize_Setting instance.
	 * @param mixed  $value      Post value.
	 */
	public function set_post_value( $setting_id, $value ) {
		$this->unsanitized_post_values();
		$this->_post_values[ $setting_id ] = $value;

		/**
		 * Announce when a specific setting's unsanitized post value has been set.
		 *
		 * Fires when the {@see SiteEditorManager::set_post_value()} method is called.
		 *
		 * The dynamic portion of the hook name, `$setting_id`, refers to the setting ID.
		 *
		 * @since 4.4.0
		 *
		 * @param mixed                $value Unsanitized setting post value.
		 * @param SiteEditorManager $this  SiteEditorManager instance.
		 */
		do_action( "sed_app_post_value_set_{$setting_id}", $value, $this );

		/**
		 * Announce when any setting's unsanitized post value has been set.
		 *
		 * Fires when the {@see SiteEditorManager::set_post_value()} method is called.
		 *
		 * This is useful for `WP_Customize_Setting` instances to watch
		 * in order to update a cached previewed value.
		 *
		 * @since 4.4.0
		 *
		 * @param string               $setting_id Setting ID.
		 * @param mixed                $value      Unsanitized setting post value.
		 * @param SiteEditorManager $this       SiteEditorManager instance.
		 */
		do_action( 'sed_app_post_value_set', $setting_id, $value, $this );
	}

    /**
     * Register default settings
     */
    function register_settings( ){

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
	 * Determine whether the user agent is iOS.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return bool Whether the user agent is iOS.
	 */
	public function is_ios() {
		return wp_is_mobile() && preg_match( '/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'] );
	}

	/**
	 * Get the template string for the Customizer pane document title.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return string The template string for the document title.
	 */
	public function get_document_title_template() {
		if ( $this->is_theme_active() ) {
			/* translators: %s: document title from the preview */
			$document_title_tmpl = __( 'Customize: %s' );
		} else {
			/* translators: %s: document title from the preview */
			$document_title_tmpl = __( 'Live Preview: %s' );
		}
		$document_title_tmpl = html_entity_decode( $document_title_tmpl, ENT_QUOTES, 'UTF-8' ); // Because exported to JS and assigned to document.title.
		return $document_title_tmpl;
	}

	/**
	 * Set the initial URL to be previewed.
	 *
	 * URL is validated.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $preview_url URL to be previewed.
	 */
	public function set_preview_url( $preview_url ) {
		$preview_url = esc_url_raw( $preview_url );
		$this->preview_url = wp_validate_redirect( $preview_url, home_url( '/' ) );
	}

	/**
	 * Get the initial URL to be previewed.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return string URL being previewed.
	 */
	public function get_preview_url() {
		if ( empty( $this->preview_url ) ) {
			$preview_url = home_url( '/' );
		} else {
			$preview_url = $this->preview_url;
		}
		return $preview_url;
	}

	/**
	 * Set URL to link the user to when closing the Customizer.
	 *
	 * URL is validated.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $return_url URL for return link.
	 */
	public function set_return_url( $return_url ) {
		$return_url = esc_url_raw( $return_url );
		$return_url = remove_query_arg( wp_removable_query_args(), $return_url );
		$return_url = wp_validate_redirect( $return_url );
		$this->return_url = $return_url;
	}

	/**
	 * Get URL to link the user to when closing the Customizer.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return string URL for link to close Customizer.
	 */
	public function get_return_url() {
		$referer = wp_get_referer();
		$excluded_referer_basenames = array( 'customize.php', 'wp-login.php' ); //?editor=siteeditor

		if ( $this->return_url ) {
			$return_url = $this->return_url;
		} else if ( $referer && ! in_array( basename( parse_url( $referer, PHP_URL_PATH ) ), $excluded_referer_basenames, true ) ) {
			$return_url = $referer;
		} else if ( $this->preview_url ) {
			$return_url = $this->preview_url;
		} else {
			$return_url = home_url( '/' );
		}
		return $return_url;
	}
	
	/**
	 * Print JavaScript settings for parent window.
	 *
	 * @since 4.4.0
	 */
	public function sed_app_pane_settings() {
		/*
		 * If the front end and the admin are served from the same domain, load the
		 * preview over ssl if the Customizer is being loaded over ssl. This avoids
		 * insecure content warnings. This is not attempted if the admin and front end
		 * are on different domains to avoid the case where the front end doesn't have
		 * ssl certs. Domain mapping plugins can allow other urls in these conditions
		 * using the customize_allowed_urls filter.
		 */

		$allowed_urls = array( home_url( '/' ) );
		$admin_origin = parse_url( admin_url() );
		$home_origin  = parse_url( home_url() );
		$cross_domain = ( strtolower( $admin_origin['host'] ) !== strtolower( $home_origin['host'] ) );

		if ( is_ssl() && ! $cross_domain ) {
			$allowed_urls[] = home_url( '/', 'https' );
		}

		/**
		 * Filter the list of URLs allowed to be clicked and followed in the Customizer preview.
		 *
		 * @since 3.4.0
		 *
		 * @param array $allowed_urls An array of allowed URLs.
		 */
		$allowed_urls = array_unique( apply_filters( 'sed_app_allowed_urls', $allowed_urls ) );

		//TODO deprecate this setting
		$fallback_url = add_query_arg( array(
			'preview'        => 1,
			'template'       => $this->get_template(),
			'stylesheet'     => $this->get_stylesheet(),
			'preview_iframe' => true,
			'TB_iframe'      => 'true'
		), home_url( '/' ) );

		$login_url = add_query_arg( array(
			'interim-login' => 1,
			'customize-login' => 1,
		), wp_login_url() );

		$info = $this->get_page_editor_info();

		$sed_page_id    =  $info['id'];
		$sed_page_type  =  $info['type'];

		// Prepare Customizer settings to pass to JavaScript.
		$settings = array(
			'theme'    => array(
				'stylesheet' => $this->get_stylesheet(),
				'active'     => $this->is_theme_active(),
			),
			'page'     => array(
				'id'                    =>  $sed_page_id , //$sed_apps->sed_page_id,
				'type'                  =>  $sed_page_type  //$sed_apps->sed_page_type
			),
			'url'      => array(
				'preview'       => esc_url_raw( $this->get_preview_url() ),
				'parent'        => esc_url_raw( admin_url() ),
				'activated'     => esc_url_raw( home_url( '/' ) ),
				'ajax'          => esc_url_raw( admin_url( 'admin-ajax.php', 'relative' ) ),
				'allowed'       => array_map( 'esc_url_raw', $allowed_urls ),
				'isCrossDomain' => $cross_domain,
				'fallback'      => esc_url_raw( $fallback_url ),
				'home'          => esc_url_raw( home_url( '/' ) ),
				'login'         => esc_url_raw( $login_url ),
			),
			'browser'  => array(
				'mobile' => wp_is_mobile(),
				'ios'    => $this->is_ios(),
				'mobileVersion'     => sed_is_mobile_version() || wp_is_mobile()
			),
			'panels'   => array(),
			'groups'   => array(),
			'nonce'    => $this->get_nonces(),
			'documentTitleTmpl' => $this->get_document_title_template()
		);

		// Prepare Customize Section objects to pass to JavaScript.
		foreach ( $this->groups() as $id => $options_group ) {
			if ( $options_group->check_capabilities() ) {
				$settings['groups'][ $id ] = $options_group->json();
			}
		}

		foreach ( $this->panels() AS $panel_id => $panel ){

			if ( $panel->check_capabilities() ) {

					$group_panels[$panel_id] = $panel;

					$settings['panels'][$panel_id] = $panel->json();

			}

		}

		?>
		<script type="text/javascript">
			var _sedAppEditorSettings = <?php echo wp_json_encode( $settings ); ?>;
			_sedAppEditorSettings.controls = {};
			_sedAppEditorSettings.settings = {};
			<?php

			// Serialize settings one by one to improve memory usage.
			echo "(function ( s ){\n";
			foreach ( $this->settings() as $setting ) {
				if ( $setting->check_capabilities() ) {
					printf(
						"s[%s] = %s;\n",
						wp_json_encode( $setting->id ),
						wp_json_encode( array(
							'value'     	=> $setting->js_value(),
							'transport' 	=> $setting->transport,
							'dirty'     	=> $setting->dirty,
							'type'          => $setting->type ,
							'option_type'   => $setting->option_type ,
						) )
					);
				}
			}
			echo "})( _sedAppEditorSettings.settings );\n";

			// Serialize controls one by one to improve memory usage.
			echo "(function ( c ){\n";
			foreach ( $this->controls() as $id => $control ) {
				if ( $control->check_capabilities() ) {
					printf(
						"c[%s] = %s;\n",
						wp_json_encode( $control->id ), //$control->id
						wp_json_encode( $control->json() ) //$control->json()
					);
				}
			}
			echo "})( _sedAppEditorSettings.controls );\n";
			?>
		</script>
		<?php
	}

    /**
     * Print Settings Templates
     */
    public function print_options_template(){
        ?>

        <div id="sed-dialog-settings" class="sed-dialog" title="">

        </div>

        <?php

        $groups = $this->groups();

        foreach ( $groups AS $group_id => $group ){

            if( $group->check_capabilities() ){

                foreach ( $this->panels() AS $panel_id => $panel ){

                    if ( $panel->check_capabilities() ) {

                        if ( $panel->option_group == $group_id ) {

                            $group->panels[$panel_id] = $panel;

                        }

                    }

                }

                foreach ( $this->controls() AS $control_id => $control ){

                    if ( $control->check_capabilities() ) {

                        if ( $control->option_group == $group_id ) {

                            $group->controls[$control_id] = $control;

                        }

                    }

                }

                ?>
                <script type="text/html"  id="group_settings_<?php echo $group_id;?>_tmpl" >
                    <?php echo $group->get_content();?>
                </script>
                <?php

            }

        }

        $panels = array();

        foreach ( $this->panels() AS $panel_id => $panel ){

            if ( $panel->check_capabilities() ) {

                $panels[$panel_id] = $panel->json();

            }

        }

        ?>

        <script>
            var _sedAppSettingsPanels = <?php if( !empty( $panels ) ) echo wp_json_encode( $panels ); else echo "{}"; ?>;
        </script>
        <?php
    }

}

