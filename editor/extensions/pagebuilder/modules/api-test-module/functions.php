<?php
/**
 * Twenty Fifteen functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @link https://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * {@link https://codex.wordpress.org/Plugin_API}
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since Twenty Fifteen 1.0
 */
if ( ! isset( $content_width ) ) {
	$content_width = 660;
}

/**
 * Twenty Fifteen only works in WordPress 4.1 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.1-alpha', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
}

if ( ! function_exists( 'twentyfifteen_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on twentyfifteen, use a find and replace
	 * to change 'twentyfifteen' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'twentyfifteen', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * See: https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 825, 510, true );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu',      'twentyfifteen' ),
		'social'  => __( 'Social Links Menu', 'twentyfifteen' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
	) );

	/*
	 * Enable support for custom logo.
	 *
	 * @since Twenty Fifteen 1.5
	 */
	add_theme_support( 'custom-logo', array(
		'height'      => 248,
		'width'       => 248,
		'flex-height' => true,
	) );

	$color_scheme  = twentyfifteen_get_color_scheme();
	$default_color = trim( $color_scheme[0], '#' );

	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'twentyfifteen_custom_background_args', array(
		'default-color'      => $default_color,
		'default-attachment' => 'fixed',
	) ) );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	 */
	add_editor_style( array( 'css/editor-style.css', 'genericons/genericons.css', twentyfifteen_fonts_url() ) );

	// Indicate widget sidebars can use selective refresh in the Customizer.
	add_theme_support( 'customize-selective-refresh-widgets' );
}
endif; // twentyfifteen_setup
add_action( 'after_setup_theme', 'twentyfifteen_setup' );

/**
 * Register widget area.
 *
 * @since Twenty Fifteen 1.0
 *
 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
 */
function twentyfifteen_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Widget Area', 'twentyfifteen' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar.', 'twentyfifteen' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'twentyfifteen_widgets_init' );

if ( ! function_exists( 'twentyfifteen_fonts_url' ) ) :
/**
 * Register Google fonts for Twenty Fifteen.
 *
 * @since Twenty Fifteen 1.0
 *
 * @return string Google fonts URL for the theme.
 */
function twentyfifteen_fonts_url() {
	$fonts_url = '';
	$fonts     = array();
	$subsets   = 'latin,latin-ext';

	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Noto Sans, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Noto Sans font: on or off', 'twentyfifteen' ) ) {
		$fonts[] = 'Noto Sans:400italic,700italic,400,700';
	}

	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Noto Serif, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Noto Serif font: on or off', 'twentyfifteen' ) ) {
		$fonts[] = 'Noto Serif:400italic,700italic,400,700';
	}

	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Inconsolata, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Inconsolata font: on or off', 'twentyfifteen' ) ) {
		$fonts[] = 'Inconsolata:400,700';
	}

	/*
	 * Translators: To add an additional character subset specific to your language,
	 * translate this to 'greek', 'cyrillic', 'devanagari' or 'vietnamese'. Do not translate into your own language.
	 */
	$subset = _x( 'no-subset', 'Add new subset (greek, cyrillic, devanagari, vietnamese)', 'twentyfifteen' );

	if ( 'cyrillic' == $subset ) {
		$subsets .= ',cyrillic,cyrillic-ext';
	} elseif ( 'greek' == $subset ) {
		$subsets .= ',greek,greek-ext';
	} elseif ( 'devanagari' == $subset ) {
		$subsets .= ',devanagari';
	} elseif ( 'vietnamese' == $subset ) {
		$subsets .= ',vietnamese';
	}

	if ( $fonts ) {
		$fonts_url = add_query_arg( array(
			'family' => urlencode( implode( '|', $fonts ) ),
			'subset' => urlencode( $subsets ),
		), 'https://fonts.googleapis.com/css' );
	}

	return $fonts_url;
}
endif;

/**
 * JavaScript Detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @since Twenty Fifteen 1.1
 */
function twentyfifteen_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'twentyfifteen_javascript_detection', 0 );

/**
 * Enqueue scripts and styles.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_scripts() {
	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'twentyfifteen-fonts', twentyfifteen_fonts_url(), array(), null );

	// Add Genericons, used in the main stylesheet.
	wp_enqueue_style( 'genericons', get_template_directory_uri() . '/genericons/genericons.css', array(), '3.2' );

	// Load our main stylesheet.
	wp_enqueue_style( 'twentyfifteen-style', get_stylesheet_uri() );

	// Load the Internet Explorer specific stylesheet.
	wp_enqueue_style( 'twentyfifteen-ie', get_template_directory_uri() . '/css/ie.css', array( 'twentyfifteen-style' ), '20141010' );
	wp_style_add_data( 'twentyfifteen-ie', 'conditional', 'lt IE 9' );

	// Load the Internet Explorer 7 specific stylesheet.
	wp_enqueue_style( 'twentyfifteen-ie7', get_template_directory_uri() . '/css/ie7.css', array( 'twentyfifteen-style' ), '20141010' );
	wp_style_add_data( 'twentyfifteen-ie7', 'conditional', 'lt IE 8' );

	wp_enqueue_script( 'twentyfifteen-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20141010', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'twentyfifteen-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20141010' );
	}

	wp_enqueue_script( 'twentyfifteen-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20150330', true );
	wp_localize_script( 'twentyfifteen-script', 'screenReaderText', array(
		'expand'   => '<span class="screen-reader-text">' . __( 'expand child menu', 'twentyfifteen' ) . '</span>',
		'collapse' => '<span class="screen-reader-text">' . __( 'collapse child menu', 'twentyfifteen' ) . '</span>',
	) );
}
add_action( 'wp_enqueue_scripts', 'twentyfifteen_scripts' );

/**
 * Add featured image as background image to post navigation elements.
 *
 * @since Twenty Fifteen 1.0
 *
 * @see wp_add_inline_style()
 */
function twentyfifteen_post_nav_background() {
	if ( ! is_single() ) {
		return;
	}

	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );
	$css      = '';

	if ( is_attachment() && 'attachment' == $previous->post_type ) {
		return;
	}

	if ( $previous &&  has_post_thumbnail( $previous->ID ) ) {
		$prevthumb = wp_get_attachment_image_src( get_post_thumbnail_id( $previous->ID ), 'post-thumbnail' );
		$css .= '
			.post-navigation .nav-previous { background-image: url(' . esc_url( $prevthumb[0] ) . '); }
			.post-navigation .nav-previous .post-title, .post-navigation .nav-previous a:hover .post-title, .post-navigation .nav-previous .meta-nav { color: #fff; }
			.post-navigation .nav-previous a:before { background-color: rgba(0, 0, 0, 0.4); }
		';
	}

	if ( $next && has_post_thumbnail( $next->ID ) ) {
		$nextthumb = wp_get_attachment_image_src( get_post_thumbnail_id( $next->ID ), 'post-thumbnail' );
		$css .= '
			.post-navigation .nav-next { background-image: url(' . esc_url( $nextthumb[0] ) . '); border-top: 0; }
			.post-navigation .nav-next .post-title, .post-navigation .nav-next a:hover .post-title, .post-navigation .nav-next .meta-nav { color: #fff; }
			.post-navigation .nav-next a:before { background-color: rgba(0, 0, 0, 0.4); }
		';
	}

	wp_add_inline_style( 'twentyfifteen-style', $css );
}
add_action( 'wp_enqueue_scripts', 'twentyfifteen_post_nav_background' );

/**
 * Display descriptions in main navigation.
 *
 * @since Twenty Fifteen 1.0
 *
 * @param string  $item_output The menu item output.
 * @param WP_Post $item        Menu item object.
 * @param int     $depth       Depth of the menu.
 * @param array   $args        wp_nav_menu() arguments.
 * @return string Menu item with possible description.
 */
function twentyfifteen_nav_description( $item_output, $item, $depth, $args ) {
	if ( 'primary' == $args->theme_location && $item->description ) {
		$item_output = str_replace( $args->link_after . '</a>', '<div class="menu-item-description">' . $item->description . '</div>' . $args->link_after . '</a>', $item_output );
	}

	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'twentyfifteen_nav_description', 10, 4 );

/**
 * Add a `screen-reader-text` class to the search form's submit button.
 *
 * @since Twenty Fifteen 1.0
 *
 * @param string $html Search form HTML.
 * @return string Modified search form HTML.
 */
function twentyfifteen_search_form_modify( $html ) {
	return str_replace( 'class="search-submit"', 'class="search-submit screen-reader-text"', $html );
}
add_filter( 'get_search_form', 'twentyfifteen_search_form_modify' );

/**
 * Implement the Custom Header feature.
 *
 * @since Twenty Fifteen 1.0
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 *
 * @since Twenty Fifteen 1.0
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions.
 *
 * @since Twenty Fifteen 1.0
 */
require get_template_directory() . '/inc/customizer.php';

function twentyfifteen_theme_Kirki_options()
{
	Kirki::add_config('my_theme', array(
		'capability' => 'edit_theme_options',
		'option_type' => 'theme_mod',
	));

	Kirki::add_panel('panel_id', array(
		'priority' => 10,
		'title' => __('My Panel', 'textdomain'),
		'description' => __('My Description', 'textdomain'),
	));

	/*
	* @Text Box Settings
	*/

	Kirki::add_section('my_text_section', array(
		'title' => __('Text Fields'),
		'description' => __('Add Fields Here'),
		'panel' => 'panel_id', // Not typically needed.
		'priority' => 160,
		'capability' => 'edit_theme_options',
		'theme_supports' => '', // Rarely needed.
	));

	Kirki::add_field('my_config_text', array(
		'settings' => 'my_setting1',
		'label' => __('My custom control', 'translation_domain'),
		'section' => 'my_text_section',
		'type' => 'text',
		'priority' => 10,
		'default' => 'some-default-value',
		'transport'	  => 'postMessage' ,
		'js_vars'   => array(
			array(
				'element'  => '.entry-header .entry-title > a',
				'function' => 'html',
			),
			array(
				'element'  => '.site-title > a',
				'function' => 'html',
			),
		)
	));


	Kirki::add_field( 'dimension_config', array(
		'type'        => 'dimension',
		'settings'    => 'dimension_setting',
		'label'       => __( 'Dimension Control', 'my_textdomain' ),
		'section'     => 'my_text_section',
		'default'     => '1.5em',
		'priority'    => 10,
	) );

	Kirki::add_field( 'textarea_config', array(
		'type'     => 'textarea',
		'settings' => 'textarea_setting',
		'label'    => __( 'Textarea Control', 'my_textdomain' ),
		'section'  => 'my_text_section',
		'default'  => esc_attr__( 'This is a defualt value', 'my_textdomain' ),
		'priority' => 10,
	) );

	/*
	 * @Check Box Settings
	 */

	Kirki::add_section('my_checkbox_section', array(
		'title' => __('Checkbox Fields'),
		'description' => __('Add Fields Here'),
		'panel' => 'panel_id', // Not typically needed.
		'priority' => 160,
		'capability' => 'edit_theme_options',
		'theme_supports' => '', // Rarely needed.
	));

	Kirki::add_field( 'my_config_checkbox', array(
		'type'        => 'checkbox',
		'settings'    => 'my_setting2',
		'label'       => __( 'This is the label', 'my_textdomain' ),
		'section'     => 'my_checkbox_section',
		'default'     => '1',
		'priority'    => 10,
	) );

	Kirki::add_field( 'my_config_toggle', array(
		'type'        => 'toggle',
		'settings'    => 'my_setting3',
		'label'       => __( 'This is the toggle label', 'my_textdomain' ),
		'section'     => 'my_checkbox_section',
		'tooltip'	  => __( 'this is one description', 'my_textdomain' ) ,
		'default'     => '1',
		'priority'    => 10,
	) );

	Kirki::add_field( 'my_config_switch', array(
		'type'        => 'switch',
		'settings'    => 'my_setting4',
		'label'       => __( 'This is the switch label', 'my_textdomain' ),
		'choices'     => array(
			'on'  => esc_attr__( 'Enable', 'my_textdomain' ),
			'off' => esc_attr__( 'Disable', 'my_textdomain' ),
		),
		'section'     => 'my_checkbox_section',
		'default'     => '1',
		'description' => __( 'this is one description', 'my_textdomain' ) ,
		'priority'    => 10,
		'transport'	  => 'postMessage'
	) );

	Kirki::add_field( 'multicheck_config', array(
		'type'        => 'multicheck',
		'settings'    => 'multicheck_setting',
		'label'       => esc_attr__( 'Multi Check Control', 'my_textdomain' ),
		'section'     => 'my_checkbox_section',
		'default'     => array('option-1', 'option-3', 'option-4'),
		'priority'    => 10,
		'choices'     => array(
			'option-1' => esc_attr__( 'Option 1', 'my_textdomain' ),
			'option-2' => esc_attr__( 'Option 2', 'my_textdomain' ),
			'option-3' => esc_attr__( 'Option 3', 'my_textdomain' ),
			'option-4' => esc_attr__( 'Option 4', 'my_textdomain' ),
			'option-5' => esc_attr__( 'Option 5', 'my_textdomain' ),
		),
	) );

	/*
	 * @Code Editor Settings
	 */
	Kirki::add_section('text_editor_section', array(
		'title' => __('Code Editor'),
		'description' => __('Add Fields Here'),
		'panel' => 'panel_id', // Not typically needed.
		'priority' => 160,
		'capability' => 'edit_theme_options',
		'theme_supports' => '', // Rarely needed.
	));

	Kirki::add_field( 'code_field_config', array(
		'type'        => 'code',
		'settings'    => 'code_demo',
		'label'       => __( 'Custom Css', 'my_textdomain' ),
		'section'     => 'text_editor_section',
		'default'     => 'body { background: #fff; }',
		'priority'    => 10,
		'transport'	  => 'postMessage' ,
		'choices'     => array(
			'language' => 'css',
			'theme'    => 'monokai',
			'height'   => 250,
		),
	) );

	Kirki::add_field( 'code_html_field_config', array(
		'type'        => 'code',
		'settings'    => 'code_html_demo',
		'label'       => __( 'Custom HTML', 'my_textdomain' ),
		'section'     => 'text_editor_section',
		'default'     => '<div class="test"></div>',
		'priority'    => 10,
		'transport'	  => 'postMessage' ,
		'choices'     => array(
			'language' => 'html',
			'theme'    => 'monokai',
			'height'   => 200,
		),
	) );

	/*
	 * @Color Settings
	 */
	Kirki::add_section('color_section', array(
		'title' => __('Color Fields'),
		'description' => __('Add Fields Here'),
		'panel' => 'panel_id', // Not typically needed.
		'priority' => 160,
		'capability' => 'edit_theme_options',
		'theme_supports' => '', // Rarely needed.
	));


	Kirki::add_field( 'my_color_config', array(
		'type'        => 'color',
		'settings'    => 'color_setting',
		'label'       => __( 'This is the Color label', 'my_textdomain' ),
		'section'     => 'color_section',
		'default'     => '#0088CC',
		'priority'    => 10,
		'alpha'       => true,
		'transport' => 'postMessage',
		'js_vars'   => array(
			array(
				'element'  => 'body',
				'function' => 'css',
				'property' => 'color',
			),
			array(
				'element'  => 'h1, h2, h3, h4',
				'function' => 'css',
				'property' => 'color',
			),
		)
	) );


	Kirki::add_field( 'multicolor_config', array(
		'type'        => 'multicolor',
		'settings'    => 'multicolor_setting',
		'label'       => esc_attr__( 'Multi Color Label', 'my_textdomain' ),
		'section'     => 'color_section',
		'priority'    => 10,
		'choices'     => array(
			'link'    => esc_attr__( 'Color', 'my_textdomain' ),
			'hover'   => esc_attr__( 'Hover', 'my_textdomain' ),
			'active'  => esc_attr__( 'Active', 'my_textdomain' ),
		),
		'default'     => array(
			'link'    => '#0088cc',
			'hover'   => '#00aaff',
			'active'  => '#00ffff',
		),
	) );

	Kirki::add_field( 'palette_config', array(
		'type'        => 'palette',
		'settings'    => 'palette_setting',
		'label'       => __( 'Palette Control', 'my_textdomain' ),
		'section'     => 'color_section',
		'default'     => 'light',
		'priority'    => 10,
		'choices'     => array(
			'light' => array(
				'#ECEFF1',
				'#333333',
				'#4DD0E1',
			),
			'dark' => array(
				'#37474F',
				'#FFFFFF',
				'#F9A825',
			),
		),
	) );


	/*
	 * @Radio Button Settings
	 */
	Kirki::add_section('radio_btn_section', array(
		'title' => __('Radio Fields'),
		'description' => __('Add Fields Here'),
		'panel' => 'panel_id', // Not typically needed.
		'priority' => 160,
		'capability' => 'edit_theme_options',
		'theme_supports' => '', // Rarely needed.
	));

	Kirki::add_field( 'radio_buttonset_config', array(
		'type'        => 'radio-buttonset',
		'settings'    => 'radio_buttonset_setting',
		'label'       => __( 'Radio-Buttonset Control', 'my_textdomain' ),
		'section'     => 'radio_btn_section',
		'default'     => 'red',
		'priority'    => 10,
		'choices'     => array(
			'red'   => esc_attr__( 'Red', 'my_textdomain' ),
			'green' => esc_attr__( 'Green', 'my_textdomain' ),
			'blue'  => esc_attr__( 'Blue', 'my_textdomain' ),
		),
	) );

	Kirki::add_field( 'radio_image_config', array(
		'type'        => 'radio-image',
		'settings'    => 'radio_image_setting',
		'label'       => esc_html__( 'Radio Control', 'my_textdomain' ),
		'section'     => 'radio_btn_section',
		'default'     => 'red',
		'priority'    => 10,
		'choices'     => array(
			'red'   => get_template_directory_uri() . '/assets/images/red.png',
			'green' => get_template_directory_uri() . '/assets/images/green.png',
			'blue'  => get_template_directory_uri() . '/assets/images/blue.png',
		),
	) );

	Kirki::add_field( 'radio_config', array(
		'type'        => 'radio',
		'settings'    => 'radio_setting',
		'label'       => __( 'Radio Control', 'my_textdomain' ),
		'section'     => 'radio_btn_section',
		'default'     => 'red',
		'priority'    => 10,
		'choices'     => array(
			'red'   => esc_attr__( 'Red', 'my_textdomain' ),
			'green' => esc_attr__( 'Green', 'my_textdomain' ),
			'blue'  => esc_attr__( 'Blue', 'my_textdomain' ),
		),
	) );

	/*
	* @Repeater Settings
	*/
	Kirki::add_section('repeater_section', array(
		'title' => __('repeater Section'),
		'description' => __('Add Fields Here'),
		'panel' => 'panel_id', // Not typically needed.
		'priority' => 160,
		'capability' => 'edit_theme_options',
		'theme_supports' => '', // Rarely needed.
	));

	Kirki::add_field( 'repeater_config', array(
		'type'        => 'repeater',
		'label'       => esc_attr__( 'Repeater Control', 'my_textdomain' ),
		'section'     => 'repeater_section',
		'priority'    => 10,
		'settings'    => 'repeater_setting',
		'default'     => array(
			array(
				'link_text' => esc_attr__( 'Kirki Site', 'my_textdomain' ),
				'link_url'  => 'https://kirki.org',
			),
			array(
				'link_text' => esc_attr__( 'Kirki Repository', 'my_textdomain' ),
				'link_url'  => 'https://github.com/aristath/kirki',
			),
		),
		'fields' => array(
			'link_text' => array(
				'type'        => 'text',
				'label'       => esc_attr__( 'Link Text', 'my_textdomain' ),
				'description' => esc_attr__( 'This will be the label for your link', 'my_textdomain' ),
				'default'     => '',
			),
			'link_url' => array(
				'type'        => 'text',
				'label'       => esc_attr__( 'Link URL', 'my_textdomain' ),
				'description' => esc_attr__( 'This will be the link URL', 'my_textdomain' ),
				'default'     => '',
			),
		)
	) );

	/*
	* @Select Settings
	*/
	Kirki::add_section('select_section', array(
		'title' => __('Select Fields'),
		'description' => __('Add Fields Here'),
		'panel' => 'panel_id', // Not typically needed.
		'priority' => 160,
		'capability' => 'edit_theme_options',
		'theme_supports' => '', // Rarely needed.
	));

	Kirki::add_field( 'select_config', array(
		'type'        => 'select',
		'settings'    => 'select_setting',
		'label'       => __( 'This is the label', 'my_textdomain' ),
		'section'     => 'select_section',
		'default'     => 'option-1',
		'priority'    => 10,
		'multiple'    => 1,
		'choices'     => array(
			'option-1' => esc_attr__( 'Option 1', 'my_textdomain' ),
			'option-2' => esc_attr__( 'Option 2', 'my_textdomain' ),
			'option-3' => esc_attr__( 'Option 3', 'my_textdomain' ),
			'option-4' => esc_attr__( 'Option 4', 'my_textdomain' ),
		),
	) );

	Kirki::add_field( 'multi_select_config', array(
		'type'        => 'select',
		'settings'    => 'multi_select_setting',
		'label'       => __( 'This is the label', 'my_textdomain' ),
		'section'     => 'select_section',
		'default'     => array( 'option-1' , 'option-2' ),
		'priority'    => 10,
		'multiple'    => 3,
		'choices'     => array(
			'option-1' => esc_attr__( 'Option 1', 'my_textdomain' ),
			'option-2' => esc_attr__( 'Option 2', 'my_textdomain' ),
			'option-3' => esc_attr__( 'Option 3', 'my_textdomain' ),
			'option-4' => esc_attr__( 'Option 4', 'my_textdomain' ),
		),
	) );

	Kirki::add_field( 'dropdown_pages_config', array(
		'type'        => 'dropdown-pages',
		'settings'    => 'dropdown_pages_setting',
		'label'       => __( 'This is the label', 'my_textdomain' ),
		'section'     => 'select_section',
		'default'     => 42,
		'priority'    => 10,
	) );

	/*
	* @Number Settings
	*/
	Kirki::add_section('number_section', array(
		'title' => __('Number Fields'),
		'description' => __('Add Fields Here'),
		'panel' => 'panel_id', // Not typically needed.
		'priority' => 160,
		'capability' => 'edit_theme_options',
		'theme_supports' => '', // Rarely needed.
	));

	Kirki::add_field( 'number_config', array(
		'type'        => 'number',
		'settings'    => 'number_setting',
		'label'       => esc_attr__( 'This is the label', 'my_textdomain' ),
		'section'     => 'number_section',
		'default'     => 42,
		'choices'     => array(
			'min'  => 0,
			'max'  => 30,
			'step' => 1,
		),
	) );

	Kirki::add_field( 'slider_config', array(
		'type'        => 'slider',
		'settings'    => 'slider_setting',
		'label'       => esc_attr__( 'This is the label', 'my_textdomain' ),
		'section'     => 'number_section',
		'default'     => 42,
		'choices'     => array(
			'min'  => '0',
			'max'  => '100',
			'step' => '1',
		),
	) );

	Kirki::add_field( 'spacing_config', array(
		'type'        => 'spacing',
		'settings'    => 'spacing_setting',
		'label'       => __( 'Spacing Control', 'my_textdomain' ),
		'section'     => 'number_section',
		'default'     => array(
			'top'    => '1.5em',
			'bottom' => '10px',
			'left'   => '40%',
			'right'  => '2rem',
		),
		'priority'    => 10,
	) );

	Kirki::add_field( 'spacing2_config', array(
		'type'        => 'spacing',
		'settings'    => 'spacing2_setting',
		'label'       => __( 'Spacing2 Control', 'my_textdomain' ),
		'section'     => 'number_section',
		'priority'    => 10,
		'default'     => array(
			'top'    => '1.5em',
			'bottom' => '10px',
		),
	) );

	/*
	* @Icon Settings
	*/
	Kirki::add_section('icon_fields_section', array(
		'title' => __('Icons Fields'),
		'description' => __('Add Fields Here'),
		'panel' => 'panel_id', // Not typically needed.
		'priority' => 160,
		'capability' => 'edit_theme_options',
		'theme_supports' => '', // Rarely needed.
	));

	Kirki::add_field( 'icon_config', array(
		'type'     => 'dashicons',
		'settings' => 'icon_setting',
		'label'    => __( 'Dashicons Control', 'my_textdomain' ),
		'section'  => 'icon_fields_section',
		'default'  => 'menu',
		'priority' => 10,
	) );

	/*
	* @Image Settings
	*/
	Kirki::add_section('image_fields_section', array(
		'title' => __('Images Fields'),
		'description' => __('Add Fields Here'),
		'panel' => 'panel_id', // Not typically needed.
		'priority' => 160,
		'capability' => 'edit_theme_options',
		'theme_supports' => '', // Rarely needed.
	));

	Kirki::add_field( 'image_config', array(
		'type'        => 'image',
		'settings'    => 'image_demo',
		'label'       => __( 'This is the label', 'my_textdomain' ),
		'description' => __( 'This is the control description', 'my_textdomain' ),
		'help'        => __( 'This is some extra help text.', 'my_textdomain' ),
		'section'     => 'image_fields_section',
		'default'     => '',
		'priority'    => 10,
	) );

	Kirki::add_field( 'upload_config', array(
		'type'        => 'upload',
		'settings'    => 'upload_demo',
		'label'       => __( 'This is the label', 'my_textdomain' ),
		'description' => __( 'This is the control description', 'my_textdomain' ),
		'help'        => __( 'This is some extra help text.', 'my_textdomain' ),
		'section'     => 'image_fields_section',
		'default'     => '',
		'priority'    => 10,
	) );

	/*
	* @typography Settings
	*/
	Kirki::add_section('typography_section', array(
		'title' => __('Typography Section'),
		'description' => __('Add Fields Here'),
		'panel' => 'panel_id', // Not typically needed.
		'priority' => 160,
		'capability' => 'edit_theme_options',
		'theme_supports' => '', // Rarely needed.
	));

	Kirki::add_field( 'typography_config', array(
		'type'        => 'typography',
		'settings'    => 'typography_setting',
		'label'       => esc_attr__( 'Control Label', 'kirki' ),
		'section'     => 'typography_section',
		'default'     => array(
			'font-family'    => 'Roboto',
			'variant'        => 'regular',
			'font-size'      => '14px',
			'line-height'    => '1.5',
			'letter-spacing' => '0',
			'subsets'        => array( 'latin-ext' ),
			'color'          => '#333333',
			'text-transform' => 'none',
			'text-align'     => 'left'
		),
		'priority'    => 10,
		'output'      => array(
			array(
				'element' => 'body',
			),
		),
	) );

	/*
	* @Sortable Settings
	*/
	Kirki::add_section('sortable_section', array(
		'title' => __('Sortable Fields'),
		'description' => __('Add Fields Here'),
		'panel' => 'panel_id', // Not typically needed.
		'priority' => 160,
		'capability' => 'edit_theme_options',
		'theme_supports' => '', // Rarely needed.
	));

	Kirki::add_field( 'sortable_config', array(
		'type'        => 'sortable',
		'settings'    => 'sortable_setting',
		'label'       => __( 'This is the label', 'my_textdomain' ),
		'section'     => 'sortable_section',
		'default'     => array(
			'option3',
			'option1',
			'option4'
		),
		'choices'     => array(
			'option1' => esc_attr__( 'Option 1', 'kirki' ),
			'option2' => esc_attr__( 'Option 2', 'kirki' ),
			'option3' => esc_attr__( 'Option 3', 'kirki' ),
			'option4' => esc_attr__( 'Option 4', 'kirki' ),
			'option5' => esc_attr__( 'Option 5', 'kirki' ),
			'option6' => esc_attr__( 'Option 6', 'kirki' ),
		),
		'priority'    => 10,
	) );

	/*
	* @Custom Settings
	*/
	Kirki::add_section('custom_fields_section', array(
		'title' => __('Custom Fields'),
		'description' => __('Add Fields Here'),
		'panel' => 'panel_id', // Not typically needed.
		'priority' => 160,
		'capability' => 'edit_theme_options',
		'theme_supports' => '', // Rarely needed.
	));

	Kirki::add_field( 'my_custom_config', array(
		'type'        => 'custom',
		'settings'    => 'custom_setting',
		'label'       => __( 'This is the label', 'my_textdomain' ),
		'section'     => 'custom_fields_section',
		'default'     => '<div style="padding: 30px;background-color: #333; color: #fff; border-radius: 50px;">' . esc_html__( 'You can enter custom markup in this control and use it however you want', 'my_textdomain' ) . '</div>',
		'priority'    => 10,
	) );


}

if( class_exists( 'Kirki' ) )
	add_action("init" , 'twentyfifteen_theme_Kirki_options' );
