<?php
/*
* Module Name: Code Syntax Highlighter
* Module URI: http://www.siteeditor.org/modules/code-syntax-highlighter
* Description: Code Syntax Highlighter Module , Easily post syntax-highlighted code to your site without having to modify the code at all. Uses Alex Gorbatchev's SyntaxHighlighter.
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "paragraph")){
    sed_admin_notice( __("<b>Code Syntax Highlighter Module</b> needed to <b>Paragraph Module</b> <br /> please first install and activate its ") );
    return ;
}

class PBCodeSyntaxHighlighter extends PBShortcodeClass{
    static $sed_counter_id = 0;

	// All of these variables are private. Filters are provided for things that can be modified.
	var $agshver              = false;
	var $brushes              = array();  // Array of aliases => brushes
	var $themes               = array();  // Array of themes
	var $usedbrushes          = array();  // Stores used brushes so we know what to output
	var $encoded              = false;    // Used to mark that a character encode took place
	//var $codeformat           = false;    // If set, SyntaxHighlighter::get_code_format() will return this value
	var $content_save_pre_ran = false;    // It's possible for the "content_save_pre" filter to run multiple times, so keep track
    var $localize_array       = array();

	function __construct(){

		parent::__construct( array(
			"name"		=> "sed_code_syntax_highlighter",
			"title"	   => __( "Code Syntax Highlighter" , "site-editor" ),
			"description" => __( "" , "site-editor" ),
            "icon"        => "icon-codesyntaxhighlighter",
			"module"	  => "code-syntax-highlighter",
		));

        $this->agshver  = '1.0.0';

        // Into the database
        //add_filter( 'content_save_pre',   array( $this, 'encode_shortcode_contents_slashed_noquickedit' ), 1 ); // Posts
        add_filter( 'sed_content_save_pre',   array( $this, 'encode_post_content' ), 1 );
        add_filter( 'sed_update_settings' , array( $this, 'encode_theme_content' ) , 10 , 4 ); //$settings , $option_name , $sed_page_id  , $sed_page_type
        add_filter( 'sed_app_sanitize_sed_layouts_content' , array( $this, 'encode_sub_themes_content' ) , 10 , 2 );

        // Out of the database for editing
        add_filter( 'sed_theme_shortcode_content_output' , array( $this, 'decode_theme_content' ) , 1 );
        add_filter( 'sed_post_shortcode_content_output' , array( $this, 'decode_theme_content' ) , 1 );

        if( site_editor_app_on() ){
            add_filter( 'wp_footer', array( $this, 'localize' ) );
        }

		add_action("init" , array( $this, "register_scripts" ) );

		add_action("plugins_loaded" , array( $this, "init_module" ) );

	}

	function init_module(){
		// Create list of brush aliases and map them to their real brushes
		// The key is the language alias
		// The value is the script handle suffix: syntaxhighlighter-brush-ThisBitHere  (your plugin needs to register the script itself)
		$this->brushes = (array) apply_filters( 'syntaxhighlighter_brushes', array(
			'as3'           => 'as3',
			'actionscript3' => 'as3',
			'bash'          => 'bash',
			'shell'         => 'bash',
			'coldfusion'    => 'coldfusion',
			'cf'            => 'coldfusion',
			'clojure'       => 'clojure',
			'clj'           => 'clojure',
			'cpp'           => 'cpp',
			'c'             => 'cpp',
			'c-sharp'       => 'csharp',
			'csharp'        => 'csharp',
			'css'           => 'css',
			'delphi'        => 'delphi',
			'pas'           => 'delphi',
			'pascal'        => 'delphi',
			'diff'          => 'diff',
			'patch'         => 'diff',
			'erl'           => 'erlang',
			'erlang'        => 'erlang',
			'fsharp'        => 'fsharp',
			'groovy'        => 'groovy',
			'java'          => 'java',
			'jfx'           => 'javafx',
			'javafx'        => 'javafx',
			'js'            => 'jscript',
			'jscript'       => 'jscript',
			'javascript'    => 'jscript',
			'latex'         => 'latex', // Not used as a shortcode
			'tex'           => 'latex',
			'matlab'        => 'matlabkey',
			'objc'          => 'objc',
			'obj-c'         => 'objc',
			'perl'          => 'perl',
			'pl'            => 'perl',
			'php'           => 'php',
			'plain'         => 'plain',
			'text'          => 'plain',
			'ps'            => 'powershell',
			'powershell'    => 'powershell',
			'py'            => 'python',
			'python'        => 'python',
			'r'             => 'r', // Not used as a shortcode
			'splus'         => 'r',
			'rails'         => 'ruby',
			'rb'            => 'ruby',
			'ror'           => 'ruby',
			'ruby'          => 'ruby',
			'scala'         => 'scala',
			'sql'           => 'sql',
			'vb'            => 'vb',
			'vbnet'         => 'vb',
			'xml'           => 'xml',
			'xhtml'         => 'xml',
			'xslt'          => 'xml',
			'html'          => 'xml',
		) );


		// Create list of themes and their human readable names
		// Plugins can add to this list: http://www.viper007bond.com/wordpress-plugins/syntaxhighlighter/adding-a-new-theme/
		$this->themes = (array) apply_filters( 'syntaxhighlighter_themes', array(
			'default'    => __( 'Default',      'syntaxhighlighter' ),
			'django'     => __( 'Django',       'syntaxhighlighter' ),
			'eclipse'    => __( 'Eclipse',      'syntaxhighlighter' ),
			'emacs'      => __( 'Emacs',        'syntaxhighlighter' ),
			'fadetogrey' => __( 'Fade to Grey', 'syntaxhighlighter' ),
			'midnight'   => __( 'Midnight',     'syntaxhighlighter' ),
			'rdark'      => __( 'RDark',        'syntaxhighlighter' ),
			'none'       => __( '[None]',       'syntaxhighlighter' ),
		) );

		// Other special characters that need to be encoded before going into the database (namely to work around kses)
		$this->specialchars = (array) apply_filters( 'sed_syntaxhighlighter_specialchars', array(
			'\0' => '&#92;&#48;',
		) );
	}

	function register_scripts(){

		// Register brush scripts
		wp_register_script( 'syntaxhighlighter-core',             SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shCore.js',             array(),                         $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-as3',        SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushAS3.js',         array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-bash',       SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushBash.js',        array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-coldfusion', SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushColdFusion.js',  array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-cpp',        SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushCpp.js',         array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-csharp',     SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushCSharp.js',      array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-css',        SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushCss.js',         array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-delphi',     SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushDelphi.js',      array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-diff',       SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushDiff.js',        array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-erlang',     SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushErlang.js',      array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-groovy',     SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushGroovy.js',      array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-java',       SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushJava.js',        array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-javafx',     SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushJavaFX.js',      array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-jscript',    SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushJScript.js',     array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-perl',       SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushPerl.js',        array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-php',        SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushPhp.js',         array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-plain',      SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushPlain.js',       array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-powershell', SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushPowerShell.js',  array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-python',     SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushPython.js',      array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-ruby',       SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushRuby.js',        array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-scala',      SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushScala.js',       array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-sql',        SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushSql.js',         array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-vb',         SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushVb.js',          array('syntaxhighlighter-core'), $this->agshver );
		wp_register_script( 'syntaxhighlighter-brush-xml',        SED_PB_MODULES_URL . 'code-syntax-highlighter/js/shBrushXml.js',         array('syntaxhighlighter-core'), $this->agshver );

		// Register some popular third-party brushes
		wp_register_script( 'syntaxhighlighter-brush-clojure',    SED_PB_MODULES_URL . 'code-syntax-highlighter/js/third-party-brushes/shBrushClojure.js',            array('syntaxhighlighter-core'), '20090602'     );
		wp_register_script( 'syntaxhighlighter-brush-fsharp',     SED_PB_MODULES_URL . 'code-syntax-highlighter/js/third-party-brushes/shBrushFSharp.js',             array('syntaxhighlighter-core'), '20091003'     );
		wp_register_script( 'syntaxhighlighter-brush-latex',      SED_PB_MODULES_URL . 'code-syntax-highlighter/js/third-party-brushes/shBrushLatex.js',              array('syntaxhighlighter-core'), '20090613'     );
		wp_register_script( 'syntaxhighlighter-brush-matlabkey',  SED_PB_MODULES_URL . 'code-syntax-highlighter/js/third-party-brushes/shBrushMatlabKey.js',          array('syntaxhighlighter-core'), '20091209'     );
		wp_register_script( 'syntaxhighlighter-brush-objc',       SED_PB_MODULES_URL . 'code-syntax-highlighter/js/third-party-brushes/shBrushObjC.js',               array('syntaxhighlighter-core'), '20091207'     );
		wp_register_script( 'syntaxhighlighter-brush-r',          SED_PB_MODULES_URL . 'code-syntax-highlighter/js/third-party-brushes/shBrushR.js',                  array('syntaxhighlighter-core'), '20100919'     );

		// Handle Scripts
		wp_register_script( 'syntaxhighlighter-handler', SED_PB_MODULES_URL . 'code-syntax-highlighter/js/sh-handler.js', array( 'syntaxhighlighter-core' , 'syntaxhighlighter-brush-php' ), $this->agshver );

		// Localize the script with new data
		$translation_array = array(
			'expand_source'         => __( '+ expand source', 'site-editor' ) ,
			'question'              => __( '?', 'site-editor' ) ,
			'Syntax_highlighter'    => __( 'SyntaxHighlighter\n\n', 'site-editor' ) ,
			'find_brush'            => __( "Can't find brush for: ", 'site-editor' ) ,
			'brush_configured'      => __( "Brush wasn't configured for html-script option: ", 'site-editor' ) ,
		);

		$this->localize_array = $translation_array;

		wp_localize_script( 'syntaxhighlighter-handler' , '__sedSyntaxHighlighter' , $translation_array );

		// Register theme stylesheets
		wp_register_style(  'syntaxhighlighter-core',             SED_PB_MODULES_URL .  'code-syntax-highlighter/css/shCore.css',             array(),                         $this->agshver );
		wp_register_style(  'syntaxhighlighter-theme-default',    SED_PB_MODULES_URL .  'code-syntax-highlighter/css/shThemeDefault.css',     array('syntaxhighlighter-core'), $this->agshver );
		wp_register_style(  'syntaxhighlighter-theme-django',     SED_PB_MODULES_URL .  'code-syntax-highlighter/css/shThemeDjango.css',      array('syntaxhighlighter-core'), $this->agshver );
		wp_register_style(  'syntaxhighlighter-theme-eclipse',    SED_PB_MODULES_URL .  'code-syntax-highlighter/css/shThemeEclipse.css',     array('syntaxhighlighter-core'), $this->agshver );
		wp_register_style(  'syntaxhighlighter-theme-emacs',      SED_PB_MODULES_URL .  'code-syntax-highlighter/css/shThemeEmacs.css',       array('syntaxhighlighter-core'), $this->agshver );
		wp_register_style(  'syntaxhighlighter-theme-fadetogrey', SED_PB_MODULES_URL .  'code-syntax-highlighter/css/shThemeFadeToGrey.css',  array('syntaxhighlighter-core'), $this->agshver );
		wp_register_style(  'syntaxhighlighter-theme-midnight',   SED_PB_MODULES_URL .  'code-syntax-highlighter/css/shThemeMidnight.css',    array('syntaxhighlighter-core'), $this->agshver );
		wp_register_style(  'syntaxhighlighter-theme-rdark',      SED_PB_MODULES_URL .  'code-syntax-highlighter/css/shThemeRDark.css',       array('syntaxhighlighter-core'), $this->agshver );

	}

	function get_atts(){

        $atts = array(
            'lang'           => "php",
            'classname'      => "code-syntax-highlighter-editor" ,
            'autolinks'      => true,
            'collapse'       => false,
            'firstline'      => 1,
            'fontsize'       => 14,
            'gutter'         => true,
            'highlight'      => "",
            'htmlscript'     => true,
            'light'          => false,
            'padlinenumbers' => -1,
            'smarttabs'      => true,
            'tabsize'        => 4,
            'title'          => '',
            'toolbar'        => false,
            'theme'          => 'default' ,
            //'mode'           => 'editor'    //editor || visual
        );

        return $atts;
	}

	function add_shortcode( $atts , $content = null){


		self::$sed_counter_id++;
		$module_html_id = "sed_code_syntax_highlighter_module_html_id_" . self::$sed_counter_id;

		$this->set_vars( array(
			"module_html_id"     => $module_html_id ,   
		));		

        $lang = ( !empty( $atts['lang'] ) ) ? $atts['lang'] : "php";
        $this->usedbrushes[$lang] = true;
        $params = array();
		$params[] = "brush: $lang;";

		// Parameter renaming (the shortcode API doesn't like parameter names with dashes)
		$rename_map = array(
			'autolinks'      => 'auto-links',
			'classname'      => 'class-name',
			'firstline'      => 'first-line',
			'fontsize'       => 'font-size',
			'htmlscript'     => 'html-script',
			'padlinenumbers' => 'pad-line-numbers',
			'smarttabs'      => 'smart-tabs',
			'tabsize'        => 'tab-size',
			//'wraplines'      => 'wrap-lines',
		);

		// Allowed configuration parameters and their type
		// Use the proper names (see above)
		$allowed_atts = (array) apply_filters( 'sed_syntaxhighlighter_allowedatts', array(
			'auto-links'       => 'boolean',
			'class-name'       => 'other',
			'collapse'         => 'boolean',
			'first-line'       => 'integer',
			'font-size'        => 'integer',
			'gutter'           => 'boolean',
			'highlight'        => 'other',
			'html-script'      => 'boolean',
			'light'            => 'boolean',
			'pad-line-numbers' => 'other',
			'smart-tabs'       => 'boolean',
			'tab-size'         => 'integer',
			'title'            => 'other',
			'toolbar'          => 'boolean',
			//'wrap-lines'       => 'boolean',
		) );

      $title = '';

      foreach ( $atts as $key => $value) {
          $key = strtolower( $key );

          if ( !empty($rename_map[$key]) )
              $key = $rename_map[$key];

          if ( !isset( $allowed_atts[$key] ) || empty($allowed_atts[$key])  )
              continue;

          if(is_bool($value) && $value === true){
              $value = "true";
          }elseif(is_bool($value) && $value === false){
              $value = "false";
          }

          if( $allowed_atts[$key] == 'integer' ){
              $value = (int) $value;
          }

          // Sanitize the "classname" parameter
          if ( 'class-name' == $key )
            $value = trim( preg_replace( '/[^a-zA-Z0-9 _-]/i', '', $value ) );

          // Special sanitization for "pad-line-numbers"
          if ( 'pad-line-numbers' == $key ) {
              if ( -1 == $value )
                $value = 'true';
              elseif ( 0 == $value )
                $value = 'false';
              else
                $value = (int) $value;
          }

          // Add % sign to "font-size"
          if ( 'font-size' == $key )
            $value = $value . '%';

          // If "html-script", then include the XML brush as it's needed
          if ( 'html-script' == $key && 'true' == $value )
              $this->usedbrushes['xml'] = true;

          // Sanitize row highlights
          if ( 'highlight' == $key ) {
              if ( false === strpos( $value, ',' ) && false === strpos( $value, '-' ) ) {
              	  $value = (int) $value;
              } else {
                  $lines = explode( ',', $value );
                  $highlights = array();

                  foreach ( $lines as $line ) {
                      // Line range
                      if ( false !== strpos( $line, '-' ) ) {
                          list( $range_start, $range_end ) = array_map( 'intval', explode( '-', $line ) );
                          if ( ! $range_start || ! $range_end || $range_end <= $range_start )
                              continue;

                          for ( $i = $range_start; $i <= $range_end; $i++ )
                              $highlights[] = $i;
                      } else {
                      	  $highlights[] = (int) $line;
                      }
                  }

                  natsort( $highlights );

                  $value = implode( ',', $highlights );
              }

              if ( empty( $value ) )
              	continue;

              // Wrap highlight in [ ]
              $params[] = "$key: [$value];";
              continue;
          }

          // Don't allow HTML in the title parameter
          if ( 'title' == $key ) {
              $value = strip_tags( html_entity_decode( strip_tags( $value ) ) );
          }

          $params[] = "$key: $value;";

          // Set the title variable if the title parameter is set (but not for feeds)
          if ( 'title' == $key && ! is_feed() )
              $title = ' title="' . esc_attr( $value ) . '"';

      }

      //$code = htmlspecialchars( $code );

      $params[] = 'notranslate'; // For Google, see http://otto42.com/9k

      $params = apply_filters( 'sed_syntaxhighlighter_cssclasses', $params ); // Use this to add additional CSS classes / SH parameters

      if( !site_editor_app_on() )
        $this->atts['mode'] = "visual";

      $this->set_vars( array(
          "code_params"      => esc_attr( implode( ' ', $params ) ) ,
          "title"            => $title
      ));

	}

    function localize(){
        $l10n = array();
		foreach ( $this->localize_array as $key => $value ) {
			if ( !is_scalar($value) )
				continue;
			$l10n[$key] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8');
		}

        ?>
        <script type='text/javascript'>
        /* <![CDATA[ */
    		var __sedSyntaxHighlighter = <?php echo wp_json_encode( $l10n );?>;
        /* ]]> */
        </script>
        <?php
    }

	function shortcode_settings(){

        $langs = array();

        foreach( $this->brushes AS $key => $value ){
            if( isset( $langs[$value] ) ){
                $langs[$value] .= " OR " . $key;
            }else{
                $langs[$value] = $key;
            }
        }

        $params = array(

            'lang' => array(
                'type' => 'select',
                'label' => __('Select Language', 'site-editor'),
                'description'  => __('The language syntax to highlight with', 'site-editor'),
                'choices'   => $langs,
                "js_params" =>  array(
                    "force_refresh"   =>   true
                ),                
            ),

            'autolinks'    => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Automatically make URLs clickable' , 'site-editor' ) ,
                'description'  => __( 'Toggle automatic URL linking.' , 'site-editor' ),
            ),

            'collapse'    => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Collapse code boxes' , 'site-editor' ) ,
                'description'  => __( 'Toggle collapsing the code box by default, requiring a click to expand it. Good for large code posts.' , 'site-editor' ),
            ),

            'firstline' => array(
                'type' => 'number',
                'after_field'  => 'px',
                'label' => __('Starting Line Number', 'site-editor'),
                'description'  => __('An interger specifying what number the first line should be (for the line numbering).', 'site-editor'),
                "js_params"  =>  array(
                    "min"  =>  0,
                ),
            ),

            /*'fontsize' => array(
                'type' => 'number',
                'after_field'  => '%',
                'label' => __('Font Size', 'site-editor'),
                'description'  => __('Font Size', 'site-editor'),
                "js_params"  =>  array(
                    "min"  =>  0,
                ),
            ),*/

            'gutter'    => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Display line numbers' , 'site-editor' ) ,
                'description'  => __( 'Toggle the left-side line numbering.' , 'site-editor' ),
            ),

            'highlight' =>  array(
                'type'          => 'text',
                'label'         => __('Line numbers to highlight', 'site-editor'),
                'description'   => __('A comma-separated list of line numbers to highlight. You can also specify a range. Example: 2,5-10,12', 'site-editor'),
            ),

            'htmlscript'    => array(
                'type'  => 'checkbox' ,
                'label' => __( 'highlighting HTML/XML' , 'site-editor' ) ,
                'description'  => __( "Toggle highlighting any extra HTML/XML. Good for when you're mixing HTML/XML with another language, such as having PHP inside an HTML web page. The above preview has it enabled for example. This only works with certain languages." , 'site-editor' ),
            ),

            'light'    => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Use the light display mode?' , 'site-editor' ) ,
                'description'  => __( 'Use the light display mode, best for single lines of code. Toggle light mode which disables the gutter and toolbar all at once' , 'site-editor' ),
            ),

            'padlinenumbers' => array(
                'type' => 'number',
                'after_field'  => 'px',
                'label' => __('Line Number Padding', 'site-editor'),
                'description'  => __('Controls line number padding. Valid values are 0 (no padding), -1 (automatic padding), or an integer (forced padding)', 'site-editor'),
                "js_params"  =>  array(
                    "min"  =>  0,
                ),
            ),

            'smarttabs'    => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Use smart tabs?' , 'site-editor' ) ,
                'description'  => __( 'Use smart tabs allowing tabs being used for alignment' , 'site-editor' ),
            ),

            'tabsize' => array(
                'type' => 'number',
                'after_field'  => 'px',
                'label' => __('Tab Size', 'site-editor'),
                'description'  => __('Tab Size', 'site-editor'),
                "js_params"  =>  array(
                    "min"  =>  0,
                ),
            ),

            'title' =>  array(
                'type'          => 'text',
                'label'         => __('Title', 'site-editor'),
                'description'   => __('Sets some text to show up before the code. Very useful when combined with the collapse parameter', 'site-editor'),
            ),

            'toolbar'    => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Toggle the toolbar?' , 'site-editor' ) ,
                'description'  => __( 'Toggle the toolbar' , 'site-editor' ),
            ),

            'theme' => array(
                'type' => 'select',
                'label' => __('Select Theme', 'site-editor'),
                'description'  => __('Select Color Theme Or Skin for Codes', 'site-editor'),
                'choices'   => $this->themes,
            ),

            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "default"       => "10 0 10 0" ,
            ), 
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ), 
        );
        return $params;
	}

    function scripts(){
        $scripts = array();
        array_push( $scripts , array('syntaxhighlighter-core') );
        array_push( $scripts , array('syntaxhighlighter-handler') );

        if( empty( $this->usedbrushes ) ){
            $usedbrushes["php"] = true;
        }else{
            $usedbrushes = $this->usedbrushes;
        }

        foreach ( $usedbrushes as $brush => $unused ){
            $scripts[] = array( 'syntaxhighlighter-brush-' . strtolower( $brush ) );
        }

        return $scripts;
    }

    function styles(){
        $theme = ( !empty($this->themes[$this->atts['theme']]) ) ? strtolower($this->atts['theme']) : "default";

        $styles = array();

        if( $theme == "none" ){
            array_push( $styles , array( 'syntaxhighlighter-core' ) );
        }else{
            $theme = 'syntaxhighlighter-theme-' . $theme;
            array_push( $styles , array( 'syntaxhighlighter-core' ) );
            array_push( $styles , array( $theme ) );
        }

        return $styles;
    }

	// Simple function for escaping just single quotes (the original js_escape() escapes more than we need)
	function js_escape_singlequotes( $string ) {
		return str_replace( "'", "\'", $string );
	}

    function contextmenu( $context_menu ){
        $code_syntax_highlighter_menu = $context_menu->create_menu( "code-syntax-highlighter"  , __( "Code Syntax Highlighter" , "site-editor" ) , 'code-syntax-highlighter' , 'class' , 'element' , '' , "sed_code_syntax_highlighter" , array() );
    }

	// HTML entity encode the contents of shortcodes. Expects slashed content. Aborts if AJAX.
	function encode_shortcode_contents_slashed_noquickedit( $content ) {

		// In certain weird circumstances, the content gets run through "content_save_pre" twice
		// Keep track and don't allow this filter to be run twice
		// I couldn't easily figure out why this happens and didn't bother looking into it further as this works fine
		if ( true == $this->content_save_pre_ran ) {
			return $content;
		}
		$this->content_save_pre_ran = true;

		// Post quick edits aren't decoded for display, so we don't need to encode them (again)
		// This also aborts for (un)trashing to avoid extra encoding.
		if ( empty( $_POST ) || ( ! empty( $_POST['action'] ) && 'inline-save' == $_POST['action'] ) ) {
			return $content;
		}

		return $this->encode_shortcode_contents_slashed( $content );
	}

	// HTML entity encode the contents of shortcodes. Expects slashed content.
	function encode_shortcode_contents_slashed( $content ) {
		return addslashes( $this->encode_shortcode_contents( stripslashes( $content ) ) );
	}

	// HTML entity encode the contents of shortcodes
	function encode_shortcode_contents( $content ) {
		return $this->shortcode_hack( $content, array( $this, 'encode_shortcode_contents_callback' ) );
	}


	// The callback function for SyntaxHighlighter::encode_shortcode_contents()
	function encode_shortcode_contents_callback( $atts, $code = '', $tag = false ) {
		$this->encoded = true;
		$code = str_replace( array_keys($this->specialchars), array_values($this->specialchars), htmlspecialchars( $code ) );
		return '[' . $tag . $this->atts2string( $atts ) . "]{$code}[/$tag]";
	}

	// A filter function that runs do_shortcode() but only with this plugin's shortcodes
	function shortcode_hack( $content, $callback ) {
		global $shortcode_tags;

		// Backup current registered shortcodes and clear them all out
		$orig_shortcode_tags = $shortcode_tags;
		remove_all_shortcodes();

		add_shortcode( "sed_code_syntax_highlighter", $callback );

		// Do the shortcodes (only this plugins's are registered)
		$content = $this->do_shortcode_keep_escaped_tags( $content );

		// Put the original shortcodes back
		$shortcode_tags = $orig_shortcode_tags;

		return $content;
	}


	// This is a clone of do_shortcode() that uses a different callback function
	// The new callback function will keep escaped tags escaped, i.e. [[foo]]
	// Up to date as of r18324 (3.2)
	function do_shortcode_keep_escaped_tags( $content ) {
		global $shortcode_tags;

		if (empty($shortcode_tags) || !is_array($shortcode_tags))
			return $content;

		$pattern = get_shortcode_regex();
		return preg_replace_callback('/'.$pattern.'/s', array( $this, 'do_shortcode_tag_keep_escaped_tags' ), $content);
	}


	// Callback for above do_shortcode_keep_escaped_tags() function
	// It's a clone of core's do_shortcode_tag() function with a modification to the escaped shortcode return
	// Up to date as of r18324 (3.2)
	function do_shortcode_tag_keep_escaped_tags( $m ) {
		global $shortcode_tags;

		// allow [[foo]] syntax for escaping a tag
		if ( $m[1] == '[' && $m[6] == ']' ) {
			return $m[0]; // This line was modified for this plugin (no substr call)
		}

		$tag = $m[2];
		$attr = shortcode_parse_atts( $m[3] );

		if ( isset( $m[5] ) ) {
			// enclosing tag - extra parameter
			return $m[1] . call_user_func( $shortcode_tags[$tag], $attr, $m[5], $tag ) . $m[6];
		} else {
			// self-closing tag
			return $m[1] . call_user_func( $shortcode_tags[$tag], $attr, NULL,  $tag ) . $m[6];
		}
	}

	// Transforms an attributes array into a 'key="value"' format (i.e. reverses the process)
	function atts2string( $atts, $quotes = true ) {
		if ( empty($atts) )
			return '';

		$atts = $this->attributefix( $atts );

		// Re-map [code="php"] style tags
		if ( isset($atts[0]) ) {
			if ( empty($atts['language']) )
				$atts['language'] = $atts[0];

			unset($atts[0]);
		}

		$strings = array();
		foreach ( $atts as $key => $value )
			$strings[] = ( $quotes ) ? $key . '="' . esc_attr( $value ) . '"' : $key . '=' . esc_attr( $value );

		return ' ' . implode( ' ', $strings );
	}

	// No-name attribute fixing
	function attributefix( $atts = array() ) {
		if ( empty($atts[0]) )
			return $atts;

		// Quoted value
		if ( 0 !== preg_match( '#=("|\')(.*?)\1#', $atts[0], $match ) )
			$atts[0] = $match[2];

		// Unquoted value
		elseif ( '=' == substr( $atts[0], 0, 1 ) )
			$atts[0] = substr( $atts[0], 1 );

		return $atts;
	}

    function get_codes_models( $models ){
        $codes = array();

        if( !empty( $models ) ){
            $codes_parent_ids = array();

            foreach( $models AS $model ){
                if( $model['tag'] == "sed_code_syntax_highlighter" ){
                    array_push( $codes_parent_ids , $model['id'] );
                }
            }

            foreach( $models AS $index => $model ){
                if( $model['tag'] == "content" && in_array( $model['parent_id'] , $codes_parent_ids ) ){
                    $codes[$index] = $model['content'] ;
                }
            }

        }

        return $codes;
    }

    function encode_shortcode_models( $models ){

        if( !empty( $models ) ){

            $codes = $this->get_codes_models( $models );

            if( !empty( $codes ) ){
                foreach( $codes AS $index => $code ){
                    $code = str_replace( array_keys($this->specialchars), array_values($this->specialchars), htmlspecialchars( $code ) );
                    $models[$index]['content'] = $code;
                }
            }
        }

        return $models;
    }


    function decode_shortcode_models( $models ){

        if( !empty( $models ) ){

            $codes = $this->get_codes_models( $models );

            if( !empty( $codes ) ){
                foreach( $codes AS $index => $code ){
                    $code = str_replace(  array_values($this->specialchars), array_keys($this->specialchars), htmlspecialchars_decode( $code ) );
                    $models[$index]['content'] = $code;
                }
            }
        }

        return $models;
    }


    function decode_theme_content( $models ){

        if( !empty( $models ) && is_array( $models ) ){
            $models = $this->decode_shortcode_models( $models );
        }

        return $models;
    }

    function encode_post_content( $models ){

        if( !empty( $models ) && is_array( $models ) ){
            $models = $this->encode_shortcode_models( $models );
        }

        return $models;
    }

    function encode_theme_content( $settings , $option_name = null , $sed_page_id = null  , $sed_page_type = null ){

        if( isset( $settings['theme_content'] ) && !empty( $settings['theme_content'] ) ){

            foreach( $settings['theme_content'] AS $index => $models  ){
                $settings['theme_content'][$index] = $this->encode_shortcode_models( $models );
            }
        }

        return $settings;
    }

    function encode_sub_themes_content( $content_models , $manager ){

        if(isset( $_POST['sed_app_editor'] ) && $_POST['sed_app_editor'] == "save" ){

            if( !empty( $content_models ) ){
                foreach( $content_models AS  $theme_id => $theme_row  ){
                    $content_models[$theme_id] = $this->encode_shortcode_models( $theme_row );
                }
            }
        }

        return $content_models;
    }

}

new PBCodeSyntaxHighlighter;

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "content" ,                 	 //  Group Module
    "name"        => "code-syntax-highlighter",        //  Module Name
    "title"       => __( "Code Syntax Highlighter" , "site-editor" ),
    "description" => __("","site-editor"),
    "icon"        => "icon-codesyntaxhighlighter",
    "shortcode"   => "sed_code_syntax_highlighter",    //  Shortcode Name
    //"has_extra_spacing"   =>  true ,
    "sub_modules"   => array('paragraph'),
    //"transport"   => "ajax" ,
    "tpl_type"    => "underscore" ,
    "js_module"   => array( 'sed-code-syntax-highlighter-module', 'code-syntax-highlighter/js/sed-code-syntax-highlighter-module.min.js', array('sed-frontend-editor') )
    //"helper_shortcodes" => array('sed_items_code_syntax_highlighter_inner'),
));



