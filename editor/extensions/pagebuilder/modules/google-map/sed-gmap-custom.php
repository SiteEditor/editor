<?php
    function gmap_rgb2hsl( $hex_color ) {

    	$hex_color	= str_replace( '#', '', $hex_color );

    	if( strlen( $hex_color ) < 3 ) {
    		str_pad( $hex_color, 3 - strlen( $hex_color ), '0' );
    	}

    	$add		 = strlen( $hex_color ) == 6 ? 2 : 1;
    	$aa		  = 0;
    	$add_on	  = $add == 1 ? ( $aa = 16 - 1 ) + 1 : 1;

    	$red		 = round( ( hexdec( substr( $hex_color, 0, $add ) ) * $add_on + $aa ) / 255, 6 );
    	$green	   = round( ( hexdec( substr( $hex_color, $add, $add ) ) * $add_on + $aa ) / 255, 6 );
    	$blue		= round( ( hexdec( substr( $hex_color, ( $add + $add ) , $add ) ) * $add_on + $aa ) / 255, 6 );

    	$hsl_color	= array( 'hue' => 0, 'sat' => 0, 'lum' => 0 );

    	$minimum	 = min( $red, $green, $blue );
    	$maximum	 = max( $red, $green, $blue );

    	$chroma	  = $maximum - $minimum;

    	$hsl_color['lum'] = ( $minimum + $maximum ) / 2;

    	if( $chroma == 0 ) {
    		$hsl_color['lum'] = round( $hsl_color['lum'] * 100, 0 );

    		return $hsl_color;
    	}

    	$range = $chroma * 6;

    	$hsl_color['sat'] = $hsl_color['lum'] <= 0.5 ? $chroma / ( $hsl_color['lum'] * 2 ) : $chroma / ( 2 - ( $hsl_color['lum'] * 2 ) );

    	if( $red <= 0.004 ||
    		$green <= 0.004 ||
    		$blue <= 0.004
    	) {
    		$hsl_color['sat'] = 1;
    	}

    	if( $maximum == $red ) {
    		$hsl_color['hue'] = round( ( $blue > $green ? 1 - ( abs( $green - $blue ) / $range ) : ( $green - $blue ) / $range ) * 255, 0 );
    	} else if( $maximum == $green ) {
    		$hsl_color['hue'] = round( ( $red > $blue ? abs( 1 - ( 4 / 3 ) + ( abs ( $blue - $red ) / $range ) ) : ( 1 / 3 ) + ( $blue - $red ) / $range ) * 255, 0 );
    	} else {
    		$hsl_color['hue'] = round( ( $green < $red ? 1 - 2 / 3 + abs( $red - $green ) / $range : 2 / 3 + ( $red - $green ) / $range ) * 255, 0 );
    	}

    	$hsl_color['sat'] = round( $hsl_color['sat'] * 100, 0 );
    	$hsl_color['lum']  = round( $hsl_color['lum'] * 100, 0 );

    	return $hsl_color;
    }
?>
    <?php extract($_POST); ?>
     <!DOCTYPE HTML>
    <html>

    <head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
    <title>Custom Google Map</title>
      <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
      <script type="text/javascript" src="<?php echo $baseUrl . 'libraries/jquery/jquery.js';?>"></script>
      <script type="text/javascript" src="<?php echo $moduleUrl . 'google-map/js/gmap-min.js';?>"></script>
      <style type="text/css">
      <!--
      body{
        color: #333333;
        font-family: tahoma;
        font-size: 12px;
        line-height: 1.42857;
        margin: 0 auto !important;
        overflow: hidden;
        padding: 0 !important;
        width: 100%;
      }

      -->
      </style>
    </head>

    <body>
        <div class="sed-gmap" style="width:100%;height:<?php echo $height?>px;"></div>
        <?php
    	$address = addslashes($address);

    	$addresses = explode('|', $address);

    	$description = addslashes($description);
    	$descriptions = explode('|', $description);

    	$markers = '';
    	$marker_counter = 0;
        if(!empty($addresses)){
        	foreach($addresses as $address_string) {

                $address_desc = isset( $descriptions[$marker_counter] ) ? $descriptions[$marker_counter] : "";

        		$markers .= "{
        			id: '{$id}-{$marker_counter}',
        			address: '{$address_string}',
                    html: {
        				content: '{$address_desc}',
        				popup: true
        			}
        		},";

        		$marker_counter++;
        	}
        }

        $overlayColor = ( $overlayColor == "transparent" ) ? "" : $overlayColor;

        $overlayColorHsl = ( !empty( $overlayColor ) && $overlayColor != "transparent" ) ? json_encode( gmap_rgb2hsl( $overlayColor ) ) : "{}";

        $inline_js = "jQuery(document).ready(function($) {
        	jQuery('.sed-gmap').goMap({
        		address: '{$addresses[0]}',
        		zoom: {$zoom},
                overlayColor : '{$overlayColor}' ,
                overlayColorHsl : {$overlayColorHsl},
        		scrollwheel: {$scrollwheel},
        		scaleControl: {$scaleControl},
        		panControl: {$panControl},
                mapTypeControl : {$mapTypeControl},
                streetViewControl : {$streetViewControl},
        		maptype: '{$type}',
        		markers: [{$markers}]
        	});
        });";
        ?>
        <script type='text/javascript'><?php echo $inline_js;?></script>


    </body>

    </html>