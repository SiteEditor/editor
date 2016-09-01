<div {{sed_attrs}} class="{{class}} s-tb-sm module module-progress-bar progress-bar-default">
	<div class="progress {{#ifCond type_text "==" "title-progress-bar" }} title-progress-bar {{/ifCond}} {{type}} {{#ifCond type "==" "vertical" }}{{direction_v}}{{/ifCond}} {{#ifCond type "==" "" }}{{direction_h}}{{/ifCond}}
        {{#if striped}} progress-striped {{/if}}
        {{#if active}} active {{/if}} "
        style="{{#ifCond type "==" "vertical" }} width: {{width}}px; height: {{height}}px; {{/ifCond}}{{#ifCond type "==" "" }} height: {{height_h}}px; line-height: {{height_h}}px;{{/ifCond}}">
        <div id="{{sed_model_id}}-pbar" class="progress-bar {{style}}
        {{#if animation_pbar}} six-sec-ease-in-out {{/if}}"
        role="progressbar"
        {{{item_settings}}}
        aria-valuemin="{{valuemin}}"
        aria-valuemax="{{valuemax}}"
        style="{{#ifCond type "==" "" }} line-height: {{height_h}}px; {{/ifCond}}" >
        </div>
        {{{content}}}
    </div>
</div>