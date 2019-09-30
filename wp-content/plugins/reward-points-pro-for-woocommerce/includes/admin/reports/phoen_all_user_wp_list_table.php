<?php 

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Phoen_Reward_All_User extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Reward', ' phoen-rewpts' ), //singular name of the listed records
			'plural'   => __( 'Rewards', ' phoen-rewpts' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		] );

	}
	
	function column_user_email( $item ) {
		
	  // create a nonce
	  $delete_nonce = wp_create_nonce( 'phoen_user_list_delete_user' );
	  $edit_nonce = wp_create_nonce( 'phoen_user_list_edit_user' );

	  $title = '<strong>' . $item['user_email'] . '</strong>';

	  $actions = [
		
		'edit' => sprintf( '<a href="?page=%s&action=%s&user=%s&_wpnonce=%s">Edit</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['ID'] ), $delete_nonce ),
		'view' => sprintf( '<a href="?page=%s&action=%s&user=%s&_wpnonce=%s">View</a>', esc_attr( $_REQUEST['page'] ), 'view', absint( $item['ID'] ), $edit_nonce )
	  ];

	  return $title . $this->row_actions( $actions );
	}
	
	function get_columns(){
	  $columns = array(
		'user_email' =>  __('EMAIL ID','phoen-rewpts'),
		'order_count'    => __('COMPLETED ORDER ','phoen-rewpts'),
		'amount_spent'      => __('AMOUNT SPENT','phoen-rewpts'),
		'total_point_reward'      =>__('REWARD POINTS','phoen-rewpts'),
		'amount_in_wallet' => __('AMOUNT IN WALLET ','phoen-rewpts'),
	  );
	  return $columns;
	}
	function column_default( $item, $column_name ) {
		
	  switch( $column_name ) { 
		case 'user_email': 			
		case 'order_count':
		case 'amount_spent':
		case 'total_point_reward':
		case 'amount_in_wallet':
		 return $item[ $column_name ];
		default:
		 return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
	  }
	}
	
	function prepare_items()
    {
        global $wpdb;
		
        $table_name = $wpdb->prefix . 'users'; // do not forget about tables prefix
        $per_page = 10; // constant, how much records will be shown per page
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
		$search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
		$do_search = ( $search ) ? $wpdb->prepare(" AND user_email LIKE '%%%s%%' ", $search ) : ''; 
        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);
        // will be used in pagination settings
		$total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE 1 ORDER BY ID");
        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'user_email';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';
        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
		include_once(PHOEN_REWPTSPLUGPATH.'includes/admin/reports/phoen_all_user_function.php');
       // [REQUIRED] configure pagination
		$this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
	
}