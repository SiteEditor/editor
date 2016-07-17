<?php
class ModuleSettings
{
    public $output;
    public static $group_id;

    public static function get_sub_panel( $panels , $parent_id = "root" ) {
        $sub_panels = array();

        if( !empty( $panels ) ){
            foreach( $panels AS $id => $panel ){
                if( ($parent_id == "root" && !isset( $panel['parent_id'] ) ) || ( isset( $panel['parent_id'] ) && $panel['parent_id'] == $parent_id ) ){
                    array_push( $sub_panels , $id );
                }
            }
        }
        return $sub_panels;
    }

	public static function create_settings($params , $panels = array())
	{

        $root_settings = array();

        $panels_ids = wp_list_pluck( $panels , 'id');
        $count = 0;

        foreach( $params as $pkey => $param )
        {

            if( !isset($param['panel']) || !in_array( $param['panel'] , $panels_ids ) ){
                $root_settings[$pkey] = $param;
            }else{
                if( !isset( $panels[$param['panel']]['settings'] ) )
                    $panels[$param['panel']]['settings'] = array();

                $param['instance_number'] = $count;
                $panels[$param['panel']]['settings'][$pkey] = $param;
                $count ++;
            }

        }

        $sub_panels = self::get_sub_panel( $panels , 'root');

        $settings = self::get_settings( $panels , $root_settings , $sub_panels );

        $output = self::create_output( $panels , $settings);

        return $output;
	}

    public static function get_settings( $panels , $params = array() , $sub_panels  ){

        $penels_settings = array();
        $settings = array();
        $count = 0;

        if(!empty($sub_panels)){
            foreach( $sub_panels as $panel_id )
            {
                array_push( $settings , array(
                    'priority'          => $priority = isset($panels[$panel_id]['priority']) ? $panels[$panel_id]['priority'] : 10   ,
                    'type'              => 'panel' ,
                    'panel_id'          =>  $panel_id ,
                    'instance_number'   =>  $count
                ) );
                $count ++;
            }
        }

        if(!empty( $params )){
            foreach( $params as $pkey => $param )
            {
                array_push( $settings , array(
                    'priority'        => $priority = isset($param['priority']) ? $param['priority'] : 10   ,
                    'param'           => $param ,
                    'key'             => $pkey ,
                    'instance_number' => $count
                ) );
                $count ++;
            }
        }

        // sort by priority
        uasort($settings, array( 'ModuleSettings', '_cmp_priority' ) );

        return $settings;
    }


    public static function create_output( $panels , $settings ){

        $output = "";

        // filters and excutes params
        foreach( $settings as $setting )
        {

            if( !isset($setting['type']) || $setting['type'] != "panel" ){
                $param = $setting['param'];
                $pkey  = $setting['key'];
                $output .= self::get_output_settings( $param , $pkey , $panels );

            }else{
                $panel = $panels[$setting['panel_id']];
                $type_panel = $panel['type'];
                $func_panel = $type_panel."_panel";
                $content_panel = "";

                $sub_panels = self::get_sub_panel( $panels , $setting['panel_id']); ;

                if( isset( $panel['settings'] ) ){
                    $panel_settings = self::get_settings( $panels , $panel['settings'] , $sub_panels );

                    $content_panel = self::create_output( $panels , $panel_settings  );
                }

                if(!empty($type_panel) && method_exists('ModuleSettings' , $func_panel) ){
                    $output .= self::$func_panel( $panel , $content_panel );
                }
            }

        }

        return $output;
    }



    public static function get_output_settings( $param , $pkey , $panels = array() ){
        $output = '';
        if(!preg_match("/^fieldset/", $pkey)){ 

            if(!isset($param['type']))
                return '';

            $func_field = $param['type']."_field";
            if(!empty($param['type']) && method_exists('ModuleSettings' , $func_field) ){
                    if( isset($param['panel']) && !isset($param['in_box']) && isset($panels[$param['panel']]) ){
                        $panel = $panels[$param['panel']];
                        $type_panel = $panel['type'];
                        $in_box = ( $type_panel == "fieldset" ) ? false : true;
                    }else
                        $in_box = ( isset($param['in_box']) ) ? $param['in_box'] : true;

                $output .= self::$func_field( $pkey , $param , $in_box );
            }
        }else{
            $output .= '<fieldset class="row_setting_box" >';
            foreach( $param as $fspkey => $fsparam )
            {
                if(!isset($fsparam['type']))
                    continue;

                $fsfunc_field = $fsparam['type']."_field";
                if(!empty($fsparam['type']) && method_exists('ModuleSettings' , $fsfunc_field)  ){

                    $output .= self::$fsfunc_field( $fspkey , $fsparam , false );
                }
            }
            $output .= '</fieldset>' ;
        }

        return $output;
    }

    public static function _cmp_priority($a, $b) {
		if ( $a['priority'] === $b['priority'] ) {
			return $a['instance_number'] - $b['instance_number'];
		} else {
			return $a['priority'] - $b['priority'];
		}
    } // sort alphabetically by name usort($data, 'compare_lastname');

    public static function fieldset_panel( $panel , $content){

        $pkey = $panel['id'];
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;

        $output = "";

        extract( array_merge(
                array(
                    'label'  =>  '',
                    'is_accordion' => false,  //accordion-panel-settings
                    'atts'  =>  array()
                ),$panel )
        );

        $atts           = self::get_atts( $atts );
        $atts_string    = $atts["atts"];
        $class          = $atts["class"];

        $label = trim( $label );
        $label = ( !empty($label) ) ? $label : $panel['title'];

        $output .= '<fieldset id="'.$pkey.'_fieldset" class="row_setting_box '. $class .'" '.$atts_string.'>';
        $output .= '<legend id="'.$pkey.'_title">'.$label.'</legend>';

        if( $is_accordion === true )
            $output .= '<div class="accordion-panel-settings">' ;

        $output .= $content ;

        if( $is_accordion === true )
            $output .= '</div>' ;

        $output .= '</fieldset>' ;
        return $output;
    }

    public static function dialog_panel(  $panel , $content  ){

    }

    public static function accordion_item_panel(  $panel , $content  ){

        $pkey = $panel['id'];
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;
        // prefix the fields names and ids with sed_pb_
        $sed_field_id = 'sed_pb_' . $pkey;

        $output = "";

        extract( array_merge(
                array(
                    'label'  =>  '',
                    'desc'   =>  '',
                    'atts'   =>  array() ,
                    'has_help' => false ,
                    'custom_html' => ''
                ),$panel )
        );

        $atts           = self::get_atts( $atts );
        $atts_string    = $atts["atts"];
        $class          = $atts["class"];


        if( $has_help === true)
            $output .= '<span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="'.$desc.'"></span>';

        $custom_html = trim( $custom_html );

        if( empty($custom_html) )
            $output .= '<div class="sed-accordion-header go-panel-element '.$class.'" data-panel-id="'.$pkey.'" id="' . $sed_field_id . '" '.$atts_string.'>'.$label.'</div>';
        else
            $output .= $custom_html;

        $output .= '<div id="'.$pkey.'_ac_panel" class="sed-accordion-content" data-title="'.$label.'" class="sed-dialog content " >';
        $output .= $content ;
        $output .= '</div>' ;
        return $output;

    }

    public static function tab_panel(  ){

    }

    public static function inner_box_panel( $panel , $content ){

        $pkey = $panel['id'];
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;
        // prefix the fields names and ids with sed_pb_
        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'  =>  '',
                    'desc'   =>  '',
                    'atts'   =>  array() ,
                    'has_help' => false ,
                    'custom_html' => '' ,
                    'in_box'      => true ,
                    'is_accordion' => false  //accordion-panel-settings
                ),$panel )
        );

        $output = "";

        $atts           = self::get_atts( $atts );
        $atts_string    = $atts["atts"];
        $class          = $atts["class"];

        if( $has_help === true)
            $output .= '<span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="'.$desc.'"></span>';

        $custom_html = trim( $custom_html );

        $ac_class = "";
        if( $is_accordion === true )
            $ac_class = "go-accordion-panel";

        if( empty($custom_html) ){

            $box = '<button data-related-level-box="'.$pkey.'_level_box" type="button" class="sed-btn-blue go-panel-element '.$ac_class . ' ' . $class.'" data-panel-id="'.$panel['id'].'"  name="' . $sed_field_id . '"
                                    id="' . $sed_field_id . '" '.$atts_string.'>'.$label.'<span class="fa f-sed icon-chevron-right sed-arrow-right fa-lg"></span></button>';

            $in_box_class = ($in_box === true) ? "row_setting_box" : '';

            $output .= '<div class="row_settings">
              <div class="row_setting_inner '. $in_box_class .'">
                <div class="clearfix">'.
                    $box
                .'</div>
              </div>
            </div>';

        }else
            $output .= $custom_html;

        $output .= '<div id="'.$pkey.'_level_box" data-multi-level-box="true" data-title="'.$label.'" class="sed-dialog content " >';

        if( $is_accordion === true )
            $output .= '<div class="accordion-panel-settings">' ;   //accordion-panel-settings using in js in siteeditor/plugins/settings/plugin.min.js

        $output .= $content ;

        if( $is_accordion === true )
            $output .= '</div>' ;

        $output .= '</div>' ;
        return $output;

    }

    public static function post_button_field(  $pkey , $param , $in_box = true  ){

        // prefix the fields names and ids with sed_pb_
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;

        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'  =>  '',
                    'desc'   =>  '',
                    'style'  =>  'default',
                    'class'  =>  '' ,
                    'atts'   =>  array()
                ),$param )
        );

        switch ($style) {
          case "black":
            $class_style = "sed-btn-black";
          break;
          case "blue":
            $class_style = "sed-btn-blue";
          break;
          default:
            $class_style = "sed-btn-default";
        }

        $atts_string = "";
        if(is_array($atts)){
            foreach($atts AS $nameAttr => $valueAttr){
                $atts_string .= $nameAttr.'="'.$valueAttr.'" ';
            }
        }elseif(is_string($atts)){
            $atts_string = $atts;
        }

        $class = (!empty($class)) ? $class_style . " " . $class : $class_style;

        if(isset( $relation ))
            $relation = self::related_fields_values( $relation );
        else
            $relation = '';

        return self::row_box( '<span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="'.$desc.'"></span>
                                <button data-related-level-box="dialog-page-box-posts-edit-settings" class="'.$class.' sed_post_edit_button"  name="' . $sed_field_id . '"
                                id="' . $sed_field_id . '" '.$atts_string.'>'.$label.'<span class="fa f-sed icon-chevron-right sed-arrow-right fa-lg"></span></button>'
                                , $in_box , $pkey , $relation );
    }

    public static function style_editor_button_field(  $pkey , $param , $in_box = false  ){

        // prefix the fields names and ids with sed_pb_
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;

        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'  =>  '',
                    'icon'   =>  '',
                    'style'  =>  'default',
                    'class'  =>  '' ,
                    'atts'   =>  array()
                ),$param )
        );

        $atts_string = "";
        if(is_array($atts)){
            foreach($atts AS $nameAttr => $valueAttr){
                $atts_string .= $nameAttr.'="'.$valueAttr.'" ';
            }
        }elseif(is_string($atts)){
            $atts_string = $atts;
        }

        if(isset( $relation ))
            $relation = self::related_fields_values( $relation );
        else
            $relation = '';
               //self::row_box(
        return '<button data-related-level-box="modules_styles_settings_'.self::$group_id.'_level_box" type="button" class="sed-btn-half sed-btn-default go-panel-element '.$class.'" data-panel-id="'.$pkey.'"  name="' . $sed_field_id . '"
                                    id="' . $sed_field_id . '" '.$atts_string.'><span class="'.$icon.'"></span><span class="sed-btn-label">'.$label.'</span><span class="fa f-sed icon-chevron-right sed-arrow-right fa-lg"></span> </button>'; //, $in_box , $pkey , $relation )
    }

    public static function row_settings_button_field(  $pkey , $param , $in_box = true  ){

        // prefix the fields names and ids with sed_pb_
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;

        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'  =>  '',
                    'desc'   =>  '',
                    'style'  =>  'default',
                    'class'  =>  '' ,
                    'atts'   =>  array()
                ),$param )
        );

        switch ($style) {
          case "black":
            $class_style = "sed-btn-black";
          break;
          case "blue":
            $class_style = "sed-btn-blue";
          break;
          default:
            $class_style = "sed-btn-default";
        }

        $atts_string = "";
        if(is_array($atts)){
            foreach($atts AS $nameAttr => $valueAttr){
                $atts_string .= $nameAttr.'="'.$valueAttr.'" ';
            }
        }elseif(is_string($atts)){
            $atts_string = $atts;
        }

        $class = (!empty($class)) ? $class_style . " " . $class : $class_style;

        if(isset( $relation ))
            $relation = self::related_fields_values( $relation );
        else
            $relation = '';

        return self::row_box( '<span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="'.$desc.'"></span>
                                <button class="'.$class.' go-row-container-settings"  name="' . $sed_field_id . '"
                                id="' . $sed_field_id . '" '.$atts_string.'>'.$label.'<span class="fa f-sed icon-chevron-right sed-arrow-right fa-lg"></span></button>'
                                , $in_box , $pkey , $relation );
    }

    public static function widget_button_field(  $pkey , $param , $in_box = true  ){

        // prefix the fields names and ids with sed_pb_
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;

        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'  =>  '',
                    'desc'   =>  '',
                    'style'  =>  'default',
                    'class'  =>  '' ,
                    'atts'   =>  array()
                ),$param )
        );

        switch ($style) {
          case "black":
            $class_style = "sed-btn-black";
          break;
          case "blue":
            $class_style = "sed-btn-blue";
          break;
          default:
            $class_style = "sed-btn-default";
        }

        $atts_string = "";
        if(is_array($atts)){
            foreach($atts AS $nameAttr => $valueAttr){
                $atts_string .= $nameAttr.'="'.$valueAttr.'" ';
            }
        }elseif(is_string($atts)){
            $atts_string = $atts;
        }

        $class = (!empty($class)) ? $class_style . " " . $class : $class_style;

        if(isset( $relation ))
            $relation = self::related_fields_values( $relation );
        else
            $relation = '';

        return self::row_box( '<span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="'.$desc.'"></span>
                                <button data-related-level-box="dialog-page-box-widgets-settings" class="'.$class.' sed_widget_button"  name="' . $sed_field_id . '"
                                id="' . $sed_field_id . '" '.$atts_string.'>'.$label.'<span class="fa f-sed icon-chevron-right sed-arrow-right fa-lg"></span></button>'
                                , $in_box , $pkey , $relation );
    }


    public static function panel_button_field(  $pkey , $param , $in_box = true  ){

        // prefix the fields names and ids with sed_pb_
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;


        $sed_field_id = 'sed_pb_' . $pkey;
        $level_box_id = 'dialog_page_box_' . $pkey;

        extract( array_merge(
                array(
                    'label'  =>  '',
                    'desc'   =>  '',
                    'style'  =>  'default',
                    'class'  =>  '' ,
                    'atts'   =>  array() ,
                    'dialog_title' => '' ,
                    'dialog_content' => '' ,
                    'is_accordion' => false  //accordion-panel-settings
                ),$param )
        );

        switch ($style) {
          case "black":
            $class_style = "sed-btn-black";
          break;
          case "blue":
            $class_style = "sed-btn-blue";
          break;
          default:
            $class_style = "sed-btn-default";
        }

        $atts_string = "";
        if(is_array($atts)){
            foreach($atts AS $nameAttr => $valueAttr){
                $atts_string .= $nameAttr.'="'.$valueAttr.'" ';
            }
        }elseif(is_string($atts)){
            $atts_string = $atts;
        }

        $class = (!empty($class)) ? $class_style . " " . $class : $class_style;

        if(isset( $relation ))
            $relation = self::related_fields_values( $relation );
        else
            $relation = '';


        $content = "";

        $ac_class = "";

        if( $is_accordion === true ){
            $content .= '<div class="accordion-panel-settings">' ;
            $class .= " go-accordion-panel";
        }

        $content .= $dialog_content ;

        if( $is_accordion === true )
            $content .= '</div>' ;

        return self::row_box( '<span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="'.$desc.'"></span>
                                <button data-related-level-box="'.$level_box_id.'" class="'.$class.' "  name="' . $sed_field_id . '"
                                id="' . $sed_field_id . '" '.$atts_string.'>'.$label.'<span class="fa f-sed icon-chevron-right sed-arrow-right fa-lg"></span></button>
                                <div id="'.$level_box_id.'" class=""  data-title="'.$dialog_title.'" data-multi-level-box="true">'.$content. '</div>'
                                , $in_box , $pkey , $relation );
    }

    public static function row_box( $box , $in_box = false , $id = '' , $relation = '' ){
        $in_box_class = ($in_box === true) ? "row_setting_box" : '';
        $box_id = (!empty($id)) ? "id='sed-app-control-" . $id . "'" : '';

        $related = (isset($relation['control'])) ? $relation['control'] : '';

        $output = '<div class="row_settings">
          <div class="row_setting_inner '. $in_box_class .'">
            <div '. $box_id .' '. $related .' class="clearfix">'.
                $box
            .'</div>
          </div>
        </div>';
        return $output ;
    }

    public static function related_fields_values( $relation ){

        $new_relations = array(
            'control'  =>  '',
            'values'   =>  array()
        );

        if(isset( $relation['control'] )){
            $control = $relation['control'];
            if(isset( $control['control'] ) && isset( $control['value'] )){
                $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $control['control'] : $control['control'];
                $sed_field_id = 'sed-app-control-' . $pkey;
                $new_relations['control']  = 'data-related-control="'.$sed_field_id.'" ';
                $new_relations['control'] .= 'data-related-value="'.$control['value'].'" ';
            }
        }

        if(isset( $relation['values'] )){
            $values = $relation['values'];

            foreach($values AS $key => $value ){

                if(isset( $value['control'] ) && isset( $value['value'] )){
                    $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $value['control'] : $value['control'];
                    $sed_field_id = 'sed-app-control-' . $pkey;
                    $related_value = 'data-related-control="'.$sed_field_id.'" ';
                    $related_value .= 'data-related-value="'.$value['value'].'" ';

                    $new_relations['values'][$key] = $related_value;
                }

            }


        }

        return $new_relations;
    }

    public static function info_field( $pkey , $param , $in_box = true ){

        // prefix the fields names and ids with sed_pb_
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;

        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'  =>  '',
                    'value'  =>  '',
                    'desc'   =>  '',
                    'placeholder' => '',
                    'subtype' => 'text'  //email | url | search | password | tel | text
                ),$param )
        );

        if(!empty($placeholder))
            $att = 'placeholder="'.$placeholder.'"';
        else
            $att = "";

        if(isset( $relation ))
            $relation = self::related_fields_values( $relation );
        else
            $relation = '';

        return self::row_box( '<label>'.$label.'</label>
                                <span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="'.$desc.'"></span>
                                <input type="'.$subtype.'"  class="sed-module-element-control sed-element-control sed-bp-form-text sed-bp-input" name="' . $sed_field_id . '"
                                id="' . $sed_field_id . '" value="' . $value . '" '. $att .' />' , $in_box , $pkey , $relation );
    }

    public static function legend_field( $pkey , $param , $in_box = false ){

        // prefix the fields names and ids with sed_pb_
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;

        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'  =>  ''
                ),$param )
        );

        return '<legend id="'.$sed_field_id.'">'.$label.'</legend>' ;
    }

    public static function text_field( $pkey , $param , $in_box = true ){

        // prefix the fields names and ids with sed_pb_
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;

        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'  =>  '',
                    'value'  =>  '',
                    'desc'   =>  '',
                    'placeholder' => '',
                    'atts'        => '',
                    'subtype' => 'text'  //email | url | search | password | tel | text
                ),$param )
        );

        $atts           = self::get_atts( $atts );
        $atts_string    = $atts["atts"];
        $class          = $atts["class"];

        if(!empty($placeholder))
            $atts_string .= ' placeholder="'.$placeholder.'"';


        if(isset( $relation ))
            $relation = self::related_fields_values( $relation );
        else
            $relation = '';

        return self::row_box( '<label>'.$label.'</label>
                                <span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="'.$desc.'"></span>
                                <input type="'.$subtype.'"  class="sed-module-element-control sed-element-control sed-bp-form-text sed-bp-input ' . $class . '" name="' . $sed_field_id . '"
                                id="' . $sed_field_id . '" value="' . $value . '" '. $atts_string .' />' , $in_box , $pkey , $relation );

    }

    public static function textarea_field( $pkey , $param , $in_box = true ){

        // prefix the fields names and ids with sed_pb_
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;
        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'  =>  '',
                    'value'  =>  '',
                    'desc'   =>  ''
                ),$param )
        );

        if(isset( $relation ))
            $relation = self::related_fields_values( $relation );
        else
            $relation = '';

        return self::row_box( '<label>'.$label.'</label>
                                <span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="'.$desc.'"></span>
                                <textarea rows="5" cols="30" name="' . $sed_field_id . '"
                                id="' . $sed_field_id . '" class="sed-module-element-control sed-element-control sed-bp-form-textarea sed-bp-input">' . $value . '</textarea>', $in_box , $pkey , $relation  );
    }

    public static function autocomplete_field( $pkey , $param ){

    }

    public static function select_field( $pkey , $param , $in_box = true ){
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;
        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'  =>  '',
                    'value'  =>  '',
                    'desc'   =>  '',
                    'options' => array(),
                    'subtype' => 'single' ,
                    'atts'    => array() ,
                    'optgroup'=> false ,
                    'groups' => array()

                ),$param )
        );

        $atts           = self::get_atts( $atts );
        $atts_string    = $atts["atts"];
        $class          = $atts["class"];

        if(!empty($subtype) && $subtype == "multiple"){
            $class .= " multiple-select";
            $atts_string .= ' multiple="multiple"';
        }else{
            $class .= " select";
        }

        if(isset( $relation ))
            $relation = self::related_fields_values( $relation );
        else
            $relation = '';

        $output  = '<div class="sed-bp-form-select-field-container">';
        $output .= '<label>'.$label.'</label>';
        $output .= '<span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="'.$desc.'"></span> ';

        $output .= '<select  name="' . $sed_field_id . '" id="' . $sed_field_id . '" class="sed-module-element-control sed-element-control '.$class.' sed-bp-input" '.$atts_string.'>';

        if( $optgroup === false ){

            foreach( $options as $val => $option )
            {
            	$selected = ($value == $val) ? 'selected="selected"' : '';
                $related = (isset($relation['values'][$val])) ? $relation['values'][$val] : '';
            	$output .= '<option value="' . $val . '"' . $selected .' '. $related .'>' . $option . '</option>';
            }

        }else{

            foreach( $options as $optgroup => $group_options )
            {
                $output .= '<optgroup label="'.$groups[$optgroup].'">';
                foreach( $group_options as $val => $option )
                {
                	$selected = ($value == $val) ? 'selected="selected"' : '';
                    $related = (isset($relation['values'][$val])) ? $relation['values'][$val] : '';
                	$output .= '<option value="' . $val . '"' . $selected .' '. $related .'>' . $option . '</option>';
                }
                $output .= '</optgroup>';
            }

        }

        $output .= '</select></div>';


        return self::row_box( $output , $in_box , $pkey , $relation );
    }

    public static function get_atts( $atts ){
        $atts_string = "";
        $class = "";
        if(is_array($atts)){
            foreach($atts AS $name_attr => $value_attr){
               if($name_attr == "class"){
                    $class = $value_attr;
               }else{
                    $atts_string .= $name_attr.'="'.$value_attr.'" ';
               }
            }
        }

        return array(
            "atts"   =>  $atts_string  ,
            "class"  =>  $class
        );
    }

    public static function checkbox_field( $pkey , $param , $in_box = true ){
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;
        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'   =>  '',
                    'value'   =>  '',
                    'desc'    =>  '',
                    'subtype' =>  'single',
                    'options' => array() ,
                    'atts'    => array()

                ),$param )
        );

        $atts           = self::get_atts( $atts );
        $atts_string    = $atts["atts"];
        $class          = $atts["class"];


        if(isset( $relation ))
            $relation = self::related_fields_values( $relation );
        else
            $relation = '';

        $output = '<span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="'.$desc.'"></span>';
        if($subtype == "single"){
                $output .= '<label for="' . $sed_field_id .'" class="sed-bp-form-checkbox">';
            	$checked = ( "true" == $value ) ? 'checked="checked"' : '';
                $output .= '<input  type="checkbox" class="sed-module-element-control sed-element-control sed-bp-input sed-bp-checkbox-input '. $class .'" value="true" name="' . $sed_field_id . '" id="' . $sed_field_id .'" ' . $checked . ' ' . $atts_string . ' />';
                $output .= $label . '</label>';
        }else{
            $i = 1;
            $output .= '<div for="' . $sed_field_id . $i . '" class="sed-bp-form-checkboxes sed-checkboxes">';
            $output .= '<h5 class="sed-checkboxes-title">'.$label.' : </h5>';
            $values = explode( "," , $value);
            $values = array_map( 'trim' , $values );
            foreach( $options as $val => $option )
            {
                $output .= '<div><label for="' . $sed_field_id . $i . '" class="sed-bp-form-checkbox">';
            	$checked = ( is_array( $values ) && in_array( $val , $values) ) ? 'checked="checked"' : '';
                $output .= '<input type="checkbox" class="sed-module-element-control sed-element-control sed-bp-input sed-bp-checkbox-input '. $class .'" value="'.$val.'" name="' . $sed_field_id . '[]" id="' . $sed_field_id . $i . '" ' . $checked . ' ' . $atts_string . ' />';
                $output .= $option . '</label></div>';
                $i++;
            }
            $output .= '</div>';
        }

        return self::row_box( $output , $in_box , $pkey , $relation );
    }

    public static function spinner_field( $pkey , $param , $in_box = true ){

        // prefix the fields names and ids with sed_pb_
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;
        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'  =>  '',
                    'value'  =>  '',
                    'desc'   =>  '' ,
                    'atts'   => array(),
                    'after_field' => ''

                ),$param )
        );
                          //var_dump( $value );
        $atts           = self::get_atts( $atts );
        $atts_string    = $atts["atts"];
        $class          = $atts["class"];

        if(isset( $relation ))
            $relation = self::related_fields_values( $relation );
        else
            $relation = '';

        return self::row_box( '<span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="'.$desc.'"></span>
                                <label for="' . $sed_field_id .'" >'.$label.'</label>
                                <span class="after_field">'.$after_field.'</span><input  type="text" class="sed-module-element-control sed-element-control sed-spinner spinner sed-bp-spinner sed-bp-input ' . $class . '" name="' . $sed_field_id . '" id="' . $sed_field_id . '" value="' . $value . '" ' . $atts_string . ' /> '
                                , $in_box , $pkey , $relation );
    }

    public static function image_field( $pkey , $param , $in_box = true ){

        // prefix the fields names and ids with sed_pb_
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;
        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'      => __("Change image" ,"site-editor"),
                    'value'      => SED_EDITOR_FOLDER_URL.'images/no-image.jpg',
                    'desc'       => '' ,
                    'remove_btn' => false
                ),$param )
        );

        $value = (!empty($value)) ? $value : SED_EDITOR_FOLDER_URL.'images/no-image.jpg';

        $output  = '<div class="setting-img"><div class="change-img-setting">';
        $output .= '<div class="change-img-container"><img class="change_img" src="'.$value.'"/></div></div><span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="'.$desc.'"></span>';
        $output .= '<div class="change-img-setting">';

        $output .= '<button class="change_image sed-change-media-button sed-btn-blue" data-media-type="image" data-selcted-type="single">'. $label.'</button>';

        if($remove_btn === true)
            $output .= '<a class="remove-img-btn" href="#"><span class="fa f-sed fa-lg icon-delete"></span></a>';

        $output .= '</div><div class="clr"></div></div>';

        if(isset( $relation ))
            $relation = self::related_fields_values( $relation );
        else
            $relation = '';

        return self::row_box( $output , $in_box , $pkey , $relation );
    }

    public static function icon_field( $pkey , $param , $in_box = true ){

        // prefix the fields names and ids with sed_pb_
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;
        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'      =>  __("Select Icon" ,"site-editor"),
                    'value'      =>  '',
                    'remove_btn' => false
                ),$param )
        );

        $output  = '<div class="setting-icon">';
        $output .= '<div class="change-icon-container"><span sed-icon="'.$value.'" class="icon-demo '.$value.'"></span></div>';
        $output .= '<div class="change-icon-btns">';

        $output .= '<button class="select-icon-btn change_icon sed-btn-blue" >'. $label.'</button>';

        if($remove_btn === true)
            $output .= '<button class="remove-icon-btn sed-btn-red">'. __("Remove Icon" , "site-editor") .'</button>';

        $output .= '</div><div class="clr"></div></div>';

        return self::row_box( $output , $in_box , $pkey );
    }

    public static function multi_icons_field( $pkey , $param , $in_box = true ){

        // prefix the fields names and ids with sed_pb_
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;
        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'      =>  __("Select Icons" ,"site-editor"),
                    'value'      =>  ''
                ),$param )
        );

        $output  = '<div class="setting-icon">';
        $output .= '<div class="icons-organize-box"><ul class="icons-sortable"></ul></div>';
        $output .= '<div class="select-icon-btns">';

        $output .= '<button class="select-icon-btn change_icon sed-btn-blue" >'. $label.'</button>';

        $output .= '</div><div class="clr"></div></div>';

        return self::row_box( $output , $in_box , $pkey );
    }

    public static function multi_images_field( $pkey , $param , $in_box = true ){

        // prefix the fields names and ids with sed_pb_
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;
        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'      =>  __("Select Images" ,"site-editor"),
                    'value'      =>  ''
                ),$param )
        );

        $output  = '<div class="setting-image">';
        $output .= '<div class="images-organize-box"><ul class="images-sortable"></ul></div>';
        $output .= '<div class="select-img-btns">';

        $output .= '<button class="select-img-btn change_image_btn sed-btn-blue" >'. $label.'</button>';

        $output .= '</div><div class="clr"></div></div>';

        return self::row_box( $output , $in_box , $pkey );
    }

    public static function change_media_field( $pkey , $param , $in_box = true ){
          // prefix the fields names and ids with sed_pb_
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;
        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'  =>  __("Change media" ,"site-editor"),
                    'value'  =>  '',
                    'desc'   =>  '',
                    'media_type' =>  'all',
                    'selcted_type' =>  'single',
                ),$param )
        );

        //$atts           = self::get_atts( $atts );
        //$atts_string    = $atts["atts"];
        //$class          = $atts["class"];

        $output  = '<div class=""><label>'. __("Url" ,"site-editor").'</label><span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="'.$desc.'"></span>';

        $output .= '<input type="text"  class="sed-bp-form-text sed-bp-input media-url-field" name="' . $sed_field_id . '" id="' . $sed_field_id . '" value="" disabled="disabled" /><a class="remove-media-src-btn" href="#"><span class="fa f-sed fa-lg icon-delete"></span></a></div>';

        $output .= '<button class="change_media sed-change-media-button sed-btn-blue" data-media-type="'.$media_type.'" data-selcted-type="'.$selcted_type.'">'. $label.'</button>';


        if(isset( $relation ))
            $relation = self::related_fields_values( $relation );
        else
            $relation = '';


        return self::row_box( $output , $in_box , $pkey , $relation );
    }


    public static function datepicker_field( $pkey , $param ){
        // prefix the fields names and ids with sed_pb_
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;

        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'  =>  '',
                    'value'  =>  '',
                    'desc'   =>  '',
                    'atts'   =>  array() ,

                ),$param )
        );

        $atts           = self::get_atts( $atts );
        $atts_string    = $atts["atts"];
        $class          = $atts["class"];


        if(isset( $relation ))
            $relation = self::related_fields_values( $relation );
        else
            $relation = '';

        return self::row_box( '<label>'.$label.'</label>
                                <span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="'.$desc.'"></span>
                                <input type="date"  class="sed-module-element-control sed-element-control sed-bp-form-datepicker sed-bp-input ' . $class . '" name="' . $sed_field_id . '"
                                id="' . $sed_field_id . '" value="' . $value . '" '. $atts_string .' />' , $in_box , $pkey , $relation );

    }


    public static function date_range_field( $pkey , $param ){

    }


    public static function radio_field( $pkey , $param , $in_box = true ){
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;
        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'label'  =>  '',
                    'value'  =>  '',
                    'desc'   =>  '',
                    'options' => array()

                ),$param )
        );


        if(isset( $relation ))
            $relation = self::related_fields_values( $relation );
        else
            $relation = '';

        $output = '<label class="">'.$label.'</label>';
        $output .= '<span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="'.$desc.'"></span>';
        $output .= '<div class="sed-bp-form-radio">';
        $i = 1;

        foreach( $options as $val => $option )
        {
            $related = (isset($relation['values'][$val])) ? $relation['values'][$val] : '';
        	$checked = ( $val == $value ) ? 'checked="checked"' : '';
            $output .= '<div class="sed-bp-form-radio-item"><label for="' . $sed_field_id . $i . '">';
            $output .= '<input  type="radio" class="sed-module-element-control sed-element-control sed-bp-input sed-bp-radio-input" value="'.$val.'" name="' . $sed_field_id . '" id="' . $sed_field_id . $i . '" '. $related .' ' . $checked . ' />' . $option;
            $output .= '</label></div>';
            $i++;
        }
        $output .= '</div>';

        return self::row_box( $output , $in_box , $pkey , $relation );
    }

    public static function uploader_field( $pkey , $param ){

    }

    public static function button_field( $pkey , $param , $in_box = true ){

        // prefix the fields names and ids with sed_pb_
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;
        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'value'  => '',
                    'label'  =>  '',
                    'desc'   =>  '',
                    'style'  =>  'default',
                    'class'  =>  '' ,
                    'dialog' =>  array(),
                    'atts'   =>  array()
                ),$param )
        );

        $dialog_class = (isset($dialog['class'])) ? $dialog['class']: "";
        $dialog_attrs = "";
        if(!empty($dialog) && is_array($dialog)){
            foreach($dialog AS $attr => $value){
                $dialog_attrs .= $attr . "='" . $value . "' ";
            }
        }

        switch ($style) {
          case "black":
            $class_style = "sed-btn-black";
          break;
          case "blue":
            $class_style = "sed-btn-blue";
          break;
          default:
            $class_style = "sed-btn-default";
        }

        $atts_string = "";
        if(is_array($atts)){
            foreach($atts AS $nameAttr => $valueAttr){
                $atts_string .= $nameAttr.'="'.$valueAttr.'" ';
            }
        }elseif(is_string($atts)){
            $atts_string = $atts;
        }

        $class = (!empty($class)) ? $class_style . " " . $class : $class_style;

        if(isset( $relation ))
            $relation = self::related_fields_values( $relation );
        else
            $relation = '';

        return self::row_box( '<span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="'.$desc.'"></span>
                                <button type="button" class="'.$class.'"  name="' . $sed_field_id . '"
                                id="' . $sed_field_id . '" '.$atts_string.'>'.$label.'<span class="fa f-sed icon-chevron-right sed-arrow-right fa-lg"></span></button><div id="'.$sed_field_id.'_dialog" class="sed-dialog content '.$dialog_class.'" '.$dialog_attrs.'></div>'
                                , $in_box , $pkey , $relation );
    }



    public static function custom_field( $pkey , $param ){
        extract( array_merge(
                array(
                    'html'  =>  '',
                    'in_box'   =>  false
                ),$param )
        );

        return self::row_box( $html , $in_box );
    }

    public static function slider_field( $pkey , $param ){

    }
    public static function color_field( $pkey , $param  , $in_box = true){
        // prefix the fields names and ids with sed_pb_
        $pkey = (!empty(self::$group_id)) ? self::$group_id . "_" . $pkey : $pkey;
        $sed_field_id = 'sed_pb_' . $pkey;

        extract( array_merge(
                array(
                    'value'  => '',
                    'label'  =>  '',
                    'desc'   =>  ''
                ),$param )
        );


        if(isset( $relation ))
            $relation = self::related_fields_values( $relation );
        else
            $relation = '';

        $output = '<span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="'.$desc.'"></span>';
        $output .= '<div class="colorpicker " ><label>'.$label.'</label> ';
        $output .= '<span class="colorselector"><input type="text" class="input-colorpicker sed-colorpicker" id="' . $sed_field_id . '" name="' . $sed_field_id . '" value='.$value.'>&nbsp;&nbsp;</span> ';
        $output .= '</div>';
        return self::row_box( $output , $in_box , $pkey , $relation );

    }

}