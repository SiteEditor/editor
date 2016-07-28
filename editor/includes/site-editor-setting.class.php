<?php
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

    private $_base_value;

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
            case 'base' :

                $_base_values = json_decode( wp_unslash( $_POST['sed_page_base_settings'] ), true );

                if( !empty( $_base_values ) && is_array( $_base_values ) ) {
                    $sed_page_ids = array_keys($_base_values);
                    $is_once = false;

                    foreach ($sed_page_ids AS $sed_page_id) {
                        $is_post = false;

                        if ( preg_match( '/(\d+)/', $sed_page_id , $matches ) && ( $post_id = (int) $matches[1] ) && !is_null( get_post( $post_id ) ) ) {
                            $is_post = true;
                        }

                        if( $is_post === true && $is_once === false ){
                            add_filter('get_post_metadata', array($this, '_preview_base_meta_settings'), 1, 4);
                            $is_once = true;
                        } else if( $is_post !== true && $sed_page_id != 0 ) {
                            $option_name = 'sed_' . $sed_page_id . '_settings';
                            add_filter('option_' . $option_name, array($this, '_preview_base_option_settings'));
                            add_filter('default_option_' . $option_name, array($this, '_preview_base_option_settings'));
                        }

                    }
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

    public function _preview_base_meta_settings( $original_meta_value, $post_id, $meta_key, $single ) {

        if ( isset( $meta_key ) && $meta_key == 'sed_post_settings' ) {

            $value = $this->base_value( $this->default , $post_id );

            if ( ! isset( $value ) ){
                return $original_meta_value;
            }else{
                $meta_value = ( !empty( $original_meta_value ) && is_array( $original_meta_value ) ) ? $original_meta_value : array();

                if( $single )
                    $meta_value[0][ $this->id ] = $value;
                else
                    $meta_value[ $this->id ] = $value;

                return $single ? $meta_value : array( $meta_value );
            }
        }

    }

    public function _preview_base_option_settings( $original ){

        global $sed_apps;
        $sed_page_id = $sed_apps->framework->sed_page_id;
        $value = $this->base_value( $this->default , $sed_page_id );

        if ( ! isset( $value ) ){
            return $original;
        }else{
            $current_page_settings = $original;
            $current_page_settings[ $this->id ] = $value;
            return $current_page_settings;
        }

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

    public final function base_value( $default = null , $sed_page_id ) {
        // Check for a cached value
        if ( isset( $this->_base_value ) )
            return $this->_base_value;

        // Call the manager for the post value
        $result = $this->manager->base_value( $this , $sed_page_id );

        if ( isset( $result ) )
            return $this->_base_value = $result;
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
        /*if( $this->option_type == "base" || empty( $this->option_type ) ){

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

        }*/

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
            case 'base' :
                $sed_settings = $this->manager->sed_page_settings();
                $value = ( isset( $sed_settings[ $this->id ] ) ) ? $sed_settings[ $this->id ] : $this->default;
                return $value;
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

class SedThemeContentSetting extends SedAppSettings{

	public function __construct( $manager, $id, $args = array() ) {

		add_filter( "base_settings_save_filter" , array( $this , "save_theme_content" ) );

		return parent::__construct( $manager, $id, $args );
	}

	public function _preview_base_meta_settings( $original_meta_value, $post_id, $meta_key, $single ) {

		if ( isset( $meta_key ) && $meta_key == 'sed_post_settings' ) {

			$value = $this->base_value( $this->default , $post_id );

			if ( ! isset( $value ) ){
				return $original_meta_value;
			}else{
				$meta_value = ( !empty( $original_meta_value ) && is_array( $original_meta_value ) ) ? $original_meta_value : array();

				if( $value !== false && !empty( $value ) && is_array( $value ) ) {
					foreach ($value AS $key => $model) {
						$value[$key]['content'] = $this->_filter_row_content($model['content']);
					}
				}

				if( $single )
					$meta_value[0][ $this->id ] = $value;
				else
					$meta_value[ $this->id ] = $value;

				return $single ? $meta_value : array( $meta_value );
			}
		}

	}


	public function _preview_base_option_settings( $original ){

		global $sed_apps;
		$sed_page_id = $sed_apps->framework->sed_page_id;
		$value = $this->base_value( $this->default , $sed_page_id );

		if ( ! isset( $value ) ){
			return $original;
		}else{

			if( $value !== false && !empty( $value ) && is_array( $value ) ) {
				foreach ($value AS $key => $model) {
					$value[$key]['content'] = $this->_filter_row_content($model['content']);
				}
			}

			$current_page_settings = $original;
			$current_page_settings[ $this->id ] = $value;
			return $current_page_settings;
		}

	}

	public function save_theme_content( $base_settings_values , $page_id ){

		if( isset( $base_settings_values['theme_content'] ) ) {
			$theme_content = $base_settings_values['theme_content'];

			foreach ( $theme_content AS $key => $model ){
				$theme_content[$key]['content'] = $this->_filter_row_content( $model['content'] );
			}

			$base_settings_values['theme_content'] = $theme_content;
		}

		return $base_settings_values;
	}

	public function _filter_row_content( $content_shortcodes ){

		global $sed_apps;
		$tree_shortcodes = $sed_apps->editor->save->build_tree_shortcode( $content_shortcodes , $content_shortcodes[0]['parent_id'] );
		$content = $sed_apps->editor->save->create_shortcode_content( $tree_shortcodes , array() );

		return $content;
	}

}