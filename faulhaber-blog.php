<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://dig.id
 * @since             1.0.0
 * @package           Faulhaber_Blog
 *
 * @wordpress-plugin
 * Plugin Name:       Faulhaber Blog
 * Plugin URI:        https://dig.id
 * Description:       Simple plugin that gives an shortcode([faulhaber-blog]) to display the blog list with infinite-scroll and filters.
 * Version:           1.0.0
 * Author:            dig.id
 * Author URI:        https://dig.id
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       faulhaber-blog
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'FAULHABER_BLOG_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-faulhaber-blog-activator.php
 */
function activate_faulhaber_blog() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-faulhaber-blog-activator.php';
	Faulhaber_Blog_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-faulhaber-blog-deactivator.php
 */
function deactivate_faulhaber_blog() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-faulhaber-blog-deactivator.php';
	Faulhaber_Blog_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_faulhaber_blog' );
register_deactivation_hook( __FILE__, 'deactivate_faulhaber_blog' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-faulhaber-blog.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_faulhaber_blog() {

	$plugin = new Faulhaber_Blog();
	$plugin->run();

}
run_faulhaber_blog();

/**
 * The res api call
 */
require plugin_dir_path( __FILE__ ) . 'includes/ajax-loader.php';

/**
 * Creates the short code
 */
require plugin_dir_path( __FILE__ ) . 'includes/shortcode.php';
