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

		if( isset( $_GET['page'] ) &&
		   (strToLower( $_GET['page'] ) == 'contest-code-contestants') &&
		   isset( $_GET['ccc-action'] ) &&
		   (strtolower( $_GET['ccc-action'] ) == "contest-code-export-winners")) {
		   	add_action('init', array($this, "export_winners"));
		}


		if( isset( $_GET['page'] ) &&
		   ( strToLower( $_GET['page'] ) == 'contest-code-contestants' ) &&
		   isset( $_GET['ccc-action'] ) &&
		   ( strtolower( $_GET['ccc-action'] ) == "contest-code-export" ) ) {
		   	add_action('init', array($this, "export_all"));
		}

		if( isset( $_GET['page'] ) &&
		   ( strToLower( $_GET['page'] ) == 'contest-codes' ) &&
		   isset( $_GET['ccc-action'] ) &&
		   ( strtolower( $_GET['ccc-action'] ) == "contest-code-export" ) ) {
		   	add_action('init', array($this, "export_contest_codes"));
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

		wp_enqueue_style( 'jquery_multi_select_css',
								plugin_dir_url( __FILE__ ) . 'js/multi-select/css/multi-select.css',
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

		wp_enqueue_script( "contest-code-multiselect",
											 plugin_dir_url( __FILE__ ) . 'js/multi-select/jquery.multi-select.js',
											 array( 'jquery' ),
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

		add_submenu_page( "contest-code-checker",
			__("Generic Prizes", "contest-code"),
			__("Generic Prizes", "contest-code"),
			$this->get_required_capability(),
			"contest-code-prizes",
			array($this, "show_prizes_page") );

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

		add_settings_field("ccc_display_popover",
											__("Display pop-over with prize information", "contest-code"),
											array($this, "display_popover_field"),
											"ccc_options",
											"contest_code_checker_options");

		add_settings_field("ccc_pop_up_width",
										   __("Pop-over Dimensions", "contest-code"),
										   array($this, "display_popover_dimensions_field"),
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

		 add_settings_field("ccc_text_invalid",
 											 __("Text for Invalid Contest Code", "contest-code"),
 											 array($this, "display_text_invalid_code_field"),
 											 "ccc_options",
 											 "contest_code_checker_options");

		 add_settings_field("ccc_text_already_used",
 											 __("Text for Already Used Contest Code", "contest-code"),
 											 array($this, "display_text_already_used_code_field"),
 											 "ccc_options",
 											 "contest_code_checker_options");

		add_settings_field("ccc_contest_not_running",
											__("Text for when the contest is not currently running", "contest-code"),
											array($this, "display_contest_not_running_field"),
											"ccc_options",
											"contest_code_checker_options");

		add_settings_field("ccc_hide_email",
											__("Hide email address", "contest-code"),
											array($this, "display_hide_email_field"),
											"ccc_options",
											"contest_code_checker_options");

		add_settings_field("ccc_hide_first_name",
											__("Hide first name", "contest-code"),
											array($this, "display_hide_first_name_field"),
											"ccc_options",
											"contest_code_checker_options");

		add_settings_field("ccc_hide_last_name",
											__("Hide last name", "contest-code"),
											array($this, "display_hide_last_name_field"),
											"ccc_options",
											"contest_code_checker_options");

		// add_settings_field("ccc_enable_captcha",
		// 									__("Enable CAPTCHA", "contest-code"),
		// 									array($this, "display_enable_captcha_field"),
		// 									"ccc_options",
		// 									"contest_code_checker_options");

		// add_settings_field("ccc_recaptcha_secret_api",
		// 									__("reCAPTCHA Secret Key", "contest-code"),
		// 									array($this, "display_recaptcha_secret_key"),
		// 									"ccc_options",
		// 									"contest_code_checker_options");

		// add_settings_field("ccc_recaptcha_site_key",
		// 									__("reCAPTCHA Site Key", "contest-code"),
		// 									array($this, "display_recaptcha_site_key"),
		// 									"ccc_options",
		// 									"contest_code_checker_options");

		register_setting("contest_code_checker_options", "ccc_start_date");
		register_setting("contest_code_checker_options", "ccc_end_date");
		register_setting("contest_code_checker_options", "ccc_display_popover");
		register_setting("contest_code_checker_options", "ccc_pop_up_width");
		register_setting("contest_code_checker_options", "ccc_pop_up_height");
		register_setting("contest_code_checker_options", "ccc_email_winner");
		register_setting("contest_code_checker_options", "ccc_email_winner_body");
		register_setting("contest_code_checker_options", "ccc_email_winner_subject");
		register_setting("contest_code_checker_options", "ccc_text_winning");
		register_setting("contest_code_checker_options", "ccc_text_losing");
		register_setting("contest_code_checker_options", "ccc_text_invalid");
		register_setting("contest_code_checker_options", "ccc_text_already_used");
		register_setting("contest_code_checker_options", "ccc_contest_not_running");
		register_setting("contest_code_checker_options", "ccc_hide_email");
		register_setting("contest_code_checker_options", "ccc_hide_first_name");
		register_setting("contest_code_checker_options", "ccc_hide_last_name");
		register_setting("contest_code_checker_options", "ccc_enable_captcha");
		register_setting("contest_code_checker_options", "ccc_recaptcha_secret_api");
		register_setting("contest_code_checker_options", "ccc_recaptcha_site_key");
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

	public function show_prizes_page() {
		$prizes = new CCC_Contest_Code_Checker_Admin_Prizes();
		$prizes->display_page();
	}

	/**
	 * [load_dependencies description]
	 * @return [type] [description]
	 */
	private function load_dependencies() {
		$base_dir = plugin_dir_path( dirname( __FILE__ ) );

		require_once $base_dir . 'admin/partials/contest-code-checker-admin-settings.php';
		require_once $base_dir . 'admin/partials/contest-code-checker-admin-display.php';
		require_once $base_dir . 'admin/partials/contest-code-checker-admin-display-prizes.php';
		require_once $base_dir . 'admin/contest_codes/class-contest-codes-table.php';
		require_once $base_dir . 'admin/contestants/class-contestants-table.php';
		require_once $base_dir . 'admin/prizes/class-prizes-table.php';
		require_once $base_dir . 'admin/class-contest-code-checker-admin-contest-codes.php';
		require_once $base_dir . 'admin/class-contest-code-checker-admin-contestants.php';
		require_once $base_dir . 'admin/class-contest-code-checker-admin-prizes.php';

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
	public function display_popover_field($args) {
		?>
			<input type="checkbox" name="ccc_display_popover" id="ccc_display_popover" value="Y" <?php echo (get_option("ccc_display_popover") == "Y") ? "checked=\"checked\"" : ""; ?>/>
		<?php
	}

	/**
	 *  Displays the hide email address setting field
	 *
	 * @since 1.1.5
	 */
	public function display_hide_email_field($args) {
		?>
			<input type="checkbox" name="ccc_hide_email" id="ccc_hide_email" value="Y" <?php echo (get_option("ccc_hide_email") == "Y") ? "checked=\"checked\"" : ""; ?>/>
		<?php
	}

	/**
	 *  Displays the hide first name setting field
	 *
	 * @since 1.1.5
	 */
	public function display_hide_first_name_field($args) {
		?>
			<input type="checkbox" name="ccc_hide_first_name" id="ccc_hide_first_name" value="Y" <?php echo (get_option("ccc_hide_first_name") == "Y") ? "checked=\"checked\"" : ""; ?>/>
		<?php
	}

	/**
	 *  Displays the hide last name setting field
	 *
	 * @since 1.1.5
	 */
	public function display_hide_last_name_field($args) {
		?>
			<input type="checkbox" name="ccc_hide_last_name" id="ccc_hide_last_name" value="Y" <?php echo (get_option("ccc_hide_last_name") == "Y") ? "checked=\"checked\"" : ""; ?>/>
		<?php
	}

	/**
	 *  Displays the enable CAPTCHA setting field
	 *
	 * @since 1.1.5
	 */
	public function display_enable_captcha_field($args) {
		?>
			<input type="checkbox" name="ccc_enable_captcha" id="ccc_enable_captcha" value="Y" <?php echo (get_option("ccc_enable_captcha") == "Y") ? "checked=\"checked\"" : ""; ?>/>
		<?php
	}	

	/**
	 *  Displays the reCAPTCHA secret key setting field
	 *
	 * @since 1.1.5
	 */
	public function display_recaptcha_secret_key($args) {
		?>
			<input type="text" name="ccc_recaptcha_secret_api" id="ccc_recaptcha_secret_api" value="<?php echo esc_attr(get_option("ccc_recaptcha_secret_api")); ?>" class="large-text" />
		<?php
	}

	/**
	 *  Displays the reCAPTCHA site key setting field
	 *
	 * @since 1.1.5
	 */
	public function display_recaptcha_site_key($args) {
		?>
			<input type="text" name="ccc_recaptcha_site_key" id="ccc_recaptcha_site_key" value="<?php echo esc_attr(get_option("ccc_recaptcha_site_key")); ?>" class="large-text" />
		<?php
	}

	/**
	 *  Displays the pop-over dimensions inputs
	 *
	 * @since 1.0.3
	 */
	public function display_popover_dimensions_field($args) {
	?>
		<label>Width: <input type="text" name="ccc_pop_up_width" id="ccc_pop_up_width" value="<?php echo esc_attr(get_option("ccc_pop_up_width")); ?>" size="5" />pixels</label> -
		<label>Height: <input type="text" name="ccc_pop_up_height" id="ccc_pop_up_height" value="<?php echo esc_attr(get_option("ccc_pop_up_height")); ?>" size="5" />pixels</label>
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
		<?php
	}

	/**
	 *  Displays the winning text setting field
	 *
	 * @since 1.0
	 */
	public function display_text_winning_field($args) {
		?>
			<textarea name="ccc_text_winning" id="ccc_text_winning" class="large-text" rows="10"><?php echo esc_html(get_option("ccc_text_winning")); ?></textarea>
		<?php
	}

	/**
	 *  Displays the losing text setting field
	 *
	 * @since 1.0
	 */
	public function display_text_losing_field($args) {
		?>
			<textarea name="ccc_text_losing" id="ccc_text_losing" class="large-text" rows="10"><?php echo esc_html(get_option("ccc_text_losing")); ?></textarea>
		<?php
	}

	/**
	 *  Displays the invalid contest code text setting field
	 *
	 * @since 1.0.3
	 */
	public function display_text_invalid_code_field($args) {
		?>
			<textarea name="ccc_text_invalid" id="ccc_text_invalid" class="large-text" rows="10"><?php echo esc_html(get_option("ccc_text_invalid")); ?></textarea>
		<?php
	}

	/**
	 *  Displays the already used contest code text setting field
	 *
	 * @since 1.0.3
	 */
	public function display_text_already_used_code_field($args) {
		?>
			<textarea name="ccc_text_already_used" id="ccc_text_already_used" class="large-text" rows="10"><?php echo esc_html(get_option("ccc_text_already_used")); ?></textarea>
		<?php
	}

	public function display_contest_not_running_field($args) {
		?>
			<textarea name="ccc_contest_not_running" id="ccc_contest_not_running" class="large-text" rows="10"><?php echo esc_html(get_option("ccc_contest_not_running")); ?></textarea>
		<?php
	}

	/**
	 * Exports all of the winners
	 * @return A CSV with the winner information
	 * @since 1.0.0
	 */
	public function export_winners() {
		set_time_limit(0); // Set the time limit to forever to handle large number of contestants from being exported
		
		$hide_first_name = ( get_option("ccc_hide_first_name") === "Y" ) ? true : false;
		$hide_last_name = ( get_option("ccc_hide_last_name") === "Y" ) ? true : false;
		$hide_email = ( get_option("ccc_hide_email") === "Y" ) ? true : false;

		if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT'])) {
			// IE Bug in download name workaround
			ini_set( 'zlib.output_compression','Off' );
		}
		header('Content-Description: Contest Code Checker Export');
		header("Content-Type: application/vnd.ms-excel", true);
		header('Content-Disposition: attachment; filename="ccc_winners.csv"');
		$csv = "";

		if( ! $hide_first_name ) {
			$csv .= "\"First Name\",";
		}

		if( ! $hide_last_name ) {
			$csv .= "\"Last Name\",";
		}

		if( ! $hide_email ) {
			$csv .= "\"Email\",";
		}

		$csv .= "\"Contest Code\",\"Prize\",\"Submission Date\"\r\n";

		$args = array(
				'post_type'	=> "ccc_contestants",
				"meta_key"	=> "ccc_contest_code_id",
				"meta_value" => "0",
				"meta_compare" => ">",
				'posts_per_page' => -1,
			);
		$contestants = new WP_Query($args);
		if ( $contestants->have_posts() ) {
			while ( $contestants->have_posts() ) {
				$contestants->the_post();
				$id = $contestants->post->ID;
				$ccId = get_post_meta($id, "ccc_contest_code_id", true);
				$cc = new CCC_Contest_Codes($ccId);
				if( $cc->get_prize() != '' ) {
					if( ! $hide_first_name ) {
						$csv .= "\"".str_replace("\"", "\"\"",get_post_meta($id, "ccc_contestant_first_name", true))."\",";
					}

					if ( ! $hide_last_name ) {
						$csv .= "\"".str_replace("\"", "\"\"",get_post_meta($id, "ccc_contestant_last_name", true))."\",";	
					}
					
					if ( ! $hide_email ) {
						$csv .= "\"".get_post_meta($id, "ccc_email", true)."\",";	
					}
					
					$csv .= "\"".$cc->get_code()."\",\"".str_replace("\"", "\"\"",$cc->get_prize())."\",\"" . date_i18n(
                get_option('date_format') . ' ' . get_option('time_format'),
                strtotime($contestants->post->post_date)
            ) . "\"\r\n";
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
		global $wpdb;
		set_time_limit(0); // Set the time limit to forever to handle large number of contestants from being exported

		$hide_first_name = ( get_option("ccc_hide_first_name") === "Y" ) ? true : false;
		$hide_last_name = ( get_option("ccc_hide_last_name") === "Y" ) ? true : false;
		$hide_email = ( get_option("ccc_hide_email") === "Y" ) ? true : false;

		if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT'])) {
			// IE Bug in download name workaround
			ini_set( 'zlib.output_compression','Off' );
		}
		header('Content-Description: Contest Code Checker Export');
		header("Content-Type: application/vnd.ms-excel", true);
		header('Content-Disposition: attachment; filename="ccc_contestants.csv"');
		$csv = "";

		if( ! $hide_first_name ) {
			$csv .= "\"First Name\",";
		}

		if( ! $hide_last_name ) {
			$csv .= "\"Last Name\",";
		}

		if( ! $hide_email ) {
			$csv .= "\"Email\",";
		}

		$csv .= "\"Contest Code\",\"Prize\",\"Invalid Contest Code\",\"Submission Date\"\r\n";

		$contestants = $wpdb->get_results("SELECT ID, post_date FROM $wpdb->posts WHERE post_type = 'ccc_contestants'");
		$date_format = get_option('date_format') . ' ' . get_option('time_format');
		foreach($contestants as $c) {
			$id = $c->ID;
			$custom_fields = get_post_custom( $id );

			$ccId = isset( $custom_fields['ccc_contest_code_id'] ) ? $custom_fields['ccc_contest_code_id'] : 0;
			if( ! $hide_first_name ) {
				$csv .= "\"".str_replace("\"", "\"\"", $custom_fields["ccc_contestant_first_name"][0])."\",";
			}

			if ( ! $hide_last_name ) {
				$csv .= "\"".str_replace("\"", "\"\"", $custom_fields["ccc_contestant_last_name"][0])."\",";	
			}
			
			if ( ! $hide_email ) {
				$csv .= "\"". $custom_fields["ccc_email"][0]."\",";	
			}

			if($ccId > 0) {
				$cc = new CCC_Contest_Codes($ccId);
				$csv .= "\"".$cc->get_code()."\",\"".str_replace("\"", "\"\"",$cc->get_prize())."\",";
			} else {
				$csv .= "\"\",\"\",";
			}

			$csv .= "\"".str_replace("\"", "\"\"", isset( $custom_fields["ccc_invalid_contest_code"] ) ? $custom_fields["ccc_invalid_contest_code"][0] : '')."\"";
			$csv .= ",\"" . date_i18n(
                $date_format,
                strtotime($c->post_date)
            ) ."\"";
			$csv .= "\r\n";
		}

		echo $csv;
		wp_reset_postdata();
		exit;
	}

	/**
	 * Exports all of the contest codes
	 * @return A CSV with the contest code information
	 * @since 1.0.0
	 */
	public function export_contest_codes() {
		global $wpdb;
		set_time_limit(0); // Set the time limit to forever to handle large number of contest codes from being exported
		ob_start();
		if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT'])) {
			// IE Bug in download name workaround
			ini_set( 'zlib.output_compression','Off' );
		}
		header('Content-Description: Contest Code Checker Export');
		header("Content-Type: application/vnd.ms-excel", true);
		header('Content-Disposition: attachment; filename="ccc_contest_codes.csv"');
		echo "\"Contest Code\",\"Prize Associated with Code\",\"Has code been used?\",\"Prize Details\"\r\n";

		$codes = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'ccc_codes'");
		foreach($codes as $c) {
			$id = $c->ID;
			$cc = new CCC_Contest_Codes($id);
			$csv = "\"" . str_replace( "\"", "\"\"", $cc->get_code() ) . "\",";
			$csv .= "\"" . str_replace( "\"", "\"\"", $cc->get_prize() ) . "\",";
			$csv .= "\"" . ( $cc->get_has_been_used() ? "Yes" : "No" ) . "\",";
			$csv .= "\"" . str_replace( "\"", "\"\"", $cc->get_prize_information() ) . "\"";
			$csv .= "\r\n";
			echo $csv;
			flush();
			ob_flush();
			unset($cc);
			unset($csv);
			gc_collect_cycles();
		}
		ob_end_flush();
		exit;
	}

}
