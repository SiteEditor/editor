<div {{sed_attrs}} class="module module-separator separator-skin-default {{class}}">
	<div class="separator {{type}} {{border_style}}"></div> 
	<style type="text/css">

		[sed_model_id="{{sed_model_id}}"].module-separator .separator {
		    border-color: {{separator_color}}; 
		}

		[sed_model_id="{{sed_model_id}}"] .spr-vertical {
			min-height: {{vertical_height}}px;
		}

		[sed_model_id="{{sed_model_id}}"] .spr-horizontal {
			max-width: {{max_width}}px;
		}

		[sed_model_id="{{sed_model_id}}"] .separator.spr-horizontal  {
		    border-width: {{separator_width}}px 0 0 0; 
		}

		[sed_model_id="{{sed_model_id}}"] .separator.spr-horizontal.spr-double {
		    border-width: {{separator_width}}px 0 {{separator_width}}px 0 ;
		}

		[sed_model_id="{{sed_model_id}}"] .separator.spr-vertical  { 
		    border-width: 0 0 0 {{separator_width}}px; 
		}

		[sed_model_id="{{sed_model_id}}"] .separator.spr-vertical.spr-double {
		    border-width: 0 {{separator_width}}px 0 {{separator_width}}px ;
		}  

	</style>
</div>
