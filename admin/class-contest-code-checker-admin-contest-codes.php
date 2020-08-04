<?php
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Common\Type;

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
 * @subpackage Contest_Code_Checker/ad min
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
		} elseif( $action === 'confirm-delete-contest-codes' ) {
			$this->display_confirm_delete();
			return;
		} elseif($action == "delete-contest-code") {
			$this->delete_contest_code();
		} elseif($action == "handle_import_contest_codes") {
			$this->handle_import();
		} elseif( $action === 'delete_all_contest_codes' ) {
			$this->delete_all_contest_codes();
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
	 * Displays the form to confirm deleting all contest codes that currently exist.
	 *
	 * @since 1.0.3
	 */
	private function display_confirm_delete() {
		$this->display->contest_code_confirm_delete();
	}

  	/**
  	 * Displays the contest code import form.
  	 *
  	 * @since 1.0.0
  	 */
  	public function display_import_form() {
  		$this->display->contest_code_import_form();
  	}

  	/**
  	 * Handles the saving of contest codes
  	 */
  	private function save_contest_code() {
  		if(wp_verify_nonce($_POST['contest-code-nonce'], "contest-code-form")) {
	  		$codeId = false;
	  		if(isset($_POST['contest_code']) && ($_POST['contest_code'] > 0)) {
	  			$codeId = absint($_POST['contest_code']);
	  		}

	  		$code = new CCC_Contest_Codes($codeId);
	  		$data = array();

	  		$data['hasBeenUsed'] = ( isset( $_POST['hasBeenUsed'] ) && ( strtoupper( $_POST['hasBeenUsed'] ) == "Y" ) ) ? true : false;
	  		$data['prize'] = $_POST['prize'];
	  		$data['post_title'] = $_POST['post_title'];
	  		$data['prizeInformation'] = $_POST['prizeInformation'];
	  		$code->save($data);
	  	}
  	}

	private function delete_all_contest_codes() {
		global $wpdb;

		if( wp_verify_nonce( $_POST['contest-code-delete-nonce'], "contest-code-delete-form" ) ) {
			set_time_limit(0); // Set the time limit to forever to handle large number of contest codes from being deleted
			$codes = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'ccc_codes'");
			foreach($codes as $c) {
				$code = new CCC_Contest_Codes($c->ID);
				$code->delete();
			}
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

  	/**
  	 * Handles the importing of contest codes from a given file.
  	 *
  	 * @since 1.0.0
  	 */
  	private function handle_import() {
  		if( !empty($_REQUEST['contest-code-import-nonce']) &&
  			wp_verify_nonce($_REQUEST['contest-code-import-nonce'], "contest-code-import-form") &&
  			(count($_FILES) > 0) ) {

			set_time_limit(0); // Set the time limit to forever to handle large files being imported...

      		$reader = $this->get_file_type_reader($_FILES['importFile']['name']);
      		if ( null !== $reader ) {
      			$reader->open( $_FILES['importFile']['tmp_name'] );
				foreach ( $reader->getSheetIterator() as $sheet ) {
					foreach ( $sheet->getRowIterator() as $row ) {
						$tmp_array = $row->getCells();
						if((count($tmp_array) > 0) && !empty($tmp_array[0])) {
							$code = new CCC_Contest_Codes();
							$data = array();

							$data['post_title'] = $tmp_array[0];

							if(!empty($tmp_array[1])) {
								$data['prize'] = $tmp_array[1];
							}

							if(!empty($tmp_array[2])) {
								$data['prizeInformation'] = $tmp_array[2];
							}

							$code->save($data, true);
						} // if ((count($row)...
					}
				} // foreach ( $reader->getSheetIterator()...
			}
  		} // nonce check if-statement
  	}

  	/**
	 * Gets the file type based on the users uploaded extension.
	 *
	 * @param  string $file_path The file name that the user uploaded for importing.
	 * @return object            Null or the Spout type used for parsing the files.
	 */
	private function get_file_type_reader( $file_path ) {
		$ext = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );

		if ( 'csv' === $ext ) {
			return ReaderEntityFactory::createCSVReader();
		} elseif ( 'xlsx' === $ext ) {
			return ReaderEntityFactory::createXLSXReader();
		} elseif ( 'ods' === $ext ) {
			return ReaderEntityFactory::createODSReader();
		}

		return null;
	}
}
