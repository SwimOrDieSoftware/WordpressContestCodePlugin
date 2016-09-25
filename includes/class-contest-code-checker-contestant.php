<?php
/*
 * Contestant object
 *
 * @package Contest_Code_Checker
 * @subpackage Contest_Code_Checker/includes
 * @since 1.0.0
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The Contest Codes data object class
 *
 * @since 1.0.0
 */
class CCC_Contestant {

	/**
	 * The post type for this object
	 *
	 * @since 1.0.0
	 */
	private $post_type = "ccc_contestants";


  	/**
   	 * The contest code ID
	 *
	 * @since 1.0.0
   	 */
  	private $ID = 0;

	/**
	 * The contestant's whole name
	 *
	 * @since 1.0.0
	 */
	private $name;

	/**
	 * The contestant's first name
	 *
	 * @since 1.0.0
	 */
	private $first_name;

	/**
	 * The contestant's last name
	 *
	 * @since 1.0.0
	 */
	private $last_name;


	/**
	 * The contest code ID
	 *
	 * @var int
	 * @since 1.0.0
	 */
	private $codeID;

	/**
	 * The code object if the code ID is defined
	 *
	 * @var object CCC_Contest_Codes
	 * @since 1.0.0
	 */
	private $code;

	/**
	 * The contestant's email address
	 *
	 * @since 1.0.0
	 */
	private $email;

	/**
	 * The prize for the code, if any
	 *
	 * @since 1.0.0
	 */
	private $invalidCode;

	/**
	 * Declare the default properties in WP_Post as we can't extend it
	 * Anything we've declared above has been removed.
	 */
	public $post_author = 0;
	public $post_date = '0000-00-00 00:00:00';
	public $post_date_gmt = '0000-00-00 00:00:00';
	public $post_content = '';
	public $post_title = '';
	public $post_excerpt = '';
	public $post_status = 'publish';
	public $comment_status = 'open';
	public $ping_status = 'open';
	public $post_password = '';
	public $post_name = '';
	public $to_ping = '';
	public $pinged = '';
	public $post_modified = '0000-00-00 00:00:00';
	public $post_modified_gmt = '0000-00-00 00:00:00';
	public $post_content_filtered = '';
	public $post_parent = 0;
	public $guid = '';
	public $menu_order = 0;
	public $post_mime_type = '';
	public $comment_count = 0;
	public $filter;

	/**
	 * Constructor for the contest code object
	 *
	 * @since 1.0.0
	 */
	public function __construct( $_id = false, $_args = array() ) {

		$contestant = WP_Post::get_instance( $_id );

		return $this->populate_data( $contestant );

	}

	/**
	 * Gets the information populated for a given contestant
	 *
	 * @since 1.0.0
	 * @param  object $contest_code The Contest Code object
	 * @return true               If the retrieval of the data was successful
	 */
	private function populate_data($contestant) {

		if(!is_object($contestant)) {
			return false;
		}

		if(!is_a($contestant, "WP_Post")) {
			return false;
		}

		if($this->post_type !== $contestant->post_type) {
			return false;
		}

		/* Load the values into the object properties */
		foreach($contestant as $key => $value) {
			$this->$key = $value;
		}

		$this->name = get_the_title($this->ID);

		$this->codeID = get_post_meta($this->ID, "ccc_contest_code_id");
		if(intval($this->codeID) > 0) {
			$this->code = new CCC_Contest_Codes($this->codeID);
		}
		$this->email = get_post_meta($this->ID, "ccc_email");
		$this->invalidCode = get_post_meta($this->ID, "ccc_invalid_contest_code");
		$this->first_name = get_post_meta($this->ID, "ccc_contestant_first_name");
		$this->last_name = get_post_meta($this->ID, "ccc_contestant_last_name");

		return true;

	}

	/**
	 * Saves an existing contest code
	 *
	 * @since 1.0.0
	 * @var array $data Array of the attributes for the contest code
	 * @return mixed false if the ID isn't set for this object, otherwise the updated class
	 */
	public function save($data = array()) {

		$default_values = array(
			"post_type"		=> $this->post_type,
			"post_status"	=> $this->post_status,
			"post_title"	=> $this->name,
			"ID" 			=> $this->ID,
		);

		$args = wp_parse_args($data, $default_values);

		$id = wp_insert_post($args, true);

		$contestant = WP_Post::get_instance($id);

		if ( isset( $data['contestCodeID'] ) && ! add_post_meta( $id, 'ccc_contest_code_id', $data['contestCodeID'], true ) ) {
			update_post_meta( $id, 'ccc_contest_code_id', $data['contestCodeID'] );
		}

		if ( ! add_post_meta( $id, 'ccc_email', $data['email'], true ) ) {
			update_post_meta( $id, 'ccc_email', $data['email'] );
		}

		if ( isset( $data['invalidPrizeCode'] ) && ! add_post_meta( $id, "ccc_invalid_contest_code", $data['invalidPrizeCode'], true ) ) {
			update_post_meta( $id, "ccc_invalid_contest_code", $data['invalidPrizeCode'] );
		}

		if ( ! add_post_meta( $id, "ccc_contestant_first_name", $data['first_name'], true ) ) {
			update_post_meta( $id, "ccc_contestant_first_name", $data['first_name'] );
		}

		if ( ! add_post_meta( $id, "ccc_contestant_last_name", $data['last_name'], true ) ) {
			update_post_meta( $id, "ccc_contestant_last_name", $data['last_name'] );
		}

		return $this->populate_data($contestant);
	}

	/**
	 * Deletes a given contest code and its related meta fields
	 *
	 * @since 1.0.0
	 */
	public function delete() {
		if($this->ID > 0) {
			$contestCodeID = get_post_meta($this->ID, "ccc_contest_code_id");

			delete_post_meta($this->ID, "ccc_contest_code_id");
			delete_post_meta($this->ID, "ccc_email");
			delete_post_meta($this->ID, "ccc_invalid_contest_code");
			delete_post_meta($this->ID, "ccc_contestant_first_name");
			delete_post_meta($this->ID, "ccc_contestant_last_name");
			wp_delete_post($this->ID, true);

			// Reset the has been used flag on the cotest code
			$code = new CCC_Contest_Codes($contestCodeID);
			$code->set_has_been_used(false);
		}
	}

	/**
	 * Get the ID for the object
	 *
	 * @since 1.0.0
	 * @return int ID of the contestant object
	 */
	public function get_ID() {
		return $this->ID;
	}

	/**
	 * Get the contestant's first name
	 *
	 * @since 1.0.0
	 * @return the string of the contestant's first name
	 */
	public function get_first_name() {
		if(!isset($this->first_name)) {
			$this->first_name = get_post_meta($this->ID, "ccc_contestant_first_name", true);
		}
		return $this->first_name;
	}

	/**
	 * Get the contestant's last name
	 *
	 * @since 1.0.0
	 * @return the string of the contestant's last name
	 */
	public function get_last_name() {
		if(!isset($this->last_name)) {
			$this->last_name = get_post_meta($this->ID, "ccc_contestant_last_name", true);
		}
		return $this->last_name;
	}

	/**
	 * Gets the contest code ID that the user etnered
	 *
	 * @since 1.0.0
	 * @return int the contest code ID which can be used to retrieve the contest code information
	 */
	public function get_code_id() {
		if(!isset($this->codeID)) {
			$this->codeID = get_post_meta($this->ID, "ccc_contest_code_id", true);
		}

		return $this->codeID;
	}

	/**
	 * Returns the contest code that the user entered
	 *
	 * @since 1.0.0
	 * @return string the contest code that the user entered
	 */
	public function get_code() {
		if(!isset($this->code) && ($this->get_code_id() > 0)) {
			$this->code = new CCC_Contest_Codes($this->get_code_id());
		}

		return $this->code;
	}

	/**
	 * Returns the email address associated with this user
	 *
	 * @return string the contestant's email address
	 */
	public function get_email() {
		if(!isset($this->email)) {
			$this->email = get_post_meta($this->ID, "ccc_email", true);
		}

		return $this->email;
	}

	/**
	 * If a user enter in a contest code that is not in our database it will be available here
	 *
	 * @return string The invalid contest code that the user entered
	 */
	public function get_invalid_code() {
		if(!isset($this->invalidCode)) {
			$this->invalidCode = get_post_meta($this->ID, "ccc_invalid_contest_code", true);
		}

		return $this->invalidCode;
	}
}
