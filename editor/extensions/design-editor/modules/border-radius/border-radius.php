<?php
/**
 * Module Name: Border Radius
 * Module URI: http://www.siteeditor.org/design-editor/border-radius
 * Description: Border Radius Module For Design Editor
 * Author: Site Editor Team
 * Author URI: http://www.siteeditor.org
 * @since 1.0.0
 * @package SiteEditor
 * @category designEditor
 */

/**
 * Class SedDesignEditorBorderRadius
 */
final class SedDesignEditorBorderRadius {

    /**
     * Capability required to access border-radius fields
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * Border Radius fields option group
     *
     * @access private
     * @var array
     */
    private $option_group = 'border-radius';

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
    public $control_prefix = 'sed_border_radius';

    /**
     * SedDesignEditorBorder Radius constructor.
     */
    public function __construct(){

        $this->title = __("Border Radius" , "site-editor");

        $this->description = __("Add border-radius To each dom element" , "site-editor");

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

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-radius-tl-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-radius-tl-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-radius-tr-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-radius-tr-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-radius-bl-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-radius-bl-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-radius-br-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-radius-br-field.class.php'; 

    }

    /**
     * Register Default Border Radius Group
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
     * Register Options For Border Radius Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_options(){

        $panels = array();

        //$lock_id = "sed_pb_".$this->id."_border_radius_lock";

        //$spinner_class = 'sed-border-radius-spinner-' . $this->id;    //shortcode_name
        //$spinner_class_selector = '.' . $spinner_class;
        //$sh_name = $this->id;
        //$sh_name_c = $sh_name. "_border_radius_";

        //$controls = array( $sh_name_c . "tr" , $sh_name_c . "tl" , $sh_name_c . "br" , $sh_name_c . "bl" );

        $fields = array(

            'border_radius_tl' => array(
                "type"              => "border-radius-tl" ,
                "label"             => "juh",//( is_rtl() ) ? __('Top left corner', 'site-editor') : __('Top right corner', 'site-editor') ,
                "description"       => __("Add corner For Element", "site-editor"),
                'atts'  => array(
                    "class" =>   $spinner_class
                ) ,
                'js_params'     =>  array(
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
            
            ),

/*            'border_radius_tr' => array(
                "type"              => "border-radius-tr" ,
                "label"             => ( is_rtl() ) ? __('Top Right corner', 'site-editor') : __('Top left corner', 'site-editor') ,
                "description"       => __("Add corner For Element", "site-editor"),
                'atts'  => array(
                    "class" =>   $spinner_class
                ) ,
                'js_params'     =>  array(
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
            
            ),


            'border_radius_br' => array(
                "type"              => "border-radius-br" ,
                "label"             => ( is_rtl() ) ? __('Bottom Right corner', 'site-editor') : __('Bottom left corner', 'site-editor') ,
                "description"       => __("Add corner For Element", "site-editor"),
                'atts'  => array(
                    "class" =>   $spinner_class
                ) ,
                'js_params'     =>  array(
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
            
            ),

            'border_radius_bl' => array(
                "type"              => "border-radius-bl" ,
                "label"             => ( is_rtl() ) ? __('Bottom left corner', 'site-editor') : __('Bottom right corner', 'site-editor') ,
                "description"       => __("Add corner For Element", "site-editor"),
                'atts'  => array(
                    "class" =>   $spinner_class
                ) ,  
                'js_params'     =>  array(
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
            
            ),

*/
        );

        $fields = apply_filters( 'sed_border_radius_options_fields_filter' , $fields );

        $panels = apply_filters( 'sed_border_radius_options_panels_filter' , $panels );

        $new_options = sed_options()->fix_controls_panels_ids( $fields , $panels , $this->control_prefix );

        $new_params = $new_options['fields'];

        $new_panels = $new_options['panels'];

        sed_options()->add_fields( $new_params );

        sed_options()->add_panels( $new_panels );

    }

}

new SedDesignEditorBorderRadius();
