<?php

/**
 * The admin-specific functionality for managing prizes
 *
 * @since      1.0.0
 *
 * @package    Contest_Code_Checker
 * @subpackage Contest_Code_Checker/admin
 */

/**
 * The admin-specific functionality for managing prizes
 *
 * Defines the functionality to view prizes and edit them
 *
 * @package    Contest_Code_Checker
 * @subpackage Contest_Code_Checker/admin
 * @author     Mike de Libero <mikede@mde-dev.com>
 */
class CCC_Contest_Code_Checker_Admin_Prizes {

	/**
	 * Holds the display object
	 * @var CCC_Contest_Code_Checker_Admin_Display_Prizes
	 */
	private $display;

	/**
	 * Constructor for the object
	 */
	public function __construct() {
		$this->display = new CCC_Contest_Code_Checker_Admin_Display_Prizes();
	}

	/**
	 * Figures out and displays the correct page for contest codes
	 */
	public function display_page() {
		$action = "listing";

		if(isset($_GET['ccc-action'])) {
			$tmp = strtolower($_GET['ccc-action']);
		}

		if(isset($_REQUEST['ccc-action'])) {
			$action = strtolower($_REQUEST['ccc-action']);
		}

		if($action == "add-prize") {
			$this->display_prizes_form();
			return;
		} elseif($action == "edit-prize") {
			$this->display_prizes_form();
			return;
		} elseif($action == "save-prize") {
			$this->save_prize();
		} elseif($action == "delete-prize") {
			$this->delete_prize();
		} elseif($action == "bulk") {
			if(isset($_GET['action']) && (strtolower($_GET['action']) == "delete")) {
				$this->bulk_delete_prizes();
			}
		}

		$this->display_prizes();
	}

	/**
	 * Displays the prize list
	 */
  	public function display_prizes() {
    	$this->display->prize_listings();
  	}

	/**
  	 * Displays the generic prize form
  	 */
  	public function display_prizes_form() {
  		$prize_id = false;
  		if(isset($_GET['prize']) && ($_GET['prize'] > 0)) {
  			$prize_id = absint($_GET['prize']);
  		}

  		$prize = new CCC_Prizes( $prize_id );
  		$this->display->prize_form( $prize );
  	}

  	/**
  	 * Handles deleting just one prize
  	 * @since 1.0.0
  	 */
  	private function delete_prize() {
  		if(wp_verify_nonce($_GET['_wpnonce'], "ccc_prize_nonce") && isset($_GET['prize']) && (intval($_GET['prize']) > 0)) {
  			$prize = new CCC_Prizes($_GET['prize']);
  			$prize->delete();
  		}
  	}

	/**
  	 * Handles the saving of prizes
  	 */
  	private function save_prize() {
  		if(wp_verify_nonce($_POST['generic-prize-nonce'], "generic-prize-form")) {
	  		$prize_id = false;
	  		if(isset($_POST['prize']) && ($_POST['prize'] > 0)) {
	  			$prize_id = absint($_POST['prize']);
	  		}

	  		$prize = new CCC_Prizes($prize_id);
	  		$data = array();

	  		$data['post_title'] = $_POST['post_title'];
	  		$data['post_content'] = $_POST['post_content'];
	  		$data['prize_codes'] = $_POST['prize_codes'];
	  		$prize->save($data);
	  	}
  	}

  	/**
  	 * Handles deleting of multiple contestants
  	 *
  	 * @since 1.0.0
  	 */
  	private function bulk_delete_prizes() {
  		if( !empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-genericprizes' ) ) {
  			$ids = isset( $_GET['prize'] ) ? $_GET['prize'] : false;

	    	if ( ! is_array( $ids ) )
	      		$ids = array( $ids );

	    	foreach($ids as $id) {
	      		$p = new CCC_Prizes($id);
	      		$p->delete();
	    	}
  		}
  	}
}
