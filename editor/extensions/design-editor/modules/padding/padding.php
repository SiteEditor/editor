<?php
/**
 * Module Name: Padding
 * Module URI: http://www.siteeditor.org/design-editor/padding
 * Description: Padding Module For Design Editor
 * Author: Site Editor Team
 * Author URI: http://www.siteeditor.org
 * @since 1.0.0
 * @package SiteEditor
 * @category designEditor
 */

/**
 * Class SedDesignEditorPadding
 */
final class SedDesignEditorPadding {

    /**
     * Capability required to access padding fields
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * Padding fields option group
     *
     * @access private
     * @var array
     */
    private $option_group = 'padding';

    /**
     * This group title
     *
     * @access public
     * @var array
     */
    public $title = '';

    /**
     * this group description
     *
     * @access public
     * @var array
     */
    public $description = '';

    /**
     * prefix for controls ids for prevent conflict
     *
     * @var string
     * @access public
     */
    public $control_prefix = 'sed_padding';

    /**
     * SedDesignEditorPadding constructor.
     */
    public function __construct(){

        $this->title = __("Padding" , "site-editor");

        $this->description = __("Add padding To each dom element" , "site-editor");

        add_action( "sed_app_register"          , array( $this , 'register_group' ) , -9999 );

        add_action( "sed_app_register"          , array( $this , 'register_options' ) );

        add_action( "sed_after_init_manager"    , array( $this , 'register_components' ) , 100 , 1 );

    }

    /**
     * Register Controls && Fields
     *
     * @access public
     * @since 1.0.0
     */
    public function register_components(){

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-padding-top-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-padding-top-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-padding-right-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-padding-right-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-padding-bottom-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-padding-bottom-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-padding-left-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-padding-left-field.class.php';

    }

    /**
     * Register Default Padding Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_group(){

        SED()->editor->manager->add_group( $this->option_group , array(
            'capability'        => $this->capability,
            'theme_supports'    => '',
            'title'             => $this->title ,
            'description'       => $this->description ,
            'type'              => 'default',
        ));

    }

    /**
     * Register Options For Padding Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_options(){

        $panels = array();

        /*$lock_id = "sed_pb_".$this->id."_padding_lock";

        $spinner_class = 'sed-padding-spinner-' . $this->id;
        $spinner_class_selector = '.' . $spinner_class;
        $sh_name = $this->id;
        $sh_name_c = $sh_name. "_padding_";

        $controls = array( $sh_name_c . "top" , $sh_name_c . "right" , $sh_name_c . "left" , $sh_name_c . "bottom" );

        $this->controls['padding'] = array();*/

        $fields = array(

            'padding_top' => array(
                "type"              => "padding-top" ,
                "label"             => __('Top', 'site-editor'),
                "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
                /*'atts'  => array(
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
                ),*/           
            ),

            'padding_right' => array(
                "type"              => "padding-right" ,
                "label"             => ( is_rtl() ) ? __('Right', 'site-editor') : __('Left', 'site-editor'),
                "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
                /*'atts'  => array(
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
                ),*/           
            ),

            'padding_bottom' => array(
                "type"              => "padding-bottom" ,
                "label"             => __('Bottom', 'site-editor'),
                "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
                /*'atts'  => array(
                    "class" =>   $spinner_class
                ) ,
                'control_param'     =>  array(
                    'lock'    => array(
                        'id'       => $lock_id,
                        'spinner'  => $spinner_class_selector,
                        'controls' => array( $sh_name_c . "top" , $sh_name_c . "right" , $sh_name_c . "left" )
                    ),
                    'min'   =>  0 ,
                    
                    //'max'     => 100,
                    //'step'    => 2,
                    //'page'    => 5
                ),*/           
            ),

            'padding_left' => array(
                "type"              => "padding-left" ,
                "label"             => ( is_rtl() ) ? __('Left', 'site-editor') : __('Right', 'site-editor'),
                "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
                /*'atts'  => array(
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
                ),*/           
            ),

        );


        $fields = apply_filters( 'sed_padding_options_fields_filter' , $fields );

        $panels = apply_filters( 'sed_padding_options_panels_filter' , $panels );

        $new_options = sed_options()->fix_controls_panels_ids( $fields , $panels , $this->control_prefix );

        $new_params = $new_options['fields'];

        $new_panels = $new_options['panels'];

        sed_options()->add_fields( $new_params );

        sed_options()->add_panels( $new_panels );

    }

}

new SedDesignEditorPadding();
