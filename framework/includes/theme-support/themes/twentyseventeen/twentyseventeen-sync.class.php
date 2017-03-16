<?php
/**
 * Twenty seventeen Theme Sync class
 *
 * @package SiteEditor
 * @subpackage framework
 * @since 1.0.0
 */

/**
 * SiteEditor Twenty seventeen Theme Sync class.
 *
 * Sync Twenty seventeen WordPress theme with SiteEditor Framework
 *
 * @since 1.0.0
 */

class SiteEditorTwentyseventeenThemeSync{

    /**
     * @access protected
     * @var object instance of SiteEditorThemeSupport Class
     */
    protected $theme_support;

    /**
     * SiteEditorTwentyseventeenThemeSync constructor.
     * @param $theme_support object instance of SiteEditorThemeSupport Class
     */
    public function __construct( $theme_support ) {

        $this->theme_support = $theme_support;
        
        add_action( "plugins_loaded" , array( $this , 'add_features' ) , 9000  );

        add_action( "sed_static_module_register" , array( $this , 'register_static_modules' ) , 10 , 1 );

        //add_filter( 'template_include', array(&$this,'template_chooser') , 99 );

        //add_filter( 'sed_header_wrapping_template', array( $this , 'get_header' ) , 100 , 1 );

        add_filter( 'sed_theme_color_css' , array( $this , 'theme_color_css' ) , 100 , 3 );

        //add_filter( 'sed_color_schemes' , array( $this , 'color_schemes' ) );

        add_filter( 'sed_customize_color_settings' , array( $this , 'color_settings' ) );

        add_filter( "sed_color_options_panels_filter" , array( $this , 'register_color_panels' ) );

    }

    /**
     * Add several SiteEditor theme framework features.
     *
     * @since 1.0.0
     * @access public
     */
    public function add_features(){

        sed_add_theme_support( "site_layout_feature" , array(
            "default_page_length"   =>  'wide' ,
            "default_sheet_width"   =>  '1100px' ,
            'selector'              =>  '.site-content-contain'
        ) );

        sed_add_theme_support( 'sed_custom_background' , array(
            "default_color "        =>  '#ffffff' ,
            'selector'              =>  'body'
        ) );

    }

    /**
     * Register Static Modules
     *
     * @since 1.0.0
     * @access public
     */
    public function register_static_modules( $manager ){

        require_once dirname( __FILE__ ) . "/modules/header.php";

        $manager->add_static_module( new TwentyseventeenHeaderStaticModule( $manager , 'twenty_seventeen_header' , array(
                'title'                 => __("Twentyseventeen Header" , "site-editor") ,
                'description'           => __("Twentyseventeen Header Module" , "site-editor")
            )
        ));

        require_once dirname( __FILE__ ) . "/modules/footer.php";

        $manager->add_static_module( new TwentyseventeenFooterStaticModule( $manager , 'twenty_seventeen_footer' , array(
                'title'         => __("Twentyseventeen Footer" , "site-editor") ,
                'description'   => __("Twentyseventeen Footer Module" , "site-editor") ,
            )
        ));

    }

    /**
     * Register Static Modules
     *
     * @since 1.0.0
     * @access public
     */
    public function is_page( $module ){

        return is_page();

    }

    public function template_chooser( $template ) {

        $overridden_template = locate_template( 'header.php' );

        var_dump( $overridden_template );

        return $template;

    }

    public function get_header( $template ) {

        return dirname( __FILE__ ) . '/header.php';

    }

    public function register_color_panels( $panels ){

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

    public function color_settings( $settings ){

        $new_settings = array(

            'border_radius' => array(
                'setting_id'        => 'twentyseventeen_border_radius',
                'type'              => 'dimension',
                'label'             => __('Border Radius', 'site-editor'),
                "description"       => __("Border Radius for theme", "site-editor") ,
                'priority'          => 10,
                'default'           => '0px',
                'panel'             => 'colors_customize_panel' ,
            ),

        );

        return array_merge( $settings , $new_settings );

    }

    public function theme_color_css( $css , $color_scheme , $colors ){

        //extract( $colors );

        $css .= <<<CSS
        
    .btn {
        border-radius: {$colors['border_radius']} !important;
    }
        
/* Color Scheme */




.color-scheme button,
.color-scheme input[type="button"],
.color-scheme input[type="submit"],
.color-scheme .entry-footer .edit-link a.post-edit-link {
    background-color: {$colors['first_main_color']};
}

.color-scheme a:hover,
.color-scheme a:active,
.color-scheme .entry-content a:focus,
.color-scheme .entry-content a:hover,
.color-scheme .entry-summary a:focus,
.color-scheme .entry-summary a:hover,
.color-scheme .widget a:focus,
.color-scheme .widget a:hover,
.color-scheme .site-footer .widget-area a:focus,
.color-scheme .site-footer .widget-area a:hover,
.color-scheme .posts-navigation a:focus,
.color-scheme .posts-navigation a:hover,
.color-scheme .comment-metadata a:focus,
.color-scheme .comment-metadata a:hover,
.color-scheme .comment-metadata a.comment-edit-link:focus,
.color-scheme .comment-metadata a.comment-edit-link:hover,
.color-scheme .comment-reply-link:focus,
.color-scheme .comment-reply-link:hover,
.color-scheme .widget_authors a:focus strong,
.color-scheme .widget_authors a:hover strong,
.color-scheme .entry-title a:focus,
.color-scheme .entry-title a:hover,
.color-scheme .entry-meta a:focus,
.color-scheme .entry-meta a:hover,
.color-scheme.blog .entry-meta a.post-edit-link:focus,
.color-scheme.blog .entry-meta a.post-edit-link:hover,
.color-scheme.archive .entry-meta a.post-edit-link:focus,
.color-scheme.archive .entry-meta a.post-edit-link:hover,
.color-scheme.search .entry-meta a.post-edit-link:focus,
.color-scheme.search .entry-meta a.post-edit-link:hover,
.color-scheme .page-links a:focus .page-number,
.color-scheme .page-links a:hover .page-number,
.color-scheme .entry-footer .cat-links a:focus,
.color-scheme .entry-footer .cat-links a:hover,
.color-scheme .entry-footer .tags-links a:focus,
.color-scheme .entry-footer .tags-links a:hover,
.color-scheme .post-navigation a:focus,
.color-scheme .post-navigation a:hover,
.color-scheme .pagination a:not(.prev):not(.next):focus,
.color-scheme .pagination a:not(.prev):not(.next):hover,
.color-scheme .comments-pagination a:not(.prev):not(.next):focus,
.color-scheme .comments-pagination a:not(.prev):not(.next):hover,
.color-scheme .logged-in-as a:focus,
.color-scheme .logged-in-as a:hover,
.color-scheme a:focus .nav-title,
.color-scheme a:hover .nav-title,
.color-scheme .edit-link a:focus,
.color-scheme .edit-link a:hover,
.color-scheme .site-info a:focus,
.color-scheme .site-info a:hover,
.color-scheme .widget .widget-title a:focus,
.color-scheme .widget .widget-title a:hover,
.color-scheme .widget ul li a:focus,
.color-scheme .widget ul li a:hover {
    color: {$colors['main_text_color']};
}

.color-scheme .entry-content a:focus,
.color-scheme .entry-content a:hover,
.color-scheme .entry-summary a:focus,
.color-scheme .entry-summary a:hover,
.color-scheme .widget a:focus,
.color-scheme .widget a:hover,
.color-scheme .site-footer .widget-area a:focus,
.color-scheme .site-footer .widget-area a:hover,
.color-scheme .posts-navigation a:focus,
.color-scheme .posts-navigation a:hover,
.color-scheme .comment-metadata a:focus,
.color-scheme .comment-metadata a:hover,
.color-scheme .comment-metadata a.comment-edit-link:focus,
.color-scheme .comment-metadata a.comment-edit-link:hover,
.color-scheme .comment-reply-link:focus,
.color-scheme .comment-reply-link:hover,
.color-scheme .widget_authors a:focus strong,
.color-scheme .widget_authors a:hover strong,
.color-scheme .entry-title a:focus,
.color-scheme .entry-title a:hover,
.color-scheme .entry-meta a:focus,
.color-scheme .entry-meta a:hover,
.color-scheme.blog .entry-meta a.post-edit-link:focus,
.color-scheme.blog .entry-meta a.post-edit-link:hover,
.color-scheme.archive .entry-meta a.post-edit-link:focus,
.color-scheme.archive .entry-meta a.post-edit-link:hover,
.color-scheme.search .entry-meta a.post-edit-link:focus,
.color-scheme.search .entry-meta a.post-edit-link:hover,
.color-scheme .page-links a:focus .page-number,
.color-scheme .page-links a:hover .page-number,
.color-scheme .entry-footer .cat-links a:focus,
.color-scheme .entry-footer .cat-links a:hover,
.color-scheme .entry-footer .tags-links a:focus,
.color-scheme .entry-footer .tags-links a:hover,
.color-scheme .post-navigation a:focus,
.color-scheme .post-navigation a:hover,
.color-scheme .pagination a:not(.prev):not(.next):focus,
.color-scheme .pagination a:not(.prev):not(.next):hover,
.color-scheme .comments-pagination a:not(.prev):not(.next):focus,
.color-scheme .comments-pagination a:not(.prev):not(.next):hover,
.color-scheme .logged-in-as a:focus,
.color-scheme .logged-in-as a:hover,
.color-scheme a:focus .nav-title,
.color-scheme a:hover .nav-title,
.color-scheme .edit-link a:focus,
.color-scheme .edit-link a:hover,
.color-scheme .site-info a:focus,
.color-scheme .site-info a:hover,
.color-scheme .widget .widget-title a:focus,
.color-scheme .widget .widget-title a:hover,
.color-scheme .widget ul li a:focus,
.color-scheme .widget ul li a:hover {
    -webkit-box-shadow: inset 0 0 0 rgba(255, 255, 255, 0), 0 3px 0 rgba(255, 255, 255, 1); /* Equivalant to #fff */
    box-shadow: inset 0 0 0 rgba(255, 255, 255, 0), 0 3px 0 rgba(255, 255, 255, 1); /* Equivalant to #fff */
}

.color-scheme .entry-content a,
.color-scheme .entry-summary a,
.color-scheme .widget a,
.color-scheme .site-footer .widget-area a,
.color-scheme .posts-navigation a,
.color-scheme .widget_authors a strong {
    -webkit-box-shadow: inset 0 -1px 0 rgba(240, 240, 240, 1); /* Equivalant to #f0f0f0 */
    box-shadow: inset 0 -1px 0 rgba(240, 240, 240, 1); /* Equivalant to #f0f0f0 */
}

body.color-scheme,
.color-scheme button,
.color-scheme input,
.color-scheme select,
.color-scheme textarea,
.color-scheme h3,
.color-scheme h4,
.color-scheme h6,
.color-scheme label,
.color-scheme .entry-title a,
.color-scheme.twentyseventeen-front-page .panel-content .recent-posts article,
.color-scheme .entry-footer .cat-links a,
.color-scheme .entry-footer .tags-links a,
.color-scheme .format-quote blockquote,
.color-scheme .nav-title,
.color-scheme .comment-body {
    color: {$colors['secondary_text_color']};
}

/* Placeholder text color -- selectors need to be separate to work. */
.color-scheme ::-webkit-input-placeholder {
    color: {$colors['third_text_color']};
}

.color-scheme :-moz-placeholder {
    color: {$colors['third_text_color']};
}

.color-scheme ::-moz-placeholder {
    color: {$colors['third_text_color']};
}

.color-scheme :-ms-input-placeholder {
    color: {$colors['third_text_color']};
}

.color-scheme input[type="text"]:focus,
.color-scheme input[type="email"]:focus,
.color-scheme input[type="url"]:focus,
.color-scheme input[type="password"]:focus,
.color-scheme input[type="search"]:focus,
.color-scheme input[type="number"]:focus,
.color-scheme input[type="tel"]:focus,
.color-scheme input[type="range"]:focus,
.color-scheme input[type="date"]:focus,
.color-scheme input[type="month"]:focus,
.color-scheme input[type="week"]:focus,
.color-scheme input[type="time"]:focus,
.color-scheme input[type="datetime"]:focus,
.color-scheme input[type="datetime-local"]:focus,
.color-scheme input[type="color"]:focus,
.color-scheme textarea:focus,
.bypostauthor > .comment-body > .comment-meta > .comment-author .avatar {
    border-color: {$colors['secondary_text_color']};
}

.color-scheme input[type="text"]:focus,
.color-scheme input[type="email"]:focus,
.color-scheme input[type="url"]:focus,
.color-scheme input[type="password"]:focus,
.color-scheme input[type="search"]:focus,
.color-scheme input[type="number"]:focus,
.color-scheme input[type="tel"]:focus,
.color-scheme input[type="range"]:focus,
.color-scheme input[type="date"]:focus,
.color-scheme input[type="month"]:focus,
.color-scheme input[type="week"]:focus,
.color-scheme input[type="time"]:focus,
.color-scheme input[type="datetime"]:focus,
.color-scheme input[type="datetime-local"]:focus,
.color-scheme input[type="color"]:focus,
.color-scheme textarea:focus,
.color-scheme button.secondary,
.color-scheme input[type="reset"],
.color-scheme input[type="button"].secondary,
.color-scheme input[type="reset"].secondary,
.color-scheme input[type="submit"].secondary,
.color-scheme a,
.color-scheme .site-title,
.color-scheme .site-title a,
.color-scheme .navigation-top a,
.color-scheme .dropdown-toggle,
.color-scheme .menu-toggle,
.color-scheme .page .panel-content .entry-title,
.color-scheme .page-title,
.color-scheme.page:not(.twentyseventeen-front-page) .entry-title,
.color-scheme .page-links a .page-number,
.color-scheme .comment-metadata a.comment-edit-link,
.color-scheme .comment-reply-link .icon,
.color-scheme h2.widget-title,
.color-scheme mark,
.color-scheme .post-navigation a:focus .icon,
.color-scheme .post-navigation a:hover .icon,
.color-scheme.blog .entry-meta a.post-edit-link,
.color-scheme.archive .entry-meta a.post-edit-link,
.color-scheme.search .entry-meta a.post-edit-link,
.colors-custom .twentyseventeen-panel .recent-posts .entry-header .edit-link {
    color: {$colors['third_text_color']};
}

.color-scheme h2,
.color-scheme blockquote,
.color-scheme input[type="text"],
.color-scheme input[type="email"],
.color-scheme input[type="url"],
.color-scheme input[type="password"],
.color-scheme input[type="search"],
.color-scheme input[type="number"],
.color-scheme input[type="tel"],
.color-scheme input[type="range"],
.color-scheme input[type="date"],
.color-scheme input[type="month"],
.color-scheme input[type="week"],
.color-scheme input[type="time"],
.color-scheme input[type="datetime"],
.color-scheme input[type="datetime-local"],
.color-scheme input[type="color"],
.color-scheme textarea,
.color-scheme .navigation-top .current-menu-item > a,
.color-scheme .navigation-top .current_page_item > a,
.color-scheme .entry-content blockquote.alignleft,
.color-scheme .entry-content blockquote.alignright,
.color-scheme .taxonomy-description,
.color-scheme .site-info a,
.color-scheme .wp-caption {
    color: {$colors['secondary_text_color']};
}

.color-scheme abbr,
.color-scheme acronym {
    border-bottom-color: #ccc;
}

.color-scheme h5,
.main-navigation a:hover,
.color-scheme .entry-meta,
.color-scheme .entry-meta a,
.color-scheme .nav-subtitle,
.color-scheme .comment-metadata,
.color-scheme .comment-metadata a,
.color-scheme .no-comments,
.color-scheme .comment-awaiting-moderation,
.color-scheme .page-numbers.current,
.color-scheme .page-links .page-number,
.color-scheme .site-description {
    color: {$colors['third_text_color']};
}

.color-scheme button:hover,
.color-scheme button:focus,
.color-scheme input[type="button"]:hover,
.color-scheme input[type="button"]:focus,
.color-scheme input[type="submit"]:hover,
.color-scheme input[type="submit"]:focus,
.color-scheme .prev.page-numbers:focus,
.color-scheme .prev.page-numbers:hover,
.color-scheme .next.page-numbers:focus,
.color-scheme .next.page-numbers:hover,
.color-scheme .entry-footer .edit-link a.post-edit-link:focus,
.color-scheme .entry-footer .edit-link a.post-edit-link:hover {
    background: #bbb;
}

.color-scheme .social-navigation a:hover,
.color-scheme .social-navigation a:focus {
    background: #999;
    color: {$colors['background_color']};
}

.color-scheme .entry-footer .cat-links .icon,
.color-scheme .entry-footer .tags-links .icon {
    color: #666;
}

.color-scheme button.secondary:hover,
.color-scheme button.secondary:focus,
.color-scheme input[type="reset"]:hover,
.color-scheme input[type="reset"]:focus,
.color-scheme input[type="button"].secondary:hover,
.color-scheme input[type="button"].secondary:focus,
.color-scheme input[type="reset"].secondary:hover,
.color-scheme input[type="reset"].secondary:focus,
.color-scheme input[type="submit"].secondary:hover,
.color-scheme input[type="submit"].secondary:focus,
.color-scheme .social-navigation a,
.color-scheme hr {
    background: #555;
}

.color-scheme input[type="text"],
.color-scheme input[type="email"],
.color-scheme input[type="url"],
.color-scheme input[type="password"],
.color-scheme input[type="search"],
.color-scheme input[type="number"],
.color-scheme input[type="tel"],
.color-scheme input[type="range"],
.color-scheme input[type="date"],
.color-scheme input[type="month"],
.color-scheme input[type="week"],
.color-scheme input[type="time"],
.color-scheme input[type="datetime"],
.color-scheme input[type="datetime-local"],
.color-scheme input[type="color"],
.color-scheme textarea,
.color-scheme select,
.color-scheme fieldset,
.color-scheme .widget .tagcloud a:hover,
.color-scheme .widget .tagcloud a:focus,
.color-scheme .widget.widget_tag_cloud a:hover,
.color-scheme .widget.widget_tag_cloud a:focus,
.color-scheme .wp_widget_tag_cloud a:hover,
.color-scheme .wp_widget_tag_cloud a:focus {
    border-color: #555;
}

.color-scheme button.secondary,
.color-scheme input[type="reset"],
.color-scheme input[type="button"].secondary,
.color-scheme input[type="reset"].secondary,
.color-scheme input[type="submit"].secondary,
.color-scheme .prev.page-numbers,
.color-scheme .next.page-numbers {
    background-color: #444;
}

.color-scheme .widget .tagcloud a,
.color-scheme .widget.widget_tag_cloud a,
.color-scheme .wp_widget_tag_cloud a {
    border-color: #444;
}

.color-scheme.twentyseventeen-front-page article:not(.has-post-thumbnail):not(:first-child),
.color-scheme .widget ul li {
    border-top-color: #444;
}

.color-scheme .widget ul li {
    border-bottom-color: #444;
}

.color-scheme pre,
.color-scheme mark,
.color-scheme ins,
.color-scheme input[type="text"],
.color-scheme input[type="email"],
.color-scheme input[type="url"],
.color-scheme input[type="password"],
.color-scheme input[type="search"],
.color-scheme input[type="number"],
.color-scheme input[type="tel"],
.color-scheme input[type="range"],
.color-scheme input[type="date"],
.color-scheme input[type="month"],
.color-scheme input[type="week"],
.color-scheme input[type="time"],
.color-scheme input[type="datetime"],
.color-scheme input[type="datetime-local"],
.color-scheme input[type="color"],
.color-scheme textarea,
.color-scheme select,
.color-scheme fieldset {
    background: #333;
}

.color-scheme tr,
.color-scheme thead th {
    border-color: #333;
}

.color-scheme .navigation-top,
.color-scheme .main-navigation > div > ul,
.color-scheme .pagination,
.color-scheme .comment-navigation,
.color-scheme .entry-footer,
.color-scheme .site-footer {
    border-top-color: #333;
}

.color-scheme .single-featured-image-header,
.color-scheme .navigation-top,
.color-scheme .main-navigation li,
.color-scheme .entry-footer,
.color-scheme #comments {
    border-bottom-color: #333;
}

.color-scheme .site-header,
.color-scheme .single-featured-image-header {
    background-color: #262626;
}

.color-scheme button,
.color-scheme input[type="button"],
.color-scheme input[type="submit"],
.color-scheme .prev.page-numbers:focus,
.color-scheme .prev.page-numbers:hover,
.color-scheme .next.page-numbers:focus,
.color-scheme .next.page-numbers:hover {
    color: {$colors['background_color']};
}

body.color-scheme,
.color-scheme .site-content-contain,
.color-scheme .navigation-top,
.color-scheme .main-navigation ul {
    background: {$colors['background_color']};
}

.color-scheme .entry-title a,
.color-scheme .entry-meta a,
.color-scheme.blog .entry-meta a.post-edit-link,
.color-scheme.archive .entry-meta a.post-edit-link,
.color-scheme.search .entry-meta a.post-edit-link,
.color-scheme .page-links a,
.color-scheme .page-links a .page-number,
.color-scheme .entry-footer a,
.color-scheme .entry-footer .cat-links a,
.color-scheme .entry-footer .tags-links a,
.color-scheme .edit-link a,
.color-scheme .post-navigation a,
.color-scheme .logged-in-as a,
.color-scheme .comment-navigation a,
.color-scheme .comment-metadata a,
.color-scheme .comment-metadata a.comment-edit-link,
.color-scheme .comment-reply-link,
.color-scheme a .nav-title,
.color-scheme .pagination a,
.color-scheme .comments-pagination a,
.color-scheme .widget .widget-title a,
.color-scheme .widget ul li a,
.color-scheme .site-footer .widget-area ul li a,
.color-scheme .site-info a {
    -webkit-box-shadow: inset 0 -1px 0 rgba(34, 34, 34, 1); /* Equivalant to #222 */
    box-shadow: inset 0 -1px 0 rgba(34, 34, 34, 1); /* Equivalant to #222 */
}

/* Fixes linked images */
.color-scheme .entry-content a img,
.color-scheme .widget a img {
    -webkit-box-shadow: 0 0 0 8px {$colors['background_color']};
    box-shadow: 0 0 0 8px {$colors['background_color']};
}

.color-scheme .entry-footer .edit-link a.post-edit-link {
    color: #000;
}

.color-scheme .menu-toggle,
.color-scheme .menu-toggle:hover,
.color-scheme .menu-toggle:focus,
.color-scheme .dropdown-toggle,
.color-scheme .dropdown-toggle:hover,
.color-scheme .dropdown-toggle:focus,
.color-scheme .menu-scroll-down,
.color-scheme .menu-scroll-down:hover,
.color-scheme .menu-scroll-down:focus {
    background-color: transparent;
}

.color-scheme .gallery-item a,
.color-scheme .gallery-item a:hover,
.color-scheme .gallery-item a:focus,
.color-scheme .widget .tagcloud a,
.color-scheme .widget .tagcloud a:focus,
.color-scheme .widget .tagcloud a:hover,
.color-scheme .widget.widget_tag_cloud a,
.color-scheme .widget.widget_tag_cloud a:focus,
.color-scheme .widget.widget_tag_cloud a:hover,
.color-scheme .wp_widget_tag_cloud a,
.color-scheme .wp_widget_tag_cloud a:focus,
.color-scheme .wp_widget_tag_cloud a:hover,
.color-scheme .entry-footer .edit-link a.post-edit-link:focus,
.color-scheme .entry-footer .edit-link a.post-edit-link:hover {
    -webkit-box-shadow: none;
    box-shadow: none;
}

@media screen and (min-width: 48em) {

    .color-scheme .nav-links .nav-previous .nav-title .icon,
    .color-scheme .nav-links .nav-next .nav-title .icon {
        color: {$colors['secondary_text_color']};
    }

    .color-scheme .main-navigation li li:hover,
    .color-scheme .main-navigation li li.focus {
        background: #999;
    }

    .color-scheme .menu-scroll-down {
        color: #999;
    }

    .color-scheme .main-navigation ul ul {
        border-color: #333;
        background: {$colors['background_color']};
    }

    .color-scheme .main-navigation ul li.menu-item-has-children:before,
    .color-scheme .main-navigation ul li.page_item_has_children:before {
        border-bottom-color: #333;
    }

    .main-navigation ul li.menu-item-has-children:after,
    .main-navigation ul li.page_item_has_children:after {
        border-bottom-color: {$colors['background_color']};
    }

    .color-scheme .main-navigation li li.focus > a,
    .color-scheme .main-navigation li li:focus > a,
    .color-scheme .main-navigation li li:hover > a,
    .color-scheme .main-navigation li li a:hover,
    .color-scheme .main-navigation li li a:focus,
    .color-scheme .main-navigation li li.current_page_item a:hover,
    .color-scheme .main-navigation li li.current-menu-item a:hover,
    .color-scheme .main-navigation li li.current_page_item a:focus,
    .color-scheme .main-navigation li li.current-menu-item a:focus {
        color: {$colors['background_color']};
    }

}






















































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

        return $css;

    }

}



