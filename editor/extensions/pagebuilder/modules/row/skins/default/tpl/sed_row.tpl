<#
var lengthClass;
if(length == "boxed")
    lengthClass = "sed-row-boxed";
else
    lengthClass = "sed-row-wide";

if(content) { #>
       <div sed-layout-role="pb-module" class="sed-row-pb sed-bp-element {{className}} {{lengthClass}}" {{sed_attrs}} data-type-row="{{type}}" length_element sed-role="row-pb">{{{content}}}</div>
<# }else{ #>
      <div sed-layout-role="pb-module" class="sed-row-pb sed-bp-element {{className}} {{lengthClass}}" {{sed_attrs}} data-type-row="{{type}}" length_element sed-role="row-pb">
      <div class="empty-row"><span class="drop-module-icon"></span><span class="drop-module-txt">Drop A Module Here</span></div>
      </div>
<# } #>
