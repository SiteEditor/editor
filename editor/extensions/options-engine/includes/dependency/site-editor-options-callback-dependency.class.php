<?php

/**
 * SiteEditor Options Callback Dependency Class
 *
 * Implements Controls && Panel Dependencies management in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class SiteEditorOptionsCallBackDependency
 * @description : Options Callback Dependency For SiteEditor Application.
 */
class SiteEditorOptionsCallbackDependency extends SiteEditorOptionsDependency{

    /**
     * The dependency type Default is queries
     *
     * @since 1.0.0
     * @access public
     * @var string
     */
    public $type = "callback";

    /**
     * The Js Function Name
     *
     * @since 1.0.0
     * @access public
     * @var string
     */
    public $callback = "";

    /**
     * The arguments for js callback
     *
     * @since 1.0.0
     * @access public
     * @var array
     */
    public $callback_args = array();

    /**
     * Initialize Dependency type
     */
    protected function init(){

        if( !empty( $this->params ) ){

        }

    }

    /**
     * Is Valid Dependency
     */
    public function is_valid(){

        if( empty( $this->callback ) || ! is_array( $this->callback_args ) ){
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

        $json['callback']       = $this->callback;

        $json['callback_args']  = $this->callback_args;

        if( !empty( $this->controls ) ) {
            $json['controls']   = $this->controls;
        }

        return $json;
    }

}
