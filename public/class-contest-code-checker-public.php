<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Contest_Code_Checker
 * @subpackage Contest_Code_Checker/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Contest_Code_Checker
 * @subpackage Contest_Code_Checker/public
 * @author     Your Name <email@example.com>
 */
class CCC_Contest_Code_Checker_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Holds the display object
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object $display 	An object of the display class
	 */
	private $display;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->load_dependencies();
		$this->display = new CCC_Contest_Code_Checker_Public_Displays();

	}

	private function load_dependencies() {
		$baseDir = plugin_dir_path( dirname( __FILE__ ) );

		require_once $baseDir."public/partials/contest-code-checker-public-display.php";
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/contest-code-checker-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script('jquery-validate', plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/contest-code-checker-public.js', array( 'jquery', 'jquery-validate' ), $this->version, false );

	}

	/**
	 * Shortcode handling functionl, will call the appropriate function depending on the step the short code is on
	 * 
	 * @param  array $atts attributes that are specified in the shortcode
	 * @return string       the output that should be displayed...
	 */
	public function handle_shortcode($atts) {
		$output = "";
		$action = "";

		if($this->is_contest_running()) {
			if(isset($_REQUEST['step'])) {
				$action = strtolower($_REQUEST['step']);
			}

			if($action == "check_code") {
				$output = $this->check_contest_code();
			} else {
				$output = $this->get_contest_code_form();	
			}	
		} else {
			$output = $this->contest_has_not_started();
		}
		
		
		return $output;	
	}

	/**
	 * Returns the contest code form that allows users to enter in their information. 
	 * 
	 * @return string The HTML for the form...
	 */
	private function get_contest_code_form() {
		return $this->display->contest_form();
	}

	/**
	 * Checks the contest code and records the information
	 *
	 * @return string The HTML for if the persond won or lost
	 */
	private function check_contest_code() {
		global $wpdb; 

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'contest_code_frontend_form' ) ) {
			return $this->get_contest_code_form();
		}


		if(isset($_POST['contestants_code']) && !empty($_POST['contestants_code'])) {
			$cc = trim(strtolower($_POST['contestants_code']));

			// Try to find the contest code....
			$sql = "SELECT ID FROM ".$wpdb->posts." WHERE post_title = %s AND post_type='ccc_codes'";
			$codes = $wpdb->get_results($wpdb->prepare($sql, $cc));
			if(count($codes) > 0) {
				foreach ( $codes as $c ) {
					$hasBeenUsed = get_post_meta($c->ID, "ccc_has_been_used", true);
					if(!boolval($hasBeenUsed)) {	
						$customer = new CCC_Contestant();
						$code = new CCC_Contest_Codes($c->ID);

						$data = array(
								"post_title" => $_POST['contestants_name'],
								"contestCodeID" => $c->ID, 
								"email" => $_POST['contestants_email'],
							);
						$customer->save($data);

						// toggle the has been used flag...
						$code->set_has_been_used(true);

						if($code->get_prize() != "") {
							return $this->display_winning_message($code);
						}
					}
				}
			} else {
				$customer = new CCC_Contestant();

				$data = array(
						"post_title" => $_POST['contestants_name'],
						 "email" => $_POST['contestants_email'],
						 "invalidPrizeCode" => $cc,
					);
				$customer->save($data);
			}

		} 

		return $this->display_losing_message();
		
	}

	/**
	 * Gets the text for when the contest has not yet started.
	 *
	 * @since 1.0.0
	 * @return string Text for whent he contest has not yet started
	 */
	private function contest_has_not_started() {
		return $this->display->contest_has_not_started();
	}

	/**
	 * Checks to see if the contest is currently running
	 *
	 * @since 1.0.0
	 * @return boolean True if the contest is within a given date range
	 */
	private function is_contest_running() {
		$start_date = get_option("ccc_start_date");
		$end_date = get_option("ccc_end_date");
		$now = time();

		if(empty($end_date) && empty($start_date)) {
			return true;
		}

		if(empty($end_date)) {
			$start_date = strtotime($start_date);	

			return ($start_date <= $now);
		}

		if(empty($start_date)) {
			$end_date = strtotime($end_date);

			return ($end_date >= $now);
		}

		$start_date = strtotime(get_option("ccc_start_date"));
		$end_date = strtotime(get_option("ccc_end_date"));
		
		if(($start_date <= $now) && ($end_date >= $now)) {
			return true;
		}

		return false;
	}

	private function display_losing_message() {
		return $this->display->losing_code_entered();
	}

	private function display_winning_message($code) {
		return $this->display->winning_code_entered($code);
	}
}
