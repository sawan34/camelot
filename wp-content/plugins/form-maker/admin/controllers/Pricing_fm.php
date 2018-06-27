<?php
/**
 * Class FMControllerPricing_fm
 */
class FMControllerPricing_fm {
  /**
	* @var $model
	*/
	private $model;
	/**
	* @var $view
	*/
	private $view;
	/**
	* @var string $page
	*/
	private $page;
	
	public function __construct() {
		$this->page 	= WDW_FM_Library::get('page');
		$this->page_url = add_query_arg( array (
										'page' => $this->page,
										WDFM()->nonce => wp_create_nonce(WDFM()->nonce),
									  ), admin_url('admin.php')
								  );
		require_once WDFM()->plugin_dir . "/admin/views/Pricing_fm.php";
		$this->view = new FMViewpricing_fm();		
	}
	
	/**
	* Execute.
	*/
	public function execute() {
		$task = WDW_FM_Library::get('task');
		$id = (int) WDW_FM_Library::get('current_id', 0);
		if (method_exists($this, $task)) {
		  $this->$task($id);
		}
		else {
		  $this->display();
		}
	}

	/**
	* Display.
	*/
	public function display() {
	// Set params for view.
	$params = array();
	$params['page'] 		= $this->page;
	$params['page_url']		= $this->page_url;	
	$this->view->display( $params );
  }
}