<?php
/*
Module Name:Menu
Module URI: http://www.siteeditor.org/modules/menu
Description: Module Box For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/
/*
if( !is_pb_module_active( "image" )){
    sed_admin_notice( __("<b>Diamond Gallery Module</b> needed to <b>Image module</b><br /> please first install and activate it ") );
    return ;
}
 */

class PBMenuShortcode extends PBShortcodeClass{
    public $menus = array();

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_menu",                          //*require
                "title"       => __("Menu","site-editor"),            //*require for toolbar
                "description" => __("","site-editor"),
                "icon"        => "icon-promenu",                         //*require for icon toolbar
                "module"      =>  "menu" ,                             //*require
                "scripts"           => array(
                    array("bs-collapse" , SED_PB_MODULES_URL.'menu/js/collapse.js',array(),"1.0.0" , 1) ,
                    array("bs-dropdown" , SED_PB_MODULES_URL.'menu/js/dropdown.js',array(),"1.0.0" , 1) ,
                    array("bootstrap-js-menu" , SED_PB_MODULES_URL.'menu/js/jquery.sedMegaMenu.js',array("bs-dropdown"),"1.0.0" , 1) ,
                ),      //array($handle, $src, $deps, $ver, $in_footer) ,array($handle, $src, $deps, $ver, $in_footer)
                "styles"            => array(
                    array('menu-main-less' , SED_PB_MODULES_URL.'menu/less/main.less' , array() , "1.0.0" , "all")
                ),       //$handle, $src, $deps, $ver, $media
            ) // Args
        );

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'custom_fields.php';

        $this->menus = wp_get_nav_menus();

        //add css to admin nav menu
        add_action('admin_enqueue_scripts', array(&$this, 'sed_admin_styles'));

        add_action( "init" , array( $this , "image_init") );
    }

    function image_init(){
        add_image_size( 'sedMenuThumbnail', 64, 64, true );
        //if ( has_post_thumbnail() ) { the_post_thumbnail( 'sed-x-large' ); }
    }

    function sed_admin_styles($hook) {

        if ($hook != 'nav-menus.php')
            return;

        wp_register_style('sed_nav_menus_admin', SED_PB_MODULES_URL.'menu/css/admin_nav_menu.css' , array());
        wp_enqueue_style('sed_nav_menus_admin');
    }

    function get_atts(){
        $atts = array(
            'class'                     => 'sed-menu-module' ,
            'menu'                      => (!empty($this->menus)) ? $this->menus[0]->name: "",
            'orientation'               => 'horizontal',
            'image_width'               => 32,
            'image_height'              => 32,
            'display_description'       => true,
            'trigger'                   => 'hover',
            'vertical_menu_width'       => 250,
            'sticky'                    => false,
            'sticky_styles'             => 'sticky-menu-default',
            'img_align'                 => 'left',
            'icon_align'                => 'left',
            'navbar_align'              => 'center',
            'delay_hover'               => 500,
            'icon_font_size'            => 15,
            'show_search'               => false,
            'image_icon_preference'     => 'icon' ,
            'scroll_animate_anchor'     => 'easeInOutQuint' ,
            'scroll_animate_duration'   =>  2000 ,
            'show_cart'                 => false ,
            'is_vertical_fixed'         =>  false ,
            'length'                    => "boxed" ,
            'enable_draggable_area'     => false ,
            'draggable_area_direction'  => "left" ,
            'draggable_area_width'      => 100 ,
        );

        return $atts;
    }

    function scripts(){
        return array(
            array("bs-collapse" , SED_PB_MODULES_URL.'menu/js/collapse.js',array(),"1.0.0" , 1) ,
            array("bs-dropdown" , SED_PB_MODULES_URL.'menu/js/dropdown.js',array(),"1.0.0" , 1) ,
            array("sed-megamenu" , SED_PB_MODULES_URL.'menu/js/jquery.sedMegaMenu.js',array("bs-dropdown" , "jquery" , "underscore"),"1.0.0" , 1)
        );
    }

    function less(){
        return array(
            array('menu-main-less')
        );
    }

    function add_shortcode( $atts , $content = null ){
        global $current_module;

        extract( $atts );

        //$this->add_script("bs-collapse" , SED_PB_MODULES_URL.'menu/js/collapse.js',array(),"1.0.0" , 1);
        //$this->add_script("bs-dropdown" , SED_PB_MODULES_URL.'menu/js/dropdown.js',array(),"1.0.0" , 1);
        //$this->add_script("sed-megamenu" , SED_PB_MODULES_URL.'menu/js/jquery.sedMegaMenu.js',array("bs-dropdown" , "jquery" , "underscore"),"1.0.0" , 1);

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'walker-my-menu.php';

        $current_module['skin-path'] = dirname( __FILE__ ) . DS . 'skins' . DS . $atts['skin'];
        //$this->add_less('menu-main-less' , SED_PB_MODULES_URL.'menu/less/main.less' , array() , "1.0.0" , "all");


        $this->set_vars( array(
            "PBMenuShortcode"  => $this
        ));

    }

    public function get_menu_args( $content ){
        global $current_module;

        $before_items_file = $current_module['skin-path'] . '/walker-tpl/before_items.php';
        $after_items_file  = $current_module['skin-path'] . '/walker-tpl/after_items.php';

        $before_items_content = '';
        $after_items_content  = '';

        extract( $this->atts );

        if( is_file( $before_items_file ) ){
            ob_start();
                include $before_items_file;
            $before_items_content = ob_get_contents();
            ob_end_clean();
        }
        if( is_file( $after_items_file ) ){
            ob_start();
                include $after_items_file;
            $after_items_content = ob_get_contents();
            ob_end_clean();
        }

        $args = array(
            'menu'              =>  $this->atts['menu'] ,
            'container_class'   => 'sed-menu-container',
            'menu_class'        => 'nav navbar-nav navbar-right',
            'items_wrap'        => '<ul id="%1$s" class="%2$s">'. $before_items_content . '%3$s'. $after_items_content . '</ul>',
            'fallback_cb'       => 'wp_page_menu',
            'depth'             => 0,
            'walker'            => new SED_Walker_Nav_Menus( $this->atts , $content )
        );

        return $args;
    }

    public static function get_menu_content( $content , $tagname , $id = '' ){

        if( empty($id) ){
            $tagname = preg_quote($tagname);
            $attr = preg_quote("id");

            $tag_regex = "/<(".$tagname.")[^>]*$attr\s*=\s*".
                          "(['\"])([^>]+)\\2[^>]*>(.*?)<\/(".$tagname.")>/s";

            preg_match_all($tag_regex,
                           $content,
                           $matches,
                           PREG_PATTERN_ORDER);

            return $matches;
        }else{
            return PBMenuShortcode::get_tag_with_attr( "id" , $id , $content , $tagname );
        }

    }

    public static function get_tag_with_attr( $attr, $value, $content, $tag ) {

        $tag = preg_quote($tag);
        $attr = preg_quote($attr);
        $value = preg_quote($value);

        $tag_regex = "/<(".$tag.")[^>]*$attr\s*=\s*".
                      "(['\"])$value\\2[^>]*>(.*?)<\/(".$tag.")>/s";

        preg_match_all($tag_regex,
                       $content,
                       $matches,
                       PREG_PATTERN_ORDER);

        if( !empty( $matches[3] ) )
            return $matches[3][0];
        else
            return '';
    }

    function shortcode_settings(){
        $menus = $this->menus;
        $menu_options = array(
            "" => __('Select Menu' , 'site-editor')
        );

        if( !empty($menus) ){
            foreach ( $menus as $menu ) {
                $menu_options[$menu->name] = esc_html( $menu->name );
            }
        }

        $this->add_panel( 'general_settings_panel' , array(
            'title'         =>  __('General Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'default' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $this->add_panel( 'advance_settings_panel' , array(
            'title'         =>  __('Advance Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'default' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $this->add_panel( 'image_icon_settings_panel' , array(
            'title'         =>  __('Image & Icon Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'default' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $params = array(

            'menu' => array(
                'type'                  => 'select',
                'label'                 => __(' Select Menu ', 'site-editor'),
                'description'           => __('This feature allows you to select your desired pre-made menu – in WordPress admin, Appearance> Menus section – to be loaded in the current place of the module.', 'site-editor'),
                'choices'               => $menu_options ,
                'panel'    => 'general_settings_panel',
                /*'js_params'         =>  array(
                    "force_refresh"         =>   true
                ),*/
            ),
            'orientation' => array(
                'type' => 'radio',
                'label' => __('Orientation ', 'site-editor'),
                'description'  => __('This feature allows you to specify the type and direction of menu; the options are vertical and horizontal.', 'site-editor'),
                'choices'   =>array(
                    'horizontal' => __('Horizontal', 'site-editor'),
                    'vertical'   => __(' Vertical ', 'site-editor'),
                ),
                'panel'    => 'general_settings_panel',
                'has_border_box'   => true,

            ),

            'vertical_menu_width' => array(
                'type' => 'number',
                'label' => __('Vertical Menu width', 'site-editor'),
                'description'  => __('', 'site-editor'),
                'panel'    => 'general_settings_panel',
                "dependency" => array(
                    'controls'  =>  array(
                        "control"  =>  "orientation" ,
                        "value"    =>  "vertical"
                    )
                ), 
            ),

            'sticky' => array(
                'type' => 'checkbox',
                'label' => __('Is Sticky menu ?', 'site-editor'),
                'description'  => __('This feature allows you to activate Sticky feature for your menu.', 'site-editor'),
                'panel'    => 'general_settings_panel',
                "dependency" => array(
                    'controls'  =>  array(
                        "control"  =>  "orientation" ,
                        "value"    =>  "vertical",
                        "type"     =>  "exclude"
                    )
                ), 
            ),
            'sticky_styles' => array(
                'type' => 'select',
                'label' => __('Sticky Styles', 'site-editor'),
                'description'  => __('Style For Sticky', 'site-editor'),
                'choices'   =>array(
                    'sticky-menu-default'   => __('Default', 'site-editor'),
                    'sticky-menu-style1'    => __('Style1 ', 'site-editor'),
                    'sticky-menu-style2'    => __('Style2 ', 'site-editor'),
                ),
                'panel'    => 'general_settings_panel',
                "dependency" => array(
                    'controls'  =>  array(
                        "control"  =>  "sticky" ,
                        "value"    =>  false,
                        "type"     =>  "exclude"
                    )
                ), 

            ),
            'navbar_align' => array(
                'type' => 'select',
                'label' => __('Navbar Align ', 'site-editor'),
                'description'  => __('This feature allows you to specify the location of menu items with respect to the main menu container - on the left, right or the center of that.', 'site-editor'),
                'choices'   =>array(
                    'center'     => __('Center', 'site-editor'),
                    'right'     => __('Right ', 'site-editor'),
                    'left'     => __('Left ', 'site-editor'),
                ),

            ),
            'show_cart' => array(
                'type' => 'checkbox',
                'label' => __('Show Cart', 'site-editor'),
                'description'  => __('This feature allows you to add your shopping card (woocommerce) to your menu. (The shopping cart icon will be added to the menu.)', 'site-editor'),
                'panel'    => 'advance_settings_panel',
            ),
            'show_search' => array(
                'type' => 'checkbox',
                'label' => __('Show Search', 'site-editor'),
                'description'  => __('This feature allows you to add icon search to your menu.', 'site-editor'),
                'panel'    => 'advance_settings_panel',
            ),

            'display_description' => array(
                'type' => 'checkbox',
                'label' => __('Display Description', 'site-editor'),
                'description'  => __('This feature allows you to display the item menu descriptions in mega menus; you have inserted the descriptions in WordPress Admin.', 'site-editor'),
                'panel'    => 'advance_settings_panel',
            ),

            'trigger' => array(
                'type' => 'select',
                'label' => __('Trigger ', 'site-editor'),
                'description'  => __('This feature allows you to specify the event of sub-menus and megamenus; the options are: Click and Hover. Clicking the items, sub-menus and megamenus will be opened for the former; and for the latter, this will happen when the mouse cursor be on the items. ', 'site-editor'),
                'choices'   =>array(
                    'hover'     => __('Hover', 'site-editor'),
                    'click'     => __('Click ', 'site-editor'),
                ),
                'panel'    => 'advance_settings_panel',

            ),

            'delay_hover' => array(
                'type' => 'number',
                'label' => __('Delay Hover', 'site-editor'),
                'description'  => __('This feature allows you to specify the time delay to sub-menus and megamenus be hidden.', 'site-editor'),
                'panel'    => 'advance_settings_panel',
                "dependency" => array(
                    'controls'  =>  array(
                        "control"  =>  "trigger" ,
                        "value"    =>  'hover'
                    )
                ),                
            ),


            'scroll_animate_anchor' => array(
                'type' => 'select',
                'label' => __('Scroll Animate For Anchor : ', 'site-editor'),
                'description'  => __('This feature allows you to specify the type of animation for scrolls. (The type of scroll animation when Anchor is clicked.) ', 'site-editor'),
                'choices'   =>array(
                    ''                      => __('without using animate', 'site-editor'),
                    'easeInOutQuint'        => __('easeInOutQuint ', 'site-editor'),
                    'easeOutQuad'           => __('easeOutQuad', 'site-editor'),
                    'swing'                 => __('swing ', 'site-editor'),
                    'easeInQuad'            => __('easeInQuad', 'site-editor'),
                    'easeOutQuad'           => __('easeOutQuad', 'site-editor'),
                    'easeInOutQuad'         => __('easeInOutQuad', 'site-editor'),
                    'easeInCubic'           => __('easeInCubic', 'site-editor'),
                    'easeOutCubic'          => __('easeOutCubic', 'site-editor'),
                    'easeInOutCubic'        => __('easeInOutCubic', 'site-editor'),
                    'easeInQuart'           => __('easeInQuart', 'site-editor'),
                    'easeOutQuart'          => __('easeOutQuart ', 'site-editor'),
                    'easeInOutQuart'        => __('easeInOutQuart', 'site-editor'),
                    'easeInQuint'           => __('easeInQuint ', 'site-editor'),
                    'easeOutQuint'          => __('easeOutQuint', 'site-editor'),
                    'easeInSine'            => __('easeInSine', 'site-editor'),
                    'easeOutSine'           => __('easeOutSine', 'site-editor'),
                    'easeInOutSine'         => __('easeInOutSine', 'site-editor'),
                    'easeInExpo'            => __('easeInExpo', 'site-editor'),
                    'easeOutExpo'           => __('easeOutExpo', 'site-editor'),
                    'easeInOutExpo'         => __('easeInOutExpo', 'site-editor'),
                    'easeInCirc'            => __('easeInCirc', 'site-editor'),
                    'easeOutCirc'           => __('easeOutCirc ', 'site-editor'),
                    'easeInOutCirc'         => __('easeInOutCirc', 'site-editor'),
                    'easeInElastic'         => __('easeInElastic', 'site-editor'),
                    'easeOutElastic'        => __('easeOutElastic', 'site-editor'),
                    'easeInOutElastic'      => __('easeInOutElastic', 'site-editor'),
                    'easeInBack'            => __('easeInBack', 'site-editor'),
                    'easeOutBack'           => __('easeOutBack', 'site-editor'),
                    'easeInOutBack'         => __('easeInOutBack', 'site-editor'),
                    'easeInBounce'          => __('easeInBounce', 'site-editor'),
                    'easeOutBounce'         => __('easeOutBounce', 'site-editor'),
                    'easeInOutBounce'       => __('easeInOutBounce ', 'site-editor'),
                ),
                'panel'    => 'advance_settings_panel',

            ),

            'scroll_animate_duration' => array(
                'type' => 'number',
                'label' => __('scroll Animate Duration', 'site-editor'),
                'description'  => __('This feature allows you to specify the time it takes to, after clicking the Anchors, scroll move as animated to reach the target.', 'site-editor'),
                "js_params"  =>  array(
                    'min' => 1 ,
                    'step'  => 100
                ),
                'panel'    => 'advance_settings_panel',
            ),

            'enable_draggable_area' => array(
                'type' => 'checkbox',
                'label' => __('Enable Draggable Area', 'site-editor'),
                'description'  => __('This feature allows you disable/enable the draggable area on the left or right of the menu. Enabling this area, you can drag and drop the modules such as Image, Social bar, Icon, Button, and Search here and create attractive menus.', 'site-editor'),
                'panel'    => 'advance_settings_panel',
            ),

            'draggable_area_direction' => array(
                'type' => 'select',
                'label' => __('Draggable Area Direction', 'site-editor'),
                'description'  => __('This feature allows you to select that the draggable area be on the right or the left side of menu. ', 'site-editor'),
                'choices'   =>array(
                    'left'     => __('Left', 'site-editor'),
                    'right'     => __('Right ', 'site-editor'),
                ),
                'panel'    => 'advance_settings_panel',
                "dependency" => array(
                    'controls'  =>  array(
                        "control"  =>  "enable_draggable_area" ,
                        "value"    =>  true,
                    )
                ), 

            ),

            'draggable_area_width' => array(
                'type' => 'number',
                'label' => __('Draggable Area Width', 'site-editor'),
                'description' => __('This feature allows you to specify the draggable area width. ', 'site-editor'),
                'panel'    => 'advance_settings_panel',
                "dependency" => array(
                    'controls'  =>  array(
                        "control"  =>  "enable_draggable_area" ,
                        "value"    =>  true,
                    )
                ), 
            ),

            'image_width' => array(
                'type' => 'number',
                'label' => __('Image Width', 'site-editor'),
                'description' => __('This feature allows you to specify the Image Thumbnail width, which are created in the WordPress admin for menu items. (The maximum size is 64Px).', 'site-editor'),
                'panel'    => 'image_icon_settings_panel',
            ),
            'image_height' => array(
                'type' => 'number',
                'label' => __('Image Height', 'site-editor'),
                'description'  => __('This feature allows you to specify the Image Thumbnail height, which are created in the WordPress admin for menu items. (The maximum size is 64Px).', 'site-editor'),
                'panel'    => 'image_icon_settings_panel',
            ),
            'icon_font_size' => array(
                'type' => 'number',
                'label' => __('Icon Font Size', 'site-editor'),
                'description'  => __('This feature allows you to specify the size of Font Icons, which are created in the WordPress admin for menu items.', 'site-editor'),
                'panel'    => 'image_icon_settings_panel',
            ),
            'img_align' => array(
                'type' => 'select',
                'label' => __('Image Align ', 'site-editor'),
                'description'  => __('This feature allows you to align the Image Thumbnails with respect to the menu items and specify that the images must be on which side of menu items; the options are: Top, left, Right, and Bottom.', 'site-editor'),
                'choices'   =>array(
                    'top'     => __('Top', 'site-editor'),
                    'right'     => __('Right ', 'site-editor'),
                    'bottom'     => __('Bottom ', 'site-editor'),
                    'left'     => __('Left ', 'site-editor'),
                ),
                'panel'    => 'image_icon_settings_panel',
            ),
            'icon_align' => array(
                'type' => 'select',
                'label' => __('Icon Align ', 'site-editor'),
                'description'  => __('This feature allows you to align the Font Icons with respect to the menu items and specify that the images must be on which side of menu items; the options are: Top, left, Right, and Bottom.', 'site-editor'),
                'choices'   =>array(
                    'top'     => __('Top', 'site-editor'),
                    'right'     => __('Right ', 'site-editor'),
                    'bottom'     => __('Bottom ', 'site-editor'),
                    'left'     => __('Left ', 'site-editor'),
                ),
                'panel'    => 'image_icon_settings_panel',
            ),
            'image_icon_preference' => array(
                'type' => 'select',
                'label' => __('preference Icons Or Image : ', 'site-editor'),
                'description'  => __('If you have created both of the Font Icon and Image Thumbnail for one item, then you must specify that in general, when there are two options for the items which one is the priority.', 'site-editor'),
                'choices'   =>array(
                    'icon'     => __('icon', 'site-editor'),
                    'image'     => __('image ', 'site-editor')
                ),
                'panel'    => 'image_icon_settings_panel',
            ), 
            'length' => array(
                "type"          => "length" ,
                "label"         => __("Length", "site-editor"),
            ),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "default"       => "0 0 0 0" ,
            ), 
            "align"     =>  array(
                "type"          => "align" ,
                "label"         => __("Align", "site-editor"),
                "default"       => "default",
                "dependency" => array(
                    'controls'  =>  array(
                        "control"  =>  "orientation" ,
                        "value"    =>  "horizontal",
                        "type"     =>  "exclude"
                    )
                ),                
            ),
            "skin"  => 'skin_refresh',
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
            //'row_container' => 'row_container',

        );

        return $params;

    }

    function custom_style_settings(){
        return array(                                                                      
            array(
                'navbar-wrap' , 'sed_current' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Menu Wrapper" , "site-editor") ) ,

            array(
            'brand-menu' , '.brand-menu .navbar-brand' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Logo" , "site-editor") ) ,

            array(
                'navbar-container' , '.navbar-container' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Navbar Container" , "site-editor") ) ,

            array(
                'menu_items' , 'ul.nav.navbar-nav > li.menu-item' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin','trancparency','shadow' , 'font' ) , __("Menu Items" , "site-editor") ) ,

            array(
                'menu_items_hover' , 'ul.nav.navbar-nav > li.menu-item:hover' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin','trancparency','shadow' ) , __("Menu Items Hover" , "site-editor") ) ,

            array(
                'menu_items_inner' , 'ul.nav.navbar-nav > li.menu-item > a' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin','trancparency','shadow' ) , __("Menu Items Inner" , "site-editor") ) ,

            array(
                'menu_items_inner_hover' , 'ul.nav.navbar-nav > li.menu-item > a:hover' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin','trancparency','shadow' ) , __("Menu Items Inner Hover" , "site-editor") ) ,

            array(
                'current-menu-item' , 'ul.nav.navbar-nav > li.menu-item.current-menu-item > a' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin','trancparency','shadow' ) , __("Current Menu Items" , "site-editor") ) ,

            array(
                'current-menu-ancestor' , 'ul.nav.navbar-nav > li.menu-item.current-menu-ancestor > a' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin','trancparency','shadow' ) , __("Current Menu Ancestor" , "site-editor") ) ,

            array( 'menu_text' , 'ul.nav.navbar-nav > li.menu-item > a > span' ,
                array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Menu Text" , "site-editor") ) ,

            array( 'menu_text_hover' , 'ul.nav.navbar-nav > li.menu-item > a:hover > span' ,
                array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Menu Text Hover" , "site-editor") ) ,

            array( 'menu_icons' , 'ul.nav.navbar-nav > li.menu-item > a .menu-item-icon' ,
                array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Menu Icons" , "site-editor") ) ,

            array( 'menu_icons_hover' , 'ul.nav.navbar-nav > li.menu-item > a:hover .menu-item-icon' ,
                array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Menu Icons Hover" , "site-editor") ) ,

            array( 'arrow' , 'ul.nav.navbar-nav > li.menu-item > a:after' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin','trancparency','shadow' ) , __(" Arrow" , "site-editor") ) ,

            array(
                'sub_menu_area' , 'ul.dropdown-menu' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin','trancparency','shadow' ) , __("Sub Menus" , "site-editor") ) ,

            array(
                'columns' , '.megamenu-content .row > .columns' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin','trancparency','shadow' ) , __("Sub Menus Columns" , "site-editor") ) ,

            array(
                'sub_menu_items' , 'ul.dropdown-menu li.menu-item > a' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin' ,'trancparency','shadow' ) , __("SubMenu Items" , "site-editor") ) ,

            array(
                'sub_menu_current' , 'ul.dropdown-menu li.menu-item.current-menu-item > a' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin' ,'trancparency','shadow' ) , __("Current SubMenu Items" , "site-editor") ) ,

            array(
                'sub_menu_items_hover' , 'ul.dropdown-menu li.menu-item > a:hover' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin' ,'trancparency','shadow' ) , __("SubMenu Items Hover" , "site-editor") ) ,

            array( 'sub_menu_text_hover' , 'ul.dropdown-menu li.menu-item > a > span' ,
                array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Sub Menu Text" , "site-editor") ),

            array( 'sub_menu_text' , 'ul.dropdown-menu li.menu-item > a:hover > span' ,
                array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Sub Menu Text Hover" , "site-editor") ),

            array( 'sub_menu_icons' , 'ul.dropdown-menu li.menu-item > a > .menu-item-icon' ,
                array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Sub Menu Icons" , "site-editor") ) ,

            array( 'sub_menu_icons_hover' , 'ul.dropdown-menu li.menu-item > a:hover > .menu-item-icon' ,
                array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Sub Menu Icons Hover" , "site-editor") ) ,

            array( 'sub_menu_with_desc' , 'ul.dropdown-menu li.menu-item.with-desc > a:after' ,
                array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Sub Menu Description" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
        $menu_menu = $context_menu->create_menu( "menu" , __("Menu","site-editor") , 'menu' , 'class' , 'element' , ''  , "sed_menu" , array(
            "seperator"    => array(75) ,
            "duplicate"        => false
        ));
    }

}

new PBMenuShortcode();

include SED_PB_MODULES_PATH . '/menu/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;
$sed_pb_app->register_module(array(
    "group"       => "theme" ,
    "name"        => "menu",
    "title"       => __("Menu","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-promenu",
    "shortcode"   => "sed_menu",
    "transport"   => "ajax" ,
    "helper_shortcodes" => array('sed_row_outer' => 'sed_row','sed_module_outer' => 'sed_module'),
    "sub_modules" => array('image'),
    "js_module"   => array( 'sed_menu_module_script', 'menu/js/menu-module.min.js', array('sed-frontend-editor') )
));



