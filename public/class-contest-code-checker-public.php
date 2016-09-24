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
		wp_enqueue_style( 'wp-jquery-ui-dialog' );

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
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( $this->plugin_name,
							plugin_dir_url( __FILE__ ) . 'js/contest-code-checker-public.js',
							array( 'jquery', 'jquery-validate' ),
							$this->version,
							false );

		if( get_option( "ccc_display_popover" ) === 'Y' ) {

			wp_enqueue_script( "contest-code-checker-ajax",
								plugin_dir_url( __FILE__ ) . 'js/contest-code-checker-ajax.js',
								array( 'jquery' ),
								$this->version,
								false );

			$popup_width = 0;
			$popup_height = 0;

			if( get_option( 'ccc_pop_up_width' ) != 0 ) {
				$popup_width = get_option( 'ccc_pop_up_width' );
			}

			if( get_option( 'ccc_pop_up_height' ) != 0 ) {
				$popup_height = get_option( 'ccc_pop_up_height' );
			}

			wp_localize_script( "contest-code-checker-ajax",
								'contest_code_data',
								array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),
								 	   'popup_width' => $popup_width,
									   'popup_height' => $popup_height ) );
		}
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
	 * Handles the ajax request for checking the contest code
	 * @return string JSON response with either winning or losing codes
	 */
	public function ajax_handle_contest_code() {
		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'contest_code_frontend_form' ) ) {
			echo '{"msg": "Invalid nonce"}';
			die();
		}

		do_action("ccc_handle_contest_code_submission", $_POST);

		$is_winner = $this->is_winning_code( $_POST['contestants_code'],
											 $_POST['contestants_first_name'],
											 $_POST['contestants_last_name'],
											 $_POST['contestants_email'] );


		$message = $this->display->get_losing_message();

		if( $is_winner['is_winner'] ) {
			$message = $this->display->get_winning_message( $is_winner['code'] );
		}

		if( $is_winner['is_invalid'] ) {
			$message = $this->display->get_invalid_code_message();
		}

		if( $is_winner['already_used'] ) {
			$message = $this->display->get_already_used_code_message();
		}

		$result = array("is_winner" => $is_winner['is_winner'], "message" => $message);

		echo json_encode($result);
		die();
	}

	/**
	 * Checks the contest code and records the information
	 *
	 * @return string The HTML for if the persond won or lost
	 */
	private function check_contest_code() {

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'contest_code_frontend_form' ) ) {
			return $this->get_contest_code_form();
		}

		do_action("ccc_handle_contest_code_submission", $_POST);

		$is_winner = $this->is_winning_code( $_POST['contestants_code'],
											 $_POST['contestants_name'],
											 $_POST['contestants_last_name'],
											 $_POST['contestants_email'] );

		if( $is_winner['is_winner'] ) {
			return $this->display_winning_message($is_winner['code']);
		}

		if( $is_winner['is_invalid'] ) {
			return $this->display_invalid_message();
		}

		if( $is_winner['already_used'] ) {
			return $this->display_already_used_message();
		}

		return $this->display_losing_message();

	}

	private function is_winning_code( $constestants_code, $contestants_first_name, $contestants_last_name, $contestants_email ) {
		global $wpdb;

		$result = array("is_winner" => false, "code" => null, "is_invalid" => false, "already_used" => false);

		if(isset( $constestants_code ) && !empty( $constestants_code )) {
			$cc = trim(strtolower( $constestants_code ) );

			// Try to find the contest code....
			$sql = "SELECT ID FROM ".$wpdb->posts." WHERE post_title = %s AND post_type='ccc_codes'";
			$codes = $wpdb->get_results($wpdb->prepare($sql, $cc));
			if(count($codes) > 0) {
				foreach ( $codes as $c ) {
					$hasBeenUsed = get_post_meta($c->ID, "ccc_has_been_used", true);
					if( ! boolval($hasBeenUsed) ) {
						$customer = new CCC_Contestant();
						$code = new CCC_Contest_Codes($c->ID);

						$data = array(
								"post_title" => $contestants_first_name . ' ' . $contestants_last_name,
								"first_name" => $contestants_first_name,
								"last_name" => $contestants_last_name,
								"contestCodeID" => $c->ID,
								"email" => $contestants_email,
							);
						$customer->save($data);

						// toggle the has been used flag...
						$code->set_has_been_used(true);

						if($code->get_prize() != "") {
							$this->notify_winner($customer, $code);
							$result['is_winner'] = true;
							$result['code'] = $code;
							$result['already_used'] = false;
							return $result;
						}
					} else {
						$result['already_used'] = true;
					}
				}
			} else {
				$result['is_invalid'] = true;
			}

			$customer = new CCC_Contestant();
			$data = array(
					"post_title" => $contestants_first_name . ' ' . $contestants_last_name,
					"first_name" => $contestants_first_name,
					"last_name" => $contestants_last_name,
					 "email" => $contestants_email,
					 "invalidPrizeCode" => $cc,
				);
			$customer->save($data);

		}

		return $result;
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

	private function display_invalid_message() {
		return $this->display->invalid_code_entered();
	}

	private function display_already_used_message() {
		return $this->display->already_used_code_entered();
	}

	private function display_winning_message($code) {
		return $this->display->winning_code_entered($code);
	}

	/**
	 * If the option is enabled to notify the winner via email about their prize then an email is sent
	 * to the winner.
	 *
	 * @param CCC_Contestant $customer A contestant object
	 * @param CCC_Contest_Codes $code  A contest code object
	 * @since 1.0.1
	 *
	 */
	private function notify_winner($customer, $code) {
		if(get_option("ccc_email_winner") == "Y") {
			$body = get_option("ccc_email_winner_body");

			if(!empty($body)) {
				$body .= "\r\n\r\n";
			}

			$additional_prize_info = "<p>" . $code->get_prize_information() . "</p>";
			// Check to see if a generic prize information should be used...
			$args = array(
					'post_type'	=> "ccc_prizes",
					"meta_key"	=> "ccc_prize_codes",
					"meta_value" => $code->get_prize(),
					"meta_compare" => "=",
				);
			$generic_prize = new WP_Query($args);
			if ( $generic_prize->have_posts() ) {
				$generic_prize->the_post();
				$additional_prize_info = apply_filters( 'the_content', get_the_content() );
			}


			$body .= $additional_prize_info;

			$headers = array('Content-Type: text/html; charset=UTF-8');

			wp_mail($customer->get_email(), get_option("ccc_email_winner_subject"), $body, $headers);
		}
	}
}
