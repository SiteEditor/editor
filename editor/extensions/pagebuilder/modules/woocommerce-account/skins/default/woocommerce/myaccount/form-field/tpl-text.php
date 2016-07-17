<?php
$class = isset( $field['class'] ) ? implode( ' ' , $field['class']  ) : '' ;
$attrs = array(
    "type"        => "text" ,
    "name"        => $key,
    "id"          => $key,
    "class"       =>  $class . " form-control",
    "placeholder" => isset( $field['placeholder'] ) ? $field['placeholder'] : '',
    "value"       => isset( $field['value'] ) ? $field['value'] : '',
);?>
<div class="form-group">
    <label for="<?php echo $key ?>"><?php 
        if( isset( $field['label'] ) && $field['label'] )
            echo $field['label'];

        if( isset( $field['required'] ) && $field['required'] )
            echo '<span class="required" title="required">*</span>';
    ?></label>
    <input <?php foreach ( $attrs as $att => $val_att ) {
       if( !empty( $val_att ) ) 
        echo  $att . '="' . $val_att . '" ';
    }?>>
</div>