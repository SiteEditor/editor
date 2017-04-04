<#
var length_class;
if(length == "boxed")
    length_class = "sed-row-boxed";
else
    length_class = "sed-row-wide";

var pageTitle = window._sedAppPageTitle;
#>

<div {{sed_attrs}} sed_role="page-title-bar" class="module module-page-title page-title-default {{className}}">

    <div class="page-title-inner {{length_class}}" length_element>

        <div class="page-title-continer">

            <h3> {{{pageTitle}}} </h3>

            <# if( show_sub_title ){ #>

                <p> {{{sub_title}}} </p>

            <# } #>

        </div>

    </div>

</div>