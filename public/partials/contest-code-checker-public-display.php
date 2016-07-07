<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Contest_Code_Checker
 * @subpackage Contest_Code_Checker/public/partials
 */

class CCC_Contest_Code_Checker_Public_Displays {


	/**
	 * Returns the initial contest form that is displayed on the front-end
	 * 
	 * @return string The HTML for the form
	 */
	public function contest_form() {
		$output = "";
		ob_start();
	?>
		<div id="contest_code_checker_container">
			<form name="contest_code_checker" id="contest_code_checker" method="post">
				<input type="hidden" name="step" value="check_code" />
				<?php wp_nonce_field("contest_code_frontend_form"); ?>

				<p class="ccc_form_element">
					<label for="contestants_name"><?php _e("Your Name", "contest-code"); ?></label>
					<input type="text" name="contestants_name" id="contestants_name" required />
				</p>

				<p class="ccc_form_element">
					<label for="contestants_email"><?php _e("Your Email", "contest-code"); ?></label>
					<input type="text" name="contestants_email" id="contestants_email" />
				</p>

				<p class="ccc_form_element">
					<label for="contestants_code"><?php _e("Prize Code", "contest-code"); ?></label>
					<input type="text" name="contestants_code" id="contestants_code" required />
				</p>

				<p class="ccc_form_submit">
					<input type="submit" value="<?php _e("Submit Prize", "contest-code"); ?>" />
				</p>
			</form>
		</div>
	<?php
		$output = ob_get_clean();
		return $output;
	}

	public function contest_has_not_started() {
		ob_start();
	?>
		<div id="contest_code_checker_container">
		<?php
			if(get_option("ccc_contest_not_running") != "") {
				echo get_option("ccc_contest_not_running");
			} else {
		?>
			<p><?php _e("This contest is currently not running.", "contest-code"); ?></p>
		<?php
			}
		?>
		</div>
	<?php
		return ob_get_clean();
	}

	public function losing_code_entered() {
		ob_start();
	?>
		<div id="contest_code_checker_container">
		<?php
			if(get_option("ccc_text_losing") != "") {
				echo get_option("ccc_text_losing");
			} else {
		?>
				<p><?php _e("The code you entered was not a winner.", "contest-code"); ?></p>
		<?php
			}
		?>
		</div>
	<?php
		return ob_get_clean();
	}

	public function winning_code_entered($code) {
		ob_start();
	?>
		<div id="contest_code_checker_container">
			<?php
				if(get_option("ccc_text_winning") != "") {
					echo get_option("ccc_text_winning");
				} else {
			?>
					<p><?php _e("You have entered a winner!", "contest-code"); ?></p>
			<?php
				}

				echo "<p>".$code->get_prize_information()."</p>";
			?>
		</div>
	<?php
		return ob_get_clean();
	}
}