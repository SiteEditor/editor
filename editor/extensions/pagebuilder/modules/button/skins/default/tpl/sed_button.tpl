<# if(outline){ var btn_type = type + '-outline'; }else{ btn_type = type } #>
<div {{sed_attrs}} class="sed-button module module-button skin-default {{className}} ">
	<a href="<# if(link == ""){ #>javascript: void(0);<# }else{ link } #>" target="{{link_target}}" class="btn {{btn_type}} {{size}} <# if(full_width){ #>btn-block<# } #>" title="{{title}}">
		{{{content}}}
	</a>
</div>