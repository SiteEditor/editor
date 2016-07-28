<?php

$area_module = get_post_meta( $item->ID , '_menu_item_area-module', true );
$column_title_megamenu = get_post_meta( $item->ID , '_menu_item_column-title-megamenu', true );
$icon = get_post_meta( $item->ID , '_menu_item_icon', true );
$thumb_src = get_post_meta( $item->ID , '_menu_item_thumb', true );
$hide_title = get_post_meta( $item->ID , '_menu_item_hide-title', true );
$disable_link = get_post_meta( $item->ID , '_menu_item_disable-link', true );
$fullwidth_submenu = get_post_meta( $item->ID , '_menu_item_full-width-submenu', true );
$desc = $item->description;
$parent_id = get_menu_parent_id( $item->ID );
$alt = isset( $item->attr_title ) ? $item->attr_title : $item_title;

$parent_is_megamenu = get_post_meta( $parent_id , '_menu_item_megamenu' , true );

//var_dump( $args );
// class for li tag
$class_li  = array();
$class_li[] = $with_desc      =  ( !empty($desc) ) ? 'with-desc' : "";
$class_li[] = $with_icon      =  ( !empty($icon) && empty($desc) ) ? 'with-icon ' : "";
$class_li[] = $with_desc_icon =  ( !empty($icon) && !empty($desc) ) ? 'with-desc-icon ' : "";
$class_li[] = $with_img       =  ( !empty( $thumb_src ) && empty($desc) ) ? 'with-img ' : "";
$class_li[] = $with_desc_img  =  ( !empty( $thumb_src ) && !empty($desc) ) ? 'with-desc-img ' : "";
$class_li[] = $item_normal    =  ( empty( $thumb_src ) && empty($desc) && empty($icon) && $depth > 0 ) ? 'item-normal' : "";



$toggle_class = '';
$data_toggle  = '';
if ( $args->has_children &&  $depth == 0 ) {
   $class_li[] = 'dropdown';
   $toggle_class    .= 'dropdown-toggle';
   $data_toggle     .= 'data-toggle="dropdown"';
}


              //menu_item_parent
if ( $args->has_children && empty($parent_is_megamenu) && $depth > 0 ) {
   $class_li[] = "dropdown-submenu";
}


if( $parent_is_megamenu && $fullwidth_submenu &&  $depth == 0){
    $class_li[] = 'megamenu-fw';
}elseif( $parent_is_megamenu && !$fullwidth_submenu &&  $depth == 0){
    $class_li[] = 'megamenu-half';
}


if( !$parent_is_megamenu &&  $depth == 0){
    $class_li[] = 'menu-flyout';
}



$title ='';
$columns_width = '';
if( $parent_is_megamenu && $depth == 1 && isset( $args->parent_megamenu_children) && is_numeric( $args->parent_megamenu_children ) && $args->parent_megamenu_children > 0 ){
    $columns = $args->parent_megamenu_children;
    $col_width = (100 / $columns);
    $columns_width = 'style="width:'.$col_width.'%;"';
    $class_li[] = 'columns';

    if( $column_title_megamenu === 'column-title'){

      $title .= 'title';

      if($disable_link){
          $title .=" disabled-title";
      }

    }elseif($column_title_megamenu === 'hide-item-submenu'){

     $title .= 'hide';

    }else{

     $title .= '';

    }

}

if( $parent_is_megamenu && $depth == 1 && $area_module && $column_title_megamenu === 'hide-item-submenu' && isset( $args->parent_megamenu_children) && is_numeric( $args->parent_megamenu_children ) && $args->parent_megamenu_children > 0 ){
   $class_li[] ='reset-padding-drag-area';
}

if( $parent_is_megamenu && $depth == 1 && $area_module && isset( $args->parent_megamenu_children) && is_numeric( $args->parent_megamenu_children ) && $args->parent_megamenu_children > 0 ){
   $class_li[] ='hide-item-submenu';
}

if( !$args->has_children &&  $depth == 0){
    $class_li[] = 'without-submenu';
}

if( $this->menu_atts['display_description'] == false ){
    $display_description_class = "megamenu-display-description";
}else{
    $display_description_class = "";
}


if( ($this->menu_atts['img_align'] == 'top' || $this->menu_atts['img_align'] == 'bottom') && !empty( $thumb_src ) && (empty($icon) || ($this->menu_atts['image_icon_preference'] == 'image')) ){
    $class_li[] = 'img-align';
}


if( ($this->menu_atts['icon_align'] == 'top' || $this->menu_atts['icon_align'] == 'bottom') && !empty($icon) && (empty($thumb_src) || $this->menu_atts['image_icon_preference'] == 'icon') ){
    $class_li[]  = 'icon-align';
}

$icon_hide='';
$image_hide='';
if($this->menu_atts['image_icon_preference'] == 'image' && !empty($icon) && !empty( $thumb_src )  ){
    $icon_hide = 'hide';
}elseif($this->menu_atts['image_icon_preference'] == 'icon' && !empty($icon) && !empty( $thumb_src ) ){
    $image_hide = 'hide';
}

$hide_title_class='';
if(!empty( $hide_title )  ){
    $hide_title_class = 'hide';
}else{
    $hide_title_class = '';
}

if( is_rtl() && ($this->menu_atts['orientation'] == 'vertical') && $args->has_children &&  $depth == 0 )
    $class_li[]  ='dropdown-vertical-submenu-right';


if ( (is_rtl() &&  ($this->menu_atts['orientation'] == 'horizontal') && $args->has_children && !$parent_is_megamenu  ) || (is_rtl() &&  ($this->menu_atts['orientation'] == 'vertical') && $args->has_children && !$parent_is_megamenu && $depth > 0) ) {
   $class_li[] = "dropdown-submenu-left";
}

$classes_li = implode(" " , $class_li);

?>


<li id="nav-menu-item-<?php echo $item->ID; ?>" class=" <?php echo $class_names .' '. $depth_class_names  .' '.$classes_li ?>" <?php if(!empty($columns_width)) echo $columns_width ?>>
<?php if ( ! empty( $item->url) ): ?>
    <a <?php echo $data_toggle  ?>   class="<?php echo $toggle_class.' '.$title.' '.$display_description_class   ?>"    href="<?php echo ( !empty( $item->url )  && !$disable_link  ? $item->url : "#" )  ?>" <?php
            echo 'data-description="' . ( ! empty($desc) ? $desc : '' ) . '" ';


        if ( ! empty( $item->attr_title ) )
            echo ' title="' . esc_attr( $item->attr_title ) .'"';

        if ( ! empty( $item->target ) )
            echo ' target="' . esc_attr( $item->target ) .'"';

        if ( ! empty( $item->xfn ) )
            echo ' rel="' . esc_attr( $item->xfn ) .'"';
    ?>>

    <?php if( (! empty($thumb_src)) && $this->menu_atts['img_align'] !== 'right' && $this->menu_atts['img_align'] !== 'bottom'  ):?>
      <div class="item-img <?php echo $image_hide; ?>">
          <span class="">
              <img class="" alt="<?php echo $alt; ?>" src="<?php echo $thumb_src; ?>">
          </span>
      </div>
    <?php endif ?>

        <?php echo $args->link_before ?>
        <?php if ( (! empty( $icon)) && $this->menu_atts['icon_align'] !== 'right' && $this->menu_atts['icon_align'] !== 'bottom'): ?>
            <i class="fa menu-item-icon <?php echo $icon ?> <?php echo $icon_hide; ?>" style="font-size: <?php echo $this->menu_atts['icon_font_size']?>px;"></i>
        <?php endif ?>
        <span class="<?php echo ( $depth > 0 ? 'megamneu-target-title' : '' ) ?> <?php echo $hide_title_class ?> ">
            <?php echo $item_title ?>
        </span>

        <?php if ( ! empty( $icon) && ($this->menu_atts['icon_align'] == 'right' || $this->menu_atts['icon_align'] == 'bottom') ): ?>
            <i class="fa menu-item-icon <?php echo $icon ?> <?php echo $icon_hide; ?>" style="font-size: <?php echo $this->menu_atts['icon_font_size']?>px;"></i>
        <?php endif ?>

      <?php if( (! empty($thumb_src)) && ($this->menu_atts['img_align'] == 'right' || $this->menu_atts['img_align'] == 'bottom')  ):?>
      <div class="item-img <?php echo $image_hide; ?>">
          <span class="">
              <img class="" alt="<?php echo $alt; ?>" src="<?php echo $thumb_src; ?>">
          </span>
      </div>
    <?php endif ?>

<?php endif ?>
<?php if ( ! empty( $item->url ) ): ?>
    <?php echo $args->link_after ?>
    </a>
<?php endif;

if ( ($args->has_children && $depth == 0 ) || ($args->has_children && $depth > 0 && !$parent_is_megamenu ) ){
?>
<span class=" sed-menu-arrow fa fa-angle-down dropdown-toggle" data-toggle="dropdown"></span>
<?php
}

if( $parent_is_megamenu && $depth == 1 && !empty( $area_module ) && isset( $args->parent_megamenu_children) && is_numeric( $args->parent_megamenu_children ) && $args->parent_megamenu_children > 0 ) :
?>
<div id="megamenu-module-widget-area-<?php echo $item->ID; ?>">
    <div class="sed-pb-component megamenu-module-widget-area" data-menu-id="<?php echo $this->menu_atts['id'];?>" data-parent-id="megamenu-module-widget-area-<?php echo $item->ID; ?>"  drop-placeholder="<?php echo __('Drop A Module Here','site-editor'); ?>" >
         <?php echo PBMenuShortcode::get_menu_content( $this->menu_content , "megamenudragarea" , "megamenu-module-widget-area-".$item->ID ); ;?>
    </div>
</div>
<?php endif;   ?>


