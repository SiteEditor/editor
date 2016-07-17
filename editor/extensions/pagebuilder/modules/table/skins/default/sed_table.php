<div <?php echo $sed_attrs; ?> class="s-tb-sm s-tb-sm module module-table table-skin-default  <?php echo $class;?> ">
    <div class="table-responsive">
       <table class="table table-hover <?php if($table_bordered){ ?> table-bordered <?php } if($table_striped){ ?> table-striped <?php } ?>"><?php echo $content; ?></table>
    </div>
</div>