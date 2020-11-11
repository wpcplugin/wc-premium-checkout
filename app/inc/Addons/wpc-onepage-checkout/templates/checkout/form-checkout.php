<?php

defined( 'ABSPATH' ) || exit; 

?>

<div <?php wpc_content_class(); ?> id="wpc-wrapper">
<main id="wpc-main" role="main">
<?php

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
	return;
}

wc_print_notices();

?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<section class="step-colums">

		<?php if ( $checkout->get_checkout_fields() ) : ?>
			
			<section class="step-colum left">
				<?php do_action( 'wpc_onepage_checkout_step_colum_left_init' ); ?>

				<section class="content-box">
					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

					<?php do_action( 'woocommerce_checkout_billing' ); ?>
				</section>
				<section class="content-box content-box-address">
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>
					
					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
				</section>
			</section>

		<?php endif; ?>	

			<section class="step-colum center">
				<?php do_action( 'wpc_onepage_checkout_step_colum_center_init' ); ?>

				<section class="content-box">
					<h2 class="content-box-title"><span><?php esc_html_e( 'Shipping options', 'woocommerce' ); ?></span></h2>
					<section class="content-box-frame"> 
						<h3 class="content-box-subtitle"><span><?php esc_html_e( 'Select the desired option below', 'WPC' ); ?></span></h3>
						<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

							<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

							<?php do_action( 'wpc_onepage_cart_totals_shipping' ); ?>

							<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

						<?php endif; ?>
					</section>
				</section>

				<section class="content-box"> 
					<h2 class="content-box-title"><span><?php esc_html_e( 'Payment', 'woocommerce' ); ?></span></h2>
					<section class="content-box-frame"> 
						<h3 class="content-box-subtitle"><span><?php esc_html_e( 'Select the desired option below', 'WPC' ); ?></span></h3>
						<?php do_action( 'wpc_onepage_checkout_payment' ); ?>
					</section>
				</section>
			</section>

			<section class="step-colum right">
				<?php do_action( 'wpc_onepage_checkout_step_colum_right_init' ); ?>
				
				<section class="content-box">
					<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

						<h2 class="content-box-title"><span><?php esc_html_e( 'Your order', 'woocommerce' ); ?></span></h2>
						<section class="content-box-frame"> 

							<h3 class="content-box-subtitle"><span><?php esc_html_e( 'Order summary', 'WPC' ); ?></span></h3>
							
							<?php do_action( 'wpc_onepage_checkout_payment_cart_review' ); ?>
							<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

							<div id="order_review" class="woocommerce-checkout-review-order">
								<?php do_action( 'woocommerce_checkout_order_review' ); ?>
							</div>
						</section>
				</section>
				
				<?php do_action( 'wpc_onepage_checkout_after_order_review' ); ?>
				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
			
			</section>
			
	
	</section>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

</main>
</div>
