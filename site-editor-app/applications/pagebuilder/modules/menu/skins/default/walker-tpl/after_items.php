<?php if( $show_search || site_editor_app_on() || ( isset( $_REQUEST['sed_page_ajax'] ) && $_REQUEST['sed_page_ajax'] == "sed_load_modules" ) ) : ?>
<li class="sed-menu-item-search menu-item menu-item-has-children dropdown menu-flyout <?php if( !$show_search ) echo "hide"; ?>" >
     <a  data-toggle="dropdown" class="dropdown-toggle item-menu-toggle" ><span class="fa fa-search menu-item-icon"></span></a>
     <span class=" sed-menu-arrow fa fa-angle-down dropdown-toggle" data-toggle="dropdown"></span>
     <ul class="dropdown-menu">
         <li>
           <div>
             <div class="menu-search">
               <form class="menu-searchform"  role="search" method="get" action="<?php echo site_url(); ?>">
                 <input class="menu-search-input form-control"  name="s" type="search" placeholder="<?php _e("Search","site-editor")?>" >
                 <div class="menu-search-submit">
                   <i class="fa fa-search"></i>
                </div>
               </form>
             </div>
           </div>
         </li>
     </ul>
</li>
<?php endif;?>
<?php if( $show_cart || site_editor_app_on() || ( isset( $_REQUEST['sed_page_ajax'] ) && $_REQUEST['sed_page_ajax'] == "sed_load_modules" ) ) : ?>
<li class="sed-menu-item-cart menu-item menu-item-has-children dropdown menu-flyout <?php if( !$show_cart ) echo "hide"; ?>" >
     <a  data-toggle="dropdown" class="dropdown-toggle item-menu-toggle shopping-cart-item" ><span class="fa fa-shopping-cart menu-item-icon"><div class="sed-woo-shopping-cart-count shopping-cart-count"><?php echo WC()->cart->get_cart_contents_count();?></div></span></a>
     <span class=" sed-menu-arrow fa fa-angle-down dropdown-toggle" data-toggle="dropdown"></span>
     <ul class="dropdown-menu">                                                                                                                    
         <li>
           <div class="shopping_cart_in_menu">
              <div class="hide_cart_widget_if_empty">
                 <div class="widget_shopping_cart_content">
                    <?php
                        if( isset( $_REQUEST['sed_page_ajax'] ) && $_REQUEST['sed_page_ajax'] == "sed_load_modules" ){
                            echo __("only product appeare in site or first time after loaded site editor , after change menu settings not display real cart" , "site-editor");
                        }
                    ?>
                 </div>
              </div>
           </div>
         </li>
     </ul>
</li>
<?php endif;?>