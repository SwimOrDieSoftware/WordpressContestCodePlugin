<?php
/**
 * @link              http://www.swimordiesoftware.com
 * @since             1.0.0
 * @package           Contest_Code_Checker
 * @version 1.1.4
 *
 * @wordpress-plugin
 * Plugin Name:       Contest Code Checker
 * Plugin URI:        https://wordpress.org/plugins/contest-code-checker/
 * Description:       Maintain a list of contest codes and have users check to see if they have won anything
 * Version:           1.1.4
 * Author:            Swim or Die Software
 * Author URI:        http://www.swimordiesoftware.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       contest-code
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if (!function_exists('boolval')) {
        function boolval($val) {
                return (bool) $val;
        }
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_contest_code_checker() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-contest-code-checker-activator.php';
	CCC_Contest_Code_Checker_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_contest_code_checker() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-contest-code-checker-deactivator.php';
	CCC_Contest_Code_Checker_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_contest_code_checker' );
register_deactivation_hook( __FILE__, 'deactivate_contest_code_checker' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-contest-code-checker.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_contest_code_checker() {

	$plugin = new CCC_Contest_Code_Checker();
	$plugin->run();

}
run_contest_code_checker();
