<#

counter = 0;

var length_class;
if(length == "boxed")
    length_class = "sed-row-boxed";
else
    length_class = "sed-row-wide";

#>
<nav {{sed_attrs}} class="{{className}} module module-breadcrumbs breadcrumbs-default">
    <div class="{{length_class}}" length_element>
        <ul>
           <# if( !_.isEmpty( breadcrumbs ) ){ #>
            <# _.each( breadcrumbs , function( item , index ){ #>
                <li <# if ( _.isEmpty( item.href ) ){ #> class="current" <# } #> >
                <# if ( !_.isEmpty( item.href ) ){ #>
                    <a href="{{item.href}}" <# if( !_.isUndefined( item.type ) && item.type == 'home' ){ #> class="home-breadcrumb" <# } #>>
                        {{{item.text}}}
                    </a>
                <# }else{ #>
                    <span <# if( !_.isUndefined( item.type ) && item.type == 'home' ){ #> class="home-breadcrumb" <# } #>>
                        {{{item.text}}}
                    </span>
                <# } #>
                </li>
            <# counter++; }); #>
            <# } #>
        </ul>
    </div>  
</nav> 