<div {{sed_attrs}} class="{{class}} s-tb-sm module module-progress-bar progress-bar-skin2">
  <div class="progress-outer {{#ifCond type "==" "vertical" }} {{type}}-outer {{/ifCond}}">
	<div class="progress {{type}} {{#ifCond type "==" "vertical" }}{{direction_v}}{{/ifCond}} {{#ifCond type "==" "" }}{{direction_h}}{{/ifCond}}  
        {{#if striped}} progress-striped {{/if}}
        {{#if active}} active {{/if}} "
        style="{{#ifCond type "==" "vertical" }} width: {{width}}px; height: {{height}}px; {{/ifCond}}{{#ifCond type "==" "" }} height: {{height_h}}px; line-height: {{height_h}}px;{{/ifCond}}">
        <div id="{{sed_model_id}}-pbar" class="progress-bar {{style}}
        {{#if animation_pbar}} six-sec-ease-in-out {{/if}}
        {{#ifCond type_text "==" "title-progress-bar" }} title-progress-bar {{/ifCond}}"
        role="progressbar"
        {{{item_settings}}}
        aria-valuemin="{{valuemin}}"
        aria-valuemax="{{valuemax}}" style="{{#ifCond type "==" "" }} line-height: {{height_h}}px; {{/ifCond}}" >
            {{{content}}}
        </div>
    </div>
  </div>
</div>