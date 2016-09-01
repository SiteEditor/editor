<style type="text/css">

#<?php echo $id ?> .item-img span {
    height: <?php echo $image_height ?>px;
    width: <?php echo $image_width ?>px;
}

#<?php echo $id ?> .item-img span img {
    width: <?php echo $image_width ?>px;
    min-width: <?php echo $image_width ?>px;
    min-height: <?php echo $image_height ?>px;
}
@media (min-width: 780px) {
#<?php echo $id ?> .sed-menu-container {
    text-align: <?php echo $navbar_align ?>;
}
}

@media (min-width: 780px) {

#<?php echo $id ?> .vertical-megamenu.navbar-wrap{
    width: <?php echo $vertical_menu_width ?>px;
}

}
#<?php echo $id ?> .menu-draggable-area {
    width: <?php echo $draggable_area_width ?>px;
}

#<?php echo $id ?> .fa.fa-search , #<?php echo $id ?> .fa.fa-shopping-cart {
  font-family: "FontAwesome" !important;
}

</style>
<?php
if($length == "boxed")
    $length_class = "sed-row-boxed";
else
    $length_class = "sed-row-wide";



$class_draggable_area="";

if( $enable_draggable_area && $draggable_area_direction == "left" ){

    $class_draggable_area="menu-draggable-area-left";

}elseif( $enable_draggable_area && $draggable_area_direction == "right" ){

    $class_draggable_area="menu-draggable-area-right";

}

?>


<div <?php echo $sed_attrs; ?> class="<?php echo $class.' '.$sticky_styles ?> <?php echo ( $orientation == 'vertical'  ? 'vertical-megamenu' : '' )  ?> navbar-wrap module module-megamenu megamenu-skin2 " >
    <div class="container-megamenu <?php echo $length_class;?> <?php if( $enable_draggable_area ) echo "menu-has-draggable-area";?>" length_element >
        <?php
        if( $enable_draggable_area){
            $matches = PBMenuShortcode::get_menu_content( $content , "menudragarea" );
        ?>
        <div id="<?php echo $matches[3][0];?>">
            <div class="bp-component menu-draggable-area <?php echo $class_draggable_area; ?>"  data-parent-id="<?php echo $matches[3][0]; ?>"  drop-placeholder="<?php echo __('Drop A Module Here','site-editor'); ?>" >
              <?php echo $matches[4][0];?>
            </div>
        </div>
        <?php
          }
        ?>
        <div class="navbar-container">
            <div class="navbar-header">
                <div class="navbar-header-inner">
                    <button type="button" data-toggle="collapse" data-target="#<?php echo $id ?>-navbar-toggle" class="navbar-toggle">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <span class="navbar-header-title">Menu</span>
                </div>
            </div>
            <nav class="navbar navbar-inverse megamenu" role="navigation" >
                <div id="<?php echo $module_html_id; ?>-navbar-toggle" class="navbar-collapse collapse">
    			<?php
                    $args = $PBMenuShortcode->get_menu_args( $content );
                    wp_nav_menu( $args );
                ?>
                </div>
    		</nav>
        </div>
    </div>
</div>

<script>
// Menu drop down effect
jQuery(document).ready(function($){

    $('#<?php echo $id ?>').sedmegamenu({
        isSticky                : <?php if( $sticky == true ) echo "true"; else echo "false"; ?>,
        trigger                 : '<?php echo $trigger; ?>', // click || hover
        delay                   : <?php echo $delay_hover; ?>,
        orientation             : "<?php echo $orientation; ?>" ,
        scrollAnimate           : "<?php echo $scroll_animate_anchor; ?>" ,
        scrollAnimateDuration   : <?php echo $scroll_animate_duration; ?> ,
        isVerticalFixed         : <?php if($is_vertical_fixed) echo "true"; else echo "false";?>
    });

    <?php if( site_editor_app_on() || ( isset( $_REQUEST['sed_page_ajax'] ) && $_REQUEST['sed_page_ajax'] == "sed_load_modules" ) ){ ?>
        $(window).unbind('scroll.sticky');
    <?php }  ?>

    $('#<?php echo $id ?>').on('hide.bs.dropdown' , function(){

        var sedmegamenu = $('#<?php echo $id ?>').data('sed.sedmegamenu');

        if( sedmegamenu.activeDragArea === true )
            return false;

    });

    $('#<?php echo $id ?>').find(".dropdown.menu-flyout").on('hide.bs.dropdown' , function(){

        var trigger = $('#<?php echo $id ?>').sedmegamenu("option" , "trigger");

        //if(trigger == "click"){
            $(this).find(".dropdown-submenu").addClass("closed-submenu").removeClass("opened-submenu");
            $(this).find(".dropdown-submenu > .dropdown-menu").hide();
        //}

    });

    $('#<?php echo $id ?>').find(".dropdown").on('show.bs.dropdown' , function(){
        var sedmegamenu = $('#<?php echo $id ?>').data('sed.sedmegamenu');

        if( !sedmegamenu.responsive )
            sedmegamenu.repositionSubmenu( $(this) , true );

        if(sedmegamenu.options.orientation == "horizontal" && !sedmegamenu.responsive  )
            sedmegamenu.repositionMegamenu( this );
    });

    $('#<?php echo $id ?>').find(".dropdown").on('show.bs.dropdown' , function(){
        if( !$(this).data("sedDropdownActivated") ){
            $(this).find(".sed-row-pb > .sed-pb-module-container").trigger("sedFirstTimeMegamenuActivated" , [$(this)]);
            $(this).data("sedDropdownActivated" , "active");
        }
    });

    $('#<?php echo $id ?>').find(".dropdown").on('hide.bs.dropdown' , function(){

        var trigger = $('#<?php echo $id ?>').sedmegamenu("option" , "trigger");

        if(trigger == "hover")
            return false;
    });

    /*$('#<?php echo $id ?>').sedmegamenu("destroy");

    $('#<?php echo $id ?>').sedmegamenu({
        trigger : "click"
    });*/


});
</script>





