<#
var is_ssl = window.IS_SSL;
light_theme    = light_theme ? '&amp;theme=light' : '';

autoplay       = ( autoplay ) ?  '&amp;autoplay=1' : '';

loop           = ( loop ) ?  '&amp;loop=1' : '';

protocol       = is_ssl ? 'https' : 'http';

video_url      =  protocol + '://www.youtube.com/embed/' + video_id + '?wmode=transparent' + light_theme + autoplay + loop + api_params;
                             
#>
<div {{sed_attrs}} class="module-youtube s-tb-sm {{className}}" {{has_cover}} >
<div class="youtube-container" >
<iframe class="youtube-player" src="{{{video_url}}}" title="" frameborder="0" allowfullscreen></iframe>
</div>
</div>