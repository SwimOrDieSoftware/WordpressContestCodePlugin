<?php
/**
 * Generic prize table class
 *
 * @package     Contest_Code_Checker
 * @subpackage  Contest_Code_Checker/admin/contestants
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * CCC_Prizes_Table
 *
 * Renders the Generic Prizes table
 *
 * @since 1.0
 */
class CCC_Prizes_Table extends WP_List_Table {

  	/**
	 * Number of items per page
	 *
	 * @var int
	 * @since 1.0
	 */
	public $per_page = 10;

	/**
	 * Number of generic prizes found
	 *
	 * @var int
	 * @since 1.0
	 */
	public $count = 0;

	/**
	 * Total generic prize
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
			'singular' => __( 'Generic Prize', 'contest-code' ),
			'plural'   => __( 'Generic Prizes', 'contest-code' ),
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
		// TODO: Implement....
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
	public function column_title( $item ) {
		$prize = get_post( $item['ID'] );
		$row_actions  = array();

		$row_actions['edit'] = '<a href="' . add_query_arg( array( 'ccc-action' => 'edit-prize', 'prize' => $prize->ID ) ) . '">' . __( 'Edit', 'contest-code' ) . '</a>';

		$row_actions['delete'] = '<a href="' . esc_url( wp_nonce_url( add_query_arg( array( 'ccc-action' => 'delete-prize', 'prize' => $prize->ID ) ), 'ccc_prize_nonce' ) ) . '">' . __( 'Delete', 'contest-code' ) . '</a>';

		return esc_html(stripslashes( $item['title'] )) . $this->row_actions( $row_actions );
	}

	/**
	 * Render the checkbox column
	 *
	 * @access public
	 * @since 1.0
	 * @param array $item Contains all the data for the checkbox column
	 * @return string Displays a checkbox
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ 'prize',
			/*$2%s*/ $item['ID']
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
			'title'         => __( 'Prize Name', 'contest-code' ),
			'prizes'  		=> __( 'Prizes', 'contest-code' ),
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
			'title'        	=> array( 'title', true ),
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
	 * Get the prizes data
	 *
	 * @access public
	 * @since 1.0
	 * @global object $wpdb Used to query the database using the WordPress
	 *   Database API
	 * @return array $reports_data All the data for customer reports
	 */
	public function prizes_data() {
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
			'post_type'	=> "ccc_prizes",
		);

		$prizes = new WP_Query($args);
		while ( $prizes->have_posts() ) {
			$prizes->the_post();

			$data[] = array(
				"ID"			=> $prizes->post->ID,
				"title" 		=> get_the_title($prizes->post->ID),
				"prizes" 		=> implode( ", " , get_post_meta($prizes->post->ID, "ccc_prize_codes") ),
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
	 * @uses CCC_Prizes_Table::get_columns()
	 * @uses WP_List_Table::get_sortable_columns()
	 * @return void
	 */
	public function prepare_items() {

		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->items = $this->prizes_data();

		$count = wp_count_posts('ccc_prizes');
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
