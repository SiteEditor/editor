<?php
/**
 * @package
 *
 * @SiteEditor.Platform
 *
 * @subpackage  AppOptionsEngine
 *
 * @copyright   Copyright (C) 2013 - 2016 , Inc. All rights reserved.
 * @license     see LICENSE
 */

defined('_SEDEXEC') or die;

/**
 * Options Engine For Site Editor Application Using In Theme options , general settings , module settings , ...
 *
 * @package     
 *
 * @SiteEditor.Platform
 *
 * @subpackage  AppOptionsEngine
 * @since       1.0.0
 */
Class AppOptionsEngine {


    public $group_name = "";

    public $group;

    public $controls = array();

    public $params = array();

    public $panels = array();
    /**
    * @var    icon base url for toolbar element
    * @since  1.0.0
    */


    /**
    * Class constructor.
    *
    * @desc    wp_parse_args do not orginal zmind or php fuction
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

        global $site_editor_app;
        $sed_addon_settings["optionsEngine"] = array(
            'nonce'  => array(
                'load'  =>  wp_create_nonce( 'sed_app_options_load_' . $site_editor_app->get_stylesheet() ) ,
            )
        );

        return $sed_addon_settings;
    }


    /*function add_design_options_to_modules(){
        $this->add_style_settings();

        ob_start();
        ?>
        <div class="sed_style_editor_panel_container">

        </div>
        <div id="modules_styles_settings_<?php echo $this->shortcode->name;?>_level_box" data-multi-level-box="true" data-title="" class="sed-dialog content " >

            <div class="styles_settings_container">

            </div>

        </div>
        <?php
        $dialog_content = ob_get_clean();

        if( $this->has_styles_settings === true ){
            $params[ 'design_panel' ] = array(
                'type' => 'panel-button',
                'label' => __('Custom Edit Style',"site-editor"),
                'description' => '',
                'style' => 'blue' ,
                'class' => 'sed_style_editor_btn' ,
                'dialog_title' => __('Custom Edit Style',"site-editor") ,
                'dialog_content' => $dialog_content ,
                'priority'      => 0
            );
        }
    }*/

    /*
    * @Function :: static , this func create params for groups
    * @groups  :: groups are collection settings for same settings like image module settings or general settings or background settings
    * @params :: input params for proccess
    * @panels :: all panels for this group
    * @base_category :: settings include of 3 category : 1. module-settings  2. style-editor  3. ....
    * @set_group_params steps :
    * 1. Process params type
    * 2. Process params dependencies
    * 3. Process panels dependencies
    * 4. Process params control type
    * 5. Create controls for params
    * 6. Added Controls , Params And Panels to Final Settings
    */
    /*public function set_group_params( $group , $params_title , $params = array() , $panels = array() , $base_category = "module-settings" ){

        $controls = array();
        $this->group_name = $group;

        if(!empty($params)){

            $params = $this->params_type_process( $params );


            if( !empty($panels) && is_array($panels) ){
                if( !isset( $this->panels[ $group ] ) )
                    $this->panels[ $group ] = array();

                $this->panels[ $group ] = array_merge( $this->panels[ $group ] , $panels );
            }

            ModuleSettings::$group_id = $group;
            if( isset( $this->panels[ $group ] ) && !empty( $this->panels[ $group ] ) ){
                $cr_settings = ModuleSettings::create_settings($params , $this->panels[ $group ] );
            }else{
                $cr_settings = ModuleSettings::create_settings($params );
            }

            ModuleSettings::$group_id = "";

            $this->params[ $group ] = array(
                "settings_title"  => $params_title,
                "settings_output" => $cr_settings
            );

            foreach($params AS $key => $param){

                $control_id = $group."_".$key;

                if( $key != "content" ){

                    $control = $this->create_control($group , $key , $param );
                    if( !empty( $control ) && $control )
                        $controls[$control_id] = $control;

                }
            }

        }

        if( !empty( $controls ) ){
            $this->add_controls( $controls , $group );
        }

    }*/

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
                    //Todo
                    case "spacing" :
                        unset( $param['type'] );
                        $spacing_params = $this->add_spacing_control( $param );
                        $new_params = array_merge( $new_params , $spacing_params );
                        break;
                    //Todo
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
                    //Todo
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

        $panel_title = isset($args['label']) ?  $args['label'] : __('Link To',"site-editor") ;
        $description = isset($args['description']) ?  $args['description'] : '' ;
        $priority = isset($args['priority']) ?  $args['priority'] : 70 ;
        $value = isset($args['value']) ?  $args['value'] : "0 0 0 0";

        $panel = array(
            'title'         =>  $panel_title ,
            'description'   => $description ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'inner_box' ,
            'parent_id'     => 'root',
            'is_attr'       => true ,
            'priority'      => $priority ,
            'in_box'        => true
        );

        if( isset( $args['dependency'] ) ){
            $panel['dependency'] = $args['dependency'];
        }

        $this->group->add_panel( 'link_to_panel' , $panel );


        $params = array();

        $params["link"] = array(
            'type'          => 'text',
            'label'         => __('Url : ', 'site-editor'),
            'description'   => __('Add Link to any module elements that needs a link.', 'site-editor') ,
            'value'         => $value,
            'placeholder'   => 'E.g www.siteeditor.org' ,
            'panel'         => 'link_to_panel'
        );

        $params["link_target"] = array(
            'type'          => 'radio',
            'label'         => __('Link Target : ', 'site-editor'),
            'description'   => __('Add Link target : open link in new window or same window', 'site-editor') ,
            'value'         => "_self",
            'options'       => array(
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
        $value = isset($args['value']) ?  $args['value'] : "0 0 0 0";

        /**
         * Parse incoming $args into an array and merge it with $defaults
         */
        $panel_id = $this->group_name . "_spacing_settings_panel";
        $this->group->add_panel( $panel_id , array(
            'title'         =>  $panel_title ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'default' ,
            'description'   => $description ,
            'priority'      => $priority ,
        ));

        $values = explode(" ", trim($value) );
        $values = array_filter($values , array( $this , "spacing_values_filter" ) );
        $values = array_map( "absint" , $values );
        list( $padding_top , $padding_left , $padding_bottom , $padding_right ) = $values;

        $settings = array();
        $lock_id = "sed_pb_" . $this->group_name . "_spacing_lock";

        $spinner_class = 'sed-spacing-spinner-' . $this->group_name;
        $spinner_class_selector = '.' . $spinner_class;
        $sh_name = $this->group_name;
        $sh_name_c = $sh_name. "_spacing_";

        $controls = array( $sh_name_c . "top" , $sh_name_c . "right" , $sh_name_c . "left" , $sh_name_c . "bottom" );

  		$settings['spacing_top'] = array(
  			'type' => 'number',
            'after_field'  => 'px',
            'value' => $padding_top,
  			'label' => __('Top', 'site-editor'),
  			'description' => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
            'is_attr'   =>  true ,
            'atts'  => array(
                "class" =>   $spinner_class
            ) ,
            'control_param'     =>  array(
                'lock'    => array(
                    'id'       => $lock_id,
                    'spinner'  => $spinner_class_selector,
                    'controls' => array( $sh_name_c . "right" , $sh_name_c . "left" , $sh_name_c . "bottom" )
                ),
                'min'   =>  0 ,
                'selector' =>  'sed_current' ,
                'style_props'       =>  "padding-top" ,
            ),
            'control_category'  => "style-editor" ,
            'settings_type'     =>  "padding_top" ,
            "panel"     =>  $panel_id
            //'in_box'   =>'false'
  		);

  		$settings['spacing_left'] = array(
  			'type' => 'number',
            'after_field'  => 'px',
            'value' => $padding_left,
  			'label' => is_rtl() ? __('Right', 'site-editor') : __('Left', 'site-editor'),
  			'description' => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
            'is_attr'   =>  true ,
            'atts'  => array(
                "class" =>   $spinner_class
            ) ,
            'control_param'     =>  array(
                'lock'    => array(
                    'id'       => $lock_id,
                    'spinner'  => $spinner_class_selector,
                    'controls' => array( $sh_name_c . "top" , $sh_name_c . "right" , $sh_name_c . "bottom" )
                ),
                'min'   =>  0  ,
                'selector' =>  'sed_current' ,
                'style_props'       =>  "padding-left" ,
            ),
            'control_category'  => "style-editor" ,
            'settings_type'     =>  "padding_left" ,
            "panel"     =>  $panel_id
  		);

  		$settings['spacing_right'] = array(
  			'type' => 'number',
            'after_field'  => 'px',
            'value' => $padding_right,
  			'label' => is_rtl() ? __('Left', 'site-editor') : __('Right', 'site-editor'),
  			'description' => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
            'is_attr'   =>  true ,
            'atts'  => array(
                "class" =>   $spinner_class
            ) ,
            'control_param'     =>  array(
                'lock'    => array(
                    'id'       => $lock_id,
                    'spinner'  => $spinner_class_selector,
                    'controls' => array( $sh_name_c . "top" , $sh_name_c . "left" , $sh_name_c . "bottom" )
                ),
                'min'   =>  0 ,
                'selector' =>  'sed_current' ,
                'style_props'       =>  "padding-right" ,
            ),
            'control_category'  => "style-editor" ,
            'settings_type'     =>  "padding_right" ,
            "panel"     =>  $panel_id
  		);

  		$settings['spacing_bottom'] = array(
  			'type' => 'number',
            'after_field'  => 'px',
            'value' => $padding_bottom,
  			'label' => __('Bottom', 'site-editor'),
  			'description' => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
            'is_attr'   =>  true ,
            'atts'  => array(
                "class" =>   $spinner_class
            ) ,
            'control_param'     =>  array(
                'lock'    => array(
                    'id'       => $lock_id,
                    'spinner'  => $spinner_class_selector ,
                    'controls' => array( $sh_name_c . "top" , $sh_name_c . "right" , $sh_name_c . "left" )
                ),
                'min'   =>  0  ,
                'selector' =>  'sed_current' ,
                'style_props'       =>  "padding-bottom" ,
            ),
            'control_category'  => "style-editor" ,
            'settings_type'     =>  "padding_bottom" ,
            "panel"     =>  $panel_id
  		);

  		$settings['spacing_lock'] = array(
  			'type'          => 'checkbox',
            'value'         => false,
  			'label'         => __('lock Spacing Together', 'site-editor'),
  			'description'   => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
            'is_attr'       =>  true ,
            'control_type'  =>  "spinner_lock" ,
            'atts'          => array(
                    "class"     =>   "sed-lock-spinner"
            ) ,
            'control_param'     =>  array(
                'spinner' =>  $spinner_class_selector ,
                'controls' => array( $sh_name_c . "top" , $sh_name_c . "right" , $sh_name_c . "left" , $sh_name_c . "bottom" )
            ),
            "panel"     =>  $panel_id
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
  			'type'          => 'radio-buttonset',
            'value'         => "" ,
  			'label'         => __('Align', 'site-editor'),
  			'description'   => __('Module container alignment', 'site-editor'),
            'choices' =>array(
                'initial'       => __('Default', 'site-editor'),
                'left'          => is_rtl() ?  __('Right', 'site-editor') : __('Left', 'site-editor'),
                'center'        => __('Center', 'site-editor'),
                'right'         => is_rtl() ? __('Left', 'site-editor') :  __('Right', 'site-editor'),
            )
        );

        /**
         * Parse incoming $args into an array and merge it with $defaults
         */
        $param = wp_parse_args( $args, $defaults ) ;

        $required = array(
            'js_params'     =>  array(
                'selector'          =>  'sed_current' ,
                'style_props'       =>  "text-align" ,
            ),

            'category'          => "style-editor" ,
            'setting_id'        =>  "text_align"
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
            'priority'      => 20
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
            'value'         => 'wide' ,
  			'label'         => __('Length', 'site-editor'),
  			'description'   => __('container Length', 'site-editor'),
            'choices'       => array(
                'wide'          => __('Wide', 'site-editor'),
                'boxed'         => __('Boxed', 'site-editor')
            ),
            'priority'      => 15
        );

        /**
         * Parse incoming $args into an array and merge it with $defaults
         */
        $param = wp_parse_args( $args, $defaults ) ;
  		return $param;

    }

    /*'value' => "1000,1,1000,,0",
    function add_animation( $args ){}
    */


    function add_skin_setting($value = "default"){

        return array(
            'type'          => 'skin',
            'value'         => $value,
            'label'         => __('Change Skin', 'site-editor'),
            'button_style'  => 'black',
            'priority'      => 2
        );

    }


    function add_group_skin_control( $args = array() ){

        $label      = isset($args['label']) ?  $args['label'] : __('Items Change Skin',"site-editor") ;
        $sub_module = isset($args['sub_module']) ?  $args['sub_module'] : '' ;
        $priority   = isset($args['priority']) ?  $args['priority'] : 100 ;
        $value      = isset($args['value']) ?  $args['value'] : "default";

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
            "label"         => __('Change Panel',"site-editor"),
            "description"   => '',
            "priority"      => 10 ,
            "panel_type"    => 'inner_box' ,
            'controls'      => array(
                'image_source'          => 'image_source' ,
                'image_url'             => 'image_url' ,
                'attachment_id'         => 'attachment_id' ,
                'default_image_size'    => 'default_image_size' ,
                'custom_image_size'     => 'custom_image_size' ,
                'external_image_size'   => 'external_image_size'
            ),
            'values'        => array(
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
            'parent_id'     => 'root',
            'priority'      => $priority ,
        ) );

        $params[$controls["image_source"]] = array(
            'label'         => __('Image Source', 'site-editor'),
            'description'   => __('Select image source.', 'site-editor'),
            'type'          => 'radio-buttonset',
            'choices'       =>  array(
                "attachment"     => __('Media Library', 'site-editor')  ,
                "external"       => __('External Link', 'site-editor')  ,
            ),
            'value'         => $values['image_source'],
            'panel'         => 'sed_select_image_panel'
        );

        $params[$controls["image_url"]] = array(
            'label'         => __('External link', 'site-editor'),
            'description'   => __('Enter an external link.', 'site-editor'),
            'type'          => 'text',
            'value'         => $values["image_url"],
            'panel'         => 'sed_select_image_panel',
            'dependency' => array(
                'controls'  =>  array(
                    "control"  => $controls["image_source"] ,
                    "value"    => "external" ,
                )
            )
        );

        $params[$controls["attachment_id"]] = array(
            'label'             => __('Select image', 'site-editor'),
            'description'       => __('Select image from media library.', 'site-editor'),
            'type'              => 'image',
            'value'             => $values["attachment_id"],
            'panel'             => 'sed_select_image_panel' ,
            "control_param"     => array(
                "rel_size_control"          => $this->group_name . "_default_image_size"
            ),
            'dependency' => array(
                'controls'  =>  array(
                    "control"  => $controls["image_source"] ,
                    "value"    => "attachment" ,
                )
            )
        );

        $params[$controls["default_image_size"]] = array(
            'label'         => __('Select Image Size', 'site-editor'),
            'description'   => __('Select a Image Size Or Select Custom size for enter size in px', 'site-editor'),
            'type'          => 'image-size',
            'value'         => $values["default_image_size"],
            'js_params'     =>  array(
                "has_custom_size"   => true
            ),
            'panel'         => 'sed_select_image_panel' ,
            'dependency' => array(
                'controls'  =>  array(
                    "relation"     =>  "and" ,
                    array(
                        "control"  => $controls["attachment_id"] ,
                        "values"    => array( "" , 0 ) ,
                        "type"     => "exclude"
                    ),
                    array(
                        "control"  => $controls["image_source"] ,
                        "value"    => "attachment" ,
                    )
                )
            )
        );

        $params[$controls["custom_image_size"]] = array(
            'label'         => __('Custom Image Size', 'site-editor'),
            'description'   => __('Enter custom size in pixels (Example: 100x300 (Width x Height)).', 'site-editor'),
            'type'          => 'text',
            'value'         => $values["custom_image_size"],
            'panel'         => 'sed_select_image_panel' ,
            'dependency' => array(
                'controls'  =>  array(
                    "relation"     =>  "and" ,
                    array(
                        "control"  => $controls["default_image_size"] ,
                        "value"    => "" ,
                    ),
                    array(
                        "control"  => $controls["image_source"] ,
                        "value"    => "attachment" ,
                    )
                )
            )
        );

        $params[$controls["external_image_size"]] = array(
            'label'         => __('Custom Image Size', 'site-editor'),
            'description'   => __('Enter custom size in pixels (Example: 100x300 (Width x Height)).', 'site-editor'),
            'type'          => 'text',
            'value'         => $values["external_image_size"],
            'panel'         => 'sed_select_image_panel' ,
            'dependency' => array(
                'controls'  =>  array(
                    "control"  => $controls["image_source"] ,
                    "value"    => "external" ,
                )
            )
        );

        return $params ;
    }


}