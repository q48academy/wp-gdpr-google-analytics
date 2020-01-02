<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Plugin_Name
 *
 * @wordpress-plugin
 * Plugin Name: WP GDPR Google Analytics
 * Plugin URI: https://github.com/q48academy/wp-gdpr-google-analytics
 * Description: A Wordpress Plugin that combines the requirements for Gdpr and tracks anonymous data in  Google Analytics
 * Version: 1.0.0
 * Author: Pascal Fantou
 * Author URI: https://q48.de/
 * License:           MIT
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       plugin-name
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('PLUGIN_NAME_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
/*
function activate_plugin_name()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-plugin-name-activator.php';
	Plugin_Name_Activator::activate();
}
register_activation_hook(__FILE__, 'activate_plugin_name');
*/
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
/*
function deactivate_plugin_name()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-plugin-name-deactivator.php';
	Plugin_Name_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_plugin_name');
*/

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'src/Gdpr.php';
require plugin_dir_path(__FILE__) . 'src/GdprGoogleAnalytics.php';
require plugin_dir_path(__FILE__) . 'src/WpGdprGaPlugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_gdpr_google_analytics()
{

	#$plugin = new GdprGoogleAnalytics();
	#$plugin->run();

}

run_wp_gdpr_google_analytics();

// Options
add_action('admin_menu', ['WpGdprGaPlugin','optionsPage']);

/**
 * register our wporg_settings_init to the admin_init action hook
 */
add_action( 'admin_init', ['WpGdprGaPlugin','settingsInit'] );

function wp_gdpr_install_keys() {

	WpGdprGaPlugin::installKeys();

	exit( wp_redirect( admin_url( 'tools.php?page=' . WpGdprGaPlugin::PAGE ) ) );

}

add_action( 'admin_post_wp_gdpr_install_keys', 'wp_gdpr_install_keys' );

function wp_gdpr_ga_track_pageview(){

	WpGdprGaPlugin::trackPageImpression();

}
