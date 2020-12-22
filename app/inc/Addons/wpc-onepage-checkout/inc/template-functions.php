<?php 

if ( ! function_exists( 'wpc_onepage_checkout_place_order' ) ) {

	function wpc_onepage_checkout_place_order() 
	{
		if ( WC()->cart->needs_payment() ) {
			$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
			WC()->payment_gateways()->set_current_gateway( $available_gateways );
		} else {
			$available_gateways = array();
		}
		wc_get_template(
			'checkout/place-order.php',
			array(
				'checkout'           => WC()->checkout(),
				'available_gateways' => $available_gateways,
				'order_button_text'  => apply_filters( 'wpc_onepage_checkout_order_button_text', __( 'Place order', 'woocommerce' ) ),
			)
		);
	}
}

if ( ! function_exists( 'wpc_onepage_checkout_cart_totals_shipping_html' ) ) {

	function wpc_onepage_checkout_cart_totals_shipping_html() 
	{ 
		$packages = WC()->shipping->get_packages(); 
	 
		foreach ( $packages as $i => $package ) { 
			$chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : ''; 
			$product_names = array(); 
	 
			if ( sizeof( $packages ) > 1 ) { 
				foreach ( $package['contents'] as $item_id => $values ) { 
					$product_names[ $item_id ] = $values['data']->get_name() . ' ×' . $values['quantity']; 
				} 
				$product_names = apply_filters( 'woocommerce_shipping_package_details_array', $product_names, $package ); 
			} 
	 
			wc_get_template( 'checkout/cart-shipping.php', array( 
				'package' => $package,  
				'available_methods' => $package['rates'],  
				'show_package_details' => sizeof( $packages ) > 1,  
				'package_details' => implode( ', ', $product_names ),  
				// @codingStandardsIgnoreStart 
				'package_name' => apply_filters( 'woocommerce_shipping_package_name', sprintf( _nx( 'Shipping', 'Shipping %d', ( $i + 1 ), 'shipping packages', 'woocommerce' ), ( $i + 1 ) ), $i, $package ),  
				// @codingStandardsIgnoreEnd 
				'index' => $i,  
				'chosen_method' => $chosen_method,  
			) ); 
		} 
	} 
}

if ( ! function_exists( 'wpc_onepage_checkout_steps' ) ) {

	function wpc_onepage_checkout_steps() 
	{
		wc_get_template( 'steps.php', array() );
	}
}

if ( ! function_exists( 'wpc_onepage_checkout_cart_review' ) ) {

	function wpc_onepage_checkout_cart_review() 
	{
		wc_get_template( 'checkout/cart-review.php', array() );
	}
}

if ( ! function_exists( 'wpc_onepage_checkout_header' ) ) {

	function wpc_onepage_checkout_header() 
	{
		wc_get_template( 'header.php' );
	}
}

if ( ! function_exists( 'wpc_onepage_checkout_restrict_templates_parts' ) ) {

	function wpc_onepage_checkout_restrict_templates_parts( $load, $template, $template_name, $plugin_path ) 
	{
		switch( $template_name ) {
			case 'notices/notice.php':
			case 'notices/error.php':
			case 'notices/success.php':
				if( wpc_is_checkout() && !is_wc_endpoint_url() || ( wp_doing_ajax() && wpc_is_checkout( url_to_postid( wp_get_referer() ) ) ) ) {
					$load = true;
				} else {
					$load = false;
				}
				break;
		}
		
		return $load;
	}
}

if ( ! function_exists( 'wpc_onepage_checkout_template_active_callback' ) ) {

	function wpc_onepage_checkout_template_active_callback( $compare ) 
	{
		return (
			!is_wc_endpoint_url() && is_checkout()
		);
	}
	
}

if ( ! function_exists( 'wpc_onepage_checkout_template_active_callback' ) ) {

	function wpc_onepage_checkout_template_active_callback( $compare ) 
	{
		return (
			!is_wc_endpoint_url() && is_checkout()
		);
	}
	
}

if ( ! function_exists( 'wpc_onepage_checkout_partial_logo' ) ) {

	function wpc_onepage_checkout_partial_logo() 
	{
		return (
			!is_wc_endpoint_url() && is_checkout()
		);
	}
	
}

if ( ! function_exists( 'wpc_onepage_checkout_print_css' ) ) {

	function wpc_onepage_checkout_print_css() 
	{
		$css = '';
		$page_template = get_option( 'wpc_theme_compatibility_page_template', 'wp_theme' );
		$page_template_container = get_option( 'wpc_theme_compatibility_page_template_wp_theme_container', 'default' );

		if( !is_wc_endpoint_url() && is_checkout() ) {
			$css_vars = apply_filters( 'wpc_onepage_checkout_css_vars', array(
				'--wpc-base-max-width' => get_option( 'wpc_theme_onepage_checkout_container_max_width', '1024px' ),
				'--wpc-base-spacing' => get_option( 'wpc_theme_onepage_checkout_container_spacing', '20px' ),
				'--wpc-order-button-color' => get_option( 'wpc_theme_onepage_checkout_order_button_color', '#00899d' ),
				'--wpc-primary-background-color' => get_option( 'wpc_theme_onepage_checkout_content_primary_color', '#00646d' ),
				'--wpc-primary-link-color' => '#1e90ff',
				'--wpc-primary-text-color' => '#4a4a4a',
				'--wpc-primary-text-decoration-color' => '#ffffff',
				'--wpc-base-font-family' => 'Arial, Helvetica, sans-serif',
				'--wpc-base-font-size' => '12px',
				'--wpc-base-line-height' => '1.35',
				
				'--wpc-label-background-color' => 'var(--wpc-primary-background-color)',
				'--wpc-label-text-color' => 'var(--wpc-primary-text-decoration-color)',
				'--wpc-label-font-family' => 'var(--wpc-base-font-family)',
				'--wpc-label-font-weight' => '400',
				'--wpc-label-font-size' => '1em',
				'--wpc-label-font-style' => 'normal',
				'--wpc-label-height' => '32px',
				'--wpc-label-radius' => '0',
				'--wpc-label-text-transform' => 'none',
				'--wpc-label-icon-size' => '15px',
				'--wpc-label-icon-color' => 'var(--wpc-primary-text-decoration-color)',
				'--wpc-label-icon-border-color' => '#00000000',
				'--wpc-label-icon-font-family' => 'var(--wpc-base-font-family)',
				'--wpc-label-icon-font-weight' => '400',
				'--wpc-label-icon-font-size' => '1em',
				'--wpc-label-icon-font-style' => 'normal',
				
				'--wpc-description-visible' => 'block',
				'--wpc-description-spacing-top' => '15px',
				'--wpc-description-spacing-bottom' => '15px',
				'--wpc-description-background-color' => '#ececec',
				'--wpc-description-text-color' => 'var(--wpc-primary-text-color)',
				'--wpc-description-line-bottom-color' => '#00000000',
				'--wpc-description-font-family' => 'var(--wpc-base-font-family)',
				'--wpc-description-font-weight' => '400',
				'--wpc-description-font-size' => '1em',
				'--wpc-description-font-style' => 'normal',
				
				'--wpc-content-box-background-color' => '#ffffff',
				'--wpc-content-box-border-color' => '#ececec',
				'--wpc-content-box-border-width' => '1px',
				'--wpc-content-box-list-spacing' => '0',
				'--wpc-content-box-list-line-spacing' => '11px',
				'--wpc-content-box-order-button-spacing' => '11px',
				'--wpc-content-box-order-button-radius' => '0',
				'--wpc-content-box-order-button-font-family' => 'var(--base-font-family)',
				'--wpc-content-box-order-button-font-weight' => 'bold',
				'--wpc-content-box-order-button-font-size' => '18px',
				'--wpc-content-box-order-button-text-transform' => 'uppercase',
				'--wpc-content-box-order-table-background-color' => '#ffffff',
				'--wpc-content-box-cupom_button-background-color' => 'var(--wpc-primary-background-color)',
			) );

			if ( !empty( $css_vars ) ) {
				$css .= ":root {";
				foreach( $css_vars as $key => $value ) {
					$css .= "{$key}: {$value};";
				}
				$css .= "}";
			}

			if ( true !== WC()->cart->needs_shipping_address() ) {
				$css .= "#wpc-main .content-box.content-box-address {display: none;}";
			}
			
			if ( ( 'wp_theme' === $page_template && 'full' === $page_template_container ) || ( 'default' === $page_template ) ) {
				$css .= "#wpc-wrapper #wpc-main {max-width: var(--wpc-base-max-width);}";
			}
			
			if ( ! empty( $css ) ){			
				printf( '<style type="text/css" id="wpc_onepage_checkout-css">%s</style>', apply_filters( 'wpc_onepage_checkout_print_css', $css ) );
			}
		}
		
	}
	
}

if ( ! function_exists( 'wpc_onepage_checkout_body_classes' ) ) {

	function wpc_onepage_checkout_body_classes( $classes ) 
	{	
		$classes[] = get_option( 'wpc_theme_onepage_checkout_form_layout', '' );
  
		return $classes;
	}
	
}

if ( ! function_exists( 'wpc_onepage_checkout_cart_totals_shipping_fragment' ) ) {
 
	function wpc_onepage_checkout_cart_totals_shipping_fragment( $fragments ) 
	{
		// Get cart shipping fragment.
		ob_start();
		wpc_onepage_checkout_cart_totals_shipping_html();
		$woocommerce_cart_shipping = ob_get_clean();
		
		$fragments['.woocommerce-shipping-methods-container'] = $woocommerce_cart_shipping;
		
		return $fragments;
	}

}