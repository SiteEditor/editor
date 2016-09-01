<?php

/**
 * Disable error reporting
 *
 * Set this to error_reporting( E_ALL ) or error_reporting( E_ALL | E_STRICT ) for debugging
 */
//error_reporting(0);

/** Set ABSPATH for execution */
define( 'SEDPATH', dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))))) . '/' );
define( 'SEDAPPSPATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/' );


require_once SEDPATH."wp-content/uploads/site-editor/style_editor_settings.php";
require_once SEDAPPSPATH."siteeditor/includes/pagebuilder/module_settings.class.php";

/**
 * Pluck a certain field out of each object in a list.
 *
 * This has the same functionality and prototype of
 * array_column() (PHP 5.5) but also supports objects.
 *
 * @since 3.1.0
 * @since 4.0.0 $index_key parameter added.
 *
 * @param array      $list      List of objects or arrays
 * @param int|string $field     Field from the object to place instead of the entire object
 * @param int|string $index_key Optional. Field from the object to use as keys for the new array.
 *                              Default null.
 * @return array Array of found values. If $index_key is set, an array of found values with keys
 *               corresponding to $index_key.
 */
function wp_list_pluck( $list, $field, $index_key = null ) {
	if ( ! $index_key ) {
		/*
		 * This is simple. Could at some point wrap array_column()
		 * if we knew we had an array of arrays.
		 */
		foreach ( $list as $key => $value ) {
			if ( is_object( $value ) ) {
				$list[ $key ] = $value->$field;
			} else {
				$list[ $key ] = $value[ $field ];
			}
		}
		return $list;
	}

	/*
	 * When index_key is not set for a particular item, push the value
	 * to the end of the stack. This is how array_column() behaves.
	 */
	$newlist = array();
	foreach ( $list as $value ) {
		if ( is_object( $value ) ) {
			if ( isset( $value->$index_key ) ) {
				$newlist[ $value->$index_key ] = $value->$field;
			} else {
				$newlist[] = $value->$field;
			}
		} else {
			if ( isset( $value[ $index_key ] ) ) {
				$newlist[ $value[ $index_key ] ] = $value[ $field ];
			} else {
				$newlist[] = $value[ $field ];
			}
		}
	}

	return $newlist;
}

function add_style_control( $style , $panel_id , $selector ){
    global $icons_classes , $labeles;
    $icon  = $icons_classes[ $style ];
    $label = $labeles[ $style ];

    return  array(
                'type'      =>  'style_editor_button',
                'label'     =>  $label ,
                'icon'      =>  $icon,
                'class'     =>  'sted_element_control_btn',
                'panel'     =>  $panel_id ,
                'atts'      =>  array(
                    'data-style-id'     => $style ,
                    'data-dialog-title' => $label ,
                    'data-selector'     => $selector
                )
            );

}

function print_style_editor_settings( $style_editor_settings ){
    if( empty( $style_editor_settings ) )
        return "";

    $content = "";

    foreach( $style_editor_settings AS $shortcode_name => $settings ){
        $panels = array();
        $controls = array();
        foreach( $settings AS $setting ){
            if( is_array( $setting ) && count( $setting ) == 4 && is_array( $setting[2] ) ){

                $panel_id = $shortcode_name . '_' . $setting[0] . '_panel';

                $panels[$panel_id] = array(
                    'title'         =>  $setting[3]  ,
                    'label'         =>  $setting[3] ,
                    'capability'    => 'edit_theme_options' ,
                    'type'          => 'accordion_item' ,
                    'description'   => '' ,
                    'parent_id'     => 'root' ,
                    'priority'      => 9 ,
                    'id'            => $panel_id  ,
                    'atts'      =>  array(
                        'class'             => "design_ac_header" ,
                        'data-selector'     => $setting[1]
                    )
                );

                if( !empty($setting[2]) ){
                    foreach( $setting[2] AS $control ){
                        $controls[$shortcode_name . '_' . $setting[0] . '_' . $control ] = add_style_control( $control , $panel_id , $setting[1] );
                    }
                }

            }
        }

        if( !empty( $controls ) ){
            ModuleSettings::$group_id = $shortcode_name;
            $style_editor_settings_html = ModuleSettings::create_settings($controls, $panels);

            ob_start();
            ?>
            <script type="text/html"  id="style_editor_panel_<?php echo $shortcode_name;?>_tmpl" >
                <div class="accordion-panel-settings">
                <?php echo $style_editor_settings_html;?>
                </div>
            </script>
            <?php
            $contents = ob_get_contents();
            ob_end_clean();

            $content .= $contents;

            ModuleSettings::$group_id = "";
        }
    }

    return $content;
}

if( !function_exists("wp_json_encode") ){
    /**
     * Encode a variable into JSON, with some sanity checks.
     *
     * @since 4.1.0
     *
     * @param mixed $data    Variable (usually an array or object) to encode as JSON.
     * @param int   $options Optional. Options to be passed to json_encode(). Default 0.
     * @param int   $depth   Optional. Maximum depth to walk through $data. Must be
     *                       greater than 0. Default 512.
     * @return bool|string The JSON encoded string, or false if it cannot be encoded.
     */
    function wp_json_encode( $data, $options = 0, $depth = 512 ) {
        /*
         * json_encode() has had extra params added over the years.
         * $options was added in 5.3, and $depth in 5.5.
         * We need to make sure we call it with the correct arguments.
         */
        if ( version_compare( PHP_VERSION, '5.5', '>=' ) ) {
            $args = array( $data, $options, $depth );
        } elseif ( version_compare( PHP_VERSION, '5.3', '>=' ) ) {
            $args = array( $data, $options );
        } else {
            $args = array( $data );
        }

        $json = call_user_func_array( 'json_encode', $args );

        // If json_encode() was successful, no need to do more sanity checking.
        // ... unless we're in an old version of PHP, and json_encode() returned
        // a string containing 'null'. Then we need to do more sanity checking.
        if ( false !== $json && ( version_compare( PHP_VERSION, '5.5', '>=' ) || false === strpos( $json, 'null' ) ) )  {
            return $json;
        }

        try {
            $args[0] = _wp_json_sanity_check( $data, $depth );
        } catch ( Exception $e ) {
            return false;
        }

        return call_user_func_array( 'json_encode', $args );
    }
}

$expires_offset = 31536000; // 1 year

header('Content-Type: text/html');
header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + $expires_offset ) . ' GMT');
header("Cache-Control: public, max-age=$expires_offset");

//echo print_style_editor_settings( $style_editor_settings );
echo json_encode( array(
    'success' => true,
    'data'    => array(
        'output'             => print_style_editor_settings( $style_editor_settings ) ,
    ),
) );

