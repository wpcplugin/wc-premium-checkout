<?php
/**
 * Provide a public-facing view for the plugin
 *
 * @since      1.0.0
 */

do_action( 'wpc_template_init' );
?>

<?php get_header(); ?>
	<?php do_action( 'wpc_template_content' ); ?>
<?php get_footer(); ?>