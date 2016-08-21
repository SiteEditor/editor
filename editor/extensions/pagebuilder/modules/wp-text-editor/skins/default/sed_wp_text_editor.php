<?php
function wpb_js_remove_wpautop( $content, $autop = false ) {

    if ( $autop ) { // Possible to use !preg_match('('.WPBMap::getTagsRegexp().')', $content)
        $content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );
    }

    return do_shortcode( shortcode_unautop( $content ) );
}

?>
<div <?php echo $sed_attrs; ?> class="s-tb-sm module module-wp-text-editor <?php echo $class;?> ">
    <?php echo wpb_js_remove_wpautop( $content, true ) ; ?>
</div>
