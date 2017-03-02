<?php
/**
 * Color Scheme Class
 *
 * Implements Color Scheme management for site editor page builder and all of wp themes and plugin
 * maybe overridden in any theme
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class SiteEditorColorOptions
 *
 * @override :
 * 1. for override Customize Color Settings using the 'sed_customize_color_settings' filter.
 * 2. for override built-in Color Schemes using the 'sed_color_schemes' filter.
 * 3. for override pagebuilder color css and your theme color css using the 'sed_theme_color_css' filter.
 *
 */
class SiteEditorColorOptions extends SiteEditorOptionsCategory{

    /**
     * Capability required to edit this field.
     *
     * @access public
     * @var string
     */
    public $capability = 'edit_theme_options';

    /**
     * this field group use :
     *  "general" || "style-editor" || "module" || "post"
     *
     * @access private
     * @var array
     */
    protected $option_group = 'sed_color_options';

    /**
     * default option type
     *
     * @access public
     * @var array
     */
    public $option_type  = "theme_mod";

    /**
     * default option type
     *
     * @access protected
     * @var array
     */
    protected $category  = "theme-settings";

    /**
     * prefix for controls ids for prevent conflict
     *
     * @var string
     * @access public
     */
    public $control_prefix = 'sed_color_options';

    /**
     * SiteEditorThemeOptions constructor.
     */
    public function __construct(){

        $this->title = __("Color Options" , "site-editor");

        $this->description = __("Color Options For Wordpress Themes And Site Editor Modules" , "site-editor");

        add_filter( "{$this->option_group}_panels_filter" , array( $this , 'register_default_panels' ) );

        add_filter( "{$this->option_group}_fields_filter" , array( $this , 'register_default_fields' ) );

        add_action( "sed_editor_init"                     , array( $this , 'add_toolbar_elements' ) );

        if( site_editor_app_on() ) {
            add_action( 'wp_footer'                          , array($this, 'color_scheme_css_template') );
            add_action( 'wp_footer'                          , array($this, 'print_color_scheme_settings') );
        }

        add_action( 'wp_enqueue_scripts'                  , array( $this , 'print_color_scheme_css' ) , 100000 );

        parent::__construct();

    }

    /**
     * add element to SiteEditor toolbar
     */
    public function add_toolbar_elements(){
        global $site_editor_app;

        $site_editor_app->toolbar->add_element_group( "layout" , "color-font" , __("Color & Font","site-editor") );

        $site_editor_app->toolbar->add_element(
            "layout" ,
            "color-font" ,
            "color-font" ,
            $this->title ,
            "color_font_element" ,     //$func_action
            "" ,                //icon
            "" ,  //$capability=
            array(  ),  //"class"  => "btn_default3"
            array( "row" => 1 ,"rowspan" => 2 ),
            array('module' => 'options-engine' , 'file' => 'color_options.php'),
            //array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
            'all' ,
            array(),
            array()
        );

    }

    /**
     * Register Site Default Panels
     */
    public function register_default_panels( $panels )
    {

        $panels['colors_customize_panel'] = array(
            'title'             =>  __('Customize Color Scheme',"site-editor")  ,
            'capability'        => 'edit_theme_options' ,
            'type'              => 'default' ,
            'description'       => '' ,
            'priority'          => 8 ,
            'dependency' => array(
                'queries'  =>  array(
                    array(
                        "key"       => "color_scheme_type" ,
                        "value"     => 'customize' ,
                        "compare"   => "==="
                    )
                )
            )
        );

        return $panels;
    }

    /**
     * Register Site Default Fields
     */
    public function register_default_fields( $fields ){
        
        $new_fields = array(

            'color_scheme_type' => array(
                'setting_id'        => "sed_color_scheme_type" ,
                "type"              => "radio-buttonset" ,
                "label"             => __("Color Scheme Type", "site-editor"),
                "description"       => __("Select Color Scheme Type", "site-editor"),
                'default'           => 'skin',
                "choices"           =>  array(
                    "customize"         =>    __( "Customize" , "site-editor" ) ,
                    "skin"              =>    __( "Built-in Skins" , "site-editor" )
                ),
                //'panel'             => 'general_page_style' ,
                'transport'         => 'postMessage' ,
                'priority'          => 5
            ),

            'color_scheme_skin' => array(
                'setting_id'        => "sed_color_scheme_skin" ,
                'type'              => 'custom',
                'js_type'           => 'dropdown',
                'default'           => 'default',
                'has_border_box'    => true ,
                'custom_template'   => $this->color_scheme_skins_template() ,
                'js_params'         =>  array(
                    'options_selector'    => '.sed-palette-item',
                    'selected_class'      => 'selected-palette'
                ),
                'transport'         => 'postMessage' ,
                'priority'          => 6 ,
                'dependency' => array(
                    'queries'  =>  array(
                        array(
                            "key"       => "color_scheme_type" ,
                            "value"     => 'skin' ,
                            "compare"   => "==="
                        )
                    )
                )
            ),

        );

        $customize_color_settings = $this->get_customize_color_settings();

        foreach ( $customize_color_settings AS $key => $options ){

            $new_fields[$key] = array_merge(

                array(
                    'type'              => 'color',
                    'priority'          => 10,
                    'default'           => '',
                    'transport'         => 'postMessage' ,
                    'panel'             =>  'colors_customize_panel' ,
                ),

                $options
            );

        }

        return array_merge( $fields , $new_fields );

    }

    public function get_customize_color_settings(){

        return apply_filters( 'sed_customize_color_settings' , array(

                'first_main_color' => array(
                    'setting_id'        => 'sed_first_main_color',
                    'label'             => __('First Main Color', 'site-editor'),
                    "description"       => __("Choose the most dominant theme color", "site-editor"),
                    'default'           => '#1a1a1a',
                ),

                'second_main_color' => array(
                    'setting_id'        => 'sed_second_main_color',
                    'label'             => __('Second Main Color', 'site-editor'),
                    "description"       => __("Choose the second most dominant theme color", "site-editor"),
                    'default'           => '#626262',
                ),

                'background_color' => array(
                    'setting_id'        => 'sed_body_background_color',
                    'label'             => __('Background Color', 'site-editor'),
                    "description"       => __("Background Color for body", "site-editor"),
                    'default'           => '#1a1a1a',
                ),

                'base_color' => array(
                    'setting_id'        => 'sed_base_color',
                    'label'             => __('Base Color', 'site-editor'),
                    "description"       => __("using for border color, gradients, heading background color and ...", "site-editor"),
                    'default'           => '#cccccc',
                ),

                'main_text_color' => array(
                    'setting_id'        => 'sed_main_text_color',
                    'label'             => __('Main Text Color', 'site-editor'),
                    "description"       => __("Choose the most dominant theme text color", "site-editor"),
                    'default'           => '#1a1a1a',
                ),

                'page_background_color' => array(
                    'setting_id'        => 'sed_page_background_color',
                    'label'             => __('Page Background Color', 'site-editor'),
                    "description"       => __("Background Color for body", "site-editor"),
                    'default'           => '#ffffff',
                ),

                'secondary_text_color' => array(
                    'setting_id'        => 'sed_secondary_text_color',
                    'label'             => __('Secondary Text Color', 'site-editor'),
                    "description"       => __("Choose the second most dominant theme text color", "site-editor"),
                    'default'           => '#686868',
                ),

                'link_color' => array(
                    'setting_id'        => 'sed_link_color',
                    'label'             => __('Link Color', 'site-editor'),
                    "description"       => __("Choose the theme Links color", "site-editor"),
                    'default'           => '#007acc',
                ),

            )
        );

    }

    public function color_scheme_skins_template(){

        $color_schemes       = $this->get_color_schemes();

        $color_scheme        = "default";//get_theme_mod( 'color_scheme', 'default' );
        
        $control_id          = $this->control_prefix . "_sed_color_scheme_skin";

        ob_start();

        include dirname( dirname( __FILE__ ) ) . "/view/color-palette.php";

        $template = ob_get_clean();

        return $template;

    }

    /**
     * Registers color schemes for Site Editor
     *
     * Can be filtered with {@see 'sed_color_schemes'}.
     *
     * The order of colors in a colors array:
     * 1. First Main Color
     * 2. secondary Main Color
     * 3. Main Background Color.
     * 4. base color( Like Border color , Heading Background Color , ... )
     * 5. Main Text Color
     * 6. Page Background Color.
     * 7. Secondary Text Color
     * 8. Link Color.
     *
     * @return array An associative array of color scheme options.
     */
    public function get_color_schemes() {

        return apply_filters( 'sed_color_schemes', array(

            'brown' => array(
                'label'  => __( 'Brown', 'site-editor' ),
                'colors' => array(
                    '#B94D2D',
                    '#AED429',
                    '#FFFFFF',
                    '#CCCCCC',
                    '#000000',
                    'transparent' ,
                    '#B94D2D' ,
                    '#B94D2D'
                ),
            ),

            'orange' => array(
                'label'  => __( 'Orange', 'site-editor' ),
                'colors' => array(
                    '#FF6600',
                    '#66CC33',
                    '#FFFFFF',
                    '#CCCCCC',
                    '#000000',
                    'transparent' ,
                    '#FF6600' ,
                    '#FF6600'
                ),
            ),

            'green' => array(
                'label'  => __( 'Green', 'site-editor' ),
                'colors' => array(
                    '#a0ce4e',
                    '#EB2D1C',
                    '#FFFFFF',
                    '#CCCCCC',
                    '#000000',
                    'transparent' ,
                    '#a0ce4e' ,
                    '#a0ce4e'
                ),
            ),

            'blue' => array(
                'label'  => __( 'Blue', 'site-editor' ),
                'colors' => array(
                    '#009BF5',
                    '#f6c113',
                    '#FFFFFF',
                    '#CCCCCC',
                    '#000000',
                    'transparent' ,
                    '#009BF5' ,
                    '#009BF5'
                ),
            ),

            'purple' => array(
                'label'  => __( 'Purple', 'site-editor' ),
                'colors' => array(
                    '#C0029A',
                    '#00C5CE',
                    '#FFFFFF',
                    '#CCCCCC',
                    '#000000',
                    'transparent' ,
                    '#C0029A' ,
                    '#C0029A'
                ),
            ),

            'blue_oil' => array(
                'label'  => __( 'Blue Oil', 'site-editor' ),
                'colors' => array(
                    '#6683A3',
                    '#B94D2D',
                    '#FFFFFF',
                    '#CCCCCC',
                    '#000000',
                    'transparent' ,
                    '#6683A3' ,
                    '#6683A3'
                ),
            ),

            'dark' => array(
                'label'  => __( 'Dark', 'site-editor' ),
                'colors' => array(
                    '#262626',
                    '#1a1a1a',
                    '#9adffd',
                    '#e5e5e5',
                    '#c1c1c1',
                    'transparent' ,
                    '#262626' ,
                    '#262626'
                ),
            ),

        ) );
    }

    public function get_scheme(){

        $color_scheme_type = get_theme_mod( 'sed_color_scheme_type', 'skin' );

        if( $color_scheme_type == "skin" ){

            return get_theme_mod( 'sed_color_scheme_skin', 'default' );

        }

        return $color_scheme_type;

    }

    public function get_customize_scheme(){

        $customize_color_settings = $this->get_customize_color_settings();

        $customize_colors = array();

        foreach ( $customize_color_settings As $key => $options ){

            if( !isset( $options['setting_id'] ) )
                continue;

            $default = isset( $options['default'] ) ? $options['default'] : '';

            $customize_colors[] = get_theme_mod( $options['setting_id'] , $default );

        }

        return apply_filters( 'sed_customize_color_scheme' , $customize_colors );

    }

    /**
     * Retrieves the current color scheme.
     *
     * @return array An associative array of either the current or default color scheme HEX values.
     */
    public function get_color_scheme() {

        $color_scheme_option    =   $this->get_scheme();

        if( $color_scheme_option == "customize" ){

            return $this->get_customize_scheme();

        }

        $color_schemes          = $this->get_color_schemes();

        if ( array_key_exists( $color_scheme_option, $color_schemes ) ) {
            return $color_schemes[ $color_scheme_option ]['colors'];
        }

        $customize_color_settings = $this->get_customize_color_settings();

        $defaults = array();

        foreach ( $customize_color_settings As $key => $options ){

            $defaults[] = isset( $options['default'] ) ? $options['default'] : '';

        }

        return $defaults;
    }

    public function print_color_scheme_css() {

        global $sed_dynamic_css_string;

        $color_scheme_option    =   $this->get_scheme();

        // Don't do anything if the default color scheme is selected.
        if ( 'default' === $color_scheme_option ) {
            return;
        }

        $color_scheme = $this->get_color_scheme();

        $customize_color_settings = $this->get_customize_color_settings();

        $colors = array();

        $i = 0;

        foreach ( $customize_color_settings As $key => $options ){

            $colors[$key] = isset( $color_scheme[$i] ) ? $color_scheme[$i] : "";

            $i++;

        }

        $colors = apply_filters( 'sed_colors_for_output' , $colors , $this );

        $color_scheme_css = $this->get_color_scheme_css( $colors );

        $sed_dynamic_css_string .= $color_scheme_css;
    }

    /**
     * Outputs an Underscore template for generating CSS for the color scheme.
     *
     * The template generates the css dynamically for instant display in the
     * Site Editor preview.
     */
    public function color_scheme_css_template(){

        $customize_color_settings = $this->get_customize_color_settings();

        $colors = array();

        foreach ( $customize_color_settings As $key => $options ){

            $colors[$key] = "{{ $key }}";

        }

        ?>
        <script type="text/html" id="tmpl-sed-color-scheme">
            <?php echo $this->get_color_scheme_css( $colors ); ?>
        </script>
        <?php

    }

    public function print_color_scheme_settings() {

        $customize_color_settings = $this->get_customize_color_settings();

        $settings = array();

        $settings['skin'] = array();

        $color_schemes = $this->get_color_schemes();

        foreach ( $color_schemes As $key => $options ){

            if( !isset( $options['colors'] ) )
                continue;

            $settings['skin'][$key] = $options['colors'];

        }

        $settings['customize'] = array();

        foreach ( $customize_color_settings As $key => $options ){

            if( !isset( $options['setting_id'] ) )
                continue;

            $settings['customize'][$key] = $options['setting_id'];

        }

        ?>
        <script type="text/javascript">
            var _sedColorSchemeSettings = <?php echo wp_json_encode($settings); ?>;
        </script>
        <?php
    }

    /**
     * Returns CSS for the color schemes.
     *
     * @param array $colors Color scheme colors.
     * @return string Color scheme CSS.
     */
    public function get_color_scheme_css( $colors ) {

        $customize_color_settings = $this->get_customize_color_settings();

        $default_colors = array();

        foreach ( $customize_color_settings As $key => $options ){

            $default_colors[$key] = isset( $options['default'] ) ? $options['default'] : '';

        }

        $colors = wp_parse_args( $colors, $default_colors );

        $css = <<<CSS
	/* Color Scheme */

	/* Background Color */
	body {
		background-color: {$colors['background_color']};
	}

	/* Page Background Color */
	.site {
		background-color: {$colors['page_background_color']};
	}

	mark,
	ins,
	button,
	button[disabled]:hover,
	button[disabled]:focus,
	input[type="button"],
	input[type="button"][disabled]:hover,
	input[type="button"][disabled]:focus,
	input[type="reset"],
	input[type="reset"][disabled]:hover,
	input[type="reset"][disabled]:focus,
	input[type="submit"],
	input[type="submit"][disabled]:hover,
	input[type="submit"][disabled]:focus,
	.menu-toggle.toggled-on,
	.menu-toggle.toggled-on:hover,
	.menu-toggle.toggled-on:focus,
	.pagination .prev,
	.pagination .next,
	.pagination .prev:hover,
	.pagination .prev:focus,
	.pagination .next:hover,
	.pagination .next:focus,
	.pagination .nav-links:before,
	.pagination .nav-links:after,
	.widget_calendar tbody a,
	.widget_calendar tbody a:hover,
	.widget_calendar tbody a:focus,
	.page-links a,
	.page-links a:hover,
	.page-links a:focus {
		color: {$colors['page_background_color']};
	}

	/* Link Color */
	.menu-toggle:hover,
	.menu-toggle:focus,
	a,
	.main-navigation a:hover,
	.main-navigation a:focus,
	.dropdown-toggle:hover,
	.dropdown-toggle:focus,
	.social-navigation a:hover:before,
	.social-navigation a:focus:before,
	.post-navigation a:hover .post-title,
	.post-navigation a:focus .post-title,
	.tagcloud a:hover,
	.tagcloud a:focus,
	.site-branding .site-title a:hover,
	.site-branding .site-title a:focus,
	.entry-title a:hover,
	.entry-title a:focus,
	.entry-footer a:hover,
	.entry-footer a:focus,
	.comment-metadata a:hover,
	.comment-metadata a:focus,
	.pingback .comment-edit-link:hover,
	.pingback .comment-edit-link:focus,
	.comment-reply-link,
	.comment-reply-link:hover,
	.comment-reply-link:focus,
	.required,
	.site-info a:hover,
	.site-info a:focus {
		color: {$colors['link_color']};
	}

	mark,
	ins,
	button:hover,
	button:focus,
	input[type="button"]:hover,
	input[type="button"]:focus,
	input[type="reset"]:hover,
	input[type="reset"]:focus,
	input[type="submit"]:hover,
	input[type="submit"]:focus,
	.pagination .prev:hover,
	.pagination .prev:focus,
	.pagination .next:hover,
	.pagination .next:focus,
	.widget_calendar tbody a,
	.page-links a:hover,
	.page-links a:focus {
		background-color: {$colors['link_color']};
	}

	input[type="date"]:focus,
	input[type="time"]:focus,
	input[type="datetime-local"]:focus,
	input[type="week"]:focus,
	input[type="month"]:focus,
	input[type="text"]:focus,
	input[type="email"]:focus,
	input[type="url"]:focus,
	input[type="password"]:focus,
	input[type="search"]:focus,
	input[type="tel"]:focus,
	input[type="number"]:focus,
	textarea:focus,
	.tagcloud a:hover,
	.tagcloud a:focus,
	.menu-toggle:hover,
	.menu-toggle:focus {
		border-color: {$colors['link_color']};
	}

	/* Main Text Color */
	body,
	blockquote cite,
	blockquote small,
	.main-navigation a,
	.menu-toggle,
	.dropdown-toggle,
	.social-navigation a,
	.post-navigation a,
	.pagination a:hover,
	.pagination a:focus,
	.widget-title a,
	.site-branding .site-title a,
	.entry-title a,
	.page-links > .page-links-title,
	.comment-author,
	.comment-reply-title small a:hover,
	.comment-reply-title small a:focus {
		color: {$colors['main_text_color']};
	}

	blockquote,
	.menu-toggle.toggled-on,
	.menu-toggle.toggled-on:hover,
	.menu-toggle.toggled-on:focus,
	.post-navigation,
	.post-navigation div + div,
	.pagination,
	.widget,
	.page-header,
	.page-links a,
	.comments-title,
	.comment-reply-title {
		border-color: {$colors['main_text_color']};
	}

	button,
	button[disabled]:hover,
	button[disabled]:focus,
	input[type="button"],
	input[type="button"][disabled]:hover,
	input[type="button"][disabled]:focus,
	input[type="reset"],
	input[type="reset"][disabled]:hover,
	input[type="reset"][disabled]:focus,
	input[type="submit"],
	input[type="submit"][disabled]:hover,
	input[type="submit"][disabled]:focus,
	.menu-toggle.toggled-on,
	.menu-toggle.toggled-on:hover,
	.menu-toggle.toggled-on:focus,
	.pagination:before,
	.pagination:after,
	.pagination .prev,
	.pagination .next,
	.page-links a {
		background-color: {$colors['main_text_color']};
	}

	/* Secondary Text Color */

	/**
	 * IE8 and earlier will drop any block with CSS3 selectors.
	 * Do not combine these styles with the next block.
	 */
	body:not(.search-results) .entry-summary {
		color: {$colors['secondary_text_color']};
	}

	blockquote,
	.post-password-form label,
	a:hover,
	a:focus,
	a:active,
	.post-navigation .meta-nav,
	.image-navigation,
	.comment-navigation,
	.widget_recent_entries .post-date,
	.widget_rss .rss-date,
	.widget_rss cite,
	.site-description,
	.author-bio,
	.entry-footer,
	.entry-footer a,
	.sticky-post,
	.taxonomy-description,
	.entry-caption,
	.comment-metadata,
	.pingback .edit-link,
	.comment-metadata a,
	.pingback .comment-edit-link,
	.comment-form label,
	.comment-notes,
	.comment-awaiting-moderation,
	.logged-in-as,
	.form-allowed-tags,
	.site-info,
	.site-info a,
	.wp-caption .wp-caption-text,
	.gallery-caption,
	.widecolumn label,
	.widecolumn .mu_register label {
		color: {$colors['secondary_text_color']};
	}

	.widget_calendar tbody a:hover,
	.widget_calendar tbody a:focus {
		background-color: {$colors['secondary_text_color']};
	}

	/* Border Color */
	fieldset,
	pre,
	abbr,
	acronym,
	table,
	th,
	td,
	input[type="date"],
	input[type="time"],
	input[type="datetime-local"],
	input[type="week"],
	input[type="month"],
	input[type="text"],
	input[type="email"],
	input[type="url"],
	input[type="password"],
	input[type="search"],
	input[type="tel"],
	input[type="number"],
	textarea,
	.main-navigation li,
	.main-navigation .primary-menu,
	.menu-toggle,
	.dropdown-toggle:after,
	.social-navigation a,
	.image-navigation,
	.comment-navigation,
	.tagcloud a,
	.entry-content,
	.entry-summary,
	.page-links a,
	.page-links > span,
	.comment-list article,
	.comment-list .pingback,
	.comment-list .trackback,
	.comment-reply-link,
	.no-comments,
	.widecolumn .mu_register .mu_alert {
		border-color: {$colors['main_text_color']}; /* Fallback for IE7 and IE8 */
		border-color: {$colors['base_color']};
	}

	hr,
	code {
		background-color: {$colors['main_text_color']}; /* Fallback for IE7 and IE8 */
		background-color: {$colors['base_color']};
	}

	@media screen and (min-width: 56.875em) {
		.main-navigation li:hover > a,
		.main-navigation li.focus > a {
			color: {$colors['link_color']};
		}

		.main-navigation ul ul,
		.main-navigation ul ul li {
			border-color: {$colors['base_color']};
		}

		.main-navigation ul ul:before {
			border-top-color: {$colors['base_color']};
			border-bottom-color: {$colors['base_color']};
		}

		.main-navigation ul ul li {
			background-color: {$colors['page_background_color']};
		}

		.main-navigation ul ul:after {
			border-top-color: {$colors['page_background_color']};
			border-bottom-color: {$colors['page_background_color']};
		}
	}

CSS;

        $color_scheme_css = apply_filters( 'sed_theme_color_css' , $css , $this , $colors );

        return $color_scheme_css;

    }

}

