<div <?php echo $sed_attrs; ?> class="pc-poll-module <?php echo $class ?> ">


    <div class="poll-container">
        <div class="row">
            <div class="col-xs-9">
                <h3><?php echo __("Poll" , "site-editor");?></h3>
                <div class="spr"></div>
            </div>
            <div class="col-xs-3">
            <span class="poll-icon fa fa-comment-o"></span>
            </div>
        </div>
        <div>
            <p><?php echo $description;?></p>
            <a href="<?php echo $form_file;?>" class="btn btn-flat"><?php echo __("Form Download" , "site-editor");?></a>
        </div>
    </div>



</div>