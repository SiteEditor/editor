<header {{sed_attrs}} sed_role="site-header" class="s-tb-sm module module-header header-default {{className}}">
      <div class="sed-navbar-header">
          <div class="navbar-header-inner">
              <button class="sed-navbar-toggle-">
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <span class="navbar-header-title">Menu</span>
          </div>
      </div>
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
    <div class="sed-sc-nav-header">
      <div class="sed-header-item-search" >
           <a class="" ><span class="fa fa-search menu-item-icon"></span></a>
      </div>
      <div class="sed-header-item-cart" >    
           <a class="shopping-cart-item" >
            <span class="fa fa-shopping-cart menu-item-icon">
              <div class="sed-woo-shopping-cart-count shopping-cart-count">
                
              </div>
            </span>
          </a>
      </div> 
    </div>         

</header>