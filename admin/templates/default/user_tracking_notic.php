
<div class="updated">

    <p>
        <?php echo esc_html( $tracker_description_text ); ?>

        <a href="https://www.siteeditor.org/docs/usage-data-tracking/" target="_blank">
            <?php _e( 'Learn more.', 'site-editor' ); ?>
        </a>

    </p>

    <p>

        <a href="<?php echo $optin_url; ?>" class="button-primary">
            <?php _e( 'Sure! I\'d love to help', 'site-editor' ); ?>
        </a>&nbsp;

        <a href="<?php echo $optout_url; ?>" class="button-secondary">
            <?php _e( 'No thanks', 'site-editor' ); ?>
        </a>

    </p>

</div>