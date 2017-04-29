<div id="sed-deactivate-feedback-dialog-wrapper" title="<?php echo esc_attr__( 'Quick Feedback', 'site-editor' ); ?>">
    
    <form id="sed-deactivate-feedback-dialog-form" method="post">
        
        <?php
        wp_nonce_field( '_sed_deactivate_feedback_nonce' );
        ?>
        <input type="hidden" name="action" value="sed_deactivate_feedback" />

        <div id="sed-deactivate-feedback-dialog-form-caption"><?php _e( 'If you have a moment, please share why you are deactivating SiteEditor:', 'site-editor' ); ?></div>
        
        <div id="sed-deactivate-feedback-dialog-form-body">
            
            <?php foreach ( $deactivate_reasons as $reason_key => $reason ) : ?>
                
                <div class="sed-deactivate-feedback-dialog-input-wrapper">
                    
                    <input id="sed-deactivate-feedback-<?php echo esc_attr( $reason_key ); ?>" class="sed-deactivate-feedback-dialog-input" type="radio" name="reason_key" value="<?php echo esc_attr( $reason_key ); ?>" />
                    
                    <label for="sed-deactivate-feedback-<?php echo esc_attr( $reason_key ); ?>" class="sed-deactivate-feedback-dialog-label"><?php echo $reason['title']; ?></label>
                    
                    <?php if ( ! empty( $reason['input_placeholder'] ) ) : ?>
                        <input class="sed-feedback-text" type="text" name="reason_<?php echo esc_attr( $reason_key ); ?>" placeholder="<?php echo esc_attr( $reason['input_placeholder'] ); ?>" />
                    <?php endif; ?>
                    
                </div>
                
            <?php endforeach; ?>
            
        </div>

        <div class="action-bar">

            <div>
                <button type="button" class="sed-deactivate-feedback-send button button-primary" data-value="yes"><?php echo __("Submit & Deactivate" , "site-editor"); ?></button>
            </div>

            <?php
            if( ! $this->_is_allow_track() ) {

                ?>

                <div>
                    <button type="button" class="sed-deactivate-feedback-skip button button-default"
                            data-value="skip"><?php echo __("Skip & Deactivate", "site-editor"); ?></button>
                </div>

                <?php

            }
            ?>

        </div>
        
    </form>
    
</div>