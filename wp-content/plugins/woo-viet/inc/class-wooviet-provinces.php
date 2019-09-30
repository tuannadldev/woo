<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class to handle Vietnam Provinces
 *
 * @author   htdat
 * @since    1.0
 *
 */
class WooViet_Provinces {

	/**
	 * Constructor: Add filters
	 */
	public function __construct() {
		add_filter( 'woocommerce_states', array( $this, 'add_provinces' ) );
		add_filter( 'woocommerce_get_country_locale', array( $this, 'edit_vn_locale' ) );
		add_filter( 'woocommerce_localisation_address_formats', array( $this, 'edit_vn_address_formats' ) );

		// Enqueue province scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_provinces_scripts' ) );
	}

	/**
	 * Change the address format of Vietnam, add {state} (or "Province" in Vietnam)
	 *
	 * @param $array
	 *
	 * @return array
	 */
	public function edit_vn_address_formats( $array ) {

		$array['VN'] = "{name}\n{company}\n{address_1}\n{city}\n{state}\n{country}";

		return $array;

	}

	/**
	 * Change the way displaying address fields in the checkout page when selecting Vietnam
	 *
	 * @param $array
	 *
	 * @return array
	 */
	public function edit_vn_locale( $array ) {
		$array['VN']['state']['label']    = __( 'Province', 'woo-viet' );
		$array['VN']['state']['required'] = true;

		$array['VN']['city']['label']      = __( 'District', 'woo-viet' );
		$array['VN']['postcode']['hidden'] = true;

		return $array;
	}


	/**
	 * Add 63 provinces of Vietnam
	 *
	 * @param $states
	 *
	 * @return array
	 */
	public function add_provinces( $states ) {
		/**
		 * @source: https://vi.wikipedia.org/wiki/Tỉnh_thành_Việt_Nam and https://en.wikipedia.org/wiki/Provinces_of_Vietnam
		 */
		$states['VN'] = array(
      'CAN-THO'         => __( 'Cần Thơ', 'woo-viet' ),
      'DA-NANG'         => __( 'Đà Nẳng', 'woo-viet' ),
      'HO-CHI-MINH'     => __( 'Hồ Chí Minh', 'woo-viet' ),
      'HA-NOI'          => __( 'Hà Nội', 'woo-viet' ),
			'AN-GIANG'        => __( 'An Giang', 'woo-viet' ),
			'BA-RIA-VUNG-TAU' => __( 'Bà Rịa - Vũng Tàu', 'woo-viet' ),
			'BAC-LIEU'        => __( 'Bạc Liêu', 'woo-viet' ),
			'BAC-KAN'         => __( 'Bắc Cạn', 'woo-viet' ),
			'BAC-GIANG'       => __( 'Bắc Giang', 'woo-viet' ),
			'BAC-NINH'        => __( 'Bắc Ninh', 'woo-viet' ),
			'BEN-TRE'         => __( 'Bến Tre', 'woo-viet' ),
			'BINH-DUONG'      => __( 'Bình Dương', 'woo-viet' ),
			'BINH-DINH'       => __( 'Bình Định', 'woo-viet' ),
			'BINH-PHUOC'      => __( 'Bình Phước', 'woo-viet' ),
			'BINH-THUAN'      => __( 'Bình Thuận', 'woo-viet' ),
			'CA-MAU'          => __( 'Cà Mau', 'woo-viet' ),
			'CAO-BANG'        => __( 'Cao Bằng', 'woo-viet' ),
			'DAK-LAK'         => __( 'Đắk Lắk', 'woo-viet' ),
			'DAK-NONG'        => __( 'Đắk Nông', 'woo-viet' ),
			'DONG-NAI'        => __( 'Đồng  Nai', 'woo-viet' ),
			'DONG-THAP'       => __( 'Đồng Tháp', 'woo-viet' ),
			'DIEN-BIEN'       => __( 'Điện Biên', 'woo-viet' ),
			'GIA-LAI'         => __( 'Gia Lai', 'woo-viet' ),
			'HA-GIANG'        => __( 'Hà Giang', 'woo-viet' ),
			'HA-NAM'          => __( 'Hà Nam', 'woo-viet' ),
			'HA-TINH'         => __( 'Hà Tĩnh', 'woo-viet' ),
			'HAI-DUONG'       => __( 'Hải Dương', 'woo-viet' ),
			'HAI-PHONG'       => __( 'Hải Phòng', 'woo-viet' ),
			'HOA-BINH'        => __( 'Hoà Bình', 'woo-viet' ),
			'HAU-GIANG'       => __( 'Hậu Giang', 'woo-viet' ),
			'HUNG-YEN'        => __( 'Hưng Yên', 'woo-viet' ),
			'KHANH-HOA'       => __( 'Khánh Hoà', 'woo-viet' ),
			'KIEN-GIANG'      => __( 'Kiên Giang', 'woo-viet' ),
			'KON-TUM'         => __( 'Kon Tum', 'woo-viet' ),
			'LAI-CHAU'        => __( 'Lai Châu', 'woo-viet' ),
			'LAO-CAI'         => __( 'Lào Cai', 'woo-viet' ),
			'LANG-SON'        => __( 'Lạng Sơn', 'woo-viet' ),
			'LAM-DONG'        => __( 'Lâm Đồng', 'woo-viet' ),
			'LONG-AN'         => __( 'Long An', 'woo-viet' ),
			'NAM-DINH'        => __( 'Nam Định', 'woo-viet' ),
			'NGHE-AN'         => __( 'Nghệ An', 'woo-viet' ),
			'NINH-BINH'       => __( 'Ninh Bình', 'woo-viet' ),
			'NINH-THUAN'      => __( 'Ninh Thuận', 'woo-viet' ),
			'PHU-THO'         => __( 'Phú Thọ', 'woo-viet' ),
			'PHU-YEN'         => __( 'Phú Yên', 'woo-viet' ),
			'QUANG-BINH'      => __( 'Quảng Bình', 'woo-viet' ),
			'QUANG-NAM'       => __( 'Quảng Nam', 'woo-viet' ),
			'QUANG-NGAI'      => __( 'Quảng Ngãi', 'woo-viet' ),
			'QUANG-NINH'      => __( 'Quảng Ninh', 'woo-viet' ),
			'QUANG-TRI'       => __( 'Quảng Trị', 'woo-viet' ),
			'SOC-TRANG'       => __( 'Sóc Trăng', 'woo-viet' ),
			'SON-LA'          => __( 'Sơn La', 'woo-viet' ),
			'TAY-NINH'        => __( 'Tây Ninh', 'woo-viet' ),
			'THAI-BINH'       => __( 'Thái Bình', 'woo-viet' ),
			'THAI-NGUYEN'     => __( 'Thái Nguyên', 'woo-viet' ),
			'THANH-HOA'       => __( 'Thanh Hoá', 'woo-viet' ),
			'THUA-THIEN-HUE'  => __( 'Thừa Thiên - Huế', 'woo-viet' ),
			'TIEN-GIANG'      => __( 'Tiền Giang', 'woo-viet' ),
			'TRA-VINH'        => __( 'Trà Vinh', 'woo-viet' ),
			'TUYEN-QUANG'     => __( 'Tuyên Quang', 'woo-viet' ),
			'VINH-LONG'       => __( 'Vĩnh Long', 'woo-viet' ),
			'VINH-PHUC'       => __( 'Vĩnh Phúc', 'woo-viet' ),
			'YEN-BAI'         => __( 'Yên Bái', 'woo-viet' ),
		);

		return $states;

	}

	/**
	* Enqueue provinces scripts
	*
	* Arrange the address field orders to the Vietnam standard in the checkout page: Country - Province - District - Address
	* @author 	Longkt
	* @since 	1.4
	*/
	public function load_provinces_scripts() {
		// Enqueue province style
		wp_enqueue_style( 'woo-viet-provinces-style', WOO_VIET_URL . 'assets/provinces.css' );

		// Enqueue province script
		wp_enqueue_script( 'woo-viet-provinces-script', WOO_VIET_URL . 'assets/provinces.js', array( 'jquery' ), '1.0', true );
	}
}
