<?php
class SiteEditorTemplates{

    public $current_template = 'default' ;
    public $templates = array();
    public $groups = array();
    public $templates_params = array();
    const option_name = 'sed_app_templates';

    function __construct( ) {

       //add_action( 'sed_app_preview_init', array( $this, 'template_preview' ) , 10 , 1 );
       //add_action( "sed-app-save-data" , array( $this , "save_new_template") , 10 , 2 );

       add_action('site_editor_ajax_save_template', array($this, 'save_template'));

       add_action('site_editor_ajax_load_templates', array($this, 'load_ajax_templates'));

       add_filter( "sed_addon_settings", array($this,'template_settings'));

       add_action( "sed_editor_init" , array( $this, "add_toolbar_elements" ) );
	}

    function add_toolbar_elements(){
        global $site_editor_app;
        $site_editor_app->toolbar->add_element(
            "layout" ,
            "template" ,
            "save_as_new_template" ,
            __("Save As New Template","site-editor") ,
            "save_as_new_template" ,     //$func_action
            "" ,                //icon
            "" ,  //$capability=
            array(),
            array( "row" => 1 ,"rowspan" => 2 ),
            array('module' => 'template' , 'file' => 'save_as_new_template.php'),
            'all' ,
            array(),
            array()
        );

        $site_editor_app->toolbar->add_element(
                "layout" ,
                "template" ,
                "select_template" ,
                __("Select Template","site-editor") ,
                "select_template_element" ,     //$func_action
                "" ,                //icon
                "" ,  //$capability=
                array(),
                array( "row" => 1 ,"rowspan" => 2 ),
                array('module' => 'template' , 'file' => 'select_template.php'),
                'all' ,
                array(
                    /*'template' => array(
                        'value'     => 'default',
                        'transport'   => 'custom'
                    )*/

                ),
                array(
                    /*'select_template' => array(
                        'settings'     => array(
                            'default'       => 'template'
                        ),
                        'type'    => 'templates'
                    ),*/
                )

            );
    }

    function template_settings( $sed_addon_settings ){
        global $site_editor_app;
        $sed_addon_settings['template'] = array(
            'nonce'  => array(
                'load'    =>  wp_create_nonce( 'sed_app_template_load_' . $site_editor_app->get_stylesheet() ) ,
                'save'    =>  wp_create_nonce( 'sed_app_template_save_' . $site_editor_app->get_stylesheet() ),
                'remove'  =>  wp_create_nonce( 'sed_app_template_remove_' . $site_editor_app->get_stylesheet() )
            )
        );
        return $sed_addon_settings;
    }

    function template_validate( $template_settings , $fields = array() ){
        $errors = array();
        if( is_array( $fields ) && !empty( $fields ) ){
            foreach( $fields  AS $name ){
                $value = trim($template_settings[$name]);
                if(!isset($template_settings[$name]) || empty( $value ) )
                    array_push( $errors , __(sprintf('Invalid template %s' , $name ) , "site-editor"));
            }
        }
        return $errors;
    }


    /*$template = array(
        "title"         =>   ,
        "name"         =>   ,
        "group"         =>   ,
        "screenshot"    =>   ,
        "description"   =>   ,
        "tags"          =>   ,
        "options"       =>  array(
            "requirement"      => array(
                "modules"       =>  array()  ,  //with versions
                "skins"         =>    ,  //with versions
                "medias"        =>  array()  ,
                "icons_fonts"   =>  array()  ,
                "fonts"         =>  array()  ,
                "widgets"       =>  array()  ,
                //"plugins"     =>    ,
                //"theme_content" =>  array()  ,
                //"main_content"  =>  array()  ,
            ),
            "settings"      =>   array(  )
        )
    ); */

    //---@export role
    //create template.sedt
    //convert all module post_id attr to 0
    //convert urls && src to {{url}} && {{src}}
    //check module && skins needed??
    //create folder of medias
    //---@import role
    //replace new url && src
    //create new media posts && modify shortcodes models
    function save_template()
    {
        global $sed_apps , $wpdb;  //@args ::: sed_page_ajax , nonce
        $sed_apps->editor->manager->check_ajax_handler('sed_save_template' , 'sed_app_template_save');

        $template_value = json_decode( wp_unslash( $_POST['template'] ), true );

        $errors = $this->template_validate( $template_value , array( "title" , "description" , "tags" ) );

        if( empty( $errors ) ){
            //$name = sanitize_title( $template_params['title'] );

            $current_user = wp_get_current_user();

            $template = array(
                'template_id'   =>   '',
                'title'         =>   sanitize_text_field( $template_value['title'] ),
                'name'          =>   sanitize_title( $template_value['title'] ),
                'tags'          =>   sanitize_text_field( $template_value['tags'] ),
                'group'         =>   sanitize_text_field( $template_value['group'] ),
                'description'   =>   sanitize_text_field( $template_value['description'] ),
                'screenshot'    =>   urlencode($template_value['screenshot'] ),
                'author'        =>   $current_user->user_nicename,
                'date'          =>   date('Y-m-d H:i:s'),
                'date_gmt'      =>   gmdate('Y-m-d H:i:s'),
            );

            $result = $wpdb->insert(
                $wpdb->prefix.'sed_template',
                $template ,
            	array(
            		'%d' ,
                    '%s' ,
                    '%s' ,
                    '%s' ,
                    '%s' ,
            		'%s' ,
                    '%s' ,
                    '%s' ,
                    '%s' ,
                    '%s' ,
            	)

            );

            $temp_id = $wpdb->insert_id;

            $template['template_id'] = $temp_id;

            $template['options'] = $template_value['options'];

            if($result === false){

                $success = false;
                $output = __("Created new template with an error: error in create template" , "site-editor");
                $template = array();
            }else{

                $option_name = self::option_name.$temp_id;
                $add_option = $this->update_template_option( $option_name , $template_value['options'] );

                if($add_option === false){

                    $success = false;
                    $output = __("Created new template with an error: error in save options" , "site-editor");
                    $template = array();
                }else{

                    $success = true;
                    $output = __("New Template Created Successfully." , "site-editor");
                    $template = $this->sed_prepare_template_for_js( $template );
                }

            }

        }else{
            $success = false;
            $output = $errors;
        }

        die( wp_json_encode( array(
          'success' => $success,
          'data'    => array(
                'output'    => $output ,
                'template'  => $template
          )
        ) ) );

    }


    function update_template_option( $option_name , $settings ){
        if ( get_option( $option_name ) !== false ) {

            // The option already exists, so we just update it.
            return update_option( $option_name , $_POST["new-template-settings"] );

        } else {

            // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
            $deprecated = null;
            $autoload = 'no';
            return add_option( $option_name , $settings , $deprecated, $autoload );
        }
    }


    function get_template_options($option_name){
        return get_option( $option_name );
    }


    function load_ajax_templates(){
        global $sed_apps , $wpdb;  //@args ::: sed_page_ajax , nonce
        $sed_apps->editor->manager->check_ajax_handler('sed_load_templates' , 'sed_app_template_load');

        $templates = $this->load_templates( $_POST['query'] );

        if( !empty( $templates ) )
            $success = true;
        else
            $success = false;

    	$templates = array_map( array( $this , 'sed_prepare_template_for_js' ) , $templates );
    	$templates = array_filter( $templates );

        $output = $templates;

        wp_send_json_success( $output );

    }

    function sed_prepare_template_for_js( $template ){

        $response = array(
            'id'          => $template['template_id'],
            'title'       => $template['title'],
            'author'      => $template['author'],
            'description' => $template['description'],
            'name'        => $template['name'],
            'date'        => strtotime( $template['date_gmt'] ) * 1000,
            'tags'        => $template['tags'] ,
            'screenshot'  => urldecode( $template['screenshot'] ) ,
            'type'        => $template['group'] ,
            'nonces'      => array(
                //'update' => false,
                'delete' => false
            ) ,
            'options'     => $template['options']
        );

        if ( current_user_can( 'edit_theme_options' ) ) {
            //$response['nonces']['update'] = wp_create_nonce( 'update-template' );
            $response['nonces']['delete'] = wp_create_nonce( 'delete-template-'.$template['template_id']  );
        }

        return apply_filters( 'sed_prepare_template_for_js', $response , $template );
    }

    function load_templates( $query = array() ){
        global $wpdb;

        if($query['group'] == "all")
            $query['group'] = '';

        $query = array_merge( array(
            'item_per_page' => 20 ,
            'paged'         => 1  ,
            'order_by'      => 'date'  ,
            'order'         => 'DESC' ,
            's'             =>  '' ,
            'group'         =>  ''
        ) , $query );

        extract( $query );

        $offset = $item_per_page * ($paged - 1);
        $offset = ($offset < 0 || !$offset ) ? 0 : $offset;

        $sql = "SELECT * FROM ".$wpdb->prefix."sed_template AS a ";

        if( !empty($group) || !empty($s)  )
            $sql .= "WHERE ";

        if( !empty($group) )
            $sql .= "a.group='{$group}' ";

        if( !empty($s)  ){
            $sql .= "a.title like '%{$s}%' OR a.name like '%{$s}%' OR a.tags like '%{$s}%' OR a.description like '%{$s}%' OR a.author like '%{$s}%' ";
        }

        if( !empty($order_by) )
            $sql .= "ORDER BY a.{$order_by} $order ";

        if($item_per_page > 0)
            $sql .= "LIMIT $offset , $item_per_page";

        $results = $wpdb->get_results( $sql , 'ARRAY_A' );

        $templates = array();

        if( !empty( $results ) ){
            foreach( $results AS $row ){
                $template = array();
                $template = $row;
                $template['options'] = $this->get_template_options( self::option_name.$row['template_id'] );
                if($template !== false)
                    array_push( $templates , $template );
            }

        }

        return $templates;

    }

    function add_template_group( $name , $title ,$parent_group){
        $group = new stdClass;
        $group->name           = $name;
        $group->parent         = $parent_group;
        $group->title          = $title;

        $this->groups[ $name ] = $group;
    }

    //load library templates in page(after click on the templates in library or when selected single templates)
    //load Type :  1.override 2.merge  3.noUsing(notAction)
    /*function load_page_template( $template , $current_page_main_shortcode ){
        if($template['main_shortcode'] == $current_page_main_shortcode){
            $main_shortcode_type = "equal"; //equal
        }else{
            $main_shortcode_type = "unequal";
        }

        $template_contain_main_content = true;

        $main_content_action = "merge"; // merge || override || no_action

        $remain_main_shortcode_settings = true; // IF $main_shortcode_type == "equal"  else false

    }

    function replace_theme_content(){

    }

    function remain_main_old_shortcodes(){

    }

    function replace_main_content(){

    }

    function append_main_content(){

    }

    function modify_all_shortcodes_ids(){

    }


    function modify_old_settings(){

    }

    function modify_new_settings(){

    }

    function merge_settings(){

    }

    function create_new_page_template(){
        global ;
        $sed_posts_content
  
        ->load_page_theme_content();

    }   */



}

