<div id="sed-module-not-compiled" class="sed-module-compiled-container">
<div class="module-not-compiled-title">
    <h3> <?php _e("Less File Not Compiled","site-editor") ?></h3>
</div>
<form id="sed-compile-less-list">
    <ol>
    <?php
    foreach ( $not_compiled_files as $handle => $arr_src ):
    ?>
        <li>
            <p> <?php printf( __("Handle :( %s ) \n Url File : ...%s","site-editor") , $handle , $arr_src[0] ) ?> </p>
            <input class="less-file-item" data-rel-src="<?php echo $arr_src[1]; ?>" name="<?php echo $handle ?>" type="hidden" value="<?php echo $arr_src[0]; ?>"/>
        </li>
    <?php
    endforeach;
    ?>
    </ol>
</form>
    
<a href="javascript;void(0)" id="sed-compiled-less" class="sed-btn-blue"><?php _e("Compiled Top File","site-editor")?></a>
<a href="javascript;void(0)"  id="sed-close-compiled-less" class="sed-btn-default"><?php _e("Close","site-editor")?></a>

</div>
