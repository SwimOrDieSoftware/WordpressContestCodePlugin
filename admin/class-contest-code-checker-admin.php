<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Contest_Code_Checker
 * @subpackage Contest_Code_Checker/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Contest_Code_Checker
 * @subpackage Contest_Code_Checker/admin
 * @author     Mike de Libero <mikede@mde-dev.com>
 */
class CCC_Contest_Code_Checker_Admin {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->load_dependencies();

		if(isset($_GET['page']) && 
		   (strToLower($_GET['page']) == 'contest-code-contestants') && 
		   (strtolower($_GET['ccc-action']) == "contest-code-export-winners")) {
		   	add_action('init', array($this, "export_winners"));
		}


		if(isset($_GET['page']) && 
		   (strToLower($_GET['page']) == 'contest-code-contestants') && 
		   (strtolower($_GET['ccc-action']) == "contest-code-export")) {
		   	add_action('init', array($this, "export_all"));
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( "contest-code-jquery-ui-css",
											plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css',
											array(),
											$this->version,
											'all' );
		wp_enqueue_style( $this->plugin_name,
											plugin_dir_url( __FILE__ ) . 'css/contest-code-checker-admin.css',
											array(),
											$this->version,
											'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name,
											 plugin_dir_url( __FILE__ ) . 'js/contest-code-checker-admin.js',
											 array( 'jquery', 'jquery-ui-datepicker' ),
											 $this->version,
											 false );

	}

	/**
	 * Get required capability to access settings page and view dashboard widgets.
	 *
	 * @return string
	 */
	public function get_required_capability() {

		$capability = 'manage_options';

		/**
		 * Filters the required user capability to access the Contest Code Checker settings pages
		 *
		 * Defaults to `manage_options`
		 *
		 * @since 1.0
		 * @param string $capability
		 * @see https://codex.wordpress.org/Roles_and_Capabilities
		 */
		$capability = (string) apply_filters( 'ccc_admin_required_capability', $capability );

		return $capability;
	}

	/**
	 * Registers the admin navigation menu.
	 *
	 * @since 1.0.0
	 */
	public function register_menus() {
		add_menu_page(__("Contest Code Checker", "contest-code"),
					  __("Contest Code Checker", "contest-code"),
					  $this->get_required_capability(),
					  "contest-code-checker",
					  array($this, "show_settings_page"));

		add_submenu_page("contest-code-checker",
			__("Contestants", "contest-code"),
			__("Contestants", "contest-code"),
			$this->get_required_capability(),
			"contest-code-contestants",
			array($this, "show_contestants_page"));

		add_submenu_page("contest-code-checker",
			__("Contest Codes", "contest-code"),
			__("Contest Codes", "contest-code"),
			$this->get_required_capability(),
			"contest-codes",
			array($this, "show_contest_codes_page"));

		add_submenu_page("contest-code-checker",
			__("Import Contest Codes", "contest-code"),
			__("Import Contest Codes", "contest-code"),
			$this->get_required_capability(), 
			"contest-codes-import",
			array($this, "show_import_contest_codes_page"));
	}

	/**
	 * Registers the settings for the plugin.
	 *
	 * @since 1.0.0
	 */
	public function wireup_settings() {
		add_settings_section("contest_code_checker_options", __("General Settings", "contest-code"), null, "ccc_options");

		add_settings_field("ccc_start_date",
											 __("Start date of contest", "contest-code"),
											 array($this, "display_start_date_field"),
											 "ccc_options",
											 "contest_code_checker_options");

		add_settings_field("ccc_end_date",
											 __("End date of contest", "contest-code"),
											 array($this, "display_end_date_field"),
											 "ccc_options",
											 "contest_code_checker_options");

		add_settings_field("ccc_email_winner", 
											__("Email winners with prize information", "contest-code"), 
											array($this, "display_email_winner_field"), 
											"ccc_options", 
											"contest_code_checker_options");

		add_settings_field("ccc_email_winner_subject", 
											__("Winner email subject", "contest-code"), 
											array($this, "display_email_winner_subject_field"), 
											"ccc_options", 
											"contest_code_checker_options");

		add_settings_field("ccc_email_winner_body", 
											__("Beginning of winner email", "contest-code"), 
											array($this, "display_email_winner_body_field"), 
											"ccc_options", 
											"contest_code_checker_options");

		add_settings_field("ccc_text_winning",
											 __("Text for When Someone Wins", "contest-code"),
											 array($this, "display_text_winning_field"),
											 "ccc_options",
											 "contest_code_checker_options");

		add_settings_field("ccc_text_losing",
											 __("Text for When Someone Loses", "contest-code"),
											 array($this, "display_text_losing_field"),
											 "ccc_options",
											 "contest_code_checker_options");

		add_settings_field("ccc_contest_not_running", 
											__("Text for when the contest is not currently running", "contest-code"),
											array($this, "display_contest_not_running_field"),
											"ccc_options",
											"contest_code_checker_options");

		register_setting("contest_code_checker_options", "ccc_start_date");
		register_setting("contest_code_checker_options", "ccc_end_date");
		register_setting("contest_code_checker_options", "ccc_email_winner");
		register_setting("contest_code_checker_options", "ccc_email_winner_body");
		register_setting("contest_code_checker_options", "ccc_email_winner_subject");
		register_setting("contest_code_checker_options", "ccc_text_winning");
		register_setting("contest_code_checker_options", "ccc_text_losing");
		register_setting("contest_code_checker_options", "ccc_contest_not_running");
	}

	/**
	 * Shows the general settings for the contest code checker
	 *
	 * @since 1.0.0
	 */
	public function show_settings_page() {
		$settings = new CCC_Contest_Code_Checker_Admin_Settings();
		$settings->display_settings();
	}

	public function show_contestants_page() {
		$contestants = new CCC_Contest_Code_Checker_Admin_Contestants();
		$contestants->display_page();
	}

	public function show_contest_codes_page() {
		$contest_codes = new CCC_Contest_Code_Checker_Admin_Contest_Codes();
		$contest_codes->display_page();
	}

	public function show_import_contest_codes_page() {
		$contest_codes = new CCC_Contest_Code_Checker_Admin_Contest_Codes();
		$contest_codes->display_import_form();
	}

	/**
	 * [load_dependencies description]
	 * @return [type] [description]
	 */
	private function load_dependencies() {
		$baseDir = plugin_dir_path( dirname( __FILE__ ) );

		require_once $baseDir . 'admin/partials/contest-code-checker-admin-settings.php';
		require_once $baseDir . 'admin/partials/contest-code-checker-admin-display.php';
		require_once $baseDir . 'admin/contest_codes/class-contest-codes-table.php';
		require_once $baseDir . 'admin/contestants/class-contestants-table.php';
		require_once $baseDir . 'admin/class-contest-code-checker-admin-contest-codes.php';
		require_once $baseDir . 'admin/class-contest-code-checker-admin-contestants.php';

	}

	/**
	 *	Displays the start date setting field
	 *
	 * @since 1.0
	 */
	public function display_start_date_field($args) {
		?>
			<input type="text" name="ccc_start_date" id="ccc_start_date" value="<?php echo esc_attr(get_option("ccc_start_date")); ?>" />
		<?php
	}

	/**
	 *  Displays the end date setting field
	 *
	 * @since 1.0
	 */
	public function display_end_date_field($args) {
		?>
			<input type="text" name="ccc_end_date" id="ccc_end_date" value="<?php echo esc_attr(get_option("ccc_end_date")); ?>" />
		<?php
	}

	/**
	 *  Displays the email the winner setting field
	 *
	 * @since 1.0.1
	 */
	public function display_email_winner_field($args) {
		?>
			<input type="checkbox" name="ccc_email_winner" id="ccc_email_winner" value="Y" <?php echo (get_option("ccc_email_winner") == "Y") ? "checked=\"checked\"" : ""; ?>/> 
		<?php
	}

	/**
	 *  Displays the winner email subject setting field
	 *
	 * @since 1.0
	 */
	public function display_email_winner_subject_field($args) {
		?>
			<input type="text" name="ccc_email_winner_subject" id="ccc_email_winner_subject" value="<?php echo esc_attr(get_option("ccc_email_winner_subject")); ?>" class="large-text" />
		<?php
	}
	
	/**
	 *  Displays the winner email body text setting field
	 *
	 * @since 1.0
	 */
	public function display_email_winner_body_field($args) {
		?>
			<textarea name="ccc_email_winner_body" id="ccc_email_winner_body" class="large-text" rows="10"><?php echo esc_html(get_option("ccc_email_winner_body")); ?></textarea>
		<?
	}

	/**
	 *  Displays the winning text setting field
	 *
	 * @since 1.0
	 */
	public function display_text_winning_field($args) {
		?>
			<textarea name="ccc_text_winning" id="ccc_text_winning" class="large-text" rows="10"><?php echo esc_html(get_option("ccc_text_winning")); ?></textarea>
		<?
	}

	/**
	 *  Displays the losing text setting field
	 *
	 * @since 1.0
	 */
	public function display_text_losing_field($args) {
		?>
			<textarea name="ccc_text_losing" id="ccc_text_losing" class="large-text" rows="10"><?php echo esc_html(get_option("ccc_text_losing")); ?></textarea>
		<?
	}

	public function display_contest_not_running_field($args) {
		?>
			<textarea name="ccc_contest_not_running" id="ccc_contest_not_running" class="large-text" rows="10"><?php echo esc_html(get_option("ccc_contest_not_running")); ?></textarea>
		<?	
	}

	/**
	 * Exports all of the winners 
	 * @return A CSV with the winner information
	 * @since 1.0.0
	 */
	public function export_winners() {
		if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT'])) {
			// IE Bug in download name workaround
			ini_set( 'zlib.output_compression','Off' );
		}
		header('Content-Description: Contest Code Checker Export');
		header("Content-Type: application/vnd.ms-excel", true);
		header('Content-Disposition: attachment; filename="ccc_winners.csv"');
		$csv = "\"Name\",\"Email\",\"Contest Code\",\"Prize\"\r\n";

		$args = array(
				'post_type'	=> "ccc_contestants",
				"meta_key"	=> "ccc_contest_code_id",
				"meta_value" => "0",
				"meta_compare" => ">",
			);
		$contestants = new WP_Query($args);
		if ( $contestants->have_posts() ) {
			while ( $contestants->have_posts() ) {
				$contestants->the_post();
				$id = $contestants->post->ID;
				$ccId = get_post_meta($id, "ccc_contest_code_id", true);
				$cc = new CCC_Contest_Codes($ccId);
				if(!empty($cc->get_prize())) {
					$csv .= "\"".str_replace("\"", "\"\"",get_the_title())."\",";	
					$csv .= "\"".get_post_meta($id, "ccc_email", true)."\",";
					$csv .= "\"".$cc->get_code()."\",\"".str_replace("\"", "\"\"",$cc->get_prize())."\"\r\n";
				}
			}
		}

		echo $csv;
		wp_reset_postdata();
		exit;
	}

	/**
	 * Exports all of the contestants
	 * @return A CSV with all of the contestants information
	 * @since 1.0.0
	 */
	public function export_all() {
		if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT'])) {
			// IE Bug in download name workaround
			ini_set( 'zlib.output_compression','Off' );
		}
		header('Content-Description: Contest Code Checker Export');
		header("Content-Type: application/vnd.ms-excel", true);
		header('Content-Disposition: attachment; filename="ccc_contestants.csv"');
		$csv = "\"Name\",\"Email\",\"Contest Code\",\"Prize\",\"Invalid Contest Code\"\r\n";

		$args = array(
				'post_type'	=> "ccc_contestants"
			);
		$contestants = new WP_Query($args);
		if ( $contestants->have_posts() ) {
			while ( $contestants->have_posts() ) {
				$contestants->the_post();
				$id = $contestants->post->ID;
				$ccId = get_post_meta($id, "ccc_contest_code_id", true);
				$csv .= "\"".str_replace("\"", "\"\"",get_the_title())."\",";	
				$csv .= "\"".get_post_meta($id, "ccc_email", true)."\",";
				if($ccId > 0) {
					$cc = new CCC_Contest_Codes($ccId);
					$csv .= "\"".$cc->get_code()."\",\"".str_replace("\"", "\"\"",$cc->get_prize())."\",";	
				} else {
					$csv .= "\"\",\"\",";
				}
				
				$csv .= "\"".str_replace("\"", "\"\"",get_post_meta($id, "ccc_invalid_contest_code", true))."\"\r\n";
			}
		}

		echo $csv;
		wp_reset_postdata();
		exit;
	}

}
