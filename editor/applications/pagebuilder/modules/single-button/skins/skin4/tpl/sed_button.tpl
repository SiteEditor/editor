<div {{sed_attrs}} class="sed-button module module-button button-skin4 {{class}}" >
	<a href="{{#ifCond link "===" ''}}javascript: void(0);{{/ifCond}}{{link}}" target="{{link_target}}" class="sed-button btn {{type}} {{size}}" title="{{title}}">
        <div class="button-icon icon-button-df"><i class="{{icon}}"></i></div>
		{{{content}}}
	</a>
</div>