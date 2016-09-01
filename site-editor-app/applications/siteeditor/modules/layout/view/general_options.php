<div class="layout-general-settings">
    <a href="javascript:void(0)" class="btn btn-default3"  title="<?php echo __("General Settings","site-editor");  ?>" id="page_general_settings" role="button" >
        <span class="fa f-sed icon-settings fa-2x "></span>
        <span class="el_txt"><?php echo __("General Settings","site-editor");  ?></span>
    </a>
    <div id="sed-dialog-general-options"  class="sed-dialog"  title="<?php echo __("General Settings" ,"site-editor");  ?>">
        <div id="dialog-level-box-settings-general-container" class="dialog-level-box-settings-container content" data-title="<?php echo __('General Settings',"site-editor");?>">
        <?php


        $panels = array();

        $styles_settings = array( 'background','gradient' ,'padding' ); //,'margin'

        $general_style_controls = new ModuleStyleControls( "general_style_editor" );

        if( !empty($styles_settings) ){
            foreach( $styles_settings AS $control ){
                $general_style_controls->$control();
            }
        }

        $general_controls = array();

        if( !empty( $general_style_controls->controls ) ){
            foreach(  $general_style_controls->controls AS $styles_setting => $controls ){

                $panel_id = 'general_'.$styles_setting.'style_editor_panel';

                $panels[$panel_id] = array(
                    'title'         =>  $general_style_controls->labeles[ $styles_setting ]."&nbsp;". __("Settings","site-editor")  ,
                    'label'         =>  $general_style_controls->labeles[ $styles_setting ]."&nbsp;". __("Settings","site-editor") ,
                    'capability'    => 'edit_theme_options' ,
                    'type'          => 'inner_box' ,
                    'description'   => '' ,
                    'parent_id'     => 'root' ,
                    'priority'      => 9 ,
                    'id'            => $panel_id  ,
                    'atts'      =>  array(
                        //'class'             => "design_ac_header" ,
                        'data-selector'     => "#main"
                    )
                );

                foreach(  $controls AS $id => $control ){
                    $controls[$id]['panel'] = $panel_id;
                }

                $general_controls = array_merge( $general_controls , $controls);
            }
        }


        $controls_settings = array();
        if( !empty( $general_controls ) ){
            foreach( $general_controls As $id => $control ){

                if(isset($control["control_type"])){
                    $value = $control['value'];

                    if( $value === "true" )
                        $value = true;
                    else if( $value === "false" )
                        $value = false;

                    $args = array(
                        'settings'     => array(
                            'default'       => $control["settings_type"]
                        ),
                        'type'                =>  $control["control_type"],
                        'category'            =>  'style-editor',
                        'sub_category'        =>  'general_settings',
                        'default_value'       =>  $value,
                        'is_style_setting'    =>  true ,
                        'panel'               =>  $control["panel"] ,
                    );

                    if(!empty($control["control_param"]))
                        $args = array_merge( $args , $control["control_param"]);

                    if(!empty($control["style_props"]))
                        $args['style_props'] = $control["style_props"];

                        $controls_settings[$id] = $args;

                }

            }
        }


        if( !empty( $controls ) ){
            ModuleSettings::$group_id = "";
            $style_editor_settings = ModuleSettings::create_settings($general_controls, $panels);

            echo $style_editor_settings;

            ModuleSettings::$group_id = "";

            sed_add_controls( $controls_settings );

        }

        $settings = array(
            'page_length' => array(
                'type' => 'select',
                'value' => 'wide' ,
                'label' => __('Length', 'site-editor'),
                'desc' => '',
                'options' =>array(
                    'wide'    => __('Wide', 'site-editor'),
                    'boxed'   => __('Boxed', 'site-editor')
                ),
                'priority'      => 15
            ),

            'sheet_width_page' => array(
                'type' => 'spinner',
                'after_field'  => 'px',
                'value' => 1100 ,
                'label' => __("Sheet Width" ,"site-editor"),
                'desc' => '',
                'priority'      => 20
            ),

        );

        $cr_settings = ModuleSettings::create_settings($settings , array());

        echo $cr_settings;
        $sed_sub_themes = array();
        $sed_sub_themes = apply_filters( "sed_sub_themes" , $sed_sub_themes );
        ?>

       <fieldset class="row_setting_box">
       <legend id="sed_image_image_settings_panel_title"><?php echo __("Select Scope","site-editor");?></legend>
        <div class="row_settings">
          <div class="row_setting_inner">
            <div id="sed-general-settings-sub-themes" class="clearfix sed-container-control-element"><span class="field_desc flt-help fa icon-question  fa-lg " title=""></span>
                <ul>
                    <li class="customize-settings-action sed-bp-form-radio" data-name="show_on_sub_themes_type" >
                        <div class="sed-bp-form-radio-item">
                            <label>
                                <input type="radio" name="sed-settings-theme-type" class="sed-settings-theme-type sed-element-control sed-bp-input sed-bp-radio-input" value="public">
                                <?php echo __("Public","site-editor");?>
                            </label>
                        </div>
                    </li>

                    <li class="sed-all-sub-themes item">
                        <label for="sed_sub_theme_check_0" class="sed-all-sub-themes-check-box" >
                            <input type="checkbox" id="sed_sub_theme_check_0" name="sed-sub-theme" value="all" class="">
                            <span class="sub_theme_title"><?php echo __("Show On All Layout","site-editor");?></span>
                        </label>
                    </li>
                    <?php
                    $i = 1;
                    foreach( $sed_sub_themes AS $name => $options ){
                    ?>
                    <li class="sub-theme-item item">
                        <label for="sed_sub_theme_check_<?php echo $i;?>" class="sed-sub-themes-check-box" data-sub-theme-name="<?php echo $name;?>">
                            <input type="checkbox" id="sed_sub_theme_check_<?php echo $i;?>" name="sed-sub-theme" value="<?php echo $name;?>" class="">
                            <span class="sub_theme_title"><?php echo $options['title']?></span>
                        </label>
                    </li>
                    <?php
                    $i++;
                    }
                    ?>

                    <li class="customize-settings-action sed-bp-form-radio" data-name="show_on_sub_themes_type" >
                        <div class="sed-bp-form-radio-item">
                        <label>
                            <input type="radio" name="sed-settings-theme-type" class="sed-settings-theme-type sed-element-control sed-bp-input sed-bp-radio-input" value="customize">
                            <?php echo __("Customize in current page","site-editor");?>
                        </label>
                        </div>
                    </li>

                    <li class="customize-settings-action sed-bp-form-radio" data-name="show_on_sub_themes_type" >
                        <div class="sed-bp-form-radio-item">
                        <label>
                            <input type="radio" name="sed-settings-theme-type" class="sed-settings-theme-type sed-element-control sed-bp-input sed-bp-radio-input" value="hidden">
                            <?php echo __("Hidden in current page","site-editor");?>
                        </label>
                        </div>
                    </li>
                </ul>

            </div>
          </div>
        </div>
       </fieldset>
    </div>
    </div>
</div>
