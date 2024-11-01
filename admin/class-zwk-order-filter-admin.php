<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       zworthkey.com
 * @since      1.0.0
 *
 * @package    Zwk_Order_Filter
 * @subpackage Zwk_Order_Filter/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Zwk_Order_Filter
 * @subpackage Zwk_Order_Filter/admin
 * @author     Zworthkey Vaibhav <sales@zworthkey.com>
 */
class Zwk_Order_Filter_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	private static $filter_search = array( '*', ', ', ',' );
	private static $filter_replace = array( '%', '|', '|' );

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_order_settings' ), 999 );
		add_action( 'views_edit-shop_order', array( $this, 'zwkof_show_button' ), 2000, 2000 );
		add_action( 'restrict_manage_posts', array( $this, 'zwkof_show_filters' ), 2000, 2000 );
		add_action( 'posts_where', array( $this, 'zwkof_where_plugin_functions' ) );
	}

	public function add_order_settings( $settings ) {


		$settings[] = include __DIR__ . '/class-wc-settings-zwk-order-filter.php';


		return $settings;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Zwk_Order_Filter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Zwk_Order_Filter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/zwk-order-filter-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Zwk_Order_Filter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Zwk_Order_Filter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/zwk-order-filter-admin.js', array( 'jquery' ), $this->version, false );

	}

	function zwkof_get_filters() {

		$enabled_filters = get_option( 'zwkof_save_filter' );
		if ( ! empty( $enabled_filters ) ) {
			$enabled_filters = unserialize( $enabled_filters );
		}

		return $enabled_filters;
	}

	public function zwkof_show_button( $views ) {
		$enabled_filters = $this->zwkof_get_filters();
		if ( ! empty( $enabled_filters ) ) {
			echo esc_html('<a href="" onclick="event.preventDefault()" id="zwkof_add_filter" class="button action">Added Filters</a>');
		}

		return $views;
	}

	function zwkof_show_filters() {
		$post_type = sanitize_text_field( $_GET['post_type'] );
		if ( ! isset( $_GET['post_type'] ) || $post_type != 'shop_order' ) {
			return false;
		}
		global $wpdb;
		$output  = '';
		$filters = $this->zwkof_get_filters();
		if ( ! empty( $filters ) ) {
			$opened='';
			$output .= '<div class="zwkof_special_order_filter_wrapper">';
			if ( isset( $_COOKIE["zwkof_special_order_filter"] ) && $_COOKIE["zwkof_special_order_filter"] == 'opened' ) {
				$opened = 'style="display:block"';
			}
			$output     .= "<div class='zwkof_special_order_filter' $opened>";
			$per_column = '4';
			foreach ( array_chunk( $filters, $per_column, true ) as $filter ) {

				foreach ( $filter as $filter ) {
					$output .= '<div class="inline_block">';
					if ( 'payment_method' === $filter ) {
						$selected = ( isset( $_GET['payment_method'] ) ) ? sanitize_text_field( $_GET['payment_method'] ) : '';
						$gateways = WC()->payment_gateways->payment_gateways();
						$output   .= '<div class="order_block_wrapper">';
//						$output   .= '<label for="payment_customer_filter">' . $filter . '</label>';
						$output .= '<label for="payment_customer_filter">' . ucwords( str_replace( '_', ' ', $filter ) ) .'</label>';
						$output   .= '<select name="payment_customer_filter" id="payment_method">';
						$output   .= '<option value=""></option>';
						foreach ( $gateways as $gateway ) {
							$title     = $gateway->title;
							$method_id = $gateway->id;
							if ( $selected == $method_id ) {
								$output .= '<option value="' . $method_id . '" selected>' . $title . '</option>';
							} else {
								$output .= '<option value="' . $method_id . '">' . $title . '</option>';
							}
						}
						$output .= '</select>';
						$output .= '</div></div>';
					} else {
						$output .= '<div class="order_block_wrapper">';
						$value  = ( isset( $_GET[ $filter ] ) ) ? sanitize_text_field( $_GET[ $filter ] ) : '';
						$output .= '<label for="' . $filter . '">' . ucwords( str_replace( '_', ' ', $filter ) ) . '</label>';
						$output .= '<input type="text" value="' . $value . '" name="' . $filter . '" id="' . $filter . '">';
						$output .= '</div>';
						$output .= "</div>";

					}
				}
			}
			$output .= '<div class="filter_buttons">';
			$output .= '<input name="filter_action" class="button" value="' . __( 'Apply Filters', 'woaf-plugin' ) . '" type="submit">';
			$output .= '<input name="filter_clear" class="button" value="' . __( 'Clear', 'woaf-plugin' ) . '" id="filter_clear" type="button">';
			$output .= '</div>';
//			echo '</pre>';


			$output .= '<div class="cledarfix"></div>';

			$output .= "</div>"; // .zwkof_special_order_filter
			$output .= "</div>"; // .zwkof_special_order_filter_wrapper
		}

		echo esc_html($output);
	}

	function zwkof_where_plugin_functions( $where ) {
		global $typenow, $wpdb;
		if ( 'shop_order' == $typenow ) {
			if ( isset( $_GET['nonregistered_users_filter'] ) && ! empty( $_GET['nonregistered_users_filter'] ) ) { // search by user email
				$filter = trim( sanitize_text_field( $_GET['nonregistered_users_filter'] ) );
				$filter = str_replace( "*", "%", $filter );
				$filter = $wpdb->_escape( $filter );
				if ( ! empty( $filter ) && $filter == 'nonregistered_users' ) :
					$where .= " AND $wpdb->posts.ID IN (SELECT " . $wpdb->postmeta . ".post_id FROM " . $wpdb->postmeta . " WHERE meta_key = '_customer_user' AND meta_value = '0' OR meta_key = '_customer_user' AND meta_value = '' ) ";
				endif;
				if ( ! empty( $filter ) && $filter == 'registered_users' ) :
					$where .= " AND $wpdb->posts.ID IN (SELECT " . $wpdb->postmeta . ".post_id FROM " . $wpdb->postmeta . " WHERE meta_key = '_customer_user' AND meta_value > '0' )";
				endif;
			}
			if ( isset( $_GET['order_statuses'] ) && ! empty( $_GET['order_statuses'] ) ) { // search by order statuses
				$filter = '';
				$querry = explode( ",", $_GET['order_statuses'] );
				$count  = count( $querry);
				foreach ( $querry as $key => $value ) {
					if($count>1){
						if($key+1==$count){
							$last='';
						}else{
							$last=',';
						}
					}else{
						$last='';
					}
					$filter .= strtolower( "'wc-" . trim( sanitize_text_field( $value ) ) . "'" . $last );
				}
				if ( ! empty( $filter ) ) {
					$where .= " AND $wpdb->posts.ID IN (SELECT " . $wpdb->posts . ".ID FROM " . $wpdb->posts . " WHERE `post_status` IN (" . $filter . "))";
					print_r( $where );
				}
			}
			if ( isset( $_GET['customer_email'] ) && ! empty( $_GET['customer_email'] ) ) { // search by user email
				$filter = trim( sanitize_text_field( $_GET['customer_email'] ) );
				$filter = str_replace( self::$filter_search, self::$filter_replace, $filter );
				$filter = $wpdb->_escape( $filter );
				$where  .= " AND $wpdb->posts.ID IN (SELECT " . $wpdb->postmeta . ".post_id FROM " . $wpdb->postmeta . " WHERE meta_key = '_billing_email' AND meta_value REGEXP '" . $filter . "' )";
			}
			if ( isset( $_GET['customer_first_name'] ) && ! empty( $_GET['customer_first_name'] ) ) { // search by billing first name
				$filter = trim( sanitize_text_field( $_GET['customer_first_name'] ) );
				$filter = str_replace( self::$filter_search, self::$filter_replace, $filter );
				$filter = $wpdb->_escape( $filter );
				$where  .= " AND $wpdb->posts.ID IN (SELECT " . $wpdb->postmeta . ".post_id FROM " . $wpdb->postmeta . " WHERE meta_key = '_billing_first_name' AND meta_value REGEXP '" . $filter . "' )";
			}
			if ( isset( $_GET['customer_last_name'] ) && ! empty( $_GET['customer_last_name'] ) ) { // search by billing last name
				$filter = trim( sanitize_text_field( $_GET['customer_last_name'] ) );
				$filter = str_replace( self::$filter_search, self::$filter_replace, $filter );
				$filter = $wpdb->_escape( $filter );
				$where  .= " AND $wpdb->posts.ID IN (SELECT " . $wpdb->postmeta . ".post_id FROM " . $wpdb->postmeta . " WHERE meta_key = '_billing_last_name' AND meta_value REGEXP '" . $filter . "' )";
			}
//			if ( isset( $_GET['user_billing_address'] ) && ! empty( $_GET['user_billing_address'] ) ) { // search by billing address
//				$filter = trim( sanitize_text_field( $_GET['user_billing_address'] ) );
//				$filter = str_replace( "*", "%", $filter );
//				$filter = $wpdb->_escape( $filter );
//				$where  .= " AND $wpdb->posts.ID IN (SELECT " . $wpdb->postmeta . ".post_id FROM " . $wpdb->postmeta . " WHERE meta_key = '_billing_address_1' AND meta_value LIKE '%" . $filter . "%' )";
//			}
			if ( isset( $_GET['user_billing_country'] ) && ! empty( $_GET['user_billing_country'] ) ) { // search by billing country
				$filter = '';
				$count  = count( $_GET['user_billing_country'] );
				foreach ( $_GET['user_billing_country'] as $k => $country ) {
					$suffix       = ( $k + 1 == $count ) ? "" : " OR meta_value = ";
					$country_code = "'" . trim( sanitize_text_field( $country ) ) . "'";
					$filter       .= $country_code . $suffix;
				}
				$where .= " AND $wpdb->posts.ID IN (SELECT " . $wpdb->postmeta . ".post_id FROM " . $wpdb->postmeta . " WHERE meta_key = '_billing_country' AND meta_value = " . $filter . " )";
			}
			if ( isset( $_GET['user_phone'] ) && ! empty( $_GET['user_phone'] ) ) { // search by billing phone or shipping phone
				$filter = trim( sanitize_text_field( $_GET['user_phone'] ) );
				$filter = str_replace( self::$filter_search, self::$filter_replace, $filter );
				$filter = $wpdb->_escape( $filter );
				$where  .= " AND $wpdb->posts.ID IN (SELECT " . $wpdb->postmeta . ".post_id FROM " . $wpdb->postmeta . " WHERE meta_key = '_billing_phone' AND meta_value REGEXP '" . $filter . "' OR meta_key = '_shipping_phone' AND meta_value REGEXP '" . $filter . "' )";
			}
//			if ( isset( $_GET['order_total_start'] ) && ! empty( $_GET['order_total_start'] ) || isset( $_GET['order_total_end'] ) && ! empty( $_GET['order_total_end'] ) ) { // search by total
//				$start = $_GET['order_total_start'];
//				$end   = $_GET['order_total_end'];
//
//				if ( is_numeric( $start ) || is_numeric( $end ) ) {
//					$where .= " AND $wpdb->posts.ID IN (SELECT " . $wpdb->postmeta . ".post_id FROM " . $wpdb->postmeta . " WHERE meta_key = '_order_total'";
//					if ( is_numeric( $start ) ) {
//						$where .= " AND meta_value >= " . sprintf( "%.2f", $start );
//					}
//					if ( is_numeric( $end ) ) {
//						$where .= " AND meta_value <= " . sprintf( "%.2f", $end );
//					}
//					$where .= " ) ";
//				}
//			}
			if ( isset( $_GET['shipping_method_filter'] ) && ! empty( $_GET['shipping_method_filter'] ) ) { // search by shipping
				$filter = trim( sanitize_text_field( $_GET['shipping_method_filter'] ) );
				$filter = str_replace( self::$filter_search, self::$filter_replace, $filter );
				$filter = $wpdb->_escape( $filter );
				$where  .= " AND $wpdb->posts.ID IN (SELECT " . $wpdb->prefix . "woocommerce_order_items.order_id FROM " . $wpdb->prefix . "woocommerce_order_items WHERE order_item_type = 'shipping' AND  order_item_name REGEXP '" . $filter . "' )";
			}
			if ( isset( $_GET['payment_customer_filter'] ) && ! empty( $_GET['payment_customer_filter'] ) ) { // search by payment method
				$filter = trim( sanitize_text_field( $_GET['payment_customer_filter'] ) );
				$filter = str_replace( "*", "%", $filter );
				$filter = $wpdb->_escape( $filter );
				$where  .= " AND $wpdb->posts.ID IN (SELECT " . $wpdb->postmeta . ".post_id FROM " . $wpdb->postmeta . " WHERE meta_key = '_payment_method' AND meta_value LIKE '%" . $filter . "%' )";
			}
			if ( isset( $_GET['shpping_track_number'] ) && ! empty( $_GET['shpping_track_number'] ) ) { // search by track number
				$filter = trim( sanitize_text_field( $_GET['shpping_track_number'] ) );
				$filter = str_replace( self::$filter_search, self::$filter_replace, $filter );
				$filter = $wpdb->_escape( $filter );
				if ( is_plugin_active( 'woocommerce-shipment-tracking/shipment-tracking.php' ) ) {
					$where .= " AND $wpdb->posts.ID IN (SELECT " . $wpdb->postmeta . ".post_id FROM " . $wpdb->postmeta . " WHERE meta_key = '_wc_shipment_tracking_items' AND meta_value REGEXP '\"tracking_number\";s:" . $filter . "%' )";
				} elseif ( is_plugin_active( 'woo-shipment-tracking-order-tracking/woocommerce-shipment-tracking.php' ) ) {
					$where .= " AND $wpdb->posts.ID IN (SELECT " . $wpdb->postmeta . ".post_id FROM " . $wpdb->postmeta . " WHERE meta_key = 'wf_wc_shipment_source' AND meta_value REGEXP '\"shipment_id_cs\";s:" . $filter . "%' )";
				}
			}
			if ( isset( $_GET['ant_filter_search_sku'] ) && ! empty( $_GET['ant_filter_search_sku'] ) ) { // search by SKU
				$filter = trim( $_GET['ant_filter_search_sku'] );
				$filter = str_replace( self::$filter_search, self::$filter_replace, $filter );
				$filter = $wpdb->_escape( $filter );

				$where .= " AND ($wpdb->posts.ID IN(
				SELECT $wpdb->posts.ID FROM $wpdb->posts
				INNER JOIN " . $wpdb->prefix . "woocommerce_order_items ON $wpdb->posts.ID = " . $wpdb->prefix . "woocommerce_order_items.order_id
				INNER JOIN " . $wpdb->prefix . "woocommerce_order_itemmeta ON " . $wpdb->prefix . "woocommerce_order_items.order_item_id = " . $wpdb->prefix . "woocommerce_order_itemmeta.order_item_id
				INNER JOIN $wpdb->postmeta ON " . $wpdb->prefix . "woocommerce_order_itemmeta.meta_value = $wpdb->postmeta.post_id
				WHERE $wpdb->posts.post_type = 'shop_order'
				AND " . $wpdb->prefix . "woocommerce_order_items.order_item_type = 'line_item'
				AND " . $wpdb->prefix . "woocommerce_order_itemmeta.meta_key = '_product_id'
				AND $wpdb->postmeta.meta_key = '_sku'
				AND $wpdb->postmeta.meta_value REGEXP '" . $filter . "') )";
			}
		}

		return $where;
	}

}
