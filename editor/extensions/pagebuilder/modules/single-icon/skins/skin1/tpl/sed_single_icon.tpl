<# if( link ){ #>

    <a href="{{link}}" target="{{link_target}}" {{sed_attrs}} class="sed-stb-sm sed-ta-c sed-icons module module-single-icon single-icon-skin1  {{className}} <# if( style ){ #> {{hover_effect}} <# } #> " style="font-size:{{font_size}}px;">
        <span class="hi-icon {{icon}} {{type}} {{style}}" sed-icon="{{icon}}" style="font-size:{{font_size}}px;color:{{color}}">
        </span>
    </a>

<# }else{ #>

    <div {{sed_attrs}} class="sed-stb-sm sed-ta-c sed-icons module module-single-icon single-icon-skin1 {{className}} <# if( style ){ #> {{hover_effect}} <# } #>" style="font-size:{{font_size}}px;">
        <span class="hi-icon {{icon}} {{type}} {{style}}" sed-icon="{{icon}}" style="font-size:{{font_size}}px;color:{{color}}">
        </span>
    </div>

<# } #>

<style type="text/css">
<# if( background_color && !style ){ #>
[sed_model_id="{{sed_model_id}}"] .hex-icon:before,
[sed_model_id="{{sed_model_id}}"] .icon-ring,
[sed_model_id="{{sed_model_id}}"] .icon-default {
    background-color: {{background_color}};
}

<# } #>
<# if( border_color && !style ){ #>
    [sed_model_id="{{sed_model_id}}"] .icon-default,
    [sed_model_id="{{sed_model_id}}"] .icon-flat,
    [sed_model_id="{{sed_model_id}}"] .icon-ring:after {
        box-shadow:0 0 0 0.07em {{border_color}};
    }
<# } #>
</style>

