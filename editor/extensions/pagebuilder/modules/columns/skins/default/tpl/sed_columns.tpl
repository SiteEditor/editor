<table class="s-tb-sm sed-cols-table">
<tr {{sed_attrs}} class="sed-columns-pb {{responsive_option}} {{class}}" sed-role="column-pb">
    {{{content}}}
</tr>
<style type="text/css">
{{#if responsive_spacing}}

@media (max-width: 768px){
[sed_model_id="{{sed_model_id}}"] > td >.sed-column-contents-pb > .sed-row-pb > .sed-pb-module-container{
    padding : {{responsive_spacing}} !important;
}
}

{{/if}}
</style>

</table>
