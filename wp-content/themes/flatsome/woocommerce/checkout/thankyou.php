<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="row">

	<?php if ( $order ) : ?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>
		<div class="large-12 col order-failed">
			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
                <a href="<?php echo esc_url( home_url('/') ); ?>" class="button pay"><?php _e( 'Go to Home page', 'woocommerce' ) ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php _e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</p>
		</div><!-- .order-failed -->

		<?php else : ?>
    <div class="large-7 col">

    <?php
    $get_payment_method = fl_woocommerce_version_check('3.0.0') ? $order->get_payment_method() : $order->payment_method;
    $get_order_id = fl_woocommerce_version_check('3.0.0') ? $order->get_id() : $order->id;
    ?>
    <?php do_action( 'woocommerce_thankyou_' . $get_payment_method, $get_order_id ); ?>
    <?php do_action( 'woocommerce_thankyou', $get_order_id ); ?>

    </div>

		<div class="large-5 col">
			<div class="is-well col-inner entry-content">
				<p class="success-color woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><strong><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), $order ); ?></strong></p>

				<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">

					<li class="woocommerce-order-overview__order order">
						<?php _e( 'Order number:', 'woocommerce' ); ?>
						<strong><?php echo $order->get_order_number(); ?></strong>
					</li>

					<?php if(fl_woocommerce_version_check('3.0.0')) { ?>
						<li class="woocommerce-order-overview__date date">
							<?php _e( 'Date:', 'woocommerce' ); ?>
							<strong><?php echo wc_format_datetime( $order->get_date_created() ); ?></strong>
						</li>

						<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
							<li class="woocommerce-order-overview__total total">
								<?php _e( 'Email:', 'woocommerce' ); ?>
								<strong><?php echo $order->get_billing_email(); ?></strong>
							</li>
						<?php endif; ?>
					<?php } else { ?><!-- else when older older version-->
						<li class="woocommerce-order-overview__date date">
						  <?php _e( 'Date:', 'woocommerce' ); ?>
						  <strong><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></strong>
						</li>
					<?php } ?>

					<li class="woocommerce-order-overview__total total">
						<?php _e( 'Total:', 'woocommerce' ); ?>
						<strong><?php echo $order->get_formatted_order_total(); ?></strong>
					</li>

					<?php
					$payment_method_title = fl_woocommerce_version_check('3.0.0') ? $order->get_payment_method_title() : $order->payment_method_title;
					if ( $payment_method_title ) :
					?>
						<li class="woocommerce-order-overview__payment-method method">
							<?php _e( 'Payment method:', 'woocommerce' ); ?>
							<strong><?php echo wp_kses_post( $payment_method_title ); ?></strong>
						</li>
					<?php endif; ?>

				</ul>

				<div class="extra-information">
					<div class="nearest-store">
						<div class="nearest-address">
						   <span>
						   <img src="<?php echo home_url()?>/wp-content/themes/mypham9-mst40315/lib/assets/support-checkout.png">
						   </span>
							<div>Đội ngũ chăm sóc khách hàng sẽ gọi điện xác nhận đơn hàng</div>
						</div>
						<div class="store-hotline">
							<span><img src="<?php echo home_url()?>/wp-content/themes/mypham9-mst40315/lib/assets/truck.png"></span>
							<div>Chúng tôi sẽ vận chuyển hàng đến địa chỉ khách hàng</div>
						</div>
						<div class="nearest-direction">
							<span><img src="<?php echo home_url()?>/wp-content/themes/mypham9-mst40315/lib/assets/pay.png"></span>
							<div>Chúng tôi sẽ nhận tiền và thối tiền (nếu có) trực tiếp từ khách hàng</div>
						</div>
						<div class="nearest-direction">
							<span><img src="<?php echo home_url()?>/wp-content/themes/mypham9-mst40315/lib/assets/piggy-bank.png"></span>
							<div>Trong vòng 1 tiếng sau khi nhận tiền. Nếu bạn là thành viên ExtraCare, điểm tiết kiệm sẽ được cập nhật</div>
						</div>
					</div>
				</div>

				<div class="clear"></div>
			</div>
		</div>

		<?php endif; ?>

	<?php else : ?>

		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), null ); ?></p>

	<?php endif; ?>

</div>
