<?php

/**
 * Disable error reporting
 *
 * Set this to error_reporting( E_ALL ) or error_reporting( E_ALL | E_STRICT ) for debugging
 */
error_reporting(0);

/** Set ABSPATH for execution */
define( 'SEDPATH', dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))))) . '/' );

require_once SEDPATH."wp-content/uploads/site-editor/shortcodes.patterns.php";

function shortcodes_regexp( $tagnames = array() ){
    global $shortcodes_tagnames;
    if( empty($tagnames) ){
        global $site_editor_app;
        $tagnames = $shortcodes_tagnames;
    }

	$tagregexp = join( '|', array_map('preg_quote', $tagnames ) );

    return '\\['                              // Opening bracket
        . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
        . "($tagregexp)"                     // 2: Shortcode name
        . '(?![\\w-])'                       // Not followed by word character or hyphen
        . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
        .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
        .     '(?:'
        .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
        .         '[^\\]\\/]*'               // Not a closing bracket or forward slash
        .     ')*?'
        . ')'
        . '(?:'
        .     '(\\/)'                        // 4: Self closing tag ...
        .     '\\]'                          // ... and closing bracket
        . '|'
        .     '\\]'                          // Closing bracket
        .     '(?:'
        .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
        .             '[^\\[]*+'             // Not an opening bracket
        .             '(?:'
        .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
        .                 '[^\\[]*+'         // Not an opening bracket
        .             ')*+'
        .         ')'
        .         '\\[\\/\\2\\]'             // Closing shortcode tag
        .     ')?'
        . ')'
        . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]

}

$shortcode_tag_counter = 1;
//the initialize skin shortcodes pattern for
function get_pattern_shortcodes( $content_pattern, $parent_id = "root" , $module = "" , $module_shortcode = "" , $tagnames = array() ) {
    global $shortcode_tag_counter;
    $string = '';
    $shortcodes = array();
    $content = array();
    $content_init = false;

    if( !empty( $tagnames ) && is_array( $tagnames ) )
        $pattern = shortcodes_regexp( $tagnames );
    else
        $pattern = shortcodes_regexp();
                                                  // '/s'
    $except_content = preg_split('/'. $pattern .'/s'  , $content_pattern );

    $j = 0;
    foreach( $except_content AS $ex_content){
        $ex_content = trim($ex_content);
        if(!empty($ex_content)){

            $id = md5( time() . '-' . $shortcode_tag_counter ++ );

            $content = array(
                'tag'           => 'content',
                //'attrs_query'   => '',
                'attrs'         => array(),
                'id'            => $id,
                'content'       => $ex_content ,
                'parent_id'     => $parent_id,
            );

            $content_order = $j;
            break;
        }
        $j++;
    }

    if( count($except_content) > 1 ){
        if (   preg_match_all(  '/'. $pattern .'/s'  , $content_pattern , $matches ) && array_key_exists( 2, $matches ) ){
            $i = 0;
    		foreach ( $matches[2] as $index => $tag ) {

                if(!empty($content) && $content_order == $i){
                    $shortcodes[]  = $content;
                    $content_init = true;
                }

    			$id = md5( time() . '-' . $shortcode_tag_counter ++ );

    			$shortcode = array(
    				'tag'           => $tag,
    				//'attrs_query'   => $matches[3][ $index ],
    				'attrs'         => shortcode_parse_atts( $matches[3][ $index ] ),
    				'id'            => $id,
    				'parent_id'     => $parent_id,
    			);

                $attrs_query = $matches[3][ $index ];

                $shortcode['attrs']['sed_model_id'] = $id;

                if(isset( $shortcode['attrs']['shortcode_tag'] ) ){

                    $shortcode['tag'] = $shortcode['attrs']['shortcode_tag'];
                    unset($shortcode['attrs']['shortcode_tag']);
                }

                if( !empty($module) && $module_shortcode != $shortcode['tag'] && !in_array( $shortcode['tag'] , array( "sed_row" , "sed_module" ) )  && (!isset($shortcode['attrs']['parent_module']) || empty($shortcode['attrs']['parent_module']) ) ){
                    $shortcode['attrs']['parent_module'] = $module;
                }else if( in_array( $shortcode['tag'] , array( "sed_row" , "sed_module" ) ) ){
                    $shortcode['attrs']['parent_module'] = "";
                }

                $shortcodes[] = $shortcode;

                $children = get_pattern_shortcodes( $matches[5][$index] , $id , $module , $module_shortcode );

                if( !empty( $children['shortcodes'] ) ){
                    $shortcodes = array_merge($shortcodes , $children['shortcodes']);
                }

    			$string .= '[' . $shortcode['tag'] . ' id="' . $id . '" ' . $attrs_query . ']' . $children['string'] . '[/' . $shortcode['tag'] . ']' ;

                $i++;
            }

            if(!empty($content) && $content_init === false){
                $shortcodes[]           = $content;
                $content_init           = true;
            }

        }
    }elseif( count($except_content) == 1 && !empty($content) ){
        $shortcodes[] = $content;
        $string .= $ex_content;
    }

    return array(
        "string"        => $string,
        "shortcodes"    => $shortcodes
    );
}

function shortcode_parse_atts($text) {
    $atts = array();
    $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
    $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
    if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
        foreach ($match as $m) {
            if (!empty($m[1]))
                $atts[$m[1]] = stripcslashes($m[2]);
            elseif (!empty($m[3]))
                $atts[$m[3]] = stripcslashes($m[4]);
            elseif (!empty($m[5]))
                $atts[$m[5]] = stripcslashes($m[6]);
            elseif (isset($m[7]) and strlen($m[7]))
                $atts[] = stripcslashes($m[7]);
            elseif (isset($m[8]))
                $atts[] = stripcslashes($m[8]);
        }
    } else {
        $atts = ltrim($text);
    }
    return $atts;
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

$def_patterns = array();
foreach( $shortcodes AS $name => $settings ){
    $shortcodes_model = get_pattern_shortcodes( $settings['pattern'] , "root" ,  $settings['parent_module'] , $name );
    $def_patterns[$name] = $shortcodes_model['shortcodes'];
}

echo json_encode( array(
        'success' => true,
        'data'    => array(
            'output'             => $def_patterns ,
        ),
    )
);
