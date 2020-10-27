<?php

defined( 'ABSPATH' ) || exit; 

if ( ! $messages ) {
	return;
}

?>

<?php foreach ( $messages as $message ) : ?>
	<div class="woocommerce-info">
		<?php
			echo wc_kses_notice( $message );
		?>
	</div>
<?php endforeach; ?>
