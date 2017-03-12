<?php
$counter = 0;

if($length == "boxed")
    $length_class = "sed-row-boxed";
else
    $length_class = "sed-row-wide";

?>
<nav <?php echo $sed_attrs; ?> class="<?php echo $class ?> module module-breadcrumbs breadcrumbs-default">
    <div class="<?php echo $length_class;?>"  length_element> 
        <ul> 
           <?php if( !empty( $breadcrumbs ) ): ?>
            <?php foreach ( $breadcrumbs as $index => $item ): ?>
                <li <?php if ( empty( $item['href'] ) ): ?> class="current" <?php endif;?>>
                <?php if ( !empty( $item['href'] ) ): ?>
                    <a href="<?php echo $item['href']; ?>" class="<?php if( isset( $item["type"] ) && $item["type"] == 'home' ) echo 'home-breadcrumb'; ?>">
                        <span><?php echo $item['text']; ?></span>
                    </a>
                <?php else: ?>
                    <span class="<?php if( isset( $item["type"] ) && $item["type"] == 'home' ) echo 'home-breadcrumb'; ?>">
                        <span><?php echo $item['text']; ?></span>
                    </span>
                <?php endif;?>
                </li>
            <?php $counter++;endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>    
</nav>