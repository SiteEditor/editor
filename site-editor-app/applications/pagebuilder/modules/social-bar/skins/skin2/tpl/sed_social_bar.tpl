<div {{sed_attrs}} class="{{className}} s-tb-sm ta-c module spcial-bar social-bar-skin2">
	<ul class="social-bar-{{layout_mode}}">
		{{{content}}}
	</ul>
	<style type="text/css">
        <# if( layout_mode == "vertical" ){ #>
        	[sed_model_id="{{sed_model_id}}"] ul{
        		margin-bottom: -{{margin}}px;
        	}
        	[sed_model_id="{{sed_model_id}}"] li{
        		padding-bottom: {{margin}}px;
        	}
        <# }else{ #>
        	[sed_model_id="{{sed_model_id}}"] ul{
        		margin-right: -{{margin}}px;
        	}
        	[sed_model_id="{{sed_model_id}}"] li{
        		padding-right: {{margin}}px;
        	}
        <# } #>
	</style>
</div>
