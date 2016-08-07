<?php
/**
 * SiteEditor Options Manager classes
 *
 * @package SiteEditor
 * @subpackage Options
 * @since 1.0.0
 */

/**
 * SiteEditor Options Manager class.
 *
 * Manage all SiteEditor Application Options
 *
 * Serves as a factory for Fields and Settings and Controls, and
 * instantiates default Fields and Settings and Controls.
 *
 * @since 1.0.0
 */

class SiteEditorOptionsManager{

    /**
     * An array containing all fields.
     *
     * @static
     * @access public
     * @var array
     */
    public static $fields   = array();

    /**
     * An array containing all panels.
     *
     * @static
     * @access public
     * @var array
     */
    public static $panels   = array();

    /**
     * An array containing all sections.
     *
     * @static
     * @access public
     * @var array
     */
    public static $groups = array();

    /**
     * SiteEditorOptionsManager constructor.
     */
    function __construct(  ) {

        add_action( 'customize_register', array( $this, 'register_control_types' ) );
        add_action( 'customize_register', array( $this, 'add_panels' ), 97 );
        add_action( 'customize_register', array( $this, 'add_sections' ), 98 );
        add_action( 'customize_register', array( $this, 'add_fields' ), 99 );

        add_action( 'site_editor_ajax_sed_load_options', array($this,'sed_ajax_load_options' ) );//wp_ajax_sed_load_options


    }

    function sed_ajax_load_options(){

        do_action( "sed_load_options_" . $_POST['group_id'] );

        $options = self::get_options( $_POST['group_id'] );

        die( wp_json_encode( array(
            'success' => true,
            'data'    => $options,
        ) ) );

    }

    function get_options( $group_id ){

        return array(
            "settings"      =>  $settings ,
            "controls"      =>  $controls ,
            "panels"        =>  $panels ,
            "dependencies"  =>  $dependencies
        );
    }

    /**
     * Create a new panel.
     *
     * @static
     * @access public
     * @param string $id   The ID for this panel.
     * @param array  $args The panel arguments.
     */
    public static function add_panel( $id = '', $args = array() ) {

        $args['id']          = esc_attr( $id );
        $args['description'] = ( isset( $args['description'] ) ) ? esc_textarea( $args['description'] ) : '';
        $args['priority']    = ( isset( $args['priority'] ) ) ? esc_attr( $args['priority'] ) : 10;
        $args['type']        = ( isset( $args['type'] ) ) ? $args['type'] : 'default';
        if ( ! isset( $args['active_callback'] ) ) {
            $args['active_callback'] = ( isset( $args['required'] ) ) ? array( 'Kirki_Active_Callback', 'evaluate' ) : '__return_true';
        }

        self::$panels[ $args['id'] ] = $args;

    }

    public static function add_panels( $panels = array() ){

        if( !empty( $panels ) && is_array( $panels ) ) {

            foreach ( $panels AS $panel_id => $args ) {
                self::add_panel( $panel_id , $args );
            }

        }

    }

    public static function add_group(  $id = '', $args = array() ){

    }

    public static function add_groups( $groups = array() ){

    }

    public static function add_field(  $id = '', $args = array() ){

    }

    public static function add_fields( $groups = array() ){

    }

}


function register_options_groups()
{

    sed_options()->add_group($group_id, array(

        'title' => __('My Title', 'textdomain'),
        'description' => __('My Description', 'textdomain'),

    ));

    $groups = array(

        $group_id1 => array(

            'title' => __('My Title', 'textdomain'),
            'description' => __('My Description', 'textdomain'),
        ),

        $group_id2 => array(

            'title' => __('My Title', 'textdomain'),
            'description' => __('My Description', 'textdomain'),
        ),

    );

    sed_options()->add_groups( $groups );
}

add_action("sed_app_register", "register_options_groups");


function register_params( )
{

    sed_options()->add_panel('panel_id', array(
        'priority' => 10,
        'type' => 'inner_panel',
        'title' => __('My Title', 'textdomain'),
        'description' => __('My Description', 'textdomain'),
        'group' => 'group_id'
    ));


    sed_options()->add_panels($panel_ids);


    sed_options()->add_field('field_id', array(
        'settings' => 'my_setting',
        'label' => __('My custom control', 'translation_domain'),
        'section' => 'my_section',
        'type' => 'text',
        'priority' => 10,
        'default' => 'some-default-value',
        //panel or group
        'panel' => 'panel_id'
    ));

    sed_options()->add_fields($fields);
}

add_action( "sed_load_options_group_id1" , "register_params" );




function sed_options(){
    return SED()->editor->options;
}