<div <?php echo $sed_attrs; ?> class="s-tb-sm module module-code-syntax-highlighter code-syntax-highlighter-default <?php echo $class  ;?>">
    <pre id="code-syntax-highlighter-<?php echo $module_html_id; ?>" class="<?php echo $code_params;?>" <?php echo $title;?> ><?php echo strip_tags( $content );?></pre>
    <textarea class="code-syntax-highlighter-textarea" name="code-syntax-highlighter-editor-<?php echo $module_html_id; ?>" id="code-syntax-highlighter-editor-<?php echo $module_html_id; ?>"><?php echo strip_tags( $content );?></textarea>
    <div class="close-code-editor"><i class="close fa fa-close"></i><span class="close-text"><?php echo __("Close","site-editor");?></span></div>
</div>
