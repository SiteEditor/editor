<?php
/**
 * Integration SiteEditor Visual Theme Builder With Any Theme
 *
 * @class     SiteEditorThemeIntegration
 * @version   1.0.0
 * @author    SiteEditor
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class SiteEditorThemeIntegration {

  function __construct() {

    global $sed_static_template_output;
    global $sed_running_integration;

    $sed_static_template_output = false;

    add_action( 'sed_start_template', array( $this, 'do_footer' ) );

    /** Capture template and output in content */
    //if ( pl_is_static_template( 'int' )  ) {

      add_action( 'sed_start_template', array( $this, 'start_integration' ) );
      add_action( 'sed_after_template', array( $this, 'get_integration_output' ) );
    //}
  }


  function do_footer() {

    //remove_all_actions( 'pagelines_start_footer' );

    /**
     * Problem / Solution Statement
     * 1. All themes run get_footer in template which prevents PL from working correctly
     * 2. However, some themes/shortcodes add stuff to globals or add new actions to wp_footer
     *
     * Solution:
     * So solve first problem we run get_footer here, but first move all actions off of wp_footer to a workaround action
     * Then we reset wp_footer where it can pick up new actions.
     * Run the workaround action and wp_footer again in the real footer of the page.
     */

    global $wp_filter;

    /**
     * WordPress 4.7 introduces new class WP_Hook to handle filters and actions.
     */
    if( class_exists( 'WP_Hook' ) ) {
      $wp_filter['sed_footer_area'] = new WP_Hook;
      $wp_filter['sed_footer_area']->callbacks = $wp_filter['wp_footer']->callbacks;

      /**
       * Pre WordPress 4.7
       */
    } else {
      $wp_filter['sed_footer_area'] = $wp_filter['wp_footer'];
    }

    remove_all_actions( 'wp_footer' );

    global $get_footer_output;

    ob_start();

    if( ! defined( 'SED_ALTERNATIVE_FOOTER_SCRIPTS' ) ) {
      do_action( 'wp_print_footer_scripts' );
    }

    get_footer();

    $get_footer_output = ob_get_clean();

  }



  function start_integration() {

    global $sed_running_integration;
    $sed_running_integration = true;

    /** Start a buffer to capture plugin output (which we'll add to our content section ) */
    ob_start();

  }

  function get_integration_output() {

    global $sed_static_template_output;
    global $sed_running_integration;

    $this->wrap_start   = '<div class="static-template" ><div sed-role="static-template-content" class="static-template-content clearfix">';
    $this->wrap_end   = '</div></div>';

    $content = apply_filters( 'sed_static_template_output', ob_get_clean() );

    $sed_static_template_output = sprintf( '%s%s%s', $this->wrap_start, $content, $this->wrap_end );

    $sed_running_integration = false;

    sed_primary_template();

  }
}


class SiteEditorWrapping {
  // Stores the full path to the main template file q
  public static $main_template;

  // Basename of template file
  public $slug;

  // Array of templates
  public $templates;

  // Stores the base name of the template file; e.g. 'page' for 'page.php' etc.
  public static $base;

  public function __construct( $template = 'sed-base.php' ) {

    $this->slug = basename( $template, '.php' );

    $this->templates = array( $template );

    /** Allow base.php override */
    if ( self::$base ) {

      $str = substr( $template, 0, -4 );

      array_unshift( $this->templates, sprintf( $str . '-%s.php', self::$base ) );

    }

    new SiteEditorThemeIntegration;
  }

  //
  // Magic : http://php.net/manual/en/language.oop5.magic.php#object.tostring
  //
  public function __toString() {

    $this->templates = apply_filters( 'sed_templates_wrapping' , $this->templates , $this->slug );

    $this->templates = apply_filters( 'sed_wrapping_' . $this->slug, $this->templates );

    $path = locate_template( $this->templates );

    if ( '' == $path ) {
      return apply_filters( 'sed_base_template_wrapping' , SED_INC_FRAMEWORK_DIR . "/sed-base.php" );
    } else {
      return $path;
    }

  }

  public static function wrap( $main ) {

    // Check for other filters returning null
    if ( ! is_string( $main ) ) {
      return $main;
    }

    self::$main_template = $main;
    self::$base = basename( self::$main_template, '.php' );

    // DMS Hack, if we're on index.php then it should have render set to true. This wrap prevents it without.
    if (  'index' == self::$base ) {
      /*global $pagelines_render;
      $pagelines_render = true;*/
    }

    return new SiteEditorWrapping();
  }
}

add_filter( 'template_include', array( 'SiteEditorWrapping', 'wrap' ), 99 );


function sed_template_path() {
  return SiteEditorWrapping::$main_template;
}


/**
 * The base file name for the template
 */
function sed_template_base() {

  return SiteEditorWrapping::$base;
}

/** Add information to the header */
function sed_get_header() {

  $template = apply_filters( 'sed_header_wrapping_template' , '' );

  $template_name = "header.php";

  ob_start();

  if ( !empty( $template ) && file_exists( $template ) && ( !is_child_theme() || ( is_child_theme() && !file_exists(STYLESHEETPATH . '/' . $template_name) ) ) ){

    load_template( $template , true );

  }else {

    get_header();

  }

  $header = ob_get_clean();

  $header = str_replace( '<head>', sprintf( '<head>%1$s %2$s %1$s', "\n", '<!-- Built With SiteEditor | http://www.siteeditor.org -->' ), $header );

  echo $header;

}

function sed_get_footer() {

  global $get_footer_output;

  $template = apply_filters( 'sed_footer_wrapping_template' , '' );

  $template_name = "footer.php";

  ob_start();

  if ( !empty( $template ) && file_exists( $template ) && ( !is_child_theme() || ( is_child_theme() && !file_exists(STYLESHEETPATH . '/' . $template_name) ) ) ){

    load_template( $template , true );

  }else {

    echo sed_remove_closing_tags($get_footer_output);

    /** Run WP Footer action again for any shortcodes, etc.. that may have placed new actions there. */
    do_action('wp_footer');

    /** Takes all original wp_footer actions (see above) */
    do_action('sed_footer_area');

    if (defined('SED_ALTERNATIVE_FOOTER_SCRIPTS')) {
      do_action('wp_print_footer_scripts');
    }

    /** All JSON data from PL */
    do_action('pl_json_data');

    printf('</body></html><!-- Thanks for stopping by. Have an amazing day! -->');

  }

  $footer = ob_get_clean();

  echo $footer;

}

function sed_remove_closing_tags( $in ) {

  $out = str_replace( '</body>', '', $in );

  $out = str_replace( '</html>', '', $out );

  return $out;
}

function sed_tpl_classes() {

  $classes      = array( 'pl-region' );
  $attributes   = array( 'data-clone="template"' );

  return sprintf( 'class="%s" %s', join( ' ', $classes ), join( ' ', $attributes ) );
}

function sed_primary_template() { 

  global $sed_static_template_output;
  global $sed_running_integration , $sed_data; 
 
  $wide_boxed_class = "sed-row-wide";//( isset( $sed_data['page_length'] ) && $sed_data['page_length'] == "boxed" ) ? "sed-row-boxed" : "sed-row-wide";
  ?>
    <div id="site-editor-page-part" class="site-editor-page" sed-role="layout">
      <div id="site-editor-main-part" class="sed-site-main-part sed-pb-main-component sed-pb-post-container <?php //echo $wide_boxed_class;?>" data-post-id="<?php echo $sed_data['page_id'];?>" data-parent-id="root" data-page-type="<?php echo $sed_data['page_type'];?>" data-content-type="theme"  sed-layout="row" sed-type-row="static" sed-role="main-content">
        <?php
            do_action( 'sed_region_template', 'sed_region_template', 'templates' );
            //pl_template_hook( 'pl_region_template', 'templates' );
        ?>
      </div>
    </div>
<?php
}
