<?php
/*
 * Contest Code object
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
class CCC_Contest_Codes {

	/**
	 * The post type for this object
	 *
	 * @since 1.0.0
	 */
	private $post_type = "ccc_codes";


  	/**
   	 * The contest code ID 
	 *
	 * @since 1.0.0
   	 */
  	public $ID = 0;

	/**
	 * The actual contest code 
	 *
	 * @since 1.0.0
	 */
	public $code;

	/**
	 * If the code has been used
	 *
	 * @since 1.0.0
	 */
	public $hasBeenUsed;

	/**
	 * The prize for the code, if any
	 *
	 * @since 1.0.0
	 */
	public $prize;

	/**
	 * The prize information for the code, if any 
	 *
	 * @since 1.0.0
	 */
	public $prizeInformation;

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

		$contest_code = WP_Post::get_instance( $_id );

		return $this->populate_data( $contest_code );

	}

	/**
	 * Gets the information populated for a given contest code 
	 *
	 * @since 1.0.0
	 * @param  object $contest_code The Contest Code object
	 * @return true               If the retrieval of the data was successful
	 */
	private function populate_data($contest_code) {

		if(!is_object($contest_code)) {
			return false;
		}

		if(!is_a($contest_code, "WP_Post")) {
			return false;
		}

		if($this->post_type !== $contest_code->post_type) {
			return false;
		}

		/* Load the values into the object properties */
		foreach($contest_code as $key => $value) {
			$this->$key = $value;
		}

		$this->hasBeenUsed = get_post_meta($this->ID, "ccc_has_been_used", true);
		$this->prize = get_post_meta($this->ID, "ccc_prize", true);
		$this->prizeInformation = get_post_meta($this->ID, "ccc_prize_information", true);

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
			"post_title"	=> $this->code,
			"ID" 			=> $this->ID,
		);
		
		$args = wp_parse_args($data, $default_values);
		
		$id = wp_insert_post($args, true);

		$contest_code = WP_Post::get_instance($id);
		$this->set_has_been_used($data['hasBeenUsed']);

		if ( ! add_post_meta( $id, 'ccc_prize', $data['prize'], true ) ) { 
			update_post_meta( $id, 'ccc_prize', $data['prize'] );
		}

		if ( ! add_post_meta( $id, "ccc_prize_information", $data['prizeInformation'], true ) ) {
			update_post_meta( $id, "ccc_prize_information", $data['prizeInformation'] );
		}

		return $this->populate_data($contest_code);
	}

	/**
	 * Deletes a given contest code and its related meta fields 
	 * 
	 * @since 1.0.0
	 */
	public function delete() {
		if($this->ID > 0) {
			delete_post_meta($this->ID, "ccc_has_been_used");
			delete_post_meta($this->ID, "ccc_prize");
			delete_post_meta($this->ID, "ccc_prize_information");
			wp_delete_post($this->ID, true);
		}
	}

	/**
	 * Get the ID for the object
	 *
	 * @since 1.0.0
	 * @return int ID of the contest code object
	 */
	public function get_ID() {
		return $this->ID;
	}

	/** 
	 * Get the contest code 
	 *
	 * @since 1.0.0
	 * @return the string of the contest code for this specific object
	 */
	public function get_code() {
		if(!isset($this->code)) {
			$this->code = get_the_title($this->ID);
		}
		return $this->code;
	}

	/**
	 * Gets the prize for this contest code 
	 *
	 * @since 1.0.0
	 * @return string The prize for the current contest code, could be empty if there is no prize
	 */
	public function get_prize() {
		if(!isset($this->prize)) {
			$this->prize = get_post_meta($this->ID, "ccc_prize", true);
		}

		return $this->prize;
	}

	/**
	 * Checks to see if the contest code has been used yet or not
	 *
	 * @since 1.0.0
	 * @return boolean Returns true if the contest code has been used, false otherwise
	 */
	public function get_has_been_used() {
		if(!isset($this->hasBeenUsed)) {
			$this->hasBeenUsed = get_post_meta($this->ID, "ccc_has_been_used", true);
		} elseif(!is_bool($this->hasBeenUsed)) {
			$this->hasBeenUsed = false;
		}

		return $this->hasBeenUsed;
	}

	public function get_prize_information() {
		if(!isset($this->prizeInformation)) {
			$this->prizeInformation = get_post_meta($this->ID, "ccc_prize_information", true);
		}

		return $this->prizeInformation;
	}

	public function set_has_been_used($beenUsed) {
		$beenUsed = boolval($beenUsed);

		if ( ! add_post_meta( $this->ID, 'ccc_has_been_used', $beenUsed, true ) ) { 
			update_post_meta( $this->ID, 'ccc_has_been_used', $beenUsed );
		}
	}
}
