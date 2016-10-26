<?php

/**
 * SiteEditor Options Dependency Class
 *
 * Implements Options Dependencies management in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class SiteEditorDependencyManager
 * @description : Options Dependency For SiteEditor Application.
 */
class SiteEditorOptionsDependencyManager{

    /**
     * Array of all registered dependencies
     *
     * @since 1.0.0
     * @access public
     * @var array
     */
    protected $dependencies = array();

    /**
     * Array of all registered dependencies
     *
     * @since 1.0.0
     * @access public
     * @var array
     */
    public $registered_types = array();

    /**
     * SiteEditorOptionsDependency constructor.
     */
    public function __construct( ) {

        require_once dirname( __FILE__ ) . '/dependency/site-editor-options-dependency.class.php';

        $this->registered_types[ 'query' ] = 'SiteEditorOptionsDependency';

        require_once dirname( __FILE__ ) . '/dependency/site-editor-options-callback-dependency.class.php';

        $this->registered_types[ 'callback' ] = 'SiteEditorOptionsCallbackDependency';

        add_action( 'sed_print_footer_scripts' , array($this, 'print_settings_dependencies') , 10000 );

        add_action( 'wp_footer' , array($this, 'print_page_conditions') );

        add_action( 'wp_default_scripts' , array( $this, 'register_scripts' ), 11 );

        add_action( 'sed_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

    }

    /**
     * Get the registered controls.
     *
     * @since 1.0.0
     *
     * @param $validate boolean
     * @return array
     */
    public function dependencies( $validate = false ) {

        if( $validate === true ) {

            $dependencies = array();

            foreach ( $this->dependencies AS $group => $group_options) {

                if (!isset($dependencies[$group])) {
                    $dependencies[$group] = array();
                }

                foreach ($group_options AS $id => $dependency) {

                    if ( $dependency->is_valid() ) {

                        $dependencies[$group][$id] = $dependency->json();

                    }

                }

            }

            return $dependencies;

        }

        return $this->dependencies;
    }

    /**
     * Add a options Dependency.
     *
     * @since 1.0.0
     *
     * @param $group string
     * @param SiteEditorOptionsDependency|string $id   Dependency object, or ID.
     * @param array                       $args Control arguments; passed to SiteEditorOptionsDependency
     *                                          constructor.
     * @return object ( instance of SiteEditorOptionsDependency or extends )
     */
    public function add( $group , $id , $args = array() ) {

        if ( $id instanceof SiteEditorOptionsDependency ) {
            $dependency = $id;
        } else {

            if ( ! isset( $args['type'] ) || ! array_key_exists( $args['type'], $this->registered_types ) ) {
                $args['type'] = 'query';
            }

            $constructor = $this->registered_types[ $args['type'] ];

            $dependency = new $constructor( $this, $id, $args );

        }

        if( !isset( $this->dependencies[$group] ) ){

            $this->dependencies[$group] = array();

        }

        $this->dependencies[$group][ $dependency->id ] = $dependency;

        return $dependency;
    }

    /**
     * Retrieve a Dependency.
     *
     * @since 1.0.0
     *
     * @param string $group
     * @param string $id ID of the control.
     * @return WP_Customize_Control $control The control object.
     */
    public function get( $group , $id ) {

        if ( isset( $this->dependencies[ $group ] ) && isset( $this->dependencies[ $group ][ $id ] ) ) {
            return $this->dependencies[ $group ][$id];
        }

    }

    /**
     * Remove a Dependency
     *
     * @since 1.0.0
     *
     * @param string $group
     * @param string $id ID of the control.
     */
    public function remove( $group , $id ) {

        if ( isset( $this->dependencies[ $group ] ) && isset( $this->dependencies[ $group ][ $id ] ) ) {
            unset( $this->dependencies[ $group ][ $id ] );
        }
    }

    /**
     * Gather the parameters passed to client JavaScript via JSON.
     *
     * @since 1.0.0
     *
     * @return array The array of all supported operators
     */
    public function get_operators( ) {

        $operators = array(
            '=', '==', '===', '!=', '!==', '>', '>=', '<', '<=',
            'IN', 'NOT IN',
            'BETWEEN', 'NOT BETWEEN',
            'DEFINED', 'UNDEFINED',
            'REGEXP', 'NOT REGEXP' ,
            'EMPTY' , 'NOT EMPTY'
        );

        return apply_filters( 'sed_dependency_operators' , $operators );

    }

    public function get_page_conditions( ) {

        $conditions = array(
            'is_home'                   => is_home() ,
            'is_page'                   => is_page() ,
            'is_single'                 => is_single() ,
            'is_singular'               => is_singular() ,
            'is_post'                   => is_singular('post') ,
            'is_front_page'             => is_front_page() ,
            'is_sticky'                 => is_sticky() ,
            'is_post_type_archive'      => is_post_type_archive() ,
            'is_tax'                    => is_tax() ,
            'is_tag'                    => is_tag() ,
            'is_category'               => is_category() ,
            'is_archive'                => is_archive() ,
            'is_author'                 => is_author() ,
            'is_search'                 => is_search() ,
            'is_404'                    => is_404()  ,
            'is_attachment'             => is_attachment()  ,
            'is_rtl'                    => is_rtl()  ,
        );

        if( is_singular() ){

            $conditions['comments_open'] = comments_open();

            $conditions['pings_open'] = pings_open();

        }

        if( is_page() ){

            $conditions['is_page_template'] = is_page_template();

        }

        if( is_archive() ){

            $conditions['is_date']      = is_date();
            $conditions['is_year']      = is_year();
            $conditions['is_month']     = is_month();
            $conditions['is_day']       = is_day();
            $conditions['is_time']      = is_time();
            $conditions['is_new_day']   = is_new_day();

        }

        return apply_filters( 'sed_dependency_page_conditions' , $conditions );

    }

    public function fix_dependency_controls_ids( $dependency , $prefix ){

        if( is_array( $dependency ) && isset( $dependency['queries'] ) &&  is_array( $dependency['queries'] ) ){
            $dependency['queries'] = SiteEditorOptionsDependency::fix_controls_ids( $dependency['queries'] , $prefix  );
        }

        if( is_array( $dependency ) && isset( $dependency['controls'] ) &&  is_array( $dependency['controls'] ) ){

            foreach ( $dependency['controls'] AS $key => $control ){

                if( is_string( $control ) ) {

                    $dependency['controls'][$key] = $prefix . "_" . $control;

                }
            }

        }

        return $dependency;
    }

    public function print_settings_dependencies(){

        $dependencies = $this->dependencies( true );

        $operators = $this->get_operators();

        ?>
        <script type="text/javascript">

            var _sedAppModulesSettingsRelations = <?php echo wp_json_encode( $dependencies ); ?>;

            var _sedAppDependenciesOperators = <?php echo wp_json_encode( $operators ); ?>;

        </script>
        <?php
    }

    public function print_page_conditions(){

        $conditions = $this->get_page_conditions();

        ?>
        <script type="text/javascript">

            var _sedAppDependenciesPageConditions = <?php echo wp_json_encode( $conditions ); ?>;

        </script>
        <?php

    }

    /**
     * Register scripts for Customize Posts.
     *
     * @param WP_Scripts $wp_scripts Scripts.
     */
    public function register_scripts( WP_Scripts $wp_scripts ){

        $suffix     = (SCRIPT_DEBUG ? '' : '.min') . '.js';

        $handle     = 'sed-dependency-plugin';
        $src        = SED_EXT_URL . 'options-engine/assets/js/dependency-plugin' . $suffix;
        $deps       = array('siteeditor');
        $in_footer  = 1;

        $wp_scripts->add($handle, $src, $deps, SED_VERSION, $in_footer);

    }

    public function enqueue_scripts(){

        wp_enqueue_script( 'sed-dependency-plugin' );

    }

}
