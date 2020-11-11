<?php defined( 'ABSPATH' ) || exit;

/**
 * The plugin bootstrap file
 *
 * Plugin Name:       WPC - Checkout Editor for WooCommerce
 * Plugin URI:        https://wpcplugin.com/
 * Description:       WPC is a complete set of tools developed to scale the purchase process in WooCommerce stores. Provides what you need to build a high conversion checkout page. It's free.
 * Version:           1.3.4
 * Author:            WILLIAN SANTANA
 * Author URI:        https://github.com/santanamic
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       WPC
 * Domain Path:       /app/i18n
 * WC requires at least: 3.6.0
 * WC tested up to: 4.5.2
 */

require_once( __DIR__ . '/app/vendor/autoload.php' );
require_once( __DIR__ . '/app/setup/etc/config.php' );
require_once( __DIR__ . '/app/setup/helper.php' );
require_once( __DIR__ . '/app/setup/admin.php' );
require_once( __DIR__ . '/app/setup/check.php' );
require_once( __DIR__ . '/app/setup/plugin.php' );
require_once( __DIR__ . '/app/setup/customize.php' );
require_once( __DIR__ . '/app/setup/template.php' );
require_once( __DIR__ . '/app/hooks.php' );