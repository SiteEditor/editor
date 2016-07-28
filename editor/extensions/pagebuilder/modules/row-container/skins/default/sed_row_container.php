<?php

$bg_type = 'other';
$outer_html = "";

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

        $outer_html .= sprintf( '<div class="%s" style=""></div>', 'fullwidth-overlay' , $style );
    }

    $outer_html .= sprintf( '<div class="%s"><video %s>%s</video></div>', 'fullwidth-video', $video_attributes, $video_src );

    if( $video_preview_image ) {
        $video_preview_image_style = sprintf('background-image:url(%s);', $video_preview_image );
        $outer_html .= sprintf( '<div class="%s" style="%s"></div>', 'fullwidth-video-image', $video_preview_image_style );
    }
}


if(!empty($content)){?>
       <div class="s-tb-sm row-container-module <?php echo $class.' '. $arrow.' '.$responsive_option;?> <?php if($overlay){?>row-overlay<?php } ?> <?php if($full_height){?>row-flex row-full-height<?php } ?> <?php echo $length_class;?>" <?php echo $sed_attrs; ?> length_element>
            <div class="sed-pb-component" <?php if( site_editor_app_on() ) echo 'data-parent-id="' . $sed_model_id . '"'; ?>>
                <?php echo $content; ?>
            </div>
            <?php echo $outer_html; ?>  
       </div>
<?php }else{  ?>
      <div class="s-tb-sm row-container-module <?php echo $class.' '. $arrow.' '.$responsive_option;?> <?php if($overlay){?>row-overlay<?php } ?> <?php if($full_height){?>row-flex row-full-height<?php } ?> <?php echo $length_class;?>" <?php echo $sed_attrs; ?> length_element>
          <div class="sed-pb-component" <?php if( site_editor_app_on() ) echo 'data-parent-id="' . $sed_model_id . '"'; ?> drop-placeholder="<?php echo __('Drop A Module Here','site-editor'); ?>">

          </div>
          <?php echo $outer_html;?>
      </div>
<?php } ?>
<?php
    global $sed_dynamic_css_string;
    $selector = ( site_editor_app_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
    ob_start();
    ?>
        <?php
        if(!empty($responsive_spacing)){
        ?>
          @media (max-width: 768px){
              <?php echo $selector; ?> > .sed-pb-component > .sed-row-pb > .sed-pb-module-container{
                  padding : <?php echo $responsive_spacing; ?> !important;
              }
          }
        <?php  
        }
        if($arrow == "row-arrow-bottom"){
        ?>
        <?php echo $selector; ?>.row-arrow-bottom::after{
            border-bottom: <?php echo $arrow_size; ?>px solid <?php echo $arrow_color; ?>;
            border-left: <?php echo $arrow_size; ?>px solid transparent;
            border-right: <?php echo $arrow_size; ?>px solid transparent;
            /*margin-bottom: -<?php echo $arrow_size; ?>px;*/
            margin-left: -<?php echo $arrow_size; ?>px;
        }
        <?php
        }
        if($arrow == "row-arrow-top"){
        ?>
        <?php echo $selector; ?>.row-arrow-top::after {
            border-top: <?php echo $arrow_size; ?>px solid <?php echo $arrow_color; ?>;
            border-left: <?php echo $arrow_size; ?>px solid transparent;
            border-right: <?php echo $arrow_size; ?>px solid transparent;
            /*margin-top: -<?php echo $arrow_size; ?>px;*/
            margin-left: -<?php echo $arrow_size; ?>px;
        }
        <?php
        }
        if($overlay){
        ?>
        <?php echo $selector; ?>.row-overlay::before{
            background-color: <?php echo $overlay_color; ?>;
            opacity: <?php echo $overlay_opacity/100; ?>;
        }
        <?php
        }
        ?>
    <?php
    $css = ob_get_clean();
    $sed_dynamic_css_string .= $css;
