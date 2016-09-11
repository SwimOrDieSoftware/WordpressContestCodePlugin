<?php
/**
 * Contest codes table class
 *
 * @package     Contest_Code_Checker
 * @subpackage  Contest_Code_Checker/admin/contest_codes
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * CCC_Contest_Codes_Table
 *
 * Renders the Contest Codes table
 *
 * @since 1.0
 */
class CCC_Contest_Codes_Table extends WP_List_Table {

  	/**
	 * Number of items per page
	 *
	 * @var int
	 * @since 1.0
	 */
	public $per_page = 10;

	/**
	 * Number of contest codes found
	 *
	 * @var int
	 * @since 1.0
	 */
	public $count = 0;

	/**
	 * Total contest codes
	 *
	 * @var int
	 * @since 1.0
	 */
	public $total = 0;

	/**
	 * Get things started
	 *
	 * @since 1.0
	 * @see WP_List_Table::__construct()
	 */
	public function __construct() {
		global $status, $page;

		// Set parent defaults
		parent::__construct( array(
			'singular' => __( 'Contest Code', 'contest-code' ),
			'plural'   => __( 'Contest Codes', 'contest-code' ),
			'ajax'     => false,
		) );

	}

  /**
	 * Show the search field
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @param string $text Label for the search box
	 * @param string $input_id ID of the search box
	 *
	 * @return void
	 */
	public function search_box( $text, $input_id ) {
		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) )
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		if ( ! empty( $_REQUEST['order'] ) )
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		?>
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
			<input type="search" id="<?php echo $input_id ?>" name="s" value="" />
			<?php submit_button( $text, 'button', false, false, array('ID' => 'search-submit') ); ?>
		</p>
		<?php
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @since 1.0
	 * @access protected
	 *
	 * @return string Name of the primary column.
	 */
	protected function get_primary_column_name() {
		return 'title';
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param array $item Contains all the data of the customers
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			default:
				$value = isset( $item[ $column_name ] ) ? esc_html($item[ $column_name ]) : null;
				break;
		}
		return $value;
	}

	/**
	 * Returns how the data should be rendered for the contest code
	 * @param  object $code The data for the current contest code
	 * @return string       The title for the column and how it should be rendered with actions
	 */
	public function column_title( $code ) {
		$contest_code = get_post( $code['ID'] );
		$row_actions  = array();

		$row_actions['edit'] = '<a href="' . add_query_arg( array( 'ccc-action' => 'edit-contest-code', 'contest_code' => $contest_code->ID ) ) . '">' . __( 'Edit', 'contest-code' ) . '</a>';

		$row_actions['delete'] = '<a href="' . esc_url( wp_nonce_url( add_query_arg( array( 'ccc-action' => 'delete-contest-code', 'contest_code' => $contest_code->ID ) ), 'ccc_contest_code_nonce' ) ) . '">' . __( 'Delete', 'contest-code' ) . '</a>';

		return esc_html(stripslashes( $code['title'] )) . $this->row_actions( $row_actions );
	}

	/**
	 * Render the checkbox column
	 *
	 * @access public
	 * @since 1.0
	 * @param array $item Contains all the data for the checkbox column
	 * @return string Displays a checkbox
	 */
	function column_cb( $code ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ 'contest_code',
			/*$2%s*/ $code['ID']
		);
	}

	/**
	 * Retrieve the table columns
	 *
	 * @access public
	 * @since 1.0
	 * @return array $columns Array of all the list table columns
	 */
	public function get_columns() {
		$columns = array(
			'cb'         	=> '<input type="checkbox" />',
			'title'         => __( 'Contest Code', 'contest-code' ),
      		'prize'         => __( 'Prize', 'contest-code' ),
			'been_used'     => __( 'Has Been Used', 'contest-code' ),
		);

		return $columns;

	}

	/**
	 * Get the sortable columns
	 *
	 * @access public
	 * @since 1.0
	 * @return array Array of all the sortable columns
	 */
	public function get_sortable_columns() {
		return array(
			'title'        => array( 'title', true ),
			'prize'        => array( 'prize', true ),
			'been_used'    => array( 'been_used', false ),
		);
	}

	/**
	 * Outputs the bulk actions for the contest codes
	 *
	 * @access public
	 * @since 1.0
	 * @return array $actions Array of the bulk actions
	 */
	public function get_bulk_actions( $which = '' ) {
		$actions = array(
			'delete'     => __( 'Delete', 'contest-code' ),
		);

		return $actions;
	}

	/**
	 * Retrieves the current page number
	 *
	 * @access public
	 * @since 1.0
	 * @return int Current page number
	 */
	public function get_page_number() {
		return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
	}

	/**
	 * Retrieves the search query string
	 *
	 * @access public
	 * @since 1.7
	 * @return mixed string If search is present, false otherwise
	 */
	public function get_search() {
    	// TODO: IMplement
		return ! empty( $_GET['s'] ) ? urldecode( trim( $_GET['s'] ) ) : false;
	}

	/**
	 * Build the contest code data
	 *
	 * @access public
	 * @since 1.0
	 * @global object $wpdb Used to query the database using the WordPress
	 *   Database API
	 * @return array $reports_data All the data for customer reports
	 */
	public function contest_codes_data() {
		global $wpdb;

		$data    = array();
		$paged   = $this->get_page_number();
		$offset  = $this->per_page * ( $paged - 1 );
		$search  = $this->get_search();
		$order   = isset( $_GET['order'] )   ? sanitize_text_field( $_GET['order'] )   : 'DESC';
		$orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'id';

		$args    = array(
			'number'  	=> $this->per_page,
			'paged'		=> $paged,
			'offset'  	=> $offset,
			'order'   	=> $order,
			'orderby' 	=> $orderby,
			'post_type'	=> "ccc_codes",
		);

		$codes = new WP_Query($args);

		while ( $codes->have_posts() ) {
			$codes->the_post();

			$hasBeenUsed = get_post_meta($codes->post->ID, "ccc_has_been_used", true);
			if($hasBeenUsed) {
				$hasBeenUsed = __("Yes", "contest-code");
			} else {
				$hasBeenUsed = __("No", "contest-code");
			}

			$data[] = array(
				"ID"		=> $codes->post->ID,
				"title" 	=> get_the_title($codes->post->ID),
				"been_used" => $hasBeenUsed,
				"prize"		=> get_post_meta($codes->post->ID, "ccc_prize", true),
			);
		}

		wp_reset_postdata();

		return $data;
	}

	/**
	 * Setup the final data for the table
	 *
	 * @access public
	 * @since 1.0
	 * @uses CCC_Contest_Codes_Table::get_columns()
	 * @uses WP_List_Table::get_sortable_columns()
	 * @uses CCC_Contest_Codes_Table::get_page_number()
	 * @return void
	 */
	public function prepare_items() {

		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->items = $this->contest_codes_data();

		$count = wp_count_posts('ccc_codes');
		$total = 0;
		foreach($count as $val) {
			$total += $val;
		}
		$this->total = $total;

		$this->set_pagination_args( array(
			'total_items' => $this->total,
			'per_page'    => $this->per_page,
			'total_pages' => ceil( $this->total / $this->per_page ),
		) );
	}
}
