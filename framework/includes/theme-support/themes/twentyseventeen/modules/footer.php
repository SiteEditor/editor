<?php

/**
 * SiteEditor Static Module Class
 *
 * Handles add static module in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class TwentyseventeenFooterStaticModule
 * @description : Footer Static Module
 */
class TwentyseventeenFooterStaticModule extends SiteEditorStaticModule{

    /**
     * Main Element Selector
     *
     * @var string
     * @access public
     */
    public $selector = '#colophon';

    /**
     * Register Module Settings & Panels
     */
    public function register_settings(){

        $panels = array(

        );

        $fields = array(



        );

        return array(
            'fields'    => $fields ,
            'panels'    => $panels
        );

    }


}

