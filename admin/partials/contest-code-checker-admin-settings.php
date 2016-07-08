<?php
/**
 * Provide a admin area settings view for the plugin
 *
 *
 * @link       http://www.swimordiesoftware.com
 * @since      1.0.0
 *
 * @package    Contest_Code_Checker
 * @subpackage Contest_Code_Checker/admin/partials
 */

/**
 * Displays the admin settings area
 */
class CCC_Contest_Code_Checker_Admin_Settings {

	public function display_settings() {
	?>
		<div class="wrap">
			<?php if( isset($_GET['settings-updated']) ) { ?>
				<div id="message" class="updated">
        	<p><strong><?php _e('Settings saved.') ?></strong></p>
    		</div>
			<?php } ?>
			<h1><?php _e("Contest Code Checker General Settings", "contest-code"); ?></h1>
			<form method="post" action="options.php">
				<?php
					settings_fields("contest_code_checker_options");
					do_settings_sections("ccc_options");
					submit_button();
				?>
			</form>
		</div>
	<?php
	}
}
