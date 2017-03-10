<#

if( _.isUndefined( sed_contextmenu_class ) ){
    sed_contextmenu_class = "module_sed_row_contextmenu_container";
}

#>
<div sed-role="mm-element" {{sed_attrs}} sed-role="module-container" class="sed-bp-module {{className}} {{sed_contextmenu_class}}">
  {{{content}}}
</div>