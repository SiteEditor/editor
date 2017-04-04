<?php
/*
* Module Name: Page Title
* Module URI: http://www.siteeditor.org/modules/page-title
* Description: Page Title Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "breadcrumbs" ) || !is_pb_module_active( "title" )){
    sed_admin_notice( __("<b>Page Title Module</b> needed to <b>Title Module</b> and <b>Breadcrumbs module</b><br /> please first install and activate its ") );
    return ;
}

class PBPageTitleShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_page_title",                               //*require
                "title"       => __("Page Title","site-editor"),                 //*require for toolbar
                "description" => __("Edit Page Title in Front End","site-editor"),
                "icon"        => "sedico-page-title",                               //*require for icon toolbar
                "module"      =>  "page-title"         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
		);

        if( site_editor_app_on() )
            add_action( "wp_footer" , array( $this , "print_get_title" ) );
	}

    function print_get_title(){
        ?>
        <script>
            var _sedAppPageTitle = "<?php echo PBPageTitleShortcode::get_title();?>";
            var _sedAppSiteTagline = "<?php echo get_bloginfo('description');?>";
        </script>
        <?php
    }

    function get_atts(){

        $atts = array(
            'length'            => 'boxed' ,
            'show_sub_title'    => true ,
            'sub_title'         => __("Site Editor is the most powerful editor for WordPress","site-editor")
        );

        return $atts;

    }

    function add_shortcode( $atts , $content = null ){

    }            

    function styles(){
        return array(
            array('page-title-style', SED_PB_MODULES_URL.'page-title/css/style.css' ,'1.0.0' ) ,
        );
    } 


    static function get_title(){

        /* === OPTIONS === */
        $text['home']     = __('Home','site-editor'); // text for the 'Home' link
        $text['category'] = "%s"; // text for a category page
        $text['search']   = __('Search Results for : %s','site-editor'); // text for a search results page
        $text['tag']      = __('Tag : %s','site-editor'); // text for a tag page
        $text['author']   = __('Author : %s','site-editor'); // text for an author page
        $text['404']      = __('Error 404 Page','site-editor'); // text for the 404 page
        $title            = get_the_title();

        global $post;

        $parent_id = ($post) ? $post->post_parent : 0;

        if ( ( is_front_page() && is_home() ) || is_front_page() ) {
                $title = $text['home'];

        }else if( !is_front_page() && is_home() ){
                $title = __('Blog','site-editor');
        }else{

            if ( is_category() ) {
                $this_cat = get_category( get_query_var('cat') , false );
                $title = sprintf( $text['category'] , $this_cat->name );

            } elseif ( is_search() ) {
                $title = sprintf( $text['search'] , get_search_query() );

            } elseif ( is_day() ) {
                $title = sprintf( __("Daily Archives: %s %s %s" , "site-editor") , get_the_time('Y') , get_the_time('F') , get_the_time('d') );

            } elseif ( is_month() ) {
                $title = sprintf( __("Monthly Archives %s %s" , "site-editor") , get_the_time('Y') , get_the_time('F') );

            } elseif ( is_year() ) {
                $title = sprintf( __("Yearly Archives %s" , "site-editor") , get_the_time('Y'));
               
            } elseif ( is_single() && !is_attachment() ) {
                /*if ( get_post_type() != 'post' ) {
                    $post_type = get_post_type_object( get_post_type() );
                    $title = $post_type->labels->singular_name . ' : ' . get_the_title();
                } else {

                    /*$post_categories = wp_get_post_categories( $post->ID );
                    $cats = array();

                    foreach($post_categories as $c){
                    	$cat = get_category( $c );
                    	$cats[] = $cat->name;
                    }

                    $this_cats = !empty( $cats ) ? implode("," , $cats) : ""; */

               $title = get_the_title();

            } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
                $post_type  = get_post_type_object(get_post_type());

                if( $post_type ){
                    $page_title = $post_type->labels->singular_name;
                    if ( is_tax() ) {
                        $page_title = single_term_title( "", false );//sprintf( __("%s Category %s" , "site-editor") , $page_title , single_term_title( "", false ) );
                    }
                    $title = $page_title;
                }

            } elseif ( is_attachment() ) {
                //$parent = get_post( $parent_id );
                $title = get_the_title();//$parent->post_title . ' : ' . get_the_title();
                
            } elseif ( is_page()  ) {
                $title = get_the_title();

            } elseif ( is_tag() ) {
                $title = sprintf($text['tag'], single_tag_title('', false));

            } elseif ( is_author() ) {
                global $author;
                $userdata = get_userdata($author);
                $title = sprintf($text['author'], $userdata->display_name );

            } elseif ( is_404() ) {
                $title = $text['404'];
            }
            /*if ( get_query_var('paged') ) {
                if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_home() ) echo ' (';
                    $title = __('Page','site-editor') . ' : ' . get_query_var('paged');
            }*/
        }
        if( empty( $title ) )
            $title = get_the_title( $post->ID );

        return apply_filters( "sed_page_title" , $title );
    }

    function shortcode_settings(){

        $this->add_panel( 'page_title_settings_panel' , array(
            'title'                   =>  __('Page Title Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'inner_box' ,
            'priority'                => 9 ,  
            'btn_style'               => 'menu' ,
            'has_border_box'          => false ,
            'icon'                    => 'sedico-page-title' ,
            'field_spacing'           => 'sm'
        ) );

        $params = array(

            'show_sub_title' => array(
                'label'             => __('Show Sub Title', 'site-editor'),
                'type'              => 'switch',
                'choices'           => array(
                    "on"                =>    __("Yes" , "site-editor" ) ,
                    "off"               =>    __("No" , "site-editor" ) ,
                ),
                "panel"             => "page_title_settings_panel" ,
            ),

            'sub_title' => array(
                "type"              => "textarea" ,
                "label"             => __("Sub Title", "site-editor"),
                "description"       => __("This option allows you to set a sub title for yor page title", "site-editor"),
                "placeholder"       => __("Enter Your Sub Title", "site-editor"),
                "panel"             => "page_title_settings_panel",
                'dependency' => array(
                    'queries'  =>  array(
                        array(
                            "key"       => "show_sub_title" ,
                            "value"     => true ,
                            "compare"   => "==="
                        )
                    )
                )
            ),

            'length' => array(
                "type"          => "length" ,
                "label"         => __("Length", "site-editor"),
                'panel'         => 'page_title_settings_panel',
            ),

            'row_container' => array(
                'type'          => 'row_container',
                'label'         => __('Module Wrapper Settings', 'site-editor')
            ),

            "animation"  =>  array(
                "type"                => "animation" ,
                "label"               => __("Animation Settings", "site-editor"),
                'button_style'        => 'menu' ,
                'has_border_box'      => false ,
                'icon'                => 'sedico-animation' ,
                'field_spacing'       => 'sm' ,
                'priority'            => 530 ,
            )

        );

        return $params;
    }

    function custom_style_settings(){
        return array(

            array(
                'page-title-container' , 'sed_current' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Page Title Container" , "site-editor") 
            ) ,

            array(
                'text' , '.module-title > *' ,
                array('text_shadow' , 'font' ,'line_height','text_align' ) , __("Text" , "site-editor") 
            ) ,

        );
    }

    function contextmenu( $context_menu ){
        $page_title_menu = $context_menu->create_menu( "page-title" , __("Page Title","site-editor") , 'page-title' , 'class' , 'element' , '' , "sed_page_title" , array(
                "seperator"        => array(45 , 75) ,
                "duplicate"        => false
            )
        );
      //$context_menu->add_change_column_item( $page-title_menu );
    }

}

new PBPageTitleShortcode();

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "theme" ,
    "name"        => "page-title",
    "title"       => __("Page Title","site-editor"),
    "description" => __("Edit Page Title in Front End","site-editor"),
    "icon"        => "sedico-page-title",
    "type_icon"   => "font",
    "shortcode"         => "sed_page_title",
    "priority"          => 10 ,
    "tpl_type"    => "underscore" ,
    "sub_modules"   => array('title'),
));






