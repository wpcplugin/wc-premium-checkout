<?php defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpc_customize_builder_commmon' ) ) {

	/**
	 * Begins execution of the WP customize
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	function wpc_customize_builder_commmon( $wp_customize ) 
	{		
		$wp_customize->add_panel( 
			'wpc', 
			array(
				'title' => WPC_TITLE,
				'description' => __( 
					'Premium Checkout is the best plugin for checkout customization. Use to create beautiful checkouts.', 
					'WPC' 
				),
				'priority' => 150
			) 
		);

		$wp_customize->add_section(
			'wpc',
			array(
				'title'    => __( 'Enable / Disable', 'WPC' ),
				'priority' => 1000,
				'panel'    => 'wpc',
			)
		);

		$wp_customize->add_setting( 
			'wpc', 
			array(
				'default'   => 'no',
				'type'      => 'option',
				'transport' => 'postMessage',
				'sanitize_callback'    => 'wpc_bool_to_string', 
				'sanitize_js_callback' => 'wpc_string_to_bool',
			)
		);

		$wp_customize->add_control(
			'wpc',
			array(
				'label'    => __( 'Check to enable' , 'WPC' ),
				'description' => '',
				'type'     => 'checkbox',
				'section'  => 'wpc',
				'settings' => 'wpc',
			)
		);
	}
	
}

if ( ! function_exists( 'wpc_customize_enqueue' ) ) {

	/**
	 * Register the JavaScrip and CSS for customize
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	function wpc_customize_enqueue() 
	{
		wp_enqueue_script( 
			'wpc-customize', 
			WPC_URI . 'app/assets/js/customizer.js', 
			array( 'jquery', 'customize-base' ),
			WPC_VERSION, 
			false  
		);
		
		wp_localize_script(
			'wpc-customize',
			'wpc',
			array(			
				'nonce' => wp_create_nonce( 
					'wpc-customizer' 
				),
				'ajaxurl' => admin_url(
					'admin-ajax.php'
				)
			)
		);
		
		wp_enqueue_style( 
			'wpc-customize', 
			WPC_URI . 'app/assets/css/customizer.css', 
			array(), 
			WPC_VERSION,
			'all' 
		);
	}
	
}

if ( ! function_exists( 'wpc_customize_inline_scripts' ) ) {

	/**
	 * Register the inline JavaScrip customize load
	 *
	 * @since    1.3.7
	 * @return   void
	 */
	function wpc_customize_inline_scripts() 
	{		
		?>
		<script>
			jQuery( document ).ready( function( $ ) {
				/* Open checkout url */
				wp.customize.panel( 'wpc', function( panel ) {
					panel.expanded.bind( function( isExpanded ) {
						
						if ( isExpanded ) {
							wp.customize.previewer.previewUrl.set( '<?php echo esc_js( wc_get_page_permalink( 'checkout' ) ); ?>' );
						}
						
					} );
				} );
			} );
		</script>
		<?php
	}
	
}

if ( ! function_exists( 'wpc_preview_enqueue' ) ) {

	/**
	 * Register the JavaScrip and CSS for customize preview
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	function wpc_preview_enqueue() 
	{		
		wp_enqueue_script( 
			'wpc', 
			WPC_URI . 'app/assets/js/preview.js', 
			array( 'jquery', 'customize-preview' ), 
			WPC_VERSION, 
			true 
		);
	}
	
}

if ( ! function_exists( 'wpc_customize_run_addons' ) ) {

	/**
	 * Run addons enabled on customize
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	function wpc_customize_run_addons() 
	{
		$addons = WPC()->addons->get();
		
		foreach ( $addons as $slug => $addon ) {
			wpc_customize_builder_fields(
				$addon->get_customizer(),
				$addon->type,
				$addon->slug,
				$addon->version
			);		
		}		
	}
	
}

if ( ! function_exists( 'wpc_customize_builder_fields' ) ) {

	/**
	 * builder fields in customize control
	 *
	 * @since    1.3.9
	 * @return   void
	 */
	function wpc_customize_builder_fields( $customizer, $type, $slug, $version  ) 
	{
		global $wp_customize;	

		if( empty( $customizer ) ) {
			return $customizer;
		}
		
		if( isset( $customizer['sections'] ) ) {
			foreach(  $customizer['sections'] as $id => $args ) {			
				
				$args['active_callback'] = function() use( $type, $slug, $id ) {
					return apply_filters( 
						'wpc_addon_section_active_callback', 
						$args['active_callback'] ?? true, 
						$type, 
						$slug,
						$id
					);
				};				
				$args['panel'] = 'wpc';	
				$wp_customize->add_section( 
					$id, 
					$args 
				);
			}
		}
		if( isset( $customizer['settings'] ) ) {
			foreach(  $customizer['settings'] as $id => $args ) {
				$args['type'] = 'option';
				
				if ( ! isset( $args['class'] ) ) {
					$wp_customize->add_setting( 
						$id, 
						$args 
					);
				} else {
					$id  = $args['id'] ?? $id;
					$obj = $args['class'];
					
					unset( $args['class'] ); //remove unused values
					
					$wp_customize->add_control(
						new $obj( 
							$wp_customize, 
							$id, 
							$args 
						) 
					);
				}
			}
		}	
		if( isset( $customizer['controls'] ) ) {
			foreach( $customizer['controls'] as $id => $args ) {
				
				if( !isset( $args['active_callback'] ) ) {
					$args['active_callback'] = function() use( $type, $slug, $id ) {

						return (
							apply_filters( 
								'wpc_addon_control_active_callback', 
								true, 
								$type, 
								$slug,
								$id
							)
						);
					};
				} else{
					$active_callback = $args['active_callback'];
					$args['active_callback'] = function() use( $active_callback ) {

						return (
							$active_callback
						);
					};
				}

				if ( ! isset( $args['class'] ) ) {
			
					$wp_customize->add_control(
						$id,
						$args
					);
				} else {
					$obj = $args['class'];
					
					unset( $args['class'] ); //remove unused values
					
					$wp_customize->add_control(
						new $obj( 
							$wp_customize, 
							$id, 
							$args 
						) 
					);
				}
			}
		}
		if( isset( $customizer['refresh'] ) ) {
			foreach(  $customizer['refresh'] as $id => $args ) {
				$wp_customize->selective_refresh->add_partial( 
					$id, 
					$args 
				);
			}
		}
		
		return $customizer;
	}
	
}
