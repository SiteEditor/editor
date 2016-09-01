<?php
class ModuleStyleControls{

    public $id;
    public $controls = array();
    public $panels = array();
    public $style_editor_settings = array();

    function __construct(  $id ) {
        $this->id           = $id;

        $this->icons_classes = array(

            'background'        => 'fa f-sed icon-background fa-lg',
            'border'            => 'fa f-sed icon-border fa-lg',
            'border_radius'     => 'fa f-sed icon-cornersizes fa-lg',
            'padding'           => 'fa f-sed icon-padding fa-lg',
            'margin'            => 'fa f-sed icon-margin fa-lg',
            'shadow'            => 'fa f-sed icon-boxshadow fa-lg',
            'gradient'          => 'fa f-sed icon-gradient fa-lg',
            'position'          => 'fa f-sed icon-position fa-lg',
            'text_shadow'       => 'fa f-sed icon-textshadow fa-lg',
            'trancparency'      => 'fa f-sed icon-transparency fa-lg',
            'font'              => 'fa f-sed icon-font fa-lg', 
            'text_align'        => 'fa f-sed icon-justify fa-lg',
            'line_height'       => 'fa f-sed icon-textheight fa-lg',
            //'transform'         => 'fa f-sed icon-transform fa-lg',
            //'transition'        => 'fa f-sed icon-transition fa-lg',

        );

        $this->labeles = array(

            'background'        => __('background',"site-editor") ,
            'border'            => __('border',"site-editor") ,
            'border_radius'     => __('corner size',"site-editor") ,
            'padding'           => __('padding',"site-editor") ,
            'margin'            => __('margin',"site-editor") ,
            'shadow'            => __('box shadow',"site-editor") ,
            'gradient'          => __('gradient',"site-editor") ,
            'position'          => __('position',"site-editor") ,
            'text_shadow'       => __('text shadow',"site-editor") ,
            'trancparency'      => __('transparency',"site-editor") ,
            'font'              => __('font',"site-editor") ,
            'text_align'        => __('text align',"site-editor") ,
            'line_height'       => __('line height',"site-editor") ,
            //'transform'         => __('transform',"site-editor") ,
            //'transition'        => __('transition',"site-editor") ,

        );

    }

    function render(){
        global $site_editor_app;

        foreach( $this->labeles AS $control => $labele ){
            $func = $control;

            if( method_exists('ModuleStyleControls' , $func) )
                $this->$func(  );  //$values
        }

        foreach( $this->labeles AS $control => $label ){
            ModuleSettings::$group_id = "";
            $panels = isset( $this->panels[$control] ) ? $this->panels[$control] : array() ;

            if( is_array( $this->controls[$control] ) && !empty( $this->controls[$control] ) )
                $this->style_editor_settings[$control] = ModuleSettings::create_settings($this->controls[$control] , $panels);
        }

        add_action( "sed_footer" , array( $this , 'print_style_editor_settings' ) );

        $controls = array();

        foreach( $this->labeles AS $style => $label ){
            if( !is_array( $this->controls[$style] ) || empty( $this->controls[$style]) )
                continue;

            foreach( $this->controls[$style] AS $id => $control ){
                $args = array(
                    'settings'     => array(
                        'default'       => $control['settings_type']
                    ),
                    'type'                => $control['control_type'],
                    'category'            => "style-editor",
                    'sub_category'        => $style,           //border , background
                    'default_value'       => $control['value'],
                    'is_style_setting'    => $control['is_style_setting'],
                    'control_id'          => $id
                );

                if(isset($control["style_props"]) && !empty($control['style_props']))
                    $args['style_props'] = $control['style_props'];

                if(isset($control["control_param"]) && !empty($control["control_param"]))
                    $args = array_merge( $args , $control["control_param"]);

                $controls[$id] = $args;
            }
        }

        if( !empty( $controls ) ){
            sed_add_controls( $controls );
        }

    }

    function print_style_editor_settings(){
        foreach( $this->style_editor_settings AS $control => $output_settings ){
            ?>
            <script type="text/html"  id="style_editor_settings_<?php echo $control;?>_tmpl" >
                <?php if( $control == "border") echo '<div class="accordion-panel-settings">';?>
                <?php echo $output_settings;?>
                <?php if( $control == "border") echo '</div>';?>
            </script>
            <?php
        }
    }

    function add_style_control( $style , $panel_id , $selector ){

        $icon  = $this->icons_classes[ $style ];
        $label = $this->labeles[ $style ];

        return  array(
                    'type'      =>  'style_editor_button',
                    'label'     =>  $label ,
                    'icon'      =>  $icon,
                    'class'     =>  'sted_element_control_btn',
                    'panel'     =>  $panel_id ,
                    'atts'      =>  array(
                        'data-style-id'     => $style ,
                        'data-dialog-title' => $label ,
                        'data-selector'     => $selector
                    )
                );

    }

    function background( $values = array() ){

        $this->controls['background'] = array();

        $this->controls['background'][$this->id . "_background_color"] = array(
            'type'              => 'color',
            'value'             => isset( $values['color'] ) ? $values['color'] : "transparent" ,
            'label'             => __('background color', 'site-editor'),
            'desc'              => '<p><strong>Align:</strong> Module Align</p>',
            'settings_type'     =>  "background_color",
            'control_type'      =>  "color" ,
            'is_style_setting'  =>  true ,
            'style_props'       => "background-color" ,
            
        );


        $this->controls['background'][$this->id . "_background_image"  ] = array(
            'type'              => 'image',
            'value'             => isset( $values['image'] ) ? $values['image'] : ""  ,
            'label'             => __('Select Image', 'site-editor'),
            'remove_btn'        => true ,
            'desc'              => '<p><strong>Align:</strong> Module Align</p>',
            'settings_type'     =>  "background_image",
            'control_type'      =>  "image" ,
            'style_props'       => "background-image" ,
            'is_style_setting'  =>  true ,
        );

        $this->controls['background'][$this->id . "_external_background_image"  ] = array(
            'type'              => 'text',
            'value'             => isset( $values['external_image'] ) ? $values['external_image'] : ""  ,
            'label'             => __('Enter external url', 'site-editor'),
            'desc'              => '<p><strong>Align:</strong> Module Align</p>',
            'settings_type'     =>  "external_background_image",
            'control_type'      =>  "sed_element" ,
            'style_props'       => "background-image" ,
            'sub_type'          => "url" ,
            'is_style_setting'  =>  true ,
        );

        $this->controls['background'][$this->id . "_parallax_background_image"  ] = array(
            'type'              => 'checkbox',
            'value'             => isset( $values['parallax'] ) ? $values['parallax'] : "" ,
            'label'             => __('Parallax Background Image', 'site-editor'),
            'desc'              => '<p><strong>Align:</strong> Module Align</p>',
            'settings_type'     =>  "parallax_background_image",
            'control_type'      =>  "sed_element" ,
            'is_style_setting'  =>  true ,
            
        );

        $this->controls['background'][$this->id . "_parallax_background_ratio"  ] = array(
            'type'              => 'spinner',
            'value'             => isset( $values['parallax_ratio'] ) ? $values['parallax_ratio'] :"0.5" ,
            'label'             => __('Parallax Background Ratio', 'site-editor'),
            'desc'              => '<p><strong>Align:</strong> Module Align</p>',
            'settings_type'     =>  "parallax_background_ratio",
            'control_type'      =>  "spinner" ,
            'is_style_setting'  =>  true ,
            'control_param'     =>  array(
                'step'        => 0.1
            ),
            
        );


        $this->controls['background'][$this->id . "_background_attachment"  ] = array(
            'type'              => 'select',
            'value'             => isset( $values['attachment'] ) ? $values['attachment'] : "scroll" ,
            'label'             => __('Background attachment', 'site-editor'),
            'desc'              => '<p><strong>Background attachment</strong> Module Align</p>',
            'options' =>array(
                'scroll'     => __('Scroll', 'site-editor'),
                'fixed'     => __('Fixed ', 'site-editor')
            ),
            'settings_type'     =>  "background_attachment",
            'control_type'      =>  "sed_element" ,
            'style_props'       => "background-attachment" ,
            'is_style_setting'  =>  true ,
            
        );

        $this->controls['background'][$this->id . "_image_scaling"  ] = array(
            'type'              => 'select',
            'value'             => isset( $values['image_scaling'] ) ? $values['image_scaling'] : "normal" ,
            'label'             => __('Image Scaling', 'site-editor'),
            'desc'              => '<p><strong>Image Scaling</strong></p>',
            'options' =>array(
                'fit'                   => __('Fit', 'site-editor'),
                'tile'                  => __('Tile ', 'site-editor'),
                'tile-vertically'       => __('Tile Vertically', 'site-editor'),
                'tile-horizontally'     => __('Tile Horizontally ', 'site-editor'),
                'normal'                => __('Normal', 'site-editor'),
                'fullscreen'            => __('Full Screen ', 'site-editor'),
                'cover'                 => __('Cover ', 'site-editor'),
            ),
            'settings_type'     =>  "background_image_scaling",
            'control_type'      =>  "sed_element" ,
            'style_props'       =>  "image-scaling" ,
            'is_style_setting'  =>  true ,
            
        );

        $bg_position_control = $this->id . "_background_position";

        ob_start();
        ?>
                                           <!-- sed_menu +  menu_bar_bg_position :::: shortcode name + control name -->
        <fieldset class="row_setting_box">
            <legend id="sed_pb_sed_image_image_settings">
                <a href="javascript:void(0)" class=""  title="<?php echo __("background position" ,"site-editor");  ?>" id="<?php echo $bg_position_control;?>_btn" >
                    <span class="fa f-sed icon-backgroundposition fa-lg "></span>
                    <span class="el_txt"><?php echo __("background position" ,"site-editor");  ?> </span>
                </a>
            </legend>
            <div  id="sed-app-control-<?php echo $bg_position_control;?>">
                <ul  class="background-position dropdown-menu sed-dropdown ">
                   <li class="background-psn">
                  <div><a class="background-psn-sq" data-value="left top"><!--<img class="background-psn-img1" src="<?php echo get_modules_url()."images/bg_align_top_left2.png" ?>"/>--></a></div>
                  <div><a class="background-psn-sq" data-value="center top"></a></div>
                  <div><a class="background-psn-sq" data-value="right top"><!--<img class="background-psn-img2" src="<?php echo get_modules_url()."images/bg_align_top_right2.png" ?>"/>--></a></div>
                  <div><a class="background-psn-sq" data-value="left center"></a></div>
                  <div><a class="background-psn-sq" data-value="center center"><!--<img class="background-psn-img3" src="<?php echo get_modules_url()."images/bg_align_top_left3.png" ?>"/>--></a></div>
                  <div><a class="background-psn-sq" data-value="right center"></a></div>
                  <div><a class="background-psn-sq" data-value="left bottom"><!--<img class="background-psn-img4" src="<?php echo get_modules_url()."images/bg_align_bottom_left3.png" ?>"/>--></a></div>
                  <div><a class="background-psn-sq" data-value="center bottom"></a></div>
                  <div><a class="background-psn-sq" data-value="right bottom"><!--<img class="background-psn-img5" src="<?php echo get_modules_url()."images/bg_align_bottom_right9.png" ?>"/>--></a></div>
                   </li>
                </ul>
            </div>
        </fieldset>

        <?php
        $bg_position_content = ob_get_contents();
        ob_end_clean();

        $this->controls['background'][$this->id . "_background_position"  ] = array(
            'type'              =>  'custom',
            'value'             =>  isset( $values['position'] ) ? $values['position'] : "center center",
            'in_box'            =>  false ,
            'html'              =>  $bg_position_content ,
            'settings_type'     =>  "background_position",
            'control_type'      =>  "dropdown" ,
            'style_props'       =>  "background-position" ,
            'is_style_setting'  =>  true ,
            'control_param'     =>  array(
                'options_selector'  => '.background-psn-sq',
                'selected_class'    => 'active_background_position'
            ),
            
        );

    }

    function border( $values = array() ){

        $this->controls['border'] = array();
        $this->panels['border'] = array();
        $border_sides = array(
            "top"    => __('border top', 'site-editor') ,
            "left"   => ( is_rtl() ) ? __('border right', 'site-editor') : __('border left', 'site-editor') ,
            "bottom" => __('border bottom', 'site-editor') ,
            "right"  => ( is_rtl() ) ? __('border left', 'site-editor')  : __('border right', 'site-editor')
        );

        foreach( $border_sides AS $side => $side_label ){

             $panel_id = $this->id . "_border_{$side}_panel";
             $this->panels['border'][$panel_id] = array(
                'title'         =>  $side_label  ,
                'label'         =>  $side_label ,
                'capability'    => 'edit_theme_options' ,
                'type'          => 'accordion_item' ,
                'description'   => '' ,
                'parent_id'     => 'root' ,
                'priority'      => 9 ,
                'id'            => $panel_id  ,
                'atts'          =>  array(
                    'class'                 =>   "sed-border-side-panel-header" ,
                    'data-border-side'      => $side
                ) ,
            );

            $this->controls['border'][$this->id . "_border_{$side}_color"  ] = array(
                'type'              => 'color',
                'value'             => isset( $values['color'] ) ? $values['color'] : "transparent" ,
                'label'             => __('border color', 'site-editor'),
                'desc'              => '<p><strong>Align:</strong> Module Align</p>',
                'settings_type'     =>  "border_{$side}_color",
                'control_type'      =>  "color" ,
                'style_props'       =>  "border-{$side}-color" ,
                'is_style_setting'  =>  true ,
                /*'control_param'     =>  array(
                    'force_refresh_setting' => true
                ),*/
                'panel' =>  $panel_id

            );

            $this->controls['border'][$this->id . "_border_{$side}_width"  ] = array(
                'type'              => 'spinner',
                'value'             => isset( $values['width'] ) ? $values['width'] : 0 ,
                'label'             => __('border width', 'site-editor'),
                'desc'              => '<p><strong>Align:</strong> Module Align</p>',
                'settings_type'     =>  "border_{$side}_width",
                'control_type'      =>  "spinner" ,
                'style_props'       =>  "border-{$side}-width" ,
                'is_style_setting'  =>  true ,
                /*'control_param'     =>  array(
                    'force_refresh_setting' => true
                ),*/
                'panel' =>  $panel_id
            );

            $border_style_control = $this->id . "_border_{$side}_style";

            ob_start();
            ?>                                    <!-- sed_menu +  menu_bar_bg_position :::: shortcode name + control name -->
            <fieldset class="row_setting_box">
                <legend id="sed_pb_sed_image_image_settings">
                    <a  href="javascript:void(0)" class=""  title="<?php echo __("border Style" ,"site-editor");  ?>" id="<?php echo $border_style_control ;?>_btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button">
                        <span class="el_txt"><?php echo __("border style" ,"site-editor");  ?></span>

                    </a>
                </legend>
                <div  id="sed-app-control-<?php echo $border_style_control ;?>">

                    <ul class="dropdown-menu sed-dropdown" role="menu">
                    <!--<li><a class="heading-item  first-heading-item"  href="#"><?php echo __("No gradient" ,"site-editor");  ?></a></li>
                    <li class="border_hd"><a href="#" data-value="inherit" class="border border_sty1" ></a></li>  -->
                    <li class="border-item" data-value="none"><a href="#"><span class="border border_sty2" ></span></a></li>
                    <li class="border-item" data-value="dotted"><a href="#"><span class="border border_sty3" ></span></a></li>
                    <li class="border-item" data-value="dashed"><a href="#"><span class="border border_sty4" ></span></a></li>
                    <li class="border-item" data-value="solid"><a href="#"><span class="border border_sty5" ></span></a></li>
                    <li class="border-item" data-value="double"><a href="#"><span class="border border_sty6" ></span></a></li>
                    <li class="border-item" data-value="groove"><a href="#"><span class="border border_sty7" ></span></a></li>
                    <li class="border-item" data-value="ridge"><a href="#"><span class="border border_sty8" ></span></a></li>
                    <li class="border-item" data-value="inset"><a href="#"><span class="border border_sty9" ></span></a></li>
                    <li class="border-item" data-value="outset"><a href="#"><span class="border border_sty10" ></span></a></li>
                    </ul>
                </div>
            </fieldset>
            <?php
            $border_style_content = ob_get_contents();
            ob_end_clean();

            $this->controls['border'][$border_style_control] = array(
                'type'              =>  'custom',
                'value'             =>  isset( $values['style'] ) ? $values['style'] : 'double',
                'in_box'            =>  false ,
                'html'              =>  $border_style_content ,
                'settings_type'     =>  "border_{$side}_style",
                'control_type'      =>  "dropdown" ,
                'style_props'       =>  "border-{$side}-style" ,
                'is_style_setting'  =>  true ,
                'control_param'     =>  array(
                    'options_selector'  => '.border-item',
                    'selected_class'    => 'active_border' ,
                    //'force_refresh_setting' => true
                ),
                'panel' =>  $panel_id
            );
        }
        /*$this->controls['border'][$this->id . "_border_side"  ] = array(
            'type'              => 'checkbox',
            'value'             => isset( $values['side'] ) ? $values['side'] : "" ,
            'subtype'           =>  'multiple',
            'options'           => array(
                "top"           =>   __('Top', 'site-editor') ,
                "left"          =>   __('Left', 'site-editor') ,
                "right"         =>   __('Right', 'site-editor') ,
                "bottom"        =>   __('Bottom', 'site-editor')
            ),
            'atts'              => array(
                "class"       =>   "border-side"
            ),
            'label'             => __('Menu bar border side', 'site-editor'),
            'desc'              => '<p><strong>Align:</strong> Module Align</p>',
            'settings_type'     =>  "border_side",
            'control_type'      =>  "checkboxes" ,
            
            'is_style_setting'  =>  true ,
            'control_param'     =>  array(
                'options_selector'  => '.border-side' ,
                'force_refresh_setting' => true
            ),
            
        );*/

    }

    function border_radius( $values = array() ){

        $lock_id = "sed_pb_".$this->id."_border_radius_lock";

        $spinner_class = 'sed-border-radius-spinner-' . $this->id;    //shortcode_name
        $spinner_class_selector = '.' . $spinner_class;
        $sh_name = $this->id;
        $sh_name_c = $sh_name. "_border_radius_";

        $controls = array( $sh_name_c . "tr" , $sh_name_c . "tl" , $sh_name_c . "br" , $sh_name_c . "bl" );

        $this->controls['border_radius'] = array();

        $this->controls['border_radius'][$this->id . "_border_radius_tr"  ] = array(
            'type'              => 'spinner',
            'value'             => isset( $values['tr'] ) ? $values['tr'] : "0" ,
            'label'             => ( is_rtl() ) ? __('Top left corner', 'site-editor') : __('Top right corner', 'site-editor') ,
            'desc'              => '<p><strong>Align:</strong> Module Align</p>',
            'settings_type'     =>  "border_radius_tr",
            'control_type'      =>  "spinner" ,
            'style_props'       =>  "border-top-right-radius" ,
            'is_style_setting'  =>  true ,
            'atts'  => array(
                "class" =>   $spinner_class
            ) ,
            'control_param'     =>  array(
                'lock'    => array(
                    'id'       => $lock_id,
                    'spinner'  => $spinner_class_selector,
                    'controls' => array( $sh_name_c . "tl" , $sh_name_c . "br" , $sh_name_c . "bl" )
                ),
                
                'min'   =>  0 ,
                //'radius_demo' => true,
                //'max'     => 100,
                //'step'    => 2,
                //'page'    => 5
            ),
            
        );

        $this->controls['border_radius'][$this->id . "_border_radius_tl"  ] = array(
                'type'              => 'spinner',
                'value'             => isset( $values['tl'] ) ? $values['tl'] : "0" ,
                'label'             => ( is_rtl() ) ? __('Top right corner', 'site-editor') : __('Top left corner', 'site-editor') ,
                'desc'              => '<p><strong>Align:</strong> Module Align</p>',
                'settings_type'     =>  "border_radius_tl",
                'control_type'      =>  "spinner" ,
                'style_props'       =>  "border-top-left-radius" ,
                'is_style_setting'  =>  true ,
                'atts'  => array(
                    "class" =>   $spinner_class
                ) ,
                'control_param'     =>  array(
                    'lock'    => array(
                        'id'       => $lock_id,
                        'spinner'  => $spinner_class_selector,
                        'controls' => array( $sh_name_c . "tr" , $sh_name_c . "br" , $sh_name_c . "bl" )
                    ),
                    
                    'min'   =>  0 ,
                    //'radius_demo' => true,
                    //'max'     => 100,
                    //'step'    => 2,
                    //'page'    => 5
                ),
                
        );

        $this->controls['border_radius'][$this->id . "_border_radius_br"  ] = array(
                'type'              => 'spinner',
                'value'             => isset( $values['br'] ) ? $values['br'] : "0" ,
                'label'             => ( is_rtl() ) ? __('Bottom left corner', 'site-editor') : __('Bottom right corner', 'site-editor'),
                'desc'              => '<p><strong>Align:</strong> Module Align</p>',
                'settings_type'     =>  "border_radius_br",
                'control_type'      =>  "spinner" ,
                'style_props'       =>  "border-bottom-right-radius" ,
                'is_style_setting'  =>  true ,
                'atts'  => array(
                    "class" =>   $spinner_class
                ) ,
                'control_param'     =>  array(
                    'lock'    => array(
                        'id'       => $lock_id,
                        'spinner'  => $spinner_class_selector,
                        'controls' => array( $sh_name_c . "tr" , $sh_name_c . "tl" , $sh_name_c . "bl" )
                    ),
                    
                    'min'   =>  0 ,
                    //'radius_demo' => true,
                    //'max'     => 100,
                    //'step'    => 2,
                    //'page'    => 5
                ),
                
        );

        $this->controls['border_radius'][$this->id . "_border_radius_bl"  ] = array(
                'type'              => 'spinner',
                'value'             => isset( $values['bl'] ) ? $values['bl'] : "0" ,
                'label'             => ( is_rtl() ) ? __('Bottom right corner', 'site-editor') : __('Bottom left corner', 'site-editor'),
                'desc'              => '<p><strong>Align:</strong> Module Align</p>',
                'settings_type'     =>  "border_radius_bl",
                'control_type'      =>  "spinner" ,
                'style_props'       =>  "border-bottom-left-radius" ,
                'is_style_setting'  =>  true ,
                'atts'  => array(
                    "class" =>   $spinner_class
                ) ,
                'control_param'     =>  array(
                    'lock'    => array(
                        'id'       => $lock_id,
                        'spinner'  => $spinner_class_selector,
                        'controls' => array( $sh_name_c . "tr" , $sh_name_c . "tl" , $sh_name_c . "br" )
                    ),
                    
                    'min'   =>  0 ,
                    //'radius_demo' => true,
                    //'max'     => 100,
                    //'step'    => 2,
                    //'page'    => 5
                ),
                
        );

        $this->controls['border_radius'][$this->id . "_border_radius_lock"  ] = array(
                'type'              => 'checkbox',
                'value'             => isset( $values['lock'] ) ? $values['lock'] : false ,
                'label'             => __('lock Corners Together', 'site-editor'),
                'desc'              => '<p><strong>Align:</strong> Module Align</p>',
                'settings_type'     =>  "border_radius_lock",
                'control_type'      =>  "spinner_lock" ,
                
                'is_style_setting'  =>  true ,
                'atts'  => array(
                    "class" =>   "sed-lock-spinner"
                ) ,
                'control_param'     =>  array(
                    'spinner' =>  $spinner_class_selector ,
                    'controls' => array( $sh_name_c . "tr" , $sh_name_c . "tl" , $sh_name_c . "br" , $sh_name_c . "bl" )
                ),
                
        );

    }

    function padding( $values = array() ){

        $settings = array();
        $lock_id = "sed_pb_".$this->id."_padding_lock";

        $spinner_class = 'sed-padding-spinner-' . $this->id;
        $spinner_class_selector = '.' . $spinner_class;
        $sh_name = $this->id;
        $sh_name_c = $sh_name. "_padding_";

        $controls = array( $sh_name_c . "top" , $sh_name_c . "right" , $sh_name_c . "left" , $sh_name_c . "bottom" );

        $this->controls['padding'] = array();

  		$this->controls['padding'][$this->id . '_padding_top'] = array(
  			'type' => 'spinner',
            'value' => isset( $values['top'] ) ? $values['top'] : "0",
  			'label' => __('Top', 'site-editor'),
  			'desc' => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
            'settings_type'     =>  "padding_top",
            'control_type'      =>  "spinner" ,
            'style_props'       =>  "padding-top" ,
            'is_style_setting'  =>  true ,
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
                
                //'max'     => 100,
                //'step'    => 2,
                //'page'    => 5
            ),
            
  		);

  		$this->controls['padding'][$this->id . '_padding_left'] = array(
  			'type' => 'spinner',
            'value' => isset( $values['left'] ) ? $values['left'] : "0",
  			'label' => ( is_rtl() ) ? __('Right', 'site-editor') : __('Left', 'site-editor'),
  			'desc' => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
            'settings_type'     =>  "padding_left",
            'control_type'      =>  "spinner" ,
            'style_props'       =>  "padding-left" ,
            'is_style_setting'  =>  true ,
            'atts'  => array(
                "class" =>   $spinner_class
            ) ,
            'control_param'     =>  array(
                'lock'    => array(
                    'id'       => $lock_id,
                    'spinner'  => $spinner_class_selector,
                    'controls' => array( $sh_name_c . "top" , $sh_name_c . "right" , $sh_name_c . "bottom" )
                ),
                'min'   =>  0 ,
                
                //'max'     => 100,
                //'step'    => 2,
                //'page'    => 5
            ),
            
  		);

  		$this->controls['padding'][$this->id . '_padding_right'] = array(
  			'type' => 'spinner',
            'value' => isset( $values['right'] ) ? $values['right'] : "0",
  			'label' =>  ( is_rtl() ) ? __('Left', 'site-editor') : __('Right', 'site-editor'),
  			'desc' => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
            'settings_type'     =>  "padding_right",
            'control_type'      =>  "spinner" ,
            'style_props'       =>  "padding-right" ,
            'is_style_setting'  =>  true ,
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
                
                //'max'     => 100,
                //'step'    => 2,
                //'page'    => 5
            ),
            
  		);

  		$this->controls['padding'][$this->id . '_padding_bottom'] = array(
  			'type' => 'spinner',
            'value' => isset( $values['bottom'] ) ? $values['bottom'] : "0",
  			'label' => __('Bottom', 'site-editor'),
  			'desc' => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
            'settings_type'     =>  "padding_bottom",
            'control_type'      =>  "spinner" ,
            'style_props'       =>  "padding-bottom" ,
            'is_style_setting'  =>  true ,
            'atts'  => array(
                "class" =>   $spinner_class
            ) ,
            'control_param'     =>  array(
                'lock'    => array(
                    'id'       => $lock_id,
                    'spinner'  => $spinner_class_selector ,
                    'controls' => array( $sh_name_c . "top" , $sh_name_c . "right" , $sh_name_c . "left" )
                ),
                'min'   =>  0 ,
                
                //'max'     => 100,
                //'step'    => 2,
                //'page'    => 5
            ),
            
  		);

  		$this->controls['padding'][$this->id . '_padding_lock'] = array(
  			'type' => 'checkbox',
            'value' => isset( $values['lock'] ) ? $values['lock'] : false,
  			'label' => __('lock Spacings Together', 'site-editor'),
  			'desc' => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
            'settings_type'     =>  "padding_lock",
            'control_type'      =>  "spinner_lock" ,

            'is_style_setting'  =>  true ,
            'atts'  => array(
                "class" =>   "sed-lock-spinner"
            ) ,
            'control_param'     =>  array(
                'spinner' =>  $spinner_class_selector ,
                'controls' => array( $sh_name_c . "top" , $sh_name_c . "right" , $sh_name_c . "left" , $sh_name_c . "bottom" )
            ),
            
  		);
    }

    function margin( $values = array() ){

        $settings = array();
        $lock_id = "sed_pb_".$this->id."_margin_lock";

        $spinner_class = 'sed-margin-spinner-' . $this->id;
        $spinner_class_selector = '.' . $spinner_class;
        $sh_name = $this->id;
        $sh_name_c = $sh_name. "_margin_";

        $controls = array( $sh_name_c . "top" , $sh_name_c . "right" , $sh_name_c . "left" , $sh_name_c . "bottom" );

        $this->controls['margin'] = array();

  		$this->controls['margin'][$this->id . '_margin_top'] = array(
  			'type' => 'spinner',
            'value' => isset( $values['top'] ) ? $values['top'] : 0,
  			'label' => __('Top', 'site-editor'),
  			'desc' => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
            'settings_type'     =>  "margin_top",
            'control_type'      =>  "spinner" ,
            'style_props'       =>  "margin-top" ,
            'is_style_setting'  =>  true ,
            'atts'  => array(
                "class" =>   $spinner_class
            ) ,
            'control_param'     =>  array(
                'lock'    => array(
                    'id'       => $lock_id,
                    'spinner'  => $spinner_class_selector,
                    'controls' => array( $sh_name_c . "right" , $sh_name_c . "left" , $sh_name_c . "bottom" )
                ),
                //'min'   =>  0 ,
                
                //'max'     => 100,
                //'step'    => 2,
                //'page'    => 5
            ),
            
  		);

  		$this->controls['margin'][$this->id . '_margin_left'] = array(
  			'type' => 'spinner',
            'value' => isset( $values['left'] ) ? $values['left'] : 0,
  			'label' => ( is_rtl() ) ? __('Right', 'site-editor') : __('Left', 'site-editor'),
  			'desc' => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
            'settings_type'     =>  "margin_left",
            'control_type'      =>  "spinner" ,
            'style_props'       =>  "margin-left" ,
            'is_style_setting'  =>  true ,
            'atts'  => array(
                "class" =>   $spinner_class
            ) ,
            'control_param'     =>  array(
                'lock'    => array(
                    'id'       => $lock_id,
                    'spinner'  => $spinner_class_selector,
                    'controls' => array( $sh_name_c . "top" , $sh_name_c . "right" , $sh_name_c . "bottom" )
                ),
                //'min'   =>  0 ,
                
                //'max'     => 100,
                //'step'    => 2,
                //'page'    => 5
            ),
            
  		);

  		$this->controls['margin'][$this->id . '_margin_right'] = array(
  			'type' => 'spinner',
            'value' => isset( $values['right'] ) ? $values['right'] : 0,
  			'label' => ( is_rtl() ) ? __('Left', 'site-editor') : __('Right', 'site-editor'),
  			'desc' => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
            'settings_type'     =>  "margin_right",
            'control_type'      =>  "spinner" ,
            'style_props'       =>  "margin-right" ,
            'is_style_setting'  =>  true ,
            'atts'  => array(
                "class" =>   $spinner_class
            ) ,
            'control_param'     =>  array(
                'lock'    => array(
                    'id'       => $lock_id,
                    'spinner'  => $spinner_class_selector,
                    'controls' => array( $sh_name_c . "top" , $sh_name_c . "left" , $sh_name_c . "bottom" )
                ),
                //'min'   =>  0 ,
                
                //'max'     => 100,
                //'step'    => 2,
                //'page'    => 5
            ),
            
  		);

  		$this->controls['margin'][$this->id . '_margin_bottom'] = array(
  			'type' => 'spinner',
            'value' => isset( $values['bottom'] ) ? $values['bottom'] : 0,
  			'label' => __('Bottom', 'site-editor'),
  			'desc' => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
            'settings_type'     =>  "margin_bottom",
            'control_type'      =>  "spinner" ,
            'style_props'       =>  "margin-bottom" ,
            'is_style_setting'  =>  true ,
            'atts'  => array(
                "class" =>   $spinner_class
            ) ,
            'control_param'     =>  array(
                'lock'    => array(
                    'id'       => $lock_id,
                    'spinner'  => $spinner_class_selector ,
                    'controls' => array( $sh_name_c . "top" , $sh_name_c . "right" , $sh_name_c . "left" )
                ),
                //'min'   =>  0 ,
                
                //'max'     => 100,
                //'step'    => 2,
                //'page'    => 5
            ),
            
  		);

  		$this->controls['margin'][$this->id . '_margin_lock'] = array(
  			'type' => 'checkbox',
            'value' => isset( $values['lock'] ) ? $values['lock'] : false,
  			'label' => __('lock Spacing Together', 'site-editor'),
  			'desc' => '<p><strong>Spacing:</strong> Module Spacing from top , left , bottom , right.</p>',
            'settings_type'     =>  "margin_lock",
            'control_type'      =>  "spinner_lock" ,
            
            'is_style_setting'  =>  true ,
            'atts'  => array(
                "class" =>   "sed-lock-spinner"
            ) ,
            'control_param'     =>  array(
                'spinner' =>  $spinner_class_selector ,
                'controls' => array( $sh_name_c . "top" , $sh_name_c . "right" , $sh_name_c . "left" , $sh_name_c . "bottom" )
            ),
            
  		);
    }

    function shadow( $values = array() ){

        $this->controls['shadow'] = array();

        $this->controls['shadow'][$this->id . "_shadow_color"  ] = array(
            'type'              => 'color',
            'value'             => isset( $values['color'] ) ? $values['color'] : "transparent" ,
            'label'             => __('color', 'site-editor'),
            'desc'              => '<p><strong>shadow color:</strong></p>',
            'settings_type'     =>  "shadow_color",
            'control_type'      =>  "color" ,
            //'style_props'       =>  "box-shadow-color" ,
            'is_style_setting'  =>  true ,
            /*'control_param'     =>  array(
                'force_refresh_setting' => true
            ),*/
            
        );

        $box_shadow_control = $this->id . "_shadow";

        ob_start();
        ?>                                    <!-- sed_menu +  menu_bar_bg_position :::: shortcode name + control name -->

        <fieldset class="row_setting_box">
            <legend id="sed_pb_sed_image_image_settings">
                <a  href="javascript:void(0)" class="btn btn-default"  title="<?php echo __("box shadow" ,"site-editor");  ?>" data-toggle="dropdown" id="<?php echo $box_shadow_control ;?>_btn" role="button">
                      <span class="fa f-sed icon-boxshadow fa-lg "></span>
                      <span class="el_txt"><?php echo __("box shadow" ,"site-editor");  ?></span>
                </a>
            </legend>
            <div class="dropdown" id="sed-app-control-<?php echo $box_shadow_control ;?>">

                <form role="menu" class="dropdown-menu dropdown-common sed-dropdown"  sed-shadow-cp-el="#shadow-colorpicker" sed-style-element="">
                  <div class="dropdown-content sed-dropdown content">

                      <div>
                        <ul>
                            <li>
                            <a class="heading-item first-heading-item" data-position="topLeft"  href="#"><?php echo __("no shadow" ,"site-editor");  ?></a>
                            </li>
                            <li>
                             <ul class="itme-box-shadow">
                               <li class="no-box-shadow shadow no_shadow" data-value="none"><a href="#"><span  class="style-box-shadow"></span></a></li>
                               <li class="clr"></li>
                             </ul>
                            </li>
                        </ul>
                      </div>
                      <div>
                        <ul>
                            <li>
                            <a class="heading-item" data-position="topLeft"  href="#"><?php echo __("shadow" ,"site-editor");  ?></a>
                            </li>
                            <li>
                             <ul class="itme-box-shadow">
                                <li class="shadow border-box-type1" data-value="0px 0px 5px -1px" ><a  href="#"><span  class="style-box-shadow1 "></span></a></li>
                                <li class="shadow border-box-type2" data-value="0 0 14px -6px"    ><a href="#"><span  class="style-box-shadow2"></span></a></li>
                                <li class="shadow border-box-type1" data-value="2px 2px 5px -1px" ><a  href="#"><span  class="style-box-shadow3"></span></a></li>
                                <li class="shadow border-box-type2" data-value="2px -2px 5px -1px" ><a  href="#"><span  class="style-box-shadow4"></span></a></li>
                                <li class="shadow border-box-type1" data-value="-2px 2px 5px -1px" ><a  href="#"><span  class="style-box-shadow5"></span></a></li>
                                <li class="shadow border-box-type2" data-value="-2px -2px 5px -1px" ><a  href="#"><span  class="style-box-shadow6"></span></a></li>
                                <li class="shadow border-box-type1" data-value="0px 2px 5px -1px" ><a  href="#"><span  class="style-box-shadow7"></span></a></li>
                                <li class="shadow border-box-type2" data-value="0px -2px 5px -1px" ><a  href="#"><span  class="style-box-shadow8"></span></a></li>
                                <li class="shadow border-box-type3" data-value="2px 0px 5px -1px" ><a  href="#"><span  class="style-box-shadow9"></span></a></li>
                                <li class="shadow border-box-type4" data-value="-2px 0px 5px -1px" ><a  href="#"><span  class="style-box-shadow10"></span></a></li>
                                <li class="clr"></li>
                             </ul>
                            </li>
                        </ul>
                      </div>
                      <div>
                        <ul>
                            <li>
                            <a class="heading-item" data-position="topLeft"  href="#"><?php echo __("shadow inset" ,"site-editor");  ?></a>
                            </li>
                            <li>
                             <ul class="itme-box-shadow">
                                <li class="shadow border-box-type1" data-value="0px 0px 5px -1px inset"  ><a href="#"><span  class="style-box-shadow11 "></span></a></li>
                                <li class="shadow border-box-type2" data-value="0 0 14px -6px inset"     ><a href="#"><span  class="style-box-shadow12"></span></a></li>
                                <li class="shadow border-box-type1" data-value="2px 2px 5px -1px inset"  ><a href="#"><span  class="style-box-shadow13"></span></a></li>
                                <li class="shadow border-box-type2" data-value="2px -2px 5px -1px inset" ><a href="#"><span  class="style-box-shadow14"></span></a></li>
                                <li class="shadow border-box-type1" data-value="-2px 2px 5px -1px inset" ><a href="#"><span  class="style-box-shadow15"></span></a></li>
                                <li class="shadow border-box-type2" data-value="-2px -2px 5px -1px inset"><a  href="#"><span  class="style-box-shadow16"></span></a></li>
                                <li class="shadow border-box-type1" data-value="0px 2px 5px -1px inset"  ><a href="#"><span  class="style-box-shadow17"></span></a></li>
                                <li class="shadow border-box-type2" data-value="0px -2px 5px -1px inset" ><a href="#"><span  class="style-box-shadow18"></span></a></li>
                                <li class="shadow border-box-type3" data-value="2px 0px 5px -1px inset"  ><a href="#"><span  class="style-box-shadow19"></span></a></li>
                                <li class="shadow border-box-type4" data-value="-2px 0px 5px -1px inset" ><a href="#"><span  class="style-box-shadow20"></span></a></li>
                                <li class="clr"></li>
                             </ul>
                            </li>
                        </ul>
                      </div>
                  </div>
                </form>
          </div>
      </fieldset>
        <?php
        $box_shadow_content = ob_get_contents();
        ob_end_clean();

        $this->controls['shadow'][$this->id . "_shadow"  ] = array(
            'type'              =>  'custom',
            'in_box'            =>   false ,
            'html'              =>  $box_shadow_content ,
            'value'             => isset( $values['shadow'] ) ? $values['shadow'] : 'none' ,
            'settings_type'     =>  "shadow",
            'control_type'      =>  "dropdown" ,
            //'style_props'       =>  "box-shadow" ,
            'is_style_setting'  =>  true ,
            'control_param'     =>  array(
                'options_selector'    => '.shadow',
                'selected_class'      => 'shadow_select'
            ),
            
        );

    }

    //gradeint in version 1.0.0 not support default value
    function gradient( $values = "" ){

       $this->controls['gradient'] = array();

       $gradient_control = $this->id . "_gradient";

        ob_start();
        ?>                                    <!-- sed_menu +  menu_bar_bg_position :::: shortcode name + control name -->
        <fieldset class="row_setting_box">
            <legend id="sed_pb_sed_image_image_settings">
                <a  class="btn btn-default" title="<?php echo __("gradient" ,"site-editor");  ?>" data-toggle="dropdown" id="<?php echo $gradient_control ;?>_btn" role="button" >
                <span class="fa f-sed icon-gradient fa-lg "></span>
                <span class="el_txt"><?php echo __("gradient" ,"site-editor");  ?> </span>
                </a>
            </legend>

          <div id="sed-app-control-<?php echo $gradient_control ;?>">

            <form role="menu" class="dropdown-menu dropdown-common sed-dropdown" sed-style-element="body">
              <div id="" class="dropdown-content content">

                  <div>
                    <ul>
                        <li>
                        <a class="heading-item  first-heading-item" data-position="topLeft"  href="#"><?php echo __("No Gradient" ,"site-editor");  ?></a>
                        </li>
                        <li>
                         <ul class="gradient">
                            <li><a class="sed-gradient sed-no-gradient" sed-style-element="body" href="#"><span class="no_gradient"></span></a></li>
                            <li class="clr"></li>
                         </ul>
                        </li>
                    </ul>
                  </div>
                  <div>
                    <ul>
                        <li>
                        <a class="heading-item" data-position="topLeft"  href="#"><?php echo __("Normal" ,"site-editor");  ?></a>
                        </li>
                        <li>
                         <ul class="gradient">
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0,1" data-gradient-Orientation="vertical" href="#"><span class="gradient1"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="1,0" data-gradient-Orientation="vertical"  href="#"><span class="gradient2"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.5,0.5" data-gradient-Orientation="vertical"  href="#"><span class="gradient3"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.25,0.75" data-gradient-Orientation="vertical"  href="#"><span class="gradient4"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.75,0.25" data-gradient-Orientation="vertical"  href="#"><span class="gradient5"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0,1" data-gradient-Orientation="horizontal"  href="#"><span class="gradient6"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="1,0" data-gradient-Orientation="horizontal"  href="#"><span class="gradient7"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.5,0.5" data-gradient-Orientation="horizontal"  href="#"><span class="gradient8"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.25,0.75" data-gradient-Orientation="horizontal"  href="#"><span class="gradient9"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.75,0.25" data-gradient-Orientation="horizontal"  href="#"><span class="gradient10"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="1,1" data-gradient-Orientation="vertical"  href="#"><span class="gradient11"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="1,1" data-gradient-Orientation="horizontal"  href="#"><span class="gradient12"></span></a></li>
                            <li class="clr"></li>
                         </ul>
                        </li>
                    </ul>
                  </div>
                  <div>
                    <ul>
                        <li>
                        <a class="heading-item" data-position="topLeft"  href="#"><?php echo __("Diagonal" ,"site-editor");  ?></a>
                        </li>
                        <li>
                         <ul class="gradient">
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0,1" data-gradient-Orientation="diagonal-rb" href="#"><span class="gradient_dg1"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="1,0" data-gradient-Orientation="diagonal-rb"  href="#"><span class="gradient_dg2"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.5,0.5" data-gradient-Orientation="diagonal-rb"  href="#"><span class="gradient_dg3"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.25,0.75" data-gradient-Orientation="diagonal-rb"  href="#"><span class="gradient_dg4"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.75,0.25" data-gradient-Orientation="diagonal-rb"  href="#"><span class="gradient_dg5"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0,1" data-gradient-Orientation="diagonal-rt"  href="#"><span class="gradient_dg6"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="1,0" data-gradient-Orientation="diagonal-rt"  href="#"><span class="gradient_dg7"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.5,0.5" data-gradient-Orientation="diagonal-rt"  href="#"><span class="gradient_dg8"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.25,0.75" data-gradient-Orientation="diagonal-rt"  href="#"><span class="gradient_dg9"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.75,0.25" data-gradient-Orientation="diagonal-rt"  href="#"><span class="gradient_dg10"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="1,1" data-gradient-Orientation="diagonal-rt"  href="#"><span class="gradient_dg11"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="1,1" data-gradient-Orientation="diagonal-rb"  href="#"><span class="gradient_dg12"></span></a></li>
                            <li class="clr"></li>
                         </ul>
                        </li>
                    </ul>
                  </div>
                  <div>
                    <ul>
                        <li>
                        <a class="heading-item" data-position="topLeft"  href="#"><?php echo __("Radial" ,"site-editor");  ?></a>
                        </li>
                        <li>
                         <ul class="gradient">
                            <li><a class="sed-gradient"  data-gradient-type="radial" data-gradient-percent="0,100"  data-gradient-opacity="0,1" data-gradient-Orientation="radial" href="#"><span class="gradient_elp1"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="radial" data-gradient-percent="0,100"  data-gradient-opacity="1,0" data-gradient-Orientation="radial"  href="#"><span class="gradient_elp2"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="radial" data-gradient-percent="0,100"  data-gradient-opacity="0.5,0.5" data-gradient-Orientation="radial"  href="#"><span class="gradient_elp3"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="radial" data-gradient-percent="0,100"  data-gradient-opacity="0.25,0.75" data-gradient-Orientation="radial"  href="#"><span class="gradient_elp4"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="radial" data-gradient-percent="0,100"  data-gradient-opacity="0.75,0.25" data-gradient-Orientation="radial"  href="#"><span class="gradient_elp5"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="radial" data-gradient-percent="0,100"  data-gradient-opacity="1,1" data-gradient-Orientation="radial"  href="#"><span class="gradient_elp6"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="radial" data-gradient-percent="0,100"  data-gradient-opacity="1,1" data-gradient-Orientation="radial"  href="#"><span class="gradient_elp7"></span></a></li>
                            <li><a class="sed-gradient"  data-gradient-type="radial" data-gradient-percent="63,82"  data-gradient-opacity="0.9,1" data-gradient-Orientation="radial"  href="#"><span class="gradient_elp8"></span></a></li>
                            <li class="clr"></li>
                         </ul>
                        </li>
                    </ul>
                  </div>
              </div>
            </form>
           </div>
       </fieldset>
        <?php
        $gradient_content = ob_get_contents();
        ob_end_clean();

        $this->controls['gradient'][$this->id . "_gradient"  ] = array(
            'type'              =>  'custom',
            'in_box'            =>   false ,
            'html'              =>  $gradient_content ,
            'value'             =>  isset( $values['gradient'] ) ? $values['gradient'] : '' ,
            'settings_type'     =>  "background_gradient",
            'control_type'      =>  "gradient" ,
            'is_style_setting'  =>  true ,
            'control_param'     =>  array(
                'options_selector'  => '.sed-gradient',
                'selected_class'    => 'gradient_select'
            ),
            
        );

    }

    function position( $value = 'static' ){

       $this->controls['position'] = array();

       $this->controls['position'][$this->id . "_position"  ] = array(
            'type'              => 'select',
            'value'             => $value ,
            'label'             => __('position', 'site-editor'),
            'desc'              => '<p><strong>position:</strong> Module position</p>',
            'options' =>array(
                'relative'     => __('relative', 'site-editor'),
                'absolute'     => __('absolute ', 'site-editor'),
                'fixed'     => __('fixed', 'site-editor'),
                'static'     => __('static ', 'site-editor')
            ),
            'settings_type'     =>  "position",
            'control_type'      =>  "sed_element" ,
            'style_props'       =>  "position" ,
            'is_style_setting'  =>  true ,
            
        );

    }

    function trancparency( $value = 100 ){

        $this->controls['trancparency'] = array();

        $this->controls['trancparency'][$this->id . "_trancparency"  ] = array(
            'type'              => 'spinner',
            'value'             => $value ,
            'label'             => __('transparency', 'site-editor'),
            'desc'              => '<p><strong>transparency:</strong></p>',
            'settings_type'     =>  "trancparency",
            'control_type'      =>  "spinner" ,
            'style_props'       =>  "opacity" ,
            'is_style_setting'  =>  true ,
            'control_param'     =>  array(
                'min'   =>  0 ,
                'max'     => 100,
            ),
            
        );

    }


    function text_shadow( $values = array() ){

        $this->controls['text_shadow'] = array();

        $this->controls['text_shadow'][$this->id . "_text_shadow_color"  ] = array(
            'type'              => 'color',
            'value'             => isset( $values['color'] ) ? $values['color'] : "transparent" ,
            'label'             => __('color', 'site-editor'),
            'desc'              => '<p><strong>text shadow color:</strong></p>',
            'settings_type'     =>  "text_shadow_color",
            'control_type'      =>  "color" ,
            //'style_props'       =>  "text-shadow-color" ,
            'is_style_setting'  =>  true ,
            
        );

        $text_shadow_control = $this->id . "_text_shadow";

        ob_start();
        ?>                                    <!-- sed_menu +  menu_bar_bg_position :::: shortcode name + control name -->

        <fieldset class="row_setting_box">
            <legend id="sed_pb_sed_image_image_settings">
               <a  href="javascript:void(0)" class="btn btn-default"  title="<?php echo __("text shadow" ,"site-editor");  ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="<?php echo $text_shadow_control ;?>_btn" role="button">
                  <span class="fa f-sed icon-textshadow fa-lg "></span>
                  <span class="el_txt"><?php echo __("text shadow" ,"site-editor");  ?></span>
               </a>
            </legend>

           <div id="sed-app-control-<?php echo $text_shadow_control ;?>"  class="dropdown">

               <form role="menu" class="dropdown-menu dropdown-common sed-dropdown sed-text-shadow" sed-shadow-cp-el="#text-shadow-colorpicker-button" sed-style-element="">
                  <div class="dropdown-content content">

                      <div>
                        <ul>
                            <li>
                            <a class="heading-item  first-heading-item" data-position="topLeft"  href="#"><?php echo __("no shadow" ,"site-editor");  ?></a>
                            </li>
                            <li>
                             <ul class=" text-shadow">
                                <li class="no-text-shadow"><a class="text-shadow-box" data-value="none" href="#"><span  class="style-text-shadow"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                             </ul>
                            </li>
                        </ul>
                      </div>
                      <div>
                        <ul>
                            <li>
                            <a class="heading-item" data-position="topLeft"  href="#"><?php echo __("Shadow" ,"site-editor");  ?></a>
                            </li>
                            <li>
                             <ul class=" text-shadow">
                                <li class="border-box-type1"><a class="text-shadow-box" data-value="0 0 5px" href="#"><span  class="style-text-shadow1 "><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                <li class="border-box-type2"><a class="text-shadow-box" data-value="0 0 14px" href="#"><span  class="style-text-shadow2"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                <li class="border-box-type1"><a class="text-shadow-box" data-value="2px 2px 5px" href="#"><span  class="style-text-shadow3"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                <li class="border-box-type2"><a class="text-shadow-box" data-value="2px -2px 5px" href="#"><span  class="style-text-shadow4"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                <li class="border-box-type1"><a class="text-shadow-box" data-value="-2px 2px 5px" href="#"><span  class="style-text-shadow5"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                <li class="border-box-type2"><a class="text-shadow-box" data-value="-2px -2px 5px" href="#"><span  class="style-text-shadow6"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                <li class="border-box-type1"><a class="text-shadow-box" data-value="0px 2px 5px " href="#"><span  class="style-text-shadow7"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                <li class="border-box-type2"><a class="text-shadow-box" data-value="0px -2px 5px " href="#"><span  class="style-text-shadow8"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                <li class="border-box-type1"><a class="text-shadow-box" data-value="0px 2px 5px " href="#"><span  class="style-text-shadow9"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                <li class="border-box-type2"><a class="text-shadow-box" data-value="0px -2px 5px" href="#"><span  class="style-text-shadow10"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                <li class="border-box-type3"><a class="text-shadow-box" data-value="2px 0px 5px" href="#"><span  class="style-text-shadow11"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                <li class="border-box-type4"><a class="text-shadow-box" data-value="-2px 0px 5px" href="#"><span  class="style-text-shadow12"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                             </ul>
                            </li>
                        </ul>
                      </div>
                      <div>
                        <ul>
                            <li>
                            <a class="heading-item" data-position="topLeft"  href="#"><?php echo __("Complex Shadow" ,"site-editor");  ?></a>
                            </li>
                            <li>
                             <ul class=" text-shadow">
                                <li class="border-box-type1"><a class="text-shadow-box" data-value="0px 0px 5px"  href="#"><span  class="style-text-shadow13 "><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                <li class="border-box-type2"><a class="text-shadow-box" data-value="0 0 14px"  href="#"><span  class="style-text-shadow14"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                <li class="border-box-type1"><a class="text-shadow-box" data-value="2px 2px 5px"  href="#"><span  class="style-text-shadow15"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                <li class="border-box-type2"><a class="text-shadow-box" data-value="2px -2px 5px"  href="#"><span  class="style-text-shadow16"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                <li class="border-box-type1"><a class="text-shadow-box" data-value="-2px 2px 5px"  href="#"><span  class="style-text-shadow17"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                <li class="border-box-type2"><a class="text-shadow-box" data-value="-2px -2px 5px"  href="#"><span  class="style-text-shadow18"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                <li class="border-box-type3"><a class="text-shadow-box" data-value="2px 0px 5px"  href="#"><span  class="style-text-shadow19"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                <li class="border-box-type4"><a class="text-shadow-box" data-value="-2px 0px 5px"  href="#"><span  class="style-text-shadow20"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                             </ul>
                            </li>
                        </ul>
                      </div>
                  </div>
                </form>
            </div>
         </fieldset>
        <?php
        $text_shadow_content = ob_get_contents();
        ob_end_clean();

        $this->controls['text_shadow'][$this->id . "_text_shadow"  ] = array(
            'type'              =>  'custom',
            'in_box'            =>   false ,
            'html'              =>  $text_shadow_content ,
            'value'             => isset( $values['shadow'] ) ? $values['shadow'] : 'none' ,
            'settings_type'     =>  "text_shadow",
            'control_type'      =>  "dropdown" ,
            //'style_props'       =>  "text-shadow" ,
            'is_style_setting'  =>  true ,
            'control_param'     =>  array(
                'options_selector'  => '.text-shadow-box',
                'selected_class'      => 'text-shadow-active' ,
            ),
            
        );

    }

    function text_align( $value = 'left' ){

        $this->controls['text_align'] = array();

        $this->controls['text_align'][$this->id . "_text_align"  ] = array(
            'type' => 'select',
            'value' => $value ,
            'label' => __('Align', 'site-editor'),
            'desc' => '<p><strong>Align:</strong> Module Align</p>',
            'options' =>array(
                'left'      => ( is_rtl() ) ? __('Right', 'site-editor') : __('Left', 'site-editor'),
                'center'    => __('Center', 'site-editor'),
                'right'     => ( is_rtl() ) ? __('Left', 'site-editor') : __('Right', 'site-editor'),
                'justify'   => __('justify', 'site-editor'),
            ),
            'settings_type'     =>  "text_align",
            'control_type'      =>  "sed_element" ,
            'style_props'       =>  "text-align" ,
            'is_style_setting'  =>  true ,
        );
    }

    function font( $values = array() ){

       global $sed_apps;
       $sed_apps->typography->set_fonts();

       $fonts = array();
       if( !empty( $sed_apps->typography->custom_fonts ) ){
           $fonts["custom_fonts"] = $sed_apps->typography->custom_fonts;
       }

       $fonts["standard_fonts"] = $sed_apps->typography->standard_fonts;

       $fonts["google_fonts"]   = $sed_apps->typography->google_fonts;

       $this->controls['font'] = array();

       $this->controls['font'][$this->id . "_font_family"  ] = array(
  			'type' => 'select',
            'value' => isset( $values['family'] ) ? $values['family'] : 'arial' ,
  			'label' => __('Font Family', 'site-editor'),
  			'desc' => '<p><strong>Font Family:</strong></p>',
            'options' =>    $fonts,
            'optgroup'          => true ,
            'groups'            => array(
                "custom_fonts"          => __("Custom Fonts" , "site-editor") ,
                "standard_fonts"        => __("Standard Fonts" , "site-editor") ,
                "google_fonts"          => __("Google Fonts" , "site-editor") ,
            ),
            'settings_type'     =>  "font_family",
            'control_type'      =>  "sed_element" ,
            'style_props'       =>  "font-family" ,
            'is_style_setting'  =>  true ,
  		);

        $this->controls['font'][$this->id . "_font_size"  ] = array(
            'type'              => 'spinner',
            'value'             => isset( $values['size'] ) ? $values['size'] : 12 ,
            'label'             => __('font size', 'site-editor'),
            'desc'              => '<p><strong>font size:</strong></p>',
            'settings_type'     =>  "font_size",
            'control_type'      =>  "spinner" ,
            'style_props'       =>  "font-size" ,
            'is_style_setting'  =>  true ,
            
        );

        $this->controls['font'][$this->id . "_font_color"  ] = array(
            'type'              => 'color',
            'value'             => isset( $values['color'] ) ? $values['color'] : "#000000" ,
            'label'             => __('color', 'site-editor'),
            'desc'              => '<p><strong>font color:</strong></p>',
            'settings_type'     =>  "font_color",
            'control_type'      =>  "color" ,
            'style_props'       =>  "color" ,
            'is_style_setting'  =>  true ,
            
        );

        $this->controls['font'][$this->id . "_font_weight"  ] = array(
  			'type' => 'select',
            'value' => isset( $values['weight'] ) ? $values['weight'] : "normal" ,
  			'label' => __('Font Weight', 'site-editor'),
  			'desc' => '<p><strong>Font Weight:</strong></p>',
            'options' =>array(
                'normal'        => __('normal', 'site-editor'),
                'bold'          => __('bold', 'site-editor') ,
                'bolder'        => __('bolder', 'site-editor'),
                'lighter'       => __('lighter', 'site-editor') ,
                100             => 100,
                200             => 200 ,
                300             => 300,
                400             => 400 ,
                500             => 500,
                600             => 600 ,
                700             => 700,
                800             => 800 ,
                900             => 900 ,
            ),
            'settings_type'     =>  "font_weight",
            'control_type'      =>  "sed_element" ,
            'style_props'       =>  "font-weight" ,
            'is_style_setting'  =>  true ,
  		);

        $this->controls['font'][$this->id . "_font_style"  ] = array(
  			'type' => 'select',
            'value' => isset( $values['style'] ) ? $values['style'] : "normal" ,
  			'label' => __('Font Style', 'site-editor'),
  			'desc' => '<p><strong>Font Style:</strong></p>',
            'options' =>array(
                'normal'      => __('normal', 'site-editor'),
                'oblique'    => __('oblique', 'site-editor'),
                'italic'    => __('italic', 'site-editor'),
            ),
            'settings_type'     =>  "font_style",
            'control_type'      =>  "sed_element" ,
            'style_props'       =>  "font-style" ,
            'is_style_setting'  =>  true ,
  		);

        $this->controls['font'][$this->id . "_text_decoration"  ] = array(
  			'type' => 'select',
            'value' => isset( $values['text_decoration'] ) ? $values['text_decoration'] : "none" ,
  			'label' => __('text Decoration', 'site-editor'),
  			'desc' => '<p><strong>Font Family:</strong></p>',
            'options' =>array(
                'none'              => __('none', 'site-editor'),
                'underline'         => __('underline', 'site-editor') ,
                'line-through'      => __('line-through', 'site-editor')
            ),
            'settings_type'     =>  "text_decoration",
            'control_type'      =>  "sed_element" ,
            'style_props'       =>  "text-decoration" ,
            'is_style_setting'  =>  true ,
  		);

    }

    function line_height( $value = 100 ){


        $this->controls['line_height'] = array();

        $this->controls['line_height'][$this->id . "_line_height"  ] = array(
            'type'              => 'spinner',
            'value'             => $value ,
            'label'             => __('line height', 'site-editor'),
            'desc'              => '<p><strong>line height:</strong></p>',
            'settings_type'     =>  "line_height",
            'control_type'      =>  "spinner" ,
            'style_props'       =>  "line-height" ,
            'is_style_setting'  =>  true ,
            
            
        );
    }

    function transform( $values = array() ){

    }

    function transition( $values = array() ){

    }


}
