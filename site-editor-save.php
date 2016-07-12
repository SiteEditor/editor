<?php
class SEDAppSave{
    function __construct(  ) {
        add_action("site_editor_ajax_customize_save", array(&$this,"site_editor_app_save") );
        $this->wp_theme = wp_get_theme( isset( $_REQUEST['theme'] ) ? $_REQUEST['theme'] : null );
    }

    function sed_update_page_options($settings , $option_name , $sed_page_id = "general_home" , $sed_page_type = "general"){
        switch ($sed_page_type) {
          case "tax":
          case "general":
          case "post_type":
          case "author":
              if ( get_option( $option_name ) !== false ) {

                  // The option already exists, so we just update it.
                  $res = update_option( $option_name, $settings );
                  //var_dump($res , "update_option :: teeeeeeeeeeeeeeeeeeees...............");
              } else {

                  // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                  $deprecated = null;
                  $autoload = 'no';
                  $res = add_option( $option_name, $settings, $deprecated, $autoload );
                  //var_dump($res , "add_option :: teeeeeeeeeeeeeeeeeeees...............");
              }
          break;
          case "post":
              if( !update_post_meta( $sed_page_id , $option_name , $settings ) )
                  add_post_meta( $sed_page_id , $option_name , $settings, true );
          break;
        }
    }

    //Edit for sub_theme module-----  ( add sed_current_page_options filter )
    function sed_get_page_options( $option_name , $sed_page_id = "general_home" , $sed_page_type = "general"){

        switch ($sed_page_type) {
          case "tax":
          case "general":
          case "post_type":
          //case "author":
            return get_option( $option_name ) ;
          break;
          case "post":
              $settings = get_post_meta( $sed_page_id, $option_name , true );
              if(empty( $settings )){
                return false;
              }else{
                return $settings;
              }
          break;
        }

    }        

	/**
	 * Retrieve the stylesheet name of the previewed theme.
	 *
	 * @since 3.4.0
	 *
	 * @return string Stylesheet name.
	 */
	public function get_stylesheet() {
		return $this->wp_theme->get_stylesheet();
	}

	/**
	 * Return true if it's an AJAX request.
	 *
	 * @since 3.4.0
	 *
	 * @return bool
	 */
	public function doing_ajax() {
		return isset( $_POST['sed_page_customized'] ) || ( defined( 'DOING_SITE_EDITOR_AJAX' ) && DOING_SITE_EDITOR_AJAX );
	}

	/**
	 * Custom wp_die wrapper. Returns either the standard message for UI
	 * or the AJAX message.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $ajax_message AJAX return
	 * @param mixed $message UI message
	 */
	public function sed_die( $message = null ) {
        if ( is_scalar( $message ) )
            die( (string) $message );
        die( '0' );
	}

    /*public function set_settings(){
        $this->settings = ;
    } */

    function site_editor_app_save(){
        global $sed_apps;

		if ( ! $sed_apps->editor_manager->is_preview() )
			die;

       check_ajax_referer( 'sed_app_save_' . $this->get_stylesheet(), 'nonce' );

       if( isset($_POST['sed_page_customized']) && isset($_POST['sed_posts_content'])){

           /*
           start save all pages base settings
           */
           $all_pages_settings = array();
           if( isset($_POST['other_pages_customized']) ){
               $other_pages_settings = json_decode( wp_unslash( $_POST['other_pages_customized'] ), true );
               if( is_array( $other_pages_settings ) )
                   $all_pages_settings = $other_pages_settings;
           }

           $sed_pages_types = json_decode( wp_unslash( $_POST['sed_pages_types'] ), true );
           //save all page options
           $sed_page_customized = json_decode( wp_unslash( $_POST['sed_page_customized'] ), true );

           if( is_array($sed_page_customized) )
               $all_pages_settings[ $_POST['sed_page_id'] ] = $sed_page_customized;

           foreach( $all_pages_settings AS $page_id => $page_settings ){

               $base_settings_values = $page_settings;

               //update dynamic css file related to this page
               //$sed_apps->sed_add_dynamic_css_file( $_POST['sed_page_id'] , $_POST['sed_page_type'] , $page_settings );

               foreach ( $sed_apps->editor_manager->settings() as $id => $setting ) {
                   if($setting->option_type != "base" && !empty( $setting->option_type ) ){
                       //$setting->save();
                       unset( $base_settings_values[$id] );  //$setting->id
                   }
               }
                                                                              //$_POST['sed_page_type']
               $this->sed_update_settings( $base_settings_values , $page_id , $sed_pages_types[$page_id] );
           }
           /*
           end save all pages base settings
           */

           //save general settings( general settings is option_type != "base" OR option_type not empty  )
           foreach ( $sed_apps->editor_manager->settings() as $id => $setting ) {
               if($setting->option_type != "base" && !empty( $setting->option_type ) ){
                   //var_dump( $id );
                   $setting->save();
               }
           }

           /*
           start save all posts(posts , pages , custom post type single post , ... ) content
           */

           $all_posts_content_models = json_decode( wp_unslash( $_POST['sed_posts_content'] ), true );
           $all_posts_content = array();

           foreach($all_posts_content_models AS $post_id => $shortcodes){

               $shortcodes = apply_filters("sed_content_save_pre" , $shortcodes );

               if(!empty($shortcodes)){
                   $tree_shortcodes = $this->build_tree_shortcode( $shortcodes , "root" );
                   $content = $this->create_shortcode_content( $tree_shortcodes , array() , $post_id );
               }else{
                   $content = "";
               }
               $all_posts_content[ $post_id ] = $content;
           }

           //all post content saved in this version only , on after versions when added post edit , this saved @deprecated
           if(!empty($all_posts_content)){
               foreach( $all_posts_content AS $post_id => $content ){

                  $post = array(
                      'ID'           => $post_id,
                      'post_content' => $content,
                  );

                  // Update the post into the database
                  wp_update_post( $post );

               }
           }

           /*
           end save all posts(posts , pages , custom post type single post , ... ) content
           */

           do_action( "sed-app-save-data" , $sed_page_customized , $all_posts_content );

           do_action( "sed-app-save-after" );

           do_action( "sed-app-save-response" );

           $this->sed_die( "Success" );

       }else{
           $this->sed_die( -2 );
       }

    }

    function build_tree_shortcode(&$elements, $parentId = "root") {
        $branch = array();

        if( !empty( $elements ) ){
            foreach ($elements as $key => $element) {
                if ($element['parent_id'] == $parentId) {
                    $children = $this->build_tree_shortcode($elements, $element['id']);
                    if ($children) {
                        $element['children'] = $children;
                    }
                    $branch[] = $element;
                    //unset($elements[$key]);
                }
            }
        }
        return $branch;
    }

    function create_shortcode_content( $tree_shortcodes , $tree_path , $post_id = 0 ){
        global $site_editor_app;

        $post_id = (int) $post_id ;

        $contents = '';
        foreach($tree_shortcodes AS $shortcode){
            $attrs_string = "";
            if(!empty( $shortcode['attrs'] )){
                foreach($shortcode['attrs'] AS $attr => $value){
                    if( $attr == "sed_css" && !empty( $value ) ){

                        $new_values = array();

                        foreach( $value AS $selector => $css_data ){
                            preg_match('/\[\s*(sed_model_id)\s*=\s*["\']?([^"\']*)["\']?\s*\]/', $selector, $matches);
                            $selector = str_replace( $matches[0] , "##sed_custom_class##" , $selector  );
                            $new_values[$selector] = $css_data;
                        }

                        $attrs_string .= $attr."='".rawurlencode( json_encode( $new_values ) ) ."' "; //json_encode()
                        continue;
                    }
                    //not support any id attr in shortcodes --- only added in Editor
                    if( $attr != "sed_model_id" )
                        $attrs_string .= $attr.'="'.PageBuilderApplication::sanitize_attr_value( $value ).'" ';
                }
            }

            if($shortcode['tag'] != "content"){

                $shortcode_tag = $shortcode['tag'];

                if( in_array( $shortcode['tag'] , $tree_path ) ){
                    //global $site_editor_app;
                    $helper_shortcodes = $site_editor_app->pagebuilder->get_helper_shortcodes();

                    $helper_shortcodes = ( $helper_shortcodes && is_array( $helper_shortcodes ) ) ? $helper_shortcodes : array();

                    //generate new helper tag
                    $new_tag = $this->generate_helper_shortcodes( $shortcode['tag'] , $tree_path );

                    //update new tag in db
                    if( !in_array( $new_tag , array_keys( $helper_shortcodes ) ) ){
                        $helper_shortcodes[$new_tag] = $shortcode['tag'];
                        $site_editor_app->pagebuilder->update_helper_shortcodes( $helper_shortcodes );
                    }

                    if( $post_id > 0 ){
                        $post_helper_shortcodes = $site_editor_app->pagebuilder->get_post_helper_shortcodes( $post_id );

                        $post_helper_shortcodes = ( $post_helper_shortcodes && is_array( $post_helper_shortcodes ) ) ? $post_helper_shortcodes : array();

                        if( !in_array( $new_tag , array_keys( $post_helper_shortcodes ) ) ){
                            $post_helper_shortcodes[$new_tag] = $shortcode['tag'];
                            $site_editor_app->pagebuilder->update_post_helper_shortcodes( $post_id , $post_helper_shortcodes );
                        }
                    }

                    $shortcode_tag = $new_tag;
                    $attrs_string .= 'shortcode_tag="'. $shortcode['tag'] .'" ';
                }

                $contents .= '['.$shortcode_tag . ' ' . $attrs_string .']';

                if(isset($shortcode['children'])){
                    $new_path = $tree_path;
                    array_push( $new_path , $shortcode_tag);
                    $contents .= $this->create_shortcode_content( $shortcode['children'] , $new_path , $post_id );
                }

                $contents .= '[/'.$shortcode_tag.']';

            }else{
                $contents .= $shortcode['content'];
            }
        }

        return $contents;
    }

    //generate helper shortcodes
    function generate_helper_shortcodes( $shortcode_tag , $tree_path ){
        $new_tag = $shortcode_tag . "_inner";
        if( in_array( $new_tag , $tree_path ) )
            return $this->generate_helper_shortcodes( $new_tag , $tree_path );
        else
            return $new_tag;
    }

    function sed_update_settings($settings , $sed_page_id = "general_home" , $sed_page_type = "general"){
        if($sed_page_type == "post")
            $option_name = 'sed_post_settings' ;
        else
            $option_name = 'sed_'. $sed_page_id .'_settings' ;

        $settings = apply_filters( 'sed_update_settings' , $settings , $option_name , $sed_page_id  , $sed_page_type );

        $this->sed_update_page_options($settings , $option_name , $sed_page_id  , $sed_page_type );
    }

} 



