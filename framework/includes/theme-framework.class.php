<?php
Class SiteEditorThemeFramework{

    /**
     * SiteEditorThemeFramework constructor.
     */
	public function __construct( ){

        //not call in condition if
        add_filter( 'sed_page_options_panels_filter' , array( $this , 'register_page_panels' ) );

        add_filter( 'sed_page_options_fields_filter' , array( $this , 'register_page_fields' ) );

        //do_action( 'activated_plugin', $plugin, $network_wide );
        //add_action("after_switch_theme", "mytheme_do_something");
        /*register_activation_hook( __FILE__, 'my_plugin_activation' );
        function my_plugin_activation() {
            add_option( 'my_plugin_activated', time() );
        }*/

        add_filter( 'admin_init' , array( $this , 'save_default_page_options' ) );

	}

    public function save_default_page_options(){

        $fields = apply_filters( 'sed_page_options_fields_filter' , array() );

        $default_values = array();

        foreach( $fields  AS $key => $field ){
            if( isset( $field['setting_id'] ) ) {
                $default_values[$field['setting_id']] = isset($field['default']) ? $field['default'] : '';
            }
        }

        $this->save_default_options( 'default_page_options' , $default_values );

    }

    /**
     * @param $option_name
     * @param $key
     * @param $value
     * @return bool
     */
    public function save_default_option( $option_name , $key , $value ){

        do_action('sed_default_option_before_save', $option_name , $key , $value );

        $value = apply_filters('sed_default_option_before_save', $value , $key , $option_name );

        $data = $this->get_default_options( $option_name );

        $data[$key] = $value;

        $result = update_option( $option_name , $data );

        do_action('sed_default_option_after_save', $option_name , $key , $value , $result );

        return $result;
    }

    /**
     * Save default options in self option or new option
     *
     * @param $option_name string
     * @param $data array
     * @return mixed
     */
    public function save_default_options( $option_name , $data ){

        if( empty($data) )
            return;

        do_action('sed_default_options_before_save', $option_name , $data );

        $data = apply_filters('sed_default_options_before_save', $data , $option_name );

        $result = update_option( $option_name , $data );

        do_action('sed_default_options_after_save', $option_name , $data , $result );

        return $result;
    }

    /**
     * @param $option_name
     * @param $key
     * @return mixed|void
     */
    public function get_default_option( $option_name , $key ) {

        $default_values = $this->get_default_options( $option_name );

        if ( isset( $default_values[$key] ) ) {

            return apply_filters( "sed_get_default_option_value", $default_values[$key] , $key , $option_name );
        }

        return apply_filters( "sed_get_default_option_value", null , $key , $option_name );
    }

    /**
     * @param $option_name
     * @return mixed|void
     */
    public function get_default_options( $option_name ){

        $values = get_option( $option_name );

        if ( $values === false ) {

            $values = array();
            // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
            $deprecated = null;
            $autoload = 'yes';

            $result = add_option( $option_name , $values , $deprecated, $autoload );

        }

        return apply_filters( "sed_get_default_options_values", $values , $option_name );
    }

    /**
     * @param $setting_id
     * @param $sed_page_id
     * @param $sed_page_type
     * @return mixed|void
     */
    public function get_page_setting( $setting_id , $sed_page_id , $sed_page_type ){

        if( $sed_page_type == "post" ){

            if( ! in_array( $setting_id , get_post_custom_keys( $sed_page_id ) ) ) {
                $default = $this->get_default_option( 'default_page_options' , $setting_id );
                $value = $default;
            }else {
                $value = get_post_meta($sed_page_id, $setting_id, true);
            }

        }else{

            $default = $this->get_default_option( 'default_page_options' , $setting_id );

            $option_name = 'sed_'. $sed_page_id .'_settings';

            $option_values = get_option( $option_name );

            $value = ( is_array( $option_values ) && isset( $option_values[$setting_id] ) ) ? $option_values[$setting_id] : $default;

        }

        return $value;
    }

    public function get_current_page_setting( $setting_id , $sed_page_id , $sed_page_type ){

        $scope = "public-scope";

        switch ( $scope ){
            case "public-scope" :

                break;
            case "layout-scope" :

                break;
            case "page-customize-scope" :
                $value = $this->get_page_setting( $setting_id , $sed_page_id , $sed_page_type );
                break;
        }

        return $value;

    }

    public function register_page_panels( $panels ){

        $panels = array_merge( $panels , array(

            'general_page_style' => array(
                'title'         =>  __('Page General Style',"site-editor")  ,
                'capability'    => 'edit_theme_options' ,
                'type'          => 'inner_box' ,
                'description'   => '' ,
                'priority'      => 9 ,
            )

        ));

        return $panels;

    }

    public function register_page_fields( $fields ){

        $fields = array_merge( $fields , array(

            'page_sheet_width' => array(
                'setting_id'        => 'sheet_width',
                "type"              => "dimension" ,
                "label"             => __("Sheet Width", "site-editor"),
                'default'           => "1100px",
                //'after_field'       => "px" ,
                "description"              => __("This option allows you to set a title for your image.", "site-editor"),
                'transport'         => 'postMessage' ,
                'priority'          => 11 ,
                'dependency' => array(
                    'controls'  =>  array(
                        "control"   => "page_length" ,
                        "value"     => "wide" , //value with @string , values with @array
                    )
                )
            ),

            'page_length' => array(
                'setting_id'        => "page_length" ,
                "type"              => "select" ,
                "label"             => __("Page Length", "site-editor"),
                "description"       => __("This option allows you to set a title for your image.", "site-editor"),
                'default'           => 'wide',
                "choices"       =>  array(
                    "wide"          =>    __( "Wide" , "site-editor" ) ,
                    "boxed"         =>    __( "Boxed" , "site-editor" ) ,
                ),
                //'panel'             => 'general_page_style' ,
                'transport'         => 'postMessage' ,
                'priority'          => 9 ,
            ),

            'change_image_panel' => array(
                'setting_id'        => "page_background" ,
                "type"              => "image" ,
                "label"             => __("Background Image", "site-editor"),
                "description"       => __("This option allows you to set a title for your image.", "site-editor"),
                'remove_btn'        => true ,
                'panel'             => 'general_page_style' ,
                'default'           => '',
                'transport'         => 'postMessage' ,
                'priority'          => 8 ,
            )

        ));

        return $fields;

    }

    public function get_theme_options(){

    }

    public function get_site_options(){

    }

    public function get_content_options( ){

    }

}

new SiteEditorThemeFramework;


 /*       $panels = array();

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

        echo $cr_settings;*/


/*
All Settings Group for Theme Builder :
1. site settings :
    @like per page , site title , tagline , Front page displays , site description , favicon , custom css , ...
    @save in any options && theme mode
    @not need to scope && preset
    @sample :

    $site_settings = array(

        "post_per_page"  => array(
            'default'        => get_option( 'post_per_page' ),
            'capability'     => 'manage_options',
            'option_type'    => 'option' ,
            'transport'      => 'postMessage'
        ) ,

        "site_description"  => array(
            'default'        => get_option( 'site_description' ),
            'capability'     => 'manage_options',
            'option_type'    => 'option' ,
            'transport'      => 'postMessage'
        ) ,

    );

2. page settings :
    @like page sheet width , page length , backgound , ...
    @save in sed_post_settings post meta
    @sed_post_settings model example :
    $sed_post_settings_model = array(
        "sheet_width"       =>  1100 ,
        "page_length"       =>  "wide"
    );
    @in this settings not allowed using theme mode or options settings
    @this type setting is base
    @public model save in sed_general_page_options
    @sed_general_page_options model example :
    $sed_general_page_options_model = array(
        'page'  =>  array(
            "sheet_width"       =>  1100 ,
            "page_length"       =>  "wide"
        ),
        'post'  =>  array(
            "sheet_width"       =>  1100 ,
            "page_length"       =>  "wide"
        ),
        ...
    );

3. content setting


4. sub theme models ------------- sed_layouts_models
    array(
        'single_post'   =>   array(
            array(
              'order'         =>    10 ,
              'theme_id'      =>    'theme_id_5' ,
              'after_content' =>    true ,
              'main_row'      =>    true
            ),
            array(
              'order'         =>    7 ,
              'theme_id'      =>    'theme_id_7' ,
              'after_content' =>    false ,
              'exclude'       =>    array()
            )
        ),
        'archive'   =>   array(
            array(
              'order'         =>    2 ,
              'theme_id'      =>    'theme_id_5' ,
              'after_content' =>    true ,
            ),
            array(
              'order'         =>    3 ,
              'theme_id'      =>    'theme_id_7' ,
              'after_content' =>    true ,
            )
        ),
        'page'   =>   array(

        ),

    )

5. sub theme models content -------- sed_layouts_content
    array(
      'theme_id_1' =>
          array (
              array (
                'parent_id' => string 'root' (length=4)
                'tag' => string 'sed_row' (length=7)
                'id' => string 'sed-bp-module-row-4-1' (length=21)
                'attrs' => array(),
              ),
              array (
                'parent_id' => string 'root' (length=4)
                'tag' => string 'sed_row' (length=7)
                'id' => string 'sed-bp-module-row-4-1' (length=21)
                'attrs' => array(),
              )
          )
      'theme_id_2' =>
          array (
              array (
                'parent_id' => string 'root' (length=4)
                'tag' => string 'sed_row' (length=7)
                'id' => string 'sed-bp-module-row-4-1' (length=21)
                'attrs' => array(),
              ),
              array (
                'parent_id' => string 'root' (length=4)
                'tag' => string 'sed_row' (length=7)
                'id' => string 'sed-bp-module-row-4-1' (length=21)
                'attrs' => array(),
              )
          )
    )

Shortcode Module Presets
  1. create post type sed_preset
  2. create taxonomy sed_preset_category
  3. example preset for image module :
      preset title : Image preset 1 ,
      preset slug : image-preset-1 ,
      preset category : "image" , //module name
      preset content model : json_encode( array(
          shortcode model 1 ,
          shortcode model 2 ,
          ...
      ));
      preset content : '[sed_image alt="" attachment_id="10" title=""][/sed_image]';

Shortcode page content template
  1. create post type sed_template
  2. create taxonomy sed_template_category
  3. example template :
      template title : template 1 ,
      template slug : template-1 ,
      template category : "landing page" , //module name
      template content model : json_encode( array(
          shortcode model 1 ,
          shortcode model 2 ,
          ...
      ));
      template content : '[sed_image alt="" attachment_id="10" title=""][/sed_image]';

*/



























