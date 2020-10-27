<?php

namespace WPC\Extension;

class Theme_Selector extends \WPC\Abstract_Addon 
{	
	public function __construct() 
	{		
		$this->section      =   'wpc_theme_selector';
		$this->setting      =   'wpc_theme_selector_active';
		
		$this->type         =   'extension';
		$this->slug         =   'wpc-theme-selector';
		$this->version      =   WPC_VERSION;
		$this->title        =   __( 'Theme Selector', 'WPC' );
		$this->description  =   __( 'Embedded extension for view and enable installed themes', 'WPC' );
		$this->author       =   __( 'WPC' );
		$this->author_url   =   WPC_URL;
		$this->thumbnail    =   plugins_url( 'assets/img/thumbnail.svg', __FILE__ );
		$this->embedded     =   true;

		add_action( 'wp', array( $this, 'load_active_theme_template' ) );		
		add_action( 'woocommerce_checkout_update_order_review', array( $this, 'load_active_theme_template' ) ); //correcao para update_order_review
		add_action( 'admin_action_wpc_theme_preview', array( $this, 'enable_theme_preview_by_url_param' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_theme_preview_in_url_param' ) );
		add_filter( 'wpc_addon_control_active_callback', array( $this, 'load_control_for_active_theme' ), 1, 3 );
		add_filter( 'wpc_admin_card_actions', array( $this, 'shortcut_to_theme_preview_in_admin_page' ), 1, 2 );
	}

	public function customizer() 
	{
		return (
			array( 
				'sections' => array( 
					$this->section => array(
						'title' => __( 'Themes', 'WPC' ),
						'priority' => 5,
					) 
				),
				'settings' => array( 
					$this->setting => array() 
				),
				'controls' => array( 
					'wpc_theme_selector' => array(
						'class'           => 'WPC\Control\Card_Selector',
						'active_callback' => false,
						'section'         => $this->section,
						'settings'        => $this->setting,
						'cards'           => $this->get_themes(),
						'columns'         => 1,
						'gap'             => '15px',
					)
				),
			)
		);

	}

	public function get_themes() 
	{
		$themes   = [];
		$response = wp_remote_request(
			sprintf( 'https://wpcplugin.github.io/json-remote-addons/%1$s-%2$s.json' , 
				'demo', 
				'themes' 
			),
			array(
				'method' => 'GET',
				'timeout' => 2,
			)
		);
		$installed = (array) WPC()->addons->get( 'themes' );
		
		if( !is_wp_error( $response ) && isset( $response['body'] ) ){
			$content = json_decode( $response['body'], true );
			if ( json_last_error() === JSON_ERROR_NONE ) {
				$demo = array_diff_key( $content, $installed ); // if exists, remove installed addons
				
				foreach ( $demo as $slug => $theme ) {
					$theme = (array) $theme;				
					$theme['action'] = array( 'id' => $theme['slug'], 'text' => __( 'View Demo', 'WPC' ), 'link' => $theme['preview'] );					
					$themes[] = $theme;
				}
			}
		} 
		
		foreach ( $installed as $slug => $theme ) {
			$theme = (array) $theme;				
			$theme['isActivecard'] = $theme['slug'] === $this->get_active_theme()->get( 'slug' );
			$theme['action'] = array( 'id' => $theme['slug'], 'text' => __( 'Live Preview', 'WPC' ), 'active_text' => __( '✓ Selected', 'WPC' ) );					
			$themes[] = $theme;
		}
		
		return $themes;
	}

	public function get_active_theme() 
	{
		$default_theme = 'wpc_theme_onepage_checkout';
		$saved_theme   = get_option( $this->setting );
		
		if ( $available_theme = WPC()->addons->get_by_slug( $saved_theme ) ) {
			return $available_theme;
		}
		return (
			WPC()->addons->get_by_slug( 
				$default_theme 
			)
		);
	}

	public function load_active_theme_template() 
	{			
		$this->get_active_theme()->load_template();
	}
	
	public function load_control_for_active_theme( $default, $type, $slug ) 
	{	
		if ( 'theme' === $type ) {
			return ( 
				$this->get_active_theme()->get( 'slug' ) === $slug 
			);
		}
	
		return $default;
	}

	public function enable_theme_preview_by_url_param() 
	{
		check_admin_referer();
		
		if ( isset( $_REQUEST['theme'] ) && isset( $_REQUEST['redirect'] ) ) {
			$theme = sanitize_text_field( $_REQUEST['theme'] );
			$redirect = htmlspecialchars_decode( $_REQUEST['redirect'] );
			
			add_option( 'wpc-theme-preview', $theme );
			wp_redirect( $redirect );
		}
	}
	
	public function load_theme_preview_in_url_param() 
	{
		if ( isset( $_REQUEST['wpc-theme-preview'] ) ) {
			$preview_theme = get_option( 'wpc-theme-preview', false );
			
			if ( false != $preview_theme ) {
			?>
				<script>
					window.addEventListener( 'load', function( e ) {
						jQuery( '#sub-accordion-section-wpc_theme_selector .wpc-container-card' )
							.find( '[data-id="<?php print( $preview_theme ); ?>"]' )
								.find( 'span.button' )
									.click();
					} );
				</script>
			<?php
				delete_option( 'wpc-theme-preview' );
			}
		}
	}
	
	public function shortcut_to_theme_preview_in_admin_page( $action, $addon ) 
	{
		$wpc_is_active = wpc_is_active();
		
		if ( 'theme' === $addon['type'] ) {
			$customizer_url = true === $wpc_is_active
				? 
				esc_url( 
					add_query_arg( 
						array( 
							'autofocus[section]' => $this->section,
							'url' => urlencode( wc_get_checkout_url() ),
							'wpc-theme-preview' => '1',
						), 
					admin_url( 'customize.php' ) 
					) 
				)
				:
				esc_url( 
					add_query_arg( 
						array( 
							'autofocus[panel]' => 'wpc',
							'url' => urlencode( wc_get_checkout_url() )
						), 
					admin_url( 'customize.php' ) 
					) 
				);
				
			$action_text = true === $wpc_is_active
				?
				__( 
					'✓ Active Theme', 
					'WPC' 
				)
				:
				__( 
					'Manage', 
					'WPC' 
				);
			
			if ( in_array( 'active', $addon['classes'] ) ) {
				return (
					array(
						'text' => $action_text,
						'url'  => $customizer_url
					)
				);
			} 
			elseif ( in_array( 'installed', $addon['classes'] ) ) {
				$action_url = esc_url( 
					add_query_arg( 
						array( 
							'action' => 'wpc_theme_preview',
							'theme' => $addon['slug'],
							'redirect' => urlencode( $customizer_url ),
						), 
					admin_url( 'admin.php' ) 
					) 
				);
				
				return (
					array(
						'text' => __( 'Live Preview', 'WPC' ),
						'url' => $action_url
					)
				);
			}

		}
		
		return $action;
	}
	
}