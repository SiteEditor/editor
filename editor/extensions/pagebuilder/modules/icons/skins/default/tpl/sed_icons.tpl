<# if( link ){ #>

    <a href="{{link}}" target="{{link_target}}" {{sed_attrs}} class=" sed-icons module module-icons icons-default {{className}}">
        <div class="hi-icon {{icon}}" sed-icon="{{icon}}">
        </div>
    </a>

<# }else{ #>

    <div {{sed_attrs}} class=" sed-icons module module-icons icons-default {{className}}">
        <div class="hi-icon {{icon}}" sed-icon="{{icon}}">
        </div>
    </div>

<# } #>


