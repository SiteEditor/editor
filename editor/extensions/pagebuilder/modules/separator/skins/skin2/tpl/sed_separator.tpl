<div {{sed_attrs}} class="module module-separator separator-skin2 {{class}}">
  <div class="separator-inner">
      {{{content}}}
      <div class="spr-container">
        <div class="{{border_style}} spr-horizontal separator"></div>
      </div>
  </div>
</div>

<style type="text/css">

	[sed_model_id="{{sed_model_id}}"].module-separator .separator {
	    border-color: {{separator_color}}; 
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

</style>
