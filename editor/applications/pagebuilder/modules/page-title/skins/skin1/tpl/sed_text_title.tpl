<#
 if( content === "@@@" ){
    content = "<h3>" + window._sedAppPageTitle + "</h3>";
 }
#>      <!-- content += "<h5>" + window._sedAppSiteTagline + "</h5>"; -->
<{{tag}}  class="page-title-continer sed-title  module module-title {{className}}" {{sed_attrs}}>
      {{{content}}}
</{{tag}}>