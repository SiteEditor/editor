<?php
/**
 * Page Builder Options Manager classes
 *
 * @package SiteEditor
 * @subpackage pagebuilder
 * @since 1.0.0
 */

/**
 * Page Builder Options Manager class.
 *
 * Manage all Page Builder Options
 *
 * For manage and send to SiteEditor Options Manager Class
 *
 * @since 1.0.0
 */

final class SedPageBuilderOptionsManager{

    /**
     * Page Builder Group name of Options
     *
     * @since 1.0.0
     * @access protected
     * @var array
     */
    public $group_name = "";

    /**
     * Page Builder Group instance
     * Each shortcode is a group
     *
     * @since 1.0.0
     * @access public
     * @var array
     */
    public $group;

    /**
     * Class constructor.
     *
     * @since   1.0.0
     */
    function __construct( ) {

        add_filter( "sed_addon_settings"    , array($this,'addon_settings') );

        add_filter( "sed_js_I18n"           , array($this,'js_I18n'));
    }


    function js_I18n( $I18n ){

        $I18n['custom_size']            =  __("Custom Size","site-editor");
        $I18n['organize_tab_title']     =  __("Edit Gallery","site-editor");
        $I18n['update_btn_title']       =  __("Update gallery","site-editor");
        $I18n['cancel_btn_title']       =  __("Cancel","site-editor");
        $I18n['images_gallery_update']  =  __("Update Images gallery","site-editor");
        $I18n['add_btn_title']          =  __("Add To Gallery","site-editor");

        return $I18n;
    }

    function addon_settings( $sed_addon_settings ){

        $sed_addon_settings["imageModule"] = array(
            "sizes"               => sed_get_image_sizes() ,
            "dialog_title"        =>  __("Image Library") ,
            "add_btn_title"       =>  __("Change Image","site-editor")
        );

        return $sed_addon_settings;
    }

    public function sanitize_control_value( $value ){

        if( $value === "true" )
            $value = true;
        else if( $value === "false" )
            $value = false;

        return $value;
    }

    /**
     * alias control type process for developer friendly
     *
     * @param $params
     * @param $group === shortcode
     * @return array
     */
    public function params_type_process( $params , &$group ){

        $this->group = $group;

        $this->group_name = $group->shortcode->name;

        $new_params = array();

        foreach( $params  As $key => $param ){

            if( isset( $param['type'] ) ){
                switch ( $param['type'] ) {

                    case 'spinner' :
                        $param['type'] = 'number';
                        $new_params[$key] = $param;
                        break;

                    case 'image_size' :
                        $param['type'] = 'image-size';
                        $new_params[$key] = $param;
                        break;

                    case "row_container" :
                        unset( $param['type'] );
                        $param = $this->add_row_container_setting( $param );
                        $new_params[$key] = $param;
                        break;

                    case "align" :
                        unset( $param['type'] );
                        $param = $this->add_align_control( $param );
                        $new_params[$key] = $param;
                        break;

                    case "spacing" :
                        unset( $param['type'] );
                        $spacing_params = $this->add_spacing_control( $param );
                        $new_params = array_merge( $new_params , $spacing_params );
                        break;

                    case "dropdown" :
                        $new_params[$key] = $param;
                        $new_params[$key]['type'] = "custom";
                        $new_params[$key]['js_type'] = 'dropdown';
                        break;

                    case "sed_image" :
                        unset( $param['type'] );
                        $img_params = $this->add_image_setting( $param );
                        $new_params = array_merge( $new_params , $img_params );
                        break;

                    case "link" :
                        unset( $param['type'] );
                        $link_params = $this->add_link_control( $param );
                        $new_params = array_merge( $new_params , $link_params );
                        break;

                    case "length" :
                        unset( $param['type'] );
                        $param = $this->add_length_control( $param );
                        $new_params[$key] = $param;
                        break;

                    case "group_skin" :
                        unset( $param['type'] );
                        $param = $this->add_group_skin_control( $param );
                        $new_params[$key] = $param;
                        break;

                    case "email" :
                    case "url" :
                    case "search" :
                    case "password" :
                    case "tel" :
                        $new_params[$key] = $param;
                        $new_params[$key]['type'] = "text";
                        $new_params[$key]['subtype'] = $param['type'];
                        break;

                    case "multi-checkbox" :
                        $new_params[$key] = $param;
                        $new_params[$key]['type'] = "multi-check";
                        break;

                    default :
                        $new_params[$key] = $param;

                }
            }
        }

        return $new_params;
    }

    function add_link_control( $args ){


        $panel_title    = isset($args['label']) ?  $args['label'] : __('Link To',"site-editor") ;

        $description    = isset($args['description']) ?  $args['description'] : '' ;

        $priority       = isset($args['priority']) ?  $args['priority'] : 520 ;

        $values         = isset($args['values']) ?  $args['values'] : array();

        $values         = array_merge( array(
            "link"          => '' ,
            "link_target"   => '_self'
        ), $values
        );

        $controls       = isset($args['controls']) ?  $args['controls'] : array();

        $controls       = array_merge( array(
            "link"          => "link" ,
            "link_target"   => "link_target"
        ), $controls
        );

        $panel_type     = isset($args['panel_type']) ?  $args['panel_type'] : 'inner_box';

        $capability     = isset($args['capability']) ?  $args['capability'] : 'edit_theme_options';

        $panel = array(
            'title'         => $panel_title ,
            'description'   => $description ,
            'capability'    => $capability ,
            'type'          => $panel_type ,
            'parent_id'     => 'root',
            'has_border_box'=> false ,
            'priority'      => $priority
        );

        if( isset( $args['panel_dependency'] ) ){
            $panel['dependency'] = $args['panel_dependency'];
        }

        $this->group->add_panel( 'link_to_panel' , $panel );


        $params = array();

        $params[$controls["link"]] = array(
            'type'          => 'text',
            'label'         => __('Url : ', 'site-editor'),
            'description'   => __('Add Link to any module elements that needs a link.', 'site-editor') ,
            'default'         => $values['link'],
            'placeholder'   => 'E.g www.siteeditor.org' ,
            'panel'         => 'link_to_panel'
        );

        $params[$controls["link_target"]] = array(
            'type'          => 'radio',
            'label'         => __('Link Target : ', 'site-editor'),
            'description'   => __('Add Link target : open link in new window or same window', 'site-editor') ,
            'default'         => $values['link_target'],
            'choices'       => array(
                "_blank"        => __('Open in new window', 'site-editor')  ,
                "_self"         => __('Open in same window', 'site-editor')  ,
            ),
            'panel'         => 'link_to_panel' ,
        );

        return $params;
    }

    function spacing_values_filter( $value ){
        if(trim($value) == "")
            return false;
        else
            return true;
    }

    /**
     * @Todo Move Spacing Control To Design Editor Controls
     *
     * @param array $args
     * @return array
     */
    function add_spacing_control( $args = array() ){

        $panel_title = isset($args['label']) ?  $args['label'] : __('Spacing',"site-editor") ;
        $description = isset($args['description']) ?  $args['description'] : '' ;
        $priority = isset($args['priority']) ?  $args['priority'] : 100 ;
        $value = isset($args['default']) ?  $args['default'] : "0 0 0 0";

        /**
         * Parse incoming $args into an array and merge it with $defaults
         */
        $panel_id = "spacing_settings_panel";

        $this->group->add_panel( $panel_id , array(
            'title'         =>  $panel_title ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'default' ,
            'description'   => $description ,
            'priority'      => $priority ,
            'parent_id'     => 'root'
        ));

        $values = explode(" ", trim($value) );

        $values = array_filter($values , array( $this , "spacing_values_filter" ) );

        $values = array_map( "absint" , $values );

        list( $padding_top , $padding_left , $padding_bottom , $padding_right ) = $values;

        $settings = array();

        $settings['spacing_top'] = array(
            'type'              => 'padding',
            'after_field'       => 'px',
            'default'           => $padding_top,
            "prop_side"         => "top" ,
            'label'             => __('Top', 'site-editor'),
            'description'       => __('Change Module Top Spacing', 'site-editor'),
            'js_params'         =>  array(
                'min'   =>  0  ,
            ),
            'category'          => "style-editor" ,
            'selector'          =>  'sed_current' ,
            "panel"             =>  $panel_id ,
            'lock_id'           => 'spacing_lock',
            'has_border_box'    => false
        );

        $settings['spacing_left'] = array(
            'type'              => 'padding',
            'after_field'       => 'px',
            'default'           => $padding_left,
            "prop_side"         => "left" ,
            'label'             => is_rtl() ? __('Right', 'site-editor') : __('Left', 'site-editor'),
            'description'       => __('Change Module Left Spacing', 'site-editor') ,
            'js_params'         =>  array(
                'min'   =>  0  ,
            ),
            'category'          => "style-editor" ,
            'selector'          =>  'sed_current' ,
            "panel"             =>  $panel_id ,
            'lock_id'           => 'spacing_lock',
            'has_border_box'    => false
        );

        $settings['spacing_right'] = array(
            'type'              => 'padding',
            'after_field'       => 'px',
            'default'           => $padding_right,
            "prop_side"         => "right" ,
            'label'             => is_rtl() ? __('Left', 'site-editor') : __('Right', 'site-editor'),
            'description'       => __('Change Module Right Spacing', 'site-editor') ,
            'js_params'         =>  array(
                'min'   =>  0  ,
            ),
            'category'          => "style-editor" ,
            'selector'          =>  'sed_current' ,
            "panel"             =>  $panel_id ,
            'lock_id'           => 'spacing_lock',
            'has_border_box'    => false
        );

        $settings['spacing_bottom'] = array(
            "type"              => "padding" ,
            'after_field'       => 'px',
            'default'           => $padding_bottom,
            "prop_side"         => "bottom" ,
            "label"             => __('Bottom', 'site-editor'),
            'description'       => __('Change Module Bottom Spacing', 'site-editor') ,
            'js_params'         =>  array(
                'min'   =>  0  ,
            ),
            'category'          => "style-editor" ,
            'selector'          => 'sed_current' ,
            "panel"             => $panel_id ,
            'lock_id'           => 'spacing_lock',
            'has_border_box'    => false
        );

        $settings['spacing_lock'] = array(
            'type'              => 'property-lock',
            'default'           => false,
            'label'             => __('lock Spacing Together', 'site-editor'),
            'description'       => __('Change Top , bottom , left and right Spacing Together', 'site-editor') ,
            "panel"             =>  $panel_id ,
            'category'          => "style-editor" ,
            'selector'          => 'sed_current' ,
            'setting_id'        => "spacing_lock" ,
            'has_border_box'    => false
        );

        return $settings;
    }

    /**
     * @Todo Move Alignment Control To Design Editor Controls
     *
     * @param array $args
     * @return array
     */
    function add_align_control( $args = array() ){

        /**
         * Define the array of defaults
         */
        $defaults = array(
            'default'         => "" ,
            'label'         => __('Align', 'site-editor'),
            'description'   => __('Module container alignment', 'site-editor')
        );

        /**
         * Parse incoming $args into an array and merge it with $defaults
         */
        $param = wp_parse_args( $args, $defaults ) ;

        $required = array(
            "type"              => "text-align" ,
            'category'          => "style-editor" ,
            'panel'             => 'root'
        );

        $param = wp_parse_args( $required, $param ) ;

        return $param;
    }

    /**
     * @param $args
     * @access private
     * @return array
     */
    private function add_row_container_setting( $args ){

        /**
         * Define the array of defaults
         */
        $defaults = array(
            'type'          => 'button',
            'label'         => __('Go To Row Settings', 'site-editor'),
            'description'   => __('Row Container Settings', 'site-editor'),
            'style'         => 'blue',
            'has_border_box'=> false ,
            'priority'      => 1030
        );

        /**
         * Parse incoming $args into an array and merge it with $defaults
         */
        $param = wp_parse_args( $args, $defaults ) ;

        if( ! isset( $param['atts'] ) ){
            $param['atts'] = array();
        }

        if( ! isset( $param['atts']['class'] ) ){
            $param['atts']['class'] = 'go-row-container-settings';
        }else {
            $param['atts']['class'] .= ' go-row-container-settings';
        }

        return $param;
    }

    function add_length_control( $args ){

        /**
         * Define the array of defaults
         */
        $defaults = array(
            'type'          => 'radio-buttonset',
            'default'         => 'wide' ,
            'label'         => __('Length', 'site-editor'),
            'description'   => __('container Length', 'site-editor'),
            'choices'       => array(
                'wide'          => __('Wide', 'site-editor'),
                'boxed'         => __('Boxed', 'site-editor')
            ),
            'priority'      => 500
        );

        /**
         * Parse incoming $args into an array and merge it with $defaults
         */
        $param = wp_parse_args( $args, $defaults ) ;
        return $param;

    }

    /*'default' => "1000,1,1000,,0",
    function add_animation( $args ){}
    */


    function add_skin_setting($value = "default"){

        return array(
            'type'          => 'skin',
            'default'         => $value,
            'label'         => __('Change Skin', 'site-editor'),
            'button_style'  => 'black',
            'has_border_box'    => false ,
            'priority'      => 1020
        );

    }


    function add_group_skin_control( $args = array() ){

        $label      = isset($args['label']) ?  $args['label'] : __('Items Change Skin',"site-editor") ;
        $sub_module = isset($args['sub_module']) ?  $args['sub_module'] : '' ;
        $priority   = isset($args['priority']) ?  $args['priority'] : 100 ;
        $value      = isset($args['default']) ?  $args['default'] : "default";

        $setting = $this->add_skin_setting( $value );
        $setting["label"]  = $label;
        $setting["atts"] = array();
        $setting["atts"]['data-module-name']  = $sub_module;
        $setting["priority"]  = 11;

        if( !empty( $args['js_params'] ) ){
            $setting["js_params"] = $args['js_params'] ;
        }
        if( !empty( $args['panel_id'] ) ){
            $setting["panel"] = $args['panel_id'];
        }

        return $setting;
    }

    /**
     * using for all type of settings module & app settings
     *
     * @param array $args
     * @access private
     * @return array
     */
    private function add_image_setting( $args = array() ){
        $params = array();

        /**
         * Define the array of defaults
         */
        $defaults = array(
            "label"             => __('Change Panel',"site-editor"),
            "description"       => '',
            "priority"          => 10 ,
            "panel_type"        => 'inner_box' ,
            'panel_dependency'  => array() ,
            'parent_id'         => 'root',

            'controls'          => array(
                'image_source'          => 'image_source' ,
                'image_url'             => 'image_url' ,
                'attachment_id'         => 'attachment_id' ,
                'default_image_size'    => 'default_image_size' ,
                'custom_image_size'     => 'custom_image_size' ,
                'external_image_size'   => 'external_image_size'
            ),

            'values'            => array(
                'image_source'          => 'attachment' ,
                'image_url'             => '' ,
                'attachment_id'         => 0 ,
                'default_image_size'    => 'thumbnail' ,
                'custom_image_size'     => '' ,
                'external_image_size'   => ''
            )

        );

        $param = wp_parse_args( $args, $defaults ) ;

        extract( $param );

        $this->group->add_panel( 'sed_select_image_panel' , array(
            'title'         => $label ,
            'description'   => $description ,
            'capability'    => 'edit_theme_options' ,
            'type'          => $panel_type ,
            'parent_id'     => $parent_id ,
            'priority'      => $priority ,
            'dependency'    => $panel_dependency
        ) );

        $params[$controls["image_source"]] = array(
            'label'         => __('Image Source', 'site-editor'),
            'description'   => __('Select image source.', 'site-editor'),
            'type'          => 'radio-buttonset',
            'choices'       =>  array(
                "attachment"     => __('Media Library', 'site-editor')  ,
                "external"       => __('External Link', 'site-editor')  ,
            ),
            'default'         => $values['image_source'],
            'panel'         => 'sed_select_image_panel'
        );

        $params[$controls["image_url"]] = array(
            'label'         => __('External link', 'site-editor'),
            'description'   => __('Enter an external link.', 'site-editor'),
            'type'          => 'text',
            'default'         => $values["image_url"],
            'panel'         => 'sed_select_image_panel',
            'dependency' => array(
                'queries'  =>  array(
                    array(
                        "key"       => $controls["image_source"] ,
                        "value"     => "external" ,
                        "compare"   => "=="
                    )
                )
            )
        );

        $params[$controls["attachment_id"]] = array(
            'label'             => __('Select image', 'site-editor'),
            'description'       => __('Select image from media library.', 'site-editor'),
            'type'              => 'image',
            'default'             => $values["attachment_id"],
            'panel'             => 'sed_select_image_panel' ,
            "js_params"     => array(
                "rel_size_control"          => $this->group_name . "_default_image_size"
            ),
            'dependency' => array(
                'queries'  =>  array(
                    array(
                        "key"       => $controls["image_source"] ,
                        "value"     => "attachment" ,
                        "compare"   => "=="
                    )
                )
            )
        );

        $params[$controls["default_image_size"]] = array(
            'label'         => __('Select Image Size', 'site-editor'),
            'description'   => __('Select a Image Size Or Select Custom size for enter size in px', 'site-editor'),
            'type'          => 'image-size',
            'default'         => $values["default_image_size"],
            'js_params'     =>  array(
                "has_custom_size"   => true
            ),
            'panel'         => 'sed_select_image_panel' ,
            'dependency' => array(
                'queries'  =>  array(
                    "relation"     =>  "AND" ,
                    array(
                        "key"       => $controls["attachment_id"] ,
                        "value"     => array( "" , 0 ) ,
                        "compare"   => "NOT IN"
                    ),
                    array(
                        "key"       => $controls["image_source"] ,
                        "value"     => "attachment" ,
                        "compare"   => "=="
                    )
                )
            )
        );

        $params[$controls["custom_image_size"]] = array(
            'label'         => __('Custom Image Size', 'site-editor'),
            'description'   => __('Enter custom size in pixels (Example: 100x300 (Width x Height)).', 'site-editor'),
            'type'          => 'text',
            'default'         => $values["custom_image_size"],
            'panel'         => 'sed_select_image_panel' ,
            'dependency' => array(
                'queries'  =>  array(
                    "relation"     =>  "AND" ,
                    array(
                        "key"       => $controls["default_image_size"] ,
                        "value"     => "" ,
                        "compare"   => "=="
                    ),
                    array(
                        "key"       => $controls["image_source"] ,
                        "value"     => "attachment" ,
                        "compare"   => "=="
                    )
                )
            )
        );

        $params[$controls["external_image_size"]] = array(
            'label'         => __('Custom Image Size', 'site-editor'),
            'description'   => __('Enter custom size in pixels (Example: 100x300 (Width x Height)).', 'site-editor'),
            'type'          => 'text',
            'default'         => $values["external_image_size"],
            'panel'         => 'sed_select_image_panel' ,
            'dependency' => array(
                'queries'  =>  array(
                    array(
                        "key"       => $controls["image_source"] ,
                        "value"     => "external" ,
                        "compare"   => "=="
                    )
                )
            )
        );

        return $params ;
    }

}


