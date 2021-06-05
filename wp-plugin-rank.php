<?php
/**
 * Plugin Name:       WP Plugin Rank
 * Plugin URI:        https://alikhallad.com
 * Description:       Retrieves WP repository ranking data for a single plugin.
 * Version:           1.0.0
 * Author:            Ali Khallad
 * Author URI:        https://alikhallad.com
 * Text Domain:       wpp-ranking
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('WPPR_Name', 'Mega Forms');
define('WPPR_Slug', 'mega-forms');

/**
 * The core plugin class that is used to define all related hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-plugin-rank.php';

/**
 * Execute the plugin class to kick-off all related functionality,
 * that is registered via hooks.
 *
 * @since    1.0.0
 */
function Run_WP_Plugin_Rank() {
	$plugin = new WP_Plugin_Rank();
}
Run_WP_Plugin_Rank();
