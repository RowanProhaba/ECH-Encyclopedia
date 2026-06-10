<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://#
 * @since             1.0.0
 * @package           Ech_Encyclopedia
 *
 * @wordpress-plugin
 * Plugin Name:       ECH Encyclopedia
 * Plugin URI:        https://#
 * Description:       This plugin creates shortcode to show all ECH encyclopedia list and single post profile page. It is integrated with AI Medical api.
 * Version:           1.0.0
 * Author:            Rowan Chang
 * Author URI:        https://#
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ech-encyclopedia
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
define( 'ECH_ENCYCLOPEDIA_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ech-encyclopedia-activator.php
 */
function activate_ech_encyclopedia() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ech-encyclopedia-activator.php';
	Ech_Encyclopedia_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ech-encyclopedia-deactivator.php
 */
function deactivate_ech_encyclopedia() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ech-encyclopedia-deactivator.php';
	Ech_Encyclopedia_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ech_encyclopedia' );
register_deactivation_hook( __FILE__, 'deactivate_ech_encyclopedia' );

/****************************************
 * Create an option "run_init_createVP" once plugin is activated
 ****************************************/
function encyclopedia_activate_initialize_createVP() {
	require_once plugin_dir_path( __FILE__ ) . 'public/class-ech-encyclopedia-virtual-pages.php';
	Ech_Encyclopedia_Virtual_Pages::encyclopedia_initialize_createVP();
}
register_activation_hook( __FILE__, 'encyclopedia_activate_initialize_createVP' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ech-encyclopedia.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ech_encyclopedia() {

	$plugin = new Ech_Encyclopedia();
	$plugin->run();

}
run_ech_encyclopedia();
