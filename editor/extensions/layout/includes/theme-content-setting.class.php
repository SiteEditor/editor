<?php
/**
 * Site Editor page theme content Class.
 *
 * Handles saving and sanitizing of 'theme_content' setting.
 *
 * @package SiteEditor
 * @subpackage Settings
 * @since 1.0.0
 */

class SedThemeContentSetting {

	/**
	 * pattern for theme_content options
	 */
	const SETTING_OPTION_ID_PATTERN = '/^sed_(?P<page_id>.+)_settings\[theme_content\]$/';

	/**
	 * pattern for theme_content postmeta
	 */
	const SETTING_META_ID_PATTERN = '/^postmeta\[(?P<post_type>[^\]]+)\]\[(?P<post_id>-?\d+)\]\[theme_content\]$/';

	/**
	 * SedThemeContentSetting constructor.
	 */
	public function __construct( ) {

		add_filter( 'sed_app_sanitize_setting' 	, array( $this , 'sanitize' ) , 10 , 2 );

		add_filter( "sed_app_sanitize_js_setting"  	, array( $this , "js_value" ) , 10 , 2  );

	}

	/**
	 *
	 * @param $components
	 * @param $manager
	 * @return mixed
	 */
	function sanitize( $value , $setting ){

        $setting_id = $setting->id; 

        if (preg_match(self::SETTING_OPTION_ID_PATTERN, $setting_id, $option_matches) || preg_match(self::SETTING_META_ID_PATTERN, $setting_id, $meta_matches)) {

            if (!empty($value) ) { //&& is_array($value)

                $value = json_decode( urldecode( $value ) , true );

                foreach ($value AS $key => $model) {

                    $content = $this->_filter_row_content( $model['content'] );

                    $value[$key]['content'] = $content;

                }
            }

        }

		return $value;
	}

	/**
	 * @param $value
	 * @param $setting
	 * @return array
	 */
	public function js_value( $value , $setting  ){

		if ( preg_match(self::SETTING_OPTION_ID_PATTERN, $setting->id , $option_matches) || preg_match(self::SETTING_META_ID_PATTERN, $setting->id , $meta_matches)  ) {

			$value = array();
		}

		return $value;
	}

	/**
	 * @param $content_shortcodes
	 * @return mixed
	 */
	public function _filter_row_content( $content_shortcodes ){ //var_dump( $content_shortcodes );

		global $sed_apps;
		$tree_shortcodes = $sed_apps->editor->save->build_tree_shortcode( $content_shortcodes , $content_shortcodes[0]['parent_id'] );
		$content = $sed_apps->editor->save->create_shortcode_content( $tree_shortcodes , array() , 0 , true );

		return $content;
	}

}