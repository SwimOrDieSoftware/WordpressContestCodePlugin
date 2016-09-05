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

		$nameLabel = apply_filters("ccc_form_name_label_text", __("Your Name", "contest-code"));
		$emailLabel = apply_filters("ccc_form_email_label_text", __("Your Email", "contest-code"));
		$prizeCodeLabel = apply_filters("ccc_form_prize_code_label_text", __("Prize Code", "contest-code"));
		$submitPrizeLabel = apply_filters("ccc_form_submit_prize_label_text", __("Submit Prize", "contest-code"));
	?>
		<div id="contest_code_checker_container">
			<form name="contest_code_checker" id="contest_code_checker" method="post">
				<input type="hidden" name="step" value="check_code" />
				<?php wp_nonce_field("contest_code_frontend_form"); ?>

				<p class="ccc_form_element">
					<label for="contestants_name"><?php echo esc_html($nameLabel); ?></label>
					<input type="text" name="contestants_name" id="contestants_name" required />
				</p>

				<p class="ccc_form_element">
					<label for="contestants_email"><?php echo esc_html($emailLabel); ?></label>
					<input type="text" name="contestants_email" id="contestants_email" />
				</p>

				<p class="ccc_form_element">
					<label for="contestants_code"><?php echo esc_html($prizeCodeLabel); ?></label>
					<input type="text" name="contestants_code" id="contestants_code" required />
				</p>

				<p class="ccc_form_submit">
					<input type="submit" id="contest_code_checker_submit" value="<?php echo esc_html($submitPrizeLabel); ?>" />
				</p>
			</form>
		</div>
		<?php if( get_option( "ccc_display_popover" ) === 'Y' ) { ?>
			<div id="ccc-dialog" title="<?php _e('Contest Results', 'contest-code'); ?>">
				<div id="ccc-dialog-message">
					
				</div>
			</div>
		<?php } ?>

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

	public function get_losing_message() {
		$message = "<p>".__("The code you entered was not a winner.", "contest-code")."</p>";

		if(get_option("ccc_text_losing") != "") {
			$message = get_option("ccc_text_losing");
		}

		return $message;
	}

	public function losing_code_entered() {
		ob_start();
	?>
		<div id="contest_code_checker_container">
			<?php echo $this->get_losing_message(); ?>
		</div>
	<?php
		return ob_get_clean();
	}

	public function get_winning_message($code) {
		$message = "<p>" . __("You have entered a winner!", "contest-code") . "</p>";

		if(get_option("ccc_text_winning") != "") {
			$message = get_option("ccc_text_winning");
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

		$message .= $additional_prize_info;

		return $message;
	}

	public function winning_code_entered($code) {
		ob_start();
	?>
		<div id="contest_code_checker_container">
			<?php
				echo $this->get_winning_message($code);
			?>
		</div>
	<?php
		return ob_get_clean();
	}
}
