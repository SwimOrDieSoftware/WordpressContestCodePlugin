<?php

/**
 * The admin-specific functionality for managing contest codes
 *
 * @since      1.0.0
 *
 * @package    Contest_Code_Checker
 * @subpackage Contest_Code_Checker/admin
 */

/**
 * The admin-specific functionality for managing contest codes
 *
 * Defines the functionality to handle CRUD operations and importing/exporting contest codes
 *
 * @package    Contest_Code_Checker
 * @subpackage Contest_Code_Checker/admin
 * @author     Mike de Libero <mikede@mde-dev.com>
 */
class CCC_Contest_Code_Checker_Admin_Contest_Codes {

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

		if(isset($_REQUEST['ccc-action'])) {
			$action = strtolower($_REQUEST['ccc-action']);
		}

		if($action == "add-contest-code") {
			$this->display_codes_form();
			return;
		} elseif($action == "edit-contest-code") {
			$this->display_codes_form();
			return;
		} elseif($action == "save_contest_codes") {
			$this->save_contest_code();
		} elseif($action == "delete-contest-code") {
			$this->delete_contest_code();
		} elseif($action == "bulk") {
			if(isset($_GET['action']) && (strtolower($_GET['action']) == "delete")) {
				$this->bulk_delete_contest_codes();
			}
		}

		$this->display_codes();
	}

	/**
	 * Displays the contest code list 
	 */
  	public function display_codes() {
    	$this->display->contest_code_listings();
  	}

  	/**
  	 * Displays the contest code form 
  	 */
  	public function display_codes_form() {
  		$codeId = false;
  		if(isset($_GET['contest_code']) && ($_GET['contest_code'] > 0)) {
  			$codeId = absint($_GET['contest_code']);
  		}

  		$code = new CCC_Contest_Codes($codeId);
  		$this->display->contest_code_form($code);
  	}

  	/**
  	 * Handles the saving of contest codes
  	 */
  	private function save_contest_code() {
  		if(wp_verify_nonce($_POST['contest-code-nonce'], "contest-code-form")) {
	  		$codeId = false;
	  		if(isset($_GET['contest_code']) && ($_POST['contest_code'] > 0)) {
	  			$codeId = absint($_POST['contest_code']);
	  		}

	  		$code = new CCC_Contest_Codes($codeId);
	  		$data = array();

	  		$data['hasBeenUsed'] = (strtoupper($_POST['hasBeenUsed']) == "Y") ? true : false;
	  		$data['prize'] = $_POST['prize'];
	  		$data['post_title'] = $_POST['post_title'];
	  		$code->save($data);
	  	}
  	}

  	/**
  	 * Deletes a given contest code. 
  	 *
  	 * @since 1.0.0
  	 */
  	private function delete_contest_code() {
  		if(wp_verify_nonce($_GET['_wpnonce'], "ccc_contest_code_nonce") && isset($_GET['contest_code'])) {
  			$code = new CCC_Contest_Codes($_GET['contest_code']);
  			$code->delete();
  		}
  	}

  	/**
  	 * Handles the bulk actions for deleting contest codes
  	 *
  	 * @since 1.0.0
  	 */
  	private function bulk_delete_contest_codes() {
  		if( !empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-contestcodes' )) {

	    	$ids = isset( $_GET['contest_code'] ) ? $_GET['contest_code'] : false;

	    	if ( ! is_array( $ids ) )
	      		$ids = array( $ids );

	    	foreach($ids as $id) {
	      		$c = new CCC_Contest_Codes($id);
	      		$c->delete();
	    	} 
	    }
  	}
}
