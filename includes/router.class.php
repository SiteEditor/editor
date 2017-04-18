<?php

class router {
 /*
 * @the registry
 */
 private $registry;

 /*
 * @the controller path
 */
 private $path;

 private $args = array();

 public $file;

 public $controller;

 public $action;

 public $type = "front-end";  //front-end OR Back-end

 function __construct($registry,$type = "front-end") {
        $this->registry = $registry;
        $this->type = $type;
 }

 /**
 *
 * @set controller directory path
 *
 * @param string $path
 *
 * @return void
 *
 */
 function setPath($path) {

  
	/*** check if path i sa directory ***/
	if (is_dir($path) == false)
	{
		throw new Exception ('Invalid controller path: `' . $path . '`');
	}
	/*** set the path ***/
 	$this->path = $path;
}


 /**
 *
 * @load the controller
 *
 * @access public
 *
 * @return void
 *
 */
 public function loader()
 {
	/*** check the route ***/
	$this->getController();

	/*** if the file is not there diaf ***/
	if (is_readable($this->file) == false)
	{
		$this->file = $this->path.'/error404.php';
                $this->controller = 'error404';
	}

	/*** include the controller ***/
	include $this->file;

	/*** a new controller class instance ***/
	$class = $this->controller . 'Controller';
	$controller = new $class($this->registry);

	/*** check if the action is callable ***/
	if (is_callable(array($controller, $this->action)) == false)
	{
		$action = 'index';
	}
	else
	{
		$action = $this->action;
	}
	/*** run the action ***/
	$controller->$action();
 }


 /**
 *
 * @get the controller
 *
 * @access private
 *
 * @return void
 *
 */
private function getController() {

    if( $this->type == "front-end" )
    {
        $route = ( empty( $_REQUEST['option'] ) ) ? '' : sanitize_text_field( $_REQUEST['option'] );
    }else{
        $route = ( empty( $_REQUEST['page'] ) ) ? '' : sanitize_text_field( $_REQUEST['page'] );

    }
	/*** get the route from the url ***/
    $this->action = (empty($_GET['action'])) ? '' : sanitize_text_field( $_GET['action'] );

	if (empty($route))
	{
		$route = 'index';
	}
	else
	{
		$this->controller = $route;
	}

	if (empty($this->controller))
	{
		$this->controller = 'index';
	}

	/*** Get action ***/
	if (empty($this->action))
	{
		$this->action = '';
	}

	/*** set the file path ***/
	$this->file = $this->path .'/'. $this->controller . '.php';
}


}