<!DOCTYPE HTML>
<html>

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width">
<title>Custom Pinterest</title>
  <style type="text/css">
  <!--
  body{
    color: #333333;
    font-family: tahoma;
    font-size: 12px;
    line-height: 1.42857;
    margin: 0 auto !important;
    overflow: hidden;
    padding: 8px 10px !important;
    width: 100%;
  }

  -->
  </style>
</head>
<body>
<?php

if( isset( $_GET ) && !empty( $_GET ) ){
 	extract( $_GET );
?>
<a data-pin-do="embedUser" href="<?php echo $profile_url; ?>" data-pin-scale-width="<?php echo $image_width; ?>" data-pin-scale-height="<?php echo $board_height; ?>"></a>
<script type="text/javascript" async defer src="https://assets.pinterest.com/js/pinit.js"></script>
<?php } ?>

</body>

</html>