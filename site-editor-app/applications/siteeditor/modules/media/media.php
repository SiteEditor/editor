<?php
/*
Module Name: Media
Module URI: http://www.siteeditor.org/modules/media
Description: Module media For Site Editor Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/
if(!class_exists('SiteEditorMediaManager'))
{
	class SiteEditorMediaManager
	{
        var $module_base_dir;

		function __construct()
		{
            $this->module_base_dir = SED_APPS_PATH. DS . "siteeditor" . DS . "modules" . DS . "media";
            add_action( 'sed_footer' , array($this,'add_tmpls_media_library') );
            //add_filter('sed_custom_js_plugins', array($this,'sed_add_media_plugin') );
            add_action("site_editor_ajax_load_medias", array($this,"library_load_medias") );

            add_filter( "sed_js_I18n", array($this,'js_I18n'));
		}


        function js_I18n( $I18n ){

            $I18n['unknown_artist']         =  __("Unknown Artist","site-editor");
            $I18n['change_audio_library']  =  __("Audio Library","site-editor") ;
            $I18n['change_audio_btn']      =  __("Change Audio","site-editor");
            $I18n['invalid_media_format']  =  __("Media Format Is Not Valid","site-editor");
            $I18n['change_video_library']  =  __("Video Library","site-editor") ;
            $I18n['change_video_btn']      =  __("Change Video","site-editor");
            $I18n['change_file_library']  =  __("Library","site-editor") ;
            $I18n['change_file_btn']      =  __("Change File","site-editor");            

            return $I18n;
        }

        function add_tmpls_media_library(){
            global $sed_apps;
            include $this->module_base_dir . DS . "view" . DS . "media.php";
        }


        function sed_add_media_plugin($plugin_array) {
           $plugin_array['media'] = SED_BASE_URL . 'applications/siteeditor/modules/media/js/mediaPlugin.min.js';
           return $plugin_array;
        }

        function library_load_medias(){
            global $sed_apps;
            $sed_apps->check_ajax_handler('media_loader' , 'sed_app_media_load');

        	if ( ! current_user_can( 'upload_files' ) )
        		wp_send_json_error();

        	$query = isset( $_REQUEST['query'] ) ? (array) $_REQUEST['query'] : array();

            if(!empty($query['post_mime_type']) && !in_array($query['post_mime_type'] , ["image","video","audio","text"])){
                //var_dump( $this->get_posts_mime_types( $query['post_mime_type']) );
                if($mime_type = $this->get_posts_mime_types( $query['post_mime_type'] ))
                    $query['post_mime_type'] = $mime_type;

            }

        	$query = array_intersect_key( $query, array_flip( array(
        		's', 'order', 'orderby', 'posts_per_page', 'paged', 'post_mime_type',
        		'post_parent', 'post__in', 'post__not_in', 'year', 'monthnum'
        	) ) );

        	$query['post_type'] = 'attachment';
        	if ( MEDIA_TRASH
        		&& ! empty( $_REQUEST['query']['post_status'] )
        		&& 'trash' === $_REQUEST['query']['post_status'] ) {
        		$query['post_status'] = 'trash';
        	} else {
        		$query['post_status'] = 'inherit';
        	}

        	if ( current_user_can( get_post_type_object( 'attachment' )->cap->read_private_posts ) )
        		$query['post_status'] .= ',private';

        	/**
        	 * Filter the arguments passed to WP_Query during an AJAX
        	 * call for querying attachments.
        	 *
        	 * @since 3.7.0
        	 *
        	 * @see WP_Query::parse_query()
        	 *
        	 * @param array $query An array of query variables.
        	 */
        	$query = apply_filters( 'ajax_query_attachments_args', $query );
        	$query = new WP_Query( $query );

        	$posts = array_map( 'wp_prepare_attachment_for_js', $query->posts );
        	$posts = array_filter( $posts );

        	wp_send_json_success( $posts );
        }

        function get_posts_mime_types($mType){
            $exts = $this->find_media_type_ext( $mType );
            $def_mime_types = wp_get_mime_types();
            $mime_types = array();
            if(!empty( $exts )){
                foreach($exts AS $ext){
                    if( isset( $def_mime_types[$ext] ) ){
                        $mime_types[] = $def_mime_types[$ext];
                    }
                }
            }

            return array_filter($mime_types);
        }

        function find_media_type_ext($mType){
            global $sed_apps;
            $media_types = $sed_apps->media_types();
            foreach( $media_types AS $type => $settings ){
                if($type == $mType){
                  return $settings['ext'];
                  break;
                }
            }
            return false;
        }

        function media_library_load( $post_mime_type = "all" , $offset = 0 , $posts_per_page = 20 , $keyword = '' , $orderby = 'post_date'){

            $args = array(
            	'posts_per_page'            => (int)$posts_per_page,
            	'offset'                    => (int)$offset,
            	'orderby'                   => $orderby,
            	'order'                     => 'DESC',
            	'post_type'                 => 'attachment',
            	'post_parent'               => null ,
                'post_status'               => 'inherit'
            	//'cache_results'             => true,
            	//'update_post_meta_cache'    => true,
        	 );

        	if ( current_user_can( get_post_type_object( 'attachment' )->cap->read_private_posts ) )
        		$args['post_status'] .= ',private';

             if(!empty($keyword))
                $args['s'] = $keyword;

             if(!empty($post_mime_type) && $post_mime_type != "all")
                $args['post_mime_type'] = $post_mime_type;

             $output = '';

              $query = new WP_Query($args);
              $attachments = $query->posts;
              $ids = array();
              //var_dump($attachments);
              if ($attachments) {
                  foreach ($attachments as $id => $attachment) {
                    if(wp_attachment_is_image( $attachment->ID )){
                        $img = wp_get_attachment_image_src( $attachment->ID , 'thumbnail' );
                        $img_larg = wp_get_attachment_image_src( $attachment->ID , 'large' );
                        if($img){
                            $output .= '<li><a data-media-type="image" data-post-id="'.$attachment->ID.'" class="sed-media-item" href="#"><span class="sed-media-item-selected-icon"></span><span><img class="img-library bttrlazyloading" full-src="'.$img_larg[0].'" src="'. $img[0] .'" data-bttrlazyloading-md-width="99" data-bttrlazyloading-md-height="99"  title="'. $attachment->post_title .'" /></span></a></li>';
                        }
                    }else{
                        $img = wp_get_attachment_image_src( $attachment->ID , '' , true  );
                        $media_url = wp_get_attachment_url($attachment->ID);
                        $filename = esc_html( wp_basename( $attachment->guid ) );
                        $ext = preg_replace('/^.+?\.([^.]+)$/', '$1', $attachment->guid);
                        $ext_type = '';
                        if ( !empty($ext) ) {
                           if ( wp_ext2type( $ext ) )
                               $ext_type = wp_ext2type( $ext );
                        }                   //
                        $output .= '
                        <li>
                          <a data-media-type="'. $ext_type .'" data-post-id="'.$attachment->ID.'" class="sed-media-item" href="#"><span class="sed-media-item-selected-icon"></span><span><img class="img-library media-library bttrlazyloading" full-src="'.$media_url.'" src="'. $img[0] .'" data-bttrlazyloading-md-width="99" data-bttrlazyloading-md-height="99"  title="'. $attachment->post_title .'" /></span>
                            <div class="filename">
            					<div>'. $filename .'</div>
            				</div>
                          </a>
                        </li>';
                    }
                    $ids[] = $attachment->ID;
                  }
              }

              return array( $output , sizeof($attachments) , $ids , $attachments);

        }


    }
}

if(class_exists('SiteEditorMediaManager'))
{
	new SiteEditorMediaManager;
}
