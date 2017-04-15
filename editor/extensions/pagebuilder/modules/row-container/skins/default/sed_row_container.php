<?php

$bg_type = 'other';
$outer_html = "";

if( $video_mp4 > 0 ){
    if( get_post( $video_mp4 ) ) {
        $video_mp4 = wp_get_attachment_url( $video_mp4 );
    }
}

if( $video_ogg > 0 ){
    if( get_post( $video_ogg ) ) {
        $video_ogg = wp_get_attachment_url( $video_ogg );
    }
}

if( $video_webm > 0 ){
    if( get_post( $video_webm ) ) {
        $video_webm = wp_get_attachment_url( $video_webm );
    }
}

if( $video_mp4 || $video_ogg || $video_webm ) {
    $bg_type = 'video';
    $class .= " video-background";
}

if( $bg_type == 'video' ) {
    $video_attributes = 'preload="auto" autoplay';
    $video_src = '';

    if( $video_loop === true ) {
        $video_attributes .= ' loop';
    }

    if( $video_mute === true ) {
        $video_attributes .= ' muted';
    }

    $video_attributes .= ' id="' . $module_html_id . '_video"';

    if( $video_mp4 ) {
        $video_src .= sprintf( '<source src="%s" type="video/mp4">', $video_mp4 );
    }

    if( $video_ogg ) {
        $video_src .= sprintf( '<source src="%s" type="video/ogg">', $video_ogg );
    }

    if( $video_webm ) {
        $video_src .= sprintf( '<source src="%s" type="video/webm">', $video_webm );
    }

    if( $video_overlay_color ) {

        $style = "";

		if( $video_overlay_color ) {
			$style .= sprintf( 'background-color:%s;', $video_overlay_color );
		}

		if( $video_overlay_opacity ) {
			$style .= sprintf( 'opacity:%s;', $video_overlay_opacity/100 );
		}

        $outer_html .= sprintf( '<div class="%s" style="%s"></div>', 'fullwidth-overlay' , $style );
    }

    $outer_html .= sprintf( '<div class="%s"><video %s>%s</video></div>', 'fullwidth-video', $video_attributes, $video_src );

    if( $video_preview_image ) {

        if( $video_preview_image > 0 ){
            if( get_post( $video_preview_image ) ) {
                $video_preview_image = wp_get_attachment_url( $video_preview_image );
            }
        }

        $video_preview_image_style = sprintf('background-image:url(%s);', $video_preview_image );
        $outer_html .= sprintf( '<div class="%s" style="%s"></div>', 'fullwidth-video-image', $video_preview_image_style );
    }
}


?>

    <div class="row-container-module <?php echo $class;?> <?php if($is_arrow) echo $arrow; ?> <?php if($overlay){?>row-overlay<?php } ?> <?php if($full_height){?>row-flex row-full-height<?php } ?>" <?php echo $sed_attrs; ?>>

        <?php if(!empty($content)){?>

            <div class="sed-pb-component <?php echo $length_class;?>" <?php if( site_editor_app_on() ) echo 'data-parent-id="' . $sed_model_id . '"'; ?> length_element>
                <?php echo $content; ?>
            </div>

        <?php }else{  ?>

            <div class="sed-pb-component <?php echo $length_class;?>" <?php if( site_editor_app_on() ) echo 'data-parent-id="' . $sed_model_id . '"'; ?> drop-placeholder="<?php echo __('Drop A Module Here','site-editor'); ?>" length_element></div>

        <?php } ?>

        <?php echo $outer_html; ?>  

        <?php
            $selector = ( site_editor_app_on() || sed_loading_module_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
            ob_start();
        ?>
            <?php

            if($is_arrow){

                if( $arrow == "row-arrow-bottom" ){
                    
                ?>

                <?php echo $selector; ?>.row-arrow-bottom::after {
                    border-bottom: <?php echo $arrow_size; ?>px solid <?php echo $arrow_color; ?>;
                    border-left: <?php echo $arrow_size; ?>px solid transparent;
                    border-right: <?php echo $arrow_size; ?>px solid transparent;
                    /*margin-bottom: -<?php //echo $arrow_size; ?>px;*/
                    margin-left: -<?php echo $arrow_size; ?>px;
                }

                <?php

                }

                if( $arrow == "row-arrow-top" ){

                ?>

                <?php echo $selector; ?>.row-arrow-top::after {
                    border-top: <?php echo $arrow_size; ?>px solid <?php echo $arrow_color; ?>;
                    border-left: <?php echo $arrow_size; ?>px solid transparent;
                    border-right: <?php echo $arrow_size; ?>px solid transparent;
                    /*margin-top: -<?php //echo $arrow_size; ?>px;*/
                    margin-left: -<?php echo $arrow_size; ?>px;
                }

                <?php

                }

            }

            if( $overlay ){

            ?>
                <?php echo $selector; ?>.row-overlay.row-container-module::before {
                    background-color: <?php echo $overlay_color; ?>;
                    opacity: <?php echo $overlay_opacity/100; ?>;
                }

            <?php

            }

            ?>

        <?php
            $css = ob_get_clean();
            sed_module_dynamic_css( $css );
        ?>

    </div>

