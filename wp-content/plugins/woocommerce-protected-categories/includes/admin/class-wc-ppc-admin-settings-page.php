<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides functions for the plugin settings page in the WordPress admin. Settings are under the WooCommerce 'Products' tab.
 *
 * @package   WooCommerce_Protected_Categories\Admin
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_PPC_Admin_Settings_Page implements \Barn2\Lib\Attachable {

	private $license;
	private $key_saved = false;

	public function __construct( Barn2_Plugin_License $license ) {
		$this->license = $license;
	}

	public function attach() {
		// Add plugin settings
		add_filter( 'woocommerce_get_sections_products', array( $this, 'add_section' ) );
		add_filter( 'woocommerce_get_settings_products', array( $this, 'add_settings' ), 10, 2 );
		add_action( 'woocommerce_admin_field_hidden', array( $this, 'hidden_field' ) );

		// Save settings
		add_filter( 'woocommerce_admin_settings_sanitize_option_' . $this->license->license_key_option, array( $this, 'save_license_key' ), 10, 3 );
		add_action( 'woocommerce_update_option', array( $this, 'save_license_key_pre_24' ) ); // For WooCommerce < 2.4
		add_action( 'update_option', array( $this, 'save_category_login_page' ), 10, 3 );
	}

	public function add_section( $sections ) {
		$sections['protected-cats'] = __( 'Protected categories', 'wc-cat-protect' );
		return $sections;
	}

	public function add_settings( $settings, $current_section ) {
		// Check we're on the correct settings section
		if ( 'protected-cats' !== $current_section ) {
			return $settings;
		}

		$settings_protected = array(
			array(
				'name' => __( 'Protected categories', 'wc-cat-protect' ),
				'type' => 'title',
				'desc' => __( 'The following options control the WooCommerce Protected Categories extension.', 'wc-cat-protect' ) . $this->get_settings_page_support_links(),
				'id' => 'wcppc_general'
			),
			array(
				'title' => __( 'License key', 'wc-cat-protect' ),
				'desc' => $this->license->get_license_key_admin_message(),
				'id' => $this->license->license_key_option,
				'class' => 'regular-input',
				'type' => 'text',
				'desc_tip' => __( 'The licence key is contained in your order confirmation email.', 'wc-cat-protect' )
			),
			array(
				'title' => __( 'Category visibility', 'wc-cat-protect' ),
				'desc' => __( 'Show protected categories & products in the public-facing store', 'wc-cat-protect' ),
				'id' => 'wc_ppc_show_protected',
				'type' => 'checkbox',
				'default' => 'yes',
				'checkboxgroup' => 'start',
				'desc_tip' => __( 'Tick to show protected categories in public areas of your site (e.g. the shop page, category pages, sidebars and search results). Untick to hide them from public view.', 'wc-cat-protect' ) . '<br/>' .
				__( "Protected categories which have been 'unlocked' for the current user are always displayed.", 'wc-cat-protect' )
			),
			array(
				'desc' => __( 'Show protected categories & products in navigation menus', 'wc-cat-protect' ),
				'id' => 'wc_ppc_show_protected_menu',
				'type' => 'checkbox',
				'default' => get_option( 'wc_ppc_show_protected', 'yes' ),
				'checkboxgroup' => 'end'
			),
			array(
				'title' => __( 'Prefix categories', 'wc-cat-protect' ),
				'desc' => __( 'Prefix names of protected categories', 'wc-cat-protect' ),
				'desc_tip' => __( 'Categories will be prefixed with "Protected" or "Private" on the category page, shop page, navigation menus and widgets.', 'wc-cat-protect' ),
				'id' => 'wc_ppc_prefix_categories',
				'type' => 'checkbox'
			),
			array(
				'type' => 'sectionend',
				'id' => 'wcppc_general'
			),
			array(
				'name' => __( 'Private, user and role restricted categories', 'wc-cat-protect' ),
				'type' => 'title',
				'desc' => __( 'The following options apply to private categories and categories restricted to specific roles and users.', 'wc-cat-protect' ),
				'id' => 'wcppc_user_protection'
			),
			array(
				'title' => __( 'When logged out', 'wc-cat-protect' ),
				'desc_tip' => __( 'What logged out users see when they try to access a category that is private, or protected by role or user.', 'wc-cat-protect' ),
				'id' => 'wc_ppc_user_protected',
				'type' => 'select',
				'options' => array(
					'404' => __( 'Show 404 error', 'wc-cat-protect' ),
					'wplogin' => __( 'Show WordPress login page', 'wc-cat-protect' ),
					'page' => __( 'Show custom page', 'wc-cat-protect' ),
				),
				'class' => 'toggle-parent',
				'custom_attributes' => array(
					'data-child-class' => 'user-protected',
					'data-toggle-val' => 'page'
				)
			),
			array(
				'title' => __( 'Custom page', 'wc-cat-protect' ),
				'id' => 'wc_ppc_user_protected_redirect',
				'class' => 'wc-enhanced-select-nostd user-protected',
				'css' => 'min-width:300px;',
				'type' => 'single_select_page'
			),
			array(
				'type' => 'sectionend',
				'id' => 'wcppc_user_protection'
			),
			array(
				'name' => __( 'Password protected categories', 'wc-cat-protect' ),
				'type' => 'title',
				'desc' => __( 'The following options apply to categories which are protected by a password.', 'wc-cat-protect' ),
				'id' => 'wcppc_password_protection'
			),
			array(
				'title' => __( 'Password expiry', 'wc-cat-protect' ),
				'desc_tip' => __( 'How long a password protected category stays unlocked after the user enters the correct password.', 'wc-cat-protect' ),
				'id' => 'wc_ppc_password_expires',
				'type' => 'number',
				'default' => 10,
				'custom_attributes' => array( 'min' => 1 ),
				'css' => 'width:80px;',
				'desc' => ' ' . __( 'days', 'wc-cat-protect' )
			),
			array(
				'title' => __( 'Password entry page', 'wc-cat-protect' ),
				'desc_tip' => __( "Optional: set this if you want a central password entry page, where users can 'unlock' their category. You can also add a password form anywhere using the shortcode [category_login].", 'wc-cat-protect' ),
				'id' => 'wc_ppc_category_login_page',
				'class' => 'wc-enhanced-select-nostd',
				'css' => 'min-width:300px;',
				'type' => 'single_select_page'
			),
			array(
				'title' => __( 'Heading', 'wc-cat-protect' ),
				'desc_tip' => __( 'The main heading displayed on the password entry page.', 'wc-cat-protect' ),
				'id' => 'wc_ppc_login_title',
				'type' => 'text',
				'default' => __( 'Please Login', 'wc-cat-protect' )
			),
			array(
				'title' => __( 'Message', 'wc-cat-protect' ),
				'desc' => __( 'Customize the wording displayed on the password entry page, above the password box. You can use HTML.', 'wc-cat-protect' ),
				'id' => 'wc_ppc_password_form',
				'type' => 'textarea',
				'class' => 'wide-input',
				'default' => __( 'This content is password protected. To view it please enter your password below:', 'wc-cat-protect' ),
				'desc_tip' => true,
				'custom_attributes' => array( 'cols' => 20, 'rows' => 3 )
			),
			array(
				'title' => __( 'Password label', 'wc-cat-protect' ),
				'id' => 'wc_ppc_form_password_label',
				'type' => 'text',
				'desc_tip' => __( 'The label shown next to the password box.', 'wc-cat-protect' ),
				'default' => __( 'Password:', 'wc-cat-protect' )
			),
			array(
				'title' => __( 'Use placeholder?', 'wc-cat-protect' ),
				'desc' => __( 'Use the label as a placeholder for the password input', 'wc-cat-protect' ),
				'id' => 'wc_ppc_form_label_placeholder',
				'type' => 'checkbox'
			),
			array(
				'title' => __( 'Login button', 'wc-cat-protect' ),
				'id' => 'wc_ppc_form_button',
				'type' => 'text',
				'default' => esc_attr_x( 'Login', 'category login form button', 'wc-cat-protect' )
			),
			array(
				'title' => __( 'Password container class', 'wc-cat-protect' ),
				'desc' => __( 'Advanced: enter a CSS class for the container that surrounds the password form.', 'wc-cat-protect' ),
				'desc_tip' => true,
				'id' => 'wc_ppc_form_container_class',
				'type' => 'text'
			),
			array(
				'type' => 'sectionend',
				'id' => 'wcppc_password_protection'
			),
		); // settings array

		if ( filter_input( INPUT_GET, 'license_debug' ) ) {
			$settings_protected[] = array(
				'type' => 'hidden',
				'id' => 'license_debug',
				'default' => '1'
			);
		}
		if ( $override = filter_input( INPUT_GET, 'license_override', FILTER_SANITIZE_STRING ) ) {
			$settings_protected[] = array(
				'type' => 'hidden',
				'id' => 'license_override',
				'default' => $override
			);
		}

		return $settings_protected;
	}

	public function hidden_field( $value ) {
		if ( ! empty( $value['id'] ) && isset( $value['default'] ) ) :
			?>
			<input type="hidden" name="<?php echo esc_attr( $value['id'] ); ?>" value="<?php echo esc_attr( $value['default'] ); ?>" />
			<?php
		endif;
	}

	public function save_license_key( $value, $option, $raw_value ) {
		if ( ! $this->key_saved ) {
			$this->license->save( $value );
			$this->key_saved = true;
		}
		return $value;
	}

	public function save_license_key_pre_24( $option ) {
		if ( $this->key_saved || empty( $option['id'] ) || $this->license->license_key_option !== $option['id'] ) {
			return;
		}

		$license_key = wc_clean( filter_input( INPUT_POST, $option['id'], FILTER_SANITIZE_STRING ) );

		$this->license->save( $license_key );
		$this->key_saved = true;
	}

	public function save_category_login_page( $option, $old_value, $value ) {
		// This action is only fired when options are actually changed/updated

		if ( $option !== 'wc_ppc_category_login_page' ) {
			return;
		}

		// Add login shortcode to selected page
		$login_shortcode = sprintf( '[%s]', WC_PPC_Login_Shortcode::SHORTCODE );

		if ( $value ) {
			$page = get_post( $value );

			if ( $page instanceof WP_Post && false === strpos( $page->post_content, '[' . WC_PPC_Login_Shortcode::SHORTCODE ) ) {
				$content = $page->post_content ? ( $page->post_content . "\n\n" . $login_shortcode ) : $login_shortcode;

				wp_update_post( array(
					'ID' => $page->ID,
					'post_content' => $content
				) );
			}
		}
		if ( $old_value ) {
			// Remove from old page, if present
			$page = get_post( $old_value );

			if ( $page instanceof WP_Post && false !== strpos( $page->post_content, '[' . WC_PPC_Login_Shortcode::SHORTCODE ) ) {
				$content = trim( preg_replace( sprintf( '/\[%s.*?\]/', WC_PPC_Login_Shortcode::SHORTCODE ), '', $page->post_content ) );

				wp_update_post( array(
					'ID' => $page->ID,
					'post_content' => $content
				) );
			}
		}
	}

	public function get_settings_page_support_links() {
		ob_start();
		?>
		<ul class="subsubsub wc-ppc-support-links">
			<li><a href="https://barn2.co.uk/kb-categories/wpc-getting-started/" target="_blank"><?php _e( 'Getting Started Guide', 'wc-cat-protect' ); ?></a></li>
			<li>| <a href="https://barn2.co.uk/kb-categories/wpc-kb/" target="_blank"><?php _e( 'Knowledge Base', 'wc-cat-protect' ); ?></a></li>
		</ul>
		<?php
		return ob_get_clean();
	}

}

// class WC_PPC_Admin_Settings_Page

