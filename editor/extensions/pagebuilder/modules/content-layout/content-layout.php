<?php
/*
Module Name: Content Layout
Module URI: http://www.siteeditor.org/modules/columns
Description: Content Layout Module For Page Builder Application
Author: Site Editor Team @Pakage
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/
class PBContentLayoutShortcode extends PBShortcodeClass{

    /**
     * Module Actions Support , Allowed : "remove" , "duplicate" , "edit" , "move"
     *
     * @var string
     * @access public
     */
    public $actions = array( 'edit' );

    /**
     * @var string
     * @access public
     */
    public static $content_layout_patterns = array();
	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_content_layout",                               //*require
                "title"       => __("Content Layout","site-editor"),                 //*require for toolbar
                "description" => __("Add Content Layout to page","site-editor"),
                "icon"        => "sedico-post",                               //*require for icon toolbar
                "module"      => "content-layout",         //*require
            ) // Args
		);

        if( site_editor_app_on() ){
            add_action( "wp_footer" , array( $this , "print_content_layout_patterns" ) );
        }

        $this->set_layout_patterns();

        add_filter( 'sed_shortcode_has_preset' , array( $this , 'remove_preset_support' ) , 10 , 2 );
    }

    function remove_preset_support( $is_support , $shortcode ){

        if( $shortcode == "sed_content_layout" ){
            return false;
        }

        return $is_support;

    }

    function get_atts(){
        $atts = array(
            'layout'                => "without-sidebar" ,
            'length'                => 'boxed' ,
            'equal_column_width'    => false ,
            'content_width'         =>  "wrap-layout-fixed-width",
        );
        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
        extract($atts);

    }

    function shortcode_settings(){

        /*$dropdown_html = "";
        $dropdown_control = "sed_content_layout_layout";
        ob_start();
        ?>
        <div class="dropdown" id="sed-app-control-<?php echo $dropdown_control ;?>">

            <div class="dropdown-content sed-dropdown content">
                <div>
                    <ul>
                        <li>
                            <a class="heading-item" href="#"><?php echo __("Select Content Layout" ,"site-editor");  ?></a>
                        </li>
                        <li>
                            <ul class="box-items">
                                <?php
                                foreach( self::$content_layout_patterns AS $id => $pattern ) {
                                    $class = ( $this->atts['layout'] == $id ) ? "selected-layout" : "";
                                    ?>
                                    <li class="content-leyout-item <?php echo $class;?>" data-value="<?php echo $id;?>"><a href="#"><?php echo $id;?></a></li>
                                    <?php
                                }
                                ?>
                                <li class="clr"></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
        <?php
        $dropdown_html = ob_get_contents();
        ob_end_clean();*/

        $options = array( );

        foreach( self::$content_layout_patterns AS $id => $pattern ) {

            $options[$id] = $id;

        }

        $this->add_panel( 'content_layout_settings_panel_outer' , array(
            'title'                   =>  __('Content Layout Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'inner_box' ,
            'priority'                => 9 ,
            'btn_style'               => 'menu' ,
            'has_border_box'          => false ,
            'icon'                    => 'sedico-post' ,
            'field_spacing'           => 'sm'
        ) );

        $this->add_panel( 'content_layout_settings_panel' , array(
            'title'                   =>  __('Content Layout Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'default' ,
            'parent_id'               => "content_layout_settings_panel_outer",
            'priority'                => 1 ,
        ) );        

        $params = array(

    		/*'layout' => array(
                'type'              =>  'custom',
                'js_type'           =>  'dropdown',
                //'has_border_box'    =>   true ,
                'custom_template'   =>  $dropdown_html ,
                'js_params'     =>  array(
                    'options_selector'    => '.content-leyout-item',
                    'selected_class'      => 'selected-layout'
                ),
                'has_border_box'      => false ,
                'panel'    => 'content_layout_settings_panel',
    		),*/

            'layout' => array(
                'type'              =>  'select',
                "label"             => __("Select Content Layout", "site-editor"),
                //"description"       => __("Select Content Layout", "site-editor"),
                "choices"           =>  $options,
                'has_border_box'    => false ,
                'panel'    => 'content_layout_settings_panel',
            ),

            /*'length'                => array(
                "type"                  => "length" ,
                "label"                 => __("Length", "site-editor"),
                'panel'                 => 'content_layout_settings_panel',
            ), */

            'content_width' => array(
                "type"          => "radio-buttonset" ,
                "label"         => __("Content Width", "site-editor"),
                "choices"       =>  array(
                    "wrap-layout-full-width"       =>    __('Full Width',"site-editor") ,
                    "wrap-layout-fixed-width"      =>    __('Fixed Width',"site-editor") , 
                ), 
                'has_border_box'    => false ,
                "panel"         => "content_layout_settings_panel" ,
            ),

            'row_container' => array(
                'type'                => 'row_container',
                'label'               => __('Module Wrapper Settings', 'site-editor')
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
                'columns' , 'sed_current' ,
                array( 'background','gradient','border','trancparency' ) , __("Tr Columns" , "site-editor") 
            ) ,

            array(
                'column' , '>td.sed-column-pb' ,
                array( 'background','gradient' ) , __("Td Column" , "site-editor") 
            ) ,

        );
    }


    function contextmenu( $context_menu ){
      $content_layout_menu = $context_menu->create_menu( "content-layout" , __("Content Layout","site-editor") , 'content-layout' , 'class' , 'element' , '' , "sed_content_layout" , array(
            "seperator"        => array(45 , 75)
        ));
      //$context_menu->add_change_column_item( $columns_menu );
    }

    function set_layout_patterns(){
        $patterns = array(

            "without-sidebar" => '
                [sed_content_layout_column width="100%" sed_main_content = "yes" parent_module="content-layout"]
                    {{content}}
                [/sed_content_layout_column]
            ',

            "left-sidebar" => '
                [sed_content_layout_column width="29%" parent_module="content-layout"]

                [/sed_content_layout_column]
                [sed_content_layout_column width="71%" sed_main_content = "yes" parent_module="content-layout"]
                    {{content}}
                [/sed_content_layout_column]
            ',

            "right-sidebar" => '
                [sed_content_layout_column width="71%" sed_main_content = "yes" parent_module="content-layout"]
                    {{content}}
                [/sed_content_layout_column]
                [sed_content_layout_column width="29%" parent_module="content-layout"]

                [/sed_content_layout_column]
            ',

            "lef-right-sidebar" => '
                [sed_content_layout_column width="19%" parent_module="content-layout"]

                [/sed_content_layout_column]
                [sed_content_layout_column width="62%" sed_main_content = "yes" parent_module="content-layout"]
                    {{content}}
                [/sed_content_layout_column]
                [sed_content_layout_column width="19%" parent_module="content-layout"]

                [/sed_content_layout_column]
            ',

            "two-left-sidebar" => '
                [sed_content_layout_column width="19%" parent_module="content-layout"]

                [/sed_content_layout_column]
                [sed_content_layout_column width="19%" parent_module="content-layout"]

                [/sed_content_layout_column]
                [sed_content_layout_column width="62%" sed_main_content = "yes" parent_module="content-layout"]
                    {{content}}
                [/sed_content_layout_column]
            ',

            "two-right-sidebar" => '
                [sed_content_layout_column width="62%" sed_main_content = "yes" parent_module="content-layout"]
                    {{content}}
                [/sed_content_layout_column]
                [sed_content_layout_column width="19%" parent_module="content-layout"]

                [/sed_content_layout_column]
                [sed_content_layout_column width="19%" parent_module="content-layout"]

                [/sed_content_layout_column]
            ',

            "three-right-sidebar" => '
                [sed_content_layout_column width="55%" sed_main_content = "yes" parent_module="content-layout"]
                    {{content}}
                [/sed_content_layout_column]
                [sed_content_layout_column width="15%" parent_module="content-layout"]

                [/sed_content_layout_column]
                [sed_content_layout_column width="15%" parent_module="content-layout"]

                [/sed_content_layout_column]
                [sed_content_layout_column width="15%" parent_module="content-layout"]

                [/sed_content_layout_column]
            ',

            "three-left-sidebar" => '
                [sed_content_layout_column width="15%" parent_module="content-layout"]

                [/sed_content_layout_column]
                [sed_content_layout_column width="15%" parent_module="content-layout"]

                [/sed_content_layout_column]
                [sed_content_layout_column width="15%" parent_module="content-layout"]

                [/sed_content_layout_column]

                [sed_content_layout_column width="55%" sed_main_content = "yes" parent_module="content-layout"]
                    {{content}}
                [/sed_content_layout_column]
            ',

            "two-left-one-right-sidebar" => '
                [sed_content_layout_column width="15%" parent_module="content-layout"]

                [/sed_content_layout_column]
                [sed_content_layout_column width="15%" parent_module="content-layout"]

                [/sed_content_layout_column]

                [sed_content_layout_column width="55%" sed_main_content = "yes" parent_module="content-layout"]
                    {{content}}
                [/sed_content_layout_column]

                [sed_content_layout_column width="15%" parent_module="content-layout"]

                [/sed_content_layout_column]
            ',

            "one-left-two-right-sidebar" => '
                [sed_content_layout_column width="15%" parent_module="content-layout"]

                [/sed_content_layout_column]

                [sed_content_layout_column width="55%" sed_main_content = "yes" parent_module="content-layout"]
                    {{content}}
                [/sed_content_layout_column]

                [sed_content_layout_column width="15%" parent_module="content-layout"]

                [/sed_content_layout_column]

                [sed_content_layout_column width="15%" parent_module="content-layout"]

                [/sed_content_layout_column]
            '
        );

        self::$content_layout_patterns = $patterns;

    }

    function print_content_layout_patterns(){

        $patterns_model = array();

        foreach( self::$content_layout_patterns AS $id => $pattern ) {
            $shortcodes_model = PageBuilderApplication::get_pattern_shortcodes($pattern, "root", "content-layout" , "sed_content_layout" );
            $patterns_model[$id] = $shortcodes_model['shortcodes'];
        }
        ?>
            <script type="text/javascript">
                var _sedAppContentLayoutPatterns = <?php echo wp_json_encode( $patterns_model ); ?>;
            </script>
        <?php
    }

}

new PBContentLayoutShortcode(); 

include SED_PB_MODULES_PATH . '/content-layout/includes/column.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"                 => "basic" ,
    "name"                  => "content-layout",
    "title"                 => __("Content Layout","site-editor"),
    "description"           => __("Add Full Customize Content Layout","site-editor"),
    "icon"                  => "sedico-post",
    "type_icon"             => "font",
    "is_special"            => true ,
    "has_extra_spacing"     => true ,
    "show_ui_in_toolbar"    => false ,
    "tpl_type"              => "underscore" ,
    "shortcode"             => "sed_content_layout",
    "priority"              => 10 ,
    "js_module"             => array( 'sed-content-layout-module', 'content-layout/js/sed-content-layout-module.min.js', array('sed-frontend-editor') )
));
