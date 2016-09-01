<?php

/**
 * Disable error reporting
 *
 * Set this to error_reporting( E_ALL ) or error_reporting( E_ALL | E_STRICT ) for debugging
 */
error_reporting(0);

/** Set ABSPATH for execution */
define( 'SEDPATH', dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/' );

/**
 * @ignore
 */
function __() {}

/**
 * @ignore
 */
function _x() {}

/**
 * @ignore
 */
function add_filter() {}

/**
 * @ignore
 */
function esc_attr() {}

/**
 * @ignore
 */
function apply_filters() {}

/**
 * @ignore
 */
function get_option() {}

/**
 * @ignore
 */
function is_lighttpd_before_150() {}

/**
 * @ignore
 */
function add_action() {}

/**
 * @ignore
 */
function do_action_ref_array() {}

/**
 * @ignore
 */
function get_bloginfo() {}

/**
 * @ignore
 */
function is_admin() {return true;}

/**
 * @ignore
 */
function site_url() {}

/**
 * @ignore
 */
function admin_url() {}

/**
 * @ignore
 */
function wp_guess_url() {}

function curPageURL() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
        $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

$pageURL = curPageURL();
$pos = strpos($pageURL, 'includes/load_styles.php');
$base_url = substr($pageURL, 0, $pos);

function get_file($path) {

	if ( function_exists('realpath') )
		$path = realpath($path);

	if ( ! $path || ! @is_file($path) )
		return '';

	return @file_get_contents($path);
}

$load = $_GET['load'];
if ( is_array( $load ) )
	$load = implode( '', $load );

//$load = preg_replace( '/[^a-z0-9,_-]+/i', '', $_GET['load'] );
$load = array_unique( explode( ',', $load ) );

if ( empty($load) )
	exit;

$compress = ( isset($_GET['c']) && $_GET['c'] );
$force_gzip = ( $compress && 'gzip' == $_GET['c'] );
$expires_offset = 31536000; // 1 year
$out = '';

function modification_css_url($url , $handle , $is_full_url = false){
    if( strpos($url , "http://") === false && strpos($url , "https://") === false  ){

        if($is_full_url === false){
            $style_url = $_GET['base_url'].$handle;
        }else{
            $style_url = $handle;
        }

        $pre_path = 1;
        $pre_path += substr_count($url , "../");

        $url = str_replace("../" , "" , $url);

        $handle2 = $style_url;
        for ($i=1; $i <= $pre_path; $i++)  {
            $handle2 = substr($handle2, 0 , strrpos($handle2, '/'));
        }

        return $handle2 . "/" . $url;
    }else{
        return $url;
    }
}

foreach( $load as $handle ) {
    if(!empty($handle)){
        if( strpos($handle , "http://") === false && strpos($handle , "https://") === false  ){
    	    $path =   SEDPATH .$handle;
            $is_full_url = false;
        }else{
            $base_url = 'http';
            if ($_SERVER["HTTPS"] == "on") {$base_url .= "s";}
            $base_url .= "://".$_SERVER["SERVER_NAME"];
            $path = $_SERVER['DOCUMENT_ROOT'] . str_replace( $base_url , "", $handle);

            $is_full_url = true;
        }

      	$this_file = get_file( $path ) . "\n";

        $callback = function($matches) use ( $handle , $is_full_url ){

            if (!preg_match("/^data:/i", $matches[1])) {
                return "url(".modification_css_url($matches[1] , $handle , $is_full_url).")";
            }else{
                return "url(".$matches[1].")";
            }
        };

        $this_file = preg_replace_callback('/url\(\s*[\'"]?([^\)\'"]+)[\'"]?\s*\)/U', $callback , $this_file);

        $callback2 = function($matches) use ( $handle , $is_full_url ){

            return "@import url(".modification_css_url($matches[2] , $handle , $is_full_url).")";

        };
        $this_file = preg_replace_callback('/(@import\s*[\'"]+([^\'"]+)[\'"]+\s*([^\;]+)\;)/U', $callback2, $this_file);

        $out .= $this_file;

    }
}





header('Content-Type: text/css');
header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + $expires_offset ) . ' GMT');
header("Cache-Control: public, max-age=$expires_offset");

if ( $compress && ! ini_get('zlib.output_compression') && 'ob_gzhandler' != ini_get('output_handler') && isset($_SERVER['HTTP_ACCEPT_ENCODING']) ) {
	header('Vary: Accept-Encoding'); // Handle proxies
	if ( false !== stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') && function_exists('gzdeflate') && ! $force_gzip ) {
		header('Content-Encoding: deflate');
		$out = gzdeflate( $out, 3 );
	} elseif ( false !== stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && function_exists('gzencode') ) {
		header('Content-Encoding: gzip');
		$out = gzencode( $out, 3 );
	}
}

echo $out;
exit;
