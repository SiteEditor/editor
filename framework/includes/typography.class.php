<?php
Class SiteeditorTypography{

    public $google_fonts = array();

    public $standard_fonts = array();

    public $custom_fonts = array();

    public $base_loaded_fonts = array();


	function __construct( ){

        add_action( 'init',    array( $this, 'set_fonts' ) );

        //base fonts && design editor fonts
        add_action( "wp_head" , array( $this , "load_base_fonts" ) , 1  );

        add_action( "wp_head" , array( $this , "load_mce_fonts" ) , 2  );

        if( site_editor_app_on() ){
            add_action( "wp_footer" , array( $this , "print_fonts" )  );
            add_action( "wp_footer" , array( $this , "print_font_formats" )  );
        }

        //add_action( "sed_footer" , array( $this , "print_fonts" )  );

	}

    function set_fonts(){
		$this->set_google_fonts();
		$this->set_standard_fonts();
        $this->set_custom_fonts();
    }

    function load_mce_fonts(){

        $all_fonts = apply_filters( "sed_page_mce_used_fonts" , array() , $this );

        if( is_array( $all_fonts ) && !empty( $all_fonts ) ) {

            $all_fonts = array_unique($all_fonts);

            $this->load_custom_fonts($all_fonts);

            $this->load_google_fonts($all_fonts);

        }

    }

    function load_base_fonts(){
        global $sed_general_data , $sed_data;

        $fonts = array();
        if( isset($sed_data['font_family']) && !empty( $sed_data['font_family'] ) && is_array($sed_data['font_family']) ){
            $fonts = array_values( $sed_data['font_family'] );
            $fonts = array_unique( $fonts );
        }

        $fonts[] = $sed_general_data['font-family-base'] ;
        $fonts[] = $sed_general_data['headings-font-family'];

        $fonts = array_unique( $fonts );

        $this->load_custom_fonts( $fonts );

    	$this->load_google_fonts( $fonts );
    }

    function load_custom_fonts( $fonts){
        global $sed_general_data;
        $cfonts = array();

        foreach( $fonts AS $font ){
            if( in_array( $font , array_keys( $this->custom_fonts ) ) ){
                array_push( $cfonts , $font );
            }
        }

        if( !empty( $cfonts ) ){

            ?>
            <style type="text/css">
            <!--
            <?php

            	foreach( $cfonts as $font ) {
            	    if( !in_array( $font , $this->base_loaded_fonts )  ){

                    ?>

                    @font-face {
                    	font-family: <?php echo $sed_general_data['custom_font_name']; ?>;
                    	src: url('<?php echo $sed_general_data['custom_font_eot']; ?>');
                    	src:
                    		url('<?php echo $sed_general_data['custom_font_eot']; ?>?#iefix') format('eot'),
                    		url('<?php echo $sed_general_data['custom_font_woff']; ?>') format('woff'),
                    		url('<?php echo $sed_general_data['custom_font_ttf']; ?>') format('truetype'),
                    		url('<?php echo $sed_general_data['custom_font_svg']; ?>#<?php echo $sed_general_data['custom_font_name']; ?>') format('svg');
                    	font-weight: 400;
                    	font-style: normal;
                    }

                    <?php
                    }
            	}

            ?>
            -->
            </style>
            <?php
            $this->base_loaded_fonts = array_merge( $this->base_loaded_fonts , $cfonts );
        }
    }

    function print_font_formats(){
        $fonts = $this->get_all_fonts();
        $font_formats = "";
        foreach( $fonts AS $family => $title ){
            $font_formats .= $title . "=" . $family . ";";
        }

        ?>
        <script>
            var _sedTinymceFontFormats = "<?php echo $font_formats?>";
        </script>
        <?php
    }

    function print_fonts(){
        global $sed_general_data;
        $font_groups = array();
        $custom_font_settings = array();

        if( !empty( $this->custom_fonts ) ){
            $font_groups['custom_fonts'] = $this->custom_fonts;
            foreach( $this->custom_fonts AS $family => $title ){
                $custom_font_settings[$family] = array(
                    "name"  => $family ,
                    "title" => $title ,
                    "src"   =>  array(
                        "eot"   => $sed_general_data['custom_font_eot'] ,
                        "ttf"   => $sed_general_data['custom_font_ttf'] ,
                        "woff"  => $sed_general_data['custom_font_woff'] ,
                        "svg"   => $sed_general_data['custom_font_svg']
                    )
                );
            }
        }else
            $font_groups['custom_fonts'] = array();

        $font_groups['standard_fonts']  = $this->standard_fonts;

        $font_groups['google_fonts']    = $this->google_fonts;

	    if( !empty( $sed_general_data['gfont_settings'] ) )
            $gfont_settings = ":" . $sed_general_data['gfont_settings'];
        else
            $gfont_settings = "";

        $this->base_loaded_fonts = array_unique( $this->base_loaded_fonts  );

        ?>
        <script>
            var _sedAppEditorFonts = <?php echo wp_json_encode( $font_groups ); ?>;
            var _sedGoogleFontsSettings = "<?php echo $gfont_settings; ?>";
            var _sedCustomFontsSettings = <?php echo wp_json_encode( $custom_font_settings ); ?>;
            var _sedBaseLoadedFonts = <?php echo wp_json_encode( $this->base_loaded_fonts ); ?>;
        </script>
        <?php
    }

    function get_all_fonts(){
        $fonts = array_merge( $this->standard_fonts , $this->google_fonts );

        if( !empty( $this->custom_fonts ) ){
            $fonts = array_merge( $this->custom_fonts , $fonts );
        }
        return $fonts;
    }

    function load_google_fonts( $fonts ){
        global $sed_general_data;
        $gfonts = array();

        foreach( $fonts AS $font ){
            if( in_array( $font , $this->google_fonts ) ){
                array_push( $gfonts , $font );
            }
        }

        if( !empty( $gfonts ) ){

        	foreach( $gfonts as $font ) {
        	    if( !in_array( $font , $this->base_loaded_fonts )  ){
            	    if( !empty( $sed_general_data['gfont_settings'] ) )
                        $gfont_settings = ":" . $sed_general_data['gfont_settings'];
                    else
                        $gfont_settings = "";

            		echo "<link href='http" . ((is_ssl()) ? 's' : '') . "://fonts.googleapis.com/css?family={$font}" . $gfont_settings . "' rel='stylesheet' type='text/css' />";
                }
            }

            $this->base_loaded_fonts = array_merge( $this->base_loaded_fonts , $gfonts );
        }

    }

    function set_google_fonts(){

        $this->google_fonts = self::get_google_fonts();
    }

    public static function get_google_fonts(){
        include dirname(__FILE__) . DS . 'google_fonts.php';
        return $google_fonts;
    }

    function set_standard_fonts(){

        $this->standard_fonts = self::get_standard_fonts();
    }

    public static function get_standard_fonts(){
        include dirname(__FILE__) . DS . 'standard_fonts.php';
        return $standard_fonts;
    }


    function set_custom_fonts(){
        $custom_fonts = self::get_custom_fonts();

        if( $custom_fonts !== false )
            $this->custom_fonts = $custom_fonts;
    }

    public static function get_custom_fonts(){
        global $sed_general_data;
        if( !empty( $sed_general_data['custom_font_title'] ) && !empty( $sed_general_data['custom_font_name'] ) &&
            substr( $sed_general_data['custom_font_woff'] , -5) == ".woff" &&
            substr( $sed_general_data['custom_font_ttf'] , -4) == ".ttf" &&
            substr( $sed_general_data['custom_font_svg'] , -4) == ".svg" &&
            substr( $sed_general_data['custom_font_eot'] , -4) == ".eot" ){

            return array( $sed_general_data['custom_font_name'] => $sed_general_data['custom_font_title'] );
        }else
            return false;
    }

}