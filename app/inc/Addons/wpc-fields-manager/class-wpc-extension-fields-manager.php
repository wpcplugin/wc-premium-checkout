<?php

namespace WPC\Extension;

class Fields_Manager extends \WPC\Abstract_Addon 
{	
	public function __construct() 
	{		
		$this->section      =   'wpc_field_manager';
		
		$this->type         =   'extension';
		$this->slug         =   'wpc-fields-manager';
		$this->title        =   __( 'Fields Manager', 'WPC' );
		$this->description  =   __( 'Embedded extension for manage checkout fields. Support for new fields, field removal, field masks, pattern validation and HTML attribute customization.', 'WPC' );
		$this->author       =   __( 'WPC' );	
		$this->version      =   WPC_VERSION;
		$this->author_url   =   WPC_URL;
		$this->thumbnail    =   plugins_url( 'assets/img/thumbnail.svg', __FILE__ );
		$this->embedded     =   true;	

		add_action( 'wpc_init', array( $this, 'wpc_init' ) );
		add_action( 'customize_register', array( $this, 'customize_init' ) );
		add_action( 'wpc_template_init', array( $this, 'template_init' ) );
		add_filter( 'wpc_field_manager_saved_groups_and_fields', array( $this, 'before_return_saved_groups_and_fields' ), 1 );
		add_filter( 'wpc_field_manager_incontrol_group_field', array( $this, 'to_control_field_options_sanitize' ), 1, 3 );
		add_action( 'wpc_field_manager_incheckout_group_field', array( $this, 'to_checkout_field_options_sanitize' ), 1, 3 );
		add_filter( 'wpc_field_manager_to_save_field_options_sanitize', array( $this, 'to_save_field_options_sanitize' ), 1, 2 );
		add_action( 'woocommerce_checkout_fields', array( $this, 'to_checkout_fields_unify' ), 1 );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'to_order_update_meta' ) );
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'to_admin_print_order_meta' ) );
		add_action( 'woocommerce_email_order_meta', array( $this, 'to_email_print_order_meta'), 10, 3 );
		add_action( 'wp_ajax_wpc_field_manager_reset_settings', array( $this, 'reset_settings' ) );			
	}
	
	public function wpc_init() 
	{
		$this->setting = apply_filters( 
			'wpc_field_manager_setting_key', 
			'wpc_field_manager_list' 
		);
	}

	public function customize_init() 
	{
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_enqueue' ) );
	}

	public function customize_enqueue() 
	{
		wp_enqueue_script( 
			'wpc-field-manager', 
			plugins_url( 'assets/js/customizer.js', __FILE__ ), 
			array( 'jquery' ), 
			WPC_VERSION, 
			true 
		);
		
		wp_localize_script(
			'wpc-field-manager',
			'wpc_field_manager',
			array(
				'nonce' => wp_create_nonce( 
					'wpc-field-manager-nonce' 
				),
				'i18n' => array(
					'confirm_reset_settings' => __( 'Do you want to restore the initial settings?', 'WPC' ),
				),
			)
		);
		
		wp_enqueue_style( 
			'wpc-field-manager',
			plugins_url( 'assets/css/customizer.css', __FILE__ ),
			array(), 
			WPC_VERSION,
			'all' 
		);
	}


	public function template_init() 
	{
		add_action( 'woocommerce_checkout_init', array( $this, 'checkout_enqueue' ), PHP_INT_MAX );
	}

	public function checkout_enqueue() 
	{		
		wp_enqueue_script( 
			'jquery-mask', 
			plugins_url( 'assets/js/mask/mask.min.js', __FILE__ ), 
			array( 'jquery' ),
			'1.14.10',
			true 
		);

		wp_enqueue_script( 
			'jquery-validate', 
			plugins_url( 'assets/js/validate/validate.min.js', __FILE__ ), 
			array( 'jquery' ),
			'1.13.0',
			true 
		);

		wp_enqueue_script( 
			'wpc-field-manager-frontend', 
			plugins_url( 'assets/js/frontend', __FILE__ ), 
			array( 'jquery', 'jquery-validate' ),
			WPC_VERSION,
			true 
		);
		
		wp_enqueue_style( 
			'wpc-field-manager-frontend',
			plugins_url( 'assets/css/frontend.css', __FILE__ ),
			array(), 
			WPC_VERSION,
			'all' 
		);
	}
	
	public function customizer() 
	{	
		return (
			array( 
				'sections' => array( 
					$this->section => array(
						'title' => __( 'Fields Manager', 'WPC' ),
						'description'  =>  __( 'Use these options to manage checkout fields.', 'WPC' ),
						'priority' => 300,
					) 
				),
				'settings' => array( 
					$this->setting => array(
						'sanitize_callback' => array( $this, 'filter_sanitize_before_saving' ),
					),
				),
				'controls' => array(
					'wpc_field_manager' => array(
						'class'  	   =>  'WPC\Control\Field_Group',
						'id'  	       =>  'wpc_field_manager_control',
						'label'        =>  __( 'Add Field Group', 'WPC' ),
						'description'  =>  __( 'Add a new group to organize custom fields.', 'WPC' ),
						'default_values' => [
							'group_title' => esc_html__( 'Group Title', 'WPC' ),
							'field_title' => esc_html__( 'Field Title', 'WPC' ),
						],
						'groups' => $this->filter_groups_and_fields(),
						'arrangement_fields' => array(
							'id' => [
								'type'        => 'text',
								'label'       => esc_html__( 'Field ID', 'WPC' ),
								'description' => esc_html__( '', 'WPC' ),
								'disabled'    =>  true,
							],
							'priority' => [
								'type'        => 'hidden',
								'label'       => esc_html__( 'Priority', 'WPC' ),
								'description' => esc_html__( '', 'WPC' ),
								'disabled'    =>  true,
							],
							'type'  => [
								'type'        => 'select',
								'label'       => esc_html__( 'Type', 'WPC' ),
								'description' =>  esc_html__( '', 'WPC' ),
								'choices'     => array( 
									''               =>      __( 'Default', 'WPC' ),
									'text'           =>      __( 'Text', 'WPC' ),
									'password'       =>      __( 'Password', 'WPC' ),
									'email'          =>      __( 'E-mail', 'WPC' ),
									'tel'            =>      __( 'Phone', 'WPC' ),
									'textarea'       =>      __( 'Textarea', 'WPC' ),			
									'select'         =>      __( 'Select', 'WPC' ),			
									'radio'          =>      __( 'Radio', 'WPC' ),			
									'date'           =>      __( 'Date', 'WPC' ),			
									'datetime-local' =>      __( 'Date and Time', 'WPC' ),			
								),
							],
							'options' => [
								'type'        => 'array',
								'label'       => esc_html__( 'Options', 'WPC' ),
								'description' =>  esc_html__( '', 'WPC' ),
							],
							'value' => [
								'type'        => 'text',
								'label'       => esc_html__( 'Default Value', 'WPC' ),
								'description' => '',
							],				
							'placeholder'  => [ 
								'type'        => 'text',
								'label'       => esc_html__( 'Placeholder', 'WPC' ),
								'description' => '',
							],
							'class' => [
								'type'        => 'text',
								'label'       => esc_html__( 'Class', 'WPC' ),
								'description' => '',
							],
							'validation' => [
								'type'        => 'text',
								'label'       => esc_html__( 'Pattern Validation', 'WPC' ),
								'description' => ''
							],
							'validation_message' => [
								'type'        => 'text',
								'label'       => esc_html__( 'Validation Error Message', 'WPC' ),
								'description' => ''
							],
							'mask' => [
								'type'        => 'text',
								'label'       => esc_html__( 'Input Mask', 'WPC' ),
								'description' => ''
							],
							'required' => [
								'type'        => 'select',
								'label'       => esc_html__( 'Required', 'WPC' ),
								'description' => esc_html__( '', 'WPC' ),
								'choices'     => array( 
									'yes'      =>      __( 'Yes', 'WPC' ),
									'no'       =>      __( 'No', 'WPC' ),			
								),
							],
							'visible' => [
								'type'        => 'select',
								'label'       => esc_html__( 'Visibility at checkout', 'WPC' ),
								'description' => esc_html__( '', 'WPC' ),
								'choices'     => array( 
									''        =>      __( 'Default', 'WPC' ),
									'hidden'  =>      __( 'Hidden', 'WPC' ),			
								),
							],
							'inemail' => [
								'type'        => 'select',
								'label'       => esc_html__( 'Display in Emails', 'WPC' ),
								'description' => esc_html__( '', 'WPC' ),
								'choices'     => array( 
									''         =>      __( 'Default', 'WPC' ),
									'yes'      =>      __( 'Yes', 'WPC' ),
									'no'       =>      __( 'No', 'WPC' ),			
								),
							],
							'inorder' => [
								'type'        => 'select',
								'label'       => esc_html__( 'Display in Order Detail Pages', 'WPC' ),
								'description' => esc_html__( '', 'WPC' ),
								'choices'     => array( 
									''         =>      __( 'Default', 'WPC' ),
									'yes'      =>      __( 'Yes', 'WPC' ),
									'no'       =>      __( 'No', 'WPC' ),			
								),
							],
						),
						'section'      =>  $this->section,
						'settings'     =>  $this->setting
					),
					'wpc_field_manager_reset' => array(
						'type'  => 'button',
						'input_attrs' => array(
							'id' => 'wpc_field_manager_reset',
							'value' => __( '>> Reset Settings', 'WPC' ),
							'class' => 'button-link',
						),
						'section'  => $this->section,
						'settings' => array( )
					)
				),
			)
		);

	}
	
	public function get_saved_groups_and_fields()
	{
		if ( is_null( WC()->session ) ) {
			WC()->session = new \WC_Session_Handler();
			WC()->session->init();
		}

		$control = get_option( $this->setting, array() );
		
		if ( empty( $control ) ) {
			
			$groups  = array();
			$default = array( 
				array( 'id' => 'billing', 'title' => __( 'Billing Fields', 'WPC' ), 'customGroup' => false, 'enableDelete' => false, 'addFields' => true, 'removeFields' => true, 'moveFields' => true, 'limitIncontext' => false, 'children' => array() ), 
				array( 'id' => 'shipping', 'title' =>  __( 'Shipping Fields', 'WPC' ), 'customGroup' => false, 'enableDelete' => false, 'addFields' => true, 'removeFields' => true, 'moveFields' => true, 'limitIncontext' => false, 'children' => array() ), 
				array( 'id' => 'order', 'title' =>  __( 'Additional Fields', 'WPC' ), 'customGroup' => false,'enableDelete' => false, 'addFields' => true, 'removeFields' => true, 'moveFields' => true, 'limitIncontext' => false, 'children' => array() ),
				array( 'id' => 'account', 'title' =>  __( 'Account Fields', 'WPC' ), 'customGroup' => false,'enableDelete' => false, 'addFields' => false, 'removeFields' => false, 'moveFields' => false, 'limitIncontexto' => true, 'children' => array() ),
			);
			
			foreach( $default as $group ) {				
				
				$gid    = $group['id'];
				$fields = WC()->checkout()->get_checkout_fields();

				if( isset( $fields[ $gid ] ) ) {
					foreach( $fields[ $gid ] as $fid => $field ) {
						$group['children'][ $fid ] = $field;

					}
				}
			
				$groups[ $group['id'] ] = $group;
			}
			
			update_option( $this->setting, $groups );
			
			$control = get_option( $this->setting, array() );		
		}

		return( 
			apply_filters( 
				'wpc_field_manager_saved_groups_and_fields',
				$control
			)
		);
	}
	
	public function before_return_saved_groups_and_fields( $control_fields ) 
	{	
		if ( 'yes' === get_option( 'woocommerce_registration_generate_username' ) ) {
			unset( $control_fields['account']['children']['account_username'] );
		}
		if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) ) {
			unset( $control_fields['account']['children']['account_password'] );
		}
		if ( 'hidden' === get_option( 'woocommerce_checkout_company_field', 'optional' ) ) {
			unset( $control_fields['billing']['children']['billing_company'] );
		}
		if ( 'hidden' === get_option( 'woocommerce_checkout_address_2_field', 'optional' ) ) {
			unset( $control_fields['billing']['children']['billing_address_2'] );
		}
		if ( 'hidden' === get_option( 'woocommerce_checkout_phone_field', 'required' ) ) {
			unset( $control_fields['billing']['children']['billing_phone'] );
		}
		if ( isset( $control_fields['billing']['children'] ) && empty( $control_fields['account']['children'] ) ) {
			unset( $control_fields['account'] );
		}
		
		return $control_fields;
	}

	public function filter_groups_and_fields( $type = 'incontrol' ) 
	{
		$control_fields  = $this->get_saved_groups_and_fields();
		
		foreach( $control_fields as $gid => &$group ) {
			
			$group = apply_filters( sprintf( 'wpc_field_manager_%s_group', $type ), $group, $gid );
			
			foreach( $group['children'] as $fid => &$field ) {

				$field = apply_filters( sprintf( 'wpc_field_manager_%s_group_field', $type ), $field, $fid, $gid );

				foreach( $field as $opt => &$value ) {
					
					$value = apply_filters( sprintf( 'wpc_field_manager_%s_group_field_option', $type ), $value, $opt, $fid, $gid );
				}
			}
			
		}

		return $control_fields;
	}
	
	public function to_checkout_field_options_sanitize( $content, $fid, $gid ) 
	{
		if( empty( $content['type'] ) || is_null( $content['type'] ) ) {
			unset( $content['type'] );
		}

		if( isset( $content['title'] ) && ( ! empty( $content['title'] ) || ! is_null( $content['title'] ) ) ) {
			$content['label'] = $content['title']; 
		}

		if( isset( $content['value'] ) && ( ! empty( $content['value'] ) || ! is_null( $content['value'] ) ) ) {
			$content['default'] = $content['value']; 
		}

		if ( isset( $content['validation'] ) && ! empty( $content['validation'] ) ) {
			if( isset( $content['type'] ) && in_array( $content['type'], apply_filters( 'wpc_field_manager_to_validation_ignore_input', array( 'radio', 'checkbox' ) ) ) ) {
				$content['input_class'][] = 'ignore-pattern-validation';
			} else {
				$content['custom_attributes']['pattern'] = $content['validation'];
				$content['custom_attributes']['onchange'] = 'jQuery( this ).valid()';
				$content['custom_attributes']['data-msg-pattern'] = $content['validation_message'] ?: __( 'Invalid format.' );
			}
		}
		
		if ( isset( $content['mask'] ) && ! empty( $content['mask'] ) ) {
			$content['custom_attributes']['data-mask'] = $content['mask'];
			$content['custom_attributes']['data-mask-clearifnotmatch'] = 'true';
		}
		
		if ( ! isset( $content['class'] ) ) {
			$content['class'] = array();
		}

		if ( isset( $content['visible'] ) ) {
			$content['class'][] = $content['visible'];
		}
		
		return $content;
	}

	public function to_checkout_fields_unify( $defaul_fields ) 
	{		
		$control_fields = $this->filter_groups_and_fields( 'incheckout' );
		
		$new_fields = array();
		
		foreach( $control_fields as $gid => $group ) {
			
			foreach( $group['children'] as $fid => &$field ) {
				
				foreach( $field as $opt => $value ) {
					
					$new_fields[ $gid ][ $fid ][ $opt ] = $value;
				}
			}
		}

		$new_array_values = array();
		
		foreach( $new_fields as $gid => $group ) {
			
			foreach( $group as $fid => $field ) {

				$field['group'] = $gid;

				$new_array_values[$fid] = $field;
			}
		}
		
		$defaul_array_values = [];

		foreach( $defaul_fields as $gid => &$group ) {
			
			foreach( $group as $fid => $field ) {
				
				$field['group'] = $gid;
				
				$defaul_array_values[$fid] = $field;
			}
		}

		$replace = array_replace_recursive( $defaul_array_values, $new_array_values );
		
		$final_array = [];
		
		foreach( $replace as $fid => &$field ) {
			
			$final_array[ $field['group'] ][ $fid ] = $field;
		}
		
		return $final_array;
	}
	
	public function to_control_field_options_sanitize( $content, $fid, $gid )
	{	
		$valid_types   =  apply_filters( 'wpc_field_manager_define_valid_field_types', array( 'text', 'password', 'email', 'tel', 'textarea', 'select', 'radio', 'date', 'datetime-local' ) );
	
		$priority      =  $content['priority'] ?? '';
		$placeholder   =  $content['placeholder'] ?? '';
		$visible       =  $content['visible'] ?? '';
		$inemail       =  $content['inemail'] ?? '';
		$inorder       =  $content['inorder'] ?? '';
		$options       =  $content['options'] ?? array();
		$value         =  $content['value'] ?? '';
		$title         =  $content['title'] ?? $content['label'];
		$custom_field  =  $content['customField'] ?? false;
		$enable_delete =  $content['enableDelete'] ?? false;

		$required      =  isset( $content['required'] ) && !empty( $content['required'] ) ? wpc_bool_to_string( $content['required'] ) : 'no';		
		$class         =  isset( $content['class'] ) && !empty( $content['class'] ) ? implode( ' ', $content['class'] ) : '';
		$type          =  isset( $content['type'] ) ? ( in_array( $content['type'], $valid_types ) ? $content['type'] : '' ) : '';
		
		return(
			array_replace(
				$content,
				array(
					'id'           => $fid,
					'title'        => $title,
					'value'        => $value,
					'type'         => $type,
					'required'     => $required,
					'class'        => $class,
					'options'      => $options,
					'priority'     => $priority,
					'placeholder'  => $placeholder,
					'enableDelete' => $enable_delete,
					'customField'  => $custom_field,
				)
			)
		);
	}

	public function to_save_field_options_sanitize( $content, $id )
	{		
		foreach( array( 'customField', 'enableDelete', 'inorder', 'inemail', 'required' ) as $option ) {
			if ( isset( $content[ $option ] ) && '' !== $content[ $option ] ) {
				$content[ $option ] = wpc_string_to_bool( $content[ $option ] );
			}
		}
		
		if ( isset( $content['class'] ) && ! empty( $content['class'] ) && !is_array( $content['class'] ) ) {
			$content['class'] = explode ( ' ', $content['class'] );
		} else {
			$content['class'] = array();
		}

		return $content;
	}
	
	public function filter_sanitize_before_saving( $groups )
	{	
		foreach( $groups as $gid => &$group ) {
			$group = apply_filters( 'wpc_field_manager_to_save_group_sanitize', $group, $gid );
			foreach( $group['children'] as $fid => $field ) {			
				$field = apply_filters( 'wpc_field_manager_to_save_field_options_sanitize', $field, $fid, $gid );

				$groups[ $gid ][ 'children' ][ $fid ] = $field;
			}

		}
		
		return $groups;
	}
	
	public function to_order_update_meta( $order_id ) 
	{
		$order = wc_get_order( $order_id );
		
		add_action( 'wpc_field_manager_inorder_group_field', function( $content, $fid, $gid ) use( &$order ) {

			if( ( true === $content['inemail'] || true === $content['inorder'] ) && isset( $_POST[ $fid ] ) && !empty( $_POST[ $fid ] ) ) {
				$order->update_meta_data( sprintf( '_%s', $fid ), sanitize_text_field( wp_unslash( $_POST[ $fid ] ) ) );
			}
		
			return $content;
		
		}, PHP_INT_MAX, 3 );

		$this->filter_groups_and_fields( 'inorder' );
		
		$order->save();
	}

	public function to_admin_print_order_meta( $order ) 
	{ 
		$fields = [];
		
		add_action( 'wpc_field_manager_inorder_group_field', function( $content, $fid, $gid ) use( &$order, &$fields ) {
			
			if( true === $content['inorder'] && !empty( $order->$fid ) ) {
				$fields[] = array( 'label' => $content['title'], 'value' =>  $order->$fid );
			 }

			return $content;
		
		}, PHP_INT_MAX, 3 );
	
		$this->filter_groups_and_fields( 'inorder' );
		
		include( dirname( __FILE__ ) . '/views/html-admin-print-order-meta.php' );
	}

	public function to_email_print_order_meta( $order, $sent_to_admin, $plain_text ) 
	{ 	
		$fields = [];
		
		add_action( 'wpc_field_manager_inemail_group_field', function( $content, $fid, $gid ) use( &$order, &$fields ) {

			if( true === $content['inemail'] && !empty( $order->$fid ) ) {
				$fields[] = array( 'label' => $content['title'], 'value' =>  $order->$fid );
			}
		
			return $content;
		
		}, PHP_INT_MAX, 3 );
		
		$this->filter_groups_and_fields( 'inemail' );
		
		if ( true === $plain_text ) {
			include( dirname( __FILE__ ) . '/views/plain/email-print-order-meta.php' );
		} 
		else {
			include( dirname( __FILE__ ) . '/views/html-email-print-order-meta.php' );
		}
	}

	public function reset_settings() 
	{
		check_ajax_referer( 'wpc-field-manager-nonce', 'security' );
	
		if ( update_option( $this->setting, '' ) ) {
			wp_send_json_success( array(
				'message' => __( 'Settings have been reset.', 'WPC' ),
				'status'      => 1
			) );
		} else {
			wp_send_json_error( array(
				'message' => __( 'There was a problem with the process. The settings have not been reset.', 'WPC' ),
				'status'      => 0
			) );
		}
	
		die;
	}
}