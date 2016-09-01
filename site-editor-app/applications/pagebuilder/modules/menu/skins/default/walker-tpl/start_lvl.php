<?php
$parent_id = get_menu_parent_id( $this->prev_item->ID );
$parent_is_mega = get_post_meta( $parent_id , "_menu_item_megamenu" , true );
$dropdown_menu ='';
$dropdown_menu_role ='' ;
if($parent_is_mega && $depth > 0 ){
   $dropdown_menu .='';
   $dropdown_menu_role .='';
}else{
   $dropdown_menu .='dropdown-menu';
   $dropdown_menu_role .='role="menu"';

}

$width_submenu = get_post_meta( $this->prev_item->ID , '_menu_item_width-submenu', true );
$full_width_submenu = get_post_meta( $this->prev_item->ID , '_menu_item_full-width-submenu', true );
$data_width = '';                                    //   var_dump($width_submenu);
$width = '';

if( ( ( isset( $_REQUEST['sed_page_ajax'] ) && $_REQUEST['sed_page_ajax'] == "sed_load_modules" ) || site_editor_app_on() ) && $full_width_submenu &&  !empty($width_submenu)  ){
    $data_width = 'data-submenu-width = "'.$width_submenu.'"';
}

if($this->menu_atts['orientation'] == 'vertical'){

    if( $parent_is_mega &&  $depth == 0 &&  !empty($width_submenu) ){
        $width = 'style = "width:'.$width_submenu.';"';
    }

}elseif($this->menu_atts['orientation'] == 'horizontal' && !$full_width_submenu  ){

    if( $parent_is_mega &&  $depth == 0 &&  !empty($width_submenu)  ){
        $width = 'style = "width:'.$width_submenu.';"';
    }

}
 //  var_dump($data_v_width);




?>
<ul data-depth="<?php echo $depth ?>" <?php echo $dropdown_menu_role ?> class=" <?php echo $class_names.' '.$dropdown_menu?>" data-parent="<?php echo $parent_id ?>" <?php echo $width;?> <?php echo $data_width;?> >
<?php if( $parent_is_mega && $depth == 0 ) :
$back_img =  get_post_meta( $parent_id, '_menu_item_background-image', true ) ;
if( $back_img ){
    $style = 'style="';
    $style .= 'background-image: url('.$back_img.');';
    $back_pos =  get_post_meta( $parent_id, '_menu_item_background-position', true );
    if( $back_pos ){
        $style .= 'background-position: '.$back_pos.';';
    }
    $style .= 'background-repeat: no-repeat;"';
}else{
    $style = '';
}
?>
  <li class="megamenu-content">
  <span class="megamenu-drag-area"><i class="fa fa-arrows fa-lg"></i></span>
    <div class="back-row" <?php echo $style;?>></div>
    <ul class="row">

<?php endif;?>
