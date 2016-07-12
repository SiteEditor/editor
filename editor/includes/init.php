<?php
  defined('_SEDEXEC') or die;

  /*** include the Zmind Script class ***/
  include SED_APP_PATH . DS . 'site_editor_script.class.php';

  //create global $zmind_script
  $site_editor_script = new SiteEditorScript();
  $GLOBALS['site_editor_script'] = $site_editor_script;
  $site_editor_script->default_scripts();

  /*** include the Zmind Style class ***/
  include SED_APP_PATH . DS . 'site_editor_style.class.php';

  //create global $zmind_style
  $site_editor_style = new SiteEditorStyle();
  $GLOBALS['site_editor_style'] = $site_editor_style;
  $site_editor_style->default_styles();

  /*** include the controller class ***/
  include SED_APP_PATH . DS . 'controller_base.class.php';

  /*** include the registry class ***/
  include SED_APP_PATH . DS . 'registry.class.php';

  /*** include the router class ***/
  include SED_APP_PATH . DS . 'router.class.php';

  /*** include the template class ***/
  include SED_APP_PATH . DS . 'template.class.php';

  /*** auto load model classes ***/
  function __autoload($class_name) {
    $filename = strtolower($class_name) . '.class.php';
    $file = SED_MODEL_PATH . DS . $filename;


    if (file_exists($file) == false)
    {
        return false;
    }
  include ($file);
  }
  spl_autoload_register('__autoload');

  /*** a new registry object ***/
  $registry = new registry;

  /*** create the browser registry object ***/
  $registry->browser = Browser::getInstance();

  //var_dump($registry->browser->getBrowser());
  /*** create the database registry object ***/
  // $registry->db = db::getInstance();
