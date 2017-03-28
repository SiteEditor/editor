
<?php
if($length == "boxed")
    $length_class = "sed-row-boxed";
else
    $length_class = "sed-row-wide";

?>

<div <?php echo $sed_attrs; ?> class="navigation-wrapper module module-megamenu megamenu-defult <?php echo  $class;?>"> 
    <div class="navbar-toggle-wrap">
        <div class="navbar-toggle">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span> 
            <span class="icon-bar"></span> 
        </div>
        <span class="navbar-header-title">Menu</span>
    </div>    
    <div class="navbar-wrap" length_element>
        <nav class="navbar navbar-inverse megamenu" role="navigation" >
			<?php wp_nav_menu(); ?>
		</nav>
    </div>
</div>

