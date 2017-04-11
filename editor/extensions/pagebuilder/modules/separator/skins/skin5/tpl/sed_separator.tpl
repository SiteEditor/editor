<div {{sed_attrs}} class="module module-separator separator-skin5 {{class}}">
    <div class="module-separator-inner">
        <div class="separator-inner">
          <div class="spr-container spr-left">
            <div class="{{border_style}} spr-horizontal separator"></div>
          </div>
            <div class="separator-icon special-spr-center"><i class="{{icon}}"></i></div>
          <div class="spr-container spr-right">
            <div class="{{border_style}} spr-horizontal separator"></div> 
          </div>
        </div>
    </div>

    <style type="text/css">

        [sed_model_id="{{sed_model_id}}"].module-separator .separator {
            border-color: {{separator_color}}; 
        }
      
        [sed_model_id="{{sed_model_id}}"] .module-separator-inner {
          max-width: {{max_width}}px;
        }
      
        [sed_model_id="{{sed_model_id}}"] .separator.spr-horizontal  {
            border-width: {{separator_width}}px 0 0 0; 
        }
      
        [sed_model_id="{{sed_model_id}}"] .separator.spr-horizontal.spr-double {
            border-width: {{separator_width}}px 0 {{separator_width}}px 0 ;
        }

    </style>

</div>