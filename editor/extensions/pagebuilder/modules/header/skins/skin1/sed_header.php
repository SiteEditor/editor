<header <?php echo $sed_attrs; ?> sed_role="site-header" class="sed-stb-sm module module-header header-skin1  <?php echo $class;?>">
      <div class="sed-navbar-header">
          <div class="navbar-header-inner">
              <button class="sed-navbar-toggle">
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <span class="navbar-header-title">Menu</span>
          </div>
      </div>
      <?php echo $content ?>
    <?php if( $sticky ){ ?>
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
    <?php } ?>
    <div class="sed-sc-nav-header">
        <div class="sed-header-item-search" >
             <a href="#sed_header_search_mobile_page" class="sed-go-mobile-btn"><span class="fa fa-search menu-item-icon"></span></a>
              <div id="sed_header_search_mobile_page" class="sed-mobile-page sed-mobile-page-theme-a" data-role="mobile-page" >
                <div class="sed-header-mobile-page">
                    <span class="sed-mobile-page-close fa fa-close"><?php echo __("Close" , "site-editor");?></span>
                    <h4><?php echo __("Search" , "site-editor");?></h4>
                </div>
                <div class="sed-mobile-page-content">

                    <?php
                    //$search_lbl = __("Search" , "site-editor");
                    $search_placeholder_lbl = __("Search your product" , "site-editor");
                    $search_no_result = __("No Product Found" , "site-editor");
                    $submit_button_label = __("Search" , "site-editor");

                    echo do_shortcode('[woocommerce_product_search limit="5" show_description="yes" show_price="yes" placeholder="'.$search_placeholder_lbl.'" no_results="'.$search_no_result.'" characters="2" floating="no" submit_button="yes" submit_button_label="'.$search_lbl.'"]');
                    ?>
                </div>

              </div>
        </div>

          <div class="sed-header-item-cart" >
          <?php
          global $woocommerce;
          $cart_url = $woocommerce->cart->get_cart_url();
          ?>
               <a href="<?php echo $cart_url;?>" class="shopping-cart-item" >
                <span class="fa fa-shopping-cart menu-item-icon">
                  <div class="sed-woo-shopping-cart-count shopping-cart-count">
                    <?php echo WC()->cart->get_cart_contents_count();?>
                  </div>
                </span>
              </a>
          </div>
    </div>    
</header>