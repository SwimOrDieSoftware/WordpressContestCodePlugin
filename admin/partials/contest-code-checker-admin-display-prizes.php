<?php

/**
 * Provide a admin area view for prizes
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Contest_Code_Checker
 * @subpackage Contest_Code_Checker/admin/partials
 */

/**
 * Displays the admin areas for managing prizes
 */
class CCC_Contest_Code_Checker_Admin_Display_Prizes {

  public function prize_listings() {
    $contestants_table = new CCC_Prizes_Table();
    $contestants_table->prepare_items();
  ?>
    <div class="wrap">
      <h1><?php _e("Generic Prizes", "contest-code"); ?><a href="<?php echo esc_url( add_query_arg( array( 'ccc-action' => 'add-prize' ) ) ); ?>" class="add-new-h2"><?php _e( 'Add New', 'contest-codes' ); ?></a></h1>
      <form id="ccc-prizes-filter" method="get" action="<?php echo admin_url( 'admin.php?page=contest-code-prizes' ); ?>">
        <div></div>
        <input type="hidden" name="page" value="contest-code-prizes" />
        <input type="hidden" name="ccc-action" value="bulk" />
        <?php $contestants_table->views() ?>

        <?php $contestants_table->display() ?>
      </form>
    </div>
  <?php
  }

  public function prize_form( $prize ) {
	  global $wpdb;
  ?>
    <div class="wrap">
      <h2><?php if($prize->get_ID() > 0) :
          _e("Edit Generic Prize", "contest-code");
        else:
          _e("Add Generic Prize", "contest-code");
        endif; ?> - <a href="<?php echo admin_url( 'admin.php?page=contest-code-prizes' ); ?>" class="button-secondary"><?php _e( 'Go Back', 'rsvp-pro-plugin' ); ?></a>
      </h2>
      <form id="generic-prizes-form" action="<?php echo admin_url('admin.php?page=contest-code-prizes'); ?>" method="post">
        <table class="form-table">
          <tbody>
            <tr>
              <th scope="row" valign="top">
                <label for="contest_code"><?php _e( 'Prize Name', 'contest-code' ); ?>:</label>
              </th>
              <td>
                <input name="post_title" id="post_title" type="text" value="<?php echo esc_attr( $prize->get_name() ); ?>" style="width: 300px;" required/>
              </td>
            </tr>
            <tr>
              <th scope="row" valign="top">
                <label for="prizes-select"><?php _e( 'Associated Prizes', 'contest-code' ); ?>:</label>
              </th>
              <td>
				  <?php
				  	$meta_table = $wpdb->prefix . "postmeta";
					$posts_table = $wpdb->prefix . "posts";
					$sql = "SELECT DISTINCT meta_value FROM $meta_table m
						JOIN $posts_table p ON p.ID = m.post_id
					WHERE meta_key = 'ccc_prize' AND p.post_type = 'ccc_codes' AND meta_value != ''
					AND meta_value NOT IN (SELECT meta_value FROM $meta_table WHERE meta_key = 'ccc_prize_codes' AND post_id != %d)";
					$prizes = $wpdb->get_results( $wpdb->prepare( $sql, $prize->get_ID() ) );
				  ?>
				  <select name="prize_codes[]" id="prizes-select" multiple="multiple" size="5">
				 <?php
				 	foreach($prizes as $p) {
					?>
						<option value="<?php esc_html_e($p->meta_value); ?>"
							<?php echo ( in_array($p->meta_value, $prize->get_prizes() ) ) ? "selected=\"selected\"" : ""; ?> ><?php esc_html_e($p->meta_value); ?></option>
					<?php
					}
				 ?>
				  </select>
				  <br />
				  <span class="description">Prize codes will not be shown if they are already associated with another generic prize.</span>
              </td>
            </tr>
			<tr>
				<th scope="row" valign="top">
					<label for="post_content"><?php _e( 'Description', 'contest-code' ); ?>:</label>
				</th>
				<td>
					<?php wp_editor( $prize->get_description(), "post_content" ); ?>
				</td>
			</tr>
          </tbody>
        </table>
        <p class="submit">
          <input type="hidden" name="page" value="contest-code-prizes" />
          <input type="hidden" name="ccc-action" value="save-prize"/>
          <input type="hidden" name="prize" value="<?php echo absint( $prize->get_ID() ); ?>"/>
          <input type="hidden" name="generic-prize-nonce" value="<?php echo wp_create_nonce( 'generic-prize-form'); ?>"/>
          <input type="submit" value="<?php _e( 'Save Generic Prize', 'contest-code' ); ?>" class="button-primary"/>
        </p>
      </form>
    </div>
  <?php
  }
}
