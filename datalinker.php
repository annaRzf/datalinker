<?php
/**
 * Plugin Name: DataLinkeR
 * Description: A high flexible import/export plugin for WordPress.
 * Version: 1.0.0
 * Author: Anna Razafindramiarina
 * License: GPL2 or NOT because it's super secret!
 */

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Yo! I\'m just a plugin, not much I can do when called directly.';
    exit;
}

defined( 'ABSPATH' ) or die( 'No script kiddies please! In other words GTFO.' );

define( 'DATALINKER__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once DATALINKER__PLUGIN_DIR . 'includes/class/class-dl-general.php';

// initialize the plugin
DataLinkeRGeneral::getInstance();