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
 * Application Toolbar Class For Site Editor Application and Other
 *
 * @package      
 *
 *siteeditor.Platform
 *
 * @subpackage  AppToolbar
 * @since       1.0.0
 */
Class AppContextmenu{

    /**
    * @var    icon base url for toolbar element
    * @since  1.0.0
    */
    public $icon_url;

    public $current_app;

    public $template;

    public $current_module = "";

    /**
    * @var    array contain all tabs and elements
    * @since  1.0.0
    */
    public $menus = array();

    public static $dialog_settings_otions = array(
              "autoOpen"      => false,
              //"dialogClass"   => "library-dialog",
              "modal"         => false,
              "width"         => 295,
              "height"        => 600 ,
              "position"      => array(
                  "my"    =>  "right-20",
                  "at"    =>  "right" ,
                  "of"    =>  "#sed-site-preview"
              )
           );

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
            'icon_path' => SED_BASE_DIR . DS . "images" . DS . "app_icon" . DS ,
            'icon_url' => SED_BASE_URL . "images/app_icon/"
        ) );
        /**
         * OPTIONAL: Declare each item in $args as its own variable i.e. $type, $before.
         */
         extract( $args, EXTR_SKIP );
         $this->template = $template;
         $this->current_app = $app;
         $this->icon_url = $icon_url;
    }

    public function create_menu( $name , $title , $icon = "" , $type_icon="class" , $type , $selector = "" , $shortcode = "" ,$show_general_items = array() ,$help = ""  )
    {
        if(!empty($icon) && $type_icon == "src"){
            $icon = $this->icon_url.$icon;
        }
        $menu = new stdClass;
        $menu->name        = $name;
        $menu->title       = $title;
        $menu->icon        = $icon;
        $menu->type_icon   = $type_icon;
        $menu->help        = $help;
        $menu->type        = $type;
        $menu->selector    = $selector;
        $menu->menu_id     = $name."_contextmenu";
        $menu->shortcode   = $shortcode;
        $menu->items = array();

        $show_general_items = array_merge(
            array(
                "title_bar"         =>  true,   //0
                //"footer_bar"        =>  true,   //100
                "arrangement"       =>  true,   //80
                //"show_on_page"      =>  true,   //90
                "show_on_sub_themes"  => true ,
                "settings"          =>  true,   //50
                "edit_style"        =>  true,   //60
                "animation"         =>  true,   //70
                "change_skin"       =>  true ,  //70
                "duplicate"         =>  true ,  //100
                "widget_settings"   =>  false , //10
                "link_to"           =>  false,  //40
                "change_icon"       =>  false,  //10
                "change_image"      =>  false,  //10
                "edit_image"        =>  false,  //10
                "seperator"         =>  array()
            ),
            $show_general_items
        );

        foreach( $show_general_items AS $item => $priority ){
            $func = "add_".$item."_item";

            if(!method_exists('AppContextmenu' , $func) || $priority === false)
                continue;

            if(!is_array( $priority ))
                $this->call_func_add_item( $item , $menu , $priority , $title , $shortcode );
            else{
                if(!empty( $priority )){
                    foreach($priority AS $pr_value){
                        $this->call_func_add_item( $item , $menu , $pr_value , $title , $shortcode );
                    }
                }
            }
        }

        $this->menus[$menu->name] = $menu;
        return $menu;
    }

    public function call_func_add_item( $item , $menu , $priority , $title , $shortcode = "" ){
        $func = "add_".$item."_item";
        $args = array( $menu );
        if( (is_bool($priority) && $priority === true) || is_int($priority)){
            switch ($item) {
              case "title_bar":

                  $args[] = $title;

              break;
              case "settings":
              case "edit_style"      :
              case "animation"       :
              case "change_skin"     :
              case "widget_settings" :

                  $args[] = $shortcode;

              break;
            }
        }

        if( is_int($priority) ){
            $args[] = $priority;
        }

        call_user_func_array( array($this , $func) , $args );
    }

    public function create_submenu( $menu ,$name , $title , $icon = "" , $type_icon="class" , $attr = array() , $options = array() , $html = '' , $priority = 10 , $action = "" )
    {
        if(!empty($icon) && $type_icon == "src"){
            $icon = $this->icon_url.$icon;
        }
        $submenu = new stdClass;
        $submenu->menu = $menu->name;
        $submenu->name = $name;
        $submenu->title = $title;
        $submenu->icon = $icon;
        $submenu->type_icon = $type_icon;
        $submenu->attr = $attr;
        $submenu->options = $options;
        $submenu->custom_html = $html;
        $submenu->action = $action;
        $submenu->is_submenu = true;
        $submenu->shortcode = $menu->shortcode;
        $submenu->priority = $priority;
        $submenu->items = array();

        $menu->items[$submenu->name] = $submenu;
        return $submenu;
    }

    /**
    *
    * @Add New Item To Context Menu
    *
    * @param $name Unique :: item name
    *
    * @param $title :: item title
    *
    * @param $type_icon  :: item type icon --- src|class
    *
    * @param $icon :: item icon --- image src or font icon class
    *
    * @param $attr :: tab Attributes for html element :: array("class" => "file tab-file" ,"onclick" => "run_open_file();",... );
    *
    * @return void
    *
    */

    public function add_item( $menu ,$name , $title , $icon = "" , $type_icon="class" , $attr = array() , $options = array() , $html = '' , $priority = 10 , $action = "" )
    {
        if(!empty($icon) && $type_icon == "src"){
            $icon = $this->icon_url.$icon;
        }
        $item = new stdClass;
        $item->menu = $menu->name;
        $item->name = $name;
        $item->title = $title;
        $item->icon = $icon;
        $item->type_icon = $type_icon;
        $item->attr = $attr;
        $item->options = $options;
        $item->custom_html = $html;
        $item->priority = $priority;
        $item->action = $action;
        $item->is_submenu = false;

        $key = $this->check_item_key($menu->items , $name);
        $menu->items[$key] = $item;

    }

    public function check_item_key($items , $name){
        if( array_key_exists( $name , $items ) ){
            $name .= "1";
            return $this->check_item_key($items , $name);
        }else{
            return $name;
        }
    }

    public static function get_dialog_settings_attr( $shortcode ){
        return  array(
            "data-shortcode-name"      =>  $shortcode ,
            "sed-dialog-tmpl-id"    => "tmpl-dialog-settings-".$shortcode ,
            "sed-dialog-selector"   => $shortcode ,
            "sed-dialog-id"         => "sedDialogSettings",
            "data-dialog-tmpl-type" => "dynamic" ,
            "class"                 => ""
        );
    }

    //settings item menu is for modules
    public function add_settings_item($menu , $name = '' , $priority = 50 )
    {
        $menu->shortcode   = (!empty($name)) ?  $name : $menu->shortcode;

        $attr = self::get_dialog_settings_attr( $menu->shortcode );

        $attr['class'] = "element-settings";

        $this->add_item(
            $menu ,
            "settings" ,
            __("Settings","site-editor") ,
            "icon-settingitem" ,
            "class" ,
            $attr,
            array( 'dialog_options'  => self::$dialog_settings_otions ),
            "" ,
            $priority ,
            "openDialogSettings"
        );

    }

    //settings item menu is for modules
    public function add_widget_settings_item($menu , $name = '' , $priority = 50 )
    {
        $menu->shortcode   = (!empty($name)) ?  $name : $menu->shortcode;
        $attr = self::get_dialog_settings_attr( $menu->shortcode );

        $attr['class'] = "widget-element-settings";
        $attr['data-settings-type'] = "widget";

        $menu->shortcode   = (!empty($name)) ?  $name : $menu->shortcode;
        $this->add_item(
            $menu ,
            "widget_settings" ,
            __("Widget Settings","site-editor") ,
            "icon-settingitem" ,
            "class" ,
            $attr,
            array( 'dialog_options'  => self::$dialog_settings_otions  ),
            "" ,
            $priority ,
            "openDialogSettings"
        );
    }

    public function add_animation_item( $menu , $name = '' ,  $priority = 70 )
    {

        $menu->shortcode   = (!empty($name)) ?  $name : $menu->shortcode;
        $attr = self::get_dialog_settings_attr( $menu->shortcode );

        $attr['class'] = "animation-element-settings";
        $attr['data-settings-type'] = "animation";

        $this->add_item(
            $menu ,
            "add-animation" ,
            __("Add Animation","site-editor") ,
            "icon-addanimation" ,
            "class" ,
            $attr ,
            array( 'dialog_options'  => self::$dialog_settings_otions  ),
            "" ,
            $priority ,
            "openDialogSettings"
        );

    }

    public function add_change_skin_item( $menu , $name = '' ,$priority = 70 )
    {

        $menu->shortcode   = (!empty($name)) ?  $name : $menu->shortcode;
        $attr = self::get_dialog_settings_attr( $menu->shortcode );

        $attr['class'] = "change-skin-element-settings";
        $attr['data-settings-type'] = "changeSkin";
        $attr['data-module-name'] = $this->current_module;

        $this->add_item(
            $menu ,
            "change-skin" ,
            __("Change Skin","site-editor") ,
            "icon-changeskin" ,
            "class" ,
            $attr ,
            array( 'dialog_options'  => self::$dialog_settings_otions  ),
            "" ,
            $priority ,
            "openDialogSettings"
        );

    }

    public function add_edit_style_item( $menu , $name = '' ,$priority = 60 )
    {

        $menu->shortcode   = (!empty($name)) ?  $name : $menu->shortcode;
        $attr = self::get_dialog_settings_attr( $menu->shortcode );

        $attr['class'] = "edit-styles-element-settings";
        $attr['data-settings-type'] = "editStyle";
        $attr['data-panel-id'] = $menu->shortcode . "_design_panel";

        $this->add_item(
            $menu ,
            "change-style" ,
            __("Change Style","site-editor") ,
            "icon-changestyle" ,
            "class" ,
            $attr ,
            array( 'dialog_options'  => self::$dialog_settings_otions  ),
            "" ,
            $priority ,
            "openDialogSettings"
        );

        /*$this->add_item(
            $menu ,
            "change-style" ,
            __("Change Style","site-editor") ,
            "change-style" ,
            "class" ,
            array(
                "sed-tab" => "style-editor",
                "class"   => "element-edit-styles"
            ) ,
            array(),
            "" ,
            $priority
        );*/
    }

    public function add_duplicate_item( $menu , $name = '' ,$priority = 100 )
    {

        $menu->shortcode   = (!empty($name)) ?  $name : $menu->shortcode;
        $attr = self::get_dialog_settings_attr( $menu->shortcode );

        $attr['class'] = "element-duplicate";
        //$attr['data-settings-type'] = "editStyle";
        //$attr['data-panel-id'] = $menu->shortcode . "_design_panel";

        $this->add_item(
            $menu ,
            "duplicate" ,
            __("Duplicate","site-editor") ,
            "icon-duplicate" ,
            "class" ,
            $attr ,
            array( ),
            "" ,
            $priority ,
            "duplicate"
        );

        /*$this->add_item(
            $menu ,
            "change-style" ,
            __("Change Style","site-editor") ,
            "change-style" ,
            "class" ,
            array(
                "sed-tab" => "style-editor",
                "class"   => "element-edit-styles"
            ) ,
            array(),
            "" ,
            $priority
        );*/
    }

    public function add_seperator_item( $menu , $priority = 10 )
    {
        $html = '';
        $this->add_item(
            $menu ,
            "seperator" ,
            __("Seperator","site-editor") ,
            $icon = "seperator" ,
            "class" ,
            array(
            'class' => 'divider',
            ) ,
            array() ,
            $html ,
            $priority
        );
    }

    public function add_change_image_item($menu , $priority = 10)
    {

        extract(array(
            "support_types"      =>  array("image") ,
            "dialog_title"       =>  __("Image Library") ,
            "add_btn_title"      =>  __("Change Image","site-editor")
        ));

        $this->add_item(
            $menu ,
            "change_image" ,
            __("Change Image","site-editor") ,
            "icon-changeimageitem" ,
            "class" ,
            array(
                "class"                 => "change-img",
            ),
            array(
                'media'     => array(
                    "supportTypes"       => $support_types,
                    "selctedType"        => "single",   // single or multiple
                    "dialog"     => array(
                        "title"     =>    $dialog_title,
                        "buttons"   =>    array(
                            array(
                                "title"    =>   $add_btn_title ,
                                "type"     =>   "change_media" ,
                                "select_validation" =>   true
                            )
                        )
                    ),
                    "shortcode"  =>  $menu->shortcode ,
                    "attr"       =>  "src"
                )
            ),
            "" ,
            $priority ,
            "openMediaLibrary"
        );
    }

    public function add_change_video_item($menu , $priority = 10)
    {

        extract(array(
            "support_types"      =>  array("video") ,
            "dialog_title"       =>  __("Video Library") ,
            "add_btn_title"      =>  __("Change Video","site-editor")
        ));

        $this->add_item(
            $menu ,
            "change_video" ,
            __("Change Video","site-editor") ,
            "icon-video1" ,
            "class" ,
            array(
                "class"                 => "change-video",
            ),
            array(
                'media'     => array(
                    "supportTypes"       => $support_types,
                    "selctedType"        => "single",   // single or multiple
                    "dialog"     => array(
                        "title"     =>    $dialog_title,
                        "buttons"   =>    array(
                            array(
                                "title"    =>   $add_btn_title ,
                                "type"     =>   "change_media" ,
                                "select_validation" =>   true
                            )
                        )
                    ),
                    "shortcode"  =>  $menu->shortcode ,
                    "attr"       =>  "setting_m4v"
                )
            ),
            "" ,
            $priority ,
            "openMediaLibrary"
        );
    }

    public function add_change_audio_item($menu , $priority = 10)
    {

        extract(array(
            "support_types"      =>  array("audio") ,
            "dialog_title"       =>  __("Audio Library") ,
            "add_btn_title"      =>  __("Change Audio","site-editor")
        ));

        $this->add_item(
            $menu ,
            "change_audio" ,
            __("Change Audio","site-editor") ,
            "icon-earphones" ,
            "class" ,
            array(
                "class"                 => "change-audio",
            ),
            array(
                'media'     => array(
                    "supportTypes"       => $support_types,
                    "selctedType"        => "single",   // single or multiple
                    "dialog"     => array(
                        "title"     =>    $dialog_title,
                        "buttons"   =>    array(
                            array(
                                "title"    =>   $add_btn_title ,
                                "type"     =>   "change_media" ,
                                "select_validation" =>   true
                            )
                        )
                    ),
                    "shortcode"  =>  $menu->shortcode ,
                    "attr"       =>  "setting_mp3"
                )
            ),
            "" ,
            $priority ,
            "openMediaLibrary"
        );
    }


    public function add_media_manage_item($menu , $title , $settings = array() , $priority = 10)
    {
        extract( array_merge(
            array(
               "support_types"      =>  array("all") ,
               "dialog_title"       =>  __("gallery Management") ,
               "tab_title"          =>  __("Edit gallery") ,
               "update_btn_title"   =>  __("Update gallery","site-editor") ,
               "add_btn_title"      =>  __("Add To Gallery","site-editor") ,
               "media_attrs"        =>  array("attachment_id","image_url" , "image_source")
            ),
            $settings
        ));

        if(!is_array($support_types) && $support_types)
            $support_types = array($support_types);
        else if(!is_array($support_types) && !$support_types || empty($support_types) )
            $support_types = array("all");

        $this->add_item(
            $menu ,
            "media_manage" ,
            $title ,          //__("media management","site-editor");
            "media_manage" ,
            "class" ,
            array(
                "class"      => "media_manage",
            ),
            array(
                'media'     => array(
                    "supportTypes"       => $support_types,
                    "ShowOrganizeTab"    => true,
                    "selctedType"        => "multiple",   // single or multiple
                    "activeTab"          => "organize" ,
                    "media_attrs"        =>  $media_attrs,
                    "organizeTab"        => array(
                        "title"    =>    $tab_title,
                        "buttons"  =>array(
                            array(
                                "title"    =>   $update_btn_title ,
                                "type"     =>   "update_media_collection"
                            ),
                            array(
                                "title"             =>   __("Cancel","site-editor") ,
                                "type"              =>   "cancel" ,
                            )
                        )
                    ),
                    "dialog"     => array(
                        "title"     =>    $dialog_title,
                        "buttons"   =>    array(
                            array(
                                "title"             =>   $add_btn_title ,
                                "type"              =>   "add_to_collection" ,
                                "select_validation" =>   true
                            )
                        )
                    )
                )
            ),
            "" ,
            $priority ,
            "openMediaLibrary"
        );
    }


    public function add_change_icon_item($menu , $priority = 10)
    {
        $this->add_item(
            $menu ,
            "change_icon" ,
            __("Change Icon","site-editor") ,
            "icon-icons" ,
            "class" ,
            array(
                "sed-dialog-tmpl-id"    => "tmpl-dialog-icon-library" ,
                "sed-dialog-selector"   => "#sed-dialog-icon-library" ,
                "sed-dialog-id"         => "sedDialogIconLibrary",
                "class"                 => "change-icon element-open-dialog",
                "data-selcted-type"     => "single" ,
                "data-shortcode-name"   => $menu->shortcode ,
                "data-event-key"        => "",
                "data-current-icons"    => ""
            ),
            array(
                'dialog_options'        => array(
                    "autoOpen"      => false,
                    "dialogClass"   => "icon-library-dialog",
                    "modal"         => true,
                    "width"         => 880,
                    "height"        => 550
                )
            ) ,
            "" ,
            $priority
        );
    }

    public function add_organzie_posts_item($menu , $priority = 10){

        $this->add_item(
            $menu ,
            "organzie_posts" ,
            __("Organize Posts","site-editor") ,
            "change-icon" ,
            "class" ,
            array(
                "sed-dialog-tmpl-id"    => "tpl-organize-posts-dialog" ,
                "sed-dialog-selector"   => "#sed-organize-posts-dialog" ,
                "sed-dialog-id"         => "sedDialogorganizePosts",
                "class"                 => "change-icon element-open-dialog",
                "data-selcted-type"     => "single"
            ),
            array(
                'dialog_options'        => array(
                    "autoOpen"      => false,
                    "dialogClass"   => "organize-posts-dialog",
                    "modal"         => true,
                    "width"         => 880,
                    "height"        => 550
                )
            ) ,
            "" ,
            $priority
        );
    }

    public function add_organzie_menu_item( $menu , $priority = 10){

        $this->add_item(
            $menu ,
            "organzie_menu" ,
            __("Organize menu","site-editor") ,
            "change-icon" ,
            "class" ,
            array(
                "sed-dialog-tmpl-id"    => "tpl-organize-menu-dialog" ,
                "sed-dialog-selector"   => "#sed-organzie-menu-dialog" ,
                "sed-dialog-id"         => "sedDialOgorganizeMenu",
                "class"                 => "change-icon element-open-dialog",
                "data-selcted-type"     => "single"
            ),
            array(
                'dialog_options'        => array(
                    "autoOpen"      => false,
                    "dialogClass"   => "organize-menu-dialog",
                    "modal"         => true,
                    "width"         => 880,
                    "height"        => 550
                )
            ) ,
            "" ,
            $priority
        );
    }


    public function add_edit_image_item()
    {

    }

    public function add_link_to_item( $menu , $priority = 40 )
    {

        $menu->shortcode   = (!empty($name)) ?  $name : $menu->shortcode;
        $attr = self::get_dialog_settings_attr( $menu->shortcode );

        $attr['class'] = "add-element-link";
        $attr['data-settings-type'] = "linkTo";

        $menu->shortcode   = (!empty($name)) ?  $name : $menu->shortcode;

        $this->add_item(
            $menu ,
            "link_to" ,
            __("Link To","site-editor") ,
            "icon-linktoitem" ,
            "class",
            $attr,
            array( 'dialog_options'  => self::$dialog_settings_otions  ),
            "" ,
            $priority ,
            "openDialogSettings"
        );

        //not support in version 1.0.0
        /*
        $submenu = $this->create_submenu( $menu ,"link_to" , __("Link To","site-editor") , "link-to" , "class" , array() , array() , "" , $priority );

        $this->add_item(
            $submenu ,
            "web_address" ,
            __("Web Address","site-editor") ,
            "web-address" ,
            "class",
            array(
                "sed-dialog-tmpl-id"    => "tmpl-dialog-web-address" ,
                "sed-dialog-selector"   => "#sed-dialog-web-address" ,
                "sed-dialog-id"         => "sedDialogWebAddress",
                "class"                 => "web-address element-open-dialog",
            ),
            array(
                'dialog_options'        => array(
                    "autoOpen"      => false,
                    //"dialogClass"   => "library-dialog",
                    "modal"         => false,
                    "width"         => 290,
                    "height"        => 350
                )
            )
        );

        $this->add_item(
            $submenu ,
            "page" ,
            __("Page",
            "site-editor") ,
            "page" ,
            "class",
            array(
                "sed-dialog-tmpl-id"    => "tmpl-dialog-page-link" ,
                "sed-dialog-selector"   => "#sed-dialog-page-link" ,
                "sed-dialog-id"         => "sedDialogPageLink",
                "class"                 => "page-link element-open-dialog",
            ),
            array(
                'dialog_options'        => array(
                    "autoOpen"      => false,
                    //"dialogClass"   => "library-dialog",
                    "modal"         => false,
                    "width"         => 290,
                    "height"        => 350
                )
            )
        );

        $this->add_item(
            $submenu ,"page_top" ,
            __("Page Top","site-editor") ,
            "page-top" ,
            "class",
            array(
                "sed-dialog-tmpl-id"    => "tmpl-dialog-page-top" ,
                "sed-dialog-selector"   => "#sed-dialog-page-top" ,
                "sed-dialog-id"         => "sedDialogPageTop",
                "class"                 => "page-top element-open-dialog",
            ),
            array(
                'dialog_options'        => array(
                    "autoOpen"      => false,
                    //"dialogClass"   => "library-dialog",
                    "modal"         => false,
                    "width"         => 290,
                    "height"        => 350
                )
            )
        );

        $this->add_item(
            $submenu ,
            "page_bottom" ,
            __("Page Bottom","site-editor") ,
            "page-bottom" ,
            "class",
            array(
                "sed-dialog-tmpl-id"    => "tmpl-dialog-page-bottom" ,
                "sed-dialog-selector"   => "#sed-dialog-page-bottom" ,
                "sed-dialog-id"         => "sedDialogPageBottom",
                "class"                 => "page-bottom element-open-dialog",
            ),
            array(
                'dialog_options'        => array(
                    "autoOpen"      => false,
                    //"dialogClass"   => "library-dialog",
                    "modal"         => false,
                    "width"         => 290,
                    "height"        => 350
                )
            )
        );

        $this->add_item(
            $submenu ,
            "email" ,
            __("Email",
            "site-editor") ,
            "email" ,
            "class",
            array(
                "sed-dialog-tmpl-id"    => "tmpl-dialog-email-link" ,
                "sed-dialog-selector"   => "#sed-dialog-email-link" ,
                "sed-dialog-id"         => "sedDialogEmailLink",
                "class"                 => "email-link element-open-dialog",
            ),
            array(
                'dialog_options'        => array(
                    "autoOpen"      => false,
                    //"dialogClass"   => "library-dialog",
                    "modal"         => false,
                    "width"         => 290,
                    "height"        => 350
                )
            )
        );

        $this->add_item(
            $submenu ,
            "document" ,
            __("Document","site-editor") ,
            "document" ,
            "class",
            array(
                "sed-dialog-tmpl-id"    => "tmpl-dialog-document-link" ,
                "sed-dialog-selector"   => "#sed-dialog-document-link" ,
                "sed-dialog-id"         => "sedDialogDocumentLink",
                "class"                 => "document-link element-open-dialog",
            ),
            array(
                'dialog_options'        => array(
                    "autoOpen"      => false,
                    //"dialogClass"   => "library-dialog",
                    "modal"         => false,
                    "width"         => 290,
                    "height"        => 350
                )
            )
        );

        $this->add_item(
            $submenu ,
            "anchor" ,
            __("Anchor","site-editor") ,
            "anchor" ,
            "class",
            array(
                "sed-dialog-tmpl-id"    => "tmpl-dialog-anchor-link" ,
                "sed-dialog-selector"   => "#sed-dialog-anchor-link" ,
                "sed-dialog-id"         => "sedDialogAnchorLink",
                "class"                 => "anchor-link element-open-dialog",
            ),
            array(
                'dialog_options'        => array(
                    "autoOpen"      => false,
                    //"dialogClass"   => "library-dialog",
                    "modal"         => false,
                    "width"         => 290,
                    "height"        => 350
                )
            )
        ); */
    }

    //for free draggable only
    public function add_arrangement_item( $menu , $priority = 80 )
    {

        $submenu = $this->create_submenu( $menu ,"arrangement" , __("Arrangement","site-editor") , "arrangement" , "class" , array("class" => "modules-arrangement contextmenu-hide-item") , array() , "" , $priority );

        $this->add_item(
            $submenu ,
            "bring-to-front" ,
            __("Bring To Front","site-editor") ,
            "bring-to-front" ,
            "class",
            array(
                "class" => "bring-to-front",
            ),
            array(),
            '',
            10 ,
            "bringToFront"
        );

        $this->add_item(
            $submenu ,
            "bring-forward" ,
            __("Bring Forward","site-editor") ,
            "bring-forward" ,
            "class",
            array(
                "class" => "bring-forward",
            ),
            array(),
            '',
            10 ,
            "bringForward"
        );


        $this->add_item(
            $submenu ,
            "send-backward" ,
            __("Send Backward","site-editor") ,
            "send-backward" ,
            "class",
            array(
                "class" => "send-backward",
            ),
            array(),
            '',
            10 ,
            "sendBackward"
        );


        $this->add_item(
            $submenu ,
            "send-to-back" ,
            __("Send To Back","site-editor") ,
            "send-to-back" ,
            "class",
            array(
                "class" => "send-to-back",
            ),
            array(),
            '',
            10 ,
            "sendToBack"
        );


    }

    public function add_title_bar_item( $menu , $title , $priority = 0 )
    {                                                                                        // <span class="menu_item_icon icon-question"></span>
        $html = '<div class="custom-row"><strong skinpart="label" class="editor-skins-label">'.$title.'</strong></div>';
        $this->add_item( $menu ,"title-bar" , __("Title Bar","site-editor") , $icon = "title-bar" , "class" , array() , array() , $html , $priority);
    }

    //not support in version 1.0
    /*public function add_footer_bar_item( $menu , $priority = 100 )
    {
        $html = '<div class="contextmenu-footer-bar contextmenu-icon-bar">
        <a><span class="el_txt" >'.__("Help","site-editor").'</span><span class="fa icon-question" ></span></a>
        <a><span class="el_txt">'.__("lock","site-editor").'</span><span class="fa icon-lock" ></span></a>
        <a><span class="el_txt">'.__("copy","site-editor").'</span><span class="fa icon-docs" ></span></a>
        <a><span class="el_txt">'.__("paste","site-editor").'</span><span class="fa icon-bag" ></span></a>
        <a><span class="el_txt">'.__("delete","site-editor").'</span><span class="fa icon-trash " ></span></a>
        </div>';
        $this->add_item( $menu ,"footer-bar" , __("Footer Bar","site-editor") , $icon = "footer-bar" , "class" , array() , array() , $html , $priority);
    }*/

    //not support in version 1.0
    /*
    public function add_show_on_page_item( $menu , $priority = 90 )
    {
        $submenu = $this->create_submenu( $menu ,"show_on_page" , __("Show On Page","site-editor") , "show_on_page" , "class"  , array() , array() , "" , $priority );
        $html = '<label input class="menu_item_label" for=""><input class="menu_item_icon" type="checkbox" name="" id="" /> <span class="menu_item_txt">'.__("Show On All Pages","site-editor").'</span></label>';
        $this->add_item( $submenu ,"all-pages" , __("All Pages","site-editor") , $icon = "all-pages" , "class" , array() , array() , $html);
    } */

    public function add_show_on_sub_themes_item( $menu , $priority = 90 )
    {
        $submenu = $this->create_submenu( $menu ,"show_on_sub_themes" , __("Show On Sub Themes","site-editor") , "icon-subtheme" , "class"  , array( "class" => "show-on-sub-themes" ) , array() , "" , $priority );
        $html = '<input type="radio" name="sed-row-theme-type" class="sed-row-theme-type" value="public"><span>'.__( "Show On Sub Themes" , "site-editor" ) .'</span>';
        $this->add_item( $submenu ,"show_on_sub_themes_type" , __( "Show On Sub Themes" , "site-editor" ) , "" , "class" , array( "class" => "customize-row-action" ) , array() , $html , 10 , "formElement");

    }


    public function add_edit_text_item()
    {

    }

    public function add_organize_image_item()
    {

    }




}
