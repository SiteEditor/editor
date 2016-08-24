<?php
/**
 * Page Builder Options Manager classes
 *
 * @package SiteEditor
 * @subpackage pagebuilder
 * @since 1.0.0
 */

/**
 * Page Builder Options Manager class.
 *
 * Manage all Page Builder Options
 *
 * For manage and send to SiteEditor Options Manager Class
 *
 * @since 1.0.0
 */

final class SedPageBuilderOptionsManager{

    /**
     * Registered instances of SiteEditorField.
     *
     * @since 1.0.0
     * @access protected
     * @var array
     */
    protected $fields = array();

    /**
     * SiteEditorOptionsManager constructor.
     */
    function __construct(  ) {

        add_filter( "sed_addon_settings"    , array($this,'addon_settings') );

        add_filter( "sed_js_I18n"           , array($this,'js_I18n'));

    }

    /**
     * Set Vars for script localize
     *
     * @param $I18n : array of localize vars fo js
     * @since 1.0.0
     * @access public
     * @return array
     */
    public function js_I18n( $I18n ){

        $I18n['custom_size']            =  __("Custom Size","site-editor");
        $I18n['organize_tab_title']     =  __("Edit Gallery","site-editor");
        $I18n['update_btn_title']       =  __("Update gallery","site-editor");
        $I18n['cancel_btn_title']       =  __("Cancel","site-editor");
        $I18n['images_gallery_update']  =  __("Update Images gallery","site-editor");
        $I18n['add_btn_title']          =  __("Add To Gallery","site-editor");

        return $I18n;
    }

    

}


