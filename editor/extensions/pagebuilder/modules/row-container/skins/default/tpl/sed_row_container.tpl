<#
var lengthClass;
if(length == "boxed")
    lengthClass = "sed-row-boxed";
else
    lengthClass = "sed-row-wide";

var bg_type = 'other' ,
    outer_html = "";

if( video_mp4 || video_ogg || video_webm ) {
    bg_type = 'video';
    className += " video-background";
}

if( bg_type == 'video' ) {
    var video_attributes = 'preload="auto" autoplay' ,
        video_src = '';

    if( video_loop === true ) {
        video_attributes += ' loop';
    }

    if( video_mute === true ) {
        video_attributes += ' muted';
    }

    video_attributes += ' id="' + sed_model_id + '_video"';

    if( video_mp4 ) {
        video_src += '<source src="' + video_mp4 + '" type="video/mp4">';
    }

    if( video_ogg ) {
        video_src += '<source src="' + video_ogg + '" type="video/ogg">';
    }

    if( video_webm ) {
        video_src += '<source src="' + video_webm + '" type="video/webm">';
    }

    if( video_overlay_color ) {

        var style = "";

		if( video_overlay_color ) {
			style += 'background-color:' + video_overlay_color + ';' ;
		}

		if( video_overlay_opacity ) {
			style += 'opacity:' + (video_overlay_opacity/100) + ';';
		}

        outer_html += '<div class="fullwidth-overlay" style="' + style + '"></div>';
    }

    outer_html += '<div class="fullwidth-video"><video ' + video_attributes + '>' + video_src + '</video></div>';

    if( video_preview_image ) {
        var video_preview_image_style = 'background-image:url(' + video_preview_image + ');';
        outer_html += '<div class="fullwidth-video-image" style="' + video_preview_image_style + '"></div>';
    }
}


if(content) { #>
       <div class="row-container-module {{responsive_option}} {{className}} <# if(is_arrow){ #> {{arrow}} <# } #> <# if(overlay){ #> row-overlay <# } #> <# if(full_height){ #> row-flex row-full-height <# } #>" {{sed_attrs}}>
            <div class="sed-pb-component {{lengthClass}}" data-parent-id="{{sed_model_id}}" length_element>
                {{{content}}}
            </div>
            {{{outer_html}}}
       </div>
<# }else{ #>
      <div class="row-container-module {{responsive_option}} {{className}}  <# if(is_arrow){ #> {{arrow}} <# } #> <# if(overlay){ #> row-overlay <# } #> <# if(full_height){ #> row-flex row-full-height <# } #>" {{sed_attrs}}>
          <div class="sed-pb-component {{lengthClass}}" data-parent-id="{{sed_model_id}}" drop-placeholder="Drop A Module Here" length_element>

          </div>
          {{{outer_html}}}
      </div>
<# } #>

<style type="text/css">
<# if(responsive_spacing){ #>
    @media (max-width: 768px){

    [sed_model_id="{{sed_model_id}}"] > .sed-pb-component > .sed-row-pb > .sed-pb-module-container{
        padding : {{responsive_spacing}} !important;
    }

    }
<# } #>
<# if(is_arrow){ #>
    <# if(arrow == 'row-arrow-bottom' ){ #>
    [sed_model_id="{{sed_model_id}}"].row-arrow-bottom::after{
        border-bottom: {{arrow_size}}px solid {{arrow_color}};
        border-left: {{arrow_size}}px solid transparent;
        border-right: {{arrow_size}}px solid transparent;
        /*margin-bottom: -{{arrow_size}}px;*/
        margin-left: -{{arrow_size}}px;
    }
    <# } #>
    <# if(arrow == 'row-arrow-top' ){ #>
    [sed_model_id="{{sed_model_id}}"].row-arrow-top::after {
        border-top: {{arrow_size}}px solid {{arrow_color}};
        border-left: {{arrow_size}}px solid transparent;
        border-right: {{arrow_size}}px solid transparent;
        /*margin-top: -{{arrow_size}}px;*/
        margin-left: -{{arrow_size}}px;
    }
    <# } #>
<# } #>
<# if(overlay){ #>
    [sed_model_id="{{sed_model_id}}"].row-overlay.row-container-module::before{
        background-color: {{overlay_color}};
        opacity: {{overlay_opacity/100}};
    }
<# } #>

</style>

