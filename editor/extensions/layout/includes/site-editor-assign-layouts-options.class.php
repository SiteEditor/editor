<?php

/**
 * Site Settings Class
 *
 * Implements Site Settings management in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class SiteEditorSiteOptions
 * @description : Site settings like general settings in wp admin
 */
class SiteEditorAssignLayoutsOptions extends SiteEditorOptionsCategory{

    /**
     * Capability required to edit this field.
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * this field group use :
     *  "general" || "style-editor" || "module" || "post"
     *
     * @access private
     * @var array
     */
    protected $option_group = 'sed_pages_layouts';

    /**
     * default option type
     *
     * @access public
     * @var array
     */
    public $option_type  = "option";

    /**
     * default option type
     *
     * @access protected
     * @var array
     */
    protected $category  = "app-settings";

    /**
     * prefix for controls ids for prevent conflict
     *
     * @var string
     * @access public
     */
    public $control_prefix = 'sed_pages_layouts';

    /**
     * SiteEditorSiteOptions constructor.
     */
    public function __construct( $layout ){

        $this->layout = $layout;

        $this->title = __("Pages Layouts Settings" , "site-editor");

        $this->description = __("Assign layouts to groups of pages" , "site-editor");

        add_filter( "{$this->option_group}_panels_filter" , array( $this , 'register_default_panels' ) );

        add_filter( "{$this->option_group}_fields_filter" , array( $this , 'register_default_fields' ) );

        add_action( "sed_editor_init"                     , array( $this , 'add_toolbar_elements' ) , 80 );

        parent::__construct();

    }

    /**
     * add element to SiteEditor toolbar
     */
    public function add_toolbar_elements(){
        global $site_editor_app;

        $site_editor_app->toolbar->add_element(
            "layout" ,
            "general" ,
            "pages-layouts" ,
            __("Layout settings","site-editor") ,
            "sub_theme_element" ,     //$func_action
            "" ,                //icon
            "" ,  //$capability=
            array( ),// "class"  => "btn_default3"
            array( "row" => 1 ,"rowspan" => 2 ),
            array('module' => 'layout' , 'file' => 'pages_layouts.php'),
            //array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
            'all' ,
            array(),
            array()
        );

    }

    /**
     * Register Site Default Panels
     */
    public function register_default_panels()
    {

        $panels = array(

            'default_pages_layouts' => array(
                'id'            => 'default_pages_layouts' ,
                'title'         =>  __('Default Pages Layouts',"site-editor")  ,
                'capability'    => 'edit_theme_options' ,
                'type'          => 'default' ,
                'description'   => '' ,
                'priority'      => 10 ,
                'option_group'  => 'sed_pages_layouts'
            ),

        );

        $post_types = get_post_types( array( 'show_in_nav_menus' => true , 'public' => true ), 'object' );

        if ( !empty( $post_types ) ) {

            $custom_post_types_num = 0;

            foreach ($post_types AS $post_type_name => $post_type) {

                if( in_array( $post_type_name , array( "post" , "page" ) ) )
                    continue;

                $custom_post_types_num++;

            }

            if( $custom_post_types_num > 0 ){

                $panels['custom_post_types_layouts'] = array(
                    'id'            => 'custom_post_types_layouts' ,
                    'title'         =>  __('Custom Post Types Layouts',"site-editor")  ,
                    'capability'    => 'edit_theme_options' ,
                    'type'          => 'default' ,
                    'description'   => '' ,
                    'priority'      => 10 ,
                    'option_group'  => 'sed_pages_layouts'
                );

            }

        }

        $args = array(
            'public'   => true,
            '_builtin' => false

        );

        $output = 'objects';
        $taxonomies = get_taxonomies( $args, $output );
        if ( $taxonomies ) {

            $panels['custom_taxonomies_layouts'] = array(
                'id' => 'custom_taxonomies_layouts',
                'title' => __('Custom Taxonomies Layouts', "site-editor"),
                'capability' => 'edit_theme_options',
                'type' => 'fieldset',
                'description' => '',
                'priority' => 10,
                'option_group' => 'sed_pages_layouts'
            );

        }

        return $panels;
    }

    /**
     * Register Site Default Fields
     */
    public function register_default_fields( $fields ){

        $default_pages_layouts = $this->layout->default_pages_layouts_list();

        $default_pages_layouts_labels = array(
            "posts_archive"     =>  __('Post Archive Pages Layout',"site-editor"),
            "index_blog"        =>  __('Blog Page Layout',"site-editor") ,
            "front_page"        =>  __('Front Page Layout',"site-editor") ,
            "search_results"    =>  __('Search Results Page Layout',"site-editor") ,
            "404_page"          =>  __('404 Page Layout',"site-editor") ,
            "single_post"       =>  __('Single Posts Layout',"site-editor") ,
            "single_page"       =>  __('Single Pages Layouts',"site-editor") ,
            "author_page"       =>  __('Author Pages Layout',"site-editor") ,
            "date_archive"      =>  __('Date Archive Pages Layout',"site-editor") ,
        );

        $current_pages_layouts = get_option('sed_pages_layouts');

        foreach( $default_pages_layouts AS $page_group => $layout ){

            $id = 'sed_pages_layouts[' . $page_group . ']';
            $layout = $current_pages_layouts[$page_group];

            $fields["group_".$page_group] = array(
                'type'              => 'select',
                'default'           => $layout ,
                'label'             => $default_pages_layouts_labels[$page_group],
                'description'       => '',
                'choices'           => array(),
                'atts'              => array(
                    "class"             =>  "sed_all_layouts_options_select"
                ),
                'priority'          => 15 ,
                'category'          => 'app-settings' ,
                'setting_id'        => $id ,
                "panel"             => "default_pages_layouts" , //( $_POST['current_layout_group'] == $page_group ) ?  "current_page_layout_panel" :
                'option_group'      => 'sed_pages_layouts',
                'option_type'       => 'option'
            );

        }

        $post_types = get_post_types( array( 'show_in_nav_menus' => true , 'public' => true ), 'object' );

        if ( !empty( $post_types ) ) {

            $custom_post_types_num = 0;

            foreach ($post_types AS $post_type_name => $post_type) {

                if( in_array( $post_type_name , array( "post" , "page" ) ) )
                    continue;

                $page_group = 'post_type_archive_' . $post_type_name ;
                $id = 'sed_pages_layouts[' . $page_group . ']';
                $layout = $current_pages_layouts[$page_group];

                $fields["group_".$page_group] = array(
                    'type'              => 'select',
                    'default'           => $layout ,
                    'label'             => sprintf( __("%s post type archive layout" , "site-editor") , $post_type->labels->name ),
                    'description'       => '',
                    'choices'           => array(),
                    'atts'              => array(
                        "class"             =>  "sed_all_layouts_options_select"
                    ),
                    'priority'          => 15 ,
                    'category'          => 'app-settings' ,
                    'setting_id'        => $id ,
                    "panel"             => "custom_post_types_layouts" ,
                    'option_group'      => 'sed_pages_layouts'
                );


                $page_group = 'single_' . $post_type_name ;
                $id = 'sed_pages_layouts[' . $page_group . ']';
                $layout = $current_pages_layouts[$page_group];

                $fields["group_".$page_group] = array(
                    'type'              => 'select',
                    'default'           => $layout ,
                    'label'             => sprintf( __("%s single pages layout" , "site-editor") , $post_type->labels->name ),
                    'description'       => '',
                    'choices'           => array(),
                    'atts'              => array(
                        "class"         =>  "sed_all_layouts_options_select"
                    ),
                    'priority'          => 15 ,
                    'category'          => 'app-settings' ,
                    'setting_id'        => $id ,
                    "panel"             => "custom_post_types_layouts" ,
                    'option_group'      => 'sed_pages_layouts'
                );

                $custom_post_types_num++;

            }

        }

        $args = array(
            'public'   => true,
            '_builtin' => false

        );

        $output = 'objects';
        $taxonomies = get_taxonomies( $args, $output );
        if ( $taxonomies ) {

            foreach ( $taxonomies  as $taxonomy ) {

                $page_group = 'taxonomy_' . $taxonomy->name ;
                $id = 'sed_pages_layouts[' . $page_group . ']';
                $layout = $current_pages_layouts[$page_group];

                $fields["group_".$page_group] = array(
                    'type'              => 'select',
                    'default'           => $layout ,
                    'label'             => sprintf( __("%s term pages layout" , "site-editor") , $taxonomy->label ),
                    'description'       => '',
                    'choices'           => array(),
                    'atts'              => array(
                        "class"         =>  "sed_all_layouts_options_select"
                    ),
                    'priority'          => 15 ,
                    'category'          => 'app-settings' ,
                    'setting_id'        => $id ,
                    "panel"             => "custom_taxonomies_layouts" ,
                    'option_group'      => 'sed_pages_layouts'
                );

            }
        }

        return $fields;

    }

}

