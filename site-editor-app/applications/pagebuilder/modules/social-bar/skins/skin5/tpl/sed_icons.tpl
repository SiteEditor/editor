<li {{sed_attrs}} class="module module-icons {{className}} ">
<# if( link ){ #> 
	<a class="social-icon" href="{{link}}" target="{{link_target}}">
<# }else{ #> 
    <a class="social-icon" href="javascript:void(0);">   
<# } #>  
		<span class="hi-icon {{icon}}" sed-icon="{{icon}}" style="font-size:{{font_size}}px;color:{{color}}"></span>
	</a>
</li>
