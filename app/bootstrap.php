<?php

/**
 * RoomBooking bootstrap class - load scripts, styles, setup menus, shortcodes, define pages
 *
 * @package default
 * @author
 **/
class RoomBooking
{
    public function run()
    {
        // include admin css & javascripts
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts_admin']);

        // include front end css & javascripts
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);

        // add plugin main menus
        add_action('admin_menu', [$this, 'menus']);

        // add shortcodes
        add_action('init', [$this, 'define_shortcodes']);
    }

    public function define_shortcodes()
    {
        // short code
        // https://developer.wordpress.org/reference/functions/add_shortcode/
        add_shortcode('room-booking', [__CLASS__, 'view']);
    }

    public function menus()
    {
        //create new top-level menu
        // https://developer.wordpress.org/reference/functions/add_menu_page/
        add_menu_page(
            'Room Booking', // page-title
            'Room Booking', // menu-title
            'administrator', // capbility - superadmin, administrator, editor, author, contributor, subscriber
            'room-booking-setting', // menu-slug
            [$this, 'settings']// function to be call to generate the view
        );

        //create sub menu
        // https://developer.wordpress.org/reference/functions/add_submenu_page/
        add_submenu_page(
            'room-booking-setting', // parent slug
            'Manage', // page-title
            'Manage', // menu-title
            'administrator', // capability
            'room-booking-manage', // menu-slug
            [$this, 'manage']// function to be call to generate the view
        );
    }

    public function enqueue_scripts_admin()
    {
        wp_enqueue_script(
            'fullcalendar-js',
            RB_URI . 'vendor/fullcalendar/fullcalendar.min.js',
            ['jquery'],
            '3.0.1',
            true
        );

        // sample include google font
        // wp_enqueue_style('google-font', '//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,400italic');
        wp_enqueue_style(
            'bootstrap-admin',
            RB_URI . 'assets/css/bootstrap-admin.css'
        );
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style(
            'fullcalendar',
            RB_URI . 'vendor/fullcalendar/fullcalendar.min.css'
        );

        wp_enqueue_script(
            'momentjs',
            RB_URI . 'vendor/fullcalendar/lib/moment.min.js',
            [],
            '2.15.1',
            true
        );

        wp_enqueue_script(
            'fullcalendar-js',
            RB_URI . 'vendor/fullcalendar/fullcalendar.min.js',
            ['jquery', 'momentjs'],
            '3.0.1',
            true
        );

        wp_enqueue_script(
            'view-calendar',
            RB_URI . 'assets/js/view.js',
            ['jquery', 'fullcalendar-js'],
            '1.0.0',
            true
        );
    }

    public function settings()
    {
        // handling rendering
        require_once RB_DIR . '/app/views/settings.php';
    }

    public function manage()
    {
        // handling request
        require_once RB_DIR . '/app/controllers/manage.php';

        // handling data manipulation
        require_once RB_DIR . '/app/models/manage.php';

        // handling rendering
        require_once RB_DIR . '/app/views/manage.php';
    }

    public function view()
    {
        // handling request
        require_once RB_DIR . '/app/controllers/view.php';

        // handling data manipulation
        require_once RB_DIR . '/app/models/view.php';

        // handling rendering
        require_once RB_DIR . '/app/views/view.php';
    }
} // END class
