<?php

/**
 * The admin-specific functionality for managing contestants
 *
 * @since      1.0.0
 *
 * @package    Contest_Code_Checker
 * @subpackage Contest_Code_Checker/admin
 */

/**
 * The admin-specific functionality for managing contestants
 *
 * Defines the functionality to export contestants and view them
 *
 * @package    Contest_Code_Checker
 * @subpackage Contest_Code_Checker/admin
 * @author     Mike de Libero <mikede@mde-dev.com>
 */
class CCC_Contest_Code_Checker_Admin_Contestants {

	/**
	 * Holds the display object
	 * @var CCC_Contest_Code_Checker_Admin_Displays
	 */
	private $display;

	/**
	 * Constructor for the object
	 */
	public function __construct() {
		$this->display = new CCC_Contest_Code_Checker_Admin_Displays();
	}

	/**
	 * Figures out and displays the correct page for contest codes
	 */
	public function display_page() {
		$action = "listing";

		if(isset($_GET['ccc-action'])) {
			$tmp = strtolower($_GET['ccc-action']);

			if($tmp == "delete-contestant") {
				$this->delete_contestant();
			} elseif($tmp == "bulk") {
				if(strtolower($_GET['action']) == "delete") {
					$this->bulk_delete_contestants();
				}
			}
		}

		$this->display_contestants();
	}

	/**
	 * Displays the contestants list
	 */
  	public function display_contestants() {
    	$this->display->contestant_listings();
  	}

  	/**
  	 * Handles deleting just one contestant
  	 * @since 1.0.0
  	 */
  	private function delete_contestant() {
  		if(wp_verify_nonce($_GET['_wpnonce'], "ccc_contestant_nonce") && isset($_GET['contestant']) && (intval($_GET['contestant']) > 0)) {
  			$contestant = new CCC_Contestant($_GET['contestant']);
  			$contestant->delete();
  		}
  	}

  	/**
  	 * Handles deleting of multiple contestants
  	 *
  	 * @since 1.0.0
  	 */
  	private function bulk_delete_contestants() {
  		if( !empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-contestants' )) {
  			$ids = isset( $_GET['contestant'] ) ? $_GET['contestant'] : false;

	    	if ( ! is_array( $ids ) )
	      		$ids = array( $ids );

	    	foreach($ids as $id) {
	      		$c = new CCC_Contestant($id);
	      		$c->delete();
	    	}
  		}
  	}
}
