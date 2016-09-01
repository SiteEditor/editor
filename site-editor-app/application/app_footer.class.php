<?php
/**
 * @package     
 *
 *siteeditor.Platform
 *
 * @subpackage  AppToolbar
 *
 * @copyright   Copyright (C) 2013 - 2014 , Inc. All rights reserved.
 * @license     see LICENSE
 */

defined('_SEDEXEC') or die;

/**
 * Application Toolbar Class For Mind Map Application and Other
 *
 * @package     
 *
 *siteeditor.Platform
 *
 * @subpackage  AppToolbar
 * @since       1.0.0
 */
Class AppFooter {

    /**
    * @var    icon base url for toolbar element
    * @since  1.0.0
    */
    public $icon_url;

    /**
    * @var    array contain all tabs and elements
    * @since  1.0.0
    */
    public $groups = array();

    /**
    * Class constructor.
    *
    * @param   $args
    *
    *
    * @desc    zmind_parse_args do not orginal zmind or php fuction
    *
    *
    * @since   1.0.0
    */
    function __construct(  $args = array() ) {
        $args = wp_parse_args( $args, array(
            'app' => '',
            'icon_path' => SED_BASE_DIR . DS . "images" . DS . "app_icon" . DS ,
            'icon_url' => SED_BASE_URL . "images/app_icon/"
        ) );
        /**
         * OPTIONAL: Declare each item in $args as its own variable i.e. $type, $before.
         */
         extract( $args, EXTR_SKIP );
         $this->icon_url = $icon_url;
    }

    /**
    *
    * @Add New Element Group :: add new element group to special tab
    *
    * @param $parent_tab :: parent tab for this element group
    *
    * @param $name Unique :: tab name
    *
    * @param $title :: tab title
    *
    * @return void
    *
    */
    public function add_element_group( $name , $title, $position = "left" )
    {
        //add new element group to $tab
        $new_element_group = new stdClass;
        $new_element_group->name = $name;
        $new_element_group->title = $title;
        $new_element_group->position = $position;     //left || right
        $new_element_group->elements = array();

        $this->groups[$name] = $new_element_group ;
    }
    /**
    *
    * @Add New Element Group :: add new item element group to special group
    *
    * @param $parent_tab :: parent tab for this element group
    *
    * @param $name Unique :: tab name
    *
    * @param $title :: tab title
    *
    * @param $element_group :: parent tab for this element group
    *
    * @param $icon :: tab name
    *
    * @param $attr :: tab title
    *
    * @return void
    *
    */
    public function add_element( $element_group , $name , $title , $func_action , $content = "", $icon = "" ,  $capability= "" , $attr = array(), $extra = array() )
    {
        if(array_key_exists($element_group,$this->groups)){
            $group = $this->groups[$element_group];

            $element = new stdClass;
            $element->name         = $name;
            $element->group        = $element_group;
            $element->title        = $title;
            $element->icon         = $icon;
            $element->capability   = $capability;
            $element->attr         = $attr;
            $element->func_action  = $func_action;
            $element->extra        = $extra;
            $element->content      = $content;

            $group->elements[$element->name] = $element;

        }
    }




}