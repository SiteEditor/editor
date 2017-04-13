<div {{sed_attrs}} class="{{className}}  module spcial-bar social-bar-skin2">
	<ul class="social-bar-{{layout_mode}}">
		{{{content}}}
	</ul>
	<style type="text/css">
        <# if( layout_mode == "vertical" ){ #>
        	[sed_model_id="{{sed_model_id}}"] li{
        		padding-bottom: {{margin}}px;
        	}
        <# }else{ #>
        	[sed_model_id="{{sed_model_id}}"] li{
        		padding-right: {{margin}}px;
        	}
        <# } #>
	</style>
</div>
