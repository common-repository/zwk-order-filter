<?php
defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Settings_Order_filter', false ) ) {
	return new WC_Settings_Order_filter();
}

/**
 * WC_Settings_Emails.
 */
class WC_Settings_Order_filter extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'zwk_order_filter';
		$this->label = __( 'Pay Filter', '' );


		parent::__construct();
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {

		$sections = array(
			'' => __( 'Discount options', '' ),
		);

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}


	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {

		$objects    = array(
			array(
				'id'   => 'order_statuses',
				'name' => 'Order Statuses'
			),
			array(
				'id'   => 'customer_group',
				'name' => 'Customer Group'
			),
			array(
				'id'   => 'shipping_method_filter',
				'name' => 'Shipping Method'
			),
			array(
				'id'   => 'customer_email',
				'name' => 'Customer Email'
			),
			array(
				'id'   => 'customer_first_name',
				'name' => 'Customer First Name'
			),
			array(
				'id'   => 'customer_last_name',
				'name' => 'Customer Last Name'
			),
			array(
				'id'=>'payment_method',
				'name'=>'Payment Method'
			),
			array(
				'id' => 'user_billing_country',
				'name'=> 'User Billing Country'
			),
			array(
				'id'=>'shpping_track_number',
				'name'=>'Shipping Number'
			),
			array(
				'id'=>'ant_filter_search_sku',
				'name'=>'Order SKU'
			)
		);
		$settings[] = array(
			'title' => __( 'Choose the options to you want to add filter on!', '' ),
			'type'  => 'title',
			'desc'  => __( 'these filter the will shown on orders Page', '' ),
			'id'    => 'zwkof_option_list'
		);


		foreach ( $objects as $object ) {
			$settings[] = array(
				'title'    => __( $object['name'], '' ),
				'id'       => 'filters[' . $object["id"] . ']',
				'default'  => 'no',
				'type'     => 'checkbox',
				'desc_tip' => __( 'Check if you want to filter by ' . $object['name'] . '.', '' ),
				'name'     => 'filters[]'
			);

		}
		$settings[] = array( 'type' => 'sectionend', 'id' => 'wc-save-settings' );


		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings );
	}

	public function output() {
		$settings = $this->get_settings();
		WC_Admin_Settings::output_fields( $settings );
	}

	/**
	 * Save settings.
	 */
	public function save() {
		$settings = $this->get_settings();
		$this->save_filter();
		WC_Admin_Settings::save_fields( $settings );
	}

	public function save_filter() {
		if ( ! empty( $_POST['filters'] ) ) {
			$applied_filter = array();
			foreach ( $_POST['filters'] as $filter => $val ) {
				$applied_filter[] = $filter;
			}
			update_option( 'zwkof_save_filter', serialize( $applied_filter ) );
		}
	}
}


return new WC_Settings_Order_filter();
