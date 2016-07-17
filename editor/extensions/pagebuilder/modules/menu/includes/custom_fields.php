<?php
class SEDAddCustomFields {

    function __construct() {
        if( is_admin() ) {

            add_action( 'admin_enqueue_scripts', array( $this , 'admin_script' ) );

            add_filter( 'wp_edit_nav_menu_walker', array( $this, 'edit_nav_menu_walker' ) );
            add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'option' ), 12, 4 );
            add_action( 'wp_update_nav_menu_item', array( $this, 'update_option' ), 10, 3 );

        } 
    }

    function admin_script($hook){
        if ( 'nav-menus.php' != $hook ) {
            return;
        }
               
        wp_enqueue_script( "sed-admin-script" , SED_PB_MODULES_URL . 'menu/js/admin-script.js' , array('jquery') , '1.0.0' , true );
    }

    /**
     * Custom Walker for menu edit
     *
     * Ideally we wouldn't need this, but WordPress does not provide
     * any hook to add custom fields to menu item edit screen. This function
     * defines our custom walker to be used.
     *
     * @return string custom walker
     * @since 0.1
     */
    function edit_nav_menu_walker( $walker ) {
        require_once( dirname( __FILE__ ) . DS . 'walker-nav-menu-edit.php' );
        return 'SEDWalkerNavMenuEdit';
    }

    function option( $item_id, $item, $depth, $args ) {
            wp_enqueue_media();
        ?>
        <?php
            $checked = get_post_meta( $item_id, '_menu_item_megamenu', true ) ? "checked='checked'" : "" ;
            ?>

        <div id="site-editor-megamenu-options">

        <div class="field-active-mega-menu-item condition-depth-field description description-wide">
            <label for="sed-menu-item-megamenu-<?php echo $item_id; ?>">
                <input type="checkbox" id="sed-menu-item-megamenu-<?php echo $item_id; ?>" name="sed-menu-item-megamenu[<?php echo $item_id; ?>]" <?php echo $checked ?> >
                <span><b><?php _e('Active Mega Menu') ?></b></span>
            </label>
        </div>

        <div class="field-background-image-item field-lib-img condition-depth-field description description-wide">
            <label for="sed-menu-item-background-image-<?php echo $item_id; ?>">
                <?php _e('Select Background Image');
                $thumb =  get_post_meta( $item_id, '_menu_item_background-image', true ) ;?>
                 <br />
                <input type="button" name="upload_background-image" id="sed-uploadbgbtn-<?php echo $item_id; ?>" class="button button-primary uploadbgbtn" value="<?php _e("Add Background Image","site-editor") ?>" data-id="sed-menu-item-background-image-<?php echo $item_id; ?>" data-title="<?php _e('Upload Background'); ?>" data-textbt="<?php _e('Insert Background'); ?>" >
                <input type="hidden" id="sed-menu-item-background-image-<?php echo $item_id; ?>" name="sed-menu-item-background-image[<?php echo $item_id; ?>]" value="<?php echo esc_html( $thumb ); ?>">
            </label>
            <span class="sed-image-show">
                <?php if ( !empty( $thumb ) ): ?>
                    <img src="<?php echo $thumb ?>">
                <?php endif ?>
            </span>
            <span class="remove-image sed-remove-image"  data-id="sed-menu-item-background-image-<?php echo $item_id; ?>">
               <?php _e('Remove') ; ?>
            </span>
        </div>

          <?php
              $bg_position = get_post_meta( $item_id, '_menu_item_background-position', true );
              $bg_position = ( $bg_position ) ? $bg_position : "";
          ?>
          <div class="field-background-position-item condition-depth-field description description-wide">
              <label for="sed-menu-item-background-position-<?php echo $item_id; ?>">
                  <span><?php _e('Background Position') ?></span>
                  <input type="text" style="direction: ltr" id="sed-menu-item-background-position-<?php echo $item_id; ?>" name="sed-menu-item-background-position[<?php echo $item_id; ?>]" value="<?php echo $bg_position;?>" >
              </label>
          </div>

        <p class="field-icon-item description description-wide">
            <label for="sed-menu-item-icon-<?php echo $item_id; ?>">
                <?php _e('Icon','site-editor') ?>
                <input type="text" id="sed-menu-item-icon-<?php echo $item_id; ?>" class="sed-menu-item-classes" name="sed-menu-item-icon[<?php echo $item_id; ?>]" value="<?php echo esc_html( get_post_meta( $item_id, '_menu_item_icon', true ) ); ?>">
            </label>
        </p>

        <div class="field-thumb-item field-lib-img description description-wide">
            <label for="sed-menu-item-thumb-<?php echo $item_id; ?>">
                <?php _e('Thumbnail') ;
                $thumb =  get_post_meta( $item_id, '_menu_item_thumb', true ) ;?>
                 <br />
                <input type="button" name="upload_thumbnail" id="sed-uploadbt-<?php echo $item_id; ?>" class="button button-primary uploadbt" value="<?php _e("Add Thumbnail","site-editor") ?>" data-id="sed-menu-item-thumb-<?php echo $item_id; ?>" data-title="<?php _e('Upload Thumbnail'); ?>" data-textbt="<?php _e('Insert Thumbnail'); ?>">
                <input type="hidden" id="sed-menu-item-thumb-<?php echo $item_id; ?>" name="sed-menu-item-thumb[<?php echo $item_id; ?>]" value="<?php echo esc_html( $thumb ); ?>">
            </label>
            <span class="sed-image-show">
                <?php if ( !empty( $thumb ) ): ?>
                    <img src="<?php echo $thumb ?>">
                <?php endif ?>
            </span>
            <span class="remove-image sed-remove-image"  data-id="sed-menu-item-thumb-<?php echo $item_id; ?>">
               <?php _e('Remove') ; ?>
            </span>
        </div>
        <?php
            $checked = get_post_meta( $item_id, '_menu_item_hide-title', true ) ? "checked='checked'" : "" ;
         ?>
        <div class="field-menu-item-hide-title description description-wide">
            <label for="sed-menu-item-hide-title-<?php echo $item_id; ?>">
                <input type="checkbox" id="sed-menu-item-hide-title<?php echo $item_id; ?>" name="sed-menu-item-hide-title[<?php echo $item_id; ?>]" <?php echo $checked ?> >
                <span><?php _e('Hide Title') ?></span>
            </label>
        </div>
        <?php
            $checked = get_post_meta( $item_id, '_menu_item_disable-link', true ) ? "checked='checked'" : "" ;
         ?>
        <div class="field-menu-item-disable-link description description-wide">
            <label for="sed-menu-item-disable-link-<?php echo $item_id; ?>">
                <input type="checkbox" id="sed-menu-item-disable-link<?php echo $item_id; ?>" name="sed-menu-item-disable-link[<?php echo $item_id; ?>]" <?php echo $checked ?> >
                <span><?php _e('Disable link') ?></span>
            </label>
        </div>
            <?php
                $checked = get_post_meta( $item_id, '_menu_item_full-width-submenu', true ) ? "checked='checked'" : "" ;
            ?>
            <div class="field-full-width-submenu-item condition-depth-field description description-wide">
                <label for="sed-menu-item-full-width-submenu-<?php echo $item_id; ?>">
                    <input type="checkbox" id="sed-menu-item-full-width-submenu-<?php echo $item_id; ?>" name="sed-menu-item-full-width-submenu[<?php echo $item_id; ?>]" <?php echo $checked ?> >
                    <span><?php _e('Full Width Submenu( For horizontal submenu Only)') ?></span>
                </label>
            </div>
            <span>------<?php _e('OR','site-editor'); ?>--------</span>
            <?php
                $width = get_post_meta( $item_id, '_menu_item_width-submenu', true ) ;
            ?>
            <div class="field-width-submenu-item condition-depth-field description description-wide">
                <label for="sed-menu-item-width-submenu-<?php echo $item_id; ?>">
                    <span><?php _e('Submenu Width( For horizontal and vertical megamenus)') ?></span>
                    <input type="text" id="sed-menu-item-width-submenu-<?php echo $item_id; ?>" name="sed-menu-item-width-submenu[<?php echo $item_id; ?>]" value="<?php echo $width;?>" >
                    <span><?php _e('In Vertical Mode Only Using from px') ?></span>
                </label>
            </div>

        <?php
            $checked = get_post_meta( $item_id, '_menu_item_area-module', true ) ? "checked='checked'" : "" ;
        ?>
            <div class="field-module-area-submenu-item condition-depth-field description description-wide">
                <label for="sed-menu-item-area-module-<?php echo $item_id; ?>">
                    <input type="checkbox" id="sed-menu-item-area-module-<?php echo $item_id; ?>" name="sed-menu-item-area-module[<?php echo $item_id; ?>]" <?php echo $checked ?> >
                    <?php _e('using As module or widget area') ?>
                </label>
            </div>
        <?php
            $title_status = get_post_meta( $item_id, '_menu_item_column-title-megamenu', true );

        ?>
            <div class="field-column-title-megamenu-submenu-item condition-depth-field description description-wide">
                <label for="sed-menu-item-column-title-megamenu-<?php echo $item_id; ?>">
                    <?php _e('first columnn item') ?>
                   <select name="sed-menu-item-column-title-megamenu[<?php echo $item_id; ?>]" id="sed-menu-item-column-title-megamenu-<?php echo $item_id; ?>" <?php echo $checked ?>>
                     <option <?php if($title_status == "column-title") echo 'selected="selected"';?> value="column-title"><?php _e('using As column title') ?></option>
                     <option <?php if($title_status == "normal-item-submenu") echo 'selected="selected"';?> value="normal-item-submenu"><?php _e('using As normal item submenu') ?> </option>
                     <option <?php if($title_status == "hide-item-submenu") echo 'selected="selected"';?> value="hide-item-submenu"><?php _e('hide this item') ?></option>
                   </select>
                </label>
            </div>





        </div>
    <?php }

    function update_option( $menu_id, $menu_item_db_id, $args ) {
        $fields = array(
            "column-title-megamenu",
            "area-module",
            "full-width-submenu",
            "icon",
            "thumb",
            "megamenu",
            "width-submenu",
            "disable-link" ,
            "hide-title" ,
            "background-image" ,
            "background-position"
        );

        foreach ( $fields as $field ) {
            if( isset( $_REQUEST['sed-menu-item-' . $field ] ) && is_array( $_REQUEST['sed-menu-item-' . $field ] ) ){
                $meta_value = get_post_meta( $menu_item_db_id, '_menu_item_'. $field , true );
                $new_meta_value = stripcslashes( $_REQUEST['sed-menu-item-' . $field ][$menu_item_db_id] );

                if( '' == $new_meta_value ) {
                    delete_post_meta( $menu_item_db_id, '_menu_item_' . $field , $meta_value );
                }
                elseif( $meta_value !== $new_meta_value ) {
                    update_post_meta( $menu_item_db_id, '_menu_item_' . $field , $new_meta_value );
                }
            }
        }

    }
}
new SEDAddCustomFields;