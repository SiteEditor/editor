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
Class AppToolbar {

    /**
    * @var    icon base url for toolbar element
    * @since  1.0.0
    */
    public $icon_url;

    public $current_app;

    public $template;

    /**
    * @var    array contain all tabs and elements
    * @since  1.0.0
    */
    public $tabs = array();

    public $settings = array();

    public $controls = array();

    public $sed_settings = array();

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
            'app' => 'siteeditor',
            'template' => 'default',
            'icon_path' => SED_EDITOR_DIR . DS . "images" . DS . "app_icon" . DS ,
            'icon_url' => SED_EDITOR_FOLDER_URL . "images/app_icon/"
        ) );
        /**
         * OPTIONAL: Declare each item in $args as its own variable i.e. $type, $before.
         */
         extract( $args, EXTR_SKIP );
         $this->template = $template;
         $this->current_app = $app;
         $this->icon_url = $icon_url;
         //$sed_page_id = (isset($_REQUEST['sed_page_id']) && !empty($_REQUEST['sed_page_id'])) ? $_REQUEST['sed_page_id'] : "general_home";
         //$sed_page_type = (isset($_REQUEST['sed_page_type']) && !empty($_REQUEST['sed_page_type'])) ? $_REQUEST['sed_page_type'] : "general";
         //$this->sed_settings = sed_get_settings($sed_page_id , $sed_page_type);
    }

    /**
    *
    * @Add New Tab To ToolBar
    *
    * @param $name Unique :: tab name
    *
    * @param $title :: tab title
    *
    * @param $type  :: tab type --- basic | menu  :: basic panel or menu panel
    *
    * @param $icon :: tab icon
    *
    * @param $attr :: tab Attributes for html element :: array("class" => "file tab-file" ,"onclick" => "run_open_file();",... );
    *
    * @return void
    *
    */

    public function add_new_tab( $name , $title , $icon = "" , $type="basic" , $attr = array() , $include = "all" )
    {
        if(!empty($icon)){
            $icon = $this->icon_url.$icon;
        }
        $tab = new stdClass;
        $tab->name = $name;
        $tab->title = $title;
        $tab->icon = $icon;
        $tab->type = $type;
        $tab->attr = $attr;
        $tab->site_editor_types = $include;
        $tab->elements = array();
        $this->tabs[$tab->name] = $tab;
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
    public function add_element_group( $parent_tab , $name , $title , $include = "all" )
    {
        if(array_key_exists($parent_tab,$this->tabs)){
            $tab = $this->tabs[$parent_tab];
            //add new element group to $tab
            $new_element_group = new stdClass;
            $new_element_group->name = $name;
            $new_element_group->title = $title;
            $new_element_group->site_editor_types = $include;

            if(!isset($tab->element_group)){
                $tab->element_group = array();
            }
            if( !in_array( $new_element_group , $tab->element_group ) )
                $tab->element_group[$new_element_group->name] = $new_element_group ;
        }
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
    public function add_element( $parent_tab , $element_group , $name , $title , $def_content , $icon = "" ,  $capability= "" , $attr = array(), $extra = array(), $view = array('module' => 'default' , 'file' => 'default.php') ,$include = "all" , $settings = array() , $controls = array() )
    {
        if(array_key_exists($parent_tab,$this->tabs)){
            $tab = $this->tabs[$parent_tab];
            if(isset($tab->element_group) && is_array($tab->element_group)){
                if($this->exsist_element_group($element_group , $tab) === true){
                    $element = new stdClass;
                    $element->name         = $name;
                    $element->group        = $element_group;
                    $element->title        = $title;
                    $element->icon         = $icon;
                    $element->capability   = $capability;
                    $element->attr         = $attr;
                    $element->def_content  = $def_content;
                    $element->extra        = $extra;
                    $element->settings     = $settings;
                    /*
                    sample settings
                    array(
                        'header_textcolor' => array(
                        'value'     => '#000000',
                        'transport'   => 'ajax'
                        ),
                        'header_textcolor' => array(
                        'value'     => '#000000',
                        'transport'   => 'ajax'
                        ),
                    )
                    */
                    global $sed_apps;
                    if(!empty($settings)){
                        foreach($settings AS $id => $values ){
                            /*if($this->sed_settings !== false && isset( $this->sed_settings[$id] )){
                                $values['value'] = $this->js_value( $id, $this->sed_settings[$id] );
                            }
                            $this->settings[ $id ] = $values; */
                            if( isset( $values['value'] ) ){
                                $values['default'] = $values['value'];
                                unset( $values['value'] );
                            }

                    		$sed_apps->editor_manager->add_setting( $id, $values );
                        }
                    }

                    $element->controls     = $controls;
                    /*
                    sample controls
                    array(
                        'display_header_text' => array(
                            'settings'     => array(
                                'default'       => 'header_textcolor',
                                ...
                            ),
                            'type'          => 'checkbox',
                            ...
                        ),
                        'header_textcolor' => array(
                            'settings'     => array(
                                'default'       => 'header_textcolor',
                                ...
                            ),
                            'type'          => 'color',
                            ...
                        )
                    )
                    */
                    if(!empty($controls)){
                        foreach($controls AS $id => $values ){
                            $this->controls[ $id ] = $values;
                    		$sed_apps->editor_manager->add_control( $id, $values );
                        }
                    }


                    if(is_array($view) && isset($view['module'] ) && $view['file']){

                        ob_start();

                        if(file_exists(SED_TMPL_PATH . DS . $this->template . DS . "modules" . DS . $view['module'] . DS .  $view['file'])){
                            require_once SED_TMPL_PATH . DS . $this->template . DS . "modules" . DS . $view['module'] . DS .  $view['file'];
                        }elseif(file_exists(SED_APPS_PATH . DS . $this->current_app . DS . "modules" . DS . $view['module'] . DS . "view" . DS . $view['file'])){
                            require_once SED_APPS_PATH . DS . $this->current_app . DS . "modules" . DS . $view['module'] . DS . "view" . DS . $view['file'];
                        }

                        $content = ob_get_contents();
                        ob_end_clean();
                    }else{
                        $content = "";
                    }

                    if(!empty($content)){
                        $element->content = $content;
                    }else{
                        $element->content = (!empty($def_content)) ? $def_content: "";
                    }

                    $element->site_editor_types = $include;
                    $element->sub          = array();

                    $tab->elements[$element->name] = $element;
                }
            }
        }
    }

	/**
	 * Sanitize the setting's value for use in JavaScript.
	 *
	 */
	public function js_value( $setting_id, $value ) {

		/**
		 * Filter a Customize setting value for use in JavaScript.
		 *
		 * The dynamic portion of the hook name, $this->id, refers to the setting ID.
		 *
		 */
		$value = apply_filters( "sed_value_sanitize_js_{$setting_id}", $value, $this );

		if ( is_string( $value ) )
			return html_entity_decode( $value, ENT_QUOTES, 'UTF-8');

		return $value;
	}

    /**
    *
    * @Add New Sub Element :: add new sub item element to special element
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
    public function add_sub_element( $parent_tab , $element_group , $element_name , $name , $title , $icon = "" ,  $capability= "" , $attr = array() ,$def_content = "" , $extra = array() , $include = "all" )
    {
        if(array_key_exists($parent_tab,$this->tabs)){
            $tab = $this->tabs[$parent_tab];
            if(isset($tab->element_group) && is_array($tab->element_group)){
                if($this->exsist_element_group($element_group , $tab) === true){
                   if( $element = $this->get_element($element_name ,$element_group, $tab) ){
                      $sub_element = new stdClass;
                      $sub_element->name         = $name;
                      $sub_element->element      = $element->name;
                      $sub_element->group        = $element_group;
                      $sub_element->title        = $title;
                      $sub_element->icon         = $icon;
                      $sub_element->capability   = $capability;
                      $sub_element->attr         = $attr;
                      $sub_element->def_content  = $def_content;
                      $sub_element->extra        = $extra;
                      $sub_element->site_editor_types = $include;

                      $element->sub[$sub_element->name] = $sub_element ;
                  }
                }
            }
        }
    }

    public function get_element($element ,$element_group, $tab){
        $res = false;
        foreach( $tab->element_group AS $group){
            if($group->name == $element_group){
               foreach( $tab->elements AS $element_obj){
                  if($element_obj->group == $group->name){
                     if($element_obj->name == $element){
                        $res = $element_obj;
                        break;
                     }
                  }
               }
            }
        }
        return $res;
    }

    public function exsist_element_group($element_group, $tab){
        $res = false;
        foreach( $tab->element_group AS $group){
            if($group->name == $element_group){
                $res = true;
                break;
            }
        }
        return $res;
    }

}
