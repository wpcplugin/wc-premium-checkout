<?php

defined( 'ABSPATH' ) || exit; 

?>

<?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

	<h2 class="content-box-title"><span><?php echo apply_filters( 'wpc_onepage_checkout_print_content_box_title', __( 'Billing &amp; Shipping', 'woocommerce' ), 'billing' ); ?></span></h2>

	<?php else : ?>

	<h2 class="content-box-title"><span><?php echo apply_filters( 'wpc_onepage_checkout_print_content_box_title', __( 'Billing details', 'woocommerce' ), 'billing' ); ?></span></h2>

<?php endif; ?>
	
<section class="content-box-frame"> 

	<div class="woocommerce-billing-fields">

		<h3 class="content-box-subtitle"><span><?php esc_html_e( 'Enter buyer information', 'WPC' ); ?></span></h3>

		<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

		<div class="woocommerce-billing-fields__field-wrapper">
			<?php
			$fields = $checkout->get_checkout_fields( 'billing' );

			foreach ( $fields as $key => $field ) {
				woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
			}
			?>
		</div>
		
		<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
	</div>

	<?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
		<div class="woocommerce-account-fields">
			<?php if ( ! $checkout->is_registration_required() ) : ?>

			<p class="form-row form-row-wide woocommerce-validated create-account">
				<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1" /> <label class="checkbox" for="createaccount"><?php esc_html_e( 'Create an account?', 'woocommerce' ); ?></label>
			</p>

			<?php endif; ?>

			<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

			<?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>

				<div class="create-account">
					<?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>
						<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
					<?php endforeach; ?>
					<div class="clear"></div>
				</div>

			<?php endif; ?>

			<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
		</div>
	<?php endif; ?>
</section>