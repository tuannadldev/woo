<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Barn2\Lib\Util;

/**
 * Handles display and processing of the password form used for password protected categories and products.
 *
 * @package   WooCommerce_Protected_Categories
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_PPC_Password_Form implements \Barn2\Lib\Attachable {

	private static $form_id = 1;

	public function attach() {
		add_action( 'wp', array( $this, 'do_login' ) );
		add_filter( 'wc_ppc_password_form', array( 'WC_PPC_Util', 'sanitize_whitespace_for_autop' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
	}

	/**
	 * Handle submission of category login form.
	 *
	 * @global boolean $wp_did_header
	 */
	public function do_login() {
		global $wp_did_header;

		// Bail if we're not loading a front-end template, or password form not submitted.
		if ( ! $wp_did_header || 'POST' !== $_SERVER['REQUEST_METHOD'] || ! filter_input( INPUT_POST, 'wc_ppc_login', FILTER_VALIDATE_INT ) ) {
			return;
		}

		$password = filter_input( INPUT_POST, 'post_password', FILTER_SANITIZE_STRING );

		if ( ! $password ) {
			return;
		}

		$categories		 = array();
		$central_login	 = false;

		if ( filter_input( INPUT_POST, 'wc_ppc_central_login', FILTER_VALIDATE_BOOLEAN ) ) {
			$central_login = true;
		}

		if ( $central_login ) {
			// Form submitted from central login page, shortcode or widget, so we fetch all categories.
			$categories = WC_PPC_Util::to_category_visibilities( WC_PPC_Util::get_product_categories() );
		} elseif ( is_product_category() ) {
			// Form submitted from a product catetory.
			$categories = array( WC_PPC_Util::get_category_visibility( get_queried_object_id() ) );
		} elseif ( is_product() ) {
			// Form submitted from a single product.
			$categories = WC_PPC_Util::get_the_category_visibility();
		}

		if ( ! $categories ) {
			return;
		}

		// Check the password against each category.
		foreach ( $categories as $category ) {

			if ( $term_id = $category->check_password( $password ) ) {
				// Got a valid password, so set password cookie then redirect.
				WC_PPC_Util::set_password_cookie( $term_id, $password );

				if ( $central_login ) {
					wp_safe_redirect( get_term_link( $term_id, 'product_cat' ) );
				} else {
					wp_safe_redirect( add_query_arg( null, null ) );
				}
				return;
			}
		}
	}

	public function show_password_login_form( $args = array() ) {
		echo $this->get_password_login_form( $args );
	}

	public function get_password_login_form( $args = array() ) {
		wp_enqueue_style( 'wc-ppc' );

		$args = wp_parse_args( $args, array(
			'heading' => WC_PPC_Util::get_password_form_heading(),
			'show_heading' => true,
			'message' => WC_PPC_Util::get_password_form_message(),
			'label' => get_option( 'wc_ppc_form_password_label', __( 'Password:', 'wc-cat-protect' ) ),
			'button_text' => get_option( 'wc_ppc_form_button', _x( 'Login', 'category password form', 'wc-cat-protect' ) ),
			'container_class' => get_option( 'wc_ppc_form_container_class' ),
			'central_login' => true
			) );

		$container_class = apply_filters( 'wc_ppc_password_form_container_class', trim( 'wc-ppc-form-wrapper ' . $args['container_class'] ) );
		$form_id		 = 'wc-ppc-password-form-' . self::$form_id;
		$form_class		 = apply_filters( 'wc_ppc_password_form_class', 'wc-ppc-password-form post-password-form category-login' );
		$form_action	 = add_query_arg( null, null );

		$message = $args['message'] ? do_shortcode( wpautop( $args['message'] ) ) : '';
		$message .= $this->get_error_message();

		$label_id	 = 'pwbox-' . self::$form_id;
		$label_text	 = $args['label'];
		$placeholder = '';

		if ( 'yes' === get_option( 'wc_ppc_form_label_placeholder' ) ) {
			$placeholder = ' placeholder="' . esc_attr( $label_text ) . '"';
			$label_text	 = '';
		} elseif ( $label_text ) {
			// If we have a password label, add a space after to align the form better
			$label_text .= ' ';
		}

		ob_start();
		do_action( 'wc_ppc_before_password_form' );
		?>
		<div class="<?php echo esc_attr( $container_class ); ?>">
			<form id="<?php echo esc_attr( $form_id ); ?>" action="<?php echo esc_url( $form_action ); ?>" class="<?php echo esc_attr( $form_class ); ?>" method="post">
				<?php if ( $args['show_heading'] && $args['heading'] ) : ?>
					<h3 class="category-login__heading"><?php echo $args['heading']; ?></h3>
				<?php endif; ?>
				<?php if ( $message ) : ?>
					<div class="category-login__text"><?php echo $message; ?></div>
				<?php endif; ?>
				<p class="category-login__fields wc-ppc-password-form-inputs">
					<label class="category-login__label wc-ppc-password-label"><?php echo esc_html( $label_text ); ?><input name="post_password" id="<?php echo esc_attr( $label_id ); ?>" class="category-login__password wc-ppc-password" type="password" size="25"<?php echo $placeholder; ?> /></label> <input class="category-login__submit" type="submit" name="Submit" value="<?php echo esc_attr( $args['button_text'] ); ?>" />
					<input type="hidden" name="wc_ppc_login" value="<?php echo esc_attr( self::$form_id ); ?>" />
					<?php if ( $args['central_login'] ) : ?>
						<input type="hidden" name="wc_ppc_central_login" value="1" />
					<?php endif; ?>
				</p>
			</form>
		</div>
		<?php
		do_action( 'wc_ppc_after_password_form' );

		self::$form_id ++;

		return apply_filters( 'wc_ppc_password_form', ob_get_clean() );
	}

	public function register_scripts() {
		$suffix = Util::get_script_suffix();
		wp_register_style( 'wc-ppc', plugins_url( "assets/css/wc-ppc{$suffix}.css", WC_Protected_Categories_Plugin::FILE ), array(), WC_Protected_Categories_Plugin::VERSION );
	}

	private function get_error_message() {
		if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
			return '';
		}

		$submitted_form_id = filter_input( INPUT_POST, 'wc_ppc_login', FILTER_VALIDATE_INT );

		// Check the passed form ID matches the one in $_POST
		if ( ! $submitted_form_id || self::$form_id !== (int) $submitted_form_id ) {
			return '';
		}

		$error		 = '';
		$password	 = filter_input( INPUT_POST, 'post_password', FILTER_SANITIZE_STRING );

		if ( ! $password ) {
			$error = __( 'Please enter a password.', 'wc-cat-protect' );
		} else {
			// If we have a password, and we're doing a POST request (therefore we haven't redirected), then the password must be wrong.
			$error = __( 'Incorrect password, please try again.', 'wc-cat-protect' );
		}

		if ( $error ) {
			return sprintf( '<p class="category-login__error wc-ppc-login-error">%s</p>', $error );
		}
		return '';
	}

	public static function display_password_form( $args = array() ) {
		_deprecated_function( __FUNCTION__, '2.1.1', 'WC_PPC_Password_Form()->show_password_login_form()' );

		$form = new WC_PPC_Password_Form();
		$form->show_password_login_form( $args );
	}

	public static function get_password_form( $args = array() ) {
		_deprecated_function( __FUNCTION__, '2.1.1', 'WC_PPC_Password_Form()->get_password_login_form()' );

		$form = new WC_PPC_Password_Form();
		$form->get_password_login_form( $args );
	}

	public static function handle_login() {
		_deprecated_function( __FUNCTION__, '2.1.1', 'WC_PPC_Password_Form()->do_login()' );

		$form = new WC_PPC_Password_Form();
		$form->do_login();
	}

}
