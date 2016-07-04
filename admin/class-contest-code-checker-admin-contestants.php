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

		$this->display_contestants();
	}

	/**
	 * Displays the contestants list 
	 */
  	public function display_contestants() {
    	$this->display->contestant_listings();
  	}
}
