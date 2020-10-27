<div class="steps">
        <ul class="steps-items">
            <li class="steps-item">
                <span class="steps-item-label"><?php esc_html_e( 'Shopping Cart', 'WPC' ); ?></span>
                <a href="<?php echo wc_get_cart_url() ?>">
                    <div class="steps-item-icon">
                        <i class="icon-check"></i>
                    </div>
                </a>
            </li>

            <li class="steps-item steps-item-is-current">
                <span class="steps-item-label"><?php esc_html_e( 'Delivery and Payment', 'WPC' ); ?></span>
                <div class="steps-item-icon">
                    <i class="icon-check"></i>
                </div>
            </li>

            <li class="steps-item">
                <span class="steps-item-label last-label"><?php esc_html_e( 'Confirmation', 'WPC' ); ?></span>
                <div class="steps-item-icon">
                    <i class="icon-check"></i>
                </div>
         </li>
    </ul>
</div>
