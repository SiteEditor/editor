<# if( link ){ #>

    <a href="{{link}}" target="{{link_target}}" {{sed_attrs}} class=" sed-icons module module-single-icon single-icon-default {{className}}">
        <div class="hi-icon {{icon}}" sed-icon="{{icon}}">
        </div>
    </a>

<# }else{ #>

    <div {{sed_attrs}} class=" sed-icons module module-single-icon single-icon-default {{className}}">
        <div class="hi-icon {{icon}}" sed-icon="{{icon}}">
        </div>
    </div>

<# } #>

