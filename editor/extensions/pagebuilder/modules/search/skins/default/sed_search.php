<div <?php echo $sed_attrs; ?> class="module module-search search-default <?php echo $class ;?>"  >
    <form  id="<?php echo 'form-'.$module_html_id; ?>" role="search"  method="get" action="<?php echo $action; ?>">
        <input class="search-box form-control" name="s" type="search" placeholder="<?php echo $placeholder; ?>">
        <button class="search-button" data-search-id="<?php echo 'form-'.$module_html_id; ?>">
        	<div class="search-icon"><i class="<?php echo $icon; ?>"></i></div> 
        </button>
    </form>
</div>
