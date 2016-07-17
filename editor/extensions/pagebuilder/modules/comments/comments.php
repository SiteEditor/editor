<?php

/*
* Module Name: Comments
* Module URI: http://www.siteeditor.org/modules/comments
* Description: Comments Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

class PBCommentsShortcode extends PBShortcodeClass{

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_comments",                          //*require
                "title"       => __("Comments","site-editor"),            //*require for toolbar
                "description" => __("","site-editor"),
                "icon"        => "icon-comments",                         //*require for icon toolbar
                "module"      => "comments"                              //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
        );
    }
    static function comment_form( $args = array(), $post_id = null ) {
        global $current_module;
        $base_url = dirname( $current_module['comments-template'] ) . DS . 'comments-template' . DS . 'comment_form';

        if ( null === $post_id )
            $post_id = get_the_ID();
        else
            $id = $post_id;



        $commenter = wp_get_current_commenter();
        $user = wp_get_current_user();
        $user_identity = $user->exists() ? $user->display_name : '';

        $args = wp_parse_args( $args );
        if ( ! isset( $args['format'] ) )
            $args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';

        $req      = get_option( 'require_name_email' );
        $aria_req = ( $req ? " aria-required='true'" : '' );
        $html5    = 'html5' === $args['format'];

        $tpl_fields = array(
            "author",'email','url'
        );
        $fields   =  array(
            'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
                        '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
            'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
                        '<input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
            'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label> ' .
                        '<input id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
        );

        foreach ( $tpl_fields as $slug ) {
            $tpl = $base_url . DS . $slug . '.php';
            if( is_file( $tpl ) ){
                ob_start();
                include $tpl;
                $fields[$slug] = ob_get_contents();
                ob_end_clean();
            }
            $tpl = '';
        }
        

        $required_text = sprintf( ' ' . __('Required fields are marked %s'), '<span class="required">*</span>' );

        /**
         * Filter the default comment form fields.
         *
         * @since 3.0.0
         *
         * @param array $fields The default comment fields.
         */
        $fields = apply_filters( 'comment_form_default_fields', $fields );

        $defaults = array(
            'fields'               => $fields,
            'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun' ) . '</label> <textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',
            /** This filter is documented in wp-includes/link-template.php */
            'must_log_in'          => '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
            /** This filter is documented in wp-includes/link-template.php */
            'logged_in_as'         => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), get_edit_user_link(), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
            'comment_notes_before' => '<p class="comment-notes">' . __( 'Your email address will not be published.' ) . ( $req ? $required_text : '' ) . '</p>',
            'comment_notes_after'  => '<p class="form-allowed-tags">' . sprintf( __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s' ), ' <code>' . allowed_tags() . '</code>' ) . '</p>',
            'id_form'              => 'commentform',
            'id_submit'            => 'submit',
            'title_reply'          => __( 'Leave a Reply' ),
            'title_reply_to'       => __( 'Leave a Reply to %s' ),
            'cancel_reply_link'    => __( 'Cancel reply' ),
            'label_submit'         => __( 'Post Comment' ),
            'format'               => 'xhtml',
        );

        $other_tpl = array(
            'comment_field','must_log_in','logged_in_as','comment_notes_before','comment_notes_after'
        );
        foreach ( $other_tpl as $slug ) {
            $tpl = $base_url . DS . $slug . '.php';
            if( is_file( $tpl ) ){
                ob_start();
                include $tpl;
                $defaults[$slug] = ob_get_contents();
                ob_end_clean();
            }
            $tpl = '';
        }

        /**
         * Filter the comment form default arguments.
         *
         * Use 'comment_form_default_fields' to filter the comment fields.
         *
         * @since 3.0.0
         *
         * @param array $defaults The default comment form arguments.
         */
        $args = wp_parse_args( $args, apply_filters( 'comment_form_defaults', $defaults ) );

        if ( comments_open( $post_id ) ) {
            $tpl = $base_url . DS . 'form.php';
            if( is_file( $tpl ) ){
                include $tpl;
            }
        }else{
            /**
             * Fires after the comment form if comments are closed.
             *
             * @since 3.0.0
             */
            do_action( 'comment_form_comments_closed' );
        }


    }
    static function get_comment_reply_link($args = array(), $comment = null, $post = null) {

        $defaults = array(
            'add_below'  => 'comment',
            'respond_id' => 'respond',
            'reply_text' => __('Reply'),
            'login_text' => __('Log in to Reply'),
            'depth'      => 0,
            'before'     => '',
            'after'      => '',
            'class'      => ''
        );

        $args = wp_parse_args($args, $defaults);

        if ( 0 == $args['depth'] || $args['max_depth'] <= $args['depth'] )
            return;

        extract($args, EXTR_SKIP);

        $comment = get_comment($comment);
        if ( empty($post) )
            $post = $comment->comment_post_ID;
        $post = get_post($post);

        if ( !comments_open($post->ID) )
            return false;

        $link = '';

        if( get_option('comment_registration') && ! is_user_logged_in() ){
            $link = '<a rel="nofollow" class="comment-reply-login ' . $args['class'].'" href="' . esc_url( wp_login_url( get_permalink() ) ) . '">' . $login_text . '</a>';
        }else{
            $link = "<a class='comment-reply-link ". $args['class']."' href='" . esc_url( add_query_arg( 'replytocom', $comment->comment_ID ) ) . "#" . $respond_id . "' onclick='return addComment.moveForm(\"$add_below-$comment->comment_ID\", \"$comment->comment_ID\", \"$respond_id\", \"$post->ID\")'> <i class='fa fa-reply'></i> $reply_text</a>";
        }

        /**
         * Filter the comment reply link.
         *
         * @since 2.7.0
         *
         * @param string  $link    The HTML markup for the comment reply link.
         * @param array   $args    An array of arguments overriding the defaults.
         * @param object  $comment The object of the comment being replied.
         * @param WP_Post $post    The WP_Post object.
         */
        return apply_filters( 'comment_reply_link', $before . $link . $after, $args, $comment, $post );
    }

    function get_atts(){
        $atts = array(
          'default_width' => "200px" ,
          'default_height' => "300px"
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
        global $current_module;

		if ( is_singular() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

        $current_module['comments-template'] = dirname( __FILE__ ) . DS . 'skins' . DS . $atts['skin'] . DS . 'comments-template.php';
        include_once dirname( __FILE__ ) . DS . 'WalkerSEDComments.php';
    }

    function shortcode_settings(){

        $params = array(
            'skin'      => 'skin_refresh',
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

        return $params;

    }

    function custom_style_settings(){
        return array(

            array(
            'comment-body' , '.comment-body ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Comment Container" , "site-editor") ) ,

            array(
            'comment-author-avatar' , '.comment a > img ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Comment Author Avatar" , "site-editor") ) ,

            array(
            'author-name' , '.comment cite.fn ' ,
            array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Author Name" , "site-editor") ) ,

            array(
            'reply' , '.comment-reply-link ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Reply" , "site-editor") ) ,

            array(
            'reply-hover' , '.comment-reply-link:hover ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Reply Hover" , "site-editor") ) ,

            array(
            'comment-text' , '.comment-text' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Comment Text  Container" , "site-editor") ) ,

            array(
            'arrow' , '.comment-arrow::after' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Arrow" , "site-editor") ) ,

            array(
            'form-control' , '.form-control' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align'  ) , __("Form Item" , "site-editor") ) ,

            array(
            'form-control-focus' , '.form-control:focus' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Form Item Focus" , "site-editor") ) ,

            array(
            'form-submit' , '.form-submit button' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align'  ) , __("Form Submit" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
        $comments_menu = $context_menu->create_menu( "comments" , __("Comments","site-editor") , 'comments' , 'class' , 'element' , ''  , "sed_comments" , array(
            "seperator"    => array(75),
            "duplicate"    => false
        ));
    }

}

new PBCommentsShortcode;
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "base" ,
    "name"        => "comments",
    "title"       => __("Comments","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-comments",
    "shortcode"   => "sed_comments",
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    //"js_module"   => array( 'sed_image_module_script', 'image/js/image-module.min.js', array('site-iframe') )
));



