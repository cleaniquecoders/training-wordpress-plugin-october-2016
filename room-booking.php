<?php

/*
Plugin Name: Room Booking
Version: 1.0.0
Description: A simple plugin to book room
Author: Nasrul Hazim Bin Mohamad
Author URI: http://blog.nasrulhazim.com
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Set Plugin Directory Path
define('RB_DIR', plugin_dir_path(__FILE__));

// Set Plugin URI
define('RB_URI', plugin_dir_url(__FILE__));

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once RB_DIR . 'app/bootstrap.php';

register_activation_hook(__FILE__, 'rb_activate');

register_deactivation_hook(__FILE__, 'rb_deactivate');

function rb_activate()
{
    $version = $plugin_data['Version'];

    // check WordPress version, if less than version we need, don't activate
    if (version_compare(get_bloginfo('version'), '4.0', '<')) {
        wp_die("You must use at least WordPress 4.1 to use this plugin!");
    }

    // If no record on plugin version, do create one
    if (get_option('room_booking_version') === false) {
        add_option('room_booking_version', $version);
    } else if (get_option('room_booking_version') < $version) {
        // else, do update the slider version
        update_option('room_booking_version', $version);
    }
}

function rb_deactivate()
{

}

function rb_run()
{
    $rb = new RoomBooking();
    $rb->run();
}

rb_run();
