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

    private $settings_dependencies = array();
    public $group_name = "";
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
    * @param   $args
    *
    *
    * @desc    wp_parse_args do not orginal zmind or php fuction
    *
    *
    * @since   1.0.0
    */
    function __construct(  $args = array() ) {

        add_filter( "sed_addon_settings", array($this,'addon_settings'));

        add_action("sed_footer" , array($this, 'print_settings_dependencies') , 10000 );
        add_action("sed_footer" , array($this, 'print_settings_template') , 10000 );
        add_filter( "sed_js_I18n", array($this,'js_I18n'));

        add_action( 'site_editor_ajax_sed_load_options', array($this,'sed_ajax_load_options' ) );//wp_ajax_sed_load_options

    }

    function sed_ajax_load_options(){
        //$this->set_group_params( $group , $params_title , $params = array() , $panels = array() , "module-settings" );
        //$this->add_settings( array() );
        //global $sed_apps;
        //$sed_apps->editor->manager->check_ajax_handler('sed_options_loader' , 'sed_app_options_load');
        do_action( "sed_ajax_load_options_" . $_POST['setting_id'] );

        ob_start();
        $settings = $this->params[ $_POST['setting_id'] ];
        if( in_array( $_POST['setting_id'] , array( "sed_page_options" , "sed_content_options" ) ) ){
            $class = "";
        }else{
            $class = "sed-app-settings-normal";
        }
        ?>
        <div id="dialog-level-box-settings-<?php echo $_POST['setting_id'];?>-container" data-title="<?php echo $settings['settings_title'];?>" class="dialog-level-box-settings-container hide <?php echo $class;?>" >
            <?php echo $settings['settings_output'];?>
        </div>
        <?php
        $output = ob_get_clean();

        die( wp_json_encode( array(
            'success' => true,
            'data'    => array(
                'output'        => $output ,
                'controls'      => isset( $this->controls[ $_POST['setting_id'] ] ) ? $this->controls[ $_POST['setting_id'] ] : array() ,
                'relations'     => isset( $this->settings_dependencies[ $_POST['setting_id'] ] ) ? $this->settings_dependencies[ $_POST['setting_id'] ] : array()
            ),
        ) ) );

    }

    function js_I18n( $I18n ){

        $I18n['custom_size']         =  __("Custom Size","site-editor");
        $I18n['organize_tab_title']         =  __("Edit Gallery","site-editor");
        $I18n['update_btn_title']         =  __("Update gallery","site-editor");
        $I18n['cancel_btn_title']         =  __("Cancel","site-editor");
        $I18n['images_gallery_update']         =  __("Update Images gallery","site-editor");
        $I18n['add_btn_title']         =  __("Add To Gallery","site-editor");

        return $I18n;
    }

    function addon_settings( $sed_addon_settings ){

        $sed_addon_settings["imageModule"] = array(
            "sizes"               => self::get_all_img_sizes_options() ,
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

    public static function get_all_img_sizes_options(){
        global $_wp_additional_image_sizes;

        $sizes = array();

		$possible_sizes = apply_filters( 'image_size_names_choose', array(
			'thumbnail' => __('Thumbnail'),
			'medium'    => __('Medium'),
			'large'     => __('Large')
		) );


        foreach( $possible_sizes as $_size => $label ) {

            if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {

                $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
                $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
                $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
                $sizes[ $_size ]['label'] =  $label;

            } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

                $sizes[ $_size ] = array(
                    'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
                    'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                    'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'] ,
                    'label'  => $label
                );

            }

        }

        $sizes['full'] = array( 'label'  => __('Full Size') );

        foreach( $_wp_additional_image_sizes AS $img_size => $options ){
            $sizes[ $img_size ] = array(
                'width'  => $options['width'],
                'height' => $options['height'],
                'crop'   => $options['crop'] ,
                'label'  => $img_size
            );
        }

        return $sizes;
    }

    public function print_settings_dependencies(){
		?>
		<script type="text/javascript">
            var _sedAppModulesSettingsRelations = <?php echo wp_json_encode( $this->settings_dependencies ); ?>;

		</script>
		<?php
    }

    public function print_settings_template(){
        ?>

        <div id="static-module-hover-box">
            <span class="fa icon icon-edit">edit</span>
        </div>

        <div id="sed-app-settings-panel" class="sed-dialog" title="<?php echo __("App Settings" , "site-editor");?>">

        </div>

        <div id="sed-dialog-settings" class="sed-dialog" title="">

        </div>
        <!--

        <?php
        do_action("print_sed-dialog-settings_tmpl");
        ?>

        -->

        <?php

         $custom_settings = $this->params;
          if(!empty( $custom_settings )){
            foreach($custom_settings AS $name => $settings){
              if(!empty($settings['settings_output'])){
        ?>
              <script type="text/html" data-dialog-title="<?php echo $settings['settings_title'];?>" id="sed-tmpl-dialog-settings-<?php echo $name;?>">
                <div id="dialog-level-box-settings-<?php echo $name;?>-container" data-title="<?php echo $settings['settings_title'];?>" class="dialog-level-box-settings-container" >
                    <?php echo $settings['settings_output'];?>
                    <?php do_action("print_".$name."_settings_tmpl");?>
                </div>
              </script>
        <?php
              }
            }
          }

          $panels = $this->panels;
        ?>

        <script>
            var _sedAppSettingsPanels = <?php if( !empty( $panels ) ) echo wp_json_encode( $panels ); else echo "{}"; ?>;
        </script>
        <?php
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
                'type' => 'panel_button',
                'label' => __('Custom Edit Style',"site-editor"),
                'desc' => '',
                'style' => 'blue' ,
                'class' => 'sed_style_editor_btn' ,
                'dialog_title' => __('Custom Edit Style',"site-editor") ,
                'dialog_content' => $dialog_content ,
                'priority'      => 0
            );
        }
    }*/

    public function add_settings( $settings = array() ){
        global $sed_apps;
        if( !empty( $settings ) && is_array( $settings ) ){
            foreach( $settings AS $id => $values ){
                /*if($this->sed_settings !== false && isset( $this->sed_settings[$id] )){
                    $values['value'] = $this->js_value( $id, $this->sed_settings[$id] );
                }
                $this->settings[ $id ] = $values;*/
                if( isset( $values['value'] ) ){
                    $values['default'] = $values['value'];
                    unset( $values['value'] );
                }

        		$sed_apps->editor->manager->add_setting( $id, $values );
            }
        }
    }

    public function add_controls( $controls = array() , $group = '' ){
        global $sed_apps;
        if(!empty($controls)){
            foreach($controls AS $id => $values ){
                if( !empty( $group ) ) {
                    $this->controls[$group][$id] = $values;
                }else{
                    $this->controls["without_group"][$id] = $values;
                }

                $sed_apps->editor->manager->add_control( $id, $values );
            }
        }
    }

    function set_group_dependencies( $group , $dependency , $id ){
        if( !isset( $this->settings_dependencies[$group] ) ){
            $this->settings_dependencies[$group] = array();
        }

        if( isset( $dependency['controls'] ) ){
            if( isset( $dependency['controls']['control'] ) ){
                $dependency['controls']['control'] = $group."_".$dependency['controls']['control'];
            }else{
                foreach( $dependency['controls'] AS $index => $control ){
                    if( isset( $control['control'] ) )
                        $dependency['controls'][$index]['control'] = $group."_".$control['control'];
                }
            }
        }
        $this->settings_dependencies[$group][$id] = $dependency;
    }

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
    public function set_group_params( $group , $params_title , $params = array() , $panels = array() , $base_category = "module-settings" ){

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
                if( isset( $param["dependency"] ) ){
                    $this->set_group_dependencies( $group , $param["dependency"] , $control_id );
                    unset( $param["dependency"] );
                }

                if( $key != "content" ){

                    $control = $this->create_control($group , $key , $param );
                    if( !empty( $control ) && $control )
                        $controls[$control_id] = $control;

                }
            }

        }

        if( isset( $this->panels[ $group ] ) && !empty( $this->panels[ $group ] ) ){
            foreach( $this->panels[ $group ] AS $key => $panel ){
                if( isset( $panel["dependency"] ) ){
                    $this->set_group_dependencies( $group , $panel["dependency"] , $panel['id'] );
                    unset( $this->panels[ $group ][$key]["dependency"] );
                }
            }
        }

        if( !empty( $controls ) ){
            $this->add_controls( $controls , $group );
        }

    }

    public function create_control( $name , $key , $param ){
        $args = array();
        if(isset($param["control_type"])){

            $settings_type = ( isset( $param['settings_type'] ) && !empty( $param['settings_type'] ) ) ?  $param['settings_type'] : 'sed_pb_modules';
            $category = ( isset( $param['control_category'] ) && !empty( $param['control_category'] ) ) ?  $param['control_category'] : 'module-settings';
            $is_style_setting = ( isset( $param['is_style_setting'] ) && is_bool( $param['is_style_setting'] ) ) ?  $param['is_style_setting'] : false;

            //edit risk
            $is_attr = isset( $param["is_attr"] ) ? $param["is_attr"]: false;
            $value = isset( $param["value"] ) ? $param["value"] :  "";


            $args = array(
                'settings'     => array(
                    'default'       => $settings_type
                ),
                'type'                =>  $param["control_type"],
                'category'            =>  $category,
                'sub_category'        =>  $name,           //shortcode name :: sed_image
                'default_value'       =>  is_array( $value ) ?  implode("," , $value): $this->sanitize_control_value( $value ),
                'is_style_setting'    =>  $is_style_setting ,
            );

            if( $settings_type == 'sed_pb_modules' ){
                $args['shortcode'] = $name;
                $args['attr_name'] = ( isset( $param['attr_name'] ) && !empty( $param['attr_name'] ) ) ? $param['attr_name'] : $key;
                $args['is_attr'] = $is_attr;
            }

            if( isset( $param['panel'] ) )
                $args['panel'] = $param['panel'];

            if(!empty($param["control_param"]))
                $args = array_merge( $args , $param["control_param"]);

        }

        return $args;

    }

    public function sanitize_control_value( $value ){

        if( $value === "true" )
            $value = true;
        else if( $value === "false" )
            $value = false;

        return $value;
    }

    /*public function add_post_control( $id, $values ){
        global $sed_apps;
        $sed_apps->editor->manager->add_post_control( $id, $values );
    }*/

    function control_type_filter( $param ){
        if(isset($param['control_type']) && !empty($param['control_type'])){
            return $param;
        }else{
            $control_type = '';
            switch ($param['type']) {
                case "select":
                case "text":
                case "textarea":
                case "radio":
                    $control_type = "sed_element";
                break;
                case "image":
                    $control_type = "image";
                break;
                case "spinner":
                    $control_type = "spinner";
                break;
                case "color":
                    $control_type = "color";
                break;
                case "icon":
                    $control_type = "icon";
                break;
                case "multi_icons" :
                    $control_type = "multi_icons";
                break;
                case "checkbox":
                    if( isset( $param['subtype'] ) && $param['subtype'] == "multi" ){
                        $control_type = "checkboxes";
                    }else{
                        $control_type = "sed_element";
                    }
                break;
                //default:
                    //$control_type = $param['type'];
            }

            if( !empty( $control_type ) )
                $param['control_type'] = $control_type;

            return $param;
        }
    }

    function params_type_process( $params ){

        $new_params = array();

        foreach( $params  As $key => $param ){

            if( isset( $param['type'] ) ){
                switch ( $param['type'] ) {
                    case 'image_size' :
                        unset( $param['type'] );
                        $param = $this->add_image_sizes( $param );
                        $new_params[$key] = $param;
                    break;
                    case "video" :
                    case "audio" :
                    case "file"  :
                        $param['control_type'] = $param['type'];
                        $param['type'] = "change_media";
                        $new_params[$key] = $param;
                    break;
                    case "animation" :
                        unset( $param['type'] );
                        $param = $this->add_animation( $param );
                        $new_params[$key] = $param;
                    break;
                    case "skin" :
                        unset( $param['type'] );
                        $param = $this->add_skin_control( $param );
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
                        $new_params[$key]['control_type'] = 'dropdown';
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
                    case "date" :
                    case "time" :
                    case "range" :
                        $new_params[$key] = $param;
                        $new_params[$key]['type'] = "text";
                        $new_params[$key]['subtype'] = $param['type'];
                    break;
                    case "multi-select" :
                        $new_params[$key] = $param;
                        $new_params[$key]['type'] = "select";
                        $new_params[$key]['subtype'] = "multiple";
                    break;
                    case "multi-icon" :
                        $new_params[$key] = $param;
                        $new_params[$key]['type'] = "multi_icons";
                    break;
                    case "multi-image" :
                        $new_params[$key] = $param;
                        $new_params[$key]['type'] = "multi_images";
                        $new_params[$key]['control_type'] = 'multi_images';
                    break;
                    case "multi-checkbox" :
                        $new_params[$key] = $param;
                        $new_params[$key]['type'] = "checkbox";
                        $new_params[$key]['subtype'] = "multi";

                        if( !isset( $new_params[$key]['control_param'] ) )
                            $new_params[$key]['control_param'] = array();

                        $new_params[$key]['control_param']["options_selector"] = ".sed-bp-checkbox-input";
                        
                    break;
                    /*
                    case "group_hover_effect" : ---- remove*/
                    default :
                    $new_params[$key] = $param;

                }
            }
        }

        foreach( $new_params AS $key => $param ){
            $param = $this->control_type_filter( $param );
            $new_params[$key] = $param;

        }

        //var_dump( $new_params );

        return $new_params;
    }

	public function add_panel( $id, $group , $args = array() ) {
	    if( is_array($args) ){
	        if( !isset( $this->panels[$group] ) ){
                $this->panels[$group] = array();
	        }

		    $this->panels[$group][ $id ] = array_merge( array(
                'id'            => $id  ,
                'title'         => ''  ,
                'capability'    => 'edit_theme_options' ,
                'type'          => 'fieldset' ,
                'description'   => '' ,
                'priority'      => 10
            ) , $args );
        }
	}

    function add_link_control( $args ){

        $panel_title = isset($args['label']) ?  $args['label'] : __('Link To',"site-editor") ;
        $description = isset($args['description']) ?  $args['description'] : '' ;
        $priority = isset($args['priority']) ?  $args['priority'] : 70 ;
        $value = isset($args['value']) ?  $args['value'] : "0 0 0 0";

        $panel = array(
            'label'         =>  $panel_title ,
            'title'         =>  $panel_title ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'inner_box' ,
            'desc'          => '' ,
            'parent_id'     => 'root',
            'description'   => $description ,
            'is_attr'       => true ,
            'priority'      => $priority ,
            'in_box'        => true
        );

        if( isset( $args['dependency'] ) ){
            $panel['dependency'] = $args['dependency'];
        }

        $this->add_panel( 'link_to_panel' , $this->group_name , $panel );


        $params = array();

        $params["link"] = array(
            'value'         => $value,
            'type'          => 'text',
            'placeholder'   => 'E.g www.siteeditor.org' ,
            'control_type'  =>  "sed_element",
            'label'         => __('Url : ', 'site-editor'),
            'desc'          => __('Add Link to any module elements that needs a link.', 'site-editor') ,
            'is_attr'       =>  true ,
            'panel'         => 'link_to_panel'
        );

        $params["link_target"] = array(
            'value'         => "_self",
            'type'          => 'radio',
            'control_type'  =>  "sed_element",
            'options'       =>  array(
                "_blank"        => __('Open in new window', 'site-editor')  ,
                "_self"         => __('Open in same window', 'site-editor')  ,
            ),
            'label'         => __('Link Target : ', 'site-editor'),
            'desc'          => __('Add Link target : open link in new window or same window', 'site-editor') ,
            'panel'         => 'link_to_panel' ,
            'is_attr'       =>  true ,
        );

        return $params;

    }

    function spacing_values_filter( $value ){
        if(trim($value) == "")
            return false;
        else
            return true;
    }

    function add_spacing_control( $args = array() ){

        $panel_title = isset($args['label']) ?  $args['label'] : __('Spacing',"site-editor") ;
        $description = isset($args['description']) ?  $args['description'] : '' ;
        $priority = isset($args['priority']) ?  $args['priority'] : 100 ;
        $value = isset($args['value']) ?  $args['value'] : "0 0 0 0";

        /**
         * Parse incoming $args into an array and merge it with $defaults
         */
        $panel_id = $this->group_name . "_spacing_settings_panel";
        $this->add_panel( $panel_id , $this->group_name , array(
            'title'         =>  $panel_title ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
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
  			'type' => 'spinner',
            'after_field'  => 'px',
            'value' => $padding_top,
  			'label' => __('Top', 'site-editor'),
  			'desc' => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
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
  			'type' => 'spinner',
            'after_field'  => 'px',
            'value' => $padding_left,
  			'label' => is_rtl() ? __('Right', 'site-editor') : __('Left', 'site-editor'),
  			'desc' => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
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
  			'type' => 'spinner',
            'after_field'  => 'px',
            'value' => $padding_right,
  			'label' => is_rtl() ? __('Left', 'site-editor') : __('Right', 'site-editor'),
  			'desc' => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
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
  			'type' => 'spinner',
            'after_field'  => 'px',
            'value' => $padding_bottom,
  			'label' => __('Bottom', 'site-editor'),
  			'desc' => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
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
  			'desc'          => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
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

    function add_align_control( $args = array() ){

        /**
         * Define the array of defaults
         */
        $defaults = array(
  			'type' => 'select',
            'value' => "" ,
  			'label' => __('Align', 'site-editor'),
  			'desc' => '<p><strong>Align:</strong> Module Align</p>',
            'options' =>array(
                'initial'   => __('Default', 'site-editor'),
                'left'      => is_rtl() ?  __('Right', 'site-editor') : __('Left', 'site-editor'),
                'center'    =>__('Center', 'site-editor'),
                'right'     => is_rtl() ? __('Left', 'site-editor') :  __('Right', 'site-editor'),
            ),
            'control_param'     =>  array(
                'selector' =>  'sed_current' ,
                'style_props'       =>  "text-align" ,
            ),
            'control_category'  => "style-editor" ,
            'settings_type'     =>  "text_align",
        );

        /**
         * Parse incoming $args into an array and merge it with $defaults
         */
        $param = wp_parse_args( $args, $defaults ) ;

        return $param;
    }

    function add_row_container_setting( $args ){

        /**
         * Define the array of defaults
         */
        $defaults = array(
            'type'          => 'row_settings_button',
            'label'         => __('Go To Row Settings', 'site-editor'),
            'desc'          => __('Row Settings', 'site-editor'),
            'style'         => 'blue',
            'class'         =>  '',
            //'panel'    => 'general_settings',
            //'in_box'   =>'false',
            'priority'      => 20
            /*'atts'  => array(
                'data-module-name' => $this->module
            ) */
        );

        /**
         * Parse incoming $args into an array and merge it with $defaults
         */
        $param = wp_parse_args( $args, $defaults ) ;

        return $param;
    }

    function add_length_control( $args ){

        /**
         * Define the array of defaults
         */
        $defaults = array(
  			'type' => 'select',
            'value' => "wide" ,
            'control_type'  =>  "sed_element",
  			'label' => __('Length', 'site-editor'),
  			'desc' => '<p><strong>Length:</strong> container Length</p>',
            'options' =>array(
                'wide'    => __('Wide', 'site-editor'),
                'boxed'   => __('Boxed', 'site-editor')
            ),
            'is_attr'   =>  true ,
            'priority'      => 15
        );

        /**
         * Parse incoming $args into an array and merge it with $defaults
         */
        $param = wp_parse_args( $args, $defaults ) ;
  		return $param;

    }

    function add_image_sizes( $args = array() ){

        /**
         * Define the array of defaults
         */
        $defaults = array(
            'value'         =>   '' ,
            'type'          =>   'select',
            'control_type'  =>  "sed_element",
            'label'         =>   '' ,
            'desc'          =>   '<p><strong>Stretch:</strong> Stretch the image to the size of the image frame.<br>	<strong>Fit:</strong> Fits images to the size of the image frame.</p>',
            'options'       =>   array() ,
            'is_attr'       =>  true ,
            'control_param'     =>  array(
                "is_image_size" => true
            ),
        );

        if( isset( $args['control_param'] ) ){
            $args['control_param'] = array_merge( $defaults['control_param'] , $args['control_param'] );
        }

        /**
         * Parse incoming $args into an array and merge it with $defaults
         */
        $param = wp_parse_args( $args, $defaults );

        return $param;
    }

    function add_animation( $args ){

        /**
         * Define the array of defaults
         */
        $defaults = array(
            'type' => 'panel_button',
            'control_type' => 'animations' ,
            'value' => "1000,1,1000,,0",
            'label' => __('Add Animation', 'site-editor'),
            //'desc' => __('Select One image Animation', 'site-editor'),
            'style' => 'blue' ,
            'class' => 'sed-animations-btn' ,
            'is_attr'   =>  true ,
            'dialog_title' => __('Animation Settings' , 'site-editor') ,
            'dialog_content' => '<div class="animation-dialog-inner"></div>',
            //'panel'    => 'general_settings',
            //'in_box'   =>'false'
            'priority'      => 19
        );

        /**
         * Parse incoming $args into an array and merge it with $defaults
         */
        $param = wp_parse_args( $args, $defaults );

        return $param;
    }

    function add_skin_setting($value = "default"){

        $dialog_content = '<div class="loading skin-loading"></div><div class="error error-load-skins"><span></span></div> <div class="skins-dialog-inner"></div>';

        return array(
            'type' => 'panel_button',
            'value' => $value,
            'control_type' => 'skins' ,
            'label' => __('Change Skin', 'site-editor'),
            //'desc' => __('Select One image skin', 'site-editor'),
            'style' => 'black',
            'class' =>  'sed-select-module-skins-btn',
            /*'dialog' => array(
                'class' => 'sed-dialog-skins' ,
                'title' => __('skins' , 'site-editor')
            ),*/
            'dialog_title' => __('skins' , 'site-editor') ,
            'dialog_content' => $dialog_content ,
            'is_attr'       =>  true ,
            'priority'      => 2
        );
    }

    function add_skin_control( $args = array() ){

        $dialog_content = '<div class="loading skin-loading"></div><div class="error error-load-skins"><span></span></div> <div class="skins-dialog-inner"></div>';

        /**
         * Define the array of defaults
         */
        $defaults = array(
            'type' => 'panel_button',
            'value' => "dafault",
            'control_type' => 'skins' ,
            'label' => __('Change Skin', 'site-editor'),
            //'desc' => __('Select One image skin', 'site-editor'),
            'style' => 'black',
            'class' =>  'sed-select-module-skins-btn',
            /*'dialog' => array(
                'class' => 'sed-dialog-skins' ,
                'title' => __('skins' , 'site-editor')
            ),*/
            'dialog_title' => __('skins' , 'site-editor') ,
            'dialog_content' => $dialog_content ,
            'is_attr'       =>  true ,
            'priority'      => 20
        );

        /**
         * Parse incoming $args into an array and merge it with $defaults
         */
        $param = wp_parse_args( $args, $defaults );

        return $param;
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

        if( !empty( $args['control_param'] ) ){
            $setting["control_param"] = $args['control_param'] ;
        }
        if( !empty( $args['panel_id'] ) ){
            $setting["panel"] = $args['panel_id'];
        }

        return $setting;
    }

    function add_image_setting( $args = array() ){
        $params = array();

        $panel_title = isset($args['label']) ?  $args['label'] : __('Change Panel',"site-editor") ;
        $description = isset($args['description']) ?  $args['description'] : '' ;
        $priority = isset($args['priority']) ?  $args['priority'] : 10 ;
        $value = isset($args['value']) ?  $args['value'] : "0 0 0 0";
        $panel_type = isset($args['panel_type']) ?  $args['panel_type'] : "inner_box";

        $this->add_panel( 'sed_select_image_panel' , $this->group_name , array(
            'label'         =>  $panel_title ,
            'title'         =>  $panel_title ,
            'capability'    => 'edit_theme_options' ,
            'type'          => $panel_type ,
            'desc'          => '' ,
            'parent_id'     => 'root',
            'description'   => $description ,
            'is_attr'       => true ,
            'priority'      => $priority ,
            'in_box'        => true
        ) );

        $params["image_source"] = array(
            'label'         => __('Image Source', 'site-editor'),
            'desc'          => __('Select image source.', 'site-editor'),
            'type'          => 'select',
            'options'       =>  array(
                "attachment"     => __('Media Library', 'site-editor')  ,
                "external"       => __('External Link', 'site-editor')  ,
            ),
            'control_type'  => 'sed_element',
            'value'         => 'attachment',
            'is_attr'       => true ,
            //'priority'      => 7 ,
            'panel'         => 'sed_select_image_panel'
        );

        $params["image_url"] = array(
            'label'         => __('External link', 'site-editor'),
            'desc'          => __('Enter an external link.', 'site-editor'),
            'type'          => 'text',
            'control_type'  => 'sed_element',
            'value'         => '',
            'is_attr'       => true ,
            //'priority'      => 8 ,
            'panel'         => 'sed_select_image_panel',
            'dependency' => array(
                'controls'  =>  array(
                    "control"  => "image_source" ,
                    "value"    => "external" ,
                )
            )
        );

        $params["attachment_id"] = array(
            'label'         => __('Select image', 'site-editor'),
            'desc'          => __('Select image from media library.', 'site-editor'),
            'type'          => 'image',
            'control_type'  => 'image',
            'value'         => 0,
            'is_attr'       => true ,
            //'priority'      => 9 ,
            'panel'         => 'sed_select_image_panel' ,
            "control_param"     => array(
                "rel_size_control"          => $this->group_name . "_default_image_size"
            ),
            'dependency' => array(
                'controls'  =>  array(
                    "control"  => "image_source" ,
                    "value"    => "attachment" ,
                )
            )
        );

        $params["default_image_size"] = array(
            'label'         => __('Select Image Size', 'site-editor'),
            'desc'          => __('Select a Image Size Or Select Custom size for enter size in px', 'site-editor'),
            'type'          => 'select',
            'control_type'  => 'sed_element',
            'value'         => 'thumbnail',
            'options'       =>   array() ,
            'is_attr'       =>  true ,
            'control_param'     =>  array(
                "is_image_size"     => true ,
                "has_custom_size"   => true
            ),
            //'priority'      => 8 ,
            'panel'         => 'sed_select_image_panel' ,
            'dependency' => array(
                'controls'  =>  array(
                    "relation"     =>  "and" ,
                    array(
                        "control"  => "attachment_id" ,
                        "values"    => array( "" , 0 ) ,
                        "type"     => "exclude"
                    ),
                    array(
                        "control"  => "image_source" ,
                        "value"    => "attachment" ,
                    )
                )
            )
        );

        $params["custom_image_size"] = array(
            'label'         => __('Custom Image Size', 'site-editor'),
            'desc'          => __('Enter custom size in pixels (Example: 100x300 (Width x Height)).', 'site-editor'),
            'type'          => 'text',
            'control_type'  => 'sed_element',
            'value'         => '',
            'is_attr'       => true ,
            //'priority'      => 8 ,
            'panel'         => 'sed_select_image_panel' ,
            'dependency' => array(
                'controls'  =>  array(
                    "relation"     =>  "and" ,
                    array(
                        "control"  => "default_image_size" ,
                        "value"    => "" ,
                    ),
                    array(
                        "control"  => "image_source" ,
                        "value"    => "attachment" ,
                    )
                )
            )
        );

        $params["external_image_size"] = array(
            'label'         => __('Custom Image Size', 'site-editor'),
            'desc'          => __('Enter custom size in pixels (Example: 100x300 (Width x Height)).', 'site-editor'),
            'type'          => 'text',
            'control_type'  => 'sed_element',
            'value'         => '',
            'is_attr'       => true ,
            //'priority'      => 8 ,
            'panel'         => 'sed_select_image_panel' ,
            'dependency' => array(
                'controls'  =>  array(
                    "control"  => "image_source" ,
                    "value"    => "external" ,
                )
            )
        );

        return $params ;
    }


}