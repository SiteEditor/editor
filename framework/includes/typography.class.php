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

        add_action( "wp_footer" , array( $this , "load_mce_fonts" )  );

        /**
         * Before Create Dynamic Css file
         */
        add_action( "sed_before_dynamic_css_output" , array( $this , "load_footer_fonts" ) , 100000 );

        add_filter( "sed_load_dynamic_fonts_footer" , array( $this , "register_design_editor_fonts" ) , 10 , 1 );

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

            $this->load_google_fonts($all_fonts , true);

        }

    }

    public function register_design_editor_fonts( $fonts ){

        $css_data = SED()->framework->dynamic_css_data;

        $all_fonts = array();

        if(!empty($css_data)) {

            foreach ($css_data AS $selector => $styles) {

                foreach ($styles AS $property => $value) {

                    if( $property == "font_family" ){

                        $all_fonts[] = $value;

                    }

                }

            }

        }

        $all_fonts = array_unique($all_fonts);

        return array_merge( $fonts , $all_fonts );

    }

    function load_footer_fonts(){

        $this->load_base_fonts( true );

    }

    function load_base_fonts( $footer = false ){

        $fonts = array();

        $footer_mode = $footer ? "_footer" : "";

        $fonts = apply_filters( "sed_load_dynamic_fonts{$footer_mode}" , $fonts , $this );

        $fonts = array_unique( $fonts );

        $this->load_custom_fonts( $fonts );

        $this->load_google_fonts( $fonts , $footer );

    }

    function load_custom_fonts( $fonts ){

        $cfonts = array();

        foreach( $fonts AS $font ){
            if( in_array( $font , array_keys( $this->custom_fonts ) ) ){
                $cfonts[$font] = $this->custom_fonts[$font];
            }
        }

        if( !empty( $cfonts ) ){

            global $sed_dynamic_css_string;

            ob_start();

            foreach( $cfonts as $font => $font_data ) {

                if( !isset( $this->base_loaded_fonts[$font] ) ){

                    ?>

                    @font-face {
                    font-family: <?php echo $font_data['font_family']; ?>;
                    src: url('<?php echo $font_data['font_eot']; ?>');
                    src:
                    url('<?php echo $font_data['font_eot']; ?>?#iefix') format('eot'),
                    url('<?php echo $font_data['font_woff']; ?>') format('woff'),
                    url('<?php echo $font_data['font_ttf']; ?>') format('truetype'),
                    url('<?php echo $font_data['font_svg']; ?>#<?php echo $font_data['font_family']; ?>') format('svg');
                    font-weight: 400;
                    font-style: normal;
                    }

                    <?php
                }

            }

            $sed_dynamic_css_string .= ob_get_clean();

            $this->base_loaded_fonts = array_merge( $this->base_loaded_fonts , $cfonts );
        }
    }

    function load_google_fonts( $fonts , $footer = false ){
        //global $sed_general_data;
        $gfonts = array();

        foreach( $fonts AS $font ){
            if( in_array( $font , $this->google_fonts ) ){
                array_push( $gfonts , $font );
            }
        }

        if( !empty( $gfonts ) ){

            foreach( $gfonts as $font ) {
                if( !isset( $this->base_loaded_fonts[$font] ) ){
                    /*if( !empty( $sed_general_data['gfont_settings'] ) )
                        $gfont_settings = ":" . $sed_general_data['gfont_settings'];
                    else*/
                    $gfont_settings = "";

                    if( !$footer ) {

                        echo "<link href='http" . ((is_ssl()) ? 's' : '') . "://fonts.googleapis.com/css?family={$font}" . $gfont_settings . "' rel='stylesheet' type='text/css' />";
                    }else{

                        ?>
                        <script type="text/javascript">
                            (function ($) {

                                var $style_sheet = "<link rel='stylesheet'  href='<?php echo "http" . ((is_ssl()) ? 's' : '') . "://fonts.googleapis.com/css?family={$font}" . $gfont_settings;?>' type='text/css' media='all' />";

                                $($style_sheet).appendTo("head");

                            }(jQuery));
                        </script>
                        <?php

                    }

                }
            }

            $this->base_loaded_fonts = array_merge( $this->base_loaded_fonts , $gfonts );
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
            $font_groups['custom_fonts'] = array();
            foreach( $this->custom_fonts AS $family => $font_data ){
                $font_groups['custom_fonts'][$family] = $font_data['font_title'];
                $custom_font_settings[$family] = array(
                    "name"  => $family ,
                    "title" => $font_data['font_title'] ,
                    "src"   =>  array(
                        "eot"   => $font_data['font_eot'] ,
                        "ttf"   => $font_data['font_ttf'] ,
                        "woff"  => $font_data['font_woff'] ,
                        "svg"   => $font_data['font_svg']
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

            $custom_fonts = array();

            foreach( $this->custom_fonts AS $family => $font_data ) {
                $custom_fonts[$family] = $font_data['font_title'];
            }

            $fonts = array_merge( $custom_fonts , $fonts );
        }
        return $fonts;
    }

    function set_google_fonts(){

        $this->google_fonts = self::get_google_fonts();
    }

    public static function get_google_fonts(){

        include dirname(__FILE__) . DS . 'google_fonts.php';

        $google_fonts = apply_filters( 'sed_google_fonts_filter' , $google_fonts );

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

        $custom_fonts = get_theme_mod( 'sed_custom_fonts' , array() );

        $valid_fonts = array();

        if( is_array($custom_fonts) && !empty($custom_fonts) ){

            foreach( $custom_fonts AS $font ) {
                if (!empty($font['font_title']) && !empty($font['font_family']) &&
                    substr($font['font_woff'], -5) == ".woff" &&
                    substr($font['font_ttf'], -4) == ".ttf" &&
                    substr($font['font_svg'], -4) == ".svg" &&
                    substr($font['font_eot'], -4) == ".eot"
                ) {

                    $valid_fonts[$font['font_family']] = $font;
                }
            }

        }

        if( !empty( $valid_fonts ) ){
            return $valid_fonts;
        }else{
            return false;
        }

    }

}