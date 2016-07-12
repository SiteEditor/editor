<div {{sed_attrs}} class="sed-button module module-button button-skin5 {{class}}" sed-role="mm-element">
	<a href="{{#ifCond link "===" ''}}javascript: void(0);{{/ifCond}}{{link}}" target="{{link_target}}" class="sed-button btn {{type}} {{size}}" title="{{title}}">
        <div class="icon-button-pd button-icon"><i class="{{icon}}"></i></div>
		{{{content}}}
	</a>
</div>