<?php

/**
 * SiteEditor Options Dependency Class
 *
 * Implements Controls && Panel Dependencies management in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class SiteEditorOptionsDependency
 * @description : Options Dependency For SiteEditor Application.
 */
class SiteEditorOptionsDependency{

    /**
     * The dependency type Default is queries
     *
     * @since 1.0.0
     * @access public
     * @var string
     */
    public $type = "query";

    /**
     * WP_Customize_Manager instance.
     *
     * @since 4.0.0
     * @access public
     * @var WP_Customize_Manager
     */
    public $manager;

    /**
     * Unique identifier.
     *
     * @since 4.0.0
     * @access public
     * @var string
     */
    public $id;

    /**
     * Array of dependencies queries.
     *
     * See SiteEditorOptionsDependency::init() for information on meta query arguments.
     *
     * @since 1.0.0
     * @access public
     * @var array
     */
    public $queries = array();

    /**
     * Array of all queries controls
     *
     * @since 1.0.0
     * @access public
     * @var array
     */
    protected $controls = array();

    /**
     * Array of all queries settings
     *
     * @since 1.0.0
     * @access public
     * @var array
     */
    protected $settings = array();


    /**
     * Array of all queries settings
     *
     * @since 1.0.0
     * @access public
     * @var array
     */
    protected $pageConditions = array();

    /**
     * SiteEditorOptionsDependency constructor.
     * @param object $manager
     * @param mixed $id
     * @param array $args
     */
    public function __construct(  $manager, $id, $args = array() ) {

        $keys = array_keys( get_object_vars( $this ) );

        foreach ( $keys as $key ) {
            if ( isset( $args[ $key ] ) ) {
                $this->$key = $args[ $key ];
            }
        }

        $this->manager = $manager;

        $this->id = $id;

        $this->init();

    }

    /**
     * Initialize Dependency type
     */
    protected function init(){

        if( !empty( $this->queries ) ){

            $this->queries = $this->sanitize_query( $this->queries );

            $this->set_controls();

            $this->set_settings();

            $this->set_pageConditions();

        }

    }

    /**
     * Ensure the 'meta_query' argument passed to the class constructor is well-formed.
     *
     * Eliminates empty items and ensures that a 'relation' is set.
     *
     * @since 4.1.0
     * @access public
     *
     * @param array $queries Array of query clauses.
     * @return array Sanitized array of query clauses.
     */
    public function sanitize_query( $queries ) {
        $clean_queries = array();

        if ( ! is_array( $queries ) ) {
            return $clean_queries;
        }

        foreach ( $queries as $key => $query ) {
            if ( 'relation' === $key ) {
                $relation = $query;

            } elseif ( ! is_array( $query ) ) {
                continue;

                // First-order clause.
            } elseif ( self::is_first_order_clause( $query ) ) {
                if ( isset( $query['value'] ) && array() === $query['value'] ) {
                    unset( $query['value'] );
                }

                $clean_queries[ $key ] = $query;

                // Otherwise, it's a nested query, so we recurse.
            } else {
                $cleaned_query = $this->sanitize_query( $query );

                if ( ! empty( $cleaned_query ) ) {
                    $clean_queries[ $key ] = $cleaned_query;
                }
            }
        }

        if ( empty( $clean_queries ) ) {
            return $clean_queries;
        }

        // Sanitize the 'relation' key provided in the query.
        if ( isset( $relation ) && 'OR' === strtoupper( $relation ) ) {
            $clean_queries['relation'] = 'OR';

            /*
             * If there is only a single clause, call the relation 'OR'.
             * This value will not actually be used to join clauses, but it
             * simplifies the logic around combining key-only queries.
             */
        } elseif ( 1 === count( $clean_queries ) ) {
            $clean_queries['relation'] = 'OR';

            // Default to AND.
        } else {
            $clean_queries['relation'] = 'AND';
        }

        return $clean_queries;
    }

    /**
     * Determine whether a query clause is first-order.
     *
     * A first-order query clause is one that has either a 'key' or
     * a 'value' array key.
     *
     * @since 1.0.0
     * @access protected
     *
     * @param array $query query arguments.
     * @return bool Whether the query clause is a first-order clause.
     */
    public static function is_first_order_clause( $query ) {

        $type = isset( $query['type'] ) ? $query['type'] : "control";

        $is_first_order = false;

        if( $type == "control" || $type == "setting" ){

            $is_first_order = isset( $query['key'] ) && isset( $query['value'] );

        }else if( $type == "page_condition" ){

            $is_first_order = isset( $query['key'] );

        }

        return $is_first_order;
    }

    /**
     * Is Valid Dependency
     */
    public function is_valid(){

        if( empty( $this->queries ) ){
            return false;
        }

        return true;

    }

    /**
     * Gather the parameters passed to client JavaScript via JSON.
     *
     * @since 1.0.0
     *
     * @return array The array to be exported to the client as JSON.
     */
    public function json() {

        $json = array();

        $json['type']           = $this->type;

        $json['queries']        = $this->queries;

        $json['controls']       = $this->controls;

        $json['settings']       = $this->settings;

        $json['pageConditions'] = $this->pageConditions;

        return $json;
    }

    protected function set_controls( ){

        $this->controls = $this->get_query_types( $this->queries );

    }

    protected function set_settings( ){

        $this->settings = $this->get_query_types( $this->queries , "setting" );

    }

    protected function set_pageConditions( ){

        $this->pageConditions = $this->get_query_types( $this->queries , "page_condition" );

    }

    protected function get_query_types( $queries , $type = "control" ){

        $controls = array();

        if( !empty( $queries ) ){

            foreach( $queries AS $key => $query ){

                if ( ! is_array( $query ) ) {
                    continue;

                // First-order clause.
                }elseif ( self::is_first_order_clause( $query ) ) {

                    $query_type = isset( $query['type'] ) ? $query['type'] : "control";

                    if( $query_type == $type ){
                        $controls[] = $query['key'];
                    }

                }else{

                    $nested_controls = $this->get_query_types( $query , $type );

                    $controls = array_merge( $controls , $nested_controls );

                }

            }

        }

        return $controls;

    }

    public static function fix_controls_ids( $queries , $prefix ){

        foreach( $queries AS $key => $query ){

            if ( ! is_array( $query ) ) {
                continue;

                // First-order clause.
            }elseif ( self::is_first_order_clause( $query ) ) {

                $type = isset( $query['type'] ) ? $query['type'] : "control";

                if( $type == "control" ){
                    $query['key'] = $prefix."_".$query['key'];
                }

            }else{

                $query = self::fix_controls_ids( $query , $prefix );

            }

            $queries[$key] = $query;

        }

        return $queries;
    }

}

/*
"dependency"    => array(
    'type'              =>  'callback' ,
    'callback'          =>  'sedApp.editor.fn.test',
    'callback_args'     =>  array( 'callback' , 20 ),
    'controls'          =>  array( 'site_sheet_width' )
)

$simple_sample = array(
    'type'      => 'query' ,
    'queries'   => array(
        array(
            'key'       => '_my_custom_key',
            'value'     => 'Value I am looking for',
            'compare'   => '=' ,
            'type'      => 'control'
        )
    )
);

$sample = array(

    'relation' => 'OR', // Optional, defaults to "AND"

    array(
        'key'       => '_my_custom_key',
        'value'     => 'Value I am looking for',
        'compare'   => '=' ,
        'type'      => 'control'
    ),

    array(
        'key'       => '_my_custom_key_2',
        'value'     => 'Value I am looking for 2',
        'compare'   => '!=' ,
        'type'      => 'control'
    )

);

//Support nested queries
$nested_sample = array(

    'relation' => 'OR', // Optional, defaults to "AND"

    array(
        'key'       => '_my_custom_key',
        'value'     => 'Value I am looking for',
        'compare'   => '=' ,
        'type'      => 'control'
    ),

    array(

        'relation' => 'AND',

        array(
            'key'       => '_my_custom_key_2',
            'value'     => 'Value I am looking for 2',
            'compare'   => '=' ,
            'type'      => 'setting'
        ),

        array(
            'key'     => 'is_singular',
            'type'      => 'page_condition'
        )

    )

);*/
