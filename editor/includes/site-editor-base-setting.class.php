<?php
/**
 * Site Editor Setting Class.
 *
 * Combine Options setings & post meta settings and load in all pages
 *
 * @package SiteEditor
 * @subpackage Settings
 * @since 3.4.0
 */
class SedBaseSettings extends SedAppSettings{

	const TYPE = 'base';

	/**
	 * Type of setting.
	 *
	 * @access public
	 * @var string
	 */
	public $type = self::TYPE;

	/**
	 * Cached and sanitized $_POST value for the setting.
	 *
	 * @access private
	 * @var mixed
	 */
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

		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Handle previewing the setting.
	 *
	 * @since 3.4.0
	 */
	public function preview() {

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

	}

    public function _preview_base_meta_settings( $original_meta_value, $post_id, $meta_key, $single ) {

		if ( isset( $meta_key ) && $meta_key == $this->id ) {

			$value = $this->post_value();

			if ( ! isset( $value[$post_id] ) || ! is_array( $value[$post_id] ) ){

				return $original_meta_value;

			}else{

				if( !empty( $value[$post_id] ) && is_array( $value[$post_id] ) ) {
					foreach ($value[$post_id] AS $key => $model) {
						$value[$post_id][$key]['content'] = $this->_filter_row_content($model['content']);
					}
				}

				return $single ? array( $value[$post_id] ) : $value[$post_id];
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

		return $this->multidimensional_replace( $original, $this->id_data[ 'keys' ], $this->post_value() );

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
	 * Save the value of the setting, using the related API.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $value The value to update.
	 * @return mixed The result of saving the value.
	 */
	protected function update( $value ) {

	}

	/**
	 * Fetch the value of the setting.
	 *
	 * @since 3.4.0
	 *
	 * @return mixed The value.
	 */
	public function value() {

		$sed_settings = $this->manager->sed_page_settings();
		$value = ( isset( $sed_settings[ $this->id ] ) ) ? $sed_settings[ $this->id ] : $this->default;
		return $value;
		break;

	}
}