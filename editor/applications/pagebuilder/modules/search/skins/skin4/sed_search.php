<div <?php echo $sed_attrs; ?> class="s-tb-sm module module-search search-skin4 <?php echo $class ;?>"  >
    <form id="<?php echo 'form-'.$module_html_id; ?>" role="search"  method="get" action="<?php echo $action; ?>">
         <div class="icon input-group-addon"><i class="<?php echo $icon; ?>"></i></div> 
         <input name="s" class="search-box form-control" type="search" placeholder="<?php echo $placeholder; ?>">
          <div class="search-button" data-search-id="<?php echo 'form-'.$module_html_id; ?>">
             <?php echo $content; ?>
          </div>
    </form>
</div>