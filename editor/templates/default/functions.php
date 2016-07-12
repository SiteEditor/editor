<?php
class siteEditorFunctions{
    var $suffix;

    var $browser;
    /*
     * @registry object
     */
    protected $registry;
    /**
     *
     * @constructor
     *
     * @access public
     *
     * @return void
     *
     */

    function __construct($registry) {
        $this->registry = $registry;
    	$this->suffix = ".min";
        $this->browser = $this->registry->browser;
        $this->base_theme_url = SED_EDITOR_FOLDER_URL."templates/default/";
        $this->base_library_url = SED_EDITOR_FOLDER_URL."libraries/";
    }

    function render(){
       add_action( 'sed_enqueue_scripts' , array( $this , 'theme_scripts' ) );
       $this->theme_styles();
    }

    function theme_scripts(){
        global $site_editor_script;
        $site_editor_script->add("siteeditor-settings-render", $this->base_library_url."siteeditor/siteeditor-settings-render".$this->suffix.".js" , array( 'jquery') , "1.0.0",1 );
        $site_editor_script->add("render-js-theme", $this->base_theme_url."js/render".$this->suffix.".js" , array( 'jquery', 'jquery-ui-full', 'bootstrap') , "1.0.0",1 );
        $site_editor_script->load_scripts(array( 'jquery-ui-timepicker' , 'siteeditor-settings-render' ,'render-js-theme'));
    }

    function theme_styles(){
        global $site_editor_style;
        //main style
        //$site_editor_style->add("theme-style-main", $this->base_theme_url."css/style".$this->suffix.".css" , array( ) ,false, "1.0.0" );
        //$site_editor_style->add("theme-style-main-rtl", $this->base_theme_url."css/style-rtl".$this->suffix.".css" , array( 'theme-style-main') ,true, "1.0.0" );


        //jquery ui custom
        $site_editor_style->add("jquery-ui", $this->base_library_url."jquery/ui/css/jquery-ui".$this->suffix.".css" , array() , false, "1.10.3" );
        $site_editor_style->add("jquery-ui-ie", $this->base_library_url."jquery/ui/css/jquery-ui.ie".$this->suffix.".css" , array('jquery-ui') , false, "1.10.3" );

        $site_editor_style->add("lazyloading", $this->base_library_url."lazyload/css/bttrlazyloading".$this->suffix.".css" , array() , false, "1.10.3" );

        $site_editor_style->add("impromptu-theme", $this->base_library_url."impromptu/themes/base".$this->suffix.".css" , array() , false, "1.10.3" );
        //font style
        $site_editor_style->add("theme-font-site_editor", $this->base_theme_url."css/site_editor".$this->suffix.".css" , array() ,false, "1.0.0" );
        $site_editor_style->add("theme-font-animation", $this->base_theme_url."css/animation".$this->suffix.".css" , array('theme-font-site_editor') ,false, "1.0.0" );
        $site_editor_style->add("theme-font-ie7", $this->base_theme_url."css/site_editor-ie7".$this->suffix.".css" , array('theme-font-site_editor') ,false, "1.0.0" );

        $browser = $this->browser;                 //'theme-style-main'
        if( $browser->getBrowser() == $browser::BROWSER_IE && $browser->getVersion() == 7 ){   //'theme-font-site_editor','theme-font-animation','theme-font-ie7', 'impromptu-theme',
            $site_editor_style->load_styles(array('lazyloading' , 'scrollbar','chosen'));
        }else{                                                                                 //'theme-font-site_editor','theme-font-animation','impromptu-theme',
            $site_editor_style->load_styles(array('lazyloading' , 'scrollbar','chosen'));
        }

        if( $browser->getBrowser() == $browser::BROWSER_IE && $browser->getVersion() == 10 ){
            $site_editor_style->load_styles(array('colorpicker','jquery-ui','jquery-ui-ie'));
        }else{
            $site_editor_style->load_styles(array('colorpicker')); // ,'jquery-ui'
        }



    }

}
