<#
var length_class;
if(length == "boxed")
    length_class = "sed-row-boxed";
else
    length_class = "sed-row-wide";
#>
<div {{sed_attrs}} sed_role="page-title-bar" class="s-tb-sm module module-page-title page-title-skin3 {{className}}">
    <div class="page-title-inner {{length_class}}" length_element>
        {{{content}}}
    </div>
</div>