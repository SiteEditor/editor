<header {{sed_attrs}} class="s-tb-sm module module-header header-default {{className}}">
      {{{content}}}
    <# if( sticky ){ #>
    <div class="init-sticky-header"></div>
    <header class="sticky-header" id="header-sticky">
        <div class="sticky-header-inner" >
            <div class="sticky-header-row sed-row-boxed">
                <div class="logo" id="sticky-logo">

                </div>
                <nav class="nav-holder" id="sticky-nav">

                </nav>
            </div>
        </div>
    </header>
    <# } #>
</header>