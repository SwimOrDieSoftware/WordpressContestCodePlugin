<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.swimordiesoftware.com
 * @since      1.0.0
 *
 * @package    Contest_Code_Checker
 * @subpackage Contest_Code_Checker/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Contest_Code_Checker
 * @subpackage Contest_Code_Checker/includes
 * @author     Mike de Libero <mikede@mde-dev.com>
 */
class CCC_Contest_Code_Checker {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Contest_Code_Checker_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'contest-code-checker';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_general_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Contest_Code_Checker_Loader. Orchestrates the hooks of the plugin.
	 * - Contest_Code_Checker_i18n. Defines internationalization functionality.
	 * - Contest_Code_Checker_Admin. Defines all hooks for the admin area.
	 * - Contest_Code_Checker_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		$pluginPath = plugin_dir_path( dirname( __FILE__ ) );

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once $pluginPath . 'includes/class-contest-code-checker-loader.php';

		/* Data Classes */
		require_once $pluginPath . 'includes/class-contest-code-checker-contest-codes.php';
		require_once $pluginPath . 'includes/class-contest-code-checker-contestant.php';
		require_once $pluginPath . 'includes/class-contest-code-checker-prizes.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once $pluginPath . 'includes/class-contest-code-checker-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once $pluginPath . 'admin/class-contest-code-checker-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once $pluginPath . 'public/class-contest-code-checker-public.php';

		$this->loader = new CCC_Contest_Code_Checker_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new CCC_Contest_Code_Checker_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Registers all of the general hooks that will be used for both the admin and public
	 * side of the plugin. For example registering custom post types.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function define_general_hooks() {
		$this->loader->add_action("init", $this, "register_post_types");
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new CCC_Contest_Code_Checker_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_menus');
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wireup_settings');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new CCC_Contest_Code_Checker_Public( $this->get_plugin_name(), $this->get_version() );

		add_shortcode("contest_code_checker", array($plugin_public, "handle_shortcode"));

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'wp_ajax_submit_contest_code', $plugin_public, 'ajax_handle_contest_code' );
		$this->loader->add_action( 'wp_ajax_nopriv_submit_contest_code', $plugin_public, 'ajax_handle_contest_code' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Contest_Code_Checker_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Registers the custom post types for the contest code checker.
	 *
	 * @since 1.0.0
	 * @return nothing
	 */
	public function register_post_types() {
		register_post_type("ccc_codes",
			array(
					"public" 				=> false,
					"publicly_queryable"	=> false,
					"show_in_menu"			=> false,
					"label" 				=> __("Contest Codes", "contest-code"),
					"show_in_nav_menus"		=> false,
					"labels"				=> array(
												   	'singular' => __("Contest Code", "contest-code"),
													'plural'   => __("Contest Codes", "contest-code")
												),
					"has_archive"			=> false,
					"hierarchical"			=> false,
					"singular_name"			=> "ccc_code",
					"name"					=> "Contest Codes",
					"supports"				=> array("title"),
				));

		register_post_type("ccc_contestants",
			array(
					"public" 				=> false,
					"show_in_menu"			=> false,
					"label" 				=> __("Contestants", "contest-code"),
					"show_in_nav_menus"		=> false,
					"singular_name"			=> "ccc_contestant",
					"name"					=> "Contestants",
					"has_archive"			=> false,
					"hierarchical"			=> false,
					"name"					=> "Contest Codes",
					"supports"				=> array("title"),
				));

		register_post_type("ccc_prizes",
			array(
					"public" 				=> false,
					"show_in_menu"			=> false,
					"label" 				=> __("Prizes", "contest-code"),
					"show_in_nav_menus"		=> false,
					"singular_name"			=> "ccc_prize",
					"name"					=> "Prizes",
					"has_archive"			=> false,
					"hierarchical"			=> false,
					"supports"				=> array("title", "description"),
				));
	}

}
