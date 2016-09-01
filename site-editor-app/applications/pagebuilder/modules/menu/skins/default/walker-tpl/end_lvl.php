
<?php 
$parent_id = get_menu_parent_id( $this->prev_item->ID );
$parent_is_mega = get_post_meta( $parent_id , "_menu_item_megamenu" , true );
?>
<?php if( $parent_is_mega && $depth == 0 ) : ?>
        </ul>
    </li>
<?php endif;?>
</ul>

