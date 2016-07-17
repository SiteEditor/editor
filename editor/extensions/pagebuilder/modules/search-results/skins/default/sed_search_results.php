<div <?php echo $sed_attrs; ?> class="<?php echo $class; ?> module-search-results search-results-default " >
    <div class="bp-component" <?php if( site_editor_app_on() ) echo 'data-parent-id="' . $sed_model_id . '"'; ?>>
    <h4>Need a new search?</h4>
    <p>If you didn't find what you were looking for, try a new search!</p>

        <?php echo $content; ?>
    </div>
</div>