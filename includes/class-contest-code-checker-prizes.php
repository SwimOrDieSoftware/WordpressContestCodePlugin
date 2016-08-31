<?php
/*
 * Generic Prize object
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
class CCC_Prizes {

	/**
	 * The post type for this object
	 *
	 * @since 1.0.0
	 */
	private $post_type = "ccc_prizes";


  	/**
   	 * The contest code ID
	 *
	 * @since 1.0.0
   	 */
  	public $ID = 0;

	/**
	 * The prize name
	 *
	 * @since 1.0.0
	 */
	public $name;

	/**
	 * The description of the prize
	 *
	 * @since 1.0.0
	 */
	public $description;

	/**
	 * The specific prize codes that this prize should be displayed for
	 *
	 * @since 1.0.0
	 */
	public $prizes;

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
	 * Constructor for the prize object
	 *
	 * @since 1.0.0
	 */
	public function __construct( $_id = false, $_args = array() ) {

		$prize = WP_Post::get_instance( $_id );

		return $this->populate_data( $prize );

	}

	/**
	 * Gets the information populated for a given generic prize
	 *
	 * @since 1.0.0
	 * @param  object $prize The Prize object
	 * @return true               If the retrieval of the data was successful
	 */
	private function populate_data( $prize ) {

		if( ! is_object( $prize ) ) {
			return false;
		}

		if( ! is_a( $prize, "WP_Post" ) ) {
			return false;
		}

		if( $this->post_type !== $prize->post_type ) {
			return false;
		}

		/* Load the values into the object properties */
		foreach($prize as $key => $value) {
			$this->$key = $value;
		}

		$this->prizes = get_post_meta($this->ID, "ccc_prize_codes");
		$this->name = $prize->post_title;
		$this->description = $prize->post_content;

		return true;

	}

	/**
	 * Saves an existing prize
	 *
	 * @since 1.0.0
	 * @var array $data Array of the attributes for the prize
	 * @return mixed false if the ID isn't set for this object, otherwise the updated class
	 */
	public function save($data = array()) {

		$default_values = array(
			"post_type"		=> $this->post_type,
			"post_status"	=> $this->post_status,
			"post_title"	=> $this->name,
			"post_content"  => $this->description,
			"ID" 			=> $this->ID,
		);

		$args = wp_parse_args($data, $default_values);

		$id = wp_insert_post($args, true);

		$prize = WP_Post::get_instance($id);

		// Handle the prize codes...
		delete_post_meta( $id, "ccc_prize_codes" );

		// TODO: Make sure this works...
		if( is_array($data['prize_codes']) ) {
			foreach($data['prize_codes'] as $pc) {
				add_post_meta( $id, "ccc_prize_codes", $pc );
			}
		}

		return $this->populate_data($prize);
	}

	/**
	 * Deletes a given generic prize and its related meta fields
	 *
	 * @since 1.0.0
	 */
	public function delete() {
		if($this->ID > 0) {
			delete_post_meta($this->ID, "ccc_prize_codes");
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
	 * Get the generic prize name
	 *
	 * @since 1.0.0
	 * @return the name of the generic prize
	 */
	public function get_name() {
		if(!isset($this->name)) {
			$this->name = get_the_title($this->ID);
		}
		return $this->name;
	}

	/**
	 * Gets the
	 *
	 * @since 1.0.0
	 * @return string The prize for the current contest code, could be empty if there is no prize
	 */
	public function get_description() {
		if(!isset($this->description)) {
			$this->description = get_the_content($this->ID);
		}

		return $this->description;
	}

	public function get_prizes() {
		if(!isset($this->prizes)) {
			$this->prizes = get_post_meta($this->ID, "ccc_prize_codes");

			if( empty($this->prizes) ) {
				$this->prizes = array();
			}
		}

		return $this->prizes;
	}
}
