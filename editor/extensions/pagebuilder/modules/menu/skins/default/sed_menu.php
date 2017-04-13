
<?php
if($length == "boxed")
    $length_class = "sed-row-boxed";
else
    $length_class = "sed-row-wide";

?>

<div <?php echo $sed_attrs; ?> class="navigation-wrapper module module-menu module-menu-skin-defult <?php echo  $class;?>">
    <div class="navigation-wrapper-inner <?php echo  $length_class;?>" length_element>
        <div class="navbar-toggle-wrap">
            <div class="navbar-toggle">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span> 
                <span class="icon-bar"></span> 
            </div>
            <span class="navbar-header-title">Menu</span>
        </div>    
        <div class="navbar-wrap">
            <nav class="navbar-wrap-inner">
    			<?php  
                    wp_nav_menu(array(
                        'menu'           => $menu, 
                    ));
                ?>
    		</nav>
        </div>
    </div>
</div>

