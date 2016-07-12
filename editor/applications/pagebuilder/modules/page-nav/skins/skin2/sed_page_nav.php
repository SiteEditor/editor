<?php global $sed_data;
$hide = '';
if( isset( $sed_data['archive_pagination_type'] ) && $sed_data['archive_pagination_type'] != "pagination" ){
    $hide = "hide";
}

global $current_module;

if( isset($current_module['custom_wp_query']) )
    $items = PBPageNavShortcode::get_nav_items( $current_module['custom_wp_query'] );
else
    $items = PBPageNavShortcode::get_nav_items();
?>
<nav <?php echo $sed_attrs; ?> class="<?php echo $class . ' '. $hide.' '.$align_page_nav ?> module module-pagination pagination-skin2 " >
<?php if ( !empty( $items ) ): ?>
    <ul>
        <?php foreach ( $items as $index => $item ): ?>
            <li <?php if ( isset( $item['class_item'] ) ) echo 'class="'.$item['class_item'] . '"' ?>>
                <?php if ( isset( $item['class_link'] ) && $item['class_link'] == "current-page"):?>
                    <a href="#" class="current-page" title="<?php echo  $item['title'] ?>">
                        <?php if ( isset( $item['text'] ) ) echo $item['text'] ?>
                    </a>
                <?php else:?>
                    <a href="<?php echo  $item['link'] ?>" <?php if ( isset( $item['icon'] ) ) echo 'class="'.$item['icon'] . '"' ?> title="<?php echo  $item['title'] ?>">
                        <?php if ( isset( $item['text'] ) ) echo $item['text'] ?>
                    </a>
                <?php endif;?>
            </li>
        <?php endforeach ?>
    </ul>
<?php endif ?>
</nav>