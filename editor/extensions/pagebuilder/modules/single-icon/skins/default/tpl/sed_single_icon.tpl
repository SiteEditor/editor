<# if( link ){ #>

    <a href="{{link}}" target="{{link_target}}" {{sed_attrs}} class=" sed-icons module module-single-icon single-icon-default  {{className}} ">
        <span class="hi-icon {{icon}}" sed-icon="{{icon}}" style="font-size:{{font_size}}px;color:{{color}}">
        </span>
    </a>

<# }else{ #>

    <div {{sed_attrs}} class=" sed-icons module module-single-icon single-icon-default  {{className}} ">
        <span class="hi-icon {{icon}}" sed-icon="{{icon}}" style="font-size:{{font_size}}px;color:{{color}}">
        </span>
    </div>

<# } #>

