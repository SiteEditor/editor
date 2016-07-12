<?php
$css = new SiteEditorCss();

$css->add_settings_property(array(
    'background_gradient' , 'background_image_scaling', 'length' ,'border_radius_tr' ,
    'border_radius_tl', 'border_radius_br', 'border_radius_bl' ,'trancparency' ,
    'shadow_color', 'shadow', 'border_side', 'margin_top' , 'margin_bottom' , 'margin_left' ,
    'margin_right' , 'padding_top' , 'padding_bottom' , 'padding_left' , 'padding_right' ,
    'position' , 'background_color' , 'border_top_color' , 'border_top_width' , 'border_top_style' ,
    'border_right_color' , 'border_right_width' , 'border_right_style' , 'border_bottom_color' , 'border_bottom_width' ,
    'border_bottom_style' , 'border_left_color' , 'border_left_width' , 'border_left_style' ,
    'background_image' , 'background_attachment' , 'background_position' , 'font_color' ,
    'font_family','font_size','font_weight','font_style','text_decoration' ,'text_align' ,
    'line_height' , 'text_shadow_color' , 'text_shadow' ,
));


$style_properties = array();
$elements = array();
$retina_css = "";
global $sed_apps;
$css_data = $sed_apps->dynamic_css_data;

if(!empty($css_data)){
    foreach( $css_data AS $selector => $styles ){
        foreach( $styles AS $property => $value){
            if(in_array($property , $css->get_settings_properties())){
                $elements[$selector][] = array('property' => $property , 'value' => $value);
            }

            if( $property == "background_image" ){
                $src = $value;
                if( $src == "none" || empty( $src ) || !$src )
                    continue;

                $pos = strpos( $src , "?attachment_id=" );

                if( $pos > -1 ){
                    $src = substr( $src , 0 , $pos );
                }

                if( substr($src, -5) == '.jpeg' ){
                    $retina_bg_src = substr($src , 0 , strlen( $src ) - 5  )."@2x.jpeg";
                }else if( substr($src, -4) == '.png' ){
                    $retina_bg_src = substr($src , 0 , strlen( $src ) - 4  )."@2x.png";
                }else if( substr($src, -4) == '.gif' ){
                    $retina_bg_src = substr($src , 0 , strlen( $src ) - 4  )."@2x.gif";
                }else if( substr($src, -4) == '.jpg' ){
                    $retina_bg_src = substr($src , 0 , strlen( $src ) - 4  )."@2x.jpg";
                }else if( substr($src, -4) == '.bmp' ){
                    $retina_bg_src = substr($src , 0 , strlen( $src ) - 4  )."@2x.bmp";
                }

                $upload_dir = wp_upload_dir();
                $retina_file = str_replace( $upload_dir['baseurl'] , $upload_dir['basedir'] , $retina_bg_src );

                if($selector != "default" && file_exists( $retina_file )){
                     ob_start();
                    ?>
                	@media only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min-device-pixel-ratio: 2) {
                		<?php echo $selector;?> {
                			background-image: url(<?php echo $retina_bg_src; ?>);
                		}
                	}
                    <?php
                    $retina_version = ob_get_contents();
                    ob_end_clean();

                    $retina_css .= $retina_version;
                }
            }

        }
    }
}

$output_css = '';
//var_dump( $elements );
if(!empty($elements)){
    foreach($elements AS $selector => $element_styles ){
        $output_css .= $selector . "{" . $css->output_standard_css( $element_styles ) . "}";
    }
}

global $sed_data , $sed_dynamic_css_string;

/*if(!empty($sed_data) && isset($sed_data['page_main_col'])){
    $output_css .= '@media screen and (min-width: 846px) {';
    $page_main_cols = $sed_data['page_main_col'];
    if(!empty($page_main_cols)){
        foreach($page_main_cols AS $col_id => $col){
           if($col_id != "default"){
              $col['id'] = $col_id;
              $output_css .= "#". $col_id . "{width : " . ($col['settings']['width'] * 100) . "% }";
           }
        }
    }
    $output_css .= '}';
}*/

if(!empty($sed_data) && isset($sed_data['sheet_width']))
    $output_css .= ".sed-row-boxed{max-width : " . $sed_data['sheet_width'] . "px !important;}";

echo $output_css.$sed_dynamic_css_string.$retina_css;

?>