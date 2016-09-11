<?php

/**
 * Provide a admin area view for contest codes, forms, listings, etc...
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Contest_Code_Checker
 * @subpackage Contest_Code_Checker/admin/partials
 */

/**
 * Displays the admin contest codes, contestants, etc...
 */
class CCC_Contest_Code_Checker_Admin_Displays {

  /**
   * Displays the list of contest codes for the admin area
   *
   * @since 1.0.0
   */
  public function contest_code_listings() {
      $contest_codes_table = new CCC_Contest_Codes_Table();
      $contest_codes_table->prepare_items();
  ?>
    <div class="wrap">
      <h1><?php _e("Contest Codes", "contest-code"); ?><a href="<?php echo esc_url( add_query_arg( array( 'ccc-action' => 'add-contest-code' ) ) ); ?>" class="add-new-h2"><?php _e( 'Add New', 'contest-codes' ); ?></a></h1>
	  <div id="contestantExportContainer">
		  <a href="<?php echo admin_url('admin.php?page=contest-codes&ccc-action=confirm-delete-contest-codes'); ?>" class="button action"><?php _e('Delete All Contest Codes', 'contest-code'); ?></a>
          <a href="<?php echo admin_url('admin.php?page=contest-codes&ccc-action=contest-code-export'); ?>" class="button action"><?php _e('Export Contest Codes', 'contest-code'); ?></a>
      </div>
      <form id="ccc-contest-codes-filter" method="get" action="<?php echo admin_url( 'admin.php?page=contest-codes' ); ?>">
        <div></div>
        <input type="hidden" name="page" value="contest-codes" />
        <input type="hidden" name="ccc-action" value="bulk" />

        <?php $contest_codes_table->views() ?>
        <?php $contest_codes_table->display() ?>
      </form>
    </div>
  <?php
  }

  public function contestant_listings() {
    $contestants_table = new CCC_Contestants_Table();
    $contestants_table->prepare_items();
  ?>
    <div class="wrap">
      <h1><?php _e("Contestants", "contest-code"); ?></h1>
      <div id="contestantExportContainer">
          <a href="<?php echo admin_url('admin.php?page=contest-code-contestants&ccc-action=contest-code-export-winners'); ?>" class="button action"><?php _e('Export Winners', 'contest-code'); ?></a>
          <a href="<?php echo admin_url('admin.php?page=contest-code-contestants&ccc-action=contest-code-export'); ?>" class="button action"><?php _e('Export All', 'contest-code'); ?></a>
      </div>
      <form id="ccc-contest-codes-filter" method="get" action="<?php echo admin_url( 'admin.php?page=contest-code-contestants' ); ?>">
        <div></div>
        <input type="hidden" name="page" value="contest-code-contestants" />
        <input type="hidden" name="ccc-action" value="bulk" />
        <?php $contestants_table->views() ?>

        <?php $contestants_table->display() ?>
      </form>
    </div>
  <?php
  }

  public function contest_code_form($code) {
  ?>
    <div class="wrap">
      <h2><?php if($code->get_ID() > 0) :
          _e("Edit Contest Code", "contest-code");
        else:
          _e("Add Contest Code", "contest-code");
        endif; ?> - <a href="<?php echo admin_url( 'admin.php?page=contest-codes' ); ?>" class="button-secondary"><?php _e( 'Go Back', 'rsvp-pro-plugin' ); ?></a>
      </h2>
      <form id="contest-code-form" action="<?php echo admin_url('admin.php?page=contest-codes'); ?>" method="post">
        <table class="form-table">
          <tbody>
            <tr>
              <th scope="row" valign="top">
                <label for="contest_code"><?php _e( 'Contest code', 'contest-code' ); ?>:</label>
              </th>
              <td>
                <input name="post_title" id="contest_code" type="text" value="<?php echo esc_attr( $code->get_code() ); ?>" style="width: 300px;" required/>
              </td>
            </tr>
            <tr>
              <th scope="row" valign="top">
                <label for="prize"><?php _e( 'Prize associated with code', 'contest-code' ); ?>:</label>
              </th>
              <td>
                <input name="prize" id="prize" type="text" value="<?php echo esc_attr( $code->get_prize() ); ?>" style="width: 300px;"/>
              </td>
            </tr>
            <tr>
              <th scope="row" valign="top">
                <label for="hasBeenUsed"><?php _e( 'Has this code been used?', 'contest-code' ); ?>:</label>
              </th>
              <td>
                <input type="checkbox" name="hasBeenUsed" id="hasBeenUsed" value="Y"
                  <?php echo ($code->get_has_been_used()) ? "checked=\"checked\"" : "";?> />
              </td>
            </tr>
            <tr>
              <th scope="row" valign="top">
                <label for="prizeInformation"><?php _e("Prize Details", "contest-code"); ?></label>
              </th>
              <td>
                <?php wp_editor( $code->get_prize_information(), "prizeInformation", $settings = array() ); ?>
              </td>
            </tr>
          </tbody>
        </table>
        <p class="submit">
          <input type="hidden" name="page" value="contest-codes" />
          <input type="hidden" name="ccc-action" value="save_contest_codes"/>
          <input type="hidden" name="contest_code" value="<?php echo absint( $code->get_ID() ); ?>"/>
          <input type="hidden" name="contest-code-nonce" value="<?php echo wp_create_nonce( 'contest-code-form'); ?>"/>
          <input type="submit" value="<?php _e( 'Save Contest Code', 'contest-code' ); ?>" class="button-primary"/>
        </p>
      </form>
    </div>
  <?php
  }

	public function contest_code_confirm_delete() {
 	?>
		<div class="wrap">
			<form id="contest-code-delete-all-form" action="<?php echo admin_url('admin.php?page=contest-codes'); ?>" method="post">
				<p>
					<?php _e("Are you sure you want to delete all contest codes?", "contest-code"); ?>
				</p>
				<p class="submit">
		          <input type="hidden" name="page" value="contest-codes" />
		          <input type="hidden" name="ccc-action" value="delete_all_contest_codes" />
		          <input type="hidden" name="contest-code-delete-nonce" value="<?php echo wp_create_nonce("contest-code-delete-form"); ?>" />
		          <input type="submit" value="<?php _e("Delete All Contest Codes", "contest-code"); ?>" class="button-primary" />
		        </p>
			</form>
		</div>
	<?php
	}

  public function contest_code_import_form() {
  ?>
    <div class="wrap">
      <h2><?php _e("Import Contest Codes", "contest-code"); ?></h2>
      <form id="contest-code-import-form" action="<?php echo admin_url('admin.php?page=contest-codes'); ?>" method="post" enctype="multipart/form-data" >
        <p>
          <?php _e("Import contest codes from a Excel or CSV file. The format is as follows:", "contest-code"); ?>
        </p>
        <ul>
          <li><?php _e("Column 1: Contest Code", "contest-code"); ?></li>
          <li><?php _e("Column 2: Prize (if any)", "contest-code"); ?></li>
          <li><?php _e("Column 3: Prize description", "contest-code"); ?></li>
        </ul>
        <p><input type="file" name="importFile" id="importFile" /></p>
        <p class="submit">
          <input type="hidden" name="page" value="contest-codes" />
          <input type="hidden" name="ccc-action" value="handle_import_contest_codes" />
          <input type="hidden" name="contest-code-import-nonce" value="<?php echo wp_create_nonce("contest-code-import-form"); ?>" />
          <input type="submit" value="<?php _e("Import Contest Codes", "contest-code"); ?>" class="button-primary" />
        </p>
      </form>
    </div>
  <?php
  }
}
