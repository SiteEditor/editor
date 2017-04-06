<?php $inline_columns = ($full_width_columns) ? "normal-columns" : "table-cell-columns"; ?>
<table class="sed-cols-table">
  <tr <?php echo $sed_attrs; ?> class="sed-columns-pb <?php echo  $class .' '. $inline_columns?>"  sed-role="column-pb">
      <?php echo $content; ?> 
  </tr>
</table>


