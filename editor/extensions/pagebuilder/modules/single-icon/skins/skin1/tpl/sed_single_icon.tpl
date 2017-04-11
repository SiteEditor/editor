<# if( link ){ #>

    <a href="{{link}}" target="{{link_target}}" {{sed_attrs}} class=" sed-icons module module-single-icon single-icon-skin1  {{className}} ">

<# }else{ #>

    <div {{sed_attrs}} class=" sed-icons module module-single-icon single-icon-skin1 {{className}}">

<# } #>
        <div class="hi-icon {{icon}}" sed-icon="{{icon}}"></div>

        <style type="text/css">
            [sed_model_id="{{sed_model_id}}"].module.module-single-icon .hi-icon {
                border: {{border_size}}px solid {{border_color}};
            }
        </style>

<# if( link ){ #>

    </a>

<# }else{ #>

    </div>

<# } #>

